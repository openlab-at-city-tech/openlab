(function($) {
    "use strict";

    window.NextGEN_Video = {
        /**
         * Detect video platform from URL
         * @param {string} url
         * @returns {string|null} Platform name or null if not a video URL
         */
        detect_platform: function(url) {
            if (!url) return null;

            url = url.trim().toLowerCase();

            // YouTube patterns
            if (url.match(/youtube\.com|youtu\.be|youtube-nocookie\.com/)) {
                return 'youtube';
            }

            // Vimeo patterns
            if (url.match(/vimeo\.com/)) {
                return 'vimeo';
            }

            // Dailymotion patterns
            if (url.match(/dailymotion\.com|dai\.ly/)) {
                return 'dailymotion';
            }

            // Twitch patterns
            if (url.match(/twitch\.tv/)) {
                return 'twitch';
            }

            // VideoPress patterns
            if (url.match(/videopress\.com|video\.wordpress\.com/)) {
                return 'videopress';
            }

            // Wistia patterns
            if (url.match(/wistia\.com|wistia\.net/)) {
                return 'wistia';
            }

            // Local video patterns (direct video file URLs)
            if (url.match(/\.(mp4|webm|ogg|ogv|mov|avi|wmv|flv|mkv)(\?|$)/i)) {
                return 'local';
            }

            return null;
        },

        /**
         * Extract video ID from YouTube URL
         * @param {string} url
         * @returns {string|null}
         */
        extract_youtube_id: function(url) {
            if (!url) return null;

            var patterns = [
                /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube-nocookie\.com\/embed\/)([^&\n?#]+)/,
                /youtube\.com\/.*[?&]v=([^&\n?#]+)/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            return null;
        },

        /**
         * Extract video ID from Vimeo URL
         * @param {string} url
         * @returns {string|null}
         */
        extract_vimeo_id: function(url) {
            if (!url) return null;

            var patterns = [
                /vimeo\.com\/(\d+)/,
                /vimeo\.com\/.*\/(\d+)/,
                /player\.vimeo\.com\/video\/(\d+)/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            return null;
        },

        /**
         * Extract video ID from Dailymotion URL
         * @param {string} url
         * @returns {string|null}
         */
        extract_dailymotion_id: function(url) {
            if (!url) return null;

            var patterns = [
                /dailymotion\.com\/video\/([^/?]+)/,
                /dai\.ly\/([^/?]+)/,
                /dailymotion\.com\/embed\/video\/([^/?]+)/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            return null;
        },

        /**
         * Extract video ID from Twitch URL
         * @param {string} url
         * @returns {Object|null} Object with videoId and type (video/clip)
         */
        extract_twitch_id: function(url) {
            if (!url) return null;

            // Twitch video pattern: twitch.tv/videos/VIDEO_ID
            var videoMatch = url.match(/twitch\.tv\/videos\/(\d+)/);
            if (videoMatch && videoMatch[1]) {
                return { videoId: videoMatch[1], type: 'video' };
            }

            // Twitch clip pattern: twitch.tv/CLIP_ID or clips.twitch.tv/CLIP_ID
            var clipMatch = url.match(/(?:twitch\.tv\/|clips\.twitch\.tv\/)([^/?]+)/);
            if (clipMatch && clipMatch[1]) {
                return { videoId: clipMatch[1], type: 'clip' };
            }

            return null;
        },

        /**
         * Extract video ID from VideoPress URL
         * @param {string} url
         * @returns {string|null}
         */
        extract_videopress_id: function(url) {
            if (!url) return null;

            var patterns = [
                /videopress\.com\/v\/([^/?]+)/,
                /video\.wordpress\.com\/v\/([^/?]+)/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            return null;
        },

        /**
         * Extract video ID from Wistia URL
         * @param {string} url
         * @returns {string|null}
         */
        extract_wistia_id: function(url) {
            if (!url) return null;

            var patterns = [
                /wistia\.(?:com|net)\/medias\/([^/?]+)/,
                /wistia\.(?:com|net)\/embed\/([^/?]+)/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            return null;
        },

        /**
         * Get video embed URL for a platform
         * @param {string} platform
         * @param {string|Object} videoId
         * @param {Object} settings Video settings (showVideoControls, autoplayVideos)
         * @returns {string|null}
         */
        get_embed_url: function(platform, videoId, settings) {
            if (!platform || !videoId) return null;

            settings = settings || {};
            var autoplay = settings.autoplay_videos ? 1 : 0;
            var controls = settings.show_video_controls !== false ? 1 : 0;

            switch (platform) {
                case 'youtube':
                    var youtubeId = typeof videoId === 'string' ? videoId : videoId.videoId;
                    return 'https://www.youtube.com/embed/' + youtubeId + 
                           '?autoplay=' + autoplay + 
                           '&controls=' + controls + 
                           '&rel=0&modestbranding=1';

                case 'vimeo':
                    var vimeoId = typeof videoId === 'string' ? videoId : videoId.videoId;
                    return 'https://player.vimeo.com/video/' + vimeoId + 
                           '?autoplay=' + autoplay + 
                           '&controls=' + controls;

                case 'dailymotion':
                    var dmId = typeof videoId === 'string' ? videoId : videoId.videoId;
                    return 'https://www.dailymotion.com/embed/video/' + dmId + 
                           '?autoplay=' + autoplay + 
                           '&controls=' + controls;

                case 'twitch':
                    var twitchData = typeof videoId === 'object' ? videoId : { videoId: videoId, type: 'video' };
                    if (twitchData.type === 'clip') {
                        return 'https://clips.twitch.tv/embed?clip=' + twitchData.videoId + 
                               '&autoplay=' + autoplay + 
                               '&parent=' + window.location.hostname;
                    } else {
                        return 'https://player.twitch.tv/?video=v' + twitchData.videoId + 
                               '&autoplay=' + autoplay + 
                               '&parent=' + window.location.hostname;
                    }

                case 'videopress':
                    var vpId = typeof videoId === 'string' ? videoId : videoId.videoId;
                    return 'https://videopress.com/embed/' + vpId + 
                           '?autoplay=' + autoplay + 
                           '&controls=' + controls;

                case 'wistia':
                    var wistiaId = typeof videoId === 'string' ? videoId : videoId.videoId;
                    return 'https://fast.wistia.net/embed/iframe/' + wistiaId + 
                           '?autoplay=' + autoplay + 
                           '&controlsVisibleOnLoad=' + controls;

                case 'local':
                    return typeof videoId === 'string' ? videoId : null;

                default:
                    return null;
            }
        },

        /**
         * Create HTML5 video player for local videos
         * @param {string} videoUrl
         * @param {Object} settings Video settings
         * @param {string} containerClass
         * @param {string} videoClass
         * @returns {HTMLElement}
         */
        create_local_player: function(videoUrl, settings, containerClass, videoClass) {
            var container = document.createElement("div");
            container.className = containerClass || "ngg-video-container";

            var video = document.createElement("video");
            video.className = videoClass || "ngg-video-player";
            video.controls = settings.show_video_controls !== false;
            video.autoplay = settings.autoplay_videos === true;
            video.playsInline = true;
            video.preload = "auto";
            video.setAttribute("playsinline", "");
            video.setAttribute("webkit-playsinline", "");
            video.src = videoUrl;

            // Global error handler for video loading failures (CORS, network, unsupported formats)
            video.addEventListener("error", function(e) {
                console.error("Video player error:", {
                    error: e,
                    videoUrl: videoUrl,
                    errorCode: video.error ? video.error.code : "unknown",
                    errorMessage: video.error ? video.error.message : "Unknown error"
                });
            });

            // Size video to match lightbox image sizing behavior
            video.addEventListener("loadedmetadata", function () {
                var naturalWidth = video.videoWidth;
                var naturalHeight = video.videoHeight;

                if (naturalWidth && naturalHeight) {
                    var container = video.closest('.ngg-video-container');
                    var displayWidth = naturalWidth;
                    var displayHeight = naturalHeight;

                    // Detect lightbox type and apply appropriate sizing logic
                    if (container) {
                        var fancyboxContent = container.closest('#fancybox-content');
                        var tbWindow = container.closest('#TB_window');
                        var slImage = container.closest('.sl-image');
                        var shWrap = container.closest('#shWrap');

                        if (shWrap) {
                            // Shutter/Shutter Reloaded
                            var wiH = window.innerHeight || 0;
                            var dbH = document.body.clientHeight || 0;
                            var deH = document.documentElement ? document.documentElement.clientHeight : 0;
                            var wHeight;

                            if (wiH > 0) {
                                wHeight = ((wiH - dbH) > 1 && (wiH - dbH) < 30) ? dbH : wiH;
                                wHeight = ((wHeight - deH) > 1 && (wHeight - deH) < 30) ? deH : wHeight;
                            } else {
                                wHeight = (deH > 0) ? deH : dbH;
                            }

                            if (document.getElementsByTagName("body")[0].className.match(/admin-bar/)
                                && document.getElementById('wpadminbar') !== null) {
                                wHeight = wHeight - document.getElementById('wpadminbar').offsetHeight;
                            }

                            var shHeight = wHeight - 50;
                            var deW = document.documentElement ? document.documentElement.clientWidth : 0;
                            var dbW = window.innerWidth || document.body.clientWidth;
                            var wWidth = (deW > 1) ? deW : dbW;

                            if (displayHeight > shHeight) {
                                displayWidth = displayWidth * (shHeight / displayHeight);
                                displayHeight = shHeight;
                            }
                            if (displayWidth > (wWidth - 16)) {
                                displayHeight = displayHeight * ((wWidth - 16) / displayWidth);
                                displayWidth = wWidth - 16;
                            }

                            video.style.width = displayWidth + "px";
                            video.style.height = displayHeight + "px";
                            video.style.maxWidth = "none";
                            video.style.maxHeight = "none";
                            video.setAttribute("width", displayWidth);
                            video.setAttribute("height", displayHeight);
                        } else if (fancyboxContent) {
                            // Fancybox
                            setTimeout(function() {
                                var contentRect = fancyboxContent.getBoundingClientRect();
                                if (contentRect.width > 10 && contentRect.height > 10) {
                                    var maxW = contentRect.width;
                                    var maxH = contentRect.height;

                                    if (displayWidth > maxW || displayHeight > maxH) {
                                        var ratio = displayWidth / displayHeight > maxW / maxH
                                            ? displayWidth / maxW
                                            : displayHeight / maxH;
                                        displayWidth = displayWidth / ratio;
                                        displayHeight = displayHeight / ratio;
                                    }

                                    video.style.width = displayWidth + "px";
                                    video.style.height = displayHeight + "px";
                                    video.setAttribute("width", displayWidth);
                                    video.setAttribute("height", displayHeight);
                                }
                            }, 50);
                            return;
                        } else if (tbWindow) {
                            // Thickbox
                            var pageWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                            var pageHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                            var x = pageWidth - 150;
                            var y = pageHeight - 150;

                            if (displayWidth > x) {
                                displayHeight = displayHeight * (x / displayWidth);
                                displayWidth = x;
                                if (displayHeight > y) {
                                    displayWidth = displayWidth * (y / displayHeight);
                                    displayHeight = y;
                                }
                            } else if (displayHeight > y) {
                                displayWidth = displayWidth * (y / displayHeight);
                                displayHeight = y;
                                if (displayWidth > x) {
                                    displayHeight = displayHeight * (x / displayWidth);
                                    displayWidth = x;
                                }
                            }

                            video.style.width = displayWidth + "px";
                            video.style.height = displayHeight + "px";
                            video.setAttribute("width", displayWidth);
                            video.setAttribute("height", displayHeight);
                        } else if (slImage) {
                            // SimpleLightbox
                            var widthRatio = 0.8;
                            var heightRatio = 0.9;
                            var windowWidth = window.innerWidth;
                            var windowHeight = window.innerHeight;

                            var maxWidth = windowWidth * widthRatio;
                            var maxHeight = windowHeight * heightRatio;

                            if (displayWidth > maxWidth || displayHeight > maxHeight) {
                                var ratio = displayWidth / displayHeight > maxWidth / maxHeight
                                    ? displayWidth / maxWidth
                                    : displayHeight / maxHeight;
                                displayWidth /= ratio;
                                displayHeight /= ratio;
                            }

                            video.style.width = displayWidth + "px";
                            video.style.height = displayHeight + "px";
                            video.style.maxWidth = maxWidth + "px";
                            video.style.maxHeight = maxHeight + "px";
                        }
                    }
                }
            });

            // Attempt autoplay when ready
            if (settings.autoplay_videos) {
                video.addEventListener("canplay", function () {
                    video.play().catch(function (error) {
                        console.error("Video autoplay failed:", error);
                    });
                });
            }

            container.appendChild(video);
            return container;
        },

        /**
         * Create iframe embed for platform videos
         * @param {string} embedUrl
         * @param {Object} settings Video settings
         * @param {string} containerClass
         * @returns {HTMLElement}
         */
        create_embed_player: function(embedUrl, settings, containerClass) {
            var container = document.createElement("div");
            container.className = containerClass || "ngg-video-container";

            var iframe = document.createElement("iframe");
            iframe.src = embedUrl;
            iframe.frameBorder = "0";
            iframe.allowFullscreen = true;
            iframe.setAttribute("allow", "autoplay; encrypted-media");
            iframe.style.width = "100%";
            iframe.style.height = "100%";
            iframe.style.border = "none";

            // Global error handler for iframe loading failures (CORS, network, etc.)
            iframe.addEventListener("error", function(e) {
                console.error("Video iframe error:", {
                    error: e,
                    embedUrl: embedUrl
                });
            });

            // Handle iframe load errors (some browsers don't fire error event on iframe)
            iframe.addEventListener("load", function() {
                // Check if iframe loaded successfully by trying to access content
                try {
                    // This will throw if cross-origin restrictions prevent access
                    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                } catch (e) {
                    // Cross-origin restriction is normal, not an error
                    // Only log actual errors
                    if (e.name !== "SecurityError") {
                        console.error("Video iframe load error:", e);
                    }
                }
            });

            // Size iframe to match lightbox image sizing behavior
            // Use responsive aspect ratio (16:9 default for most platforms)
            var aspectRatio = 16 / 9;
            var maxWidth = window.innerWidth * 0.9;
            var maxHeight = window.innerHeight * 0.9;
            var width = Math.min(maxWidth, 1080);
            var height = width / aspectRatio;

            if (height > maxHeight) {
                height = maxHeight;
                width = height * aspectRatio;
            }

            container.style.width = width + "px";
            container.style.height = height + "px";
            container.style.maxWidth = "100%";
            container.style.maxHeight = "90vh";

            container.appendChild(iframe);
            return container;
        },

        /**
         * Generic content handler to inject video into a container
         * @param {Object} options
         * @param {string} options.videoUrl - Video URL
         * @param {HTMLElement|jQuery} options.container - Container to append content to
         * @param {Object} options.settings - Video settings (showVideoControls, showPlayPauseControls, autoplayVideos)
         * @param {string} [options.containerClass] - CSS class for player container
         * @param {string} [options.videoClass] - CSS class for video element
         * @param {Function} [options.onBeforeAppend] - Callback before appending
         * @returns {HTMLElement|null}
         */
        handle_content: function(options) {
            var self = this;
            var videoUrl = options.videoUrl;
            var $targetContainer = $(options.container);
            var settings = options.settings || {};

            if (!videoUrl) {
                console.error("Video URL is required");
                return null;
            }

            try {
                var platform = self.detect_platform(videoUrl);
                if (!platform) {
                    // Not a recognized video URL
                    console.warn("Unrecognized video URL:", videoUrl);
                    return null;
                }
            } catch (error) {
                console.error("Error detecting video platform:", error);
                return null;
            }

            var videoContent = null;
            var videoId = null;

            // Extract video ID based on platform
            switch (platform) {
                case 'youtube':
                    videoId = self.extract_youtube_id(videoUrl);
                    break;
                case 'vimeo':
                    videoId = self.extract_vimeo_id(videoUrl);
                    break;
                case 'dailymotion':
                    videoId = self.extract_dailymotion_id(videoUrl);
                    break;
                case 'twitch':
                    videoId = self.extract_twitch_id(videoUrl);
                    break;
                case 'videopress':
                    videoId = self.extract_videopress_id(videoUrl);
                    break;
                case 'wistia':
                    videoId = self.extract_wistia_id(videoUrl);
                    break;
                case 'local':
                    videoId = videoUrl; // For local videos, use the URL directly
                    break;
            }

            if (!videoId) {
                var errorMsg = self.create_error("Could not extract video ID from URL", options.errorClass);
                if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                $targetContainer.append(errorMsg);
                return errorMsg;
            }

            // Create player based on platform
            try {
                if (platform === 'local') {
                    videoContent = self.create_local_player(videoId, settings, options.containerClass, options.videoClass);
                } else {
                    var embedUrl = self.get_embed_url(platform, videoId, settings);
                    if (embedUrl) {
                        videoContent = self.create_embed_player(embedUrl, settings, options.containerClass);
                    } else {
                        var errorMsg = self.create_error("Could not generate embed URL", options.errorClass);
                        if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                        $targetContainer.append(errorMsg);
                        return errorMsg;
                    }
                }

                if (videoContent) {
                    // Handle errors for local videos
                    if (platform === 'local') {
                        var video = videoContent.querySelector("video");
                        if (video) {
                            video.onerror = function () {
                                $(videoContent).remove();
                                var errorMsg = self.create_error("Video failed to load", options.errorClass);
                                if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                                $targetContainer.append(errorMsg);
                            };
                        }
                    }

                    if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(videoContent);
                    $targetContainer.append(videoContent);
                }
            } catch (error) {
                console.error("Error creating video player:", error);
                var errorMsg = self.create_error("Video player creation failed", options.errorClass);
                if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                $targetContainer.append(errorMsg);
                return errorMsg;
            }

            return videoContent;
        },

        /**
         * Create error message element
         * @param {string} message
         * @param {string} containerClass
         * @returns {HTMLElement}
         */
        create_error: function(message, containerClass) {
            var container = document.createElement("div");
            container.className = containerClass || "ngg-video-error";
            container.innerHTML =
                '<div class="ngg-video-error-content">' +
                '<span class="ngg-video-error-icon">&#9888;</span>' +
                '<span class="ngg-video-error-text">' +
                (message || "Video failed to load") +
                "</span>" +
                "</div>";
            return container;
        }
    };
})(jQuery);

