<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> Fonts</h1>
            <p class="text-sm text-gray-500 mt-1">Brand typography and web fonts</p>
        </div>
        <div class="flex gap-2">
            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
            <button onclick="document.getElementById('add-font-modal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Font
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fonts List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($fonts as $font): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between items-start transition hover:shadow-md hover:border-gray-200 group">
            <div class="w-full">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        <?= e($font['usage_type']) ?>
                    </span>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <?php if ($font['font_source']): ?>
                            <a href="<?= e($font['font_source']) ?>" target="_blank" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition" data-tooltip="Download or View Link">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Admin Actions -->
                        <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
                            <div class="flex items-center gap-1.5 <?= $font['font_source'] ? 'ml-1 pl-2.5 border-l border-gray-100' : '' ?>">
                                <button onclick="editFont(<?= htmlspecialchars(json_encode($font)) ?>)" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition" data-tooltip="Edit Font">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="/admin/fonts/<?= $font['id'] ?>/delete" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this font?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition" data-tooltip="Delete Font">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 truncate" data-tooltip="<?= e($font['name']) ?>"><?= e($font['name']) ?></h3>
                
                <?php if ($font['css_value']): ?>
                    <div class="mt-5 bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between group/css">
                        <code class="text-[11px] text-gray-600 font-mono truncate mr-2 font-medium">font-family: <?= e($font['css_value']) ?>;</code>
                        <button onclick="copyToClipboard('font-family: <?= e(addslashes($font['css_value'])) ?>;')" class="shrink-0 p-1.5 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-800 shadow-sm transition opacity-0 group-hover/css:opacity-100 focus:opacity-100" data-tooltip="Copy CSS">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($fonts)): ?>
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 border-dashed">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <h3 class="text-lg font-medium text-gray-900">No fonts defined</h3>
            <p class="text-gray-500 mt-1">Add your brand typography.</p>
            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
            <button onclick="document.getElementById('add-font-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                Add Font
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
<div id="add-font-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeFontModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden fade-in">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800" id="font-modal-title">Add Font</h3>
                <button onclick="closeFontModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="font-form" action="/admin/companies/<?= $company['id'] ?>/fonts" method="POST">
                <?= csrf_field() ?>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Font Name</label>
                        <input type="text" name="name" id="font-name" required placeholder="e.g. Inter"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usage Type</label>
                        <select name="usage_type" id="font-usage" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="primary">Primary / Headings</option>
                            <option value="secondary">Secondary / Subheadings</option>
                            <option value="body">Body / Paragraphs</option>
                            <option value="monospace">Monospace / Code</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CSS Font Family Value</label>
                        <input type="text" name="css_value" id="font-css" placeholder="'Inter', sans-serif"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Web Font Source URL (Optional)</label>
                        <input type="url" name="font_source" id="font-source" placeholder="https://fonts.googleapis.com/css2?family=Inter..."
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <p class="text-xs text-gray-500 mt-1">Google Fonts CSS link or direct @font-face URL.</p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeFontModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">Save Font</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function closeFontModal() {
    document.getElementById('add-font-modal').classList.add('hidden');
    document.getElementById('font-form').reset();
    document.getElementById('font-form').action = "/admin/companies/<?= $company['id'] ?>/fonts";
    document.getElementById('font-modal-title').textContent = 'Add Font';
}
function editFont(font) {
    document.getElementById('font-name').value = font.name;
    document.getElementById('font-usage').value = font.usage_type;
    document.getElementById('font-css').value = font.css_value;
    document.getElementById('font-source').value = font.font_source;
    document.getElementById('font-form').action = `/admin/companies/<?= $company['id'] ?>/fonts/${font.id}/edit`;
    document.getElementById('font-modal-title').textContent = 'Edit Font';
    document.getElementById('add-font-modal').classList.remove('hidden');
}
</script>
<?php endif; ?>
