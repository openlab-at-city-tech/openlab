<?php

/**
 * Configuration for the front end editor
 */
class EPKB_Editor_Main_Page_Config extends EPKB_Editor_KB_Base_Config {

	/** SEE DOCUMENTATION IN THE BASE CLASS **/
	protected $page_type = 'main-page';
	
	/**
	 * Content zone - The whole page (applies only to KB Template)
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function page_zone( $kb_config ) {
		
		$theme_preset_options = [];
		$theme_preset_options['current'] = esc_html__( 'Current', 'echo-knowledge-base' );
		foreach ( EPKB_KB_Wizard_Themes::get_all_themes_with_kb_config( $kb_config ) as $theme_slug => $theme_data ) {
			$theme_preset_options[$theme_data['kb_main_page_layout']][$theme_slug] = $theme_data['kb_name'];
		}

		$settings = [

			// Features Tab
			'template_main_page_display_title' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			
			'theme_presets' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'default' => 'current',
				'label' => esc_html__( 'Pre-made KB Designs', 'echo-knowledge-base' ),
				'description' => esc_html__( 'These designs are predefined styles and colors to help you quickly set up your initial KB look. Please note that these designs use sample icons and images. ' .
				                     'The actual frontend page will show either your saved icons or default icons, not the samples.', 'echo-knowledge-base' ),
				'name' => 'theme',
				'options' => $theme_preset_options,
				'type' => 'select'
			],

			'theme_presets_INFO'                               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'raw_html',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' .
				             esc_html__( 'Additional Grid Layout, Sidebar Layout, and Modules are all available in our Elegant Layouts add-on.', 'echo-knowledge-base' ) .
				             ' <a href="https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/" target="_blank"><span class="epkbfa epkbfa-external-link"></span></a></div>'
			],

			// Advanced Tab
			'template_main_page_padding_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'template_main_page_padding_left' => [
						'style_name' => 'padding-left',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_padding_top' => [
						'style_name' => 'padding-top',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_padding_right' => [
						'style_name' => 'padding-right',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_padding_bottom' => [
						'style_name' => 'padding-bottom',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
				]
			],
			'template_main_page_margin_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Margin', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'template_main_page_margin_left' => [
						'style_name' => 'margin-left',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_margin_top' => [
						'style_name' => 'margin-top',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_margin_right' => [
						'style_name' => 'margin-right',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
					'template_main_page_margin_bottom' => [
						'style_name' => 'margin-bottom',
						'postfix' => 'px',
						'target_selector' => '.eckb-kb-template',
					],
				]
			],
		];

		return [
			'content_zone' => [
				'title'     =>  esc_html__( 'Page Content', 'echo-knowledge-base' ),
				'classes'   => '#epkb-main-page-container, #eckb-article-page-container-v2, #elay-grid-layout-page-container',
				'settings'  => $settings,
				'parent_zone_tab_title' => esc_html__( 'Page Content', 'echo-knowledge-base' )
			]];
	}

	/**
	 * Serach Box zone
	 * @return array
	 */
	private static function search_box_zone() {
		
		$settings = [

			// Style Tab
			'search_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container',
				'style_name' => 'background-color'
			],

			// Features Tab
			'width' => [    // search box width
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'width_info'                               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'raw_html',
				'toggler' => [
					'templates_for_kb' => 'current_theme_templates',
				],
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' .
				             esc_html__( 'We have detected that you are using the Current Theme Template option. If your width is not expanding the way you want, it is because the theme is controlling the total width. ' .
				                'You have two options: either switch to the KB Template option or check your theme settings to expand the width.', 'echo-knowledge-base' ) .
				             ' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank"><span class="epkbfa epkbfa-external-link"></span></a></div>'
			],
			'search_layout' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],

