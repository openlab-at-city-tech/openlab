<?php
/**
 * Kadence\FSE\Accent_Complement class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Computes the classic accent complement as a fixed hex.
 *
 * The Site Editor can't express the Customizer's "link to accent", so while the Customizer
 * holds the auto-complement sentinel, the complement slot is served as the hex Chrome
 * renders for the classic formula. It stops following the accent once the record is saved.
 *
 * @since 1.6.0
 */
final class Accent_Complement {

	/**
	 * The classic complement of an accent color, as a hex.
	 *
	 * Applies the classic formula in OKLCH, then clips each channel to sRGB, which is
	 * what Chrome renders for it on an sRGB surface.
	 *
	 * @since 1.6.0
	 *
	 * @param string $accent The accent slot's Customizer value.
	 * @return string|null Null when the accent is not a hex color.
	 */
	public function compute( string $accent ): ?string {
		$rgb = $this->parse_hex( trim( $accent ) );

		if ( null === $rgb ) {
			return null;
		}

		list( $l, $a, $b ) = $this->linear_to_oklab( array_map( [ $this, 'to_linear' ], $rgb ) );

		$chroma = sqrt( $a * $a + $b * $b );
		$hue    = atan2( $b, $a ) + M_PI;
		$l     += 0.10 * ( 1 - $l );

		$linear = $this->oklab_to_linear( $l, $chroma * cos( $hue ), $chroma * sin( $hue ) );
		$hex    = '#';

		foreach ( $linear as $channel ) {
			$value = max( 0.0, min( 1.0, $this->to_srgb( $channel ) ) );
			$hex  .= sprintf( '%02x', (int) round( $value * 255 ) );
		}

		return $hex;
	}

	/**
	 * Parses a 3-, 4-, 6- or 8-digit hex color into sRGB channels from 0 to 1.
	 *
	 * Alpha is ignored, as the classic formula replaces it.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Color value.
	 * @return float[]|null
	 */
	private function parse_hex( string $value ): ?array {
		if ( ! preg_match( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value, $matches ) ) {
			return null;
		}

		$digits = $matches[1];

		if ( strlen( $digits ) <= 4 ) {
			$digits = $digits[0] . $digits[0] . $digits[1] . $digits[1] . $digits[2] . $digits[2];
		}

		return [
			hexdec( substr( $digits, 0, 2 ) ) / 255,
			hexdec( substr( $digits, 2, 2 ) ) / 255,
			hexdec( substr( $digits, 4, 2 ) ) / 255,
		];
	}

	/**
	 * Converts an sRGB channel to linear light.
	 *
	 * @since 1.6.0
	 *
	 * @param float $channel sRGB channel.
	 * @return float
	 */
	private function to_linear( float $channel ): float {
		return $channel <= 0.04045 ? $channel / 12.92 : ( ( $channel + 0.055 ) / 1.055 ) ** 2.4;
	}

	/**
	 * Converts a linear-light channel to sRGB.
	 *
	 * @since 1.6.0
	 *
	 * @param float $channel Linear channel.
	 * @return float
	 */
	private function to_srgb( float $channel ): float {
		$sign      = $channel < 0 ? -1 : 1;
		$magnitude = abs( $channel );

		return $sign * ( $magnitude > 0.0031308 ? 1.055 * ( $magnitude ** ( 1 / 2.4 ) ) - 0.055 : 12.92 * $magnitude );
	}

	/**
	 * Converts linear sRGB to OKLab.
	 *
	 * @since 1.6.0
	 *
	 * @param float[] $rgb Linear channels.
	 * @return float[]
	 */
	private function linear_to_oklab( array $rgb ): array {
		list( $r, $g, $b ) = $rgb;

		$l = $this->cbrt( 0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b );
		$m = $this->cbrt( 0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b );
		$s = $this->cbrt( 0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b );

		return [
			0.2104542553 * $l + 0.7936177850 * $m - 0.0040720468 * $s,
			1.9779984951 * $l - 2.4285922050 * $m + 0.4505937099 * $s,
			0.0259040371 * $l + 0.7827717662 * $m - 0.8086757660 * $s,
		];
	}

	/**
	 * Converts OKLab to linear sRGB.
	 *
	 * @since 1.6.0
	 *
	 * @param float $lightness OKLab L.
	 * @param float $a         OKLab a.
	 * @param float $b         OKLab b.
	 * @return float[]
	 */
	private function oklab_to_linear( float $lightness, float $a, float $b ): array {
		$l = ( $lightness + 0.3963377774 * $a + 0.2158037573 * $b ) ** 3;
		$m = ( $lightness - 0.1055613458 * $a - 0.0638541728 * $b ) ** 3;
		$s = ( $lightness - 0.0894841775 * $a - 1.2914855480 * $b ) ** 3;

		return [
			4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
			-1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
			-0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
		];
	}

	/**
	 * Real cube root, including of negative numbers.
	 *
	 * @since 1.6.0
	 *
	 * @param float $value Number.
	 * @return float
	 */
	private function cbrt( float $value ): float {
		return $value < 0 ? -( ( -$value ) ** ( 1 / 3 ) ) : $value ** ( 1 / 3 );
	}
}
