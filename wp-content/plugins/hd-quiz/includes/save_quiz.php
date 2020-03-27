<?php
	// save the quiz data
	
	// update question order

	$quiz_id = intval($_POST['quiz_id']);

	if ($quiz_id > 0){
		$questions = sanitize_text_field($_POST['questions']);
		$questions = json_decode(stripslashes($questions), false);

		if($questions){
			foreach ($questions as $q){
				$post = array();
				$post['ID'] = intval($q[0]);
				$post['menu_order' ] = intval($q[1]);
				wp_update_post($post);
			}
		}
	}

	// update quiz options
	$passPercent = intval($_POST['passPercent']);
	$passText = wp_kses_post($_POST['passText']);
	$failText = wp_kses_post($_POST['failText']);
	$hdq_share_results = sanitize_text_field($_POST['hdq_share_results']);
	$hdq_results_position = sanitize_text_field($_POST['hdq_results_position']);
	$hdq_show_results = sanitize_text_field($_POST['hdq_show_results']);
	$hdq_show_results_correct = sanitize_text_field($_POST['hdq_show_results_correct']);
	$hdq_show_extra_text = sanitize_text_field($_POST['hdq_show_extra_text']);
	$hdq_quiz_timer = intval($_POST['hdq_quiz_timer']);
	$hdq_randomize_question_order = sanitize_text_field($_POST['hdq_randomize_question_order']);
	$hdq_randomize_answer_order = sanitize_text_field($_POST['hdq_randomize_answer_order']);
	$hdq_pool_of_questions = intval($_POST['hdq_pool_of_questions']);
	$hdq_wp_paginate = intval($_POST['hdq_wp_paginate']);
	$hdq_immediate_mark = sanitize_text_field($_POST['hdq_immediate_mark']);
	$hdq_stop_answer_reselect = sanitize_text_field($_POST['hdq_stop_answer_reselect']);


	// update the meta
	$t_id = $quiz_id;
    $term_meta = get_option("taxonomy_term_$t_id");
    $term_meta["passPercent"] = $passPercent;
	$term_meta["passText"] = $passText;
	$term_meta["failText"] = $failText;
	$term_meta["shareResults"] = $hdq_share_results;
	$term_meta["resultPos"] = $hdq_results_position;
	$term_meta["showResults"] = $hdq_show_results;
	$term_meta["showResultsCorrect"] = $hdq_show_results_correct;
	$term_meta["showIncorrectAnswerText"] = $hdq_show_extra_text;
	$term_meta["quizTimerS"] = $hdq_quiz_timer;
	$term_meta["randomizeQuestions"] = $hdq_randomize_question_order;
	$term_meta["randomizeAnswers"] = $hdq_randomize_answer_order;
	$term_meta["pool"] = $hdq_pool_of_questions;
	$term_meta["paginate"] = $hdq_wp_paginate;
	$term_meta["immediateMark"] = $hdq_immediate_mark;
	$term_meta["stopAnswerReselect"] = $hdq_stop_answer_reselect;
    //save the option array
	update_option("taxonomy_term_$t_id", $term_meta);

	echo 'done';

?>