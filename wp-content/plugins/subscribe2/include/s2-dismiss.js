/* global s2DismissScriptStrings */
// Version 1.0 - original version
// Version 1.1 - eslinted

jQuery( document ).on(
	'click',
	'#sender_message .notice-dismiss',
	function() {
		var ajaxurl = s2DismissScriptStrings.ajaxurl;
		var data    = {
			'action': 's2_dismiss_notice',
			'nonce': s2DismissScriptStrings.nonce

		};
		jQuery.post( ajaxurl, data );
	}
);
