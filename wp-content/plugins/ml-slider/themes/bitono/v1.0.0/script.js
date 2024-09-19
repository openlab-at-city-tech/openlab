(function($) {
	$(document).on('metaslider/initialized', function (e, identifier) {
		if ($(identifier).closest('.metaslider.ms-theme-bitono').length) {
			var slideshow = $(identifier);

			// We look for Image slides only
			slideshow.find('.slides > li.ms-image').each(function() {
				var slide = $(this);
				var caption = slide.find('.caption-wrap');
				var link = slide.find('.metaslider_image_link');

				if (caption.length > 0 && link.length > 0 && !link.hasClass('__link_ready')) {
					link.addClass('__link_ready');
					caption.css('cursor','pointer');
					caption.find('a').on('click', function(e) {
						// Prevent click event reaching caption
						e.stopPropagation();
					});

					caption.on('click', function() {
						window.open(link.attr('href'), link.attr('target') || '_self');
					});
				}
			});
		}
	});
})(jQuery)


