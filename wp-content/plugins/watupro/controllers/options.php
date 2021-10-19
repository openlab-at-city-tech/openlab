<?php
function watupro_options() {
    global $wpdb, $wp_roles;
    $roles = $wp_roles->roles;	
    
    // auto_db_cleanup/blankout change. We do this once in version 6.3.6.6
    if(get_option('watupro_auto_db_cleanup_transferred') == '')  {
    	$auto_db_cleanup_mode = get_option('watupro_auto_db_cleanup_mode');
		$db_cleanup_config = get_option('watupro_auto_db_cleanup_config');
		
		// action is required only if mode was blankout. We should then transfer the cleanup options as blankout options 
		// and disable cleanup, but we can keep the same options
		if($auto_db_cleanup_mode == 'blankout' and get_option('watupro_auto_db_cleanup') == 1) {
			update_option('watupro_auto_db_cleanup', '');
			update_option('watupro_auto_db_blankout', 1);
			update_option('watupro_auto_db_blankout_config', $db_cleanup_config);
			$days = get_option('watupro_auto_db_cleanup_days');
			update_option('watupro_auto_db_blankout_days', $days);
			update_option('watupro_auto_db_cleanup_days', 0);
		}		
		
    	update_option('watupro_auto_db_cleanup_transferred', 1);
    } // end applying auto db cleanup/blankout change
    	
		
		if(!empty($_POST['save_options']) and check_admin_referer('watupro_options')) {
			if(empty($_POST['currency'])) $_POST['currency'] = @$_POST['custom_currency'];
			
			$view_details_hidden_columns = isset( $_POST['view_details_hidden_columns'] ) ? (array) $_POST['view_details_hidden_columns'] : array();
			$view_details_hidden_columns = array_map( 'esc_attr', $view_details_hidden_columns );
			
			$ui = array(
			   'question_spacing' => intval($_POST['question_spacing']), 
			   'answer_spacing' => intval($_POST['answer_spacing']),
			   'buttons_width' => intval($_POST['buttons_width']),
			   'buttons_height' => intval($_POST['buttons_height']),
			   'buttons_font_size' => sanitize_text_field($_POST['buttons_font_size']),
			   'qxofy_spacing' => intval($_POST['qxofy_spacing']),
			   'choices_valign' => (empty($_POST['choices_valign']) ? '' : floatval($_POST['choices_valign'])),
			   'quiz_font_size' => sanitize_text_field($_POST['quiz_font_size']),
			   'question_font_size' => sanitize_text_field($_POST['question_font_size']),
			   'choice_font_size' => sanitize_text_field($_POST['choice_font_size']),
			   'gap_font_size' => sanitize_text_field($_POST['gap_font_size']),
			   'timer_position' => sanitize_text_field($_POST['timer_position']),
			   'timer_distance_vertical' => sanitize_text_field($_POST['timer_distance_vertical']), // "top" or "bottom"
			   'timer_position_top' => intval($_POST['timer_position_top']), // for top or bottom
			   'timer_distance_horizontal' => sanitize_text_field($_POST['timer_distance_horizontal']), // "left" or "right"
			   'timer_position_left' => intval($_POST['timer_position_left']), // for left or right
			   'timer_color' => sanitize_text_field($_POST['timer_color']),
			   'timer_font_size' => sanitize_text_field($_POST['timer_font_size']),
			   'timer_format' => sanitize_text_field($_POST['timer_format']),
			   'mathjax_problem' => (empty($_POST['mathjax_problem']) ? 0 : 1),
			   'media_query_mobile' => intval($_POST['media_query_mobile']), 
			   'mobile_choice_margin' => ($_POST['mobile_choice_margin_unit'] == 'px') ? intval($_POST['mobile_choice_margin']) : floatval($_POST['mobile_choice_margin']),
			   'mobile_choice_margin_unit' => sanitize_text_field($_POST['mobile_choice_margin_unit']),
			   'use_legacy_buttons_table' => (empty($_POST['use_legacy_buttons_table']) ? 0 : 1),
			   'flag_review' => sanitize_text_field($_POST['flag_review']),
			   'exclude_details_of_taker' => (empty($_POST['exclude_details_of_taker']) ? 0 : 1),		
			   'view_details_hidden_columns' => $view_details_hidden_columns,	   
			   'autocomplete_off' => (empty($_POST['autocomplete_off']) ? 0 : 1),
			   'additional_css' => strip_tags($_POST['additional_css']),		
			);		
			
			if(watupro_intel()) {
				$ui['sortable_border'] = sanitize_text_field($_POST['sortable_border']);
				$ui['sortable_color'] = sanitize_text_field($_POST['sortable_color']);
				$ui['sortable_bgcolor'] = sanitize_text_field($_POST['sortable_bgcolor']);
				$ui['sortable_font_size'] = sanitize_text_field($_POST['sortable_font_size']);
			}
			
			// sanitize vars
			$_POST['single_page'] = intval(@$_POST['single_page']);
			$_POST['answer_type'] = sanitize_text_field($_POST['answer_type']);	
			$_POST['paypal'] = sanitize_text_field(@$_POST['paypal']);
			$_POST['other_payments'] = watupro_strip_tags(@$_POST['other_payments']);
			$_POST['currency'] = sanitize_text_field(@$_POST['currency']);
			$_POST['recaptcha_public'] = sanitize_text_field($_POST['recaptcha_public']);
			$_POST['recaptcha_private'] = sanitize_text_field($_POST['recaptcha_private']);
			$_POST['recaptcha_version'] = intval($_POST['recaptcha_version']);
			$_POST['recaptcha_lang'] = sanitize_text_field($_POST['recaptcha_lang']);
			$_POST['accept_stripe'] = empty($_POST['accept_stripe']) ? 0 : 1;
			$_POST['stripe_public'] = sanitize_text_field(@$_POST['stripe_public']);
			$_POST['stripe_secret'] = sanitize_text_field(@$_POST['stripe_secret']);
			$_POST['debug_mode'] = empty($_POST['debug_mode']) ? 0 : 1;
			$_POST['nodisplay_myquizzes'] = empty($_POST['nodisplay_myquizzes']) ? 0 : 1;
			$_POST['nodisplay_reports_tests'] = empty($_POST['nodisplay_reports_tests']) ? 0 : 1;
			$_POST['nodisplay_reports_skills'] = empty($_POST['nodisplay_reports_skills']) ? 0 : 1;
			$_POST['nodisplay_reports_history'] = empty($_POST['nodisplay_reports_history']) ? 0 : 1;
			$_POST['nodisplay_paid_quizzes'] = empty($_POST['nodisplay_paid_quizzes']) ? 0 : 1;
			$_POST['nodisplay_mysettings'] = empty($_POST['nodisplay_mysettings']) ? 0 : 1; 
			$_POST['always_load_scripts'] = empty($_POST['always_load_scripts']) ? 0 : 1;
			$_POST['disable_copy'] = empty($_POST['disable_copy']) ? 0 : 1;
			$_POST['auto_del_user_data'] = empty($_POST['auto_del_user_data']) ? '' : 'yes';
			$_POST['gdpr'] = empty($_POST['gdpr']) ? 0 : 1;
			$_POST['design_theme'] = sanitize_text_field($_POST['design_theme']);
			$_POST['paypal_sandbox'] = empty($_POST['paypal_sandbox']) ? 0 : 1;
			$_POST['paypal_button'] = sanitize_text_field(@$_POST['paypal_button']);
			$_POST['csv_delim'] = sanitize_text_field($_POST['csv_delim']);
			$_POST['csv_quotes'] = empty($_POST['csv_quotes']) ? 0 : 1;
			$_POST['low_memory_mode'] = empty($_POST['low_memory_mode']) ? 0 : 1;
			$_POST['stats_widget_off'] = empty($_POST['stats_widget_off']) ? 0 : 1;
			$_POST['use_pdt'] = empty($_POST['use_pdt']) ? 0 : 1;
			$_POST['pdt_token'] = sanitize_text_field(@$_POST['pdt_token']);
			$_POST['text_captcha'] = watupro_strip_tags($_POST['text_captcha']);
			$_POST['auto_db_cleanup'] = empty($_POST['auto_db_cleanup']) ? 0 : 1;
			//$_POST['auto_db_cleanup_mode'] = sanitize_text_field($_POST['auto_db_cleanup_mode']);
			$_POST['auto_db_cleanup_days'] = intval($_POST['auto_db_cleanup_days']);
			$_POST['auto_db_cleanup_config'] = array('points_config' => (empty($_POST['watupro_auto_db_cleanup_config_points_config']) ? 0 : 1), 
				'points_condition' => sanitize_text_field($_POST['watupro_auto_db_cleanup_config_points_condition']),
				'points' => floatval($_POST['watupro_auto_db_cleanup_config_points']),
				'percent_config' => (empty($_POST['watupro_auto_db_cleanup_config_percent_config']) ? 0 : 1), 
				'percent_condition' => sanitize_text_field($_POST['watupro_auto_db_cleanup_config_percent_condition']),
				'percent' => floatval($_POST['watupro_auto_db_cleanup_config_percent']), );
			$_POST['auto_db_blankout'] = empty($_POST['auto_db_blankout']) ? 0 : 1;
			$_POST['auto_db_blankout_days'] = intval($_POST['auto_db_blankout_days']);
			$_POST['auto_db_blankout_config'] = array('points_config' => (empty($_POST['watupro_auto_db_blankout_config_points_config']) ? 0 : 1), 
				'points_condition' => sanitize_text_field($_POST['watupro_auto_db_blankout_config_points_condition']),
				'points' => floatval($_POST['watupro_auto_db_blankout_config_points']),
				'percent_config' => (empty($_POST['watupro_auto_db_blankout_config_percent_config']) ? 0 : 1), 
				'percent_condition' => sanitize_text_field($_POST['watupro_auto_db_blankout_config_percent_condition']),
				'percent' => floatval($_POST['watupro_auto_db_blankout_config_percent']), );	
			$_POST['set_default_points'] = empty($_POST['set_default_points']) ? 0 : 1;
			$_POST['correct_answer_points'] = floatval($_POST['correct_answer_points']);
			$_POST['incorrect_answer_points'] = floatval($_POST['incorrect_answer_points']);
			$_POST['accept_moolamojo'] = empty($_POST['accept_moolamojo']) ? 0 : 1;
  			$_POST['moolamojo_price'] = intval($_POST['moolamojo_price']);
  			$_POST['moolamojo_button'] = watupro_strip_tags($_POST['moolamojo_button']);
  			$_POST['integrate_moolamojo'] = empty($_POST['integrate_moolamojo']) ? 0 : 1;
  			$_POST['unfiltered_html'] = empty($_POST['unfiltered_html']) ? 0 : 1; 
  			$_POST['license_key'] = sanitize_text_field($_POST['license_key']);
  			$_POST['license_email'] = sanitize_email($_POST['license_email']);
  			$_POST['taking_details_default_view'] = sanitize_text_field($_POST['taking_details_default_view']);
  			$_POST['taking_details_default_download'] = sanitize_text_field($_POST['taking_details_default_download']);
  			$_POST['taking_details_default_download_file'] = sanitize_text_field($_POST['taking_details_default_download_file']);
  			$_POST['calculate_total_user_points'] = empty($_POST['calculate_total_user_points']) ? 0 : 1; 
  			$_POST['login_register_text'] = watupro_strip_tags($_POST['login_register_text']);
  			$_POST['register_role'] = sanitize_text_field($_POST['register_role']);
  			$_POST['no_auto_updates'] = empty($_POST['no_auto_updates']) ? 0 : 1;
  			$_POST['default_final_screen'] = wp_kses_post($_POST['default_final_screen']);
  			$_POST['default_email_output'] = wp_kses_post($_POST['default_email_output']);
  			$_POST['hide_stats_widget'] = empty($_POST['hide_stats_widget']) ? 0 : 1;
  			$_POST['show_title_desc'] = empty($_POST['show_title_desc']) ? 0 : 1;
  			$_POST['timer_allowance'] = intval($_POST['timer_allowance']);
  			$_POST['save_contacts'] = empty($_POST['save_contacts']) ? 0 : 1;
  			$_POST['paid_assign_groups'] = empty($_POST['paid_assign_groups']) ? 0 : 1;
			
			$options = array('single_page', 'answer_type', 'paypal', 'other_payments', 'currency', 'recaptcha_public', 
				'recaptcha_private', 'recaptcha_version', 'recaptcha_lang', 'accept_stripe', 'stripe_public', 'stripe_secret',
				'accept_paypoints', 'paypoints_price', 'paypoints_button', 'debug_mode',
				'nodisplay_myquizzes', 'nodisplay_mycertificates', 'nodisplay_reports_tests',
				'nodisplay_reports_skills', 'nodisplay_reports_history', 
				'nodisplay_paid_quizzes', 'nodisplay_mysettings', 'always_load_scripts', 'disable_copy',
				'auto_del_user_data', 'design_theme', 'paypal_sandbox', 'paypal_button', 'csv_delim', 
				'csv_quotes', 'low_memory_mode', 'use_pdt', 'pdt_token', 'text_captcha',
				'auto_db_cleanup', 'auto_db_cleanup_days', 'auto_db_blankout', 'auto_db_blankout_days', 
				'del_play_data', 'set_default_points', 'correct_answer_points', 'incorrect_answer_points', 
				'accept_moolamojo', 'moolamojo_price', 'moolamojo_button', 'integrate_moolamojo', 'unfiltered_html',
				'license_key', 'license_email', 'gdpr', 'taking_details_default_view', 'taking_details_default_download',
				'taking_details_default_download_file', 'calculate_total_user_points', 'login_register_text', 'register_role',
				'auto_db_cleanup_config', 'auto_db_blankout_config', 'no_auto_updates', 'default_final_screen', 'default_email_output', 
				'hide_stats_widget', 'show_title_desc', 'timer_allowance', 'save_contacts', 'stats_widget_off', 'paid_assign_groups');
			foreach($options as $opt) {				
				if(isset($_POST[$opt])) update_option('watupro_' . $opt, $_POST[$opt]);
				else update_option('watupro_' . $opt, 0);
			}
			
			update_option('watupro_admin_email', $_POST['watupro_admin_email']);
			update_option('watupro_ui', $ui);
			$email_text_checkmarks = empty($_POST['email_text_checkmarks']) ? 0 : 1;
			update_option('watupro_email_text_checkmarks', $email_text_checkmarks);
			update_option('watupro_quiz_word', sanitize_text_field($_POST['quiz_word']));
         update_option('watupro_quiz_word_plural', sanitize_text_field($_POST['quiz_word_plural']));   
         
         // calculate total user points
         if(!empty($_POST['calculate_total_user_points']) and get_option('watupro_total_user_points_calculated') == '') {
         	watupro_calculate_total_user_points();
         }
			
			// add/remove capabilities
			if(current_user_can('manage_options')) {					
				foreach($roles as $key=>$role) {
					$r=get_role($key);
					
					if(!empty($_POST['manage_roles']) and is_array($_POST['manage_roles']) and in_array($key, $_POST['manage_roles'])) {
	    				if(empty($r->capabilities['watupro_manage_exams'])) $r->add_cap('watupro_manage_exams');
					}
					else $r->remove_cap('watupro_manage_exams');
				}	
			} // end if administrator	
		}
		
		if(watupro_intel()) {
			$currency = get_option('watupro_currency');
			$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
		   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
		   "ILS"=>"ILS", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
		   "SGD"=>"SGD", "ZAR" => "ZAR");
		   
		   $currency_keys = array_keys($currencies);		   
			$accept_stripe = get_option('watupro_accept_stripe');
			$accept_moolamojo = get_option('watupro_accept_moolamojo');
			$moolamojo_button = get_option('watupro_moolamojo_button');
			if(empty($moolamojo_button)) $moolamojo_button = "<p align='center'>".sprintf(__('You can also buy access to this %s with {{{credits}}} virtual credits from your balance. You currently have [moolamojo-balance] credits total.', 'watupro'), WATUPRO_QUIZ_WORD)."</p><p align='center'>{{{button}}}</p>";
			$payment_errors = get_option("watupro_errorlog");
			$payment_errors = substr($payment_errors, 0, 10000);
			$other_payments = get_option('watupro_other_payments');
			$other_payments = empty($other_payments) ? "" : $other_payments;
		}
		
		// exams in watu light/free?
		if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_master"."'")) == strtolower($wpdb->prefix. "watu_master")) {	
			$watu_exams=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix. "watu_master ORDER BY ID");
			
			if(!empty($_POST['copy_exams'])) {
				$num_copied=0;
				foreach($watu_exams as $exam) {
					// transfer the answer display settings in the best possible way
					$exam->live_result = 0;
					if($exam->show_answers == 1) $exam->final_screen .= "\n\n<p>%%ANSWERS%%</p>";
					if($exam->show_answers == 2) $exam->live_result = 1;		
					
					// randomize questions and/or answers?
					$randomize_questions = 0;
					if($exam->randomize and $exam->randomize_answers) $randomize_questions = 1;
					if($exam->randomize and !$exam->randomize_answers) $randomize_questions = 2;
					if(!$exam->randomize and $exam->randomize_answers) $randomize_questions = 3;
					
					// replace %%GRADE-TITLE%% and %%GRADE-DESCRIPTION%% with %%GTITLE%% and %%GDESC%%
					$exam->final_screen = str_replace('%%GRADE-TITLE%%', '%%GTITLE%%', $exam->final_screen);
					$exam->final_screen = str_replace('%%GRADE-DESCRIPTION%%', '%%GDESC%%', $exam->final_screen);
					
					$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EXAMS." SET 
						name=%s, description=%s, final_screen=%s, added_on=%s, is_active=1,
						show_answers=0, email_output='', live_result=%d, randomize_questions=%d,
						require_login=%d, email_admin=%d, email_taker=%d, admin_email=%s,
						take_again=%d, times_to_take=%d, single_page=%d", 
						stripslashes($exam->name), stripslashes($exam->description), 
						stripslashes($exam->final_screen), date("Y-m-d"), 
						$exam->live_result, $randomize_questions, $exam->require_login, 
						$exam->notify_admin, $exam->notify_user, $exam->notify_email,
						$exam->take_again, $exam->times_to_take, $exam->single_page));
						
					$id=$wpdb->insert_id;
					// echo $id.'a';
					
					if($id) {
						$num_copied++;
						
						// copy questions and choices
						$questions=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."watu_question 
							WHERE exam_id=%d ORDER BY ID", $exam->ID));
						foreach($questions as $question) {
							$elaborate_explanation = '';
							
							if(!empty($question->feedback) and strstr($question->feedback, '{{{split}}}')) {
								$elaborate_explanation = 'boolean';
							}							
							
							$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."watupro_question SET
								exam_id=%d, question=%s, answer_type=%s, sort_order=%d, explain_answer=%s, elaborate_explanation=%s", 
								$id, stripslashes($question->question), stripslashes($question->answer_type), 
								$question->sort_order, stripslashes($question->feedback), $elaborate_explanation));
							$qid=$wpdb->insert_id;
							
							if($qid) {
								$choices=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_answer 
									WHERE question_id=%d ORDER BY ID", $question->ID));
								foreach($choices as $choice) {
									$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_answer SET
										question_id=%d, answer=%s, correct=%s, point=%d, sort_order=%d",
										$qid, stripslashes($choice->answer), $choice->correct, $choice->point, $choice->sort_order));
								}	
							}	
						}				
						
						// copy grades
						$grades=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_grading WHERE exam_id=%d ORDER BY ID", $exam->ID));
						
						foreach($grades as $gct=>$grade) {
							$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_grading SET
								exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%d, gto=%d",  
								$id, stripslashes($grade->gtitle), stripslashes($grade->gdescription), $grade->gfrom, $grade->gto));
							$grade_id = $wpdb->insert_id;
							$grades[$gct]->new_grade_id = $grade_id;
						} // end foreach grade
						
						// replace shortcodes?
						if(!empty($_POST['replace_watu_shortcodes'])) {
							$wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '[WATU ".$exam->ID."]', '[watupro ".$id."]')");
						}				
						
						// copy takings?
						if(!empty($_POST['copy_takings'])) {
							$takings = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_takings 
								WHERE exam_id=%d ORDER BY ID", $exam->ID));
							
							foreach($takings as $taking) {
								// figure out the taking grade ID
								$taking_grade_id = 0;
								foreach($grades as $grade) {
									if($taking->grade_id == $grade->ID) $taking_grade_id = $grade->new_grade_id;
								}				
								
								$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_TAKEN_EXAMS." SET
									user_id=%d, exam_id=%d, date=%s, points=%s, details=%s, result=%s, ip=%s, grade_id=%d, from_watu=1",
									$taking->user_id, $id, $taking->date, $taking->points, stripslashes($taking->snapshot),
									stripslashes($taking->result), $taking->ip, $taking_grade_id));
							}	
						}		
						
					} // end if exam $id	
				} // end foreach exam
		
				$copy_message= sprintf(__("%d %s successfully copied.", 'watupro'), $num_copied, __('quizzes', 'watupro'));		
				
			} // end if copy exams
		} // end if there is watu table
		
		$delete_db = get_option('watupro_delete_db');
		
		// save no_ajax
		if(!empty($_POST['save_ajax_settings'])) {
			$ids = empty($_POST['no_ajax']) ? array(0) : watupro_int_array($_POST['no_ajax']);
			
			$wpdb->query("UPDATE ".WATUPRO_EXAMS." SET no_ajax=1 WHERE ID IN (".implode(', ', $ids).")");
			$wpdb->query("UPDATE ".WATUPRO_EXAMS." SET no_ajax=0 WHERE ID NOT IN (".implode(', ', $ids).")");
			
			update_option('watupro_max_upload', intval($_POST['max_upload']));
			update_option('watupro_upload_file_types', esc_attr($_POST['upload_file_types']));
		}
		
		// select all quizzes for No Ajax option
		$quizzes = $wpdb->get_results("SELECT ID, name, no_ajax FROM ".WATUPRO_EXAMS." ORDER BY name");
		
		// retrieve design themes
		$design_themes = watupro_get_design_themes();
		$watupro_design_theme = get_option('watupro_design_theme');
		
		// CSV field separator
		$delim = get_option('watupro_csv_delim');
		
		$use_pdt = get_option('watupro_use_pdt');
		
		$text_captcha = get_option('watupro_text_captcha');
		// load 3 default questions in case nothing is loaded
		if(empty($text_captcha)) {
			$text_captcha = __('What is the color of the snow? = white', 'watupro').PHP_EOL.__('Is fire hot or cold? = hot', 'watupro') 
				.PHP_EOL. __('In which continent is Norway? = Europe', 'watupro'); 
		}
		
		$db_cleanup_config = get_option('watupro_auto_db_cleanup_config');
		$db_blankout_config = get_option('watupro_auto_db_blankout_config');
		
		$set_default_points = get_option('watupro_set_default_points');
		$correct_answer_points = get_option('watupro_correct_answer_points');
		$incorrect_answer_points = get_option('watupro_incorrect_answer_points');
		$ui = get_option('watupro_ui');
		$register_role = get_option('watupro_register_role');
		$view_details_hidden_columns = empty($ui['view_details_hidden_columns']) ? array() : (array) $ui['view_details_hidden_columns'];
		
		$default_final_screen = get_option('watupro_default_final_screen');
		if(empty($default_final_screen)) {
			$default_final_screen =  __("<p>You have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%CORRECT%% correct out of %%TOTAL%% questions.</p>\n\n<p>You have collected %%POINTS%% points.</p>\n\n<p>Your obtained grade is <b>%%GRADE%%</b></p>\n\n<p>Your answers are shown below:</p>\n\n%%ANSWERS%%", 'watupro');
		}
		$default_email_output = get_option('watupro_default_email_output');
		if(empty($default_email_output)) $default_email_output = $default_final_screen;
		
		$timer_allowance = get_option('watupro_timer_allowance');
		if(empty($timer_allowance) or $timer_allowance < 1 or !is_numeric($timer_allowance)) $timer_allowance = 10;
		
		$low_memory_mode = get_option('watupro_low_memory_mode');
		
		$recaptcha_version = get_option('watupro_recaptcha_version');		
		if(@file_exists(get_stylesheet_directory().'/watupro/options.php')) require get_stylesheet_directory().'/watupro/options.php';
		else require WATUPRO_PATH."/views/options.php";   
}

