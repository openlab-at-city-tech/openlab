<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Sidebar_Layout_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'sidebar-layout';

	protected $block_name = 'sidebar-layout';
	protected $block_var_name = 'sidebar_layout';
	protected $block_title = 'KB Sidebar Layout';
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
		if ( ! has_filter( 'sidebar_display_categories_and_articles' ) ) {
			return;
		}

		global $eckb_is_kb_main_page;
		$eckb_is_kb_main_page = true;

		// Sidebar settings are controlled by Configuration -> Article Page
		$block_attributes = self::apply_article_sidebar_settings( $block_attributes );

		$handler = new EPKB_Modular_Main_Page();
		$handler->setup_layout_data( $block_attributes );

		$intro_text = apply_filters( 'eckb_main_page_sidebar_intro_text', $block_attributes['sidebar_main_page_intro_text'], $block_attributes['id'] );
		$temp_article = new stdClass();
		$temp_article->ID = 0;
		$temp_article->post_title = esc_html__( 'Demo Article', 'echo-knowledge-base' );
		// Use 'post' for the filter as it is the same content as in the usual page/post
		$temp_article->post_content = wp_kses( $intro_text, EPKB_Utilities::get_extended_html_tags( true ) );
		$temp_article = new WP_Post( $temp_article );
		$block_attributes['sidebar_welcome'] = 'on';
		$block_attributes['article_content_enable_back_navigation'] = 'off';
		$block_attributes['prev_next_navigation_enable'] = 'off';
		$block_attributes['article_content_enable_rows'] = 'off';

		// for sidebar component priority the block attributes represent separate UI settings, while legacy KB code is using combined setting
		// $block_attributes['article_sidebar_component_priority'] = EPKB_KB_Config_Controller::convert_ui_data_to_article_sidebar_component_priority( $block_attributes );

		// hardcoded settings (we do not show them in non-block Settings UI, let's do the same for block UI)
		$block_attributes['article-left-sidebar-match'] = 'off';
		$block_attributes['article-right-sidebar-match'] = 'off';
		$block_attributes['article-mobile-break-point-v2'] = '768';
		$block_attributes['article-tablet-break-point-v2'] = '1025';
		$block_attributes['article_search_toggle'] = 'on';

		$layout_output = EPKB_Articles_Setup::get_article_content_and_features( $temp_article, $temp_article->post_content, $block_attributes );
		$handler->set_sidebar_layout_content( $layout_output );

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
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::SIDEBAR_LAYOUT;
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

		$block_ui_specs = $this->get_block_ui_specs();

		// EL.AY Sidebar settings are controlled by Configuration -> Article Page
		$block_attributes = self::apply_article_sidebar_settings( $block_attributes );

		$output = EPKB_Modular_Main_Page::get_layout_sidebar_inline_styles( $block_attributes );

		$output .= apply_filters( 'epkb_sidebar_layout_block_inline_styles', '', $block_attributes, $block_ui_specs );

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'sidebar_section_category_typography_controls',
			'sidebar_section_body_typography_controls',
			'article_typography_controls',
			/*'sidebar_section_category_desc_typography_controls',
			'sidebar_section_subcategory_typography_controls',*/
		);
	}

	/**
	 * Sidebar settings are controlled by Frontend Editor/UI Configuration for Article Page
	 * @param $block_attributes
	 * @return mixed
	 */
	private static function apply_article_sidebar_settings( $block_attributes ) {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $block_attributes['kb_id'] );
		$add_ons_kb_config = apply_filters( 'elay_block_config', $kb_config, $block_attributes['kb_id'] );
		if ( is_wp_error( $add_ons_kb_config ) || empty( $add_ons_kb_config ) || ! is_array( $add_ons_kb_config ) || count( $add_ons_kb_config ) < 100 ) {
			return false;
		}
		$kb_config = $add_ons_kb_config;

		// sync KB article icon toggle with EL.AY article icon toggle (unless we want to have both in block settings UI)
		$block_attributes['article_icon_toggle'] = $kb_config['sidebar_article_icon_toggle'];

		// controlled by Configuration -> Article Page
		$block_attributes['article_sidebar_component_priority'] = $kb_config['article_sidebar_component_priority'];
		$block_attributes['sidebar_article_icon_toggle'] = $kb_config['sidebar_article_icon_toggle'];
		$block_attributes['article-left-sidebar-toggle'] = $kb_config['article-left-sidebar-toggle'];
		$block_attributes['article-right-sidebar-toggle'] = $kb_config['article-right-sidebar-toggle'];
		$block_attributes['article_nav_sidebar_type_left'] = $kb_config['article_nav_sidebar_type_left'];
		$block_attributes['article_nav_sidebar_type_right'] = $kb_config['article_nav_sidebar_type_right'];
		$block_attributes['sidebar_section_border_color'] = $kb_config['sidebar_section_border_color'];
		$block_attributes['sidebar_side_bar_height_mode'] = $kb_config['sidebar_side_bar_height_mode'];
		$block_attributes['sidebar_side_bar_height'] = $kb_config['sidebar_side_bar_height'];
		$block_attributes['sidebar_background_color'] = $kb_config['sidebar_background_color'];
		$block_attributes['sidebar_section_head_font_color'] = $kb_config['sidebar_section_head_font_color'];
		$block_attributes['sidebar_section_head_background_color'] = $kb_config['sidebar_section_head_background_color'];
		$block_attributes['sidebar_section_head_description_font_color'] = $kb_config['sidebar_section_head_description_font_color'];
		$block_attributes['article-left-sidebar-desktop-width-v2'] = $kb_config['article-left-sidebar-desktop-width-v2'];
		$block_attributes['article-right-sidebar-desktop-width-v2'] = $kb_config['article-right-sidebar-desktop-width-v2'];
		$block_attributes['article-left-sidebar-tablet-width-v2'] = $kb_config['article-left-sidebar-tablet-width-v2'];
		$block_attributes['article-right-sidebar-tablet-width-v2'] = $kb_config['article-right-sidebar-tablet-width-v2'];
		$block_attributes['article-left-sidebar-background-color-v2'] = $kb_config['article-left-sidebar-background-color-v2'];
		$block_attributes['article-right-sidebar-background-color-v2'] = $kb_config['article-right-sidebar-background-color-v2'];
		$block_attributes['sidebar_nof_articles_displayed'] = $kb_config['sidebar_nof_articles_displayed'];
		$block_attributes['sidebar_show_articles_before_categories'] = $kb_config['sidebar_show_articles_before_categories'];
		$block_attributes['sidebar_expand_articles_icon'] = $kb_config['sidebar_expand_articles_icon'];
		$block_attributes['sidebar_article_active_bold'] = $kb_config['sidebar_article_active_bold'];
		$block_attributes['sidebar_section_divider'] = $kb_config['sidebar_section_divider'];
		$block_attributes['sidebar_section_divider_color'] = $kb_config['sidebar_section_divider_color'];
		$block_attributes['article_icon_color'] = $kb_config['sidebar_article_icon_color'];
		$block_attributes['sidebar_article_icon_color'] = $kb_config['sidebar_article_icon_color'];
		$block_attributes['article_font_color'] = $kb_config['sidebar_article_font_color'];
		$block_attributes['sidebar_article_font_color'] = $kb_config['sidebar_article_font_color'];
		$block_attributes['sidebar_section_category_icon_color'] = $kb_config['sidebar_section_category_icon_color'];
		$block_attributes['sidebar_section_category_font_color'] = $kb_config['sidebar_section_category_font_color'];
		$block_attributes['category_box_container_background_color'] = $kb_config['category_box_container_background_color'];
		$block_attributes['category_box_count_background_color'] = $kb_config['category_box_count_background_color'];
		$block_attributes['category_box_count_text_color'] = $kb_config['category_box_count_text_color'];
		$block_attributes['category_box_count_border_color'] = $kb_config['category_box_count_border_color'];
		$block_attributes['sidebar_top_categories_collapsed'] = $kb_config['sidebar_top_categories_collapsed'];
		$block_attributes['sidebar_category_empty_msg'] = $kb_config['sidebar_category_empty_msg'];
		$block_attributes['sidebar_collapse_articles_msg'] = $kb_config['sidebar_collapse_articles_msg'];
		$block_attributes['sidebar_show_all_articles_msg'] = $kb_config['sidebar_show_all_articles_msg'];
		$block_attributes['sidebar_main_page_intro_text'] = isset( $kb_config['sidebar_main_page_intro_text'] ) ? $kb_config['sidebar_main_page_intro_text'] : '';
		$block_attributes['article_list_spacing'] = $kb_config['article_list_spacing'];
		$block_attributes['elay_article_icon'] = isset( $kb_config['elay_sidebar_article_icon'] ) ? $kb_config['elay_sidebar_article_icon'] : '';
		$block_attributes['navigation_sidebar_sticky_toggle'] = $kb_config['navigation_sidebar_sticky_toggle'];

		// not controlled by UI
		$block_attributes['sidebar_article_underline'] = $kb_config['sidebar_category_empty_msg'];
		$block_attributes['sidebar_section_desc_text_on'] = $kb_config['sidebar_section_desc_text_on'];
		$block_attributes['sidebar_section_box_shadow'] = $kb_config['sidebar_section_box_shadow'];
		$block_attributes['sidebar_scroll_bar'] = $kb_config['sidebar_scroll_bar'];
		$block_attributes['sidebar_section_divider_thickness'] = $kb_config['sidebar_section_divider_thickness'];
		$block_attributes['sidebar_section_head_padding_top'] = $kb_config['sidebar_section_head_padding_top'];
		$block_attributes['sidebar_section_head_padding_bottom'] = $kb_config['sidebar_section_head_padding_bottom'];
		$block_attributes['sidebar_section_head_padding_left'] = $kb_config['sidebar_section_head_padding_left'];
		$block_attributes['sidebar_section_head_padding_right'] = $kb_config['sidebar_section_head_padding_right'];
		$block_attributes['sidebar_section_head_alignment'] = $kb_config['sidebar_section_head_alignment'];
		$block_attributes['sidebar_section_border_radius'] = $kb_config['sidebar_section_border_radius'];
		$block_attributes['sidebar_section_border_width'] = $kb_config['sidebar_section_border_width'];
		$block_attributes['template_widget_sidebar_defaults'] = $kb_config['template_widget_sidebar_defaults'];
		$block_attributes['sidebar_section_body_padding_top'] = $kb_config['sidebar_section_body_padding_top'];
		$block_attributes['sidebar_section_body_padding_bottom'] = $kb_config['sidebar_section_body_padding_bottom'];
		$block_attributes['sidebar_section_body_padding_left'] = $kb_config['sidebar_section_body_padding_left'];
		$block_attributes['sidebar_section_body_padding_right'] = $kb_config['sidebar_section_body_padding_right'];
		$block_attributes['article_list_margin'] = $kb_config['article_list_margin'];

		return $block_attributes;
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
						),
					),
					'config-settings' => array(
						'title' => esc_html__( 'Navigation Sidebar Setting', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_main_page_intro_text_link_to_edit' => array(
								'setting_type' => 'section_description',
								'description' => esc_html__( 'To Edit Introduction Text', 'echo-knowledge-base' ) . ', ',
								'link_text' => esc_html__( 'click here', 'echo-knowledge-base' ),
								'link_url' => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-configuration#tools__other' ),
							),
							'sidebar_main_page_link_to_edit' => array(
								'setting_type' => 'section_description',
								'description' => esc_html__( 'Sidebar block settings are controlled by the Article Page Sidebar settings. To edit sidebar settings', 'echo-knowledge-base' ) . ', ',
								'link_text' => esc_html__( 'click here', 'echo-knowledge-base' ),
								'link_url' => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-configuration#settings__article-page__article-page-sidebar' ),
							),
						),
					),

					// GROUP: Left Sidebar
					/*'left-sidebar' => array(
						'title' => esc_html__( 'Left Sidebar', 'echo-knowledge-base' ),
						'fields' => array(
							'article-left-sidebar-toggle' => array(
								'setting_type' => 'toggle',
							),
							'article_nav_sidebar_type_left' => array(
								'setting_type' => 'dropdown',
								'hide_on_dependencies' => array(
									'article-left-sidebar-toggle' => 'off',
								),
							),
							'nav_sidebar_left' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Categories and Articles Navigation', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-left-sidebar-toggle' => 'off',
								),
							),
							'kb_sidebar_left' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Widgets from KB Sidebar', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-left-sidebar-toggle' => 'off',
								),
							),
							'toc_left' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Table of Contents (TOC)', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-left-sidebar-toggle' => 'off',
								),
							),
						),
					),*/

					// GROUP: Right Sidebar
					/*'right-sidebar' => array(
						'title' => esc_html__( 'Right Sidebar', 'echo-knowledge-base' ),
						'fields' => array(
							'article-right-sidebar-toggle' => array(
								'setting_type' => 'toggle',
							),
							'article_nav_sidebar_type_right' => array(
								'setting_type' => 'dropdown',
								'hide_on_dependencies' => array(
									'article-right-sidebar-toggle' => 'off',
								),
							),
							'nav_sidebar_right' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Categories and Articles Navigation', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-right-sidebar-toggle' => 'off',
								),
							),
							'kb_sidebar_right' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Widgets from KB Sidebar', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-right-sidebar-toggle' => 'off',
								),
							),
							'toc_right' => array(
								'setting_type' => 'dropdown',
								'label' => esc_html__( 'Table of Contents (TOC)', 'echo-knowledge-base' ),
								'options' => self::get_sidebar_component_priority_options(),
								'hide_on_dependencies' => array(
									'article-right-sidebar-toggle' => 'off',
								),
							),
						),
					),*/

					// GROUP: Categories
					/*'category-box' => array(
						'title' => esc_html__( 'Categories', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_top_categories_collapsed' => array(
								'setting_type' => 'toggle'
							),
							'sidebar_section_desc_text_on' => array(
								'setting_type' => 'toggle'
							),
							'sidebar_category_empty_msg' => array(
								'setting_type' => 'text',
							),
						),
					),*/

					// GROUP: Articles
					/*'articles-list' => array(
						'title' => esc_html__( 'Articles', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_nof_articles_displayed' => array(
								'setting_type' => 'range',
							),
							'sidebar_article_underline' => array(
								'setting_type' => 'toggle'
							),
							'sidebar_collapse_articles_msg' => array(
								'setting_type' => 'text',
							),
							'sidebar_show_all_articles_msg' => array(
								'setting_type' => 'text',
							),
						),
					),*/

					// GROUP: Featured Articles Sidebar
					'modular-sidebar' => array(
						'title' => esc_html__( 'Featured Articles Sidebar', 'echo-knowledge-base' ),
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
							'sidebar_section_category_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Typography', 'echo-knowledge-base' ),
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
							/*'block_presets' => array(
								'setting_type' => 'presets_dropdown',
								'label' => esc_html__( 'Apply Preset', 'echo-knowledge-base' ),
								'presets' => EPKB_Blocks_Settings::get_all_preset_settings( self::EPKB_BLOCK_NAME, EPKB_Layout::SIDEBAR_LAYOUT ),
								'default' => 'current',
							),*/
							'article-content-background-color-v2' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_body_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Content Typography', 'echo-knowledge-base' ),
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
						),
					),

					// GROUP: Featured Articles Sidebar
					'modular-sidebar' => array(
						'title' => esc_html__( 'Featured Articles Sidebar', 'echo-knowledge-base' ),
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
							'sidebar_section_category_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Typography', 'echo-knowledge-base' ),
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
							),
						),
					),

					// GROUP: Category Box Header
					/*'category-box-header' => array(
						'title' => esc_html__( 'Category Box Header', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_section_head_alignment' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'left' => esc_html__( 'Left', 'echo-knowledge-base' ),
									'center' => esc_html__( 'Centered', 'echo-knowledge-base' ),
									'right' => esc_html__( 'Right', 'echo-knowledge-base' )
								),
							),
							'sidebar_section_head_font_color' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_head_background_color' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_head_description_font_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'sidebar_section_desc_text_on' => 'off',
								),
							),
							'sidebar_section_head_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Category Name Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 20,
								'combined_settings' => array(
									'sidebar_section_head_padding_top' => array(
										'side' => 'top',
									),
									'sidebar_section_head_padding_bottom' => array(
										'side' => 'bottom',
									),
									'sidebar_section_head_padding_left' => array(
										'side' => 'left',
									),
									'sidebar_section_head_padding_right' => array(
										'side' => 'right',
									),
								),
							),
						),
					),*/

					// GROUP: Category Box
					/*'category-box' => array(
						'title' => esc_html__( 'Category Box', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_side_bar_height_mode' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'side_bar_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
									'side_bar_fixed_height' => esc_html__( 'Fixed (Scrollbar)', 'echo-knowledge-base' )
								),
							),
							'sidebar_side_bar_height' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'sidebar_side_bar_height_mode' => 'side_bar_no_height',
								),
							),
							'sidebar_scroll_bar' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'slim_scrollbar'    => esc_html__( 'Slim', 'echo-knowledge-base' ),
									'default_scrollbar' => esc_html__( 'Default', 'echo-knowledge-base' )
								),
							),
							'sidebar_section_border_radius' => array(
								'setting_type' => 'range',
							),
							'sidebar_section_border_width' => array(
								'setting_type' => 'range',
							),
							'sidebar_section_border_color' => array(
								'setting_type' => 'color',
							),
							'category_box_container_background_color' => array(
								'setting_type' => 'color',
							),
							'category_box_count_background_color' => array(
								'setting_type' => 'color',
							),
							'category_box_count_text_color' => array(
								'setting_type' => 'color',
							),
							'category_box_count_border_color' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_box_shadow' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'no_shadow' => esc_html__( 'No Shadow', 'echo-knowledge-base' ),
									'section_light_shadow' => esc_html__( 'Light Shadow', 'echo-knowledge-base' ),
									'section_medium_shadow' => esc_html__( 'Medium Shadow', 'echo-knowledge-base' ),
									'section_bottom_shadow' => esc_html__( 'Bottom Shadow', 'echo-knowledge-base' )
								),
							),
							'sidebar_section_divider' => array(
								'setting_type' => 'toggle',
								'label' => esc_html__( 'Top Category Border Bottom', 'echo-knowledge-base' ),
							),
							'sidebar_section_divider_thickness' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'sidebar_section_divider' => 'off',
								),
							),
							'sidebar_section_divider_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'sidebar_section_divider' => 'off',
								),
							),
							'sidebar_section_body_padding' => array(
								'setting_type' => 'box_control_combined',
								'label' => esc_html__( 'Category Body Padding', 'echo-knowledge-base' ),
								'min' => 0,
								'max' => 50,
								'combined_settings' => array(
									'sidebar_section_body_padding_top' => array(
										'side' => 'top',
									),
									'sidebar_section_body_padding_bottom' => array(
										'side' => 'bottom',
									),
									'sidebar_section_body_padding_left' => array(
										'side' => 'left',
									),
									'sidebar_section_body_padding_right' => array(
										'side' => 'right',
									),
								),
							),
						),
					),*/

					// GROUP: Categories
					/*'category-box-body' => array(
						'title' => esc_html__( 'Categories', 'echo-knowledge-base' ),
						'fields' => array(
							'sidebar_section_category_desc_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Description Typography', 'echo-knowledge-base' ),
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
									'sidebar_section_desc_text_on' => 'off',
								),
							),
							'sidebar_expand_articles_icon' => array(
								'setting_type' => 'dropdown',
								'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
									'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
									'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
									'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-knowledge-base' ),
									'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-knowledge-base' ),
									'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' )
								),
							),
							'sidebar_show_articles_before_categories' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'on' => _x( 'Before Categories', 'echo-knowledge-base' ),
									'off' => _x( 'After Categories', 'echo-knowledge-base' ),
								),
							),
							'sidebar_section_category_icon_color' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_category_font_color' => array(
								'setting_type' => 'color',
							),
							'sidebar_section_subcategory_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Subcategory Typography', 'echo-knowledge-base' ),
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
						),
					),*/

					// GROUP: Articles
				/*	'articles-list' => array(
						'title' => esc_html__( 'Articles', 'echo-knowledge-base' ),
						'fields' => array(
							'article_icon_toggle' => array(
								'setting_type' => 'toggle',
								'label' => esc_html__( 'Show Article Icon', 'echo-knowledge-base' ),
							),
							'article_list_margin' => array(
								'setting_type' => 'box_control',
								'side' => 'left',
							),
							'article_font_color' => array(
								'setting_type' => 'color',
							),
							'article_icon_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'article_icon_toggle' => 'off',
								),
							),
							'sidebar_background_color' => array(
								'setting_type' => 'color',
							),
							'elay_article_icon' => array(
								'setting_type' => 'dropdown',
							),
							'article_list_spacing' => array(
								'setting_type' => 'range',
							),
						),
					),*/
				),
			)
		);
	}

	/*private static function get_sidebar_component_priority_options() {
		return array(
			'0' => '-----',
			'1' => esc_html__( 'Position', 'echo-knowledge-base' ) . ' 1',
			'2' => esc_html__( 'Position', 'echo-knowledge-base' ) . ' 2',
			'3' => esc_html__( 'Position', 'echo-knowledge-base' ) . ' 3',
		);
	}*/
}