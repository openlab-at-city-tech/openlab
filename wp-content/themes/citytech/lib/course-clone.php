<?php

/**
 * Course cloning
 */

/**
 * Get the courses that a user is an admin of
 */
function openlab_get_courses_owned_by_user( $user_id ) {
	global $wpdb, $bp;

	// This is pretty hackish, but the alternatives are all hacks too
	// First, get list of all groups a user is in
	$is_admin_of = BP_Groups_Member::get_is_admin_of( $user_id );
	$is_admin_of_ids = wp_list_pluck( $is_admin_of['groups'], 'id' );
	if ( empty( $is_admin_of_ids ) ) {
		$is_admin_of_ids = array( 0 );
	}

	// Next, get list of those that are courses
	$user_course_ids = $wpdb->get_col( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'course' AND group_id IN (" . implode( ',', wp_parse_id_list( $is_admin_of_ids ) ) . ")" );
	if ( empty( $user_course_ids ) ) {
		$user_course_ids = array( 0 );
	}

	// Finally, get a pretty list
	$user_courses = groups_get_groups( array(
		'type' => 'alphabetical',
		'include' => $user_course_ids,
		'show_hidden' => true,
	) );

	return $user_courses;
}

/**
 * AJAX handler for fetching group details
 */
function openlab_group_clone_fetch_details() {
	$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
	$retval = openlab_group_clone_details( $group_id );

	die( json_encode( $retval ) );
}
add_action( 'wp_ajax_openlab_group_clone_fetch_details', 'openlab_group_clone_fetch_details' );

function openlab_group_clone_details( $group_id ) {
	$retval = array(
		'group_id'               => $group_id,
		'name'                   => '',
		'description'            => '',
		'schools'                => array(),
		'departments'            => array(),
		'course_code'            => '',
		'section_code'           => '',
		'additional_description' => '',
		'site_id'                => '',
		'site_url'               => '',
	);

	if ( $group_id ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );

		$retval['name'] = $group->name;
		$retval['description'] = $group->description;

		$schools = groups_get_groupmeta( $group_id, 'wds_group_school' );
		if ( ! empty( $schools ) ) {
			$retval['schools'] = explode( ',', $schools );
		}

		$departments = groups_get_groupmeta( $group_id, 'wds_departments' );
		if ( ! empty( $departments ) ) {
			$retval['departments'] = explode( ',', $departments );
		}

		$retval['course_code'] = groups_get_groupmeta( $group_id, 'wds_course_code' );
		$retval['section_code'] = groups_get_groupmeta( $group_id, 'wds_section_code' );
		$retval['additional_description'] = groups_get_groupmeta( $group_id, 'wds_course_html' );

		$retval['site_id'] = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
		$retval['site_url'] = get_blog_option( $retval['site_id'], 'home' );
	}

	return $retval;
}
