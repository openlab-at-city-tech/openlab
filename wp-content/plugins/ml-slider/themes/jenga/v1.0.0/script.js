(function($) {
	/**
	 * Extra JS for the Jenga theme
	 *
	 * 1. positions the different elements:
	 *    - arrows
	 *    - dots
	 *    - caption
	 *    - filmstrip
	 */

	 // metaslider has been initilalised
 	$(document).on('metaslider/initialized', function(e, identifier) {
 		// if .ms-theme-architekt
 		if ($(identifier).closest('.metaslider.ms-theme-jenga').length) {
 			var $slider = $(identifier);
 			var $container = $(identifier).closest('.metaslider.ms-theme-jenga');
 			var captions = $slider.find('.caption');
 			if (captions.length) {
 				$container.addClass('ms-has-caption');
 			}
 			$container.addClass('ms-loaded');
		}
 
		// Wrap nav and arrows in a div
		// When Dots
		$(".metaslider.has-dots-nav.ms-theme-jenga:not(.has-carousel-mode) .flexslider:not(.filmstrip) > .flex-control-paging, .metaslider.has-dots-nav.ms-theme-jenga:not(.has-carousel-mode) .flexslider:not(.filmstrip) > .flex-direction-nav").wrapAll("<div class='slide-control'></div>");

		// When Carousel
		$(".metaslider.ms-theme-jenga.has-carousel-mode .flexslider > .flex-control-paging").wrap("<div class='slide-control'></div>");

		// When Filmstrip
		$(".metaslider.ms-theme-jenga.has-filmstrip-nav .flexslider:not(.filmstrip) > .flex-direction-nav").wrap("<div class='slide-control'></div>");

		// Nivo with dots
		$(".metaslider.ms-theme-jenga:not(.has-carousel-mode).metaslider-responsive > div > .rslides_nav, .metaslider.ms-theme-jenga:not(.has-carousel-mode).metaslider-responsive > div > .rslides_tabs").wrapAll("<div class='slide-control'></div>");

		// Nivo wrap arrows
		$(".metaslider.ms-theme-jenga:not(.has-carousel-mode).metaslider-responsive .slide-control > .rslides_nav").wrapAll("<div class='rslides_arrows'></div>");

		// Nivo put arrows after dots
		var nivo_arrows = $(".metaslider.ms-theme-jenga:not(.has-carousel-mode).metaslider-responsive .rslides_arrows");
		nivo_arrows.next().insertBefore(nivo_arrows);

		$(window).trigger('resize');
 	});

	$(window).on('resize', function(e) {
		$(function() {
			var slideshow = $('.metaslider.ms-theme-jenga');
			var slide_control = slideshow.find('.slide-control');
			if (slideshow.find('.flexslider').length == 0) {
				// Legacy slider libraries
				slide_control.css({
					'position' : 'absolute',
					'top' : '39%',
					'min-height' : "140px",
					'margin-top' : "-50px"
				});
			} else {
				// Only flexslider
				var small = (slideshow.outerHeight() - 50) < slide_control.outerHeight() ? true : false;
				var small_xtra = small && $(window).width() < 439 ? true : false;
				var scale = small ? 'scale(0.7)' : '';
				scale = small_xtra ? 'scale(0.5)' : scale;
				var top = small ? '30%' : '39%';
				slide_control.css({
					'position' : 'absolute',
					'top' : top,
					'min-height' : "140px",
					'margin-top' : "-50px",
					'transform': scale
				});
			}
		});
	});

})(jQuery)
