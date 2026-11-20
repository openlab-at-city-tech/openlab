<?php
/**
 * Kadence\FSE\Palette_Shim class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Theme_JSON;
use WP_Theme_JSON_Data;

/**
 * Lets the Site Editor own the 15 palette colors in Full Site Editing mode.
 *
 * In classic mode the flow runs from the Customizer to core: `theme.json` declares each
 * `theme-paletteN` preset as `var(--global-paletteN)`, and the Customizer defines
 * `--global-paletteN`. In FSE mode the flow is inverted. Every preset holds a real
 * color, and `--global-paletteN` is redefined as
 * `var(--wp--preset--color--theme-palette-N)`, so Kadence Blocks and every other
 * `--global-*` consumer follow the Site Editor.
 *
 * Both halves have to hold for every slot, or the preset and `--global-paletteN` point at
 * each other and both compute to the guaranteed-invalid value. So both read the one
 * `Slot_Map`, and a slot it can't resolve is left on its classic behavior in both.
 *
 * The saved global styles record is not touched. A palette saved in classic mode can hold
 * pointers, and a Reset and Save in the Site Editor clears them.
 *
 * PHP that reads the palette, such as LearnDash's tints, Give's standalone documents and the
 * classic editor's `content_style`, gets the Site Editor's colors from `palette_option()`,
 * through `Palette_Literal`.
 *
 * @since 1.6.0
 */
final class Palette_Shim {

	/**
	 * The request's slot map.
	 *
	 * @since 1.6.0
	 *
	 * @var Slot_Map
	 */
	private Slot_Map $slot_map;

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
	 * Gives the theme origin a color for each resolvable slot.
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
		$data    = $theme_json->get_data();
		$palette = $data['settings']['color']['palette']['theme'] ?? null;

		if ( ! is_array( $palette ) ) {
			return $theme_json;
		}

		$changed = false;

		foreach ( $palette as $index => $entry ) {
			$slug  = is_array( $entry ) ? ( $entry['slug'] ?? null ) : null;
			$color = is_string( $slug ) ? $this->slot_map->color_of( $slug ) : null;

			if ( null === $color ) {
				continue;
			}

			$palette[ $index ]['color'] = $color;
			$changed                    = true;
		}

		return $changed ? $this->with_palette( $theme_json, $data, $palette ) : $theme_json;
	}

	/**
	 * Points each resolvable slot's `--global-paletteN` at its preset, and aliases the
	 * undashed preset names global styles reads back to the dashed ones core defines.
	 *
	 * Lands after the generator's own `:root` block in the same inline style, so it wins
	 * on source order.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		$declarations = '';

		foreach ( $this->slot_map->preset_properties() + $this->slot_map->preset_aliases() as $property => $value ) {
			$declarations .= $property . ':' . $value . ';';
		}

		return $css . ( '' === $declarations ? '' : ':root{' . $declarations . '}' );
	}

	/**
	 * Replaces the theme palette in origin data.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Theme_JSON_Data $theme_json Origin data.
	 * @param array              $data       Its raw data.
	 * @param array              $palette    New theme palette.
	 * @return WP_Theme_JSON_Data
	 */
	private function with_palette( $theme_json, array $data, array $palette ) {
		return $theme_json->update_with(
			[
				'version'  => $data['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => [
					'color' => [
						'palette' => [
							'theme' => $palette,
						],
					],
				],
			]
		);
	}
}
