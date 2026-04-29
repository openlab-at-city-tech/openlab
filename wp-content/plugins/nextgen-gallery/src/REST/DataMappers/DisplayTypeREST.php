<?php
/**
 * Class DisplayTypeREST
 * Handles REST API endpoints for NextGEN Gallery display types.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataTypes\DisplayType;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\Util\Security;

/**
 * Class DisplayTypeREST
 * Handles REST API endpoints for NextGEN Gallery display types.
 */
class DisplayTypeREST {
	/**
	 * Register the REST API routes for display types
	 */
	public static function register_routes() {

		register_rest_route(
			'imagely/v1',
			'/display-types',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_display_types' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
			]
		);

		// Get a single display type.
		register_rest_route(
			'imagely/v1',
			'/display-types/(?P<name>[a-zA-Z0-9-_]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_display_type' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'name' => [
						'required'          => true,
						'type'              => 'string',
						'pattern'           => '/^[a-zA-Z0-9-_]+$/',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Update a display type's settings.
		register_rest_route(
			'imagely/v1',
			'/display-types/(?P<name>[a-zA-Z0-9-_]+)',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_display_type' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'name'     => [
						'required'          => true,
						'type'              => 'string',
						'pattern'           => '/^[a-zA-Z0-9-_]+$/',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'settings' => [
						'required'          => true,
						'type'              => 'object',
						'validate_callback' => [ self::class, 'validate_display_type_settings' ],
						'sanitize_callback' => [ self::class, 'sanitize_display_type_settings' ],
					],
				],
			]
		);

		// Reset a display type to defaults.
		register_rest_route(
			'imagely/v1',
			'/display-types/(?P<name>[a-zA-Z0-9-_]+)/reset',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'reset_display_type' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'name' => [
						'required'          => true,
						'type'              => 'string',
						'pattern'           => '/^[a-zA-Z0-9-_]+$/',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	/**
	 * Validate display type settings.
	 *
	 * @param array            $settings The settings to validate.
	 * @param \WP_REST_Request $request  The request object.
	 * @return bool|\\WP_Error
	 */
	public static function validate_display_type_settings( $settings, $request ) {
		if ( ! is_array( $settings ) ) {
			return new WP_Error(
				'invalid_settings',
				__( 'Settings must be an array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Simple validation - just ensure values are not arrays or objects
		foreach ( $settings as $key => $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				return new WP_Error(
					'invalid_setting_value',
					// translators: %s is the setting key.
					sprintf( __( 'Setting "%s" cannot be an array or object', 'nggallery' ), $key ),
					[ 'status' => 400 ]
				);
			}
		}

		return true;
	}

	/**
	 * Sanitize display type settings.
	 *
	 * @param array            $settings The settings to sanitize.
	 * @param \WP_REST_Request $request  The request object.
	 * @return array
	 */
	public static function sanitize_display_type_settings( $settings, $request ) {
		if ( ! is_array( $settings ) ) {
			return [];
		}

		$sanitized = [];

		foreach ( $settings as $key => $value ) {
			$key = sanitize_text_field( $key );

			// Skip arrays and objects
			if ( is_array( $value ) || is_object( $value ) ) {
				continue;
			}

			// Basic sanitization based on value type
			if ( is_bool( $value ) ) {
				$sanitized[ $key ] = (bool) $value;
			} elseif ( is_numeric( $value ) ) {
				// Handle both integers and floats
				$sanitized[ $key ] = is_float( $value ) ? (float) $value : (int) $value;
			} else {
				// Treat as string and sanitize
				$sanitized[ $key ] = wp_kses_post( $value );
			}
		}

		return $sanitized;
	}

	/**
	 * Check if user has permission to read display types
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Check if user has permission to edit display types
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Change style' );
	}

	/**
	 * Get all display types
	 *
	 * @return \WP_REST_Response
	 */
	public static function get_display_types() {
		$mapper        = DisplayTypeMapper::get_instance();
		$display_types = $mapper->find_all();

		$response = [];
		foreach ( $display_types as $display_type ) {
			$response[ $display_type->name ] = self::prepare_display_type_for_response( $display_type );
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Get a single display type.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error.
	 */
	public static function get_display_type( WP_REST_Request $request ) {
		$name         = $request->get_param( 'name' );
		$mapper       = DisplayTypeMapper::get_instance();
		$display_type = $mapper->find_by_name( $name );

		if ( ! $display_type ) {
			return new WP_Error(
				'display_type_not_found',
				// translators: %s is the display type name.
				sprintf( __( 'Display type "%s" not found', 'nggallery' ), $name ),
				[ 'status' => 404 ]
			);
		}

		return new WP_REST_Response( self::prepare_display_type_for_response( $display_type ), 200 );
	}

	/**
	 * Update a display type's settings.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function update_display_type( WP_REST_Request $request ) {
		$name         = $request->get_param( 'name' );
		$new_settings = $request->get_param( 'settings' );

		$mapper       = DisplayTypeMapper::get_instance();
		$display_type = $mapper->find_by_name( $name );

		if ( ! $display_type ) {
			return new WP_Error(
				'display_type_not_found',
				// translators: %s is the display type name.
				sprintf( __( 'Display type "%s" not found', 'nggallery' ), $name ),
				[ 'status' => 404 ]
			);
		}

		// Merge new settings with existing settings.
		$display_type->settings = array_merge( $display_type->settings, $new_settings );

		// Save the updated display type.
		try {
			$mapper->save( $display_type );
			return new WP_REST_Response(
				[
					'display_type' => self::prepare_display_type_for_response( $display_type ),
					'message'      => __( 'Display type settings updated successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'save_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Reset a display type to its default settings.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function reset_display_type( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );

		$mapper       = DisplayTypeMapper::get_instance();
		$display_type = $mapper->find_by_name( $name );

		if ( ! $display_type ) {
			return new WP_Error(
				'display_type_not_found',
				// translators: %s is the display type name.
				sprintf( __( 'Display type "%s" not found', 'nggallery' ), $name ),
				[ 'status' => 404 ]
			);
		}

		$controller_class = ControllerFactory::get_registered_modules()[ $name ] ?? null;
		if ( ! $controller_class || ! class_exists( $controller_class ) ) {
			return new WP_Error(
				'controller_not_found',
				// translators: %s is the display type name.
				sprintf( __( 'Controller for display type "%s" not found', 'nggallery' ), $name ),
				[ 'status' => 500 ]
			);
		}

		$controller = new $controller_class();
		if ( ! method_exists( $controller, 'get_default_settings' ) ) {
			return new WP_Error(
				'no_default_settings',
				// translators: %s is the display type name.
				sprintf( __( 'No default settings available for display type "%s"', 'nggallery' ), $name ),
				[ 'status' => 500 ]
			);
		}

		// Reset settings to defaults.
		$display_type->settings = $controller->get_default_settings();

		// Save the updated display type.
		try {
			$mapper->save( $display_type );
			return new WP_REST_Response(
				[
					'display_type' => self::prepare_display_type_for_response( $display_type ),
					'message'      => __( 'Display type settings reset to defaults successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'save_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Prepare display type data for API response.
	 *
	 * @param object $display_type The display type object.
	 * @return array
	 */
	private static function prepare_display_type_for_response( $display_type ) {
		return [
			'name'         => $display_type->name,
			'title'        => $display_type->title,
			'settings'     => $display_type->settings,
			'entity_types' => $display_type->entity_types,
		];
	}
}
