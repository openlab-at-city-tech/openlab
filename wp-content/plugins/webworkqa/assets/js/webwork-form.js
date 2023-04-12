window.wp = window.wp || {};

( function( $ ) {
	var $section_toggle = $( '.section-toggle' );

	$( document ).ready(
		function() {
			$section_toggle.on(
				'click',
				function( e ) {
					e.preventDefault();
					toggle_section( e.target );
				}
			);
		}
	);

	toggle_section = function( clicked ) {
		var $clicked     = $( clicked );
		var $content     = $clicked.siblings( '.section-content' );
		var content_type = $clicked.data( 'content-type' );
		var string_key;

		if ( $content.hasClass( 'section-content-hide' ) ) {
			$content.removeClass( 'section-content-hide' );
			string_key = 'hide_' + content_type;
		} else {
			$content.addClass( 'section-content-hide' );
			string_key = 'show_' + content_type;
		}

		$clicked.html( WeBWorK[ string_key ] );
	};
} )( jQuery );
