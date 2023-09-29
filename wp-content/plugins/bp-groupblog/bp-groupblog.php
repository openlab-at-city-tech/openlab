<?php
/**
 * BuddyPress Groupblog main plugin file.
 *
 * @package BP_Groupblog
 */

define( 'BP_GROUPBLOG_IS_INSTALLED', 1 );
define( 'BP_GROUPBLOG_VERSION', '1.9.3' );

// Define default roles.
if ( ! defined( 'BP_GROUPBLOG_DEFAULT_ADMIN_ROLE' ) ) {
	define( 'BP_GROUPBLOG_DEFAULT_ADMIN_ROLE', 'administrator' );
}
if ( ! defined( 'BP_GROUPBLOG_DEFAULT_MOD_ROLE' ) ) {
	define( 'BP_GROUPBLOG_DEFAULT_MOD_ROLE', 'editor' );
}
if ( ! defined( 'BP_GROUPBLOG_DEFAULT_MEMBER_ROLE' ) ) {
	define( 'BP_GROUPBLOG_DEFAULT_MEMBER_ROLE', 'author' );
}

// Base groupblog component slug.
if ( ! defined( 'BP_GROUPBLOG_SLUG' ) ) {
	define( 'BP_GROUPBLOG_SLUG', 'group-blog' );
}

/**
 * Plugin activation.
 *
 * @since 1.0
 */
function bp_groupblog_setup() {
	global $wpdb;

	// Set up the array of potential defaults.
	$groupblog_blogdefaults = array(
		'theme'                  => 'bp-default|bp-groupblog',
		'page_template_layout'   => 'magazine',
		'delete_blogroll_links'  => '1',
		'default_cat_name'       => 'Uncategorized',
		'default_link_cat'       => 'Links',
		'delete_first_post'      => 0,
		'delete_first_comment'   => 0,
		'allowdashes'            => 0,
		'allowunderscores'       => 0,
		'allownumeric'           => 0,
		'minlength'              => 4,
		'redirectblog'           => 0,
		'deep_group_integration' => 0,
		'pagetitle'              => 'Blog',
	);
	// Add a site option so that we'll know set up ran.
	add_site_option( 'bp_groupblog_blog_defaults_setup', 1 );
	add_site_option( 'bp_groupblog_blog_defaults_options', $groupblog_blogdefaults );
}

register_activation_hook( __FILE__, 'bp_groupblog_setup' );

/**
 * Require the necessary files. Wait until BP is finished loading, so we have access to everything.
 *
 * @since 1.6
 */
function bp_groupblog_includes() {
	require WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-admin.php';
	require WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-cssjs.php';
	require WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-classes.php';
	require WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-templatetags.php';
}

// This file is needed earlier in BP 1.2.x, so we load it in the global scope (ugh)
// Require the abstraction file for earlier versions of BP.
$bp_version = defined( BP_VERSION ) ? BP_VERSION : '1.2';
if ( version_compare( $bp_version, '1.3', '<' ) ) {
	require_once WP_PLUGIN_DIR . '/bp-groupblog/1.5-abstraction.php';
	bp_groupblog_includes();
} else {
	add_action( 'bp_loaded', 'bp_groupblog_includes' );
}

/**
 * Add language support.
 */
if ( file_exists( WP_PLUGIN_DIR . '/bp-groupblog/languages/groupblog-' . get_locale() . '.mo' ) ) {
	load_plugin_textdomain( 'bp-groupblog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Set up globals.
 *
 * @since 1.6
 */
function bp_groupblog_setup_globals() {
	global $bp, $wpdb;

	$bp->groupblog                      = new stdClass();
	$bp->groupblog->image_base          = WP_PLUGIN_DIR . '/bp-groupblog/groupblog/images';
	$bp->groupblog->slug                = BP_GROUPBLOG_SLUG;
	$bp->groupblog->default_admin_role  = BP_GROUPBLOG_DEFAULT_ADMIN_ROLE;
	$bp->groupblog->default_mod_role    = BP_GROUPBLOG_DEFAULT_MOD_ROLE;
	$bp->groupblog->default_member_role = BP_GROUPBLOG_DEFAULT_MEMBER_ROLE;
}
add_action( 'bp_setup_globals', 'bp_groupblog_setup_globals' );

/**
 * Set up nav.
 *
 * @since 1.0
 */
function bp_groupblog_setup_nav() {
	global $bp, $current_blog;

	if ( bp_is_group() ) {

		$bp->groups->current_group->is_group_visible_to_member = ( 'public' === $bp->groups->current_group->status || groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() ) ) ? true : false;

		$group_link = bp_get_group_permalink( groups_get_current_group() );

		$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );

		if ( empty( $checks['deep_group_integration'] ) ) {

			$parent_slug = bp_get_current_group_slug();

			if (

				// Existing groupblog logic.
				bp_groupblog_is_blog_enabled( $bp->groups->current_group->id )

				||

				// mahype's fixes for the non-appearance of the groupblog tab
				// with the addition of a check for the array key to prevent PHP notices.
				(
					isset( $_POST['groupblog-create-new'] ) &&
					'yes' === $_POST['groupblog-create-new']
				)

			) {

				// Add a filter so plugins can change the name.
				$name = __( 'Blog', 'bp-groupblog' );
				$name = apply_filters( 'bp_groupblog_subnav_item_name', $name );

				// Add a filter so plugins can change the slug.
				$slug = apply_filters( 'bp_groupblog_subnav_item_slug', 'blog' );

				bp_core_new_subnav_item(
					array(
						'name'            => $name,
						'slug'            => $slug,
						'parent_url'      => $group_link,
						'parent_slug'     => $parent_slug,
						'screen_function' => 'groupblog_screen_blog',
						'position'        => 32,
						'item_css_id'     => 'nav-group-blog',
					)
				);
			}
		}
	}
}
add_action( 'bp_setup_nav', 'bp_groupblog_setup_nav' );

/**
 * Save the blog-settings accessible only by the group admin or mod.
 *
 * Since version 1.6, this function has been called directly by
 * BP_Groupblog_Extension::edit_screen_save()
 *
 * @since 1.0
 */
