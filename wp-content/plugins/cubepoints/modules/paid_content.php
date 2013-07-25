<?php

/** Paid Content Module */

cp_module_register(__('Paid Content', 'cp') , 'pcontent' , '1.1', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('This module lets you deduct point from users to view a page or post.', 'cp'), 1);

function cp_module_pcontent_install(){
	add_option('cp_module_pcontent_default_points', 10);
	add_option('cp_module_pcontent_default', false);
	add_option('cp_module_pcontent_payauthor', false);
	add_option('cp_module_pcontent_text_pay', __('You need to pay %points% to access this page.', 'cp'));
	add_option('cp_module_pcontent_text_button', __('Pay %points%', 'cp'));
	add_option('cp_module_pcontent_text_logout', __('You must be logged in to access this page.', 'cp'));
	add_option('cp_module_pcontent_text_insufficient', __('You have insufficient points to purchase access for this page.', 'cp'));
}
add_action('cp_module_pcontent_activate','cp_module_pcontent_install');

if(cp_module_activated('pcontent')){

	/* Config for this module */
	function cp_module_pcontent_config(){
	?>
		<br />
		<h3><?php _e('Paid Content','cp'); ?></h3>
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_default_points"><?php _e('Default number of points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pcontent_default_points" name="cp_module_pcontent_default_points" value="<?php echo get_option('cp_module_pcontent_default_points'); ?>" size="30" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_default"><?php _e('Enabled by default', 'cp'); ?>:</label></th>
				<td valign="middle">
					<input type="radio" id="cp_module_pcontent_default_y" name="cp_module_pcontent_default" value="1" <?php echo (get_option('cp_module_pcontent_default')==true)?'checked="checked"':''; ?> /> <label for="cp_module_pcontent_default_y"><?php _e('Yes', 'cp'); ?></label>
					<input style="margin-left:15px;" type="radio" id="cp_module_pcontent_default_n" name="cp_module_pcontent_default" value="0" <?php echo (get_option('cp_module_pcontent_default')==false)?'checked="checked"':''; ?> /> <label for="cp_module_pcontent_default_n"><?php _e('No', 'cp'); ?></label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_payauthor"><?php _e('Credit post author with points paid by user', 'cp'); ?>:</label></th>
				<td valign="middle">
					<input type="radio" id="cp_module_pcontent_payauthor_y" name="cp_module_pcontent_payauthor" value="1" <?php echo (get_option('cp_module_pcontent_payauthor')==true)?'checked="checked"':''; ?> /> <label for="cp_module_pcontent_payauthor_y"><?php _e('Yes', 'cp'); ?></label>
					<input style="margin-left:15px;" type="radio" id="cp_module_pcontent_payauthor_n" name="cp_module_pcontent_payauthor" value="0" <?php echo (get_option('cp_module_pcontent_payauthor')==false)?'checked="checked"':''; ?> /> <label for="cp_module_pcontent_payauthor_n"><?php _e('No', 'cp'); ?></label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_text_pay"><?php _e('Text to ask users to pay for page access with points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pcontent_text_pay" name="cp_module_pcontent_text_pay" value="<?php echo get_option('cp_module_pcontent_text_pay'); ?>" size="30" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_text_button"><?php _e('Text to be shown on payment button', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pcontent_text_button" name="cp_module_pcontent_text_button" value="<?php echo get_option('cp_module_pcontent_text_button'); ?>" size="30" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_text_logout"><?php _e('Text to be shown to users who are not logged in', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pcontent_text_logout" name="cp_module_pcontent_text_logout" value="<?php echo get_option('cp_module_pcontent_text_logout'); ?>" size="30" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="cp_module_pcontent_text_insufficient"><?php _e('Text to be shown when user have insufficient points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_pcontent_text_insufficient" name="cp_module_pcontent_text_insufficient" value="<?php echo get_option('cp_module_pcontent_text_insufficient'); ?>" size="30" /></td>
			</tr>

		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_pcontent_config');

	/* Process and save options */
	function cp_module_pcontent_config_process(){
		$cp_module_pcontent_default_points = (int) $_POST['cp_module_pcontent_default_points'];
		$cp_module_pcontent_default = (bool) $_POST['cp_module_pcontent_default'];
		$cp_module_pcontent_payauthor = (bool) $_POST['cp_module_pcontent_payauthor'];
		$cp_module_pcontent_text_pay = $_POST['cp_module_pcontent_text_pay'];
		$cp_module_pcontent_text_button = $_POST['cp_module_pcontent_text_button'];
		$cp_module_pcontent_text_logout = $_POST['cp_module_pcontent_text_logout'];
		$cp_module_pcontent_text_insufficient = $_POST['cp_module_pcontent_text_insufficient'];
		update_option('cp_module_pcontent_default_points', (($cp_module_pcontent_default_points<=0)?1:$cp_module_pcontent_default_points));
		update_option('cp_module_pcontent_default', $cp_module_pcontent_default);
		update_option('cp_module_pcontent_payauthor', $cp_module_pcontent_payauthor);
		update_option('cp_module_pcontent_text_pay', $cp_module_pcontent_text_pay);
		update_option('cp_module_pcontent_text_button', $cp_module_pcontent_text_button);
		update_option('cp_module_pcontent_text_logout', $cp_module_pcontent_text_logout);
		update_option('cp_module_pcontent_text_insufficient', $cp_module_pcontent_text_insufficient);
	}
	add_action('cp_config_process','cp_module_pcontent_config_process');

	/* Define the custom box */
	add_action('admin_init', 'cp_module_pcontent_add_custom_box', 1);

	/* Do something with the data entered */
	add_action('save_post', 'cp_module_pcontent_save_postdata');

	/* Adds a box to the main column on the Post and Page edit screens */
	function cp_module_pcontent_add_custom_box() {
		add_meta_box( 'cp_module_pcontent_set', 'CubePoints - Paid Content', 'cp_module_pcontent_box', 'post', 'normal', 'high' );
		add_meta_box( 'cp_module_pcontent_set', 'CubePoints - Paid Content', 'cp_module_pcontent_box', 'page', 'normal', 'high' );
	}

	/* Prints the box content */
	function cp_module_pcontent_box() {

		global $post;

		// Use nonce for verification
		wp_nonce_field( plugin_basename(__FILE__), 'cp_module_pcontent_nonce' );
		if($post->post_status == 'auto-draft'){
			$enabled = (get_option('cp_module_pcontent_default')?'checked="yes"':'');
			$points = get_option('cp_module_pcontent_default_points');
		}
		else{
			$enabled = ((bool)(get_post_meta($post->ID , 'cp_pcontent_points_enable', 1))?'checked="yes"':'');
			$points = (int)get_post_meta($post->ID , 'cp_pcontent_points', 1);
		}
		// The actual fields for data entry
		echo '<br /><input type="checkbox" id="cp_module_pcontent_enable" name="cp_module_pcontent_enable" value="1" size="25" '.$enabled.' /> ';
		echo '<label for="cp_module_pcontent_enable">' . __("Enable paid content" , 'cp') . '</label> ';
		echo '<br /><br />';
		echo '<label for="cp_module_pcontent_points">' . __("Number of points to be deducted to view this page / post" , 'cp') . ':</label> ';
		echo '<input type="text" id= "cp_module_pcontent_points" name="cp_module_pcontent_points" value="'.$points.'" size="25" /><br /><br />';
	}

	/* When the post is saved, saves our custom data */
	function cp_module_pcontent_save_postdata( $post_id ) {

		// get post id from the revision id
		if($parent_id = wp_is_post_revision($post_id)){
			$post_id = $parent_id;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( !wp_verify_nonce( $_POST['cp_module_pcontent_nonce'], plugin_basename(__FILE__) )) {
			return $post_id;
		}

		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;

	  
		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// OK, we're authenticated: we need to find and save the data
		$points = (int)$_POST['cp_module_pcontent_points'];
		if($points<1){
			$points = 1;
		}
		update_post_meta($post_id, 'cp_pcontent_points_enable', (int)$_POST['cp_module_pcontent_enable']);
		update_post_meta($post_id, 'cp_pcontent_points', $points);

	}

	add_action('the_post','cp_module_pcontent_post');
	add_filter( "the_content", "cp_module_pcontent_post_content" );
	
	function cp_module_pcontent_post($p){
		$pcontent_enabled = (bool) get_post_meta($p->ID,'cp_pcontent_points_enable', 1);
		if(!$pcontent_enabled){
			return;
		}
		if(current_user_can( 'read_private_pages' )){
			return;
		}
		$uid = cp_currentUser();
		$pid = $p->ID;
		global $wpdb;
		if( (int) $wpdb->get_var("SELECT COUNT(*) FROM ".CP_DB." WHERE `uid`=$uid AND `data`=$pid AND `type`='pcontent'") != 0 ){
			return;
		}
		global $cp_module_pcontent_hide;
		$cp_module_pcontent_hide[] = $p->ID;
	}
	
	function cp_module_pcontent_post_content($content){
		global $post;
		global $cp_module_pcontent_hide;
		if(!in_array($post->ID,(array)$cp_module_pcontent_hide)){
			return $content;
		}
		$c = '<p>' . get_option('cp_module_pcontent_text_pay') . '</p>';
		$c .= apply_filters('cp_module_pcontent_post_content_'.$post->ID, '');
		$c .= '<form method="post">';
		$c .= '<input type="hidden" name="cp_module_pcontent_pay" value="'.$post->ID.'" />';
		$c .= '<p><input type="submit" value="'.get_option('cp_module_pcontent_text_button').'" /></p>';
		$c .= '</form>';
		if(!is_user_logged_in()){
			$c = get_option('cp_module_pcontent_text_logout');
		}
		$c = str_replace('%points%',cp_formatPoints(get_post_meta($post->ID,'cp_pcontent_points', 1)),$c);
		return $c;
	}
	
	add_action('init', 'cp_module_pcontent_buy');
	function cp_module_pcontent_buy(){
		if(!isset($_POST['cp_module_pcontent_pay'])) return;
		$pcontent_enabled = (bool) get_post_meta($_POST['cp_module_pcontent_pay'],'cp_pcontent_points_enable', 1);
		if(!$pcontent_enabled) return;
		$uid = cp_currentUser();
		global $wpdb;
		$pid = $_POST['cp_module_pcontent_pay'];
		if( (int) $wpdb->get_var("SELECT COUNT(*) FROM ".CP_DB." WHERE `uid`=$uid AND `data`=$pid AND `type`='pcontent'") != 0 ){
			return;
		}
		if(!is_user_logged_in()){
			add_filter('cp_module_pcontent_post_content_'.$_POST['cp_module_pcontent_pay'], create_function('$data', 'return "<p style=\"color:red;\">'.get_option('cp_module_pcontent_text_logout').'</p>";'));
			return;
		}
		if(cp_getPoints(cp_currentUser())<get_post_meta($_POST['cp_module_pcontent_pay'],'cp_pcontent_points', 1)){
			add_filter('cp_module_pcontent_post_content_'.$_POST['cp_module_pcontent_pay'], create_function('$data', 'return "<p style=\"color:red;\">'.get_option('cp_module_pcontent_text_insufficient').'</p>";'));
			return;
		}
		cp_points('pcontent',cp_currentUser(),-get_post_meta($_POST['cp_module_pcontent_pay'],'cp_pcontent_points', 1),$_POST['cp_module_pcontent_pay']);
		if(get_option('cp_module_pcontent_payauthor')){
			$post = get_post($_POST['cp_module_pcontent_pay']);
			cp_points('pcontent_author',$post->post_author,get_post_meta($_POST['cp_module_pcontent_pay'],'cp_pcontent_points', 1),serialize(array($_POST['cp_module_pcontent_pay'],cp_currentUser())));
		}
	}
	
	/** Paid Content Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_pcontent', 10, 4);
	function cp_admin_logs_desc_pcontent($type,$uid,$points,$data){
		if($type!='pcontent') { return; }
		$post = get_post($data);
		echo __('Purchased access to', 'cp') . ' "<a href="'.get_permalink( $post ).'">' . $post->post_title . '</a>"';
	}
	
	/** Paid Content Post-author Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_pcontent_author', 10, 4);
	function cp_admin_logs_desc_pcontent_author($type,$uid,$points,$data){
		if($type!='pcontent_author') { return; }
		$data = unserialize($data);
		$post = get_post($data[0]);
		$user = get_user_by('id', $data[1]);
		echo  __('User', 'cp') . ' "' . $user->user_login . '" ' . __('purchased access to', 'cp') . ' "<a href="'.get_permalink( $post ).'">' . $post->post_title . '</a>"';
	}

}
	
?>