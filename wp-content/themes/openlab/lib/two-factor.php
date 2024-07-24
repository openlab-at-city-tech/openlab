<?php

if ( ! function_exists( 'CAC\BP2FA\is_2fa_activated' ) ) {
	return;
}

add_action(
	'bp_core_general_settings_before_submit',
	function() {
		remove_action( 'bp_core_general_settings_before_submit', 'CAC\BP2FA\Settings\output' );
	},
	0
);

add_filter(
	'two_factor_providers',
	function( $providers ) {
		// Remove all providers except 'Two_Factor_Totp'.
		$new_providers = [];
		foreach ( $providers as $provider_name => $provider ) {
			if ( 'Two_Factor_Totp' !== $provider_name ) {
				continue;
			}

			$new_providers[ $provider_name ] = $provider;
		}

		return $new_providers;
	},
	9999
);

function openlab_2fa_settings() {
	require CAC\BP2FA\DIR . '/pluggable.php';

	// Modify user admin settings URL to use BP user settings page.
	$user_settings_page_url = function( $url, $path ) {
		if ( 'user-edit.php' === $path ) {
			return bp_displayed_user_domain() . bp_get_settings_slug() . '/';
		}
		return $url;
	};

	$userdata = get_userdata( bp_displayed_user_id() );

	// Add some filters.
	add_filter( 'self_admin_url', $user_settings_page_url, 10, 2 );

	// Heading and description. Can be removed by wiping out the hook.
	add_action( 'bp_2fa_before_settings_output', function() {
		printf( '<p>%s</p>', esc_html__( 'Two-factor authentication adds an optional, additional layer of security to your account by requiring more than your password to log in.', 'bp-two-factor' ) );
	} );

	/**
	 * Do something before BP 2FA output.
	 */
	do_action( 'bp_2fa_before_settings_output' );

	// Output 2FA options table.
	Two_Factor_Core::user_two_factor_options( $userdata );

	// Remove filters.
	remove_filter( 'self_admin_url', $user_settings_page_url, 10 );

	/**
	 * Do something after BP 2FA output.
	 */
	do_action( 'bp_2fa_after_settings_output' );
}
