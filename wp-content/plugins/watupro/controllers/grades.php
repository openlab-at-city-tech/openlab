<?php
// manage grades 
class WTPGrades {
	static function copy_default($exam) {
		global $wpdb;
		
		// select default grades given the % or point based system
		$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES."
			WHERE exam_id = 0 AND percentage_based = %d AND is_cumulative_grade=0 ORDER BY ID", $exam->grades_by_percent));
		
		// copy the grades - don't copy duplicates
		foreach($grades as $grade) {
			$exists = $wpdb -> get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_GRADES." 
				WHERE exam_id=%d AND gtitle=%s AND cat_id=%d AND percentage_based=%d",
				$exam->ID, stripslashes($grade->gtitle), $grade->cat_id, $exam->grades_by_percent));
				
			if(!$exists) {
				$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_GRADES." SET
					exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%s, gto=%s, certificate_id=%d, cat_id=%d, 
					redirect_url=%s, moola=%d",
					$exam->ID, stripslashes($grade->gtitle), stripslashes($grade->gdescription), 
					$grade->gfrom, $grade->gto, $grade->certificate_id, $grade->cat_id, $grade->redirect_url, $grade->moola));
			}	
		}	
		
		// set the quiz to not use default grades now		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET reuse_default_grades=0 WHERE ID=%d", $exam->ID));
	} // end copy default grades
} // end class

