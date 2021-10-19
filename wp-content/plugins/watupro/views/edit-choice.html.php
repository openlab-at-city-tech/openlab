<div class="wrap watupro-wrap">
	<h1><?php _e('Editing an answer to question', 'watupro')?></h1>
	
	<p><a href="admin.php?page=watupro_question&question=<?php echo $question->ID?>&action=edit&quiz=<?php echo $quiz->ID?>"><?php _e('Back to edit the whole question', 'watupro')?></a></p>
	
	<p><b><?php _e('This page allows you to use the rich text editor and easily add images and media to the possible answers to each question. Points and other properties of the answer itself can be edited on the "edit question" page.', 'watupro')?></b></p>
	
	<p><a href="#" onclick="jQuery('#wtpQuestionContents').toggle();return false;"><?php _e('Remind me the question', 'watupro')?></a></p>
	
	<div id="wtpQuestionContents" style="display:none;"><hr>
	<?php echo apply_filters("watupro_content", stripslashes($question->question))?>	
	<hr></div>
	
	<form method="post" id="wtpEditChoice">
	<p><?php echo wp_editor(stripslashes($choice->answer), 'answer', array("editor_class" => 'i18n-multilingual'))?></p>
	<p align="center"><input type="submit" value="<?php _e('Save answer contents', 'watupro')?>"></p>
	<input type="hidden" name="ok" value="1">		
	</form>
</div>