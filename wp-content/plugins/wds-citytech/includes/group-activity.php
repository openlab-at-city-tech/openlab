<?php
/**
 * BP Group Activity.
 */

namespace CityTech\Group_Activity;

/**
 * Return activity types we want to filter.
 *
 * @return array
 */
function get_activity_types() {
	return [
		'added_group_document',
		'deleted_group_document',
		'edited_group_document',
		'bpeo_create_event',
		'bpeo_edit_event',
		'created_group',
		'group_details_updated',
		'joined_group',
	];
}

/**
 * Whether to hide activity based on group status.
 *
 * @param int $group_id
 * @return bool
 */
function group_hide_sitewide( $group_id = 0 ) {
	$group = \groups_get_group( [ 'group_id' => $group_id ] );

	return 'public' !== $group->status;
}

/**
 * Whether to hide activity based on blog privacy.
 *
 * @return bool
 */
function blog_hide_sitewide() {
	$privacy = get_blog_option( get_current_blog_id(), 'blog_public' );

	return 0 > (int) $privacy;
}

/**
 * Make sure 'hide_sitewide' flag is correctly set for private groups and blogs.
 *
 * @param bool|int $hide_sidewide
 * @param \BP_Activity_Activity $activity
 * @return bool|int
 */
function groups_activity_hide_sidewide( $hide_sidewide, $activity ) {
	// Bail if it's not group component.
	if ( 'groups' !== $activity->component ) {
		return $hide_sidewide;
	}

	if ( ! in_array( $activity->type, get_activity_types() ) ) {
		return $hide_sidewide;
	}

	if ( group_hide_sitewide( $activity->item_id ) || blog_hide_sitewide() ) {
		$hide_sidewide = 1;
	}

	return $hide_sidewide;
}
add_filter( 'bp_activity_hide_sitewide_before_save', __NAMESPACE__ . '\\groups_activity_hide_sidewide', 100, 2 );

/**
 * Filter the 'hide_sitewide' for BP docs to support blog privacy settings.
 *
 * @param bool|int $hide_sitewide
 * @return bool|int
 */
function docs_activity_hide_sitewide( $hide_sitewide ) {
	if ( blog_hide_sitewide() ) {
		$hide_sitewide = 1;
	}

	return $hide_sitewide;
}
add_filter( 'bp_docs_hide_sitewide', __NAMESPACE__ . '\\docs_activity_hide_sitewide' );
