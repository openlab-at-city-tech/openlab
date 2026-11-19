<?php
/**
 * Kadence\FSE\Palette_Literal class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Answers "what color does the Site Editor hold for slot N" for PHP that does color maths.
 *
 * In Full Site Editing mode `palette_option()` returns it too, through the
 * `kadence_site_palette_option` filter, so PHP that computes from the palette follows the
 * Site Editor. `customizer_palette_option()` is still the Customizer's color.
 *
 * @since 1.6.0
 */
final class Palette_Literal {

	/**
	 * The request's slot map, for a slot the Site Editor holds a pointer for.
	 *
	 * @since 1.6.0
	 *
	 * @var Slot_Map
	 */
	private Slot_Map $slot_map;

	/**
	 * Full Site Editing mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Mode
	 */
	private Mode $mode;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Slot_Map $slot_map The request's slot map.
	 * @param Mode     $mode     Full Site Editing mode.
	 */
	public function __construct( Slot_Map $slot_map, Mode $mode ) {
		$this->slot_map = $slot_map;
		$this->mode     = $mode;
	}

	/**
	 * The color a slot resolves to, as the stylesheet resolves it.
	 *
	 * Reads the merged theme and user palette. A pointer (`var(--global-paletteN)`, left in a
	 * record saved in classic mode) or a missing entry falls back to the slot map, which is
	 * what the stylesheet resolves a pointer to.
	 *
	 * @since 1.6.0
	 *
	 * @param int $slot Slot number.
	 * @return string|null The color, or null when none can be found.
	 */
	public function get( int $slot ): ?string {
		$palette = wp_get_global_settings( [ 'color', 'palette', 'theme' ] );
		$wanted  = 'theme-palette' . $slot;
		$color   = null;

		if ( is_array( $palette ) ) {
			foreach ( $palette as $entry ) {
				if ( is_array( $entry ) && ( $entry['slug'] ?? null ) === $wanted ) {
					$color = $entry['color'] ?? null;
					break;
				}
			}
		}

		if ( is_string( $color ) && '' !== trim( $color ) && false === stripos( $color, 'var(' ) ) {
			return trim( $color );
		}

		$fallback = $this->slot_map->color_of( $wanted );

		return is_string( $fallback ) && '' !== $fallback ? $fallback : null;
	}

	/**
	 * Returns the Site Editor's color for `palette_option()`.
	 *
	 * Keeps the Customizer's color:
	 * - once the mode is off, though the request started in it;
	 * - until `init` has run, so global settings aren't cached before blocks register;
	 * - inside the theme origin filter, which reading global settings would run again.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value  The Customizer's color.
	 * @param string $subkey Palette slot, such as palette1.
	 * @return string
	 */
	public function filter_palette_option( $value, $subkey ) {
		if ( ! $this->mode->is_enabled() || ! did_action( 'init' ) || doing_action( 'init' ) || doing_filter( 'wp_theme_json_data_theme' ) ) {
			return $value;
		}

		if ( 1 !== preg_match( '/^palette(\d+)$/', (string) $subkey, $matches ) ) {
			return $value;
		}

		return $this->get( (int) $matches[1] ) ?? $value;
	}
}
