<?php
function watupro_ajax($do = null, $do_exit = true) {
	global $wpdb;
	
	if(empty($do)) $do = esc_attr($_POST['do']);
	
	switch($do) {
		case 'mark_review':
			// mark question for review
			WatuPROQuestions :: mark_review();
		break;
		
		case 'select_grades':
			// select grades for a given quiz, return drop-down HTML
			if(!empty($_POST['exam_id'])) {
				$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, reuse_default_grades, grades_by_percent FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval(@$_POST['exam_id'])));
			}
			
			$html = '<option value="">------</option>';
			if(empty($_POST['exam_id'])) die($html); // when no exam, return only the main option
			
			//print_r($exam);
			$grades = WTPGrade :: get_grades($exam);
			
			foreach($grades as $grade) {
				$html .= '<option value="'.$grade->ID.'">'.stripslashes($grade->gtitle).'</option>'."\n";
			}
			
			echo $html;
		break;
		
		// check if this email can take the quiz more times
		case 'takings_by_email':
			$num_taken = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND email=%s AND in_progress=0", $_POST['exam_id'], $_POST['email']));
				
			if($_POST['allowed_attempts'] <= $num_taken) {
				echo 'ERROR|WATUPRO|';
				printf(__("Sorry, you can take this quiz only %d times.", 'watupro'), $_POST['allowed_attempts']);
			}	
		break;
		
		// calls Intelligence module to filter quizzes
		case 'select_reuse_quizzes':
			echo WatuPROIQuestion :: select_reuse_quizzes();
		break;
		
		case 'reorder_questions':
			WTPQuestion :: reorder_sortable($_POST['exam_id'], $_POST['questions']);
		break;
		
		// return drop-down of question categories available in a quiz
		case 'select_quiz_qcats':
			if(!empty($_POST['quiz_id'])) { 
				$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, reuse_questions_from FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_POST['quiz_id'])));				
				
				$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
					
				$qcats = $wpdb->get_results($wpdb->prepare("SELECT tC.* FROM " . WATUPRO_QCATS. " tC
						WHERE tC.ID IN (SELECT cat_id FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d) ", $q_exam_id));
						
				echo $wpdb->prepare("SELECT tC.* FROM " . WATUPRO_QCATS. " tC
						WHERE tC.ID IN (SELECT cat_id FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d) ", $q_exam_id);
						
				foreach($qcats as $qcat):?>
					<option value="<?php echo $qcat->ID?>"><?php echo stripslashes($qcat->name);?></option>
				<?php endforeach; 		
			}
		break;
		
		// return drop-downs from all single-choice (later also multiple choice) questions in a quiz and their choices.
		// this is used for filtering on the "View results" page but might be used in other places too.
		case 'question_filter':
			if(!empty($_POST['exam_id'])) $_GET['exam_id'] = $_POST['exam_id'];
			if(empty($_GET['exam_id'])) {
				echo '<p><b>';
				printf(__('Select a %s from the drop-down selector.', 'watupro'), WATUPRO_QUIZ_WORD);
				echo '</b></p>';
				if($do_exit) exit;
				else return;
			}
			
			$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, reuse_questions_from FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_GET['exam_id'])));
			$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
			
			$questions = $wpdb->get_results("SELECT ID, question, title FROM ".WATUPRO_QUESTIONS." 
				WHERE exam_id IN ($q_exam_id) AND answer_type='radio' ORDER BY title, question, sort_order, ID");
				
			$qids = array(-1);
			foreach($questions as $question) $qids[] = $question->ID;
				
			$choices = $wpdb->get_results("SELECT ID, question_id, answer FROM ".WATUPRO_ANSWERS." 
				WHERE question_id IN (".implode(',', $qids).") ORDER BY sort_order, ID");				
				
			// if question is preselected, load the choices
			if(!empty($_GET['filter_question_id'])) {
				$question_choices = $wpdb->get_results($wpdb->prepare("SELECT ID, question_id, answer FROM ".WATUPRO_ANSWERS." 
					WHERE question_id=%d ORDER BY sort_order, ID", intval($_GET['filter_question_id'])));		
			}	
			?>
			<p><select name="filter_question_id" onchange="wtpChangeFilterQuestion(this.value);">
				<option value=""><?php _e('- Select a question -', 'watupro');?></option>
				<?php foreach($questions as $question):?>
					<option value="<?php echo $question->ID?>" <?php if(!empty($_GET['filter_question_id']) and $_GET['filter_question_id'] == $question->ID) echo 'selected';?>><?php echo WTPQuestion :: summary($question);?></option>
				<?php endforeach;?>
			</select>
			<select name="filter_answer_id" id="filterAnswerID">
				<option value=""><?php _e('- Select an answer -', 'watupro');?></option>
				<option value="-1" <?php if(!empty($_GET['filter_answer_id']) and $_GET['filter_answer_id'] == -1) echo 'selected';?>><?php _e('- Not answered -', 'watupro');?></option>
				<?php if(!empty($question_choices)):
					foreach($question_choices as $qchoice):
						$words = preg_split('/\s/', strip_tags(stripslashes($qchoice->answer)));
						$words = array_slice($words, 0, 10);?>
						<option value="<?php echo $qchoice->ID;?>" <?php if(!empty($_GET['filter_answer_id']) and $_GET['filter_answer_id'] == $qchoice->ID) echo 'selected';?>><?php echo implode(" ", $words);?></option>
					<?php endforeach;			
				endif;?>
			</select>			
			</p>
			<script type="text/javascript">
			var choices = {
				<?php foreach($questions as $question) {
				echo $question->ID.': [';
					foreach($choices as $choice) {
						if($choice->question_id != $question->ID) continue;
						$words = preg_split('/\s/', strip_tags(stripslashes($choice->answer)));
						$words = array_slice($words, 0, 10);
						echo '{ "id": '.$choice->ID.', "title" : "'.implode(" ", $words).'"},';
					 } // end foreach choice
				echo '],';
				} // end foreach question ?>	
			};
			
			function wtpChangeFilterQuestion(id) {
				if(id == 0) {
					jQuery('#filterAnswerID').html('<option value=""><?php _e('- Select an answer -', 'watupro');?></option>');
					return false;
				}

				var optionsHTML = '<option value=""><?php _e('- Select an answer -', 'watupro');?></option><option value="-1"><?php _e('- Not answered -', 'watupro');?></option>';				
				
				var qChoices = choices[id];
				jQuery(qChoices).each(function(i, elt) {
					optionsHTML += "\n" + '<option value="'  + elt.id + '">' + elt.title + '</option>';
				});
				
				jQuery('#filterAnswerID').html(optionsHTML);
			}
			</script>
			<?php	
		break;
	}
	if($do_exit) exit;
}