<?php
if(isset($_GET['ays_quiz_tab'])){
    $ays_quiz_tab = $_GET['ays_quiz_tab'];
}else{
    $ays_quiz_tab = 'tab1';
}
$action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading = '';

$id = (isset($_GET['quiz'])) ? absint(intval($_GET['quiz'])) : null;

$user_id = get_current_user_id();
$user = get_userdata($user_id);

$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);

$quiz = array(
    'title' => '',
    'description' => '',
    'quiz_image' => '',
    'quiz_category_id' => '',
    'question_ids' => '',
    'published' => ''
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
    'hide_score' => '',
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
    'create_date' => current_time( 'mysql' ),
    'author' => $author,
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
        'twitter_link' => ''
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
        
    //Answers styles
    'answers_padding' => '10',
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


    // Develpoer version options
    'enable_copy_protection' => '',
	'activeInterval' => '',
	'deactiveInterval' => '',
	'active_date_check' => 'off',
	'active_date_message' => __("The quiz has expired!", $this->plugin_name),
    'checkbox_score_by' => 'on',
    'calculate_score' => 'by_correctness',
    'question_bank_type' => 'general',
    'enable_tackers_count' => 'off',
    'tackers_count' => '',
    'show_interval_message' => 'on',
    
    // Integration option
    'enable_paypal' => '',
    'paypal_amount' => '',
    'paypal_currency' => '',
    'enable_mailchimp' => '',
    'mailchimp_list' => '',
	'enable_monitor' => '',
	'monitor_list' => '',
	'enable_slack' => '',
	'slack_conversation' => '',
	'active_camp_list' => '',
	'active_camp_automation' => '',
	'enable_active_camp' => '',
    
    // Email config options
    'send_results_user' => 'off', //AV
    'send_interval_msg' => 'off',
    'send_results_admin' => 'on',
    'send_interval_msg_to_admin' => 'off',
    'additional_emails' => '',
    'email_config_from_email' => '',
    'email_config_from_name' => '',
    'email_config_from_subject' => '',
    
    'quiz_attributes' => array(),
    "certificate_title" => '<span style="font-size:50px; font-weight:bold">Certificate of Completion</span>',
    "certificate_body" => '<span style="font-size:25px"><i>This is to certify that</i></span><br><br>
            <span style="font-size:30px"><b>%%user_name%%</b></span><br/><br/>
            <span style="font-size:25px"><i>has completed the quiz</i></span><br/><br/>
            <span style="font-size:30px">"%%quiz_name%%"</span> <br/><br/>
            <span style="font-size:20px">with a score of <b>%%score%%</b></span><br/><br/>
            <span style="font-size:25px"><i>dated</i></span><br>
            <span style="font-size:30px">%%current_date%%</span><br/><br/><br/>'
);
$question_ids = '';
$question_id_array = array();
$quiz_intervals_defaults = array(
    array(
        'interval_min' => '0',
        'interval_max' => '25',
        'interval_text' => '',
        'interval_image' => ''
    ),
    array(
        'interval_min' => '26',
        'interval_max' => '50',
        'interval_text' => '',
        'interval_image' => ''
    ),
    array(
        'interval_min' => '51',
        'interval_max' => '75',
        'interval_text' => '',
        'interval_image' => ''
    ),
    array(
        'interval_min' => '76',
        'interval_max' => '100',
        'interval_text' => '',
        'interval_image' => ''
    ),
);
$quiz_intervals_numbers = 4;
switch ($action) {
    case 'add':
        $heading = __('Add new quiz', $this->plugin_name);
        $quiz_intervals = $quiz_intervals_defaults;
        break;
    case 'edit':
        $heading = __('Edit quiz', $this->plugin_name);
        $quiz = $this->quizes_obj->get_quiz_by_id($id);
        $options = json_decode($quiz['options'], true);
        $question_ids = $quiz['question_ids'];
        $question_id_array = explode(',', $question_ids);
        $question_id_array = ($question_id_array[0] == '' && count($question_id_array) == 1) ? array() : $question_id_array;
        $quiz_intervals = json_decode($quiz['intervals'], true);
        $post_id = $quiz['post_id'];
        $ays_quiz_view_post_url = get_permalink($post_id);
        $ays_quiz_edit_post_url = get_edit_post_link($post_id);     

        break;
}
$quiz_intervals = ($quiz_intervals == null) ? $quiz_intervals_defaults : $quiz_intervals;
$questions = $this->quizes_obj->get_published_questions();
$total_questions_count = $this->quizes_obj->published_questions_record_count();
$quiz_categories = $this->quizes_obj->get_quiz_categories();
$question_categories = $this->quizes_obj->get_question_categories();
$used_questions = $this->get_published_questions_used();
$question_bank_categories = $this->quizes_obj->get_question_bank_categories($question_ids);

$settings_options = $this->settings_obj->ays_get_setting('options');
if($settings_options){
    $settings_options = json_decode($settings_options, true);
}else{
    $settings_options = array();
}
$right_answer_sound = (isset($settings_options['right_answer_sound']) && $settings_options['right_answer_sound'] != '') ? true : false;
$wrong_answer_sound = (isset($settings_options['wrong_answer_sound']) && $settings_options['wrong_answer_sound'] != '') ? true : false;
$rw_answers_sounds_status = false;
if($right_answer_sound && $wrong_answer_sound){
    $rw_answers_sounds_status = true;
}


$quiz_integrations = (get_option( 'ays_quiz_integrations' ) != null) ? json_decode( get_option( 'ays_quiz_integrations' ), true ) : array();
$quiz_paypal = array(
    'state' => (isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '') ? true : false,
    'clientId' => isset($quiz_integrations['paypal_client_id']) ? $quiz_integrations['paypal_client_id'] : null,
);

