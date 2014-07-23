<?php
function WtiLikePostProcessVote() {
	global $wpdb;
	
	// Get request data
	$post_id = (int)$_REQUEST['post_id'];
	$task = $_REQUEST['task'];
	$ip = WtiGetRealIpAddress();
	
	// Check for valid access
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'wti_like_post_vote_nonce' ) ) {
		$error = 1;
		$msg = __( 'Invalid access', 'wti-like-post' );
	} else {
		// Get setting data
		$is_logged_in = is_user_logged_in();
		$login_required = get_option( 'wti_like_post_login_required' );
		$can_vote = false;

		if ( $login_required && !$is_logged_in ) {
			// User needs to login to vote but has not logged in
			$error = 1;
			$msg = get_option( 'wti_like_post_login_message' );
		} else {
			$has_already_voted = HasWtiAlreadyVoted( $post_id, $ip );
			$voting_period = get_option( 'wti_like_post_voting_period' );
			$datetime_now = date( 'Y-m-d H:i:s' );
			
			if ( "once" == $voting_period && $has_already_voted ) {
				// User can vote only once and has already voted.
				$error = 1;
				$msg = get_option( 'wti_like_post_voted_message' );
			} elseif ( '0' == $voting_period ) {
				// User can vote as many times as he want
				$can_vote = true;
			} else {
				if ( !$has_already_voted ) {
					// Never voted befor so can vote
					$can_vote = true;
				} else {
					// Get the last date when the user had voted
					$last_voted_date = GetWtiLastVotedDate( $post_id, $ip );
					
					// Get the bext voted date when user can vote
					$next_vote_date = GetWtiNextVoteDate( $last_voted_date, $voting_period );
					
					if ( $next_vote_date > $datetime_now ) {
						$revote_duration = ( strtotime( $next_vote_date ) - strtotime( $datetime_now ) ) / ( 3600 * 24 );
						
						$can_vote = false;
						$error = 1;
						$msg = __( 'You can vote after', 'wti-like-post' ) . ' ' . ceil( $revote_duration ) . ' ' . __( 'day(s)', 'wti-like-post' );
					} else {
						$can_vote = true;
					}
				}
			}
		}
		
		if ( $can_vote ) {
			$current_user = wp_get_current_user();
			$user_id = (int)$current_user->ID;
			
			if ( $task == "like" ) {
				if ( $has_already_voted ) {
					$query = "UPDATE {$wpdb->prefix}wti_like_post SET ";
					$query .= "value = value + 1, ";
					$query .= "date_time = '" . date( 'Y-m-d H:i:s' ) . "' ";
					$query .= "WHERE post_id = '" . $post_id . "' AND ";
					$query .= "ip = '$ip'";
				} else {			
					$query = "INSERT INTO {$wpdb->prefix}wti_like_post SET ";
					$query .= "post_id = '" . $post_id . "', ";
					$query .= "value = '1', ";
					$query .= "date_time = '" . date( 'Y-m-d H:i:s' ) . "', ";
					$query .= "ip = '$ip'";
				}
			} else {
				if ( $has_already_voted ) {
					$query = "UPDATE {$wpdb->prefix}wti_like_post SET ";
					$query .= "value = value - 1, ";
					$query .= "date_time = '" . date( 'Y-m-d H:i:s' ) . "' ";
					$query .= "WHERE post_id = '" . $post_id . "' AND ";
					$query .= "ip = '$ip'";
				} else {
					$query = "INSERT INTO {$wpdb->prefix}wti_like_post SET ";
					$query .= "post_id = '" . $post_id . "', ";
					$query .= "value = '-1', ";
					$query .= "date_time = '" . date( 'Y-m-d H:i:s' ) . "', ";
					$query .= "ip = '$ip'";
				}
			}
			
			$success = $wpdb->query( $query );
			
			if ($success) {
				$error = 0;
				$msg = get_option( 'wti_like_post_thank_message' );
			} else {
				$error = 1;
				$msg = __( 'Could not process your vote.', 'wti-like-post' );
			}
		}
		
		$options = get_option( 'wti_most_liked_posts' );
		$number = $options['number'];
		$show_count = $options['show_count'];
		
		$wti_like_count = GetWtiLikeCount( $post_id );
		$wti_unlike_count = GetWtiUnlikeCount( $post_id );
	}
	
	// Check for method of processing the data
	if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
		$result = array(
					"msg" => $msg,
					"error" => $error,
					"like" => $wti_like_count,
					"unlike" => $wti_unlike_count
				);
		
		echo json_encode($result);
	} else {
		header( "location:" . $_SERVER["HTTP_REFERER"] );
	}
	
	exit;
}