<?php
/**
 * Astra Global color palette
 *
 * @package     Astra
 * @subpackage  Class
 * @link        https://wpastra.com/
 * @since       3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global palette class
 */
class Astra_Global_Palette {
	/**
	 * Indicates if the current palette is dark.
	 *
	 * @var bool $is_dark_palette
	 */
	private static $is_dark_palette;

	/**
	 * Constructor
	 *
	 * @since 3.7.0
	 */
	public function __construct() {
		/**
		 * Support for overriding theme.json from the child theme
		 *
		 * If theme.json is not present in the child theme load Global Color Palette in the editor using add_theme_support( 'editor-color-palette', $editor_palette );.
		 * This is a known issue in Gutenberg - If theme.json is not present in the child theme, it does fallback to the parent theme's theme.json file.
		 * This will be fixed in the future updates of WordPress/Gutenberg.
		 *
		 * @see https://github.com/WordPress/gutenberg/pull/34354
		 */
		$get_stylesheet = get_stylesheet_directory();
		$is_theme_json  = $get_stylesheet . '/theme.json';
		if ( ( get_template_directory() !== $get_stylesheet && false === file_exists( $is_theme_json ) ) || astra_wp_version_compare( '5.8', '<' ) ) {
			add_action( 'after_setup_theme', array( $this, 'support_editor_color_palette' ) );
		}
		add_filter( 'astra_theme_customizer_js_localize', array( $this, 'localize_variables' ) );
		add_filter( 'astra_before_foreground_color_generation', array( $this, 'get_color_by_palette_variable' ) );
		$this->includes();
		add_filter( 'astra_dynamic_theme_css', array( $this, 'global_border_compatibility' ) );
	}

	/**
	 * Modify color palette from Gutenberg.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function support_editor_color_palette() {
		$global_palette = astra_get_option( 'global-color-palette' );
		$editor_palette = $this->format_global_palette( $global_palette );
		add_theme_support( 'editor-color-palette', $editor_palette );
	}

	/**
	 * Format color palette data required to pass for Gutenberg palette.
	 *
	 * @since 3.7.0
	 * @param array $global_palette global palette data.
	 * @return array
	 */
	public function format_global_palette( $global_palette ) {
		$editor_palette = array();
		$labels         = self::get_palette_labels();
		$custom_colors  = self::get_custom_colors();

		if ( isset( $global_palette['palette'] ) ) {
			foreach ( $global_palette['palette'] as $key => $color ) {

				if ( $key >= 9 ) {
					// Custom global colors - skip retired slots and use the user defined name as-is.
					if ( ( ! isset( $custom_colors[ $key - 9 ] ) || ! empty( $custom_colors[ $key - 9 ]['retired'] ) ) ) {
						continue;
					}
					$label = isset( $labels[ $key ] ) ? $labels[ $key ] : '';
				} else {
					/* translators: %s: palette color name. */
					$label = sprintf( __( 'Theme %s', 'astra' ), isset( $labels[ $key ] ) ? $labels[ $key ] : '' );
				}

				$editor_palette[] = array(
					'name'  => $label,
					'slug'  => str_replace( '--', '', self::get_css_variable_prefix() ) . $key,
					'color' => 'var(' . self::get_css_variable_prefix() . $key . ')',
				);
			}
		}
		return $editor_palette;
	}

	/**
	 * Get CSS variable prefix used for styling.
	 *
	 * @since 3.7.0
	 * @return string variable prefix
	 */
	public static function get_css_variable_prefix() {
		return '--ast-global-color-';
	}

	/**
	 * Localize variables used in the customizer.
	 *
	 * @since 3.7.0
	 * @param array $object localize object.
	 * @return array<array-key, mixed> $object localize object.
	 */
	public function localize_variables( $object ) {

		if ( isset( $object['customizer'] ) ) {
			$object['customizer']['globalPaletteStylePrefix']       = self::get_css_variable_prefix();
			$object['customizer']['isElementorActive']              = astra_is_elemetor_active();
			$object['customizer']['isGlobalColorElementorDisabled'] = astra_maybe_disable_global_color_in_elementor();
			$object['customizer']['globalPaletteSlugs']             = self::get_palette_slugs();
			$object['customizer']['globalPaletteLabels']            = self::get_palette_labels();
			$object['customizer']['globalPaletteCustomColors']      = self::get_custom_colors();
			$object['customizer']['globalPaletteCustomColorsLimit'] = self::get_custom_colors_limit();
		}
		return $object;
	}

