/* global jQuery */
(function($){
	$(document).ready(function(){
		if ( ! window.hasOwnProperty( 'passwordExpirationWarningNotice' ) ) {
			return
		}

		const { dismissUrl, expiration, resetUrl } = window.passwordExpirationWarningNotice;

		const expirationDate = new Date( expiration * 1000 );
		const now = new Date();

		const daysUntilExpiration = Math.floor( ( expirationDate - now ) / ( 1000 * 60 * 60 * 24 ) );

		const dayDeclined = daysUntilExpiration === 1 ? 'day' : 'days';

		// Date formatted as F j, Y.
		const formattedExpirationDate = expirationDate.toLocaleDateString( 'en-US', {
			year: 'numeric',
			month: 'long',
			day: 'numeric',
		} );

		const expirationMessage = 'Your password will expire in ' + daysUntilExpiration + ' ' + dayDeclined + ', on ' + formattedExpirationDate + '.&nbsp;<a href="' + resetUrl + '">Reset your password now</a>.';

		const warningEl = document.createElement( 'div' );
		warningEl.classList.add( 'password-expiration-warning-notice' );

		const warningElContent = document.createElement( 'span' );
		warningElContent.innerHTML = expirationMessage;
		warningEl.appendChild( warningElContent );

		const dismissButton = document.createElement( 'button' );
		dismissButton.classList.add( 'notice-dismiss', 'fa', 'fa-times' );

		const dismissButtonText = document.createElement( 'span' );
		dismissButtonText.classList.add( 'screen-reader-text' );
		dismissButtonText.innerHTML = 'Dismiss';

		dismissButton.appendChild( dismissButtonText );
		warningEl.appendChild( dismissButton );

		// insert after #wpadminbar
		const wpAdminBar = document.getElementById( 'wpadminbar' );
		wpAdminBar.parentNode.insertBefore( warningEl, wpAdminBar.nextSibling );

		// The top position should be the same as the admin bar plus the height of the admin bar.
		const adminBarHeight = wpAdminBar.offsetHeight;
		const adminBarTop = wpAdminBar.offsetTop;
		warningEl.style.top = ( adminBarTop + adminBarHeight ) + 'px';
		warningEl.style.position = 'absolute';

		document.body.classList.add( 'has-password-expiration-warning-notice' );

		$( warningEl ).on( 'click', '.notice-dismiss', function() {
			$( warningEl ).remove();
			document.body.classList.remove( 'has-password-expiration-warning-notice' );

			$.ajax( {
				url: dismissUrl,
				type: 'GET'
			} );

		} );

	});
})(jQuery);
