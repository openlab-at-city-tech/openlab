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
<?php endif; // ! empty( $events ) ?>
