<?php
/**
 * REST API endpoints for NextGEN Gallery addon management.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\Settings\Settings;

/**
 * Class AddonsREST
 * Handles REST API endpoints for NextGEN Gallery addon enable/disable functionality.
 *
 * @package Imagely\NGG\REST\DataMappers
 */
class AddonsREST {

	/**
	 * List of valid addon IDs.
	 *
	 * @var array
	 */
	public static $valid_addons = [
		'dribbble',
		'tiktok',
		'google_analytics',
		'instagram',
	];

	/**
	 * Register the REST API routes for addons
	 */
	public static function register_routes() {
		// Get all addon states.
		register_rest_route(
			'imagely/v1',
			'/addons',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_addon_states' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
			]
		);

		// Update a specific addon's enabled state.
		register_rest_route(
			'imagely/v1',
			'/addons/(?P<addon_id>[a-z_-]+)/status',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_addon_status' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'addon_id' => [
						'type'              => 'string',
						'required'          => true,
						'description'       => 'The feature identifier',
						'sanitize_callback' => 'sanitize_key',
						'validate_callback' => [ self::class, 'validate_addon_id' ],
					],
					'enabled'  => [
						'type'              => 'boolean',
						'required'          => true,
						'description'       => 'Whether the feature should be enabled',
						'sanitize_callback' => 'rest_sanitize_boolean',
					],
				],
			]
		);
	}

	/**
	 * Check if user has read permission.
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if user has edit permission.
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate addon ID.
	 *
	 * @param string $addon_id The addon ID to validate.
	 * @return bool|WP_Error
	 */
	public static function validate_addon_id( $addon_id ) {
		if ( ! in_array( $addon_id, self::$valid_addons, true ) ) {
			return new WP_Error(
				'invalid_addon_id',
				sprintf(
					/* translators: %s: feature id */
					__( 'Invalid feature ID: %s', 'nggallery' ),
					$addon_id
				),
				[ 'status' => 400 ]
			);
		}
		return true;
	}

	/**
	 * Get all addon enabled states.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_addon_states( WP_REST_Request $request ) {
		$settings       = Settings::get_instance();
		$enabled_addons = $settings->get( 'enabled_addons', [] );

		// Ensure we return a state for all valid addons.
		$addon_states = [];
		foreach ( self::$valid_addons as $addon_id ) {
			$addon_states[ $addon_id ] = isset( $enabled_addons[ $addon_id ] ) ? (bool) $enabled_addons[ $addon_id ] : false;
		}

		return new WP_REST_Response(
			[
				'enabled_addons' => $addon_states,
			],
			200
		);
	}

	/**
	 * Update a specific addon's enabled status.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function update_addon_status( WP_REST_Request $request ) {
		$addon_id = $request->get_param( 'addon_id' );
		$enabled  = $request->get_param( 'enabled' );

		$settings       = Settings::get_instance();
		$enabled_addons = $settings->get( 'enabled_addons', [] );

		if ( ! is_array( $enabled_addons ) ) {
			$enabled_addons = [];
		}

		// Update the specific addon's state.
		$enabled_addons[ $addon_id ] = $enabled;

		// Save back to settings.
		$settings->set( 'enabled_addons', $enabled_addons );

		try {
			$settings->save();

			// Return updated states for all addons.
			$addon_states = [];
			foreach ( self::$valid_addons as $id ) {
				$addon_states[ $id ] = isset( $enabled_addons[ $id ] ) ? (bool) $enabled_addons[ $id ] : false;
			}

			return new WP_REST_Response(
				[
					'success'        => true,
					'addon_id'       => $addon_id,
					'enabled'        => $enabled,
					'enabled_addons' => $addon_states,
					'message'        => self::get_toggle_message( $addon_id, $enabled ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'addon_update_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Return success message for toggle; all external sources use "Feature" wording.
	 *
	 * @param string $addon_id Feature/addon ID.
	 * @param bool   $enabled  Whether the feature was enabled.
	 * @return string
	 */
	public static function get_toggle_message( $addon_id, $enabled ) {
		$feature_display_names = [
			'dribbble'         => 'Dribbble',
			'tiktok'           => 'TikTok',
			'google_analytics' => 'Google Analytics',
			'instagram'        => 'Instagram',
		];
		$display_name = isset( $feature_display_names[ $addon_id ] ) ? $feature_display_names[ $addon_id ] : $addon_id;
		$noun         = __( 'Feature', 'nggallery' );
		if ( $enabled ) {
			return sprintf(
				/* translators: 1: Feature, 2: display name (e.g. Dribbble, TikTok) */
				__( '%1$s %2$s has been activated.', 'nggallery' ),
				$noun,
				$display_name
			);
		}
		return sprintf(
			/* translators: 1: Feature, 2: display name */
			__( '%1$s %2$s has been deactivated.', 'nggallery' ),
			$noun,
			$display_name
		);
	}

	/**
	 * Check if a specific addon is enabled.
	 * Static helper method for addon plugins to use.
	 *
	 * @param string $addon_id The addon identifier.
	 * @return bool Whether the addon is enabled.
	 */
	public static function is_addon_enabled( $addon_id ) {
		$settings       = Settings::get_instance();
		$enabled_addons = $settings->get( 'enabled_addons', [] );

		if ( ! is_array( $enabled_addons ) ) {
			return false;
		}

		return isset( $enabled_addons[ $addon_id ] ) && (bool) $enabled_addons[ $addon_id ];
	}

	/**
	 * Check if a gallery can be rendered based on its external source addon status.
	 * Returns false if the gallery uses an external source addon that is not enabled.
	 *
	 * @param object $gallery The gallery object to check.
	 * @return bool Whether the gallery can be rendered.
	 */
	public static function can_render_gallery( $gallery ) {
		if ( empty( $gallery->external_source ) || empty( $gallery->external_source['type'] ) ) {
			return true; // No external source, can render.
		}

		$source_type = $gallery->external_source['type'];

		// If it's a valid addon type and the addon is not enabled, don't render.
		if ( in_array( $source_type, self::$valid_addons, true ) && ! self::is_addon_enabled( $source_type ) ) {
			return false;
		}

		return true;
	}
}
