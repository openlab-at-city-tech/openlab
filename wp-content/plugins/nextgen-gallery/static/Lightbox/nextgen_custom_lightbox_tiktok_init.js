/**
 * NextGEN Gallery - Custom Lightbox TikTok & Video Support
 * 
 * This script provides TikTok and video playback support for custom lightboxes.
 * It intercepts clicks on TikTok/video images and displays content in a simple overlay
 * that works alongside any custom lightbox library.
 */
(function($) {
    "use strict";

    var DEBUG = false;
    var initialized = false;

    var log = function() {
        if (DEBUG && window.console) {
            console.log.apply(console, ['[NGG Custom Lightbox TikTok]'].concat(Array.prototype.slice.call(arguments)));
        }
    };

    /**
     * Get TikTok data from anchor element
     */
    var getTikTokData = function($anchor) {
        var playUrl = $anchor.attr('data-tiktok-play-url');
        var shareUrl = $anchor.attr('data-tiktok-share-url');
        var embedUrl = $anchor.attr('data-tiktok-embed-url');
        
        if (playUrl || shareUrl || embedUrl) {
            return {
                playUrl: playUrl || '',
                shareUrl: shareUrl || '',
                embedUrl: embedUrl || ''
            };
        }
        
        // Check window.ngg_tiktok_images
        var imageId = $anchor.attr('data-image-id');
        if (imageId && window.ngg_tiktok_images && window.ngg_tiktok_images[imageId]) {
            return window.ngg_tiktok_images[imageId];
        }
        
        return null;
    };

    /**
     * Get video URL from anchor element
     */
    var getVideoUrl = function($anchor) {
        return $anchor.attr('data-video-url') || null;
    };

    /**
     * Get gallery ID from element
     */
    var getGalleryId = function($element) {
        var $galleryContainer = $element.closest('[data-gallery-id]');
        if ($galleryContainer.length) {
            return $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
        }
        
        $galleryContainer = $element.closest('.ngg-galleryoverview, .ngg-imagebrowser, .ngg-slideshow');
        if ($galleryContainer.length) {
            return $galleryContainer.attr('data-gallery-id') || 
                   $galleryContainer.data('gallery-id') ||
                   $galleryContainer.attr('data-nextgen-gallery-id') ||
                   $galleryContainer.data('nextgen-gallery-id');
        }
        return null;
    };

    /**
     * Get TikTok settings for gallery
     */
    var getTikTokSettings = function(galleryId) {
        if (window.NggTikTokVideo && typeof window.NggTikTokVideo.getTikTokSettings === 'function') {
            return window.NggTikTokVideo.getTikTokSettings(galleryId);
        }
        
        if (window.ngg_tiktok_gallery_settings) {
            if (galleryId && window.ngg_tiktok_gallery_settings['gallery_' + galleryId]) {
                return window.ngg_tiktok_gallery_settings['gallery_' + galleryId];
            }
            if (window.ngg_tiktok_gallery_settings.global) {
                return window.ngg_tiktok_gallery_settings.global;
            }
        }
        
        return { link: '0', link_target: '0' };
    };

    /**
     * Get video settings for gallery
     */
    var getVideoSettings = function(galleryId) {
        if (!window.ngg_video_gallery_settings) {
            return {
                show_video_controls: true,
                show_play_pause_controls: true,
                autoplay_videos: false
            };
        }

        var settings = window.ngg_video_gallery_settings;
        var galleryIdStr = galleryId ? String(galleryId) : null;

        if (galleryIdStr && settings['gallery_' + galleryIdStr]) {
            return settings['gallery_' + galleryIdStr];
        }

        return settings.global || {
            show_video_controls: true,
            show_play_pause_controls: true,
            autoplay_videos: false
        };
    };

    /**
     * Create and show the TikTok/Video overlay
     */
    var showOverlay = function(contentElement) {
        // Remove existing overlay
        $('#ngg-custom-lightbox-overlay').remove();
        
        var $overlay = $('<div id="ngg-custom-lightbox-overlay"></div>').css({
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            backgroundColor: 'transparent',
            zIndex: 999999,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            cursor: 'pointer'
        });
        
        var $closeBtn = $('<button type="button" class="ngg-custom-lightbox-close">&times;</button>').css({
            position: 'absolute',
            top: '20px',
            right: '20px',
            background: 'transparent',
            border: 'none',
            color: '#fff',
            fontSize: '40px',
            cursor: 'pointer',
            zIndex: 1000000,
            padding: '10px',
            lineHeight: 1
        });
        
        var $content = $('<div class="ngg-custom-lightbox-content"></div>').css({
            position: 'relative',
            maxWidth: '90vw',
            maxHeight: '90vh',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        });
        
        $content.append(contentElement);
        $overlay.append($closeBtn).append($content);
        
        // Close handlers
        var closeOverlay = function() {
            // Stop video/cleanup before removing
            var $video = $overlay.find('video');
            if ($video.length) {
                $video[0].pause();
                $video.attr('src', '');
            }
            var $iframe = $overlay.find('iframe');
            if ($iframe.length) {
                $iframe.attr('src', '');
            }
            $overlay.remove();
        };
        
        $closeBtn.on('click', function(e) {
            e.stopPropagation();
            closeOverlay();
        });
        
        $overlay.on('click', function(e) {
            if (e.target === this) {
                closeOverlay();
            }
        });
        
        $(document).on('keydown.nggCustomLightbox', function(e) {
            if (e.keyCode === 27) { // ESC
                closeOverlay();
                $(document).off('keydown.nggCustomLightbox');
            }
        });
        
        $('body').append($overlay);
        
        return $overlay;
    };

    /**
     * Handle TikTok content display
     */
    var handleTikTokClick = function($anchor, tiktokData) {
        log('Handling TikTok click', tiktokData);
        
        if (!tiktokData.playUrl && !tiktokData.shareUrl) {
            return false;
        }
        
        // Create container for TikTok content
        var $container = $('<div class="ngg-tiktok-container"></div>').css({
            position: 'relative',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        });
        
        // Use NextGEN_TikTok helper if available
        if (window.NextGEN_TikTok && window.NextGEN_TikTok.handle_content) {
            window.NextGEN_TikTok.handle_content({
                playUrl: tiktokData.playUrl,
                shareUrl: tiktokData.shareUrl,
                container: $container
            });
            showOverlay($container);
            return true;
        }
        
        // Fallback: create video element directly
        if (tiktokData.playUrl) {
            var $video = $('<video></video>').attr({
                src: tiktokData.playUrl,
                controls: true,
                autoplay: true,
                playsinline: true,
                muted: true,
                loop: true
            }).css({
                maxWidth: '90vw',
                maxHeight: '90vh',
                width: 'auto',
                height: 'auto'
            });
            
            $container.append($video);
            showOverlay($container);
            
            // Try to unmute after user interaction
            $video.on('click', function() {
                this.muted = false;
            });
            
            return true;
        }
        
        return false;
    };

    /**
     * Handle video content display
     */
    var handleVideoClick = function($anchor, videoUrl, galleryId) {
        log('Handling video click', videoUrl);
        
        if (!videoUrl) {
            return false;
        }
        
        // Check if NextGEN_Video can handle this platform
        if (!window.NextGEN_Video || !window.NextGEN_Video.detect_platform(videoUrl)) {
            return false;
        }
        
        var videoSettings = getVideoSettings(galleryId);
        
        var $container = $('<div class="ngg-video-container"></div>').css({
            position: 'relative',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        });
        
        if (window.NextGEN_Video.handle_content) {
            window.NextGEN_Video.handle_content({
                videoUrl: videoUrl,
                container: $container[0],
                settings: videoSettings,
                containerClass: 'ngg-video-container',
                videoClass: 'ngg-video-player',
                errorClass: 'ngg-video-error'
            });
            showOverlay($container);
            return true;
        }
        
        return false;
    };

    /**
     * Main click handler
     */
    var handleClick = function(e) {
        var $target = $(e.target);
        var $anchor = $target.is('a') ? $target : $target.closest('a');
        
        if (!$anchor.length) {
            return;
        }
        
        // Check if this is a NextGEN gallery image
        if (!$anchor.attr('data-image-id')) {
            return;
        }
        
        var tiktokData = getTikTokData($anchor);
        var videoUrl = getVideoUrl($anchor);
        var galleryId = getGalleryId($anchor);
        
        // Handle TikTok
        if (tiktokData && (tiktokData.playUrl || tiktokData.shareUrl)) {
            var tiktokSettings = getTikTokSettings(galleryId);
            var linkSetting = String(tiktokSettings.link || '0');
            
            // If link setting is to redirect, don't intercept
            if (linkSetting === '1' || linkSetting === '2') {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            handleTikTokClick($anchor, tiktokData);
            return;
        }
        
        // Handle Video
        if (videoUrl && window.NextGEN_Video && window.NextGEN_Video.detect_platform(videoUrl)) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            handleVideoClick($anchor, videoUrl, galleryId);
            return;
        }
    };

    /**
     * Initialize the handler
     */
    var init = function() {
        if (initialized) {
            return;
        }
        initialized = true;
        
        log('Initializing custom lightbox TikTok/video handler');
        
        // Use capture phase to intercept clicks before other lightbox handlers
        document.addEventListener('click', handleClick, true);
    };

    // Initialize on DOM ready
    $(function() {
        init();
    });

    // Reinitialize on refreshed event (AJAX navigation)
    $(window).on('refreshed', function() {
        log('Refreshed event - reinitializing');
    });

    // Expose API
    window.NggCustomLightboxTikTok = {
        showOverlay: showOverlay,
        handleTikTokClick: handleTikTokClick,
        handleVideoClick: handleVideoClick
    };

})(jQuery);

