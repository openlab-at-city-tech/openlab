<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Legacy\Legacy_License;
use TEC\Common\LiquidWeb\Harbor\Legacy\License_Repository;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * WP REST API controller for reading legacy per-plugin license data.
 *
 * @since 1.0.0
 */
final class Legacy_License_Controller extends WP_REST_Controller {

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
	protected $rest_base = 'legacy-licenses';

	/**
	 * The legacy license repository.
	 *
	 * @since 1.0.0
	 *
	 * @var License_Repository
	 */
	private License_Repository $repository;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param License_Repository $repository The legacy license repository.
	 *
	 * @return void
	 */
	public function __construct( License_Repository $repository ) {
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
	 * Returns all legacy licenses.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_items( $request ): WP_REST_Response {
		$licenses = array_map(
			static fn( Legacy_License $license ): array => $license->to_array(),
			$this->repository->all()
		);

		return new WP_REST_Response( $licenses );
	}

	/**
	 * Gets the schema for a legacy license item.
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
			'title'      => 'legacy-license',
			'type'       => 'object',
			'properties' => [
				'key'        => [
					'description' => __( 'The license key.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'slug'       => [
					'description' => __( 'The plugin slug.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'name'       => [
					'description' => __( 'The plugin name.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'product'    => [
					'description' => __( 'The product name.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'is_active'  => [
					'description' => __( 'Whether the license is currently active.', 'tribe-common' ),
					'type'        => 'boolean',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'page_url'   => [
					'description' => __( 'The license management page URL.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
				'expires_at' => [
					'description' => __( 'The license expiration date.', 'tribe-common' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => [ 'view' ],
				],
			],
		];

		/** @var array<string, mixed> */
		return $this->add_additional_fields_schema( $this->schema );
	}
}
