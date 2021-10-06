<div class="wrap watupro-wrap">
	<h1><?php printf(__('Copy %s "%s"', 'watupro'), WATUPRO_QUIZ_WORD, stripslashes($exam->name))?></h1>

	<p><a href="admin.php?page=watupro_exam&quiz=<?php echo $exam->ID?>&action=edit"><?php printf(__('Edit %s', 'watupro'), WATUPRO_QUIZ_WORD)?></a>
	| <a href="admin.php?page=watupro_questions&quiz=<?php echo $exam->ID?>"><?php _e('Manage questions', 'watupro')?></a></p>	

	<form method="post" action="#">
	<div id="copyExam" class="postbox">
		<div class="inside">
			<p><?php printf(__("This will copy the entire %s along with its grades and questions into another %s.", 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD)?>
			<?php if(watupro_intel()):
				echo '<br>';
				printf(__('Alternatively you can reuse the questions from other %s without copying them. <a href="%s" target="_blank">Learn more about the differences</a>.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, 'https://blog.calendarscripts.info/copy-reuse-or-import-questions-in-watupro-whats-the-difference/');
			endif;?>
			</p>
			
			<p><input type="radio" name="copy_option" value="new" checked="true" onclick="jQuery('#otherExams').hide();"> <?php printf(__("Copy into a new %s. The %s will have the same name with '(Copy)' at the end. You can edit the exam, change its name, remove questions etc, just like with every other %s that you create.", 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD)?></p>
			
			<?php if(count($other_exams)):?>
			<p><input type="radio" name="copy_option" value="exsiting" onclick="jQuery('#otherExams').show();"> <?php printf(__("Copy into existing %s. Selecting this will result in copying only the questions and grades. If you select to copy in the same quiz, this will create clones / duplicates of the selected questions and grades.", 'watupro'), WATUPRO_QUIZ_WORD)?> </p>		
			
			<div id="otherExams" style="display:none;"><?php printf(__("Select existing %s to copy questions to:", 'watupro'), WATUPRO_QUIZ_WORD)?> <select name="copy_to">
				<?php foreach($other_exams as $other_exam):?>
					<option value="<?php echo $other_exam->ID?>"><?php echo $other_exam->name;
					if($other_exam->ID == $exam->ID) echo " ".sprintf(__('(This is the same %s)', 'watupro'), WATUPRO_QUIZ_WORD);?></option>
				<?php endforeach;?>		
				</select></div>
			<?php endif;?>
			
			<p><input type="checkbox" name="copy_select" value="1" onclick="this.checked ? jQuery('#copySelection').show() : jQuery('#copySelection').hide();"> <?php _e('Select which questions and grades to copy', 'watupro')?></p>
			
			<div style="display:none;" id="copySelection">
				<p><?php _e('Filter questions by category:', 'watupro');?> <select name="qcat_filter" onchange="wtpChangeQcat(this.value);">
					<option value=""><?php _e('- All categories -', 'watupro');?></option>
					<?php foreach($qcats as $qcat):?>
						<option value="<?php echo $qcat->ID?>"><?php echo stripslashes($qcat->name);?></option>
					<?php endforeach;?>
				</select></p>
				<?php if(count($tags)):?>
					<p><?php _e('Filter questions by tag', 'watupro');?> <a href="#" onclick="jQuery('#qTagsDiv').toggle('slow');"><?php _e('Toggle tags', 'watupro');?></a></p>
					<div id="qTagsDiv" style="display: none; padding: 10px;" class="postbox">
					 <?php foreach($tags as $tag):?>
					 	<span style="white-space: nowrap;"><input type="checkbox" name="filter_tags[]" class="filter-tags" value="<?php echo trim($tag);?>" onclick="wtpFilterTags()"> <?php echo trim($tag);?> &nbsp;</span>
					 <?php endforeach;?>
					</div>
				<?php endif;?>					
			
				<h3><?php _e('Questions:', 'watupro')?></h3>
				<input type="checkbox" checked onclick="this.checked ? jQuery('.watupro-qcopy-chk').attr('checked', 'true') : jQuery('.watupro-qcopy-chk').removeAttr('checked');"> <?php _e('Check all / Uncheck all', 'watupro');?>
				<?php foreach($qcats as $qcat):
					$cnt = 0;?>
					<div id="questions-qcat-<?php echo $qcat->ID?>" class="watupro-questions-qcat">
					<h4><input type="checkbox" class="watupro-qcopy-chk" checked onclick="this.checked ? jQuery('.watupro-qcat-<?php echo $qcat->ID?>').attr('checked', 'true') : jQuery('.watupro-qcat-<?php echo $qcat->ID?>').removeAttr('checked');"><?php printf(__("Category '%s'", 'watupro'), stripslashes($qcat->name))?></h4>
					<?php foreach($questions as  $question):
						
					   if($question->cat_id != $qcat->ID) continue;
					   $cnt++;
					   
						if($show_title_desc) $question->title = '<h3>'.$question->title.'</h3>'.$question->question; // in case user chooses to show both title and desc					   
					   
					   $qtags = explode("|", $question->tags);
					   $qtags = array_filter($qtags);
					   $tag_classes = '';
					   foreach($qtags as $tag) $tag_classes .= 'watuprotag-'.$tag.' ';?>
						<div style="padding-left:15px;padding-bottom:5px;" class="watupro-copy-question <?php echo $tag_classes;?>"><input type="checkbox" value="<?php echo $question->ID?>" name="question_ids[]" checked class="watupro-qcat-<?php echo $qcat->ID?> watupro-qcopy-chk"> <?php echo sprintf(__('(ID: %d)', 'watupro'), $question->ID).' '.(empty($question->title) ? stripslashes($question->question) : stripslashes($question->title));?></div>
					<?php endforeach; // end foreach question ?>
					</div>
				<?php endforeach; // end foreach category ?>	
				<p>&nbsp;</p><hr>
				<h3><?php _e('Grades:', 'watupro')?></h3>
				<?php foreach($grades as $grade):?>
					<div><input type="checkbox" value="<?php echo $grade->ID?>" name="grade_ids[]" checked> <?php echo stripslashes($grade->gtitle)?></div>
				<?php endforeach;?>
			</div>
					<p align="center" class="submit"><input type="submit" name="copy_exam" value="<?php printf(__('OK, Copy This %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?>" class="button-primary">
		<input type="button" value="<?php _e('Cancel', 'watupro');?>" onclick="window.location='admin.php?page=watupro_exam&quiz=<?php echo $exam->ID?>&action=edit';" class="button"></p>
		</div>
	</div>
	</form>

</div>

<script type="text/javascript" >
function wtpChangeQcat(catID) {
	if(catID == '') {
		// show all
		jQuery('.watupro-questions-qcat').show();
	}
	else {
		// hide all, then show selected
		jQuery('.watupro-questions-qcat').hide();
		jQuery('#questions-qcat-' + catID).show();
	}
} // end wtpChangeQcat

// filter by tag
function wtpFilterTags() {
	// collect checked tags
	var tags = [];
	jQuery('.filter-tags').each(function(i, elt){
		if(elt.checked) tags.push(elt.value);
	});
	
	// hide all questions
	if(tags.length > 0) jQuery('.watupro-copy-question').hide();
	else jQuery('.watupro-copy-question').show();
	
	// show all from tags
	for(i = 0; i < tags.length; i++) {
		jQuery('.watuprotag-'+tags[i]).show();
	}
} // end wtpFilterTags()
</script>