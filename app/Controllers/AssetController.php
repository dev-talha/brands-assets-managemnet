<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Company;
use App\Models\BrandAssetGroup;
use App\Models\BrandAssetFile;
use App\Services\UploadService;
use App\Services\CdnService;
use App\Services\AuditService;
use App\Services\ZipService;
use App\Models\Download;

class AssetController extends Controller
{
    public function logos(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $groups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'logo');

        $this->view('assets.logos', [
            'pageTitle' => $company['name'] . ' - Logos',
            'company' => $company,
            'groups' => $groups,
            'activeSection' => 'logos',
        ]);
    }

    public function images(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $groups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'image');

        $this->view('assets.images', [
            'pageTitle' => $company['name'] . ' - Images',
            'company' => $company,
            'groups' => $groups,
            'activeSection' => 'images',
        ]);
    }

    public function cdnLinks(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $files = BrandAssetFile::getAllForCompany((int)$id);

        // Enrich with CDN URLs
        foreach ($files as &$file) {
            $group = BrandAssetGroup::findById($file['asset_group_id']);
            if ($group) {
                $file['cdn_urls'] = CdnService::generateUrls($file, $group, $company);
                $file['hits'] = CdnService::getHitCount($file['id']);
            }
        }

        $this->view('assets.cdn-links', [
            'pageTitle' => $company['name'] . ' - CDN Links',
            'company' => $company,
            'files' => $files,
            'activeSection' => 'cdn-links',
        ]);
    }

    public function upload(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $company = Company::findById((int)$id);
        if (!$company) {
            $this->withFlash('error', 'Company not found.');
            $this->redirect('/admin/companies');
        }

        // Rate limiting
        if (!checkRateLimit('upload_' . currentUser()['id'], 60, 3600)) {
            $this->withFlash('error', 'Upload rate limit exceeded. Please wait before uploading more files.');
            $this->back();
        }

        $title = trim($this->request->post('title', ''));
        $assetType = $this->request->post('asset_type', 'logo');
        $theme = $this->request->post('theme', 'default') ?: 'default';
        $isPublic = $this->request->post('is_public') ? true : false;
        $description = trim($this->request->post('description', ''));

        if (empty($title)) {
            $this->withFlash('error', 'Asset title is required.');
            $this->back();
        }

        $uploadedFiles = $this->request->files('files');
        if (empty($uploadedFiles)) {
            $this->withFlash('error', 'Please select at least one file to upload.');
            $this->back();
        }

        $successCount = 0;
        $errorMessages = [];

        foreach ($uploadedFiles as $file) {
            if ($file['error'] === UPLOAD_ERR_NO_FILE) continue;

            $result = UploadService::handleUpload(
                $file, (int)$id, $title, $assetType, $theme, $isPublic, $description
            );

            if ($result['success']) {
                $successCount++;
                AuditService::log('asset_uploaded', 'asset_file', $result['file_id'], null, $file['name']);
            } else {
                $errorMessages = array_merge($errorMessages, $result['errors']);
            }
        }

        if ($successCount > 0) {
            $this->withFlash('success', "{$successCount} file(s) uploaded successfully!");
        }
        if (!empty($errorMessages)) {
            $this->withFlash('error', implode('; ', $errorMessages));
        }

        $this->redirect("/admin/companies/{$id}/library");
    }

    public function detail(string $id): void
    {
        $group = BrandAssetGroup::getWithFiles((int)$id);
        if (!$group) {
            $this->json(['error' => 'Not found'], 404);
        }

        $company = Company::findById($group['company_id']);

        if (!$group['is_public'] && !isLoggedIn()) {
            $this->json(['error' => 'Access denied'], 403);
        }

        foreach ($group['files'] as &$file) {
            $file['cdn_urls'] = CdnService::generateUrls($file, $group, $company);
            $file['embed_code'] = CdnService::generateEmbed($file, $group, $company);
            $file['formatted_size'] = formatBytes($file['file_size']);
        }
        $group['primary_file'] = !empty($group['files']) ? $group['files'][0] : null;

        $this->json([
            'group' => $group,
            'company' => $company,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $group = BrandAssetGroup::findById((int)$id);
        if (!$group) {
            $this->withFlash('error', 'Asset group not found.');
            $this->back();
        }

        $data = [
            'title' => trim($this->request->post('title', '')),
            'asset_type' => $this->request->post('asset_type', $group['asset_type']),
            'theme' => $this->request->post('theme', $group['theme']),
            'description' => trim($this->request->post('description', '')),
            'is_public' => $this->request->post('is_public') ? 1 : 0,
        ];

        BrandAssetGroup::update((int)$id, $data);
        AuditService::log('asset_updated', 'asset_group', (int)$id);

        $this->withFlash('success', 'Asset updated.');
        $this->redirect("/admin/companies/{$group['company_id']}/library");
    }

    public function replace(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $file = BrandAssetFile::findById((int)$id);
        if (!$file) {
            $this->withFlash('error', 'File not found.');
            $this->back();
        }

        if (!$this->request->hasFile('file')) {
            $this->withFlash('error', 'Please select a replacement file.');
            $this->back();
        }

        $uploadedFile = $this->request->file('file');
        $result = UploadService::replaceFile((int)$id, $uploadedFile);

        if ($result['success']) {
            AuditService::log('file_replaced', 'asset_file', (int)$id);
            $this->withFlash('success', 'File replaced successfully. CDN links remain the same.');
        } else {
            $this->withFlash('error', implode('; ', $result['errors']));
        }

        $group = BrandAssetGroup::findById($file['asset_group_id']);
        $this->redirect("/admin/companies/{$group['company_id']}/library");
    }

    public function deleteAsset(string $id): void
    {
        $this->authorize(['super_admin']);

        $group = BrandAssetGroup::findById((int)$id);
        if (!$group) {
            $this->withFlash('error', 'Asset not found.');
            $this->back();
        }

        BrandAssetGroup::softDelete((int)$id);
        AuditService::log('asset_deleted', 'asset_group', (int)$id);

        $this->withFlash('success', 'Asset deleted.');
        $this->redirect("/admin/companies/{$group['company_id']}/library");
    }

    public function restoreAsset(string $id): void
    {
        $this->authorize(['super_admin']);
        BrandAssetGroup::restore((int)$id);
        AuditService::log('asset_restored', 'asset_group', (int)$id);
        $this->withFlash('success', 'Asset restored.');
        $this->back();
    }

    public function toggleVisibility(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $group = BrandAssetGroup::findById((int)$id);
        if ($group) {
            $newVisibility = $group['is_public'] ? 0 : 1;
            BrandAssetGroup::update((int)$id, ['is_public' => $newVisibility]);
            AuditService::log('visibility_changed', 'asset_group', (int)$id, null, $newVisibility ? 'public' : 'private');
            $this->withFlash('success', 'Visibility updated.');
        }
        $this->back();
    }

    public function regenerateCdn(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        if (!checkRateLimit('regen_cdn_' . currentUser()['id'], 20, 3600)) {
            $this->withFlash('error', 'Rate limit exceeded for CDN regeneration.');
            $this->back();
        }

        $file = BrandAssetFile::findById((int)$id);
        if ($file) {
            $newToken = generateToken(10);
            BrandAssetFile::update((int)$id, [
                'public_token' => $newToken,
                'cache_version' => date('Y-m-d') . '-' . substr($newToken, 0, 6),
            ]);
            AuditService::log('cdn_token_regenerated', 'asset_file', (int)$id);
            $this->withFlash('success', 'CDN token regenerated.');
        }
        $this->back();
    }

    public function downloadFile(string $id): void
    {
        $file = BrandAssetFile::findById((int)$id);
        if (!$file) Response::error(404, 'File not found');

        $fullPath = storage_path('uploads/' . $file['storage_path']);
        $mimeType = getMimeTypeForExtension($file['extension']);
        
        $downloadName = $file['original_filename'];

        // Log download and fetch company for naming
        $group = BrandAssetGroup::findById($file['asset_group_id']);
        if ($group) {
            $company = Company::findById($group['company_id']);
            if ($company) {
                // E.g. godigital-logo.png
                $cleanFilename = preg_replace('/^' . preg_quote($company['slug'], '/') . '-/', '', $file['original_filename']);
                $downloadName = $company['slug'] . '-' . $cleanFilename;
            }
            
            Download::create([
                'asset_file_id' => $file['id'],
                'asset_group_id' => $file['asset_group_id'],
                'company_id' => $group['company_id'] ?? null,
                'user_id' => currentUser()['id'] ?? null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        }

        Response::download($fullPath, $downloadName, $mimeType);
    }

    public function downloadAssetZip(string $id): void
    {
        $group = BrandAssetGroup::getWithFiles((int)$id);
        if (!$group) Response::error(404, 'Asset not found');

        $files = [];
        foreach ($group['files'] as $file) {
            $path = storage_path('uploads/' . $file['storage_path']);
            if (file_exists($path)) {
                $files[] = ['path' => $path, 'name' => $file['original_filename']];
            }
        }

        $zipName = slugify($group['title']) . '-all-formats.zip';
        $zipPath = ZipService::createZip($files, $zipName);
        if (!$zipPath) Response::error(500, 'Failed to create ZIP');

        ZipService::sendAndCleanup($zipPath, $zipName);
    }

    public function downloadCompanyZip(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) Response::error(404, 'Company not found');

        $allFiles = BrandAssetFile::getAllForCompany((int)$id);
        $files = [];
        foreach ($allFiles as $file) {
            $path = storage_path('uploads/' . $file['storage_path']);
            if (file_exists($path)) {
                $folderName = slugify($file['group_title'] ?? 'asset');
                $files[] = ['path' => $path, 'name' => $folderName . '/' . $file['original_filename']];
            }
        }

        $zipName = $company['slug'] . '-brand-assets.zip';
        $zipPath = ZipService::createZip($files, $zipName);
        if (!$zipPath) Response::error(500, 'Failed to create ZIP');

        ZipService::sendAndCleanup($zipPath, $zipName);
    }
}
