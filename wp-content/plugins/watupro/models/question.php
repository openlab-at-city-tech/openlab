<?php
// Watu PRO Question model
class WTPQuestion {
	public static $advanced_settings = '';
	public static $in_progress = '';
	public static $is_copy = false; // are we calling this plugin functions from copy questions		
	
	// prepare and sanitize variables for DB input
	static function prepare_vars(&$vars) {
		if(!self :: $is_copy) $vars['content'] = watupro_strip_tags($vars['content']);
	   $vars['content'] = wp_encode_emoji($vars['content']);
	   $vars['answer_type'] = sanitize_text_field($vars['answer_type']);
	   $vars['cat_id'] = intval($vars['cat_id']);
      if(!self :: $is_copy) $vars['explain_answer'] = watupro_strip_tags($vars['explain_answer']); 
      $vars['is_required'] = empty($vars['is_required']) ? 0 : 1;
      $vars['correct_condition'] = sanitize_text_field(@$vars['correct_condition']);
      $vars['max_selections'] = intval($vars['max_selections']);
      $vars['is_inactive'] = empty($vars['is_inactive']) ? 0 : 1;
      $vars['is_survey'] = empty($vars['is_survey']) ? 0 : 1;
      $vars['open_end_mode'] = sanitize_text_field(@$vars['open_end_mode']);
      $vars['open_end_display'] = sanitize_text_field(@$vars['open_end_display']);
      $vars['exclude_on_final_screen'] = empty($vars['exclude_on_final_screen']) ? 0 : 1;
      if(!self :: $is_copy) $vars['hints'] = watupro_strip_tags(@$vars['hints']);
      $vars['compact_format'] = empty($vars['compact_format']) ? 0 : intval($vars['compact_format']);
      if(!empty($vars['compact_format_version']) and $vars['compact_format_version'] == 2 and $vars['answer_type'] != 'sort') $vars['compact_format'] = 3;
      if(!empty($vars['compact_format_version']) and $vars['compact_format_version'] == 3 and $vars['answer_type'] != 'sort') $vars['compact_format'] = 4;
      $vars['round_points'] = empty($vars['round_points']) ? 0 : 1;
      $vars['importance'] = intval(@$vars['importance']);      
      $vars['calculate_whole'] = (empty($vars['calculate_whole']) and empty($vars['calculate_checkbox_whole'])) ? 0 : 1;
      $vars['unanswered_penalty'] = floatval(@$vars['unanswered_penalty']);
      $vars['truefalse'] = empty($vars['truefalse']) ? 0 : 1;
      $vars['accept_feedback'] = empty($vars['accept_feedback']) ? 0 : 1;
      $vars['feedback_label'] = sanitize_text_field(@$vars['feedback_label']);
      $vars['reward_only_correct'] = empty($vars['reward_only_correct']) ? 0 : 1;
      $vars['discard_even_negative'] = empty($vars['discard_even_negative']) ? 0 : 1;
      $vars['difficulty_level'] = sanitize_text_field(@$vars['difficulty_level']);
      $vars['num_columns'] = intval(@$vars['num_columns']);
      $vars['allow_checkbox_groups'] = empty($vars['allow_checkbox_groups']) ? 0 : 1;
      $vars['is_flashcard'] = empty($vars['is_flashcard']) ? 0 : 1;
	   $vars['correct_gap_points'] = empty($vars['correct_gap_points']) ? 1 : floatval($vars['correct_gap_points']);
	   $vars['incorrect_gap_points'] = empty($vars['incorrect_gap_points']) ? 0 : floatval($vars['incorrect_gap_points']);
	   $vars['dont_randomize_answers'] = empty($vars['dont_randomize_answers']) ? 0 : 1;
	   $vars['reduce_points_per_hint'] = floatval(@$vars['reduce_points_per_hint']);
	   $vars['reduce_hint_points_to_zero'] = empty($vars['reduce_hint_points_to_zero']) ? 0 : 1;
	   $vars['accept_rating'] = empty($vars['accept_rating']) ? 0 : 1;
	   $vars['file_upload_required'] = empty($vars['file_upload_required']) ? 0 : 1;
	   $vars['no_negative'] = empty($vars['no_negative']) ? 0 : 1;
	   $vars['max_allowed_points'] = empty($vars['max_allowed_points']) ? 0 : floatval($vars['max_allowed_points']);
	   $vars['limit_words'] = intval(@$vars['limit_words']);
	   $vars['title'] = empty($vars['title']) ? '' : sanitize_text_field($vars['title']);
	   $vars['dont_explain_unanswered'] = empty($vars['dont_explain_unanswered']) ? 0 : 1;
		$vars['use_wpautop'] = empty($vars['use_wpautop']) ? 0 : 1;  
	   
	   // this is required both here and in the Intelligence module to avoid bugs
	    if($vars['answer_type'] == 'checkbox' and !empty($vars['calculate_checkbox_whole'])) {
		 	$vars['correct_gap_points'] = $vars['correct_checkbox_points'];
		 	$vars['incorrect_gap_points'] = $vars['incorrect_checkbox_points'];
		 }
		 
		// fill the gaps with case-sensitivity?
		if($vars['answer_type'] == 'gaps') {
			$vars['open_end_mode'] = empty($vars['sensitive_gaps']) ? '' : 'sensitive_gaps';
		} 
	} 
	
	static function add($vars) {
		global $wpdb;
		
		self :: prepare_vars($vars);
		
		// get max sort order
		if(empty($vars['sort_order'])) {
			if(empty($vars['add_first'])) {				
				$sort_order=$wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".WATUPRO_QUESTIONS."
				WHERE exam_id=%d", $vars['quiz']));
				$sort_order++;
			} else {
				// adding it as first question
				$sort_order = 1;				
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET sort_order = sort_order+1 WHERE exam_id=%d", $vars['quiz']));
			}
		}
		else $sort_order = intval($vars['sort_order']);	
		
		$elaborate_explanation = (empty($vars['do_elaborate_explanation']) or empty($vars['elaborate_explanation'])) ? '' : $vars['elaborate_explanation'];
		if(empty($vars['tags'])) $tags = ""; 
		else {
			if(preg_match("/^\|/", $vars['tags'])) $tags = str_replace(", ", "|", @$vars['tags']);
			else $tags = "|". str_replace(", ", "|", @$vars['tags']) . "|";			
		}  
		
		// design adjustments if any
		$design = '';
		if(!empty($vars['column_width']) or !empty($vars['gaps_width'])) {
			$design = serialize(array(
				"column_width" => intval(@$vars['column_width']),
				"gaps_width" => intval(@$vars['gaps_width']),
			));
		} 
		
		// open end display - accept file?
		if(!empty($vars['accept_file_upload'])) $vars['open_end_display'] .= '|file';
		
		// single-choice dropdown?
		if($vars['answer_type'] == 'radio' and !empty($vars['is_dropdown'])) $vars['open_end_display'] = 'dropdown';

		$sql = $wpdb->prepare("INSERT INTO ".WATUPRO_QUESTIONS." (exam_id, question, answer_type, 
			cat_id, explain_answer, is_required, sort_order, correct_condition, max_selections, is_inactive, is_survey, 
			elaborate_explanation, open_end_mode, tags, open_end_display, exclude_on_final_screen, hints, 
			compact_format, round_points, importance, calculate_whole, unanswered_penalty, truefalse,
			accept_feedback, feedback_label, reward_only_correct, discard_even_negative, difficulty_level, num_columns, 
			design, allow_checkbox_groups, correct_gap_points, incorrect_gap_points, is_flashcard, 
			dont_randomize_answers, reduce_points_per_hint, reduce_hint_points_to_zero, accept_rating, file_upload_required, 
			no_negative, max_allowed_points, limit_words, title, dont_explain_unanswered, use_wpautop) 
			VALUES(%d, %s, %s, %d, %s, %d, %d, %s, %d, %d, %d, %s, %s, %s, %s, %d, %s, %d, %d, %d, %d, 
				%f, %d, %d, %s, %d, %d, %s, %d, %s, %d, %f, %f, %d, %d, %f, %d, %d, %d, %d, %f, %d, %s, %d, %d)", 
			intval($vars['quiz']), $vars['content'], $vars['answer_type'], $vars['cat_id'], 
			$vars['explain_answer'], $vars['is_required'], $sort_order, $vars['correct_condition'], 
			$vars['max_selections'], $vars['is_inactive'], $vars['is_survey'], $elaborate_explanation, 
			$vars['open_end_mode'], $tags, $vars['open_end_display'], $vars['exclude_on_final_screen'], $vars['hints'], 
			$vars['compact_format'], $vars['round_points'], $vars['importance'], $vars['calculate_whole'], 
			$vars['unanswered_penalty'], $vars['truefalse'], $vars['accept_feedback'], $vars['feedback_label'], 
			$vars['reward_only_correct'], $vars['discard_even_negative'], $vars['difficulty_level'],
			$vars['num_columns'], $design, $vars['allow_checkbox_groups'], $vars['correct_gap_points'], 
			$vars['incorrect_gap_points'], $vars['is_flashcard'], $vars['dont_randomize_answers'],
			$vars['reduce_points_per_hint'], $vars['reduce_hint_points_to_zero'], $vars['accept_rating'], 
			$vars['file_upload_required'], $vars['no_negative'], $vars['max_allowed_points'], $vars['limit_words'], 
			$vars['title'], $vars['dont_explain_unanswered'], $vars['use_wpautop']);
		
		$wpdb->query($sql);
		
		$id = $wpdb->insert_id;
		
		if(watupro_intel()) {
			// extra fields in Intelligence module
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::edit($vars, $id);
		}
		
