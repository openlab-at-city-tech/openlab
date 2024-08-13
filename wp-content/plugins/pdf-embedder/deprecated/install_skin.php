<?php

/**
 * Skin class.
 *
 * @since 4.6.3
 */
class WPPDF_Skin extends WP_Upgrader_Skin {


	/**
	 * Primary class constructor.
	 *
	 * @since 4.6.3
	 *
	 * @param array $args Empty array of args (we will use defaults).
	 */
	public function __construct( $args = [] ) {

		parent::__construct();
	}

	/**
	 * Set the upgrader object and store it as a property in the parent class.
	 *
	 * @since 4.6.3
	 *
	 * @param object $upgrader The upgrader object (passed by reference).
	 */
	public function set_upgrader( &$upgrader ) {

		if ( is_object( $upgrader ) ) {
			$this->upgrader =& $upgrader;
		}
	}

	/**
	 * Set the upgrader result and store it as a property in the parent class.
	 *
	 * @since 4.6.3
	 *
	 * @param object $result The result of the install process.
	 */
	public function set_result( $result ) {

		$this->result = $result;
	}

	/**
	 * Empty out the header of its HTML content and only check to see if it has
	 * been performed or not.
	 *
	 * @since 4.6.3
	 */
	public function header() {
	}

	/**
	 * Empty out the footer of its HTML contents.
	 *
	 * @since 4.6.3
	 */
	public function footer() {
	}

	/**
	 * Instead of outputting HTML for errors, json_encode the errors and send them
	 * back to the Ajax script for processing.
	 *
	 * @since 4.6.3
	 *
	 * @param array $errors Array of errors with the install process.
	 */
	public function error( $errors ) {

		if ( ! empty( $errors ) ) {
			echo wp_json_encode( [ 'error' => __( 'There was an error installing the plugin. Please try again.', 'pdf-embedder' ) ] );
			die;
		}
	}

	/**
	 * Hides the `process_failed` error message when updating by uploading a zip file.
	 *
	 * @since 4.6.3
	 *
	 * @param WP_Error $wp_error WP_Error object.
	 *
	 * @return bool
	 */
	public function hide_process_failed( $wp_error ) {

		return true;
	}

	/**
	 * Empty out the feedback method to prevent outputting HTML strings as the install
	 * is progressing.
	 *
	 * @since 4.6.3
	 *
	 * @param string $string  The feedback string.
	 * @param array  ...$args The args.
	 */
	public function feedback( $string, ...$args ) {
	}
}
