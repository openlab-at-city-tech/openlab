/** This file will show notice about js errors */
window.epkbErrorsList = []; 
var showNoticeTimeout = true;
var previousErrorHandler = window.onerror;
var jqueryChecked = 0;

// create new errors handler - fires when an error occurs during object loading
window.onerror = function( errorMsg, url, lineNumber, columnNumber, errorObject ) {
	
	/* Firefox bug */
	if ( errorObject.name != 'NS_ERROR_FAILURE' ) {
		epkbErrorsList.push({ 'msg' : errorMsg, 'url' : url });
	}

	if ( showNoticeTimeout ) {
		setTimeout(epkbShowErrorNotices, 2000);
		showNoticeTimeout = false;
	}
	
	if ( typeof jQuery !== 'undefined' ) {
		jQuery(document).trigger( 'epkb_js_error', [errorMsg, url, lineNumber, columnNumber, errorObject] );
	}
	
	// run previous Window errors handler possibly used by other plugins if it exists
	if ( previousErrorHandler ) {
		return previousErrorHandler( errorMsg, url, lineNumber, columnNumber, errorObject );
	}
	
	// run default handler 
	return false;
};

function epkbShowErrorNotices() {
	
	// wait for jquery
	if ( typeof jQuery == 'undefined' || jQuery('.epkb-js-error-notice').length == 0 ) {
		setTimeout( epkbShowErrorNotices, 1000 );
		if ( jqueryChecked > 20 ) {
			return;	// prevent infinite loop
		}
		jqueryChecked++;
		return;
	}
	
	// hide previous message 
	jQuery('.epkb-js-error-notice').hide('fast');

	let error;
	for (error of epkbErrorsList) {
		// we will show only last error in this case
		jQuery('.epkb-js-error-notice').find('.epkb-js-error-msg').text(error.msg);
		jQuery('.epkb-js-error-notice').find('.epkb-js-error-url').text(error.url);
		jQuery('.epkb-js-error-notice').show('fast');

	}

	jQuery('.epkb-js-error-close').on('click',function(){
		jQuery(this).closest('.epkb-js-error-notice').hide('fast');
	});
}