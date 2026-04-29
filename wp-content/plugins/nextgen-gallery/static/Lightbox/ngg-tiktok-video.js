/**
 * NextGEN Gallery TikTok Video Player
 * @package NextGEN Gallery
 */

(function($, window) {
    'use strict';

    window.NggTikTokVideo = window.NggTikTokVideo || {};

    let linkHandlersInitialized = false;
    // Cache for gallery ID lookups to avoid repeated DOM traversals
    const galleryIdCache = new Map();

    // Constants
    const DEFAULT_DIMENSIONS = { width: 480, height: 854 };
    const ASPECT_RATIO = 9 / 16;
    const MAX_DEPTH = 15;
    const INIT_DELAY = 100;

    /**
     * Get TikTok settings for a gallery
     * @param {string|number|null} galleryId - Gallery ID
     * @returns {Object} Settings object with link and link_target
     */
    NggTikTokVideo.getTikTokSettings = (galleryId) => {
        if (!window.ngg_tiktok_gallery_settings) {
            window.ngg_tiktok_gallery_settings = {
                global: {
                    link: '0',
                    link_target: '0'
                }
            };
        }

        const settings = window.ngg_tiktok_gallery_settings;
        const galleryIdStr = galleryId ? String(galleryId) : null;

        if (galleryIdStr && settings[`gallery_${galleryIdStr}`]) {
            const gallerySettings = settings[`gallery_${galleryIdStr}`];
            if (gallerySettings && (gallerySettings.link !== undefined || gallerySettings.link_target !== undefined)) {
                return {
                    link: String(gallerySettings.link ?? '0'),
                    link_target: String(gallerySettings.link_target ?? '0')
                };
            }
        }

        const globalSettings = settings.global || {};
        return {
            link: String(globalSettings.link || '0'),
            link_target: String(globalSettings.link_target || '0')
        };
    };

    /**
     * Find gallery ID from anchor element
     * @param {jQuery} $anchor - jQuery anchor element
     * @param {string|null} imageId - Image ID if available
     * @returns {Object|null} Object with galleryId and $galleryContainer
     */
    const findGalleryId = ($anchor, imageId) => {
        const cacheKey = imageId || $anchor[0];
        const cachedResult = galleryIdCache.get(cacheKey);
        
        if (cachedResult) {
            return cachedResult;
        }

        let $galleryContainer = null;
        let galleryId = null;

        // Try closest data-gallery-id first (most common case)
        $galleryContainer = $anchor.closest('[data-gallery-id]');
        if ($galleryContainer.length) {
            galleryId = $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
        }

        // Try common gallery container classes
        if (!galleryId) {
            $galleryContainer = $anchor.closest('.ngg-galleryoverview, .ngg-imagebrowser, .ngg-slideshow');
            if ($galleryContainer.length) {
                galleryId = $galleryContainer.attr('data-gallery-id') ||
                           $galleryContainer.data('gallery-id') ||
                           $galleryContainer.attr('data-nextgen-gallery-id') ||
                           $galleryContainer.data('nextgen-gallery-id');
            }
        }

        // Traverse up the DOM tree
        if (!galleryId) {
            let $current = $anchor;
            let depth = 0;
            
            while (depth < MAX_DEPTH && $current.length) {
                if ($current.is('[data-gallery-id]')) {
                    galleryId = $current.attr('data-gallery-id') || $current.data('gallery-id');
                    $galleryContainer = $current;
                    break;
                }
                
                const $sibling = $current.siblings('[data-gallery-id]').first();
                if ($sibling.length) {
                    galleryId = $sibling.attr('data-gallery-id') || $sibling.data('gallery-id');
                    $galleryContainer = $sibling;
                    break;
                }
                
                $current = $current.parent();
                depth++;
            }
        }

        // Last resort: search all containers
        if (!galleryId && imageId) {
            const $allContainers = $('[data-gallery-id]');
            $allContainers.each(function() {
                const $container = $(this);
                if ($container.find($anchor).length > 0) {
                    galleryId = $container.attr('data-gallery-id') || $container.data('gallery-id');
                    $galleryContainer = $container;
                    return false;
                }
            });
        }

        const result = {
            galleryId: galleryId ? String(galleryId) : null,
            $galleryContainer
        };

        // Cache the result (even if null) to avoid repeated traversals
        galleryIdCache.set(cacheKey, result);
        return result;
    };

    /**
     * Get TikTok data from anchor element
     * @param {jQuery} $anchor - jQuery anchor element
     * @param {string|null} imageId - Image ID if available
     * @returns {Object|null} TikTok data object
     */
    const getTikTokDataFromAnchor = ($anchor, imageId) => {
        if (imageId && window.ngg_tiktok_images?.[imageId]) {
            return window.ngg_tiktok_images[imageId];
        }

        const playUrl = $anchor.attr('data-tiktok-play-url');
        const shareUrl = $anchor.attr('data-tiktok-share-url');
        const embedUrl = $anchor.attr('data-tiktok-embed-url');

        if (playUrl || shareUrl || embedUrl) {
            return {
                playUrl: playUrl || '',
                shareUrl: shareUrl || '',
                embedUrl: embedUrl || ''
            };
        }

        return null;
    };

    /**
     * Handle click events on TikTok image links
     * @param {Event} e - Click event
     */
    const handleTikTokLinkClick = (e) => {
        const anchor = e.target.closest('a[data-image-id]');
        if (!anchor) {
            return;
        }

        const $anchor = $(anchor);
        const imageId = $anchor.attr('data-image-id');
        const tiktokData = getTikTokDataFromAnchor($anchor, imageId);

        if (!tiktokData) {
            return;
        }

        const { galleryId } = findGalleryId($anchor, imageId);
        const tiktokSettings = NggTikTokVideo.getTikTokSettings(galleryId);
        const linkSetting = String(tiktokSettings.link || '0');
        const linkTarget = String(tiktokSettings.link_target || '0');

        if (linkSetting !== '1' && linkSetting !== '2') {
            return;
        }

        if (linkSetting === '1' && !tiktokData.shareUrl) {
            return;
        }
        
        if (linkSetting === '2' && !tiktokData.embedUrl && !tiktokData.shareUrl) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const openNewTab = linkTarget === '1' || linkTarget === '_blank';
        let targetUrl = '';

        if (linkSetting === '1') {
            targetUrl = tiktokData.shareUrl || '';
        } else if (linkSetting === '2') {
            targetUrl = tiktokData.embedUrl || tiktokData.shareUrl || '';
        }

        if (targetUrl) {
            if (openNewTab) {
                window.open(targetUrl, '_blank', 'noopener,noreferrer');
            } else {
                window.location.href = targetUrl;
            }
        }
    };

    /**
     * Initialize link handlers for TikTok images
     */
    NggTikTokVideo.initLinkHandlers = () => {
        if (linkHandlersInitialized) {
            return;
        }
        linkHandlersInitialized = true;

        // Use capture phase to run BEFORE lightbox handlers
        document.addEventListener('click', handleTikTokLinkClick, true);
    };

    /**
     * Check if element is a TikTok image
     * @param {Element|jQuery} element - Element to check
     * @returns {boolean} True if element is a TikTok image
     */
    NggTikTokVideo.isTikTokImage = (element) => {
        const $el = $(element);
        const $anchor = $el.is('a') ? $el : $el.closest('a');

        if ($anchor.attr('data-ngg-tiktok-source') === 'true') {
            return true;
        }

        const imageId = $anchor.attr('data-image-id');
        if (imageId && window.ngg_tiktok_images?.[imageId]) {
            const $galleryContainer = $anchor.closest('[data-gallery-id]');
            const galleryId = $galleryContainer.length ? $galleryContainer.attr('data-gallery-id') : null;
            const settings = NggTikTokVideo.getTikTokSettings(galleryId);
            return String(settings.link || '0') === '0';
        }

        return false;
    };

    /**
     * Get TikTok data from element
     * @param {Element|jQuery} element - Element to get data from
     * @returns {Object|null} TikTok data object
     */
    NggTikTokVideo.getTikTokData = (element) => {
        const $el = $(element);
        const $anchor = $el.is('a') ? $el : $el.closest('a');

        const playUrl = $anchor.attr('data-ngg-tiktok-play-url');
        const shareUrl = $anchor.attr('data-ngg-tiktok-share-url');
        const embedUrl = $anchor.attr('data-ngg-tiktok-embed-url');
        const tiktokId = $anchor.attr('data-ngg-tiktok-id');

        if (playUrl || shareUrl || embedUrl) {
            return {
                tiktokId: tiktokId || '',
                playUrl: playUrl || '',
                shareUrl: shareUrl || '',
                embedUrl: embedUrl || '',
                linkSetting: '0'
            };
        }

        const imageId = $anchor.attr('data-image-id');
        if (imageId && window.ngg_tiktok_images?.[imageId]) {
            return window.ngg_tiktok_images[imageId];
        }

        return null;
    };

    /**
     * Get video URL from TikTok data
     * @param {Object|null} tiktokData - TikTok data object
     * @returns {string} Video URL
     */
    NggTikTokVideo.getVideoUrl = (tiktokData) => {
        if (!tiktokData) {
            return '';
        }

        if (tiktokData.playUrl?.length > 0) {
            return tiktokData.playUrl;
        }

        if (tiktokData.shareUrl?.length > 0) {
            return tiktokData.shareUrl;
        }

        return '';
    };

    /**
     * Get embed URL from TikTok data
     * @param {Object|null} tiktokData - TikTok data object
     * @returns {string} Embed URL
     */
    NggTikTokVideo.getEmbedUrl = (tiktokData) => {
        if (!tiktokData) {
            return '';
        }

        if (tiktokData.embedUrl) {
            return tiktokData.embedUrl;
        }

        if (tiktokData.shareUrl) {
            const match = tiktokData.shareUrl.match(/video\/(\d+)/);
            if (match?.[1]) {
                return `https://www.tiktok.com/embed/v2/${match[1]}`;
            }
        }

        return '';
    };

    /**
     * Create error message element
     * @param {string} message - Error message
     * @returns {jQuery} Error message jQuery element
     */
    const createErrorMessage = (message) => {
        return $('<div class="ngg-tiktok-error"></div>')
            .text(message)
            .css({
                color: '#fff',
                textAlign: 'center',
                padding: '20px'
            });
    };

    /**
     * Create video player element
     * @param {string} videoUrl - Video URL
     * @param {boolean} autoplay - Autoplay setting
     * @param {Function} onReady - Ready callback
     * @param {Function} onError - Error callback
     * @returns {jQuery} Video jQuery element
     */
    const createVideoElement = (videoUrl, autoplay, onReady, onError) => {
        const $video = $('<video></video>').attr({
            src: videoUrl,
            controls: true,
            autoplay: autoplay,
            playsinline: true,
            preload: 'auto'
        }).css({
            width: '100%',
            height: '100%',
            objectFit: 'contain'
        });

        $video.on('loadeddata', () => {
            onReady($video[0]);
        });

        $video.on('error', () => {
            $video.remove();
            onError(new Error('Video failed to load'));
        });

        return $video;
    };

    /**
     * Create player container
     * @param {Object} options - Player options
     * @returns {jQuery} Container jQuery element
     */
    NggTikTokVideo.createPlayer = (options = {}) => {
        const {
            tiktokData,
            width = DEFAULT_DIMENSIONS.width,
            height = DEFAULT_DIMENSIONS.height,
            autoplay = true,
            onReady = () => {},
            onError = () => {}
        } = options;

        const $container = $('<div class="ngg-tiktok-video-container"></div>').css({
            position: 'relative',
            width,
            height,
            maxWidth: '100%',
            maxHeight: '100%',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        });

        const videoUrl = NggTikTokVideo.getVideoUrl(tiktokData);

        if (videoUrl && videoUrl.includes('tiktokcdn.com')) {
            const $video = createVideoElement(videoUrl, autoplay, onReady, (error) => {
                $container.append(createErrorMessage('Video failed to load'));
                onError(error);
            });
            $container.append($video);
        } else {
            $container.append(createErrorMessage('Video not available'));
            onError(new Error('No video URL available'));
        }

        return $container;
    };

    /**
     * Create embed player
     * @param {jQuery} $container - Container element
     * @param {string} embedUrl - Embed URL
     * @param {number} width - Width
     * @param {number} height - Height
     * @param {boolean} autoplay - Autoplay setting
     * @param {Function} onReady - Ready callback
     * @param {Function} onError - Error callback
     */
    NggTikTokVideo._createEmbedPlayer = ($container, embedUrl, width, height, autoplay, onReady, onError) => {
        $container.empty();

        if (!embedUrl) {
            $container.append(createErrorMessage('Video not available'));
            onError(new Error('No embed URL available'));
            return;
        }

        const url = autoplay
            ? `${embedUrl}${embedUrl.includes('?') ? '&' : '?'}autoplay=1`
            : embedUrl;

        const $iframe = $('<iframe></iframe>').attr({
            src: url,
            frameborder: '0',
            allowfullscreen: true,
            allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
        }).css({
            width: '100%',
            height: '100%',
            border: 'none'
        });

        $iframe.on('load', () => {
            onReady($iframe[0]);
        });

        $iframe.on('error', () => {
            onError(new Error('Failed to load TikTok embed'));
        });

        $container.append($iframe);
    };

    /**
     * Replace image with video player
     * @param {Element|jQuery} imageElement - Image element
     * @param {Object} options - Player options
     * @returns {jQuery|null} Player jQuery element or null
     */
    NggTikTokVideo.replaceImageWithVideo = (imageElement, options = {}) => {
        const $image = $(imageElement);
        const $anchor = $image.closest('a');
        const tiktokData = options.tiktokData || NggTikTokVideo.getTikTokData($anchor);

        if (!tiktokData) {
            return null;
        }

        const width = options.width || $image.width() || DEFAULT_DIMENSIONS.width;
        const height = options.height || $image.height() || DEFAULT_DIMENSIONS.height;

        const playerOptions = {
            ...options,
            tiktokData,
            width,
            height
        };

        const $player = NggTikTokVideo.createPlayer(playerOptions);
        $image.replaceWith($player);
        return $player;
    };

    /**
     * Create lightbox player
     * @param {Object} tiktokData - TikTok data object
     * @param {Object} lightboxOptions - Lightbox options
     * @returns {jQuery} Player jQuery element
     */
    NggTikTokVideo.createLightboxPlayer = (tiktokData, lightboxOptions = {}) => {
        const {
            maxWidth = window.innerWidth * 0.8,
            maxHeight = window.innerHeight * 0.9,
            onReady,
            onError
        } = lightboxOptions;

        let width, height;
        if (maxHeight * ASPECT_RATIO <= maxWidth) {
            height = maxHeight;
            width = height * ASPECT_RATIO;
        } else {
            width = maxWidth;
            height = width / ASPECT_RATIO;
        }

        return NggTikTokVideo.createPlayer({
            tiktokData,
            width: Math.round(width),
            height: Math.round(height),
            autoplay: true,
            onReady,
            onError
        });
    };

    /**
     * Destroy player and clean up resources
     * @param {jQuery} $container - Container element
     */
    NggTikTokVideo.destroyPlayer = ($container) => {
        if (!$container?.length) {
            return;
        }

        const $video = $container.find('video');
        if ($video.length) {
            $video[0].pause();
            $video.attr('src', '');
        }

        const $iframe = $container.find('iframe');
        if ($iframe.length) {
            $iframe.attr('src', '');
        }

        $container.remove();
    };

    // Initialize on DOM ready
    $(() => {
        setTimeout(() => {
            NggTikTokVideo.initLinkHandlers();
        }, INIT_DELAY);
    });

})(jQuery, window);
