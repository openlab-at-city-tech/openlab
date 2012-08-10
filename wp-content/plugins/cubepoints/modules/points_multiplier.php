<?php

/** Points Multiplier Module */

cp_module_register(__('Points Multiplier', 'cp') , 'pmultiply' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('This module allows you to temporarily double, triple points earned by users. Useful if you wish to increase user activity in a particular week. You may also use this module to temporarily disable points from being earned.', 'cp'), 1);

function cp_module_pmultiply_install(){
	add_option('cp_module_pmultiply_multiplier', 2);
}
add_action('cp_module_pmultiply_activate','cp_module_pmultiply_install');

if(cp_module_activated('pmultiply')){
	function cp_module_pmultiply_config(){
	?>
		<br />
		<h3><?php _e('Points Multiplier','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_module_pmultiply_multiplier"><?php _e('Multiply points earned by', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pmultiply_multiplier" name="cp_module_pmultiply_multiplier" value="<?php echo get_option('cp_module_pmultiply_multiplier'); ?>" size="30" /></td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_pmultiply_config');
	
	function cp_module_pmultiply_config_process(){
		$cp_module_pmultiply_multiplier = ((int)$_POST['cp_module_pmultiply_multiplier']<0)?0:(int)$_POST['cp_module_pmultiply_multiplier'];
		update_option('cp_module_pmultiply_multiplier', $cp_module_pmultiply_multiplier);
	}
	add_action('cp_config_process','cp_module_pmultiply_config_process');
	
	add_filter('cp_points',create_function('$points', 'return '.get_option('cp_module_pmultiply_multiplier').'*$points;'),10);
}
?>
