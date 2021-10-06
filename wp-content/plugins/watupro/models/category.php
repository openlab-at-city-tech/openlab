<?php
// handles both exam and question categories. We should move out exam categories handling from here to avoid confusions
class WTPCategory {
	// discovers ID of a category. If not found, creates the category
	// receives the array of all cats to avoid multiple queries because this function is used on import
	static function discover($name, &$categories) {
		global $wpdb;
		
		if(empty($name)) return 0;
		
		// has parent?
		$has_parent = $parent_id = 0;
		if(strstr($name, '>>>')) {
			$has_parent = 1;
			$parts = explode('>>>', $name);
			$name = trim($parts[1]);
			$parent_name = trim($parts[0]);
			
			foreach($categories as $cat) {
				if($cat->name == $parent_name) $parent_id = $cat->ID;
			}
		}
		
		foreach($categories as $cat) {
			if($has_parent) {
				if($cat->name == $name and $parent_id == $cat->parent_id) return $cat->ID;
			}
			else if($cat->name == $name) return $cat->ID;
		}
		
		// Not returned ID up to this point? Create category
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_QCATS." (name, description, parent_id) VALUES (%s, %s, %d)", 
			$name, '', $parent_id));
		$insert_id = $wpdb->insert_id;
		
		// add to cats array
		$cat = $wpdb->get_row("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=$insert_id");
		$categories[] = $cat;		
		
		return $insert_id;
	}
	
	// what EXAM categories does this user have access to
	static function user_cats($uid) {
		global $wpdb;		
		
		$cat_ids=array(0); // Uncategorized are always in
		$user_groups = get_user_meta($uid, "watupro_groups", true);
		if(!is_array($user_groups)) $user_groups = array($user_groups);
		$cats=$wpdb->get_results("SELECT * FROM ".WATUPRO_CATS);

		$use_wp_roles = get_option('watupro_use_wp_roles');
		
		$user = get_userdata($uid);
		//print_r($user->roles);
		foreach($cats as $cat) {
			if($cat->ugroups=="||" or empty($cat->ugroups)) {
				$cat_ids[]=$cat->ID;
				continue;
			}			
			
			if($use_wp_roles) {
				$allowed_roles = explode("|", $cat->ugroups);
				//print_r($allowed_roles);
				foreach($allowed_roles as $role) {
					if(empty($role)) continue;					 
					if ( in_array( $role, (array) $user->roles ) ) {
						$cat_ids[] = $cat->ID;
						break;
					}
				}  // end foreach role 
			} // end if using WP roles
			else { // using user groups
			  if(sizeof($user_groups)>0 and is_array($user_groups)) {
				  foreach($user_groups as $g) {
					  if(strstr($cat->ugroups, "|".$g."|")) {
						  $cat_ids[]=$cat->ID;
					  } // end if
				  } // end foreach group
			  } // end if there are any groups
			} // end if using user groups
		} // end foreach cats
		//echo "CATEGORIES:";		
		//print_r($cat_ids);
		return $cat_ids;
	}
	
	// add question category, no duplicates
	static function add($name, $description='', $parent_id=0, $exclude_from_reports=0, $icon = '') {
		global $wpdb, $user_ID;
		
		$name = sanitize_text_field($name);
		$description = watupro_strip_tags($description);
		$exclude_from_reports = empty($exclude_from_reports) ? 0 : 1;
		$icon = sanitize_text_field($icon);
		
		// already exists?
		$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_QCATS." 
			WHERE name=%s AND parent_id=%d", $name, $parent_id));		
		if($exists) return false;
		
		$wpdb -> query( $wpdb -> prepare(" INSERT INTO ".WATUPRO_QCATS." 
			SET name=%s, description=%s, editor_id=%d, parent_id=%d, exclude_from_reports=%d, icon=%s", 
			$name, $description, $user_ID, $parent_id, $exclude_from_reports, $icon) );		
		return $wpdb->insert_id;
	}
	
	// save category, no duplicates
	static function save($name, $id, $description='', $exclude_from_reports = 0, $icon = '') {
		global $wpdb;
		$id = intval($id);
		$name = sanitize_text_field($name);
		$description = watupro_strip_tags($description);
		$exclude_from_reports = empty($exclude_from_reports) ? 0 : 1;
		$icon = sanitize_text_field($icon);
		
		// select cat
		$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $id));
		
		// another one with this name already exists?
		$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_QCATS." 
			WHERE name=%s AND ID!=%d AND parent_id=%d", $name, $id, $cat->parent_id));		
		if($exists) return false;
		
		$wpdb -> query( $wpdb -> prepare(" UPDATE ".WATUPRO_QCATS." SET name=%s, description=%s, exclude_from_reports=%d, icon=%s 
		WHERE ID=%d", $name, $description, $exclude_from_reports, $icon, $id) );	
		
		return true;
	}
	
	// delete
	static function delete($id) {
		global $wpdb;
		
		$wpdb -> query( $wpdb->prepare("DELETE FROM ".WATUPRO_QCATS." WHERE id=%d", $id) );
	}	
	
	// This method works with EXAM categories
	// user group checks - can user access this exam based on category/user group restrictions
	static function has_access($exam) {
		 global $wpdb, $user_ID;
		
    	 if(!$exam->cat_id) return true; // uncategorized exams are not restricted further
    	     	 
    	 // select exam category
    	 $cat=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CATS." WHERE id=%d", $exam->cat_id));    	 
    	 if(empty($cat->ugroups) or $cat->ugroups=="||") return true;
    	 
    	 // restricted to certain groups
    	 $cat_groups=explode("|",$cat->ugroups);
    	 
    	 $use_wp_roles = get_option('watupro_use_wp_roles');    	 
    	 if($use_wp_roles) {    	 	
    	 	 $roles = $cat_groups;
			 foreach($roles as $role) {
			 	if(empty($role)) continue;
			 	if(current_user_can($role)) return true;
			 } // end foreach role
			 echo "<!-- WATUPROCOMMENT user role has no access to this category -->";
    	 } // end if using WP roles
    	 else {    	 	
	    	 $user_groups=get_user_meta($user_ID, "watupro_groups", true);  
	    	 if(!is_array($user_groups)) $user_groups = array($user_groups);  	
			 
			 if(!is_array($user_groups)) {
			 	echo "<!-- WATUPROCOMMENT not in any user groups -->";
			 	return false;
			 } // end if
			 
			 foreach($user_groups as $group) {
			 	if(empty($group)) continue;
			 	if(in_array($group, $cat_groups)) return true;
			 }  // end foreach group 
		} // end if using user groups
		
		return false;  	
	} // end has_access
	
	// sort categories by name when showing an exam
	static function sort_cats($cats, $advanced_settings, $exam = null) {
	   if(empty($advanced_settings['sorted_categories'])) sort($cats);
		else {			
			// sort by the advanced settings which is stored as array (name => order, name => order)
			$final_cats = array();
			$sorted_cats = $advanced_settings['sorted_categories'];
			
			asort($sorted_cats); // sort by the order number
			// print_r($sorted_cats);
			foreach($sorted_cats as $key => $val) {
				if(!empty($advanced_settings['sorted_categories_encoded'])) $key = rawurldecode($key);
				foreach($cats as $cat) {										
					if($cat == $key) $final_cats[] = $cat;
				}
			}
			
			// any cats left out of final cats? (could happen if we saved quiz, then changed category of a question to a category that had no
			// questions before that). Add them at the end
			foreach($cats as $cat) {
				if(!in_array($cat, $final_cats)) $final_cats[] = $cat;
			}
			
			$cats = $final_cats;
		}
		
		if(!empty($exam->randomize_cats)) shuffle($cats);
		
		return $cats;
	} // end sort_cats
}