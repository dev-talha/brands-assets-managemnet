<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Brand CDN Manager') ?></title>
    <meta name="description" content="Sister Concern Brand CDN Manager - Manage brand assets and logos">
    <link rel="icon" type="image/svg+xml" href="<?= url('/favicon.svg') ?>">
    <script>
        window.APP_URL = "<?= rtrim(env('APP_URL', ''), '/') ?>";
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
        .sidebar-link.active { background: #f1f3f5; color: #4263eb; font-weight: 500; }
        .logo-card:hover .logo-card-actions { opacity: 1; }
        .logo-card-actions { opacity: 0; transition: opacity 0.2s; }
        .drawer-backdrop { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .checkerboard { background-image: linear-gradient(45deg, #e9ecef 25%, transparent 25%), linear-gradient(-45deg, #e9ecef 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e9ecef 75%), linear-gradient(-45deg, transparent 75%, #e9ecef 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; }
        .toast { animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 2.7s; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        .fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation -->
    <?php \App\Core\View::component('top-nav', ['pageTitle' => $pageTitle ?? '']); ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php if (isset($company)): ?>
            <?php \App\Core\View::component('sidebar', ['company' => $company, 'activeSection' => $activeSection ?? '']); ?>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="flex-1 min-h-[calc(100vh-64px)] <?= isset($company) ? 'ml-0 lg:ml-60' : '' ?>">
            <!-- Flash Messages -->
            <?php $flash = flash(); if ($flash): ?>
                <div class="mx-4 mt-4">
                    <div class="toast rounded-lg px-4 py-3 text-sm font-medium shadow-lg
                        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' ?>"
                        id="flash-toast">
                        <div class="flex items-center gap-2">
                            <?php if ($flash['type'] === 'success'): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php endif; ?>
                            <span><?= e($flash['message']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="p-4 lg:p-6">
                <?= $content ?>
            </div>
        </main>
    </div>

    <!-- Asset Preview Drawer -->
    <?php \App\Core\View::component('asset-drawer'); ?>

    <!-- Upload Modal -->
    <?php if (isset($company)): ?>
        <?php \App\Core\View::component('upload-modal', ['company' => $company]); ?>
        <?php \App\Core\View::component('edit-asset-modal'); ?>
    <?php endif; ?>

    <!-- Toast container -->
    <?php \App\Core\View::component('toast'); ?>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="/assets/js/app.js"></script>
    <script>
        // Auto-dismiss flash
        setTimeout(() => { const el = document.getElementById('flash-toast'); if (el) el.remove(); }, 3000);
    </script>
</body>
</html>
