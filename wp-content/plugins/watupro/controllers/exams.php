<?php
// lists the user dashboard of exams
// @param string $passed_cat_ids - allows to pass cat IDs by a shortcode to further restrict the number of cat IDs that are used
function watupro_my_exams($passed_cat_ids = "", $orderby = "tE.ID", $status = 'all', $in_shortcode = false, $reorder_by_latest_taking = false, $atts = null) {
	global $wpdb, $user_ID, $post, $wp;	
	
	// admin can see this for every student
	if(!empty($_GET['user_id']) and current_user_can(WATUPRO_MANAGE_CAPS)) $user_id = $_GET['user_id'];
	else $user_id = $user_ID;
		
	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE ID=%d", $user_id));
	
	// delete all results of this user?
	$multiuser_access = 'all';
	if(watupro_intel() and !empty($_GET['user_id'])) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if(!empty($_GET['del_all_results']) and (current_user_can(WATUPRO_MANAGE_CAPS) or $user_id == $user_ID)) {		
		if($multiuser_access != 'all' and $user_id != $user_ID) return false;
		
		// delete all records from takings and student_answers tables
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE user_id=%d", $user->ID));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE user_id=%d", $user->ID));
		
		do_action('watupro_deleted_user_data', $user->ID);
		
		$redirect_url = ($user_id == $user_ID) ? "admin.php?page=my_watupro_exams" : "admin.php?page=my_watupro_exams&user_id=".$user_id;
		if($in_shortcode) $redirect_url = get_permalink($post->ID);
		watupro_redirect($redirect_url);
	}
			
	// select what categories I have access to 
	$current_user = wp_get_current_user();
	$cat_ids = WTPCategory::user_cats($user_id);

	if(!empty($passed_cat_ids) and $passed_cat_ids != 'ALL' and $passed_cat_ids != 'all') {
		$passed_cat_ids = explode(",", $passed_cat_ids);
		
		$cat_ids = array_intersect($cat_ids, $passed_cat_ids);
		
		// no access to any category. In this case we should make sure that the user can't access any tests.
		if(empty($cat_ids)) $cat_ids = array(-1);
	}

	$cat_id_sql = implode(",",$cat_ids);
	
	list($my_exams, $takings, $num_taken) = WTPExam::my_exams($user_id, $cat_id_sql, $orderby, $reorder_by_latest_taking);
	
	// intelligence dependencies	
	if(watupro_intel()) {
		require_once(WATUPRO_PATH."/i/models/dependency.php");
		$my_exams = WatuPRODependency::mark($my_exams, $takings);	
	}
	
	$num_to_take = sizeof($my_exams) - $num_taken;
	$dateformat = get_option('date_format');
	
	if(!empty($_GET['export_results']) and (current_user_can(WATUPRO_MANAGE_CAPS) or $user_id == $user_ID)) {		
		if($multiuser_access != 'all' and $user_id != $user_ID) return false;
		
		WTPRecord :: export_my_exams($my_exams, $takings, $num_taken);
		exit;
	}
	
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	wp_enqueue_style('style.css', plugins_url().'/watupro/style.css', null, '1.0');
	
	if($in_shortcode) {
		// called in shortcode
		$permalink = get_permalink($post->ID);
		$params = array('view_details' => 1);
		$target_url = add_query_arg( $params, $permalink );
		$current_url = home_url( $wp->request );
	}
   
	if(@file_exists(get_stylesheet_directory().'/watupro/my_exams.php')) require get_stylesheet_directory().'/watupro/my_exams.php';
	else require WATUPRO_PATH."/views/my_exams.php";   
}

// called on template_redirect for export
function watupro_export_my_exams() {
	if(empty($_GET['watupro_export_my_exams'])) return;
	
	return watupro_my_exams();
}

// exams controller object
class WatuPROExams {
	// show the Question X of Y text and progress bar only if we don't show progress bar
	static function show_qXofY($qct, $total, $advanced_settings, $pos = 'bottom') {
		// the showing position defaults to bottom		
		if(empty($advanced_settings['show_progress_bar']) and $pos == 'top') return '';
      
      if(empty($advanced_settings['show_xofy']) and isset($advanced_settings['show_xofy'])) return '';
      
      $rtl_class = empty($advanced_settings['is_rtl']) ? '' : 'watupro-rtl';	
		
		return  "<p class='watupro-qnum-info $rtl_class'>".sprintf(__("Question %d of %d", 'watupro'), $qct, $total)."</p>";
	}
	
