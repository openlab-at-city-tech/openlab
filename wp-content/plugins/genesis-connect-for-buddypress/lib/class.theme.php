<?php
/*
	This is the buddypress specific code to manage the buddypress content being added to the Genesis Framework
	Do not modify this file
*/
class GConnect_Theme {
	var $home = false;
	var $adminbar = true;
	var $stylesheet = '';
	var $front = null;
	var $admin = null;
	var $style = null;
	var $simple_menu = false;
	var $custom_subnav = null;
	var $settings_key = 'gconnect_settings';
	var $settings = null;

	function GConnect_Theme( $tp_active = false ) {
		$this->__construct( $tp_active );
	}
	function __construct( $tp_active = false ) {
		global $current_blog, $gsm_simple_menu;

		$this->home = ( !is_multisite() || defined( 'BP_ENABLE_MULTIBLOG' ) || $current_blog->blog_id == BP_ROOT_BLOG );
		$this->adminbar = !( bp_use_wp_admin_bar() || defined( 'BP_DISABLE_ADMIN_BAR' ) || ( get_site_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) );
		$this->stylesheet = get_option( 'stylesheet' );
		$this->settings = get_option( $this->settings_key );
		if( !( $this->style = genesis_get_option( 'style_selection' ) ) )
			$this->style = 'style.css';
		if( ( $this->simple_menu = !empty( $gsm_simple_menu ) ) )
			$this->custom_subnav = &$gsm_simple_menu;

		do_action( 'gconnect_before_init' );

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			require_once( GENESISCONNECT_DIR . 'lib/class.front.php' );
			$this->front = new GConnect_Front( $this, $tp_active );

		}
		if( is_admin() ) {
			require_once( GENESISCONNECT_DIR . 'lib/class.options.php' );
			$this->admin = new GConnect_Admin( $this, $tp_active );
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 20 );
		} elseif( ! $tp_active && ! is_user_logged_in() && $this->home ) {
			$custom_register = $this->get_option( 'custom_register' );
			if( $custom_register && 'none' != $custom_register ) {
				require_once( GENESISCONNECT_DIR . 'lib/class.visitor.php' );
				$this->front->set_visitor( new GConnect_visitor( $this, $custom_register ) );
			} elseif ( is_multisite() )
				add_action( 'wp', array( &$this, 'bp_core_wpsignup_redirect' ) );
			else
				add_action( 'init', array( &$this, 'bp_core_wpsignup_redirect' ) );
		}
		if( 'widget' == $this->get_option( 'before_content' ) ) {
			genesis_register_sidebar(array(
				'name'=>'Before BuddyPress',
				'description' => __( 'This is above the BuddyPress content', 'genesis-connect' ),
				'id' => 'gconnect-before'
			));
		}

		if( is_dir( GENESISCONNECT_DIR . 'child-theme/' . $this->stylesheet ) ) {
			$addon_directory = GENESISCONNECT_DIR . 'child-theme/' . $this->stylesheet;
			$addon_functions = $addon_directory . '/my-functions.php';
			if( is_file( $addon_functions ) )
				require_once( $addon_functions );
			if( !is_admin() )
				$this->front->set_addon( $addon_directory, GENESISCONNECT_URL . 'child-theme/' . $this->stylesheet );
		} elseif( !is_admin() )
			$this->front->set_addon( get_stylesheet_directory(), get_stylesheet_directory_uri() );
			
		do_action( 'gconnect_after_init' );
	}
	function is_home() {
		return $this->home;
	}
	function have_adminbar() {
		return $this->adminbar;
	}
	function child_name() {
		return $this->stylesheet;
	}
	function child_style() {
		return $this->style;
	}
	function do_custom_subnav() {
		return $this->simple_menu;
	}
	function get_option( $key ) {
		return apply_filters( 'gconnect_option_' . $key, empty( $this->settings[$key] ) ? false : $this->settings[$key] );
	}
	function bp_core_wpsignup_redirect() {
		if ( ( !isset( $_SERVER['SCRIPT_NAME'] ) || false === strpos( $_SERVER['SCRIPT_NAME'], 'wp-signup.php' ) ) && ( !isset( $_GET['action'] ) || $_GET['action'] != 'register' ) )
			return false;

		if ( gconnect_locate_template( array( 'registration/register.php' ), false ) || gconnect_locate_template( array( 'register.php' ), false ) )
			bp_core_redirect( bp_get_root_domain() . '/' . BP_REGISTER_SLUG . '/' );
	}
	function after_setup_theme() {
		if ( function_exists( 'bp_dtheme_ajax_querystring' ) )
			return;

		require_once( GENESISCONNECT_DIR . 'lib/ajax.php' );
		add_theme_support( 'buddypress' );
	}
}
global $gconnect_theme;
$gconnect_theme = new GConnect_Theme( function_exists( 'bp_tpack_loader' ) );
/*
 * Login Widget
 */
