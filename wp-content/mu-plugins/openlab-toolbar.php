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
	function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Admin_Bar;
		}
	}

	function __construct() {
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

			// Add the notification menus
			add_action( 'admin_bar_menu', array( $this, 'add_invites_menu' ), 22 );
			add_action( 'admin_bar_menu', array( $this, 'add_messages_menu' ), 24 );
			add_action( 'admin_bar_menu', array( $this, 'add_activity_menu' ), 26 );

			add_action( 'admin_bar_menu', array( $this, 'maybe_remove_thisblog' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'add_logout_item' ), 9999 );
			add_action( 'admin_bar_menu', array( $this, 'fix_logout_redirect' ), 10000 );
		} else {
			add_action( 'admin_bar_menu', array( $this, 'add_signup_item' ), 11 );
			add_action( 'admin_bar_menu', array( $this, 'fix_tabindex' ), 999 );
		}
	}

 	/**
 	 * Add the main OpenLab menu
 	 */
 	function add_network_menu( $wp_admin_bar ) {
 		$wp_admin_bar->add_node( array(
 			'parent' => 'top-secondary',
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
			'href'   => trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() )
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
			'href'   => trailingslashit( bp_get_root_domain() . '/help' )
 		) );
 	}

 	/**
 	 * Adds 'My OpenLab' menu
 	 */
 	function add_my_openlab_menu( $wp_admin_bar ) {
 		$wp_admin_bar->add_node( array(
			'id'    => 'my-openlab',
			'title' => 'My OpenLab',
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
			'href'   => trailingslashit( bp_loggedin_user_domain() . 'my-courses' )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-projects',
			'title'  => 'My Projects',
			'href'   => trailingslashit( bp_loggedin_user_domain() . 'my-projects' )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-clubs',
			'title'  => 'My Clubs',
			'href'   => trailingslashit( bp_loggedin_user_domain() . 'my-clubs' )
		) );

		// @todo This will need to be conditional, and we'll need to be dynamic about
		// href generation. But not strategicially dynamic
		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-portfolio',
			'title'  => 'My Portfolio',
			'href'   => bp_loggedin_user_domain()
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-friends',
			'title'  => 'My Friends',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-messages', // @todo Unread message count
			'title'  => 'My Messages',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-invitations', // @todo Invitations count
			'title'  => 'My Invitations',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' )
		) );

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
			'href'   => trailingslashit( bp_loggedin_user_domain() . 'my-courses' ) // @todo Where does this go?
		) );
	}

	/**
	 * Remove the Notifications menu
	 *
	 * We have to do it in a function like this because of the way BP adds the menu in the first
	 * place
	 */
	function remove_notifications_hook( $wp_admin_bar ) {
		remove_action( 'bp_setup_admin_bar', 'bp_members_admin_bar_notifications_menu', 5 );
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
		$wp_admin_bar->add_menu( array(
			'id' => 'invites',
			'title' => 'Invitations',
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

		$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
		$members_args = array(
			'include' => implode( ',', array_slice( $request_ids, 0, 3 ) ),
			'max'     => 0
		);

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

			if ( 3 < count( $request_ids ) ) {
				// "See More"
				$wp_admin_bar->add_node( array(
					'parent' => 'invites',
					'id'     => 'friend-requests-more',
					'title'  => 'See More',
					'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests' )
				) );
			}

		} else {
			// The user has no friend requests
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'friend-requests-none',
				'title'  => 'None', // @todo - What should this say?
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

		$groups_args = array(
			'type'    => 'invites',
			'user_id' => bp_loggedin_user_id()
		);
		if ( bp_has_groups( $groups_args ) ) {
			$group_counter = 0;
			while ( bp_groups() ) {
				bp_the_group();

				if ( $group_counter < 3 ) {
					// avatar
					$title = '<div class="item-avatar"><a href="' . bp_get_group_permalink() . '">' . bp_get_group_avatar( 'type=thumb&width=50&height=50' ) . '</a></div>';

					// name link
					$title .= '<div class="item"><div class="item-title"><a href="' . bp_get_group_permalink() . '">' . bp_get_group_name() . '</a></div></div>';

					// accept/reject buttons
					$title .= '<div class="action"><a class="button accept" href="' . bp_get_group_accept_invite_link() . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="button reject" href="' . bp_get_group_reject_invite_link() . '">' . __( 'Reject', 'buddypress' ) . '</a></div>';

					$wp_admin_bar->add_node( array(
						'parent' => 'invites',
						'id'     => 'invitation-' . bp_get_group_id(),
						'title'  => $title,
						'meta'   => array(
							'class' => 'nav-content-item nav-invitation'
						)
					) );
				}

				$group_counter++;
			}

			if ( 3 < $group_counter ) {
				// "See More"
				$wp_admin_bar->add_node( array(
					'parent' => 'invites',
					'id'     => 'invitations-more',
					'title'  => 'See More',
					'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' )
				) );
			}
		} else {
			// The user has no friend requests
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'friend-requests-none',
				'title'  => 'None', // @todo - What should this say?
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
		$wp_admin_bar->add_menu( array(
			'id' => 'messages',
			'title' => 'Messages',
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
					$title .= '<div class="item-title"><a href="' . bp_get_message_thread_view_link() . '">' . bp_get_message_thread_subject() . '</a></div>';

					// last sender
					$title .= '<div class="last-sender"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '">' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '</a></div>';

					// date and time
					$last_time_unix = strtotime( $messages_template->thread->last_message_date );
					$last_date = date( 'M n', $last_time_unix ); // 'Apr 6'
					$last_time = date( 'g:i a', $last_time_unix ); // '3:40 pm'
					$title .= sprintf( '%1$s at %2$s', $last_date, $last_time );

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
				'title'  => 'None', // @todo - What should this say?
				'meta'   => array(
					'class' => 'nav-no-items'
				)
			) );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'messages',
			'id'     => 'messages-more',
			'title'  => 'Go to Inbox',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() )
		) );
	}

	/**
	 * Add the Activity menu (My Group activity)
	 */
	function add_activity_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'activity',
			'title' => 'Activity',
		) );
	}

	/**
	 * Add a 'Log Out' link to the far right
	 */
	function add_logout_item( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'top-secondary',
			'id'     => 'top-logout',
			'href'   => add_query_arg( 'redirect_to', wp_guess_url(), wp_logout_url() ),
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

		var loginform = '<form name="login-form" style="display:none;" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( "wp-login.php", "login_post" ) ?>" method="post"><label><?php _e( "Username", "buddypress" ) ?><br /><input type="text" name="log" id="sidebar-user-login" class="input" value="" /></label><label><?php _e( "Password", "buddypress" ) ?><br /><input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label><p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( "Remember Me", "buddypress" ) ?></label></p><input type="hidden" name="redirect_to" value="<?php bp_get_root_domain() . $request_uri; ?>" /><input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e("Log In"); ?>" tabindex="100" /><input type="hidden" name="testcookie" value="1" /><a href="<?php echo wp_lostpassword_url(); ?>" class="lost-pw">Forgot Password?</a><input type="hidden" name="redirect_to" value="<?php echo wp_guess_url() ?>" /></form>';

		$("#wp-admin-bar-bp-login").append(loginform);

		$("#wp-admin-bar-bp-login > a").click(function(){
			$("#sidebar-login-form").toggle();
			$("#sidebar-user-login").focus();
			$(this).toggleClass("login-click");
			return false;
		});
	});
	</script>

	<style type="text/css">
