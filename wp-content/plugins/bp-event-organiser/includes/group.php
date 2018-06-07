<?php

/**
 * Group functionality.
 */

/**
 * Register group connection taxonomy.
 *
 * Fires at init:15 to ensure EO has a chance to register its post type first.
 */
function bpeo_register_group_connection_taxonomy() {
	register_taxonomy( 'bpeo_event_group', 'event', array(
		'public' => false,
	) );
}
add_action( 'init', 'bpeo_register_group_connection_taxonomy', 15 );

/**
 * Connect an event to a group.
 *
 * @param int $event_id ID of the event.
 * @param int $group_id ID of the group.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function bpeo_connect_event_to_group( $event_id, $group_id ) {
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	if ( ! $group->id ) {
		return new WP_Error( 'group_not_found', __( 'No group found by that ID.', 'bp-event-organiser' ) );
	}

	$event = get_post( $event_id );
	if ( ! ( $event instanceof WP_Post ) || 'event' !== $event->post_type ) {
		return new WP_Error( 'event_not_found', __( 'No event found by that ID.', 'bp-event-organiser' ) );
	}

	$set = wp_set_object_terms( $event_id, array( 'group_' . $group_id ), 'bpeo_event_group', true );

	if ( is_wp_error( $set ) || empty( $set ) ) {
		return $set;
	} else {
		return true;
	}
}

/**
 * Disconnect an event from a group.
 *
 * @param int $event_id ID of the event.
 * @param int $group_id ID of the group.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
 function bpeo_disconnect_event_from_group( $event_id, $group_id ) {
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	if ( ! $group->id ) {
		return new WP_Error( 'group_not_found', __( 'No group found by that ID.', 'bp-event-organiser' ) );
	}

	$event = get_post( $event_id );
	if ( ! ( $event instanceof WP_Post ) || 'event' !== $event->post_type ) {
		return new WP_Error( 'event_not_found', __( 'No event found by that ID.', 'bp-event-organiser' ) );
	}

	$event_groups = bpeo_get_group_events( $group_id );
	if ( ! in_array( $event_id, $event_groups ) ) {
		return new WP_Error( 'event_not_found_for_group', __( 'No event found by that ID connected to this group.', 'bp-event-organiser' ) );
	}

	$removed = wp_remove_object_terms( $event_id, 'group_' . $group_id , 'bpeo_event_group' );

	return $removed;
}

/**
 * Get event IDs associated with a group.
 *
 * @param int   $group_id ID of the group.
 * @param array $args {
 *     Optional query args. All WP_Query args are accepted, along with the following.
 *     @type bool $showpastevents True to show past events, false otherwise. Default: false.
 * }
 * @return array Array of event IDs.
 */
function bpeo_get_group_events( $group_id, $args = array() ) {
	$r = array_merge( array(
		'posts_per_page' => -1,
		'showpastevents' => true,
	), $args );

	$r['fields'] = 'ids';
	$r['post_type'] = 'event';
	$r['post_status'] = 'any';

	$r['tax_query'] = array(
		array(
			'taxonomy' => 'bpeo_event_group',
			'terms' => 'group_' . $group_id,
			'field' => 'name',
		),
	);

	$q = new WP_Query( $r );

	return $q->posts;
}

/**
 * Get group IDs associated with an event.
 *
 * @param int $event_id ID of the event.
 * @return array Array of group IDs.
 */
function bpeo_get_event_groups( $event_id ) {
	$group_terms = wp_get_object_terms( $event_id, 'bpeo_event_group' );
	$group_term_names = wp_list_pluck( $group_terms, 'name' );

	$group_ids = array();
	foreach ( $group_term_names as $group_term_name ) {
		// Trim leading 'group_'.
		$group_ids[] = intval( substr( $group_term_name, 6 ) );
	}

	return $group_ids;
}

/**
 * Modify `WP_Query` requests for the 'bp_group' param.
 *
 * @param WP_Query Query object, passed by reference.
 */
