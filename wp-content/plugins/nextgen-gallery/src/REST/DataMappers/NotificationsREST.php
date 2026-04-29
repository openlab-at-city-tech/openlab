<?php

namespace Imagely\NGG\REST\DataMappers;

use Imagely\NGG\Admin\AMNotifications;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * REST API endpoints for notifications
 *
 * @since 3.5.0
 */
class NotificationsREST {

	/**
	 * Register REST routes
	 *
	 * @since 3.5.0
	 */
	public function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/notifications',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_notifications' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			'imagely/v1',
			'/notifications/dismiss',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'dismiss_notification' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $param ) {
							return ! empty( $param );
						},
					],
				],
			]
		);
	}

	/**
	 * Check permissions for notifications endpoints
	 *
	 * @since 3.5.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return bool|WP_Error
	 */
	public function check_permissions( WP_REST_Request $request ) {
		// Use the same security pattern as other NextGen REST endpoints
		return \Imagely\NGG\Util\Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Get notifications data
	 *
	 * @since 3.5.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_notifications( WP_REST_Request $request ) {
		$notifications_manager   = new AMNotifications();
		$active_notifications    = $notifications_manager->get_active_notifications();
		$dismissed_notifications = $notifications_manager->get_dismissed_notifications();

		return new WP_REST_Response(
			[
				'active'    => $active_notifications,
				'dismissed' => $dismissed_notifications,
				'counts'    => [
					'active'    => count( $active_notifications ),
					'dismissed' => count( $dismissed_notifications ),
				],
			],
			200
		);
	}

	/**
	 * Dismiss notification(s)
	 *
	 * @since 3.5.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function dismiss_notification( WP_REST_Request $request ) {
		$notifications_manager = new AMNotifications();

		if ( ! $notifications_manager->has_access() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to dismiss notifications.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		$id = $request->get_param( 'id' );

		// Use the existing dismiss logic from AMNotifications
		$result = $this->dismiss_notification_by_id( $id );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return updated counts after dismissal
		$active_notifications    = $notifications_manager->get_active_notifications();
		$dismissed_notifications = $notifications_manager->get_dismissed_notifications();

		return new WP_REST_Response(
			[
				'success' => true,
				'counts'  => [
					'active'    => count( $active_notifications ),
					'dismissed' => count( $dismissed_notifications ),
				],
			],
			200
		);
	}

	/**
	 * Dismiss notification by ID (refactored from AMNotifications::dismiss)
	 *
	 * @since 3.5.0
	 *
	 * @param string $id The notification ID to dismiss.
	 * @return bool|WP_Error
	 */
	private function dismiss_notification_by_id( $id ) {
		$notifications_manager = new AMNotifications();
		$option                = $notifications_manager->get_option();

		// Dismiss all notifications and add them to dismiss array
		if ( 'all' === $id ) {
			if ( is_array( $option['feed'] ) && ! empty( $option['feed'] ) ) {
				foreach ( $option['feed'] as $key => $notification ) {
					array_unshift( $option['dismissed'], $notification );
					unset( $option['feed'][ $key ] );
				}
			}
			if ( is_array( $option['events'] ) && ! empty( $option['events'] ) ) {
				foreach ( $option['events'] as $key => $notification ) {
					array_unshift( $option['dismissed'], $notification );
					unset( $option['events'][ $key ] );
				}
			}
		} else {
			$type = is_numeric( $id ) ? 'feed' : 'events';

			// Remove notification and add in dismissed array
			if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
				$found = false;
				foreach ( $option[ $type ] as $key => $notification ) {
					if ( $notification['id'] == $id ) { // phpcs:ignore WordPress.PHP.StrictComparisons,Universal.Operators.StrictComparisons.LooseEqual
						// Add notification to dismissed array
						array_unshift( $option['dismissed'], $notification );
						// Remove notification from feed or events
						unset( $option[ $type ][ $key ] );
						$found = true;
						break;
					}
				}

				if ( ! $found ) {
					return new WP_Error(
						'notification_not_found',
						__( 'Notification not found.', 'nggallery' ),
						[ 'status' => 404 ]
					);
				}
			}
		}

		update_option( AMNotifications::$option_name, $option, false );

		return true;
	}
}
