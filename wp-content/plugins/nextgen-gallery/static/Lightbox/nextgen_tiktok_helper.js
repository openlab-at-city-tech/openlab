(function($) {
    "use strict";

    window.NextGEN_TikTok = {
        extract_id: function(url) {
            if (!url) return null;
            url = url.trim();

            var patterns = [
                /tiktok\.com\/@[^\/]+\/video\/(\d+)/i,
                /tiktok\.com\/v\/(\d+)/i,
                /tiktok\.com\/embed\/v2\/(\d+)/i,
                /tiktok\.com\/.*[?&]v=(\d+)/i,
                /\/video\/(\d+)/i,
                /^(\d{15,25})$/,
            ];

            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match && match[1]) {
                    return match[1];
                }
            }

            if (url.match(/vm\.tiktok\.com|tiktok\.com\/t\//i)) {
                var idMatch = url.match(/(\d{15,25})/);
                if (idMatch) {
                    return idMatch[1];
                }
                return null;
            }

            return null;
        },

        create_player: function(videoUrl, containerClass, videoClass) {
            var container = document.createElement("div");
            container.className = containerClass || "ngg-tiktok-container";

            var video = document.createElement("video");
            video.className = videoClass || "ngg-tiktok-video";
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            video.muted = true;
            video.loop = true;
            video.preload = "auto";
            video.setAttribute("playsinline", "");
            video.setAttribute("webkit-playsinline", "");
            video.src = videoUrl;

            video.addEventListener("loadedmetadata", function () {
                var naturalWidth = video.videoWidth;
                var naturalHeight = video.videoHeight;

                if (naturalWidth && naturalHeight) {
                    var container = video.closest('.ngg-tiktok-container');
                    var displayWidth = naturalWidth;
                    var displayHeight = naturalHeight;

                    if (container) {
                        var fancyboxContent = container.closest('#fancybox-content');
                        var tbWindow = container.closest('#TB_window');
                        var slImage = container.closest('.sl-image');
                        var shWrap = container.closest('#shWrap');

                        if (shWrap) {
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

                            // Apply EXACT same scaling logic as Shutter images (lines 239-249)
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
                            // Thickbox: replicate EXACT same image sizing logic from thickbox.js lines 204-224
                            var pageWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                            var pageHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                            var x = pageWidth - 150;  // Same as Thickbox: pagesize[0] - 150
                            var y = pageHeight - 150; // Same as Thickbox: pagesize[1] - 150

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

            video.addEventListener("canplay", function () {
                video.play().catch(function () {});
            });

            container.appendChild(video);
            return container;
        },

        handle_content: function(options) {
            var self = this;
            var playUrl = options.playUrl;
            var $targetContainer = $(options.container);

            if (!playUrl) return null;

            var tiktokContent = null;

            var applyDimensions = function (el) {
                if (options.width) {
                    var w = typeof options.width === "number" ? options.width + "px" : options.width;
                    el.style.width = w;
                    var inner = el.querySelector("video, iframe, .ngg-tiktok-error-content");
                    if (inner) inner.style.width = "100%";
                }
                if (options.height) {
                    var h = typeof options.height === "number" ? options.height + "px" : options.height;
                    el.style.height = h;
                    var inner = el.querySelector("video, iframe, .ngg-tiktok-error-content");
                    if (inner) inner.style.height = "100%";
                }
            };

            if (playUrl) {
                var decodedUrl = playUrl;
                try {
                    decodedUrl = decodeURIComponent(playUrl);
                } catch (e) {}

                tiktokContent = self.create_player(decodedUrl, options.containerClass, options.videoClass);
                if (tiktokContent) {
                    applyDimensions(tiktokContent);
                    var video = tiktokContent.querySelector("video");
                    if (video) {
                        video.onerror = function () {
                            $(tiktokContent).remove();
                            var errorMsg = self.create_error("Video failed to load", options.errorClass);
                            applyDimensions(errorMsg);
                            if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                            $targetContainer.append(errorMsg);
                        };
                    }
                    if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(tiktokContent);
                    $targetContainer.append(tiktokContent);
                }
            } else {
                var errorMsg = self.create_error("Video not available", options.errorClass);
                applyDimensions(errorMsg);
                if (typeof options.onBeforeAppend === "function") options.onBeforeAppend(errorMsg);
                $targetContainer.append(errorMsg);
                tiktokContent = errorMsg;
            }

            return tiktokContent;
        },

        create_error: function(message, containerClass) {
            var container = document.createElement("div");
            container.className = containerClass || "ngg-tiktok-error";
            container.innerHTML =
                '<div class="ngg-tiktok-error-content">' +
                '<span class="ngg-tiktok-error-icon">&#9888;</span>' +
                '<span class="ngg-tiktok-error-text">' +
                (message || "Video failed to load") +
                "</span>" +
                "</div>";
            return container;
        }
    };
})(jQuery);

