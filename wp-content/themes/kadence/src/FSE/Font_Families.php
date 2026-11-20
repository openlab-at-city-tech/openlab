<?php
/**
 * Kadence\FSE\Font_Families class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_REST_Request;
use WP_REST_Response;
use WP_Theme_JSON;
use WP_Theme_JSON_Data;
use WP_Theme_JSON_Resolver;
use function Kadence\kadence;

/**
 * Registers the fonts the Customizer uses as `settings.typography.fontFamilies`, so the
 * Site Editor's Styles → Typography picker lists them.
 *
 * Only the picker entries are registered. The entries carry no `fontFace`, because core
 * prints an `@font-face` rule for every theme font that has one, and Kadence already loads
 * its Google fonts itself (from Google, or from the site when local hosting is on). A
 * second declaration would load each file twice. Any other Google font comes from core's
 * own Font Library, which downloads it and writes its `@font-face`.
 *
 * A family installed through the Font Library with a `fontFace` is left out of Kadence's
 * Google fonts, so only core's `@font-face` loads it, with the styles the install brought.
 *
 * Known limit: without `fontFace`, the picker offers every weight of a Customizer font,
 * while only the variants chosen in the Customizer or in Styles are loaded.
 *
 * @since 1.6.0
 */
final class Font_Families {

	/**
	 * The Customizer options that hold a font family.
	 *
	 * @since 1.6.0
	 */
	private const OPTIONS = [ 'base_font', 'heading_font', 'h1_font', 'h2_font', 'h3_font', 'h4_font', 'h5_font', 'h6_font' ];

	/**
	 * The slug of the system font stack. Fixed, since the entry's name is translated.
	 *
	 * @since 1.6.0
	 */
	private const SYSTEM_SLUG = 'system-default';

	/**
	 * Gives the theme origin an entry for each family the Customizer uses.
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
		$data     = $theme_json->get_data();
		$existing = $data['settings']['typography']['fontFamilies']['theme'] ?? $data['settings']['typography']['fontFamilies'] ?? [];
		$existing = is_array( $existing ) ? $existing : [];
		$slugs    = $this->installed_slugs();

		foreach ( $existing as $entry ) {
			if ( is_array( $entry ) && isset( $entry['slug'] ) ) {
				$slugs[ $entry['slug'] ] = true;
			}
		}

		$added = [];

		foreach ( $this->families() as $family ) {
			if ( isset( $slugs[ $family['slug'] ] ) ) {
				continue;
			}

			$slugs[ $family['slug'] ] = true;
			$added[]                  = $family;
		}

		if ( ! $added ) {
			return $theme_json;
		}

		return $theme_json->update_with(
			[
				'version'  => $data['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'settings' => [
					'typography' => [
						'fontFamilies' => array_merge( $existing, $added ),
					],
				],
			]
		);
	}

	/**
	 * Drops the `theme` list from the saved global styles, so the Customizer's fonts stay the source.
	 *
	 * The Site Editor writes its whole `fontFamilies` object back when anything in it is saved,
	 * such as a font installed from the Font Library. That leaves a copy of the theme list in the
	 * saved record, and the copy outranks `fill_theme_origin()`, so a font changed in the
	 * Customizer would never reach the picker. Fonts the user installed sit under `custom`
	 * and are left alone. The saved record itself isn't touched.
	 *
	 * Untyped for the same reason as `fill_theme_origin()`.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Theme_JSON_Data $theme_json User origin data.
	 * @return WP_Theme_JSON_Data
	 */
	public function strip_user_theme_copy( $theme_json ) {
		$data = $theme_json->get_data();

		if ( ! isset( $data['settings']['typography']['fontFamilies']['theme'] ) ) {
			return $theme_json;
		}

		unset( $data['settings']['typography']['fontFamilies']['theme'] );

		$class = get_class( $theme_json );

		return new $class( $data, 'custom' );
	}

