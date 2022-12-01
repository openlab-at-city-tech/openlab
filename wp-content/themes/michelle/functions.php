<?php
/**
 * Loading theme functionality.
 *
 * @link  https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Check that the site meets the minimum requirements:

	define( 'MICHELLE_WP_VERSION', '5.5' );
	define( 'MICHELLE_PHP_VERSION', '7.0' );

	if (
		version_compare( $GLOBALS['wp_version'], MICHELLE_WP_VERSION, '<' )
		|| version_compare( PHP_VERSION, MICHELLE_PHP_VERSION, '<' )
	) {
		require_once get_template_directory() . '/includes/Compatibility.php';
		return;
	}

// Constants:

	define( 'MICHELLE_THEME_VERSION', wp_get_theme( 'michelle' )->get( 'Version' ) );

	define( 'MICHELLE_PATH', trailingslashit( get_template_directory() ) );
		define( 'MICHELLE_PATH_INCLUDES', MICHELLE_PATH . 'includes/' );
		define( 'MICHELLE_PATH_VENDOR',   MICHELLE_PATH . 'vendor/' );

	if ( ! defined( 'MICHELLE_ENQUEUE_PRIORITY' ) ) {
		/**
		 * Theme assets enqueue priority.
		 *
		 * To rise the priority use:
		 * =========================
		 * + 1...9 for core theme assets setup,
		 * + 10...98 for additional assets setup,
		 * + 99 for modifications, such as deregistering and dequeuing.
		 */
		define( 'MICHELLE_ENQUEUE_PRIORITY', 11 );
	}

// Load theme functionality.
require_once MICHELLE_PATH_INCLUDES . 'Autoload.php';
WebManDesign\Michelle\Theme::init();
