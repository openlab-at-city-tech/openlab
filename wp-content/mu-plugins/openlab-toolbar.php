<?php

/**
 * OpenLab implementation of the WP toolbar
 */

/**
 * Ensure that the toolbar always shows
 */
add_filter( 'show_admin_bar', '__return_true', 999999 );

/**
 * Removing the default WP admin bar styles; a customized version of the default styles can now be found
 * in the mu-plugins folder
 * This is done for two reasons:
 * 
 *  1) The current default css uses a an all selector ('*') to apply a number of default styles that completely
 *     inhibit the ability to inject Bootstrap into the admin, and make custom responsive styling an immense challenge
 *  2) Because this admin bar is now highly customized, we do not want future releases of WP to upset those customizations
 *     without proper vetting
 * 
 * Note: in addition to removing the all selector styles, there are also a number of customizations to the custom default admin
 * bar css, to reduce overall styling overhead
 * 
 * @param type $styles
 */
function openlab_remove_admin_bar_default_css($styles) {
    $styles->remove('admin-bar');
}

add_action('wp_default_styles', 'openlab_remove_admin_bar_default_css', 99999);

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
                
                //taking out the admin network menu for now
		//add_action( 'admin_bar_menu', array( $this, 'add_network_menu' ), 1 );

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
                        //creating custom menus for comments, new content, and editing
                        if (!is_network_admin() && !is_user_admin()) {
                            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
                            remove_action('admin_bar_menu', 'wp_admin_bar_new_content_menu', 70);
                            add_action('admin_bar_menu',array($this,'add_custom_comments_menu'), 60);
                            add_action('admin_bar_menu',array($this,'add_custom_content_menu'), 70);
                        }
                        
                        remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
                        add_action('admin_bar_menu',array($this,'add_custom_edit_menu'),80);
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
			'href'   => bp_loggedin_user_domain(),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-courses',
			'title'  => 'My Courses',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-courses' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-projects',
			'title'  => 'My Projects',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-projects' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-clubs',
			'title'  => 'My Clubs',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-clubs' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item'
                        )
		) );

		// Only show a My Portfolio link for users who actually have one
		$portfolio_url = openlab_get_user_portfolio_url( bp_loggedin_user_id() );
		if ( !empty( $portfolio_url ) ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-portfolio',
				'title'  => sprintf( 'My %s', openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() ) ),
				'href'   => $portfolio_url,
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
			) );
		}

		if ( bp_is_active( 'friends' ) ) {
			$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
			$request_count = count( $request_ids );
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-friends',
				'title'  => sprintf( 'My Friends <span class="toolbar-item-count count-' . $request_count . ' pull-right">%d</span>', $request_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
			) );
		}

		if ( bp_is_active( 'messages' ) ) {
			$messages_count = bp_get_total_unread_messages_count();
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-messages',
				'title'  => sprintf( 'My Messages <span class="toolbar-item-count count-' . $messages_count . ' pull-right">%d</span>', $messages_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
			) );
		}

		if ( bp_is_active( 'groups' ) ) {
			$invites = groups_get_invites_for_user();
			$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-invitations',
				'title'  => sprintf( 'My Invitations <span class="toolbar-item-count count-' . $invite_count . ' pull-right">%d</span>', $invite_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
			) );
		}

		// My Dashboard points to the my-sites.php Dashboard panel for this user. However,
		// this panel only works if looking at a site where the user has Dashboard-level
		// permissions. So we have to find a valid site for the logged in user.
		$primary_site_id = get_user_meta( bp_loggedin_user_id(), 'primary_blog', true );
		$primary_site_url = get_blog_option( $primary_site_id, 'siteurl' );

		if ( !empty( $primary_site_id ) && !empty( $primary_site_url ) ) {

			// @todo Do we really want this kind of separator?
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-openlab-separator',
				'title'  => '-----------',
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
			) );

			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-dashboard',
				'title'  => 'My Dashboard',
				'href'   => $primary_site_url . '/wp-admin/my-sites.php',
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item'
                                )
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
			'title' => '<span class="toolbar-item-icon fa fa-user"></span><span class="toolbar-item-count sub-count count-' . $total_count . '">' . $total_count . '</span>',
		) );

		/**
		 * FRIEND REQUESTS
		 */

		// "Friend Requests" title
		$wp_admin_bar->add_node(array(
                    'parent' => 'invites',
                    'id' => 'friend-requests-title',
                    'title' => 'Friend Requests',
                    'meta' => array(
                        'class' => 'submenu-title bold'
                    )
                ));

        $members_args = array(
			'max'     => 0
		);

		$members_args['include'] = ! empty( $request_ids ) ? implode( ',', array_slice( $request_ids, 0, 3 ) ) : '0';

		if ( bp_has_members( $members_args ) ) {
			while ( bp_members() ) {
				bp_the_member();
                                
				// avatar
				$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_member_link() . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.bp_get_member_name().'"/></a></div></div>';

				// name link
				$title .= '<div class="col-sm-18"><p class="item"><a class="bold" href="' . bp_get_member_link() . '">' . bp_get_member_name() . '</a></p>';

				// accept/reject buttons
				$title .= '<p class="actions clearfix"><a class="btn btn-primary link-btn accept" href="' . bp_get_friend_accept_request_link() . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="btn btn-default link-btn reject" href="' . bp_get_friend_reject_request_link() . '">' . __( 'Reject', 'buddypress' ) . '</a></p></div></div>';

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
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new friendship requests.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-no-items nav-content-item'
				)
			) );
		}

		/**
		 * INVITATIONS
		 */
                
                $title = 'Invitations';
                if ( !empty( $invites['groups'] )){
                    $title .= '<span class="see-all pull-right"><a class="regular" href="'.trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug()) . '/invites">See All Invites</a></span>';
                }
		// "Invitations" title
		$wp_admin_bar->add_node( array(
			'parent' => 'invites',
			'id'     => 'invitations-title',
			'title'  => $title,
                        'meta' => array(
                            'class' => 'submenu-title bold'
                        )
		) );

		$groups_args = array(
			'type'    => 'invites',
			'user_id' => bp_loggedin_user_id()
		);

		if ( !empty( $invites['groups'] ) ) {
			$group_counter = 0;
			foreach ( (array) $invites['groups'] as $group ) {
				if ( $group_counter < 3 ) {
					// avatar
					$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_group_permalink( $group ) . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => $group->id, 'object' => 'group', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '. $group->name.'"/></a></div></div>' ;

					// name link
					$title .= '<div class="col-sm-18"><p class="item-title"><a class="bold" href="' . bp_get_group_permalink( $group ) . '">' . $group->name . '</a></p>';

					// accept/reject buttons
					$title .= '<p class="actions clearfix"><a class="btn btn-primary link-btn accept" href="' . bp_get_group_accept_invite_link( $group ) . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="btn btn-default link-btn reject" href="' . bp_get_group_reject_invite_link( $group ) . '">' . __( 'Reject', 'buddypress' ) . '</a></p></div></div>';

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
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new invitations.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-no-items nav-content-item'
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
			'title' => '<span class="toolbar-item-icon fa fa-envelope"></span><span class="toolbar-item-count sub-count count-' . $total_count . '">' . $total_count . '</span>',
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
					$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => $messages_template->thread->last_sender_id, 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.$messages_template->thread->last_sender_id.'"/></a></div></div>';

					// subject
					$title .= '<div class="col-sm-18"><p class="item"><a class="bold" href="' . bp_get_message_thread_view_link() . '">' . bp_create_excerpt(bp_get_message_thread_subject(), 30) . '</a>';

					// last sender
					$title .= '<span class="last-sender"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '">' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '</a></span></p>';

					// date and time
					$title .= '<p class="message-excerpt">' .bp_format_time( strtotime( $messages_template->thread->last_message_date ) ).'<br />';

					// Message excerpt
					$title .= strip_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 75 ) ) . ' <a class="message-excerpt-see-more" href="' . bp_get_message_thread_view_link() . '">See More</a></p></div></div>';

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
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new messages.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-content-item nav-no-items'
				)
			) );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'messages',
			'id'     => 'messages-more',
			'title'  => 'See All Messages',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() ),
                        'meta' => array(
                            'class' => 'menu-bottom-link'
                        )
		) );
	}

	/**
	 * Add the Activity menu (My Group activity)
	 */
	function add_activity_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'activity',
			'title' => '<span class="toolbar-item-name fa fa-bullhorn"></span>'
		) );

		$activity_args = array(
			'user_id' => bp_loggedin_user_id(),
			'scope'   => 'groups',
			'max'     => 5
		);

		if ( bp_has_activities( $activity_args ) ) {
                    global $activities_template;
			while ( bp_activities() ) {
				bp_the_activity();

				// avatar
				$title = '<div class="row activity-row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_activity_user_link() . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => bp_get_activity_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.  bp_get_activity_user_id().'"/></a></div></div>';

				// action
				$title .= '<div class="col-sm-18">';
                                
                                //the things we do...
                                $action_output = '';
                                $action_output_raw = $activities_template->activity->action;
                                $action_output_ary = explode('<a',$action_output_raw);
                                $count = 0;
                                foreach ($action_output_ary as $action_redraw){
                                    if(!ctype_space($action_redraw)){
                                        $class = ($count == 0 ? 'activity-user' : 'activity-action');
                                        $action_output .= '<a class="'.$class.'"'.$action_redraw;
                                        $count++;
                                    }
                                }
                                
                                $title .= '<p class="item inline-links">'.$action_output.'</p>';
                                $title .= '<p class="item">'.bp_insert_activity_meta('').' ago</p>';
                                $title .= '</div></div>';

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
                        'meta' => array(
                                'class' => 'menu-bottom-link'
                            )
		) );
	}
        
        function add_custom_edit_menu( $wp_admin_bar ) {
                global $tag, $wp_the_query;

                if ( is_admin() ) {
                        $current_screen = get_current_screen();
                        $post = get_post();

                        if ( 'post' == $current_screen->base
                                && 'add' != $current_screen->action
                                && ( $post_type_object = get_post_type_object( $post->post_type ) )
                                && current_user_can( 'read_post', $post->ID )
                                && ( $post_type_object->public )
                                && ( $post_type_object->show_in_admin_bar ) )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'view',
                                        'title' => '<span class="fa fa-eye "></span>',
                                        'href' => get_permalink( $post->ID )
                                ) );
                        } elseif ( 'edit-tags' == $current_screen->base
                                && isset( $tag ) && is_object( $tag )
                                && ( $tax = get_taxonomy( $tag->taxonomy ) )
                                && $tax->public )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'view',
                                        'title' => '<span class="fa fa-eye "></span>',
                                        'href' => get_term_link( $tag )
                                ) );
                        }
                } else {
                        $current_object = $wp_the_query->get_queried_object();

                        if ( empty( $current_object ) )
                                return;

                        if ( ! empty( $current_object->post_type )
                                && ( $post_type_object = get_post_type_object( $current_object->post_type ) )
                                && current_user_can( 'edit_post', $current_object->ID )
                                && $post_type_object->show_ui && $post_type_object->show_in_admin_bar )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'edit',
                                        'title' => '<span class="fa fa-pencil"></span>',
                                        'href' => get_edit_post_link( $current_object->ID )
                                ) );
                        } elseif ( ! empty( $current_object->taxonomy )
                                && ( $tax = get_taxonomy( $current_object->taxonomy ) )
                                && current_user_can( $tax->cap->edit_terms )
                                && $tax->show_ui )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'edit',
                                        'title' => '<span class="fa fa-pencil"></span>',
                                        'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy )
                                ) );
                        }
                }
        }
        
    /**
     * Custom content menu
     * @param type $wp_admin_bar
     * @return type
     */
    function add_custom_content_menu($wp_admin_bar) {
        $actions = array();

	$cpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );

	if ( isset( $cpts['post'] ) && current_user_can( $cpts['post']->cap->create_posts ) )
		$actions[ 'post-new.php' ] = array( $cpts['post']->labels->name_admin_bar, 'new-post' );

	if ( isset( $cpts['attachment'] ) && current_user_can( 'upload_files' ) )
		$actions[ 'media-new.php' ] = array( $cpts['attachment']->labels->name_admin_bar, 'new-media' );

	if ( current_user_can( 'manage_links' ) )
		$actions[ 'link-add.php' ] = array( _x( 'Link', 'add new from admin bar' ), 'new-link' );

	if ( isset( $cpts['page'] ) && current_user_can( $cpts['page']->cap->create_posts ) )
		$actions[ 'post-new.php?post_type=page' ] = array( $cpts['page']->labels->name_admin_bar, 'new-page' );

	unset( $cpts['post'], $cpts['page'], $cpts['attachment'] );

	// Add any additional custom post types.
	foreach ( $cpts as $cpt ) {
		if ( ! current_user_can( $cpt->cap->create_posts ) )
			continue;

		$key = 'post-new.php?post_type=' . $cpt->name;
		$actions[ $key ] = array( $cpt->labels->name_admin_bar, 'new-' . $cpt->name );
	}
	// Avoid clash with parent node and a 'content' post type.
	if ( isset( $actions['post-new.php?post_type=content'] ) )
		$actions['post-new.php?post_type=content'][1] = 'add-new-content';

	if ( current_user_can( 'create_users' ) || current_user_can( 'promote_users' ) )
		$actions[ 'user-new.php' ] = array( _x( 'User', 'add new from admin bar' ), 'new-user' );

	if ( ! $actions )
		return;

	$title = '<span class="fa fa-plus-circle"></span>';

	$wp_admin_bar->add_menu( array(
		'id'    => 'new-content',
		'title' => $title,
		'href'  => admin_url( current( array_keys( $actions ) ) ),
		'meta'  => array(
			'title' => _x( 'Add New', 'admin bar menu group label' ),
		),
	) );

	foreach ( $actions as $link => $action ) {
		list( $title, $id ) = $action;

		$wp_admin_bar->add_menu( array(
			'parent'    => 'new-content',
			'id'        => $id,
			'title'     => $title,
			'href'      => admin_url( $link ),
                        'meta' => array(
                                'class' => 'admin-bar-menu-item'
                            )
		) );
	}
    }
    
    /**
     * Custom comments menu
     * @param type $wp_admin_bar
     * @return type
     */
    function add_custom_comments_menu($wp_admin_bar) {
        if (!current_user_can('edit_posts'))
            return;

        $awaiting_mod = wp_count_comments();
        $awaiting_mod = $awaiting_mod->moderated;
        $awaiting_title = esc_attr(sprintf(_n('%s comment awaiting moderation', '%s comments awaiting moderation', $awaiting_mod), number_format_i18n($awaiting_mod)));

        $icon = '<span class="fa fa-comment"></span>';
        $title = '<span id="ab-awaiting-mod" class="ab-label awaiting-mod pending-count toolbar-item-count sub-count count-' . $awaiting_mod . '">' . number_format_i18n($awaiting_mod) . '</span>';

        $wp_admin_bar->add_menu(array(
            'id' => 'comments',
            'title' => $icon . $title,
            'href' => admin_url('edit-comments.php'),
            'meta' => array('title' => $awaiting_title),
        ));
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
                //custom admin bar styles
                wp_enqueue_style( 'admin-bar-custom', WP_CONTENT_URL . '/mu-plugins/css/admin-bar-custom.css',array('font-awesome') );
		wp_enqueue_style( 'openlab-toolbar', WP_CONTENT_URL . '/mu-plugins/css/openlab-toolbar.css',array('font-awesome') );
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
