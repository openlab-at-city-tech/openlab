<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Initial setup for ajax.
if(isset($_REQUEST['action']) and $_REQUEST['action']=='watupro_submit' ) $exam_id = $_REQUEST['quiz_id'];

$_question = new WTPQuestion();
$_exam = new WTPExam();
global $wpdb, $post, $user_ID;
$ui = get_option('watupro_ui'); 

watupro_load_fb_sdk();

// select exam
$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $exam_id));
if(empty($exam->is_active)) {
	printf(__('This %s is currently inactive.', 'watupro'), __('quiz', 'watupro'));
	return true;
}

// passed question IDs in the shortcode?
if(!empty($passed_question_ids)) $exam->passed_question_ids = $passed_question_ids;

$_question->exam = $exam; 
do_action('watupro_select_show_exam', $exam); // API Call

// override atts from shortcode?
watupro_override_atts($exam, @$attr);
$advanced_settings = unserialize( stripslashes($exam->advanced_settings)); 

if(!empty($_POST['watupro_submit_button_value'])) $advanced_settings['submit_button_value'] = $_POST['watupro_submit_button_value']; // allow dynamic override of the value
WTPQuestion :: $advanced_settings = $advanced_settings;
if(watupro_intel()) WatuPROIQuestion :: $advanced_settings = $advanced_settings;

// in progress taking of this exam?
$in_progress = null;
if(!empty($attr['time_limit']) and is_numeric($attr['time_limit'])) {
		$exam->time_limit = $attr['time_limit'];
}
// adjust time limit for scheudled quizzes
watupro_scheduled_adjust_time_limit($exam);
$exam->full_time_limit = $exam->time_limit; // store this for the verify_timer calculations
$log_timer = $timer_log = '';
$log_timer = (empty($advanced_settings['log_timer']) or $exam->time_limit <= 0) ? 0 : 1;
//print_r($_COOKIE);
if(is_user_logged_in()) {	
	$in_progress = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND exam_id=%d AND in_progress=1 ORDER BY ID DESC LIMIT 1", $user_ID, $exam_id));
	
	if($exam->time_limit > 0) {
		$meta_start_time = get_user_meta($user_ID, "start_exam_".$exam->ID, true);
		if($log_timer) $timer_log .= "The user meta start time is ".($meta_start_time ? date("Y-m-d H:i:s", $meta_start_time) : 'empty'). ' ';
	}
		
	if($exam->time_limit > 0 and (!empty($in_progress->ID) or !empty($meta_start_time))) {		
		// recalculate time limit
		$start_time = !empty($in_progress->ID) ? watupro_mktime($in_progress->start_time) : $meta_start_time;
		$timer_warning = WatuPROTimer :: calculate($start_time, $exam);
		
		if($log_timer) {
			$timer_log .= "The start time is ".(empty($in_progress->ID) ? ' taken from user meta' : 'taken from the timer initialization and stored as '.date("Y-m-d H:i:s", $start_time) .'. ');
		}
	}	
}
else {
	// user not logged in, but is the timer running?
	if($exam->full_time_limit > 0 and !empty($_COOKIE['start_time'.$exam->ID])) {		
		$timer_warning = WatuPROTimer :: calculate($_COOKIE['start_time'.$exam->ID], $exam);
	}
	
	// is there in_progress taking?
	if(!empty($_COOKIE['watupro_taking_id_' . $exam_id])) {
		$in_progress = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE ID=%d AND in_progress=1", intval($_COOKIE['watupro_taking_id_' . $exam_id])));		
	}
}

$_POST['timer_log'] = $timer_log;

if(!empty($advanced_settings['dont_load_inprogress'])) $in_progress = null;
 
if(!WTPUser::check_access($exam, $post)) return false;

// third party hook
$exam = apply_filters('watupro_ignore_schedule', $exam); 
// is scheduled?
if($exam->is_scheduled==1 or !empty($user_schedules)) {	 
    // global schedule
    $schedule_from = strtotime($exam->schedule_from);
    $schedule_to = strtotime($exam->schedule_to);
    if(!watupro_check_schedule($schedule_from, $schedule_to)) return false;
}
// indivudual schedules
if(!watupro_check_user_schedules($exam)) return false; 

