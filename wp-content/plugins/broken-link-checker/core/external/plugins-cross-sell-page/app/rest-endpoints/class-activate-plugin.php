<?php
/**
 * REST API route/endpoint to activate free plugin.
 *
 * @link    https://wpmudev.com/
 * @since   1.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV\Plugin_Cross_Sell
 *
 * @copyright (c) 2025, WPMU DEV (http://wpmudev.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell\App\Rest_Endpoints;

use WPMUDEV\Modules\Plugin_Cross_Sell\Rest_Api;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Summary of Activate_Plugin
 */
class Activate_Plugin extends Rest_Api {
	/**
	 * Endpoint for activating plugin.
	 *
	 * @var string
	 */
	protected $endpoint = '/plugincrosssell/activate_plugin';

	/**
	 * Register the routes for handling confirmation functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Route to get auth url.
		register_rest_route(
			$this->get_namespace(),
			$this->get_endpoint(),
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'activate' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->input_args(),
				),
			)
		);
	}

	/**
	 * Get the input arguments schema for the endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function input_args(): array {
		return array(
			'plugin_slug'  => array(
				'type'     => 'string',
				'required' => true,
			),
			'current_slug' => array(
				'type'     => 'string',
				'required' => false,
			),
		);
	}

	/**
	 * Check if the current user has permission to activate plugins.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function check_permission( \WP_REST_Request $request ): bool {
		return $this->has_permission( $request, 'activate_plugins' );
	}

	/**
	 * Callback to activate the plugin.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response
	 */
	public function activate( \WP_REST_Request $request ): \WP_REST_Response {
		$response_message = '';
		$success          = true;

		$plugin_slug = $request->get_param( 'plugin_slug' );
		$plugin_path = $this->utilities->get_plugin_path_by_slug( $plugin_slug );

		if ( empty( $plugin_path ) ) {
			$response_message = __( 'Plugin not found.', 'plugin-cross-sell-textdomain' );
			$success          = false;
		}

		$activate = $this->activate_plugin( $plugin_path );

		if ( is_wp_error( $activate ) ) {
			$response_message = $activate->get_error_message();
			$success          = false;
		}

		$response = $this->get_response( array( 'message' => $response_message ), $success );

		return $response;
	}

	/**
	 * Activate the plugin.
	 *
	 * @param string $plugin_slug Plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function activate_plugin( string $plugin_slug ) {
		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Function activate_plugin returns null on success and WP_Error on failure.
		$activate = activate_plugin( $plugin_slug );

		if ( is_wp_error( $activate ) ) {
			return $activate;
		}

		return true;
	}
}
