<div class="relative bg-white overflow-hidden pb-12 border-b border-gray-100/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12 relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 tracking-tight mb-6">
            Explore Our <span class="text-gray-900">Brand Universe</span>
        </h1>
        <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-12">
            Discover and download official logos, typography, colors, and design assets across our entire organization.
        </p>

        <!-- Search UI -->
        <form action="<?= url('/') ?>" method="GET" class="max-w-3xl mx-auto bg-white p-2 rounded-2xl shadow-lg shadow-brand-100/30 border border-gray-100 flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1 flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Search by brand name or domain..." class="w-full pl-11 pr-4 py-3 bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-gray-900 placeholder-gray-400 font-medium">
            </div>
            <button type="submit" class="px-8 py-3 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition shadow-sm whitespace-nowrap">
                Search
            </button>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Brand Count Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <?= number_format($total) ?> Brand<?= $total !== 1 ? 's' : '' ?>
            </span>
            <?php if (!empty($search)): ?>
            <span class="text-sm text-gray-500">
                results for <strong class="text-gray-800">&ldquo;<?= e($search) ?>&rdquo;</strong>
            </span>
            <?php elseif ($total > 0): ?>
            <span class="text-sm text-gray-400">
                Showing <?= (($page - 1) * $limit) + 1 ?> - <?= min($page * $limit, $total) ?> of <?= $total ?>
            </span>
            <?php endif; ?>
        </div>
        <!-- Per-page selector -->
        <form method="GET" class="flex items-center gap-2 text-sm text-gray-500">
            <?php if (!empty($search)): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
            <label for="limit-select" class="text-gray-400">Per page:</label>
            <select id="limit-select" name="limit" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                <?php foreach ([12, 24, 32, 48, 96] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($companies as $company): ?>
        <div onclick="window.location.href='<?= url('/brand/' . e($company['slug'])) ?>'" class="group block bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-gray-200 transition-all duration-300 overflow-hidden cursor-pointer">
            <div class="h-32 bg-gray-100 relative overflow-hidden">
                <?php if (!empty($company['cover_image_path'])): ?>
                    <img src="/cdn-internal/cover/<?= e($company['id']) ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 checkerboard group-hover:scale-105 transition-transform duration-500"></div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
            </div>
            
            <div class="px-6 pb-6 relative pt-10">
                <div class="absolute -top-10 left-6">
                    <?php if (!empty($company['avatar_image_path'])): ?>
                        <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-16 h-16 rounded-2xl object-cover border-4 border-white shadow-md bg-white">
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-2xl border-4 border-white shadow-md bg-white flex items-center justify-center text-gray-500 font-bold text-xl">
                            <?= strtoupper(substr($company['name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h3 class="font-bold text-gray-900 text-xl leading-tight mb-3 truncate group-hover:text-brand-600 transition-colors"><?= e($company['name']) ?></h3>
                
                <?php 
                    $socialLinksStr = $company['social_links'] ?? '[]';
                    if (empty($socialLinksStr)) $socialLinksStr = '[]';
                    $socialLinks = json_decode($socialLinksStr, true); 
                    if (!is_array($socialLinks)) $socialLinks = [];
                    
                    if (!empty($company['domain'])) {
                        $hasWebsite = false;
                        foreach ($socialLinks as $sl) {
                            if (($sl['platform'] ?? '') === 'website') {
                                $hasWebsite = true;
                                break;
                            }
                        }
                        if (!$hasWebsite) {
                            $socialLinks = array_merge([[
                                'platform' => 'website',
                                'icon' => 'ri-global-line',
                                'url' => $company['domain']
                            ]], $socialLinks);
                        }
                    }
                    
                    if (count($socialLinks) > 0): 
                        $hasSocials = false;
                ?>
                <div class="flex items-center gap-2 mb-4 text-gray-400">
                    <?php 
                        foreach ($socialLinks as $link): 
                            if (!is_array($link)) continue;
                            if (empty($link['icon'])) continue;
                            $hasSocials = true;
                            $url = trim($link['url'] ?? '#');
                            if ($url !== '#' && !preg_match('~^(?:f|ht)tps?://~i', $url)) {
                                $url = 'https://' . $url;
                            }
                    ?>
                        <a href="<?= e($url) ?>" target="_blank" onclick="event.stopPropagation()" class="w-6 h-6 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center hover:text-brand-600 hover:bg-brand-50 hover:border-brand-200 transition-colors" data-tooltip="<?= e(ucfirst($link['platform'] ?? '')) ?>">
                            <i class="<?= e($link['icon']) ?> text-xs"></i>
                        </a>
                    <?php endforeach; ?>
                    <?php if (!$hasSocials): ?><div class="h-6"></div><?php endif; ?>
                </div>
                <?php else: ?>
                <div class="h-6 mb-4"></div>
                <?php endif; ?>
                
                <div class="flex items-center text-sm font-medium text-brand-600 group-hover:text-brand-700">
                    View Brand
                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($companies)): ?>
    <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 border-dashed mt-8">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <h3 class="text-xl font-medium text-gray-900">No Brands Found</h3>
        <p class="text-gray-500 mt-2">Try adjusting your search query.</p>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="mt-16 flex items-center justify-center gap-2">
        <?php 
            $currentUrl = '/?';
            if (!empty($search)) $currentUrl .= 'search=' . urlencode($search) . '&';
            $currentUrl .= 'limit=' . $limit . '&page=';
        ?>
        
        <!-- Prev button -->
        <a href="<?= $page > 1 ? $currentUrl . ($page - 1) : '#' ?>" 
           class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 <?= $page > 1 ? 'text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition' : 'text-gray-300 cursor-not-allowed pointer-events-none' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        
        <div class="flex items-center gap-1 mx-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || abs($page - $i) <= 1): ?>
                    <a href="<?= $currentUrl . $i ?>" 
                       class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium transition <?= $i == $page ? 'bg-brand-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-brand-600' ?>">
                        <?= $i ?>
                    </a>
                <?php elseif (abs($page - $i) == 2): ?>
                    <span class="text-gray-400 px-1">...</span>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

        <!-- Next button -->
        <a href="<?= $page < $totalPages ? $currentUrl . ($page + 1) : '#' ?>" 
           class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 <?= $page < $totalPages ? 'text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition' : 'text-gray-300 cursor-not-allowed pointer-events-none' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    <?php endif; ?>
</div>
