<?php
// questions controller. For now keep mix with procedural functions, but to-do is to move them all in the class
class WatuPROQuestions {
	static function mark_review() {
		global $wpdb;
		$_watu = new WatuPRO();
		
		// this will only happen for logged in users
		if(!is_user_logged_in()) return false;
				
		$taking_id = $_watu->add_taking($_POST['exam_id'],1);
				
		// select current data if any
		$marked_for_review = $wpdb->get_var($wpdb->prepare("SELECT marked_for_review FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE ID=%d", $taking_id));
			
		if(empty($marked_for_review)) $marked_for_review = array("question_ids"=>array(), "question_nums"=>array());
		else $marked_for_review = unserialize($marked_for_review);
		
		if($_POST['act'] == 'mark') {
			$marked_for_review['question_ids'][] = $_POST['question_id'];
			$marked_for_review['question_nums'][] = $_POST['question_num']; 
		}
		else {
			// unmark
			foreach($marked_for_review['question_ids'] as $cnt=>$id) {
				if($id == $_POST['question_id']) unset($marked_for_review['question_ids'][$cnt]);
			}
			
			foreach($marked_for_review['question_nums'] as $cnt=>$num) {
				if($num == $_POST['question_num']) unset($marked_for_review['question_nums'][$cnt]);
			}
		}	
		
		// now save
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." SET marked_for_review=%s WHERE ID=%d",
			serialize($marked_for_review), $taking_id));
	} // end mark_review
	
} // end class

// add/edit question 
function watupro_question() {
	global $wpdb, $user_ID;
	
	// check access
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(sprintf(__('You can only manage the questions on your own %s.','watupro'), WATUPRO_QUIZ_WORD_PLURAL));
	}
	if($multiuser_access == 'group') {
		$cat_ids = WTPCategory::user_cats($user_ID);
		$cat_id_sql=implode(",",$cat_ids);
		$allowed_to_edit = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_EXAMS." 
			WHERE cat_id IN ($cat_id_sql) AND ID=%d", $_GET['quiz']));
		if(!$allowed_to_edit) wp_die(__('You can only manage questions of quizzes within your allowed categories', 'watupro'));					
	}		
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this");
	
	$action = 'new';
	if($_REQUEST['action'] == 'edit') $action = 'edit';
	
	$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", intval(@$_GET['question'])));
	$all_answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE question_id=%d AND question_id>0 ORDER BY sort_order", intval(@$_GET['question'])));
	
	$ans_type = ($action =='new' and empty($_GET['question'])) ? get_option('watupro_answer_type'): $question->answer_type;
	$answer_count = 4;
	if( ($action == 'edit' or !empty($_GET['question'])) and $answer_count < count($all_answers)) $answer_count = count($all_answers) ;	

	// true false is always 2
	if(!empty($question->ID) and $question->answer_type == 'radio' and $question->truefalse) { $truefalse = true; $answer_count = 2;}
	
	// select question categories
	$qcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id=0 ORDER BY name");
	$subcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id!=0 ORDER BY name");
	foreach($qcats as $cnt => $qcat) {
		$cat_subs = array();
		foreach($subcats as $sub) {
			if($sub->parent_id == $qcat->ID) $cat_subs[] = $sub;
		}
		$qcats[$cnt]->subs = $cat_subs;
	}
	
	// select exam	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	if(watupro_intel() and $exam->is_personality_quiz) $grades = WTPGrade :: get_grades($exam);
	if(watupro_intel() and ($ans_type == 'matrix' or $ans_type == 'nmatrix')) {
		$matches = array();
		foreach($all_answers as $answer) {
			list($left, $right) = explode('{{{split}}}', $answer->answer);
			$matches[] = array("id"=>$answer->ID, "left"=>$left, "right"=>$right);
		}
		$answer_count = 0;
	}
	
	// any difficulty levels?
	$difficulty_levels = stripslashes(get_option('watupro_difficulty_levels'));
	if(!empty($difficulty_levels)) $difficulty_levels = explode(PHP_EOL, $difficulty_levels);
	
	// default correct / incorrect points
	$set_default_points = get_option('watupro_set_default_points');
	$default_correct_points = $default_incorrect_points = 0;
	if($set_default_points) {
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(empty($advanced_settings['default_correct_answer_points'])) {
			$default_correct_points = get_option('watupro_correct_answer_points');
			$default_incorrect_points = get_option('watupro_incorrect_answer_points');
		}
		else {
			$default_correct_points = $advanced_settings['default_correct_answer_points'];
			$default_incorrect_points = $advanced_settings['default_incorrect_answer_points'];
		}
	}
	
	if(!empty($question->ID)) $question_design = empty($question->design) ? '' : unserialize(stripslashes($question->design));
	
	$flashcard_design = WatuPROFlashcard :: get_settings(); 
	
	add_thickbox();
	wp_enqueue_editor();
	wp_enqueue_media();
	if(@file_exists(get_stylesheet_directory().'/watupro/question_form.php')) require get_stylesheet_directory().'/watupro/question_form.php';
	else require WATUPRO_PATH."/views/question_form.php";
}

