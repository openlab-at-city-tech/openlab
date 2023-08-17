<?php
/**
 * Common functions.
 */

/**
 * Return sanitized version of the events slug.
 */
function bpeo_get_events_slug() {
	return sanitize_title( constant( 'BPEO_EVENTS_SLUG' ) );
}

/**
 * Return sanitized version of the new events slug.
 */
function bpeo_get_events_new_slug() {
	return sanitize_title( constant( 'BPEO_EVENTS_NEW_SLUG' ) );
}

/**
 * Register common assets.
 *
 * No longer used. See https://github.com/cuny-academic-commons/bp-event-organiser/issues/48.
 */
function bpeo_register_assets() {
	// Deprecated. See bpeo_enqueue_assets() and issue #48.
}

/**
 * Register and enqueue common assets.
 *
 * Because of reported conflicts between our version of Select2 and that required by other plugins, we register
 * scripts only when they're needed. This minimizes the chances of mismatches.
 */
function bpeo_enqueue_assets() {
	// Select2
	if ( false === wp_script_is( 'select2' ) ) {
		wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js', array( 'jquery' ) );
	}
	if ( false === wp_style_is( 'select2' ) ) {
		wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css' );
	}

	wp_enqueue_script( 'bp_event_organiser_js', BUDDYPRESS_EVENT_ORGANISER_URL . 'assets/js/bp-event-organiser.js', array( 'jquery' ), BUDDYPRESS_EVENT_ORGANISER_VERSION, true );

	wp_enqueue_script( 'bpeo-group-select', BUDDYPRESS_EVENT_ORGANISER_URL . 'assets/js/group-select.js', array( 'jquery', 'select2' ), BUDDYPRESS_EVENT_ORGANISER_VERSION, true );

	wp_localize_script( 'bp_event_organiser_js', 'BpEventOrganiserSettings', array(
		'calendar_filter_title' => __( 'Filters', 'bp-event-organiser' ),
		'calendar_author_filter_title' => __( 'By Author', 'bp-event-organiser' ),
		'calendar_group_filter_title' => __( 'By Group', 'bp-event-organiser' ),
		'loggedin_user_id' => bp_loggedin_user_id()
	) );

	wp_localize_script( 'bpeo-group-select', 'BpEventOrganiserSettings', array(
		'group_privacy_message' => __( 'You have added a group to this event.  Since groups have their own privacy settings, we have removed the ability to set the status for this event.', 'bp-event-organiser' ),
		'group_public_message' => sprintf( __( 'You have added a %1$s to this event. Since the added group is %2$s, be aware that your event will also be publicized on the sitewide event calendar.', 'bp-event-organiser' ),
			'<strong>' . __( 'public group', 'bp-event-organiser' ) . '</strong>',
			'<strong>' . __( 'public', 'bp-event-organiser' ) . '</strong>'
		),
	) );
}

/**
 * Filters the CPT arguments for Event Organiser.
 *
 * Currently, we do not allow any admin bar items to display for EO since we
 * grant all logged-in users the ability to create events, but we do not want
 * all users to see the Event item in the "New" dropdown.
 *
 * @param  array $args Current post type args.
 * @return array
 */
function bpeo_filter_register_post_type_args( $args = array() ) {
	$args['show_in_admin_bar'] = false;
	return $args;
}
add_filter( 'eventorganiser_event_properties', 'bpeo_filter_register_post_type_args' );

/**
 * Check if we're on a BPEO events page.
 *
 * This function is needed to normalize how conditional checks are done
 * across the members and groups components. Why? Because the BP groups
 * component shifts the current path over by 1. So, instead of doing two
 * different checks, we do it with one in this function.
 *
 * @return bool
 */
function bpeo_is_component() {
	$retval = false;

	if ( bp_is_user() ) {
		$is_component = 'bp_is_current_component';
	} elseif( bp_is_group() ) {
		$is_component = 'bp_is_current_action';
	} else {
		return $retval;
	}

	return $is_component( bpeo_get_events_slug() );
}

