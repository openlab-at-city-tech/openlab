<?php

/**
 * Functions responsible for shortcodes management.
 *
 * @since        5.0.5
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */

/**
 * Add a shortcode.
 *
 * @since  5.0.5
 * @param array   $data    New shortcode data.
 * @param boolean $replace Replace existing shortcode or not.
 */
function su_add_shortcode( $data, $replace = true ) {
	return Shortcodes_Ultimate_Shortcodes::add( $data, $replace );
}

/**
 * Remove a shortcode.
 *
 * @since  5.0.5
 * @param string  $id Shortcode ID to remove.
 */
function su_remove_shortcode( $id ) {
	return Shortcodes_Ultimate_Shortcodes::remove( $id );
}

/**
 * Get all shortcodes.
 *
 * @since  5.0.5
 * @return array The collection of available shortcodes.
 */
function su_get_all_shortcodes() {
	return Shortcodes_Ultimate_Shortcodes::get_all();
}

/**
 * Get specific shortcode by ID.
 *
 * @since  5.0.5
 * @param string  $id The ID (without prefix) of shortcode.
 * @return array|boolean   Shortcode data if found, False otherwise.
 */
function su_get_shortcode( $id ) {
	return Shortcodes_Ultimate_Shortcodes::get( $id );
}
