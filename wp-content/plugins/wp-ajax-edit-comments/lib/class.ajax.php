<?php
class AECAjax {
		/*** BEGIN AJAX ACTIONS **/
		public static function action_getthetimeleft() {
			$commentID = AECAjax::get_comment_id();
			AECAjax::get_time_left($commentID);
			exit;
		} //end ajax_action_getthetimeleft
		public static function action_editcomment() {
			check_ajax_referer('wp-ajax-edit-comments_save-comment');
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			AECAjax::get_comment($commentID, $postID);
			exit;
		} //end action_editcomment
		public static function action_movecomment() {
			check_ajax_referer('wp-ajax-edit-comments_move-comment');
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			if (isset($_POST['post_offset'])) {
				$response = AECAjax::get_posts( absint( $_POST['post_offset']), $postID );
			}
			if (isset($_POST['post_title'])) {
				$response = AECAjax::get_posts_by_title($_POST['post_title'], $postID );
			}
			if (isset($_POST['post_id'])) {
				$response = AECAjax::get_posts_by_id( absint( $_POST['post_id'] ) );
			}
			if (isset($_POST['newid'])) {
				$response = AECAjax::move_comment($commentID, absint( $_POST['pid'] ), absint( $_POST['newid'] ) );
			}
			$response['comment_id'] = $commentID;
			if (isset($_POST['approve'])) {
				if ($_POST['approve'] == "1") {
					AECAjax::approve_comment($commentID, $postID);
					$approve_count = get_comment_count($postID);
					$comment_count = wp_count_comments();
					$response['approved'] = array( 
						'comment_id' => $commentID,
						'approve_count' => $comment_count->approved,
						'moderation_count' => $comment_count->moderated,
						'spam_count' => $comment_count->spam,
						'trash_count' => $comment_count->trash
					);
				}
			}
			$response['status_message'] = "<em>". __("Move Successful. ", 'ajaxEdit') . "</em>";
			die( json_encode( $response ) );

		} //end action_movecomment
		public static function action_savecomment() {
			check_ajax_referer('wp-ajax-edit-comments_save-comment');
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			AECAjax::maybe_change_comment_status( $commentID, $postID );
			$comment = get_comment($commentID, ARRAY_A);
			$comment['comment_content'] = trim(urldecode($_POST['comment_content']));
			if (AECCore::can_edit_name($commentID, $postID)) {
				$comment['comment_author'] = trim(strip_tags(urldecode($_POST['comment_author'])));
			}
			if (AECCore::can_edit_email($commentID, $postID)) {
				$comment['comment_author_email'] = trim(strip_tags(urldecode($_POST['comment_author_email'])));
			}
			if (AECCore::can_edit_url($commentID, $postID)) {
				$comment['comment_author_url'] = trim(strip_tags(urldecode($_POST['comment_author_url'])));
				//Quick JS Test
				if ($comment['comment_author_email'] == "undefined") {$comment['comment_author_email']='';}
				if ($comment['comment_author_url'] == "undefined") {$comment['comment_author_url']='http://';}
				if ($comment['comment_author'] == "undefined") {$comment['comment_author']='';}
			}
			//For the date function
			if (isset($_POST['aa'])) {
				$aa = (int)urldecode($_POST['aa']);
				$mm = (int)urldecode($_POST['mm']);
				$jj = (int)urldecode($_POST['jj']);
				$hh = (int)urldecode($_POST['hh']);
				$mn = (int)urldecode($_POST['mn']);
				$ss = (int)urldecode($_POST['ss']);
				$jj = ($jj > 31 ) ? 31 : $jj;
				$hh = ($hh > 23 ) ? $hh -24 : $hh;
				$mn = ($mn > 59 ) ? $mn -60 : $mn;
				$ss = ($ss > 59 ) ? $ss -60 : $ss;
				$comment['comment_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
			}
			$response = AECAjax::save_comment($commentID, $postID, $comment);
			$response = apply_filters('wp_ajax_comments_save_comment', $response, $comment, $_POST);
			die( json_encode( $response ) );
		} //end action_savecomment
		public static function action_blacklist() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer('wp-ajax-edit-comments_blacklist-comment');
			
			global $wpdb;
			$comment = get_comment($commentID, ARRAY_A);
			$spamCount = 0; $blacklisted = false;
			
			
			//Get the parameters
			$params = explode(',',$_POST['parameters']);
			$where = '';
			$blacklistparams = array();
			foreach ($params as $p) {
				switch($p) {
					case "name":
						$blacklistparams[$p] = $comment['comment_author'];
					break;
					case "email":
						$blacklistparams[$p] = $comment['comment_author_email'];
					break;
					case "ip":
						$blacklistparams[$p] = $comment['comment_author_IP'];
					break;
					case "url":
						$blacklistparams[$p] = $comment['comment_author_url'];
					break;
					case "spamname":
						$blacklistparams[$p] = $comment['comment_author'];
						$where .= "comment_author = '" . $comment['comment_author'] . "' and ";
					break;
					case "spamemail":
						$blacklistparams[$p] = $comment['comment_author_email'];
						$where .= "comment_author_email = '" . $comment['comment_author_email'] . "' and ";
					break;
					case "spamip":
						$blacklistparams[$p] = $comment['comment_author_IP'];
						$where .= "comment_author_ip = '" . $comment['comment_author_IP'] . "' and ";
					break;
					case "spamurl":
						$blacklistparams[$p] = $comment['comment_author_url'];
						$where .= "comment_author_ip = '" . $comment['comment_author_url'] . "' and ";
					break;
				}
			} //end foreach
			
			//Do some error checking to make sure something was indeed selected
			if (empty($where) && sizeof($blacklistparams) == 0) {
				die( json_encode( array( 'error' => $aecomments->get_error('blacklist_empty') ) ) );
			}
			if ($comment['comment_approved'] != "spam") {
				//Spam the comment
				$spamCount += 1;
				AECAjax::spam_comment($commentID, $postID);
			}
			$blacklist = '';
			if (sizeof($blacklistparams) > 0) {
				//Retrieve the blacklist
				$blacklist = get_option('blacklist_keys');
				
				foreach ($blacklistparams as $b) {
					//Check to see if it's already in the blacklist so we can avoid duplicates
					if (!strpos($blacklist,$b)) {
						$blacklist .= "\n$b";
					}
				}
				//Save the blacklist
				update_option('blacklist_keys', stripslashes_deep($blacklist));
			}
			
			$query = '';
			//Get comments to spam
			if (!empty($where)) {
				$where = preg_replace('/and $/','',$where); //strip out excessive ands
				$query .= "select * from $wpdb->comments where $where and comment_approved != 'spam'";
				$results = $wpdb->get_results($query, ARRAY_A);
				if ($results) {
					foreach ($results as $r) {
						$spamCount += 1;
						AECAjax::spam_comment(intval($r['comment_ID']), intval($r['comment_post_ID']));
					}
				}
			}
			//Build the results
			$comment_count = wp_count_comments();
			$response =  array(
							'errors' => '',
							'spam_count' => $spamCount,
							'query' => $query,
							'blacklist' => $blacklist,
							'message' => __("Successfully blacklisted.", 'ajaxEdit') . " " . $spamCount . " " . __("comment(s) were marked as spam according to your advanced criteria.", 'ajaxEdit'),
							'comment_links' => AECCore::build_admin_links($commentID, $postID),
							'moderation_count' => $comment_count->moderated,
							'spam_count' => $comment_count->spam
				);
				die( json_encode( $response ) );
		} //end action_blacklist
		public static function action_email() {
			check_ajax_referer('wp-ajax-edit-comments_email-comment');
			error_log('did not make it far');
			$site_name = str_replace('"', "'", get_bloginfo('name'));
			$to = sanitize_text_field(urldecode($_POST['to']));
			$from = sanitize_text_field(urldecode($_POST['from']));
			error_log('did make it a little further');
			$subject = sanitize_text_field(strip_tags(urldecode($_POST['subject'])));
			//Validate the e-mail address
			if (!is_email($to) && !empty($to)) {
				die( json_encode( array( 'error' => 'Invalid Email' ) ) );
			}
			$message = strip_tags(urldecode($_POST['message']));
			$charset =  get_option('blog_charset');
			$headers  = "From: \"{$site_name}\" <{$from}>\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
			//Send the e-mail
			wp_mail($to, $subject, $message, $headers);
			error_log('almost to the end');
			die( json_encode( array( 'success' => 'Valid Email' ) ) );
		} //end action_email
		
