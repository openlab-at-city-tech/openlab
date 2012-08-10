<?php 

/** Notify Module */

cp_module_register(__('Notify', 'cp') , 'notify' , '1.1', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('After activating this module, a growl-like pop up will appear to your users each time they earn points.', 'cp'), 1);

function cp_module_notify_install(){
	global $wpdb;
	if($wpdb->get_var("SHOW TABLES LIKE '".CP_DB."_notify'") != CP_DB.'_notify' || (int) get_option('cp_module_notify_db_version') < 1) {
		$sql = "CREATE TABLE " . CP_DB . "_notify (
			  id bigint(20) NOT NULL AUTO_INCREMENT,
			  uid bigint(20) NOT NULL,
			  notice text NOT NULL,
			  UNIQUE KEY id (id)
			);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("cp_module_notify_db_version'", "1.0");
	}
}
add_action('cp_module_notify_activate','cp_module_notify_install');

function cp_module_notify_uninstall(){
	global $wpdb;
	$wpdb->query('DROP TABLE `'.CP_DB.'_notify`');	
}
add_action('cp_module_notify_deactivate','cp_module_notify_uninstall');

if(cp_module_activated('notify')){
	/**********************
	 * Enqueuing Scripts
	 ***********************/
	wp_register_script(
		'jQuery_notice',
		WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'jquery.notice.js',
		array('jquery'),
		'1.0.1'
	);
	wp_register_style(
		'jQuery_notice',
		WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'jquery.notice.css'
	);
	
	function cp_notify_script(){ 
		wp_enqueue_script('jquery');
		wp_enqueue_script('jQuery_notice');
	}
	function cp_notify_style(){
		wp_enqueue_style('jQuery_notice');
	}
	
	add_action('init', 'cp_notify_script');
	add_action('init', 'cp_notify_style');
	/************************
	 * Enqueue End
	 ************************/	 

	add_action('wp_footer', 'cp_module_notify_display_hook', 10, 1);	
	function cp_module_notify_display($text = ''){
		if($text==''){ return; }

		echo '<script>';
			echo 'jQuery.noticeAdd({';			
				echo ' text: "';
				echo $text;
				echo '",';
				echo 'stay: false';
			echo '});';
		echo '</script>';
	}
	function cp_module_notify_hook(){
		$notices = apply_filters('cp_module_notify', array());
		foreach($notices as $notice){
			cp_module_notify_queue($notice);
		}
	}
	function cp_module_notify_queue($notice){
		global $wpdb;
		$wpdb->insert( CP_DB.'_notify', array( 'uid' => $notice[0], 'notice' => $notice[1] ) );
	}
	function cp_module_notify_display_hook(){
		$notices = apply_filters('cp_module_notify_display_hook', array());
		foreach($notices as $notice){
			cp_module_notify_display($notice);
		}
	}
	function cp_module_notify_displayNoticesFor($uid){
		if($uid==''){return;}
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM `'.CP_DB.'_notify` WHERE `uid`='.$uid.' ORDER BY id DESC');
		$wpdb->query('DELETE FROM `'.CP_DB.'_notify` WHERE `uid`='.$uid);
		foreach($results as $result){
			add_filter('cp_module_notify_display_hook',create_function('$query', '$query[]="'.htmlentities($result->notice).'"; return $query;'));
		}
	}
	add_action('init', 'cp_module_notify_hook',0,2);
	
	function cp_module_notify_msg_filter($d){
		list($m, $type, $uid, $points, $data) = $d;
		$user = get_userdata($uid);
		$m = str_replace('%npoints%',abs($points),$m);
		$m = str_replace('%points%',cp_formatPoints(abs($points)),$m);
		$m = str_replace('%type%',$type,$m);
		$m = str_replace('%username%',$user->user_login,$m);
		$m = str_replace('%user%',$user->display_name,$m);
		return array($m);
	}
	add_filter('cp_module_notify_msg','cp_module_notify_msg_filter',99999);
	
	function cp_module_notify_do(){
		cp_module_notify_displayNoticesFor(cp_currentUser());
	}
	add_action('wp_footer', 'cp_module_notify_do',1);
	
	/** hook into cp_points() */
	add_action('cp_log','cp_module_notify_logsHook',10,4);
	function cp_module_notify_logsHook($type, $uid, $points, $data){
		if($points>0){
			$m= '+%npoints%';
		} else {
			$m='-%npoints%';
		}
		$m = apply_filters('cp_module_notify_msg',array($m, $type, $uid, $points, $data));
		$message = $m[0];
		cp_module_notify_queue(array( $uid, $message ));
	}
	
}
?>