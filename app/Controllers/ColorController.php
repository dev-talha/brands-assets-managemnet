<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;
use App\Models\BrandColor;
use App\Services\ColorService;
use App\Services\AuditService;

class ColorController extends Controller
{
    public function index(string $id): void
    {
        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $colors = BrandColor::findByCompany((int)$id);

        $this->view('companies.colors', [
            'pageTitle' => $company['name'] . ' - Colors',
            'company' => $company,
            'colors' => $colors,
            'activeSection' => 'colors',
        ]);
    }

    public function store(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $company = Company::findById((int)$id);
        if (!$company) { $this->redirect('/admin/companies'); }

        $name = trim($this->request->post('name', ''));
        $hexCode = trim($this->request->post('hex_code', ''));
        $colorType = $this->request->post('color_type', 'primary');

        if (empty($name) || empty($hexCode)) {
            $this->withFlash('error', 'Color name and hex code are required.');
            $this->redirect("/admin/companies/{$id}/colors");
        }

        // Ensure # prefix
        if (!str_starts_with($hexCode, '#')) {
            $hexCode = '#' . $hexCode;
        }

        // Auto-convert color formats
        $colorValues = ColorService::fillFromHex($hexCode);

        BrandColor::create([
            'company_id' => (int)$id,
            'name' => $name,
            'hex_code' => strtoupper($hexCode),
            'rgb_value' => $colorValues['rgb_value'],
            'hsl_value' => $colorValues['hsl_value'],
            'cmyk_value' => $colorValues['cmyk_value'],
            'color_type' => $colorType,
        ]);

        AuditService::log('color_added', 'brand_color', null, null, "{$name}: {$hexCode}");

        $this->withFlash('success', 'Color added successfully!');
        $this->redirect("/admin/companies/{$id}/colors");
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $color = BrandColor::findById((int)$id);
        if (!$color) { $this->back(); }

        $hexCode = trim($this->request->post('hex_code', ''));
        if (!str_starts_with($hexCode, '#')) $hexCode = '#' . $hexCode;

        $colorValues = ColorService::fillFromHex($hexCode);

        BrandColor::update((int)$id, [
            'name' => trim($this->request->post('name', $color['name'])),
            'hex_code' => strtoupper($hexCode),
            'rgb_value' => $colorValues['rgb_value'],
            'hsl_value' => $colorValues['hsl_value'],
            'cmyk_value' => $colorValues['cmyk_value'],
            'color_type' => $this->request->post('color_type', $color['color_type']),
        ]);

        AuditService::log('color_updated', 'brand_color', (int)$id);

        $this->withFlash('success', 'Color updated.');
        $this->redirect("/admin/companies/{$color['company_id']}/colors");
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'brand_manager']);

        $color = BrandColor::findById((int)$id);
        if (!$color) { $this->back(); }

        BrandColor::delete((int)$id);
        AuditService::log('color_deleted', 'brand_color', (int)$id);

        $this->withFlash('success', 'Color deleted.');
        $this->redirect("/admin/companies/{$color['company_id']}/colors");
    }
}
