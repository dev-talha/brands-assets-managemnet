<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Backups</h1>
            <p class="text-sm text-gray-500 mt-1">Manage database and storage backups</p>
        </div>
        <div>
            <form action="/admin/backups/create" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Create Backup Now
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Filename</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Size</th>
                        <th class="px-6 py-4">Created Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($backups as $backup): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 font-mono text-xs">
                            <?= e($backup['name']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (str_contains($backup['name'], 'db')): ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase bg-blue-50 text-blue-600 border border-blue-100">Database</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">Uploads (ZIP)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= e($backup['size']) ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= e($backup['created']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($backups)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No backups found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-500 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Backups are stored in <code>storage/backups</code>. Please download them manually from the server for offsite storage.
            </p>
        </div>
    </div>
</div>
