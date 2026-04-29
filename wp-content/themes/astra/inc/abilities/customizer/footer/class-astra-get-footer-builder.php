<?php
/**
 * Get Footer Builder Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Footer_Builder
 */
class Astra_Get_Footer_Builder extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-footer-builder';
		$this->label       = __( 'Get Astra Footer Builder Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme footer builder configuration including desktop and mobile layouts with components like buttons, HTML blocks, widgets, social icons, footer menu, and copyright positioned in above footer, primary footer, and below footer sections.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'read',
			'constraints' => array(
				'usage_hints' => array(
					'return_structure'     => 'Returns desktop, mobile, column counts (above/primary/below_footer_columns), layout widths (above/primary/below_footer_layout), and available_components. Desktop/mobile contain nested objects: section → column → array of component IDs. Example: desktop.primary.primary_1 = ["copyright"], desktop.primary.primary_2 = ["menu"].',
					'available_components' => 'CRITICAL: This map contains exact component IDs you MUST use. Keys are component IDs (use these in updates), values are display names (for reference only). Example: "social-icons-1" is the ID (use this), "Social Icons 1" is the name (display only). NEVER use display names - always use exact ID keys. Common: copyright, menu, widget-1 through widget-4, social-icons-1, social-icons-2, html-1/2/3, button-1/2.',
					'desktop_layout'       => 'Desktop structure: { above: { above_1: [], above_2: [], above_3: [], above_4: [], above_5: [] }, primary: { primary_1: [], primary_2: [], primary_3: [], primary_4: [], primary_5: [] }, below: { below_1: [], below_2: [], below_3: [], below_4: [], below_5: [] } }. Each array contains component IDs currently in that column. Empty arrays mean column is empty.',
					'mobile_layout'        => 'Mobile structure: { above: { above_1: [], above_2: [] }, primary: { primary_1: [], primary_2: [] }, below: { below_1: [], below_2: [] } }. Mobile columns are independent of desktop - a component in desktop primary_1 does NOT automatically appear in mobile. Must explicitly configure both.',
					'column_counts'        => 'above_footer_columns, primary_footer_columns, below_footer_columns show how many columns are ACTIVE in each section. Example: If primary_footer_columns=3, only columns primary_1, primary_2, primary_3 display on desktop. Columns primary_4 and primary_5 exist in data but are hidden. Mobile always uses 2 columns regardless.',
					'layout_widths'        => 'above_footer_layout, primary_footer_layout, below_footer_layout indicate section width. "full" = edge-to-edge full width, "content" = boxed within content width. Use this to understand current visual layout.',
					'finding_components'   => 'To find where a component is currently located: Search through desktop and mobile column arrays. Example: If copyright is in desktop.below.below_1, that array will contain "copyright". To move it, you must remove it from below_1 (set to []) and add to new column.',
					'column_naming'        => 'Column names follow pattern: section_number. Sections: above, primary, below. Numbers: 1-5 for desktop, 1-2 for mobile. Examples: above_1, primary_3, below_5. Use underscore, not hyphen (above_1 not above-1).',
					'interpreting_results' => 'When you get results: 1) Check available_components for exact IDs, 2) Examine desktop layout to see current placement, 3) Check column counts to see how many columns are active, 4) Examine mobile layout (independent from desktop), 5) Note layout widths, 6) Use this info to plan updates.',
					'usage_workflow'       => 'Typical workflow: 1) Call this tool to get current state, 2) Note exact component IDs from available_components, 3) Find current placement of components you want to move, 4) Check column counts to ensure you have enough columns, 5) Plan changes (what columns to empty, what columns to populate), 6) Call update-footer-builder with changes. For moving: old column = [], new column = [component-id].',
				),
			),
		);
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
			'get footer builder settings',
			'show footer builder layout',
			'view footer components',
			'display footer builder configuration',
			'get footer desktop layout',
			'show footer mobile layout',
			'view footer sections',
			'display footer zones',
			'get above footer items',
			'show primary footer layout',
			'view below footer components',
			'display footer builder items',
			'get footer widget positions',
			'show footer copyright placement',
			'view footer HTML blocks',
			'display footer social icons',
			'get footer menu position',
			'show footer button locations',
			'view footer builder zones',
			'display footer layout configuration',
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

		$desktop_items = astra_get_option( 'footer-desktop-items', array() );
		$mobile_items  = astra_get_option( 'footer-mobile-items', array() );

		$default_desktop = array(
			'above'   => array(
				'above_1' => array(),
				'above_2' => array(),
				'above_3' => array(),
				'above_4' => array(),
				'above_5' => array(),
			),
			'primary' => array(
				'primary_1' => array(),
				'primary_2' => array(),
				'primary_3' => array(),
				'primary_4' => array(),
				'primary_5' => array(),
			),
			'below'   => array(
				'below_1' => array(),
				'below_2' => array(),
				'below_3' => array(),
				'below_4' => array(),
				'below_5' => array(),
			),
		);

		$default_mobile = array(
			'above'   => array(
				'above_1' => array(),
				'above_2' => array(),
			),
			'primary' => array(
				'primary_1' => array(),
				'primary_2' => array(),
			),
			'below'   => array(
				'below_1' => array(),
				'below_2' => array(),
			),
		);

		$desktop_items = is_array( $desktop_items ) ? $this->array_merge_recursive_distinct( $default_desktop, $desktop_items ) : $default_desktop;
		$mobile_items  = is_array( $mobile_items ) ? $this->array_merge_recursive_distinct( $default_mobile, $mobile_items ) : $default_mobile;

		$above_footer_columns   = astra_get_option( 'hba-footer-column', '3' );
		$primary_footer_columns = astra_get_option( 'hb-footer-column', '3' );
		$below_footer_columns   = astra_get_option( 'hbb-footer-column', '1' );

		$above_footer_layout   = astra_get_option( 'hba-footer-layout', 'full' );
		$primary_footer_layout = astra_get_option( 'hb-footer-layout', 'full' );
		$below_footer_layout   = astra_get_option( 'hbb-footer-layout', 'full' );

		$available_components = array(
			'copyright'      => 'Copyright',
			'menu'           => 'Footer Menu',
			'widget-1'       => 'Widget 1',
			'widget-2'       => 'Widget 2',
			'widget-3'       => 'Widget 3',
			'widget-4'       => 'Widget 4',
			'social-icons-1' => 'Social Icons 1',
			'social-icons-2' => 'Social Icons 2',
			'html-1'         => 'HTML 1',
			'html-2'         => 'HTML 2',
			'html-3'         => 'HTML 3',
			'button-1'       => 'Button 1',
			'button-2'       => 'Button 2',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved footer builder settings successfully.', 'astra' ),
			array(
				'desktop'                => $desktop_items,
				'mobile'                 => $mobile_items,
				'above_footer_columns'   => (int) $above_footer_columns,
				'primary_footer_columns' => (int) $primary_footer_columns,
				'below_footer_columns'   => (int) $below_footer_columns,
				'above_footer_layout'    => $above_footer_layout,
				'primary_footer_layout'  => $primary_footer_layout,
				'below_footer_layout'    => $below_footer_layout,
				'available_components'   => $available_components,
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
				// If both values are arrays, merge them recursively.
				$merged[ $key ] = $this->array_merge_recursive_distinct( $merged[ $key ], $value );
			} else {
				// Otherwise, use the value from array2.
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}
}

Astra_Get_Footer_Builder::register();
