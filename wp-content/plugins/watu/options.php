<?php
global $wpdb,  $wp_roles;
$is_admin = current_user_can('administrator');

if(!empty($_REQUEST['submit']) and check_admin_referer('watu_options')) {
	$delete_db = empty($_POST['delete_db']) ? 0 : 1;
	$delete_db_confirm = (empty($_POST['delete_db_confirm']) or $_POST['delete_db_confirm']!= 'yes') ? '' : 'yes';
	$answer_type = ($_POST['answer_type'] == 'radio') ? 'radio' : 'checkbox';
	$use_the_content = empty($_POST['use_the_content']) ? 0 : 1;
	$debug_mode = empty($_POST['debug_mode']) ? 0 : 1;
	$integrate_moolamojo = empty($_POST['integrate_moolamojo']) ? 0 : 1;
	$dont_autoscroll = empty($_POST['dont_autoscroll']) ? 0 : 1;
	$no_ajax = empty($_POST['no_ajax']) ? 0 : 1;
	
	// sanitize email in format test@dot.com or Your Name <test@dot.com>
	$admin_email = $_POST['watu_admin_email'];  
	if(strstr($admin_email, '<')) {
	   $parts = explode('<', $admin_email);
	   $parts[0] = sanitize_text_field($parts[0]);
	   $parts[1] = sanitize_email($parts[1]);
	   $admin_email = $parts[0].' <'.$parts[1].'>';
	}
	else $admin_email = sanitize_email($admin_email);
	
	$csv_delim = sanitize_text_field($_POST['csv_delim']);
	$csv_quotes = empty($_POST['csv_quotes']) ? 0 : 1;
	
	update_option( "watu_delete_db", $delete_db );
	update_option('watu_delete_db_confirm', $delete_db_confirm);
	update_option('watu_answer_type', $answer_type);
	update_option('watu_use_the_content', $use_the_content);
	update_option('watu_text_captcha', watu_strip_tags($_POST['text_captcha']));
	update_option('watu_debug_mode', $debug_mode);
	update_option('watu_admin_email', $admin_email);
	update_option('watu_integrate_moolamojo', $integrate_moolamojo);
	update_option('watu_quiz_word', sanitize_text_field($_POST['quiz_word']));
   update_option('watu_quiz_word_plural', sanitize_text_field($_POST['quiz_word_plural']));  
   update_option('watu_csv_delim', $csv_delim);
   update_option('watu_csv_quotes', $csv_quotes);
   update_option('watu_dont_autoscroll', $dont_autoscroll);
   update_option('watu_no_ajax', $no_ajax);		
	
	$roles = $wp_roles->roles;			
			
	foreach($roles as $key=>$r) {
		if($key == 'administrator') continue;
		
		$role = get_role($key);

		// manage Watu - allow only admin change this
		if($is_admin) {
			if(!empty($_POST['manage_roles']) and is_array($_POST['manage_roles']) and in_array($key, $_POST['manage_roles'])) {					
 				if(!$role->has_cap('watu_manage')) $role->add_cap('watu_manage');
			}
			else $role->remove_cap('watu_manage');
		}	// end if can_manage_options
	} // end foreach role 
		
	print '<div id="message" class="updated fade"><p>' . __('Options updated', 'watu') . '</p></div>';	
}

// save no_ajax
if(!empty($_POST['save_ajax_settings']) and check_admin_referer('watu_ajax_options')) {
	$ids = empty($_POST['no_ajax']) ? array(0) : watu_int_array($_POST['no_ajax']);
	// make sure IDs contains only exam IDs
	$id_sql = implode(',', $ids);
	if(!preg_match("/^[0-9,]+$/", $id_sql)) $id_sql = "0";
	
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=1 WHERE ID IN (".$id_sql.")");
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=0 WHERE ID NOT IN (".$id_sql.")");
}

$answer_display = get_option('watu_show_answers');
$delete_db = get_option('watu_delete_db');

$text_captcha = get_option('watu_text_captcha');
// load 3 default questions in case nothing is loaded
if(empty($text_captcha)) {
	$text_captcha = __('What is the color of the snow? = white', 'watu').PHP_EOL.__('Is fire hot or cold? = hot', 'watu') 
		.PHP_EOL. __('In which continent is Norway? = Europe', 'watu'); 
}

// select all quizzes for No Ajax option
$quizzes = $wpdb->get_results("SELECT ID, name, no_ajax FROM ".WATU_EXAMS." ORDER BY name");

$roles = $wp_roles->roles;				

$delim = get_option('watu_csv_delim');

if(@file_exists(get_stylesheet_directory().'/watu/options.html.php')) include get_stylesheet_directory().'/watu/options.html.php';
else include(WATU_PATH . '/views/options.html.php');