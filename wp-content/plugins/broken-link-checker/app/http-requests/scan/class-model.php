<?php
/**
 * The Http Request model.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Models
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Http_Requests\Scan;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Traits\Sanitize;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Models\Http_Request;


/**
 * Class Installer
 *
 * @package WPMUDEV_BLC\Core\Models
 */
class Model extends Http_Request {

	public function start_scan( string $url = '', string $api_key = '' ) {
		$args = array(

		);

		$this->request( $args );
		return $this->get_response();
	}
}
