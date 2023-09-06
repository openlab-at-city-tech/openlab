<?php
class Quiz_Maker_Settings_Actions {
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }

    public function store_data($data){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        if( isset($data["settings_action"]) && wp_verify_nonce( $data["settings_action"], 'settings_action' ) ){
            $success = 0;
            $paypal_client_id = isset($data['ays_paypal_client_id']) ? $data['ays_paypal_client_id'] : '';
            $paypal_payment_terms = isset($data['ays_paypal_payment_terms']) ? $data['ays_paypal_payment_terms'] : '';
            $paypal_extra_check = isset($data['ays_paypal_extra_check']) && $data['ays_paypal_extra_check'] == 'on' ? 'on' : 'off';
            $paypal_subscribtion_duration = isset($_REQUEST['ays-subscribtion-duration']) && $_REQUEST['ays-subscribtion-duration'] != '' ? absint( sanitize_text_field( $_REQUEST['ays-subscribtion-duration'] ) ) : '';
            $paypal_subscribtion_duration_by = isset($_REQUEST['ays-subscribtion-duration-by']) && $_REQUEST['ays-subscribtion-duration-by'] != '' ? sanitize_text_field( $_REQUEST['ays-subscribtion-duration-by'] ) : 'day';


            $roles = (isset($data['ays_user_roles']) && !empty($data['ays_user_roles'])) ? $data['ays_user_roles'] : array('administrator');
            $mailchimp_username = isset($data['ays_mailchimp_username']) ? $data['ays_mailchimp_username'] : '';
            $mailchimp_api_key = isset($data['ays_mailchimp_api_key']) ? $data['ays_mailchimp_api_key'] : '';
            $mailchimp = array(
                'username' => $mailchimp_username,
                'apiKey' => $mailchimp_api_key
            );
            
			$stripe_secret_key = isset($data['ays_stripe_secret_key']) ? $data['ays_stripe_secret_key'] : '';
			$stripe_api_key    = isset($data['ays_stripe_api_key']) ? $data['ays_stripe_api_key'] : '';
			$stripe_payment_terms = isset($data['ays_stripe_payment_terms']) ? $data['ays_stripe_payment_terms'] : '';
			$stripe = array(
				'secret_key' => $stripe_secret_key,
				'api_key' => $stripe_api_key,
				'payment_terms' => $stripe_payment_terms,
			);

			$monitor_client  = isset($data['ays_monitor_client']) ? $data['ays_monitor_client'] : '';
			$monitor_api_key = isset($data['ays_monitor_api_key']) ? $data['ays_monitor_api_key'] : '';
			$monitor         = array(
				'client' => $monitor_client,
				'apiKey' => $monitor_api_key
			);

			$slack_client = isset($data['ays_slack_client']) ? $data['ays_slack_client'] : '';
			$slack_secret = isset($data['ays_slack_secret']) ? $data['ays_slack_secret'] : '';
			$slack_token  = !empty($data['ays_slack_token']) ? $data['ays_slack_token'] : '';
			$slack        = array(
				'client' => $slack_client,
				'secret' => $slack_secret,
				'token'  => $slack_token,
			);

			$active_camp_url     = isset($data['ays_active_camp_url']) ? $data['ays_active_camp_url'] : '';
			$active_camp_api_key = isset($data['ays_active_camp_api_key']) ? $data['ays_active_camp_api_key'] : '';
			$active_camp         = array(
				'url'    => $active_camp_url,
				'apiKey' => $active_camp_api_key
			);

			$zapier_hook = isset($data['ays_zapier_hook']) ? $data['ays_zapier_hook'] : '';
			$zapier      = array(
				'hook' => $zapier_hook
			);
            
            $paypal_options = array(
                "paypal_client_id" => $paypal_client_id,
                "payment_terms"    => $paypal_payment_terms,
                "extra_check"      => $paypal_extra_check,
                "subscribtion_duration" => $paypal_subscribtion_duration,
                "subscribtion_duration_by" => $paypal_subscribtion_duration_by,
            );
            
            $ays_leadboard_count      = isset($data['ays_leadboard_count']) ? $data['ays_leadboard_count'] : '5';
            $ays_leadboard_width      = isset($data['ays_leadboard_width']) ? $data['ays_leadboard_width'] : '0';
            $ays_leadboard_orderby    = isset($data['ays_leadboard_orderby']) ? $data['ays_leadboard_orderby'] : 'id';
            $ays_leadboard_sort       = isset($data['ays_leadboard_sort']) ? $data['ays_leadboard_sort'] : 'avg';
            $ays_leadboard_color      = isset($data['ays_leadboard_color']) ? $data['ays_leadboard_color'] : '#99BB5A';
            $ays_leadboard_custom_css = isset($data['ays_leadboard_custom_css']) ? stripslashes($data['ays_leadboard_custom_css']) : '';
            $ays_leadboard_points_display = isset($data['ays_leadboard_points_display']) ? stripslashes($data['ays_leadboard_points_display']) : 'without_max_point';
            
            // Individual leadboard columns
            $ind_leadboard_columns = (isset( $data['ays_ind_leadboard_columns'] ) && !empty($data['ays_ind_leadboard_columns'])) ? $data['ays_ind_leadboard_columns'] : array();
            $ind_leadboard_columns_order = (isset( $data['ays_ind_leadboard_columns_order'] ) && !empty($data['ays_ind_leadboard_columns_order'])) ? $data['ays_ind_leadboard_columns_order'] : array();

            // Enable pagination
            $ind_leadboard_enable_pagination = (isset($data['ays_leadboard_enable_pagination']) && $data['ays_leadboard_enable_pagination'] == "on") ? "on" : "off";

            // Enable User Avatar
            $ind_leadboard_enable_user_avatar = (isset($data['ays_leadboard_enable_user_avatar']) && $data['ays_leadboard_enable_user_avatar'] == "on") ? "on" : "off";

            $ays_gleadboard_count     = isset($data['ays_gleadboard_count']) ? $data['ays_gleadboard_count'] : '5';
            $ays_gleadboard_width     = isset($data['ays_gleadboard_width']) ? $data['ays_gleadboard_width'] : '0';
            $ays_gleadboard_orderby   = isset($data['ays_gleadboard_orderby']) ? $data['ays_gleadboard_orderby'] : 'id';
            $ays_gleadboard_sort      = isset($data['ays_gleadboard_sort']) ? $data['ays_gleadboard_sort'] : 'avg';
            $ays_gleadboard_color     = isset($data['ays_gleadboard_color']) ? $data['ays_gleadboard_color'] : '#99BB5A';
            $ays_gleadboard_custom_css = isset($data['ays_gleadboard_custom_css']) ? stripslashes($data['ays_gleadboard_custom_css']) : '';

            // Global leadboard columns
            $glob_leadboard_columns = (isset( $data['ays_glob_leadboard_columns'] ) && !empty($data['ays_glob_leadboard_columns'])) ? $data['ays_glob_leadboard_columns'] : array();
            $glob_leadboard_columns_order = (isset( $data['ays_glob_leadboard_columns_order'] ) && !empty($data['ays_glob_leadboard_columns_order'])) ? $data['ays_glob_leadboard_columns_order'] : array();

            // Enable pagination
            $glob_leadboard_enable_pagination = (isset($data['ays_gleadboard_enable_pagination']) && $data['ays_gleadboard_enable_pagination'] == "on") ? "on" : "off";

            // Enable User Avatar
            $glob_leadboard_enable_user_avatar = (isset($data['ays_gleadboard_enable_user_avatar']) && $data['ays_gleadboard_enable_user_avatar'] == "on") ? "on" : "off";

            $ays_gleadboard_quiz_cat_count     = isset($data['ays_gleadboard_quiz_cat_count']) ? $data['ays_gleadboard_quiz_cat_count'] : '5';
            $ays_gleadboard_quiz_cat_width     = isset($data['ays_gleadboard_quiz_cat_width']) ? $data['ays_gleadboard_quiz_cat_width'] : '0';
            $ays_gleadboard_quiz_cat_orderby   = isset($data['ays_gleadboard_quiz_cat_orderby']) ? $data['ays_gleadboard_quiz_cat_orderby'] : 'id';
            $ays_gleadboard_quiz_cat_sort      = isset($data['ays_gleadboard_quiz_cat_sort']) ? $data['ays_gleadboard_quiz_cat_sort'] : 'avg';
            $ays_gleadboard_quiz_cat_color     = isset($data['ays_gleadboard_quiz_cat_color']) ? $data['ays_gleadboard_quiz_cat_color'] : '#99BB5A';
            $ays_gleadboard_quiz_cat_custom_css = isset($data['ays_gleadboard_quiz_cat_custom_css']) ? stripslashes($data['ays_gleadboard_quiz_cat_custom_css']) : '';

            // Global Quiz Category leadboard columns
            $glob_quiz_cat_leadboard_columns = (isset( $data['ays_glob_quiz_cat_leadboard_columns'] ) && !empty($data['ays_glob_quiz_cat_leadboard_columns'])) ? $data['ays_glob_quiz_cat_leadboard_columns'] : array();
            $glob_quiz_cat_leadboard_columns_order = (isset( $data['ays_glob_quiz_cat_leadboard_columns_order'] ) && !empty($data['ays_glob_quiz_cat_leadboard_columns_order'])) ? $data['ays_glob_quiz_cat_leadboard_columns_order'] : array();

            // Enable pagination
            $glob_quiz_cat_leadboard_enable_pagination = (isset($data['ays_gleadboard_quiz_cat_enable_pagination']) && $data['ays_gleadboard_quiz_cat_enable_pagination'] == "on") ? "on" : "off";

            // Enable User Avatar
            $glob_quiz_cat_leadboard_enable_user_avatar = (isset($data['ays_gleadboard_quiz_cat_enable_user_avatar']) && $data['ays_gleadboard_quiz_cat_enable_user_avatar'] == "on") ? "on" : "off";

            $leaderboard = array(                
                'individual' => array(
                    'count' => $ays_leadboard_count,
                    'width' => $ays_leadboard_width,
                    'orderby' => $ays_leadboard_orderby,
                    'sort' => $ays_leadboard_sort,
                    'color' => $ays_leadboard_color,
                    'leadboard_custom_css' => $ays_leadboard_custom_css,
                    'leadboard_points_display' => $ays_leadboard_points_display,

                    // Individual leadboard shortcode
                    "ind_leadboard_columns"         => $ind_leadboard_columns,
                    "ind_leadboard_columns_order"   => $ind_leadboard_columns_order,
                    "leadboard_enable_pagination"   => $ind_leadboard_enable_pagination,
                    "leadboard_enable_user_avatar"  => $ind_leadboard_enable_user_avatar,
                ),
                'global' => array(
                    'count' => $ays_gleadboard_count,
                    'width' => $ays_gleadboard_width,
                    'orderby' => $ays_gleadboard_orderby,
                    'sort' => $ays_gleadboard_sort,
                    'color' => $ays_gleadboard_color,
                    'gleadboard_custom_css' => $ays_gleadboard_custom_css,

                    // Global leadboard shortcode
                    "glob_leadboard_columns"        => $glob_leadboard_columns,
                    "glob_leadboard_columns_order"  => $glob_leadboard_columns_order,
                    "leadboard_enable_pagination"   => $glob_leadboard_enable_pagination,
                    "leadboard_enable_user_avatar"  => $glob_leadboard_enable_user_avatar,
                ),
                'global_quiz_cat' => array(
                    'count' => $ays_gleadboard_quiz_cat_count,
                    'width' => $ays_gleadboard_quiz_cat_width,
                    'orderby' => $ays_gleadboard_quiz_cat_orderby,
                    'sort' => $ays_gleadboard_quiz_cat_sort,
                    'color' => $ays_gleadboard_quiz_cat_color,
                    'gleadboard_custom_css' => $ays_gleadboard_quiz_cat_custom_css,

                    // Global Quiz Cat leadboard shortcode
                    "glob_quiz_cat_leadboard_columns"       => $glob_quiz_cat_leadboard_columns,
                    "glob_quiz_cat_leadboard_columns_order" => $glob_quiz_cat_leadboard_columns_order,
                    "leadboard_enable_pagination"           => $glob_quiz_cat_leadboard_enable_pagination,
                    "leadboard_enable_user_avatar"          => $glob_quiz_cat_leadboard_enable_user_avatar,
                )
            );

            $start_button           = (isset($data['ays_start_button']) && $data['ays_start_button'] != '') ? $data['ays_start_button'] : 'Start' ;
            $next_button            = (isset($data['ays_next_button']) && $data['ays_next_button'] != '') ? $data['ays_next_button'] : 'Next' ;
            $previous_button        = (isset($data['ays_previous_button']) && $data['ays_previous_button'] != '') ? $data['ays_previous_button'] : 'Prev' ;
            $clear_button           = (isset($data['ays_clear_button']) && $data['ays_clear_button'] != '') ? $data['ays_clear_button'] : 'Clear' ;
            $finish_button          = (isset($data['ays_finish_button']) && $data['ays_finish_button'] != '') ? $data['ays_finish_button'] : 'Finish' ;
            $see_result_button      = (isset($data['ays_see_result_button']) && $data['ays_see_result_button'] != '') ? $data['ays_see_result_button'] : 'See Result' ;
            $restart_quiz_button    = (isset($data['ays_restart_quiz_button']) && $data['ays_restart_quiz_button'] != '') ? $data['ays_restart_quiz_button'] : 'Restart quiz' ;
            $send_feedback_button   = (isset($data['ays_send_feedback_button']) && $data['ays_send_feedback_button'] != '') ? $data['ays_send_feedback_button'] : 'Send feedback' ;
            $load_more_button       = (isset($data['ays_load_more_button']) && $data['ays_load_more_button'] != '') ? $data['ays_load_more_button'] : 'Load more' ;
            $exit_button            = (isset($data['ays_exit_button']) && $data['ays_exit_button'] != '') ? $data['ays_exit_button'] : 'Exit' ;
            $check_button           = (isset($data['ays_check_button']) && $data['ays_check_button'] != '') ? $data['ays_check_button'] : 'Check' ;
            $login_button           = (isset($data['ays_login_button']) && $data['ays_login_button'] != '') ? $data['ays_login_button'] : 'Log In' ;

            $buttons_texts = array(
                'start_button'          => $start_button,
                'next_button'           => $next_button,
                'previous_button'       => $previous_button,
                'clear_button'          => $clear_button,
                'finish_button'         => $finish_button,
                'see_result_button'     => $see_result_button,
                'restart_quiz_button'   => $restart_quiz_button,
                'send_feedback_button'  => $send_feedback_button,
                'load_more_button'      => $load_more_button,
                'exit_button'           => $exit_button,
                'check_button'          => $check_button,
                'login_button'          => $login_button,
            );

            $quiz_fields_placeholder_name  = (isset($_REQUEST['ays_quiz_fields_placeholder_name']) && $_REQUEST['ays_quiz_fields_placeholder_name'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_name'] ) ) : 'Name' ;

            $quiz_fields_placeholder_eamil = (isset($_REQUEST['ays_quiz_fields_placeholder_eamil']) && $_REQUEST['ays_quiz_fields_placeholder_eamil'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_eamil'] ) ) : 'Email' ;

            $quiz_fields_placeholder_phone = (isset($_REQUEST['ays_quiz_fields_placeholder_phone']) && $_REQUEST['ays_quiz_fields_placeholder_phone'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_phone'] ) ) : 'Phone Number' ;

            $quiz_fields_label_name  = (isset($_REQUEST['ays_quiz_fields_label_name']) && $_REQUEST['ays_quiz_fields_label_name'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_name'] ) ) : 'Name' ;

            $quiz_fields_label_eamil = (isset($_REQUEST['ays_quiz_fields_label_eamil']) && $_REQUEST['ays_quiz_fields_label_eamil'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_eamil'] ) ) : 'Email' ;

            $quiz_fields_label_phone = (isset($_REQUEST['ays_quiz_fields_label_phone']) && $_REQUEST['ays_quiz_fields_label_phone'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_phone'] ) ) : 'Phone Number' ;

            $fields_placeholders = array(
                'quiz_fields_placeholder_name'   => $quiz_fields_placeholder_name,
                'quiz_fields_placeholder_eamil'  => $quiz_fields_placeholder_eamil,
                'quiz_fields_placeholder_phone'  => $quiz_fields_placeholder_phone,
                'quiz_fields_label_name'         => $quiz_fields_label_name,
                'quiz_fields_label_eamil'        => $quiz_fields_label_eamil,
                'quiz_fields_label_phone'        => $quiz_fields_label_phone,
            );
                        
            $question_default_type = isset($data['ays_question_default_type']) ? $data['ays_question_default_type'] : '';
            $question_default_cat = isset($data['ays_questions_default_cat']) ? $data['ays_questions_default_cat'] : '';
            $show_result_report = (isset( $data['ays_show_result_report'] ) && $data['ays_show_result_report'] == 'on') ? 'on' : 'off';
            $ays_answer_default_count = isset($data['ays_answer_default_count']) ? $data['ays_answer_default_count'] : '';
            $right_answer_sound = isset($data['ays_right_answer_sound']) ? $data['ays_right_answer_sound'] : '';
            $wrong_answer_sound = isset($data['ays_wrong_answer_sound']) ? $data['ays_wrong_answer_sound'] : '';

            // User page columns
            $user_page_columns = (isset( $data['ays_user_page_columns'] ) && !empty($data['ays_user_page_columns'])) ? $data['ays_user_page_columns'] : array();
            $user_page_columns_order = (isset( $data['ays_user_page_columns_order'] ) && !empty($data['ays_user_page_columns_order'])) ? $data['ays_user_page_columns_order'] : array();
            
            // All results column
            $all_results_columns = (isset($data['ays_all_results_columns']) && !empty($data['ays_all_results_columns'])) ? $data['ays_all_results_columns'] : array();
            $all_results_columns_order = (isset($data['ays_all_results_columns_order']) && !empty($data['ays_all_results_columns_order'])) ? $data['ays_all_results_columns_order'] : array();

            // Questions title length
            $question_title_length = (isset($data['ays_question_title_length']) && intval($data['ays_question_title_length']) != 0) ? absint(intval($data['ays_question_title_length'])) : 5;
            if($question_title_length == 0){
                $question_title_length = 5;
            }

            // Quizzes title length
            $quizzes_title_length = (isset($data['ays_quizzes_title_length']) && intval($data['ays_quizzes_title_length']) != 0) ? absint(intval($data['ays_quizzes_title_length'])) : 5;
            if($quizzes_title_length == 0){
                $quizzes_title_length = 5;
            }

            // Results title length
            $results_title_length = (isset($data['ays_results_title_length']) && intval($data['ays_results_title_length']) != 0) ? absint(intval($data['ays_results_title_length'])) : 5;
            if($results_title_length == 0){
                $results_title_length = 5;
            }

            // Question categories title length
            $question_categories_title_length = (isset($_REQUEST['ays_question_categories_title_length']) && intval($_REQUEST['ays_question_categories_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_question_categories_title_length'])) : 5;
            if($question_categories_title_length == 0){
                $question_categories_title_length = 5;
            }

            // Quiz categories title length
            $quiz_categories_title_length = (isset($_REQUEST['ays_quiz_categories_title_length']) && intval($_REQUEST['ays_quiz_categories_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_quiz_categories_title_length'])) : 5;

            // Reviews title length
            $quiz_reviews_title_length = (isset($_REQUEST['ays_quiz_reviews_title_length']) && intval($_REQUEST['ays_quiz_reviews_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_quiz_reviews_title_length'])) : 5;

            // Do not store IP adressess
            $disable_user_ip = (isset( $data['ays_disable_user_ip'] ) && $data['ays_disable_user_ip'] == 'on') ? 'on' : 'off';

            // Show publicly ( All Results )
            $all_results_show_publicly = (isset( $data['ays_all_results_show_publicly'] ) && $data['ays_all_results_show_publicly'] == 'on') ? 'on' : 'off';

            // Show publicly ( Single Quiz Results )
            $quiz_all_results_show_publicly = (isset( $data['ays_quiz_all_results_show_publicly'] ) && $data['ays_quiz_all_results_show_publicly'] == 'on') ? 'on' : 'off';

            // Keyword default count
            $keyword_default_max_value = (isset($data['ays_keyword_default_max_value']) && $data['ays_keyword_default_max_value'] != '') ? absint(intval($data['ays_keyword_default_max_value'])) : 6;

            // Animation Top
            $quiz_animation_top = (isset($data['ays_quiz_animation_top']) && $data['ays_quiz_animation_top'] != '') ? absint(intval($data['ays_quiz_animation_top'])) : 100;
            $quiz_enable_animation_top = (isset( $_REQUEST['ays_quiz_enable_animation_top'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_animation_top'] ) == 'on') ? 'on' : 'off';

            // Quiz All results column
            $quiz_all_results_columns = (isset($data['ays_quiz_all_results_columns']) && !empty($data['ays_quiz_all_results_columns'])) ? $data['ays_quiz_all_results_columns'] : array();
            $quiz_all_results_columns_order = (isset($data['ays_quiz_all_results_columns_order']) && !empty($data['ays_quiz_all_results_columns_order'])) ? $data['ays_quiz_all_results_columns_order'] : array();

            // Enable question allow HTML
            $quiz_enable_question_allow_html = (isset( $_REQUEST['ays_quiz_enable_question_allow_html'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_question_allow_html'] ) == 'on') ? 'on' : 'off';

            // Start button activation
            $enable_start_button_loader = (isset( $_REQUEST['ays_enable_start_button_loader'] ) && sanitize_text_field( $_REQUEST['ays_enable_start_button_loader'] ) == 'on') ? 'on' : 'off';

            // WP Editor height
            $quiz_wp_editor_height = (isset($_REQUEST['ays_quiz_wp_editor_height']) && $_REQUEST['ays_quiz_wp_editor_height'] != '' && $_REQUEST['ays_quiz_wp_editor_height'] != 0) ? absint( sanitize_text_field($_REQUEST['ays_quiz_wp_editor_height']) ) : 100;

            // Hide correct answer user page shortcode
            $hide_correct_answer = (isset( $_REQUEST['ays_quiz_hide_correct_answer_user_page'] ) && $_REQUEST['ays_quiz_hide_correct_answer_user_page'] == "on") ? "on" : "off";

            // Textarea height (public)
            $quiz_textarea_height = (isset($_REQUEST['ays_quiz_textarea_height']) && $_REQUEST['ays_quiz_textarea_height'] != '' && $_REQUEST['ays_quiz_textarea_height'] != 0 ) ? absint( sanitize_text_field($_REQUEST['ays_quiz_textarea_height']) ) : 100;

            // User roles to change quiz
            $user_roles_to_change_quiz = (isset($_REQUEST['ays_user_roles_to_change_quiz']) && !empty( $_REQUEST['ays_user_roles_to_change_quiz'] ) ) ? array_map( 'sanitize_text_field', $_REQUEST['ays_user_roles_to_change_quiz'] ) : array('administrator');

            // Show quiz button to Admins only
            $quiz_show_quiz_button_to_admin_only = (isset( $_REQUEST['ays_quiz_show_quiz_button_to_admin_only'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_quiz_button_to_admin_only'] ) == 'on') ? 'on' : 'off';

            // Flash Card Width
            $quiz_flash_card_width = (isset( $_REQUEST['ays_quiz_flash_card_width'] ) && $_REQUEST['ays_quiz_flash_card_width'] != '') ? sanitize_text_field( $_REQUEST['ays_quiz_flash_card_width'] ) : '';

            // Flash Card Color
            $quiz_flash_card_color = (isset( $_REQUEST['ays_quiz_flash_card_color'] ) && $_REQUEST['ays_quiz_flash_card_color'] != '') ? sanitize_text_field( $_REQUEST['ays_quiz_flash_card_color'] ) : '#ffffff';

            // Flash Card Randomize
            $quiz_flash_card_randomize = (isset( $_REQUEST['ays_quiz_flash_card_randomize'] ) && $_REQUEST['ays_quiz_flash_card_randomize'] == 'on') ? sanitize_text_field( $_REQUEST['ays_quiz_flash_card_randomize'] ) : 'off';

            //Flash Card Introduction Page
            $quiz_flash_card_enable_introduction = (isset( $_REQUEST['ays_enable_fc_introduction'] ) && $_REQUEST['ays_enable_fc_introduction'] == 'on') ? 'on' : 'off';
            $quiz_flash_card_introduction = (isset( $_REQUEST['ays_quiz_flash_card_introduction']) && $_REQUEST['ays_quiz_flash_card_introduction'] != '') ? htmlspecialchars( wp_unslash( $_REQUEST['ays_quiz_flash_card_introduction'] ) ) : '';

            /*
            ==========================================
            Result settings start
            ==========================================
            */

            // Store all not finished results
            $store_all_not_finished_results = (isset( $_REQUEST['ays_store_all_not_finished_results'] ) && $_REQUEST['ays_store_all_not_finished_results'] == 'on') ? sanitize_text_field( $_REQUEST['ays_store_all_not_finished_results'] ) : 'off';
            
            // Show information form only once
            $quiz_show_information_form_only_once = (isset( $_REQUEST['ays_quiz_show_information_form_only_once'] ) && $_REQUEST['ays_quiz_show_information_form_only_once'] == 'on') ? sanitize_text_field( $_REQUEST['ays_quiz_show_information_form_only_once'] ) : 'off';

            // Show Result Information
            $ays_quiz_show_result_info_user_ip = (isset( $_REQUEST['ays_quiz_show_result_info_user_ip'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_user_ip'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_user_id = (isset( $_REQUEST['ays_quiz_show_result_info_user_id'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_user_id'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_user = (isset( $_REQUEST['ays_quiz_show_result_info_user'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_user'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_admin_note = (isset( $_REQUEST['ays_quiz_show_result_info_admin_note'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_admin_note'] ) == 'on') ? 'on' : 'off';

            $ays_quiz_show_result_info_start_date = (isset( $_REQUEST['ays_quiz_show_result_info_start_date'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_start_date'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_duration = (isset( $_REQUEST['ays_quiz_show_result_info_duration'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_duration'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_score = (isset( $_REQUEST['ays_quiz_show_result_info_score'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_score'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_rate = (isset( $_REQUEST['ays_quiz_show_result_info_rate'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_rate'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_unique_code = (isset( $_REQUEST['ays_quiz_show_result_info_unique_code'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_unique_code'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_keywords = (isset( $_REQUEST['ays_quiz_show_result_info_keywords'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_keywords'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_res_by_cats = (isset( $_REQUEST['ays_quiz_show_result_info_res_by_cats'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_res_by_cats'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_coupon = (isset( $_REQUEST['ays_quiz_show_result_info_coupon'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_coupon'] ) == 'on') ? 'on' : 'off';
            $ays_quiz_show_result_info_certificate = (isset( $_REQUEST['ays_quiz_show_result_info_certificate'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_result_info_certificate'] ) == 'on') ? 'on' : 'off';

            /*
            ==========================================
            Result settings end
            ==========================================
            */

            // General CSS File
            $quiz_exclude_general_css = (isset( $_REQUEST['ays_quiz_exclude_general_css'] ) && sanitize_text_field( $_REQUEST['ays_quiz_exclude_general_css'] ) == 'on') ? 'on' : 'off';

            // Enable question answers
            $quiz_enable_question_answers = (isset( $_REQUEST['ays_quiz_enable_question_answers'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_question_answers'] ) == 'on') ? 'on' : 'off';

            // Quiz All orders column
            $quiz_all_orders_columns = (isset($_REQUEST['ays_quiz_all_orders_columns']) && !empty($_REQUEST['ays_quiz_all_orders_columns'])) ? $_REQUEST['ays_quiz_all_orders_columns'] : array();
            $quiz_all_orders_columns_order = (isset($_REQUEST['ays_quiz_all_orders_columns_order']) && !empty($_REQUEST['ays_quiz_all_orders_columns_order'])) ? $_REQUEST['ays_quiz_all_orders_columns_order'] : array();

            // Enable lazy loading attribute for images
            $quiz_enable_lazy_loading = (isset( $_REQUEST['ays_quiz_enable_lazy_loading'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_lazy_loading'] ) == 'on') ? 'on' : 'off';

            // Disable Quiz maker menu item notification
            $quiz_disable_quiz_menu_notification = (isset( $_REQUEST['ays_quiz_disable_quiz_menu_notification'] ) && sanitize_text_field( $_REQUEST['ays_quiz_disable_quiz_menu_notification'] ) == 'on') ? 'on' : 'off';

            // Disable Results menu item notification
            $quiz_disable_results_menu_notification = (isset( $_REQUEST['ays_quiz_disable_results_menu_notification'] ) && sanitize_text_field( $_REQUEST['ays_quiz_disable_results_menu_notification'] ) == 'on') ? 'on' : 'off';

            // Enable custom login form redirect if user fail
            $quiz_enable_custom_login_form_redirect = (isset( $_REQUEST['ays_quiz_enable_custom_login_form_redirect'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_custom_login_form_redirect'] ) == 'on') ? 'on' : 'off';

            // Custom login form link
            $quiz_custom_login_form_redirect_link = (isset( $_REQUEST['ays_quiz_custom_login_form_redirect_link'] ) && $_REQUEST['ays_quiz_custom_login_form_redirect_link'] != '') ? sanitize_url( $_REQUEST['ays_quiz_custom_login_form_redirect_link'] ) : '';

            $options = array(
                "question_default_type"                 => $question_default_type,
                "question_default_cat"                  => $question_default_cat,
                "ays_answer_default_count"              => $ays_answer_default_count,
                "right_answer_sound"                    => $right_answer_sound,
                "wrong_answer_sound"                    => $wrong_answer_sound,
                "question_title_length"                 => $question_title_length,
                "quizzes_title_length"                  => $quizzes_title_length,
                "results_title_length"                  => $results_title_length,
                "disable_user_ip"                       => $disable_user_ip,

                // User page shortcode
                "ays_show_result_report"                => $show_result_report,
                "user_page_columns"                     => $user_page_columns,
                "user_page_columns_order"               => $user_page_columns_order,
                "user_page_hide_answer"                 => $hide_correct_answer,
                
                // All results
                "all_results_columns"                   => $all_results_columns,
                "all_results_columns_order"             => $all_results_columns_order,
                "all_results_show_publicly"             => $all_results_show_publicly,
                "quiz_all_results_show_publicly"        => $quiz_all_results_show_publicly,

                // Quiz All results
                "quiz_all_results_columns"              => $quiz_all_results_columns,
                "quiz_all_results_columns_order"        => $quiz_all_results_columns_order,


                "keyword_default_max_value"             => $keyword_default_max_value,
                "quiz_animation_top"                    => $quiz_animation_top,
                "quiz_enable_animation_top"             => $quiz_enable_animation_top,

                "quiz_enable_question_allow_html"       => $quiz_enable_question_allow_html,
                "enable_start_button_loader"            => $enable_start_button_loader,
                "quiz_wp_editor_height"                 => $quiz_wp_editor_height,
                "quiz_textarea_height"                  => $quiz_textarea_height,

                "quiz_show_quiz_button_to_admin_only"   => $quiz_show_quiz_button_to_admin_only,
                "question_categories_title_length"      => $question_categories_title_length,
                "quiz_categories_title_length"          => $quiz_categories_title_length,
                "quiz_reviews_title_length"             => $quiz_reviews_title_length,

                // User roles options
                "user_roles_to_change_quiz"             => $user_roles_to_change_quiz,

                //Flash Cards
                "quiz_flash_card_width"                 => $quiz_flash_card_width,
                "quiz_flash_card_randomize"             => $quiz_flash_card_randomize,
                "quiz_flash_card_color"                 => $quiz_flash_card_color,
                "quiz_flash_card_enable_introduction"   => $quiz_flash_card_enable_introduction,
                "quiz_flash_card_introduction"          => $quiz_flash_card_introduction,

                // Result settings
                "store_all_not_finished_results"        => $store_all_not_finished_results,
                "quiz_show_information_form_only_once"  => $quiz_show_information_form_only_once,

                "quiz_exclude_general_css"              => $quiz_exclude_general_css,
                "quiz_enable_question_answers"          => $quiz_enable_question_answers,
                "quiz_enable_lazy_loading"              => $quiz_enable_lazy_loading,
                "quiz_disable_quiz_menu_notification"   => $quiz_disable_quiz_menu_notification,
                "quiz_disable_results_menu_notification" => $quiz_disable_results_menu_notification,

                // Quiz All orders
                "quiz_all_orders_columns"               => $quiz_all_orders_columns,
                "quiz_all_orders_columns_order"         => $quiz_all_orders_columns_order,

                // Show Result Information
                'ays_quiz_show_result_info_user_ip'     => $ays_quiz_show_result_info_user_ip,
                'ays_quiz_show_result_info_user_id'     => $ays_quiz_show_result_info_user_id,
                'ays_quiz_show_result_info_user'        => $ays_quiz_show_result_info_user,
                'ays_quiz_show_result_info_admin_note'  => $ays_quiz_show_result_info_admin_note,

                'ays_quiz_show_result_info_start_date'  => $ays_quiz_show_result_info_start_date,
                'ays_quiz_show_result_info_duration'    => $ays_quiz_show_result_info_duration,
                'ays_quiz_show_result_info_score'       => $ays_quiz_show_result_info_score,
                'ays_quiz_show_result_info_rate'        => $ays_quiz_show_result_info_rate,
                'ays_quiz_show_result_info_unique_code' => $ays_quiz_show_result_info_unique_code,
                'ays_quiz_show_result_info_keywords'    => $ays_quiz_show_result_info_keywords,
                'ays_quiz_show_result_info_res_by_cats' => $ays_quiz_show_result_info_res_by_cats,
                'ays_quiz_show_result_info_coupon'      => $ays_quiz_show_result_info_coupon,
                'ays_quiz_show_result_info_certificate' => $ays_quiz_show_result_info_certificate,

                'quiz_enable_custom_login_form_redirect'    => $quiz_enable_custom_login_form_redirect,
                'quiz_custom_login_form_redirect_link'      => $quiz_custom_login_form_redirect_link,
            );
            
//            $month_count = 10;
            $del_stat = "";
            $month_count = isset($data['ays_delete_results_by']) ? intval($data['ays_delete_results_by']) : null;
            if($month_count !== null && $month_count > 0){
                $year = intval( date( 'Y', current_time('timestamp') ) );
                $dt = intval( date( 'n', current_time('timestamp') ) );
                $month = $dt - $month_count;
                if($month < 0){
                    $month = 12 - $month;
                    if($month > 12){
                        $mn = $month % 12;
                        $mnac = ($month - $mn) / 12;
                        $month = 12 - ($mn);
                        $year -= $mnac;
                    }
                }elseif($month == 0){        
                    $month = 12;
                    $year--;
                }                
                $sql = "DELETE FROM " . $wpdb->prefix . "aysquiz_reports 
                        WHERE YEAR(end_date) = '". esc_sql( $year ) ."'
                          AND MONTH(end_date) <= '". esc_sql( $month ) ."'";
                $res = $wpdb->query($sql);
                if($res >= 0){
                    $del_stat = "&del_stat=ok&mcount=" . $month_count;
                }
            }
            
            $result = update_option(
                'ays_quiz_integrations',
                json_encode($paypal_options)
            );

            if($result){
                $success++;
            }

            $fields = array();

            $fields['user_roles'] = json_encode( $roles );
            $fields['mailchimp'] = json_encode( $mailchimp );
            $fields['monitor'] = json_encode( $monitor );
            $fields['slack'] = json_encode( $slack );
            $fields['active_camp'] = json_encode( $active_camp );
            $fields['zapier'] = json_encode( $zapier );
            $fields['stripe'] = json_encode( $stripe );
            $fields['leaderboard'] = json_encode( $leaderboard );
            $fields['buttons_texts'] = json_encode( $buttons_texts, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
            $fields['fields_placeholders'] = json_encode( $fields_placeholders, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
            $fields['options'] = json_encode( $options, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );

            $fields = apply_filters( 'ays_qm_settings_page_integrations_saves', $fields, $data );
            foreach ($fields as $key => $value) {
                $result = $this->ays_update_setting( $key, $value );
                if($result){
                    $success++;
                }
            }

            $fields = apply_filters( 'ays_qm_settings_page_extra_shortcodes_saves', $fields, $data );
            foreach ($fields as $key => $value) {
                $result = $this->ays_update_setting( $key, $value );
                if($result){
                    $success++;
                }
            }


            $message = "saved";
            if($success > 0){
                $tab = "";
                if(isset($data['ays_quiz_tab'])){
                    $tab = "&ays_quiz_tab=".$data['ays_quiz_tab'];
                }
                $url = admin_url('admin.php') . "?page=quiz-maker-settings" . $tab . '&status=' . $message . $del_stat;
                wp_redirect( $url );
            }
        }
        
    }

    public function get_data(){
        $data = get_option( "ays_quiz_integrations" );
        if($data == null || $data == ''){
            return array();
        }else{
            return json_decode( get_option( "ays_quiz_integrations" ), true );
        }
    }

    public function get_db_data(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT * FROM ".$settings_table;
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(count($results) > 0){
            return $results;
        }else{
            return array();
        }
    }    
    
    public function check_settings_meta($metas){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        foreach($metas as $meta_key){
            $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $this->ays_add_setting($meta_key, "", "", "");
            }
        }
        return false;
    }
    
    public function check_setting_user_roles(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = 'user_roles'";
        $result = $wpdb->get_var($sql);
        if(intval($result) == 0){
            $roles = json_encode(array('administrator'));
            $this->ays_add_setting("user_roles", $roles, "", "");
        }
        return false;
    }
        
    public function get_reports_titles(){
        global $wpdb;

        $sql = "SELECT {$wpdb->prefix}aysquiz_quizes.id,{$wpdb->prefix}aysquiz_quizes.title FROM {$wpdb->prefix}aysquiz_quizes";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }
    
    public static function ays_get_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";

        if($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table) {
            return false;
        }

        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
        $result = $wpdb->get_var($sql);
        if($result != ""){
            return $result;
        }
        return false;
    }
    
    public static function ays_add_setting($meta_key, $meta_value, $note = "", $options = ""){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $result = $wpdb->insert(
            $settings_table,
            array(
                'meta_key'    => $meta_key,
                'meta_value'  => $meta_value,
                'note'        => $note,
                'options'     => $options
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public static function ays_update_setting($meta_key, $meta_value, $note = null, $options = null){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $value = array(
            'meta_value'  => $meta_value,
        );
        $value_s = array( '%s' );
        if($note != null){
            $value['note'] = $note;
            $value_s[] = '%s';
        }
        if($options != null){
            $value['options'] = $options;
            $value_s[] = '%s';
        }
        $result = $wpdb->update(
            $settings_table,
            $value,
            array( 'meta_key' => $meta_key, ),
            $value_s,
            array( '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public function ays_delete_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $wpdb->delete(
            $settings_table,
            array( 'meta_key' => $meta_key ),
            array( '%s' )
        );
    }

    public function get_empty_duration_rows_count(){
        global $wpdb;
        $sql = "SELECT COUNT(*) AS c
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE (duration = '' OR duration IS NULL)";
        $result = $wpdb->get_var($sql);
        return intval($result);
    }

    public function update_duration_data(){
        global $wpdb;
        $sql = "UPDATE `{$wpdb->prefix}aysquiz_reports`
                SET `duration`= TIMESTAMPDIFF(SECOND, start_date, end_date)";
        $result = $wpdb->query($sql);
        if($result){
            $tab = "&ays_quiz_tab=tab3";
            $message = "duration_updated";
            $url = admin_url('admin.php') . "?page=quiz-maker-settings" . $tab . '&status=' . $message;
            wp_redirect( $url );
            exit;
        }
    }

    public function quiz_settings_notices($status){

        if ( empty( $status ) )
            return;

        if ( 'saved' == $status )
            $updated_message = esc_html( __( 'Changes saved.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz attribute .', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz attribute deleted.', $this->plugin_name ) );
        elseif ( 'duration_updated' == $status )
            $updated_message = esc_html( __( 'Duration old data is successfully updated.', $this->plugin_name ) );
        elseif ( 'gconnected' == $status )
            $updated_message = esc_html( __( 'Google Sheets account was successfully connected.', $this->plugin_name ) );
        elseif ( 'gdisconnected' == $status )
            $updated_message = esc_html( __( 'Google Sheets account was successfully disconnected.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
    
}
