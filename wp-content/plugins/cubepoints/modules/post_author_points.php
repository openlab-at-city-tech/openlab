<?php

/** Post Author Points Module */

cp_module_register(__('Post Author Points', 'cp') , 'post_author_points' , '1.2', 'xBerry Labs', 'http://xBerryLabs.com', 'http://xBerryLabs.com' , __('Gives points to authors when people comment on their posts', 'cp'), 1);

function cp_module_post_author_points_install(){
	add_option('cp_post_author_points', 1);
}
add_action('cp_module_post_author_points_activate','cp_module_post_author_points_install');

if(cp_module_activated('post_author_points')){

	function cp_module_post_author_points_config(){
	?>
		<br />
		<h3><?php _e('Post Author Points','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_post_author_points"><?php _e('Points for comments on author posts', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_post_author_points" name="cp_post_author_points" value="<?php echo get_option('cp_post_author_points'); ?>" size="30" /></td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_post_author_points_config');

	function cp_module_post_author_points_config_process(){
		$cp_post_author_points = (int)$_POST['cp_post_author_points'];
		update_option('cp_post_author_points', $cp_post_author_points);
	}
	add_action('cp_config_process','cp_module_post_author_points_config_process');

	add_action('cp_comment_add', 'cp_module_post_author_points_comment_add', 10, 1);
	function cp_module_post_author_points_comment_add($cid){
		$cdata = get_comment($cid);
		$pid = $cdata->comment_post_ID;
		$pdata = get_post($pid);
		// do not give points if comment is made by post author
		if($cdata->user_id!=$pdata->post_author){
			cp_points('post_comment', $pdata->post_author, get_option('cp_post_author_points'), $cid);
		}
	}
	
	add_action('cp_comment_remove', 'cp_module_post_author_points_comment_remove', 10, 1);
	function cp_module_post_author_points_comment_remove($cid){
		$cdata = get_comment($cid);
		$pid = $cdata->comment_post_ID;
		$pdata = get_post($pid);
		// do not subtract points if comment is made by post author
		if($cdata->user_id!=$pdata->post_author){
			cp_points('post_comment_remove', $pdata->post_author, -get_option('cp_post_author_points'), $cid);
		}
	}


	/** Post Author Points Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_post_author_points', 10, 4);
	function cp_admin_logs_desc_post_author_points($type,$uid,$points,$data){
		if($type!='post_comment') { return; }
		$cdata = get_comment($data);
		$pid = $cdata->comment_post_ID;
		$pdata = get_post($pid);
		$ptitle = $pdata->post_title;
		$url = get_permalink( $pid ) . '#comment-' . $data;
		$detail = __('Comment', 'cp').': '.cp_truncate(strip_tags($cdata->comment_content), 100, false);
		if($cdata->user_id!='0'){
			$commenter = '"<a href="'.get_author_posts_url($cdata->user_id).'">'.get_the_author_meta('display_name',$cdata->user_id).'</a>"';
		}
		else{
			$commenter = 'Someone';
		}
		echo '<span title="'.$detail.'">'.$commenter.' '.__('posted a comment on your post', 'cp').' "<a href="'.$url.'">'.$ptitle.'</a>"</span>';
	}

	/** Post Author Points Removal Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_post_author_points_remove', 10, 4);
	function cp_admin_logs_desc_post_author_points_remove($type,$uid,$points,$data){
		if($type!='post_comment_remove') { return; }
		_e('Comment on your post was removed');
	}

}

?>