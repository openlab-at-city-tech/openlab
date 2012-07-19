<?php

/** Limit Logs Display Module */

cp_module_register(__('Limit Logs Display', 'cp') , 'limitlogs' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('This module allows you to limit the number of log items being displayed in the logs page.', 'cp'), 1);

function cp_module_limitlogs_install(){
	add_option('cp_module_limitlogs_max', 100);
}
add_action('cp_module_limitlogs_activate','cp_module_limitlogs_install');

if(cp_module_activated('limitlogs')){
	function cp_module_limitlogs_config(){
	?>
		<br />
		<h3><?php _e('Limit Logs Display','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_module_limitlogs_max"><?php _e('Number of log entries to show', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_limitlogs_max" name="cp_module_limitlogs_max" value="<?php echo get_option('cp_module_limitlogs_max'); ?>" size="30" /></td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_limitlogs_config');
	
	function cp_module_limitlogs_config_process(){
		$cp_module_limitlogs_max = ((int)$_POST['cp_module_limitlogs_max']<1)?1:(int)$_POST['cp_module_limitlogs_max'];
		update_option('cp_module_limitlogs_max', $cp_module_limitlogs_max);
	}
	add_action('cp_config_process','cp_module_limitlogs_config_process');
	
	add_filter('cp_admin_logs_limit',create_function('$num', 'return '.get_option('cp_module_limitlogs_max').';'),10);
}
?>