(function($) {
   // metaslider has been initilalised
	$(document).on('metaslider/initialized', function (e, identifier) {
		// if .ms-theme-nexus
		if ($(identifier).closest('.metaslider.ms-theme-nexus').length) {
			var $slider = $(identifier);
			var $container = $(identifier).closest('.metaslider.ms-theme-nexus');
			var captions = $slider.find('.caption');
			if (captions.length) {
				$container.addClass('ms-has-caption');
			}
			var slides = $slider.find('ul.slides');
			slides.find('li').each(function(index) {
				var link = $(this).find('a.metaslider_image_link');
				if (link.length > 0) {
					 var href = link.attr('href');
					 var target = link.attr('target');
					 var text = link.attr('aria-label');
					 if (text == undefined) {
						text = nexusText.buttonText;
					 }
					 var newLink = $('<a></a>')
						.attr('href', href)
						.attr('target', target || '_self')
						.text(text)
						.addClass('nexus-link');
					$(this).append(newLink);
				} 
			});
		}
		$(window).trigger('resize');
	});
})(jQuery)


