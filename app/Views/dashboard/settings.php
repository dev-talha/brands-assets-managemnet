<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure global application settings</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form action="/admin/settings" method="POST">
            <?= csrf_field() ?>
            
            <div class="p-6 md:p-8 space-y-8">
                <!-- General Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        General Configuration
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                            <input type="text" name="app_name" value="<?= e($settings['app_name'] ?? 'Brand CDN Manager') ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CDN Base URL</label>
                            <input type="url" name="cdn_base_url" value="<?= e($settings['cdn_base_url'] ?? env('APP_URL')) ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <p class="text-xs text-gray-500 mt-1">Leave empty to use APP_URL</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Upload Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload & Storage
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Upload Size (MB)</label>
                            <input type="number" name="max_upload_mb" value="<?= e($settings['max_upload_mb'] ?? '50') ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Security Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Security & Login
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Login Attempts</label>
                            <input type="number" name="login_max_attempts" value="<?= e($settings['login_max_attempts'] ?? '3') ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <p class="text-xs text-gray-500 mt-1">Number of failed attempts before IP is blocked</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lockout Time (Minutes)</label>
                            <input type="number" name="login_lockout_minutes" value="<?= e($settings['login_lockout_minutes'] ?? '30') ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <p class="text-xs text-gray-500 mt-1">Duration of the block after max attempts</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Feature Toggles -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Features
                    </h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-start gap-3 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="hidden" name="public_brand_pages" value="0">
                            <input type="checkbox" name="public_brand_pages" value="1" <?= ($settings['public_brand_pages'] ?? '1') == '1' ? 'checked' : '' ?> 
                                class="mt-1 w-4 h-4 text-brand-600 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="block text-sm font-medium text-gray-900">Public Brand Pages & CDN Links</span>
                                <span class="block text-xs text-gray-500">Allow guest access to public company profiles and enable CDN URLs. Disabling this turns off all public pages and CDN access.</span>
                            </div>
                        </label>
                        
                        <label class="flex items-start gap-3 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="hidden" name="enable_online_editor" value="0">
                            <input type="checkbox" name="enable_online_editor" value="1" <?= ($settings['enable_online_editor'] ?? '1') == '1' ? 'checked' : '' ?> 
                                class="mt-1 w-4 h-4 text-brand-600 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="block text-sm font-medium text-gray-900">Enable Online Editor (Vectorpea / Photopea)</span>
                                <span class="block text-xs text-gray-500">Show "Edit Online" buttons on assets to allow quick edits via Vectorpea (for SVG, AI, PDF) or Photopea (for PNG, JPG).</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="hidden" name="cdn_logging_enabled" value="0">
                            <input type="checkbox" name="cdn_logging_enabled" value="1" <?= ($settings['cdn_logging_enabled'] ?? '1') == '1' ? 'checked' : '' ?> 
                                class="mt-1 w-4 h-4 text-brand-600 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="block text-sm font-medium text-gray-900">CDN Access Logging</span>
                                <span class="block text-xs text-gray-500">Log every hit to a CDN URL for analytics (may increase database size).</span>
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CDN Access Limit (Hits)</label>
                            <input type="number" name="cdn_access_limit" value="<?= e($settings['cdn_access_limit'] ?? '0') ?>" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <p class="text-xs text-gray-500 mt-1">Maximum number of hits allowed per CDN file. 0 for unlimited.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CDN Expiration (Days)</label>
                            <input type="number" name="cdn_expiration_days" value="<?= e($settings['cdn_expiration_days'] ?? '0') ?>" max="15" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <p class="text-xs text-gray-500 mt-1">CDN links will expire this many days after file update. Max 15 days. 0 for never.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

</div>
