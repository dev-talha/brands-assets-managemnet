<?php
/**
 * Logo Card Component
 * Required: $group (asset group with primary_file), $company
 */
$file = $group['primary_file'] ?? null;
$previewUrl = '';
if ($file) {
    $previewUrl = cdn_url('cdn/file/' . $file['public_token'] . '/' . $file['extension']);
}
?>
<div class="logo-card group bg-white rounded-2xl border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 cursor-pointer relative"
     onclick="openAssetDrawer(<?= $group['id'] ?>)">
    <!-- Preview area -->
    <div class="relative aspect-[4/3] bg-gray-50 flex items-center justify-center p-6 overflow-hidden rounded-t-[calc(1rem-1px)]">
        <?php if ($file && in_array($file['extension'], ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'avif'])): ?>
            <img src="<?= e($previewUrl) ?>" alt="<?= e($group['title']) ?>" class="max-w-full max-h-full object-contain" loading="lazy">
        <?php else: ?>
            <div class="flex flex-col items-center gap-2 text-gray-300">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-xs font-medium uppercase"><?= e($file['extension'] ?? 'N/A') ?></span>
            </div>
        <?php endif; ?>

        <!-- Hover actions -->
        <div class="logo-card-actions absolute inset-0 bg-black/5 flex items-end justify-center gap-2 p-3">
            <button onclick="event.stopPropagation(); openAssetDrawer(<?= $group['id'] ?>)" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-white transition text-gray-700" data-tooltip="Preview">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
            <?php if ($file && in_array($file['extension'], ['svg', 'png', 'jpg', 'jpeg', 'webp'])): ?>
            <button onclick="event.stopPropagation(); openResizeDrawer('<?= e($previewUrl) ?>', '<?= e($group['title']) ?>')" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-white transition text-gray-700" data-tooltip="Resize Image">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>
            <?php endif; ?>
            <?php if ($file): ?>
            <button onclick="event.stopPropagation(); copyToClipboard('<?= e(cdn_url('cdn/file/' . $file['public_token'] . '/' . $file['extension'])) ?>')" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-white transition text-gray-700" data-tooltip="Copy CDN Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </button>
            <?php if (settings('enable_online_editor', '1') === '1'): ?>
            <button onclick="event.stopPropagation(); editInPea('<?= e(cdn_url('cdn/file/' . $file['public_token'] . '/' . $file['extension'])) ?>', '<?= e($file['extension']) ?>')" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-brand-50 hover:text-brand-600 transition text-gray-700" data-tooltip="Edit Online">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            </button>
            <?php endif; ?>
            <a href="/download/file/<?= $file['id'] ?>" onclick="event.stopPropagation()" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-white transition text-gray-700" data-tooltip="Download">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <?php endif; ?>

            <?php if (hasRole('super_admin') || hasRole('brand_manager')): ?>
                <button onclick="event.stopPropagation(); editAsset(<?= htmlspecialchars(json_encode([
                    'id' => $group['id'],
                    'title' => $group['title'],
                    'asset_type' => $group['asset_type'],
                    'theme' => $group['theme'],
                    'description' => $group['description'],
                    'is_public' => $group['is_public']
                ])) ?>)" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-indigo-50 hover:text-indigo-600 transition text-gray-700 ml-1 border-l border-white/20" data-tooltip="Edit Asset">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <form action="/admin/assets/<?= $group['id'] ?>/delete" method="POST" class="inline m-0 p-0" onsubmit="event.stopPropagation(); return confirm('Delete this asset?');" onclick="event.stopPropagation();">
                    <?= csrf_field() ?>
                    <button type="submit" class="p-2 bg-white/90 backdrop-blur rounded-lg shadow-sm hover:bg-red-50 hover:text-red-600 transition text-gray-700" data-tooltip="Delete Asset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Theme badge -->
        <?php if ($group['theme'] !== 'default'): ?>
        <span class="absolute top-2 right-2 px-2 py-0.5 text-[10px] font-medium rounded-full <?= $group['theme'] === 'dark' ? 'bg-gray-800 text-gray-200' : 'bg-white text-gray-600 border border-gray-200' ?>">
            <?= e(ucfirst($group['theme'])) ?>
        </span>
        <?php endif; ?>
    </div>

    <!-- Info bar -->
    <div class="px-5 py-4 border-t border-gray-100 rounded-b-[calc(1rem-1px)] bg-white relative z-10">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-sm font-bold text-gray-800 truncate pr-2"><?= e($group['title']) ?></span>
            <div class="flex items-center gap-1.5 shrink-0">
                <?php if ($file): ?>
                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600 rounded-md">
                        <?= strtoupper($file['extension']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($group['file_count'] > 1): ?>
                    <span class="px-1.5 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-600 rounded-md">
                        +<?= $group['file_count'] - 1 ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs font-medium text-gray-400">
            <?php if ($file): ?>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <?= $file['width'] && $file['height'] ? $file['width'] . 'x' . $file['height'] : 'Vector' ?>
                </span>
                <span><?= formatBytes($file['file_size']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
