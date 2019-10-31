/**
 * widget-img-links.js
 * 
 * Adds class to style images links in the widget area.
 */


( function( $ ) {

$(document).ready( function() {
	$('a').has('img').addClass('has-image')
});

} ( jQuery ));