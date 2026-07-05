<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> Logos</h1>
            <p class="text-sm text-gray-500 mt-1">Brand marks and logotypes</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openUploadModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Logo
            </button>
        </div>
    </div>

    <!-- Logos Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($groups as $group): ?>
            <?php \App\Core\View::component('logo-card', ['group' => $group, 'company' => $company]); ?>
        <?php endforeach; ?>
    </div>

    <?php if (empty($groups)): ?>
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <h3 class="text-lg font-medium text-gray-900">No logos yet</h3>
        <p class="text-gray-500 mt-1">Upload the primary and secondary logos for this brand.</p>
        <button onclick="openUploadModal()" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
            Upload First Logo
        </button>
    </div>
    <?php endif; ?>
</div>
