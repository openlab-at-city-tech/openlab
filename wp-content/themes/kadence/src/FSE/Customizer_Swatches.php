<?php
/**
 * Kadence\FSE\Customizer_Swatches class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Shows the Site Editor's palette in the Customizer's color swatches in Full Site Editing mode.
 *
 * The color controls draw their swatches from the live value of the
 * `kadence_global_palette` setting, and paint some of them (slot 10, gradient control
 * points, the selected-color indicators) through `--global-paletteN` on the controls
 * pane. Both are given the color the front end renders for each slot, so picking a swatch
 * shows what the site will show. Only what the Customizer's scripts receive changes. The
 * stored option, and everything built from it, is left alone.
 *
 * A slot the slot map leaves on its classic behavior keeps the Customizer's color, as it
 * does on the front end.
 *
 * @since 1.6.0
 */
final class Customizer_Swatches {

	/**
	 * How many preset references deep a value is followed.
	 *
	 * @since 1.6.0
	 */
	private const MAX_DEPTH = 15;

	/**
	 * The request's slot map.
	 *
	 * @since 1.6.0
	 *
	 * @var Slot_Map
	 */
	private Slot_Map $slot_map;

	/**
	 * Preset slug => color to show, or null to keep the Customizer's.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string, string|null>|null
	 */
	private ?array $colors = null;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Slot_Map $slot_map The request's slot map.
	 */
	public function __construct( Slot_Map $slot_map ) {
		$this->slot_map = $slot_map;
	}

	/**
	 * Gives the palette setting's JS value the color each slot shows.
	 *
	 * Only `color` changes, and only for slots that have a color. `slug` and `name` stay,
	 * so picking a swatch still stores the slot reference.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value The setting's value as JSON.
	 * @return mixed
	 */
	public function filter_js_value( $value ) {
		$palette = is_string( $value ) ? json_decode( $value, true ) : null;

		if ( ! is_array( $palette ) ) {
			return $value;
		}

		$active = ! empty( $palette['active'] ) && is_string( $palette['active'] ) ? $palette['active'] : 'palette';

		if ( ! isset( $palette[ $active ] ) || ! is_array( $palette[ $active ] ) ) {
			return $value;
		}

		$colors  = $this->colors();
		$changed = false;

		foreach ( $palette[ $active ] as $index => $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['slug'] ) || ! preg_match( '/^palette([1-9]\d*)$/', (string) $entry['slug'], $matches ) ) {
				continue;
			}

			$color = $colors[ 'theme-palette' . $matches[1] ] ?? null;

			if ( null === $color ) {
				continue;
			}

			$palette[ $active ][ $index ]['color'] = $color;
			$changed                               = true;
		}

		return $changed ? wp_json_encode( $palette ) : $value;
	}

	/**
	 * Redefines `--global-paletteN` on the controls pane for each slot that has a color.
	 *
	 * The color is written directly, since the controls pane doesn't load the stylesheet
	 * that defines the presets.
	 *
	 * @since 1.6.0
	 */
	public function add_controls_properties(): void {
		$declarations = '';

		foreach ( $this->colors() as $slug => $color ) {
			if ( null === $color ) {
				continue;
			}

			$declarations .= $this->slot_map->property_of( $slug ) . ':' . $color . ';';
		}

		if ( '' === $declarations ) {
			return;
		}

		wp_add_inline_style( 'kadence-customizer-controls', wp_strip_all_tags( ':root{' . $declarations . '}' ) );
	}

	/**
	 * The color each slot shows, built once per instance.
	 *
	 * A slot the map leaves unresolved shows the Customizer's color. Otherwise it shows
	 * its merged `theme-paletteN` preset, with preset references replaced by colors.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string|null> Preset slug => color, or null to keep the Customizer's.
	 */
	private function colors(): array {
		if ( null !== $this->colors ) {
			return $this->colors;
		}

		$palette = wp_get_global_settings( [ 'color', 'palette' ] );
		$presets = $this->presets( is_array( $palette ) ? $palette : [] );
		$theme   = $this->presets( [ 'theme' => $palette['theme'] ?? [] ] );

		$this->colors = [];

		foreach ( $this->slot_map->slugs() as $slug ) {
			$value = $theme[ _wp_to_kebab_case( $slug ) ] ?? null;

			$this->colors[ $slug ] = null === $this->slot_map->color_of( $slug ) || null === $value ? null : $this->resolve( $value, $presets, 0 );
		}

		return $this->colors;
	}

	/**
	 * Preset name => value across the given origins, later origins winning as in the stylesheet.
	 *
	 * @since 1.6.0
	 *
	 * @param array $origins Origin => palette entries.
	 * @return array<string, string>
	 */
	private function presets( array $origins ): array {
		$presets = [];

		foreach ( [ 'default', 'theme', 'custom' ] as $origin ) {
			if ( empty( $origins[ $origin ] ) || ! is_array( $origins[ $origin ] ) ) {
				continue;
			}

			foreach ( $origins[ $origin ] as $entry ) {
				if ( is_array( $entry ) && isset( $entry['slug'], $entry['color'] ) && is_string( $entry['color'] ) ) {
					$presets[ _wp_to_kebab_case( (string) $entry['slug'] ) ] = $entry['color'];
				}
			}
		}

		return $presets;
	}

	/**
	 * Replaces each color preset reference in a value with that preset's color.
	 *
	 * @since 1.6.0
	 *
	 * @param string                $value   Preset value.
	 * @param array<string, string> $presets Preset name => value.
	 * @param int                   $depth   References followed so far.
	 * @return string|null Null when the value can't be turned into a color.
	 */
	private function resolve( string $value, array $presets, int $depth ) {
		if ( $depth > self::MAX_DEPTH ) {
			return null;
		}

		$unresolved = false;
		$resolved   = preg_replace_callback(
			'/var\(\s*--wp--preset--color--([a-z0-9-]+)\s*(?:,[^()]*)?\)/i',
			function ( $matches ) use ( $presets, $depth, &$unresolved ) {
				$color = isset( $presets[ $matches[1] ] ) ? $this->resolve( $presets[ $matches[1] ], $presets, $depth + 1 ) : null;

				if ( null === $color ) {
					$unresolved = true;

					return $matches[0];
				}

				return $color;
			},
			$value
		);

		if (
			$unresolved
			|| null === $resolved
			|| preg_match( '/var\(/i', $resolved )
			|| str_contains( $resolved, '--wp--preset--color--' )
		) {
			return null;
		}

		// No color contains these. A record saved with `unfiltered_html` skips core's
		// sanitiser, and such a value would end the declaration in the controls pane.
		if ( preg_match( '/[;{}<>]/', $resolved ) ) {
			return null;
		}

		return $resolved;
	}
}
