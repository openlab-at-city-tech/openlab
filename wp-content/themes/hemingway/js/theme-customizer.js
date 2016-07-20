/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

	//Update site accent color in real time...
	wp.customize( 'accent_color', function( value ) {
		value.bind( function( newval ) {
			$('.post-content a').css('color', newval );
			$('.widget-content a').css('color', newval );
			$('.featured-media .sticky-post').css('background', newval );
			$('.widget_search #searchsubmit').css('background', newval ).css('border', newval );
		} );
	} );
	
} )( jQuery );