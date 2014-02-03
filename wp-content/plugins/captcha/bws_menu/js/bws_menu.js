(function($) {
	$(document).ready( function() {
		/* new version */
	/*	var product = $( '.bws_product' ),
			max = 0;
		$( product ).each( function () {
			if ( $( this ).outerHeight( true ) > max )
				max = $( this ).outerHeight( true );
		});

		$( '.bws_product' ).css( 'height', max + 'px' );
	*/	
		$( '.bws_product_button' ).css( 'display', 'none' );
	
		$( '.bws_product_box' ).hover( function() {
			if ( $( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_description' ).length > 0 ) {
				$( this ).children( '.bws_product' ).addClass( 'bws_product_pro' );
				$( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_description' ).css( 'display', 'block' );
				$( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_icon' ).css( 'display', 'none' );
				$( this ).children( '.bws_product' ).children( 'a' ).children( '.bws_product_button' ).css( 'display', 'block' );
			}
		}, function() {			
			if ( $( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_description' ).length > 0 ) {
				$( this ).children( '.bws_product' ).removeClass( 'bws_product_pro' );
				$( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_description' ).css( 'display', 'none' );
				$( this ).children( '.bws_product' ).children( '.bws_product_content' ).children( '.bws_product_icon' ).css( 'display', 'block' );
				$( this ).children( '.bws_product' ).children( 'a' ).children( '.bws_product_button' ).css( 'display', 'none' );
			}
		});

		/* old version */
		if ( $( 'input[name="bwsmn_form_email"]' ).val() == '' ) {
			$( '.bws_system_info_meta_box .inside' ).css( 'display', 'none' );
		}

		$( '.bws_system_info_meta_box .hndle' ).click( function() {
			if ( $( '.bws_system_info_meta_box .inside' ).is( ':visible' ) ) {
				$( '.bws_system_info_meta_box .inside' ).css( 'display', 'none' );
			} else {
				$( '.bws_system_info_meta_box .inside' ).css( 'display', 'block' );
			}					
		});
		
		$( '.bws_system_info_meta_box .handlediv' ).click( function() {
			if ( $( '.bws_system_info_meta_box .inside' ).is( ':visible' ) ) {
				$( '.bws_system_info_meta_box .inside' ).css( 'display', 'none' );
			} else {
				$( '.bws_system_info_meta_box .inside' ).css( 'display', 'block' );
			}					
		});
	});
})(jQuery);