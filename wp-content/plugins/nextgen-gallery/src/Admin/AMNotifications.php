<?php
/**
 * Envira Notifications Class
 *
 * @since 1.8.7
 *
 * @package Envira Gallery Lite
 */

namespace Imagely\NGG\Admin;

/**
 * Notifications Class
 *
 * @since 1.8.7
 */
class AMNotifications {

	/**
	 * Source of notifications content.
	 *
	 * @since 1.8.7
	 *
	 * @var string
	 */
	const SOURCE_URL = 'https://plugin.imagely.com/wp-content/notifications.json';

	/**
	 * Option value.
	 *
	 * @since 1.8.7
	 *
	 * @var bool|array
	 */
	public $option = false;

	/**
	 * The name of the option used to store the data.
	 *
	 * @since 1.8.7
	 *
	 * @var string
	 */
	public static $option_name = 'nextgen_notifications';

	/**
	 * Register hooks.
	 *
	 * @since 1.8.7
	 */
	public function hooks() {

		add_action( 'wp_ajax_nextgen_notification_dismiss', [ $this, 'dismiss' ] );
		add_action( 'nextgen_admin_notifications_update', [ $this, 'update' ] );
	}

	/**
	 * Check if user has access and is enabled.
	 *
	 * @since 1.8.7
	 *
	 * @return bool
	 */
	public function has_access() {
		return apply_filters( 'nextgen_admin_notifications_has_access', ! get_option( 'hide_am_notices', false ) );
	}

	/**
	 * Get option value.
	 *
	 * @since 1.8.7
	 *
	 * @param bool $cache Reference property cache if available.
	 * @return array
	 */
	public function get_option( $cache = true ) {

		if ( $this->option && $cache ) {
			return $this->option;
		}

		$option = get_option( self::$option_name, [] );

		$this->option = [
			'update'    => ! empty( $option['update'] ) ? $option['update'] : 0,
			'events'    => ! empty( $option['events'] ) ? $option['events'] : [],
			'feed'      => ! empty( $option['feed'] ) ? $option['feed'] : [],
			'dismissed' => ! empty( $option['dismissed'] ) ? $option['dismissed'] : [],
		];

		return $this->option;
	}

	/**
	 * Fetch notifications from feed.
	 *
	 * @since 1.8.7
	 *
	 * @return array
	 */
	public function fetch_feed() {

		$res = wp_remote_get( self::SOURCE_URL );

		if ( is_wp_error( $res ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $res );
		if ( empty( $body ) ) {
			return [];
		}

		return $this->verify( json_decode( $body, true ) );
	}

	/**
	 * Verify notification data before it is saved.
	 *
	 * @since 1.8.7
	 *
	 * @param array $notifications Array of notifications items to verify.
	 * @return array
	 */
	public function verify( $notifications ) {
		$data = [];

		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return $data;
		}

		$option = $this->get_option();

		foreach ( $notifications as $notification ) {

			// The message and license should never be empty, if they are, ignore.
			if ( empty( $notification['content'] ) && empty( $notification['type'] ) ) {
				continue;
			}

			// Ignore if notification is not ready to display(based on start time).
			if ( ! empty( $notification['start'] ) && time() < strtotime( $notification['start'] ) ) {
				continue;
			}

			// Ignore if expired.
			if ( ! empty( $notification['end'] ) && time() > strtotime( $notification['end'] ) ) {
				continue;
			}

			// Check that the license type matches.
			if ( ! in_array( $this->get_license_type(), (array) $notification['type'], true ) ) {
				continue;
			}

			// Ignore if notification has already been dismissed.
			$notification_already_dismissed = false;
			if ( is_array( $option['dismissed'] ) && ! empty( $option['dismissed'] ) ) {
				foreach ( $option['dismissed'] as $dismiss_notification ) {
					if ( $notification['id'] === $dismiss_notification['id'] ) {
						$notification_already_dismissed = true;
						break;
					}
				}
			}

			if ( true === $notification_already_dismissed ) {
				continue;
			}

			$data[] = $notification;
		}
		return $data;
	}

	/**
	 * Verify saved notification data for active notifications.
	 *
	 * @since 1.8.7
	 *
	 * @param array $notifications Array of notifications items to verify.
	 * @return array
	 */
	public function verify_active( $notifications ) {

		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return [];
		}