	/**
	 * Drops the saved `theme` list from the global styles record the Site Editor loads or saves.
	 *
	 * The REST controller builds the record from the saved post content, without the
	 * `wp_theme_json_data_user` filter and without a `rest_prepare_*` filter of its own, and the
	 * editor merges that copy over the theme's list. Without this, a font changed in the
	 * Customizer stays out of the picker for as long as the record carries the copy.
	 *
	 * Every method is filtered, not only reads. The stored record keeps its copy, so the response
	 * to a save would hand it straight back, and the editor keeps that response as its saved state.
	 * The saved record itself isn't touched.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed           $response The response, or an error.
	 * @param array           $handler  The matched route handler.
	 * @param WP_REST_Request $request  The request.
	 * @return mixed
	 */
	public function strip_theme_copy_from_response( $response, $handler, $request ) {
		if ( ! $response instanceof WP_REST_Response || ! preg_match( '#^/wp/v2/global-styles/\d+$#', $request->get_route() ) ) {
			return $response;
		}

		$data = $response->get_data();

		// A record with no settings to expose comes back with an empty object there, not an array.
		if ( ! is_array( $data ) || ! is_array( $data['settings'] ?? null ) || ! isset( $data['settings']['typography']['fontFamilies']['theme'] ) ) {
			return $response;
		}

		unset( $data['settings']['typography']['fontFamilies']['theme'] );

		$response->set_data( $data );

		return $response;
	}

	/**
	 * The slug of the preset registered for a Customizer font's family.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, mixed> $font Customizer typography.
	 * @return string|null Null when the font sets no family, as on Inherit.
	 */
	public function preset_slug_of( array $font ): ?string {
		$entry = $this->entry( $font );

		return null === $entry ? null : $entry['slug'];
	}

	/**
	 * The Google family a Customizer font uses under a preset slug.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Preset slug.
	 * @return string|null Null when no Customizer font loads that family from Google.
	 */
	public function google_family_of( string $slug ): ?string {
		foreach ( self::OPTIONS as $option ) {
			$font  = kadence()->option( $option );
			$entry = $this->entry( $font );

			if ( null !== $entry && $slug === $entry['slug'] && true === ( $font['google'] ?? false ) ) {
				return $entry['name'];
			}
		}

		return null;
	}

	/**
	 * Leaves the families installed from the Font Library out of Kadence's Google fonts when
	 * core prints their `@font-face`, so they aren't loaded twice.
	 *
	 * Both slug rules are checked: core's Google Fonts collection slugs a family with
	 * `sanitize_title()` ("abeezee"), while kebab-casing gives "a-bee-zee".
	 *
	 * Untyped, since Kadence passes null when it has no fonts.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $fonts Family => font, as Kadence collects them.
	 * @return mixed
	 */
	public function skip_installed_google_fonts( $fonts ) {
		if ( ! is_array( $fonts ) ) {
			return $fonts;
		}

		$installed = $this->installed_slugs_with_faces();

		foreach ( $fonts as $key => $font ) {
			$family = $font['fontfamily'] ?? null;

			if ( ! is_string( $family ) ) {
				continue;
			}

			if ( isset( $installed[ sanitize_title( $family ) ] ) || isset( $installed[ $this->slug_for( $family ) ] ) ) {
				unset( $fonts[ $key ] );
			}
		}

		return $fonts;
	}

	/**
	 * The slugs of the fonts installed from the Font Library.
	 *
	 * A family installed there is listed instead of the Customizer's copy of it, so the
	 * picker shows it once, with every style the install brought.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, true>
	 */
	private function installed_slugs(): array {
		$slugs = [];

		foreach ( $this->installed_fonts() as $font ) {
			$slugs[ $font['slug'] ] = true;
		}

		return $slugs;
	}

	/**
	 * The slugs of the fonts installed from the Font Library with a `fontFace`, since core
	 * prints no `@font-face` without one.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, true>
	 */
	private function installed_slugs_with_faces(): array {
		$slugs = [];

		foreach ( $this->installed_fonts() as $font ) {
			if ( ! empty( $font['fontFace'] ) ) {
				$slugs[ $font['slug'] ] = true;
			}
		}

		return $slugs;
	}