function watupro_questions() {
	global $wpdb, $user_ID;	
	if(empty($_GET['quiz']) and !empty($_GET['exam_id'])) $_GET['quiz'] = $_GET['exam_id'];
	$_GET['quiz'] = intval(@$_GET['quiz']);
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['quiz'])));
	
	if(watupro_intel() and !empty($exam->reuse_questions_from)) $reusing_questions = true;
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(sprintf(__('You can only manage the questions on your own %s.','watupro'), WATUPRO_QUIZ_WORD_PLURAL));
	}
	if($multiuser_access == 'group') {
		$cat_ids = WTPCategory::user_cats($user_ID);
		$cat_id_sql=implode(",",$cat_ids);
		$allowed_to_edit = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_EXAMS." 
			WHERE cat_id IN ($cat_id_sql) AND ID=%d", $_GET['quiz']));
		if(!$allowed_to_edit) wp_die(__('You can only manage questions of quizzes within your allowed categories', 'watupro'));					
	}			
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this");
	
	if(!empty($_GET['export'])) watupro_export_questions();
	if(!empty($_POST['watupro_import'])) watupro_import_questions();
	
	$action = 'new';
	if(!empty($_GET['action']) and $_GET['action'] == 'edit') $action = 'edit';
	
	if(isset($_POST['ok']) and check_admin_referer('watupro_question') and empty($reusing_questions)) {
		// add new category?
		if(!empty($_POST['new_cat'])) {
			$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_QCATS." (name, editor_id) VALUES (%s, %d) ", sanitize_text_field($_POST['new_cat']), $user_ID));
			$_POST['cat_id'] = $wpdb->insert_id;
		}	
		
		// only 'radio' questuons can be truefalse
		if($_POST['answer_type'] != 'radio') $_POST['truefalse'] = 0;
		
		if($action == 'edit') { 
			WTPQuestion::edit($_POST, $_POST['question']);			
		} 
		else  {
			$_POST['question'] = WTPQuestion::add($_POST);
			$action='edit';
		}
		
		// when we have selected "exact" feedback we need to match feedback/explanation to answers
		$explanations = array();
		if(!empty($_POST['explain_answer']) and !empty($_POST['do_elaborate_explanation']) and @$_POST['elaborate_explanation'] == 'exact') {
			$explanations = explode("{{{split}}}", $_POST['explain_answer']);
		}
		
		// adding answers
		$question_id = intval($_POST['question']);
		if($question_id > 0) {
			// select old answers
			$old_answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE question_id=%d ORDER BY ID", $question_id));	
			
			// handle matrix
			if(watupro_intel() and ($_POST['answer_type'] == 'matrix' or $_POST['answer_type'] == 'nmatrix')) WatuPROIQuestion :: save_matrix($question_id, $old_answers);		
			
			// the $counter will skip over empty answers, $sort_order_counter will track the provided answers order.
			$counter = 1;
			$sort_order_counter = 1;
			$correctArry = empty($_POST['correct_answer']) ? array() : $_POST['correct_answer'];
			$checkedArry = empty($_POST['is_checked']) ? array() : $_POST['is_checked'];		
			$pointArry = empty($_POST['point']) ? array() : $_POST['point'];
						
			if(!empty($_POST['answer']) and is_array($_POST['answer'])) {
				foreach ($_POST['answer'] as $key => $answer_text) {
					$correct = $is_checked = $accept_freetext = 0;
					if( @in_array($counter, $correctArry) ) $correct = 1;
					if( @in_array($counter, $checkedArry) ) $is_checked = 1;
					if(!empty($_POST['accept_freetext']) and @in_array($counter, $_POST['accept_freetext']) ) $accept_freetext = 1;
					$answer_text = wp_encode_emoji($answer_text);
					
					$point = floatval($pointArry[$key]);
					$grade_id_key = $key + 1;
					$chk_group = intval($_POST['chk_group'][$key]);
					
					// correct answers must always have positive number of points
					if($correct and $point <=0) $point = 1;
					
					// actually add or save the answer
					if($answer_text!=="") {
						if(empty($point)) $point = 0;
	
						// is there old answer?					
						if(isset($old_answers[$counter-1])) {
							$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_ANSWERS." SET 
								answer=%s, correct=%s, point=%s, sort_order=%d, explanation=%s, grade_id=%s, 
								chk_group=%d, is_checked = %d, accept_freetext = %d
								WHERE ID=%d",
								$answer_text, $correct, $point, $sort_order_counter, @$explanations[$key], 
								 @implode('|',watupro_int_array($_POST['grade_id_'.$grade_id_key])), $chk_group, $is_checked, $accept_freetext,
								 $old_answers[$counter-1]->ID));						
						} 
						else { 
							$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_ANSWERS." (question_id, answer, correct, point, 
							   sort_order, explanation, grade_id, chk_group, is_checked, accept_freetext)
								   VALUES(%d, %s, %s, %s, %d, %s, %s, %d, %d, %d)", 
								   $question_id, $answer_text, $correct, $point, $sort_order_counter, 
									@$explanations[$key], @implode('|',watupro_int_array($_POST['grade_id_'.$grade_id_key])), $chk_group, $is_checked, $accept_freetext));
						}
						$sort_order_counter++;
						// for truefalse questions don't save more than 2 answers
						if(!empty($_POST['truefalse']) and $sort_order_counter > 2) break; // break the foreach						
					}
					$counter++;
				} // end foreach $_POST['answer']
			
				// any old answers to cleanup?
				if($sort_order_counter <= count($old_answers)) {				
					$answers_to_del = array_slice($old_answers, $sort_order_counter-1);
					
					$ans_del_ids = array(0);
					foreach($answers_to_del as $a) $ans_del_ids[] = $a->ID;
					$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_ANSWERS." WHERE ID IN (".implode(',', $ans_del_ids).") AND question_id=%d", $question_id));
				}
			} // end if $_POST['answer']
			
			do_action('watupro_saved_question', $question_id);
			
			// preview? 
			if(!empty($_POST['preview'])) {
				if(@file_exists(get_stylesheet_directory().'/watupro/preview-question.html.php')) require get_stylesheet_directory().'/watupro/preview-question.html.php';
				else require WATUPRO_PATH."/views/preview-question.html.php";
				return true;
			}
			
			// should I redirect to edit a choice in rich text editor?
			if(!empty($_POST['goto_rich_text'])) {
				watupro_redirect("admin.php?page=watupro_edit_choice&id=".intval($_POST['goto_rich_text']));
			}
			
			// save & reuse clicked?
			if(!empty($_POST['reuse'])) {
				watupro_redirect("admin.php?page=watupro_question&action=new&quiz=".intval($_GET['quiz'])."&question=" . $question_id);
			}
			// save & add new blank clicked?
			if(!empty($_POST['add_blank'])) {
				watupro_redirect("admin.php?page=watupro_question&action=new&quiz=".intval($_GET['quiz']));
			}
		} // end if $question_id
	} // end adding/saving question
	
	// delete question
	if(!empty($_GET['action']) and $_GET['action'] == 'delete' and empty($reusing_questions)) {
		$_REQUEST['question'] = intval($_REQUEST['question']);
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id=%d", $_REQUEST['question']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $_REQUEST['question']));	
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d", $_REQUEST['question']));
	}
	
	// mass delete questions
	if(!empty($_POST['mass_delete']) and check_admin_referer('watupro_questions') and empty($reusing_questions)) {
		$qids = is_array($_POST['qids']) ? watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query("DELETE FROM ".WATUPRO_QUESTIONS." WHERE ID IN ($qid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id IN ($qid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id IN ($qid_sql)");
	}
	
	// mass activate/deactivate questions
	if((!empty($_POST['mass_activate']) or !empty($_POST['mass_deactivate'])) and check_admin_referer('watupro_questions') and empty($reusing_questions)) {
		$qids = is_array($_POST['qids']) ? watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$is_inactive = empty($_POST['mass_activate']) ? 1 : 0;
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET is_inactive=%d WHERE ID IN ($qid_sql)", $is_inactive));		
	}
	
	// save question hints settings
	if(!empty($_POST['hints_settings'])) {
		if(empty($_POST['enable_question_hints'])) {
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET question_hints='' WHERE ID=%d", $_GET['quiz']));
		} 
		else {
			$per_quiz = intval($_POST['hints_per_quiz']);
			$per_question = intval($_POST['hints_per_question']);
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET question_hints='$per_quiz|$per_question' WHERE ID=%d", $_GET['quiz']));
		}
	}
	
	// mass change question category
	if(!empty($_POST['mass_change_category']) and check_admin_referer('watupro_questions') and empty($reusing_questions)) {
		$qids = is_array($_POST['qids']) ?  watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET cat_id=%d 
			WHERE ID IN ($qid_sql) AND exam_id=%d", $_POST['mass_cat_id'], $_GET['quiz']));
	}
	
	// mass update question properties
	if(!empty($_POST['mass_update']) and check_admin_referer('watupro_questions') and empty($reusing_questions)) {
		$qids = is_array($_POST['qids']) ?  watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$update_sql = '';
		if($_POST['is_required'] != -1) $update_sql .= ', is_required='.intval($_POST['is_required']);
		if($_POST['is_important'] != -1) $update_sql .= ', importance='.intval($_POST['is_important']);
		if($_POST['is_survey'] != -1) $update_sql .= ', is_survey='.intval($_POST['is_survey']);
		if($_POST['accept_feedback'] != -1) $update_sql .= ', accept_feedback='.intval($_POST['accept_feedback']);
		if($_POST['exclude_on_final_screen'] != -1) $update_sql .= ', exclude_on_final_screen='.intval($_POST['exclude_on_final_screen']);
		
		$wpdb->query("UPDATE ".WATUPRO_QUESTIONS." SET exam_id=exam_id $update_sql WHERE ID IN ($qid_sql)");
	}
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['quiz'])));
	$exam_name = stripslashes($exam->name);
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	// save reused questions
	if(!empty($_POST['save_reused']) and check_admin_referer('watupro_questions') and !empty($reusing_questions)) {
		$qids = is_array($_POST['qids']) ?  watupro_int_array($_POST['qids']) : array(0);
		$qid_str = implode(",", $qids);
		
		$wpdb->query($wpdb->prepare("UPDATE " . WATUPRO_EXAMS ." SET reused_question_ids=%s WHERE ID=%d", $qid_str, $exam->ID));
		$exam->reused_question_ids = $qid_str;
	}
	
	// reorder questions
	if(!empty($_GET['move']) and empty($reusing_questions)) {
		WTPQuestion::reorder($_GET['move'], $_GET['quiz'], $_GET['dir']);
		watupro_redirect("admin.php?page=watupro_questions&quiz=".$_GET['quiz']);
	}
	
	// filter by category SQL
	$filter_sql = "";
	if(!empty($_GET['filter_cat_id'])) {
		 if($_GET['filter_cat_id']==-1) $filter_sql .= " AND Q.cat_id = 0 ";
		 else $filter_sql .= $wpdb->prepare(" AND Q.cat_id = %d ", $_GET['filter_cat_id']);
	}
	
	// filter by tag 
	if(!empty($_GET['filter_tag'])) {
		$tags = explode(",", sanitize_text_field($_GET['filter_tag']));
		
		foreach($tags as $tag) {
			$tag = trim($tag);
			$filter_sql .= " AND Q.tags LIKE '%|".$tag."|%'";
		}
	}
	
	// filter by difficulty level
	if(!empty($_GET['filter_dlevel'])) {
		$filter_sql .= $wpdb->prepare(" AND Q.difficulty_level=%s ", sanitize_text_field($_GET['filter_dlevel']));
	}
	
	// filter by ID
	if(!empty($_GET['filter_id'])) {
		// cleanup everything that is not comma or number
		$_GET['filter_id'] = preg_replace('/[^0-9\s\,]/', '', $_GET['filter_id']);
		if(!empty($_GET['filter_id'])) $filter_sql .= " AND Q.ID IN ($_GET[filter_id]) ";
	}
	
	// filter by contents
	if(!empty($_GET['filter_contents'])) {
		$filter_sql .= $wpdb->prepare(" AND Q.question LIKE %s ", '%'.sanitize_text_field($_GET['filter_contents']).'%');
	}
	
	// filter by answer type
	if(!empty($_GET['filter_answer_type'])) {
		$truefalse_sql = '';
		$filter_answer_type = sanitize_text_field($_GET['filter_answer_type']);
		if($_GET['filter_answer_type'] == 'truefalse') {
			$filter_answer_type = 'radio';
			$truefalse_sql = " AND Q.truefalse=1 ";
		}
		$filter_sql .= $wpdb->prepare(" AND Q.answer_type = %s $truefalse_sql ", $filter_answer_type);
	}
	
	// Retrieve the questions
	if(watupro_intel()) $sorting_answers_sql = 'Q.sorting_answers as sorting_answers,';
	else $sorting_answers_sql = '';
	
	// reset page limit?
	if(!empty($_POST['reset_page_limit'])) update_option('watupro_manage_questions_per_page', intval($_POST['page_limit']));
	
	$page_limit = get_option('watupro_manage_questions_per_page');
	if(empty($page_limit)) $page_limit = 50;
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	
	// SQL limit will be used for optimization UNLESS "fix sort order" is selected
	$limit_sql = '';
	if(empty($_GET['fix_sort_order'])) {
		$limit_sql = " LIMIT $offset, $page_limit";
	}
	
	$answer_count_sql = " (SELECT COUNT(*) FROM ".WATUPRO_ANSWERS." WHERE question_id=Q.ID) AS answer_count, ";
	$low_memory_mode = get_option('watupro_low_memory_mode');
	if($low_memory_mode == 1) $answer_count_sql = '';
	
	$q_exam_id = empty($exam->reuse_questions_from) ? $exam->ID : $exam->reuse_questions_from;
		
	$all_question = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS Q.ID, Q.question, C.name as cat, 
		Q.answer_type as answer_type, Q.is_inactive as is_inactive, Q.is_survey as is_survey, Q.is_required as is_required, 
		Q.title as title, Q.exam_id as exam_id,
		$sorting_answers_sql	 
		$answer_count_sql	
		Q.importance as importance, Q.truefalse as truefalse, Q.tags as tags				
		FROM `".WATUPRO_QUESTIONS."` AS Q
		LEFT JOIN ".WATUPRO_QCATS." AS C ON C.ID=Q.cat_id 
		WHERE Q.exam_id IN($q_exam_id) $filter_sql ORDER BY Q.sort_order, Q.ID $limit_sql");
	
	if(empty($_GET['fix_sort_order'])) $num_questions = $wpdb->get_var("SELECT FOUND_ROWS()"); 
	else $num_questions = sizeof($all_question);
	
	if(empty($filter_sql) and !empty($_GET['fix_sort_order']) and empty($reusing_questions)) {
		 WTPQuestion::fix_sort_order($all_question);
		 watupro_redirect("admin.php?page=watupro_questions&quiz=".intval($_GET['quiz']));
	}
	
	// select question categories
	// select question categories
	$qcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id=0 ORDER BY name");
	$subcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id!=0 ORDER BY name");
	foreach($qcats as $cnt => $qcat) {
		$cat_subs = array();
		foreach($subcats as $sub) {
			if($sub->parent_id == $qcat->ID) $cat_subs[] = $sub;
		}
		$qcats[$cnt]->subs = $cat_subs;
	}
	
	// hints related stuff
	$enable_question_hints = $hints_per_quiz = $hints_per_question = 0;
	if(!empty($exam->question_hints)) {
		$enable_question_hints = true;
		list($hints_per_quiz, $hints_per_question) = explode("|", $exam->question_hints);
	}
	
	// any difficulty levels?
	$difficulty_levels = stripslashes(get_option('watupro_difficulty_levels'));
	if(!empty($difficulty_levels)) $difficulty_levels = explode(PHP_EOL, $difficulty_levels);
	
	// user ratings?
	if(!empty($advanced_settings['accept_rating']) and $num_questions) {
		$q_ids = array();
		foreach($all_question as $q) $q_ids[] = $q->ID;
		$ratings = $wpdb->get_results("SELECT AVG(rating) as rating, question_id as question_id 
			FROM ".WATUPRO_STUDENT_ANSWERS."
			WHERE question_id IN (".implode(',', $q_ids).") AND rating!=0
			GROUP BY question_id");		
		
		foreach($all_question as $cnt=>$question) {
			$all_question[$cnt]->rating = 0;
			foreach($ratings as $rating) {
				if($question->ID == $rating->question_id) $all_question[$cnt]->rating = $rating->rating;
			}
		}
	} // end calculating user rating
	
	// reused quesiton IDs array
	if(!empty($reusing_questions)) $reused_question_ids = explode(",", $exam->reused_question_ids);
	
	$show_title_desc = get_option('watupro_show_title_desc');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/questions.php')) require get_stylesheet_directory().'/watupro/questions.php';
	else require WATUPRO_PATH."/views/questions.php";
}