function bpeo_filter_query_for_bp_group( $query ) {
	// Only modify 'event' queries.
	$post_types = $query->get( 'post_type' );
	if ( ! in_array( 'event', (array) $post_types ) ) {
		return;
	}

	$bp_group = $query->get( 'bp_group', null );
	if ( null === $bp_group ) {
		return;
	}

	if ( ! is_array( $bp_group ) ) {
		$group_ids = array( $bp_group );
	} else {
		$group_ids = $bp_group;
	}

	// Empty array will always return no results.
	if ( empty( $group_ids ) ) {
		$query->set( 'post__in', array( 0 ) );
		return;
	}

	// Make sure private events are displayed
	$query->set( 'post_status', array( 'publish', 'private' ) );

	// Convert group IDs to a tax query.
	$tq = $query->get( 'tax_query' );
	if ( '' === $tq ) {
		$tq = array();
	}

	$group_terms = array();
	foreach ( $group_ids as $group_id ) {
		$group_terms[] = 'group_' . $group_id;
	}

	$tq[] = array(
		'taxonomy' => 'bpeo_event_group',
		'terms' => $group_terms,
		'field' => 'name',
		'operator' => 'IN',
	);

	$query->set( 'tax_query', $tq );
}
add_action( 'pre_get_posts', 'bpeo_filter_query_for_bp_group' );

/**
 * Modify the calendar query to include the current group ID.
 *
 * @param  array $query Query vars as set up by EO.
 * @return array
 */
function bpeo_filter_calendar_query_for_bp_group( $query ) {
	if ( ! bp_is_group() ) {
		return $query;
	}

	$query['bp_group'] = bp_get_current_group_id();

	return $query;
}
add_filter( 'eventorganiser_fullcalendar_query', 'bpeo_filter_calendar_query_for_bp_group' );

/**
 * Filter event links on a group events page to use the group event permalink.
 *
 * @param string $retval Current event permalink
 * @return string
 */
function bpeo_calendar_filter_event_link_for_bp_group( $retval ) {
	if ( ! bp_is_group() ) {
		return $retval;
	}

	// this is to avoid requerying the event just for the post slug
	$event_url = explode( '/', untrailingslashit( $retval ) );
	$post_slug = array_pop( $event_url );

	// regenerate the post URL to account for group permalink
	return trailingslashit( bpeo_get_group_permalink() . $post_slug );
}
add_filter( 'eventorganiser_calendar_event_link', 'bpeo_calendar_filter_event_link_for_bp_group' );

/**
 * Add group information to calendar event markup.
 *
 * @param array $event         Array of data about the event.
 * @param int   $event_id      ID of the event.
 * @param int   $occurrence_id ID of the occurrence.
 * @return array
 */
function bpeo_add_group_info_to_calendar_event( $event, $event_id, $occurrence_id ) {
	foreach ( bpeo_get_event_groups( $event_id ) as $group_id ) {
		$event['className'][] = 'eo-event-bp-group-' . intval( $group_id );

		if ( ! isset( $event['groups'] ) ) {
			$event['groups'] = array();
		}

		if ( ! isset( $event['groups'][ $group_id ] ) ) {
			$group = groups_get_group( array( 'group_id' => $group_id ) );
			$event['groups'][ $group_id ] = array(
				'name' => $group->name,
				'url' => bp_get_group_permalink( $group ),
				'id' => $group_id,
				'color' => bpeo_get_item_calendar_color( $group_id, 'group' ),
			);
		}
	}

	return $event;
}
add_filter( 'eventorganiser_fullcalendar_event', 'bpeo_add_group_info_to_calendar_event', 10, 3 );

/**
 * Unhook BP's rel=canonical and replace with our custom version.
 */
