<?php
/**
 * Controller for admin notices.
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
abstract class Admin_Notice extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 2.0.0
	 */
	use Enqueue;

	/**
	 * The admin pages the notice will be visible at.
	 *
	 * @since 2.0.0
	 * @var array $admin_pages
	 *
	 */
	protected $admin_pages = array();

	/**
	 * Init Admin Page
	 *
	 * @since 2.0.0
	 *
	 * @return void Initialize the Admin_Page.
	 */
	public function init() {
		$this->prepare_props();
		$this->actions();
	}

	/**
	 * Prepares the properties of the Admin Notice.
	 *
	 * @since 2.0.0
	 *
	 * @return void Prepares properties of the admin notice.
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
		add_action( 'admin_init', array( $this, 'boot' ) );
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
			$this->notice_hooks();

			add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );
			add_action( 'admin_notices', array( $this, 'output' ) );
		}
	}

	/**
	 * Checks if admin page actions/scripts should load in current screen.
	 *
	 * @since 2.0.0
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueuing scripts.
	 */
	public abstract function can_boot();

	/**
	 * Adds specific hooks per notice. Extends $this->actions.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function notice_hooks() {
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