	/**
	 * Default global palette options.
	 *
	 * @since 3.7.0
	 * @return array Palette options.
	 */
	public static function get_default_color_palette() {
		$update_colors_for_starter_library = Astra_Dynamic_CSS::astra_4_4_0_compatibility();
		$update_color_styles_with_presets  = Astra_Dynamic_CSS::astra_4_5_0_compatibility();
		$update_color_for_forms_ui         = Astra_Dynamic_CSS::astra_4_6_0_compatibility();
		$color_palette_reorganize          = Astra_Dynamic_CSS::astra_4_8_9_compatibility();

		return array(
			'currentPalette' => 'palette_1',
			'palettes'       => $update_color_styles_with_presets ? array(
				'palette_1' => array(
					'#046bd2',
					'#045cb4',
					'#1e293b',
					'#334155',
					$color_palette_reorganize ? '#FFFFFF' : '#F0F5FA',
					$color_palette_reorganize ? '#F0F5FA' : '#FFFFFF',
					$color_palette_reorganize ? '#111111' : ( $update_color_for_forms_ui ? '#D1D5DB' : '#ADB6BE' ),
					$color_palette_reorganize ? ( $update_color_for_forms_ui ? '#D1D5DB' : '#ADB6BE' ) : '#111111',
					'#111111',
				),
				'palette_2' => array(
					'#0067FF',
					'#005EE9',
					'#0F172A',
					'#364151',
					$color_palette_reorganize ? '#FFFFFF' : '#E7F6FF',
					$color_palette_reorganize ? '#E7F6FF' : '#FFFFFF',
					$color_palette_reorganize ? '#070614' : '#D1DAE5',
					$color_palette_reorganize ? '#D1DAE5' : '#070614',
					'#222222',
				),
				'palette_3' => array(
					'#6528F7',
					'#5511F8',
					'#0F172A',
					'#454F5E',
					$color_palette_reorganize ? '#FFFFFF' : '#F2F0FE',
					$color_palette_reorganize ? '#F2F0FE' : '#FFFFFF',
					$color_palette_reorganize ? '#0D0614' : '#D8D8F5',
					$color_palette_reorganize ? '#D8D8F5' : '#0D0614',
					'#222222',
				),
				'palette_4' => array(
					'#0085FF',
					'#0177E3',
					'#FFFFFF',
					'#E7F6FF',
					'#212A37',
					'#0F172A',
					'#4F5B62',
					'#070614',
					'#222222',
				),
			) : array(
				'palette_1' => array(
					'#046bd2',
					'#045cb4',
					'#1e293b',
					'#334155',
					$color_palette_reorganize ? '#FFFFFF' : ( $update_colors_for_starter_library ? '#F0F5FA' : '#f9fafb' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#F0F5FA' : '#f9fafb' ) : '#FFFFFF',
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#111111' : '#cbd5e1' ) : ( $update_colors_for_starter_library ? '#ADB6BE' : '#e2e8f0' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#ADB6BE' : '#e2e8f0' ) : ( $update_colors_for_starter_library ? '#111111' : '#cbd5e1' ),
					$update_colors_for_starter_library ? '#111111' : '#94a3b8',
				),
				'palette_2' => array(
					'#0170B9',
					$update_colors_for_starter_library ? '#045cb4' : '#3a3a3a',
					'#3a3a3a',
					'#4B4F58',
					$color_palette_reorganize ? '#FFFFFF' : ( $update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5' ) : '#FFFFFF',
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#111111' : '#424242' ) : ( $update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7' ) : ( $update_colors_for_starter_library ? '#111111' : '#424242' ),
					$update_colors_for_starter_library ? '#111111' : '#000000',
				),
				'palette_3' => array(
					'#0170B9',
					$update_colors_for_starter_library ? '#045cb4' : '#3a3a3a',
					'#3a3a3a',
					'#4B4F58',
					$color_palette_reorganize ? '#FFFFFF' : ( $update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5' ) : '#FFFFFF',
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#111111' : '#424242' ) : ( $update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7' ),
					$color_palette_reorganize ? ( $update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7' ) : ( $update_colors_for_starter_library ? '#111111' : '#424242' ),
					$update_colors_for_starter_library ? '#111111' : '#000000',
				),
				'palette_4' => array(
					'#0085FF',
					'#0177E3',
					'#FFFFFF',
					'#E7F6FF',
					'#212A37',
					'#0F172A',
					'#4F5B62',
					'#070614',
					'#222222',
				),
			),
			'presets'        => astra_get_palette_presets(),
		);
	}

	/**
	 * Get the key of the selected global color palette.
	 *
	 * @return string|null Palette key if found, otherwise null.
	 */
	public static function astra_get_active_global_palette() {
		$palettes_data   = astra_get_palette_colors();
		$current_palette = isset( $palettes_data['currentPalette'] ) ? $palettes_data['currentPalette'] : 'palette_1';

		/**
		 * Filters the active global palette key.
		 *
		 * @param string $current_palette The active global palette key.
		 *
		 * @return string The filtered active global palette key.
		 * @since 4.10.0
		 */
		return apply_filters( 'astra_get_active_global_palette_key', $current_palette );
	}

	/**
	 * Determines if the specified or active global palette is considered dark.
	 *
	 * @param string $palette_key The key of the palette to check. Defaults to 'current' to check the active global palette.
	 *
	 * @since 4.10.0
	 * @return bool True if the specified or active global palette is dark, otherwise false.
	 */
	public static function is_dark_palette( $palette_key = 'current' ) {
		if ( $palette_key === 'current' ) {
			if ( ! self::$is_dark_palette ) {
				// Determine if the active global palette is 'palette_4', which is considered dark.
				self::$is_dark_palette = self::astra_get_active_global_palette() === 'palette_4';
			}

			// Return the cached result for the active global palette.
			return self::$is_dark_palette;
		}

		// For a specific palette key, directly check if it's 'palette_4'.
		return $palette_key === 'palette_4';
	}

	/**
	 * Get labels for palette colors.
	 *
	 * @since 3.7.0
	 * @return array Palette labels.
	 */
	public static function get_palette_labels() {
		$astra_settings           = Astra_Theme_Options::get_options();
		$color_palette_reorganize = Astra_Dynamic_CSS::astra_4_8_9_compatibility();
		$new_color_palette_labels = apply_filters( 'astra_new_color_labels', $color_palette_reorganize || isset( $astra_settings['new-color-labels'] ) );

		$color_1_label = __( 'Brand', 'astra' );
		$color_2_label = __( 'Alternate Brand', 'astra' );
		$color_3_label = __( 'Headings', 'astra' );
		$color_4_label = __( 'Text', 'astra' );
		$color_5_label = $color_palette_reorganize ? __( 'Primary Background', 'astra' ) : __( 'Secondary Background', 'astra' );
		$color_6_label = $color_palette_reorganize ? __( 'Secondary Background', 'astra' ) : __( 'Primary Background', 'astra' );
		$color_7_label = $color_palette_reorganize ? __( 'Alternate Background', 'astra' ) : __( 'Subtle Background', 'astra' );
		$color_8_label = $color_palette_reorganize ? __( 'Subtle Background', 'astra' ) : __( 'Alternate Background', 'astra' );
		$color_9_label = __( 'Other Supporting', 'astra' );

		$labels = array(
			$new_color_palette_labels ? $color_1_label : __( 'Color 1', 'astra' ),
			$new_color_palette_labels ? $color_2_label : __( 'Color 2', 'astra' ),
			$new_color_palette_labels ? $color_3_label : __( 'Color 3', 'astra' ),
			$new_color_palette_labels ? $color_4_label : __( 'Color 4', 'astra' ),
			$new_color_palette_labels ? $color_5_label : __( 'Color 5', 'astra' ),
			$new_color_palette_labels ? $color_6_label : __( 'Color 6', 'astra' ),
			$new_color_palette_labels ? $color_7_label : __( 'Color 7', 'astra' ),
			$new_color_palette_labels ? $color_8_label : __( 'Color 8', 'astra' ),
			$new_color_palette_labels ? $color_9_label : __( 'Color 9', 'astra' ),
		);

		// Append user defined custom color names so positional lookups cover every palette slot.
		foreach ( self::get_custom_colors() as $custom_color ) {
			$labels[] = $custom_color['name'];
		}

		// Names a child theme declares in its own theme.json rename Astra's palette slots.
		$child_names = array_diff_assoc(
			self::get_theme_json_palette_names( get_stylesheet_directory() ),
			self::get_theme_json_palette_names( get_template_directory() )
		);

		if ( ! empty( $child_names ) ) {
			// Core translates these names for the editor, so match it here.
			$child_text_domain = wp_get_theme()->get( 'TextDomain' );

			foreach ( self::get_palette_slugs() as $index => $slug ) {
				if ( isset( $child_names[ $slug ], $labels[ $index ] ) ) {
					$labels[ $index ] = translate_with_gettext_context( $child_names[ $slug ], 'Color name', $child_text_domain ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain,WordPress.WP.I18n.LowLevelTranslationFunction -- Names come from a child theme's theme.json, mirroring core's own translation of that file.
				}
			}
		}

		/**
		 * Filters the global color palette labels.
		 *
		 * Runs during theme.json resolution, so callbacks must not call global styles
		 * APIs. Return the array with its keys intact; anything else is ignored.
		 * Elementor and the editor-color-palette fallback prefix the label with "Theme ".
		 *
		 * @since 4.13.11
		 * @param array<string> $labels Palette labels in palette slot order.
		 */
		$filtered_labels = apply_filters( 'astra_global_palette_labels', $labels );

		if ( ! is_array( $filtered_labels ) ) {
			return $labels;
		}

		// Different keys would shift labels onto the wrong slots, so ignore such a return.
		if ( array_keys( $filtered_labels ) !== array_keys( $labels ) ) {
			return $labels;
		}

		// Overwrite in place so Astra's own label survives anything the filter cannot supply.
		foreach ( $filtered_labels as $index => $label ) {
			if ( is_string( $label ) && '' !== trim( $label ) ) {
				$labels[ $index ] = $label;
			}
		}

		return $labels;
	}

	/**
	 * Get the palette names a theme declares in its theme.json.
	 *
	 * @since 4.13.11
	 * @param string $theme_directory Absolute path to the theme directory.
	 * @return array Palette names keyed by palette slug.
	 */
	public static function get_theme_json_palette_names( $theme_directory ) {
		static $cache = array();

		if ( isset( $cache[ $theme_directory ] ) ) {
			return $cache[ $theme_directory ];
		}

		$names           = array();
		$theme_json_file = trailingslashit( $theme_directory ) . 'theme.json';

		if ( is_readable( $theme_json_file ) && function_exists( 'wp_json_file_decode' ) ) {
			$theme_json_data = wp_json_file_decode( $theme_json_file, array( 'associative' => true ) );
			$palette         = isset( $theme_json_data['settings']['color']['palette'] ) ? $theme_json_data['settings']['color']['palette'] : array();

			foreach ( (array) $palette as $color_data ) {
				// A malformed theme.json can hold any type here, and a non-string key is fatal.
				if ( ! is_array( $color_data ) || empty( $color_data['slug'] ) || ! is_string( $color_data['slug'] ) ) {
					continue;
				}

				$name = isset( $color_data['name'] ) && is_string( $color_data['name'] ) ? $color_data['name'] : '';

				if ( '' !== $name ) {
					$names[ $color_data['slug'] ] = $name;
				}
			}
		}

		$cache[ $theme_directory ] = $names;

		return $names;
	}

	/**
	 * Get slugs for palette colors.
	 *
	 * @since 3.7.0
	 * @return array Palette slugs.
	 */
	public static function get_palette_slugs() {
		$slugs = array(
			'ast-global-color-0',
			'ast-global-color-1',
			'ast-global-color-2',
			'ast-global-color-3',
			'ast-global-color-4',
			'ast-global-color-5',
			'ast-global-color-6',
			'ast-global-color-7',
			'ast-global-color-8',
		);

		$custom_colors_count = count( self::get_custom_colors() );
		for ( $index = 0; $index < $custom_colors_count; $index++ ) {
			$slugs[] = 'ast-global-color-' . ( 9 + $index );
		}

		return $slugs;
	}

	/**
	 * Get the maximum number of custom global colors allowed.
	 *
	 * @since 4.14.0
	 * @return int Custom colors limit.
	 */
	public static function get_custom_colors_limit() {
		/**
		 * Filters the maximum number of custom global colors a user can add to the palette.
		 *
		 * @param int $limit Maximum number of custom colors. Default 9.
		 *
		 * @since 4.14.0
		 */
		return absint( apply_filters( 'astra_global_palette_custom_colors_limit', 9 ) );
	}

	/**
	 * Get user defined custom global colors metadata.
	 *
	 * Each entry maps to palette slot `9 + index` and carries a `name` and a `retired` flag.
	 * Retired colors keep their values in the palette arrays so adding them back restores
	 * them losslessly, but nothing is emitted or listed for them while removed.
	 *
	 * @since 4.14.0
	 * @return array Custom colors data - array of arrays with `name` and `retired` keys.
	 */
	public static function get_custom_colors() {
		$palette_data  = get_option( 'astra-color-palettes', array() );
		$custom_colors = isset( $palette_data['customColors'] ) && is_array( $palette_data['customColors'] ) ? array_values( $palette_data['customColors'] ) : array();
		$limit         = self::get_custom_colors_limit();
		$sanitized     = array();

		foreach ( $custom_colors as $index => $custom_color ) {
			if ( $index >= $limit ) {
				break;
			}

			$name = isset( $custom_color['name'] ) && is_string( $custom_color['name'] ) ? sanitize_text_field( $custom_color['name'] ) : '';

			$sanitized[] = array(
				/* translators: %d: custom color number. */
				'name'    => '' !== $name ? $name : sprintf( __( 'Custom %d', 'astra' ), (int) $index + 1 ),
				'retired' => ! empty( $custom_color['retired'] ),
			);
		}

		return $sanitized;
	}

	/**
	 * Include required files.
	 *
	 * @since 3.7.0
	 */
	public function includes() {
		require_once ASTRA_THEME_DIR . 'inc/dynamic-css/global-color-palette.php';// PHPCS:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Generate palette CSS required to display on front end.
	 *
	 * @since 3.7.0
	 * @return array palette style array.
	 */
	public static function generate_global_palette_style() {
		$palette_data  = astra_get_option( 'global-color-palette' );
		$custom_colors = self::get_custom_colors();

		$palette_style = array();

		if ( isset( $palette_data['palette'] ) ) {
			foreach ( $palette_data['palette'] as $key => $color ) {
				// Removed custom colors are not emitted - matching the frontend.
				if ( $key >= 9 && ( ! isset( $custom_colors[ $key - 9 ] ) || ! empty( $custom_colors[ $key - 9 ]['retired'] ) ) ) {
					continue;
				}

				$palette_key                   = self::get_css_variable_prefix() . $key;
				$palette_style[ $palette_key ] = $color;
			}
		}

		return $palette_style;
	}

	/**
	 * Pass hex value for global palette to process forground color.
	 *
	 * @since 3.7.0
	 * @param string $color hex color / css variable.
	 * @return string
	 */
	public function get_color_by_palette_variable( $color ) {
		// Check if color is CSS variable.
		if ( 0 === strpos( $color, 'var(--' ) ) {

			$global_palette = astra_get_option( 'global-color-palette' );

			foreach ( $global_palette['palette'] as $palette_index => $value ) {

				if ( 'var(' . self::get_css_variable_prefix() . $palette_index . ')' === $color ) {
					return $value;
				}
			}
		}

		return $color;
	}

	/**
	 * Add dynamic CSS for the global border color.
	 *
	 * @since 3.9.0
	 *
	 * @param  string $dynamic_css          Astra Dynamic CSS.
	 *
	 * @return String Generated dynamic CSS for global border.
	 */
	public function global_border_compatibility( $dynamic_css ) {
		$global_border_color = astra_get_option( 'border-color', '#dddddd' );

		$global_border = '
			:root {
				--ast-border-color : ' . $global_border_color . ';
			}
		';

		return $dynamic_css . Astra_Enqueue_Scripts::trim_css( $global_border );
	}
}

new Astra_Global_Palette();