// manage question categories
function watupro_question_cats() {
	global $wpdb, $user_ID;
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('qcats_access');	
	$error = false;
	
	$action = empty($_GET['do']) ? 'list' : $_GET['do'];
	
	// select parent
	if(!empty($_GET['parent_id'])) {
		$parent = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . WATUPRO_QCATS. " WHERE ID=%d ", $_GET['parent_id']));
	}			
	
	switch($action) {
		case 'add':
			if(!empty($_POST['ok']) and check_admin_referer('watupro_qcat')) {
				if(!WTPCategory::add($_POST['name'], $_POST['description'], @$_GET['parent_id'], @$_POST['exclude_from_reports'], $_POST['icon'])) {
					$error = __('Another category with this name already exists.', 'watupro');
					wp_die($error);
				}
				if(!$error) {
				   $url = "admin.php?page=watupro_question_cats&parent_id=" . intval(@$_GET['parent_id']);
				   if(!empty($_POST['save_and_new'])) $url .= "&do=add";	
				   watupro_redirect($url);
				}
			}
			
			include(WATUPRO_PATH."/views/question-cat.html.php");
		break;
		
		case 'edit':
		   $_GET['id'] = intval($_GET['id']); 
			if(!empty($_POST['ok']) and check_admin_referer('watupro_qcat')) {
				if($multiuser_access == 'own') {
					$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $_GET['id']));
					if($cat->editor_id != $user_ID) wp_die(__('You can manage only your own categories', 'watupro'));
				}		
				
				if(!WTPCategory::save($_POST['name'], $_GET['id'], $_POST['description'], @$_POST['exclude_from_reports'], $_POST['icon'])) {
					$error = __('Another category with this name already exists.', 'watupro');
					wp_die($error);
				}
				
				if(!$error) {
				   $url = "admin.php?page=watupro_question_cats&parent_id=" . intval(@$_GET['parent_id']);
				   if(!empty($_POST['save_and_new'])) $url .= "&do=add";	
				   watupro_redirect($url);
				} 
			}
			
			$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $_GET['id']));
			include(WATUPRO_PATH."/views/question-cat.html.php");
		break;
		
		case 'list':
		default:
			if(!empty($_GET['del'])) {
				if($multiuser_access == 'own') {
					$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d ", $_POST['id']));
					if($cat->editor_id != $user_ID) wp_die(__('You can manage only your own categories', 'watupro'));
				}		
				WTPCategory::delete($_GET['id']);
				watupro_redirect("admin.php?page=watupro_question_cats&parent_id=" . @$_GET['parent_id']);
			}
			
			// select all question categories	
			$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" AND editor_id = %d ", $user_ID) : "";
			$parent_id = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);
			$parent_sql = $wpdb->prepare(" AND parent_id = %d ", $parent_id);			
					
			$ob = empty($_GET['ob']) ? 'ID' : sanitize_text_field($_GET['ob']);
			if($ob != 'ID' and $ob != 'name') $ob = 'ID';
			$dir = empty($_GET['dir']) ? 'ASC' : sanitize_text_field($_GET['dir']);
			$odir = empty($_GET['dir']) ? 'ASC' : ($_GET['dir'] == 'ASC' ? 'DESC' : 'ASC');
			if($dir != 'ASC' and $dir !='DESC') $dir = 'ASC';
			
			$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE 1 $own_sql $parent_sql ORDER BY $ob $dir");	
			
			// foreach cats select number of subcats
			if(empty($parent_id)) {
				$subs = $wpdb->get_results("SELECT ID, parent_id FROM ".WATUPRO_QCATS." WHERE 1 $own_sql AND parent_id!=0");
				
				foreach($cats as $cnt => $cat) {
					$num_subs = 0;
					foreach($subs as $sub) {
						if($sub->parent_id == $cat->ID) $num_subs++;
					}
					
					$cats[$cnt]->num_subs = $num_subs;
				} // end foreach cat
			} // end filling subcat numbers
			
			if(@file_exists(get_stylesheet_directory().'/watupro/question_cats.php')) require get_stylesheet_directory().'/watupro/question_cats.php';
			else require WATUPRO_PATH."/views/question_cats.php";
		break;
	}	
}

