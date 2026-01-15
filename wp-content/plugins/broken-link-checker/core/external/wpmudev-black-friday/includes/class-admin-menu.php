<?php
/**
 * WPMUDEV Black Friday Admin Menu class
 *
 * Handles the addition of admin menu page for Black Friday campaign.
 *
 * @since   2.0.0
 * @author  WPMUDEV
 * @package WPMUDEV\Modules\BlackFriday
 */

namespace WPMUDEV\Modules\BlackFriday;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Menu
 *
 * Manages Black Friday admin menu page.
 *
 * @since 2.0.0
 */
class Admin_Menu {

	/**
	 * Campaign URL.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $campaign_url = '';

	/**
	 * Menu title.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $menu_title = '';

	/**
	 * Page title.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $page_title = '';

	/**
	 * Menu slug.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $menu_slug = 'wpmudev-black-friday';

	/**
	 * UTM content for the menu.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_content = 'BF-Plugins-2025';

	/**
	 * UTM campaign for the menu.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_campaign = 'BF-Plugins-2025-menu
';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. Admin menu configuration arguments.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'campaign_url' => 'https://wpmudev.com/black-friday/',
			'menu_title'   => __( 'Get Black Friday Deal', 'wpmudev-black-friday' ),
			'page_title'   => __( 'WPMUDEV Black Friday Deal', 'wpmudev-black-friday' ),
			'menu_slug'    => 'wpmudev-black-friday',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->campaign_url = esc_url( $args['campaign_url'] );
		$this->menu_title   = sanitize_text_field( $args['menu_title'] );
		$this->page_title   = sanitize_text_field( $args['page_title'] );
		$this->menu_slug    = sanitize_key( $args['menu_slug'] );

		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		$hook = ( is_multisite() && is_network_admin() ) ? 'network_admin_menu' : 'admin_menu';
		add_action( $hook, array( $this, 'add_admin_page' ), 99999 );
		// Normally we would remove the admin pages on admin_menu hook, we will use admin_head though which is used in some plugins.
		add_action( 'admin_head', array( $this, 'remove_admin_pages' ), 99999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_menu_styles' ), 999999 );
	}

	/**
	 * Add admin menu page.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function add_admin_page() {
		// Only show to administrators.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get all WPMU DEV plugins data.
		$plugins_list = Utils::get_plugins_list();

		foreach ( $plugins_list as $plugin_key => $plugin_data ) {
			$utm_source  = $plugin_data['utm_source'] ?? 'admin_menu';
			$parent_slug = $plugin_data['admin_parent_page'] ?? '';

			if ( empty( $parent_slug ) ) {
				continue;
			}

			$menu_url = add_query_arg(
				array(
					'utm_source'   => $utm_source,
					'utm_medium'   => 'plugin',
					'utm_campaign' => $utm_source . '-' . $this->utm_campaign,
					'utm_content'  => $this->utm_content,
				),
				$this->campaign_url
			);

			add_submenu_page(
				$parent_slug,
				$this->page_title,
				$this->menu_title,
				'manage_options',
				$menu_url,
				null,
				PHP_INT_MAX // Position as last submenu item.
			);

		}
	}

	/**
	 * Remove sales/offers and cross-sell admin menus to replace them with Black Friday campaign.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function remove_admin_pages() {
		global $submenu;

		$parent_menu_slugs = Utils::get_plugin_parent_screens();

		foreach ( $parent_menu_slugs as $parent_slug ) {
			if ( empty( $parent_slug ) || empty( $submenu[ $parent_slug ] ) ) {
				continue;
			}

			$prefix = 'https://wpmudev.com/project/';

			foreach ( $submenu[ $parent_slug ] as $index => $item ) {
				$slug = $item[2] ?? '';

				// Match the submenu URL.
				if ( is_string( $slug ) && ( strpos( $slug, $prefix ) === 0 || Utils::str_ends_with( $slug, '_cross_sell' ) || Utils::str_ends_with( $slug, 'cross-sell' ) ) ) {
					unset( $submenu[ $parent_slug ][ $index ] );
				}
			}
		}
	}

	/**
	 * Get campaign URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string The campaign URL.
	 */
	public function get_campaign_url() {
		return $this->campaign_url;
	}

	/**
	 * Get menu slug.
	 *
	 * @since 2.0.0
	 *
	 * @return string The menu slug.
	 */
	public function get_menu_slug() {
		return $this->menu_slug;
	}

	/**
	 * Enqueue custom styles for the Black Friday menu items.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_menu_styles() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$custom_css = '
			#adminmenu #toplevel_page_branding ul.wp-submenu li:last-child a[href*="/black-friday"],
			#adminmenu #toplevel_page_wds_wizard ul.wp-submenu li:last-child a[href*="/black-friday"],
			#adminmenu #toplevel_page_wphb ul.wp-submenu li:last-child a[href*="/black-friday"],
			#adminmenu #toplevel_page_forminator ul.wp-submenu li:last-child a[href*="/black-friday"],
			#adminmenu #toplevel_page_hustle .wp-submenu li:last-child a[href*="/black-friday"],
			#toplevel_page_smush li:last-child a[href*="/black-friday"],
			#adminmenu a[href="' . esc_attr( $this->campaign_url ) . '"],
			#adminmenu a[href*="/black-friday"] {
				background: #B1FF65 !important;
				color: #000000 !important;
				font-weight: 500 !important;
			}

			#adminmenu #toplevel_page_branding ul.wp-submenu li:last-child a[href*="/black-friday"]:hover,
			#adminmenu #toplevel_page_branding ul.wp-submenu li:last-child a[href*="/black-friday"]:focus,
			#adminmenu #toplevel_page_wds_wizard ul.wp-submenu li:last-child a[href*="/black-friday"]:hover,
			#adminmenu #toplevel_page_wds_wizard ul.wp-submenu li:last-child a[href*="/black-friday"]:focus,
			#adminmenu #toplevel_page_wphb ul.wp-submenu li:last-child a[href*="/black-friday"]:hover,
			#adminmenu #toplevel_page_wphb ul.wp-submenu li:last-child a[href*="/black-friday"]:focus,
			#adminmenu #toplevel_page_forminator ul.wp-submenu li:last-child a[href*="/black-friday"]:hover,
			#adminmenu #toplevel_page_forminator ul.wp-submenu li:last-child a[href*="/black-friday"]:focus,
			#adminmenu #toplevel_page_hustle .wp-submenu li:last-child a[href*="/black-friday"]:hover,
			#adminmenu #toplevel_page_hustle .wp-submenu li:last-child a[href*="/black-friday"]:focus,
			#toplevel_page_smush li:last-child a[href*="/black-friday"]:hover,
			#toplevel_page_smush li:last-child a[href*="/black-friday"]:focus,
			#adminmenu a[href*="/black-friday"]:hover,
			#adminmenu a[href*="/black-friday"]:focus,
			#toplevel_page_smush li:last-child a[href*="' . esc_attr( $this->campaign_url ) . '"]:hover,
			#toplevel_page_smush li:last-child a[href*="' . esc_attr( $this->campaign_url ) . '"]:focus,
			#adminmenu a[href*="' . esc_attr( $this->campaign_url ) . '"]:hover,
			#adminmenu a[href*="' . esc_attr( $this->campaign_url ) . '"]:focus {
				background: #71A341 !important;
				transition: background 0.3s ease !important;
			}
		';

		wp_add_inline_style( 'admin-menu', $custom_css );
	}
}