function bpeo_rel_canonical_for_group() {
	if ( ! bp_is_group() ) {
		return;
	}

	if ( ! bp_is_current_action( bpeo_get_events_slug() ) ) {
		return;
	}

	if ( ! $event_slug = bp_action_variable( 0 ) ) {
		return;
	}

	if ( ! $e = get_page_by_path( $event_slug, OBJECT, 'event' ) ) {
		return;
	}

	// Don't let BP output its own canonical tag.
	remove_action( 'bp_head', 'bp_rel_canonical' );

	$canonical_url = get_permalink( $e );
	echo "<link rel='canonical' href='" . esc_url( $canonical_url ) . "' />\n";
}
add_action( 'wp_head', 'bpeo_rel_canonical_for_group', 9 );

/**
 * Modify EO capabilities for group membership.
 *
 * @param array  $caps    Capability array.
 * @param string $cap     Capability to check.
 * @param int    $user_id ID of the user being checked.
 * @param array  $args    Miscellaneous args.
 * @return array Caps whitelist.
 */
function bpeo_group_event_meta_cap( $caps, $cap, $user_id, $args ) {
	// @todo Need real caching in BP for group memberships.
	if ( false === strpos( $cap, '_event' ) ) {
		return $caps;
	}

	// Some caps do not expect a specific event to be passed to the filter.
	$primitive_caps = array( 'read_events', 'read_group_events', 'read_private_events', 'edit_events', 'edit_others_events', 'publish_events', 'delete_events', 'delete_others_events', 'manage_event_categories' );
	if ( ! in_array( $cap, $primitive_caps ) ) {
		$event = get_post( $args[0] );
		if ( 'event' !== $event->post_type ) {
			return $caps;
		}

		$event_groups = bpeo_get_event_groups( $event->ID );
		if ( empty( $event_groups ) ) {
			return $caps;
		}

		$user_groups = groups_get_user_groups( $user_id );
	}

	switch ( $cap ) {
		case 'read_group_events' :
			// If on a group page, use already-queried data.
			if ( groups_get_current_group() && $args[0] == bp_get_current_group_id() ) {
				$group = groups_get_current_group();
			} else {
				$group = groups_get_group( array( 'group_id' => $args[0], 'populate_extras' => false ) );
			}

			// Public groups are open.
			if ( 'public' === bp_get_group_status( $group  ) ) {
				$caps = array( 'exist' );

			// Private and hidden groups require checking member access.
			} else {
				$can_access = false;

				// If on a group page, use already-queried data.
				if ( bp_is_group() && $args[0] == bp_get_current_group_id() && isset( buddypress()->groups->current_group->is_user_member ) ) {
					$can_access = buddypress()->groups->current_group->is_user_member;

				} elseif ( groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
					$can_access = true;
				}

				if ( is_super_admin() || $can_access ) {
					$caps = array( 'read' );
				}
			}

			break;

		/*
		 * Give group members access to private events in their private groups. Ugh.
		 */
		case 'read_private_events' :
			if ( ! bp_is_group() ) {
				return $caps;
			}

			$can_access = false;
			$group = groups_get_current_group();

			if ( isset( buddypress()->groups->current_group->is_user_member ) ) {
				$can_access = buddypress()->groups->current_group->is_user_member;

			} elseif ( groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
				$can_access = true;
			}

			if ( is_super_admin() || $can_access ) {
				$caps = array( 'read' );
			}

			break;

		case 'read_event' :
			// we've already parsed this logic in bpeo_map_basic_meta_caps()
			if ( 'exist' === $caps[0] ) {
				return $caps;
			}

			if ( 'private' !== $event->post_status ) {
				// EO uses 'read', which doesn't include non-logged-in users.
				$caps = array( 'exist' );

			} elseif ( array_intersect( $user_groups['groups'], $event_groups ) ) {
				$caps = array( 'read' );
			}

		// @todo group admins / mods permissions
		case 'edit_event' :
			break;
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'bpeo_group_event_meta_cap', 20, 4 );

/**
 * Register activity actions and format callbacks for 'groups' component.
 */
function bpeo_register_activity_actions_for_groups() {
	bp_activity_set_action(
		buddypress()->groups->id,
		'bpeo_create_event',
		__( 'Events created', 'bp-event-organiser' ),
		'bpeo_activity_action_format',
		__( 'Events created', 'buddypress' ),
		array( 'activity', 'member', 'group', 'member_groups' )
	);

	bp_activity_set_action(
		buddypress()->groups->id,
		'bpeo_edit_event',
		__( 'Events edited', 'bp-event-organiser' ),
		'bpeo_activity_action_format',
		__( 'Events edited', 'buddypress' ),
		array( 'activity', 'member', 'group', 'member_groups' )
	);

	bp_activity_set_action(
		buddypress()->groups->id,
		'bpeo_delete_event',
		__( 'Events deleted', 'bp-event-organiser' ),
		'bpeo_activity_action_format',
		__( 'Events deleted', 'buddypress' ),
		array( 'activity', 'member', 'group', 'member_groups' )
	);
}
add_action( 'bp_register_activity_actions', 'bpeo_register_activity_actions_for_groups' );

/**
 * Create activity items for connected groups.
 *
 * @param array   $activity_args Arguments used to create the 'events' activity item.
 * @param WP_Post $event         Event post object.
 */
function bpeo_create_group_activity_items( $activity_args, $event ) {
	$group_ids = bpeo_get_event_groups( $event->ID );

	foreach ( $group_ids as $group_id ) {
		$_activity_args = $activity_args;
		$_activity_args['component'] = buddypress()->groups->id;
		$_activity_args['item_id'] = $group_id;
		$_activity_args['hide_sitewide'] = true;
		bp_activity_add( $_activity_args );
	}
}
add_action( 'bpeo_create_event_activity', 'bpeo_create_group_activity_items', 10, 2 );

/**
 * Format activity items related to groups.
 *
 * @param string $action
 * @param object $activity
 * @return string
 */
function bpeo_activity_action_format_for_groups( $action, $activity ) {
	global $_bpeo_recursing_activity;

	if ( ! empty( $_bpeo_recursing_activity ) ) {
		return $action;
	}

	$groups = bpeo_get_event_groups( $activity->secondary_item_id );

	if ( empty( $groups ) ) {
		return $action;
	}

	$_groups = groups_get_groups( array(
		'include' => $groups,
		'populate_extras' => false,
		'per_page' => false,
		'type' => 'alphabetical',
		'show_hidden' => true,
	) );
	$groups = $_groups['groups'];

	// Remove groups the current user doesn't have access to.
	foreach ( $groups as $group_index => $group ) {
		if ( 'public' === $group->status ) {
			continue;
		}

		if ( ! is_user_logged_in() || ! groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
			unset( $groups[ $group_index ] );
			continue;
		}
	}

	$groups = array_values( array_filter( $groups ) );
	if ( empty( $groups ) ) {
		return $action;
	}

	$event = get_post( $activity->secondary_item_id );
	$user_url = bp_core_get_user_domain( $activity->user_id );
	$user_name = bp_core_get_user_displayname( $activity->user_id );

	// The URL should correspond to the current group, or the first group that the user is a member of.
	$event_url = get_permalink( $event );
	if ( bp_is_group() ) {
		$event_url = bpeo_get_group_permalink( groups_get_current_group() ) . $event->post_name;
	} else {
		foreach ( $groups as $group ) {
			if ( groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
				$event_url = bpeo_get_group_permalink( $group ) . $event->post_name;
				break;
			}
		}
	}

	$event_url = trailingslashit( $event_url );

	$event_name = $event->post_title;

	$group_count = count( $groups );
	switch ( $activity->type ) {
		case 'bpeo_create_event' :
			/* translators: 1: link to user, 2: link to event, 3: comma-separated list of group links */
			$base = _n( '%1$s created the event %2$s in the group %3$s.', '%1$s created the event %2$s in the groups %3$s.', $group_count, 'bp-event-organiser' );
			$event_text = sprintf( '<a href="%s">%s</a>', esc_url( $event_url ), esc_html( $event_name ) );
			break;
		case 'bpeo_edit_event' :
			/* translators: 1: link to user, 2: link to event, 3: comma-separated list of group links */
			$base = _n( '%1$s edited the event %2$s in the group %3$s.', '%1$s edited the event %2$s in the groups %3$s.', $group_count, 'bp-event-organiser' );
			$event_text = sprintf( '<a href="%s">%s</a>', esc_url( $event_url ), esc_html( $event_name ) );
			break;
		case 'bpeo_delete_event' :
			/* translators: 1: link to user, 2: link to event, 3: comma-separated list of group links */
			$base = _n( '%1$s deleted the event %2$s in the group %3$s.', '%1$s deleted the event %2$s in the groups %3$s.', $group_count, 'bp-event-organiser' );
			$event_text = esc_html( $event_name );
			break;
	}

	// If this is a user activity item, keeps groups in alphabetical order. Otherwise put primary group first.
	if ( buddypress()->groups->id === $activity->component ) {
		foreach ( $groups as $group_index => $group ) {
			if ( $activity->item_id == $group->id ) {
				$this_group = $group;
				unset( $groups[ $group_index ] );
				array_unshift( $groups, $this_group );
			}
		}
	}

	$groups = array_values( array_filter( $groups ) );

	$group_links = array();
	foreach ( $groups as $group ) {
		$group_links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( trailingslashit( bp_get_group_permalink( $group ) . bpeo_get_events_slug() ) ),
			esc_html( $group->name )
		);
	}

	$event = get_post( $activity->secondary_item_id );

	$action = sprintf(
		$base,
		sprintf( '<a href="%s">%s</a>', esc_url( $user_url ), esc_html( $user_name ) ),
		$event_text,
		implode( ', ', $group_links )
	);

	return $action;
}
add_filter( 'bpeo_activity_action', 'bpeo_activity_action_format_for_groups', 10, 2 );

