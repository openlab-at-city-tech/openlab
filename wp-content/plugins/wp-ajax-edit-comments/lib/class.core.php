<?php
class AECCore {
		/* add_author_spans - Adds spans to author links */
		//public static class.core
		
		
		/*add_query_triggers - Adds multiple triggers for the various overlays and Ajax processor*/
		//public static class.core
		public static function add_query_triggers($queries) {
			array_push($queries, 'aec_page');
			return $queries;
		} //end add_query_triggers
		
		//public static class.core
		public static function build_admin_links($commentID, $postID, $content = '') {
			global $aecomments;
			
			$comment = get_comment( $commentID );
			$ajax_url = admin_url( 'admin-ajax.php' ) . '?';
			$plugin_url = $aecomments->get_plugin_url( '/views/' );
			if (!AECCore::is_comment_owner($postID)) {
				$edit_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=comment-editor.php&action=editcomment&cid=$commentID&pid=$postID", "editcomment_$commentID")."&height=435&width=560");
			} else {
				$edit_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=comment-editor.php&action=editcomment&cid=$commentID&pid=$postID", "editcomment_$commentID")."&height=525&width=620");
			}
			$move_comment_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=move-comment.php&action=movecomment&pid=$postID&cid=$commentID", "movecomment_$commentID")."&height=500&width=560");
			
			$request_deletion_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=request-deletion.php&action=requestdeletion&pid=$postID&cid=$commentID", "requestdeletion_$commentID")."&height=495&width=560");
			$request_delete_url = esc_url( wp_nonce_url( $ajax_url . "action=requestdeletecomment&pid=$postID&cid=$commentID", "requestdeletecomment_$commentID" ) );
			$spam_url = esc_url( wp_nonce_url( $ajax_url . "action=spamcomment&pid=$postID&cid=$commentID", "spamcomment_$commentID" ) );
			$admin_email = get_bloginfo('admin_email');
			$commenter_email = $comment->comment_author_email;
			$email_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=email.php&action=email&admin=$admin_email&commenter=$commenter_email&pid=$postID&cid=$commentID", "email_$commentID")."&height=500&width=600");
			$blacklist_url = esc_url(wp_nonce_url(get_bloginfo('url') . "/?aec_page=blacklist-comment.php&action=blacklist&pid=$postID&cid=$commentID", "blacklist_$commentID")."&height=550&width=600");
			$delete_url = esc_url( wp_nonce_url( $ajax_url . "action=deletecomment&pid=$postID&cid=$commentID", "deletecomment_$commentID" ) );
			$restore_url = esc_url( wp_nonce_url( $ajax_url . "action=restore&pid=$postID&cid=$commentID", "restore_$commentID" ) );
			$deleteperm_url = esc_url( wp_nonce_url( $ajax_url . "action=deleteperm&pid=$postID&cid=$commentID", "deleteperm_$commentID" ) );
			$moderate_url = esc_url( wp_nonce_url( $ajax_url . "action=unapprovecomment&pid=$postID&cid=$commentID", "unapprovecomment_$commentID" ) );
			$approve_url = esc_url( wp_nonce_url( $ajax_url . "action=approvecomment&pid=$postID&cid=$commentID", "approvecomment_$commentID" ) );
			$delink_url = esc_url( wp_nonce_url( $ajax_url . "action=delinkcomment&pid=$postID&cid=$commentID", "delinkcomment_$commentID" ) );
			//Icon Classes
			$spam_class=$moderate_class=$edit_class=$approve_class=$delete_class=$clearfix=$timer_class='';
			$edit_admin = "edit-comment-admin-links";
			if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons') {
					$spam_class = 'spam-comment';
					$moderate_class = 'moderate-comment';
					$edit_class = 'edit-comment';
					$request_deletion_class = "request-deletion-comment";
					$approve_class = 'approve-comment';
					$delete_class = 'delete-comment';
					$delink_class = 'delink-comment';
					$move_class = "move-comment";			
					$blacklist_class = "blacklist-comment";
					$email_class = "email-comment";
			} else {
				$edit_admin = "edit-comment-admin-links-no-icon";
				$timer_class = "ajax-edit-time-left-no-icon";
				$request_deletion_class = "request-deletion-comment";
			}
			if ($aecomments->get_admin_option( 'clear_after' ) == 'true') {
				$clearfix = "clearfix";
			}
			$aec_content_arr = array();
			//For anonymous users
			if (!AECCore::is_comment_owner($postID)) {
				$aeccontent = "<a title='".__('Comment Editor','ajaxEdit')."' class='$edit_class' href='$edit_url' onclick='return jQuery.ajaxeditcomments.edit(this);' id='edit-$commentID'>";
				if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$aeccontent .= "<span class='aec-icons $edit_class'></span>";
				$aeccontent .= "<span class='aec_anon_text'>" .  __("Click to Edit",'ajaxEdit') . "</span>";
				if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$aeccontent .= "<span class='aec-icons $edit_class'></span>";
				$aeccontent .= "</a>";
				array_push($aec_content_arr, $aeccontent);
				//check if deletion option is on
				if ($aecomments->get_admin_option( 'request_deletion_behavior' ) == "request") {
					$aeccontent = "<a title='" . __('Request Deletion', 'ajaxEdit')."' class='$request_deletion_class' href='$request_deletion_url' onclick='return jQuery.ajaxeditcomments.request_deletion(this);' id='request-deletion-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$aeccontent .= "<span class='aec-icons $request_deletion_class'></span>";
					$aeccontent .= "<span class='aec_anon_text'>" .  __("Request Deletion",'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$aeccontent .= "<span class='aec-icons $request_deletion_class'></span>";
					$aeccontent .= "</a>";
					array_push($aec_content_arr, $aeccontent);
				} elseif ($aecomments->get_admin_option( 'request_deletion_behavior' ) == "delete") {
					$aeccontent = "<a title='" . __('Delete', 'ajaxEdit')."' class='$request_deletion_class' href='$request_delete_url' onclick='return jQuery.ajaxeditcomments.request_delete(this);' id='request-delete-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$aeccontent .= "<span class='aec-icons $request_deletion_class'></span>";
					$aeccontent .= "<span class='aec_anon_text'>" .  __("Delete",'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$aeccontent .= "<span class='aec-icons $request_deletion_class'></span>";
					$aeccontent .= "</a>";
					array_push($aec_content_arr, $aeccontent);
				} 
				if ($aecomments->get_admin_option( 'icon_display' ) == 'noicons') 
					$content .= sprintf("[%s]", implode(' | ', $aec_content_arr));
				else 
					$content .= implode('', $aec_content_arr);
				//Check to see if timer is on
				if ($aecomments->get_admin_option( 'show_timer' ) == 'true') {
					//Check to see if user is logged in and admin can indefinitely edit
					if (!AECCore::can_indefinitely_edit($comment->user_id)) {
						$content .= " <span class='ajax-edit-time-left $timer_class' id='ajax-edit-time-left-$commentID'></span>";
					}
				}
				$content .= "<br style='clear: left;' />";
				return $content;
			}
			if ($aecomments->get_user_option( 'comment_editing' ) == "true" || (is_admin() && $aecomments->get_user_option( 'admin_editing' ) == "false")) { 
					//Admin Options
					//Edit link
					$edit = "<a title='".__('Comment Editor','ajaxEdit')."' href='$edit_url' onclick='return jQuery.ajaxeditcomments.edit(this);' id='edit-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$edit .= "<span class='aec-icons $edit_class'></span>";
					$edit .= "<span class='aec_link_text'>" .  __('Edit', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$edit .= "<span class='aec-icons $edit_class'></span>";
					$edit .= "</a>";
					
					//Restore Link
					$restore = "<a title='" . __("Restore", 'ajaxEdit') . "'  href='$restore_url' onclick='jQuery.ajaxeditcomments.restore_comment(this); return false;' id='restore-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$restore .= "<span class='aec-icons $approve_class'></span>";
					$restore .= "<span class='aec_link_text'>" . __('Restore', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$restore .= "<span class='aec-icons $approve_class'></span>";
					$restore .= "</a>";
					
					//Delete Permanently link
					$delete_perm = "<a title='" . __("Delete Permanently", 'ajaxEdit') . "'  href='$deleteperm_url' onclick='jQuery.ajaxeditcomments.deleteperm_comment(this); return false;' id='deleteperm-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$delete_perm .= "<span class='aec-icons $delete_class'></span>";
					$delete_perm .= "<span class='aec_link_text'>" . __('Delete Permanently', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$delete_perm .= "<span class='aec-icons $delete_class'></span>";
					$delete_perm .= "</a>";
					
					//Email link
					if (!empty($comment->comment_author_email)) {
						$email = "<a title='" . __("E-mail", 'ajaxEdit') . "' href='$email_url' onclick='return jQuery.ajaxeditcomments.email(this);' id='email-$commentID'>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
							$email .= "<span class='aec-icons $email_class'></span>";
						$email .= "<span class='aec_link_text'>" . __('E-mail', 'ajaxEdit') . "</span>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
							$email .= "<span class='aec-icons $email_class'></span>";
						$email .= "</a>";
					} else {
						$email = '';
					}
					
					//Move Comments Link
					$move = "<a title='" . __("Move Comments", 'ajaxEdit') . "' href='$move_comment_url' onclick='return jQuery.ajaxeditcomments.move(this);' id='move-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$move .= "<span class='aec-icons $move_class'></span>";
					$move .= "<span class='aec_link_text'>" . __('Move', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$move .= "<span class='aec-icons $move_class'></span>";
					$move .= "</a>";
					
					//Delink link
					if (!empty($comment->comment_author_url) && $comment->comment_author_url != "http://") {
						$delink_link = "<a title='" . __("De-link", 'ajaxEdit') . "'  href='$delink_url' onclick='jQuery.ajaxeditcomments.delink(this); return false;' id='delink-$commentID'>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
							$delink_link .= "<span class='aec-icons $delink_class'></span>";
						$delink_link .= "<span class='aec_link_text'>" . __('De-link', 'ajaxEdit') . "</span>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
							$delink_link .= "<span class='aec-icons $delink_class'></span>";
						$delink_link .= "</a>";
					 } else {
					 	$delink_link = '';
					 }
					//Approve link
					if ($comment->comment_approved == "0" || $comment->comment_approved == "spam") {
						$approve = "<a title='" . __("Approve", 'ajaxEdit') . "'  href='$approve_url' onclick='jQuery.ajaxeditcomments.approve(this); return false;' id='approve-$commentID'>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
							$approve .= "<span class='aec-icons $approve_class'></span>";
						$approve .= "<span class='aec_link_text'>" . __('Approve', 'ajaxEdit') . "</span>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
							$approve .= "<span class='aec-icons $approve_class'></span>";
						$approve .= "</a>";
					} else {
						$approve = '';
					}
					
					//Moderate link
					if ($comment->comment_approved != "0") {
						$moderate = "<a title='" . __("Moderate", 'ajaxEdit') . "'  href='$moderate_url' onclick='jQuery.ajaxeditcomments.moderate(this); return false;' id='moderate-$commentID'>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
							$moderate .= "<span class='aec-icons $moderate_class'></span>";
						$moderate .= "<span class='aec_link_text'>" . __('Moderate', 'ajaxEdit') . "</span>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
							$moderate .= "<span class='aec-icons $moderate_class'></span>";
						$moderate .= "</a>";
					} else {
						$moderate = '';
					}
		
					//Spam link
					if ($comment->comment_approved != "spam") {
						$spam = "<a title='" . __("Spam", 'ajaxEdit') . "'  href='$spam_url' onclick='jQuery.ajaxeditcomments.spam(this); return false;' id='spam-$commentID'>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
							$spam .= "<span class='aec-icons $spam_class'></span>";
						$spam .= "<span class='aec_link_text'>" . __('Spam', 'ajaxEdit') . "</span>";
						if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
							$spam .= "<span class='aec-icons $spam_class'></span>";
						$spam .= "</a>";
					} else {
						$spam = '';
					}

					//Blacklist link
					$blacklist = "<a title='" . __("Blacklist", 'ajaxEdit') . "'  title='".__('Blacklist Comment', 'ajaxEdit')."'  href='$blacklist_url' onclick='jQuery.ajaxeditcomments.blacklist_comment(this); return false;' id='blacklist-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$blacklist .= "<span class='aec-icons $blacklist_class'></span>";
					$blacklist .= "<span class='aec_link_text'>" . __('Blacklist', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$blacklist .= "<span class='aec-icons $blacklist_class'></span>";
					$blacklist .= "</a>";
					
					//Delete link
					$delete = "<a title='" . __("Trash", 'ajaxEdit') . "'  href='$delete_url' onclick='jQuery.ajaxeditcomments.delete_comment(this); return false;' id='delete-$commentID'>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'false')
						$delete .= "<span class='aec-icons $delete_class'></span>";
					$delete .= "<span class='aec_link_text'>" . __('Trash', 'ajaxEdit') . "</span>";
					if ($aecomments->get_admin_option( 'icon_display' ) != 'noicons' && $aecomments->get_admin_option( 'use_rtl' ) == 'true')
						$delete .= "<span class='aec-icons $delete_class'></span>";
					$delete .= "</a>";
					$delete .= "";
					
					//For the trash view
					if ($comment->comment_approved == 'trash') {
						$aec_content_arr = array($edit, $restore, $delete_perm);
						if ($aecomments->get_admin_option( 'icon_display' ) == 'noicons') 
							$content .= sprintf("[%s]", implode(' | ', $aec_content_arr));
						else 
							$content .= implode('', $aec_content_arr);
						return $content;
					}
					if ($aecomments->get_admin_option( 'icon_display' ) == "dropdown") {
						//Enable dropdown menu
						$content .= $edit;
						$content .= "<span class='aec-dropdownarrow' id='aec-dropdownarrow-$commentID'>";
						$content .= "<a class='aec-dropdownlink' href='cid=$commentID' onclick='jQuery.ajaxeditcomments.dropdown(this); return false;' id='aec-dropdownlink-$commentID'>";
						$content .= "<span class='aec-icons'></span>";
						$content .= "<span id='aec-dropdownlink-text-$commentID'>" . __('More Options', 'ajaxEdit') . "</span>";
						$content .= "</a></span>";
						
						//Fill dropdown menu
						$content .= "<span class='aec-dropdown' id='aec-dropdown-$commentID'>"; //start dropdown menu container
						$dropdown = $aecomments->get_admin_option( 'drop_down' );
						
						//Create the array columns
						$columns = array('column0'=>array(),'column1'=>array(), 'column2'=> array());
						foreach ($dropdown as $items => $item) {
							switch ($item['column']) {
								case "0":
									$columns['column0'][sizeof($columns['column0'])] = $item;
									break;
								case "1":
									$columns['column1'][sizeof($columns['column1'])] = $item;
									break;
								case "2":
									$columns['column2'][sizeof($columns['column2'])] = $item;
									break;
							}	
						}
						//Remove empty columns						
						if (sizeof($columns['column0']) == 0) 
							unset($columns['column0']);
						if (sizeof($columns['column1']) == 0) 
							unset($columns['column1']);
						if (sizeof($columns['column2']) == 0) 
							unset($columns['column2']);
						
						//Build the columns for the dropdown
						$cols = array();
						foreach ($columns as $column) {
							usort($column, array("AECUtility",'build_admin_links_sort'));
							//Build the items
							$items = '';
							foreach ($column as $info) {
								if ($info['enabled'] == '0') 
									continue;
								
								//Build column links
								switch($info['id']) {
									case 'dropdownapprove':
										if ($comment->comment_approved == "0" || $comment->comment_approved == "spam")
											$items .= $approve;
										break;
									case 'dropdownmoderate':
										if ($comment->comment_approved != "0") 
											$items .= $moderate;
										break;
									case 'dropdownspam':
										if ($comment->comment_approved != "spam")
											$items .= $spam;
										break;
									case 'dropdowndelink':
										if (!isset($delink))  //don't delink a comment if it has no link
											$items .= $delink_link;
										break;
									case 'dropdowndelete':
										$items .= $delete;
										break;
									case 'dropdownblacklist':
										$items .= $blacklist;
										break;
									case 'dropdownemail':
										$items .= $email;
										break;
									case 'dropdownmove':
										$items .= $move;
										break;
								} //end switch
							} //end foreach
							$cols[sizeof($cols)] = $items;
						} //end foreach
						
						
						//Output the column containers for the dropdown
						for ($i = 0; $i < sizeof($cols); $i++) {
							$content .= "<span style='display:block;float:left'>";
							$content .= $cols[$i];
							$content .= "</span>";
						}
						
						$content .= "</span>"; //end dropdown menu container
					} elseif($aecomments->get_admin_option( 'icon_display' ) == "classic" || $aecomments->get_admin_option( 'icon_display' ) == "iconsonly" || $aecomments->get_admin_option( 'icon_display' ) == 'noicons'){
						//Clasic view
						$classic = $aecomments->get_admin_option( 'classic' );
						$columns = array();
						foreach ($classic as $items => $item) {
							if ($item['enabled'] == '0') { continue; }
							$columns[sizeof($columns)] = $item;
						}
						//Create the array columns
						usort($columns, array("AECUtility",'build_admin_links_sort_classic'));
						//Build the columns for the dropdown
						foreach ($columns as $column) {
							//Build column links
							switch($column['id']) {
								case 'edit':
									array_push($aec_content_arr,$edit);
									break;
								case 'approve':
									if ($comment->comment_approved == "0" || $comment->comment_approved == "spam")
										array_push($aec_content_arr,$approve);
									break;
								case 'moderate':
									if ($comment->comment_approved != "0") 
										array_push($aec_content_arr,$moderate);
									break;
								case 'spam':
									if ($comment->comment_approved != "spam")
										array_push($aec_content_arr,$spam);
									break;
								case 'delink':
									//don't delink a comment if it has no link
									if (isset($delink_link) && !empty( $delink_link ) )  { 
										array_push($aec_content_arr,$delink_link);
									}
									break;
								case 'delete':
									array_push($aec_content_arr,$delete);
									break;
								case 'blacklist':
									array_push($aec_content_arr,$blacklist);
									break;
								case 'email':
									array_push($aec_content_arr,$email);
									break;
								case 'move':
									array_push($aec_content_arr,$move);
									break;
							} //end switch
							
						} //end foreach
						if ($aecomments->get_admin_option( 'icon_display' ) == 'noicons') 
							$content .= sprintf("[%s]", implode(' | ', $aec_content_arr));
						else 
							$content .= implode('', $aec_content_arr);
					} //endif
					$content .= "<br style='clear:left' />";
					return $content;
			}
			return $content;
		} //end build admin links
		
		/*
		can_edit - Determines if a user can edit a particular comment on a particular post
		Parameters - commentID, postID
		Returns - Enumeration (0=unsuccessful,1=successful,or string error code)
		*/
		//public static class.core
		public static function can_edit($commentID = 0, $postID = 0) {
			global $wpdb, $aecomments;
			
			//Check if admin/editor/post author
			if (AECCore::is_comment_owner($postID)) {
				return 1;
			}
			
			//Get the current comment, if necessary
			$comment = AECCore::get_edit_comment($commentID); 
			
			//Check to see if the user is logged in and can indefinitely edit
			if ($comment['user_id'] != 0) {
				if ($aecomments->get_admin_option( 'allow_registeredediting' ) == 'false') {
					
					return 'no_user_editing';
				}
			} else {
				//Check to see if admin allows comment editing for anonymous users
				if ($aecomments->get_admin_option( 'allow_editing' ) == "false") 
					return 'no_user_editing';
			}
			
			if (!$comment) { return 'get_comment_failed'; }
			//Check to see if the comment is spam
			if ($comment['comment_approved'] === 'spam') { 
				return 'comment_spam';
			}
			
			//Check to see if the user is logged in and can indefinitely edit
			if ( is_user_logged_in() ) {
				global $current_user;
				$user_id = $current_user->ID;
				if ( $user_id == $comment[ 'user_id' ] && AECCore::can_indefinitely_edit( $comment[ 'user_id' ] ) ) {
					return 1;
				}
			}
			
			
			
			//Now we check to see if there is any time remaining for comments
			$timestamp = $comment['time'];
			$time = current_time('timestamp',1)-$timestamp;
			$minutesPassed = round(((($time%604800)%86400)%3600)/60); 

			//Get the time the admin has set for minutes
			$minutes = $aecomments->get_admin_option( 'minutes' );
			if (!is_numeric($minutes)) {
				$minutes = $aecomments->get_minutes(); //failsafe
			}
			if ($minutes < 1) {
				$minutes = $aecomments->get_minutes();
			}
			if (($minutesPassed - $minutes) > 0) {
				return 'comment_time_elapsed';
			}
			
			//Now check if options allow editing after an additional comment has been made
			if ($aecomments->get_admin_option( 'allow_editing_after_comment' ) == "false") {
				//Admin doesn't want users to edit - so now check if any other comments have been left
				$query = "SELECT comment_ID from $wpdb->comments where comment_post_ID = %d and comment_type <> 'pingback' and comment_type <> 'trackback' order by comment_ID DESC limit 1";
				$newComment = $wpdb->get_row( $wpdb->prepare( $query, $postID ), ARRAY_A); 
				if (!$newComment) { return 'new_comment_posted'; }
				//Check to see if there is a higher comment ID
				if ($commentID != $newComment['comment_ID']) { return 'new_comment_posted'; }
			}
			//Check to see if cookie is set
			$hash = md5($comment['comment_author_IP'] . $comment['comment_date_gmt']);
			if ( !isset( $_COOKIE['WPAjaxEditCommentsComment' . $commentID . $hash] ) ) {
				return 'comment_edit_denied'; 
			}
			//Get post security key
			$postContent = $wpdb->get_row( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d and meta_key = '_%d'", $comment['comment_post_ID'], $comment['comment_ID'] ), ARRAY_A);//$wpdb->get_row("SELECT post_content from $wpdb->posts WHERE post_type = 'ajax_edit_comments' and guid = $commentID order by ID desc limit 1", ARRAY_A);
			if (!$postContent) { return 'comment_edit_denied'; }
			
			
			
			//Now check to see if there's a valid cookie
			if ( !isset( $GLOBALS['WPAjaxEditCommentsComment' . $commentID . $hash] ) ){ //For compatability with CFORMS
				if (isset($_COOKIE['WPAjaxEditCommentsComment' . $commentID . $hash])) {
					if ($_COOKIE['WPAjaxEditCommentsComment' . $commentID . $hash] != $postContent['meta_value']) { return 'comment_edit_denied'; }
				} else {
					return 'comment_edit_denied';
				}
			} else {
				if ($GLOBALS['WPAjaxEditCommentsComment' . $commentID . $hash] != $postContent['meta_value']) { return 'comment_edit_denied'; }
			}
			return 1;  //Yay, user can edit
		} //End function can_edit
		/*
		can_edit_quickckech - Quick check without DB access before can_edit() --ag
		Parameters - comment
		Returns - Enumeration (0=unsuccessful,1=successful,or string error code)
		*/
		//public static class.core
		public static function can_edit_quickcheck($comment) {
			global $aecomments;
			//Check if admin/editor/post author
			if (AECCore::is_comment_owner($comment->comment_post_ID)) {
				return 1;
			}
			//Check to see if the user is logged in and can indefinitely edit
			if (AECCore::can_indefinitely_edit($comment->user_id)) {
			return 1;
			}
			

			//Now we check to see if there is any time remaining for comments
			$timestamp = strtotime($comment->comment_date);
			$time = current_time('timestamp',get_option('gmt_offset'))-$timestamp;
						$minutesPassed = round(((($time%604800)%86400)%3600)/60); 

			//Get the time the admin has set for minutes
			$minutes = $aecomments->get_admin_option( 'minutes' );
			if (!is_numeric($minutes)) {
				$minutes = $aecomments->get_minutes();//failsafe
			}
			if ($minutes < 1) {
				$minutes = $aecomments->get_minutes();
			}
			if (($minutesPassed - $minutes) > 0) {
				return 'comment_time_elapsed';
			} else {
				return 1;  //Yay, user can edit
			}

		} //End function can_edit_quickcheck
		/* can_edit_name - Checks to see if a user can edit a name
		Parameters - $commentID, $postID
		Returns true if one can, false if not */
		//public static class.core
		public static function can_edit_name($commentID, $postID) {
			global $aecomments;
			if (AECCore::is_comment_owner($postID)) { return true; }
			$comment = get_comment($commentID, ARRAY_A);
			if ( is_user_logged_in() ) { //logged in
				if ($aecomments->get_admin_option( 'registered_users_name_edit' ) == "true") { return true;}
			} else { //not logged in 
				if ($aecomments->get_admin_option( 'allow_name_editing' ) == "true") { return true;}
			}
			return false;
		}
		/* can_edit_email - Checks to see if a user can edit an e-mail address
		Parameters - $commentID, $postID
		Returns true if one can, false if not */
		//public static class.core
		public static function can_edit_email($commentID, $postID) {
			global $aecomments;
			$comment = get_comment($commentID, ARRAY_A);
			//Return false if comment is pingback or trackback
			if ($comment['comment_type'] == "pingback" || $comment['comment_type'] == 'trackback') { return false; }
			if (AECCore::is_comment_owner($postID)) { return true; }	
			
			if ( is_user_logged_in() ) { //logged in
				if ($aecomments->get_admin_option( 'registered_users_email_edit' ) == "true") { return true;}
			} else { //not logged in 
				if ($aecomments->get_admin_option( 'allow_email_editing' ) == "true") { return true;}
			}
			return false;
		}
		/* can_edit_url - Checks to see if a user can edit a url
		Parameters - $commentID, $postID
		Returns true if one can, false if not */
		//public static class.core
		public static function can_edit_url($commentID, $postID) {
			global $aecomments;
			if (AECCore::is_comment_owner($postID)) { return true; }
			$comment = get_comment($commentID, ARRAY_A);
			if ( is_user_logged_in() ) { //logged in
				if ($aecomments->get_admin_option( 'registered_users_url_edit' ) == "true") { return true;}
			} else { //not logged in 
				if ($aecomments->get_admin_option( 'allow_url_editing' ) == "true") { return true;}
			}
			return false;
		}
		/* can_edit_options - Checks to see if a non-admin user can edit various options 
		Parameters - $commentID, $postID
		Returns true if one can, false if not */
		//public static class.core
		public static function can_edit_options($commentID, $postID) {
			global $aecomments;
			if (AECCore::is_comment_owner($postID)) { return true; }
			$comment = get_comment($commentID, ARRAY_A);
			if ( is_user_logged_in() ) { //logged in
				if ($aecomments->get_admin_option( 'registered_users_url_edit' ) == "true" || $aecomments->get_admin_option( 'registered_users_email_edit' ) == "true" || $aecomments->get_admin_option( 'registered_users_name_edit' ) == "true" ) { return true;}
			} else { //not logged in 
				if ($aecomments->get_admin_option( 'allow_url_editing' ) == "true" || $aecomments->get_admin_option( 'allow_email_editing' ) == "true" || $aecomments->get_admin_option( 'allow_name_editing' ) == "true") { return true;}
			}
			return false;
		}
		/* can_indefinitely_edit
		Parameters - $userID
		Returns - true if can, false if not */
		//public static class.core
		public static function can_indefinitely_edit($userID = 0) {
			global $aecomments;
			if ( is_user_logged_in() ) {
				//User is logged in and this is the user's comment - Does admin allow indefinite editing?
				if ($aecomments->get_admin_option( 'registered_users_edit' ) == "true") {
					return true; //Logged in user can indefinitely edit
				}
			}
			return false;
		}
		/* can_scroll 
		Checks to see if an admin can scroll to the comment or not 
		Returns - true if can, false if not*/
		//public static class.core
		public static function can_scroll() {
			global $aecomments;
			//if (AECCore::is_comment_owner($postID)) {
				if ($aecomments->get_admin_option( 'javascript_scrolling' ) == "true") {
					return "true";
				}
			//}
			return "false";
		}
		/* check_spam - Checks an edited comment for spam 
		Parameters - $commentID, $postID
		Returns - True if spam, false if not */
		//public static class.core
		public static function check_spam($commentID = 0, $postID = 0) {
			global $aecomments;
			
			//Check to see if spam protection is enabled
			if ($aecomments->get_admin_option( 'spam_protection' ) == "none") { return false;} 
			//Return if user is post author or can edit posts
			if ( AECCore::is_comment_owner($postID)) { return false; }
			
			if (function_exists("akismet_check_db_comment") && $aecomments->get_admin_option( 'spam_protection' ) == 'akismet') { //Akismet
				//Check to see if there is a valid API key
				if (akismet_verify_key(get_option('wordpress_api_key')) != "failed") { //Akismet
					$response = akismet_check_db_comment($commentID);
					if ($response == "true") { //You have spam
						wp_set_comment_status($commentID, 'spam');
						return true;
					}
				}
			} elseif ($aecomments->get_admin_option( 'spam_protection' ) == "defensio" && function_exists('defensio_post') ) { //Defensio
				global $defensio_conf, $wpdb;
				$comment = get_comment($commentID, ARRAY_A);
				if (!$comment) { return true; }
				$comment['owner-url'] = $defensio_conf['blog'];
				$comment['user-ip'] = $comment['comment_author_IP'];
				$comment['article-date'] = strftime("%Y/%m/%d", strtotime($wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID=" . $comment['comment_post_ID'])));
				$comment['comment-author'] = $comment['comment_author'];
				$comment['comment-author-email'] = $comment['comment_author_email'];
				$comment['comment-author-url'] = $comment['comment_author_url'];
				$comment['comment-content'] = defensio_unescape_string($comment['comment_content']);
				if (!isset($comment['comment_type']) or empty($comment['comment_type'])) {
					$comment['comment-type'] = 'comment';
				} else {
					$comment['comment-type'] = $comment['comment_type'];
				}
				if (defensio_reapply_wp_comment_preferences($comment) === "spam") {
					return true;
				}
				$results = defensio_post('audit-comment',$comment);
				$ar = Spyc :: YAMLLoad($results);
				if (isset($ar['defensio-result'])) {
					if ($ar['defensio-result']['spam']) {
						wp_set_comment_status($commentID, 'spam');
						return true;
					}
				}
			}
			return false;			
		} //end function check_spam
		
		
		/*
		get_can_comment - Get comment values, if necessary
		Parameters - commentID
		Returns - Array of values for comment if successfull
		*/
		//public static class.core
		public static function get_edit_comment($commentID) {
			global $comment, $wpdb;
			if ( !is_object( $comment ) ) {
				$comment = get_comment( $commentID );
			}

			if ($comment->comment_ID == $commentID) {
				$c = get_object_vars($comment);
				$c['time'] = mysql2date('U', $c['comment_date']);
				return $c;
			}

			$query = "SELECT UNIX_TIMESTAMP(comment_date) time, comment_author_email, comment_author_IP, comment_date_gmt, comment_post_ID, comment_approved, comment_ID, user_id  FROM $wpdb->comments where comment_ID = $commentID";
			return($wpdb->get_row($query, ARRAY_A));
		} //end get_edit_comment
		public static function get_icon_size() {
			global $aecomments;
			switch ($aecomments->get_admin_option( 'icon_set' )) {
				case "aesthetica-large":
				case "classy-large":
				case "shadow":
				case "moi":
				case "pictos-dark":
				case "pictos-light":
					return 24;
					break;
				default:
					return 16;
					break;
			} //end switch
		} //end get_icon_size
		/* increment_edit_count - Increments the number of edits */
		public static function increment_edit_count() {
			global $aecomments;
			$numEdits = intval($aecomments->get_admin_option( 'number_edits' ));
			$numEdits += 1;
			$aecomments->save_admin_option( 'number_edits', $numEdits );
		} //end increment_edit_count
		
		/* Initializes all the error messages */
		//public static class.core
		public static function initialize_errors() {
			global $aecomments;
			$errors = new WP_Error();
			$errors->add('new_comment_posted', __('You cannot edit a comment after other comments have been posted.', 'ajaxEdit'));
			$errors->add('comment_time_elapsed', __('The time to edit your comment has elapsed.','ajaxEdit'));
			$errors->add('comment_edit_denied',__('You do not have permission to edit this comment.','ajaxEdit') );
			$errors->add('comment_marked_spam',$aecomments->get_admin_option( 'spam_text' ));
			$errors->add('comment_spam',__('This comment cannot be edited because it is marked as spam.','ajaxEdit') );
			$errors->add('get_comment_failed',__('Comment loading failed.','ajaxEdit') );
			$errors->add('no_user_editing',__('Comment editing has been disabled.','ajaxEdit') );
			$errors->add('comment_spam_failed', __('Could not mark as spam.','ajaxEdit'));
			$errors->add('comment_spam_failed_permission', __('You do not have permission to mark this comment as Spam.','ajaxEdit'));
			$errors->add('delete_failed',__('Could not delete comment.','ajaxEdit') );
			$errors->add('delete_failed_permission',__('You do not have permission to delete this comment.','ajaxEdit') );
			$errors->add('approve_failed_permission', __('You do not have permission to approve this comment.','ajaxEdit'));
			$errors->add('approve_failed', __('Could not approve comment.','ajaxEdit'));
			$errors->add('moderate_failed', __('Could not mark this comment for moderation.','ajaxEdit'));
			$errors->add('moderate_failed_permission', __('You do not have permission to mark this comment for moderation.','ajaxEdit'));
			$errors->add('invalid_email', __('Please enter a valid email address.','ajaxEdit'));
			$errors->add('required_fields', __('Please fill in the required fields (Name, E-mail)','ajaxEdit'));
			$errors->add('content_empty',__('You cannot have an empty comment.','ajaxEdit') );
			$errors->add('delink_failed_permission',__('You do not have permission to delink this comment.','ajaxEdit') );
			$errors->add('comment_marked_moderated',__('Your comment was marked for moderation','ajaxEdit') );
			$errors->add('comment_moderated',__('This comment cannot be edited because it is marked for moderation.','ajaxEdit') );
			$errors->add('request_deletion_failed',__('Request failed.','ajaxEdit') );
			$errors->add('blacklist_empty',__('You have not selected anything to blacklist.','ajaxEdit') );
			$errors->add('upgrade_check_failed',__('Upgrade check failed.','ajaxEdit') );
			$errors->add('upgrade_check_none',__('You are using the latest version.','ajaxEdit') );
			return $errors;
		} //end function initialize_errors
		
		/* is_comment_owner - Checks to see if a user can edit a comment */
		/* Parameters - postID */
		/* Returns - true if user is comment owner, false if not*/
		public static function is_comment_owner($postID = 0) {
			global $aecomments;
			if (!isset($aecomments->admin)) { $aecomments->admin = false; }
			if ($aecomments->admin) { return true; }
			//Check to see if user is admin of the blog
			if (current_user_can('edit_users')) {
				$aecomments->admin = true;
				return true;
			} elseif( current_user_can( 'edit_page', $postID) || current_user_can( 'edit_post', $postID)) { /*author privs */
				$aecomments->admin = false;
				return true;
			} elseif( current_user_can( 'moderate_comments' ) ) {
				$aecomments->admin = false;
				return true;
			}
			return false;
		}
		
		public static function is_multisite() {
			global $aecomments;
			$multisite_network = false;
			if ( ! function_exists( 'is_plugin_active_for_network' ) )  require_once( ABSPATH . '/wp-admin/includes/plugin.php' );		
			if ( is_plugin_active_for_network( $aecomments->get_plugin_path() ) ) {
				$multisite_network = true;
			}
			return $multisite_network;
		}
		/*load_pages - Checks query variables and loads the appropriate page */
		public static function load_pages() {
			global $aecomments;
			$pagepath = $aecomments->get_plugin_dir( '/views/' );
			$queryvar = get_query_var('aec_page');
			if (empty($queryvar)) 
				return;
				
			global $wp_query;
			$wp_query->is_home = false;
			switch($queryvar) {
				case 'comment-editor.php':
					include($pagepath . 'comment-editor.php');
					exit;
					break;
				case 'ajax-processor.php':
					include( $aecomments->get_plugin_dir( '/php/ajax-processor.php' ) );
					exit;
					break;
				case 'email.php':
					include($pagepath . 'email.php');
					exit;
					break;
				case 'move-comment.php':
					include($pagepath . 'move-comment.php');
					exit;
					break;
				case 'blacklist-comment.php':
					include($pagepath . 'blacklist-comment.php');
					exit;
					break;
				case 'comment-popup.php':
					include($pagepath . 'comment-popup.php');
					exit;
					break;
				case 'request-deletion.php':
					include($pagepath . 'request-deletion.php');
					exit;
					break;
			}
		} //end load_pages
		
		/* show_affiliate_link - Returns true if affiliate links should be displayed.  False if not*/
		public static function show_affiliate_link() {
			global $aecomments;
			
			if ( $aecomments->get_admin_option( 'affiliate_show' ) == "false") 
				return false;
			return true;
		} //end show_affiliate_link
		
		
} //end AECCore
?>
