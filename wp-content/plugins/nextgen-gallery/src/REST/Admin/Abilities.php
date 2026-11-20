<?php

namespace Imagely\NGG\REST\Admin;

use Imagely\NGG\Abilities\Abilities as AbilitiesHandler;
use Imagely\NGG\Abilities\AbstractAbilities;

/**
 * REST API controller for the Settings → AI & MCP tab.
 *
 * Reports the abilities NextGEN declares, whether WordPress registered them and whether an MCP
 * bridge is active, and stores the write opt-in.
 */
class Abilities extends \WP_REST_Controller {

	/**
	 * Known MCP bridge plugins, keyed by wp.org slug.
	 *
	 * @var array<string, array{basename: string, label: string}>
	 */
	const BRIDGES = [
		'vibe-ai'     => [
			'basename' => 'vibe-ai/vibe-ai.php',
			'label'    => 'WPVibe',
		],
		'mcp-adapter' => [
			'basename' => 'mcp-adapter/mcp-adapter.php',
			'label'    => 'MCP Adapter',
		],
	];

	public function __construct() {
		$this->namespace = 'imagely/v1';
		$this->rest_base = 'abilities';
	}

	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_status' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_status' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'write_enabled' => [
							'type'     => 'boolean',
							'required' => true,
						],
					],
				],
			]
		);
	}

	/**
	 * Who may view the tab and switch writes on: administrators of this site.
	 *
	 * The switch decides whether AI assistants may change galleries at all, so it is held to
	 * the site-settings capability, not to a NextGEN capability a role editor can hand out.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return bool
	 */
	public function permissions_check( $request ) {
		return \Imagely\NGG\Admin\App::can_access_abilities_settings();
	}

	/**
	 * Current state of the abilities.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response
	 */
	public function get_status( $request ) {
		return new \WP_REST_Response( $this->build_status(), 200 );
	}

	/**
	 * Store the write opt-in.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_status( $request ) {
		$enabled = \rest_sanitize_boolean( $request->get_param( 'write_enabled' ) );
		$value   = $enabled ? '1' : '0';

		// update_option() returns false both on failure and when the value is unchanged.
		if ( ! \update_option( AbstractAbilities::WRITE_OPTION, $value, false ) && \get_option( AbstractAbilities::WRITE_OPTION ) !== $value ) {
			return new \WP_Error(
				'ngg_abilities_save_failed',
				\__( 'The setting could not be saved.', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		return new \WP_REST_Response( $this->build_status(), 200 );
	}

	/**
	 * Assemble the status the tab renders.
	 *
	 * @return array
	 */
	private function build_status(): array {
		$handler   = AbilitiesHandler::instance();
		$available = $handler->is_abilities_api_available();

		return [
			'available'           => $available,
			// The stored opt-in, and the effective value after the ngg_abilities_allow_write
			// filter, so the tab can say when code overrides the switch.
			'write_option'        => (bool) \get_option( AbstractAbilities::WRITE_OPTION, false ),
			'write_enabled'       => $handler->write_enabled(),
			'category_registered' => $available ? $handler->is_category_registered() : false,
			'failed'              => $available ? $handler->get_failed_registrations() : [],
			'abilities'           => $available ? $this->get_abilities( $handler ) : [],
			'bridge'              => $this->get_bridge(),
		];
	}

	/**
	 * Summarise the declared abilities.
	 *
	 * Read from the handler rather than the registry: the registry is only populated on
	 * requests where wp_abilities_api_init has fired.
	 *
	 * @param AbstractAbilities $handler Abilities handler.
	 * @return array<int, array{name: string, label: string, readonly: bool, destructive: bool, mcp_public: bool}>
	 */
	private function get_abilities( AbstractAbilities $handler ): array {
		$rows = [];

		foreach ( $handler->get_ability_definitions() as $name => $args ) {
			if ( ! is_array( $args ) ) {
				continue;
			}

			$annotations = isset( $args['meta']['annotations'] ) && is_array( $args['meta']['annotations'] )
				? $args['meta']['annotations']
				: [];

			$rows[] = [
				'name'        => (string) $name,
				'label'       => isset( $args['label'] ) ? (string) $args['label'] : (string) $name,
				'readonly'    => ! empty( $annotations['readonly'] ),
				'destructive' => ! empty( $annotations['destructive'] ),
				'mcp_public'  => ! empty( $args['meta']['mcp']['public'] ),
			];
		}

		return $rows;
	}

	/**
	 * The active MCP bridge, or the actions available to get one.
	 *
	 * Links point at the core plugin install and activate screens; nothing is fetched here.
	 *
	 * @return array{active: string|null, actions: array<int, array{label: string, url: string}>}
	 */
	private function get_bridge(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach ( self::BRIDGES as $bridge ) {
			if ( \is_plugin_active( $bridge['basename'] ) ) {
				return [
					'active'  => $bridge['label'],
					'actions' => [],
				];
			}
		}

		// The adapter can also be loaded as a library rather than a plugin.
		if ( class_exists( '\\WP\\MCP\\Core\\McpAdapter' ) ) {
			return [
				'active'  => self::BRIDGES['mcp-adapter']['label'],
				'actions' => [],
			];
		}

		$installed = (array) \get_plugins();
		$actions   = [];

		foreach ( self::BRIDGES as $slug => $bridge ) {
			$is_installed = isset( $installed[ $bridge['basename'] ] );

			if ( $is_installed && \current_user_can( 'activate_plugins' ) ) {
				$actions[] = [
					/* translators: %s: bridge plugin name. */
					'label' => sprintf( \__( 'Activate %s', 'nggallery' ), $bridge['label'] ),
					// add_query_arg(), not wp_nonce_url(): the latter returns an HTML-encoded `&amp;`,
					// which a React href would send literally.
					'url'   => \add_query_arg(
						[
							'action'   => 'activate',
							'plugin'   => rawurlencode( $bridge['basename'] ),
							'_wpnonce' => \wp_create_nonce( 'activate-plugin_' . $bridge['basename'] ),
						],
						\self_admin_url( 'plugins.php' )
					),
				];
			} elseif ( ! $is_installed && \current_user_can( 'install_plugins' ) ) {
				$actions[] = [
					/* translators: %s: bridge plugin name. */
					'label' => sprintf( \__( 'Get %s', 'nggallery' ), $bridge['label'] ),
					'url'   => \self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . rawurlencode( $slug ) ),
				];
			}
		}

		return [
			'active'  => null,
			'actions' => $actions,
		];
	}
}