/**
 * Disable secondary avatars for event-related activity.
 */
function bpeo_disable_secondary_avatars( $avatar ) {
	global $activities_template;

	$a = $activities_template->activity;

	if ( buddypress()->groups->id !== $a->component ) {
		return $avatar;
	}

	if ( 'bpeo_' !== substr( $a->type, 0, 5 ) ) {
		return $avatar;
	}

	return '';
}
add_filter( 'bp_get_activity_secondary_avatar', 'bpeo_disable_secondary_avatars' );

/** TEMPLATE ************************************************************/

/**
 * Get the permalink to a group's events page.
 *
 * @param  BP_Groups_Group|int $group The group object or the group ID to fetch the group for.
 * @return string
 */
function bpeo_get_group_permalink( $group = 0 ) {
	if ( empty( $group ) ) {
		$group = groups_get_current_group();
	}

	if ( ! empty( $group ) && ! $group instanceof BP_Groups_Group && is_int( $group ) ) {
		$group = groups_get_group( array(
			'group_id'        => $group,
			'populate_extras' => false
		) );
	}

	return trailingslashit( bp_get_group_permalink( $group ) . bpeo_get_events_slug() );
}

/**
 * Display a list of connected groups on single event pages.
 */
function bpeo_list_connected_groups() {
	$event_group_ids = bpeo_get_event_groups( get_the_ID() );

	if ( empty( $event_group_ids ) ) {
		return;
	}

	$event_groups = groups_get_groups( array(
		'include' => $event_group_ids,
		'show_hidden' => true, // We roll our own.
	) );

	$markup = array();
	foreach ( $event_groups['groups'] as $eg ) {
		// Remove groups that the current user should not have access to.
		if ( 'public' !== $eg->status && ! current_user_can( 'bp_moderate' ) && ! groups_is_user_member( bp_current_user_id(), $eg->id ) ) {
			continue;
		}

		$markup[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( bpeo_get_group_permalink( $eg ) ),
			esc_html( stripslashes( $eg->name ) )
		);
	}

	if ( empty( $markup ) ) {
		return;
	}

	$count = count( $markup );
	$base = _n( '<strong>Connected group:</strong> %s', '<strong>Connected groups:</strong> %s', $count, 'bp-event-organiser' );

	echo sprintf( '<li>' . wp_filter_kses( $base ) . '</li>', implode( ', ', $markup ) );
}
add_action( 'eventorganiser_additional_event_meta', 'bpeo_list_connected_groups' );

