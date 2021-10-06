<?php
function watupro_export_questions() {
	global $wpdb;
	$newline = watupro_define_newline();
	
	// select questions
	$questions = $wpdb->get_results($wpdb->prepare("SELECT tQ.*, tC.name as category, 
		tC.parent_id as cat_parent_id, tParentCats.name as parent_category 
		FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC ON tC.ID=tQ.cat_id 
		LEFT JOIN ".WATUPRO_QCATS." tParentCats ON tParentCats.ID = tC.parent_id
		WHERE tQ.exam_id=%d ORDER BY tQ.sort_order, tQ.ID", $_GET['exam_id']), ARRAY_A);		
		
	$qids = array(-1);
	foreach($questions as $question) $qids[]=$question['ID'];
	$qid_sql=implode(",", $qids);
		
	// select all answers in the exam
	$answers = $wpdb->get_results("SELECT * FROM ".WATUPRO_ANSWERS." WHERE question_id IN ($qid_sql) ORDER BY sort_order, ID");
	
	// match answers to questions
	foreach($questions as $cnt => $question) {
		$questions[$cnt]['answers'] = array();
		foreach($answers as $answer) {
			if($answer->question_id == $question['ID']) $questions[$cnt]['answers'][]=$answer;
		}
		
		// category has parent?
		if($question['cat_parent_id']) $questions[$cnt]['category'] = $question['parent_category'].'>>>'.$question['category'];
	}
	
	// run last query to define the max number of answers
	$num_ans = $wpdb->get_row("SELECT COUNT(ID) as num_answers FROM ".WATUPRO_ANSWERS." WHERE question_id IN ($qid_sql)
			GROUP BY question_id ORDER BY num_answers DESC");
	$rows = array();
	
	$delim = get_option('watupro_csv_delim');
	if(empty($delim) or !in_array($delim, array(",", "tab"))) $delim = ",";
	if($delim == 'tab') $delim = "\t";
	$quote = get_option('watupro_csv_quotes');
	if(empty($quote)) $quote = ''; 
	else $quote = '"';
	
	if(empty($_GET['copy'])) {
		$titlerow="Question ID".$delim."Question".$delim."Answer Type".$delim."Order".$delim."Category".$delim."Explanation/Feedback".$delim."Required?".$delim."Correct answer condition".$delim."Fill the gap/sorting points".$delim."Sorting Answers".$delim."Max selections".$delim."Is Inactive?".$delim."Is Survey?".$delim."Elaborate answer feedback";
		for($i=1;$i<=$num_ans->num_answers;$i++) $titlerow.="".$delim."Answer ID".$delim."Answer".$delim."Points";
	}
	else {
		$titlerow="Question".$delim."Answer Type".$delim."Order".$delim."Category".$delim."Explanation/Feedback".$delim."Required?".$delim."Correct answer condition".$delim."Fill the gap/sorting points".$delim."Sorting Answers".$delim."Max selections".$delim."Is Inactive?".$delim."Is survey?".$delim."Elaborate answer feedback";
		
		// non-legacy export
		if(empty($GET['legacy'])) {
			$titlerow .= "".$delim."Open end mode".$delim."tags".$delim."Open-end question display style".$delim."Exclude from showing on the final screen? (0 or 1)".$delim."Hints".$delim."Display in compact format? (0 or 1)".$delim."Round the points to the closest decimal? (0 or 1)".$delim."Is this an important question? (0 or 100)"
			.$delim."Difficulty level".$delim."Penalty for non-answering".$delim."Multiple gaps as drop-downs".$delim."Answer columns".$delim."Don't randomize answers".$delim."Title";
		}		
		
		if(empty($GET['legacy'])) for($i=1;$i<=$num_ans->num_answers;$i++) $titlerow.=$delim."Answer".$delim."Is Correct?".$delim."Points";
		else for($i=1; $i<= $num_ans->num_answers; $i++) $titlerow.=$delim."Answer".$delim."Points";
	}		
	
	$rows[] = $titlerow;
		
	foreach($questions as $question) {
		// replace tabulators and quotes to avoid issues with excel
		$question['question'] = str_replace("\t", "   ", $question['question']);
		$question['question'] = str_replace('"', "'", $question['question']);
		$question['question'] = watupro_nl2br($question['question']);		
		$question['explain_answer'] = str_replace("\t", "   ", $question['explain_answer']);
		$question['explain_answer'] = str_replace('"', "'", $question['explain_answer']);
		$question['explain_answer'] = watupro_nl2br($question['explain_answer']);
		$question['explain_answer'] = str_replace("\n", "", $question['explain_answer']);
		$question['explain_answer'] = str_replace("\r", "", $question['explain_answer']);
		$question['hints'] = str_replace("\t", "   ", $question['hints']);
		$question['hints'] = str_replace('"', "'", $question['hints']);
		$question['hints'] = watupro_nl2br($question['hints']);		
		$question['sorting_answers'] = empty($question['sorting_answers']) ? '' : str_replace('"', "'", $question['sorting_answers']);
		$question['sorting_answers'] = str_replace("\n", "|||", $question['sorting_answers']);
		$question['sorting_answers'] = str_replace("\r", "|||", $question['sorting_answers']);
		
		if(empty($question['gaps_as_dropdowns'])) $question['gaps_as_dropdowns'] = '';
		
		// handle true/false questions
		if($question['answer_type'] == 'radio' and $question['truefalse']) $question['answer_type'] = "true/false";
		
		$row = "";		
		if(empty($_GET['copy'])) $row .= $question['ID'].$delim;
		$row .= $quote.stripslashes($question['question']).$quote.$delim.$question['answer_type'].$delim.$question['sort_order'].
			$delim.stripslashes($question['category']).$delim.$quote.stripslashes($question['explain_answer']).$quote.$delim.$question['is_required'].
			$delim.$question['correct_condition'].$delim.$question['correct_gap_points']."/".$question['incorrect_gap_points'].
			$delim.$quote.stripslashes($question['sorting_answers']).$quote.$delim.$question['max_selections'].$delim.$question['is_inactive'].
			$delim.$question['is_survey'].$delim.$question['elaborate_explanation'];
			
		// new export - adds the new fields
		if(empty($_GET['legacy'])) {
			$row .= $delim.$question['open_end_mode'].$delim.$quote.$question['tags'].$quote.$delim.$question['open_end_display'].
						$delim.$question['exclude_on_final_screen'].$delim.$quote.$question['hints'].$quote.$delim.$question['compact_format'].
						$delim.$question['round_points'].$delim.$question['importance'].$delim.$question['difficulty_level'].
						$delim.$question['unanswered_penalty'].$delim.$question['gaps_as_dropdowns'].
						$delim.$question['num_columns'].$delim.$question['dont_randomize_answers'].$delim.$question['title'];
		}	
		
		foreach($question['answers'] as $answer) {
			// replace tabulators and quotes to avoid issues with excel
			$answer->answer = str_replace("\t", "   ", $answer->answer);
			$answer->answer = str_replace('"', "'", $answer->answer);
			$answer->answer = watupro_nl2br($answer->answer);					
			
			if(empty($_GET['copy'])) $row .= $delim.$answer->ID;
			$row .= $delim.$quote.stripslashes($answer->answer).$quote.$delim.$answer->correct.$delim.$answer->point;
		}		
		
		$row = str_replace("\n", "", $row);
		$row = str_replace("\r", "", $row);				
		$rows[]=$row;
	}
	
	$csv=implode($newline,$rows);
	
	// credit to http://yoast.com/wordpress/users-to-csv/	
	$now = gmdate('D, d M Y H:i:s') . ' GMT';
	
	if(empty($_GET['copy'])) $filename = WATUPRO_QUIZ_WORD . '-'.$_GET['exam_id'].'-questions-edit.csv';
	else $filename = WATUPRO_QUIZ_WORD . '-'.$_GET['exam_id'].'-questions.csv';

	header('Content-Type: ' . watupro_get_mime_type());
	header('Expires: ' . $now);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Pragma: no-cache');
	echo $csv;
	exit;
}

// nl2br but without screwing tables and other tags
function watupro_nl2br($content) {
	$content = preg_replace("/\>(\r?\n){1,}/", ">", $content);	
	
	$content = nl2br($content);
	
	// remove br inside pre
	$match = array();
	if(preg_match_all('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s', $content, $match)){		
	    foreach($match as $a){
	        foreach($a as $b){	        		
	           $content = str_replace($b, str_replace("<br />", "", $b), $content);	           
	        }
	    }
	}
	
	return $content;
}