<?php
add_action( 'wp_ajax_update_sharing_settings', 'update_sharing_settings' );

function update_sharing_settings() {
	if ( !wp_verify_nonce( $_POST['nonce'], "update_sharing_settings")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }
	
	$option5  = unserialize(get_option('sfsi_section5_options',false));
	$option5['sfsi_custom_social_hide'] = $_POST['sfsi_custom_social_hide'];
	update_option('sfsi_section5_options',serialize($option5));
	echo true;
	wp_die(); // this is required to terminate immediately and return a proper response
}
?>