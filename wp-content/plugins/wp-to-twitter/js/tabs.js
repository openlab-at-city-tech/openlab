jQuery(document).ready(function ($) {
	var tabs = $('.wpt-settings .wptab').length;
	$('.wpt-settings .tabs a[href="#' + wpt.firstItem + '"]').addClass('active').attr( 'aria-selected', 'true' );
	if (tabs > 1) {
		$('.wpt-settings .wptab').not('#' + wpt.firstItem).hide();
		$('.wpt-settings .tabs a').on('click', function (e) {
			e.preventDefault();
			$('.wpt-settings .tabs a').removeClass('active').attr( 'aria-selected', 'false' );
			$(this).addClass('active').attr( 'aria-selected', 'true' );
			var target = $(this).attr('href');
			$('.wpt-settings .wptab').not(target).hide();
			$(target).show().attr( 'tabindex', '-1' ).focus();
		});
	};

	var permissions = $('.wpt-permissions .wptab').length;
	$('.wpt-permissions .tabs a[href="#' + wpt.firstPerm + '"]').addClass('active').attr( 'aria-selected', 'true' );
	if (permissions > 1) {
		$('.wpt-permissions .wptab').not('#' + wpt.firstPerm).hide();
		$('.wpt-permissions .tabs a').on('click', function (e) {
			e.preventDefault();
			$('.wpt-permissions .tabs a').removeClass('active').attr( 'aria-selected', 'false' );
			$(this).addClass('active').attr( 'aria-selected', 'true' );
			var target = $(this).attr('href');
			$('.wpt-permissions .wptab').not(target).hide();
			$(target).show().attr( 'tabindex', '-1' ).focus();
		});
	};
});