	// shows progress bar
	static function progress_bar($questions, $exam, $in_progress = null) {
		// handle $total_pages based on different paginations
		switch($exam->single_page) {
			case WATUPRO_PAGINATE_PAGE_PER_CATEGORY:
				$cat_ids = array();
				foreach($questions as $question) {
					if(!in_array($question->cat_id, $cat_ids)) $cat_ids[] = $question->cat_id;
				}
				$total_pages = count($cat_ids);
			break;
			case WATUPRO_PAGINATE_ONE_PER_PAGE:
				$total_pages = count($questions);
			break;
			case WATUPRO_PAGINATE_CUSTOM_NUMBER:
				$total_pages = ceil( sizeof($questions) / $exam->custom_per_page );	
			break;
			case WATUPRO_PAGINATE_ALL_ON_PAGE:
			default:
				return '';
			break;
		}		
				
		$init_width = round(100 / $total_pages);
	   $init_width = 0;
		
		$progress = '<div id="watupro-progress-container-'.$exam->ID.'" class="watupro-progress-container">
  				<div class="watupro-progress-bar" id="watupro-progress-bar-'.$exam->ID.'" style="width:'.$init_width.'%;">&nbsp;';
  		
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(!empty($advanced_settings['progress_bar_percent'])) {
			$progress .= '<span class="watupro-progress-percent" id="watupro-progress-bar-percent-'.$exam->ID.'">'.$init_width.'%</span>';
		}		
  			$progress .= '</div>
		</div>';

		
		$progress .= '<input type="hidden" value="'.$total_pages.'" id="watupro-progress-bar-pages-'.$exam->ID.'">';
		
		return $progress;
	}
}

