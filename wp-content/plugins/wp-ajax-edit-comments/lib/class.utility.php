<?php
class AECUtility {
		//Sorts arrays for the drop-down feature
		//public static class.utility
		public static function build_admin_links_sort($a, $b) {
			return strnatcmp($a['position'], $b['position']); 
		}
		//Sorts arrays for the drop-down feature
		//public static class.utility
		public static function build_admin_links_sort_classic($a, $b) {
			return strnatcmp($a['column'], $b['column']); 
		}
		//Builds an undo message
		//public static class.utility
		public static function build_undo_url($action,$commentID,$postID, $message) {
			global $aecomments;
			$undo_url = wp_nonce_url( add_query_arg( array( 'action' => $action, 'pid' => $postID, 'cid' => $commentID ), admin_url( 'admin-ajax.php' ) ) );
			$undo = "<em>$message<span class='aec-undo-span undo$commentID'> - <a href='$undo_url' class='aec-undo-link'>" . __('Undo', 'ajaxEdit') . "</a></em></span>";
			return $undo;
		} //end build_undo_url
		
		
		/* encode - Encodes comment content to various charsets 
		Parameters - $content - The comment content 
		Returns the encoded content */ 
		public static function encode($content) {
			global $aecomments;
			if ($aecomments->get_admin_option( 'use_mb_convert' ) == "false" || !function_exists("mb_convert_encoding")) { return $content; }
			return mb_convert_encoding($content, ''.get_option('blog_charset').'', mb_detect_encoding($content, "UTF-8, ISO-8859-1, ISO-8859-15", true));
		} //end encode
		
		/* filter_strip_trackbacks
		Parameters - $comment - The comment to extract the trackback
		Returns true if no trackback is present, else false*/
		//Called from AECFilter::filter_trackbacks
		public static function filter_strip_trackbacks($comment) {
			if ($comment->comment_type == 'trackback' || $comment->comment_type == 'pingback')
				return false;
			return true;
		} //end filter_strip_trackbacks
		
		/* get_comment_id - Returns an ID based on an incoming string */
		public static function get_comment_id($string) {
			preg_match('/([0-9]+)$/i', $string, $matches);
			if (is_numeric($matches[1])) {
				return $matches[1];
			} 
			return 0;
		} //end get_comment_id
		
		
		//Returns a logged-in user's e-mail address
		public static function get_user_email() {
			global $user_email;
			if (!function_exists("get_currentuserinfo")) { return ''; }
			if (empty($user_email)) {get_currentuserinfo();} //try to get user info
			if (empty($user_email)) { return '0'; } //Can't get user info, so return empty string
			return $user_email;
		} //end get_user_email
		
		// Returns a logged-in user's ID
		public static function get_user_id() {
			global $user_ID;
			if (!function_exists("get_currentuserinfo")) { return "-1"; }
			if (empty($user_ID)) {get_currentuserinfo();} //try to get user info
			if (empty($user_ID)) { return '-1'; } //Can't get user info, so return empty string
			return $user_ID;
		} //end get_user_id
		
		/* is_logged_in - Checks to see if the user (non-admin) is logged in 
		Parameters - $userID
		Returns true if logged in, false if not */
		//remove for is_user_logged_in()
		public static function is_logged_in($userID = 0) {
			if ( AECUtility::get_user_id() == $userID) {
				return true;
			} else { 
				return false;
			}
		}
		
		//Localizes a script using function get_js_vars
		//Builds an object based on the passed $name variable
		//public static class.utility
		public static function js_localize($name, $vars, $just_data = false) {
			$data = "var $name = {";
			$arr = array();
			foreach ($vars as $key => $value) {
				$arr[count($arr)] = $key . " : '" . esc_js($value) . "'";
			}
			$data .= implode(",",$arr);
			$data .= "};";
			
			$content = "<script type='text/javascript'>\n";
			$content .= "/* <![CDATA[ */\n";
			$content .= $data;
			$content .= "\n/* ]]> */\n";
			$content .= "</script>\n";
			
			if ( !$just_data ) {
				echo $content;
			} else {
				echo $data;
			}
		} //end js_localize
		
		//Get a user's role
		//public static class.utility
		public static function get_user_role() {
			global $current_user;
		
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
		
			return $user_role;
		} //end get_user_role
		
		//Returns a random security key
		public static function random() {
			$chars = "%CDEF#cGHIJ\:ab!@defg9ABhijklmn<>;opqrstuvwxyz10234/+_-=5678MKL^&*NOP";
			$pass = '';
			for ($i = 0; $i < 50; $i++) {
				$pass .= $chars{rand(0, strlen($chars)-1)};
			} //end for
			return $pass;
		} //end random

		/**
		 * Wrapper for get_post_types with preset arguments to get all default post types
		 * @param  array 	$args 		An array of key value arguments to match against the post types	
		 * @param  string 	$output 	The type of output to return, either 'names' or 'objects'
		 * @param  string 	$operator 	Operator (and/or) to use with multiple $args
		 * @return array
		 */
		public static function get_post_types($args = [], $output = "names", $operator = 'and') {
			$default_args = [
				'public'   => true,
				'_builtin' => true,
			];

			$args = array_merge($default_args, $args);

			$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'

			return get_post_types( $args, $output, $operator );
		}
} 
?>