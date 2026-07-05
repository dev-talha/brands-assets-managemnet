<!DOCTYPE html>
<html lang="en">
<?php
$imageTools = [
    ['name' => 'All-in-one image tools', 'provider' => 'iLoveIMG', 'url' => 'https://www.iloveimg.com/'],
    ['name' => 'Compress / optimize', 'provider' => 'TinyPNG', 'url' => 'https://tinypng.com/'],
    ['name' => 'Resize / crop / convert', 'provider' => 'ImageResizer.org', 'url' => 'https://imageresizer.org/tools'],
    ['name' => 'Advanced photo edit', 'provider' => 'Photopea', 'url' => 'https://www.photopea.com/'],
    ['name' => 'Background remove', 'provider' => 'Photoroom', 'url' => 'https://www.photoroom.com/tools/background-remover'],
    ['name' => 'GIF / animation', 'provider' => 'EZGIF', 'url' => 'https://ezgif.com/'],
    ['name' => 'Privacy/local compress', 'provider' => 'Squoosh', 'url' => 'https://squoosh.app/'],
];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Brand Assets') ?></title>
    <meta name="description" content="Browse brand assets and logos">
    <link rel="icon" type="image/svg+xml" href="<?= url('/favicon.svg') ?>">
    <script>
        window.APP_URL = "<?= rtrim(env('APP_URL', ''), '/') ?>";
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0f4ff', 100: '#dbe4ff', 200: '#bac8ff', 300: '#91a7ff', 400: '#748ffc', 500: '#5c7cfa', 600: '#4c6ef5', 700: '#4263eb', 800: '#3b5bdb', 900: '#364fc7' }
                    }
                }
            }
        }
        window.ENABLE_ONLINE_EDITOR = <?= json_encode(settings('enable_online_editor', '1') === '1') ?>;
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .logo-card:hover .logo-card-actions { opacity: 1; }
        @keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* Custom Tooltip */
        [data-tooltip] { position: relative; }
        [data-tooltip]::before, [data-tooltip]::after {
            position: absolute;
            left: 50%;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            transform: translateX(-50%) translateY(4px);
            z-index: 100;
        }
        [data-tooltip]::before {
            content: "";
            bottom: 100%;
            border: 5px solid transparent;
            border-top-color: #fff;
            margin-bottom: -6px;
        }
        [data-tooltip]::after {
            content: attr(data-tooltip);
            bottom: 100%;
            background: #fff;
            color: #111827;
            font-size: 0.65rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            white-space: nowrap;
            margin-bottom: 4px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        [data-tooltip]:hover::before, [data-tooltip]:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            transition-delay: 1s;
        }
        [data-tooltip-pos="bottom"]::before, [data-tooltip-pos="bottom"]::after {
            bottom: auto;
            top: 100%;
            transform: translateX(-50%) translateY(-4px);
        }
        [data-tooltip-pos="bottom"]::before {
            border-top-color: transparent;
            border-bottom-color: #fff;
            margin-top: -6px;
            margin-bottom: 0;
        }
        [data-tooltip-pos="bottom"]::after {
            margin-top: 4px;
            margin-bottom: 0;
        }
        [data-tooltip-pos="bottom-right"]::before, [data-tooltip-pos="bottom-right"]::after {
            bottom: auto;
            top: 100%;
            left: auto;
            right: 0;
            transform: translateY(-4px);
        }
        [data-tooltip-pos="bottom-right"]::before {
            border-top-color: transparent;
            border-bottom-color: #fff;
            margin-top: -6px;
            margin-bottom: 0;
            right: 12px;
        }
        [data-tooltip-pos="bottom-right"]::after {
            margin-top: 4px;
            margin-bottom: 0;
        }
        [data-tooltip-pos="bottom"]:hover::before, [data-tooltip-pos="bottom"]:hover::after {
            transform: translateX(-50%) translateY(0);
        }
        [data-tooltip-pos="bottom-right"]:hover::before, [data-tooltip-pos="bottom-right"]:hover::after {
            transform: translateY(0);
        }
        .logo-card-actions { opacity: 0; transition: opacity 0.2s; }
        .checkerboard { background-image: linear-gradient(45deg, #e9ecef 25%, transparent 25%), linear-gradient(-45deg, #e9ecef 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e9ecef 75%), linear-gradient(-45deg, transparent 75%, #e9ecef 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; }
    </style>
</head>
<body class="bg-white min-h-screen">
    <!-- Simple top bar -->
    <nav class="border-b border-gray-100 bg-white sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="<?= url('/') ?>" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-brand-600 to-brand-800 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="font-semibold text-gray-800">Brandspace</span>
            </a>
            
            <div class="flex items-center gap-6">
                <!-- Global Brand Search -->
                <div class="relative hidden sm:block group">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="global-brand-search" placeholder="Search brands..." autocomplete="off"
                        class="w-48 lg:w-64 pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all focus:w-64 lg:focus:w-80 placeholder-gray-400">
                    
                    <!-- Search Results Dropdown -->
                    <div id="global-search-results" class="absolute top-full right-0 mt-2 w-full min-w-[240px] bg-white border border-gray-100 rounded-xl shadow-xl hidden flex-col overflow-hidden z-50 max-h-[300px] overflow-y-auto">
                        <!-- Results injected via JS -->
                    </div>
                </div>

                <a href="<?= url('/') ?>" class="text-sm font-medium text-gray-600 hover:text-brand-600 transition hidden md:block">Brands</a>
                <a href="/guidelines" class="text-sm font-medium text-gray-600 hover:text-brand-600 transition hidden md:block">Guidelines</a>
                
                <!-- Tools Dropdown (Desktop) -->
                <div class="relative hidden md:block group">
                    <button class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-brand-600 transition py-2">
                        Tools <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <!-- Invisible hover bridge -->
                    <div class="absolute top-full left-0 w-full h-2"></div>
                    <div class="absolute top-[calc(100%+0.5rem)] left-1/2 -translate-x-1/2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2">
                            <?php foreach ($imageTools as $tool): ?>
                            <a href="<?= $tool['url'] ?>" target="_blank" class="block px-4 py-2.5 hover:bg-gray-50 transition">
                                <div class="text-sm font-medium text-gray-800"><?= e($tool['name']) ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?= e($tool['provider']) ?> <i class="ri-external-link-line ml-1"></i></div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>
                <a href="/login" class="text-gray-400 hover:text-brand-600 transition flex items-center justify-center w-8 h-8 rounded-full hover:bg-brand-50" data-tooltip="Admin Login" data-tooltip-pos="bottom-right">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </a>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 -mr-2 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                    <i class="ri-menu-3-line text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/20 z-40 hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-drawer" class="fixed inset-y-0 right-0 w-72 bg-white z-50 transform translate-x-full transition-transform duration-300 shadow-2xl flex flex-col">
        <div class="p-4 flex items-center justify-between border-b border-gray-100">
            <span class="font-semibold text-gray-800">Menu</span>
            <button id="mobile-close-btn" class="p-2 -mr-2 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        <div class="p-6 flex flex-col gap-6 overflow-y-auto">
            <div class="flex flex-col gap-4">
                <a href="<?= url('/') ?>" class="text-base font-medium text-gray-800 hover:text-brand-600 transition">Brands</a>
                <a href="/guidelines" class="text-base font-medium text-gray-800 hover:text-brand-600 transition">Guidelines</a>
            </div>
            
            <div class="pt-6 border-t border-gray-100">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Tools</div>
                <div class="flex flex-col gap-4 pl-3 border-l-2 border-brand-100">
                    <?php foreach ($imageTools as $tool): ?>
                    <a href="<?= $tool['url'] ?>" target="_blank" class="block group">
                        <div class="text-sm font-medium text-gray-800 group-hover:text-brand-600 transition"><?= e($tool['name']) ?></div>
                        <div class="text-xs text-gray-500 mt-1"><?= e($tool['provider']) ?> <i class="ri-external-link-line"></i></div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <?= $content ?>
    </main>
    <footer class="border-t border-gray-100 py-8 mt-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-400">
            <div class="flex items-center gap-6 font-medium">
                <a href="<?= url('/') ?>" class="hover:text-brand-600 transition">Brands</a>
                <a href="/guidelines" class="hover:text-brand-600 transition">Guidelines</a>
            </div>
            <div>
                &copy; <?= date('Y') ?> Brandspace &middot; Powered by <a href="https://alpha.net.bd" target="_blank" class="font-medium text-gray-500 hover:text-brand-600 transition">Alpha Net</a>
            </div>
        </div>
    </footer>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium z-50';
                toast.textContent = 'Copied to clipboard!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        }

        // Global Search Autocomplete Logic
        const searchInput = document.getElementById('global-brand-search');
        const searchResults = document.getElementById('global-search-results');
        let searchTimeout;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    searchResults.classList.remove('flex');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`${window.APP_URL}/api/brands/search?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(brand => {
                                    const a = document.createElement('a');
                                    a.href = `${window.APP_URL}/brand/${brand.slug}`;
                                    a.className = 'flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0';
                                    a.innerHTML = `
                                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500 font-bold shrink-0">
                                            ${brand.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate">${brand.name}</div>
                                            <div class="text-xs text-gray-400 truncate">${brand.domain || ''}</div>
                                        </div>
                                    `;
                                    searchResults.appendChild(a);
                                });
                            } else {
                                searchResults.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500">No brands found.</div>';
                            }
                            searchResults.classList.remove('hidden');
                            searchResults.classList.add('flex');
                        });
                }, 300);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                    searchResults.classList.remove('flex');
                }
            });
            
            // Re-open if clicking input with text
            searchInput.addEventListener('focus', () => {
                if (searchInput.value.trim().length >= 2) {
                    searchResults.classList.remove('hidden');
                    searchResults.classList.add('flex');
                }
            });
        }

        // Mobile Menu Logic
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileCloseBtn = document.getElementById('mobile-close-btn');
        const mobileOverlay = document.getElementById('mobile-menu-overlay');
        const mobileDrawer = document.getElementById('mobile-drawer');

        function openMobileMenu() {
            mobileOverlay.classList.remove('hidden');
            // small delay to allow display block to apply before opacity transition
            setTimeout(() => {
                mobileOverlay.classList.remove('opacity-0');
                mobileDrawer.classList.remove('translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileOverlay.classList.add('opacity-0');
            mobileDrawer.classList.add('translate-x-full');
            setTimeout(() => {
                mobileOverlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }

        if (mobileMenuBtn && mobileCloseBtn && mobileOverlay && mobileDrawer) {
            mobileMenuBtn.addEventListener('click', openMobileMenu);
            mobileCloseBtn.addEventListener('click', closeMobileMenu);
            mobileOverlay.addEventListener('click', closeMobileMenu);
        }
    </script>
</body>
</html>