// user options
function watupro_my_options() {
	global $wpdb, $user_ID;
	
	if(!empty($_POST['ok'])) {
		update_user_meta($user_ID, "watupro_no_quiz_mails", @$_POST['no_quiz_mails']);
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/my-options.html.php')) require get_stylesheet_directory().'/watupro/my-options.html.php';
	else require WATUPRO_PATH."/views/my-options.html.php";
}

// text settings
function watupro_text_options() {
	global $wpdb, $wp_roles;
    $roles = $wp_roles->roles;
    
   // initialization of the available phrases 
   $texts = array(
			__('[no answer]', 'watupro').'===',
			__('Submit', 'watupro').'===',
			__('View Results', 'watupro').'===',
			__('Next page', 'watupro').'===',
			__('Previous page', 'watupro').'===',
			__('Next', 'watupro').'===',
			__('Previous', 'watupro').'===',
			__('(correct answer: %s)', 'watupro').'===',
			__('Time left:', 'watupro').'===',
		); 	
	sort($texts);		
		
	if(!empty($_POST['save_options']) and check_admin_referer('watupro_options')) {
		$texts = array();
		
		foreach($_POST['phrases_left'] as $cnt => $left) {
			// if left contains %s masks but right doesn't, unset the translation			
			if(strstr($left, '%s') and !strstr($_POST['phrases_right'][$cnt], '%s')) $_POST['phrases_right'][$cnt] = '';
			
			$text = $left .'==='.trim(sanitize_text_field($_POST['phrases_right'][$cnt]));
			$texts[] = $text;
		}
		update_option('watupro_texts', $texts);		
	}
	
	// if the option is not empty, we have to use it instead of the predefined $texts
	// however we must allow the code to add new settings so the variable $texts at the beginning of this function is the leading one
	$option_texts = get_option('watupro_texts');

	if(!empty($option_texts)) {
		foreach($texts as $cnt => $text) {
			list($left, $right) = explode('===', $text);
			
			foreach($option_texts as $otext) {
				list($oleft, $oright) = explode('===', $otext);
				if($oleft == $left) $texts[$cnt] = $left .'==='. $oright;
			} // end foreach option text
		} // end foreach $text
	} // end filling $texts on this page	
	
	include(WATUPRO_PATH . "/views/text-options.html.php");
}

// runs once to calculate total user points because this was not done in the past 
// (and in current versions if the option is not selected)
function watupro_calculate_total_user_points() {
	global $wpdb;
	
	// select all points grouped by user
	$points = $wpdb->get_results("SELECT SUM(points) as points, user_id as user_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE user_id!=0 AND in_progress=0 GROUP BY user_id" );
	foreach($points as $point) {
		update_user_meta($point->user_id, 'watupro_total_points', $point->points);
	}

  // at the end update option 'watupro_total_user_points_calculated' to 1 so this runs only once
  update_option('watupro_total_user_points_calculated', 1);
}

// create / manage custom design themes
function watupro_design_themes() {
	global $wpdb;
	
	$action = empty($_GET['action']) ? 'list' : sanitize_text_field($_GET['action']);
	
	switch($action) {
		case 'add':
			if(!empty($_POST['ok']) and check_admin_referer('watupro_themes')) {
				$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_THEMES." SET name=%s, css=%s", preg_replace('/\W\s/', '', $_POST['name']), strip_tags($_POST['css'])));
				watupro_redirect("admin.php?page=watupro_design_themes&".time());
			}
			
			include(WATUPRO_PATH . "/views/design-theme.html.php");
		break;
		
		case 'edit':
			if(!empty($_POST['del']) and check_admin_referer('watupro_themes')) {
				$wpdb->query($wpdb->prepare("DELETE FROM " . WATUPRO_THEMES." WHERE ID=%d", intval($_GET['id'])));
				watupro_redirect("admin.php?page=watupro_design_themes");
			}		
		
			if(!empty($_POST['ok']) and check_admin_referer('watupro_themes')) {
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_THEMES." SET css=%s WHERE ID=%d", 
					strip_tags($_POST['css']), intval($_GET['id'])));
				watupro_redirect("admin.php?page=watupro_design_themes&".time());
			}
			
			$theme = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_THEMES." WHERE ID=%d", intval($_GET['id'])));
			
			include(WATUPRO_PATH . "/views/design-theme.html.php");
		break;		
		
		case 'list':
		default:
			$themes = $wpdb->get_results("SELECT * FROM ".WATUPRO_THEMES." ORDER BY name");		
		
			include(WATUPRO_PATH . "/views/design-themes.html.php");
		break;
	}
} // end watupro_design_themes