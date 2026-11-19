<?php
/**
 * Kadence\FSE\Rest class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST endpoints the dashboard uses to read and change Full Site Editing mode, and to read or dismiss its repair checklist.
 *
 * @since 1.6.0
 */
final class Rest {

	/**
	 * Route namespace.
	 *
	 * @since 1.6.0
	 */
	private const ROUTE_NAMESPACE = 'kadence/v1';

	/**
	 * Route path.
	 *
	 * @since 1.6.0
	 */
	private const ROUTE = '/fse-mode';

	/**
	 * Repair checklist route path.
	 *
	 * @since 1.6.0
	 */
	private const REPAIR_ROUTE = '/fse-repair';

	/**
	 * Capability required to read or change the mode.
	 *
	 * @since 1.6.0
	 */
	private const CAPABILITY = 'edit_theme_options';

	/**
	 * Full Site Editing mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Mode
	 */
	private Mode $mode;

	/**
	 * Repair checklist.
	 *
	 * @since 1.6.0
	 *
	 * @var Repair
	 */
	private Repair $repair;

	/**
	 * Whether the repair checklist was dismissed.
	 *
	 * @since 1.6.0
	 *
	 * @var Repair_Dismissal
	 */
	private Repair_Dismissal $dismissal;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Mode             $mode      Full Site Editing mode.
	 * @param Repair           $repair    Repair checklist.
	 * @param Repair_Dismissal $dismissal Whether the repair checklist was dismissed.
	 */
	public function __construct( Mode $mode, Repair $repair, Repair_Dismissal $dismissal ) {
		$this->mode      = $mode;
		$this->repair    = $repair;
		$this->dismissal = $dismissal;
	}

	/**
	 * Registers the routes.
	 *
	 * @since 1.6.0
	 */
	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_mode' ],
					'permission_callback' => [ $this, 'can_manage' ],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_mode' ],
					'permission_callback' => [ $this, 'can_manage' ],
					'args'                => [
						'enabled' => [
							'type'     => 'boolean',
							'required' => true,
						],
					],
				],
			]
		);

		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::REPAIR_ROUTE,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_repair' ],
					'permission_callback' => [ $this, 'can_manage' ],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_repair' ],
					'permission_callback' => [ $this, 'can_manage' ],
					'args'                => [
						'dismissed' => [
							'type' => 'boolean',
						],
						'type'      => [
							'type' => 'string',
							'enum' => [ 'wp_template', 'wp_template_part', 'notice' ],
						],
						'slug'      => [
							'type' => 'string',
						],
						'done'      => [
							'type' => 'boolean',
						],
					],
				],
			]
		);
	}

	/**
	 * The mode route's path, as the REST API client expects it.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_path(): string {
		return '/' . self::ROUTE_NAMESPACE . self::ROUTE;
	}

	/**
	 * The repair checklist route's path, as the REST API client expects it.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_repair_path(): string {
		return '/' . self::ROUTE_NAMESPACE . self::REPAIR_ROUTE;
	}

	/**
	 * Whether the current user can read or change the mode.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function can_manage(): bool {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Returns the current mode.
	 *
	 * @since 1.6.0
	 *
	 * @return WP_REST_Response
	 */
	public function get_mode(): WP_REST_Response {
		return new WP_REST_Response( [ 'enabled' => $this->mode->is_enabled() ] );
	}

	/**
	 * Turns the mode on or off.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_mode( WP_REST_Request $request ) {
		if ( $request['enabled'] && ! $this->mode->is_supported() ) {
			return new WP_Error(
				'kadence_fse_mode_unsupported',
				sprintf(
					/* translators: %s: WordPress version number. */
					__( 'Full Site Editing needs WordPress %s or later.', 'kadence' ),
					$this->mode->minimum_wp_version()
				),
				[ 'status' => 400 ]
			);
		}

		$saved = $request['enabled'] ? $this->mode->enable() : $this->mode->disable();

		if ( ! $saved ) {
			return new WP_Error(
				'kadence_fse_mode_not_saved',
				__( 'The setting could not be saved. Please try again.', 'kadence' ),
				[ 'status' => 500 ]
			);
		}

		return $this->get_mode();
	}

	/**
	 * Returns the repair checklist, empty while the mode is off, whether it was dismissed, and whether every item is done.
	 *
	 * @since 1.6.0
	 *
	 * @return WP_REST_Response
	 */
	public function get_repair(): WP_REST_Response {
		$items = $this->repair->get_checklist();

		return new WP_REST_Response(
			[
				'items'     => $items,
				'dismissed' => $this->dismissal->is_dismissed(),
				'complete'  => $this->repair->is_complete( $items ),
			]
		);
	}

	/**
	 * Marks a checklist item done or to do, or dismisses the checklist on this site or shows it again.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_repair( WP_REST_Request $request ) {
		if ( isset( $request['done'], $request['type'], $request['slug'] ) ) {
			$saved = $this->repair->set_status( $request['type'], $request['slug'], $request['done'] );
		} elseif ( isset( $request['dismissed'] ) ) {
			$saved = $this->dismissal->set( $request['dismissed'] );
		} else {
			return new WP_Error(
				'kadence_fse_repair_missing_param',
				__( 'Send dismissed, or done with the item’s type and slug.', 'kadence' ),
				[ 'status' => 400 ]
			);
		}

		if ( ! $saved ) {
			return new WP_Error(
				'kadence_fse_repair_not_saved',
				__( 'The setting could not be saved. Please try again.', 'kadence' ),
				[ 'status' => 500 ]
			);
		}

		return $this->get_repair();
	}
}
