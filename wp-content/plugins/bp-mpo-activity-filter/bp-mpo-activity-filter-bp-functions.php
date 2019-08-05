<?php

/**
 * Reaches into the activity global and filters out items the user doesn't have access to
 *
 * Uses privacy settings from More Privacy Options
 *
 * @param boolean $has_activities True if there are activities, false otherwise
 * @param object $activities The BP activities template object
 * @param array $template_args The arguments used to init $activities_template
 * @return boolean $has_activities True if there are activities, false otherwise
 */
function bp_mpo_activity_filter( $has_activities, $activities, $template_args ) {
	global $bp;

	if ( is_super_admin() )
		return $has_activities;

	/**
	 * List of activity types that this plugin filters.
	 *
	 * @param array $activity_types List of activity type identifiers.
	 */
	$activity_types = apply_filters( 'bp_mpo_activity_types', array(
		'new_blog',
		'new_blog_post',
		'new_blog_comment',
		'new_groupblog_post',
		'new_groupblog_comment',
	) );

	foreach ( $activities->activities as $key => $activity ) {
		if ( in_array( $activity->type, $activity_types ) ) {

			$current_user = $bp->loggedin_user->id;

			// Account for bp-groupblog
			if ( $activity->component == 'groups' && bp_is_active( 'groups' ) && 0 === strpos( $activity->type, 'new_groupblog_' ) ) {
				$group_id = $activity->item_id;
				$blog_id = groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
			} else {
				$blog_id = $activity->item_id;
			}

			$privacy = get_blog_option( $blog_id, 'blog_public' );

			$remove_from_stream = false;

			switch ( $privacy ) {
				case '1':
				break;

				case '0':
				case '1':
					$remove_from_stream = ! is_user_logged_in();
				break;

				case '-2':
					if ( is_user_logged_in() ) {
						$meta_key = 'wp_' . $blog_id . '_capabilities';
						$caps = get_user_meta( $current_user, $meta_key, true );

						if ( empty( $caps ) ) {
							$remove_from_stream = true;
						}
					} else {
						$remove_from_stream = true;
					}
				break;

				case '-3':
					if ( is_user_logged_in() ) {
						switch_to_blog( $blog_id );

						$user = new WP_User( $current_user );

						if ( ! in_array( 'administrator', $user->roles ) ) {
							$remove_from_stream = true;
						}
						restore_current_blog();
					} else {
						$remove_from_stream = true;
					}
				break;

			}

			if ( $remove_from_stream ) {
				$activities->activity_count = $activities->activity_count - 1;
				unset( $activities->activities[$key] );

			}
		}
	}

	$activities_new = array_values( $activities->activities );
	$activities->activities = $activities_new;

	return $activities->has_activities();
}
add_filter( 'bp_has_activities', 'bp_mpo_activity_filter', 10, 3 );


// Filter the output of 'bp_get_activity_count' to account for the fact that there are fewer items. A total hack.
function bp_mpo_activity_count() {
	return '20';
}
add_action( 'bp_get_activity_count', 'bp_mpo_activity_count' );