/* adminbar login form
----------------------------------------------------------------------------------------
- adding some default BP form styles so the form will look accurate across all WP blogs */
li.wp-admin-bar-bp-login {position:relative;}

#wpadminbar ul li#wp-admin-bar-bp-login {
	width: 70px;
}

form#sidebar-login-form {position:absolute; background:url(<?php echo get_stylesheet_directory_uri() . '/images/bg_trans.png' ?>) repeat 0 0 !important; width:200px; padding:10px; border-radius:0 8px 8px 8px; -moz-border-radius:0 8px 8px 8px; -webkit-border-radius:0 8px 8px 8px; box-shadow:0px 3px 3px #999; display:none; margin-left: -155px; text-align: left;}
	li.bp-login:hover form {display:block; cursor:default;}

form#sidebar-login-form label, form#sidebar-login-form span.label {display:block; color:#fff; margin:5px 0;}

#wpadminbar form#sidebar-login-form textarea, #wpadminbar form#sidebar-login-form input[type="text"],
#wpadminbar form#sidebar-login-form select, #wpadminbar form#sidebar-login-form input[type="password"],
#wpadminbar form#sidebar-login-form input[type="submit"]
	{border:1px inset #ccc; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; color: #888; text-shadow: none;}

#sidebar-login-form input[type="text"], #sidebar-login-form input[type="password"] {padding:4px; width:95%;}

form#sidebar-login-form input[type="submit"] {padding:3px 10px; text-decoration:none; vertical-align:bottom; border:1px solid #ddd; cursor:pointer; text-shadow: none;}

input#sidebar-rememberme {margin-left:0;}

form#sidebar-login-form p.forgetmenot {float:none;}

form#sidebar-login-form a.lost-pw {padding-right:2px !important; float:right; font-size:.9em !important; background:none !important;}
	#wp-admin-bar li a.lost-pw:hover {text-decoration:underline !important; background:none !important;}

form#sidebar-login-form #sidebar-wp-submit {background:#fff;}

.login-click {background:#000 !important;}
	</style>
<?php
}
add_action( 'wp_footer', 'cac_adminbar_js', 999 );


?>