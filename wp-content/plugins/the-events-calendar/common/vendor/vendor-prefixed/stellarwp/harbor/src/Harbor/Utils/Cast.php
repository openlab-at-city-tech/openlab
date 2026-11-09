<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Utils;

/**
 * Safe type casting utilities.
 *
 * @since 1.0.0
 */
class Cast {

	/**
	 * Safely casts a value to a string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return string
	 */
	public static function to_string( $value ): string {
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( ! is_scalar( $value ) ) {
			return '';
		}

		return strval( $value );
	}

	/**
	 * Safely casts a value to an int.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return int
	 */
	public static function to_int( $value ): int {
		if ( is_int( $value ) ) {
			return $value;
		}

		if ( ! is_scalar( $value ) ) {
			return 0;
		}

		return intval( $value );
	}

	/**
	 * Safely casts a value to a float.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return float
	 */
	public static function to_float( $value ): float {
		if ( is_float( $value ) ) {
			return $value;
		}

		if ( ! is_scalar( $value ) ) {
			return 0.0;
		}

		return floatval( $value );
	}

	/**
	 * Safely casts a value to a bool.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return bool
	 */
	public static function to_bool( $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( ! is_scalar( $value ) ) {
			return false;
		}

		return boolval( $value );
	}
}
