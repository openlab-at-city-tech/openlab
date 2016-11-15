
	<h3><?php _e( 'Manage iCalendar Settings', 'bp-event-organiser' ); ?></h3>

	<span class="dashicons dashicons-calendar-alt"></span> <strong><?php _e( 'Private URL:', 'bp-event-organiser' ); ?></strong>

	<input type="text" value="<?php bpeo_the_user_private_ical_url(); ?>" readonly="readonly" onclick="this.select()" style="width:100%;" />

	<p><?php _e( "This is the private iCalendar link for your calendar. Do not share this address with others unless you want them to see all events from your calendar.", 'bp-event-organiser' ); ?></p>

	<p><?php _e( 'You can copy and paste this link into any calendar application that supports the iCalendar format.', 'bp-event-organiser' ); ?></p>

	<p><?php _e( 'You can also reset your private iCalendar link below:', 'bp-event-organiser' ); ?></p>

	<?php
		bp_button( array(
			'id' => 'user-reset-private-ical',
			'component' => 'members',
			'block_self' => false,
			'link_href' => wp_nonce_url( bp_displayed_user_domain() . bpeo_get_events_slug() . '/manage/', 'bpeo_user_reset_private_ical', 'bpeo-reset' ),
			'link_class' => 'confirm',
			'link_text' => __( 'Reset private URL', 'bp-event-organiser' ),
			'link_title' => __( 'This will invalidate the existing private iCalendar link and generate a new link.', 'bp-event-organiser' )
		) );
	?>