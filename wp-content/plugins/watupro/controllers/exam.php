<?php
// add/edit exam
function watupro_exam() {
	global $wpdb, $user_ID, $wp_roles;
	$roles = $wp_roles->roles;	
	$use_wp_roles = get_option('watupro_use_wp_roles');
	
	// select user groups
	if($use_wp_roles != 1) {
		$groups = $wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	}
		
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this");
	
	// set default points for answers?
	$set_default_points = get_option('watupro_set_default_points');
	
	if(isset($_REQUEST['submit'])) {
		// prepare advanced settings - email grades and contact info fields
		$advanced_settings = $wpdb->get_var($wpdb->prepare("SELECT advanced_settings FROM ".WATUPRO_EXAMS."
			WHERE id=%d",  @$_REQUEST['quiz']));
		if(!empty($advanced_settings)) $advanced_settings = unserialize( stripslashes($advanced_settings));
		else $advanced_settings = array();
		$old_advanced_settings = $advanced_settings;			
		
		// email grades
		$advanced_settings['email_grades'] = watupro_int_array(@$_POST['email_grades']);
		$advanced_settings['admin_email_grades'] = watupro_int_array(@$_POST['admin_email_grades']);
		
		// flag for review
		$advanced_settings['flag_for_review'] = @$_POST['flag_for_review'];
		
		// dont display question numbers
		$advanced_settings['dont_display_question_numbers'] = @$_POST['dont_display_question_numbers'];
		
		// contact fields	
		$advanced_settings['contact_fields'] = array();
		$advanced_settings['contact_fields']['intro_text'] = rawurlencode($_POST['ask_contact_intro']);
		$advanced_settings['contact_fields']['email'] = sanitize_text_field($_POST['ask_for_email']);
		$advanced_settings['contact_fields']['email_label'] = rawurlencode($_POST['ask_for_email_label']);		
		$advanced_settings['contact_fields']['name'] = sanitize_text_field($_POST['ask_for_name']);
		$advanced_settings['contact_fields']['name_label'] = rawurlencode($_POST['ask_for_name_label']);
		$advanced_settings['contact_fields']['phone'] = sanitize_text_field($_POST['ask_for_phone']);
		$advanced_settings['contact_fields']['phone_label'] = rawurlencode($_POST['ask_for_phone_label']);
		$advanced_settings['contact_fields']['company'] = sanitize_text_field($_POST['ask_for_company']);
		$advanced_settings['contact_fields']['company_label'] = rawurlencode($_POST['ask_for_company_label']);
		$advanced_settings['contact_fields']['field1'] = sanitize_text_field($_POST['ask_for_field1']);
		$advanced_settings['contact_fields']['field1_label'] = rawurlencode($_POST['ask_for_field1_label']);
		$advanced_settings['contact_fields']['field1_is_dropdown'] = empty($_POST['field1_is_dropdown']) ? 0 : 1;
		$advanced_settings['contact_fields']['field1_dropdown_values'] = rawurlencode(watupro_strip_tags($_POST['field1_dropdown_values']));
		$advanced_settings['contact_fields']['field2'] = sanitize_text_field($_POST['ask_for_field2']);
		$advanced_settings['contact_fields']['field2_label'] = rawurlencode($_POST['ask_for_field2_label']);
		$advanced_settings['contact_fields']['field2_is_dropdown'] = empty($_POST['field2_is_dropdown']) ? 0 : 1;
		$advanced_settings['contact_fields']['field2_dropdown_values'] = rawurlencode(watupro_strip_tags($_POST['field2_dropdown_values']));
		$advanced_settings['contact_fields']['checkbox'] = rawurlencode($_POST['ask_for_checkbox']);
		$advanced_settings['contact_fields']['start_button'] = sanitize_text_field($_POST['ask_for_start_button']);
		$advanced_settings['contact_fields']['labels_encoded_url'] = 1;
		$advanced_settings['enumerate_choices'] = $_POST['enumerate_choices'];
		$advanced_settings['show_progress_bar'] = empty($_POST['show_progress_bar']) ? 0 : 1;
		$advanced_settings['progress_bar_percent'] = empty($_POST['progress_bar_percent']) ? 0 : 1;
		$advanced_settings['show_category_paginator'] = @$_POST['show_category_paginator'];
		$advanced_settings['retakings_per_period'] = @$_POST['retakings_per_period'];
		
		$advanced_settings['ask_for_contact_details'] = sanitize_text_field($_POST['ask_for_contact_details']);
		
		// question based captcha & honeypot
		$advanced_settings['require_text_captcha'] = empty($_POST['require_text_captcha']) ? 0 : 1;
		$advanced_settings['use_honeypot'] = empty($_POST['use_honeypot']) ? 0 : 1;
		
		// question difficulty level
		$advanced_settings['difficulty_level'] = sanitize_text_field(@$_POST['difficulty_level']);
		
		// accept rating
		$advanced_settings['accept_rating'] = empty($_POST['accept_rating']) ? 0 : 1;
		$advanced_settings['accept_rating_per_question'] = empty($_POST['accept_rating_per_question']) ? 0 : 1;
		
		// display result when no re-takings are allowed
		$advanced_settings['no_retake_display_result'] = empty($_POST['no_retake_display_result']) ? 0 : 1;
		$advanced_settings['no_retake_display_result_what'] = sanitize_text_field(@$_POST['no_retake_display_result_what']);
		
		// don't store takings in DB
		$advanced_settings['dont_store_taking'] = empty($_POST['dont_store_taking']) ? 0 : 1;
		$advanced_settings['store_taking_only_logged'] = empty($_POST['store_taking_only_logged']) ? 0 : 1;
		
		// store full category grades info
		$advanced_settings['store_full_catgrades'] = (empty($_POST['store_full_catgrades']) or !empty($_POST['dont_store_taking'])) ? 0 : 1;
		
		// on "Fill the gaps" questions user may accidentally fill more than one space inside the answer. Replace these spaces with a single space
		$advanced_settings['gaps_replace_spaces'] = empty($_POST['gaps_replace_spaces']) ? 0 : 1;
		
		// always show quiz description, even to non logged users
		$advanced_settings['always_show_description'] = empty($_POST['always_show_description']) ? 0 : 1;
		
		// when selecting "grades by %" what mode - correct answers or % of maximum points
		$advanced_settings['grades_by_percent_mode'] = @$_POST['grades_by_percent_mode'];
		
		// don't show previously answered questions to the user (for questions that pull from a pool)
		$advanced_settings['dont_show_answered'] = intval(@$_POST['dont_show_answered']);
		// or only these that were correctly answered
		$advanced_settings['dont_show_correctly_answered'] = intval(@$_POST['dont_show_correctly_answered']);
		$advanced_settings['dont_show_answered_restart'] = empty($_POST['dont_show_answered_restart']) ? 0 : 1;
		
		// admin comments
		$advanced_settings['admin_comments'] = rawurlencode($_POST['admin_comments']);
		
		// store source URL along with taking
		$advanced_settings['save_source_url'] = empty($_POST['save_source_url']) ? 0 : 1;
		
		// moolamojo integration
		$advanced_settings['transfer_moola'] = empty($_POST['transfer_moola']) ? 0  : 1;
		$advanced_settings['transfer_moola_mode'] = sanitize_text_field(@$_POST['transfer_moola_mode']);
		
		// show / hide X of Y text
		$advanced_settings['show_xofy'] = empty($_POST['show_xofy']) ? 0 : 1;
		
		// text on submit button
		$advanced_settings['submit_button_value'] = empty($_POST['submit_button_value']) ? _wtpt(__('Submit', 'watupro')) : sanitize_text_field($_POST['submit_button_value']);
		
		// timer turns red
		$advanced_settings['timer_turns_red'] = intval($_POST['timer_turns_red']);
		
		// allowed users to access
		$advanced_settings['restrict_by_user'] = empty($_POST['restrict_by_user']) ? 0 : 1;
		$advanced_settings['allowed_users'] = watupro_strip_tags($_POST['allowed_users']);
		
		// re-attempting period
		$advanced_settings['retake_days_limit'] = intval($_POST['retake_days_limit']);
		
		// RTL tests
		$advanced_settings['is_rtl'] = empty($_POST['is_rtl']) ? 0 : 1;
		
		// design theme
		$advanced_settings['design_theme'] = sanitize_text_field($_POST['design_theme']);
		
		// toggle answer explanations
		$advanced_settings['toggle_answer_explanations'] = empty($_POST['toggle_answer_explanations']) ? 0 : 1;
		$advanced_settings['toggle_answer_explanations_button'] = rawurlencode(sanitize_text_field(@$_POST['toggle_answer_explanations_button']));
		
		// don't require user to select answer when "live result" mode is allowed
		$advanced_settings['live_result_no_answer'] = empty($_POST['live_result_no_answer']) ? 0 : 1;
		
		// likert survey display configurations
		$advanced_settings['likert_cell_width_type'] = sanitize_text_field(@$_POST['likert_cell_width_type']);
		$advanced_settings['likert_cell_width'] = intval(@$_POST['likert_cell_width']);
		$advanced_settings['likert_header_align'] = sanitize_text_field(@$_POST['likert_header_align']);
		$advanced_settings['likert_question_align'] = sanitize_text_field(@$_POST['likert_question_align']);
		$advanced_settings['likert_choice_align'] = sanitize_text_field(@$_POST['likert_choice_align']);
		$advanced_settings['likert_table_border'] = sanitize_text_field(@$_POST['likert_table_border']);
		$advanced_settings['likert_border_custom_css'] = sanitize_text_field(@$_POST['likert_border_custom_css']);
		
		// for personality quizzes to force only single personality to be assigned
		$advanced_settings['single_personality_result'] = empty($_POST['single_personality_result']) ? 0 : 1;
		
		// delay results per group
		$advanced_settings['delay_results_per_group'] = empty($_POST['delay_results_per_group']) ? 0 : 1;
		$advanced_settings['delay_results_groups'] = empty($_POST['delay_results_groups']) ? '' : esc_sql($_POST['delay_results_groups']);
		
		// log timer on timed tests
		$advanced_settings['log_timer'] = empty($_POST['log_timer']) ? 0 : 1;
		
		// category header on every page?
		$advanced_settings['cat_header_every_page'] = empty($_POST['cat_header_every_page']) ? 0 : 1;
		
		// time limit on retake grades
		$advanced_settings['retake_grades_expire'] = intval(@$_POST['retake_grades_expire']);
		
		// number of total attempts
		$advanced_settings['total_attempts_limit'] = intval(@$_POST['total_attempts_limit']);
		$advanced_settings['total_attempts_limit_message'] = rawurlencode(sanitize_text_field(@$_POST['total_attempts_limit_message']));
		
		// pull X random categories
		$advanced_settings['random_cats'] = intval($_POST['random_cats']);
		
		// use respondend's email address as a reply-to
		$advanced_settings['taker_reply_to'] = empty($_POST['taker_reply_to']) ? 0 : 1;
		
		// pdf download
		$pdf_settings = array();
		if(!empty($_POST['pdf_bridge_paper_size'])) $pdf_settings['paper_size'] = sanitize_text_field($_POST['pdf_bridge_paper_size']);
		if(!empty($_POST['pdf_bridge_orientation'])) $pdf_settings['orientation'] = sanitize_text_field($_POST['pdf_bridge_orientation']);
		if(!empty($_POST['pdf_bridge_force_download'])) $pdf_settings['force_download'] = sanitize_text_field($_POST['pdf_bridge_force_download']);
		if(!empty($_POST['pdf_bridge_file_name'])) $pdf_settings['file_name'] = sanitize_text_field($_POST['pdf_bridge_file_name']);
		$pdf_settings['pdf_header'] = empty($_POST['pdf_header']) ? '' : wp_kses_post($_POST['pdf_header']);
		$pdf_settings['pdf_footer'] = empty($_POST['pdf_footer']) ? '' : wp_kses_post($_POST['pdf_footer']);
		$advanced_settings['pdf_settings'] = $pdf_settings;
		$advanced_settings['print_pdf'] = empty($_POST['print_pdf']) ? 0 : 1;
		
		// category headings when questions are grouped
		if(empty($_POST['question_category_heading_tag']) 
			or !in_array($_POST['question_category_heading_tag'], array('h1', 'h2', 'h3', 'h4'))) {
			$_POST['question_category_heading_tag'] = 'h2';
		}  
		$advanced_settings['question_category_heading_tag'] = $_POST['question_category_heading_tag'];
		
		if(empty($_POST['question_subcategory_heading_tag']) 
			or !in_array($_POST['question_subcategory_heading_tag'], array('h1', 'h2', 'h3', 'h4'))) {
			$_POST['question_subcategory_heading_tag'] = 'h3';
		}  
		$advanced_settings['question_subcategory_heading_tag'] = $_POST['question_subcategory_heading_tag'];
		
		// paginator for subcategories
		$advanced_settings['exclude_subcat_paginator'] = empty($_POST['exclude_subcat_paginator']) ? 0 : 1;
		
		// per-quiz default points
		if($set_default_points) {
			$advanced_settings['default_correct_answer_points'] = $_POST['default_correct_answer_points'];
			$advanced_settings['default_incorrect_answer_points'] = $_POST['default_incorrect_answer_points'];
		}
		
		// completion criteria
		if(!empty($_POST['completion_criteria'])) {
		   $advanced_settings['completion_criteria'] = $_POST['completion_criteria'];
		   if( $_POST['completion_criteria'] == 'grades' ) $advanced_settings['completion_grades'] = '|'.@implode('|', @$_POST['completion_grades']).'|';
		   else $advanced_settings['completion_grades'] = '||';
		}
		
		// paginator position
		$advanced_settings['paginator_position'] = sanitize_text_field($_POST['paginator_position']);
		$advanced_settings['paginator_decade'] = (empty($_POST['paginator_decade']) or $_POST['paginator_decade'] < 1) ? 10 : intval($_POST['paginator_decade']);
		
		// extra settings from the Intelligence module
		if(watupro_intel()) {
			$advanced_settings['premature_end_percent'] = intval(@$_POST['premature_end_percent']);
			$advanced_settings['premature_end_question'] = intval(@$_POST['premature_end_question']);
			if(empty($advanced_settings['premature_end_question'])) 	$advanced_settings['premature_end_percent'] = 0;
			$advanced_settings['prevent_forward_percent'] = intval(@$_POST['prevent_forward_percent']);
			$advanced_settings['premature_text'] = rawurlencode(watupro_strip_tags($_POST['premature_text']));
			$advanced_settings['prevent_forward_question'] = intval(@$_POST['prevent_forward_question']);
			if(empty($advanced_settings['prevent_forward_question'])) $advanced_settings['prevent_forward_percent'] = 0;
			
			if(!empty($advanced_settings['premature_end_question']) or !empty($advanced_settings['prevent_forward_question'])) $_POST['store_progress'] = 1;
			$advanced_settings['dependency_type'] = @$_POST['dependency_type'];
			
			$advanced_settings['user_choice'] = @$_POST['user_choice'];
			$advanced_settings['user_choice_enhanced'] = 1; // used to mark the moment we enhanced the feature with more settings
			$advanced_settings['user_choice_modes'] = @$_POST['user_choice_modes'];
			
			if($use_wp_roles == 1) {
				$advanced_settings['free_access_roles'] = @$_POST['free_access_roles'];
			}
			else $advanced_settings['free_access_groups'] = watupro_int_array(@$_POST['free_access_groups']);
			
			$advanced_settings['free_access_bp_groups'] = empty($_POST['free_access_bp_groups']) ? array(): watupro_int_array($_POST['free_access_bp_groups']);
			
			$advanced_settings['payment_instructions'] = rawurlencode(watupro_strip_tags($_POST['payment_instructions']));
			$advanced_settings['paid_quiz_redirect'] = esc_url_raw($_POST['paid_quiz_redirect']);
				
			$advanced_settings['attempts_price_change_action'] = sanitize_text_field(@$_POST['attempts_price_change_action']);
			$advanced_settings['attempts_price_change_amt'] = floatval(@$_POST['attempts_price_change_amt']);
			$advanced_settings['attempts_price_change_limit'] = floatval(@$_POST['attempts_price_change_limit']);
			
			$advanced_settings['woo_product_id'] = empty($_POST['woo_product_id']) ? 0 : intval($_POST['woo_product_id']);
			
			// restart number of attempts when a payment is made
			$advanced_settings['payment_restarts_attempts'] = empty($_POST['payment_restarts_attempts']) ? 0 : 1;
		}
		
		$_POST['advanced_settings'] = serialize($advanced_settings);
		
		if($_REQUEST['action'] == 'edit') { //Update goes here
			$exam_id = $_REQUEST['quiz'];

			if($multiuser_access == 'own') {
				$editor_id = $wpdb->get_var($wpdb->prepare("SELECT editor_id FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));				
				if($editor_id != $user_ID) wp_die('You can edit only your own exams','watupro');
			}	
			
			if($multiuser_access == 'group') {		
				$cat_ids = WTPCategory::user_cats($user_ID);
				$cat_id_sql=implode(",",$cat_ids);
				$allowed_to_edit = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_EXAMS." 
					WHERE cat_id IN ($cat_id_sql) AND ID=%d", $exam_id));
				if(!$allowed_to_edit) wp_die(__('You can edit only quizzes within your allowed categories', 'watupro'));					
			}					
			
			if(empty($_POST['use_different_email_output'])) $_POST['email_output']='';
			WTPExam::edit($_POST, $exam_id);
			if(!empty($_POST['auto_publish'])) watupro_auto_publish($exam_id);
			$wp_redirect = admin_url('admin.php?page=watupro_exams&message=updated');	
			
			// save advanced settings
			if($exam_id) {
				$_GET['exam_id'] = $exam_id;
				$_POST['ok'] = true;
				watupro_advanced_exam_settings();
			}
		} else {
			// add new exam
			$exam_id = WTPExam::add($_POST);			
			if($exam_id == 0 ) $wp_redirect = admin_url('admin.php?page=watupro_exams&message=fail');
			if($exam_id and !empty($_POST['auto_publish'])) watupro_auto_publish($exam_id);
			$wp_redirect = admin_url('admin.php?page=watupro_questions&message=new_quiz&quiz='.$exam_id);
		}
		
		if(watupro_intel() and !empty($exam_id)) WatuPROWoo :: update_woo($exam_id, (empty($_POST['woo_product_id']) ? 0 : $_POST['woo_product_id']), (empty($old_advanced_settings['woo_product_id']) ? 0 : $old_advanced_settings['woo_product_id']) );
	   echo "<meta http-equiv='refresh' content='0;url=$wp_redirect' />"; 
    exit;
	}
	
	$action = 'new';
	if(@$_REQUEST['action'] == 'edit') $action = 'edit';
	
	// global answer_display
	$answer_display=get_option('watupro_show_answers');
	// global single page display
	$single_page = get_option('watupro_single_page');
	
	$dquiz = array();
	$grades = array();
	
	// initialize advanced settings to avoid PHP notices
	$advanced_settings = array('play_levels' => '');
	
	if($action == 'edit') {
		$dquiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
		$single_page = $dquiz->single_page;

		if($multiuser_access == 'own' and $dquiz->editor_id != $user_ID) wp_die('You can edit only your own exams','watupro');		
		
		$grades = WTPGrade :: get_grades($dquiz);	
		$final_screen = stripslashes($dquiz->final_screen);
		$schedule_from = $dquiz->schedule_from;
		list($schedule_from) = explode(" ", $schedule_from);
		$schedule_to = $dquiz->schedule_to;
		list($schedule_to) = explode(" ", $schedule_to);
		
		$advanced_settings = unserialize( stripslashes($dquiz->advanced_settings));	
		
	  	// rawurl decode fields?	  	 
	  	$advanced_settings['contact_fields']['email_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['email_label']));
	  	$advanced_settings['contact_fields']['name_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['name_label']));
	  	$advanced_settings['contact_fields']['phone_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['phone_label']));
	  	$advanced_settings['contact_fields']['company_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['company_label']));
	  	$advanced_settings['contact_fields']['field1_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['field1_label']));
	  	$advanced_settings['contact_fields']['field1_dropdown_values'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['field1_dropdown_values']));
	  	$advanced_settings['contact_fields']['field2_label'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['field2_label']));
	  	$advanced_settings['contact_fields']['field2_dropdown_values'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['field2_dropdown_values']));
	  	$advanced_settings['contact_fields']['checkbox'] = stripslashes(rawurldecode(@$advanced_settings['contact_fields']['checkbox']));
	  	 
	} // end edit 
	else {
		$default_final_screen = get_option('watupro_default_final_screen');
		$final_screen = empty($default_final_screen) ? __("<p>You have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%CORRECT%% correct out of %%TOTAL%% questions.</p>\n\n<p>You have collected %%POINTS%% points.</p>\n\n<p>Your obtained grade is <b>%%GRADE%%</b></p>\n\n<p>Your answers are shown below:</p>\n\n%%ANSWERS%%", 'watupro') : $default_final_screen;
		$schedule_from = date("Y-m-d");
		$schedule_to = date("Y-m-d");
	}
	
	// select certificates if any
	$certificates=$wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." ORDER BY title");
	$cnt_certificates=sizeof($certificates);
	
	// categories if any
	$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=0 ORDER BY name");
	$subs = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id!=0 ORDER BY name");
	// match cats & subs
	foreach($cats as $cnt => $cat) {
		$cat_subs = array();
		foreach($subs as $sub) {
			if($sub->parent_id == $cat->ID) $cat_subs[] = $sub;
		}
		$cats[$cnt] -> subs = $cat_subs;
	}

	// avoid PHP notices
	if(empty($dquiz->ID)) {
		$default_email_output = get_option('watupro_default_email_output');
		$dquiz = (object)array("ID"=>0, "name"=>"", "description"=>"", 
		"single_page" => WATUPRO_PAGINATE_ALL_ON_PAGE, 'schedule_from'=>'', 'schedule_to'=>'', 'email_output'=> $default_email_output); 
	}
	
	// select other exams
	$other_exams=$wpdb->get_results("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID!='".intval($dquiz->ID)."' ORDER BY name");
	
	if(watupro_intel()) {		
		require_once(WATUPRO_PATH."/i/models/dependency.php");
		$dependencies = WatuPRODependency::select($dquiz->ID);	
			
		// select all editors for the editors drop-down. Makes sense only if any roles are selected to manage exams so do it only then
		$editors = get_users(array("role" => 'administrator'));
		$more_roles = false;		
		foreach($roles as $key=>$r) {			
			$role = get_role($key);
			if(empty($role->capabilities['watupro_manage_exams'])) continue;
			
			// add users to $editors array
			$users = get_users(array("role" => $key)); 
			$editors = array_merge($editors, $users);
			$more_roles = true;
		}
			
		// prepare the $user_choice_modes
		$user_choice_modes = empty($advanced_settings['user_choice_modes']) ? array() : $advanced_settings['user_choice_modes'];
	}
	
	// check if recaptcha keys are in place
	$recaptcha_public = get_option('watupro_recaptcha_public');
	$recaptcha_private = get_option('watupro_recaptcha_private');
	
	// is this quiz currently published?
	if(!empty($_GET['quiz'])) {
		$quiz_id = intval($_GET['quiz']);
		$is_published = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%[watupro ".$quiz_id."]%' 
				AND post_status='publish' AND post_title!=''");
	} 
	else $is_published = false;
	
	// any difficulty levels?
	$difficulty_levels = stripslashes(get_option('watupro_difficulty_levels'));
	if(!empty($difficulty_levels)) $difficulty_levels = explode(PHP_EOL, $difficulty_levels);
	
	$grades_by_percent_dropdown = '<select name="grades_by_percent_mode">
		<option value="correct_answer" '.((empty($advanced_settings['grades_by_percent_mode']) or $advanced_settings['grades_by_percent_mode'] != 'max_points') ? "selected" : '').'>'.__('% correct answers', 'watupro').'</option>
		<option value="max_points" '.((!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points') ? "selected" : '').'>'.__('% from maximum points', 'watupro').'</option>
	</select>';
	
	 // default points
  	 if($set_default_points) {
  	 	$default_correct_answer_points = isset($advanced_settings['default_correct_answer_points']) ? $advanced_settings['default_correct_answer_points'] : get_option('watupro_correct_answer_points');
  	 	$default_incorrect_answer_points = isset($advanced_settings['default_incorrect_answer_points']) ? $advanced_settings['default_incorrect_answer_points'] : get_option('watupro_incorrect_answer_points');
  	 } // end default points
  	 
  	 // check watupro admin sender to give warning if there is no sender's name defined
  	 $admin_email = watupro_admin_email();
  	 if(!strstr($admin_email, '<')) {
  	    $email_warning = sprintf(__('You have not defined email sender name so your emails will be sent from "WordPress". You can change this at the top of the <a href="%s" target="_blank">WatuPRO Settings page</a>.', 'watupro'), 'admin.php?page=watupro_options');
  	 }
	
	// namaste integration?
	if(class_exists('NamasteLMS') and get_option('namaste_use_exams') == 'watupro') {
		$_course = new NamasteLMSCourseModel();
		$courses = $_course->select();
	}	
	
	$post_types = get_post_types(array('public'=>true));
	
	// retrieve design themes
	$design_themes = watupro_get_design_themes();
	
	// woocommerce products
	$woo_products = WatuPROWoo :: get_products();
	
	watupro_enqueue_datepicker();
	if(@file_exists(get_stylesheet_directory().'/watupro/exam_form.php')) require get_stylesheet_directory().'/watupro/exam_form.php';
	else require WATUPRO_PATH."/views/exam_form.php";
} // end watupro_exam()

// list exams
function watupro_exams() {
	global $wpdb, $user_ID;
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	// categories if any
	$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=0 ORDER BY name");
	$subs = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id!=0 ORDER BY name");
	// match cats & subs
	foreach($cats as $cnt => $cat) {
		$cat_subs = array();
		foreach($subs as $sub) {
			if($sub->parent_id == $cat->ID) $cat_subs[] = $sub;
		}
		$cats[$cnt] -> subs = $cat_subs;
	}
	
	// mass activate/deactivate quizzes
	if((!empty($_POST['mass_activate']) or !empty($_POST['mass_deactivate'])) and check_admin_referer('watupro_exams') ) {
		$qids = is_array($_POST['qids']) ? watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$is_active = empty($_POST['mass_activate']) ? 0 : 1;
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET is_active=%d WHERE ID IN ($qid_sql)", $is_active));		
	}
	
	// mass change quiz category
	if(!empty($_POST['mass_change_category']) and check_admin_referer('watupro_exams')) {
		$qids = is_array($_POST['qids']) ?  watupro_int_array($_POST['qids']) : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET cat_id=%d 
			WHERE ID IN ($qid_sql)", $_POST['mass_cat_id']));
	}

	
	if(!empty($_REQUEST['action']) and $_REQUEST['action'] == 'delete') {	
	   check_admin_referer('delete_quiz', 'delete_nonce');	
	   
		$_GET['quiz'] = intval($_GET['quiz']);		   
	   
		if($multiuser_access == 'view' or $multiuser_access == 'group_view' or $multiuser_access == 'view_approve' or $multiuser_access == 'group_view_approve') wp_die("You are not allowed to do this");
		if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(sprintf(__('You can delete only your own %s.','watupro'), WATUPRO_QUIZ_WORD_PLURAL));
		}
		if($multiuser_access == 'group') {
			// make sure I can delete
			$cat_ids = WTPCategory::user_cats($user_ID);
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if(!in_array($quiz->cat_id, $cat_ids)) wp_die(sprintf(__('You are not allowed to delete this %s', 'watupro'), WATUPRO_QUIZ_WORD));
		}
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id IN (SELECT ID FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d)", $_GET['quiz']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d", $_GET['quiz']));		
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_GRADES." WHERE exam_id=%d", $_GET['quiz']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE exam_id=%d", $_GET['quiz']));
	}
	
	// auto cleanup / blank out data
	if(get_option('watupro_auto_db_cleanup') == '1') {		
		$days = get_option('watupro_auto_db_cleanup_days');
		$days = intval($days) ? intval($days) : 30;
	
		// global points and percent correct conditions		
		$config = get_option('watupro_auto_db_cleanup_config');
		$points_sql = $percent_sql = '';		
		
		if(!empty($config['points_config'])) {
			if($config['points_condition'] == 'more') $points_sql = $wpdb->prepare(" AND points > %f ", floatval($config['points']));
			else $points_sql = $wpdb->prepare(" AND points < %f ", floatval($config['points'])); // less
		} // end points config
		
		if(!empty($config['percent_config'])) {
			if($config['percent_condition'] == 'more') $points_sql = $wpdb->prepare(" AND percent_correct > %d ", intval($config['percent']));
			else $points_sql = $wpdb->prepare(" AND percent_correct < %d ", intval($config['percent'])); // less
		} // end percent config
		
		if($days > 0) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." 
				WHERE date < '".date("Y-m-d", current_time('timestamp'))."' - INTERVAL %d DAY $points_sql $percent_sql", $days));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." 
				WHERE timestamp < '".current_time('mysql')."' - INTERVAL %d DAY AND taking_id NOT IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS.")", $days));
			$wpdb->query("DELETE FROM ".WATUPRO_USER_FILES." WHERE taking_id NOT IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS.")");				
		}
	
		
	} // end db cleanup 
	
	// blankout
	if(get_option('watupro_auto_db_blankout') == '1') {	
			
		$days = get_option('watupro_auto_db_blankout_days');
		$days = intval($days) ? intval($days) : 30;
		
		// global points and percent correct conditions		
		$config = get_option('watupro_auto_db_blankout_config');
		$points_sql = $percent_sql = '';		
		
		if(!empty($config['points_config'])) {
			if($config['points_condition'] == 'more') $points_sql = $wpdb->prepare(" AND points > %f ", floatval($config['points']));
			else $points_sql = $wpdb->prepare(" AND points < %f ", floatval($config['points'])); // less
		} // end points config
		
		if(!empty($config['percent_config'])) {
			if($config['percent_condition'] == 'more') $points_sql = $wpdb->prepare(" AND percent_correct > %d ", intval($config['percent']));
			else $points_sql = $wpdb->prepare(" AND percent_correct < %d ", intval($config['percent'])); // less
		} // end percent config
		
		if($days > 0) {			
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
				SET details='data removed', catgrades='data removed' 
				WHERE date < '".date("Y-m-d", current_time('timestamp'))."' - INTERVAL %d DAY $points_sql $percent_sql", $days));
				
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." 
				SET question_text='', snapshot='data removed' 
				WHERE timestamp < '".current_time('mysql')."' - INTERVAL %d DAY AND taking_id NOT IN (SELECT ID FROM ".WATUPRO_TAKEN_EXAMS.")", $days));	
		}		
	} // end db blankout 
	
	$ob = empty($_GET['ob']) ? "Q.ID" : sanitize_text_field($_GET['ob']);
	$dir = empty($_GET['dir']) ? "DESC" : $_GET['dir'];
	if($dir != 'DESC' and $dir != 'ASC') $dir = 'ASC';
	$odir = ($dir == 'ASC') ? 'DESC' : 'ASC';
	
	// reset page limit?
	if(!empty($_POST['reset_page_limit'])) update_option('watupro_manage_quizzes_per_page', intval($_POST['page_limit']));
	
	$page_limit = get_option('watupro_manage_quizzes_per_page');
	if(empty($page_limit)) $page_limit = 50;
	
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	$limit_sql = $wpdb->prepare(" LIMIT %d, %d ", $offset, $page_limit);
	
	// filters
	$filter_sql = $filter_params = "";
	if(isset($_GET['cat_id']) and $_GET['cat_id']!= -1) {
		$filter_sql .= $wpdb->prepare(" AND Q.cat_id = %d ", intval($_GET['cat_id']));
		$filter_params .= "&cat_id=$_GET[cat_id]";
	}
	if(!empty($_GET['title'])) {
		$_GET['title'] = sanitize_text_field($_GET['title']);
		$filter_sql .= " AND Q.name LIKE '%$_GET[title]%' ";
		$filter_params .= "&title=$_GET[title]";
	}
	if(!empty($_GET['comments'])) {
		$_GET['comments'] = sanitize_text_field($_GET['comments']);
		$filter_sql .= " AND Q.admin_comments LIKE '%$_GET[comments]%' ";
		$filter_params .= "&comments=$_GET[comments]";
	}
	if(!empty($_GET['exam_id'])) {
		$filter_sql .= $wpdb->prepare(" AND Q.ID = %d ", intval($_GET['exam_id']));
		$filter_params .= "&exam_id=".intval($_GET['exam_id']);
	}
	// filter by tag 
	if(!empty($_GET['filter_tag'])) {
		$tags = explode(",", sanitize_text_field($_GET['filter_tag']));
		
		foreach($tags as $tag) {
			$tag = trim($tag);
			$filter_sql .= " AND tags LIKE '%|".$tag."|%'";
			$filter_params .= "&filter_tag=".esc_attr($_GET['filter_tag']);
		}
	}
	
	$editor_sql = '';
	if($multiuser_access == 'own') $editor_sql = $wpdb->prepare(" AND Q.editor_id = %d", $user_ID);
	// handle access to all exams but with user group restrictions

	if($multiuser_access == 'group' or $multiuser_access == 'group_view' or $multiuser_access == 'group_view_approve') {		
		$cat_ids = WTPCategory::user_cats($user_ID);
		$cat_id_sql=implode(",",$cat_ids);
		$editor_sql = " AND Q.cat_id IN ($cat_id_sql) ";
	}	
	
	$count_sqls = ",(SELECT COUNT(ID) FROM ".WATUPRO_QUESTIONS." WHERE exam_id=Q.ID) AS question_count,
	(SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=Q.ID AND in_progress=0) AS taken";
	
	$low_memory_mode = get_option('watupro_low_memory_mode');
	if($low_memory_mode == 1) $count_sqls = '';

	$exams = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS Q.*, tC.name as cat
	$count_sqls
	FROM ".WATUPRO_EXAMS." AS Q LEFT JOIN ".WATUPRO_CATS." as tC ON tC.id=Q.cat_id
	WHERE Q.ID > 0 $filter_sql $editor_sql
	ORDER BY $ob $dir $limit_sql");
	
	$count = $wpdb->get_var("SELECT FOUND_ROWS()");
	
	// now select all posts that have watupro shortcode in them
	$posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} 
		WHERE post_content LIKE '%[watupro %]%'
		AND (post_status='publish' OR post_status='private')
		AND post_title!=''
		ORDER BY post_date DESC");	
		
	// match posts to exams
	foreach($exams as $cnt=>$exam) {
		$exam_author = new WP_User( $exam->editor_id );

		foreach($posts as $post) {
			if(stristr($post->post_content,"[watupro ".$exam->ID."]") or stristr($post->post_content,"[watupro ".$exam->ID." ")) {
				$exams[$cnt]->post=$post;

				if ( $exam_author ) {
					$exams[ $cnt ]->author = $exam_author->user_login;
				}

				break;
			}
		}
	}

	// detect if Chained Logic is installed & active but the version is old
	if(function_exists('wchained_activate')) {
		$chained_version = get_option('wchained_version');
		if(floatval($chained_version) < 0.22) $chained_notice = true;
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/exams.php')) require get_stylesheet_directory().'/watupro/exams.php';
	else require WATUPRO_PATH."/views/exams.php";
}

