<?php

namespace Imagely\NGG\Util;

/**
 * Utility class for URL and request parameter handling.
 *
 * Provides methods for safely accessing and validating URL parameters and request data.
 */
class URL {

	/**
	 * Gets the specified superglobal array by name.
	 *
	 * @param string $source_name The name of the source ('request', 'get', 'post', 'server').
	 * @return array|null The superglobal array or null if not found.
	 */
	public static function get_source( $source_name ) {
		// Nonce checks are not necessary: nothing is happening here, only the mapping of string to variable.
		//
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( 'request' === $source_name ) {
			return $_REQUEST;
		} elseif ( 'get' === $source_name ) {
			return $_GET;
		} elseif ( 'post' === $source_name ) {
			return $_POST;
		} elseif ( 'server' === $source_name ) {
			return $_SERVER;
		}

        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        // phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Gets a parameter value from the specified source with validation.
	 *
	 * @param string $name              The parameter name to retrieve.
	 * @param string $source            The source to get the parameter from. Default 'request'.
	 * @param string $validation_method The validation function to apply. Default 'sanitize_text_field'.
	 * @return mixed|null The validated parameter value or null if not found.
	 */
	public static function param( string $name, string $source = 'request', string $validation_method = 'sanitize_text_field' ) {
		if ( ! self::has_param( $name ) ) {
			return null;
		}

		$source = self::get_source( $source );
		return $validation_method( wp_unslash( $source[ $name ] ) );
	}

	/**
	 * Checks if a parameter exists in the specified source.
	 *
	 * @param string $name   The parameter name to check.
	 * @param string $source The source to check in. Default 'request'.
	 * @return bool True if the parameter exists, false otherwise.
	 */
	public static function has_param( string $name, string $source = 'request' ): bool {
		$source = self::get_source( $source );
		return isset( $source[ $name ] );
	}
}
