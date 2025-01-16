<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Classic_Layout_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'classic-layout';

	protected $block_name = 'classic-layout';
	protected $block_var_name = 'classic_layout';

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
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::CLASSIC_LAYOUT;
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
		$block_selector = '.eckb-kb-block-classic-layout #epkb-ml__module-categories-articles #epkb-ml-classic-layout';
		$sidebar_selector = '.eckb-kb-block-classic-layout #epkb-ml-cat-article-sidebar';

		// Typography -----------------------------------------/
		$output .=
			/* Category Name Font */
			$block_selector . ' ' . '.epkb-category-section__head_title .epkb-category-section__head_title__text,' .
			$sidebar_selector . ' ' . '.epkb-ml-article-section__head {
				font-size: ' . intval( $block_attributes['section_head_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_head_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_head_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_head_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_head_typography_controls']['font_family'] ) ) . ' !important;
			}'.
			/* Category Description Font */
			$block_selector . ' ' . '.epkb-cat-desc {
				font-size: ' . intval( $block_attributes['section_head_description_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_head_description_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_head_description_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_head_description_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_head_description_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Subcategory Name Font */
			$block_selector . ' ' . '.epkb-ml-2-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-category__text {
				font-size: ' . intval( $block_attributes['section_subcategory_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['section_subcategory_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['section_subcategory_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['section_subcategory_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['section_subcategory_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Article Icon Font */
			$block_selector . ' ' . '.eckb-article-title__icon {
				font-size: ' . intval( $block_attributes['article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['article_typography_controls']['font_appearance'] ) ) . ' !important;
			}' .
			/* Article Title Font */
			$block_selector . ' ' . '.epkb-article__text,' .
			$sidebar_selector . ' ' . '.epkb-article__text {
				font-size: ' . intval( $block_attributes['article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['article_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['article_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['article_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['article_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Article Toggle Font */
			$block_selector . ' ' . '.epkb-ml-articles-coming-soon,' .
			$block_selector . ' ' . '.epkb-ml-article-count {
				font-size: ' . intval( $block_attributes['article_collapse_message_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( self::get_font_appearance_weight( $block_attributes['article_collapse_message_typography_controls']['font_appearance'] ) ) . ' !important;
				font-style: ' . esc_attr( self::get_font_appearance_style( $block_attributes['article_collapse_message_typography_controls']['font_appearance'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['article_collapse_message_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['article_collapse_message_typography_controls']['font_family'] ) ) . ' !important;
			}';

		// Container -----------------------------------------/
		$border_style_escaped = 'none';
		if ( $block_attributes['section_border_width'] > 0 ) {
			$border_style_escaped = 'solid';
		}

		$output .=
			$block_selector . ' ' . '.epkb-category-section {
				border-color: ' . sanitize_hex_color( $block_attributes['section_border_color'] ) . ' !important;
				border-width: ' . intval( $block_attributes['section_border_width'] ) . 'px !important;
				border-radius: ' . intval( $block_attributes['section_border_radius'] ) . 'px !important;
				border-style: ' . $border_style_escaped . ' !important;
			}';

		// Headings  -----------------------------------------/
		if ( $block_attributes['section_head_category_icon_location'] == 'top' ) {
			$output .=
				'.eckb-kb-block-classic-layout #epkb-ml__module-categories-articles {
					margin-top:60px;
				}';
		}

		if ( $block_attributes['ml_categories_articles_top_category_icon_bg_color_toggle'] == 'off' ) {
			$output .=
				$block_selector . ' ' . '.epkb-category-section__head .epkb-cat-icon {
					background-color: transparent !important;
				}';
		}

		$output .=
			$block_selector . ' ' . '.epkb-category-section__head .epkb-cat-icon--font {
		        font-size: ' . intval( $block_attributes['section_head_category_icon_size'] ) . 'px;
			}' .
			$block_selector . ' ' . '.epkb-category-section__head .epkb-cat-icon--image {
			    background-color: ' . $block_attributes['ml_categories_articles_top_category_icon_bg_color'] . ';
			    width: ' . ( intval( $block_attributes['section_head_category_icon_size'] ) + 20 ) . 'px;
			    max-width: ' . ( intval( $block_attributes['section_head_category_icon_size'] ) + 20 ) . 'px;
			    height: ' . ( intval( $block_attributes['section_head_category_icon_size'] ) + 20 ) . 'px;
			}' .
			$block_selector . ' ' . '.epkb-category-section__head_title__text {
			    color: ' . sanitize_hex_color( $block_attributes['section_head_font_color'] ) . ';
			}';

		$output .=
			$block_selector . ' ' . '.epkb-category-section .epkb-category-section__head_icon .epkb-cat-icon--font {
			    color: ' . sanitize_hex_color( $block_attributes['section_head_category_icon_color'] ) . ';
			    background-color: ' . sanitize_hex_color( $block_attributes['ml_categories_articles_top_category_icon_bg_color'] ) . '; 
			}' .
			$block_selector . ' ' . '.epkb-category-section__head_desc {
			    color: ' . $block_attributes['section_head_description_font_color'] . ';
			}';

		// Articles  -----------------------------------------/

		// Variable - No CSS required
		// Minimum  - Min Height of Article List
		// Maximum  - Max Height of Article List with Overflow and scroll bar
		if ( $block_attributes['section_box_height_mode'] == 'section_fixed_height' )  {
			$output .=
				$block_selector . '.epkb-ml-classic-layout--height-fixed .epkb-category-section__body{
					height: ' . intval( $block_attributes['section_body_height'] ) . 'px;
					overflow:auto;
				}';
		}
		if ( $block_attributes['section_box_height_mode'] == 'section_min_height' )  {
			$output .=
				$block_selector . '.epkb-ml-classic-layout--height-fixed .epkb-category-section {
					min-height: ' . intval( $block_attributes['section_body_height'] ) . 'px;
				}';
		}
		if ( $block_attributes['ml_categories_articles_collapse_categories'] == 'all_collapsed' ) {
			$output .=
				$block_selector . ' ' . '.epkb-ml-articles-show-more {
					border-color: ' . sanitize_hex_color( $block_attributes['section_head_category_icon_color'] ) . ';
				}' .
				$block_selector . ' ' . '.epkb-ml-articles-show-more:hover {
					color: #fff;
					background-color: ' . sanitize_hex_color( $block_attributes['section_box_expand_hover_color'] ) . ';
				}';
		}

		$output .=
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__icon {
			    color: ' . sanitize_hex_color( $block_attributes['article_icon_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__text {
			    color: ' . sanitize_hex_color( $block_attributes['article_font_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-category__icon .epkb-cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-category__icon .epkb-cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-category__icon .epkb-cat-icon,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-category__icon .epkb-cat-icon {
			    color: ' . sanitize_hex_color( $block_attributes['section_category_icon_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-ml-2-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-3-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-4-lvl-category__text,' .
			$block_selector . ' ' . '.epkb-ml-5-lvl-category__text {
			    color: ' . sanitize_hex_color( $block_attributes['section_category_font_color'] ) . ';
			}';

		$output .=
			$block_selector . ' ' . '.epkb-category-section__body li {
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
			'article_collapse_message_typography_controls'
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
							'ml_categories_articles_collapse_categories' => array(
								'setting_type' => 'select_buttons_string',	// default KB is selection, we need fit the KB core values (cannot switch to toggle with 'on' and 'off' values)
							),
							'section_desc_text_on' => array(
								'setting_type' => 'toggle',
							),
							'category_empty_msg' => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Articles List
					'articles-list' => array(
						'title' => esc_html__( 'Articles List', 'echo-knowledge-base' ),
						'fields' => array(
							'ml_categories_articles_article_text' => array(
								'setting_type' => 'text',
							),
							'ml_categories_articles_articles_text' => array(
								'setting_type' => 'text',
							),
						)
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
							'section_box_expand_hover_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'ml_categories_articles_collapse_categories' => 'all_expanded',
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
							'section_box_height_mode' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'section_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
									'section_min_height' => esc_html__( 'Minimum', 'echo-knowledge-base' ),
									'section_fixed_height' => esc_html__( 'Maximum', 'echo-knowledge-base' )
								),
							),
							'section_body_height' => array(
								'setting_type' => 'range',
								'hide_on_dependencies' => array(
									'section_box_height_mode' => 'section_no_height',
								),
							),
							'section_category_font_color' => array(
								'setting_type' => 'color',
							),
							'section_category_icon_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Subcategory Icon', 'echo-knowledge-base' ),
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
							'article_collapse_message_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Article Count Typography', 'echo-knowledge-base' ),
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