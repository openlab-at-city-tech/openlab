<?php
/**
 * Network-wide two-factor policy.
 */

/**
 * Restricts two-factor authentication to the authenticator app.
 *
 * @param array $providers Provider file paths indexed by provider class name.
 * @return array
 */
function openlab_two_factor_providers( $providers ) {
	return array_intersect_key( $providers, [ 'Two_Factor_Totp' => true ] );
}
add_filter( 'two_factor_providers', 'openlab_two_factor_providers', 9999 );
