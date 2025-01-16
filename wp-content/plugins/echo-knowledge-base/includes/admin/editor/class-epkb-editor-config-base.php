<?php

/**
 * NEW Base class for every page configuration for KB and addons
 */
abstract class EPKB_Editor_Config_Base {
	
	/** 
		Options docs 
		
		editor_tab: tab, where will be shown input 
		type: most of the types are in the specs
		content: text for header setting  or html for raw_html
		
		List of the types 
			- color_hex
			- text
			- select
			- checkbox
			- number
			- units ( special view for select )
			
			- header
			- divider
			- notice (in the future)
			- hidden 
			- raw_html
			
		preview: if this parameter exist and have any "true" value, after changing setting iframe will be reloaded

	    target_selector: CSS selector for the style_name option
	    style_name: name of the style that will be changed (for live preview). Value will be used like style value (+postfix)

	    postfix: text that will be added to style's value

	    label: rewrite spec's label
		
		group_type: type of the group control (like type value for usual control)
		subfields: settings that should be shown in group fields 
		units: use this to set units for the dimensions setting. Will NOT be used like postfix
			Grouped array example for dimensions type 

			'search_button_padding' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS
				subfields => [
				]
			],
			
			Grouped array example for multiple type 
			
			'styles', 'target_attr', 'text', 'html' not supported here, this type only for css changes
			
			'advanced_search_mp_title_text_shadow' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'group_type' => self::EDITOR_GROUP_MULTIPLE
				'toggler'           => 'advanced_search_mp_title_text_shadow_toggle',
				'style_template' => 'advanced_search_mp_title_text_shadow_x_offset  advanced_search_mp_title_text_shadow_y_offset advanced_search_mp_title_text_shadow_toggle advanced_search_mp_title_font_shadow_color',
				'target_selector' => '#asea-search-title',
				'style_name' => 'box-shadow',
				'subfields' => [
					'advanced_search_mp_title_font_shadow_color'    => [],
					'advanced_search_mp_title_text_shadow_x_offset' => [],
					'advanced_search_mp_title_text_shadow_y_offset' => [],
					'advanced_search_mp_title_text_shadow_blur'     => [],
				]
			],

		target_attr: attribute that should be changed in elements by target_selector. If need many, use | like separator

		text: any "true" value (1 for example) will change text of the  target_selector's element (NOT html!)

		text_style: parameter for style of the text control, full for 2 rows, inline for 1 row and 2 columns. Default full

		style: for number input can be slider - then number input will have slider under setting , default: default
		for select it can be 'prev-next' - arrows instead dropdown, large, medium, small - to set size of the select width
		style_important: true/false - do we need to add !important to the styles. Default: true
		
		min, max: parameters for number input, standard html attributes
		
		toggler: if the field A have this parameter, then field A will be shown/hidden when field B (toggler) will be on/off. Example: 'toggler' => 'section_divider'. When section_divider will be off, then field will be not shown.
		
		Can be an array: 
		'toggler' => [
			'section_divider' => 'on',
            'section_divider2' => '!style3',
			'section_style' => 'style_1|style_2'
		]
		
		then it will work with relation AND for each element. | is divider with OR options. (Show field if section_divider == on AND section_divider2 !== style3 AND ( section_style == style_1 OR style_2) )

		styles: use it if you need additional styles for the few selectors, example:
			
			'styles' => [
				'#epkb-content-container .epkb-nav-tabs .active:after' => 'border-top-color',
				'#epkb-content-container .epkb-nav-tabs .active' => 'color'
			]
			
			will generate 
			
			#epkb-content-container .epkb-nav-tabs .active:after {
				border-top-color: [value][postfix]!important;
			}
			
			#epkb-content-container .epkb-nav-tabs .active {
				color: [value][postfix]!important;
			}
	 		*If you need few styles for the same selector use additional spaces in the middle of the string:
			'.epkb-articles  .epkb-article-level-1' => 'padding-top',
			'.epkb-articles .epkb-article-level-1' => 'padding-bottom'
		
		parent_zone_tab_title: optional parameter in Parent setting that indicates that child zone will shows this "parent" tab with this name (can be any name). JS then automatically finds the parents (NOT using this name)
		
		description: will add description under the field (html)
		
		options: will be used for select, checkboxes, units. 
			- for usual select, checkboxes, units: [ option_slug => 'Option Label' ... ]
			- for optons groups only for select type: 
				[
					'Option Group Label 1' => [
						'grouped_option_slug1' => 'Option Label 1'
						'grouped_option_slug2' => 'Option Label 2'
						...
					],
					'out_of_groups_option_slug' => 'Label'
					...
				]
		Link for the documentation example (add near the 'settings' parameter)
			'docs_html' => sprintf( '%s %s <a href="http://google.com" target="_blank">%s</a>',
			esc_html__( 'Read documentation about', 'echo-knowledge-base' ),
			esc_html__( 'Page Zone', 'echo-knowledge-base' ),
			esc_html__( 'here.', 'echo-knowledge-base' ) ),
	*/

	/**
		DIVIDER EXAMPLE ----------------------
		Checked setting: Divider - will show only 1 line between elements, don't need any additional attibutes, only that we have here. Id can be random, but unique

		'search_divider_1' => [
			'editor_tab' => self::EDITOR_TAB_CONTENT,
			'type' => 'divider'
		],

		HEADER EXAMPLE -----------------------
		Checked setting: Text
		text changing content of the target_selector element

		'search_header' => [
			'editor_tab' => self::EDITOR_TAB_CONTENT,
			'type' => 'header',
			'content' => 'Search Header Example'
		],

	 */

