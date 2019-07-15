<?php
/**
 * Multipage Admin Actions.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init',               'mpp_admin_init'                   );

// Hook on to admin_init.
add_action( 'mpp_admin_init',			'mpp_setup_updater',          1000 );
add_action( 'mpp_admin_init',			'mpp_register_admin_settings'      );

/**
 * Piggy back admin_init action.
 *
 * @since 1.4
 *
 */
function mpp_admin_init() {

	/**
	 * Fires inside the mpp_admin_init function.
	 *
	 * @since 1.4
	 */
	do_action( 'mpp_admin_init' );
}

/**
 * Dedicated action to register admin settings.
 *
 * @since 1.4
 *
 */
function mpp_register_admin_settings() {

	/**
	 * Fires inside the register_admin_settings function.
	 *
	 * @since 1.4
	 */
	do_action( 'mpp_register_admin_settings' );
}