(function($){
	$(document).ready(function(){
		// Reposition typology-header element.
		setTimeout(function(){
			if ($('#wpadminbar').length && $('#wpadminbar').is(':visible')) {
				// Find the position of the bottom of the admin bar.
				var adminBarBottom = $('#wpadminbar').offset().top + $('#wpadminbar').outerHeight();
				$('.typology-header').css('top', adminBarBottom);
			}
		}, 500);

		// We have to do the same thing with typology-sidebar, which the
		// theme positions only after open.
		$('body').on('click', '.typology-action-sidebar', function() {
			setTimeout(function(){
				if ($('#wpadminbar').length && $('#wpadminbar').is(':visible')) {
					// Find the position of the bottom of the admin bar.
					var adminBarBottom = $('#wpadminbar').offset().top + $('#wpadminbar').outerHeight();
					$('.typology-sidebar').css('top', adminBarBottom);
				}
			}, 10);
		});


	});
})(jQuery);
