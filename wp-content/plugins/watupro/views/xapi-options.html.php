<div class="wrap">
	<h1><?php _e('xAPI / Tin Can Options', 'watupro');?></h1>
	
	<p><?php printf(__('xAPI / Tin Can integration requires installing the free <a href="%s" target="_blank">WP Experience API plugin</a>. Your learning record store options should be saved there. On this page you only set up which activities from your system will go into the record store.', 'watupro'), 'https://wordpress.org/plugins/wp-experience-api/');?></p>
	
	<form method="post">
		<h2><?php _e('LMS Activities To Track', 'watupro');?></h2>
		
		<p><input type="checkbox" name="passed_exam" value="1" <?php if(!empty($options['passed_exam'])) echo "checked"?>> <?php printf(__('User passed %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
		<p><input type="checkbox" name="failed_exam" value="1" <?php if(!empty($options['failed_exam'])) echo "checked"?>> <?php printf(__('User failed %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
		<p><?php printf(__('By default each %s is considered passed when the user has completed it. However you can define that only some grades/result make the %s passed from the Edit %s -> Advanced Settings tab. Thus achieving some of the grades that do not pass the %s will make it failed. Note that these actions will be sent to the LRS only when taker email is provided or the taker is logged in user.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD, ucfirst(WATUPRO_QUIZ_WORD), WATUPRO_QUIZ_WORD);?> </p>
		
		<p><input type="submit" name="ok" value="<?php _e('Save Options', 'watupro');?>"></p>
		<?php wp_nonce_field('watupro_xapi');?>
	</form>
</div>