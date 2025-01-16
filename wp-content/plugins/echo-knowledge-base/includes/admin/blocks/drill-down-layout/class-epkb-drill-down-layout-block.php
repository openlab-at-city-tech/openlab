<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Drill_Down_Layout_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'drill-down-layout';

	protected $block_name = 'drill-down-layout';
	protected $block_var_name = 'drill_down_layout';

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

		$handler = new EPKB_Modular_Main_Page();
		$handler->setup_layout_data_for_blocks( $block_attributes );

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
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::DRILL_DOWN_LAYOUT;
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

		$output = EPKB_Modular_Main_Page::get_layout_sidebar_inline_styles( $block_attributes );

		// define block selectors to ensure the inline styles do not affect other blocks - do not include empty space here ' ' as some of the selectors my need no space
		$block_selector = '.eckb-kb-block-drill-down-layout #epkb-ml__module-categories-articles #epkb-ml-drill-down-layout';
		$sidebar_selector = '.eckb-kb-block-drill-down-layout #epkb-ml-cat-article-sidebar';

		// Typography -----------------------------------------/
		$output .=
			/* Category Name Font */
			$block_selector . ' ' . '.epkb-ml-top__cat-title,' .
			$sidebar_selector . ' ' . '.epkb-ml-article-section__head {
				font-size: ' . intval( $block_attributes['section_head_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_head_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_head_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_head_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_head_typography_controls']['font_family'] ) ) . ' !important;
			}'.
			/* Category Description Font */
			$block_selector . ' ' . '.epkb-ml-1-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl__desc {
				font-size: ' . intval( $block_attributes['section_head_description_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_head_description_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_head_description_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_head_description_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_head_description_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Subcategory Name Font */
			$block_selector . ' ' . '.epkb-ml-2-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl__cat-title {
				font-size: ' . intval( $block_attributes['section_subcategory_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_subcategory_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_subcategory_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_subcategory_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_subcategory_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Article Icon Font */
			$block_selector . ' ' . '.eckb-article-title__icon {
				font-size: ' . intval( $block_attributes['article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . self::get_font_appearance_weight( $block_attributes['article_typography_controls']['font_appearance'] ) . ' !important;
			}' .
			/* Article Title Font */
			$block_selector . ' ' . '.epkb-article__text,' .
			$sidebar_selector . ' ' . '.epkb-article__text {
				font-size: ' . intval( $block_attributes['article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . self::get_font_appearance_weight( $block_attributes['article_typography_controls']['font_appearance'] ) . ' !important;
				font-style: ' . self::get_font_appearance_style( $block_attributes['article_typography_controls']['font_appearance'] ) . ' !important;
				font-family: ' . ( empty( $block_attributes['article_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['article_typography_controls']['font_family'] ) ) . ' !important;
			}';

		// Section  -----------------------------------------/
		if ( $block_attributes['ml_categories_articles_top_category_icon_bg_color_toggle'] == 'off' ) {
			$output .=
				$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon {
					background-color: transparent !important;
				}';
		}

		$border_style_escaped = 'none';
		if ( $block_attributes['section_border_width'] > 0 ) {
			$border_style_escaped = 'solid';
		}

		$output .=
			$block_selector . ' ' . '.epkb-ml-top__cat-container {
				border-color: ' . sanitize_hex_color( $block_attributes['section_border_color'] ) . ' !important;
				border-width:' . intval( $block_attributes['section_border_width'] ) . 'px !important;
				border-radius:' . intval( $block_attributes['section_border_radius'] ) . 'px !important;
				border-style: ' . $border_style_escaped. ' !important;
			}';

		$output .=
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon--font {
			    font-size: ' . intval( $block_attributes['section_head_category_icon_size'] ) . 'px;
			}' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-title {
			    color: ' . sanitize_hex_color( $block_attributes['section_head_font_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon {
			    color: ' . sanitize_hex_color( $block_attributes['section_head_category_icon_color'] ) . ';
			    background-color: ' . sanitize_hex_color( $block_attributes['ml_categories_articles_top_category_icon_bg_color'] ) . ';
			    width: ' . ( intval( $block_attributes['section_head_category_icon_size'] ) + 40 ) . 'px;
			    height: ' . ( intval( $block_attributes['section_head_category_icon_size'] ) + 40 ) . 'px;
			}' .
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__icon {
			    color: ' . sanitize_hex_color( $block_attributes['article_icon_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__text {
			    color: ' . sanitize_hex_color( $block_attributes['article_font_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-all-categories-content-container,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl__cat-content {
			    background-color: ' . sanitize_hex_color( $block_attributes['ml_categories_articles_article_bg_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-1-lvl__cat-content .epkb-ml-1-lvl-desc-articles .epkb-ml-1-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl__cat-content .epkb-ml-2-lvl-desc-articles .epkb-ml-2-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl__cat-content .epkb-ml-3-lvl-desc-articles .epkb-ml-3-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl__cat-content .epkb-ml-4-lvl-desc-articles .epkb-ml-4-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl__cat-content .epkb-ml-5-lvl-desc-articles .epkb-ml-5-lvl__desc,' .
			$block_selector . ' ' . '.epkb-ml-1-lvl__cat-content .epkb-ml-articles-coming-soon,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl__cat-content .epkb-ml-articles-coming-soon,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl__cat-content .epkb-ml-articles-coming-soon,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl__cat-content .epkb-ml-articles-coming-soon,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl__cat-content .epkb-ml-articles-coming-soon {
			    color: ' . sanitize_hex_color( $block_attributes['section_head_description_font_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-back-button {
			    background-color: ' . sanitize_hex_color( $block_attributes['ml_categories_articles_back_button_bg_color'] ) . '!important;
			}' .
			$block_selector . ' ' . '.epkb-back-button:hover {
			    background-color: ' . EPKB_Utilities::darken_hex_color( sanitize_hex_color( $block_attributes['ml_categories_articles_back_button_bg_color'] ), 0.2 )  . '!important;
			}' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-container,' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-container:hover {
			    border-color: ' . sanitize_hex_color( $block_attributes['section_border_color'] ) . ' !important;
			}' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-top-categories-button-container .epkb-ml-top__cat-container--active:hover {
			    box-shadow: 0 0 0 4px ' . sanitize_hex_color( $block_attributes['section_border_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container:hover,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container:hover,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container:hover,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container:hover {
			    border-color: ' . sanitize_hex_color( $block_attributes['section_border_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-1-lvl-categories-button-container .epkb-ml__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml__cat-container--active,' .
			$block_selector . ' ' . '.epkb-ml-1-lvl-categories-button-container .epkb-ml__cat-container--active:hover,' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml__cat-container--active:hover,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml__cat-container--active:hover,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml__cat-container--active:hover,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml__cat-container--active:hover{
			    box-shadow: 0px 1px 0 0px ' . sanitize_hex_color( $block_attributes['section_category_icon_color'] ) . ';
			    border-color: ' . sanitize_hex_color( $block_attributes['section_category_icon_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container .epkb-ml-2-lvl__cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container .epkb-ml-3-lvl__cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container .epkb-ml-4-lvl__cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container .epkb-ml-5-lvl__cat-icon {
			    color: ' . sanitize_hex_color( $block_attributes['section_category_icon_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container .epkb-ml-2-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container .epkb-ml-3-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container .epkb-ml-4-lvl__cat-title,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container .epkb-ml-5-lvl__cat-title {
			    color: ' . sanitize_hex_color( $block_attributes['section_category_font_color'] ) . ';
			}';

		$output .=
			$block_selector . ' ' . '.epkb-ml-articles-list li {
		        padding-top: ' . intval( $block_attributes['article_list_spacing'] ) . 'px !important;
		        padding-bottom: ' . intval( $block_attributes['article_list_spacing'] ) . 'px !important;
	            line-height: 1 !important;
			}';

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'section_head_typography_controls',
			'section_head_description_typography_controls',
			'section_subcategory_typography_controls',
			'article_typography_controls',
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
				'title' => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'kb_id' => self::get_kb_id_setting(),
							'nof_columns' => array(
								'setting_type' => 'select_buttons_string',
								'label' => esc_html__( 'Number of Columns', 'echo-knowledge-base' ),
							),
							'ml_categories_articles_back_button_text' => array(
								'setting_type' => 'text',
							),

							// Mention KB block template for Main Page
							'mention_kb_block_template' => self::get_kb_block_template_mention(),
						),
					),

					// GROUP: Category Box
					'category-box' => array(
						'title' => esc_html__( 'Category Box', 'echo-knowledge-base' ),
						'fields' => array(
							'ml_categories_articles_category_title_html_tag' => array(
								'setting_type' => 'select_buttons_string',
								'options' => array(
									'h2' => 'H2',
									'h3' => 'H3',
									'h4' => 'H4',
									'h5' => 'H5',
									'h6' => 'H6',
								),
							),
							'section_desc_text_on' => array(
								'setting_type' => 'toggle',
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
							'custom_css_class' => self::get_custom_css_class_setting(),
						)
					),
				),
			),

			// TAB: Style
			'style' => array(
				'title' => esc_html__( 'Style', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-adjust',
				'groups' => array(

					// GROUP: Category Box
					'category-box' => array(
						'title' => esc_html__( 'Category Box', 'echo-knowledge-base' ),
						'fields' => array(
							'section_border_width' => array(
								'setting_type' => 'range',
							),
							'section_border_radius' => array(
								'setting_type' => 'range',
							),
							'section_border_color' => array(
								'setting_type' => 'color',
							)
						),
					),

					// GROUP: Category Box Header
					'category-box-header' => array(
						'title' => esc_html__( 'Category Box Header', 'echo-knowledge-base' ),
						'fields' => array(
							'ml_categories_articles_top_category_icon_bg_color_toggle' => array(
								'setting_type' => 'toggle',
							),
							'ml_categories_articles_top_category_icon_bg_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'ml_categories_articles_top_category_icon_bg_color_toggle' => 'off',
								),
							),
							'section_head_category_icon_location' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'no_icons'      => esc_html__( 'No Icons', 'echo-knowledge-base' ),
									'top'           => esc_html__( 'Top',   'echo-knowledge-base' ),
									'left'          => is_rtl() ? esc_html__( 'Start', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
									'right'         => is_rtl() ? esc_html__( 'End', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' )
								),
							),
							'section_head_category_icon_size' => array(
								'setting_type' => 'range',
								'default' => 21,
							),
							'section_head_category_icon_color' => array(
								'setting_type' => 'color',
							),
							'section_head_font_color' => array(
								'setting_type' => 'color',
							),
							'section_head_description_font_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'section_desc_text_on' => 'off',
								),
							),
							'section_head_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 18,
										'normal' => 21,
										'big' => 36,
									), 21 ),
								),
							),
							'section_head_description_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Category Description Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 12,
										'normal' => 14,
										'big' => 16,
									), 14 ),
								),
								'hide_on_dependencies' => array(
									'section_desc_text_on' => 'off',
								),
							),
						),
					),

					// GROUP: Category Box Body
					'category-box-body' => array(
						'title' => esc_html__( 'Category Box Body', 'echo-knowledge-base' ),
						'fields' => array(
							'section_category_font_color' => array(
								'setting_type' => 'color',
							),
							'section_category_icon_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Subcategory Icon', 'echo-knowledge-base' ),
							),
							'ml_categories_articles_back_button_bg_color' => array(
								'setting_type' => 'color',
							),
							'section_subcategory_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Subcategory Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 18,
										'normal' => 21,
										'big' => 36,
									), 21 ),
								),
							),
						),
					),

					// GROUP: Articles List
					'articles-list' => array(
						'title' => esc_html__( 'Articles List', 'echo-knowledge-base' ),
						'fields' => array(
							'article_icon_toggle' => array(
								'setting_type' => 'toggle',
								'label' => esc_html__( 'Show Article Icon', 'echo-knowledge-base' ),
							),
							'article_icon_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'article_icon_toggle' => 'off',
								),
							),
							'article_font_color' => array(
								'setting_type' => 'color',
							),
							'ml_categories_articles_article_bg_color' => array(
								'setting_type' => 'color',
							),
							'article_list_spacing' => array(
								'setting_type' => 'range',
							),
							'article_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Article Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => self::get_typography_control_font_family(),
									'font_appearance' => self::get_typography_control_font_appearance(),
									'font_size' => self::get_typography_control_font_size( array(
										'small' => 12,
										'normal' => 14,
										'big' => 16,
									), 14 ),
								),
							),
						),
					),
				),
			)
		);
	}
}