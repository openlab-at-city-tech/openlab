<?php
class WatuQuestion {
	// calculate points, correctness and return the class
	static function calculate($question, $ans, $ansArr, $correct, $class) {
		$points = 0;
				
		if($question->answer_type != 'textarea') {
			if($ans->correct == 1 and !$question->is_survey) $class .= ' correct-answer';
			if(  in_array($ans->ID , $ansArr) ) $class .= ' user-answer';
			if( in_array($ans->ID , $ansArr ) and $ans->correct == 1 and !$question->is_survey) $correct = true;
			if( in_array($ans->ID , $ansArr ) ) $points = $ans->point;
		}
		else {
			// textareas			
			$user_answer = $ansArr[0];		
			
			if( strtolower(trim($ans->answer)) == strtolower(trim($user_answer)) ) { 
				$class .= ' user-answer';
				$points = $ans->point;
			}
			if( strtolower(trim($ans->answer)) == strtolower(trim($user_answer)) and $ans->correct == 1 and !$question->is_survey) {
				$correct = true; 
				$class .= ' correct-answer';
			}			 
		} // end working with textareas
		
		if($question->is_survey) $class = str_replace('user-answer', 'user-answer-survey');
		
		return array($points, $correct, $class);
	}
	
	// figure out the maximum number of points the user can get on the question
	static function max_points($question, $all_answers) {
		$max_points = 0;
		
		// get only the answers of this question
		$q_answers = array();
		foreach($all_answers as $answer) {
			if($answer->question_id == $question->ID) $q_answers[] = $answer;
		}		
		
		if(!count($q_answers)) return 0;
		
		switch($question->answer_type) {
			case 'radio':
			case 'textarea':
				// get the answer with most points
				$max = 0;
				foreach($q_answers as $answer) {
					if($answer->point > $max) $max = $answer->point;
				} 
				
				$max_points += $max;
			break;
			
			case 'checkbox':
				foreach($q_answers as $answer) {				
					if($answer->point > 0) $max_points += $answer->point;
				}
			break;
		}
		
		return $max_points;
	} // end max_points
	
	// backward compatibility. In old versions sort order was not given
	// so we'll make sure all questions have correct one when loading the page
	static function fix_sort_order($questions) {
		global $wpdb;
		foreach($questions as $cnt => $question) {
			$cnt++;
			if(@$question->sort_order!=$cnt) {
				$wpdb->query("UPDATE ".WATU_QUESTIONS." SET sort_order=$cnt WHERE ID={$question->ID}");
			}
		}
	}
	
	static function reorder($id, $exam_id, $dir) {
		global $wpdb;
		$id = intval($id);
      $exam_id = intval($exam_id);
			
		// select question
		$question=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." WHERE ID=%d", $id));
		
		if($dir=="up") {
			$new_order = $question->sort_order-1;
			if($new_order<0) $new_order=0;
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE ".WATU_QUESTIONS." SET sort_order=sort_order+1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}
		
		if($dir=="down") {
			$new_order = $question->sort_order+1;			
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE ".WATU_QUESTIONS." SET sort_order=sort_order-1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}		
			
		// change this one
		$wpdb->query($wpdb->prepare("UPDATE ".WATU_QUESTIONS." SET sort_order=%d WHERE ID=%d", 
			$new_order, $id));
	}
}