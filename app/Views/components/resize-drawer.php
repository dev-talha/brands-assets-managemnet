<!-- Resize Drawer -->
<div id="resize-drawer" class="fixed inset-0 z-50 hidden">
    <div class="drawer-backdrop absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeResizeDrawer()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-4xl bg-white shadow-2xl flex flex-col lg:flex-row overflow-auto">
        <!-- Close button -->
        <button onclick="closeResizeDrawer()" class="absolute top-4 right-4 z-10 p-2 rounded-lg bg-gray-100/80 backdrop-blur hover:bg-gray-200 transition text-gray-500 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Left: Preview area -->
        <div class="flex-1 flex flex-col border-r border-gray-100">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white">
                <h3 class="font-semibold text-gray-800">Preview</h3>
                <div class="flex items-center gap-1">
                    <button onclick="setResizePreviewBg('light')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 hover:bg-gray-50 transition">Light</button>
                    <button onclick="setResizePreviewBg('dark')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition">Dark</button>
                    <button onclick="setResizePreviewBg('checker')" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition checkerboard">Trans</button>
                </div>
            </div>
            <div id="resize-preview-area" class="flex-1 flex items-center justify-center p-8 bg-gray-50 min-h-[300px] overflow-hidden checkerboard">
                <canvas id="resize-canvas" class="max-w-full max-h-[70vh] object-contain shadow-md border border-gray-200/50 bg-white/50 backdrop-blur"></canvas>
            </div>
        </div>

        <!-- Right: Controls panel -->
        <div class="w-full lg:w-80 bg-white flex flex-col">
            <div class="p-6 flex-1">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Resize Image</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Width (px)</label>
                        <input type="number" id="resize-width" min="10" max="10000" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-gray-800 font-medium bg-gray-50 focus:bg-white transition-colors" oninput="handleResizeInput('width')">
                    </div>
                    
                    <div class="flex items-center justify-center py-1">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors w-full justify-center">
                            <input type="checkbox" id="resize-lock-ratio" checked class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4" onchange="handleLockRatioChange()">
                            <span class="font-medium">Lock aspect ratio</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Height (px)</label>
                        <input type="number" id="resize-height" min="10" max="10000" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-gray-800 font-medium bg-gray-50 focus:bg-white transition-colors" oninput="handleResizeInput('height')">
                    </div>
                    
                    <hr class="border-gray-100">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Output Format</label>
                        <select id="resize-format" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-gray-800 font-medium bg-gray-50 focus:bg-white transition-colors" onchange="handleFormatChange()">
                            <option value="image/png">PNG (Transparent)</option>
                            <option value="image/jpeg">JPG</option>
                            <option value="image/webp">WEBP</option>
                        </select>
                    </div>

                    <div id="optimize-container" class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-sm">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" id="resize-optimize" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4" onchange="document.getElementById('optimize-controls').classList.toggle('hidden', !this.checked)">
                            <span class="font-medium">Optimize Image Size</span>
                        </label>
                        <div id="optimize-controls" class="hidden mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500 font-medium">Quality</span>
                                <span id="quality-label" class="text-xs font-bold text-gray-700">80%</span>
                            </div>
                            <input type="range" id="resize-quality" min="10" max="100" value="80" class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-600" oninput="document.getElementById('quality-label').textContent = this.value + '%'">
                            <p class="text-[10px] text-gray-400 mt-2 leading-tight">Lower quality reduces file size but may degrade visual clarity. Not applicable to PNG format.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-100 bg-gray-50 mt-auto">
                <button onclick="downloadResizedImage()" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all hover:shadow-md hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Resized
                </button>
            </div>
        </div>
    </div>
</div>
