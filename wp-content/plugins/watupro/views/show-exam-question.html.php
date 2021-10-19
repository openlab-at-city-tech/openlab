<?php 
	   if($exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER) echo watupro_paginate_header($exam, $qct, $num_pages);
	   $qct++;
	   $compact_class = $ques->compact_format ? ' watu-question-compact ' : '';
	   
		// if single page and we should make the first question display with !important
		$visible_style = '';
		if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE) {
			if($qct == $current_page) $visible_style = "display:block;";
		}	  
		
		echo "<div class='watu-question $compact_class' id='question-$question_count' style='$visible_style;'>";
			// handle question intros
			WatuPROQuestionEnhanced :: intro($ques, $exam); 		
					 
			 if(!$single_page and $cnt_questions > 1) echo WatuPROExams :: show_qXofY($qct, $total, $advanced_settings, 'top');
			 
			  echo $_question->display($ques, $qct, $question_count, @$in_progress, $exam);		 	
			 
			if($exam->live_result or (!empty($advanced_settings['single_choice_action']) and $advanced_settings['single_choice_action']=='show')):
			   if(empty($_question->inprogress_snapshots[$ques->ID])):?>
					<div style="display:none;" id='liveResult-<?php echo $question_count?>'>		   
						<img src="<?php echo plugins_url('watupro/img/loading.gif')?>" width="16" height="16" alt="<?php _e('Loading...', 'watu', 'watupro')?>" title="<?php _e('Loading...', 'watu', 'watupro')?>" />&nbsp;<?php _e('Loading...', 'watu', 'watupro')?>
					</div>	
			<?php endif; // end if displaying the div 
			endif; // end if live_result     
			
			 
			 if($exam->live_result or (!empty($advanced_settings['single_choice_action']) and $advanced_settings['single_choice_action']=='show')):
			   if(!empty($_question->inprogress_snapshots[$ques->ID])): echo stripslashes($_question->inprogress_snapshots[$ques->ID]); endif; // end if displaying snapshot	
			endif; // end if live_result  
			 
			 $question_ids .= $ques->ID.',';
		    if(!$single_page and $cnt_questions > 1) echo WatuPROExams :: show_qXofY($qct, $total, $advanced_settings);
	   echo "</div>"; // end question div
	   
	   // "live result" when the quiz is not paginated
	   if($exam->live_result and !$exam->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE and empty($_question->inprogress_snapshots[$ques->ID])):?> 
	   	<p id="liveResBtn-<?php echo $ques->ID?>" style="clear:both;"><input type="button" id="liveResultBtn<?php echo $ques->ID?>" class="liveResultBtn<?php echo $qct?> watupro-live-result-btn" value="<?php _e('See Answer', 'watupro')?>" onclick="WatuPRO.liveResult(<?php echo $ques->ID?>, <?php echo $qct?>);"></p>
	   <?php endif;
	      
	   if(!in_array($ques->cat_id, $question_catids)) $question_catids[] = $ques->cat_id; 
	   $question_count++;        