/**
 * Render the "Silent" checkbox during event creation/editing.
 */
function bpeo_render_silent_checkbox( $post_type, $location, $post ) {
	// Only do this on the front end.
	if ( is_admin() ) {
		return;
	}

	// Only do this for events.
	if ( 'event' !== $post_type ) {
		return;
	}

	// Only do this for 'side'.
	if ( 'side' !== $location ) {
		return;
	}

	// Create vs Edit.
	if ( ! $post->ID || 'auto-draft' === $post->post_status ) {
		$title = __( 'Silent Create', 'bp-event-organiser' );
	} else {
		$title = __( 'Silent Edit', 'bp-event-organiser' );
	}

	?>
	<p id="bpeo-silent-wrapper" style="display:none">
		<label for="bp_event_organiser_silent"><input type="checkbox" value="silent" id="bp_event_organiser_silent" name="bpeo-silent"></input> <strong><?php echo esc_html( $title ); ?></strong> <?php esc_html_e( '(notifications will not be sent to subscribed group members)', 'bp-event-organiser' ); ?>
	</p>
	<?php
}
add_action( 'do_meta_boxes', 'bpeo_render_silent_checkbox', 10, 3 );

/** Embed ********************************************************************/

/**
 * Loads our group events oEmbed component.
 */
function bpeo_group_setup_oembed() {
	if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) && function_exists( 'bp_rest_api_init' ) && true === apply_filters( 'bpeo_groups_enable_oembed', true ) ) {
		$GLOBALS['buddypress_event_organiser']->group_oembed = new BPEO_Group_oEmbed_Extension;
	}
}
add_action( 'bp_loaded', 'bpeo_group_setup_oembed' );

