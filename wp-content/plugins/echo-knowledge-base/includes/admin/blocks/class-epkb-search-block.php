<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Search_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'search';

	protected $block_name = 'search';
	protected $block_var_name = 'search';
	protected $block_title = 'KB Search';
	protected $icon = 'search';
	protected $keywords = ['search', 'find', 'query', 'knowledge base'];	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		// when insert blocks programmatically we need to utilize non-static methods of the block classes, but we do not need hooks for this
		if ( ! $init_hooks ) {
			return;
		}

		// must be assigned to hook inside child class to enqueue unique assets for each block type
		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) ); // Frontend / Backend
	}

	/**
	 * Return the actual specific block content
	 * @param $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {

		$search_handler = new EPKB_ML_Search( $block_attributes, true );     ?>

		<div id="epkb-ml__module-search" class="epkb-ml__module">   <?php

			switch ( $block_attributes['ml_search_layout'] ) {
				case 'modern':
				default:
					$search_handler->display_modern_search_layout();
					break;

				case 'classic':
					$search_handler->display_classic_search_layout();
					break;
			} ?>

		</div>  <?php
	}

	/**
	 * Add required specific attributes to work correctly with KB core functionality
	 * @param $block_attributes
	 * @return array
	 */
	protected function add_this_block_required_kb_attributes( $block_attributes ) {
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::BASIC_LAYOUT;

		// Add AI collection ID from KB config for Simple Search
		$kb_id = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int)$block_attributes['kb_id'];
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		if ( ! empty( $kb_config['kb_ai_collection_id'] ) ) {
			$block_attributes['kb_ai_collection_id'] = $kb_config['kb_ai_collection_id'];
		}

		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

		$block_ui_specs = $this->get_block_ui_specs();

		$output = EPKB_ML_Search::get_inline_styles( $block_attributes, false, true );

		$output .=
			'.eckb-kb-block-search {
				margin-top: ' . intval( $block_attributes['search_box_margin_top'] ) . 'px !important;
				margin-bottom: ' . intval( $block_attributes['search_box_margin_bottom'] ) . 'px !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box {
				padding: ' . intval( $block_attributes['search_input_border_width'] ) . 'px !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search {
				padding-left: ' . intval( $block_attributes['search_box_padding_left'] ) . 'px !important;
				padding-right: ' . intval( $block_attributes['search_box_padding_right'] ) . 'px !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-title,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-title {
				font-size: ' . intval( $block_attributes['search_title_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['search_title_typography_controls'], $block_ui_specs['search_title_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['search_title_typography_controls'], $block_ui_specs['search_title_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_title_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_title_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__input,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__input {
				font-size: ' . intval( $block_attributes['search_input_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['search_input_typography_controls'], $block_ui_specs['search_input_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['search_input_typography_controls'], $block_ui_specs['search_input_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_input_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_input_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn {
				font-size: ' . intval( $block_attributes['search_button_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['search_button_typography_controls'], $block_ui_specs['search_button_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['search_button_typography_controls'], $block_ui_specs['search_button_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_button_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_button_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__icon,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__icon {
				font-size: ' . intval( $block_attributes['search_results_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['search_results_typography_controls'], $block_ui_specs['search_results_typography_controls'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__text,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__text {
				font-size: ' . intval( $block_attributes['search_results_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['search_results_typography_controls'], $block_ui_specs['search_results_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['search_results_typography_controls'], $block_ui_specs['search_results_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_results_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_results_typography_controls']['font_family'] ) ) . ' !important;
			}';

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'search_title_typography_controls',
			'search_input_typography_controls',
			'search_button_typography_controls',
			'search_results_typography_controls',
		);
	}

	/**
	 * Return list attributes with custom specs - they are not allowed in attributes when registering block, thus need to keep them separately
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {
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
							'kb_id' => EPKB_Blocks_Settings::get_kb_id_setting(),
							'ml_search_layout' => array(
								'setting_type' => 'select_buttons_string',
							),

							// Mention KB block template for Main Page
							'mention_kb_block_template' => EPKB_Blocks_Settings::get_kb_block_template_mention(),
						),
					),

					// GROUP: Title
					'title' => array(
						'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
						'fields' => array(
							'search_title' => array(
								'setting_type' => 'text',
							),
							'search_title_html_tag' => array(
								'setting_type' => 'select_buttons_string',
							),
						),
					),

					// GROUP: Input
					'input' => array(
						'title' => esc_html__( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'search_box_hint' => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Button
					'button' => array(
						'title' => esc_html__( 'Button', 'echo-knowledge-base' ),
						'fields' => array(
							'search_button_name' => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'ml_search_layout' => 'modern',
								),
							),
						),
					),

					// GROUP: Search Results
					'search_results' => array(
						'title' => esc_html__( 'Search Results', 'echo-knowledge-base' ),
						'fields' => array(
							'search_result_mode' => array(
								'setting_type' => 'select_buttons_string',
							),
							'no_results_found' => array(
								'setting_type' => 'text',
							),
							'min_search_word_size_msg' => array(
								'setting_type' => 'text',
							),
							'search_results_msg' => array(
								'setting_type' => 'text',
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

					// GROUP: Help + Setup Wizard
					'help-resources' => array(
						'title' => esc_html__( 'Help + Setup Wizard', 'echo-knowledge-base' ),
						'fields' => array(
							'help_resources_link' => EPKB_Blocks_Settings::get_help_resources_link(),
							'setup_wizard_link' => EPKB_Blocks_Settings::get_setup_wizard_link(),
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
							'search_box_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 500,
								'combined_settings' => array(
									'search_box_padding_top' => array(
										'side' => 'top',
									),
									'search_box_padding_bottom' => array(
										'side' => 'bottom',
									),
									'search_box_padding_left' => array(
										'side' => 'left',
									),
									'search_box_padding_right' => array(
										'side' => 'right',
									),
								),
							),
							'search_box_margin' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Margin', 'echo-knowledge-base' ),
								'min' => -200,
								'max' => 200,
								'combined_settings' => array(
									'search_box_margin_top' => array(
										'side' => 'top',
									),
									'search_box_margin_bottom' => array(
										'side' => 'bottom',
									),
								),
							),
							'search_background_color' => array(
								'setting_type' => 'color',
							),
						),
					),

					// GROUP: Title
					'title' => array(
						'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
						'fields' => array(
							'search_title_font_color' => array(
								'setting_type' => 'color',
							),
							'search_title_typography_controls' => array(
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
							),
						),
					),

					// GROUP: Input
					'input' => array(
						'title' => esc_html__( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'search_box_input_width' => array(
								'setting_type' => 'range',
							),
							'search_text_input_border_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'ml_search_layout' => 'modern',
								),
							),
							'search_input_border_width' => array(
								'setting_type' => 'range',
							),
							'search_box_input_height' => array(
								'setting_type' => 'select_buttons_string',
							),
							'search_text_input_background_color' => array(
								'setting_type' => 'color',
							),
							'search_input_typography_controls' => array(
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

					// GROUP: Button
					'button' => array(
						'title' => esc_html__( 'Button', 'echo-knowledge-base' ),
						'fields' => array(
							'search_btn_background_color' => array(
								'setting_type' => 'color',
							),
							'search_button_typography_controls' => array(
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

					// GROUP: Search Results
					'search_results' => array(
						'title' => esc_html__( 'Search Results', 'echo-knowledge-base' ),
						'fields' => array(
							'search_results_typography_controls' => array(
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