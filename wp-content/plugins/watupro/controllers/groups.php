<?php
// user groups
function watupro_groups() {
	global $wpdb;
	$groups_table = WATUPRO_GROUPS;
	
	if(!empty($_POST['roles_to_groups'])) {
		$use_wp_roles = empty($_POST['use_wp_roles']) ? 0 : 1;
		update_option('watupro_use_wp_roles', $use_wp_roles);
	}		
		
	if(!empty($_POST['add'])) {
		$wpdb->query($wpdb->prepare("INSERT INTO $groups_table (name, is_def)
			VALUES (%s, %d)", $_POST['name'], intval(@$_POST['is_def'])));
	}
	
	if(!empty($_POST['save'])) {
		$wpdb->query($wpdb->prepare("UPDATE $groups_table SET
			name=%s, is_def=%d WHERE ID=%d", $_POST['name'], intval(@$_POST['is_def']), intval($_POST['id'])));
	}
	
	if(!empty($_POST['del'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM $groups_table WHERE ID=%d",intval($_POST['id'])));
	}
	
	if(!empty($_POST['signup_options'])) {
		update_option('watupro_select_group_on_signup', $_POST['select_group_on_signup']); 
	}
	
	// select current groups
	$groups = $wpdb->get_results("SELECT * FROM $groups_table ORDER BY name");
	
	$use_wp_roles = get_option('watupro_use_wp_roles');	
	
	if(@file_exists(get_stylesheet_directory().'/watupro/groups.php')) require get_stylesheet_directory().'/watupro/groups.php';
	else require WATUPRO_PATH."/views/groups.php";
}

// registers the default groups for everyone, not just for students
// this is required because admin may want to allow other roles also take exams	
// use this function also for setting up default difficulty level
function watupro_register_group($user_id) {
	global $wpdb;
	$groups_table = $wpdb->prefix."watupro_groups";		
		
	// any default groups?
	$groups=$wpdb->get_results("SELECT * FROM $groups_table WHERE is_def=1");
	$gids=array();
	foreach($groups as $group) $gids[]=$group->ID;
	
	// selected group?
	if(!empty($_POST['watupro_user_group']) and get_option('watupro_select_group_on_signup') == 1) {
		$gids[] = $_POST['watupro_user_group'];
	}
	
	// update_user_meta($user_id, "watupro_groups", $gids);
	watupro_assign_groups($user_id, $gids);
	
	// set default difficulty levels
	$user_diff_levels = get_option('watupro_default_user_diff_levels');
	if(!empty($user_diff_levels)) {
		update_user_meta($user_id, "watupro_difficulty_levels", $user_diff_levels);
	}
} // end watupro_register_group


// user profile custom fields functions
// http://wordpress.stackexchange.com/questions/4028/how-to-add-custom-form-fields-to-the-user-profile-page#4029
function watupro_user_fields($user) {
	global $wpdb;

    if(!current_user_can(WATUPRO_MANAGE_CAPS)) return false;

	$groups_table=$wpdb->prefix."watupro_groups";		
	
	$groups=$wpdb->get_results("SELECT * FROM $groups_table ORDER BY name");
	
	$user_groups = get_user_meta(@$user->ID, "watupro_groups", true);
	if(!is_array($user_groups)) $user_groups = array($user_groups);
	?>
	<h3><?php _e("Watu PRO Fields", 'watupro'); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="phone"><?php _e("User Groups", 'watupro'); ?></label></th>
      <td>
      	<select name="watupro_groups[]" multiple="multiple" size="4">
      	<option>-------------------</option>
      	<?php foreach($groups as $group):
      	if(@in_array($group->ID, $user_groups)) $selected="selected";
      	else $selected="";?>
      		<option value="<?php echo $group->ID?>" <?php echo $selected;?>><?php echo $group->name?></option>
      	<?php endforeach;?>
      	</select> 
    </td>
    </tr>
  
	<?php	
	// if question difficulty level restrictions are applied
	if(get_option('watupro_apply_diff_levels') == '1') {
		// are there any diff levels?
		$diff_levels = stripslashes(get_option('watupro_difficulty_levels'));
		$user_diff_levels = get_user_meta($user->ID, "watupro_difficulty_levels", true);
		// print_r($user_diff_levels);
		if(!empty($diff_levels)) {
			$diff_levels = explode(PHP_EOL, $diff_levels);
			?>
			 <tr>
		      <th><label for="phone"><?php _e("Accessible difficulty levels", 'watupro'); ?></label></th>
		      <td>
		      	<select name="watupro_diff_levels[]" multiple="multiple" size="4">
		      	<option>-------------------</option>
		      	<?php foreach($diff_levels as $level):
		      	$level = trim($level);
		      	if(@in_array($level, $user_diff_levels)) $selected="selected";
		      	else $selected="";?>
		      		<option value="<?php echo $level?>" <?php echo $selected;?>><?php echo $level?></option>
		      	<?php endforeach;?>
		      	</select> 
		    </td>
		    </tr>
			<?php 
		}
	}	
	?>
	</table>
	<?php 	
} // watupro_user_fields()

function watupro_save_extra_user_fields($user_id) {
  $saved = false;  
  if (defined('WATUPRO_MANAGE_CAPS') and current_user_can( WATUPRO_MANAGE_CAPS ) ) {
    //update_user_meta( $user_id, 'watupro_groups', watupro_int_array(@$_POST['watupro_groups']) );
    watupro_assign_groups($user_id, watupro_int_array(@$_POST['watupro_groups']));
	 update_user_meta( $user_id, 'watupro_difficulty_levels', @$_POST['watupro_diff_levels'] );
    $saved = true;
  }
  return true;
}

function watupro_group_field() {
    global $wpdb;
    
    if(get_option('watupro_select_group_on_signup') != '1') return "";
    
    // select user groups
    $groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
    if(!count($groups)) return '';
    ?>
    <p><label><?php _e('User Group:', 'watupro')?></label></p>
    <p><select name="watupro_user_group" class="input">
    	<?php foreach($groups as $group):?>
    		<option value="<?php echo $group->ID?>" <?php if(!empty($_GET['watupro_group_id']) and $_GET['watupro_group_id'] == $group->ID) echo 'selected';?>><?php echo $group->name;?></option>
    	<?php endforeach;?>
    </select></p>
    <?php
}

// get user groups as array - appropriate for Ultimate Member and maybe other plugins
function watupro_get_user_groups() {
    global $wpdb;
   
    $groups = $wpdb->get_results("SELECT ID, name FROM ".WATUPRO_GROUPS." ORDER BY name");
    if(!count($groups)) return '';
    
    $user_groups = array();
    foreach($groups as $group) {
    	 $user_groups[$group->ID] = $group->name;
    }
    
    return $user_groups;
}

function watupro_group_assign() {
	global $wpdb, $wp_roles;
	$roles = $wp_roles->roles;
	
	// select group
	$group = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GROUPS." WHERE ID=%d", intval($_GET['group_id'])));
	
	// filters
	$filters_sql = $filters_url = "";
	
	if(!empty($_GET['email'])) {
		$_GET['email'] = sanitize_text_field($_GET['email']);  
		switch($_GET['emailf']) {
			case 'contains': $like = "%$_GET[email]%"; break;
			case 'starts': $like = "$_GET[email]%"; break;
			case 'ends': $like = "%$_GET[email]"; break;
			case 'equals':
			default: $like = $_GET['email']; break;			
		}
		
		$filters_sql .= $wpdb->prepare(" AND user_email LIKE %s ", $like);
		$filters_url .= "&email=".esc_attr($_GET['email'])."&emailf=".esc_attr($_GET['emailf']);
	}
	
	if(!empty($_GET['username'])) {
		$_GET['username'] = sanitize_text_field($_GET['username']);  
		switch($_GET['usernamef']) {
			case 'contains': $like = "%$_GET[username]%"; break;
			case 'starts': $like = "$_GET[username]%"; break;
			case 'ends': $like = "%$_GET[username]"; break;
			case 'equals':
			default: $like = $_GET['username']; break;			
		}
		
		$filters_sql .= $wpdb->prepare(" AND user_login LIKE %s ", $like);
		$filters_url .= "&username=".esc_attr($_GET['username'])."&usernamef=".esc_attr($_GET['usernamef']);
	}	
	
	$role_join_sql = '';
	if(!empty($_GET['role'])) {
		$_GET['role'] = sanitize_text_field($_GET['role']);
		$blog_prefix = $wpdb->get_blog_prefix();
		$role_join_sql = "JOIN {$wpdb->usermeta} tUM ON tUM.user_id = tU.ID 
			AND tUM.meta_key = '{$blog_prefix}capabilities' AND tUM.meta_value LIKE '%:".'"'.$_GET['role'].'"'.";%'";
	}
	
	// select all users, alphabetic sorting, 100 per page
	$per_page = 100;
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	$ob = empty($_GET['ob']) ? 'user_login' : sanitize_text_field($_GET['ob']);
	$dir = empty($_GET['dir']) ? 'desc' : sanitize_text_field($_GET['dir']);	
	
	$users = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->users} tU
		$role_join_sql 
		WHERE tU.ID>0 $filters_sql ORDER BY $ob $dir LIMIT $offset, $per_page");	
	$cnt_users = $wpdb->get_var("SELECT FOUND_ROWS()"); 	

	// assign users
	if(!empty($_POST['assign'])) {
		$_POST['uids'] = empty($_POST['uids']) ? array() : $_POST['uids'];		
		foreach($users as $user) {
			$user_groups=get_user_meta($user->ID, "watupro_groups", true);
			if(!is_array($user_groups)) $user_groups = array($user_groups);
			
			// group is not yet assigned but must be
			if(in_array($user->ID, $_POST['uids']) and !@in_array($group->ID, $user_groups)) {
				$user_groups[] = $group->ID;
				//update_user_meta($user->ID, "watupro_groups", $user_groups);
				watupro_assign_groups($user->ID, $user_groups);
				
			}
			
			// group was assigned but must be  not
			if(!in_array($user->ID, $_POST['uids']) and @in_array($group->ID, $user_groups)) {
				foreach($user_groups as $cnt=>$gid) {
					if($gid == $group->ID) unset($user_groups[$cnt]); 
				}
				//update_user_meta($user->ID, "watupro_groups", $user_groups);
				watupro_assign_groups($user->ID, $user_groups);
			}
		}
	} // end assigning
	
	include(WATUPRO_PATH . "/views/group-users.html.php");	
}

// show filter for Namaste! LMS
function watupro_namaste_show_students_filter() {
	global $wpdb;
	
	$use_wp_roles = get_option('watupro_use_wp_roles');
	if($use_wp_roles) return '';
	
	$groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	if(!count($groups)) return '';
	
	echo "<p>".__('Filter by WatuPRO user group:', 'watupro').' <select name="watupro_group_id" onchange="this.form.submit();">
		<option value=0>'.__('- Any group -', 'watupro').'</option>';
	foreach($groups as $group) {
		$selected = (!empty($_GET['watupro_group_id']) and $_GET['watupro_group_id'] == $group->ID) ? ' selected' : '';
		echo '<option value="'.$group->ID.'"'.$selected.'>' . stripslashes($group->name). '</option>';
	}	
	echo "</select></p>";	
}

// apply filter for Namaste! LMS
function watupro_namaste_students_filter($filter_sql) {
	global $wpdb;
	if(!empty($_GET['watupro_group_id'])) {
		$students = $wpdb->get_results($wpdb->prepare("SELECT tU.ID as ID
			 		FROM {$wpdb->users} tU JOIN ".NAMASTE_STUDENT_COURSES." tS 
			 		ON tS.user_id = tU.ID AND tS.course_id=%d ", $_GET['course_id']));
		$uids = array(0);
		foreach($students as $student) {
			$user_groups = get_user_meta($student->ID, "watupro_groups", true);
			if(!is_array($user_groups)) $user_groups = array($user_groups);
			if(@in_array($_GET['watupro_group_id'], $user_groups)) $uids[] = $student->ID;
		}	 	
		
		$filter_sql .= " AND tU.ID IN (".implode(',', $uids).") ";	
	}
	
	return $filter_sql;
}

// show extra th in Namaste! Students page
function watupro_namaste_students_extra_th() {
	global $wpdb;
	
	$use_wp_roles = get_option('watupro_use_wp_roles');
	if($use_wp_roles) return '';
	
	$groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	if(!count($groups)) return '';
	
	echo '<th>'.sprintf(__('%s Group (WatuPRO)', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)).'</th>';
} // end extra_th

// show extra td in Namaste! Students page
function watupro_namaste_students_extra_td($student) {
	global $wpdb;
	
	$use_wp_roles = get_option('watupro_use_wp_roles');
	if($use_wp_roles) return '';
	
	// echo '<td>';
	$groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	//if(!count($groups)) return '</td>';
	if(!count($groups)) return '';
	
	echo '<td>';
	
	$user_groups = get_user_meta($student->ID, "watupro_groups", true);
	if(empty($user_groups)) return '';
	$groups_str = '';
	foreach($user_groups as $cnt => $gid) {
		foreach($groups as $group) {
			if($gid == $group->ID) {
				if($cnt) $groups_str .= ', ';
				$groups_str .= stripslashes($group->name);
			}
		}
	} // end constructing groups_str
	
	echo $groups_str.'</td>';
} // end extra_th

// this function does two things: assigns user group in wp_usermeta field AND into the new watupro_users_groups table.
// the table is required so we can make queries to find users within the same groups
// @param $user_id INT the user's ID
// @param $user_groups array of user group IDs
function watupro_assign_groups($user_id, $user_groups) {
	global $wpdb;
	if(function_exists('watupro_int_array')) $user_groups = watupro_int_array($user_groups);
	
	$old_user_groups = get_user_meta($user_id, 'watupro_groups', true);
	
	update_user_meta($user_id, "watupro_groups", $user_groups);
	
	foreach($user_groups as $group_id) {
		// add only new groups
		if(is_array($old_user_groups) and in_array($group_id, $old_user_groups)) continue;
		$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_users_groups SET user_id=%d, group_id=%d",
			$user_id, $group_id));
	}
	
	// delete any non included groups
	if(empty($user_groups) or !count($user_groups)) $user_groups = array(0);
	
	$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}watupro_users_groups WHERE user_id=%d AND group_id NOT IN (" .implode(',', $user_groups). ")", $user_id));
	
} // end watupro_assign_groups()

// this function runs only once from watupro_activate to resolve the old stored user groups
function watupro_copy_user_groups() {
	global $wpdb;
	
	// select all users that have user groups 
	$users = $wpdb->get_results("SELECT tU.ID as ID, tM.meta_value as groups FROM {$wpdb->users} tU
		JOIN {$wpdb->usermeta} tM ON tM.user_id = tU.ID and tM.meta_key = 'watupro_groups' ORDER BY ID");
		
	foreach($users as $user) {
		$groups = unserialize($user->groups);
		
		if(is_array($groups) and count($groups)) {
			$sql = "INSERT INTO ".WATUPRO_USERS_GROUPS." (user_id, group_id) VALUES ";
			
			foreach($groups as $cnt => $group_id) {
				$group_id = intval($group_id);				
				if($cnt > 0) $sql .=", ";
				$sql .= $wpdb->prepare("(%d, %d)", $user->ID, $group_id);
			}
			
			$wpdb->query($sql);
		} // end inserting
	}	// end foreach user
} // end watupro_copy_user_groups

// return the user IDs from the same user group or user role
function watupro_same_groups_uids($user_id) {
	global $wpdb;
	
	if(!watupro_intel()) return null;
	
	$use_wp_roles = get_option('watupro_use_wp_roles');
	
	if($use_wp_roles == 1) {
		$user_meta = get_userdata($user_id);
		$user_roles = $user_meta->roles;
		$uids = array(0);
		foreach($user_roles as $role) {
			$args = array(
			    'role'    => $role,
			    'orderby' => 'user_nicename',
			    'order'   => 'ASC'
			);
			$users = get_users( $args );
			foreach($users as $u) {
				if(!in_array($u->ID, $uids)) $uids[] = $u->ID;
			}
		} // end foreach role
	}
	else {
		// watupro user groups
		$user_groups = get_user_meta($user_id, 'watupro_groups', true);
		if(empty($user_groups) or !count($user_groups)) return null;
		$user_groups = watupro_int_array($user_groups); // just in case. Not supposed to be required.
		
		$users = $wpdb->get_results("SELECT DISTINCT(user_id) FROM ".WATUPRO_USERS_GROUPS." WHERE group_id IN (".implode(',', $user_groups).")");
		$uids = array(0);
		foreach($users as $u) $uids[] = $u->user_id;
	}
	
	return $uids;
} // end watupro_same_groups_uids()