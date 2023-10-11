<?php
if(isset($_GET['ays_quiz_tab'])){
    $ays_quiz_tab = esc_attr($_GET['ays_quiz_tab']);
}else{
    $ays_quiz_tab = 'tab1';
}
$action = (isset($_GET['action'])) ? sanitize_key($_GET['action']) : '';
$heading = '';
$loader_iamge = '';

$id = (isset($_GET['quiz'])) ? absint(intval($_GET['quiz'])) : null;

$user_id = get_current_user_id();
$user = get_userdata($user_id);

$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);

$quiz = array(
    'title' => '',
    'author_id' => $user_id,
    'description' => '',
    'quiz_image' => '',
    'quiz_category_id' => '1',
    'question_ids' => '',
    'create_date' => current_time( 'mysql' ),
    'published' => 1,
    'quiz_url'  => '',
);
$options = array(
    'color' => '#27AE60',
    'bg_color' => '#fff',
    'text_color' => '#000',
    'height' => 350,
    'width' => 400,
    'timer' => 100,
    'information_form' => 'disable',
    'form_name' => '',
    'form_email' => '',
    'form_phone' => '',
    'enable_logged_users' => '',
    'image_width' => '',
    'image_height' => '',
    'enable_correction' => '',
    'enable_questions_counter' => 'on',
    'limit_users' => '',
    'limitation_message' => '',
    'redirect_url' => '',
    'redirection_delay' => '',
    'enable_progress_bar' => '',
    'randomize_questions' => '',
    'randomize_answers' => '',
    'enable_questions_result' => '',
    'custom_css' => '',
    'enable_restriction_pass' => '',
    'restriction_pass_message' => '',
    'user_role' => '',
    'result_text' => '',
    'enable_result' => '',
    'enable_timer' => 'off',
    'enable_pass_count' => 'on',
    'enable_quiz_rate' => '',
    'enable_rate_avg' => '',
    'enable_rate_comments' => '',
    'hide_score' => 'off',
    'rate_form_title' => '',
    'enable_box_shadow' => 'on',
    'box_shadow_color' => '#000',
    'quiz_border_radius' => '0',
    'quiz_bg_image' => '',
    'enable_border' => '',
    'quiz_border_width' => '1',
    'quiz_border_style' => 'solid',
    'quiz_border_color' => '#000',
    'quiz_timer_in_title' => '',
    'enable_restart_button' => 'off',
    'quiz_loader' => 'default',
    'autofill_user_data' => 'off',
    'quest_animation' => 'shake',
    'enable_bg_music' => 'off',
    'quiz_bg_music' => '',
    'answers_font_size' => '15',
    'show_create_date' => 'off',
    'show_author' => 'off',
    'enable_early_finish' => 'off',
    'answers_rw_texts' => 'on_passing',
    'disable_store_data' => 'off',
    'enable_background_gradient' => 'off',
    'background_gradient_color_1' => '#000',
    'background_gradient_color_2' => '#fff',
    'quiz_gradient_direction' => 'vertical',
    'redirect_after_submit' => 'off',
    'submit_redirect_url' => '',
    'submit_redirect_delay' => '',
    'progress_bar_style' => 'first',
    'enable_exit_button' => 'off',
    'exit_redirect_url' => '',
    'image_sizing' => 'cover',
    'quiz_bg_image_position' => 'center center',
    'custom_class' => '',
    'enable_social_links' => 'off',
    'social_links' => array(
        'linkedin_link' => '',
        'facebook_link' => '',
        'twitter_link' => '',
        'vkontakte_link' => '',
        'instagram_link' => '',
        'youtube_link' => '',
        'behance_link' => '',
    ),
    'show_quiz_title' => 'on',
    'show_quiz_desc' => 'on',
    'show_login_form' => 'off',
    'mobile_max_width' => '',
    'limit_users_by' => 'ip',
	'explanation_time' => '4',
	'enable_clear_answer' => 'off',
	'show_category' => 'off',
	'show_question_category' => 'off',
    'display_score' => 'by_percentage',
    'enable_rw_asnwers_sounds' => 'off',
    'enable_enter_key' => 'on',
    'show_rate_after_rate' => 'on',
    'buttons_text_color' => '#333',
    'enable_audio_autoplay' => 'off',
    'enable_leave_page' => 'on',
    'show_only_wrong_answer' => 'off',
    'quiz_max_pass_count' => 1,
    'questions_hint_icon_or_text' => 'default',
    'questions_hint_value' => '',
    'progress_live_bar_style' => 'default',
    'show_questions_explanation' => 'on_results_page',
    'enable_questions_ordering_by_cat' => 'off',
    'enable_send_mail_to_user_by_pass_score' => 'off',
    'enable_send_mail_to_admin_by_pass_score' => 'off',
    'show_questions_numbering' => 'none',
    'show_answers_numbering' => 'none',
    'quiz_loader_custom_gif' => '',
    'disable_hover_effect' => 'off',
    'quiz_loader_custom_gif_width' => 100,
    'quiz_box_shadow_x_offset' => 0,
    'quiz_box_shadow_y_offset' => 0,
    'quiz_box_shadow_z_offset' => 15,
    'quiz_question_text_alignment' => 'center',
    'quiz_arrow_type' => 'default',
    'quiz_show_wrong_answers_first' => 'off',
    'quiz_display_all_questions' => 'off',
    'quiz_timer_red_warning' => 'off',
    'quiz_schedule_timezone' => get_option( 'timezone_string' ),
    'questions_hint_button_value' => '',
    'quiz_tackers_message' => __( "This quiz is expired!", $this->plugin_name ),
    'quiz_enable_linkedin_share_button' => 'on',
    'quiz_enable_facebook_share_button' => 'on',
    'quiz_enable_twitter_share_button' => 'on',
    'quiz_enable_vkontakte_share_button' => 'on',
    'quiz_make_responses_anonymous' => 'off',
    'quiz_make_all_review_link' => 'off',
    'quiz_message_before_timer' => '',
    'quiz_password_message' => '',
    'enable_see_result_confirm_box' => 'off',
    'display_fields_labels' => 'off',
    'social_buttons_heading' => '',
    'social_links_heading' => '',
    'quiz_enable_question_category_description' => 'off',
    'answers_margin' => '10',
    'quiz_message_before_redirect_timer' => '',
    'buttons_mobile_font_size' => 17,
    'quiz_answer_box_shadow_x_offset' => 0,
    'quiz_answer_box_shadow_y_offset' => 0,
    'quiz_answer_box_shadow_z_offset' => 10,
    'quiz_create_author' => $user_id,
    'quiz_enable_title_text_shadow' => "off",
    'quiz_title_text_shadow_color' => "#333",
    'right_answers_font_size' => "16",
    'wrong_answers_font_size' => "16",
    'quest_explanation_font_size' => "16",
    'quiz_waiting_time' => "off",
    'quiz_title_text_shadow_x_offset' => 2,
    'quiz_title_text_shadow_y_offset' => 2,
    'quiz_title_text_shadow_z_offset' => 2,
    'quiz_show_only_wrong_answers' => "off",
    'quiz_title_font_size' => 21,
    'quiz_title_mobile_font_size' => 21,
    'quiz_password_width' => "",
    'quiz_review_placeholder_text' => "",
    'quiz_make_review_required' => "off",
    'quiz_enable_results_toggle' => "off",
    'quiz_review_thank_you_message' => "",
    'quiz_review_enable_comment_field' => "on",
    'quest_explanation_mobile_font_size' => "16",
    'wrong_answers_mobile_font_size' => "16",
    'quiz_enable_question_image_zoom' => 'off',
    'right_answers_mobile_font_size' => '16',
    'quiz_display_messages_before_buttons' => 'off',
    'enable_question_reporting' => 'off',
    'quiz_enable_question_reporting_mail' => 'off',
    'quiz_enable_user_cհoosing_anonymous_assessment' => 'off',
    'note_text_font_size' => "14",
    'note_text_mobile_font_size' => "14",
    'quiz_questions_numbering_by_category' => "off",


    //Buttons Styles
    'buttons_size' => 'medium',
    'buttons_font_size' => '17',
    'buttons_left_right_padding' => '20',
    'buttons_top_bottom_padding' => '10',
    'buttons_border_radius' => '3',
    'buttons_width' => '',

    //Answers styles
    'answers_padding' => '5',
    'answers_border' => 'on',
    'answers_border_width' => '1',
    'answers_border_style' => 'solid',
    'answers_border_color' => '#444',
    'ans_img_height' => '150',
    'ans_img_caption_style' => 'outside',
    'ans_img_caption_position' => 'bottom',
    'answers_box_shadow' => 'off',
    'answers_box_shadow_color' => '#000',
    'show_answers_caption' => 'on',
    'answers_margin' => '10',
    'ans_right_wrong_icon' => 'default',
    'quiz_bg_img_in_finish_page' => 'off',
    'finish_after_wrong_answer' => 'off',
    'answers_object_fit' => 'cover',

    // Develpoer version options
    'enable_copy_protection' => '',
	'activeInterval' => '',
	'deactiveInterval' => '',
	'active_date_check' => 'off',
	'active_date_message' => __("The quiz has expired!", $this->plugin_name),
    'active_date_pre_start_message' => __("The quiz will be available soon!", $this->plugin_name),
    'checkbox_score_by' => 'on',
    'calculate_score' => 'by_correctness',
    'question_bank_type' => 'general',
    'enable_tackers_count' => 'off',
    'tackers_count' => '',
    'show_interval_message' => 'on',
    'allow_collecting_logged_in_users_data' => 'off',
    'quiz_pass_score' => 0,
    'make_questions_required' => 'off',
    'enable_password' => 'off',
    'password_quiz'   => '',
    'generate_password' => 'general',
    'display_score_by' => 'by_percentage',
    'show_schedule_timer' => 'off',
    'show_timer_type' => 'countdown',
    'enable_negative_mark' => 'off',
    'negative_mark_point' => 0,
    'enable_full_screen_mode' => 'off',
    'enable_navigation_bar' => 'off',
    'turn_on_extra_security_check' => 'on',
    'hide_limit_attempts_notice' => 'off',
    'quiz_enable_coupon' => 'off',
    'apply_points_to_keywords' => 'off',
    'quiz_enable_password_visibility' => 'off',
    'question_mobile_font_size' => 16,
    'answers_mobile_font_size' => 15,
    'limit_attempts_count_by_user_role' => '',
    'enable_autostart' => 'off',
    'quiz_enable_keyboard_navigation' => 'off',
    'question_count_per_page_type' => 'general',
    'quiz_timer_type' => 'quiz_timer',
    'quiz_pass_score_type' => 'percentage',
    'quiz_equal_keywords_text' => '',
    'enable_navigation_bar_marked_questions' => 'off',
    'quiz_question_text_to_speech' => 'off',
    'quiz_password_import_type' => 'default',


    // Integration option
    'enable_paypal' => '',
    'paypal_amount' => '',
    'paypal_currency' => '',
    'paypal_message' => __('You need to pay to pass this quiz.', $this->plugin_name),
    'enable_mailchimp' => '',
    'mailchimp_list' => '',
	'enable_monitor' => '',
	'monitor_list' => '',
	'enable_slack' => '',
	'slack_conversation' => '',
	'active_camp_list' => '',
	'active_camp_automation' => '',
	'enable_active_camp' => '',
    'enable_google_sheets' => '',
	'spreadsheet_id' => '',
    'payment_type' => 'prepay',

    // Email config options
    'send_results_user' => 'off', //AV
    'send_interval_msg' => 'off',
    'send_results_admin' => 'on',
    'send_interval_msg_to_admin' => 'off',
    'send_certificate_to_admin' => 'off',
    'use_subject_for_admin_email' => 'off',
    'additional_emails' => '',
    'email_config_from_email' => '',
    'email_config_from_name' => '',
    'email_config_from_subject' => '',
    'email_config_replyto_email' => '',
    'email_config_replyto_name' => '',
    'send_mail_to_site_admin' => 'on',

    'quiz_attributes' => array(),
    "certificate_title" => '<span style="font-size:50px; font-weight:bold">Certificate of Completion</span>',
    "certificate_body" => '<span style="font-size:25px"><i>This is to certify that</i></span><br><br>
            <span style="font-size:30px"><b>%%user_name%%</b></span><br/><br/>
            <span style="font-size:25px"><i>has completed the quiz</i></span><br/><br/>
            <span style="font-size:30px">"%%quiz_name%%"</span> <br/><br/>
            <span style="font-size:20px">with a score of <b>%%score%%</b></span><br/><br/>
            <span style="font-size:25px"><i>dated</i></span><br>
            <span style="font-size:30px">%%current_date%%</span><br/><br/><br/>',
    "certificate_image" => '',
    "certificate_frame" => 'default',
    "certificate_orientation" => 'l',

);
$question_ids = '';
$question_id_array = array();
$question_id_array_count = 0;
$quiz_intervals_defaults = array(
    array(
        'interval_min' => '0',
        'interval_max' => '25',
        'interval_text' => '',
        'interval_image' => '',
        'interval_keyword' => 'A',
        'interval_redirect_url' => '',
        'interval_redirect_delay' => '',
    ),
    array(
        'interval_min' => '26',
        'interval_max' => '50',
        'interval_text' => '',
        'interval_image' => '',
        'interval_keyword' => 'B',
        'interval_redirect_url' => '',
        'interval_redirect_delay' => '',
    ),
    array(
        'interval_min' => '51',
        'interval_max' => '75',
        'interval_text' => '',
        'interval_image' => '',
        'interval_keyword' => 'C',
        'interval_redirect_url' => '',
        'interval_redirect_delay' => '',
    ),
    array(
        'interval_min' => '76',
        'interval_max' => '100',
        'interval_text' => '',
        'interval_image' => '',
        'interval_keyword' => 'D',
        'interval_redirect_url' => '',
        'interval_redirect_delay' => '',
    ),
);

