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

			add_action( 'admin_bar_menu', array( $this, 'change_howdy_to_my_commons' ), 7 );
			add_action( 'admin_bar_menu', array( $this, 'prepend_my_to_my_commons_items' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'move_notifications_hook' ), 5 );

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
	 * Change 'Howdy' message to 'Hi'
	 */
	function change_howdy_to_my_commons( $wp_admin_bar ) {
		global $bp;
		$wp_admin_bar->add_node( array(
			'id'        => 'my-account',
			'title'     => sprintf( "Hi, %s", $bp->loggedin_user->userdata->display_name ),
			'meta'	    => array()
		) );
	}

	/**
	 * Add 'My' to the front of the My Commons items
	 */
	function prepend_my_to_my_commons_items( $wp_admin_bar ) {
		$nodes = $wp_admin_bar->get_nodes();
		$my_commons_node_ids = array();

		foreach( $nodes as $id => $node ) {
			if ( 'my-account-buddypress' == $node->parent ) {
				$wp_admin_bar->add_node( array(
					'id'    => $id,
					'title' => 'My ' . $node->title
				) );
			}
		}
	}

	/**
	 * Move the Notifications menu to a different hook
	 *
	 * We have to do it in a function like this because of the way BP adds the menu in the first
	 * place
	 */
	function move_notifications_hook( $wp_admin_bar ) {
		remove_action( 'bp_setup_admin_bar', 'bp_members_admin_bar_notifications_menu', 5 );
		add_action( 'admin_bar_menu', 'bp_members_admin_bar_notifications_menu', 999 );

		add_action( 'admin_bar_menu', array( $this, 'move_notifications_from_secondary' ), 998 );
	}

	/**
	 * Move the Notifications menu out of the secondary section
	 */
	function move_notifications_from_secondary( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id'     => 'bp-notifications',
		) );
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