function groupblog_edit_settings() {
	global $bp, $groupblog_blog_id, $errors, $filtered_results;

	$group_id = isset( $_POST['groupblog-group-id'] ) ? (int) $_POST['groupblog-group-id'] : bp_get_current_group_id();

	if ( ! bp_groupblog_blog_exists( $group_id ) ) {
		if ( isset( $_POST['groupblog-enable-blog'] ) ) {
			if ( isset( $_POST['groupblog-create-new'] ) && 'yes' === $_POST['groupblog-create-new'] ) {
				// Create a new blog and assign the blog id to the global $groupblog_blog_id.
				if ( ! bp_groupblog_validate_blog_signup() ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$errors = $filtered_results['errors'];
					bp_core_add_message( $errors );
					$group_id = '';
				}
			} elseif ( isset( $_POST['groupblog-create-new'] ) && 'no' === $_POST['groupblog-create-new'] ) {
				// They're using an existing blog, so we try to assign that to $groupblog_blog_id.
				$groupblog_blog_id = isset( $_POST['groupblog-blogid'] ) ? (int) $_POST['groupblog-blogid'] : 0;
				if ( ! $groupblog_blog_id ) {
					// They forgot to choose a blog, so send them back and make them do it.
					bp_core_add_message( __( 'Please choose one of your blogs from the drop-down menu.', 'bp-groupblog' ), 'error' );
					if ( bp_is_action_variable( 'step', 0 ) ) {
						bp_core_redirect( trailingslashit( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] ) );
					} else {
						bp_core_redirect( trailingslashit( $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/group-blog' ) );
					}
				}
			}
		}
	} else {
		// They already have a blog associated with the group, we're just saving other settings.
		$groupblog_blog_id = groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_blog_id' );
	}

	// Get the necessary settings out of the $_POST global so that we can use them to set up
	// the blog.
	$settings = array(
		'groupblog-enable-blog' => '',
		'groupblog-silent-add'  => '',
		'default-administrator' => '',
		'default-moderator'     => '',
		'default-member'        => '',
		'page_template_layout'  => '',
	);

	foreach ( $settings as $setting => $val ) {
		if ( isset( $_POST[ $setting ] ) ) {
			$settings[ $setting ] = sanitize_text_field( wp_unslash( $_POST[ $setting ] ) );
		}
	}

	if ( ! groupblog_edit_base_settings( $settings['groupblog-enable-blog'], $settings['groupblog-silent-add'], $settings['default-administrator'], $settings['default-moderator'], $settings['default-member'], $settings['page_template_layout'], $group_id, $groupblog_blog_id ) ) {
		bp_core_add_message( __( 'There was an error creating your group blog, please try again.', 'bp-groupblog' ), 'error' );
	} else {
		bp_core_add_message( __( 'Group details were successfully updated.', 'bp-groupblog' ) );
	}

	do_action( 'groupblog_details_edited', $bp->groups->current_group->id );
}

/**
 * Updates the groupmeta with the blog_id, default roles and if it is enabled or not.
 *
 * Initiating member permissions loop on save - by Boone.
 *
 * @since 1.5
 *
 * @param string $groupblog_enable_blog         The enable blog setting.
 * @param string $groupblog_silent_add          The silent add setting.
 * @param string $groupblog_default_admin_role  The default admin role setting.
 * @param string $groupblog_default_mod_role    The default moderator role setting.
 * @param string $groupblog_default_member_role The default member role setting.
 * @param string $page_template_layout          The layout setting.
 * @param int    $group_id                      The group ID.
 * @param int    $groupblog_blog_id             The blog ID.
 */
function groupblog_edit_base_settings( $groupblog_enable_blog, $groupblog_silent_add = '', $groupblog_default_admin_role = '', $groupblog_default_mod_role = '', $groupblog_default_member_role = '', $page_template_layout = '', $group_id = 0, $groupblog_blog_id = 0 ) {
	global $bp;

	$group_id = (int) $group_id;

	if ( empty( $group_id ) ) {
		return false;
	}

	$default_role_array = array(
		'groupblog_default_admin_role'  => $groupblog_default_admin_role,
		'groupblog_default_mod_role'    => $groupblog_default_mod_role,
		'groupblog_default_member_role' => $groupblog_default_member_role,
	);

	$update_users = false;

	foreach ( $default_role_array as $role_name => $role ) {
		$old_default_role = groups_get_groupmeta( $group_id, $role_name );
		if ( $role !== $old_default_role ) {
			$update_users = true;
			break;
		}
	}

	groups_update_groupmeta( $group_id, 'groupblog_enable_blog', $groupblog_enable_blog );
	groups_update_groupmeta( $group_id, 'groupblog_blog_id', $groupblog_blog_id );

	groups_update_groupmeta( $group_id, 'groupblog_silent_add', $groupblog_silent_add );

	groups_update_groupmeta( $group_id, 'groupblog_default_admin_role', $groupblog_default_admin_role );
	groups_update_groupmeta( $group_id, 'groupblog_default_mod_role', $groupblog_default_mod_role );
	groups_update_groupmeta( $group_id, 'groupblog_default_member_role', $groupblog_default_member_role );

	groups_update_groupmeta( $group_id, 'page_template_layout', $page_template_layout );

	if ( $update_users ) {
		bp_groupblog_member_join( $group_id );
	}

	do_action( 'groupblog_details_updated', $group_id );

	return true;
}

/**
 * Runs whenever member permissions are changed and saved - by Boone
 *
 * @since 1.3
 *
 * @param int $group_id The group ID.
 */
function bp_groupblog_member_join( $group_id ) {
	$params = array(
		'exclude_admins_mods' => 0,
		'per_page'            => 10000,
		'group_id'            => $group_id,
	);

	if ( bp_group_has_members( $params ) ) {
		$blog_id = groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
		$group   = groups_get_group( array( 'group_id' => $group_id ) );

		while ( bp_group_members() ) {
			bp_group_the_member();
			$user_id = bp_get_group_member_id();

			if ( (int) $group->creator_id !== (int) $user_id ) {
				bp_groupblog_upgrade_user( $user_id, $group_id, $blog_id );
			}
		}
	}
}

/**
 * Gets a group's groupblog settings.
 *
 * @since 1.8.13
 *
 * @param int $group_id The group ID.
 * @return array $r The array of group settings.
 */
function bp_groupblog_get_group_settings( $group_id ) {
	$defaults = array(
		'groupblog_silent_add'          => false,
		'groupblog_default_member_role' => BP_GROUPBLOG_DEFAULT_MEMBER_ROLE,
		'groupblog_default_mod_role'    => BP_GROUPBLOG_DEFAULT_MOD_ROLE,
		'groupblog_default_admin_role'  => BP_GROUPBLOG_DEFAULT_ADMIN_ROLE,
		'groupblog_enable_blog'         => false,
		'groupblog_blog_id'             => null,
	);

	$r = array();
	foreach ( $defaults as $key => $default_value ) {
		$saved_value = groups_get_groupmeta( $group_id, $key, true );
		if ( '' === $saved_value ) {
			$r[ $key ] = $default_value;
		} else {
			$r[ $key ] = $saved_value;
		}
	}

	return $r;
}

/**
 * Subscribes user in question to blog in question
 *
 * This code was initially inspired by Burt Adsit re-interpreted by Boone
 *
 * @since 1.3
 *
 * @param int $user_id The user ID.
 * @param int $group_id The group ID.
 * @param int $blog_id The blog ID.
 */
function bp_groupblog_upgrade_user( $user_id, $group_id, $blog_id = false ) {
	global $bp;

	if ( ! $blog_id ) {
		$blog_id = groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
	}

	// If the group has no blog linked, get the heck out of here.
	if ( ! $blog_id ) {
		return;
	}

	$settings = bp_groupblog_get_group_settings( $group_id );
	if ( ! $settings['groupblog_silent_add'] ) {
		return;
	}

	// Set up some variables.
	$groupblog_silent_add          = $settings['groupblog_silent_add'];
	$groupblog_default_member_role = $settings['groupblog_default_member_role'];
	$groupblog_default_mod_role    = $settings['groupblog_default_mod_role'];
	$groupblog_default_admin_role  = $settings['groupblog_default_admin_role'];
	$groupblog_creator_role        = 'admin';

	// Get user's blog role.
	$user_role = bp_groupblog_get_user_role( $user_id, false, $blog_id );

	// Get the current user's group status.
	if ( groups_is_user_admin( $user_id, $group_id ) ) {
		$user_group_status = 'admin';
	} elseif ( groups_is_user_mod( $user_id, $group_id ) ) {
		$user_group_status = 'mod';
	} elseif ( groups_is_user_member( $user_id, $group_id ) ) {
		$user_group_status = 'member';
	} else {
		return false;
	}

	// Change user status based on promotion / demotion.
	switch ( bp_action_variable( 1 ) ) {
		case 'promote':
			$user_group_status = bp_action_variable( 2 );
			break;

		case 'demote':
		case 'unban':
			$user_group_status = 'member';
			break;

		// We don't remove users from blogs at the moment.
		// We give them the minimum role of 'subscriber'.
		case 'ban':
		case 'remove':
			$user_group_status = 'subscriber';
			break;
	}

	// Set the role.
	switch ( $user_group_status ) {
		case 'admin':
			$default_role = $groupblog_default_admin_role;
			break;

		case 'mod':
			$default_role = $groupblog_default_mod_role;
			break;

		case 'subscriber':
			$default_role = 'subscriber';
			break;

		case 'member':
		default:
			$default_role = $groupblog_default_member_role;
			break;
	}

	if ( $user_role === $default_role && $groupblog_silent_add ) {
		return false;
	}

	if ( ! $groupblog_silent_add ) {
		$default_role = 'subscriber';
	}

	add_user_to_blog( $blog_id, $user_id, $default_role );

	do_action( 'bp_groupblog_upgrade_user', $user_id, $user_role, $default_role );
}

/**
 * Called when user joins group - by Boone
 *
 * @since 1.3
 *
 * @param int $group_id The group ID.
 * @param int $user_id The user ID.
 */
function bp_groupblog_just_joined_group( $group_id, $user_id ) {
	bp_groupblog_upgrade_user( $user_id, $group_id );
}
add_action( 'groups_join_group', 'bp_groupblog_just_joined_group', 5, 2 );

/**
 * Called when user changes status in the group
 *
 * Variables ($user_id, $group_id) are switched around for these hooks,
 * therefore we put these in a separate function.
 *
 * @since 1.3
 *
 * @param int $user_id  The user ID.
 * @param int $group_id The group ID.
 */
function bp_groupblog_changed_status_group( $user_id, $group_id ) {
	bp_groupblog_upgrade_user( $user_id, $group_id );
}
add_action( 'groups_promoted_member', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_demoted_member', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_unbanned_member', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_banned_member', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_removed_member', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_membership_accepted', 'bp_groupblog_changed_status_group', 10, 2 );
add_action( 'groups_accept_invite', 'bp_groupblog_changed_status_group', 10, 2 );


/**
 * Called when user leaves.
 *
 * @since 1.3
 *
 * @param int $group_id The group ID.
 * @param int $user_id The user ID.
 */
function bp_groupblog_remove_user( $group_id, $user_id = false ) {
	// Only modify site membership if the plugin is configured to do so.
	$settings = bp_groupblog_get_group_settings( $group_id );
	if ( ! $settings['groupblog_silent_add'] ) {
		return;
	}

	$blog_id = get_groupblog_blog_id( $group_id );

	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	// Users with no existing role should not be modified.
	if ( ! is_user_member_of_blog( $user_id, $blog_id ) ) {
		return;
	}

	remove_user_from_blog( $user_id, $blog_id, 0 );

	wp_cache_delete( $user_id, 'users' );
}
add_action( 'groups_leave_group', 'bp_groupblog_remove_user', 10, 2 );

/**
 * Reworked function to retrieve the users current role - by Boone
 *
 * @since 1.3
 *
 * @param int  $user_id    The user ID.
 * @param bool $user_login Deprecated. Don't use.
 * @param int  $blog_id    The blog ID.
 * @return string The user's blog role.
 */
function bp_groupblog_get_user_role( $user_id, $user_login = false, $blog_id = 0 ) {
	global $wpdb;

	// Determine users role, if any, on this blog.
	$roles = get_user_meta( $user_id, $wpdb->get_blog_prefix( $blog_id ) . 'capabilities', true );

	// This seems to be the only way to do this.
	if ( isset( $roles['subscriber'] ) ) {
		$user_role = 'subscriber';
	} elseif ( isset( $roles['contributor'] ) ) {
		$user_role = 'contributor';
	} elseif ( isset( $roles['author'] ) ) {
		$user_role = 'author';
	} elseif ( isset( $roles['editor'] ) ) {
		$user_role = 'editor';
	} elseif ( isset( $roles['administrator'] ) ) {
		$user_role = 'administrator';
	} elseif ( is_super_admin( $user_id ) ) {
		$user_role = 'siteadmin';
	} else {
		$user_role = 'norole';
	}

	return $user_role;
}

/**
 * Saves the information from the BP group blog creation step.
 *
 * TODO: groupblog-edit-settings is more efficient, rewrite this to be more like that one.
 *
 * @since 1.0
 */
function bp_groupblog_create_screen_save() {
	global $bp;
	global $groupblog_blog_id, $groupblog_create_screen, $filtered_results;

	if ( bp_is_action_variable( 'step', 0 ) ) {
		$groupblog_create_screen = true;
	} else {
		$groupblog_create_screen = false;
	}

	// Set up some default roles.
	$groupblog_default_admin_role  = isset( $_POST['default-administrator'] ) ? sanitize_text_field( wp_unslash( $_POST['default-administrator'] ) ) : BP_GROUPBLOG_DEFAULT_ADMIN_ROLE;
	$groupblog_default_mod_role    = isset( $_POST['default-moderator'] ) ? sanitize_text_field( wp_unslash( $_POST['default-moderator'] ) ) : BP_GROUPBLOG_DEFAULT_MOD_ROLE;
	$groupblog_default_member_role = isset( $_POST['default-member'] ) ? sanitize_text_field( wp_unslash( $_POST['default-member'] ) ) : BP_GROUPBLOG_DEFAULT_MEMBER_ROLE;

	// Set up some other values.
	$groupblog_group_id   = isset( $_POST['group_id'] ) ? (int) $_POST['group_id'] : bp_get_new_group_id();
	$silent_add           = isset( $_POST['groupblog-silent-add'] ) ? sanitize_text_field( wp_unslash( $_POST['groupblog-silent-add'] ) ) : '';
	$page_template_layout = isset( $_POST['page_template_layout'] ) ? sanitize_text_field( wp_unslash( $_POST['page_template_layout'] ) ) : '';
	$enable_group_blog    = isset( $_POST['groupblog-enable-blog'] ) ? sanitize_text_field( wp_unslash( $_POST['groupblog-enable-blog'] ) ) : '';

	if ( isset( $_POST['groupblog-create-new'] ) && 'yes' === $_POST['groupblog-create-new'] ) {
		// Create a new blog and assign the blog id to the global $groupblog_blog_id.
		$groupblog_blog_id = bp_groupblog_validate_blog_signup();
		if ( ! $groupblog_blog_id ) {
			$errors = $filtered_results['errors'];
			bp_core_add_message( $errors );
			$group_id = '';
		}
	} elseif ( isset( $_POST['groupblog-create-new'] ) && 'no' === $_POST['groupblog-create-new'] ) {
		// They're using an existing blog, so we try to assign that to $groupblog_blog_id.
		$groupblog_blog_id = isset( $_POST['groupblog-blogid'] ) ? (int) $_POST['groupblog-blogid'] : 0;
		if ( ! $groupblog_blog_id ) {
			// They forgot to choose a blog, so send them back and make them do it.
			bp_core_add_message( __( 'Please choose one of your blogs from the drop-down menu.', 'bp-groupblog' ), 'error' );
			bp_core_redirect( trailingslashit( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] ) );
		}
	} else {
		// They already have a blog associated with the group, we're just saving other settings.
		$groupblog_blog_id = groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_blog_id' );
	}

	if ( ! groupblog_edit_base_settings( $enable_group_blog, $silent_add, $groupblog_default_admin_role, $groupblog_default_mod_role, $groupblog_default_member_role, $page_template_layout, $groupblog_group_id, $groupblog_blog_id ) ) {
		bp_core_add_message( __( 'There was an error creating your group blog, please try again.', 'bp-groupblog' ), 'error' );
		bp_core_redirect( trailingslashit( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] ) );
	}
}

