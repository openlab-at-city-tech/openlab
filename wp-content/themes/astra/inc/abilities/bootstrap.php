<?php
/**
 * Astra Abilities API Bootstrap
 *
 * Loads the Abilities API integration and initializes
 * the Astra abilities registration.
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize Astra Abilities.
 *
 * Boots the Astra_Abilities_Init class which registers all Astra abilities
 * when the Abilities API is available (WordPress 6.9+ or via plugin polyfill).
 *
 * @return void
 */
function astra_abilities_init() {
	// Check if abilities are enabled in the dashboard settings.
	if ( ! Astra_API_Init::get_admin_settings_option( 'enable_abilities', false ) ) {
		return;
	}

	$abilities_dir = ASTRA_THEME_DIR . 'inc/abilities/';

	// Load base classes.
	require_once $abilities_dir . 'class-astra-abilities-response.php';
	require_once $abilities_dir . 'class-astra-abstract-ability.php';
	require_once $abilities_dir . 'class-astra-abilities-helper.php';
	require_once $abilities_dir . 'class-astra-abilities-init.php';

	// Initialize abilities registration.
	Astra_Abilities_Init::get_instance();
}

// Initialize after theme setup so astra_get_option() is available.
add_action( 'after_setup_theme', 'astra_abilities_init' );
