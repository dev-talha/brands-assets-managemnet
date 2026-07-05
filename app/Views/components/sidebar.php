<?php $active = $activeSection ?? ''; ?>
<!-- Mobile sidebar backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/30 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 w-60 h-[calc(100vh-64px)] bg-white border-r border-gray-100 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 overflow-y-auto">
    <div class="p-4">
        <!-- Company name -->
        <div class="mb-4">
            <a href="/admin/companies" class="flex items-center gap-2 text-xs font-medium text-gray-400 hover:text-gray-600 uppercase tracking-wider mb-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                All Companies
            </a>
            <div class="flex items-center gap-3">
                <?php if (!empty($company['avatar_image_path'])): ?>
                    <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="" class="w-9 h-9 rounded-lg object-cover border border-gray-100">
                <?php else: ?>
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center text-indigo-600 font-bold text-sm">
                        <?= strtoupper(substr($company['name'], 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 leading-tight"><?= e($company['name']) ?></h3>
                    <?php if (!empty($company['domain'])): ?>
                        <p class="text-xs text-gray-400"><?= e($company['domain']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-0.5">
            <a href="/admin/companies/<?= $company['id'] ?>/overview"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'overview' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                Overview
            </a>
            <a href="/admin/companies/<?= $company['id'] ?>/library"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'library' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Library
            </a>

            <div class="pt-3 pb-1 px-3"><span class="text-[10px] font-semibold uppercase tracking-widest text-gray-300">Assets</span></div>

            <a href="/admin/companies/<?= $company['id'] ?>/logos"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'logos' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Logos
            </a>
            <a href="/admin/companies/<?= $company['id'] ?>/colors"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'colors' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                Colors
            </a>
            <a href="/admin/companies/<?= $company['id'] ?>/fonts"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'fonts' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Fonts
            </a>
            <a href="/admin/companies/<?= $company['id'] ?>/images"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'images' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Images
            </a>
            <a href="/admin/companies/<?= $company['id'] ?>/cdn-links"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition <?= $active === 'cdn-links' ? 'active' : '' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                CDN Links
            </a>

            <div class="pt-3 pb-1 px-3"><span class="text-[10px] font-semibold uppercase tracking-widest text-gray-300">Manage</span></div>

            <a href="/admin/companies/<?= $company['id'] ?>/edit"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>

            <!-- Upload Button -->
            <div class="pt-4">
                <button onclick="openUploadModal()"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl text-sm font-medium hover:shadow-lg hover:shadow-indigo-500/25 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Upload Asset
                </button>
            </div>
        </div>
    </div>
</aside>
