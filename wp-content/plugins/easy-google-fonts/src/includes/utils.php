<?php
/**
 * Utils
 *
 * Contains common font utils to handle google fonts.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Utils;

/**
 * Get Google API Key
 *
 * Developers: To provide a default api key
 * hook into the 'option_tt-font-google-api-key'
 * filter.
 *
 * @return string Saved google API Key.
 */
function get_google_api_key() {
	return get_option( 'tt-font-google-api-key', '' );
}

/**
 * Get Key from Setting ID
 *
 * Used to return the array key from the setting
 * id in the customizer.
 * e.g. 'tt_font_theme_options[my_key]' returns 'my_key'
 * e.g. 'tt_font_theme_options[my_key][setting]' returns 'my_key'
 *
 * @param string $setting_id   Full setting id key.
 * @param string $setting_base Base setting array key.
 * @return string|boolean Key if located or false.
 */
function get_key_from_setting_id( $setting_id, $setting_base = 'tt_font_theme_options' ) {
	$id_pattern = '/^' . $setting_base . '\[([a-zA-Z0-9_-]+)\]$/';

	if ( preg_match( $id_pattern, $setting_id, $matches ) ) {
		return $matches[1];
	}

	$id_pattern = '/^' . $setting_base . '\[([a-zA-Z0-9_-]+)\]\[([a-zA-Z0-9_-]+)\]$/';

	if ( preg_match( $id_pattern, $setting_id, $matches ) ) {
		return $matches[1];
	}

	return false;
}

/**
 * Is Default Font
 *
 * @param string $font_slug Lowercase, underscore separated font slug.
 * @return boolean true if default font, false otherwise.
 */
function is_default_font( $font_slug ) {
	return \in_array(
		$font_slug,
		array_keys( get_default_fonts() ),
		true
	);
}

/**
 * Get Default Fonts
 */
function get_default_fonts() {
	$fonts = [
		'arial'               => [ 'family' => 'Arial' ],
		'century_gothic'      => [ 'family' => 'Century Gothic' ],
		'courier_new'         => [ 'family' => 'Courier New' ],
		'georgia'             => [ 'family' => 'Georgia' ],
		'helvetica'           => [ 'family' => 'Helvetica' ],
		'impact'              => [ 'family' => 'Impact' ],
		'lucida_console'      => [ 'family' => 'Lucida Console' ],
		'lucida_sans_unicode' => [ 'family' => 'Lucida Sans Unicode' ],
		'palatino_linotype'   => [ 'family' => 'Palatino linotype' ],
		'sans-serif'          => [ 'family' => 'sans-serif' ],
		'serif'               => [ 'family' => 'serif' ],
		'tahoma'              => [ 'family' => 'Tahoma' ],
		'trebuchet_ms'        => [ 'family' => 'Trebuchet MS' ],
		'verdana'             => [ 'family' => 'Verdana' ],
	];

	foreach ( $fonts as $id => $props ) {
		$fonts[ $id ]['name']     = $id;
		$fonts[ $id ]['variants'] = [ '400', '400italic', '700', '700italic' ];
	}

	return apply_filters( 'egf_get_default_fonts', $fonts );
}

/**
 * Get Fallback Fonts
 *
 * @param string $font_name Font family to get fallback for.
 */
function get_fallback_fonts( $font_name = '' ) {
	return apply_filters(
		'egf_get_fallback_fonts',
		[
			'sans-serif',
		],
		$font_name
	);
}


/**
 * Get Theme Color Palette
 *
 * Gets any registered colors
 */
function get_theme_color_palette() {
	$theme_color_palette = get_theme_support( 'editor-color-palette' );

	if ( empty( $theme_color_palette ) ) {
		$color_palette = get_color_palette_fallback();
	} else {
		list( $color_palette ) = $theme_color_palette;
	}

	return apply_filters(
		'egf_get_theme_color_palette',
		$color_palette
	);
}

/**
 * Get Color Palette Fallback
 */
function get_color_palette_fallback() {
	return [
		[
			'name'  => 'Black',
			'color' => '#000000',
		],
		[
			'name'  => 'White',
			'color' => '#ffffff',
		],
		[
			'name'  => 'Red',
			'color' => '#f44336',
		],
		[
			'name'  => 'Orange',
			'color' => '#ff5722',
		],
		[
			'name'  => 'Yellow',
			'color' => '#ffeb3b',
		],
		[
			'name'  => 'Green',
			'color' => '#4caf50',
		],
		[
			'name'  => 'Blue',
			'color' => '#2196f3',
		],
		[
			'name'  => 'Purple',
			'color' => '#673ab7',
		],
	];
}

/**
 * Get Media Query
 *
 * @param array $min Assoc arr containing 'amount' and 'unit' keys.
 * @param array $max Assoc arr containing 'amount' and 'unit' keys.
 * @return array $media_query
 */
function get_media_query( $min, $max ) {
	$media_query = [
		'open'  => '',
		'close' => '',
	];

	if ( empty( $min['amount'] ) && empty( $max['amount'] ) ) {
		return $media_query;
	}

	$media_query_rules = [];

	if ( ! empty( $min['amount'] ) ) {
		$media_query_rules[] = "(min-width: {$min['amount']}{$min['unit']})";
	}

	if ( ! empty( $max['amount'] ) ) {
		$media_query_rules[] = "(max-width: {$max['amount']}{$max['unit']})";
	}

	$rules = implode( ' and ', $media_query_rules );

	$media_query['open']  = "@media {$rules} {";
	$media_query['close'] = '}';

	return $media_query;
}
