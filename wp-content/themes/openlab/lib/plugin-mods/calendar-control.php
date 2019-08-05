<?php

/**
 * Calendar control
 * Hooks into Events Organiser and BuddyPress Event Organiser
 */

/**
 * Checks whether Calendar is enabled for a group.
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_is_calendar_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to true in case no value is found.
	if ( ! $group_id ) {
		return true;
	}

	// Default to true in case no setting is found, except for portfolios.
	$is_disabled = groups_get_groupmeta( $group_id, 'calendar_is_disabled', true );
	if ( '' === $is_disabled && openlab_is_portfolio( $group_id ) ) {
		$is_disabled = 1;
	}

	return ! $is_disabled;
}

/**
 * Disable Calendar subnav if not enabled for group.
 */
add_action(
	'bp_screens',
	function() {
		if ( ! bp_is_group() ) {
			return;
		}

		if ( openlab_is_calendar_enabled_for_group() ) {
			return;
		}

		bp_core_remove_subnav_item( groups_get_current_group()->slug, 'events', 'groups' );
		bp_core_remove_subnav_item( groups_get_current_group()->slug, 'events-mobile', 'groups' );
	},
	9
);

/**
 * Google maps API now requires a key
 */
function openlab_custom_calendar_assets() {

	$key = 'AIzaSyDQrCvCLzpXoahl68dVJmfBxemu36CUsTM';

	wp_deregister_script( 'eo_GoogleMap' );
	wp_register_script( 'eo_GoogleMap', '//maps.googleapis.com/maps/api/js?key=' . $key . '&sensor=false&language=' . substr( get_locale(), 0, 2 ) );
}
add_action( 'wp_enqueue_scripts', 'openlab_custom_calendar_assets', 999 );
add_action( 'admin_enqueue_scripts', 'openlab_custom_calendar_assets', 999 );

/**
 * Right now there doesn't seem to be a good way to delineate the event detail screen from the other actions
 * @return boolean
 */
