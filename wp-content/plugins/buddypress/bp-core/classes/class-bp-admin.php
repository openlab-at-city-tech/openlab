<?php
/**
 * Main BuddyPress Admin Class.
 *
 * @package BuddyPress
 * @subpackage CoreAdministration
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Admin' ) ) :

/**
 * Load BuddyPress plugin admin area.
 *
 * @todo Break this apart into each applicable Component.
 *
 * @since 1.6.0
 */
class BP_Admin {

	/** Directory *************************************************************/

	/**
	 * Path to the BuddyPress admin directory.
	 *
	 * @since 1.6.0
	 * @var string $admin_dir
	 */
	public $admin_dir = '';

	/** URLs ******************************************************************/

	/**
	 * URL to the BuddyPress admin directory.
	 *
	 * @since 1.6.0
	 * @var string $admin_url
	 */
	public $admin_url = '';

	/**
	 * URL to the BuddyPress images directory.
	 *
	 * @since 1.6.0
	 * @var string $images_url
	 */
	public $images_url = '';

	/**
	 * URL to the BuddyPress admin CSS directory.
	 *
	 * @since 1.6.0
	 * @var string $css_url
	 */
	public $css_url = '';

	/**
	 * URL to the BuddyPress admin JS directory.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $js_url = '';

	/** Other *****************************************************************/

	/**
	 * Notices used for user feedback, like saving settings.
	 *
	 * @since 1.9.0
	 * @var array()
	 */
	public $notices = array();

	/** Methods ***************************************************************/

	/**
	 * The main BuddyPress admin loader.
	 *
	 * @since 1.6.0
	 *
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set admin-related globals.
	 *
	 * @since 1.6.0
	 */
	private function setup_globals() {
		$bp = buddypress();

		// Paths and URLs
		$this->admin_dir  = trailingslashit( $bp->plugin_dir  . 'bp-core/admin' ); // Admin path.
		$this->admin_url  = trailingslashit( $bp->plugin_url  . 'bp-core/admin' ); // Admin url.
		$this->images_url = trailingslashit( $this->admin_url . 'images'        ); // Admin images URL.
		$this->css_url    = trailingslashit( $this->admin_url . 'css'           ); // Admin css URL.
		$this->js_url     = trailingslashit( $this->admin_url . 'js'            ); // Admin css URL.

		// Main settings page.
		$this->settings_page = bp_core_do_network_admin() ? 'settings.php' : 'options-general.php';

		// Main capability.
		$this->capability = bp_core_do_network_admin() ? 'manage_network_options' : 'manage_options';
	}

	/**
	 * Include required files.
	 *
	 * @since 1.6.0
	 */
	private function includes() {
		require( $this->admin_dir . 'bp-core-admin-actions.php'    );
		require( $this->admin_dir . 'bp-core-admin-settings.php'   );
		require( $this->admin_dir . 'bp-core-admin-functions.php'  );
		require( $this->admin_dir . 'bp-core-admin-components.php' );
		require( $this->admin_dir . 'bp-core-admin-slugs.php'      );
		require( $this->admin_dir . 'bp-core-admin-tools.php'      );
		require( $this->admin_dir . 'bp-core-admin-optouts.php'    );
	}

