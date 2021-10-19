<?php
// manage difficulty levels
class WatuPRODiffLevels {
	static function manage() {
		global $wpdb; 
		
		// for the moment use this same page to manage difficulty levels. When we extend their functionality this will be moved to a separate menu entry
		if(!empty($_POST['ok'])) {
			update_option('watupro_difficulty_levels', trim($_POST['difficulty_levels']));
			update_option('watupro_apply_diff_levels', @$_POST['apply_diff_levels']);
			update_option('watupro_default_user_diff_levels', @$_POST['user_diff_levels']);
			
			// make sure there are no questions with saved unexisting difficulty level
			$diff_levels = stripslashes(get_option('watupro_difficulty_levels'));
			$diff_levels_arr = explode(PHP_EOL, $diff_levels); 
			if(!empty($diff_levels) and count($diff_levels_arr)) {
				$wpdb->query("UPDATE ".WATUPRO_QUESTIONS." SET difficulty_level='' 
					WHERE difficulty_level != '' AND difficulty_level NOT IN ('".implode("','", $diff_levels_arr)."') ");
			} 
		}
		
		$apply_diff_levels = get_option('watupro_apply_diff_levels');
		$diff_levels = stripslashes(get_option('watupro_difficulty_levels'));
		$user_diff_levels = get_option('watupro_default_user_diff_levels'); // sets through watupro_register_group()
		$diff_levels_arr = explode(PHP_EOL, $diff_levels); 	
		
		// unlock criteria
		if(!empty($_POST['add_criteria'])) {
			// don't allow duplicates
			$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_UNLOCK_LEVELS." WHERE unlock_level=%s", $_POST['unlock_level']));
			if($exists) wp_die(__('You can have only one set of unlock criteria per level.', 'watupro'));		
			
			$wpdb->query($wpdb->prepare("INSERT INTO " . WATUPRO_UNLOCK_LEVELS . " SET
				unlock_level=%s, min_points=%d, min_questions=%d, percent_correct=%d, from_level=%s",
				$_POST['unlock_level'], $_POST['min_points'], $_POST['min_questions'], $_POST['percent_correct'], $_POST['from_level']));
		}
		
		if(!empty($_POST['save_criteria'])) {
			$wpdb->query($wpdb->prepare("UPDATE " . WATUPRO_UNLOCK_LEVELS . " SET
				unlock_level=%s, min_points=%d, min_questions=%d, percent_correct=%d, from_level=%s
				WHERE ID=%d",
				$_POST['unlock_level'], $_POST['min_points'], $_POST['min_questions'], $_POST['percent_correct'], 
				$_POST['from_level'], $_POST['id']));
		}
		
		if(!empty($_POST['del_criteria'])) {
			$wpdb->query($wpdb->prepare("DELETE FROM " . WATUPRO_UNLOCK_LEVELS . " WHERE ID=%d", $_POST['id']));
		}
		
		// select unlock criteria if any
		$unlocks = $wpdb->get_results("SELECT * FROM " . WATUPRO_UNLOCK_LEVELS . " ORDER BY ID");
		
		// select logs if any
		$page_limit = 20;
		$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
		$logs = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tL.*, tE.name as quiz_name, 
			tT.end_time as taken_time, tU.display_name as user_name, tT.exam_id as exam_id  
			FROM ".WATUPRO_UNLOCK_LOGS." tL JOIN {$wpdb->users} tU ON tU.ID = tL.user_id 
			LEFT JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tL.taking_id
			LEFT JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id
			ORDER BY tL.ID DESC LIMIT $offset, $page_limit");
		$count_logs = $wpdb->get_var("SELECT FOUND_ROWS()");
		$timeformat = get_option('date_format') . ' ' . get_option('time_format');
		
		include(WATUPRO_PATH."/views/diff-levels.html.php");
	} // end manage
	
	// catch the watupro_completed_exam action and unlock levels if any
	static function completed_exam($taking_id) {
		global $wpdb, $user_ID;
		
		if(!is_user_logged_in()) return true;
		
		// are we applying diff_level restrictions?
		if(get_option('watupro_apply_diff_levels') != 1) return true;
		
		$diff_levels = stripslashes(get_option('watupro_difficulty_levels'));
		$user_diff_levels = get_user_meta($user_ID, 'watupro_difficulty_levels', true);
		$diff_levels_arr = explode(PHP_EOL, $diff_levels); 	
		
		// select all unlock criteria
		$unlocks = $wpdb->get_results("SELECT * FROM " . WATUPRO_UNLOCK_LEVELS . " ORDER BY ID");
		
		foreach($unlocks as $unlock) {
			// check if user already has the level
			if(@in_array($unlock->unlock_level, $user_diff_levels)) continue;
			
			self :: maybe_unlock_level($unlock, $taking_id);
		}
 	}  // end completed_exam() action
 	
 	// this method actually checks if a level has to be unlocked and unlocks it
 	static function maybe_unlock_level($unlock, $taking_id) {
 		global $wpdb, $user_ID;
 		
 		// check criteria: points
 		if($unlock->min_points) {
 			$points = $wpdb->get_var($wpdb->prepare("SELECT SUM(tA.points) as points FROM
 				".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
 				AND tQ.difficulty_level=%s
 				JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tA.taking_id AND tT.in_progress=0 AND tT.user_id=%d",
 				$unlock->from_level, $user_ID));
 			// echo "POINTS: $points;";	
 			if($points < $unlock->min_points) return false;	
 		}
 		
 		// check criteria: questions
 		if($unlock->min_questions) {
 			$questions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(tA.ID) as questions FROM
 				".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
 				AND tQ.difficulty_level=%s
 				JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tA.taking_id AND tT.in_progress=0 AND tT.user_id=%d",
 				$unlock->from_level, $user_ID));
 			// echo "QUESTIONS: $questions;";	
 			if($questions < $unlock->min_questions) return false;	
 		}
 		
 		// check criteria: % correct
 		if($unlock->percent_correct) {
 			$all_answers = $wpdb->get_results($wpdb->prepare("SELECT tA.is_correct as is_correct FROM
 				".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
 				AND tQ.difficulty_level=%s AND tQ.is_survey = 0
 				JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tA.taking_id AND tT.in_progress=0 AND tT.user_id=%d",
 				$unlock->from_level, $user_ID));
 			$total = count($all_answers);
 			if($total == 0) return false;	
 				
 			$num_correct = 0;
 			foreach($all_answers as $a) {
 				if($a->is_correct) $num_correct++;
 			}	
 			
 			$percent = round(100 * $num_correct / $total);
 			// echo "PERCENT: $percent;";
 			if($percent < $unlock->percent_correct) return false;	
 		}
 		
 		// if all passed, unlock the level
 		$user_diff_levels = get_user_meta($user_ID, 'watupro_difficulty_levels', true);
 		$user_diff_levels[] = $unlock->unlock_level;
 		update_user_meta( $user_ID, 'watupro_difficulty_levels', $user_diff_levels ); 
 		
 		// on screen echo?
 		printf("<p>".__('You just unlocked questions from %s difficulty level!', 'watupro')."</p>", stripslashes($unlock->unlock_level));
 		
 		// save log
 		$wpdb->query($wpdb->prepare("INSERT INTO " . WATUPRO_UNLOCK_LOGS . " SET 
 			unlocked_level = %s, user_id=%d, taking_id=%d", $unlock->unlock_level, $user_ID, $taking_id));
 	} // end maybe unlock level
}