<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">System overview and recent activity</p>
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

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Main stats taking more space -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm col-span-1 md:col-span-2 lg:col-span-2">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Companies</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_companies']) ?></h3>
                </div>
            </div>
            <a href="/admin/companies" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm col-span-1 md:col-span-2 lg:col-span-2">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Brand Assets</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_assets']) ?></h3>
                </div>
            </div>
            <p class="text-xs text-gray-400">Total groups across all brands</p>
        </div>

        <!-- Smaller stats -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm col-span-1 md:col-span-2 lg:col-span-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">CDN Hits (Month)</p>
            <h3 class="text-2xl font-bold text-gray-900 mb-4"><?= number_format($stats['cdn_hits_month']) ?></h3>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Storage Used</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $stats['storage_usage'] ?></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Companies List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Companies</h3>
                    <a href="/admin/companies" class="text-sm font-medium text-brand-600 hover:text-brand-700">View all</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php foreach ($companies as $company): ?>
                    <a href="/admin/companies/<?= $company['id'] ?>/overview" class="block p-5 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <?php if (!empty($company['avatar_image_path'])): ?>
                                <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100 bg-white">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-bold">
                                    <?= strtoupper(substr($company['name'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-gray-800 truncate"><?= e($company['name']) ?></h4>
                                <p class="text-sm text-gray-500 truncate"><?= e($company['domain'] ?? 'No domain') ?></p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $company['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= ucfirst($company['status']) ?>
                                </span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($companies)): ?>
                        <div class="p-8 text-center text-gray-500 text-sm">No companies found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Uploads -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Recent Uploads</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php foreach ($recentUploads as $upload): ?>
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0">
                            <?php if (in_array($upload['extension'], ['svg', 'png', 'jpg', 'jpeg'])): ?>
                                <img src="<?= e(cdn_url("cdn/file/{$upload['public_token']}.{$upload['extension']}")) ?>" class="max-w-[24px] max-h-[24px] object-contain">
                            <?php else: ?>
                                <span class="text-[10px] font-bold text-gray-400 uppercase"><?= $upload['extension'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate"><?= e($upload['group_title']) ?></p>
                            <p class="text-xs text-gray-400 truncate"><?= e($upload['company_name']) ?></p>
                        </div>
                        <div class="text-[10px] text-gray-400 shrink-0">
                            <?= date('M d', strtotime($upload['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentUploads)): ?>
                        <div class="p-6 text-center text-gray-500 text-sm">No recent uploads.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
