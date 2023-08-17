<?php
/**
 * Rest endpoint starting new Scan.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Scan
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Rest_Endpoints\Scan;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

use WPMUDEV_BLC\Core\Controllers\Rest_Api;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results\Controller as Scan_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Scan
 */
class Controller extends Rest_Api {
	/**
	 * Settings keys.
	 *
	 * @var array
	 */
	protected $settings_keys = array();

	public function init() {
		$this->settings_keys = array_map(
			function ( $settings_key ) {
				return sanitize_key( $settings_key );
			},
			array_keys( Settings::instance()->default )
		);

		$this->namespace = "wpmudev_blc/{$this->version}";
		$this->rest_base = 'scan';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'handle_scan_request' ),
					'permission_callback' => array( $this, 'get_scan_permissions' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	public function handle_scan_request( $request ) {
		$action = $request->get_param( 'action' );

		if ( 'start_scan' === $action ) {
			return $this->start_scan( $request );
		}

		// Action is `fetch_scan_data`.
		return $this->monitor_scan( $request );
	}

	/**
	 * Returns new scan info.
	 *
	 * @param object $request WP_REST_Request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response
	 */
	public function start_scan( $request ) {
		$response_data = array();

		$scan = Scan_API::instance();
		$scan->start();

		$response_data['status_code'] = $scan->get_response_code();
		$response_data['message']     = $scan->get_response_message();
		$response_data['data']        = $scan->get_response_data();
		$response_data['success']     = 200 === $scan->get_response_code();
		$response_data['scan_status'] = $scan->get_response_scan_status();
		$response                     = $this->prepare_item_for_response( $response_data, $request );

		return rest_ensure_response( $response );
	}


	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	public function monitor_scan( $request ) {
		$response_data = array();
		$scan          = Scan_API::instance();

		$scan->start();

		if ( is_wp_error( $scan->get_response() ) ) {
			$response_data['success']     = false;
			$response_data['status_code'] = 500;
			$response_data['message']     = $scan->get_error_message();
			$response_data['data']        = null;
		} else {
			$response_data['status_code'] = $scan->get_response_code();
			$response_data['message']     = $scan->get_response_message();
		}

		$response_data['success']     = $scan->get_response_code() === 200;
		$response_data['scan_status'] = $scan->get_response_scan_status();
		$response_data['data']        = $scan->get_response_data();

		return rest_ensure_response( $this->prepare_item_for_response( $response_data, $request ) );
	}

	/**
	 * Check permissions for fetching avatar.
	 *
	 * @param object $request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|object Boolean or WP_Error.
	 */
	public function get_scan_permissions( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You are not allowed to start a new scan.', 'broken-link-checker' ),
				array( 'status' => $this->authorization_status_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => isset( $args['rest_base'] ) ? $args['rest_base'] : '',
			'type'       => 'object',
			'properties' => array(),
		);

		$this->schema['properties'] = array(
			'success' => array(
				'description' => esc_html__( 'If true scan has started successfully, else it has not.', 'broken-link-checker' ),
				'type'        => 'boolean',
			),

			'scan_status' => array(
				'description' => esc_html__( 'The scan status.', 'broken-link-checker' ),
				'type'        => 'string',
				'enum'        => array(
					'completed',
					'in_progress',
					'none',
				),
			),

			'message' => array(
				'description' => esc_html__( 'Response message.', 'broken-link-checker' ),
				'type'        => 'string',
			),

			'data' => array(
				'description' => esc_html__( 'Scan response data/results.', 'broken-link-checker' ),
				'type'        => 'string',
			),

			'status_code' => array(
				'description' => esc_html__( 'Response status code.', 'broken-link-checker' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array(
					'200',
					'400',
					'401',
					'403',
					'500',
				),
				'readonly'    => true,
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

}