/**
 * Check if we're on a BPEO action page.
 *
 * @param string $action The action to check. eg. 'new', 'edit', 'delete' or 'manage'.
 * @return bool
 */
function bpeo_is_action( $action = '' ) {
	$retval = false;

	if ( bp_is_user() ) {
		$is_action = 'bp_is_current_action';
		$pos = 0;
	} elseif( bp_is_group() ) {
		$is_action = 'bp_is_action_variable';
		$pos = 1;
	} else {
		return $retval;
	}

	// not on an events page, so stop!
	if ( false === bpeo_is_component() ) {
		return $retval;
	}

	// alias of 'new'
	$action = 'new' === $action ? bpeo_get_events_new_slug() : $action;

	// check if we're on a 'new event' page
	if ( bpeo_get_events_new_slug() === $action ) {
		return $is_action( $action );
	}

	// check if we're on a 'manage events' page
	if ( 'manage' === $action ) {
		return $is_action( $action );
	}

	// all other actions - 'edit', 'delete'
	if ( false === bp_is_action_variable( $action, $pos ) ) {
		return $retval;
	}

	return true;
}

/**
 * Output the filter title depending on URL querystring.
 *
 * @see bpeo_get_the_filter_title()
 */
function bpeo_the_filter_title() {
	echo bpeo_get_the_filter_title();
}
	/**
	 * Return the filter title depending on URL querystring.
	 *
	 * If the 'cat' or 'tag' URL parameter is in use, this function will output
	 * a title based on these parameters.
	 *
	 * @return string
	 */
	function bpeo_get_the_filter_title() {
		$cat = $tag = '';

		if ( ! empty( $_GET['cat'] ) ) {
			$cat = str_replace( ',', ', ', esc_attr( $_GET['cat'] ) );
		}

		if ( ! empty( $_GET['tag'] ) ) {
			$tag = str_replace( ',', ', ', esc_attr( $_GET['tag'] ) );
		}

		if ( ! empty( $cat ) && ! empty( $tag ) ) {
			return sprintf( __( "Filtered by category '%1$s' and tag '%2$s'", 'bp-event-organiser' ), $cat, $tag );
		} elseif ( ! empty( $cat ) ) {
			return sprintf( __( "Filtered by category '%s'", 'bp-event-organiser' ), $cat );
		} elseif ( ! empty( $tag ) ) {
			return sprintf( __( "Filtered by tag '%s'", 'bp-event-organiser' ), $tag );
		} else {
			return '';
		}
	}

/**
 * Helper function to process and generate an iCal download.
 *
 * Must be used before get_header().
 *
 * @param array $r {
 *     Arguments for setting up the iCal download.  See {@link eo_get_events()}
 *     for full list of arguments, as well as {@link WP_Query}.
 *
 *     @type string $name     Name of the iCalendar. This shows up in the iCalendar file header.
 *     @type string $url      URL of source. This shows up in the iCalendar file header.
 *     @type string $filename Filename for the iCal download.
 * }
 */