/**
 * Displays the blog signup form and takes the privacy settings from the
 * group privacy settings, where "private & hidden" equal "private".
 *
 * @since 1.0
 *
 * @param string         $blogname   The name of the blog.
 * @param string         $blog_title The title of the blog.
 * @param WP_Error|false $errors     Error object.
 */
function bp_groupblog_show_blog_form( $blogname = '', $blog_title = '', $errors = false ) {
	global $bp, $groupblog_create_screen, $current_site;

	// Get the group id, which is fetched differently depending on whether this is a group
	// Create or Edit screen.
	$group_id = bp_is_group_create() ? bp_get_new_group_id() : bp_get_current_group_id();

	$blog_id = get_groupblog_blog_id();

	?>

	<div id="blog-details-fields">

	<?php if ( ! $groupblog_create_screen && $blog_id ) : ?>
		<?php /* We're showing the admin form */ ?>
		<?php $blog_details = get_blog_details( get_groupblog_blog_id(), true ); ?>
		<label for="blog_title"><strong><?php esc_html_e( 'Blog Title:', 'bp-groupblog' ); ?></strong></label>

		<?php $errmsg = $errors->get_error_message( 'blog_title' ); ?>
		<?php if ( $errmsg ) { ?>
			<p class="error"><?php echo esc_html( $errmsg ); ?></p>
		<?php } ?>

		<p><?php echo esc_html( $blog_details->blogname ); ?></p>
		<input name="blog_title" type="hidden" id="blog_title" value="<?php echo esc_attr( $blog_details->blogname ); ?>" />

		<label for="blogname"><strong><?php esc_html_e( 'Blog Address:', 'bp-groupblog' ); ?></strong></label>
		<?php $errmsg = $errors->get_error_message( 'blogname' ); ?>
		<?php if ( $errmsg ) : ?>
			<p class="error"><?php echo esc_html( $errmsg ); ?></p>
		<?php endif ?>

		<p><em><?php echo esc_html( $blog_details->siteurl ); ?> </em></p>
		<input name="blogname" type="hidden" id="blogname" value="<?php echo esc_attr( $blog_details->siteurl ); ?>" maxlength="50" />

		<div id="uncouple-blog">
			<?php // translators: 1. Site name; 2. Group name. ?>
			<label for="uncouple"><?php printf( esc_html__( 'Uncouple the blog "%1$s" from the group "%2$s":', 'bp-groupblog' ), esc_html( $blog_details->blogname ), esc_html( $bp->groups->current_group->name ) ); ?></label>

			<?php // translators: Link to Users panel. ?>
			<p class="description"><?php printf( __( '<strong>Note:</strong> Uncoupling will remove the blog from your group&#8217;s navigation and prevent future synchronization of group members and blog authors, but it will not remove change blog permissions for any current member. Visit <a href="%1$s">the Users panel</a> if you&#8217;d like to remove users from the blog.', 'bp-groupblog' ), esc_attr( $blog_details->siteurl . '/wp-admin/users.php' ) ); ?></p> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<a class="button" href="<?php echo esc_attr( wp_nonce_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/group-blog/uncouple', 'groupblog-uncouple' ) ); ?>"><?php esc_html_e( 'Uncouple', 'bp-groupblog' ); ?></a>

		</div>

		<?php
		$group_public = 'public' === groups_get_current_group()->status ? '1' : '0';
		?>

		<input type="hidden" id="blog_public" name="blog_public" value="<?php echo esc_attr( $group_public ); ?>" />
		<input type="hidden" id="groupblog_create_screen" name="groupblog_create_screen" value="<?php echo esc_attr( $groupblog_create_screen ); ?>" />

	<?php else : ?>
		<?php /* Showing the create screen form */ ?>

		<p><?php esc_html_e( 'Choose either one of your existing blogs or create a new one all together with the details displayed below.', 'bp-groupblog' ); ?><br /><?php esc_html_e( 'Take care as you can only choose once. Later you may still disable or enable the blog, but your choice is set.', 'bp-groupblog' ); ?></p>

		<p>
			<label for="groupblog-create-new-no"><input type="radio" value="no" name="groupblog-create-new" id="groupblog-create-new-no" /><span>&nbsp;<?php esc_html_e( 'Use one of your own available blogs:', 'bp-groupblog' ); ?>&nbsp;</span>

			<?php $user_blogs = get_blogs_of_user( get_current_user_id() ); ?>

			<select name="groupblog-blogid" id="groupblog-blogid">
				<option value="0"><?php esc_html_e( 'choose a blog', 'bp-groupblog' ); ?></option>
				<?php

				foreach ( (array) $user_blogs as $user_blog ) {
					if ( ! get_groupblog_group_id( $user_blog->userblog_id ) ) :
						?>
						<option value="<?php echo esc_attr( $user_blog->userblog_id ); ?>"><?php echo esc_html( $user_blog->blogname ); ?></option>
						<?php
					endif;
				}
				?>
			</select>
			</label>
		</p>

		<p>
			<label for="groupblog-create-new-yes"><input type="radio" value="yes" name="groupblog-create-new" id="groupblog-create-new-yes" checked="checked" /><span>&nbsp;<?php esc_html_e( 'Or, create a new blog', 'bp-groupblog' ); ?></label></span>
		</p>

		<ul id="groupblog-details">
			<li>
				<label class="groupblog-label" for="blog_title"><strong><?php esc_html_e( 'Blog Title:', 'bp-groupblog' ); ?></strong></label>

				<?php $errmsg = $errors->get_error_message( 'blog_title' ); ?>
				<?php if ( $errmsg ) : ?>
					<span class="error"><?php echo esc_html( $errmsg ); ?></span>
				<?php endif ?>

				<?php
				if ( isset( $_GET['invalid_name'] ) ) {
					$blog_title = urldecode( sanitize_text_field( wp_unslash( $_GET['invalid_name'] ) ) );
				} else {
					$blog_title = bp_groupblog_sanitize_blog_name( $bp->groups->current_group->name );
				}
				?>

				<span class="gbd-value">
					<input name="blog_title" type="text" id="blog_title" value="<?php echo esc_attr( $blog_title ); ?>" />
				</span>
			</li>

			<li>
				<label class="groupblog-label" for="blogname"><strong><?php esc_html_e( 'Blog Address:', 'bp-groupblog' ); ?></strong></label>
				<?php $errmsg = $errors->get_error_message( 'blogname' ); ?>
				<?php if ( $errmsg ) : ?>
					<span class="error"><?php echo esc_html( $errmsg ); ?></span>
				<?php endif ?>

				<?php
				if ( isset( $_GET['invalid_address'] ) ) {
					$blog_address = urldecode( sanitize_text_field( wp_unslash( $_GET['invalid_address'] ) ) );
				} else {
					$blog_address = bp_groupblog_sanitize_blog_name( $bp->groups->current_group->slug );
				}

				// Don't suggest a subdomain if it's really long,
				// since subdomains longer than 63 chars won't work.
				if ( strlen( $blog_address > 50 ) ) {
					$blog_address = '';
				}
				?>

				<?php if ( is_subdomain_install() ) : ?>
					<span class="gbd-value"><em>http://</em><input name="blogname" type="text" id="blogname" value="<?php echo esc_attr( $blog_address ); ?>" maxlength="50" /><em><?php echo esc_attr( $current_site->domain . $current_site->path ); ?></em></span>
				<?php else : ?>
					<span class="gbd-value"><em>http://<?php echo esc_attr( $current_site->domain . $current_site->path ); ?></em><input name="blogname" type="text" id="blogname" value="<?php echo esc_attr( $blog_address ); ?>" maxlength="50" /></span>
				<?php endif ?>

			</li>
		</ul>

		<?php
		$group_public = 'public' === groups_get_current_group()->status ? '1' : '0';
		?>

		<input type="hidden" id="blog_public" name="blog_public" value="<?php echo esc_attr( $group_public ); ?>" />
		<input type="hidden" id="groupblog_create_screen" name="groupblog_create_screen" value="<?php echo esc_attr( $groupblog_create_screen ); ?>" />

	<?php endif ?>

	</div>
	<?php

	do_action( 'signup_blogform', $errors );
}

