<?php
/**
 * Helper functions
 */

/**
 * Validates a code
 */
function cac_ncs_validate_code( $code = false ) {
	return OpenLab\SignupCodes\Schema::is_code_valid( $code );
}

/**
 * Verify that an email address is from a whitelisted domain.
 */
function cac_ncs_email_domain_is_in_whitelist( $email ) {
	$domains = array(
		'mail.citytech.cuny.edu',
		'citytech.cuny.edu',
	);

	$email = explode( '@', trim( $email ) );

	if ( ! isset( $email[1] ) || ! in_array( $email[1], $domains, true ) ) {
		return false;
	}

	return true;
}