function bpeo_do_ical_download( $r = array() ) {
	$r = wp_parse_args( $r, array(
		'name' => '',
		'url'  => '',

		// Emulate Google Calendar's default filename. Why not?
		'filename' => 'basic',

		// Query args; mostly copied from EO_Event_List_Widget.
		'posts_per_page'   => -1,
		'post_type'        => 'event',
		'suppress_filters' => false,
		'orderby'          => 'eventstart',
		'order'            => 'ASC',
		'showrepeats'      => 1,
		'group_events_by'  => '',
		'showpastevents'   => true,

		// Custom query args
		'post_status'      => array( 'publish' ),
	) );

	// Correct format for 'showpastevents' variable
	if ( 'false' === strtolower( $r['showpastevents'] ) ) {
		$r['showpastevents'] = 0;
	}

	$filename = sanitize_title( $r['filename'] ) . '.ics';

	// Override iCalendar name
	$name = esc_attr( $r['name'] );
	if ( ! empty( $name ) ) {
		add_filter(
			'pre_option_blogname',
			function() use ( $name ) {
				return $name;
			}
		);
	}

	// Override iCalendar URL
	$url = esc_url( $r['url'] );
	if ( ! empty( $url ) ) {
		add_filter(
			'post_type_archive_link',
			function() use ( $url ) {
				return $url;
			}
		);
	}

	// Do our query.
	unset( $r['filename'], $r['name'], $r['url'] );
	$GLOBALS['wp_query'] = new WP_Query( $r );

	// Set proper headers
	nocache_headers();
	status_header( 200 );

	// iCal time!
	Event_Organiser_Im_Export::get_object()->export_events( $filename, null );
}

/**
 * Output the iCal link for an event.
 *
 * @param int $post_id The post ID.
 */
function bpeo_the_ical_link( $post_id ) {
	echo bpeo_get_the_ical_link( $post_id );
}
	/**
	 * Returns the iCal link for an event.
	 *
	 * Only works for the 'event' post type.
	 *
	 * @param  int $post_id The post ID.
	 * @return string
	 */
	function bpeo_get_the_ical_link( $post_id ) {
		if ( 'event' !== get_post( $post_id )->post_type ) {
			return '';
		}

		return trailingslashit( get_permalink( $post_id ) . 'feed/eo-events' );
	}

/**
 * Output the single event action links.
 *
 * @param WP_Post|int $post The WP Post object or the post ID.
 */
