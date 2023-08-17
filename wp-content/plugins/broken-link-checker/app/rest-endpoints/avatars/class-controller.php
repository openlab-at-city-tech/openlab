<?php
/**
 * Rest endpoint fetching Avatars.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Avatars
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Rest_Endpoints\Avatars;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use WPMUDEV_BLC\Core\Controllers\Rest_Api;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Avatars
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
		$this->rest_base = 'avatars';

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
					'callback'            => array( $this, 'get_avatar' ),
					'permission_callback' => array( $this, 'get_avatar_permissions' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Returns avatar.
	 *
	 * @since 2.0.0
	 *
	 * @param object $request WP_REST_Request get data from request.
	 *
	 * @return mixed WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response
	 */
	public function get_avatar( $request ) {
		$email = $request->get_param( 'email' );

		$response_data = array(
			'message'     => __( 'Avatar url', 'broken-link-checker' ),
			'status_code' => 200,
		);

		if ( ! is_email( $email ) ) {
			$response_data['message']     = __( 'Invalid email address', 'broken-link-checker' );
			$response_data['status_code'] = 500;
		} else {
			$avatar                  = get_avatar_url( $email, array( 'size' => 24 ) );
			$response_data['avatar'] = $avatar;
		}

		$response = $this->prepare_item_for_response( $response_data, $request );

		return rest_ensure_response( $response );
	}


	/**
	 * Check permissions for fetching avatar.
	 *
	 * @since 2.0.0
	 *
	 * @param object $request get data from request.
	 *
	 * @return bool|object Boolean or WP_Error.
	 */
	public function get_avatar_permissions( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You can not fetch avatars.', 'broken-link-checker' ),
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
			'avatar' => array(
				'description' => esc_html__( 'Avatar by email.', 'broken-link-checker' ),
				'type'        => 'string',
			),

			'confirmed' => array(
				'description' => esc_html__( 'Auto-confirmed when email belongs to user.', 'broken-link-checker' ),
				'type'        => 'boolean',
			),

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
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

}
