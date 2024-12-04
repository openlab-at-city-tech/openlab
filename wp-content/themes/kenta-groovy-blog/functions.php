<?php
/**
 * Theme functions
 *
 * @package Kenta Groovy Blog
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'KENTA_GROOVY_BLOG_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'KENTA_GROOVY_BLOG_VERSION', '1.0.0' );
}

if ( ! defined( 'KENTA_GROOVY_BLOG_PATH' ) ) {
	define( 'KENTA_GROOVY_BLOG_PATH', trailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'KENTA_GROOVY_BLOG_URL' ) ) {
	define( 'KENTA_GROOVY_BLOG_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

if ( ! defined( 'KENTA_GROOVY_BLOG_ASSETS_URL' ) ) {
	define( 'KENTA_GROOVY_BLOG_ASSETS_URL', KENTA_GROOVY_BLOG_URL . 'assets/' );
}

// Helper functions
require_once KENTA_GROOVY_BLOG_PATH . 'helpers.php';
// Customizer settings hook
require_once KENTA_GROOVY_BLOG_PATH . 'setup.php';
// Theme patterns
require_once KENTA_GROOVY_BLOG_PATH . 'patterns.php';
// Customizer settings hook
require_once KENTA_GROOVY_BLOG_PATH . 'customizer.php';
