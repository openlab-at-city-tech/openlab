<?php
/**
 * An endpoint where Hub can send requests and add nofollow attribute on broken links.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Hub_Endpoints\Edit_Link
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Hub_Endpoints\Nofollow_Link;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Broken_Links_Actions\Router;
use WPMUDEV_BLC\Core\Controllers\Hub_Endpoint;
/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hub_Endpoint\Unlink_Link
 */
class Controller extends Hub_Endpoint {
	public function process() {
		$this->output_formatted_response( Router::instance()->direct_endpoint( 'nofollow' ) );
	}


	/**
	 * Sets the endpoint's action vars to be used by Dash plugin.
	 */
	protected function setup_action_vars() {
		$this->endpoint_action_name     = 'blc_nofollow_link';
		$this->endpoint_action_callback = 'process';
	}

	/**
	 * Provides the schema that the requested input should have in order to be escaped properly.
	 *
	 * @return string[]
	 */
	public function get_sanitize_schema() {
		return array(
			//'site_id'     => 'int',
			'link'      => 'url',
			//'target_tags' => 'attr',
			'origins'   => 'url',
			'full_site' => 'bool',
		);
	}
}
