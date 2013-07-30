<?php

/** Reset Data Module */

cp_module_register(__('Reset Data', 'cp') , 'resetdata' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Erases all point data from the database.', 'cp'), 1);

if(cp_module_activated('resetdata')){

function cp_module_resetdata_add_admin_page(){
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Reset Data','cp'), __('Reset Data','cp'), 'manage_options', 'cp_modules_resetdata_admin', 'cp_modules_resetdata_admin');
}
add_action('cp_admin_pages','cp_module_resetdata_add_admin_page');

function cp_modules_resetdata_admin(){

// handles form submissions
if ($_POST['cp_module_resetdata_form_submit'] == 'Y') {
	global $wpdb;
	$wpdb->query('TRUNCATE TABLE `'.CP_DB.'`');
	$wpdb->query('UPDATE  `'.$wpdb->base_prefix.'usermeta` SET  `meta_value` = 0 WHERE `meta_key` = \'cpoints\'');
	echo '<div class="updated"><p><strong>'.__('Database reseted!','cp').'</strong></p></div>';
}
?>

<div class="wrap">
	<h2>CubePoints - <?php _e('CubePoints - Reset Data', 'cp'); ?></h2>
	<?php _e('Erase all point data from the database.', 'cp'); ?><br /><br />

	<form name="cp_module_resetdata_form" method="post" onsubmit="return confirm('<?php _e('Are you sure you wish to completely erase the CubePoints points database?', 'cp'); ?>');">
		<input type="hidden" name="cp_module_resetdata_form_submit" value="Y" />
	<h3><?php _e('Reset Data','cp'); ?></h3>
	<p style="color:red;"><?php _e('WARNING: Clicking the button below would reset all the points for your users to zero and remove all log entries.', 'cp'); ?></p>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Reset Data','cp'); ?>" />
	</p>
</form>
</div>
<?php
}
}
	
?>