// advanced exam settings
function watupro_advanced_exam_settings() {
	global $wpdb;
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['exam_id'])));
	
	if(empty($exam->ID)) {
		echo "<div class='inside'><p>".sprintf(__('This tab will become available after the %s is created.', 'watupro'), __('quiz', 'watupro'))."</p></div>"; 
		return false;
	}	

	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	$q_exam_id = empty($exam->reuse_questions_from) ? $exam->ID : $exam->reuse_questions_from;
	$exam_id = $q_exam_id;
	$q_id_sql = '';
	if(watupro_intel()) $q_id_sql = WatuPROIExam :: reused_questions_sql($exam, 'tQ.ID'); 
	
	// select question categories
	$qcats = $wpdb->get_results("SELECT tC.* FROM ".WATUPRO_QCATS." tC
		JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.cat_id = tC.ID $q_id_sql
		JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tQ.exam_id AND tE.ID IN ($exam_id) 
		GROUP BY tC.ID ORDER BY tQ.sort_order, tQ.ID, tC.name");
		
	// shall we add parent categories?
	if(!empty($advanced_settings['sum_subcats_catgrades'])) {
		$final_cats = array();
		$final_cat_ids = array();
		foreach($qcats as $cat) {
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
		$qcats = $final_cats;
	}	// end adding empty parent categories when summing up subcategory performance is selected	
		
	// regroup categories by main category
	$regrouped_cats = $regrouped_cat_ids = array();
	foreach($qcats as $qcat) {
		if(empty($qcat->parent_id)) {
			$regrouped_cats[] = $qcat;
			$regrouped_cat_ids[] = $qcat->ID;
			foreach($qcats as $sub) {
				if($sub->parent_id == $qcat->ID) {
					$regrouped_cats[] = $sub;
					$regrouped_cat_ids[] = $sub->ID;
				}
			}
		}
	}

	// are there categories that are still not included (orphan subcats?)
	// we'll add them here	
	foreach($qcats as $qcat) {
		if(!in_array($qcat->ID, $regrouped_cat_ids)) $regrouped_cats[] = $qcat;
	}
	
	$qcats = $regrouped_cats;
		
	// any uncategorized questions?
	$num_uncategozied = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_QUESTIONS."
		WHERE exam_id=%d", $exam->ID));	
	if($num_uncategozied) $qcats[] = (object)array("ID"=>0, "name"=>__('Uncategorized', 'watupro'));	
	
	if(!empty($_POST['ok'])) {
		// save advanced config
		unset($_POST['ok']);
		
		// add sorted categories
		$sorted_cats = array();
		foreach($qcats as $qcat) {
			$sorted_cats[rawurlencode($qcat->name)] = intval($_POST['qcat_order_'.$qcat->ID]);
		}
		
		$advanced_settings['sorted_categories'] = $sorted_cats;
		$advanced_settings['sorted_categories_encoded'] = 1; // we need this flag because in the older versions we did not encode
		$advanced_settings['confirm_on_submit'] = empty($_POST['confirm_on_submit']) ? 0 : 1;	
		$advanced_settings['no_checkmarks'] = empty($_POST['no_checkmarks']) ? 0 : 1;
		$advanced_settings['no_checkmarks_unresolved'] = empty($_POST['no_checkmarks_unresolved']) ? 0 : 1;
		$advanced_settings['feedback_unresolved'] = empty($_POST['feedback_unresolved']) ? 0 : 1;
		$advanced_settings['reveal_correct_gaps'] = empty($_POST['reveal_correct_gaps']) ? 0 : 1;
		$advanced_settings['dont_prompt_unanswered'] = empty($_POST['dont_prompt_unanswered']) ? 0 : 1;		
		$advanced_settings['dont_prompt_notlastpage'] = empty($_POST['dont_prompt_notlastpage']) ? 0 : 1;
		$advanced_settings['dont_load_inprogress'] = empty($_POST['dont_load_inprogress']) ? 0 : 1;
		$advanced_settings['email_not_required'] = @$_POST['email_not_required'];
		$advanced_settings['show_only_snapshot'] = empty($_POST['show_only_snapshot']) ? 0 : 1;
		$advanced_settings['show_result_and_points'] = empty($_POST['show_result_and_points']) ? 0 : 1;
		$advanced_settings['answer_snapshot_in_table_format'] = empty($_POST['answer_snapshot_in_table_format']) ? 0 : 1;
		$advanced_settings['answered_paginator_color'] = sanitize_text_field(@$_POST['answered_paginator_color']);
		$advanced_settings['unanswered_paginator_color'] = sanitize_text_field(@$_POST['unanswered_paginator_color']);		
		$advanced_settings['dont_scroll'] = empty($_POST['dont_scroll']) ? 0 : 1;
		$advanced_settings['dont_scroll_start'] = empty($_POST['dont_scroll_start']) ? 0 : 1;
		$advanced_settings['single_choice_action'] = sanitize_text_field($_POST['single_choice_action']);
		$advanced_settings['unselect'] = empty($_POST['unselect']) ? 0 : 1;
		$advanced_settings['takings_by_email'] = intval($_POST['takings_by_email']);
		foreach($qcats as $cnt=>$qcat) {
			$advanced_settings['qcat_order_'.$qcat->ID]  = intval(@$_POST['qcat_order_'.$qcat->ID]);
			$advanced_settings['random_per_'.$qcat->ID]  = intval(@$_POST['random_per_'.$qcat->ID]);
		}
		$advanced_settings['play_levels'] = @$_POST['play_levels'] ; // restrict to levels from the play plugin
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET advanced_settings=%s WHERE ID=%d",
			serialize($advanced_settings), $exam->ID));
		return true; // becuse $_POST['ok'] is now called from the WatuPRO edit exam page, we'll return here instead of displaying anything	
	}	
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam->ID));
	$q_exam_id = empty($exam->reuse_questions_from) ? $exam->ID : $exam->reuse_questions_from;
	$exam_id = $q_exam_id;
		
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	// add sort order 
	$sorted_cats = @$advanced_settings['sorted_categories'];	
	
	// print_r($sorted_cats);
	foreach($qcats as $cnt=>$qcat) {
		$def_order = $cnt+1;		
		$qcat_name = $qcat->name; 
		if(!empty($advanced_settings['sorted_categories_encoded'])) $qcat_name = rawurlencode($qcat->name); 
		if(isset($sorted_cats[$qcat_name])) $qcats[$cnt]->sort_order = intval($sorted_cats[$qcat_name]);
		else $qcats[$cnt]->sort_order = $def_order;
	}	
	
	$grades = WTPGrade :: get_grades($exam);	
	
	if(@file_exists(get_stylesheet_directory().'/watupro/advanced-settings.html.php')) require get_stylesheet_directory().'/watupro/advanced-settings.html.php';
	else require WATUPRO_PATH."/views/advanced-settings.html.php";
}

