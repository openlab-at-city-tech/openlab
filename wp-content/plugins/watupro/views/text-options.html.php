<div class="wrap">
	<h1><?php _e('Manage Phrases', 'watupro');?></h1>
	
	<p><?php printf(__('This page lets you change or translate the most important of the user-facing words and phrases used in the plugin. This is done for convenience - if a word or phrase is not shown here you should use <a href="%s" target="_blank">the standard WordPress way</a> of changing it. You can however suggest any of the phrases to be added here.', 'watupro'), 'http://blog.calendarscripts.info/how-to-change-text-in-a-wordpress-plugin/');?></p>
	
	<p><?php _e('The words and phrases are shown in alphabetical order. If you leave the right box empty, the original phrase will be used.', 'watupro')?></p>
	
	<form method="post">
	<table class="watupro-table widefat">
		<head>
			<tr><th><?php _e('Word or phrase', 'watupro');?></th><th><?php _e('Your translation or override', 'watupro');?></th></tr>
			<?php foreach($texts as $text):
				list($left, $right) = explode('===', $text);
				$class = ('alternate' == @$class) ? "" : 'alternate';?>
				<tr class="<?php echo $class?>">	
					<td><input type="hidden" name="phrases_left[]" value="<?php echo stripslashes($left);?>">
					<?php echo $left;?></td>
					<td><input type="text" name="phrases_right[]" value="<?php echo stripslashes($right);?>">
					<?php if(strstr($left, '%s')) _e('(Your translation must contain the same number of "%s" masks as in the original phrase.)', 'watupro');?></td>
				</tr>
			<?php endforeach;?>
		</head>
	</table>
	<p align="center">
		<input type="submit" class="button button-primary" value="<?php _e('Save settings', 'watupro');?>">
	</p>
	<?php wp_nonce_field('watupro_options');?>
	<input type="hidden" name="save_options" value="1">
	</form>
	
	<p><strong><?php _e('Note that if you change your site language and apply a WatuPRO translation file the program will start using the values from your translation unless you again overwrite them here.', 'watupro');?></strong></p>
</div>