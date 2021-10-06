<?php 
// handles the display of questions in likert-like tables
  WatuPROQuestionEnhanced :: likert_table_header($exam, $ques);
  if($exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) echo watupro_paginate_header($exam, $qct, $num_pages);
  $qct++;
  echo $_question->display($ques, $qct, $question_count, @$in_progress, $exam);		 	
  $question_ids .= $ques->ID.',';
	      
  if(!in_array($ques->cat_id, $question_catids)) $question_catids[] = $ques->cat_id; 
  $question_count++;        