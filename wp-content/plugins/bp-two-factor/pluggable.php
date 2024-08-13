<?php
/**
 * Admin pluggable functions
 *
 * These functions are required for the two-factor plugin to work
 * properly on the frontend. This is primarily for the U2F
 * Security Keys table to display without fatal errors.
 *
 * @package    bp-two-factor
 * @subpackage pluggable
 */

// Sneakily impersonate wp_screen() to prevent fatals.
if ( ! function_exists( 'convert_to_screen' ) ) {
	function convert_to_screen() {
		$screen = new CAC_Dummy_Admin_Profile_Screen();
		return $screen;
	}
}

// get_column_headers() needs to be declared as well.
if ( ! function_exists( 'get_column_headers' ) ) {
	function get_column_headers( $screen ) {
		if ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}

		static $column_headers = array();

		if ( ! isset( $column_headers[ $screen->id ] ) ) {
			/** This filter is documented in /wp-admin/includes/screen.php */
			$column_headers[ $screen->id ] = apply_filters( "manage_{$screen->id}_columns", array() );
		}

		return $column_headers[ $screen->id ];
	}
}

// get_hidden_columns() needs to be declared as well.
if ( ! function_exists( 'get_hidden_columns' ) ) {
	function get_hidden_columns( $screen ) {
		if ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}

		/** This filter is documented in /wp-admin/includes/screen.php */
		$hidden = get_user_option( 'manage' . $screen->id . 'columnshidden' );

		$use_defaults = ! is_array( $hidden );

		if ( $use_defaults ) {
			$hidden = array();

			/** This filter is documented in /wp-admin/includes/screen.php */
			$hidden = apply_filters( 'default_hidden_columns', $hidden, $screen );
		}

		/** This filter is documented in /wp-admin/includes/screen.php */
		return apply_filters( 'hidden_columns', $hidden, $screen, $use_defaults );
	}
}

/**
 * Dummy class to impersonate wp_screen().
 */
class CAC_Dummy_Admin_Profile_Screen {
	public $id   = 'profile';
	public $base = 'profile';

	public function render_screen_reader_content( $type = '' ) {}
}