function bpeo_the_single_event_action_links( $post = 0 ) {
	echo bpeo_get_the_single_event_action_links( $post );
}
	/**
	 * Return the single event action links.
	 *
	 * @param  WP_Post|int $post The WP Post object or the post ID.
	 * @return string
	 */
	function bpeo_get_the_single_event_action_links( $post = 0 ) {
		if ( false === $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		if ( bp_is_user() ) {
			$back = $root = trailingslashit( bp_displayed_user_domain() . bpeo_get_events_slug() );
		} elseif ( bp_is_group() ) {
			$back = $root = bpeo_get_group_permalink();

		// WP single event page
		} else {
			// see if we have an events page
			$back = get_page_by_path( bpeo_get_events_slug() );
			if ( ! empty( $back ) ) {
				$back = trailingslashit( home_url( bpeo_get_events_slug() ) );

			// no events page, so use EO's main events archive page
			} else {
				$back = trailingslashit( home_url( trim( eventorganiser_get_option( 'url_events', 'events/event' ) ) ) );
			}

			$root = trailingslashit( bp_loggedin_user_domain() . bpeo_get_events_slug() );
		}

		$links = array();

		$links['back'] = '<a href="' . esc_url( $back ) . '">' . __( '&larr; Back', 'bp-events-organiser' ). '</a>';

		// @todo make 'edit' slug changeable
		if ( current_user_can( 'edit_event', $post->ID ) ) {
			$links['edit'] = '<a href="' . esc_url( $root ) . $post->post_name . '/edit/">' . __( 'Edit', 'bp-events-organiser' ). '</a>';
		}

		// @todo make 'delete' slug changeable
		if ( current_user_can( 'delete_event', $post->ID ) ) {
			$links['delete'] = '<a class="confirm" href="' . esc_url( $root ) . $post->post_name . '/delete/' . wp_create_nonce( "bpeo_delete_event_{$post->ID}" ). '/">' . __( 'Delete', 'bp-events-organiser' ). '</a>';
		}

		return implode( ' | ', (array) apply_filters( 'bpeo_get_the_single_event_action_links', $links ) );
	}

/**
 * Output the post status message for an event.
 *
 * @param WP_Post|int $post Either the WP post or the post ID.
 */
function bpeo_the_post_status_message( $post = 0 ) {
	echo bpeo_get_the_post_status_message( $post = 0 );
}

	/**
	 * Return the post status message for an event.
	 *
	 * @param  WP_Post|int $post Either the WP post or the post ID.
	 * @return string
	 */
	function bpeo_get_the_post_status_message( $post ) {
		if ( false === $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		$message = '';

		// if in admin area, stop now!
		if ( defined( 'WP_NETWORK_ADMIN' ) ) {
			return $message;
		}

		$post_type = get_post_type_object( $post->post_type );

		switch ( $post->post_status ) {
			case 'draft' :
				$message = sprintf( __( 'This %1$s is a draft.  Please remember to publish this %1$s once you are ready.', 'bp-event-organiser' ), strtolower( $post_type->labels->singular_name ) );
				break;

			case 'future' :
				$message = sprintf( __( 'This %1$s is scheduled to be published at <strong>%2$s</strong>.', 'bp-event-organiser' ),
	strtolower( $post_type->labels->singular_name ),
	/* translators: Date format for future event messages, see http://php.net/date */
	date_i18n( __( 'M j, Y @ G:i', 'bp-event-organiser' ), strtotime( $post->post_date ) )
				);
				break;

			case 'private' :
				if ( ! bp_is_group() ) {
					$message = sprintf( __( 'This %1$s is marked as private.  Only site moderators and yourself can view this %1$s.', 'bp-event-organiser' ), strtolower( $post_type->labels->singular_name ) );
				}
				break;
		}

		if ( ! empty( $message ) ) {
			$id = false === bpeo_is_action( 'edit' ) ? 'id="message"' : '';
			$message = '<div ' . $id . ' class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
		}

		return $message;
	}
add_action( 'edit_form_after_title', 'bpeo_the_post_status_message' );

/**
 * Determine if the post thumbnail has already displayed.
 *
 * @return bool
 */
function bpeo_has_thumbnail_shown() {
	// has to be greater than one due to post CSS class
	return _bpeo_thumbnail_counter() > 1;
}

/**
 * Count the number of times get_the_post_thumbnail() is called.
 *
 * @return int
 */
function _bpeo_thumbnail_counter() {
	static $counter = 0;

	if ( in_the_loop() ) {
		++$counter;
	}

	return $counter;
}
add_action( 'begin_fetch_post_thumbnail_html',  '_bpeo_thumbnail_counter' );


/** HOOKS ***************************************************************/

/**
 * Replace EO's default content with our own one when on a canonical event page.
 *
 * If you want to use EO's original default content, use this snippet:
 *     add_filter( 'bpeo_enable_replace_canonical_event_content', '__return_false' );
 *
 * @param  string $retval Existing canonical event content.
 * @return string
 */
function bpeo_remove_default_canonical_event_content( $retval ) {
	// bail if we shouldn't replace the existing content
	if ( false === (bool) apply_filters( 'bpeo_enable_replace_canonical_event_content', true ) ) {
		return $retval;
	}

	if( is_singular('event') && false === eventorganiser_is_event_template( '', 'event' ) ) {
		remove_filter( 'the_content', '_eventorganiser_single_event_content' );
		add_filter( 'the_content', 'bpeo_canonical_event_content', 999 );
	}

	return $retval;
}
add_filter( 'template_include', 'bpeo_remove_default_canonical_event_content', 20 );

/**
 * Callback filter to use BPEO's content for the canonical event page.
 *
 * @see bpeo_remove_default_canonical_event_content()
 *
 * @param  string $content Current content.
 * @return string $content The modified content.
 */
function bpeo_canonical_event_content( $content ) {
	global $pages;

	// bail if not canonical event
	if ( ! is_singular( 'event' ) ) {
		return $content;
	}

	// bail if not event post type
	if ( get_post_type( get_the_ID() ) != 'event' ) {
		return $content;
	}

	// reset get_the_content() to use already-rendered content so we can use it in
	// our content-eo-event.php template part
	//
	// get_the_content() is weird and checks the $pages global for the content
	// so let's use the rendered content here and set it in the $pages global
	$pages[0] = $content;

	// remove all filters for 'the_content' to prevent recursion when using
	// 'the_content' again
	bp_remove_all_filters( 'the_content' );

	// buffer the template part
	ob_start();
	eo_get_template_part( 'content-eo', 'event' );
	$tpart = ob_get_contents();
	ob_end_clean();

	// restore filters for 'the_content'
	bp_restore_all_filters( 'the_content' );

	remove_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );

	return $tpart;
}

/**
 * Registers BPEO's template directory with EO's template stack.
 *
 * To register the stack, use:
 *     add_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );
 *
 * @param  array $retval Current template stack.
 * @return array|string
 */
function bpeo_register_template_stack( $retval = array() ) {
	$dir = constant( 'BPEO_PATH' ) . 'templates/';

	// inject our stack between the current theme and EO's template directory
	if ( ! empty( $retval ) ) {
		array_splice( $retval, 2, 0, $dir );

	// for BuddyPress
	} else {
		$retval = $dir;
	}
	return $retval;
}

/**
 * Use our template stack only when calling the content-eo-event.php template.
 *
 * The content-eo-event.php template is a custom template bundled with BPEO.  We
 * want EO to use our template directory ahead of their own.
 *
 * @param string $slug The template part slug
 * @param string $name The template part name.
 */
function bpeo_add_template_stack_to_content_event_template( $slug, $name ) {
	// not matching our template name? stop now!
	if ( 'event' !== $name ) {
		return;
	}

	// use our template stack
	add_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );

	// this is for cleaning up the post global when using the
	// event-meta-single-event.php template with recurring events
	add_action( 'loop_end', 'bpeo_catch_reset_postdata' );
}
add_action( 'get_template_part_content-eo', 'bpeo_add_template_stack_to_content_event_template', 10, 2 );

