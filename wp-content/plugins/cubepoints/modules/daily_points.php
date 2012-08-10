<?php

/** Daily Points Module */

cp_module_register(__('Daily Points', 'cp') , 'dailypoints' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Give your users points for visiting your site everyday or every fixed interval.', 'cp'), 1);

function cp_module_dailypoints_install(){
	add_option('cp_module_dailypoints_points', 5);
	add_option('cp_module_dailypoints_time', 86400);
}
add_action('cp_module_dailypoints_activate','cp_module_dailypoints_install');

if(cp_module_activated('dailypoints')){

	function cp_module_dailypoints_checkTimer() {
		if(!is_user_logged_in()) return;
		$uid = cp_currentUser();
		$time = get_option('cp_module_dailypoints_time');
		$difference = time() - $time;
		global $wpdb;
		$count = (int) $wpdb->get_var("SELECT COUNT(*) FROM ".CP_DB." WHERE `uid`=$uid AND `timestamp`>$difference AND `type`='dailypoints'");
		if($count!=0) return;
		cp_points('dailypoints', $uid, get_option('cp_module_dailypoints_points'), '');
	}
	
	add_action('init', 'cp_module_dailypoints_checkTimer', 1);
	
	function cp_module_dailypoints_config(){
	?>
		<br />
		<h3><?php _e('Daily Points','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_module_dailypoints_points"><?php _e('Points awarded', 'cp'); ?>:</label></th>
				<td valign="middle" colspan="2"><input type="text" id="cp_module_dailypoints_points" name="cp_module_dailypoints_points" value="<?php echo get_option('cp_module_dailypoints_points'); ?>" size="30" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cp_module_dailypoints_time"><?php _e('Time interval for awarding points (in seconds)', 'cp'); ?>:</label></th>
				<td valign="middle" width="190"><input type="text" id="cp_module_dailypoints_time" name="cp_module_dailypoints_time" value="<?php echo get_option('cp_module_dailypoints_time'); ?>" size="30" /></td>
				<td valign="middle">
					Presets:
					<a href="javascript:void(0);" onclick="document.getElementById('cp_module_dailypoints_time').value='86400';">Daily</a> |
					<a href="javascript:void(0);" onclick="document.getElementById('cp_module_dailypoints_time').value='43200';">Every 12 hours</a> | 
					<a href="javascript:void(0);" onclick="document.getElementById('cp_module_dailypoints_time').value='7200';">Every 2 hours</a> | 
					<a href="javascript:void(0);" onclick="document.getElementById('cp_module_dailypoints_time').value='3600';">Every hour</a>
				</td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_dailypoints_config');
	
	function cp_module_dailypoints_config_process(){
		$cp_module_dailypoints_points = (int) $_POST['cp_module_dailypoints_points'];
		$cp_module_dailypoints_time = (int) $_POST['cp_module_dailypoints_time'];
		if($cp_module_dailypoints_time<0) $cp_module_dailypoints_time = 0;
		update_option('cp_module_dailypoints_points', $cp_module_dailypoints_points);
		update_option('cp_module_dailypoints_time', $cp_module_dailypoints_time);
	}
	add_action('cp_config_process','cp_module_dailypoints_config_process');
	
	/** Daily Points Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_dailypoints', 10, 4);
	function cp_admin_logs_desc_dailypoints($type,$uid,$points,$data){
		if($type!='dailypoints') { return; }
		_e('Daily Points', 'cp');
	}
	
}
	
?>