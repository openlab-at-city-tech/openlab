<?php

/**
 * OpenLab implementation of the WP toolbar
 */

/**
 * Ensure that the toolbar always shows
 */
add_filter( 'show_admin_bar', '__return_true', 999999 );


/**
 * Bootstrap
 */
add_action( 'add_admin_bar_menus', array( 'OpenLab_Admin_Bar', 'init' ) );

class OpenLab_Admin_Bar {
	public static function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Admin_Bar;
		}
	}

	function __construct() {
		// Bail if BP is not present
		if ( !class_exists( 'BP_Core' ) ) {
			return;
		}

		// Add a body style to distinguish between sites
		add_action( 'body_class', array( &$this, 'body_class' ), 999 );
		add_action( 'admin_body_class', array( &$this, 'admin_body_class' ), 999 );

		// Enqueue styles
		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );
		add_action( 'admin_print_styles', array( &$this, 'enqueue_styles' ) );

		// Removes the rude WP logo menu item
		remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );

		// Removes the Search menu item
		remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );

		add_action( 'admin_bar_menu', array( $this, 'add_network_menu' ), 1 );

		// Logged-in only
		if ( is_user_logged_in() ) {
			add_action( 'admin_bar_menu', array( $this, 'add_my_openlab_menu' ), 1 );
			add_action( 'admin_bar_menu', array( $this, 'change_howdy_to_hi' ), 7 );
			add_action( 'admin_bar_menu', array( $this, 'prepend_my_to_my_openlab_items' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'remove_notifications_hook' ), 5 );

			// Don't show the My Sites menu
			remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );

			// Don't show the Edit Group or Edit Member menus
			remove_action( 'admin_bar_menu', 'bp_groups_group_admin_menu', 99 );
			remove_action( 'admin_bar_menu', 'bp_members_admin_bar_user_admin_menu', 99 );

			// Add the notification menus
			add_action( 'admin_bar_menu', array( $this, 'add_invites_menu' ), 22 );
			add_action( 'admin_bar_menu', array( $this, 'add_messages_menu' ), 24 );
			add_action( 'admin_bar_menu', array( $this, 'add_activity_menu' ), 26 );

			add_action( 'admin_bar_menu', array( $this, 'maybe_remove_thisblog' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'remove_adduser' ), 9999 );

			add_action( 'admin_bar_menu', array( $this, 'add_logout_item' ), 9999 );
			add_action( 'admin_bar_menu', array( $this, 'fix_logout_redirect' ), 10000 );
		} else {
			add_action( 'admin_bar_menu', array( $this, 'add_signup_item' ), 30 );
			add_action( 'admin_bar_menu', array( $this, 'fix_tabindex' ), 999 );
		}
	}

 	/**
 	 * Add the main OpenLab menu
 	 */
 	function add_network_menu( $wp_admin_bar ) {
 		$wp_admin_bar->add_node( array(
 			/*'parent' => 'top-secondary',*/
			'id'     => 'openlab',
			'title'  => '<span class="openlab-open">Open</span>Lab', // Span is here in case you want to bold 'OPEN'
			'href'   => bp_get_root_domain(),
			'meta'	 => array(
				'tabindex' => 90
			)
 		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'home',
			'title'  => 'Home',
			'href'   => bp_get_root_domain()
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'about',
			'title'  => 'About',
			'href'   => trailingslashit( bp_get_root_domain() . '/about' )
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'people',
			'title'  => 'People',
			'href'   => trailingslashit( bp_get_root_domain() . '/people' )
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'courses',
			'title'  => 'Courses',
			'href'   => trailingslashit( bp_get_root_domain() . '/courses' )
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'projects',
			'title'  => 'Projects',
			'href'   => trailingslashit( bp_get_root_domain() . '/projects' )
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'clubs',
			'title'  => 'Clubs',
			'href'   => trailingslashit( bp_get_root_domain() . '/clubs' )
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'portfolios',
			'title'  => 'Portfolios',
			'href'   => trailingslashit( bp_get_root_domain() . '/portfolios' )
 		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'openlab',
			'id'     => 'help',
			'title'  => 'Help',
			'href'   => trailingslashit( bp_get_root_domain() . '/blog/help/openlab-help' )
 		) );
 	}

 	/**
 	 * Adds 'My OpenLab' menu
 	 */
 	function add_my_openlab_menu( $wp_admin_bar ) {
 		$wp_admin_bar->add_node( array(
			'id'    => 'my-openlab',
			'title' => 'My OpenLab',
			'href'  => bp_loggedin_user_domain()
		) );
 	}

	/**
	 * Change 'Howdy' message to 'Hi'
	 */
	function change_howdy_to_hi( $wp_admin_bar ) {
		global $bp;
		$wp_admin_bar->add_node( array(
			'id'    => 'my-account',
			'title' => sprintf( "Hi, %s", $bp->loggedin_user->userdata->display_name ),
			'meta'	=> array()
		) );
	}

	/**
	 * Removes BP default "My" items, and builds our own
	 */
	function prepend_my_to_my_openlab_items( $wp_admin_bar ) {
		$nodes = $wp_admin_bar->get_nodes();
		$my_openlab_nodes = array();

		foreach( $nodes as $id => $node ) {
			if ( 'my-account-buddypress' == $node->parent ) {
				$wp_admin_bar->remove_node( $id );
				$my_openlab_nodes[] = $id;
			}
		}

		// Loop through one more time and remove submenus (those with a parent that is a
		// child of my-openlab)
		unset( $nodes );
		$nodes = $wp_admin_bar->get_nodes();
		foreach( $nodes as $id => $node ) {
			if ( in_array( $node->parent, $my_openlab_nodes ) ) {
				$wp_admin_bar->remove_node( $id );
			}
		}

		// Now add our menus
		// profile, courses, projects, clubs, portfolio, friends, messages, invitations, dashboard
		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-profile',
			'title'  => 'My Profile',
			'href'   => bp_loggedin_user_domain()
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-courses',
			'title'  => 'My Courses',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-courses' )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-projects',
			'title'  => 'My Projects',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-projects' )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-clubs',
			'title'  => 'My Clubs',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-clubs' )
		) );

		// Only show a My Portfolio link for users who actually have one
		$portfolio_url = openlab_get_user_portfolio_url( bp_loggedin_user_id() );
		if ( !empty( $portfolio_url ) ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-portfolio',
				'title'  => sprintf( 'My %s', openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() ) ),
				'href'   => $portfolio_url
			) );
		}

		if ( bp_is_active( 'friends' ) ) {
			$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
			$request_count = count( $request_ids );
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-friends',
				'title'  => sprintf( 'My Friends <span class="toolbar-item-count count-' . $request_count . '">%d</span>', $request_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() )
			) );
		}

		if ( bp_is_active( 'messages' ) ) {
			$messages_count = bp_get_total_unread_messages_count();
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-messages',
				'title'  => sprintf( 'My Messages <span class="toolbar-item-count count-' . $messages_count . '">%d</span>', $messages_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() )
			) );
		}

		if ( bp_is_active( 'groups' ) ) {
			$invites = groups_get_invites_for_user();
			$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-invitations',
				'title'  => sprintf( 'My Invitations <span class="toolbar-item-count count-' . $invite_count . '">%d</span>', $invite_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' )
			) );
		}

		// My Dashboard points to the my-sites.php Dashboard panel for this user. However,
		// this panel only works if looking at a site where the user has Dashboard-level
		// permissions. So we have to find a valid site for the logged in user.
		$primary_site_id = get_user_meta( bp_loggedin_user_id(), 'primary_blog', true );
		$primary_site_url = set_url_scheme( get_blog_option( $primary_site_id, 'siteurl' ) );

		if ( !empty( $primary_site_id ) && !empty( $primary_site_url ) ) {

			// @todo Do we really want this kind of separator?
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-openlab-separator',
				'title'  => '-----------'
			) );

			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-dashboard',
				'title'  => 'My Dashboard',
				'href'   => $primary_site_url . '/wp-admin/my-sites.php'
			) );
		}
	}

	/**
	 * Remove the Notifications menu
	 *
	 * We have to do it in a function like this because of the way BP adds the menu in the first
	 * place
	 */
	function remove_notifications_hook( $wp_admin_bar ) {
		remove_action( 'admin_bar_menu', 'bp_members_admin_bar_notifications_menu', 90 );
	}

	/**
	 * Maybe remove the current blog menu
	 */
	function maybe_remove_thisblog( $wp_admin_bar ) {
		if ( !current_user_can( 'publish_posts' ) ) {
			$wp_admin_bar->remove_node( 'site-name' );
		}
	}

	/**
	 * Add the Invites menu (Friend Requests + Group Invitations)
	 */
	function add_invites_menu( $wp_admin_bar ) {
		if ( ! bp_is_active( 'friends' ) || ! bp_is_active( 'groups' ) ) {
			return;
		}

		// We need this data up front so we can provide counts
		$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
		$request_count = count( (array) $request_ids );

		$invites = groups_get_invites_for_user();
		$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;

		$total_count = $request_count + $invite_count;

		$wp_admin_bar->add_menu( array(
			'id' => 'invites',
			'title' => '<span class="toolbar-item-name">Invitations </span><span class="toolbar-item-count count-' . $total_count . '">' . $total_count . '</span>',
		) );

		/**
		 * FRIEND REQUESTS
		 */

		// "Friend Requests" title
		$wp_admin_bar->add_node( array(
			'parent' => 'invites',
			'id'     => 'friend-requests-title',
			'title'  => 'Friend Requests'
		) );

		if ( 0 < count( $request_ids ) ) {
			// "See More" - changed so it shows up for anything greater than 0
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'friend-requests-more',
				'title'  => 'See All Friends',
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests' )
			) );
		}

		$members_args = array(
			'max'     => 0
		);

		$members_args['include'] = ! empty( $request_ids ) ? implode( ',', array_slice( $request_ids, 0, 3 ) ) : '0';

		if ( bp_has_members( $members_args ) ) {
			while ( bp_members() ) {
				bp_the_member();

				// avatar
				$title = '<div class="item-avatar"><a href="' . bp_get_member_link() . '">' . bp_get_member_avatar() . '</a></div>';

				// name link
				$title .= '<div class="item"><div class="item-title"><a href="' . bp_get_member_link() . '">' . bp_get_member_name() . '</a></div></div>';

				// accept/reject buttons
				$title .= '<div class="action"><a class="button accept" href="' . bp_get_friend_accept_request_link() . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="button reject" href="' . bp_get_friend_reject_request_link() . '">' . __( 'Reject', 'buddypress' ) . '</a></div>';

				$wp_admin_bar->add_node( array(
					'parent' => 'invites',
					'id'     => 'friendship-' . bp_get_friend_friendship_id(),
					'title'  => $title,
					'meta'   => array(
						'class' => 'nav-content-item nav-friendship-request'
					)
				) );
			}

		} else {
			// The user has no friend requests
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'friend-requests-none',
				'title'  => 'No new friendship requests.',
				'meta'   => array(
					'class' => 'nav-no-items'
				)
			) );
		}

		/**
		 * INVITATIONS
		 */

		// "Invitations" title
		$wp_admin_bar->add_node( array(
			'parent' => 'invites',
			'id'     => 'invitations-title',
			'title'  => 'Invitations'
		) );

		// "See More" - changed so it shows up for anything greater than 0
		if ( !empty( $invites['groups'] )) {
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'invites-see-more',
				'title'  => 'See All Invites',
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' )
			) );
		}

		$groups_args = array(
			'type'    => 'invites',
			'user_id' => bp_loggedin_user_id()
		);

		if ( !empty( $invites['groups'] ) ) {
			$group_counter = 0;
			foreach ( (array) $invites['groups'] as $group ) {
				if ( $group_counter < 3 ) {
					// avatar
					$title = '<div class="item-avatar"><a href="' . bp_get_group_permalink( $group ) . '">' . 					bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'avatar_dir' => 'group-avatars', 'alt' => $group->name . ' avatar', 'width' => '50', 'height' => '50', 'class' => 'avatar', 'no_grav' => false )).'</a></div>' ;

					// name link
					$title .= '<div class="item"><div class="item-title"><a href="' . bp_get_group_permalink( $group ) . '">' . $group->name . '</a></div></div>';

					// accept/reject buttons
					$title .= '<div class="action"><a class="button accept" href="' . bp_get_group_accept_invite_link( $group ) . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="button reject" href="' . bp_get_group_reject_invite_link( $group ) . '">' . __( 'Reject', 'buddypress' ) . '</a></div>';

					$wp_admin_bar->add_node( array(
						'parent' => 'invites',
						'id'     => 'invitation-' . $group->id,
						'title'  => $title,
						'meta'   => array(
							'class' => 'nav-content-item nav-invitation'
						)
					) );
				}

				$group_counter++;
			}
		} else {
			// The user has no group invites
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'group-invites-none',
				'title'  => 'No new invitations',
				'meta'   => array(
					'class' => 'nav-no-items'
				)
			) );
		}

	}

	/**
	 * Add the Messages menu
	 */
	function add_messages_menu( $wp_admin_bar ) {
		if ( ! bp_is_active( 'messages' ) ) {
			return;
		}

		$total_count = bp_get_total_unread_messages_count();
		$wp_admin_bar->add_menu( array(
			'id' => 'messages',
			'title' => '<span class="toolbar-item-name">Messages </span><span class="toolbar-item-count count-' . $total_count . '">' . $total_count . '</span>',
		) );

		// Only show the first 5
		$messages_counter = 0;

		$messages_args = array(
			'type' => 'unread'
		);
		if ( bp_has_message_threads( $messages_args ) ) {
			global $messages_template;

			while ( bp_message_threads() ) {
				bp_message_thread();

				if ( $messages_counter < 5 ) {
					// avatar
					$title = '<div class="item-avatar"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '">' . bp_get_message_thread_avatar() . '</a></div>';

					$title .= '<div class="item">';

					// subject
					$title .= '<div class="item-title"><a href="' . bp_get_message_thread_view_link() . '">' . bp_create_excerpt(bp_get_message_thread_subject(), 30) . '</a></div>';

					// last sender
					$title .= '<div class="last-sender"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '">' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '</a></div>';

					// date and time
					$title .= bp_format_time( strtotime( $messages_template->thread->last_message_date ) );

					// Message excerpt
					$title .= '<p class="message-excerpt">' . strip_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 75 ) ) . ' <a class="message-excerpt-see-more" href="' . bp_get_message_thread_view_link() . '">See More</a></p>';

					$title .= '</div>'; // .item

					$wp_admin_bar->add_node( array(
						'parent' => 'messages',
						'id'     => 'message-' . bp_get_message_thread_id(),
						'title'  => $title,
						'meta'   => array(
							'class' => 'nav-content-item nav-message'
						)
					) );
				}

				$messages_counter++;
			}

		} else {
			// The user has no unread messages
			$wp_admin_bar->add_node( array(
				'parent' => 'messages',
				'id'     => 'messages-none',
				'title'  => 'No new messages.',
				'meta'   => array(
					'class' => 'nav-no-items'
				)
			) );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'messages',
			'id'     => 'messages-more',
			'title'  => 'See All Messages',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() )
		) );
	}

	/**
	 * Add the Activity menu (My Group activity)
	 */
	function add_activity_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'activity',
			'title' => '<span class="toolbar-item-name">Activity </span>'
		) );

		$activity_args = array(
			'user_id' => bp_loggedin_user_id(),
			'scope'   => 'groups',
			'max'     => 5
		);

		if ( bp_has_activities( $activity_args ) ) {
			while ( bp_activities() ) {
				bp_the_activity();

				// avatar
				$title = '<div class="item-avatar"><a href="' . bp_get_activity_user_link() . '">' . bp_get_activity_avatar() . '</a></div>';

				// action
				$title .= '<div class="item">' . bp_get_activity_action() . '</div>';

				$wp_admin_bar->add_node( array(
					'parent' => 'activity',
					'id'     => 'activity-' . bp_get_activity_id(),
					'title'  => $title,
					'meta'   => array(
						'class' => 'nav-content-item nav-activity'
					)
				) );
			}
		}

		$link = trailingslashit( bp_loggedin_user_domain() . bp_get_activity_slug() );
		if ( bp_is_active( 'groups' ) ) {
			$link .= trailingslashit( bp_get_groups_slug() );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'activity',
			'id'     => 'activity-more',
			'title'  => 'See All Activity',
			'href'   => $link,
		) );
	}

	/**
	 * Remove + > User
	 */
	public function remove_adduser( $wp_admin_bar ) {
		$wp_admin_bar->remove_menu( 'new-user' );
	}

	/**
	 * Add a 'Log Out' link to the far right
	 */
	function add_logout_item( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'top-secondary',
			'id'     => 'top-logout',
			'href'   => add_query_arg( 'redirect_to', bp_get_root_domain(), wp_logout_url() ),
			'title'  => 'Log Out'
		) );
	}

	/**
	 * Fix the logout redirect
	 */
	function fix_logout_redirect( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id'     => 'logout',
			'href'   => add_query_arg( 'redirect_to', bp_get_root_domain(), wp_logout_url() )
		) );
	}

	/**
	 * Adds the Sign Up item
	 */
	function add_signup_item( $wp_admin_bar ) {
		// Remove so we can replace in the right order
		$signup = $wp_admin_bar->get_node( 'bp-register' );
		$login  = $wp_admin_bar->get_node( 'bp-login' );

		$wp_admin_bar->remove_node( 'bp-register' );
		$wp_admin_bar->remove_node( 'bp-login' );

		// Change the title of the signup node
		$signup->title = 'Sign Up';

		// Move them both to top-secondary, to appear at the right
		$signup->parent = 'top-secondary';
		$login->parent  = 'top-secondary';

		// Re-add
		$wp_admin_bar->add_node( (array) $signup );
		$wp_admin_bar->add_node( (array) $login );
	}

	function fix_tabindex( $wp_admin_bar ) {
		//var_Dump( $wp_admin_bar );
		$wp_admin_bar->add_menu( array(
			'id'    => 'bp-login',
			'meta'	=> array(
				'tabindex' => 101
			)
		) );

		$wp_admin_bar->add_menu( array(
			'id'    => 'bp-register',
			'meta'	=> array(
				'tabindex' => 100
			)
		) );
	}

	function body_class( $body_class ) {
		if ( bp_is_root_blog() ) {
			$body_class[] = 'openlab-main';
		} else {
			$body_class[] = 'openlab-member';
		}

		return $body_class;
	}

	function admin_body_class( $body_class ) {
		if ( bp_is_root_blog() ) {
			$body_class .= ' openlab-main ';
		} else {
			$body_class .= ' openlab-member ';
		}

		return $body_class;
	}

	function enqueue_styles() {
		$url = WP_CONTENT_URL . '/mu-plugins/css/openlab-toolbar.css';
		$url = set_url_scheme( $url );
		wp_enqueue_style( 'openlab-toolbar', $url );
	}
}

