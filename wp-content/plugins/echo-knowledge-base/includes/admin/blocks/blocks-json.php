<?php

function epkb_get_block_attributes( $block_name ) {
	$blocks = [
		'search' => [
			'kb_id' => [
				'type' => 'number'
			],
			'ml_search_layout' => [
				'type' => 'string'
			],
			'search_title_html_tag' => [
				'type' => 'string'
			],
			'search_title' => [
				'type' => 'string'
			],
			'search_title_font_color' => [
				'type' => 'string'
			],
			'search_title_typography_controls' => [
				'type' => 'object'
			],
			'search_box_hint' => [
				'type' => 'string'
			],
			'search_button_typography_controls' => [
				'type' => 'object'
			],
			'search_button_name' => [
				'type' => 'string'
			],
			'search_btn_background_color' => [
				'type' => 'string'
			],
			'no_results_found' => [
				'type' => 'string'
			],
			'min_search_word_size_msg' => [
				'type' => 'string'
			],
			'search_background_color' => [
				'type' => 'string'
			],
			'search_text_input_background_color' => [
				'type' => 'string'
			],
			'search_text_input_border_color' => [
				'type' => 'string'
			],
			'search_box_padding_top' => [
				'type' => 'number'
			],
			'search_box_padding_bottom' => [
				'type' => 'number'
			],
			'search_box_padding_left' => [
				'type' => 'number'
			],
			'search_box_padding_right' => [
				'type' => 'number'
			],
			'search_box_margin_top' => [
				'type' => 'number'
			],
			'search_box_margin_bottom' => [
				'type' => 'number'
			],
			'search_input_typography_controls' => [
				'type' => 'object'
			],
			'search_box_input_width' => [
				'type' => 'number'
			],
			'search_box_input_height' => [
				'type' => 'string'
			],
			'search_results_typography_controls' => [
				'type' => 'object'
			],
			'search_result_mode' => [
				'type' => 'string'
			],
			'search_box_results_style' => [
				'type' => 'string'
			],
			'search_input_border_width' => [
				'type' => 'number'
			],
			'search_results_msg' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'basic-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'nof_columns' => [
				'type' => 'string'
			],
			'section_desc_text_on' => [
				'type' => 'string'
			],
			'section_hyperlink_on' => [
				'type' => 'string'
			],
			'section_box_height_mode' => [
				'type' => 'string'
			],
			'section_box_shadow' => [
				'type' => 'string'
			],
			'section_divider' => [
				'type' => 'string'
			],
			'section_divider_thickness' => [
				'type' => 'number'
			],
			'section_divider_color' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'section_head_typography_controls' => [
				'type' => 'object'
			],
			'section_head_category_icon_location' => [
				'type' => 'string'
			],
			'section_head_category_icon_size' => [
				'type' => 'number'
			],
			'section_head_alignment' => [
				'type' => 'string'
			],
			'section_head_padding_top' => [
				'type' => 'number'
			],
			'section_head_padding_bottom' => [
				'type' => 'number'
			],
			'section_head_padding_left' => [
				'type' => 'number'
			],
			'section_head_padding_right' => [
				'type' => 'number'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_head_background_color' => [
				'type' => 'string'
			],
			'section_head_description_typography_controls' => [
				'type' => 'object'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_body_height' => [
				'type' => 'number'
			],
			'section_body_padding_top' => [
				'type' => 'number'
			],
			'section_body_padding_bottom' => [
				'type' => 'number'
			],
			'section_body_padding_left' => [
				'type' => 'number'
			],
			'section_body_padding_right' => [
				'type' => 'number'
			],
			'section_category_icon_color' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'article_collapse_message_typography_controls' => [
				'type' => 'object'
			],
			'nof_articles_displayed' => [
				'type' => 'number'
			],
			'expand_articles_icon' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'collapse_articles_msg' => [
				'type' => 'string'
			],
			'show_all_articles_msg' => [
				'type' => 'string'
			],
			'article_list_margin' => [
				'type' => 'number'
			],
			'sub_article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'tabs-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'nof_columns' => [
				'type' => 'string'
			],
			'section_desc_text_on' => [
				'type' => 'string'
			],
			'section_hyperlink_on' => [
				'type' => 'string'
			],
			'section_box_height_mode' => [
				'type' => 'string'
			],
			'section_box_shadow' => [
				'type' => 'string'
			],
			'section_divider' => [
				'type' => 'string'
			],
			'section_divider_thickness' => [
				'type' => 'number'
			],
			'section_divider_color' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'section_head_typography_controls' => [
				'type' => 'object'
			],
			'section_head_category_icon_location' => [
				'type' => 'string'
			],
			'section_head_category_icon_size' => [
				'type' => 'number'
			],
			'section_head_alignment' => [
				'type' => 'string'
			],
			'section_head_padding_top' => [
				'type' => 'number'
			],
			'section_head_padding_bottom' => [
				'type' => 'number'
			],
			'section_head_padding_left' => [
				'type' => 'number'
			],
			'section_head_padding_right' => [
				'type' => 'number'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_head_background_color' => [
				'type' => 'string'
			],
			'section_head_description_typography_controls' => [
				'type' => 'object'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_body_height' => [
				'type' => 'number'
			],
			'section_body_padding_top' => [
				'type' => 'number'
			],
			'section_body_padding_bottom' => [
				'type' => 'number'
			],
			'section_body_padding_left' => [
				'type' => 'number'
			],
			'section_body_padding_right' => [
				'type' => 'number'
			],
			'section_category_icon_color' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'article_collapse_message_typography_controls' => [
				'type' => 'object'
			],
			'nof_articles_displayed' => [
				'type' => 'number'
			],
			'expand_articles_icon' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'collapse_articles_msg' => [
				'type' => 'string'
			],
			'show_all_articles_msg' => [
				'type' => 'string'
			],
			'article_list_margin' => [
				'type' => 'number'
			],
			'sub_article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'choose_main_topic' => [
				'type' => 'string'
			],
			'tab_down_pointer' => [
				'type' => 'string'
			],
			'tab_nav_active_font_color' => [
				'type' => 'string'
			],
			'tab_nav_active_background_color' => [
				'type' => 'string'
			],
			'tab_nav_font_color' => [
				'type' => 'string'
			],
			'tab_nav_background_color' => [
				'type' => 'string'
			],
			'tab_nav_border_color' => [
				'type' => 'string'
			],
			'tab_nav_name_typography_controls' => [
				'type' => 'object'
			],
			'tab_nav_desc_typography_controls' => [
				'type' => 'object'
			],
			'tab_nav_overflow_mode' => [
				'type' => 'string'
			],
			'tab_nav_max_tabs_per_row' => [
				'type' => 'number'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'categories-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'nof_columns' => [
				'type' => 'string'
			],
			'section_desc_text_on' => [
				'type' => 'string'
			],
			'section_hyperlink_on' => [
				'type' => 'string'
			],
			'section_box_height_mode' => [
				'type' => 'string'
			],
			'section_box_shadow' => [
				'type' => 'string'
			],
			'section_divider' => [
				'type' => 'string'
			],
			'section_divider_thickness' => [
				'type' => 'number'
			],
			'section_divider_color' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'section_head_typography_controls' => [
				'type' => 'object'
			],
			'section_head_category_icon_location' => [
				'type' => 'string'
			],
			'section_head_category_icon_size' => [
				'type' => 'number'
			],
			'section_head_alignment' => [
				'type' => 'string'
			],
			'section_head_padding_top' => [
				'type' => 'number'
			],
			'section_head_padding_bottom' => [
				'type' => 'number'
			],
			'section_head_padding_left' => [
				'type' => 'number'
			],
			'section_head_padding_right' => [
				'type' => 'number'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_head_background_color' => [
				'type' => 'string'
			],
			'section_head_description_typography_controls' => [
				'type' => 'object'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_body_height' => [
				'type' => 'number'
			],
			'section_body_padding_top' => [
				'type' => 'number'
			],
			'section_body_padding_bottom' => [
				'type' => 'number'
			],
			'section_body_padding_left' => [
				'type' => 'number'
			],
			'section_body_padding_right' => [
				'type' => 'number'
			],
			'section_category_icon_color' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'article_collapse_message_typography_controls' => [
				'type' => 'object'
			],
			'nof_articles_displayed' => [
				'type' => 'number'
			],
			'expand_articles_icon' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'collapse_articles_msg' => [
				'type' => 'string'
			],
			'show_all_articles_msg' => [
				'type' => 'string'
			],
			'article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'classic-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'nof_columns' => [
				'type' => 'string'
			],
			'section_desc_text_on' => [
				'type' => 'string'
			],
			'section_box_height_mode' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'section_head_typography_controls' => [
				'type' => 'object'
			],
			'section_head_category_icon_location' => [
				'type' => 'string'
			],
			'section_head_category_icon_size' => [
				'type' => 'number'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_head_description_typography_controls' => [
				'type' => 'object'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_box_expand_hover_color' => [
				'type' => 'string'
			],
			'section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_body_height' => [
				'type' => 'number'
			],
			'section_category_icon_color' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'article_collapse_message_typography_controls' => [
				'type' => 'object'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'article_list_margin' => [
				'type' => 'number'
			],
			'sub_article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'ml_categories_articles_top_category_icon_bg_color_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_top_category_icon_bg_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_category_title_html_tag' => [
				'type' => 'string'
			],
			'ml_categories_articles_collapse_categories' => [
				'type' => 'string'
			],
			'ml_categories_articles_article_text' => [
				'type' => 'string'
			],
			'ml_categories_articles_articles_text' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'custom_css_class' => [
				'type' => 'string'
			],
			'sub_categories_design' => array(
				'type' => 'string'
			),
		],
		'drill-down-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'nof_columns' => [
				'type' => 'string'
			],
			'section_desc_text_on' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'section_head_typography_controls' => [
				'type' => 'object'
			],
			'section_head_category_icon_location' => [
				'type' => 'string'
			],
			'section_head_category_icon_size' => [
				'type' => 'number'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_head_description_typography_controls' => [
				'type' => 'object'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_category_icon_color' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'ml_categories_articles_top_category_icon_bg_color_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_top_category_icon_bg_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_article_bg_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_back_button_bg_color' => [
				'type' => 'string'
			],
			'ml_categories_articles_category_title_html_tag' => [
				'type' => 'string'
			],
			'ml_categories_articles_back_button_text' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'featured-articles' => [
			'kb_id' => [
				'type' => 'number'
			],
			'ml_articles_list_title_text' => [
				'type' => 'string'
			],
			'ml_articles_list_title_location' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_column_1' => [
				'type' => 'string'
			],
			'ml_articles_list_column_2' => [
				'type' => 'string'
			],
			'ml_articles_list_column_3' => [
				'type' => 'string'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'section_box_shadow' => [
				'type' => 'string'
			],
			'ml_articles_list_title_color' => [
				'type' => 'string'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'articles_list_title_typography_controls' => [
				'type' => 'object'
			],
			'articles_list_head_typography_controls' => [
				'type' => 'object'
			],
			'articles_list_article_typography_controls' => [
				'type' => 'object'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'faqs' => [
			'kb_id' => [
				'type' => 'number'
			],
			'ml_faqs_title_location' => [
				'type' => 'string'
			],
			'ml_faqs_title_text' => [
				'type' => 'string'
			],
			'faq_empty_msg' => [
				'type' => 'string'
			],
			'faq_nof_columns' => [
				'type' => 'number'
			],
			'faq_compact_mode' => [
				'type' => 'string'
			],
			'faq_open_mode' => [
				'type' => 'string'
			],
			'faq_icon_type' => [
				'type' => 'string'
			],
			'faq_icon_location' => [
				'type' => 'string'
			],
			'faq_icon_color' => [
				'type' => 'string'
			],
			'faq_border_mode' => [
				'type' => 'string'
			],
			'faq_border_style' => [
				'type' => 'string'
			],
			'faq_border_color' => [
				'type' => 'string'
			],
			'faq_question_text_color' => [
				'type' => 'string'
			],
			'faq_answer_text_color' => [
				'type' => 'string'
			],
			'faq_question_background_color' => [
				'type' => 'string'
			],
			'faq_answer_background_color' => [
				'type' => 'string'
			],
			'faq_question_space_between' => [
				'type' => 'string'
			],
			'faq_title_typography_controls' => [
				'type' => 'object'
			],
			'faq_group_title_typography_controls' => [
				'type' => 'object'
			],
			'faq_question_typography_controls' => [
				'type' => 'object'
			],
			'faq_answer_typography_controls' => [
				'type' => 'object'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'faq_group_ids' => [
				'type' => 'array'
			],
			'faq_groups_link' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			],
		],
		'grid-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			'category_empty_msg' => [
				'type' => 'string'
			],
			'grid_nof_columns' => [
				'type' => 'string'
			],
			'grid_section_desc_text_on' => [
				'type' => 'string'
			],
			'grid_category_link_text' => [
				'type' => 'string'
			],
			'section_hyperlink_text_on' => [
				'type' => 'string'
			],
			'grid_article_count_text' => [
				'type' => 'string'
			],
			'grid_article_count_plural_text' => [
				'type' => 'string'
			],
			'grid_section_box_height_mode' => [
				'type' => 'string'
			],
			'grid_section_body_height' => [
				'type' => 'number'
			],
			'section_border_radius' => [
				'type' => 'number'
			],
			'section_border_width' => [
				'type' => 'number'
			],
			'section_border_color' => [
				'type' => 'string'
			],
			'grid_section_box_shadow' => [
				'type' => 'string'
			],
			'grid_section_box_hover' => [
				'type' => 'string'
			],
			'grid_section_body_alignment' => [
				'type' => 'string'
			],
			'grid_section_body_padding_top' => [
				'type' => 'number'
			],
			'grid_section_body_padding_bottom' => [
				'type' => 'number'
			],
			'grid_section_body_padding_left' => [
				'type' => 'number'
			],
			'grid_section_body_padding_right' => [
				'type' => 'number'
			],
			'grid_section_head_alignment' => [
				'type' => 'string'
			],
			'grid_section_head_padding_top' => [
				'type' => 'number'
			],
			'grid_section_head_padding_bottom' => [
				'type' => 'number'
			],
			'grid_section_head_padding_left' => [
				'type' => 'number'
			],
			'grid_section_head_padding_right' => [
				'type' => 'number'
			],
			'grid_category_icon_location' => [
				'type' => 'string'
			],
			'grid_section_icon_size' => [
				'type' => 'number'
			],
			'grid_section_icon_padding_top' => [
				'type' => 'number'
			],
			'grid_section_icon_padding_bottom' => [
				'type' => 'number'
			],
			'grid_section_icon_padding_left' => [
				'type' => 'number'
			],
			'grid_section_icon_padding_right' => [
				'type' => 'number'
			],
			'section_head_background_color' => [
				'type' => 'string'
			],
			'section_head_category_icon_color' => [
				'type' => 'string'
			],
			'section_head_description_font_color' => [
				'type' => 'string'
			],
			'grid_section_cat_name_padding_top' => [
				'type' => 'number'
			],
			'grid_section_cat_name_padding_bottom' => [
				'type' => 'number'
			],
			'grid_section_cat_name_padding_left' => [
				'type' => 'number'
			],
			'grid_section_cat_name_padding_right' => [
				'type' => 'number'
			],
			'grid_section_head_typography_controls' => [
				'type' => 'object'
			],
			'grid_section_desc_padding_top' => [
				'type' => 'number'
			],
			'grid_section_desc_padding_bottom' => [
				'type' => 'number'
			],
			'grid_section_desc_padding_left' => [
				'type' => 'number'
			],
			'grid_section_desc_padding_right' => [
				'type' => 'number'
			],
			'grid_section_description_typography_controls' => [
				'type' => 'object'
			],
			'grid_section_divider' => [
				'type' => 'string'
			],
			'grid_section_divider_thickness' => [
				'type' => 'number'
			],
			'section_divider_color' => [
				'type' => 'string'
			],
			'grid_section_article_count' => [
				'type' => 'string'
			],
			'grid_section_article_count_typography_controls' => [
				'type' => 'object'
			],
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'section_category_font_color' => [
				'type' => 'string'
			],
			'section_head_font_color' => [
				'type' => 'string'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'sidebar_article_icon_toggle' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_font_color' => [
				'type' => 'string'
			],
			'article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'sidebar-layout' => [
			'kb_id' => [
				'type' => 'number'
			],
			'kb_block_template_toggle' => [
				'type' => 'string'
			],
			'templates_for_kb' => [
				'type' => 'string'
			],
			/*'sidebar_top_categories_collapsed' => [
				'type' => 'string'
			],
			'sidebar_nof_articles_displayed' => [
				'type' => 'number'
			],
			'sidebar_main_page_intro_text' => [
				'type' => 'string'
			],
			'sidebar_section_desc_text_on' => [
				'type' => 'string'
			],
			'sidebar_article_underline' => [
				'type' => 'string'
			],
			'sidebar_collapse_articles_msg' => [
				'type' => 'string'
			],
			'sidebar_show_all_articles_msg' => [
				'type' => 'string'
			],
			'sidebar_category_empty_msg' => [
				'type' => 'string'
			],
			'article-left-sidebar-toggle' => [
				'type' => 'string'
			],
			'article_nav_sidebar_type_left' => [
				'type' => 'string'
			],
			'nav_sidebar_left' => [
				'type' => 'string'
			],
			'kb_sidebar_left' => [
				'type' => 'string'
			],
			'toc_left' => [
				'type' => 'string'
			],
			'article-right-sidebar-toggle' => [
				'type' => 'string'
			],
			'article_nav_sidebar_type_right' => [
				'type' => 'string'
			],
			'nav_sidebar_right' => [
				'type' => 'string'
			],
			'kb_sidebar_right' => [
				'type' => 'string'
			],
			'toc_right' => [
				'type' => 'string'
			],
			'sidebar_side_bar_height_mode' => [
				'type' => 'string'
			],
			'sidebar_side_bar_height' => [
				'type' => 'number'
			],
			'sidebar_scroll_bar' => [
				'type' => 'string'
			],
			'sidebar_section_head_font_color' => [
				'type' => 'string'
			],
			'sidebar_section_head_background_color' => [
				'type' => 'string'
			],
			'sidebar_section_head_description_font_color' => [
				'type' => 'string'
			],
			'sidebar_section_head_alignment' => [
				'type' => 'string'
			],
			'sidebar_section_head_padding_top' => [
				'type' => 'number'
			],
			'sidebar_section_head_padding_bottom' => [
				'type' => 'number'
			],
			'sidebar_section_head_padding_left' => [
				'type' => 'number'
			],
			'sidebar_section_head_padding_right' => [
				'type' => 'number'
			],
			'sidebar_section_category_icon_color' => [
				'type' => 'string'
			],
			'sidebar_section_category_font_color' => [
				'type' => 'string'
			],*/
			'sidebar_section_category_typography_controls' => [
				'type' => 'object'
			],
			/*'sidebar_section_category_desc_typography_controls' => [
				'type' => 'object'
			],
			'sidebar_section_subcategory_typography_controls' => [
				'type' => 'object'
			],
			'sidebar_section_border_radius' => [
				'type' => 'number'
			],
			'sidebar_section_border_width' => [
				'type' => 'number'
			],
			'sidebar_section_border_color' => [
				'type' => 'string'
			],
			'category_box_container_background_color' => [
				'type' => 'string'
			],
			'category_box_count_background_color' => [
				'type' => 'string'
			],
			'category_box_count_text_color' => [
				'type' => 'string'
			],
			'category_box_count_border_color' => [
				'type' => 'string'
			],
			'sidebar_section_box_shadow' => [
				'type' => 'string'
			],
			'sidebar_section_divider' => [
				'type' => 'string'
			],
			'sidebar_section_divider_thickness' => [
				'type' => 'number'
			],
			'sidebar_section_divider_color' => [
				'type' => 'string'
			],
			'sidebar_expand_articles_icon' => [
				'type' => 'string'
			],
			'sidebar_show_articles_before_categories' => [
				'type' => 'string'
			],
			'sidebar_section_body_padding_top' => [
				'type' => 'number'
			],
			'sidebar_section_body_padding_bottom' => [
				'type' => 'number'
			],
			'sidebar_section_body_padding_left' => [
				'type' => 'number'
			],
			'sidebar_section_body_padding_right' => [
				'type' => 'number'
			],*/
			'sidebar_section_body_typography_controls' => [
				'type' => 'object'
			],
			'article-content-background-color-v2' => [
				'type' => 'string'
			],
			'elay_article_icon' => [
				'type' => 'string'
			],
			/*'sidebar_article_icon_toggle' => [
				'type' => 'string'
			],
			'article_icon_color' => [
				'type' => 'string'
			],
			'article_font_color' => [
				'type' => 'string'
			],*/
			'sidebar_article_list_margin' => [
				'type' => 'number'
			],
			'article_list_spacing' => [
				'type' => 'number'
			],
			/*'sidebar_background_color' => [
				'type' => 'string'
			],
			'sidebar_article_font_color' => [
				'type' => 'string'
			],
			'sidebar_article_icon_color' => [
				'type' => 'string'
			],*/
			'ml_categories_articles_sidebar_toggle' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_location' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_desktop_width' => [
				'type' => 'number'
			],
			'ml_categories_articles_sidebar_position_1' => [
				'type' => 'string'
			],
			'ml_categories_articles_sidebar_position_2' => [
				'type' => 'string'
			],
			'ml_articles_list_nof_articles_displayed' => [
				'type' => 'number'
			],
			'ml_articles_list_popular_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_newest_articles_msg' => [
				'type' => 'string'
			],
			'ml_articles_list_recent_articles_msg' => [
				'type' => 'string'
			],
			'article_typography_controls' => [
				'type' => 'object'
			],
			'section_body_background_color' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'theme_presets' => [
				'type' => 'string'
			],
			'theme_name' => [
				'type' => 'string'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		],
		'advanced-search' => [
			'kb_id' => [
				'type' => 'number'
			],
			'search_multiple_kbs_toggle' => [
				'type' => 'string'
			],
			'search_multiple_kbs_list' => [
				'type' => 'array'
			],
			'advanced_search_context_toggle' => [
				'type' => 'string'
			],
			'advanced_search_context_characters' => [
				'type' => 'number'
			],
			'advanced_search_context_highlight_font_color' => [
				'type' => 'string'
			],
			'advanced_search_text_highlight_enabled' => [
				'type' => 'string'
			],
			'advanced_search_mp_auto_complete_wait' => [
				'type' => 'number'
			],
			// 'advanced_search_mp_visibility' => [ not for Main Page
			// 	'type' => 'string'
			// ],
			'advanced_search_mp_title_font_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_font_shadow_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_tag' => [
				'type' => 'string'
			],
			'search_box_hint' => [
				'type' => 'string'
			],
			'advanced_search_mp_btn_border_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_link_font_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_gradient_from_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_gradient_to_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_typography_controls' => [
				'type' => 'object'
			],
			'advanced_search_mp_title' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_by_filter' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_clear_results' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_title_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_title' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_input_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_input' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_title_font_shadow_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_box_padding_top' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_padding_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_padding_left' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_padding_right' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_margin_top' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_margin_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_font_width' => [
				'type' => 'number'
			],
			'advanced_search_mp_title_padding_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_title_text_shadow_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_title_text_shadow_x_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_title_text_shadow_y_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_title_text_shadow_blur' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_title_typography_controls' => [
				'type' => 'object'
			],
			'advanced_search_mp_description_below_title_padding_top' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_title_padding_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_title_text_shadow_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_description_below_title_text_shadow_x_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_title_text_shadow_y_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_title_text_shadow_blur' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_border_width' => [
				'type' => 'number'
			],
			'advanced_search_mp_box_input_width' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_radius' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_typography_controls' => [
				'type' => 'object'
			],
			'advanced_search_mp_input_box_shadow_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_input_box_shadow_x_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_shadow_y_offset' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_shadow_blur' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_shadow_spread' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_padding_top' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_padding_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_padding_left' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_padding_right' => [
				'type' => 'number'
			],
			'advanced_search_mp_input_box_search_icon_placement' => [
				'type' => 'string'
			],
			'advanced_search_mp_input_box_loading_icon_placement' => [
				'type' => 'string'
			],
			'advanced_search_mp_text_input_background_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_text_input_border_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_filter_category_level' => [
				'type' => 'string'
			],
			'advanced_search_mp_filter_toggle' => [
				'type' => 'string'
			],
			'advanced_search_mp_filter_indicator_text' => [
				'type' => 'string'
			],
			'advanced_search_mp_filter_dropdown_width' => [
				'type' => 'number'
			],
			'advanced_search_mp_filter_box_font_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_filter_box_background_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_show_top_category' => [
				'type' => 'string'
			],
			'advanced_search_context_enabled' => [
				'type' => 'string'
			],
			'advanced_search_context_characters' => [
				'type' => 'number'
			],
			'advanced_search_context_highlight_font_color' => [
				'type' => 'string'
			],
			

			'advanced_search_mp_results_typography_controls' => [
				'type' => 'object'
			],
			'advanced_search_mp_results_msg' => [
				'type' => 'string'
			],
			'advanced_search_mp_no_results_found' => [
				'type' => 'string'
			],
			'advanced_search_mp_more_results_found' => [
				'type' => 'string'
			],
			'advanced_search_mp_search_result_category_color' => [
				'type' => 'string'
			],
			'advanced_search_mp_results_list_size' => [
				'type' => 'number'
			],
			'advanced_search_mp_results_page_size' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_input_typography_controls' => [
				'type' => 'object'
			],
			'advanced_search_mp_description_below_input_padding_top' => [
				'type' => 'number'
			],
			'advanced_search_mp_description_below_input_padding_bottom' => [
				'type' => 'number'
			],
			'advanced_search_mp_background_image_url' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_image_position_x' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_image_position_y' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_pattern_image_url' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_pattern_image_position_x' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_pattern_image_position_y' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_pattern_image_opacity' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_gradient_degree' => [
				'type' => 'number'
			],
			'advanced_search_mp_background_gradient_opacity' => [
				'type' => 'string'
			],
			'advanced_search_mp_background_gradient_toggle' => [
				'type' => 'string'
			],
			'advanced_search_results_meta_created_on_toggle' => [
				'type' => 'string'
			],
			'advanced_search_results_meta_author_toggle' => [
				'type' => 'string'
			],
			'advanced_search_results_meta_categories_toggle' => [
				'type' => 'string'
			],
			'block_full_width_toggle' => [
				'type' => 'string'
			],
			'block_max_width' => [
				'type' => 'number'
			],
			'custom_css_class' => [
				'type' => 'string'
			]
		]
	];

	return $blocks[$block_name];
}
