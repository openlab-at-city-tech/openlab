if (typeof window.thickboxL10n == 'undefined') {
    if (typeof nextgen_thickbox_i18n == 'undefined') {
        // for backwards compatibility, nextgen_thickbox_i18n may not exist and we should just use the English defaults
        window.thickboxL10n = {
            loadingAnimation: photocrati_ajax.wp_includes_url + '/js/thickbox/loadingAnimation.gif',
            closeImage: photocrati_ajax.wp_includes_url + '/js/thickbox/tb-close.png',
            next: 'Next &gt;',
            prev: '&lt; Prev',
            image: 'Image',
            of: 'of',
            close: 'Close',
            noiframes: 'This feature requires inline frames. You have iframes disabled or your browser does not support them.'
        };
    } else {
        window.thickboxL10n = {
            loadingAnimation: photocrati_ajax.wp_includes_url + '/js/thickbox/loadingAnimation.gif',
            closeImage: photocrati_ajax.wp_includes_url + '/js/thickbox/tb-close.png',
            next: nextgen_thickbox_i18n.next,
            prev: nextgen_thickbox_i18n.prev,
            image: nextgen_thickbox_i18n.image,
            of: nextgen_thickbox_i18n.of,
            close: nextgen_thickbox_i18n.close,
            noiframes: nextgen_thickbox_i18n.noiframes
        };
    }
}

jQuery(function($) {
    var selector = nextgen_lightbox_filter_selector($, $(".thickbox, a[class*='thickbox']"));
    selector.addClass('thickbox');

    // Wrap window.tb_show to handle TikTok and video content
    if (typeof window.tb_show !== "undefined" && !window.ngg_tiktok_tb_wrapped) {
        var old_tb_show = window.tb_show;
        window.tb_show = function(caption, url, imageGroup) {
            old_tb_show.apply(this, arguments);

            // Find the trigger element by URL
            var $trigger = $("a.thickbox").filter(function() {
                var href = $(this).attr("href");
                return href === url || (href && url && url.indexOf(href) !== -1);
            }).first();

            if ($trigger.length) {
                var playUrl = $trigger.data("tiktok-play-url");
                var shareUrl = $trigger.data("tiktok-share-url");
                var videoUrl = $trigger.attr("data-video-url");

                if (playUrl || shareUrl) {
                    var checkThickbox = setInterval(function () {
                        var $tbWindow = $("#TB_window");
                        var $tbImage = $("#TB_Image");
                        var $tbImageOff = $("#TB_ImageOff");
                        // Thickbox adds TB_Image when it's an image
                        if ($tbWindow.length && ($tbImage.length || $("#TB_ajaxContent").length)) {
                            clearInterval(checkThickbox);
                            $tbWindow.addClass("ngg-tiktok-mode");

                            var w = $tbImage.length ? ($tbImage.width() || $tbImage.attr("width")) : null;
                            var h = $tbImage.length ? ($tbImage.height() || $tbImage.attr("height")) : null;

                            // Insert TikTok container inside TB_ImageOff to maintain layout structure
                            // This ensures it's positioned like the image would be
                            var $tiktokContainer = $("<div class='ngg-tiktok-wrapper'></div>");
                            if ($tbImageOff.length) {
                                $tbImageOff.after($tiktokContainer);
                            } else {
                                $tbWindow.prepend($tiktokContainer);
                            }

                            NextGEN_TikTok.handle_content({
                                playUrl: playUrl,
                                shareUrl: shareUrl,
                                container: $tiktokContainer,
                                width: w,
                                height: h,
                                onBeforeAppend: function () {
                                    // Hide the image but keep TB_ImageOff structure for layout
                                    if ($tbImage.length) $tbImage.hide();
                                    // Hide TB_ImageOff but keep caption visible for navigation
                                    if ($tbImageOff.length) $tbImageOff.hide();
                                    // DO NOT hide TB_caption - it contains navigation controls
                                },
                            });
                        }
                    }, 50);
                    setTimeout(function() { clearInterval(checkThickbox); }, 5000);
                }
                // Handle video links
                else if (videoUrl && window.NextGEN_Video && window.NextGEN_Video.detect_platform(videoUrl)) {
                    var checkThickbox = setInterval(function () {
                        var $tbWindow = $("#TB_window");
                        var $tbImage = $("#TB_Image");
                        if ($tbWindow.length && ($tbImage.length || $("#TB_ajaxContent").length)) {
                            clearInterval(checkThickbox);
                            $tbWindow.addClass("ngg-video-mode");
                            
                            $tbWindow.find(".ngg-video-container, .ngg-video-error").remove();
                            
                            var galleryId = null;
                            var $galleryContainer = $trigger.closest('[data-gallery-id]');
                            if ($galleryContainer.length) {
                                galleryId = $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
                            }
                            
                            var videoSettings = {};
                            if (window.ngg_video_gallery_settings) {
                                if (galleryId && window.ngg_video_gallery_settings['gallery_' + galleryId]) {
                                    videoSettings = window.ngg_video_gallery_settings['gallery_' + galleryId];
                                } else if (window.ngg_video_gallery_settings.default) {
                                    videoSettings = window.ngg_video_gallery_settings.default;
                                }
                            }

                            // Insert video container inside TB_ImageOff to maintain layout structure
                            var $videoContainer = $("<div class='ngg-video-wrapper'></div>");
                            var $tbImageOff = $("#TB_ImageOff");
                            if ($tbImageOff.length) {
                                $tbImageOff.after($videoContainer);
                            } else {
                                $tbWindow.prepend($videoContainer);
                            }

                            window.NextGEN_Video.handle_content({
                                videoUrl: videoUrl,
                                container: $videoContainer[0],
                                settings: videoSettings,
                                containerClass: "ngg-video-container",
                                videoClass: "ngg-video-player",
                                errorClass: "ngg-video-error",
                                onBeforeAppend: function () {
                                    // Hide the image but keep TB_ImageOff structure for layout
                                    if ($tbImage.length) $tbImage.hide();
                                    // Hide TB_ImageOff but keep caption visible for navigation
                                    if ($tbImageOff.length) $tbImageOff.hide();
                                    // DO NOT hide TB_caption - it contains navigation controls
                                },
                            });
                        }
                    }, 50);
                    setTimeout(function() { clearInterval(checkThickbox); }, 5000);
                }
            }
        };
        window.ngg_tiktok_tb_wrapped = true;
    }
});
