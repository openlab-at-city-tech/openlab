/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
 
( function( $ ) {

	// Site Name
	wp.customize( 'blogname', function( value ) {
		value.bind( function( newval ) {
			$( '.site-name' ).text( newval );
		} );
	} );

	// Background color
	wp.customize( 'background_color', function( value ) {
		value.bind( function( newval ) {
			$( '.site-nav, .site-nav footer' ).css( 'background', newval );
		} );
	} );

	// Dark Mode
	wp.customize( 'hamilton_dark_mode', function( value ) {
		value.bind( function( newval ) {
			if ( newval == true ) {
				$( 'body' ).addClass( 'dark-mode' );
			} else {
				$( 'body' ).removeClass( 'dark-mode' );
			}
		} );
	} );
	
	// Alt Nav
	wp.customize( 'hamilton_alt_nav', function( value ) {
		value.bind( function( newval ) {
			console.log( newval );
			if ( newval == true ) {
				$( 'body' ).addClass( 'show-alt-nav' );
			} else {
				$( 'body' ).removeClass( 'show-alt-nav' );
			}
		} );
	} );
	
	// Three grid columns
	wp.customize( 'hamilton_max_columns', function( value ) {
		value.bind( function( newval ) {
			if ( newval == true ) {
				$( 'body' ).addClass( 'three-columns-grid' );
			} else {
				$( 'body' ).removeClass( 'three-columns-grid' );
			}
			$( '.posts' ).masonry();
			$( '.tracker' ).each( function() {
				$( this ).addClass( 'will-spot' ).removeClass( 'spotted' );
				if ( $( this ).offset().top < $( window ).height() ) {
					$( this ).addClass( 'spotted' );
				}
			} );
		} );
	} );
	
	// Show preview titles
	wp.customize( 'hamilton_show_titles', function( value ) {
		value.bind( function( newval ) {
			if ( newval == true ) {
				$( 'body' ).addClass( 'show-preview-titles' );
			} else {
				$( 'body' ).removeClass( 'show-preview-titles' );
			}
		} );
	} );
	
} )( jQuery );