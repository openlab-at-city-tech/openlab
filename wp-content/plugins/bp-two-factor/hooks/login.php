<?php
/**
 * Login page integration
 *
 * Handles altering a few strings from the two-factor plugin on the login
 * page.
 *
 * @package    bp-two-factor
 * @subpackage hooks
 */

namespace CAC\BP2FA\Login;

/**
 * Alter a few strings from the 2FA plugin on the login page.
 *
 * @param  string $retval       Translated string.
 * @param  string $untranslated Untranslated string.
 * @return string
 */
function gettext_overrides( $retval, $untranslated ) {
	switch ( $untranslated ) {
		case 'Backup Verification Codes (Single Use)' :
			return esc_html__( 'Recovery Codes', 'bp-two-factor' );
			break;

		case 'FIDO U2F Security Keys' :
			return esc_html__( 'Security Keys', 'bp-two-factor' );
			break;
	}

	return $retval;
};

add_filter( 'gettext_with_context_two-factor', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
//add_filter( 'gettext_two-factor', __NAMESPACE__ . '\\gettext_overrides', 10, 2 );
