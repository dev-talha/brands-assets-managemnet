<?php
/**
 * Route definitions.
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\CompanyController;
use App\Controllers\AssetController;
use App\Controllers\ColorController;
use App\Controllers\FontController;
use App\Controllers\CdnController;
use App\Controllers\PublicBrandController;
use App\Controllers\BackupController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// --- Public Routes ---
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// Public brand page
$router->get('/', [PublicBrandController::class, 'index']);
$router->get('/api/brands/search', [PublicBrandController::class, 'searchApi']);
$router->get('/guidelines', [PublicBrandController::class, 'guidelines']);
$router->get('/brand/{slug}', [PublicBrandController::class, 'show']);

// CDN routes (public, no auth)
$router->get('/cdn/{companySlug}/{theme}/{assetSlug}/{ext}', [CdnController::class, 'latest']);
$router->get('/cdn/file/{token}/{ext}', [CdnController::class, 'token']);

$router->get('/cdn/v/{version}/{assetSlug}/{ext}', [CdnController::class, 'versioned']);

// Download routes
$router->get('/download/file/{id}', [AssetController::class, 'downloadFile']);
$router->get('/download/asset/{id}', [AssetController::class, 'downloadAssetZip']);
$router->get('/download/company/{id}', [AssetController::class, 'downloadCompanyZip']);

// Internal CDN for avatars and covers (no auth required for public view compatibility)
$router->get('/cdn-internal/avatar/{id}', [CdnController::class, 'internalAvatar']);
$router->get('/cdn-internal/cover/{id}', [CdnController::class, 'internalCover']);

// Asset detail (AJAX API for both public and admin)
$router->get('/api/assets/{id}/detail', [AssetController::class, 'detail']);

// --- Admin Routes (require auth) ---
$router->middleware([AuthMiddleware::class, CsrfMiddleware::class])->group(function ($router) {
    // Dashboard
    $router->get('/admin', [DashboardController::class, 'index']);
    $router->get('/admin/dashboard', [DashboardController::class, 'index']);

    // Profile
    $router->get('/admin/profile', [ProfileController::class, 'index']);
    $router->post('/admin/profile/password', [ProfileController::class, 'updatePassword']);

    // Users
    $router->get('/admin/users', [UserController::class, 'index']);
    $router->get('/admin/users/create', [UserController::class, 'create']);
    $router->post('/admin/users', [UserController::class, 'store']);
    $router->get('/admin/users/{id}/edit', [UserController::class, 'edit']);
    $router->post('/admin/users/{id}/update', [UserController::class, 'update']);
    $router->post('/admin/users/{id}/delete', [UserController::class, 'delete']);

    // Companies
    $router->get('/admin/companies', [CompanyController::class, 'index']);
    $router->get('/admin/companies/create', [CompanyController::class, 'create']);
    $router->post('/admin/companies', [CompanyController::class, 'store']);
    $router->get('/admin/companies/{id}/edit', [CompanyController::class, 'edit']);
    $router->post('/admin/companies/{id}/update', [CompanyController::class, 'update']);
    $router->post('/admin/companies/{id}/delete', [CompanyController::class, 'delete']);
    $router->post('/admin/companies/{id}/restore', [CompanyController::class, 'restore']);

    // Company sections
    $router->get('/admin/companies/{id}/overview', [CompanyController::class, 'overview']);
    $router->get('/admin/companies/{id}/library', [CompanyController::class, 'library']);
    $router->get('/admin/companies/{id}/logos', [AssetController::class, 'logos']);
    $router->get('/admin/companies/{id}/colors', [ColorController::class, 'index']);
    $router->get('/admin/companies/{id}/fonts', [FontController::class, 'index']);
    $router->get('/admin/companies/{id}/images', [AssetController::class, 'images']);
    $router->get('/admin/companies/{id}/cdn-links', [AssetController::class, 'cdnLinks']);

    // Assets
    $router->post('/admin/companies/{id}/assets/upload', [AssetController::class, 'upload']);
    $router->post('/admin/assets/{id}/update', [AssetController::class, 'update']);
    $router->post('/admin/assets/{id}/replace', [AssetController::class, 'replace']);
    $router->post('/admin/assets/{id}/delete', [AssetController::class, 'deleteAsset']);
    $router->post('/admin/assets/{id}/restore', [AssetController::class, 'restoreAsset']);
    $router->post('/admin/assets/{id}/visibility', [AssetController::class, 'toggleVisibility']);
    $router->post('/admin/assets/{id}/regenerate-cdn', [AssetController::class, 'regenerateCdn']);

    // Colors
    $router->post('/admin/companies/{id}/colors', [ColorController::class, 'store']);
    $router->post('/admin/colors/{id}/update', [ColorController::class, 'update']);
    $router->post('/admin/colors/{id}/delete', [ColorController::class, 'delete']);

    // Fonts
    $router->post('/admin/companies/{id}/fonts', [FontController::class, 'store']);
    $router->post('/admin/fonts/{id}/update', [FontController::class, 'update']);
    $router->post('/admin/fonts/{id}/delete', [FontController::class, 'delete']);

    // Search
    $router->get('/admin/search', [DashboardController::class, 'search']);

    // Audit logs
    $router->get('/admin/audit-logs', [DashboardController::class, 'auditLogs']);

    // Settings
    $router->get('/admin/settings', [DashboardController::class, 'settings']);
    $router->post('/admin/settings', [DashboardController::class, 'updateSettings']);

    // Backups
    $router->get('/admin/backups', [BackupController::class, 'index']);
    $router->post('/admin/backups/create', [BackupController::class, 'create']);
});
