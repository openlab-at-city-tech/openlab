<?php

/** My Points Module */

cp_module_register(__('My Points', 'cp') , 'mypoints' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Allow users to see a history of their point transactions.', 'cp'), 1);

if(cp_module_activated('mypoints')){

	add_action('admin_print_scripts-cubepoints_page_cp_modules_mypoints_admin', 'cp_datatables_script');
	add_action('admin_print_styles-cubepoints_page_cp_modules_mypoints_admin', 'cp_datatables_style');

	function cp_module_mypoints_add_admin_page(){
		add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('My Points','cp'), __('My Points','cp'), 'read', 'cp_modules_mypoints_admin', 'cp_modules_mypoints_admin');
	}
	add_action('cp_admin_pages','cp_module_mypoints_add_admin_page');

	function cp_modules_mypoints_admin(){

		echo '<div class="wrap">';
		echo '<h2>CubePoints - ' . __('My Points', 'cp') . '</h2>';
		echo __('Manage and view information about your points.', 'cp');
		echo '<br /><br />';
		echo '<div style="background:#EFEFEF;display:inline-block;margin-right:25px;"><div style="float:left;font-size:17px;font-weight:bold;background:#E0E0E0;padding:18px;color:#565656;">' . __('My Points', 'cp') . ':</div><div style="float:left;padding:18px;font-size:20px;">' . cp_getPoints(cp_currentUser()) . '</div></div>';
		if(cp_module_activated('ranks')){
			echo '<div style="background:#EFEFEF;display:inline-block;"><div style="float:left;font-size:17px;font-weight:bold;background:#E0E0E0;padding:18px;color:#565656;">' . __('My Rank', 'cp') . ':</div><div style="float:left;padding:18px;font-size:20px;">' . cp_module_ranks_getRank(cp_currentUser()) . '</div></div>';
		}
		echo '<div style="clear:both;"></div><br />';
		
		echo '<p style="font-weight:bold;">' . __('Your recent point transactions:', 'cp') . '</p>';
		
		cp_show_logs(cp_currentUser(), 15 , false);
		
		echo '</div>';
	}
	
}
	
?>