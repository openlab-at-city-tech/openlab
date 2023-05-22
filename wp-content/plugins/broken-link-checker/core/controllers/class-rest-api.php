<?php
/**
 * Controller for rest endpoints.
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
use WP_REST_Controller;

defined( 'WPINC' ) || die;

/**
 * Class Admin_Page
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Rest_Api extends WP_REST_Controller {
	/**
	 * Holds the request param.
	 *
	 * @var array|object
	 */
	protected $request_action;

	/**
	 * The version.
	 *
	 * @var string
	 */
	protected $version = 'v1';

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Rest base for the current object.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Instance obtaining method.
	 *
	 * @since 2.0.0
	 *
	 * @return static Called class instance.
	 */
	public static function instance() {
		static $instances = array();

		$called_class_name = get_called_class();

		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();
		}

		return $instances[ $called_class_name ];
	}

	/**
	 * Formatting the response
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request request object.
	 * @param array $item
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		foreach ( $fields as $field_key ) {
			if ( rest_is_field_included( $field_key, $fields ) ) {
				$data[ $field_key ] = isset( $item[ $field_key ] ) ? $item[ $field_key ] : '';
			}
		}

		return $data;
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function authorization_status_code() {
		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

}
