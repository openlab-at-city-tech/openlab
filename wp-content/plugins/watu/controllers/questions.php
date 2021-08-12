<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function watu_questions() {
	global $wpdb;
	
	$action = 'new';
	if(!empty($_GET['action']) and $_GET['action'] == 'edit') $action = 'edit';
	$quiz_id = intval($_GET['quiz']);
		
	if(isset($_REQUEST['submit']) and check_admin_referer('watu_questions')) {
		$content = watu_strip_tags($_POST['content']);		
		$answer_type = $_POST['answer_type'];
		if(!in_array($answer_type, array('radio', 'checkbox', 'textarea'))) $answer_type = 'radio';
		$is_required = empty($_POST['is_required']) ? 0 : 1;
		$is_inactive = empty($_POST['is_inactive']) ? 0 : 1;
		$is_survey = empty($_POST['is_survey']) ? 0 : 1;
		$feedback = watu_strip_tags($_POST['feedback']);
		$num_columns = empty($_POST['num_columns']) ? 1 : intval($_POST['num_columns']);
		
		if($action == 'edit'){ //Update goes here
			$question_id = intval($_POST['question']);
			
			$wpdb->query($wpdb->prepare("UPDATE ".WATU_QUESTIONS." 
				SET question=%s, answer_type=%s, is_required=%d, feedback=%s, is_inactive=%d, is_survey=%d, num_columns=%d
				WHERE ID=%d", $content, $answer_type, $is_required, 
				$feedback, $is_inactive, $is_survey, $num_columns, $question_id));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_ANSWERS." WHERE question_id=%d", $question_id));
				
		} else {	
			// select max sort order in this quiz
			$sort_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".WATU_QUESTIONS." WHERE exam_id=%d", $quiz_id));
			$sort_order++;			
			
			$sql = $wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." (exam_id, question, answer_type, is_required, feedback, sort_order, 
			is_inactive, is_survey, num_columns) 
			VALUES(%d, %s, %s, %d, %s, %d, %d, %d, %d)", $quiz_id, $content, $answer_type, 
				$is_required, $feedback, $sort_order, $is_inactive, $is_survey, $num_columns);
			$wpdb->query($sql);
	
			$_POST['question'] = $wpdb->insert_id;
			$action='edit';
		}
		
		$question_id = intval($_POST['question']);
		if($question_id>0) {
			// the $counter will skip over empty answers, $sort_order_counter will track the provided answers order.
			$counter = 1;
			$sort_order_counter = 1;
			$correctArry = watu_int_array(@$_POST['correct_answer']);
			$pointArry = $_POST['point'];
			
			if(is_array($_POST['answer']) and !empty($_POST['answer'])) {
				
				foreach ($_POST['answer'] as $key => $answer_text) {
					$correct=0;
					if( @in_array($counter, $correctArry) ) $correct=1;
					$point = floatval($pointArry[$key]);
					if($answer_text!='') {
					   $answer_text = watu_strip_tags($answer_text);
						$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." (question_id,answer,correct,point, sort_order)
							VALUES(%d, %s, %s, %f, %d)", $question_id, $answer_text, $correct, $point, $sort_order_counter));
						$sort_order_counter++;
					}
					$counter++;
				}
			} 	// end if(is_array($_POST['answer']) and !empty($_POST['answer']))
			
			// save & reuse clicked?
			if(!empty($_POST['reuse'])) {
				watu_redirect("admin.php?page=watu_question&action=new&quiz=".intval($_GET['quiz'])."&question=" . $question_id);
			}
			// save & add new blank clicked?
			if(!empty($_POST['add_blank'])) {
				watu_redirect("admin.php?page=watu_question&action=new&quiz=".intval($_GET['quiz']));
			}			
			
		} // end if $question_id
	} // end add/save
	
	if(!empty($_GET['action']) and $_GET['action'] == 'delete' and check_admin_referer('watu_questions')) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_ANSWERS." WHERE question_id=%d", intval($_GET['question'])));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_QUESTIONS." WHERE ID=%d", intval($_GET['question'])));		
	}
	$exam_name = stripslashes($wpdb->get_var($wpdb->prepare("SELECT name FROM ".WATU_EXAMS." WHERE ID=%d", $quiz_id)));
	
	// mass delete questions
	if(!empty($_POST['mass_delete']) and check_admin_referer('watu_questions')) {
		$qids = is_array($_POST['qids']) ? watu_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query("DELETE FROM ".WATU_QUESTIONS." WHERE ID IN ($qid_sql)");
		$wpdb->query("DELETE FROM ".WATU_ANSWERS." WHERE question_id IN ($qid_sql)");		
	}
	
	// mass delete questions
	if(!empty($_POST['mass_update']) and check_admin_referer('watu_questions')) {
		$qids = is_array($_POST['qids']) ? watu_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		// constructing SQL this way because we may add new properties in the next version
		$is_required_sql = '';
		if($_POST['is_required'] != -1) {
			$is_required_sql = $wpdb->prepare(", is_required=%d", intval($_POST['is_required']));
		}
		
		$wpdb->query("UPDATE ".WATU_QUESTIONS." SET ID=ID $is_required_sql WHERE ID IN ($qid_sql)");		
	}
	
	// reorder questions
	if(!empty($_GET['move'])) {
		WatuQuestion::reorder($_GET['move'], $_GET['quiz'], $_GET['dir']);
		watu_redirect("admin.php?page=watu_questions&quiz=".intval($_GET['quiz']));
	}		
	
	$offset = 0; // for now initialize as 0
	
			
	$filter_sql = '';

	// filter by type
	if(!empty($_GET['filter_answer_type'])) {
		$filter_answer_type = $_GET['filter_answer_type'];
		if( !in_array($filter_answer_type, array('radio', 'checkbox', 'textarea'))) $filter_answer_type = 'radio';
		$filter_sql .= $wpdb->prepare(" AND Q.answer_type = %s ", $filter_answer_type);
	}		
	
	// filter by ID
	if(!empty($_GET['filter_id'])) {
		// cleanup everything that is not comma or number
		$filter_id = $_GET['filter_id'];
		$filter_id = preg_replace('/[^0-9\s\,]/', '', $filter_id);
		$filter_sql .= " AND Q.ID IN ($filter_id) ";
	}
	
	// filter by contents
	if(!empty($_GET['filter_contents'])) {
		$filter_sql .= $wpdb->prepare(" AND Q.question LIKE %s ", '%'.sanitize_text_field($_GET['filter_contents']).'%');
	}	
		
	// Retrieve the questions
	$all_question = $wpdb->get_results("SELECT Q.ID,Q.question, Q.answer_type as answer_type, 
			Q.is_required as is_required, Q.is_inactive as is_inactive,
			(SELECT COUNT(*) FROM ".WATU_ANSWERS." WHERE question_id=Q.ID) AS answer_count
			FROM `".WATU_QUESTIONS."` as Q
			WHERE Q.exam_id=$quiz_id $filter_sql ORDER BY Q.sort_order, Q.ID");
											
	if(empty($filter_sql)) WatuQuestion::fix_sort_order($all_question);		
	$num_questions = sizeof($all_question);	
	
	if(@file_exists(get_stylesheet_directory().'/watu/questions.html.php')) include get_stylesheet_directory().'/watu/questions.html.php';
	else include(WATU_PATH . '/views/questions.html.php');  
} 

