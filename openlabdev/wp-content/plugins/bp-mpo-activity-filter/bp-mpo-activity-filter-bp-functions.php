<?php

function bp_mpo_activity_filter( $a, $activities ) {
	global $bp; 
	
	if ( is_site_admin() )
		return $activities;	
	
	foreach ( $activities->activities as $key => $activity ) {
		if ( $activity->component == 'blogs' ) {
			$blog_id = $activity->item_id;
			$current_user = $bp->loggedin_user->id;
			$privacy = get_blog_option( $blog_id, 'blog_public' );
			
			switch ( $privacy ) {
				case '1':
					continue;
					break;
					
				case '0':
					if ( $current_user != 0 ) {
						continue;
						}
					else
						unset( $activities->activities[$key] );
					break;			
				
				
				case '-1':
					if ( $current_user != 0 )
						continue;
					else
						unset( $activities->activities[$key] );
					break;
					
				case '-2':
					switch_to_blog( $blog_id );
					
					$user = new WP_User( $current_user );
					
					if ( !empty( $user->caps ) )
						continue;
					else
						unset( $activities->activities[$key] );
					
					restore_current_blog();
					break;
				
				case '-3':
					switch_to_blog( $blog_id );
					
					$user = new WP_User( $current_user );
					
					if ( in_array( 'administrator', $user->roles ) )
						continue;
					else
						unset( $activities->activities[$key] );
					
					restore_current_blog();
					break;					
				
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
?>