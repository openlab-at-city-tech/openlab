<?php
/**
 * Update Header Builder Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Header_Builder
 */
class Astra_Update_Header_Builder extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-header-builder';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Header Builder Settings', 'astra' );
		$this->description = __( 'Updates the Astra theme header builder configuration for desktop and mobile layouts. Allows positioning components like buttons, HTML blocks, menus, widgets, social icons, and search in popup (mobile drawer), above header, primary header, and below header sections with specific zone placements.', 'astra' );

		$this->meta = array(
			'tool_type'   => 'write',
			'constraints' => array(
				'dependencies' => array(
					array(
						'tool'             => 'astra/get-header-builder',
						'description_hint' => 'Get current header layout and available_components before updating',
					),
				),
				'usage_hints'  => array(
					'CRITICAL_WORKFLOW'   => 'ALWAYS call astra/get-header-builder FIRST before updating. This tool returns available_components (exact IDs to use) and current layout. SMART APPEND: When adding a single new component to a zone, it automatically appends to existing components. When sending multiple components or removing, it replaces. Only specify zones you are changing.',
					'component_ids'       => 'EXACT component IDs (case-sensitive, use exactly as shown): logo, menu-1, menu-2, mobile-menu, mobile-trigger, button-1, button-2, button-3, html-1, html-2, html-3, widget-1, widget-2, widget-3, social-icons-1 (NOT social-1!), social-icons-2, search, woo-cart, edd-cart, account, language-switcher. NEVER use "social-1" or "primary-menu" - use "social-icons-1" and "menu-1".',
					'desktop_structure'   => 'Desktop has 4 sections: popup (off-canvas), above (top row), primary (main row), below (bottom row). Each visible row has 5 zones: section_left, section_left_center, section_center, section_right_center, section_right. Popup has popup_content zone. Zone names: above_left, above_left_center, above_center, above_right_center, above_right, primary_left, primary_left_center, primary_center, primary_right_center, primary_right, below_left, below_left_center, below_center, below_right_center, below_right, popup_content.',
					'mobile_structure'    => 'Mobile has 4 sections: popup (drawer menu), above, primary, below. Each visible row has 3 zones: section_left, section_center, section_right. Popup has popup_content. Zone names: above_left, above_center, above_right, primary_left, primary_center, primary_right, below_left, below_center, below_right, popup_content. Typical mobile: logo in primary_left, mobile-trigger in primary_right, mobile-menu in popup_content.',
					'moving_components'   => 'To MOVE a component: Send the single component ID to the new zone (auto-removes from old location). Example: To move menu-1 to above_center: {"desktop": {"above": {"above_center": ["menu-1"]}}}. The component is automatically added to the new zone while preserving other existing components in that zone.',
					'adding_components'   => 'To ADD a single component: Simply send the component ID in an array to the target zone. Example: To add search to primary_right: {"desktop": {"primary": {"primary_right": ["search"]}}}. SMART BEHAVIOR: The search component will be automatically APPENDED to existing components in primary_right (e.g., if primary_right has ["menu-1"], result will be ["menu-1", "search"]). No need to include existing components when adding a single new one.',
					'removing_components' => 'To REMOVE a specific component: Send zone with only the components you want to KEEP (exclude the one to remove). Example: To remove search from primary_right (currently has ["logo", "menu-1", "search"]): {"desktop": {"primary": {"primary_right": ["logo", "menu-1"]}}}. To clear entire zone: send empty array []. Note: Sending a single existing component will keep only that one (removes others).',
					'multiple_operations' => 'To ADD multiple components at once OR rearrange: Send the complete array with all components in desired order. Example: To add both search and button-1 to primary_right (currently has ["menu-1"]): {"desktop": {"primary": {"primary_right": ["menu-1", "search", "button-1"]}}}. Order in array determines display order.',
					'zone_arrays'         => 'Smart zone behavior: Single NEW component ["search"] = APPEND to existing. Single EXISTING component ["menu-1"] = KEEP only this (remove others). Multiple components ["logo", "menu-1", "search"] = REPLACE with exact list. Empty array [] = CLEAR zone. This smart behavior makes it easy to add components without knowing the current layout.',
					'popup_usage'         => 'Desktop popup: Typically {"popup": {"popup_content": ["mobile-menu"]}} for responsive behavior. Mobile popup: {"popup": {"popup_content": ["mobile-menu", "social-icons-1", "button-1"]}} - this is the hamburger menu drawer content. The mobile-trigger component (usually in primary_right) opens the popup. Components in popup_content display vertically in the drawer.',
					'common_mistakes'     => 'AVOID: 1) Using "social-1" instead of "social-icons-1" (wrong ID), 2) Using zone names incorrectly (e.g., "primary-left" instead of "primary_left"), 3) Providing string instead of array (use ["menu-1"] not "menu-1"), 4) Updating mobile_popup separately (deprecated - use mobile.popup instead). Good news: You no longer need to include existing components when adding a single new component - it auto-appends!',
					'responsive_design'   => 'Desktop and mobile are INDEPENDENT. A component in desktop primary_right does NOT automatically appear in mobile. Must explicitly configure both desktop and mobile layouts. Typical pattern: Desktop has full menu in primary_right, mobile has mobile-trigger in primary_right with mobile-menu in popup_content. Configure both in same update or separately.',
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
		return 'write';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'desktop' => array(
					'type'        => 'object',
					'description' => 'Desktop header layout configuration with popup, above, primary, and below sections. Each section contains zones with arrays of component IDs.',
					'properties'  => array(
						'popup'   => array(
							'type'        => 'object',
							'description' => 'Popup section for desktop (typically contains mobile-menu for mobile view).',
							'properties'  => array(
								'popup_content' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'above'   => array(
							'type'        => 'object',
							'description' => 'Above header section zones.',
							'properties'  => array(
								'above_left'         => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_left_center'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_center'       => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_right_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_right'        => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'primary' => array(
							'type'        => 'object',
							'description' => 'Primary header section zones.',
							'properties'  => array(
								'primary_left'         => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_left_center'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_center'       => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_right_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_right'        => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'below'   => array(
							'type'        => 'object',
							'description' => 'Below header section zones.',
							'properties'  => array(
								'below_left'         => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_left_center'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_center'       => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_right_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_right'        => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
				'mobile'  => array(
					'type'        => 'object',
					'description' => 'Mobile header layout configuration with popup, above, primary, and below sections. The popup section contains the mobile drawer/hamburger menu content.',
					'properties'  => array(
						'popup'   => array(
							'type'        => 'object',
							'description' => 'Popup section for mobile drawer menu (opened by mobile-trigger).',
							'properties'  => array(
								'popup_content' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'above'   => array(
							'type'        => 'object',
							'description' => 'Above header section zones for mobile.',
							'properties'  => array(
								'above_left'   => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_right'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'primary' => array(
							'type'        => 'object',
							'description' => 'Primary header section zones for mobile.',
							'properties'  => array(
								'primary_left'   => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_right'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'below'   => array(
							'type'        => 'object',
							'description' => 'Below header section zones for mobile.',
							'properties'  => array(
								'below_left'   => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_center' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_right'  => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			// Basic adding.
			'add menu-1 to primary header right',
			'add social-icons-1 to above header right',
			'add search to primary header left',
			'add button-1 to primary header right',
			'add logo to primary header left',
			'add widget-1 to below header center',
			'add html-1 to above header center',
			'add account to primary header right',
			'add woo-cart to primary header right',

			// Moving components.
			'move menu-1 to above header center',
			'move logo to primary header center',
			'move search from primary to below header',
			'move social-icons-1 to below header right',
			'move button-1 to above header right',
			'move primary menu to above header center and add search in below header right',

			// Specific zone placements.
			'place menu-1 in primary_right',
			'place logo in primary_left',
			'place search in primary_left_center',
			'place social-icons-1 in above_right',
			'place button-1 in primary_right_center',
			'place widget-1 in below_center',

			// Mobile configurations.
			'add mobile-trigger to primary header right for mobile',
			'add mobile-menu to mobile popup',
			'place mobile-menu in popup_content',
			'add social-icons-1 to mobile popup',
			'configure mobile header with logo left and trigger right',

			// Multiple components.
			'add menu-1 and search to primary header right',
			'add button-1 and social-icons-1 to primary header',
			'place logo in left and menu-1 in right of primary header',

			// Removing components.
			'remove menu-1 from primary header',
			'remove search from header',
			'clear above header',

			// Complex layouts.
			'add logo to primary left, menu-1 to primary right',
			'move menu-1 to center and add search to left',
			'reorganize header with logo center, menu left, search right',
			'add html-1 to above center and widget-1 to below left',

			// Zone-specific.
			'add button-2 to primary_right_center',
			'place html-2 in above_left_center',
			'move widget-2 to below_center',

			// Multi-section.
			'add social-icons-1 to above header and menu-2 to below header',
			'place copyright in above and menu in primary',
			'configure header with components in above, primary, and below',
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

		if ( ! function_exists( 'astra_get_option' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme helper functions not available.', 'astra' ),
				__( 'The astra_get_option function is required but not found.', 'astra' )
			);
		}

		$updated          = false;
		$update_messages  = array();
		$detailed_changes = array();

		if ( isset( $args['desktop'] ) && is_array( $args['desktop'] ) ) {
			$current_desktop_raw = astra_get_option( 'header-desktop-items', array() );
			if ( ! is_array( $current_desktop_raw ) ) {
				$current_desktop_raw = array();
			}

			$default_desktop = $this->get_default_desktop_structure();
			$current_desktop = $this->array_merge_recursive_distinct( $default_desktop, $current_desktop_raw );

			$desktop_layout = $this->sanitize_header_layout( $args['desktop'], 'desktop' );

			$changes = $this->get_layout_changes( $current_desktop, $desktop_layout, 'desktop' );
			if ( ! empty( $changes ) ) {
				$detailed_changes = array_merge( $detailed_changes, $changes );
			}

			$new_desktop = $this->array_merge_recursive_distinct( $current_desktop, $desktop_layout );

			astra_update_option( 'header-desktop-items', $new_desktop );
			$updated           = true;
			$update_messages[] = 'Desktop header layout updated';
		}

		if ( isset( $args['mobile'] ) && is_array( $args['mobile'] ) ) {

			$current_mobile_raw = astra_get_option( 'header-mobile-items', array() );
			if ( ! is_array( $current_mobile_raw ) ) {
				$current_mobile_raw = array();
			}

			$default_mobile = $this->get_default_mobile_structure();
			$current_mobile = $this->array_merge_recursive_distinct( $default_mobile, $current_mobile_raw );

			$mobile_layout = $this->sanitize_header_layout( $args['mobile'], 'mobile' );

			$changes = $this->get_layout_changes( $current_mobile, $mobile_layout, 'mobile' );
			if ( ! empty( $changes ) ) {
				$detailed_changes = array_merge( $detailed_changes, $changes );
			}

			$new_mobile = $this->array_merge_recursive_distinct( $current_mobile, $mobile_layout );

			astra_update_option( 'header-mobile-items', $new_mobile );
			$updated           = true;
			$update_messages[] = 'Mobile header layout updated';
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one layout to update (desktop or mobile).', 'astra' )
			);
		}

		$message = implode( ', ', $update_messages ) . '.';
		if ( ! empty( $detailed_changes ) ) {
			$message .= ' Details: ' . implode( '; ', $detailed_changes ) . '.';
		}

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'changes' => $detailed_changes,
			)
		);
	}

	/**
	 * Sanitize header layout data.
	 *
	 * @param array  $layout Layout array.
	 * @param string $type   Layout type (desktop or mobile).
	 * @return array Sanitized layout.
	 */
	private function sanitize_header_layout( $layout, $type = 'desktop' ) {
		$sanitized = array();
		$sections  = array( 'popup', 'above', 'primary', 'below' );

		$desktop_zones = array(
			'popup'   => array( 'popup_content' ),
			'above'   => array( 'above_left', 'above_left_center', 'above_center', 'above_right_center', 'above_right' ),
			'primary' => array( 'primary_left', 'primary_left_center', 'primary_center', 'primary_right_center', 'primary_right' ),
			'below'   => array( 'below_left', 'below_left_center', 'below_center', 'below_right_center', 'below_right' ),
		);

		$mobile_zones = array(
			'popup'   => array( 'popup_content' ),
			'above'   => array( 'above_left', 'above_center', 'above_right' ),
			'primary' => array( 'primary_left', 'primary_center', 'primary_right' ),
			'below'   => array( 'below_left', 'below_center', 'below_right' ),
		);

		$zones = 'desktop' === $type ? $desktop_zones : $mobile_zones;

		foreach ( $sections as $section ) {
			if ( isset( $layout[ $section ] ) && is_array( $layout[ $section ] ) ) {
				$section_zones = array();
				foreach ( $zones[ $section ] as $zone ) {
					if ( isset( $layout[ $section ][ $zone ] ) && is_array( $layout[ $section ][ $zone ] ) ) {
						$normalized = array_map( array( $this, 'normalize_component_id' ), $layout[ $section ][ $zone ] );

						$section_zones[ $zone ] = $normalized;
					}
				}

				if ( ! empty( $section_zones ) ) {
					$sanitized[ $section ] = $section_zones;
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Normalize component ID to match Astra's expected format.
	 * Maps common variations and incorrect IDs to the correct component IDs.
	 *
	 * @param string $component_id Component ID to normalize.
	 * @return string Normalized component ID.
	 */
	private function normalize_component_id( $component_id ) {
		$component_id = sanitize_text_field( $component_id );
		$component_id = strtolower( trim( $component_id ) );

		$id_mapping = array(
			// Menu variations.
			'menu'              => 'menu-1',
			'menu_primary'      => 'menu-1',
			'primary_menu'      => 'menu-1',
			'primary-menu'      => 'menu-1',
			'menu_1'            => 'menu-1',
			'menu_secondary'    => 'menu-2',
			'secondary_menu'    => 'menu-2',
			'secondary-menu'    => 'menu-2',
			'menu_2'            => 'menu-2',
			// Social icons variations.
			'social'            => 'social-icons-1',
			'social_icons'      => 'social-icons-1',
			'social-icons'      => 'social-icons-1',
			'social_icons_1'    => 'social-icons-1',
			'social_1'          => 'social-icons-1',
			'social-1'          => 'social-icons-1',
			'social_icons_2'    => 'social-icons-2',
			'social_2'          => 'social-icons-2',
			'social-2'          => 'social-icons-2',
			// Button variations.
			'button'            => 'button-1',
			'btn'               => 'button-1',
			'button_1'          => 'button-1',
			'button_2'          => 'button-2',
			'button_3'          => 'button-3',
			'btn-1'             => 'button-1',
			'btn-2'             => 'button-2',
			'btn-3'             => 'button-3',
			// HTML variations.
			'html'              => 'html-1',
			'html_1'            => 'html-1',
			'html_2'            => 'html-2',
			'html_3'            => 'html-3',
			// Widget variations.
			'widget'            => 'widget-1',
			'widget_1'          => 'widget-1',
			'widget_2'          => 'widget-2',
			'widget_3'          => 'widget-3',
			// Mobile variations.
			'mobile_menu'       => 'mobile-menu',
			'mobile_trigger'    => 'mobile-trigger',
			'hamburger'         => 'mobile-trigger',
			'menu-toggle'       => 'mobile-trigger',
			// Logo variations.
			'site_logo'         => 'logo',
			'site-logo'         => 'logo',
			'site_title'        => 'logo',
			'site-title'        => 'logo',
			// Search variations.
			'search_bar'        => 'search',
			'search-bar'        => 'search',
			'search_box'        => 'search',
			'search-box'        => 'search',
			// WooCommerce variations.
			'cart'              => 'woo-cart',
			'woo_cart'          => 'woo-cart',
			'woocommerce_cart'  => 'woo-cart',
			'woocommerce-cart'  => 'woo-cart',
			// EDD variations.
			'edd_cart'          => 'edd-cart',
			// Account variations.
			'user_account'      => 'account',
			'user-account'      => 'account',
			'login'             => 'account',
			// Other variations.
			'language_switcher' => 'language-switcher',
		);

		if ( isset( $id_mapping[ $component_id ] ) ) {
			return $id_mapping[ $component_id ];
		}

		return $component_id;
	}

	/**
	 * Recursively merge two arrays, with values from the second array overwriting the first.
	 * Unlike array_merge_recursive, this doesn't create nested arrays for duplicate keys.
	 * Special handling: Component arrays at zone level are intelligently merged or replaced.
	 *
	 * @param array $array1 The base array.
	 * @param array $array2 The array to merge in (takes precedence).
	 * @param int   $depth  Current recursion depth.
	 * @return array The merged array.
	 */
	private function array_merge_recursive_distinct( $array1, $array2, $depth = 0 ) {
		$merged = $array1;

		foreach ( $array2 as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$is_component_array = $this->is_numeric_array( $value );

				if ( ! $is_component_array ) {
					$merged[ $key ] = $this->array_merge_recursive_distinct( $merged[ $key ], $value, $depth + 1 );
				} else {
					if ( 1 === $depth ) {
						$existing = $merged[ $key ];
						$new      = $value;

						if ( empty( $new ) ) {
							$merged[ $key ] = array();
						} elseif ( count( $new ) === 1 && ! empty( $existing ) ) {
							$new_component = $new[0];
							$is_new        = ! in_array( $new_component, $existing, true );

							if ( $is_new ) {
								$merged[ $key ] = array_values( array_unique( array_merge( $existing, $new ) ) );
							} else {
								$merged[ $key ] = array_values( array_unique( $value ) );
							}
						} else {
							$merged[ $key ] = array_values( array_unique( $value ) );
						}
					} else {
						$merged[ $key ] = $value;
					}
				}
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Check if array is a numeric/sequential array (list) vs associative array (map).
	 *
	 * @param array $array Array to check.
	 * @return bool True if numeric array, false if associative.
	 */
	private function is_numeric_array( $array ) {
		if ( empty( $array ) ) {
			return true;
		}
		return array_keys( $array ) === range( 0, count( $array ) - 1 );
	}

	/**
	 * Get detailed changes made to the layout for user feedback.
	 *
	 * @param array  $current_layout Current layout structure.
	 * @param array  $new_layout     New layout being applied.
	 * @param string $device         Device type (desktop or mobile).
	 * @return array Array of change descriptions.
	 */
	private function get_layout_changes( $current_layout, $new_layout, $device ) {
		$changes = array();

		foreach ( $new_layout as $section => $zones ) {
			if ( ! is_array( $zones ) ) {
				continue;
			}

			foreach ( $zones as $zone => $components ) {
				if ( ! is_array( $components ) ) {
					continue;
				}

				$existing = isset( $current_layout[ $section ][ $zone ] ) && is_array( $current_layout[ $section ][ $zone ] )
					? $current_layout[ $section ][ $zone ]
					: array();

				$zone_label = $this->get_zone_label( $section, $zone );

				if ( empty( $components ) && ! empty( $existing ) ) {
					$existing_names = array_map( array( $this, 'get_component_name' ), $existing );
					$changes[]      = sprintf(
						'Removed %s from %s (%s)',
						implode( ', ', $existing_names ),
						$zone_label,
						$device
					);
					continue;
				}

				if ( ! empty( $components ) ) {
					$new_components = array_diff( $components, $existing );

					if ( ! empty( $new_components ) ) {
						$component_names = array_map( array( $this, 'get_component_name' ), $new_components );

						if ( ! empty( $existing ) ) {
							$existing_names = array_map( array( $this, 'get_component_name' ), $existing );
							$changes[]      = sprintf(
								'Added %s to %s (%s) alongside existing %s',
								implode( ', ', $component_names ),
								$zone_label,
								$device,
								implode( ', ', $existing_names )
							);
						} else {
							$changes[] = sprintf(
								'Added %s to %s (%s)',
								implode( ', ', $component_names ),
								$zone_label,
								$device
							);
						}
					}
				}
			}
		}

		return $changes;
	}

	/**
	 * Get human-readable zone label.
	 *
	 * @param string $section Section name (popup, above, primary, below).
	 * @param string $zone    Zone name (e.g., primary_left, above_center).
	 * @return string Formatted zone label.
	 */
	private function get_zone_label( $section, $zone ) {
		$section_labels = array(
			'popup'   => 'Popup',
			'above'   => 'Above Header',
			'primary' => 'Primary Header',
			'below'   => 'Below Header',
		);

		$position_labels = array(
			'left'          => 'Left',
			'left_center'   => 'Left Center',
			'center'        => 'Center',
			'right_center'  => 'Right Center',
			'right'         => 'Right',
			'popup_content' => 'Content',
		);

		$position = str_replace( $section . '_', '', $zone );

		$section_label  = isset( $section_labels[ $section ] ) ? $section_labels[ $section ] : $section;
		$position_label = isset( $position_labels[ $position ] ) ? $position_labels[ $position ] : $position;

		if ( 'popup' === $section ) {
			return $section_label;
		}

		return sprintf( '%s %s', $section_label, $position_label );
	}

	/**
	 * Get human-readable component name.
	 *
	 * @param string $component_id Component ID.
	 * @return string Component name.
	 */
	private function get_component_name( $component_id ) {
		$names = array(
			'logo'              => 'Logo',
			'menu-1'            => 'Primary Menu',
			'menu-2'            => 'Secondary Menu',
			'button-1'          => 'Button 1',
			'button-2'          => 'Button 2',
			'button-3'          => 'Button 3',
			'html-1'            => 'HTML 1',
			'html-2'            => 'HTML 2',
			'html-3'            => 'HTML 3',
			'widget-1'          => 'Widget 1',
			'widget-2'          => 'Widget 2',
			'widget-3'          => 'Widget 3',
			'social-icons-1'    => 'Social Icons',
			'social-icons-2'    => 'Social Icons 2',
			'search'            => 'Search',
			'mobile-menu'       => 'Mobile Menu',
			'mobile-trigger'    => 'Mobile Trigger',
			'woo-cart'          => 'WooCommerce Cart',
			'edd-cart'          => 'EDD Cart',
			'account'           => 'Account',
			'language-switcher' => 'Language Switcher',
		);

		return isset( $names[ $component_id ] ) ? $names[ $component_id ] : $component_id;
	}

	/**
	 * Get default desktop header structure with all zones initialized.
	 *
	 * @return array Default desktop structure with all zones as empty arrays.
	 */
	private function get_default_desktop_structure() {
		return array(
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
	}

	/**
	 * Get default mobile header structure with all zones initialized.
	 *
	 * @return array Default mobile structure with all zones as empty arrays.
	 */
	private function get_default_mobile_structure() {
		return array(
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
	}
}

Astra_Update_Header_Builder::register();