function openlab_eo_is_event_detail_screen() {

	if ( ! function_exists( 'bpeo_is_action' ) ) {
		return false;
	}

	if ( ! empty( buddypress()->action_variables ) && ! bp_is_action_variable( 'ical' ) && ! bp_is_action_variable( 'upcoming', 0 ) && ! bpeo_is_action( 'new' ) && ! bpeo_is_action( 'edit' ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Retrieving the event detail obj outside of the EO's loop
 * This will, in most cases, be used in instances where the object has to exist
 * EO and BP EO already have checks in place to handle non-existing event detail pages
 * @return type
 */
function openlab_eo_get_single_event_query_obj() {
	$obj_out = array();

	// Set up query args
	$query_args                     = array();
	$query_args['suppress_filters'] = true;
	$query_args['orderby']          = 'none';
	$query_args['post_status']      = array( 'publish', 'pending', 'private', 'draft', 'future', 'trash' );

	// this is a draft with no slug
	if ( false !== strpos( bp_current_action(), 'draft-' ) ) {
		$query_args['post__in'] = (array) str_replace( 'draft-', '', bp_action_variable() );

		// use post slug
	} else {
		$query_args['name'] = bp_action_variable();
	}

	// query for the event
	$event = eo_get_events( $query_args );

	$obj_out = $event[0];

	return $obj_out;
}

/**
 * Custom control over what events provide for editing
 * @param array $args
 * @return string
 */
function openlab_control_event_post_type( $args ) {
	$args['supports'] = array( 'title', 'editor', 'author', 'excerpt', 'custom-fields' );
	return $args;
}
add_filter( 'eventorganiser_event_properties', 'openlab_control_event_post_type' );

/* * *
 * Preventing the creation of dedicated venue pages
 */

function openlab_control_venue_taxonomy( $event_category_args ) {
	$event_category_args['rewrite'] = false;
	return $event_category_args;
}
add_filter( 'eventorganiser_register_taxonomy_event-venue', 'openlab_control_venue_taxonomy' );

/**
 * Modifying links for sitewide calendar
 * Making sure back button goes back to sitewide calendar
 * Taking out actions links (there's some complicated issues there)
 * @global type $post
 * @param type $links
 * @return type
 */
function openlab_control_event_action_links( $links ) {
	global $post;

	if ( 'event' === $post->post_type && ! bp_current_action() ) {
		$links         = array();
		$back_link     = get_permalink( get_page_by_path( 'about/calendar' ) );
		$links['back'] = "<a href='$back_link'>← Back</a>";
	}

	return $links;
}
add_filter( 'bpeo_get_the_single_event_action_links', 'openlab_control_event_action_links' );

/**
 * Pointing to custom templates in OpenLab theme folder
 * @param type $stack
 * @return type
 */
function openlab_add_eventorganiser_custom_template_folder( $stack ) {

	$custom_loc = get_stylesheet_directory() . '/event-organiser';

	array_unshift( $stack, $custom_loc );

	return $stack;
}
add_filter( 'eventorganiser_template_stack', 'openlab_add_eventorganiser_custom_template_folder' );

/**
 * Redirects to control calendar page access
 * @param type $wp
 * @return type
 */
function openlab_event_page_controller( $wp ) {
	$redirect_url = '';

	/**
	 * For now there are no events pages for members
	 * Attempting to go to an events page will redirect to the member's profile page
	 */
	if ( strpos( $wp->request, '/events' ) !== false && strpos( $wp->request, 'members/' ) !== false ) {

		$request_url  = $wp->request;
		$redirect_url = explode( '/events', $request_url );

		if ( is_array( $redirect_url ) ) {
			wp_safe_redirect( get_site_url() . '/' . $redirect_url[0] );
			exit;
		} else {
			wp_safe_redirect( get_site_url() );
			exit;
		}
	}

	/**
	 * Also controls access to new events interface - if a member is a non-admin and non-mod
	 * and the group calendar settings are set to only allow admins and mods the ability to
	 * create new events, then the member will be redirected
	 */
	if ( strpos( $wp->request, '/events/' ) !== false && strpos( $wp->request, '/new-event' ) !== false ) {

		$event_create_access = groups_get_groupmeta( bp_get_current_group_id(), 'openlab_bpeo_event_create_access' );

		if ( 'admin' === $event_create_access && ! bp_is_item_admin() && ! bp_is_item_mod() ) {

			$request_url  = $wp->request;
			$redirect_url = explode( '/new-event', $request_url );

			if ( is_array( $redirect_url ) ) {
				wp_safe_redirect( get_site_url() . '/' . $redirect_url[0] );
				exit;
			} else {
				wp_safe_redirect( get_site_url() );
				exit;
			}
		}
	}

	return $redirect_url;
}
add_filter( 'wp', 'openlab_event_page_controller' );

/**
 * Custom control of Event Organiser options
 * @param array $options
 * @return string
 */
function openlab_eventorganiser_custom_options( $options ) {
	$options['dateformat'] = 'mm-dd';
	return $options;
}
add_filter( 'eventorganiser_options', 'openlab_eventorganiser_custom_options' );

/**
 * Adds a title above the description box when editing an event
 */
function openlab_eventorganiser_custom_content_after_title() {
	if ( ! function_exists( 'bpeo_is_action' ) ) {
		return;
	}

	if ( bpeo_is_action( 'new' ) || bpeo_is_action( 'edit' ) ) {
		echo '<h3 class="outside-title"><span class="font-size font-18">Event Description</span></h3>';
	}
}
add_action( 'edit_form_after_title', 'openlab_eventorganiser_custom_content_after_title' );

function openlab_manage_media_buttons( $editor_id ) {
	if ( bp_is_current_action( 'events' ) && 'editor-content' === $editor_id ) {
		$remove_button = <<<HTML
                <script type="text/javascript">
                jQuery(document).ready(function () {

                                jQuery('#wp-editor-content-media-buttons').remove();

                            });
                </script>
HTML;
		echo $remove_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'media_buttons', 'openlab_manage_media_buttons' );

/**
 * Remove Event Categories
 */
add_filter( 'eventorganiser_register_taxonomy_event-category', '__return_false' );

/**
 * Remove Event Tags
 */
add_filter( 'eventorganiser_register_taxonomy_event-tag', '__return_false' );

/**
 * Remove plugin action for adding author
 */
remove_action( 'eventorganiser_additional_event_meta', 'bpeo_list_author' );

/**
 * Custom markup for author listing on event detail page
 */
function openlab_bpeo_list_author() {
	$event     = get_post( get_the_ID() );
	$author_id = $event->post_author;

	$base = __( '<strong>Author:</strong> %s', 'bp-event-organiser' );

	echo sprintf( '<li>' . esc_html( wp_filter_kses( $base ) ) . '</li>', esc_html( bp_core_get_user_displayname( $author_id ) ) );
}
add_action( 'eventorganiser_additional_event_meta', 'openlab_bpeo_list_author', 5 );

/**
 * For custom Event Organiser meta boxes
 * In some cases we need to add custom content to the Event Organiser meta boxes,
 * and right now this is the only way (hooks are not available for meta box content
 */
function openlab_handlng_eventorganiser_metaboxes() {
	remove_meta_box( 'eventorganiser_detail', 'event', 'normal' );
	add_meta_box( 'eventorganiser_detail', __( 'Event Details', 'eventorganiser' ), '_eventorganiser_details_metabox_openlab_custom', 'event', 'normal', 'high' );

	remove_meta_box( 'authordiv', 'event', 'normal' );
}
add_action( 'add_meta_boxes_event', 'openlab_handlng_eventorganiser_metaboxes', 20 );

/**
 * Custom meta box for Event Details
 * @global type $wp_locale
 */
function _eventorganiser_details_metabox_openlab_custom( $post ) {
	global $wp_locale;

	//Sets the format as php understands it, and textual.
	$php_format = eventorganiser_get_option( 'dateformat' );
	if ( 'd-m-Y' === $php_format ) {
		$format = 'dd &ndash; mm &ndash; yyyy'; //Human form
	} elseif ( 'Y-m-d' === $php_format ) {
		$format = 'yyyy &ndash; mm &ndash; dd'; //Human form
	} else {
		$format = 'mm &ndash; dd &ndash; yyyy'; //Human form
	}

	$is24        = eventorganiser_blog_is_24();
	$time_format = $is24 ? 'H:i' : 'g:ia';

	//Get the starting day of the week
	$start_day = intval( get_option( 'start_of_week' ) );
	$ical_days = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA' );

	//Retrieve event details
	$schedule_arr = eo_get_event_schedule( $post->ID );

	$schedule      = $schedule_arr['schedule'];
	$start         = $schedule_arr['start'];
	$end           = $schedule_arr['end'];
	$all_day       = $schedule_arr['all_day'];
	$frequency     = $schedule_arr['frequency'];
	$schedule_meta = $schedule_arr['schedule_meta'];
	$occurs_by     = $schedule_arr['occurs_by'];
	$until         = $schedule_arr['until'];
	$include       = $schedule_arr['include'];
	$exclude       = $schedule_arr['exclude'];

	$venues   = eo_get_venues();
	$venue_id = (int) eo_get_venue( $post->ID );

	//$sche_once is used to disable date editing unless the user specifically requests it.
	//But a new event might be recurring (via filter), and we don't want to 'lock' new events.
	//See https://wordpress.org/support/topic/wrong-default-in-input-element
	$sche_once = ( 'once' === $schedule || ! empty( get_current_screen()->action ) );

	if ( ! $sche_once ) {
		$notices = sprintf(
			'<label for="eo-event-recurrring-notice">%s</label>',
			__( 'This is a recurring event. Check to edit this event and its recurrences', 'eventorganiser' )
		)
				. ' <input type="checkbox" id="eo-event-recurrring-notice" name="eo_input[AlterRe]" value="yes">';
	} else {
		$notices = '';
	}

	/**
	 * Filters the notice at the top of the event details metabox.
	 *
	 * @param string  $notices The message text.
	 * @param WP_Post $post    The corresponding event (post).
	 */
	$notices = apply_filters( 'eventorganiser_event_metabox_notice', $notices, $post );
	if ( $notices ) {
		//updated class used for backwards compatability see https://core.trac.wordpress.org/ticket/27418
		echo '<div class="notice notice-success updated inline"><p>' . $notices . '</p></div>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	$date_desc = sprintf( __( 'Enter date in %s format', 'eventorganiser' ), $format );
	$time_desc = $is24 ? __( 'Enter time in 24-hour hh colon mm format', 'eventorganiser' ) : __( 'Enter time in 12-hour hh colon mm am or pm format', 'eventorganiser' );

	//variables pulled from meta box markup
	$recurrence_schedules = array(
		'once'    => __( 'none', 'eventorganiser' ),
		'daily'   => __( 'daily', 'eventorganiser' ),
		'weekly'  => __( 'weekly', 'eventorganiser' ),
		'monthly' => __( 'monthly', 'eventorganiser' ),
		'yearly'  => __( 'yearly', 'eventorganiser' ),
		'custom'  => __( 'custom', 'eventorganiser' ),
	);

	if ( ! empty( $include ) ) {
		$include_str = array_map( 'eo_format_datetime', $include, array_fill( 0, count( $include ), 'Y-m-d' ) );
		$include_str = esc_attr( sanitize_text_field( implode( ',', $include_str ) ) );
	} else {
		$include_str = '';
	}

	if ( ! empty( $exclude ) ) {
		$exclude_str = array_map( 'eo_format_datetime', $exclude, array_fill( 0, count( $exclude ), 'Y-m-d' ) );
		$exclude_str = esc_attr( sanitize_text_field( implode( ',', $exclude_str ) ) );
	} else {
		$exclude_str = '';
	}

	if ( taxonomy_exists( 'event-venue' ) ) :
		$address_fields    = _eventorganiser_get_venue_address_fields();
		$address           = array();
		$venue_stored_name = '';

		//check for stored fields when editing
		if ( bp_is_action_variable( 'edit' ) ) {
			if ( $venue_id && $venue_id > 0 ) {
				$venue_obj         = get_term_by( 'id', $venue_id, 'event-venue' );
				$venue_stored_name = $venue_obj->name;
				$address           = eo_get_venue_address( $venue_id );
			}
		}
	endif;

	ob_start();
	include locate_template( 'parts/plugin-mods/calendar-custom-event-meta-box.php' );
	$custom_meta_box = ob_get_clean();

	echo $custom_meta_box; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Save calendar group settings
 */
function openlab_process_group_calendar_settings( $group_id ) {
	if ( ! empty( $_POST['openlab-bpeo-event-create-access'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$access_level = sanitize_text_field( $_POST['openlab-bpeo-event-create-access'] );
		groups_update_groupmeta( $group_id, 'openlab_bpeo_event_create_access', $access_level );
	} else {
		groups_delete_groupmeta( $group_id, 'openlab_bpeo_event_create_access' );
	}
}

add_action( 'groups_group_settings_edited', 'openlab_process_group_calendar_settings' );

/**
 * Saving some extra meta when inserting a venue
 * This is not utilized yet, but will be, and I wanted to get it set up
 * before users actually start entering venues
 * Also used for venues with no physical address, by setting LatLng to NaN
 * @param type $venue_id
 */
function openlab_bpeo_extra_venue_meta( $venue_id ) {

	//attaching venue to group posts
	if ( ! is_admin() ) {

		if ( isset( $_POST['post_ID'] ) ) {

			//check for legacy
			$post_ids = get_metadata( 'eo_venue', $venue_id, '_postids', true );

			if ( $post_ids && ! empty( $post_ids ) ) {

				$post_ids[] = $_POST['post_ID'];
				$post_ids   = array_unique( $post_ids );
			} else {

				$post_ids = array( $_POST['post_ID'] );
			}

			update_metadata( 'eo_venue', $venue_id, '_postids', $post_ids );
		}

		//attaching venue to user
		if ( isset( $_POST['user_ID'] ) ) {

			//check for legacy
			$user_ids = get_metadata( 'eo_venue', $venue_id, '_userids', true );

			if ( $user_ids && ! empty( $user_ids ) ) {

				$user_ids[] = $_POST['user_ID'];
				$user_ids   = array_unique( $user_ids );
			} else {

				$user_ids = array( $_POST['user_ID'] );
			}

			update_metadata( 'eo_venue', $venue_id, '_userids', $user_ids );
		}
	}

	//check if venue has physical address
	//if it does not, set Lat and Lng to NaN
	$venue_address = eo_get_venue_meta( $venue_id, '_address' );

	if ( empty( $venue_address ) ) {
		eo_update_venue_meta( $venue_id, '_lat', 0.00000 );
		eo_update_venue_meta( $venue_id, '_lng', 0.00000 );
	}
}
add_action( 'eventorganiser_save_venue', 'openlab_bpeo_extra_venue_meta' );

/**
 * Render the "Notify subsribers" checkbox during event creation/editing.
 */
function openlab_bpeo_render_silent_checkbox( $post_type, $location, $post ) {
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

	?>
	<p id="bpeo-silent-wrapper" style="display:none">
		<?php openlab_notify_group_members_ui( bp_is_action_variable( 'new-event', 0 ) ); ?>
	</p>
	<?php
}
add_action( 'do_meta_boxes', 'openlab_bpeo_render_silent_checkbox', 10, 3 );
remove_action( 'do_meta_boxes', 'bpeo_render_silent_checkbox', 10, 3 );

/**
 * Trick: Hook in before bpeo_send_bpges_notification_for_user() and fake $_POST.
 */
function openlab_bpeo_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( 'groups' !== $activity->component || 0 !== strpos( $activity->type, 'bpeo_' ) ) {
		return $send_it;
	}

	if ( ! $send_it ) {
		return $send_it;
	}

	if ( openlab_notify_group_members_of_this_action() && 'no' !== $sub ) {
		unset( $_POST['bpeo-silent'] );
	} else {
		$_POST['bpeo-silent'] = 1;
	}

	return openlab_notify_group_members_of_this_action();
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_bpeo_activity_notification_control', 5, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_bpeo_activity_notification_control', 5, 4 );
