<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;
use App\Models\BrandAssetGroup;
use App\Models\BrandAssetFile;
use App\Models\BrandColor;
use App\Models\BrandFont;
use App\Services\AuditService;
use App\Services\UploadService;

class CompanyController extends Controller
{
    public function index(): void
    {
        $filters = [
            'search' => $this->request->get('search'),
            'status' => $this->request->get('status'),
            'is_public' => $this->request->get('is_public'),
        ];
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $companies = Company::all($filters);

        $this->view('companies.index', [
            'pageTitle' => 'Companies',
            'companies' => $companies,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'brand_manager']);
        $this->view('companies.create', ['pageTitle' => 'Create Company']);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $name = trim($this->request->post('name', ''));
        $domain = trim($this->request->post('domain', ''));
        $description = trim($this->request->post('description', ''));
        $location = trim($this->request->post('location', ''));
        $isPublic = $this->request->post('is_public') ? 1 : 0;

        $platforms = $this->request->post('social_platforms') ?? [];
        $urls = $this->request->post('social_urls') ?? [];
        $icons = $this->request->post('social_icons') ?? [];
        $socialLinks = [];
        if (is_array($platforms) && is_array($urls)) {
            foreach ($platforms as $index => $platform) {
                if (!empty(trim($platform)) && !empty(trim($urls[$index] ?? ''))) {
                    $socialLinks[] = [
                        'platform' => trim($platform),
                        'url' => trim($urls[$index]),
                        'icon' => trim($icons[$index] ?? '')
                    ];
                }
            }
        }

        if (empty($name)) {
            $this->withFlash('error', 'Company name is required.');
            $this->withOld(['name' => $name, 'domain' => $domain, 'description' => $description, 'location' => $location]);
            $this->redirect('/admin/companies/create');
        }

        $data = [
            'name' => $name,
            'domain' => $domain,
            'description' => $description,
            'location' => $location,
            'social_links' => json_encode($socialLinks),
            'is_public' => $isPublic,
            'status' => 'active',
        ];

        // Handle avatar upload
        if ($this->request->hasFile('avatar')) {
            $file = $this->request->file('avatar');
            $avatarPath = $this->handleCompanyImage($file, 'avatar');
            if ($avatarPath) $data['avatar_image_path'] = $avatarPath;
        }

        // Handle cover upload
        if ($this->request->hasFile('cover')) {
            $file = $this->request->file('cover');
            $coverPath = $this->handleCompanyImage($file, 'cover');
            if ($coverPath) $data['cover_image_path'] = $coverPath;
        }

        $id = Company::create($data);
        AuditService::log('company_created', 'company', $id, null, $name);

        // Create storage directories
        $company = Company::findById($id);
        $uploadsDir = storage_path("uploads/{$company['slug']}");
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $this->withFlash('success', 'Company created successfully!');
        $this->redirect("/admin/companies/{$id}/overview");
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);
        $company = Company::findById((int)$id);
        if (!$company) {
            $this->withFlash('error', 'Company not found.');
            $this->redirect('/admin/companies');
        }

        $this->view('companies.edit', [
            'pageTitle' => 'Edit ' . $company['name'],
            'company' => $company,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);
        $company = Company::findById((int)$id);
        if (!$company) {
            $this->withFlash('error', 'Company not found.');
            $this->redirect('/admin/companies');
        }

        $platforms = $this->request->post('social_platforms') ?? [];
        $urls = $this->request->post('social_urls') ?? [];
        $icons = $this->request->post('social_icons') ?? [];
        $socialLinks = [];
        if (is_array($platforms) && is_array($urls)) {
            foreach ($platforms as $index => $platform) {
                if (!empty(trim($platform)) && !empty(trim($urls[$index] ?? ''))) {
                    $socialLinks[] = [
                        'platform' => trim($platform),
                        'url' => trim($urls[$index]),
                        'icon' => trim($icons[$index] ?? '')
                    ];
                }
            }
        }

        $data = [
            'name' => trim($this->request->post('name', '')),
            'domain' => trim($this->request->post('domain', '')),
            'description' => trim($this->request->post('description', '')),
            'location' => trim($this->request->post('location', '')),
            'social_links' => json_encode($socialLinks),
            'is_public' => $this->request->post('is_public') ? 1 : 0,
        ];

        if (empty($data['name'])) {
            $this->withFlash('error', 'Company name is required.');
            $this->redirect("/admin/companies/{$id}/edit");
        }

        if ($this->request->hasFile('avatar')) {
            $file = $this->request->file('avatar');
            $avatarPath = $this->handleCompanyImage($file, 'avatar');
            if ($avatarPath) $data['avatar_image_path'] = $avatarPath;
        }

        if ($this->request->hasFile('cover')) {
            $file = $this->request->file('cover');
            $coverPath = $this->handleCompanyImage($file, 'cover');
            if ($coverPath) $data['cover_image_path'] = $coverPath;
        }

        Company::update((int)$id, $data);
        AuditService::log('company_updated', 'company', (int)$id, json_encode($company), json_encode($data));

        $this->withFlash('success', 'Company updated successfully!');
        $this->redirect("/admin/companies/{$id}/overview");
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin']);
        Company::softDelete((int)$id);
        AuditService::log('company_deleted', 'company', (int)$id);
        $this->withFlash('success', 'Company deleted.');
        $this->redirect('/admin/companies');
    }

    public function restore(string $id): void
    {
        $this->authorize(['super_admin']);
        Company::restore((int)$id);
        AuditService::log('company_restored', 'company', (int)$id);
        $this->withFlash('success', 'Company restored.');
        $this->redirect('/admin/companies');
    }

    public function overview(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) {
            $this->withFlash('error', 'Company not found.');
            $this->redirect('/admin/companies');
        }

        $logoGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'logo');
        $imageGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'image');
        $colors = BrandColor::findByCompany((int)$id);
        $fonts = BrandFont::findByCompany((int)$id);

        $this->view('companies.overview', [
            'pageTitle' => $company['name'] . ' - Overview',
            'company' => $company,
            'logoGroups' => $logoGroups,
            'imageGroups' => $imageGroups,
            'colors' => $colors,
            'fonts' => $fonts,
            'activeSection' => 'overview',
        ]);
    }

    public function library(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) {
            $this->withFlash('error', 'Company not found.');
            $this->redirect('/admin/companies');
        }

        $logoGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'logo');
        $imageGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile((int)$id, 'image');
        $colors = BrandColor::findByCompany((int)$id);
        $fonts = BrandFont::findByCompany((int)$id);

        $logosCount = BrandAssetGroup::countByCompany((int)$id, 'logo');
        $imagesCount = BrandAssetGroup::countByCompany((int)$id, 'image');
        $colorsCount = BrandColor::countByCompany((int)$id);
        $fontsCount = BrandFont::countByCompany((int)$id);

        $this->view('companies.library', [
            'pageTitle' => $company['name'] . ' - Library',
            'company' => $company,
            'logoGroups' => $logoGroups,
            'imageGroups' => $imageGroups,
            'colors' => $colors,
            'fonts' => $fonts,
            'counts' => [
                'logos' => $logosCount,
                'images' => $imagesCount,
                'colors' => $colorsCount,
                'fonts' => $fontsCount,
            ],
            'activeSection' => 'library',
        ]);
    }

    private function handleCompanyImage(array $file, string $type): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'])) return null;

        $dir = storage_path("uploads/companies/{$type}");
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = generateToken(12) . '.' . $ext;
        $path = $dir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            return "companies/{$type}/{$filename}";
        }
        return null;
    }
}
