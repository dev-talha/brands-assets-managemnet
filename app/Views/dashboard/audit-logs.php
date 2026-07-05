<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">System activity and user actions trail</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Entity</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            <?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($log['user_name']): ?>
                                <div class="font-medium text-gray-900"><?= e($log['user_name']) ?></div>
                                <div class="text-xs text-gray-400"><?= e($log['user_email']) ?></div>
                            <?php else: ?>
                                <span class="text-gray-400 italic">System / Guest</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <?= e($log['action']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?= e($log['entity_type']) ?>
                            <?php if ($log['entity_id']): ?>
                                <span class="text-gray-400">#<?= $log['entity_id'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                            <?= e($log['ip_address']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing <?= count($logs) ?> of <?= number_format($total) ?> logs
            </span>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
