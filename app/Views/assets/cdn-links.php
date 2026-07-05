<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= e($company['name']) ?> CDN Links</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all public CDN URLs and monitor hits</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4 w-12">Preview</th>
                        <th class="px-6 py-4">Asset Details</th>
                        <th class="px-6 py-4">CDN URLs</th>
                        <th class="px-6 py-4 w-32">Status/Hits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($files as $file): ?>
                    <tr class="hover:bg-gray-50/50 transition group/row">
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-200 checkerboard flex items-center justify-center overflow-hidden">
                                <?php if (in_array($file['extension'], ['svg', 'png', 'jpg', 'jpeg', 'webp'])): ?>
                                    <img src="<?= e($file['cdn_urls']['latest']) ?>" class="max-w-full max-h-full object-contain">
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase"><?= $file['extension'] ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= e($file['group_title']) ?></div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold uppercase border border-gray-200"><?= $file['extension'] ?></span>
                                <span class="text-xs text-gray-500"><?= formatBytes($file['file_size']) ?></span>
                                <?php if ($file['width']): ?>
                                    <span class="text-xs text-gray-400">&middot; <?= $file['width'] ?>x<?= $file['height'] ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-semibold uppercase text-gray-500">Latest (Persistent)</span>
                                        <button onclick="copyToClipboard('<?= e($file['cdn_urls']['latest']) ?>')" class="text-xs text-brand-600 hover:text-brand-700 font-medium opacity-0 group-hover/row:opacity-100 transition">Copy</button>
                                    </div>
                                    <input type="text" readonly value="<?= e($file['cdn_urls']['latest']) ?>" class="w-full text-[11px] bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-gray-500 font-mono">
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-semibold uppercase text-gray-500">Versioned (Immutable)</span>
                                        <button onclick="copyToClipboard('<?= e($file['cdn_urls']['versioned']) ?>')" class="text-xs text-brand-600 hover:text-brand-700 font-medium opacity-0 group-hover/row:opacity-100 transition">Copy</button>
                                    </div>
                                    <input type="text" readonly value="<?= e($file['cdn_urls']['versioned']) ?>" class="w-full text-[11px] bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-gray-500 font-mono">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($file['is_public'] && $file['is_cdn_enabled']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100 mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-red-50 text-red-700 border border-red-100 mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Disabled
                                </span>
                            <?php endif; ?>
                            <div class="text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <?= number_format($file['hits']) ?> hits
                            </div>
                            
                            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
                            <form action="/admin/assets/<?= $file['id'] ?>/regenerate-cdn" method="POST" class="mt-3 opacity-0 group-hover/row:opacity-100 transition" onsubmit="return confirm('This will break all existing token/versioned CDN links for this file. The latest link will remain active. Proceed?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-[10px] text-orange-600 hover:text-orange-700 font-medium flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Regen Tokens
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($files)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No assets uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
