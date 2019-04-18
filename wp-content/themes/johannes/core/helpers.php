<?php

/**
 * Debug (log) function
 *
 * Outputs any content into log file in theme root directory
 *
 * @param mixed   $mixed Content to output
 * @since  1.0
 */

if ( !function_exists( 'johannes_log' ) ):
	function johannes_log( $mixed ) {

		if ( !function_exists('WP_Filesystem') || !WP_Filesystem() ) {
			return false;
		}

		if ( is_array( $mixed ) ) {
			$mixed = print_r( $mixed, 1 );
		} else if ( is_object( $mixed ) ) {
			ob_start();
			var_dump( $mixed );
			$mixed = ob_get_clean();
		}

		global $wp_filesystem;
		$existing = $wp_filesystem->get_contents(  get_parent_theme_file_path( 'log' ) );
		$wp_filesystem->put_contents( get_parent_theme_file_path( 'log' ), $existing. $mixed . PHP_EOL );
	}
endif;

/**
 * Get option value from theme options
 *
 * A wrapper function for WordPress native get_option()
 * which gets an option from specific option key (set in theme options panel)
 *
 * @param string  $option Name of the option
 * @param string  $format How to parse the option based on its type
 * @return mixed Specific option value or "false" (if option is not found)
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_option' ) ):
	function johannes_get_option( $option, $format = false ) {

		$johannes_settings = get_option( 'johannes_settings' );

		if ( !isset( $johannes_settings[$option] ) ) {
			$johannes_settings[$option] = johannes_get_default_option( $option );
		}

		if ( empty( $format ) ) {
			return $johannes_settings[$option];
		}

		$value = $johannes_settings[$option];

		switch ( $format ) {

		case 'image' :
			$value = is_array( $johannes_settings[$option] ) && isset( $johannes_settings[$option]['url'] ) ? $johannes_settings[$option]['url'] : '';
			break;
		case 'multi':
			$value = is_array( $johannes_settings[$option] ) && !empty( $johannes_settings[$option] ) ? array_keys( array_filter( $johannes_settings[$option] ) ) : array();
			break;

		case 'font':
			$native_fonts = johannes_get_native_fonts();
			if ( !in_array( $value['font-family'], $native_fonts ) ) {
				$value['font-family'] = "'" . $value['font-family'] . "'";
			}

			break;

		default:
			$value = false;
			break;
		}

		return $value;

	}
endif;


/**
 * Get grid vars
 *
 * We use grid vars for dynamic sizes of specific elements such as generating image sizes and breakpoints etc...
 *
 * @return array
 * @since  1.0
 */

if ( !function_exists( 'johannes_grid_vars' ) ):
	function johannes_grid_vars() {

		$grid['column'] = 50;

		$grid['gutter'] = array(
			'xs' => 15,
			'sm' => 15,
			'md' => 30,
			'lg' => 30,
			'xl' => 48
		);

		$grid['breakpoint'] = array(
			'xs' => 0,
			'sm' => 374,
			'md' => 600,
			'lg' => 900,
			'xl' => 1128
		);

		$grid = apply_filters( 'johannes_modify_grid_vars', $grid );

		return $grid;

	}
endif;


if ( !function_exists( 'johannes_size_by_col' ) ):
	function johannes_size_by_col( $cols ) {
		$grid = johannes_grid_vars();
		return ceil( ( $cols * $grid['column'] ) + ( ( $cols - 1 ) * $grid['gutter']['xl'] ) );
	}
endif;





/**
 * Get presets of options for bulk selection of designs
 *
 * @param mixed   $preset_id
 * @return array
 * @param since   1.0
 */