// open form to copy quiz
function watupro_copy_exam() {	
	global $wpdb, $user_ID;
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" AND editor_id=%d ", $user_ID) : "";
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['id']));
	$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE  exam_id=%d order by ID ", $exam->ID) );
	$questions = $wpdb->get_results($wpdb->prepare("SELECT cat_id, question, ID, tags, title, sort_order 
		FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d ORDER BY sort_order, ID", $exam->ID));
	$cids = [];
	$tags = array();
	foreach($questions as $question) {
		if(!in_array($question->cat_id, $cids)) $cids[] = $question->cat_id;
		
		$qtags = explode('|',$question->tags);
		$qtags = array_filter($qtags);
		$tags = array_merge($tags, $qtags);
	}
	$cids[] = 0;
	$cidsql = implode(", ", $cids);
	$tags = array_unique($tags);
	sort($tags);
	
	// select question categories to group questions by cats
	$qcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE ID IN ($cidsql) ORDER BY name");
	// add Uncategorized
	$qcats[] = (object) array("ID"=>0, "name"=>__('Uncategorized', 'watupro'));
	
	// reorder $qcats following $cids
	$new_qcats = [];
	foreach($cids as $cid) {
		foreach($qcats as $qcat) {
			if($cid == $qcat->ID) $new_qcats[] = $qcat;
		}
	}	
	$qcats = $new_qcats;
	
	$other_exams=$wpdb->get_results("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID>0 $own_sql ORDER BY name");
	
	if(!empty($_POST['copy_exam'])) {		
		try {
			$copy_to=($_POST['copy_option']=='new')?0:$_POST['copy_to'];
			WTPExam::copy($exam->ID, $copy_to);			
			$redirect = "admin.php?page=watupro_exams&flash=".urlencode(__("The quiz was successfully copied!", 'watupro'));
			watupro_redirect($redirect);
		}
		catch(Exception $e) {
			$error=$e->getMessage();
		}	 
	}
	
	$show_title_desc = get_option('watupro_show_title_desc');
	session_write_close();
	if(@file_exists(get_stylesheet_directory().'/watupro/copy-exam-form.html.php')) require get_stylesheet_directory().'/watupro/copy-exam-form.html.php';
	else require WATUPRO_PATH."/views/copy-exam-form.html.php";
}

// replace title & meta tags on shared URLs
// called on template_redirect from init.php
function watupro_share_redirect() {
	global $post, $wpdb;
	
	if(empty($_GET['waturl'])) return false;
	
	$url = @base64_decode(sanitize_text_field($_GET['waturl'])); 
	list($exam_id, $tid) = explode("|", $url); 
	if(!is_numeric($exam_id) or !is_numeric($tid)) return false;
		
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $tid));
	if(empty($taking->grade_id)) return false;
	
	// select exam
	$shareable = $wpdb->get_var($wpdb->prepare("SELECT shareable_final_screen FROM ".WATUPRO_EXAMS." WHERE ID=%d", $taking->exam_id)); 
	if(!$shareable) return false;
	
	// select grade
	$grade = $wpdb->get_row($wpdb->prepare("SELECT gtitle, gdescription FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));
	
	$post->post_title = stripslashes($grade->gtitle);
	$post->post_excerpt = stripslashes($taking->result);
}

// display snippets for social sharing
// used to force G+ and LinkedIn to use proper content
function watupro_social_share_snippet() {
	global $post, $wpdb;
	
	if(empty($_GET['tid']) or empty($_GET['watupro_sssnippet'])) return false;
		
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $_GET['tid']));
	
	// select exam and make sure social sharing buttons are there. If not,  redirect to the post
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT name, final_screen FROM ".WATUPRO_EXAMS." WHERE ID=%d", $taking->exam_id));
	
	if(!strstr($quiz->final_screen, '[watuproshare-buttons')) watupro_redirect(get_permalinkg($_GET['redirect_to']));
	$quiz_name = $quiz->name;
			
	// select grade
	$grade = $wpdb->get_row($wpdb->prepare("SELECT gtitle, gdescription FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));
	if(empty($grade->gtitle)) $grade = (object)array("gtitle"=>__('None', 'watupro'), 'gdescription'=>__('None', 'watupro'));	
	
	// try to get the image
	// this code repeats in the social-sharing.php controller, let's try to avoid this
	$target_image = '';
	if(strstr($grade->gdescription, '<img')) {
		// find all pictures in the grade descrption
		$html = stripslashes($grade->gdescription);
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		$images = array();
		foreach ($dom->getElementsByTagName('img') as $image) {
		    $src =  $image->getAttribute('src');	
		    $class = $image->getAttribute('class');
		    $images[] = array('src'=>$src, 'class'=>$class);
		} // end foreach DOM element
		
		if(sizeof($images)) {
			$target_image = $images[0]['src'];
			
			// but check if we have any that are marked with the class
			foreach($images as $image) {
				if(strstr($image['class'], 'watupro-share')) {
					$target_image = $image['src'];
					break;
				}
			}
		}
	}   // end searching for image
	
 	// prepare open graph title & description - same for LinkedIn and Gplus 
	$linkedin = get_option('watuproshare_linkedin');
	$og_msg = stripslashes($linkedin['msg']);
	$og_title = stripslashes($linkedin['title']);
			
	// title and description set up?
	if(!empty($og_title)) {
		$og_title = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $og_title);				
		$og_title = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $og_title);
	}
	if(!empty($og_msg)) {
		$og_msg = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $og_msg);			
		$og_msg = str_replace('{{{grade-description}}}', stripslashes($grade->gdescription), $og_msg);	
		$og_msg = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $og_msg);
		$og_msg = str_replace('{{{url}}}', get_permalink($_POST['post_id']), $og_msg);
	}
	
	// if not, default to grade title and desc
	if(empty($og_title)) $og_title = $grade->gtitle;
	if(empty($og_msg)) $og_msg = $grade->gdescription;
	
	$og_title = stripslashes($og_title);
	$og_msg = stripslashes($og_msg);	
	
	$og_description = str_replace('"',"'",$og_msg);
	$og_description = str_replace(array("\n","\r")," ",$og_description);	
	$og_description = strip_tags($og_description);
	$og_title = str_replace('"',"'",$og_title);
	$og_title = str_replace(array("\n","\r")," ",$og_title);
	
	include(WATUPRO_PATH."/views/social-share-snippet.html.php");
	exit;
}

