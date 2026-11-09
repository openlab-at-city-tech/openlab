<?php
/**
 * File that includes all integration related classes.
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Integrations
 */

namespace WPMUDEV_BLC\App\Integrations;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use WPMUDEV_BLC\App\Integrations\Elementor\Elementor_Integration;
use WPMUDEV_BLC\App\Integrations\SiteOrigin\SiteOrigin_Integration;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
/**
 * Class App_Integrations
 *
 * @package WPMUDEV_BLC\App\Integrations
 */
class App_Integrations {

	/**
	 * Flag to ensure integrations are initialized only once.
	 *
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * Initialize the integrations.
	 */
	public static function init() {
		if ( self::$initialized ) {
			return;
		}
		self::$initialized = true;

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			Elementor_Integration::instance()->init();
		}

		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			SiteOrigin_Integration::instance()->init();
		}
	}
}
