<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
    <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <h1 class="text-4xl font-bold text-gray-900 mb-2">404</h1>
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">
        <?= e($message ?? "The page you're looking for doesn't exist or has been moved.") ?>
    </p>
    <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-brand-600 text-white rounded-xl font-medium hover:bg-brand-700 transition">
        Go Back
    </button>
</div>
