<div id="icon-picker-modal" class="fixed inset-0 z-[100] hidden">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeIconPicker()"></div>
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[60vh]">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Select Icon</h3>
            <button type="button" onclick="closeIconPicker()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        <div class="p-4 border-b border-gray-100">
            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 flex items-center justify-center"></i>
                <input type="text" id="icon-search" onkeyup="filterIcons()" placeholder="Search icons (e.g. facebook, tiktok, link)..." 
                    class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
            </div>
        </div>
        <div class="p-4 overflow-y-auto grid grid-cols-4 sm:grid-cols-6 gap-3" id="icon-grid">
            <!-- Icons will be injected here -->
        </div>
    </div>
</div>

<script>
    const platforms = ['Facebook', 'Instagram', 'LinkedIn', 'WhatsApp', 'X (Twitter)', 'YouTube', 'Custom Link'];
    
    // Default mapping for platforms
    const defaultPlatformIcons = {
        'Facebook': 'ri-facebook-line',
        'Instagram': 'ri-instagram-line',
        'LinkedIn': 'ri-linkedin-line',
        'WhatsApp': 'ri-whatsapp-line',
        'X (Twitter)': 'ri-twitter-x-line',
        'YouTube': 'ri-youtube-line',
        'Custom Link': 'ri-link'
    };

    const popularIcons = [
        // Comprehensive Brands List (300+ icons)
        'ri-alipay-fill', 'ri-alipay-line', 'ri-amazon-fill', 'ri-amazon-line', 'ri-android-fill', 'ri-android-line',
        'ri-angularjs-fill', 'ri-angularjs-line', 'ri-app-store-fill', 'ri-app-store-line', 'ri-apple-fill', 'ri-apple-line',
        'ri-baidu-fill', 'ri-baidu-line', 'ri-behance-fill', 'ri-behance-line', 'ri-bilibili-fill', 'ri-bilibili-line',
        'ri-blender-fill', 'ri-blender-line', 'ri-blogger-fill', 'ri-blogger-line', 'ri-bluesky-fill', 'ri-bluesky-line',
        'ri-bootstrap-fill', 'ri-bootstrap-line', 'ri-centos-fill', 'ri-centos-line', 'ri-chrome-fill', 'ri-chrome-line',
        'ri-codepen-fill', 'ri-codepen-line', 'ri-copilot-fill', 'ri-copilot-line', 'ri-coreos-fill', 'ri-coreos-line',
        'ri-dingding-fill', 'ri-dingding-line', 'ri-discord-fill', 'ri-discord-line', 'ri-disqus-fill', 'ri-disqus-line',
        'ri-douban-fill', 'ri-douban-line', 'ri-dribbble-fill', 'ri-dribbble-line', 'ri-drive-fill', 'ri-drive-line',
        'ri-dropbox-fill', 'ri-dropbox-line', 'ri-edge-fill', 'ri-edge-line', 'ri-evernote-fill', 'ri-evernote-line',
        'ri-facebook-box-fill', 'ri-facebook-box-line', 'ri-facebook-circle-fill', 'ri-facebook-circle-line',
        'ri-facebook-fill', 'ri-facebook-line', 'ri-finder-fill', 'ri-finder-line', 'ri-firefox-fill', 'ri-firefox-line',
        'ri-flutter-fill', 'ri-flutter-line', 'ri-gatsby-fill', 'ri-gatsby-line', 'ri-github-fill', 'ri-github-line',
        'ri-gitlab-fill', 'ri-gitlab-line', 'ri-google-fill', 'ri-google-line', 'ri-google-play-fill', 'ri-google-play-line',
        'ri-hammer-fill', 'ri-hammer-line', 'ri-honor-of-kings-fill', 'ri-honor-of-kings-line', 'ri-ie-fill', 'ri-ie-line',
        'ri-instagram-fill', 'ri-instagram-line', 'ri-invision-fill', 'ri-invision-line', 'ri-kakao-talk-fill', 'ri-kakao-talk-line',
        'ri-kick-fill', 'ri-kick-line', 'ri-line-fill', 'ri-line-line', 'ri-linkedin-box-fill', 'ri-linkedin-box-line',
        'ri-linkedin-fill', 'ri-linkedin-line', 'ri-mastercard-fill', 'ri-mastercard-line', 'ri-mastodon-fill', 'ri-mastodon-line',
        'ri-medium-fill', 'ri-medium-line', 'ri-messenger-fill', 'ri-messenger-line', 'ri-meta-fill', 'ri-meta-line',
        'ri-microsoft-fill', 'ri-microsoft-line', 'ri-mini-program-fill', 'ri-mini-program-line', 'ri-netease-cloud-music-fill', 'ri-netease-cloud-music-line',
        'ri-netflix-fill', 'ri-netflix-line', 'ri-notion-fill', 'ri-notion-line', 'ri-npmjs-fill', 'ri-npmjs-line',
        'ri-open-source-fill', 'ri-open-source-line', 'ri-opera-fill', 'ri-opera-line', 'ri-patreon-fill', 'ri-patreon-line',
        'ri-paypal-fill', 'ri-paypal-line', 'ri-pinterest-fill', 'ri-pinterest-line', 'ri-pixelfed-fill', 'ri-pixelfed-line',
        'ri-playstation-fill', 'ri-playstation-line', 'ri-product-hunt-fill', 'ri-product-hunt-line', 'ri-qq-fill', 'ri-qq-line',
        'ri-reactjs-fill', 'ri-reactjs-line', 'ri-reddit-fill', 'ri-reddit-line', 'ri-remixicon-fill', 'ri-remixicon-line',
        'ri-safari-fill', 'ri-safari-line', 'ri-skype-fill', 'ri-skype-line', 'ri-slack-fill', 'ri-slack-line',
        'ri-snapchat-fill', 'ri-snapchat-line', 'ri-soundcloud-fill', 'ri-soundcloud-line', 'ri-spectrum-fill', 'ri-spectrum-line',
        'ri-spotify-fill', 'ri-spotify-line', 'ri-stack-overflow-fill', 'ri-stack-overflow-line', 'ri-steam-fill', 'ri-steam-line',
        'ri-supabase-fill', 'ri-supabase-line', 'ri-svelte-fill', 'ri-svelte-line', 'ri-switch-fill', 'ri-switch-line',
        'ri-taobao-fill', 'ri-taobao-line', 'ri-telegram-fill', 'ri-telegram-line',
        'ri-threads-fill', 'ri-threads-line', 'ri-tiktok-fill', 'ri-tiktok-line', 'ri-trello-fill', 'ri-trello-line',
        'ri-tumblr-fill', 'ri-tumblr-line', 'ri-twitch-fill', 'ri-twitch-line', 'ri-twitter-fill', 'ri-twitter-line',
        'ri-twitter-x-fill', 'ri-twitter-x-line', 'ri-ubuntu-fill', 'ri-ubuntu-line', 'ri-unsplash-fill', 'ri-unsplash-line',
        'ri-vimeo-fill', 'ri-vimeo-line', 'ri-visa-fill', 'ri-visa-line', 'ri-vuejs-fill', 'ri-vuejs-line',
        'ri-wechat-2-fill', 'ri-wechat-2-line', 'ri-wechat-channels-fill', 'ri-wechat-channels-line', 'ri-wechat-fill', 'ri-wechat-line',
        'ri-wechat-pay-fill', 'ri-wechat-pay-line', 'ri-weibo-fill', 'ri-weibo-line', 'ri-whatsapp-fill', 'ri-whatsapp-line',
        'ri-windows-fill', 'ri-windows-line', 'ri-wordpress-fill', 'ri-wordpress-line', 'ri-xbox-fill', 'ri-xbox-line',
        'ri-xing-fill', 'ri-xing-line', 'ri-youtube-fill', 'ri-youtube-line', 'ri-zcool-fill', 'ri-zcool-line',
        'ri-zhihu-fill', 'ri-zhihu-line',

        // General/UI Icons
        'ri-link', 'ri-global-line', 'ri-global-fill', 'ri-mail-line', 'ri-mail-fill', 'ri-phone-line', 'ri-phone-fill',
        'ri-map-pin-line', 'ri-map-pin-fill', 'ri-shopping-bag-line', 'ri-shopping-bag-fill', 'ri-shopping-cart-line',
        'ri-chat-1-line', 'ri-music-2-line', 'ri-video-line', 'ri-camera-line', 'ri-image-line',
        'ri-briefcase-line', 'ri-book-line', 'ri-cup-line', 'ri-gift-line',
        'ri-heart-line', 'ri-star-line', 'ri-emotion-line', 'ri-thumb-up-line',
        'ri-group-line', 'ri-user-line', 'ri-flashlight-line', 'ri-line-chart-line', 'ri-cloud-line',
        'ri-download-line', 'ri-share-forward-box-line', 'ri-hashtag',
        'ri-rss-line', 'ri-share-line', 'ri-smartphone-line', 'ri-computer-line',
        'ri-tv-2-line', 'ri-headphone-line', 'ri-mic-line', 'ri-film-line'
    ];
    let socialLinkIndex = 0;
    let activeIconIndex = null;
    
    function addSocialLink(platform = '', url = '', icon = '') {
        const container = document.getElementById('social-links-container');
        const index = socialLinkIndex++;
        
        const row = document.createElement('div');
        row.className = 'flex items-start gap-3';
        row.id = `social-link-${index}`;
        
        let platformOptions = '<option value="">Select Platform</option>';
        platforms.forEach(p => {
            const selected = (p === platform) ? 'selected' : '';
            platformOptions += `<option value="${p}" ${selected}>${p}</option>`;
        });

        const displayIcon = icon || defaultPlatformIcons[platform] || 'ri-link';

        row.innerHTML = `
            <div class="w-1/3">
                <select name="social_platforms[]" required onchange="handlePlatformChange(this, ${index})"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    ${platformOptions}
                </select>
            </div>
            <div class="flex-1 flex gap-2">
                <button type="button" id="icon-btn-${index}" onclick="openIconPicker(${index})" class="flex shrink-0 items-center justify-center w-10 h-10 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 hover:text-brand-600 hover:border-brand-300 hover:bg-brand-50 transition text-lg" data-tooltip="Select Icon">
                    <i class="${displayIcon}"></i>
                </button>
                <input type="hidden" name="social_icons[]" id="icon-input-${index}" value="${displayIcon}">
                <input type="url" name="social_urls[]" placeholder="https://" value="${url}" required
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
            </div>
            <button type="button" onclick="removeSocialLink(${index})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition mt-0.5" data-tooltip="Remove Link">
                <i class="ri-delete-bin-line text-xl"></i>
            </button>
        `;
        
        container.appendChild(row);
    }

    function handlePlatformChange(selectElement, index) {
        const platform = selectElement.value;
        const iconBtn = document.getElementById(`icon-btn-${index}`);
        const iconInput = document.getElementById(`icon-input-${index}`);
        
        // Auto-update icon based on selected platform (if one exists)
        if (iconBtn && platform) {
            const newIcon = defaultPlatformIcons[platform] || 'ri-link';
            iconInput.value = newIcon;
            iconBtn.innerHTML = `<i class="${newIcon}"></i>`;
        }
    }

    function removeSocialLink(index) {
        const row = document.getElementById(`social-link-${index}`);
        if (row) row.remove();
    }

    // Icon Picker Logic
    function openIconPicker(index) {
        activeIconIndex = index;
        document.getElementById('icon-picker-modal').classList.remove('hidden');
        renderIcons(popularIcons);
        document.getElementById('icon-search').value = '';
        setTimeout(() => document.getElementById('icon-search').focus(), 100);
    }

    function closeIconPicker() {
        document.getElementById('icon-picker-modal').classList.add('hidden');
        activeIconIndex = null;
    }

    function renderIcons(iconsList) {
        const grid = document.getElementById('icon-grid');
        grid.innerHTML = iconsList.map(icon => `
            <button type="button" onclick="selectIcon('${icon}')" class="flex flex-col items-center justify-center p-3 gap-2 rounded-xl border border-gray-100 hover:border-brand-300 hover:bg-brand-50 text-gray-600 hover:text-brand-600 transition group" data-tooltip="${icon.replace('ri-', '')}">
                <i class="${icon} text-2xl group-hover:scale-110 transition-transform"></i>
            </button>
        `).join('');
    }

    function filterIcons() {
        const query = document.getElementById('icon-search').value.toLowerCase();
        const filtered = popularIcons.filter(icon => icon.includes(query));
        renderIcons(filtered);
    }

    function selectIcon(icon) {
        if (activeIconIndex !== null) {
            document.getElementById(`icon-input-${activeIconIndex}`).value = icon;
            const btn = document.getElementById(`icon-btn-${activeIconIndex}`);
            btn.innerHTML = `<i class="${icon}"></i>`;
        }
        closeIconPicker();
    }

    // Escape key to close modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeIconPicker();
    });

</script>
