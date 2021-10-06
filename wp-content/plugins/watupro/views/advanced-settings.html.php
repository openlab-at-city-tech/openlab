	<div class="inside">
			<h3><?php _e('Advanced Final Screen Settings', 'watupro') ?></h3>
			
			<p><input type="checkbox" name="confirm_on_submit" value="1" <?php if(!empty($advanced_settings['confirm_on_submit'])) echo 'checked'?>> <?php _e('Ask for confirmation when the "Submit" button is pressed.', 'watupro')?></p>		
			
			<p><input type="checkbox" name="no_checkmarks" value="1" <?php if(!empty($advanced_settings['no_checkmarks'])) echo 'checked'?>> <?php _e('Do not show correct / incorrect checkmarks at all.', 'watupro')?></p>		
			<p><input type="checkbox" name="no_checkmarks_unresolved" value="1" <?php if(!empty($advanced_settings['no_checkmarks_unresolved'])) echo 'checked'?>> <?php _e('Do not show correct / incorrect checkmarks on unresolved questions to avoid right answers being revealed.', 'watupro')?></p>	
			<p><input type="checkbox" name="feedback_unresolved" value="1" <?php if(!empty($advanced_settings['feedback_unresolved'])) echo 'checked'?>> <?php _e('Include the optional answer feedback in the %%UNRESOLVED%% variable.', 'watupro')?></p>	
			<?php if(watupro_intel()):?>
			<p><input type="checkbox" name="reveal_correct_gaps" value="1" <?php if(!empty($advanced_settings['reveal_correct_gaps'])) echo 'checked'?>> <?php _e('Reveal the correct answers on unanswered and wrongly answered fields in "Fill the gaps" questions.', 'watupro')?></p>		
			<?php endif;?>
			<p>&nbsp;</p>	
	</div>
			
	<div class="inside">
			<h3><?php _e('Advanced Workflow Settings', 'watupro') ?></h3>
			<div>
				<p><?php printf(__('Allow total of %1$s attempts on this %2$s (enter 0 for no limit).', 'watupro'), '<input type="text" name="total_attempts_limit" value="'.(empty($advanced_settings['total_attempts_limit']) ? 0 : $advanced_settings['total_attempts_limit']).'" size="4" id="totalAttemptsLimit">', WATUPRO_QUIZ_WORD);?></p>
				
				<p id="totalAttemptsLimitMessage" style='display:<?php echo empty($advanced_settings['total_attempts_limit']) ? 'none' : 'block' ?>'>
					<?php _e('Message that will be shown after the attempts are used (if empty, a default message will be used):', 'watupro');?>
					<input type="text" name="total_attempts_limit_message" size="100" value="<?php echo htmlentities(stripslashes(rawurldecode(@$advanced_settings['total_attempts_limit_message'])))?>">
				</p>
			</div>
			<p><input type="checkbox" name="dont_prompt_unanswered" value="1" <?php if(!empty($advanced_settings['dont_prompt_unanswered'])) echo 'checked'?>> <?php _e('Do not prompt the user when a non-required question is not answered.', 'watupro')?></p>		
			
			<p><input type="checkbox" name="dont_prompt_notlastpage" value="1" <?php if(!empty($advanced_settings['dont_prompt_notlastpage'])) echo 'checked'?>> <?php printf(__('Do not prompt the user when they are trying to submit a paginated %s but are not on the last page.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>		
			
			<?php if($exam->single_page==0):?>
				<p><input type="checkbox" name="dont_load_inprogress" value="1" <?php if(!empty($advanced_settings['dont_load_inprogress'])) echo 'checked'?>> <?php printf(__("Don't load the unfinished %s when user comes back to continue (Normally the software would let the user continue from where they were).", 'watupro'), WATUPRO_QUIZ_WORD)?></p>		
			<?php endif;?>
				
				<p><input type="checkbox" name="dont_scroll" value="1" <?php if(!empty($advanced_settings['dont_scroll'])) echo 'checked'?>> <?php _e("Don't auto-scroll the screen when user moves from page to page (Auto-scrolling happens to ensure user always sees the top of the page).", 'watupro')?></p>	
				
				<p><input type="checkbox" name="dont_scroll_start" value="1" <?php if(!empty($advanced_settings['dont_scroll_start'])) echo 'checked'?>> <?php _e("Don't auto-scroll the screen when using start button.", 'watupro')?></p>	
			
			
			<p><?php _e('When user answers a "single choice" question:', 'watupro')?> <select name="single_choice_action">
				<option value=""><?php _e('Do nothing (default)', 'watupro')?></option>			
				<?php if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE):?>
					<option value="next" <?php if(!empty($advanced_settings['single_choice_action']) and $advanced_settings['single_choice_action'] == 'next') echo 'selected'?>><?php _e('Go to next question', 'watupro')?></option>
				<?php endif;?>	
				<option value="show" <?php if(!empty($advanced_settings['single_choice_action']) and $advanced_settings['single_choice_action'] == 'show') echo 'selected'?>><?php _e('Show the answer', 'watupro')?></option>				
			</select><br>
			<i><?php _e('(By default nothing happens until the user clicks "Next", "Show answer", or other button.)', 'watupro')?></i></p>
			
			<p><input type="checkbox" name="unselect" value="1" <?php if(!empty($advanced_settings['unselect'])) echo 'checked'?>> <?php _e('Provide "Unselect" button on single choice and multiple choice questions.', 'watupro');?></p>
			
			<p><input type="checkbox" name="dont_store_taking" <?php if(!empty($advanced_settings['dont_store_taking'])) echo 'checked'?> value="1" onclick="this.checked ? jQuery('#storeOnlyLoggedIn').hide() : jQuery('#storeOnlyLoggedIn').show();"> <?php printf(__("Don't store any data / results of this %s in the database.", 'watupro'), WATUPRO_QUIZ_WORD);?> <br>
			<?php printf(__("This setting will be useful in fun %s or %s for practicing in which you don't need history. You can't have certificates in these tests.<br> %s will also not send the 'watupro_completed_exam' API call used by other plugins like WatuPRO Play to assign levels, badges, add to user's point balance etc.", 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD_PLURAL, ucfirst(WATUPRO_QUIZ_WORD_PLURAL));?></p>		
			
			<p id="storeOnlyLoggedIn" style='display:<?php echo empty($advanced_settings['dont_store_taking']) ? 'block' : 'none';?>'><input type="checkbox" name="store_taking_only_logged" <?php if(!empty($advanced_settings['store_taking_only_logged'])) echo 'checked'?> value="1"> <?php printf(__("Store only the data / results of logged in users.", 'watupro'), WATUPRO_QUIZ_WORD);?> 
			
			<p><input type="checkbox" value="1" name="store_full_catgrades" <?php if(!empty($advanced_settings['store_full_catgrades'])) echo 'checked'?>> <?php _e("Store full category grades along with the results. This can be useful for certificates but can use a lot of database space.", 'watupro');?></p>	
			
			<p><input type="checkbox" name="save_source_url" <?php if(!empty($advanced_settings['save_source_url'])) echo 'checked'?>> <?php printf(__('Save source URL when submitting the %s (useful if you have published the %s in multiple places on your site).', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?> </p>
			
			<?php if(watupro_intel()):?>
			<p><input type="checkbox" name="gaps_replace_spaces" <?php if(!empty($advanced_settings['gaps_replace_spaces'])) echo 'checked'?>> <?php _e('Replace multiple spaces entered by users on "fill the gaps" questions with single space when evaluating the answer.', 'watupro');?> </p>
			<?php endif;?>
			
			<p><input type="checkbox" name="is_likert_survey" <?php if(!empty($exam->is_likert_survey)) echo 'checked'?> onclick="this.checked ? jQuery('#wtpLikertSettings').show() : jQuery('#wtpLikertSettings').hide();"> <?php printf(__('Design this %s as a likert survey table. <b>Pagination will be reset to "all questions on one page"</b>. (<a href="%s" target="_blank">This is an experimental feature</a>).', 'watupro'), WATUPRO_QUIZ_WORD, 'http://blog.calendarscripts.info/likert-scale-survey-maker-for-watupro/#likert-table');?> </p>
			
			<div id="wtpLikertSettings" style='display:<?php echo empty($exam->is_likert_survey)? 'none' : 'block';?>;margin-left:50px;'>
				<p>
					<?php _e('Cell width:', 'watupro');?> <input type="radio" name="likert_cell_width_type" value="dynamic" <?php if(empty($advanced_settings['likert_cell_width_type']) or $advanced_settings['likert_cell_width_type'] == 'dynamic') echo 'checked'?>> <?php _e('Dynamic', 'watupro');?> 
					&nbsp;
					<input type="radio" name="likert_cell_width_type" value="fixed" <?php if(!empty($advanced_settings['likert_cell_width_type']) and $advanced_settings['likert_cell_width_type'] == 'fixed') echo 'checked'?>> <?php printf(__('Fixed: %spx', 'watupro'), '<input type="text" size="4" name="likert_cell_width" value="'.@$advanced_settings['likert_cell_width'].'">');?> <br />
					<?php _e('Header cells alignment:','watupro');?> <select name="likert_header_align">
						<option value="left"><?php _e('Left', 'watupro');?></option>
						<option value="right" <?php if(!empty($advanced_settings['likert_header_align']) and $advanced_settings['likert_header_align'] == 'right') echo 'selected'?>><?php _e('Right', 'watupro');?></option>
						<option value="center" <?php if(!empty($advanced_settings['likert_header_align']) and $advanced_settings['likert_header_align'] == 'center') echo 'selected'?>><?php _e('Center', 'watupro');?></option>
					</select>
					&nbsp;
					<?php _e('Question cells alignment:','watupro');?> <select name="likert_question_align">
						<option value="left"><?php _e('Left', 'watupro');?></option>
						<option value="right" <?php if(!empty($advanced_settings['likert_question_align']) and $advanced_settings['likert_question_align'] == 'right') echo 'selected'?>><?php _e('Right', 'watupro');?></option>
						<option value="center" <?php if(!empty($advanced_settings['likert_question_align']) and $advanced_settings['likert_question_align'] == 'center') echo 'selected'?>><?php _e('Center', 'watupro');?></option>
					</select>
					&nbsp;
					<?php _e('Choices/answers cells alignment:','watupro');?> <select name="likert_choice_align">
						<option value="left"><?php _e('Left', 'watupro');?></option>
						<option value="right" <?php if(!empty($advanced_settings['likert_choice_align']) and $advanced_settings['likert_choice_align'] == 'right') echo 'selected'?>><?php _e('Right', 'watupro');?></option>
						<option value="center" <?php if(!empty($advanced_settings['likert_choice_align']) and $advanced_settings['likert_choice_align'] == 'center') echo 'selected'?>><?php _e('Center', 'watupro');?></option>
					</select>
					<br />
					<?php _e('Table border:', 'watupro');?> <select name="likert_table_border" onchange="this.value == 'css' ? jQuery('#wtpLikertCustomBorderCSS').show() : jQuery('#wtpLikertCustomBorderCSS').hide();">
						<option value="default"><?php _e('Default', 'watupro');?></option>
						<option value="none" <?php if(!empty($advanced_settings['likert_table_border']) and $advanced_settings['likert_table_border'] == 'none') echo 'selected'?>><?php _e('None', 'watupro');?></option>
						<option value="css" <?php if(!empty($advanced_settings['likert_table_border']) and $advanced_settings['likert_table_border'] == 'css') echo 'selected'?>><?php _e('Custom CSS', 'watupro');?></option>
					</select>
					<input type="text" name="likert_border_custom_css" size="20" value="<?php echo @$advanced_settings['likert_border_custom_css']?>" style='display:<?php echo (!empty($advanced_settings['likert_table_border']) and $advanced_settings['likert_table_border'] == 'css') ? 'inline' : 'none'; ?>' id="wtpLikertCustomBorderCSS" placeholder="<?php _e('example: 1pt solid blue;', 'watupro');?>">
				</p>
			</div>
			
			<p><input type="checkbox" name="log_timer" value="1" <?php if(!empty($advanced_settings['log_timer'])) echo 'checked';?>> <?php _e('Save debug log when timer is enabled. Select this only if instructed, the option can take a lot of database space.', 'watupro');?></p>
			
			<?php if(get_option('watupro_integrate_moolamojo') == 1):?>
				<p><input type="checkbox" name="transfer_moola" value="1" <?php if(!empty($advanced_settings['transfer_moola'])) echo 'checked'?> onclick="this.checked ? jQuery('#moolaMojoOptions').show() : jQuery('#moolaMojoOptions').hide();"> <?php printf(__('Transfer points from this %s as virtual <a href="%s" target="_blank">MoolaMojo</a> credits.', 'watupro'), WATUPRO_QUIZ_WORD, 'https://moolamojo.com');?>
					<div id="moolaMojoOptions" style='padding-left:25px;display:<?php echo empty($advanced_settings['transfer_moola']) ? 'none' : 'block';?>'>
						<input type="radio" name="transfer_moola_mode" value="equal" <?php if(empty($advanced_settings['transfer_moola_mode']) or $advanced_settings['transfer_moola_mode'] == 'equal') echo 'checked'?>> <?php _e('Transfer the earned points as MoolaMojo credits 1:1 (if negative points are earned, the user will be charged credits)', 'watupro');?> <br />
						<input type="radio" name="transfer_moola_mode" value="grades" <?php if(!empty($advanced_settings['transfer_moola_mode']) and $advanced_settings['transfer_moola_mode'] == 'grades') echo 'checked'?>> <?php _e('Transfer fixed amount of points based on the grade/result achieved (A field will appear for each grade/result on your Grades page to enter amount of virtual credits to be transferred)', 'watupro');?>
					</div>			
				</p>
			<?php endif;?>	
				
			<p>&nbsp;</p>
	</div>		
	
	<div class="inside">		
			
			<h3><?php _e('Student Dashboard Settings', 'watupro') ?></h3>
			
			
				<p><input type="checkbox" name="show_result_and_points" value="1" <?php if(!empty($advanced_settings['show_result_and_points'])) echo 'checked'?>> <?php _e('Show results and points of every question in the table view (reveals the correct answer).', 'watupro')?></p>
				<p><input type="checkbox" name="answer_snapshot_in_table_format" value="1" <?php if(!empty($advanced_settings['answer_snapshot_in_table_format'])) echo 'checked'?> onclick="this.checked ? jQuery('#showResultPoints').hide() : jQuery('#showResultPoints').show();"> <?php printf(__('Show full snapshot of user answer in the table view, along with the optional answer feedback.', 'watupro'), WATUPRO_QUIZ_WORD);?></p>				
			
			<p><input type="checkbox" name="show_only_snapshot" value="1" <?php if(!empty($advanced_settings['show_only_snapshot'])) echo 'checked'?>> <?php printf(__('Show only snapshot when user opens taken %s details pop-up. Admins/teachers will still be able to get the table format and CSV download.', 'watupro'), WATUPRO_QUIZ_WORD);?></p>			
			
			<?php if(!empty($grades) and count($grades)):?>
			   <p><?php _e('This test will be considered completed when:', 'watupro');?></p>
			   <ul>
			      <li><input type="radio" name="completion_criteria" value="taken" <?php if(empty($advanced_settings['completion_criteria']) or $advanced_settings['completion_criteria'] == 'taken') echo "checked"?>> <?php _e('it is taken at least once (default criteria)', 'watupro');?></li>
			      <li><input type="radio" name="completion_criteria" value="grades" <?php if(!empty($advanced_settings['completion_criteria']) and $advanced_settings['completion_criteria'] == 'grades') echo "checked"?>> <?php _e('only when the user has achieved some of the following grades:', 'watupro');
			      echo '&nbsp;';
			      foreach($grades as $grade):?>
								<input type="checkbox" name="completion_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($advanced_settings['completion_grades']) and strstr($advanced_settings['completion_grades'], "|".$grade->ID."|")) echo "checked"?>> <?php echo stripslashes($grade->gtitle)?> &nbsp;&nbsp;&nbsp;
					<?php endforeach;?></li>
			   </ul>
			<?php endif;?>
			
			<p>&nbsp;</p>
	</div>		
	
	<?php if(watupro_intel()):?>
	<div class="inside">			
			
			<h3><?php _e('Paginator Settings', 'watupro') ?></h3>
			
			<p><?php printf(__('This configuration takes effect for %s that use numbered pagination. For the colors below you can enter words like "red", "orange", etc, or HTML color value like "#FFCCAA".','watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
			
			<p><label><?php _e('Color of answered question number (defaults to green):','watupro')?></label> <input type="text" size="10" name="answered_paginator_color" value="<?php echo @$advanced_settings['answered_paginator_color']?>"></p>			
			<p><label><?php _e('Color of unanswered question number (defaults to red):','watupro')?></label> <input type="text" size="10" name="unanswered_paginator_color" value="<?php echo @$advanced_settings['unanswered_paginator_color']?>"></p>
			
			<p>&nbsp;</p>
	</div>
	<?php endif;?>
	
	<div class="inside">			
			
			<h3><?php _e('Optional Answer Explanations', 'watupro') ?></h3>
			
			<p><?php printf(__('By default answer explanations (when existing) will be shown accordingly to your conditions when you use the %%ANSWERS%% variable on the final screen of the %s. If your %s is long or the explanations are long this may be overwhelming to show at once. You can make them hidden by default and toggled by a button.', 'watupro'), 
			WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
			
			<p><input type="checkbox" name="toggle_answer_explanations" value="1" <?php if(!empty($advanced_settings['toggle_answer_explanations'])) echo 'checked'?> onclick="this.checked ? jQuery('#wtpToggleButtonDiv').show() : jQuery('#wtpToggleButtonDiv').hide();"> <?php _e('Toggle answer explanations by a button.', 'watupro');?></p>
			
			<p id="wtpToggleButtonDiv" style='display:<?php echo empty($advanced_settings['toggle_answer_explanations']) ? 'none' : 'block'; ?>'><label><?php _e('Text/value on the button:', 'watupro');?></label> <input type="text" name="toggle_answer_explanations_button" value="<?php echo stripslashes(rawurldecode(@$advanced_settings['toggle_answer_explanations_button']))?>"></p>			
			
	</div>
	
	<div class="inside">			
			
    		<h3 class="hndle"><span><?php _e('Advanced Question Randomization and Category Order', 'watupro') ?></span></h3>
    		<?php if(!$exam->random_per_category):?>
			<p><b><?php _e('Randomization currently not in effect.', 'watupro');?></b> <?php _e('You need to pull random questions per category on the main page for this to have any effect.', 'watupro')?></p>
		<?php endif?>
    		
    		<?php if($exam->pull_random and $exam->random_per_category):?>
	    		<p><?php printf(__('You have chosen to pull %d random questions per category. Here you can elaborate by selecting specific random number for every question category. If you do not want to include any questions from a given category you should enter "-1" for it. Leaving 0 in the field will actually pull the default %d questions of that category.', 'watupro'), $exam->pull_random, $exam->pull_random);?></p>
    		<?php endif;?>
    		
			<table cellpadding="8">
				<tr><th><?php _e('Order', 'watupro')?></th> <th><?php _e('Category', 'watupro')?></th> <th><?php _e('No. questions', 'watupro')?></th></tr>
				<?php foreach($qcats as $qcat):
					if(!empty($qcat->parent_id)) $offset_style = 'style="padding-left:20px;font-style:italic;"';
					else $offset_style = ''; ?>
					<tr><td <?php echo $offset_style?>><input type="text" size="3" name="qcat_order_<?php echo $qcat->ID?>" value="<?php echo $qcat->sort_order?>"></td><td <?php echo $offset_style?>><?php echo stripslashes(apply_filters('watupro_qtranslate', $qcat->name))?></td><td <?php echo $offset_style?>><input type="text" size="4" name="random_per_<?php echo $qcat->ID?>" value="<?php echo isset($advanced_settings['random_per_'.$qcat->ID]) ? $advanced_settings['random_per_'.$qcat->ID] : $exam->pull_random?>"></td></tr>
				<?php endforeach;?>
			</table>
			<p><?php _e('The "Order" field in the above table lets you specify the order categories appear when the questions are grouped by category.', 'watupro');?></p>
	</div>
	
<script>
jQuery(function(){
	jQuery('#totalAttemptsLimit').keyup(function(){
		if(jQuery('#totalAttemptsLimit').val() > 0) jQuery('#totalAttemptsLimitMessage').show();
		else jQuery('#totalAttemptsLimitMessage').hide();
	});
});
</script>	