<?php
/**
 * List Header Builder Settings Ability
 *
 * Lists all available header builder widgets, current layout configuration,
 * and metadata about the header builder structure.
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_List_Header_Builder_Settings
 *
 * Provides comprehensive information about the header builder including:
 * - Available widgets/elements in the free theme
 * - Current desktop and mobile header layouts
 * - Widget metadata (sections, limits, types)
 * - Available positions for each header row
 */
class Astra_List_Header_Builder_Settings extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/list-header-builder-setting';
		$this->category    = 'astra';
		$this->label       = __( 'List Header Builder Settings', 'astra' );
		$this->description = __( 'Lists all available header builder widgets/elements, current layout configuration, available positions, and complete metadata about the header builder structure. Shows which widgets can be added, current widget placements in all header positions (above/primary/below headers), and position availability for both desktop and mobile headers. Use this to discover what elements are available, what are already in use, and what can be added to the header.', 'astra' );
		$this->version     = '1.0.0';
		$this->meta        = array(
			'tool_type'   => 'list',
			'constraints' => array(
				'usage_hints' => array(
					'purpose'              => 'Use this tool to discover available header widgets/elements, see current header configuration, and understand what can be added where',
					'when_to_use'          => 'Call this when user asks: "what widgets are available", "what elements can I add", "show header options", "list header widgets", "what is in my header", "which widgets are being used"',
					'widget_limits'        => 'Free theme limits: 1 button, 2 HTML elements, 2 widget areas, 2 menus, 1 social icons set. Cannot exceed these limits.',
					'positions'            => 'Desktop has 5 positions per row: left, left_center, center, right_center, right. Mobile has 3 positions: left, center, right',
					'rows'                 => 'Three header rows: above (top), primary (main/middle), below (bottom). Each can contain widgets.',
					'response_includes'    => 'Available widgets list, current layouts for desktop and mobile, widget limits, position information, and which widgets are currently placed',
					'understanding_layout' => 'The response shows BOTH available widgets AND current placements, so you can answer questions about what is available and what is currently being used',
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
		return 'list';
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
				'detailed' => array(
					'type'        => 'boolean',
					'description' => 'Whether to include detailed metadata about each widget and position. Default: true',
					'default'     => true,
				),
				'device'   => array(
					'type'        => 'string',
					'description' => 'Filter by device type: desktop, mobile, or both. Default: both',
					'enum'        => array( 'desktop', 'mobile', 'both' ),
					'default'     => 'both',
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
			// Basic listing.
			'list header builder settings',
			'show available header widgets',
			'get header builder options',
			'list header builder widgets',
			'show header widget options',
			'list header elements',
			'show available header elements',
			'get available header components',
			'list available header options',
			'show header builder configuration',

			// What can be added.
			'what widgets can I add to header',
			'what elements can I add to header',
			'what can I add to header',
			'what components are available for header',
			'what options do I have for header',
			'which widgets are available',
			'which elements can be used in header',
			'show me what I can add to header',
			'what are my header options',
			'what widgets does astra header support',

			// Current state questions.
			'what widgets are in header',
			'what is in my header',
			'what elements are currently in header',
			'which widgets are being used',
			'which elements are in use',
			'show current header widgets',
			'what is currently in the header',
			'which widgets are placed in header',
			'show me what is in header right now',
			'what widgets are already added',

			// Structure and layout.
			'show header structure',
			'get header configuration',
			'show header builder layout',
			'list header positions',
			'what are the header sections',
			'what header rows are available',
			'show header builder structure',
			'explain header layout options',

			// Specific questions.
			'how many buttons can I add',
			'what are the widget limits',
			'can I add more menus',
			'what is the limit for HTML widgets',
			'show widget restrictions',
			'what are the free theme limits',
			'how many widgets can I add',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$detailed = isset( $args['detailed'] ) ? (bool) $args['detailed'] : true;
		$device   = isset( $args['device'] ) ? sanitize_text_field( $args['device'] ) : 'both';

		// Get available widgets in free theme.
		$available_widgets = $this->get_available_widgets( $detailed );

		// Get current header layouts.
		$layouts = array();
		if ( 'desktop' === $device || 'both' === $device ) {
			$layouts['desktop'] = $this->get_current_layout( 'desktop', $detailed );
		}
		if ( 'mobile' === $device || 'both' === $device ) {
			$layouts['mobile'] = $this->get_current_layout( 'mobile', $detailed );
		}

		// Get header structure metadata.
		$structure = $this->get_header_structure( $detailed );

		// Build response data.
		$response_data = array(
			'available_widgets' => $available_widgets,
			'current_layouts'   => $layouts,
			'structure'         => $structure,
			'summary'           => array(
				'total_available_widgets' => count( $available_widgets ),
				'widget_limits'           => array(
					'buttons'      => 1,
					'html'         => 2,
					'widgets'      => 2,
					'menus'        => 2,
					'social_icons' => 1,
				),
				'header_rows'             => array( 'above', 'primary', 'below' ),
				'desktop_positions'       => 5,
				'mobile_positions'        => 3,
			),
		);

		if ( $detailed ) {
			$response_data['usage_notes'] = array(
				'Adding widgets'  => 'Use the update-header-builder-layout tool to add widgets to specific positions',
				'Widget IDs'      => 'Use the widget ID from available_widgets (e.g., "button-1", "menu-1", "logo")',
				'Position format' => 'Format: {row}_{position} (e.g., "primary_left", "above_center")',
				'Cloneable limit' => 'Cloneable widgets (button, html, widget, menu) are limited in the free theme',
				'Mobile specific' => 'mobile-trigger and mobile-menu are mobile-only elements',
			);
		}

		return Astra_Abilities_Response::success(
			sprintf(
				/* translators: 1: widget count, 2: layout count */
				__( 'Retrieved header builder settings. %1$d widgets available, %2$d layouts configured.', 'astra' ),
				count( $available_widgets ),
				count( $layouts )
			),
			$response_data
		);
	}

	/**
	 * Get available widgets in free theme.
	 *
	 * @param bool $detailed Whether to include detailed metadata.
	 * @return array Available widgets.
	 */
	private function get_available_widgets( $detailed ) {
		$widgets = array(
			'logo'           => array(
				'name'      => 'Site Title & Logo',
				'type'      => 'core',
				'cloneable' => false,
				'section'   => 'title_tagline',
				'limit'     => 1,
			),
			'search'         => array(
				'name'      => 'Search',
				'type'      => 'core',
				'cloneable' => false,
				'section'   => 'section-header-search',
				'limit'     => 1,
			),
			'account'        => array(
				'name'      => 'Account',
				'type'      => 'core',
				'cloneable' => false,
				'section'   => 'section-header-account',
				'limit'     => 1,
			),
			'button-1'       => array(
				'name'      => 'Button',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-button-1',
				'limit'     => 1,
			),
			'html-1'         => array(
				'name'      => 'HTML 1',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-html-1',
				'limit'     => 2,
			),
			'html-2'         => array(
				'name'      => 'HTML 2',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-html-2',
				'limit'     => 2,
			),
			'widget-1'       => array(
				'name'      => 'Widget 1',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'sidebar-widgets-header-widget-1',
				'limit'     => 2,
			),
			'widget-2'       => array(
				'name'      => 'Widget 2',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'sidebar-widgets-header-widget-2',
				'limit'     => 2,
			),
			'menu-1'         => array(
				'name'      => 'Primary Menu',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-menu-1',
				'limit'     => 2,
			),
			'menu-2'         => array(
				'name'      => 'Secondary Menu',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-menu-2',
				'limit'     => 2,
			),
			'social-icons-1' => array(
				'name'      => 'Social Icons',
				'type'      => 'cloneable',
				'cloneable' => true,
				'section'   => 'section-hb-social-icons-1',
				'limit'     => 1,
			),
			'mobile-trigger' => array(
				'name'      => 'Mobile Toggle Button',
				'type'      => 'mobile',
				'cloneable' => false,
				'section'   => 'section-header-mobile-trigger',
				'limit'     => 1,
			),
			'mobile-menu'    => array(
				'name'      => 'Mobile Off-Canvas Menu',
				'type'      => 'mobile',
				'cloneable' => false,
				'section'   => 'section-header-mobile-menu',
				'limit'     => 1,
			),
		);

		// Add WooCommerce cart if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			$widgets['woo-cart'] = array(
				'name'      => 'WooCommerce Cart',
				'type'      => 'conditional',
				'cloneable' => false,
				'section'   => 'section-header-woo-cart',
				'limit'     => 1,
			);
		}

		// Add EDD cart if Easy Digital Downloads is active.
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			$widgets['edd-cart'] = array(
				'name'      => 'EDD Cart',
				'type'      => 'conditional',
				'cloneable' => false,
				'section'   => 'section-header-edd-cart',
				'limit'     => 1,
			);
		}

		if ( $detailed ) {
			foreach ( $widgets as $id => &$widget ) {
				$widget['id']             = $id;
				$widget['description']    = $this->get_widget_description( $id );
				$widget['device_support'] = $this->get_widget_device_support( $id );
			}
		}

		return $widgets;
	}

	/**
	 * Get current header layout.
	 *
	 * @param string $device   Device type (desktop or mobile).
	 * @param bool   $detailed Whether to include detailed metadata.
	 * @return array Current layout.
	 */
	private function get_current_layout( $device, $detailed ) {
		$option_key = 'desktop' === $device ? 'header-desktop-items' : 'header-mobile-items';
		$layout     = astra_get_option( $option_key, array() );

		$rows      = array( 'above', 'primary', 'below' );
		$positions = 'desktop' === $device
			? array( 'left', 'left_center', 'center', 'right_center', 'right' )
			: array( 'left', 'center', 'right' );

		$current_layout = array(
			'device' => $device,
			'rows'   => array(),
		);

		foreach ( $rows as $row ) {
			$row_data = array(
				'row'       => $row,
				'positions' => array(),
			);

			foreach ( $positions as $position ) {
				$position_key = "{$row}_{$position}";
				$widgets      = isset( $layout[ $row ][ $position_key ] ) ? $layout[ $row ][ $position_key ] : array();

				$row_data['positions'][ $position ] = array(
					'position_key' => $position_key,
					'widgets'      => $widgets,
					'widget_count' => count( $widgets ),
					'is_empty'     => empty( $widgets ),
				);

				if ( $detailed && ! empty( $widgets ) ) {
					$row_data['positions'][ $position ]['widget_details'] = array_map(
						function ( string $widget_id ) {
							return array(
								'id'   => $widget_id,
								'name' => $this->get_widget_name( $widget_id ),
							);
						},
						$widgets
					);
				}
			}

			$current_layout['rows'][ $row ] = $row_data;
		}

		// Add popup (mobile menu container).
		if ( isset( $layout['popup'] ) ) {
			$current_layout['popup'] = $layout['popup'];
		}

		return $current_layout;
	}

	/**
	 * Get header structure metadata.
	 *
	 * @param bool $detailed Whether to include detailed metadata.
	 * @return array Header structure.
	 */
	private function get_header_structure( $detailed ) {
		$structure = array(
			'rows'              => array(
				'above'   => array(
					'label'       => 'Above Header',
					'option_name' => 'ast-hfb-above-header-display',
					'section'     => 'section-above-header-builder',
				),
				'primary' => array(
					'label'       => 'Primary Header',
					'option_name' => 'ast-main-header-display',
					'section'     => 'section-primary-header-builder',
				),
				'below'   => array(
					'label'       => 'Below Header',
					'option_name' => 'ast-hfb-below-header-display',
					'section'     => 'section-below-header-builder',
				),
			),
			'desktop_positions' => array(
				'left'         => 'Left',
				'left_center'  => 'Left Center',
				'center'       => 'Center',
				'right_center' => 'Right Center',
				'right'        => 'Right',
			),
			'mobile_positions'  => array(
				'left'   => 'Left',
				'center' => 'Center',
				'right'  => 'Right',
			),
		);

		if ( $detailed ) {
			$structure['notes'] = array(
				'Widget placement'    => 'Each widget can only be in one position at a time',
				'Cloneable limits'    => 'Cloneable widgets are limited in free theme (check widget_limits in summary)',
				'Default layout'      => 'Primary header typically has logo on left and menu on right',
				'Mobile optimization' => 'Mobile headers have fewer positions for better mobile experience',
			);
		}

		return $structure;
	}

	/**
	 * Get widget description.
	 *
	 * @param string $widget_id Widget ID.
	 * @return string Description.
	 */
	private function get_widget_description( $widget_id ) {
		$descriptions = array(
			'logo'           => 'Displays site title, logo, and tagline',
			'search'         => 'Search form for site content',
			'account'        => 'User account/login link',
			'button-1'       => 'Customizable call-to-action button',
			'html-1'         => 'Custom HTML/text content area',
			'html-2'         => 'Custom HTML/text content area',
			'widget-1'       => 'WordPress widget area',
			'widget-2'       => 'WordPress widget area',
			'menu-1'         => 'Navigation menu (usually primary)',
			'menu-2'         => 'Navigation menu (usually secondary)',
			'social-icons-1' => 'Social media icon links',
			'mobile-trigger' => 'Hamburger menu button for mobile',
			'mobile-menu'    => 'Off-canvas mobile navigation',
			'woo-cart'       => 'WooCommerce shopping cart icon',
			'edd-cart'       => 'Easy Digital Downloads cart icon',
		);

		return isset( $descriptions[ $widget_id ] ) ? $descriptions[ $widget_id ] : '';
	}

	/**
	 * Get widget device support.
	 *
	 * @param string $widget_id Widget ID.
	 * @return array Device support.
	 */
	private function get_widget_device_support( $widget_id ) {
		$mobile_only = array( 'mobile-trigger', 'mobile-menu' );

		if ( in_array( $widget_id, $mobile_only, true ) ) {
			return array( 'mobile' );
		}

		return array( 'desktop', 'mobile' );
	}

	/**
	 * Get widget name.
	 *
	 * @param string $widget_id Widget ID.
	 * @return string Widget name.
	 */
	private function get_widget_name( $widget_id ) {
		$widgets = $this->get_available_widgets( false );
		return isset( $widgets[ $widget_id ]['name'] ) ? $widgets[ $widget_id ]['name'] : $widget_id;
	}
}

Astra_List_Header_Builder_Settings::register();
