(function( $ ) {
	'use strict';

	$(function() {

	$( '.bp-toolkit-rating-link' ).on('click', function() {
		$(this).parent().text($(this).data("rated"));
	});

});

})( jQuery );
