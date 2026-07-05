<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> Overview</h1>
            <p class="text-sm text-gray-500 mt-1">Brand summary and quick actions</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openUploadModal()" class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload
            </button>
            <a href="/download/company/<?= $company['id'] ?>" class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download All
            </a>
            <?php if ($company['is_public']): ?>
            <a href="/brand/<?= e($company['slug']) ?>" target="_blank" class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-brand-600 rounded-xl text-sm font-medium hover:bg-brand-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Public Page
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats & Cover -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="h-48 bg-gray-100 relative rounded-t-2xl overflow-hidden">
            <?php if (!empty($company['cover_image_path'])): ?>
                <img src="/cdn-internal/cover/<?= e($company['id']) ?>" alt="" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full bg-gradient-to-r from-slate-200 to-slate-300 opacity-90 checkerboard"></div>
            <?php endif; ?>
        </div>
        <div class="px-6 pb-6 relative pt-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="absolute -top-12 left-6">
                <?php if (!empty($company['avatar_image_path'])): ?>
                    <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-md bg-white">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-2xl border-4 border-white shadow-md bg-white flex items-center justify-center text-gray-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?></h2>
                <p class="text-gray-500"><?= e($company['domain'] ?? 'No domain specified') ?></p>
            </div>
            
            <div class="flex gap-6 items-center flex-wrap">
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900"><?= count($logoGroups) ?></p>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Logos</p>
                </div>
                <div class="w-px h-10 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900"><?= count($colors) ?></p>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Colors</p>
                </div>
                <div class="w-px h-10 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900"><?= count($fonts) ?></p>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fonts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logos Preview -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Primary Logos</h3>
            <a href="/admin/companies/<?= $company['id'] ?>/logos" class="text-sm font-medium text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($logoGroups, 0, 4) as $group): ?>
                <?php \App\Core\View::component('logo-card', ['group' => $group, 'company' => $company]); ?>
            <?php endforeach; ?>
            <?php if (empty($logoGroups)): ?>
                <div class="col-span-full p-8 text-center bg-white border border-gray-100 rounded-2xl border-dashed">
                    <p class="text-sm text-gray-500 mb-3">No logos uploaded yet.</p>
                    <button onclick="openUploadModal()" class="text-sm font-medium text-brand-600 hover:text-brand-700">Upload Logo</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Brand Colors Preview -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Brand Colors</h3>
            <a href="/admin/companies/<?= $company['id'] ?>/colors" class="text-sm font-medium text-brand-600 hover:text-brand-700">Manage</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach (array_slice($colors, 0, 6) as $color): ?>
                <?php \App\Core\View::component('color-card', ['color' => $color]); ?>
            <?php endforeach; ?>
            <?php if (empty($colors)): ?>
                <div class="col-span-full p-8 text-center bg-white border border-gray-100 rounded-2xl border-dashed">
                    <p class="text-sm text-gray-500 mb-3">No colors defined yet.</p>
                    <a href="/admin/companies/<?= $company['id'] ?>/colors" class="text-sm font-medium text-brand-600 hover:text-brand-700">Add Color</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
