<?php
/**
 * BP Classic Groups Functions.
 *
 * @package bp-classic\inc\groups
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output group directory permalink.
 *
 * @since 1.0.0
 */
function bp_groups_directory_permalink() {
	bp_groups_directory_url();
}

/**
 * Return group directory permalink.
 *
 * @since 1.0.0
 *
 * @return string The group directory permalink.
 */
function bp_get_groups_directory_permalink() {
	$url = bp_get_groups_directory_url();

	/**
	 * Filters the group directory permalink.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Permalink for the group directory.
	 */
	return apply_filters( 'bp_get_groups_directory_permalink', $url );
}

/**
 * Output the permalink for the group.
 *
 * @since 1.0.0
 *
 * @param false|int|string|BP_Groups_Group $group (Optional) The Group ID, the Group Slug or the Group object.
 *                                                Default: false.
 */
function bp_group_permalink( $group = false ) {
	bp_group_url( $group );
}

/**
 * Return the permalink for the group.
 *
 * @since 1.0.0
 *
 * @param false|int|string|BP_Groups_Group $group (Optional) The Group ID, the Group Slug or the Group object.
 *                                                Default: false.
 * @return string The permalink for the group.
 */
function bp_get_group_permalink( $group = false ) {
	$url = bp_get_group_url( $group );

	/**
	 * Filters the permalink for the group.
	 *
	 * @since 1.0.0
	 *
	 * @param string          $url   Permalink for the group.
	 * @param BP_Groups_Group $group The group object.
	 */
	return apply_filters( 'bp_get_group_permalink', $url, $group );
}

/**
 * Output the permalink for the admin section of the group.
 *
 * @since 1.0.0
 *
 * @param false|int|string|BP_Groups_Group $group (Optional) The Group ID, the Group Slug or the Group object.
 *                                                Default: false.
 */
function bp_group_admin_permalink( $group = false ) {
	bp_group_manage_url( $group );
}

/**
 * Return the permalink for the admin section of the group.
 *
 * @since 1.0.0
 *
 * @param false|int|string|BP_Groups_Group $group (Optional) The Group ID, the Group Slug or the Group object.
 *                                                Default: false.
 * @return string The permalink for the admin section of the group.
 */
function bp_get_group_admin_permalink( $group = false ) {
	$permalink = bp_get_group_manage_url( $group );

	/**
	 * Filters the permalink for the admin section of the group.
	 *
	 * @since 1.0.0
	 *
	 * @param string          $permalink Permalink for the admin section of the group.
	 * @param BP_Groups_Group $group     The group object.
	 */
	return apply_filters( 'bp_get_group_admin_permalink', $permalink, $group );
}
