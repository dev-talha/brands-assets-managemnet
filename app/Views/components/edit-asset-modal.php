<!-- Edit Asset Modal -->
<div id="edit-asset-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditAssetModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-full fade-in">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Edit Asset Details</h3>
                <button onclick="closeEditAssetModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form action="" method="POST" id="edit-asset-form">
                    <?= csrf_field() ?>
                    <div class="space-y-5">
                        <!-- Asset Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asset Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="edit-asset-title" required placeholder="e.g. Main Logo"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="asset_type" id="edit-asset-type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                                    <option value="logo">Logo</option>
                                    <option value="image">Image / Banner</option>
                                    <option value="document">Document / PDF</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                                <select name="theme" id="edit-asset-theme" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                                    <option value="default">Default</option>
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                            <textarea name="description" id="edit-asset-description" rows="2"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400"></textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_public" id="edit-asset-public" value="1" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <label for="edit-asset-public" class="text-sm text-gray-700">Public (Asset and CDN links will be public)</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeEditAssetModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</button>
                <button type="submit" form="edit-asset-form" class="px-5 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-sm">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    function closeEditAssetModal() {
        document.getElementById('edit-asset-modal').classList.add('hidden');
    }

    function editAsset(asset) {
        document.getElementById('edit-asset-form').action = `${window.APP_URL}/admin/assets/${asset.id}/update`;
        document.getElementById('edit-asset-title').value = asset.title;
        document.getElementById('edit-asset-type').value = asset.asset_type;
        document.getElementById('edit-asset-theme').value = asset.theme;
        document.getElementById('edit-asset-description').value = asset.description || '';
        document.getElementById('edit-asset-public').checked = asset.is_public == 1;
        document.getElementById('edit-asset-modal').classList.remove('hidden');
    }
</script>