function watupro_grades() {
	global $wpdb, $user_ID;
	$in_default_grades = false;	
	$_GET['quiz'] = intval($_GET['quiz']);
	
	// check access
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(__('You can only manage the grades on your own quizzes.','watupro'));
	}	
	if($multiuser_access == 'group') {
		$cat_ids = WTPCategory::user_cats($user_ID);
		$cat_id_sql=implode(",",$cat_ids);
		$allowed_to_edit = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_EXAMS." 
			WHERE cat_id IN ($cat_id_sql) AND ID=%d", $_GET['quiz']));
		if(!$allowed_to_edit) wp_die(__('You can only manage the grades of quizzes within your allowed categories', 'watupro'));					
	}		
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die(__("You are not allowed to do this", 'watupro'));	
	
	// reuse default grades?
	if(!empty($_POST['set_reuse_default_grades']) and check_admin_referer('watupro_grades')) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET reuse_default_grades = %d WHERE ID = %d",
			intval(@$_POST['reuse_default_grades']), $_GET['quiz']));
	}
	
	if(!empty($_POST['set_final_grade_depends']) and check_admin_referer('watupro_grades')) {
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));	
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		$advanced_settings['final_grade_depends_on_cats'] = empty($_POST['final_grade_depends_on_cats']) ? 0 : 1;
		$advanced_settings['calculate_dependent_ignore_empty_cats'] = empty($_POST['calculate_dependent_ignore_empty_cats']) ? 0 : 1;
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET advanced_settings=%s WHERE ID=%d",
			serialize($advanced_settings), intval($_GET['quiz'])));
	}
	
	if(!empty($_GET['copy_default_grades'])) {	
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));	
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		WTPGrades :: copy_default($exam);
		watupro_redirect("admin.php?page=watupro_grades&quiz=".$_GET['quiz']);
	}

	// change the common gradecat design	
	if(!empty($_POST['save_design']) and check_admin_referer('watupro_gradecat_design')) {
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));	
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		
		$advanced_settings['gradecat_order'] = sanitize_text_field($_POST['gradecat_order']);		
		$advanced_settings['gradecat_limit'] = intval($_POST['gradecat_limit']);	
		$advanced_settings['exclude_survey_from_catgrades'] = empty($_POST['exclude_survey_from_catgrades']) ? 0 : 1;
		$advanced_settings['always_calculate_catgrades'] = empty($_POST['always_calculate_catgrades']) ? 0 : 1;
		$advanced_settings['sum_subcats_catgrades'] = empty($_POST['sum_subcats_catgrades']) ? 0 : 1;
		$advanced_settings['subcats_catgrades_include'] = empty($_POST['subcats_catgrades_include']) ? 0 : 1;
		$advanced_settings['sort_catgrades_by_points'] = empty($_POST['sort_catgrades_by_points']) ? 0 : 1;
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET gradecat_design=%s, advanced_settings=%s 
			WHERE id=%d", watupro_strip_tags($_POST['gradecat_design']), serialize($advanced_settings), $_GET['quiz']));
	}
	
	// select this exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	// need to assign default gradecat design?
	if(empty($exam->gradecat_design)) {
		$gradecat_design="<p>".__('For category <strong>%%CATEGORY%%</strong> you got grade <strong>%%GTITLE%%</strong>.', 'watupro')."</p>
		<p>%%GDESC%%</p><hr>";
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET gradecat_design=%s WHERE id=%d", $gradecat_design, $exam->ID));
		
		$exam->gradecat_design = $gradecat_design;
	}
	
	// select question categories
	$exam_sql = '';
	if(!empty($exam->ID)) {
		$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
		$exam_sql = " AND tQ.exam_id IN ($q_exam_id) ";
	}

	// let's select  only cats that have some questions in them
	$cats = $wpdb->get_results("SELECT tC.* FROM ".WATUPRO_QCATS." tC
		JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.cat_id=tC.ID $exam_sql
		WHERE tC.name!='' GROUP BY tC.ID ORDER BY tC.name"); 
		
	// shall we add parent categories?
	if(!empty($advanced_settings['sum_subcats_catgrades'])) {		
		$final_cats = array();
		$final_cat_ids = array();
		foreach($cats as $cat) {
			$final_cat_ids[] = $cat->ID;
			if($cat->parent_id and !in_array($cat->parent_id, $final_cat_ids)) {
				$parent = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $cat->parent_id));
				if(empty($parent->ID)) continue;
				$final_cats[] = $parent;
				$final_cat_ids[] = $parent->ID;
			}
			// after possibly assigning the parent, add the cat to $final_cats
			$final_cats[] = $cat;
		} // end foreach cats
		$cats = $final_cats;
	}	// end adding empty parent categories when summing up subcategory performance is selected
	
	// sanitize grade vars
	if(!empty($_POST['add']) or !empty($_POST['save'])) {
		$_POST['gtitle'] = sanitize_text_field($_POST['gtitle']);
		$_POST['gdescription'] = wp_encode_emoji(watupro_strip_tags(@$_POST['gdescription']));
		$_POST['gfrom'] = floatval($_POST['gfrom']);
		$_POST['gto'] = floatval($_POST['gto']);
		$_POST['certificate_id'] = intval(@$_POST['certificate_id']);
		$_POST['cat_id'] = intval($_POST['cat_id']);
		$_POST['redirect_url'] = esc_url_raw($_POST['redirect_url']);
		$_POST['moola'] = intval(@$_POST['moola']);
	}
	
	$cat_id = empty($_POST['cat_id']) ? 0 : intval($_POST['cat_id']);
	
	// prepare per-category requirements if such are selected
	$catreqs = '';
	if((!empty($_POST['add']) or !empty($_POST['save'])) 
			and !empty($advanced_settings['final_grade_depends_on_cats']) and empty($exam->reuse_default_grades) and count($cats) and empty($cat_id)) {
				
		$catreqs = array();
		foreach($cats as $cat) {
			$catreqs[$cat->ID]['from'] = $_POST['from_'.$cat->ID];
			$catreqs[$cat->ID]['to'] = $_POST['to_'.$cat->ID];
		}
		$catreqs = serialize($catreqs);
	}
	
	if(!empty($_POST['add']) and check_admin_referer('watupro_grade')) {
		
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_GRADES." SET
			exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%s, gto=%s, certificate_id=%d, cat_id=%d, redirect_url=%s, 
			moola=%d, category_requirements=%s",
			$exam->ID, $_POST['gtitle'], $_POST['gdescription'], $_POST['gfrom'], $_POST['gto'], @$_POST['certificate_id'], 
			$_POST['cat_id'], $_POST['redirect_url'], $_POST['moola'], $catreqs));
	}
	
	if(!empty($_POST['del']) and check_admin_referer('watupro_grade')) {
		$_POST['id'] = intval($_POST['id']);
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_GRADES." WHERE ID=%d", $_POST['id']));
	}
	
	if(!empty($_POST['save']) and check_admin_referer('watupro_grade')) {
		$_POST['id'] = intval($_POST['id']);
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_GRADES." SET
			gtitle=%s, gdescription=%s, gfrom=%s, gto=%s, certificate_id=%d, redirect_url=%s, moola=%d, category_requirements=%s
			WHERE ID=%d",
			$_POST['gtitle'], watupro_strip_tags($_POST['gdescription'.$_POST['id']]), $_POST['gfrom'], $_POST['gto'], 
			@$_POST['certificate_id'], $_POST['redirect_url'], $_POST['moola'], $catreqs, $_POST['id']));
	}
	
	// select all grades of the selected category
	$grades = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." 
	     WHERE exam_id=%d AND cat_id=%d AND is_cumulative_grade=0", $exam->ID, $cat_id) );
	
	// for the moment certificates will be used only on non-category grades	
	if(!$cat_id) {	
		// select certificates if any
		$certificates = $wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE is_multi_quiz=0 ORDER BY title");
		$cnt_certificates = sizeof($certificates);
	}	
	
	$integrate_moolamojo = false;
	if(!empty($advanced_settings['transfer_moola']) and $advanced_settings['transfer_moola_mode'] == 'grades') $integrate_moolamojo = true;
	
	if(@file_exists(get_stylesheet_directory().'/watupro/grades.php')) require get_stylesheet_directory().'/watupro/grades.php';
	else require WATUPRO_PATH."/views/grades.php";
}

