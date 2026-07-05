<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Search Results</h1>
            <p class="text-sm text-gray-500 mt-1">Showing results for "<?= e($query) ?>"</p>
        </div>
    </div>

    <!-- Companies Results -->
    <?php if (!empty($companies)): ?>
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Companies</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($companies as $company): ?>
            <a href="/admin/companies/<?= $company['id'] ?>/overview" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition flex items-center gap-4">
                <?php if (!empty($company['avatar_image_path'])): ?>
                    <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                <?php else: ?>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 font-bold">
                        <?= strtoupper(substr($company['name'], 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h4 class="font-semibold text-gray-800"><?= e($company['name']) ?></h4>
                    <p class="text-sm text-gray-500"><?= e($company['domain']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Assets Results -->
    <?php if (!empty($assets)): ?>
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Assets</h2>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Asset</th>
                        <th class="px-6 py-3">Company</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Theme</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($assets as $asset): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= e($asset['title']) ?></td>
                        <td class="px-6 py-4 text-gray-500"><?= e($asset['company_name']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-gray-100 text-gray-600">
                                <?= e($asset['asset_type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500"><?= e(ucfirst($asset['theme'])) ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/companies/<?= $asset['company_id'] ?>/library" class="text-brand-600 hover:text-brand-800 font-medium text-sm">View in Library</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($companies) && empty($assets)): ?>
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <h3 class="text-lg font-medium text-gray-900">No results found</h3>
        <p class="text-gray-500 mt-1">Try adjusting your search terms.</p>
    </div>
    <?php endif; ?>
</div>
