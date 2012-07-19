<?php
/**
 * This file is included when BuddyPress is active, and after the plugins_loaded action.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements for BuddyPress
 * @subpackage core
 *
 * $Id: achievements-core.php 1024 2012-01-02 13:53:50Z DJPaul $
 */

/**
 * Constant for third-party plugins to check if Achievements is active
 */
define ( 'ACHIEVEMENTS_IS_INSTALLED', 1 );

/**
 * Version number
 */
define ( 'ACHIEVEMENTS_VERSION', '2.0.6' );

load_plugin_textdomain( 'dpa', false, '/achievements/includes/languages/' );

// The classes file holds all database access classes and functions
require ( dirname( __FILE__ ) . '/achievements-classes.php' );

// The ajax file holds all functions used in AJAX queries
require ( dirname( __FILE__ ) . '/achievements-ajax.php' );

// The cssjs file sets up and enqueue all CSS and JS files
require ( dirname( __FILE__ ) . '/achievements-cssjs.php' );

// The templatetags file contains classes and functions designed for use in template files
require ( dirname( __FILE__ ) . '/achievements-templatetags.php' );

// The widgets file contains code to create and register widgets
require ( dirname( __FILE__ ) . '/achievements-widgets.php' );

// The notifications file contains functions to send email notifications on specific user actions
require ( dirname( __FILE__ ) . '/achievements-notifications.php' );

// The filters file creates and apply filters to component output functions
require ( dirname( __FILE__ ) . '/achievements-filters.php' );

/**
 * Set up global variables
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_setup_globals() {
	global $bp;

	$bp->achievements->id = 'achievements';
	$bp->achievements->slug = DPA_SLUG;
	$bp->achievements->table_achievements = $bp->table_prefix . 'achievements';
	$bp->achievements->table_unlocked = $bp->table_prefix . 'achievements_unlocked';
	$bp->achievements->table_actions = $bp->table_prefix . 'achievements_actions';
	$bp->active_components[$bp->achievements->slug] = $bp->achievements->id;

	// For BuddyPress 1.5
	$bp->achievements->root_slug = isset( $bp->pages->achievements->slug ) ? $bp->pages->achievements->slug : DPA_SLUG;
	$bp->achievements->name = __( 'Achievements', 'dpa' );
	$bp->achievements->notification_callback = 'dpa_format_notifications';
}
add_action( 'bp_setup_globals', 'dpa_setup_globals' );

/**
 * Registers as a root component (example.com/achievements/)
 *
 * @since 2.0
 */
function dpa_setup_root_component() {
	bp_core_add_root_component( DPA_SLUG );
}
add_action( 'bp_setup_root_components', 'dpa_setup_root_component' );

/**
 * Set up navigation and register pages
 *
 * @global object $bp BuddyPress global settings
 * @global bool $is_member_page If we are under anything with a members slug
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_setup_nav() {
	global $bp, $is_member_page;

	$url = dpa_get_achievements_permalink() . '/';

	if ( bp_is_current_component( $bp->achievements->slug ) && $bp->achievements->current_achievement = new DPA_Achievement( array( 'type' => 'single', 'slug' => apply_filters( 'dpa_get_achievement_slug', $bp->current_action ) ) ) ) {
		if ( isset( $bp->achievements->current_achievement->is_active ) && !$bp->achievements->current_achievement->is_active && !dpa_permission_can_user_edit() ) {
			// Require edit permission to view an inactive Achievement's pages
			bp_core_redirect( dpa_get_achievements_permalink() );
			return;

		} else {
			if ( $bp->achievements->current_achievement->id )
				$bp->is_single_item = true;

			$bp->is_item_admin = 0;
			if ( $bp->loggedin_user->is_super_admin )
				$bp->is_item_admin = 1;  // Possibly redundant since BP 1.1, but left for compatibility just in case
		}
	}

	// Add 'Achievements' to the main navigation
	bp_core_new_nav_item( array( 'name' => sprintf( __( 'Achievements <span>%s</span>', 'dpa' ), dpa_get_total_achievement_count_for_user() ), 'slug' => $bp->achievements->slug, 'position' => 80, 'screen_function' => 'dpa_screen_my_achievements', 'default_subnav_slug' => DPA_SLUG_MY_ACHIEVEMENTS, 'item_css_id' => $bp->achievements->id ) );


	$subnav_url = $bp->loggedin_user->domain . DPA_SLUG . '/';
	bp_core_new_subnav_item( array( 'name' => sprintf( __( 'My Achievements <span>%s</span>', 'dpa' ), dpa_get_total_achievement_count_for_user() ), 'slug' => DPA_SLUG_MY_ACHIEVEMENTS, 'parent_url' => $subnav_url, 'parent_slug' => $bp->achievements->slug, 'screen_function' => 'dpa_screen_my_achievements', 'position' => 10, 'item_css_id' => 'achievements_my' ) );

 	if ( bp_is_current_component( $bp->achievements->slug ) ) {
		if ( bp_is_my_profile() && !$bp->is_single_item ) {
			$bp->bp_options_title = __( 'My Achievements', 'dpa' );

		} elseif ( !bp_is_my_profile() && !$bp->is_single_item ) {
			$bp->bp_options_title = $bp->displayed_user->fullname;

		} elseif ( $bp->is_single_item ) {
			// When in a single listing, the first action is bumped down one because of the listing ID, so we need to adjust this and set the listing name to current_item.

			$bp->current_item = $bp->current_action;

			if ( isset( $bp->action_variables[0] ) )
				$bp->current_action = $bp->action_variables[0];
			else
				$bp->current_action = '';

			array_shift( $bp->action_variables );

			$bp->bp_options_title = apply_filters( 'dpa_get_achievement_name', $bp->achievements->current_achievement->name );
			$achievement_link     = $url . $bp->achievements->current_achievement->slug . '/';
			$parent_slug          = $bp->achievements->current_achievement->slug;

			// Add to the main navigation
			$main_nav = array(
				'name'                => __( 'Home', 'dpa' ),
				'slug'                => $bp->achievements->current_achievement->slug,
				'position'            => -1, // Do not show in BuddyBar
				'screen_function'     => 'dpa_screen_achievement_activity',
				'default_subnav_slug' => DPA_SLUG_ACHIEVEMENT_ACTIVITY,
				'item_css_id'         => $bp->achievements->id
			);
			bp_core_new_nav_item( $main_nav );

			/**
			 * Setup the subnav items
			 */
			// Add the "Home" subnav item, as this will always be present
			$sub_nav[] = array(
				'name'            =>  __( 'Home', 'dpa' ),
				'slug'            => DPA_SLUG_ACHIEVEMENT_ACTIVITY,
				'parent_url'      => $achievement_link,
				'parent_slug'     => $parent_slug,
				'screen_function' => 'dpa_screen_achievement_activity',
				'position'        => 20,
				'item_css_id'     => 'achievements_activity'
			);

			$sub_nav[] = array(
				'name'            => sprintf( __( 'Unlocked By <span>%s</span>', 'dpa' ), dpa_get_achievement_unlocked_count( $bp->achievements->current_achievement->id ) ),
				'slug'            => DPA_SLUG_ACHIEVEMENT_UNLOCKED_BY,
				'parent_url'      => $achievement_link,
				'parent_slug'     => $parent_slug,
				'screen_function' => 'dpa_screen_achievement_unlocked_by',
				'position'        => 40,
				'item_css_id'     => 'achievements_unlockedby'
			);

			if ( dpa_permission_can_user_change_picture() ) {
				$sub_nav[] = array(
					'name'            =>  __( 'Change Picture', 'dpa' ),
					'slug'            => DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE,
					'parent_url'      => $achievement_link,
					'parent_slug'     => $parent_slug,
					'screen_function' => 'dpa_screen_achievement_change_picture',
					'position'        => 50,
					'item_css_id'     => 'achievements_changepicture'
				);
			}

			if ( dpa_permission_can_user_delete() ) {
				$sub_nav[] = array(
					'name'            =>  __( 'Delete', 'dpa' ),
					'slug'            => DPA_SLUG_ACHIEVEMENT_DELETE,
					'parent_url'      => $achievement_link,
					'parent_slug'     => $parent_slug,
					'screen_function' => 'dpa_screen_achievement_delete',
					'position'        => 50,
					'user_has_access' => dpa_permission_can_user_delete(),
					'item_css_id'     => 'achievements_delete'
				);
			}

			if ( dpa_permission_can_user_delete() ) {
				$sub_nav[] = array(
					'name'            =>  __( 'Edit', 'dpa' ),
					'slug'            => DPA_SLUG_ACHIEVEMENT_EDIT,
					'parent_url'      => $achievement_link,
					'parent_slug'     => $parent_slug,
					'screen_function' => 'dpa_screen_achievement_edit',
					'position'        => 60,
					'user_has_access' => dpa_permission_can_user_edit(),
					'item_css_id'     => 'achievements_edit'
				);
			}

			if ( dpa_permission_can_user_grant() ) {
				$sub_nav[] = array(
					'name'            =>  __( 'Grant', 'dpa' ),
					'slug'            => DPA_SLUG_ACHIEVEMENT_GRANT,
					'parent_url'      => $achievement_link,
					'parent_slug'     => $parent_slug,
					'screen_function' => 'dpa_screen_achievement_grant',
					'position'        => 70,
					'user_has_access' => dpa_permission_can_user_grant(),
					'item_css_id'     => 'achievements_grant'
				);
			}

			// initialize the subnav items
			foreach( $sub_nav as $nav )
				bp_core_new_subnav_item( $nav );
		}
	}
}
add_action( 'bp_setup_nav', 'dpa_setup_nav' );

