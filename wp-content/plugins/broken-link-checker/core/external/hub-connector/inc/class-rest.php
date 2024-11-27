<?php
/**
 * The Rest API class.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;

/**
 * Class Rest
 */
class Rest {

	use Singleton;

	/**
	 * API namespace.
	 *
	 * @var string $namespace
	 */
	protected $namespace = 'hub-connector/v1';

	/**
	 * Initialize Rest class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for rest API.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		// Sync endpoint.
		register_rest_route(
			$this->namespace,
			'sync',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'sync' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'force' => array(
							'required'    => false,
							'description' => __( 'Should do a force sync.', 'wpmudev' ),
							'type'        => 'boolean',
							'default'     => false,
						),
					),
				),
			)
		);

		// Sync endpoint.
		register_rest_route(
			$this->namespace,
			'logout',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'logout' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Callback for sync API request.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function sync( $request ) {
		// Should we do a force sync?.
		$force = $request->get_param( 'force' );

		// Do hub sync.
		$response = API::get()->sync_site( $force );

		// If error, get error message.
		if ( is_wp_error( $response ) ) {
			return $this->get_response(
				$response->get_error_message(),
				false
			);
		}

		return $this->get_response();
	}

	/**
	 * Callback for logout API request.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function logout() {
		// Logout.
		$response = API::get()->logout();

		// If error, get error message.
		if ( is_wp_error( $response ) ) {
			return $this->get_response(
				$response->get_error_message(),
				false
			);
		}

		return $this->get_response();
	}

	/**
	 * Get API response data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data    Data to return.
	 * @param bool  $success Success?.
	 * @param int   $code    Status code.
	 *
	 * @return WP_REST_Response
	 */
	private function get_response( $data = array(), $success = true, $code = 200 ) {
		$response = array(
			'success' => $success,
		);

		// Response data.
		if ( $success ) {
			$response['data'] = $data;
		} else {
			$response['error'] = $data;
		}

		return new WP_REST_Response( $response, $code );
	}
}
