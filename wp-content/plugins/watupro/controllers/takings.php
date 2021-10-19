<?php
function watupro_takings($in_shortcode = false, $atts = null) {	
	global $wpdb, $wp_roles, $user_ID, $post;
	$roles = $wp_roles->roles;	
	$multiuser_access = 'all';
	if(empty($atts['public']) and watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	// select user groups
	$groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	
	// select quiz categories
	// categories if any
	$quiz_cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=0 ORDER BY name");
	$subs = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id!=0 ORDER BY name");
	// match cats & subs
	foreach($quiz_cats as $cnt => $cat) {
		$cat_subs = array();
		foreach($subs as $sub) {
			if($sub->parent_id == $cat->ID) $cat_subs[] = $sub;
		}
		$quiz_cats[$cnt] -> subs = $cat_subs;
	}
	
	// if Namaste! LMS is installed we'll also select courses
	if(class_exists('NamasteLMSCourseModel')) {
		$_course = new NamasteLMSCourseModel();
		$namaste_courses = $_course->select();
	}
	
	if($in_shortcode) {
		$_GET['exam_id'] = intval(@$atts['quiz_id']);
		
		if(!empty($atts['orderby']) and empty($_GET['ob'])) $_GET['ob'] = $atts['orderby'];
		if(!empty($atts['dir']) and empty($_GET['dir'])) $_GET['dir'] = $atts['dir'];
		if(!empty($atts['taker_name']) and empty($_GET['dn'])) $_GET['dn'] = $atts['taker_name'];
		if(!empty($atts['taker_name_filter']) and empty($_GET['dnf'])) $_GET['dnf'] = $atts['taker_name_filter'];
		if(!empty($atts['email']) and empty($_GET['email'])) $_GET['email'] = $atts['email'];
		if(!empty($atts['email_filter']) and empty($_GET['emailf'])) $_GET['emailf'] = $atts['email_filter'];
		if(!empty($atts['company']) and empty($_GET['field_company'])) $_GET['field_company'] = $atts['company'];
		if(!empty($atts['company_filter']) and empty($_GET['companyf'])) $_GET['companyf'] = $atts['company_filter'];
		if(!empty($atts['phone']) and empty($_GET['field_phone'])) $_GET['field_phone'] = $atts['phone'];
		if(!empty($atts['phone_filter']) and empty($_GET['phonef'])) $_GET['phonef'] = $atts['phone_filter'];
		if(!empty($atts['field_1']) and empty($_GET['field_1'])) $_GET['field_1'] = $atts['field_1'];
		if(!empty($atts['field_1_filter']) and empty($_GET['field1f'])) $_GET['field1f'] = $atts['field_1_filter'];
		if(!empty($atts['field_2']) and empty($_GET['field_2'])) $_GET['field_2'] = $atts['field_2'];
		if(!empty($atts['field_2_filter']) and empty($_GET['field2f'])) $_GET['field2f'] = $atts['field_2_filter'];
		if(!empty($atts['role']) and empty($_GET['role'])) $_GET['role'] = $atts['role'];
		if(!empty($atts['ugroup']) and empty($_GET['ugroup'])) $_GET['ugroup'] = $atts['ugroup'];
		if(!empty($atts['ip']) and empty($_GET['ip'])) $_GET['ip'] = $atts['ip'];
		if(!empty($atts['ip_filter']) and empty($_GET['ipf'])) $_GET['ipf'] = $atts['if_filter'];
		if(!empty($atts['points']) and empty($_GET['points'])) $_GET['points'] = $atts['points'];
		if(!empty($atts['points_filter']) and empty($_GET['pointsf'])) $_GET['pointsf'] = $atts['points_filter'];
		if(!empty($atts['date']) and empty($_GET['date'])) $_GET['date'] = $atts['date'];
		if(!empty($atts['date2']) and empty($_GET['date2'])) $_GET['date2'] = $atts['date2'];
		if(!empty($atts['date_filter']) and empty($_GET['datef'])) $_GET['datef'] = $atts['date_filter'];
		if(!empty($atts['percent_correct']) and empty($_GET['percent_correct'])) $_GET['percent_correct'] = $atts['percent_correct'];
		if(!empty($atts['percent_correct_filter']) and empty($_GET['percentf'])) $_GET['percentf'] = $atts['percent_correct_filter'];
		if(!empty($atts['grade']) and empty($_GET['grade'])) $_GET['grade'] = $atts['grade'];
		if(!empty($atts['quiz_cat_id']) and empty($_GET['quiz_cat_id'])) $_GET['quiz_cat_id'] = $atts['quiz_cat_id'];
		if(!empty($atts['loggedin']) and empty($_GET['loggedin'])) $_GET['loggedin'] = $atts['loggedin'];
	}
	
	// shows data for a taken exam
	$ob=empty($_GET['ob']) ? "id" : sanitize_text_field($_GET['ob']);
	$dir=!empty($_GET['dir'])? $_GET['dir'] : "DESC";
	if($dir != 'ASC' and $dir != 'DESC') $dir = 'ASC';
	$odir=($dir=='ASC')?'DESC':'ASC';
	$offset = empty($_GET['offset'])? 0 : intval($_GET['offset']);
	$owner_sql = '';
	$per_page = empty($atts['per_page']) ? 10 : intval($atts['per_page']);
	if(!empty($_GET['per_page'])) $per_page = intval($_GET['per_page']);
	if(!empty($_POST['per_page'])) $per_page = intval($_POST['per_page']); // change to POST form overwrites GET
	
	$_GET['exam_id'] = intval(@$_GET['exam_id']);
	
	// class manager limitations from Namaste! PRO
	$class_manager_sql = '';
	if(class_exists('NamastePROClasses')) {
		$manager_classes = $wpdb->get_results($wpdb->prepare("SELECT tCM.class_id as class_id FROM ".NAMASTEPRO_CLASSES." tC
			JOIN ".NAMASTEPRO_CLASS_MANAGERS." tCM ON tCM.class_id = tC.id  
			WHERE tCM.user_id=%d", $user_ID));
			$manager_class_ids = array();
		foreach($manager_classes as $mc) $manager_class_ids[] = $mc->class_id;
		
		if(!empty($manager_class_ids)) {
			$class_manager_sql = " AND tU.ID IN (SELECT student_id FROM ".NAMASTEPRO_STUDENT_CLASSES." 
				WHERE class_id IN (".implode(',', $manager_class_ids).")) ";
		}
	} // end Namaste! PRO class limitations
	
	// select exam
	if(!empty($_GET['exam_id'])) {
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['exam_id']));
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	}
	
	// delete unfinished attempts
	if(!empty($_POST['delete_unfinished']) and !empty($_POST['yesiamsure']) and check_admin_referer('watupro_unfinished')) {
		// now cleanup		
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE taking_id IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=%d AND in_progress=1)", $exam->ID));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_FILES." WHERE taking_id IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=%d AND in_progress=1)", $exam->ID));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=%d AND in_progress=1", $exam->ID));
	}
	
	if(!empty($_POST['mass_delete']) and check_admin_referer('watupro_takings') and empty($atts['public']) and empty($class_manager_sql)) {
		$tids = is_array($_POST['tids']) ? watupro_int_array($_POST['tids']) : array(0);
		$tid_sql = implode(", ", $tids);
		
		$wpdb->query("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID IN ($tid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE taking_id IN ($tid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_USER_FILES." WHERE taking_id IN ($tid_sql)");
	}
		
	// check access	
	$owner_sql = '';
	if($multiuser_access == 'own') {
		if(!empty($exam->ID) and $exam->editor_id != $user_ID) wp_die(__('You can only view results on your own quizzes.','watupro'));
		$owner_sql = $wpdb->prepare(" AND tE.editor_id=%d ", $user_ID);
	}	
	if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {
		$cat_ids = WTPCategory::user_cats($user_ID);
		if(!in_array(@$exam->cat_id, $cat_ids)) wp_die('You can only view results of quizzes within your allowed categories.', 'watupro');
	}	
	
	// search/filter
	$filters = array();
	$joins = array();
	$filter_sql = $left_join_sql = $role_join_sql = $group_join_sql = $left_join = "";
	$join_sql = "LEFT JOIN {$wpdb->users} tU ON tU.ID=tT.user_id";
	
	// add filters and joins
	
	// display name
	if(!empty($_GET['dn'])) {
		$_GET['dn'] = sanitize_text_field($_GET['dn']);
		switch($_GET['dnf']) {
			case 'contains': $like="%$_GET[dn]%"; break;
			case 'starts': $like="$_GET[dn]%"; break;
			case 'ends': $like="%$_GET[dn]"; break;
			case 'equals':
			default: $like=$_GET['dn']; break;			
		}
		
		$filters[] = $wpdb->prepare(" (display_name LIKE %s OR user_login LIKE %s  OR tT.name LIKE %s) ", $like, $like, $like);
	}
	
	// email
	if(!empty($_GET['email'])) {
	   $_GET['email'] = sanitize_text_field($_GET['email']);  
		switch($_GET['emailf']) {
			case 'contains': $like="%$_GET[email]%"; break;
			case 'starts': $like="$_GET[email]%"; break;
			case 'ends': $like="%$_GET[email]"; break;
			case 'equals':
			default: $like=$_GET['email']; break;			
		}
		
		$joins[]=$wpdb->prepare(" user_email LIKE %s ", $like);
		$filters[]=$wpdb->prepare(" ((tT.user_id=0 AND email LIKE %s) OR (tT.user_id!=0 AND user_email LIKE %s)) ", $like, $like);
		$left_join = 'LEFT'; // when email is selected, do left join because it might be without logged user
	}
	
	// company field
	if(!empty($_GET['field_company'])) {
	   $_GET['field_company'] = sanitize_text_field($_GET['field_company']);
		switch($_GET['companyf']) {
			case 'contains': $like="%$_GET[field_company]%"; break;
			case 'starts': $like="$_GET[field_company]%"; break;
			case 'ends': $like="%$_GET[field_company]"; break;
			case 'equals':
			default: $like=$_GET['field_company']; break;			
		}
		
		$filters[]=$wpdb->prepare(" field_company LIKE %s ", $like);
	}
	
	// phone field
	if(!empty($_GET['field_phone'])) {
	   $_GET['field_phone'] = sanitize_text_field($_GET['field_phone']);
		switch($_GET['phonef']) {
			case 'contains': $like="%$_GET[field_phone]%"; break;
			case 'starts': $like="$_GET[field_phone]%"; break;
			case 'ends': $like="%$_GET[field_phone]"; break;
			case 'equals':
			default: $like=$_GET['field_phone']; break;			
		}
		
		$filters[]=$wpdb->prepare(" field_phone LIKE %s ", $like);
	}
	
	// custom field 1
	if(!empty($_GET['field_1'])) {
	   $_GET['field_1'] = sanitize_text_field($_GET['field_1']);
		switch($_GET['field1f']) {
			case 'contains': $like="%$_GET[field_1]%"; break;
			case 'starts': $like="$_GET[field_1]%"; break;
			case 'ends': $like="%$_GET[field_1]"; break;
			case 'equals':
			default: $like = $_GET['field_1']; break;			
		}
		
		$filters[]=$wpdb->prepare(" custom_field1 LIKE %s ", $like);
	}
	
	// custom field 2
	if(!empty($_GET['field_2'])) {
	   $_GET['field_2'] = sanitize_text_field($_GET['field_2']);
		switch($_GET['field2f']) {
			case 'contains': $like = "%$_GET[field_2]%"; break;
			case 'starts': $like = "$_GET[field_2]%"; break;
			case 'ends': $like = "%$_GET[field_2]"; break;
			case 'equals':
			default: $like = $_GET['field_2']; break;			
		}
		
		$filters[] = $wpdb->prepare(" custom_field2 LIKE %s ", $like);
	}
	
	
	// WP user role - when selected role the join always becomes right join
	if(!empty($_GET['role'])) {
		$_GET['role'] = sanitize_text_field($_GET['role']);
		$left_join = '';
		$blog_prefix = $wpdb->get_blog_prefix();
		$role_join_sql = "JOIN {$wpdb->usermeta} tUM ON tUM.user_id = tU.ID 
			AND tUM.meta_key = '{$blog_prefix}capabilities' AND tUM.meta_value LIKE '%:".'"'.$_GET['role'].'"'.";%'";
	}
	
	// Watupro user group
	if(!empty($_GET['ugroup'])) {
		$_GET['ugroup'] = sanitize_text_field($_GET['ugroup']);
		$left_join = '';		
		$group_join_sql = "JOIN {$wpdb->usermeta} tUM2 ON tUM2.user_id = tU.ID 
			AND tUM2.meta_key = 'watupro_groups' AND tUM2.meta_value LIKE '%:".'"'.$_GET['ugroup'].'"'.";%'";
	}
	else $group_join_sql = "LEFT JOIN {$wpdb->usermeta} tUM2 ON tUM2.user_id = tU.ID 
			AND tUM2.meta_key = 'watupro_groups' ";
	
	// IP
	if(!empty($_GET['ip'])) {
		$_GET['ip'] = sanitize_text_field($_GET['ip']);
		switch($_GET['ipf']) {
			case 'contains': $like="%$_GET[ip]%"; break;
			case 'starts': $like="$_GET[ip]%"; break;
			case 'ends': $like="%$_GET[ip]"; break;
			case 'equals':
			default: $like=$_GET['ip']; break;			
		}
		
		$filters[]=$wpdb->prepare(" ip LIKE %s ", $like);
	}
	
	// Date
	if(!empty($_GET['date'])) {
		$_GET['date'] = sanitize_text_field($_GET['date']);
		$_GET['date2'] = sanitize_text_field($_GET['date2']);
		switch($_GET['datef']) {
			case 'after': $filters[]=$wpdb->prepare(" date>%s ", $_GET['date']); break;
			case 'before': $filters[]=$wpdb->prepare(" date<%s ", $_GET['date']); break;
			case 'range': $filters[]=$wpdb->prepare(" date >= %s AND date <= %s ", $_GET['date'], $_GET['date2']); break; 
			case 'equals':
			default: $filters[]=$wpdb->prepare(" date=%s ", $_GET['date']); break;
		}
	}
	
	// Points
	if(!empty($_GET['points'])) {
		if(!is_numeric($_GET['points'])) $_GET['points'] = 0;
		switch($_GET['pointsf']) {
			case 'less': $filters[]=$wpdb->prepare(" points<%d ", $_GET['points']); break;
			case 'more': $filters[]=$wpdb->prepare(" points>%d ", $_GET['points']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" points=%d ", $_GET['points']); break;
		}
	}
	
	// % correct
	if(!empty($_GET['percent_correct'])) {
		$_GET['percent_correct'] = intval($_GET['percent_correct']);
		switch($_GET['percentf']) {
			case 'less': $filters[]=$wpdb->prepare(" percent_correct < %d ", $_GET['percent_correct']); break;
			case 'more': $filters[]=$wpdb->prepare(" percent_correct > %d ", $_GET['percent_correct']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" percent_correct = %d ", $_GET['percent_correct']); break;
		}
	}
	
	// passed taking ID from the manage user-certificates page
	if(!empty($_GET['taking_id'])) {		
		$_GET['taking_id'] = intval($_GET['taking_id']);
		$filters[] = $wpdb->prepare(" tT.ID=%d ", intval($_GET['taking_id']));
	}
	
	// Grade
	if(!empty($_GET['grade'])) {
		$_GET['grade'] = intval($_GET['grade']);
		$filters[]=$wpdb->prepare(" grade_id=%d ", intval($_GET['grade']));
	}
	
	// source URL
	if(!empty($_GET['source_url'])) {
		$_GET['source_url'] = esc_url_raw($_GET['source_url']);
		$filters[] = $wpdb->prepare(" source_url=%s ", $_GET['source_url']);
	}
	
	// logged in or guest	
	if(!empty($_GET['loggedin'])) {
		if($_GET['loggedin'] == 'yes') $filters[] = " tT.user_id!=0 "; 
		if($_GET['loggedin'] == 'no') $filters[] = " tT.user_id=0 ";
	}
	
	// Namaste! LMS Course
	if(!empty($_GET['namaste_course_id']) and !empty($namaste_courses)) {
		// let's select here as a subquery might be slower (is it?)
		$namaste_uids = array(-1);
		$namaste_uids1 = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM ".NAMASTE_STUDENT_COURSES." 
			WHERE course_id=%d AND (status='enrolled' OR status='completed')", intval($_GET['namaste_course_id'])));
		foreach($namaste_uids1 as $nu) $namaste_uids[] = $nu->user_id;
		$namaste_uids_sql = implode(",", $namaste_uids);
		$filters[] = " tT.user_id IN ($namaste_uids_sql) ";
	}
	
	// quiz category ID
	if(!empty($_GET['quiz_cat_id']) and empty($_GET['exam_id'])) {
		$_GET['quiz_cat_id'] = intval($_GET['quiz_cat_id']);

		// if it's a parent category we have to include also subcats
		$subs	=  $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=%d ORDER BY name", $_GET['quiz_cat_id'])); 	
		
		if(count($subs)) {
			$all_quizcat_ids = array($_GET['quiz_cat_id']);
			foreach($subs as $sub) $all_quizcat_ids[] = $sub->ID;
			
			$filters[] = " tE.cat_id IN (" . implode(',', $all_quizcat_ids) . ") ";
		} 
		else $filters[] = $wpdb->prepare(" tE.cat_id=%d ", $_GET['quiz_cat_id']);
	}
	else $_GET['quiz_cat_id'] = 0;
	
	// construct filter & join SQLs
	if(count($filters)) {
		$filter_sql=" AND ".implode(" AND ", $filters);
	}
	
	if(count($joins)) {
		$join_sql=" $left_join JOIN {$wpdb->users} tU ON tU.ID=tT.user_id AND "
			.implode(" AND ", $joins);
	}
	
	$limit_sql = "LIMIT $offset,$per_page";
	if(!empty($atts['per_page']) and $atts['per_page'] == -1) $limit_sql = '';
	if(!empty($_POST['cleanup']) or !empty($_POST['blankout'])) $limit_sql = '';
	
	if(!empty($_GET['export'])) $limit_sql="";
		
	// select takings
	$exam_sql = empty($_GET['exam_id']) ? "" : $wpdb->prepare("tT.exam_id=%d AND ", $exam->ID);  
	
	if($multiuser_access == 'own') {
		$exam_sql .= $wpdb->prepare(" tE.editor_id = %d AND ", $user_ID);
	}	
	
	$multiuser_group_sql = '';
	if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {
		if(empty($cat_ids) or !is_array($cat_ids)) $cat_ids = array(0);
		$exam_sql .= " tE.cat_id IN (".implode(',', $cat_ids).") AND ";
		$owner_sql .= " AND tE.cat_id IN (".implode(',', $cat_ids).") ";
		
		// user results must also be limited only to the groups I belong to		
		// limit to the same groups/roles user IDs
		$uids = watupro_same_groups_uids($user_ID);		
		if($uids !== null) $multiuser_group_sql = " AND tT.user_id IN (". implode(',', $uids) .") ";		
	}
	
	// filter by specific answer
	$answer_sql = '';
	if(!empty($_GET['exam_id']) and !empty($_GET['filter_by_question']) and !empty($_GET['filter_question_id']) and !empty($_GET['filter_answer_id'])) {
		// select that specific choice
		if($_GET['filter_answer_id'] > 0) {
			$filter_answer = $wpdb->get_var($wpdb->prepare("SELECT answer FROM ".WATUPRO_ANSWERS." WHERE ID=%d AND question_id=%d", 
				intval($_GET['filter_answer_id']), intval($_GET['filter_question_id'])));
		}
		else $filter_answer = '';
		
		$answer_sql = $wpdb->prepare("JOIN ".WATUPRO_STUDENT_ANSWERS." tA ON tA.taking_id=tT.ID AND tA.answer=%s 
			AND tA.question_id=%d", $filter_answer, intval($_GET['filter_question_id']));				
	}
	
		
	$in_progress = empty($_GET['in_progress']) ? 0 : 1; // completed or "in progress" takings 
	$q="SELECT SQL_CALC_FOUND_ROWS tT.*, IF(tU.display_name!='', tU.display_name, tT.name) as display_name, tU.user_email as user_email,
	tU.user_login as user_login, tUM2.meta_value as user_groups, tG.gtitle as grade_title, tE.name as exam_name,
	tT.percent_points as percent_of_max
	FROM ".WATUPRO_TAKEN_EXAMS." tT JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id $owner_sql
	LEFT JOIN ".WATUPRO_GRADES." tG ON tG.ID = tT.grade_id
	$join_sql $role_join_sql $group_join_sql $answer_sql 
	WHERE $exam_sql tT.in_progress=$in_progress $filter_sql $class_manager_sql $multiuser_group_sql 
	ORDER BY $ob $dir $limit_sql";
	 		// echo $q;
	$takings=$wpdb->get_results($q);
	$count=$wpdb->get_var("SELECT FOUND_ROWS()");
	
	// this is after the select because we want to apply filters
	if((!empty($_POST['cleanup']) or !empty($_POST['blankout'])) and check_admin_referer("watupro_cleanup") and empty($atts['public']) and empty($class_manager_sql)) {
		if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this");
		if($multiuser_access == 'own' and $exam->editor_id != $user_ID) wp_die(__('You can manage only the results on exams created by you.', 'watupro'));
		
		$id_sql = $takings_id_sql = '';
		if(!empty($filter_sql)) {
			$ids = array(0);
			foreach($takings as $taking) $ids[] = $taking->ID;
			$id_sql =" AND ID IN (".implode(',', $ids).") ";
			$takings_id_sql =" AND taking_id IN (".implode(',', $ids).") ";
		}
		
		if(!empty($_POST['cleanup'])) {		
			// now cleanup
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=%d $id_sql ", $exam->ID));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE exam_id=%d $takings_id_sql ", $exam->ID));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_FILES." WHERE exam_id=%d $takings_id_sql ", $exam->ID));
		}
		
		if(!empty($_POST['blankout'])) {
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
				SET details='data removed', catgrades='data removed' WHERE exam_id=%d $id_sql ", $exam->ID));
				
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." 
				SET question_text='', snapshot='data removed' WHERE exam_id=%d $takings_id_sql", $exam->ID));	
		}		
		
		// redirect to the page
		watupro_redirect("admin.php?page=watupro_takings&exam_id=".intval($_GET['exam_id']));
	}	
	
	// select unique source URLs in this quiz
	$source_urls = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT(source_url) FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND source_url != ''", intval(@$_GET['exam_id'])));
	
	// fill user groups & category grades	
	$has_catgrades = $has_personalities = false;
	foreach($takings as $cnt => $taking) {
		if(empty($taking->user_groups) and empty($taking->catgrades_serialized)) continue;
		
		if(!empty($taking->user_groups)) {
			$ugroups = unserialize($taking->user_groups);		
			$ugroup_names = array();
			foreach($groups as $group) {
				if(in_array($group->ID, $ugroups)) $ugroup_names[] = $group->name;
			}
			
			$takings[$cnt]->user_groups = implode(', ', $ugroup_names);
		}
		
		if(!empty($taking->catgrades_serialized)) {
			$catgrades_array = unserialize(stripslashes($taking->catgrades_serialized));
			if(!empty($catgrades_array)) $has_catgrades = true;
		} 
		
		if(!empty($taking->personality_grade_ids)) {
			$personality_array = unserialize(stripslashes($taking->personality_grade_ids));			
			$taking_personalities = array();
			foreach($personality_array as $pers_id) {
				if(!isset($taking_personalities[$pers_id])) $taking_personalities[$pers_id] = 0;
				$taking_personalities[$pers_id]++; 
			}
			
			$takings[$cnt]->personalities = $taking_personalities;
			if(!empty($personality_array)) $has_personalities = true;
		}
	} // end filling user groups info
	
	// select number of in_progress takings unless we are showing them now
	if(!$in_progress and !empty($exam->ID)) {
		$num_unfinished = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE exam_id = %d AND in_progress=1", $exam->ID));
	}	
	
	if(!empty($_GET['export']) and !$in_shortcode) {
		do_action('watupro_custom_export', @$exam, $takings); // this happens often because many customers want custom export
		$_record = new WTPRecord();
		$_record -> export($takings, @$exam);
	}
		
	// grades for the dropdown	
	if(!empty($exam->ID)) $grades = WTPGrade :: get_grades($exam);	
		
	// this var will be added to links at the view
	$get_dn = empty($_GET['dn']) ? '' : esc_attr($_GET['dn']);
	$get_dnf = empty($_GET['dnf']) ? '' : esc_attr($_GET['dnf']);
	$get_email = empty($_GET['email']) ? '' : esc_attr($_GET['email']);
	$get_emailf = empty($_GET['emailf']) ? '' : esc_attr($_GET['emailf']);
	$get_companyf = empty($_GET['companyf']) ? '' : esc_attr($_GET['companyf']);
	$get_field_company = empty($_GET['field_company']) ? '' : esc_attr($_GET['field_company']);
	$get_phonef = empty($_GET['phonef']) ? '' : esc_attr($_GET['phonef']);					
	$get_field_phone = empty($_GET['field_phone']) ? '' : esc_attr($_GET['field_phone']);
	$get_ip = empty($_GET['ip']) ? '' : esc_attr($_GET['ip']);
	$get_ipf = empty($_GET['ipf']) ? '' : esc_attr($_GET['ipf']);
	$get_date = empty($_GET['date']) ? '' : esc_attr($_GET['date']);																
	$get_date2 = empty($_GET['date2']) ? '' : esc_attr($_GET['date2']);
	$get_datef = empty($_GET['datef']) ? '' : esc_attr($_GET['datef']);
	$get_points = empty($_GET['points']) ? '' : esc_attr($_GET['points']);
	$get_pointsf = empty($_GET['pointsf']) ? '' : esc_attr($_GET['pointsf']);
	$get_grade = empty($_GET['grade']) ? '' : esc_attr($_GET['grade']);
	$get_role = empty($_GET['role']) ? '' : esc_attr($_GET['role']);
	$get_ugroup = empty($_GET['ugroup']) ? '' : esc_attr($_GET['ugroup']);
	$get_quiz_cat_id = empty($_GET['quiz_cat_id']) ? '' : esc_attr($_GET['quiz_cat_id']);
	$get_percent_correct = empty($_GET['percent_correct']) ? '' : esc_attr($_GET['percent_correct']);
	$get_percentf = empty($_GET['percentf']) ? '' : esc_attr($_GET['percentf']);
	$get_source_url = empty($_GET['source_url']) ? '' : esc_attr($_GET['source_url']);
	$get_loggedin = empty($_GET['loggedin']) ? '' : esc_attr($_GET['loggedin']);
	$get_filter_by_question = empty($_GET['filter_by_question']) ? '' : esc_attr($_GET['filter_by_question']);
	$get_filter_question_id = empty($_GET['filter_question_id']) ? '' : esc_attr($_GET['filter_question_id']);
	$get_filter_answer_id = empty($_GET['filter_answer_id']) ? '' : esc_attr($_GET['filter_answer_id']);
	
	$filters_url="dn=".$get_dn."&dnf=".$get_dnf."&email=".$get_email."&emailf=".
		$get_emailf."&companyf=".$get_companyf."&field_company=".$get_field_company	.
		"&phonef=".$get_phonef."&field_phone=".$get_field_phone."&ip=".$get_ip."&ipf=".$get_ipf."&date=".$get_date.
		"&date2=".$get_date2."&datef=".$get_datef."&points=".$get_points."&pointsf=".$get_pointsf.
		"&grade=".$get_grade."&role=".$get_role."&ugroup=".$get_ugroup."&quiz_cat_id=".$get_quiz_cat_id.
		"&percent_correct=".$get_percent_correct."&percentf=".$get_percentf."&source_url=".$get_source_url.
		"&in_progress=".$in_progress."&per_page=".$per_page."&loggedin=".$get_loggedin."&filter_by_question=".$get_filter_by_question.
		"&filter_question_id=".$get_filter_question_id."&filter_answer_id=".$get_filter_answer_id;
		
	if(!empty($namaste_courses) and !empty($_GET['namaste_course_id'])) {
		$filters_url .= "&namaste_course_id=".intval($_GET['namaste_course_id']);
	}		
		
	$display_filters=(!count($filters) and !count($joins) and empty($role_join_sql) and empty($_GET['ugroup']) and empty($_GET['filter_by_question'])) ? false : true;

	$dateformat = get_option('date_format');	
	$timeformat = get_option('time_format');
	
	// if in shortcode prepare the target URL
	if($in_shortcode) {
		$permalink = get_permalink($post->ID);
		$params = array('exam_id' => @$exam->ID);
		$target_url = add_query_arg( $params, $permalink );
		if(strstr($target_url, '?')) $target_url .= '&';
		else $target_url .= '?';
	}
	else $target_url = "?page=watupro_takings&exam_id=" . @$exam->ID.'&';
	
	// select all quizzes for the dropdown
	$dd_quizzes = $wpdb->get_results("SELECT ID, name FROM ".WATUPRO_EXAMS." tE WHERE 1 $owner_sql ORDER BY name");
	
	$gdpr = get_option('watupro_gdpr');
	
	// figure out the whole test grading system so we can display it on the Grade column for information
	$grade_info = '';
	if(!empty($exam->ID)) {
		if($exam->is_personality_quiz) {
			$grade_info ='<a href="https://calendarscripts.info/watupro/grading.html?tab=personality" target="_blank">'.sprintf(__('(personality %s)', 'watupro'), WATUPRO_QUIZ_WORD).'</a>';
		}			
		else {
			if(!empty($advanced_settings['final_grade_depends_on_cats'])) {
				$grade_info = '<a href="https://blog.calendarscripts.info/test-grade-based-on-question-category-performance-watupro/" target="_blank">'.__('*Grading is based on<br> category performance', 'watupro').'</a>';
			} 
			else {
				// points, percent correct or % of points
				if($exam->grades_by_percent) {
					if(!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points') {
						$grade_info = '<a href="https://calendarscripts.info/watupro/grading.html?tab=whole#percentpoints" target="_blank">'.__('*Based on % of points', 'watupro').'</a>';
					}
					else $grade_info = '<a href="https://calendarscripts.info/watupro/grading.html?tab=whole#percent" target="_blank">'.__('*Based on % correct answers', 'watupro').'</a>';
				}
				else $grade_info = '<a href="https://calendarscripts.info/watupro/grading.html?tab=whole#points" target="_blank">'.__('*Based on points', 'watupro').'</a>';
			} // end not depend on cats	
		}	// end not personality	
	} // end defining grade info	
	
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/takings.php')) require get_stylesheet_directory().'/watupro/takings.php';
	else require WATUPRO_PATH."/views/takings.php";
}

