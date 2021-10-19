<?php
// edge cases & enhanced features for handling questions - to keep the main Question model shorter and more readable
class WatuPROQuestionEnhanced {
	// static array that holds all table-like headers in likert table quizzes
	public static $table_header;	
	
   // parses groups of checkboxes in questions that support this
   // $question - the $question data array / object
   // $_question - the WTPQuestion class object 
   static function parse_groups($question, $_question, $in_progress = null){
      global $wpdb;  
      $choices = $question->q_answers;
      
      // split question choices based on their groups
      $groups = array();
      foreach($choices as $choice) {
         if(!empty($choice->chk_group)) {
            if(!isset($groups[$choice->chk_group])) $groups[$choice->chk_group] = array();
            $groups[$choice->chk_group][] = $choice;
         }      
      }
      
      // now parse the group tags within the question
      foreach($groups as $key => $group) {         
         // call display_choices
         $q = $question;
         $q_answers = $group;
         ob_start();
         $_question->display_choices($q, $in_progress, $q_answers);
         $content = ob_get_clean();
         
         // replace the {{{group-N}}} variable
         $question->question = str_replace('{{{group-'.$key.'}}}', $content, $question->question);
      } // end foreach
      
      return $question->question;
   } // end parse_groups
   
   // process the group checkbox questions similar to the displaying 
   static function process_groups($question, $_question, $ansArr, $enumerator){
      global $wpdb;  
      $choices = $question->q_answers;
      $div_columns_class = ($question->num_columns > 1 and empty($question->compact_format)) ? 'watupro-' . $question->num_columns.'-columns' : ''; 
      
      // split question choices based on their groups
      $groups = array();
      foreach($choices as $choice) {
         if(!empty($choice->chk_group)) {
            if(!isset($groups[$choice->chk_group])) $groups[$choice->chk_group] = array();
            $groups[$choice->chk_group][] = $choice;
         }      
      }
      
      $question_text = $question->question;
      
      // now parse the group tags within the question
      foreach($groups as $key => $group) {
         $ques = $question;
         $question_answers = $group;
         $current_text = '<ul>';
      
         foreach ($question_answers as $ans) {
   	  		$user_answer_class = ($ques->is_survey or !empty($_watu->this_quiz->is_personality_quiz)) ? 'user-answer-unrevealed' : 'user-answer';
   			$class = $div_columns_class . 'answer';			
   			if( in_array($ans->ID , $ansArr) ) { $class .= ' '.$user_answer_class; }
   			if($ans->correct == 1 and $ques->answer_type!='textarea' and !$ques->is_survey) $class .= ' correct-answer';
   			
   			if($enumerator) { 
        			$enumerator_visible = $enumerator.'. ';
        			$enumerator++;
        		} else $enumerator_visible = '';
            $sr_text = watupro_screen_reader_text($class);   
            $current_text .= "<li class='$class'><span class='answer'><!--WATUEMAIL".$class."WATUEMAIL-->" . stripslashes($enumerator_visible.$ans->answer) . "</span>$sr_text</li>\n";
   		} // end foreach choice;
   		$current_text .= '</ul>';
   		
   		$question_text = str_replace('{{{group-'.$key.'}}}', $current_text, $question_text);
      } // end foreach group
      
      return $question_text;
   } // end parse_groups
   
   // display likert survey table
	static function likert_table_header($exam, $question) {
		if(empty($exam->is_likert_survey)) return "";
		
		// check if we already displayed header
		$answers = $question->q_answers;
		$key_str = ''; // we'll construct string from answers to check if this was the last heading
		foreach($answers as $ans) $key_str .= stripslashes($ans->answer);
		
		if($key_str == self :: $table_header) return "";
		
		if(!empty(self :: $table_header)) {
			// there was a previous table but it was not the same table. Therefore we have to close it.
			echo '</tbody></table>';
		}
		
		if($question->answer_type != 'radio' and $question->answer_type != 'checkbox') return '';
		
		self :: $table_header = $key_str;
		
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));		
		
		// cell width?
		$cell_width = '';
		if(!empty($advanced_settings['likert_cell_width_type']) and $advanced_settings['likert_cell_width_type'] == 'fixed') {
			$cell_width = 'width:'.$advanced_settings['likert_cell_width'].'px;';
		}
		$header_align = '';
		if(!empty($advanced_settings['likert_header_align']) ) {
			$header_align = 'text-align:'.$advanced_settings['likert_header_align'].';';
		}
		
		// now really open table and construct header
		echo '<table class="watupro-stripped-columns watupro-border watupro-likert-survey">
			<thead><tr><th width="40%">&nbsp;</th>';
		foreach($answers as $answer) {
			echo '<th style="'.$cell_width.$header_align.'">'.stripslashes($answer->answer).'</th>';
		}
		echo '</tr></thead><tbody>';	
		
	} // end likert_table_header
	
	// question intro. If the question has an intro, it should be displayed before it. Based on the pagination the question itself will be hidden or not
	public static function intro(&$question, $exam) {
		if(!strstr($question->question, '{{{split}}}')) return false;
		
		$parts = explode('{{{split}}}', $question->question);
		list($intro, $question->question) = $parts;
		
		echo '<div class="watupro-question-intro">';
		if(empty($question->use_wpautop)) echo watupro_nl2br(stripslashes($intro));
		else echo wpautop( stripslashes($intro));
		echo '</div>';		
	} // end intro
}