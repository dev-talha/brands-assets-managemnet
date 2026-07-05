// Global Utilities
const appUrl = (window.APP_URL || '').replace(/\/$/, '');
const u = (path) => path.startsWith('/') ? appUrl + path : path;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }
}

function toggleUserMenu() {
    const menu = document.getElementById('user-dropdown');
    if (menu) menu.classList.toggle('hidden');
}

// Close dropdowns on outside click
document.addEventListener('click', (e) => {
    const userMenu = document.getElementById('user-dropdown');
    const userBtn = document.getElementById('user-menu');
    if (userMenu && userBtn && !userBtn.contains(e.target) && !userMenu.classList.contains('hidden')) {
        userMenu.classList.add('hidden');
    }
});

function openUploadModal() {
    const modal = document.getElementById('upload-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeUploadModal() {
    const modal = document.getElementById('upload-modal');
    if (modal) modal.classList.add('hidden');
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const template = document.getElementById('toast-template');
    if (!container || !template) return;

    const el = template.content.cloneNode(true).firstElementChild;
    el.querySelector('.toast-message').textContent = message;
    
    if (type === 'success') el.querySelector('.toast-icon-success').classList.remove('hidden');
    else if (type === 'error') el.querySelector('.toast-icon-error').classList.remove('hidden');
    else el.querySelector('.toast-icon-info').classList.remove('hidden');

    container.appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        el.style.transition = 'all 0.3s ease';
        setTimeout(() => el.remove(), 300);
    }, 3000);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (text.includes('/cdn/')) {
            showToast('Copied! Note: CDN links are for testing purposes only, not for real projects. Max expiration is 15 days.', 'info');
        } else {
            showToast('Copied to clipboard!');
        }
    }).catch(() => {
        showToast('Failed to copy', 'error');
    });
}

function copyInput(id) {
    const input = document.getElementById(id);
    if (input) {
        input.select();
        document.execCommand('copy');
        if (input.value.includes('/cdn/')) {
            showToast('Copied! Note: CDN links are for testing purposes only, not for real projects. Max expiration is 15 days.', 'info');
        } else {
            showToast('Copied to clipboard!');
        }
    }
}

// Asset Drawer Logic
let currentAssetGroup = null;

