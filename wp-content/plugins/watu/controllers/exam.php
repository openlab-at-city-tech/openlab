<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function watu_exams() {
	global $wpdb;
	$quiz_id = empty($_REQUEST['quiz']) ? 0 : intval($_REQUEST['quiz']);
	
	if( isset($_REQUEST['message']) && $_REQUEST['message'] == 'updated') print '<div id="message" class="updated fade"><p>' . __('Test updated', 'watu') . '</p></div>';
	if(isset($_REQUEST['message']) && $_REQUEST['message'] == 'fail') print '<div id="message" class="updated error"><p>' . __('Error occured', 'watu') . '</p></div>';
	if( isset($_REQUEST['grade']) )  {
		$_REQUEST['grade'] = esc_html($_REQUEST['grade']);
		print '<div id="message" class="updated fade"><p>' . $_REQUEST['grade']. '</p></div>';
	}
	
	if(!empty($_GET['action']) and $_GET['action'] == 'delete' and check_admin_referer('watu_exams')) {
		$wpdb->get_results($wpdb->prepare("DELETE FROM ".WATU_EXAMS." WHERE ID=%d", $quiz_id));
		$wpdb->get_results($wpdb->prepare("DELETE FROM ".WATU_ANSWERS." WHERE question_id IN (SELECT ID FROM ".WATU_QUESTIONS." WHERE exam_id=%d)", $quiz_id));
		$wpdb->get_results($wpdb->prepare("DELETE FROM ".WATU_QUESTIONS." WHERE exam_id=%d", $quiz_id));
		print '<div id="message" class="updated fade"><p>' . __('Test deleted', 'watu') . '</p></div>';
	}
	
	$ob = empty($_GET['ob']) ? "Q.ID" : sanitize_text_field($_GET['ob']);
	$dir = empty($_GET['dir']) ? "DESC" : $_GET['dir'];
	if($dir != 'DESC' and $dir != 'ASC') $dir = 'ASC';
	$odir = ($dir == 'ASC') ? 'DESC' : 'ASC';
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	$page_limit = 10;	
	
	$filter_sql = $filter_params = '';
	
	if(!empty($_GET['title'])) {
		$get_title = sanitize_text_field($_GET['title']);
		$filter_sql .= " AND Q.name LIKE '%$get_title%' ";
		$filter_params .= "&title=$get_title";
	}
	
	if(!empty($_GET['exam_id'])) {
		$filter_sql .= $wpdb->prepare(" AND Q.ID = %d ", intval($_GET['exam_id']));
		$filter_params .= "&exam_id=".intval($_GET['exam_id']);
	}
	
	// Retrieve the quizzes
	$exams = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS Q.ID,Q.name,Q.added_on,
			(SELECT COUNT(ID) FROM ".WATU_QUESTIONS." WHERE exam_id=Q.ID) AS question_count,
			(SELECT COUNT(ID) FROM ".WATU_TAKINGS." WHERE exam_id=Q.ID) AS taken
			FROM `".WATU_EXAMS."` AS Q
			WHERE ID > 0 $filter_sql
			ORDER BY $ob $dir LIMIT $offset, $page_limit");
			
	 $count = $wpdb->get_var("SELECT FOUND_ROWS()");			
		
		// now select all posts that have watu shortcode in them
		$posts=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts 
		WHERE post_content LIKE '%[WATU %]%' 
		AND post_status='publish' AND post_title!=''
		ORDER BY post_date DESC");	
		
		// match posts to exams
		foreach($exams as $cnt=>$exam) {
			foreach($posts as $post) {
				if(strstr($post->post_content,"[WATU ".$exam->ID."]")) {
					$exams[$cnt]->post=$post;			
					break;
				}
			}
		}
	if(@file_exists(get_stylesheet_directory().'/watu/exams.html.php')) include get_stylesheet_directory().'/watu/exams.html.php';
	else include(WATU_PATH . '/views/exams.html.php');
} 

