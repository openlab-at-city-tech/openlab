<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Installer\Utils;

class Array_Utils {
	/**
	 * Find a value inside of an array or object, including one nested a few levels deep.
	 *
	 * Example: get( $a, [ 0, 1, 2 ] ) returns the value of $a[0][1][2] or the default.
	 *
	 * @since 1.0.0
	 *
	 * @param array|object $variable Array or object to search within.
	 * @param array|string $indexes Specify each nested index in order.
	 *                                Example: array( 'lvl1', 'lvl2' );
	 * @param mixed        $default Default value if the search finds nothing.
	 *
	 * @return mixed The value of the specified index or the default if not found.
	 */
	public static function get( $variable, $indexes, $default = null ) {
		if ( is_object( $variable ) ) {
			$variable = (array) $variable;
		}

		if ( ! is_array( $variable ) ) {
			return $default;
		}

		foreach ( (array) $indexes as $index ) {
			if ( ! is_array( $variable ) || ! isset( $variable[ $index ] ) ) {
				$variable = $default;
				break;
			}

			$variable = $variable[ $index ];
		}

		return $variable;
	}

	/**
	 * Find a value inside a list of array or objects, including one nested a few levels deep.
	 *
	 * @since 1.0.0
	 *
	 * Example: get( [$a, $b, $c], [ 0, 1, 2 ] ) returns the value of $a[0][1][2] found in $a, $b or $c
	 * or the default.
	 *
	 * @param array        $variables Array of arrays or objects to search within.
	 * @param array|string $indexes Specify each nested index in order.
	 *                                 Example: array( 'lvl1', 'lvl2' );
	 * @param mixed        $default Default value if the search finds nothing.
	 *
	 * @return mixed The value of the specified index or the default if not found.
	 */
	public static function get_in_any( array $variables, $indexes, $default = null ) {
		foreach ( $variables as $variable ) {
			$found = self::get( $variable, $indexes, '__not_found__' );
			if ( '__not_found__' !== $found ) {
				return $found;
			}
		}

		return $default;
	}

	/**
	 * Sanitizes a value according to its type.
	 *
	 * The function will recursively sanitize array values.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value, or values, to sanitize.
	 *
	 * @return mixed|null Either the sanitized version of the value, or `null` if the value is not a string, number or
	 *                    array.
	 */
	public static function sanitize_deep( &$value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_string( $value ) ) {
			$value = filter_var( $value, FILTER_SANITIZE_STRING );
			return $value;
		}
		if ( is_int( $value ) ) {
			$value = filter_var( $value, FILTER_VALIDATE_INT );
			return $value;
		}
		if ( is_float( $value ) ) {
			$value = filter_var( $value, FILTER_VALIDATE_FLOAT );
			return $value;
		}

		if ( is_array( $value ) ) {
			array_walk( $value, [ __CLASS__, 'sanitize_deep' ] );
			return $value;
		}

		return null;
	}
}
