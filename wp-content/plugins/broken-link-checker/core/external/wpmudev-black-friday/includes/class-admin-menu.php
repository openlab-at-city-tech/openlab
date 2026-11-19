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
	private $utm_content = 'BF-Plugins-2026';

	/**
	 * UTM campaign for the menu.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_campaign = 'BF-Plugins-2026-menu';

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
			'menu_title'   => __( 'Black Friday', 'wpmudev-black-friday' ),
			'page_title'   => __( 'WPMUDEV Black Friday Deal', 'wpmudev-black-friday' ),
			'menu_slug'    => 'wpmudev-black-friday',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->campaign_url  = esc_url( $args['campaign_url'] );
		$this->menu_title    = sanitize_text_field( $args['menu_title'] );
		$this->page_title    = sanitize_text_field( $args['page_title'] );
		$this->menu_slug     = sanitize_key( $args['menu_slug'] );

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
		// Admin Menu Editor replaces the menu with its own copy after admin_head, so remove the items from that copy too.
		add_action( 'admin_menu_editor-menu_replaced', array( $this, 'remove_admin_pages' ) );
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
		// Only for users who can see the campaign.
		if ( ! Utils::current_user_can_view() ) {
			return;
		}

		// Get all WPMU DEV plugins data.
		$plugins_list = Utils::get_plugins_list();

		foreach ( $plugins_list as $plugin_key => $plugin_data ) {
			$utm_source  = $plugin_data['utm_source'] ?? 'admin_menu';
			$parent_slug = $plugin_data['admin_parent_page'] ?? '';

			// Skip plugins whose parent menu is not registered, to avoid orphan submenus.
			if ( empty( $parent_slug ) || ! $this->parent_menu_exists( $parent_slug ) ) {
				continue;
			}

			$menu_url = add_query_arg(
				array(
					'utm_source'   => $utm_source,
					'utm_medium'   => 'plugin',
					'utm_campaign' => $utm_source . '-' . $this->utm_campaign,
					'utm_content'  => $this->utm_content,
				),
				Utils::get_campaign_url( $plugin_data, $this->campaign_url )
			);

			// Skip if the menu item has already been added, eg by another copy of the module.
			if ( $this->submenu_exists( $parent_slug, $menu_url ) ) {
				continue;
			}

			add_submenu_page(
				$parent_slug,
				$this->page_title,
				$this->get_menu_title_html(),
				'manage_options',
				$menu_url,
				null,
				PHP_INT_MAX // Position as last submenu item.
			);

		}
	}

	/**
	 * Get the menu title markup: the title, a "Sale" tag and an external link arrow.
	 *
	 * @since 2.1.0
	 *
	 * @return string The menu title HTML.
	 */
	private function get_menu_title_html(): string {
		return sprintf(
			'%1$s <span class="wpmudev-bf-menu-tag"><span class="wpmudev-bf-menu-sale">%2$s</span><span class="wpmudev-bf-menu-arrow" aria-hidden="true"></span></span>',
			esc_html( $this->menu_title ),
			esc_html__( 'Sale', 'wpmudev-black-friday' )
		);
	}

	/**
	 * Checks if a top level admin menu is registered.
	 *
	 * @since 2.1.0
	 *
	 * @param string $parent_slug The parent menu slug.
	 *
	 * @return bool
	 */
	private function parent_menu_exists( string $parent_slug ): bool {
		global $menu;

		if ( empty( $menu ) || ! is_array( $menu ) ) {
			return false;
		}

		return in_array( $parent_slug, wp_list_pluck( $menu, 2 ), true );
	}

	/**
	 * Checks if a submenu item is already registered under the given parent.
	 *
	 * @since 2.1.0
	 *
	 * @param string $parent_slug The parent menu slug.
	 * @param string $menu_slug   The submenu slug.
	 *
	 * @return bool
	 */
	private function submenu_exists( string $parent_slug, string $menu_slug ): bool {
		global $submenu;

		if ( empty( $submenu[ $parent_slug ] ) || ! is_array( $submenu[ $parent_slug ] ) ) {
			return false;
		}

		// Core runs the slug through plugin_basename() when registering it.
		return in_array( plugin_basename( $menu_slug ), wp_list_pluck( $submenu[ $parent_slug ], 2 ), true );
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

				// Don't remove our own menu item.
				if ( ! is_string( $slug ) || $this->is_campaign_menu_url( $slug ) ) {
					continue;
				}

				// Match the submenu URL.
				if ( strpos( $slug, $prefix ) === 0 || Utils::str_ends_with( $slug, '_cross_sell' ) || Utils::str_ends_with( $slug, 'cross-sell' ) ) {
					unset( $submenu[ $parent_slug ][ $index ] );
				}
			}
		}
	}

	/**
	 * Checks if a submenu URL is our Black Friday menu item.
	 *
	 * Uses the UTM campaign, because the URL can look like the plugin's own upgrade links.
	 *
	 * @since 2.1.0
	 *
	 * @param string $url The submenu URL.
	 *
	 * @return bool
	 */
	private function is_campaign_menu_url( string $url ): bool {
		return str_contains( $url, $this->utm_campaign );
	}

	/**
	 * Get the CSS selector for the Black Friday menu links.
	 *
	 * Uses the UTM campaign, because each plugin's link goes to a different page.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_menu_link_attribute(): string {
		return 'a[href*="' . esc_attr( $this->utm_campaign ) . '"]';
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
		if ( ! Utils::current_user_can_view() ) {
			return;
		}

		$link       = $this->get_menu_link_selectors();
		$arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M0.75 8.25L8 1M2 0.75H8.25V7" stroke="black" stroke-width="1.5"/></svg>';
		$arrow_mask = 'url("data:image/svg+xml,' . rawurlencode( $arrow_svg ) . '") no-repeat center / contain';

		// All plugins share the same design. The title keeps the admin menu's system font.
		$custom_css = $link . ' {
				background: #00ff08 !important;
				color: #000 !important;
				font-size: 14px !important;
				font-weight: 400 !important;
				padding-right: 8px !important; /* More room, so the title fits on one line. */
				transition: background-color 0.3s ease !important;
			}

			' . $this->get_menu_link_selectors( ':hover' ) . ' {
				background: #72A441 !important;
				color: #000 !important;
				box-shadow: none !important;
			}

			/* A focused link, eg after a click opened the new tab, looks the same as when idle. */
			' . $this->get_menu_link_selectors( ':focus:not(:hover)' ) . ' {
				background: #00ff08 !important;
				color: #000 !important;
			}

			/* The "Sale" tag and arrow wrap together, so the arrow is never on its own line. */
			#adminmenu ' . $this->get_menu_link_attribute() . ' .wpmudev-bf-menu-tag {
				white-space: nowrap;
			}

			/* The "Sale" tag uses Roboto when the page has it loaded. */
			#adminmenu ' . $this->get_menu_link_attribute() . ' .wpmudev-bf-menu-sale {
				display: inline-block;
				margin-left: 1px;
				padding: 0 5px;
				border: 1px solid currentColor;
				border-radius: 100px;
				font-family: Roboto, -apple-system, BlinkMacSystemFont, "Segoe UI", Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
				font-size: 12px;
				font-weight: 400;
				line-height: 14px;
				vertical-align: 1px;
			}

			/* Drawn with CSS, as some menu editors remove svg tags from menu titles, eg Branda. It uses the text color. */
			#adminmenu ' . $this->get_menu_link_attribute() . ' .wpmudev-bf-menu-arrow {
				display: inline-block;
				width: 9px;
				height: 9px;
				margin-left: 6px;
				vertical-align: 0;
				background-color: currentColor;
				-webkit-mask: ' . $arrow_mask . ';
				mask: ' . $arrow_mask . ';
			}
		';

		wp_add_inline_style( 'admin-menu', $custom_css );

		// Open the Black Friday menu links in a new tab.
		wp_add_inline_script(
			'common',
			'document.querySelectorAll(' . wp_json_encode( '#adminmenu ' . $this->get_menu_link_attribute() ) . ').forEach(function(a){a.target="_blank";a.rel="noopener noreferrer";});'
		);
	}

	/**
	 * Get the CSS selectors of the Black Friday menu links.
	 *
	 * Some plugins style the last item of their menu, which is our link, eg Hustle's
	 * `#adminmenu #toplevel_page_hustle .wp-submenu li:last-child a[href^="https://wpmudev.com"]`.
	 * So each plugin's menu gets a selector with two ids and the top level item's core classes to win.
	 *
	 * @since 2.1.0
	 *
	 * @param string $state Optional. A state to append to the link, eg `:hover`.
	 *
	 * @return string Comma separated selectors.
	 */
	private function get_menu_link_selectors( string $state = '' ): string {
		$link      = $this->get_menu_link_attribute();
		$selectors = array( '#adminmenu ' . $link . $state );

		foreach ( Utils::get_plugins_list() as $plugin_data ) {
			$parent_slug = $plugin_data['admin_parent_page'] ?? '';

			if ( empty( $parent_slug ) ) {
				continue;
			}

			// Same as core builds the top level menu item id.
			$menu_id     = preg_replace( '|[^a-zA-Z0-9_:.]|', '-', 'toplevel_page_' . $parent_slug );
			$selectors[] = '#adminmenu #' . $menu_id . '.menu-top.wp-has-submenu .wp-submenu li ' . $link . $state;
		}

		return implode( ",\n\t\t\t", $selectors );
	}

}
