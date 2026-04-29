<?php
/**
 * Get Header Builder Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Header_Builder
 */
class Astra_Get_Header_Builder extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-header-builder';
		$this->label       = __( 'Get Astra Header Builder Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme header builder configuration including desktop and mobile layouts with components like buttons, HTML blocks, menus, widgets, social icons, and search positioned in above header, primary header, and below header sections.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'read',
			'constraints' => array(
				'usage_hints' => array(
					'return_structure'     => 'Returns desktop, mobile, mobile_popup, and available_components. Desktop/mobile contain nested objects: section > zone > array of component IDs. Example: desktop.primary.primary_left = ["logo"], desktop.primary.primary_right = ["menu-1", "search"]. Use this structure to understand current layout before making changes with update-header-builder.',
					'available_components' => 'CRITICAL: This map contains exact component IDs you MUST use. Keys are component IDs (use these in updates), values are display names (for reference only). Example: "social-icons-1" is the ID (use this), "Social Icons 1" is the name (display only). NEVER use display names or shortened versions - always use exact ID keys. Common mistake: using "social-1" instead of "social-icons-1".',
					'desktop_layout'       => 'Desktop structure: { popup: { popup_content: [] }, above: { above_left: [], above_left_center: [], above_center: [], above_right_center: [], above_right: [] }, primary: { primary_left: [], primary_left_center: [], primary_center: [], primary_right_center: [], primary_right: [] }, below: { below_left: [], below_left_center: [], below_center: [], below_right_center: [], below_right: [] } }. Each array contains component IDs currently placed in that zone.',
					'mobile_layout'        => 'Mobile structure: { popup: { popup_content: [] }, above: { above_left: [], above_center: [], above_right: [] }, primary: { primary_left: [], primary_center: [], primary_right: [] }, below: { below_left: [], below_center: [], below_right: [] } }. Mobile zones are independent of desktop - a component in desktop primary_right does NOT automatically appear in mobile.',
					'finding_components'   => 'To find where a component is currently located: Search through desktop and mobile zone arrays. Example: If menu-1 is in desktop.primary.primary_right, that array will contain "menu-1". To move it, you must remove it from desktop.primary.primary_right (set to []) and add to new zone. Always check current location before moving.',
					'zone_naming'          => 'Zone names follow pattern: section_position. Sections: above, primary, below, popup. Desktop positions: left, left_center, center, right_center, right. Mobile positions: left, center, right. Popup position: popup_content. Examples: above_left, primary_center, below_right_center, popup_content. Use underscore, not hyphen.',
					'interpreting_results' => 'When you get results: 1) Check available_components for exact IDs, 2) Examine desktop layout to see current desktop placement, 3) Examine mobile layout to see current mobile placement (may differ from desktop), 4) Check popup_content in mobile for drawer menu items, 5) Use this info to determine what zones to update for desired changes.',
					'common_patterns'      => 'Standard desktop: logo in primary_left, menu-1 in primary_right. Standard mobile: logo in primary_left, mobile-trigger in primary_right, mobile-menu in popup.popup_content. Multi-component zones: primary_right often has ["menu-1", "search", "button-1"]. Empty zones have []. Popup on desktop typically has ["mobile-menu"] for responsive behavior.',
					'mobile_popup_note'    => 'The mobile_popup return value is DEPRECATED but still returned for compatibility. The actual mobile popup content is in mobile.popup.popup_content. When updating, use mobile.popup structure, NOT separate mobile_popup parameter. Both return the same data, but updates should target mobile.popup.',
					'usage_workflow'       => 'Typical workflow: 1) Call this tool to get current state, 2) Note exact component IDs from available_components, 3) Find current placement of components you want to move, 4) Plan changes (what zones to empty, what zones to populate), 5) Call update-header-builder with changes. For moving: specify old zone as [] and new zone with component ID.',
				),
			),
		);
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'read';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array();
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'get header builder settings',
			'show header builder layout',
			'view header components',
			'display header builder configuration',
			'get header desktop layout',
			'show header mobile layout',
			'view header sections',
			'display header zones',
			'get above header items',
			'show primary header layout',
			'view below header components',
			'display header builder items',
			'get header menu positions',
			'show header widget placement',
			'view header button locations',
			'display header social icons',
			'get header HTML blocks',
			'show header search position',
			'view header builder zones',
			'display header layout configuration',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! defined( 'ASTRA_THEME_SETTINGS' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme is not active.', 'astra' ),
				__( 'Please activate the Astra theme to use this feature.', 'astra' )
			);
		}

		$desktop_items      = astra_get_option( 'header-desktop-items', array() );
		$mobile_items       = astra_get_option( 'header-mobile-items', array() );
		$mobile_popup_items = astra_get_option( 'header-mobile-popup-items', array() );

		$default_desktop = array(
			'popup'   => array(
				'popup_content' => array(),
			),
			'above'   => array(
				'above_left'         => array(),
				'above_left_center'  => array(),
				'above_center'       => array(),
				'above_right_center' => array(),
				'above_right'        => array(),
			),
			'primary' => array(
				'primary_left'         => array(),
				'primary_left_center'  => array(),
				'primary_center'       => array(),
				'primary_right_center' => array(),
				'primary_right'        => array(),
			),
			'below'   => array(
				'below_left'         => array(),
				'below_left_center'  => array(),
				'below_center'       => array(),
				'below_right_center' => array(),
				'below_right'        => array(),
			),
		);

		$default_mobile = array(
			'popup'   => array(
				'popup_content' => array(),
			),
			'above'   => array(
				'above_left'   => array(),
				'above_center' => array(),
				'above_right'  => array(),
			),
			'primary' => array(
				'primary_left'   => array(),
				'primary_center' => array(),
				'primary_right'  => array(),
			),
			'below'   => array(
				'below_left'   => array(),
				'below_center' => array(),
				'below_right'  => array(),
			),
		);

		$default_popup = array(
			'popup' => array(
				'popup_content' => array(),
			),
		);

		$desktop_items      = is_array( $desktop_items ) ? $this->array_merge_recursive_distinct( $default_desktop, $desktop_items ) : $default_desktop;
		$mobile_items       = is_array( $mobile_items ) ? $this->array_merge_recursive_distinct( $default_mobile, $mobile_items ) : $default_mobile;
		$mobile_popup_items = is_array( $mobile_popup_items ) ? $this->array_merge_recursive_distinct( $default_popup, $mobile_popup_items ) : $default_popup;

		$available_components = array(
			'logo'              => 'Logo',
			'button-1'          => 'Button 1',
			'button-2'          => 'Button 2',
			'button-3'          => 'Button 3',
			'html-1'            => 'HTML 1',
			'html-2'            => 'HTML 2',
			'html-3'            => 'HTML 3',
			'menu-1'            => 'Primary Menu',
			'menu-2'            => 'Secondary Menu',
			'mobile-menu'       => 'Mobile Menu',
			'mobile-trigger'    => 'Mobile Trigger',
			'widget-1'          => 'Widget 1',
			'widget-2'          => 'Widget 2',
			'widget-3'          => 'Widget 3',
			'social-icons-1'    => 'Social Icons 1',
			'social-icons-2'    => 'Social Icons 2',
			'search'            => 'Search',
			'woo-cart'          => 'WooCommerce Cart',
			'edd-cart'          => 'EDD Cart',
			'account'           => 'Account',
			'language-switcher' => 'Language Switcher',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved header builder settings successfully.', 'astra' ),
			array(
				'desktop'              => $desktop_items,
				'mobile'               => $mobile_items,
				'mobile_popup'         => $mobile_popup_items,
				'available_components' => $available_components,
			)
		);
	}

	/**
	 * Recursively merge two arrays, with values from the second array overwriting the first.
	 * Unlike array_merge_recursive, this doesn't create nested arrays for duplicate keys.
	 *
	 * @param array $array1 The base array.
	 * @param array $array2 The array to merge in (takes precedence).
	 * @return array The merged array.
	 */
	private function array_merge_recursive_distinct( $array1, $array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = $this->array_merge_recursive_distinct( $merged[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}
}

Astra_Get_Header_Builder::register();