$quiz_settings = $this->settings_obj;
$mailchimp_res = ($quiz_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mailchimp');
$mailchimp = json_decode($mailchimp_res, true);
$mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
$mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
$mailchimp_lists = $this->ays_get_mailchimp_lists($mailchimp_username, $mailchimp_api_key);

$mailchimp_select = array();
if($mailchimp_lists['total_items'] > 0){
    foreach($mailchimp_lists['lists'] as $list){
        $mailchimp_select[] = array(
            'listId' => $list['id'],
            'listName' => $list['name']
        );
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
//var_dump($slack_conversations);
//die();

$active_camp_res               = ($quiz_settings->ays_get_setting('active_camp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('active_camp');
$active_camp                   = json_decode($active_camp_res, true);
$active_camp_url               = isset($active_camp['url']) ? $active_camp['url'] : '';
$active_camp_api_key           = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';
$active_camp_lists             = $this->ays_get_active_camp_data('lists', $active_camp_url, $active_camp_api_key);
$active_camp_automations       = $this->ays_get_active_camp_data('automations', $active_camp_url, $active_camp_api_key);
$active_camp_list_select       = !isset($active_camp_lists['Code']) ? $active_camp_lists['lists'] : __("There are no lists", $this->plugin_name);
$active_camp_automation_select = !isset($active_camp_automations['Code']) ? $active_camp_automations['automations'] : __("There are no automations", $this->plugin_name);

$zapier_res  = ($quiz_settings->ays_get_setting('zapier') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('zapier');
$zapier      = json_decode($zapier_res, true);
$zapier_hook = isset($zapier['hook']) ? $zapier['hook'] : '';


if (isset($_POST['ays_submit']) || isset($_POST['ays_submit_top'])) {
    $_POST['id'] = $id;
    $this->quizes_obj->add_or_edit_quizes($_POST);
}
if (isset($_POST['ays_apply_top']) || isset($_POST['ays_apply'])) {
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->quizes_obj->add_or_edit_quizes($_POST);
}

$style = null;
$image_text = __('Add Image', $this->plugin_name);
$bg_image_text = __('Add Image', $this->plugin_name);
if ($quiz['quiz_image'] != '') {
    $style = "display: block;";
    $image_text = __('Edit Image', $this->plugin_name);
}

global $wp_roles;
$ays_users_roles = $wp_roles->roles;


$all_attributes = $this->quizes_obj->get_al_attributes();
$quiz_attributes = (isset($options['quiz_attributes'])) ? $options['quiz_attributes'] : array();
$required_fields = (isset($options['required_fields'])) ? $options['required_fields'] : array();
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

// MailChimp
$enable_mailchimp = (isset($options['enable_mailchimp']) && $options['enable_mailchimp'] == 'on') ? true : false;
$mailchimp_list = (isset($options['mailchimp_list'])) ? $options['mailchimp_list'] : '';

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


$enable_box_shadow = (!isset($options['enable_box_shadow'])) ? 'on' : $options['enable_box_shadow'];
$box_shadow_color = (!isset($options['box_shadow_color'])) ? '#000' : $options['box_shadow_color'];
$quiz_border_radius = (isset($options['quiz_border_radius']) && $options['quiz_border_radius'] != '') ? $options['quiz_border_radius'] : '0';
$quiz_bg_image = (isset($options['quiz_bg_image']) && $options['quiz_bg_image'] != '') ? $options['quiz_bg_image'] : '';
$enable_border = (isset($options['enable_border']) && $options['enable_border'] == 'on') ? true : false;
$quiz_border_width = (isset($options['quiz_border_width']) && $options['quiz_border_width'] != '') ? $options['quiz_border_width'] : '1';
$quiz_border_style = (isset($options['quiz_border_style']) && $options['quiz_border_style'] != '') ? $options['quiz_border_style'] : 'solid';
$quiz_border_color = (isset($options['quiz_border_color']) && $options['quiz_border_color'] != '') ? $options['quiz_border_color'] : '#000';
$quiz_timer_in_title = (isset($options['quiz_timer_in_title']) && $options['quiz_timer_in_title'] == 'on') ? true : false;
$enable_restart_button = (isset($options['enable_restart_button']) && $options['enable_restart_button'] == 'on') ? true : false;

$rate_form_title = (isset($options['rate_form_title'])) ? $options['rate_form_title'] : __('Please click the stars to rate the quiz', $this->plugin_name);
$quiz_loader = (isset($options['quiz_loader']) && $options['quiz_loader'] != '') ? $options['quiz_loader'] : 'default';

$quiz_create_date = (isset($options['create_date']) && $options['create_date'] != '') ? $options['create_date'] : "0000-00-00 00:00:00";
if(isset($options['author']) && $options['author'] != 'null'){
    $quiz_author = $options['author'];
} else {
    $quiz_author = array('name' => 'Unknown');
}

$autofill_user_data = (isset($options['autofill_user_data']) && $options['autofill_user_data'] == 'on') ? true : false;

$quest_animation = (isset($options['quest_animation'])) ? $options['quest_animation'] : "shake";
$enable_bg_music = (isset($options['enable_bg_music']) && $options['enable_bg_music'] == "on") ? true : false;
$quiz_bg_music = (isset($options['quiz_bg_music']) && $options['quiz_bg_music'] != "") ? $options['quiz_bg_music'] : "";
$answers_font_size = (isset($options['answers_font_size']) && $options['answers_font_size'] != "") ? $options['answers_font_size'] : '15';

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
$background_gradient_color_1 = (isset($options['background_gradient_color_1']) && $options['background_gradient_color_1'] != '') ? $options['background_gradient_color_1'] : '#000';
$background_gradient_color_2 = (isset($options['background_gradient_color_2']) && $options['background_gradient_color_2'] != '') ? $options['background_gradient_color_2'] : '#fff';
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
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1
    );
    $products = get_posts($args);
    foreach($products as $key => $prod){
        $image_url = get_the_post_thumbnail_url( $prod->ID, 'thumbnail' );
        $prod->image = $image_url;
    }
    $wc_for_js = "with-woo-product";
    wp_localize_script($this->plugin_name, 'quiz_wc_products', $products);
}

// Email configuration

$additional_emails = (isset($options['additional_emails']) && $options['additional_emails'] != '') ? $options['additional_emails'] : '';
$email_config_from_email = (isset($options['email_config_from_email']) && $options['email_config_from_email'] != '') ? $options['email_config_from_email'] : '';
$email_config_from_name = (isset($options['email_config_from_name']) && $options['email_config_from_name'] != '') ? stripslashes(htmlentities($options['email_config_from_name'], ENT_QUOTES, "UTF-8")) : '';
$email_config_from_subject = (isset($options['email_config_from_subject']) && $options['email_config_from_subject'] != '') ? stripslashes(htmlentities($options['email_config_from_subject'], ENT_QUOTES, "UTF-8")) : '';

// Calculate the score option
$options['calculate_score'] = (!isset($options['calculate_score'])) ? 'by_correctness' : $options['calculate_score'];
$calculate_score = (isset($options['calculate_score']) && $options['calculate_score'] != '') ? $options['calculate_score'] : 'by_correctness';

// Quiz theme
$quiz_theme = isset($options['quiz_theme']) && $options['quiz_theme'] != "" ? $options['quiz_theme'] : 'classic_light';

// Redirect after submit
$options['redirect_after_submit'] = (!isset($options['redirect_after_submit'])) ? 'off' : $options['redirect_after_submit'];
$redirect_after_submit = isset($options['redirect_after_submit']) && $options['redirect_after_submit'] == 'on' ? true : false;
$submit_redirect_url = isset($options['submit_redirect_url']) ? $options['submit_redirect_url'] : '';
$submit_redirect_delay = isset($options['submit_redirect_delay']) ? $options['submit_redirect_delay'] : '';

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
    'twitter_link' => ''
);
$linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? $social_links['linkedin_link'] : '';
$facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? $social_links['facebook_link'] : '';
$twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? $social_links['twitter_link'] : '';

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
$answers_padding = (isset($options['answers_padding']) && $options['answers_padding'] != '') ? $options['answers_padding'] : '10';
// Answers margin option
$answers_margin = (isset($options['answers_margin']) && $options['answers_margin'] != '') ? $options['answers_margin'] : '10';
// Answers border options
$options['answers_border'] = (isset($options['answers_border'])) ? $options['answers_border'] : 'on';
$answers_border = (isset($options['answers_border']) && $options['answers_border'] == 'on') ? true : false;
$answers_border_width = (isset($options['answers_border_width']) && $options['answers_border_width'] != '') ? $options['answers_border_width'] : '1';
$answers_border_style = (isset($options['answers_border_style']) && $options['answers_border_style'] != '') ? $options['answers_border_style'] : 'solid';
$answers_border_color = (isset($options['answers_border_color']) && $options['answers_border_color'] != '') ? $options['answers_border_color'] : '#444';

$answers_box_shadow = (isset($options['answers_box_shadow']) && $options['answers_box_shadow'] == 'on') ? true : false;
$answers_box_shadow_color = (isset($options['answers_box_shadow_color']) && $options['answers_box_shadow_color'] != '') ? $options['answers_box_shadow_color'] : '#000';

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

// Display score option
$display_score = (isset($options['display_score']) && $options['display_score'] != "") ? $options['display_score'] : 'by_percentage';

// Right / Wrong answers sound option
$options['enable_rw_asnwers_sounds'] = isset($options['enable_rw_asnwers_sounds']) ? $options['enable_rw_asnwers_sounds'] : 'off';
$enable_rw_asnwers_sounds = (isset($options['enable_rw_asnwers_sounds']) && $options['enable_rw_asnwers_sounds'] == "on") ? true : false;


?>


<style id="ays_live_custom_css"></style>
<div class="wrap">
    <div class="container-fluid">
        <form class="ays-quiz-category-form" id="ays-quiz-category-form" method="post">
            <input type="hidden" name="ays_quiz_tab" value="<?php echo $ays_quiz_tab; ?>">
            <input type="hidden" name="ays_quiz_ctrate_date" value="<?php echo $quiz_create_date; ?>">
            <input type="hidden" name="ays_quiz_author" value="<?php echo htmlentities(json_encode($quiz_author)); ?>">
            <h1 class="wp-heading-inline">
                <?php
                echo $heading;
                $other_attributes = array();
                submit_button(__('Save and close', $this->plugin_name), 'primary', 'ays_submit_top', false, $other_attributes);
                submit_button(__('Save', $this->plugin_name), '', 'ays_apply_top', false, $other_attributes);

                ?>
            </h1>
            <div>
                <p class="ays-subtitle">
                    <strong class="ays_quiz_title_in_top"><?php echo stripslashes(htmlentities($quiz['title'])); ?></strong>
                </p>
                <?php if($id !== null): ?>
                <div class="row">
                    <div class="col-sm-3">
                        <label> <?php echo __( "Shortcode text for editor", $this->plugin_name ); ?> </label>
                    </div>
                    <div class="col-sm-9">
                        <p style="font-size:14px; font-style:italic;">
                            <?php echo __("To insert the Quiz into a page, post or text widget, copy shortcode", $this->plugin_name); ?>
                            <strong onClick="selectElementContents(this)" style="font-size:16px; font-style:normal;"><?php echo "[ays_quiz id='".$id."']"; ?></strong>
                            <?php echo " " . __( "and paste it at the desired place in the editor.", $this->plugin_name); ?>
                        </p>
                    </div>
                </div>
                <?php endif;?>
            </div>
            <hr/>

            <div class="ays-top-menu-wrapper">
                <div class="ays_menu_left" data-scroll="0"><i class="ays_fa ays_fa_angle_left"></i></div>
                <div class="ays-top-menu">
                    <div class="nav-tab-wrapper ays-top-tab-wrapper">
                        <a href="#tab1" data-tab="tab1" class="nav-tab <?php echo ($ays_quiz_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("General", $this->plugin_name);?>
                        </a>
                        <a href="#tab2" data-tab="tab2" class="nav-tab <?php echo ($ays_quiz_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Styles", $this->plugin_name);?>
                        </a>
                        <a href="#tab3" data-tab="tab3" class="nav-tab <?php echo ($ays_quiz_tab == 'tab3') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Settings", $this->plugin_name);?>
                        </a>
                        <a href="#tab4" data-tab="tab4" class="nav-tab <?php echo ($ays_quiz_tab == 'tab4') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Results Settings", $this->plugin_name);?>
                        </a>
                        <a href="#tab5" data-tab="tab5" class="nav-tab <?php echo ($ays_quiz_tab == 'tab5') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Limitation Users", $this->plugin_name);?>
                        </a>
                        <a href="#tab6" data-tab="tab6" class="nav-tab <?php echo ($ays_quiz_tab == 'tab6') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("User Data", $this->plugin_name);?>
                        </a>
                        <a href="#tab7" data-tab="tab7" class="nav-tab <?php echo ($ays_quiz_tab == 'tab7') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("E-Mail, Certificate", $this->plugin_name);?>
                        </a>
                        <a href="#tab8" data-tab="tab8" class="nav-tab <?php echo ($ays_quiz_tab == 'tab8') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Integrations", $this->plugin_name);?>
                        </a>
                    </div>  
                </div>
                <div class="ays_menu_right" data-scroll="-1"><i class="ays_fa ays_fa_angle_right"></i></div>
            </div>

            <div id="tab1" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab1') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label for='ays-quiz-title'>
                            <?php echo __('Title', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the quiz',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="ays-text-input" id='ays-quiz-title' name='ays_quiz_title'
                               value="<?php echo stripslashes(htmlentities($quiz['title'])); ?>"/>
                    </div>
                </div>
                <hr/>
                <div class='ays-field-dashboard'>
                    <label>
                        <?php echo __('Quiz image', $this->plugin_name); ?>
                        <a href="javascript:void(0)" class="add-quiz-image"><?php echo $image_text; ?></a>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Add image to the starting page of the quiz',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <div class="ays-quiz-image-container" style="<?php echo $style; ?>">
                        <span class="ays-remove-quiz-img"></span>
                        <img src="<?php echo $quiz['quiz_image']; ?>" id="ays-quiz-img"/>
                    </div>
                </div>
                <hr/>
                <input type="hidden" name="ays_quiz_image" id="ays-quiz-image" value="<?php echo $quiz['quiz_image']; ?>"/>
                <div class='ays-field-dashboard'>
                    <label for='ays-quiz-description'>
                        <?php echo __('Description', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide more information about the quiz. You can choose whether to show it or not in the front end in the “Settings” tab.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <?php
                        $content = stripslashes((wpautop($quiz['description'])));
                        $editor_id = 'ays-quiz-description';
                        $settings = array(
                            'editor_height' => '8',
                            'textarea_name' => 'ays_quiz_description',
                            'editor_class' => 'ays-textarea',
                            'media_buttons' => true
                        );
                        wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label for="ays-category">
                            <?php echo __('Quiz category', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Category of the quiz. For making a category please visit Quiz Categories page from left navbar',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-10">
                        <select id="ays-category" name="ays_quiz_category">
                            <option></option>
                            <?php
                            $cat = 0;
                            foreach ($quiz_categories as $key => $quiz_category) {
                                $selected = (intval($quiz_category['id']) == intval($quiz['quiz_category_id'])) ? "selected" : "";
                                if ($cat == 0 && intval($quiz['quiz_category_id']) == 0) {
                                    $selected = 'selected';
                                }
                                echo '<option value="' . $quiz_category["id"] . '" ' . $selected . '>' . stripslashes($quiz_category['title']) . '</option>';
                                $cat++;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label>
                            <?php echo __('Quiz status', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the quiz is active or not.If you choose Unpublished option, the quiz won’t be shown anywhere in your website (You don’t need to remove shortcodes).',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-publish" name="ays_publish"
                                   value="1" <?php echo ($quiz["published"] == '') ? "checked" : ""; ?>  <?php echo ($quiz["published"] == '1') ? 'checked' : ''; ?>/>
                            <label class="form-check-label"
                                   for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-unpublish" name="ays_publish"
                                   value="0" <?php echo ($quiz["published"] == '0') ? 'checked' : ''; ?>/>
                            <label class="form-check-label"
                                   for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                        </div>
                    </div>
                </div>
                <hr/>
                <?php if($id === null): ?>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_add_post_for_quiz">
                            <?php echo __('Create post for quiz',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('A new WordPress post will be created automatically and will include the shortcode of this quiz. This function will be executed only once. You can find this post on Posts page, which will have the same title as the quiz. The image of the quiz will be the featured image of the post.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_add_post_for_quiz" name="ays_add_post_for_quiz" value="on" class="ays_toggle_checkbox" checked/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_add_postcat_for_quiz">
                                    <?php echo __('Choose Post Categories',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose one or several categories. These categories are WordPress default post categories. There is no connection with quiz categories.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">                        
                                <div class="input-group">
                                    <select name="ays_add_postcat_for_quiz[]" 
                                            id="ays_add_postcat_for_quiz"
                                            class="ays_postcat_for_quiz" 
                                            multiple>
                                <?php

                                    foreach ($cat_list as $cat) {          
                                        echo "<option value='" . $cat->cat_ID . "' >" . $cat->name . "</option>";
                                    }
                                ?>
                                    </select>
                                </div>
                            </div>
                        </div>                   
                    </div>                   
                </div>
                <hr/>
                <?php elseif($post_id !== null): ?>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_add_post_for_quiz">
                                <?php echo __('WP Post',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Via these two links you can see the connected post in front end and make changes in the dashboard.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-10">
                            <div class="row">
                                <div style="margin-right: 10px;">
                                    <a class="button" href="<?php echo $ays_quiz_view_post_url; ?>" target="_blank">View Post <i class="ays_fa ays_fa_external_link"></i></a>
                                </div>
                                <div>
                                    <a class="button" href="<?php echo $ays_quiz_edit_post_url; ?>" target="_blank">Edit Post <i class="ays_fa ays_fa_external_link"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php endif; ?>
                <div class='ays-field-dashboard ays_items_count_div'>
                    <div style='display: flex;align-items: center;margin-right: 15px;'>
                        <a href="javascript:void(0)" class="ays-add-question">
                            <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            <?php echo __('Add questions', $this->plugin_name); ?>
                        </a>
                        <a class="ays_help" style="font-size:15px;" data-placement="bottom" data-html="true" data-toggle="tooltip" title="<?php echo "<p style='margin:0;text-indent:7px;'>".__('For adding questions to the quiz you need to make questions first from the “Questions page” in the left navbar. After popup’s opening, you can filter and select your prepared questions for this quiz.', $this->plugin_name)."</p><p style='margin:0;text-indent:7px;'>".__('The ordering of the questions will be the same as you chose. Also, you can reorder them after selection. There are no limitations for questions quantity.', $this->plugin_name)."</p>"; ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </div>
                    <p class="ays_questions_action">
                        <span class="ays_questions_count">
                            <?php
                            echo '<span class="questions_count_number">' . count($question_id_array) . '</span> '. __('items',$this->plugin_name);
                            ?>
                        </span>
                        <button class="ays_bulk_del_questions button" type="button" disabled>
                            <?php echo __( 'Delete', $this->plugin_name); ?>                            
                        </button>
                        <button class="ays_select_all button" type="button">
                            <?php echo __( 'Select All', $this->plugin_name); ?>                            
                        </button>
                    </p>
                </div>
                <div class="ays-field-dashboard ays-table-wrap" style="padding-top: 15px;">
                    <table class="ays-questions-table" id="ays-questions-table">
                        <thead>
                        <tr class="ui-state-default">
                            <th class="th-150"><?php echo __('Ordering', $this->plugin_name); ?></th>
                            <th class="th-650"><?php echo __('Question', $this->plugin_name); ?></th>
                            <th class="th-150"><?php echo __('Type', $this->plugin_name); ?></th>
                            <th class="th-150"><?php echo __('ID', $this->plugin_name); ?></th>
                            <th class="th-150" style="min-width:100px;"><?php echo __('Delete', $this->plugin_name); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!(count($question_id_array) === 1 && $question_id_array[0] == '')) {
                            foreach ($question_id_array as $key => $question_id) {
                                $data = $this->quizes_obj->get_published_questions_by('id', absint(intval($question_id)));
                                $className = "";
                                if (($key + 1) % 2 == 0) {
                                    $className = "even";
                                }
                                $table_question = (strip_tags(stripslashes($data['question'])));
                                $table_question = $this->ays_restriction_string("word",$table_question, 10);
                                ?>
                                <tr class="ays-question-row ui-state-default <?php echo $className; ?>"
                                    data-id="<?php echo $data['id']; ?>">
                                    <td class="ays-sort"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td><?php echo $table_question ?></td>
                                    <td>
                                        <?php echo $data['type']; ?>
                                        <input type="hidden" name="ays_question_type[<?php echo $data['type']; ?>][]" value="<?php echo $data['id']; ?>">
                                    </td>
                                    <td><?php echo $data['id']; ?></td>
                                    <td>
                                        <input type="checkbox" class="ays_del_tr" style="margin-right:15px;">
                                        <a href="javascript:void(0)" class="ays-delete-question"
                                           data-id="<?php echo $data['id']; ?>">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        if(empty($question_id_array)){                            
                            ?>
                            <tr class="ays-question-row ui-state-default">
                                <td colspan="5" class="empty_quiz_td">
                                    <div>
                                        <i class="ays_fa ays_fa_info" aria-hidden="true" style="margin-right:10px"></i>
                                        <span style="font-size: 13px; font-style: italic;">
                                        <?php
                                            echo __( 'There are no questions yet.', $this->plugin_name );
                                        ?>
                                        </span>
                                        <a class="create_question_link" href="admin.php?page=<?php echo $this->plugin_name; ?>-questions&action=add" target="_blank"><?php echo __('Create question', $this->plugin_name); ?></a>
                                    </div>
                                    <div class='ays_add_question_from_table'>                                        
                                        <a href="javascript:void(0)" class="ays-add-question">
                                            <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                            <?php echo __('Add questions', $this->plugin_name); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <p class="ays_questions_action" style="width:100%;">                
                        <span class="ays_questions_count">
                            <?php
                            echo '<span class="questions_count_number">' . count($question_id_array) . '</span> '. __('items',$this->plugin_name);
                            ?>
                        </span>
                        <button class="ays_bulk_del_questions button" type="button" disabled>
                            <?php echo __( 'Delete', $this->plugin_name); ?>                            
                        </button>
                        <button class="ays_select_all button" type="button">
                            <?php echo __( 'Select All', $this->plugin_name); ?>                            
                        </button>
                    </p>
                </div>
                <input type="hidden" id="ays_already_added_questions" name="ays_added_questions"
                       value="<?php echo $question_ids; ?>"/>
            </div>
            <div id="tab2" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab2') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Quiz Styles',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label>
                            <?php echo __('Quiz Theme', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose your preferred template and customize it with options below Elegant Dark, Elegant Light, Classic Dark, Classic Light, Rect Dark, Rect Light, Modern Light and Modern Dark',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-group row ays_themes_images_main_div">
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'elegant_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_elegant_dark" class="ays-quiz-theme-item">
                                    <p><?php echo __('Elegant Dark',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/elegant_dark.JPG' ?>" alt="Elegant Dark">
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'elegant_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_elegant_light" class="ays-quiz-theme-item">
                                    <p><?php echo __('Elegant Light',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/elegant_light.JPG' ?>" alt="Elegant Light">
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'classic_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_classic_dark" class="ays-quiz-theme-item">
                                    <p><?php echo __('Classic Dark',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/classic_dark.jpg' ?>" alt="Classic Dark">
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'classic_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_classic_light" class="ays-quiz-theme-item">
                                    <p><?php echo __('Classic Light',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/classic_light.jpg' ?>" alt="Classic Light">
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'rect_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_rect_dark" class="ays-quiz-theme-item">
                                    <p><?php echo __('Rect Dark',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/rect_dark.JPG' ?>" alt="Rect Dark" >
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'rect_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_rect_light" class="ays-quiz-theme-item">
                                    <p><?php echo __('Rect Light',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/rect_light.JPG' ?>" alt="Rect Light" >
                                </label>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row ays_themes_images_main_div">
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'modern_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_modern_light" class="ays-quiz-theme-item">
                                    <p><?php echo __('Modern Light',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/modern_light.jpg' ?>" alt="Modern Light" >
                                </label>
                            </div>
                            <div class="ays_theme_image_div col-sm-2 <?php echo ($quiz_theme == 'modern_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                                <label for="theme_modern_dark" class="ays-quiz-theme-item">
                                    <p><?php echo __('Modern Dark',$this->plugin_name)?></p>
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/modern_dark.jpg' ?>" alt="Modern Dark" >
                                </label>
                            </div>
                        </div>
                        <input type="checkbox" id="theme_elegant_dark" name="ays_quiz_theme" value="elegant_dark" <?php echo ($quiz_theme == 'elegant_dark') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_elegant_light" name="ays_quiz_theme" value="elegant_light" <?php echo ($quiz_theme == 'elegant_light') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_classic_dark" name="ays_quiz_theme" value="classic_dark" <?php echo ($quiz_theme == 'classic_dark') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_classic_light" name="ays_quiz_theme" value="classic_light" <?php echo ($quiz_theme == 'classic_light') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_rect_dark" name="ays_quiz_theme" value="rect_dark" <?php echo ($quiz_theme == 'rect_dark') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_rect_light" name="ays_quiz_theme" value="rect_light" <?php echo ($quiz_theme == 'rect_light') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_modern_light" name="ays_quiz_theme" value="modern_light" <?php echo ($quiz_theme == 'modern_light') ? 'checked' : '' ?>>
                        <input type="checkbox" id="theme_modern_dark" name="ays_quiz_theme" value="modern_dark" <?php echo ($quiz_theme == 'modern_dark') ? 'checked' : '' ?>>

                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-7 col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays_quest_animation'>
                                    <?php echo __('Animation effect', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Animation effect of transition between questions.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <select class="ays-text-input ays-text-input-short" name="ays_quest_animation" id="ays_quest_animation">
                                    <option <?php echo $quest_animation == "none" ? "selected" : ""; ?> value="none">None</option>
                                    <option <?php echo $quest_animation == "fade" ? "selected" : ""; ?> value="fade">Fade</option>
                                    <option <?php echo $quest_animation == "shake" ? "selected" : ""; ?> value="shake">Shake</option>
                                    <option <?php echo $quest_animation == "rswing" ? "selected" : ""; ?> value="rswing">Swing right</option>
                                    <option <?php echo $quest_animation == "lswing" ? "selected" : ""; ?> value="lswing">Swing left</option>
                                </select>
                                <button type="button" style="height:100%;" class="button ays_animate_animation"><?php echo __('Animate!', $this->plugin_name); ?></button>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays-quiz-color'>
                                    <?php echo __('Quiz Color', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Colors of the quiz main attributes(buttons, hover effect, progress bar, etc.)',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="text" class="ays-text-input" id='ays-quiz-color' name='ays_quiz_color' data-alpha="true"
                                       value="<?php echo (isset($options['color'])) ? $options['color'] : ''; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays-quiz-bg-color'>
                                    <?php echo __('Quiz Background Color', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background color of the quiz box. You can also choose the opacity(alfa) level on the right side',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="text" class="ays-text-input" id='ays-quiz-bg-color' data-alpha="true"
                                       name='ays_quiz_bg_color'
                                       value="<?php echo (isset($options['bg_color'])) ? $options['bg_color'] : ''; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays-quiz-bg-color'>
                                    <?php echo __('Text Color', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the text color inside the quiz and questions. It affects all kinds of texts and icons, including buttons.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="text" class="ays-text-input" id='ays-quiz-text-color' data-alpha="true"
                                       name='ays_quiz_text_color'
                                       value="<?php echo (isset($options['text_color'])) ? $options['text_color'] : ''; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays-quiz-width'>
                                    <?php echo __('Quiz width', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container width in pixels.Set it 0 or leave it blank for making a quiz with 100% width. It accepts only numeric values.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays-quiz-width'
                                       name='ays_quiz_width'
                                       value="<?php echo (isset($options['width'])) ? $options['width'] : ''; ?>"/>
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays_mobile_max_width'>
                                    <?php echo __('Quiz max-width for mobile', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container max-width for mobile in percentage. This option will work for the screens with less than 640 pixels width.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_mobile_max_width'
                                       name='ays_mobile_max_width' style="display:inline-block;"
                                       value="<?php echo $mobile_max_width; ?>"/> %
                                       <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays-quiz-height'>
                                    <?php echo __('Quiz min height', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz minimal height in pixels',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short"
                                       id='ays-quiz-height'
                                       name='ays_quiz_height'
                                       value="<?php echo (isset($options['height'])) ? $options['height'] : ''; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label>
                                    <?php echo __('Questions Image Styles',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It affects the images chosen from “Add Image” not from “Add media” on the Edit question page',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_image_width">
                                            <?php echo __('Image Width',$this->plugin_name)?>(px)
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Question image width in pixels. Set it 0 or leave it blank for making it 100%. It accepts only numeric values.',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <input type="number" class="ays-text-input ays-text-input-short" id="ays_image_width" name="ays_image_width" value="<?php echo (isset($options['image_width']) && $options['image_width'] != '') ? $options['image_width'] : ''; ?>"/>
                                        <span class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_image_height">
                                            <?php echo __('Image Height',$this->plugin_name)?>(px)
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Question image height in pixels. It accepts only number values.',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <input type="number" class="ays-text-input ays-text-input-short" id="ays_image_height" name="ays_image_height" value="<?php echo (isset($options['image_height']) && $options['image_height'] != '') ? $options['image_height'] : ''; ?>"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_image_sizing">
                                            <?php echo __('Image sizing', $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It helps to configure the scale of the images inside the quiz in case of differences between the sizes',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <select name="ays_image_sizing" id="ays_image_sizing" class="ays-text-input ays-text-input-short" style="display:block;">
                                            <option value="cover" <?php echo $image_sizing == 'cover' ? 'selected' : ''; ?>><?php echo __( "Cover", $this->plugin_name ); ?></option>
                                            <option value="contain" <?php echo $image_sizing == 'contain' ? 'selected' : ''; ?>><?php echo __( "Contain", $this->plugin_name ); ?></option>
                                            <option value="none" <?php echo $image_sizing == 'none' ? 'selected' : ''; ?>><?php echo __( "None", $this->plugin_name ); ?></option>
                                            <option value="unset" <?php echo $image_sizing == 'unset' ? 'selected' : ''; ?>><?php echo __( "Unset", $this->plugin_name ); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_enable_border">
                                    <?php echo __('Quiz container border',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow quiz container border',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                       id="ays_enable_border"
                                       name="ays_enable_border"
                                       value="on"
                                       <?php echo ($enable_border) ? 'checked' : ''; ?>/>
                                <label for="ays_enable_border" class="ays_switch_toggle">Toggle</label>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_quiz_border_width">
                                        <?php echo __('Border width',$this->plugin_name)?> (px)
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of quiz container border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                     </label>
                                    <input type="number" class="ays-text-input" id='ays_quiz_border_width'
                                           name='ays_quiz_border_width'
                                           value="<?php echo $quiz_border_width; ?>"/>
                                </div>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_quiz_border_style">
                                        <?php echo __('Border style',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The style of quiz container border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                    <select id="ays_quiz_border_style"
                                            name="ays_quiz_border_style"
                                            class="ays-text-input">
                                        <option <?php echo ($quiz_border_style == 'solid') ? 'selected' : ''; ?> value="solid">Solid</option>
                                        <option <?php echo ($quiz_border_style == 'dashed') ? 'selected' : ''; ?> value="dashed">Dashed</option>
                                        <option <?php echo ($quiz_border_style == 'dotted') ? 'selected' : ''; ?> value="dotted">Dotted</option>
                                        <option <?php echo ($quiz_border_style == 'double') ? 'selected' : ''; ?> value="double">Double</option>
                                        <option <?php echo ($quiz_border_style == 'groove') ? 'selected' : ''; ?> value="groove">Groove</option>
                                        <option <?php echo ($quiz_border_style == 'ridge') ? 'selected' : ''; ?> value="ridge">Ridge</option>
                                        <option <?php echo ($quiz_border_style == 'inset') ? 'selected' : ''; ?> value="inset">Inset</option>
                                        <option <?php echo ($quiz_border_style == 'outset') ? 'selected' : ''; ?> value="outset">Outset</option>
                                        <option <?php echo ($quiz_border_style == 'none') ? 'selected' : ''; ?> value="none">None</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_quiz_border_color">
                                        <?php echo __('Border color',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the quiz container border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                    <input id="ays_quiz_border_color" class="ays-text-input"  data-alpha="true" type="text" name='ays_quiz_border_color'
                                           value="<?php echo $quiz_border_color; ?>"
                                           data-default-color="#000000">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_quiz_border_radius">
                                    <?php echo __('Quiz border radius',$this->plugin_name)?>(px)
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container border-radius in pixels. It accepts only numeric values.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short"
                                       id="ays_quiz_border_radius"
                                       name="ays_quiz_border_radius"
                                       value="<?php echo $quiz_border_radius; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_enable_box_shadow">
                                    <?php echo __('Quiz box shadow',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow quiz container box shadow',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                       id="ays_enable_box_shadow"
                                       name="ays_enable_box_shadow"
                                       <?php echo ($enable_box_shadow == 'on') ? 'checked' : ''; ?>/>
                                <label for="ays_enable_box_shadow" class="ays_switch_toggle">Toggle</label>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_box_shadow == 'on') ? '' : 'display:none;' ?>">
                                    <label for="ays-quiz-box-shadow-color">
                                        <?php echo __('Box shadow color',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the shadow of the quiz container',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                     </label>
                                    <input type="text" class="ays-text-input" id='ays-quiz-box-shadow-color' data-alpha="true"
                                           name='ays_quiz_box_shadow_color'
                                           value="<?php echo $box_shadow_color; ?>"/>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label>
                                    <?php echo __('Quiz background image',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background image of the container. You can choose different images for each question from the Settings tab on the Edit question page. The background-size is set “Cover” by default for not scaling the image.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <a href="javascript:void(0)" style="<?php echo $quiz_bg_image == '' ? 'display:inline-block' : 'display:none'; ?>" class="add-quiz-bg-image"><?php echo $bg_image_text; ?></a>
                                <input type="hidden" id="ays_quiz_bg_image" name="ays_quiz_bg_image"
                                       value="<?php echo $quiz_bg_image; ?>"/>
                                <div class="ays-quiz-bg-image-container" style="<?php echo $quiz_bg_image == '' ? 'display:none' : 'display:block'; ?>">
                                    <span class="ays-edit-quiz-bg-img">
                                        <i class="ays_fa ays_fa_pencil_square_o"></i>
                                    </span>
                                    <span class="ays-remove-quiz-bg-img"></span>
                                    <img src="<?php echo $quiz_bg_image; ?>" id="ays-quiz-bg-img"/>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_quiz_bg_image_position">
                                            <?php echo __( "Background image position", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The position of background image of the quiz',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <select id="ays_quiz_bg_image_position" name="ays_quiz_bg_image_position" class="ays-text-input ays-text-input-short" style="display:inline-block;">
                                            <option value="left top" <?php echo $quiz_bg_image_position == "left top" ? "selected" : ""; ?>><?php echo __( "Left Top", $this->plugin_name ); ?></option>
                                            <option value="left center" <?php echo $quiz_bg_image_position == "left center" ? "selected" : ""; ?>><?php echo __( "Left Center", $this->plugin_name ); ?></option>
                                            <option value="left bottom" <?php echo $quiz_bg_image_position == "left bottom" ? "selected" : ""; ?>><?php echo __( "Left Bottom", $this->plugin_name ); ?></option>
                                            <option value="center top" <?php echo $quiz_bg_image_position == "center top" ? "selected" : ""; ?>><?php echo __( "Center Top", $this->plugin_name ); ?></option>
                                            <option value="center center" <?php echo $quiz_bg_image_position == "center center" ? "selected" : ""; ?>><?php echo __( "Center Center", $this->plugin_name ); ?></option>
                                            <option value="center bottom" <?php echo $quiz_bg_image_position == "center bottom" ? "selected" : ""; ?>><?php echo __( "Center Bottom", $this->plugin_name ); ?></option>
                                            <option value="right top" <?php echo $quiz_bg_image_position == "right top" ? "selected" : ""; ?>><?php echo __( "Right Top", $this->plugin_name ); ?></option>
                                            <option value="right center" <?php echo $quiz_bg_image_position == "right center" ? "selected" : ""; ?>><?php echo __( "Right Center", $this->plugin_name ); ?></option>
                                            <option value="right bottom" <?php echo $quiz_bg_image_position == "right bottom" ? "selected" : ""; ?>><?php echo __( "Right Bottom", $this->plugin_name ); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays-enable-background-gradient">
                                    <?php echo __('Quiz background gradient',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color gradient of the quiz background',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                       id="ays-enable-background-gradient"
                                       name="ays_enable_background_gradient"
                                        <?php echo ($enable_background_gradient) ? 'checked' : ''; ?>/>
                                <label for="ays-enable-background-gradient" class="ays_switch_toggle">Toggle</label>
                                <div class="row ays_toggle_target" style="margin: 10px 0 0 0; padding-top: 10px; <?php echo ($enable_background_gradient) ? '' : 'display:none;' ?>">
                                    <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                                        <label for='ays-background-gradient-color-1'>
                                            <?php echo __('Color 1', $this->plugin_name); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color 1 of the quiz background gradient',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <input type="text" class="ays-text-input" id='ays-background-gradient-color-1' name='ays_background_gradient_color_1' data-alpha="true" value="<?php echo $background_gradient_color_1; ?>"/>
                                    </div>
                                    <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                                        <label for='ays-background-gradient-color-2'>
                                            <?php echo __('Color 2', $this->plugin_name); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color 2 of the quiz background gradient',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <input type="text" class="ays-text-input" id='ays-background-gradient-color-2' name='ays_background_gradient_color_2' data-alpha="true" value="<?php echo $background_gradient_color_2; ?>"/>
                                    </div>
                                    <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                                        <label for="ays_quiz_gradient_direction">
                                            <?php echo __('Gradient direction',$this->plugin_name)?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The direction of the color gradient.',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <select id="ays_quiz_gradient_direction" name="ays_quiz_gradient_direction" class="ays-text-input">
                                            <option <?php echo ($quiz_gradient_direction == 'vertical') ? 'selected' : ''; ?> value="vertical"><?php echo __( 'Vertical', $this->plugin_name); ?></option>
                                            <option <?php echo ($quiz_gradient_direction == 'horizontal') ? 'selected' : ''; ?> value="horizontal"><?php echo __( 'Horizontal', $this->plugin_name); ?></option>
                                            <option <?php echo ($quiz_gradient_direction == 'diagonal_left_to_right') ? 'selected' : ''; ?> value="diagonal_left_to_right"><?php echo __( 'Diagonal left to right', $this->plugin_name); ?></option>
                                            <option <?php echo ($quiz_gradient_direction == 'diagonal_right_to_left') ? 'selected' : ''; ?> value="diagonal_right_to_left"><?php echo __( 'Diagonal right to left', $this->plugin_name); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_progress_bar_style">
                                    <?php echo __('Progress bar style',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Design of the progress bar which will appear on the finish page only. It will show the user’s score.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <select id="ays_progress_bar_style" name="ays_progress_bar_style" class="ays-text-input ays-text-input-short">
                                    <option <?php echo ($progress_bar_style == 'first') ? 'selected' : ''; ?> value="first"><?php echo __( 'Rounded', $this->plugin_name); ?></option>
                                    <option <?php echo ($progress_bar_style == 'second') ? 'selected' : ''; ?> value="second"><?php echo __( 'Rectangle', $this->plugin_name); ?></option>
                                    <option <?php echo ($progress_bar_style == 'third') ? 'selected' : ''; ?> value="third"><?php echo __( 'With stripes', $this->plugin_name); ?></option>
                                    <option <?php echo ($progress_bar_style == 'fourth') ? 'selected' : ''; ?> value="fourth"><?php echo __( 'With stripes and animation', $this->plugin_name); ?></option>
                                </select>
                                <div style="margin:20px 0;">
                                    <div class='ays-progress first <?php echo ($progress_bar_style == 'first') ? "display_block" : ""; ?>'>
                                        <span class='ays-progress-value first' style='width:67%;'>67%</span>
                                        <div class="ays-progress-bg first">
                                            <div class="ays-progress-bar first" style='width:67%;'></div>
                                        </div>
                                    </div>

                                    <div class='ays-progress second <?php echo ($progress_bar_style == 'second') ? "display_block" : ""; ?>'>
                                        <span class='ays-progress-value second' style='width:88%;'>88%</span>
                                        <div class="ays-progress-bg second">
                                            <div class="ays-progress-bar second" style='width:88%;'></div>
                                        </div>
                                    </div>

                                    <div class="ays-progress third <?php echo ($progress_bar_style == 'third') ? "display_block" : ""; ?>">
                                        <span class="ays-progress-value third">55%</span>
                                        <div class="ays-progress-bg third">
                                            <div class="ays-progress-bar third" style='width:55%;'></div>
                                        </div>
                                    </div>

                                    <div class="ays-progress fourth <?php echo ($progress_bar_style == 'fourth') ? "display_block" : ""; ?>">
                                        <span class="ays-progress-value fourth">34%</span>
                                        <div class="ays-progress-bg fourth">
                                            <div class="ays-progress-bar fourth" style="width:34%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_custom_class">
                                    <?php echo __('Custom class for quiz container',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Custom HTML class for quiz container. You can use your class for adding your custom styles for quiz container. Example: p{color:red !important}',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="text" class="ays-text-input" name="ays_custom_class" id="ays_custom_class" placeholder="myClass myAnotherClass..." value="<?php echo $custom_class; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-12 ays_divider_left" style="position:relative;">
                        <div id="ays_styles_tab" style="position:sticky;top:50px; margin:auto;">
                            <div class="ays-quiz-live-container ays-quiz-live-container-1">
                                <div class="step active-step">
                                    <div class="ays-abs-fs">
                                        <img src="" alt="Ays Question Image" class="ays-quiz-live-image">
                                        <p class="ays-fs-title ays-quiz-live-title"></p>
                                        <p class="ays-fs-subtitle ays-quiz-live-subtitle"></p>
                                        <input type="hidden" name="ays_quiz_id" value="2">
                                        <input type="button" name="next" class="action-button ays-quiz-live-button"
                                               value="<?php echo __( "Start", $this->plugin_name ); ?>">
                                        <br>
                                        <br>
                                    </div>
                                </div>
                            </div>
                            <div class="ays-quiz-live-container ays-quiz-live-container-2" style="display:none;">
                                <div class="step active-step">
                                    <div class="ays-abs-fs">
                                        <img src="" alt="Ays Question Image" class="ays-quiz-live-image">
                                        <p class="ays-fs-title ays-quiz-live-title"></p>
                                        <p class="ays-fs-subtitle ays-quiz-live-subtitle"></p>
                                        <input type="hidden" name="ays_quiz_id" value="2">
                                        <input type="button" name="next" class="ays_next start_button action-button ays-quiz-live-button"
                                               value="<?php echo __( "Start", $this->plugin_name ); ?>">
                                        <br>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <p class="ays-subtitle" style="margin-top:0;"><?php echo __('Answers Styles',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-lg-7 col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for='ays_answers_font_size'>
                                    <?php echo __('Answers font size', $this->plugin_name); ?> (px)
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The font size of the answers in pixels in the quiz. It accepts only numeric values.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_answers_font_size'name='ays_answers_font_size' value="<?php echo $answers_font_size; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_answers_view">
                                    <?php echo __('Answers view',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the design of the answers of question.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <select class="ays-text-input ays-text-input-short" id="ays_answers_view" name="ays_answers_view">
                                    <option value="list" <?php echo (isset($options['answers_view']) && $options['answers_view'] == 'list') ? 'selected' : ''; ?>>
                                        <?php echo __('List',$this->plugin_name)?>
                                    </option>
                                    <option value="grid" <?php echo (isset($options['answers_view']) && $options['answers_view'] == 'grid') ? 'selected' : ''; ?>>
                                        <?php echo __('Grid',$this->plugin_name)?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_answers_padding">
                                    <?php echo __('Answers padding (px)',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Padding of answers.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_answers_padding' name='ays_answers_padding' value="<?php echo $answers_padding; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_answers_margin">
                                    <?php echo __('Answers gap (px)',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Gap between answers.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_answers_margin' name='ays_answers_margin' value="<?php echo $answers_margin; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_answers_border">
                                    <?php echo __('Answers border',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow answer border',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide" id="ays_answers_border" name="ays_answers_border" value="on"
                                       <?php echo ($answers_border) ? 'checked' : ''; ?>/>
                                <label for="ays_answers_border" class="ays_switch_toggle">Toggle</label>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_answers_border_width">
                                        <?php echo __('Border width',$this->plugin_name)?> (px)
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of answers border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                     </label>
                                    <input type="number" class="ays-text-input" id='ays_answers_border_width' name='ays_answers_border_width'
                                           value="<?php echo $answers_border_width; ?>"/>
                                </div>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_answers_border_style">
                                        <?php echo __('Border style',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The style of answers border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                    <select id="ays_answers_border_style" name="ays_answers_border_style" class="ays-text-input">
                                        <option <?php echo ($answers_border_style == 'solid') ? 'selected' : ''; ?> value="solid">Solid</option>
                                        <option <?php echo ($answers_border_style == 'dashed') ? 'selected' : ''; ?> value="dashed">Dashed</option>
                                        <option <?php echo ($answers_border_style == 'dotted') ? 'selected' : ''; ?> value="dotted">Dotted</option>
                                        <option <?php echo ($answers_border_style == 'double') ? 'selected' : ''; ?> value="double">Double</option>
                                        <option <?php echo ($answers_border_style == 'groove') ? 'selected' : ''; ?> value="groove">Groove</option>
                                        <option <?php echo ($answers_border_style == 'ridge') ? 'selected' : ''; ?> value="ridge">Ridge</option>
                                        <option <?php echo ($answers_border_style == 'inset') ? 'selected' : ''; ?> value="inset">Inset</option>
                                        <option <?php echo ($answers_border_style == 'outset') ? 'selected' : ''; ?> value="outset">Outset</option>
                                        <option <?php echo ($answers_border_style == 'none') ? 'selected' : ''; ?> value="none">None</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                                    <label for="ays_answers_border_color">
                                        <?php echo __('Border color',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the answers border',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                    <input id="ays_answers_border_color" class="ays-text-input" type="text" data-alpha="true" name='ays_answers_border_color'
                                           value="<?php echo $answers_border_color; ?>" data-default-color="#000000">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_answers_box_shadow">
                                    <?php echo __('Answers box shadow',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow answer container box shadow',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                       id="ays_answers_box_shadow" name="ays_answers_box_shadow"
                                       <?php echo ($answers_box_shadow) ? 'checked' : ''; ?>/>
                                <label for="ays_answers_box_shadow" class="ays_switch_toggle">Toggle</label>
                                <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_box_shadow) ? '' : 'display:none;' ?>">
                                    <label for="ays_answers_box_shadow_color">
                                        <?php echo __('Answers shadow color',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The shadow color of answers container',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                     </label>
                                    <input type="text" class="ays-text-input" id='ays_answers_box_shadow_color'
                                           name='ays_answers_box_shadow_color' data-alpha="true" data-default-color="#000000"
                                           value="<?php echo $answers_box_shadow_color; ?>"/>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_ans_img_height">
                                    <?php echo __('Answers image height (px)',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Height of answers images.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_ans_img_height' name='ays_ans_img_height' value="<?php echo $ans_img_height; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_show_answers_caption">
                                    <?php echo __('Show answers caption',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show answers caption near the answer image. This option will be work only when answer has image.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                       id="ays_show_answers_caption" name="ays_show_answers_caption"
                                       <?php echo ($show_answers_caption) ? 'checked' : ''; ?>/>
                                <label for="ays_show_answers_caption" class="ays_switch_toggle">Toggle</label>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_ans_img_caption_style">
                                    <?php echo __('Answers image caption style',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Height of answers images.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <select id="ays_ans_img_caption_style" name="ays_ans_img_caption_style" class="ays-text-input ays-text-input-short">
                                    <option <?php echo ($ans_img_caption_style == 'outside') ? 'selected' : ''; ?> value="outside"><?php echo __('Outside', $this->plugin_name); ?></option>
                                    <option <?php echo ($ans_img_caption_style == 'inside') ? 'selected' : ''; ?> value="inside"><?php echo __('Inside', $this->plugin_name); ?></option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_ans_img_caption_position">
                                    <?php echo __('Answers image caption position',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Height of answers images.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <select id="ays_ans_img_caption_position" name="ays_ans_img_caption_position" class="ays-text-input ays-text-input-short">
                                    <option <?php echo ($ans_img_caption_position == 'top') ? 'selected' : ''; ?> value="top"><?php echo __('Top', $this->plugin_name); ?></option>
                                    <option <?php echo ($ans_img_caption_position == 'bottom') ? 'selected' : ''; ?> value="bottom"><?php echo __('Bottom', $this->plugin_name); ?></option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label for="ays_ans_right_wrong_icon">
                                    <?php echo __('Right/wrong answers icons',$this->plugin_name)?>
                                </label>
                                <p>
                                    <span><?php echo __('Show icons in live preview',$this->plugin_name)?></span>
                                    <input type="checkbox" class="ays_toggle" id="ays_ans_rw_icon_preview"/>
                                    <label for="ays_ans_rw_icon_preview" style="display:inline-block;margin-left:3px;" class="ays_switch_toggle">Toggle</label>
                                </p>
                                <p>
                                    <span><?php echo __('Show wrong icons in live preview',$this->plugin_name)?></span>
                                    <input type="checkbox" class="ays_toggle" id="ays_wrong_icon_preview"/>
                                    <label for="ays_wrong_icon_preview" style="display:inline-block;margin-left:3px;" class="ays_switch_toggle">Toggle</label>
                                </p>
                            </div>
                            <div class="col-sm-7 ays_divider_left">
                                <label class="ays_quiz_rw_icon">
                                    <input name="ays_ans_right_wrong_icon" type="radio" value="default" <?php echo $ans_right_wrong_icon == 'default' ? 'checked' : ''; ?>>
                                    <img class="right_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/correct.png">
                                    <img class="wrong_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/wrong.png">
                                </label>
                                <?php
                                    for($i = 1; $i <= 10; $i++):
                                        $right_style_name = "correct-style-".$i;
                                        $wrong_style_name = "wrong-style-".$i;
                                ?>
                                <label class="ays_quiz_rw_icon">
                                    <input name="ays_ans_right_wrong_icon" type="radio" value="style-<?php echo $i; ?>" <?php echo $ans_right_wrong_icon == 'style-'.$i ? 'checked' : ''; ?>>
                                    <img class="right_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/<?php echo $right_style_name; ?>.png">
                                    <img class="wrong_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/<?php echo $wrong_style_name; ?>.png">
                                </label>
                                <?php
                                    endfor;
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-12 ays_divider_left" style="position:relative;">
                        <div style="position:sticky;top:90px; margin:auto;">
                            <div class="ays-quiz-live-container ays-quiz-live-container-answers" style="overflow:initial;">
                                <div class="answers-with">
                                    <div class="nav-answers-tab-wrapper">
                                        <a href="#step1" class="nav-tab nav-tab-active">
                                            <?php echo __("Without images", $this->plugin_name);?>
                                        </a>
                                        <a href="#step2" class="nav-tab">
                                            <?php echo __("With images", $this->plugin_name);?>
                                        </a>
                                    </div>
                                </div>
                                <p style="position: absolute;top: 5px;">
                                    <span class="ays_quiz_small_hint_text" style="color: #ccc;"><?php echo __("This species does not apply to themes Modern light and Modern dark", $this->plugin_name); ?></span>
                                </p>
                                <div id="step1" class="step active-step">
                                    <div class="ays-abs-fs">
                                        <div class="ays-quiz-answers ays_list_view_container">
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="1">
                                                <input type="radio" name="ays_questions[ays-question-74]" id="ays-answer-72-19" value="72">
                                                <label for="ays-answer-72-19">Mark Zuckerberg</label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-74]" id="ays-answer-73-19" value="73">
                                                <label for="ays-answer-73-19">Elon Musk</label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-74]" id="ays-answer-74-19" value="74">
                                                <label for="ays-answer-74-19">Bill Gates</label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-74]" id="ays-answer-75-19" value="75">
                                                <label for="ays-answer-75-19">Steve Jobs</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step2" class="step">
                                    <div class="ays-abs-fs answers-image-container">
                                        <div class="ays-quiz-answers ays_list_view_container">
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="1">
                                                <input type="radio" name="ays_questions[ays-question-124]" id="ays-answer-245-1" value="245">
                                                <label for="ays-answer-245-1" style="margin-bottom: 0; line-height: 1.5">Mark Zuckerberg</label>
                                                <label for="ays-answer-245-1" class="ays_answer_image ays_empty_before_content">
                                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416.jpg" alt="answer_image" class="ays-answer-image">
                                                </label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-124]" id="ays-answer-249-1" value="249">
                                                <label for="ays-answer-249-1" style="margin-bottom: 0; line-height: 1.5">Elon Musk</label>
                                                <label for="ays-answer-249-1" class="ays_answer_image ays_empty_before_content">
                                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-2.jpg" alt="answer_image" class="ays-answer-image">
                                                </label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-124]" id="ays-answer-248-1" value="248">
                                                <label for="ays-answer-248-1" style="margin-bottom: 0; line-height: 1.5">Bill Gates</label>
                                                <label for="ays-answer-248-1" class="ays_answer_image ays_empty_before_content">
                                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-1.jpg" alt="answer_image" class="ays-answer-image">
                                                </label>
                                            </div>
                                            <div class="ays-field ays_list_view_item">
                                                <input type="hidden" name="ays_answer_correct[]" value="0">
                                                <input type="radio" name="ays_questions[ays-question-124]" id="ays-answer-250-1" value="249">
                                                <label for="ays-answer-250-1" style="margin-bottom: 0; line-height: 1.5">Steve Jobs</label>
                                                <label for="ays-answer-250-1" class="ays_answer_image ays_empty_before_content">
                                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-3.jpg" alt="answer_image" class="ays-answer-image">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_custom_css">
                            <?php echo __('Custom CSS',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Field for entering your own CSS code',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                    <textarea class="ays-textarea" id="ays_custom_css" name="ays_custom_css" cols="30"
                              rows="10"><?php echo (isset($options['custom_css']) && $options['custom_css'] != '') ? $options['custom_css'] : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div id="tab3" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab3') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Quiz Settings',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Show quiz head information',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable to show the quiz title and description in the start page of the quiz(in the front-end).',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="checkbox" id="ays_show_quiz_title" name="ays_show_quiz_title"
                                    value="on" <?php echo $show_quiz_title ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_show_quiz_title"><?php echo __('Show title',$this->plugin_name)?></label>
                        </div>
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="checkbox" id="ays_show_quiz_desc" name="ays_show_quiz_desc"
                                    value="on" <?php echo $show_quiz_desc ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_show_quiz_desc"><?php echo __('Show description',$this->plugin_name)?></label>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_randomize_answers">
                            <?php echo __('Enable randomize answers',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The possibility of showing the answers of the questions in an accidental sequence. Every time it will show answers in random order.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timerl" id="ays_enable_randomize_answers"
                               name="ays_enable_randomize_answers"
                               value="on" <?php echo (isset($options['randomize_answers']) && $options['randomize_answers'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_randomize_questions">
                           <?php echo __('Enable randomize questions',$this->plugin_name)?>
                           <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The possibility of showing questions in an accidental sequence. It will show questions in random order. If you want to take a specific amount of questions from a pool of questions randomly you need to enable question bank option.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timerl" id="ays_enable_randomize_questions"
                               name="ays_enable_randomize_questions"
                               value="on" <?php echo (isset($options['randomize_questions']) && $options['randomize_questions'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_question_bank">
                            <?php echo __('Enable question bank',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable to take a specific amount of questions from the quiz randomly. For example, you can choose 20 questions from 50 randomly. Every time it will take different questions from the pool.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_question_bank"
                               name="ays_enable_question_bank" value="on"
                            <?php echo (isset($options['enable_question_bank']) && $options['enable_question_bank'] == 'on') ? 'checked' : ''; ?>>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_question_bank']) && $options['enable_question_bank'] == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="ays_quiz_loader">
                                    <input type="radio" class="ays-enable-timer1" name="ays_question_bank_type" value="general" <?php echo ($question_bank_type == 'general') ? 'checked' : '' ?>/>
                                    <span><?php echo __( "General", $this->plugin_name ); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" class="ays-enable-timer1" name="ays_question_bank_type" value="by_category" <?php echo ($question_bank_type == 'by_category') ? 'checked' : ''; ?>/>
                                    <span><?php echo __( "By Category", $this->plugin_name ); ?></span>
                                </label>
                                <a class="ays_help" data-toggle="tooltip" data-html="true" title="<?php echo "<p style='text-indent:10px;margin:0;'>" . 
                                    __('There are two ways of making question bank system.', $this->plugin_name ) . "</p><p style='text-indent:10px;margin:0;'><strong>" . 
                                    __('General', $this->plugin_name ) . ": </strong>" . 
                                    __('It will take the specified amount of questions from all the questions you include in this quiz.', $this->plugin_name ) . "</p><p style='text-indent:10px;margin:0;'><strong>" . 
                                    __('By Category', $this->plugin_name ) . ": </strong>" . 
                                    __('Here you can see all the categories of questions you have included in the general tab. You can provide different numbers for different categories. Also, you can reorder them as you want by drag and dropping. The category order will be kept in the front end, but questions will be printed randomly.', $this->plugin_name ) . "</p>"; ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                                <div class="ays_refresh_qbank_categories display_none float-right">
                                    <p>
                                        <button type="button" class="button ays_refresh_qbank_cats_button"><?php echo __( "Refresh Categories", $this->plugin_name ); ?></button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="question_bank_general <?php echo ($question_bank_type == 'general') ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_questions_count">
                                        <?php echo __('Questions count',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Number of randomly selected questions',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" name="ays_questions_count" id="ays_questions_count"
                                           class="ays-enable-timerl ays-text-input"
                                           value="<?php echo (isset($options['questions_count'])) ? $options['questions_count'] : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="question_bank_by_category <?php echo ($question_bank_type == 'by_category') ? '' : 'display_none'; ?>">
                            <div class="form-group row" style="margin:0;">
                                <div class="col-sm-12 question_bank_by_category_div">
                                    <?php 
                                    $bank_i = 0;
                                    foreach($questions_bank_cat_count as $cid => $val):
                                        if(! array_key_exists(strval($cid), $question_bank_categories)){
                                            continue;
                                        }
                                    ?>
                                    <div class="row question_bank_category">
                                        <div class="col-sm-4">
                                            <label for="ays_questions_count_<?php echo $cid; ?>">
                                                <i class="ays_fa ays_fa_arrows question_bank_by_category_sort_handle"></i>
                                                <?php echo $question_bank_categories[$cid]; ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="number" name="ays_questions_bank_cat_count[<?php echo $cid; ?>]" id="ays_questions_count_<?php echo $cid; ?>"
                                                   class="ays-enable-timerl ays-text-input"
                                                   value="<?php echo $val; ?>">
                                        </div>
                                    </div>
                                    <?php
                                        $bank_i++;
                                    ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_question_count_per_page">
                            <?php echo __('Question count per page',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow more than one question per page',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_question_count_per_page"
                               name="ays_question_count_per_page" value="on"
                            <?php echo (isset($options['question_count_per_page']) && $options['question_count_per_page'] == 'on') ? 'checked' : ''; ?>>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo (isset($options['question_count_per_page']) && $options['question_count_per_page'] == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_question_count_per_page_number">
                                    <?php echo __('Questions count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Number of questions per page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_question_count_per_page_number"
                                       id="ays_question_count_per_page_number" class="ays-enable-timerl ays-text-input"
                                       value="<?php echo (isset($options['question_count_per_page_number'])) ? $options['question_count_per_page_number'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_questions_counter">
                            <?php echo __('Show questions counter',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the number of the current question and the total amount of the question in the quiz. It will be shown on the right top corner of the quiz container. Example:3/7',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timerl" id="ays_enable_questions_counter"
                               name="ays_enable_questions_counter"
                               value="on" <?php echo (isset($options['enable_questions_counter']) && $options['enable_questions_counter'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_rtl_direction">
                            <?php echo __('Use RTL Direction',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable Right to Left direction for the text. This option is intended for the Arabic language.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timerl" id="ays_enable_rtl_direction"
                               name="ays_enable_rtl_direction"
                               value="on" <?php echo (isset($options['enable_rtl_direction']) && $options['enable_rtl_direction'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_copy_protection">
                            <?php echo __('Enable copy protection',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable copy functionality in quiz page(CTRL+C) and Right-click',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_copy_protection"
                               name="ays_enable_copy_protection"
                               value="on" <?php echo ($enable_copy_protection == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_correction">
                            <?php echo __('Show correct answers',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" data-html="true" title="<?php echo __('Show if the selected answer is right or wrong with green and red marks. To decide when the right/wrong answers will be shown go to “Text for right/wrong answers show option”.',$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_correction"
                               name="ays_enable_correction"
                               value="on" <?php echo (isset($options['enable_correction']) && $options['enable_correction'] == 'on') ? 'checked' : ''; ?>/>
                    </div>                    
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_correction']) && $options['enable_correction'] == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label class="form-check-label" for="ays_explanation_time">
                                    <?php echo __('Display duration of right/wrong answer text (in seconds)', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip"
                                    title="<?php echo __('Display duration of right/wrong answer text (in seconds) after answering the question.', $this->plugin_name) ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <input type="number" class="ays-text-input" id="ays_explanation_time" name="ays_explanation_time" value="<?php echo $explanation_time; ?>" placeholder="4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Text for right/wrong answers show',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify where to display right/wrong answers. Note that the “Show correct answers” option should be enabled.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_answers_rw_texts" value="on_passing" <?php echo ($answers_rw_texts == 'on_passing') ? 'checked' : '' ?>/>
                            <span><?php echo __( "On passing the quiz", $this->plugin_name ); ?></span>
                        </label>
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_answers_rw_texts" value="on_results_page" <?php echo ($answers_rw_texts == 'on_results_page') ? 'checked' : '' ?>/>
                            <span><?php echo __( "On results page", $this->plugin_name ); ?></span>
                        </label>
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_answers_rw_texts" value="on_both" <?php echo ($answers_rw_texts == 'on_both') ? 'checked' : '' ?>/>
                            <span><?php echo __( "Both", $this->plugin_name ); ?></span>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_enable_pass_count">
                            <?php echo __('Show passed users count',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show how many users passed the quiz. It will be shown at the bottom of the start page of the quiz',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_enable_pass_count"
                               name="ays_enable_pass_count"
                               value="on" <?php echo ($enable_pass_count == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_enable_rate_avg">
                            <?php echo __('Show Quiz average rate',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the average rate of the quiz. It will be shown at the bottom of the start page of the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_enable_rate_avg"
                               name="ays_enable_rate_avg"
                               value="on" <?php echo ($enable_rate_avg == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_show_create_date">
                            <?php echo __('Show quiz creation date',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show quiz creation date in quiz start page',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_show_create_date"
                               name="ays_show_create_date"
                               value="on" <?php echo ($show_create_date == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_show_author">
                            <?php echo __('Show quiz author',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show quiz author in quiz start page',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_show_author"
                               name="ays_show_author"
                               value="on" <?php echo ($show_author == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_show_category">
                            <?php echo __('Show quiz category',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show quiz category in quiz start page',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_show_category"
                               name="ays_show_category"
                               value="on" <?php echo ($show_category) ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_show_question_category">
                            <?php echo __('Show question category',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show question category in each question.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="ays_show_question_category"
                               name="ays_show_question_category"
                               value="on" <?php echo ($show_question_category) ? 'checked' : ''; ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_enable_quiz_rate">
                            <?php echo __('Enable Quiz assessment',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Comment and rate the quiz with up to 5 stars at the end of the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_enable_quiz_rate" class="ays_toggle_checkbox"
                               name="ays_enable_quiz_rate"
                               value="on" <?php echo ($enable_quiz_rate == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_quiz_rate == 'on') ? '' : 'display_none' ?>">
                        <div class="form-group row">
                            <div class="col-sm-4" style="padding-right: 0px;">
                                <label for="ays_enable_rate_comments">
                                    <?php echo __('Show last 5 reviews',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show last 5 reviews after rating the quiz',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_enable_rate_comments"
                                       name="ays_enable_rate_comments"
                                       value="on" <?php echo ($enable_rate_comments == 'on') ? 'checked' : ''; ?>/>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-3" style="padding-right: 0px;">
                                <label for="ays_rate_form_title">
                                    <?php echo __('Rating form title',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Text which will notify user that he can submit a feedback',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <?php
                                $content = stripslashes(wpautop($rate_form_title));
                                $editor_id = 'ays_rate_form_title';
                                $settings = array('editor_height' => '4', 'textarea_name' => 'ays_rate_form_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_live_bar_option">
                            <?php echo __('Enable live progressbar',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the current state of the user passing the quiz. It will be shown at the top of the quiz container.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_live_bar_option"
                               name="ays_enable_live_progress_bar"
                               value="on" <?php echo (isset($options['enable_live_progress_bar']) && $options['enable_live_progress_bar'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_live_progress_bar']) && $options['enable_live_progress_bar'] == 'on') ? '' : 'display_none' ?>">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_enable_percent_view_option">
                                    <?php echo __('Enable percent view',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the progress bar by percentage',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-enable-timer1" id="ays_enable_percent_view_option"
                                       name="ays_enable_percent_view"
                                       value="on" <?php echo (isset($options['enable_percent_view']) && $options['enable_percent_view'] == 'on') ? 'checked' : '' ?>/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_early_finish">
                            <?php echo __('Enable finish button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow user to finish the quiz early',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_early_finish"
                               name="ays_enable_early_finish"
                               value="on" <?php echo ($enable_early_finish) ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_clear_answer">
                            <?php echo __('Enable clear answer button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow user to clear the selected answer. Button will not be displayed if Show correct answers option is enabled.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_clear_answer"
                               name="ays_enable_clear_answer"
                               value="on" <?php echo ($enable_clear_answer) ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_enable_next_button">
                            <?php echo __('Enable next button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('User can change the question forward manually. If you want to make the questions required just disable this option.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_enable_next_button" value="on"
                               name="ays_enable_next_button" <?php echo (isset($options['enable_next_button']) && $options['enable_next_button'] == 'on') ? 'checked' : '' ?>>
                    </div>
                    <div class="col-sm-3" style="border-left: 1px solid #ccc">
                        <label for="ays_enable_previous_button">
                            <?php echo __('Enable previous button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('User can change the question backward manually',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_enable_previous_button" value="on"
                               name="ays_enable_previous_button" <?php echo (isset($options['enable_previous_button']) && $options['enable_previous_button'] == 'on') ? 'checked' : '' ?>>
                    </div>
                    <div class="col-sm-3" style="border-left: 1px solid #ccc">
                        <label for="ays_enable_arrows">
                            <?php echo __('Use arrows instead of buttons',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Buttons will be replaced to icons.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl" id="ays_enable_arrows" name="ays_enable_arrows"
                               value="on" <?php echo (isset($options['enable_arrows']) && $options['enable_arrows'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                </div>                
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_timer">
                            <?php echo __('Enable Timer',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show countdown time in the quiz. It will be automatically submitted if the time is over.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_timer"
                               name="ays_enable_timer"
                               value="on" <?php echo ($enable_timer == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo ($enable_timer == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="ays_quiz_timer"><?php echo __('Timer seconds',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="number" name="ays_quiz_timer" id="ays_quiz_timer"
                                       class="ays-text-input"
                                       value="<?php echo (isset($options['timer'])) ? $options['timer'] : ''; ?>"/>
                                <p class="ays-important-note"><span><?php echo __('Note!!',$this->plugin_name)?></span> <?php echo __('After timer finished
                                    countdowning, quiz will be submitted automatically.',$this->plugin_name)?></p>
                                <label for="timer_text">Timer Text</label>
                                <a class="ays_help" data-toggle="tooltip" title="Text before starting the quiz. Use %%time%% for showing time.">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                                <?php
                                $content = wpautop(stripslashes((isset($options['timer_text'])) ? $options['timer_text'] : ''));
                                $editor_id = 'timer_text';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_timer_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_timer_in_title">
                                    <?php echo __('Show timer in page tab',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable to show countdown timer in the browser tab.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_timer_in_title" id="ays_quiz_timer_in_title"
                                       <?php echo ($quiz_timer_in_title) ? 'checked' : ''; ?>/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_enable_bg_music">
                            <?php echo __('Enable Background music',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background music will play while passing the quiz. Upload your own audio file for the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_enable_bg_music"
                               name="ays_enable_bg_music" class="ays_toggle_checkbox"
                               value="on" <?php echo $enable_bg_music ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left" style="<?php echo $enable_bg_music ? '' : 'display:none;' ?>">
                        <div class="ays-bg-music-container">
                            <a class="add-quiz-bg-music" href="javascript:void(0);"><?php echo __("Select music", $this->plugin_name); ?></a>
                            <audio controls src="<?php echo $quiz_bg_music; ?>"></audio>
                            <input type="hidden" name="ays_quiz_bg_music" class="ays_quiz_bg_music" value="<?php echo $quiz_bg_music; ?>">
                        </div>
                    </div>
                </div>
                <hr/>
                   <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4" style="padding-right: 0px;">
                        <label for="ays_enable_rw_asnwers_sounds">
                            <?php echo __('Enable Right/Wrong answers sounds',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This option will work only when Enable correct answers option is enabled and sounds are selected from General options page.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="ays_enable_rw_asnwers_sounds"
                               name="ays_enable_rw_asnwers_sounds" class="ays_toggle_checkbox"
                               value="on" <?php echo $enable_rw_asnwers_sounds ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left" style="<?php echo $enable_rw_asnwers_sounds ? '' : 'display:none;' ?>">
                        <?php if($rw_answers_sounds_status): ?>
                        <blockquote class=""><?php echo __('Sounds are selected. For change sounds go to', $this->plugin_name); ?> <a href="?page=quiz-maker-settings" target="_blank"><?php echo __('General options', $this->plugin_name); ?></a> <?php echo __('page', $this->plugin_name); ?></blockquote>
                        <?php else: ?>
                        <blockquote class=""><?php echo __('Sounds are not selected. For selecting sounds go to', $this->plugin_name); ?> <a href="?page=quiz-maker-settings" target="_blank"><?php echo __('General options', $this->plugin_name); ?></a> <?php echo __('page', $this->plugin_name); ?></blockquote>
                        <?php endif; ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="active_date_check">
							<?php echo __('Schedule the Quiz', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?php echo __('The period of time when quiz will be active. When the date is out the expiration message will be shown.', $this->plugin_name) ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input id="active_date_check" type="checkbox" class="active_date_check ays_toggle_checkbox"
                                name="active_date_check" <?php echo $active_date_check ? 'checked' : '' ?>>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left active_date <?php echo $active_date_check ? '' : 'display_none' ?>">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label class="form-check-label" for="ays-active"> <?php echo __('Start date:', $this->plugin_name); ?> </label>
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <input type="text" class="ays-text-input ays-text-input-short" id="ays-active" name="ays-active"
                                       value="<?php echo $activeQuiz; ?>" placeholder="<?php echo current_time( 'mysql' ); ?>">
                                    <div class="input-group-append">
                                        <label for="ays-active" class="input-group-text">
                                            <span><i class="ays_fa ays_fa_calendar"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label class="form-check-label" for="ays-deactive"> <?php echo __('End date:', $this->plugin_name); ?> </label>
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <input type="text" class="ays-text-input ays-text-input-short" id="ays-deactive" name="ays-deactive"
                                       value="<?php echo $deactiveQuiz; ?>" placeholder="<?php echo current_time( 'mysql' ); ?>">
                                    <div class="input-group-append">
                                        <label for="ays-deactive" class="input-group-text">
                                            <span><i class="ays_fa ays_fa_calendar"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label class="form-check-label" for="active_date_message"><?php echo __("Expiration message:", $this->plugin_name) ?></label>
                            </div>
                            <div class="col-sm-8">
                                <div class="editor">
                                    <?php
                                    $content   = isset($options['active_date_message']) ? stripslashes($options['active_date_message']) : __("This quiz has expired!", $this->plugin_name);
                                    $editor_id = 'active_date_message';
                                    $settings  = array(
                                        'editor_height'  => '4',
                                        'textarea_name'  => 'active_date_message',
                                        'editor_class'   => 'ays-textarea',
                                        'media_elements' => false
                                    );
                                    wp_editor($content, $editor_id, $settings);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab4" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab4') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Quiz results settings',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_calculate_score">
                            <?php echo __('Calculate the score',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Calculate the score of results by the selected method. You can only choose one of these two options.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_calculate_score" value="by_correctness" <?php echo ($calculate_score == 'by_correctness') ? 'checked' : '' ?>/>
                            <span style="margin-right:5px;"><?php echo __( "By correctness", $this->plugin_name ); ?></span>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It will calculate the score based on correct answers of the question. It will store the score by percentage. You can use Variables (General Settings) to show the quantity of the questions answered right.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_calculate_score" value="by_points" <?php echo ($calculate_score == 'by_points') ? 'checked' : '' ?>/>
                            <span style="margin-right:5px;"><?php echo __( "By weight / points", $this->plugin_name ); ?></span>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It will calculate the score based on Answers points and Questions points. Again you can use Variables to show the user’s score at the end of the quiz. If you choose this option the features connected with correctness will be disabled.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_disable_store_data">
                            <?php echo __('Disable data storing in database',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable data storing in the database, and results will not be displayed on the \'Results\' page. (not recommended)',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_disable_store_data"
                               name="ays_disable_store_data"
                               value="on" <?php echo $disable_store_data ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_redirect_after_submit">
                            <?php echo __('Redirect after submit',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Redirect to custom URL after user submit the form.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_redirect_after_submit"
                               name="ays_redirect_after_submit"
                               value="on" <?php echo $redirect_after_submit ? 'checked' : '' ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $redirect_after_submit ? '' : 'display_none'; ?>">                                
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_submit_redirect_url">
                                    <?php echo __('Redirect URL',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The URL for redirecting after the user submits the form.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="ays-text-input" id="ays_submit_redirect_url"
                                    name="ays_submit_redirect_url"
                                    value="<?php echo $submit_redirect_url; ?>"/>
                            </div>
                        </div>
                        <hr/>                                
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_submit_redirect_delay">
                                    <?php echo __('Redirect delay (sec)', $this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The redirection delay in seconds after the user submits the form. Value should be greater than 0.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" class="ays-text-input" id="ays_submit_redirect_delay"
                                    name="ays_submit_redirect_delay"
                                    value="<?php echo $submit_redirect_delay; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_exit_button">
                            <?php echo __('Enable EXIT button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Exit button will be displayed in the finish page and must redirect the user to a custom URL.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_exit_button"
                               name="ays_enable_exit_button"
                               value="on" <?php echo $enable_exit_button ? 'checked' : '' ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_exit_button ? '' : 'display_none'; ?>">                                
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_exit_redirect_url">
                                    <?php echo __('Redirect URL',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The custom URL address for EXIT button in finish page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="ays-text-input" id="ays_exit_redirect_url"
                                    name="ays_exit_redirect_url"
                                    value="<?php echo $exit_redirect_url; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_result">
                            <?php echo __('Hide Result',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Text to show on final page instead of result page',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_result"
                               name="ays_enable_result"
                               value="on" <?php echo (isset($options['enable_result']) && $options['enable_result'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_result']) && $options['enable_result'] == "on") ? "" : "display_none" ?>">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_quiz_result">
                                    <?php echo __('Text for showing after the quiz',$this->plugin_name)?>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <?php
                                $content = wpautop(stripslashes((isset($options['result_text'])) ? $options['result_text'] : ''));
                                $editor_id = 'ays_quiz_result';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_quiz_result_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_hide_score">
                            <?php echo __('Hide score',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable to show the user score with percentage on the finish page. If you want to show points or correct answers count, you need to tick this option and use Variables (General Settings) in the “Text for showing after quiz completion” option.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_hide_score"
                               name="ays_hide_score"
                               value="on" <?php echo (isset($options['hide_score']) && $options['hide_score'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Display score',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How to display score of result',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_percentage" <?php echo ($display_score == 'by_percentage') ? 'checked' : '' ?>/>
                            <span><?php echo __( "By percentage", $this->plugin_name ); ?></span>
                        </label>
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_correctness" <?php echo ($display_score == 'by_correctness') ? 'checked' : '' ?>/>
                            <span><?php echo __( "By correct answers count", $this->plugin_name ); ?></span>
                        </label>
                        <label class="ays_quiz_loader">
                            <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_points" <?php echo ($display_score == 'by_points') ? 'checked' : '' ?>/>
                            <span><?php echo __( "By weight/point", $this->plugin_name ); ?></span>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_bar_option">
                            <?php echo __('Enable progressbar',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show score via progressbar',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_bar_option"
                               name="ays_enable_progress_bar"
                               value="on" <?php echo (isset($options['enable_progress_bar']) && $options['enable_progress_bar'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_restart_button">
                            <?php echo __('Enable restart button',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the restart button at the end of the quiz for restarting the quiz and pass it again.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_restart_button"
                               name="ays_enable_restart_button"
                               value="on" <?php echo ($enable_restart_button) ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_questions_result_option">
                            <?php echo __('Show all questions result in finish page',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" data-html="true" title="<?php echo __('Show all questions with right and wrong answers after quiz.<br> This option will be disabled if the option \'Calculate the score\' chosen \'By weight/points\'.', $this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_questions_result_option"
                               name="ays_enable_questions_result"
                               value="on" <?php echo (isset($options['enable_questions_result']) && $options['enable_questions_result'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_average_statistical_option">
                            <?php echo __('Show the Average statistical',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show average score according to all results of the quiz',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_average_statistical_option"
                               name="ays_enable_average_statistical"
                               value="on" <?php echo (isset($options['enable_average_statistical']) && $options['enable_average_statistical'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_social_buttons">
                            <?php echo __('Show the Social buttons',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display social buttons for sharing quiz page URL. LinkedIn, Facebook, Twitter.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_social_buttons"
                               name="ays_social_buttons"
                               value="on" <?php echo (isset($options['enable_social_buttons']) && $options['enable_social_buttons'] == 'on') ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-4">
                        <label for="ays_enable_social_links">
                            <?php echo __('Enable Social Media links',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display social media links at the end of the quiz to allow users to visit your pages in the Social media.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_social_links"
                               name="ays_enable_social_links"
                               value="on" <?php echo $enable_social_links ? 'checked' : '' ?>/>
                    </div>
                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_social_links ? '' : 'display_none' ?>">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_linkedin_link">
                                    <i class="ays_fa ays_fa_linkedin_square"></i>
                                    <?php echo __('Linkedin link',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Linkedin profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="ays-text-input" id="ays_linkedin_link" name="ays_social_links[ays_linkedin_link]"
                                    value="<?php echo $linkedin_link; ?>" />
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_facebook_link">
                                    <i class="ays_fa ays_fa_facebook_square"></i>
                                    <?php echo __('Facebook link',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Facebook profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="ays-text-input" id="ays_facebook_link" name="ays_social_links[ays_facebook_link]"
                                    value="<?php echo $facebook_link; ?>" />
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_twitter_link">
                                    <i class="ays_fa ays_fa_twitter_square"></i>
                                    <?php echo __('Twitter link',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Twitter profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="ays-text-input" id="ays_twitter_link" name="ays_social_links[ays_twitter_link]"
                                    value="<?php echo $twitter_link; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Select quiz loader',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the design of the loader on the finish page after submitting. It will inherit the Quiz Text color from the Styles tab.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="default" <?php echo ($quiz_loader == 'default') ? 'checked' : ''; ?>>
                            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                        </label>
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="circle" <?php echo ($quiz_loader == 'circle') ? 'checked' : ''; ?>>
                            <div class="lds-circle"></div>
                        </label>
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="dual_ring" <?php echo ($quiz_loader == 'dual_ring') ? 'checked' : ''; ?>>
                            <div class="lds-dual-ring"></div>
                        </label>
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="facebook" <?php echo ($quiz_loader == 'facebook') ? 'checked' : ''; ?>>
                            <div class="lds-facebook"><div></div><div></div><div></div></div>
                        </label>
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="hourglass" <?php echo ($quiz_loader == 'hourglass') ? 'checked' : ''; ?>>
                            <div class="lds-hourglass"></div>
                        </label>
                        <label class="ays_quiz_loader">
                            <input name="ays_quiz_loader" type="radio" value="ripple" <?php echo ($quiz_loader == 'ripple') ? 'checked' : ''; ?>>
                            <div class="lds-ripple"><div></div><div></div></div>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_final_result_text">
                            <?php echo __('Text for showing after quiz completion',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The message will be displayed after submitting the quiz. You can use Variables (General Settings) to insert user data here. If you want to show results with points or with the number of correct answers, you need to use correspondent variables and enable the “Hide score” option.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = stripslashes(wpautop((isset($options['final_result_text'])) ? $options['final_result_text'] : ''));
                        $editor_id = 'ays_final_result_text';
                        $settings = array('editor_height' => '8', 'textarea_name' => 'ays_final_result_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_checkbox_score_by">
                            <?php echo __('Strong calculation of checkbox answers score',$this->plugin_name)?>
                            <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo "<ul style='list-style-type:disc;padding-left: 20px;'><li>".__("If this option is enabled then our system will calculate checkbox's answer as 1 or 0.",$this->plugin_name). "</li><li>".__("If the user has one wrong answer he/she will get 0 points.",$this->plugin_name). "</li><li>".__("If the option is disabled, the system will calculate the answer as a percentage.",$this->plugin_name). "</li><li>".__("It means if you answer 2 of 3 correct answers then you will get 2/3 points.",$this->plugin_name)."</li></ul>"; ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_checkbox_score_by"
                               name="ays_checkbox_score_by"
                               value="on" <?php echo $checkbox_score_by ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_show_interval_message">
                            <?php echo __('Show interval message',$this->plugin_name)?>
                            <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Show an interval message after quiz completion in the finish page.",$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_show_interval_message"
                               name="ays_show_interval_message"
                               value="on" <?php echo $show_interval_message ? 'checked' : '' ?>/>
                    </div>
                </div>
                <hr/>
                <div class='ays-field-dashboard'>
                    <label for="ays-answers-table"><?php echo __('Intervals', $this->plugin_name); ?>
                        <a href="javascript:void(0)" class="ays-add-interval">
                            <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                        </a>
                        <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" title="<?php echo __('Specify different messages based on the user’s score. It is calculated by percentage. You need to cover the 0-100 range with as many intervals as you want. The message will be displayed at the end of the quiz.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class='ays-field-dashboard ays-table-wrap'>
                    <style>
                        #woo-icon {
                            display: inline-block;
                            margin-right: 5px;
                        }
                        #woo-icon::before {
                            font-family: WooCommerce!important;
                            content: '\e03d';
                            font-size: 18px;
                            line-height: 1;
                        }
                    </style>
                    <table class="ays-intervals-table <?php echo $wc_for_js; ?>">
                        <thead>
                        <tr class="ui-state-default">
                            <th><?php echo __('Ordering', $this->plugin_name); ?></th>
                            <th><?php echo __('Min', $this->plugin_name); ?></th>
                            <th><?php echo __('Max', $this->plugin_name); ?></th>
                            <th><?php echo __('Text', $this->plugin_name); ?></th>
							<?php if ($quiz_intervals_wc): ?>
                            <th><span id='woo-icon'></span><?php echo __('WooCommerce Product', $this->plugin_name); ?></th>
							<?php endif; ?>
                            <th><?php echo __('Image', $this->plugin_name); ?></th>
                            <th><?php echo __('Delete', $this->plugin_name); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($quiz_intervals as $key => $quiz_interval) {
                                $className = "";
                                if (($key + 1) % 2 == 0) {
                                    $className = "even";
                                }
                                $quiz_interval_text = 'Add';

                                if (isset($quiz_interval['interval_min']) && !empty($quiz_interval['interval_max'])) {
                                    ?>
                                    <tr class="ays-interval-row ui-state-default <?php echo $className; ?>">
                                    <td class="ays-sort">
                                        <i class="ays_fa ays_fa_arrows" aria-hidden="true"></i>
                                    </td>
                                    <td>
                                        <input type="number" name="interval_min[]"
                                               value="<?php echo $quiz_interval['interval_min'] ?>" class="interval_min">
                                    </td>
                                    <td>
                                        <input type="number" name="interval_max[]"
                                               value="<?php echo $quiz_interval['interval_max'] ?>" class="interval_max">
                                    </td>
                                    <td>
                                        <textarea type="text" name="interval_text[]" class="interval_text"><?php echo stripslashes(htmlentities($quiz_interval['interval_text'])) ?></textarea>
                                    </td>
									<?php if ($quiz_intervals_wc): ?>
                                        <td>
                                            <select name="interval_wproduct[]" class="interval_wproduct">
                                                <option></option>
												<?php foreach ( $products as $product ): ?>
                                                    <?php $selected_product = (isset($quiz_interval['interval_wproduct']) && $quiz_interval['interval_wproduct'] == $product->ID) ? 'selected' : ''; ?>
                                                    <option <?php echo $selected_product; ?> data-nkar="<?php echo $product->image; ?>" value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </td>
									<?php endif; ?>
                                    <td class="ays-interval-image-td">
                                        <label class='ays-label' for='ays-answer'>
                                            <a href="javascript:void(0)" class="add-answer-image add-interval-image" <?php echo (is_null($quiz_interval['interval_image']) || $quiz_interval['interval_image'] == '') ? "style=display:block;" : "style=display:none" ?>>
                                                <?php echo $quiz_interval_text; ?>
                                            </a>
                                        </label>
                                        <div class="ays-answer-image-container ays-interval-image-container" <?php echo (is_null($quiz_interval['interval_image']) || $quiz_interval['interval_image'] == '') ? "style=display:none; " : "style=display:block" ?>>
                                            <span class="ays-remove-answer-img"></span>
                                            <img src="<?php echo $quiz_interval['interval_image']; ?>" class="ays-answer-img"
                                                 style="width: 100%;"/>
                                            <input type="hidden" name="interval_image[]" class="ays-answer-image"
                                                   value="<?php echo $quiz_interval['interval_image']; ?>"/>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="ays-delete-interval"
                                           data-id="<?php echo $key; ?>">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    </tr>
                                    <?php
                                } else {
                                    $className = "";
                                    if (($key + 1) % 2 == 0) {
                                        $className = "even";
                                    }
                                    ?>
                                    <tr class="ays-interval-row ui-state-default <?php echo $className; ?>">
                                    <td class="ays-sort">
                                        <i class="ays_fa ays_fa_arrows" aria-hidden="true"></i>
                                    </td>
                                    <td>
                                        <input type="number" name="interval_min[]" value="" class="interval_min">
                                    </td>
                                    <td>
                                        <input type="number" name="interval_max[]" value="" class="interval_max">
                                    </td>
                                    <td>
                                        <textarea type="text" name="interval_text[]" class="interval__text"></textarea>
                                    </td>
									<?php if ($quiz_intervals_wc): ?>
                                        <td>sad
                                            <select name="interval_wproduct[]" class="interval_wproduct">
                                                <option></option>
												<?php foreach ( $products as $product ): ?>
                                                    <option data-nkar="<?php echo $product->image; ?>" value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </td>
									<?php endif; ?>
                                    <td class="ays-interval-image-td">
                                        <label class='ays-label' for='ays-answer'>
                                            <a href="javascript:void(0)" class="add-answer-image add-interval-image" style=display:block;>
                                                <?php echo $quiz_interval_text; ?>
                                            </a>
                                        </label>
                                        <div class="ays-answer-image-container ays-interval-image-container"
                                             style=display:none;>
                                            <span class="ays-remove-answer-img"></span>
                                            <img src="" class="ays-answer-img" style="width: 100%;"/>
                                            <input type="hidden" name="interval_image[]" class="ays-answer-image" value=""/>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="ays-delete-interval"
                                           data-id="<?php echo $key; ?>">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="tab5" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab5') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Limitation of Users',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_limit_users">
                            <?php echo __('Limit Users to pass quiz only once',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This option allows to block the users who have already passed the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_limit_users" name="ays_limit_users"
                               value="on" <?php echo (isset($options['limit_users']) && $options['limit_users'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['limit_users']) && $options['limit_users'] == "on") ? "" : "display_none" ?>">
                        <div class="ays-limitation-options">
                            <!-- Limitation by -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_limitation_message">
                                        <?php echo __('Limit users by',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<?php echo __('Limit users pass the quiz by IP or by User ID.',$this->plugin_name)?><br><?php echo __('If you choose \'User ID\', the \'Limit users\' option will not work for the not logged in users. It works only with \'Only for logged in users\' option.',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-check form-check-inline checkbox_ays">
                                        <input type="radio" id="ays_limit_users_by_ip" class="form-check-input" name="ays_limit_users_by" value="ip" <?php echo ($limit_users_by == 'ip') ? 'checked' : ''; ?>/>
                                        <label class="form-check-label" for="ays_limit_users_by_ip"><?php echo __('IP',$this->plugin_name)?></label>
                                    </div>
                                    <div class="form-check form-check-inline checkbox_ays">
                                        <input type="radio" id="ays_limit_users_by_user_id" class="form-check-input" name="ays_limit_users_by" value="user_id" <?php echo ($limit_users_by == 'user_id') ? 'checked' : ''; ?>/>
                                        <label class="form-check-label" for="ays_limit_users_by_user_id"><?php echo __('User ID',$this->plugin_name)?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ays-limitation-options">
                            <!-- Limitation message -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_limitation_message">
                                        <?php echo __('Message',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for those who have passed the quiz',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <?php
                                    $content = wpautop(stripslashes((isset($options['limitation_message'])) ? $options['limitation_message'] : ''));
                                    $editor_id = 'ays_limitation_message';
                                    $settings = array('editor_height' => '8', 'textarea_name' => 'ays_limitation_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                    wp_editor($content, $editor_id, $settings);
                                    ?>
                                </div>
                            </div>
                            <!-- Limitation redirect url -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_redirect_url">
                                        <?php echo __('Redirect URL',$this->plugin_name)?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Leave a current page to go to the link provided',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="ays_redirect_url" id="ays_redirect_url"
                                           class="ays-text-input"
                                           value="<?php echo isset($options['redirect_url']) ? $options['redirect_url'] : ''; ?>"/>
                                </div>
                            </div>
                            <!-- Limitation redirect delay -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_redirection_delay">
                                        <?php echo __('Redirect delay',$this->plugin_name)?>(s)
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Leave current page and go to the link provided after X second',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" name="ays_redirection_delay" id="ays_redirection_delay"
                                           class="ays-text-input"
                                           value="<?php echo isset($options['redirection_delay']) ? $options['redirection_delay'] : 0; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_logged_users">
                            <?php echo __('Only for logged in users',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, only logged in users will be able to pass the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_logged_users"
                               name="ays_enable_logged_users" <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') ? 'disabled' : ''; ?>
                               value="on" <?php echo (((isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on')) || (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on')) ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo ((isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on')) ? '' : 'display_none' ?>"
                         id="ays_logged_in_users_div" >
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="ays_logged_in_message">
                                    <?php echo __('Message',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for those who haven’t logged in',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-10">
                                <?php
                                $content = wpautop(stripslashes((isset($options['enable_logged_users_message'])) ? $options['enable_logged_users_message'] : ''));
                                $editor_id = 'ays_logged_in_message';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_enable_logged_users_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_show_login_form">
                                    <?php echo __('Show Login form',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the Login form at the bottom of the message for not logged in users.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="checkbox" class="ays-enable-timer1" id="ays_show_login_form" name="ays_show_login_form" value="on" <?php echo $show_login_form ? 'checked' : ''; ?>/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_restriction_pass">
                            <?php echo __('Only for selected user role',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz is available only for the roles mentioned in the list.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_restriction_pass"
                               name="ays_enable_restriction_pass"
                               value="on" <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="ays_users_roles">
                                    <?php echo __('User role',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Role of the user on the website.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-10">
                                <select name="ays_users_roles[]" id="ays_users_roles" multiple>
                                    <?php
                                    foreach ($ays_users_roles as $key => $user_role) {
                                        $selected_role = "";
                                        if(isset($options['user_role'])){
                                            if(is_array($options['user_role'])){
                                                if(in_array($user_role['name'], $options['user_role'])){
                                                    $selected_role = 'selected';
                                                }else{
                                                    $selected_role = '';
                                                }
                                            }else{
                                                if($options['user_role'] == $user_role['name']){
                                                    $selected_role = 'selected';
                                                }else{
                                                    $selected_role = '';
                                                }
                                            }
                                        }
                                        echo "<option value='" . $user_role['name'] . "' " . $selected_role . ">" . $user_role['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="restriction_pass_message">
                                    <?php echo __('Message',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for the users who aren’t included in the above-mentioned list.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-10">
                                <?php
                                $content = wpautop(stripslashes((isset($options['restriction_pass_message'])) ? $options['restriction_pass_message'] : ''));
                                $editor_id = 'restriction_pass_message';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'restriction_pass_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_tackers_count">
                            <?php echo __('Limitation count of takers', $this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose how many users can pass the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_tackers_count"
                               name="ays_enable_tackers_count" value="on" <?php echo $enable_tackers_count ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo $enable_tackers_count ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="ays_tackers_count">
                                    <?php echo __('Count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The number of users who can pass the quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-10">
                                <input type="number" name="ays_tackers_count" id="ays_tackers_count" class="ays-enable-timerl ays-text-input"
                                       value="<?php echo $tackers_count; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab6" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab6') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('User Information',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_form_title">
                            <?php echo __('Information Form title',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Description of the Information Form which will be shown at the top of the Form Fields.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8" style="border-left: 1px solid #ccc">
                        <?php
                        $content = wpautop(stripslashes((isset($options['form_title'])) ? $options['form_title'] : ''));
                        $editor_id = 'ays_form_title';
                        $settings = array('editor_height' => '8', 'textarea_name' => 'ays_form_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-2">
                        <label for="ays_information_form">
                            <?php echo __('Information Form',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Data form for the user personal information. You can choose when the Information Form will be shown for completion.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-2">
                        <div class="information_form_settings">
                            <select class="ays_toggle_select" name="ays_information_form" data-hide="disable" id="ays_information_form">
                                <option value="after" <?php echo (isset($options['information_form']) && $options['information_form'] == 'after') ? 'selected' : ''; ?>>
                                    <?php echo __('After Quiz',$this->plugin_name)?>
                                </option>
                                <option value="before" <?php echo (isset($options['information_form']) && $options['information_form'] == 'before') ? 'selected' : ''; ?>>
                                    <?php echo __('Before Quiz',$this->plugin_name)?>
                                </option>
                                <option value="disable" <?php echo (isset($options['information_form']) && $options['information_form'] == 'disable') ? 'selected' : ''; ?>>
                                    <?php echo __('Disable',$this->plugin_name)?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-8 ays_divider_left ays_toggle_target <?php echo (!isset($options['information_form']) || $options['information_form'] == "disable") ? 'display_none' : ''; ?>">
                        <p class="ays_required_field_title"><?php echo __('Form Fields',$this->plugin_name)?></p>
                        <hr>
                        <div class="checkbox_carousel form_fields">
<!--
                            <div class="cb_carousel_arrows">
                                <button type="button" class="button cb_carousel_left">
                                    <i class="ays_fa ays_fa_angle_left"></i>
                                </button>
                                <button type="button" class="button cb_carousel_right">
                                    <i class="ays_fa ays_fa_angle_right"></i>
                                </button>
                            </div>
-->
                            <div class="form_fields checkbox_carousel_body">
                                <div class="form-check form-check-inline checkbox_ays">
                                    <input type="checkbox" class="form-check-input" id="ays_form_name"
                                           name="ays_form_name"
                                           value="on" <?php echo (isset($options['form_name']) && $options['form_name'] == 'on') ? 'checked' : ''; ?>/>
                                    <label class="form-check-label" for="ays_form_name"><?php echo __('Name',$this->plugin_name)?></label>
                                </div>
                                <div class="form-check form-check-inline checkbox_ays">
                                    <input type="checkbox" class="form-check-input" id="ays_form_email"
                                           name="ays_form_email"
                                           value="on" <?php echo (isset($options['form_email']) && $options['form_email'] == 'on') ? 'checked' : ''; ?>/>
                                    <label class="form-check-label" for="ays_form_email"><?php echo __('Email',$this->plugin_name)?></label>
                                </div>
                                <div class="form-check form-check-inline checkbox_ays">
                                    <input type="checkbox" class="form-check-input" id="ays_form_phone"
                                           name="ays_form_phone"
                                           value="on" <?php echo (isset($options['form_phone']) && $options['form_phone'] == 'on') ? 'checked' : ''; ?>/>
                                    <label class="form-check-label" for="ays_form_phone"><?php echo __('Phone',$this->plugin_name)?></label>
                                </div>
                                <?php
                                foreach ($all_attributes as $attribute) {
                                    $checked = (in_array(strval($attribute['id']), $quiz_attributes)) ? 'checked' : '';
                                    echo "<div class=\"form-check form-check-inline checkbox_ays\">
                                            <input type=\"checkbox\" class=\"form-check-input\" id=\"" . $attribute['slug'] . "\" name=\"ays_quiz_attributes[]\"
                                                   value=\"" . $attribute['id'] . "\" " . $checked . "/>
                                            <label class=\"form-check-label\" for=\"" . $attribute['slug'] . "\">" . $attribute['name'] . "</label>
                                        </div>";
                                }
                                ?>
                            </div>
                        </div>
                        <hr>
                        <p class="ays_required_field_title"><?php echo __('Required Fields',$this->plugin_name)?></p>
                        <hr>
                        <div class="checkbox_carousel required_fields">
<!--
                            <div class="cb_carousel_arrows">
                                <button type="button" class="button cb_carousel_left">
                                    <i class="ays_fa ays_fa_angle_left"></i>
                                </button>
                                <button type="button" class="button cb_carousel_right">
                                    <i class="ays_fa ays_fa_angle_right"></i>
                                </button>
                            </div>
-->
                                <div class="required_fields checkbox_carousel_body">
                                    <div class="form-check form-check-inline checkbox_ays">
                                        <input type="checkbox" class="form-check-input" id="ays_form_name_required"
                                               name="ays_required_field[]"
                                               value="ays_user_name" <?php echo (in_array('ays_user_name', $required_fields)) ? 'checked' : ''; ?>/>
                                        <label class="form-check-label" for="ays_form_name_required"><?php echo __('Name',$this->plugin_name)?></label>
                                    </div>
                                    <div class="form-check form-check-inline checkbox_ays">
                                        <input type="checkbox" class="form-check-input" id="ays_form_email_required"
                                               name="ays_required_field[]"
                                               value="ays_user_email" <?php echo (in_array('ays_user_email', $required_fields)) ? 'checked' : ''; ?>/>
                                        <label class="form-check-label" for="ays_form_email_required"><?php echo __('Email',$this->plugin_name)?></label>
                                    </div>
                                    <div class="form-check form-check-inline checkbox_ays">
                                        <input type="checkbox" class="form-check-input" id="ays_form_phone_required"
                                               name="ays_required_field[]"
                                               value="ays_user_phone" <?php echo (in_array('ays_user_phone', $required_fields)) ? 'checked' : ''; ?>/>
                                        <label class="form-check-label" for="ays_form_phone_required"><?php echo __('Phone',$this->plugin_name)?></label>
                                    </div>
                                    <?php
                                    foreach ($all_attributes as $attribute) {
                                        $checked = (in_array(strval($attribute['slug']), $required_fields)) ? 'checked' : '';
                                        if (isset($attribute) && $attribute['type'] != 'select'){
                                            
                                            echo "<div class=\"form-check form-check-inline checkbox_ays\">
                                                <input type=\"checkbox\" class=\"form-check-input\" id=\"" . $attribute['slug'] . "_required\" name=\"ays_required_field[]\"
                                                       value=\"" . $attribute['slug'] . "\" " . $checked . "/>
                                                <label class=\"form-check-label\" for=\"" . $attribute['slug'] . "_required\">" . $attribute['name'] . "</label>
                                            </div>";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Add custom fields',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can add form custom fields from “Custom fields” page in Quiz Maker menu.  (text, textarea, checkbox, select, URL etc.)',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8 ays_divider_left">
                        <blockquote>
                            <?php echo __("For creating custom fields click ", $this->plugin_name); ?>
                            <a href="?page=<?php echo $this->plugin_name; ?>-quiz-attributes" ><?php echo __("here", $this->plugin_name); ?></a>
                        </blockquote>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_autofill_user_data">
                            <?php echo __('Autofill logged in user data',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, logged in  user’s name and email will be autofilled in Information Form.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8 ays_divider_left">
                        <div class="information_form_settings">
                            <input type="checkbox" id="ays_autofill_user_data" name="ays_autofill_user_data" value="on" <?php echo $autofill_user_data ? "checked" : ""; ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab7" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab7') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('E-mail and Certificate settings',$this->plugin_name)?></p>                
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_mail_user">
                            <?php echo __('Send Mail To User',$this->plugin_name)?>
                            <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send mail to user after quiz completion.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_mail_user"
                               name="ays_enable_user_mail"
                               value="on" <?php echo (isset($options['user_mail']) && $options['user_mail'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['user_mail']) && $options['user_mail'] == 'on') ? '' : 'display_none'; ?>">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_mail_message">
                                    <?php echo __('Mail message',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide the message text for sending to the user by email. You can use Variables from General Settings page to insert user’s data. (name, score, date etc.)',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <?php
                                $content = wpautop(stripslashes((isset($options['mail_message'])) ? $options['mail_message'] : ''));
                                $editor_id = 'ays_mail_message';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_mail_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                        <hr>
                        <!-- AV -->
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_send_results_user">
                                    <?php echo __('Send Results to User',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send results report to user after quiz completion.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="checkbox" class="ays-enable-timerl" id="ays_send_results_user"
                               name="ays_send_results_user"
                               value="on" <?php echo $send_results_user ?>/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_send_interval_msg">
                                    <?php echo __('Send Interval message to User',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send interval message to user after quiz completion. Your specified interval messages will be sent to the user. You can provide them at the bottom of the Results Settings page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="checkbox" class="ays-enable-timerl" id="ays_send_interval_msg"
                                       name="ays_send_interval_msg" value="on" <?php echo $send_interval_msg; ?>/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_certificate">
                            <?php echo __('Send Certificate To User',$this->plugin_name)?>
                            <a  class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Send Certificate PDF file to user after quiz completion. Configure the PDF file’s content with the following options.", $this->plugin_name ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_certificate"
                               name="ays_enable_certificate"
                               value="on" <?php echo (isset($options['enable_certificate']) && $options['enable_certificate'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_divider_left ays_toggle_target <?php echo (isset($options['enable_certificate']) && $options['enable_certificate'] == "on") ? "" : "display_none" ?>">
                        <div class="form-group row">
                            <div class="col-sm-3" style="padding-right: 10px;">
                                <label for="ays_certificate_pass">
                                    <?php echo __('Certificate pass score',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Minimum score to receive a certificate (by percentage)',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" id="ays_certificate_pass" name="ays_certificate_pass" class="ays-text-input"
                                       value="<?php echo (isset($options['certificate_pass'])) ? $options['certificate_pass'] : 0 ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_certificate_title">
                                    <?php echo __('Certificate title',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of certificate',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <?php
                                $content = wpautop(stripslashes((isset($options['certificate_title'])) ? $options['certificate_title'] : ''));
                                $editor_id = 'ays_certificate_title';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_certificate_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_certificate_body">
                                    <?php echo __('Certificate body',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the content of the certificate PDF file. You can copy Variables from General Settings and insert them here.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <?php
                                $content = wpautop(stripslashes((isset($options['certificate_body'])) ? $options['certificate_body'] : ''));
                                $editor_id = 'ays_certificate_body';
                                $settings = array('editor_height' => '8', 'textarea_name' => 'ays_certificate_body', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                                wp_editor($content, $editor_id, $settings);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_smtp">
                            <?php echo __('Use SMTP',$this->plugin_name)?>
                            <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('For using this option you need to fill in the whole fields intended for it.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_smtp"
                               name="ays_enable_smtp"
                               value="on" <?php echo (isset($options['enable_smtp']) && $options['enable_smtp'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_smtp']) && $options['enable_smtp'] == "on") ? "" : "display_none" ?>">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays-smtp-name"><?php echo __('SMTP Username',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" id="ays-smtp-name" name="ays_smtp_username"
                                       class="ays-text-input"
                                       value="<?php echo (isset($options['smtp_username']) && $options['smtp_username'] != '') ? $options['smtp_username'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays-smtp-password"><?php echo __('SMTP Password',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="password" id="ays-smtp-password" name="ays_smtp_password"
                                       class="ays-text-input"
                                       value="<?php echo (isset($options['smtp_password']) && $options['smtp_password'] != '') ? $options['smtp_password'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays-smtp-host"><?php echo __('Host',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" id="ays-smtp-host" name="ays_smtp_host"
                                       class="ays-text-input"
                                       value="<?php echo (isset($options['smtp_host']) && $options['smtp_host'] != '') ? $options['smtp_host'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays-smtp-secure"><?php echo __('SMTP Secure',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-9">
                                <select name="ays_smtp_secure" id="ays_smtp_secures"
                                        class="ays-text-input">
                                    <option value="ssl" <?php echo (isset($options['smtp_secure']) && $options['smtp_secure'] == 'ssl') ? 'selected' : '' ?>>
                                        <?php echo __('SSL',$this->plugin_name)?>
                                    </option>
                                    <option value="tls" <?php echo (isset($options['smtp_secure']) && $options['smtp_secure'] == 'tls') ? 'selected' : '' ?>>
                                        <?php echo __('TLS',$this->plugin_name)?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays-smtp-port"><?php echo __('Port',$this->plugin_name)?></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" id="ays-smtp-port" name="ays_smtp_port"
                                       class="ays-text-input ays-text-input-short"
                                       value="<?php echo (isset($options['smtp_port']) && $options['smtp_port'] != '') ? $options['smtp_port'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row ays_toggle_parent">
                    <div class="col-sm-3">
                        <label for="ays_enable_mail_admin">
                            <?php echo __('Send Mail To Admin',$this->plugin_name)?>
                            <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Every time when someone passes the Quiz, it sends an email with information about each Quiz result to the admin email from WordPress General Settings.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_mail_admin"
                               name="ays_enable_admin_mail"
                               value="on" <?php echo (isset($options['admin_mail']) && $options['admin_mail'] == 'on') ? 'checked' : ''; ?>/>
                    </div>
                    <div class="col-sm-8 ays_toggle_target ays_divider_left" style="<?php echo (isset($options['admin_mail']) && $options['admin_mail'] == "on") ? "" : "display:none;" ?>">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_additional_emails">
                                    <?php echo __('Additional Emails',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send quiz results to additional emails. Insert emails comma seperated.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_additional_emails"
                                       name="ays_additional_emails"
                                       value="<?php echo $additional_emails; ?>" placeholder="example1@gmail.com, example2@gmail.com, ..."/>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_send_results_admin">
                                    <?php echo __('Send Report table to Admin',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('You can send results to the admin after the quiz is completed',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="checkbox" class="ays-enable-timerl" id="ays_send_results_admin"
                                       name="ays_send_results_admin" value="on" <?php echo $send_results_admin; ?>/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_send_interval_msg_to_admin">
                                    <?php echo __('Send Interval message to Admin',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('If this option is enabled then the admin will get the Email with Interval message.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="checkbox" class="ays-enable-timerl" id="ays_send_interval_msg_to_admin"
                                       name="ays_send_interval_msg_to_admin" value="on" <?php echo $send_interval_msg_to_admin; ?>/>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Email Configuration',$this->plugin_name)?>
                            <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Attributes of Sending Mail',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8 ays_divider_left" id="ays_email_configuration">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_email_configuration_from_email">
                                    <?php echo __('From Email',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify from which email the results will be sent. If you leave it blank, it will take default value as quiz_maker@{your_site_url}',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_email_configuration_from_email"
                                       name="ays_email_configuration_from_email"
                                       value="<?php echo $email_config_from_email; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_email_configuration_from_name">
                                    <?php echo __('From Name',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify from which name the results will be sent. It will take “Quiz Maker” if you don’t complete it.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_email_configuration_from_name"
                                       name="ays_email_configuration_from_name"
                                       value="<?php echo $email_config_from_name; ?>"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_email_configuration_from_subject">
                                    <?php echo __('From Subject',$this->plugin_name)?>
                                    <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide the subject of the mail. It will take the quiz title if you don’t complete it.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_email_configuration_from_subject"
                                       name="ays_email_configuration_from_subject"
                                       value="<?php echo $email_config_from_subject; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab8" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab8') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Integrations settings',$this->plugin_name)?></p>
                <hr/>
                <fieldset>
                    <legend>
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
                        <h5><?php echo __('MailChimp Settings',$this->plugin_name)?></h5>
                    </legend>
                    <?php
                        if(count($mailchimp) > 0):
                    ?>
                        <?php
                            if($mailchimp_username == "" || $mailchimp_api_key == ""):
                        ?>
                        <blockquote class="error_message">
                            <?php echo __( 
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ), 
                                $this->plugin_name ); 
                            ?>
                        </blockquote>
                        <?php
                            else:
                        ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_enable_mailchimp">
                                    <?php echo __('Enable MailChimp',$this->plugin_name)?>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <input type="checkbox" class="ays-enable-timer1" id="ays_enable_mailchimp"
                                       name="ays_enable_mailchimp"
                                       value="on" 
                                       <?php 
                                            if($mailchimp_username == "" || $mailchimp_api_key == ""){
                                                echo "disabled";
                                            }else{
                                                echo ($enable_mailchimp == 'on') ? 'checked' : '';
                                            }
                                       ?>/>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_mailchimp_list">
                                    <?php echo __('MailChimp list',$this->plugin_name)?>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <?php if(is_array($mailchimp_select)): ?>
                                    <select name="ays_mailchimp_list" id="ays_mailchimp_list"
                                       <?php
                                            if($mailchimp_username == "" || $mailchimp_api_key == ""){
                                                echo 'disabled';
                                            }
                                        ?>>
                                        <option value="" disabled selected>Select list</option>
                                    <?php foreach($mailchimp_select as $mlist): ?>
                                        <option <?php echo ($mailchimp_list == $mlist['listId']) ? 'selected' : ''; ?>
                                            value="<?php echo $mlist['listId']; ?>"><?php echo $mlist['listName']; ?></option>
                                    <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <span><?php echo $mailchimp_select; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                            endif;
                        ?>
                    <?php
                        else:
                    ?>
                        <blockquote class="error_message">
                            <?php echo __( 
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ), 
                                $this->plugin_name ); 
                            ?>
                        </blockquote>
                    <?php
                        endif;
                    ?>
                </fieldset>
                <hr/>
                <fieldset>
                    <legend>
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/paypal_logo.png" alt="">
                        <h5><?php echo __('PayPal Settings',$this->plugin_name)?></h5>
                    </legend>
                    <?php
                        $ays_paypal_enabling = ($quiz_paypal['clientId'] == null || $quiz_paypal['clientId'] == '') ? false : true;
                        if(!$ays_paypal_enabling):
                    ?>
                    <blockquote class="error_message">
                        <?php echo __( 
                            sprintf(
                                "For enabling this option, please go to %s page and fill options.",
                                "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                            ), 
                            $this->plugin_name ); 
                        ?>
                    </blockquote>
                    <?php
                        else:
                    ?>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_enable_paypal">
                                <?php echo __('Enable PayPal',$this->plugin_name)?>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_enable_paypal"
                                   name="ays_enable_paypal"
                                   value="on" 
                                   <?php 
                                        if($ays_paypal_enabling){
                                            echo ($enable_paypal == 'on') ? 'checked' : ''; 
                                        }else{
                                            echo 'disabled';
                                        }
                                   ?>/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_paypal_amount">
                                <?php echo __('Amount',$this->plugin_name)?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" 
                                class="ays-text-input ays-text-input-short" 
                                id="ays_paypal_amount" 
                                name="ays_paypal_amount" 
                                value="<?php echo $paypal_amount; ?>"
                                <?php 
                                    if(!$ays_paypal_enabling){
                                        echo 'disabled';
                                    }
                                ?>
                            />
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_paypal_currency">
                                <?php echo __('Currency',$this->plugin_name)?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <select name="ays_paypal_currency" id="ays_paypal_currency"
                                <?php 
                                    if(!$ays_paypal_enabling){
                                        echo 'disabled';
                                    }
                                ?>>
                                <option <?php echo ($paypal_currency == 'USD') ? 'selected' : ''; ?> value="USD">
                                    USD - <?php echo __( 'United States Dollar', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'EUR') ? 'selected' : ''; ?> value="EUR">
                                    EUR - <?php echo __( 'Euro', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'GBP') ? 'selected' : ''; ?> value="GBP">
                                    GBP - <?php echo __( 'British Pound Sterling', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'CHF') ? 'selected' : ''; ?> value="CHF">
                                    CHF - <?php echo __( 'Swiss Franc', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'JPY') ? 'selected' : ''; ?> value="JPY">
                                    JPY - <?php echo __( 'Japanese Yen', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'INR') ? 'selected' : ''; ?> value="INR">
                                    INR - <?php echo __( 'Indian Rupee', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'CNY') ? 'selected' : ''; ?> value="CNY">
                                    CNY - <?php echo __( 'Chinese Yuan', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'CAD') ? 'selected' : ''; ?> value="CAD">
                                    CAD - <?php echo __( 'Canadian Dollar', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'AED') ? 'selected' : ''; ?> value="AED">
                                    AED - <?php echo __( 'United Arab Emirates Dirham', $this->plugin_name ); ?></option>
                                <option <?php echo ($paypal_currency == 'RUB') ? 'selected' : ''; ?> value="RUB">
                                    RUB - <?php echo __( 'Russian Ruble', $this->plugin_name ); ?></option>
                            </select>
                        </div>
                    </div>
                    <?php
                        endif;
                    ?>
                </fieldset>
                <hr/>
                <fieldset>
                    <legend>                        
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/campaignmonitor_logo.png" alt="">
                        <h5><?php echo __('Campaign Monitor Settings', $this->plugin_name) ?></h5>
                    </legend>
                    <?php
                    if (count($monitor) > 0):
                        ?>
                        <?php
                        if ($monitor_client == "" || $monitor_api_key == ""):
                            ?>
                            <blockquote class="error_message">
                                <?php echo __(
                                    sprintf(
                                        "For enabling this option, please go to %s page and fill all options.",
                                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                    ),
                                    $this->plugin_name);
                                ?>
                            </blockquote>
                        <?php
                        else:
                            ?>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_enable_monitor">
                                        <?php echo __('Enable Campaign Monitor', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-1">
                                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_monitor"
                                           name="ays_enable_monitor"
                                           value="on"
                                        <?php
                                        if ($monitor_client == "" || $monitor_api_key == "") {
                                            echo "disabled";
                                        } else {
                                            echo ($enable_monitor == 'on') ? 'checked' : '';
                                        }
                                        ?>/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_monitor_list">
                                        <?php echo __('Campaign Monitor list', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <?php if (is_array($monitor_select)): ?>
                                        <select name="ays_monitor_list" id="ays_monitor_list"
                                            <?php
                                            if ($monitor_client == "" || $monitor_api_key == "") {
                                                echo 'disabled';
                                            }
                                            ?>>
                                            <option value="" disabled selected><?= __("Select List", $this->plugin_name) ?></option>
                                            <?php foreach ( $monitor_select as $mlist ): ?>
                                                <option <?= ($monitor_list == $mlist['ListID']) ? 'selected' : ''; ?>
                                                        value="<?= $mlist['ListID']; ?>"><?php echo $mlist['Name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span><?php echo $monitor_select; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        endif;
                        ?>
                    <?php
                    else:
                        ?>
                        <blockquote class="error_message">
                            <?php echo __(
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ),
                                $this->plugin_name);
                            ?>
                        </blockquote>
                    <?php
                    endif;
                    ?>
                </fieldset>
                <hr/>                
                <fieldset>
                    <legend>
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/zapier_logo.png" alt="">
                        <h5><?php echo __('Zapier Integration Settings', $this->plugin_name) ?></h5>
                    </legend>
                    <?php
                    if (count($zapier) > 0):
                        ?>
                        <?php
                        if ($zapier_hook == ""):
                            ?>
                            <blockquote class="error_message">
                                <?php echo __(
                                    sprintf(
                                        "For enabling this option, please go to %s page and fill all options.",
                                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                    ),
                                    $this->plugin_name);
                                ?>
                            </blockquote>
                        <?php else: ?>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_enable_zapier">
                                        <?php echo __('Enable Zapier Integration', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-1">
                                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_zapier"
                                           name="ays_enable_zapier"
                                           value="on"
                                        <?php
                                        if ($zapier_hook == "") {
                                            echo "disabled";
                                        } else {
                                            echo ($enable_zapier == 'on') ? 'checked' : '';
                                        }
                                        ?>/>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button"
                                            data-url="<?= $zapier_hook ?>" <?= $zapier_hook ? "" : "disabled" ?>
                                            id="testZapier"
                                            class="btn btn-outline-secondary">
                                        <?= __("Send test data", $this->plugin_name) ?>
                                    </button>
                                    <a class="ays_help" data-toggle="tooltip" style="font-size: 16px;"
                                       title="<?= __('We will send you a test data, and you can catch it in your ZAP for configure it.', $this->plugin_name) ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </div>
                            </div>
                            <div id="testZapierFields" class="d-none">
                                <input type="checkbox" name="zapierTest[]" value="ays_user_name" data-name="Name" checked/>
                                <input type="checkbox" name="zapierTest[]" value="ays_user_email" data-name="E-mail" checked/>
                                <input type="checkbox" name="zapierTest[]" value="ays_user_phone" data-name="Phone" checked/>
                                <?php
                                foreach ( $all_attributes as $attribute ) {
                                    $checked = (in_array(strval($attribute['id']), $quiz_attributes)) ? 'checked' : '';
                                    echo "<input type=\"checkbox\" name=\"zapierTest[]\" value=\"" . $attribute['slug'] . "\" data-name=\"".$attribute['name']."\" checked/>";
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php
                    else:
                        ?>
                        <blockquote class="error_message">
                            <?php echo __(
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ),
                                $this->plugin_name);
                            ?>
                        </blockquote>
                    <?php
                    endif;
                    ?>
                </fieldset>
                <hr/>                
                <fieldset>
                    <legend>
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/activecampaign_logo.png" alt="">
                        <h5><?php echo __('ActiveCampaign Settings', $this->plugin_name) ?></h5>
                    </legend>
                    <?php
                    if (count($active_camp) > 0):
                        ?>
                        <?php
                        if ($active_camp_url == "" || $active_camp_api_key == ""):
                            ?>
                            <blockquote class="error_message">
                                <?php echo __(
                                    sprintf(
                                        "For enabling this option, please go to %s page and fill all options.",
                                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                    ),
                                    $this->plugin_name);
                                ?>
                            </blockquote>
                        <?php
                        else:
                            ?>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_enable_active_camp">
                                        <?php echo __('Enable ActiveCampaign', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-1">
                                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_active_camp"
                                           name="ays_enable_active_camp"
                                           value="on"
                                        <?php
                                        if ($active_camp_url == "" || $active_camp_api_key == "") {
                                            echo "disabled";
                                        } else {
                                            echo ($enable_active_camp == 'on') ? 'checked' : '';
                                        }
                                        ?>/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_active_camp_list">
                                        <?php echo __('ActiveCampaign list', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <?php if (is_array($active_camp_list_select)): ?>
                                        <select name="ays_active_camp_list" id="ays_active_camp_list"
                                            <?php
                                            if ($active_camp_url == "" || $active_camp_api_key == "") {
                                                echo 'disabled';
                                            }
                                            ?>>
                                            <option value="" disabled
                                                    selected><?= __("Select List", $this->plugin_name) ?></option>
                                            <option value=""><?= __("Just create contact", $this->plugin_name) ?></option>
                                            <?php foreach ( $active_camp_list_select as $list ): ?>
                                                <option <?= ($active_camp_list == $list['id']) ? 'selected' : ''; ?>
                                                        value="<?= $list['id']; ?>"><?= $list['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span><?php echo $active_camp_list_select; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_active_camp_automation">
                                        <?php echo __('ActiveCampaign automation', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <?php if (is_array($active_camp_automation_select)): ?>
                                        <select name="ays_active_camp_automation" id="ays_active_camp_automation"
                                            <?php
                                            if ($active_camp_url == "" || $active_camp_api_key == "") {
                                                echo 'disabled';
                                            }
                                            ?>>
                                            <option value="" disabled
                                                    selected><?= __("Select List", $this->plugin_name) ?></option>
                                            <option value=""><?= __("Just create contact", $this->plugin_name) ?></option>
                                            <?php foreach ( $active_camp_automation_select as $automation ): ?>
                                                <option <?= ($active_camp_automation == $automation['id']) ? 'selected' : ''; ?>
                                                        value="<?= $automation['id']; ?>"><?= $automation['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span><?php echo $active_camp_automation_select; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        endif;
                        ?>
                    <?php
                    else:
                        ?>
                        <blockquote class="error_message">
                            <?php echo __(
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ),
                                $this->plugin_name);
                            ?>
                        </blockquote>
                    <?php
                    endif;
                    ?>
                </fieldset>
                <hr/>
                <fieldset>
                    <legend>
                        <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/slack_logo.png" alt="">
                        <h5><?php echo __('Slack Settings', $this->plugin_name) ?></h5>
                    </legend>
                    <?php
                    if (count($slack) > 0):
                        ?>
                        <?php
                        if ($slack_token == ""):
                            ?>
                            <blockquote class="error_message">
                                <?php echo __(
                                    sprintf(
                                        "For enabling this option, please go to %s page and fill all options.",
                                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                    ),
                                    $this->plugin_name);
                                ?>
                            </blockquote>
                        <?php
                        else:
                            ?>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_enable_slack">
                                        <?php echo __('Enable Slack integration', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-1">
                                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_slack"
                                           name="ays_enable_slack"
                                           value="on"
                                        <?php
                                        if ($slack_token == "") {
                                            echo "disabled";
                                        } else {
                                            echo ($enable_slack == 'on') ? 'checked' : '';
                                        }
                                        ?>/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_slack_conversation">
                                        <?php echo __('Slack conversation', $this->plugin_name) ?>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <?php if (is_array($slack_select)): ?>
                                        <select name="ays_slack_conversation" id="ays_slack_conversation"
                                            <?php
                                            if ($slack_token == "") {
                                                echo 'disabled';
                                            }
                                            ?>>
                                            <option value="" disabled
                                                    selected><?= __("Select Channel", $this->plugin_name) ?></option>
                                            <?php foreach ( $slack_select as $conversation ): ?>
                                                <option <?= ($slack_conversation == $conversation['id']) ? 'selected' : ''; ?>
                                                        value="<?= $conversation['id']; ?>"><?php echo $conversation['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span><?php echo $slack_select; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        endif;
                        ?>
                    <?php
                    else:
                        ?>
                        <blockquote class="error_message">
                            <?php echo __(
                                sprintf(
                                    "For enabling this option, please go to %s page and fill all options.",
                                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>this</a>"
                                ),
                                $this->plugin_name);
                            ?>
                        </blockquote>
                    <?php
                    endif;
                    ?>
                </fieldset>
            </div>
            <hr/>
            <?php
                wp_nonce_field('quiz_action', 'quiz_action');
                $other_attributes = array();
                submit_button(__('Save and close', $this->plugin_name), 'primary', 'ays_submit', true, $other_attributes);
                submit_button(__('Save', $this->plugin_name), '', 'ays_apply', true, $other_attributes);
            ?>
        </form>
    </div>
</div>

<div id="ays-questions-modal" class="ays-modal">
    <!-- Modal content -->
    <div class="ays-modal-content">
        <form method="post" id="ays_add_question_rows">
            <div class="ays-quiz-preloader">
                <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/cogs.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close">&times;</span>
                <h2><?php echo __('Select questions', $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body">
                <?php
                wp_nonce_field('add_question_rows_top', 'add_question_rows_top_second');
                $other_attributes = array();
                submit_button(__('Select questions', $this->plugin_name), 'primary', 'add_question_rows_top', true, $other_attributes);
                ?>
                <span style="font-size: 13px; font-style: italic;">
                    <?php echo __('For select questions click on question row and then click "Select questions" button', $this->plugin_name); ?>
                </span>
                <p style="font-size: 16px; padding-right:20px; margin:0; text-align:right;">
                    <a class="" href="admin.php?page=<?php echo $this->plugin_name; ?>-questions&action=add" target="_blank"><?php echo __('Create question', $this->plugin_name); ?></a>
                </p>
                <div class="row" style="margin:0;">
                    <div class="col-sm-12" id="quest_cat_container">
                        <label style="width:100%;" for="add_quest_category_filter">
                            <p style="font-size: 13px; margin:0; font-style: italic;">
                                <?php echo __( "Filter by category", $this->plugin_name); ?>
                                <button type="button" class="ays_filter_cat_clear button button-small wp-picker-default"><?php echo __( "Clear", $this->plugin_name ); ?></button>
                            </p>
                        </label>
                        <select id="add_quest_category_filter" multiple="multiple" class='cat_filter custom-select custom-select-sm form-control form-control-sm'>
                            <?php
                                $quiz_cats = $this->get_questions_categories();
                                foreach($quiz_cats as $cat){
                                    echo "<option value='".$cat['title']."'>".$cat['title']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="ays-add-questions-table hover order-column" id="ays-question-table-add" data-page-length='5'>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo __('Question', $this->plugin_name); ?></th>
                            <th><?php echo __('Type', $this->plugin_name); ?></th>
                            <th style="width:250px;"><?php echo __('Created', $this->plugin_name); ?></th>
                            <th><?php echo __('Category', $this->plugin_name); ?></th>
                            <th><?php echo __('Used', $this->plugin_name); ?></th>
                            <th style="width:50px;">ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($questions as $index => $question) {
                            $question_options = json_decode($question['options'], true);
                            $date = isset($question['create_date']) && $question['create_date'] != '' ? $question['create_date'] : "0000-00-00 00:00:00";
                            if(isset($question_options['author'])){
                                if(is_array($question_options['author'])){
                                    $author = $question_options['author'];
                                }else{
                                    $author = json_decode($question_options['author'], true);
                                }
                            }else{
                                $author = array("name"=>"Unknown");
                            }
                            $text = "";
                            if(Quiz_Maker_Admin::validateDate($date)){
                                $text .= "<p style='margin:0;text-align:left;'><b>Date:</b> ".$date."</p>";
                            }
                            if($author['name'] !== "Unknown"){
                                $text .= "<p style='margin:0;text-align:left;'><b>Author:</b> ".$author['name']."</p>";
                            }
                            $selected_question = (in_array($question["id"], $question_id_array)) ? "selected" : "";
                            $table_question = (strip_tags(stripslashes($question['question'])));
                            $table_question = $this->ays_restriction_string("word", $table_question, 8);

                            $used = "False";

                            if( in_array($question["id"], $used_questions) ){
                                $used = "True";
                            }

                            ?>
                            <tr class="ays_quest_row <?php echo $selected_question; ?>" data-id='<?php echo $question["id"]; ?>'>
                                <td>
                                    <span>
                                    <?php if (in_array($question["id"], $question_id_array)) : ?>
                                       <i class="ays-select-single ays_fa ays_fa_check_square_o"></i>
                                    <?php else: ?>
                                       <i class="ays-select-single ays_fa ays_fa_square_o"></i>
                                    <?php endif; ?>
                                    </span>
                                </td>
                                <td class="ays-modal-td-question"><?php echo $table_question; ?></td>
                                <td><?php echo $question["type"]; ?></td>
                                <td><?php echo $text; ?></td>
                                <td class="ays-modal-td-category"><?php echo stripslashes($question["title"]); ?></td>
                                <td class="ays-modal-td-used"><?php echo $used; ?></td>
                                <td><?php echo $question["id"]; ?></td>

                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ays-modal-footer" style="justify-content:flex-start;">
                <?php
                wp_nonce_field('add_question_rows', 'add_question_rows');
                $other_attributes = array('id' => 'ays-button');
                submit_button(__('Select questions', $this->plugin_name), 'primary', 'add_question_rows', true, $other_attributes);
                ?>
            </div>
        </form>
    </div>
</div>
