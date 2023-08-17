<?php
/**
 * Wrapper class for registering and enqueueing scripts and styles.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Traits
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Loader as Core;

/**
 * Class Enqueue
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Enqueue {
	/**
	 * JS assets url.
	 *
	 * @since 2.0.0
	 *
	 * @return void JS assets url.
	 */
	public $scripts_dir = WPMUDEV_BLC_ASSETS_URL . 'js/';

	/**
	 * CSS assets url.
	 *
	 * @since 2.0.0
	 *
	 * @return void CSS assets url.
	 */
	public $styles_dir = WPMUDEV_BLC_ASSETS_URL . 'css/';

	/**
	 * Set frontend scripts.
	 *
	 * @since 2.0.0
	 *
	 * @return array Set frontend scripts.
	 */
	public function set_front_scripts() {
		return array();
	}

	/**
	 * Set backend scripts.
	 *
	 * @since 2.0.0
	 *
	 * @return array Set backend scripts.
	 */
	public function set_admin_scripts() {
		return array();
	}

	/**
	 * Set frontend styles.
	 *
	 * @since 2.0.0
	 *
	 * @return array Set frontend styles.
	 */
	public function set_front_styles() {
		return array();
	}

	/**
	 * Set backend styles.
	 *
	 * @since 2.0.0
	 *
	 * @return array Set backend styles.
	 */
	public function set_admin_styles() {
		return array();
	}

	/**
	 * Prepares scripts.
	 *
	 * @since 2.0.0
	 *
	 * @return void Prepare scripts.
	 */
	public function prepare_scripts() {
		if ( \method_exists( $this, 'set_front_scripts' ) ) {
			$scripts = $this->set_front_scripts();

			if ( is_array( $scripts ) ) {
				Core::$scripts = array_merge( Core::$scripts, $scripts );
			}
		}

		if ( \method_exists( $this, 'set_admin_scripts' ) ) {
			$scripts = $this->set_admin_scripts();

			if ( is_array( $scripts ) ) {
				Core::$admin_scripts = array_merge( Core::$admin_scripts, $scripts );
			}
		}

		if ( \method_exists( $this, 'set_front_styles' ) ) {
			$scripts = $this->set_front_styles();

			if ( is_array( $scripts ) ) {
				Core::$styles = array_merge( Core::$styles, $scripts );
			}
		}

		if ( \method_exists( $this, 'set_admin_styles' ) ) {
			$scripts = $this->set_admin_styles();

			if ( is_array( $scripts ) ) {
				Core::$admin_styles = array_merge( Core::$admin_styles, $scripts );
			}
		}
	}

}
