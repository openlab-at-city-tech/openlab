<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_FAQs_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'faqs';

	protected $block_name = 'faqs';
	protected $block_var_name = 'faqs';
	protected $block_title = 'KB FAQs';
	protected $icon = 'editor-table';
	protected $keywords = ['knowledge base', 'faqs', 'questions', 'frequently asked questions'];	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization

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
	public function render_block_inner( $block_attributes ) {	?>
		<div id="epkb-ml__module-faqs" class="epkb-ml__module">   <?php
			$faqs_handler = new EPKB_ML_FAQs( $block_attributes );
			$faqs_handler->display_faqs_module( false ); ?>
		</div>	<?php
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

		$output = '';

		$block_selector = '.eckb-kb-block-faqs #epkb-ml__module-faqs';

		$output .=
			/* Title Font */
			$block_selector . ' ' . '.epkb-faqs-title {
				font-size: ' . intval( $block_attributes['faq_title_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['faq_title_typography_controls'], $block_ui_specs['faq_title_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['faq_title_typography_controls'], $block_ui_specs['faq_title_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['faq_title_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['faq_title_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Group Title Font */
			$block_selector . ' ' . '.epkb-faqs__cat-header__title {
				font-size: ' . intval( $block_attributes['faq_group_title_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['faq_group_title_typography_controls'], $block_ui_specs['faq_group_title_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['faq_group_title_typography_controls'], $block_ui_specs['faq_group_title_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['faq_group_title_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['faq_group_title_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Question Font */
			$block_selector . ' ' . '.epkb-faqs-cat-content-container .epkb-faqs__item__question .epkb-faqs__item__question__text {
				font-size: ' . intval( $block_attributes['faq_question_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['faq_question_typography_controls'], $block_ui_specs['faq_question_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['faq_question_typography_controls'], $block_ui_specs['faq_question_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['faq_question_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['faq_question_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Answer Font */
			$block_selector . ' ' . '.epkb-faqs-cat-content-container .epkb-faqs__item__answer .epkb-faqs__item__answer__text {
				font-size: ' . intval( $block_attributes['faq_answer_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['faq_answer_typography_controls'], $block_ui_specs['faq_answer_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['faq_answer_typography_controls'], $block_ui_specs['faq_answer_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['faq_answer_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['faq_answer_typography_controls']['font_family'] ) ) . ' !important;
			}';

		if ( $block_attributes['faq_border_mode'] == 'separator' ) {
			$output .=
				$block_selector . ' ' . '.epkb-faqs-border-separator .epkb-faqs__item__question {
				    border-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_border_color'] ) . '!important;
				}' .
				$block_selector . ' ' . '.epkb-faqs-border-separator .epkb-faqs__item-container--active .epkb-faqs__item__question {
					border-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_border_color'] ) . '!important;
				}';
		}

		$faq_question_background_color_escaped = empty( $block_attributes['faq_question_background_color'] ) ? 'transparent' : EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_question_background_color'] );
		$faq_answer_background_color_escaped = empty( $block_attributes['faq_answer_background_color'] ) ? 'transparent' : EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_answer_background_color'] );
		$faq_icon_color_escaped = empty( $block_attributes['faq_icon_color'] ) ? 'transparent' : EPKB_Utilities::sanitize_hex_color(  $block_attributes['faq_icon_color'] );

		$output .=
			$block_selector . ' ' . '.epkb-faqs__item__question {
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_question_text_color'] ) . '!important;
			}' .
			$block_selector . ' ' . '.epkb-faqs__item-container {
				border-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_border_color'] ) . '!important;
			}' .
			$block_selector . ' ' . '.epkb-faqs__item-container--active .epkb-faqs__item__question {
				border-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_border_color'] ) . '!important;
			}' .
			$block_selector . ' ' . '.epkb-faqs__item__question {
				background-color: ' .  $faq_question_background_color_escaped . ';
			}' .
			$block_selector . ' ' . '.epkb-faqs__item__answer {
				background-color: ' .  $faq_answer_background_color_escaped . ';
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['faq_answer_text_color'] ) . '!important;
			}' .
			$block_selector . ' ' . '.epkb-faqs__item__question__icon {
				color: '.  $faq_icon_color_escaped . ';
			}';

		// Display the FAQs Title set in the FAQ Module or shortcode Parameter (could be empty)
		if ( $block_attributes['ml_faqs_title_location'] != 'none' && ! empty( $block_attributes['ml_faqs_title_text'] ) ) {
			$output .= $block_selector . ' ' . '.epkb-faqs-title { text-align: ' . esc_attr( $block_attributes['ml_faqs_title_location'] ) . '!important; }';
		}

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'faq_title_typography_controls',
			'faq_group_title_typography_controls',
			'faq_question_typography_controls',
			'faq_answer_typography_controls',
		);
	}

	/**
	 * Always return array: either empty on WP error or pairs id=>name for available FAQ Groups
	 * @return array
	 */
	private static function get_faq_groups_list() {
		$all_faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $all_faq_groups ) ) {
			return array();
		}
		return $all_faq_groups;
	}

	/**
	 * Return list of presets to use as 'options' array for presets setting UI
	 * @return array[]
	 */
	private static function get_all_preset_settings() {

		$all_design_settings = array(
			'0' => array(
				'label' => '-----',
				'settings' => array(),
			),
		);

		$design_names = EPKB_FAQs_Utilities::get_design_names();
		foreach ( $design_names as $key => $label ) {
			$all_design_settings[ $key ] = array(
				'label' => $label,
				'settings' => EPKB_FAQs_Utilities::get_design_settings( $key ),
			);
		}

		return $all_design_settings;
	}

	/**
	 * Return list attributes with custom specs - they are not allowed in attributes when registering block, thus need to keep them separately
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {

		$faq_groups_list = self::get_faq_groups_list();
		$default_faq_group = empty( $faq_groups_list ) ? array() : array( array_key_first( $faq_groups_list ) );

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
							'kb_id' => array(
								'setting_type' => 'internal',
								'default' => EPKB_KB_Config_DB::DEFAULT_KB_ID
							),
							'faq_group_ids' => array(
								'setting_type' => 'checkbox_multi_select',
								'label' => esc_html__( 'FAQ Groups', 'echo-knowledge-base' ),
								'options' => $faq_groups_list,
								'default' => $default_faq_group,
							),
							'faq_groups_link' => array(
								'setting_type' => 'section_description',
								'description' => esc_html__( 'Manage your FAQs and FAQ Groups', 'echo-knowledge-base' ),
								'link_text' => esc_html__( 'Open FAQs Admin', 'echo-knowledge-base' ),
								'link_url' => admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-faqs' ),
							),
							'ml_faqs_title_location' => array(
								'setting_type' => 'select_buttons_string',
							),
							'ml_faqs_title_text' => array(
								'setting_type' => 'text',
							),
							'faq_empty_msg' => array(
								'setting_type' => 'text',
							),
							'faq_nof_columns' => array(
								'setting_type' => 'select_buttons',
							),
							'faq_compact_mode' => array(
								'setting_type' => 'select_buttons_string',
							),
							'faq_open_mode' => array(
								'setting_type' => 'dropdown',
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
							'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting(),
							'block_max_width' => EPKB_Blocks_Settings::get_block_max_width_setting(),
							'faq_presets' => array(
								'setting_type' => 'presets_dropdown',
								'label' => esc_html__( 'Apply Design', 'echo-knowledge-base' ),
								'presets' => self::get_all_preset_settings(),
								'default' => '0',
							),
							'faq_title_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 24,
										'normal' => 28,
										'big' => 32,
									), 32 ),
								),
								'hide_on_dependencies' => array(
									'ml_faqs_title_location' => 'none',
								),
							)
						),
					),

					// GROUP: FAQ ICONS
					'faq-icons' => array(
						'title' => esc_html__( 'Icons', 'echo-knowledge-base' ),
						'fields' => array(
							'faq_icon_type' => array(
								'setting_type' => 'select_buttons_icon',
								'options'     => array(
									'icon_plus_box' => array(
										'label' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
										'icon_class' => 'epkbfa-plus-square',
										'depen'
									),
									'icon_plus_circle' => array(
										'label' => _x( 'Plus circle', 'icon type', 'echo-knowledge-base' ),
										'icon_class' => 'epkbfa-plus-circle',
									),
									'icon_plus' => array(
										'label' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
										'icon_class' => 'epkbfa-plus',
									),
									'icon_arrow_caret' => array(
										'label' => _x( 'Arrow Down Caret', 'icon type', 'echo-knowledge-base' ),
										'icon_class' => 'epkbfa-angle-down',
									),
									'icon_arrow_angle' => array(
										'label' => _x( 'Arrow Right Angle', 'icon type', 'echo-knowledge-base' ),
										'icon_class' => 'ep_font_icon_arrow_carrot_right',
									),
								),
								'hide_on_dependencies' => array(
									'faq_icon_location' => 'no_icons',
								),
							),
							'faq_icon_location' => array(
								'setting_type' => 'select_buttons_string',
							),
							'faq_icon_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'faq_icon_location' => 'no_icons',
								),
							),
						),
					),

					// GROUP: FAQ BORDER
					'faq-border' => array(
						'title' => esc_html__( 'Border', 'echo-knowledge-base' ),
						'fields' => array(
							'faq_border_mode' => array(
								'setting_type' => 'select_buttons_string',
							),
							'faq_border_style' => array(
								'setting_type' => 'select_buttons_string',
							),
							'faq_border_color' => array(
								'setting_type' => 'color',
							),
						),
					),

					// GROUP: FAQ BORDER
					'faq-groups' => array(
						'title' => esc_html__( 'Groups', 'echo-knowledge-base' ),
						'fields' => array(
							'faq_group_title_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Group Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 18,
										'normal' => 21,
										'big' => 36,
									), 21 ),
								),
								'hide_on_selection_amount_dependencies' => array(
									'faq_group_ids' => 1,
								),
							),
						),
					),

					// GROUP: FAQ QUESTION
					'faq-question' => array(
						'title' => esc_html__( 'Questions', 'echo-knowledge-base' ),
						'fields' => array(
							'faq_question_text_color' => array(
								'setting_type' => 'color',
							),
							'faq_question_background_color' => array(
								'setting_type' => 'color',
							),
							'faq_question_space_between' => array(
								'setting_type' => 'select_buttons_string',
							),
							'faq_question_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Question Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 16,
										'normal' => 18,
										'big' => 21,
									), 18 ),
								),
							),
						),
					),

					// GROUP: FAQ ANSWER
					'faq-answer' => array(
						'title' => esc_html__( 'Answers', 'echo-knowledge-base' ),
						'fields' => array(
							'faq_answer_text_color' => array(
								'setting_type' => 'color',
							),
							'faq_answer_background_color' => array(
								'setting_type' => 'color',
							),
							'faq_answer_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Answer Typography', 'echo-knowledge-base' ),
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
			)
		);
	}
}