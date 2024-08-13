(function($) {
   // metaslider has been initilalised
	$(document).on('metaslider/initialized', function (e, identifier) {
		// if .ms-theme-precognition
		if ($(identifier).closest('.metaslider.ms-theme-precognition').length) {
			var $slider = $(identifier);
			var $container = $(identifier).closest('.metaslider.ms-theme-precognition');
			var captions = $slider.find('.caption');
			if (captions.length) {
				$container.addClass('ms-has-caption');
			}

			// Revert nav and slides order
			var slides = $slider.find('.flex-viewport ul.slides');
			if (slides.length) {
				var nav = $slider.find('.flex-control-nav').detach();
				nav.insertAfter(slides);
			}
			
			$container.addClass('ms-loaded');
		}
		$(window).trigger('resize');
	});

   $(window).on('load resize', function(e) {
		// go through the sliders with this theme
		$('.metaslider').each(function(index) {
			var width = $(this).outerWidth();
			if (width < 800) {
				$(this).addClass('ms-is-small');
			} else {
				$(this).removeClass('ms-is-small');
			}
		});
	});
   $(window).on('load', function() {
      $(".nivo-control, .cs-buttons a").wrap("<li></li>");
	});
})(jQuery)
