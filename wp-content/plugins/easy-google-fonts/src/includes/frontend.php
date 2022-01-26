<?php
/**
 * Frontend Functionality
 *
 * Contains the logic to output styles
 * the frontend.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Frontend;

use EGF\Settings as Settings;
use EGF\Utils as Utils;

/**
 * Output preconnect tag
 */
function output_preconnect_tag() {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<?php
}
add_action( 'wp_head', __NAMESPACE__ . '\\output_preconnect_tag', 10 );
add_action( 'admin_head', __NAMESPACE__ . '\\output_preconnect_tag', 10 );


/**
 * Enqueue Stylesheets
 */
function enqueue_stylesheets() {
	$font_families = [];

	foreach ( Settings\get_saved_settings() as $setting ) {
		if (
			empty( $setting['font_name'] ) ||
			empty( $setting['font_id'] ) ||
			Utils\is_default_font( $setting['font_id'] )
		) {
			continue;
		}

		$font_family = str_replace( ' ', '+', $setting['font_name'] );

		if ( ! isset( $font_families[ $font_family ] ) ) {
			$font_families[ $font_family ] = [];
		}

		$is_regular_font_weight = empty( $setting['font_weight_style'] ) || 'regular' === $setting['font_weight_style'];

		$font_families[ $font_family ][] = $is_regular_font_weight ? 400 : $setting['font_weight_style'];
	}

	$base_url    = 'https://fonts.googleapis.com/css2?display=swap';
	$request_url = $base_url;

	foreach ( $font_families as $family => $variants ) {
		// Sort variant tuples.
		usort(
			$variants,
			function( $a, $b ) {
				$a_is_italic = \strpos( $a, 'italic' ) !== false;
				$b_is_italic = \strpos( $b, 'italic' ) !== false;

				$a = \str_replace( 'italic', '', $a );
				$b = \str_replace( 'italic', '', $b );

				$font_weight_a = empty( $a ) ? '400' : $a;
				$font_weight_b = empty( $b ) ? '400' : $b;

				if ( $a_is_italic && ! $b_is_italic ) {
					return 1;
				}

				if ( ! $a_is_italic && $b_is_italic ) {
					return -1;
				}

				if ( $font_weight_a === $font_weight_b ) {
					return 0;
				}

				return ( $font_weight_a < $font_weight_b ) ? -1 : 1;
			}
		);

		// Determine variants to load.
		$has_italic               = false;
		$load_additional_variants = false;

		foreach ( $variants as $variant ) {
			if ( 400 !== $variant && 'regular' !== $variant ) {
				$load_additional_variants = true;
			}

			if ( false !== \strpos( $variant, 'italic' ) ) {
				$has_italic = true;
			}
		}

		// Construct url fragments.
		$url_fragment = "&family={$family}";

		// Regular only (no italic).
		if ( ! $has_italic && ! $load_additional_variants ) {
			$request_url .= $url_fragment;
			continue;
		}

		// Regular only (italic).
		if ( $has_italic && ! $load_additional_variants ) {
			$request_url .= "{$url_fragment}:ital@1";
			continue;
		}

		// Additional variants (no italic).
		if ( ! $has_italic && $load_additional_variants ) {
			$request_url .= "{$url_fragment}:wght@" . \implode( ';', array_unique( $variants ) );
			continue;
		}

		// Additional variants (some italic).
		$additional_variants = array_map(
			function( $variant ) {
				$is_italic   = \strpos( $variant, 'italic' ) !== false;
				$font_weight = \str_replace( 'italic', '', $variant );
				$font_weight = empty( $font_weight ) ? '400' : $font_weight;

				return $is_italic ? "1,{$font_weight}" : "0,{$font_weight}";
			},
			$variants
		);

		if ( $has_italic && $load_additional_variants ) {
			$request_url .= "{$url_fragment}:ital,wght@" . \implode( ';', array_unique( $additional_variants ) );
		}
	}

	if ( $request_url === $base_url ) {
		return;
	}

	echo "<link href='{$request_url}' rel='stylesheet'>"; // @codingStandardsIgnoreLine
}
add_action( 'wp_head', __NAMESPACE__ . '\\enqueue_stylesheets' );
add_action( 'admin_head', __NAMESPACE__ . '\\enqueue_stylesheets' );

/**
 * Output Inline Styles In <head>
 */
function output_styles() {
	if ( is_customize_preview() ) {
		output_frontend_preview_css();
	} else {
		output_frontend_css();
	}
}
add_action( 'wp_head', __NAMESPACE__ . '\\output_styles', 1000 );

/**
 * Output CSS for Frontend.
 */
