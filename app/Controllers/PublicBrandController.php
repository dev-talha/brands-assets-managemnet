<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Company;
use App\Models\BrandAssetGroup;
use App\Models\BrandColor;
use App\Models\BrandFont;

class PublicBrandController extends Controller
{
    public function index(): void
    {
        if (settings('public_brand_pages') === '0') {
            http_response_code(404);
            View::render('errors.404', ['pageTitle' => 'Not Found'], 'public');
            return;
        }

        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        $allowedLimits = [12, 24, 32, 48, 96];
        $limit = (int)($_GET['limit'] ?? 32);
        if (!in_array($limit, $allowedLimits)) {
            $limit = 32;
        }

        $offset = ($page - 1) * $limit;
        
        $whereClause = "is_public = 1 AND status = 'active' AND deleted_at IS NULL";
        $params = [];
        
        if ($search !== '') {
            $whereClause .= " AND (name LIKE ? OR domain LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
        }

        // Count total for pagination
        $totalRow = db()->query("SELECT COUNT(*) as count FROM companies WHERE {$whereClause}", $params)->fetch();
        $total = $totalRow['count'] ?? 0;
        $totalPages = ceil($total / $limit);

        // Fetch paginated results
        $companies = db()->query(
            "SELECT * FROM companies WHERE {$whereClause} ORDER BY sort_order ASC, name ASC LIMIT {$limit} OFFSET {$offset}",
            $params
        )->fetchAll();

        View::render('public-brand.index', [
            'pageTitle' => 'Public Brands Directory',
            'companies' => $companies,
            'search' => $search,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => $totalPages
        ], 'public');
    }

    public function guidelines(): void
    {
        if (settings('public_brand_pages') === '0') {
            http_response_code(404);
            View::render('errors.404', ['pageTitle' => 'Not Found'], 'public');
            return;
        }

        View::render('public-brand.guidelines', [
            'pageTitle' => 'General Brand Guidelines'
        ], 'public');
    }

    public function show(string $slug): void
    {
        if (settings('public_brand_pages') === '0') {
            http_response_code(404);
            View::render('errors.404', ['pageTitle' => 'Not Found'], 'public');
            return;
        }

        $company = Company::findBySlug($slug);
        if (!$company || !$company['is_public'] || $company['status'] !== 'active') {
            http_response_code(404);
            View::render('errors.404', ['pageTitle' => 'Not Found'], 'public');
            return;
        }

        // Get public & approved assets only
        $logoGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile($company['id'], 'logo');
        $logoGroups = array_filter($logoGroups, fn($g) => $g['is_public'] && $g['status'] === 'approved');

        $imageGroups = BrandAssetGroup::getCompanyGroupsWithPrimaryFile($company['id'], 'image');
        $imageGroups = array_filter($imageGroups, fn($g) => $g['is_public'] && $g['status'] === 'approved');

        $colors = BrandColor::findByCompany($company['id']);
        $fonts = BrandFont::findByCompany($company['id']);

        View::render('public-brand.show', [
            'pageTitle' => $company['name'] . ' - Brand Assets',
            'company' => $company,
            'logoGroups' => $logoGroups,
            'imageGroups' => $imageGroups,
            'colors' => $colors,
            'fonts' => $fonts,
        ], 'public');
    }
    public function searchApi(): void
    {
        if (settings('public_brand_pages') === '0') {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        header('Content-Type: application/json');
        $search = trim($_GET['q'] ?? '');
        if (empty($search)) {
            echo json_encode([]);
            return;
        }

        $searchTerm = "%{$search}%";
        $companies = db()->query(
            "SELECT id, name, slug, domain FROM companies WHERE is_public = 1 AND status = 'active' AND deleted_at IS NULL AND (name LIKE ? OR domain LIKE ?) ORDER BY sort_order ASC, name ASC LIMIT 10",
            [$searchTerm, $searchTerm]
        )->fetchAll();

        echo json_encode($companies);
    }
}
