<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CdnService;

class CdnController extends Controller
{
    public function latest(string $companySlug, string $theme, string $assetSlug, string $ext): void
    {
        CdnService::serveLatest($companySlug, $theme, $assetSlug, $ext);
    }

    public function token(string $token, string $ext): void
    {
        CdnService::serveByToken($token, $ext);
    }

    public function versioned(string $version, string $assetSlug, string $ext): void
    {
        CdnService::serveVersioned($version, $assetSlug, $ext);
    }

    public function internalAvatar(string $id): void
    {
        $company = \App\Models\Company::findById((int)$id);
        if (!$company || empty($company['avatar_image_path'])) {
            \App\Core\Response::error(404, 'Not found');
        }
        $fullPath = storage_path('uploads/' . $company['avatar_image_path']);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        \App\Core\Response::stream($fullPath, 'avatar.' . $ext, getMimeTypeForExtension($ext), [
            'Cache-Control: public, max-age=3600'
        ]);
    }

    public function internalCover(string $id): void
    {
        $company = \App\Models\Company::findById((int)$id);
        if (!$company || empty($company['cover_image_path'])) {
            \App\Core\Response::error(404, 'Not found');
        }
        $fullPath = storage_path('uploads/' . $company['cover_image_path']);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        \App\Core\Response::stream($fullPath, 'cover.' . $ext, getMimeTypeForExtension($ext), [
            'Cache-Control: public, max-age=3600'
        ]);
    }
}