// mnanage default grades
function watupro_default_grades() {
	global $wpdb, $user_ID;
	$in_default_grades = true;	
	$percentage_based = intval(@$_GET['percentage_based']);	
	$exam = (object)array("ID"=>0, "name"=>"", "grades_by_percent"=>$percentage_based);
	
	// check access
	$multiuser_access = 'all';
	$userid_sql = '';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' 
	  or $multiuser_access == 'own' or $multiuser_access == 'group' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die(__("You are not allowed to do this", 'watupro'));	
	
	// change the common gradecat design	
	if(!empty($_POST['save_design']) and $multiuser_access == 'all') {
		update_option('watupro_gradecat_design', $_POST['gradecat_design']);
		update_option('watupro_gradecat_order', $_POST['gradecat_order']);
	}
	
	// prepare the default gradecat design
	$gradecat_design = get_option('watupro_gradecat_design');	
	if(empty($gradecat_design)) {		
		$gradecat_design="<p>".__('For category <strong>%%CATEGORY%%</strong> you got grade <strong>%%GTITLE%%</strong>.', 'watupro')."</p>
			<p>%%GDESC%%</p><hr>";
		update_option('watupro_gradecat_design', $gradecat_design);
	}
	$advanced_settings = array();
	$advanced_settings['gradecat_order'] = get_option('watupro_gradecat_order');	
	
	// select question categories
	$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE name!='' ORDER BY name"); 
	
	// sanitize grade vars
	if(!empty($_POST['add']) or !empty($_POST['edit'])) {
		$_POST['gtitle'] = sanitize_text_field($_POST['gtitle']);
		$_POST['gdescription'] = wp_encode_emoji(watupro_strip_tags($_POST['gdescription']));
		$_POST['gfrom'] = floatval($_POST['gfrom']);
		$_POST['gto'] = floatval($_POST['gto']);
		$_POST['certificate_id'] = intval(@$_POST['certificate_id']);
		$_POST['cat_id'] = intval($_POST['cat_id']);
		$_POST['redirect_url'] = esc_url_raw($_POST['redirect_url']);
		$_POST['moola'] = intval(@$_POST['moola']);
	}
	
	if(!empty($_POST['add']) and check_admin_referer('watupro_grade')) {		
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_GRADES." SET
			exam_id=0, gtitle=%s, gdescription=%s, gfrom=%s, gto=%s, certificate_id=%d, 
			cat_id=%d, editor_id=%d, percentage_based=%d, redirect_url=%s",
			$_POST['gtitle'], $_POST['gdescription'], $_POST['gfrom'], $_POST['gto'], @$_POST['certificate_id'], 
			$_POST['cat_id'], $user_ID, $percentage_based, $_POST['redirect_url']));
	}
	
	if(!empty($_POST['del']) and check_admin_referer('watupro_grade')) {		
		$_POST['id'] = intval($_POST['id']);
		if($multiuser_access == 'own') $userid_sql = $wpdb->prepare(" AND editor_id=%d ", $user_ID);
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_GRADES." WHERE ID=%d $userid_sql", $_POST['id']));
	}
	
	if(!empty($_POST['save']) and check_admin_referer('watupro_grade')) {
		$_POST['id'] = intval($_POST['id']);
		if($multiuser_access == 'own') $userid_sql = $wpdb->prepare(" AND editor_id=%d ", $user_ID);
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_GRADES." SET
			gtitle=%s, gdescription=%s, gfrom=%s, gto=%s, certificate_id=%d, redirect_url=%s
			WHERE ID=%d $userid_sql",
			$_POST['gtitle'], $_POST['gdescription'.$_POST['id']], $_POST['gfrom'], $_POST['gto'], 
			@$_POST['certificate_id'], $_POST['redirect_url'], $_POST['id']));
	}
	
	$cat_id = empty($_POST['cat_id'])?0:$_POST['cat_id'];
	
	// select all grades of the selected category
	$grades = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." 
		WHERE exam_id=0 AND cat_id=%d AND percentage_based=%d AND is_cumulative_grade=0 ", $cat_id, $percentage_based) );
	
	// for the moment certificates will be used only on non-category grades	
	if(!$cat_id) {	
		// select certificates if any
		$certificates = $wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." ORDER BY title");
		$cnt_certificates = sizeof($certificates);
	}	
	
	if(@file_exists(get_stylesheet_directory().'/watupro/grades.php')) require get_stylesheet_directory().'/watupro/grades.php';
	else require WATUPRO_PATH."/views/grades.php";
} // end default grades