/**
 * This function validates that the blog does not exist already, illegal names, etc...
 *
 * @since 1.0
 */
function bp_groupblog_validate_blog_form() {
	$user = '';
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
	}

	$blogname   = isset( $_POST['blogname'] ) ? sanitize_text_field( wp_unslash( $_POST['blogname'] ) ) : '';
	$blog_title = isset( $_POST['blog_title'] ) ? sanitize_text_field( wp_unslash( $_POST['blog_title'] ) ) : '';

	$result = wpmu_validate_blog_signup( $blogname, $blog_title, $user );

	$errors = $result['errors'];

	// We only want to filter if there is an error.
	if ( ! is_object( $errors ) ) {
		return $result;
	}

	$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );

	// Create a new error object to hold errors.
	$newerrors = new WP_Error();

	// Loop through the errors and look for the one we are concerned with.
	foreach ( $errors->errors as $key => $value ) {

		// If the error is with the blog name, check to see which one.
		if ( 'blogname' === $key ) {

			foreach ( $value as $subkey => $subvalue ) {

				// TODO: This isn't compatible with languages other than "en_*".
				// Maybe "switch_to_locale()" might help?
				switch ( $subvalue ) {
					/*
					 * Removed in WordPress 4.4.
					 * @see https://github.com/WordPress/WordPress/commit/a0bbd154d93c20724b9e1f54d515468845870dc0
					 */
					case 'Only lowercase letters (a-z) and numbers are allowed.':
						// Support WordPress 4.4 string.
					case 'Site names can only contain lowercase letters (a-z) and numbers.':
						$allowedchars = '';
						if ( ! empty( $checks['allowdashes'] ) && 1 === (int) $checks['allowdashes'] ) {
							$allowedchars .= '-';
						}
						if ( ! empty( $checks['allowunderscores'] ) && 1 === (int) $checks['allowunderscores'] ) {
							$allowedchars .= '_';
						}

						$allowed = '/[a-z0-9' . $allowedchars . ']+/';
						preg_match( $allowed, $result['blogname'], $maybe );
						if ( $result['blogname'] !== $maybe[0] ) {

							// Still fails, so add an error to the object.
							$newerrors->add( 'blogname', __( 'Only lowercase letters and numbers allowed.', 'bp-groupblog' ) );

						}
						break;

					case 'Site name must be at least 4 characters.':
						if ( ! is_super_admin() && ! empty( $checks['minlength'] ) && strlen( $result['blogname'] ) < $checks['minlength'] ) {
							$newerrors->add(
								'blogname',
								sprintf(
									/* translators: %d: The minimum number of characters for a blog name. */
									__( 'Blog name must be at least %d characters.', 'bp-groupblog' ),
									(int) $checks['minlength']
								)
							);
						}
						break;

					/*
					 * Removed in WordPress 4.4.
					 * @see https://github.com/WordPress/WordPress/commit/a0bbd154d93c20724b9e1f54d515468845870dc0
					 */
					case 'Sorry, site names may not contain the character &#8220;_&#8221;!':
						if ( ! empty( $checks['allowunderscores'] ) && 1 === (int) $checks['allowunderscores'] ) {
							$newerrors->add(
								'blogname',
								sprintf(
									/* translators: %d: The minimum number of characters for a Site name. */
									__( 'Sorry, blog names may not contain the character %s!', 'bp-groupblog' ),
									"'_"
								)
							);
						}
						break;

					case 'Sorry, site names must have letters too!':
						if ( ! empty( $checks['allownumeric'] ) && 1 === (int) $checks['allownumeric'] ) {
							$newerrors->add( 'blogname', __( 'Sorry, blog names must have letters too!', 'bp-groupblog' ) );
						}
						break;

					default:
						$newerrors->add( 'blogname', $subvalue );

				} // End switch.
			}
		} else {

			// Add all other errors into the error object, but they're in sub-arrays, so loop through to get the right stuff.
			foreach ( $value as $subkey => $subvalue ) {
				$newerrors->add( $key, $subvalue );
			}
		}
	}

	// Unset the error object from the results & reset it with our new errors.
	unset( $result['errors'] );
	$result['errors'] = $newerrors;

	return $result;
}

/**
 * Sanitizes a group name into a blog address, based on site settings.
 *
 * @since 1.7
 *
 * @param str $group_name The group name.
 * @return str $blog_address The blog address.
 */
function bp_groupblog_sanitize_blog_name( $group_name = '' ) {
	$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );

	$baddies = array();
	if ( ! empty( $checks['allowdashes'] ) && 1 !== (int) $checks['allowdashes'] ) {
		$baddies[] = '-';
	}
	if ( ! empty( $checks['allowunderscores'] ) && 1 === (int) $checks['allowunderscores'] ) {
		$baddies[] = '_';
	}

	$blog_address = str_replace( $baddies, '', $group_name );

	return $blog_address;
}

/**
 * Catches and processes a groupblog uncoupling
 *
 * @since 1.7
 */
function bp_groupblog_process_uncouple() {
	if ( bp_is_group() && bp_is_current_action( 'admin' ) && bp_is_action_variable( 'group-blog', 0 ) && bp_is_action_variable( 'uncouple', 1 ) ) {
		check_admin_referer( 'groupblog-uncouple' );

		if ( ! bp_group_is_admin() ) {
			bp_core_add_message( __( 'You must be a group admin to perform this action.', 'bp-groupblog' ), 'error' );
			bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) );
		}

		$blog_id = get_groupblog_blog_id();

		// If groupblog is enabled, disable it.
		groups_update_groupmeta( bp_get_current_group_id(), 'groupblog_enable_blog', 0 );

		// Unset the groupblog ID.
		groups_update_groupmeta( bp_get_current_group_id(), 'groupblog_blog_id', '' );

		bp_core_add_message( __( 'Blog uncoupled.', 'bp-groupblog' ) );

		// Redirect to the groupblog admin.
		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-blog' );
	}
}
add_action( 'bp_actions', 'bp_groupblog_process_uncouple', 1 );

/**
 * This function is called from the template and initiates the blog creation.
 *
 * @since 1.0
 *
 * @param str $blogname The name of the blog.
 * @param str $blog_title The title of the blog.
 * @param str $errors The errors.
 */
