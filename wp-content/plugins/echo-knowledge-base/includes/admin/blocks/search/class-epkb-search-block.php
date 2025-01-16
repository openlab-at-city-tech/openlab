<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Search_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'search';

	protected $block_name = 'search';
	protected $block_var_name = 'search';

	public function __construct() {
		parent::__construct();

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
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

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
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn .epkbfa-ml-loading-icon,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn .epkbfa-ml-search-icon,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn .epkbfa-ml-loading-icon {
				top: calc(50% - 12px) !important;' . /* TODO: is it better to apply the same for KB search module in _module_search.scss or it is more safe to not affect existing users? */ '
			}
			.eckb-kb-block-search #epkb-ml__module-search {
				padding-left: ' . intval( $block_attributes['search_box_padding_left'] ) . 'px !important;
				padding-right: ' . intval( $block_attributes['search_box_padding_right'] ) . 'px !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-title,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-title {
				font-size: ' . intval( $block_attributes['search_title_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['search_title_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['search_title_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_title_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_title_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__input,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__input {
				font-size: ' . intval( $block_attributes['search_input_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['search_input_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['search_input_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_input_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_input_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box .epkb-ml-search-box__btn {
				font-size: ' . intval( $block_attributes['search_button_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['search_button_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['search_button_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_button_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_button_typography_controls']['font_family'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__icon,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__icon {
				font-size: ' . intval( $block_attributes['search_results_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['search_results_typography_controls']['font_appearance'] ) ) . ' !important;
			}
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__text,
			.eckb-kb-block-search #epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-results .epkb-article__text {
				font-size: ' . intval( $block_attributes['search_results_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['search_results_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['search_results_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['search_results_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['search_results_typography_controls']['font_family'] ) ) . ' !important;
			}';

		// TODO future: this part is defined in EPKB_ML_Search->get_inline_styles(), but for blocks we need to avoid using '#epkb-modular-main-page-container' selector - this can be removed here if refactor it inside EPKB_ML_Search->get_inline_styles()
		$output .=
			'.eckb-kb-block-search #epkb-ml__module-search {
			padding-top: ' . intval( $block_attributes['search_box_padding_top'] ) . 'px !important;
			padding-bottom: ' . intval( $block_attributes['search_box_padding_bottom'] ) . 'px !important;
			background-color: ' . sanitize_hex_color( $block_attributes['search_background_color'] ) . ' !important;
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
	 * Return list attributes with custom specs - they are not allowed in the {name}-block.json file
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {
		return array(

			// TAB: Settings
			'settings' => array(
				'title' => __( 'Settings', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => __( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'kb_id' => self::get_kb_id_setting(),
							'ml_search_layout' => array(
								'setting_type' => 'select_buttons_string',
							),

							// Mention KB block template for Main Page
							'mention_kb_block_template' => self::get_kb_block_template_mention(),
						),
					),

					// GROUP: Title
					'title' => array(
						'title' => __( 'Title', 'echo-knowledge-base' ),
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
						'title' => __( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'search_box_hint' => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Button
					'button' => array(
						'title' => __( 'Button', 'echo-knowledge-base' ),
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
						'title' => __( 'Search Results', 'echo-knowledge-base' ),
						'fields' => array(
							'search_result_mode' => array(
								'setting_type' => 'select_buttons_string',
							),
							/*'search_box_results_style' => array(	TODO future: hide for now in block UI - it can be tricky ot sync this with other blocks and misleading if sync with selected KB; still keep it JSON to be aligned with KB code
								'setting_type' => 'toggle',
								'label' => __( 'Match Article Colors', 'echo-knowledge-base' )
							),*/
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
						'title' => __( 'Advanced', 'echo-knowledge-base' ),
						'fields' => array(
							'custom_css_class' => self::get_custom_css_class_setting(),
						),
					),
				),
			),

			// TAB: Style
			'style' => array(
				'title' => __( 'Style', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-adjust',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => __( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'search_box_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => __( 'Padding', 'echo-knowledge-base' ),
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
								'label' => __( 'Margin', 'echo-knowledge-base' ),
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
						'title' => __( 'Title', 'echo-knowledge-base' ),
						'fields' => array(
							'search_title_typography_controls' => array(
								'label' => __( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance( array(
										'fontWeight' => 700,
									) ),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 24,
										'normal' => 36,
										'big' => 48,
									), 36 ),
								),
							),
							'search_title_font_color' => array(
								'setting_type' => 'color',
							),
						),
					),

					// GROUP: Input
					'input' => array(
						'title' => __( 'Input', 'echo-knowledge-base' ),
						'fields' => array(
							'search_input_typography_controls' => array(
								'label' => __( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
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
						),
					),

					// GROUP: Button
					'button' => array(
						'title' => __( 'Button', 'echo-knowledge-base' ),
						'fields' => array(
							'search_button_typography_controls' => array(
								'label' => __( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
							'search_btn_background_color' => array(
								'setting_type' => 'color',
							),
						),
					),

					// GROUP: Search Results
					'search_results' => array(
						'title' => __( 'Search Results', 'echo-knowledge-base' ),
						'fields' => array(
							'search_results_typography_controls' => array(
								'label' => __( 'Typography', 'echo-knowledge-base' ),
								'setting_type' => 'typography_controls',
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 16 ),
								),
							),
							/*'search_box_results_style' => array(	TODO future: hide for now in block UI - it can be tricky ot sync this with other blocks and misleading if sync with selected KB; still keep it JSON to be aligned with KB code
								'setting_type' => 'toggle',
								'label' => __( 'Match Article Colors', 'echo-knowledge-base' )
							),*/
						),
					),
				),
			),
		);
	}
}