function watupro_delete_taking() {
	global $wpdb, $user_ID;
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this"); 
	
	do_action('watupro_deleted_taking', $_GET['id']);
	
	$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE id=%d", $_GET['id']));
		
	// delete from student_answers
	$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE taking_id=%d", $_GET['id']));	
	
	// delete user files
	$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_FILES." WHERE taking_id=%d", $_GET['id']));
	
	exit;	
}

// display the user's feedback on questions
function watupro_questions_feedback() {
	global $wpdb, $wp_roles, $user_ID;
	$dateformat = get_option('date_format');
	$limit = 50;
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	
	$roles = $wp_roles->roles;	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	// select quiz
	if(!empty($_GET['quiz_id'])) $quiz = $wpdb->get_row($wpdb->prepare("SELECT ID, name FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['quiz_id'])));
	
	// check access	
	$owner_sql = '';
	if($multiuser_access == 'own') {
		if(!empty($quiz->ID) and $exam->editor_id != $user_ID) wp_die(__('You can only view results on your own quizzes.','watupro'));
		$owner_sql = $wpdb->prepare(" AND tE.editor_id=%d ", $user_ID);
	}	
	
	$cat_ids = [];
	if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {
		$cat_ids = WTPCategory::user_cats($user_ID);
		if(!empty($quiz->cat_id) and !in_array($quiz->cat_id, $cat_ids)) wp_die('You can only view results of quizzes within your allowed categories.', 'watupro');
	}	
	
	// select all feedback on this quiz
	$exam_sql = empty($quiz->ID) ? '' : $wpdb->prepare("AND tT.exam_id=%d", $quiz->ID);
	
	$multiuser_group_sql = '';
	if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {
		if(empty($cat_ids) or !is_array($cat_ids)) $cat_ids = array(0);
		$exam_sql .= " AND tE.cat_id IN (".implode(',', $cat_ids).") ";
		$owner_sql .= " AND tE.cat_id IN (".implode(',', $cat_ids).") ";
		
		$uids = watupro_same_groups_uids($user_ID);
		if($uids !== null) $multiuser_group_sql = " AND tT.user_id IN (". implode(',', $uids) .") ";
	}	
	
	
	$search_sql = $search_str = '';	
	if(!empty($_GET['search'])) {
		$search_str = sanitize_text_field($_GET['search']);
		$search_sql = $wpdb->prepare(" AND (tA.feedback LIKE %s OR tQ.question LIKE %s OR tA.answer LIKE %s) ", '%'.$search_str.'%', '%'.$search_str.'%', '%'.$search_str.'%');
	}
	
	$ob = empty($_GET['ob']) ? 'tA.ID' : sanitize_text_field($_GET['ob']);
	if(!in_array($ob, ['tA.ID', 'question', 'feedback'])) $ob = 'tA.ID';
	$dir = empty($_GET['dir']) ? 'ASC' : sanitize_text_field($_GET['dir']);
	$odir = ($dir == 'ASC') ? 'DESC' : 'ASC';
	
	if(!empty($_GET['delete']) and wp_verify_nonce($_GET['watupro_feedback_nonce'], 'delete_feedback')) {
		$check_quiz = $wpdb->get_row($wpdb->prepare("SELECT editor_id, cat_id FROM ".WATUPRO_EXAMS." tE
			JOIN ".WATUPRO_STUDENT_ANSWERS." tA ON tA.exam_id=tE.ID 
			WHERE tA.ID = %d", intval($_GET['delete'])));			
			
		if($multiuser_access == 'own' and $check_quiz->editor_id != $user_ID) wp_die("You are not allowed to do this.");
		if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {
			// $cat_ids is already figured out in the previous check
			if(!empty($check_quiz->cat_id) and !in_array($check_quiz->cat_id, $cat_ids)) wp_die("You are not allowed to do this.");
		}
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." tT SET feedback='' WHERE ID=%d $multiuser_group_sql", intval($_GET['delete']) ));
	}
		
	$feedbacks = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tA.answer as answer, tA.feedback as feedback, tA.is_correct as is_correct, 
		tT.ID as taking_id, tT.result as taking_result, tT.date as taking_date, tT.name as author, tA.ID as answer_id,
		tQ.question as question , tE.name as quiz_name, tT.user_id as user_id
		FROM ".WATUPRO_STUDENT_ANSWERS." tA 
		JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tA.taking_id = tT.ID AND tT.in_progress=0
		JOIN ".WATUPRO_QUESTIONS." tQ ON tA.question_id = tQ.ID
		JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id $owner_sql $exam_sql
		WHERE tA.feedback != '' $multiuser_group_sql $search_sql
		ORDER BY $ob $dir LIMIT $offset, $limit");
	$count = $wpdb->get_var("SELECT FOUND_ROWS()");	
	
	if(@file_exists(get_stylesheet_directory().'/watupro/questions-feedback.html.php')) require get_stylesheet_directory().'/watupro/questions-feedback.html.php';
	else require WATUPRO_PATH."/views/questions-feedback.html.php";
}