	/**
	 * The fonts installed from the Font Library, as the user's global styles hold them.
	 *
	 * @since 1.6.0
	 *
	 * @return array<int, array{slug: string, fontFace?: array<int, array<string, string>>}>
	 */
	private function installed_fonts(): array {
		$settings  = WP_Theme_JSON_Resolver::get_user_data()->get_settings();
		$installed = $settings['typography']['fontFamilies']['custom'] ?? [];
		$fonts     = [];

		foreach ( is_array( $installed ) ? $installed : [] as $entry ) {
			if ( is_array( $entry ) && isset( $entry['slug'] ) ) {
				$fonts[] = $entry;
			}
		}

		return $fonts;
	}

	/**
	 * The distinct families the Customizer fonts use, as theme.json entries.
	 *
	 * @since 1.6.0
	 *
	 * @return array<int, array{name: string, slug: string, fontFamily: string}>
	 */
	private function families(): array {
		$families = [];

		foreach ( self::OPTIONS as $option ) {
			$entry = $this->entry( kadence()->option( $option ) );

			if ( null !== $entry ) {
				$families[ $entry['slug'] ] = $entry;
			}
		}

		return array_values( $families );
	}

	/**
	 * The theme.json entry for a Customizer font's family.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $font Customizer typography.
	 * @return array{name: string, slug: string, fontFamily: string}|null Null when the font sets no family.
	 */
	private function entry( $font ): ?array {
		if ( ! is_array( $font ) || ! is_string( $font['family'] ?? null ) ) {
			return null;
		}

		$family = trim( $font['family'] );

		if ( '' === $family || 'inherit' === $family ) {
			return null;
		}

		$entry = true === ( $font['google'] ?? false ) ? $this->google_entry( $family, $font ) : $this->stack_entry( $family );

		return '' === $entry['slug'] ? null : $entry;
	}

	/**
	 * The entry for a Google font.
	 *
	 * @since 1.6.0
	 *
	 * @param string                                                  $family The family name.
	 * @param array{family: string, google?: bool, fallback?: string} $font   Customizer typography.
	 * @return array{name: string, slug: string, fontFamily: string}
	 */
	private function google_entry( string $family, array $font ): array {
		return [
			'name'       => $family,
			'slug'       => $this->slug_for( $family ),
			'fontFamily' => '"' . str_replace( '"', '', $family ) . '", ' . $this->fallback( $font ),
		];
	}

	/**
	 * The entry for a system or custom font stack.
	 *
	 * @since 1.6.0
	 *
	 * @param string $stack The CSS font-family value.
	 * @return array{name: string, slug: string, fontFamily: string}
	 */
	private function stack_entry( string $stack ): array {
		if ( str_starts_with( $stack, '-apple-system' ) ) {
			return [
				'name'       => __( 'System Default', 'kadence' ),
				'slug'       => self::SYSTEM_SLUG,
				'fontFamily' => $stack,
			];
		}

		$name = trim( explode( ',', $stack )[0], " \t\n\r\"'" );

		return [
			'name'       => $name,
			'slug'       => $this->slug_for( $name ),
			'fontFamily' => $stack,
		];
	}

	/**
	 * The font a Google family falls back to, as `Kadence_CSS::render_font()` picks it for the
	 * element's own `font-family`.
	 *
	 * @since 1.6.0
	 *
	 * @param array{family: string, google?: bool, fallback?: string} $font Customizer typography.
	 * @return string
	 */
	private function fallback( array $font ): string {
		$fallback = $font['fallback'] ?? '';

		if ( ! is_string( $fallback ) || '' === $fallback ) {
			return 'var(--global-fallback-font)';
		}

		if ( 'handwriting' === $fallback ) {
			return 'cursive';
		}

		return 'display' === $fallback ? 'var(--global-display-fallback-font)' : $fallback;
	}

	/**
	 * Turns a font name into a slug that core's kebab-casing leaves unchanged.
	 *
	 * Core runs preset slugs through `_wp_to_kebab_case()` when it builds the CSS variable,
	 * so a slug that doesn't survive it would name one variable and register another.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name The font name.
	 * @return string
	 */
	private function slug_for( string $name ): string {
		$slug = _wp_to_kebab_case( $name );

		// Kebab-casing isn't always idempotent (digit boundaries), so settle on the fixed point.
		for ( $i = 0; $i < 3; $i++ ) {
			$next = _wp_to_kebab_case( $slug );

			if ( $next === $slug ) {
				break;
			}

			$slug = $next;
		}

		return $slug;
	}
}
