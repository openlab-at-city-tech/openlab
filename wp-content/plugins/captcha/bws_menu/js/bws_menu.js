(function($) {
	$(document).ready( function() {
		var product = $( '.bws_product_box' ),
			max = 0;
		$( product ).each( function () {
			if ( $( this ).height() > max )
				max = $( this ).height();
		});		
		$( '.bws_product_box' ).css( 'height', max + 'px' );

		if ( $( '.bws-filter' ).length ) {
			var prvPos = $( '.bws-filter' ).offset().top;
			var maxPos = prvPos + $( '.bws-products' ).outerHeight() - $( '.bws-filter' ).outerHeight();

			$( window ).scroll( function() {
				if ( $( window ).width() > 580 ) {
					var scrPos = Number( $( document ).scrollTop() ) + 40;
					if ( scrPos > maxPos ) {
						$( '.bws-filter' ).removeClass( 'bws_fixed' );
					} else if ( scrPos > prvPos ) {
						$( '.bws-filter' ).addClass( 'bws_fixed' );
					} else {
						$( '.bws-filter' ).removeClass( 'bws_fixed' );
					}
				}
			});
		}
		$( '.bws-menu-item-icon' ).click( function() {
			if ( $( this ).hasClass( 'bws-active' ) ) {
				$( this ).removeClass( 'bws-active' );
				$( '.bws-nav-tab-wrapper, .bws-help-links-wrapper' ).hide();
			} else {
				$( this ).addClass( 'bws-active' );
				$( '.bws-nav-tab-wrapper, .bws-help-links-wrapper' ).css( 'display', 'inline-block' );
			}
		});
		$( '.bws-filter-top h2' ).click( function() {
			if ( $( '.bws-filter-top' ).hasClass( 'bws-opened' ) ) {
				$( '.bws-filter-top' ).removeClass( 'bws-opened' );
			} else {
				$( '.bws-filter-top' ).addClass( 'bws-opened' );
			}
		});
		
	});
})(jQuery);