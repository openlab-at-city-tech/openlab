<?php
/**
 * List Container Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_List_Container_Settings
 */
class Astra_List_Container_Settings extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/list-container-setting';
		$this->label       = __( 'List Astra Container Settings', 'astra' );
		$this->description = __( 'Lists all available Astra theme container and layout settings including container layout options, container styles, width configurations, background settings, and their current values. Provides comprehensive information about site layout structure.', 'astra' );
		$this->category    = 'astra';
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
					'description' => __( 'Whether to include detailed descriptions and available options for each setting. Default is true.', 'astra' ),
					'default'     => true,
				),
			),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'summary'        => array(
					'type'        => 'object',
					'description' => 'Quick overview of current container settings.',
				),
				'settings'       => array(
					'type'        => 'object',
					'description' => 'Detailed container settings with values, labels, and metadata.',
				),
				'total_settings' => array(
					'type'        => 'integer',
					'description' => 'Total number of settings returned.',
				),
				'detailed'       => array(
					'type'        => 'boolean',
					'description' => 'Whether detailed information was included.',
				),
				'notes'          => array(
					'type'        => 'array',
					'description' => 'Helpful notes about container settings.',
					'items'       => array( 'type' => 'string' ),
				),
			)
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'list all container settings',
			'show all container options',
			'display container configuration',
			'view all layout settings',
			'get container settings list',
			'show available container layouts',
			'list container layout options',
			'display all container styles',
			'view container width settings',
			'get complete container settings',
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

		// Get current values.
		$container_layout       = astra_get_option( 'ast-site-content-layout', 'normal-width-container' );
		$container_style        = astra_get_option( 'site-content-style', 'boxed' );
		$site_content_width     = astra_get_option( 'site-content-width', 1200 );
		$narrow_container_width = astra_get_option( 'narrow-container-max-width', 750 );
		$site_layout            = astra_get_option( 'site-layout', 'ast-full-width-layout' );
		$site_sidebar_width     = astra_get_option( 'site-sidebar-width', 30 );
		$site_bg                = astra_get_option( 'site-layout-outside-bg-obj-responsive' );
		$content_bg             = astra_get_option( 'content-bg-obj-responsive' );

		// Layout labels.
		$layout_labels = array(
			'normal-width-container' => __( 'Normal', 'astra' ),
			'narrow-width-container' => __( 'Narrow', 'astra' ),
			'full-width-container'   => __( 'Full Width', 'astra' ),
		);

		// Style labels.
		$style_labels = array(
			'boxed'   => __( 'Boxed', 'astra' ),
			'unboxed' => __( 'Unboxed', 'astra' ),
		);

		// Site layout labels.
		$site_layout_labels = array(
			'ast-full-width-layout' => __( 'Full Width', 'astra' ),
			'ast-padded-layout'     => __( 'Padded', 'astra' ),
			'ast-fluid-layout'      => __( 'Fluid', 'astra' ),
		);

		// Build settings array.
		$settings = array(
			'container_layout'       => array(
				'value'        => $container_layout,
				'label'        => isset( $layout_labels[ $container_layout ] ) ? $layout_labels[ $container_layout ] : $container_layout,
				'setting_name' => 'ast-site-content-layout',
				'type'         => 'layout',
			),
			'container_style'        => array(
				'value'        => $container_style,
				'label'        => isset( $style_labels[ $container_style ] ) ? $style_labels[ $container_style ] : $container_style,
				'setting_name' => 'site-content-style',
				'type'         => 'style',
			),
			'site_content_width'     => array(
				'value'        => $site_content_width,
				'label'        => $site_content_width . 'px',
				'setting_name' => 'site-content-width',
				'type'         => 'dimension',
			),
			'narrow_container_width' => array(
				'value'        => $narrow_container_width,
				'label'        => $narrow_container_width . 'px',
				'setting_name' => 'narrow-container-max-width',
				'type'         => 'dimension',
			),
			'site_layout'            => array(
				'value'        => $site_layout,
				'label'        => isset( $site_layout_labels[ $site_layout ] ) ? $site_layout_labels[ $site_layout ] : $site_layout,
				'setting_name' => 'site-layout',
				'type'         => 'layout',
			),
			'site_sidebar_width'     => array(
				'value'        => $site_sidebar_width,
				'label'        => $site_sidebar_width . '%',
				'setting_name' => 'site-sidebar-width',
				'type'         => 'dimension',
			),
			'site_background'        => array(
				'value'        => $site_bg,
				'setting_name' => 'site-layout-outside-bg-obj-responsive',
				'type'         => 'background',
			),
			'content_background'     => array(
				'value'        => $content_bg,
				'setting_name' => 'content-bg-obj-responsive',
				'type'         => 'background',
			),
		);

		// Add detailed information if requested.
		if ( $detailed ) {
			$settings['container_layout']['description']       = __( 'Controls the width of the content area across the site. Choose from Normal, Narrow, or Full Width layouts.', 'astra' );
			$settings['container_layout']['available_options'] = array(
				'normal-width-container' => array(
					'label'       => __( 'Normal', 'astra' ),
					'description' => __( 'Standard width container for general content.', 'astra' ),
				),
				'narrow-width-container' => array(
					'label'       => __( 'Narrow', 'astra' ),
					'description' => __( 'Narrower container ideal for blog posts and text-heavy content.', 'astra' ),
				),
				'full-width-container'   => array(
					'label'       => __( 'Full Width', 'astra' ),
					'description' => __( 'Full-width stretched container that spans the entire viewport.', 'astra' ),
				),
			);

			$settings['container_style']['description']       = __( 'Defines the visual style of the content container. Only applies when layout is Normal or Narrow.', 'astra' );
			$settings['container_style']['available_options'] = array(
				'boxed'   => array(
					'label'       => __( 'Boxed', 'astra' ),
					'description' => __( 'Content has padding and background, creating a boxed appearance.', 'astra' ),
				),
				'unboxed' => array(
					'label'       => __( 'Unboxed', 'astra' ),
					'description' => __( 'Plain style without padding, content flows naturally.', 'astra' ),
				),
			);

			$settings['site_content_width']['description']  = __( 'Maximum width of the normal container in pixels. Default is 1200px.', 'astra' );
			$settings['site_content_width']['default']      = 1200;
			$settings['site_content_width']['unit']         = 'px';
			$settings['site_content_width']['setting_type'] = 'numeric';

			$settings['narrow_container_width']['description']  = __( 'Maximum width of the narrow container in pixels. Default is 750px.', 'astra' );
			$settings['narrow_container_width']['default']      = 750;
			$settings['narrow_container_width']['unit']         = 'px';
			$settings['narrow_container_width']['setting_type'] = 'numeric';

			$settings['site_layout']['description']       = __( 'Site-wide layout style that affects the overall page structure.', 'astra' );
			$settings['site_layout']['available_options'] = array(
				'ast-full-width-layout' => array(
					'label'       => __( 'Full Width', 'astra' ),
					'description' => __( 'Content spans full width without outer spacing.', 'astra' ),
				),
				'ast-padded-layout'     => array(
					'label'       => __( 'Padded', 'astra' ),
					'description' => __( 'Adds padding around the entire site content (Requires Astra Pro).', 'astra' ),
				),
				'ast-fluid-layout'      => array(
					'label'       => __( 'Fluid', 'astra' ),
					'description' => __( 'Container width adjusts fluidly to viewport (Requires Astra Pro).', 'astra' ),
				),
			);

			$settings['site_sidebar_width']['description']  = __( 'Width of the sidebar as a percentage of the container. Default is 30%.', 'astra' );
			$settings['site_sidebar_width']['default']      = 30;
			$settings['site_sidebar_width']['unit']         = '%';
			$settings['site_sidebar_width']['setting_type'] = 'numeric';

			$settings['site_background']['description'] = __( 'Background styling for the area outside the content container.', 'astra' );
			$settings['site_background']['properties']  = array(
				__( 'Supports color, gradient, and image backgrounds', 'astra' ),
				__( 'Responsive settings available', 'astra' ),
				__( 'Affects the entire site background', 'astra' ),
			);

			$settings['content_background']['description'] = __( 'Background styling for the content area itself.', 'astra' );
			$settings['content_background']['properties']  = array(
				__( 'Supports color, gradient, and image backgrounds', 'astra' ),
				__( 'Responsive settings available', 'astra' ),
				__( 'Visible when container style is boxed', 'astra' ),
			);
		}

		// Prepare summary information.
		$summary = array(
			'current_layout' => isset( $layout_labels[ $container_layout ] ) ? $layout_labels[ $container_layout ] : $container_layout,
			'current_style'  => isset( $style_labels[ $container_style ] ) ? $style_labels[ $container_style ] : $container_style,
			'content_width'  => $site_content_width . 'px',
			'narrow_width'   => $narrow_container_width . 'px',
			'sidebar_width'  => $site_sidebar_width . '%',
			'style_applies'  => in_array( $container_layout, array( 'normal-width-container', 'narrow-width-container' ), true ),
			'style_note'     => __( 'Container style applies only when layout is Normal or Narrow', 'astra' ),
		);

		// Build response data.
		$response_data = array(
			'summary'        => $summary,
			'settings'       => $settings,
			'total_settings' => count( $settings ),
			'detailed'       => $detailed,
		);

		// Add helpful notes.
		if ( $detailed ) {
			$response_data['notes'] = array(
				__( 'Container style (boxed/unboxed) only takes effect when container layout is set to Normal or Narrow.', 'astra' ),
				__( 'Full Width layout ignores container style setting.', 'astra' ),
				__( 'Width values can be adjusted through the Customizer under Global > Container.', 'astra' ),
				__( 'Background settings support colors, gradients, and images with responsive options.', 'astra' ),
				__( 'Padded and Fluid layouts require Astra Pro plugin.', 'astra' ),
			);
		}

		return Astra_Abilities_Response::success(
			/* translators: %d: number of container settings */
			sprintf( __( 'Retrieved %d container settings successfully.', 'astra' ), count( $settings ) ),
			$response_data
		);
	}
}

Astra_List_Container_Settings::register();