class GConnect_LoginWidget extends WP_Widget {
	function GConnect_LoginWidget() {
		$widget_ops = array( 'description' => __( 'Genesis Connect Login Widget', 'genesis-connect' ) );
		$this->WP_Widget( 'gc_login_widget', __( 'Genesis Connect Login Widget', 'genesis-connect' ), $widget_ops );
	}
	function widget( $args, $instance ) {
		global $gconnect_theme;
		if( !empty( $gconnect_theme->front ) )
			$gconnect_theme->front->sidebar();
	}
}

function register_gconnect_login_widget() {
	register_widget( 'GConnect_LoginWidget' ); // This adds the Widget to the backend
}
add_action( 'widgets_init', 'register_gconnect_login_widget' );
/*
 * add the BuddyPress navbar
 */
function gconnect_site_nav() {
	global $gconnect_theme;
	if( !empty( $gconnect_theme->front ) )
		return $gconnect_theme->front->site_nav();
}
/*
 *  add home page site activity body class
 */
function gconnect_body_class( $classes = array() ) {
	global $gconnect_theme;
	if( !empty( $gconnect_theme->front ) )
		return $gconnect_theme->front->body_class( $classes );
	return $classes;
}
/*
 *  before and after content hooks for BP content pages
 */
function gconnect_before_content( $wrap = true ) {
	global $gconnect_theme;
	if( !empty( $gconnect_theme->front ) ) {
		if( $wrap ) { ?>
	<div id="content-sidebar-wrap">
<?php		}
		$gconnect_theme->front->genesis_before_content( $wrap );
		if( $wrap ) { ?>
		<div class="padder">
<?php		}
	}
}
function gconnect_after_content() {
	do_action( 'gconnect_after_content' );
?>
		</div><!-- .padder -->
	</div><!-- #content -->
	<?php do_action( 'genesis_after_content' ); ?>
</div><!-- end #content-sidebar-wrap -->
<?php
}
function gconnect_get_header() {
	global $gconnect_theme;
	
	get_header();
	do_action( 'genesis_before_content_sidebar_wrap' );
	echo "\n\t\t<div id=\"content-sidebar-wrap\">\n";
	$gconnect_theme->front->genesis_before_content( 'true' );
	echo "\t\t\t<div class=\"padder\">\n";
	do_action( 'gconnect_before_content' );
}
function gconnect_get_footer() {
	do_action( 'gconnect_after_content' );
	echo "\t\t\t</div><!-- .padder -->\n\t\t</div><!-- #content -->\n";
	do_action( 'genesis_after_content' );
	echo "</div><!-- end #content-sidebar-wrap -->\n";
	do_action( 'genesis_after_content_sidebar_wrap' );
	get_footer();
}
function gconnect_group_single_template() {
	$template = false;

	if ( bp_is_group_membership_request() )
		$template = 'request-membership.php';
	elseif( bp_group_is_visible() ) {
		if ( bp_is_group_admin_page() )
			$template = 'admin.php';
		elseif ( bp_is_group_members() )
			$template = 'members.php';
		elseif ( bp_is_group_invites() )
			$template = 'send-invites.php';
		elseif ( bp_is_group_forum() )
			$template = 'forum.php';
		elseif ( bp_is_active( 'activity' ) )
			$template = 'activity.php';
	}
	if( $template )
		gconnect_locate_template( array( "groups/single/$template" ), true );
		
	return $template;
}
function gconnect_member_single_template() {
	$template = 'activity.php';

	if ( bp_is_user_blogs() )
		$template = 'blogs.php';
	elseif ( bp_is_user_friends() )
		$template = 'friends.php';
	elseif ( bp_is_user_groups() )
		$template = 'groups.php';
	elseif ( bp_is_user_messages() )
		$template = 'messages.php';
	elseif ( bp_is_user_profile() )
		$template = 'profile.php';
	elseif (bp_is_forums_component() )
		$template = 'forums.php';
		
	do_action( 'bp_before_member_body' );
	gconnect_locate_template( array( "members/single/$template" ), true );
	do_action( 'bp_after_member_body' );
}
/*
 *  Helper functions
 */