function watu_question() {
	global $wpdb;	
	
	$action = 'new';
	if($_REQUEST['action'] == 'edit') $action = 'edit';
	$question_id = intval(@$_GET['question']);
	
	$all_answers = array();
	
	if(!empty($question_id)) {
		$question= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." WHERE ID=%d", $question_id));
		$all_answers = $wpdb->get_results($wpdb->prepare("SELECT answer, correct, point FROM ".WATU_ANSWERS." 
			WHERE question_id=%d ORDER BY sort_order", $question_id));	
	}
	
	$ans_type = ($action =='new' and empty($_GET['question']))  ? get_option('watu_answer_type'): $question->answer_type;
	$answer_count = 4;
	if( ($action == 'edit' or !empty($_GET['question'])) and $answer_count < count($all_answers)) $answer_count = count($all_answers) ;	
	
	wp_enqueue_editor();
	wp_enqueue_media();
	if(@file_exists(get_stylesheet_directory().'/watu/question-form.html.php')) include get_stylesheet_directory().'/watu/question-form.html.php';
	else include(WATU_PATH . '/views/question-form.html.php');  
}

// import questions page
function watu_import_questions() {
	global $wpdb;
	$quiz_id = intval($_GET['quiz_id']);
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT ID, name FROM ".WATU_EXAMS." WHERE ID=%d", $quiz_id));
	
	if(!empty($_POST['watu_import']) and check_admin_referer('watu_import_questions')) {
		if(empty($_FILES['csv']['name'])) wp_die(__('Please upload file', 'watu'));
			
		// check for non UTF-8 encoding
		$content = file_get_contents($_FILES['csv']['tmp_name']);
		if(!mb_detect_encoding($content, 'UTF-8', true)) $non_utf8_error = true;
		
		$row = 0;
		ini_set("auto_detect_line_endings", true);
		$delimiter = sanitize_text_field($_POST['delimiter']);
		if($delimiter == "tab") $delimiter="\t";
		
		if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE) {
			 if(empty($_POST['import_fails'])) {		
			    while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {	    	  
			    	  $row++;	
			        if(empty($data)) continue;			  			  
			        if(!empty($_POST['skip_title_row']) and $row == 1) continue;	        
			        watu_import_question($data, $quiz_id);      
			    } // end while
			 } else {
			 	// the customer says that import fails - let's try the handmade import function
			 	while(($csv_line = fgets($handle, 10000)) !== FALSE) {
			 		$row++;
			 		if(empty($csv_line)) continue;			  			  
			      if(!empty($_POST['skip_title_row']) and $row == 1) continue;
			      $data = watu_parse_csv_line($csv_line);		         
			      watu_import_question($data, $quiz_id);   
			 	} // end while
			 }	// end alternate CSV parsing
			 $result = true;
		} // end if $handle
		else $result = false;		
	} // end import
	
	if(@file_exists(get_stylesheet_directory().'/watu/import.html.php')) include get_stylesheet_directory().'/watu/import.html.php';
	else include(WATU_PATH . '/views/import.html.php');  
}

