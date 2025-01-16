<?php

/**
 * Configuration for the front end editor
 */
 
class EPKB_Editor_Archive_Page_Config extends EPKB_Editor_KB_Base_Config {

	/** SEE DOCUMENTATION IN THE BASE CLASS **/
	protected $page_type = 'archive-page';

	/**
	 * Archive zone
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function archive_zone( $kb_config ) {

		$settings = [

			// Content Tab
			'category_focused_menu_heading_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-acll__title',
				'text' => 1
			],
			'template_category_archive_page_heading_description' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-category-archive-title-desc',
				'text' => 1
			],
			'template_category_archive_read_more' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-read-more-text',
				'text' => 1
			],

			// Style Tab
			'template_category_archive_page_style' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'reload' => '1'
			],

			// Features Tab
			'archive-content-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'style'       => 'small',
			],
			'archive-show-sub-categories' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],

			// Advanced Tab
			'archive-container-width-units-v2' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'type' => 'units',
				'target_selector' => '#eckb-categories-archive-container-v2'
			],
			'archive-container-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'postfix' => 'archive-container-width-units-v2',
				'style'       => 'small',
				'styles' => [
					'#eckb-categories-archive-container-v2' => 'width',
				]
			],
			'archive-content-padding-v2' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '#eckb-categories-archive__body__content',
				'style_name' => 'padding',
				'postfix' => 'px',
				'style'       => 'small',

			],

		];

		if ( $kb_config['kb_main_page_layout'] != 'Categories' ) {
			unset($settings['category_focused_menu_heading_text']);
		}

		return [
			'archive' => [
				'title'     =>  esc_html__( 'Archive', 'echo-knowledge-base' ),
				'classes'   => '#eckb-categories-archive__body',
				'settings'  => $settings
			]];
	}

	/**
	 * Archive Meta zone
	 * @return array
	 */
	private static function archive_meta_zone() {
		$settings = [

			// Content Tab
			'template_category_archive_date' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-posted-on .eckb-article-meta-name',
				'text' => 1
			],
			'template_category_archive_author' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-byline .eckb-article-meta-name',
				'text' => 1
			],
			'template_category_archive_categories' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-categories .eckb-article-meta-name',
				'text' => 1
			],

			// Features Tab
			'template_category_archive_date_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'template_category_archive_author_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'template_category_archive_categories_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],

		];

		return [
			'archive_meta' => [
				'title'     =>  esc_html__( 'Archive Meta Data', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-metadata',
				'settings'  => $settings,
				'disabled_settings' => [
					'template_category_archive_date_on' => 'off',
					'template_category_archive_author_on' => 'off',
					'template_category_archive_categories_on' => 'off'
				],
			]];
	}

	/**
	 * Categories List zone
	 * @return array
	 */
	private static function categories_layout_list_zone() {

		$settings = [

			// Content Tab
			'category_focused_menu_heading_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-acll__title',
				'text' => 1
			],

			// Style Tab
			'categories_box_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-cat-layout-list, .eckb-article-cat-layout-list a',
			],
			'category_box_title_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => ' .eckb-acll__title',
				'style_name' => 'color'
			],
			'category_box_container_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-cat-layout-list',
				'style_name' => 'background-color'
			],
			'category_box_category_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__name',
				'style_name' => 'color'
			],
			'category_box_count_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'background-color'
			],
			'category_box_count_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'color'
			],
			'category_box_count_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'border-color'
			],

			// Features Tab

		];

		return [
			'categories_list' => [
				'title'     =>  esc_html__( 'Categories List', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-cat-layout-list',
				'settings'  => $settings
			]];
	}
	
	/**
	 * Retrieve Editor configuration
	 */
	public function load_setting_zones() {

		// Result config
		$this->setting_zones = [];
		$this->setting_zones += self::archive_zone( $this->config );
		$this->setting_zones += self::archive_meta_zone();
		$this->setting_zones += self::categories_layout_list_zone();
	}
}