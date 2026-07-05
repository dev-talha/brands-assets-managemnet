<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="/admin/companies/<?= $company['id'] ?>/overview" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Overview
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Company Settings</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <form action="/admin/companies/<?= $company['id'] ?>/update" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="p-6 md:p-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?= e($company['name']) ?>" required
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                        <input type="text" name="domain" value="<?= e($company['domain'] ?? '') ?>" placeholder="example.com"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"><?= e($company['description'] ?? '') ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" value="<?= e($company['location'] ?? '') ?>" placeholder="e.g. New York, USA"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Update Avatar / Icon</label>
                        <?php if (!empty($company['avatar_image_path'])): ?>
                            <div class="mb-2">
                                <img src="/cdn-internal/avatar/<?= e($company['id']) ?>" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Update Cover Image</label>
                        <?php if (!empty($company['cover_image_path'])): ?>
                            <div class="mb-2">
                                <img src="/cdn-internal/cover/<?= e($company['id']) ?>" class="w-24 h-12 rounded-lg object-cover border border-gray-200">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="cover" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                </div>

                <!-- Social Links Section -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Social Media Links</h3>
                        <button type="button" onclick="addSocialLink()" class="px-3 py-1.5 text-sm bg-brand-50 text-brand-700 rounded-lg font-medium hover:bg-brand-100 transition">
                            + Add Link
                        </button>
                    </div>
                    <div id="social-links-container" class="space-y-3">
                        <!-- JS dynamic fields will go here -->
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" <?= $company['is_public'] ? 'checked' : '' ?> class="mt-1 w-4 h-4 text-brand-600 rounded border-gray-300 focus:ring-brand-500">
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Make Company Public</span>
                            <span class="block text-xs text-gray-500">If public, the brand page will be visible to guests.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <a href="/admin/companies/<?= $company['id'] ?>/overview" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    
    <!-- Danger Zone -->
    <?php if (hasRole('super_admin')): ?>
    <div class="bg-red-50 border border-red-100 rounded-2xl p-6 md:p-8">
        <h3 class="text-lg font-bold text-red-700 mb-2">Danger Zone</h3>
        <p class="text-sm text-red-600 mb-4">Deleting a company will hide all its assets and invalidate all associated CDN links. This action can be undone by a Super Admin.</p>
        <form action="/admin/companies/<?= $company['id'] ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this company?');">
            <?= csrf_field() ?>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition shadow-sm">
                Delete Company
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

    <?php \App\Core\View::component('social-link-script'); ?>
    <script>
        // Load existing links
        const existingLinks = <?= json_encode($company['social_links_array'] ?? []) ?>;
        if (existingLinks && existingLinks.length > 0) {
            existingLinks.forEach(link => {
                addSocialLink(link.platform, link.url, link.icon || '');
            });
        }
    </script>
