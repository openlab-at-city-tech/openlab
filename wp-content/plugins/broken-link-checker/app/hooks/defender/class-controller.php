<?php
/**
 * Filter for adding BLC Crawler UA to accepted list.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Hooks\Defender
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Hooks\Defender;

defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hooks\Defender
 */
class Controller extends Base {
	public function init() {
		add_filter( 'wd_mask_login_is_bot_request', array( $this, 'bot_request' ) );
	}

	/**
	 * Sets to not a bot request when Crawler's UA found so that masked urls are not marked broken.
	 *
	 * @param boolean $is_bot_request
	 * @return bool
	 */
	public function bot_request( bool $is_bot_request ) {
		if (
			! empty( $_SERVER['HTTP_USER_AGENT'] ) &&
			'WPMU DEV Broken Link Checker Spider' === $_SERVER['HTTP_USER_AGENT']
		) {
			$is_bot_request = false;
		}

		return $is_bot_request;
	}
}
