<?php
/**
 * Controller for admin pages.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Controllers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Controllers;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Enqueue;
use WPMUDEV_BLC\Core\Utils\Utilities;
use function get_current_screen;

/**
 * Class Admin_Page
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Admin_Page extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 2.0.0
	 */
	use Enqueue;

	/**
	 * The Admin Page's Menu Type.
	 *
	 * @since 2.0.0
	 * @var bool $is_submenu Set to true if page uses submenu.
	 *
	 */
	protected $is_submenu = false;

	/**
	 * The Admin SubPage's Parent Slug.
	 *
	 * @since 2.0.0
	 * @var string $parent_slug The slug of the parent admin menu.
	 *
	 */
	protected $parent_slug;

	/**
	 * The Admin Page's Title.
	 *
	 * @since 2.0.0
	 * @var string $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 *
	 */
	protected $page_title;

	/**
	 * The Admin Menu's Title.
	 *
	 * @since 2.0.0
	 * @var string $menu_title The text to be used for the menu.
	 *
	 */
	protected $menu_title;

	/**
	 * Unique ID. Used for unique DOM element id.
	 *
	 * @since 2.0.0
	 * @var string|int $unique_id Unique ID.
	 *
	 */
	protected $unique_id;

	/**
	 * The Admin Menu's capability.
	 *
	 * @since 2.0.0
	 * @var string $capability The capability required for this menu to be displayed to the user.
	 *
	 */
	protected $capability = 'manage_options';

	/**
	 * The Admin Menu's Slug.
	 *
	 * @since 2.0.0
	 * @var string $menu_slug The slug name to refer to this menu by. Should be unique.
	 *
	 */
	protected $menu_slug;

	/**
	 * The Admin Menu's Icon.
	 *
	 * @since 2.0.0
	 * @var string $icon_url The URL to the icon to be used for this menu.
	 *
	 */
	protected $icon_url = '';

	/**
	 * The Admin Menu's position.
	 *
	 * @since 2.0.0
	 * @var int $position The position in the menu order this item should appear.
	 *
	 */
	protected $position = null;

	/**
	 * The Admin Menu's hook suffix.
	 *
	 * @since 2.1.0
	 * @var string $hook_suffix
	 *
	 */
	protected $hook_suffix;


	/**
	 * Init Admin Page
	 *
	 * @since 2.0.0
	 *
	 * @return void Initialize the Admin_Page.
	 */
	public function init() {
		if (
			apply_filters(
				'wpmudev_blc_admin_page_abort_load',
				! is_admin() || wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ),
				$this::instance()
			)
		) {
			return;
		}

		$this->prepare_props();
		$this->actions();
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 2.0.0
	 *
	 * @return void Prepares of the admin page.
	 */
	abstract public function prepare_props();

	/**
	 * Add Actions
	 *
	 * @since 2.0.0
	 *
	 * @return void Add the Actions.
	 */
	public function actions() {
		/**
		 * Until multisites are officially supported, BLC v2 menus are disabled in subsites.
		 * Legacy menus are loaded instead.
		 */
		if ( Utilities::is_subsite() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'admin_submenu' ) );
		add_action( 'admin_init', array( $this, 'boot' ) );
	}

	/**
	 * Set the admin_menu for the Admin Page.
	 *
	 * @since 2.0.0
	 *
	 * @return void Creates the admin page menu(s). Should check if menu or submenu.
	 */
	public function admin_menu() {
		if ( ! $this->is_submenu() && ! empty( $this->page_prop( 'menu_slug' ) ) ) {
			$this->hook_suffix = add_menu_page(
				$this->page_prop( 'page_title' ),
				$this->page_prop( 'menu_title' ),
				$this->page_prop( 'capability' ),
				$this->page_prop( 'menu_slug' ),
				array( $this, 'output' ),
				$this->page_prop( 'icon_url' ),
				$this->page_prop( 'position' )
			);
		}

	}


	/**
	 * Returns true if page uses submenu.
	 *
	 * @since 2.0.0
	 *
	 * @return bool Returns true if page uses submenu..
	 */
	private function is_submenu() {
		return $this->is_submenu;
	}

	/**
	 * Get Admin Page's properties.
	 *
	 * @since 2.0.0
	 *
	 * @return string|int|null Returns true if page uses submenu.
	 */
	private function page_prop( $prop ) {
		$properties = $this->get_properties();

		return isset( $properties[ $prop ] ) ? $properties[ $prop ] : null;
	}

	/**
	 * Admin Page's Properties.
	 *
	 * @since 2.0.0
	 *
	 * @return array An array with all Page's Properties.
	 */
	public function get_properties() {
		return apply_filters(
			'wpmudev_blc_admin_page_props',
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => $this->page_title,
				'menu_title'  => $this->menu_title,
				'capability'  => $this->capability,
				'menu_slug'   => $this->menu_slug,
				'icon_url'    => $this->icon_url,
				'position'    => $this->position,
			),
			$this->menu_slug,
			$this
		);
	}

	/**
	 * Set the admin_submenu for the Admin Page.
	 *
	 * @since 2.0.0
	 *
	 * @return void Creates the admin sub menu(s). Should check if menu or submenu.
	 */
	public function admin_submenu() {
		if ( $this->is_submenu() && ! empty( $this->page_prop( 'parent_slug' ) ) ) {
			$this->hook_suffix = add_submenu_page(
				$this->page_prop( 'parent_slug' ),
				$this->page_prop( 'page_title' ),
				$this->page_prop( 'menu_title' ),
				$this->page_prop( 'capability' ),
				$this->page_prop( 'menu_slug' ),
				array( $this, 'output' ),
				$this->page_prop( 'position' )
			);
		}
	}

	/**
	 * Admin init actions.
	 *
	 * @since 2.0.0
	 *
	 * @return void Admin init actions.
	 */
	public function boot() {
		add_action( 'current_screen', array( $this, 'current_screen_actions' ) );
	}

	/**
	 * Current screen actions.
	 *
	 * @since 2.0.0
	 *
	 * @return void Current screen actions.
	 */
	public function current_screen_actions() {
		if ( $this->can_boot() ) {
			$this->prepare_scripts();
			$this->page_hooks();
			add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );
		}
	}

	/**
	 * Checks if admin page actions/scripts should load in current screen.
	 *
	 * @since 2.0.0
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueuing scripts.
	 */
	public function can_boot() {
		// Using strpos of the menu_slug, so it can be checked dynamically for toplevel or not.
		// Should also take care of translated menu_slugs.
		return (
			is_admin() &&
			is_callable( '\get_current_screen' ) &&
			isset( get_current_screen()->id ) &&
			strpos( get_current_screen()->id, $this->menu_slug )
		);
	}

	/**
	 * Adds Page specific hooks. Extends $this->actions.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function page_hooks() {
	}

	/**
	 * Adds SUI admin body class. It will be used in all admin pages.
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function admin_body_classes( $classes ) {
		$sui_classes   = explode( ' ', $classes );
		$sui_classes[] = BLC_SHARED_UI_VERSION;

		if ( apply_filters( 'wpmudev_branding_hide_branding', false ) ) {
			$sui_classes[] = 'wpmudev-hide-branding';
		}

		return join( ' ', $sui_classes );
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 2.0.0
	 *
	 * @return array Register scripts for the admin page.
	 */
	public function set_scripts() {
		return array();
	}

	/**
	 * Admin Menu Output.
	 *
	 * @since 2.0.0
	 *
	 * @return void The output function of the Admin Menu Page.
	 */
	abstract public function output();

}
