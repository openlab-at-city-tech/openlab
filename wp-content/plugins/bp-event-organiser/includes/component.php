<?php

/**
 * `BP_Component` implementation.
 */
class BPEO_Component extends BP_Component {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::start(
			'bpeo',
			__( 'Event Organiser', 'bp-event-organiser' ),
			BPEO_PATH,
			array( 'adminbar_myaccount_order' => 36 )
		);

		// setup single event screen
		// run on 'bp_parse_query' to allow EO's post type to register
		add_action( 'bp_parse_query', array( $this, 'setup_single_event_screen' ) );
	}

	/**
	 * Set up globals.
	 */
	public function setup_globals( $args = array() ) {
		parent::setup_globals( array(
			'slug' => bpeo_get_events_slug(),
			'has_directory' => false,
		) );
	}

	/**
	 * Set up navigation.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
		$name = __( 'Events', 'bp-event-organiser' );

		$main_nav = array(
			'name' => $name,
			'slug' => $this->slug,
			'position' => 65,
			'show_for_displayed_user' => false,
			'screen_function' => array( $this, 'template_loader' ),
			'default_subnav_slug' => 'calendar',
		);

		$sub_nav[] = array(
			'name' => __( 'Calendar', 'bp-event-organiser' ),
			'slug' => 'calendar', // @todo better l10n
			'parent_url' => bp_displayed_user_domain() . trailingslashit( $this->slug ),
			'parent_slug' => $this->slug,
			'user_has_access' => bp_core_can_edit_settings(),
			'screen_function' => array( $this, 'template_loader' ),
		);

		$sub_nav[] = array(
			'name' => __( 'Upcoming', 'bp-event-organiser' ),
			'slug' => 'upcoming', // @todo better l10n
			'parent_url' => bp_displayed_user_domain() . trailingslashit( $this->slug ),
			'parent_slug' => $this->slug,
			'user_has_access' => bp_core_can_edit_settings(),
			'screen_function' => array( $this, 'template_loader' ),
		);

		$sub_nav[] = array(
			'name' => __( 'Manage', 'bp-event-organiser' ),
			'slug' => 'manage',
			'parent_url' => bp_displayed_user_domain() . trailingslashit( $this->slug ),
			'parent_slug' => $this->slug,
			'user_has_access' => bp_core_can_edit_settings() && current_user_can( 'publish_events' ),
			'screen_function' => array( $this, 'template_loader' ),
		);

		$sub_nav[] = array(
			'name' => __( 'New Event', 'bp-event-organiser' ),
			'slug' => bpeo_get_events_new_slug(),
			'parent_url' => bp_displayed_user_domain() . trailingslashit( $this->slug ),
			'parent_slug' => $this->slug,
			'user_has_access' => bp_core_can_edit_settings() && current_user_can( 'publish_events' ),
			'screen_function' => array( $this, 'template_loader' ),
			'position' => 99,
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up admin bar links.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		$bp = buddypress();

		if ( ! is_user_logged_in() ) {
			return;
		}

		// Add the "My Account" sub menus
		$wp_admin_nav[] = array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'my-account-events',
			'title'  => __( 'Events', 'bp-event-organiser' ),
			'href'   => bp_loggedin_user_domain() . bpeo_get_events_slug(),
		);

		$wp_admin_nav[] = array(
			'parent' => 'my-account-events',
			'id'     => 'my-account-events-calendar',
			'title'  => __( 'Calendar', 'bp-event-organiser' ),
			'href'   => trailingslashit( bp_loggedin_user_domain() . bpeo_get_events_slug() ),
		);

		$wp_admin_nav[] = array(
			'parent' => 'my-account-events',
			'id'     => 'my-account-events-new',
			'title'  => __( 'New Event', 'bp-event-organiser' ),
			'href'   => trailingslashit( bp_loggedin_user_domain() . bpeo_get_events_slug() . '/' . bpeo_get_events_new_slug() ),
		);

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up single event screen.
	 */
	public function setup_single_event_screen() {
		if ( ! bp_is_user() ) {
			return;
		}

		if ( ! bp_is_current_component( bpeo_get_events_slug() ) ) {
			return;
		}

		if ( bp_is_current_action( bpeo_get_events_new_slug() ) ) {
			return;
		}

		$reserved_slugs = array( 'manage', 'calendar', 'upcoming' );
		if ( in_array( bp_current_action(), $reserved_slugs ) ) {
			return;
		}

		// omit iCal feeds
		if ( bp_is_current_action( 'ical' ) || true === ctype_xdigit( bp_current_action() ) ) {
			$this->ical_action();
			return;
		}

		// This is not a single event.
		if ( ! bp_current_action() ) {
			return;
		}

		// Set up query args
		$query_args = array();
		$query_args['suppress_filters'] = true;
		$query_args['orderby'] = 'none';
		$query_args['post_status'] = array( 'publish', 'pending', 'private', 'draft', 'future', 'trash' );

		// this is a draft with no slug
		if ( false !== strpos( bp_current_action(), 'draft-' ) ) {
			$query_args['post__in'] = (array) str_replace( 'draft-', '', bp_current_action() );

		// use post slug
		} else {
			$query_args['name'] = bp_current_action();
		}

		// query for the event
		$event = eo_get_events( $query_args );

		// check if event exists
		if ( empty( $event ) ) {
			bp_core_add_message( __( 'Event does not exist.', 'bp-event-organiser' ), 'error' );
			bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bpeo_get_events_slug() ) );
			die();
		}

		// Only those on their own profile or moderators can view single events
		if ( false === bp_core_can_edit_settings() ) {
			bp_core_no_access();
			die();
		}

		// save queried event as property
		$this->queried_event = $event[0];

		// add our screen hook
		add_action( 'bp_screens', array( $this, 'template_loader' ) );
	}

	public function template_loader() {
		// new event
		if ( bpeo_is_action( 'new' ) ) {
			// magic admin screen code!
			require BPEO_PATH . '/includes/class.bpeo_frontend_admin_screen.php';

			$this->create_event = new BPEO_Frontend_Admin_Screen( array(
				'type' => 'new',
				'redirect_root'  => trailingslashit( bp_displayed_user_domain() . $this->slug )
			) );

			add_action( 'bp_template_content', array( $this->create_event, 'display' ) );

		// manage
		} elseif ( bpeo_is_action( 'manage' ) ) {
			$this->manage_events_screen();
			add_action( 'bp_template_title',   create_function( '', "
				_e( 'Manage Events', 'bp-event-organiser' );
			" ) );
			add_action( 'bp_template_content', array( $this, 'display_manage_events' ) );

		// single event
		} elseif ( false === bp_is_current_action( 'calendar' ) && false === bp_is_current_action( 'upcoming' ) ) {
			$this->single_event_screen();
			add_action( 'bp_template_title',   array( $this, 'display_single_event_title' ) );
			add_action( 'bp_template_content', array( $this, 'display_single_event' ) );

		// user calendar
		} else {
			add_action( 'bp_template_content', array( $this, 'select_template' ) );
		}

		bp_core_load_template( 'members/single/plugins' );
	}

	/**
	 * Utility function for selecting the correct template to be loaded in the component
	 */
	public function select_template() {
		$template_slug = bp_current_action();

		// show secondary title if filter is in use
		$filter_title = bpeo_get_the_filter_title();
		if ( ! empty( $filter_title ) ) {
			echo "<h4>{$filter_title}</h4>";
		}

		// use our template stack
		add_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );

		// load our template part
		eo_get_template_part( $template_slug );

		// remove our template stack
		remove_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );
	}

	/**
	 * Single event screen handler.
	 */
	protected function single_event_screen() {
		if ( empty( $this->queried_event ) ) {
			return;
		}

		// edit single event logic
		if ( bpeo_is_action( 'edit' ) ) {
			// check if user has access
			if ( false === current_user_can( 'edit_event', $this->queried_event->ID ) ) {
				bp_core_add_message( __( 'You do not have access to edit this event.', 'bp-event-organiser' ), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug ) . "{$this->queried_event->post_name}/" );
				die();
			}

			// magic admin screen code!
			require BPEO_PATH . '/includes/class.bpeo_frontend_admin_screen.php';

			$this->edit_event = new BPEO_Frontend_Admin_Screen( array(
				'queried_post'   => $this->queried_event,
				'redirect_root'  => trailingslashit( bp_displayed_user_domain() . $this->slug )
			) );

		// restore single event logic
		} elseif ( bpeo_is_action( 'restore' ) ) {
			// check if user has access
			if ( false === current_user_can( 'delete_event', $this->queried_event->ID ) ) {
				bp_core_add_message( __( 'You do not have permission to restore this event.', 'bp-event-organiser' ), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug ) . "{$this->queried_event->post_name}/" );
				die();
			}

			// verify nonce
			if ( false === bp_action_variable( 1 ) || ! wp_verify_nonce( bp_action_variable( 1 ), "bpeo_restore_event_{$this->queried_event->ID}" ) ) {
				bp_core_add_message( __( 'You do not have permission to restore this event.', 'bp-event-organiser' ), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug ) . "{$this->queried_event->post_name}/" );
				die();
			}

			// untrash event
			$delete = wp_untrash_post( $this->queried_event->ID );
			if ( false === $delete ) {
				bp_core_add_message( __( 'There was a problem restoring the event.', 'bp-event-organiser' ), 'error' );
			} else {
				bp_core_add_message( sprintf( __( "'%s' restored.", 'bp-event-organiser' ), $this->queried_event->post_title ) );
			}

			bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug . '/manage' ) );
			die();


		// delete single event logic
		} elseif ( bpeo_is_action( 'delete' ) ) {
			// check if user has access
			if ( false === current_user_can( 'delete_event', $this->queried_event->ID ) ) {
				bp_core_add_message( __( 'You do not have permission to delete this event.', 'bp-event-organiser' ), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug ) . "{$this->queried_event->post_name}/" );
				die();
			}

			// verify nonce
			if ( false === bp_action_variable( 1 ) || ! wp_verify_nonce( bp_action_variable( 1 ), "bpeo_delete_event_{$this->queried_event->ID}" ) ) {
				bp_core_add_message( __( 'You do not have permission to delete this event.', 'bp-event-organiser' ), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $this->slug ) . "{$this->queried_event->post_name}/" );
				die();
			}

			// delete event
			$delete = wp_delete_post( $this->queried_event->ID, true );
			if ( false === $delete ) {
				bp_core_add_message( __( 'There was a problem deleting the event.', 'bp-event-organiser' ), 'error' );
			} else {
				bp_core_add_message( __( 'Event deleted.', 'bp-event-organiser' ) );
			}

			// set up redirect URL
			$redirect = trailingslashit( bp_displayed_user_domain() . $this->slug );
			if ( false !== strpos( wp_get_referer(), '/manage/' ) ) {
				$redirect .= 'manage/';
			}

			bp_core_redirect( $redirect );
			die();
		}
	}

	/**
	 * Display the single event title within a user's profile.
	 *
	 * This is for themes using the 'bp_template_title' hook.
	 */
	public function display_single_event_title() {
		if ( bpeo_is_action( 'edit' ) ) {
			return;
		}

		if ( empty( $this->queried_event ) ) {
			return;
		}

		// save $post global temporarily
		global $post;
		$_post = false;
		if ( ! empty( $post ) ) {
			$_post = $post;
		}

		// override the $post global so EO can use its functions
		$post = $this->queried_event;

		the_title();

		// revert $post global
		if ( ! empty( $_post ) ) {
			$post = $_post;
		}
	}

	/**
	 * Display a single event within a user's profile.
	 *
	 * @todo Move part of this functionality into a template part so theme devs can customize.
	 * @todo Merge single event display logic for users and groups
	 */
	public function display_single_event() {
		if ( empty( $this->queried_event ) ) {
			return;
		}

		// save $post global temporarily
		global $post, $pages;
		$_post = false;
		if ( ! empty( $post ) ) {
			$_post = $post;
		}

		// override the $post global so EO can use its functions
		$post = $this->queried_event;

		// edit screen has its own display method
		if ( bpeo_is_action( 'edit' ) ) {
			$this->edit_event->display();

			// revert $post global
			if ( ! empty( $_post ) ) {
				$post = $_post;
			}
			return;
		}

		// output title if theme is not using the 'bp_template_title' hook
		if ( ! did_action( 'bp_template_title' ) ) {
			the_title( '<h2>', '</h2>' );
		}

		// do something after the title
		// this is the same hook used in the admin area
		do_action( 'edit_form_after_title', $post );

		// BP removes all filters for 'the_content' during theme compat.
		// bring it back and remove BP's content filter
		bp_restore_all_filters( 'the_content' );
		remove_filter( 'the_content', 'bp_replace_the_content' );

		// hey there, mr. hack!
		//
		// we're going to use the_content() in our BPEO template part.  so we want to
		// get the rendered post content for the event without BP theme compat running
		// its filter.
		//
		// get_the_content() is weird and checks the $pages global for the content
		if ( bp_use_theme_compat_with_current_theme() ) {
			$key = 0;

		// bp-default requires the key set to -1
		} else {
			$key = -1;
		}
		$pages[$key] = apply_filters( 'the_content', $post->post_content );

		// remove all filters like before
		bp_remove_all_filters( 'the_content' );

		// output single event content
		eo_get_template_part( 'content-eo', 'event' );

		// revert $post global
		if ( ! empty( $_post ) ) {
			$post = $_post;
		}
	}

	/**
	 * "Manage Events" screen handler.
	 */
	protected function manage_events_screen() {
		// 'Reset Private URL' action
		if ( ! empty( $_GET['bpeo-reset'] ) ) {
			check_admin_referer( 'bpeo_user_reset_private_ical', 'bpeo-reset' );

			// reset hash
			bpeo_get_the_user_private_ical_hash( bp_displayed_user_id(), true );

			bp_core_add_message( __( 'Private iCalendar URL has been reset. Please copy the new link below to use in your calendar application.', 'bp-event-organiser' ) );
			bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bpeo_get_events_slug() . '/manage' ) );
			die();
		}

		$this->events = new WP_Query( array(
			'author' => bp_displayed_user_id(),
			'post_type' => 'event',
			'orderby' => 'eventstart',
			'suppress_filters' => false,
			'showpastevents' => true,
			'showrepeats' => 0,
			'order' => 'ASC',
			'nopaging' => true,
			'post_status' => array( 'pending', 'draft', 'future', 'trash' )
		) );
	}

	/**
	 * "Manage Events" screen content.
	 */
	public function display_manage_events() {
		// inline CSS for now during dev period
	?>
		<style type="text/css">
		#manage-events .post-status {width:5.5em;}
		#manage-events .event-date {width:11.5em;}
		#manage-events .post-date {width:94px; padding-right:0;}

		#manage-events td {vertical-align:top; padding-top:15px;}

		td.post-status span {text-transform:uppercase; color:#fff; font-weight:600; border-radius:4px; padding:5px;}
			td.status-draft span {background:green;}
			td.status-pending span {background:orange;}
			td.status-future span {background:purple;}
			td.status-trash span {background:red;}

		td.event-date p {font-size: 0.85em; line-height:1.3; margin-top:0.5em;}
		</style>

	<?php
		// 'Manage Events' content
		// @todo Move this to a template part.
		echo '<div id="manage-events">';

		if ( $this->events->have_posts() ) :
	?>

		<p><?php _e( 'At the moment, only unpublished events are shown here to allow you to edit them.' ); ?></p>

		<table>
			<thead>
				<th class="post-status"><?php _e( 'Status', 'bp-event-organiser' ); ?></th>
				<th class="post-title"><?php _ex( 'Event', 'Manage events header', 'bp-event-organiser' ); ?></th>
				<th class="event-date"><?php _e( 'Event Date', 'bp-event-organiser' ); ?></th>
				<th class="post-date"><?php _e( 'Publish Date', 'bp-event-organiser' ); ?></th>
			</thead>

			<tbody>

	<?php
			while( $this->events->have_posts() ) : $this->events->the_post();
				$slug   = ! empty( $this->events->post->post_name ) ? $this->events->post->post_name : 'draft-' . get_the_ID();
				$status = $this->events->post->post_status;

				// admin links
				// @todo maybe break this out into a function
				$links = array();
				if ( 'trash' !== $status ) {
					$links['edit'] = '<a href="' . esc_url( bp_displayed_user_domain() . bpeo_get_events_slug() . '/' . $slug . '/edit/' ) .'">' . __( 'Edit', 'bp-event-organiser' ) . '</a>';
				} else {
					$links['restore'] = '<a href="' . esc_url( bp_displayed_user_domain() . bpeo_get_events_slug() . '/' . $slug . '/restore/' . wp_create_nonce( "bpeo_restore_event_{$this->events->post->ID}" ) ) . '/">' . __( 'Restore', 'bp-event-organiser' ) . '</a>';
				}

				$links['delete'] = '<a class="confirm" href="' . esc_url( bp_displayed_user_domain() . bpeo_get_events_slug() . '/' . $slug . '/delete/' . wp_create_nonce( "bpeo_delete_event_{$this->events->post->ID}" ) ) . '/">' . __( 'Delete', 'bp-event-organiser' ) . '</a>';
	?>

			<tr>
				<td class="post-status status-<?php esc_attr_e( $status ); ?>" data-label="<?php esc_attr_e( 'Status', 'bp-event-organiser' ); ?>"><span><?php esc_html_e( $status ); ?></span></td>
				<td class="post-title" data-label="<?php esc_attr_e( 'Event', 'bp-event-organiser' ); ?>"><strong><?php the_title(); ?></strong> &ndash; <?php echo implode( ' &middot; ', $links ); ?></td>
				<td class="event-date" data-label="<?php esc_attr_e( 'Event Date', 'bp-event-organiser' ); ?>">
					<?php eo_the_start( 'M j, Y, ' . get_option( 'time_format' ) ); ?> to <?php eo_the_end( 'M j, Y, ' . get_option( 'time_format' ) ); ?>
					<p>(<?php eo_display_reoccurence( get_the_ID() ); ?>)</p>
				</td>
				<td class="post-date" data-label="<?php esc_attr_e( 'Publish Date', 'bp-event-organiser' ); ?>"><?php the_date(); ?></td>
			</tr>
	<?php
			endwhile;

			echo '</tbody></table>';

		else :
			_e( 'Looks like you have no unpublished events to manage.  To create a new event, click on the "New Event" link above.', 'bp-event-organiser' );

		endif;

		echo '</div>';

		// 'Manage iCalendar Settings' template part
		add_filter( 'eventorganiser_template_stack', 'bpeo_register_template_stack' );
		eo_get_template_part( 'buddypress/events/manage-user-ical' );
	}

	/**
	 * Validate iCalendar download.
	 */
	protected function ical_action() {
		$args = array(
			'filename' => bp_get_displayed_user_username(),
			'url'      => bp_displayed_user_domain()
		);

		// public iCal
		if ( bp_is_current_action( 'ical' ) ) {
			$args['name']   = bp_get_displayed_user_fullname();
			$args['author'] = bp_displayed_user_id();

		// private iCal
		} else {
			if ( false === bp_is_current_action( bpeo_get_the_user_private_ical_hash() ) ) {
				return;
			}

			if ( false === bp_is_action_variable( 'ical' ) ) {
				return;
			}

			$args['name'] = sprintf( __( '%s (Private)', 'bp-event-organiser' ), bp_get_displayed_user_fullname() );
			$args['bp_displayed_user_id'] = bp_displayed_user_id();
		}

		// iCal time!
		bpeo_do_ical_download( $args );
	}
}

buddypress()->bpeo = new BPEO_Component();
