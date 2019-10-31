/**
 * mymasonry.js
 * 
 * Initializing masonry grid for widget areas.
 */


( function( $ ) {

$(window).on("load", function() {
    $('#footer-sidebar').masonry({
	itemSelector: '.widget'
    });
});

} ( jQuery ));