// auto publish quiz in post
// some data comes directly from the $_POST to save unnecessary DB query
function watupro_auto_publish($quiz_id) {	
	global $wpdb;
	// if the quiz has category try to match with post categories
	$post_cat_id=0;
	$cat_name = $wpdb->get_var($wpdb->prepare("SELECT tC.name FROM ".WATUPRO_CATS." tC
		JOIN ".WATUPRO_EXAMS." tE ON tE.cat_id=tC.ID 
		WHERE tE.ID=%d", $quiz_id));
	if(!empty($cat_name)) {
		$post_cat_id = get_cat_ID($cat_name);
	}
	
	if(!empty($_POST['auto_publish_name'])) $_POST['name'] = $_POST['auto_publish_name'];
	if(empty($_POST['auto_publish_post_type'])) $_POST['auto_publish_post_type'] = 'post';

	$post = array('post_content' => '[watupro '.$quiz_id.']', 'post_name'=> $_POST['name'], 
		'post_title'=>$_POST['name'], 'post_status'=>'publish', 'post_category' => array($post_cat_id), 'post_type' => $_POST['auto_publish_post_type']);
	wp_insert_post($post);
}

// in case the user has set some CSS properties in the settings page, let's generate onpage CSS
function watupro_onpage_css() {	
	$ui = get_option('watupro_ui'); 
	
	$css = '';
	
	if(!empty($ui['quiz_font_size'])) {
	   $css .= "div.quiz-area {
	      font-size: ".$ui['quiz_font_size']."
	   }\n";
	}
	
	if(!empty($ui['question_spacing']) or !empty($ui['question_font_size'])) {
		$css .= "div.watu-question, div.show-question {\n";
		if(!empty($ui['question_spacing'])) $css .=	"margin-top: ".$ui['question_spacing']."px !important;\n";
		if(!empty($ui['question_font_size'])) $css .=	"font-size: ".$ui['question_font_size']."!important;\n";
		$css .="}\n";
	}
	
	if(!empty($ui['answer_spacing']) or !empty($ui['choice_font_size'])) {
		$css .= ".question-choices, .show-question-choices {\n";
		if(!empty($ui['answer_spacing'])) $css .=	"margin-top: ".$ui['answer_spacing']."px !important;\n";
		if(!empty($ui['choice_font_size'])) $css .=	"font-size: ".$ui['choice_font_size']." !important;\n";
		$css .= "}\n";
	}
	
	if(!empty($ui['qxofy_spacing'])) {
		$css .= "p.watupro-qnum-info {\n";
		$css .= "padding-top:".$ui['qxofy_spacing']."px !important;\n";		
		$css .= "}\n";
	}
	
	if(!empty($ui['gap_font_size'])) {
		$css .= "input.watupro-gap, select.watupro-gap {\n";
		if(!empty($ui['gap_font_size'])) $css .=	"font-size: ".$ui['gap_font_size']." !important;\n";
		$css .= "}\n";
	}
	
	if(!empty($ui['buttons_width']) or !empty($ui['buttons_height']) or !empty($ui['buttons_font_size'])) {
	   $css .= ".watupro_buttons input, input.watupro-start-quiz, button.watupro-start-quiz {\n";
	   if(!empty($ui['buttons_width'])) $css .= 'width:'.$ui['buttons_width'].'px !important;';
      if(!empty($ui['buttons_height'])) $css .= 'height:'.$ui['buttons_height'].'px !important;';   
      if(!empty($ui['buttons_font_size'])) $css .= 'font-size:'.$ui['buttons_font_size'].' !important;';
	   $css .="}\n";
	}
	
	if(!empty($ui['buttons_table_width'])) {
	   $css .= ".watupro_buttons {\n";
	   $css .= "width:".$ui['buttons_table_width']." !important;\n";
	   $css .="}\n";
	}
	
	// timer
	if(!empty($ui['timer_position'])) {
		$css .= "div#timerDiv {
			position: fixed;
			".$ui['timer_distance_vertical'].": ".intval($ui['timer_position_top'])."px;
			".$ui['timer_distance_horizontal'].": ".intval($ui['timer_position_left'])."px;
		}\n";
	}
	
	if(!empty($ui['timer_color'])) {
		$css .= "div#timerDiv {
			color: ".$ui['timer_color'].";
		}\n";
	}
	
	if(!empty($ui['timer_font_size'])) {
		$css .= "div#timerDiv {
			font-size: ".$ui['timer_font_size'].";
		}\n";
	}
	
	// sortables
	if(!empty($ui['sortable_border']) or !empty($ui['sortable_color']) or !empty($ui['sortable_bgcolor']) or !empty($ui['sortable_font_size'])) {
		$css .= ".watupro-sortable li {\n";
		if(!empty($ui['sortable_border'])) $css .= "border: ".$ui['sortable_border']." !important;\n";
		if(!empty($ui['sortable_color'])) $css .= "color: ".$ui['sortable_color']." !important;\n";	
		if(!empty($ui['sortable_bgcolor'])) $css .= "background-color: ".$ui['sortable_bgcolor']." !important;\n";
		if(!empty($ui['sortable_font_size'])) $css .= "font-size: ".$ui['sortable_font_size']." !important;\n";
		$css.= "}\n";
	}
	
	// adjustment for checkboxes/radio buttons when they are going a little below or above the label text next to them 
	if(!empty($ui['choices_valign'])) {
		$css .= ".watupro-question-choice input[type=radio],
			.watupro-question-choice input[type=checkbox] {
			    vertical-align: ".$ui['choices_valign']."em; 
			}\n";
	}
	
	// additional CSS
	if(!empty($ui['additional_css'])) {
		$css .= "\n".$ui['additional_css']."\n\n";
	}
	
	// add this CSS unless user marked problem with MathJax or similar plugins
	if(empty($ui['mathjax_problem'])) {
		$css .= ".watupro-question-choice span {
		  display: inline !important; /* added in WatuPRO 6.1.0.4 */
		}\n";
	}
	
	if(!empty($ui['media_query_mobile'])) {
		$css .= "@media 
			only screen and (max-width: ".(intval($ui['media_query_mobile']))."px) {\n";
			
		if(!empty($ui['mobile_choice_margin'])) {
			$mobile_choice_magin_unit = empty($ui['mobile_choice_margin_unit']) ? 'px' : $ui['mobile_choice_margin_unit']; 			
			
			$css .= ".watupro-question-choice {
				margin-top: ".intval($ui['mobile_choice_margin']).$mobile_choice_magin_unit." !important;
				margin-bottom: ".intval($ui['mobile_choice_margin']).$mobile_choice_magin_unit." !important;
			}\n";
		}			
			
		$css .=	"}\n";
	}
		
	return $css;
	
}

// template_redirect function that loads the dynamic CSS which from the WatuPRO Settings page -> Theme and Design tab
// calls watupro_onpage_css
function watupro_dynamic_css_redirect() {
	if(empty($_GET['watupro_dynamic_css'])) return true;
	header("Content-type: text/css; charset: UTF-8");
	echo watupro_onpage_css();
	exit;
}

function watupro_custom_theme() {
	global $wpdb;
	
	$css = $wpdb->get_var($wpdb->prepare("SELECT css FROM ".WATUPRO_THEMES." WHERE ID=%d", intval($_GET['watupro_custom_theme'])));
	echo $css;
	exit;
}

// template_redirect function that loads the custom theme CSS when the theme is stored in the DB
// calls watupro_onpage_css
function watupro_custom_theme_redirect() {
	if(empty($_GET['watupro_custom_theme'])) return true;
	header("Content-type: text/css; charset: UTF-8");
	echo watupro_custom_theme();
	exit;
}