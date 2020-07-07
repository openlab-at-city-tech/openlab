<?php
function WtiLikePostProcessVote() {
	global $wpdb, $wti_ip_address;
	
	// Get request data
	$post_id = (int)$_REQUEST['post_id'];
	$task = $_REQUEST['task'];
	
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
			$msg = esc_html(get_option( 'wti_like_post_login_message' ));
		} else {
			$has_already_voted = HasWtiAlreadyVoted( $post_id, $wti_ip_address );
			$voting_period = get_option( 'wti_like_post_voting_period' );
			$datetime_now = date( 'Y-m-d H:i:s' );
			
			if ( "once" == $voting_period && $has_already_voted ) {
				// User can vote only once and has already voted.
				$error = 1;
				$msg = esc_html(get_option( 'wti_like_post_voted_message' ));
			} elseif ( '0' == $voting_period ) {
				// User can vote as many times as he want
				$can_vote = true;
			} else {
				if ( !$has_already_voted ) {
					// Never voted befor so can vote
					$can_vote = true;
				} else {
					// Get the last date when the user had voted
					$last_voted_date = GetWtiLastVotedDate( $post_id, $wti_ip_address );
					
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
					$success = $wpdb->query(
								$wpdb->prepare(
									"UPDATE {$wpdb->prefix}wti_like_post SET 
									value = value + 1,
									date_time = '" . date( 'Y-m-d H:i:s' ) . "',
									user_id = %d WHERE post_id = %d AND ip = %s",
									$user_id, $post_id, $wti_ip_address
								)
							);
				} else {
					$success = $wpdb->query(
								$wpdb->prepare(
									"INSERT INTO {$wpdb->prefix}wti_like_post SET 
									post_id = %d, value = '1',
									date_time = '" . date( 'Y-m-d H:i:s' ) . "',
									user_id = %d, ip = %s",
									$post_id, $user_id, $wti_ip_address
								)
							);
				}
			} else {
				if ( $has_already_voted ) {
					$success = $wpdb->query(
								$wpdb->prepare(
									"UPDATE {$wpdb->prefix}wti_like_post SET 
									value = value - 1,
									date_time = '" . date( 'Y-m-d H:i:s' ) . "',
									user_id = %d WHERE post_id = %d AND ip = %s",
									$user_id, $post_id, $wti_ip_address
								)
							);
				} else {
					$success = $wpdb->query(
								$wpdb->prepare(
									"INSERT INTO {$wpdb->prefix}wti_like_post SET 
									post_id = %d, value = '-1',
									date_time = '" . date( 'Y-m-d H:i:s' ) . "',
									user_id = %d, ip = %s",
									$post_id, $user_id, $wti_ip_address
								)
							);
				}
			}
			
			if ($success) {
				$error = 0;
				$msg = esc_html(get_option( 'wti_like_post_thank_message' ));
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
		wp_safe_redirect($_SERVER["HTTP_REFERER"]);
	}
	
	exit;
}