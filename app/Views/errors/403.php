<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h1 class="text-4xl font-bold text-gray-900 mb-2">403</h1>
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Access Denied</h2>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">
        <?= e($message ?? "You don't have permission to access this page or perform this action.") ?>
    </p>
    <a href="/admin/dashboard" class="inline-flex items-center justify-center px-6 py-3 bg-brand-600 text-white rounded-xl font-medium hover:bg-brand-700 transition">
        Return to Dashboard
    </a>
</div>
