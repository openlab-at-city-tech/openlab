<?php
/**
 * OpenLab implementation of the WP toolbar
 */

/**
 * Bootstrap
 */
add_action( 'add_admin_bar_menus', array( 'OpenLab_Admin_Bar', 'init' ) );

class OpenLab_Admin_Bar {

	public static function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Admin_Bar();
		}
	}

	public function __construct() {
		// Bail if BP is not present
		if ( ! class_exists( 'BP_Core' ) ) {
			return;
		}

		// Removes the WP logo menu item.
		remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );

		// Add OpenLab logo link.
		add_action( 'admin_bar_menu', array( $this, 'add_openlab_logo_link' ), 1 );

		// Customize my-account.
		remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account', 0 );
		add_action( 'admin_bar_menu', [ $this, 'my_account' ], 0 );

		// Don't let BP load its admin bar.
		remove_action( 'admin_bar_menu', 'bp_setup_admin_bar', 20 );

		// For cleaning up any plugin add-ons.
		add_action( 'wp_before_admin_bar_render', array( $this, 'adminbar_plugin_cleanup' ), 9999 );

		// Logged-in only
		if ( is_user_logged_in() ) {

			add_action( 'admin_bar_menu', array( $this, 'change_howdy_to_hi' ), 9999999 );

			add_action( 'admin_bar_menu', array( $this, 'remove_notifications_hook' ), 5 );

			// Don't show the My Sites menu
			remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );

			// Don't show the My Achievements menu item.
			remove_action( 'admin_bar_menu', 'dpa_admin_bar_menu' );

			add_action( 'admin_bar_menu', array( $this, 'maybe_remove_thisblog' ), 99 );

			remove_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 );

			// for cleanning up any plugin add ons
			add_action( 'wp_before_admin_bar_render', array( $this, 'adminbar_plugin_cleanup' ), 9999 );
		} else {
			add_action( 'admin_bar_menu', [ $this, 'add_sign_in_menu' ], 1 );
		}
	}

	public function remove_bp_admin_bar_styles( $styles ) {
		return array();
	}

	/**
	 * Add the main OpenLab logo link.
	 */
	public function add_openlab_logo_link( $wp_admin_bar ) {
		ob_start();
		include WPMU_PLUGIN_DIR . '/parts/persistent/svg-logo.php';
		$openlab_logo = ob_get_clean();

		$title = "<span class='screen-reader-text'>OpenLab at City Tech</span> <span class='logo-wrapper'>$openlab_logo</span>";

		$wp_admin_bar->add_node(
			array(
				/*'parent' => 'top-secondary',*/
				'id'    => 'openlab',
				'title' => $title,
				'href'  => bp_get_root_domain(),
				'meta'  => array(
					'tabindex' => 90,
					'class'    => 'admin-bar-menu admin-bar-menu-openlab-logo hidden-xs',
				),
			)
		);
	}

	/**
	 * Adds the Sign In menu.
	 */
	public function add_sign_in_menu( $wp_admin_bar ) {
		ob_start();
		include WPMU_PLUGIN_DIR . '/parts/persistent/svg-logo-notext.php';
		$openlab_logo = ob_get_clean();

		$my_openlab_logo_url = home_url( 'wp-content/mu-plugins/img/my-openlab-icon.png' );
		$openlab_logo_url    = home_url( 'wp-content/mu-plugins/img/openlab-logo-notext.png' );

		$title = "<span>Sign In</span> <img class='openlab-logo hidden-xs' src='$openlab_logo_url' alt='OpenLab at City Tech' /><img class='my-openlab-logo visible-xs' src='$my_openlab_logo_url' alt='OpenLab at City Tech' />";

		$wp_admin_bar->add_node(
			array(
				'id'    => 'openlab-sign-in',
				'title' => $title,
				'href'  => '#',
				'meta'  => array(
					'class' => 'ab-top-secondary',
				),
			)
		);

		$wp_admin_bar->add_group(
			array(
				'parent' => 'openlab-sign-in',
				'id'     => 'openlab-sign-in-actions',
			)
		);

		$info_title = sprintf(
			'<div class="openlab-sign-in-info-container">
				<div class="openlab-sign-in-info-logo"><img src="%s" alt="OpenLab at City Tech" /></div>
				<div class="openlab-sign-in-info-text">
					<div class="openlab-sign-in-info-sitename">OpenLab at City Tech</div>
					<div class="openlab-sign-in-info-tagline">A place to learn, work, and share</div>

					<div class="openlab-sign-in-info-signin">
						<a href="%s">Sign In</a>
					</div>

					<div class="openlab-sign-up-info-sign-up">
						Need an account? <a href="%s">Sign Up</a>
					</div>
				</div>
			</div>',
			$openlab_logo_url,
			wp_login_url(),
			bp_get_signup_page()
		);

		$wp_admin_bar->add_node(
			[
				'parent' => 'openlab-sign-in-actions',
				'id'     => 'openlab-sign-in-info',
				'title'  => false,
				'meta'   => array(
					'class' => 'openlab-sign-in-info',
					'html'  => $info_title,
				),
			]
		);
	}

	/**
	 * Change 'Howdy' message to 'Hi'
	 */
	public function change_howdy_to_hi( $wp_admin_bar ) {
		$wp_admin_bar->add_node(
			array(
				'id'    => 'my-account',
				'title' => sprintf( '<span class="hi-username">Hi, %s</span>', bp_get_loggedin_user_fullname() ),
				'meta'  => array(
					'class' => 'user-display-name',
				),
			)
		);
	}

	/**
	 * Add the My Account menu.
	 */
	public function my_account( $wp_admin_bar ) {
		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();

		if ( ! $user_id ) {
			return;
		}

		$my_openlab_url = bp_members_get_user_url( bp_loggedin_user_id() );

		$user_avatar = get_avatar( $user_id, 64 );
		$user_name   = bp_get_loggedin_user_fullname();

		$wp_admin_bar->add_group(
			array(
				'parent' => 'my-account',
				'id'     => 'user-actions',
			)
		);

		$user_info = sprintf(
			'<span class="user-avatar">%s</span><span class="username">%s</span>',
			$user_avatar,
			$user_name
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'user-actions',
				'id'     => 'user-info',
				'title'  => $user_info,
				'href'   => $my_openlab_url,
			)
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'user-actions',
				'id'     => 'my-openlab-link',
				'title'  => 'My OpenLab',
				'href'   => $my_openlab_url,
			)
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'user-actions',
				'id'     => 'my-account-logout-link',
				'title'  => 'Sign Out',
				'href'   => wp_logout_url( bp_get_root_domain() ),
			)
		);
	}

	/**
	 * Remove the Notifications menu.
	 *
	 * We have to do it in a function like this because of the way BP adds the menu in the first
	 * place.
	 */
	public function remove_notifications_hook( $wp_admin_bar ) {
		remove_action( 'admin_bar_menu', 'bp_members_admin_bar_notifications_menu_priority', 6 );
	}

	/**
	 * Maybe remove the current blog menu.
	 */
	public function maybe_remove_thisblog( $wp_admin_bar ) {
		if ( ! current_user_can( 'publish_posts' ) ) {
			$wp_admin_bar->remove_node( 'site-name' );
		}
	}

	/**
	 * Miscellaneous button cleanup.
	 *
	 * @param type $wp_admin_bar
	 */
	public function adminbar_plugin_cleanup( $wp_admin_bar ) {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'tribe-events' );
		$wp_admin_bar->remove_menu( 'openlab-favorites' );
		$wp_admin_bar->remove_menu( 'enable-jquery-migrate-helper' );
		$wp_admin_bar->remove_menu( 'new-user' );
		$wp_admin_bar->remove_menu( 'ngg-menu' );
		$wp_admin_bar->remove_menu( 'duplicate-post' );
		$wp_admin_bar->remove_menu( 'new-draft' );

		$wp_admin_bar->remove_menu( 'search' );
		$wp_admin_bar->remove_menu( 'logout' );
	}
}

