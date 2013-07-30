<?php
//A class that contains all the action callbacks
class AECActions {
		/* - comment_edited - Run after a comment has successfully been edited 
		Parameters - $commentID, $postID*/
		//Called from wp_ajax_comments_comment_edited action
		public static function comment_edited($commentID = 0, $postID = 0) {
			//Clear the comment cache
			if (function_exists('clean_comment_cache')) { clean_comment_cache($commentID); }
			
			//For WP Cache and WP Super Cache
			if (function_exists('wp_cache_post_change')) {
				@wp_cache_post_change($postID);
			}
			//Get out if user is admin or post owner
			if (AECCore::is_comment_owner($postID)) { return; }
			
			//Increment the number of edited comments
			AECCore::increment_edit_count();
		} //end function comment_edited
		
		//Removes the div and various links from a comment 
		//called from wp_ajax_comments_remove_content_filter action
		function comment_filter() {
			global $aecomments;
			$aecomments->skip = true;
		}
		
		/* comment_posted - This function is run whenever a comment is posted - 
		Adds a cookie and security key for future editing
		Parameters - $commentID (the comment's ID)
		*/
		//called from comment_post action
		public static function comment_posted($commentID=0) {
			global $wpdb, $aecomments;
			//Get comment
			$comment = get_comment($commentID, ARRAY_A);
			//Some sanity checks
			if (!$comment) { return;}
			
			//if ($comment['comment_approved'] == "1") { return; }	
			if ($comment['comment_approved'] == "spam") { return; }	
			//If admin, exit since we don't want to add anything
			if (AECCore::is_comment_owner($comment['comment_post_ID'])) {
				return $commentID;
			}
			//Check to see if the user is logged in and can indefinitely edit
			if ($comment['user_id'] != 0) {
				if ($aecomments->get_admin_option( 'allow_registeredediting' ) == 'false')
					return 'no_user_editing';
			} else {
				//Check to see if admin allows comment editing for anonymous users
				if ($aecomments->get_admin_option( 'allow_editing' ) == "false") 
					return 'no_user_editing';
			}
			//Don't save data if user can indefinitely edit
			if (AECCore::can_indefinitely_edit($comment['user_id'])) { return; }
			
			
			//Get hash and random security key
			$hash = md5($comment['comment_author_IP'] . $comment['comment_date_gmt']);
			$rand = 'wpAjax' . $hash . md5(AECUtility::random()) . md5(AECUtility::random());
			
			//Get the minutes allowed to edit
			$minutes = $aecomments->get_admin_option( 'minutes' );
			if (!is_numeric($minutes)) {
				$minutes = $aecomments->get_minutes();
			}
			if ($minutes < 1) {
				$minutes = $aecomments->get_minutes();
			}		
			//Insert the random key into the database
			//todo - update to update_post_meta or use comment meta instead
			$query = "INSERT INTO " . $wpdb->postmeta .
				"(post_id, meta_key, meta_value) " .
				"VALUES (%d,'_%d', %s)";
			@$wpdb->query( $wpdb->prepare( $query, $comment['comment_post_ID'], $comment['comment_ID'], $rand ) );
			
			//Set the cookie
			$cookieName = 'WPAjaxEditCommentsComment' . $commentID . $hash;
			$value = $rand;
			$expire = time()+60*$minutes;
			if (!isset($_COOKIE[$cookieName])) {
				setcookie($cookieName, $value, $expire, COOKIEPATH,COOKIE_DOMAIN);
				//setcookie($cookieName, $value, $expire, SITECOOKIEPATH,COOKIE_DOMAIN);
				$GLOBALS[$cookieName] = $value; //For compatability with CFORMS
			}
			
			//Read in security key count, delete keys if over 100
			$securityCount = get_site_option('ajax-edit-comments_security_key_count');
			if ( !$securityCount ) $securityCount = get_option('ajax-edit-comments_security_key_count'); //for upgrade/multi-site support
			if (!$securityCount) {
				$securityCount = 1;  update_site_option('ajax-edit-comments_security_key_count', $securityCount);
			} else {
				$securityCount = (int)$securityCount;
			}
			//Delete keys if over a 100
			if ($securityCount >= 100) {
				$metakey = "_" . $comment['comment_ID'];
				@$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where left(meta_value, 6) = 'wpAjax' and meta_key <> '%s'", $metakey ) );
				$securityCount = 0;
			}
			$securityCount += 1;
			update_site_option('ajax-edit-comments_security_key_count', $securityCount);
			return $commentID;
		}//End function comment_posted
		
		/* disable_selfpings - Disables WordPress sites from pinging themselves
		Parameters - $links 
		Credits - http://blogwaffe.com/ */
		//Called from pre_ping action
		public static function disable_selfpings(&$links) {
			$home = get_option( 'home' );
			foreach ( $links as $l => $link ) {
				if ( 0 === strpos( $link, $home ) )
					unset($links[$l]);
			}
		}
		
		
		
		//When a comment is edited, an e-mail notification is sent out
		//Parameters - $commentID (a comment ID) and $postID (a post ID)
		//Returns false if e-mail failed
		//Called from wp_ajax_comments_comment_edited action
		public static function edit_notification($commentID = 0, $postID = 0) {
			global $wpdb, $aecomments;
			//Check admin options and also if user editing is post author
			if ($aecomments->get_admin_option( 'email_edits' ) == "false") { return false; }
			//Get the comment and post
			$comment = get_comment($commentID, ARRAY_A);
			if (empty($comment)) { return false; }
			$query = "SELECT * FROM $wpdb->posts WHERE ID=$postID";
			$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $postID ), ARRAY_A);
			
			if (!$post) { return false; }
			if (AECCore::is_comment_owner($postID)) {  return false; }
			//Make sure the comment is approved and not a trackback/pingback
			if ( $comment['comment_approved'] == '1' && ($comment['comment_type'] != 'pingback' || $comment['comment_type'] != 'trackback')) { 
			//Put together the e-mail message
			$message  = sprintf(__("A comment has been edited on post %s", 'ajaxEdit') . ": \n%s\n\n", stripslashes($post['post_title']), get_permalink($comment['comment_post_ID']));
			$message .= sprintf(__("Author: %s\n", 'ajaxEdit'), $comment['comment_author']);
			$message .= sprintf(__("Author URL: %s\n", 'ajaxEdit'), stripslashes($comment['comment_author_url']));
			$message .= sprintf(__("Author E-mail: %s\n", 'ajaxEdit'), stripslashes($comment['comment_author_email']));
			$message .= __("Comment:\n", 'ajaxEdit') . stripslashes($comment['comment_content']) . "\n\n";
			$message .= __("See all comments on this post here:\n", 'ajaxEdit');
			$message .= get_permalink($comment['comment_post_ID']) . "#comments\n\n";
			$subject = sprintf(__('New Edited Comment On: %s', 'ajaxEdit'), stripslashes($post['post_title']));
			$subject = '[' . get_bloginfo('name') . '] ' . $subject;
			$email = get_bloginfo('admin_email');
			$site_name = str_replace('"', "'", get_bloginfo('name'));
			$charset = get_option('blog_charset');
			$headers  = "From: \"{$site_name}\" <{$email}>\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
			//Send the e-mail
			return wp_mail($email, $subject, $message, $headers);
			}
			return false;
		} //End function edit_notification
		
}

?>
