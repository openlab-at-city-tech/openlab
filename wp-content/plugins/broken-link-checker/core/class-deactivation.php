<?php
/**
 * Class is called upon plugin de-activation.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class WPMUDEV_BLC
 *
 * @package WPMUDEV_BLC\Core
 */
final class Deactivation extends Base {
	/**
	 * Activation hooks.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	protected function __construct() {
		\WPMUDEV_BLC\wpmudev_blc_instance();
		do_action( 'wpmudev_blc_plugin_deactivated' );
	}
}
