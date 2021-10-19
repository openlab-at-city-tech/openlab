<div class="wrap watupro-wrap">
	<h1><?php printf(__('My %s Settings', 'watupro'), __('Quiz', 'watupro'))?></h1>
	
	<form method="post">
		<p><input type="checkbox" name="no_quiz_mails" value="1" <?php if(get_user_meta($user_ID, 'watupro_no_quiz_mails', true)) echo 'checked'?>> <?php printf(__('Do not send me emails about completed %s.', 'watupro'), __('quizzes', 'watupro'))?></p>	
	
		<p><input type="submit" name="ok" value="<?php _e('Save Settings', 'watupro')?>"></p>
	</form>
</div>