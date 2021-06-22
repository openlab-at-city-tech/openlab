<div class="wrap watu-wrap">
	<h1><?php _e("Questions Import", 'watu')?></h1>
	
	<h2><?php printf(__('%1$s: %2$s', 'watu'), ucfirst(WATU_QUIZ_WORD), stripslashes($quiz->name))?></h2>
	
	<p><a href="admin.php?page=watu_questions&quiz=<?php echo $quiz->ID?>"><?php _e('Back to manage questions', 'watu')?></a></p>
	
	<?php if(!empty($_POST['watu_import'])):
	 // output error/success message ?>
	 	<h2 style="color:green;"><?php echo $result ? __('The questions were imported.', 'watu') : __('There was an error importing your questions.', 'watu')?></h2>
	<?php
		if(!empty($non_utf8_error)):
			echo "<p style='color:red;'>".sprintf(__('The imported file was not UTF-8 encoded. If it contains non-English (non-ASCII) characters there may be problems with the imported questions. If you notice such problems you will have to delete your questions, <a href="%s" target="_blank">convert your file to UTF-8</a> and import it then.', 'watu'),
				'https://www.ablebits.com/office-addins-blog/2014/04/24/convert-excel-csv/')."</p>";
		endif;
	endif;?>
	
	<form method="post" enctype="multipart/form-data">
		<div class="inside watu">		
			<p><?php printf(__('Your CSV file should follow exactly the format <a href="%s" target="_blank">described here</a>.','watu'), 'http://blog.calendarscripts.info/import-questions-in-the-free-watu-plugin/');?></p>
			<p><label><?php _e('Upload file:', 'watu')?></label> <input type="file" name="csv"><br />
			<b><?php _e('Note: if you are uploading file containing non-English characters you should make sure the file is in Unicode format (UTF-8 encoded).', 'watu');?></b></p>
			
				<p><label><?php _e('Fields Delimiter:', 'watu')?></label> <select name="delimiter">
				<option value=","><?php _e('Comma', 'watu')?></option>
				<option value="tab"><?php _e('Tab', 'watu')?></option>
				<option value=";"><?php _e('Semicolon', 'watu')?></option>
				</select></p>
				<p><input type="checkbox" name="skip_title_row" value="1" checked> <?php _e('Skip title row', 'watu')?></p>		
				<p><?php _e('If you have problems importing files with foreign characters, please', 'watu')?> <input type="checkbox" name="import_fails" value="1"> <?php _e('check this checkbox and try again.', 'watu')?></p>
				
			<p><input type="submit" name="watu_import" value="<?php _e('Import Questions', 'watu')?>" class="button-primary"></p>
		</div>	
		<?php wp_nonce_field('watu_import_questions');?>	
	</form>	
</div>