/**
 * Filter event taxonomy term links to match the current BP page.
 *
 * BP event content should be displayed within BP instead of event links
 * linking to Event Organiser's pages.
 *
 * @param  string $retval Current term links
 * @return string
 */
function bpeo_filter_term_list( $retval = '' ) {
	if ( ! is_buddypress() ) {
		return $retval;
	}

	global $wp_rewrite;

	$taxonomy = str_replace( 'term_links-', '', current_filter() );
	$base = str_replace( "%{$taxonomy}%", '', $wp_rewrite->get_extra_permastruct( $taxonomy ) );
	$base = home_url( $base );

	// group
	if ( bp_is_group() ) {
		$bp_base = bpeo_get_group_permalink();

	// assume user
	} else {
		$bp_base = trailingslashit( bp_displayed_user_domain() . bpeo_get_events_slug() );
	}

	// set query arg
	if ( 'event-tag' === $taxonomy ) {
		$query_arg = 'tag';
	} else {
		$query_arg = 'cat';
	}

	// string manipulation
	$retval = str_replace( $base, $bp_base . "?{$query_arg}=", $retval );
	$retval = str_replace( '/"', '"', $retval );
	return $retval;
}
add_filter( 'term_links-event-tag',      'bpeo_filter_term_list' );
add_filter( 'term_links-event-category', 'bpeo_filter_term_list' );

/**
 * Add iCal link to single event pages.
 */
function bpeo_add_ical_link_to_eventmeta() {
	// do not show for drafts
	if ( 'draft' === get_post( get_the_ID() )->post_status ) {
		return;
	}
?>
	<li><?php
		printf(
		__( "%sDownload iCalendar file%s to save this event to your preferred calendar application", 'bp-event-organiser' ),
		'<a class="bpeo-ical-link" href="' . bpeo_get_the_ical_link( get_the_ID() ) . '"><span class="icon"></span>',
		'</a>'
	); ?></li>

<?php
}
add_action( 'eventorganiser_additional_event_meta', 'bpeo_add_ical_link_to_eventmeta', 50 );

