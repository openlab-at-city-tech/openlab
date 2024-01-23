<div id="message" class="info">

	<?php if ( bp_is_current_action( 'unread' ) ) : ?>

		<?php if ( bp_is_my_profile() ) : ?>

			<p><?php _e( 'You have no unread notifications.', 'bp-classic' ); ?></p>

		<?php else : ?>

			<p><?php _e( 'This member has no unread notifications.', 'bp-classic' ); ?></p>

		<?php endif; ?>

	<?php else : ?>

		<?php if ( bp_is_my_profile() ) : ?>

			<p><?php _e( 'You have no notifications.', 'bp-classic' ); ?></p>

		<?php else : ?>

			<p><?php _e( 'This member has no notifications.', 'bp-classic' ); ?></p>

		<?php endif; ?>

	<?php endif; ?>

</div>