/**
 * Should we load our override template for embedding group events?
 *
 * We only override if we're on a group events page and if '?embedded=true' is
 * to the URL:
 * - example.com/groups/GROUP/events/?embedded=true - Will show group calendar
 * - example.com/groups/GROUP/events/upcoming/?embedded=true - Will show a list
 *   of upcoming events
 *
 * This is done so we can remove the header and footer so embedding can be
 * done via an IFRAME.
 *
 * If true, we'll also remove a bunch of unnecessary content to avoid resource
 * bloat.
 *
 * @return bool
 */
function bpeo_groups_event_embed_override_template() {
	if ( false === bp_is_current_action( 'events' ) ) {
		return false;
	}

	if ( empty( $_GET['embedded'] ) ) {
		return false;
	}

	// Main event calendar page
	if ( false === bp_action_variables() ) {
		$embed = true;

	// Upcoming
	} elseif ( bp_is_action_variable( 'upcoming' ) ) {
		$embed = true;
	}

	if ( false === $embed ) {
		return false;
	}

	// Temporary marker
	buddypress()->bpeo_events_embed = true;

	// Register our template stack
	bp_register_template_stack( 'bpeo_register_template_stack', 13 );

	// Remove all actions from 'wp_head'.
	remove_all_actions( 'wp_head' );
	remove_all_actions( 'bp_head' );

	// Add back some head hooks.
	add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
	add_action( 'wp_head', 'wp_print_styles', 8 );

	// Remove all assets
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	add_action( 'wp_enqueue_scripts', 'bpeo_group_events_embed_remove_all_assets', 999 );

	// Remove all footer hooks
	remove_all_actions( 'wp_footer' );

	// And add back required EO footer hooks
	add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
	add_action( 'wp_footer', array( 'EventOrganiser_Shortcodes', 'print_script' ) );

	return true;
}

/**
 * Use a different template when embedding group events.
 *
 * This is for BP theme compatibility.
 *
 * @param  array $retval Current templates.
 * @return array
 */