	const EDITOR_PAGE_TYPES = [ 'main-page', 'article-page', 'archive-page', 'search-page' ];

	// Visual Editor Tabs
	const EDITOR_TAB_CONTENT = 'content';
	const EDITOR_TAB_STYLE = 'style';
	const EDITOR_TAB_FEATURES = 'features';
	const EDITOR_TAB_ADVANCED = 'advanced';
	const EDITOR_TAB_GLOBAL = 'global';
	const EDITOR_TAB_DISABLED = 'hidden';
		
	const EDITOR_GROUP_DIMENSIONS = 'dimensions';
	const EDITOR_GROUP_MULTIPLE = 'multiple';

	// config for current page
	protected $config = array();
	
	// specs for current page 
	protected $specs = array();
	
	// unset settings for current page
	protected $unset_settings = array();

	// page type
	protected $page_type = '';
	
	// settings for left panel 
	public $setting_zones = [];

	abstract function load_config();
	abstract function load_specs();
	abstract function load_setting_zones();

	public function __construct( ) {
		
		// get current values from DB and store in the class 
		$this->load_config();

		// get specs values 
		$this->load_specs();

		// define settings zones based on each class 
		$this->load_setting_zones();

		// ensure everything is loaded correctly
		if ( ! $this->is_initialized() ) {
			return;
		}

		// add fields settings to each zone/field based on specs, apply filters and unset settings 
		$this->setting_zones = $this->apply_defaults_to_setting_zones( $this->config, $this->setting_zones, $this->specs, $this->unset_settings, $this->page_type );
	}

	private function apply_defaults_to_setting_zones( $config, $setting_zones, $field_specification, $unset_settings = [], $page_type = '' ) {
		
		// unset not used settings 
		$setting_zones = self::unset_settings( $setting_zones, $unset_settings );
		
		// configuration for add-on fields; for backward compatibility prevent Advanced Search to add its config (depending on kb config values)
		$setting_zones = apply_filters( 'eckb_editor_fields_config', $setting_zones, $config, $page_type );

		foreach ( $setting_zones as $zone => $zone_data ) {
			
			if ( empty( $zone_data['settings'] ) ) {
				continue;
			}
			
			foreach( $zone_data['settings'] as $field_name => $field_data ) {

				// handle special types without inputs 
				if ( ! empty( $field_data['type'] ) && in_array( $field_data['type'], ['header','divider','notice','header_desc', 'preset', 'raw_html'] ) ||
				     ( ! isset( $field_specification[$field_name] ) && empty( $field_data['group_type'] ) ) && ! self::is_sidebar_priority( $field_name ) ) {
					continue;
				}
				
				// handle regular control
				if ( empty( $field_data['group_type'] ) ) {

					// sidebar priority is handled differently
					if ( self::is_sidebar_priority( $field_name ) ) {

						// add current value
						if ( isset( $config['article_sidebar_component_priority'][$field_name] ) ) {
							$setting_zones[$zone]['settings'][$field_name]['value'] = $config['article_sidebar_component_priority'][$field_name];
						} else {
							$setting_zones[$zone]['settings'][$field_name]['value'] = $field_data['default'];
						}

					} else {

						$setting_zones[$zone]['settings'][$field_name] += $field_specification[$field_name];
						
						// add current value
						if ( isset( $config[$field_name] ) ) {
							$setting_zones[$zone]['settings'][$field_name]['value'] = $config[$field_name];
						} else {
							$setting_zones[$zone]['settings'][$field_name]['value'] = $setting_zones[$zone]['settings'][$field_name]['default'];
						}

					}

				// handle group control
				} else {

					foreach ( $field_data['subfields'] as $subfield_name => $subfield_data ) {
						
						$setting_zones[$zone]['settings'][$field_name]['subfields'][$subfield_name] += $field_specification[$subfield_name];
						
						// add current value
						if ( isset( $config[$subfield_name] ) ) {
							$setting_zones[$zone]['settings'][$field_name]['subfields'][$subfield_name]['value'] = $config[$subfield_name];
						} else {
							$setting_zones[$zone]['settings'][$field_name]['subfields'][$subfield_name]['value'] = $setting_zones[$zone]['settings'][$field_name]['subfields'][$subfield_name]['default'];
						}
					}
				}
			}
		}
		
		return $setting_zones;
	}

	/**
	 * Unset Settings from array
	 *
	 * @param $settings
	 * @param $unset_settings
	 * @return mixed
	 */
	public static function unset_settings( $settings, $unset_settings ) {
		foreach( $unset_settings as $field_name ) {
			foreach ( $settings as $zone => $data ) {
				if ( isset( $settings[$zone]['settings'][$field_name] ) ) {
					unset( $settings[$zone]['settings'][$field_name] );
				}
			}
		}

		return $settings;
	}

	public static function is_sidebar_priority( $field_name ) {
		return in_array( $field_name, ['toc_left', 'toc_content', 'toc_right', 'kb_sidebar_left', 'kb_sidebar_right', 'nav_sidebar_left',  'nav_sidebar_right'] );
	}

	public function is_initialized() {
		return ! empty( $this->config ) || ! empty( $this->specs ) || ! empty( $this->setting_zones );
	}
}