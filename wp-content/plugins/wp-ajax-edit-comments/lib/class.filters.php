<?php
//A class that contains all the filter callbacks
class AECFilters {
		public static function add_author_spans($content) {
			global $comment, $aecomments;
			if ($aecomments->skip) { $aecomments->skip = false; return $content; }
			if (!is_object($comment)) { return $content; }
			if (AECCore::can_edit_quickcheck($comment) != 1) { return $content; } //--ag
			if (AECCore::can_edit($comment->comment_ID, $comment->comment_post_ID) != 1) { return $content; }
			$content = "<span id='edit-author" . "$comment->comment_ID'>$content</span>";
			return $content;
		} //end add_author_spans
		/* add_date_spans - Adds spans to date links */
		//public static class.core
		public static function add_date_spans($content) {
			global $comment, $aecomments;
			if ($aecomments->skip) { $aecomments->skip = false; return $content; }
			if (!is_object($comment)) { return $content; }
			if (AECCore::can_edit_quickcheck($comment) != 1) { return $content; } //--ag
			if (AECCore::can_edit($comment->comment_ID, $comment->comment_post_ID) != 1) { return $content; }
			$content = "<span id='aecdate$comment->comment_ID'>$content</span>";
			return $content;
		} //end add_date_spans
		/* add_edit_links - Adds edit links to post and admin panels */
		//public static class.core
		public static function add_edit_links($content) {
			global $comment, $aecomments;
			if ($aecomments->skip) { $aecomments->skip = false; return $content; }
			if (empty($comment)) { return $content; }
			
			if (is_page() && $aecomments->get_admin_option( 'show_pages' ) != 'true') {
				return $content;
			}
			if (AECCore::can_edit_quickcheck($comment) != 1) { return $content; } //--ag
			if (AECCore::can_edit($comment->comment_ID, $comment->comment_post_ID) != 1) { return $content; }
			
			if ($aecomments->get_admin_option( 'comment_display_top' ) == 'true') {
				$aec_top = true;
			}
			
			$tempContent = $content; //temporary variable to store content
			$edit_admin = "edit-comment-admin-links";
			$clearfix=$timer_class='';
			if ($aecomments->get_admin_option( 'icon_display' ) != 'classic' && $aecomments->get_admin_option( 'icon_display' ) != 'dropdown') {
					$edit_admin = "edit-comment-admin-links-no-icon";	
					$timer_class = "ajax-edit-time-left-no-icon";
			}
			
			/*If you're wondering why the JS is inline, it's because people with 500+ comments were having their browsers lock up.  With inline, the JS is run as needed.  Not elegant, but the best solution.*/
			if (!isset($aec_top)) { //Test to see if user wants interface on top or bottom
				$content = '<div class="edit-comment" id="edit-comment' . $comment->comment_ID . '" style="background: none">' . $content .  '</div>';
				$content .= "<div id='comment-undo-$comment->comment_ID' class='aec-undo' style='background: none'></div>";
			} else {
				$content = '';
			}
			
			if (!AECCore::is_comment_owner($comment->comment_post_ID)) {
				//For anonymous users
				$content .= "<div class='$edit_admin $clearfix' id='edit-comment-user-link-$comment->comment_ID' style='background:none'>";
				
				$content .= AECCore::build_admin_links($comment->comment_ID, $comment->comment_post_ID);
				$content .= "</div>";
				//Show custom content to users
				if (AECCore::show_affiliate_link()) {
					$message = do_shortcode(stripslashes($aecomments->get_admin_option( 'affiliate_text' )));
					$message = str_replace("[url]", "<a href='http://www.ajaxeditcomments.com/?affiliate_id=" . $aecomments->get_admin_option( 'affiliate_id' ) . "'>",$message);
					$message = str_replace("[/url]", "</a>", $message);
					$content .= "<div class='aec-custom-text'>$message</div><!--/aec-custom-text-->";
				}
				//End for anonymous users
			} else {
				//Check if user is editor
				$role = AECUtility::get_user_role();
				
				//todo change editor to capability
				if ($role == 'editor' && $aecomments->get_admin_option( 'allow_editing_editors' ) == 'false')
					return $content;
				
				
				if (is_admin() && $aecomments->get_admin_option( 'admin_editing' ) == "false") { 
					//We're in the admin panel
					
					$content .= '<div class="' .$edit_admin. ' ' . $clearfix.'" id="edit-comment-admin-links' . $comment->comment_ID . '">';
					$content .= AECCore::build_admin_links($comment->comment_ID, $comment->comment_post_ID);
					$content .= "</div>";
					//End in the admin panel
					
				} elseif ($aecomments->get_user_option( 'comment_editing' ) == "true") { 
					
					//We're in a post
					$content .= '<div class="' . $edit_admin . ' ' . $clearfix . '" id="edit-comment-admin-links' . $comment->comment_ID . '" style="background: none">';
					$content .= AECCore::build_admin_links($comment->comment_ID, $comment->comment_post_ID);
					$content .= "</div>";
				}
				
			}
			if (isset($aec_top)) { //Test to see if user wants interface on top or bottom
				$content .= "<div id='comment-undo-$comment->comment_ID' class='aec-undo' style='background: none'></div>";
				$content .= '<div class="edit-comment" id="edit-comment' . $comment->comment_ID . '" style="background: none">' . $tempContent .  '</div>';
			}
			return $content;
			
		} //end function add_edit_links
		
