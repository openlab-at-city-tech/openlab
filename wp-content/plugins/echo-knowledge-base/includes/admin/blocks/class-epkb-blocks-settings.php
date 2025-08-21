<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EPKB_Blocks_Settings {

	/**
	 * Return 'kb_id' setting for each block
	 * @return array
	 */
	public static function get_kb_id_setting() {

		if ( EPKB_Utilities::is_frontend() ) {
			return array(
				'setting_type' => '',
				'default' => EPKB_KB_Config_DB::DEFAULT_KB_ID,
			);
		}

		$kb_id_setting = array(
			'setting_type' => 'custom_dropdown',
			'default' => EPKB_KB_Config_DB::DEFAULT_KB_ID,
			'label' => esc_html__( 'Selected KB', 'echo-knowledge-base' ),
			'options' => array(),
		);

		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {

			$one_kb_id = $one_kb_config['id'];

			// do not show archived KBs
			if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}

			// do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
			$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
			if ( !current_user_can( $required_capability ) ) {
				continue;
			}

			// add current KB to the list
			$kb_id_setting['options'][] = array(
				'key' => (int)$one_kb_id,
				'name' => $one_kb_config['kb_name'],
				'style' => array(),
			);
		}

		return $kb_id_setting;
	}

	/**
	 * Return configuration array for KB Custom Block Template for this page toggle
	 * @return array
	 */
	public static function get_kb_block_template_toggle() {

		if ( EPKB_Utilities::is_frontend() ) {
			return array(
				'setting_type' => '',
			);
		}

		$show_toggle = EPKB_Block_Utilities::is_kb_block_page_template_available();

		return array(
			'setting_type' =>  $show_toggle ? 'template_toggle' : '',
			'label' => esc_html__( 'KB Template', 'echo-knowledge-base' ),
			'default' => 'off',
		);
	}

	/**
	 * Return configuration array for KB legacy page template toggle
	 * Legacy KB Template if theme is a block theme + we are using blocks on the page
	 * @return array
	 */
	public static function get_kb_legacy_template_toggle() {

		if ( EPKB_Utilities::is_frontend() ) {
			return array(
				'setting_type' => '',
			);
		}

		$show_toggle = ! EPKB_Block_Utilities::is_block_theme();

		return array(
			'setting_type' =>  $show_toggle ? 'custom_toggle' : '',
			'label' => esc_html__( 'KB Template', 'echo-knowledge-base' ),
			'default' => 'kb_templates',
			'options' => array(
				'on' => 'kb_templates',
				'off' => 'current_theme_templates',
			),
		);
	}

	/**
	 * Return configuration array for message about KB block page template
	 * @return array
	 */
	public static function get_kb_block_template_mention() {

		if ( EPKB_Utilities::is_frontend() ) {
			return array(
				'setting_type' => '',
			);
		}

		$show_toggle = EPKB_Block_Utilities::is_kb_block_page_template_available() || ! EPKB_Block_Utilities::is_block_theme();

		$setting_config = array(
			'setting_type' => $show_toggle ? 'section_description' : '',
			'description' => EPKB_Block_Utilities::is_block_theme()
				? esc_html__( 'Consider to use KB Block Page Template for this KB Main Page.', 'echo-knowledge-base' ) . ' ' .
				( EPKB_Block_Utilities::is_kb_block_page_template_available() ? '' : esc_html__( 'You need to upgrade the WordPress core to version 6.7 or higher in order to use the KB template.', 'echo-knowledge-base' ) )
				: esc_html__( 'Consider to use KB Template for this KB Main Page.', 'echo-knowledge-base' ) . ' ' . esc_html__( 'Note that the KB Template effect is not visible in this editor. Please check the page frontend.', 'echo-knowledge-base' ),
			'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ),
			'link_url' => EPKB_Block_Utilities::is_block_theme() ? 'https://www.echoknowledgebase.com/documentation/kb-block-template/' : 'https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/',
			'hide_on_dependencies' => array(
				'templates_for_kb' => 'kb_templates',
				'kb_block_template_toggle' => 'on',
			),
		);

		return $setting_config;
	}

	/**
	 * Return configuration array for block full width toggle setting
	 * @param $default_args
	 * @return array
	 */
	public static function get_block_full_width_setting( $default_args = array() ) {
		$default_args = wp_parse_args( $default_args, array(
			'default' => 'off',
		) );

		return array(
			'setting_type' => 'toggle',
			'label' => esc_html__( 'Full Width', 'echo-knowledge-base' ),
			'default' => $default_args['default'],
		);
	}

	/**
	 * Return configuration array for block max width setting
	 * @param $default_args
	 * @return array
	 */
	public static function get_block_max_width_setting( $default_args = array() ) {
		$default_args = wp_parse_args( $default_args, array(
			'default' => 1400,
			'min' => 400,
			'max' => 2000,
		) );

		return array(
			'setting_type' => 'range',
			'label' => esc_html__( 'Width ( px )', 'echo-knowledge-base' ),
			'min' => $default_args['min'],
			'max' => $default_args['max'],
			'default' => $default_args['default'],
			'help_text' => esc_html__( 'Block width may be limited by container or theme; check them if displayed width does not match settings.', 'echo-knowledge-base' ),
			'help_link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ),
			'help_link_url' => 'https://www.echoknowledgebase.com/documentation/main-page-width/',
			'disable_on_dependencies' => array(
				'block_full_width_toggle' => 'on',
			),
		);
	}

	/**
	 * Return list of presets to use as 'options' array for presets setting UI
	 * @return array[]
	 */
	public static function get_all_preset_settings( $block_name, $layout_name) {

		// don't load presets if not in editor or not in edit mode
		if ( EPKB_Utilities::is_frontend() ) {
			return [];
		}

		$all_themes = EPKB_KB_Wizard_Themes::get_all_themes_with_kb_config();
		$relevant_attribute_names = array_keys( epkb_get_block_attributes( $block_name ) );

		$presets_config = EPKB_KB_Wizard_Setup::get_modules_presets_config( $layout_name );
		$all_design_settings['current'] = [
			'label' => '-----',
			'settings' => array(),
		];
		foreach( $presets_config['categories_articles'][ $layout_name ]['presets'] as $preset_name => $preset ) {
			$all_design_settings[ $preset_name ] = [ 'label' => $preset['title'] ];
		}

		foreach ( $all_design_settings as $theme_name => $theme_label ) {

			if ( $theme_name == 'current' ) {
				continue;
			}

			$one_theme = empty( $all_themes[ $theme_name ] ) ? $all_themes['office'] : $all_themes[ $theme_name ];

			$all_design_settings[ $theme_name ]['settings'] = array();

			// filter current preset settings
			foreach ( $one_theme as $setting_key => $setting_value ) {

				// do nothing for KB settings which are not used for the current block
				if ( ! in_array( $setting_key, $relevant_attribute_names ) ) {
					continue;
				}

				$all_design_settings[ $theme_name ]['settings'][ $setting_key ] = $setting_value;
			}
		}

		return $all_design_settings;
	}

	/**
	 * Return 'custom_css_class' setting for each block
	 * @return array
	 */
	public static function get_custom_css_class_setting() {
		return array(
			'label' => esc_html__( 'Additional CSS Classes', 'echo-knowledge-base' ),
			'setting_type' => 'text',
			'default' => '',
			'description' => esc_html__( 'Separate multiple classes with spaces.', 'echo-knowledge-base' ),
		);
	}

	/**
	 * Print block fonts - use common KB slugs to avoid duplicated fonts loading
	 * @param $block_font_slugs
	 * @return void
	 */
	public static function print_block_fonts( $block_font_slugs ) {

		foreach ( $block_font_slugs as $one_font_slug ) {

			// do nothing if slug is empty, or the font is not registered yet, or the font is already enqueued
			if ( empty( $one_font_slug ) || !wp_style_is( $one_font_slug, 'registered' ) || wp_style_is( $one_font_slug ) ) {
				continue;
			}

			wp_print_styles( $one_font_slug );
		}
	}

	public static function get_font_appearance_weight( $typography_setting, $typography_specs = null ) {
		$font_appearance_specs = empty( $typography_specs ) ? EPKB_Blocks_Settings::get_typography_control_font_appearance() : $typography_specs['controls']['font_appearance'];
		return $font_appearance_specs['options'][ $typography_setting['font_appearance'] ]['style']['fontWeight'];
	}

	public static function get_font_appearance_style( $typography_setting, $typography_specs = null ) {
		$font_appearance_specs = empty( $typography_specs ) ? EPKB_Blocks_Settings::get_typography_control_font_appearance() : $typography_specs['controls']['font_appearance'];
		return $font_appearance_specs['options'][ $typography_setting['font_appearance'] ]['style']['fontStyle'];
	}

	/**
	 * Return font family specs for Typography control
	 * @param $default
	 * @return array
	 */
	public static function get_typography_control_font_family( $default = '' ) {
		return array(
			'label' => esc_html__( 'Font', 'echo-knowledge-base' ),
			'default' => $default,
		);
	}

	/**
	 * Return font size specs for Typography control
	 * @return array
	 */
	public static function get_typography_control_font_size( $size_options, $default_size ) {

		$font_size_control = array(
			'label' => esc_html__( 'Size', 'echo-knowledge-base' ),
			'default' => $default_size,
			'units' => 'px',
			'options' => [],
		);

		$all_size_options = array(
			'small' => array(
				'name' => esc_html__( 'Small', 'echo-knowledge-base' ),
				'size' => 24,
			),
			'normal' => array(
				'name' => esc_html__( 'Medium', 'echo-knowledge-base' ),
				'size' => 36,
			),
			'big' => array(
				'name' => esc_html__( 'Large', 'echo-knowledge-base' ),
				'size' => 48,
				'slug' => 'big',
			),
		);

		foreach ( $size_options as $one_size_key => $one_size_value ) {
			if ( empty( $all_size_options[ $one_size_key ] ) ) {
				continue;
			}
			$font_size_control['options'][ $one_size_key ] = $all_size_options[ $one_size_key ];
			$font_size_control['options'][ $one_size_key ]['size'] = $one_size_value;
		}

		return $font_size_control;
	}

	/**
	 * Return font appearance specs for Typography control
	 * @param $default_args
	 * @return array
	 */
	public static function get_typography_control_font_appearance( $default_args = array() ) {
		$default_args = wp_parse_args( $default_args, array(
			'fontWeight' => 400,
			'fontStyle' => 'normal',
		) );
		return array(
			'label' => esc_html__( 'Appearance', 'echo-knowledge-base' ),
			'default' => 'default',
			'options' => array(
				'default' => array(
					'name' => esc_html__( 'Default', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => $default_args['fontWeight'],
						'fontStyle' => $default_args['fontStyle'],
					),
				),
				'thin' => array(
					'name' => esc_html__( 'Thin', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 100,
						'fontStyle' => 'normal',
					),
				),
				'extra_light' => array(
					'name' => esc_html__( 'Extra Light', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 200,
						'fontStyle' => 'normal',
					),
				),
				'light' => array(
					'name' => esc_html__( 'Light', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 300,
						'fontStyle' => 'normal',
					),
				),
				'regular' => array(
					'name' => esc_html__( 'Regular', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 400,
						'fontStyle' => 'normal',
					),
				),
				'medium' => array(
					'name' => esc_html__( 'Medium', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 500,
						'fontStyle' => 'normal',
					),
				),
				'semi_bold' => array(
					'name' => esc_html__( 'Semi Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 600,
						'fontStyle' => 'normal',
					),
				),
				'bold' => array(
					'name' => esc_html__( 'Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 700,
						'fontStyle' => 'normal',
					),
				),
				'extra_bold' => array(
					'name' => esc_html__( 'Extra Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 800,
						'fontStyle' => 'normal',
					),
				),
				'black' => array(
					'name' => esc_html__( 'Black', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 900,
						'fontStyle' => 'normal',
					),
				),
				'thin_italic' => array(
					'name' => esc_html__( 'Thin Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 100,
						'fontStyle' => 'italic',
					),
				),
				'extra_light_italic' => array(
					'name' => esc_html__( 'Extra Light Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 200,
						'fontStyle' => 'italic',
					),
				),
				'light_italic' => array(
					'name' => esc_html__( 'Light Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 300,
						'fontStyle' => 'italic',
					),
				),
				'regular_italic' => array(
					'name' => esc_html__( 'Regular Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 400,
						'fontStyle' => 'italic',
					),
				),
				'medium_italic' => array(
					'name' => esc_html__( 'Medium Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 500,
						'fontStyle' => 'italic',
					),
				),
				'semi_bold_italic' => array(
					'name' => esc_html__( 'Semi Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 600,
						'fontStyle' => 'italic',
					),
				),
				'bold_italic' => array(
					'name' => esc_html__( 'Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 700,
						'fontStyle' => 'italic',
					),
				),
				'extra_bold_italic' => array(
					'name' => esc_html__( 'Extra Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 800,
						'fontStyle' => 'italic',
					),
				),
				'black_italic' => array(
					'name' => esc_html__( 'Black Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 900,
						'fontStyle' => 'italic',
					),
				),
			),
		);
	}
}