if ( !function_exists( 'johannes_get_option_presets' ) ):
	function johannes_get_option_presets( $args = array(), $ignore_prefixing = false ) {

		global $johannes_translate;

		$presets = array(

			'layouts' => array(

				'1' => array(
					'alt' => esc_html__( 'Layout 1', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_1.jpg' )
				),

				'2' => array(
					'alt' => esc_html__( 'Layout 2', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_2.jpg' )
				),

				'3' => array(
					'alt' => esc_html__( 'Layout 3', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_3.jpg' )
				),

				'4' => array(
					'alt' => esc_html__( 'Layout 4', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_4.jpg' )
				),

				'5' => array(
					'alt' => esc_html__( 'Layout 5', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_5.jpg' )
				),

				'6' => array(
					'alt' => esc_html__( 'Layout 6', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_6.jpg' )
				),


				'7' => array(
					'alt' => esc_html__( 'Layout 7', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_7.jpg' )
				),

				'8' => array(
					'alt' => esc_html__( 'Layout 8', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_8.jpg' )
				),

				'9' => array(
					'alt' => esc_html__( 'Layout 9', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_9.jpg' )
				),

				'10' => array(
					'alt' => esc_html__( 'Layout 10', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_10.jpg' )
				),


				'11' => array(
					'alt' => esc_html__( 'Layout 11', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_11.jpg' )
				),

				'12' => array(
					'alt' => esc_html__( 'Layout 12', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_12.jpg' )
				),

				'13' => array(
					'alt' => esc_html__( 'Layout 13', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_13.jpg' )
				),

				'14' => array(
					'alt' => esc_html__( 'Layout 14', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_layout_14.jpg' )
				),
			),

			'colors' => array(
				'1' => array(
					'alt' => esc_html__( 'Red', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_1.svg' )
				),

				'2' => array(
					'alt' => esc_html__( 'Pink', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_2.svg' )
				),


				'3' => array(
					'alt' => esc_html__( 'Purple', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_3.svg' )
				),


				'4' => array(
					'alt' => esc_html__( 'Blue', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_4.svg' )
				),


				'5' => array(
					'alt' => esc_html__( 'Teal', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_5.svg' )
				),

				'6' => array(
					'alt' => esc_html__( 'Vegan', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_6.svg' )
				),


				'7' => array(
					'alt' => esc_html__( 'Green', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_7.svg' )
				),


				'8' => array(
					'alt' => esc_html__( 'Orange', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_8.svg' )
				),

				'9' => array(
					'alt' => esc_html__( 'Gold', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_9.svg' )
				),


				'10' => array(
					'alt' => esc_html__( 'Dark Red', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_10.svg' )
				),

				'11' => array(
					'alt' => esc_html__( 'Dark Pink', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_11.svg' )
				),


				'12' => array(
					'alt' => esc_html__( 'Dark Purple', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_12.svg' )
				),

				'13' => array(
					'alt' => esc_html__( 'Dark Blue', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_13.svg' )
				),

				'14' => array(
					'alt' => esc_html__( 'Dark Teal', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_14.svg' )
				),

				'15' => array(
					'alt' => esc_html__( 'Dark Vegan', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_15.svg' )
				),

				'16' => array(
					'alt' => esc_html__( 'Dark Green', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_16.svg' )
				),

				'17' => array(
					'alt' => esc_html__( 'Dark Orange', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_17.svg' )
				),

				'18' => array(
					'alt' => esc_html__( 'Dark Gold', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_18.svg' )
				),

				'19' => array(
					'alt' => esc_html__( 'Sandy Beach', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_19.svg' )
				),

				'20' => array(
					'alt' => esc_html__( 'Monochromatic', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_20.svg' )
				),

				'21' => array(
					'alt' => esc_html__( 'Olive', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_21.svg' )
				),

				'22' => array(
					'alt' => esc_html__( 'Bubble Gum', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_22.svg' )
				),

				'23' => array(
					'alt' => esc_html__( 'Pistachio', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_23.svg' )
				),

				'24' => array(
					'alt' => esc_html__( 'Espresso', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_24.svg' )
				),				

				'25' => array(
					'alt' => esc_html__( 'Old Gold', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_25.svg' )
				),

				'26' => array(
					'alt' => esc_html__( 'Deep Ocean', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_26.svg' )
				),

				'27' => array(
					'alt' => esc_html__( 'Baby Blue', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_color_27.svg' )
				),

			),

			'fonts' => array(

				'1' => array(
					'alt' => esc_html__( 'Muli Bold', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_1.svg' )
				),

				'2' => array(
					'alt' => esc_html__( 'Roboto Light', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_2.svg' )
				),

				'3' => array(
					'alt' => esc_html__( 'Source Serif Pro', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_3.svg' )
				),

				'4' => array(
					'alt' => esc_html__( 'Satisfy', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_4.svg' )
				),

				'5' => array(
					'alt' => esc_html__( 'Playfair Display', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_5.svg' )
				),

				'6' => array(
					'alt' => esc_html__( 'Abril', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_6.svg' )
				),

				'7' => array(
					'alt' => esc_html__( 'Rajdhani', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_7.svg' )
				),

				'8' => array(
					'alt' => esc_html__( 'Exo 2', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_8.svg' )
				),

				'9' => array(
					'alt' => esc_html__( 'Roboto Slab', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_9.svg' )
				),

				'10' => array(
					'alt' => esc_html__( 'Alegreya', 'johannes' ),
					'src' => get_parent_theme_file_uri( '/assets/img/admin/preset_font_10.svg' )
				),
			),
		);


		if ( !empty( $args ) ) {
			$new_presets = array();

			foreach ( $args as $type => $id ) {

				if ( array_key_exists( $type, $presets ) && array_key_exists( $id, $presets[$type] ) ) {
					$new_presets[$type][$id] = array();
				}

			}

			$presets = $new_presets;

		}

		foreach ( $presets as $type => $items ) {

			foreach ( $items as $slug => $item ) {

				$preset = array();
				$packed = array();

				include get_parent_theme_file_path( '/core/admin/presets/'.$type.'/'.$slug.'.php' );

				if ( !$ignore_prefixing ) {

					foreach ( $preset as $key => $value ) {
						$packed['johannes_settings['.$key.']'] = $value; //Kirki why???
					}

					$presets[$type][$slug]['settings'] = $packed;

				} else {
					$presets[$type][$slug]['settings'] = $preset;
				}

			}

		}

		$presets = apply_filters( 'johannes_modify_option_presets', $presets, $args );

		return $presets;


	}
endif;


/**
 * Check if RTL mode is enabled
 *
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_is_rtl' ) ):
	function johannes_is_rtl() {

		if ( johannes_get_option( 'rtl_mode' ) ) {
			$rtl = true;
			//Check if current language is excluded from RTL
			$rtl_lang_skip = explode( ",", johannes_get_option( 'rtl_lang_skip' ) );
			if ( !empty( $rtl_lang_skip ) ) {
				$locale = get_locale();
				if ( in_array( $locale, $rtl_lang_skip ) ) {
					$rtl = false;
				}
			}
		} else {
			$rtl = false;
		}

		return $rtl;
	}
endif;



/**
 * Generate dynamic css
 *
 * Function parses theme options and generates css code dynamically
 *
 * @return string Generated css code
 * @since  1.0
 */

if ( !function_exists( 'johannes_generate_dynamic_css' ) ):
	function johannes_generate_dynamic_css() {
		ob_start();
		get_template_part( 'assets/css/dynamic-css' );
		$output = ob_get_contents();
		ob_end_clean();

		$output = johannes_compress_css_code( $output );

		return $output;
	}
endif;

/**
 * Generate dynamic css
 *
 * Function parses theme options and generates css code dynamically
 *
 * @return string Generated css code
 * @since  1.0
 */
if ( !function_exists( 'johannes_generate_dynamic_editor_css' ) ):
	function johannes_generate_dynamic_editor_css() {
		ob_start();
		get_template_part( 'assets/css/admin/dynamic-editor-css' );
		$output = ob_get_contents();
		ob_end_clean();
		$output = johannes_compress_css_code( $output );

		return $output;
	}
endif;


/**
 * Get JS settings
 *
 * Function creates list of settings from thme options to pass
 * them to global JS variable so we can use it in JS files
 *
 * @return array List of JS settings
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_js_settings' ) ):
	function johannes_get_js_settings() {
		$js_settings = array();

		$js_settings['rtl_mode'] = johannes_is_rtl() ? true : false;
		$js_settings['header_sticky'] = johannes_get_option( 'header_sticky' ) ? true : false;
		$js_settings['header_sticky_offset'] = absint( johannes_get_option( 'header_sticky_offset' ) );
		$js_settings['header_sticky_up'] = johannes_get_option( 'header_sticky_up' ) ? true : false;
		$js_settings['popup'] = johannes_get_option( 'popup' ) ? true : false;
		$js_settings['go_to_top'] = johannes_get_option( 'go_to_top' ) ? true : false;
		$js_settings['grid'] = johannes_grid_vars();

		$js_settings = apply_filters( 'johannes_modify_js_settings', $js_settings );

		return $js_settings;
	}
endif;


/**
 * Get all translation options
 *
 * @return array Returns list of all translation strings available in theme options panel
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_translate_options' ) ):
	function johannes_get_translate_options() {
		global $johannes_translate;
		$johannes_translate = apply_filters( 'johannes_modify_translate_options', $johannes_translate );
		return $johannes_translate;
	}
endif;


/**
 * Generate fonts link
 *
 * Function creates font link from fonts selected in theme options
 *
 * @return string
 * @since  1.0
 */

if ( !function_exists( 'johannes_generate_fonts_link' ) ):
	function johannes_generate_fonts_link() {


		$fonts = array();
		$fonts[] = johannes_get_option( 'main_font' );
		$fonts[] = johannes_get_option( 'h_font' );
		$fonts[] = johannes_get_option( 'nav_font' );
		$fonts[] = johannes_get_option( 'button_font' );
		$unique = array(); //do not add same font links
		$link = array();
		$native = johannes_get_native_fonts();
		$protocol = is_ssl() ? 'https://' : 'http://';

		//print_r($fonts);

		foreach ( $fonts as $font ) {
			if ( !in_array( $font['font-family'], $native ) ) {
				$unique[$font['font-family']][] = $font['variant'];
			}
		}

		foreach ( $unique as $family => $variants ) {
			$link[$family] = $family;
			$link[$family] .= ':' . implode( ",", array_unique( $variants ) );
		}

		if ( !empty( $link ) ) {
			$query_args = array(
				'family' =>  implode( '|', $link )
			);
			$fonts_url = add_query_arg( $query_args, $protocol . 'fonts.googleapis.com/css' );

			return esc_url_raw( $fonts_url );
		}

		return '';

	}
endif;


/**
 * Get native fonts
 *
 *
 * @return array List of native fonts
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_native_fonts' ) ):
	function johannes_get_native_fonts() {

		$fonts = array(
			"Arial, Helvetica, sans-serif",
			"'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif",
			"'Comic Sans MS', cursive",
			"Courier, monospace",
			"Garamond, serif",
			"Georgia, serif",
			"Impact, Charcoal, sans-serif",
			"'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			"Tahoma,Geneva, sans-serif",
			"'Times New Roman', Times,serif",
			"'Trebuchet MS', Helvetica, sans-serif",
			"Verdana, Geneva, sans-serif"
		);

		return $fonts;
	}
endif;


/**
 * Get list of image sizes
 *
 * @return array
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_image_sizes' ) ):
	function johannes_get_image_sizes() {

		$sizes = array(
			'johannes-a' => array( 'title' => esc_html__( 'A', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'layout_a' ), 'crop' => true ),
			'johannes-b' => array( 'title' => esc_html__( 'B', 'johannes' ), 'w' => johannes_size_by_col( 8 ), 'ratio' => johannes_get_image_ratio( 'layout_b' ), 'crop' => true ),
			'johannes-c' => array( 'title' => esc_html__( 'C', 'johannes' ), 'w' => johannes_size_by_col( 6 ), 'ratio' => johannes_get_image_ratio( 'layout_c' ), 'crop' => true ),
			'johannes-d' => array( 'title' => esc_html__( 'D', 'johannes' ), 'w' => johannes_size_by_col( 4 ), 'ratio' => johannes_get_image_ratio( 'layout_d' ), 'crop' => true ),
			'johannes-e' => array( 'title' => esc_html__( 'E', 'johannes' ), 'w' => johannes_size_by_col( 6 ), 'ratio' => johannes_get_image_ratio( 'layout_e' ), 'crop' => true ),
			'johannes-f' => array( 'title' => esc_html__( 'F', 'johannes' ), 'w' => johannes_size_by_col( 2.67 ), 'ratio' => johannes_get_image_ratio( 'layout_f' ), 'crop' => true ),

			'johannes-fa-a' => array( 'title' => esc_html__( 'Featured A', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'layout_fa_a_height' ), 'crop' => true ),
			'johannes-fa-b' => array( 'title' => esc_html__( 'Featured B', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'layout_fa_b' ), 'crop' => true ),
			'johannes-fa-c' => array( 'title' => esc_html__( 'Featured C', 'johannes' ), 'w' => johannes_size_by_col( 6 ), 'ratio' => johannes_get_image_ratio( 'layout_fa_c' ), 'crop' => true ),
			'johannes-fa-d' => array( 'title' => esc_html__( 'Featured D', 'johannes' ), 'w' => johannes_size_by_col( 4 ), 'ratio' => johannes_get_image_ratio( 'layout_fa_d' ), 'crop' => true ),
			'johannes-fa-e' => array( 'title' => esc_html__( 'Featured E', 'johannes' ), 'w' => johannes_size_by_col( 5 ), 'ratio' => johannes_get_image_ratio( 'layout_fa_e' ), 'crop' => true ),

			'johannes-single-1' => array( 'title' => esc_html__( 'Single post layout 1', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'single_layout_1' ), 'crop' => true ),
			'johannes-single-2' => array( 'title' => esc_html__( 'Single post layout 2', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'single_layout_2' ), 'crop' => true ),
			'johannes-single-3' => array( 'title' => esc_html__( 'Single post layout 3', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'single_layout_3_height' ), 'crop' => true ),
			'johannes-single-4' => array( 'title' => esc_html__( 'Single post layout 4', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'single_layout_4_height' ), 'crop' => true ),
			'johannes-single-5' => array( 'title' => esc_html__( 'Single post layout 5', 'johannes' ), 'w' => johannes_size_by_col( 5 ), 'ratio' => johannes_get_image_ratio( 'single_layout_5' ), 'crop' => true ),

			'johannes-page-1' => array( 'title' => esc_html__( 'Page layout 1', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'page_layout_1' ), 'crop' => true ),
			'johannes-page-2' => array( 'title' => esc_html__( 'Page layout 2', 'johannes' ), 'w' => johannes_size_by_col( 12 ), 'ratio' => johannes_get_image_ratio( 'page_layout_2' ), 'crop' => true ),
			'johannes-page-3' => array( 'title' => esc_html__( 'Page layout 3', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'page_layout_3_height' ), 'crop' => true ),
			'johannes-page-4' => array( 'title' => esc_html__( 'Page layout 4', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'page_layout_4_height' ), 'crop' => true ),

			'johannes-wa-1' => array( 'title' => esc_html__( 'Welcome area layout 1', 'johannes' ), 'w' => johannes_size_by_col( 5 ), 'ratio' => johannes_get_image_ratio( 'wa_layout_1' ), 'crop' => true ),
			'johannes-wa-2' => array( 'title' => esc_html__( 'Welcome area layout 2', 'johannes' ), 'w' => johannes_size_by_col( 6 ), 'ratio' => johannes_get_image_ratio( 'wa_layout_2' ), 'crop' => true ),
			'johannes-wa-3' => array( 'title' => esc_html__( 'Welcome area layout 3 ', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'wa_layout_3_height' ), 'crop' => true ),
			'johannes-wa-4' => array( 'title' => esc_html__( 'Welcome area layout 4', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'wa_layout_4_height' ), 'crop' => true ),

			'johannes-archive-2' => array( 'title' => esc_html__( 'Archive layout 2', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'archive_layout_2_height' ), 'crop' => true ),
			'johannes-archive-3' => array( 'title' => esc_html__( 'Archive layout 3', 'johannes' ), 'w' => 1920, 'h' => johannes_get_option( 'archive_layout_3_height' ), 'crop' => true ),
		);

		$disable_img_sizes = johannes_get_option( 'disable_img_sizes' );

		if ( !empty( $disable_img_sizes ) ) {
			$disable_img_sizes = array_keys( array_filter( $disable_img_sizes ) );
		}

		if ( !empty( $disable_img_sizes ) ) {
			foreach ( $disable_img_sizes as $size_id ) {
				unset( $sizes[$size_id] );
			}
		}

		foreach ( $sizes as $key => $size ) {

			if ( !isset( $size['ratio'] ) ) {
				$sizes[$key]['cover'] = true;
				continue;
			}

			if ( $size['ratio'] == 'original' ) {
				$size['h'] = 99999;
				$size['crop'] = false;
			} else {
				$size['h'] =  johannes_calculate_image_height( $size['w'], $size['ratio'] );
			}

			unset( $size['ratio'] );
			$sizes[$key] = $size;
		}

		//print_r( $sizes );

		$sizes = apply_filters( 'johannes_modify_image_sizes', $sizes );

		return $sizes;
	}
endif;


/**
 * Gets an image ratio setting for a specific layout
 *
 * @param string  $option ID
 * @return string
 */
if ( !function_exists( 'johannes_get_image_ratio' ) ):
	function johannes_get_image_ratio( $layout ) {
		$ratio = johannes_get_option( $layout . '_img_ratio' );
		$custom_ratio = johannes_get_option( $layout . '_img_custom' );

		if ( $ratio === "custom" && !empty( $custom_ratio ) ) {
			$ratio = str_replace( ":", "_", $custom_ratio );
		}

		$ratio = apply_filters( 'johannes_modify_' . $layout . '_image_ratio', $ratio );

		return $ratio;
	}
endif;


/**
 * Parse image height
 *
 * Calculate an image size based on a given ratio and width
 *
 * @param int     $width
 * @param string  $ration in 'w_h' format
 * @return int $height
 * @since  1.0
 */

if ( !function_exists( 'johannes_calculate_image_height' ) ):
	function johannes_calculate_image_height( $width = 1200, $ratio = '16_9' ) {

		list( $rw, $rh ) = explode( '_', $ratio );

		$height = ceil( $width * absint( $rh ) / absint( $rw ) );

		return $height;
	}
endif;



if ( !function_exists( 'johannes_get_editor_font_sizes' ) ):
	function johannes_get_editor_font_sizes( ) {

		$regular = absint( johannes_get_option( 'font_size_p' ) );

		$s = $regular  * 0.8;
		$l = $regular * 2.5;
		$xl = $regular * 3.25;

		$sizes = array( array(
				'name'      => esc_html__( 'Small', 'johannes' ),
				'shortName' => esc_html__( 'S', 'johannes' ),
				'size'      => $s,
				'slug'      => 'small',
			),

			array(
				'name'      => esc_html__( 'Normal', 'johannes' ),
				'shortName' => esc_html__( 'M', 'johannes' ),
				'size'      => $regular,
				'slug'      => 'normal',
			),

			array(
				'name'      => esc_html__( 'Large', 'johannes' ),
				'shortName' => esc_html__( 'L', 'johannes' ),
				'size'      => $l,
				'slug'      => 'large',
			),
			array(
				'name'      => esc_html__( 'Huge', 'johannes' ),
				'shortName' => esc_html__( 'XL', 'johannes' ),
				'size'      => $xl,
				'slug'      => 'huge',
			)
		);

		$sizes = apply_filters( 'johannes_modify_editor_font_sizes', $sizes );

		return $sizes;

	}
endif;


if ( !function_exists( 'johannes_get_editor_colors' ) ):
	function johannes_get_editor_colors( ) {

		$colors = array(
			array(
				'name'  => esc_html__( 'Accent', 'johannes' ),
				'slug' => 'johannes-acc',
				'color' => johannes_get_option( 'color_acc' ),
			),
			array(
				'name'  => esc_html__( 'Meta', 'johannes' ),
				'slug' => 'johannes-meta',
				'color' => johannes_get_option( 'color_meta' ),
			),
			array(
				'name'  => esc_html__( 'Background', 'johannes' ),
				'slug' => 'johannes-bg',
				'color' => johannes_get_option( 'color_bg' ),
			),
			array(
				'name'  => esc_html__( 'Background Alt 1', 'johannes' ),
				'slug' => 'johannes-bg-alt-1',
				'color' => johannes_get_option( 'color_bg_alt_1' ),
			),
			array(
				'name'  => esc_html__( 'Background Alt 2', 'johannes' ),
				'slug' => 'johannes-bg-alt-2',
				'color' => johannes_get_option( 'color_bg_alt_2' ),
			)
		);

		$colors = apply_filters( 'johannes_modify_editor_colors', $colors );

		return $colors;

	}
endif;



/**
 * Get image ID from URL
 *
 * It gets image/attachment ID based on URL
 *
 * @param string  $image_url URL of image/attachment
 * @return int|bool Attachment ID or "false" if not found
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_image_id_by_url' ) ):
	function johannes_get_image_id_by_url( $image_url ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		if ( isset( $attachment[0] ) ) {
			return $attachment[0];
		}

		return false;
	}
endif;


/**
 * Calculate reading time by content length
 *
 * @param string  $text Content to calculate
 * @return int Number of minutes
 * @since  1.0
 */

if ( !function_exists( 'johannes_read_time' ) ):
	function johannes_read_time( $text ) {

		$words = count( preg_split( "/[\n\r\t ]+/", wp_strip_all_tags( $text ) ) );
		$number_words_per_minute = johannes_get_option( 'words_read_per_minute' );
		$number_words_per_minute = !empty( $number_words_per_minute ) ? absint( $number_words_per_minute ) : 200;

		if ( !empty( $words ) ) {
			$time_in_minutes = ceil( $words / $number_words_per_minute );
			return $time_in_minutes;
		}

		return false;
	}
endif;


/**
 * Trim chars of a string
 *
 * @param string  $string Content to trim
 * @param int     $limit  Number of characters to limit
 * @param string  $more   Chars to append after trimed string
 * @return string Trimmed part of the string
 * @since  1.0
 */

if ( !function_exists( 'johannes_trim_chars' ) ):
	function johannes_trim_chars( $string, $limit, $more = '...' ) {

		if ( !empty( $limit ) ) {

			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $string ), ' ' );
			preg_match_all( '/./u', $text, $chars );
			$chars = $chars[0];
			$count = count( $chars );

			if ( $count > $limit ) {

				$chars = array_slice( $chars, 0, $limit );

				for ( $i = ( $limit - 1 ); $i >= 0; $i-- ) {
					if ( in_array( $chars[$i], array( '.', ' ', '-', '?', '!' ) ) ) {
						break;
					}
				}

				$chars = array_slice( $chars, 0, $i );
				$string = implode( '', $chars );
				$string = rtrim( $string, ".,-?!" );
				$string .= $more;
			}

		}

		return $string;
	}
endif;


/**
 * Parse args ( merge arrays )
 *
 * Similar to wp_parse_args() but extended to also merge multidimensional arrays
 *
 * @param array   $a - set of values to merge
 * @param array   $b - set of default values
 * @return array Merged set of elements
 * @since  1.0
 */

if ( !function_exists( 'johannes_parse_args' ) ):
	function johannes_parse_args( &$a, $b ) {
		$a = (array)$a;
		$b = (array)$b;
		$r = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $r[$k] ) ) {
				$r[$k] = johannes_parse_args( $v, $r[$k] );
			} else {
				$r[$k] = $v;
			}
		}
		return $r;
	}
endif;


/**
 * Compare two values
 *
 * Fucntion compares two values and sanitazes 0
 *
 * @param mixed   $a
 * @param mixed   $b
 * @return bool Returns true if equal
 * @since  1.0
 */

if ( !function_exists( 'johannes_compare' ) ):
	function johannes_compare( $a, $b ) {
		return (string)$a === (string)$b;
	}
endif;


/**
 * Compare two values and return a string if true
 *
 *
 * @param mixed   $a
 * @param mixed   $b
 * @param string  $output
 * @return string Returns output if true
 * @since  1.0
 */
if ( !function_exists( 'johannes_selected' ) ):
	function johannes_selected( $a, $b, $output ) {
		return johannes_compare( $a, $b ) ? $output : '';
	}
endif;


/**
 * Sort option items
 *
 * Use this function to properly order sortable options
 *
 * @param array   $items    Array of items
 * @param array   $selected Array of IDs of currently selected items
 * @return array ordered items
 * @since  1.0
 */

if ( !function_exists( 'johannes_sort_option_items' ) ):
	function johannes_sort_option_items( $items, $selected, $field = 'term_id' ) {

		if ( empty( $selected ) ) {
			return $items;
		}

		$new_items = array();
		$temp_items = array();
		$temp_items_ids = array();

		foreach ( $selected as $selected_item_id ) {

			foreach ( $items as $item ) {
				if ( $selected_item_id == $item->$field ) {
					$new_items[] = $item;
				} else {
					if ( !in_array( $item->$field, $selected ) && !in_array( $item->$field, $temp_items_ids ) ) {
						$temp_items[] = $item;
						$temp_items_ids[] = $item->$field;
					}
				}
			}

		}

		$new_items = array_merge( $new_items, $temp_items );

		return $new_items;
	}
endif;


/**
 * Compress CSS Code
 *
 * @param string  $code Uncompressed css code
 * @return string Compressed css code
 * @since  1.0
 */

if ( !function_exists( 'johannes_compress_css_code' ) ) :
	function johannes_compress_css_code( $code ) {

		// Remove Comments
		$code = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code );

		// Remove tabs, spaces, newlines, etc.
		$code = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $code );

		return $code;
	}
endif;


/**
 * Get list of social options
 *
 * Used for user social profiles
 *
 * @return array
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_social' ) ) :
	function johannes_get_social() {
		$social = array(
			'behance' => 'Behance',
			'delicious' => 'Delicious',
			'deviantart' => 'DeviantArt',
			'digg' => 'Digg',
			'dribbble' => 'Dribbble',
			'facebook' => 'Facebook',
			'flickr' => 'Flickr',
			'github' => 'Github',
			'google' => 'GooglePlus',
			'instagram' => 'Instagram',
			'linkedin' => 'LinkedIN',
			'pinterest' => 'Pinterest',
			'reddit' => 'ReddIT',
			'rss' => 'Rss',
			'skype' => 'Skype',
			'snapchat' => 'Snapchat',
			'slack' => 'Slack',
			'stumbleupon' => 'StumbleUpon',
			'soundcloud' => 'SoundCloud',
			'spotify' => 'Spotify',
			'tumblr' => 'Tumblr',
			'twitter' => 'Twitter',
			'vimeo-square' => 'Vimeo',
			'vk' => 'vKontakte',
			'vine' => 'Vine',
			'weibo' => 'Weibo',
			'wordpress' => 'WordPress',
			'xing' => 'Xing' ,
			'yahoo' => 'Yahoo',
			'youtube' => 'Youtube'
		);

		return $social;
	}
endif;



/**
 * Calculate time difference
 *
 * @param string  $timestring String to calculate difference from
 * @return  int Time difference in miliseconds
 * @since  1.0
 */
if ( !function_exists( 'johannes_calculate_time_diff' ) ) :
	function johannes_calculate_time_diff( $timestring ) {

		$now = current_time( 'timestamp' );

		switch ( $timestring ) {
		case '-1 day' : $time = $now - DAY_IN_SECONDS; break;
		case '-3 days' : $time = $now - ( 3 * DAY_IN_SECONDS ); break;
		case '-1 week' : $time = $now - WEEK_IN_SECONDS; break;
		case '-1 month' : $time = $now - ( YEAR_IN_SECONDS / 12 ); break;
		case '-3 months' : $time = $now - ( 3 * YEAR_IN_SECONDS / 12 ); break;
		case '-6 months' : $time = $now - ( 6 * YEAR_IN_SECONDS / 12 ); break;
		case '-1 year' : $time = $now - ( YEAR_IN_SECONDS ); break;
		default : $time = $now;
		}

		return $time;
	}
endif;


/**
 * Get post format
 *
 * Checks format of current post and possibly modify it based on specific options
 *
 * @param unknown $restriction_check bool Wheter to check for post restriction (if restricted we threat it as standard)
 * @return string Format value
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_post_format' ) ):
	function johannes_get_post_format() {

		$format = get_post_format();

		$supported_formats = get_theme_support( 'post-formats' );

		if ( !empty( $supported_formats ) && is_array( $supported_formats[0] ) && !in_array( $format, $supported_formats[0] ) ) {
			$format = '';
		}

		return $format;
	}
endif;


/**
 * Get the css class for a specific background type
 *
 * @param string  $id
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_background_css_class' ) ) :
	function johannes_get_background_css_class( $id = 'none' ) {

		$classes = array(
			'none'          => '',
			'alt-1' => 'johannes-bg-alt-1',
			'alt-2' => 'johannes-bg-alt-2'
		);

		if ( empty( $id ) || !array_key_exists( $id , $classes ) ) {
			return '';
		}

		return $classes[$id];
	}
endif;


/**
 * Get page meta data
 *
 * @param string  $field specific option array key
 * @return mixed meta data value or set of values
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_page_meta' ) ):
	function johannes_get_page_meta( $post_id = false, $field = false ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$defaults = array(
			'settings' => 'inherit',
			'layout'            => johannes_get_option( 'page_layout' ),
			'sidebar' => array(
				'position' => johannes_get_option( 'page_sidebar_position' ),
				'classic'   => johannes_get_option( 'page_sidebar_standard' ),
				'sticky'   => johannes_get_option( 'page_sidebar_sticky' ),
			),
		);

		$meta = get_post_meta( $post_id, '_johannes_meta', true );
		$meta = johannes_parse_args( $meta, $defaults );

		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

/**
 * Get post meta data
 *
 * @param string  $field specific option array key
 * @return mixed meta data value or set of values
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_post_meta' ) ):
	function johannes_get_post_meta( $post_id = false, $field = false ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$defaults = array(
			'settings' => 'inherit',
			'layout'            => johannes_get_option( 'single_layout' ),
			'sidebar' => array(
				'position' => johannes_get_option( 'single_sidebar_position' ),
				'classic'   => johannes_get_option( 'single_sidebar_standard' ),
				'sticky'   => johannes_get_option( 'single_sidebar_sticky' ),
			),
		);

		$meta = get_post_meta( $post_id, '_johannes_meta', true );

		$meta = johannes_parse_args( $meta, $defaults );

		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

/**
 * Get category meta data
 *
 * @param string  $field specific option array key
 * @return mixed meta data value or set of values
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_category_meta' ) ):
	function johannes_get_category_meta( $cat_id = false, $field = false ) {

		$inherit_from = johannes_get_option( 'category_settings' ) == 'custom' ? 'category' : 'archive';

		$defaults = array(
			'settings' => 'inherit',
			'image'            => '',
			'loop' => johannes_get_option( $inherit_from . '_loop' ),
			'layout' => johannes_get_option( $inherit_from . '_layout' ),
			'sidebar' => array(
				'position' => johannes_get_option( $inherit_from . '_sidebar_position' ),
				'classic'   => johannes_get_option( $inherit_from . '_sidebar_standard' ),
				'sticky'   => johannes_get_option( $inherit_from . '_sidebar_sticky' ),
			),
			'pagination'       => johannes_get_option( $inherit_from . '_pagination' ),
			'ppp_num'              => johannes_get_option( $inherit_from . '_ppp' ) == 'inherit' ? johannes_get_default_option( $inherit_from . '_ppp_num' ) : johannes_get_option( $inherit_from . '_ppp_num' ),
			'archive' => array(
				'description' => johannes_get_option( $inherit_from . '_description' ),
				'meta'   => johannes_get_option( $inherit_from . '_meta' ),
			)
		);

		if ( $cat_id ) {
			$meta = get_term_meta( $cat_id, '_johannes_meta', true );
			$meta = johannes_parse_args( $meta, $defaults );
		} else {
			$meta = $defaults;
		}

		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

if ( !function_exists( 'johannes_hex_to_hsl' ) ):
	function johannes_hex_to_hsla( $hex, $lightness = false, $opacity = 1, $raw = false ) {
		$rgb = johannes_hex_to_rgba( $hex, false, false, true );

		$hsl = johannes_rgb_to_hsl( $rgb, $lightness );
		if ( $raw ) {
			return $hsl;
		}

		if ( $opacity !== false ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			return 'hsla( ' . $hsl[0] . ', ' . $hsl[1] . '%, ' . $hsl[2] . '%, ' . $opacity . ')';
		} else {
			return 'hsl(' . $hsl[0] . ', ' . $hsl[1] . '%, ' . $hsl[2] . '%)';
		}
	}
endif;

/**
 * Hex to rgba
 *
 * Convert hexadecimal color to rgba
 *
 * @param string  $color   Hexadecimal color value
 * @param float   $opacity Opacity value
 * @return string RGBA color value
 * @since  1.0
 */

if ( !function_exists( 'johannes_hex_to_rgba' ) ):
	function johannes_hex_to_rgba( $color, $opacity = false, $array = false ) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if ( empty( $color ) )
			return $default;

		//Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb = array_map( 'hexdec', $hex );

		if ( $array ) {
			return $rgb;
		}

		//Check if opacity is set(rgba or rgb)
		if ( $opacity !== false ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ",", $rgb ) . ')';
		}

		//Return rgb(a) color string
		return $output;
	}
endif;

/**
 * It converts rgb to hex color mode.
 *
 * @param unknown $rgb array
 * @return string
 * @since  1.0
 */
if ( !function_exists( 'johannes_rgb_to_hex' ) ):
	function johannes_rgb_to_hex( array $rgb ) {
		return sprintf( "#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2] );
	}
endif;

/**
 * Convert RGB to HSL color code
 *
 * @param unknown $rgb
 * @return array HSL color
 * @since  1.0
 */
if ( !function_exists( 'johannes_rgb_to_hsl' ) ):
	function johannes_rgb_to_hsl( $rgb, $lightness = false ) {
		list( $r, $g, $b ) = $rgb;

		$r /= 255;
		$g /= 255;
		$b /= 255;
		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );
		$h = 0;
		$l = ( $max + $min ) / 2;
		$d = $max - $min;
		if ( $d == 0 ) {
			$h = $s = 0; // achromatic
		} else {
			$s = $d / ( 1 - abs( 2 * $l - 1 ) ) * 100;
			switch ( $max ) {
			case $r:
				$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
				if ( $b > $g ) {
					$h += 360;
				}
				break;
			case $g:
				$h = 60 * ( ( $b - $r ) / $d + 2 );
				break;
			case $b:
				$h = 60 * ( ( $r - $g ) / $d + 4 );
				break;
			}
		}

		$l *= 100;

		if ( $lightness ) {

			$percentage = ( absint( $lightness ) / 100 ) * $l;

			if ( $lightness < 0 ) {
				$l = $l - $percentage;
			}else {
				$l = $l + $percentage;
			}
			$l = ( $l > 100 ) ? 100 : $l;
			$l = ( $l < 0 ) ? 0 : $l;
		}

		return array( round( $h, 2 ), round( $s, 2 ), round( $l, 2 ) );
	}
endif;

/**
 * Convert HSL to RGB color code
 *
 * @param unknown $hsl
 * @return array RGB color
 * @since  1.1
 */
if ( !function_exists( 'johannes_hsl_to_rgb' ) ):
	function johannes_hsl_to_rgb( $hsl ) {
		list( $h, $s, $l ) = $hsl;

		$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
		$x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
		$m = $l - ( $c / 2 );

		if ( $h < 60 ) {
			$r = $c;
			$g = $x;
			$b = 0;
		} else if ( $h < 120 ) {
				$r = $x;
				$g = $c;
				$b = 0;
			} else if ( $h < 180 ) {
				$r = 0;
				$g = $c;
				$b = $x;
			} else if ( $h < 240 ) {
				$r = 0;
				$g = $x;
				$b = $c;
			} else if ( $h < 300 ) {
				$r = $x;
				$g = 0;
				$b = $c;
			} else {
			$r = $c;
			$g = 0;
			$b = $x;
		}

		$r = ( $r + $m ) * 255;
		$g = ( $g + $m ) * 255;
		$b = ( $b + $m ) * 255;
		return array( floor( $r ), floor( $g ), floor( $b ) );
	}
endif;

/**
 * Check if color is light
 *
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_is_color_light' ) ):
	function johannes_is_color_light( $color = false ) {
		$hsl = johannes_rgb_to_hsl( johannes_hex_to_rgba( $color, false, true ) );
		return $hsl[2] >= 70;
	}
endif;


/**
 * Check if post is currently restricted
 *
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_is_restricted_post' ) ):
	function johannes_is_restricted_post() {

		//Check if password protected
		if ( post_password_required() ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Get number of posts for the current archive
 *
 * @return int
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_archive_posts_count' ) ):
	function johannes_get_archive_posts_count( ) {

		global $wp_query;

		return isset( $wp_query->found_posts ) ? $wp_query->found_posts : 0;
	}
endif;


/**
 * Get category/taxonomy child links
 *
 * @return string HTML output
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_archive_subnav' ) ):
	function johannes_get_archive_subnav() {

		$obj = get_queried_object();

		if ( empty( $obj ) ) {
			return '';
		}

		$terms = get_terms( array( 'taxonomy' => $obj->taxonomy, 'child_of' => $obj->term_id ) );

		if ( is_wp_error( $terms ) ) {
			return '';
		}

		if ( empty( $terms ) ) {
			return '';
		}

		$links = array();

		foreach ( $terms as $term ) {
			$link = get_term_link( $term, $obj->taxonomy );
			if ( !is_wp_error( $link ) ) {
				$links[] = '<li><a href="' . esc_url( $link ) . '" rel="tag" class="cat-'.esc_attr( $term->term_id ).'">' . $term->name . '</a></li>';
			}
		}

		if ( !empty( $links ) ) {
			return implode( '', $links );
		}

		return '';

	}
endif;




/**
 * Woocommerce  Cart Elements
 *
 * @return array
 * @since  1.0
 */
if ( !function_exists( 'johannes_woocommerce_cart_elements' ) ):
	function johannes_woocommerce_cart_elements() {

		if ( !johannes_is_woocommerce_active() ) {
			return false;
		}

		$elements = array();
		$elements['cart_url'] = wc_get_cart_url();
		$elements['products_count'] = WC()->cart->get_cart_contents_count();
		return $elements;
	}
endif;

/**
 * Check if we are on WooCommerce page
 *
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_is_woocommerce_page' ) ):
	function johannes_is_woocommerce_page() {

		return johannes_is_woocommerce_active() && ( is_woocommerce() || is_shop() || is_cart() || is_checkout() );
	}
endif;


/**
 * Check if WooCommerce is active
 *
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_is_woocommerce_active' ) ):
	function johannes_is_woocommerce_active() {

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


/**
 * Check if Yet Another Related Posts Plugin (YARPP) is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_yarpp_active' ) ):
	function johannes_is_yarpp_active() {
		if ( in_array( 'yet-another-related-posts-plugin/yarpp.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Contextual Related Posts is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_crp_active' ) ):
	function johannes_is_crp_active() {
		if ( in_array( 'contextual-related-posts/contextual-related-posts.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if WordPress Related Posts is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_wrpr_active' ) ):
	function johannes_is_wrpr_active() {
		if ( in_array( 'wordpress-23-related-posts-plugin/wp_related_posts.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Jetpack is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_jetpack_active' ) ):
	function johannes_is_jetpack_active() {
		if ( in_array( 'jetpack/jetpack.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Yoast SEO is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_yoast_active' ) ):
	function johannes_is_yoast_active() {
		if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Breadcrumb NavXT is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_breadcrumbs_navxt_active' ) ):
	function johannes_is_breadcrumbs_navxt_active() {
		if ( in_array( 'breadcrumb-navxt/breadcrumb-navxt.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Kirki customozer framework is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_kirki_active' ) ):
	function johannes_is_kirki_active() {
		if ( class_exists( 'Kirki' ) ) {
			return true;
		}

		return false;
	}
endif;

/**
 * Check if Meks Easy Social Share is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_meks_ess_active' ) ):
	function johannes_is_meks_ess_active() {
		if ( in_array( 'meks-easy-social-share/meks-easy-social-share.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


/**
 * Check if is Gutenberg page
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_is_gutenberg_page' ) ):
	function johannes_is_gutenberg_page() {

		if ( function_exists( 'is_gutenberg_page' ) ) {
			return is_gutenberg_page();
		}

		global $wp_version;

		if ( version_compare( $wp_version, '5', '<' ) ) {
			return false;
		}

		global $current_screen;

		if ( ( $current_screen instanceof WP_Screen ) && !$current_screen->is_block_editor() ) {
			return false;
		}

		return true;

		return is_gutenberg_page();
	}
endif;


/**
 * Get layouts map
 *
 * Function which keeps the definition parameters for each of post listing layouts
 *
 * @param int     $layout_id
 * @param int     $loop_index current post in the loop
 * @return array set of parameters
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_layouts_map' ) ):
	function johannes_get_layouts_map() {

		$params =  array(

			//Layout A
			1 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_a.svg' ),
				'alt' => esc_html__( 'A', 'johannes' ),
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'a' )
				)
			),

			//Layout B
			2 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_b.svg' ),
				'alt' => esc_html__( 'B', 'johannes'  ),
				'col' => 'col-lg-8',
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'b' )
				)
			),

			//Layout B with sidebar
			'3' => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_b_sid.svg' ),
				'alt' => esc_html__( 'B (w/ sidebar)', 'johannes'  ),
				'sidebar' => true,
				'col' => 'col-lg-8',
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'b' )
				)
			),

			//Layout C
			4 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_c.svg' ),
				'alt' => esc_html__( 'C', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'c' )
				)
			),

			//Layout D
			5 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_d.svg' ),
				'alt' => esc_html__( 'D', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12 col-md-6 col-lg-4', 'style' => 'd' )
				)
			),

			//Layout D 2 columns
			6 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_d_2col.svg' ),
				'alt' => esc_html__( 'D (2 columns)', 'johannes'  ),
				'col' => 'col-lg-8',
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'd' )
				)
			),

			//Layout D with sidebar
			7 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_d_sid.svg' ),
				'alt' => esc_html__( 'D (w/ sidebar)', 'johannes'  ),
				'col' => 'col-lg-8',
				'sidebar' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'd' )
				)
			),

			//Layout E
			8 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_e.svg' ),
				'alt' => esc_html__( 'E', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'e' )
				)
			),

			//Layout F
			9 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_f.svg' ),
				'alt' => esc_html__( 'F', 'johannes'  ),
				'col' => 'col-lg-8',
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'f' )
				)
			),

			//Layout F (with sidebar)
			10 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_f_sid.svg' ),
				'alt' => esc_html__( 'F (w/ sidebar)', 'johannes'  ),
				'col' => 'col-lg-8',
				'sidebar' => true,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'f' )
				)
			),

			//Layout A + C
			11 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_a_c.svg' ),
				'alt' => esc_html__( 'A + C', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'a' ),
					array( 'col' => 'col-12 col-md-6', 'style' => 'c' )
				)
			),

			//Layout A + D
			12 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_a_d.svg' ),
				'alt' => esc_html__( 'A + D', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'a' ),
					array( 'col' => 'col-12 col-md-6 col-lg-4', 'style' => 'd' )
				)
			),

			//Layout A + E
			13 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_a_e.svg' ),
				'alt' => esc_html__( 'A + E', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'a' ),
					array( 'col' => 'col-12', 'style' => 'e' )
				)
			),

			//Layout B + D (with sidebar)
			14 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_b_d_sid.svg' ),
				'alt' => esc_html__( 'B + D (w/ sidebar)', 'johannes'  ),
				'col' => 'col-lg-8',
				'sidebar' => true,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'b' ),
					array( 'col' => 'col-12 col-md-6', 'style' => 'd' )
				)
			),

			//Layout B + F (with sidebar)
			15 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_b_f_sid.svg' ),
				'alt' => esc_html__( 'B + F (w/ sidebar)', 'johannes'  ),
				'col' => 'col-lg-8',
				'sidebar' => true,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'b' ),
					array( 'col' => 'col-12', 'style' => 'f' )
				)
			),

			//Layout C + D
			16 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_c_d.svg' ),
				'alt' => esc_html__( 'C + D', 'johannes'  ),
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'c' ),
					array( 'col' => 'col-12 col-md-6', 'style' => 'c' ),
					array( 'col' => 'col-12 col-md-6 col-lg-4', 'style' => 'd' )
				)
			)

		);

		return apply_filters( 'johannes_modify_layouts_map', $params );

	}
endif;

/**
 * Get layouts map
 *
 * Function which keeps the definition parameters for each of post listing layouts
 *
 * @return array set of parameters
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_featured_layouts_map' ) ):
	function johannes_get_featured_layouts_map() {

		$params =  array(

			1 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_a.svg' ),
				'alt' => esc_html__( 'A (1 item)', 'johannes' ),
				'ppp' => 1,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-a' )
				)
			),

			2 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_a_slider.svg' ),
				'alt' => esc_html__( 'A (slider)', 'johannes' ),
				'slider' => true,
				'carousel' => true,
				'classes' => 'alignfull',
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-a' )
				)
			),

			3 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_b.svg' ),
				'alt' => esc_html__( 'B (1 item)', 'johannes' ),
				'ppp' => 1,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-b' )
				)
			),

			4 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_b_slider.svg' ),
				'alt' => esc_html__( 'B (slider)', 'johannes' ),
				'slider' => true,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-b' )
				)
			),

			5 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_b_carousel.svg' ),
				'alt' => esc_html__( 'B (carousel)', 'johannes' ),
				'slider' => true,
				'carousel' => true,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-b' )
				)
			),

			6 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_c.svg' ),
				'alt' => esc_html__( 'C (2 items)', 'johannes' ),
				'ppp' => 2,
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'fa-c' )
				)
			),

			7 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_c_slider.svg' ),
				'alt' => esc_html__( 'C (slider)', 'johannes' ),
				'slider' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'fa-c' )
				)
			),

			8 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_c_carousel.svg' ),
				'alt' => esc_html__( 'C (carousel)', 'johannes' ),
				'slider' => true,
				'carousel' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'fa-c' )
				)
			),

			9 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_c_carousel_center.svg' ),
				'alt' => esc_html__( 'C (carousel centered)', 'johannes' ),
				'slider' => true,
				'carousel' => true,
				'center' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-md-6', 'style' => 'fa-c' )
				)
			),

			10 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_d.svg' ),
				'alt' => esc_html__( 'D (3 items)', 'johannes' ),
				'ppp' => 3,
				'loop' => array(
					array( 'col' => 'col-12 col-lg-4 col-md-6', 'style' => 'fa-d' )
				)
			),

			11 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_d_slider.svg' ),
				'alt' => esc_html__( 'D (slider)', 'johannes' ),
				'slider' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-lg-4 col-md-6', 'style' => 'fa-d' )
				)
			),

			12 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_d_carousel.svg' ),
				'alt' => esc_html__( 'D (carousel)', 'johannes' ),
				'slider' => true,
				'carousel' => true,
				'loop' => array(
					array( 'col' => 'col-12 col-lg-4 col-md-6', 'style' => 'fa-d' )
				)
			),


			13 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_e.svg' ),
				'alt' => esc_html__( 'E (1 item)', 'johannes' ),
				'ppp' => 1,
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-e' )
				)
			),

			14 => array(
				'src' => get_parent_theme_file_uri( '/assets/img/admin/layout_fa_e_slider.svg' ),
				'alt' => esc_html__( 'E (slider)', 'johannes' ),
				'slider' => true,
				'classes' => 'arrows-reset',
				'loop' => array(
					array( 'col' => 'col-12', 'style' => 'fa-e' )
				)
			),


		);

		return apply_filters( 'johannes_modify_featured_layouts_map', $params );

	}
endif;


?>