function gconnect_have_adminbar() {
	global $gconnect_theme;
	return $gconnect_theme->have_adminbar();
}
function gconnect_is_bp_home() {
	global $gconnect_theme;
	return $gconnect_theme->is_home();
}
function gconnect_get_option( $setting ) {
	global $gconnect_theme;
	return $gconnect_theme->get_option( $setting );
}

function gconnect_get_bp_signup() {
	global $gconnect_theme;
	static $signup = false;

	if( $gconnect_theme->is_home() && $signup === false ) {
		if( !empty( $gconnect_theme->front->visitor ) )
			$signup = $gconnect_theme->front->visitor->signup_url();
		else
			$signup = '<li><a href="' . bp_get_signup_page(false) . '">' . __( 'Sign Up', 'buddypress' ) . '</a></li>';
	}
	return $signup;
}
function do_gb_before_bp_nav() {
	do_action( 'gb_before_bp_nav' );
}
add_action( 'gconnect_before_bp_nav', 'do_gb_before_bp_nav' );

function do_rabp_nav_items() {
	do_action( 'rabp_nav_items' );
}
add_action( 'gconnect_nav_items', 'do_rabp_nav_items' );

function gconnect_after_setup_theme() {
if( !function_exists('bp_dtheme_firstname') ) {
    function bp_dtheme_firstname( $name = false, $echo = false ) {
            global $bp;

            if ( !$name )
                    $name = $bp->loggedin_user->fullname;

            $fullname = (array)explode( ' ', $name );

            if ( $echo )
                    echo $fullname[0];
            else
                    return $fullname[0];
    }
}
function gconnect_GB_admin_notice() { ?>
		<div id="message" class="updated fade">
			<p><?php printf( __( 'Please <a href="%s">switch</a> to a non-GenesisBuddy child theme to enable the BuddyPress features in GenesisConnect.', 'genesis-connect' ), admin_url( 'themes.php' ) ) ?></p>
		</div>
<?php	}
if( function_exists( 'rabp_genesis_css' ) ) {
	add_action( 'admin_notices', 'gconnect_GB_admin_notice' );
	return;
}
function gconnect_is_blog_page() {
	global $gconnect_theme;
	return $gconnect_theme->front->is_blog_page();
}
/*
 * All functions below are included for backward compatability with GenesisBuddy
 */
function rabp_genesis_site_nav() {
	gconnect_site_nav();
}
function rabp_genesis_body_class( $classes = array() ) {
	return gconnect_body_class( $classes );
}
function genesisbuddy_before_content() {
	gconnect_before_content( false );
}
function rabp_genesis_have_bp_adminbar() {
	return gconnect_have_adminbar();
}
function rabp_genesis_have_bp() {
	global $bp;
	return isset( $bp );
}
function rabp_genesis_is_bp_home() {
	return gconnect_is_bp_home();
}
function rabp_genesis_is_blog_page() {
	return bp_is_blog_page();
}
function rabp_genesis_get_bp_signup() {
	return gconnect_get_bp_signup();
}
/*
 * add clear divs to directory pages
 */
function rabp_genesis_add_clear() { ?>
		<div class="clear"></div>
<?php
}
add_action( 'bp_after_member_header', 'rabp_genesis_add_clear' );
add_action( 'bp_after_group_header', 'rabp_genesis_add_clear' );
}
add_action( 'after_setup_theme', 'gconnect_after_setup_theme' );
/*
 * custom template locate function
 */
function gconnect_locate_template($template_names, $load = false, $require_once = true ) {
	global $gconnect_theme;

        if( !is_array($template_names) || empty( $gconnect_theme->front ) )
                return '';

        if( !( $located = locate_template( $template_names, false ) ) ) {
		foreach( $template_names as $template_name ) {
			if( !$template_name )
				continue;

			if( ( $template = $gconnect_theme->front->locate_template( $template_name ) ) ) {
				$located = $template;
				break;
			}
                }
        }

        if( $load && '' != $located )
                load_template( $located, $require_once );

        return $located;
}
