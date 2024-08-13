<?php
/*
Plugin Name: CM Tooltip Glossary
Plugin URI: https://www.cminds.com/
Description: Easily create a Glossary, Encyclopedia or Dictionary of your custom terms. Plugin parses posts and pages searching for defined glossary terms and adds links to the glossary term page. Hovering over the link shows a tooltip with the definition.
Version: 4.3.7
Text Domain: enchanced-tooltipglossary
Author: CreativeMindsSolutions
Author URI: https://www.cminds.com/
*/


/**
 * Define Plugin Version
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_VERSION' ) ) {
	define( 'CMTT_VERSION', '4.3.7' );
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_NAME' ) ) {
	define( 'CMTT_NAME', 'CM Tooltip Glossary' );
}

/**
 * Define Plugin canonical name
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_CANONICAL_NAME' ) ) {
	define( 'CMTT_CANONICAL_NAME', 'CM Tooltip Glossary' );
}

/**
 * Define Plugin license name
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_LICENSE_NAME' ) ) {
	define( 'CMTT_LICENSE_NAME', 'CM Tooltip Glossary' );
}

/**
 * Define Plugin File Name
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_PLUGIN_FILE' ) ) {
	define( 'CMTT_PLUGIN_FILE', __FILE__ );
}

/**
 * Define Plugin URL
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_URL' ) ) {
	define( 'CMTT_URL', 'https://www.cminds.com/wordpress-plugins-library/tooltipglossary/' );
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if ( !defined( 'CMTT_RELEASE_NOTES' ) ) {
	define( 'CMTT_RELEASE_NOTES', 'https://www.cminds.com/wordpress-plugins-library/cm-tooltip-glossary-changelog/' );
}

require_once plugin_dir_path( __FILE__ ) . "glossaryFree.php";
register_activation_hook( __FILE__, array( 'CMTT_Free', '_install' ) );

add_action('cmtt_include_files_after', function( ) {
	require_once plugin_dir_path( __FILE__ ). 'package/cminds-free.php';
});

CMTT_Free::init();