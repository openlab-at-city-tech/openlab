<?php

namespace TEC\Common\StellarWP\Uplink\Utils;

use TEC\Common\StellarWP\Uplink\Config;

class Checks {
	/**
	 * Determines if the provided value should be regarded as 'true'.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $var
	 *
	 * @return bool
	 */
	public static function is_truthy( $var ) {
		if ( is_bool( $var ) ) {
			return $var;
		}

		/**
		 * Provides an opportunity to modify strings that will be
		 * deemed to evaluate to true.
		 *
		 * @param array $truthy_strings
		 */
		$truthy_strings = (array) apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/is_truthy_strings', [
			'1',
			'enable',
			'enabled',
			'on',
			'y',
			'yes',
			'true',
		] );

		// Makes sure we are dealing with lowercase for testing
		if ( is_string( $var ) ) {
			$var = strtolower( $var );
		}

		// If $var is a string, it is only true if it is contained in the above array
		if ( in_array( $var, $truthy_strings, true ) ) {
			return true;
		}

		// All other strings will be treated as false
		if ( is_string( $var ) ) {
			return false;
		}

		// For other types (ints, floats etc) cast to bool
		return (bool) $var;
	}

	/**
	 * String Starts With PHP80 polyfill.
	 *
	 * @param  string  $haystack  The string to search in.
	 * @param  string  $needle  The substring to search for in the haystack.
	 *
	 * @return bool Returns true if haystack begins with needle, false otherwise.
	 */
	public static function str_starts_with( string $haystack, string $needle ): bool {
		if ( function_exists( 'str_starts_with' ) ) {
			return str_starts_with( $haystack, $needle );
		}

		return 0 === strncmp( $haystack, $needle, strlen( $needle ) );
	}

}
