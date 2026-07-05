<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;
use App\Models\BrandAssetFile;
use App\Models\BrandAssetGroup;
use App\Models\AuditLog;
use App\Models\Download;
use App\Services\CdnService;

class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            'total_companies' => Company::count(),
            'total_assets' => BrandAssetGroup::countAll(),
            'total_files' => BrandAssetFile::countAll(),
            'public_cdn_links' => BrandAssetFile::countPublicCdn(),
            'cdn_hits_month' => CdnService::getMonthlyHits(),
            'storage_usage' => formatBytes(BrandAssetFile::getTotalStorageBytes()),
        ];

        $recentUploads = BrandAssetFile::getRecentUploads(8);
        $companies = Company::all(['limit' => 6]);

        $this->view('dashboard.index', [
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'recentUploads' => $recentUploads,
            'companies' => $companies,
        ]);
    }

    public function search(): void
    {
        $q = trim($this->request->get('q', ''));
        if (empty($q)) {
            $this->redirect('/admin/dashboard');
        }

        $companies = Company::all(['search' => $q]);

        $searchTerm = '%' . $q . '%';
        $assets = db()->query(
            'SELECT ag.*, c.name AS company_name, c.slug AS company_slug, c.id AS company_id
             FROM brand_asset_groups ag
             JOIN companies c ON c.id = ag.company_id
             WHERE ag.deleted_at IS NULL AND c.deleted_at IS NULL
             AND (ag.title LIKE ? OR ag.asset_type LIKE ? OR ag.tags LIKE ? OR c.name LIKE ?)
             ORDER BY ag.updated_at DESC
             LIMIT 50',
            [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
        )->fetchAll();

        $this->view('dashboard.search', [
            'pageTitle' => 'Search: ' . e($q),
            'query' => $q,
            'companies' => $companies,
            'assets' => $assets,
        ]);
    }

    public function auditLogs(): void
    {
        $this->authorize(['super_admin']);

        $page = max(1, (int) $this->request->get('page', 1));
        $perPage = 30;
        $offset = ($page - 1) * $perPage;

        $logs = AuditLog::all($perPage, $offset);
        $total = AuditLog::count();
        $totalPages = ceil($total / $perPage);

        $this->view('dashboard.audit-logs', [
            'pageTitle' => 'Audit Logs',
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    public function settings(): void
    {
        $this->authorize(['super_admin']);

        $settings = [];
        $rows = db()->query('SELECT * FROM settings')->fetchAll();
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->view('dashboard.settings', [
            'pageTitle' => 'Settings',
            'settings' => $settings,
        ]);
    }

    public function updateSettings(): void
    {
        $this->authorize(['super_admin']);

        $keys = ['app_name', 'cdn_base_url', 'max_upload_mb', 'public_brand_pages', 'cdn_logging_enabled', 'login_max_attempts', 'login_lockout_minutes', 'cdn_access_limit', 'cdn_expiration_days', 'enable_online_editor'];

        foreach ($keys as $key) {
            $value = $this->request->post($key, '');
            if ($key === 'cdn_expiration_days' && (int)$value > 15) {
                $value = '15'; // Enforce max 15 days
            }
            db()->query(
                'INSERT INTO settings (key, value, updated_at) VALUES (?, ?, ?)
                 ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at',
                [$key, $value, now()]
            );
        }

        $this->withFlash('success', 'Settings updated successfully.');
        $this->redirect('/admin/settings');
    }
}
