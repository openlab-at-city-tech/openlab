(function ($) {
	var init = function () {
		$.each(window.galleries, function (index, gallery) {
			if (gallery.display_type === 'photocrati-nextgen_basic_slideshow') {
				var settings = gallery.display_settings;
				var fadeValue = settings.transition_style == 'fade' ? true : false;
				// Markup added after page load (AJAX pagination, a page builder preview) arrives with 'refreshed';
				// slideshows that are already running are left alone.
				var $slideshow = $(
					'.ngg-galleryoverview.ngg-slideshow[data-gallery-id="' + gallery.ID + '"]'
				).not('.slick-initialized');
				if (!$slideshow.length) {
					return;
				}
				$slideshow.slick({
					autoplay: Number(settings.autoplay) ? true : false,
					arrows: Number(settings.arrows) ? true : false,
					draggable: false,
					dots: false,
					fade: fadeValue,
					autoplaySpeed: settings.interval,
					speed: settings.transition_speed,
					pauseOnHover: Number(settings.pauseonhover) ? true : false
				});
				$slideshow.css({
					visibility: 'visible'
				});
			}
		});
	};

	$(init);
	$(document).on('refreshed', init);
})(jQuery);
