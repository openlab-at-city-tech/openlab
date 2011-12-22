<?php

function bp_mpo_activity_filter( $a, $activities ) {
	global $bp;

	if ( is_super_admin() )
		return $activities;

	foreach ( $activities->activities as $key => $activity ) {
		if ( $activity->component == 'blogs' ) {
			$blog_id = $activity->item_id;
			$current_user = $bp->loggedin_user->id;
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
						switch_to_blog( $blog_id );
	
						$user = new WP_User( $current_user );
	
						if ( !empty( $user->caps ) )
							continue;
						else {
							$remove_from_stream = true;
						}
						restore_current_blog();
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
	/* Renumber the array keys to account for missing items */
	$activities_new = array_values( $activities->activities );

	$activities->activities = $activities_new;
	//print "<pre>"; print_r($activities);
	return $activities;
}
add_action( 'bp_has_activities', 'bp_mpo_activity_filter', 10, 2 );


// Filter the output of 'bp_get_activity_count' to account for the fact that there are fewer items. A total hack.
function bp_mpo_activity_count() {
	return '20';
}
add_action( 'bp_get_activity_count', 'bp_mpo_activity_count' );
?>