function watu_import_question($data, $exam_id) {
	global $wpdb;
	if(!get_magic_quotes_gpc()) $data[0] = addslashes($data[0]);
	 	
	// get max sort order
	$sort_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".WATU_QUESTIONS."
			WHERE exam_id=%d", $exam_id));
	$sort_order++;
	
   $sql = $wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." SET exam_id=%d, question=%s, answer_type=%s, sort_order=%d, is_required=%d, feedback=%s",
		$exam_id, watu_strip_tags($data[0]), sanitize_text_field($data[1]), $sort_order, intval($data[2]), watu_strip_tags($data[3]));
	$wpdb->query($sql);
	$qid = $wpdb->insert_id;	
     
     // add answers
     $data = array_slice($data, 4);  		
     
     $answers = array();
  	  $step = 1;
     foreach($data as $cnt=>$d) {			  			
  			if($step == 1) {
  				$answer = array();
  				$answer['answer'] = watu_strip_tags($d);			  				
  				$step=2;
  				continue;
  			}
  			if($step == 2) {
  				$answer['is_correct'] = intval($d);	
				$step = 3;
				continue;
  			}
  			if($step == 3) {
  				$answer['points'] = floatval($d);
  				$step = 1;
  				$answers[] = $answer;
  			}
  		} // end filling answers array
  			  		
  		// finally insert them	
		$vals = array();
		foreach($answers as $cnt=>$answer) {
			if($answer['answer'] === '') continue;
			$cnt++;
			$vals[] = $wpdb->prepare("(%d, %s, %s, %s, %d)", $qid, $answer['answer'], 
				$answer['is_correct'], $answer['points'], $cnt);
		}
		$values_sql = implode(",",$vals);
		
		if(count($answers)) { $wpdb->query("INSERT INTO ".WATU_ANSWERS." (question_id,answer,correct,point, sort_order) 
			VALUES $values_sql"); }	
}