$quiz_top_keywords_defaults = array(
    array(
        'assign_top_keyword' => 'A',
        'assign_top_keyword_text' => '',
    ),
    array(
        'assign_top_keyword' => 'B',
        'assign_top_keyword_text' => '',
    ),
    array(
        'assign_top_keyword' => 'C',
        'assign_top_keyword_text' => '',
    ),
    array(
        'assign_top_keyword' => 'D',
        'assign_top_keyword_text' => '',
    ),
);

$quiz_intervals_numbers = 4;
$quiz_settings = $this->settings_obj;

$post_id = null;
$ays_quiz_view_post_url = "";
$ays_quiz_edit_post_url = "";
switch ($action) {
    case 'add':
        $heading = __('Add new quiz', $this->plugin_name);
        $quiz_intervals = $quiz_intervals_defaults;
        $quiz_top_keywords = $quiz_top_keywords_defaults;
        $quiz_default_options = ($quiz_settings->ays_get_setting('quiz_default_options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('quiz_default_options');
        if (! empty($quiz_default_options)) {
            $quiz_default_options = json_decode($quiz_default_options, true);
        }
        if (! empty($quiz_default_options)) {
            $options = $quiz_default_options;
        }
        if( ! isset( $options['certificate_title'] ) || $options['certificate_title'] == '' ){
            $options['certificate_title'] = '<span style="font-size:50px; font-weight:bold">Certificate of Completion</span>';
        }
        if( ! isset( $options['certificate_body'] ) || $options['certificate_body'] == '' ){
            $options['certificate_body'] = '<span style="font-size:25px"><i>This is to certify that</i></span><br><br>
                <span style="font-size:30px"><b>%%user_name%%</b></span><br/><br/>
                <span style="font-size:25px"><i>has completed the quiz</i></span><br/><br/>
                <span style="font-size:30px">"%%quiz_name%%"</span> <br/><br/>
                <span style="font-size:20px">with a score of <b>%%score%%</b></span><br/><br/>
                <span style="font-size:25px"><i>dated</i></span><br>
                <span style="font-size:30px">%%current_date%%</span><br/><br/><br/>';
        }
        break;
    case 'edit':
        $heading = __('Edit quiz', $this->plugin_name);
        $quiz = $this->quizes_obj->get_quiz_by_id($id);
        if (isset( $quiz['options'] ) && $quiz['options'] != "") {
            $options = json_decode($quiz['options'], true);
        }
        $question_ids = (isset( $quiz['question_ids'] ) && $quiz['question_ids'] != "") ? $quiz['question_ids'] : "";
        $question_id_array = explode(',', $question_ids);
        $question_id_array = ($question_id_array[0] == '' && count($question_id_array) == 1) ? array() : $question_id_array;

        $question_id_array_count_data = Quiz_Maker_Data::get_published_questions_count($question_ids);
        if ( !is_null( $question_id_array_count_data ) && !empty($question_id_array_count_data) ) {
            $question_id_array_count = (isset( $question_id_array_count_data['res_count'] ) && absint( $question_id_array_count_data['res_count'] ) > 0) ? absint( $question_id_array_count_data['res_count'] ) : count($question_id_array);
        }

        if (isset( $quiz['intervals'] ) && $quiz['intervals'] != "") {
            $quiz_intervals = json_decode($quiz['intervals'], true);
        } else {
            $quiz_intervals = $quiz_intervals_defaults;
        }
        $quiz_top_keywords = isset($options['assign_keywords']) && !empty($options['assign_keywords']) ? $options['assign_keywords'] : $quiz_top_keywords_defaults;
        $post_id = (isset( $quiz['post_id'] ) && $quiz['post_id'] != "") ? $quiz['post_id'] : null;
        if (!is_null( $post_id )) {
            $ays_quiz_view_post_url = get_permalink($post_id);
            $ays_quiz_edit_post_url = get_edit_post_link($post_id);
        }

        break;
}

$author_id =  (isset( $quiz['author_id'] ) && $quiz['author_id'] != "" && $quiz['author_id'] != 0) ? intval( $quiz['author_id'] ) : $user_id;
$owner = false;
if( $user_id == $author_id ){
    $owner = true;
}

if( $this->current_user_can_edit ){
    $owner = true;
}

if( !$owner ){
    $url = esc_url_raw( remove_query_arg( array('action', 'quiz') ) );
    wp_redirect( $url );
}

$disabled_option = '';
$readonly_option = '';
if( !$owner ){
    $disabled_option = ' disabled ';
    $readonly_option = ' readonly ';
}

$quiz_iframe_html = "";
if( !is_null( $id ) && intval($id) > 0 ){
    $iframe_attr = array(
        'id'    => $id,
        'embed' => true,
    );

    $ays_quiz_get_iframe = Quiz_Maker_iFrame::get_iframe( $id, $iframe_attr );
    $ays_quiz_get_iframe = str_replace(array("\r\n", "\n", "\r"), "", $ays_quiz_get_iframe);
    $ays_quiz_get_iframe = preg_replace('/\s+/', ' ', $ays_quiz_get_iframe);
        
    $quiz_iframe_html = "<textarea class='ays-quiz-embed-code-textarea display_none_imp' style='display:none;'>". $ays_quiz_get_iframe ."</textarea>";
}


$quiz_title = (isset( $quiz['title'] ) && $quiz['title'] != "") ? stripslashes( esc_attr($quiz['title']) ) : ""; 
$quiz_description = (isset( $quiz['description'] ) && $quiz['description'] != "") ? stripslashes(wpautop($quiz['description'])) : "";
$quiz_published = (isset( $quiz['published'] ) && $quiz['published'] != "") ? esc_attr( absint( $quiz['published'] ) ) : 1;
$quiz_category_id = (isset( $quiz['quiz_category_id'] ) && $quiz['quiz_category_id'] != "") ? absint($quiz['quiz_category_id']) : 1;

if( $action == 'edit' ) {
    $loader_iamge = '<span class="ays_quiz_loader_box" style="padding-left: 8px; display: inline-block;"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/loaders/loading.gif"></span>';
}else{
    $loader_iamge = '<span class="display_none ays_quiz_loader_box"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/loaders/loading.gif"></span>';
}

$quiz_intervals = ($quiz_intervals == null) ? $quiz_intervals_defaults : $quiz_intervals;
$quiz_top_keywords = ($quiz_top_keywords == null) ? $quiz_top_keywords_defaults : $quiz_top_keywords;
// $questions = $this->quizes_obj->get_published_questions();
$total_questions_count = $this->quizes_obj->published_questions_record_count();
$quiz_categories = $this->quizes_obj->get_quiz_categories();
$question_categories = $this->get_questions_categories();
$question_categories_array = array();
foreach($question_categories as $cat){
    $question_categories_array[$cat['id']] = $cat['title'];
}

$question_tags = $this->get_questions_tags();
$question_tags_array = array();
foreach($question_tags as $tag){
    $question_tags_array[$tag['id']] = $tag['title'];
}

$used_questions = $this->get_published_questions_used();
$question_bank_categories = $this->quizes_obj->get_question_bank_categories($question_ids);

$settings_options = $this->settings_obj->ays_get_setting('options');
if($settings_options){
    $settings_options = json_decode(stripcslashes($settings_options), true);
}else{
    $settings_options = array();
}
$right_answer_sound = (isset($settings_options['right_answer_sound']) && $settings_options['right_answer_sound'] != '') ? true : false;
$wrong_answer_sound = (isset($settings_options['wrong_answer_sound']) && $settings_options['wrong_answer_sound'] != '') ? true : false;
$rw_answers_sounds_status = false;
if($right_answer_sound && $wrong_answer_sound){
    $rw_answers_sounds_status = true;
}

// WP Editor height
$quiz_wp_editor_height = (isset($settings_options['quiz_wp_editor_height']) && $settings_options['quiz_wp_editor_height'] != '') ? absint( sanitize_text_field($settings_options['quiz_wp_editor_height']) ) : 100;


$quiz_integrations = (get_option( 'ays_quiz_integrations' ) != null) ? json_decode( get_option( 'ays_quiz_integrations' ), true ) : array();
$quiz_paypal = array(
    'state' => (isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '') ? true : false,
    'clientId' => isset($quiz_integrations['paypal_client_id']) ? $quiz_integrations['paypal_client_id'] : null,
);

$stripe_res         = ($quiz_settings->ays_get_setting('stripe') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('stripe');
$stripe             = json_decode($stripe_res, true);
$stripe_secret_key  = isset($stripe['secret_key']) && $stripe['secret_key'] != '' ? $stripe['secret_key'] : '';
$stripe_api_key     = isset($stripe['api_key']) && $stripe['api_key'] != '' ? $stripe['api_key'] : '';
$is_enabled_stripe  = $stripe_api_key != '' && $stripe_secret_key != '' ? true : false;

$mailchimp_res = ($quiz_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mailchimp');
$mailchimp = json_decode($mailchimp_res, true);
$mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
$mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
$mailchimp_lists = $this->ays_get_mailchimp_lists($mailchimp_username, $mailchimp_api_key);

$mailchimp_select = array();
if(isset($mailchimp_lists['total_items']) && $mailchimp_lists['total_items'] > 0){
    if (isset($mailchimp_lists['lists'])) {
        foreach($mailchimp_lists['lists'] as $list){
            $mailchimp_select[] = array(
                'listId' => $list['id'],
                'listName' => $list['name']
            );
        }
    }
}else{
    $mailchimp_select = __( "There are no lists", $this->plugin_name );
}

$monitor_res     = ($quiz_settings->ays_get_setting('monitor') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('monitor');
$monitor         = json_decode($monitor_res, true);
$monitor_client  = isset($monitor['client']) ? $monitor['client'] : '';
$monitor_api_key = isset($monitor['apiKey']) ? $monitor['apiKey'] : '';
$monitor_lists   = $this->ays_get_monitor_lists($monitor_client, $monitor_api_key);
$monitor_select  = !isset($monitor_lists['Code']) ? $monitor_lists : __("There are no lists", $this->plugin_name);


$slack_res           = ($quiz_settings->ays_get_setting('slack') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('slack');
$slack               = json_decode($slack_res, true);
$slack_client        = isset($slack['client']) ? $slack['client'] : '';
$slack_secret        = isset($slack['secret']) ? $slack['secret'] : '';
$slack_token         = isset($slack['token']) ? $slack['token'] : '';
$slack_conversations = $this->ays_get_slack_conversations($slack_token);
$slack_select        = !isset($slack_conversations['Code']) ? $slack_conversations : __("There are no conversations", $this->plugin_name);

$active_camp_res               = ($quiz_settings->ays_get_setting('active_camp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('active_camp');
$active_camp                   = json_decode($active_camp_res, true);
$active_camp_url               = isset($active_camp['url']) ? $active_camp['url'] : '';
$active_camp_api_key           = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';
$active_camp_lists             = $this->ays_get_active_camp_data('lists', $active_camp_url, $active_camp_api_key);
$active_camp_automations       = $this->ays_get_active_camp_data('automations', $active_camp_url, $active_camp_api_key);
$active_camp_list_select       = ( !isset($active_camp_lists['Code']) && isset( $active_camp_lists['lists'] ) ) ? $active_camp_lists['lists'] : __("There are no lists", $this->plugin_name);
$active_camp_automation_select = ( !isset($active_camp_automations['Code']) && isset( $active_camp_automations['automations'] ) ) ? $active_camp_automations['automations'] : __("There are no automations", $this->plugin_name);

$zapier_res  = ($quiz_settings->ays_get_setting('zapier') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('zapier');
$zapier      = json_decode($zapier_res, true);
$zapier_hook = isset($zapier['hook']) ? $zapier['hook'] : '';

// Google Sheets
$google_res           = ($quiz_settings->ays_get_setting('google') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('google');
$google               = json_decode($google_res, true);
$google_client        = isset($google['client']) ? $google['client'] : '';
$google_secret        = isset($google['secret']) ? $google['secret'] : '';
$google_token         = isset($google['token']) ? $google['token'] : '';
$google_refresh_token = isset($google['refresh_token']) ? $google['refresh_token'] : '';

if (isset($_POST['ays_submit']) || isset($_POST['ays_submit_top'])) {
    $_POST['id'] = $id;
    $this->quizes_obj->add_or_edit_quizes($_POST);
}
if (isset($_POST['ays_apply_top']) || isset($_POST['ays_apply'])) {
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->quizes_obj->add_or_edit_quizes($_POST);
}
if (isset($_POST['ays_default'])) {
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $_POST['ays_default_option'] = 'ays_default_option';
    $this->quizes_obj->add_or_edit_quizes($_POST);
}

$next_quiz_id = "";
$prev_quiz_id = "";
if ( isset( $id ) && !is_null( $id ) ) {
    $next_quiz = $this->get_next_or_prev_row_by_id( $id, "next", "aysquiz_quizes" );
    $next_quiz_id = (isset( $next_quiz['id'] ) && $next_quiz['id'] != "") ? absint( $next_quiz['id'] ) : null;

    $prev_quiz = $this->get_next_or_prev_row_by_id( $id, "prev", "aysquiz_quizes" );
    $prev_quiz_id = (isset( $prev_quiz['id'] ) && $prev_quiz['id'] != "") ? absint( $prev_quiz['id'] ) : null;
}

$ays_user = wp_get_current_user();
$ays_super_admin_email = get_option('admin_email'); //$user->data->user_email;
$wp_general_settings_url = admin_url( 'options-general.php' );

$quiz_message_vars = array(
    "%%user_name%%"                         => __("User Name", $this->plugin_name),
    "%%user_email%%"                        => __("User Email", $this->plugin_name),
    "%%user_phone%%"                        => __("User Phone", $this->plugin_name),
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%score%%"                             => __("Score", $this->plugin_name),
    "%%user_points%%"                       => __("User Point", $this->plugin_name),
    "%%user_corrects_count%%"               => __("Correct answers count", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%max_points%%"                        => __("Max point", $this->plugin_name),
    "%%current_date%%"                      => __("Current Date", $this->plugin_name),
    "%%quiz_logo%%"                         => __("Quiz Image", $this->plugin_name),
    "%%interval_message%%"                  => __("Interval message", $this->plugin_name),
    "%%avg_score%%"                         => __("Average score", $this->plugin_name),
    "%%avg_rate%%"                          => __("Average Rate", $this->plugin_name),
    "%%user_pass_time%%"                    => __("User passed time", $this->plugin_name),
    "%%quiz_time%%"                         => __("Quiz time", $this->plugin_name),
    "%%results_by_cats%%"                   => __("Results by question categories", $this->plugin_name),
    "%%unique_code%%"                       => __("Unique code", $this->plugin_name),
    "%%download_certificate%%"              => __("Download certificate button", $this->plugin_name),
    "%%wrong_answers_count%%"               => __("Wrong answers count (skipped questions are included)", $this->plugin_name),
    "%%only_wrong_answers_count%%"          => __("Only wrong answers count", $this->plugin_name),
    "%%avg_score_by_category%%"             => __("Average score by the question category", $this->plugin_name),
    "%%skipped_questions_count%%"           => __("Unanswered questions count", $this->plugin_name),
    "%%answered_questions_count%%"          => __("Answered questions count", $this->plugin_name),
    "%%score_by_answered_questions%%"       => __("Score by answered questions", $this->plugin_name),
    "%%user_first_name%%"                   => __("User's First Name", $this->plugin_name),
    "%%user_last_name%%"                    => __("User's Last Name", $this->plugin_name),
    "%%user_nickname%%"                     => __("User's Nick Name", $this->plugin_name),
    "%%user_display_name%%"                 => __("User's Display Name", $this->plugin_name),
    "%%keyword_count_{keyword}%%"           => __("Keywords count", $this->plugin_name),
    "%%keyword_percentage_{keyword}%%"      => __("Keywords percentage", $this->plugin_name),
    "%%top_keywords_count_{count}%%"        => __("Top keywords count", $this->plugin_name),
    "%%top_keywords_percentage_{count}%%"   => __("Top keywords percentage", $this->plugin_name),
    "%%quiz_coupon%%"                       => __("Quiz coupon", $this->plugin_name),
    "%%user_wordpress_email%%"              => __("User's WordPress profile email", $this->plugin_name),
    "%%user_wordpress_roles%%"              => __("User's Wordpress Roles", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_quiz_page_link%%"            => __("Quiz page link", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),
    "%%user_keyword_point_{keyword}%%"      => __("User keyword point", $this->plugin_name),
    "%%max_point_keyword_{keyword}%%"       => __("Max point keyword", $this->plugin_name),
    "%%user_keyword_percentage_{keyword}%%" => __("User keyword percentage", $this->plugin_name),
    "%%avg_user_points%%"                   => __("AVG User points", $this->plugin_name),
    "%%avg_res_by_cats%%"                   => __("AVG results by cats", $this->plugin_name),
    "%%results_by_tags%%"                   => __("Results by question tags", $this->plugin_name),
    "%%personality_result_by_question_ids_{CatID_1,CatID_2,CatID_3,CatID_4}%%"  => __("Personality result by question ids ", $this->plugin_name),
);

$quiz_message_vars_timer = array(
    "%%time%%"                              => __("Time", $this->plugin_name),
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%user_first_name%%"                   => __("User's First Name", $this->plugin_name),
    "%%user_last_name%%"                    => __("User's Last Name", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%user_nickname%%"                     => __("User's Nick Name", $this->plugin_name),
    "%%user_display_name%%"                 => __("User's Display Name", $this->plugin_name),
    "%%user_wordpress_email%%"              => __("User's WordPress profile email", $this->plugin_name),
    "%%user_wordpress_roles%%"              => __("User's Wordpress Roles", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),

);

$quiz_message_vars_information_form = array(
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%user_first_name%%"                   => __("User's First Name", $this->plugin_name),
    "%%user_last_name%%"                    => __("User's Last Name", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%user_nickname%%"                     => __("User's Nick Name", $this->plugin_name),
    "%%user_display_name%%"                 => __("User's Display Name", $this->plugin_name),
    "%%user_wordpress_email%%"              => __("User's WordPress profile email", $this->plugin_name),
    "%%user_wordpress_roles%%"              => __("User's Wordpress Roles", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),

);

$quiz_message_vars_description = array(
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%user_first_name%%"                   => __("User's First Name", $this->plugin_name),
    "%%user_last_name%%"                    => __("User's Last Name", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%user_nickname%%"                     => __("User's Nick Name", $this->plugin_name),
    "%%user_display_name%%"                 => __("User's Display Name", $this->plugin_name),
    "%%user_wordpress_email%%"              => __("User's WordPress profile email", $this->plugin_name),
    "%%user_wordpress_roles%%"              => __("User's Wordpress Roles", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),
);

$quiz_message_vars_limitation_message = array(
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%user_first_name%%"                   => __("User's First Name", $this->plugin_name),
    "%%user_last_name%%"                    => __("User's Last Name", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%user_nickname%%"                     => __("User's Nick Name", $this->plugin_name),
    "%%user_display_name%%"                 => __("User's Display Name", $this->plugin_name),
    "%%user_wordpress_email%%"              => __("User's WordPress profile email", $this->plugin_name),
    "%%user_wordpress_roles%%"              => __("User's Wordpress Roles", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),
);

$quiz_message_vars_logged_in_users = array(
    "%%quiz_name%%"                         => __("Quiz Title", $this->plugin_name),
    "%%questions_count%%"                   => __("Questions count", $this->plugin_name),
    "%%quiz_creation_date%%"                => __("Quiz creation date", $this->plugin_name),
    "%%current_quiz_author%%"               => __("Quiz Author", $this->plugin_name),
    "%%current_user_ip%%"                   => __("User's IP Address", $this->plugin_name),
    "%%current_quiz_author_email%%"         => __("Quiz Author Email", $this->plugin_name),
    "%%admin_email%%"                       => __("Admin Email", $this->plugin_name),
);


$quiz_message_vars_html                     = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars );
$quiz_message_vars_timer_html               = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars_timer );
$quiz_message_vars_information_form_html    = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars_information_form );
$quiz_message_vars_description_html         = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars_description );
$quiz_message_vars_limitation_message_html  = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars_limitation_message );
$quiz_message_vars_logged_in_users_html     = Quiz_Maker_Data::ays_quiz_generate_message_vars_html( $quiz_message_vars_logged_in_users );

$style = null;
$image_text = __('Add Image', $this->plugin_name);
$bg_image_text = __('Add Image', $this->plugin_name);

$quiz_image = (isset( $quiz['quiz_image']  ) && $quiz['quiz_image'] != '') ? $quiz['quiz_image'] : "";

if ($quiz_image != '') {
    $style = "display: block;";
    $image_text = __('Edit Image', $this->plugin_name);
}

$get_all_quizzes = Quiz_Maker_Data::ays_quiz_ays_quiz_get_quizzes();

global $wp_roles;
global $wpdb;
$ays_users_roles = $wp_roles->roles;
$ays_users_search = array();

if( isset( $options['ays_users_search'] ) && !empty( $options['ays_users_search'] ) ){

    $users_table = esc_sql( $wpdb->prefix . 'users' );

    $quiz_user_ids = implode( ",", $options['ays_users_search'] );

    $sql_users = "SELECT ID,display_name FROM {$users_table} WHERE ID IN (". $quiz_user_ids .")";

    $ays_users_search = $wpdb->get_results($sql_users, "ARRAY_A");
}

$all_attributes = $this->quizes_obj->get_al_attributes();
$quiz_attributes = (isset($options['quiz_attributes'])) ? $options['quiz_attributes'] : array();
$required_fields = (isset($options['required_fields'])) ? $options['required_fields'] : array();
$quiz_attributes_active_order = (isset($options['quiz_attributes_active_order'])) ? $options['quiz_attributes_active_order'] : array();
$quiz_attributes_passive_order = (isset($options['quiz_attributes_passive_order'])) ? $options['quiz_attributes_passive_order'] : array();
$default_attributes = array("ays_form_name", "ays_form_email", "ays_form_phone");
$quiz_attributes_checked = array();
$quiz_form_attrs = array();

if(isset($options['form_name']) && $options['form_name'] == 'on'){
    $quiz_attributes_checked[] = "ays_form_name";
}
if(isset($options['form_email']) && $options['form_email'] == 'on'){
    $quiz_attributes_checked[] = "ays_form_email";
}
if(isset($options['form_phone']) && $options['form_phone'] == 'on'){
    $quiz_attributes_checked[] = "ays_form_phone";
}

$quiz_form_attrs[] = array(
    "id" => null,
    "slug" => "ays_form_name",
    "name" => __( "Name", $this->plugin_name ),
    "type" => 'text'
);
$quiz_form_attrs[] = array(
    "id" => null,
    "slug" => "ays_form_email",
    "name" => __( "Email", $this->plugin_name ),
    "type" => 'email'
);
$quiz_form_attrs[] = array(
    "id" => null,
    "slug" => "ays_form_phone",
    "name" => __( "Phone", $this->plugin_name ),
    "type" => 'text'
);

$all_attributes = array_merge($quiz_form_attrs, $all_attributes);
$custom_fields = array();
foreach($all_attributes as $key => $attr){
    $attr_checked = in_array(strval($attr['id']), $quiz_attributes) ? 'checked' : '';
    $attr_required = in_array(strval($attr['id']), $required_fields) ? 'checked' : '';
    if(in_array($attr['slug'], $quiz_attributes_checked)){
        $attr_checked = 'checked';
    }
    if(in_array($attr['slug'], $required_fields)){
        $attr_required = 'checked';
    }
    $custom_fields[$attr['slug']] = array(
        'id' => $attr['id'],
        'name' => $attr['name'],
        'type' => $attr['type'],
        'slug' => $attr['slug'],
        'checked' => $attr_checked,
        'required' => $attr_required,
    );
}


$custom_fields_active = array();
$custom_fields_passive = array();
foreach($custom_fields as $key => $attr){
    if($attr['checked'] == 'checked'){
        $custom_fields_active[$attr['slug']] = $attr;
    }else{
        $custom_fields_passive[$attr['slug']] = $attr;
    }
}

uksort($custom_fields_active, function($key1, $key2) use ($quiz_attributes_active_order) {
	return ((array_search($key1, $quiz_attributes_active_order) > array_search($key2, $quiz_attributes_active_order)) ? 1 : -1);
});
uksort($custom_fields_passive, function($key1, $key2) use ($quiz_attributes_passive_order) {
	return ((array_search($key1, $quiz_attributes_passive_order) > array_search($key2, $quiz_attributes_passive_order)) ? 1 : -1);
});

$enable_pass_count = (isset($options['enable_pass_count'])) ? $options['enable_pass_count'] : '';
$enable_timer = (isset($options['enable_timer'])) ? $options['enable_timer'] : 'off';
$enable_quiz_rate = (isset($options['enable_quiz_rate'])) ? $options['enable_quiz_rate'] : '';
$enable_rate_avg = (isset($options['enable_rate_avg'])) ? $options['enable_rate_avg'] : '';
$enable_rate_comments = (isset($options['enable_rate_comments'])) ? $options['enable_rate_comments'] : '';
$enable_copy_protection = (isset($options['enable_copy_protection'])) ? $options['enable_copy_protection'] : '';

// Paypal
$enable_paypal = (isset($options['enable_paypal'])) ? $options['enable_paypal'] : '';
$paypal_amount = (isset($options['paypal_amount'])) ? $options['paypal_amount'] : '';
$paypal_currency = (isset($options['paypal_currency'])) ? $options['paypal_currency'] : '';
$paypal_message = (isset($options['paypal_message'])) ? $options['paypal_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
$paypal_message = stripslashes( $paypal_message );

// Stripe
$options['enable_stripe'] = !isset( $options['enable_stripe'] ) ? 'off' : $options['enable_stripe'];
$enable_stripe = ( isset($options['enable_stripe']) && $options['enable_stripe'] == 'on' ) ? true : false;
$stripe_amount = (isset($options['stripe_amount'])) ? $options['stripe_amount'] : '';
$stripe_currency = (isset($options['stripe_currency'])) ? $options['stripe_currency'] : '';
$stripe_message = (isset($options['stripe_message'])) ? $options['stripe_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
$stripe_message = stripslashes( $stripe_message );

// Paypal And Stripe Paymant type
$payment_type = (isset($options['payment_type']) && sanitize_text_field( $options['payment_type'] ) != '') ? sanitize_text_field( esc_attr( $options['payment_type']) ) : 'prepay';

// MailChimp
$enable_mailchimp = (isset($options['enable_mailchimp']) && $options['enable_mailchimp'] == 'on') ? true : false;
$mailchimp_list = (isset($options['mailchimp_list'])) ? $options['mailchimp_list'] : '';
$enable_double_opt_in = (isset($options['enable_double_opt_in']) && $options['enable_double_opt_in'] == 'on') ? true : false;

// Campaign Monitor
$enable_monitor = (isset($options['enable_monitor']) && $options['enable_monitor'] == 'on') ? true : false;
$monitor_list   = (isset($options['monitor_list'])) ? $options['monitor_list'] : '';

// Slack
$enable_slack       = (isset($options['enable_slack']) && $options['enable_slack'] == 'on') ? true : false;
$slack_conversation = (isset($options['slack_conversation'])) ? $options['slack_conversation'] : '';

// ActiveCampaign
$enable_active_camp     = (isset($options['enable_active_camp']) && $options['enable_active_camp'] == 'on') ? true : false;
$active_camp_list       = (isset($options['active_camp_list'])) ? $options['active_camp_list'] : '';
$active_camp_automation = (isset($options['active_camp_automation'])) ? $options['active_camp_automation'] : '';

// Zapier
$enable_zapier = (isset($options['enable_zapier']) && $options['enable_zapier'] == 'on') ? true : false;

// Google Sheets
$enable_google_sheets       = (isset($options['enable_google_sheets']) && $options['enable_google_sheets'] == 'on') ? true : false;

$enable_box_shadow = (!isset($options['enable_box_shadow'])) ? 'on' : $options['enable_box_shadow'];
$box_shadow_color = (!isset($options['box_shadow_color'])) ? '#000' : esc_attr( stripslashes($options['box_shadow_color']) );
$quiz_border_radius = (isset($options['quiz_border_radius']) && $options['quiz_border_radius'] != '') ? $options['quiz_border_radius'] : '0';
$quiz_bg_image = (isset($options['quiz_bg_image']) && $options['quiz_bg_image'] != '') ? $options['quiz_bg_image'] : '';
$enable_border = (isset($options['enable_border']) && $options['enable_border'] == 'on') ? true : false;
$quiz_border_width = (isset($options['quiz_border_width']) && $options['quiz_border_width'] != '') ? $options['quiz_border_width'] : '1';
$quiz_border_style = (isset($options['quiz_border_style']) && $options['quiz_border_style'] != '') ? $options['quiz_border_style'] : 'solid';
$quiz_border_color = (isset($options['quiz_border_color']) && $options['quiz_border_color'] != '') ? esc_attr( stripslashes($options['quiz_border_color']) ) : '#000';
$quiz_timer_in_title = (isset($options['quiz_timer_in_title']) && $options['quiz_timer_in_title'] == 'on') ? true : false;
$enable_restart_button = (isset($options['enable_restart_button']) && $options['enable_restart_button'] == 'on') ? true : false;

$rate_form_title = (isset($options['rate_form_title'])) ? $options['rate_form_title'] : __('Please click the stars to rate the quiz', $this->plugin_name);
$quiz_loader = (isset($options['quiz_loader']) && $options['quiz_loader'] != '') ? $options['quiz_loader'] : 'default';

$quiz_create_date = (isset($quiz['create_date']) && $quiz['create_date'] != '') ? $quiz['create_date'] : "0000-00-00 00:00:00";

$main_quiz_url = (isset($quiz['quiz_url']) && esc_url($quiz['quiz_url']) != '') ? esc_url($quiz['quiz_url']) : '';

$autofill_user_data = (isset($options['autofill_user_data']) && $options['autofill_user_data'] == 'on') ? true : false;

$quest_animation = (isset($options['quest_animation'])) ? $options['quest_animation'] : "shake";
$enable_bg_music = (isset($options['enable_bg_music']) && $options['enable_bg_music'] == "on") ? true : false;
$quiz_bg_music = (isset($options['quiz_bg_music']) && $options['quiz_bg_music'] != "") ? $options['quiz_bg_music'] : "";
$answers_font_size = (isset($options['answers_font_size']) && $options['answers_font_size'] != "" && absint( esc_attr( $options['answers_font_size'] ) ) > 0) ? absint( esc_attr( $options['answers_font_size'] ) ) : '15';

// Strong calculation of checkbox answers
$options['checkbox_score_by'] = ! isset($options['checkbox_score_by']) ? 'on' : $options['checkbox_score_by'];
$checkbox_score_by = (isset($options['checkbox_score_by']) && $options['checkbox_score_by'] == "on") ? true : false;

$show_create_date = (isset($options['show_create_date']) && $options['show_create_date'] == "on") ? true : false;
$show_author = (isset($options['show_author']) && $options['show_author'] == "on") ? true : false;
$enable_early_finish = (isset($options['enable_early_finish']) && $options['enable_early_finish'] == "on") ? true : false;
$answers_rw_texts = (isset($options['answers_rw_texts']) && $options['answers_rw_texts'] != '') ? $options['answers_rw_texts'] : 'on_passing';
$disable_store_data = (isset($options['disable_store_data']) && $options['disable_store_data'] == 'on') ? true : false;


// Background gradient
$options['enable_background_gradient'] = (!isset($options['enable_background_gradient'])) ? 'off' : $options['enable_background_gradient'];
$enable_background_gradient = (isset($options['enable_background_gradient']) && $options['enable_background_gradient'] == 'on') ? true : false;
$background_gradient_color_1 = (isset($options['background_gradient_color_1']) && $options['background_gradient_color_1'] != '') ? esc_attr( stripslashes($options['background_gradient_color_1']) ) : '#000';
$background_gradient_color_2 = (isset($options['background_gradient_color_2']) && $options['background_gradient_color_2'] != '') ? esc_attr( stripslashes($options['background_gradient_color_2']) ) : '#fff';
$quiz_gradient_direction = (isset($options['quiz_gradient_direction']) && $options['quiz_gradient_direction'] != '') ? $options['quiz_gradient_direction'] : 'vertical';


//Schedule of Quiz
$options['active_date_check'] = isset($options['active_date_check']) ? $options['active_date_check'] : 'off';
$active_date_check = (isset($options['active_date_check']) && $options['active_date_check'] == 'on') ? true : false;
if ($active_date_check) {
	$activateTime   = strtotime($options['activeInterval']);
	$activeQuiz     = date('Y-m-d H:i:s', $activateTime);
	$deactivateTime = strtotime($options['deactiveInterval']);
	$deactiveQuiz   = date('Y-m-d H:i:s', $deactivateTime);
} else {
	$activeQuiz   = current_time( 'mysql' );
	$deactiveQuiz = current_time( 'mysql' );
}

// WooCommerce integration
$quiz_intervals_wc = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));

$wc_for_js = "";
if($quiz_intervals_wc){
    $wc_for_js = "with-woo-product";
}

// Email configuration

$additional_emails = (isset($options['additional_emails']) && $options['additional_emails'] != '') ? $options['additional_emails'] : '';
$email_config_from_email = (isset($options['email_config_from_email']) && $options['email_config_from_email'] != '') ? $options['email_config_from_email'] : '';
$email_config_from_name = (isset($options['email_config_from_name']) && $options['email_config_from_name'] != '') ? stripslashes(htmlentities($options['email_config_from_name'], ENT_QUOTES, "UTF-8")) : '';
$email_config_from_subject = (isset($options['email_config_from_subject']) && $options['email_config_from_subject'] != '') ? stripslashes(htmlentities($options['email_config_from_subject'], ENT_QUOTES, "UTF-8")) : '';
$email_config_replyto_email = (isset($options['email_config_replyto_email']) && $options['email_config_replyto_email'] != '') ? $options['email_config_replyto_email'] : '';
$email_config_replyto_name = (isset($options['email_config_replyto_name']) && $options['email_config_replyto_name'] != '') ? stripslashes(htmlentities($options['email_config_replyto_name'], ENT_QUOTES, "UTF-8")) : '';

// Calculate the score option
$options['calculate_score'] = (!isset($options['calculate_score'])) ? 'by_correctness' : $options['calculate_score'];
$calculate_score = (isset($options['calculate_score']) && $options['calculate_score'] != '') ? $options['calculate_score'] : 'by_correctness';

// Quiz theme
$quiz_theme = isset($options['quiz_theme']) && $options['quiz_theme'] != "" ? $options['quiz_theme'] : 'classic_light';

// Redirect after submit
$options['redirect_after_submit'] = (!isset($options['redirect_after_submit'])) ? 'off' : $options['redirect_after_submit'];
$redirect_after_submit = isset($options['redirect_after_submit']) && $options['redirect_after_submit'] == 'on' ? true : false;
$submit_redirect_url = isset($options['submit_redirect_url']) ? $options['submit_redirect_url'] : '';
$submit_redirect_delay = (isset($options['submit_redirect_delay']) && $options['submit_redirect_delay'] != "") ? esc_attr( absint($options['submit_redirect_delay']) ) : '';
// Progress bar style
$progress_bar_style = (isset($options['progress_bar_style']) && $options['progress_bar_style'] != "") ? $options['progress_bar_style'] : 'first';

// Exit button in finish page
$options['enable_exit_button'] = (!isset($options['enable_exit_button'])) ? 'off' : $options['enable_exit_button'];
$enable_exit_button = isset($options['enable_exit_button']) && $options['enable_exit_button'] == 'on' ? true : false;
$exit_redirect_url = isset($options['exit_redirect_url']) ? $options['exit_redirect_url'] : '';

// Question image sizing
$image_sizing = (isset($options['image_sizing']) && $options['image_sizing'] != "") ? $options['image_sizing'] : 'cover';

// Quiz background image position
$quiz_bg_image_position = (isset($options['quiz_bg_image_position']) && $options['quiz_bg_image_position'] != "") ? $options['quiz_bg_image_position'] : 'center center';

// Custom class for quiz container
$custom_class = (isset($options['custom_class']) && $options['custom_class'] != "") ? $options['custom_class'] : '';

// Social Media links
$enable_social_links = (isset($options['enable_social_links']) && $options['enable_social_links'] == "on") ? true : false;
$social_links = (isset($options['social_links'])) ? $options['social_links'] : array(
    'linkedin_link' => '',
    'facebook_link' => '',
    'twitter_link' => '',
    'vkontakte_link' => '',
    'instagram_link' => '',
    'youtube_link' => '',
    'behance_link' => '',
);
$linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? esc_url($social_links['linkedin_link']) : '';
$facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? esc_url($social_links['facebook_link']) : '';
$twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? esc_url($social_links['twitter_link']) : '';
$vkontakte_link = isset($social_links['vkontakte_link']) && $social_links['vkontakte_link'] != '' ? esc_url($social_links['vkontakte_link']) : '';
$instagram_link = isset($social_links['instagram_link']) && $social_links['instagram_link'] != '' ? esc_url($social_links['instagram_link']) : '';
$youtube_link = isset($social_links['youtube_link']) && $social_links['youtube_link'] != '' ? esc_url($social_links['youtube_link']) : '';
$behance_link = isset($social_links['behance_link']) && $social_links['behance_link'] != '' ? esc_url($social_links['behance_link']) : '';

// Show quiz head information
// Show quiz title and description
$options['show_quiz_title'] = isset($options['show_quiz_title']) ? $options['show_quiz_title'] : 'on';
$options['show_quiz_desc'] = isset($options['show_quiz_desc']) ? $options['show_quiz_desc'] : 'on';
$show_quiz_title = (isset($options['show_quiz_title']) && $options['show_quiz_title'] == "on") ? true : false;
$show_quiz_desc = (isset($options['show_quiz_desc']) && $options['show_quiz_desc'] == "on") ? true : false;


// Show login form for not logged in users
$options['show_login_form'] = isset($options['show_login_form']) ? $options['show_login_form'] : 'off';
$show_login_form = (isset($options['show_login_form']) && $options['show_login_form'] == "on") ? true : false;


// Quiz container max-width for mobile
$mobile_max_width = (isset($options['mobile_max_width']) && $options['mobile_max_width'] != "") ? $options['mobile_max_width'] : '';


// Quiz theme
$quiz_theme = (isset($options['quiz_theme']) && $options['quiz_theme'] != '') ? $options['quiz_theme'] : 'classic_light';


// Limit users by option
$limit_users_by = (isset($options['limit_users_by']) && $options['limit_users_by'] != '') ? $options['limit_users_by'] : 'ip';

//Send results to user
$options['send_results_user'] = !isset($options['send_results_user']) ? 'off' : $options['send_results_user'];
$send_results_user = isset($options['send_results_user']) && $options['send_results_user'] == 'on' ? 'checked' : '';

//Send interval message to user
$options['send_interval_msg'] = !isset($options['send_interval_msg']) ? 'off' : $options['send_interval_msg'];
$send_interval_msg = isset($options['send_interval_msg']) && $options['send_interval_msg'] == 'on' ? 'checked' : '';

// Question bank options
// Question bank type
$question_bank_type = (isset($options['question_bank_type']) && $options['question_bank_type'] != '') ? $options['question_bank_type'] : 'general';
$questions_bank_cat_count = (isset($options['questions_bank_cat_count']) && !empty($options['questions_bank_cat_count'])) ? $options['questions_bank_cat_count'] : array();

foreach($question_bank_categories as $cid => $cat){
    if(! array_key_exists($cid, $questions_bank_cat_count)){
        $questions_bank_cat_count[$cid] = '';
    }
}

// Limitation tackers of quiz
$options['enable_tackers_count'] = !isset($options['enable_tackers_count']) ? 'off' : $options['enable_tackers_count'];
$enable_tackers_count = (isset($options['enable_tackers_count']) && $options['enable_tackers_count'] == 'on') ? true : false;
$tackers_count = (isset($options['tackers_count']) && $options['tackers_count'] != '') ? $options['tackers_count'] : '';

//AV Get post categories
$cat_list = get_categories(
    array(
        'hide_empty' => false,
    )
);


// Right/wrong answer text showing time option
$explanation_time = (isset($options['explanation_time']) && $options['explanation_time'] != '') ? $options['explanation_time'] : '4';

// Enable claer answer button
$options['enable_clear_answer'] = isset($options['enable_clear_answer']) ? $options['enable_clear_answer'] : 'off';
$enable_clear_answer = (isset($options['enable_clear_answer']) && $options['enable_clear_answer'] == "on") ? true : false;

//Send results to admin
$options['send_results_admin'] = !isset($options['send_results_admin']) ? 'on' : $options['send_results_admin'];
$send_results_admin = isset($options['send_results_admin']) && $options['send_results_admin'] == 'on' ? 'checked' : '';

//Send interval message to admin
$options['send_interval_msg_to_admin'] = !isset($options['send_interval_msg_to_admin']) ? 'off' : $options['send_interval_msg_to_admin'];
$send_interval_msg_to_admin = isset($options['send_interval_msg_to_admin']) && $options['send_interval_msg_to_admin'] == 'on' ? 'checked' : '';

// Show quiz category
$options['show_category'] = isset($options['show_category']) ? $options['show_category'] : 'off';
$show_category = (isset($options['show_category']) && $options['show_category'] == "on") ? true : false;

// Show question category
$options['show_question_category'] = isset($options['show_question_category']) ? $options['show_question_category'] : 'off';
$show_question_category = (isset($options['show_question_category']) && $options['show_question_category'] == "on") ? true : false;

/*
 * Answers images styles
 **********************************************
 */
// Answers padding option
$answers_padding = (isset($options['answers_padding']) && $options['answers_padding'] != '') ? $options['answers_padding'] : '5';
// Answers margin option
$answers_margin = (isset($options['answers_margin']) && $options['answers_margin'] != '') ? $options['answers_margin'] : '10';
// Answers border options
$options['answers_border'] = (isset($options['answers_border'])) ? $options['answers_border'] : 'on';
$answers_border = (isset($options['answers_border']) && $options['answers_border'] == 'on') ? true : false;
$answers_border_width = (isset($options['answers_border_width']) && $options['answers_border_width'] != '') ? $options['answers_border_width'] : '1';
$answers_border_style = (isset($options['answers_border_style']) && $options['answers_border_style'] != '') ? $options['answers_border_style'] : 'solid';
$answers_border_color = (isset($options['answers_border_color']) && $options['answers_border_color'] != '') ? esc_attr( stripslashes($options['answers_border_color']) ) : '#444';

$answers_box_shadow = (isset($options['answers_box_shadow']) && $options['answers_box_shadow'] == 'on') ? true : false;
$answers_box_shadow_color = (isset($options['answers_box_shadow_color']) && $options['answers_box_shadow_color'] != '') ? esc_attr( stripslashes($options['answers_box_shadow_color']) ) : '#000';

// Answers image options
$ans_img_height = (isset($options['ans_img_height']) && $options['ans_img_height'] != '') ? $options['ans_img_height'] : '150';
$ans_img_caption_style = (isset($options['ans_img_caption_style']) && $options['ans_img_caption_style'] != '') ? $options['ans_img_caption_style'] : 'outside';
$ans_img_caption_position = (isset($options['ans_img_caption_position']) && $options['ans_img_caption_position'] != '') ? $options['ans_img_caption_position'] : 'bottom';

// Show answers caption
$options['show_answers_caption'] = isset($options['show_answers_caption']) ? $options['show_answers_caption'] : 'on';
$show_answers_caption = (isset($options['show_answers_caption']) && $options['show_answers_caption'] == 'on') ? true : false;

// Answers right/wrong answers icons
$ans_right_wrong_icon = (isset($options['ans_right_wrong_icon']) && $options['ans_right_wrong_icon'] != '') ? $options['ans_right_wrong_icon'] : 'default';

/*************************************************/

// Show interval message
$options['show_interval_message'] = isset($options['show_interval_message']) ? $options['show_interval_message'] : 'on';
$show_interval_message = (isset($options['show_interval_message']) && $options['show_interval_message'] == 'on') ? true : false;

// Apply points to keywords
$options['apply_points_to_keywords'] = isset($options['apply_points_to_keywords']) ? $options['apply_points_to_keywords'] : 'off';
$apply_points_to_keywords = (isset($options['apply_points_to_keywords']) && $options['apply_points_to_keywords'] == 'on') ? true : false;

// Display score option
$display_score = (isset($options['display_score']) && $options['display_score'] != "") ? $options['display_score'] : 'by_percentage';

// Right / Wrong answers sound option
$options['enable_rw_asnwers_sounds'] = isset($options['enable_rw_asnwers_sounds']) ? $options['enable_rw_asnwers_sounds'] : 'off';
$enable_rw_asnwers_sounds = (isset($options['enable_rw_asnwers_sounds']) && $options['enable_rw_asnwers_sounds'] == "on") ? true : false;

// Allow collecting logged in users data
$options['allow_collecting_logged_in_users_data'] = isset($options['allow_collecting_logged_in_users_data']) ? $options['allow_collecting_logged_in_users_data'] : 'off';
$allow_collecting_logged_in_users_data = (isset($options['allow_collecting_logged_in_users_data']) && $options['allow_collecting_logged_in_users_data'] == "on") ? true : false;

// Pass score of the quiz
$quiz_pass_score = (isset($options['quiz_pass_score']) && $options['quiz_pass_score'] != "") ? $options['quiz_pass_score'] : 0;

// Hide quiz background image on the result page
$options['quiz_bg_img_in_finish_page'] = isset($options['quiz_bg_img_in_finish_page']) ? $options['quiz_bg_img_in_finish_page'] : 'off';
$quiz_bg_img_in_finish_page = (isset($options['quiz_bg_img_in_finish_page']) && $options['quiz_bg_img_in_finish_page'] == "on") ? true : false;

// Finish the quiz after making one wrong answer
$options['finish_after_wrong_answer'] = isset($options['finish_after_wrong_answer']) ? $options['finish_after_wrong_answer'] : 'off';
$finish_after_wrong_answer = (isset($options['finish_after_wrong_answer']) && $options['finish_after_wrong_answer'] == "on") ? true : false;

// Text after timer ends
$after_timer_text = (isset($options['after_timer_text']) && $options['after_timer_text'] != '') ? wpautop(stripslashes($options['after_timer_text'])) : '';

// Send certificate to admin too
$options['send_certificate_to_admin'] = isset($options['send_certificate_to_admin']) ? $options['send_certificate_to_admin'] : 'off';
$ays_send_certificate_to_admin = (isset($options['send_certificate_to_admin']) && $options['send_certificate_to_admin'] == "on") ? true : false;

// Use subject for the admin email
$options['use_subject_for_admin_email'] = isset($options['use_subject_for_admin_email']) ? $options['use_subject_for_admin_email'] : 'off';
$use_subject_for_admin_email = (isset($options['use_subject_for_admin_email']) && $options['use_subject_for_admin_email'] == "on") ? true : false;

// Enable certificate
$options['enable_certificate'] = isset($options['enable_certificate']) ? $options['enable_certificate'] : 'off';
$ays_enable_certificate = (isset($options['enable_certificate']) && $options['enable_certificate'] == "on") ? true : false;

// Enable certificate without send
$options['enable_certificate_without_send'] = isset($options['enable_certificate_without_send']) ? $options['enable_certificate_without_send'] : 'off';
$ays_enable_certificate_without_send = (isset($options['enable_certificate_without_send']) && $options['enable_certificate_without_send'] == "on") ? true : false;

// Enable to go next by pressing Enter key
$options['enable_enter_key'] = isset($options['enable_enter_key']) ? $options['enable_enter_key'] : 'on';
$enable_enter_key = (isset($options['enable_enter_key']) && $options['enable_enter_key'] == "on") ? true : false;

// Certificate title
$certificate_title = wpautop(stripslashes((isset($options['certificate_title'])) ? $options['certificate_title'] : ''));

// Certificate body
$certificate_body = wpautop(stripslashes((isset($options['certificate_body'])) ? $options['certificate_body'] : ''));

// Certificate background image
$certificate_image = (isset($options['certificate_image']) && $options['certificate_image'] != '') ? $options['certificate_image'] : '';

// Certificate background frame
$certificate_frame = (isset($options['certificate_frame']) && $options['certificate_frame'] != '') ? $options['certificate_frame'] : 'default';

$pdfapi_url = "https://ays-pro.com/pdfapi"; // rtrim(AYS_QUIZ_BASE_URL, '/') . '-pdfapi/pdfapi/',
//$pdfapi_url = "http://localhost/pdfapi";
$certificate_frames_url = apply_filters( 'ays_quiz_pdfapi_api_url', $pdfapi_url );
$certificate_frames_url = rtrim($certificate_frames_url, '/') . '/frames/';

// Certificate orientation
$certificate_orientation = (isset($options['certificate_orientation']) && $options['certificate_orientation'] != '') ? $options['certificate_orientation'] : 'l';

// Make the questions required
$options['make_questions_required'] = isset($options['make_questions_required']) ? $options['make_questions_required'] : 'off';
$make_questions_required = (isset($options['make_questions_required']) && $options['make_questions_required'] == "on") ? true : false;

// Show average rate after rate
$options['show_rate_after_rate'] = isset($options['show_rate_after_rate']) ? $options['show_rate_after_rate'] : 'on';
$show_rate_after_rate = (isset($options['show_rate_after_rate']) && $options['show_rate_after_rate'] == "on") ? true : false;

// Text color
$text_color = (isset($options['text_color']) && $options['text_color'] != '') ? esc_attr( stripslashes($options['text_color']) ) : '#333';

// Buttons text color
$buttons_text_color = (isset($options['buttons_text_color']) && $options['buttons_text_color'] != '') ? esc_attr( stripslashes($options['buttons_text_color']) ) : $text_color;

// Buttons position
$buttons_position = (isset($options['buttons_position']) && $options['buttons_position'] != '') ? $options['buttons_position'] : 'center';

// Password quiz
$options['enable_password'] = !isset($options['enable_password']) ? 'off' : $options['enable_password'];
$enable_password = (isset($options['enable_password']) && $options['enable_password'] == 'on') ? true : false;
$password_quiz = (isset($options['password_quiz']) && $options['password_quiz'] != '') ? $options['password_quiz'] : '';

$mail_message_admin = (isset($options['mail_message_admin']) && $options['mail_message_admin'] != '') ? wpautop(stripslashes($options['mail_message_admin'])) : '';

// Enable audio autoplay
$enable_audio_autoplay = (isset($options['enable_audio_autoplay']) && $options['enable_audio_autoplay'] == 'on') ? true : false;

// =========== Buttons Styles Start ===========

// Buttons size
$buttons_size = (isset($options['buttons_size']) && $options['buttons_size'] != "") ? $options['buttons_size'] : 'medium';

// Buttons font size
$buttons_font_size = (isset($options['buttons_font_size']) && $options['buttons_font_size'] != "") ? $options['buttons_font_size'] : '17';

// Buttons Left / Right padding
$buttons_left_right_padding = (isset($options['buttons_left_right_padding']) && $options['buttons_left_right_padding'] != '') ? $options['buttons_left_right_padding'] : '20';

// Buttons Top / Bottom padding
$buttons_top_bottom_padding = (isset($options['buttons_top_bottom_padding']) && $options['buttons_top_bottom_padding'] != '') ? $options['buttons_top_bottom_padding'] : '10';

// Buttons border radius
$buttons_border_radius = (isset($options['buttons_border_radius']) && $options['buttons_border_radius'] != "") ? $options['buttons_border_radius'] : '3';

// Buttons font size
$buttons_width = (isset($options['buttons_width']) && $options['buttons_width'] != "") ? $options['buttons_width'] : '';

// =========== Buttons Styles End ===========

//Send mail to site admin
$options['send_mail_to_site_admin'] = !isset($options['send_mail_to_site_admin']) ? 'on' : $options['send_mail_to_site_admin'];
$send_mail_to_site_admin = isset($options['send_mail_to_site_admin']) && $options['send_mail_to_site_admin'] == 'on' ? 'checked' : '';

// Enable leave page
$options['enable_leave_page'] = isset($options['enable_leave_page']) ? $options['enable_leave_page'] : 'on';
$enable_leave_page = (isset($options['enable_leave_page']) && $options['enable_leave_page'] == "on") ? true : false;

// Show only wrong answer
$options['show_only_wrong_answer'] = isset($options['show_only_wrong_answer']) ? $options['show_only_wrong_answer'] : 'off';
$show_only_wrong_answer = (isset($options['show_only_wrong_answer']) && $options['show_only_wrong_answer'] == "on") ? true : false;

// Pass Score
$pass_score = (isset($options['pass_score']) && $options['pass_score'] != '') ? absint(intval($options['pass_score'])) : '0';

// Quiz pass message
$pass_score_message = isset($options['pass_score_message']) ? stripslashes($options['pass_score_message']) : '<h4 style="text-align: center;">'. __("Congratulations!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You passed the quiz!", $this->plugin_name) .'</p>';

// Quiz pass message
$fail_score_message = isset($options['fail_score_message']) ? stripslashes($options['fail_score_message']) : '<h4 style="text-align: center;">'. __("Oops!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You have not passed the quiz! <br> Try again!", $this->plugin_name) .'</p>';

// Maximum pass score of the quiz
$quiz_max_pass_count = (isset($options['quiz_max_pass_count']) && $options['quiz_max_pass_count'] != "") ?  absint(intval($options['quiz_max_pass_count'])) : 1;

// Question Font Size
$question_font_size = (isset($options['question_font_size']) && $options['question_font_size'] != '' && absint(esc_attr($options['question_font_size'])) > 0) ? absint(esc_attr($options['question_font_size'])) : '16';

// Quiz Width by percentage or pixels
$quiz_width_by_percentage_px = (isset($options['quiz_width_by_percentage_px']) && $options['quiz_width_by_percentage_px'] != '') ? $options['quiz_width_by_percentage_px'] : 'pixels';

// Text instead of question hint
$questions_hint_icon_or_text = (isset($options['questions_hint_icon_or_text']) && $options['questions_hint_icon_or_text'] != '') ? $options['questions_hint_icon_or_text'] : 'default';
$questions_hint_value = (isset($options['questions_hint_value']) && $options['questions_hint_value'] != '') ? stripslashes(esc_attr($options['questions_hint_value'])) : '';

// Generated password
$ays_passwords_quiz = (isset($options['generate_password']) && $options['generate_password'] != '') ? $options['generate_password'] : 'general';
$generated_passwords = (isset($options['generated_passwords']) && $options['generated_passwords'] != '') ? $options['generated_passwords'] : array();
if(!empty($generated_passwords)){
    $created_passwords = (isset( $generated_passwords['created_passwords']) && !empty( $generated_passwords['created_passwords'])) ?  $generated_passwords['created_passwords'] : array();
    $active_passwords = (isset( $generated_passwords['active_passwords']) && !empty( $generated_passwords['active_passwords'])) ?  $generated_passwords['active_passwords'] : array();
    $used_passwords = (isset( $generated_passwords['used_passwords']) && !empty( $generated_passwords['used_passwords'])) ?  $generated_passwords['used_passwords'] : array();
}

// Display score by
$display_score_by = (isset($options['display_score_by']) && $options['display_score_by'] != '') ? $options['display_score_by'] : 'by_percentage';

// Show schedule timer
$options['show_schedule_timer'] = isset($options['show_schedule_timer']) ? $options['show_schedule_timer'] : 'off';
$schedule_show_timer = (isset($options['show_schedule_timer']) && $options['show_schedule_timer'] == 'on') ? true : false;
$show_timer_type = isset($options['show_timer_type']) && $options['show_timer_type'] != '' ? $options['show_timer_type'] : 'countdown';


$keyword_default_max_value = isset($settings_options['keyword_default_max_value']) ? $settings_options['keyword_default_max_value'] : null;
if($keyword_default_max_value === null){
    $keyword_default_max_value = 6;
}
$keyword_arr = $this->ays_quiz_generate_keyword_array( $keyword_default_max_value );

wp_localize_script( $this->plugin_name . "-functions", 'aysQuizKewordsArray', $keyword_arr );

// Enable Finish Button Comfirm Box
$options['enable_early_finsh_comfirm_box'] = isset($options['enable_early_finsh_comfirm_box']) ? $options['enable_early_finsh_comfirm_box'] : 'on';
$enable_early_finsh_comfirm_box = (isset($options['enable_early_finsh_comfirm_box']) && $options['enable_early_finsh_comfirm_box'] == "on") ? true : false;

// Enable Negative Mark
//$options['enable_negative_mark'] = isset($options['enable_negative_mark']) ? $options['enable_negative_mark'] : 'off';
//$enable_negative_mark = (isset($options['enable_negative_mark']) && $options['enable_negative_mark'] == 'on') ? true : false;

// Negative Mark Point
//$negative_mark_point = (isset($options['negative_mark_point']) && $options['negative_mark_point'] != '') ? abs($options['negative_mark_point']) : 0;

// Progress live bar style
$progress_live_bar_style = (isset($options['progress_live_bar_style']) && $options['progress_live_bar_style'] != "") ? $options['progress_live_bar_style'] : 'default';

// Show all questions result in finish page
$options['enable_questions_result'] = isset($options['enable_questions_result']) ? $options['enable_questions_result'] : 'off';
$enable_questions_result = (isset($options['enable_questions_result']) && $options['enable_questions_result'] == 'on') ? true : false;

// Hide correct answers
$options['hide_correct_answers'] = isset($options['hide_correct_answers']) ? $options['hide_correct_answers'] : 'off';
$hide_correct_answers = (isset($options['hide_correct_answers']) && $options['hide_correct_answers'] == 'on') ? true : false;

// Quiz loader text value
$quiz_loader_text_value = (isset($options['quiz_loader_text_value']) && $options['quiz_loader_text_value'] != '') ? stripslashes(esc_attr($options['quiz_loader_text_value'])) : '';

// Show information form to logged in users
$options['show_information_form'] = isset($options['show_information_form']) ? $options['show_information_form'] : 'on';
$show_information_form = (isset($options['show_information_form']) && $options['show_information_form'] == 'on') ? true : false;

// Show questions explanation on
$show_questions_explanation = (isset($options['show_questions_explanation']) && $options['show_questions_explanation'] != '') ? $options['show_questions_explanation'] : 'on_results_page';

// Enable questions ordering by category
$options['enable_questions_ordering_by_cat'] = isset($options['enable_questions_ordering_by_cat']) ? $options['enable_questions_ordering_by_cat'] : 'off';
$enable_questions_ordering_by_cat = (isset($options['enable_questions_ordering_by_cat']) && $options['enable_questions_ordering_by_cat'] == "on") ? true : false;

// Send mail to USER by pass score
$options['enable_send_mail_to_user_by_pass_score'] = isset($options['enable_send_mail_to_user_by_pass_score']) ? $options['enable_send_mail_to_user_by_pass_score'] : 'off';
$enable_send_mail_to_user_by_pass_score = (isset($options['enable_send_mail_to_user_by_pass_score']) && $options['enable_send_mail_to_user_by_pass_score'] == "on") ? true : false;

// Send mail to ADMIN by pass score
$options['enable_send_mail_to_admin_by_pass_score'] = isset($options['enable_send_mail_to_admin_by_pass_score']) ? $options['enable_send_mail_to_admin_by_pass_score'] : 'off';
$enable_send_mail_to_admin_by_pass_score = (isset($options['enable_send_mail_to_admin_by_pass_score']) && $options['enable_send_mail_to_admin_by_pass_score'] == "on") ? true : false;

// Show questions numbering
$show_questions_numbering = (isset($options['show_questions_numbering']) && $options['show_questions_numbering'] != '') ? $options['show_questions_numbering'] : 'none';

// Show answers numbering
$show_answers_numbering = (isset($options['show_answers_numbering']) && $options['show_answers_numbering'] != '') ? $options['show_answers_numbering'] : 'none';

// Quiz loader custom gif value
$quiz_loader_custom_gif = (isset($options['quiz_loader_custom_gif']) && $options['quiz_loader_custom_gif'] != '') ? stripslashes(esc_url($options['quiz_loader_custom_gif'])) : '';

// Disable answer hover
$options['disable_hover_effect'] = isset($options['disable_hover_effect']) ? $options['disable_hover_effect'] : 'off';
$disable_hover_effect = (isset($options['disable_hover_effect']) && $options['disable_hover_effect'] == 'on') ? true : false;

// Quiz loader custom gif width
$quiz_loader_custom_gif_width = (isset($options['quiz_loader_custom_gif_width']) && $options['quiz_loader_custom_gif_width'] != '') ? absint( intval( $options['quiz_loader_custom_gif_width'] ) ) : 100;

// Quiz title transformation
$quiz_title_transformation = (isset($options['quiz_title_transformation']) && sanitize_text_field($options['quiz_title_transformation']) != "") ? sanitize_text_field($options['quiz_title_transformation']) : 'uppercase';

// Image Width(px)
$image_width = (isset($options['image_width']) && sanitize_text_field($options['image_width']) != '') ? absint( sanitize_text_field($options['image_width']) ) : '';

// Quiz image width percentage/px
$quiz_image_width_by_percentage_px = (isset($options['quiz_image_width_by_percentage_px']) && sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) != '') ? sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) : 'pixels';

// Quiz image height
$quiz_image_height = (isset($options['quiz_image_height']) && sanitize_text_field($options['quiz_image_height']) != '') ? absint( sanitize_text_field($options['quiz_image_height']) ) : '';

// Hide background image on start page
$options['quiz_bg_img_on_start_page'] = isset($options['quiz_bg_img_on_start_page']) ? $options['quiz_bg_img_on_start_page'] : 'off';
$quiz_bg_img_on_start_page = (isset($options['quiz_bg_img_on_start_page']) && $options['quiz_bg_img_on_start_page'] == 'on') ? true : false;

//  Box Shadow X offset
$quiz_box_shadow_x_offset = (isset($options['quiz_box_shadow_x_offset']) && $options['quiz_box_shadow_x_offset'] != '' && intval( $options['quiz_box_shadow_x_offset'] ) != 0) ? intval( $options['quiz_box_shadow_x_offset'] ) : 0;

//  Box Shadow Y offset
$quiz_box_shadow_y_offset = (isset($options['quiz_box_shadow_y_offset']) && $options['quiz_box_shadow_y_offset'] != '' && intval( $options['quiz_box_shadow_y_offset'] ) != 0) ? intval( $options['quiz_box_shadow_y_offset'] ) : 0;

//  Box Shadow Z offset
$quiz_box_shadow_z_offset = (isset($options['quiz_box_shadow_z_offset']) && $options['quiz_box_shadow_z_offset'] != '' && intval( $options['quiz_box_shadow_z_offset'] ) != 0) ? intval( $options['quiz_box_shadow_z_offset'] ) : 15;

// Question text alignment
$quiz_question_text_alignment = (isset($options['quiz_question_text_alignment']) && sanitize_text_field( $options['quiz_question_text_alignment'] ) != '') ? sanitize_text_field( $options['quiz_question_text_alignment'] ) : 'center';

// Quiz arrows option arrows
$quiz_arrow_type = (isset($options['quiz_arrow_type']) && ( $options['quiz_arrow_type'] ) != '') ? ( $options['quiz_arrow_type'] ) : 'default';

// Show wrong answers first
$options['quiz_show_wrong_answers_first'] = isset($options['quiz_show_wrong_answers_first']) ? sanitize_text_field($options['quiz_show_wrong_answers_first']) : 'off';
$quiz_show_wrong_answers_first = (isset($options['quiz_show_wrong_answers_first']) && $options['quiz_show_wrong_answers_first'] == 'on') ? true : false;

//Enable Full Screen Mode
$options['enable_full_screen_mode'] = isset($options['enable_full_screen_mode']) ? $options['enable_full_screen_mode'] : 'off';
$enable_full_screen_mode = (isset($options['enable_full_screen_mode']) && $options['enable_full_screen_mode'] == 'on') ? true : false;

//Enable navigation bar
$options['enable_navigation_bar'] = isset($options['enable_navigation_bar']) ? $options['enable_navigation_bar'] : 'off';
$enable_navigation_bar = (isset($options['enable_navigation_bar']) && $options['enable_navigation_bar'] == 'on') ? true : false;

// Display all questions on one page
$options['quiz_display_all_questions'] = isset($options['quiz_display_all_questions']) ? sanitize_text_field($options['quiz_display_all_questions']) : 'off';
$quiz_display_all_questions = (isset($options['quiz_display_all_questions']) && $options['quiz_display_all_questions'] == 'on') ? true : false;

// Turn red warning
$options['quiz_timer_red_warning'] = isset($options['quiz_timer_red_warning']) ? sanitize_text_field($options['quiz_timer_red_warning']) : 'off';
$quiz_timer_red_warning = (isset($options['quiz_timer_red_warning']) && $options['quiz_timer_red_warning'] == 'on') ? true : false;

// Timezone | Schedule the quiz
$ays_quiz_schedule_timezone = (isset($options['quiz_schedule_timezone']) && $options['quiz_schedule_timezone'] != '') ? sanitize_text_field( $options['quiz_schedule_timezone'] ) : get_option( 'timezone_string' );

// Remove old Etc mappings. Fallback to gmt_offset.
if ( strpos( $ays_quiz_schedule_timezone, 'Etc/GMT' ) !== false ) {
    $ays_quiz_schedule_timezone = '';
}

$current_offset = get_option( 'gmt_offset' );
if ( empty( $ays_quiz_schedule_timezone ) ) { // Create a UTC+- zone if no timezone string exists.

    if ( 0 == $current_offset ) {
        $ays_quiz_schedule_timezone = 'UTC+0';
    } elseif ( $current_offset < 0 ) {
        $ays_quiz_schedule_timezone = 'UTC' . $current_offset;
    } else {
        $ays_quiz_schedule_timezone = 'UTC+' . $current_offset;
    }
}

// Hint icon | Button | Text Value
$questions_hint_button_value = (isset($options['questions_hint_button_value']) && sanitize_text_field( $options['questions_hint_button_value'] ) != '') ? sanitize_text_field( esc_attr( $options['questions_hint_button_value']) ) : '';

// Quiz takers message
$quiz_tackers_message = ( isset($options['quiz_tackers_message']) && $options['quiz_tackers_message'] != '' ) ? stripslashes( wpautop( $options['quiz_tackers_message'] ) ) : __( "This quiz is expired!", $this->plugin_name );

// Show the Social buttons
$options['enable_social_buttons'] = isset($options['enable_social_buttons']) ? sanitize_text_field($options['enable_social_buttons']) : 'on';
$enable_social_buttons = (isset($options['enable_social_buttons']) && $options['enable_social_buttons'] == 'on') ? true : false;

// Enable Linkedin button
$options['quiz_enable_linkedin_share_button'] = isset($options['quiz_enable_linkedin_share_button']) ? sanitize_text_field($options['quiz_enable_linkedin_share_button']) : 'on';
$quiz_enable_linkedin_share_button = (isset($options['quiz_enable_linkedin_share_button']) && $options['quiz_enable_linkedin_share_button'] == 'on') ? true : false;

// Enable Facebook button
$options['quiz_enable_facebook_share_button'] = isset($options['quiz_enable_facebook_share_button']) ? sanitize_text_field($options['quiz_enable_facebook_share_button']) : 'on';
$quiz_enable_facebook_share_button = (isset($options['quiz_enable_facebook_share_button']) && $options['quiz_enable_facebook_share_button'] == 'on') ? true : false;

// Enable Twitter button
$options['quiz_enable_twitter_share_button'] = isset($options['quiz_enable_twitter_share_button']) ? sanitize_text_field($options['quiz_enable_twitter_share_button']) : 'on';
$quiz_enable_twitter_share_button = (isset($options['quiz_enable_twitter_share_button']) && $options['quiz_enable_twitter_share_button'] == 'on') ? true : false;

// Enable VKontakte button
$options['quiz_enable_vkontakte_share_button'] = isset($options['quiz_enable_vkontakte_share_button']) ? sanitize_text_field($options['quiz_enable_vkontakte_share_button']) : 'on';
$quiz_enable_vkontakte_share_button = (isset($options['quiz_enable_vkontakte_share_button']) && $options['quiz_enable_vkontakte_share_button'] == 'on') ? true : false;

if ( ! $quiz_enable_linkedin_share_button &&
        ! $quiz_enable_facebook_share_button &&
            ! $quiz_enable_vkontakte_share_button &&
                ! $quiz_enable_twitter_share_button ) {
    $quiz_enable_linkedin_share_button = true;
    $quiz_enable_facebook_share_button = true;
    $quiz_enable_twitter_share_button = true;
    $quiz_enable_vkontakte_share_button = true;
}

// Turn on extra security check
$options['turn_on_extra_security_check'] = isset($options['turn_on_extra_security_check']) ? sanitize_text_field($options['turn_on_extra_security_check']) : 'on';
$turn_on_extra_security_check = (isset($options['turn_on_extra_security_check']) && $options['turn_on_extra_security_check'] == 'on') ? true : false;

// Hide attempts limitation notice
$options['hide_limit_attempts_notice'] = isset($options['hide_limit_attempts_notice']) ? sanitize_text_field($options['hide_limit_attempts_notice']) : 'off';
$hide_limit_attempts_notice = (isset($options['hide_limit_attempts_notice']) && $options['hide_limit_attempts_notice'] == 'on') ? true : false;

// Enable top keywords
$options['enable_top_keywords'] = isset($options['enable_top_keywords']) ? sanitize_text_field($options['enable_top_keywords']) : 'off';
$enable_top_keywords = (isset($options['enable_top_keywords']) && $options['enable_top_keywords'] == 'on') ? true : false;

// Make responses anonymous
$options['quiz_make_responses_anonymous'] = isset($options['quiz_make_responses_anonymous']) ? sanitize_text_field($options['quiz_make_responses_anonymous']) : 'off';
$quiz_make_responses_anonymous = (isset($options['quiz_make_responses_anonymous']) && $options['quiz_make_responses_anonymous'] == 'on') ? true : false;

// Add all reviews link
$options['quiz_make_all_review_link'] = isset($options['quiz_make_all_review_link']) ? sanitize_text_field($options['quiz_make_all_review_link']) : 'off';
$quiz_make_all_review_link = (isset($options['quiz_make_all_review_link']) && $options['quiz_make_all_review_link'] == 'on') ? true : false;

// Custom CSS
$ays_quiz_custom_css = (isset($options['custom_css']) && $options['custom_css'] != '') ? esc_attr( $options['custom_css'] ) : '';

//Enable Bulk Coupon
$options['quiz_enable_coupon'] = isset($options['quiz_enable_coupon']) ? sanitize_text_field($options['quiz_enable_coupon']) : 'off';
$quiz_enable_coupon = (isset($options['quiz_enable_coupon']) && $options['quiz_enable_coupon'] == 'on') ? true : false;

//Active/Inactive coupons
$active_inactive_coupons = (isset($options['quiz_coupons_array']) && $options['quiz_coupons_array'] != '') ? $options['quiz_coupons_array'] : array();

if(!empty($active_inactive_coupons)){

    $quiz_active_coupons = (isset( $active_inactive_coupons['quiz_active_coupons']) && !empty( $active_inactive_coupons['quiz_active_coupons'])) ?  $active_inactive_coupons['quiz_active_coupons'] : array();

    $quiz_inactive_coupons = (isset( $active_inactive_coupons['quiz_inactive_coupons']) && !empty( $active_inactive_coupons['quiz_inactive_coupons'])) ?  $active_inactive_coupons['quiz_inactive_coupons'] : array();
}

// Message before timer
$quiz_message_before_timer = (isset($options['quiz_message_before_timer']) && $options['quiz_message_before_timer'] != '') ? esc_attr( sanitize_text_field( $options['quiz_message_before_timer'] ) ) : '';

// Password for passing quiz | Message
$quiz_password_message = ( isset( $options['quiz_password_message']) && $options['quiz_password_message'] != '' ) ? stripslashes( $options['quiz_password_message'] ) : '';

// Enable confirmation box for the See Result button
$options['enable_see_result_confirm_box'] = isset($options['enable_see_result_confirm_box']) ? sanitize_text_field($options['enable_see_result_confirm_box']) : 'off';
$enable_see_result_confirm_box = (isset($options['enable_see_result_confirm_box']) && $options['enable_see_result_confirm_box'] == 'on') ? true : false;

// Display form fields labels
$options['display_fields_labels'] = isset($options['display_fields_labels']) ? sanitize_text_field($options['display_fields_labels']) : 'off';
$display_fields_labels = (isset($options['display_fields_labels']) && $options['display_fields_labels'] == 'on') ? true : false;

// Enable toggle password visibility
$options['quiz_enable_password_visibility'] = isset($options['quiz_enable_password_visibility']) ? $options['quiz_enable_password_visibility'] : 'off';
$quiz_enable_password_visibility = (isset($options['quiz_enable_password_visibility']) && $options['quiz_enable_password_visibility'] == 'on') ? true : false;

// Question font size | On mobile
$question_mobile_font_size = (isset($options['question_mobile_font_size']) && sanitize_text_field($options['question_mobile_font_size']) != '' && absint( esc_attr($options['question_mobile_font_size']) ) > 0) ? absint( esc_attr($options['question_mobile_font_size']) ) : 16;

// Answer font size | On mobile
$answers_mobile_font_size = (isset($options['answers_mobile_font_size']) && sanitize_text_field($options['answers_mobile_font_size']) != '' && absint( sanitize_text_field($options['answers_mobile_font_size']) ) > 0) ? absint( sanitize_text_field($options['answers_mobile_font_size']) ) : 15;

// Heading for social buttons
$social_buttons_heading = (isset($options['social_buttons_heading']) && $options['social_buttons_heading'] != '') ? stripslashes( wpautop( $options['social_buttons_heading'] ) ) : "";

// Limit Attempts Count By User Role
$limit_attempts_count_by_user_role = (isset($options['limit_attempts_count_by_user_role']) && $options['limit_attempts_count_by_user_role'] != "") ?  absint(intval($options['limit_attempts_count_by_user_role'])) : '';

// Enable autostart
$options['enable_autostart'] = isset($options['enable_autostart']) ? $options['enable_autostart'] : 'off';
$enable_autostart = (isset($options['enable_autostart']) && $options['enable_autostart'] == 'on') ? true : false;

// Heading for social media links
$social_links_heading = (isset($options['social_links_heading']) && $options['social_links_heading'] != '') ? stripslashes( wpautop( $options['social_links_heading'] ) ) : "";

// Show question category description
$options['quiz_enable_question_category_description'] = isset($options['quiz_enable_question_category_description']) ? $options['quiz_enable_question_category_description'] : 'off';
$quiz_enable_question_category_description = (isset($options['quiz_enable_question_category_description']) && $options['quiz_enable_question_category_description'] == 'on') ? true : false;

// Answers margin option
$answers_margin = (isset($options['answers_margin']) && $options['answers_margin'] != '') ? esc_attr( stripslashes( $options['answers_margin'] ) ) : '10';

// Message before redirect timer
$quiz_message_before_redirect_timer = (isset($options['quiz_message_before_redirect_timer']) && $options['quiz_message_before_redirect_timer'] != '') ? stripslashes( esc_attr( $options['quiz_message_before_redirect_timer'] ) ) : '';

// Button font-size (px) | Mobile
$buttons_mobile_font_size = (isset($options['buttons_mobile_font_size']) && $options['buttons_mobile_font_size'] != '') ? absint( esc_attr( $options['buttons_mobile_font_size'] ) ) : 17;

// Change current quiz creation date
$change_creation_date = (isset($quiz['create_date']) && $quiz['create_date'] != '') ? $quiz['create_date'] : current_time( 'mysql' );

// Answer box Shadow X offset
$quiz_answer_box_shadow_x_offset = (isset($options['quiz_answer_box_shadow_x_offset']) && ( $options['quiz_answer_box_shadow_x_offset'] ) != '' && ( $options['quiz_answer_box_shadow_x_offset'] ) != 0) ? esc_attr( intval( $options['quiz_answer_box_shadow_x_offset'] ) ) : 0;

// Answer box Shadow Y offset
$quiz_answer_box_shadow_y_offset = (isset($options['quiz_answer_box_shadow_y_offset']) && ( $options['quiz_answer_box_shadow_y_offset'] ) != '' && ( $options['quiz_answer_box_shadow_y_offset'] ) != 0) ? esc_attr( intval( $options['quiz_answer_box_shadow_y_offset'] ) ) : 0;

// Answer box Shadow Z offset
$quiz_answer_box_shadow_z_offset = (isset($options['quiz_answer_box_shadow_z_offset']) && ( $options['quiz_answer_box_shadow_z_offset'] ) != '' && ( $options['quiz_answer_box_shadow_z_offset'] ) != 0) ? esc_attr( intval( $options['quiz_answer_box_shadow_z_offset'] ) ) : 10;

// Change the author of the current quiz
$change_quiz_create_author = (isset($quiz['author_id']) && $quiz['author_id'] != '') ? absint( sanitize_text_field( $quiz['author_id'] ) ) : $user_id;

if( $change_quiz_create_author  && $change_quiz_create_author > 0 ){
    global $wpdb;
    $users_table = esc_sql( $wpdb->prefix . 'users' );

    $sql_users = "SELECT ID,display_name FROM {$users_table} WHERE ID = {$change_quiz_create_author}";

    $ays_quiz_create_author_data = $wpdb->get_row($sql_users, "ARRAY_A");
} else {
    $change_quiz_create_author = $user_id;
    $ays_quiz_create_author_data = array(
        "ID" => $user_id,
        "display_name" => $user->data->display_name,
    );
}

$ays_users_to_export_search = array();

if( isset( $options['ays_users_to_export_search'] ) && !empty( $options['ays_users_to_export_search'] ) ){

    $users_table = esc_sql( $wpdb->prefix . 'users' );

    $quiz_user_ids = implode( ",", $options['ays_users_to_export_search'] );

    $sql_users = "SELECT ID,display_name FROM {$users_table} WHERE ID IN (". $quiz_user_ids .")";

    $ays_users_to_export_search = $wpdb->get_results($sql_users, "ARRAY_A");
}

// Quiz title text shadow
$options['quiz_enable_title_text_shadow'] = isset($options['quiz_enable_title_text_shadow']) ? esc_attr($options['quiz_enable_title_text_shadow']) : 'off';
$quiz_enable_title_text_shadow = (isset($options['quiz_enable_title_text_shadow']) && $options['quiz_enable_title_text_shadow'] == 'on') ? true : false;

// Quiz title text shadow color
$quiz_title_text_shadow_color = (isset($options['quiz_title_text_shadow_color']) && $options['quiz_title_text_shadow_color'] != '') ? esc_attr($options['quiz_title_text_shadow_color']) : '#333';

// Font size for the right answer
$right_answers_font_size = (isset($options['right_answers_font_size']) && $options['right_answers_font_size'] != '') ? absint(esc_attr($options['right_answers_font_size'])) : '16';

// Font size for the wrong answer
$wrong_answers_font_size = (isset($options['wrong_answers_font_size']) && $options['wrong_answers_font_size'] != '') ? absint(esc_attr($options['wrong_answers_font_size'])) : '16';

// Font size for the wrong answer | Mobile
$wrong_answers_mobile_font_size = (isset($options['wrong_answers_mobile_font_size']) && $options['wrong_answers_mobile_font_size'] != '') ? absint(esc_attr($options['wrong_answers_mobile_font_size'])) : $wrong_answers_font_size;

// Font size for the question explanation
$quest_explanation_font_size = (isset($options['quest_explanation_font_size']) && $options['quest_explanation_font_size'] != '') ? absint(esc_attr($options['quest_explanation_font_size'])) : '16';

// Font size for the question explanation | Mobile
$quest_explanation_mobile_font_size = (isset($options['quest_explanation_mobile_font_size']) && $options['quest_explanation_mobile_font_size'] != '') ? absint(esc_attr($options['quest_explanation_mobile_font_size'])) : $quest_explanation_font_size;

// Waiting time
$options['quiz_waiting_time'] = isset($options['quiz_waiting_time']) ? esc_attr($options['quiz_waiting_time']) : 'off';
$quiz_waiting_time = (isset($options['quiz_waiting_time']) && $options['quiz_waiting_time'] == 'on') ? true : false;

// Quiz Title Text Shadow X offset
$quiz_title_text_shadow_x_offset = (isset($options['quiz_title_text_shadow_x_offset']) && ( $options['quiz_title_text_shadow_x_offset'] ) != '' && ( $options['quiz_title_text_shadow_x_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_x_offset'] ) ) : 2;

// Quiz Title Text Shadow Y offset
$quiz_title_text_shadow_y_offset = (isset($options['quiz_title_text_shadow_y_offset']) && ( $options['quiz_title_text_shadow_y_offset'] ) != '' && ( $options['quiz_title_text_shadow_y_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_y_offset'] ) ) : 2;

// Quiz Title Text Shadow Z offset
$quiz_title_text_shadow_z_offset = (isset($options['quiz_title_text_shadow_z_offset']) && ( $options['quiz_title_text_shadow_z_offset'] ) != '' && ( $options['quiz_title_text_shadow_z_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_z_offset'] ) ) : 2;

// Show only wrong answers
$options['quiz_show_only_wrong_answers'] = isset($options['quiz_show_only_wrong_answers']) ? sanitize_text_field($options['quiz_show_only_wrong_answers']) : 'off';
$quiz_show_only_wrong_answers = (isset($options['quiz_show_only_wrong_answers']) && $options['quiz_show_only_wrong_answers'] == 'on') ? true : false;

// Quiz title font size
$quiz_title_font_size = (isset($options['quiz_title_font_size']) && ( $options['quiz_title_font_size'] ) != '' && ( $options['quiz_title_font_size'] ) != 0) ? esc_attr( absint( $options['quiz_title_font_size'] ) ) : 21;

// Quiz title font size | On mobile
$quiz_title_mobile_font_size = (isset($options['quiz_title_mobile_font_size']) && sanitize_text_field($options['quiz_title_mobile_font_size']) != '') ? esc_attr( absint($options['quiz_title_mobile_font_size']) ) : 21;

// Quiz password width
$quiz_password_width = (isset($options['quiz_password_width']) && ( $options['quiz_password_width'] ) != '' && ( $options['quiz_password_width'] ) != 0) ? esc_attr( absint( $options['quiz_password_width'] ) ) : "";

// Enable quiz assessment | Placeholder text
$quiz_review_placeholder_text = (isset($options['quiz_review_placeholder_text']) && $options['quiz_review_placeholder_text'] != '') ? stripslashes( esc_attr( $options['quiz_review_placeholder_text'] ) ) : "";

// Make review required
$options['quiz_make_review_required'] = isset($options['quiz_make_review_required']) ? sanitize_text_field($options['quiz_make_review_required']) : 'off';
$quiz_make_review_required = (isset($options['quiz_make_review_required']) && $options['quiz_make_review_required'] == 'on') ? true : false;

// Enable the Show/Hide toggle
$options['quiz_enable_results_toggle'] = isset($options['quiz_enable_results_toggle']) ? sanitize_text_field($options['quiz_enable_results_toggle']) : 'off';
$quiz_enable_results_toggle = (isset($options['quiz_enable_results_toggle']) && $options['quiz_enable_results_toggle'] == 'on') ? true : false;

// Thank you message | Review
$quiz_review_thank_you_message = (isset($options['quiz_review_thank_you_message']) && $options['quiz_review_thank_you_message'] != '') ? stripslashes( wpautop( $options['quiz_review_thank_you_message'] ) ) : "";

// Enable Comment Field
$options['quiz_review_enable_comment_field'] = isset($options['quiz_review_enable_comment_field']) ? sanitize_text_field($options['quiz_review_enable_comment_field']) : 'on';
$quiz_review_enable_comment_field = (isset($options['quiz_review_enable_comment_field']) && $options['quiz_review_enable_comment_field'] == 'on') ? true : false;

//Enable keyboard navigation
$options['quiz_enable_keyboard_navigation'] = isset($options['quiz_enable_keyboard_navigation']) ? $options['quiz_enable_keyboard_navigation'] : 'off';
$quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
if( $action == 'add' ){
    $quiz_enable_keyboard_navigation = true;
}

// Question Image Zoom
$options['quiz_enable_question_image_zoom'] = isset($options['quiz_enable_question_image_zoom']) ? esc_attr($options['quiz_enable_question_image_zoom']) : 'off';
$quiz_enable_question_image_zoom = (isset($options['quiz_enable_question_image_zoom']) && $options['quiz_enable_question_image_zoom'] == 'on') ? true : false;

// Font size for the right answer | Mobile
$right_answers_mobile_font_size = (isset($options['right_answers_mobile_font_size']) && $options['right_answers_mobile_font_size'] != '') ? absint(esc_attr($options['right_answers_mobile_font_size'])) : $right_answers_font_size;

// Display Messages before the buttons
$options['quiz_display_messages_before_buttons'] = isset($options['quiz_display_messages_before_buttons']) ? esc_attr($options['quiz_display_messages_before_buttons']) : 'off';
$quiz_display_messages_before_buttons = (isset($options['quiz_display_messages_before_buttons']) && $options['quiz_display_messages_before_buttons'] == 'on') ? true : false;

// Question per page type
$question_count_per_page_type = (isset($options['question_count_per_page_type']) && $options['question_count_per_page_type'] != '') ? sanitize_text_field($options['question_count_per_page_type']) : 'general';

$question_count_per_page_custom_order = (isset($options['question_count_per_page_custom_order']) && $options['question_count_per_page_custom_order'] != "" ) ? sanitize_text_field($options['question_count_per_page_custom_order']) : '';

if( !empty( $question_count_per_page_custom_order ) ){
    $question_count_per_page_custom_order_arr = explode(',', $question_count_per_page_custom_order);

    if ( !empty( $question_count_per_page_custom_order_arr ) ) {
        $question_count_per_page_custom_order_arr_new = array();
        foreach ($question_count_per_page_custom_order_arr as $per_page_key => $per_page_value) {
            if( is_numeric( $per_page_value ) && $per_page_value != "" && $per_page_value != 0 ){
                $question_count_per_page_custom_order_arr_new[] = absint($per_page_value);
            }
        }
        $question_count_per_page_custom_order = implode( "," , $question_count_per_page_custom_order_arr_new);
    }
}

// Timer type
$quiz_timer_type = (isset($options['quiz_timer_type']) && $options['quiz_timer_type'] != '') ? sanitize_text_field( $options['quiz_timer_type'] ) : 'quiz_timer';

// Display Messages before the buttons
$options['ays_allow_exporting_quizzes'] = isset($options['ays_allow_exporting_quizzes']) ? esc_attr($options['ays_allow_exporting_quizzes']) : 'off';
$ays_allow_exporting_quizzes = (isset($options['ays_allow_exporting_quizzes']) && $options['ays_allow_exporting_quizzes'] == 'on') ? true : false;

// Quiz Pass Score type
$quiz_pass_score_type = (isset($options['quiz_pass_score_type']) && $options['quiz_pass_score_type'] != '') ? sanitize_text_field( $options['quiz_pass_score_type'] ) : 'percentage';

// Certificate Quiz Pass Score type
$quiz_certificate_pass_score_type = (isset($options['quiz_certificate_pass_score_type']) && $options['quiz_certificate_pass_score_type'] != '') ? sanitize_text_field( $options['quiz_certificate_pass_score_type'] ) : 'percentage';

// Equal keywords text
$quiz_equal_keywords_text = (isset($options['quiz_equal_keywords_text']) && $options['quiz_equal_keywords_text'] != '') ? stripslashes( $options['quiz_equal_keywords_text'] ) : "";

//Enable navigation bar marked questions
$options['enable_navigation_bar_marked_questions'] = isset($options['enable_navigation_bar_marked_questions']) ? $options['enable_navigation_bar_marked_questions'] : 'off';
$enable_navigation_bar_marked_questions = (isset($options['enable_navigation_bar_marked_questions']) && $options['enable_navigation_bar_marked_questions'] == 'on') ? true : false;

// enable question reporting
$options['enable_questions_reporting'] = isset($options['enable_questions_reporting']) ? $options['enable_questions_reporting'] : 'off';
$enable_question_reporting = (isset($options['enable_questions_reporting']) && $options['enable_questions_reporting'] == "on") ? true : false;

// Show question category description
$options['quiz_enable_questions_reporting_mail'] = isset($options['quiz_enable_questions_reporting_mail']) ? $options['quiz_enable_questions_reporting_mail'] : 'off';
$quiz_enable_question_reporting_mail = (isset($options['quiz_enable_questions_reporting_mail']) && $options['quiz_enable_questions_reporting_mail'] == 'on') ? true : false;

// Enable users' anonymous assessment
$options['quiz_enable_user_cհoosing_anonymous_assessment'] = isset($options['quiz_enable_user_cհoosing_anonymous_assessment']) ? sanitize_text_field($options['quiz_enable_user_cհoosing_anonymous_assessment']) : 'off';
$quiz_enable_user_cհoosing_anonymous_assessment = (isset($options['quiz_enable_user_cհoosing_anonymous_assessment']) && $options['quiz_enable_user_cհoosing_anonymous_assessment'] == 'on') ? true : false;

// Font size for the Note text | PC
$note_text_font_size = (isset($options['note_text_font_size']) && $options['note_text_font_size'] != '') ? absint(esc_attr($options['note_text_font_size'])) : '14';

// Font size for the Note text | Mobile
$note_text_mobile_font_size = (isset($options['note_text_mobile_font_size']) && $options['note_text_mobile_font_size'] != '') ? absint(esc_attr($options['note_text_mobile_font_size'])) : $note_text_font_size;

// Enable questions numbering by category
$options['quiz_questions_numbering_by_category'] = isset($options['quiz_questions_numbering_by_category']) ? sanitize_text_field($options['quiz_questions_numbering_by_category']) : 'off';
$quiz_questions_numbering_by_category = (isset($options['quiz_questions_numbering_by_category']) && $options['quiz_questions_numbering_by_category'] == 'on') ? true : false;

// Questions text to speech enable
$options[ 'quiz_question_text_to_speech' ] = isset($options[ 'quiz_question_text_to_speech' ]) ? $options[ 'quiz_question_text_to_speech' ] : 'off';
$quiz_question_text_to_speech = (isset($options[ 'quiz_question_text_to_speech' ]) && $options[ 'quiz_question_text_to_speech' ] == 'on') ? true : false;

// Disable input focusing
$options[ 'quiz_disable_input_focusing' ] = isset($options[ 'quiz_disable_input_focusing' ]) ? $options[ 'quiz_disable_input_focusing' ] : 'off';
$quiz_disable_input_focusing = (isset($options[ 'quiz_disable_input_focusing' ]) && $options[ 'quiz_disable_input_focusing' ] == 'on') ? true : false;

// Password Import type
$quiz_password_import_type = (isset($options['quiz_password_import_type']) && ( $options['quiz_password_import_type'] ) != '' ) ? esc_attr( $options['quiz_password_import_type'] ) : "default";

// Show all conditions results
$options[ 'quiz_condition_show_all_results' ] = isset($options[ 'quiz_condition_show_all_results' ]) ? $options[ 'quiz_condition_show_all_results' ] : 'off';
$quiz_condition_show_all_results = (isset($options[ 'quiz_condition_show_all_results' ]) && $options[ 'quiz_condition_show_all_results' ] == 'on') ? true : false;

// Condition calculation type
$quiz_condition_calculation_type = (isset($options['quiz_condition_calculation_type']) && $options['quiz_condition_calculation_type'] != '') ? sanitize_text_field( $options['quiz_condition_calculation_type'] ) : 'default';
