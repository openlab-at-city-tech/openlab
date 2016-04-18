(function ($) {
	"use strict";

	$(function () {

		// This used to be done server-side; however, since we support multiple post types now,
		// it's easier to have JavaScript duck out when it's not needed.
		if ( 0 == $('#post_is_sticky').length ) {
			return;
		} // end if

		// Move the 'Category Sticky' container before the 'Categories' container
		$('#post_is_sticky').insertBefore('#categorydiv');

	});
}(jQuery));