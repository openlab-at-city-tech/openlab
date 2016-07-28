(function($) {
	$(document).ready( function() {
		/*  add to whitelist my ip */
		$( 'input[name="cptch_add_to_whitelist_my_ip"]' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				var my_ip = $( 'input[name="cptch_add_to_whitelist_my_ip_value"]' ).val();
				$( 'input[name="cptch_add_to_whitelist"]' ).val( my_ip ).attr( 'readonly', 'readonly' );
			} else {
				$( 'input[name="cptch_add_to_whitelist"]' ).val( '' ).removeAttr( 'readonly' );
			}
		});

		var limit_options = $( '.cptch_limt_options' );
		$( 'input[name="cptch_use_time_limit"]' ).each( function() {
			if ( ! $( this ).is( ':checked' ) )
				limit_options.hide();
		}).click( function() {
			if ( $( this ).is( ':checked' ) )
				limit_options.show();
			else
				limit_options.hide();
		});

		$( '#cptch_use_la_whitelist' ).click( function() {
			$( this ).closest( 'form' ).submit();
		});

		/**
		 * @since 4.2.0
		 */
		$( 'button[name="cptch_show_whitelist_form"]' ).click( function() {
			$( this ).parent( 'form' ).hide();
			$( '.cptch_whitelist_form' ).show();
			return false;
		});
		var package_list = $( "#cptch_package_list" );
		if ( package_list.length ) {
			package_list.resizable({ handles: ( $( 'body' ).hasClass( 'rtl' ) ? 'sw' : 'se' ) }).
				find( '.ui-resizable-handle' ).addClass( 'dashicons dashicons-editor-code' );
		}
	});
})(jQuery);