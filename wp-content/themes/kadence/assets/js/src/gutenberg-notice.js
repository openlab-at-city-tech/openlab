/**
 * Ajax install the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";
	$(function(){
		$( '#kadence-notice-gutenberg-plugin .notice-dismiss' ).on( 'click', function( event ) {
			kadence_dismissGutenbergNotice();
		} );
		function kadence_dismissGutenbergNotice(){
			var data = new FormData();
			data.append( 'action', 'kadence_dismiss_gutenberg_notice' );
			data.append( 'security', kadenceGutenbergDeactivate.ajax_nonce );
			$.ajax({
				url : kadenceGutenbergDeactivate.ajax_url,
				method:  'POST',
				data: data,
				contentType: false,
				processData: false,
			});
		}
	});
})(jQuery, window, document);