		/* add_date_spans - Adds spans to date links */
		//public static class.core
		public static function add_time_spans($content) {
			global $comment;
			if (!is_object($comment)) { return $content; }
			if ( AECCore::can_edit_quickcheck($comment) != 1) { return $content; } //--ag
			if ( AECCore::can_edit($comment->comment_ID, $comment->comment_post_ID) != 1) { return $content; }
			$content = "<span id='aectime$comment->comment_ID'>$content</span>";
			return $content;
		} //end add_time_spans
		//Add a settings link to the plugin's page
		//Called from plugin_action_links
		public static function add_settings_link($links) {
			global $aecomments;
			$multisite_network = AECCore::is_multisite();

			$admin_uri = add_query_arg( array( 'page' => 'wpaec' ), admin_url( sprintf( '%sadmin.php', $multisite_network ? 'network/' : '' ) ) );
			array_push($links, sprintf('<a href="%s">%s</a>', $admin_uri, __("Settings", 'ajaxEdit')));
			return $links;
		} //end add_settings_link
		/* filter_trackbacks - Strips trackbacks from the comments loop
		Parameters - $comms - The passed comments
		Returns the comments array without trackbacks
		*/
		//Called from comments_array filter
		public static function filter_trackbacks($comms) {
			global $comments;
			$comments = array_filter($comms,array("AECUtility", 'filter_strip_trackbacks'));
			return $comments;
		} //end filter_trackbacks
		
		/* filter_comments_number - Updates the comment number with no trackbacks
		Parameters - $count - The current comment count for a post
		Returns the updated comment count */
		//Called from get_comments_number filter
		public static function filter_comments_number($count) {
			global $id;
			if (empty($id)) { return $count; }
			$comments = get_approved_comments((int)$id);
			$comments = array_filter($comments, array("AECUtility", 'filter_strip_trackbacks'));
			return sizeof($comments);
		} //end filter_comments_number
		
		/* remove_nofollow - Removes no-follow from comment links
		Parameters - $text - The comment text
		Returns altered text
		Credits:  http://www.allpassionmarketing.com*/
		//Called from get_comment_author_link, comment_text, thesis_comment_text filters
		public static function remove_nofollow($text = '') {
			$text = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $text);
			$text = preg_replace("/(<a[^>]*[^\s])(\s*rel=\"\s*\")/i", "$1", $text);
			return $text;
		} //end remove_nofollow
		
		
}

?>