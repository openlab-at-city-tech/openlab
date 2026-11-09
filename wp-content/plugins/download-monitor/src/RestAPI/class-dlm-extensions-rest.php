<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extensions/license/menu REST routes for the Extensions React app.
 * Ported from Modula_Rest_Api (extensions/license/menu portion).
 */
class DLM_Extensions_Rest {

	private static $instance;

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) || ! ( self::$instance instanceof DLM_Extensions_Rest ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register_routes() {
		register_rest_route(
			'download-monitor/v1',
			'/extensions',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_extensions' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/license',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'license_action' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	public function permissions_check() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_extensions() {
		$instance = class_exists( 'DLM_Pro\Extensions\Extensions' )
			? DLM_Pro\Extensions\Extensions::get_instance()
			: DLM_Extensions_Base::get_instance();

		return new \WP_REST_Response( $instance->get_extensions(), 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function license_action( \WP_REST_Request $request ) {
		$body        = $request->get_json_params();
		$license_key = isset( $body['license_key'] ) ? trim( sanitize_text_field( $body['license_key'] ) ) : '';
		$action      = isset( $body['action'] ) ? $body['action'] : '';
		$saved       = get_option( 'dlm_pro_license_key', '' );

		if ( empty( $license_key ) && empty( $saved ) ) {
			return new \WP_REST_Response( array( 'message' => 'no_license_key', 'status' => 'error' ), 200 );
		}

		if ( ! class_exists( 'DLM_Pro\Extensions\Licensing' ) ) {
			if ( DLM_Pro_Compat::is_pro_outdated() ) {
				return new \WP_REST_Response( 'Please update DLM Pro to version ' . DLM_Pro_Compat::MIN_VERSION . ' or later to manage your license from this screen.', 400 );
			}

			return new \WP_REST_Response( 'DLM Pro is not installed.', 400 );
		}

		$license = DLM_Pro\Extensions\Licensing::get_instance();

		if ( 'activate' === $action ) {
			return new \WP_REST_Response( $license->activate_license( $license_key ), 200 );
		}

		if ( 'deactivate' === $action ) {
			return new \WP_REST_Response( $license->deactivate_license( $license_key ), 200 );
		}

		return new \WP_REST_Response( $license->check_license(), 200 );
	}
}
