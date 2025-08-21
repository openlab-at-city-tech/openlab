<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Grid_Layout_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'grid-layout';

	protected $block_name = 'grid-layout';
	protected $block_var_name = 'grid_layout';
	protected $block_title = 'KB Grid Layout';
	protected $icon = 'editor-table';
	protected $keywords = ['knowledge base', 'layout', 'articles', 'categories'];	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		// when insert blocks programmatically we need to utilize non-static methods of the block classes, but we do not need hooks for this
		if ( ! $init_hooks ) {
			return;
		}

		// must be assigned to hook inside child class to enqueue unique assets for each block type
		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) ); // Frontend / Backend

		add_action( 'save_post', array( $this, 'update_kb_setting_on_save_post'), 10, 3 );
	}

	/**
	 * Check if the block is available
	 * @return bool
	 */
	protected static function is_block_available() {
		return class_exists( 'EL'.'AY_Blocks' );
	}

	/**
	 * Return handle for block public styles
	 * @return string
	 */
	protected function get_block_public_styles_handle() {
		return 'elay-' . $this->block_name . '-block';
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
		EPKB_Core_Utilities::register_elay_block_public_styles( $this->block_name, $suffix, $block_styles_dependencies );
	}

	protected function register_block_public_scripts( $suffix ) {
		if ( ! self::is_block_available() ) {
			return;
		}
		EPKB_Core_Utilities::register_elay_block_public_scripts( $suffix );
	}

	/**
	 * Return the actual specific block content
	 * @param $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {

		// for add-on block it may be too early to render content in the Block Editor
		if ( ! has_filter( 'grid_display_categories_and_articles' ) ) {
			return;
		}

		// sync KB article icon toggle with EL.AY article icon toggle (unless we want to have both in block settings UI)
		$block_attributes['article_icon_toggle'] = $block_attributes['sidebar_article_icon_toggle'];

		$handler = new EPKB_Modular_Main_Page();
		$handler->setup_layout_data( $block_attributes );

		// show message that articles are coming soon if the current KB does not have any Category
		if ( $handler->has_kb_categories() ) {

			// render content
			$handler->categories_articles_module( $block_attributes );

		} else {
			// render no content message
			$handler->show_categories_missing_message();
		}
	}

	/**
	 * Add required specific attributes to work correctly with KB core functionality
	 * @param $block_attributes
	 * @return array
	 */
	protected function add_this_block_required_kb_attributes( $block_attributes ) {
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::GRID_LAYOUT;
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

		$block_ui_specs = $this->get_block_ui_specs();

		$output = EPKB_Modular_Main_Page::get_layout_sidebar_inline_styles( $block_attributes );

		$output .= apply_filters( 'epkb_grid_layout_block_inline_styles', '', $block_attributes, $block_ui_specs );

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'grid_section_head_typography_controls',
			'grid_section_description_typography_controls',
			'grid_section_article_count_typography_controls',
			'article_typography_controls',
		);
	}

	/**
	 * Return handle for block public scripts
	 * @return string
	 */
	protected function get_block_public_scripts_handle() {
		return 'elay-public-scripts';
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
							'kb_block_template_toggle' => EPKB_Blocks_Settings::get_kb_block_template_toggle(),
							'templates_for_kb' => EPKB_Blocks_Settings::get_kb_legacy_template_toggle(),
							'mention_kb_block_template' => EPKB_Blocks_Settings::get_kb_block_template_mention(),
							'grid_nof_columns' => array(
								'setting_type' => 'select_buttons_string',
								'label' => esc_html__( 'Number of Columns', 'echo-knowledge-base' ),
							),
						),
					),

					// GROUP: Category Box
					'category-box' => array(
						'title' => esc_html__( 'Category Box', 'echo-knowledge-base' ),
						'fields' => array(

							'section_hyperlink_text_on' => array(
								'setting_type' => 'toggle',
							),
						),
					),
					// GROUP: Category Box Header
					'category-box-header' => array(
						'title' => esc_html__( 'Category Box Header', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_section_desc_text_on' => array(
								'setting_type' => 'toggle',
							),
							'grid_section_divider' => array(
								'setting_type' => 'toggle',
							),
						),
					),
					// GROUP: Category Box Body
					'category-box-body' => array(
						'title' => esc_html__( 'Category Box Body', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_section_article_count' => array(
								'setting_type' => 'toggle',
							),
							'grid_article_count_text' => array(
								'setting_type' => 'text',
							),
							'grid_article_count_plural_text' => array(
								'setting_type' => 'text',
							),
							'grid_category_link_text' => array(        // Category link to Category Archive Page
								'setting_type' => 'text',
							),
							'category_empty_msg' => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Sidebar
					'sidebar' => array(
						'title' => esc_html__( 'Sidebar', 'echo-knowledge-base' ),
						'fields' => array(
							'ml_categories_articles_sidebar_toggle' => array(
								'setting_type' => 'toggle',
							),
							'ml_categories_articles_sidebar_location' => array(
								'setting_type' => 'select_buttons_string',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_categories_articles_sidebar_desktop_width' => array(
								'setting_type' => 'select_buttons',
								'options' => array(
									25 => esc_html__( 'Small', 'echo-knowledge-base' ),
									28 => esc_html__( 'Medium', 'echo-knowledge-base' ),
									30 => esc_html__( 'Large', 'echo-knowledge-base' ),
								),
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_categories_articles_sidebar_position_1' => array(
								'setting_type' => 'dropdown',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_categories_articles_sidebar_position_2' => array(
								'setting_type' => 'dropdown',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_articles_list_nof_articles_displayed' => array(
								'setting_type' => 'number',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_articles_list_popular_articles_msg' => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_articles_list_newest_articles_msg' => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'ml_articles_list_recent_articles_msg' => array(
								'setting_type' => 'text',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
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
							'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting(),
							'block_max_width' => EPKB_Blocks_Settings::get_block_max_width_setting(),
							'block_presets' => array(
								'setting_type' => 'presets_dropdown',
								'label' => esc_html__( 'Apply Preset', 'echo-knowledge-base' ),
								'presets' => EPKB_Blocks_Settings::get_all_preset_settings( self::EPKB_BLOCK_NAME, EPKB_Layout::GRID_LAYOUT ),
								'default' => 'current',
							),
						),
					),

					// GROUP: Category Box
					'category-box' => array(
						'title' => esc_html__( 'Category Box', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_section_box_height_mode' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'section_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
									'section_min_height' => esc_html__( 'Minimum', 'echo-knowledge-base' ),
									'section_fixed_height' => esc_html__( 'Maximum', 'echo-knowledge-base' )
								),
							),
							'grid_section_body_height' => array(
								'setting_type' => 'range',
							),
							'section_border_radius' => array(
								'setting_type' => 'range',
							),
							'section_border_width' => array(
								'setting_type' => 'range',
							),
							'section_border_color' => array(
								'setting_type' => 'color',
							),
							'grid_section_box_shadow' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'no_shadow' => esc_html__( 'No Shadow', 'echo-knowledge-base' ),
									'section_light_shadow' => esc_html__( 'Light', 'echo-knowledge-base' ),
									'section_medium_shadow' => esc_html__( 'Medium', 'echo-knowledge-base' ),
									'section_bottom_shadow' => esc_html__( 'Bottom', 'echo-knowledge-base' )
								),
							),
							'grid_section_box_hover' => array(
								'setting_type' => 'dropdown',
								'options'     => array(
									'no_effect' => esc_html__( 'No Effect', 'echo-knowledge-base'),
									'hover-1' => esc_html__( 'Opacity 70%', 'echo-knowledge-base' ),
									'hover-2' => esc_html__( 'Opacity 80%', 'echo-knowledge-base' ),
									'hover-3' => esc_html__( 'Opacity 90%', 'echo-knowledge-base' ),
									'hover-4' => esc_html__( 'Lightest Grey Background', 'echo-knowledge-base' ),
									'hover-5' => esc_html__( 'Lighter Grey Background', 'echo-knowledge-base' ),
								),
								'default'     => 'no_effect'
							),

						),
					),

					// GROUP: Category Box Header
					'category-box-header' => array(
						'title' => esc_html__( 'Category Box Header', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_section_head_alignment' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'left'   => is_rtl() ? esc_html__( 'Right', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
									'center' => esc_html__( 'Centered', 'echo-knowledge-base' ),
									'right'  => is_rtl() ? esc_html__( 'Left', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' ),
								),
								'default'     => 'center'
							),
							'grid_section_head_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Section Header Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 200,
								'combined_settings' => array(
									'grid_section_head_padding_top' => array(
										'side' => 'top',
									),
									'grid_section_head_padding_bottom' => array(
										'side' => 'bottom',
									),
									'grid_section_head_padding_left' => array(
										'side' => 'left',
									),
									'grid_section_head_padding_right' => array(
										'side' => 'right',
									),
								),
							),
							'section_head_font_color' => array(
								'setting_type' => 'color',
							),
							'section_head_background_color' => array(
								'setting_type' => 'color',
							),
							'grid_section_head_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Name Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 18,
										'normal' => 21,
										'big' => 36,
									), 21 ),
								),
							),
							'grid_section_cat_name_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Category Name Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 200,
								'combined_settings' => array(
									'grid_section_cat_name_padding_top' => array(
										'side' => 'top',
									),
									'grid_section_cat_name_padding_bottom' => array(
										'side' => 'bottom',
									),
									'grid_section_cat_name_padding_left' => array(
										'side' => 'left',
									),
									'grid_section_cat_name_padding_right' => array(
										'side' => 'right',
									),
								),
							),
							'section_head_description_font_color' => array(
								'setting_type' => 'color',
							),
							'grid_section_description_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Description Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 19,
									), 14 ),
								),
								'hide_on_dependencies' => array(
									'grid_section_desc_text_on' => 'off',
								),
							),
							'grid_section_desc_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Category Description Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 200,
								'combined_settings' => array(
									'grid_section_desc_padding_top' => array(
										'side' => 'top',
									),
									'grid_section_desc_padding_bottom' => array(
										'side' => 'bottom',
									),
									'grid_section_desc_padding_left' => array(
										'side' => 'left',
									),
									'grid_section_desc_padding_right' => array(
										'side' => 'right',
									),
								),
								'hide_on_dependencies' => array(
									'grid_section_desc_text_on' => 'off',
								),
							),
							'section_divider_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Section Divider Color', 'echo-knowledge-base' ),
								'hide_on_dependencies' => array(
									'grid_section_divider' => 'off',
								),
							),
							'grid_section_divider_thickness' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'grid_section_divider' => 'off',
								),
							),
						),
					),

					// GROUP: Category Box Icons
					'category-box-icons' => array(
						'title' => esc_html__( 'Category Icons', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_category_icon_location' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'no_icons' => esc_html__( 'No Icons', 'echo-knowledge-base' ),
									'top' => esc_html__( 'Top', 'echo-knowledge-base' ),
									'left'   => is_rtl() ? esc_html__( 'Right', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
									'right'  => is_rtl() ? esc_html__( 'Left', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' ),
									'bottom' => esc_html__( 'Bottom', 'echo-knowledge-base' ),
								),
							),
							'section_head_category_icon_color' => array(
								'setting_type' => 'color',
							),							'grid_section_icon_size' => array(
								'setting_type' => 'range',
							),
							'grid_section_icon_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Icon Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 200,
								'combined_settings' => array(
									'grid_section_icon_padding_top' => array(
										'side' => 'top',
									),
									'grid_section_icon_padding_bottom' => array(
										'side' => 'bottom',
									),
									'grid_section_icon_padding_left' => array(
										'side' => 'left',
									),
									'grid_section_icon_padding_right' => array(
										'side' => 'right',
									),
								),
							),
						),
					),

					// GROUP: Category Box Body
					'category-box-body' => array(
						'title' => esc_html__( 'Category Box Body', 'echo-knowledge-base' ),
						'fields' => array(
							'grid_section_body_alignment' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'left'   => is_rtl() ? esc_html__( 'Right', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
									'center' => esc_html__( 'Centered', 'echo-knowledge-base' ),
									'right'  => is_rtl() ? esc_html__( 'Left', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' ),
								),
								'default'     => 'center'
							),
							'section_category_font_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Body Text / Article', 'echo-knowledge-base' ),
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'section_body_background_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'grid_section_article_count_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Body Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 12,
										'normal' => 14,
										'big' => 16,
									), 14 ),
								),
							),
							'grid_section_body_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Category Body Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 200,
								'combined_settings' => array(
									'grid_section_body_padding_top' => array(
										'side' => 'top',
									),
									'grid_section_body_padding_bottom' => array(
										'side' => 'bottom',
									),
									'grid_section_body_padding_left' => array(
										'side' => 'left',
									),
									'grid_section_body_padding_right' => array(
										'side' => 'right',
									),
								),
							),
						),
					),

					// GROUP: Sidebar
					'sidebar' => array(
						'title' => esc_html__( 'Sidebar', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_article_icon_toggle' => array(
								'setting_type' => 'toggle',
								'label' => esc_html__( 'Show Article Icon', 'echo-knowledge-base' ),
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'elay_article_icon' => array(
								'setting_type' => 'dropdown',
								'hide_on_dependencies' => array(
									'sidebar_article_icon_toggle' => 'off',
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'article_icon_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'sidebar_article_icon_toggle' => 'off',
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'article_list_spacing' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
							'article_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Article Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 12,
										'normal' => 14,
										'big' => 16,
									), 14 ),
								),
								'hide_on_dependencies' => array(
									'ml_categories_articles_sidebar_toggle' => 'off',
								),
							),
						),
					),
				),
			)
		);
	}
}