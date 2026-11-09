<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Features\Feature_Resource;
use TEC\Common\LiquidWeb\Harbor\Features\Manager;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * WP REST API controller for managing features.
 *
 * Supports listing, retrieving, enabling, disabling, and updating features.
 * Restricted to logged-in Administrators (manage_options capability).
 *
 * @since 1.0.0
 */
class Feature_Controller extends WP_REST_Controller {

	/**
	 * The REST API namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $namespace = 'liquidweb/harbor/v1';

	/**
	 * The REST API route base.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $rest_base = 'features';

	/**
	 * The feature manager instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Constructor for the feature REST API controller.
	 *
	 * @since 1.0.0
	 *
	 * @param Manager $manager The feature manager.
	 *
	 * @return void
	 */
	public function __construct( Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Registers the routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		if ( ! $this->namespace ) {
			return;
		}

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>[a-zA-Z0-9_-]+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'slug' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>[a-zA-Z0-9_-]+)/enable',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'enable' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => $this->get_slug_args(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>[a-zA-Z0-9_-]+)/disable',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'disable' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => $this->get_slug_args(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>[a-zA-Z0-9_-]+)/update',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => $this->get_slug_args(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Permission callback: require manage_options capability.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_permissions(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Lists features with optional filters.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$features = $this->manager->get_all();

		if ( is_wp_error( $features ) ) {
			return $this->ensure_error_status( $features );
		}

		$product   = $request->get_param( 'product' );
		$tier      = $request->get_param( 'tier' );
		$available = $request->get_param( 'available' );
		$type      = $request->get_param( 'type' );

		if ( $product !== null || $tier !== null || $available !== null || $type !== null ) {
			$features = $features->filter(
				$product !== null ? Cast::to_string( $product ) : null,
				$tier !== null ? Cast::to_string( $tier ) : null,
				$available !== null ? Cast::to_bool( $available ) : null,
				$type !== null ? Cast::to_string( $type ) : null
			);
		}

		$data = [];

		foreach ( $features as $feature ) {
			$data[] = Feature_Resource::from_feature( $feature )->to_array();
		}