		// Remove notifications that are not active, or if the license type not exists.
		foreach ( $notifications as $key => $notification ) {
			if (
				( ! empty( $notification['start'] ) && time() < strtotime( $notification['start'] ) ) ||
				( ! empty( $notification['end'] ) && time() > strtotime( $notification['end'] ) )
			) {
				unset( $notifications[ $key ] );
			}
		}

		return $notifications;
	}

	/**
	 * Get notification data.
	 *
	 * @since 1.8.7
	 *
	 * @return array
	 */
	public function get() {

		if ( ! $this->has_access() ) {
			return [];
		}

		$option = $this->get_option();

		// Update notifications using async task.
		if ( empty( $option['update'] ) || time() > $option['update'] + DAY_IN_SECONDS ) {
			if ( false === wp_next_scheduled( 'nextgen_admin_notifications_update' ) ) {
				wp_schedule_single_event( time(), 'nextgen_admin_notifications_update' );
			}
		}

		$events = ! empty( $option['events'] ) ? $this->verify_active( $option['events'] ) : [];
		$feed   = ! empty( $option['feed'] ) ? $this->verify_active( $option['feed'] ) : [];

		$notifications              = [];
		$notifications['active']    = array_merge( $events, $feed );
		$notifications['active']    = $this->get_notifications_with_human_readeable_start_time( $notifications['active'] );
		$notifications['active']    = $this->get_notifications_with_formatted_content( $notifications['active'] );
		$notifications['dismissed'] = ! empty( $option['dismissed'] ) ? $option['dismissed'] : [];
		$notifications['dismissed'] = $this->get_notifications_with_human_readeable_start_time( $notifications['dismissed'] );
		$notifications['dismissed'] = $this->get_notifications_with_formatted_content( $notifications['dismissed'] );

		return $notifications;
	}

	/**
	 * Improve format of the content of notifications before display. By default, it just runs wpautop.
	 *
	 * @since 1.8.7
	 *
	 * @param array $notifications The notifications to be parsed.
	 * @return array
	 */
	public function get_notifications_with_formatted_content( $notifications ) {
		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return $notifications;
		}

		foreach ( $notifications as $key => $notification ) {
			if ( ! empty( $notification['content'] ) ) {
				$notifications[ $key ]['content'] = wpautop( $notification['content'] );
				$notifications[ $key ]['content'] = apply_filters( 'nextgen_notification_content_display', $notifications[ $key ]['content'] );
			}
		}

		return $notifications;
	}

	/**
	 * Get notifications start time with human time difference
	 *
	 * @since 1.8.7
	 *
	 * @param array $notifications The array of notifications to convert.
	 * @return array
	 */
	public function get_notifications_with_human_readeable_start_time( $notifications ) {
		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return [];
		}

		foreach ( $notifications as $key => $notification ) {
			if ( empty( $notification['start'] ) ) {
				continue;
			}

			// Translators: Human-Readable time to display.
			$modified_start_time            = sprintf( __( '%1$s ago', 'nggallery' ), human_time_diff( strtotime( $notification['start'] ), time() ) );
			$notifications[ $key ]['start'] = $modified_start_time;
		}

		return $notifications;
	}

	/**
	 * Get active notifications.
	 *
	 * @since 1.8.7
	 *
	 * @return array $notifications['active'] active notifications
	 */
	public function get_active_notifications() {

		$notifications = $this->get();

		// Show only 5 active notifications plus any that has a priority of 1.
		$all_active = isset( $notifications['active'] ) ? $notifications['active'] : [];
		$displayed  = [];

		foreach ( $all_active as $notification ) {
			if ( ( isset( $notification['priority'] ) && 1 === $notification['priority'] ) || count( $displayed ) < 5 ) {
				$displayed[] = $notification;
			}
		}

		return $displayed;
	}

	/**
	 * Get dismissed notifications.
	 *
	 * @since 1.8.7
	 *
	 * @return array $notifications['dismissed'] dismissed notifications
	 */
	public function get_dismissed_notifications() {
		$notifications = $this->get();

		return isset( $notifications['dismissed'] ) ? $notifications['dismissed'] : [];
	}

	/**
	 * Get notification count.
	 *
	 * @since 1.8.7
	 *
	 * @return int
	 */
	public function get_count() {
		return count( $this->get_active_notifications() );
	}

	/**
	 * Get the dismissed notifications count.
	 *
	 * @since 1.8.7
	 *
	 * @return int
	 */
	public function get_dismissed_count() {
		return count( $this->get_dismissed_notifications() );
	}

	/**
	 * Check if a notification has been dismissed before
	 *
	 * @since 1.8.7
	 *
	 * @param array $notification The notification to check if is dismissed.
	 * @return bool
	 */
	public function is_dismissed( $notification ) {
		if ( empty( $notification['id'] ) ) {
			return true;
		}

		$option = $this->get_option();

		foreach ( $option['dismissed'] as $item ) {
			if ( $item['id'] === $notification['id'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add a manual notification event.
	 *
	 * @since 1.8.7
	 *
	 * @param array $notification Notification data.
	 * @return bool
	 */
	public function add( $notification ) {

		if ( empty( $notification['id'] ) || $this->is_dismissed( $notification ) ) {
			return false;
		}

		$option = $this->get_option();

		$current_notifications = $option['events'];

		foreach ( $current_notifications as $item ) {
			if ( $item['id'] === $notification['id'] ) {
				return false;
			}
		}

		$notification = $this->verify( [ $notification ] );

		$notifications = array_merge( $notification, $current_notifications );

		// Sort notifications by priority.
		usort(
			$notifications,
			function ( $a, $b ) {
				if ( ! isset( $a['priority'] ) || ! isset( $b['priority'] ) ) {
					return 0;
				}

				if ( $a['priority'] === $b['priority'] ) {
					return 0;
				}

				return $a['priority'] < $b['priority'] ? - 1 : 1;
			}
		);

		update_option(
			self::$option_name,
			[
				'update'    => $option['update'],
				'feed'      => $option['feed'],
				'events'    => $notifications,
				'dismissed' => $option['dismissed'],
			],
			false
		);

		return true;
	}

	/**
	 * Update notification data from feed.
	 *
	 * @since 1.8.7
	 *
	 * @return void
	 */
	public function update() {

		$feed   = $this->fetch_feed();
		$option = $this->get_option();

		update_option(
			self::$option_name,
			[
				'update'    => time(),
				'feed'      => $feed,
				'timeout'   => strtotime( '+2 hours', current_time( 'timestamp' ) ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'events'    => $option['events'],
				'dismissed' => array_slice( $option['dismissed'], 0, 30 ),
			],
			false
		);
	}

	/**
	 * Dismiss notification via AJAX.
	 *
	 * @since 1.8.7
	 */
	public function dismiss() {

		// Run a security check.
		check_ajax_referer( 'nextgen_dismiss_notification', 'nonce' );

		// Check for access and required param.
		if ( ! $this->has_access() || empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = $this->get_option();

		// Dismiss all notifications and add them to dissmiss array.
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
		}

		$type = is_numeric( $id ) ? 'feed' : 'events';

		// Remove notification and add in dismissed array.
		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( $notification['id'] == $id ) { // phpcs:ignore WordPress.PHP.StrictComparisons,Universal.Operators.StrictComparisons.LooseEqual
					// Add notification to dismissed array.
					array_unshift( $option['dismissed'], $notification );
					// Remove notification from feed or events.
					unset( $option[ $type ][ $key ] );
					break;
				}
			}
		}

		update_option( self::$option_name, $option, false );

		wp_send_json_success();
	}

	/**
	 * Delete the notification options.
	 *
	 * @since 1.8.7
	 *
	 * @return void
	 */
	public static function delete_notifications_data() {
		delete_option( self::$option_name );
	}

	/**
	 * Get the license type for the current plugin.
	 *
	 * @since 1.8.7
	 *
	 * @return string
	 */
	public function get_license_type() {

		if ( defined( 'NGG_PRO_PLUGIN_BASENAME' ) ) {
			return 'pro';
		} elseif ( defined( 'NGG_PLUS_PLUGIN_BASENAME' ) ) {
			return 'plus';
		} elseif ( defined( 'NGG_STARTER_PLUGIN_BASENAME' ) ) {
			return 'starter';
		}

		return 'lite';
	}

	/**
	 * Helper Method to get icon
	 *
	 * @since 1.8.7
	 *
	 * @param string $type Icon type.
	 * @return string
	 */
	public function get_icon( $type = 'gear' ) {
		switch ( $type ) {
			case 'info':
				return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
					<path fill="#EBAD35" d="M8.167 4.833h1.666V6.5H8.167V4.833zm0 3.333h1.666v5H8.167v-5zM9 .666A8.336 8.336 0 00.667 9c0 4.6 3.733 8.333 8.333 8.333S17.333 13.6 17.333 9 13.6.667 9 .667zm0 15A6.676 6.676 0 012.333 9 6.676 6.676 0 019 2.333 6.675 6.675 0 0115.667 9 6.675 6.675 0 019 15.666z"></path>
				</svg>';
			case 'percent':
				return '<svg
				xmlns="http://www.w3.org/2000/svg"
				width="20"
				height="20"
				fill="none"
				viewBox="0 0 20 20"
			  >
				<mask
				  id="mask0_161_1127"
				  style={{ maskType: "alpha" }}
				  width="20"
				  height="20"
				  x="0"
				  y="0"
				  maskUnits="userSpaceOnUse"
				>
				</mask>
				<g mask="url(#mask0_161_1127)">
				  <path
					fill="#D99B3D"
					d="M6.25 9.167a2.81 2.81 0 01-2.063-.854 2.81 2.81 0 01-.854-2.063c0-.805.285-1.493.854-2.062a2.81 2.81 0 012.063-.854 2.81 2.81 0 012.062.854c.57.57.855 1.257.855 2.062a2.81 2.81 0 01-.855 2.063 2.81 2.81 0 01-2.062.854zm0-1.667c.347 0 .642-.121.885-.364s.365-.539.365-.886-.122-.642-.365-.885A1.205 1.205 0 006.25 5c-.347 0-.642.122-.886.365A1.205 1.205 0 005 6.25c0 .347.121.643.364.886.244.243.539.364.886.364zm7.5 9.167a2.81 2.81 0 01-2.063-.854 2.81 2.81 0 01-.854-2.063c0-.805.285-1.493.854-2.062a2.81 2.81 0 012.063-.854 2.81 2.81 0 012.062.854c.57.57.855 1.257.855 2.062a2.81 2.81 0 01-.855 2.063 2.81 2.81 0 01-2.062.854zm0-1.667c.347 0 .642-.121.885-.364s.365-.539.365-.886-.122-.642-.365-.885a1.206 1.206 0 00-.885-.365c-.347 0-.642.122-.886.365a1.205 1.205 0 00-.364.885c0 .347.121.643.364.886.244.243.539.364.886.364zM4.5 16.667L3.333 15.5 15.5 3.334 16.667 4.5 4.5 16.667z"
				  ></path>
				</g>
			  </svg>';

			case 'check':
				return '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" fill="none" viewBox="0 0 21 20">
					<path fill="#37993B" d="M10.5 1.667A8.336 8.336 0 002.167 10c0 4.6 3.733 8.333 8.333 8.333S18.833 14.6 18.833 10 15.1 1.667 10.5 1.667zm0 15A6.676 6.676 0 013.833 10 6.676 6.676 0 0110.5 3.333 6.675 6.675 0 0117.167 10a6.675 6.675 0 01-6.667 6.666zm3.825-10.35l-5.492 5.491-2.158-2.15L5.5 10.833l3.333 3.333L15.5 7.5l-1.175-1.184z"></path>
				</svg>';

			default:
			case 'gear':
				return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
					<path fill="#3C434A" fillOpacity="0.7" fillRule="evenodd" d="M16.252 10a6.5 6.5 0 01-.058.816l1.758 1.375c.158.125.2.35.1.534l-1.667 2.883a.413.413 0 01-.358.208.47.47 0 01-.15-.025l-2.075-.833a6.4 6.4 0 01-1.409.817l-.316 2.208c-.025.2-.2.35-.409.35H8.335a.406.406 0 01-.408-.35l-.317-2.208a6.088 6.088 0 01-1.408-.817l-2.075.833a.416.416 0 01-.508-.183l-1.667-2.883a.42.42 0 01.1-.534l1.758-1.375A6.61 6.61 0 013.752 10c0-.275.025-.55.058-.817L2.052 7.808a.41.41 0 01-.1-.533L3.618 4.39a.413.413 0 01.359-.208c.05 0 .1.008.15.025l2.075.833c.433-.325.9-.608 1.408-.816l.317-2.208c.025-.2.2-.35.408-.35h3.333c.209 0 .384.15.409.35l.316 2.208c.509.208.975.483 1.409.817l2.075-.834a.416.416 0 01.508.183l1.667 2.884a.42.42 0 01-.1.533l-1.759 1.375a6.5 6.5 0 01.059.817zm-1.667 0c0-.175-.008-.35-.042-.608l-.116-.942.742-.583.891-.709-.583-1.008-1.059.425-.883.358-.758-.583a4.726 4.726 0 00-1.025-.592l-.884-.358-.133-.942-.158-1.125H9.418l-.166 1.125-.134.942-.883.358a4.881 4.881 0 00-1.042.609l-.75.566-.866-.35-1.059-.425-.583 1.008.9.7.742.584-.117.942c-.025.25-.042.441-.042.608 0 .166.017.358.042.617l.117.941-.742.584-.9.7.583 1.008 1.059-.425.883-.359.758.584c.334.25.667.441 1.025.591l.884.359.133.941.158 1.125h1.167l.167-1.125.133-.941.883-.358a4.886 4.886 0 001.042-.609l.75-.566.867.35 1.058.425.584-1.009-.9-.7-.742-.583.117-.942c.024-.25.041-.433.041-.608zm-4.583-3.333A3.332 3.332 0 1013.335 10a3.332 3.332 0 00-3.333-3.333zM8.335 10c0 .916.75 1.666 1.667 1.666.916 0 1.666-.75 1.666-1.666 0-.917-.75-1.667-1.666-1.667-.917 0-1.667.75-1.667 1.667z" clipRule="evenodd"></path>
				</svg>';
		}
	}

	/**
	 * Helper to get notification marketup
	 *
	 * @since 1.8.7
	 *
	 * @param array $notification The notification.
	 * @return void
	 */
	public function get_notification_markup( $notification ) {
		$type         = ! empty( $notification['icon'] ) ? $notification['icon'] : 'gear';
		$allowed_html = [
			'svg'   => [
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
			],
			'g'     => [ 'fill' => true ],
			'title' => [ 'title' => true ],
			'path'  => [
				'd'    => true,
				'fill' => true,
			],
		];
		?>
		<li>
			<div class="nextgen-notification-icon"><?php echo wp_kses( $this->get_icon( $notification['icon'] ), $allowed_html ); ?></div>
			<div class="nextgen-notification-content">
				<h4><?php echo esc_html( $notification['title'] ); ?></h4>
				<p><?php echo wp_kses_post( wp_strip_all_tags( $notification['content'] ) ); ?></p>
				<div class="nextgen-notification-actions">
					<?php
					$main_button = ! empty( $notification['btns']['main'] ) ? $notification['btns']['main'] : false;
					$alt_button  = ! empty( $notification['btns']['alt'] ) ? $notification['btns']['alt'] : false;
					if ( $main_button ) {
						?>
						<a href="<?php echo esc_url( $main_button['url'] ); ?>" class="nextgen-button nextgen-primary-button nextgen-button-small" target="_blank">
							<?php echo esc_html( $main_button['text'] ); ?>
						</a>
						<?php
					}
					if ( $alt_button ) {
						?>
						<a href="<?php echo esc_url( $alt_button['url'] ); ?>" class="nextgen-button nextgen-secondary-button nextgen-button-small" target="_blank">
							<?php echo esc_html( $alt_button['text'] ); ?>
						</a>
						<?php
					}
					?>
					<a href="#" class="nextgen-button-text nextgen-notification-dismiss" data-id="<?php echo esc_attr( $notification['id'] ); ?>"><?php esc_html_e( 'Dismiss', 'nggallery' ); ?></a>
				</div>
			</div>
		</li>
		<?php
	}
}