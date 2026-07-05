<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Companies</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all brands and sister concerns</p>
        </div>
        <div class="flex gap-3">
            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
            <a href="/admin/companies/create" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Company
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-wrap gap-4">
        <form action="/admin/companies" method="GET" class="flex-1 flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search companies..."
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                </div>
            </div>
            <select name="status" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                <option value="">All Statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-sm">Filter</button>
            <?php if (!empty($filters)): ?>
                <a href="/admin/companies" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 mt-1">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Companies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($companies as $company): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
            <div class="h-32 bg-gray-100 relative">
                <?php if (!empty($company['cover_image_path'])): ?>
                    <img src="/cdn-internal/cover/<?= e($company['id']) ?>" alt="" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 opacity-50 checkerboard"></div>
                <?php endif; ?>
                <div class="absolute top-3 right-3 flex gap-2">
                    <?php if ($company['is_public']): ?>
                        <span class="bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-emerald-600 shadow-sm border border-emerald-100">Public</span>
                    <?php else: ?>
                        <span class="bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-gray-500 shadow-sm border border-gray-200">Private</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="px-6 pb-6 relative pt-10">
                <div class="absolute -top-10 left-6">
                    <?php if (!empty($company['avatar_image_path'])): ?>
                        <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-16 h-16 rounded-xl object-cover border-4 border-white shadow-sm bg-white">
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-xl border-4 border-white shadow-sm bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                            <?= strtoupper(substr($company['name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1 truncate"><?= e($company['name']) ?></h3>
                <p class="text-sm text-gray-500 truncate mb-4"><?= e($company['domain'] ?? 'No domain') ?></p>

                <div class="flex gap-2">
                    <a href="/admin/companies/<?= $company['id'] ?>/overview" class="flex-1 text-center py-2 bg-gray-50 hover:bg-brand-50 border border-gray-100 hover:border-brand-100 text-gray-700 hover:text-brand-700 rounded-xl text-sm font-medium transition">
                        Manage Assets
                    </a>
                    <?php if ($company['is_public']): ?>
                    <a href="/brand/<?= e($company['slug']) ?>" target="_blank" class="p-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 rounded-xl text-gray-500 hover:text-gray-700 transition" data-tooltip="View Public Page">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($companies)): ?>
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <h3 class="text-lg font-medium text-gray-900">No companies found</h3>
        <p class="text-gray-500 mt-1">Get started by creating your first company.</p>
        <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
        <a href="/admin/companies/create" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Company
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
