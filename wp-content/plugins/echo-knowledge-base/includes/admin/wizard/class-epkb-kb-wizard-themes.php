<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Store Wizard theme data aka designs
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Themes {

	// if not specified use defaults (except user icons and font family)
	private static function get_main_page_themes() {

		return [

			// Setup; DESIGNS NOT USED: 11 and 15
			'theme_name' => [1 => 'simple', 2 => 'elegant', 3 => 'modern', 4 => 'image', 5 => 'informative', 6 => 'formal', 7 => 'bright', 8 => 'compact', 9 => 'office', 10 => 'organized', 11 => 'unused', 12 => 'gray',
							13 => 'clean', 14 => 'corporate', 15 => 'icon_focused', 16 => 'business', 17 => 'minimalistic', 18 => 'sharp', 19 => 'standard_classic', 20 => 'standard_drill_down', 21 => 'creative' ],

			'kb_name' => [1 => esc_html__('Simple', 'echo-knowledge-base' ), 2 => esc_html__('Elegant', 'echo-knowledge-base' ), 3 => esc_html__('Modern', 'echo-knowledge-base' ), 4 => esc_html__('Image', 'echo-knowledge-base' ),
							5 => esc_html__('Informative', 'echo-knowledge-base' ), 6 => esc_html__('Formal', 'echo-knowledge-base' ), 7 => esc_html__('Bright', 'echo-knowledge-base' ), 8 => esc_html__('Compact', 'echo-knowledge-base' ),
							9 => esc_html__('Office', 'echo-knowledge-base' ), 10 => esc_html__('Organized', 'echo-knowledge-base' ), 11 => esc_html__('Office', 'echo-knowledge-base' ), 12 => esc_html__('Gray', 'echo-knowledge-base' ),
							13 => esc_html__('Clean', 'echo-knowledge-base' ), 14 => esc_html__('Corporate', 'echo-knowledge-base' ), 15 => esc_html__('Icon Focused', 'echo-knowledge-base' ), 16 => esc_html__('Business', 'echo-knowledge-base' ),
							17 => esc_html__('Minimalistic', 'echo-knowledge-base' ), 18 => esc_html__('Sharp', 'echo-knowledge-base' ), 19 => esc_html__('Standard', 'echo-knowledge-base' ), 20 => esc_html__('Standard', 'echo-knowledge-base' ) ],

			'kb_main_page_layout' => [1 => 'Basic', 2 => 'Basic', 3 => 'Basic', 4 => 'Basic', 5 => 'Basic', 6 => 'Basic', 7 => 'Basic', 8 => 'Basic', 9 => 'Tabs', 10 => 'Tabs', 11 => 'Tabs', 12 => 'Tabs',
										13 => 'Tabs', 14 => 'Categories', 15 => 'Basic', 16 => 'Categories', 17 => 'Categories',  18 => 'Basic', 19 => 'Classic', 20 => 'Drill-Down', 21 => 'Basic'],

			// General
			'nof_columns' => [13 => 'two-col'],
			'expand_articles_icon' => [6 => 'ep_font_icon_right_arrow', 7 => 'ep_font_icon_right_arrow', 8 => 'ep_font_icon_plus_box', 10 => 'ep_font_icon_plus_box', 12 => 'ep_font_icon_right_arrow',
										13 => 'ep_font_icon_right_arrow', 14 => 'ep_font_icon_right_arrow', 15 => 'ep_font_icon_plus_box', 16 => 'ep_font_icon_folder_add', 17 => 'ep_font_icon_folder_add'],
			'article_icon_toggle' => [15 => 'off'],
			'sidebar_article_icon_toggle' => [15 => 'off'],

			// KB Core Search
			'search_background_color' => [1 => '#f7941d', 2 => '#f3e6c8', 3 => '#6aa6a2', 4 => '#B1D5E1', 5 => '#43596e', 6 => '#921612', 7 => '#ffa401', 8 => '#f4f8ff', 9 => '#1E4C5E', 10 => '#8c1515', 11 => '#43596e', 12 => '#6e6767',
											13 => '#f2f2f2', 14 => '#1e73be', 15 => '#8c1515', 16 => '#eb5a46', 17 => '#d4d4d4',
											18 => '#8224e3', 19 => '#43596E', 20 => '#F3E6C8', 21 => '#0d2c41' ],
			'search_box_input_width' => [3 => 40, 9 => '40', 14 => '40'],

			'search_box_input_height' => [1 => 'medium', 2 => 'medium', 3 => 'large', 4 => 'large', 5 => 'medium', 6 => 'large', 7 => 'medium', 8 => 'narrow', 9 => 'large', 10 => 'medium', 11 => 'medium', 12 => 'medium',
			                              13 => 'medium', 14 => 'medium', 15 => 'medium', 16 => 'medium', 17 => 'medium', 18 => 'medium', 19 => 'medium', 20 => 'medium', 21 => 'large' ],

			'search_box_padding_top' => [8=>'0'],
			'search_box_padding_bottom' => [8=>'30'],
			'search_box_margin_bottom' => [2 => '0', 3 => 40, 8=>'0', 9 => '23', 11 => '23', 16 => '0', 17 => '0'],
			'search_btn_background_color' => [1 => '#40474f', 2 => '#40474f', 3 => '#6aa6a2', 4 => '#686868', 5 => '#43596e', 6 => '#921612', 7 => '#ffa401', 8 => '#bf25ff', 9 => '#6aa6a2', 10 => '#878787', 11 => '#40474f', 12 => '#686868',
												13 => '#000000', 14 => '#757069', 15 => '#878787', 16 => '#40474f', 17 => '#6fb24c', 20 => '#1E4C5E', 21 => '#d34d04'],
			'search_btn_border_color' => [1 => '#F1F1F1', 2 => '#F1F1F1', 3 => '#FFFFFF', 4 => '#F1F1F1', 5 => '#F1F1F1', 6 => '#FFFFFF', 7 => '#f4c60c', 8 => '#bf25ff', 9 => '#6aa6a2', 10 => '#000000', 11 => '#F1F1F1', 12 => '#F1F1F1',
											13 => '#000000', 14 => '#000000', 15 => '#000000', 16 => '#F1F1F1', 17 => '#6fb24c'],
			'search_input_border_width' => [ 7 => 3, 9 => '0', 12 => '5'],
			'search_layout' => [ 3 => 'epkb-search-form-1', 8 => 'epkb-search-form-3', 9 => 'epkb-search-form-3', 11 => 'epkb-search-form-3', 12 => 'epkb-search-form-3', 13 => 'epkb-search-form-1'],
			'search_text_input_background_color' => [1 => '#FFFFFF', 2 => '#FFFFFF', 3 => '#FFFFFF', 9 => '#FFFFFF', 11 => '#FFFFFF', 14 => '#FFFFFF', 16 => '#FFFFFF', 17 => '#FFFFFF'],
			'search_text_input_border_color' => [1 => '#CCCCCC', 2 => '#CCCCCC', 3 => '#CCCCCC', 4 => '#CCCCCC', 5 => '#3b4e60', 6 => '#FFFFFF', 7 => '#FFFFFF', 8 => '#bf25ff', 9 => '#636567', 10 => '#000000', 11 => '#CCCCCC', 12 => '#000000',
												13 => '#000000', 14 => '#000000', 15 => '#000000', 16 => '#CCCCCC', 17 => '#6fb24c', 21 => '#d34d04'],
			'search_title_font_color' => [1 => '#FFFFFF', 2 => '#103244', 3 => '#FFFFFF', 4 => '#ffffff', 5 => '#ffffff', 6 => '#FFFFFF', 7 => '#FFFFFF', 8 => '#528ffe', 9 => '#FFFFFF', 10 => '#FFFFFF', 11 => '#e69e4a',
											13 => '#000000', 14 => '#FFFFFF', 15 => '#FFFFFF', 16 => '#000000', 17 => '#6fb24c', 20 => '#1E4C5E' ],

			// Category Box
			'section_border_color' => [1 => '#F7F7F7', 2 => '#f7f7f7', 3 => '#DBDBDB', 4 => '#DBDBDB', 5 => '#DBDBDB', 6 => '#DBDBDB', 7 => '#DBDBDB', 8 => '#528ffe', 9 => '#e0e0e0', 10 => '#bababa', 11 => '#f7f7f7',
										13 => '#FFFFFF', 14 => '#F7F7F7', 15 => '#bababa', 16 => '#CACACE', 17 => '#CACACE', 18 => '#8224e3', 20 => '#f3e6c8', 21 =>'#b7e5ff' ],
			'section_border_radius' => [ 2 => '4', 3 => '4', 7 => '0', 9 => '4', 11 => '4', 14 => '4', 18 => '5', 21=> '18'],
			'section_border_width' => [ 2 => 0, 3 => 0, 4 => '0', 5 => '0', 6 => '0', 7 => '0', 8 => '0', 9 => 0, 10 => '1', 11 => 0, 12 => '1', 13 => '1', 14 => '1', 15 => '1', 16 => '1', 17 => '1', 18 => '1', 20 => '1', 21 => '1' ],
			'section_box_shadow' => [ 3 => 'section_light_shadow', 4 => 'section_light_shadow', 5 => 'section_light_shadow', 7 => 'section_light_shadow', 8 => 'section_medium_shadow', 9 => 'section_light_shadow', 10 => 'section_light_shadow',
										14 => 'section_medium_shadow', 15 => 'section_light_shadow', 16 => 'section_light_shadow', 18 => 'no_shadow'],

			// Category Box Head
			'section_head_alignment' => [1 => 'left', 2 => 'left', 3 => 'center', 4 => 'center', 5 => 'center', 6 => 'left', 7 => 'center', 8 => 'center', 9 => 'left', 10 => 'center', 11 => 'left', 12 => 'center',
										13 => 'center', 14 => 'center', 15 => 'center', 16 => 'left', 17 => 'left', 18 => 'center', 21 => 'center'],
			'section_head_background_color' => [1 => '#FFFFFF', 2 => '#F9F9F9', 3 => '#FFFFFF', 7 => '#FFFFFF', 9 => '#ffffff', 10 => '#eeeeee', 11 => '#b1d5e1', 12 => '#6e6767',
												13 => '#ffffff', 14 => '#fcfcfc', 15 => '#eeeeee', 16 => '#FFFFFF', 17 => '#FFFFFF'],
			'section_head_category_icon_color' => [1 => '#f7941d', 2 => '#ca428f', 3 => '#904e95', 4 => '#904e95', 5 => '#43596e', 6 => '#e3474b', 7 => '#ffa401', 8 => '#bf25ff', 9 => '#ca428f', 10 => '#8c1515', 11 => '#ca428f', 12 => '#868686',
													13 => '#868686', 14 => '#1e73be', 15 => '#8c1515', 16 => '#eb5a46', 17 => '#4EB3C4', 18 => '#8224e3', 19 => '#000000', 20 => '#1E4C5E' ],
			'section_head_category_icon_location' => [1 => 'left', 2 => 'left', 3 => 'top', 4 => 'top', 5 => 'top', 6 => 'left', 7 => 'top', 8 => 'left', 9 => 'left', 10 => 'top', 11 => 'left', 12 => 'no_icons',
														13 => 'no_icons', 14 => 'top', 15 => 'top', 16 => 'left', 17 => 'no_icons', 18 => 'no_icons', 19 => 'top', 20 => 'top', 21 => 'top' ],
			'section_head_category_icon_size' => [1 => '50', 2 => '57', 3 => '121', 4 => '300', 5 => '50', 6 => '100', 7 => '300', 8 => '25', 9 => '87', 10 => '30', 11 => '57', 14 => '40',
				15 => '30', 16 => '30', 17 => '30', 19 => '100', 20 => '130', 21 => '300' ],
			'section_head_description_font_color' => [5 => '#444444',9 => '#b3b3b3', 11 => '#b3b3b3', 12 => '#828282', 13 => '#828282', 14 => '#b3b3b3', 17 => '#b3b3b3'],
			'section_head_font_color' => [1 => '#40474f', 2 => '#40474f', 3 => '#827a74', 4 => '#827a74', 5 => '#000000', 6 => '#e3474b', 7 => '#ffa401', 8 => '#528ffe', 9 => '#000000', 10 => '#000000', 11 => '#40474f', 12 => '#ffffff',
											13 => '#000000', 14 => '#666666', 15 => '#000000', 16 => '#000000', 17 => '#6fb24c', 18 => '#8224e3', 19 => '#000000' , 20 => '#1E4C5E' ],
			'section_head_padding_bottom' => [ 2 => '20', 3 => '20', 4 => '0', 7 => '0', 9 => '20', 10 => '10', 11 => '20', 14 => '20', 15 => '10', 16 => '20', 17 => '20', 21 =>'0' ],
			'section_head_padding_left' => [ 2 => '4', 3 => 0, 4 => '0', 9 => '4', 7 => '0', 11 => '4', 12 => '30', 13 => '30', 14 => '20', 16 => '20', 17 => '20', 21 => '0' ],
			'section_head_padding_right' => [ 2 => '4', 3 => 0, 4 => '0', 9 => '4', 7 => '0', 11 => '4', 14 => '20', 16 => '20', 17 => '20', 21 => '0' ],
			'section_head_padding_top' => [ 2 => '20', 3 => '20', 4 => '0', 7 => '0', 9 => '20', 10 => '10', 11 => '20', 14 => '20', 15 => '10', 16 => '20', 17 => '20', 21 => '0' ],
			'section_divider' => [ 3 => 'on', 4 => 'off', 7 => 'off', 10 => 'off', 15 => 'off', 21 => 'off'],
			'section_divider_color' => [1 => '#edf2f6', 2 => '#edf2f6', 3 => '#afa7a7', 5 => '#43596e', 6 => '#edf2f6', 7 => '#edf2f6', 8 => '#528ffe', 9 => '#edf2f6', 10 => '#CDCDCD', 11 => '#edf2f6', 12 => '#1e73be',
										13 => '#888888', 14 => '#1e73be', 15 => '#CDCDCD', 16 => '#FFFFFF', 17 => '#FFFFFF', 18 => '#c5c5c5'],
			'section_divider_thickness' => [ 2 => '5', 3 => '1', 4 => '0', 5 => '0', 6 => '2', 7 => '2', 8 => '2', 9 => '5', 10 => '1', 11 => '5', 12 => '2', 13 => '2', 14 => '2', 15 => '1', 16 => '1', 17 => '1', 18 => '1'],

			// Category Box Body
			'section_article_underline' => [ 2 => 'on', 3 => 'on', 9 => 'on', 11 => 'on', 14 => 'on'],
			'section_body_background_color' => [1 => '#FFFFFF', 2 => '#ffffff', 3 => '#FFFFFF', 9 => '#ffffff', 11 => '#ffffff', 12 => '#FFFFFF', 13 => '#ffffff', 14 => '#FFFFFF', 16 => '#FEFEFE', 17 => '#FEFEFE'],
			'section_body_height' => [ 2 => '120', 16 => 130, 17 => 130],
			'section_body_padding_bottom' => [ 2 => '4', 3 => 4, 9 => '4', 11 => '4', 14 => '4'],
			'section_body_padding_left' => [1 => '5', 2 => '5', 3 => '5', 4 => '5', 5 => '5', 6 => '5', 7 => '5', 8 => '5', 9 => '5', 10 => '5', 11 => '5', 12 => '5', 13 => '5', 14 => '5', 15 => '5', 16 => '5', 17 => '5'],
			'section_body_padding_right' => [1 => '5', 2 => '5', 3 => '5', 4 => '5', 5 => '5', 6 => '5', 7 => '5', 8 => '5', 9 => '5', 10 => '5', 11 => '5', 12 => '5', 13 => '5', 14 => '5', 15 => '5', 16 => '5', 17 => '5'],
			'section_body_padding_top' => [ 2 => '4', 3 => 5, 4 => '5', 5 => '5', 6 => '5', 7 => '5', 8 => '5', 9 => '4', 11 => '4', 14 => '4'],
			'section_category_font_color' => [1 => '#40474f', 2 => '#40474f', 9 => '#40474f', 10 => '#868686', 11 => '#40474f', 12 => '#000000',
												13 => '#000000', 14 => '#40474f', 15 => '#868686', 16 => '#40474f', 17 => '#40474f'],
			'section_category_icon_color' => [1 => '#f7941d', 2 => '#ca428f', 3 => '#868686', 4 => '#868686', 5 => '#868686', 6 => '#e3474b', 7 => '#dddddd', 8 => '#528ffe', 9 => '#ca428f', 10 => '#8c1515', 11 => '#ca428f', 12 => '#00b4b3',
												13 => '#00b4b3', 14 => '#1e73be', 15 => '#8c1515', 16 => '#eb5a46', 17 => '#6fb24c',  18 => '#8224e3', 19 => '#000000', 20 => '#6AA6A2'  ],

			// Drill Down
			'ml_categories_articles_back_button_bg_color' => [ 20 => '#1E4C5E' ],

			// Tabs
			'tab_down_pointer' => [ 9 => 'on', 10 => 'on', 11 => 'on', 15 => 'on', 16 => 'on', 17 => 'on'],
			'tab_nav_active_background_color' => [ 9 => '#f7f7f7', 10 => '#F1F1F1', 11 => '#43596e', 12 => '#6e6767', 13 => '#ffffff', 15 => '#F1F1F1', 16 => '#F1F1F1', 17 => '#F1F1F1'],
			'tab_nav_active_font_color' => [ 9 => '#3a3a3a', 10 => '#8c1515', 11 => '#e69e4a', 12 => '#ffffff', 13 => '#000000', 15 => '#8c1515', 16 => '#8c1515', 17 => '#8c1515'],
			'tab_nav_background_color' => [ 9 => '#ffffff', 11 => '#f7f7f7', 12 => '#f7f7f7', 13 => '#ffffff'],
			'tab_nav_border_color' => [ 9 => '#f7941d', 10 => '#000000', 11 => '#686868', 12 => '#1e73be', 13 => '#888888', 15 => '#000000', 16 => '#000000', 17 => '#000000'],
			'tab_nav_font_color' => [ 9 => '#000000', 10 => '#686868', 11 => '#e69e4a', 12 => '#686868', 13 => '#adadad', 15 => '#686868', 16 => '#686868', 17 => '#686868'],

			// Articles
			'article_font_color' => [1 => '#000000', 2 => '#000000', 3 => '#000000', 4 => '#000000', 5 => '#000000', 6 => '#000000', 7 => '#14104b', 8 => '#566e8b', 9 => '#000000', 10 => '#8c1515', 11 => '#000000', 12 => '#000000',
									13 => '#000000', 14 => '#1e73be', 15 => '#8c1515', 16 => '#666666', 17 => '#666666', 18 => '#000000', 20 => '#1E4C5E' ],
			'article_icon_color' => [1 => '#b3b3b3', 2 => '#b3b3b3', 3 => '#525252', 4 => '#525252', 5 => '#43596e', 6 => '#e3474b', 7 => '#ffa401', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 11 => '#00b4b3', 12 => '#1e73be',
									13 => '#adadad', 14 => '#000000', 15 => '#000000', 16 => '#e8a298', 17 => '#6fb24c', 18 => '#8224e3'],
			'article_list_spacing' => [1 => '6', 2 => '6', 3 => '8', 4 => '6', 5 => '6', 6 => '6', 7 => '8', 8 => '4', 9 => '6', 10 => '6', 11 => '6', 12 => '6', 13 => '6', 14 => '6', 15 => '6', 16 => '6', 17 => '6'],

			'breadcrumb_icon_separator' => [ 5 => 'ep_font_icon_right_arrow'],
			'breadcrumb_text_color' => [ 2 => '#1e73be', 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#43596e', 6 => '#eb5a46', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 11 => '#00b4b3', 12 => '#6e6767',
										13 => '#1e73be', 15 => '#000000', 16 => '#1e73be', 17 => '#1e73be'],

			'back_navigation_text_color' => [ 2 => '#1e73be', 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#ffffff', 6 => '#ffffff', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 11 => '#00b4b3', 12 => '#6e6767',
												13 => '#1e73be', 15 => '#000000', 16 => '#1e73be', 17 => '#1e73be'],
			'back_navigation_bg_color' => [ 5 => '#43596e', 6 => '#eb5a46'],
			'back_navigation_padding_top' => [ 5 => '10', 6 => '10'],
			'back_navigation_padding_right' => [ 5 => '15', 6 => '10'],
			'back_navigation_padding_bottom' => [ 5 => '10', 6 => '10'],
			'back_navigation_padding_left' => [ 5 => '15', 6 => '10'],
			'back_navigation_border_radius' => [ 6 => '1'],
			'back_navigation_border_color' => [ 5 => '#43596e', 6 => '#eb5a46' ],
			'back_navigation_border' => [ 3 => 'none', 4 => 'none'],

			'article-meta-color' => [ 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#43596e', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 15 => '#000000' ],

			'article_content_toolbar_icon_color' => [ 5 => '#ffffff', 6 => '#ffffff'],
			'article_content_toolbar_text_color' => [ 5 => '#ffffff', 6 => '#ffffff', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 15 => '#000000' ],
			'article_content_toolbar_text_hover_color' => [ 5 => '#ffffff', 6 => '#ffffff', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 15 => '#000000' ],
			'article_content_toolbar_button_background' => [ 5 => '#43596e', 6 => '#eb5a46'],
			'article_content_toolbar_button_background_hover' => [ 5 => '#bc68c9', 6 => '#ea8577' ],
			'article_content_toolbar_border_color' => [ 5 => '#43596e', 6 => '#eb5a46' ],

			'article_toc_text_color' => [ 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#43596e', 6 => '#eb5a46', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 15 => '#000000' ],
			'article_toc_active_bg_color' => [ 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#43596e', 6 => '#eb5a46', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 15 => '#000000' ],
			'article_toc_title_color' => [ 3 => '#000000', 4 => '#000000', 5 => '#000000', 6 => '#000000', 8 => '#000000', 9 => '#000000'],
			'article_toc_border_color' => [ 3 => '#b1d5e1', 4 => '#b1d5e1', 5 => '#43596e', 6 => '#eb5a46', 8 => '#566e8b', 9 => '#dd9933', 11 => '#000000'],

			'sidebar_article_icon_color' => [1 => '#b3b3b3', 2 => '#b3b3b3', 3 => '#525252', 4 => '#525252', 5 => '#43596e', 6 => '#e3474b', 7 => '#1e1e1e', 8 => '#566e8b', 9 => '#dd9933', 10 => '#000000', 11 => '#00b4b3', 12 => '#1e73be',
											13 => '#adadad', 14 => '#000000', 15 => '#000000', 16 => '#e8a298', 17 => '#6fb24c'],
			'sidebar_section_head_font_color' => [ 2 => '#1e73be', 3 => '#ffffff', 4 => '#ffffff', 5 => '#ffffff', 6 => '#ffffff', 8 => '#000000', 9 => '#dd9933', 21 => '#000000' ],
			'sidebar_section_head_background_color' => [ 3 => '#90b4c4', 4 => '#90b4c4', 5 => '#5a748d', 6 => '#f96e5a'],
			'sidebar_section_divider_color' => [ 5 => '#5a748d', 6 => '#f96e5a'],
			'sidebar_section_category_font_color' => [ 5 => '#000000'],
			'sidebar_article_active_font_color' => [ 3 => '#000000', 4 => '#000000', 5 => '#000000'],
			'sidebar_article_active_background_color' => [ 3 => '#f9f9f9', 4 => '#f9f9f9', 5 => '#f7f7f7'],
			'article_nav_sidebar_type_left' => [1 => 'eckb-nav-sidebar-v1', 2 => 'eckb-nav-sidebar-v1', 3 => 'eckb-nav-sidebar-v1', 4 => 'eckb-nav-sidebar-v1', 5 => 'eckb-nav-sidebar-v1', 6 => 'eckb-nav-sidebar-v1', 7 => 'eckb-nav-sidebar-v1',
												8 => 'eckb-nav-sidebar-v1', 9 => 'eckb-nav-sidebar-v1', 10 => 'eckb-nav-sidebar-v1', 11 => 'eckb-nav-sidebar-v1', 12 => 'eckb-nav-sidebar-v1', 13 => 'eckb-nav-sidebar-v1', 14 => 'eckb-nav-sidebar-categories',
												15 => 'eckb-nav-sidebar-v1', 16 => 'eckb-nav-sidebar-categories', 17 => 'eckb-nav-sidebar-categories'],

			// Other
			'search_title' => [ 2 => esc_html__( 'Welcome to our Support Center', 'echo-knowledge-base' ), 3 => esc_html__( 'How can we help?', 'echo-knowledge-base' ), 4 => esc_html__( 'What can we help you with?', 'echo-knowledge-base' ),
								5 => esc_html__( 'Looking for help?', 'echo-knowledge-base' ), 6 => esc_html__( 'Welcome to our Knowledge Base', 'echo-knowledge-base' ), 7 => esc_html__('What are you looking for?', 'echo-knowledge-base' ),
								8 => esc_html__( 'Self Help Documentation', 'echo-knowledge-base' ), 9 => esc_html__( 'Help Center', 'echo-knowledge-base' ), 10 => esc_html__( 'Have a Question?', 'echo-knowledge-base' ),
								11 => esc_html__( 'How can we help you today?', 'echo-knowledge-base' ), 12 => esc_html__( 'Customer Help Portal', 'echo-knowledge-base' ), 13 => esc_html__( 'Help Center', 'echo-knowledge-base' ),
								14 => esc_html__( 'Have a Question?', 'echo-knowledge-base' ),
								15 => esc_html__( 'Have a Question?', 'echo-knowledge-base' ), 16 => esc_html__( 'Knowledge Base Help Center', 'echo-knowledge-base' ), 17 => esc_html__( 'Howdy! How can we help you?', 'echo-knowledge-base' ) ],
			'ml_categories_articles_top_category_icon_bg_color'         => [ 20 => '#dedede' ],
			'ml_categories_articles_top_category_icon_bg_color_toggle'  => [ 19  => 'off', 20  => 'off' ],

			// Typography is reset for each preset
			'section_head_typography' => [],
			'categories_box_typography' => [],
			'article_search_title_typography' => [],
			'article_toc_header_typography' => [],
			'article_toc_typography' => [],
			'article_title_typography' => [],
			'article_typography' => [ 3 => ['font-size' => '14'], 10 => ['font-size' => '14'], 11 => ['font-size' => '14'], 15 => ['font-size' => '14'], 16 => ['font-size' => '12'], 17 => ['font-size' => '12']],
			'back_navigation_typography' => [],
			'breadcrumb_typography' => [],
			'search_title_typography' => [],
			'section_head_description_typography' => [],
			'section_typography' => [1 => ['font-size' => '16'], 3 => ['font-size' => '14'], 10 => ['font-size' => '12'], 11 => ['font-size' => '14'], 15 => ['font-size' => '12'], 16 => ['font-size' => '12'], 17 => ['font-size' => '12']],
			'tab_typography' => [ 9 => ['font-size' => '14'], 11 => ['font-size' => '14']],
			'search_input_typography' => [],
			'article_search_input_typography' => [],
		];
	}

	public static $sidebar_themes = array(
		'nav_sidebar_left' => [ 1 => '1', 2 => '0', 3 => '1', 4 => '0', 5 => '1', 6 => '0', 7 => '0' ],
		'article_nav_sidebar_type_left' => [ 1 => 'eckb-nav-sidebar-v1', 2 => 'eckb-nav-sidebar-none', 3 => 'eckb-nav-sidebar-categories', 4 => 'eckb-nav-sidebar-none', 5 => 'eckb-nav-sidebar-current-category', 6 => 'eckb-nav-sidebar-none', 7 => 'eckb-nav-sidebar-none' ],
		'nav_sidebar_right' => [ 1 => '0', 2 => '1', 3 => '0', 4 => '1', 5 => '0', 6 => '1', 7 => '0' ],
		'article_nav_sidebar_type_right' => [ 1 => 'eckb-nav-sidebar-none', 2 => 'eckb-nav-sidebar-v1', 3 => 'eckb-nav-sidebar-none', 4 => 'eckb-nav-sidebar-categories', 5 => 'eckb-nav-sidebar-none', 6 => 'eckb-nav-sidebar-current-category', 7 => 'eckb-nav-sidebar-none' ],
		'toc_left' => [ 1 => '0', 2 => '1', 3 => '0', 4 => '1', 5 => '0', 6 => '1', 7 => '0' ],
		'toc_right' => [ 1 => '1', 2 => '0', 3 => '1', 4 => '0', 5 => '1', 6 => '0', 7 => '0' ],
		'toc_content' => [ 1 => '0', 2 => '0', 3 => '0', 4 => '0', 5 => '0', 6 => '0', 7 => '0' ],
		'article-left-sidebar-toggle' => [ 1 => 'on', 2 => 'on', 3 => 'on', 4 => 'on', 5 => 'on', 6 => 'on', 7 => 'off' ],
		'article-right-sidebar-toggle' => [ 1 => 'on', 2 => 'on', 3 => 'on', 4 => 'on', 5 => 'on', 6 => 'on', 7 => 'off' ],
	);

	/**
	 * Return specific theme configuration + all other core and add-ons configuration so we can display preview
	 *
	 * @param $theme_name
	 * @param $kb_config
	 *
	 * @return array
	 */
	public static function get_theme( $theme_name, $kb_config ) {
		$themes = self::get_all_themes_with_kb_config( $kb_config );
		return empty( $themes[ $theme_name ] ) ? $themes['office'] : $themes[ $theme_name ];
	}

	/**
	 * Retrieve themes-specific configuration for core and add-ons
	 *
	 * @param $kb_config - KB and add-ons configuration
	 * @return array
	 */
	public static function get_all_themes_with_kb_config( $kb_config ) {

		// retrieve themes from add-ons like Elegant Layouts
		$add_on_themes = apply_filters( 'eckb_theme_wizard_get_themes_v2', array() );
		if ( empty( $add_on_themes ) || ! is_array( $add_on_themes ) ) {
			$add_on_themes = array();
		} else {
			// remove empty values (legacy) TODO remove in future
			foreach( $add_on_themes as $config_name => $theme_values ) {
				foreach ( $theme_values as $theme_id => $preset_value ) {
					if ( empty( $preset_value ) && $preset_value !== '0' && $preset_value !== 0 ) {
						unset( $add_on_themes[$config_name][$theme_id] );
					}
				}
			}
		}

		// retrieve themes from core
		$main_page_themes = self::get_main_page_themes();

		// set 'sidebar_article_font_color' to value of 'article_font_color' as they should match
		$main_page_themes['sidebar_article_font_color'] = $main_page_themes['article_font_color'];

		// merge core and add-ons theme configs
		foreach ( $add_on_themes as $config_name => $theme_values ) {
			if ( isset( $main_page_themes[$config_name] ) ) {
				$main_page_themes[$config_name] += $theme_values;
			} else {
				$main_page_themes[$config_name] = $theme_values;
			}
		}

		// get core and add-on theme names
		$theme_names = array();
		foreach ( $main_page_themes['theme_name'] as $theme_id => $theme_name ) {
			$theme_names[$theme_id] = $theme_name;
		}

		$all_themes = array();
		$all_default_configuration = self::get_all_configuration_defaults();        // article_typography -> do not change font family
		foreach ( $main_page_themes as $config_name => $theme_values ) {

			// first set defaults
			if ( ! in_array( $config_name, ['theme_name','kb_name','kb_main_page_layout'] ) ) {
				foreach ( array_keys( $theme_names ) as $theme_id ) {
					$all_themes[$theme_names[$theme_id]][$config_name] = $all_default_configuration[ $config_name ];
				}
			}

			foreach ( $theme_values as $theme_id => $preset_value ) {

				// avoid adding setting that is missing and keep user setting
				if ( ! isset( $preset_value ) && ! isset( $all_default_configuration[$config_name] ) ) {
					continue;
				}

				// if null then use default value
				if ( ! isset( $preset_value ) ) {
					$new_value = $all_default_configuration[$config_name];
				} else {
					$new_value = $preset_value;
				}

				if ( $theme_id >= 50 && ! EPKB_Utilities::is_elegant_layouts_enabled() ) {
					continue;
				}

				// set or append the value, including '0' values
				$all_themes[$theme_names[$theme_id]][$config_name] = $new_value;
			}
		}

		$all_themes = self::copy_themes( $all_themes );

		// populate KB Config with the theme
		foreach ( $all_themes as $theme_name => $theme ) {

			// add settings not part of any theme
			$kb_theme_config = array_merge( $kb_config, $theme );

			// copy Main Page search settings to Article Page
			$all_themes[$theme_name] = self::copy_search_mp_to_ap( $kb_theme_config );
		}

		return $all_themes;
	}

	// reuse some themes in multiple layouts
	private static function copy_themes( $all_themes ) {

		$all_themes['organized_basic'] = $all_themes['organized'];
		$all_themes['organized_basic']['kb_main_page_layout'] = 'Basic';
		$all_themes['gray_basic'] = $all_themes['gray'];
		$all_themes['gray_basic']['kb_main_page_layout'] = 'Basic';

		// Tabs Layout
		$all_themes['office_tabs'] = $all_themes['office'];
		$all_themes['office_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['organized_tabs'] = $all_themes['organized'];
		$all_themes['organized_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['elegant_tabs'] = $all_themes['elegant'];
		$all_themes['elegant_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['modern_tabs'] = $all_themes['modern'];
		$all_themes['modern_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['image_tabs'] = $all_themes['image'];
		$all_themes['image_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['informative_tabs'] = $all_themes['informative'];
		$all_themes['informative_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['formal_tabs'] = $all_themes['formal'];
		$all_themes['formal_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['compact_tabs'] = $all_themes['compact'];
		$all_themes['compact_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['sharp_tabs'] = $all_themes['sharp'];
		$all_themes['sharp_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['simple_tabs'] = $all_themes['simple'];
		$all_themes['simple_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['creative_tabs'] = $all_themes['creative'];
		$all_themes['creative_tabs']['kb_main_page_layout'] = 'Tabs';
		$all_themes['icon_focused_tabs'] = $all_themes['icon_focused'];
		$all_themes['icon_focused_tabs']['kb_main_page_layout'] = 'Tabs';

		// Categories Layout
		$all_themes['sharp_categories'] = $all_themes['sharp'];
		$all_themes['sharp_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['office_categories'] = $all_themes['office'];
		$all_themes['office_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['compact_categories'] = $all_themes['compact'];
		$all_themes['compact_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['simple_categories'] = $all_themes['simple'];
		$all_themes['simple_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['creative_categories'] = $all_themes['creative'];
		$all_themes['creative_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['formal_categories'] = $all_themes['formal'];
		$all_themes['formal_categories']['kb_main_page_layout'] = 'Categories';
		$all_themes['icon_focused_categories'] = $all_themes['icon_focused'];
		$all_themes['icon_focused_categories']['kb_main_page_layout'] = 'Categories';

		// Classic Layout
		$all_themes['sharp_classic'] = $all_themes['sharp'];
		$all_themes['sharp_classic']['kb_main_page_layout'] = 'Classic';

		$all_themes['organized_classic'] = $all_themes['organized'];
		$all_themes['organized_classic']['kb_main_page_layout'] = 'Classic';
		$all_themes['organized_classic']['section_head_category_icon_size'] = '80';
		$all_themes['organized_classic']['ml_categories_articles_top_category_icon_bg_color'] = '#eeeeee';

		$all_themes['simple_classic'] = $all_themes['simple'];
		$all_themes['simple_classic']['kb_main_page_layout'] = 'Classic';
		$all_themes['simple_classic']['ml_categories_articles_top_category_icon_bg_color_toggle'] = 'off';

		$all_themes['creative_classic'] = $all_themes['creative'];
		$all_themes['creative_classic']['kb_main_page_layout'] = 'Classic';
		$all_themes['creative_classic']['section_head_category_icon_size'] = '120';

		$all_themes['icon_focused_classic'] = $all_themes['icon_focused'];
		$all_themes['icon_focused_classic']['kb_main_page_layout'] = 'Classic';

		// Drill Down Layout
		$all_themes['sharp_drill_down'] = $all_themes['sharp'];
		$all_themes['sharp_drill_down']['kb_main_page_layout'] = 'Drill-Down';

		$all_themes['organized_drill_down'] = $all_themes['organized'];
		$all_themes['organized_drill_down']['kb_main_page_layout'] = 'Drill-Down';
		$all_themes['organized_drill_down']['ml_categories_articles_top_category_icon_bg_color'] = '#EEEEEE';

		$all_themes['simple_drill_down'] = $all_themes['simple'];
		$all_themes['simple_drill_down']['kb_main_page_layout'] = 'Drill-Down';
		$all_themes['simple_drill_down']['ml_categories_articles_back_button_bg_color'] = '#40474f';
		$all_themes['simple_drill_down']['ml_categories_articles_top_category_icon_bg_color_toggle'] = 'off';

		$all_themes['creative_drill_down'] = $all_themes['creative'];
		$all_themes['creative_drill_down']['kb_main_page_layout'] = 'Drill-Down';
		$all_themes['creative_drill_down']['ml_categories_articles_back_button_bg_color'] = '#0d2c41';
		$all_themes['creative_drill_down']['section_head_category_icon_size'] = '160';
		$all_themes['creative_drill_down']['section_head_category_icon_color'] = '#d34d04';
		$all_themes['creative_drill_down']['section_category_icon_color'] = '#d34d04';

		$all_themes['icon_focused_drill_down'] = $all_themes['icon_focused'];
		$all_themes['icon_focused_drill_down']['kb_main_page_layout'] = 'Drill-Down';

		return $all_themes;
	}


	public static function copy_search_mp_to_ap( $kb_config ) {

		$config_names = array( 'search_input_border_width', 'search_box_padding_top', 'search_box_padding_bottom', 'search_box_padding_left', 'search_box_padding_right', 'search_box_margin_top',
			'search_box_margin_bottom', 'search_box_input_width', 'search_box_results_style', 'search_title_html_tag', 'search_title_font_color',
			'search_background_color', 'search_text_input_background_color', 'search_text_input_border_color', 'search_btn_background_color', 'search_btn_border_color', 'search_title',
			'search_box_hint', 'search_button_name', 'search_results_msg', 'search_layout', 'search_input_typography', 'search_title_typography' );
		foreach( $config_names as $config_name ) {
			if ( isset($kb_config[$config_name]) ) {
				$kb_config['article_' . $config_name] = $kb_config[$config_name];
			}
		}

		return $kb_config;
	}

	/**
	 * Get default values for themes for both core and add-ons
	 * @return array
	 */
	private static function get_all_configuration_defaults() {

		$kb_defaults = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// add all configuration defaults from addons
		$kb_all_defaults = apply_filters( 'eckb_editor_get_default_config', $kb_defaults );
		if ( empty($kb_all_defaults) || is_wp_error($kb_all_defaults) ) {
			$kb_all_defaults = $kb_defaults;
		}

		return $kb_all_defaults;
	}
}