			// Advanced Tab
			'search_box_padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'search_box_padding_left' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'search_box_padding_top' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'search_box_padding_right' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'search_box_padding_bottom' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],
			'search_box_margin' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Margin', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'search_box_margin_top' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'margin-top',
						'postfix' => 'px'
					],
					'search_box_margin_bottom' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'margin-bottom',
						'postfix' => 'px'
					],
				]
			],
		];

		return [
			'search_box_zone' => [
				'title'     =>  esc_html__( 'Search Box', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'search_layout' => 'epkb-search-form-0'
				],
				'parent_zone_tab_title' => esc_html__( 'Search Box', 'echo-knowledge-base' ),
			]];
	}

	/**
	 * Search Title zone
	 * @return array
	 */
	private static function search_title_zone() {

		$settings = [

			// Content Tab
			'search_title' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-doc-search-container__title',
				'target_attr' => 'value',
				'text' => 1
			],
			'search_title_html_tag' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-doc-search-container__title',
				'reload' => 1,
				'text_style' => 'inline'
			],

			// Style Tab
			'search_title_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container__title',
			],
			'search_title_font_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container__title',
				'style_name' => 'color'
			],
		];

		return [
			'search_title_zone' => [
				'title'     =>  esc_html__( 'Search Title', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container__title',
				'settings'  => $settings
			]];
	}

	/**
	 * Search Input box zone
	 * @return array
	 */
	private static function search_input_zone() {

		$settings = [
			// Content Tab
			'search_box_hint' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#epkb_search_terms',
				'target_attr' => 'placeholder|aria-label',
			],
			'search_results_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'no_results_found' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'min_search_word_size_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],

			// Style Tab
			'search_input_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb_search_terms',
			],
			'search_box_input_width' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb_search_form',
				'style_name' => 'width',
				'postfix' => '%'
			],
			'search_input_border_width' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'border-width',
				'postfix' => 'px',
				'style'       => 'small',
			],
			'search_text_input_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'border-color',
				'description' => esc_html__( 'The color appears only if the border width is larger than zero.', 'echo-knowledge-base' ),
			],
			'search_text_input_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'background-color'
			],

			// Advanced Tab
			'search_box_results_style' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '#epkb_search_results',
			],
		];

		return [
			'search_input_zone' => [
				'title'     =>  esc_html__( 'Search Input Box', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container input',
				'settings'  => $settings
			]];
	}

	/**
	 * Serach Button zone
	 * @return array
	 */
	private static function search_button_zone() {

		$settings = [
			'search_button_name' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#epkb-search-kb',
				'target_attr' => 'value',
				'text' => 1
			],
			'search_btn_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box button',
				'style_name' => 'background-color'
			],
			'search_btn_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box button',
				'style_name' => 'border-color'
			],
		];

		return [
			'search_button_zone' => [
				'title'     =>  esc_html__( 'Search Button', 'echo-knowledge-base' ),
				'classes'   => '.epkb-search-box button',
				'disabled_settings' => [
					'search_layout' => 'epkb-search-form-3'
				],
				'settings'  => $settings
			]];
	}

	/**
	 * Category Zone - all articles and categories
	 * @return array
	 */
	private static function categories_container_zone() {

		$settings = [

			// Content Tab

			// Style Tab
			'background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-content-container',
				'style_name' => 'background-color'
			],
			'categories_container_category_box_header' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => esc_html__( 'Category Box', 'echo-knowledge-base' )
			],
			'section_border_radius' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'postfix' => 'px',
				'styles' => [
					'.epkb-top-category-box' => 'border-radius',
					'#epkb-ml__module-articles-list .epkb-ml-article-list-container .epkb-ml-article-section' => 'border-radius',
					'#epkb-ml__module-faqs .epkb-ml-faqs-cat-container' => 'border-radius',
					'#epkb-ml__module-categories-articles #epkb-ml-cat-article-sidebar .epkb-ml-article-section' => 'border-radius',
					'.section-head' => 'border-top-right-radius',
					'.section-head' . ' ' => 'border-top-left-radius', // space is important to have different keys in array
				]
			],
			'section_border_width' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-top-category-box,
					#epkb-ml__module-articles-list .epkb-ml-article-list-container .epkb-ml-article-section,
					#epkb-ml__module-faqs .epkb-ml-faqs-cat-container,
					#epkb-ml__module-categories-articles #epkb-ml-cat-article-sidebar .epkb-ml-article-section',
				'style_name' => 'border-width',
				'postfix' => 'px'
			],
			'section_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-top-category-box,
					#epkb-ml__module-articles-list .epkb-ml-article-list-container .epkb-ml-article-section,
					#epkb-ml__module-faqs .epkb-ml-faqs-cat-container,
					#epkb-ml__module-categories-articles #epkb-ml-cat-article-sidebar .epkb-ml-article-section',
				'style_name' => 'border-color',
				'description' => esc_html__( 'The border width must be larger than zero', 'echo-knowledge-base' ),
			],

			// Features Tab
			'section_body_height' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.epkb-section-body',
				'reload' => '1',
				'separator_above' => 'yes',
			],
			'section_box_height_mode' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.epkb-section-body',
				'reload' => '1'
			],
			'section_divider'       => [
				'editor_tab'        => self::EDITOR_TAB_FEATURES,
				'target_selector'   => '.epkb-top-category-box .section-head',
				'reload'            => 1,
				'separator_above'   => 'yes'
			],
			'section_box_shadow' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'nof_columns' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			// Advanced Tab

		];

		return [
			'categories_zone' => [
				'title'     =>  esc_html__( 'Categories', 'echo-knowledge-base' ),
				'classes'   => '.eckb-categories-list',
				'parent_zone_tab_title' => esc_html__( 'Categories', 'echo-knowledge-base' ),
				'settings'  => $settings
			]];
	}

	/**
	 * Category Header
	 *
	 * @param $kb_id
	 * @return array
	 */
	private static function category_header_zone( $kb_id ) {

		$settings = [

			// Content Tab


			// Style Tab
			'section_head_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-top-category-box .section-head h2, .epkb-top-category-box .section-head h2 a, .epkb-top-category-box .section-head .epkb-cat-name h2,' . ' ' .
				                     '.epkb-cat-name-count-container h2, .epkb-cat-name-count-container .epkb-cat-count, .epkb-tab-panel section .epkb-cat-name, .epkb-categories-template .epkb-cat-name-count-container',
			],
			'section_head_description_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => 'ul:not(.epkb-nav-tabs) .epkb-cat-desc, .section-head>.epkb-cat-desc',
				'toggler'           => 'section_desc_text_on'
			],
			'section_head_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-top-category-box .section-head',
				'style_name' => 'background-color'
			],
			'section_head_category_icon_color'      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.section-head .epkb-cat-icon',
				'style_name' => 'color',
				'separator_above'   => 'yes',
				'toggler' => [
					'section_head_category_icon_location' => '!no_icons',
				],
			],
			'section_head_description_font_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-cat-desc',
				'style_name'        => 'color',
				'toggler'           => 'section_desc_text_on'
			],
			'section_head_font_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.section-head .epkb-cat-name, .section-head .epkb-cat-name a, div>.epkb-category-level-1, #epkb-ml__module-faqs .epkb-ml-faqs__cat-header h3',
				'style_name' => 'color'
			],

			// Features Tab
			'section_head_category_icon_location' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'section_head_category_icon_size' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.section-head .epkb-cat-icon',
				'description' => '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .
				                                         '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ) ) . '" target="_blank">' . esc_html__( 'Edit Categories Icons', 'echo-knowledge-base' ) . '</a>',
				'style_name' => 'font-size',
				'postfix' => 'px',
				'styles' => [
					'.section-head img.epkb-cat-icon' => 'max-height'
				],
				'toggler' => [
					'section_head_category_icon_location' => '!no_icons',
				],
			],
			'section_desc_text_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'separator_above'   => 'yes',
				'description' => '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .
				                                         '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ) ) . '" target="_blank">' . esc_html__( 'Edit Categories Descriptions', 'echo-knowledge-base' ) . '</a>'
			],
			'section_head_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'separator_above'   => 'yes',
			],
			'section_hyperlink_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'separator_above'   => 'yes',
				'reload' => '1',
			],

			// Advanced Tab
			'section_head_padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),
				'subfields' => [
					'section_head_padding_left' => [
						'target_selector' => '.epkb-top-category-box .section-head',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'section_head_padding_top' => [
						'target_selector' => '.epkb-top-category-box .section-head',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'section_head_padding_right' => [
						'target_selector' => '.epkb-top-category-box .section-head',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'section_head_padding_bottom' => [
						'target_selector' => '.epkb-top-category-box .section-head',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],

		];
		return [
			'category_header_zone' => [
				'title'     =>  esc_html__( 'Category Header', 'echo-knowledge-base' ),
				'classes'   => '.section-head:not(.epkb-category--top-warning)',
				'settings'  => $settings
			]];
	}

	/**
	 * Category Body
	 *
	 * @param $kb_id
	 * @return array
	 */
	private static function category_body_zone( $kb_id ) {

		$settings = [

			// Content Tab
			'category_empty_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-articles-coming-soon',
				'text' => '1'
			],

			// Style Tab
			'section_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-section-body, #epkb-main-page-container .epkb-category-level-2-3__cat-name, #epkb-main-page-container .epkb-articles-coming-soon, #epkb-main-page-container .epkb-show-all-articles'
			],
			'section_body_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-top-category-box,
					#epkb-ml__module-articles-list .epkb-ml-article-list-container .epkb-ml-article-section,
					#epkb-ml__module-faqs .epkb-ml-faqs-cat-container,
					#epkb-ml__module-categories-articles #epkb-ml-cat-article-sidebar .epkb-ml-article-section',
				'style_name' => 'background-color'
			],
			'category_body_sub_category_header' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => 'Sub Category'
			],
			'expand_articles_icon' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'reload' => '1'
			],
			'link_to_toolbar_style' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'content' => '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .
				                                     '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ) ) . '" target="_blank">' . esc_html__( 'Edit sub-categories Icons', 'echo-knowledge-base' ) . '</a>'
			],
			'section_category_icon_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-category-level-2-3>.epkb-category-level-2-3__cat-icon',
				'style_name' => 'color'
			],
			'section_category_font_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-category-level-2-3__cat-name, .epkb-category-level-2-3__cat-name a',
				'style_name' => 'color',
			],

			// Features Tab
			'section_body_height' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.epkb-section-body',
				'reload' => '1',
				'separator_above' => 'yes',
			],
			'section_box_height_mode' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '.epkb-section-body',
				'reload' => '1'
			],
			'section_divider'       => [
				'editor_tab'        => self::EDITOR_TAB_FEATURES,
				'target_selector'   => '.epkb-top-category-box .section-head',
				'reload'            => 1,
				'separator_above'   => 'yes'
			],
			'section_divider_thickness' => [
				'editor_tab'        => self::EDITOR_TAB_FEATURES,
				'target_selector'   => '.epkb-top-category-box .section-head',
				'style_name'        => 'border-bottom-width',
				'postfix'           => 'px',
				'toggler'           => 'section_divider'
			],
			'section_divider_color' => [
				'editor_tab'        => self::EDITOR_TAB_FEATURES,
				'target_selector'   => '.epkb-top-category-box .section-head',
				'style_name'        => 'border-bottom-color',
				'toggler'           => 'section_divider'
			],

			// Advanced Tab
			'section_body_padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => esc_html__( 'Padding', 'echo-knowledge-base' ),

				'subfields' => [
					'section_body_padding_left' => [
						'target_selector' => '.epkb-section-body',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'section_body_padding_top' => [
						'target_selector' => '.epkb-section-body',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'section_body_padding_right' => [
						'target_selector' => '.epkb-section-body',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'section_body_padding_bottom' => [
						'target_selector' => '.epkb-section-body',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],
			'article_list_spacing' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'styles' => [
					'.epkb-articles [class*="epkb-article-level-"], .epkb-section-body .epkb-sub-category li' . ' ' => 'padding-top', // space important for php and don't brake css
					'.epkb-articles [class*="epkb-article-level-"], .epkb-section-body .epkb-sub-category li' => 'padding-bottom',
					'#epkb-ml__module-faqs .epkb-ml-faqs__item-container' . ' ' => 'padding-top', // space important for php and don't brake css
					'#epkb-ml__module-faqs .epkb-ml-faqs__item-container' => 'padding-bottom',
					'#epkb-ml__module-articles-list .epkb-ml-articles-list li a' . ' ' => 'padding-top', // space important for php and don't brake css
					'#epkb-ml__module-articles-list .epkb-ml-articles-list li a' => 'padding-bottom',
					'#epkb-ml-cat-article-sidebar .epkb-ml-articles-list li a' . ' ' => 'padding-top', // space important for php and don't brake css
					'#epkb-ml-cat-article-sidebar .epkb-ml-articles-list li a' => 'padding-bottom',
				],
				'postfix' => 'px'
			],
		];

		return [
			'category_box_zone' => [
				'title'     =>  esc_html__( 'Category Body', 'echo-knowledge-base' ),
				'parent_zone_tab_title' => esc_html__( 'Category Body', 'echo-knowledge-base' ),
				'classes'   => '.epkb-section-body',
				'settings'  => $settings
			]];
	}

	/**
	 * Articles zone
	 * @return array
	 */
	private static function articles_zone() {

		$settings = [

			// content
			'collapse_articles_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'show_all_articles_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],

			// style
			'article_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-main-page-container .epkb-section-body .eckb-article-title__text,
					#epkb-ml__module-categories-articles .epkb-section-body .eckb-article-title,
					#epkb-ml-cat-article-sidebar .epkb-article-inner,
					#epkb-ml__module-faqs .epkb-ml-faqs__item__question .epkb-ml-faqs__item__question__text,
					#epkb-ml__module-articles-list .epkb-article-inner',
				'style_important' => 1
			],
			'article_font_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-title,
					#epkb-ml__module-articles-list .epkb-article-inner .epkb-article__text,
					#epkb-ml__module-faqs .epkb-ml-faqs__item__question .epkb-ml-faqs__item__question__text',
				'style_name' => 'color'
			],
			'article_icon_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-title>.eckb-article-title__icon,
					#epkb-ml__module-articles-list .epkb-article-inner .epkb-article__icon',
				'style_name' => 'color'
			],

			// features
			'nof_articles_displayed' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			// advanced
			'article_list_margin' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '.epkb-main-category, .epkb-sub-category',
				'style_name' => is_rtl() ? 'padding-right' : 'padding-left',
				'postfix' => 'px'
			],
			'sub_article_list_margin' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '.epkb-sub-category ul',
				'style_name' => is_rtl() ? 'padding-right' : 'padding-left',
				'postfix' => 'px'
			],
			'section_article_underline' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
		];

		return [
			'articles_zone' => [
				'title'     =>  esc_html__( 'Articles', 'echo-knowledge-base' ),
				'classes'   => '.epkb-articles',
				'settings'  => $settings
			]];
	}

	/**
	 * Tabs zone - for Tabs Layout
	 *
	 * @param $kb_config
	 *
	 * @return array
	 */
	private static function tabs_zone( $kb_config ) {

		$settings = [
			// Content Tab

			// Style Tab
			'tab_typography'                     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-main-nav, .epkb-tabs-template .main-category-selection-1',
			],
			'tab_nav_font_color'                => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-nav-tabs .epkb-category-level-1, .epkb-nav-tabs .epkb-category-level-1+p, .epkb-tabs-template .main-category-selection-1 .epkb-category-level-1',
				'style_name' => 'color',
			],
			'tab_nav_active_font_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' =>
					'
					#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
					#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1+p
					',
				'style_name' => 'color',
				'separator_above' => 'yes'
			],
			'tab_nav_active_background_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#epkb-content-container .epkb-nav-tabs .active',
				'style_name' => 'background-color',
				'styles' => [
					'#epkb-content-container .epkb-nav-tabs .active:after' => 'border-top-color'
				]
			],
			'tab_nav_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-nav-tabs',
				'style_name' => 'border-color',
				'separator_above' => 'yes'
			],
			'tab_nav_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-main-nav, .epkb-nav-tabs, .epkb-tabs-template .main-category-selection-1',
				'style_name' => 'background-color'
			],

			// Features Tab
			'tab_down_pointer' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],

			// Advanced Tab

		];

		// if we have too many tabs and will show categories drop down instead then show the title of the drop down and hide irrelevant settings
		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
		}

		if ( count($category_seq_data) > 6 ) {
			$settings['choose_main_topic'] = [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-tabs-template .main-category-selection-1 .epkb-category-level-1',
				'text' => 1
			];

			unset( $settings['tab_nav_active_font_color'] );
			unset( $settings['tab_nav_active_background_color'] );
			unset( $settings['tab_nav_border_color'] );
		}

		return [
			'tabs_zone' => [
				'title'     =>  esc_html__( 'Tabs', 'echo-knowledge-base' ),
				'classes'   => '.epkb-main-nav, .main-category-selection-1',
				'settings'  => $settings
			]];
	}

	/**
	 * Retrieve Editor configuration for Settings panel
	 * @return array
	 */
	public static function get_editor_panel_config() {
		
		$global_settings = [
			'kb_main_page_layout' => [
				'type' => 'none',
				'reload' => 1,
			],
			'templates_for_kb' => [
				'type' => 'none',
				'reload' => 1,
			],
		];
		
		$editor_panel_config = [
			'settings_zone' => [
				'settings' => $global_settings
			],
		];
		
		return $editor_panel_config;
	}

	/**
	 * Retrieve Editor configuration
	 */
	public function load_setting_zones() {
		
		$this->setting_zones = [];

		// Advanced Search has its own search box settings so exclude the KB core ones
		if ( ! $this->is_asea ) {
			$this->setting_zones += self::search_box_zone();
			$this->setting_zones += self::search_title_zone();
			$this->setting_zones += self::search_input_zone();
			$this->setting_zones += self::search_button_zone();
		}

		// Categories and Articles for KB Core Layouts
		if ( $this->is_basic_main_page || $this->is_tabs_main_page || $this->is_categories_main_page ) {
			$this->setting_zones += self::categories_container_zone();
			$this->setting_zones += self::category_header_zone( $this->config['id'] );
			$this->setting_zones += self::category_body_zone( $this->config['id'] );
			$this->setting_zones += self::articles_zone();
			$this->setting_zones += self::tabs_zone( $this->config );
		}
		
		$this->unset_settings = [];
		if ( $this->config['templates_for_kb'] != 'kb_templates' ) {
			$this->unset_settings = array_merge($this->unset_settings,[
				'template_main_page_display_title',
			]);
		}
		if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {
			$this->unset_settings = array_merge($this->unset_settings, [
				'theme_presets_INFO',
			]);
		}
		if ( $this->is_categories_main_page ) {
			$this->unset_settings = array_merge($this->unset_settings, [
				'expand_articles_icon'
			]);
		}
		if ( $this->is_basic_main_page || $this->is_tabs_main_page ) {
			$this->unset_settings = array_merge($this->unset_settings, [
				'link_to_toolbar_style'
			]);
		}

		// Sidebar uses article page zone otherwise use Main Page page zone
		if ( $this->is_sidebar_main_page ) {

			if ( $this->config['templates_for_kb'] == 'kb_templates' ) {
				$this->setting_zones += EPKB_Editor_Article_Page_Config::page_zone( $this->config );
			} else {
				$this->setting_zones += self::page_zone( $this->config );
			}

			$this->unset_settings = array_merge($this->unset_settings, [
				'width',
				'template_article_padding_group',
				'template_article_margin_group',
			]);

		} else {
			$this->setting_zones += self::page_zone( $this->config );
		}
		
		// global settings like changing layout, theme, presets
		$this->setting_zones += self::get_editor_panel_config();
	}
}