// override exam properties from shortcode atts
function watupro_override_atts(&$exam, $attr) {
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));	
	
	// difficulty level 
	if(!empty($attr['difficulty_level'])) {		
		$advanced_settings['difficulty_level'] = sanitize_text_field(trim($attr['difficulty_level']));
		$exam->advanced_settings = serialize($advanced_settings);
	}
	
	// dont load inprogress
	if(!empty($attr['dont_load_inprogress'])) {		
		$advanced_settings['dont_load_inprogress'] = intval($attr['dont_load_inprogress']);
		$exam->advanced_settings = serialize($advanced_settings);
	}
	
	// category
	if(!empty($attr['category_id'])) {		
		$advanced_settings['question_category_id'] = intval($attr['category_id']);
		$exam->advanced_settings = serialize($advanced_settings);
	}
	
	// tags
	if(!empty($attr['tags'])) {		
		$advanced_settings['tags'] = sanitize_text_field(trim($attr['tags']));
		$exam->advanced_settings = serialize($advanced_settings);
	}
	
	// pull random
	if(!empty($attr['pull_random']) and is_numeric($attr['pull_random'])) {		
		$exam->pull_random = intval($attr['pull_random']);
	}
	
	// pull random per category
	if(!empty($attr['random_per_category']) and is_numeric($attr['random_per_category'])) {		
		$exam->random_per_category = intval($attr['random_per_category']);
		
		// to avoid mistakes let's ensure that if someone enters a number higher than 1, 
		// this will actually set how many random per cat we want
		if($exam->random_per_category > 1) {
			$exam->pull_random = $exam->random_per_category;
			$exam->random_per_category = 1; 
		}
	}
	
	// time limit
	if(!empty($attr['time_limit']) and is_numeric($attr['time_limit'])) {
		$exam->time_limit = $attr['time_limit'];
	}
	
	// pagination
	if(isset($attr['pagination']) and is_numeric($attr['pagination']) and $attr['pagination'] >= 0 and $attr['pagination'] <= 3) {
		$exam->single_page = $attr['pagination'];
		
		if($attr['pagination'] == 3 and !empty($attr['custom_per_page'])) $exam->custom_per_page = intval($attr['custom_per_page']);
	}
	
	// require login
	if(isset($attr['require_login']) and is_numeric($attr['require_login'])) $exam->require_login = intval($attr['require_login']);
} // end watupro_override_atts()

// individual quiz schedules
function watupro_schedule() {
	global $wpdb;
	
	// select quiz
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['quiz_id'])));
	// select existing schedules 
	$schedules = unserialize(stripslashes($quiz->user_schedules));
	
	// add or save schedule - it's possible to have only one schedule per user
	if(!empty($_POST['ok']) and check_admin_referer('watupro_schedules')) {
		// find user by login
		if(empty($_POST['user_id'])) {
			if(strstr($_POST['login'], '@')) $user = get_user_by('email', sanitize_email($_POST['login']));
			else $user = get_user_by('login', sanitize_text_field($_POST['login']));
			
			if(!empty($user->ID)) $user_id = $user->ID;
		}
		else $user_id = intval($_POST['user_id']);
		
		if(!empty($user_id)) {
			// save schedule
			$from = sanitize_text_field($_POST['schedule_from']).' '.sanitize_text_field($_POST['schedule_from_hour']).':'.sanitize_text_field($_POST['schedule_from_minute']).':00';
			$to = sanitize_text_field($_POST['schedule_to']).' '.sanitize_text_field($_POST['schedule_to_hour']).':'.sanitize_text_field($_POST['schedule_to_minute']).':00';
			$schedule = array("user_id" => $user_id, "from" => $from, "to" => $to);
			
			if(empty($schedules)) $schedules = array();
			
			// check if a schedule for this user already exists. If yes, update it
			$found = false;
			foreach($schedules as $cnt => $sch) {
				if($sch['user_id'] == $user_id) {
					$found = true;
					$schedules[$cnt] = $schedule;
					break;
				}
			}	// end foreach
			
			// if not found, add
			if(!$found)	array_unshift($schedules, $schedule);	
			
			// update quiz 
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET user_schedules=%s WHERE ID=%d", serialize($schedules), $quiz->ID));
		}
		else $error = __('User not found.', 'watupro');
	}
	
	// delete schedule
	if(!empty($_POST['del']) and check_admin_referer('watupro_schedules')) {
		foreach($schedules as $cnt => $sch) {
			if($sch['user_id'] == $_POST['user_id']) {
				unset($schedules[$cnt]);
				break;
			}
		} // end foreach
		
		// update quiz 
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET user_schedules=%s WHERE ID=%d", serialize($schedules), $quiz->ID));
	}
	
	// select quiz and schedules again
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['quiz_id'])));	
	$schedules = unserialize(stripslashes($quiz->user_schedules));
	
	// for existing schedules we have to collect username and email. If an user no longer exists, unset the schedule
	if(!empty($schedules) and is_array($schedules) and count($schedules)){
		foreach($schedules as $cnt => $schedule) {
			$sch_user = get_userdata($schedule['user_id']);
			
			if(empty($sch_user->data->ID)) {
				unset($schedules[$cnt]);
				continue;
			}
			
			$schedules[$cnt]['userdata'] = $sch_user->data->display_name .' ('.$sch_user->data->user_email.')';
		}
	}
	

	watupro_enqueue_datepicker();
	if(@file_exists(get_stylesheet_directory().'/watupro/schedules.html.php')) require get_stylesheet_directory().'/watupro/schedules.html.php';
	else require WATUPRO_PATH."/views/schedules.html.php";
} // end watupro_schedule

