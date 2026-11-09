<?php
/**
 * Rest endpoint for Hub Connector operations.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Rest_Endpoints\HubConnector
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Rest_Endpoints\Hub_Connector;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WPMUDEV_BLC\Core\Controllers\Rest_Api;
use WPMUDEV\Hub\Connector\API;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Rest_Endpoints\HubConnector
 */
class Controller extends Rest_Api {

    use Dashboard_API;

    /**
     * Initialize the controller.
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function init() {
        $this->namespace = "wpmudev_blc/{$this->version}";
        $this->rest_base = 'hub-connector';

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
            '/' . $this->rest_base . '/disconnect',
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'disconnect' ),
                    'permission_callback' => array( $this, 'disconnect_permissions_check' ),
                ),
                'schema' => array( $this, 'get_item_schema' ),
            )
        );
    }

    /**
     * Disconnect user from the hub
     *
     * @since 2.0.0
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function disconnect( $request ) {
        // Check if Hub Connector API is available
        if ( ! class_exists( '\WPMUDEV\Hub\Connector\API' ) ) {
            $response_data = array(
                'message'     => __( 'Hub Connector is not available', 'broken-link-checker' ),
                'status_code' => 500,
            );

            $response = $this->prepare_item_for_response( $response_data, $request );
            return rest_ensure_response( $response );
        }

        // Check if user is connected to the hub
        if ( ! self::hub_connector_connected() ) {
            $response_data = array(
                'message'     => __( 'User is not connected to the hub', 'broken-link-checker' ),
                'status_code' => 400,
            );

            $response = $this->prepare_item_for_response( $response_data, $request );
            return rest_ensure_response( $response );
        }

        // Disconnect from the hub using the Hub Connector API
        $logout_result = self::hub_connector_disconnect();

        // Check if logout was successful
        if ( is_wp_error( $logout_result ) ) {
            $response_data = array(
                'message'     => sprintf(
                    __( 'Failed to disconnect from hub: %s', 'broken-link-checker' ),
                    $logout_result->get_error_message()
                ),
                'status_code' => 500,
            );

            $response = $this->prepare_item_for_response( $response_data, $request );
            return rest_ensure_response( $response );
        }

        Settings::instance()->init();
		Settings::instance()
            ->set(
                array(
                    'show_disconnect_notice' => true,
                )
            );
		Settings::instance()->save();

        $response_data = array(
            'message'     => __( 'Successfully disconnected from hub', 'broken-link-checker' ),
            'status_code' => 200,
            'data'        => array(
                'disconnected_at' => current_time( 'mysql' ),
            ),
        );

        $response = $this->prepare_item_for_response( $response_data, $request );

        return rest_ensure_response( $response );
    }

    /**
     * Check if a given request has access to disconnect from hub
     *
     * @since 2.0.0
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return true|WP_Error True if the request has access, WP_Error object otherwise.
     */
    public function disconnect_permissions_check( WP_REST_Request $request ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__( 'You do not have permissions to disconnect from hub.', 'broken-link-checker' ),
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
            'title'      => 'hub-connector',
            'type'       => 'object',
            'properties' => array(),
        );

        $this->schema['properties'] = array(
            'message' => array(
                'description' => esc_html__( 'Response message.', 'broken-link-checker' ),
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
                ),
                'readonly'    => true,
            ),

            'data' => array(
                'description' => esc_html__( 'Response data.', 'broken-link-checker' ),
                'type'        => 'object',
                'properties'  => array(
                    'disconnected_at' => array(
                        'description' => esc_html__( 'Timestamp when disconnected.', 'broken-link-checker' ),
                        'type'        => 'string',
                    ),
                ),
            ),
        );

        return $this->add_additional_fields_schema( $this->schema );
    }
}