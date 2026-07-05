<?php
/**
 * Color Card Component
 * Required: $color (brand_colors array)
 */
?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow group">
    <!-- Swatch -->
    <div class="h-32 w-full flex items-end justify-end p-3 relative cursor-pointer" style="background-color: <?= e($color['hex_code']) ?>"
         onclick="copyToClipboard('<?= e($color['hex_code']) ?>')">
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
            <span class="bg-white/90 backdrop-blur text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy HEX
            </span>
        </div>
    </div>
    <!-- Details -->
    <div class="p-4">
        <h4 class="text-sm font-semibold text-gray-800 truncate mb-2"><?= e($color['name']) ?></h4>
        <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs group/copy cursor-pointer" onclick="copyToClipboard('<?= e($color['hex_code']) ?>')">
                <span class="text-gray-400 uppercase font-medium">HEX</span>
                <span class="text-gray-700 font-mono group-hover/copy:text-brand-600"><?= e($color['hex_code']) ?></span>
            </div>
            <?php if (!empty($color['rgb_value'])): ?>
            <div class="flex items-center justify-between text-xs group/copy cursor-pointer" onclick="copyToClipboard('<?= e($color['rgb_value']) ?>')">
                <span class="text-gray-400 uppercase font-medium">RGB</span>
                <span class="text-gray-700 font-mono group-hover/copy:text-brand-600 truncate max-w-[120px] text-right"><?= e($color['rgb_value']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($color['hsl_value'])): ?>
            <div class="flex items-center justify-between text-xs group/copy cursor-pointer" onclick="copyToClipboard('<?= e($color['hsl_value']) ?>')">
                <span class="text-gray-400 uppercase font-medium">HSL</span>
                <span class="text-gray-700 font-mono group-hover/copy:text-brand-600 truncate max-w-[120px] text-right"><?= e($color['hsl_value']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
