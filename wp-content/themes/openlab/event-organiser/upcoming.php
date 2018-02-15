<?php
/**
 * Upcoming event list template.
 */

$eo_get_events_args = array(
	'showpastevents' => false,
);

if ( bp_is_user() ) {
	$eo_get_events_args['bp_displayed_user_id'] = bp_displayed_user_id();
} elseif ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
	$eo_get_events_args['bp_group'] = bp_get_current_group_id();
}

$events = eo_get_events( $eo_get_events_args ); ?>
<h2>Group Events</h2>
<?php if ( ! empty( $events ) ) : ?>
	<ul class="bpeo-upcoming-events">
	<?php
		$_post = $GLOBALS['post'];
		foreach ( $events as $post ) {
			eo_get_template_part( 'content-eo', 'upcoming' );
		}
		$GLOBALS['post'] = $_post;
	?>
	</ul>
<?php else : // ! empty( $events ) ?>
	<p><?php _e( 'No upcoming events found.', 'bp-event-organiser' ) ?></p>
<?php endif; // ! empty( $events )

// iCalendar download
echo '<div id="bpeo-ical-download">';

echo '<h3>' . __( 'Subscribe', 'bp-event-organiser' ) . '</h3>';

if ( bp_is_user() ) {
	echo '<ul>';
	echo '<li><a class="bpeo-ical-link" href="' . bp_displayed_user_domain() . bpeo_get_events_slug() . '/ical/" title="' . __( 'Only public events are listed in this iCalendar. Suitable for sharing.', 'bp-event-organiser' ) . '"><span class="icon"></span>' . __( 'Download iCalendar file (Public)', 'bp-event-organiser' ) . '</a></li>';

	if ( bp_is_my_profile() ) {
		echo '<li><a class="bpeo-ical-link" href="' . bpeo_get_the_user_private_ical_url() . '" title="' . __( 'Both public and private events are listed in this iCalendar.  Be mindful of who you share this with.', 'bp-event-organiser' ) . '"><span class="icon"></span>' . __( 'Download iCalendar file (Private)', 'bp-event-organiser' ) . '</a></li>';
	}
	echo '</ul>';
} elseif ( bp_is_active( 'groups' ) && bp_is_group() ) {
	echo '<ul>';

	if ( 'public' === bp_get_group_status( groups_get_current_group() ) ) {
		echo '<li><a class="bpeo-ical-link" href="' . bpeo_get_group_permalink() . 'ical/"><span class="icon"></span>' . __( 'Download iCalendar file', 'bp-event-organiser' ) . '</a></li>';

	} else {
		echo '<li><a class="bpeo-ical-link" href="' . bpeo_get_the_group_private_ical_url() . '" title="' . __( 'This is a private group.  Be mindful of who you share this calendar with.', 'bp-event-organiser' ) . '"><span class="icon"></span>' . __( 'Download iCalendar file (Private)', 'bp-event-organiser' ) . '</a></li>';
	}
	echo '</ul>';
}

echo '</div>';
