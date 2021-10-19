<div class="wrap watupro-wrap">
	<h2><?php echo ($action == 'new') ? __("Add Question", 'watupro') : __('Edit Question', 'watupro');?></h2>
	
	<div id="titlediv">
		<input type="hidden" id="title" name="ignore_me" value="This is here for a workaround for a editor bug" />
	</div>
	
	<script type="text/javascript">
	var answer_count = <?php echo $answer_count?>;
	var ans_type = "<?php print $ans_type?>";
	function newAnswer() {
		answer_count++;		
		chkType=(ans_type=='radio')?'radio':'checkbox';	
		// $("extra-answers").innerHTML += code.replace(/%%NUMBER%%/g, answer_count);
		var chkGroupDisplay = (allowCheckboxGroups ? 'inline' : 'none');
		var rtfLink = "<a href=\"#\" onclick=\"watuPROMCE('wtpAnswer"+answer_count+"', this);return false;\">Rich Text Editor</a><br />";

		var para = '<p class="wtp-notruefalse">'+rtfLink+
		'<textarea name="answer[]" rows="3" cols="50" class="answer answer-textarea" id="wtpAnswer'+answer_count+'"></textarea> <label for="correct_answer_'+
			answer_count + '"><?php _e("Correct Answer ", 'watupro'); ?></label> <input type="'+ chkType + 
			'" name="correct_answer[]" class="correct_answer" value="' + answer_count + '" id="correct_answer_' + 
			answer_count + '"> <label style="margin-left:10px;"><?php _e("Points: ", 'watupro'); ?></label> '+
			'<input type="text" name="point[]" class="numeric" size="4" id="answer_points_'+answer_count+'" value="<?php echo $default_incorrect_points?>">' +
			'<span class="wtp-chkradio-span" style="display:<?php echo ($ans_type == "radio" or $ans_type == "checkbox") ? 'inline' : 'none';?>">' +
			' <input type="'+chkType+'" name="is_checked" value="1" id="is_checked_' + answer_count + '"> <?php _e("Checked by default", "watupro");?> '+
			' <input type="checkbox" class="wtp-freetext" id="accept_freetext_' + answer_count + '" name="accept_freetext[]" value="' + answer_count + '"> <label for="accept_freetext_'+ answer_count +'"><?php _e('Allow also a free text answer', 'watupro');?></label></span>' +
			'<span class="wtp_chk_group" style="display:' + chkGroupDisplay + '">'+
		    '<label  style="margin-left:10px"><?php _e('Checkbox group:', 'watupro');?></label> <input type="text" name="chk_group[]" value="" size="2"></span>';
		<?php if(watupro_intel() and $exam->is_personality_quiz):?>
			// find current number
			var gradeNum = jQuery('.personaility-grade').length;
			
			para += ' <?php _e('assign to results:', 'watupro')?> <select name="grade_id_' + (gradeNum+1) + '[]" class="personaility-grade" multiple="true" size="3"><option value="0"><?php _e('- please select -', 'watupro')?></option>';			
			<?php foreach($grades as $grade):?>
				para += '<option value="<?php echo $grade->ID?>"><?php echo $grade->gtitle?></option>';
			<?php endforeach;
		endif;?>
		para += '</p>';	
		
		jQuery('#extra-answers').append(para);
		init();
	}
	
	function init() {
		jQuery("#wtpQuestionForm").submit(function(e) {
			// Make sure question is suplied
			var contents;
			if(window.tinyMCE && document.getElementById("content").style.display=="none") { // If visual mode is activated.
				contents = tinyMCE.get("content").getContent();
			} else {
				contents = document.getElementById("content").value;
			}
	
			if(!contents) {
				alert("<?php _e("Please enter the question", 'watupro'); ?>");
				e.preventDefault();
				e.stopPropagation();
				return true;
			}
		});
		
		jQuery('input[name=answer_type]').click(function(){
			ans_type = (this.value=='radio')?'radio':'checkbox';
			 jQuery('.correct_answer').each(function(){
				this.removeAttribute('type');
				this.setAttribute('type', ans_type);
			});
			
			jQuery('.is_checked').each(function(){
				this.removeAttribute('type');
				this.setAttribute('type', ans_type);
			});
		});
		
		<?php if(!empty($set_default_points)):?>
		jQuery('.correct_answer').click(function(){			
			// get the ID and figure out the ID of the corresponding field with points
			var targetID = this.id.replace('correct_answer', 'answer_points');
			if(this.checked) jQuery('#' + targetID).val('<?php echo $default_correct_points?>');
			else jQuery('#' + targetID).val('<?php echo $default_incorrect_points?>');
		});
		<?php endif;?>
		
		wtpAcceptFreetext();
	}
	jQuery(document).ready(init);
	</script>
	
	<p><a href="admin.php?page=watupro_questions&amp;quiz=<?php echo $_GET['quiz']?>"><?php _e("Go to Questions Page", 'watupro') ?></a>
	&nbsp; <a href="edit.php?page=watupro_exam&quiz=<?php echo $_GET['quiz']?>&action=edit"><?php printf(__('Edit this %s', 'watupro'), WATUPRO_QUIZ_WORD)?></a></p>
	
	<form name="post" action="admin.php?page=watupro_questions&amp;quiz=<?php echo $_GET['quiz']; ?>&action=<?php echo $_GET['action']?>" method="post" id="wtpQuestionForm">
	
	<div class="wrap">
		<h3><?php _e('Question Contents and Settings', 'watupro') ?></h3>
		<div class="inside">
			<p><input type="checkbox" name="is_inactive" <?php if(!empty($question->ID) and $question->is_inactive) echo 'checked'?> value="1"> <?php printf(__('Deactivate this question. This will exclude it from showing on the %s, counting it, including it in reports etc.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
			<p><input type="checkbox" name="importance" <?php if(!empty($question->ID) and $question->importance == 100) echo 'checked'?> value="100"> <?php printf(__('This is important question. (This means it should be included with priority if the %s is pulling only a subset of all the questions.)', 'watupro'), WATUPRO_QUIZ_WORD)?></p>			
			<?php if(empty($question->ID)):?>
				<p><input type="checkbox" name="add_first" value="1"> <?php _e('Add this question in the beginning (Useful only if you do not randomize questions. By default new questions are added at the end.)', 'watupro')?></p>
			<?php else:?>
				<p><?php _e('Display code:','watupro')?> <input type="text" size="14" readonly="true" onclick="this.select();" value="{{{answerto-<?php echo $question->ID?>}}}"> <?php _e('You can use this in the final screen or certificate contents to display the user answer on this question.', 'watupro')?>
				<?php printf(__('To display it elsewhere use the shortcode %1$s. It accepts optional parameter %2$s for the user ID, otherwise displays the latest answer of the question from the currently logged in user.', 'watupro'), '<input type="text" value="[watupro-answer question_id='.$question->ID.']" onclick="this.select();" readonly="readonly">', 'user_id');?></p>	
				<?php if(watupro_module('reports')):?>
				<p><?php _e('Shortcode for poll-like chart:', 'watupro')?> <input type="text" size="30" onclick="this.select();" value='[watupror-poll question_id="<?php echo $question->ID?>"]' readonly="readonly"> <a href="admin.php?page=watupro_help#reporting" target="_blank"><?php _e('How to configure?', 'watupro')?></a></p>
				<?php endif; // end if reporting moudle available 		
			  endif; // end if editing question (to display the Display code)?>

			<p><label><?php _e('Title (optional):', 'watupro');?></label> <input type="text" name="title" value="<?php echo empty($question->title) ? '' : htmlspecialchars(stripslashes($question->title));?>" size="60"></p>			 
			  
			<?php wp_editor(stripslashes(@$question->question), "watupro_content", array("editor_class" => 'i18n-multilingual', 'textarea_name' => 'content')); ?>
			<p><?php printf(__('You can use the variable %s to display the question ID inside the question', 'watupro'), '{{{ID}}}');?><br>
			<?php printf(__('Part of the question contents can be hidden on the final screen if you place them betwneen HTML comments: %1$s and %2$s. The rich text editor should be switched to Text mode when entering the comments.', 'watupro'), '&lt;!--watupro-hide-start--&gt;', '&lt;!--watupro-hide-end--&gt;');?><br>
			<?php printf(__('Use the %1$s tag to create a question intro. Learn more <a href="%2$s" target="_blank">here</a>.', 'watupro'), '{{{split}}}', 'https://blog.calendarscripts.info/question-intros-in-watupro/');?> </p>
		</div>
		
		<p><?php _e('Tags:', 'watupro')?> <input type="text" name="tags" size="50" value="<?php echo empty($question->tags) ? '' : str_replace('|', ', ', substr($question->tags, 1, strlen($question->tags)-2) )?>"> <?php _e('(Optional, for management purposes. Separate tags by commas.)', 'watupro')?></p>
	</div>
	
	<div class="postbox">
		<h3 class="hndle">&nbsp;<?php echo !empty($difficulty_levels) ? __('Question Category and Difficulty Level', 'watupro') : __('Question Category', 'watupro'); ?></h3>
		<div class="inside">
			<label><?php _e('Category:', 'watupro')?></label> <select name="cat_id" onchange="WatuPRO.changeQCat(this);">
			<option value="0" <?php if(empty($question->cat_id)) echo "selected"?>><?php _e("- Uncategorized  ", 'watupro');?></option>
			<option value="-1"><?php _e("- Add new category -", 'watupro');?></option>
			<?php foreach($qcats as $cat):?>
				<option value="<?php echo $cat->ID?>" <?php if(@$question->cat_id==$cat->ID) echo "selected"?>><?php echo stripslashes(apply_filters('watupro_qtranslate', $cat->name));?></option>
				<?php foreach($cat->subs as $sub):?>
					<option value="<?php echo $sub->ID?>" <?php if(@$question->cat_id==$sub->ID) echo "selected"?>> - <?php echo stripslashes(apply_filters('watupro_qtranslate', $sub->name));?></option>
				<?php endforeach;?>
			<?php endforeach;?>
			</select>
			
			<input type="text" name="new_cat" id="newCat" style="display:none;" placeholder="<?php _e('Enter category', 'watupro')?>">
			
			<?php if(!empty($difficulty_levels) and count($difficulty_levels)):?>
			&nbsp; <label><?php _e('Difficulty level:', 'watupro');?></label> <select name="difficulty_level">
				<option value=""><?php _e('None', 'watupro');?></option>
				<?php foreach($difficulty_levels as $dlev):
					$selected = (!empty($question) and $question->difficulty_level == trim($dlev)) ? ' selected' : '';?>
					<option value="<?php echo trim($dlev);?>"<?php echo $selected?>><?php echo trim($dlev);?></option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
		</div>
		
		<div class="postbox" id="atdiv">
			<h3 class="hndle">&nbsp;<?php _e('Answer Type and Settings', 'watupro') ?></h3>			
			<div class="inside" style="padding:8px">
				<?php $def_answer_type = get_option('watupro_answer_type');
				if(empty($ans_type)) $ans_type = $def_answer_type;	?>
				<label>&nbsp;<input type='radio' name='answer_type' <?php if($ans_type == 'radio' or empty($answer_type)) echo 'checked'?> id="answer_type_r" value='radio' onclick="selectAnswerType('radio');" /> <?php _e("Single Choice (Radio buttons)", 'watupro')?> </label>
				&nbsp;&nbsp;&nbsp;
				
				<label>&nbsp;<input type='radio' name='answer_type' <?php if($ans_type == 'checkbox') echo 'checked'?> id="answer_type_c" value='checkbox' onclick="selectAnswerType('checkbox');" /> <?php _e('Multiple Choices (Checkboxes)', 'watupro')?></label>
				&nbsp;&nbsp;&nbsp;
				
				<label>&nbsp;<input type='radio' name='answer_type' <?php if($ans_type == 'textarea') echo 'checked'?> id="answer_type_o" value='textarea' onclick="selectAnswerType('textarea');" /> <?php _e('Open End', 'watupro')?></label>
				&nbsp;&nbsp;&nbsp;
				
					<?php if(watupro_intel()): 
						if(@file_exists(get_stylesheet_directory().'/watupro/i/question_form.php')) require get_stylesheet_directory().'/watupro/i/question_form.php';
						else require WATUPRO_PATH."/i/views/question_form.php";					
					endif; ?>
					
				<div id="openEndText" class="wrap" style='display:<?php echo ($ans_type == 'textarea')?'block':'none'?>;'>
					<p><?php _e("In open-end questions you can also add any number of answers but none of them will be shown to the end user. Instead of that if the answer they typed matches any of your answers the matching points will be assigned.", 'watupro')?></p>
				
					<p><label><?php _e('Matching mode:', 'watupro');?></label> <select name="open_end_mode">
						<option value="loose" <?php if(empty($question->ID) or $question->open_end_mode == 'loose') echo 'selected'?>><?php _e('Loose', 'watupro');?></option>
						<option value="contained" <?php if(!empty($question->ID) and $question->open_end_mode == 'contained') echo 'selected'?>><?php _e('User answer text contains your answer', 'watupro');?></option>
						<option value="contains" <?php if(!empty($question->ID) and $question->open_end_mode == 'contains') echo 'selected'?>><?php _e('Your answer text contains the whole user answer', 'watupro');?></option>
						<option value="exact" <?php if(!empty($question->ID) and ($question->open_end_mode == 'exact' or empty($question->open_end_mode))) echo 'selected'?>><?php _e('Exact match (case-insensitive)', 'watupro');?></option>
						<option value="exact_sensitive" <?php if(!empty($question->ID) and ($question->open_end_mode == 'exact_sensitive')) echo 'selected'?>><?php _e('Strict exact match (case-sensitive)', 'watupro');?></option>
					</select> <a href="http://blog.calendarscripts.info/open-end-essay-questions-in-watupro/" target="_blank"><?php _e('Learn more', 'watupro')?></a></p>		
					
					<p><?php _e('Display mode:', 'watupro')?> <select name="open_end_display" onchange="this.value == 'text' ? jQuery('#wtpLimitWords').hide() : jQuery('#wtpLimitWords').show();">
						<option value="medium" <?php if(empty($question->open_end_display) or strstr($question->open_end_display, 'medium')) echo 'selected'?>><?php _e('Medium box ("textarea")', 'watupro')?></option>
						<option value="large" <?php if(!empty($question->open_end_display) and strstr($question->open_end_display, 'large')) echo 'selected'?>><?php _e('Large box ("textarea")', 'watupro')?></option>
						<option value="text" <?php if(!empty($question->open_end_display) and strstr($question->open_end_display, 'text')) echo 'selected'?>><?php _e('Text input field (single-line)', 'watupro')?></option>
					</select>
					
						<span style='display:<?php echo (empty($question->open_end_display) or !strstr($question->open_end_display, 'text')) ? 'inline' : 'none';?>' id="wtpLimitWords">
							<?php _e('Words limit:', 'watupro')?> <input type="text" name="limit_words" size="6" value="<?php echo empty($question->limit_words) ? 0 : $question->limit_words;?>"> 
							<?php _e('Enter 0 for no limit.', 'watupro');?>
						</span>					
					</p>
					
					<?php if($exam->no_ajax):?>
						<p><input type="checkbox" name="accept_file_upload" value="1" <?php if(!empty($question->open_end_display) and strstr($question->open_end_display, '|file')) echo 'checked'?> onclick="if(this.checked && this.form.is_required.checked) { jQuery('#fileIsRequired').show() } else { jQuery('#fileIsRequired').hide()};"> <?php _e('Accept also file upload with the answer', 'watupro')?></p>
					<?php endif;?>
				</div>
				
				<div id="trueFalseArea" class="wrap" style='display:<?php echo ($ans_type == 'radio') ? 'block':'none'?>;'>
					<p>&nbsp;<input type="checkbox" id="wtpTrueFalse" name="truefalse" value="1" <?php if(!empty($question->truefalse)) echo "checked"?> onclick="wtpSetTrueFalse(this.checked);"> <?php _e("This is a True/False question", 'watupro');?></p>	
					<p>&nbsp;<input type="checkbox" name="is_dropdown" value="1" <?php if(@$question->open_end_display == 'dropdown') echo "checked"?>> <?php _e("Display as drop-down selector instead of a radio buttons group.", 'watupro');?></p>	
				</div>
				
				<p>&nbsp;<input type="checkbox" name="is_required" value="1" <?php if(!empty($question->is_required)) echo "checked"?> onclick="if(this.checked && this.form.accept_file_upload.checked) { jQuery('#fileIsRequired').show() } else { jQuery('#fileIsRequired').hide()};"> <?php _e("Answering this question is required", 'watupro');?>
				<?php if($exam->no_ajax):?>
				<span id="fileIsRequired" style='display:<?php echo (empty($question->is_required) or empty($question->open_end_display) or !strstr($question->open_end_display, '|file') or !$exam->no_ajax) ? 'none' :'inline'?>'>&nbsp;&nbsp;<input type="checkbox" name="file_upload_required" value="1" <?php if(!empty($question->file_upload_required)) echo "checked"?>> <?php _e("File upload is required", 'watupro');?></span>
				<?php endif;?></p>	
				<p>&nbsp;<input type="checkbox" name="is_survey" value="1" <?php if(!empty($question->is_survey)) echo "checked"?>> <?php printf(__("This is a survey question (will not be marked as correct/incorrect). Its answers will never be randomized regardless of the %s settings.", 'watupro'), WATUPRO_QUIZ_WORD);?></p>	
				<p style='display:<?php echo ($exam->randomize_questions == 1 or $exam->randomize_questions == 3) ? 'block' : 'none'; ?>;'>&nbsp;<input type="checkbox" name="dont_randomize_answers" value="1" <?php if(!empty($question->dont_randomize_answers)) echo "checked"?>> <?php _e("Don't randomize answers.", 'watupro');?></p>	
				<p>&nbsp;<input type="checkbox" name="exclude_on_final_screen" value="1" <?php if(!empty($question->exclude_on_final_screen)) echo "checked"?>> <?php _e("Exclude from showing in the final screen (when %%ANSWERS%% variable is used).", 'watupro');?></p>	
				<?php if($ans_type != 'gaps'):?>
					<p> <?php if($ans_type == 'radio' or $ans_type == 'checkbox'):?>
						<span id="wtpNumColumns" style='display:<?php echo empty($question->compact_format) ? 'inline' : 'none';?>'><?php _e('Display possible answers in:', 'watupro');?> <select name="num_columns" onchange="this.value > 1 ? jQuery('#columnFixedWidth').show() : jQuery('#columnFixedWidth').hide();">
							<option value="1" <?php if(empty($question->num_columns) or $question->num_columns == 1) echo 'selected'?>><?php _e('1 column (default)', 'watupro');?></option>
							<option value="2" <?php if(!empty($question->num_columns) and $question->num_columns == 2) echo 'selected'?>><?php _e('2 columns', 'watupro');?></option>
							<option value="3" <?php if(!empty($question->num_columns) and $question->num_columns == 3) echo 'selected'?>><?php _e('3 columns', 'watupro');?></option>
							<option value="4" <?php if(!empty($question->num_columns) and $question->num_columns == 4) echo 'selected'?>><?php _e('4 columns', 'watupro');?></option>
							<option value="5" <?php if(!empty($question->num_columns) and $question->num_columns == 5) echo 'selected'?>><?php _e('5 columns', 'watupro');?></option>
						</select> <span id="columnFixedWidth" style='display:<?php echo (empty($question->num_columns) or $question->num_columns == 1) ? 'none' : 'inline';?>'>
					 <?php printf(__('(Optional fixed width: %spx)', 'watupro'), '<input type="text" name="column_width" size="4" value="'.(empty($question_design['column_width']) ? '' : $question_design['column_width']).'">');?>
					</span> <?php _e('OR', 'watupro');?></span>
					<?php endif;?>
					
					&nbsp;<input type="checkbox" name="compact_format" id="compactFormat" value="1" <?php if(!empty($question->compact_format) and ($question->compact_format == 1 or $question->compact_format == 3)) echo "checked"?> onclick="this.checked ? jQuery('#wtpNumColumns').hide() : jQuery('#wtpNumColumns').show();"> <?php _e("Display in compact format", 'watupro');?>					
					<span style='display:<?php echo ($ans_type == 'sort') ? 'inline' : 'none';?>' id="horizontalSortableCheck">
						<input type="checkbox" name="compact_format" value="2" <?php if(!empty($question->compact_format) and $question->compact_format == 2) echo "checked"?> onclick="if(this.checked) { jQuery('#compactFormat').removeAttr('checked')}"> <?php _e("Display as horizontal sortable", 'watupro');?>
					</span>	
					<span style='display:<?php echo (empty($question->compact_format) and !empty($question->ID)) ? 'none' : 'inline';?>' id="compactVersionCheck">
						<select name="compact_format_version">
							<option value="1"<?php if(!empty($question->compact_format) and $question->compact_format == 1) echo ' selected';?>><?php _e('Version 1', 'watupro');?></option>
							<option value="2"<?php if(!empty($question->compact_format) and $question->compact_format == 3) echo ' selected';?>><?php _e('Version 2', 'watupro');?></option>
							<option value="3"<?php if(!empty($question->compact_format) and $question->compact_format == 4) echo ' selected';?>><?php _e('Version 3', 'watupro');?></option>
						</select>
						<a href="https://blog.calendarscripts.info/compact-format-questions-in-watupro/" target="_blank"><?php _e("What's this?", 'watupro');?></a>
					</span>		
					</p>
				<?php if(!empty($advanced_settings['accept_rating']) and !empty($advanced_settings['accept_rating_per_question'])):?>
					<p>&nbsp;<input type="checkbox" name="accept_rating" value="1" <?php if(!empty($question->accept_rating)) echo 'checked'?>> <?php _e("Allow users to rate this question", 'watupro');?></p>
				<?php endif;?>	
				<?php endif;?>	
				<p>&nbsp;<input type="checkbox" name="round_points" value="1" <?php if(!empty($question->round_points)) echo "checked"?>> <?php _e("Round the points collected from this question to the closest decimal. (Example: 0.98 points will be rounded to 1 point. But 0.9 points will remain 0.9 points.)", 'watupro');?></p>
				<p>&nbsp;<input type="checkbox" name="no_negative" value="1" <?php if(!empty($question->no_negative)) echo "checked"?>> <?php _e("Discard negative points so points collected on this question are never below zero.", 'watupro');?></p>
				<p><?php _e('Penalize not-answering this question with', 'watupro');?> <input type="text" size="4" name="unanswered_penalty" value="<?php echo @$question->unanswered_penalty?>"> <?php _e('<b>negative</b> points (type positive number).', 'watupro')?></p>
				
				<div id="maxSelections" style='display:<?php echo (empty($question->ID) or $question->answer_type!='checkbox')?'none':'block'?>;'>
					<p><?php _e('Maximum selections allowed:','watupro')?> <input type="text" name="max_selections" value="<?php echo @$question->max_selections?>" size="4"> <?php _e('(Leave as 0 for unlimited)', 'watupro')?></p>
					<p id="checkboxGroups"><input type="checkbox" name="allow_checkbox_groups" value="1" <?php if(!empty($question->allow_checkbox_groups)) echo 'checked'?> onclick="wtpAllowCheckboxGroups(this);"> <?php printf(__('Allow checkbox groups (<a href="%s" target="_blank">Learn how this works</a>)', 'watupro'), 'http://blog.calendarscripts.info/group-checkbox-questions-in-watupro/');?></p>
					<p id="flashCards"><input type="checkbox" name="is_flashcard" value="1" <?php if(!empty($question->is_flashcard)) echo 'checked'?> onclick="if(this.checked) { this.form.allow_checkbox_groups.checked = false;}"> <?php printf(__('This question uses flashcards (<a href="%1$s" target="_blank">flashcard design settings</a>). You need to enter the two sides of each flashcard separated by %2$s sign. The respondent should leave flipped the cards that match correctly and unflip the cards that are wrong. (<a href="%3$s" target="_blank">Learn more about this</a>)', 'watupro'), 'admin.php?page=watupro_flashcard_design', $flashcard_design['flashcard_separator'], 'http://blog.calendarscripts.info/flashcards/');?></p>
				</div>
				
				<div id="questionCorrectCondition" style='display:<?php echo (empty($question->ID) or $question->answer_type=='radio')?'none':'block'?>;'>
					<p><strong><?php _e('Answering this question will be considered CORRECT when:', 'watupro')?></strong></p>
					
					<p><input type="radio" name="correct_condition" value="any" <?php if(@$question->correct_condition!='all') echo 'checked'?> onclick="jQuery('#rewardOnlyCorrect').hide();"> <?php _e('Positive number of points is achieved (so at least one correct answer is given)', 'watupro')?></p>
					<p><input type="radio" name="correct_condition" value="all" <?php if(@$question->correct_condition=='all') echo 'checked'?> onclick="jQuery('#rewardOnlyCorrect').show();"> <?php _e('The maximum number of points is achieved (so all positive-point answers are given and none negative-points answer is given.)', 'watupro')?>										
					</p>
					<div id="rewardOnlyCorrect" style='display:<?php echo @$question->correct_condition=='all' ? 'block' : 'none';?>;'>
							<p><input type="checkbox" name="reward_only_correct" value="1" <?php if(!empty($question->reward_only_correct)) echo 'checked'?>> <?php _e('Discard the collected positive points on the question unless this condition is satisfied.', 'watupro')?>
							<input type="checkbox" name="discard_even_negative" value="1" <?php if(!empty($question->discard_even_negative)) echo 'checked'?>> <?php printf(__('Discard ANY collected points on the question unless this condition is satisfied (so the %s taker gets 0 points for the question).', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
							<p><?php _e('IMPORTANT: <b>Answers that have 0 points assigned to them do not affect the question points balance!</b> When the user selects all correct + some "0 points" answers, the question will be considered correctly answered. <b>If you do not want this you must assign negative points to the wrong answers.</b>', 'watupro')?></p>						
					</div>	
					
					<p><?php printf(__('Maximum points this question will reward to the user regardless of other calculations: %s (enter 0 or leave empty for no maximum. This serves as a cap on top only.)', 'watupro'),
						'<input type="text" name="max_allowed_points" size="6" value="'.@$question->max_allowed_points.'" id="maxAllowedPoints">');?></p>					
					
					<div id="checkboxAsWhole" style='display:<?php echo ($ans_type == 'checkbox') ? 'block' : 'none'?>;'>
         			<p><input type="checkbox" name="calculate_checkbox_whole" value="1" <?php if(!empty($question->calculate_whole) and $ans_type == 'checkbox') echo "checked"?> onclick="this.checked? jQuery('#checkboxAsWholeOptions').show() : jQuery('#checkboxAsWholeOptions').hide();if(this.checked) {jQuery('#maxAllowedPoints').val(jQuery('#correctCheckboxPoints').val()) }; "> <?php _e('Treat this question as a whole', 'watupro')?></p>
         			<p id="checkboxAsWholeOptions" style='display:<?php echo empty($question->calculate_whole) ? 'none' : 'block';?>'>
                  <?php _e('When you select this, the points collected or lost by the individual answers will be discarded and the points given here will be assigned to the question.<br> Note that you still need to assign points to the individual answers exactly to be able to determine when the question is answered correctly!', 'watupro')?>  <br>       			
         			<span><?php _e('Points to assign when the whole question is answered correctly accordingly to the above condition:', 'watupro'); ?></span> <input type="text" name="correct_checkbox_points" value="<?php echo (@$question->correct_gap_points > 0) ? @$question->correct_gap_points  : 1;?>" size="4" id="correctCheckboxPoints" onkeyup="jQuery('#maxAllowedPoints').val(this.value)"> &nbsp; <span><?php _e('Points to assign when the whole question is not answered correctly (optional):', 'watupro');?></span> <input type="text" name="incorrect_checkbox_points" value="<?php echo @$question->incorrect_gap_points?>" size="4"> <?php _e('(Decimals allowed)', 'watupro')?></p>			
         		</div>	
				</div>
			  
		</div>
		</div>
		
		<?php if(watupro_intel()):  
			if(@file_exists(get_stylesheet_directory().'/watupro/i/answer_area.php')) require get_stylesheet_directory().'/watupro/i/answer_area.php';
			else require WATUPRO_PATH."/i/views/answer_area.php";		
		endif; ?>
		
		<?php do_action('watupro_question_form', @$question);?>
		
		<div class="postbox" id="answersArea" style='display:<?php echo (empty($question) or ($question->answer_type!='gaps' and $question->answer_type!='sort' and $question->answer_type != 'matrix' and $question->answer_type != 'nmatrix'))?'block':'none';?>'>
			<h3 class="hndle">&nbsp;<span><?php _e('Answers', 'watupro') ?></span></h3>
			<div class="inside" id="answerAreaInside">	
				<p class="help"><?php _e('Correct answers must always have positive number of points. If you forget this, 1 point will be automatically assigned to each correct answer when saving the question.', 'watupro')?></p>
				<?php for($i=1; $i<=$answer_count; $i++): ?>
				<p style="border-bottom:1px dotted #ccc" <?php if($i>2) echo "class='wtp-notruefalse'"?>>
					<a href="#" onclick="watuPROMCE('wtpAnswer<?php echo $i;?>', this);return false;">Rich Text Editor</a><br />					 
						<textarea name="<?php echo !empty($truefalse) ? 'answer-ignored' : 'answer[]'?>" id="wtpAnswer<?php echo $i?>" class="answer answer-textarea i18n-multilingual" rows="3" cols="50" <?php if(!empty($truefalse)) echo "style='display:none;'"?>><?php if($action == 'edit' or !empty($_GET['question'])) echo stripslashes(htmlspecialchars(@$all_answers[$i-1]->answer)); ?></textarea>
						<span style='font-weight:bold;margin-right:100px;<?php if(empty($truefalse)) echo 'display:none;'?>' class="truefalse-text"><?php echo ($i ==1) ? __('True', 'watupro') : __('False', 'watupro');?></span>
						<input type="hidden" name="<?php echo !empty($truefalse) ? 'answer[]' : 'answer-ignored'?>" class="answer-hidden" value="<?php echo ($i ==1) ? __('True', 'watupro') : __('False', 'watupro');?>">
						
					<label for="correct_answer_<?php echo $i?>"><?php _e("Correct Answer", 'watupro'); ?></label>
					<input type="<?php print ($ans_type=='radio')?'radio':'checkbox'?>" class="correct_answer" id="correct_answer_<?php echo $i?>" <?php if(@$all_answers[$i-1]->correct == 1) echo 'checked="checked"';?> name="correct_answer[]" value="<?php echo $i?>" />
					<label style="margin-left:10px"><?php _e('Points:', 'watupro')?> <input type="text" class="numeric" size="4" name="point[]" value="<?php if($action == 'edit' or !empty($_GET['question'])): echo stripslashes(@$all_answers[$i-1]->point); else: echo $default_incorrect_points; endif; ?>" id="answer_points_<?php echo $i?>"></label>
					<span class="wtp-chkradio-span" style='display:<?php echo ($ans_type == "radio" or $ans_type == "checkbox") ? 'inline' : 'none';?>'>
						<input type="<?php print ($ans_type=='radio')?'radio':'checkbox'?>" class="is_checked" id="is_checked_<?php echo $i?>" name="is_checked[]" value="<?php echo $i?>" <?php if(@$all_answers[$i-1]->is_checked == 1) echo 'checked="checked"';?>> <label for="is_checked_<?php echo $i?>"><?php _e('Checked by default', 'watupro');?></label> 					
						<input type="checkbox" class="wtp-freetext" id="accept_freetext_<?php echo $i?>" name="accept_freetext[]" value="<?php echo $i?>" <?php if(@$all_answers[$i-1]->accept_freetext == 1) echo 'checked="checked"';?>> <label for="accept_freetext_<?php echo $i?>"><?php _e('Allow also a free text answer', 'watupro');?></label>
					</span>
					<span class="wtp_chk_group" style='display:<?php echo ($ans_type == 'checkbox' and !empty($question->allow_checkbox_groups)) ? 'inline' : 'none';?>'>
					    <label  style="margin-left:10px"><?php _e('Checkbox group:', 'watupro');?></label> <input type="text" name="chk_group[]" value="<?php echo intval(@$all_answers[$i-1]->chk_group);?>" size="2">
					</span>
					<?php if(watupro_intel() and !empty($exam->is_personality_quiz)):
						if(@file_exists(get_stylesheet_directory().'/watupro/i/grade-to-answer.html.php')) require get_stylesheet_directory().'/watupro/i/grade-to-answer.html.php';
						else require WATUPRO_PATH."/i/views/grade-to-answer.html.php";		 
					endif;?>				
				</p>
				<?php endfor; ?>
			</div>
			<div class="inside" id="answerAreaAddNew" style='display:<?php echo empty($truefalse) ? 'block' : 'none';?>'>
				<style>#extra-answers p{border-bottom:1px dotted #ccc;}</style>
				<div id="extra-answers"></div>
				<a href="javascript:newAnswer();"><?php _e("Add New Answer", 'watupro'); ?></a>				
			</div>				
		</div>
	
		<div class="wrap inside">
			<h3>&nbsp;<?php _e('Optional Answer Explanation / Feedback Shown At The End', 'watupro') ?></h3>			
			<div class="inside">
				<p><a href="#" onclick="jQuery('#wtpExplainAnswer').toggle();return false;"><?php _e('Show / hide box', 'watupro');?></a></p>
				<div style='display:<?php echo empty($question->explain_answer) ? 'none' : 'block';?>' id="wtpExplainAnswer">		
					<?php echo wp_editor(stripslashes(@$question->explain_answer), "explain_answer", array("editor_class" => 'i18n-multilingual'));?>
					<br />
					<p><?php printf(__('You can use this field to explain the correct answer. This will be shown only at the end of the %1$s if you have choosen to display correct answers. Use the tag %2$s if you want to show how many points the user has earned on this question and %3$s if you want to show the maximum possible points.', 'watupro'), WATUPRO_QUIZ_WORD, '{{{points}}}', '{{{max-points}}}'); ?></p>
					<p><input type="checkbox" name="dont_explain_unanswered" value="1" <?php if(!empty($question->dont_explain_unanswered)) echo 'checked';?>> <?php _e("Don't display feedback if the question is left unanswered.", 'watupro');?> <?php if(watupro_intel()): _e('(Some question types like sortable and slider do not have "unanswered" state - they are always answered in some way.)', 'watupro'); endif;?></p>					
					<p><input type="checkbox" name="do_elaborate_explanation" value="1" <?php if(!empty($question->elaborate_explanation)) echo 'checked'?> onclick="this.checked ? jQuery('#elaborateExplanation').show() : jQuery('#elaborateExplanation').hide();"> <?php _e('Elaborate answer feedback', 'watupro')?></p>
					<div id="elaborateExplanation" style='display:<?php echo empty($question->elaborate_explanation)?'none':'block'?>;margin-left:50px;'>
						<p><input type="radio" name="elaborate_explanation" value="boolean" <?php if(!empty($question->elaborate_explanation) and $question->elaborate_explanation == 'boolean') echo 'checked'?> onclick="jQuery('#elaborateBoolean').show();jQuery('#elaborateExact').hide();"> <?php _e('I want to have different answer feedback for correctly and incorrectly answered question.', 'watupro');?></p>
						<p><input type="radio" name="elaborate_explanation" value="exact" <?php if(!empty($question->elaborate_explanation) and $question->elaborate_explanation == 'exact') echo 'checked'?> onclick="jQuery('#elaborateBoolean').hide();jQuery('#elaborateExact').show();"> <?php _e('I want to have different answer feedback depending on every specific user selection.', 'watupro');?></p>
						
						<p id="elaborateBoolean" style='display:<?php echo (empty($question->elaborate_explanation) or $question->elaborate_explanation!='boolean')?'none':'block'?>'><?php printf(__('In this case please use the tag {{{split}}} to split the two contents. The content that should be shown if the question is answered correctly goes before the {{{split}}} tag, and the incorrect goes after it. For more info visit <a href="%s" target="_blank">this link</a>.', 'watupro'), 'http://blog.calendarscripts.info/watupro-answer-feedback-elaboration/');?></p>
						<p id="elaborateExact" style='display:<?php echo (empty($question->elaborate_explanation) or $question->elaborate_explanation!='exact')?'none':'block'?>'><?php printf(__('In this case please use the tag {{{split}}} to split between all the feedbacks. They should be ordered in the same way you have ordered the user choices. For more info visit <a href="%s" target="_blank">this link</a>.', 'watupro'), 'http://blog.calendarscripts.info/watupro-answer-feedback-elaboration/');?></p>
					</div>
				</div> <!-- end wtpExplainAnswer div-->
				
				<p><input type="checkbox" name="accept_feedback" value="1" <?php if(!empty($question->accept_feedback)) echo 'checked'?> onclick="this.checked ? jQuery('#acceptFeedback').show() : jQuery('#acceptFeedback').hide();"> <?php _e('Accept feedback / comments from users', 'watupro')?></p>
				
				<div id="acceptFeedback" style='display:<?php echo empty($question->accept_feedback)?'none':'block'?>'>
					<p><?php _e('A text box will be displayed along with the question to allow the user to comment on the question along with answering it.', 'watupro')?><br>
					<?php _e('The following label will be displayed above the box:', 'watupro')?>
					<input type="text" name="feedback_label" value="<?php echo empty($question->feedback_label) ? __('Your comments:', 'watupro') : stripslashes($question->feedback_label)?>"></p>
				</div>
			</div>
		</div>
		
		<?php if(!empty($exam->question_hints)):?>
		<div class="postbox">
			<h3 class="hndle">&nbsp;<?php _e('Question Hints', 'watupro') ?></h3>
			<div class="inside">		
				<?php echo wp_editor(stripslashes(@$question->hints), "hints", array("editor_class" => 'i18n-multilingual'));?>
				
				<p><?php printf(__('Question hints support rich text formatting. You can enter multiple hints for this question splitting them with the tag %s.', 'watupro'), '{{{split}}}')?></p>
				
				<p><?php printf(__("Reduce %s points per each hint used. %s Don't go below zero points for the question.", 'watupro'),
					'<input type="text" size="4" name="reduce_points_per_hint" value="'.@$question->reduce_points_per_hint.'">', 
					'<input type="checkbox" name="reduce_hint_points_to_zero" value="1" '.(empty($question->reduce_hint_points_to_zero) ? '' : 'checked="true"').'>');?></p>
			</div>
		</div>
		<?php endif;?>
		
		<div class="postbox">
			<h3 class="hndle">&nbsp;<?php _e('Other Adjustments', 'watupro') ?></h3>
			<div class="inside">		
				<p><input type="checkbox" name="use_wpautop" value="1" <?php if(!empty($question->use_wpautop)) echo 'checked';?>> <?php _e('New lines do not look right. Revert to using the WordPress auto paragraphs function.', 'watupro');?></p>
			</div>
		</div>
	
	</div>
	
	
	<p class="submit">
		<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']?>" />
		<input type="hidden" name="question" value="<?php echo stripslashes(@$_REQUEST['question'])?>" />
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) @$user_ID ?>" />
		<input type="hidden" name="action" value="<?php echo $action ?>" />
		<input type="hidden" name="goto_rich_text" value="0" />
		<input type="hidden" name="ok" value="1" />
		<span id="autosave"></span>
		<input type="submit" value="<?php _e('Save Question', 'watupro') ?>" class="button-primary" />
		<input type="submit" name="reuse" value="<?php _e('Save &amp; Reuse as New', 'watupro') ?>" class="button-primary" />
		<?php if(empty($question->ID)):?>
		  <input type="submit" name="add_blank" value="<?php _e('Save &amp; Add New (Blank)', 'watupro') ?>" class="button-primary" />
		<?php endif;?>
		<input type="submit" name="preview" value="<?php _e('Save &amp; Preview', 'watupro') ?>" class="button-primary" />
	</p>
	<p><?php _e('If you click on the "Save & Reuse as New" button the question will be saved and the form for the next question will be loaded with the prefilled settings and answers to save you time.', 'watupro');?></p>
	<a href="admin.php?page=watupro_questions&amp;quiz=<?php echo $_REQUEST['quiz']?>"><?php _e("Go to Questions Page", 'watupro') ?></a>
	<?php wp_nonce_field('watupro_question');?>
	</form>
	
	<!-- new rich text editor floating div -->
<div id="rtfWin" style="display:none;background:white;border:1pt solid black;padding:10px;width:60%;min-width:480px;">
	 <form>
    <?php wp_editor('', 'wtpFloatingRTF', array('editor_height' => 425,'textarea_rows' => 10, 'tinymce' => array('width' => '100%', 'forced_root_block' => false)));?>
    <p><input type="button" class="button button-primary" value="<?php _e('Done. Close this.', 'watupro');?>" id="floatingOKBtn" onclick="jQuery('#'+this.form.floating_answer_transfer_id.value).val(tinymce.get('wtpFloatingRTF').getContent());jQuery('#rtfWin').hide();"></p>
    <input type="hidden" name="floating_answer_transfer_id" id="floatingAnswerTransferID" value="">
    </form>
</div>

</div>


<script type="text/javascript">

function selectAnswerType(ansType) {
	jQuery('#openEndText').hide();
	jQuery('#answersArea').hide();
	jQuery('#questionCorrectCondition').hide();
	jQuery('#maxSelections').hide();
	jQuery('#trueFalseArea').hide();
	jQuery('#checkboxAsWhole').hide();
	jQuery('.wtp-chkradio-span').hide();
	wtpSetTrueFalse(false);
	<?php if(watupro_intel()):?>
	jQuery('#fillTheGapsText').hide();
	jQuery('#sliderText').hide();
	jQuery('#sortingText').hide();
	jQuery('#sortAnswerArea').hide();
	jQuery('#matrixAnswerArea').hide();
	jQuery('#horizontalSortableCheck').hide();
	<?php endif;?>
	
	switch(ansType) {
		case 'radio': jQuery('#answersArea').show(); jQuery('#trueFalseArea').show(); wtpSetTrueFalse(jQuery('#wtpTrueFalse').attr('checked')); jQuery('.wtp-chkradio-span').show(); break;
		case 'checkbox': jQuery('#answersArea').show(); jQuery('#questionCorrectCondition').show(); jQuery('#maxSelections').show(); jQuery('#checkboxAsWhole').show(); jQuery('.wtp-chkradio-span').show(); break;
		case 'textarea': jQuery('#answersArea').show(); jQuery('#questionCorrectCondition').show(); jQuery('#openEndText').show(); break;
		<?php if(watupro_intel()):?>
		case 'gaps': jQuery('#fillTheGapsText').show(); jQuery('#questionCorrectCondition').show(); break;
		case 'sort': jQuery('#sortingText').show(); jQuery('#sortAnswerArea').show(); jQuery('#questionCorrectCondition').show(); jQuery('#horizontalSortableCheck').show(); break;
		case 'matrix': case 'nmatrix': jQuery('#sortingText').show(); jQuery('#questionCorrectCondition').show(); jQuery('#matrixAnswerArea').show(); break;
		case 'slider': jQuery('#sliderText').show(); jQuery('#answersArea').show(); break;
		<?php endif;?>
	}
}

// handles the specific behavior of true/false qustions
// @param mode boolean (true when true/false question, false otherwise)
function wtpSetTrueFalse(mode) {
	if(mode) {
		jQuery('#answerAreaAddNew').hide();		
		jQuery('.wtp-notruefalse').hide();		
		jQuery('.wtpRTELink').hide();
		jQuery('.answer-textarea').hide();
		jQuery('.truefalse-text').show();
		jQuery('.answer-textarea').attr('name', 'answer-ignored');
		jQuery('.answer-hidden').attr('name', 'answer[]');
	} 
	else {		
		jQuery('#answerAreaAddNew').show();	
		jQuery('.wtp-notruefalse').show();
		jQuery('.wtpRTELink').show();
		jQuery('.answer-textarea').show();
		jQuery('.truefalse-text').hide();
		jQuery('.answer-textarea').attr('name', 'answer[]');
		jQuery('.answer-hidden').attr('name', 'answer-ignored');
	}
	
	// jQuery('#answerAreaInside').html(htmlContents);
}

var allowCheckboxGroups = <?php echo ($ans_type == 'checkbox' and !empty($question->allow_checkbox_groups)) ? 'true' : 'false';?>;
function wtpAllowCheckboxGroups(chk) {
   if(chk.checked) {
      jQuery('.wtp_chk_group').show();
      chk.form.is_flashcard.checked = false;
      allowCheckboxGroups = true;
   }
   else {
      jQuery('.wtp_chk_group').hide();
      allowCheckboxGroups = false;
   }
}

jQuery.fn.wtpCenter = function () {
	 this.show();
    this.css("position","absolute");
    this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) + 
                                                jQuery(window).scrollTop()) + "px");
    this.css("left", Math.max(0, ((jQuery(window).width() - jQuery(this).outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");
    return this;
}

function wtpAcceptFreetext() {
	jQuery('.wtp-freetext').click(function(){
		if(jQuery(this).is(':checked')) {
			jQuery('.wtp-freetext').prop('checked', false);
			jQuery(this).prop('checked', true);
		}	
	});
}

function watuPROMCE(textareaID, lnk) {
	//tinymce.init({selector:'textarea#' + textareaID});
	settings = {	
	    tinymce: {
	        wpautop  : true,
	        theme    : 'modern',
	        skin     : 'lightgray',
	        language : 'en',
	        formats  : {
	            alignleft  : [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'left' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'alignleft' }
	            ],
	            aligncenter: [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'center' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'aligncenter' }
	            ],
	            alignright : [
	                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'right' } },
	                { selector: 'img,table,dl.wp-caption', classes: 'alignright' }
	            ],
	            strikethrough: { inline: 'del' }
	        },
	        relative_urls       : false,
	        remove_script_host  : false,
	        convert_urls        : false,
	        browser_spellcheck  : true,
	        fix_list_elements   : true,
	        entities            : '38,amp,60,lt,62,gt',
	        entity_encoding     : 'raw',
	        keep_styles         : false,
	        paste_webkit_styles : 'font-weight font-style color',
	        preview_styles      : 'font-family font-size font-weight font-style text-decoration text-transform',
	        tabfocus_elements   : ':prev,:next',
	        plugins    : 'charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview',
	        resize     : 'vertical',
	        menubar    : false,
	        indent     : false,
	        toolbar1   : 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
	        toolbar2   : 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
	        toolbar3   : '',
	        toolbar4   : '',
	        body_class : 'id post-type-post post-status-publish post-format-standard',
	        wpeditimage_disable_captions: false,
	        wpeditimage_html5_captions  : true
	
	    },
	    quicktags   : true,
	    mediaButtons: true
	
	}
	wp.editor.initialize(textareaID, settings);
	lnk.style.display='none';
} // end watuMCE
</script>