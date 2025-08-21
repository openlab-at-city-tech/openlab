<?php
/**
 * Controller for rest endpoints.
 *
 * @link    https://wpmudev.com/
 * @since   1.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV\Plugin_Cross_Sell
 *
 * @copyright (c) 2025, WPMU DEV (http://wpmudev.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell;

// Abort if called directly.
use WP_REST_Controller;
use WPMUDEV\Modules\Plugin_Cross_Sell\Container;

defined( 'WPINC' ) || die;

/**
 * Class Rest_Api
 *
 * @package WPMUDEV\Plugin_Cross_Sell
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
	protected $namespace = 'wpmudev_pcs/v1';

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @var string $endpoint
	 */
	protected $endpoint = '';

	/**
	 * Dependency container.
	 *
	 * @since 1.0.0
	 *
	 * @var Container
	 */
	protected $di_container = null;

	/**
	 * Utilities object.
	 *
	 * @since 1.0.0
	 *
	 * @var Utilities
	 */
	protected $utilities = null;

	/**
	 * Prepares the class properties.
	 *
	 * @param Container $container Dependency container.
	 * @return void
	 */
	public function init( ?Container $container = null ): void {
		$this->di_container = $container;
		$this->utilities    = $container->get( 'utilities' );

		if ( ! $this->utilities instanceof Utilities ) {
			$this->utilities = new Utilities();
		}

		// Allow to prepare some params early if required.
		$this->prepare_endpoint_params();

		// If the single instance hasn't been set, set it now.
		$this->register_hooks();
	}

	/**
	 * Set up WordPress hooks and filters
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Formatting the response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed           $item    The item to format.
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ): array {
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
	public function authorization_status_code(): int {
		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Check if a given request has access to manage settings.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param string           $capability Capability to check.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public function has_permission( ?\WP_REST_Request $request = null, string $capability = 'manage_options' ): bool {
		$capable = current_user_can( $capability );

		/**
		 * Filter to modify settings rest capability.
		 *
		 * @param WP_REST_Request $request Request object.
		 *
		 * @param bool            $capable Is user capable?.
		 *
		 * @since 1.0.0
		 */
		return boolval( apply_filters( 'wpmudev_pluginscrosssell_rest_settings_permission', $capable, $request ) );
	}

	/**
	 * Get formatted response for the current request.
	 *
	 * @param array $data    Response data.
	 * @param bool  $success Is request success.
	 *
	 * @return \WP_REST_Response
	 * @since  1.0.0
	 */
	public function get_response( $data = array(), $success = true ): \WP_REST_Response {
		// Response status.
		$status = $success ? 200 : 400;

		return new \WP_REST_Response(
			array(
				'success' => $success,
				'message' => $data['message'] ?? '',
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Get the Endpoint's namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}

	/**
	 * Set the Endpoint's namespace.
	 *
	 * @param string $api_namespace The namespace.
	 * @return void
	 */
	protected function set_namespace( string $api_namespace = '' ): void {
		$this->namespace = $api_namespace;
	}

	/**
	 * Get the Endpoint's endpoint part
	 *
	 * @return string
	 */
	public function get_endpoint(): string {
		return $this->endpoint;
	}

	/**
	 * Gives the full url of the Rest endpoint (with site url).
	 *
	 * @return string
	 */
	public function get_endpoint_url(): string {
		return trailingslashit( rest_url() ) . trailingslashit( $this->get_namespace() ) . $this->get_endpoint();
	}

	/**
	 * Gives the path of the Rest endpoint without site url
	 *
	 * @return string
	 */
	public function get_endpoint_path(): string {
		return trailingslashit( $this->get_namespace() ) . $this->get_endpoint();
	}

	/**
	 * Register the routes for the objects of the controller. This should be defined in extending class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
	}

	/**
	 * A helper function called early to allow prepare some variables
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function prepare_endpoint_params(): void {
	}
}
