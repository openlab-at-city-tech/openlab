<?php // phpcs:ignore
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Patterns Docs functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Patterns Docs
 */

/**
 * Current theme path.
 * Current theme url.
 * Current theme version.
 * Current theme name.
 * Current theme option name.
 */
define( 'PATTERNS_DOCS_PATH', trailingslashit( get_template_directory() ) );
define( 'PATTERNS_DOCS_URL', trailingslashit( get_template_directory_uri() ) );
define( 'PATTERNS_DOCS_VERSION', '1.0.0' );
define( 'PATTERNS_DOCS_THEME_NAME', 'patterns-docs' );
define( 'PATTERNS_DOCS_OPTION_NAME', 'patterns-docs' );

/**
 * The core theme class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require PATTERNS_DOCS_PATH . 'includes/main.php';

/**
 * Begins execution of the theme.
 *
 * @since    1.0.0
 */
function patterns_docs_run() {
	new Patterns_Docs();
}
patterns_docs_run();
