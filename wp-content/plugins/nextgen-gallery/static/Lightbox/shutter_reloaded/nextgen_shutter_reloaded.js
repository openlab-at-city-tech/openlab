jQuery(function ($) {
	// SECURITY (#1187): Shutter Reloaded writes the stored title into innerHTML. Only links
	// NextGEN rendered (class shutterset_<gallery>) may carry caption markup, and only after
	// sanitizeTitle(); any other link's title, e.g. a comment link picked up by a global
	// lightbox context, is escaped to plain text.
	var isNextgenLink = function (L) {
		return L.className.indexOf('shutterset_') !== -1;
	};

	var escapeTitle = function (s) {
		return String(s)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	};

	// NextGEN captions are stored as markup and arrive entity-decoded in the title, and a post
	// author can set the shutterset_ class too, so they are filtered rather than trusted: parsed
	// inertly (a DOMParser document runs no handlers and loads nothing), keeping the same tags,
	// attributes and URL rules as the Shutter cores' sanitizer from #1132. Without DOMParser the
	// title falls back to plain text.
	var allowedTags = {
		A: ['href', 'class', 'title'],
		BR: [],
		EM: [],
		I: [],
		STRONG: [],
		B: [],
		U: [],
		P: ['class'],
		DIV: ['class'],
		SPAN: ['class'],
		IMG: ['src', 'alt', 'title']
	};

	var isSafeUrl = function (value, sameOrigin) {
		try {
			var u = new URL(value, document.baseURI);
			var scheme = u.protocol.toLowerCase();
			if (scheme === 'mailto:') {
				return true;
			}
			if (scheme === 'http:' || scheme === 'https:') {
				return !sameOrigin || u.origin === window.location.origin;
			}
			return false;
		} catch (e) {
			return false;
		}
	};

	var sanitizeTitle = function (s) {
		if (typeof DOMParser === 'undefined') {
			return escapeTitle(s);
		}
		var doc = new DOMParser().parseFromString(String(s), 'text/html');
		var els = doc.body.querySelectorAll('*');
		for (var i = 0; i < els.length; i++) {
			var el = els[i];
			var allowed = allowedTags[el.tagName];
			if (!allowed) {
				el.parentNode && el.parentNode.removeChild(el);
				continue;
			}
			for (var j = el.attributes.length - 1; j >= 0; j--) {
				var name = el.attributes[j].name;
				if (
					allowed.indexOf(name) === -1 ||
					((name === 'href' || name === 'src') &&
						!isSafeUrl(el.attributes[j].value, name === 'src'))
				) {
					el.removeAttribute(name);
				}
			}
		}
		return doc.body.innerHTML;
	};

	var initTimeout;
	var callback = function () {
		var selector = nextgen_lightbox_filter_selector($, $([]));
		selector.addClass('shutterset');
		if (typeof nextgen_shutter2_i18n != 'undefined') {
			shutterReloaded.L10n = nextgen_shutter2_i18n;
		}

		if (typeof shutterReloaded !== 'undefined' && !shutterReloaded.ngg_tiktok_wrapped) {
			var oldInit = shutterReloaded.Init;
			shutterReloaded.Init = function (a) {
				oldInit.apply(this, arguments);
				for (var i = 0; i < document.links.length; i++) {
					var L = document.links[i];
					if (shutterLinks[i]) {
						shutterLinks[i].tiktok_play_url = $(L).data('tiktok-play-url');
						shutterLinks[i].tiktok_share_url = $(L).data('tiktok-share-url');
						shutterLinks[i].video_url = $(L).attr('data-video-url');
						if (shutterLinks[i].title) {
							shutterLinks[i].title = isNextgenLink(L)
								? sanitizeTitle(shutterLinks[i].title)
								: escapeTitle(shutterLinks[i].title);
						}
					}
				}
			};

			var oldMake = shutterReloaded.Make;
			shutterReloaded.Make = function (ln, fs) {
				this.current_ln = ln;
				return oldMake.apply(this, arguments);
			};

			var oldShowImg = shutterReloaded.ShowImg;
			shutterReloaded.ShowImg = function () {
				oldShowImg.apply(this, arguments);
				var t = this;
				var ln = t.current_ln;
				var $img = $('#shTopImg');
				if (ln && shutterLinks[ln]) {
					var link = shutterLinks[ln];
					// Handle TikTok
					if (link.tiktok_play_url || link.tiktok_share_url) {
						$('#shShutter').addClass('ngg-tiktok-mode');
						$('#shDisplay').addClass('ngg-tiktok-mode');
						$('body').addClass('ngg-tiktok-shutter-active');
						$('html').addClass('ngg-tiktok-shutter-active');
						NextGEN_TikTok.handle_content({
							playUrl: link.tiktok_play_url,
							shareUrl: link.tiktok_share_url,
							container: $('#shWrap'),
							onBeforeAppend: function () {
								$img.hide();
							}
						});
					}
					// Handle video links
					else if (
						link.video_url &&
						window.NextGEN_Video &&
						window.NextGEN_Video.detect_platform(link.video_url)
					) {
						$('#shShutter').addClass('ngg-video-mode');
						$('#shDisplay').addClass('ngg-video-mode');
						$('#shWrap').addClass('ngg-video-mode');

						// Remove existing video containers
						$('#shWrap .ngg-video-container, #shWrap .ngg-video-error').remove();

						// Get gallery ID for settings
						var galleryId = null;
						var $link = $(document.links[ln]);
						var $galleryContainer = $link.closest('[data-gallery-id]');
						if ($galleryContainer.length) {
							galleryId =
								$galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
						}

						// Get video settings
						var videoSettings = {};
						if (window.ngg_video_gallery_settings) {
							if (galleryId && window.ngg_video_gallery_settings['gallery_' + galleryId]) {
								videoSettings = window.ngg_video_gallery_settings['gallery_' + galleryId];
							} else if (window.ngg_video_gallery_settings.default) {
								videoSettings = window.ngg_video_gallery_settings.default;
							}
						}

						window.NextGEN_Video.handle_content({
							videoUrl: link.video_url,
							container: $('#shWrap')[0],
							settings: videoSettings,
							containerClass: 'ngg-video-container',
							videoClass: 'ngg-video-player',
							errorClass: 'ngg-video-error',
							onBeforeAppend: function () {
								$img.hide();
							}
						});
					}
				}
			};

			var oldHide = shutterReloaded.hideShutter;
			shutterReloaded.hideShutter = function () {
				$('#shShutter').removeClass('ngg-tiktok-mode ngg-video-mode');
				$('#shDisplay').removeClass('ngg-tiktok-mode ngg-video-mode');
				$('#shWrap').removeClass('ngg-video-mode');
				$('body').removeClass('ngg-tiktok-shutter-active');
				$('html').removeClass('ngg-tiktok-shutter-active');
				$('#shWrap .ngg-tiktok-container, #shWrap .ngg-tiktok-error').remove();
				$('#shWrap .ngg-video-container, #shWrap .ngg-video-error').remove();
				return oldHide.apply(this, arguments);
			};

			shutterReloaded.ngg_tiktok_wrapped = true;
		}

		// Debounce Init so ready + refreshed in quick succession (e.g. admin live preview) only run once
		clearTimeout(initTimeout);
		initTimeout = setTimeout(function () {
			// 'sh': only links whose class contains "shutter" (NextGEN's shutterset_<gallery> and
			// the shutterset class added above), not every image link on the page (#1187).
			shutterReloaded.Init('sh');
		}, 80);
	};

	$(window).on('refreshed', callback);

	var flag = 'shutterReloaded';
	if (typeof $(window).data(flag) == 'undefined') {
		$(window).data(flag, true);
	} else {
		return;
	}

	callback();
});