/**
 * Add WP Admin Bar support
 *
 * @global object $bp BuddyPress global settings
 * @global object $wp_admin_bar WP Admin Bar object
 * @since 2.1
 */
function dpa_setup_admin_bar() {
	global $bp, $wp_admin_bar;

	// Prevent debug notices
	$wp_admin_nav = array();

	// Menus for logged in user
	if ( is_user_logged_in() ) {

		// Setup the logged in user variables
		$user_domain   = $bp->loggedin_user->domain;
		$link = trailingslashit( $user_domain . $bp->achievements->slug );

		// Add the "Achievements" sub menu
		$wp_admin_nav[] = array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'my-account-' . $bp->achievements->id,
			'title'  => __( 'Achievements', 'dpa' ),
			'href'   => trailingslashit( $link )
		);

		// My Achievements
		$wp_admin_nav[] = array(
			'parent' => 'my-account-' . $bp->achievements->id,
			'title'  => __( 'My Achievements', 'dpa' ),
			'href'   => trailingslashit( $link . DPA_SLUG_MY_ACHIEVEMENTS )
		);

		foreach( $wp_admin_nav as $admin_menu )
			$wp_admin_bar->add_menu( $admin_menu );

	}
}
add_action( 'bp_setup_admin_bar', 'dpa_setup_admin_bar' );

/**
 * Add "show achievement unlocks" filter to sitewide and member activity streams
 *
 * @since 2.0
 */
function dpa_add_sitewide_activity_filter() {
?>
	<option value="new_achievement"><?php _e( 'Achievements', 'dpa' ); ?></option>
<?php
}
add_action( 'bp_activity_filter_options', 'dpa_add_sitewide_activity_filter' );
add_action( 'bp_member_activity_filter_options', 'dpa_add_sitewide_activity_filter' );

/**
 * Adds RSS links to page <head> section
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_add_rss() {
	global $bp;

	if ( dpa_is_achievement_single() ) :
	?>
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ) ?> | <?php echo apply_filters( 'dpa_get_achievement_name', $bp->achievements->current_achievement->name ) ?> | <?php _e( 'Achievement RSS Feed', 'dpa' ) ?>" href="<?php dpa_achievement_activity_feed_link() ?>" />
	<?php
	endif;
}
add_action( 'bp_head', 'dpa_add_rss' );

/**
 * This function adds a wp-admin menu item under "BuddyPress."
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_add_admin_menu() {
	global $bp;

	if ( !$bp->loggedin_user->is_super_admin )
		return false;

	// This file holds everything that goes into /wp-admin/
	require ( dirname( __FILE__ ) . '/achievements-admin.php' );

	wp_enqueue_style( 'achievements-wpadmin', plugins_url( '/css/wpadmin.css', __FILE__ ), array(), ACHIEVEMENTS_VERSION );

	add_submenu_page( 'bp-general-settings', __( 'Achievements', 'dpa' ), __( 'Achievements', 'dpa' ), 'manage_options', $bp->achievements->id, 'dpa_admin_screen' );
	add_action( 'load-buddypress_page_achievements', 'dpa_admin_screen_on_load' );
	add_action( 'admin_init', 'dpa_register_admin_settings' );
}
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'dpa_add_admin_menu' );

/**
 * Load template filter; allows site/super admin to override Achievements' templates in
 * their active theme and replace the ones that are stored in the plugin directory.
 *
 * @global object $bp BuddyPress global settings
 * @param string $found_template Contains any matches found for the requested template(s)
 * @param array $templates Page template(s) we want to load
 * @return string Absolute directory path to the requested template
 * @since 2.0
 */
function dpa_load_template_filter( $found_template, $templates ) {
	global $bp;

	if ( !bp_is_current_component( $bp->achievements->slug ) || $found_template )
		return $found_template;

	$filtered_templates = array();
	$filtered_templates = locate_template( $templates, false );

	if ( is_array( $filtered_templates ) && isset( $filtered_templates[0] ) && $filtered_templates[0] )
		return apply_filters( 'dpa_load_template_filter', $filtered_templates[0] );

	$filtered_templates = array();
	foreach ( (array) $templates as $template ) {
		$url = dirname( __FILE__ ) . '/templates/' . $template;

		if ( file_exists( $url ) )
			$filtered_templates[] = $url;
	}

	if ( !isset( $filtered_templates[0] ) )
		$filtered_templates[0] = '';

	return apply_filters( 'dpa_load_template_filter', $filtered_templates[0] );
}
add_filter( 'bp_located_template', 'dpa_load_template_filter', 10, 2 );

/**
 * Includes, into page output, a template file. To be used within a template included by bp_core_load_template().
 *
 * If the requested template follows the BuddyPress core theme file structure, for example "[groups]/[single]/file.php"
 * i.e. being a file of a registered root component underneath its "single" folder, we'll also try to match against a version
 * of that template path suffixed with the identifier of the object of the current component.
 *
 * For example, on pages of a group named "snugglewugglepants", we'd look for "groups/single/file-snugglewugglepants.php"
 * as well as plain-old "groups/single/file.php".
 *
 * Underneath the member page hierarchy, the user's login name is used, e.g. "members/single/page-admin.php" for the admin user.
 *
 * @global object $bp BuddyPress global settings
 * @param array $templates Requested templates, in priority order
 * @see bp_core_add_root_component()
 * @see bp_core_load_template()
 * @since 2.0
 */
function dpa_load_template( $templates ) {
	global $bp;

	// Until BuddyPress gets better template filtering, support GenesisConnect.
	if ( function_exists( 'gconnect_locate_template' ) && $templates == array( 'members/single/member-header.php' ) ) {
		gconnect_locate_template( $templates, true );
		return;
	}

	$alternative_templates = array();

	foreach ( $templates as $template ) {
		$uri = explode( '/', $template );
		$uri_size = count( $uri );

		if ( !is_array( $uri ) || $uri_size < 2 )
			continue;

		if ( '/' == $uri[0] ) {
			unset( $uri[0] );
			$uri = array_merge( $uri, array() );  // Reset the keys by merging with an empty array
		}

		if ( !bp_is_current_component( $uri[0] ) || 'single' != $uri[1] )
			continue;

		if ( $bp->current_item )
			$template_slug = $bp->current_item;
		elseif ( BP_MEMBERS_SLUG == $uri[0] && isset( $bp->displayed_user ) )
			$template_slug = $bp->displayed_user->userdata->user_login;
		else
			continue;

		$extension = strpos( $uri[$uri_size - 1], '.php' );
		if ( false === $extension )
			continue;

		$template_slug = apply_filters( 'dpa_load_template_slug', $template_slug, $template, $templates );
		if ( $template_slug ) {
			// Reconstruct the URL with the filename suffix
			$uri[$uri_size - 1] = substr( $uri[$uri_size - 1], 0, $extension ) . "-{$template_slug}.php";
			$alternative_templates[] = implode( '/', $uri );
		}
	}

	$templates = array_merge( apply_filters( 'dpa_load_template_alternatives', $alternative_templates, $templates ), $templates );
	if ( $located_template = apply_filters( 'bp_located_template', locate_template( $templates, false ), $templates, $alternative_templates ) )
		load_template( apply_filters( 'bp_load_template', $located_template ) );
}

/**
 * Loads the Achievements meta from the settings database
 *
 * @return array
 * @since 2.0
 */
function dpa_get_achievements_meta() {
	return apply_filters( 'dpa_get_achievements_meta', get_site_option( 'achievements_meta' ) );
}

/**
 * Loads active actions from the database
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @return array
 * @since 2.0
 */
function dpa_get_active_actions() {
	global $bp, $wpdb;

	if ( !$actions = wp_cache_get( 'dpa_active_actions', 'dpa' ) ) {
		$actions = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT action.name FROM {$bp->achievements->table_achievements} as achievement, {$bp->achievements->table_actions} as action WHERE achievement.action_id = action.id AND achievement.action_id != -1 AND (achievement.is_active = 1 OR achievement.is_active = 2)" ) );
		wp_cache_set( 'dpa_active_actions', $actions, 'dpa' );
	}
	return apply_filters( 'dpa_get_active_actions', (array)$actions );
}

/**
 * Loads the actions from the database.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @return string
 * @since 2.0
 */
function dpa_get_actions() {
	global $bp, $wpdb;

	if ( !$actions = wp_cache_get( 'dpa_actions', 'dpa' ) ) {
		$actions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bp->achievements->table_actions} ORDER BY category, description" ) );
		wp_cache_set( 'dpa_actions', $actions, 'dpa' );
	}
	return apply_filters( 'dpa_get_actions', (array)$actions );
}

/**
 * On Achievement creation, create/update all the relevant metas.
 *
 * @param DPA_Achievement $achievement
 * @since 2.0
 */