// edit a selected choice with rich text editor
function watupro_edit_choice() {
	global $wpdb;
	
	// select choice
	$choice = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE ID=%d", $_GET['id']));
	
	// select question
	$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $choice->question_id));	
	
	if(!empty($_POST['ok'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_ANSWERS." SET answer=%s WHERE ID=%d",
			$_POST['answer'], $choice->ID));			
			
		// redirect to questions page
		watupro_redirect("admin.php?page=watupro_question&question=".$question->ID."&action=edit&quiz=".$question->exam_id);				
	}
	
	// select quiz
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $question->exam_id));
	
	if(watupro_intel() and $quiz->is_personality_quiz) {
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $question->exam_id));		
		$grades = WTPGrade :: get_grades($exam);	
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/edit-choice.html.php')) require get_stylesheet_directory().'/watupro/edit-choice.html.php';
	else require WATUPRO_PATH."/views/edit-choice.html.php";
}

// parses any occurencies of {{{answerto-...}}} mask.
// this mask is used to include the answer of a specific question
function watupro_parse_answerto($content, $taking_id, $exam) {
	global $wpdb;
	
	if(!strstr($content, '{{{answerto-') and !strstr($content, '{{{feedbackto-')) return $content;
	
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	// select all user answers of this taking
	$answers = $wpdb->get_results($wpdb->prepare("SELECT tA.answer as answer, tA.question_id as question_id, 
		tQ.is_survey as is_survey, tA.is_correct as is_correct 
		FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
		WHERE tA.taking_id=%d", $taking_id));
	
	$matches = array();
	preg_match_all("/{{{answerto-([^}}}])*}}}/", $content, $matches);
	
	foreach($matches[0] as $cnt=>$match) {
		// extract the question number
		$qid = str_replace('{{{answerto-','',$match);
		$qid = str_replace('}}}','',$qid);
		
		foreach($answers as $answer) {
			if($answer->question_id == $qid) {

				// in case the question is survey or we have selected to not reveal answers we have to remove any hardcoded correct/wrong images
				if($answer->is_survey or !empty($advanced_settings['no_checkmarks']) or (!$answer->is_correct and !empty($advanced_settings['no_checkmarks_unresolved']))) {
					
					$answer->answer = preg_replace("/<img[^>]+wrong.png\" hspace=\"5\"\>/i", "", $answer->answer);
					$answer->answer = preg_replace("/<img[^>]+correct.png\" hspace=\"5\"\>/i", "", $answer->answer);  
				}							
				
				$content = str_replace('{{{answerto-'.$qid.'}}}', stripslashes($answer->answer), $content);
				break;
			}
		}	
		
		// gone through all answers but could not find any? replace with empty	
		$content = str_replace('{{{answerto-'.$qid.'}}}', '', $content);
	} // end foreach matches
	
	// now parse feedback to
	$matches = array();
	preg_match_all("/{{{feedbackto-([^}}}])*}}}/", $content, $matches);
	
	foreach($matches[0] as $cnt=>$match) {
		// extract the question number
		$qid = str_replace('{{{feedbackto-','',$match);
		$qid = str_replace('}}}','',$qid);
		
		// select feedback
		$feedback = $wpdb->get_var($wpdb->prepare("SELECT explain_answer FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $qid));
		
		// gone through all answers but could not find any? replace with empty	
		$content = str_replace('{{{feedbackto-'.$qid.'}}}', stripslashes($feedback), $content);
	} // end foreach matches
	
	return $content;
} // end parse_answerto

// answer to a single question for the watupro-answer shortcode
function watupro_user_answer($question_id, $user_id = null, $taking_id = null) {
	global $wpdb;
	if(empty($user_id)) $user_id = get_current_user_id();
	if(empty($user_id)) return "<!--watupro-comment no user ID -->";
	
	$taking_id_sql = '';
	if(!empty($taking_id)) {
		$taking_id_sql = $wpdb->prepare(" AND tA.taking_id=%d ", $taking_id);
	}
	
	$answer = $wpdb->get_row($wpdb->prepare("SELECT tA.answer as answer, tA.question_id as question_id, 
		tQ.is_survey as is_survey, tA.is_correct as is_correct 
		FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
		WHERE tQ.ID=%d AND tA.user_id=%d $taking_id_sql ORDER BY tA.taking_id DESC LIMIT 1", $question_id, $user_id));
		
	if(empty($answer->answer))	return "<!--watupro-comment answer not found -->";
	
	if($answer->is_survey or !empty($advanced_settings['no_checkmarks']) or (!$answer->is_correct and !empty($advanced_settings['no_checkmarks_unresolved']))) {					
		$answer->answer = preg_replace("/<img[^>]+wrong.png\" hspace=\"5\"\>/i", "", $answer->answer);
		$answer->answer = preg_replace("/<img[^>]+correct.png\" hspace=\"5\"\>/i", "", $answer->answer);  
	}		
	
	return stripslashes($answer->answer);					
} // end watupro_user_answer