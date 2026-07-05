<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;
use App\Models\BrandFont;
use App\Services\AuditService;

class FontController extends Controller
{
    public function index(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $fonts = BrandFont::findByCompany((int)$id);

        $this->view('companies.fonts', [
            'pageTitle' => $company['name'] . ' - Fonts',
            'company' => $company,
            'fonts' => $fonts,
            'activeSection' => 'fonts',
        ]);
    }

    public function store(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $name = trim($this->request->post('name', ''));
        $usageType = $this->request->post('usage_type', 'body');
        $fontSource = trim($this->request->post('font_source', ''));
        $cssValue = trim($this->request->post('css_value', ''));

        if (empty($name)) {
            $this->withFlash('error', 'Font name is required.');
            $this->redirect("/admin/companies/{$id}/fonts");
        }

        BrandFont::create([
            'company_id' => (int)$id,
            'name' => $name,
            'usage_type' => $usageType,
            'font_source' => $fontSource,
            'css_value' => $cssValue,
        ]);

        AuditService::log('font_added', 'brand_font', null, null, $name);

        $this->withFlash('success', 'Font added!');
        $this->redirect("/admin/companies/{$id}/fonts");
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $font = BrandFont::findById((int)$id);
        if (!$font) { $this->back(); }

        BrandFont::update((int)$id, [
            'name' => trim($this->request->post('name', $font['name'])),
            'usage_type' => $this->request->post('usage_type', $font['usage_type']),
            'font_source' => trim($this->request->post('font_source', '')),
            'css_value' => trim($this->request->post('css_value', '')),
        ]);

        $this->withFlash('success', 'Font updated.');
        $this->redirect("/admin/companies/{$font['company_id']}/fonts");
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $font = BrandFont::findById((int)$id);
        if (!$font) { $this->back(); }

        BrandFont::delete((int)$id);
        $this->withFlash('success', 'Font deleted.');
        $this->redirect("/admin/companies/{$font['company_id']}/fonts");
    }
}
