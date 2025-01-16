'use strict';
jQuery(document).ready(function($) {

	// get all article copies on this page
	let $articles_counter_class = $('.eckb-article-page-content-counter');

	// check if we have duplicate article content
	if ( $articles_counter_class.length < 2 ) {
		return;
	}

	// prevent infinite loop
	if ( typeof window.epkb_the_content_fix !== 'undefined' ) {
		return;
	}

	window.epkb_the_content_fix = 1;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {
			action: 'epkb_update_the_content_flag',
			_wpnonce_epkb_ajax_action: epkb_the_content_i18n.nonce,
			post_id: epkb_the_content_i18n.post_id
		},
		url: epkb_the_content_i18n.ajaxurl,
	}).done(function (response) {

		if (typeof response.error != 'undefined' && response.error) {
			return;
		}

		// replace content with the single body
		$.get( location.href ).success(function(data){
			document.open();
			document.write(data);
			document.close();
		});
	});

});
