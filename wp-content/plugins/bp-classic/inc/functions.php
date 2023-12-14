<?php
/**
 * BP Classic Funcions.
 *
 * @package bp-classic\inc
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the plugin version.
 *
 * @since 1.0.0
 */
function bp_classic_version() {
	return bp_classic()->version;
}

/**
 * Load translation.
 *
 * @since 1.0.0
 */
function bp_classic_load_translation() {
	$bpc = bp_classic();

	// Load translations.
	load_plugin_textdomain( 'bp-classic', false, trailingslashit( basename( $bpc->dir ) ) . 'languages' );
}
add_action( 'bp_loaded', 'bp_classic_load_translation' );

/**
 * Should BP Classic load Legacy Widgets?
 *
 * @since 1.0.0
 *
 * @return bool False if BuddyPress shouldn't load Legacy Widgets. True otherwise.
 */
function bp_classic_retain_legacy_widgets() {
	$theme_supports = current_theme_supports( 'widgets-block-editor' );

	/** This filter is documented in wp-includes/widgets.php */
	$block_widgets_enabled = $theme_supports && apply_filters( 'use_widgets_block_editor', true );

	$retain_legacy_widgets = true;
	if ( $block_widgets_enabled ) {
		$retain_legacy_widgets = false;
	}

	/**
	 * Filter here to force Legacy Widgets to be retained or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $retain_legacy_widgets False if BuddyPress shouldn't load Legacy Widgets. True otherwise.
	 */
	return apply_filters( 'bp_core_retain_legacy_widgets', $retain_legacy_widgets );
}

/**
 * Returns the path to the `themes` directory.
 *
 * @since 1.0.0
 */
function bp_classic_get_themes_dir() {
	return bp_classic()->themes_dir;
}

/**
 * Returns the url of the `themes` directory.
 *
 * @since 1.0.0
 */
function bp_classic_get_themes_url() {
	return bp_classic()->themes_url;
}
