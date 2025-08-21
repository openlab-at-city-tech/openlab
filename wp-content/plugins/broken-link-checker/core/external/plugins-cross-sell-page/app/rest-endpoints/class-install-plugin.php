<?php
/**
 * REST API route/endpoint to install free plugin.
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
use WPMUDEV\Modules\Plugin_Cross_Sell\Container;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Install_Plugin
 *
 * @since 1.0.0
 */
class Install_Plugin extends Rest_Api {
	/**
	 * The endpoint for the route.
	 *
	 * @var string
	 */
	protected $endpoint = '/plugincrosssell/install_plugin';

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
					'callback'            => array( $this, 'install' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->input_args(),
				),
			)
		);
	}

	/**
	 * The input arguments schema for the route.
	 *
	 * @since 1.0.0
	 * @return array{current_slug: array{required: bool, type: string, plugin_slug: array{required: bool, type: string}}}
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
	 * Check if the current user has permission to install plugins.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request The Request object.
	 * @return bool
	 */
	public function check_permission( \WP_REST_Request $request ): bool {
		return $this->has_permission( $request, 'install_plugins' );
	}

	/**
	 * Save the client id and secret.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response
	 */
	public function install( \WP_REST_Request $request ): \WP_REST_Response {
		$response_message = '';
		$success          = true;
		$plugin_slug      = $request->get_param( 'plugin_slug' );
		$submenu_params   = ( $this->di_container instanceof Container ) ? $this->di_container->get( 'submenu_data' ) : null;
		$current_slug     = ! empty( $submenu_params['slug'] ) ? $submenu_params['slug'] : '';

		// First let's make sure that we are not modifying the current plugin.
		if ( $plugin_slug === $current_slug ) {
			$response_message = __( 'You are already using this plugin.', 'plugin-cross-sell-textdomain' );
			$success          = false;
		} elseif ( ! $this->utilities->is_plugin_installed( $plugin_slug ) ) {
			// Install the plugin.
			$plugin = $this->install_plugin( $plugin_slug );
			if ( is_wp_error( $plugin ) ) {
				$response_message = $plugin->get_error_message();
				$success          = false;
			} else {
				$response_message = __( 'Plugin installed successfully.', 'plugin-cross-sell-textdomain' );
			}
		} else {
			$response_message = __( 'Plugin already installed.', 'plugin-cross-sell-textdomain' );
		}

		return $this->get_response(
			array(
				'message' => $response_message,
				'success' => $success,
			),
			$success
		);
	}

	/**
	 * Performs the actual plugin installation.
	 *
	 * @param string $plugin_slug Will be used to get the path to the plugin.
	 * @return bool|\WP_Error
	 */
	protected function install_plugin( string $plugin_slug ) {
		// Include required files for plugin installation.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin_slug,
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			return $api;
		}

		$install = $this->install_from_api( $api );

		return $install;
	}

	/**
	 * Implements the install using Plugin_Upgrader, WP built in class.
	 *
	 * @param \stdClass $api Plugin installer data in object, see plugins_api().
	 * @return bool|\WP_Error
	 */
	protected function install_from_api( \stdClass $api ) {

		// Include necessary plugin functions.
		$installer = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$install   = $installer->install( $api->download_link );

		if ( empty( $install ) || is_wp_error( $install ) ) {
			return $install;
		}

		return true;
	}
}
