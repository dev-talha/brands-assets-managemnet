<!-- Asset Preview Drawer -->
<div id="asset-drawer" class="fixed inset-0 z-50 hidden">
    <div class="drawer-backdrop absolute inset-0" onclick="closeAssetDrawer()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-5xl bg-white shadow-2xl flex flex-col lg:flex-row fade-in overflow-auto">
        <!-- Close button -->
        <button onclick="closeAssetDrawer()" class="absolute top-4 right-4 z-10 p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Left: Preview area -->
        <div class="flex-1 flex flex-col">
            <!-- Background toggle -->
            <div class="flex items-center gap-1 p-4 border-b border-gray-100">
                <button onclick="setPreviewBg('light')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 hover:bg-gray-50 transition">Light</button>
                <button onclick="setPreviewBg('dark')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition">Dark</button>
                <button onclick="setPreviewBg('checker')" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition checkerboard">Trans</button>
            </div>
            <!-- Preview image -->
            <div id="drawer-preview-area" class="flex-1 flex items-center justify-center p-8 bg-gray-50 min-h-[300px]">
                <img id="drawer-preview-img" src="" alt="" class="max-w-full max-h-[60vh] object-contain">
            </div>
        </div>

        <!-- Right: Details panel -->
        <div class="w-full lg:w-96 border-l border-gray-100 overflow-y-auto">
            <div class="p-6">
                <h2 id="drawer-title" class="text-xl font-semibold text-gray-800 mb-1"></h2>
                <p id="drawer-type" class="text-sm text-gray-500 mb-4"></p>

                <!-- Action buttons -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <a id="drawer-download-all" href="#" class="flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download All
                    </a>
                    <button id="drawer-copy-cdn" onclick="copyDrawerCdn()" class="flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy CDN
                    </button>
                </div>

                <!-- Formats list -->
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Available Formats</h3>
                <div id="drawer-formats-list" class="space-y-2 mb-6">
                    <!-- Populated by JS -->
                </div>

                <!-- CDN box -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-100">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">ðŸ”— Keep this logo always up-to-date</h4>
                    <p class="text-xs text-gray-500 mb-3">Embed this live link. Any changes to the logo will update automatically wherever the link is used.</p>

                    <div class="space-y-2">
                        <div>
                            <label class="text-[10px] font-semibold uppercase text-gray-400">CDN URL</label>
                            <div class="flex items-center gap-1 mt-1">
                                <input id="drawer-cdn-url" type="text" readonly class="flex-1 text-xs bg-white border border-gray-200 rounded-lg px-3 py-2 text-gray-600 font-mono">
                                <button onclick="copyInput('drawer-cdn-url')" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-semibold uppercase text-gray-400">HTML Embed</label>
                            <div class="flex items-center gap-1 mt-1">
                                <input id="drawer-embed-code" type="text" readonly class="flex-1 text-xs bg-white border border-gray-200 rounded-lg px-3 py-2 text-gray-600 font-mono">
                                <button onclick="copyInput('drawer-embed-code')" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
