<div class="max-w-3xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/users" class="p-2 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                <p class="text-sm text-gray-500 mt-1">Update user account details and permissions</p>
            </div>
        </div>
    </div>

    <form action="/admin/users/<?= $editUser['id'] ?>/update" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <?= csrf_field() ?>
        
        <div class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= e($editUser['name']) ?>" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?= e($editUser['email']) ?>" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="viewer" <?= $editUser['role'] === 'viewer' ? 'selected' : '' ?>>Viewer (Read Only)</option>
                        <option value="brand_manager" <?= $editUser['role'] === 'brand_manager' ? 'selected' : '' ?>>Brand Manager (Can manage assets)</option>
                        <option value="super_admin" <?= $editUser['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin (Full Access)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="active" <?= $editUser['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $editUser['status'] === 'inactive' ? 'selected' : '' ?>>Suspended (Inactive)</option>
                    </select>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-4">Security</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reset Password</label>
                    <input type="password" name="password" class="w-full max-w-md px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Leave blank to keep current password">
                    <p class="text-xs text-gray-500 mt-1">If you enter a password here, it will overwrite the user's current password.</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="/admin/users" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
