<style type="text/css">
<?php watupro_resp_table_css(600);?>
tr.watupro-sortable-question {
	cursor:move;
}
</style>

<div class="wrap watupro-wrap">
	<h1><?php _e("Manage Questions in ", 'watupro')?> <?php echo apply_filters('watupro_qtranslate', stripslashes($exam_name)); ?></h1>
	
	<?php watupro_display_alerts(); ?>
	
	<p><a href="admin.php?page=watupro_exams"><?php printf(__('Back to %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></a> 
	&nbsp;|&nbsp;
	<a href="edit.php?page=watupro_exam&quiz=<?php echo $_GET['quiz']?>&action=edit"><?php printf(__('Edit this %s', 'watupro'), WATUPRO_QUIZ_WORD);?></a>
	&nbsp;|&nbsp;
	<a href="admin.php?page=watupro_grades&quiz=<?php echo $_GET['quiz']?>"><?php if(empty($exam->is_personality_quiz)) _e('Manage Grades', 'watupro');
	else  _e('Manage Personality Types', 'watupro');?></a>
	<?php if(empty($reusing_questions)):?>&nbsp;|&nbsp;
	<a href="admin.php?page=watupro_questions&export=1&exam_id=<?php echo $_GET['quiz']?>&noheader=1&copy=1"><?php _e('Export Questions', 'watupro')?></a>
	&nbsp;|&nbsp;
	<a href="admin.php?page=watupro_advanced_import&id=<?php echo $_GET['quiz']?>"><?php _e('Import questions', 'watupro')?></a>	
	&nbsp;|&nbsp;
	<a href="admin.php?page=watupro_copy_exam&id=<?php echo $_GET['quiz']?>&comefrom=questions"><?php printf(__('Copy %s and / or questions', 'watupro'), WATUPRO_QUIZ_WORD)?></a>
	<?php endif;?></p>
	
<p class="note"><?php printf(__('Note: The fields in the CSV export files are currently separated by <b>%s</b>. You can change this <a href="%s">at the Settings page</a>.', 'watupro'), ((get_option('watupro_csv_delim') == 'tab') ? __('TAB', 'watupro') : __('COMMA', 'watupro')), 'admin.php?page=watupro_options');?> </p>
	
	<p style="color:green;"><?php printf(__('To add this %s to your site, insert the code ', 'watupro'), WATUPRO_QUIZ_WORD) ?> <b>[watupro <?php echo $_REQUEST['quiz'] ?>]</b> <?php _e('into any post or page.', 'watupro') ?></p>
	
	<?php $intelligence_display=""; // variable used to hide the div with own questions if required
	if(watupro_intel()):
	require_once(WATUPRO_PATH."/i/models/question.php");
	WatuPROIQuestion::reuse_questions($exam, $intelligence_display);
	endif;?>
	
<div id="watuProQuestions" <?php echo $intelligence_display;?>>
	
	<form method="get" action="admin.php">
		<input type="hidden" name="page" value="watupro_questions">
		<input type="hidden" name="quiz" value="<?php echo $exam->ID?>">
		<p><label><?php _e('Show questions from category:', 'watupro')?></label> <select name="filter_cat_id">
		<option value=""><?php _e('- All categories -', 'watupro')?></option>
		<?php foreach($qcats as $cat):?>
			<option value="<?php echo $cat->ID?>"<?php if(!empty($_GET['filter_cat_id']) and $_GET['filter_cat_id'] == $cat->ID) echo ' selected'?>><?php echo $cat->name?></option>
			<?php foreach($cat->subs as $sub):?>
					<option value="<?php echo $sub->ID?>" <?php if(!empty($_GET['filter_cat_id']) and $_GET['filter_cat_id'] == $sub->ID) echo ' selected'?>> - <?php echo stripslashes(apply_filters('watupro_qtranslate', $sub->name));?></option>
		<?php endforeach; 
		endforeach;?>
		<option value="-1"<?php if(!empty($_GET['filter_cat_id']) and $_GET['filter_cat_id']==-1) echo ' selected'?>><?php _e('Uncategorized', 'watupro')?></option>
		</select>
		&nbsp;
		<?php _e('and tagged as:', 'watupro')?> <input type="text" name="filter_tag" value="<?php echo empty($_GET['filter_tag']) ? '' : esc_attr($_GET['filter_tag'])?>"> 
		&nbsp;
		<?php _e('from question type:', 'watupro')?> <select name="filter_answer_type">
			<option value=""><?php _e('- All question types -', 'watupro');?></option>
			<option value="radio" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'radio') echo 'selected';?>><?php _e('Single choice (radio buttons)', 'watupro');?></option>
			<option value="truefalse" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'truefalse') echo 'selected';?>><?php _e('True / false', 'watupro');?></option>
			<option value="checkbox" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'checkbox') echo 'selected';?>><?php _e('Multiple choice (checkboxes)', 'watupro');?></option>
			<option value="textarea" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'textarea') echo 'selected';?>><?php _e('Open end / essay', 'watupro');?></option>
			<?php if(watupro_intel()):?>
				<option value="gaps" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'gaps') echo 'selected';?>><?php _e('Fill the gaps', 'watupro');?></option>
				<option value="sort" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'sort') echo 'selected';?>><?php _e('Sort the values', 'watupro');?></option>
				<option value="nmatrix" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'nmatrix') echo 'selected';?>><?php _e('Match / matrix', 'watupro');?></option>
				<option value="slider" <?php if(!empty($_GET['filter_answer_type']) and $_GET['filter_answer_type'] == 'slider') echo 'selected';?>><?php _e('Slider', 'watupro');?></option>
			<?php endif;?>
		</select>
		<br><br>
		<?php if(!empty($difficulty_levels) and count($difficulty_levels)):?>			
			<?php _e('with difficulty level:', 'watupro');?> <select name="filter_dlevel">
				<option value=""><?php _e('- Any level -', 'watupro');?></option>
				<?php foreach($difficulty_levels as $dlev):
					$selected = (!empty($_GET['filter_dlevel']) and $_GET['filter_dlevel'] == trim($dlev)) ? ' selected' : ''; ?>
					<option value="<?php echo trim($dlev);?>"<?php echo $selected;?>><?php echo trim($dlev);?></option>
				<?php endforeach;?>
			</select> &nbsp;
		<?php endif;?>
		<?php _e('with ID (you can separate multiple IDs with comma):', 'watupro')?> <input type="text" name="filter_id" value="<?php echo empty($_GET['filter_id']) ? '' : esc_attr($_GET['filter_id'])?>" size="6"> 
		&nbsp;
		<?php _e('and question contains (phrase):', 'watupro');?> <input type="text" name="filter_contents" value="<?php echo empty($_GET['filter_contents']) ? '' : esc_attr($_GET['filter_contents'])?>">
		
		<input type="submit" value="<?php _e('Filter questions', 'watupro')?>" class="button-primary">
		<input type="button" class="button" value="<?php _e('Clear filters', 'watupro');?>" onclick="window.location='admin.php?page=watupro_questions&quiz=<?php echo $_GET['quiz']?>';"></p>	
	</form>
	

	<?php if(empty($reusing_questions)):?><p><a href="admin.php?page=watupro_question&amp;action=new&amp;quiz=<?php echo $_GET['quiz'] ?>"><?php _e('Create New Question', 'watupro')?></a></p><?php endif;?>
   
   <form method="post" action="admin.php?page=watupro_questions&quiz=<?php echo $_GET['quiz']?>">
      <p><?php _e('Questions per page:', 'watupro');?> <select name="page_limit" onchange="this.form.submit();">
         <option value="10" <?php if($page_limit == 10) echo 'selected'?>>10</option>
         <option value="20" <?php if($page_limit == 20) echo 'selected'?>>20</option>
         <option value="50" <?php if($page_limit == 50) echo 'selected'?>>50</option>
         <option value="100" <?php if($page_limit == 100) echo 'selected'?>>100</option>
         <option value="200" <?php if($page_limit == 200) echo 'selected'?>>200</option>
         <option value="500" <?php if($page_limit == 500) echo 'selected'?>>500</option>
         <option value="1000" <?php if($page_limit == 1000) echo 'selected'?>>1000</option>
      </select></p>
      
      <input type="hidden" name="reset_page_limit" value="1">
   </form>
	
	<div class="watupro-num-selected"></div>	
	
	<form method="post">
	<table class="widefat watupro-table">
		<thead>
		<tr>
			<th><input type="checkbox" onclick="WatuPROSelectAll(this);"></th>
			<th><div style="text-align: center;"><a href="admin.php?page=watupro_questions&quiz=<?php echo $_GET['quiz']?>&fix_sort_order=1" title="<?php _e('Click here to automatically fix the order of questions.', 'watupro')?>">#</a></div></th>
			<th><?php _e('ID', 'watupro') ?></th>
			<th scope="col"><?php _e('Question', 'watupro') ?></th>
			<th scope="col"><?php _e('Type', 'watupro') ?></th>
			<th scope="col"><?php _e('Category', 'watupro') ?></th>
			<?php if($low_memory_mode != 1):?><th scope="col"><?php _e('Number Of Answers', 'watupro') ?></th><?php endif;?>
			<?php if(!empty($advanced_settings['accept_rating'])):?>
				<th><?php _e('Avg. users rating', 'watupro');?></th>
			<?php endif;?>
			<th scope="col" colspan="3"><?php _e('Action', 'watupro') ?></th>
		</tr>
		</thead>
	
		<tbody id="watuproQuestionsList">
	<?php
	if (count($all_question)) :
		$bgcolor = '';		
		$question_count = 0;
		foreach($all_question as $question) :
			if($show_title_desc) $question->title = '<h3>'.$question->title.'</h3>'.$question->question; // in case user chooses to show both title and desc
			
			$question->tags = str_replace('|', ', ', substr($question->tags, 1, strlen($question->tags)-2));
			$question_count++;
			if($question->answer_type == 'sort') $question->answer_count = sizeof(explode(PHP_EOL, @$question->sorting_answers));
			if(empty($class)) $class = 'alternate';
			else $class = '';			
			print "<tr id='question-{$question->ID}' class='$class watupro-sortable-question'>\n"; ?>
			<td><input type="checkbox" name="qids[]" value="<?php echo $question->ID?>" class="qids" onclick="toggleMassDelete();" <?php if(!empty($reusing_questions) and !empty($exam->limit_reused_questions) and in_array($question->ID, $reused_question_ids)) echo 'checked';?>></td>
			<td scope="row" style="text-align: center;cursor:move;">			
			<?php echo $question_count + $offset; ?></td>
			<td><?php echo $question->ID ?></td>
			<td><?php echo empty($question->title) ? apply_filters('watupro_qtranslate', stripslashes($question->question)) : apply_filters('watupro_qtranslate', stripslashes($question->title));
			if(!empty($question->tags)) echo '<p><i>'.sprintf(__('Tags: %s', 'watupro'), $question->tags).'</i></p>';?></td>
			<td><?php switch($question->answer_type):
				case 'sorting': _e('Sorting', 'watupro'); break;
				case 'gaps': _e('Fill the gaps', 'watupro'); break;
				case 'textarea': _e('Open-end (essay)', 'watupro'); break;
				case 'checkbox': _e('Multiple choices', 'watupro'); break;			
				case 'sort': _e('Sort the values', 'watupro'); break;	
				case 'matrix': case 'nmatrix': _e('Match / Matrix', 'watupro'); break;
				case 'slider': _e('Slider', 'watupro'); break;
				case 'radio':
				default:
					if($question->truefalse) _e('True/False', 'watupro');
					else _e('Single choice', 'watupro');
				break;
			endswitch;
			if($question->is_inactive or $question->importance > 0 or $question->is_required or $question->is_survey) echo '<br />';
			if($question->is_inactive) echo "&nbsp;<span style='color:red'>".__('Inactive', 'watupro')."</span>&nbsp;";
			if($question->importance > 0) echo "&nbsp;<span style='color:green;'>".__('Important', 'watupro')."</span>&nbsp;";
			if($question->is_required) echo "&nbsp;<span style='color:blue;'>".__('Required', 'watupro')."</span>&nbsp;";
			if($question->is_survey) echo "&nbsp;<span style='color:orange;'>".__('Survey', 'watupro')."</span>&nbsp;";?></td>
			<td><?php echo $question->cat ? stripslashes(apply_filters('watupro_qtranslate', $question->cat)) : __("Uncategorized", 'watupro')?></td>
			<?php if($low_memory_mode != 1):?><td><?php if($question->answer_type != 'slider' and $question->answer_type != 'gaps') echo  $question->answer_count;
			else echo __('n/a', 'watupro');?></td><?php endif;?>
			<?php if(!empty($advanced_settings['accept_rating'])):?>
				<td><?php echo number_format_i18n($question->rating, 2)?></td>
			<?php endif;?>
			<td><a href='admin.php?page=watupro_question&amp;question=<?php echo $question->ID?>&amp;action=edit&amp;quiz=<?php echo $question->exam_id?>' class='edit' <?php if(!empty($reusing_questions)) echo 'target="_blank"';?>><?php echo (empty($reusing_questions)) ? __('Edit', 'watupro') : __('Edit in master', 'watupro'); ?></a></td>
			<td><?php if(empty($reusing_questions)):?><a href='admin.php?page=watupro_questions&amp;action=delete&amp;question=<?php echo $question->ID?>&amp;quiz=<?php echo $_GET['quiz']?>' class='delete' onclick="return confirm('<?php echo addslashes(__("You are about to delete this question. This will delete the answers to this question. Press 'OK' to delete and 'Cancel' to stop.", 'watupro'))?>');"><?php _e('Delete', 'watupro')?></a><?php endif;?></td>
			</tr>
	<?php endforeach;
		else: ?>
		<tr style='background-color: <?php echo @$bgcolor; ?>;'>
			<td colspan="4"><?php _e('No questions found.', 'watupro') ?></td>
		</tr>
	<?php endif;?>
		</tbody>
	</table>
	
	<div class="watupro-num-selected"></div>
	
	<p align="center"><?php if($offset > 0):?>
		<a href="admin.php?page=watupro_questions&quiz=<?php echo $exam->ID?>&filter_cat_id=<?php echo empty($_GET['filter_cat_id']) ? '' : esc_attr($_GET['filter_cat_id'])?>&filter_tag=<?php echo empty($_GET['filter_tag']) ? '' : esc_attr($_GET['filter_tag']);?>&filter_id=<?php echo empty($_GET['filter_id']) ? '' : esc_attr($_GET['filter_id'])?>&filter_dlevel=<?php echo empty($_GET['filter_dlevel']) ? '' : esc_attr($_GET['filter_dlevel']);?>&filter_contents=<?php echo empty($_GET['filter_contents']) ? '' : esc_attr($_GET['filter_contents'])?>&filter_answer_type=<?php echo empty($_GET['filter_answer_type']) ? '' : esc_attr($_GET['filter_answer_type']);?>&offset=<?php echo $offset - $page_limit?>"><?php echo _wtpt(__('Previous page', 'watupro'));?></a>
	<?php endif;?>
		<?php if($num_questions > ($offset + $page_limit)):?>
			&nbsp; <a href="admin.php?page=watupro_questions&quiz=<?php echo $exam->ID?>&filter_cat_id=<?php echo empty($_GET['filter_cat_id']) ? '' : esc_attr($_GET['filter_cat_id'])?>&filter_tag=<?php echo empty($_GET['filter_tag']) ? '' : esc_attr($_GET['filter_tag']);?>&filter_id=<?php echo empty($_GET['filter_id']) ? '' : esc_attr($_GET['filter_id'])?>&filter_dlevel=<?php echo empty($_GET['filter_dlevel']) ? '' : esc_attr($_GET['filter_dlevel']);?>&filter_contents=<?php echo empty($_GET['filter_contents']) ? '' : esc_attr($_GET['filter_contents'])?>&filter_answer_type=<?php echo empty($_GET['filter_answer_type']) ? '' : esc_attr($_GET['filter_answer_type']);?>&offset=<?php echo $offset + $page_limit?>"><?php echo _wtpt(__('Next page', 'watupro'))?></a>
		<?php endif;?>	
	</p>
	
	<p style="display:none;color:red;text-align:center;font-weight:bold;" id="wtpSaveOrderBtn">
		<?php _e('When done with reordering press the button to save:', 'watupro');?>
		&nbsp;
		<input type="button" class="button button-primary" value="<?php _e('Save New Questions Order', 'watupro');?>" onclick="WatuPROReorderQuestions();">
	</p>
	<p style="display:none;color:green;text-align:center;font-weight:bold;" id="wtpSaveOrderBtnMsg">
		<?php _e('Questions order saved.', 'watupro');?>
	</p>
	

	<div align="center" style="display:none;" id="massDeleteQuesions">
		<?php if(empty($reusing_questions)):?>	
			<p>
			<?php if(count($qcats)): 
				_e('Change category of selected questions to:', 'watupro');?> <select name="mass_cat_id">			
					<?php foreach($qcats as $cat):?>
						<option value="<?php echo $cat->ID?>" <?php if(!empty($question->cat_id) and $question->cat_id == $cat->ID) echo "selected"?>><?php echo stripslashes($cat->name);?></option>
						<?php foreach($cat->subs as $sub):?>
							<option value="<?php echo $sub->ID?>" <?php if(!empty($question->cat_id) and $question->cat_id == $sub->ID) echo "selected"?>> - <?php echo stripslashes($sub->name);?></option>
						<?php endforeach;?>
					<?php endforeach;?>
					</select>
			<?php endif; // end if count($qcats)?>
		 	<input type="submit" name="mass_change_category" value="<?php _e('Assign selected category', 'watupro')?>" class="button-primary">	
		 	&nbsp;
			<input type="submit" name="mass_activate" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Activate Selected', 'watupro')?>" class="button-primary">
			&nbsp;
			<input type="submit" name="mass_deactivate" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Deactivate Selected', 'watupro')?>" class="button-primary">
			&nbsp;
			<input type="submit" name="mass_delete" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Delete Selected', 'watupro')?>" class="button"></p>
			  <h3 align="center"><?php _e('Other Mass Changes:', 'watupro');?></h3>
			  <p><?php _e('Change properties of selected questions:', 'watupro');?> <select name="is_required">
			     <option value="-1"><?php _e("Don't change required status", 'watupro');?></option>
			     <option value="1"><?php _e('Change all to required', 'watupro');?></option>
			     <option value="0"><?php _e('Change all to not required', 'watupro');?></option>
			  </select> &nbsp;
			  <select name="is_important">
			     <option value="-1"><?php _e("Don't change important status", 'watupro');?></option>
			     <option value="100"><?php _e('Change all to important', 'watupro');?></option>
			     <option value="0"><?php _e('Change all to not important', 'watupro');?></option>
			  </select>
			  &nbsp;
			  <select name="is_survey">
			     <option value="-1"><?php _e("Don't change survey type", 'watupro');?></option>
			     <option value="1"><?php _e('Change all to survey', 'watupro');?></option>
			     <option value="0"><?php _e('Change all to not survey', 'watupro');?></option>
			  </select>
			  
			   <select name="accept_feedback">
			     <option value="-1"><?php _e("Don't change feedback setting", 'watupro');?></option>
			     <option value="1"><?php _e('Change all to accept feedback', 'watupro');?></option>
			     <option value="0"><?php _e('Change all to not accept feedback', 'watupro');?></option>
			  </select>
			  
			   <select name="exclude_on_final_screen">
			     <option value="-1"><?php _e("Don't change final screen setting", 'watupro');?></option>
			     <option value="1"><?php _e('Exclude from final screen (%%ANSWERS%% var)', 'watupro');?></option>
			     <option value="0"><?php _e('Include in final screen (%%ANSWERS%% var)', 'watupro');?></option>
			  </select>
			  
			  <input type="submit" name="mass_update" value="<?php _e('Update Selected', 'watupro');?>" class="button-primary"></p>
		<?php else: // handle quizzes that reuse questions here?>	
			<p align="center"><input type="submit" name="save_reused" value="<?php _e('Use Selected Questions', 'watupro');?>" class="button-primary"></p>
		<?php endif; // end mass actions - here ending save reuse questions clause ;?>		  
	</div>

	
	
	<?php if(!empty($_POST['filter_cat_id'])):?>
		<input type="hidden" name="filter_cat_id" value="<?php echo esc_attr($_POST['filter_cat_id'])?>">
	<?php endif;?>
	<?php wp_nonce_field('watupro_questions');?>
	</form>
	
	<?php // show if not reusing 
	if(empty($reusing_questions)):?>
		<p><a href="admin.php?page=watupro_question&amp;action=new&amp;quiz=<?php echo intval($_GET['quiz']) ?>"><?php _e('Create New Question', 'watupro')?></a></p>
	
		<p><?php printf(__("Note: you can drag and drop questions to reorder them. Press the button that will appear to save the new order when you are done. This will take effect for %s whose questions are <b>not randomized</b>.", 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>	
	<?php endif; // end if not reusing ?>
</div>

<h3><?php _e('Question Hints Settings:', 'watupro')?></h3>
<form method="post">
<p><input type="checkbox" name="enable_question_hints" <?php if(!empty($enable_question_hints)) echo 'checked'?> onclick="this.checked ? jQuery('#questionHints').show() : jQuery('#questionHints').hide();"> <?php printf(__('Enable question hints in this %s.', 'watupro'), WATUPRO_QUIZ_WORD)?> &nbsp; <input type="submit" name="hints_settings" value="<?php _e('Save')?>" class="button-primary"></p>
<div id="questionHints" style='display:<?php echo empty($enable_question_hints) ? 'none' : 'block';?>'>
	<p><?php printf(__('Question hints are optionally displayed to the %s taker upon request. It is usually a good idea to limit the number of hints the user can see so they have some incentive to try taking the %s without using all the hints.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD)?></p>
	<p><?php _e('Allow the user to view up to', 'watupro')?> <input type="text" size="4" name="hints_per_quiz" value="<?php echo @$hints_per_quiz?>"> <?php printf(__('total hints for the whole %s (leave 0 for unlimited hints).', 'watupro'), WATUPRO_QUIZ_WORD)?></p> 
	<?php _e('Allow the user to view up to', 'watupro')?> <input type="text" size="4" name="hints_per_question" value="<?php echo @$hints_per_question?>"> <?php _e('hints per question - for questions that have more than one hint available (leave 0 for unlimited hints).', 'watupro')?>
</div>
</form>

<?php $debug = print_r(error_get_last(),true);
	//if(!empty($debug)) echo '<p>php-error: '.esc_attr($debug).'</p>';?>

<script type="text/javascript" >
function validateWatuproImportForm(frm) {
	if(frm.csv.value=="") {
		alert("<?php _e('Please select CSV file.', 'watupro')?>");
		frm.csv.focus();
		return false;
	}
}

function WatuPROSelectAll(chk) {
	if(chk.checked) {
		jQuery(".qids").prop('checked', true);
	}
	else {
		jQuery(".qids").prop('checked', false);
	}
	
	toggleMassDelete();
}

// shows or hides the mass delete button
function toggleMassDelete() {
	var len = jQuery(".qids:checked").length;
	
	if(len) jQuery('#massDeleteQuesions').show();
	else jQuery('#massDeleteQuesions').hide();
	
	if(len) {
		var msg = "<?php echo '<p>'.__('%d questions selected on the page', 'watupro').'</p>';?>";
		msg = msg.replace('%d', len);
	}
	else msg = "";	
	
	jQuery('.watupro-num-selected').html(msg);
}

<?php if(empty($reusing_questions)):?>
jQuery(function(){
	jQuery('#watuproQuestionsList').sortable({								
		stop: function(event, ui) { 
			jQuery('#wtpSaveOrderBtn').show();
			jQuery('#wtpSaveOrderBtnMsg').hide();
		}				    	
	});
});
<?php endif;?>

function WatuPROReorderQuestions() {
	// to avoid sending many unnecessary queries to the server
	var questions = jQuery('#watuproQuestionsList').sortable("toArray");
		
	data = {"action":'watupro_ajax', 'do' : 'reorder_questions', 'exam_id' : <?php echo $exam->ID?>, 'questions' : questions };		
		
	jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data, function(msg){
		jQuery('#wtpSaveOrderBtn').hide();	
		jQuery('#wtpSaveOrderBtnMsg').show();
	});
}

<?php watupro_resp_table_js();?>
</script>