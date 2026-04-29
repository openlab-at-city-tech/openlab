<?php
/**
 * Update Footer Builder Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Footer_Builder
 */
class Astra_Update_Footer_Builder extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-footer-builder';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Footer Builder Settings', 'astra' );
		$this->description = __( 'Updates the Astra theme footer builder configuration for desktop and mobile layouts. Allows positioning components like buttons, HTML blocks, widgets, social icons, footer menu, and copyright in above footer, primary footer, and below footer sections with column-based placements. Also configures column counts and layout widths for each footer section.', 'astra' );

		$this->meta = array(
			'tool_type'   => 'write',
			'constraints' => array(
				'dependencies' => array(
					array(
						'tool'             => 'astra/get-footer-builder',
						'description_hint' => 'Get current footer layout and available_components before updating',
					),
				),
				'usage_hints'  => array(
					'SMART_MERGE_WORKFLOW' => 'Uses SMART MERGING that auto-detects operation type. Call astra/get-footer-builder FIRST to see current layout. ADD operations (new components) append to existing. REMOVE operations (subset of current) keep only specified. CLEAR operations (empty array) remove all. NO need to manually reconstruct arrays for simple add/remove.',
					'component_ids'        => 'EXACT footer component IDs (case-sensitive, use exactly as shown): copyright, menu, widget-1, widget-2, widget-3, widget-4, social-icons-1 (NOT social-1!), social-icons-2, html-1, html-2, html-3, button-1, button-2. NEVER use shortened versions - use exact IDs from available_components.',
					'desktop_structure'    => 'Desktop has 3 sections: above (top), primary (main), below (bottom). Each section has 5 columns numbered 1-5. Column names: above_1, above_2, above_3, above_4, above_5, primary_1, primary_2, primary_3, primary_4, primary_5, below_1, below_2, below_3, below_4, below_5. The number of active columns is controlled by section_footer_columns parameters.',
					'mobile_structure'     => 'Mobile has 3 sections: above, primary, below. Each section has 2 columns. Column names: above_1, above_2, primary_1, primary_2, below_1, below_2. Mobile layout is INDEPENDENT from desktop - components must be explicitly placed for mobile.',
					'column_visibility'    => 'Active columns determined by: above_footer_columns (1-5), primary_footer_columns (1-5), below_footer_columns (1-5). Example: If primary_footer_columns=3, only primary_1, primary_2, primary_3 display on desktop. primary_4 and primary_5 are hidden. Set column count based on how many components you want to display horizontally.',
					'adding_components'    => 'To ADD a component: Just send the new component(s), they will be APPENDED to existing. Example: To add menu to below_1 (currently has ["copyright"]): {"desktop": {"below": {"below_1": ["menu"]}}} → Result: ["copyright", "menu"]. NO need to include existing components. Smart merge detects new items and appends them.',
					'removing_components'  => 'To REMOVE a specific component: Call get-footer-builder first, then send column with ONLY the components to KEEP (exclude ones to remove). Example: To remove widget-2 from primary_1 (currently has ["widget-1", "widget-2", "widget-3"]): {"desktop": {"primary": {"primary_1": ["widget-1", "widget-3"]}}}. Smart merge detects subset and removes missing items. To clear entire column: send empty array [].',
					'moving_components'    => 'To MOVE a component: Specify BOTH source and destination. Example: To move copyright from primary_1 to below_1 (primary_1 currently has ["copyright", "menu"]): {"desktop": {"primary": {"primary_1": ["menu"]}, "below": {"below_1": ["copyright"]}}}. Source gets remaining items (remove operation), destination gets new item (add operation).',
					'multiple_operations'  => 'Multiple operations in one update: Combine all changes. Example: Add menu to below_1 AND add widget-1 to primary_2: {"desktop": {"below": {"below_1": ["menu"]}, "primary": {"primary_2": ["widget-1"]}}}. Each will append to existing contents. To add to one column and remove from another: combine appropriately.',
					'column_arrays'        => 'Each column value is an ARRAY of component IDs. Single: ["copyright"]. Multiple: ["copyright", "menu"]. Empty: []. Smart merge behavior: NEW items (not in current) → APPEND. SUBSET of current → REMOVE (keep only specified). SAME → no change. Empty → CLEAR. Components stack vertically in column.',
					'layout_widths'        => 'Control section width: above_footer_layout, primary_footer_layout, below_footer_layout. Values: "full" (edge-to-edge) or "content" (boxed). Example: Full-width footer: set all to "full". Boxed footer: set all to "content". Mix: primary="full", below="content".',
					'common_mistakes'      => 'AVOID: 1) Using "social-1" instead of "social-icons-1" (wrong ID), 2) Sending duplicate items when adding (smart merge handles this), 3) Using column names incorrectly (e.g., "primary-1" instead of "primary_1"), 4) Not checking current layout before removing (might remove wrong items).',
					'typical_layouts'      => 'Common footer layouts: 1) 1-column: Copyright centered in below_1 (set below_footer_columns=1), 2) 3-column: Copyright in primary_1, Menu in primary_2, Social in primary_3 (set primary_footer_columns=3), 3) 4-column: Widget-1/2/3/4 in primary_1/2/3/4 (set primary_footer_columns=4), 4) Multi-row: Widgets in above (4-5 columns), Copyright in below (1 column).',
					'responsive_design'    => 'Desktop and mobile are INDEPENDENT. A component in desktop primary_1 does NOT automatically appear in mobile. Must explicitly configure both. Typical: Desktop 4-column footer, mobile 2-column footer. Configure both desktop and mobile layouts in same update or separately.',
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
				'desktop'                => array(
					'type'        => 'object',
					'description' => 'Desktop footer layout configuration with above, primary, and below sections. Each section contains numbered columns (1-5) with arrays of component IDs.',
					'properties'  => array(
						'above'   => array(
							'type'        => 'object',
							'description' => 'Above footer section columns.',
							'properties'  => array(
								'above_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_3' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_4' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_5' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'primary' => array(
							'type'        => 'object',
							'description' => 'Primary footer section columns.',
							'properties'  => array(
								'primary_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_3' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_4' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_5' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'below'   => array(
							'type'        => 'object',
							'description' => 'Below footer section columns.',
							'properties'  => array(
								'below_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_3' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_4' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_5' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
				'mobile'                 => array(
					'type'        => 'object',
					'description' => 'Mobile footer layout configuration with above, primary, and below sections. Each section contains numbered columns (1-2) with arrays of component IDs.',
					'properties'  => array(
						'above'   => array(
							'type'        => 'object',
							'description' => 'Above footer section columns for mobile.',
							'properties'  => array(
								'above_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'above_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'primary' => array(
							'type'        => 'object',
							'description' => 'Primary footer section columns for mobile.',
							'properties'  => array(
								'primary_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'primary_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
						'below'   => array(
							'type'        => 'object',
							'description' => 'Below footer section columns for mobile.',
							'properties'  => array(
								'below_1' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'below_2' => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
				'above_footer_columns'   => array(
					'type'        => 'integer',
					'description' => 'Number of columns for above footer section (1-5).',
				),
				'primary_footer_columns' => array(
					'type'        => 'integer',
					'description' => 'Number of columns for primary footer section (1-5).',
				),
				'below_footer_columns'   => array(
					'type'        => 'integer',
					'description' => 'Number of columns for below footer section (1-5).',
				),
				'above_footer_layout'    => array(
					'type'        => 'string',
					'description' => 'Above footer layout width. Options: "full" (Full Width), "content" (Content Width).',
					'enum'        => array( 'full', 'content' ),
				),
				'primary_footer_layout'  => array(
					'type'        => 'string',
					'description' => 'Primary footer layout width. Options: "full" (Full Width), "content" (Content Width).',
					'enum'        => array( 'full', 'content' ),
				),
				'below_footer_layout'    => array(
					'type'        => 'string',
					'description' => 'Below footer layout width. Options: "full" (Full Width), "content" (Content Width).',
					'enum'        => array( 'full', 'content' ),
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
			'add copyright to primary footer column 1',
			'add menu to primary footer column 2',
			'add social-icons-1 to above footer column 1',
			'add widget-1 to primary footer column 3',
			'add html-1 to below footer column 1',
			'add button-1 to primary footer column 4',
			'add widget-2 to above footer column 2',

			// Moving components.
			'move copyright from primary to below footer',
			'move menu to above footer column 1',
			'move social-icons-1 from above to primary footer',
			'move widget-1 to below footer column 1',

			// Specific column placements.
			'place copyright in primary_1',
			'place menu in primary_2',
			'place social-icons-1 in above_3',
			'place widget-1 in primary_4',
			'place html-1 in below_1',

			// Column configuration.
			'set primary footer to 3 columns',
			'set above footer to 4 columns',
			'set below footer to 1 column',
			'configure footer with 4 columns',

			// Layout width.
			'set primary footer to full width',
			'set above footer to content width',
			'make footer full width',
			'set below footer layout to content',

			// Multiple components.
			'add copyright and menu to primary footer',
			'add widget-1 and social-icons-1 to footer',
			'place copyright in column 1 and menu in column 2',

			// Complex operations.
			'move copyright to below footer and add menu to primary footer',
			'add copyright to primary_1, menu to primary_2, and social-icons-1 to primary_3',
			'configure 3-column footer with copyright left, menu center, social right',

			// Removing components.
			'remove copyright from footer',
			'remove menu from primary footer',
			'clear above footer',

			// Mobile configurations.
			'add copyright to mobile footer column 1',
			'configure mobile footer with menu in column 1',
			'add social-icons-1 to mobile footer column 2',

			// Multi-section.
			'add widget-1 to above footer and copyright to below footer',
			'place menu in primary and social in above footer',
			'configure footer with components in above, primary, and below sections',

			// Combined operations.
			'set footer to 4 columns and add copyright, menu, social-icons-1, widget-1',
			'configure full-width footer with 3 columns',
			'reorganize footer with copyright center and menu left',
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

		// Check if astra_get_option function exists (required for proper data retrieval).
		if ( ! function_exists( 'astra_get_option' ) || ! function_exists( 'astra_update_option' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme helper functions not available.', 'astra' ),
				__( 'The astra_get_option and astra_update_option functions are required but not found.', 'astra' )
			);
		}

		$updated          = false;
		$update_messages  = array();
		$detailed_changes = array();

		if ( isset( $args['desktop'] ) && is_array( $args['desktop'] ) ) {
			// Get current desktop structure from database using Astra's helper function.
			$current_desktop_raw = astra_get_option( 'footer-desktop-items', array() );
			if ( ! is_array( $current_desktop_raw ) ) {
				$current_desktop_raw = array();
			}

			$default_desktop = $this->get_default_desktop_structure();
			// Merge current values with defaults to ensure all columns exist.
			$current_desktop = $this->array_merge_recursive_distinct( $default_desktop, $current_desktop_raw );

			$desktop_layout = $this->sanitize_footer_layout( $args['desktop'], 'desktop' );

			$changes = $this->get_layout_changes( $current_desktop, $desktop_layout, 'desktop' );
			if ( ! empty( $changes ) ) {
				$detailed_changes = array_merge( $detailed_changes, $changes );
			}

			$new_desktop = $this->array_merge_recursive_distinct( $current_desktop, $desktop_layout );

			// Update the option using Astra's update method to ensure proper storage.
			astra_update_option( 'footer-desktop-items', $new_desktop );
			$updated           = true;
			$update_messages[] = 'Desktop footer layout updated';
		}

		if ( isset( $args['mobile'] ) && is_array( $args['mobile'] ) ) {
			$current_mobile_raw = astra_get_option( 'footer-mobile-items', array() );
			if ( ! is_array( $current_mobile_raw ) ) {
				$current_mobile_raw = array();
			}

			// Initialize default mobile structure with all columns to prevent data loss during merge.
			$default_mobile = $this->get_default_mobile_structure();
			$current_mobile = $this->array_merge_recursive_distinct( $default_mobile, $current_mobile_raw );

			$mobile_layout = $this->sanitize_footer_layout( $args['mobile'], 'mobile' );

			$changes = $this->get_layout_changes( $current_mobile, $mobile_layout, 'mobile' );
			if ( ! empty( $changes ) ) {
				$detailed_changes = array_merge( $detailed_changes, $changes );
			}

			// Merge current with new layout to get final result.
			$new_mobile = $this->array_merge_recursive_distinct( $current_mobile, $mobile_layout );

			astra_update_option( 'footer-mobile-items', $new_mobile );
			$updated           = true;
			$update_messages[] = 'Mobile footer layout updated';
		}

		if ( isset( $args['above_footer_columns'] ) ) {
			$columns = absint( $args['above_footer_columns'] );
			if ( $columns < 1 || $columns > 5 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: column count */
					sprintf( __( 'Invalid above_footer_columns: %d.', 'astra' ), $columns ),
					__( 'Value must be between 1 and 5.', 'astra' )
				);
			}
			astra_update_option( 'hba-footer-column', (string) $columns );
			$updated = true;
			/* translators: %d: column count */
			$update_messages[] = sprintf( __( 'Above footer columns set to %d', 'astra' ), $columns );
		}

		if ( isset( $args['primary_footer_columns'] ) ) {
			$columns = absint( $args['primary_footer_columns'] );
			if ( $columns < 1 || $columns > 5 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: column count */
					sprintf( __( 'Invalid primary_footer_columns: %d.', 'astra' ), $columns ),
					__( 'Value must be between 1 and 5.', 'astra' )
				);
			}
			astra_update_option( 'hb-footer-column', (string) $columns );
			$updated = true;
			/* translators: %d: column count */
			$update_messages[] = sprintf( __( 'Primary footer columns set to %d', 'astra' ), $columns );
		}

		if ( isset( $args['below_footer_columns'] ) ) {
			$columns = absint( $args['below_footer_columns'] );
			if ( $columns < 1 || $columns > 5 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: column count */
					sprintf( __( 'Invalid below_footer_columns: %d.', 'astra' ), $columns ),
					__( 'Value must be between 1 and 5.', 'astra' )
				);
			}
			astra_update_option( 'hbb-footer-column', (string) $columns );
			$updated = true;
			/* translators: %d: column count */
			$update_messages[] = sprintf( __( 'Below footer columns set to %d', 'astra' ), $columns );
		}

		if ( isset( $args['above_footer_layout'] ) && ! empty( $args['above_footer_layout'] ) ) {
			$layout        = sanitize_text_field( $args['above_footer_layout'] );
			$valid_layouts = array( 'full', 'content' );
			if ( ! in_array( $layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: layout value */
					sprintf( __( 'Invalid above_footer_layout: %s.', 'astra' ), $layout ),
					__( 'Valid options: full, content', 'astra' )
				);
			}
			astra_update_option( 'hba-footer-layout', $layout );
			$updated = true;
			/* translators: %s: layout value */
			$update_messages[] = sprintf( __( 'Above footer layout set to %s', 'astra' ), $layout );
		}

		if ( isset( $args['primary_footer_layout'] ) && ! empty( $args['primary_footer_layout'] ) ) {
			$layout        = sanitize_text_field( $args['primary_footer_layout'] );
			$valid_layouts = array( 'full', 'content' );
			if ( ! in_array( $layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: layout value */
					sprintf( __( 'Invalid primary_footer_layout: %s.', 'astra' ), $layout ),
					__( 'Valid options: full, content', 'astra' )
				);
			}
			astra_update_option( 'hb-footer-layout', $layout );
			$updated = true;
			/* translators: %s: layout value */
			$update_messages[] = sprintf( __( 'Primary footer layout set to %s', 'astra' ), $layout );
		}

		if ( isset( $args['below_footer_layout'] ) && ! empty( $args['below_footer_layout'] ) ) {
			$layout        = sanitize_text_field( $args['below_footer_layout'] );
			$valid_layouts = array( 'full', 'content' );
			if ( ! in_array( $layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: layout value */
					sprintf( __( 'Invalid below_footer_layout: %s.', 'astra' ), $layout ),
					__( 'Valid options: full, content', 'astra' )
				);
			}
			astra_update_option( 'hbb-footer-layout', $layout );
			$updated = true;
			/* translators: %s: layout value */
			$update_messages[] = sprintf( __( 'Below footer layout set to %s', 'astra' ), $layout );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update.', 'astra' )
			);
		}

		// Build detailed message.
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
	 * Sanitize footer layout data.
	 *
	 * @param array  $layout Layout data to sanitize.
	 * @param string $type   Device type (desktop or mobile).
	 * @return array Sanitized layout data.
	 */
	private function sanitize_footer_layout( $layout, $type = 'desktop' ) {
		$sanitized = array();
		$sections  = array( 'above', 'primary', 'below' );

		$desktop_columns = array(
			'above'   => array( 'above_1', 'above_2', 'above_3', 'above_4', 'above_5', 'above_6' ),
			'primary' => array( 'primary_1', 'primary_2', 'primary_3', 'primary_4', 'primary_5', 'primary_6' ),
			'below'   => array( 'below_1', 'below_2', 'below_3', 'below_4', 'below_5', 'below_6' ),
		);

		$mobile_columns = array(
			'above'   => array( 'above_1', 'above_2' ),
			'primary' => array( 'primary_1', 'primary_2' ),
			'below'   => array( 'below_1', 'below_2' ),
		);

		$columns = 'desktop' === $type ? $desktop_columns : $mobile_columns;

		foreach ( $sections as $section ) {
			if ( isset( $layout[ $section ] ) && is_array( $layout[ $section ] ) ) {
				$section_columns = array();
				foreach ( $columns[ $section ] as $column ) {
					if ( isset( $layout[ $section ][ $column ] ) && is_array( $layout[ $section ][ $column ] ) ) {
						// Normalize component IDs (e.g., widget -> widget-1).
						$normalized = array_map( array( $this, 'normalize_component_id' ), $layout[ $section ][ $column ] );

						$section_columns[ $column ] = $normalized;
					}
				}

				// Only include section if it has columns.
				if ( ! empty( $section_columns ) ) {
					$sanitized[ $section ] = $section_columns;
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Normalize component ID to match Astra's expected format for footer components.
	 *
	 * @param string $component_id Component ID to normalize.
	 * @return string Normalized component ID.
	 */
	private function normalize_component_id( $component_id ) {
		$component_id = sanitize_text_field( $component_id );
		$component_id = strtolower( trim( $component_id ) );

		$id_mapping = array(
			'widget'         => 'widget-1',
			'widget_1'       => 'widget-1',
			'widget_2'       => 'widget-2',
			'widget_3'       => 'widget-3',
			'widget_4'       => 'widget-4',
			'html'           => 'html-1',
			'html_1'         => 'html-1',
			'html_2'         => 'html-2',
			'html_3'         => 'html-3',
			'button'         => 'button-1',
			'btn'            => 'button-1',
			'button_1'       => 'button-1',
			'button_2'       => 'button-2',
			'btn-1'          => 'button-1',
			'btn-2'          => 'button-2',
			'social'         => 'social-icons-1',
			'social_icons'   => 'social-icons-1',
			'social-icons'   => 'social-icons-1',
			'social_icons_1' => 'social-icons-1',
			'social_1'       => 'social-icons-1',
			'social-1'       => 'social-icons-1',
			'social_icons_2' => 'social-icons-2',
			'social_2'       => 'social-icons-2',
			'social-2'       => 'social-icons-2',
			'footer_menu'    => 'menu',
			'footer-menu'    => 'menu',
			'nav'            => 'menu',
			'navigation'     => 'menu',
		);

		if ( isset( $id_mapping[ $component_id ] ) ) {
			return $id_mapping[ $component_id ];
		}

		return $component_id;
	}

	/**
	 * Recursively merge two arrays, with SMART MERGING for component arrays.
	 * Unlike array_merge_recursive, this doesn't create nested arrays for duplicate keys.
	 * Component arrays at column level use intelligent merge that auto-detects operation type.
	 *
	 * Structure: {section: {column: [components]}}
	 * - Depth 0: sections (above, primary, below)
	 * - Depth 1: columns (primary_1, primary_2, etc) with component arrays
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
						$current = $merged[ $key ];
						$new     = $value;

						// Case 1: Empty array = CLEAR column.
						if ( empty( $new ) ) {
							$merged[ $key ] = array();
						} elseif ( ! empty( array_diff( $new, $current ) ) ) {
							// Case 2: New has items not in current = ADD operation (append).
							$merged[ $key ] = array_values( array_unique( array_merge( $current, $new ) ) );
						} elseif ( ! empty( $current ) && empty( array_diff( $new, $current ) ) && count( $new ) < count( $current ) ) {
							$merged[ $key ] = array_values( array_unique( $new ) );
						} else {
							$merged[ $key ] = array_values( array_unique( $new ) );
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
	 * Get default desktop footer structure with all columns initialized.
	 *
	 * @return array Default desktop structure with all columns as empty arrays.
	 */
	private function get_default_desktop_structure() {
		return array(
			'above'   => array(
				'above_1' => array(),
				'above_2' => array(),
				'above_3' => array(),
				'above_4' => array(),
				'above_5' => array(),
				'above_6' => array(),
			),
			'primary' => array(
				'primary_1' => array(),
				'primary_2' => array(),
				'primary_3' => array(),
				'primary_4' => array(),
				'primary_5' => array(),
				'primary_6' => array(),
			),
			'below'   => array(
				'below_1' => array(),
				'below_2' => array(),
				'below_3' => array(),
				'below_4' => array(),
				'below_5' => array(),
				'below_6' => array(),
			),
		);
	}

	/**
	 * Get default mobile footer structure with all columns initialized.
	 *
	 * @return array Default mobile structure with all columns as empty arrays.
	 */
	private function get_default_mobile_structure() {
		return array(
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
	}

	/**
	 * Get detailed changes made to the layout for user feedback.
	 * Compares current layout with new layout to identify what was added/removed/moved.
	 *
	 * @param array  $current_layout Current layout structure.
	 * @param array  $new_layout New layout being applied.
	 * @param string $device Device type (desktop or mobile).
	 * @return array Array of change descriptions.
	 */
	private function get_layout_changes( $current_layout, $new_layout, $device ) {
		$changes = array();

		foreach ( $new_layout as $section => $columns ) {
			if ( ! is_array( $columns ) ) {
				continue;
			}

			foreach ( $columns as $column => $components ) {
				if ( ! is_array( $components ) ) {
					continue;
				}

				$existing = isset( $current_layout[ $section ][ $column ] ) && is_array( $current_layout[ $section ][ $column ] )
					? $current_layout[ $section ][ $column ]
					: array();

				$column_label = $this->get_column_label( $section, $column );

				if ( empty( $components ) && ! empty( $existing ) ) {
					$existing_names = array_map( array( $this, 'get_component_name' ), $existing );
					$changes[]      = sprintf(
						'Removed %s from %s (%s)',
						implode( ', ', $existing_names ),
						$column_label,
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
								$column_label,
								$device,
								implode( ', ', $existing_names )
							);
						} else {
							$changes[] = sprintf(
								'Added %s to %s (%s)',
								implode( ', ', $component_names ),
								$column_label,
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
	 * Get human-readable column label.
	 *
	 * @param string $section Section name (above, primary, below).
	 * @param string $column Column name (e.g., primary_1, above_2).
	 * @return string Formatted column label.
	 */
	private function get_column_label( $section, $column ) {
		$section_labels = array(
			'above'   => 'Above Footer',
			'primary' => 'Primary Footer',
			'below'   => 'Below Footer',
		);

		// Extract column number from column name (e.g., primary_1 -> 1).
		$column_number = str_replace( $section . '_', '', $column );

		$section_label = isset( $section_labels[ $section ] ) ? $section_labels[ $section ] : $section;

		return sprintf( '%s Column %s', $section_label, $column_number );
	}

	/**
	 * Get human-readable component name.
	 *
	 * @param string $component_id Component ID.
	 * @return string Component name.
	 */
	private function get_component_name( $component_id ) {
		$names = array(
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

		return isset( $names[ $component_id ] ) ? $names[ $component_id ] : $component_id;
	}
}

Astra_Update_Footer_Builder::register();
