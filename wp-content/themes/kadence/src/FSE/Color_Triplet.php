<?php
/**
 * Kadence\FSE\Color_Triplet class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Turns a CSS color into the `r, g, b` triplet that `rgba( var( --global-palette9rgb ), .35 )` needs.
 *
 * The Site Editor accepts any CSS color for a palette slot, and the Customizer only ever held
 * hex. This reads hex, `rgb()`, `hsl()` and `oklch()`, with or without an alpha channel, which
 * is ignored. A color it can't compute in PHP, such as a named color or a relative color
 * syntax, returns null.
 *
 * @since 1.6.0
 */
final class Color_Triplet {

	/**
	 * Converts a color.
	 *
	 * @since 1.6.0
	 *
	 * @param string|null $color A CSS color.
	 * @return string|null Such as `16, 32, 48`, or null when the color can't be computed.
	 */
	public static function from( ?string $color ): ?string {
		if ( null === $color ) {
			return null;
		}

		$color = strtolower( trim( $color ) );
		$rgb   = null;

		if ( 1 === preg_match( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/', $color, $m ) ) {
			$rgb = self::hex( $m[1] );
		} elseif ( 1 === preg_match( '/^(rgba?|hsla?|oklch)\(([^()]*)\)$/', $color, $m ) ) {
			$args = self::arguments( $m[2] );

			if ( null !== $args ) {
				$rgb = 'oklch' === $m[1] ? self::oklch( $args ) : ( 0 === strpos( $m[1], 'hsl' ) ? self::hsl( $args ) : self::rgb( $args ) );
			}
		}

		return null === $rgb ? null : implode( ', ', array_map( 'intval', $rgb ) );
	}

	/**
	 * Reads hex digits.
	 *
	 * @since 1.6.0
	 *
	 * @param string $hex 3, 4, 6 or 8 digits.
	 * @return int[]
	 */
	private static function hex( string $hex ): array {
		if ( strlen( $hex ) <= 4 ) {
			$hex = preg_replace( '/./', '$0$0', $hex );
		}

		return [ hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ];
	}

	/**
	 * Splits a color function's arguments into the three channels, dropping any alpha.
	 *
	 * @since 1.6.0
	 *
	 * @param string $arguments Text between the parentheses.
	 * @return string[]|null Null unless there are three channels, then at most an alpha.
	 */
	private static function arguments( string $arguments ): ?array {
		$parts = preg_split( '/\s*[,\/]\s*|\s+/', trim( $arguments ) );

		return is_array( $parts ) && count( $parts ) >= 3 && count( $parts ) <= 4 ? array_slice( $parts, 0, 3 ) : null;
	}

	/**
	 * A channel as a number, with a percentage scaled to the given full value.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Such as `50`, `50%` or `120deg`.
	 * @param float  $full  What 100% stands for.
	 * @return float|null Null when it isn't a number.
	 */
	private static function number( string $value, float $full ): ?float {
		if ( 1 !== preg_match( '/^([+-]?(?:\d+\.?\d*|\.\d+))(%|deg)?$/', $value, $m ) ) {
			return 'none' === $value ? 0.0 : null;
		}

		return '%' === ( $m[2] ?? '' ) ? (float) $m[1] / 100 * $full : (float) $m[1];
	}

	/**
	 * Reads `rgb()` channels.
	 *
	 * @since 1.6.0
	 *
	 * @param string[] $args Three channels.
	 * @return int[]|null
	 */
	private static function rgb( array $args ): ?array {
		$rgb = [];

		foreach ( $args as $arg ) {
			$value = self::number( $arg, 255 );

			if ( null === $value ) {
				return null;
			}

			$rgb[] = (int) round( max( 0, min( 255, $value ) ) );
		}

		return $rgb;
	}

	/**
	 * Reads `hsl()` channels.
	 *
	 * @since 1.6.0
	 *
	 * @param string[] $args Hue, saturation and lightness.
	 * @return int[]|null
	 */
	private static function hsl( array $args ): ?array {
		$hue        = self::number( $args[0], 360 );
		$saturation = self::number( rtrim( $args[1], '%' ) . '%', 1 );
		$lightness  = self::number( rtrim( $args[2], '%' ) . '%', 1 );

		if ( null === $hue || null === $saturation || null === $lightness ) {
			return null;
		}

		$hue        = fmod( fmod( $hue, 360 ) + 360, 360 );
		$saturation = max( 0, min( 1, $saturation ) );
		$lightness  = max( 0, min( 1, $lightness ) );
		$chroma     = $saturation * min( $lightness, 1 - $lightness );
		$rgb        = [];

		foreach ( [ 0, 8, 4 ] as $offset ) {
			$k     = fmod( $offset + $hue / 30, 12 );
			$rgb[] = (int) round( 255 * ( $lightness - $chroma * max( -1, min( $k - 3, 9 - $k, 1 ) ) ) );
		}

		return $rgb;
	}

	/**
	 * Reads `oklch()` channels and converts them to sRGB, clipping what is out of gamut.
	 *
	 * @since 1.6.0
	 *
	 * @param string[] $args Lightness, chroma and hue.
	 * @return int[]|null
	 */
	private static function oklch( array $args ): ?array {
		$lightness = self::number( $args[0], 1 );
		$chroma    = self::number( $args[1], 0.4 );
		$hue       = self::number( $args[2], 360 );

		if ( null === $lightness || null === $chroma || null === $hue ) {
			return null;
		}

		$a = max( 0, $chroma ) * cos( deg2rad( $hue ) );
		$b = max( 0, $chroma ) * sin( deg2rad( $hue ) );

		$l = ( $lightness + 0.3963377774 * $a + 0.2158037573 * $b ) ** 3;
		$m = ( $lightness - 0.1055613458 * $a - 0.0638541728 * $b ) ** 3;
		$s = ( $lightness - 0.0894841775 * $a - 1.2914855480 * $b ) ** 3;

		$linear = [
			4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
			-1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
			-0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
		];

		return array_map(
			static function ( $value ) {
				$value = max( 0, min( 1, $value ) );
				$value = $value <= 0.0031308 ? 12.92 * $value : 1.055 * $value ** ( 1 / 2.4 ) - 0.055;

				return (int) round( 255 * $value );
			},
			$linear
		);
	}
}
