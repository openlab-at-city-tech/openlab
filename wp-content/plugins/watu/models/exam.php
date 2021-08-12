<?php
class WatuExam {
	// keep the questions after submit in the same order they were show to the user
	// and only the questions that were shown
	function reorder_questions($questions, $orders) {
		global $wpdb;
		$new_questions = array();
		$qids = array(0);
		foreach($questions as $question) $qids[] = $question->ID;		
		
		// all answers in the quiz
		$all_answers = $wpdb->get_results("SELECT ID,answer,correct, point, question_id 
			FROM ".WATU_ANSWERS." WHERE question_id IN (" . implode(',', $qids) . ")");
		$new_answers = array();
				
		// reorder the answers accordingly to POST info		
		if(!empty($_POST['answer_ids'])) {
			foreach($_POST['answer_ids'] as $aorder) {
				foreach($all_answers as $answer) {
					if($answer->ID == $aorder) $new_answers[] = $answer;
				} // end foreach answer
			} // end foreach ID from post
		} // end reordering answers
				
		// reorder the questions accordingly to POST info
		foreach($orders as $order) {
			foreach($questions as $question) {
				// dump answers
				$question_answers = array();
				foreach($new_answers as $answer) {
					if($question->ID == $answer->question_id) $question_answers[] = $answer;
				}				
				$question->answers = $question_answers;				
				if($question->ID == $order) $new_questions[] = $question;
			} // end foreach question
		} // end foreach orders (means question IDs ordered as POST var)
	
		return $new_questions;
	}
	
	// create one demo quiz when user first installs the plugin
	static function create_demo() {
		global $wpdb;
		
		// don't do this if the user has already created some quizzes (let's not disturb old users)
		$num_quizzes = $wpdb->get_var("SELECT COUNT(ID) FROM ".WATU_EXAMS);
		if($num_quizzes) {
			update_option('watu_demo_quiz_created', '1');	
			return true;
		}
		
		$final_screen = __("<p>Congratulations - you have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%POINTS%% points out of %%MAX-POINTS%% points total.</p>\n\n<p>Your obtained grade is <b>%%GRADE-TITLE%%</b></p><p>%%GRADE-DESCRIPTION%%</p>", 'watu');
		
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_EXAMS." SET 
			name=%s, description=%s, final_screen=%s, added_on = CURDATE(), show_answers=1",
			__('Demo Quiz', 'watu'), __('This quiz is automatically created to help you get started. It will be created only once.','watu'),
			$final_screen));
		$quiz_id = $wpdb->insert_id;
		
		// create 3 questions
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." SET
			exam_id=%d, question=%s, answer_type=%s", $quiz_id, __("Select the correct answer:", 'watu'), 'radio'));
		$qid = $wpdb->insert_id;	
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='0', point=0, sort_order=1",
			$qid, __('The Earth is a star', 'watu')));
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='0', point=0, sort_order=2",
			$qid, __('The Sun is a planet', 'watu')));
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='1', point=1, sort_order=3",
			$qid, __('The Earth is a planet', 'watu')));	
			
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." SET
			exam_id=%d, question=%s, answer_type=%s", $quiz_id, __("Select all correct answers:", 'watu'), 'checkbox'));
		$qid = $wpdb->insert_id;	
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='1', point=1, sort_order=1",
			$qid, __('WordPress is open source software', 'watu')));
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='0', point=-1, sort_order=2",
			$qid, __('There are no quiz plugins for WordPress', 'watu')));
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." SET
			question_id=%d, answer=%s, correct='1', point=1, sort_order=3",
			$qid, __('WordPress can be used for a lot more than blogging', 'watu')));	
			
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." SET
			exam_id=%d, question=%s, answer_type=%s", $quiz_id, __("Do you have any comments about this quiz?", 'watu'), 'textarea'));	
			
		// now add 2 grades
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_GRADES." SET 
			exam_id=%d, gtitle=%s, gdescription=%s, gfrom=-1, gto=1",
			$quiz_id, __('Failed', 'watu'), __('Sorry, you could not collect enough points to pass this quiz', 'watu')));		
		$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_GRADES." SET 
			exam_id=%d, gtitle=%s, gdescription=%s, gfrom=2, gto=3",
			$quiz_id, __('Passed', 'watu'), __('Congratulations, you passed!', 'watu')));	
			
		update_option('watu_demo_quiz_created', '1');				
	}
}