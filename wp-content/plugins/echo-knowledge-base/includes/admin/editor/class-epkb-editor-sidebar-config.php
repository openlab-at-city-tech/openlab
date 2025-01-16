<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Store Visual Editor configuration data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Editor_Sidebar_Config {

	// Visual Editor Tabs
	const EDITOR_TAB_CONTENT = 'content';
	const EDITOR_TAB_STYLE = 'style';
	const EDITOR_TAB_FEATURES = 'features';
	const EDITOR_TAB_ADVANCED = 'advanced';

	const EDITOR_GROUP_DIMENSIONS = 'dimensions';

	/**
	 * Sidebar Content zone
	 *
	 * @return array
	 */
	private static function epkb_sidebar_navigation_zone() {

		$settings = [

			// Content Tab
			'sidebar_category_empty_msg'            => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-articles-coming-soon',
				'text' => '1'
			],

			// Style Tab
			'sidebar_section_category_heading'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => esc_html__( 'Category Name', 'echo-knowledge-base' ),
			],
			'sidebar_section_category_typography'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb_section_heading,
							#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container .epkb-sidebar__heading__inner__cat-name',
			],

			'sidebar_section_head_font_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-category-level-1, 
							.epkb-sidebar-template .epkb-category-level-1 a, 
							#epkb-sidebar-container-v2 .epkb-sidebar__heading__inner .epkb-sidebar__heading__inner__name, 
							#epkb-sidebar-container-v2 .epkb-sidebar__heading__inner .epkb-sidebar__heading__inner__cat-name,
							#epkb-sidebar-container-v2 .epkb-sidebar__heading__inner .epkb-sidebar__heading__inner__name>a',
				'style_name' => 'color'
			],
			'sidebar_section_head_background_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb_section_heading,
							#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
				'style_name' => 'background-color'
			],
			'sidebar_section_category_description_heading'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => esc_html__( 'Category Description', 'echo-knowledge-base' ),
			],
			'sidebar_section_category_typography_desc'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb_section_heading,
							#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container .epkb-sidebar__heading__inner__desc p',
			],
			'sidebar_section_subcategory_typography'        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-category-level-2-3__cat-name,
							#epkb-sidebar-container-v2 .epkb-category-level-2-3__cat-name>*',
			],
			'sidebar_section_head_description_font_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb_section_heading p, #epkb-sidebar-container-v2 .epkb-sidebar__heading__inner .epkb-sidebar__heading__inner__desc p',
				'style_name' => 'color',
			],
			'sidebar_background_color'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-sidebar, #epkb-sidebar-container-v2',
				'style_name' => 'background-color',
				'separator_above' => 'yes'
			],
			'sidebar_section_border_color'      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-sidebar,#epkb-sidebar-container-v2',
				'style_name' => 'border-color'
			],
			'sidebar_section_category_icon_color'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-sub-category li .epkb-category-level-2-3 i, #epkb-sidebar-container-v2 .epkb_sidebar_expand_category_icon',
				'style_name' => 'color',
				'separator_above' => 'yes'
			],
			'sidebar_section_category_font_color'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-sidebar-template .epkb-sub-category li .epkb-category-level-2-3 a, #epkb-sidebar-container-v2 .epkb-category-level-2-3 a, .epkb-sidebar-template .epkb-category-level-2-3__cat-name, #epkb-sidebar-container-v2 .epkb-category-level-2-3__cat-name>*',
				'style_name' => 'color'
			],

			// Features Tab
			'epkb_sidebar_heading'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => esc_html__( 'Navigation', 'echo-knowledge-base' ),
			],

			'sidebar_section_box_shadow' 		=> [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'sidebar_side_bar_height'           => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'sidebar_side_bar_height_mode'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'sidebar_scroll_bar'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'epkb_sidebar_category_heading'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => esc_html__( 'Categories', 'echo-knowledge-base' ),
			],
			'sidebar_section_head_alignment'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'reload' => 1
			],
			'sidebar_section_desc_text_on'          => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'sidebar_top_categories_collapsed'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'sidebar_expand_articles_icon'                  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'sidebar_section_divider_heading'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => esc_html__( 'Article List Divider', 'echo-knowledge-base' ),
			],
			'sidebar_section_divider'                       => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'sidebar_section_divider_color'                 => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.epkb-sidebar-template .epkb_section_heading, #epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
				'style_name' => 'border-bottom-color',
				'toggler' => 'sidebar_section_divider',
			],
			'sidebar_section_divider_thickness'             => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
				'style_name' => 'border-bottom-width',
				'postfix' => 'px',
				'toggler' => 'sidebar_section_divider',

			],

			// Advanced Tab
			'sidebar_section_padding_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'sidebar_section_head_padding_left' => [
						'style_name' => 'padding-left',
						'postfix' => 'px',
						'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
					],
					'sidebar_section_head_padding_top' => [
						'style_name' => 'padding-top',
						'postfix' => 'px',
						'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
					],
					'sidebar_section_head_padding_right' => [
						'style_name' => 'padding-right',
						'postfix' => 'px',
						'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
					],
					'sidebar_section_head_padding_bottom' => [
						'style_name' => 'padding-bottom',
						'postfix' => 'px',
						'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__heading-container',
					],
				]
			],
		];

		return [
			'epkb_sidebar_navigation_zone' => [
				'title'     =>  esc_html__( 'Navigation', 'echo-knowledge-base' ),
				'classes'   => '#epkb-sidebar-container-v2',
				'parent_zone_tab_title' => esc_html__( 'Navigation', 'echo-knowledge-base' ),
				'settings'  => $settings
			]];
	}

	/**
	 * Articles zone
	 * @return array
	 */
	private static function epkb_sidebar_articles_zone() {

		$settings = [

			// Content Tab
			'sidebar_collapse_articles_msg'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-show-all-articles .epkb-hide-text',
				'text' => '1'
			],
			'sidebar_show_all_articles_msg'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-show-all-articles .epkb-show-text span',
				'text' => '1'
			],

			// Style Tab
			'sidebar_section_body_typography'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__body-container, #epkb-sidebar-container-v2 .epkb-sidebar__cat__top-cat__body-container .eckb-article-title__text',
			],
			'sidebar_article_icon_color'                => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-sidebar-container-v2 .eckb-article-title .ep_font_icon_document',
				'style_name' => 'color'
			],
			'sidebar_article_font_color'                => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-sidebar-container-v2 .eckb-article-title',
				'style_name' => 'color'
			],
			'sidebar_article_active_font_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-sidebar-container-v2 .active .eckb-article-title',
				'style_name' => 'color',
			],
			'sidebar_article_active_background_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-sidebar-container-v2 .active',
				'style_name' => 'background-color',
			],

			// Features Tab
			'sidebar_nof_articles_displayed'    => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],

			'sidebar_article_list_margin'    => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'postfix' => 'px',
				'style_name' => is_rtl() ? 'padding-right' : 'padding-left',
				'target_selector' => '#epkb-sidebar-container-v2 .epkb-sub-sub-category, #epkb-sidebar-container-v2 .epkb-sidebar__body__main-cat, #epkb-sidebar-container-v2 .epkb-articles',
			],

			// Advanced Tab

		];

		return [
			'sidebar_articles_zone' => [
				'title'     =>  esc_html__( 'Articles', 'echo-knowledge-base' ),
				'classes'   => '.epkb-articles',
				'settings'  => $settings
			]];
	}

	/**
	 * Retrieve Editor configuration
	 * @return array
	 */
	public static function get_config() {

		$editor_config = [];

		$editor_config += self::epkb_sidebar_navigation_zone();
		$editor_config += self::epkb_sidebar_articles_zone();

		return $editor_config;
	}
}