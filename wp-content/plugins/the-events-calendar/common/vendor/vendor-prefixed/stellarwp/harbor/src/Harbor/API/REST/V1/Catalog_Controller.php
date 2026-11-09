<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * WP REST API controller for reading the product catalog.
 *
 * @since 1.0.0
 */
final class Catalog_Controller extends WP_REST_Controller {

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
	protected $rest_base = 'catalog';

	/**
	 * The catalog repository.
	 *
	 * @since 1.0.0
	 *
	 * @var Catalog_Repository
	 */
	private Catalog_Repository $repository;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Repository $repository The catalog repository.
	 *
	 * @return void
	 */
	public function __construct( Catalog_Repository $repository ) {
		$this->repository = $repository;
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
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/refresh',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'refresh_items' ],
					'permission_callback' => [ $this, 'check_permissions' ],
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
	 * Returns all product catalogs.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_items( $request ) {
		$catalog = $this->repository->get();

		if ( is_wp_error( $catalog ) ) {
			return $catalog;
		}

		return new WP_REST_Response( $catalog->to_array() );
	}

	/**
	 * Force refreshes the catalog from the upstream API and returns the result.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function refresh_items( $request ) {
		$catalog = $this->repository->refresh();

		if ( is_wp_error( $catalog ) ) {
			return $catalog;
		}

		return new WP_REST_Response( $catalog->to_array() );
	}

	/**
	 * Gets the schema for a single catalog item.
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

		$this->schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'catalog',
			'type'       => 'object',
			'properties' => [
				'product_id'   => [
					'description' => __( 'The product ID from the Commerce Portal.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'product_slug' => [
					'description' => __( 'The product slug.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'product_name' => [
					'description' => __( 'The product display name.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'tiers'        => [
					'description' => __( 'The product tiers ordered by rank.', 'tribe-common' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => [ 'view' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'slug'         => [
								'type' => 'string',
							],
							'name'         => [
								'type' => 'string',
							],
							'rank'         => [
								'type' => 'integer',
							],
							'price'        => [
								'type' => 'integer',
							],
							'currency'     => [
								'type' => 'string',
							],
							'herald_slugs' => [
								'type'  => 'array',
								'items' => [
									'type' => 'string',
								],
							],
							'purchase_url' => [
								'type' => 'string',
							],
							'upgrade_url'  => [
								'type' => 'string',
							],
						],
					],
				],
				'features'     => [
					'description' => __( 'The product features.', 'tribe-common' ),
					'type'        => 'array',
					'readonly'    => true,
					'context'     => [ 'view' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'slug'              => [
								'type' => 'string',
							],
							'kind'              => [
								'type' => 'string',
							],
							'minimum_tier'      => [
								'type' => 'string',
							],
							'top_dir'           => [
								'type' => [ 'string', 'null' ],
							],
							'main_file'         => [
								'type' => [ 'string', 'null' ],
							],
							'plugin_file'       => [
								'type' => [ 'string', 'null' ],
							],
							'wporg_slug'        => [
								'type' => [ 'string', 'null' ],
							],
							'version'           => [
								'type' => [ 'string', 'null' ],
							],
							'release_date'      => [
								'type' => [ 'string', 'null' ],
							],
							'changelog'         => [
								'type' => [ 'string', 'null' ],
							],
							'name'              => [
								'type' => 'string',
							],
							'description'       => [
								'type' => 'string',
							],
							'category'          => [
								'type' => 'string',
							],
							'authors'           => [
								'type'  => [ 'array', 'null' ],
								'items' => [
									'type' => 'string',
								],
							],
							'documentation_url' => [
								'type' => 'string',
							],
							'homepage'          => [
								'type' => [ 'string', 'null' ],
							],
						],
					],
				],
			],
		];

		/** @var array<string, mixed> */
		return $this->add_additional_fields_schema( $this->schema );
	}
}
