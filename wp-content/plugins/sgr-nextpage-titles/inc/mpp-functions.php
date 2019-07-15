<?php
/**
 * Multipage Common Functions.
 *
 * @package Multipage
 * @subpackage Functions
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** Versions ******************************************************************/

/**
 * Output the Multipage version.
 *
 * @since 1.4
 *
 */
function mpp_version() {
	echo mpp_get_version();
}
	/**
	 * Return the Multipage version.
	 *
	 * @since 1.4
	 *
	 * @return string The Multipage version.
	 */
	function mpp_get_version() {
		return multipage()->version;
	}

/**
 * Output the Multipage database version.
 *
 * @since 1.4
 *
 */
function mpp_db_version() {
	echo mpp_get_db_version();
}
	/**
	 * Return the Multipage database version.
	 *
	 * @since 1.4
	 *
	 * @return string The Multipage database version.
	 */
	function mpp_get_db_version() {
		return multipage()->db_version;
	}
	
/**
 * Output the Multipage database version.
 *
 * @since 1.4
 *
 */
function mpp_db_version_raw() {
	echo mpp_get_db_version_raw();
}
	/**
	 * Return the Multipage database version.
	 *
	 * @since 1.4
	 *
	 * @return string The Multipage version direct from the database.
	 */
	function mpp_get_db_version_raw() {
		$mpp = multipage();
		return !empty( $mpp->db_version_raw ) ? $mpp->db_version_raw : 0;
	}
	
/** Miscellaneous hooks *******************************************************/

/**
 * Load the Multipage translation file for current language.
 *
 * @since 1.4
 *
 * @see load_textdomain() for a description of return values.
 *
 * @return bool True on success, false on failure.
 */
function mpp_load_multipage_textdomain() {
	$domain = 'sgr-nextpage-titles';

	// WP and glotpress.
	return load_plugin_textdomain( $domain );
}
add_action( 'init', 'mpp_load_multipage_textdomain' );
