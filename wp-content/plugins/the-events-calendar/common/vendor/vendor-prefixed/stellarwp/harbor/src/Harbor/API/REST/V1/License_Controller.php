<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Licensing\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Utils\License_Key;
use TEC\Common\LiquidWeb\Harbor\Licensing\License_Manager;
use TEC\Common\LiquidWeb\Harbor\Licensing\Product_Collection;
use TEC\Common\LiquidWeb\Harbor\Site\Data;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * WP REST API controller for the unified license key.
 *
 * Provides GET, POST, and DELETE endpoints for reading,
 * storing, and removing the unified license key.
 *
 * All endpoints require the manage_options capability.
 *
 * @since 1.0.0
 */
final class License_Controller extends WP_REST_Controller {

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
	protected $rest_base = 'license';

	/**
	 * The license manager.
	 *
	 * @since 1.0.0
	 *
	 * @var License_Manager
	 */
	private License_Manager $manager;

	/**
	 * The site data provider.
	 *
	 * @since 1.0.0
	 *
	 * @var Data
	 */
	private Data $site_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param License_Manager $manager   The license manager.
	 * @param Data            $site_data The site data provider.
	 *
	 * @return void
	 */
	public function __construct( License_Manager $manager, Data $site_data ) {
		$this->manager   = $manager;
		$this->site_data = $site_data;
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
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'check_permissions' ],
				],
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'store_item' ],
					'permission_callback' => [ $this, 'check_key_write_permissions' ],
					'args'                => $this->get_store_args(),
				],
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'check_key_write_permissions' ],
					'args'                => $this->get_network_args(),
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
					'callback'            => [ $this, 'refresh_item' ],
					'permission_callback' => [ $this, 'check_permissions' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<key>[A-Za-z0-9-]+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'lookup_item' ],
					'permission_callback' => [ $this, 'check_permissions' ],
					'args'                => [
						'key' => [
							'description'       => __( 'The license key to look up.', 'tribe-common' ),
							'type'              => 'string',
							'required'          => true,
							'validate_callback' => static function ( $value ): bool {
								return is_string( $value ) && License_Key::is_valid_format( $value );
							},
						],
					],
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
	 * Permission callback for storing or deleting the key.
	 *
	 * The network-level key applies to every site, so changing it also needs
	 * network-level authority.
	 *
	 * @since 2.0.2
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return bool
	 */
	public function check_key_write_permissions( WP_REST_Request $request ): bool {
		if ( ! $this->check_permissions() ) {
			return false;
		}

		if ( is_multisite() && (bool) $request->get_param( 'network' ) ) {
			return current_user_can( 'manage_network_options' );
		}

		return true;
	}

	/**
	 * Returns the current unified license key and its associated products.
	 *
	 * Returns 200 on success. Returns a WP_Error if the API call fails or a
	 * recent failure is still within the throttle window. The key field will
	 * be null if no key is stored and none is discoverable from the product
	 * registry.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$domain = $this->site_data->get_domain();
		$key    = $this->manager->get_key();

		if ( $key === null ) {
			return new WP_REST_Response(
				License_Response::make( null, new Product_Collection() )
			);
		}

		$products = $this->manager->get_products( $domain );

		if ( is_wp_error( $products ) ) {
			if ( $products->get_error_code() === Error_Code::INVALID_KEY ) {
				return new WP_REST_Response(
					License_Response::make( $key, new Product_Collection(), $products )
				);
			}

			return $products;
		}

		return new WP_REST_Response(
			License_Response::make( $key, $products )
		);
	}

	/**
	 * Refreshes the license data from the upstream API.
	 *
	 * Re-fetches products from the licensing service and returns the key +
	 * products in the same shape as GET /license. When a not-found response
	 * leaves the cached purchases in place, those are the products returned.
	 *
	 * The With_Error_Throttle trait on License_Manager prevents hammering the
	 * upstream API after a recent failure (60-second TTL).
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function refresh_item( $request ) {
		$key = $this->manager->get_key();

		if ( $key === null ) {
			return new WP_REST_Response(
				License_Response::make( null, new Product_Collection() )
			);
		}

		$domain   = $this->site_data->get_domain();
		$products = $this->manager->refresh_products( $domain );

		if ( is_wp_error( $products ) ) {
			if ( $products->get_error_code() === Error_Code::INVALID_KEY ) {
				// Only a confirmed rejection clears the cache; an ambiguous 404 keeps the purchases.
				$stored = $this->manager->get_products( $domain );

				return new WP_REST_Response(
					License_Response::make( $key, $stored instanceof Product_Collection ? $stored : new Product_Collection(), $products )
				);
			}

			return $products;
		}

		return new WP_REST_Response(
			License_Response::make( $key, $products )
		);
	}

	/**
	 * Looks up the products for a license key, skipping storage.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function lookup_item( $request ) {
		/** @var string $key */
		$key    = $request->get_param( 'key' );
		$domain = $this->site_data->get_domain();
		$result = $this->manager->lookup_products( $key, $domain );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response(
			License_Response::make( $key, $result )
		);
	}

	/**
	 * Validates a license key against the remote API and stores it.
	 *
	 * Verifies the key is recognized (has products) but does not activate
	 * any product or consume a seat. Returns the stored key on success.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function store_item( $request ) {
		/** @var string $key */
		$key     = $request->get_param( 'key' );
		$network = (bool) $request->get_param( 'network' );
		$domain  = $this->site_data->get_domain();

		$result = $this->manager->validate_and_store( $key, $domain, $network );

		if ( is_wp_error( $result ) ) {
			$data = $result->get_error_data();

			if ( ! is_array( $data ) || empty( $data['status'] ) ) {
				$result->add_data( [ 'status' => 500 ] );
			}

			return $result;
		}

		$products = $this->manager->get_products( $domain );

		if ( is_wp_error( $products ) ) {
			$products = new Product_Collection();
		}

		return new WP_REST_Response(
			License_Response::make( $this->manager->get_key(), $products )
		);
	}

	/**
	 * Deletes the stored unified license key.
	 *
	 * This only removes the locally stored key. It does not free any
	 * activation seats on the licensing service.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response
	 */
	public function delete_item( $request ): WP_REST_Response {
		$network = (bool) $request->get_param( 'network' );

		$this->manager->delete_key( $network );

		return new WP_REST_Response( null, 204 );
	}

	/**
	 * Gets the schema for the license resource.
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
			'title'      => 'license',
			'type'       => 'object',
			'properties' => [
				'key'      => [
					'description' => __( 'The unified license key.', 'tribe-common' ),
					'type'        => [ 'string', 'null' ],
					'context'     => [ 'view' ],
				],
				'products' => [
					'description' => __( 'The products associated with the license key.', 'tribe-common' ),
					'type'        => 'array',
					'context'     => [ 'view' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'product_slug'      => [
								'description' => __( 'The product identifier.', 'tribe-common' ),
								'type'        => 'string',
							],
							'tier'              => [
								'description' => __( 'The entitlement tier.', 'tribe-common' ),
								'type'        => 'string',
							],
							'status'            => [
								'description' => __( 'The entitlement status.', 'tribe-common' ),
								'type'        => 'string',
							],
							'expires'           => [
								'description' => __( 'The expiration date.', 'tribe-common' ),
								'type'        => 'string',
								'format'      => 'date-time',
							],
							'activations'       => [
								'description' => __( 'Activation seat data.', 'tribe-common' ),
								'type'        => 'object',
								'properties'  => [
									'site_limit'   => [
										'description' => __( 'Maximum activation seats (0 = unlimited).', 'tribe-common' ),
										'type'        => 'integer',
									],
									'active_count' => [
										'description' => __( 'Current active activations.', 'tribe-common' ),
										'type'        => 'integer',
									],
									'over_limit'   => [
										'description' => __( 'Whether the seat limit is exceeded.', 'tribe-common' ),
										'type'        => 'boolean',
									],
									'domains'      => [
										'description' => __( 'Activated domain names.', 'tribe-common' ),
										'type'        => 'array',
										'items'       => [ 'type' => 'string' ],
									],
								],
							],
							'capabilities'      => [
								'description' => __( 'Feature slugs granted by this entitlement.', 'tribe-common' ),
								'type'        => 'array',
								'items'       => [ 'type' => 'string' ],
							],
							'activated_here'    => [
								'description' => __( 'Whether the product is activated on this domain.', 'tribe-common' ),
								'type'        => [ 'boolean', 'null' ],
							],
							'validation_status' => [
								'description' => __( 'The validation status for this product.', 'tribe-common' ),
								'type'        => [ 'string', 'null' ],
							],
							'is_valid'          => [
								'description' => __( 'Whether the product has a valid license.', 'tribe-common' ),
								'type'        => 'boolean',
							],
						],
					],
				],
				'error'    => [
					'description' => __( 'An error encountered while fetching the license, or null if none.', 'tribe-common' ),
					'type'        => [ 'object', 'null' ],
					'context'     => [ 'view' ],
					'properties'  => [
						'code'    => [
							'description' => __( 'The error code.', 'tribe-common' ),
							'type'        => 'string',
						],
						'message' => [
							'description' => __( 'The error message.', 'tribe-common' ),
							'type'        => 'string',
						],
					],
				],
			],
		];

		/** @var array<string, mixed> */
		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Gets the argument definitions for the store (POST) endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_store_args(): array {
		return array_merge(
			[
				'key' => [
					'description'       => __( 'The license key to store.', 'tribe-common' ),
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static function ( $value ): bool {
						return is_string( $value ) && License_Key::is_valid_format( $value );
					},
				],
			],
			$this->get_network_args()
		);
	}

	/**
	 * Gets the shared network argument definition.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_network_args(): array {
		return [
			'network' => [
				'description' => __( 'Whether to operate on the network-level key (multisite only).', 'tribe-common' ),
				'type'        => 'boolean',
				'default'     => false,
			],
		];
	}
}
