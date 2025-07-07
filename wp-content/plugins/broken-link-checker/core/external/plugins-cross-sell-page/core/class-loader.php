<?php
/**
 * Class to boot up module.
 *
 * @link    https://wpmudev.com/
 * @since   1.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV/Plugin_Cross_Sell
 *
 * @copyright (c) 2025, Incsub (http://incsub.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * The Loader class is responsible for initializing the module.
 */
final class Loader {
	/**
	 * Settings helper class instance.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $settings;

	/**
	 * Minimum supported php version.
	 *
	 * @since  1.0.0
	 * @var float
	 */
	public $php_version = '7.4';

	/**
	 * Minimum WordPress version.
	 *
	 * @since  1.0.0
	 * @var float
	 */
	public $wp_version = '6.3';

	/**
	 * The dependency container.
	 *
	 * @since  1.0.0
	 * @var Container
	 */
	private $container;

	/**
	 * Initialize the loader.
	 *
	 * @since  1.0.0
	 * @param Container $container The dependency container.
	 * @return void
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Initialize functionality if requirements are met.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! $this->can_boot() ) {
			return;
		}

		$this->setup_components();
	}

	/**
	 * Main condition that checks if plugin parts should continue loading.
	 *
	 * @return bool
	 */
	private function can_boot() {
		/**
		 * Checks
		 *  - PHP version
		 *  - WP Version
		 * If not then return.
		 */
		global $wp_version;

		return (
			version_compare( PHP_VERSION, $this->php_version, '>=' ) &&
			version_compare( $wp_version, $this->wp_version, '>=' )
		);
	}

	/**
	 * Register all the actions and filters.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_components(): void {
		$submenus = new App\Submenus\CrossSell();
		$submenus->init( $this->container );

		$install_endpoint = new App\Rest_Endpoints\Install_Plugin();
		$install_endpoint->init( $this->container );

		$activation_endpoint = new App\Rest_Endpoints\Activate_Plugin();
		$activation_endpoint->init( $this->container );
	}
}
