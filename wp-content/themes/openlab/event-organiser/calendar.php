<?php

$cat = ! empty( $_GET['cat'] ) ? esc_attr( $_GET['cat'] ) : '';
$tag = ! empty( $_GET['tag'] ) ? esc_attr( $_GET['tag'] ) : '';

$args = array(
	'headerright' => 'prev,today,next,month,agendaWeek',
);

if ( ! empty( $cat ) ) {
	$args['event-category'] = $cat;
}

if ( ! empty( $tag ) ) {
	$args['event-tag'] = $tag;
}

if ( bp_is_user() ) {
	$args['bp_displayed_user_id'] = bp_displayed_user_id();
} elseif ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
	$args['bp_group'] = bp_get_current_group_id();
}

echo eo_get_event_fullcalendar( $args );

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