function bpeo_group_events_embed_template_hierarchy( $retval = array() ) {
	if ( false === bpeo_groups_event_embed_override_template() ) {
		return $retval;
	}

	// Use our special template when embedding group events
	return array( 'groups/single/index-action-events-embed.php' );
}
add_filter( 'bp_template_hierarchy_groups_single_item', 'bpeo_group_events_embed_template_hierarchy' );

/**
 * Use a different template when embedding group events for bp-default themes.
 *
 * Basically the same as {@link bpeo_group_events_embed_template_hierarchy()},
 * but for bp-default.  Yay for backward compatibility!
 *
 * @param  string $retval Template
 * @return string
 */
function bpeo_group_events_embed_template_for_bpdefault( $retval = '' ) {
	if ( true === bp_detect_theme_compat_with_current_theme() ) {
		return $retval;
	}

	if ( false === bpeo_groups_event_embed_override_template() ) {
		return $retval;
	}

	// For bp-default, we do not allow overriding in child themes. Meh!
	return bpeo_register_template_stack() . 'buddypress/groups/single/index-action-events-embed.php';
}
add_filter( 'bp_located_template', 'bpeo_group_events_embed_template_for_bpdefault', 0 );

/**
 * Remove all styles and scripts when embedding a group calendar.
 *
 * This is so we can remove any unnecessary network requests.
 */
function bpeo_group_events_embed_remove_all_assets() {
	if ( empty( buddypress()->bpeo_events_embed ) ) {
		return;
	}

	$styles = wp_styles();
	$styles->queue = array();

	$scripts = wp_scripts();
	$scripts->queue = array();

	wp_enqueue_style( 'dashicons' );
}

/** iCal *********************************************************************/

/**
 * Get the private iCalendar hash for a group.
 *
 * If hash doesn't exist, we generate it and save it for the group. You can
 * generate a new hash by setting $reset to true.
 *
 * @param  int  $group_id The group ID
 * @param  bool $reset    Resets the private hash for the group. Default: false.
 * @return string|bool
 */
function bpeo_get_the_group_private_ical_hash( $group_id = 0, $reset = false ) {
	if ( empty( $group_id ) ) {
		$group_id = bp_get_current_group_id();
	}

	if ( empty( $group_id ) ) {
		return false;
	}

	if ( false === $reset ) {
		$hash = groups_get_groupmeta( $group_id, 'bpeo_private_ical_hash' );
	} else {
		$hash = '';
	}

	if ( empty( $hash ) ) {
		$hash = md5( uniqid( '' ) );
		groups_update_groupmeta( $group_id, 'bpeo_private_ical_hash', $hash );
	}

	return $hash;
}

/**
 * Output the private iCalendar URL for a group.
 *
 * @param int $group_id The group ID
 */
function bpeo_the_group_private_ical_url( $group_id = 0 ) {
	echo bpeo_get_the_group_private_ical_url( $group_id = 0 );
}
	/**
	 * Get the private iCalendar URL for a group.
	 *
	 * @param  int $group_id The group ID
	 * @return string|bool
	 */
	function bpeo_get_the_group_private_ical_url( $group_id = 0 ) {
		if ( empty( $group_id ) ) {
			$group_id = bp_get_current_group_id();
		}

		if ( empty( $group_id ) ) {
			return false;
		}

		if ( $group_id == bp_get_current_group_id() ) {
			$group = groups_get_current_group();

		} else {
			$group = groups_get_group( array(
				'group_id'        => $group_id,
				'populate_extras' => false
			) );
		}

		return trailingslashit( esc_url( bpeo_get_group_permalink( $group ) . bpeo_get_the_group_private_ical_hash( $group_id ) . '/ical' ) );
	}

/**
 * Allow iCal feeds to be populated for group members.
 *
 * By default, WordPress limits private posts to the author of the post. This
 * doesn't work for us as group members should be able to view the post item
 * in iCal feeds.
 *
 * This function modifies the WP query to check if the event is connected to
 * any groups and if the logged-in user is a member of those groups. If so, we
 * allow the user to view the iCal feed as intended.
 *
 * @param WP_Query $q
 */