function watu_exam() {
	global $wpdb, $user_ID;
	$answer_display = get_option('watu_show_answers');
	
	if(isset($_POST['submit']) and check_admin_referer('watu_create_edit_quiz')) {
		// use email output?
		$_POST['email_output'] = empty($_POST['different_email_output']) ? '' : sanitize_text_field($_POST['email_output']);	
		
		$name = sanitize_text_field($_POST['name']);	
		$randomize = empty($_POST['randomize']) ? 0 : 1;
		$single_page = empty($_POST['single_page']) ? 0 : 1;
		$show_answers = empty($_POST['show_answers']) ? 0 : intval($_POST['show_answers']);
		$show_answers = empty($_POST['show_answers']) ? 0 : intval($_POST['show_answers']);
		$require_login = empty($_POST['require_login']) ? 0 : 1;
		$notify_admin = empty($_POST['notify_admin']) ? 0 : 1;
		$randomize_answers = empty($_POST['randomize_answers']) ? 0 : 1;
		$pull_random = intval($_POST['pull_random']);
		$dont_store_data = empty($_POST['dont_store_data']) ? 0 : 1;
		$show_prev_button = empty($_POST['show_prev_button']) ? 0 : 1;
		$dont_display_question_numbers = empty($_POST['dont_display_question_numbers']) ? 0 : 1;
		$require_text_captcha = empty($_POST['require_text_captcha']) ? 0 : 1;
		$notify_user = empty($_POST['notify_user']) ? 0 : 1;
		$notify_email = sanitize_text_field($_POST['notify_email']);
		$email_subject = sanitize_text_field($_POST['email_subject']);
		$take_again = empty($_POST['take_again']) ? 0 : 1;
		$times_to_take = intval($_POST['times_to_take']);
		$quiz_id = intval($_POST['quiz']);
		$description = watu_strip_tags($_POST['description']);
		$content = watu_strip_tags($_POST['content']);
		$no_alert_unanswered = empty($_POST['no_alert_unanswered']) ? 0 : 1;
		$use_honeypot = empty($_POST['use_honeypot']) ? 0 : 1;
		$save_source_url = empty($_POST['save_source_url']) ? 0 : 1;
		$advanced_settings = array();
		$advanced_settings['submit_button_value'] = empty($_POST['submit_button_value']) ? __('Submit', 'watu') : sanitize_text_field($_POST['submit_button_value']);
		$advanced_settings['transfer_moola'] = empty($_POST['transfer_moola']) ? 0 : 1;
		$advanced_settings['transfer_moola_mode'] = empty($_POST['transfer_moola_mode']) ? '' : sanitize_text_field($_POST['transfer_moola_mode']);
		$advanced_settings['design_theme'] = sanitize_text_field($_POST['design_theme']);
		$advanced_settings = serialize($advanced_settings);
				
		if($_REQUEST['action'] == 'edit') { //Update goes here
			$exam_id = $_REQUEST['quiz'];
			$wpdb->query($wpdb->prepare("UPDATE ".WATU_EXAMS."
				SET name=%s, description=%s,final_screen=%s, randomize=%d, single_page=%d, 
				show_answers=%d, require_login=%d, notify_admin=%d, randomize_answers=%d,
				pull_random=%d, dont_store_data=%d, show_prev_button=%d, 
				dont_display_question_numbers=%d, require_text_captcha=%d, email_output=%s,
				notify_user=%d, notify_email=%s, take_again=%d, times_to_take=%d, no_alert_unanswered=%d,
				use_honeypot=%d, save_source_url=%d, advanced_settings = %s, email_subject=%s    
				WHERE ID=%d", $name, $description, $content, 
				$randomize, $single_page, $show_answers, 
				$require_login, $notify_admin, $randomize_answers,
				$pull_random, $dont_store_data, $show_prev_button, 
				$dont_display_question_numbers, $require_text_captcha, 
				watu_strip_tags($_POST['email_output']), $notify_user, $notify_email, 
				$take_again, $times_to_take, $no_alert_unanswered, $use_honeypot, $save_source_url,  
				$advanced_settings, $email_subject, $quiz_id));
			
			if(!empty($_POST['auto_publish'])) watu_auto_publish($exam_id);
			$wp_redirect = 'admin.php?page=watu_exams&message=updated';
		
		} else {
			$no_ajax = 0;
			if(get_option('watu_no_ajax') == 1) $no_ajax = 1;						
			
			$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_EXAMS." 
				(name, description, final_screen,  added_on, randomize, single_page, show_answers, require_login, 
				notify_admin, randomize_answers, pull_random, dont_store_data, show_prev_button, 
				dont_display_question_numbers, require_text_captcha, email_output, notify_user, 
				notify_email, take_again, times_to_take, no_alert_unanswered, use_honeypot, save_source_url, 
				advanced_settings, email_subject, no_ajax) 
				VALUES(%s, %s, %s, NOW(), %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %d, %s, %d, %d, %d, %d, %d, %s, %s, %d)", 
				$name, $description, $content, $randomize, $single_page, 
				$show_answers, $require_login, $notify_admin, 
				$randomize_answers, $pull_random, $dont_store_data, 
				$show_prev_button, $dont_display_question_numbers, 
				$require_text_captcha, watu_strip_tags($_POST['email_output']), $notify_user, 
				$notify_email, $take_again, $times_to_take, $no_alert_unanswered, $use_honeypot, $save_source_url, 
				$advanced_settings, $email_subject, $no_ajax));
			$exam_id = $wpdb->insert_id;
			if(!empty($_POST['auto_publish'])) watu_auto_publish($exam_id);
			if($exam_id == 0 ) $wp_redirect = 'admin.php?page=watu_exams&message=fail';
			$wp_redirect = 'admin.php?page=watu_questions&message=new_quiz&quiz='.$exam_id;
		}
				
		$wp_redirect = admin_url($wp_redirect);
		
		do_action('watu_exam_saved', $exam_id);
		
		echo "<meta http-equiv='refresh' content='0;url=$wp_redirect' />"; 
		exit;
	}

		
	$action = 'new';
	if(!empty($_REQUEST['action']) and $_REQUEST['action'] == 'edit') $action = 'edit';
	
	$dquiz = array();
	$grades = array();
	if($action == 'edit') {
		$dquiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", intval($_REQUEST['quiz'])));
		$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_GRADES." WHERE  exam_id=%d order by ID ", intval($_REQUEST['quiz'])) );
		$final_screen = stripslashes($dquiz->final_screen);
	} else {
		$final_screen = __("<p>Congratulations - you have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%POINTS%% points out of %%MAX-POINTS%% points total.</p>\n\n<p>Your obtained grade is <b>%%GRADE-TITLE%%</b></p><p>%%GRADE-DESCRIPTION%%</p>\n\n<p>Your answers are shown below:<p>%%ANSWERS%%", 'watu');
	}
	
	// see what is the show_answers to this exam
	if(!isset($dquiz->show_answers) or $dquiz->show_answers == 100) $answer_display = $answer_display; // assign the default
	else $answer_display = $dquiz->show_answers;
	
	if(!empty($_GET['quiz'])) {
		$quiz_id = intval($_GET['quiz']);
		$is_published = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%[watu ".$quiz_id."]%' 
				AND post_status='publish' AND post_title!=''");
		$advanced_settings = unserialize(stripslashes($dquiz->advanced_settings));  		
	} 
	else $is_published = false;
	
	if(@file_exists(get_stylesheet_directory().'/watu/exam_form.php')) include get_stylesheet_directory().'/watu/exam_form.php';
	else include(WATU_PATH . '/views/exam_form.php');
}

// auto publish quiz in post
// some data comes directly from the $_POST to save unnecessary DB query
function watu_auto_publish($quiz_id) {	
	$post = array('post_content' => '[WATU '.$quiz_id.']', 'post_name'=> $_POST['name'], 
		'post_title'=>$_POST['name'], 'post_status'=>'publish');
	wp_insert_post($post);
}