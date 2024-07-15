<?php
/**
 * BuddyPress "Settings > General" integration
 *
 * This code handles two-factor plugin integration into a user's
 * BuddyPress "Settings > General" page. The "General" tab is renamed
 * to "Security" to better describe what is on the page.
 *
 * @package    bp-two-factor
 * @subpackage hooks
 */

namespace CAC\BP2FA\Settings;

use CAC\BP2FA as Loader;
use Two_Factor_Core;

/**
 * Validation routine.
 */
function validate() {
	$user_id  = bp_displayed_user_id();
	$redirect = trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() );

	// Eek. Ensure we add a notice if a save attempt was made.
	add_action( 'updated_user_meta', function( $meta_id, $object_id, $meta_key ) use ( $redirect ) {
		if ( ! isset( $_POST['_nonce_user_two_factor_options'] ) ) {
			return;
		}

		$should_redirect = false;

		// Enabled providers.
		if ( Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY === $meta_key ) {
			$should_redirect = true;

			/*
			 * Check to see if the primary provider changed.
			 *
			 * If primary provider did change, we redirect then.
			 */
			$new_provider = isset( $_POST[ Two_Factor_Core::PROVIDER_USER_META_KEY ] ) ? $_POST[ Two_Factor_Core::PROVIDER_USER_META_KEY ] : '';
			$primary      = get_user_meta( $object_id, Two_Factor_Core::PROVIDER_USER_META_KEY, true );
			if ( '' !== $new_provider && $primary !== $new_provider ) {
				$should_redirect = false;
			}

		// Primary provider.
		} elseif ( Two_Factor_Core::PROVIDER_USER_META_KEY === $meta_key ) {
			$should_redirect = true;
		}

		if ( $should_redirect ) {
			bp_core_add_message( esc_html__( 'Two-factor authentication options updated', 'bp-two-factor' ) );
			bp_core_redirect( $redirect );
		}
	}, 10, 3 );

	// TOTP.
	if ( class_exists( 'Two_Factor_Totp' ) && ! empty( $_POST['totp-changed'] ) ) {
		$totp = \Two_Factor_Totp::get_instance();

		// Set TOTP as enabled (and primary if blank) during secret key save.
		$enabled_providers_for_user = Two_Factor_Core::get_enabled_providers_for_user( $user_id );

		$_POST[ Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY ] = ! empty( $enabled_providers_for_user ) ? $enabled_providers_for_user : [];
		$_POST[ Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY ][] = 'Two_Factor_Totp';

		// Sanity check.
		$_POST[ Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY ] = array_unique( $_POST[ Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY ] );

		// Primary.
		$_POST[ Two_Factor_Core::PROVIDER_USER_META_KEY ] = Two_Factor_Core::is_user_using_two_factor( $user_id ) ? Two_Factor_Core::get_primary_provider_for_user( $user_id ) : 'Two_Factor_Totp';

		// Save primary and enabled providers again.
		Two_Factor_Core::user_two_factor_options_update( $user_id );

		bp_core_add_message( esc_html__( 'Two-factor authentication options updated', 'bp-two-factor' ) );
		bp_core_redirect( $redirect );
	}

	// U2F.
	// @todo Notice handling?
	if ( class_exists( 'Two_Factor_FIDO_U2F' ) ) {
		// Actions.
		\Two_Factor_FIDO_U2F_Admin::catch_submission( $user_id );
		\Two_Factor_FIDO_U2F_Admin::catch_delete_security_key();
	}

	// Handle 2FA provider custom action saving like resetting TOTP key.
	Two_Factor_Core::trigger_user_settings_action();

	// Handle 2FA options saving.
	Two_Factor_Core::user_two_factor_options_update( bp_displayed_user_id() );
}

// Run validation routine.
validate();

/**
 * Enqueue assets.
 */