		public static function action_deletecomment() {
			global $aecomments;
			
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			
			
			
			//Save the old comment for undo
			check_ajax_referer("deletecomment_{$commentID}");
			$comment = get_comment($commentID, ARRAY_A);
			$aecomments->save_admin_option( 'undo', $comment );
			
			$message = AECAjax::delete_comment($commentID, $postID); 
			//Get error messages
			if ($message != "1") {
				die( json_encode( array( 'error' => $aecomments->get_error($message) ) ) );
			}
			//Build the undo URL
			$undo = AECUtility::build_undo_url("undodelete", $commentID, $postID, __("Successfully Deleted",'ajaxEdit'));
			//Send the response
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			
			$response = array(
				'undo' => $undo,
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'trash_count' =>$comment_count->trash,
				'comment_links' => false
			); 
			die( json_encode( $response ) );
		} //end action_deletecomment
		public static function action_restore() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("restore_{$commentID}");
			wp_untrash_comment($commentID);
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			$response = array(
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'comment_links' => AECCore::build_admin_links($commentID, $postID)
			);
			die( json_encode( $response ) );
		}
		public static function action_deleteperm() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("deleteperm_{$commentID}");
			$message = AECAjax::delete_comment($commentID, $postID); 
			//Get error messages
			if ($message != "1") {
				die( json_encode( array( 'error' => $aecomments->get_error($message) ) ) );
			}
			//Send the response
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			
			$response = array(
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'trash_count' =>$comment_count->trash
			);
			die( json_encode( $response ) );
		} //end action_deleteperm
		public static function action_requestdeletecomment() {
			global $aecomments;
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("requestdeletecomment_{$commentID}");
			$status = wp_delete_comment($commentID);
			if ( !$status ) {
				die( json_encode( array( 'error' => $aecomments->get_error( 'delete_failed' ), 'cid' => $commentID ) ) );
			} else {
				die( json_encode( array( 'success' => __( 'Comment deleted.', 'ajaxEdit' ), 'cid' => $commentID ) ) );
			}
		} //end functon requestdeletecomment
		public static function action_spamcomment() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("spamcomment_{$commentID}");
			//Save the old comment for undo
			$comment = get_comment($commentID, ARRAY_A);
			$aecomments->save_admin_option( 'undo', $comment );
			
			$message = AECAjax::spam_comment($commentID, $postID);
			//Get error messages
			if ($message != "1") {
				die( json_encode( array( 'error' => $aecomments->get_error($message) ) ) );
			}
			//Build the undo URL
			$undo = AECUtility::build_undo_url("undospam", $commentID, $postID, __("Successfully Marked as Spam",'ajaxEdit'));
			//Send the response
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			$response = array(
				'undo' => $undo,
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'comment_links' => AECCore::build_admin_links($commentID, $postID)
			);
			die( json_encode( $response ) );
		} //end function action_spamcomment
		public static function action_approvecomment() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			//Save the old comment for undo
			check_ajax_referer("approvecomment_{$commentID}");
			$comment = get_comment($commentID, ARRAY_A);
			$aecomments->save_admin_option( 'undo', $comment );
			
			$message = AECAjax::approve_comment($commentID, $postID);
			//Get error messages
			if ($message != "1") {
			 	die( json_encode( array( 'error' => $aecomments->get_error($message) ) ) );
			}
			//Build the undo URL
			$undo = AECUtility::build_undo_url("undoapprove", $commentID, $postID, __("Successfully Approved",'ajaxEdit'));
			//Send the response
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			$response = array(
				'undo' => $undo,
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'comment_links' => AECCore::build_admin_links($commentID, $postID)
			);
			die( json_encode( $response ) );
		} //end action_approvecomment
		public static function action_unapprovecomment() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("unapprovecomment_{$commentID}");
			$message = AECAjax::moderate_comment($commentID, $postID);
			
			//Get error messages
			if ($message != "1") {
				 die( json_encode( array( 'error' => $aecomments->get_error($message) ) ) );
			}
			//Build the undo URL
			$undo = AECUtility::build_undo_url("undomoderate", $commentID, $postID, __("Successfully Marked for Moderation",'ajaxEdit'));
			//Send the response
			$approve_count = get_comment_count($postID);
			$comment_count = wp_count_comments();
			$response = array(
				'undo' => $undo,
				'approve_count' => $approve_count['approved'],
				'moderation_count' => $comment_count->moderated,
				'spam_count' => $comment_count->spam,
				'comment_links' => AECCore::build_admin_links($commentID, $postID)
			);
			die( json_encode( $response ) );
		} //end function action_unapprovecomment
		public static function action_delinkcomment() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("delinkcomment_{$commentID}");
			$comment = get_comment($commentID, ARRAY_A);
			$aecomments->save_admin_option( 'undo', $comment );
			$comment['comment_author_url'] = '';
			if ($aecomments->get_admin_option( 'delink_content' ) == 'true') {
				$pattern = '/<[^>]*>([^<]*)<\/a>/';
				$comment['comment_content'] = preg_replace($pattern, '$1', $comment['comment_content']);
			}
			$message = AECAjax::delink_comment($commentID, $postID, $comment);
			//Get error messages
			if ($message != "1") {
				$error = $aecomments->get_error($message);
			 	die( json_encode( array( 'error' => $error ) ) );
			}
			//Build the undo URL
			do_action('wp_ajax_comments_remove_content_filter');
			$content = stripslashes(apply_filters('comment_text',apply_filters('get_comment_text',AECUtility::encode($comment['comment_content']))));
			$undo = AECUtility::build_undo_url("undodelink", $commentID, $postID, __("Successfully De-linked",'ajaxEdit'));
			//Send the response
			$response = array( 'content' => $content,
							'comment_author_url' => stripslashes(apply_filters('comment_url', apply_filters('get_comment_author_url', $comment['comment_author_url']))),
							'undo' => $undo,
							'comment_links' => AECCore::build_admin_links($commentID, $postID)
			);
			die( json_encode( $response ) );
		} //end action_delinkcomment
		public static function action_requestdeletion() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			check_ajax_referer("requestdeletion_{$commentID}");
			$message = AECAjax::request_deletion($commentID, $postID, trim(strip_tags(urldecode($_POST['message']))));
			if ( $message == 1 ) {
				die( json_encode( array( 'cid' => $commentID ) ) );
			} else {
				die( json_encode( array( 'error' => $aecomments->get_error( $message ) ) ) );
			}
		}	//end function action_requestdeletion
		public static function action_undo() {
			global $aecomments;
			$postID = AECAjax::get_post_id();
			$commentID = AECAjax::get_comment_id();
			//todo nonce

			$comment = $aecomments->get_admin_option( 'undo' );
			$response = AECAjax::save_comment($commentID, $postID, $comment);
			$response = apply_filters('wp_ajax_comments_save_comment', $response, $comment, $_POST);
			die( json_encode( $response ) );
			exit;
		} //end action_undo
		
		
		/*** END AJAX ACTIONS **/
		/*******************************/
		
		
		public static function maybe_change_comment_status( $commentID, $postID ) {
			global $aecomments;
			if (isset($_POST['comment_status'])) {
				$oldComment = get_comment($commentID,ARRAY_A); //Used in the undo portion
				$aecomments->save_admin_option( 'undo', $oldComment );
				switch($_POST['comment_status']) {
					case "1":
						if ($_POST['comment_status'] != $oldComment['comment_approved']) {
							AECAjax::approve_comment($commentID, $postID);
						}
						break;
					case "0":
						AECAjax::moderate_comment($commentID, $postID);
						break;
					case "spam":
						AECAjax::spam_comment($commentID, $postID);
						break;
					case "trash":
						if ($_POST['comment_status'] != $oldComment['comment_approved']) {
							AECAjax::delete_comment($commentID, $postID);
						}
						break;
				}
			}
		} //end comment_status
		public static function add_action( $action = '', $noprivs = false ) {
			if ( !empty( $action ) ) {
				add_action( "wp_ajax_{$action}", array( "AECAjax", 'action_' . $action ) );
				if ( $noprivs ) {
					add_action( "wp_ajax_nopriv_{$action}", array( "AECAjax", 'action_' . $action ) );
				}
			}
		} //end add_action
		public static function initialize_actions() {
			AECAjax::add_action( "getthetimeleft", true );
			AECAjax::add_action( "editcomment", true );
			AECAjax::add_action( "movecomment", false );
			AECAjax::add_action( "savecomment", true );
			AECAjax::add_action( "blacklist", false );
			AECAjax::add_action( "email", false );
			AECAjax::add_action( "deletecomment", false );
			AECAjax::add_action( "deleteperm", false );
			AECAjax::add_action( "requestdeletecomment", true );
			AECAjax::add_action( "spamcomment", false );
			AECAjax::add_action( "approvecomment", false );
			AECAjax::add_action( "unapprovecomment", false );
			AECAjax::add_action( "delinkcomment", false );
			AECAjax::add_action( "requestdeletion", true );
			AECAjax::add_action( "undo", false );
			AECAjax::add_action( "restore", false );
		} //end initialize_actions
		public static function get_post_id() {
			return isset($_POST['pid'])? (int) $_POST['pid'] : 0;
		} //end get_post_id
		public static function get_comment_id() {
			return isset($_POST['cid'])? (int) $_POST['cid'] : 0;
		} //end get_comment_id
		/* approve_comment - Marks a comment as approved 
		Parameters - $commentID, $postID
		Returns - 1 if successful, a string error message if not */
		//public static class.core
		public static function approve_comment($commentID=0, $postID = 0) {
			if (AECCore::is_comment_owner($postID)) {
				$status = wp_set_comment_status($commentID, 'approve')? "1" : 'approve_failed';
				return $status;
			} else {
				return 'approve_failed_permission';
			}
		}
		
		/* delete_comment */
		//public static class.ajax
		public static function delete_comment($commentID = 0, $postID = 0) {
			if ( AECCore::is_comment_owner($postID)) {
				$status = wp_delete_comment($commentID)? "1" : 'delete_failed';
				return $status;
			} else {
				return 'delete_failed_permission';
			}
		} //end delete_comment
		
		/*Delinks a comment */
		//public static class.ajax
		public static function delink_comment($commentID = 0, $postID = 0, $comment = '') {
			if ( AECCore::is_comment_owner($postID)) {
				wp_update_comment($comment);
				return "1";
			} else {
				return 'delink_failed_permission';
			}
		} //end delink_comment
		
		/* get_comment - Returns a comment ready for editing
		parameters - $commentID
		returns - a json encoded string */
		//public static class.ajax
		public static function get_comment($commentID = 0) {
			global $aecomments;
			$comment = get_comment($commentID);
			
			$response = array();
			if (!$comment) { 
				$response[ 'error' ] =  $aecomments->get_error('get_comment_failed');
				$response = json_encode( $response );
				die( $response );
			}
			//Check to see if the comment is spam if the user isn't admin or comment owner
			if (!AECCore::is_comment_owner($comment->comment_post_ID)) {
				if ($comment->comment_approved === 'spam') { 
					$response[ 'error' ] =  $aecomments->get_error('comment_spam');
					$response = json_encode( $response );
					die( $response );
				}
			}
			
			//Check to see if user can edit and return any appropriate error messages
			$message = AECCore::can_edit($commentID, $comment->comment_post_ID);
			if (is_string($message)) {
				$response[ 'error' ] =  $aecomments->get_error($message);
				$response = json_encode( $response );
				die( $response );
			}
			
			//Okay, user can edit - Let's prepare the comment for editing
			$comment->comment_content = format_to_edit( $comment->comment_content ,1);
			$comment->comment_content = apply_filters( 'comment_edit_pre', $comment->comment_content);
			$comment->comment_author = format_to_edit( $comment->comment_author );
			$comment->comment_author_email = format_to_edit( $comment->comment_author_email );
			$comment->comment_author_url = esc_url($comment->comment_author_url);
			$comment->comment_author_url = format_to_edit( $comment->comment_author_url );
			//Prepare the response
			$response[ 'comment_content' ] = $comment->comment_content;
			$response[ 'comment_author' ] = $comment->comment_author;
			$response[ 'comment_author_email' ] = $comment->comment_author_email;
			$response[ 'comment_author_url' ] = $comment->comment_author_url;
			$response = apply_filters('wp_ajax_comments_get_comment', $response, $comment);
			$response = json_encode( $response );
			die( $response );
		}//End function get_comment
		
		
		/* get_time_left - Returns time remaining in seconds
		parameters - $commentID 
		Returns 1 if no time is necessary.  -1 if time is unavailable.  Time if available.
		*/
		//public static class.ajax
		public static function get_time_left($commentID = 0) {
			global $wpdb, $aecomments;
			$adminMinutes = (int)$aecomments->get_admin_option( 'minutes' );
			$query = $wpdb->prepare( "SELECT ($adminMinutes * 60 - (UNIX_TIMESTAMP('" . current_time('mysql') . "') - UNIX_TIMESTAMP(comment_date))) time, comment_author_email, user_id FROM $wpdb->comments where comment_ID = %d", $commentID );
			
			//Get the Timestamp
			$comment = $wpdb->get_row($query, ARRAY_A);
			if (!$comment) { 
				die( json_encode( array( 'error' => '-1' ) ) );
			}
			if (AECCore::can_indefinitely_edit($comment['user_id'])) {	
				die( json_encode( array( 'success' => '1' ) ) );	
			}
			//Get the time elapsed since making the comment
			if ((int)$comment['time'] <= 0) { die( json_encode( array( 'error' => '-1' ) ) ); }
			$timeleft = (int)$comment['time'];
			$minutes = floor($timeleft/60);
			$seconds = $timeleft - ($minutes*60);
			$response = array(
				'minutes' => $minutes,
				'cid' => $commentID, 
				'seconds' => $seconds
			);
			die( json_encode( $response ) );
		}//end function get_time_left
		
		
		/* get_posts - Returns five posts with an offset
		parameters - $offset 
		Returns five posts with an offset
		*/
		//public static class.ajax
		public static function get_posts($offset = 0, $post_id = 0 ) {
			global $wpdb;
			$response_arr =  array();
			$post_type = get_post_type( $post_id );
			$results = $wpdb->get_results( $wpdb->prepare( "select ID, post_title from $wpdb->posts where post_type = %s and post_status = 'publish' order by ID desc limit %d,6", $post_type, $offset ), ARRAY_A);
			foreach ($results as $r) {
				$response_arr['posts'][] = array(
					'post_id' => $r['ID'],
					'post_title' => $r['post_title']
				);
			}
			return $response_arr;
		} //end get_posts
		/* get_posts_by_title - Returns five posts with an offset
		parameters - $title 
		Returns five posts by title
		*/
		//public static class.ajax
		public static function get_posts_by_title( $title, $post_id ) {
			global $wpdb;
			$title = '%' . $title . '%';
			$post_type = get_post_type( $post_id );
			$response_arr =  array();
			$results = $wpdb->get_results( $wpdb->prepare( "select ID, post_title from $wpdb->posts where post_type = %s and post_status = 'publish' and post_title like '%s' limit 6", $post_type, $title ), ARRAY_A);
			foreach ($results as $r) {
				$response_arr['posts'][] = array(
					'post_id' => $r['ID'],
					'post_title' => $r['post_title']
				);
			}
			return $response_arr;
		} //end get_posts_by_title
		
		/* get_posts_by_id - Returns five posts with an offset
		parameters - $id 
		Returns five posts by id
		*/
		//public static class.ajax
		public static function get_posts_by_id($id) {
			global $wpdb;
			$post_type = get_post_type( $id );
			$results = $wpdb->get_row( $wpdb->prepare( "select * from $wpdb->posts where post_type = %s and post_status = 'publish' and ID = %d", $post_type, $id ), ARRAY_A);
			if ($results) {
				$response_arr['posts'] = array(
					'post_id' => $results['ID'],
					'post_title' => $results['post_title']
				);
				return $response_arr;
			}
			return array();
		} //end get_posts_by_id
		
		/* moderate_comment - Marks a comment as unapproved 
		Parameters - $commentID, $postID
		Returns - 1 if successful, a string error message if not */
		//public static class.ajax
		public static function moderate_comment($commentID=0, $postID = 0) {
			if (AECCore::is_comment_owner($postID)) {
				$status = wp_set_comment_status($commentID, 'hold')? "1" : 'moderate_failed';
				return $status;
			} else {
				return 'moderate_failed_permission';
			}
		}
		/* move_comment - Moves a comment from an old post to a new post
		Parameters - $commentID, $oldPostID, $newPostID
		Returns nothing */
		//public static class.ajax
		public static function move_comment($commentID=0, $oldPostID=0, $newPostID=0) {
			global $wpdb, $post;
			
			//Return if not comment owner and if posts are the same
			if ($oldPostID == $newPostID || !AECCore::is_comment_owner($newPostID)) { 
				return array( 'nochange' => true );
			}
			
			//Update the comment with the new post number
			$wpdb->update($wpdb->comments, array('comment_post_ID' => intval($newPostID)), array('comment_ID' => intval($commentID)));
			
			//Update the posts' comment count
			@wp_update_comment_count_now(intval($newPostID));
			@wp_update_comment_count_now(intval($oldPostID));
			
			$posts = get_posts("include=$oldPostID,$newPostID");
			$response_arr = array();
			foreach ($posts as $post) {
				if ($post->ID == $oldPostID) {
					$response_arr['old_post'] = array(
							'new_id' => $newPostID,
							'old_id' => $oldPostID,
							'title' => esc_js( get_the_title( $oldPostID ) ),
							'permalink' => apply_filters('the_permalink', get_permalink( $oldPostID )),
							'comments' => intval( $post->comment_count )
					);
				} else {
					$response_arr['new_post'] = array(
							'new_id' => $newPostID,
							'old_id' => $oldPostID,
							'title' => esc_js( get_the_title( $newPostID ) ),
							'permalink' => apply_filters('the_permalink', get_permalink( $newPostID )),
							'comments' => intval( $post->comment_count )
					);
				} //end if
			} //end foreeach
			
			return $response_arr;
		} //end move_comment
		
		/* request_Deletion - Sends a request to admin for comment removal
		Parameters - $commentID, $postID
		Returns error or response */
		//public static class.ajax
		public static function request_deletion($commentID = 0, $postID = 0, $message) {
			$canedit = AECCore::can_edit($commentID, $postID);
			if (is_string($canedit)) {
				return 'request_deletion_failed';
			}
			if (wp_set_comment_status($commentID, 'hold') || wp_get_comment_status($commentID) == "unapproved") {
				AECAjax::request_deletion_message($commentID, $message);
				
				//Get the comment and remove the cookie
				$comment = get_comment($commentID);
				$hash = md5($comment->comment_author_IP . $comment->comment_date_gmt);
				$GLOBALS['WPAjaxEditCommentsComment' . $commentID . $hash] = ''; //for CFORMS compatibility
				setcookie('WPAjaxEditCommentsComment' . $commentID . $hash, '', time() - 3600, COOKIEPATH,COOKIE_DOMAIN); //removes the cookie
				return 1;
			} else {
				return 'request_deletion_failed';
			}
		} //end request_deletion
		
		//Sends out a request deletion email
		public static function request_deletion_message($commentID = 0, $message) {
			$comment = get_comment($commentID);
			$post    = get_post($comment->comment_post_ID);
			$notify_message  = sprintf( __('A commenter requests deletion of a comment on your post #%1$s "%2$s".  The comment has been moved to the moderation queue.', 'ajaxEdit'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
			$notify_message  .= sprintf( __('The reason given is: ' . $message, 'ajaxEdit'), $comment->comment_post_ID, $post->post_title ) . "\r\n\r\n";
			$notify_message .= sprintf( __('E-mail : %s', 'ajaxEdit'), $comment->comment_author_email ) . "\r\n";
			$notify_message .= sprintf( __('URL    : %s', 'ajaxEdit'), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s', 'ajaxEdit'), $comment->comment_author_IP ) . "\r\n";
			$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
			$notify_message .= sprintf( __('Delete it: %s', 'ajaxEdit'), admin_url("comment.php?action=cdc&c=$commentID") ) . "\r\n";
			$notify_message .= sprintf( __('Approve it: %s', 'ajaxEdit'),  admin_url("comment.php?action=mac&c=$commentID") ) . "\r\n";
			$notify_message .= sprintf( __('Spam it: %s', 'ajaxEdit'), admin_url("comment.php?action=cdc&dt=spam&c=$commentID") ) . "\r\n";
			$email = get_option('admin_email');
			$blogname = get_option('blogname');
			$from = "From: \"$blogname\" <$email>";
			$reply_to = "Reply-To: $email";
			$message_headers = "$from\n"
				. "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
			$message_headers .= $reply_to . "\n";
			$subject = sprintf( __('Request for Comment Deletion: "%2$s"','ajaxEdit'), $blogname, $post->post_title );
			@wp_mail($email, $subject, $notify_message, $message_headers);
		}
		/* save_comment - Saves a new comment
		Parameters - $commentID, $postID, $commentarr (comment array)
		Returns errors or response*/
		//public static class.ajax
		public static function save_comment($commentID, $postID, $commentarr) {
			global $wpdb, $aecomments;
			//Save the old comment and build an undo spot
			$undoComment = $commentarr;

			//Make sure the comment has something in it
			$response = array();
			if ('' == $commentarr['comment_content'] || $commentarr['comment_content'] == "undefined") {
				$response['error'] = $aecomments->get_error('content_empty');
				return $response;
			}
			//Check to see if user can edit
			$message = AECCore::can_edit($commentID, $postID);
			if (is_string($message)) {
				$response['error'] = $aecomments->get_error( $message );
				return $response;
			}
			
			
			//Sanity checks
			if (!AECCore::is_comment_owner($postID)) {
				//Make sure required fields are filled out
				if ( get_option('require_name_email') && ((6 > strlen($commentarr['comment_author_email']) && AECCore::can_edit_email($commentID, $postID)) || ('' == $commentarr['comment_author'] && AECCore::can_edit_name($commentID, $postID)))) {
					$response['error'] = $aecomments->get_error( 'required_fields' );
					return $response;
				}
			}// end comment_owner check
			//Make sure the e-mail is valid - Skip if pingback or trackback
			if (!($aecomments->admin  && empty($commentarr['comment_author_email']))) {
				if (!is_email($commentarr['comment_author_email']) && $commentarr['comment_type'] != "pingback" && $commentarr['comment_type'] != "trackback") {
					if (!get_option('require_name_email') && empty($commentarr['comment_author_email'])) {
					
					} else {
						if ( AECCore::can_edit_email($commentID, $postID)) {
							$response['error'] = $aecomments->get_error( 'invalid_email' );
							return $response;
						}
					}
				}
			}
			if (strtolower(get_option('blog_charset')) != 'utf-8') { @$wpdb->query("SET names 'utf8'");} //comment out if getting char errors
			
			//Save the comment
			$commentarr['comment_ID'] = (int)$commentID;
			$commentapproved = $commentarr['comment_approved'];
			//Condition the data for returning
			do_action('wp_ajax_comments_remove_content_filter');
			
			//Do some comment checks before updating
			if (!AECCore::is_comment_owner($postID)) {
				//Preserve moderation/spam setting.  Only check approved comments
				if ($commentarr['comment_approved'] == 1) {
					// Everyone else's comments will be checked.
					if ( check_comment($commentarr['comment_author'], $commentarr['comment_author_email'], $commentarr['comment_author_url'], $commentarr['comment_content'], $commentarr['comment_author_IP'], $commentarr['comment_agent'], $commentarr['comment_type'])) 
						$commentarr['comment_approved'] = 1;
					else
						$commentarr['comment_approved'] = 0;
				}
					
				if ( wp_blacklist_check($commentarr['comment_author'], $commentarr['comment_author_email'], $commentarr['comment_author_url'], $commentarr['comment_content'], $commentarr['comment_author_IP'], $commentarr['comment_agent']) )
					$commentarr['comment_approved'] = 'spam';
			}
			
			
			//Update the comment
			wp_update_comment($commentarr);
			

			//If spammed, return error
			if (!$aecomments->admin && $commentarr['comment_approved'] === 'spam') {
				$response['error'] = $aecomments->get_error( 'comment_marked_spam' );
				return $response;
			}
			
			//If moderated, return error
			if ($commentarr['comment_approved'] == 0 && $commentapproved != 0) {
				$response['error'] = $aecomments->get_error( 'comment_marked_moderated' );
				return $response;
			}
			
			
			//Check for spam
			if (!AECCore::is_comment_owner($postID)) {
				if(AECCore::check_spam($commentID, $postID)) {
					$response['error'] = $aecomments->get_error( 'comment_marked_spam' );
					return $response;
				};
			}
			//Do actions after a comment has successfully been edited
			do_action_ref_array('wp_ajax_comments_comment_edited', array(&$commentID, &$postID));
			
			//Get undo data
			if ($aecomments->admin) {
				$oldComment = $aecomments->get_admin_option( 'undo' );
				$undo = AECUtility::build_undo_url("undoedit", $commentID, $postID, __('Comment successfully saved','ajaxEdit')); 				
			} else {
				$undo = '';
			}
			$approve_count = get_comment_count($postID);
			$comment_count = get_comment_count();
			
			//For security, get the new comment
			if ( isset( $GLOBALS['comment'] ) ) unset( $GLOBALS['comment'] );
			global $comment;
			$comment = get_comment( $commentID );
			//Condition the data for returning
			do_action('wp_ajax_comments_remove_content_filter');
			$response = array(
						'content' => apply_filters('comment_text',apply_filters('get_comment_text',AECUtility::encode($comment->comment_content))),
						'comment_author' => stripslashes(apply_filters('comment_author', apply_filters('get_comment_author', AECUtility::encode($comment->comment_author)))),
						'comment_author_url' => stripslashes(apply_filters('comment_url', apply_filters('get_comment_author_url', $comment->comment_author_url))),
						'comment_date' =>  get_comment_date('F jS, Y'),
						'comment_time' => get_comment_time(),
						'comment_approved' => $comment->comment_approved,
						'old_comment_approved' => isset( $oldComment ) ? $oldComment['comment_approved'] : false,
						'undo_comment_approved' => isset( $undoComment ) ? $undoComment['comment_approved'] : false,
						'approve_count' => $approve_count['approved'],
						'moderation_count' => $comment_count['awaiting_moderation'],
						'spam_count' => $comment_count['spam'],
						'comment_links' => AECCore::build_admin_links($commentID, $postID),
						'undo' => $undo
			);
			return $response;		
		} //save_comment
		
		
		/* spam_comment - Marks a comment as spam or de-spams a comment
		Parameters - $commentID, $postID
		Returns - 1 if successful, a string error message if not */
		//public static class.ajax
		public static function spam_comment($commentID = 0, $postID = 0) {
			if (AECCore::is_comment_owner($postID)) {
				$status = wp_set_comment_status($commentID, 'spam')? "1" : 'comment_spam_failed';
				return $status;
			} else {
				return 'comment_spam_failed_permission';
			}
		} //end spam_comment
		
}

?>