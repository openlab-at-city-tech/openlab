<?php
/* WatuPRO Advanced Import Class: 
* Supports additional import formats (and in some moment will replace the legacy formats):
* Simple: Question, Type, Answer, Points, Is Correc, Answer, Points, Is Correct
* Advanced: Same as the other format but with all fields (this once changes often)
* Aiken http://docs.moodle.org/20/en/Aiken_Format (both from txt file and from CSV) */
class WatuPROImport {
	// this function will display the import form and dispatch 
	// the import process to the proper function
	static function dispatch() {
		global $wpdb;

		// select the quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['id']));		
		
		if(!empty($_POST['watupro_import'])) {
			if(empty($_FILES['csv']['name'])) wp_die(__('Please upload file', 'watupro'));
			
			// check for non UTF-8 encoding
			$content = file_get_contents($_FILES['csv']['tmp_name']);
			if(!mb_detect_encoding($content, 'UTF-8', true)) $non_utf8_error = true;
			
			$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS);
			
			switch($_POST['import_format']) {
				case 'simple':
				case 'advanced':				
					$row = 0;
					ini_set("auto_detect_line_endings", true);
					$delimiter = sanitize_text_field($_POST['delimiter']);
					if($delimiter=="tab") $delimiter="\t";
					if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE) {
						 if(empty($_POST['import_fails'])) {		
						    while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {	    	  
						    	  $row++;	
						        if(empty($data)) continue;			  			  
						        if(!empty($_POST['skip_title_row']) and $row == 1) continue;	        
						        self :: import_question_dispatcher($data, $quiz, $cats);      
						    } // end while
						 } else {
						 	// the customer says that import fails - let's try the handmade import function
						 	while(($csv_line = fgets($handle, 10000)) !== FALSE) {
						 		$row++;
						 		if(empty($csv_line)) continue;			  			  
						      if(!empty($_POST['skip_title_row']) and $row == 1) continue;
						      $data = watupro_parse_csv_line($csv_line);		         
						      self :: import_question_dispatcher($data, $quiz, $cats);      
						 	} // end while
						 }	// end alternate CSV parsing
						 $result = true;
					} // end if $handle
					else $result = false;		
				break;
				case 'aiken':
					$row = 0;
					ini_set("auto_detect_line_endings", true);
					
					if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE) {
						$result = true;						
						self :: import_questions_aiken($quiz, $handle, $cats);
					}
					else $result = false;
				break;
			}
		}
		
		if(@file_exists(get_stylesheet_directory().'/watupro/advanced-import.html.php')) require get_stylesheet_directory().'/watupro/advanced-import.html.php';
		else require WATUPRO_PATH."/views/advanced-import.html.php";
	}
	
	// calls the appropriate import question function depending on the import type selected
	static function import_question_dispatcher($data, $quiz, &$cats) {
		switch($_POST['import_format']) {
			case 'simple': self :: import_question_simple($data, $quiz); break;
			case 'advanced': self :: import_question_advanced($data, $quiz, $cats); break;			
		}
	}
	
	// imports a question in the simple format
	static function import_question_simple($data, $quiz) {
		global $wpdb;
			// handle true/false subtype
	  		$truefalse = 0;
	  		if(@$data[1] == 'true/false') {
	  			$truefalse = 1;
	  			$data[1] = 'radio';
	  		}
	  		
	  		if(!function_exists('get_magic_quotes_gpc') or !get_magic_quotes_gpc()) $data[0] = addslashes($data[0]);
	  		
	  		// make sure answer type defaults to radio in case it's not properly formatted.
	  		$data[1] = empty($data[1]) ? 'radio' : $data[1];
	  		if($data[1] != 'radio' and $data[1] != 'checkbox' and $data[1] != 'textarea') $data[1] = 'radio';
	  		
		  $question_array = array("content"=>$data[0], "answer_type"=>@$data[1], "quiz"=>$quiz->ID, "cat_id"=>0,
		 	'is_required'=> $data[2], 'explain_answer'=>$data[3], 'max_selections'=>0, 'truefalse' => $truefalse );	
        $qid = WTPQuestion::add($question_array);
        
        // add answers
        $data = array_slice($data, 4);  		
        
        $answers=array();
  		  $step=1;
        foreach($data as $cnt=>$d) {			  			
	  			if($step==1) {
	  				$answer=array();
	  				$answer['answer']=$d;			  				
	  				$step=2;
	  				continue;
	  			}
	  			if($step==2) {
	  				$answer['is_correct']=$d;	
					$step=3;
					continue;
	  			}
	  			if($step==3) {
	  				$answer['points']=$d;
	  				$step=1;
	  				$answers[]=$answer;
	  			}
	  		} // end filling answers array
	  			  		
	  		// finally insert them	
			$vals=array();
			foreach($answers as $cnt=>$answer) {
				if($answer['answer']==='') continue;
				$cnt++;
				if(!function_exists('get_magic_quotes_gpc') or !get_magic_quotes_gpc()) $answer['answer'] = addslashes($answer['answer']);
				$vals[]=$wpdb->prepare("(%d, %s, %s, %s, %d)", $qid, $answer['answer'], 
					$answer['is_correct'], $answer['points'], $cnt);
			}
			$values_sql=implode(",",$vals);
			
			if(count($answers)) { $wpdb->query("INSERT INTO ".WATUPRO_ANSWERS." (question_id,answer,correct,point, sort_order) 
				VALUES $values_sql"); }		    
	} // end import question simple
	
	// imports a question from the advanced format
	static function import_question_advanced($data, $quiz, &$cats) {
		global $wpdb;
			// handle true/false subtype
	  		$truefalse = 0;
	  		if($data[1] == 'true/false') {
	  			$truefalse = 1;
	  			$data[1] = 'radio';
	  		}		
	  		
	  		// shall we not add the Latex codition here too? !empty($_POST['latex_problems'])
	  		if(!function_exists('get_magic_quotes_gpc') or !get_magic_quotes_gpc()) {
	  			$data[0] = addslashes($data[0]);
	  			$data[4] = addslashes(@$data[4]);
				$data[12] = addslashes($data[12]);
	  		} 
	  		
			$cat_id = WTPCategory::discover(@$data[3], $cats);
		   $question_array = array("content"=> $data[0], "answer_type"=>$data[1], "quiz"=>$quiz->ID, "cat_id"=>$cat_id,
		 	'explain_answer'=> @$data[4], 'is_required'=>$data[5], 'truefalse' => $truefalse );	
		 	$question_array['correct_condition'] = $data[6];
	  		$gapdata = explode("/", $data[7]); // handle both gap & sort
	  		$question_array['correct_gap_points'] = $question_array['correct_sort_points'] = $question_array['slide_from'] = @$gapdata[0];
			$question_array['incorrect_gap_points'] = $question_array['incorrect_sort_points'] = $question_array['slide_to'] = @$gapdata[1];
			
			$question_array['sorting_answers'] = $data[8];
			$question_array['max_selections'] = $data[9];
			$question_array['is_inactive'] = $data[10];
			$question_array['is_survey'] = $data[11];
			$question_array['elaborate_explanation'] = $data[12];
			if(!empty($data[12])) $question_array['do_elaborate_explanation'] = 1;
			$question_array['open_end_mode'] = $data[13];
			$question_array['tags'] = $data[14];
			$question_array['open_end_display'] = $data[15];
			$question_array['exclude_on_final_screen'] = $data[16];
			$question_array['hints'] = $data[17];
			$question_array['compact_format'] = $data[18];
			$question_array['round_points'] = $data[19];
			$question_array['importance'] = $data[20];
			$question_array['feedback_label'] = ''; // temp as it's not yet included in export
			// sorting answers may contain ||| or |||||| for new lines separator
			$question_array['sorting_answers'] = str_replace('||||||', "\n", $question_array['sorting_answers']);
			$question_array['sorting_answers'] = str_replace('|||', "\n", $question_array['sorting_answers']);
			$question_array['difficulty_level'] = $data[21];
			$question_array['unanswered_penalty'] = $data[22];
			$question_array['gaps_as_dropdowns'] = $data[23];
			$question_array['num_columns'] = $data[24];
			$question_array['dont_randomize_answers'] = $data[25];
			$question_array['title'] = $data[26];
						
			
        $qid = WTPQuestion::add($question_array);
        
        // add answers
        $data = array_slice($data, 27);  		

        $answers=array();
  		  $step=1;
        foreach($data as $cnt=>$d) {			  			
	  			if($step==1) {
	  				if(!function_exists('get_magic_quotes_gpc') or !get_magic_quotes_gpc()) $d = addslashes($d);
	  				$answer = array();
	  				$answer['answer']=$d;			  				
	  				$step=2;
	  				continue;
	  			}
	  			if($step==2) {
	  				$answer['is_correct'] = $d;	
					$step=3;
					continue;
	  			}
	  			if($step==3) {
	  				$answer['points']=$d;
	  				$step=1;
	  				$answers[]=$answer;
	  			}
	  		} // end filling answers array
	  			  		
	  		// finally insert them	
			$vals=array();
			foreach($answers as $cnt=>$answer) {
				if($answer['answer']==='') continue;
				$cnt++;
				if(!empty($_POST['latex_problems'])) $answer['answer'] = addslashes($answer['answer']);
				$vals[]=$wpdb->prepare("(%d, %s, %s, %s, %d)", $qid, $answer['answer'], 
					$answer['is_correct'], $answer['points'], $cnt);
			}
			$values_sql=implode(",",$vals);
			
			if(sizeof($answers)) { $wpdb->query("INSERT INTO ".WATUPRO_ANSWERS." (question_id,answer,correct,point, sort_order) 
				VALUES $values_sql"); }		    
	}
	
	static function import_questions_aiken($quiz, $handle, &$cats) {
		global $wpdb;
		
		$start_question = true;
		$qid = 0; // question ID
		$answers = array();
		while(($aiken_line = fgets($handle, 10000)) !== FALSE) {
			$aiken_line = trim($aiken_line);
			if(empty($aiken_line)) continue;
			
			if(!empty($_POST['latex_problems'])) $aiken_line = addslashes($aiken_line);
			
			if($start_question) {				
				// let's import the question and get its ID for the answers				
				$question_array = array("content"=>$aiken_line, "answer_type"=>'radio', "quiz"=>$quiz->ID, "cat_id"=>0,
		 			'explain_answer'=> '', 'is_required'=>0, 'max_selections'=>0 );	
		 		$qid = WTPQuestion::add($question_array);
		 		$start_question = false;
		 		continue;	
			}
			
			// import answers
			
			if(!$start_question) {
				// the correct answer is here: let's find it, assign it, and set start_question to true
				if(preg_match("/^ANSWER\:/", $aiken_line)) {
					
					$correct_letter = preg_replace("/^ANSWER\:/", '', $aiken_line);
					$correct_letter = trim($correct_letter);
					
					foreach($answers as $key=>$answer) {
						$correct = ($key == $correct_letter) ? 1 : 0;
						$points = $correct;
						$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_ANSWERS." SET
							question_id=%d, answer=%s, correct=%s, point=%s", 
							$qid, $answer, $correct, $points));
					}
					$start_question = true;	
					$answers = array();				
				}
				elseif(preg_match("/^CATEGORY\:/", $aiken_line)) {
										// question category added:
					$category = preg_replace("/^CATEGORY\:/", '', $aiken_line);
					if(!empty($category)) {
						$cat_id = WTPCategory::discover($category, $cats);
						
						// update category for that question
						$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET cat_id=%d WHERE ID=%d", $cat_id, $qid));
					}
				}
				elseif(preg_match("/^EXPLANATION\:/", $aiken_line)) {
					// "explain answer" added:
					$explanation = preg_replace("/^EXPLANATION\:/", '', $aiken_line);
					if(!empty($explanation)) {
						// lets add some extra intelligence here and assume if there is one {{{split}}} inside we want boolean mode, and if there are more, we want exact
						$elaborate_explanation = '';
						if(strstr($explanation, '{{{split}}}')) {
							$explanation_parts = explode('{{{split}}}', $explanation);
							if(count($explanation_parts) == 2) $elaborate_explanation = 'boolean';
							else $elaborate_explanation = 'exact';
						}						
						
						// update explanation for that question
						$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET explain_answer=%s, elaborate_explanation=%s 
							WHERE ID=%d", $explanation, $elaborate_explanation, $qid));
					}
				}
				else {
					// fill into the answers array					
					$answer_text = substr($aiken_line, 2);
					$answer_letter = substr($aiken_line,0,1);
					$answers[$answer_letter] = $answer_text;
				}
			}
		} // end while
	} // end import aiken
}