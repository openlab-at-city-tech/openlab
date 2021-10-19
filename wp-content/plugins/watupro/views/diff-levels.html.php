<style type="text/css">
textarea {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    width: 100%;
}
</style>
<div class="wrap watupro-wrap">
	<h1><?php _e('Manage Question Difficulty Levels', 'watupro');?></h1>
	
	<p><?php _e('Difficulty levels can be used in the administration to search the questions. You can also control which difficulty levels are active in a given quiz.', 'watupro');?><br>
		<?php _e('Please enter difficulty levels one per line.', 'watupro');?></p>
		
	<form method="post">
		<p><textarea name="difficulty_levels" rows="7" cols="50"><?php echo $diff_levels?></textarea></p>
		<p><input type="checkbox" name="apply_diff_levels" value="1" <?php if($apply_diff_levels == '1') echo 'checked'?> onclick="applyDiffLevels(this.checked);"> <?php _e('Apply difficulty level restrictions per user account.', 'watupro');?> </p>
		<div id="diffLevelsUser" style="display:<?php echo $apply_diff_levels ? 'block':'none';?>"><p><?php _e('When this is selected you will be able to limit the access of users to the difficulty level selected in their profile page.<br> If there are no difficulty levels selected on the user profile page they will have access to all questions.<br> Note that such limitations will apply together with any other limitations to the questions you give at per-quiz setting.', 'watupro');?></p>
		<p><label><?php _e('Default levels for new users:', 'watupro');?></label> <select name="user_diff_levels[]" multiple="true" size="4">
			<option value=""><?php _e('- None -', 'watupro');?></option>
			<?php foreach($diff_levels_arr as $l):
				$l = trim($l);
				$selected = in_array($l, $user_diff_levels) ? ' selected' : '';?>
				<option value="<?php echo $l?>"<?php echo $selected?>><?php echo $l?></option>
			<?php endforeach;?>
		</select></p></div>
		<input type="submit" value="<?php _e('Save Difficulty Levels', 'watupro');?>" class="button-primary">
		<input type="hidden" name="ok" value="1">
	</form>
	
	<div id="unlockLevels" style="display:<?php echo $apply_diff_levels ? 'block':'none';?>">	
		<p>&nbsp;</p>
		<h2><?php _e('Manage Difficulty Levels Unlock Criteria', 'watupro');?></h2>
		
		<p><?php _e('Here you can define criteria for users to automatically unlock difficulty levels when they achieve given points, number of correct questions etc.<br> Once a level is unlocked it will never be locked again unless you as admin do this manually from the Edit user profile page.', 'watupro');?></p>
				
		<form method="post" onsubmit="return validateLevelForm(this);">
			<p><?php _e('Unlock level', 'watupro');?> <select name="unlock_level">
			<?php foreach($diff_levels_arr as $l):
				$l = trim($l);?>
				<option value="<?php echo $l?>"><?php echo $l?></option>
			<?php endforeach;?>
		</select>
		<?php _e('when user collects min.', 'watupro');?> <input type="text" size="4" name="min_points"> <?php _e('points and answers min.', 'watupro');?>
		<input type="text" size="4" name="min_questions"> <?php _e('questions with min.')?> 
		<input type="text" size="4" name="percent_correct"> <?php _e('% correct answers. All of this from difficulty level:', 'watupro');?>
		<select name="from_level">
			<option value=""><?php _e('- Any level -', 'watupro');?></option>
			<?php foreach($diff_levels_arr as $l):
				$l = trim($l);?>
				<option value="<?php echo $l?>"><?php echo $l?></option>
			<?php endforeach;?>
		</select> <input type="submit" name="add_criteria" value="<?php _e('Add Criteria', 'watupro');?>" class="button-primary"></p></form>
		
		<?php foreach($unlocks as $unlock):?>
			<form method="post" onsubmit="return validateLevelForm(this);">
			<p><?php _e('Unlock level', 'watupro');?> <select name="unlock_level">
			<?php foreach($diff_levels_arr as $l):
				$l = trim($l);
				if($l == $unlock->unlock_level) $selected = ' selected';
				else $selected ='';?>
				<option value="<?php echo $l?>"<?php echo $selected?>><?php echo $l?></option>
			<?php endforeach;?>
			</select>
			<?php _e('when user collects min.', 'watupro');?> <input type="text" size="4" name="min_points" value="<?php echo $unlock->min_points;?>"> <?php _e('points and answers min.', 'watupro');?>
			<input type="text" size="4" name="min_questions" value="<?php echo $unlock->min_questions;?>"> <?php _e('questions with min.')?> 
			<input type="text" size="4" name="percent_correct" value="<?php echo $unlock->percent_correct;?>"> <?php _e('% correct answers. All of this from difficulty level:', 'watupro');?>
			<select name="from_level">
			<option value=""><?php _e('- Any level -', 'watupro');?></option>
			<?php foreach($diff_levels_arr as $l):
			  	$l = trim($l);	
				if($l == $unlock->from_level) $selected = ' selected';
				else $selected ='';?>
				<option value="<?php echo $l?>"<?php echo $selected?>><?php echo $l?></option>
			<?php endforeach;?>
			</select> <input type="submit" name="save_criteria" value="<?php _e('Save', 'watupro');?>" class="button-primary">
			<input type="button" value="<?php _e('Delete', 'watupro')?>" onclick="confirmDelUnlock(this.form);" class="button"></p>
			<input type="hidden" name="id" value="<?php echo $unlock->ID?>">
			<input type="hidden" name="del_criteria" value="0">
			</form>
		<?php endforeach;?>
		
		<?php if($count_logs):?>
			<p>&nbsp;</p>
	   	<h2><?php _e('Unlock levels log', 'watupro');?></h2>
	   	
	   	<table class="widefat">
	   		<tr><th><?php _e('User name', 'watupro');?></th><th><?php _e('Unlocked level', 'watupro');?></th><th><?php _e('Date / time', 'watupro');?></th>
	   			<th><?php _e('Quiz / Details', 'watupro');?></th></tr>
	   		<?php foreach($logs as $log):
	   			$class = ('alternate' == @$class) ? '' : 'alternate';?>
	   			<tr class="<?php echo $class?>">
	   				<td><?php echo $log->user_name;?></td>
	   				<td><?php echo $log->unlocked_level?></td>
	   				<td><?php echo date($timeformat, strtotime($log->taken_time));?></td>
	   				<td><a href="admin.php?page=watupro_takings&exam_id=<?php echo $log->exam_id?>&taking_id=<?php echo $log->taking_id?>" target="_blank"><?php echo stripslashes($log->quiz_name);?></a></td>
	   			</tr>
	   		<?php endforeach;?>	
	   	</table>
	   	
	   	<p><?php if($offset > 0):?>
	   		<a href="admin.php?page=watupro_diff_levels&offset=<?php echo $offset - $page_limit?>"><?php _e('previous page', 'watupro');?></a>
	   	<?php endif;?>
	   	<?php if(($offset + $page_limit) < $count_logs):?>
	   		<a href="admin.php?page=watupro_diff_levels&offset=<?php echo $offset + $page_limit?>"><?php _e('next page', 'watupro');?></a>
	   	<?php endif;?></p>
	   <?php endif; // end showing unlock logs; ?>	
	</div>
</div>

<script type="text/javascript" >
function validateLevelForm(frm) {
	 if(frm.unlock_level.value == frm.from_level.value) {
	 	alert("<?php _e('From level cannot be the same as the unlock level', 'watupro');?>");
	 	frm.from_level.focus();
	 	return false;
	 }
	 
	 if((frm.min_points.value == '' || isNaN(frm.min_points.value)) &&
	 		(frm.min_questions.value == '' || isNaN(frm.min_questions.value)) &&
	 		(frm.percent_correct.value == '' || isNaN(frm.percent_correct.value))) {
	 			alert("<?php _e('You have to enter at least one valid condition to save this unlock criteria', 'watupro');?>");
	 			frm.min_points.focus();
	 			return false;
	 } 
	 
	 return true;
}

function confirmDelUnlock(frm) {
	if(confirm("<?php _e('Are you sure?', 'watupro');?>")) {
		frm.del_criteria.value=1;
		frm.submit();
	}
}

function applyDiffLevels(status) {
	if(status) {
		jQuery('#diffLevelsUser').show();
		jQuery('#unlockLevels').show();
	}
	else {
	   jQuery('#diffLevelsUser').hide();
	   jQuery('#unlockLevels').hide();
	}
}
</script>