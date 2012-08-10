<?php

/** Comment Spam Control Module */

cp_module_register(__('Comment Spam Control', 'cp') , 'cp_csc' , '1.1', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('When enabled, this module would only allow users to receive points for one comment per post.', 'cp'), 1);

if(cp_module_activated('cp_csc')){
	add_action('cp_comment_add', 'cp_module_csc_newComment');
	function cp_module_csc_newComment($cid) {
		if (is_user_logged_in()) {
			$cdata = get_comment($cid);
			$uid = $cdata->user_id;
			$pid = $cdata->comment_post_ID;
			global $wpdb;
			if( (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE `user_id`=$uid AND `comment_post_ID`=$pid AND comment_approved=1") != 1 ){
				add_filter('cp_comment_points',create_function('$points', 'return 0;'),10);
			}
		}
	}
}

?>