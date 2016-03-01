(function ($) {
	"use strict";

	$(function () {

		// This used to be done server-side; however, since we support multiple post types now,
		// it's easier to have JavaScript duck out when it's not needed.
		if ( 0 === $('.post-title').length ) {
			return;
		} // end if

		$('.post-title > strong').each(function () {

			// Store a reference to the particular post title anchor
			var $this = $(this);

			// Send a request back to the server to find out if this post is category sticky
			$.get(ajaxurl, {

				action:		'is_category_sticky_post',
				post_id:	$(this).parent().prev().children('input').val()

			}, function(response) {

				// If the response isn't zero, then append the localized response to this post title
				if( '0' !== response ) {
					$this.append( response );
				} // end if

			});

		});

	});

}(jQuery));