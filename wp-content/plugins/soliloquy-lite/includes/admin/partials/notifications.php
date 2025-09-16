<?php
/**
 * Footer Markup
 *
 * @package Soliloquy Lite.
 */

$base = Soliloquy_Lite::get_instance();

?>
<div class="soliloquy-notifications-drawer" id="soliloquy-notifications-drawer">
			<div class="soliloquy-notifications-header">
				<h3 id="soliloquy-active-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of active notifications.
							__( 'New Notifications (%s)', 'soliloquy' )
						),
						'<span id="soliloquy-notifications-count">' . absint( $base->notifications->get_count() ) . '</span>'
					);
					?>
				</h3>
				<h3 id="soliloquy-dismissed-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of dismissed notifications.
							__( 'Notifications (%s)', 'soliloquy' )
						),
						'<span id="soliloquy-notifications-dismissed-count">' . absint( $base->notifications->get_dismissed_count() ) . '</span>'
					);
					?>
				</h3>
				<a href="#" class="soliloquy-button-text" id="soliloquy-notifications-show-dismissed">
					<?php esc_html_e( 'Dismissed Notifications', 'soliloquy' ); ?>
				</a>
				<a href="#" class="soliloquy-button-text" id="soliloquy-notifications-show-active">
					<?php esc_html_e( 'Active Notifications', 'soliloquy' ); ?>
				</a>
				<a class="soliloquy-just-icon-button soliloquy-notifications-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path d="M18.984 6.422L13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z"></path>
					</svg>
				</a>
			</div>
			<div class="soliloquy-notifications-list">
				<ul class="soliloquy-notifications-active">
					<?php
					$notifications = $base->notifications->get_active_notifications();
					foreach ( $notifications as $notification ) {
						$base->notifications->get_notification_markup( $notification );
					}
					?>
				</ul>
				<ul class="soliloquy-notifications-dismissed">
					<?php
					$notifications = $base->notifications->get_dismissed_notifications();
					foreach ( $notifications as $notification ) {
						$base->notifications->get_notification_markup( $notification );
					}
					?>
				</ul>
			</div>
			<div class="soliloquy-notifications-footer">
				<a href="#" class="soliloquy-button-text soliloquy-notification-dismiss" id="soliloquy-dismiss-all" data-id="all"><?php esc_html_e( 'Dismiss all', 'soliloquy' ); ?></a>
			</div>
		</div>