function bpeo_ical_modify_query_for_group_permissions( $q ) {
	// make sure we're checking an EO feed
	if ( false === $q->is_feed( 'eo-events' ) ) {
		return;
	}

	// make sure there's a post slug and user is logged in
	if ( empty( $q->query_vars['name'] ) || false === is_user_logged_in() ) {
		return;
	}

	// query for the event by page slug
	$post = get_page_by_path( $q->query_vars['name'], 'OBJECT', 'event' );
	if ( empty( $post ) ) {
		return;
	}

	// check if any groups are attached to this event
	$group_ids = bpeo_get_event_groups( $post->ID );
	if ( empty( $group_ids ) ) {
		return;
	}

	// make sure the logged-in user is a part of the group
	$groups = bp_has_groups( array(
		'user_id' => bp_loggedin_user_id(),
		'include' => $group_ids,
		'populate_extras' => false,
		'update_meta_cache' => false,
		'per_page' => false,
	) );
	if ( empty( $groups ) ) {
		return;
	}

	// make sure logged-in user can see feed
	$q->set( 'post_status', array( 'publish', 'private' ) );

}
add_action( 'pre_get_posts', 'bpeo_ical_modify_query_for_group_permissions', 99 );

/** BuddyPress Group Email Subscription integration **************************/

/**
 * Ensure that GES does not send multiple emails to a given user for a given event.
 *
 * @since 1.0.0
 *
 * @param bool   $send_it  Whether to send to the given user.
 * @param object $activity Activity object.
 * @param int    $user_id  User ID.
 * @return bool
 */
function bpeo_send_bpges_notification_for_user( $send_it, $activity, $user_id ) {
	global $_bpeo_bpges_sent;

	if ( 'groups' !== $activity->component || 0 !== strpos( $activity->type, 'bpeo_' ) ) {
		return $send_it;
	}

	if ( isset( $_POST['bpeo-silent'] ) ) {
		$send_it = false;
		return $send_it;
	}

	if ( ! isset( $_bpeo_bpges_sent ) ) {
		$_bpeo_bpges_sent = array();
	}

	if ( ! isset( $_bpeo_bpges_sent[ $user_id ] ) ) {
		$_bpeo_bpges_sent[ $user_id ] = array();
	}

	foreach ( $_bpeo_bpges_sent[ $user_id ] as $a ) {
		// A duplicate is one with the same type + secondary_item_id + date_recorded.
		if ( $a->type === $activity->type && $a->secondary_item_id === $activity->secondary_item_id && $a->date_recorded === $activity->date_recorded ) {
			$send_it = false;
			break;
		}
	}

	$_bpeo_bpges_sent[ $user_id ][] = $activity;

	return $send_it;
}
add_filter( 'bp_ass_send_activity_notification_for_user', 'bpeo_send_bpges_notification_for_user', 10, 3 );

/**
 * Append iCal link to BP Group Email notifications.
 *
 * Requires the BP Group Email Subscription plugin.
 *
 * @param  string $content  Email content
 * @param  object $activity Activity object
 * @return string
 */
function bpeo_ges_add_ical_link( $content, $activity ) {
	// not a BPEO item, so bail!
	if ( 0 !== strpos( $activity->type, 'bpeo_' ) ) {
		return $content;
	}

	$ical_link = __( 'Download iCalendar file:', 'bp-event-organiser' );
	$ical_link .= "\n";

	if ( ! empty( $activity->hide_sidewide ) ) {
		$ical_link .= ass_get_login_redirect_url( bpeo_get_the_ical_link( $activity->secondary_item_id ), 'bpeo_event' );
	} else {
		$ical_link .= bpeo_get_the_ical_link( $activity->secondary_item_id );
	}

	return $content . $ical_link;
}
add_filter( 'bp_ass_activity_notification_content', 'bpeo_ges_add_ical_link', 20, 2 );
