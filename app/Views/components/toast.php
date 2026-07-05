<!-- Global Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2"></div>

<template id="toast-template">
    <div class="toast rounded-lg px-4 py-3 text-sm font-medium shadow-lg bg-gray-900 text-white flex items-center gap-2 fade-in">
        <svg class="w-5 h-5 text-emerald-400 toast-icon-success hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg class="w-5 h-5 text-red-400 toast-icon-error hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg class="w-5 h-5 text-blue-400 toast-icon-info hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="toast-message"></span>
    </div>
</template>
