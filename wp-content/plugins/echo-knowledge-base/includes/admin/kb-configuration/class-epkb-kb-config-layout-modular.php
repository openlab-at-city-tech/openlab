<?php

/**
 * Lists settings, default values and display of Modular Main Page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Modular {

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

        $config_specification = array(

	        'modular_main_page_toggle'                              => array(
		        'label'       => esc_html__( 'Modular Main Page', 'echo-knowledge-base' ),
		        'name'        => 'modular_main_page_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'on'
	        ),
	        'modular_main_page_custom_css_toggle'                   => array(
		        'label'       => esc_html__( 'Custom CSS', 'echo-knowledge-base' ),
		        'name'        => 'modular_main_page_custom_css_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'off'
	        ),

	        // Row 1
	        'ml_row_1_module'                                       => array(
		        'label'       => esc_html__( 'Row Feature', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => esc_html__( 'Search Box',   'echo-knowledge-base' ),
			        'categories_articles'   => esc_html__( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => esc_html__( 'Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => esc_html__( 'FAQs',   'echo-knowledge-base' ),
			        'resource_links'        => esc_html__( 'Resource Links',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'search'
	        ),
	        'ml_row_1_desktop_width'                                => array(
		        'label'       => esc_html__( 'Row Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 100
	        ),
	        'ml_row_1_desktop_width_units'                          => array(
		        'label'       => esc_html__( 'Row Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Row 2
	        'ml_row_2_module'                                       => array(
		        'label'       => esc_html__( 'Row Feature', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => esc_html__( 'Search Box',   'echo-knowledge-base' ),
			        'categories_articles'   => esc_html__( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => esc_html__( 'Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => esc_html__( 'FAQs',   'echo-knowledge-base' ),
			        'resource_links'        => esc_html__( 'Resource Links',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'categories_articles'
	        ),
	        'ml_row_2_desktop_width'                                => array(
		        'label'       => esc_html__( 'Row Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 1400
	        ),
	        'ml_row_2_desktop_width_units'                          => array(
		        'label'       => esc_html__( 'Row Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 3
	        'ml_row_3_module'                                       => array(
		        'label'       => esc_html__( 'Row Feature', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => esc_html__( 'Search Box',   'echo-knowledge-base' ),
			        'categories_articles'   => esc_html__( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => esc_html__( 'Featured Articles',   'echo-knowledge-base' ),
			        'faqs'                  => esc_html__( 'FAQs',   'echo-knowledge-base' ),
			        'resource_links'        => esc_html__( 'Resource Links',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'articles_list'
	        ),
	        'ml_row_3_desktop_width'                                => array(
		        'label'       => esc_html__( 'Row Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 1400
	        ),
	        'ml_row_3_desktop_width_units'                          => array(
		        'label'       => esc_html__( 'Row Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 4
	        'ml_row_4_module'                                       => array(
		        'label'       => esc_html__( 'Row Feature', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => esc_html__( 'Search Box',   'echo-knowledge-base' ),
			        'categories_articles'   => esc_html__( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => esc_html__( 'Featured Articles',   'echo-knowledge-base' ),
			        'faqs'                  => esc_html__( 'FAQs',   'echo-knowledge-base' ),
			        'resource_links'        => esc_html__( 'Resource Links',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'faqs'
	        ),
	        'ml_row_4_desktop_width'                                => array(
		        'label'       => esc_html__( 'Row Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 1400
	        ),
	        'ml_row_4_desktop_width_units'                          => array(
		        'label'       => esc_html__( 'Row Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 5
	        'ml_row_5_module'                                       => array(
		        'label'       => esc_html__( 'Row Feature', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => esc_html__( 'Search Box',   'echo-knowledge-base' ),
			        'categories_articles'   => esc_html__( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => esc_html__( 'Featured Articles',   'echo-knowledge-base' ),
			        'faqs'                  => esc_html__( 'FAQs',   'echo-knowledge-base' ),
			        'resource_links'        => esc_html__( 'Resource Links',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_row_5_desktop_width'                                => array(
		        'label'       => esc_html__( 'Row Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 1400
	        ),
	        'ml_row_5_desktop_width_units'                          => array(
		        'label'       => esc_html__( 'Row Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // MODULE: CATEGORIES AND ARTICLES
	        'ml_categories_articles_top_category_icon_bg_color_toggle'   => array(
		        'label'       => esc_html__( 'Show Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_top_category_icon_bg_color_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'on'
	        ),
	        'ml_categories_articles_top_category_icon_bg_color'          => array(
		        'label'       => esc_html__( 'Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_top_category_icon_bg_color',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#e9f6ff'
	        ),
	        'ml_categories_articles_article_bg_color'               => array(
		        'label'       => esc_html__( 'Article Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_bg_color',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#ffffff'
	        ),
	        'ml_categories_articles_back_button_bg_color'           => array(
		        'label'       => esc_html__( 'Back Button Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_back_button_bg_color',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#1e73be'
	        ),
	        'ml_categories_articles_category_title_html_tag'        => array(
		        'label'       => esc_html__( 'Category Title HTML Tag', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_category_title_html_tag',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'default'     => 'h2',
		        'options'     => array(
			        'div' => 'div',
			        'h1' => 'h1',
			        'h2' => 'h2',
			        'h3' => 'h3',
			        'h4' => 'h4',
			        'h5' => 'h5',
			        'h6' => 'h6',
			        'span' => 'span',
			        'p' => 'p',
		        ),
	        ),
	        'ml_categories_articles_collapse_categories'            => array(
		        'label'       => esc_html__( 'Collapse Categories', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_collapse_categories',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'all_expanded'  => esc_html__( 'All Expanded',   'echo-knowledge-base' ),
			        'all_collapsed' => esc_html__( 'All Collapsed',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'all_collapsed'
	        ),

			// MODULE: SIDEBAR
	        'ml_categories_articles_sidebar_toggle'                 => array(
		        'label'       => esc_html__( 'Sidebar', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'off'
	        ),
	        'ml_categories_articles_sidebar_desktop_width'          => array(
		        'label'       => esc_html__( 'Sidebar Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_desktop_width',
		        'max'         => 3000,
		        'min'         => 5,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 28,
	        ),
	        'ml_categories_articles_sidebar_location'               => array(
		        'label'       => esc_html__( 'Sidebar Location', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_location',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'left'   => esc_html__( 'Left',   'echo-knowledge-base' ),
			        'right'  => esc_html__( 'Right',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'right'
	        ),
	        'ml_categories_articles_sidebar_position_1'             => array(
		        'label'       => esc_html__( 'Sidebar Position 1', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_position_1',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'popular_articles' => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
			        'newest_articles'   => esc_html__( 'Newest Articles',   'echo-knowledge-base' ),
			        'recent_articles'   => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'popular_articles'
	        ),
	        'ml_categories_articles_sidebar_position_2'             => array(
		        'label'       => esc_html__( 'Sidebar Position 2', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_position_2',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'              => '-----',
			        'popular_articles' => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
			        'newest_articles'   => esc_html__( 'Newest Articles',   'echo-knowledge-base' ),
			        'recent_articles'   => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'newest_articles'
	        ),

	        // MODULE: SEARCH
	        'ml_search_layout'                                      => array(
		        'label'       => esc_html__( 'Design', 'echo-knowledge-base' ),
		        'name'        => 'ml_search_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => esc_html__( 'Classic Design',   'echo-knowledge-base' ),
			        'modern'    => esc_html__( 'Modern Design',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),
	        'ml_article_search_layout'                              => array(
		        'label'       => esc_html__( 'Design', 'echo-knowledge-base' ),
		        'name'        => 'ml_article_search_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => esc_html__( 'Classic Design',   'echo-knowledge-base' ),
			        'modern'    => esc_html__( 'Modern Design',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),

	        // MODULE: ARTICLE LIST
	        'ml_articles_list_nof_articles_displayed'               => array(
		        'label'       => esc_html__( 'Number of Articles Listed', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_nof_articles_displayed',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'default'     => 5
	        ),
	        'ml_articles_list_column_1'                             => array(
		        'label'   => esc_html__( 'List of Articles 1', 'echo-knowledge-base' ),
		        'name'    => 'ml_articles_list_column_1',
		        'type'    => EPKB_Input_Filter::SELECTION,
		        'options' => array(
			        'none'             => '-----',
			        'popular_articles' => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
			        'newest_articles'  => esc_html__( 'New Articles', 'echo-knowledge-base' ),
			        'recent_articles'  => esc_html__( 'Recently Updated Articles', 'echo-knowledge-base' )
		        ),
		        'default' => 'popular_articles'
	        ),
	        'ml_articles_list_column_2'                             => array(
		        'label'   => esc_html__( 'List of Articles 2', 'echo-knowledge-base' ),
		        'name'    => 'ml_articles_list_column_2',
		        'type'    => EPKB_Input_Filter::SELECTION,
		        'options' => array(
			        'none'             => '-----',
			        'popular_articles' => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
			        'newest_articles'  => esc_html__( 'New Articles', 'echo-knowledge-base' ),
			        'recent_articles'  => esc_html__( 'Recently Updated Articles', 'echo-knowledge-base' )
		        ),
		        'default' => 'newest_articles'
	        ),
	        'ml_articles_list_column_3'                             => array(
		        'label'   => esc_html__( 'List of Articles 3', 'echo-knowledge-base' ),
		        'name'    => 'ml_articles_list_column_3',
		        'type'    => EPKB_Input_Filter::SELECTION,
		        'options' => array(
			        'none'             => '-----',
			        'popular_articles' => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
			        'newest_articles'  => esc_html__( 'New Articles', 'echo-knowledge-base' ),
			        'recent_articles'  => esc_html__( 'Recently Updated Articles', 'echo-knowledge-base' )
		        ),
		        'default' => 'recent_articles'
	        ),
	        'ml_articles_list_title_text'                           => array(
		        'label'       => esc_html__( 'Title', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_title_text',
		        'max'         => '150',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Featured Articles', 'echo-knowledge-base' )
	        ),
	        'ml_articles_list_title_location'                       => array(
		        'label'       => esc_html__( 'Title Location', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_title_location',
		        'max'         => '150',
		        'min'         => '0',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'      => esc_html__( 'None', 'echo-knowledge-base' ),
			        'left'      => esc_html__( 'Left', 'echo-knowledge-base' ),
			        'center'    => esc_html__( 'Center', 'echo-knowledge-base' ),
			        'right'     => esc_html__( 'Right', 'echo-knowledge-base' ),
		        ),
		        'default'     => 'center',
	        ),
	        'ml_articles_list_popular_articles_msg'                 => array(
		        'label'       => esc_html__( 'Popular Articles Title', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_popular_articles_msg',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Popular Articles', 'echo-knowledge-base' )
	        ),
	        'ml_articles_list_newest_articles_msg'                  => array(
		        'label'       => esc_html__( 'Newest Articles Title', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_newest_articles_msg',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Newest Articles', 'echo-knowledge-base' )
	        ),
	        'ml_articles_list_recent_articles_msg'                  => array(
		        'label'       => esc_html__( 'Recently Updated Articles Title', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_recent_articles_msg',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Recently Updated Articles', 'echo-knowledge-base' )
	        ),

	        // MODULE: FAQs
	        'ml_faqs_content_mode'                                  => array(
		        'label'       => esc_html__( 'Content Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_content_mode',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'content'    => esc_html__( 'Content', 'echo-knowledge-base' ),
			        'excerpt'    => esc_html__( 'Excerpt', 'echo-knowledge-base' )
		        ),
		        'default'     => 'content'
	        ),
	        'ml_faqs_custom_css_class'                              => array(
		        'label'       => esc_html__( 'Custom CSS class', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_custom_css_class',
		        'max'         => '200',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => ''
	        ),
	        'ml_faqs_title_text'                                    => array(
		        'label'       => esc_html__( 'Title', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_title_text',
		        'max'         => '150',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Frequently Asked Questions', 'echo-knowledge-base' )
	        ),
	        'ml_faqs_title_location'                                => array(
		        'label'       => esc_html__( 'Title Location', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_title_location',
		        'max'         => '150',
		        'min'         => '0',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'      => esc_html__( 'None', 'echo-knowledge-base' ),
			        'left'      => esc_html__( 'Left', 'echo-knowledge-base' ),
			        'center'    => esc_html__( 'Center', 'echo-knowledge-base' ),
			        'right'     => esc_html__( 'Right', 'echo-knowledge-base' ),
		        ),
		        'default'     => 'center',
	        ),

			// MODULE: CATEGORIES AND ARTICLES
	        'ml_categories_articles_back_button_text'               => array(
		        'label'       => esc_html__( 'Back Button Text', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_back_button_text',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'Back', 'echo-knowledge-base' )
	        ),
	        'ml_categories_articles_article_text'                   => array(
		        'label'       => esc_html__( 'Article', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_text',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => esc_html__( 'ARTICLE', 'echo-knowledge-base' )
	        ),
	        'ml_categories_articles_articles_text'                  => array(
		        'label'       => esc_html__( 'Articles', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_articles_text',
		        'max'         => '150',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'ARTICLES', 'echo-knowledge-base' )
	        ),
        );

		return $config_specification;
	}
}
