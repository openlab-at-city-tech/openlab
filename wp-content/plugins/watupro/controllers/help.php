<?php
// user manual - added in version 3.1, to be developed
function watupro_help() {	
   global $wpdb;
	
	$tab = empty($_GET['tab']) ? "help" : $_GET['tab'];
	
	switch($tab) {
		case 'email_log':
			$date = empty($_POST['date']) ? date('Y-m-d') : sanitize_text_field($_POST['date']);
			if(!empty($_POST['cleanup'])) update_option('watupro_cleanup_raw_log', intval($_POST['cleanup_days']));
			$receiver_sql = '';
			if(!empty($_POST['receiver'])) $receiver_sql = $wpdb->prepare(" AND receiver=%s ", sanitize_text_field($_POST['receiver']));
			
			$emails = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_EMAILLOG."  WHERE date=%s $receiver_sql ORDER BY id", $date));
			
			$cleanup_raw_log = get_option('watupro_cleanup_raw_log');
			if(empty($cleanup_raw_log)) $cleanup_raw_log = 7;
			
			watupro_enqueue_datepicker();
			require(WATUPRO_PATH."/views/email-log.html.php"); 
		break;
		case 'help':
		default:
			if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_master"."'")) == strtolower($wpdb->prefix. "watu_master")) {	
				$watu_exams=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix. "watu_master ORDER BY ID");
			}	   
			   
			if(@file_exists(get_stylesheet_directory().'/watupro/help.php')) require get_stylesheet_directory().'/watupro/help.php';
			else require WATUPRO_PATH."/views/help.php";
		break;
	} // end switch	
}