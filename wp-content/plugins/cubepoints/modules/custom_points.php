<?php

/** Custom Points Module */

cp_module_register(__('Custom Points', 'cp') , 'customp' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('This module gives you the ability to set custom points earned for comments for each post.', 'cp'), 1);

if(cp_module_activated('customp')){

	/* Define the custom box */
	add_action('admin_init', 'cp_module_customp_add_custom_box', 1);

	/* Do something with the data entered */
	add_action('save_post', 'cp_module_customp_save_postdata');

	/* Adds a box to the main column on the Post and Page edit screens */
	function cp_module_customp_add_custom_box() {
		add_meta_box( 'cp_module_customp_set', 'CubePoints - Custom Points', 'cp_module_customp_box', 'post', 'normal', 'high' );
		add_meta_box( 'cp_module_customp_set', 'CubePoints - Custom Points', 'cp_module_customp_box', 'page', 'normal', 'high' );
	}

	/* Prints the box content */
	function cp_module_customp_box() {

		global $post;

		// Use nonce for verification
		wp_nonce_field( plugin_basename(__FILE__), 'cp_module_customp_nonce' );

		// The actual fields for data entry
		echo '<br /><input type="checkbox" id="cp_module_customp_enable" name="cp_module_customp_enable" value="1" size="25" '.((bool)(get_post_meta($post->ID , 'cp_points_enable', 1))?'checked="yes"':'').' /> ';
		echo '<label for="cp_module_customp_enable">' . __("Set custom points for comments on this page" , 'cp') . '</label> ';
		echo '<br /><br />';
		echo '<label for="cp_module_customp_points">' . __("Number of points per comment" , 'cp') . ':</label> ';
		echo '<input type="text" id= "cp_module_customp_points" name="cp_module_customp_points" value="'.(int)get_post_meta($post->ID , 'cp_points', 1).'" size="25" /><br /><br />';
	}

	/* When the post is saved, saves our custom data */
	function cp_module_customp_save_postdata( $post_id ) {

		// get post id from the revision id
		if($parent_id = wp_is_post_revision($post_id)){
			$post_id = $parent_id;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( !wp_verify_nonce( $_POST['cp_module_customp_nonce'], plugin_basename(__FILE__) )) {
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
		update_post_meta($post_id, 'cp_points_enable', (int)$_POST['cp_module_customp_enable']);
		update_post_meta($post_id, 'cp_points', (int)$_POST['cp_module_customp_points']);

	}

	/* Add CubePoints comment action hook */
	add_action('cp_comment_add', 'cp_module_customp_newComment');
		function cp_module_customp_newComment($cid) {
			if (is_user_logged_in()) {
				$cdata = get_comment($cid);
				$pid = $cdata->comment_post_ID;
				$customp_enabled = (bool) get_post_meta($pid,'cp_points_enable', 1);
				$customp_points  = (int) get_post_meta($pid,'cp_points', 1);
				if( $customp_enabled ){
					add_filter('cp_comment_points',create_function('$points', 'return '.$customp_points.';'),1);
				}
			}
		}

}
	
?>