	/**
	 * Set up the admin hooks, actions, and filters.
	 *
	 * @since 1.6.0
	 *
	 */
	private function setup_actions() {

		/* General Actions ***************************************************/

		// Add some page specific output to the <head>.
		add_action( 'bp_admin_head',            array( $this, 'admin_head'  ), 999 );

		// Add menu item to settings menu.
		add_action( 'admin_menu',               array( $this, 'site_admin_menus' ), 5 );
		add_action( bp_core_admin_hook(),       array( $this, 'admin_menus' ), 5 );

		// Enqueue all admin JS and CSS.
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'admin_register_styles' ), 1 );
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 1 );
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* BuddyPress Actions ************************************************/

		// Load the BuddyPress metabox in the WP Nav Menu Admin UI.
		add_action( 'load-nav-menus.php', 'bp_admin_wp_nav_menu_meta_box' );

		// Add settings.
		add_action( 'bp_register_admin_settings', array( $this, 'register_admin_settings' ) );

		// Add a link to BuddyPress Hello in the admin bar.
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_about_link' ), 100 );

		// Add a description of new BuddyPress tools in the available tools page.
		add_action( 'tool_box',            'bp_core_admin_available_tools_intro' );
		add_action( 'bp_network_tool_box', 'bp_core_admin_available_tools_intro' );

		// On non-multisite, catch.
		add_action( 'load-users.php', 'bp_core_admin_user_manage_spammers' );

		// Emails.
		add_filter( 'manage_' . bp_get_email_post_type() . '_posts_columns',       array( $this, 'emails_register_situation_column' ) );
		add_action( 'manage_' . bp_get_email_post_type() . '_posts_custom_column', array( $this, 'emails_display_situation_column_data' ), 10, 2 );

		// Privacy Policy.
		add_action( 'bp_admin_init', array( $this, 'add_privacy_policy_content' ) );

		// BuddyPress Hello.
		add_action( 'admin_footer', array( $this, 'about_screen' ) );

		// BuddyPress Types administration.
		add_action( 'load-edit-tags.php', array( 'BP_Admin_Types', 'register_types_admin' ) );

		/* Filters ***********************************************************/

		// Add link to settings page.
		add_filter( 'plugin_action_links',               array( $this, 'modify_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );

		// Add "Mark as Spam" row actions on users.php.
		add_filter( 'ms_user_row_actions', 'bp_core_admin_user_row_actions', 10, 2 );
		add_filter( 'user_row_actions',    'bp_core_admin_user_row_actions', 10, 2 );

		// Emails
		add_filter( 'bp_admin_menu_order', array( $this, 'emails_admin_menu_order' ), 20 );
	}

	/**
	 * Register site- or network-admin nav menu elements.
	 *
	 * Contextually hooked to site or network-admin depending on current configuration.
	 *
	 * @since 1.6.0
	 */
	public function admin_menus() {

		// Bail if user cannot moderate.
		if ( ! bp_current_user_can( 'manage_options' ) ) {
			return;
		}

		$hooks = array();

		// Changed in BP 1.6 . See bp_core_admin_backpat_menu().
		$hooks[] = add_menu_page(
			__( 'BuddyPress', 'buddypress' ),
			__( 'BuddyPress', 'buddypress' ),
			$this->capability,
			'bp-general-settings',
			'bp_core_admin_backpat_menu',
			'div'
		);

		$hooks[] = add_submenu_page(
			'bp-general-settings',
			__( 'BuddyPress Help', 'buddypress' ),
			__( 'Help', 'buddypress' ),
			$this->capability,
			'bp-general-settings',
			'bp_core_admin_backpat_page'
		);

		// Add the option pages.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'BuddyPress Components', 'buddypress' ),
			__( 'BuddyPress', 'buddypress' ),
			$this->capability,
			'bp-components',
			'bp_core_admin_components_settings'
		);

		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'BuddyPress Pages', 'buddypress' ),
			__( 'BuddyPress Pages', 'buddypress' ),
			$this->capability,
			'bp-page-settings',
			'bp_core_admin_slugs_settings'
		);

		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'BuddyPress Options', 'buddypress' ),
			__( 'BuddyPress Options', 'buddypress' ),
			$this->capability,
			'bp-settings',
			'bp_core_admin_settings'
		);

		// Credits.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'BuddyPress Credits', 'buddypress' ),
			__( 'BuddyPress Credits', 'buddypress' ),
			$this->capability,
			'bp-credits',
			array( $this, 'credits_screen' )
		);

		// For consistency with non-Multisite, we add a Tools menu in
		// the Network Admin as a home for our Tools panel.
		if ( is_multisite() && bp_core_do_network_admin() ) {
			$tools_parent = 'network-tools';

			$hooks[] = add_menu_page(
				__( 'Tools', 'buddypress' ),
				__( 'Tools', 'buddypress' ),
				$this->capability,
				$tools_parent,
				'bp_core_tools_top_level_item',
				'',
				24 // Just above Settings.
			);

			$hooks[] = add_submenu_page(
				$tools_parent,
				__( 'Available Tools', 'buddypress' ),
				__( 'Available Tools', 'buddypress' ),
				$this->capability,
				'available-tools',
				'bp_core_admin_available_tools_page'
			);
		} else {
			$tools_parent = 'tools.php';
		}

		$hooks[] = add_submenu_page(
			$tools_parent,
			__( 'BuddyPress Tools', 'buddypress' ),
			__( 'BuddyPress', 'buddypress' ),
			$this->capability,
			'bp-tools',
			'bp_core_admin_tools'
		);

		$hooks[] = add_submenu_page(
			$tools_parent,
			__( 'Manage Opt-outs', 'buddypress' ),
			__( 'Manage Opt-outs', 'buddypress' ),
			$this->capability,
			'bp-optouts',
			'bp_core_optouts_admin'
		);

		// For network-wide configs, add a link to (the root site's) Emails screen.
		if ( is_network_admin() && bp_is_network_activated() ) {
			$email_labels = bp_get_email_post_type_labels();
			$email_url    = get_admin_url( bp_get_root_blog_id(), 'edit.php?post_type=' . bp_get_email_post_type() );

			$hooks[] = add_menu_page(
				$email_labels['name'],
				$email_labels['menu_name'],
				$this->capability,
				'',
				'',
				'dashicons-email',
				26
			);

			// Hack: change the link to point to the root site's admin, not the network admin.
			$GLOBALS['menu'][26][2] = esc_url_raw( $email_url );
		}

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'bp_core_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register site-admin nav menu elements.
	 *
	 * @since 2.5.0
	 */
	public function site_admin_menus() {
		if ( ! bp_current_user_can( 'manage_options' ) ) {
			return;
		}

		$hooks = array();

		// Appearance > Emails.
		$hooks[] = add_theme_page(
			_x( 'Emails', 'screen heading', 'buddypress' ),
			_x( 'Emails', 'screen heading', 'buddypress' ),
			$this->capability,
			'bp-emails-customizer-redirect',
			'bp_email_redirect_to_customizer'
		);

		// Emails > Customize.
		$hooks[] = add_submenu_page(
			'edit.php?post_type=' . bp_get_email_post_type(),
			_x( 'Customize', 'email menu label', 'buddypress' ),
			_x( 'Customize', 'email menu label', 'buddypress' ),
			$this->capability,
			'bp-emails-customizer-redirect',
			'bp_email_redirect_to_customizer'
		);

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'bp_core_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register the settings.
	 *
	 * @since 1.6.0
	 *
	 */
	public function register_admin_settings() {

		/* Main Section ******************************************************/

		// Add the main section.
		add_settings_section( 'bp_main', __( 'Main Settings', 'buddypress' ), 'bp_admin_setting_callback_main_section', 'buddypress' );

		// Hide toolbar for logged out users setting.
		add_settings_field( 'hide-loggedout-adminbar', __( 'Toolbar', 'buddypress' ), 'bp_admin_setting_callback_admin_bar', 'buddypress', 'bp_main' );
		register_setting( 'buddypress', 'hide-loggedout-adminbar', 'intval' );

		// Allow account deletion.
		add_settings_field( 'bp-disable-account-deletion', __( 'Account Deletion', 'buddypress' ), 'bp_admin_setting_callback_account_deletion', 'buddypress', 'bp_main' );
		register_setting( 'buddypress', 'bp-disable-account-deletion', 'intval' );

		// Template pack picker.
		add_settings_field( '_bp_theme_package_id', __( 'Template Pack', 'buddypress' ), 'bp_admin_setting_callback_theme_package_id', 'buddypress', 'bp_main', array( 'label_for' => '_bp_theme_package_id' ) );
		register_setting( 'buddypress', '_bp_theme_package_id', 'sanitize_text_field' );

		/* Members Section  **************************************************/

		// Add the main section.
		add_settings_section( 'bp_members', _x( 'Members Settings', 'BuddyPress setting tab', 'buddypress' ), 'bp_admin_setting_callback_members_section', 'buddypress' );

		// Avatars.
		add_settings_field( 'bp-disable-avatar-uploads', __( 'Profile Photo Uploads', 'buddypress' ), 'bp_admin_setting_callback_avatar_uploads', 'buddypress', 'bp_members' );
		register_setting( 'buddypress', 'bp-disable-avatar-uploads', 'intval' );

		// Cover images.
		if ( bp_is_active( 'members', 'cover_image' ) ) {
			add_settings_field( 'bp-disable-cover-image-uploads', __( 'Cover Image Uploads', 'buddypress' ), 'bp_admin_setting_callback_cover_image_uploads', 'buddypress', 'bp_members' );
			register_setting( 'buddypress', 'bp-disable-cover-image-uploads', 'intval' );
		}

		// Community Invitations.
		if ( bp_is_active( 'members', 'invitations' ) ) {
			add_settings_field( 'bp-enable-members-invitations', __( 'Invitations', 'buddypress' ), 'bp_admin_setting_callback_members_invitations', 'buddypress', 'bp_members' );
			register_setting( 'buddypress', 'bp-enable-members-invitations', 'intval' );
		}

		/* XProfile Section **************************************************/

		if ( bp_is_active( 'xprofile' ) ) {

			// Add the main section.
			add_settings_section( 'bp_xprofile', _x( 'Profile Settings', 'BuddyPress setting tab', 'buddypress' ), 'bp_admin_setting_callback_xprofile_section', 'buddypress' );

			// Profile sync setting.
			add_settings_field( 'bp-disable-profile-sync',   __( 'Profile Syncing',  'buddypress' ), 'bp_admin_setting_callback_profile_sync', 'buddypress', 'bp_xprofile' );
			register_setting  ( 'buddypress', 'bp-disable-profile-sync', 'intval' );
		}

		/* Groups Section ****************************************************/

		if ( bp_is_active( 'groups' ) ) {

			// Add the main section.
			add_settings_section( 'bp_groups', __( 'Groups Settings',  'buddypress' ), 'bp_admin_setting_callback_groups_section', 'buddypress' );

			// Allow subscriptions setting.
			add_settings_field( 'bp_restrict_group_creation', __( 'Group Creation', 'buddypress' ), 'bp_admin_setting_callback_group_creation',   'buddypress', 'bp_groups' );
			register_setting( 'buddypress', 'bp_restrict_group_creation', 'intval' );

			// Allow group avatars.
			add_settings_field( 'bp-disable-group-avatar-uploads', __( 'Group Photo Uploads', 'buddypress' ), 'bp_admin_setting_callback_group_avatar_uploads', 'buddypress', 'bp_groups' );
			register_setting( 'buddypress', 'bp-disable-group-avatar-uploads', 'intval' );

			// Allow group cover images.
			if ( bp_is_active( 'groups', 'cover_image' ) ) {
				add_settings_field( 'bp-disable-group-cover-image-uploads', __( 'Group Cover Image Uploads', 'buddypress' ), 'bp_admin_setting_callback_group_cover_image_uploads', 'buddypress', 'bp_groups' );
				register_setting( 'buddypress', 'bp-disable-group-cover-image-uploads', 'intval' );
			}
		}

		/* Activity Section **************************************************/

		if ( bp_is_active( 'activity' ) ) {

			// Add the main section.
			add_settings_section( 'bp_activity', __( 'Activity Settings', 'buddypress' ), 'bp_admin_setting_callback_activity_section', 'buddypress' );

			// Activity commenting on post and comments.
			add_settings_field( 'bp-disable-blogforum-comments', __( 'Post Comments', 'buddypress' ), 'bp_admin_setting_callback_blogforum_comments', 'buddypress', 'bp_activity' );
			register_setting( 'buddypress', 'bp-disable-blogforum-comments', 'bp_admin_sanitize_callback_blogforum_comments' );

			// Activity Heartbeat refresh.
			add_settings_field( '_bp_enable_heartbeat_refresh', __( 'Activity auto-refresh', 'buddypress' ), 'bp_admin_setting_callback_heartbeat', 'buddypress', 'bp_activity' );
			register_setting( 'buddypress', '_bp_enable_heartbeat_refresh', 'intval' );

			// Allow activity akismet.
			if ( is_plugin_active( 'akismet/akismet.php' ) && defined( 'AKISMET_VERSION' ) ) {
				add_settings_field( '_bp_enable_akismet', __( 'Akismet', 'buddypress' ), 'bp_admin_setting_callback_activity_akismet', 'buddypress', 'bp_activity' );
				register_setting( 'buddypress', '_bp_enable_akismet', 'intval' );
			}
		}
	}

	/**
	 * Add a link to BuddyPress Hello to the admin bar.
	 *
	 * @since 1.9.0
	 * @since 3.0.0 Hooked at priority 100 (was 15).
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_about_link( $wp_admin_bar ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$wp_admin_bar->add_node( array(
			'parent' => 'wp-logo',
			'id'     => 'bp-about',
			'title'  => esc_html_x( 'Hello, BuddyPress!', 'Colloquial alternative to "learn about BuddyPress"', 'buddypress' ),
			'href'   => bp_get_admin_url( '?hello=buddypress' ),
			'meta'   => array(
				'class' => 'say-hello-buddypress',
			),
		) );
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not BuddyPress.
		if ( plugin_basename( buddypress()->basename ) != $file ) {
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge( $links, array(
			'settings' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'bp-components' ), bp_get_admin_url( $this->settings_page ) ) ) . '">' . esc_html__( 'Settings', 'buddypress' ) . '</a>',
			'about'    => '<a href="' . esc_url( bp_get_admin_url( '?hello=buddypress' ) ) . '">' . esc_html_x( 'Hello, BuddyPress!', 'Colloquial alternative to "learn about BuddyPress"', 'buddypress' ) . '</a>'
		) );
	}

	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.6.0
	 */
	public function admin_head() {

		// Settings pages.
		remove_submenu_page( $this->settings_page, 'bp-page-settings' );
		remove_submenu_page( $this->settings_page, 'bp-settings'      );
		remove_submenu_page( $this->settings_page, 'bp-credits'       );

		// Network Admin Tools.
		remove_submenu_page( 'network-tools', 'network-tools' );

		// About and Credits pages.
		remove_submenu_page( 'index.php', 'bp-about'   );
		remove_submenu_page( 'index.php', 'bp-credits' );

		// Nonmembers Opt-outs page.
		if ( is_network_admin() ) {
			remove_submenu_page( 'network-tools', 'bp-optouts' );
		} else {
			remove_submenu_page( 'tools.php', 'bp-optouts' );
		}
	}

	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.6.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'bp-admin-common-css' );

		// BuddyPress Hello.
		if ( 0 === strpos( get_current_screen()->id, 'dashboard' ) && ! empty( $_GET['hello'] ) && $_GET['hello'] === 'buddypress' ) {
			wp_enqueue_style( 'bp-hello-css' );
			wp_enqueue_script( 'bp-hello-js' );
			wp_localize_script( 'bp-hello-js', 'bpHelloStrings', array(
				'pageNotFound' => __( 'Sorry, the page you requested was not found.', 'buddypress' ),
				'modalLabel'   => __( 'Hello BuddyPress', 'buddypress' ),
			) );
		}
	}

	/**
	 * Registers BuddyPress's suggested privacy policy language.
	 *
	 * @since 4.0.0
	 */
	public function add_privacy_policy_content() {
		// Nothing to do if we're running < WP 4.9.6.
		if ( bp_is_running_wp( '4.9.6', '<' ) ) {
			return;
		}

		$suggested_text = '<strong class="privacy-policy-tutorial">' . esc_html__( 'Suggested text:', 'buddypress' ) . ' </strong>';
		$content = '';

		$content .= '<div class="wp-suggested-text">';

		$content .= '<h2>' . esc_html__( 'What personal data we collect and why we collect it', 'buddypress' ) . '</h2>';
		$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'Sites powered by BuddyPress rely heavily on user-provided data. In this section, you should note what data you collect, from both registered users and anonymous visitors.', 'buddypress' ) . '</p>';

		if ( bp_is_active( 'xprofile' ) ) {
			$content .= '<h3>' . esc_html__( 'Profile Data', 'buddypress' ) . '</h3>';
			$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'In this section you should note what information is collected on user profiles. The suggested text gives an overview of the kinds of profile data collected by BuddyPress.', 'buddypress' ) . '</p>';

			$content .= '<p>' . $suggested_text . esc_html__( 'When you register for the site, you may be asked to provide certain personal data for display on your profile. The "Name" field is required as well as public, and user profiles are visible to any site visitor. Other profile information may be required or optional, as configured by the site administrator.', 'buddypress' ) . '</p>';
			$content .= '<p>' . esc_html__( 'User information provided during account registration can be modified or removed on the Profile > Edit panel. In most cases, users also have control over who is able to view a particular piece of profile content, limiting visibility on a field-by-field basis to friends, logged-in users, or administrators only. Site administrators can read and edit all profile data for all users.', 'buddypress' ) . '</p>';
		}

		if ( bp_is_active( 'activity' ) ) {
			$content .= '<h3>' . esc_html__( 'Activity', 'buddypress' ) . '</h3>';
			$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'In this section you should describe the kinds of information collected in the activity stream, how and whether it can be edited or deleted, and to whom the activity is visible.', 'buddypress' ) . '</p>';

			$content .= '<p>' . $suggested_text . esc_html__( 'This site records certain user actions, in the form of "activity" data. Activity includes updates and comments posted directly to activity streams, as well as descriptions of other actions performed while using the site, such as new friendships, newly joined groups, and profile updates.', 'buddypress' ) . '</p>';
			$content .= '<p>' . esc_html__( 'The content of activity items obey the same privacy rules as the contexts in which the activity items are created. For example, activity updates created in a user\'s profile is publicly visible, while activity items generated in a private group are visible only to members of that group. Site administrators can view all activity items, regardless of context.', 'buddypress' ) . '</p>';
			$content .= '<p>' . esc_html__( 'Activity items may be deleted at any time by users who created them. Site administrators can edit all activity items.', 'buddypress' ) . '</p>';
		}

		if ( bp_is_active( 'messages' ) ) {
			$content .= '<h3>' . esc_html__( 'Messages', 'buddypress' ) . '</h3>';
			$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'In this section you should describe any personal data related to private messages.', 'buddypress' ) . '</p>';

			$content .= '<p>' . $suggested_text . esc_html__( 'The content of private messages is visible only to the sender and the recipients of the message. With the exception of site administrators, who can read all private messages, private message content is never visible to other users or site visitors. Site administrators may delete the content of any message.', 'buddypress' ) . '</p>';
		}

		$content .= '<h3>' . esc_html__( 'Cookies', 'buddypress' ) . '</h3>';
		$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'In this section you should describe the BuddyPress-specific cookies that your site collects. The suggested text describes the default cookies.', 'buddypress' ) . '</p>';

		$content .= '<p>' . $suggested_text . esc_html__( 'We use a cookie to show success and failure messages to logged-in users, in response to certain actions, like joining a group. These cookies contain no personal data, and are deleted immediately after the next page load.', 'buddypress' ) . '</p>';

		$content .= '<p>' . esc_html__( 'We use cookies on group, member, and activity directories to keep track of a user\'s browsing preferences. These preferences include the last-selected values of the sort and filter dropdowns, as well as pagination information. These cookies contain no personal data, and are deleted after 24 hours.', 'buddypress' ) . '</p>';

		if ( bp_is_active( 'groups' ) ) {
			$content .= '<p>' . esc_html__( 'When a logged-in user creates a new group, we use a number of cookies to keep track of the group creation process. These cookies contain no personal data, and are deleted either upon the successful creation of the group or after 24 hours.', 'buddypress' ) . '</p>';
		}

		$content .= '</div><!-- .wp-suggested-text -->';

		wp_add_privacy_policy_content(
			'BuddyPress',
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	/** About *****************************************************************/

	/**
	 * Output the BuddyPress Hello template.
	 *
	 * @since 1.7.0 Screen content.
	 * @since 3.0.0 Now outputs BuddyPress Hello template.
	 */
	public function about_screen() {
		if ( 0 !== strpos( get_current_screen()->id, 'dashboard' ) || empty( $_GET['hello'] ) || $_GET['hello'] !== 'buddypress' ) {
			return;
		}

		// Get BuddyPress stable version.
		$version      =  self::display_version();
		$version_slug = 'version-' . str_replace( '.', '-', $version );
	?>

		<div id="bp-hello-container">
			<div id="plugin-information-scrollable" role="document">
				<div id='plugin-information-title' class="with-banner">
					<div class='vignette'></div>
					<h1>
						<?php printf(
							/* translators: %s is the placeholder for the BuddyPress version number. */
							esc_html__( 'BuddyPress %s', 'buddypress' ),
							$version
						); ?>
					</h1>
				</div>
				<div id="plugin-information-tabs">
					<a name="whats-new" href="#whats-new" class="current"><?php esc_html_e( 'What\'s new?', 'buddypress' ); ?></a>
					<a name="changelog" href="#changelog" class="dynamic" data-slug="<?php echo esc_attr( $version_slug ); ?>" data-endpoint="https://codex.buddypress.org/wp-json/wp/v2/pages"><?php esc_html_e( 'Changelog', 'buddypress' ); ?></a>
					<a name="get-involved" href="#get-involved" class="dynamic" data-slug="participate-and-contribute" data-endpoint="https://codex.buddypress.org/wp-json/wp/v2/pages"><?php esc_html_e( 'Get involved', 'buddypress' ); ?></a>
				</div>

				<div class="bp-hello-content">
					<div id="dynamic-content"></div>
					<div id="top-features">
						<h2><?php esc_html_e( 'It’s all about Widget Blocks!', 'buddypress' ); ?></h2>
						<figure class="bp-hello-aligncenter">
							<img src="<?php echo esc_url( buddypress()->plugin_url . 'bp-core/images/latest-activities.jpg' ); ?>" alt="<?php esc_attr_e( 'Illustration showing the Latest Activities Widget Block.', 'buddypress' ); ?>" />
						</figure>
						<p>
							<?php esc_html_e( 'WordPress 5.8 is right around the corner and debuts a brand new way to manage widgets: the Widget Block Editor. With BuddyPress 9.0.0, we have introduced 10 new BuddyPress Blocks, so you can continue to use your favorite BP Widgets with the new WP Block approach.', 'buddypress' ); ?>
						</p>
						<p>
							<?php esc_html_e( 'The new BP Widget Blocks are simply Legacy Widgets we’ve rebuilt as BP Blocks, which means you can also access them in the Block Editor for use in your posts or pages!', 'buddypress' ); ?>
						</p>
						<figure class="bp-hello-aligncenter">
							<img src="<?php echo esc_url( buddypress()->plugin_url . 'bp-core/images/bp-widget-blocks.jpg' ); ?>" alt="<?php esc_attr_e( 'Illustration showing BP Blocks collection.', 'buddypress' ); ?>" />
						</figure>
						<p>
							<em><?php esc_html_e( 'PS: Have you noticed that the icons for BuddyPress Blocks are now bright red?', 'buddypress' ); ?></em>
						</p>
						<h3>
							<?php
							printf(
								/* Translators: %s is a smiling face with sunglasses emoji. */
								esc_html__( 'Convert a Legacy Widget into a Block Widget in two clicks %s', 'buddypress' ),
								wp_staticize_emoji( '😎' )
							);
							?>
						</h3>
						<figure class="bp-hello-aligncenter">
							<img src="<?php echo esc_url( buddypress()->plugin_url . 'bp-core/images/legacy-widget-to-block.jpg' ); ?>" alt="<?php esc_attr_e( 'Illustration showing the Legacy Widget to Widget Block transformation.', 'buddypress' ); ?>" />
						</figure>
						<p>
							<?php esc_html_e( 'As shown in the image above, it’s very easy to transform a Widget into a Block. Your existing Widget settings will automagically be imported into your shiny, new BP Block.', 'buddypress' ); ?>
						</p>
						<p>
							<?php esc_html_e( 'NB: Creating a BP Block for each existing BP Legacy Widget marks the first step toward the progressive retirement of these tiny pieces of BuddyPress content.', 'buddypress' ); ?>
						</p>

						<hr class="bp-hello-divider"/>

						<h2><?php esc_html_e( 'The BP REST API:  Improved endpoints and a new one!', 'buddypress' ); ?></h2>
						<p>
							<?php esc_html_e( 'This BuddyPress release was built in a short time frame to be ready for the release of WordPress 5.8.', 'buddypress' ); ?>
						</p>
						<p>
							<?php esc_html_e( 'While doing the work on BP Widget Blocks, we’ve also manipulated many of our REST API endpoints and took this opportunity to improve several of them. We’re also introducing a new Sitewide Notices endpoint to allow site admins to create, edit, or delete notices and let all of your users fetch the active notice.', 'buddypress' ); ?>
						</p>

						<hr class="bp-hello-divider"/>

						<h2><?php esc_html_e( 'Under the hood', 'buddypress' ); ?></h2>
						<p>
							<?php esc_html_e( '9.0.0 comes with fewer changes than most of our releases but includes fixes for issues which appeared in 8.0.0 and BP Nouveau Template Pack improvements.', 'buddypress' ); ?>
							<?php esc_html_e( 'Click on the "Changelog" tab to discover them all!', 'buddypress' ); ?>
						</p>
						<figure class="bp-hello-aligncenter">
							<div class="dashicons dashicons-buddicons-buddypress-logo big"></div>
						</figure>

						<hr class="bp-hello-divider"/>

						<h2><?php echo esc_html( _x( 'Your feedback', 'screen heading', 'buddypress' ) ); ?></h2>
						<p>
							<?php esc_html_e( 'How are you using BuddyPress? Receiving your feedback and suggestions for future versions of BuddyPress genuinely motivates and encourages our contributors.', 'buddypress' ); ?>
							<?php
							printf(
								/* translators: %s is the placeholder for the link to BuddyPress support forums. */
								esc_html__( 'Please %s about this version of BuddyPress on our website.', 'buddypress' ),
								sprintf(
									'<a href="%1$s">%2$s</a>',
									esc_url( 'https://buddypress.org/support/' ),
									esc_html__( 'share your feedback', 'buddypress' )
								)
							);
							?>
						</p>
						<p>
							<?php
								printf(
									/* Translators: %s is a hugging face emoji. */
									esc_html__( 'Thank you for using BuddyPress! %s', 'buddypress' ),
									wp_staticize_emoji( '🤗' )
								);
							?>
						</p>

						<br /><br />
					</div>
				</div>
			</div>
			<div id="plugin-information-footer">
				<div class="bp-hello-social-cta">
					<p>
						<?php
						printf(
							/* translators: 1: heart dashicons. 2: BP Credits screen url. 3: number of BuddyPress contributors to this version. */
							_n( 'Built with %1$s by <a href="%2$s">%3$d volunteer</a>.', 'Built with %1$s by <a href="%2$s">%3$d volunteers</a>.', 24, 'buddypress' ),
							'<span class="dashicons dashicons-heart"></span>',
							esc_url( bp_get_admin_url( 'admin.php?page=bp-credits' ) ),
							number_format_i18n( 24 )
						);
						?>
					</p>
				</div>

				<div class="bp-hello-social-links">
					<ul class="bp-hello-social">
						<li>
							<?php
							printf(
								'<a class="twitter bp-tooltip" data-bp-tooltip="%1$s" href="%2$s"><span class="screen-reader-text">%3$s</span></a>',
								esc_attr__( 'Follow BuddyPress on Twitter', 'buddypress' ),
								esc_url( 'https://twitter.com/buddypress' ),
								esc_html__( 'Follow BuddyPress on Twitter', 'buddypress' )
							);
							?>
						</li>

						<li>
							<?php
							printf(
								'<a class="support bp-tooltip" data-bp-tooltip="%1$s" href="%2$s"><span class="screen-reader-text">%3$s</span></a>',
								esc_attr__( 'Visit the Support Forums', 'buddypress' ),
								esc_url( 'https://buddypress.org/support/' ),
								esc_html__( 'Visit the Support Forums', 'buddypress' )
							);
							?>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Output the credits screen.
	 *
	 * Hardcoding this in here is pretty janky. It's fine for now, but we'll
	 * want to leverage api.wordpress.org eventually.
	 *
	 * @since 1.7.0
	 */
	public function credits_screen() {
	?>

		<div class="wrap bp-about-wrap">

		<h1 class="wp-heading-inline"><?php esc_html_e( 'BuddyPress Settings', 'buddypress' ); ?></h1>
		<hr class="wp-header-end">

		<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs( esc_html__( 'Credits', 'buddypress' ) ); ?></h2>

			<p class="about-description"><?php esc_html_e( 'Meet the contributors behind BuddyPress:', 'buddypress' ); ?></p>

			<h3 class="wp-people-group"><?php esc_html_e( 'Project Leaders', 'buddypress' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-project-leaders">
				<li class="wp-person" id="wp-person-johnjamesjacoby">
					<a class="web" href="https://profiles.wordpress.org/johnjamesjacoby"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/7a2644fb53ae2f7bfd7143b504af396c?s=120">
					John James Jacoby</a>
					<span class="title"><?php esc_html_e( 'Project Lead', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-boonebgorges">
					<a class="web" href="https://profiles.wordpress.org/boonebgorges"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/9cf7c4541a582729a5fc7ae484786c0c?s=120">
					Boone B. Gorges</a>
					<span class="title"><?php esc_html_e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-djpaul">
					<a class="web" href="https://profiles.wordpress.org/djpaul"><img alt="" class="gravatar" src="https://avatars2.githubusercontent.com/u/1275914?s=120">
					Paul Gibbs</a>
					<span class="title"><?php esc_html_e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-r-a-y">
					<a class="web" href="https://profiles.wordpress.org/r-a-y"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/3bfa556a62b5bfac1012b6ba5f42ebfa?s=120">
					Ray</a>
					<span class="title"><?php esc_html_e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-imath">
					<a class="web" href="https://profiles.wordpress.org/imath"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/8b208ca408dad63888253ee1800d6a03?s=120">
					Mathieu Viet</a>
					<span class="title"><?php esc_html_e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-mercime">
					<a class="web" href="https://profiles.wordpress.org/mercime"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/fae451be6708241627983570a1a1817a?s=120">
					Mercime</a>
					<span class="title"><?php esc_html_e( 'Lead Developer', 'buddypress' ); ?></span>
				</li>
			</ul>

			<h3 class="wp-people-group"><?php esc_html_e( 'BuddyPress Team', 'buddypress' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-core-team">
				<li class="wp-person" id="wp-person-hnla">
					<a class="web" href="https://profiles.wordpress.org/hnla"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/3860c955aa3f79f13b92826ae47d07fe?s=120">
					Hugo Ashmore</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-dcavins">
					<a class="web" href="https://profiles.wordpress.org/dcavins"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/a5fa7e83d59cb45ebb616235a176595a?s=120">
					David Cavins</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-tw2113">
					<a class="web" href="https://profiles.wordpress.org/tw2113"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/a5d7c934621fa1c025b83ee79bc62366?s=120">
					Michael Beckwith</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-henry-wright">
					<a class="web" href="https://profiles.wordpress.org/henry.wright"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/0da2f1a9340d6af196b870f6c107a248?s=120">
					Henry Wright</a>
					<span class="title"><?php esc_html_e( 'Community Support', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-danbp">
					<a class="web" href="https://profiles.wordpress.org/danbp"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/0deae2e7003027fbf153500cd3fa5501?s=120">
					danbp</a>
					<span class="title"><?php esc_html_e( 'Community Support', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-shanebp">
					<a class="web" href="https://profiles.wordpress.org/shanebp"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/ffd294ab5833ba14aaf175f9acc71cc4?s=120">
					shanebp</a>
					<span class="title"><?php esc_html_e( 'Community Support', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-slaffik">
					<a class="web" href="https://profiles.wordpress.org/r-a-y"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/61fb07ede3247b63f19015f200b3eb2c?s=120">
					Slava Abakumov</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-offereins">
					<a class="web" href="https://profiles.wordpress.org/Offereins"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/2404ed0a35bb41aedefd42b0a7be61c1?s=120">
					Laurens Offereins</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-netweb">
					<a class="web" href="https://profiles.wordpress.org/netweb"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/97e1620b501da675315ba7cfb740e80f?s=120">
					Stephen Edgar</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-espellcaste">
					<a class="web" href="https://profiles.wordpress.org/espellcaste"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/b691e67be0ba5cad6373770656686bc3?s=120">
					Renato Alves</a>
					<span class="title"><?php esc_html_e( 'Core Developer', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-venutius">
					<a class="web" href="https://profiles.wordpress.org/venutius"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/6a7c42a77fd94b82b217a7a97afdddbc?s=120">
					Venutius</a>
					<span class="title"><?php esc_html_e( 'Community Support', 'buddypress' ); ?></span>
				</li>
			</ul>

			<h3 class="wp-people-group">
				<?php
				printf(
					/* translators: %s: BuddyPress version number */
					esc_html__( 'Noteworthy Contributors to %s', 'buddypress' ),
					self::display_version()
				);
				?>
			</h3>
			<ul class="wp-people-group " id="wp-people-group-noteworthy">
				<li class="wp-person" id="wp-person-vapvarun">
					<a class="web" href="https://profiles.wordpress.org/vapvarun"><img alt="" class="gravatar" src="//gravatar.com/avatar/78a3bf7eb3a1132fc667f96f2631e448?s=120">
					Varun Dubey</a>
				</li>
				<li class="wp-person" id="wp-person-sbrajesh">
					<a class="web" href="https://profiles.wordpress.org/sbrajesh/"><img alt="" class="gravatar" src="//gravatar.com/avatar/2106622ee90d53d15ac1402806616aca?s=120">
					Brajesh Singh</a>
				</li>
			</ul>

			<h3 class="wp-people-group">
				<?php
				printf(
					/* translators: %s: BuddyPress version number */
					esc_html__( 'All Contributors to BuddyPress %s', 'buddypress' ),
					self::display_version()
				);
				?>
			</h3>
			<p class="wp-credits-list">
				<a href="https://profiles.wordpress.org/boonebgorges/">Boone B Gorges (boonebgorges)</a>,
				<a href="https://profiles.wordpress.org/sbrajesh/">Brajesh Singh (sbrajesh)</a>,
				<a href="https://profiles.wordpress.org/dcavins/">David Cavins (dcavins)</a>,
				<a href="https://profiles.wordpress.org/dancaragea/">Dan Caragea (dancaragea)</a>,
				<a href="https://profiles.wordpress.org/ianbarnes/">Ian Barnes (ianbarnes)</a>,
				<a href="https://profiles.wordpress.org/nobnob/">Javier Esteban (nobnob)</a>,
				<a href="https://profiles.wordpress.org/johnjamesjacoby/">John James Jacoby (johnjamesjacoby)</a>,
				<a href="https://profiles.wordpress.org/leahkoerper/">leahkoerper</a>,
				<a href="https://profiles.wordpress.org/marbaque/">marbaque</a>,
				<a href="https://profiles.wordpress.org/markscottrobson/">Mark Robson (markscottrobson)</a>,
				<a href="https://profiles.wordpress.org/imath/">Mathieu Viet (imath)</a>,
				<a href="https://profiles.wordpress.org/modemlooper/">modemlooper</a>,
				<a href="https://profiles.wordpress.org/niftythree/">Nifty (niftythree)</a>,
				<a href="https://profiles.wordpress.org/DJPaul/">Paul Gibbs (DJPaul)</a>,
				<a href="https://profiles.wordpress.org/nekojonez/">Pieterjan Deneys (nekojonez)</a>,
				<a href="https://profiles.wordpress.org/r-a-y/">r-a-y</a>,
				<a href="https://profiles.wordpress.org/espellcaste/">Renato Alves (espellcaste)</a>,
				<a href="https://profiles.wordpress.org/shanebp">shanebp</a>,
				<a href="https://profiles.wordpress.org/shawfactor/">shawfactor</a>,
				<a href="https://profiles.wordpress.org/slaffik/">Slava Abakumov (slaffik)</a>,
				<a href="https://profiles.wordpress.org/netweb/">Stephen Edgar (netweb)</a>,
				<a href="https://profiles.wordpress.org/tamarazambrana/">tamarazambrana</a>,
				<a href="https://github.com/tkserver">TKServer</a>,
				<a href="https://profiles.wordpress.org/vapvarun/">Varun Dubey (vapvarun)</a>.
			</p>

			<h3 class="wp-people-group"><?php esc_html_e( 'With our thanks to these Open Source projects', 'buddypress' ); ?></h3>
			<p class="wp-credits-list">
				<a href="https://github.com/ichord/At.js">At.js</a>,
				<a href="https://bbpress.org">bbPress</a>,
				<a href="https://github.com/ichord/Caret.js">Caret.js</a>,
				<a href="https://tedgoas.github.io/Cerberus/">Cerberus</a>,
				<a href="https://ionicons.com/">Ionicons</a>,
				<a href="https://github.com/carhartl/jquery-cookie">jquery.cookie</a>,
				<a href="https://mattbradley.github.io/livestampjs/">Livestamp.js</a>,
				<a href="https://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>,
				<a href="https://momentjs.com/">Moment.js</a>,
				<a href="https://wordpress.org">WordPress</a>.
			</p>

			<h3 class="wp-people-group"><?php esc_html_e( 'Contributor Emeriti', 'buddypress' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-emeriti">
				<li class="wp-person" id="wp-person-apeatling">
					<a class="web" href="https://profiles.wordpress.org/apeatling/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/bb29d699b5cba218c313b61aa82249da?s=120">
					Andy Peatling</a>
					<span class="title"><?php esc_html_e( 'Project Founder', 'buddypress' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-burtadsit">
					<a class="web" href="https://profiles.wordpress.org/burtadsit"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/185e1d3e2d653af9d49a4e8e4fc379df?s=120">
					Burt Adsit</a>
				</li>
				<li class="wp-person" id="wp-person-dimensionmedia">
					<a class="web" href="https://profiles.wordpress.org/dimensionmedia"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/7735aada1ec39d0c1118bd92ed4551f1?s=120">
					David Bisset</a>
				</li>
				<li class="wp-person" id="wp-person-jeffsayre">
					<a class="web" href="https://profiles.wordpress.org/jeffsayre"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/8e009a84ff5d245c22a69c7df6ab45f7?s=120">
					Jeff Sayre</a>
				</li>
				<li class="wp-person" id="wp-person-karmatosed">
					<a class="web" href="https://profiles.wordpress.org/karmatosed"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/ca7d4273a689cdbf524d8332771bb1ca?s=120">
					Tammie Lister</a>
				</li>
				<li class="wp-person" id="wp-person-modemlooper">
					<a class="web" href="https://profiles.wordpress.org/modemlooper"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/1c07be1016e845de514931477c939307?s=120">
					modemlooper</a>
				</li>
			</ul>
		</div>

		<?php
	}

	/** Emails ****************************************************************/

	/**
	 * Registers 'Situations' column on Emails dashboard page.
	 *
	 * @since 2.6.0
	 *
	 * @param array $columns Current column data.
	 * @return array
	 */
	public function emails_register_situation_column( $columns = array() ) {
		$situation = array(
			'situation' => _x( 'Situations', 'Email post type', 'buddypress' )
		);

		// Inject our 'Situations' column just before the last 'Date' column.
		return array_slice( $columns, 0, -1, true ) + $situation + array_slice( $columns, -1, null, true );
	}

	/**
	 * Output column data for our custom 'Situations' column.
	 *
	 * @since 2.6.0
	 *
	 * @param string $column  Current column name.
	 * @param int    $post_id Current post ID.
	 */
	public function emails_display_situation_column_data( $column = '', $post_id = 0 ) {
		if ( 'situation' !== $column ) {
			return;
		}

		// Grab email situations for the current post.
		$situations = wp_list_pluck( get_the_terms( $post_id, bp_get_email_tax_type() ), 'description' );

		// Output each situation as a list item.
		echo '<ul><li>';
		echo implode( '</li><li>', $situations );
		echo '</li></ul>';
	}

	/** Helpers ***************************************************************/

	/**
	 * Return true/false based on whether a query argument is set.
	 *
	 * @see bp_do_activation_redirect()
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_new_install() {
		return (bool) isset( $_GET['is_new_install'] );
	}

	/**
	 * Return a user-friendly version-number string, for use in translations.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function display_version() {

		// Use static variable to prevent recalculations.
		static $display = '';

		// Only calculate on first run.
		if ( '' === $display ) {

			// Get current version.
			$version = bp_get_version();

			// Check for prerelease hyphen.
			$pre     = strpos( $version, '-' );

			// Strip prerelease suffix.
			$display = ( false !== $pre )
				? substr( $version, 0, $pre )
				: $version;
		}

		// Done!
		return $display;
	}

	/**
	 * Add Emails menu item to custom menus array.
	 *
	 * Several BuddyPress components have top-level menu items in the Dashboard,
	 * which all appear together in the middle of the Dashboard menu. This function
	 * adds the Emails screen to the array of these menu items.
	 *
	 * @since 2.4.0
	 *
	 * @param array $custom_menus The list of top-level BP menu items.
	 * @return array $custom_menus List of top-level BP menu items, with Emails added.
	 */
	public function emails_admin_menu_order( $custom_menus = array() ) {
		array_push( $custom_menus, 'edit.php?post_type=' . bp_get_email_post_type() );

		if ( is_network_admin() && bp_is_network_activated() ) {
			array_push(
				$custom_menus,
				get_admin_url( bp_get_root_blog_id(), 'edit.php?post_type=' . bp_get_email_post_type() )
			);
		}

		return $custom_menus;
	}

	/**
	 * Register styles commonly used by BuddyPress wp-admin screens.
	 *
	 * @since 2.5.0
	 */
	public function admin_register_styles() {
		$min = bp_core_get_minified_asset_suffix();
		$url = $this->css_url;

		/**
		 * Filters the BuddyPress Core Admin CSS file path.
		 *
		 * @since 1.6.0
		 *
		 * @param string $file File path for the admin CSS.
		 */
		$common_css = apply_filters( 'bp_core_admin_common_css', "{$url}common{$min}.css" );

		/**
		 * Filters the BuddyPress admin stylesheet files to register.
		 *
		 * @since 2.5.0
		 *
		 * @param array $value Array of admin stylesheet file information to register.
		 */
		$styles = apply_filters( 'bp_core_admin_register_styles', array(
			// Legacy.
			'bp-admin-common-css' => array(
				'file'         => $common_css,
				'dependencies' => array(),
			),

			// 2.5
			'bp-customizer-controls' => array(
				'file'         => "{$url}customizer-controls{$min}.css",
				'dependencies' => array(),
			),

			// 3.0
			'bp-hello-css' => array(
				'file'         => "{$url}hello{$min}.css",
				'dependencies' => array( 'bp-admin-common-css', 'thickbox' ),
			),
		) );

		$version = bp_get_version();

		foreach ( $styles as $id => $style ) {
			wp_register_style( $id, $style['file'], $style['dependencies'], $version );
			wp_style_add_data( $id, 'rtl', 'replace' );

			if ( $min ) {
				wp_style_add_data( $id, 'suffix', $min );
			}
		}
	}

	/**
	 * Register JS commonly used by BuddyPress wp-admin screens.
	 *
	 * @since 2.5.0
	 */
	public function admin_register_scripts() {
		$min = bp_core_get_minified_asset_suffix();
		$url = $this->js_url;

		/**
		 * Filters the BuddyPress admin JS files to register.
		 *
		 * @since 2.5.0
		 *
		 * @param array $value Array of admin JS file information to register.
		 */
		$scripts = apply_filters( 'bp_core_admin_register_scripts', array(
			// 2.5
			'bp-customizer-controls' => array(
				'file'         => "{$url}customizer-controls{$min}.js",
				'dependencies' => array( 'jquery' ),
				'footer'       => true,
			),

			// 3.0
			'bp-hello-js' => array(
				'file'         => "{$url}hello{$min}.js",
				'dependencies' => array( 'thickbox', 'bp-api-request' ),
				'footer'       => true,
			),
		) );

		$version = bp_get_version();

		foreach ( $scripts as $id => $script ) {
			wp_register_script( $id, $script['file'], $script['dependencies'], $version, $script['footer'] );
		}
	}
}
endif; // End class_exists check.