		return new WP_REST_Response( $data );
	}

	/**
	 * Retrieves a single feature by slug.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$slug     = Cast::to_string( $request->get_param( 'slug' ) );
		$features = $this->manager->get_all();

		if ( is_wp_error( $features ) ) {
			return $this->ensure_error_status( $features );
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found.', $slug ),
				[ 'status' => 404 ]
			);
		}

		return new WP_REST_Response( Feature_Resource::from_feature( $feature )->to_array() );
	}

	/**
	 * Enables a feature.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function enable( WP_REST_Request $request ) {
		$slug = Cast::to_string( $request->get_param( 'slug' ) );

		$feature = $this->manager->enable( $slug );

		if ( is_wp_error( $feature ) ) {
			return $this->ensure_error_status( $feature );
		}

		return new WP_REST_Response(
			Feature_Resource::from_feature( $feature )->to_array()
		);
	}

	/**
	 * Disables a feature.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function disable( WP_REST_Request $request ) {
		$slug    = Cast::to_string( $request->get_param( 'slug' ) );
		$feature = $this->manager->disable( $slug );

		if ( is_wp_error( $feature ) ) {
			return $this->ensure_error_status( $feature );
		}

		return new WP_REST_Response(
			Feature_Resource::from_feature( $feature )->to_array()
		);
	}

	/**
	 * Triggers an update for a feature.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		$slug    = Cast::to_string( $request->get_param( 'slug' ) );
		$feature = $this->manager->update( $slug );

		if ( is_wp_error( $feature ) ) {
			return $this->ensure_error_status( $feature );
		}

		return new WP_REST_Response(
			Feature_Resource::from_feature( $feature )->to_array()
		);
	}

	/**
	 * Gets the schema for a single feature response.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function get_item_schema(): array {
		if ( $this->schema ) {
			/** @var array<string, mixed> */
			return $this->add_additional_fields_schema( $this->schema );
		}

		$base_properties = [
			'slug'              => [
				'description' => __( 'The feature slug.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'name'              => [
				'description' => __( 'The feature display name.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'description'       => [
				'description' => __( 'The feature description.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'product'           => [
				'description' => __( 'The product the feature belongs to.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'tier'              => [
				'description' => __( 'The feature tier.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'type'              => [
				'description' => __( 'The feature type identifier.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'is_available'      => [
				'description' => __( 'Whether the feature is available for the current site.', 'tribe-common' ),
				'type'        => 'boolean',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'access_sources'    => [
				'description' => __( 'The grants that make the feature available: purchases activated on this site, free access, or an opted-in legacy license.', 'tribe-common' ),
				'type'        => 'array',
				'readonly'    => true,
				'context'     => [ 'view' ],
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'type'         => [
							'type' => 'string',
							'enum' => [ 'purchase', 'free', 'legacy' ],
						],
						'product_slug' => [ 'type' => 'string' ],
						'tier'         => [ 'type' => 'string' ],
						'product_name' => [ 'type' => 'string' ],
						'tier_name'    => [ 'type' => 'string' ],
					],
				],
			],
			'in_catalog_tier'   => [
				'description' => __( "True when the user's licensed tier covers this feature's minimum tier, or another product's purchase includes the feature.", 'tribe-common' ),
				'type'        => 'boolean',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'documentation_url' => [
				'description' => __( 'The URL to the feature documentation.', 'tribe-common' ),
				'type'        => 'string',
				'format'      => 'uri',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'is_enabled'        => [
				'description' => __( 'Whether the feature is currently enabled.', 'tribe-common' ),
				'type'        => 'boolean',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
		];

		$installable_properties = [
			'release_date'      => [
				'description' => __( 'Release date of the latest version, or null if not applicable.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'version'           => [
				'description' => __( 'Latest available version from the catalog, or null. Only present for installable features.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'changelog'         => [
				'description' => __( 'Changelog HTML for the latest version, or null. Only present for installable features.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'wporg_slug'        => [
				'description' => __( 'The WordPress.org slug for plugins_api() lookups, or null if not on WordPress.org.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'installed_version' => [
				'description' => __( 'The currently installed version, or null if not installed.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
			'update_version'    => [
				'description' => __( 'Version available in the WordPress update transient, or null when no update is pending.', 'tribe-common' ),
				'type'        => [ 'string', 'null' ],
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
		];

		$plugin_properties = [
			'plugin_file' => [
				'description' => __( 'The plugin file path relative to the plugins directory.', 'tribe-common' ),
				'type'        => 'string',
				'readonly'    => true,
				'context'     => [ 'view' ],
			],
		];

		$this->schema = [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title'   => 'feature',
			'oneOf'   => [
				[
					'title'                => 'plugin',
					'type'                 => 'object',
					'additionalProperties' => true,
					'properties'           => array_merge(
						$base_properties,
						[ 'type' => array_merge( $base_properties['type'], [ 'enum' => [ Feature::TYPE_PLUGIN ] ] ) ],
						$plugin_properties,
						$installable_properties
					),
				],
				[
					'title'                => 'theme',
					'type'                 => 'object',
					'additionalProperties' => true,
					'properties'           => array_merge(
						$base_properties,
						[ 'type' => array_merge( $base_properties['type'], [ 'enum' => [ Feature::TYPE_THEME ] ] ) ],
						$installable_properties
					),
				],
				[
					'title'                => 'service',
					'type'                 => 'object',
					'additionalProperties' => true,
					'properties'           => array_merge(
						$base_properties,
						[ 'type' => array_merge( $base_properties['type'], [ 'enum' => [ Feature::TYPE_SERVICE ] ] ) ]
					),
				],
			],
		];

		/** @var array<string, mixed> */
		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Gets the query parameters for the features collection.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_collection_params(): array {
		return [
			'product'   => [
				'description'       => __( 'Filter by product.', 'tribe-common' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'tier'      => [
				'description'       => __( 'Filter by feature tier.', 'tribe-common' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'available' => [
				'description' => __( 'Filter by availability.', 'tribe-common' ),
				'type'        => 'boolean',
			],
			'type'      => [
				'description'       => __( 'Filter by feature type.', 'tribe-common' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}

	/**
	 * Ensures a WP_Error has an HTTP status code in its data.
	 *
	 * Errors from the Manager and its strategies do not carry HTTP
	 * status codes.  This method maps known error codes to the most
	 * appropriate HTTP status before the error reaches the REST
	 * infrastructure (which defaults to 500 when no status is set).
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Error $error The error to enrich.
	 *
	 * @return WP_Error The same instance, with a status code guaranteed.
	 */
	private function ensure_error_status( WP_Error $error ): WP_Error {
		$data = $error->get_error_data();

		if ( is_array( $data ) && isset( $data['status'] ) ) {
			return $error;
		}

		$status = Error_Code::http_status( (string) $error->get_error_code() );

		$error->add_data(
			is_array( $data )
			? array_merge( $data, [ 'status' => $status ] )
			: [ 'status' => $status ]
		);

		return $error;
	}

	/**
	 * Gets the slug argument definition for toggle endpoints.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_slug_args(): array {
		return [
			'slug' => [
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function ( $slug ): bool {
					return Cast::to_bool(
						$this->manager->exists(
							Cast::to_string( $slug )
						)
					);
				},
			],
		];
	}
}
