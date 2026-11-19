<?php
/**
 * Kadence\FSE\Style_Feed class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Theme_JSON;
use WP_Theme_JSON_Data;
use function Kadence\kadence;

/**
 * Gives the Site Editor real values for the site's body and link colors, font sizes and
 * spacing in Full Site Editing mode.
 *
 * `theme.json` declares the font size and spacing presets as `var(--global-*)` pointers and
 * no body or link styles, so the Styles panel and Style Book show core's defaults and a
 * preset edit never reaches Kadence's CSS. In FSE mode the theme origin gets the values the
 * classic stylesheet defines and the Customizer's colors, and each `--global-*` variable is
 * redefined as its preset, so Kadence's CSS follows the Site Editor.
 *
 * Both halves read the resolved presets, so a preset that still holds a pointer, from a
 * record saved in classic mode, keeps its classic variable and nothing cycles.
 *
 * @since 1.6.0
 */
final class Style_Feed {

	/**
	 * Font size slug => size, as `assets/css/src/_vars.scss` defines them.
	 *
	 * @since 1.6.0
	 */
	private const FONT_SIZES = [
		'small'   => 'clamp(0.8rem, 0.73rem + 0.217vw, 0.9rem)',
		'medium'  => 'clamp(1.1rem, 0.995rem + 0.326vw, 1.25rem)',
		'large'   => 'clamp(1.75rem, 1.576rem + 0.543vw, 2rem)',
		'larger'  => 'clamp(2rem, 1.6rem + 1vw, 2.5rem)',
		'xxlarge' => 'clamp(2.25rem, 1.728rem + 1.63vw, 3rem)',
	];

	/**
	 * Spacing slug => size, as `assets/css/src/_vars.scss` defines them. Its mix of em and
	 * rem is kept, so spacing renders as it does in classic mode.
	 *
	 * @since 1.6.0
	 */
	private const SPACING_SIZES = [
		'xs'  => '1em',
		'sm'  => '1.5rem',
		'md'  => '2rem',
		'lg'  => '2.5em',
		'xl'  => '3.5em',
		'xxl' => '5rem',
	];

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
	 * Gives the theme origin the font size and spacing presets as values, and the body and link colors.
	 *
	 * The presets are written as whole lists, since a child theme's `theme.json` is filtered on its own
	 * and the parent's pointers would otherwise stay. Any other presets the data has are kept, and so is
	 * a child theme's own size for one of the theme's slugs.
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