// Themes like TwentyTen don't use jQuery by default, so let's enqueue it!
// added by r-a-y (05.16.11)
function cac_adminbar_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'cac_adminbar_enqueue_scripts' );

/**
 * JS to toggle adminbar login form
 *
 * JS is inline to reduce a server request
 */
function cac_adminbar_js() {
	$request_uri = $_SERVER['REQUEST_URI'];
?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {

		var loginform = '<form name="login-form" style="display:none;" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( "wp-login.php", "login_post" ) ?>" method="post"><label><?php _e( "Username", "buddypress" ) ?><br /><input type="text" name="log" id="sidebar-user-login" class="input" value="" /></label><label><?php _e( "Password", "buddypress" ) ?><br /><input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label><p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( "Keep Me Logged In", "buddypress" ) ?></label></p><input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain() . $request_uri; ?>" /><input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e("Log In"); ?>" tabindex="100" /><a href="<?php echo wp_lostpassword_url(); ?>" class="lost-pw">Forgot Password?</a></form>';

		$("#wp-admin-bar-bp-login").append(loginform);

		$("#wp-admin-bar-bp-login > a").click(function(){
			$("#sidebar-login-form").toggle();
			$("#sidebar-user-login").focus();
			$(this).toggleClass("login-click");
			return false;
		});
	});
	</script>

<?php
}
add_action( 'wp_footer', 'cac_adminbar_js', 999 );


?>
