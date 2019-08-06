<?php
/**
 * BP Group Activity.
 */

namespace CityTech\Group_Activity;

/**
 * Remove BP core hooks.
 */
remove_action( 'bp_blogs_new_blog', 'bp_blogs_record_activity_on_site_creation', 10, 4 );

/**
 * Return activity types we want to filter.
 *
 * @return array
 */
function get_activity_types() {
	return [
		'bbp_topic_create',
		'bbp_reply_create',
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
	$group = groups_get_group( [ 'group_id' => $group_id ] );

	return 'public' !== $group->status;
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

	if ( group_hide_sitewide( $activity->item_id ) ) {
		$hide_sidewide = 1;
	}

	return $hide_sidewide;
}
add_filter( 'bp_activity_hide_sitewide_before_save', __NAMESPACE__ . '\\groups_activity_hide_sidewide', 100, 2 );

/**
 * Add an activity entry for a newly-created site with correct privacy.
 *
 * @param BP_Blogs_Blog $blog          Current blog being recorded. Passed by reference.
 * @param bool          $is_private    Whether or not the current blog being recorded is private.
 * @param bool          $is_recorded   Whether or not the current blog was recorded.
 * @param bool          $no_activity   Whether to skip recording an activity item for this blog creation.
 * @return void
 */
function record_site_creation_activity( $blog, $is_private, $is_recoreed, $no_activity ) {
	if ( $no_activity ) {
		return null;
	}

	if ( ! bp_blogs_is_blog_trackable( $blog->blog_id, $blog->user_id ) ) {
		return;
	}

	$privacy = get_blog_option( $blog->blog_id, 'blog_public' );

	bp_blogs_record_activity( [
		'user_id'       => $blog->user_id,
		'primary_link'  => apply_filters( 'bp_blogs_activity_created_blog_primary_link', bp_blogs_get_blogmeta( $blog->blog_id, 'url' ), $blog->blog_id ),
		'type'          => 'new_blog',
		'item_id'       => $blog->blog_id,
		'hide_sitewide' => 0 > (int) $privacy,
	] );
}
add_action( 'bp_blogs_new_blog', __NAMESPACE__ . '\\record_site_creation_activity', 10, 4 );
