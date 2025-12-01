(function($) {
   // metaslider has been initilalised
	$(document).on('metaslider/initialized', function (e, identifier) {
		// if .ms-theme-precognition
		var $wrapper = $(identifier).closest('.metaslider.ms-theme-precognition');
		if ($wrapper.length) {
			var $slider = $(identifier);
			var $container = $(identifier).closest('.metaslider.ms-theme-precognition');
			var captions = $slider.find('.caption');
			if (captions.length) {
				$container.addClass('ms-has-caption');
			}

			// Revert nav and slides order
			var slides = $slider.find('ul.slides');
			if (slides.length) {
				var nav = $slider.find('.flex-control-nav').detach();
				nav.insertAfter(slides);

				// Update nav dots with image titles
				if (!$wrapper.hasClass('has-carousel-mode') && ($wrapper.hasClass('has-dots-nav') || $wrapper.hasClass('has-dots-onhover-navigation'))) {
					var id = $slider.attr('id').split('_')[1] || '';
					var slideItems = slides.find('> li:not(.clone)');
					var navItems = nav.find('li > a');

					nav.addClass('titleNav-' + id).removeClass('flex-control-paging');

					slideItems.each(function(index) {
						var title = $(this).find('img').attr('title') || '';
						if (title) {
							navItems.eq(index).text(title);
						}
					});
				}
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
