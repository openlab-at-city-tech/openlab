<?php
/*
 * class for the front end of the site
 */
class GConnect_Front {
	var $theme = null;
	var $adminbar = true;
	var $nav = null;
	var $visitor = null;
	var $key = null;
	var $addon_directory = null;
	var $addon_url = null;
	var $show_activity_page = false;

	function GConnect_Front( &$theme, $tp_active = false ) {
		$this->__construct( $theme, $tp_active );
	}
	function  __construct( &$theme, $tp_active = false ) {
		$this->theme = $theme;
		$this->nav = $this->theme->get_option( 'subnav' );

		add_action( 'genesis_meta', array( &$this, 'genesis_meta' ), 2 );

		if( $tp_active )
			return;

		add_filter( 'bp_located_template', array( &$this, 'bp_located_template' ), 10, 2 );
		add_filter( 'bp_get_activation_page', array( &$this, 'bp_get_activation_page' ) );
		add_filter( 'bp_get_signup_page', array( &$this, 'bp_get_signup_page' ) );
		add_filter( 'genesis_pre_get_option_site_layout', array( &$this, 'site_layout' ), 20 );
		add_action( 'wp_head', array( &$this, 'wp_head' ) );
		add_action( 'template_include', array( &$this, 'template_include' ) );
		add_action( 'init', array( &$this, 'init' ), 11 );
		add_action( 'gconnect_load_template', array( &$this, 'crop_avatar' ), 2 );
	}
	function set_visitor( &$visitor ) {
		$this->visitor = $visitor;
	}
	function set_addon( $directory = '', $url = '' ) {
		$this->addon_directory = untrailingslashit( $directory );
		$this->addon_url = untrailingslashit( $url );
	}
	function genesis_meta() {
		$this->adminbar = ( !is_front_page() || $this->get_option( 'home_adminbar' ) );
		if( $this->theme->have_adminbar() && $this->adminbar )
			wp_enqueue_style( 'gconnect-adminbar', GENESISCONNECT_URL . 'css/adminbar.css', GENESISCONNECT_VERSION );
		if( $this->theme->is_home() ) {
			wp_enqueue_style( 'gconnect-bp', GENESISCONNECT_URL . 'css/buddypress.css', GENESISCONNECT_VERSION );

			wp_enqueue_script( 'bp-legacy-js', GENESISCONNECT_URL . 'lib/global.js', array( 'jquery' ), GENESISCONNECT_VERSION );
			$params = array(
				'my_favs'           => __( 'My Favorites', 'buddypress' ),
				'accepted'          => __( 'Accepted', 'buddypress' ),
				'rejected'          => __( 'Rejected', 'buddypress' ),
				'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
				'show_all'          => __( 'Show all', 'buddypress' ),
				'comments'          => __( 'comments', 'buddypress' ),
				'close'             => __( 'Close', 'buddypress' ),
				'view'              => __( 'View', 'buddypress' )
			);
			wp_localize_script( 'gconnect-ajax-js', 'Connect', $params );
			
			remove_filter( 'body_class', 'bp_get_the_body_class', 10, 2 );
			add_filter( 'body_class', array( &$this, 'body_class' ), 9, 2 );
		}
		foreach( array( '-' . $this->theme->child_style(), '.css' ) as $f ) {

			$maybe_css = '/buddypress' . $f;

			if( is_file( CHILD_DIR . $maybe_css ) ) {

				wp_enqueue_style( 'gconnect-cust' . str_replace( '.css', '', $f ), CHILD_URL . $maybe_css, defined( 'CHILD_THEME_VERSION' ) ? CHILD_THEME_VERSION : GENESISCONNECT_VERSION );
				break;

			}

			if( is_file( $this->addon_directory . $maybe_css ) ) {

				wp_enqueue_style( 'gconnect-cust' . str_replace( '.css', '', $f ), $this->addon_url . $maybe_css, GENESISCONNECT_VERSION );
				break;

			}
		}
		// BP hooks 
		if( is_multisite() && bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions', 'bp_blogs_visit_blog_button' );
			
		add_action( 'bp_member_header_actions', 'gconnect_member_header' );
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}
	}
	function after_setup_theme() {
		if( ! function_exists( 'bp_dtheme_ajax_querystring' ) )
			require_once( GENESISCONNECT_DIR . 'lib/ajax.php' );
	}
	function init() {
		if( is_admin() )
			return;

//		wp_deregister_style( 'bp-admin-bar' );
		foreach( array( 'groups'/*, 'forums' , 'blogs'*/ ) as $component )
			add_action( "bp_before_directory_{$component}_content", array( &$this, 'directory_before_content' ), 0 );
	}
	function crop_avatar() {
		global $bp;
		$step = bp_get_avatar_admin_step();
		if( 'crop-image' == $step || ( !empty( $bp->groups->current_create_step ) && $bp->groups->current_create_step == 'group-avatar' ) )
			bp_core_add_jquery_cropper();
	}
	function wp_head() {
		global $gconnect_theme;

		if( $this->theme->have_adminbar() ) {
			remove_action( 'wp_footer', 'bp_core_admin_bar', 8 );

			if( $this->adminbar )
				add_action( 'genesis_after_footer', 'bp_core_admin_bar', 88 );

			if( $this->get_option( 'adminbar' ) ) {
				remove_action( 'bp_adminbar_logo', 'bp_adminbar_logo' );
				add_action( 'bp_adminbar_logo', array( &$this, 'bp_adminbar_logo' ) );
			}
		}
		if( $this->get_option( 'login_sidebar' ) )
			add_action('genesis_before_sidebar_widget_area', array( &$this, 'sidebar' ) );

		add_filter( 'wp_list_pages', array( &$this,'wp_list_pages' ) );

		if( $this->is_blog_page() )
			return;
			
		$this->key = ( is_user_logged_in() ? 'user' : 'visitor' ) . '_';
		if( ( $do_sidebars = defined( 'SS_SETTINGS_FIELD' ) ) || $gconnect_theme->do_custom_subnav() ) {
			if( $gconnect_theme->do_custom_subnav() && ( $gconnect_theme->custom_subnav->menu = $this->get_option( $this->key . 'subnav' ) ) ) {
				add_filter( 'genesis_pre_get_option_subnav_type', array( &$gconnect_theme->custom_subnav, 'pre_get_option_subnav_type' ) );
				add_filter( 'theme_mod_nav_menu_locations', array( &$gconnect_theme->custom_subnav, 'theme_mod' ) );
			}
			if( $do_sidebars ) {
				remove_action( 'genesis_sidebar', 'ss_do_sidebar' );
				remove_action( 'genesis_sidebar_alt', 'ss_do_sidebar_alt' );
				add_action('genesis_sidebar', array( &$this, 'genesis_do_sidebar' ) );
				add_action('genesis_sidebar_alt', array( &$this, 'genesis_do_sidebar' ) );
			}
		}
	}
	function sidebar() {
		if( preg_match( '|^([^\?]+)|', $_SERVER['REQUEST_URI'], $match ) )
			$url = $match[0];
		else
			$url = get_option( 'siteurl' );
			
		$url = apply_filters( 'gconnect_login_redirect', $url ); ?>
	<div class="widget gc-login-widget"><div class="padder">

	<?php if ( $this->theme->is_home() ) { do_action( 'bp_inside_before_sidebar' ); } ?>

	<?php if ( is_user_logged_in() ) : ?>
		<?php do_action( 'bp_before_sidebar_me' ) ?>

			<div id="sidebar-me">
				<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
				<h3><?php echo bp_core_get_userlink( bp_loggedin_user_id() ) ?></h3>
				<a class="button" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>
			<?php do_action( 'bp_sidebar_me' ) ?>
			</div>
		<?php do_action( 'bp_after_sidebar_me' ) ?>
	<?php else : ?>
		<?php do_action( 'bp_before_sidebar_login_form' ) ?>

			<form name="login-form" id="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login' ) ?>" method="post">
				<label><?php _e( 'Username', 'buddypress' ) ?><br />
				<input type="text" name="log" id="userbar_user_login" class="input" tabindex="97" /></label>

				<label><?php _e( 'Password', 'buddypress' ) ?><br />
				<input type="password" name="pwd" id="userbar_user_pass" class="input" tabindex="98" /></label>

				<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="userbar_rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddypress' ) ?></label></p>

				<input type="submit" name="wp-submit" id="userbar_wp-submit" value="<?php _e( 'Log In', 'genesis-connect' ); ?>" tabindex="100" />
				<input type="hidden" name="redirect_to" value="<?php echo $url; ?>" />
				<input type="hidden" name="testcookie" value="1" />
			</form>
		<?php do_action( 'bp_after_sidebar_login_form' ) ?>

	<?php endif; ?>

	<?php if( $this->theme->is_home() ) { do_action( 'bp_inside_after_sidebar' ); } ?>

	    </div></div><!-- .padder --><?php
	}
	function body_class( $wp_classes, $custom_classes = false ) {
		global $bp;

		// observe WP custom background body class
		$bp_classes = in_array( 'custom-background', $wp_classes ) ? array( 'custom-background' ) : array();

		if( bp_is_directory() )
			$bp_classes[] = 'directory';
		elseif( bp_is_single_item() )
			$bp_classes[] = 'single-item';
		elseif( bp_is_activity_component() || $this->show_activity_page )
			$bp_classes[] = 'activity';

		if( $this->adminbar && $this->theme->have_adminbar() )
			$bp_classes[] = 'adminbar';

		if( !is_page() && is_front_page() && is_file( CHILD_DIR . '/home.php' ) )
			$bp_classes[] = 'home';

		if( $this->show_activity_page )
			$bp_classes[] = 'internal-page';
		elseif( !bp_is_blog_page() ) {
			$wp_classes = array();
			if( !bp_is_directory() )
				$bp_classes[] = 'internal-page';

			if( bp_is_user_profile() )
				$bp_classes[] = 'profile';
			elseif( bp_is_blogs_component() )
				$bp_classes[] = 'blogs';
			elseif( bp_is_messages_component() )
				$bp_classes[] = 'messages';
			elseif( bp_is_friends_component() )
				$bp_classes[] = 'friends';
			elseif( bp_is_groups_component() )
				$bp_classes[] = 'groups';
			elseif( bp_is_settings_component() )
				$bp_classes[] = 'settings';
		}

		if ( is_user_logged_in() ) {
			$bp_classes[] = 'logged-in';
			if( !bp_is_directory() ) {
				if( bp_is_user_friends() )
					$bp_classes[] = 'my-friends';
				elseif( bp_is_user_activity() )
					$bp_classes[] = 'my-activity';
				elseif( bp_is_user_blogs() )
					$bp_classes[] = 'my-blogs';
				elseif( bp_is_user_groups() )
					$bp_classes[] = 'my-groups';
				elseif( bp_is_messages_inbox() )
					$bp_classes[] = 'inbox';
				elseif( bp_is_messages_sentbox() )
					$bp_classes[] = 'sentbox';
				elseif( bp_is_messages_compose_screen() )
					$bp_classes[] = 'compose';
				elseif( bp_is_notices() )
					$bp_classes[] = 'notices';
				elseif( bp_is_user_friend_requests() )
					$bp_classes[] = 'friend-requests';
				elseif( bp_is_create_blog() )
					$bp_classes[] = 'create-blog';
				elseif( bp_is_group_leave() )
					$bp_classes[] = 'leave-group';
				elseif( bp_is_group_invites() )
					$bp_classes[] = 'group-invites';
				elseif( bp_is_group_forum_topic_edit() )
					$bp_classes[] = 'group-forum-topic-edit';
				elseif( bp_is_group_admin_page() )
					$bp_classes[] = 'group-admin';
				elseif( bp_is_group_create() )
					$bp_classes[] = 'group-create';
				elseif( bp_is_user_change_avatar() )
					$bp_classes[] = 'change-avatar';
				elseif( bp_is_user_profile_edit() )
					$bp_classes[] = 'profile-edit';
			}
		} else 
			$bp_classes[] = 'visitor';
			
		if( bp_is_group_members() )
			$bp_classes[] = 'group-members';
		elseif( bp_is_group_home() )
			$bp_classes[] = 'group-home';
		elseif( bp_is_group_forum() ) {
			$bp_classes[] = 'group-forum';

			if( bp_is_group_forum_topic() )
				$bp_classes[] = 'group-forum-topic';
		} else {
			if ( bp_is_user_recent_commments() )
				$bp_classes[] = 'recent-comments';

			if ( bp_is_user_recent_posts() )
				$bp_classes[] = 'recent-posts';

			if( bp_is_user_friends_activity() )
				$bp_classes[] = 'friends-activity';
			elseif( bp_is_single_activity() )
				$bp_classes[] = 'activity-permalink';
			elseif( bp_is_register_page() )
				$bp_classes[] = 'registration';
			elseif ( bp_is_activation_page() )
				$bp_classes[] = 'activation';
		}

		/* Add the current_component, current_action into the bp classes */
		if ( !bp_is_blog_page() ) {
			if ( !empty( $bp->current_component ) )
				$bp_classes[] = $bp->current_component;

			if ( !empty( $bp->current_action ) )
				$bp_classes[] = $bp->current_action;
		}

		if ( ( !bp_is_blog_page() || is_home() ) && !empty( $custom_classes ) )
				$wp_classes = (array) $custom_classes;

		/* Merge WP classes with BP classes */
		$classes = array_merge( (array) $bp_classes, (array) $wp_classes );

		/* Remove any duplicates */
		$classes = array_unique( $classes );

		return apply_filters( 'bp_get_the_body_class', $classes, $bp_classes, $wp_classes, $custom_classes );
	}
	function genesis_before_content( $wrap = false ) {
		$before = $this->get_option( 'before_content' );
		if( 'genesis' == $before )
			do_action( 'genesis_before_content' );
		if( $wrap )
			echo "<div id=\"content\">\n";
		if( 'widget' == $before ) { ?>
				<div class="bp-content-top">
					<?php if( !dynamic_sidebar( 'gconnect-before' ) ) : ?>
						<div class="widget">
							<h4><?php _e( "Before BuddyPress (Optional)", 'genesis-connect' ); ?></h4>
							<div class="wrap">
								<p><?php _e( "This is an optional widgeted area which is called Before BuddyPress. To get started, log into your WordPress dashboard, and then go to the Genesis > Theme Settings. In the BuddyPress Settings choose Widget Area on the BuddyPress Before Content dropdown. Then go to Appearance > Widgets screen. There you can drag any widget into the Before BuddyPress widget area on the right hand side. ", 'genesis-connect' ); ?></p>
							</div><!-- end .wrap -->
						</div><!-- end .widget -->
					<?php endif; ?>
				</div><!-- end .bp-content-top -->
	<?php	}
	}
	function bp_adminbar_logo() {
		global $bp;
		echo '<ul class="main-nav"><li><a href="' . $bp->root_domain . '">' . get_blog_option( BP_ROOT_BLOG, 'blogname') . '</a>';
		wp_nav_menu( array( 'menu' => $this->get_option('adminbar') ) );
		echo '</li></ul>';
	}
	function wp_list_pages( $menu ) {
		if( $this->theme->is_home() && bp_get_signup_allowed() && !empty( $this->visitor ) ) {
			if( 'before_pages' == $this->visitor->get_register() )
				$menu = rabp_genesis_get_bp_signup() . $menu;
			elseif( 'after_pages' == $this->visitor->get_register() )
				$menu .= rabp_genesis_get_bp_signup();
		}
		return $menu;
	}
	function bp_located_template( $found, $templates ) {
		// exists in the child theme
		if( !empty( $found ) )
			return $found;

		if( !empty( $templates ) && is_array( $templates ) ) {
			reset( $templates );
			$template = current( $templates );

			$maybe = $this->locate_template( $template );
			if( $maybe ) {
				do_action( 'gconnect_load_template', $template, $maybe );
				return $maybe;
			}
		}
		return $found;
	}
	function locate_template( $template, $default = '' ) {
		$maybe = GENESISCONNECT_DIR . 'templates/' . $template;

		if( is_file( $maybe ) )
			return $maybe;

		return $default;
	}
	function directory_before_content() {
		if( !preg_match( '|^bp_before_directory_([^\_]+)_content$|', current_filter(), $match ) )
			return;

		$component = $match[1];

		$defaults = array(
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			'show_create' => is_user_logged_in()
		);
		switch( $component ) {
			case 'groups':
				$defaults['directory_title'] = __( 'Groups Directory', 'buddypress' );
				$defaults['create_html'] = ' &nbsp;<a class="button" href="' . bp_get_root_domain() . '/' . bp_get_root_slug( 'groups' ) . '/create/">' . __( 'Create a Group', 'buddypress' ) . '</a>';
				break;
			case 'forums':
				$defaults['directory_title'] = __( 'Group Forums Directory', 'buddypress' );
				$defaults['create_html'] = ' &nbsp;<a class="button" href="#new-topic" id="new-topic-button">' . __( 'New Topic', 'buddypress' ) .'</a>';
				break;
			case 'blogs':
				$defaults['show_create'] &= bp_blog_signup_enabled();
				$defaults['directory_title'] = __( 'Blogs Directory', 'buddypress' );
				$defaults['create_html'] = ' &nbsp;<a class="button" href="' . bp_get_root_domain() . '/' . bp_get_root_slug( 'blogs' ) . '/create/">' . __( 'Create a Blog', 'buddypress' ) . '</a>';
				break;
		}
		$args = apply_filters( 'gconnect_directory_title', $defaults, $component );
		extract( $args, EXTR_SKIP );

		echo $before_title . $directory_title;
		if( $show_create )
			echo $create_html;
		echo $after_title;
	}
	function bp_get_activation_page() {
		global $bp;
		return $bp->root_domain . '/' . BP_ACTIVATION_SLUG;
	}
	function bp_get_signup_page() {
		global $bp;
		return $bp->root_domain . '/' . BP_REGISTER_SLUG;
	}
	function genesis_do_sidebar() {
		$suffix = '_alt';
		$hook = current_filter();
		if( substr( $hook, -4 ) != $suffix )
			$suffix = '';
		if( ( $_sidebar = $this->get_option( $this->key . 'sidebar' . $suffix ) ) && dynamic_sidebar( $_sidebar ) )
			return;

		$bar = 'genesis_do_sidebar' . $suffix;
		$bar();
	}
	function template_include( $template ) {
		if( ( ( is_home() || is_front_page() ) && 'activity' == $this->get_option( 'home' ) ) || ( ( $page = get_option( 'gconnect_activity_page' ) ) && is_page( $page ) ) ) {
			$this->show_activity_page = true;
			$activity = gconnect_locate_template( array( 'activity/index.php' ) );
			if( $activity )
				return $activity;
		}
		return $template;
	}
	function site_layout( $layout ) {
		global $bp;
		if( !$this->is_blog_page() && ( bp_is_group_forum() || ( isset( $bp->component ) && $bp->component == 'forum' ) ) ) {
			$forum_layout = $this->get_option( 'forum_layout' );
			if( !empty( $forum_layout ) )
				return $forum_layout;
		}
		return $layout;
	}
	function is_blog_page() {
		global $wp_query;
		return ( $wp_query->post_count > 0 );
	}
	function get_option( $key ) {
		return $this->theme->get_option( $key );
	}
}
function gconnect_member_header() {
	if( bp_is_active( 'friends' ) )
		bp_add_friend_button();
	if( bp_is_active( 'activity' ) )
		bp_send_public_message_button();
	if( bp_is_active( 'messages' ) )
		bp_send_private_message_button();
}
