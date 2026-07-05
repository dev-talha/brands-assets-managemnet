<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> Colors</h1>
            <p class="text-sm text-gray-500 mt-1">Brand color palette and codes</p>
        </div>
        <div class="flex gap-2">
            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
            <button onclick="document.getElementById('add-color-modal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Color
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Colors Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($colors as $color): ?>
            <div class="relative group">
                <?php \App\Core\View::component('color-card', ['color' => $color]); ?>
                
                <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1 z-10">
                    <button onclick="editColor(<?= htmlspecialchars(json_encode($color)) ?>)" class="p-1.5 bg-white/90 backdrop-blur rounded shadow hover:bg-white text-gray-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <form action="/admin/companies/<?= $company['id'] ?>/colors/<?= $color['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Delete this color?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="p-1.5 bg-white/90 backdrop-blur rounded shadow hover:bg-red-50 text-red-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($colors)): ?>
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
        <h3 class="text-lg font-medium text-gray-900">No colors defined</h3>
        <p class="text-gray-500 mt-1">Add your brand's primary and secondary colors.</p>
        <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
        <button onclick="document.getElementById('add-color-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
            Add Color
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Modal -->
<?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
<div id="add-color-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeColorModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden fade-in">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800" id="color-modal-title">Add Color</h3>
                <button onclick="closeColorModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="color-form" action="/admin/companies/<?= $company['id'] ?>/colors" method="POST">
                <?= csrf_field() ?>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color Name</label>
                        <input type="text" name="name" id="color-name" required placeholder="e.g. Primary Blue"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HEX Code</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="color-picker" class="w-10 h-10 rounded cursor-pointer border-0 p-0" oninput="document.getElementById('color-hex').value = this.value.toUpperCase()">
                            <input type="text" name="hex_code" id="color-hex" required placeholder="#000000" pattern="^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$"
                                class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                oninput="if(this.value.startsWith('#') && this.value.length === 7) document.getElementById('color-picker').value = this.value">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">RGB, HSL, and CMYK will be calculated automatically.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="color_type" id="color-type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="primary">Primary</option>
                            <option value="secondary">Secondary</option>
                            <option value="accent">Accent</option>
                            <option value="background">Background</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeColorModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">Save Color</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function closeColorModal() {
    document.getElementById('add-color-modal').classList.add('hidden');
    document.getElementById('color-form').reset();
    document.getElementById('color-form').action = `${window.APP_URL}/admin/companies/<?= $company['id'] ?>/colors`;
    document.getElementById('color-modal-title').textContent = 'Add Color';
}
function editColor(color) {
    document.getElementById('color-name').value = color.name;
    document.getElementById('color-hex').value = color.hex_code;
    document.getElementById('color-picker').value = color.hex_code;
    document.getElementById('color-type').value = color.color_type;
    document.getElementById('color-form').action = `${window.APP_URL}/admin/colors/${color.id}/update`;
    document.getElementById('color-modal-title').textContent = 'Edit Color';
    document.getElementById('add-color-modal').classList.remove('hidden');
}
</script>
<?php endif; ?>
