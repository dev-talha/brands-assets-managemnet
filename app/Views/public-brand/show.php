<!-- Brand Header -->
<style>
.brand-cover-wrap {
    position: relative;
    margin-bottom: 70px; /* space for avatar overlap */
}
.brand-cover-bg {
    height: 190px;
    border-radius: 1.5rem 1.5rem 0 0;
    background: linear-gradient(135deg, #e8edf5 0%, #f0f4fb 40%, #e6ecf5 100%);
    position: relative;
    overflow: hidden;
}
.brand-cover-bg::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 420px; height: 420px;
    border-radius: 50%;
    border: 60px solid rgba(180,195,220,0.22);
}
.brand-cover-bg::after {
    content: '';
    position: absolute;
    right: 60px; top: 20px;
    width: 260px; height: 260px;
    border-radius: 50%;
    border: 40px solid rgba(180,195,220,0.15);
}
.brand-avatar-abs {
    position: absolute;
    bottom: -60px;
    left: 32px;
    width: 120px;
    height: 120px;
    border-radius: 22px;
    border: 4px solid #fff;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
    background: #fff;
    overflow: hidden;
    z-index: 10;
}
.brand-avatar-abs img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}
.brand-avatar-abs-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    color: #6366f1;
}
.brand-social-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
    font-size: 0.95rem;
    transition: all 0.18s ease;
    text-decoration: none;
    flex-shrink: 0;
}
.brand-social-btn:hover {
    background: #0f172a;
    border-color: #0f172a;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(15,23,42,0.25);
}
</style>

<!-- Back button -->
<div class="mb-4">
    <a href="<?= url('/') ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 font-medium transition-colors group">
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        All Brands
    </a>
</div>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm mb-12">

    <!-- Cover + Avatar together -->
    <div class="brand-cover-wrap">
        <div class="brand-cover-bg">
            <?php if (!empty($company['cover_image_path'])): ?>
                <img src="/cdn-internal/cover/<?= e($company['id']) ?>" alt="" class="w-full h-full object-cover absolute inset-0">
            <?php endif; ?>
        </div>
        <!-- Avatar: absolutely positioned at bottom of cover -->
        <div class="brand-avatar-abs">
            <?php if (!empty($company['avatar_image_path'])): ?>
                <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" alt="<?= e($company['name']) ?>">
            <?php else: ?>
                <div class="brand-avatar-abs-placeholder"><?= strtoupper(substr($company['name'], 0, 2)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info body -->
    <div class="px-6 md:px-8 pb-7">

        <!-- Name + verified badge -->
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight"><?= e($company['name']) ?></h1>
            <span data-tooltip="Verified Brand">
                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </span>
        </div>

        <!-- Description -->
        <?php if (!empty($company['description'])): ?>
        <p class="text-gray-400 text-sm leading-relaxed max-w-2xl mb-5">
            <?= e($company['description']) ?>
        </p>
        <?php else: ?>
        <div class="mb-5"></div>
        <?php endif; ?>

        <!-- Social icons + Download button -->
        <div class="flex flex-wrap items-center justify-between gap-4">

            <!-- Social icons -->
            <div class="flex items-center gap-2 flex-wrap">
                <?php if (!empty($company['domain'])): ?>
                    <a href="https://<?= e($company['domain']) ?>" target="_blank" rel="noopener noreferrer"
                       class="brand-social-btn" data-tooltip="<?= e($company['domain']) ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if (!empty($company['social_links_array'])): ?>
                    <?php foreach ($company['social_links_array'] as $social): ?>
                        <?php
                            $platform = $social['platform'] ?? '';
                            $icon     = $social['icon'] ?? '';
                            if (empty($icon)) {
                                $map = [
                                    'Facebook'    => 'ri-facebook-fill',
                                    'Instagram'   => 'ri-instagram-fill',
                                    'LinkedIn'    => 'ri-linkedin-fill',
                                    'WhatsApp'    => 'ri-whatsapp-fill',
                                    'X (Twitter)' => 'ri-twitter-x-fill',
                                    'YouTube'     => 'ri-youtube-fill',
                                    'Pinterest'   => 'ri-pinterest-fill',
                                    'Behance'     => 'ri-behance-fill',
                                    'Dribbble'    => 'ri-dribbble-fill',
                                    'Medium'      => 'ri-medium-fill',
                                    'Custom Link' => 'ri-link',
                                ];
                                $icon = $map[$platform] ?? 'ri-link';
                            }
                            $url = trim($social['url'] ?? '#');
                            if ($url !== '#' && !preg_match('~^(?:f|ht)tps?://~i', $url)) {
                                $url = 'https://' . $url;
                            }
                        ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer"
                           class="brand-social-btn" data-tooltip="<?= e(ucfirst($platform)) ?>">
                            <i class="<?= e($icon) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Download button (BLACK) -->
            <a href="/download/company/<?= $company['id'] ?>"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download All Assets
            </a>
        </div>
    </div>
</div>

<!-- Logos Section -->
<?php if (!empty($logoGroups)): ?>
<div class="mb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Logos</h2>
        <div class="h-px bg-gray-200 flex-1 ml-4"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($logoGroups as $group): ?>
            <?php \App\Core\View::component('logo-card', ['group' => $group, 'company' => $company]); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Colors Section -->
<?php if (!empty($colors)): ?>
<div class="mb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Brand Colors</h2>
        <div class="h-px bg-gray-200 flex-1 ml-4"></div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        <?php foreach ($colors as $color): ?>
            <?php \App\Core\View::component('color-card', ['color' => $color]); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Fonts Section -->
<?php if (!empty($fonts)): ?>
<div class="mb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Typography</h2>
        <div class="h-px bg-gray-200 flex-1 ml-4"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($fonts as $font): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between items-start transition hover:shadow-md hover:border-gray-200">
            <div class="w-full">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        <?= e($font['usage_type']) ?>
                    </span>
                    <?php if ($font['font_source']): ?>
                        <a href="<?= e($font['font_source']) ?>" target="_blank"
                           class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition"
                           data-tooltip="Download or View Link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
                <h3 class="text-xl font-bold text-gray-900 truncate" data-tooltip="<?= e($font['name']) ?>"><?= e($font['name']) ?></h3>
                <?php if ($font['css_value']): ?>
                    <div class="mt-5 bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between group">
                        <code class="text-[11px] text-gray-600 font-mono truncate mr-2 font-medium">font-family: <?= e($font['css_value']) ?>;</code>
                        <button onclick="copyToClipboard('font-family: <?= e(addslashes($font['css_value'])) ?>;')"
                                class="shrink-0 p-1.5 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-800 shadow-sm transition opacity-0 group-hover:opacity-100 focus:opacity-100"
                                data-tooltip="Copy CSS">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Images & Banners Section -->
<?php if (!empty($imageGroups)): ?>
<div class="mb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Images &amp; Banners</h2>
        <div class="h-px bg-gray-200 flex-1 ml-4"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($imageGroups as $group): ?>
            <?php \App\Core\View::component('logo-card', ['group' => $group, 'company' => $company]); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (empty($logoGroups) && empty($colors) && empty($fonts) && empty($imageGroups)): ?>
    <div class="text-center py-20">
        <h2 class="text-xl font-semibold text-gray-900">No public assets available yet.</h2>
        <p class="text-gray-500 mt-2">Check back later for updates.</p>
    </div>
<?php endif; ?>

<?php \App\Core\View::component('asset-drawer'); ?>
<?php \App\Core\View::component('resize-drawer'); ?>
<?php \App\Core\View::component('toast'); ?>

<script src="/assets/js/app.js"></script>