/**
 * Moved login form so that is injected via a localized variable
 * Allows for additional interaction in openlab.nav.js
 * Moved markup to separate template for easier editing
 */
function openlab_get_loginform() {
	$form_out = '';

	$request_uri = openlab_sanitize_url_params( $_SERVER['REQUEST_URI'] );

	ob_start();
	include WPMU_PLUGIN_DIR . '/parts/persistent/loginform.php';
	$form_out = ob_get_clean();

	return $form_out;
}

/**
 * Prevent get_blogs_of_user() from being invoked during admin bar initialization.
 *
 * get_blogs_of_user() can be very costly for accounts (mostly test accounts)
 * that have large numbers of blogs.
 *
 * @param array $pre The value to return instead of the actual value.
 * @return array
 */
function openlab_short_circuit_user_blog_queries( $pre ) {
	$allow_query = true;

	$backtrace = debug_backtrace();
	foreach ( $backtrace as $trace ) {
		if ( ! empty( $trace['class'] ) && 'WP_Admin_Bar' === $trace['class'] && ! empty( $trace['function'] ) && 'initialize' === $trace['function'] ) {
			$allow_query = false;
			break;
		}
	}

	if ( ! $allow_query ) {
		return [];
	}

	return $pre;
}
add_filter( 'pre_get_blogs_of_user', 'openlab_short_circuit_user_blog_queries' );