		return $id;
	}
	
	static function edit($vars, $id) {
		global $wpdb;
      self :: prepare_vars($vars);
		
		$elaborate_explanation = (empty($vars['do_elaborate_explanation']) or empty($vars['elaborate_explanation'])) ? '' : $vars['elaborate_explanation'];
		$tags = "|".str_replace(",", "|", str_replace(", ", "|", $vars['tags']) )."|";
		
		// open end display - accept file?
		if(!empty($vars['accept_file_upload'])) $vars['open_end_display'] .= '|file';
		
		// single-choice dropdown?
		if($vars['answer_type'] == 'radio' and !empty($vars['is_dropdown'])) $vars['open_end_display'] = 'dropdown';
		
		// design adjustments if any
		$design = '';
		if(!empty($vars['column_width']) or !empty($vars['gaps_width'])) {
			$design = serialize(array(
				"column_width" => intval(@$vars['column_width']),
				"gaps_width" => intval(@$vars['gaps_width']),
			));
		} 
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS."
			SET question=%s, answer_type=%s, cat_id=%d, explain_answer=%s, is_required=%d,
			correct_condition=%s, max_selections=%d, is_inactive=%d, is_survey=%d, elaborate_explanation = %s, 
			open_end_mode=%s, tags=%s, open_end_display=%s, exclude_on_final_screen=%d, hints=%s, 
			compact_format=%d, round_points=%d, importance=%d, calculate_whole=%d, unanswered_penalty=%f,
			truefalse=%d, accept_feedback=%d, feedback_label=%s, reward_only_correct=%d, discard_even_negative=%d,
			difficulty_level=%s, num_columns=%d, design=%s, allow_checkbox_groups=%d, correct_gap_points=%f, 
			incorrect_gap_points=%f, is_flashcard=%d, dont_randomize_answers=%d, reduce_points_per_hint=%f, 
			reduce_hint_points_to_zero=%f, accept_rating=%d, file_upload_required=%d, no_negative=%d, 
			max_allowed_points=%f, limit_words=%d, title=%s, dont_explain_unanswered=%d, use_wpautop=%d
			WHERE ID=%d", 
			$vars['content'], $vars['answer_type'], $vars['cat_id'], $vars['explain_answer'],
			$vars['is_required'],	$vars['correct_condition'], $vars['max_selections'], 
			$vars['is_inactive'], $vars['is_survey'], $elaborate_explanation, $vars['open_end_mode'], $tags, 
			$vars['open_end_display'], $vars['exclude_on_final_screen'], $vars['hints'], 
			$vars['compact_format'], $vars['round_points'], $vars['importance'], 
			$vars['calculate_whole'], $vars['unanswered_penalty'], $vars['truefalse'], 
			$vars['accept_feedback'], $vars['feedback_label'], $vars['reward_only_correct'], 
			$vars['discard_even_negative'], $vars['difficulty_level'], 
			$vars['num_columns'], $design, $vars['allow_checkbox_groups'], 
			$vars['correct_gap_points'], $vars['incorrect_gap_points'], $vars['is_flashcard'], $vars['dont_randomize_answers'],
			$vars['reduce_points_per_hint'], $vars['reduce_hint_points_to_zero'], $vars['accept_rating'], 
			$vars['file_upload_required'], $vars['no_negative'], $vars['max_allowed_points'], $vars['limit_words'], 
			$vars['title'], $vars['dont_explain_unanswered'], $vars['use_wpautop'], $id));
						
		if(watupro_intel()) {
			// extra fields in Intelligence module
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::edit($vars, $id);
		}		
	}	
	
	// backward compatibility. In old versions sort order was not given
	// so we'll make sure all questions have correct one when loading the page
	static function fix_sort_order($questions) {
		global $wpdb;
		
		foreach($questions as $cnt=>$question) {
			$cnt++;
			if(@$question->sort_order!=$cnt) {
				$wpdb->query("UPDATE ".WATUPRO_QUESTIONS." SET sort_order=$cnt WHERE ID={$question->ID}");
			}
		}
	}
	
	static function reorder($id, $exam_id, $dir) {
		global $wpdb;
		$questions_table=$wpdb->prefix."watupro_question";
		
		// select question
		$question=$wpdb->get_row($wpdb->prepare("SELECT * FROM $questions_table WHERE ID=%d", $id));
		
		if($dir=="up") {
			$new_order=$question->sort_order-1;
			if($new_order<0) $new_order=0;
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=sort_order+1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}
		
		if($dir=="down") {
			$new_order=$question->sort_order+1;			
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=sort_order-1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}		
			
		// change this one
		$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=%d WHERE ID=%d", 
			$new_order, $id));
	}
	
	// NEW reorder by using jQuery sortable
	static function reorder_sortable($exam_id, $questions) {
		global $wpdb;
		
		// fill all question IDs in array in the same way they come from the sortable Ajax call
		$qids = array(-1);

		foreach($questions as $question) {
			$id = intval(str_replace('question-', '', $question));
			$qids[] = $id;
		}
		
		// find the min sort order for the group
		$min_sort_order = $wpdb->get_var($wpdb->prepare("SELECT MIN(sort_order) FROM ".WATUPRO_QUESTIONS." 
			WHERE exam_id=%d AND ID IN (".implode(',', $qids).")", $exam_id));
		
		// go through the questions and increment the min for each of them
		foreach($qids as $qid) {
			if($qid == -1) continue;
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET sort_order=%d WHERE ID=%d", $min_sort_order, $qid));
			$min_sort_order++;
		}
	}
	
	// fill in_progress details
	function fill_in_progress($in_progress) {
		global $wpdb;
		
		if(!empty($in_progress)) {
	  		// check if we already fetched the answers. if not, fetch
	  		// this is to avoid queries on every question
	  		WTPQuestion :: $in_progress = $in_progress;
	  		if(empty($this->inprogress_details)) {
	  			$answers=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_STUDENT_ANSWERS." 
	  				WHERE taking_id=%d AND exam_id=%d", $in_progress->ID, $in_progress->exam_id));
	 			 				
	  			$this->inprogress_details = $this->inprogress_hints = $this->inprogress_snapshots = array();
	  			$this->num_hints_total = 0;
	  			foreach($answers as $answer) {
	  					$this->inprogress_details[$answer->question_id] = unserialize($answer->answer);
	  					$this->inprogress_freetext[$answer->question_id] = stripslashes($answer->freetext_answer);
	  					$this->inprogress_hints[$answer->question_id] = stripslashes($answer->hints_used);
	  					$this->inprogress_snapshots[$answer->question_id] = stripslashes($answer->snapshot);
	  					if(!empty($answer->feedback)) $this->inprogress_feedbacks[$answer->question_id]= $answer->feedback;
	  					$this->num_hints_total += $answer->num_hints_used;
	  			}
	  		}
	  } // end if(!empty($in_progress))   	
	  
	  
	} // end fill_in_progress
	
	// to display a question
	function display($ques, $qct, $question_count, $in_progress, $exam = null) {
		global $wpdb, $question_catids;
		$this->exam = $exam;
		$this->qct = $qct;
		$advanced_settings = unserialize(stripslashes(@$exam->advanced_settings));
		if(!empty($exam->ID)) self :: $advanced_settings = $advanced_settings;
		
		//print_r($ques);
		// should we display category header? (when quiz is paginated 1 category per page this is handled by watupro_cat_header())
		if(!empty($exam) and $exam->group_by_cat and (!in_array($ques->cat_id, $question_catids) or !empty($advanced_settings['cat_header_every_page'])) 
			and $exam->single_page != WATUPRO_PAGINATE_PAGE_PER_CATEGORY) {	
			if(empty($ques->cat_parent_id)) {
				// main category
				$tag = empty($advanced_settings['question_category_heading_tag']) ? 'h2' : $advanced_settings['question_category_heading_tag'];
			}
			else {
				// subcategory
				$tag = empty($advanced_settings['question_subcategory_heading_tag']) ? 'h3' : $advanced_settings['question_subcategory_heading_tag'];
			}
								
			echo "<$tag class='watupro-category-header'>".stripslashes(apply_filters('watupro_qtranslate', $ques->cat))."</$tag>";
			if(!empty($ques->cat_description)) echo "<div class='watupro-category-wrap'>".apply_filters('watupro_content', stripslashes(wpautop($ques->cat_description)))."</div>";
			$question_catids[] = $ques->cat_id;
		}		
				
		// fill in_progress once to avoid running multiple queries
		// it's called in show_exam but we are calling it here again to ensure in-progress will be filled in any case
		$this->fill_in_progress($in_progress);
	  $rtl_class = empty(self :: $advanced_settings['is_rtl']) ? '' : 'watupro-rtl';
	  //echo $rtl_class;
	  
	  // if there is snapshot, means we have called 'see answer'. In this case we should make the div below invisible
	  $nodisplay = '';
	  if(!empty($this->inprogress_snapshots[$ques->ID]) 
	  		and ($exam->live_result 
	  				or (!empty(self::$advanced_settings['single_choice_action']) and self::$advanced_settings['single_choice_action']=='show'))) {
	  	  $nodisplay = 'style="display:none;"';
	  }

	  // compact format version 1 or 2	
	  $compact_class = '';  
	  if($ques->compact_format == 1) $compact_class = " watupro-compact";
	  if($ques->compact_format == 3) $compact_class = " watupro-compact watupro-compact2";	  	  
	  if($ques->compact_format == 4) $compact_class = " watupro-compact watupro-compact3";
	  	  
	  $required_class = ($ques->is_required) ? 'watupro-required-'.$ques->ID : '';
	  $question_number = (empty(self :: $advanced_settings['dont_display_question_numbers']) and empty($this->practice_mode))  ? "<span class='watupro_num'>$qct. </span>"  : '';
		
		if(!empty($exam->is_likert_survey)) {
			$likert_question_align = '';
			if(!empty($advanced_settings['likert_question_align']) ) {
				$likert_question_align = 'text-align:'.$advanced_settings['likert_question_align'].';';
			}	
		   echo '<tr><td class="watupro-likert-question" style="'.$likert_question_align.'">';
	   }		
		
		echo "<div id='questionWrap-$question_count' $nodisplay class='$compact_class $rtl_class $required_class watupro-question-id-".$ques->ID."'>
			<div class='question-content'>";
			
		// replace {{{ID}}} if any
		$ques->question = str_replace('{{{ID}}}', $ques->ID, $ques->question);	
		
		// if [embed] shortcode is used 
		$ques->question = $GLOBALS['wp_embed']->run_shortcode($ques->question);
		
		if($ques->answer_type=='checkbox' and $ques->allow_checkbox_groups and strstr($ques->question, '{{{group-')) {
 		   // parse checkbox groups inside question contents
 		   $ques->question = WatuPROQuestionEnhanced :: parse_groups($ques, $this, $in_progress);
 		}
		
		if(watupro_intel() and ($ques->answer_type=='gaps' or $ques->answer_type=='sort' 
			or $ques->answer_type == 'matrix' or $ques->answer_type == 'nmatrix'  or $ques->answer_type == 'slider' )) {				
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::display($ques, $qct, $question_count, @$this->inprogress_details, @$this->practice_mode);
		}
		// this line used watupro_nl2br instead of wpautop(). Now wpautop() seems no longer breaking tables with extra new lines?
		else {
			if(empty($ques->use_wpautop)) echo watupro_nl2br( '<div>'.$question_number . self :: flag_review($ques, $qct) . stripslashes($ques->question).'</div>');
			else echo wpautop( '<div>'.$question_number . self :: flag_review($ques, $qct) . stripslashes($ques->question).'</div>');
		}
				
 		echo "<input type='hidden' name='question_id[]' id='qID_{$question_count}' value='{$ques->ID}' />";
 		echo "<input type='hidden' id='answerType{$ques->ID}' value='{$ques->answer_type}'>";
 		if($ques->is_required) echo "<input type='hidden' id='watupro-required-question-".$ques->ID."'>";
 		
 		if(!empty($exam->question_hints) ) $this->display_hints($ques, $in_progress);
 		
 		if($ques->answer_type != 'sort') echo  "<!-- end question-content--></div>"; // end question-content
 		
 		// checkbox questions that allows groups?
 		if($ques->answer_type != 'checkbox' or !$ques->allow_checkbox_groups or strstr($ques->question, '{{{group-')) {
 			 if(!empty($exam->is_likert_survey)) echo '</td>';
 		    $this->display_choices($ques, $in_progress); // the standard call for all questions
 		}
 		
 		if(!empty(self :: $advanced_settings['unselect']) and ($ques->answer_type == 'radio' or $ques->answer_type == 'checkbox')) {
 		   echo '<p><input type="button" value="'.__('Unselect', 'watupro').'" style="display:'.(empty($this->inprogress_details[$ques->ID]) ? 'none' : 'block').'" onclick="WatuPRO.unselect(this, '.$ques->ID.')" id="watuPROUnselect-'.$ques->ID.'"></p>';
 		}
 		
 		// accept feedback?
 		if($ques->accept_feedback) {
 			$feedback = empty($this->inprogress_feedbacks[$ques->ID]) ? '' : stripslashes($this->inprogress_feedbacks[$ques->ID]);
 			echo "<p>".stripslashes($ques->feedback_label)."<br>
 			<textarea name='feedback-{$ques->ID}' rows='3' cols='30' class='watupro-user-feedback' id='watuproUserFeedback{$ques->ID}'>$feedback</textarea></p>";
		}
 		
 		echo '<!-- end questionWrap--></div>'; // end questionWrap
 		
 		$advanced_settings = unserialize( stripslashes(@$exam->advanced_settings));		 
		if((!empty($advanced_settings['accept_rating']) and empty($advanced_settings['accept_rating_per_question']))
			or (!empty($advanced_settings['accept_rating_per_question']) and !empty($ques->accept_rating))) {
			echo '<div class="watupro-rating-wrap">'.__('Rate this question:', 'watupro').'<br> <div class="watupro-rating" data-rating-max="5" id="watuPRORatingWidget'.$ques->ID.'"></div></div>
			<input type="hidden" name="question_rating_'.$ques->ID.'" value="5" id="watuPRORatingWidget'.$ques->ID.'-val">';
		}
		
 		if(!empty($exam->is_likert_survey)) echo '</tr>';
	}
		
	// display the radio, checkbox or text area for answering a question
    // also take care for pre-selecting anything in case we are continuing on unfinished exam
    // $q_answers is a variable letting other functions replace the default q_answers array of the question without affecting the
    // question globally. Don't change this, seems a very odd PHP bug allows changing the global q_answers of the question
    // even when you choose a local variable in another function?!
  function display_choices($ques, $in_progress = null, $q_answers = null) {
		global $wpdb, $answer_display;
		  
  	  $ans_type = $ques->answer_type;
  	  if(empty($ans_type)) $ans_type = 'radio';
  	  $answer_class = '';
  	  $enumerator = self :: define_enumerator();
  	  $question_design = empty($ques->design) ? '' : unserialize(stripslashes($ques->design));
     $q_answers = empty($q_answers) ? $ques->q_answers : $q_answers;
     $rtl_class = empty(self :: $advanced_settings['is_rtl']) ? '' : 'watupro-rtl';
     $exam = $this->exam;
     $advanced_settings = unserialize(stripslashes(@$exam->advanced_settings));
     
     // the new likert survey table will reset some question settings if they are done
     if($ques->open_end_display == 'dropdown' and $exam->is_likert_survey) $ques->open_end_display = '';
     if($ques->is_flashcard and $exam->is_likert_survey) $ques->is_flashcard = 0;
      
      switch($ans_type) {
      	case 'textarea':
      	 echo "<div class='question-choices $rtl_class'>";
      	 // open end question
      	 $value = (!empty($this->inprogress_details[$ques->ID][0])) ? stripslashes($this->inprogress_details[$ques->ID][0]) : ""; 
      	 
      	 // open_end_display may also contain "file" upload allowance like this: medium|file
      	 $allow_file_upload = false;
			 if(strstr($ques->open_end_display, '|')) {
			 	  list($ques->open_end_display) = explode("|",   $ques->open_end_display);
			 	  if(!empty($this->exam->no_ajax)) $allow_file_upload = true;
			 }      	 
      	 
      	 switch($ques->open_end_display) {
      	 	 case 'text':      	 	 	
      	 	 	echo "<p><input type='text' name='answer-{$ques->ID}[]' id='textarea_q_{$ques->ID}' class='watupro-text' value=\"$value\" size='60'></p>";
      	 	 break;	
      	 	 case 'medium':
      	 	 case 'large':
      	 	 default:
      	 	 	 $class = (empty($ques->open_end_display) or $ques->open_end_display == 'medium') ? 'watupro-textarea-medium' : 'watupro-textarea-large';
      	 	 	 
					 // classes due to word limit?
					 if($ques->limit_words > 0) {
					 	$class .= ' watupro-word-count watuprowordcount-'.$ques->limit_words.'-'.$ques->ID;
					 }      	 	 	 
      	 	 	 
      	 	 	  echo "<p><textarea name='answer-{$ques->ID}[]' id='textarea_q_{$ques->ID}' class='$class' rows='5' cols='80'>$value</textarea>\n";
      	 	 	  if($ques->limit_words > 0) {
      	 	 	  		$words_typed = array_filter(preg_split('/\s/', $value));
      	 	 	  	   $num_words_typed = count($words_typed);
      	 	 	  	   if($num_words_typed < 0) $num_words_typed = 0;      	 	 	  	  
      	 	 	  	   echo '<div><span id="words_count_'.$ques->ID.'">'.$num_words_typed.'</span> '.__('words typed', 'watupro').'</div>';
      	 	 	  }
      	 	 	  echo "</p>";
      	 	 	 // echo "<p>".wp_editor($value, 'textarea_q_'.$ques->ID, array("textarea_name"=>'answer-'.$ques->ID.'[]'))."</p>";
      	 	 break;
      	 }        

			 // output file upload?
			 if($allow_file_upload) {
			 	$required_upload_class = empty($ques->file_upload_required) ? '' : 'watupro-file-upload-required-'.$ques->ID;
			 	echo "<p>".__('Upload file:', 'watupro')." <input type='file' name='file-answer-{$ques->ID}' class='watupro-file-upload $required_upload_class' id='watuproFileUpload".$ques->ID."-".$this->qct."'></p>";
			 }    	 
      	 
      	 echo "<!-- end question-choices--></div>"; 
      	break;
      	case 'radio':
      	case 'checkbox':
      	 	// these two classes define if the choices will display in columns
      		$wrap_columns_class = empty($ques->compact_format) ? 'watupro-choices-columns' : '';
      		$div_columns_class = ($ques->num_columns > 1 and empty($ques->compact_format)) ? 'watupro-' . $ques->num_columns.'-columns' : ''; 
      		$column_style = '';
      		if($ques->num_columns > 1 and empty($ques->compact_format) and !empty($question_design['column_width'])) {
      		   $column_style = 'style="width:'.intval($question_design['column_width']).'px;"';
      		   
      		} 
      		echo "<div class='question-choices $wrap_columns_class $rtl_class'>";
      		
      		// flashcard?
      		if($ans_type == 'checkbox' and $ques->is_flashcard) {
      			WatuPROFlashcard :: display($ques, $in_progress, $q_answers);
      			$column_style = 'style="display:none;"';
      		}
				
				// radios allow drop-down display. This is stored in the "open_end_display" field
				if($ques->open_end_display == 'dropdown') echo "<select name='answer-{$ques->ID}[]' id='dropdowQuestion-{$ques->ID}'>
				<option value=''>".__('- please select -', 'watupro')."</option>";      		
      		
      		// radio and checkbox
      		foreach ($q_answers as $ans_cnt => $ans) {      		
      			if(!empty($exam->is_likert_survey)) {
      				$likert_choice_align = '';
						if(!empty($advanced_settings['likert_choice_align']) ) {
							$likert_choice_align = 'text-align:'.$advanced_settings['likert_choice_align'].';';
						}	
      				echo '<td class="watupro-likert-choice" style="'.$likert_choice_align.'">';
      			}	
	        		if($answer_display == 2) {
	        			$answer_class = 'wrong-answer-label';
	        			if($ans->correct) $answer_class = 'correct-answer-label';
	        		}
	        		
	        		if($ques->truefalse and $ans_cnt >= 2) {
	        			echo "<!-- end question-choices--></div>";
	        			break;
	        		}
	        		
	        		$checked="";
					if(!empty($this->inprogress_details[$ques->ID])) {
							if(is_array($this->inprogress_details[$ques->ID])) {
								if(in_array($ans->ID, $this->inprogress_details[$ques->ID])) $checked=" checked ";
							}
							else {
								if($this->inprogress_details[$ques->ID]==$ans->ID) $checked=" checked ";
							}
					}	  
					
					// checked because "checked by default" is selected? This should work only if in progress is empty
					if(empty($this->inprogress_details[$ques->ID]) and !empty($ans->is_checked)) $checked = ' checked ';     		
	        			        		
	        		// show unselect button?
	        		$unselect_js = '';
	        		if(!empty(self :: $advanced_settings['unselect']) and ($ques->answer_type == 'radio' or $ques->answer_type == 'checkbox')) {
	        		   $unselect_js = 'WatuPRO.showHideUnselect(this, '.$ques->ID.')';
	        		}	  
	        		
	        		// max selection limit?
	        		$maxsel_js = $maxsel_class = '';
	        		if($ques->answer_type == 'checkbox' and $ques->max_selections > 0) {
	        		   if(!empty($unselect_js)) $unselect_js .= '; ';
	        			$maxsel_js = "onclick='".$unselect_js."return WatuPRO.maxSelections(".$ques->ID.",".$ques->max_selections.", this);'";
	        			$unselect_js = '';
	        			$maxsel_class = "watupro_max_selections-".$ques->max_selections;
	        		}   
	        		
	        		// if there is no maxsel_js, $unselect_js needs "onclick"
	        		if(empty($maxsel_js) and !empty($unselect_js)) {
	        		   $unselect_js = 'onclick="'.$unselect_js.'"';
	        		}  		
	        		
	        		if($ques->open_end_display == 'dropdown') {
	        			if(!empty($checked)) $checked = 'selected';
	        			echo "<option value='{$ans->ID}' $checked class='answer  $answer_class answerof-{$ques->ID}'>".stripslashes($ans->answer)."</option>";
	        		}
	        		else { // not dropdown
		        		if($enumerator) { 
		        			$enumerator_visible = '<i>' . $enumerator .'.</i>';
		        			$num_class = 'watupro-ansnum';
		        			$enumerator++;
		        		} 
		        		else {
		        			$enumerator_visible = '';
		        			$num_class = '';
		        		}
		        		echo "<div class='watupro-question-choice $num_class $div_columns_class' dir='auto' $column_style>$enumerator_visible<input type='$ans_type' name='answer-{$ques->ID}[]' id='answer-id-{$ans->ID}' class='answer  $answer_class answerof-{$ques->ID} $maxsel_class' value='{$ans->ID}' $checked $maxsel_js $unselect_js/>";
   					echo "<label for='answer-id-{$ans->ID}' id='answer-label-{$ans->ID}' class='$answer_class answer'><span>" . (!empty($exam->is_likert_survey) ? '' : stripslashes($ans->answer)) . "</span>";
   					// accept freetext answer?
   					if($ans->accept_freetext) {
   						$inprogress_freetext = empty($this->inprogress_freetext[$ques->ID]) ? '' : $this->inprogress_freetext[$ques->ID];
   						echo ' <span class="watupro-choice-freetext"><input type="text" name="freetext_'.$ans->ID.'" class="watupro-choice-freetext" id="watuPROFreeText'.$ans->ID.'" value="'.htmlentities($inprogress_freetext).'"></span>';
   					} // end free text answer field
   					
   					echo "</label></div>";
		        } // end if NOT dropdown
        	 } // end foreach answer   
        	 
        	 if($ques->open_end_display == 'dropdown') echo "</select>";
        	 if(!empty($exam->is_likert_survey)) echo '</td>';
        	 echo "<!-- end question-choices--></div>";
      	break;
      }      
    }
    
    // a small helper that will cleanup markup that shows correct/incorrect info
    // so unresolved questions can be displayed
    function display_unresolved($output) {
    	$output = WatuPRO::cleanup($output, 'web');
    	
    	// now remove correct-answer style
    	$output = str_replace('correct-answer','',$output);
    	$output = str_replace('user-answer-unrevealed','user-answer',$output); // do it back & forth to avoid nasty bug
    	$output = str_replace('user-answer','user-answer-unrevealed',$output);
    	
    	// remove hardcoded correct/incorrect images if any
    	// (for example we may have these in fill the gaps questions)
    	$output = str_replace('<img src="'.WATUPRO_URL.'correct.png" hspace="5">', '', $output);
    	$output = str_replace('<img src="'.WATUPRO_URL.'correct.png" hspace="10">', '', $output); // flashcards
    	$output = str_replace('<img alt="Correct" src="'.WATUPRO_URL.'correct.png" hspace="5">', '', $output);
    	$output = str_replace('<img src="'.WATUPRO_URL.'wrong.png" hspace="5">', '', $output);
    	$output = str_replace('<img src="'.WATUPRO_URL.'wrong.png" hspace="10">', '', $output); // flashcards
    	$output = str_replace('<img alt="Wrong" src="'.WATUPRO_URL.'wrong.png" hspace="5">', '', $output);
    	
    	// in case of Fill the gaps we have to remove the answer here
    	if(strstr($output, 'watupro-revealed-gap')) {
    		$output = preg_replace('/<span class="watupro-revealed-gap">(.*?)<\/span>/', '', $output);
    	}

    	return $output;	
    }
    
    // figure out if a question is correctly answered accordingly to the requirements
    // $answer is single value or array depending on the question type
    // $choices are the possible choices of this question
    // $user_grade_ids is passed by referrence and used only in personality quizzes
    // returns array($points, $is_correct, $is_empty)
    static function calc_answer($question, $answer, $choices = -1, &$user_grade_ids = null) {    	
		// points for unanswered question
		$empty_points = ($question->unanswered_penalty > 0 and !$question->is_survey) ? (0 - $question->unanswered_penalty) : 0; 
		$is_empty = 0; // by default we won't consider any unanswered, because some question types are always answered  
		
		// protect from odd bug
		if($question->answer_type == '') $question->answer_type = 'radio';
    
    	// negative points and unanswered questions are always incorrect
    	// but let intelligence module take care for gaps and matrix
    	if($question->answer_type != 'gaps' and $question->answer_type != 'matrix' and $question->answer_type != 'nmatrix') {
    		if(empty($answer)) return array($empty_points, 0, 1);
    	}
    	    	
    	// when textareas have no possible answers, they are always correct when answered and incorrect when not answered
    	if($question->answer_type == 'textarea' and !count($choices)) {    			
    		$is_correct = $question->is_survey ? 0 : 1; // if survey - not correct
    		if(!empty($answer[0])) return array(0, $is_correct, 0);    		
    		else return array($empty_points, 0, 1); // unanswered    	
    	} 
    	
    	global $wpdb;
    	
    	// when choices is -1 means we have not passed them and we have to select them
    	if($choices == -1) {
    		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." 
    			WHERE question_id=%d", $question->ID));
    	}
    	
    	// single-answer questions
    	if($question->answer_type=='radio') {
    		// answers are given however. We need to figure out whether the answer is correct
    		$is_correct = $points = 0;    		
    		$answer = $answer[0];
			if(empty($answer)) return array($empty_points, 0, 1); // unanswered and incorrect
    		foreach($choices as $choice) {
    			 if($choice->ID == trim($answer)) {
    			 		$points = $choice->point;
    			 		if($choice->correct) $is_correct = 1;    	
    			 		if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 	
    			 		break;
    			 }	
			}		
			if($question->round_points) $points = round($points, 1);	
			if($question->is_survey) $is_correct = 0;	
			return array($points, $is_correct, 0);				
    	}
 
 		// multiple answer and open-end			   	
		if($question->answer_type == 'checkbox' or $question->answer_type == 'textarea') {			
			// figure out maximum points and calculate received points
			$points = $max_points = $is_correct = 0;		
			$max_points = self :: max_points($question);
			
			foreach($choices as $choice) {				
				list($p) = self :: evaluate_choice($question, $answer, $choice, $user_grade_ids);
				$points += $p;				
			}
			
			if(empty($question->correct_condition) or $question->correct_condition == 'any') {
				 if($points > 0) $is_correct = 1;
			}
			else {				
				// max points required			
				if(!function_exists('bccomp')) die("BCMath functions for PHP are not enabled on your server. This is a standard package absolutely required for doing proper calculations. Please contact your hosting support to enable BCMath package");	
				if(bccomp($points, $max_points, 3) >= 0) $is_correct = 1;				
			}
		
			if($question->round_points) $points = round($points, 1);
			if($answer[0] == '') {$points = $empty_points; $is_empty = 1;} // unanswered question

         // if "treat this question as a whole" is selected, points will be recalculated accordingly to is_correct			
			if($question->calculate_whole) {
			   $points = $is_correct ? $question->correct_gap_points : $question->incorrect_gap_points;
		   }
			
			if($question->is_survey) $is_correct = 0;	

			return array($points, $is_correct, $is_empty);
		}
		
		// fill the gaps and sortable
		if(watupro_intel() and ($question->answer_type == 'gaps' 
			or $question->answer_type == 'sort' or $question->answer_type == 'matrix' or $question->answer_type == 'nmatrix' or $question->answer_type == 'slider')) {
			$is_correct = 0;
			list($points, $html, $max_points, $is_correct_calculated, $is_empty) = WatuPROIQuestion::process($question, $answer);
	
			if(empty($question->correct_condition) or $question->correct_condition == 'any') {
				 if($points > 0) $is_correct = 1;
			}
			else {
				// max points required		
				if(!function_exists('bccomp')) die("BCMath functions for PHP are not enabled on your server. This is a standard package absolutely required for doing proper calculations. Please contact your hosting support to enable BCMath package");			
				if(bccomp($points, $max_points, 3) >= 0) $is_correct = 1;
			}
			
			// when $is_correct_calculated is passed it should override $is_correct.
			// this is because in some question types, for example slider with slider_transfer_points=1 even a question with positive points can be wrong 
			if($is_correct_calculated === false) $is_correct = 0;
			if($is_correct_calculated === true) $is_correct = 1;
			
			if($question->round_points) $points = round($points, 1);
			if(empty($answer) and empty($points)) $points = $empty_points; // unanswered question
			if($question->is_survey) $is_correct = 0;	
			return array($points, $is_correct, $is_empty);
		}
		
		// return just in case
		return array(0, 0, 0);   
    }
    
    // calculate maximum points that can be achieved by the question   
    static function max_points($question) {
    	 $points = 0;
    	 
    	// if($question->is_survey) return 0;
    	 
    	 // sorting and fill the gaps questions
    	 if($question->answer_type == 'gaps') {
    	 	 $matches = array();
			 preg_match_all("/{{{([^}}}])*}}}/", $question->question, $matches);
			
			 $num_gaps = sizeof($matches[0]);
			 $points = $num_gaps * $question->correct_gap_points;
			 if($points > $question->max_allowed_points and $question->max_allowed_points > 0) $points = $question->max_allowed_points; 
			 if($question->round_points) $points = round($points, 1);			 
			 return $points;
    	 }
    	 
    	 if($question->answer_type == 'sort') {
    	 	 if($question->calculate_whole) return $question->correct_gap_points;			    	 	
    	 	
    	 	 $sort_values = explode("\n",trim(stripslashes($question->sorting_answers)));
    	 	 
    	 	 $points = sizeof($sort_values) * $question->correct_gap_points;
    	 	 if($points > $question->max_allowed_points and $question->max_allowed_points > 0) $points = $question->max_allowed_points; 
    	 	 if($question->round_points) $points = round($points, 1);
    	 	 return $points;
    	 }
    	 
    	  if($question->answer_type == 'matrix' or $question->answer_type == 'nmatrix') {
    	 	 if($question->calculate_whole) return $question->correct_gap_points;			    	 	
    	 	
    	 	 $points = sizeof($question->q_answers) * $question->correct_gap_points;
    	 	 if($points > $question->max_allowed_points and $question->max_allowed_points > 0) $points = $question->max_allowed_points; 
    	 	 if($question->round_points) $points = round($points, 1);			
    	 	 return $points;
    	 }
    	 
    	 // checkbox questions having "treat as a whole selected"
    	 if($question->answer_type == 'checkbox' and $question->calculate_whole) {
    	    $points = @$question->correct_gap_points;
    	    return $points;
    	 }
    	 
    	 // thereon further we have to have possible answers, otherwise points are zero
    	 if(empty($question->q_answers) or !is_array($question->q_answers)) return 0;
    	 
    	 // for now cover only the basic question types - single answer,multiple answer, open-end
    	 // take into account the possible limit of number of selections for multiple-choice (max_selections)
    	 // for 'open-end' questions and radios max_selction will be set to 1
    	 if($question->answer_type == 'radio') $question->max_selections = 1;

		 // we have to reorder choices so the max point ones are on top
		 $qanswers = $question->q_answers;
		 uasort($qanswers, array(__CLASS__, 'max_points_reorder'));	 
    	 
    	 $num_calculated = 0;    	 
    	 foreach($qanswers as $choice) {
    	 	 if($choice->point <= 0) continue; // skip these with no points or negative points (we need max!)
    	 	 if($question->max_selections > 0 and $num_calculated >= $question->max_selections) break; // no more selections than the max allowed
    	 	 
    	 	 $points += $choice->point;
    	 	 $num_calculated++;
    	 }
    	 if($points > $question->max_allowed_points and $question->max_allowed_points > 0) $points = $question->max_allowed_points; 
    	 if($question->round_points) $points = round($points, 1);		
    	 return $points;
	 } // end calc_answer
	 
	 // called by max_points to reorder the answers in a way that lets the best ones on top 
	 // so we can extract maximum points properly
	 static function max_points_reorder($a, $b) {
	 	 if ($a->point == $b->point) {
        return 0;
	    }
	    return ($a->point > $b->point) ? -1 : 1;
	 }
    
    // select all questions for an exam
    static function select_all($exam) {    	
    	global $wpdb;
    	$user_ID = get_current_user_id();
    	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
    		
    		// if specific question IDs are passed in the shortcode, disregard anything below and select those questions
    	   // this is both to allow using same exam with different specified questions and to allow the "user selects" addon
    	   if(!empty($exam->passed_question_ids)) return self :: select_specified($exam);

    		// order by
			$ob=($exam->randomize_questions==1 or $exam->randomize_questions==2 or $exam->pull_random) ? "RAND()":"sort_order,ID";
			//echo $ob;
			if($exam->random_per_category and $ob == 'RAND()') $ob = "cat, RAND()";			
			
			$limit_sql="";
			if($exam->pull_random and !$exam->random_per_category) {
				$limit_sql=" LIMIT ".$exam->pull_random;
			}
			
			// when we are pulling random questions from all we need to make sure important questions are included
			if($exam->pull_random) {
				if(strstr($ob, 'cat, RAND()')) $ob = str_replace("cat, RAND()", "importance DESC, cat, RAND()", $ob);
				else $ob = str_replace("RAND()", "importance DESC, RAND()", $ob);
			}
			
			
			$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
			$q_id_sql = '';
			if(watupro_intel()) $q_id_sql = WatuPROIExam :: reused_questions_sql($exam, 'tQ.ID'); 
			
			// limit per question difficulty level?
			$advanced_settings = unserialize( stripslashes($exam->advanced_settings));		

			$difficulty_sql = '';
			if(!empty($advanced_settings['difficulty_level'])) $difficulty_sql = $wpdb->prepare(" AND tQ.difficulty_level=%s ", $advanced_settings['difficulty_level']);
			
			// limit per question difficulty level based on user's restrictions			
			if(get_option('watupro_apply_diff_levels') == '1' and is_user_logged_in()) {
				$user_diff_levels = get_user_meta($user_ID, "watupro_difficulty_levels", true);
				if(!is_array($user_diff_levels)) $user_diff_levels = array();
				$user_diff_levels[] = ''; // add no level, otherwise questions with no level are not accessible
				
				if(!empty($user_diff_levels) and !empty($user_diff_levels[0])) {
					$difficulty_sql .= " AND tQ.difficulty_level IN ('".implode("', '", $user_diff_levels)."')";
				}
			}
			
			$qcat_id_sql = '';
			if(!empty($advanced_settings['question_category_id'])) $qcat_id_sql = $wpdb->prepare(" AND tQ.cat_id=%d ", $advanced_settings['question_category_id']);
			
			// pull X random question categories?
			$random_qcats_sql = '';
			if(!empty($advanced_settings['random_cats']) and is_numeric($advanced_settings['random_cats'])) {
				$qcats = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT(cat_id) FROM ".WATUPRO_QUESTIONS."
					WHERE exam_id=%d $q_id_sql", $q_exam_id ));
				$q_cat_ids = array();
				foreach($qcats as $qcat) $q_cat_ids[] = $qcat->cat_id;
				shuffle($q_cat_ids);
				$q_cat_ids = array_slice($q_cat_ids, 0, $advanced_settings['random_cats']);
				$random_qcats_sql  = " AND tQ.cat_id IN (".implode(', ', $q_cat_ids).") ";
			}
			
			// limit questions per tags?
			$tags_sql = '';
		   if(!empty($advanced_settings['tags'])) {
				$tags = explode(',', $advanced_settings['tags']);
				$tags = array_map('trim', $tags);		   	
		   	
		   	$tags_sql = " AND (";
				foreach($tags as $cnt => $tag) {
					if($cnt) $tags_sql .= " OR ";
					$tags_sql .= " tQ.tags LIKE '%|".sanitize_text_field(trim($tag))."|%' "; 
				}
				$tags_sql .= ')';			
		   }
			
			// user has not to see question already answered before?
			$unseen_sql = '';
			
			if((!empty($advanced_settings['dont_show_answered']) or !empty($advanced_settings['dont_show_correctly_answered'])) 
				and $exam->require_login and is_user_logged_in()) {					
				
				// first get any inprogress takings to get them out of the equation
				$inprogress_ids = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." 
					WHERE user_id=%d AND in_progress=1", $user_ID));
				$inids = array(0);
				foreach($inprogress_ids as $in_id) $inids[] = $in_id->ID;
				$inprogress_ids_sql = implode(',', $inids);		
				
				// if there is latest taking ID in user meta and dont_show_answered_restart is on, we have to add to unseen SQL
				$restart_unseen_sql = '';
				if(!empty($advanced_settings['dont_show_answered_restart'])) {
					
					$latest_taking_id = get_user_meta($user_ID, 'watupro_unseen_taking_id_'.$exam->ID, true);
					if(!empty($latest_taking_id)) $latest_taking_exists = $wpdb->get_row($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $latest_taking_id));
					if(!empty($latest_taking_exists)) {
						$restart_unseen_sql = $wpdb->prepare(" AND taking_id > %d ", $latest_taking_id);
					}
				}	 			
				
				// chosen to not display only correctly answered quesitions
				$correct_sql = '';	
				if(!empty($advanced_settings['dont_show_correctly_answered'])) $correct_sql = " AND (is_correct=1 OR tQ.is_survey=1) ";
				$unseen_sql = $wpdb->prepare(" AND tQ.ID NOT IN (SELECT question_id FROM ".WATUPRO_STUDENT_ANSWERS."
					WHERE user_id=%d AND answer!='' AND taking_id NOT IN ($inprogress_ids_sql) $correct_sql $restart_unseen_sql)", $user_ID);	
					
			}
						
			$sql = "SELECT tQ.*, tC.name as cat, tC.description as cat_description, 
			tC.parent_id as cat_parent_id 
			FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC
			ON tC.ID=tQ.cat_id
			WHERE tQ.exam_id IN ($q_exam_id) AND tQ.is_inactive=0 $difficulty_sql $unseen_sql $qcat_id_sql $tags_sql $q_id_sql $random_qcats_sql
			ORDER BY $ob $limit_sql";
			$questions = $wpdb->get_results($sql);
			
			// no questions due to $unseen_sql but selected that we should re-start in such case?
			if(!count($questions) and !empty($advanced_settings['dont_show_answered_restart'])) {
				$sql = str_replace($unseen_sql, '', $sql);
				$questions = $wpdb->get_results($sql);
				
				// also set the latest taking ID in user meta
				$latest_taking_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." 
					WHERE in_progress=0 AND user_id=%d AND exam_id=%d ORDER BY ID DESC LIMIT 1", $user_ID, $exam->ID));
				update_user_meta($user_ID, 'watupro_unseen_taking_id_'.$exam->ID, $latest_taking_id);
			}
			
			// when questions are pulled per category and randomized but NOT grouped, we have to shuffle them
			if( ($exam->randomize_questions==1 or $exam->randomize_questions==2) 
				and $exam->pull_random and $exam->random_per_category and !$exam->group_by_cat) shuffle($questions);
						
			return $questions;
    }
    
    // select specified questions
    static function select_specified($exam) {    	
    	global $wpdb;
    	
    	// extract the question IDs making sure there are no empty etc
		$exam->passed_question_ids = str_replace(' ', '', trim($exam->passed_question_ids));
    	$question_ids = explode(",", $exam->passed_question_ids);
    	$question_ids = array_filter($question_ids);
    	    	
    	$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
    	
    	// now select
    	$questions = $wpdb->get_results("SELECT tQ.*, tC.name as cat, tC.description as cat_description
			FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC
			ON tC.ID=tQ.cat_id WHERE tQ.exam_id IN ($q_exam_id) AND tQ.is_inactive=0
			AND tQ.ID IN (".implode(',', $question_ids).")");
		
		// now reorder accordingly to the passed order
		$ordered_questions = array();
		foreach($question_ids as $qid) {
			foreach($questions as $question) {
				if($question->ID == $qid) $ordered_questions[] = $question;
			}
		} 
		
		return $ordered_questions;	
    }
    
    // processes a question when submitting exam or toggling answer. Used in submit_exam and the toggle result button 
    // returns array:
    // $answer_text - answers as text
    // $current_text the question with answer for the %%ANSWERS%% variable $current_text
    // $unresolved_text - the unresolved questions for %%UNRESOLVED%% variable
    // $short_text - the questions with answers for the %%SHORT-ANSWERS%% variable 
    function process($_watu, $qct, $question_content, $ques, $ansArr, $correct, $points) {
    	if(empty($ques->answer_type)) $ques->answer_type = 'radio';
    	
    	// in case of a {{{split}}} tag (question into) we have to use only the second part
    	if(strstr($question_content, '{{{split}}}')) {
    		$parts = explode('{{{split}}}', $question_content);
    		$question_content = $parts[1];
    	} 
			    	
		$original_answer = ""; // this var is used only for textareas    	
		$answer_text = ""; // answers as text
		$is_answered = true; // let's default to answered and then set to false if adding "question was not answered" text
		$unresolved_text = "";
		
		$compact_class = '';  
	   if($ques->compact_format == 1) $compact_class = " watupro-compact";
	   if($ques->compact_format == 3) $compact_class = " watupro-compact watupro-compact2";
		
		$flagged_for_review = self :: flagged_for_review($ques, $qct);
		$question_number = empty(self :: $advanced_settings['dont_display_question_numbers']) ? "<span class='watupro_num'>$qct. ".$flagged_for_review."</span>"  : $flagged_for_review;
		$rtl_class = empty(self :: $advanced_settings['is_rtl']) ? '' : 'watupro-rtl';
		
		$enumerator = self :: define_enumerator();
		
		$wrap_columns_class = empty($ques->compact_format) ? 'watupro-choices-columns' : '';
      $div_columns_class = ($ques->num_columns > 1 and empty($ques->compact_format)) ? 'watupro-' . $ques->num_columns.'-columns ' : '';  
		
		if($ques->answer_type == 'gaps') {			
			// gaps are displayed in different way to avoid repeating the question
			$current_text = "<div class='$wrap_columns_class show-question [[watupro-resolvedclass]]'><div class='show-question-content'>" . $question_number;
			$short_text = $current_text;
		}	
    	else {
			$include_choices = true; // this is the default behavior. But on checkbox questions with group and flashcards $include choices will be false    		
    		    		
   		if($ques->answer_type=='checkbox' and $ques->allow_checkbox_groups and strstr($ques->question, '{{{group-')) {
    		   // parse checkbox groups inside question contents
    		   $question_content = WatuPROQuestionEnhanced :: process_groups($ques, $this, $ansArr, $enumerator);
    		   // echo count($ques->q_answers);
    		   $include_choices = false;
    		}
    		
    		$current_text = "<div class='$wrap_columns_class show-question $rtl_class [[watupro-resolvedclass]][[watupro-unanswered]]".$compact_class."'><div class='show-question-content'>"
	    		.$question_number . stripslashes($question_content) . "</div>\n";
	    	$short_text = $current_text;	// this needs to handle also checkbox groups better, NYI	
	    	
	    	if(!empty($include_choices)) {
	    	   $current_text .= "<div class='show-question-choices $rtl_class'>";
	    	   
	    	   /// sortable?
	    	   if($ques->answer_type == 'sort') {
	    	   	$horizontal_style = ($ques->compact_format == 2) ? 'watupro-sortable-horizontal' : '';
					$current_text  .= "<ul class='watupro-sortable ".$horizontal_style."'>";	    	   
	    	   }
				else $current_text .= "<ul>";
		   }
		}			        
		
		// replace the {{{ID}}} mask
		// replace {{{ID}}} if any
		$ques->question = str_replace('{{{ID}}}', $ques->ID, $ques->question);	
		$current_text = str_replace('{{{ID}}}', $ques->ID, $current_text);
		$short_text = str_replace('{{{ID}}}', $ques->ID, $short_text);
		
	   $class = 'answer';
	   $any_answers=false; // this is for textareas -is there any answer provided at all?
		
	   foreach ($ques->q_answers as $ans) {	   	
	   	// sometimes sortable (and other types?) of questions may have old answers remained when changed question type
	   	// maybe we should think about removing them when saving the question? For now this works
	   	if($ques->answer_type == 'sort') break;
	   	 
	      if(empty($include_choices)) continue;
	   	if($ques->answer_type == 'matrix' or $ques->answer_type == 'nmatrix' or $ques->answer_type == 'slider') continue;
	  		$user_answer_class = ($ques->is_survey or !empty($_watu->this_quiz->is_personality_quiz)) ? 'user-answer-unrevealed' : 'user-answer';
			$class = $div_columns_class . 'answer';			
			if( in_array($ans->ID , $ansArr) ) { $class .= ' '.$user_answer_class; }
			if($ans->correct == 1 and $ques->answer_type!='textarea' and !$ques->is_survey and empty($_watu->this_quiz->is_personality_quiz)) $class .= ' correct-answer';
			$is_open_end = ($ques->answer_type == 'textarea') ? true : false;
			
			$sr_text = watupro_screen_reader_text($class, $is_open_end);
			
			if($enumerator) { 
     			$enumerator_visible = $enumerator.'. ';
     			$enumerator++;
     		} else $enumerator_visible = '';
            
        if($ques->answer_type == 'textarea'):
             // textarea answers have only 1 element. Make comparison case insensitive
				 $original_answer=@$ansArr[0];
				 $ansArr[0]=strtolower(strip_tags(trim($ansArr[0])));
             $compare=strtolower($ans->answer);
             if(!empty($compare)): $any_answers=true; endif;
        else:
             $compare = $ans->ID;

				 // alter answer with freetext answer if needed				 
				 if( ($ques->answer_type == 'checkbox' or $ques->answer_type == 'radio') and $ans->accept_freetext and !empty($_POST['freetext_'.$ans->ID])) $ans->answer .= ' '.stripslashes($_POST['freetext_'.$ans->ID]);             
             
             if($ques->answer_type == 'checkbox' and $ques->is_flashcard) $current_text .= WatuPROFlashcard :: process($class, $ans, $compare, $sr_text);
             else $current_text .= "<li class='$class'><span class='answer'><!--WATUEMAIL".$class."WATUEMAIL-->" . stripslashes($enumerator_visible.$ans->answer) . "</span>$sr_text</li>\n";
        endif;    
		} // end foreach choice;
		
     // open end will be displayed here
     if($ques->answer_type=='textarea') {
     		 $user_answer_class = $ques->is_survey ? 'user-answer-unrevealed' : 'user-answer';
			
			 // repeat this line in case there were no answers to compare	
			 $answer_text = empty($original_answer) ? $ansArr[0] : $original_answer;
			 $ansArr[0] = strtolower($ansArr[0]);
			 
          $class .= ' '. $user_answer_class;
          if($correct) $class .= ' correct-answer';
          $sr_text = watupro_screen_reader_text($class, true);
          $current_text .= "<li class='$class'><span class='answer'>" . nl2br(stripslashes($answer_text)) . "</span>$sr_text</li>\n";
          
          // uploaded file?
          if(!empty($_FILES['file-answer-'.$ques->ID]['tmp_name'])) $current_text .= '<!--watupro-uploaded-file-'.$ques->ID.'-->';
     }
     
     if(($ques->answer_type=='gaps' or $ques->answer_type=='sort' 
     		or $ques->answer_type=='matrix' or $ques->answer_type=='nmatrix' or $ques->answer_type == 'slider') and watupro_intel()) {
     		list($points, $answer_text) = WatuPROIQuestion::process($ques, $ansArr);
     		$current_text .= $answer_text;
     }
    
     if(empty($answer_text)) $answer_text = $_watu->answer_text($ques->q_answers, $ansArr);
  		             
  		if($ques->answer_type != 'gaps' and !empty($include_choices)) $current_text .= "</ul>"; // close the ul for answers
  		if(empty($_POST["answer-" . $ques->ID])) {
  			$is_answered = false;
  			$current_text .= "<p class='unanswered watupro-unanswered'>" . __('Question was not answered', 'watupro') . "</p>";
  			
  			$current_text = str_replace('[[watupro-unanswered]]',' watupro-unanswered-question ', $current_text);
  			$short_text = str_replace('[[watupro-unanswered]]',' watupro-unanswered-question ', $short_text);
  		}
  		else {
  			$current_text = str_replace('[[watupro-unanswered]]','', $current_text); // replace unanswered mask with nothing
  			$short_text = str_replace('[[watupro-unanswered]]','', $short_text); // replace unanswered mask with nothing
  		}
  		
  		if(!$correct) $unresolved_text = $this->display_unresolved($current_text)."</div>";
  
		// close question-choices
		// do for all questions except checkbox with groups
		if(!empty($include_choices)) {
		  $current_text .= "</div>";  
		  if(!$correct) $unresolved_text .= "</div>";
		}
		
		// if there is user's feedback, display it too
		if($ques->accept_feedback and !empty($_POST['feedback-'.$ques->ID])) {
			$current_text .= "<p><b>".stripslashes($ques->feedback_label)."</b><br>".htmlspecialchars(stripslashes($_POST['feedback-'.$ques->ID]))."</p>";
			$short_text .= "<p><b>".stripslashes($ques->feedback_label)."</b><br>".htmlspecialchars(stripslashes($_POST['feedback-'.$ques->ID]))."</p>";
		}
  
		// if explain_answer, display it	
		$answer_feedback = $this->answer_feedback($ques, $correct, $ansArr, $points, $is_answered);	
		$current_text .= $answer_feedback; 
		if(!empty(self :: $advanced_settings['feedback_unresolved']) and !$correct) $unresolved_text .= $answer_feedback;
    
  		$current_text .= "</div>";
  		$short_text .= "<div class='show-question-choices'>{{{answerto-".$ques->ID."}}}</div>\n</div>";
  		//echo "TESTEC: ".wpautop($current_text);
  		$current_text = wpautop($current_text);  		
  		$short_text = wpautop($short_text);
  		
  		// apply filter to allow 3rd party changes.
  		$current_text = apply_filters( 'watu_filter_current_question_text', $current_text, $qct, $question_content, $correct );
  		
  		// remove checkmarks if so is selected
		if(!empty(self :: $advanced_settings['no_checkmarks']) or (!empty(self :: $advanced_settings['no_checkmarks_unresolved']) and !$correct) ) {
			$current_text = $this->display_unresolved($current_text);
		}	
  		
  		// if question is survey, unresolved should be empty
  		if($ques->is_survey) $unresolved_text = '';
  		
  		// clear any content that should not be shown - marked with the <!--watupro-hide-start--><!--watupro-hide-end--> comment
  		$start_comment = '<!--watupro-hide-start-->';
		$end_comment = '<!--watupro-hide-end-->'; 
		$current_text  = preg_replace('#('.preg_quote($start_comment).')(.*)('.preg_quote($end_comment).')#siU', '', $current_text );
		$unresolved_text  = preg_replace('#('.preg_quote($start_comment).')(.*)('.preg_quote($end_comment).')#siU', '', $unresolved_text );
		$short_text  = preg_replace('#('.preg_quote($start_comment).')(.*)('.preg_quote($end_comment).')#siU', '', $short_text );
  		
  		return array($answer_text, $current_text, $unresolved_text, $short_text); 
    } // end process()
    
    // displays the optional answerfeedback
    function answer_feedback($question, $is_correct, $ansArr, $points, $is_answered = true) {    	
		// don't display feedback if the question was not answered?
		if(!$is_answered and !empty($question->dont_explain_unanswered)) return "";    
    	//echo "HERE";
    	$feedback = "";
    	$feedback_contents = stripslashes($question->explain_answer);
		if(empty($feedback_contents)) return "";
		$toggle_feedback = empty(self :: $advanced_settings['toggle_answer_explanations']) ? 0 : 1;
		if($toggle_feedback) $toggle_feedback_button = stripslashes(rawurldecode(self :: $advanced_settings['toggle_answer_explanations_button']));
		
		// replace {{{ID}}} if any
		$feedback_contents = str_replace('{{{ID}}}', $question->ID, $feedback_contents);	
		
    	if(!empty($question->explain_answer)) {
    		if(!empty($question->elaborate_explanation)) {
				if($question->elaborate_explanation == 'boolean') {
					$parts = explode("{{{split}}}", $feedback_contents);
	    			if($is_correct and !empty($parts[0])) $feedback .= "<div class='watupro-main-feedback feedback-correct'>".$parts[0]."</div>";        			
	    			elseif(!empty($parts[1])) $feedback .= "<div class='watupro-main-feedback feedback-incorrect'>".$parts[1]."</div>";
	    		}
	    		
	    		if($question->elaborate_explanation == 'exact') {	  
					// handle slider questions	    		
	    		  	if($question->answer_type == 'slider') {
	    		  		foreach ($question->q_answers as $ans) {
		    				$steps = explode(",", $ans->answer);
		    				if(count($steps)  == 2) {
		    					if($ansArr[0] >= $steps[0] and $ansArr[0] <= $steps[1])  $feedback .= "<div class='watupro-choice-feedback'>".stripslashes($ans->explanation)."</div>"; 
		    				}
		    				else {
		    					// no steps, exact value
		    					if($ansArr[0] == $steps[0])  $feedback .= "<div class='watupro-choice-feedback'>".stripslashes($ans->explanation)."</div>"; 
		    				} 
		    			}
	    		  	}
	    		  	else {
	    		  		foreach ($question->q_answers as $ans) {
		    				if(in_array($ans->ID , $ansArr)) $feedback .= "<div class='watupro-choice-feedback'>".stripslashes($ans->explanation)."</div>"; 
		    			}
	    		  	}	
	    		}	// end explanatiuon of every possible answer
    		}
    		else  $feedback .= "<div class='watupro-main-feedback'>".$feedback_contents."</div>";    
    	}
    	
    	// discard points?
		if($points > 0 and !$is_correct and $question->reward_only_correct) $points = 0; 
		if($points and !$is_correct and $question->discard_even_negative) $points = 0;
		if($points < 0 and $question->no_negative) $points = 0;
    	
    	if($question->round_points) $points = round($points, 1);		
    	$points = number_format($points, 2);
    	$points = $points + 0;
    	$feedback = str_replace("{{{points}}}", $points, $feedback);    
    	
		// maximum points?
    	if(strstr($feedback, '{{{max-points}}}')) {
    		$max_points = self :: max_points($question);
    		$max_points = $max_points + 0;
    		$feedback = str_replace("{{{max-points}}}", $max_points, $feedback);
    	}
    	
    	$feedback = wpautop($feedback);
		
		// toggle feedback?
		if($toggle_feedback and !empty($feedback)) {
			$feedback = '<p><input type="button" value="'.$toggle_feedback_button.'" onclick="jQuery('."'#feedback-{$question->ID}'".').toggle();"></p>
			<div id="feedback-'.$question->ID.'" style="display:none;">' . $feedback . '</div>';
		}    	
    	
    	return $feedback;
    } // end feedback   
    
    // evaluates if a choice is within the user answer(s) and returns the points
    // used by self -> calc_answer method for multiple choice and textarea questions
    // @param true_if_selected boolean - when true, we'll return array of points and boolean showing whether the choice matches user's answer
    // $user_grade_ids - passed by referrence and used in personality quzzes, the same global as in calc_answers
    static function evaluate_choice($question, $answer, $choice, &$user_grade_ids = null) {    	
    	$points = 0;
    	$is_selected = false;
    	
    	if($question->answer_type == 'checkbox') {
		 	foreach($answer as $part) {
				 if($part == $choice->ID) { 
				 	$points += $choice->point; 
				 	$is_selected = true;
				 	if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
				 }
			}
		}  // end if checkbox
		
		if($question->answer_type == 'textarea') {
		   if(empty($question->open_end_mode) or $question->open_end_mode != 'exact_sensitive') $answer[0] = mb_strtolower(@$answer[0]);
			$answer = trim($answer[0]); // user answer			
			$answer = stripslashes($answer);
			$answer = watupro_convert_smart_quotes($answer);
			if(empty($question->open_end_mode) or $question->open_end_mode != 'exact_sensitive') $choice->answer = mb_strtolower($choice->answer);
			$compare = trim($choice->answer); // the choice given
			$compare = stripslashes($compare);
			$compare = watupro_convert_smart_quotes($compare);			
			
			if($compare === '' or $answer === '') return array(0, false);
				 
 			switch(@$question->open_end_mode) {
 			 	 case 'contained': // the choice is contained in the user answer 			 	 
 			 	  	 if(strstr($answer, $compare) or $answer == $compare) { 
 			 	  	 	$points = $choice->point; 
 			 	  	 	$is_selected = true;
 			 	  	 	if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
 			 	  	 }
			 	 break;    			 	 
			 
			 	 case 'contains': // the given choice contains the user answer
 			 	 	 if(strstr($compare, $answer) or $answer == $compare) {
 			 	 	 	$points = $choice->point; 
 			 	 	 	$is_selected = true;
 			 	 	 	if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
 			 	 	 }
			 	 break;
 			 	 
 			 	 // correct in both cases (contains, contained)
 			 	 case 'loose': 			 	 
 			 	 	 if(strstr($answer, $compare) or strstr($compare, $answer) or $answer == $compare) { 			 	 	 		
 			 	 	 		$points = $choice->point; 
 			 	 	 		$is_selected = true;
 			 	 	 		if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
 			 	 	 	}
 			 	 break;
 			 	 	
 			 	 case 'exact':
 			 	 	if($compare == $answer or strtolower($compare) == strtolower($answer) or mb_strtolower($compare) == mb_strtolower($answer)) {		 	 	  
		 	 	   	$points = $choice->point; 
		 	 	   	$is_selected = true;
		 	 	   	if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
		 	 	   }
		 	 	 break;  
		 	 	 
 			 	 case 'exact_sensitive': 
 			 	 default: // defaults to strict because this is how it used to be 			 	
 			 	   if($compare == $answer) {		 	 	  
		 	 	   	$points = $choice->point; 
		 	 	   	$is_selected = true;
		 	 	   	if(!empty($choice->grade_id)) self :: update_personality_grades($choice, $choice->point, $user_grade_ids); 
		 	 	   }
 			 	 break;
 			}			
		} // end if textarea
		
		return array($points, $is_selected);
    } // end evaluate choice
    
    // display question hints
    // using $in_progress we must display the hints that were shown already
    function display_hints($question, $in_progress = null) {
    	if(empty($question->hints)) return "";    	
    	$get_hints_link = true;    	
    	if(empty($this->exam->question_hints)) return "";
    	
    	list($per_quiz, $per_question) = explode("|", $this->exam->question_hints);    	
    	
		$current_hints = empty($this->inprogress_hints[$question->ID]) ? "" : $this->inprogress_hints[$question->ID];
		
		if($in_progress and $per_quiz and $this->num_hints_total >= $per_quiz) $get_hints_link = false;
		
		// now check per question
		if($in_progress and $per_question and $get_hints_link) {
			$num = sizeof(explode('watupro-hint', $current_hints)) - 1;
			if($num >= $per_question) $get_hints_link = false;
		}
		    	    	
    	// wrap div
    	echo "<div class='watupro-question-hints'>";
		if($get_hints_link) echo "<p id='questionHintLink".$question->ID."'><a href='#' onclick='WatuPRO.getHints(".$question->ID.");return false;'>".__('[Get Hints]', 'watupro')."</a></p>";
		echo "<div id='questionHints".$question->ID."'>".$current_hints."</div>";
    	echo "</div>";
    }
    
    // define enumeration for answers
    static function define_enumerator() {
    	  $enumerate = empty(self :: $advanced_settings['enumerate_choices']) ? false : self :: $advanced_settings['enumerate_choices'];
	  	  // $enumerate = 'cap_letter'; // TEMP!!!
	  	  $enumerator = '';
	  	  if($enumerate) {
	  	  		switch($enumerate) {
	  	  			case 'cap_letter': $enumerator = 'A'; break;
					case 'small_letter': $enumerator = 'a'; break;
					case 'number': $enumerator = '1'; break;
					default: $enumerator = ''; break;
	  	  		}
	  	  }
	  	  
	  	  return $enumerator;
	 }
	 
	 // mark for review icon
	 static function flag_review($question, $qct) {
	 	$allow = empty(self :: $advanced_settings['flag_for_review']) ? false : self :: $advanced_settings['flag_for_review'];
	 	if(!$allow) return '';
	 	
	 	$ui = get_option('watupro_ui');
	 	if(empty($ui['flag_review'])) $ui['flag_review'] = '';
	 	
	 	// in progress?
	 	$marked_class = '';
		$filename = 'mark-review'.$ui['flag_review'].'.png';
	 	if(!empty(self :: $in_progress)) {
	 		$marked_for_review = self :: $in_progress->marked_for_review;
	 		$marked_for_review = unserialize($marked_for_review);
	 		
	 		if(!empty($marked_for_review['question_ids']) and is_array($marked_for_review['question_ids']) 
	 			and in_array($question->ID, $marked_for_review['question_ids'])) {
	 			$marked_class = ' marked ';
	 			$filename = 'unmark-review'.$ui['flag_review'].'.png';
	 		}
	 	}
	 	
	 	// flag for review allowed
	 	// for now let's not worry about "in progress"
	 	return '<img src="'.WATUPRO_URL.'img/'.$filename.'" class="'.$marked_class.'watupro-mark-review question-id-'.$question->ID.' question-cnt-'.$qct.'" alt=""  title="'.__('Flag for review', 'watupro').'" id="watuproMarkHandler'.$question->ID.'">';
	 }
	 
	 // is the question flagged for review? This shows the red flag on the processed questions so the user knows they have been flagged
	 // $_POST['watupro_current_taking_id'] should exist here
	 static function flagged_for_review($question, $qct) {
		 	global $wpdb;
		 	$taking_id = empty($_POST['watupro_current_taking_id']) ? $_POST['watupro_current_taking_id'] : 0;
		 	
		 	$allow = empty(self :: $advanced_settings['flag_for_review']) ? false : self :: $advanced_settings['flag_for_review'];
		 	if(!$allow or empty($taking_id)) return '';
	 		
	 		$marked_for_review = $wpdb->get_var($wpdb->prepare("SELECT marked_for_review FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE ID=%d", $taking_id));
				
			if(empty($marked_for_review)) $marked_for_review = array("question_ids"=>array(), "question_nums"=>array());
			else $marked_for_review = unserialize($marked_for_review);	
	
			foreach($marked_for_review['question_ids'] as $cnt=>$id) {
				if($id == $question->ID) {
					$ui = get_option('watupro_ui');
	 				if(empty($ui['flag_review'])) $ui['flag_review'] = '';						
					$filename = 'unmark-review'.$ui['flag_review'].'.png';
					
					return '<img src="'.WATUPRO_URL.'img/'.$filename.'" alt="'.__('Flagged for review', 'watupro').'"  title="'.__('Flagged for review', 'watupro').'">';
					break;
				}
			}
			
			return "";
	 } // end flagged for review
	 
	 // properly assign personality grade points. So if user gives 2 points to a selection, then it assigns 2 matches not just one
	 static function update_personality_grades($choice, $points, &$user_grade_ids) {
	 	 if(empty($choice->grade_id)) return true;
	 	 
	 	 if($points <= 0) $points = 1;
	 	 
	 	 for($i=0; $i < $points; $i++) $user_grade_ids[] = $choice->grade_id;
	 } // end update_personality_grades
	 
	 // gets a short resume from a question;
	 static function summary($question, $id = true, $use_title = true) {
	 	if($id) $qid_str = "(ID: ".$question->ID.") ";
	 	else $qid_str = '';
	 	
		if(!empty($question->title) and $use_title) return $qid_str.stripslashes($question->title);
		
		$question_contents = stripslashes($question->question);	
		
		// remove special tags like gaps and 
		$matches = array();
		preg_match_all("/{{{([^}}}])*}}}/", $question_contents, $matches);	
		foreach($matches[0] as $cnt => $match) {		
			$question_contents = str_replace($match, '_____', $question_contents);			
		}
		
		$words = preg_split('/\s/', strip_tags($question_contents));
		$words = array_slice($words, 0, 10);
		$summary = $qid_str.implode(" ", $words).'...';	
		return $summary;
	 } // end summary()
} // end class