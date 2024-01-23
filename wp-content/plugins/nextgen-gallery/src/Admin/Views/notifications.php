<?php
/**
 * Footer Template
 *
 * @package Envira Gallery Lite
 */

use Imagely\NGG\Admin\AMNotifications as Notifications;
$notifications = new Notifications();
?>

<div class="nextgen-notifications-drawer" id="nextgen-notifications-drawer">
			<div class="nextgen-notifications-header">
				<h3 id="nextgen-active-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of active notifications.
							__( 'New Notifications (%s)', 'nggallery' )
						),
						'<span id="nextgen-notifications-count">' . absint( $notifications->get_count() ) . '</span>'
					);
					?>
				</h3>
				<h3 id="nextgen-dismissed-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of dismissed notifications.
							__( 'Notifications (%s)', 'nggallery' )
						),
						'<span id="nextgen-notifications-dismissed-count">' . absint( $notifications->get_dismissed_count() ) . '</span>'
					);
					?>
				</h3>
				<a href="#" class="nextgen-button-text" id="nextgen-notifications-show-dismissed">
					<?php esc_html_e( 'Dismissed Notifications', 'nggallery' ); ?>
				</a>
				<a href="#" class="nextgen-button-text" id="nextgen-notifications-show-active">
					<?php esc_html_e( 'Active Notifications', 'nggallery' ); ?>
				</a>
				<a class="nextgen-just-icon-button nextgen-notifications-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path d="M18.984 6.422L13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z"></path>
					</svg>
				</a>
			</div>
			<div class="nextgen-notifications-list">
				<ul class="nextgen-notifications-active">
					<?php
					$active_notifications = $notifications->get_active_notifications();
					foreach ( $active_notifications as $active ) {
						$notifications->get_notification_markup( $active );
					}
					?>
				</ul>
				<ul class="nextgen-notifications-dismissed">
					<?php
					$dismissed_notifications = $notifications->get_dismissed_notifications();
					foreach ( $dismissed_notifications as $dismissed ) {
						$notifications->get_notification_markup( $dismissed );
					}
					?>
				</ul>
			</div>
			<div class="nextgen-notifications-footer">
				<a href="#" class="nextgen-button-text nextgen-notification-dismiss" id="nextgen-dismiss-all" data-id="all"><?php esc_html_e( 'Dismiss all', 'nggallery' ); ?></a>
			</div>
		</div>