function enqueue_assets() {
	// U2F.
	if ( class_exists( 'Two_Factor_FIDO_U2F' ) ) {
		/*
		 * Override 2FA fido-u2f-admin as it relies on the WP admin form.
		 *
		 * This will be removed once fixed upstream.
		 */
		wp_register_script(
			'fido-u2f-admin',
			plugins_url( 'assets/fido-u2f-admin.js', Loader\FILE ),
			array( 'jquery', 'fido-u2f-api' ),
			'20201026',
			true
		);

		wp_enqueue_style( 'list-tables' );

		\Two_Factor_FIDO_U2F_Admin::enqueue_assets( 'profile.php' );
	}

	// WebAuthn.
	if ( class_exists( '\WildWolf\WordPress\TwoFactorWebAuthn\Admin' ) ) {
		$webauthn = \WildWolf\WordPress\TwoFactorWebAuthn\Admin::instance();
		$webauthn->admin_enqueue_scripts( 'profile.php' );
	}

	// CSS
	wp_enqueue_style( 'bp-2fa', plugins_url( 'assets/settings.css', Loader\FILE ), [ 'dashicons' ], '20240625' );
	wp_add_inline_style( 'bp-2fa', '
		#security-keys-section .spinner {background-image: url(' . admin_url( '/images/spinner.gif' ) . ')}
	' );


	// JS
	wp_enqueue_script( 'bp-2fa', plugins_url( 'assets/settings.js', Loader\FILE ), [ 'jquery', 'wp-api' ], '20240625', true );
	wp_localize_script( 'bp-2fa', 'bp2fa', [
		'security_key_desc' => sprintf( '<p>%s</p>', esc_html__( "To register your security key, click on the button below and plug your key into your device's USB port when prompted.", 'bp-two-factor' ) ),
		'security_key_webauthn_desc' => sprintf( '<p>%s</p>', esc_html__( "To register your WebAuthn security key, enter a key name. Next, click on the \"Register New Key\" button below and plug your key into your device's USB port when prompted.", 'bp-two-factor' ) ),
	//	'backup_codes_count' => \Two_Factor_Backup_Codes::codes_remaining_for_user( buddypress()->displayed_user->userdata ),
		'backup_codes_misplaced' => sprintf( '<p>%s</p>', esc_html__( "If you misplaced your recovery codes, you can generate a new set of recovery codes below. Please note that your old codes will no longer work.", 'bp-two-factor' ) ),
		'backup_codes_generate' => sprintf( '<p>%s</p>', esc_html__( "Click on the button below to generate your recovery codes.", 'bp-two-factor' ) ),
		'recovery_codes_desc' => sprintf( '<p>%s</p>', esc_html__( "Recovery codes can be used to access your account if you lose access to your device and cannot receive two-factor authentication codes.", 'bp-two-factor' ) ),
		'totp_key' => sprintf( '<strong>%s</strong> ', esc_html__( 'Key:', 'bp-two-factor' ) )
	] );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Output 2FA options.
 */
function output() {
	require Loader\DIR . '/pluggable.php';

	// Modify user admin settings URL to use BP user settings page.
	$user_settings_page_url = function( $url, $path ) {
		if ( 'user-edit.php' === $path ) {
			return bp_displayed_user_domain() . bp_get_settings_slug() . '/';
		}
		return $url;
	};

	$userdata = get_userdata( bp_displayed_user_id() );

	// Add some filters.
	add_filter( 'self_admin_url', $user_settings_page_url, 10, 3 );

	// Heading and description. Can be removed by wiping out the hook.
	add_action( 'bp_2fa_before_settings_output', function() {
		printf( '<h3 id="two-factor-heading">%s</h3>', esc_html__( 'Two-factor Authentication', 'bp-two-factor' ) );

		printf( '<p>%s</p>', esc_html__( 'Two-factor authentication adds an optional, additional layer of security to your account by requiring more than your password to log in. Configure these additional methods below.', 'bp-two-factor' ) );
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
add_action( 'bp_core_general_settings_before_submit', __NAMESPACE__ . '\\output' );
