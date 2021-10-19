<?php
// functions that manage the users.php page in admin and maybe more
class WTPUser {
	static function add_custom_column($columns) {	
		$columns['watu_exams'] = sprintf(__('%s Data', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));
	 	return $columns;	
	}
	
	static function manage_custom_column($empty='', $column_name = '', $id = 0) {		
		if( $column_name == 'watu_exams' ) {
			$html = "<a href='admin.php?page=my_watupro_exams&user_id=$id' target='_blank'>".sprintf(__('%s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))."</a> |
			<a href='admin.php?page=watupro_my_certificates&user_id=$id' target='_blank'>".__('Certificates', 'watupro')."</a>";
			
			if(get_option('watupro_calculate_total_user_points') == 1) {
				$total_points = get_user_meta($id, 'watupro_total_points', true);
				
				$html .= '<br>'.sprintf(__('%s points earned', 'watupro'), $total_points);
			}
			
			return $html;
	  }
	  
	  // this is used only from Reporting module
	  if( $column_name == 'exam_reports' ) {
			return "<a href='admin.php?page=watupro_reports&user_id=$id' target='_blank'>".__('View reports', 'watupro')."</a>";
	  }
	  
	  return $empty;
	}
	
	// checks if user can access exam and outputs the proper strings
	// for now calls can_access() from lib/watupro.php
	static function check_access($exam, $post) {
		WatuPRO::$output_sent = false; // change this var from class method to avoid outputting the generic message
		if(!WatuPRO::can_access($exam)) {
			// show the quiz description even without access?
			$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
			if(!empty($advanced_settings['always_show_description'])) {
				// if there is {{{button}}} tag remove it
				$exam->description = preg_replace('#({{{button).*?(}}})#', '', $exam->description);				
				
				echo apply_filters('watupro_content', wpautop(stripslashes($exam->description)));
			}			
			
			// maybe it's a paid quiz that requires no user login?
			if($exam->fee > 0 and class_exists('WatuPROIExam') and method_exists('WatuPROIExam', 'adjust_price')) WatuPROIExam :: adjust_price($exam);
			if($exam->fee > 0 and empty($exam->require_login)) return false;			
			
			 // not logged in error
			 if(!is_user_logged_in()) {
			 	
				// is there custom login/register text?
				$login_register_text = get_option('watupro_login_register_text');
				if(empty($login_register_text)) {
					 echo "<p><b>".sprintf(__('You need to be registered and logged in to take this %s.', 'watupro'),__('quiz', 'watupro')). 
		      	" <a href='".wp_login_url(get_permalink( $post->ID ))."'>".__('Log in', 'watupro')."</a>";
			      if(get_option("users_can_register")) {
							echo " ".__('or', 'watupro')." <a href='".add_query_arg(array("watupro_redirect_to"=> get_permalink( $post->ID ), 'watupro_register'=>1), wp_registration_url())."'>".__('Register', 'watupro')."</a></b>";        
						}
						echo "</p>";
				}			 	
			 	else {
			 		$login_register_text = stripslashes($login_register_text);
			 		$login_register_text = str_replace('{{{quiz-url}}}', get_permalink( $post->ID ), $login_register_text);
			 		
			 		// if it's just text and no HTML, add p tags around
			 		if(!strstr($login_register_text, '<')) $login_register_text = '<p>'.$login_register_text.'</p>';
			 		echo $login_register_text;
			 	}
		     
		   }	
		   else { // logged in but no rights to access
		  	if(!WatuPRO::$output_sent) echo "<p>".sprintf(__('You are not allowed to access this %s at the moment.', 'watupro'), WATUPRO_QUIZ_WORD)."</p><!-- logged in but no rights to access-->";
		  } 
		  return false;  // can_access returned false  
		}
		
		return true;
	}
	
	// delete user data?
	static function auto_delete_data($user_id) {
		global $wpdb;
		if(get_option('watupro_auto_del_user_data') != 'yes') return false;
		
		// delete all records from takings and student_answers tables
		self :: delete_data($user_id);
	}
	
	// delete all user data		
	static function delete_data($user_id) {
		global $wpdb;
		
		// when called by the WP data eraser we get email address instead of ID		
		if(!is_numeric($user_id) and strstr($user_id, '@')) {
		}
		
		// delete exam results
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE user_id=%d", $user_id));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE user_id=%d", $user_id));
		
		// delete certificates
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." WHERE user_id=%d", $user_id));
		
		// delete user files
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_FILES." WHERE user_id=%d", $user_id));
		
		// cleanup watuproplay points if any
		update_user_meta($user_id, 'watuproplay-points', 0);			
	} // end delete data
	
	// again deleting user data but this time when called from the WP erase data hook
	static function erase_data($email_address, $page = 1) {
		 global $wpdb;

		 $number = 200; // Limit us to avoid timing out
  		 $page = (int) $page;
  		 $email_address = sanitize_email($email_address);
  		 
  		 // find student
  		 $user = get_user_by('email', $email_address);
  		 
  		 if(empty($user->ID)) {
  		 	// delete exam results
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." 
				WHERE taking_id IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." WHERE email=%s)", $email_address));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE email=%s", $email_address));
  		 }
  		 else self :: delete_data($user->ID);
  		 
  		 return array( 'items_removed' => true,
		    'items_retained' => false, // always false in this example
		    'messages' => array(), // no messages in this example
		    'done' => true,
		  );
  	} // end data eraser
	
	// register personal data eraser
	static function register_eraser($erasers) {
		 $erasers['watupro'] = array(
		    'eraser_friendly_name' => __( 'Watu PRO', 'watupro' ),
		    'callback'             => array(__CLASS__, 'erase_data')
		    );
		    
		  return $erasers;
	}
	
	// split final screen contents depending on logged in status
	static function split_final_screen(&$content) {
		if(!strstr($content, '{{{loggedin}}}')) return false;
		
		$parts = explode('{{{loggedin}}}', $content);
		
		if(is_user_logged_in()) $content = $parts[0]; // logged in gets the part before the tag
		else $content = $parts[1];
	}
	
	// delay / hold test results accordingly to user role or user group
	static function delay_results($exam) {
		global $wpdb, $user_ID;
		$delay_results = $exam->delay_results;
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		
		// if no delay_results at all or no condition return as is
		if(empty($exam->delay_results) or empty($advanced_settings['delay_results_per_group']) 
			or empty($advanced_settings['delay_results_groups'])  or !is_array($advanced_settings['delay_results_groups'])) return $exam->delay_results;
		
		// now let's set the return value to zero and put it back to 1 only in case of a match
		$delay_results = false; 
		
		// check for conditions for non-logged in, this the same if using groups or roles
		if(!is_user_logged_in() and in_array('guest', $advanced_settings['delay_results_groups'])) return true;
		
		// at this point if the user is not logged in we have to return false and not check the roles or groups
		// because we have selected that delay will not be applied to guests
		if(!is_user_logged_in()) return false;			
		
		// OK, apparently there is a hold AND there is a condition that it applies to only some groups.
		$use_wp_roles = get_option('watupro_use_wp_roles');	
		if($use_wp_roles == 1) {
			// using WP roles
			$user = wp_get_current_user();
			foreach($advanced_settings['delay_results_groups'] as $role_key) {
				if ( in_array( $role_key, (array) $user->roles ) ) return true; // even one role found, means delay applies
			}
		}
		else {
			// using WatuPro Groups
			$user_groups = get_user_meta($user_ID, "watupro_groups", true);  
			if(empty($user_groups) or !is_array($user_groups)) return false;
			foreach($advanced_settings['delay_results_groups'] as $group) {
				if ( in_array( $group, $user_groups ) ) return true; // even one group found, means delay applies
			}
		} // end checking for groups
		
		return $delay_results; // just in case no condition was met		
	} // end delay_results()
	
	// find user IDs for users with the same WatuPRO groups as the current user
	/* Deprecated, we now use the better function watupro_same_groups_uids() which also handles roles
	static function same_group_uids() {
		global $wpdb;
		
		$uids = array(0);
		if(!is_user_logged_in()) return $uids;
		
		$user_id = get_current_user_id();
		$user_groups = get_user_meta($user_id, "watupro_groups", true);
		
		//print_r($user_groups);
		
		foreach($user_groups as $user_group) {
			$group_uids = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} 
				WHERE meta_key = 'watupro_groups' AND meta_value LIKE '%:\"".intval($user_group)."\"%' ");
			
			// add to $uids
			foreach($group_uids as $group_uid) {
				if(!in_array($group_uid->user_id, $uids)) $uids[] = $group_uid->user_id;
			}	
		}
		//print_r($uids);
		return $uids;		
	} // end same_group_uids()*/
}