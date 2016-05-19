
	<h3><?php _e( 'Manage iCalendar Settings', 'bp-event-organiser' ); ?></h3>

	<span class="dashicons dashicons-calendar-alt"></span> <strong><?php _e( 'Private URL:', 'bp-event-organiser' ); ?></strong>

	<input type="text" value="<?php bpeo_the_group_private_ical_url(); ?>" readonly="readonly" onclick="this.select()" style="width:100%;" />

	<p><?php _e( "This is the private iCalendar link for your group calendar. Do not share this address with others unless you want them to see all events from your group's calendar.", 'bp-event-organiser' ); ?></p>

	<p><?php _e( 'You can copy and paste this link into any calendar application that supports the iCalendar format.', 'bp-event-organiser' ); ?></p>

	<p><?php _e( 'You can also reset your private iCalendar link below:', 'bp-event-organiser' ); ?></p>

	<?php
		bp_button( array(
			'id' => 'group-reset-private-ical',
			'component' => 'groups',
			'block_self' => false,
			'link_href' => wp_nonce_url( trailingslashit( bp_get_group_permalink( groups_get_current_group() ) . 'admin/' . bpeo_get_events_slug() ), 'bpeo_group_reset_private_ical', 'bpeo-reset' ),
			'link_class' => 'confirm',
			'link_text' => __( 'Reset private URL', 'bp-event-organiser' ),
			'link_title' => __( 'This will invalidate the existing private iCalendar link and generate a new link.', 'bp-event-organiser' )
		) );
	?>

	<?php /* Temp */ ?>
	<input type="submit" style="display:none;" />