function bp_groupblog_signup_blog( $blogname = '', $blog_title = '', $errors = '' ) {
	global $current_user, $current_site, $groupblog_create_screen;
	global $bp, $filtered_results;

	if ( ! is_wp_error( $errors ) ) {
		$errors = new WP_Error();
	}

	// allow definition of default variables.
	$filtered_results = apply_filters(
		'signup_blog_init',
		array(
			'blogname'   => $blogname,
			'blog_title' => $blog_title,
			'errors'     => $errors,
		)
	);
	$blogname         = $filtered_results['blogname'];
	$blog_title       = $filtered_results['blog_title'];
	$errors           = $filtered_results['errors'];

	if ( ! isset( $groupblog_create_screen ) ) {
		$groupblog_create_screen = false;
	}

	// Get the group id, which is fetched differently depending on whether this is a group
	// Create or Edit screen.
	$group_id = bp_is_group_create() ? bp_get_new_group_id() : bp_get_current_group_id();

	?>
	<h2><?php esc_html_e( 'Group Blog', 'bp-groupblog' ); ?></h2>
	<?php if ( ! $groupblog_create_screen ) { ?>
		<input type="hidden" name="stage" value="gimmeanotherblog" />
		<?php do_action( 'signup_hidden_fields' ); ?>
	<?php } ?>

		<div class="checkbox">
			<label><input type="checkbox" name="groupblog-enable-blog" id="groupblog-enable-blog" value="1"<?php bp_groupblog_show_enabled( $group_id ); ?>/> <?php esc_html_e( 'Enable group blog', 'bp-groupblog' ); ?></label>
		</div>

		<?php bp_groupblog_show_blog_form( $blogname, $blog_title, $errors ); ?>

		<br />

		<div id="groupblog-member-options">

			<h3><?php esc_html_e( 'Member Options', 'bp-groupblog' ); ?></h3>

			<p><?php esc_html_e( 'Enable blog posting to allow adding of group members to the blog with the roles set below.', 'bp-groupblog' ); ?><br /><?php esc_html_e( 'When disabled, all members will temporarily be set to subscribers, disabling posting.', 'bp-groupblog' ); ?></p>

			<div class="checkbox">
				<label><input type="checkbox" name="groupblog-silent-add" id="groupblog-silent-add" value="1" <?php checked( bp_groupblog_silent_add( $group_id ) ); ?> /> <?php esc_html_e( 'Enable member blog posting', 'bp-groupblog' ); ?></label>
			</div>

			<?php
			// Assign our default roles to variables.
			// If nothing has been saved in the groupmeta yet, then we assign our own defalt values.
			$groupblog_default_admin_role = groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_default_admin_role' );
			if ( ! $groupblog_default_admin_role ) {
				$groupblog_default_admin_role = $bp->groupblog->default_admin_role;
			}

			$groupblog_default_mod_role = groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_default_mod_role' );
			if ( ! $groupblog_default_mod_role ) {
				$groupblog_default_mod_role = $bp->groupblog->default_mod_role;
			}

			$groupblog_default_member_role = groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_default_member_role' );
			if ( ! $groupblog_default_member_role ) {
				$groupblog_default_member_role = $bp->groupblog->default_member_role;
			}
			?>

			<label><strong><?php esc_html_e( 'Default Administrator Role:', 'bp-groupblog' ); ?></strong></label>
			<input type="radio" <?php checked( $groupblog_default_admin_role, 'administrator' ); ?> value="administrator" name="default-administrator" /><span>&nbsp;<?php esc_html_e( 'Administrator', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_admin_role, 'editor' ); ?> value="editor" name="default-administrator" /><span>&nbsp;<?php esc_html_e( 'Editor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_admin_role, 'author' ); ?> value="author" name="default-administrator" /><span>&nbsp;<?php esc_html_e( 'Author', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_admin_role, 'contributor' ); ?> value="contributor" name="default-administrator" /><span>&nbsp;<?php esc_html_e( 'Contributor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_admin_role, 'subscriber' ); ?> value="subscriber" name="default-administrator" /><span>&nbsp;<?php esc_html_e( 'Subscriber', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>

			<label><strong><?php esc_html_e( 'Default Moderator Role:', 'bp-groupblog' ); ?></strong></label>
			<input type="radio" <?php checked( $groupblog_default_mod_role, 'administrator' ); ?> value="administrator" name="default-moderator" /><span>&nbsp;<?php esc_html_e( 'Administrator', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_mod_role, 'editor' ); ?> value="editor" name="default-moderator" /><span>&nbsp;<?php esc_html_e( 'Editor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_mod_role, 'author' ); ?> value="author" name="default-moderator" /><span>&nbsp;<?php esc_html_e( 'Author', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_mod_role, 'contributor' ); ?> value="contributor" name="default-moderator" /><span>&nbsp;<?php esc_html_e( 'Contributor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_mod_role, 'subscriber' ); ?> value="subscriber" name="default-moderator" /><span>&nbsp;<?php esc_html_e( 'Subscriber', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>

			<label><strong><?php esc_html_e( 'Default Member Role:', 'bp-groupblog' ); ?></strong></label>
			<input type="radio" <?php checked( $groupblog_default_member_role, 'administrator' ); ?> value="administrator" name="default-member" /><span>&nbsp;<?php esc_html_e( 'Administrator', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_member_role, 'editor' ); ?> value="editor" name="default-member" /><span>&nbsp;<?php esc_html_e( 'Editor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_member_role, 'author' ); ?> value="author" name="default-member" /><span>&nbsp;<?php esc_html_e( 'Author', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_member_role, 'contributor' ); ?> value="contributor" name="default-member" /><span>&nbsp;<?php esc_html_e( 'Contributor', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio" <?php checked( $groupblog_default_member_role, 'subscriber' ); ?> value="subscriber" name="default-member" /><span>&nbsp;<?php esc_html_e( 'Subscriber', 'bp-groupblog' ); ?>&nbsp;&nbsp;</span>

			<div id="groupblog-member-roles">
				<label><strong><?php esc_html_e( 'A bit about WordPress member roles:', 'bp-groupblog' ); ?></strong></label>
				<ul id="groupblog-members">
					<li><?php esc_html_e( 'Administrator', 'bp-groupblog' ); ?> - <?php esc_html_e( 'Somebody who has access to all the administration features.', 'bp-groupblog' ); ?></li>
					<li><?php esc_html_e( 'Editor', 'bp-groupblog' ); ?> - <?php esc_html_e( "Somebody who can publish posts, manage posts as well as manage other people's posts, etc.", 'bp-groupblog' ); ?></li>
					<li><?php esc_html_e( 'Author', 'bp-groupblog' ); ?> - <?php esc_html_e( 'Somebody who can publish and manage their own posts.', 'bp-groupblog' ); ?></li>
					<li><?php esc_html_e( 'Contributor', 'bp-groupblog' ); ?> - <?php esc_html_e( 'Somebody who can write and manage their posts but not publish posts.', 'bp-groupblog' ); ?></li>
					<li><?php esc_html_e( 'Subscriber', 'bp-groupblog' ); ?> - <?php esc_html_e( 'Somebody who can read comments/comment/receive news letters, etc.', 'bp-groupblog' ); ?></li>
				</ul>
			</div>

		</div>

		<br />

		<?php if ( bp_groupblog_allow_group_admin_layout() ) : ?>

			<?php
			$page_template_layout = groups_get_groupmeta( $bp->groups->current_group->id, 'page_template_layout' );
			if ( ! $page_template_layout ) {
				$page_template_layout = groupblog_get_page_template_layout();
			}
			?>

			<div id="groupblog-layout-options">

				<h3><?php esc_html_e( 'Select Layout', 'bp-groupblog' ); ?></h3>

				<p class="enabled"><?php esc_html_e( 'Please select a Layout which you would like to use for your Group Blog.', 'bp-groupblog' ); ?></p>

				<table class="enabled" id="availablethemes" cellspacing="0" cellpadding="0">
					<tbody>
					<tr>
						<td class="available-theme top left">
							<?php echo '<img src="' . esc_attr( WP_PLUGIN_URL ) . '/bp-groupblog/inc/i/screenshot-mag.png">'; ?>
							<br /><br />
							<input <?php disabled( ! bp_groupblog_is_blog_enabled( $group_id ) ); ?> name="page_template_layout" id="page_template_layout" value="magazine" type="radio" <?php checked( 'magazine', $page_template_layout ); ?> /><label style="display:inline;"> <?php esc_html_e( 'Magazine', 'bp-groupblog' ); ?></label>
							<p class="description"><?php esc_html_e( 'Balanced template for groups with diverse postings.', 'bp-groupblog' ); ?></p>
						</td>
						<td class="available-theme top">
							<?php echo '<img src="' . esc_attr( WP_PLUGIN_URL ) . '/bp-groupblog/inc/i/screenshot-micro.png">'; ?>
							<br /><br />
							<input <?php disabled( ! bp_groupblog_is_blog_enabled( $group_id ) ); ?> name="page_template_layout" id="page_template_layout" value="microblog" type="radio" <?php checked( 'microblog', $page_template_layout ); ?> /><label style="display:inline;"> <?php esc_html_e( 'Microblog', 'bp-groupblog' ); ?></label>
							<p class="description"><?php esc_html_e( 'Great for simple listing of posts in a chronological order.', 'bp-groupblog' ); ?></p>
						</td>
					</tr>
					</tbody>
				</table>

			</div>

			<br />

		<?php endif; ?>

		<?php if ( ! $groupblog_create_screen ) : ?>
			<p>
				<input id="save" type="submit" name="save" class="submit" value="<?php esc_html_e( 'Save Changes &raquo;', 'bp-groupblog' ); ?>"/>
			</p>
		<?php endif; ?>

	<?php
}

/**
 * Final step before the blog gets created it needs to be validated
 *
 * @since 1.0
 */
function bp_groupblog_validate_blog_signup() {
	global $bp, $wpdb, $current_user, $blogname, $blog_title, $errors;
	global $groupblog_blog_id, $filtered_results;

	$group_id = isset( $_COOKIE['bp_new_group_id'] ) ? (int) $_COOKIE['bp_new_group_id'] : bp_get_current_group_id();

	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$current_user = wp_get_current_user();
	if ( ! is_user_logged_in() ) {
		die();
	}

	// Re-validate user info.
	$result = bp_groupblog_validate_blog_form();
	// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	extract( $result );

	$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );

	if ( ! empty( $checks ) && $errors->get_error_code() ) {
		$message  = '';
		$message .= $errors->get_error_message( 'blogname' ) . '<br />';
		$message .= __( ' We suggest adjusting the blog address below, in accordance with the following requirements:', 'bp-groupblog' ) . '<br />';
		if ( 1 === (int) $checks['allowunderscores'] || 1 !== (int) $checks['allowdashes'] ) {
			$message .= __( ' &raquo; Only letters and numbers allowed.', 'bp-groupblog' ) . '<br />';
		}
		// translators: Character count.
		$message .= sprintf( __( ' &raquo; Must be at least %s characters.', 'bp-groupblog' ), $checks['minlength'] ) . '<br />';
		if ( 1 === (int) $checks['allownumeric'] ) {
			$message .= __( ' &raquo; Has to contain letters as well.', 'bp-groupblog' );
		}
		bp_core_add_message( $message, 'error' );

		$redirect_url = bp_is_current_action( 'create' ) ? trailingslashit( bp_get_groups_directory_permalink() . 'create/step/' . bp_action_variable( 1 ) ) : bp_get_group_permalink( groups_get_current_group() ) . '/admin/group-blog/';

		$error_params = array(
			'create_error'    => '4815162342',
			'invalid_address' => isset( $_POST['blogname'] ) ? rawurlencode( sanitize_text_field( wp_unslash( $_POST['blogname'] ) ) ) : '',
			'invalid_name'    => isset( $_POST['blog_title'] ) ? rawurlencode( sanitize_text_field( wp_unslash( $_POST['blog_title'] ) ) ) : '',
		);
		$redirect_url = add_query_arg( $error_params, $redirect_url );
		bp_core_redirect( $redirect_url );

	}

	$public = isset( $_POST['blog_public'] ) ? (int) $_POST['blog_public'] : 0;

	groups_update_groupmeta( $group_id, 'groupblog_public', $public );
	groups_update_groupmeta( $group_id, 'groupblog_title', $blog_title );
	groups_update_groupmeta( $group_id, 'groupblog_path', $path );
	groups_update_groupmeta( $group_id, 'groupblog_domain', $domain );

	$meta = apply_filters(
		'signup_create_blog_meta',
		array(
			'lang_id' => 1,
			'public'  => $public,
		)
	); // Deprecated.
	$meta = apply_filters( 'add_signup_meta', $meta );

	$groupblog_blog_id = wpmu_create_blog( $domain, $path, $blog_title, $current_user->ID, $meta, $wpdb->siteid );

	if ( ! empty( $filtered_results['errors'] ) ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$errors = $filtered_results['errors'];
	}

	return true;
}

/**
 * Detects a post edit and modifies the BP Groupblog activity entry if found.
 *
 * This is needed for BuddyPress 2.2+. Older versions of BP continues to use
 * the {@link bp_groupblog_set_group_to_post_activity()} function.
 *
 * @since 1.8.10
 *
 * @param string $new_status New status for the post.
 * @param string $old_status Old status for the post.
 * @param object $post       Post data.
 */
function bp_groupblog_catch_transition_post_type_status( $new_status, $old_status, $post ) {
	// Only needed for >= BP 2.2.
	if ( ! function_exists( 'bp_activity_post_type_update' ) ) {
		return;
	}

	// But not needed for BP 2.5. See BP ticket #6834.
	if ( function_exists( 'bp_register_post_types' ) ) {
		return;
	}

	// Bail if not a blog post.
	if ( 'post' !== $post->post_type ) {
		return;
	}

	// This is an edit.
	if ( $new_status === $old_status ) {
		// An edit of an existing post should update the existing activity item.
		if ( 'publish' === $new_status ) {
			$group_id = get_groupblog_group_id( get_current_blog_id() );

			// Grab existing activity ID.
			$id = bp_activity_get_activity_id(
				array(
					'component'         => 'groups',
					'type'              => 'new_groupblog_post',
					'item_id'           => $group_id,
					'secondary_item_id' => $post->ID,
				)
			);

			if ( empty( $id ) ) {
				return;
			}

			// Grab activity item and modify some properties.
			$activity = new BP_Activity_Activity( $id );

			$activity->content       = $post->post_content;
			$activity->date_recorded = bp_core_current_time();

			// Pass activity to our edit function.
			bp_groupblog_set_group_to_post_activity(
				$activity,
				array(
					'group_id' => $group_id,
					'post'     => $post,
				)
			);
		}
	}
}
add_action( 'transition_post_status', 'bp_groupblog_catch_transition_post_type_status', 10, 3 );

/**
 * Record the blog activity for the group - by Luiz Armesto
 *
 * @since 1.8.10 Added $args parameter.
 * @todo Move this functionality into bp_groupblog_catch_transition_post_type_status().
 *
 * @param BP_Activity_Activity $activity The activity object.
 * @param array                $args {
 *                    Optional. Handy if you've already parsed the blog post and group ID.
 *     @type WP_Post $post     The WP post object.
 *     @type int     $group_id The group ID.
 * }
 */
function bp_groupblog_set_group_to_post_activity( $activity, $args = array() ) {

	// Sanity check.
	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	// If we've using this function outside the regular BP activity save process,
	// set some variables.
	if ( ! empty( $args['post'] ) ) {
		$post     = $args['post'];
		$group_id = $args['group_id'];
		$id       = $activity->id;

		// Regular BP save routine.
	} else {
		// Stop if this activity item is not a blog post.
		if ( 'new_blog_post' !== $activity->type ) {
			return;
		}

		$blog_id  = $activity->item_id;
		$post_id  = $activity->secondary_item_id;
		$group_id = get_groupblog_group_id( $blog_id );

		// No group is attached to this blog, so stop now.
		if ( ! $group_id ) {
			return;
		}

		$post = get_post( $post_id );

		// Try to see if we are editing an existing groupblog post.
		$id = bp_activity_get_activity_id(
			array(
				'type'              => 'new_groupblog_post',
				'item_id'           => $group_id,
				'secondary_item_id' => $post_id,
			)
		);
	}

	// Fetch group data.
	$group = groups_get_group( array( 'group_id' => $group_id ) );

	// Only allow certain HTML tags in post titles.
	if ( ! empty( $post->post_title ) ) {
		$allowed_tags     = array(
			'em'     => array(),
			'strong' => array(),
		);
		$post->post_title = wp_kses( $post->post_title, $allowed_tags );
	}

	if ( ! empty( $id ) ) {

		// This is an existing blog post.
		if ( apply_filters( 'groupblog_skip_edit_activity', false ) ) {
			return;
		}

		$activity->id      = $id;
		$activity->user_id = $post->post_author;

		// translators: 1. User link; 2. Post link; 3. Group link.
		$action = sprintf( __( '%1$s edited the blog post %2$s in the group %3$s:', 'bp-groupblog' ), bp_core_get_userlink( $post->post_author ), '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>', '<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>' );

	} else {
		// This is a new blog post.
		// translators: 1. User link; 2. Post link; 3. Group link.
		$action = sprintf( __( '%1$s wrote a new blog post %2$s in the group %3$s:', 'bp-groupblog' ), bp_core_get_userlink( $post->post_author ), '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>', '<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>' );
	}

	// Build args for action filter.
	$filter_args = array(
		'new_post' => ! empty( $id ) ? true : false,
		'post'     => $post,
		'group'    => $group,
		'activity' => $activity,
	);

	/**
	 * Filters the activity action string.
	 *
	 * @since 1.9.3
	 *
	 * @param str $action The complete activity action string.
	 * @param array $filter_args The array of contextual variables.
	 */
	$activity->action = apply_filters( 'bp_groupblog_set_group_to_post_activity_action', $action, $filter_args );

	$activity->primary_link = get_permalink( $post->ID );

	// Replace the necessary values to display in group activity stream.
	$activity->item_id   = (int) $group_id;
	$activity->component = 'groups';

	// Use group's privacy settings for activity privacy.
	$activity->hide_sitewide = 'public' === $group->status ? 0 : 1;

	/*
	 * Need to set type as new_groupblog_post or filters won't work.
	 * @see bp_groupblog_posts()
	 */
	$activity->type = 'new_groupblog_post';

	remove_action( 'bp_activity_before_save', 'bp_groupblog_set_group_to_post_activity' );

	// Using this function outside BP's save routine requires us to manually save.
	if ( ! empty( $args['post'] ) ) {
		$activity->save();
	}

	// Update the last_active flag for the group.
	groups_update_last_activity( $group_id );
}
add_action( 'bp_activity_before_save', 'bp_groupblog_set_group_to_post_activity' );

/**
 * When a blog post is deleted, delete the activity item
 *
 * @since 1.8.5
 *
 * @param int $post_id The post ID.
 * @param int $blog_id The blog ID.
 * @param int $user_id The user ID.
 */
function bp_groupblog_remove_post( $post_id, $blog_id = 0, $user_id = 0 ) {
	// Bail if the activity or blogs components are not enabled.
	if ( ! bp_is_active( 'blogs' ) || ! bp_is_active( 'activity' ) ) {
		return;
	}

	global $wpdb, $bp;

	$post_id = (int) $post_id;

	if ( ! $blog_id ) {
		$blog_id = get_current_blog_id();
	}

	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	$group_id = get_groupblog_group_id( $blog_id );

	if ( ! $group_id ) {
		return false;
	}

	do_action( 'bp_groupblog_before_remove_post', $blog_id, $post_id, $user_id, $group_id );

	// Delete activity stream item.
	bp_blogs_delete_activity(
		array(
			'item_id'           => $group_id,
			'secondary_item_id' => $post_id,
			'type'              => 'new_groupblog_post',
			'component'         => $bp->groups->id,
		)
	);

	do_action( 'bp_groupblog_remove_post', $blog_id, $post_id, $user_id, $group_id );
}
add_action( 'wp_trash_post', 'bp_groupblog_remove_post', 5 );
add_action( 'delete_post', 'bp_groupblog_remove_post', 5 );

/**
 * Add "new_groupblog_post" activity type to "Posts" dropdown filter option.
 *
 * When the "Posts" option is selected in the activity dropdown filter, it
 * only filters activity items by blog posts and not groupblog posts. This
 * function allows both types of blog posts to be filtered in activity loops.
 *
 * @since 1.8.9
 *
 * @param string $qs          The querystring for the BP loop.
 * @param string $object_type The current object for the querystring.
 * @return string Modified querystring.
 */
function bp_groupblog_override_new_blog_post_activity_filter( $qs, $object_type ) {
	// Not on the blogs object? stop now.
	if ( 'activity' !== $object_type ) {
		return $qs;
	}

	// Parse querystring into an array.
	$r = wp_parse_args( $qs );

	if ( empty( $r['type'] ) || 'new_blog_post' !== $r['type'] ) {
		return $qs;
	}

	// Calls from "bp_activity_heartbeat_last_recorded()" have no action key.
	if ( empty( $r['action'] ) ) {
		return $qs;
	}

	// Add the 'new_groupblog_post' type if it doesn't exist.
	if ( false === strpos( $r['action'], 'new_groupblog_post' ) ) {
		// 'action' filters activity items by the 'type' column.
		$r['action'] .= ',new_groupblog_post';
	}

	// 'type' isn't used anywhere internally.
	unset( $r['type'] );

	// Return a querystring.
	return build_query( $r );
}
add_filter( 'bp_ajax_querystring', 'bp_groupblog_override_new_blog_post_activity_filter', 20, 2 );

/**
 * See if users are able to comment to the activity entry of the groupblog post.
 *
 * @since 1.8.4
 *
 * @param bool $retval True if allowed, false otherwise.
 * @return bool Modified allowed value.
 */
function bp_groupblog_activity_can_comment( $retval ) {
	if ( 'new_groupblog_post' !== bp_get_activity_action_name() && 'new_groupblog_comment' !== bp_get_activity_action_name() ) {
		return $retval;
	}

	// Explicitly disable activity commenting on groupblog items.
	return false;
}
add_filter( 'bp_activity_can_comment', 'bp_groupblog_activity_can_comment' );

/**
 * Register 'new_groupblog_comment' action with the activity component.
 *
 * @since 1.9.0
 */
function bp_groupblog_register_activity_actions() {
	bp_activity_set_action(
		'groups',
		'new_groupblog_comment',
		__( 'New groupblog comment', 'bp-groupblog' ),
		'bp_groupblog_format_activity_action_new_groupblog_comment',
		bp_is_user() ? __( 'Groupblog Comments', 'bp-groupblog' ) : __( 'Blog Comments', 'bp-groupblog' ),
		array( 'activity', 'member', 'group' ),
		0
	);
}
add_action( 'bp_register_activity_actions', 'bp_groupblog_register_activity_actions' );

/**
 * Action format Callback for our 'new_groupblog_comment' activity type.
 *
 * @since 1.9.0
 *
 * @param str    $action   The action.
 * @param object $activity The activity object.
 * @return str $action The modified action.
 */
function bp_groupblog_format_activity_action_new_groupblog_comment( $action, $activity ) {
	$blog_id = get_groupblog_blog_id( $activity->item_id );

	$blog_url  = bp_blogs_get_blogmeta( $blog_id, 'url' );
	$blog_name = bp_blogs_get_blogmeta( $blog_id, 'name' );

	if ( empty( $blog_url ) || empty( $blog_name ) ) {
		$blog_url  = get_home_url( $blog_id );
		$blog_name = get_blog_option( $blog_id, 'blogname' );

		bp_blogs_update_blogmeta( $blog_id, 'url', $blog_url );
		bp_blogs_update_blogmeta( $blog_id, 'name', $blog_name );
	}

	$post_url   = bp_activity_get_meta( $activity->id, 'post_url' );
	$post_title = bp_activity_get_meta( $activity->id, 'post_title' );

	if ( empty( $activity->user_id ) ) {
		$anonymous = bp_activity_get_meta( $activity->id, 'anonymous_comment_author' );
	}

	// Should only be empty at the time of post creation.
	if ( empty( $post_url ) || empty( $post_title ) ) {
		switch_to_blog( $blog_id );

		$comment = get_comment( $activity->secondary_item_id );

		if ( ! empty( $comment->comment_post_ID ) ) {
			$post_url = add_query_arg( 'p', $comment->comment_post_ID, trailingslashit( $blog_url ) );
			bp_activity_update_meta( $activity->id, 'post_url', $post_url );

			$post = get_post( $comment->comment_post_ID );

			if ( is_a( $post, 'WP_Post' ) ) {
				$post_title = $post->post_title;
				bp_activity_update_meta( $activity->id, 'post_title', $post_title );
			}
		}

		if ( empty( $activity->user_id ) ) {
			$anonymous = $comment->comment_author;
			bp_activity_update_meta( $activity->id, 'anonymous_comment_author', $anonymous );
		}

		restore_current_blog();
	}

	$post_link = '<a href="' . esc_url( $post_url ) . '">' . $post_title . '</a>';
	$user_link = bp_core_get_userlink( $activity->user_id );

	if ( empty( $activity->user_id ) && ! empty( $anonymous ) ) {
		$user_link = esc_attr( $anonymous );
	} elseif ( empty( $activity->user_id ) ) {
		$user_link = esc_html__( 'Anonymous user', 'bp-groupblog' );
	}

	// Build the complete activity action string.
	// translators: 1. User link; 2. Post link; 3. Site lin.
	$action = sprintf( __( '%1$s commented on the post, %2$s, on the groupblog %3$s', 'bp-groupblog' ), $user_link, $post_link, '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );

	// Build args for filter.
	$args = array(
		'user_link' => $user_link,
		'post_link' => $post_link,
		'blog_url'  => $blog_url,
		'blog_name' => $blog_name,
		'blog_id'   => $blog_id,
		'activity'  => $activity,
	);

	/**
	 * Filters the activity action string.
	 *
	 * @since 1.9.3
	 *
	 * @param str $action The complete activity action string.
	 * @param array $args The array of contextual variables.
	 */
	$action = apply_filters( 'bp_groupblog_format_activity_action_new_groupblog_comment', $action, $args );

	return $action;
}

/**
 * Hook to switch 'new_blog_comment' activity type to 'new_groupblog_comment'.
 *
 * We're going to be piggybacking off of BuddyPress' existing blog comment
 * recording, but we're going to switch the activity type for groupblog
 * comments to our custom 'new_groupblog_comment' so groups can view these
 * items in their activity stream.
 *
 * @since 1.9.0
 *
 * @param object $activity The activity object.
 */
function bp_groupblog_activity_before_save( $activity ) {
	// We handle groupblog activities differently.
	if ( 'new_blog_comment' !== $activity->type ) {
		return;
	}

	/*
	 * See if the blog is connected to a group.
	 *
	 * If so, switch the activity properties around.
	 */
	$group_id = get_groupblog_group_id( $activity->item_id );
	if ( ! empty( $group_id ) ) {
		$activity->component = 'groups';
		$activity->type      = 'new_groupblog_comment';
		$activity->item_id   = $group_id;

		if ( ! $activity->hide_sitewide ) {
			$group = groups_get_group( $group_id );
			if ( 'public' !== $group->status ) {
				$activity->hide_sitewide = true;
			}
		}
	}
}
add_action( 'bp_activity_before_save', 'bp_groupblog_activity_before_save' );

/**
 * Groupblog comment status transition listener.
 *
 * @since 1.9.0
 *
 * @param string $new_status New comment status.
 * @param string $old_status Old comment status.
 * @param object $comment    Comment object.
 */
function bp_groupblog_transition_comment_status( $new_status, $old_status, $comment ) {
	$group_id = get_groupblog_group_id( get_current_blog_id() );
	if ( empty( $group_id ) ) {
		return;
	}

	buddypress()->activity->groupblog_temp_id = $group_id;

	$post_type = get_post_type( $comment->comment_post_ID );
	if ( 'post' !== $post_type ) {
		return;
	}

	if ( in_array( $new_status, array( 'delete', 'hold' ), true ) ) {
		bp_activity_delete_by_item_id(
			array(
				'item_id'           => $group_id,
				'secondary_item_id' => $comment->comment_ID,
				'component'         => 'groups',
				'type'              => 'new_groupblog_comment',
				'user_id'           => false,
			)
		);

		remove_action( 'transition_comment_status', 'bp_activity_transition_post_type_comment_status', 10 );
		return;
	}

	add_filter( 'bp_activity_get_activity_id', '_bp_groupblog_set_activity_id_for_groupblog_comment', 10, 2 );
	add_filter( 'bp_disable_blogforum_comments', '__return_true' );
}
add_action( 'transition_comment_status', 'bp_groupblog_transition_comment_status', 0, 3 );

/**
 * Set activity filters when posting groupblog comments.
 *
 * We need to hook into {@link bp_activity_post_type_comment()} to manipulate
 * how BuddyPress records blog post comments into the activity stream. This
 * is mainly to handle existing activity items and to generate separate
 * activity entries and not nested, activity comments.
 *
 * @since 1.9.0
 *
 * @param bool $retval  Whether the post should be published.
 * @param int  $blog_id ID of the blog.
 * @return bool $retval Filtered value for whether the post should be published.
 */
function bp_groupblog_activity_post_pre_comment( $retval, $blog_id ) {
	$group_id = get_groupblog_group_id( $blog_id );
	if ( empty( $group_id ) ) {
		return $retval;
	}

	buddypress()->activity->groupblog_temp_id = $group_id;

	add_filter( 'bp_activity_get_activity_id', '_bp_groupblog_set_activity_id_for_groupblog_comment', 10, 2 );
	add_filter( 'bp_disable_blogforum_comments', '__return_true' );

	return $retval;
}
add_filter( 'bp_activity_post_pre_comment', 'bp_groupblog_activity_post_pre_comment', 10, 2 );

/**
 * Delete corresponding activity item when groupblog comment is deleted.
 *
 * @since 1.9.0
 *
 * @param int $comment_id Blog comment ID.
 */
function bp_groupblog_delete_activity_on_delete_blog_comment( $comment_id ) {
	$group_id = get_groupblog_group_id( get_current_blog_id() );
	if ( empty( $group_id ) ) {
		return;
	}

	$comment   = get_comment( $comment_id );
	$post_type = get_post_type( $comment->comment_post_ID );
	if ( 'post' !== $post_type ) {
		return;
	}

	bp_activity_delete_by_item_id(
		array(
			'item_id'           => $group_id,
			'secondary_item_id' => $comment_id,
			'component'         => 'groups',
			'type'              => 'new_groupblog_comment',
			'user_id'           => false,
		)
	);

	remove_action( 'delete_comment', 'bp_activity_post_type_remove_comment', 10 );
}
add_action( 'delete_comment', 'bp_groupblog_delete_activity_on_delete_blog_comment', 0 );

/**
 * Delete corresponding post comments when groupblog activity item is deleted.
 *
 * @since 1.9.0
 *
 * @param array $activities The array of activity objects.
 */
function bp_groupblog_activity_after_delete( $activities ) {
	$switched = false;
	foreach ( $activities as $activity ) {
		if ( 'groups' === $activity->component && 'new_groupblog_comment' === $activity->type ) {
			$blog_id = get_groupblog_blog_id( $activity->item_id );

			if ( ! $switched ) {
				remove_action( 'transition_comment_status', 'bp_groupblog_transition_comment_status', 0 );
				remove_action( 'transition_comment_status', 'bp_activity_transition_post_type_comment_status', 10 );
				remove_action( 'delete_comment', 'bp_groupblog_delete_activity_on_delete_blog_comment', 0 );
				remove_action( 'delete_comment', 'bp_activity_post_type_remove_comment', 10 );

				if ( ! empty( $blog_id ) ) {
					switch_to_blog( $blog_id );
					$switched = true;
				}
			}

			if ( ! empty( $blog_id ) ) {
				wp_delete_comment( $activity->secondary_item_id, true );
			}
		}
	}

	if ( $switched ) {
		restore_current_blog();

		add_action( 'transition_comment_status', 'bp_groupblog_transition_comment_status', 0, 3 );
		add_action( 'transition_comment_status', 'bp_activity_transition_post_type_comment_status', 10, 3 );
		add_action( 'delete_comment', 'bp_groupblog_delete_activity_on_delete_blog_comment', 0 );
		add_action( 'delete_comment', 'bp_activity_post_type_remove_comment', 10 );
	}
}
add_action( 'bp_activity_after_delete', 'bp_groupblog_activity_after_delete' );

/**
 * Helper function to fetch the activity ID for a groupblog comment.
 *
 * @since 1.9.0
 *
 * @param  int   $activity_id Activity ID.
 * @param  array $args        Activity arguments used to fetch the activity ID.
 * @return int   $activity_id Activity ID.
 */
function _bp_groupblog_set_activity_id_for_groupblog_comment( $activity_id, $args ) {
	$groupblog_temp_id = isset( buddypress()->activity->groupblog_temp_id ) ? buddypress()->activity->groupblog_temp_id : null;
	if ( ! $groupblog_temp_id ) {
		return $activity_id;
	}

	$args['component'] = 'groups';
	$args['type']      = 'new_groupblog_comment';
	$args['item_id']   = buddypress()->activity->groupblog_temp_id;

	// Check BuddyPress version before getting the activity ID.
	if ( version_compare( bp_get_version(), '10.0.0', '>=' ) ) {

		// BuddyPress 10 expects an array.
		$activity_id = BP_Activity_Activity::get_id( $args );

	} else {

		// Legacy method.
		$activity_id = BP_Activity_Activity::get_id(
			$args['user_id'],
			$args['component'],
			$args['type'],
			$args['item_id'],
			$args['secondary_item_id'],
			$args['action'],
			$args['content'],
			$args['date_recorded']
		);

	}

	return $activity_id;
}

/**
 * Set the activity permalink for groupblog posts to the post permalink.
 *
 * @since 1.8.4
 *
 * @param string $retval   The URL.
 * @param object $activity The activity object.
 * @return str $retval The modified URL.
 */
function bp_groupblog_activity_permalink( $retval, $activity ) {
	// Not a groupblog post? Stop now.
	if ( 'new_groupblog_post' !== $activity->type && 'new_groupblog_comment' !== $activity->type ) {
		return $retval;
	}

	return $activity->primary_link;
}
add_filter( 'bp_activity_get_permalink', 'bp_groupblog_activity_permalink', 10, 2 );

/**
 * Add a filter option to the filter select box on group activity pages.
 *
 * @since 1.8.4
 */
function bp_groupblog_posts() {

	?>
	<option value="new_groupblog_post"><?php esc_html_e( 'Blog Posts', 'bp-groupblog' ); ?></option>
	<?php
}
add_action( 'bp_group_activity_filter_options', 'bp_groupblog_posts' );

/**
 * This screen gets called when the 'group blog' link is clicked.
 *
 * @since 1.0
 */
function groupblog_screen_blog() {

	if ( bp_is_groups_component() && bp_is_current_action( apply_filters( 'bp_groupblog_subnav_item_slug', 'blog' ) ) ) {

		$checks  = get_site_option( 'bp_groupblog_blog_defaults_options' );
		$blog_id = get_groupblog_blog_id();

		$home_url = ! empty( $blog_id ) ? get_home_url( get_groupblog_blog_id() ) : false;

		if ( isset( $checks['redirectblog'] ) && 1 === (int) $checks['redirectblog'] && ! empty( $home_url ) ) {
			wp_safe_redirect( $home_url );
			die();

		} elseif ( isset( $checks['redirectblog'] ) && 2 === (int) $checks['redirectblog'] && ! empty( $home_url ) ) {
			wp_safe_redirect( $home_url . '/' . $checks['pageslug'] . '/' );
			die();

		} elseif ( file_exists( locate_template( array( 'groupblog/blog.php' ) ) ) ) {
			bp_core_load_template( apply_filters( 'groupblog_screen_blog', 'groupblog/blog' ) );
			add_action( 'bp_screens', 'groupblog_screen_blog' );
		} elseif ( ! empty( groups_get_current_group() ) ) {
			add_action( 'bp_template_content', 'groupblog_screen_blog_content' );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
		}
	}
}

/**
 * Depending on the groupblog admin setup we load the correct template.
 *
 * @since 1.0
 */
function groupblog_screen_blog_content() {
	global $bp, $wp;

	load_template( WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-blog.php' );
}

/**
 * Redirect Group Home page to Blog Home page if set in admin settings.
 *
 * @since 1.0
 */
function groupblog_redirect_group_home() {
	global $bp;

	if ( bp_is_group_home() ) {

		$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );

		$blog_id = get_groupblog_blog_id();

		if ( isset( $checks['deep_group_integration'] ) && $checks['deep_group_integration'] && ! empty( $blog_id ) ) {
			$home_url = get_home_url( $blog_id );
			bp_core_redirect( $home_url );
		}
	}
}
add_action( 'bp_init', 'groupblog_redirect_group_home' );

/**
 * Clean up groupmeta after a blog gets deleted.
 *
 * @since 1.0
 *
 * @param int $blog_id The blog ID.
 */
function bp_groupblog_delete_meta( $blog_id ) {

	$group_id = get_groupblog_group_id( $blog_id );

	groups_update_groupmeta( $group_id, 'groupblog_enable_blog', '' );
	groups_update_groupmeta( $group_id, 'groupblog_blog_id', '' );

	groups_update_groupmeta( $group_id, 'groupblog_silent_add', '' );

	groups_update_groupmeta( $group_id, 'groupblog_default_admin_role', '' );
	groups_update_groupmeta( $group_id, 'groupblog_default_mod_role', '' );
	groups_update_groupmeta( $group_id, 'groupblog_default_member_role', '' );
}
add_action( 'delete_blog', 'bp_groupblog_delete_meta', 10, 1 );

/**
 * Use the group avatar on the Site Directory page for groupblogs.
 *
 * If a site in the site loop is a groupblog, use the group logo only if
 * the site doesn't already have a customized site icon.
 *
 * @since 1.9.2
 *
 * @param  string $retval  Current site avatar.
 * @param  int    $blog_id Site ID in loop.
 * @param  array  $r       Avatar arguments.
 * @return string
 */
function bp_groupblog_use_group_avatar_in_site_loop( $retval, $blog_id, $r ) {
	// Not a groupblog? Bail.
	$group_id = get_groupblog_group_id( $blog_id );
	if ( empty( $group_id ) ) {
		return $retval;
	}

	// Already using a site icon, so bail.
	$site_icon = bp_blogs_get_blogmeta( $blog_id, "site_icon_url_{$r['type']}" );
	if ( ! empty( $site_icon ) ) {
		return $retval;
	}

	// Site is using the site admin's avatar, so switch to group logo.
	return bp_core_fetch_avatar(
		array(
			'item_id'    => $group_id,
			'avatar_dir' => 'group-avatars',
			'object'     => 'group',
			'type'       => $r['type'],
			'alt'        => 'Group logo',
			'width'      => $r['width'],
			'height'     => $r['height'],
		)
	);
}
add_filter( 'bp_get_blog_avatar', 'bp_groupblog_use_group_avatar_in_site_loop', 10, 3 );