function dpa_achievement_creation_update_meta( $achievement ) {
	// Create site Achievement meta...
	$meta = dpa_get_achievements_meta();
	$meta[$achievement->id] = array( 'no_of_unlocks' => 0 );
	update_site_option( 'achievements_meta', apply_filters( 'dpa_achievement_creation_update_meta', $meta, $achievement ) );

	// Update other metas...
	wp_cache_delete( 'dpa_achievements_meta', 'dpa' );
	wp_cache_delete( 'dpa_active_actions', 'dpa' );
}
add_action( 'dpa_screen_achievement_create_success', 'dpa_achievement_creation_update_meta', 10, 1 );

/**
 * On Achievement edit, update all the relevant metas.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param DPA_Achievement $new_achievement New version of the Achievement
 * @param DPA_Achievement $old_achievement Previous version of the Achievement
 * @since 2.0
 */
function dpa_achievement_modification_update_meta( $new_achievement, $old_achievement ) {
	global $bp, $wpdb;

	// Achievement picture has changed, clear picture url cache.
	if ( $new_achievement->picture_id != $old_achievement->picture_id )
		wp_cache_delete( 'dpa_achievement_picture_urls', 'dpa' );

	// Achievement type has changed, clear user count meta.
	if ( $new_achievement->action_id != $old_achievement->action_id ) {
		$users = $wpdb->query( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'achievements_counters'" );

		foreach ( (array)$users as $user_id ) {
			$counters = get_user_meta( $user_id, 'achievements_counters', true );

			if ( is_array( $counters ) && isset( $counters[$new_achievement->id] ) ) {
				unset( $counters[$new_achievement->id] );
				update_user_meta( $user_id, 'achievements_counters', $counters );
			}
		}

	}
}
add_action( 'dpa_screen_achievement_edit_success', 'dpa_achievement_modification_update_meta', 10, 2 );

/**
 * On Achievement deletion, remove/update all the relevant metas.
 *
 * @global wpdb $wpdb WordPress database object
 * @param int $id Achievement ID
 * @since 2.0
 */
function dpa_achievement_deletion_update_meta( $id ) {
	global $wpdb;

	// Update site Achievement meta...
	$meta = dpa_get_achievements_meta();
	unset( $meta[$id] );
	update_site_option( 'achievements_meta', apply_filters( 'dpa_achievement_deletion_update_meta', $meta, $id ) );

	// Update other metas...
	$users = $wpdb->query( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'achievements_counters'" );
	foreach ( (array)$users as $user_id ) {
		$counters = get_user_meta( $user_id, 'achievements_counters', true );

		if ( is_array( $counters ) && isset( $counters[$id] ) ) {
			unset( $counters[$id] );
			update_user_meta( $user_id, 'achievements_counters', $counters );
		}
	}

	wp_cache_delete( 'dpa_achievements_meta', 'dpa' );
	wp_cache_delete( 'dpa_active_actions', 'dpa' );
	dpa_delete_highscore_cache();
}
add_action( 'dpa_achievement_deleted', 'dpa_achievement_deletion_update_meta', 10, 1 );

/**
 * Clean up after a user is deleted.
 *
 * @global object $bp BuddyPress global settings
 * @param integer $user_id
 * @since 2.0
 */
function dpa_remove_user_data( $user_id ) {
	global $bp;

	delete_user_meta( $user_id, 'achievements_points' );
	delete_user_meta( $user_id, 'achievements_counters' );
	dpa_delete_highscore_cache();
	bp_core_delete_notifications_from_user( $user_id, $bp->achievements->id, 'new_achievement' );

	// Delete achievement unlocks
	dpa_delete_achievement_unlocks_for_user( $user_id );

	// Delete unlocked achievements count cache
	wp_cache_delete( 'dpa_get_total_achievements_count_for_user_' . $user_id, 'dpa' );

	// Update site achievement meta
	$meta = dpa_get_achievements_meta();
	$achievements = DPA_Achievement::get( array( 'type' => 'all' ) );
	$achievements = $achievements['achievements'];
	foreach ( (array)$achievements as $achievement )
		$meta[$achievement->id] = array( 'no_of_unlocks' => dpa_get_total_achievement_unlocked_count( $achievement->id ) );

	update_site_option( 'achievements_meta', apply_filters( 'dpa_remove_user_data_update_achievement_meta', $meta, $user_id ) );
	do_action( 'dpa_remove_user_data', $user_id );
}
add_action( 'wpmu_delete_user', 'dpa_remove_user_data', 1, 1 );
add_action( 'delete_user', 'dpa_remove_user_data', 1, 1 );
add_action( 'make_spam_user', 'dpa_remove_user_data', 1, 1 );

/**
 * This function works around a bug present in BuddyPress 1.2.6 and earlier where the 'make_spam_user' action was
 * not firing on non-multisite installs when the user was marked as a spammer from the buddybar.
 *
 * @param int $user_id Spammer's user ID
 * @param bool $is_spam
 * @see bp_core_action_set_spammer_status()
 * @since 2.0.2
 */
function dpa_user_marked_as_spammer( $user_id, $is_spam ) {
	if ( $is_spam )
		dpa_remove_user_data( $user_id );
}
add_action( 'bp_core_action_set_spammer_status', 'dpa_user_marked_as_spammer', 10, 2 );

/**
 * Deletes highscore cache for all widgets.
 *
 * @since 2.0
 */
function dpa_delete_highscore_cache() {
	$sitewide_widget_options = get_option( 'widget_achievements-sitewide' );
	foreach ( (array) $sitewide_widget_options as $widget ) {
		if ( empty( $widget ) )
			continue;

		if ( !empty( $widget->limit ) )
			wp_cache_delete( 'dpa_high_scorers_' . apply_filters( 'dpa_widget_limit', $widget->limit ), 'dpa' );
	}
}

/**
 * When someone unlocks an Achievement, create/update the relevant metas.
 *
 * @param int $achievement_id
 * @param int $user_id
 * @since 2.0
 */
function dpa_achievement_unlocked_update_meta( $achievement_id, $user_id ) {
	$meta = dpa_get_achievements_meta();
	$meta[$achievement_id]['no_of_unlocks'] += apply_filters( 'dpa_achievement_unlocked_update_meta', 1 );

	update_site_option( 'achievements_meta', $meta );
	wp_cache_delete( 'dpa_achievements_meta', 'dpa' );
	dpa_delete_highscore_cache();
}
add_action( 'dpa_achievement_unlocked', 'dpa_achievement_unlocked_update_meta', 5, 2 );

/**
 * Restores BuddyPress Groups global settings after the activity stream loop on the Achievement's page.
 * Or, in other words, tidies up after a hack. Ahem.
 *
 * @deprecated 2.1 Not used any more
 * @global object $bp BuddyPress global settings
 * @see dpa_achievement_activity_filter
 * @see dpa_screen_achievement_activity
 * @since 2.0
 */
function dpa_activity_screen_restore_settings() {
	global $bp;

	_deprecated_function( __FUNCTION__, '2.1' );
	$bp->groups->id = $bp->achievements->old_groups_id;

	if ( bp_is_active( 'groups' ) && isset( $bp->achievements->old_current_group_id ) ) {
		$bp->groups->current_group->id = $bp->achievements->old_current_group_id;
		$bp->groups->current_group->status = $bp->achievements->old_current_group_status;

	} elseif ( bp_is_active( 'groups' ) ) {
		$bp->groups->current_group = null;
	} else {
		$bp->groups = null;
	}
}

/**
 * Replaces a string in the internationalisation table with a custom value.
 *
 * @global object $l10n List of domain translated string (gettext_reader) objects
 * @param string $find Text to find in the table
 * @param string $replace Replacement text
 * @since 2.0
 */
function dpa_override_i18n( $find, $replace ) {
	global $l10n;

	if ( isset( $l10n['buddypress'] ) && isset( $l10n['buddypress']->entries[$find] ) ) {
		$l10n['buddypress']->entries[$find]->translations[0] = $replace;

	} else {
		$mo = new MO();
		$mo->add_entry( array( 'singular' => $find, 'translations' => array( $replace ) ) );

		if ( isset( $l10n['buddypress'] ) )
			$mo->merge_with( $l10n['buddypress'] );

		$l10n['buddypress'] = $mo;
	}
}

/**
 * Changes the 'no activity' message on an achievement's activity page.
 *
 * @since 2.0
 */
function dpa_achievement_activity_il8n_filter() {
	$find = __( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' );  // Intentionally uses BuddyPress' text domain.
	$replace = __( 'Sorry, there was no activity found.', 'dpa' );
	dpa_override_i18n( $find, $replace );
}

/**
 * Hijacks the activity stream on an achievement's activity page so only achievement items are displayed.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.1
 */
function dpa_achievement_activity_filter( $query_string ) {
	global $bp;

	if ( bp_is_current_component( $bp->achievements->slug ) && !empty( $bp->current_action ) )
		$query_string = 'object=achievements&primary_id=' . $bp->achievements->current_achievement->id;

	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'dpa_achievement_activity_filter' );


/********************************************************************************
 * Achievements
 *
 * These functions implement the main logic.
 */

/**
 * We have to (always) register some actions early due to the BuddyPress load order.
 * This won't add extra queries to each page, it'll only add action hooks; the queries are only made
 * if the action is called.
 *
 * The other difference between this and dpa_setup_achievements() is that this one doesn't abort if
 * the user is not logged in. If you find a reason to want to hook into this function, then we're
 * probably doing something wrong. Let me know what you're trying to do and we'll see if core can be improved.
 *
 * @since 2.0
 */
function dpa_setup_achievements_early() {
	// v2.2 - bp_core_activated_user doesn't work because the user is not logged in when we get to the handling functions
	$actions = apply_filters( 'dpa_setup_achievements_early', array( 'bp_core_activated_user', 'pending_to_publish' ) );
	foreach ( $actions as $action )
		add_action( $action, 'dpa_handle_action_' . $action, 10, 10 );
}
add_action( 'dpa_init', 'dpa_setup_achievements_early' );

/**
 * Hooks into the relevant WordPress actions which Achievements need to make its magic work.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_setup_achievements() {
	global $bp;

	$actions = apply_filters( 'dpa_setup_achievements', dpa_get_active_actions() );
	foreach ( $actions as $action )
		add_action( $action->name, 'dpa_handle_action_' . $action->name, 10, 10 );
}
add_action( 'bp_init', 'dpa_setup_achievements', 9 );

/**
 * Implements the Achievement actions, and unlocks if criteria met.
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global int $blog_id Site ID (variable is from WordPress and hasn't been updated for 3.0; confusing name is confusing)
 * @global object $bp BuddyPress global settings
 * @param string $name Action name
 * @param array $func_args Optional; action's arguments, from func_get_args().
 * @param string $type Optional; if the Achievement's action is called from within the loop in this function (i.e. points awarded), set this to "latent" to avoid a single Achievement being awarded mulitple times.
 * @see dpa_setup_achievements()
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_handle_action( $name, $func_args=null, $type='' ) {
	global $achievements_template, $blog_id, $bp;

	do_action( 'dpa_before_handle_action', $name, $func_args, $type );

	if ( !$name = apply_filters( 'dpa_handle_action', $name, $func_args, $type ) )
		return;

	$user_id = apply_filters( 'dpa_handle_action_user_id', $bp->loggedin_user->id, $name, $func_args, $type );
	if ( false === $user_id || empty( $user_id ) )
		return;

	if ( 'latent' == $type ) {
		$latent_achievements = DPA_Achievement::get( array( 'user_id' => $user_id, 'type' => 'active_by_action', 'action' => $name ) );
		$achievements_template->achievements = array_merge( $achievements_template->achievements, $latent_achievements['achievements'] );
		$achievements_template->achievement_count += count( $latent_achievements['achievements'] );

		do_action( 'dpa_handle_latent_action', $name, $func_args, $is_latent, $latent_achievements );

		// Avoid duplicate invocations of latent Achievement actions
		if ( 'dpa_points_incremented' == $name )
			remove_action( 'dpa_points_incremented', 'dpa_handle_action_dpa_points_incremented', 10, 10 );

		if ( 'dpa_achievement_unlocked' == $name )
			remove_action( 'dpa_achievement_unlocked', 'dpa_handle_action_dpa_achievement_unlocked', 10, 10 );

		return;
	}

	// This is to support plugins which use the 'dpa_handle_action_user_id' filter to return an array of user IDs.
	if ( is_array( $user_id ) )
		$user_ids = $user_id;
	else
		$user_ids = array( $user_id );

	foreach ( $user_ids as $user_id ) {
		if ( dpa_has_achievements( array( 'user_id' => $user_id, 'type' => 'active_by_action', 'action' => $name ) ) ) {
			while ( dpa_achievements() ) {
				dpa_the_achievement();

				$site_id = apply_filters( 'dpa_handle_action_site_id', dpa_get_achievement_site_id(), $name, $func_args, $type, $user_id );
				if ( false === $site_id )
					continue;

				$site_is_valid = false;
				if ( !is_multisite() || $site_id < 1 || $blog_id == $site_id )
					$site_is_valid = true;

				$group_is_valid = false;
				if ( dpa_get_achievement_group_id() < 1 || dpa_is_group_achievement_valid( $name, $func_args, $user_id ) )
					$group_is_valid = true;

				$site_is_valid = apply_filters( 'dpa_handle_action_site_is_valid', $site_is_valid, $name, $func_args, $type, $user_id );
				$group_is_valid = apply_filters( 'dpa_handle_action_group_is_valid', $group_is_valid, $name, $func_args, $type, $user_id );

				if ( $site_is_valid && $group_is_valid )
					dpa_maybe_unlock_achievement( $user_id );
			}
		}
	}

	do_action( 'dpa_after_handle_action', $name, $func_args, $type );
	$achievements_template = null;
}

/**
 * Checks if an Achievement's criteria has been met, and if it has, unlock the Achievement.
 * Achievements with an action count of 0 are, effectively, unlocked each time but only
 * the points are added to user's total.
 *
 * @global object $bp BuddyPress global settings
 * @param int $user_id
 * @param string $skip_validation Set to 'force' to skip Achievement validation, e.g. the Achievement is unlocked regardless of its criteria.
 * @since 2.0
 */
function dpa_maybe_unlock_achievement( $user_id, $skip_validation='' ) {
	global $bp;

	$action_count = dpa_get_achievement_action_count();
	if ( dpa_is_achievement_unlocked() && $action_count > 0 )
		return;

	$counters = get_user_meta( $user_id, 'achievements_counters', true );
	$achievement_id = dpa_get_achievement_id();
	$skip_validation = ( 'force' == $skip_validation );

	// No point saving a count of 1 if the action_count is 1.
	$unlocked = false;
	if ( 0 === $action_count || 1 == $action_count ) {
		$unlocked = true;

	} elseif ( !$skip_validation ) {
		if ( !$counters && !is_array( $counters ) )
			$counters = array();

		$counters[$achievement_id] += apply_filters( 'dpa_achievement_counter_increment_by', 1 );
		update_user_meta( $user_id, 'achievements_counters', $counters );
		do_action( 'dpa_achievement_counter_incremented' );
	}

	if ( !$unlocked && ( $skip_validation || $counters[$achievement_id] >= $action_count ) ) {
		if ( isset( $counters[$achievement_id] ) ) {
			unset( $counters[$achievement_id] );
			update_user_meta( $user_id, 'achievements_counters', $counters );
		}

		$unlocked = true;
	}

	// Update points, insert unlocked record into DB and send notifications.
	if ( $unlocked ) {
		dpa_points_increment( dpa_get_achievement_points(), $user_id );

		// Let Achievements with action_count == 0, which have already been unlocked, only increment points.
		if ( dpa_is_achievement_unlocked() && 0 === $action_count )
			return;

		dpa_unlock_achievement( $user_id );

		if ( apply_filters( 'dpa_achievement_unlocked_tell_user', true, $achievement_id, $user_id ) ) {
			bp_core_add_notification( $achievement_id, $user_id, $bp->achievements->id, 'new_achievement', $user_id );
			dpa_record_activity( $user_id, dpa_format_activity( $user_id, $achievement_id ), $achievement_id );
		}

		do_action( 'dpa_achievement_unlocked', $achievement_id, $user_id );
	}
}

/**
 * Use this to unlock an Achievement for the specified user, ignoring the Achievement's criteria.
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global object $bp BuddyPress global settings
 * @param int $user_id Optional
 * @param string $achievement_slug Optional; defaults to the Achievement currently being viewed
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_force_unlock_achievement( $user_id=0, $achievement_slug='' ) {
	global $achievements_template, $bp;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	if ( !$achievement_slug )
		$achievement_slug = apply_filters( 'dpa_get_achievement_slug', $bp->current_item );

	if ( !empty( $achievements_template->achievement ) )
		$original_achievement = $achievements_template->achievement;

	if ( !empty( $achievements_template->achievements ) )
		$original_achievements = $achievements_template->achievements;

  // Can't use $bp->achievements_current_achievement as that achievement's populate_extras meta is for the logged-in user (or doesn't exist).
	$achievements_template->achievement  = new DPA_Achievement( array( 'type' => 'single', 'slug' => $achievement_slug, 'user_id' => $user_id ) );
	$achievements_template->achievements = array( $achievements_template->achievement );

	dpa_maybe_unlock_achievement( $user_id, 'force' );

	if ( isset( $original_achievements ) )
		$achievements_template->achievements = $original_achievements;

	if ( isset( $original_achievement ) )
		$achievements_template->achievement = $original_achievement;
}

/**
 * Implements the comment_post action for WordPress
 *
 * @since 2.0
 */
function dpa_handle_action_comment_post() { $func_get_args = func_get_args(); dpa_handle_action( 'comment_post', $func_get_args ); }

/**
 * Implements the draft_to_publish (posts, pages) action for WordPress
 *
 * @since 2.0
 */
function dpa_handle_action_draft_to_publish() { $func_get_args = func_get_args(); dpa_handle_action( 'draft_to_publish', $func_get_args ); }

/**
 * Implements the pending_to_publish (posts, pages) action for WordPress. Bit of a kludge that works in conjunction with dpa_handle_action_draft_to_publish().
 *
 * @see dpa_handle_action_draft_to_publish()
 * @since 2.0
 */
function dpa_handle_action_pending_to_publish() { $func_get_args = func_get_args(); dpa_handle_action( 'draft_to_publish', $func_get_args ); }

/**
 * Implements the friends_friendship_requested action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_friends_friendship_requested() { $func_get_args = func_get_args(); dpa_handle_action( 'friends_friendship_requested', $func_get_args ); }

/**
 * Implements the groups_invite_user action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_invite_user() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_invite_user', $func_get_args ); }

/**
 * Implements the groups_join_group action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_join_group() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_join_group', $func_get_args ); }

/**
 * Implements the groups_promoted_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_promoted_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_promoted_member', $func_get_args ); }

/**
 * Implements the groups_demoted_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_demoted_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_demoted_member', $func_get_args ); }

/**
 * Implements the groups_banned_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_banned_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_banned_member', $func_get_args ); }

/**
 * Implements the groups_unbanned_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_unbanned_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_unbanned_member', $func_get_args ); }

/**
 * Implements the groups_premote_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_premote_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_premote_member', $func_get_args ); }

/**
 * Implements the groups_demote_member action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_demote_member() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_demote_member', $func_get_args ); }

/**
 * Implements the messages_message_sent action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_messages_message_sent() { $func_get_args = func_get_args(); dpa_handle_action( 'messages_message_sent', $func_get_args ); }

/**
 * Implements the xprofile_updated_profile action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_xprofile_updated_profile() { $func_get_args = func_get_args(); dpa_handle_action( 'xprofile_updated_profile', $func_get_args ); }

/**
 * Implements the bp_core_activated_user action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_core_activated_user() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_core_activated_user', $func_get_args ); }

/**
 * Implements the xprofile_avatar_uploaded action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_xprofile_avatar_uploaded() { $func_get_args = func_get_args(); dpa_handle_action( 'xprofile_avatar_uploaded', $func_get_args ); }

/**
 * Implements the friends_friendship_accepted action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_friends_friendship_accepted() { $func_get_args = func_get_args(); dpa_handle_action( 'friends_friendship_accepted', $func_get_args ); }

/**
 * Implements the friends_friendship_rejected action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_friends_friendship_rejected() { $func_get_args = func_get_args(); dpa_handle_action( 'friends_friendship_rejected', $func_get_args ); }

/**
 * Implements the trashed_post action for WordPress
 *
 * @since 2.0
 */
function dpa_handle_action_trashed_post() { $func_get_args = func_get_args(); dpa_handle_action( 'trashed_post', $func_get_args ); }

/**
 * Implements the messages_delete_thread action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_messages_delete_thread() { $func_get_args = func_get_args(); dpa_handle_action( 'messages_delete_thread', $func_get_args ); }

/**
 * Implements the friends_friendship_deleted action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_friends_friendship_deleted() { $func_get_args = func_get_args(); dpa_handle_action( 'friends_friendship_deleted', $func_get_args ); }

/**
 * Implements the groups_create_group action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_create_group() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_create_group', $func_get_args ); }

/**
 * Implements the groups_leave_group action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_leave_group() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_leave_group', $func_get_args ); }

/**
 * Implements the groups_delete_group action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_delete_group() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_delete_group', $func_get_args ); }

/**
 * Implements the groups_new_forum_topic action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_new_forum_topic() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_new_forum_topic', $func_get_args ); }

/**
 * Implements the groups_new_forum_topic_post action for Achievements
 *
 * @since 2.0
 */
function dpa_handle_action_groups_new_forum_topic_post() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_new_forum_topic_post', $func_get_args ); }

/**
 * Implements the groups_delete_group_forum_post action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_delete_group_forum_post() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_delete_group_forum_post', $func_get_args ); }

/**
 * Implements the groups_delete_group_forum_topic action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_delete_group_forum_topic() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_delete_group_forum_topic', $func_get_args ); }

/**
 * Implements the groups_update_group_forum_post action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_groups_update_group_forum_post() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_update_group_forum_post', $func_get_args ); }

/**
 * Implements the groups_update_group_forum_topic action for Achievements
 *
 * @since 2.0
 */
function dpa_handle_action_groups_update_group_forum_topic() { $func_get_args = func_get_args(); dpa_handle_action( 'groups_update_group_forum_topic', $func_get_args ); }

/**
 * Implements the bp_groups_posted_update action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_groups_posted_update() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_groups_posted_update', $func_get_args ); }

/**
 * Implements the bp_activity_posted_update action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_activity_posted_update() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_activity_posted_update', $func_get_args ); }

/**
 * Implements the bp_activity_comment_posted action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_activity_comment_posted() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_activity_comment_posted', $func_get_args ); }

/**
 * Implements the signup_finished action for WordPress
 *
 * @since 2.0
 */
function dpa_handle_action_signup_finished() { $func_get_args = func_get_args(); dpa_handle_action( 'signup_finished', $func_get_args ); }

/**
 * Implements the dpa_points_incremented action for Achievements
 *
 * @since 2.0
 */
function dpa_handle_action_dpa_points_incremented() { $func_get_args = func_get_args(); dpa_handle_action( 'dpa_points_incremented', $func_get_args, 'latent' ); }

/**
 * Implements the dpa_achievement_unlocked action for Achievements
 *
 * @since 2.0
 */
function dpa_handle_action_dpa_achievement_unlocked() { $func_get_args = func_get_args(); dpa_handle_action( 'dpa_achievement_unlocked', $func_get_args, 'latent' ); }

/**
 * Implements the publish_ep_event action for EventPress
 *
 * @since 2.0
 */
function dpa_handle_action_publish_ep_event() { $func_get_args = func_get_args(); dpa_handle_action( 'publish_ep_event', $func_get_args ); }

/**
 * Implements the publish_ep_reg action for EventPress
 *
 * @since 2.0
 */
function dpa_handle_action_reg_approved_ep_reg() { $func_get_args = func_get_args(); dpa_handle_action( 'reg_approved_ep_reg', $func_get_args ); }

/**
 * Implements the bp_moderation_content_status_changed action for BuddyPress Moderation
 *
 * @since 2.0
 */
function dpa_handle_action_bp_moderation_content_status_changed() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_moderation_content_status_changed', $func_get_args ); }

/**
 * Implements the bp_moderation_content_flagged action for BuddyPress Moderation
 *
 * @since 2.0
 */
function dpa_handle_action_bp_moderation_content_flagged() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_moderation_content_flagged', $func_get_args ); }

/**
 * Implements the bp_moderation_content_unflagged action for BuddyPress Moderation
 *
 * @since 2.0
 */
function dpa_handle_action_bp_moderation_content_unflagged() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_moderation_content_unflagged', $func_get_args ); }

/**
 * Implements the bp_privacy_update_privacy_settings action for BuddyPress Privacy
 *
 * @since 2.0
 */
function dpa_handle_action_bp_privacy_update_privacy_settings() { $func_get_args = func_get_args(); dpa_handle_action( 'dpa_handle_action_bp_privacy_update_privacy_settings', $func_get_args ); }

/**
 * Implements the bp_activity_add_user_favorite action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_activity_add_user_favorite() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_activity_add_user_favorite', $func_get_args ); }

/**
 * Implements the bp_activity_remove_user_favorite action for BuddyPress
 *
 * @since 2.0
 */
function dpa_handle_action_bp_activity_remove_user_favorite() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_activity_remove_user_favorite', $func_get_args ); }

/**
 * Implements the buddystream_twitter_activated action for BuddyStream
 *
 * @since 2.0
 */
function dpa_handle_action_buddystream_twitter_activated() { $func_get_args = func_get_args(); dpa_handle_action( 'buddystream_twitter_activated', $func_get_args ); }

/**
 * Implements the buddystream_facebook_activated action for BuddyStream
 *
 * @since 2.0
 */
function dpa_handle_action_buddystream_facebook_activated() { $func_get_args = func_get_args(); dpa_handle_action( 'buddystream_facebook_activated', $func_get_args ); }

/**
 * Implements the buddystream_flickr_activated action for BuddyStream
 *
 * @since 2.0
 */
function dpa_handle_action_buddystream_flickr_activated() { $func_get_args = func_get_args(); dpa_handle_action( 'buddystream_flickr_activated', $func_get_args ); }

/**
 * Implements the buddystream_youtube_activated action for BuddyStream
 *
 * @since 2.0
 */
function dpa_handle_action_buddystream_youtube_activated() { $func_get_args = func_get_args(); dpa_handle_action( 'buddystream_youtube_activated', $func_get_args ); }

/**
 * Implements the buddystream_lastfm_activated action for BuddyStream
 *
 * @since 2.0
 */
function dpa_handle_action_buddystream_lastfm_activated() { $func_get_args = func_get_args(); dpa_handle_action( 'buddystream_lastfm_activated', $func_get_args ); }

/**
 * Implements the accepted_email_invite action for Invite Anyone
 *
 * @since 2.0
 */
function dpa_handle_action_accepted_email_invite() { $func_get_args = func_get_args(); dpa_handle_action( 'accepted_email_invite', $func_get_args ); }

/**
 * Implements the sent_email_invite action for Invite Anyone
 *
 * @since 2.0
 */
function dpa_handle_action_sent_email_invite() { $func_get_args = func_get_args(); dpa_handle_action( 'sent_email_invite', $func_get_args ); }

/**
 * Implements the bp_links_cast_vote_success action for BuddyPress Links
 *
 * @since 2.0.1
 */
function dpa_handle_action_bp_links_cast_vote_success() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_links_cast_vote_success', $func_get_args ); }

/**
 * Implements the bp_links_delete_link action for BuddyPress Links
 *
 * @since 2.0.1
 */
function dpa_handle_action_bp_links_delete_link() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_links_delete_link', $func_get_args ); }

/**
 * Implements the bp_links_posted_update action for BuddyPress Links
 *
 * @since 2.0.1
 */
function dpa_handle_action_bp_links_posted_update() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_links_posted_update', $func_get_args ); }

/**
 * Implements the bp_links_create_complete action for BuddyPress Links
 *
 * @since 2.0.2
 */
function dpa_handle_action_bp_links_create_complete() { $func_get_args = func_get_args(); dpa_handle_action( 'bp_links_create_complete', $func_get_args ); }

/**
 * Implements the events_event_create_complete action for Jet Event System for BuddyPress
 *
 * @since 2.0.2
 */
function dpa_handle_action_events_event_create_complete() { $func_get_args = func_get_args(); dpa_handle_action( 'events_event_create_complete', $func_get_args ); }

/**
 * Implements the events_join_event action for Jet Event System for BuddyPress
 *
 * @since 2.0.2
 */
function dpa_handle_action_events_join_event() { $func_get_args = func_get_args(); dpa_handle_action( 'events_join_event', $func_get_args ); }

/**
 * Implements the events_leave_event action for Jet Event System for BuddyPress
 *
 * @since 2.0.2
 */
function dpa_handle_action_events_leave_event() { $func_get_args = func_get_args(); dpa_handle_action( 'events_leave_event', $func_get_args ); }

/**
 * Implements the events_event_deleted action for Jet Event System for BuddyPress
 *
 * @since 2.0.2
 */
function dpa_handle_action_events_event_deleted() { $func_get_args = func_get_args(); dpa_handle_action( 'events_event_deleted', $func_get_args ); }

/**
 * Implements the wp_login action for BuddyPress
 *
 * @since 2.0.5
 */
function dpa_handle_action_wp_login() { $func_get_args = func_get_args(); dpa_handle_action( 'wp_login', $func_get_args ); }

/**
 * For the wp_login action, manually retrieve the user ID as it won't be conventionally accessible until the next page load.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|bool User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0.5
 */
function dpa_filter_wp_login_user_id( $user_id, $action_name, $action_func_args ) {
	if ( 'wp_login' != $action_name )
		return $user_id;

	if ( empty( $action_func_args[0] ) || is_wp_error( $action_func_args[0] ) )
		return $user_id;

	$the_user_id = bp_core_get_userid( $action_func_args[0] );
	if ( !$the_user_id )
		return $user_id;

	return apply_filters( 'dpa_filter_wp_login_user_id', (int)$the_user_id );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_wp_login_user_id', 10, 3 );

/**
 * For the draft_to_publish (posts, pages) action, swap the (logged in) user's ID for the ID of the post author.
 * This accommodates post moderation/publishing by admin users.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|array User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_draft_to_publish_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'draft_to_publish' != $action_name )
		return $user_id;

	$post = $action_func_args[0];
	if ( 'post' != $post->post_type && 'page' != $post->post_type )
		return $user_id;

	return apply_filters( 'dpa_filter_draft_to_publish_action_userid', (int)$post->post_author );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_draft_to_publish_action_userid', 10, 3 );

/**
 * For the bp_moderation_content_status_changed action, swap the (logged in) user's ID for the ID of the reporter.
 * This may be an array of user IDs, as multiple users can report the same content and all deserve recognition!
 *
 * Contents of $action_func_args are:
 * 0 = <int> $bp_moderation_content_id
 * 1 = <string> $old_status,
 * 2 = <string> $new_status,
 * 3 = <int> $content_author_id
 * 4 = <array<int>> $ids_of_reporters
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|array User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_bp_moderation_content_status_changed_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'bp_moderation_content_status_changed' != $action_name )
		return $user_id;

	$valid_statuses = array( 'moderated', 'edited', 'deleted' );
	if ( in_array( $action_func_args[1], $valid_statuses ) )
		return false;

	if ( !in_array( $action_func_args[2], $valid_statuses ) )
		return false;

	if ( !is_array( $action_func_args[4] ) )
		$reporter_user_ids = array( $action_func_args[4] );
	else
		$reporter_user_ids = $action_func_args[4];

	$valid_user_ids = array();
	foreach ( $reporter_user_ids as $user_id ) {
		if ( !$user = get_userdata( $user_id ) )
			continue;

		$valid_user_ids[] = $user->ID;
	}

	return apply_filters( 'dpa_filter_bp_moderation_content_status_changed_action_userid', $valid_user_ids );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_bp_moderation_content_status_changed_action_userid', 10, 3 );

/**
 * For the comment_post action, swap the (logged in) user's ID for the ID of the comment author.
 * This accommodates comment moderation.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|bool User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_comment_post_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'comment_post' != $action_name )
		return $user_id;

	if ( ( !$comment = get_comment( $action_func_args[0] ) ) || !$comment->user_id )
		return $user_id;

	// Bail if comment isn't approved
	if ( 1 != $action_func_args[1]  )
		return false;

	return apply_filters( 'dpa_filter_comment_post_action_userid', (int)$comment->user_id );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_comment_post_action_userid', 10, 3 );

/**
 * For the groups_premote_member action, swap the (logged in) user's ID for the ID of the promoted member.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|bool User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_groups_premote_member_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'groups_premote_member' != $action_name )
		return $user_id;

	return apply_filters( 'dpa_filter_groups_premote_member_action_userid', (int)$action_func_args[1] );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_groups_premote_member_action_userid', 10, 3 );

/**
 * For the groups_demote_member action, swap the (logged in) user's ID for the ID of the demoted member.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|bool User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_groups_demote_member_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'groups_demote_member' != $action_name )
		return $user_id;

	return apply_filters( 'dpa_filter_groups_demote_member_action_userid', (int)$action_func_args[1] );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_groups_demote_member_action_userid', 10, 3 );

/**
 * For the bp_core_activated_user action, get the user ID from the function arguments as the user
 * isn't logged in yet.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|array User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_bp_core_activated_user_action_userid( $user_id, $action_name, $action_func_args ) {
	if ( 'bp_core_activated_user' != $action_name )
		return $user_id;

	return apply_filters( 'dpa_filter_bp_core_activated_user_action_userid', (int)$action_func_args[0] );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_bp_core_activated_user_action_userid', 10, 3 );

/**
 * For the accepted_email_invite action from Invite Anyone, get the user ID from the function arguments as
 * the user isn't logged in yet.
 *
 * @param int $user_id
 * @param string $action_name
 * @param array $action_func_args The action's arguments, from func_get_args().
 * @return int|array User ID or, if false is returned, this Achievement will be skipped.
 * @since 2.0
 */
function dpa_filter_accepted_email_invite( $user_id, $action_name, $action_func_args ) {
	if ( 'accepted_email_invite' != $action_name )
		return $user_id;

	return apply_filters( 'dpa_filter_accepted_email_invite', (int)$action_func_args[0] );
}
add_filter( 'dpa_handle_action_user_id', 'dpa_filter_accepted_email_invite', 10, 3 );

/**
 * Does the group_id of this Achievement match the ID of the group that the action being processed belongs to?
 *
 * @global object $bp BuddyPress global settings
 * @param string $action_name
 * @param array $func_args Optional; action's arguments, from func_get_args().
 * @param int $user_id User ID of that the current Achievement is being actioned on.
 * @return bool
 * @since 2.0
 */
function dpa_is_group_achievement_valid( $action_name, $func_args, $user_id ) {
	global $bp;

	$group_id = 0;

	switch ( $action_name ) {
		case 'groups_invite_user':
			$group_id = $func_args[0]['group_id'];
		break;

		case 'groups_join_group':
		case 'groups_create_group':
		case 'groups_leave_group':
		case 'groups_delete_group':
		case 'groups_new_forum_topic':
		case 'groups_new_forum_topic_post':
			$group_id = $func_args[0];
		break;

		case 'groups_promoted_member':
			$group_id = $func_args[1];
		break;

		case 'bp_groups_posted_update':
			$group_id = $func_args[2];
		break;

		case 'groups_delete_group_forum_post':
		case 'groups_delete_group_forum_topic':
		case 'groups_update_group_forum_post':
		case 'groups_update_group_forum_topic':
			$group_id = $bp->groups->current_group->id;
		break;
	}

	$is_valid = ( dpa_get_achievement_group_id() == (int)$group_id && (int)$group_id );
	return apply_filters( 'dpa_is_group_achievement_valid', $is_valid, $action_name, $func_args, $user_id );
}


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * Loads an Achievement's activity stream's RSS feed
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_action_achievement_feed() {
	global $bp;

	if ( !bp_is_current_component( $bp->achievements->slug ) || !$bp->loggedin_user->id || !$bp->is_single_item || DPA_SLUG_ACHIEVEMENT_ACTIVITY_RSS != $bp->current_action )
		return;

	$wp_query->is_404 = false;
	status_header( 200 );
	dpa_achievement_activity_il8n_filter();

	include_once( 'feeds/dpa-myachievements-feed.php' );
	die;
}
add_action( 'bp_setup_nav', 'dpa_action_achievement_feed', 11 );

/**
 * Sets up the Achievement Directory page
 *
 * @global object $bp BuddyPress global settings
 * @global bool $is_member_page If we are under anything with a members slug
 * @since 2.0
 */
function dpa_setup_nav_directory() {
	global $bp, $is_member_page;

	if ( bp_is_current_component( $bp->achievements->slug ) && !$bp->current_action && !$bp->current_item && !$is_member_page ) {
		$bp->is_directory = true;

		if ( $bp->loggedin_user->id )
			bp_core_delete_notifications_by_type( $bp->loggedin_user->id, $bp->achievements->id, 'new_achievement' );

		do_action( 'dpa_setup_nav_directory' );
		bp_core_load_template( apply_filters( 'achievements_template_directory', 'achievements/index' ) );
		return;
	}
}
add_action( 'wp', 'dpa_setup_nav_directory', 2 );

/**
 * Load a user's "my achievements" page.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_screen_my_achievements() {
	global $bp;

	if ( $bp->loggedin_user->id )
		bp_core_delete_notifications_by_item_id( $bp->loggedin_user->id, $bp->achievements->current_achievement->id, $bp->achievements->id, 'new_achievement' );

	do_action( 'dpa_screen_my_achievements' );
	bp_core_load_template( apply_filters( 'dpa_screen_my_achievements_template', 'members/single/achievements' ) );
}

/**
 * Loads an Achievement's home (or activity stream) page.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_screen_achievement_activity() {
	global $bp;

	if ( $bp->loggedin_user->id )
		bp_core_delete_notifications_by_item_id( $bp->loggedin_user->id, $bp->achievements->current_achievement->id, $bp->achievements->id, 'new_achievement' );

	do_action( 'dpa_screen_achievement_activity' );
	bp_core_load_template( apply_filters( 'dpa_screen_achievement_activity_template', 'achievements/single/home' ) );
}

/**
 * Loads an Achievement's "unlocked by" page.
 *
 * @global object $bp BuddyPress global settings
 * @see dpa_filter_users_by_achievement() for the matching remove_filter calls to prevent conflict with regular use of the members template loop.
 * @since 2.0
 */
function dpa_screen_achievement_unlocked_by() {
	add_filter( 'bp_core_get_total_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );
	add_filter( 'bp_core_get_paged_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );
	add_filter( 'bp_member_last_active', 'dpa_filter_unlockedby_activity_timestamp' );
	add_action( 'bp_directory_members_actions', 'dpa_member_achievements_link' );
	add_action( 'bp_after_members_loop', 'dpa_remove_filters_after_members_loop' );

	do_action( 'dpa_screen_achievement_unlocked_by' );
	bp_core_load_template( apply_filters( 'dpa_screen_achievement_details_template', 'achievements/single/home' ) );
}

/**
 * Loads an Achievement's change picture page. Also implements controller logic for updating the graphic.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_screen_achievement_change_picture() {
	global $bp;

	if ( empty( $_POST['achievement-change-picture'] ) || empty( $_POST['picture_id'] ) ) {
		do_action( 'dpa_screen_achievement_change_picture' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_change_picture_template', 'achievements/single/home' ) );
		return;
	}

  if ( !wp_verify_nonce( $_POST['_wpnonce'], 'achievement-change-picture-' . $bp->current_item ) ) {
		wp_nonce_ays( '' );
		die();
  }

  $bp->achievements->current_achievement = new DPA_Achievement( array( 'slug' => apply_filters( 'dpa_get_achievement_slug', $bp->current_item ), 'populate_extras' => false ) );
	$achievement =& $bp->achievements->current_achievement;
	$achievement->picture_id = apply_filters( 'dpa_get_achievement_picture_id', (int)$_POST['picture_id'] );

	$achievements_errors = $achievement->save();
	if ( !is_wp_error( $achievements_errors ) ) {
		do_action( 'dpa_screen_achievement_change_picture_success', $achievement );
		bp_core_add_message( __( "The Achievement's picture has been updated.", 'dpa' ) );
		bp_core_redirect( dpa_get_achievements_permalink() . '/' . $achievement->slug . '/' );

	} else {
		do_action( 'dpa_screen_achievement_change_picture_fail', $achievement, $achievements_errors );
		bp_core_add_message( __( "There was an error changing the Achievement's picture, please try again.", 'dpa' ), 'error' );
		bp_core_redirect( dpa_get_achievements_permalink() . '/' . $achievement->slug . '/' );
	}
}

/**
 * Loads an Achievement's edit page. Also implements controller logic.
 *
 * @global WP_Error $achievements_errors Achievement creation error object
 * @global object $bp BuddyPress global settings
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_screen_achievement_edit() {
	global $achievements_errors, $bp;

  if ( empty( $_POST['achievement-edit'] ) ) {
	  $bp->achievements->current_achievement = new DPA_Achievement( array( 'slug' => apply_filters( 'dpa_get_achievement_slug', $bp->current_item ), 'populate_extras' => false ) );

		do_action( 'dpa_screen_achievement_edit' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_edit_template', 'achievements/single/home' ) );
		return;
  }

  // Edit
  if ( !wp_verify_nonce( $_POST['_wpnonce'], 'achievement-edit-' . $bp->current_item ) ) {
		wp_nonce_ays( '' );
		die();
  }

  $bp->achievements->current_achievement = new DPA_Achievement( array( 'slug' => apply_filters( 'dpa_get_achievement_slug', $bp->current_item ), 'populate_extras' => false ) );
	$old_achievement = wp_clone( $bp->achievements->current_achievement );
	$achievement =& $bp->achievements->current_achievement;

	/* We can't use template tags because if the new details fail validation and do not save, the template loop will fetch the old version. */
	if ( 'badge' == stripslashes( $_POST['achievement_type'] ) ) {
		$achievement->action_count = 1;
		$achievement->action_id    = -1;
	} else {
		$achievement->action_count = (int)$_POST['action_count'];
		$achievement->action_id    = (int)$_POST['action_id'];
	}

	if ( is_multisite() && bp_is_active( 'blogs' ) )
		$achievement->site_id      = (int)$_POST['site_id'];
	else
		$achievement->site_id      = BP_ROOT_BLOG;

	if ( bp_is_active( 'groups' ) )
		$achievement->group_id     = (int)$_POST['group_id'];
	else
		$achievement->group_id     = -1;

	if ( !empty( $_POST['is_hidden'] ) )
		$achievement->is_active    = 2;
	elseif ( !empty( $_POST['is_active'] ) )
		$achievement->is_active    = 1;
	else
		$achievement->is_active    = 0;

	$achievement->id             = (int)$achievement->id;
	$achievement->name           = stripslashes( $_POST['name'] );
	$achievement->description    = stripslashes( $_POST['description'] );
	$achievement->points         = (int)$_POST['points'];
	$achievement->slug           = stripslashes( $_POST['slug'] );

	$achievements_errors = $achievement->save( $old_achievement );
	if ( !is_wp_error( $achievements_errors ) ) {
		do_action( 'dpa_screen_achievement_edit_success', $achievement, $old_achievement );
		bp_core_add_message( __( "The Achievement's details have been updated.", 'dpa' ) );
		bp_core_redirect( dpa_get_achievements_permalink() . '/' . $achievement->slug . '/' );

	} else {
		if ( !$achievement->points )
			$achievement->points = '';

		if ( !$achievement->action_count )
			$achievement->action_count = '';

		do_action( 'dpa_screen_achievement_edit_fail', $achievement, $achievements_errors );
		bp_core_add_message( __( "An error has occurred and the Achievement's details have not been updated. See below for details.", 'dpa' ), 'error' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_edit_template', 'achievements/single/home' ) );
	}
}

/**
 * Loads an Achievement's grant page. Also implements controller logic.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_screen_achievement_grant() {
	global $bp;

	if ( empty( $_POST['achievement-grant'] ) ) {
		do_action( 'dpa_screen_achievement_grant' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_grant_template', 'achievements/single/home' ) );
		return;
	}

	if ( !empty( $_POST['achievement-grant'] ) && empty( $_POST['members'] ) || !is_array( $_POST['members'] ) ) {
		do_action( 'dpa_screen_achievement_grant_no_users' );
		bp_core_add_message( __( "You need to select at least one person to give this Achievement to!", 'dpa' ), 'error' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_grant_template', 'achievements/single/home' ) );
		return;
	}

  if ( !wp_verify_nonce( $_POST['_wpnonce_achievements_grant'], 'achievement_grant_' . $bp->current_item ) ) {
		wp_nonce_ays( '' );
		die();
  }

	foreach ( $_POST['members'] as $member_id ) {
		$member_id = (int)$member_id;
		if ( $member_id && get_userdata( $member_id ) )
			dpa_force_unlock_achievement( $member_id );
	}

	do_action( 'dpa_screen_achievement_grant_success' );
	bp_core_add_message( __( "The specified members have successfully received this Achievement.", 'dpa' ) );
	bp_core_redirect( dpa_get_achievements_permalink() . '/' . $bp->current_item . '/' );
}

/**
 * Loads an Achievement's delete page. Also implements controller logic.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_screen_achievement_delete() {
	global $bp;

	if ( empty( $_REQUEST['delete-achievement-button'] ) || empty( $_REQUEST['delete-achievement-understand'] ) ) {
		do_action( 'dpa_screen_achievement_delete' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_delete_template', 'achievements/single/home' ) );
		return;
	}

  if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'achievements-delete-achievement-' . $bp->current_item ) ) {
		wp_nonce_ays( '' );
		die();
  }

	// Delete Achievement
	$slug = $bp->current_item;
	if ( !$achievement_id = DPA_Achievement::delete( $slug ) ) {
		do_action( 'dpa_screen_achievement_delete_fail', $slug );
		bp_core_add_message( __( 'There was an error deleting the Achievement, please try again.', 'dpa' ), 'error' );
		bp_core_redirect( dpa_get_achievements_permalink() . '/' . $slug );

	} else {
		do_action( 'dpa_screen_achievement_delete_success', $slug, $achievement_id );

		// Remove all notifications for any user relating to this Achievement
		bp_core_delete_all_notifications_by_type( $achievement_id, $bp->achievements->id, 'new_achievement' );

		bp_core_add_message( __( 'The Achievement was deleted successfully.', 'dpa' ) );
		bp_core_redirect( dpa_get_achievements_permalink() );
	}
}

/**
 * Loads the create Achievement page. Also implements controller logic.
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global WP_Error $achievements_errors Achievement creation error object
 * @global object $bp BuddyPress global settings
 * @since 2.0
 * @uses DPA_Achievement
 */
function dpa_screen_achievement_create() {
	global $achievements_template, $achievements_errors, $bp, $current_blog;

	if ( !bp_is_current_component( $bp->achievements->slug ) || DPA_SLUG_CREATE != $bp->current_action || !dpa_permission_can_user_create() )
		return;

	$bp->achievements->current_achievement = new DPA_Achievement;
	$achievement =& $bp->achievements->current_achievement;

	// Has form been submitted?
	if ( empty( $_POST['achievement-create'] ) ) {
		$achievement->points       = '';
		$achievement->action_count = 1;
		$achievement->is_active    = 1;

		do_action( 'dpa_screen_achievement_create', $achievement );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_create_template', 'achievements/create' ) );
		return;
	}

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'achievement-create' ) ) {
		wp_nonce_ays( '' );
		die();
	}

	/* We can't use template tags because if the new details fail validation and do not save, the template loop will fetch the old version. */
	if ( 'badge' == stripslashes( $_POST['achievement_type'] ) ) {
		$achievement->action_count = 1;
		$achievement->action_id    = -1;
	} else {
		$achievement->action_count = (int)$_POST['action_count'];
		$achievement->action_id    = (int)$_POST['action_id'];
	}

	if ( is_multisite() && bp_is_active( 'blogs' ) )
		$achievement->site_id      = (int)$_POST['site_id'];
	else
		$achievement->site_id      = BP_ROOT_BLOG;

	if ( bp_is_active( 'groups' ) )
		$achievement->group_id     = (int)$_POST['group_id'];
	else
		$achievement->group_id     = -1;

	if ( !empty( $_POST['is_hidden'] ) )
		$achievement->is_active    = 2;
	elseif ( !empty( $_POST['is_active'] ) )
		$achievement->is_active    = 1;
	else
		$achievement->is_active    = 0;

	$achievement->name           = stripslashes( $_POST['name'] );
	$achievement->description    = stripslashes( $_POST['description'] );
	$achievement->points         = (int)$_POST['points'];
	$achievement->slug           = stripslashes( $_POST['slug'] );
	$achievement->picture_id     = -1;  // A pictures is chosen on its own page, after creation.

	$achievements_errors = $achievement->save();
	if ( !is_wp_error( $achievements_errors ) ) {
		$achievements_template->achievement = $achievement;  // Required for dpa_record_activity()

		if ( 1 == $achievement->is_active )
			dpa_record_activity( $bp->loggedin_user->id, dpa_format_activity( $bp->loggedin_user->id, $achievement->id ), $achievement->id, 'achievement_created' );

		bp_core_add_message( __( "Achievement created succesfully!", 'dpa' ) );
		do_action( 'dpa_screen_achievement_create_success', $achievement );

	if ( dpa_permission_can_user_change_picture() )
			bp_core_redirect( dpa_get_achievements_permalink() . '/' . $achievement->slug . '/' . DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE );
		else
			bp_core_redirect( dpa_get_achievements_permalink() . '/' . $achievement->slug );

	} else {
		if ( !$achievement->points )
			$achievement->points = '';

		if ( !$achievement->action_count )
			$achievement->action_count = '';

		do_action( 'dpa_screen_achievement_create_fail', $achievement, $achievements_errors );
		bp_core_add_message( __( 'An error has occurred and the Achievement has not been created. See below for details.', 'dpa' ), 'error' );
		bp_core_load_template( apply_filters( 'dpa_screen_achievement_create_template', 'achievements/create' ) );
	}
}
add_action( 'wp', 'dpa_screen_achievement_create', 3 );

/**
 * Adds notification settings, so that a user can turn off email notifications etc.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_screen_notification_settings() {
	global $bp;
?>
	<table class="notification-settings zebra" id="achievements-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Achievements', 'dpa' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'dpa' ) ?></th>
				<th class="no"><?php _e( 'No', 'dpa' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td></td>
				<td><?php _e( 'You unlock an achievement', 'dpa' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_dpa_unlock_achievement]" value="yes" <?php if ( !get_user_meta( $bp->displayed_user->id, 'notification_dpa_unlock_achievement', true ) || 'yes' == get_user_meta( $bp->displayed_user->id, 'notification_dpa_unlock_achievement', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_dpa_unlock_achievement]" value="no" <?php if ( get_user_meta( $bp->displayed_user->id, 'notification_dpa_unlock_achievement', true ) == 'no' ) { ?>checked="checked" <?php } ?>/></td>
			</tr>

			<?php do_action( 'dpa_screen_notification_settings' ); ?>
		</tbody>
	</table>
<?php
}
add_action( 'bp_notification_settings', 'dpa_screen_notification_settings' );


/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */

/**
 * Tells BuddyPress which activity stream types belong to Achievements.
 *
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_register_activity_actions() {
	global $bp;

	if ( !bp_is_active( 'activity' ) )
		return false;

	bp_activity_set_action( $bp->achievements->id, 'new_achievement', __( 'Achievement unlocked', 'dpa' ) );
	do_action( 'dpa_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'dpa_register_activity_actions' );

/**
 * Adds entries to the activity stream.
 * Has to be called from within the Achievement template loop.
 *
 * @global object $bp BuddyPress global settings
 * @param int $user_id
 * @param string $content The main text of the activity stream item; see dpa_format_activity()
 * @param int $item_id Achievement ID
 * @param string $component_action Optional; activity stream action name
 * @see dpa_format_activity
 * @since 2.0
 */
function dpa_record_activity( $user_id, $content, $item_id, $component_action = 'new_achievement' ) {
	global $bp;

	if ( !bp_is_active( 'activity' ) )
		return false;

	$permalink = dpa_get_achievement_slug_permalink();

	switch ( $component_action ) {
		default:
		case 'new_achievement':
			$entry = array(
				'action' =>  sprintf( __( '%1$s unlocked %2$s', 'dpa' ), bp_core_get_userlink( $user_id ), '<a href="' . $permalink . '">' . dpa_get_achievement_picture( 'activitystream' ) . '</a><a href="' . $permalink . '">' . dpa_get_achievement_name() . '</a>' ),
				'component' => $bp->achievements->id,
				'type' => $component_action,
				'primary_link' => bp_core_get_user_domain( $user_id ),
				'user_id' => $user_id,
				'item_id' => $item_id,
			);
		break;

		case 'achievement_created':
			$entry = array(
				'action' =>  sprintf( __( '%1$s created the Achievement %2$s', 'dpa' ), bp_core_get_userlink( $user_id ), '<a href="' . $permalink . '">' . dpa_get_achievement_name() . '</a>' ),
				'component' => $bp->achievements->id,
				'type' => $component_action,
				'primary_link' => bp_core_get_user_domain( $user_id ),
				'user_id' => $user_id,
				'item_id' => $item_id,
			);
		break;
	}

	return bp_activity_add( apply_filters( 'dpa_record_activity', $entry, $user_id, $content, $item_id, $component_action ) );
}

/**
 * Filters Achievements activity stream entries. Not used much here, but it is called by BuddyPress, so here it is!
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @see dpa_record_activity()
 * @since 2.0
 */
function dpa_format_activity( $user_id, $achievement_id, $component_action = 'new_achievement' ) {
	global $bp, $wpdb;

	switch ( $component_action ) {
		default:
		case 'achievement_created':
		case 'new_achievement':
			$stream_item = apply_filters( 'dpa_format_activity_new_achievement', dpa_get_achievement_description(), $user_id, $achievement_id, $component_action );
		break;
	}

	return apply_filters( 'dpa_format_activity', $stream_item, $user_id, $achievement_id, $component_action );
}
?>