		return $theme_json->update_with(
			[
				'version'  => $data['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => [
					'typography' => [
						'fontSizes' => $this->merge( $this->font_sizes(), $data['settings']['typography']['fontSizes']['theme'] ?? [] ),
					],
					'spacing'    => [
						'spacingSizes' => $this->merge( $this->spacing_sizes(), $data['settings']['spacing']['spacingSizes']['theme'] ?? [] ),
					],
				],
				'styles'   => $this->styles(),
			]
		);
	}

	/**
	 * Points each font size and spacing variable at its preset on the front end.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		return $css . $this->rule( ':root' );
	}

	/**
	 * Points each font size and spacing variable at its preset in the editor.
	 *
	 * On `body` too, since the editor canvas prints the classic `:root` values after this.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_editor_properties( $css ): string {
		return $css . $this->rule( ':root,body' );
	}

	/**
	 * The rule that points each variable at its preset.
	 *
	 * @since 1.6.0
	 *
	 * @param string $selector Selector to declare them on.
	 * @return string
	 */
	private function rule( string $selector ): string {
		$declarations = '';

		foreach ( $this->resolved( [ 'typography', 'fontSizes' ], self::FONT_SIZES ) as $slug ) {
			$declarations .= '--global-font-size-' . $slug . ':var(--wp--preset--font-size--' . $slug . ');';
		}

		foreach ( $this->resolved( [ 'spacing', 'spacingSizes' ], self::SPACING_SIZES ) as $slug ) {
			$declarations .= '--global-' . $slug . '-spacing:var(--wp--preset--spacing--' . $slug . ');';
		}

		return '' === $declarations ? '' : $selector . '{' . $declarations . '}';
	}

	/**
	 * The font size presets.
	 *
	 * @since 1.6.0
	 *
	 * @return array[]
	 */
	private function font_sizes(): array {
		$names = [
			'small'   => __( 'Small', 'kadence' ),
			'medium'  => __( 'Medium', 'kadence' ),
			'large'   => __( 'Large', 'kadence' ),
			'larger'  => __( 'Larger', 'kadence' ),
			'xxlarge' => __( 'XX-Large', 'kadence' ),
		];

		return $this->presets( $names, self::FONT_SIZES );
	}

	/**
	 * The spacing presets.
	 *
	 * @since 1.6.0
	 *
	 * @return array[]
	 */
	private function spacing_sizes(): array {
		$names = [
			'xs'  => __( 'X-Small', 'kadence' ),
			'sm'  => __( 'Small', 'kadence' ),
			'md'  => __( 'Medium', 'kadence' ),
			'lg'  => __( 'Large', 'kadence' ),
			'xl'  => __( 'X-Large', 'kadence' ),
			'xxl' => __( 'XX-Large', 'kadence' ),
		];

		return $this->presets( $names, self::SPACING_SIZES );
	}

	/**
	 * The theme's presets, followed by any others the data already has. An existing preset with one
	 * of the theme's slugs replaces the theme's when its size doesn't point at a `--global-*` variable.
	 *
	 * @since 1.6.0
	 *
	 * @param array[] $presets  The theme's presets.
	 * @param mixed   $existing Presets the data already has.
	 * @return array[]
	 */
	private function merge( array $presets, $existing ): array {
		$slugs = array_column( $presets, 'slug' );

		foreach ( (array) $existing as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['slug'] ) ) {
				continue;
			}

			$index = array_search( $entry['slug'], $slugs, true );

			if ( false === $index ) {
				$presets[] = $entry;
			} elseif ( is_string( $entry['size'] ?? null ) && ! str_contains( $entry['size'], 'var(--global-' ) ) {
				$presets[ $index ] = $entry;
			}
		}

		return $presets;
	}

	/**
	 * Preset entries from names and sizes, keyed alike.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, string> $names Slug => name.
	 * @param array<string, string> $sizes Slug => size.
	 * @return array[]
	 */
	private function presets( array $names, array $sizes ): array {
		$presets = [];

		foreach ( $sizes as $slug => $size ) {
			$presets[] = [
				'name' => $names[ $slug ],
				'slug' => $slug,
				'size' => $size,
			];
		}

		return $presets;
	}

	/**
	 * The body background and text colors and the link colors, from the Customizer.
	 *
	 * The background is fed only when it is a plain color, not an image or a gradient.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function styles(): array {
		$styles     = [];
		$background = kadence()->sub_option( 'site_background', 'desktop' );
		$base_font  = kadence()->option( 'base_font' );
		$link       = kadence()->option( 'link_color' );

		if ( is_array( $background ) && 'color' === ( empty( $background['type'] ) ? 'color' : $background['type'] ) ) {
			$this->set( $styles, [ 'color', 'background' ], $background['color'] ?? null );
		}

		$this->set( $styles, [ 'color', 'text' ], is_array( $base_font ) ? ( $base_font['color'] ?? null ) : null );
		$this->set( $styles, [ 'elements', 'link', 'color', 'text' ], is_array( $link ) ? ( $link['highlight'] ?? null ) : null );
		$this->set( $styles, [ 'elements', 'link', ':hover', 'color', 'text' ], is_array( $link ) ? ( $link['highlight-alt'] ?? null ) : null );

		return $styles;
	}

	/**
	 * Sets a color in the styles, when the Customizer value is one the Site Editor can hold.
	 *
	 * @since 1.6.0
	 *
	 * @param array    $styles Styles built so far.
	 * @param string[] $path   Path under `styles`.
	 * @param mixed    $color  Customizer value.
	 */
	private function set( array &$styles, array $path, $color ): void {
		$value = $this->color_value( $color );

		if ( null === $value ) {
			return;
		}

		foreach ( array_reverse( $path ) as $key ) {
			$value = [ $key => $value ];
		}

		$styles = array_replace_recursive( $styles, $value );
	}

	/**
	 * A Customizer color as global styles holds it.
	 *
	 * A palette slot becomes a preset reference, so the style follows palette edits and Pro's
	 * dark mode. Core prints the reference with the undashed preset name, which the palette
	 * shim aliases only for the slots it resolves.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $color Customizer value.
	 * @return string|null
	 */
	public function color_value( $color ): ?string {
		if ( ! is_string( $color ) ) {
			return null;
		}

		if ( 1 === preg_match( '/^palette(\d+)$/', $color, $matches ) ) {
			$slug = 'theme-palette' . $matches[1];

			return null === $this->slot_map->color_of( $slug ) ? null : 'var:preset|color|' . $slug;
		}

		return Derived_Tokens::is_color( $color ) && ! str_contains( strtolower( $color ), 'var(' ) ? trim( $color ) : null;
	}

	/**
	 * The slugs whose resolved preset holds a value rather than a pointer.
	 *
	 * @since 1.6.0
	 *
	 * @param string[]              $path  Path under `settings`.
	 * @param array<string, string> $sizes Slug => size, for the slugs to check.
	 * @return string[]
	 */
	private function resolved( array $path, array $sizes ): array {
		$presets = wp_get_global_settings( $path );
		$values  = [];

		foreach ( (array) ( $presets['theme'] ?? [] ) as $entry ) {
			if ( is_array( $entry ) && isset( $entry['slug'], $entry['size'] ) && is_string( $entry['size'] ) ) {
				$values[ $entry['slug'] ] = $entry['size'];
			}
		}

		$slugs = [];

		foreach ( array_keys( $sizes ) as $slug ) {
			if ( isset( $values[ $slug ] ) && ! str_contains( $values[ $slug ], 'var(--global-' ) ) {
				$slugs[] = $slug;
			}
		}

		return $slugs;
	}
}
