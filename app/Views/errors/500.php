<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
    <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h1 class="text-4xl font-bold text-gray-900 mb-2">500</h1>
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Internal Server Error</h2>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">
        <?= e($message ?? "Something went wrong on our end. Please try again later.") ?>
    </p>
    <a href="/admin/dashboard" class="inline-flex items-center justify-center px-6 py-3 bg-brand-600 text-white rounded-xl font-medium hover:bg-brand-700 transition">
        Return to Dashboard
    </a>
</div>
