<?php
/**
 * String overrides for the two-factor plugin
 *
 * This code overrides some strings used by the two-factor plugin to
 * better describe certain actions and descriptions.
 *
 * @package    bp-two-factor
 * @subpackage strings
 */

namespace CAC\BP2FA\Strings;

add_filter( 'gettext_two-factor', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
add_filter( 'gettext_with_context_two-factor', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
add_filter( 'gettext_two-factor-provider-webauthn', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
add_filter( 'gettext_with_context_two-factor-provider-webauthn', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
add_filter( 'ngettext_two-factor', __NAMESPACE__ . '\\ngettext_overrides', 10, 4 );

/**
 * Override strings for the two-factor plugin.
 *
 * @param  string $retval       Translated string.
 * @param  string $untranslated Untranslated string.
 * @return string
 */
function gettext_overrides( $retval, $untranslated ) {
	switch ( $untranslated ) {
		case 'Name' :
			//return esc_html__( 'Type', 'bp-two-factor' );
			break;

		case 'Please scan the QR code or manually enter the key, then enter an authentication code from your app in order to complete setup.' :
			return esc_html__( 'Please scan the QR code or manually enter the key into your authenticator app. Next, enter the authentication code from your app to complete set up.', 'bp-two-factor' );
			break;

		case 'Invalid Two Factor Authentication code.' :
			return esc_html__( 'Invalid code. Please ensure you have correctly entered the code from your authenticator app.', 'bp-two-factor' );
			break;

		case 'Submit' :
			return esc_html__( 'Complete Set Up', 'bp-two-factor' );
			break;

		case 'FIDO U2F Security Keys' :
			return esc_html__( 'Security Keys', 'bp-two-factor' );
			break;

		case 'Requires an HTTPS connection. Configure your security keys in the "Security Keys" section below.' :
			return esc_html__( 'Security keys are hardware devices that can be used as your second factor of authentication. To configure your security keys, click on the "Enabled" checkbox and view the "Security Keys" section below.', 'bp-two-factor' );
			break;

		case 'Requires an HTTPS connection. Please configure your security keys in the <a href="#webauthn-security-keys-section">Security Keys (WebAuthn)</a> section below.' :
			return esc_html__( 'WebAuthn can be used as your second factor of authentication. To configure your WebAuthn security keys, click on the "Enabled" checkbox and view the "Security Keys (WebAuthn)" section below.', 'bp-two-factor' );
			break;

		case 'An authenticator app is currently configured. You will need to re-scan the QR code on all devices if reset.' :
			return esc_html__( 'An authenticator app is currently configured. If you misplaced your authenticator app, you can reset and restart the process below. If you used the previous QR code or key on other devices, they will also need to be updated in order to continue working.', 'bp-two-factor' );
			break;

		case 'Backup Verification Codes (Single Use)' :
			return esc_html__( 'Recovery Codes', 'bp-two-factor' );
			break;

		case 'Generate Verification Codes' :
			return esc_html__( 'Generate New Recovery Codes', 'bp-two-factor' );
			break;

		// Removing this string since it sounds like we're upselling something.
		// This text just links to a Google support article anyway...
		case 'You can find FIDO U2F Security Key devices for sale from here.' :
			return '';
			break;
	}

	return $retval;
}

/**
 * Override context strings for the two-factor plugin.
 *
 * @param  string $retval Translated string.
 * @param  string $single Untranslated string.
 * @param  string $plural
 * @param  int    $number
 * @return string
 */
function ngettext_overrides( $retval, $single, $plural, $number ) {
	if ( '%s unused code remaining.' === $single ) {
		return _n( 'You previously generated some prior codes and have %s unused code left.', 'You previously generated some prior codes and have %s unused codes left.', $number, 'bp-two-factor' );
	}

	return $retval;
}