function output_frontend_css() {
	$settings = Settings\get_saved_settings();
	$config   = Settings\get_settings_config();
	?>
	<style id="egf-frontend-styles" type="text/css">
		<?php
		foreach ( $settings as $setting => $setting_props ) {
			$setting_config = $config[ $setting ];
			$selector       = $setting_config['properties']['selector'];
			$force_styles   = $setting_config['properties']['force_styles'];
			$min_screen     = $setting_config['properties']['min_screen'];
			$max_screen     = $setting_config['properties']['max_screen'];
			$media_query    = Utils\get_media_query( $min_screen, $max_screen );

			if ( ! $selector ) {
				continue;
			}

			$css  = '';
			$css .= $media_query['open'];
			$css .= "{$selector} {";
			foreach ( get_css_property_mappings() as $prop => $css_prop ) {
				$setting_prop = $setting_props[ $prop ];
				$has_units    = prop_has_units( $prop );
				$no_style_set = $has_units ? empty( $setting_prop['amount'] ) : empty( $setting_prop );

				if ( $no_style_set ) {
					continue;
				}

				if ( 'font_name' === $prop ) {
					$setting_prop = "'{$setting_props[ $prop ]}'";
				}

				$css .= "{$css_prop}: ";
				$css .= $has_units ? $setting_prop['amount'] . $setting_prop['unit'] : $setting_prop;
				$css .= 'font_name' === $prop ? ', ' . implode( ', ', Utils\get_fallback_fonts( $setting_props[ $prop ] ) ) : '';
				$css .= $force_styles ? '!important;' : ';';
			}
			$css .= '} ';
			$css .= $media_query['close'];

			echo $css; // @codingStandardsIgnoreLine
		}
		?>
	</style>
	<?php
}

/**
 * Output CSS for Frontend (in customizer preview context).
 */
function output_frontend_preview_css() {
	$settings = Settings\get_saved_settings();
	$config   = Settings\get_settings_config();

	foreach ( $settings as $setting => $setting_props ) {
		$setting_config = $config[ $setting ];
		$selector       = $setting_config['properties']['selector'];
		$force_styles   = $setting_config['properties']['force_styles'];
		$min_screen     = $setting_config['properties']['min_screen'];
		$max_screen     = $setting_config['properties']['max_screen'];
		$media_query    = Utils\get_media_query( $min_screen, $max_screen );

		if ( ! $selector ) {
			continue;
		}

		$css = '';

		foreach ( get_css_property_mappings() as $prop => $css_prop ) {
			$setting_prop = $setting_props[ $prop ];
			$has_units    = prop_has_units( $prop );
			$no_style_set = $has_units ? empty( $setting_prop['amount'] ) : empty( $setting_prop );

			if ( $no_style_set ) {
				continue;
			}

			if ( 'font_name' === $prop ) {
				$setting_prop = "'{$setting_props[ $prop ]}'";
			}

			$style_id = "egf-font-{$setting}-{$css_prop}";

			$css .= "<style id='{$style_id}' type='text/css'>";
			$css .= $media_query['open'];
			$css .= "{$selector} {";
			$css .= "{$css_prop}: ";
			$css .= $has_units ? $setting_prop['amount'] . $setting_prop['unit'] : $setting_prop;
			$css .= 'font_name' === $prop ? ', ' . implode( ', ', Utils\get_fallback_fonts( $setting_props[ $prop ] ) ) : '';
			$css .= $force_styles ? '!important;' : ';';
			$css .= '} ';
			$css .= $media_query['close'];
			$css .= '</style>';
		}

		echo $css; // @codingStandardsIgnoreLine
	}
}

/**
 * Get CSS Property Mapping
 */
function get_css_property_mappings() {
	return [
		'background_color'           => 'background-color',
		'display'                    => 'display',
		'font_color'                 => 'color',
		'font_name'                  => 'font-family',
		'font_size'                  => 'font-size',
		'font_style'                 => 'font-style',
		'font_weight'                => 'font-weight',
		'letter_spacing'             => 'letter-spacing',
		'line_height'                => 'line-height',
		'margin_top'                 => 'margin-top',
		'margin_bottom'              => 'margin-bottom',
		'margin_left'                => 'margin-left',
		'margin_right'               => 'margin-right',
		'padding_top'                => 'padding-top',
		'padding_bottom'             => 'padding-bottom',
		'padding_left'               => 'padding-left',
		'padding_right'              => 'padding-right',
		'text_decoration'            => 'text-decoration',
		'text_transform'             => 'text-transform',
		'border_top_color'           => 'border-top-color',
		'border_top_style'           => 'border-top-style',
		'border_top_width'           => 'border-top-width',
		'border_bottom_color'        => 'border-bottom-color',
		'border_bottom_style'        => 'border-bottom-style',
		'border_bottom_width'        => 'border-bottom-width',
		'border_left_color'          => 'border-left-color',
		'border_left_style'          => 'border-left-style',
		'border_left_width'          => 'border-left-width',
		'border_right_color'         => 'border-right-color',
		'border_right_style'         => 'border-right-style',
		'border_right_width'         => 'border-right-width',
		'border_radius_top_left'     => 'border-top-left-radius',
		'border_radius_top_right'    => 'border-top-right-radius',
		'border_radius_bottom_right' => 'border-bottom-right-radius',
		'border_radius_bottom_left'  => 'border-bottom-left-radius',
	];
}

/**
 * Field Has Units
 *
 * @param string $prop Font control setting prop to check.
 * @return boolean true if the prop has units, otherwise false.
 */
function prop_has_units( $prop ) {
	return in_array(
		$prop,
		[
			'font_size',
			'letter_spacing',
			'margin_top',
			'margin_bottom',
			'margin_left',
			'margin_right',
			'padding_top',
			'padding_bottom',
			'padding_left',
			'padding_right',
			'border_top_width',
			'border_bottom_width',
			'border_left_width',
			'border_right_width',
			'border_radius_top_left',
			'border_radius_top_right',
			'border_radius_bottom_right',
			'border_radius_bottom_left',
		],
		true
	);
}


