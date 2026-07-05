<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> Library</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all brand assets</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openUploadModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Asset
            </button>
        </div>
    </div>

    <!-- Assets List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-semibold text-gray-800">All Asset Groups</h2>
            <div class="flex gap-4 text-sm">
                <span class="text-gray-500"><strong class="text-gray-900"><?= $counts['logos'] ?></strong> Logos</span>
                <span class="text-gray-500"><strong class="text-gray-900"><?= $counts['images'] ?></strong> Images</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Title & Preview</th>
                        <th class="px-6 py-4">Type / Theme</th>
                        <th class="px-6 py-4">Files</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php 
                    $allGroups = array_merge($logoGroups, $imageGroups);
                    usort($allGroups, fn($a, $b) => strtotime($b['updated_at']) - strtotime($a['updated_at']));
                    foreach ($allGroups as $group): 
                    ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200 checkerboard overflow-hidden cursor-pointer" onclick="openAssetDrawer(<?= $group['id'] ?>)">
                                    <?php if ($group['primary_file']): ?>
                                        <img src="/cdn/file/<?= $group['primary_file']['public_token'] ?>.<?= $group['primary_file']['extension'] ?>" class="max-w-[32px] max-h-[32px] object-contain">
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?= e($group['title']) ?></div>
                                    <div class="text-xs text-gray-400 font-mono"><?= e($group['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-start gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-gray-100 text-gray-600 border border-gray-200">
                                    <?= e($group['asset_type']) ?>
                                </span>
                                <span class="text-xs text-gray-500"><?= e(ucfirst($group['theme'])) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                <?= $group['file_count'] ?> format(s)
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($group['is_public']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Public
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Private
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openAssetDrawer(<?= $group['id'] ?>)" class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded transition" data-tooltip="Preview & Get Links">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <form action="/admin/assets/<?= $group['id'] ?>/toggle-visibility" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded transition" data-tooltip="Toggle Visibility">
                                        <?php if ($group['is_public']): ?>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <?php endif; ?>
                                    </button>
                                </form>
                                <?php if (hasRole('super_admin')): ?>
                                <form action="/admin/assets/<?= $group['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this asset? CDN links will break immediately.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" data-tooltip="Delete Asset">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($allGroups)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No assets found in library.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