async function openAssetDrawer(groupId) {
    try {
        const res = await fetch(u(`/api/assets/${groupId}/detail`));
        if (!res.ok) {
            const errText = await res.text();
            throw new Error(`Failed to load asset details: ${res.status} ${errText}`);
        }
        const data = await res.json();
        
        currentAssetGroup = data.group;
        const company = data.company;
        const primaryFile = currentAssetGroup.primary_file;

        if (!primaryFile) {
            showToast('No files in this asset group', 'error');
            return;
        }

        // Populate drawer
        document.getElementById('drawer-title').textContent = currentAssetGroup.title;
        document.getElementById('drawer-type').textContent = currentAssetGroup.asset_type.toUpperCase() + ' • ' + currentAssetGroup.theme;
        
        const previewUrl = u(`/cdn/file/${primaryFile.public_token}.${primaryFile.extension}`);
        document.getElementById('drawer-preview-img').src = previewUrl;
        
        document.getElementById('drawer-download-all').href = u(`/download/asset/${currentAssetGroup.id}`);
        
        updateCdnBox(primaryFile);
        
        // Populate formats list
        const formatsList = document.getElementById('drawer-formats-list');
        formatsList.innerHTML = '';
        
        currentAssetGroup.files.forEach(f => {
            const isPrimary = f.id === primaryFile.id;
            const item = document.createElement('div');
            item.className = `flex items-center justify-between p-3 rounded-xl border ${isPrimary ? 'border-indigo-200 bg-indigo-50' : 'border-gray-100 hover:border-gray-200 cursor-pointer'}`;
            if (!isPrimary) {
                item.onclick = () => {
                    document.getElementById('drawer-preview-img').src = u(`/cdn/file/${f.public_token}.${f.extension}`);
                    updateCdnBox(f);
                    // Reset styling
                    Array.from(formatsList.children).forEach(c => {
                        c.className = 'flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-gray-200 cursor-pointer';
                    });
                    item.className = 'flex items-center justify-between p-3 rounded-xl border border-indigo-200 bg-indigo-50';
                };
            }

            item.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 bg-white border border-gray-200 rounded text-[10px] font-bold uppercase text-gray-600">${f.extension}</span>
                    <div>
                        <div class="text-sm font-medium text-gray-800 flex items-center gap-2">
                            ${f.width && f.height ? `${f.width}x${f.height}` : 'Vector'}
                            ${isPrimary ? '<span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">Primary</span>' : ''}
                        </div>
                        <div class="text-xs text-gray-400">${f.formatted_size}</div>
                    </div>
                </div>
                <div class="flex gap-1">
                    ${window.ENABLE_ONLINE_EDITOR ? `
                    <button type="button" onclick="event.stopPropagation(); editInPea(u('/cdn/file/${f.public_token}.${f.extension}'), '${f.extension}')" class="p-1.5 text-gray-400 hover:text-brand-600 bg-white rounded hover:bg-brand-50 border border-transparent shadow-sm" title="Edit Online">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </button>
                    ` : ''}
                    <a href="${u(`/download/file/${f.id}`)}" onclick="event.stopPropagation()" class="p-1.5 text-gray-400 hover:text-gray-700 bg-white rounded hover:bg-gray-100 border border-transparent shadow-sm" title="Download">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </div>
            `;
            formatsList.appendChild(item);
        });

        // Show drawer
        document.getElementById('asset-drawer').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

    } catch (err) {
        console.error(err);
        showToast('Error loading asset', 'error');
    }
}

function updateCdnBox(file) {
    document.getElementById('drawer-cdn-url').value = file.cdn_urls.latest;
    document.getElementById('drawer-embed-code').value = file.embed_code;
}

function closeAssetDrawer() {
    document.getElementById('asset-drawer').classList.add('hidden');
    document.body.style.overflow = '';
}

function copyDrawerCdn() {
    copyInput('drawer-cdn-url');
}

function setPreviewBg(type) {
    const area = document.getElementById('drawer-preview-area');
    area.className = 'flex-1 flex items-center justify-center p-8 min-h-[300px] transition-colors duration-300';
    if (type === 'light') area.classList.add('bg-white');
    else if (type === 'dark') area.classList.add('bg-gray-900');
    else if (type === 'checker') area.classList.add('checkerboard');
}

// Editor integration
function editInPea(fileUrl, ext) {
    // Ensure absolute URL
    if (fileUrl.startsWith('/')) {
        fileUrl = window.location.origin + fileUrl;
    }
    
    const config = { files: [fileUrl] };
    const hash = encodeURIComponent(JSON.stringify(config));
    const isVector = ['svg', 'ai', 'pdf'].includes(ext.toLowerCase());
    const domain = isVector ? 'https://www.vectorpea.com' : 'https://www.photopea.com';
    
    window.open(domain + '#' + hash, '_blank');
}

// Resize feature logic
let resizeOriginalImage = null;
let resizeOriginalAspectRatio = 1;
let currentResizeUrl = '';
let currentResizeFilename = 'resized-image';

function openResizeDrawer(url, filename) {
    currentResizeUrl = url;
    currentResizeFilename = filename || 'resized-image';
    
    document.getElementById('resize-drawer').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    const img = new Image();
    img.crossOrigin = "Anonymous";
    img.onload = () => {
        resizeOriginalImage = img;
        resizeOriginalAspectRatio = img.width / img.height;
        
        document.getElementById('resize-width').value = img.width;
        document.getElementById('resize-height').value = img.height;
        
        const formatSelect = document.getElementById('resize-format');
        if (url.toLowerCase().endsWith('.jpg') || url.toLowerCase().endsWith('.jpeg')) {
            formatSelect.value = 'image/jpeg';
        } else if (url.toLowerCase().endsWith('.webp')) {
            formatSelect.value = 'image/webp';
        } else {
            formatSelect.value = 'image/png';
        }
        
        handleFormatChange();
        updateResizeCanvas();
    };
    img.src = url;
}

function closeResizeDrawer() {
    document.getElementById('resize-drawer').classList.add('hidden');
    document.body.style.overflow = '';
}

function handleResizeInput(source) {
    const widthInput = document.getElementById('resize-width');
    const heightInput = document.getElementById('resize-height');
    const lockRatio = document.getElementById('resize-lock-ratio').checked;
    
    if (lockRatio) {
        if (source === 'width') {
            const w = parseInt(widthInput.value) || 0;
            heightInput.value = Math.round(w / resizeOriginalAspectRatio);
        } else if (source === 'height') {
            const h = parseInt(heightInput.value) || 0;
            widthInput.value = Math.round(h * resizeOriginalAspectRatio);
        }
    }
    
    updateResizeCanvas();
}

function handleLockRatioChange() {
    handleResizeInput('width');
}

function handleFormatChange() {
    const format = document.getElementById('resize-format').value;
    const optimizeContainer = document.getElementById('optimize-container');
    if (optimizeContainer) {
        if (format === 'image/png') {
            optimizeContainer.classList.add('hidden');
        } else {
            optimizeContainer.classList.remove('hidden');
        }
    }
}

function updateResizeCanvas() {
    if (!resizeOriginalImage) return;
    
    const canvas = document.getElementById('resize-canvas');
    const ctx = canvas.getContext('2d');
    
    const width = parseInt(document.getElementById('resize-width').value) || resizeOriginalImage.width;
    const height = parseInt(document.getElementById('resize-height').value) || resizeOriginalImage.height;
    
    canvas.width = width;
    canvas.height = height;
    
    ctx.clearRect(0, 0, width, height);
    ctx.drawImage(resizeOriginalImage, 0, 0, width, height);
}

function downloadResizedImage() {
    const canvas = document.getElementById('resize-canvas');
    const format = document.getElementById('resize-format').value;
    const ext = format === 'image/jpeg' ? 'jpg' : format.split('/')[1];
    
    let quality = 1.0;
    const optimizeCheckbox = document.getElementById('resize-optimize');
    if (optimizeCheckbox && optimizeCheckbox.checked) {
        const qualitySlider = document.getElementById('resize-quality');
        if (qualitySlider) {
            quality = parseInt(qualitySlider.value) / 100.0;
        }
    }
    
    const dataUrl = canvas.toDataURL(format, quality);
    
    const a = document.createElement('a');
    a.href = dataUrl;
    a.download = `${currentResizeFilename}-resized.${ext}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function setResizePreviewBg(type) {
    const area = document.getElementById('resize-preview-area');
    area.className = 'flex-1 flex items-center justify-center p-8 min-h-[300px] transition-colors duration-300';
    if (type === 'light') area.classList.add('bg-white');
    else if (type === 'dark') area.classList.add('bg-gray-900');
    else if (type === 'checker') area.classList.add('checkerboard');
}
