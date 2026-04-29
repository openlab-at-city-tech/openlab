<?php
/**
 * Get Transparent Header Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Transparent_Header
 */
class Astra_Get_Transparent_Header extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-transparent-header';
		$this->label       = __( 'Get Astra Transparent Header Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra transparent header settings including enable state, device visibility, logo configuration, disable-on rules for specific page types, header border, and all color settings for header backgrounds, site title, menus, submenus, and content links.', 'astra' );
		$this->category    = 'astra';
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'enabled'         => array(
					'type'        => 'boolean',
					'description' => 'Whether transparent header is enabled.',
				),
				'on_devices'      => array(
					'type'        => 'string',
					'description' => 'Device visibility (both, desktop, or mobile).',
				),
				'logo'            => array(
					'type'        => 'object',
					'description' => 'Logo configuration for transparent header.',
				),
				'border'          => array(
					'type'        => 'object',
					'description' => 'Header border settings.',
				),
				'disable_on'      => array(
					'type'        => 'object',
					'description' => 'Page types where transparent header is disabled.',
				),
				'colors'          => array(
					'type'        => 'object',
					'description' => 'All color settings for transparent header.',
				),
				'available_devices' => array(
					'type'        => 'object',
					'description' => 'Available device visibility options.',
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
			'get transparent header settings',
			'is transparent header enabled',
			'show transparent header colors',
			'view transparent header configuration',
			'get transparent header logo',
			'show transparent header disable rules',
			'view transparent header menu colors',
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

		$logo = array(
			'different_logo'    => (bool) astra_get_option( 'different-transparent-logo', false ),
			'logo_url'          => astra_get_option( 'transparent-header-logo', '' ),
			'retina_logo_url'   => astra_get_option( 'transparent-header-retina-logo', '' ),
			'different_retina'  => (bool) astra_get_option( 'different-transparent-retina-logo', false ),
			'logo_width'        => astra_get_option( 'transparent-header-logo-width', array() ),
		);

		$border = array(
			'size'  => astra_get_option( 'transparent-header-main-sep', '' ),
			'color' => astra_get_option( 'transparent-header-main-sep-color', '' ),
		);

		$disable_on = array(
			'404_page'          => '1' == astra_get_option( 'transparent-header-disable-404-page', astra_get_option( 'transparent-header-disable-archive' ) ),
			'search_page'       => '1' == astra_get_option( 'transparent-header-disable-search-page', astra_get_option( 'transparent-header-disable-archive' ) ),
			'archive_pages'     => '1' == astra_get_option( 'transparent-header-disable-archive-pages', astra_get_option( 'transparent-header-disable-archive' ) ),
			'blog_index'        => '1' == astra_get_option( 'transparent-header-disable-index', false ),
			'latest_posts_index' => '1' == astra_get_option( 'transparent-header-disable-latest-posts-index', true ),
			'pages'             => '1' == astra_get_option( 'transparent-header-disable-page', false ),
			'posts'             => '1' == astra_get_option( 'transparent-header-disable-posts', false ),
		);

		$colors = array(
			'logo_color'              => astra_get_option( 'transparent-header-logo-color', '' ),
			'header_bg'               => array(
				'above'   => astra_get_option( 'hba-transparent-header-bg-color-responsive', array() ),
				'primary' => astra_get_option( 'transparent-header-bg-color-responsive', array() ),
				'below'   => astra_get_option( 'hbb-transparent-header-bg-color-responsive', array() ),
			),
			'site_title'              => astra_get_option( 'transparent-header-color-site-title-responsive', array() ),
			'site_title_hover'        => astra_get_option( 'transparent-header-color-h-site-title-responsive', array() ),
			'menu_color'              => astra_get_option( 'transparent-menu-color-responsive', array() ),
			'menu_bg_color'           => astra_get_option( 'transparent-menu-bg-color-responsive', array() ),
			'menu_hover_color'        => astra_get_option( 'transparent-menu-h-color-responsive', array() ),
			'submenu_color'           => astra_get_option( 'transparent-submenu-color-responsive', array() ),
			'submenu_bg_color'        => astra_get_option( 'transparent-submenu-bg-color-responsive', array() ),
			'submenu_hover_color'     => astra_get_option( 'transparent-submenu-h-color-responsive', array() ),
			'content_link_color'      => astra_get_option( 'transparent-content-section-link-color-responsive', array() ),
			'content_link_hover_color' => astra_get_option( 'transparent-content-section-link-h-color-responsive', array() ),
		);

		$device_labels = array(
			'both'    => 'Both (Desktop & Mobile)',
			'desktop' => 'Desktop Only',
			'mobile'  => 'Mobile Only',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved transparent header settings successfully.', 'astra' ),
			array(
				'enabled'           => (bool) astra_get_option( 'transparent-header-enable', false ),
				'on_devices'        => astra_get_option( 'transparent-header-on-devices', 'both' ),
				'logo'              => $logo,
				'border'            => $border,
				'disable_on'        => $disable_on,
				'colors'            => $colors,
				'available_devices' => $device_labels,
			)
		);
	}
}

Astra_Get_Transparent_Header::register();
