<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Advanced_Search_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'advanced-search';

	protected $block_name = 'advanced-search';
	protected $block_var_name = 'advanced_search';
	protected $block_title = 'KB Advanced Search';
	protected $icon = 'search';
	protected $keywords = ['search', 'find', 'query', 'knowledge base'];	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization
	protected $has_rtl_css = true;

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		// when insert blocks programmatically we need to utilize non-static methods of the block classes, but we do not need hooks for this
		if ( ! $init_hooks ) {
			return;
		}

		// must be assigned to hook inside child class to enqueue unique assets for each block type
		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) ); // Frontend / Backend

		// used for search highlight
		add_action( 'save_post', array( $this, 'update_kb_setting_on_save_post'), 10, 3 );
	}

	/**
	 * Return the actual specific block content
	 * @param $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {
		$block_attributes = $this->sanitize_block_attributes( $block_attributes );
		$block_attributes['search_multiple_kbs'] = $block_attributes['search_multiple_kbs_toggle'] == 'on' ? implode( ',', $block_attributes['search_multiple_kbs_list'] ) : '';
		do_action( 'eckb_advanced_search_box', $block_attributes );
	}

	/**
	 * Add required specific attributes to work correctly with KB core functionality
	 * @param $block_attributes
	 * @return array
	 */
	protected function add_this_block_required_kb_attributes( $block_attributes ) {
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::BASIC_LAYOUT;
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {
		$block_ui_specs = $this->get_block_ui_specs();
		$output = apply_filters( 'eckb_advanced_search_block_inline_styles', '', $block_attributes, $block_ui_specs );
		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'advanced_search_mp_title_typography_controls',
			'advanced_search_mp_description_below_title_typography_controls',
			'advanced_search_mp_input_box_typography_controls',
			'advanced_search_mp_description_below_input_typography_controls',
			'advanced_search_mp_results_typography_controls',
		);
	}

	/**
	 * Check if the block is available
	 * @return bool
	 */
	protected static function is_block_available() {
		return class_exists( 'AS'.'EA_Blocks' );
	}

	/**
	 * Return handle for block public styles
	 * @return string
	 */
	protected function get_block_public_styles_handle() {
		return 'asea-' . $this->block_name . '-block';
	}

	/**
	 * Return handle for block public scripts
	 * @return string
	 */
	protected function get_block_public_scripts_handle() {
		return 'asea-public-scripts';
	}

	/**
	 * Register add-on's block styles
	 * @param $suffix
	 * @param $block_styles_dependencies
	 * @return void
	 */
	protected function register_block_public_styles( $suffix, $block_styles_dependencies ) {
		if ( ! self::is_block_available() ) {
			return;
		}
		EPKB_Core_Utilities::register_asea_block_public_styles( $this->block_name, $suffix, $block_styles_dependencies );
	}

	protected function register_block_public_scripts( $suffix ) {
		if ( ! self::is_block_available() ) {
			return;
		}
		EPKB_Core_Utilities::register_asea_block_public_scripts( $suffix );
	}

	/**
	 * Return list attributes with custom specs - they are not allowed in attributes when registering block, thus need to keep them separately
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {

		$settings_json = epkb_get_block_attributes( $this->block_name );

		$presets = apply_filters( 'eckb_advanced_search_block_presets', array(
			'current' => array(
				'label' => '-----',
				'settings' => array(),
			)
		) );

		// ensure preset settings have correct type and are registered in the block json config
		foreach ( $presets as $preset_key => $preset_config ) {
			foreach ( $preset_config['settings'] as $setting_key => $setting_value ) {

				// filter out settings which are not registered in the block json config
				if ( ! isset( $settings_json[ $setting_key ] ) ) {
					unset( $presets[ $preset_key ][ $setting_key ] );
					continue;
				}

				if ( $settings_json[ $setting_key ]['type'] == 'number' ) {
					$presets[ $preset_key ]['settings'][ $setting_key ] = intval( $setting_value );
				}

				if ( $settings_json[ $setting_key ]['type'] == 'string' ) {
					$presets[ $preset_key ]['settings'][ $setting_key ] = strval( $setting_value );
				}
			}
		}

		$kb_id_setting = EPKB_Blocks_Settings::get_kb_id_setting();

		// for optimization reason on the frontend the $kb_id_setting can be empty - ensure it has options before use it
		$search_multiple_kbs_list_options = array();
		if ( ! empty( $kb_id_setting['options'] ) ) {
			foreach ( $kb_id_setting['options'] as $one_kb_id_setting ) {
				$search_multiple_kbs_list_options[ $one_kb_id_setting['key'] ] = $one_kb_id_setting['name'];
			}
		}

		return array(

			// TAB: Settings
			'settings' => array(
				'title' => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'kb_id' => $kb_id_setting,

							// Search multiple KBs
							'search_multiple_kbs_toggle' => array(
								'label' => esc_html__( 'Search Multiple KBs', 'echo-knowledge-base' ),
								'setting_type' => 'toggle',
								'default' => 'off',
							),
							'search_multiple_kbs_list' => array(
								'setting_type' => 'checkbox_multi_select',
								'label' => '',
								'options' => $search_multiple_kbs_list_options,
								'default' => array( EPKB_KB_Config_DB::DEFAULT_KB_ID ),
								'hide_on_dependencies' => array(
									'search_multiple_kbs_toggle' => 'off',
								),
							),

							// Mention KB block template for Main Page
							'mention_kb_block_template' => EPKB_Blocks_Settings::get_kb_block_template_mention(),

							'advanced_search_context_toggle' => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_context_characters' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_context_toggle' => 'off',
								),
							),
							'advanced_search_context_highlight_font_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'advanced_search_context_toggle' => 'off',
								),
							),
							'advanced_search_text_highlight_enabled' => array(
								'setting_type' => 'toggle'
							),
						),
					),

					// GROUP: Title
					'title' => array(
						'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_title_toggle'                               => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_title'                                      => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_tag'                                  => array(
								'setting_type' => 'select_buttons_string',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
						),
					),

					// GROUP: Search Box
					'search_box' => array(
						'title' => esc_html__( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_auto_complete_wait'                         => array(
								'setting_type' => 'range',
							),
							/*'advanced_search_mp_visibility'                                 => array( not for Main Page
								'setting_type' => 'toggle'
							),*/
							'search_box_hint'                                				=> array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Description Below
					'description' => array(
						'title' => esc_html__( 'Description', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_description_below_title_toggle'             => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_description_below_title'                    => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_description_below_input_toggle'             => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_description_below_input'                    => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_input_toggle' => 'off',
								),
							),
						),
					),

					// GROUP: Filter
					'input' => array(
						'title' => esc_html__( 'Filter', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_filter_toggle'                              => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_title_by_filter'                            => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'advanced_search_mp_filter_toggle' => 'off',
								),
							),
							'advanced_search_mp_filter_category_level'                      => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'top' => esc_html__( 'Top Level' ),
									'sub' => esc_html__( 'Top + Sub Level' ),
								),
								'hide_on_dependencies' => array(
									'advanced_search_mp_filter_toggle' => 'off',
								),
							),
							'advanced_search_mp_filter_indicator_text'                      => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'advanced_search_mp_filter_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_clear_results'                        => array(
								'setting_type' => 'text',
							),
							'advanced_search_mp_filter_dropdown_width'                      => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_filter_toggle' => 'off',
								),
							),
						),
					),

					// GROUP: Search Results List
					'search_results_list' => array(
						'title' => esc_html__( 'Search Results List', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_show_top_category'                          => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_context_enabled'                          => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_context_characters'                          => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_results_list_size'                          => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_more_results_found'                         => array(
								'setting_type' => 'text',
							),
							'advanced_search_mp_no_results_found'                           => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Search Results Page
					'search_results_page' => array(
						'title' => esc_html__( 'Search Results Page', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_results_msg'                                => array(
								'setting_type' => 'text',
							),
							'advanced_search_mp_results_page_size'                          => array(
								'setting_type' => 'range',
							),
							'advanced_search_results_meta_created_on_toggle'                => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_results_meta_author_toggle'                    => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_results_meta_categories_toggle'                => array(
								'setting_type' => 'toggle'
							),
						),
					),

					// GROUP: Advanced
					'advanced' => array(
						'title' => esc_html__( 'Advanced', 'echo-knowledge-base' ),
						'fields' => array(
							'custom_css_class' => EPKB_Blocks_Settings::get_custom_css_class_setting(),
						)
					),
				),
			),

			// TAB: Style
			'style' => array(
				'title' => esc_html__( 'Style', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-adjust',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting( array(
								'default' => 'on'
							) ),
							'block_max_width' => EPKB_Blocks_Settings::get_block_max_width_setting(),
							'block_presets' => array(
								'setting_type' => 'presets_dropdown',
								'label' => esc_html__( 'Apply Preset', 'echo-knowledge-base' ),
								'presets' => $presets,
								'default' => 'current',
							),
						),
					),

					// GROUP: Search Box
					'search_box' => array(
						'title' => esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_background_color'                           => array(
								'setting_type' => 'color',
							),
							
							'advanced_search_mp_box_font_width'                             => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_box_padding'                                => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Search Box Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 500,
								'combined_settings' => array(
									'advanced_search_mp_box_padding_top' => array(
										'side' => 'top',
									),
									'advanced_search_mp_box_padding_bottom' => array(
										'side' => 'bottom',
									),
									'advanced_search_mp_box_padding_left' => array(
										'side' => 'left',
									),
									'advanced_search_mp_box_padding_right' => array(
										'side' => 'right',
									),
								),
							),
							'advanced_search_mp_box_margin'                                 => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Margin', 'echo-knowledge-base' ),
								'min' => -200,
								'max' => 200,
								'combined_settings' => array(
									'advanced_search_mp_box_margin_top' => array(
										'side' => 'top',
									),
									'advanced_search_mp_box_margin_bottom' => array(
										'side' => 'bottom',
									),
								),
							),
							'advanced_search_mp_background_gradient_toggle'                 => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_background_gradient_from_color'             => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'advanced_search_mp_background_gradient_toggle' => 'off',
								),
							),
							'advanced_search_mp_background_gradient_to_color'               => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'advanced_search_mp_background_gradient_toggle' => 'off',
								),
							),
							'advanced_search_mp_background_gradient_degree'                 => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_background_gradient_toggle' => 'off',
								),
							),
							'advanced_search_mp_background_gradient_opacity'                => array(
								'setting_type' => 'range_float',
								'hide_on_dependencies' => array(
									'advanced_search_mp_background_gradient_toggle' => 'off',
								),
							),
							'advanced_search_context_highlight_font_color'                           => array(
								'setting_type' => 'color',
							),
						),
					),

					// GROUP: Search Box Background
					'search_box_background' => array(
						'title' => esc_html__( 'Search Box Background', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_background_image_url'                       => array(
								'setting_type' => 'text',
							),
							'advanced_search_mp_background_image_position_x'                => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'center' => esc_html__( 'Center', 'echo-knowledge-base' ),
									'left'   => esc_html__( 'Left', 'echo-knowledge-base' ),
									'right'  => esc_html__( 'Right', 'echo-knowledge-base' )
								),
							),
							'advanced_search_mp_background_image_position_y'                => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'center'    => esc_html__( 'Center', 'echo-knowledge-base' ),
									'top'       => esc_html__( 'Top', 'echo-knowledge-base' ),
									'bottom'    => esc_html__( 'Bottom', 'echo-knowledge-base' )
								),
							),
							'advanced_search_mp_background_pattern_image_url'               => array(
								'setting_type' => 'text',
							),
							'advanced_search_mp_background_pattern_image_position_x'        => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'center' => esc_html__( 'Center', 'echo-knowledge-base' ),
									'left'   => esc_html__( 'Left', 'echo-knowledge-base' ),
									'right'  => esc_html__( 'Right', 'echo-knowledge-base' )
								),
							),
							'advanced_search_mp_background_pattern_image_position_y'        => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'center'    => esc_html__( 'Center', 'echo-knowledge-base' ),
									'top'       => esc_html__( 'Top', 'echo-knowledge-base' ),
									'bottom'    => esc_html__( 'Bottom', 'echo-knowledge-base' )
								),
							),
							'advanced_search_mp_background_pattern_image_opacity'           => array(
								'setting_type' => 'range_float',
							),

						),
					),

					// GROUP: Title
					'title' => array(
						'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_title_font_color'                           => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_title_padding_bottom'                       => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_typography_controls'                  => array(
								'label' => esc_html__( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance( array(
										'fontWeight' => 700,
									) ),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 24,
										'normal' => 36,
										'big' => 48,
									), 48 ),
								),
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_text_shadow_toggle'                   => array(
								'setting_type' => 'toggle',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_font_shadow_color'                    => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_text_shadow_toggle' => 'off',
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_text_shadow_x_offset'                 => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_text_shadow_toggle' => 'off',
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_text_shadow_y_offset'                 => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_text_shadow_toggle' => 'off',
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
							'advanced_search_mp_title_text_shadow_blur'                     => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_title_text_shadow_toggle' => 'off',
									'advanced_search_mp_title_toggle' => 'off',
								),
							),
						),
					),

					// GROUP: Description Below Title
					'description_below_title' => array(
						'title' => esc_html__( 'Description Below Title', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_description_below_title_padding'                    => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Description Below Title Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 500,
								'combined_settings' => array(
									'advanced_search_mp_description_below_title_padding_top' => array(
										'side' => 'top',
									),
									'advanced_search_mp_description_below_title_padding_bottom' => array(
										'side' => 'bottom',
									),
								),
							),
							'advanced_search_mp_description_below_title_text_shadow_toggle'         => array(
								'setting_type' => 'toggle'
							),
							'advanced_search_mp_description_below_title_text_shadow_x_offset'       => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_title_text_shadow_toggle' => 'off',
								),
							),
							'advanced_search_mp_description_below_title_text_shadow_y_offset'       => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_title_text_shadow_toggle' => 'off',
								),
							),
							'advanced_search_mp_description_below_title_text_shadow_blur'           => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_title_text_shadow_toggle' => 'off',
								),
							),
							'advanced_search_mp_description_below_title_font_shadow_color'          => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Shadow Color', 'echo-knowledge-base' ),
								'hide_on_dependencies' => array(
									'advanced_search_mp_description_below_title_text_shadow_toggle' => 'off',
								),
							),
							'advanced_search_mp_description_below_title_typography_controls'        => array(
								'label' => esc_html__( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
						),
					),

					// GROUP: Input
					'input' => array(
						'title' => esc_html__( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_input_box_search_icon_placement'            => array(
								'setting_type' => 'select_buttons_string',
							),
							'advanced_search_mp_input_box_loading_icon_placement'           => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'left'      => esc_html__( 'Left' ),
									'right'     => esc_html__( 'Right' )
								),
							),
							'advanced_search_mp_input_border_width'                         => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_box_input_width'                            => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_input_box_radius'                           => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_text_input_background_color'                => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_text_input_border_color'                    => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_input_box_typography_controls'              => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
							'advanced_search_mp_input_box_padding'                          => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Input Box Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 500,
								'combined_settings' => array(
									'advanced_search_mp_input_box_padding_top' => array(
										'side' => 'top',
									),
									'advanced_search_mp_input_box_padding_bottom' => array(
										'side' => 'bottom',
									),
									'advanced_search_mp_input_box_padding_left' => array(
										'side' => 'left',
									),
									'advanced_search_mp_input_box_padding_right' => array(
										'side' => 'right',
									),
								),
							),
							'advanced_search_mp_input_box_shadow_color'                      => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Box Shadow Color', 'echo-knowledge-base' ),		// Advanced Search has extended label because does not have color control with alpha channel for Editor UI
								'default' => '#00000000',															// Advanced Search supports empty shadow while blocks UI requires color to be valid
							),
							'advanced_search_mp_input_box_shadow_x_offset'                  => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_input_box_shadow_y_offset'                  => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_input_box_shadow_blur'                      => array(
								'setting_type' => 'range',
							),
							'advanced_search_mp_input_box_shadow_spread'                    => array(
								'setting_type' => 'range',
							),
						),
					),

					// GROUP: Description Below Input
					'description_below_input' => array(
						'title' => esc_html__( 'Description Below Input', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_description_below_input_padding'             => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Description Below Input Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 500,
								'combined_settings' => array(
									'advanced_search_mp_description_below_input_padding_top' => array(
										'side' => 'top',
									),
									'advanced_search_mp_description_below_input_padding_bottom' => array(
										'side' => 'bottom',
									),
								),
							),
							'advanced_search_mp_link_font_color'                             => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_description_below_input_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
						),
					),

					// GROUP: Search Results
					'search_results' => array(
						'title' => esc_html__( 'Search Results Page', 'echo-knowledge-base' ),
						'fields' => array(
							'advanced_search_mp_filter_box_font_color'                      => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_filter_box_background_color'                => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_search_result_category_color'               => array(
								'setting_type' => 'color',
							),
							'advanced_search_mp_results_typography_controls' => array(
								'label' => esc_html__( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
						),
					),
				),
			),
		);
	}
}