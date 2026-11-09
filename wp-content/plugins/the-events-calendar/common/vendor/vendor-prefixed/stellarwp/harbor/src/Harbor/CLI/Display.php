<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\CLI;

/**
 * Display formatting helpers for CLI table output.
 *
 * @since 1.0.0
 */
class Display {

	/**
	 * Converts a boolean to a display-friendly 'true'/'false' string.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $value The boolean value.
	 *
	 * @return string
	 */
	public static function bool( bool $value ): string {
		return $value ? 'true' : 'false';
	}

	/**
	 * Converts a nullable boolean to a display-friendly string.
	 *
	 * Returns 'true'/'false' for boolean values, or an empty string for null.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|null $value The nullable boolean value.
	 *
	 * @return string
	 */
	public static function nullable_bool( ?bool $value ): string {
		if ( $value === null ) {
			return '';
		}

		return $value ? 'true' : 'false';
	}
}
