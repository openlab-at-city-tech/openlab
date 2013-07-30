<?php

/** Limit Comment Points Module */

cp_module_register(__('Limit Comment Points', 'cp') , 'lcpoints' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Limits the number of comments that would earn points for users across all posts.', 'cp'), 1);

function cp_module_lcpoints_install(){
	add_option('cp_module_lcpoints_limit', 5);
}
add_action('cp_module_lcpoints_activate','cp_module_lcpoints_install');

if(cp_module_activated('lcpoints')){

	function cp_module_lcpoints_config(){
	?>
		<br />
		<h3><?php _e('Limit Comment Points','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_module_lcpoints_limit"><?php _e('Max number of comments earning points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_lcpoints_limit" name="cp_module_lcpoints_limit" value="<?php echo get_option('cp_module_lcpoints_limit'); ?>" size="30" /></td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_lcpoints_config');
	
	function cp_module_lcpoints_config_process(){
		$cp_module_lcpoints_limit = ((int)$_POST['cp_module_lcpoints_limit']<1)?1:(int)$_POST['cp_module_lcpoints_limit'];
		update_option('cp_module_lcpoints_limit', $cp_module_lcpoints_limit);
	}
	add_action('cp_config_process','cp_module_lcpoints_config_process');

	function cp_module_lcpoints_newComment($cid) {
		if (is_user_logged_in()) {
			$uid = cp_currentUser();
			$timelimit = time() - 86400; // one day
			global $wpdb;
			$comments = (int) $wpdb->get_var('SELECT comment_add - comment_remove as comments from (SELECT 1 as id, count(*) as comment_add FROM `'. CP_DB .'` WHERE `type`=\'comment\' AND `uid`='.$uid.' AND `timestamp`>'.$timelimit.') as t1 LEFT JOIN (SELECT 1 as id, count(*) as comment_remove FROM `'. CP_DB .'` WHERE `type`=\'comment_remove\' AND `uid`='.$uid.' AND `timestamp`>'.$timelimit.') as t2 ON t1.id = t2.id');
			if( $comments >= get_option('cp_module_lcpoints_limit') ){
				add_filter('cp_comment_points',create_function('$points', 'return 0;'),10);
			}
		}
	}
	add_action('cp_comment_add', 'cp_module_lcpoints_newComment');

}

?>