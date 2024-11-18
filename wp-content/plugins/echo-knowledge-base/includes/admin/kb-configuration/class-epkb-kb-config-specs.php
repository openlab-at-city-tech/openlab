<?php

/**
 * Lists all KB configuration settings and adds filter to get configuration from add-ons.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Specs {

	const BLANK = 'blank';
	const ARCHIVED = 'archived';
	const PUBLISHED = 'published';

	private static $cached_specs = array();

	public static function get_defaults() {
		return array(
			'label'       => esc_html__( 'Label', 'echo-knowledge-base' ),
			'type'        => EPKB_Input_Filter::TEXT,
			'mandatory'   => true,
			'max'         => '20',
			'min'         => '3',
			'options'     => array(),
			'internal'    => false,
			'default'     => ''
		);
	}

	public static function get_categories_display_order() {
		return array( 'alphabetical-title' => esc_html__( 'Alphabetical by Name', 'echo-knowledge-base' ),
							 'created-date' => esc_html__( 'Chronological by Date Created or Published', 'echo-knowledge-base' ),
							 'user-sequenced' => esc_html__( 'Custom - Drag and Drop Categories', 'echo-knowledge-base' ) );
	}

	public static function get_articles_display_order() {
		return array( 'alphabetical-title' => esc_html__( 'Alphabetical by Title', 'echo-knowledge-base' ),
						'created-date' => esc_html__( 'Chronological by Date Created or Published', 'echo-knowledge-base' ),
						'modified-date' => esc_html__( 'Chronological by Date Modified', 'echo-knowledge-base' ),
						'user-sequenced' => esc_html__( 'Custom - Drag and Drop articles', 'echo-knowledge-base' ) );
	}

	// not displayed if the priority is set to 0
	private static $sidebar_component_priority_defaults = array(
		'kb_sidebar_left' => '0',
		'kb_sidebar_right' => '0',
		'toc_left' => '0',
		'toc_content' => '0',
		'toc_right' => '1',
		'nav_sidebar_left' => '1',
		'nav_sidebar_right' => '0'
	);

	public static function add_sidebar_component_priority_defaults( $article_sidebar_component_priority ) {
		return array_merge( self::$sidebar_component_priority_defaults, $article_sidebar_component_priority );
	}

	public static function get_sidebar_component_priority_names() {
		return array_keys( self::$sidebar_component_priority_defaults );
	}

	/**
	 * Defines how KB configuration fields will be displayed, initialized and validated/sanitized
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array with KB config specification
	 */
	public static function get_fields_specification( $kb_id ) {

		// if kb_id is invalid use default KB
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// retrieve settings if already cached
		if ( ! empty( self::$cached_specs[$kb_id] ) && is_array( self::$cached_specs[$kb_id] ) ) {
			return self::$cached_specs[$kb_id];
		}


		// all CORE settings are listed here; 'name' used for HTML elements
		$config_specification = array(

			/******************************************************************************
			 *
			 *  Internal settings
			 *
			 ******************************************************************************/
			'id' => array(
				'label'       => 'KB ID',
				'type'        => EPKB_Input_Filter::ID,
				'internal'    => true,
				'default'     => $kb_id
			),
			'status' => array(
				'label'       => 'status',
				'type'        => EPKB_Input_Filter::ENUMERATION,
				'options'     => array( self::BLANK, self::PUBLISHED, self::ARCHIVED ),
				'internal'    => true,
				'default'     => self::PUBLISHED
			),
			'kb_main_pages' => array(
				'label'       => 'kb_main_pages',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'article_sidebar_component_priority' => array(
				'label'       => 'article_sidebar_component_priority',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => self::$sidebar_component_priority_defaults
			),
			'first_plugin_version' => array(
				'label'       => 'first_plugin_version',
				'name'        => 'first_plugin_version',
				'type'        => EPKB_Input_Filter::TEXT,
				'internal'    => true,
				'default'     => EPKB_Upgrades::NOT_INITIALIZED    // TODO 2025 Echo_Knowledge_Base::$version
			),
			'upgrade_plugin_version' => array(
				'label'       => 'upgrade_plugin_version',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::TEXT,
				'internal'    => true,
				'default'     => EPKB_Upgrades::NOT_INITIALIZED,    // TODO 2025 Echo_Knowledge_Base::$version
			),


			/******************************************************************************
			 *
			 *  Main
			 *
			 ******************************************************************************/
			'kb_name' => array(
				'label'       => esc_html__( 'CPT Name', 'echo-knowledge-base' ),
				'name'        => 'kb_name',
				'max'         => '70',
				'min'         => '1',
				'reload'      => true,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == 1 ? '' : ' ' . $kb_id)
			),
			'kb_articles_common_path' => array(
				'label'       => esc_html__( 'Common Path for Articles', 'echo-knowledge-base' ),
				'name'        => 'kb_articles_common_path',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::URL,
				'default'     => EPKB_KB_Handler::get_default_slug( $kb_id )
			),
			'kb_main_page_layout' => array(
				'label'       => esc_html__( 'Main Page Layout', 'echo-knowledge-base' ),
				'name'        => 'kb_main_page_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => [
					EPKB_Layout::BASIC_LAYOUT       => esc_html__( 'Basic', 'echo-knowledge-base' ),
					EPKB_Layout::TABS_LAYOUT        => esc_html__( 'Tabs', 'echo-knowledge-base' ),
					EPKB_Layout::CLASSIC_LAYOUT     => esc_html__( 'Classic', 'echo-knowledge-base' ),
					EPKB_Layout::DRILL_DOWN_LAYOUT  => esc_html__( 'Drill Down', 'echo-knowledge-base' ),
					EPKB_Layout::CATEGORIES_LAYOUT  => esc_html__( 'Category Focused', 'echo-knowledge-base' ),
					EPKB_Layout::GRID_LAYOUT        => esc_html__( 'Grid', 'echo-knowledge-base' ),
					EPKB_Layout::SIDEBAR_LAYOUT     => esc_html__( 'Sidebar', 'echo-knowledge-base' ),
				],
				'default'     => EPKB_Layout::BASIC_LAYOUT,
			),
			'kb_sidebar_location' => array(
					'label'       => esc_html__( 'Article Sidebar Location', 'echo-knowledge-base' ),
					'name'        => 'kb_sidebar_location',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
							'left-sidebar'   => is_rtl() ? _x( 'Right Sidebar', 'echo-knowledge-base' ) : _x( 'Left Sidebar', 'echo-knowledge-base' ),
							'right-sidebar'  => is_rtl() ? _x( 'Left Sidebar', 'echo-knowledge-base' ) : _x( 'Right Sidebar', 'echo-knowledge-base' ),
							'no-sidebar'     => _x( 'No Sidebar', 'echo-knowledge-base' ) ),
					'default'     => 'no-sidebar'
			),
			'article_nav_sidebar_type_left' => array(
				'label'       => esc_html__( 'Sidebar Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_nav_sidebar_type_left',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-nav-sidebar-v1' => esc_html__( 'All Categories and Articles', 'echo-knowledge-base' ), // core or elay
					'eckb-nav-sidebar-categories' => esc_html__( 'Top Categories', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-current-category' => esc_html__( 'Current Category and Articles', 'echo-knowledge-base' ), // current category and below
					'eckb-nav-sidebar-none' => esc_html__( 'None', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-nav-sidebar-v1'
			),
			'article_nav_sidebar_type_right' => array(
				'label'       => esc_html__( 'Sidebar Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_nav_sidebar_type_right',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-nav-sidebar-v1' => esc_html__( 'All Categories and Articles', 'echo-knowledge-base' ), // core or elay
					'eckb-nav-sidebar-categories' => esc_html__( 'Top Categories', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-current-category' => esc_html__( 'Current Category and Articles', 'echo-knowledge-base' ), // current category and below
					'eckb-nav-sidebar-none' => esc_html__( 'None', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-nav-sidebar-none'
			),
			'article-left-sidebar-toggle' => array(
				'label'       => is_rtl() ? esc_html__( 'Right Sidebar' ) : esc_html__( 'Left Sidebar' ),
				'name'        => 'article-left-sidebar-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article-right-sidebar-toggle' => array(
				'label'       => is_rtl() ? esc_html__( 'Left Sidebar' ) : esc_html__( 'Right Sidebar' ),
				'name'        => 'article-right-sidebar-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_list_spacing' => array(    // common across all layouts, modules and sidebars
				'label'       => esc_html__( 'Space Between Articles', 'echo-knowledge-base' ),
				'name'        => 'article_list_spacing',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 6
			),
			'visual_helper_switch_visibility_toggle' => array(
				'label'       => esc_html__( 'Visual Helper', 'echo-knowledge-base' ),
				'name'        => 'visual_helper_switch_visibility_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'visual_helper_switch_show_state' => array(
				'label'       => esc_html__( 'Visual Helper - Show', 'echo-knowledge-base' ),
				'name'        => 'visual_helper_switch_show_state',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  OTHER
			 *
			 ******************************************************************************/
			'categories_in_url_enabled' => array(
				'label'       => esc_html__( 'Categories in URL', 'echo-knowledge-base' ),
				'name'        => 'categories_in_url_enabled',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'     => esc_html__( 'on', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'off', 'echo-knowledge-base' )
				),
				'default'     => 'off'
			),
			'category_slug' => array(   
				'label'       => esc_html__( 'Category Slug', 'echo-knowledge-base' ),
				'name'        => 'category_slug',
				'max'         => '70',
				'min'         => '1',
				'reload'      => true,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '',
				'mandatory'   => false,
			),
			'kb_main_page_category_link' => array(      // NOT USED; done in Grid Layout
				'label'       => esc_html__( 'Main Page Category Link', 'echo-knowledge-base' ),
				'name'        => 'kb_main_page_category_link',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     =>
					array(
						'default'          => esc_html__( 'Article Page', 'echo-knowledge-base' ),
						'category_archive' => esc_html__( 'Category Archive Page', 'echo-knowledge-base' )
					),
				'default'     => 'default',
			),
			'categories_display_sequence' => array(
				'label'       => esc_html__( 'Categories Sequence', 'echo-knowledge-base' ),
				'name'        => 'categories_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_categories_display_order(),
				'default'     => 'user-sequenced'
			),
			'articles_display_sequence' => array(
				'label'       => esc_html__( 'Articles Sequence', 'echo-knowledge-base' ),
				'name'        => 'articles_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_articles_display_order(),
				'default'     => 'user-sequenced'
			),
			'templates_for_kb' => array(
				'label'       => esc_html__( 'Choose Template', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'kb_templates'       => esc_html__( 'Knowledge Base Template', 'echo-knowledge-base' ),
					'current_theme_templates'    => esc_html__( 'Current Theme Template', 'echo-knowledge-base' ),
				),
				'default'     => 'kb_templates'
			),
			'template_for_archive_page' => array(
				'label'       => esc_html__( 'Archive Page Template', 'echo-knowledge-base' ),
				'name'        => 'template_for_archive_page',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'kb_templates'       => esc_html__( 'KB Archive Page Template', 'echo-knowledge-base' ),
					'current_theme_templates'    => esc_html__( 'Current Theme Template', 'echo-knowledge-base' ),
				),
				'default'     => 'kb_templates'
			),
			'wpml_is_enabled' => array(
				'label'       => esc_html__( 'Polylang and WPML', 'echo-knowledge-base' ),
				'name'        => 'wpml_is_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'articles_comments_global' => array(
				'label'       => esc_html__( 'Comments', 'echo-knowledge-base' ),
				'name'        => 'articles_comments_global',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'		=> esc_html__( "Enabled for all articles", 'echo-knowledge-base' ),
					'off'		=> esc_html__( "Disabled for all articles", 'echo-knowledge-base' ),
					'article'	=> esc_html__( "Determined by individual article's comments option", 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),
			'template_widget_sidebar_defaults'  => array(
				'label'       => esc_html__( 'Widget Sidebar Styling', 'echo-knowledge-base' ),
				'name'        => 'template_widget_sidebar_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/******************************************************************************
			 *
			 *  ARTICLE STRUCTURE v2
			 *
			 ******************************************************************************/

			// Article Version 2 - PAGE
			'article-container-desktop-width-v2' => array(  // article page width (search and content)
				'label'       => esc_html__( 'Search Box Width', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-container-desktop-width-units-v2' => array(
				'label'       => esc_html__( 'Width - Units', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),
			'article-container-tablet-width-v2' => array(
				'label'       => esc_html__( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-container-tablet-width-units-v2' => array(
				'label'       => esc_html__( 'Width - Units(Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),

			// Article Version 2 - BODY ( left sidebar, content, right sidebar )
			'article-body-desktop-width-v2' => array(   // article body width (content) -> includes sidebars, excludes search box
				'label'       => esc_html__( 'Content Width Including Any Sidebars', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1400
			),
			'article-body-desktop-width-units-v2' => array(
				'label'       => esc_html__( 'Width Units', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => 'px'
			),
			'article-body-tablet-width-v2' => array(
				'label'       => esc_html__( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-body-tablet-width-units-v2' => array(
				'label'       => esc_html__( 'Width - Units (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),

			// Article Version 2 - LEFT SIDEBAR
			'article-left-sidebar-desktop-width-v2' => array(
				'label'       => esc_html__( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'article-left-sidebar-tablet-width-v2' => array(
				'label'       => esc_html__( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'article-left-sidebar-padding-v2_top' => array(
				'label'       => esc_html__( 'Top ', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_top',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_right',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_bottom',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_left',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-background-color-v2' => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-background-color-v2',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-left-sidebar-starting-position' => array(
				'label'       => esc_html__( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-left-sidebar-starting-position-mobile' => array(
				'label'       => esc_html__( 'Top Offset Mobile ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-starting-position-mobile',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-left-sidebar-match' => array(
				'label'       => esc_html__( 'Align sidebar to article content', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Article Version 2 - CONTENT
			'article-content-padding-v2' => array(
				'label'       => esc_html__( 'Content Area Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-content-background-color-v2' => array(
				'label'       => esc_html__( 'Content Area Background', 'echo-knowledge-base' ),
				'name'        => 'article-content-background-color-v2',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-meta-typography' => array(
				'label'       => esc_html__( 'Meta Typography', 'echo-knowledge-base' ),
				'name'        => 'article-meta-typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article-meta-color' => array(
				'label'       => esc_html__( 'Meta', 'echo-knowledge-base' ),
				'name'        => 'article-meta-color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),

			// Article Version 2 - RIGHT SIDEBAR
			'article-right-sidebar-desktop-width-v2' => array(
				'label'       => esc_html__( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'article-right-sidebar-tablet-width-v2' => array(
				'label'       => esc_html__( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'article-right-sidebar-padding-v2_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_top',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_right',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_bottom',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_left',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-background-color-v2' => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-background-color-v2',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-right-sidebar-starting-position' => array(
				'label'       => esc_html__( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-right-sidebar-starting-position-mobile' => array(
				'label'       => esc_html__( 'Top Offset Mobile ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-starting-position-mobile',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-right-sidebar-match' => array(
				'label'       => esc_html__( 'Align sidebar to article content', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Article Version 2 - Advanced
			'article-mobile-break-point-v2' => array(
				'label'       => esc_html__( 'Mobile (px)', 'echo-knowledge-base' ),
				'name'        => 'article-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 768
			),
			'article-tablet-break-point-v2' => array(
				'label'       => esc_html__( 'Tablet (px)', 'echo-knowledge-base' ),
				'name'        => 'article-tablet-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1025
			),


			/******************************************************************************
			 *
			 *  ARTICLE SIDEBAR V1
			 *
			 ******************************************************************************/

			/***  Article Sidebar -> General ***/

			'sidebar_side_bar_height_mode' => array(
				'label'       => esc_html__( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'sidebar_side_bar_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'side_bar_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
					'side_bar_fixed_height' => esc_html__( 'Fixed (Scrollbar)', 'echo-knowledge-base' ) ),
				'default'     => 'side_bar_no_height'
			),
			'sidebar_side_bar_height' => array(
				'label'       => esc_html__( 'Height ( px )', 'echo-knowledge-base' ),
				'name'        => 'sidebar_side_bar_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '350'
			),
			'sidebar_scroll_bar' => array(
				'label'       => esc_html__( 'Scroll Bar style', 'echo-knowledge-base' ),
				'name'        => 'sidebar_scroll_bar',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'slim_scrollbar'    => _x( 'Slim','echo-knowledge-base' ),
					'default_scrollbar' => _x( 'Default', 'echo-knowledge-base' ) ),
				'default'     => 'slim_scrollbar'
			),
			'sidebar_section_category_typography' => array(
				'label'       => esc_html__( 'Category Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '18',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_category_typography_desc' => array(
				'label'       => esc_html__( 'Category Description Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_typography_desc',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_body_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_top_categories_collapsed' => array(
				'label'       => esc_html__( 'Top Categories Collapsed', 'echo-knowledge-base' ),
				'name'        => 'sidebar_top_categories_collapsed',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_nof_articles_displayed' => array(
				'label'       => esc_html__( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'sidebar_nof_articles_displayed',
				'max'         => '200',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 15,
			),
			'sidebar_show_articles_before_categories' => array(
				'label'       => esc_html__( 'Show Articles', 'echo-knowledge-base' ),
				'name'        => 'sidebar_show_articles_before_categories',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => esc_html__( 'Before Categories', 'echo-knowledge-base' ),
					'off' => esc_html__( 'After Categories', 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),
			'sidebar_expand_articles_icon' => array(
				'label'       => esc_html__( 'Icon to Expand/Collapse Articles', 'echo-knowledge-base' ),
				'name'        => 'sidebar_expand_articles_icon',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
										'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
										'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
										'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-knowledge-base' ),
										'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-knowledge-base' ),
										'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' ) ),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),

			/***  Article Sidebar -> Articles Listed in Sub-Category ***/

			'sidebar_section_head_alignment' => array(
				'label'       => esc_html__( 'Category Text Alignment', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left' => esc_html__( 'Left', 'echo-knowledge-base' ),
					'center' => esc_html__( 'Centered', 'echo-knowledge-base' ),
					'right' => esc_html__( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'sidebar_section_head_padding_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_padding_top',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_padding_bottom',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_padding_left',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_padding_right',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_desc_text_on' => array(
				'label'       => esc_html__( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_desc_text_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_section_border_radius' => array(
				'label'       => esc_html__( 'Corner Radius', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5
			),
			'sidebar_section_border_width' => array(
				'label'       => esc_html__( 'Border Thickness', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'sidebar_section_box_shadow' => array(
				'label'       => esc_html__( 'Navigation Shadow', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_box_shadow',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => esc_html__( 'No Shadow', 'echo-knowledge-base' ),
					'section_light_shadow' => esc_html__( 'Light Shadow', 'echo-knowledge-base' ),
					'section_medium_shadow' => esc_html__( 'Medium Shadow', 'echo-knowledge-base' ),
					'section_bottom_shadow' => esc_html__( 'Bottom Shadow', 'echo-knowledge-base' )
				),
				'default'     => 'section_medium_shadow'
			),
			'sidebar_section_divider' => array(
				'label'       => esc_html__( 'On/Off', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_divider',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'sidebar_section_divider_thickness' => array(
				'label'       => esc_html__( 'Thickness ( px )', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_divider_thickness',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'sidebar_section_box_height_mode' => array(
				'label'       => esc_html__( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_box_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
					'section_min_height' => esc_html__( 'Minimum', 'echo-knowledge-base' ),
					'section_fixed_height' => esc_html__( 'Maximum', 'echo-knowledge-base' )
				),
				'default'     => 'section_min_height'
			),
			'sidebar_section_body_height' => array(
				'label'       => esc_html__( 'Height ( px )', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 350
			),
			'sidebar_section_body_padding_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_body_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'sidebar_section_body_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'sidebar_section_body_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5
			),
			'sidebar_article_underline' => array(
				'label'       => esc_html__( 'Article Underline Hover', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_underline',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_article_active_bold' => array(
				'label'       => esc_html__( 'Article Active Bold', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_active_bold',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'sidebar_article_list_margin' => array(
				'label'       => esc_html__( 'Indentation', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_list_margin',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),

			/***  Article Sidebar -> Colors -> Articles Listed in Category Box ***/

			'sidebar_background_color' => array(
				'label'       => esc_html__( 'Article / Sub Category Background', 'echo-knowledge-base' ),
				'name'        => 'sidebar_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fdfdfd'
			),
			'sidebar_article_font_color' => array(
				'label'       => esc_html__( 'Article Title', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#202124'
			),
			'sidebar_article_icon_color' => array(
				'label'       => esc_html__( 'Article Icon', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#202124'
			),
			'sidebar_article_icon_toggle' => array(
				'label'       => esc_html__( 'Article Icon', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_icon_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'sidebar_article_active_font_color' => array(
				'label'       => esc_html__( 'Active Article', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_active_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'sidebar_article_active_background_color' => array(
				'label'       => esc_html__( 'Active Article Background', 'echo-knowledge-base' ),
				'name'        => 'sidebar_article_active_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e8e8e8'
			),
			'sidebar_section_head_font_color' => array(
				'label'       => esc_html__( 'Category Name', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#525252'
			),
			'sidebar_section_head_background_color' => array(
				'label'       => esc_html__( 'Category Background', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f1f1f1'
			),
			'sidebar_section_head_description_font_color' => array(
				'label'       => esc_html__( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_head_description_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'sidebar_section_border_color' => array(
				'label'       => esc_html__( 'Sidebar Border', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F7F7F7'
			),
			'sidebar_section_divider_color' => array(
				'label'       => esc_html__( 'Top Category Border Bottom', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_divider_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CDCDCD'
			),
			'sidebar_section_category_font_color' => array(
				'label'       => esc_html__( 'Subcategory Name', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#868686'
			),
			'sidebar_section_subcategory_typography' => array(
				'label'       => esc_html__( 'Subcategory Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_subcategory_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_category_icon_color' => array(
				'label'       => esc_html__( 'Subcategory: Expand Icon', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#868686'
			),

			 /*** Article Sidebar -> Front-End Text ***/

			'sidebar_category_empty_msg' => array(
				'label'       => esc_html__( 'Empty Category Message', 'echo-knowledge-base' ),
				'name'        => 'sidebar_category_empty_msg',
				'max'         => '150',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Articles coming soon', 'echo-knowledge-base' )
			),
			'sidebar_collapse_articles_msg' => array(
				'label'       => esc_html__( 'Collapse Articles Text', 'echo-knowledge-base' ),
				'name'        => 'sidebar_collapse_articles_msg',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Collapse Articles', 'echo-knowledge-base' )
			),
			'sidebar_show_all_articles_msg' => array(
				'label'       => esc_html__( 'Show Remaining Articles Text', 'echo-knowledge-base' ),
				'name'        => 'sidebar_show_all_articles_msg',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Show Remaining Articles', 'echo-knowledge-base' )
			),
            'sidebar_show_sub_category_articles_msg' => array(
                'label'       => esc_html__( 'Show Sub Category Articles Text', 'echo-knowledge-base' ),
                'name'        => 'sidebar_show_sub_category_articles_msg',
                'max'         => '150',
                'min'         => '1',
                'type'        => EPKB_Input_Filter::TEXT,
                'default'     => esc_html__( 'Show Other Articles', 'echo-knowledge-base' )
            ),


			/******************************************************************************
			 *
			 *  CATEGORY ARCHIVE PAGE - V2
			 *
			 ******************************************************************************/

			'archive-show-sub-categories' => array(
				'label'       => esc_html__( 'Articles from Sub-Categories', 'echo-knowledge-base' ),
				'name'        => 'archive-show-sub-categories',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),

			// Archive Content v2 - Legacy old layout , new layout doesn't have version
			'archive-container-width-v2' => array(
				'label'       => esc_html__( 'Archive Container Width', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1080
			),
			'archive-container-width-units-v2' => array(
				'label'       => esc_html__( 'Archive Container Width Units', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'          => _x( '%',  'echo-knowledge-base' ),

				),
				'default'     => 'px'
			),
			'archive-content-width-v2' => array(
				'label'       => esc_html__( 'Width', 'echo-knowledge-base' ) . ' (%)',
				'name'        => 'archive-content-width-v2',
				'max'         => 100,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'archive-content-padding-v2' => array(
				'label'       => esc_html__( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-content-background-color-v2' => array(
				'label'       => esc_html__( 'Content Background', 'echo-knowledge-base' ),
				'name'        => 'archive-content-background-color-v2',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),

			// Archive Left Sidebar v2
			'archive-left-sidebar-width-v2' => array(
				'label'       => esc_html__( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-width-v2',
				'max'         => 80,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'archive-left-sidebar-padding-v2' => array(
				'label'       => esc_html__( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-left-sidebar-background-color-v2' => array(
				'label'       => esc_html__( 'Left Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-background-color-v2',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),

			// Archive Advanced v2
			'archive-mobile-break-point-v2' => array(
				'label'       => esc_html__( 'Small Screen Break point ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1000
			),


			/******************************************************************************
			 *
			 *  CATEGORY ARCHIVE PAGE - V3
			 *
			 ******************************************************************************/

			'archive_page_v3_toggle' => array(
				'label'       => esc_html__( 'Enable New Design and Features', 'echo-knowledge-base' ),
				'name'        => 'archive_page_v3_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),

			// ARCHIVE PAGE - HEADER
			'archive_search_toggle' => array(
				'label'       => esc_html__( 'Search', 'echo-knowledge-base' ),
				'name'        => 'archive_search_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive_header_desktop_width' => array(
				'label'       => esc_html__( 'Search Width', 'echo-knowledge-base' ),
				'name'        => 'archive_header_desktop_width',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 100
			),
			'archive_header_desktop_width_units' => array(
				'label'       => esc_html__( 'Width Units', 'echo-knowledge-base' ),
				'name'        => 'archive_header_desktop_width_units',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'         => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),
			'archive_category_name_prefix' => array(
				'label'       => esc_html__( 'Category Name Prefix', 'echo-knowledge-base' ),
				'name'        => 'archive_category_name_prefix',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Category - ', 'echo-knowledge-base' )
			),
			'archive_category_desc_toggle' => array(
				'label'       => esc_html__( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'archive_category_desc_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// ARCHIVE PAGE - SIDEBARS
			'archive_sidebar_navigation_type' => array(
				'label'       => esc_html__( 'Sidebar Navigation Type', 'echo-knowledge-base' ),
				'name'        => 'archive_sidebar_navigation_type',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'navigation-categories'        => esc_html__( 'Top Categories', 'echo-knowledge-base' ),  // Categories Focused Layout Navigation
					'navigation-current-category'  => esc_html__( 'Current Category and Articles', 'echo-knowledge-base' ), // current category and below
					'navigation-all-categories'    => esc_html__( 'All Categories and Articles', 'echo-knowledge-base' )
				),
				'default'     => 'navigation-current-category'
			),
			'archive_sidebar_background_color' => array(
				'label'       => esc_html__( 'Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive_sidebar_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'archive_left_sidebar_toggle' => array(
				'label'       => is_rtl() ? esc_html__( 'Right Sidebar' ) : esc_html__( 'Left Sidebar' ),
				'name'        => 'archive_left_sidebar_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive_right_sidebar_toggle' => array(
				'label'       => is_rtl() ? esc_html__( 'Left Sidebar' ) : esc_html__( 'Right Sidebar' ),
				'name'        => 'archive_right_sidebar_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'archive_left_sidebar_desktop_width' => array(
				'label'       => esc_html__( 'Left Sidebar Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive_left_sidebar_desktop_width',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 24
			),
			'archive_right_sidebar_desktop_width' => array(
				'label'       => esc_html__( 'Right Sidebar Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive_right_sidebar_desktop_width',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 24
			),
			// Left Sidebar - Position 1 - 3
			'archive-left-sidebar-position-1' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-left-sidebar-position-1',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					//'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					//'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'navigation'
			),
			// Right Sidebar - Position 1 - 3
			'archive-right-sidebar-position-1' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-right-sidebar-position-1',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					//'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					//'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'none'
			),

			// ARCHIVE PAGE - CONTENT
			'archive_content_desktop_width' => array(
				'label'       => esc_html__( 'Content Width', 'echo-knowledge-base' ),
				'name'        => 'archive_content_desktop_width',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1400
			),
			'archive_content_desktop_width_units' => array(
				'label'       => esc_html__( 'Width Units', 'echo-knowledge-base' ),
				'name'        => 'archive_content_desktop_width_units',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => 'px'
			),
			'archive_content_background_color' => array(
				'label'       => esc_html__( 'Content Area Background', 'echo-knowledge-base' ),
				'name'        => 'archive_content_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),

			// ARCHIVE PAGE - CONTENT - LIST OF ARTICLES
			'archive_content_articles_list_title' => array(
				'label'       => esc_html__( 'Articles Title', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_list_title',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Articles', 'echo-knowledge-base' )
			),
			'archive_content_articles_nof_articles_displayed' => array(
				'label'       => esc_html__( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_nof_articles_displayed',
				'max'         => '2000',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'archive_content_articles_display_mode' => array(
				'label'       => esc_html__( 'Article Content Mode', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_display_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'title'             => 'No Preview',
					'title_excerpt'     => 'Excerpt or Content Preview',
					'title_content'     => 'Content Preview',
				),
				'default'     => 'title'
			),
			'archive_content_articles_nof_columns' => array(
				'label'       => esc_html__( 'Columns of Articles', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( '1' => '1', '2' => '2', '3' => '3' ),
				'default'     => '2'
			),
			'archive_content_articles_separator_toggle' => array(
				'label'       => esc_html__( 'Separator', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_separator_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'archive_content_articles_arrow_toggle' => array(
				'label'       => esc_html__( 'Arrow', 'echo-knowledge-base' ),
				'name'        => 'archive_content_articles_arrow_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),

			// ARCHIVE PAGE - CONTENT - LIST OF SUB-CATEGORIES
			'archive_content_sub_categories_toggle' => array(
				'label'       => esc_html__( 'Show Sub Categories', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive_content_sub_categories_title' => array(
				'label'       => esc_html__( 'Sub Categories Title or Empty', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_title',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Sub Categories', 'echo-knowledge-base' )
			),
			'archive_content_sub_categories_nof_columns' => array(
				'label'       => esc_html__( 'Columns of Sub-categories', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( '1' => '1', '2' => '2', '3' => '3' ),
				'default'     => '2'
			),
			'archive_content_sub_categories_with_articles_toggle' => array(
				'label'       => esc_html__( 'Show Sub Category Articles', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_with_articles_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive_content_sub_categories_nof_articles_displayed' => array(
				'label'       => esc_html__( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_nof_articles_displayed',
				'max'         => '2000',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 3
			),
			'archive_content_sub_categories_icon_toggle' => array(
                'label'       => esc_html__( 'Category Icon', 'echo-knowledge-base' ),
                'name'        => 'archive_content_sub_categories_icon_toggle',
                'type'        => EPKB_Input_Filter::CHECKBOX,
                'default'     => 'off'
			),
			'archive_content_sub_categories_border_toggle' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_border_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive_content_sub_categories_background_color' => array(
				'label'       => esc_html__( 'Category Box Background', 'echo-knowledge-base' ),
				'name'        => 'archive_content_sub_categories_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),

			// ARCHIVE PAGE - TODO FUTURE Settings Position Settings
			/***
			'archive-left-sidebar-position-2' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-left-sidebar-position-2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'none'
			),
			'archive-left-sidebar-position-3' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-left-sidebar-position-3',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'none'
			),
			'archive-right-sidebar-position-2' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-right-sidebar-position-2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'popular_articles'
			),
			'archive-right-sidebar-position-3' => array(
				'label'       => esc_html__( 'Position', 'echo-knowledge-base' ) . '1',
				'name'        => 'archive-right-sidebar-position-3',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'                 => '-----',
					'navigation'           => esc_html__( 'Navigation',   'echo-knowledge-base' ),
					'popular_articles'     => esc_html__( 'Popular Articles',   'echo-knowledge-base' ),
					'recent_articles'      => esc_html__( 'Recent Articles',   'echo-knowledge-base' ),
				),
				'default'     => 'recent_articles'
			), */


			/******************************************************************************
			 *
			 *  CATEGORIES NAVIGATION SIDEBAR - Article Page, Category Archive Page
			 *
			 ******************************************************************************/
			'categories_layout_list_mode' => array(
				'label'       => esc_html__( 'Categories to Display', 'echo-knowledge-base' ),
				'name'        => 'categories_layout_list_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'list_top_categories' => esc_html__( 'Top Categories', 'echo-knowledge-base' ),
					'list_sibling_categories' => esc_html__( 'Article-Level Categories', 'echo-knowledge-base' ),
				),
				'default'     => 'list_top_categories'
			),
			'categories_box_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'categories_box_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'category_box_title_text_color' => array(
				'label'       => esc_html__( 'Title Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_title_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#666666'
			),
			'category_box_container_background_color' => array(
				'label'       => esc_html__( 'Container Background', 'echo-knowledge-base' ),
				'name'      => 'category_box_container_background_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'category_box_category_text_color' => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_category_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'category_box_count_background_color' => array(
				'label'       => esc_html__( 'Count Background', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_background_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'category_box_count_text_color' => array(
				'label'       => esc_html__( 'Count Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'category_box_count_border_color' => array(
				'label'       => esc_html__( 'Count Border', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_border_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			'category_focused_menu_heading_text' => array(
				'label'       => esc_html__( 'Categories Heading', 'echo-knowledge-base' ),
				'name'        => 'category_focused_menu_heading_text',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Categories', 'echo-knowledge-base' )
			),


			/******************************************************************************
			 *
			 *  KB TEMPLATE settings
			 *
			 ******************************************************************************/

			// TEMPLATES for Main Page
			'template_main_page_display_title' => array(
				'label'       => esc_html__( 'Display Page Title', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_display_title',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'template_main_page_padding_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_padding_top',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_main_page_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'template_main_page_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_main_page_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_main_page_margin_top' => array(
				'label'       => esc_html__( 'Margin Top', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_margin_top',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_main_page_margin_bottom' => array(
				'label'       => esc_html__( 'Margin Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_margin_bottom',
				'max'         => '500',
				'min'         => '-500',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'template_main_page_margin_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_margin_left',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_main_page_margin_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_margin_right',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),

			// TEMPLATES for Article Page
			'templates_for_kb_article_reset'            => array(
				'label'       => esc_html__( 'Article Content - Remove Theme Style', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_reset',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'templates_for_kb_article_defaults'         => array(
				'label'       => esc_html__( 'Article Content - Add KB Style', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'template_article_padding_top'      => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_top',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_bottom'   => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_left'     => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_left',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_right'    => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_right',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_top'       => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_top',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_bottom'    => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_bottom',
				'max'         => '500',
				'min'         => '-500',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'template_article_margin_left'      => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_left',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_right'     => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_right',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),

			// TEMPLATES for V2 Category Archive Page
			'template_category_archive_page_style' => array(
				'label'       => esc_html__( 'Pre-made Designs', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_page_style',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-category-archive-style-1' => esc_html__( 'Basic List', 'echo-knowledge-base' ),
					'eckb-category-archive-style-2' => esc_html__( 'Standard', 'echo-knowledge-base' ),
					'eckb-category-archive-style-3' => esc_html__( 'Standard 2', 'echo-knowledge-base' ),
					'eckb-category-archive-style-4' => esc_html__( 'Box', 'echo-knowledge-base' ),
					'eckb-category-archive-style-5' => esc_html__( 'Grid', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-category-archive-style-2'
			),
			'template_category_archive_page_heading_description' => array(
				'label'       => esc_html__( 'Category Name Prefix', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_page_heading_description',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Category - ', 'echo-knowledge-base' )
			),
			'template_category_archive_read_more' => array(
				'label'       => esc_html__( 'Read More', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_read_more',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Read More', 'echo-knowledge-base' )
			),
			'template_category_archive_date' => array(
				'label'       => esc_html__( 'Date Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_date',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Date:', 'echo-knowledge-base' )
			),
			'template_category_archive_author' => array(
				'label'       => esc_html__( 'Author Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_author',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'By:', 'echo-knowledge-base' )
			),
			'template_category_archive_categories' => array(
				'label'       => esc_html__( 'Categories Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_categories',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Categories:', 'echo-knowledge-base' )
			),
			'template_category_archive_date_on'         => array(
				'label'       => esc_html__( 'Date' ),
				'name'        => 'template_category_archive_date_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'template_category_archive_author_on'         => array(
				'label'       => esc_html__( 'Author' ),
				'name'        => 'template_category_archive_author_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'template_category_archive_categories_on'         => array(
				'label'       => esc_html__( 'Categories' ),
				'name'        => 'template_category_archive_categories_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/******************************************************************************
			 *
			 *  TOC
			 *
			 ******************************************************************************/
			'article_toc_enable' => array(
				'label'       => esc_html__( 'Table of Contents' ),
				'name'        => 'article_toc_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_toc_hx_level' => array(
				'label'       => esc_html__( 'From Hx', 'echo-knowledge-base' ),
				'name'        => 'article_toc_hx_level',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1' => esc_html__( 'H1', 'echo-knowledge-base' ),
					'2' => esc_html__( 'H2', 'echo-knowledge-base' ),
					'3' => esc_html__( 'H3', 'echo-knowledge-base' ),
					'4' => esc_html__( 'H4', 'echo-knowledge-base' ),
					'5' => esc_html__( 'H5', 'echo-knowledge-base' ),
					'6' => esc_html__( 'H6', 'echo-knowledge-base' ),
				),
				'default'     => '2'
			),
			'article_toc_hy_level' => array(
				'label'       => esc_html__( 'To Hy', 'echo-knowledge-base' ),
				'name'        => 'article_toc_hy_level',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'2' => esc_html__( 'H2', 'echo-knowledge-base' ),
					'3' => esc_html__( 'H3', 'echo-knowledge-base' ),
					'4' => esc_html__( 'H4', 'echo-knowledge-base' ),
					'5' => esc_html__( 'H5', 'echo-knowledge-base' ),
					'6' => esc_html__( 'H6', 'echo-knowledge-base' ),
				),
				'default'     => '6'
			),
			'article_toc_exclude_class' => array(
				'label'       => esc_html__( 'CSS Class to exclude headers from the TOC', 'echo-knowledge-base' ),
				'name'        => 'article_toc_exclude_class',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
			),
			'article_toc_active_bg_color' => array(
				'label'       => esc_html__( 'Active Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_bg_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#1e73be'
			),
			'article_toc_title_color' => array(
				'label'       => esc_html__( 'Title', 'echo-knowledge-base' ),
				'name'      => 'article_toc_title_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_text_color' => array(
				'label'       => esc_html__( 'Headings', 'echo-knowledge-base' ),
				'name'      => 'article_toc_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_active_text_color' => array(
				'label'       => esc_html__( 'Active Heading', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_toc_cursor_hover_bg_color' => array(
				'label'       => esc_html__( 'Hover: Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_bg_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e1ecf7'
			),
			'article_toc_cursor_hover_text_color' => array(
				'label'       => esc_html__( 'Hover: Text', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_text_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_toc_scroll_offset' => array(
				'label'       => esc_html__( 'Heading position is relative to the screen after scroll (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_scroll_offset',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '130'
			),
			'article_toc_border_mode' => array(
				'label'       => esc_html__( 'Border Style', 'echo-knowledge-base' ),
				'name'        => 'article_toc_border_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'   => esc_html__( 'None',   'echo-knowledge-base' ),
					'between'   => esc_html__( 'Between Article and TOC', 'echo-knowledge-base' ),
					'around'   => esc_html__( 'Around TOC', 'echo-knowledge-base' ),
				),
				'default'     => 'between'
			),
			'article_toc_border_color' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'      => 'article_toc_border_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_header_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_toc_header_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '15',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_toc_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_toc_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_toc_background_color' => array(
				'label'       => esc_html__( 'Container Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_background_color',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'article_toc_title' => array(
				'label'       => esc_html__( 'Title (optional)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_title',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Table of Contents', 'echo-knowledge-base' )
			),
			'article_toc_scroll_speed' => array(
				'label'       => esc_html__( 'Scroll Time', 'echo-knowledge-base' ),
				'name'        => 'article_toc_scroll_speed',
				'max'         => '5000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '300',
			),

			/******************************************************************************
			 *
			 *  ARTICLE CONTENT - zone - header rows
			 *
			 ******************************************************************************/
			'article_content_enable_rows'               => array(
				'label'       => esc_html__( 'Article Header Rows', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_content_enable_rows_1_gap'         => array(
				'label'       => esc_html__( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_1_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '8'
			),
			'article_content_enable_rows_1_alignment'   => array(
				'label'       => esc_html__( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_1_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'center'
			),
			'article_content_enable_rows_2_gap'         => array(
				'label'       => esc_html__( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_2_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_enable_rows_2_alignment'   => array(
				'label'       => esc_html__( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_2_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_3_gap'         => array(
				'label'       => esc_html__( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_3_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '32'
			),
			'article_content_enable_rows_3_alignment'   => array(
				'label'       => esc_html__( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_4_gap'         => array(
				'label'       => esc_html__( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_enable_rows_4_alignment'   => array(
				'label'       => esc_html__( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_5_gap'         => array(
				'label'       => esc_html__( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_5_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '100'
			),
			'article_content_enable_rows_5_alignment'   => array(
				'label'       => esc_html__( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_5_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'center'
			),


			/******************************************************************************
			 *
			 *  Article Title
			 *
			 ******************************************************************************/
			'article_content_enable_article_title'      => array(
				'label'       => esc_html__( 'Article Title', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_article_title',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_title_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => EPKB_Typography::$typography_defaults
			),
			'article_title_row'                         => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'article_title_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'article_title_alignment'                   => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_title_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'article_title_sequence'                    => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_title_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),

			/******************************************************************************
			 *
			 *  Back Navigation
			 *
			 ******************************************************************************/
			'article_content_enable_back_navigation'    => array(
				'label'       => esc_html__( 'Back Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_back_navigation',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'back_navigation_row'           => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'back_navigation_alignment'     => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'back_navigation_sequence'      => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'back_navigation_mode'          => array(
				'label'       => esc_html__( 'Navigation Mode', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'navigate_browser_back'   => esc_html__( 'Browser Go Back Action',   'echo-knowledge-base' ),
					'navigate_kb_main_page'   => esc_html__( 'Redirect to KB Main Page', 'echo-knowledge-base' ),
				),
				'default'     => 'navigate_browser_back'
			),
			'back_navigation_text'          => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '< ' . esc_html__( 'All Topics', 'echo-knowledge-base' )
			),
			'back_navigation_text_color'    => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'back_navigation_bg_color'      => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_bg_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'back_navigation_border_color'  => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b5b5b5'
			),
			'back_navigation_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'back_navigation_border'        => array(
				'label'       => esc_html__( 'Button Border', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'    => esc_html__( '-- No Border --', 'echo-knowledge-base' ),
					'solid'   => esc_html__( 'Solid', 'echo-knowledge-base' ),
				),
				'default'     => 'solid'
			),
			'back_navigation_border_radius' => array(
				'label'       => esc_html__( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_radius',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '3'
			),
			'back_navigation_border_width'  => array(
				'label'       => esc_html__( 'Border Thickness', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_width',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '1'
			),
			'back_navigation_margin_top'    => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_top',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_bottom',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_left'   => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_left',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_right'  => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_right',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '15'
			),
			'back_navigation_padding_top'   => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '5'
			),
			'back_navigation_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '5'
			),
			'back_navigation_padding_left'  => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'back_navigation_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),

			// OTHER
			'meta-data-header-toggle' => array(  // OLD article content header
				'label'       => esc_html__( 'Header Meta Data', 'echo-knowledge-base' ),
				'name'        => 'meta-data-header-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'meta-data-footer-toggle' => array(  // current meta data footer
				'label'       => esc_html__( 'Meta Data at the Bottom', 'echo-knowledge-base' ),
				'name'        => 'meta-data-footer-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/******************************************************************************
			 *
			 *  Author
			 *
			 ******************************************************************************/
			'article_content_enable_author' => array(
				'label'       => esc_html__( 'Author', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_author',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'author_footer_toggle'          => array(
				'label'       => esc_html__( 'Author' ),
				'name'        => 'author_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'author_text'                   => array(
				'label'       => esc_html__( 'Author Text', 'echo-knowledge-base' ),
				'name'        => 'author_text',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'By', 'echo-knowledge-base' )
			),
			'author_row'                    => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'author_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'author_alignment'              => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'author_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'author_sequence'               => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'author_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'author_icon_on'                => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'author_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => esc_html__( 'Show icon', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Created Date
			 *
			 ******************************************************************************/
			'article_content_enable_created_date' => array(
				'label'       => esc_html__( 'Created Date', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_created_date',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'created_on_footer_toggle'      => array(
				'label'       => esc_html__( 'Created On', 'echo-knowledge-base' ),
				'name'        => 'created_on_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'created_on_text'               => array(
				'label'       => esc_html__( 'Created Date Prefix', 'echo-knowledge-base' ),
				'name'        => 'created_on_text',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Posted', 'echo-knowledge-base' )
			),
			'created_date_row'              => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'created_date_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'created_date_alignment'        => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'created_date_sequence'         => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'created_date_icon_on'          => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'created_date_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => esc_html__( 'Show icon', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Last Updated Date
			 *
			 ******************************************************************************/
			'article_content_enable_last_updated_date'  => array(
				'label'       => esc_html__( 'Last Updated Date', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_last_updated_date',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'last_updated_on_footer_toggle' => array(
				'label'       => esc_html__( 'Last Updated On', 'echo-knowledge-base' ),
				'name'        => 'last_updated_on_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'last_updated_on_text'          => array(
				'label'       => esc_html__( 'Updated Date Prefix', 'echo-knowledge-base' ),
				'name'        => 'last_updated_on_text',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Updated', 'echo-knowledge-base' )
			),
			'last_updated_date_row'         => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'last_updated_date_alignment'   => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'last_updated_date_sequence'    => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'last_updated_date_icon_on'     => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => esc_html__( 'Show icon', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Breadcrumb
			 *
			 ******************************************************************************/
			'breadcrumb_enable'  => array(
				'label'       => esc_html__( 'Breadcrumb', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'breadcrumb_row'                => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'breadcrumb_alignment'          => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'breadcrumb_sequence'           => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'breadcrumb_icon_separator'     => array(
				'label'       => esc_html__( 'Breadcrumb Separator', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_icon_separator',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'ep_font_icon_none'    => esc_html__( '-- No Icon --',   'echo-knowledge-base' ),
					'ep_font_icon_right_arrow'   => esc_html__( 'Right Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_left_arrow'    => esc_html__( 'Left Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right_circle'    => esc_html__( 'Arrow Right Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left_circle'    => esc_html__( 'Arrow Left Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left'    => esc_html__( 'Arrow Caret Left',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right'    => esc_html__( 'Arrow Caret Right',   'echo-knowledge-base' ),
				),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),
			'breadcrumb_padding_top'        => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_padding_bottom'     => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_padding_left'       => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'breadcrumb_padding_right'      => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'breadcrumb_margin_top'         => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_top',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_bottom'      => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_left'        => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_left',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_right'       => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_right',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_text_color'         => array(
				'label'       => esc_html__( 'Breadcrumb Text', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'breadcrumb_description_text'   => array(
				'label'       => esc_html__( 'Breadcrumb Label', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_description_text',
				'max'         => '70',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
			),
			'breadcrumb_home_text'          => array(
				'label'       => esc_html__( 'Breadcrumb Home Text', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_home_text',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Main', 'echo-knowledge-base' )
			),
			'breadcrumb_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),


			/******************************************************************************
			 *
			 *  ARTICLE CONTENT TOOLBAR - zone
			 *
			 ******************************************************************************/
			'article_content_toolbar_enable'                => array(
				'label'       => esc_html__( 'Content Toolbar', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_content_toolbar_row'                   => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'article_content_toolbar_alignment'             => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'right'
			),
			'article_content_toolbar_sequence'              => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'article_content_toolbar_button_background'     => array(
				'label'       => esc_html__( 'Button Background', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_background',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_button_background_hover' => array(
				'label'       => esc_html__( 'Button Background Hover', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_background_hover',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_button_format'         => array(
				'label'       => esc_html__( 'Button Format', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_format',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'icon'      => _x( 'Icon', 'echo-knowledge-base' ),
					'text'      => _x( 'Text', 'echo-knowledge-base' ),
					'icon_text' => _x( 'Icon and Text', 'echo-knowledge-base' ),
					'text_icon' => _x( 'Text and Icon', 'echo-knowledge-base' ) ),
				'default'     => 'text_icon'
			),
			'article_content_toolbar_icon_size'             => array(
				'label'       => esc_html__( 'Icon Size (px)', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_size',
				'max'         => '50',
				'min'         => '12',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '20'
			),
			'article_content_toolbar_icon_color'            => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_icon_hover_color'      => array(
				'label'       => esc_html__( 'Icon Hover', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_hover_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_border_color'          => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_border_radius'         => array(
				'label'       => esc_html__( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_content_toolbar_border_width'          => array(
				'label'       => esc_html__( 'Border Thickness', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_content_toolbar_text_size'             => array(
				'label'       => esc_html__( 'Text Size (px)', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_text_size',
				'max'         => '30',
				'min'         => '12',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '15'
			),
			'article_content_toolbar_text_color'            => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_text_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_text_hover_color'      => array(
				'label'       => esc_html__( 'Text Hover', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_hover_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_button_padding_top'    => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_left'   => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_left',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_right'  => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_right',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_margin_top'     => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_top',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_bottom'  => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_bottom',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_left'    => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_left',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_right'   => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_right',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),


			/******************************************************************************
			 *
			 *  Print Button
			 *
			 ******************************************************************************/
			'print_button_enable'                           => array(
				'label'       => esc_html__( 'Print Button', 'echo-knowledge-base' ),
				'name'        => 'print_button_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'print_button_text'                             => array(
				'label'       => esc_html__( 'Print Text', 'echo-knowledge-base' ),
				'name'        => 'print_button_text',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Print', 'echo-knowledge-base' )
			),
			'print_button_doc_padding_top'                  => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_bottom'               => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_left'                 => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_left',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_right'                => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_right',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),


			/******  PREV/NEXT NAVIGATION  ******/
			'prev_next_navigation_enable' => array(
				'label'       => esc_html__( 'Prev/Next Navigation', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'next_navigation_text' => array(
				'label'       => esc_html__( 'Next Text', 'echo-knowledge-base' ),
				'name'        => 'next_navigation_text',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     =>  esc_html__( 'Next', 'echo-knowledge-base' )
			),
			'prev_navigation_text' => array(
				'label'       => esc_html__( 'Previous Text', 'echo-knowledge-base' ),
				'name'        => 'prev_navigation_text',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     =>  esc_html__( 'Previous', 'echo-knowledge-base' )
			),
			'prev_next_navigation_text_color' => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#1e73be'
			),
			'prev_next_navigation_bg_color' => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_bg_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7f7f7'
			),
			'prev_next_navigation_hover_text_color' => array(
				'label'       => esc_html__( 'Hover: Text', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_hover_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#6d6d6d'
			),
			'prev_next_navigation_hover_bg_color' => array(
				'label'       => esc_html__( 'Hover: Background', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_hover_bg_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#dee3e5'
			),

			/******  ARTICLE VIEWS COUNTER  ******/
			'article_views_counter_enable' => array(        // feature toggle
				'label'       => esc_html__( 'Count Article Views', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'article_views_counter_method'         => array(
				'label'       => esc_html__( 'Views Counter Method', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_method',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'delay'   => _x( 'Delay', 'echo-knowledge-base' ),
					'scroll'  => _x( 'After User Scroll', 'echo-knowledge-base' ),
					'php'     => _x( 'PHP', 'echo-knowledge-base' ) ),
				'default'     => 'delay'
			),
			'article_content_enable_views_counter'  => array(   // header toggle
				'label'       => esc_html__( 'Display in Article Header', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_views_counter',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'article_views_counter_text'          => array(
				'label'       => esc_html__( 'Article Views Prefix', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_text',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Views', 'echo-knowledge-base' )
			),
			'article_views_counter_row'         => array(
				'label'       => esc_html__( 'Row', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'article_views_counter_alignment'   => array(
				'label'       => esc_html__( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'article_views_counter_sequence'    => array(
				'label'       => esc_html__( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '5'
			),
			'article_views_counter_icon_on'     => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'article_views_counter_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => esc_html__( 'Show icon', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			// old Article Content Header
			'article_meta_icon_on' => array(
				'label'       => esc_html__( 'Article Meta Icon', 'echo-knowledge-base' ),
				'name'        => 'article_meta_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => esc_html__( 'Show icon', 'echo-knowledge-base' ),
					'off'    => esc_html__( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),
			'breadcrumb_margin_bottom_old'      => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom_old',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),/* option postponed
            'date_format' => array(
                'label'       => esc_html__( 'Date Format', 'echo-knowledge-base' ),
                'name'        => 'date_format',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'F j, Y'    => esc_html__( 'January 1, 2020', 'echo-knowledge-base' ),
                    'M j, Y'    => esc_html__( 'Jan 1, 2020', 'echo-knowledge-base' ),
                    'j F Y'    => esc_html__( '1 January 2020', 'echo-knowledge-base' ),
                    'j M Y'    => esc_html__( '1 Jan 2020', 'echo-knowledge-base' ),
                    'm/d/Y'    => esc_html__( '01/30/2020', 'echo-knowledge-base' ),
                    'Y/m/d'    => esc_html__( '2020/01/30', 'echo-knowledge-base' ),
                ),
                'default'     => 'M j, Y'
            ), */


			/******************************************************************************
			 *
			 *  Admin UI Access - CONTEXTs
			 *
			 ******************************************************************************/

			// Access to visual Editor (write)
			'admin_eckb_access_frontend_editor_write' => array(
				'label'       => esc_html__( 'Edit KB colors, fonts, labels and features', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_frontend_editor_write',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY
			),

			// Access to Order Articles (write)
			'admin_eckb_access_order_articles_write' => array(
				'label'       => esc_html__( 'Order Articles and Categories', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_order_articles_write',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY
			),

			// Access to KB Analytics (read)
			'admin_eckb_access_search_analytics_read' => array(
				'label'       => esc_html__( 'KB Analytics', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_search_analytics_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			// Access to Get Started (read)
			'admin_eckb_access_need_help_read' => array(
				'label'       => esc_html__( 'Get Started', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_need_help_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			// Access to Add-ons / News (read)
			'admin_eckb_access_addons_news_read' => array(
				'label'       => esc_html__( 'Add-ons / News', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_addons_news_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			// Access to FAQs
			'admin_eckb_access_faqs_write' => array(
				'label'       => esc_html__( 'FAQs', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_faqs_write',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			/******************************************************************************
			 *
			 *  Shortcodes
			 *
			 ******************************************************************************/

			'faq_shortcode_content_mode' => array(
				'label'       => esc_html__( 'Content Mode', 'echo-knowledge-base' ),
				'name'        => 'faq_shortcode_content_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'content'    => esc_html__( 'Content', 'echo-knowledge-base' ),
					'excerpt'    => esc_html__( 'Excerpt', 'echo-knowledge-base' )
				),
				'default'     => 'content'
			),
			'faq_schema_toggle'                 => array(
				'label'       => esc_html__( 'FAQs Schema', 'echo-knowledge-base' ),
				'name'        => 'faq_schema_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/******************************************************************************
			 *
			 *  FAQ Module / Shortcode
			 *
			 ******************************************************************************/

			'faq_border_style' => array(
				'label'       => esc_html__( 'Border Style', 'echo-knowledge-base' ),
				'name'        => 'faq_border_style',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'sharp'      => esc_html__( 'Sharp', 'echo-knowledge-base' ),
					'rounded'    => esc_html__( 'Rounded', 'echo-knowledge-base' ),
					//'thick'      => esc_html__( 'Thick', 'echo-knowledge-base' )
				),
				'default'     => 'rounded'
			),
			'faq_border_mode' => array(
				'label'       => esc_html__( 'Border Mode', 'echo-knowledge-base' ),
				'name'        => 'faq_border_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'          => esc_html__( 'None', 'echo-knowledge-base' ),
					'all_around'    => esc_html__( 'All Around', 'echo-knowledge-base' ),
					'separator'     => esc_html__( 'Separator', 'echo-knowledge-base' )
				),
				'default'     => 'all_around'
			),
			'faq_compact_mode' => array(
				'label'       => esc_html__( 'Compact Mode', 'echo-knowledge-base' ),
				'name'        => 'faq_compact_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'compact_small'            => esc_html__( 'Small', 'echo-knowledge-base' ),
					'compact_medium'           => esc_html__( 'Medium', 'echo-knowledge-base' ),
				),
				'default'     => 'compact_medium'
			),
			'faq_open_mode' => array(
				'label'       => esc_html__( 'Open Mode', 'echo-knowledge-base' ),
				'name'        => 'faq_open_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'accordion_mode'   => esc_html__( 'Accordion - Collapsed Articles', 'echo-knowledge-base' ),
					'toggle_mode'      => esc_html__( 'Toggle - Show one Article at a time', 'echo-knowledge-base' ),
					'show_all_mode'    => esc_html__( 'Show All - Show all Articles', 'echo-knowledge-base' ),
				),
				'default'     => 'accordion_mode'
			),
			'faq_question_space_between' => array(
				'label'       => esc_html__( 'Space Between Questions', 'echo-knowledge-base' ),
				'name'        => 'faq_question_space_between',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'space_none'     => esc_html__( 'None', 'echo-knowledge-base' ),
					'space_small'    => esc_html__( 'Small', 'echo-knowledge-base' ),
					'space_medium'   => esc_html__( 'Medium', 'echo-knowledge-base' ),
				),
				'default'     => 'space_medium'
			),
			'faq_icon_location' => array(
				'label'       => esc_html__( 'Icons Location', 'echo-knowledge-base' ),
				'name'        => 'faq_icon_location',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_icons'      => esc_html__( 'No Icons', 'echo-knowledge-base' ),
					'left'          => is_rtl() ? esc_html__( 'Start', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
					'right'         => is_rtl() ? esc_html__( 'End', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'faq_icon_type' => array(
				'label'       => esc_html__( 'Icon to Expand/Collapse FAQs', 'echo-knowledge-base' ),
				'name'        => 'faq_icon_type',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'icon_plus_box'                             => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
					'icon_plus_circle'                          => _x( 'Plus circle', 'icon type', 'echo-knowledge-base' ),
					'icon_plus'                                 => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
					'icon_arrow_caret'                          => _x( 'Arrow Down Caret', 'icon type', 'echo-knowledge-base' ),
					'icon_arrow_angle'                          => _x( 'Arrow Right Angle', 'icon type', 'echo-knowledge-base' ),
				),
				'default'     => 'icon_arrow_caret'
			),
			'faq_nof_columns' => array(
				'label'       => esc_html__( 'Number of Columns', 'echo-knowledge-base' ),
				'name'        => 'faq_nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( '1' => '1', '2' => '2' ),
				'default'     => '1'
			),
			'faq_empty_msg' => array(
				'label'       => esc_html__( 'Empty FAQs Message', 'echo-knowledge-base' ),
				'name'        => 'faq_empty_msg',
				'max'         => '150',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'FAQs coming soon', 'echo-knowledge-base' )
			),
			'faq_icon_color' => array(
				'label'       => esc_html__( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'faq_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'faq_border_color' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'faq_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e8e8e8'
			),
			'faq_question_background_color' => array(
				'label'       => esc_html__( 'Question Background', 'echo-knowledge-base' ),
				'name'        => 'faq_question_background_color',
				'max'         => '7',
				'min'         => '7',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'faq_answer_background_color' => array(
				'label'       => esc_html__( 'Answer Background', 'echo-knowledge-base' ),
				'name'        => 'faq_answer_background_color',
				'max'         => '7',
				'min'         => '7',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'faq_question_text_color' => array(
				'label'       => esc_html__( 'Question Text', 'echo-knowledge-base' ),
				'name'        => 'faq_question_text_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
		);

		// add CORE LAYOUTS SHARED configuration
		$config_specification = array_merge( $config_specification, self::shared_configuration() );

		// add CORE LAYOUTS non-shared configuration
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Basic::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Tabs::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Categories::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Modular::get_fields_specification() );    // TODO: move settings to Classic and Drill Down classes and remove Modular class usage
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Classic::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Drill_Down::get_fields_specification() );

		self::$cached_specs[$kb_id] = $config_specification;

		return self::$cached_specs[$kb_id];
	}

	/**
	 * Shared STYLE, COLOR and TEXT configuration between CORE LAYOUTS
	 *
	 * @return array
	 */
	public static function shared_configuration() {

		/**
		 * Layout/color settings shared among layouts and color sets are listed here.
		 * If a setting becomes unique to color/layout, move it to its file.
		 * If a setting becomes common, move it from its file to this file.
		 */
		$shared_specification = array(

			/******************************************************************************
			 *
			 *  KB Main Layout - Layout and Style
			 *
			 ******************************************************************************/

			/***  KB Main Page -> General ***/

			'width' => array(       // Not Modular Main Page; used also by Advanced Search box width; see ::width
				'label'       => esc_html__( 'Search Box Width', 'echo-knowledge-base' ),
				'name'        => 'width',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-boxed' => esc_html__( 'Boxed Width', 'echo-knowledge-base' ),
					'epkb-full' => esc_html__( 'Full Width', 'echo-knowledge-base' ) ),
				'default'     => 'epkb-full'
			),
			'general_typography' => array(
				'label'       => esc_html__( 'Font Family', 'echo-knowledge-base' ),
				'name'        => 'general_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => EPKB_Typography::$typography_defaults
			),
			'section_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'section_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'show_articles_before_categories' => array(         // shown on Order Articles and Categories page
				'label'       => esc_html__( 'Show Articles', 'echo-knowledge-base' ),
				'name'        => 'show_articles_before_categories',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => esc_html__( 'Before Categories', 'echo-knowledge-base' ),
					'off' => esc_html__( 'After Categories', 'echo-knowledge-base' ),
					),
				'default'     => 'on'
			),
			'nof_columns' => array(
				'label'       => esc_html__( 'Number of Columns', 'echo-knowledge-base' ),
				'name'        => 'nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'one-col' => '1', 'two-col' => '2', 'three-col' => '3', 'four-col' => '4' ),
				'default'     => 'three-col'
			),
			'nof_articles_displayed' => array(
				'label'       => esc_html__( 'Number of Articles Displayed', 'echo-knowledge-base' ),
				'name'        => 'nof_articles_displayed',
				'max'         => '2000',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'expand_articles_icon' => array(
				'label'       => esc_html__( 'Icon to Expand/Collapse Articles', 'echo-knowledge-base' ),
				'name'        => 'expand_articles_icon',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' ) ),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),


			/***  KB Main Page -> Search Box ***/

			'search_layout' => array(           // deprecated: used for non-modular search box (modular has ml_search_layout )
				'label'       => esc_html__( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'search_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-search-form-1' => esc_html__( 'Rounded search button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-4' => esc_html__( 'Squared search Button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-2' => esc_html__( 'Search button is below', 'echo-knowledge-base' ),
					'epkb-search-form-3' => esc_html__( 'No search button', 'echo-knowledge-base' ),
					'epkb-search-form-0' => esc_html__( 'No search box', 'echo-knowledge-base' )
				),
				'default'     => 'epkb-search-form-1'
			),
			'search_title_html_tag' => array(
				'label'       => esc_html__( 'Search Title HTML Tag', 'echo-knowledge-base' ),
				'name'        => 'search_title_html_tag',
				'type'        => EPKB_Input_Filter::SELECTION,
				'default'     => 'div',
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
			'search_title_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'search_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '36',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'search_input_border_width' => array(
				'label'       => esc_html__( 'Border (px)', 'echo-knowledge-base' ),
				'name'        => 'search_input_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'search_input_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'search_input_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'search_box_padding_top' => array(
				'label'       => esc_html__( 'Padding Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_top',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'search_box_padding_bottom' => array(
				'label'       => esc_html__( 'Padding Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 50
			),
			'search_box_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_margin_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_top',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_margin_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_bottom',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'search_box_input_width' => array(
				'label'       => esc_html__( 'Search Input Width', 'echo-knowledge-base' ) . ' (%)',
				'name'        => 'search_box_input_width',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'search_box_input_height' => array(     // used for Advanced Search and Article Pages as well
				'label'       => esc_html__( 'Search Input Height', 'echo-knowledge-base' ),
				'name'        => 'search_box_input_height',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'narrow' => esc_html__( 'Narrow', 'echo-knowledge-base' ),
					'medium' => esc_html__( 'Medium', 'echo-knowledge-base' ),
					'large' => esc_html__( 'Large', 'echo-knowledge-base' )  ),
				'default'     => 'large'
			),
			'search_box_results_style' => array(
				'label'       => esc_html__( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'search_box_results_style',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'search_result_mode' => array(
				'label'       => esc_html__( 'Search Results Mode', 'echo-knowledge-base' ),
				'name'        => 'search_result_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
					'title_excerpt' => esc_html__( 'Title and Excerpt', 'echo-knowledge-base' )
				),
				'default'     => 'title'
			),

			/***  KB Article Page -> Search Box ***/

			'article_search_toggle' => array(       // turn search on or off (non-modular/modular/Advanced Search)
				'label'       => esc_html__( 'Search', 'echo-knowledge-base' ),
				'name'        => 'article_search_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_search_sync_toggle' => array(
				'label'       => esc_html__( 'Use Main Page Search Settings', 'echo-knowledge-base' ),
				'name'        => 'article_search_sync_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_search_layout' => array(       // deprecated: used for non-modular search box (modular has ml_article_search_layout )
				'label'       => esc_html__( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'article_search_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-search-form-1' => esc_html__( 'Rounded search button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-4' => esc_html__( 'Squared search Button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-2' => esc_html__( 'Search button is below', 'echo-knowledge-base' ),
					'epkb-search-form-3' => esc_html__( 'No search button', 'echo-knowledge-base' ),
					'epkb-search-form-0' => esc_html__( 'No search box', 'echo-knowledge-base' )        // TODO REMOVE
				),
				'default'     => 'epkb-search-form-1'
			),
			'article_search_title_html_tag' => array(
				'label'       => esc_html__( 'Search Title Html Tag', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_html_tag',
				'type'        => EPKB_Input_Filter::SELECTION,
				'default'     => 'div',
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
			'article_search_title_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '36',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_search_input_border_width' => array(
				'label'       => esc_html__( 'Border (px)', 'echo-knowledge-base' ),
				'name'        => 'article_search_input_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'article_search_input_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_search_input_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_search_box_padding_top' => array(
				'label'       => esc_html__( 'Padding Top', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_top',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article_search_box_padding_bottom' => array(
				'label'       => esc_html__( 'Padding Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article_search_box_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_margin_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_margin_top',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_margin_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_margin_bottom',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'article_search_box_input_width' => array(
				'label'       => esc_html__( 'Search Input Width', 'echo-knowledge-base' ) . ' (%)',
				'name'        => 'article_search_box_input_width',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'article_search_box_results_style' => array(
				'label'       => esc_html__( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_results_style',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'article_search_result_mode' => array(
				'label'       => esc_html__( 'Search Results Mode', 'echo-knowledge-base' ),
				'name'        => 'article_search_result_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'title' => esc_html__( 'Title', 'echo-knowledge-base' ),
					'title_excerpt' => esc_html__( 'Title and Excerpt', 'echo-knowledge-base' )
				),
				'default'     => 'title'
			),


			/***   Categories Box   ***/

			// Section Box
			'section_box_height_mode' => array(
				'label'       => esc_html__( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'section_box_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_no_height' => esc_html__( 'Variable', 'echo-knowledge-base' ),
					'section_min_height' => esc_html__( 'Minimum', 'echo-knowledge-base' ),
					'section_fixed_height' => esc_html__( 'Maximum', 'echo-knowledge-base' )  ),
				'default'     => 'section_min_height'
			),
			'section_box_shadow' => array(
				'label'       => esc_html__( 'Article List Shadow', 'echo-knowledge-base' ),
				'name'        => 'section_box_shadow',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => esc_html__( 'No Shadow', 'echo-knowledge-base' ),
					'section_light_shadow' => esc_html__( 'Light', 'echo-knowledge-base' ),
					'section_medium_shadow' => esc_html__( 'Medium', 'echo-knowledge-base' ),
					'section_bottom_shadow' => esc_html__( 'Bottom', 'echo-knowledge-base' )
				),
				'default'     => 'no_shadow'
			),
			'section_border_radius' => array(
				'label'       => esc_html__( 'Corner Radius', 'echo-knowledge-base' ),
				'name'        => 'section_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_border_width' => array(
				'label'       => esc_html__( 'Border Thickness', 'echo-knowledge-base' ),
				'name'        => 'section_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'section_body_height' => array(
				'label'       => esc_html__( 'Height', 'echo-knowledge-base' ),
				'name'        => 'section_body_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 150
			),
			'section_body_padding_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_top',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_body_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_bottom',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_body_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'section_body_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),

			// Section Head
			'section_head_alignment' => array(
				'label'       => esc_html__( 'Category Name Alignment', 'echo-knowledge-base' ),
				'name'        => 'section_head_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left' => is_rtl() ? esc_html__( 'Right', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
					'center' => esc_html__( 'Centered', 'echo-knowledge-base' ),
					'right' => is_rtl() ? esc_html__( 'Left', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'section_head_category_icon_location' => array(
				'label'       => esc_html__( 'Icon Location', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_location',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_icons'      => esc_html__( 'No Icons', 'echo-knowledge-base' ),
					'top'           => esc_html__( 'Top',   'echo-knowledge-base' ),
					'left'          => is_rtl() ? esc_html__( 'Start', 'echo-knowledge-base' ) : esc_html__( 'Left', 'echo-knowledge-base' ),
					'right'         => is_rtl() ? esc_html__( 'End', 'echo-knowledge-base' ) : esc_html__( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'section_head_category_icon_size' => array(
				'label'       => esc_html__( 'Icon Size ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_size',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '21'
			),
			'section_head_padding_top' => array(
				'label'       => esc_html__( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'section_head_padding_bottom' => array(
				'label'       => esc_html__( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'section_head_padding_left' => array(
				'label'       => esc_html__( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_head_padding_right' => array(
				'label'       => esc_html__( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_divider' => array(
				'label'       => esc_html__( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'section_divider_thickness' => array(
				'label'       => esc_html__( 'Divider Thickness ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_divider_thickness',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5
			),

			// Section Body
			'section_desc_text_on' => array(
				'label'       => esc_html__( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'section_desc_text_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'section_hyperlink_text_on' => array(   // Grid Layout only
				'label'       => esc_html__( 'Click on Category', 'echo-knowledge-base' ),
				'name'        => 'section_hyperlink_text_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => esc_html__( 'Go to Category Archive Page', 'echo-knowledge-base' ),
					'off' => esc_html__( 'Go to the first Article', 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),
			'section_hyperlink_on' => array(   // Basic, Tabs and Categories Layouts
				'label'       => esc_html__( 'Category Link to Archive page', 'echo-knowledge-base' ),
				'name'        => 'section_hyperlink_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Section Articles
			'section_article_underline' => array(       // NOT USED
				'label'       => esc_html__( 'Article Underline Hover', 'echo-knowledge-base' ),
				'name'        => 'section_article_underline',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_list_margin' => array(
				'label'       => esc_html__( 'Left offset for Articles List', 'echo-knowledge-base' ),
				'name'        => 'article_list_margin',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'sub_article_list_margin' => array(
				'label'       => esc_html__( 'Left offset for Sub Articles List', 'echo-knowledge-base' ),
				'name'        => 'sub_article_list_margin',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),


			/******************************************************************************
			 *
			 *  KB Main Colors - All Colors Settings
			 *
			 ******************************************************************************/

			/***  Main Page Search Box COLORS ***/
			'search_title_font_color' => array(
				'label'       => esc_html__( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'search_title_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'search_background_color' => array(
				'label'       => esc_html__( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'search_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'search_text_input_background_color' => array(
				'label'       => esc_html__( 'Input Background', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'search_text_input_border_color' => array(
				'label'       => esc_html__( 'Input Border', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			'search_btn_background_color' => array(
				'label'       => esc_html__( 'Button Background', 'echo-knowledge-base' ),
				'name'        => 'search_btn_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'search_btn_border_color' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_btn_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),

			/***  Article Page Search Box COLORS ***/
			'article_search_title_font_color' => array(
				'label'       => esc_html__( 'Title', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article_search_background_color' => array(
				'label'       => esc_html__( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'article_search_text_input_background_color' => array(
				'label'       => esc_html__( 'Input Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_text_input_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article_search_text_input_border_color' => array(
				'label'       => esc_html__( 'Input Border', 'echo-knowledge-base' ),
				'name'        => 'article_search_text_input_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			'article_search_btn_background_color' => array(
				'label'       => esc_html__( 'Button Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_btn_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'article_search_btn_border_color' => array(
				'label'       => esc_html__( 'Button Border', 'echo-knowledge-base' ),
				'name'        => 'article_search_btn_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),

			/***  Content ***/
			'background_color' => array(
				'label'       => esc_html__( 'Container Background', 'echo-knowledge-base' ),
				'name'        => 'background_color',
				'max'         => '7',
				'min'         => '7',
				'mandatory'   => false,     /* intentional due to padding spacing */
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     =>  ''
			),

			/***  List of Articles (Main Page, Modules ) COLORS ***/
			'article_typography' => array(
				'label'       => esc_html__( 'Article List Typography', 'echo-knowledge-base' ),
				'name'        => 'article_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_font_color' => array(
				'label'       => esc_html__( 'Article Title', 'echo-knowledge-base' ),
				'name'        => 'article_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#202124'
			),
			'article_icon_color' => array(
				'label'       => esc_html__( 'Article Icon', 'echo-knowledge-base' ),
				'name'        => 'article_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#202124'
			),
			'article_icon_toggle' => array(
				'label'       => esc_html__( 'Article Icon', 'echo-knowledge-base' ),
				'name'        => 'article_icon_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'section_body_background_color' => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_body_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'section_border_color' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'section_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F7F7F7'
			),

			/***  Categories COLORS ***/
			'section_head_font_color' => array(
				'label'       => esc_html__( 'Category Name', 'echo-knowledge-base' ),
				'name'        => 'section_head_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'section_head_background_color' => array(
				'label'       => esc_html__( 'Category Name Background', 'echo-knowledge-base' ),
				'name'        => 'section_head_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'section_head_description_font_color' => array(
				'label'       => esc_html__( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'section_divider_color' => array(
				'label'       => esc_html__( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#edf2f6'
			),
			'section_category_font_color' => array(
				'label'       => esc_html__( 'Subcategory Name', 'echo-knowledge-base' ),
				'name'        => 'section_category_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'section_category_icon_color' => array(
				'label'       => esc_html__( 'Subcategory Expand Icon', 'echo-knowledge-base' ),
				'name'        => 'section_category_icon_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'section_box_expand_hover_color' => array(
				'label'       => esc_html__( 'Category Expand Hover', 'echo-knowledge-base' ),
				'name'        => 'section_box_expand_hover_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'section_head_category_icon_color' => array(
				'label'             => esc_html__( 'Category Icon', 'echo-knowledge-base' ),
				'name'              => 'section_head_category_icon_color',
				'max'               => '7',
				'min'               => '7',
				'type'              => EPKB_Input_Filter::COLOR_HEX,
				'default'           => '#f7941d'
			),
			'section_head_typography' => array(
				'label'       => esc_html__( 'Name Typography', 'echo-knowledge-base' ),
				'name'        => 'section_head_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
								'font-family'       => '',
								'font-size'         => '21',
								'font-size-units'   => 'px',
								'font-weight'       => '',
				)
			),
			'section_head_description_typography' => array(
				'label'       => esc_html__( 'Description Typography', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family'       => '',
					'font-size'         => '14',
					'font-size-units'   => 'px',
					'font-weight'       => '',
				)
			),


			/******************************************************************************
			 *
			 *  Front-End Text
			 *
			 ******************************************************************************/

			/***   Search  ***/

			'search_title' => array(
				'label'       => esc_html__( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'search_title',
				'max'         => '100',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'How Can We Help?', 'echo-knowledge-base' )
			),
			'search_box_hint' => array(
				'label'       => esc_html__( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'search_box_hint',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'search_button_name' => array(
				'label'       => esc_html__( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'search_button_name',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search', 'echo-knowledge-base' )
			),
			'search_results_msg' => array(
				'label'       => esc_html__( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'search_results_msg',
				'max'         => '80',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search Results for', 'echo-knowledge-base' )
			),

			'article_search_title' => array(
				'label'       => esc_html__( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'article_search_title',
				'max'         => '100',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'How Can We Help?', 'echo-knowledge-base' )
			),
			'article_search_box_hint' => array(
				'label'       => esc_html__( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_hint',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'article_search_button_name' => array(
				'label'       => esc_html__( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'article_search_button_name',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search', 'echo-knowledge-base' )
			),
			'article_search_results_msg' => array(
				'label'       => esc_html__( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'article_search_results_msg',
				'max'         => '80',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Search Results for', 'echo-knowledge-base' )
			),

			'no_results_found' => array(
				'label'       => esc_html__( 'No Matches Found Text', 'echo-knowledge-base' ),
				'name'        => 'no_results_found',
				'max'         => '80',
				'min'         => '1',
				'allowed_tags' => array('a' => array(
													'href'  => true,
													'title' => true,
												)),
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'No matches found', 'echo-knowledge-base' )
			),
			'min_search_word_size_msg' => array(
				'label'       => esc_html__( 'Minimum Search Word Size Message', 'echo-knowledge-base' ),
				'name'        => 'min_search_word_size_msg',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Enter a word with at least one character.', 'echo-knowledge-base' )
			),


			/***   KB Main Page - Categories and Articles ***/

			'category_empty_msg' => array(
				'label'       => esc_html__( 'Empty Category Message', 'echo-knowledge-base' ),
				'name'        => 'category_empty_msg',
				'max'         => '150',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Articles coming soon', 'echo-knowledge-base' )
			),
			'collapse_articles_msg' => array(
				'label'       => esc_html__( 'Collapse Articles Message', 'echo-knowledge-base' ),
				'name'        => 'collapse_articles_msg',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Collapse Articles', 'echo-knowledge-base' )
			),
			'show_all_articles_msg' => array(
				'label'       => esc_html__( 'Show Remaining Articles Message', 'echo-knowledge-base' ),
				'name'        => 'show_all_articles_msg',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Show Remaining Articles', 'echo-knowledge-base' )
			),
		);

		return $shared_specification;
	}

	/**
	 * Get KB default configuration
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array contains default values for KB configuration
	 */
	public static function get_default_kb_config( $kb_id ) {
		$config_specs = self::get_fields_specification( $kb_id );

		$default_configuration = array();
		foreach( $config_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for KB configuration
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID ) );
	}

	/**
	 * Get names of all configuration items for KB configuration
	 * @return array
	 */
	public static function get_specs_item_name_keys() {
		$keys = array();
		foreach ( self::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID ) as $key => $spec ) {
			$keys[$key] = '';
		}
		return $keys;
	}
}
