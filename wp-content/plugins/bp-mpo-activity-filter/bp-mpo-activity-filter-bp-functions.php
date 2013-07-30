<?php

/**
 * Reaches into the activity global and filters out items the user doesn't have access to
 *
 * Uses privacy settings from More Privacy Options
 */
function bp_mpo_activity_filter( $a, $activities ) {
	global $bp;

	if ( is_super_admin() )
		return $activities;

	foreach ( $activities->activities as $key => $activity ) {
		if ( $activity->type == 'new_blog_post' || $activity->type == 'new_blog_comment' ) {

			$current_user = $bp->loggedin_user->id;

			// Account for bp-groupblog
			if ( $activity->component == 'groups' ) {
				$group_id = $activity->item_id;
				$blog_id = groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
			} else {
				$blog_id = $activity->item_id;
			}

			$privacy = get_blog_option( $blog_id, 'blog_public' );

			$remove_from_stream = false;

			switch ( $privacy ) {
				case '1':
					continue;
					break;

				case '0':
					if ( $current_user != 0 ) {
						continue;
						}
					else {
						$remove_from_stream = true;
					}
					break;


				case '-1':
					if ( $current_user != 0 )
						continue;
					else {
						$remove_from_stream = true;
					}
					break;

				case '-2':
					if ( is_user_logged_in() ) {
						$meta_key = 'wp_' . $blog_id . '_capabilities';
						$caps = get_user_meta( $current_user, $meta_key, true );

						if ( !empty( $caps ) ) {
							continue;
						} else {
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

						if ( in_array( 'administrator', $user->roles ) )
							continue;
						else {
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

	return $activities;
}
add_action( 'bp_has_activities', 'bp_mpo_activity_filter', 10, 2 );


// Filter the output of 'bp_get_activity_count' to account for the fact that there are fewer items. A total hack.
function bp_mpo_activity_count() {
	return '20';
}
add_action( 'bp_get_activity_count', 'bp_mpo_activity_count' );
