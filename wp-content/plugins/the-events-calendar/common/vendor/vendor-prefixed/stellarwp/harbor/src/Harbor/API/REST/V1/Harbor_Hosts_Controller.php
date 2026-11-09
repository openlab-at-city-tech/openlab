<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * WP REST API controller for reading the active Harbor host plugins.
 *
 * Returns the flat list of plugin basenames that registered themselves in the
 * Harbor instance registry during this request's bootstrap. Because this is a
 * fresh HTTP request, all currently active Harbor-bundled plugins are present.
 *
 * @since 1.0.0
 */
final class Harbor_Hosts_Controller extends WP_REST_Controller {

	/**
	 * @since 1.0.0
	 * @var string
	 */
	protected $namespace = 'liquidweb/harbor/v1';

	/**
	 * @since 1.0.0
	 * @var string
	 */
	protected $rest_base = 'hosts';

	/**
	 * @inheritDoc
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
	 * Returns the list of plugin basenames registered in the Harbor instance registry.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_items( $request ): WP_REST_Response {
		$registry  = _lw_harbor_instance_registry();
		$basenames = array_reduce(
			$registry,
			static function ( array $carry, array $files ): array {
				return array_merge( $carry, $files );
			},
			[]
		);

		return new WP_REST_Response( $basenames, 200 );
	}

	/**
	 * Checks whether the current user has permission to read the hosts list.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_permissions(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns the JSON schema for the hosts list response.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function get_public_item_schema(): array {
		return [
			'$schema'     => 'http://json-schema.org/draft-04/schema#',
			'title'       => 'harbor-hosts',
			'type'        => 'array',
			'description' => __( 'Plugin basenames of all active Harbor-bundled plugins registered during this request.', 'tribe-common' ),
			'items'       => [
				'type'        => 'string',
				'description' => __( 'Plugin basename (e.g. give/give.php).', 'tribe-common' ),
			],
		];
	}
}