// check if user has an individual schedule
function watupro_check_user_schedules($quiz) {
	if(!is_user_logged_in()) return true;
	$user_id = get_current_user_id();
	$schedules = unserialize(stripslashes($quiz->user_schedules));
	
	if(!empty($schedules) and is_array($schedules) and count($schedules)){
		foreach($schedules as $cnt => $schedule) {
			if($schedule['user_id'] == $user_id) {
				// now check				
   			$schedule_from = strtotime($schedule['from']);
   		   $schedule_to = strtotime($schedule['to']);
   		   if(!watupro_check_schedule($schedule_from, $schedule_to)) return false;
				break;
			}
		} // end foreach schedule
	} // end if
	
	return true;
}

// helper to check the schedle. Same called in show_exam.php
function watupro_check_schedule($schedule_from, $schedule_to) {
	$now = current_time('timestamp');
	if ($now < $schedule_from or $now > $schedule_to) {
		// "will be" or "was"
		if($now > $schedule_to) $time_msg = __('was', 'watupro');
		else $time_msg = __('will be', 'watupro');    	
    	
        printf(__('This %1$s %2$s available between %3$s and %4$s.', 'watupro'), WATUPRO_QUIZ_WORD, $time_msg, date_i18n(get_option('date_format').' '.get_option('time_format'), $schedule_from), date_i18n(get_option('date_format').' '.get_option('time_format'), $schedule_to));
        if(current_user_can(WATUPRO_MANAGE_CAPS)) echo ' '.__('You can still see it only because you are an administrator or a manager.', 'watupro').' ';
        else return false; // students can't take this test
    }
	return true;		    
}

// this should adjust the time limit in case the quiz schedule ends sooner than the time that the user has
// BUT it should also set a variable that will avoid the secsPassed reduction in WatuPRO.StartTheTimer because we should not reduce for these people
function watupro_scheduled_adjust_time_limit(&$exam) {
	if($exam->is_scheduled != 1) return false;
	
	$schedule_to = strtotime($exam->schedule_to);
	if($schedule_to < current_time('timestamp')) return false; // admins can take quiz out of schedule but we should not affect the timer in this case
	
	if(current_time('timestamp') + $exam->time_limit * 60 > $schedule_to) {
		$exam->timer_adjusted_by_schedule = 1;
		$exam->time_limit = round(($schedule_to - current_time('timestamp'))/60, 1);
	}
}

// saves contact details of logged in user in case this is selected. The idea is to pre-fill the data for next time
function watupro_save_contacts() {
	if(!is_user_logged_in() or get_option('watupro_save_contacts') != '1') return false;
	$user_id = get_current_user_id();
	
	$contact_data = array(
		"name" => sanitize_text_field($_POST['taker_name']), 
		"email" => sanitize_email($_POST['taker_email']),
		"phone" => sanitize_text_field($_POST['taker_phone']),
		"company" => sanitize_text_field($_POST['taker_company']),
		"field1" => sanitize_text_field($_POST['taker_field1']),
		"field2" => sanitize_text_field($_POST['taker_field2']),
	);
	
	update_user_meta($user_id, 'watupro_contact_data', $contact_data);
} // end watupro_save_contacts()