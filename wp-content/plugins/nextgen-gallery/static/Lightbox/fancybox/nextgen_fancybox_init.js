jQuery(function($) {
    var nextgen_fancybox_init = function() {
        var selector = nextgen_lightbox_filter_selector($, $(".ngg-fancybox"));

        window.addEventListener(
            "click",
            e => {
                let $target = $(e.target);
                var $anchor = $target.is('a') ? $target : $target.parents('a').first();
                
                if (!$anchor.is(selector) && !$target.is(selector)) {
                    return;
                }

                // Skip Dribbble direct links - they should navigate to external URL, not open lightbox.
                if ($anchor.attr('data-dribbble-direct') === 'true') {
                    return;
                }

                var imageId = $anchor.attr('data-image-id');
                var isTikTokImage = false;
                var tiktokData = null;

                if (imageId && window.ngg_tiktok_images && window.ngg_tiktok_images[imageId]) {
                    isTikTokImage = true;
                    tiktokData = window.ngg_tiktok_images[imageId];
                } else if ($anchor.attr('data-tiktok-play-url') || $anchor.attr('data-tiktok-share-url')) {
                    isTikTokImage = true;
                    tiktokData = {
                        playUrl: $anchor.attr('data-tiktok-play-url') || '',
                        shareUrl: $anchor.attr('data-tiktok-share-url') || '',
                        embedUrl: $anchor.attr('data-tiktok-embed-url') || ''
                    };
                }

                if (isTikTokImage && tiktokData) {
                    var $galleryContainer = $anchor.closest('[data-gallery-id]');
                    var galleryId = null;
                    
                    if ($galleryContainer.length) {
                        galleryId = $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
                    } else {
                        $galleryContainer = $anchor.closest('.ngg-galleryoverview, .ngg-imagebrowser, .ngg-slideshow');
                        if ($galleryContainer.length) {
                            galleryId = $galleryContainer.attr('data-gallery-id') || 
                                       $galleryContainer.data('gallery-id') ||
                                       $galleryContainer.attr('data-nextgen-gallery-id') ||
                                       $galleryContainer.data('nextgen-gallery-id');
                        }
                    }
                    
                    if (galleryId) {
                        galleryId = String(galleryId);
                    }

                    var tiktokSettings = {};
                    if (window.NggTikTokVideo && typeof window.NggTikTokVideo.getTikTokSettings === 'function') {
                        tiktokSettings = window.NggTikTokVideo.getTikTokSettings(galleryId);
                    } else {
                        if (window.ngg_tiktok_gallery_settings) {
                            if (galleryId && window.ngg_tiktok_gallery_settings['gallery_' + galleryId]) {
                                tiktokSettings = window.ngg_tiktok_gallery_settings['gallery_' + galleryId];
                            } else if (window.ngg_tiktok_gallery_settings.global) {
                                tiktokSettings = window.ngg_tiktok_gallery_settings.global;
                            }
                        }
                    }
                    
                    var linkSetting = String(tiktokSettings.link || '0');

                    if (linkSetting === '1' || linkSetting === '2') {
                        return;
                    }
                }

                e.preventDefault();
                $(selector).fancybox({
                    titlePosition: 'inside',
                    // Needed for twenty eleven
                    onComplete: function(selectedArray, selectedIndex, selectedOpts) {
                        $("#fancybox-wrap").css("z-index", 10000);

                        var element = selectedArray[selectedIndex];
                        var $element = $(element);
                        var playUrl = $element.data("tiktok-play-url");
                        var shareUrl = $element.data("tiktok-share-url");
                        var videoUrl = $element.attr("data-video-url");

                        // Handle TikTok
                        if (playUrl || shareUrl) {
                            $("#fancybox-wrap").addClass("ngg-tiktok-mode");
                            NextGEN_TikTok.handle_content({
                                playUrl: playUrl,
                                shareUrl: shareUrl,
                                container: $("#fancybox-content"),
                                onBeforeAppend: function () {
                                    $("#fancybox-img").hide();
                                },
                            });
                        }
                        // Handle video links
                        else if (videoUrl && window.NextGEN_Video && window.NextGEN_Video.detect_platform(videoUrl)) {
                            $("#fancybox-wrap").addClass("ngg-video-mode");
                            
                            // Remove existing video containers
                            $("#fancybox-content .ngg-video-container, #fancybox-content .ngg-video-error").remove();
                            
                            // Get gallery ID for settings
                            var galleryId = null;
                            var $galleryContainer = $element.closest('[data-gallery-id]');
                            if ($galleryContainer.length) {
                                galleryId = $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
                            }
                            
                            // Get video settings
                            var videoSettings = {};
                            if (window.ngg_video_gallery_settings) {
                                if (galleryId && window.ngg_video_gallery_settings['gallery_' + galleryId]) {
                                    videoSettings = window.ngg_video_gallery_settings['gallery_' + galleryId];
                                }
                            }
                            
                            window.NextGEN_Video.handle_content({
                                videoUrl: videoUrl,
                                container: $("#fancybox-content")[0],
                                settings: videoSettings,
                                containerClass: "ngg-video-container",
                                videoClass: "ngg-video-player",
                                errorClass: "ngg-video-error",
                                onBeforeAppend: function () {
                                    $("#fancybox-img").hide();
                                },
                            });
                        }
                    },
                    onCleanup: function () {
                        $("#fancybox-wrap").removeClass("ngg-tiktok-mode ngg-video-mode");
                        $("#fancybox-content .ngg-tiktok-container, #fancybox-content .ngg-tiktok-error").remove();
                        $("#fancybox-content .ngg-video-container, #fancybox-content .ngg-video-error").remove();
                    },
                });
                $target.trigger('click.fb');
                
                e.stopPropagation();
            },
            true
        )
    };
    $(window).on('refreshed', nextgen_fancybox_init);
    nextgen_fancybox_init();
});