/**
 * Whitelist BPEO shortcode attributes.
 *
 * @param array $out Output array of shortcode attributes.
 * @param array $pairs Default attributes as defined by EO.
 * @param array $atts Attributes passed to the shortcode.
 * @return array
 */
function bpeo_filter_eo_fullcalendar_shortcode_attributes( $out, $pairs, $atts ) {
	$whitelisted_atts = array(
		'bp_group',
		'bp_displayed_user_id',
	);

	foreach ( $atts as $att_name => $att_value ) {
		if ( isset( $out[ $att_name ] ) ) {
			continue;
		}

		if ( ! in_array( $att_name, $whitelisted_atts ) ) {
			continue;
		}

		$out[ $att_name ] = $att_value;
	}

	return $out;
}
add_filter( 'shortcode_atts_eo_fullcalendar', 'bpeo_filter_eo_fullcalendar_shortcode_attributes', 10, 3 );

/**
 * Disable EO's transient cache for calendar queries.
 */
add_filter( 'pre_transient_eo_full_calendar_public', '__return_empty_array' );
add_filter( 'pre_transient_eo_full_calendar_public_priv', '__return_empty_array' );

/**
 * Add the Room field to the Event editing metabox.
 *
 * @param WP_Post $post Post currently being edited.
 */
function bpeo_add_room_field_to_metabox( WP_Post $post ) {
	if ( ! taxonomy_exists( 'event-venue' ) ) {
		return;
	}

	$room = get_post_meta( $post->ID, 'bpeo_room', true );

	?>

	<div class="eo-grid-row eo-room">
		<div class="eo-grid-4">
			<label for="room"><?php esc_html_e( 'Room:', 'bp-event-organiser' ); ?></label>
		</div>
		<div class="eo-grid-8">
			<input type="text" id="room" name="eo_input[room]" value="<?php echo esc_attr( $room ); ?>" />
		</div>

		<?php wp_nonce_field( 'bpeo_room_' . $post->ID, 'bpeo-room-nonce', false ); ?>
	</div>

	<?php
}
add_action( 'eventorganiser_metabox_additional_fields', 'bpeo_add_room_field_to_metabox' );

/**
 * Save Room data when an event is saved.
 *
 * Fired with priority 15 to follow `eventorganiser_details_save()`.
 *
 * @param int $post_id ID of the post being edited.
 */
