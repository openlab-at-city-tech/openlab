jQuery(document).ready(function ($) {
	const tabs = $('.wpt-settings .wptab').length;
	let selected = ( window.location.hash != '' ) ? window.location.hash : wpt.firstItem;
	selected = selected.replace( '#', '' );
	$('.wpt-settings .tabs button[aria-controls="' + selected + '"]').attr( 'aria-selected', 'true' );
	if (tabs > 1) {
		$('.wpt-settings .wptab').not( '#' + selected ).hide();
		$('.wpt-settings .tabs button').on('click', function(e) {
			$('.wpt-settings .tabs button').attr( 'aria-selected', 'false' );
			$(this).attr( 'aria-selected', 'true' );
			let target = '#' + $(this).attr('aria-controls');
			$('.wpt-settings .wptab').not( target).hide();
			let form = $( this ).parents( 'form' );
			form.attr( 'action', target );
			$(target).show();
		});
	};

	const permissions = $('.wpt-permissions .wptab').length;
	selected = ( window.location.hash != '' ) ? window.location.hash : wpt.firstPerm;
	selected = selected.replace( '#', '' );
	$('.wpt-permissions .tabs button[aria-controls="' + selected + '"]').attr( 'aria-selected', 'true' );
	if (permissions > 1) {
		$('.wpt-permissions .wptab').not('#' + wpt.firstPerm).hide();
		$('.wpt-permissions .tabs button').on('click', function (e) {
			e.preventDefault();
			$('.wpt-permissions .tabs button').attr( 'aria-selected', 'false' );
			$(this).attr( 'aria-selected', 'true' );
			let target = '#' + $(this).attr('aria-controls');
			$('.wpt-permissions .wptab').not(target).hide();
			let form = $( this ).parents( 'form' );
			form.attr( 'action', target );
			$(target).show();
		});
	};

	var maxLength = $( '#wpt_x_length' );
	if ( maxLength.val() <= 280 ) {
		$( '#maxlengthwarning' ).hide();
	}
	if ( maxLength.length > 0 ) {
		maxLength.on( 'change', function(e) {
			var val = $( this ).val();
			if ( val > 280 ) {
				$( '#maxlengthwarning' ).show();
			} else {
				$( '#maxlengthwarning' ).hide();
			}
		});
	}
});