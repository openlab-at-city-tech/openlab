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
					continue 2;
					break;

				case '0':
					if ( $current_user != 0 ) {
						continue 2;
						}
					else {
						$remove_from_stream = true;
					}
					break;


				case '-1':
					if ( $current_user != 0 )
						continue 2;
					else {
						$remove_from_stream = true;
					}
					break;

				case '-2':
					if ( is_user_logged_in() ) {
						$meta_key = 'wp_' . $blog_id . '_capabilities';
						$caps = get_user_meta( $current_user, $meta_key, true );

						if ( !empty( $caps ) ) {
							continue 2;
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

						if ( in_array( 'administrator', $user->roles ) ) {
							restore_current_blog();
							continue 2;
						} else {
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

/**
 * Prevent activity from private sites to be visible in sitewide stream.
 *
 * We deem a site as private, if the Site Visibility setting is anything
 * but "Allow search engines to index this site". If a site is private,
 * we set the 'hide_sitewide' activity flag to 1.
 *
 * @param BP_Activity_Activity $activity Activity object.
 */
function bp_mpo_set_hide_sitewide_for_private_sites( $activity ) {
	if ( 'blogs' !== $activity->component ) {
		return;
	}

	$privacy = (int) get_blog_option( $activity->item_id, 'blog_public' );

	if ( $privacy < 1 ) {
		$activity->hide_sitewide = 1;
	}
}
add_action( 'bp_activity_before_save', 'bp_mpo_set_hide_sitewide_for_private_sites', 0 );