function bpeo_save_room_field_on_post_save( $post_id ) {
	if ( ! isset( $_POST['bpeo-room-nonce'] ) || ! wp_verify_nonce( $_POST['bpeo-room-nonce'], 'bpeo_room_' . $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_event', $post_id ) ) {
		return;
	}

	$room = isset( $_POST['eo_input']['room'] ) ? wp_unslash( $_POST['eo_input']['room'] ) : '';

	update_post_meta( $post_id, 'bpeo_room', $room );
}
add_action( 'save_post', 'bpeo_save_room_field_on_post_save', 15 );

/**
 * Display Room info on event page.
 *
 * Hooked at priority 0 so it's directly after Venue.
 */
function bpeo_list_room() {
	$room = get_post_meta( get_the_ID(), 'bpeo_room', true );
	if ( ! $room ) {
		return;
	}

	printf( '<li><strong>' . esc_html( 'Room:', 'bp-event-organiser' ) . '</strong> %s</li>', esc_html( $room ) );
}
add_action( 'eventorganiser_additional_event_meta', 'bpeo_list_room', 0 );

/**
 * Get an item's calendar color.
 *
 * Will select one randomly from a whitelist if not found.
 *
 * @param int    $item_id   ID of the item.
 * @param string $item_type Type of the item. 'author' or 'group'.
 * @return string Hex code for the item color.
 */
function bpeo_get_item_calendar_color( $item_id, $item_type ) {
	$color = '';
	switch ( $item_type ) {
		case 'group' :
			$color = groups_get_groupmeta( $item_id, 'bpeo_calendar_color' );
			break;

		case 'author' :
		default :
			$color = bp_get_user_meta( $item_id, 'bpeo_calendar_color', true );
			break;
	}

	if ( ! $color ) {
		// http://stackoverflow.com/a/4382138
		$colors = array(
			'FFB300', // Vivid Yellow
			'803E75', // Strong Purple
			'FF6800', // Vivid Orange
			'A6BDD7', // Very Light Blue
			'C10020', // Vivid Red
			'CEA262', // Grayish Yellow
			'817066', // Medium Gray

			// The following don't work well for people with defective color vision
			'007D34', // Vivid Green
			'F6768E', // Strong Purplish Pink
			'00538A', // Strong Blue
			'FF7A5C', // Strong Yellowish Pink
			'53377A', // Strong Violet
			'FF8E00', // Vivid Orange Yellow
			'B32851', // Strong Purplish Red
			'F4C800', // Vivid Greenish Yellow
			'7F180D', // Strong Reddish Brown
			'93AA00', // Vivid Yellowish Green
			'593315', // Deep Yellowish Brown
			'F13A13', // Vivid Reddish Orange
			'232C16', // Dark Olive Green
		);

		$index = array_rand( $colors );
		$color = $colors[ $index ];

		switch ( $item_type ) {
			case 'group' :
				groups_update_groupmeta( $item_id, 'bpeo_calendar_color', $color );
				break;

			case 'author' :
			default :
				bp_update_user_meta( $item_id, 'bpeo_calendar_color', $color );
				break;
		}
	}

	return $color;
}

/**
 * Ensure that wp_reset_postdata() doesn't reset the post back to page ID 0.
 *
 * The event meta template provided by EO uses {@link wp_reset_postdata()} when
 * an event is recurring.  This interferes with BuddyPress when using EO's
 * 'eventorganiser_additional_event_meta' hook and wanting to fetch EO's WP
 * post for further data output.
 *
 * This method catches the end of the reoccurence event loop and wipes out the
 * post so wp_reset_postdata() doesn't reset the post back to page ID 0.
 */
function bpeo_catch_reset_postdata( $q ) {
	// check if a reoccurence loop occurred; if not, bail
	if ( empty( $q->query['post_type'] ) ) {
		return;
	}

	// wipe out the post property in $wp_query to prevent our page from resetting
	// when wp_reset_postdata() is used
	$GLOBALS['wp_query']->post = null;
}

/**
 * Filter attachments when selecting a "Featured Image" on the frontend.
 *
 * By default, the "Featured Image" metabox shows all available attachments
 * across the site.  We do not want to do this due to privacy issues. Instead,
 * this function filters the attachments query to only list attachments
 * uploaded by the logged-in user.
 *
 * @see https://github.com/cuny-academic-commons/bp-event-organiser/issues/17#issuecomment-99083587
 *
 * @param array $retval Current attachment query arguments
 * @return array
 */
function bpeo_filter_ajax_query_attachments( $retval ) {
	// don't do this in the admin area or if user isn't logged in
	if ( defined( 'WP_NETWORK_ADMIN' ) || false === is_user_logged_in() ) {
		return $retval;
	}

	if ( empty( $_POST['post_id'] ) )  {
		return $retval;
	}

	// check if the post is our event type
	$post = get_post( $_POST['post_id'] );
	if ( 'event' !== $post->post_type ) {
		return $retval;
	}

	// modify the attachments query to filter by the logged-in user
	$retval['author'] = bp_loggedin_user_id();
	return $retval;
}
add_filter( 'ajax_query_attachments_args', 'bpeo_filter_ajax_query_attachments' );