// logged in or login not required here		
$_watu = new WatuPRO();    
 
// re-taking allowed?       
$ok = $_watu->can_retake($exam);
 
// check time limits on submit
if($ok and $exam->time_limit > 0 and !empty($_REQUEST['action']) and $_REQUEST['action']=='watupro_submit') {	
	$ok = $_watu->verify_time_limit($exam, @$in_progress);
	if(!$ok) {
		if($log_timer) $_POST['timer_log'] .= "Time expired. Submitting automatically at ".current_time('mysql').". ";
		// whether or not there is $in_progress we'll submit the quiz. But the $timeout_submit variable says that
		// in submit_exam.php we'll cleanup anything from $_POST that was not pre-saved as $in_progress
		$ok = true;
		$timeout_submit = true;			
	}
}
            
if(!$ok) return false;

if(!is_singular() and !empty($GLOBALS['watupro_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, don't show another.
	printf(__("Please go to <a href='%s'>%s</a> to view this %s", 'watupro'), get_permalink(), get_the_title(), WATUPRO_QUIZ_WORD);
	return false;
} 

// this one is not working well, we'll have to try another method, by searching in the post contents
if(is_singular() and !empty($GLOBALS['watupro_client_includes_loaded'])) { #If this is in the singular page - and a quiz is already shown, don't show another.
	//printf(__("There is already a %s shortcode on this page. You can use only one %s shortcode per post or page.", 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);
	//return false;
}
            
// now select and display questions			
$answer_display = $exam->show_answers==""?get_option('watupro_show_answers'):$exam->show_answers;	
//print_r($in_progress);
// loading serialized questions or questions coming by POST
if(!empty($_POST['action']) or !empty($in_progress->serialized_questions)) {	
	$serialized_questions = empty($_REQUEST['watupro_questions']) ? @$in_progress->serialized_questions : $_REQUEST['watupro_questions'];
	$all_question = watupro_unserialize_questions($serialized_questions);	
}

// this happens either at the beginning or if for some reason $all_question is empty on submitting			
if(empty($all_question)) {	

	$all_question = WTPQuestion :: select_all($exam);
	
	// regroup by cats?	
	if(empty($passed_question_ids)) $all_question = $_watu->group_by_cat($all_question, $exam);	
 		
	// now match answers to non-textarea questions
	$_watu->match_answers($all_question, $exam);
}    					
$cnt_questions	= count($all_question);	

// get required question ids as string
$rids=array(0);
foreach($all_question as $q)  {
	if($q->is_required) $rids[]=$q->ID;
}
$required_ids_str=implode(",",$rids);

// honeypot validation?
if(!empty($advanced_settings['use_honeypot']) and !empty($_POST['action']) and $_POST['action']=='watupro_submit') {
	if($_POST['h_app_id'] != '__' . md5('honeyforme' . $_SERVER['REMOTE_ADDR'])) die('WATUPRO_CAPTCHA:::'.__('No answer to the verification question.', 'watupro'));	
}

// don't check captchas when quiz is auto submitted due to expired timer
$dont_check_captchas = false;
if($exam->time_limit and !empty($_POST['auto_submitted'])) $dont_check_captchas = true;

// requires captcha?
if($exam->require_captcha and !$dont_check_captchas) {
	$recaptcha_public = get_option("watupro_recaptcha_public");
	$recaptcha_private = get_option("watupro_recaptcha_private");
	$recaptcha_version = get_option('watupro_recaptcha_version');
	$recaptcha_lang = get_option('watupro_recaptcha_lang');
	$recaptcha_style = $exam->single_page==1?"":"display:none;";
	
	if(!empty($recaptcha_version) and $recaptcha_version == 1) {
		if(!function_exists('recaptcha_get_html')) {
			 require(WATUPRO_PATH."/lib/recaptcha/recaptchalib.php");					 
		}
		$recaptcha_html = recaptcha_get_html($recaptcha_public, null, 1);
		$recaptcha_html = "<div id='WTPReCaptcha' style='$recaptcha_style' class'g-recaptcha'><p>".recaptcha_get_html($recaptcha_public)."</p></div>";
	}
	else {
		// recaptcha v 2
		wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?hl='.$recaptcha_lang);
		$recaptcha_html = '<div style="width:100%;clear:both;"><div id="WTPReCaptcha" style="'.$recaptcha_style.'" class="g-recaptcha" data-sitekey="'.$recaptcha_public.'"></div></div>';
	}		
	
	// check captcha
	if(!empty($_POST['action']) and $_POST['action']=='watupro_submit') {
		if(!empty($recaptcha_version) and $recaptcha_version == 1) {
			$resp = recaptcha_check_answer ($recaptcha_private,
	                          $_SERVER["REMOTE_ADDR"],
	                          $_POST["recaptcha_challenge_field"],
	                          $_POST["recaptcha_response_field"]);
	      if (!$resp->is_valid) die('WATUPRO_CAPTCHA:::'.__('Invalid image validation code', 'watupro'));			
	  }
	  else {
	  	  // recaptcha v 2, thanks to https://www.sitepoint.com/no-captcha-integration-wordpress/
	  	  $response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';
			$remote_ip = $_SERVER["REMOTE_ADDR"];     
			// make a GET request to the Google reCAPTCHA Server
			$request = wp_remote_get(
				'https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_private.'&response=' . $response . '&remoteip=' . $remote_ip
			);       
			$response_body = wp_remote_retrieve_body( $request );
			$result = json_decode( $response_body, true );					
			if(!$result['success']) {
				echo 'WATUPRO_CAPTCHA:::'.sprintf(__('The captcha verification is not correct. Please go back and try again. (%s)', 'watupro'), @$result['error-codes'][0]);
				exit;
			}
	  }
	}
} // end recaptcha code

// text captcha?
if(!empty($advanced_settings['require_text_captcha']) and !$dont_check_captchas) {
	$text_captcha_html = WatuPROTextCaptcha :: generate();
	$textcaptca_style = $exam->single_page==1?"":"style='display:none;'";
	$text_captcha_html = "<div id='WatuPROTextCaptcha' class='watupro-text-captcha' $textcaptca_style>".$text_captcha_html."</div>";
	
	// verify captcha
	if(!empty($_POST['action']) and $_POST['action']=='watupro_submit') {		
		if(!WatuPROTextCaptcha :: verify($_POST['watupro_text_captcha_question'], $_POST['watupro_text_captcha_answer'])) {
			if(!empty($_POST['in_ajax'])) die('WATUPRO_CAPTCHA:::'.__('Wrong answer to the verification question.', 'watupro'));
			else {
				_e('Wrong answer to the verification question.', 'watupro');
				return false;
			}
		}	
	} // end verifying captcha
}

// count question categories
$q_cids = array();
foreach($all_question as $q) {
   if(!in_array($q->cat_id, $q_cids)) $q_cids[] = $q->cat_id; 
}
$num_cats = count($q_cids);
$exam->num_cats =$num_cats;

$GLOBALS['watupro_client_includes_loaded'] = true;

// allowed file upload types
$allowed_uploads = get_option('watupro_upload_file_types');
$allowed_uploads = strtolower(str_replace(' ', '', trim($allowed_uploads)));

// fill in_progress in $_question to avoid bugs
$_question->fill_in_progress($in_progress);

$current_page = 1;
if(!empty($in_progress->ID)) {	
	$current_page = $wpdb->get_var($wpdb->prepare("SELECT current_page FROM ".	WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $in_progress->ID));
	if($current_page == 0) $current_page = 1;
}

//echo "CURRENT PAGE: $current_page";
		
if(empty($_REQUEST['action']) or $_REQUEST['action']!='watupro_submit') {	
	if(@file_exists(get_stylesheet_directory().'/watupro/show_exam.php')) $show_exam_view = get_stylesheet_directory().'/watupro/show_exam.php';
	else $show_exam_view = WATUPRO_PATH."/views/show_exam.php";	
	$show_exam_view = apply_filters( 'watupro_filter_view_show_exam', $show_exam_view, $exam);
	do_action('watupro_access_exam', $exam);	
	
	require($show_exam_view);
}
else require(WATUPRO_PATH.'/controllers/submit_exam.php'); 