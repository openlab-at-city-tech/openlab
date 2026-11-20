<?php
/**
 * Kadence\FSE\Layout_Widths class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Theme_JSON;
use WP_Theme_JSON_Data;
use Kadence\Kadence_CSS;
use function Kadence\kadence;

/**
 * Lets the Site Editor own the content width and the wide width in Full Site Editing mode.
 *
 * Kadence's `content_width` is the outer container, with the edge padding inside it. Core's
 * `contentSize` is the content itself, so the theme origin holds `content_width` less the
 * desktop edge padding, and Kadence adds its responsive edge padding back when it sizes its
 * containers. `wideSize` is `content_width` plus the wide offset. Both are real lengths, since
 * the Layout panel shows a `var()` or `calc()` as an empty field.
 *
 * `contentSize` is one number, so it is a snapshot at the desktop padding. A breakpoint with
 * another padding lands a few pixels off, which shows only where `content_width` is below the
 * viewport.
 *
 * A width saved in the Site Editor is the user origin and is used as is. The stored Customizer
 * options are never written.
 *
 * @since 1.6.0
 */
final class Layout_Widths {

	/**
	 * Pixels in an em or rem, for adding a padding in another unit to `content_width`.
	 *
	 * @since 1.6.0
	 */
	private const ROOT_FONT_SIZE = 16;

	/**
	 * Gives the theme origin the Customizer's content width and wide width.
	 *
	 * Untyped, since the Gutenberg plugin passes its own WP_Theme_JSON_Data_Gutenberg, which
	 * has the same methods but doesn't extend core's class.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Theme_JSON_Data $theme_json Theme origin data.
	 * @return WP_Theme_JSON_Data
	 */
	public function fill_theme_origin( $theme_json ) {
		$data = $theme_json->get_data();
		$unit = $this->content_unit();
		$size = (float) kadence()->sub_option( 'content_width', 'size' );

		return $theme_json->update_with(
			[
				'version'  => $data['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => [
					'layout' => [
						'contentSize' => $this->length( max( 0.0, $size - 2 * $this->edge_padding_in( $unit ) ), $unit ),
						'wideSize'    => $this->length( $size + $this->wide_offset( $unit ), $unit ),
					],
				],
			]
		);
	}

	/**
	 * Points Kadence's width variables at the ones core builds from the Site Editor's widths.
	 *
	 * The generator defines `--global-content-width` as the Customizer's outer width, so the
	 * content size gets the responsive edge padding added back. `--global-calc-content-width`
	 * is the width inside the padding, which is the content size again. Both pipelines run
	 * this, so the editor canvas follows the same widths. Lands after the generator's own
	 * `:root` block in the same inline style, so it wins on source order.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		return $css . ':root{'
			. '--global-content-width:calc(var(--wp--style--global--content-size) + 2 * var(--global-content-edge-padding));'
			. '--global-content-wide-width:var(--wp--style--global--wide-size);'
			. '--global-calc-content-width:var(--wp--style--global--content-size);'
			. '--global-calc-wide-content-width:var(--wp--style--global--wide-size);'
			. '}';
	}

	/**
	 * The unit of the Customizer's content width.
	 *
	 * @since 1.6.0
	 *
	 * @return string px, em or rem.
	 */
	private function content_unit(): string {
		$unit = kadence()->sub_option( 'content_width', 'unit' );

		return in_array( $unit, [ 'px', 'em', 'rem' ], true ) ? $unit : 'px';
	}

	/**
	 * The desktop edge padding, in a unit.
	 *
	 * Zero when the padding is not set or is in a unit that is not a width, so a value that
	 * can't be read never leaves `contentSize` pointing back at the variable that reads it.
	 *
	 * @since 1.6.0
	 *
	 * @param string $unit px, em or rem.
	 * @return float
	 */
	private function edge_padding_in( string $unit ): float {
		$padding = ( new Kadence_CSS() )->render_range( kadence()->option( 'content_edge_spacing' ), 'desktop' );

		if ( ! is_string( $padding ) || ! preg_match( '/^(-?[\d.]+)(px|em|rem)$/', $padding, $parts ) ) {
			return 0.0;
		}

		return $this->convert( (float) $parts[1], $parts[2], $unit );
	}

	/**
	 * The offset the wide width adds to the content width, in a unit.
	 *
	 * The filter and its defaults are the ones the dynamic CSS builds `--global-content-wide-width` with.
	 *
	 * @since 1.6.0
	 *
	 * @param string $unit px, em or rem.
	 * @return float
	 */
	private function wide_offset( string $unit ): float {
		$offsets = apply_filters(
			'kadence_align_wide_array',
			[
				'px'  => '230',
				'em'  => '10',
				'rem' => '10',
				'vw'  => '10',
			]
		);

		return isset( $offsets[ $unit ] ) ? (float) $offsets[ $unit ] : 0.0;
	}

	/**
	 * Converts a length between px, em and rem, taking an em and a rem as 16px.
	 *
	 * @since 1.6.0
	 *
	 * @param float  $value Length.
	 * @param string $from  Its unit.
	 * @param string $to    The unit to return it in.
	 * @return float
	 */
	private function convert( float $value, string $from, string $to ): float {
		if ( $from === $to ) {
			return $value;
		}

		$pixels = 'px' === $from ? $value : $value * self::ROOT_FONT_SIZE;

		return 'px' === $to ? $pixels : $pixels / self::ROOT_FONT_SIZE;
	}

	/**
	 * Formats a length, with at most two decimals.
	 *
	 * @since 1.6.0
	 *
	 * @param float  $value Length.
	 * @param string $unit  Its unit.
	 * @return string
	 */
	private function length( float $value, string $unit ): string {
		return rtrim( rtrim( number_format( $value, 2, '.', '' ), '0' ), '.' ) . $unit;
	}
}
