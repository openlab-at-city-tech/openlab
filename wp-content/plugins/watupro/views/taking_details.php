<style type="text/css">
<?php watupro_resp_table_css(800);?>
</style>
<link type="text/css" rel="stylesheet" href="<?php echo WATUPRO_URL.'style.css' ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo WATUPRO_URL.'css/conditional.css' ?>" />
<div class="wrap watupro-wrap" style="display:block;float:left;">

	<h2><?php printf(__('Details for completed %s ', 'watupro'), WATUPRO_QUIZ_WORD)?>"<?php echo stripslashes($exam->name)?>"</h2>
	<?php if(current_user_can(WATUPRO_MANAGE_CAPS) and empty($_GET['export'])):?>
		<p><?php _e('User:', 'watupro')?> <?php
		if(!empty($taking->user_id)): $full_name = $student->first_name ?  $student->first_name.' '.$student->last_name : $student->user_login; endif; 
		echo $taking->user_id?"<a href='user-edit.php?user_id=".$taking->user_id."&wp_http_referer=".urlencode("admin.php?page=watupro_takings&exam_id=".$exam->ID)."' target='_blank'>".$full_name."</a>":($taking->email?$taking->email:"<b>N/A</b>")?><br>
	<?php endif;?>	
	<?php if(current_user_can(WATUPRO_MANAGE_CAPS) and !empty($_GET['export'])):?>
		<p><?php _e('User:', 'watupro')?> <?php
		if(!empty($taking->user_id)): $full_name = $student->first_name ?  $student->first_name.' '.$student->last_name : $student->user_login; endif; 
		echo $taking->user_id ? $full_name : ($taking->email ? $taking->email : "<b>N/A</b>")?><br>
	<?php endif;?>	
	<?php _e('Date:', 'watupro')?> <?php echo date(get_option('date_format'), strtotime($taking->date)) ?><br>
	<?php if(empty($disallow_results)):?>
	<?php _e('Total points collected:', 'watupro')?> <b><?php echo $taking->points;?></b><br>
	<?php if(!empty($gtitle)):?><?php _e('Achieved grade:', 'watupro')?> <b><?php echo $gtitle;?></b><?php endif;?></p>
	<?php endif;?>
	<?php if(empty($_GET['export'])):?>
		<?php if(current_user_can(WATUPRO_MANAGE_CAPS)):?>
		<p><?php printf(__('The textual details below show exact snapshot of the questions in the way that student have seen them when taking the %s. If you have added, edited or deleted questions since then you will not see these changes here.', 'watupro'), __('quiz', 'watupro'))?></p>
		<?php endif;?>
		<?php if(empty($advanced_settings['show_only_snapshot']) and !$disallow_results):?>
			<p><a href="#" onclick="jQuery('#detailsText').css({position: 'absolute', top: '-9999px', left: '-9999px'});jQuery('#catgradesTable').hide();jQuery('#detailsTable').show();return false;"><?php _e('Table format', 'watupro')?></a>
			<?php if(!empty($catgrades) and is_array($catgrades) and count($catgrades)):?>	
			&nbsp;<a href="#" onclick="jQuery('#catgradesTable').show();jQuery('#detailsTable').hide();jQuery('#detailsText').hide();return false;"><?php _e('Performance per category', 'watupro')?></a>
			<?php endif;?>
			&nbsp;<a href="#" onclick="jQuery('#detailsText').show();jQuery('#detailsText').css({position: 'relative', top: 10, left: 10});jQuery('#catgradesTable').hide();jQuery('#detailsTable').hide();return false;"><?php _e('Snapshot', 'watupro')?></a>  
			&nbsp; <a href="<?php echo admin_url('admin-ajax.php?action=watupro_taking_details&noheader=1&id='. $taking->ID .'&export=1');?>"><?php _e('Download', 'watupro')?></a></p>
		<?php endif; // end if not $advanced_settings['show_only_snapshot']
	endif; // end if not export?>	
	
	<?php if(empty($_GET['export']) or $default_view == 'snapshot'):?>
	<div id="detailsText" style='padding:5px;width:90%;<?php if(empty($advanced_settings['show_only_snapshot']) and !$disallow_results and $default_view != 'snapshot'):?>position:absolute;top:-9999px;left:-9999px;<?php endif;?>'>	
		<?php if($disallow_results) echo apply_filters('watupro_content', stripslashes($exam->delay_results_content)); 
		else echo WatuPRO::cleanup($taking->details, 'web'); ?>
	</div>
	<?php endif;?>
	
	<?php if(empty($advanced_settings['show_only_snapshot']) and !$disallow_results):	
	if(empty($_GET['export'])):?> <div id="detailsTable" <?php if(!empty($default_view) and $default_view == 'snapshot') echo 'style="display:none;"';?>> <?php endif;?>	
	<?php if(empty($_GET['export']) and empty($taking->from_watu)):?>
	<p><?php _e('Show:', 'watupro');?> <a href="#" onclick="watuPRODetailsTableShow('all');return false;"><?php _e('All', 'watupro');?></a> | <a href="#"  onclick="watuPRODetailsTableShow('correct');return false;"><?php _e('Correct', 'watupro');?></a> | <a href="#"  onclick="watuPRODetailsTableShow('wrong');return false;"><?php _e('Wrong', 'watupro');?></a> | <a href="#"  onclick="watuPRODetailsTableShow('empty');return false;"><?php _e('Unanswered', 'watupro');?></a></p>
	<?php endif; // links to choose show only if not downloading 
	if(!empty($taking->from_watu)) printf('<p>'.__('This %s attempt was done in the free Watu plugin. Detailed per-question data is not available for these submissions.', 'watupro').'</p>', WATUPRO_QUIZ_WORD);
	if(empty($taking->from_watu) and (empty($_GET['export']) or $default_view == 'table')):?>	
	<table align="center" class="widefat watupro-table">
		<thead>
			<tr> 
			 	<?php if(empty($view_details_hidden_columns) or !in_array('id', $view_details_hidden_columns)):?><th><?php _e('ID', 'watupro')?></th><?php endif;?>
				<?php if(empty($view_details_hidden_columns) or !in_array('num', $view_details_hidden_columns)):?><th><?php _e('No.', 'watupro')?></th><?php endif;?>
				<th><?php _e('Question', 'watupro')?></th>
				<?php if(empty($view_details_hidden_columns) or !in_array('cat', $view_details_hidden_columns)):?><th><?php _e('Category', 'watupro')?></th><?php endif;?>				
				<th><?php _e('Answer(s) given', 'watupro')?></th>
				<?php if(current_user_can(WATUPRO_MANAGE_CAPS) or !empty($advanced_settings['show_result_and_points'])):?>			
					<?php if(empty($view_details_hidden_columns) or !in_array('points', $view_details_hidden_columns)):?><th><?php _e('Points received', 'watupro')?></th><?php endif;?>
					<?php if(empty($view_details_hidden_columns) or !in_array('result', $view_details_hidden_columns)):?><th><?php _e('Result', 'watupro');?></th><?php endif;?>
				<?php endif;?>	
			</tr>
		</thead>
		<tbody>
			<?php foreach($answers as $cnt => $answer):
			   if(empty($class)) $class = 'alternate';
			   else $class = '';				
				$answer_class = '';
				if($answer->answer == '') $answer_class = 'empty';
				else $answer_class = ($answer->is_correct ? 'correct' : 'wrong');
				
				// remove <script tags from answers if any. We won't use htmlspecialchars on this because it causes issues with sortables, gaps, etc
				$answer->answer = str_replace('<script', '<i', $answer->answer);

				// prepare question text
				$question_text = ($answer->question_text == 'data removed' or empty($answer->question_text)) ? wpautop(stripslashes($answer->question)) : wpautop(stripslashes($answer->question_text));
				$question_text = str_replace('{{{ID}}}', $answer->question_id, $question_text);
				?>
				<tr class="<?php echo $class?> watupro-answer-row-<?php echo $answer_class?> watupro-answer-row">
					<?php if(empty($view_details_hidden_columns) or !in_array('id', $view_details_hidden_columns)):?><td><?php echo $answer->question_id?></td><?php endif; // end if ID not hidden in WatuPRO Settings page?>
					<?php if(empty($view_details_hidden_columns) or !in_array('num', $view_details_hidden_columns)):?><td><?php echo $cnt+1;?></td><?php endif; // end if No. not hidden in WatuPRO Settings page?>				
				<td><?php echo $question_text?></td>
				<?php if(empty($view_details_hidden_columns) or !in_array('cat', $view_details_hidden_columns)):?><td><?php echo stripslashes($answer->category);?></td><?php endif; // end if No. not hidden in WatuPRO Settings page?>			
				<td><?php if(empty($advanced_settings['answer_snapshot_in_table_format'])) echo nl2br(stripslashes($answer->answer));
				else echo $answer->snapshot;
				if(!empty($answer->file->ID)) echo "<p><a href=".site_url('?watupro_download_file=1&id='.$answer->file->ID).">".sprintf(__('Uploaded: %s (%d KB)', 'watupro'), $answer->file->filename, $answer->file->filesize)."</a>";
				if(!empty($answer->feedback)) echo wpautop("<b>".stripslashes($answer->feedback_label)."</b><br>".htmlspecialchars(stripslashes($answer->feedback)));
				if(!empty($answer->rating)) echo "<p>".sprintf(__('Rating: %d', 'watupro'), $answer->rating)."</p>";?></td>
				<?php if(current_user_can(WATUPRO_MANAGE_CAPS) or !empty($advanced_settings['show_result_and_points'])):?>		
					<?php if(empty($view_details_hidden_columns) or !in_array('points', $view_details_hidden_columns)):?><td><?php echo $answer->points;
					if($answer->calculate_whole) echo '<br>'.__('(calculated for the whole question)', 'watupro');?></td><?php endif; // end if points not hidden in WatuPRO Settings page;?>
					<?php if(empty($view_details_hidden_columns) or !in_array('result', $view_details_hidden_columns)):?><td><?php if($answer->is_survey): _e('N/a (survey question)', 'watupro'); 
					else: 
						if($answer->answer == ''): echo __('Not answered', 'watupro');
						else: echo $answer->is_correct ? __('Correct answer', 'watupro') : __('Wrong answer', 'watupro');
						endif;
					endif;
					if(!empty($answer->teacher_comments)): echo wpautop($answer->teacher_comments); endif;?></td><?php endif; // end if result not hidden in WatuPRO Settings page;?>
				<?php endif;?>
				</tr>
			<?php endforeach;?>
		</tbody>	
	</table>
	<?php endif; // end if default_view == table or not export 
	if(empty($_GET['export'])):?></div>
	<div id="catgradesTable" style="display:none;">
		<table align="center" class="widefat watupro-table">
			<thead>
				<tr><th><?php _e('Category', 'watupro');?></th><th><?php _e('% correct', 'watupro');?></th>
				<th><?php _e('Points', 'watupro');?></th>
				<th><?php _e('% of Max. Points', 'watupro');?></th>
				<th><?php _e('Grade', 'watupro');?></th></tr>
				</thead>
			<tbody>	
					<?php  if(!empty($catgrades) and is_array($catgrades)): 
					foreach($catgrades as $catgrade):
						$cls = ('alternate' == @$cls) ? '' : 'alternate';?>
						<tr class="<?php echo $cls;?>"><td><?php echo stripslashes($catgrade['name']);?></td><td><?php echo $catgrade['percent'];?>%</td>												
						<td><?php echo $catgrade['points'];?></td>
						<td><?php echo $catgrade['percent_points'];?>%</td>
						<td><?php echo $catgrade['gtitle'] ? stripslashes($catgrade['gtitle']) : __('N/a', 'watupro');?></td></tr>
					<?php endforeach;
					endif;?>	
			</tbody>
		</table>	
	</div>	
	<?php endif;
	endif; // end if not $advanced_settings['show_only_snapshot']?>
	
</div>

<script type="text/javascript" >

// in the details popup table shows rows that are 
// unanswered, all, wrong, correct 
function watuPRODetailsTableShow(what) {
	if(what == 'all') {
		jQuery('.watupro-answer-row').show();
		return true;
	}
	
	jQuery('.watupro-answer-row').hide();
	jQuery('.watupro-answer-row-' + what).show();
}
</script>