<?php
/**
 * The Http Request to HUB when the Edit Link actions complete.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Http_Requests\Edit_Complete
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Http_Requests\Edit_Complete;

// Abort if called directly.
use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\Core\Models\Http_Request;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_Dashboard;

defined( 'WPINC' ) || die;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Http_Requests\Edit_Complete
 */
class Controller extends Base {
	/**
	 * The BLC remote api full url.
	 *
	 * @var string
	 */
	private $url = null;

	/**
	 *
	 * Null or Object of eiter Http_Request or WP_Error.
	 *
	 * @var object
	 */
	private $request_api = null;

	/**
	 * The BLC api key.
	 *
	 * @var string
	 */
	private $api_key = null;

	/**
	 * The request data.
	 *
	 * @var array
	 */
	private $request_body = array();

	public function send_report( Link $link = null ) {
		$link_url           = $link->get_link();
		$types_map          = $link->types_map();
		$link_type          = $types_map[ $link->get_type() ] ?? 'edited';
		$this->request_body = array(
			'site_id' => Utilities::site_id(),
			'domain'  => network_site_url(),
			'link'    => array(
				'link' => $link_url,
				'type' => $link_type,
			),
		);

		return $this->request();
	}

	/**
	 * Sends the data of link, that was processed by plugin, to HUB API.
	 *
	 * @return WP_HTTP_Response|null|WP_Error
	 */
	public function request() {
		$this->prepare_request();

		/*
		 * Request to get scan results.
		 */
		$args = array(
			'method'  => 'POST',
			'url'     => $this->get_request_url(),
			'headers' => array(
				'Authorization' => $this->api_key,
				'Content-Type'  => 'application/json; charset=utf-8',
			),
			'body'    => $this->request_body,
		);

		$response = $this->request_api->request( $args );

		Utilities::log( " : \nResponse from Edit Link Request: " . var_export( $response, true ) );
	}

	/**
	 * Prepares some params.
	 *
	 * @return void
	 */
	private function prepare_request() {
		if ( is_null( $this->request_api ) ) {
			$this->request_api = Http_Request::instance();
		}

		// WPMUDEV_Dashboard class has been checked already in `$this->can_do_request()`.
		$this->api_key = WPMUDEV_Dashboard::$api->get_key();
	}

	/**
	 * Returns full request url.
	 *
	 * @return string
	 */
	public function get_request_url() {
		if ( is_null( $this->url ) ) {
			$this->url = Utilities::hub_edit_link_completed();
		}

		return $this->url;
	}
}
