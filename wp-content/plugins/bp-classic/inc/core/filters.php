<?php
/**
 * BP Classic Core Filters.
 *
 * @package bp-classic\inc\core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Force BuddyPress directories to use the `page` post type.
 *
 * @since 1.0.0
 *
 * @return string The name of the post type to use for BuddyPress directories.
 */
function bp_classic_get_directory_post_type() {
	return 'page';
}
add_filter( 'bp_core_get_directory_post_type', 'bp_classic_get_directory_post_type', 10, 0 );

/**
 * Force BuddyPress to use the Legacy URL parser.
 *
 * @since 1.0.0
 *
 * @return string The name of the Legacy URL parser.
 */
function bp_classic_use_legacy_parser() {
	return 'legacy';
}
add_filter( 'bp_core_get_query_parser', 'bp_classic_use_legacy_parser', 10, 0 );

/**
 * Fire the 'bp_register_theme_directory' action.
 *
 * The main action used registering theme directories.
 *
 * @since 1.0.0
 */
function bp_register_theme_directory() {
	/**
	 * Fires inside the 'bp_register_theme_directory' function.
	 *
	 * The main action used registering theme directories.
	 *
	 * @since 1.0.0
	 */
	do_action( 'bp_register_theme_directory' );
}
add_action( 'bp_loaded', 'bp_register_theme_directory', 4 );

/**
 * If the BP Classic is symlinked the Theme Root URI might not be set the right way.
 *
 * @since 1.0.0
 *
 * @param string $theme_root_uri         The URI for themes directory.
 * @param string $siteurl                WordPress web address which is set in General Options.
 * @param string $stylesheet_or_template The stylesheet or template name of the theme.
 */
function bp_classic_default_theme_root_uri( $theme_root_uri, $siteurl, $stylesheet_or_template ) {
	if ( 'bp-default' === $stylesheet_or_template && false === strpos( $theme_root_uri, $siteurl ) ) {
		$theme_root_uri = buddypress()->old_themes_url;
	}

	return $theme_root_uri;
}
add_filter( 'theme_root_uri', 'bp_classic_default_theme_root_uri', 10, 3 );

/**
 * Inits Legacy navigation to preserve backward compatibility with BP < 2.6 code.
 *
 * @since 1.0.0
 */
function bp_classic_init_legacy_backcompat_nav() {
	$bp = buddypress();

	// Backward compatibility for plugins modifying the legacy bp_nav and bp_options_nav global properties.
	$bp->bp_nav         = new BP_Classic_Core_Legacy_Nav_BackCompat();
	$bp->bp_options_nav = new BP_Classic_Core_Legacy_Options_Nav_BackCompat();
}
add_action( 'bp_core_setup_globals', 'bp_classic_init_legacy_backcompat_nav', 1 );

/**
 * Resets the Legacy navigation when a nav or subnav item was removed.
 *
 * @since 1.0.0
 */
function bp_classic_reset_legacy_backcompat_nav() {
	$bp = buddypress();

	// Reset backcompat nav items so that subsequent references will be correct.
	$bp->bp_nav->reset();
	$bp->bp_options_nav->reset();
}
add_action( 'bp_core_removed_nav_item', 'bp_classic_reset_legacy_backcompat_nav', 1, 0 );
add_action( 'bp_core_removed_subnav_item', 'bp_classic_reset_legacy_backcompat_nav', 1, 0 );
