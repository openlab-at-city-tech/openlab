<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;
    
    
    protected $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('ays_quiz', array($this, 'ays_generate_quiz_method'));
        add_shortcode('ays_user_page', array($this, 'ays_generate_user_page_method'));
        add_shortcode('ays_quiz_leaderboard', array($this, 'ays_generate_leaderboard_list'));
        add_shortcode('ays_quiz_gleaderboard', array($this, 'ays_generate_gleaderboard_list'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name.'-font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'sweetalert-css', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-animate', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-animations', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-rating', plugin_dir_url(__FILE__) . 'css/rating.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-loaders', plugin_dir_url(__FILE__) . 'css/loaders.css', array(), $this->version, 'all');

    }

    public function enqueue_styles_early(){
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(){

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script("jquery-effects-core");
        wp_enqueue_script($this->plugin_name.'select2js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name.'sweetalert-js', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.all.min.js', array('jquery'), $this->version, true );
        wp_enqueue_script ($this->plugin_name .'-rate-quiz', plugin_dir_url(__FILE__) . 'js/rating.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name .'-functions.js', plugin_dir_url(__FILE__) . 'js/functions.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-ajax-public', plugin_dir_url(__FILE__) . 'js/quiz-maker-public-ajax.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-public.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-ajax-public', 'quiz_maker_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_localize_script( $this->plugin_name, 'quizLangObj', array(
            'notAnsweredText'       => __( 'You have not answered to this question', $this->plugin_name ),
            'areYouSure'            => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
            'sorry'                 => __( 'Sorry', $this->plugin_name ),
            'unableStoreData'       => __( 'We are unable to store your data', $this->plugin_name ),
            'connectionLost'        => __( 'Connection is lost', $this->plugin_name ),
            'checkConnection'       => __( 'Please check your connection and try again', $this->plugin_name ),
            'selectPlaceholder'     => __( 'Select an answer', $this->plugin_name ),
            'shareDialog'           => __( 'Share Dialog', $this->plugin_name )
        ) );
    }
    
    public function ays_generate_quiz_method($attr){
        ob_start();
        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
        
        if (is_null($id)) {
            echo "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return false;
        }
        
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        $this->show_quiz($id, $attr);
        return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());        
    }
    
    public function show_quiz($id, $attr){
        if( ! session_id() ){
            session_start();
        }
        $quiz = $this->get_quiz_by_id($id);
        
        if (is_null($quiz)) {
            echo "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return false;
        }
        if (intval($quiz['published']) === 0) {
            return false;
        }
        
        
        $options = json_decode($quiz['options'], true);
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        $paypal_client_id = isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '' ? $quiz_integrations['paypal_client_id'] : null;
        $quiz_paypal = (isset($options['enable_paypal']) && $options['enable_paypal'] == "on") ? true : false;
        
        if($quiz_paypal){
            if($paypal_client_id == null || $paypal_client_id == ''){
                $attr['quiz_paypal'] = null;
            }else{
                wp_enqueue_script(
                    $this->plugin_name . '-paypal',
                    "https://www.paypal.com/sdk/js?client-id=".$quiz_integrations['paypal_client_id']."&currency=".$options['paypal_currency']."",
                    array('jquery'),
                    null,
                    true
                );
                $attr['quiz_paypal'] = '<div class="ays_paypal_div">
                    <p>'.__('You need to pay to pass this quiz.').'</p>
                    <div id="ays_quiz_paypal_button_container_'.$id.'"></div>                    
                </div>
                    <script>
                        (function($){
                            $(document).ready(function(){
                                paypal.Buttons({
                                    createOrder: function(data, actions) {
                                        return actions.order.create({
                                            purchase_units: [{
                                                amount: {
                                                    value: "'.$options['paypal_amount'].'"
                                                }
                                            }]
                                        });
                                    },
                                    onApprove: function(data, actions) {
                                        return actions.order.capture().then(function(details) {
                                            return fetch("'. AYS_QUIZ_PUBLIC_URL .'/partials/paypal-transaction-complete.php", {
                                                method: "post",
                                                headers: {
                                                    "Content-Type": "application/json"
                                                },
                                                body: JSON.stringify({
                                                    data: data,
                                                    details: details,
                                                    quizId: '.$id.'
                                                }),
                                                credentials: "same-origin"
                                            }).then(response => response.json())
                                            .then(data => {
                                                Swal.fire({
                                                    title:"Your payment successfuly finished.",
                                                    type: "success",
                                                    showCancelButton: false,
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    allowEnterKey: false,
                                                    width: "450px",
                                                }).then((result) => {
                                                    location.reload();
                                                });
                                            }).catch(error => console.error(error));
                                        });
                                    }
                                }).render("#ays_quiz_paypal_button_container_'.$id.'");
                            });
                        })(jQuery);
                    </script>';
            }
        }else{
            $attr['quiz_paypal'] = null;
        }
        
        if(is_user_logged_in()){
            if($payment_terms == "onetime"){
                if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                    if($_SESSION['ays_quiz_paypal_purchase'] == true){
                        $attr['quiz_paypal'] = null;
                    }
                }else{
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                }
            }elseif($payment_terms == "lifetime"){
                $current_user = wp_get_current_user();
                $current_usermeta = get_user_meta($current_user->data->ID, "quiz_paypal_purchase");
                if($current_usermeta !== false && !empty($current_usermeta)){
                    foreach($current_usermeta as $usermeta){
                        if($id == json_decode($usermeta, true)['quizId']){
                            $quiz_paypal_usermeta = json_decode($usermeta, true);
                            break;
                        }else{
                            $quiz_paypal_usermeta = false;
                        }
                    }
                    if($quiz_paypal_usermeta !== false){
                        if($quiz_paypal_usermeta['purchased'] == true){
                            $attr['quiz_paypal'] = null;
                        }
                    }else{
                        $opts = json_encode(array(
                            'quizId' => $id,
                            'purchased' => false
                        ));
                        add_user_meta($current_user->data->ID, "quiz_paypal_purchase", $opts);
                    }
                }else{
                    $opts = json_encode(array(
                        'quizId' => $id,
                        'purchased' => false
                    ));
                    add_user_meta($current_user->data->ID, "quiz_paypal_purchase", $opts);
                }
            }
        }else{
            if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                if($_SESSION['ays_quiz_paypal_purchase'] == true){
                    $attr['quiz_paypal'] = null;
                }
            }else{
                $_SESSION['ays_quiz_paypal_purchase'] = false;
            }
        }
        if($enable_copy_protection){
            wp_enqueue_script ($this->plugin_name .'-quiz_copy_protection', plugin_dir_url(__FILE__) . 'js/quiz_copy_protection.min.js', array('jquery'), $this->version, true);
        }
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';
        $quiz_parts = $this->ays_quiz_parts($id, $attr['quiz_paypal'], $quiz_integrations);
        
        switch ($options['quiz_theme']) {
            case 'elegant_dark':
                include_once('partials/class-quiz-theme-elegant-dark.php');
                $theme_obj = new Quiz_Theme_Elegant_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_dark');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'elegant_light':
                include_once('partials/class-quiz-theme-elegant-light.php');
                $theme_obj = new Quiz_Theme_Elegant_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_light');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_light':
                include_once('partials/class-quiz-theme-rect-light.php');
                $theme_obj = new Quiz_Theme_Rect_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_light');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_dark':
                include_once('partials/class-quiz-theme-rect-dark.php');
                $theme_obj = new Quiz_Theme_Rect_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_dark');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'modern_light':
                include_once('partials/class-quiz-theme-modern-light.php');
                $theme_obj = new Quiz_Theme_Modern_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'modern_light');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'modern_dark':
                include_once('partials/class-quiz-theme-modern-dark.php');
                $theme_obj = new Quiz_Theme_Modern_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'modern_light');
                echo $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            default:
                echo $this->ays_generate_quiz($quiz_parts);
        }

    }
    
    public function ays_quiz_parts($id, $paypal, $paypal_options){
        
        global $wpdb;        
        
    /*******************************************************************************************************/
        
        /*
         * Get Quiz data from database by id
         * Separation options from quiz data
         */
        $quiz = $this->get_quiz_by_id($id);
        $options = json_decode($quiz['options'], true);
        
        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode($settings_options, true);
        }else{
            $settings_options = array();
        }

    /*******************************************************************************************************/
        
        /* 
        ========================================== 
            Quiz questions
            Quetion bank
        ========================================== 
        */
        
        $randomize_answers = false;
        $questions = null;
        $quiz_questions_ids = "";
        
        $arr_questions = ($quiz["question_ids"] == "") ? array() : explode(',', $quiz["question_ids"]);
        $arr_questions = (count($arr_questions) == 1 && $arr_questions[0] == '') ? array() : $arr_questions;
        
        $quiz_questions_ids = join(',', $arr_questions);
        
        $question_bank = false;
        $question_bank_type = 'general';
        $question_bank_count = 0;
        $questions_bank_cat_count = array();
        $custom_cat_id = 0;
        
        if(isset($options['enable_question_bank']) && $options['enable_question_bank'] == "on"){
            $question_bank = true;
        }
        if(isset($options['questions_count']) && intval($options['questions_count']) != 0){
            $question_bank_count = intval($options['questions_count']);
        }
        if(isset($options['question_bank_type']) && $options['question_bank_type'] != ''){
            $question_bank_type = $options['question_bank_type'];
        }
        if(isset($options['questions_bank_cat_count']) && !empty($options['questions_bank_cat_count'])){
            $questions_bank_cat_count = $options['questions_bank_cat_count'];
        }
        if($question_bank){
            if($question_bank_type == 'general'){
                if ($question_bank_count > 0 && $question_bank_count <= count($arr_questions)) {
                    $random_questions = array_rand($arr_questions, $question_bank_count);
                    foreach ($random_questions as $key => $question) {
                        $random_questions[$key] = strval($arr_questions[$question]);
                    }
                    $arr_questions = $random_questions;
                    $quiz_questions_ids = join(',', $random_questions);
                }
            }elseif($question_bank_type == 'by_category'){
                $question_bank_questions = array();
                $question_bank_cats = array();
                $quiz_questions_ids = array();
                $question_bank_by_categories1 = array();

                foreach($arr_questions as $key => $val){
                    $question_bank_questions[$val] = $this->get_quiz_question_by_id(intval($val));
                    $question_bank_cats[$question_bank_questions[$val]['category_id']][] = strval($val);
                }
                if(!empty($question_bank_cats)){                
                    foreach($question_bank_cats as $key => $value){
                        if(intval($questions_bank_cat_count[strval($key)]) > count($value)){
                            $rand_count = count($value);
                        }else{
                            $rand_count = intval($questions_bank_cat_count[strval($key)]);
                        }
                        if($rand_count === 0){
                            continue;
                        }
                        $rand_cat_questions = array_rand($value, $rand_count);

                        if(is_array($rand_cat_questions)){
                            foreach($rand_cat_questions as $k => $v){
                                $rand_cat_questions[$k] = $value[strval($v)];
                                $quiz_questions_ids[] = $value[strval($v)];
                            }
                            $question_bank_by_categories1[strval($key)] = $rand_cat_questions;
                        }else{
                            $rand_cat_question = strval($rand_cat_questions);
                            $rand_cat_questions = array();
                            $rand_cat_questions[] = $value[$rand_cat_question];

                            $quiz_questions_ids[] = $value[strval($rand_cat_question)];
                            $question_bank_by_categories1[strval($key)] = $rand_cat_questions;
                        }                    
                    }                
                }

                $question_bank_by_categories = array();            
                foreach($questions_bank_cat_count as $k => $v){
                    if(array_key_exists($k, $question_bank_by_categories1)){
                        $question_bank_by_categories[$k] = $question_bank_by_categories1[$k];
                    }
                }

                $arr_questions = array();
                foreach($question_bank_by_categories as $key => $value){
                    $arr_questions = array_merge($arr_questions, $value);
                }

                $quiz_questions_ids = implode(',', $quiz_questions_ids);
            }
        }
        
        if (isset($options['randomize_questions']) && $options['randomize_questions'] == "on") {
            shuffle($arr_questions);
        }
        
        $questions_count = count($arr_questions);
        
        if (isset($options['randomize_answers']) && $options['randomize_answers'] == "on") {
            $randomize_answers = true;
        }else{
            $randomize_answers = false;
        }

        if(isset($options['enable_correction']) && $options['enable_correction'] == "on"){
            $enable_correction = true;
        }else{
            $enable_correction = false;
        }
        
        // Calculate the score
        $options['calculate_score'] = ! isset($options['calculate_score']) ? 'by_correctness' : $options['calculate_score'];
        $calculate_score = (isset($options['calculate_score']) && $options['calculate_score'] != "") ? $options['calculate_score'] : 'by_correctness';

//        if($calculate_score == 'by_points'){
//            $enable_correction = false;
//        }
        

    /*******************************************************************************************************/
        
        /*
         * Quiz information form fields
         *
         * Checking required filelds
         *
         * Creating HTML code for printing
         */
        
        $form_inputs = null;
        $show_form = null;
        $required_fields = (array_key_exists('required_fields', $options) && !is_null($options['required_fields'])) ? $options['required_fields'] : array();
        $name_required = (in_array('ays_user_name', $required_fields)) ? 'required' : '';
        $email_required = (in_array('ays_user_email', $required_fields)) ? 'required' : '';
        $phone_required = (in_array('ays_user_phone', $required_fields)) ? 'required' : '';
        $form_title = (isset($options['form_title']) && $options['form_title'] != '') ? stripslashes(wpautop($options['form_title'])): '';

        if($options['form_name'] == "on"){
            $show_form = "show";
            $form_inputs .= "<input type='text' name='ays_user_name' placeholder='".__('Name', $this->plugin_name)."' class='ays_quiz_form_input ays_animated_x5ms' " . $name_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_name' placeholder='".__('Name', $this->plugin_name)."' value=''>";
        }
        if($options['form_email'] == "on"){
            $show_form = "show";
            $form_inputs .= "<input type='text' name='ays_user_email' placeholder='".__('Email', $this->plugin_name)."' class='ays_quiz_form_input ays_animated_x5ms' " . $email_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_email' placeholder='".__('Email', $this->plugin_name)."' value=''>";
        }
        if($options['form_phone'] == "on"){
            $show_form = "show";
            $form_inputs .= "<input type='text' name='ays_user_phone' placeholder='".__('Phone Number', $this->plugin_name)."' class='ays_quiz_form_input ays_animated_x5ms' " . $phone_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_phone' placeholder='".__('Phone Number', $this->plugin_name)."' value=''>";
        }
        
        /*
         * Quiz attributes for form fields
         * 
         * Adding Attribute fields to information form
         */
        
        $quiz_attributes = $this->get_quiz_attributes_by_id($id);
        
        if(count($quiz_attributes) !== 0){
            $show_form = "show";
        }
        
        foreach ($quiz_attributes as $attribute) {
            $attr_required = (in_array($attribute->slug, $required_fields)) ? 'required' : '';
            if($attribute->type == "textarea"){
                $form_inputs .= "<textarea name='". $attribute->slug ."' class='ays_quiz_form_input ays_animated_x5ms' placeholder='" . $attribute->name . "' " . $attr_required . "></textarea>";
            }elseif($attribute->type == "select"){
                $attr_options = $attribute->options;
                $attr_options = explode(';', $attr_options);
                $attr_options1 = array();
                $attr_options1[] = "<option value=''>".$attribute->name."</option>";
                foreach ($attr_options as $attr_options_val) {                       
                    $attr_options1[] = "<option value='".trim( $attr_options_val, ' ' )."'>".trim( $attr_options_val, ' ' )."</option>";
                }
                $attr_options2 = implode('', $attr_options1);
                $form_inputs .= 
                "<select name='". $attribute->slug ."' class='ays_quiz_form_input ays_animated_x5ms' " . $attr_required . ">". $attr_options2 ."</select>";
            }elseif($attribute->type == "checkbox"){
                $form_inputs .= "<label class='ays_for_checkbox' " . $attr_required . ">". $attribute->name ."
                 <input type='checkbox' class='ays_quiz_form_input ays_animated_x5ms' name='". $attribute->slug ."' " . $attr_required . "/></label>";
            }else{
                $form_inputs .= "<input type='". $attribute->type ."' class='ays_quiz_form_input ays_animated_x5ms' name='". $attribute->slug ."' placeholder='" . $attribute->name . "' " . $attr_required . "/>";
            }

        }
        
    /*******************************************************************************************************/
        
        /*
         * Quiz colors
         * 
         * Quiz container colors
         */
        
        // Quiz container background color
        
        if(isset($options['bg_color']) && $options['bg_color'] != ''){
            $bg_color = $options['bg_color'];
        }else{
            $bg_color = "#fff";
        }
        
        // Color of elements inside quiz container
        
        if(isset($options['color']) && $options['color'] != ''){
            $color = $options['color'];
        }else{
            $color = "#27ae60";
        }
        
        // Color of text inside quiz container
        
        if(isset($options['text_color']) && $options['text_color'] != ''){
            $text_color = $options['text_color'];
        }else{
            $text_color = "#333";
        }
        
        // Quiz container shadow color
        
        // CHecking exists box shadow option
        $options['enable_box_shadow'] = (!isset($options['enable_box_shadow'])) ? "on" : $options['enable_box_shadow'];
        
        if(isset($options['box_shadow_color']) && $options['box_shadow_color'] != ''){
            $box_shadow_color = $options['box_shadow_color'];
        }else{
            $box_shadow_color = "#333";
        }
        
        // Quiz container border color
        
        if(isset($options['quiz_border_color']) && $options['quiz_border_color'] != ''){
            $quiz_border_color = $options['quiz_border_color'];
        }else{
            $quiz_border_color = '#000';
        }
                
        
    /*******************************************************************************************************/ 
        
        /*
         * Quiz styles
         *
         * Quiz container styles
         */
        
        
        // Quiz container minimal height
        
        if(isset($options['height']) && $options['height'] != ''){
            $quiz_height = $options['height'];
        }else{
            $quiz_height = '400';
        }
        
        // Quiz container width
        
        if(isset($options['width']) && $options['width'] != ''){
            $quiz_width = $options['width'] . 'px';
        }else{
            $quiz_width = '100%';
        }
                
        // Quiz container max-width for mobile
        if(isset($options['mobile_max_width']) && $options['mobile_max_width'] != ''){
            $mobile_max_width = $options['mobile_max_width'] . '%';
        }else{
            $mobile_max_width = '100%';
        }
        
        // Quiz container border radius
        
        // Modified border radius for Pass count option and Rate avg option
        $quiz_modified_border_radius = "";
        
        if(isset($options['quiz_border_radius']) && $options['quiz_border_radius'] != ''){
            $quiz_border_radius = $options['quiz_border_radius'];
        }else{
            $quiz_border_radius = '3px';
        }
        
        // Quiz container shadow enabled/disabled
        
        if(isset($options['enable_box_shadow']) && $options['enable_box_shadow'] == "on"){
            $enable_box_shadow = true;
        }else{
            $enable_box_shadow = false;
        }
        
        // Quiz container background image
        
        if(isset($options['quiz_bg_image']) && $options['quiz_bg_image'] != ''){
            $ays_quiz_bg_image = $options['quiz_bg_image'];
        }else{
            $ays_quiz_bg_image = null;
        }
        
        // Quiz container background image position
        $quiz_bg_image_position = "center center";

        if(isset($options['quiz_bg_image_position']) && $options['quiz_bg_image_position'] != ""){
            $quiz_bg_image_position = $options['quiz_bg_image_position'];
        }
        
        /*
         * Quiz container border enabled/disabled
         *
         * Quiz container border width
         *
         * Quiz container border style
         */
        
        if(isset($options['enable_border']) && $options['enable_border'] == "on"){
            $enable_border = true;
        }else{
            $enable_border = false;
        }
        
        if(isset($options['quiz_border_width']) && $options['quiz_border_width'] != ''){
            $quiz_border_width = $options['quiz_border_width'];
        }else{
            $quiz_border_width = '1';
        }
        
        if(isset($options['quiz_border_style']) && $options['quiz_border_style'] != ''){
            $quiz_border_style = $options['quiz_border_style'];
        }else{
            $quiz_border_style = 'solid';
        }
        
        // Questions image width and height
        
        if(isset($options['image_width']) && $options['image_width'] != '' && intval($options['height']) !== 0){
            $question_image_width = $options['image_width'] . 'px';
        }else{
            $question_image_width = "100%";
        }
        
        if(isset($options['image_height']) && $options['image_height'] != '' && intval($options['height']) !== 0){
            $question_image_height = $options['image_height'] . 'px';
        }else{
            $question_image_height = "auto";
        }
        
        if(isset($options['image_sizing']) && $options['image_sizing'] != ''){
            $question_image_sizing = $options['image_sizing'];
        }else{
            $question_image_sizing = "cover";
        }

        
        /* 
         * Quiz container background gradient
         * 
         */
        
        // Checking exists background gradient option
        $options['enable_background_gradient'] = (!isset($options['enable_background_gradient'])) ? "off" : $options['enable_background_gradient'];
        
        if(isset($options['background_gradient_color_1']) && $options['background_gradient_color_1'] != ''){
            $background_gradient_color_1 = $options['background_gradient_color_1'];
        }else{
            $background_gradient_color_1 = "#000";
        }

        if(isset($options['background_gradient_color_2']) && $options['background_gradient_color_2'] != ''){
            $background_gradient_color_2 = $options['background_gradient_color_2'];
        }else{
            $background_gradient_color_2 = "#fff";
        }

        if(isset($options['quiz_gradient_direction']) && $options['quiz_gradient_direction'] != ''){
            $quiz_gradient_direction = $options['quiz_gradient_direction'];
        }else{
            $quiz_gradient_direction = 'vertical';
        }
        switch($quiz_gradient_direction) {
            case "horizontal":
                $quiz_gradient_direction = "to right";
                break;
            case "diagonal_left_to_right":
                $quiz_gradient_direction = "to bottom right";
                break;
            case "diagonal_right_to_left":
                $quiz_gradient_direction = "to bottom left";
                break;
            default:
                $quiz_gradient_direction = "to bottom";
        }

        // Quiz container background gradient enabled/disabled        
        if(isset($options['enable_background_gradient']) && $options['enable_background_gradient'] == "on"){
            $enable_background_gradient = true;
        }else{
            $enable_background_gradient = false;
        }

        /*
        ==========================================
            Answers styles
        ==========================================
        */

        // Answers view
        $answer_view_class = "list";
        if(isset($options['answers_view']) && $options['answers_view'] != ''){
            $answer_view_class = $options['answers_view'];
        }

        // Answers font size
        $answers_font_size = '15';
        if(isset($options['answers_font_size']) && $options['answers_font_size'] != ""){
            $answers_font_size = $options['answers_font_size'];
        }

        // Answers padding option
        $answers_padding = '10';
        if(isset($options['answers_padding']) && $options['answers_padding'] != ''){
            $answers_padding = $options['answers_padding'];
        }

        // Answers margin option
        $answers_margin = '10';
        if(isset($options['answers_margin']) && $options['answers_margin'] != ''){
            $answers_margin = $options['answers_margin'];
        }

        // Answers border options
        $options['answers_border'] = (isset($options['answers_border'])) ? $options['answers_border'] : 'on';
        $answers_border = false;
        if(isset($options['answers_border']) && $options['answers_border'] == 'on'){
            $answers_border = true;
        }
        $answers_border_width = '1';
        if(isset($options['answers_border_width']) && $options['answers_border_width'] != ''){
            $answers_border_width = $options['answers_border_width'];
        }
        $answers_border_style = 'solid';
        if(isset($options['answers_border_style']) && $options['answers_border_style'] != ''){
            $answers_border_style = $options['answers_border_style'];
        }
        $answers_border_color = '#444';
        if(isset($options['answers_border_color']) && $options['answers_border_color'] != ''){
            $answers_border_color = $options['answers_border_color'];
        }

        // Answers image options
        // Show answers caption
        $show_answers_caption = false;
        $options['show_answers_caption'] = isset($options['show_answers_caption']) ? $options['show_answers_caption'] : 'on';
        if(isset($options['show_answers_caption']) && $options['show_answers_caption'] == 'on'){
            $show_answers_caption = true;
        }

        $ans_img_height = '15em';
        if(isset($options['ans_img_height']) && $options['ans_img_height'] != ''){
            $ans_img_height = $options['ans_img_height']."px";
        }

        $ans_img_caption_position = 'bottom';
        if(isset($options['ans_img_caption_position']) && $options['ans_img_caption_position'] != ''){
            $ans_img_caption_position = $options['ans_img_caption_position'];
        }

        $ans_image_caption_style = 'outside';
        $ans_img_caption_style = "column-reverse";
        if(isset($options['ans_img_caption_style']) && $options['ans_img_caption_style'] != ''){
            $ans_image_caption_style = $options['ans_img_caption_style'];
        }
        $ans_image_caption_position = $ans_img_caption_position;

        if($answer_view_class == 'list'){
            if($ans_image_caption_style == 'outside'){
                $ans_img_caption_position = "position:initial;".$ans_img_caption_position.":0;";
            }elseif($ans_image_caption_style == 'inside'){
                $ans_img_caption_position = "position:absolute;".$ans_img_caption_position.":0;";
            }
            if($ans_image_caption_position == 'top'){
                $ans_img_caption_style = "row";
            }elseif($ans_image_caption_position == 'bottom'){
                $ans_img_caption_style = "row-reverse";
            }
        }elseif($answer_view_class == 'grid'){
            if($ans_image_caption_style == 'outside'){
                $ans_img_caption_position = "position:initial;".$ans_img_caption_position.":0;";
            }elseif($ans_image_caption_style == 'inside'){
                $ans_img_caption_position = "position:absolute;".$ans_img_caption_position.":0;";
            }
            if($ans_image_caption_position == 'top'){
                $ans_img_caption_style = "column";
            }elseif($ans_image_caption_position == 'bottom'){
                $ans_img_caption_style = "column-reverse";
            }
        }

        
        // Answers box shadow
        $answers_box_shadow = false;
        $answers_box_shadow_color = '#000';
        if(isset($options['answers_box_shadow']) && $options['answers_box_shadow'] == 'on'){
            $answers_box_shadow = true;
        }
        if(isset($options['answers_box_shadow_color']) && $options['answers_box_shadow_color'] != ''){
            $answers_box_shadow_color = $options['answers_box_shadow_color'];
        }

        // Answers right/wrong icons
        $ans_right_wrong_icon = 'default';
        if(isset($options['ans_right_wrong_icon']) && $options['ans_right_wrong_icon'] != ''){
            $ans_right_wrong_icon = $options['ans_right_wrong_icon'];
        }


    /*******************************************************************************************************/
        
        /*
         * Quiz start page
         *
         * Quiz title
         * Quiz desctiption
         * Quiz image
         *
         * Quiz Start button
         */
        
        $title = do_shortcode(stripslashes($quiz['title']));
        
        $description = do_shortcode(stripslashes(wpautop($quiz['description'])));
                
        $quiz_image = $quiz['quiz_image'];
        
        
        $quiz_rate_reports = '';
        $quiz_result_reports = '';
        
        
        if($questions_count == 0){
            $empty_questions_notification = '<p id="ays_no_questions_message" style="color:red">' . __('You need to add questions', $this->plugin_name) . '</p>';
            $empty_questions_button = "disabled";
        }else{
            $empty_questions_notification = "";
            $empty_questions_button = "";
        }
        
        $quiz_start_button = "<input type='button' $empty_questions_button name='next' class='ays_next start_button action-button' value='".__('Start',$this->plugin_name)."' />";
        
        
        /*
         * Show quiz head information
         * Show quiz title and description
         */
        
        $options['show_quiz_title'] = isset($options['show_quiz_title']) ? $options['show_quiz_title'] : 'on';
        $options['show_quiz_desc'] = isset($options['show_quiz_desc']) ? $options['show_quiz_desc'] : 'on';
        $show_quiz_title = (isset($options['show_quiz_title']) && $options['show_quiz_title'] == "on") ? true : false;
        $show_quiz_desc = (isset($options['show_quiz_desc']) && $options['show_quiz_desc'] == "on") ? true : false;

        
        /* 
         * Quiz passed users count
         *
         * Generate HTML code
         */
        
        if(isset($options['enable_pass_count']) && $options['enable_pass_count'] == "on"){
            $enable_pass_count = true;
            $quiz_result_reports = $this->get_quiz_results_count_by_id($id);
            $quiz_result_reports = "<span class='ays_quizn_ancnoxneri_qanak'><i class='ays_fa ays_fa_users'></i> ".$quiz_result_reports['res_count']."</span>";
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px " . $quiz_border_radius . "px;";
        }else{
            $enable_pass_count = false;
        }
        
        
        
        /* 
         * Quiz average rate
         *
         * Generate HTML code
         */
        
        $quiz_rates_avg = round($this->ays_get_average_of_rates($id), 1);
        $quiz_rates_count = $this->ays_get_count_of_rates($id);
        if(isset($options['enable_rate_avg']) && $options['enable_rate_avg'] == "on"){
            $enable_rate_avg = true;
            $quiz_rate_reports = "<div class='ays_quiz_rete_avg'>
                <div class='for_quiz_rate_avg ui star rating' data-rating='".round($quiz_rates_avg)."' data-max-rating='5'></div>
                <span>$quiz_rates_count " . __( "votes", $this->plugin_name ) . ", $quiz_rates_avg " . __( "avg", $this->plugin_name ) . "</span>
            </div>";
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px;";
        }else{
            $enable_rate_avg = false;
        }
        
        
        
        /* 
         * Generate HTML code when passed users count and average rate both are enabled
         */
        
        if($enable_rate_avg && $enable_pass_count){
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px 0px;";
            $ays_quiz_reports = "<div class='ays_quiz_reports'>$quiz_rate_reports $quiz_result_reports</div>";
        }else{
            $ays_quiz_reports = $quiz_rate_reports.$quiz_result_reports;
        }
        
        
        /* 
         * Generate HTML code when passed users count and average rate both are enabled
         * 
         * Show quiz author and create date
         */
        
        // Show quiz category
        if(isset($options['show_category']) && $options['show_category'] == "on"){
            $show_category = true;
        }else{
            $show_category = false;
        }
        
        // Show question category
        if(isset($options['show_question_category']) && $options['show_question_category'] == "on"){
            $show_question_category = true;
        }else{
            $show_question_category = false;
        }

        if(isset($options['show_create_date']) && $options['show_create_date'] == "on"){
            $show_create_date = true;
        }else{
            $show_create_date = false;
        }
        
        if(isset($options['show_author']) && $options['show_author'] == "on"){
            $show_author = true;
        }else{
            $show_author = false;
        }
        
        $show_cd_and_author = "<div class='ays_cb_and_a'>";
        if($show_create_date){
            $quiz_create_date = (isset($options['create_date']) && $options['create_date'] != '') ? $options['create_date'] : "0000-00-00 00:00:00";
            if(Quiz_Maker_Admin::validateDate($quiz_create_date)){
                $show_cd_and_author .= "<span>".__("Created on",$this->plugin_name)." </span><strong><time>".date("F d, Y", strtotime($quiz_create_date))."</time></strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        if($show_author){
            if(isset($options['author'])){
                if(is_array($options['author'])){
                    $author = $options['author'];
                }else{
                    $author = json_decode($options['author'], true);
                }
            }else{
                $author = array("name"=>"Unknown");
            }
            $user_id = 0;
            if(isset($author['id']) && intval($author['id']) != 0){
                $user_id = intval($author['id']);
            }
            $image = get_avatar($user_id, 32);
            if($author['name'] !== "Unknown"){
                if($show_create_date){
                    $text = __("By", $this->plugin_name);
                }else{
                    $text = __("Created by", $this->plugin_name);
                }
                $show_cd_and_author .= "<span>   ".$text." </span>".$image."<strong>".$author['name']."</strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        if($show_category){
            $category_id = isset($quiz['quiz_category_id']) ? intval($quiz['quiz_category_id']) : null;
            if($category_id !== null){
                $quiz_category = $this->get_quiz_category_by_id($category_id);
                $show_cd_and_author .= "<p style='margin:0!important;'><strong>".$quiz_category['title']."</strong></p>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        $show_cd_and_author .= "</div>";
        
        if($show_create_date == false && $show_author == false && $show_category == false){
            $show_cd_and_author = "";
        }
        
    /*******************************************************************************************************/
        
        /* 
         * Quiz passing options
         *
         * Generate HTML code
         */
        
        $live_progress_bar = "";
        $timer_row = "";
        $correction_class = "";
        $ie_container_css = "";
        $rtl_style = "";
            
        
        /*
         * Generating Quiz timer
         *
         * Checking timer enabled or diabled
         */
        
        $timer_enabled = false;
        if (isset($options['enable_timer']) && $options['enable_timer'] == 'on') {
            $timer_enabled = true;
            $timer_text = (isset($options['timer_text'])) ? $options['timer_text'] : '';
            $timer_text = stripslashes(str_replace('%%time%%', $this->secondsToWords($options['timer']), wpautop($timer_text)));
            $hide_timer_cont = "";
            if($timer_text == ""){
                $hide_timer_cont = " style='display:none;' ";
            }
            $timer_row = "<section {$hide_timer_cont} class='ays_quiz_timer_container'><div class='ays-quiz-timer' data-timer='" . $options['timer'] . "'>{$timer_text}</div><hr></section>";
        }
        
        
        /*
         * Quiz live progress bar
         *
         * Checking enabled or diabled
         *
         * Checking percent view or not
         */
        
        if(isset($options['enable_live_progress_bar']) && $options['enable_live_progress_bar'] == "on"){
            
            if(isset($options['enable_percent_view']) && $options['enable_percent_view'] == "on"){
                $live_progress_bar_percent = "<span class='ays-live-bar-percent'>0</span>%";
            }else{
                $live_progress_bar_percent = "<span class='ays-live-bar-percent ays-live-bar-count'></span>/$questions_count";
            }
            
            $live_progress_bar = "<div class='ays-live-bar-wrap'><div class='ays-live-bar-fill' style='width: 0%;'><span>$live_progress_bar_percent</span></div></div>";            
        }
        

        /*
         * Get site url for social sharing buttons
         *
         * Generate HTML class for answers view
         */
        
        $actual_link = "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on"){
            $actual_link = "https" . $actual_link;
        }else{
            $actual_link = "http" . $actual_link;
        }
        
        /*
         * Show correct answers
         *
         * Generate HTML class for answers view
         */
        
        if($enable_correction){
            $correction_class = "enable_correction";
        }
              
        
        /*
         * Show correct answers
         *
         * Generate HTML class for answers view
         */
        
        if(isset($options['enable_questions_counter']) && $options['enable_questions_counter'] == "on"){
            $questions_counter = true;
        }else{
            $questions_counter = false;
        }
           
        
        /*
         * Get Browser data for Internet Explorer
         */
        
        $useragent = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
        if(preg_match('~MSIE|Internet Explorer~i', $useragent) || 
           (strpos($useragent, 'Trident/7.0; rv:11.0') !== false)){
            $ie_container_css = 'display:flex;flex-wrap:wrap;';
        }
        
        
        /*
         * Quiz questions per page count
         */        
        if(isset($options['question_count_per_page']) && $options['question_count_per_page'] == "on"){
            $question_per_page = true;
        }else{
            $question_per_page = false;
        }
        
        if(array_key_exists('question_count_per_page_number', $options)){
            $question_count_per_page = intval($options['question_count_per_page_number']);
        }else{
            $question_count_per_page = 0;
        }
        
        
        /*
         * Quiz buttons
         * 
         * Next button
         * Previous button
         * Arrows instead buttons
         */
        if(isset($options['enable_previous_button']) && $options['enable_previous_button'] == "on"){
            $prev_button = true;
        }else{
            $prev_button = false;
        }
        
        if(isset($options['enable_next_button']) && $options['enable_next_button'] == "on"){
            $next_button = true;
        }else{
            $next_button = false;
        }
        
        if(isset($options['enable_arrows']) && $options['enable_arrows'] == "on"){
            $enable_arrows = true;
        }else{
            $enable_arrows = false;
        }
        
        if(isset($options['enable_early_finish']) && $options['enable_early_finish'] == 'on'){
            $enable_early_finish = true;
        }else{
            $enable_early_finish = false;
        }
        
        if($enable_arrows){
            $arrows_visibility = "";
        }else{
            $arrows_visibility = 'ays_display_none';
        }
        if($prev_button && $enable_arrows){
            $prev_arrow_visibility = "";
        }else{
            $prev_arrow_visibility = 'ays_display_none';
        }
        if($prev_button && !$enable_arrows){
            $prev_button_visibility = "";
        }else{
            $prev_button_visibility = 'ays_display_none';
        }
        if($next_button && $enable_arrows){
            $next_arrow_visibility = "";
        }else{
            $next_arrow_visibility = 'ays_display_none';
        }
        if($next_button == true && $enable_arrows == false){
            $next_button_visibility = "";
        }else{
            $next_button_visibility = 'ays_display_none';
        }
        
        /*
         * Quiz restart button
         */
        $enable_restart_button = false;
        if(isset($options['enable_restart_button']) && $options['enable_restart_button'] == 'on'){
            $enable_restart_button = true;
        }

        if($enable_restart_button){
            $restart_button = "<button type='button' class='action-button ays_restart_button'>
                    <i class='ays_fa ays_fa_undo'></i>
                    <span>".__( "Restart", $this->plugin_name )."</span>
                </button>";
        }else{
            $restart_button = "";
        }

        
        /*
         * EXIT button in finish page
         */

        $enable_exit_button = false;
        $exit_redirect_url = null;
        if(isset($options['enable_exit_button']) && $options['enable_exit_button'] == 'on'){
            $enable_exit_button = true;
        }
        if(isset($options['exit_redirect_url']) && $options['exit_redirect_url'] != ''){
            $exit_redirect_url = $options['exit_redirect_url'];
        }


        if($enable_exit_button && $exit_redirect_url !== null){
            $exit_button = "<a style='width:auto;' href='".$exit_redirect_url."' class='action-button ays_restart_button' target='_top'>
                        <span>".__( "Exit", $this->plugin_name )."</span>
                        <i class='ays_fa ays_fa_sign_out'></i>
                    </a>";
        }else{
            $exit_button = "";
        }
        
        /*
         * Clear answer button
         */
        $enable_clear_answer = false;
        if(isset($options['enable_clear_answer']) && $options['enable_clear_answer'] == 'on'){
            $enable_clear_answer = true;
        }
        
        $buttons = array(
            "enableArrows" => $enable_arrows,
            "arrows" => $arrows_visibility,
            "nextArrow" => $next_arrow_visibility,
            "prevArrow" => $prev_arrow_visibility,
            "nextButton" => $next_button_visibility,
            "prevButton" => $prev_button_visibility,
            "earlyButton" => $enable_early_finish,
            "clearAnswerButton" => $enable_clear_answer,
        );
        
        /*
         * Quiz RTL direction
         */
        
        if(isset($options['enable_rtl_direction']) && $options['enable_rtl_direction'] == "on"){
            $rtl_direction = true;
            $rtl_style = "
                #ays-quiz-container-" . $id . " p {
                    direction:rtl;
                    text-align:right;   
                }
                #ays-quiz-container-" . $id . " p.ays_score {
                    text-align: center;   
                }
                #ays-quiz-container-" . $id . " p.ays-question-counter {
                    right: unset;
                    left: 8px;
                }
                #ays-quiz-container-" . $id . " .ays_question_hint {
                    left:unset;
                    right:10px;
                }
                #ays-quiz-container-" . $id . " .ays_question_hint_text {
                    left:unset;
                    right:20px;
                }
                #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option {
                    direction:rtl;
                    text-align:right;
                }
                #ays-quiz-container-" . $id . " .select2-container--default .select2-selection--single .select2-selection__placeholder,
                #ays-quiz-container-" . $id . " .select2-container--default .select2-selection--single .select2-selection__rendered {
                    direction:rtl;
                    text-align:right;
                    display: inline-block;
                    width: 95%;
                }
                #ays-quiz-container-" . $id . " .ays-field.ays-select-field {
                    margin: 0;
                }

                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]{
                    direction:rtl;
                    text-align:right;
                    padding-left: 0px;
                    padding-right: 10px;
                    position: relative;
                    text-overflow: ellipsis;
                }                        
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]:last-child {
                    padding-right: 0;
                }
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]::before {
                    margin-left: 5px;
                    margin-right: 5px;
                }
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]::after {
                    margin-left: 0px;
                    margin-right: 10px;
                }
                ";
        }else{
            $rtl_direction = false;
        }
        
        
        
        /*
         * Quiz background music 
         */
        
        $enable_bg_music = false;
        $quiz_bg_music = "";
        $ays_quiz_music_html = "";
        $ays_quiz_music_sound = "";
        
        if(isset($options['enable_bg_music']) && $options['enable_bg_music'] == "on"){
            $enable_bg_music = true;
        }
        
        if(isset($options['quiz_bg_music']) && $options['quiz_bg_music'] != ""){
            $quiz_bg_music = $options['quiz_bg_music'];
        }        

        if($enable_bg_music && $quiz_bg_music != ""){
            $ays_quiz_music_html = "<audio id='ays_quiz_music_".$id."' loop class='ays_quiz_music' src='".$quiz_bg_music."'></audio>";
            $with_timer = "";
            if($timer_enabled){
                $with_timer = " ays_sound_with_timer ";
            }
            $ays_quiz_music_sound = "<span class='ays_music_sound ".$with_timer." ays_sound_active ays_display_none'><i class='ays_fa ays_fa_volume_up'></i></span>";
        }
        

        /*
         * Quiz Right / Wrong answers sounds
         */

        $enable_rw_asnwers_sounds = false;
        $rw_answers_sounds_status = false;
        $right_answer_sound_status = false;
        $wrong_answer_sound_status = false;
        $right_answer_sound = "";
        $wrong_answer_sound = "";
        $rw_asnwers_sounds_html = "";


        if(isset($settings_options['right_answer_sound']) && $settings_options['right_answer_sound'] != ''){
            $right_answer_sound_status = true;
            $right_answer_sound = $settings_options['right_answer_sound'];
        }

        if(isset($settings_options['wrong_answer_sound']) && $settings_options['wrong_answer_sound'] != ''){
            $wrong_answer_sound_status = true;
            $wrong_answer_sound = $settings_options['wrong_answer_sound'];
        }

        if($right_answer_sound_status && $wrong_answer_sound_status){
            $rw_answers_sounds_status = true;
        }

        if(isset($options['enable_rw_asnwers_sounds']) && $options['enable_rw_asnwers_sounds'] == "on"){
            if($rw_answers_sounds_status){
                $enable_rw_asnwers_sounds = true;
            }
        }

        if($enable_rw_asnwers_sounds){
            $rw_asnwers_sounds_html = "<audio id='ays_quiz_right_ans_sound_".$id."' class='ays_quiz_right_ans_sound' src='".$right_answer_sound."'></audio>";
            $rw_asnwers_sounds_html .= "<audio id='ays_quiz_wrong_ans_sound_".$id."' class='ays_quiz_wrong_ans_sound' src='".$wrong_answer_sound."'></audio>";
        }

    /*******************************************************************************************************/
        
        /* 
         * Quiz finish page
         *
         * Generating some HTML code for finish page
         */
        
        $progress_bar = false;
        $progress_bar_style = "first";
        $progress_bar_html = "";
        $show_average = "";
        $show_score_html = "";
        $enable_questions_result = "";
        $rate_form_title = "";
        $quiz_rate_html = "";
        $ays_social_buttons = "";
        
        /*
         * Quiz progress bar for finish page
         *
         * Checking enabled or diabled
         */
        
        if(isset($options['enable_progress_bar']) && $options['enable_progress_bar'] == 'on'){
            $progress_bar = true;
        }

        if(isset($options['progress_bar_style']) && $options['progress_bar_style'] != ""){
            $progress_bar_style = $options['progress_bar_style'];
        }

        if($progress_bar){
            $progress_bar_html = "<div class='ays-progress " . $progress_bar_style . "'>
                <span class='ays-progress-value " . $progress_bar_style . "'>0%</span>
                <div class='ays-progress-bg " . $progress_bar_style . "'>
                    <div class='ays-progress-bar " . $progress_bar_style . "' style='width:0%;'></div>
                </div>
            </div>";
        } 
        
        
        /*
         * Average statistical of quiz
         *
         * Checking enabled or diabled
         */
        if (isset($options['enable_average_statistical']) && $options['enable_average_statistical'] == "on") {
            $result = $this->ays_get_average_of_scores($id);
            $show_average = "<p class='ays_average'>" . __('The average score is', $this->plugin_name) . " " . $result . "%</p>";
        }
        
        
        /*
         * Passed quiz score
         *
         * Checking enabled or diabled
         */
                
        if(array_key_exists('hide_score',$options) && $options['hide_score'] != "on"){
            $show_score_html = "<p class='ays_score ays_score_display_none animated'>" . __( 'Your score is ', $this->plugin_name ) . "</p>";
        }
        
        /*
         * Show quiz results after passing quiz
         *
         * Checking enabled or diabled
         */
              
        if(isset($options['enable_questions_result']) && $options['enable_questions_result'] == "on"){
            $enable_questions_result = 'enable_questions_result';
        }
        
        
//        if($calculate_score == 'by_points'){
//            $enable_questions_result = '';
//        }
        
        /*
         * Quiz rate
         *
         * Generating HTML code
         */
        
        if(isset($options['rate_form_title'])){
            $rate_form_title = stripslashes(wpautop($options['rate_form_title']));
        }
        
        if(isset($options['enable_quiz_rate']) && $options['enable_quiz_rate'] == "on"){
            $quiz_rate_html = "<div class='ays_quiz_rete'>
                <div>$rate_form_title</div>
                <div class='for_quiz_rate ui huge star rating' data-rating='0' data-max-rating='5'></div>
                <div><div class='lds-spinner-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                <div class='for_quiz_rate_reason'>
                    <textarea id='quiz_rate_reason_".$id."' class='quiz_rate_reason'></textarea>
                    <div class='ays_feedback_button_div'>
                        <button type='button' class='action-button'>". __('Send feedback', $this->plugin_name) ."</button>
                    </div>
                </div>
                <div><div class='lds-spinner2-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                <div class='quiz_rate_reasons_body'></div>
            </div>";
        }
        
        
        
        /*
         * Quiz social sharing buttons
         *
         * Generating HTML code
         */
        
        
        if(isset($options['enable_social_buttons']) && $options['enable_social_buttons'] == "on"){
              $ays_social_buttons = "<div class='ays-quiz-social-shares'>
                        <!-- Branded LinkedIn button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-linkedin'
                           href='https://www.linkedin.com/shareArticle?mini=true&url=" . $actual_link . "'
                           title='Share on LinkedIn'>
                            <span class='ays-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>LinkedIn</span>
                        </a>
                        <!-- Branded Facebook button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-facebook'
                           href='https://www.facebook.com/sharer/sharer.php?u=" . $actual_link . "'
                           title='Share on Facebook'>
                            <span class='ays-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>Facebook</span>
                        </a>
                        <!-- Branded Twitter button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-twitter'
                           href='https://twitter.com/share?url=" . $actual_link . "'
                           title='Share on Twitter'>
                            <span class='ays-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>Twitter</span>
                        </a>
                    </div>";
        }
        
        
        /*
         * Quiz social media links
         *
         * Generating HTML code
         */
        // Social Media links

        $enable_social_links = (isset($options['enable_social_links']) && $options['enable_social_links'] == "on") ? true : false;
        $social_links = (isset($options['social_links'])) ? $options['social_links'] : array(
            'linkedin_link' => '',
            'facebook_link' => '',
            'twitter_link' => ''
        );
        $ays_social_links_array = array();

        $linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? $social_links['linkedin_link'] : '';
        $facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? $social_links['facebook_link'] : '';
        $twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? $social_links['twitter_link'] : '';
        if($linkedin_link != ''){
            $ays_social_links_array['Linkedin'] = $linkedin_link;
        }
        if($facebook_link != ''){
            $ays_social_links_array['Facebook'] = $facebook_link;
        }
        if($twitter_link != ''){
            $ays_social_links_array['Twitter'] = $twitter_link;
        }
        $ays_social_links = '';
        
        if($enable_social_links){
            $ays_social_links .= "<div class='ays-quiz-social-shares'>";
            foreach($ays_social_links_array as $media => $link){
                $ays_social_links .= "<!-- Branded " . $media . " button -->
                    <a class='ays-share-btn ays-share-btn-branded ays-share-btn-rounded ays-share-btn-" . strtolower($media) . "'
                        href='" . $link . "'
                        target='_blank'
                        title='" . $media . " link'>
                        <span class='ays-share-btn-icon'></span>
                    </a>";
            }
                    
                    // "<!-- Branded Facebook button -->
                    // <a class='ays-share-btn ays-share-btn-branded ays-share-btn-facebook'
                    //     href='" . . "'
                    //     title='Share on Facebook'>
                    //     <span class='ays-share-btn-icon'></span>
                    // </a>
                    // <!-- Branded Twitter button -->
                    // <a class='ays-share-btn ays-share-btn-branded ays-share-btn-twitter'
                    //     href='" . . "'
                    //     title='Share on Twitter'>
                    //     <span class='ays-share-btn-icon'></span>
                    // </a>";
            $ays_social_links .= "</div>";
        }
        
        
        /*
         * Quiz loader
         *
         * Generating HTML code
         */
                
        $quiz_loader = 'default';
        
        if(isset($options['quiz_loader']) && $options['quiz_loader'] != ''){
            $quiz_loader = $options['quiz_loader'];
        }
        
        switch($quiz_loader){
            case 'default':
                $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                break;
            case 'circle':
                $quiz_loader_html = "<div data-class='lds-circle' data-role='loader' class='ays-loader'></div>";
                break;
            case 'dual_ring':
                $quiz_loader_html = "<div data-class='lds-dual-ring' data-role='loader' class='ays-loader'></div>";
                break;
            case 'facebook':
                $quiz_loader_html = "<div data-class='lds-facebook' data-role='loader' class='ays-loader'><div></div><div></div><div></div></div>";
                break;
            case 'hourglass':
                $quiz_loader_html = "<div data-class='lds-hourglass' data-role='loader' class='ays-loader'></div>";
                break;
            case 'ripple':
                $quiz_loader_html = "<div data-class='lds-ripple' data-role='loader' class='ays-loader'><div></div><div></div></div>";
                break;
            default:
                $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                break;
        }
        
        
        
    /*******************************************************************************************************/
        
        /*
         * Quiz limitations
         *
         * Blocking content
         *
         * Generating HTML code
         */
        
        $limit_users_html = "";        
        $limit_users = null;
        
        /*
         * Quiz timer in tab title
         */
        
        if(isset($options['quiz_timer_in_title']) && $options['quiz_timer_in_title'] == "on"){
            $show_timer_in_title = "true";
        }else{
            $show_timer_in_title = "false";
        }
        
        /*
         * Quiz one time passing
         *
         * Generating HTML code
         */        
        
        // Limit users by option
        $limit_users_by = 'ip';

        if(isset($options['limit_users_by']) && $options['limit_users_by'] != ''){
            $limit_users_by = $options['limit_users_by'];
        }

        
        if (isset($options['limit_users']) && $options['limit_users'] == "on") {
            if($limit_users_by == 'ip'){
                $result = $this->get_user_by_ip($id);
            }elseif($limit_users_by == 'user_id'){
                if(is_user_logged_in()){
                    $user_id = get_current_user_id();
                    $result = $this->get_limit_user_by_id($id, $user_id);
                }else{
                    $result = 0;
                }
            }else{
                $result = 0;
            }
            if ($result != 0) {
                $limit_users = true;
                $timer_row = "";
                if(isset($options['redirection_delay']) && $options['redirection_delay'] != ''){
                    if(isset($options['redirect_url']) && $options['redirect_url'] != ''){
                        $timer_row = "<p class='ays_redirect_url' style='display:none'>" . 
                                $options['redirect_url'] . 
                            "</p>                                
                            <div class='ays-quiz-timer' data-show-in-title='".$show_timer_in_title."' data-timer='" . $options['redirection_delay'] . "'>". 
                                __( "Redirecting after", $this->plugin_name ). " " . 
                                $this->secondsToWords($options['redirection_delay']) . 
                                "<EXTERNAL_FRAGMENT></EXTERNAL_FRAGMENT>                                
                            </div>";
                    }
                }
                $limit_message = do_shortcode(stripslashes(wpautop($options['limitation_message'])));
                
                if($limit_message == ''){
                    $limit_message = __('You already passed this quiz.', $this->plugin_name);
                }
                
                $limit_users_html = $timer_row . "<div style='color:" . $text_color . ";min-height:".($quiz_height/2)."px;' class='ays_block_content'>" . $limit_message . "</div><style>form{min-height:0 !important;}</style>";
            }
        }else{
            $limit_users = false;
        }
                
        
        /*
         * Quiz only for logged in users
         *
         * Generating HTML code
         */
        
        // Show login form for not logged in users
        $options['show_login_form'] = isset($options['show_login_form']) ? $options['show_login_form'] : 'off';
        $show_login_form = (isset($options['show_login_form']) && $options['show_login_form'] == "on") ? true : false;
        $quiz_login_form = "";
        if($show_login_form){
            $args = array(
                'echo' => false,
                'id_username' => 'ays_user_login',
                'id_password' => 'ays_user_pass',
                'id_remember' => 'ays_rememberme',
                'id_submit' => 'ays-submit',
            );
            $quiz_login_form = "<div class='ays_quiz_login_form'>" . wp_login_form( $args ) . "</div>";
        }
        
        global $wp_roles;
        
        if(isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on' && !is_user_logged_in()){
            $enable_logged_users = 'only_logged_users';
            if(isset($options['enable_logged_users_message']) && $options['enable_logged_users_message'] != ""){
                $logged_users_message = do_shortcode(stripslashes(wpautop($options['enable_logged_users_message'])));
            }else{
                $logged_users_message =  __('You must log in to pass this quiz.', $this->plugin_name);
            }
            if($logged_users_message !== null){
                $user_massage = '<div class="logged_in_message">' . $logged_users_message . '</div>';
            }else{
                $user_massage = null;
            }
        }else{
            $user_massage = null;
            $enable_logged_users = '';
            if (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') {
                $user = wp_get_current_user();
                $user_roles   = $wp_roles->role_names;
                $message = (isset($options['restriction_pass_message']) && $options['restriction_pass_message'] != '') ? $options['restriction_pass_message'] : __('Permission Denied', $this->plugin_name);
                $user_role = (isset($options['user_role']) && $options['user_role'] != '') ? $options['user_role'] : '';
                $user_massage = '<div class="logged_in_message">' . do_shortcode(stripslashes(wpautop($message))) . '</div>';
                
                if (is_array($user_role)) {
                    foreach($user_role as $key => $role){
                        if(in_array($role, $user_roles)){
                            $user_role[$key] = array_search($role, $user_roles);
                        }                        
                    }
                }else{
                    if(in_array($user_role, $user_roles)){
                        $user_role = array_search($user_role, $user_roles);
                    }
                }

                if(is_array($user_role)){
                    foreach($user_role as $role){                        
                        if (in_array(strtolower($role), (array)$user->roles)) {
                            $user_massage = null;
                            break;
                        }
                    }                    
                }else{
                    if (in_array(strtolower($user_role), (array)$user->roles)) {
                        $user_massage = null;
                    }
                }
            }
        }
        
        if($user_massage !== null){
            if(!is_user_logged_in()){
                $user_massage .= $quiz_login_form;
            }
        }
        
        
        // Limitation tackers of quiz
        $enable_tackers_count = false;
        $tackers_count = 0;
        $tackers_message = "<div style='padding:50px;'><p>" . __( "This quiz is expired!", $this->plugin_name ) . "</p></div>";
        $options['enable_tackers_count'] = !isset($options['enable_tackers_count']) ? 'off' : $options['enable_tackers_count'];
        if(isset($options['enable_tackers_count']) && $options['enable_tackers_count'] == 'on'){
            $enable_tackers_count = true;
        }
        if(isset($options['tackers_count']) && $options['tackers_count'] != ''){
            $tackers_count = intval($options['tackers_count']);
        }
        
    /*******************************************************************************************************/

        
        /*
         * Schedule quiz
         * Check is quiz expired
         */
        
        $is_expired = false;
        $active_date_check = false;
		$expired_quiz_message = "<p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>";
		if (isset($options['active_date_check']) && $options['active_date_check'] == "on") {
            $active_date_check = true;
			if (isset($options['activeInterval']) && isset($options['deactiveInterval'])) {
				$startDate = strtotime($options['activeInterval']);
				$endDate   = strtotime($options['deactiveInterval']);
				if ($startDate > current_time( 'timestamp' ) || $endDate < current_time( 'timestamp' )) {
					$is_expired = true;
                    if(isset($options['active_date_message'])){ 
				        $expired_quiz_message = "<div class='step active-step'>
                            <div class='ays-abs-fs'>
                                " . do_shortcode(stripslashes(wpautop($options['active_date_message']))) . "
                            </div>
                        </div>";
                    }else{
                        $expired_quiz_message = "<div class='ays-abs-fs'>
                            <p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>
                        </div>";
                    }
				}
			}
		}
        
        
    /*******************************************************************************************************/
        
        /*
         * Quiz main content
         *
         * Generating HTML code
         *
         */
        
        
        if($quiz_image != ""){
            $quiz_image = "<img src='{$quiz_image}' alt='' class='ays_quiz_image'>";
        }else{
            $quiz_image = "";
        }
        
        
        if($show_quiz_title){
            $title = "<p class='ays-fs-title'>" . $title . "</p>";
        }else{
            $title = "";
        }

        if($show_quiz_desc){
            $description = "<p class='ays-fs-subtitle'>" . $description . "</p>";
        }else{
            $description = "";
        }
        
        $main_content_first_part = "{$timer_row}
            {$rw_asnwers_sounds_html}
            {$ays_quiz_music_sound}
            <div class='step active-step'>
                <div class='ays-abs-fs ays-start-page'>
                    {$show_cd_and_author}
                    {$quiz_image}
                    {$title}
                    {$description}
                    <input type='hidden' name='ays_quiz_id' value='{$id}'/>
                    <input type='hidden' name='ays_quiz_questions' value='{$quiz_questions_ids}'>
                    <div class='ays_buttons_div'>
                        {$quiz_start_button}
                    </div>
                    {$empty_questions_notification}
                    </div>
                </div>";
        
        if($limit_users === false || $limit_users === null){
            $restart_button_html = $restart_button;
        }else{
            $restart_button_html = "";
        }
        
        $main_content_last_part = "<div class='step ays_thank_you_fs'>
            <div class='ays-abs-fs ays-end-page'>".
            $quiz_loader_html .
            "<div class='ays_quiz_results_page'>
                <div class='ays_message'></div>" .
                $show_score_html .
                $show_average .
                $ays_social_buttons .
                $ays_social_links .
                $progress_bar_html .
                "<p class='ays_restart_button_p'>".
                    $restart_button_html .
                    $exit_button .
                "</p>".
                $quiz_rate_html .
                "</div>
            </div>
        </div>";
        
        if($show_form != null){
            if ($options['information_form'] == "after") {
                $main_content_last_part = "<div class='step'>
                    <div class='ays-abs-fs ays-end-page information_form'>
                    <div class='ays-form-title'>{$form_title}</div>
                        " . $form_inputs . "
                        <div class='ays_buttons_div'>
                            <i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow'></i>
                            <input type='submit' name='ays_finish_quiz' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_finish action-button' value='" . __('See Result', $this->plugin_name) . "'/>
                        </div>
                    </div>
                  </div>" . $main_content_last_part;
                
            } elseif ($options['information_form'] == "before") {
                $main_content_first_part = $main_content_first_part . "<div class='step' data-role='info-form'>
                    <div class='ays-abs-fs ays-start-page information_form'>
                    <div class='ays-form-title'>{$form_title}</div>
                        " . $form_inputs . "
                        <div class='ays_buttons_div'>
                            <i class='ays_fa ays_fa_arrow_right ays_next action-button ays_arrow ays_next_arrow " . ($enable_arrows ? '' : 'ays_display_none') . "'></i>
                            <input type='button' name='next' class='ays_next action-button " . ($enable_arrows ? 'ays_display_none' : '') . "' value='" . __('Next', $this->plugin_name) . "' />
                        </div>
                    </div>
                  </div>" ;

            }
        }else{
            $options['information_form'] = "disable";
        }
        
        
    /*******************************************************************************************************/
        
        /*
         * Script for getting quiz options
         *
         * Script for question type dropdown
         *
         * Generating HTML code
         */
        
        $quiz_content_script = "<script>";
        
        if(isset($options['submit_redirect_delay'])){
            if($options['submit_redirect_delay'] == ''){
                $options['submit_redirect_delay'] = 0;
            }
            $options['submit_redirect_after'] = $this->secondsToWords($options['submit_redirect_delay']);
        }
        
        $options['rw_answers_sounds'] = $enable_rw_asnwers_sounds;

        unset($quiz['options']);
        $quiz_options = $options;
        foreach($quiz as $k => $q){
            $quiz_options[$k] = $q;
        }
        
        foreach($quiz_options as $k => $q){
            if(strpos($k, 'smtp') !== false || strpos($k, 'email') !== false ){
                if($k == 'form_email'){
                    continue;
                }
                unset($quiz_options[$k]);
            }
        }
        
        if ($limit_users) {
            if($limit_users_by == 'ip'){
                $result = $this->get_user_by_ip($id);
            }elseif($limit_users_by == 'user_id'){
                if(is_user_logged_in()){
                    $user_id = get_current_user_id();
                    $result = $this->get_limit_user_by_id($id, $user_id);
                }else{
                    $result = 0;
                }
            }else{
                $result = 0;
            }
            $result = $this->get_user_by_ip($id);
            if ($result == 0) {
                $quiz_content_script .= "
                    if(typeof options === 'undefined'){
                        var options = [];
                    }
                    options['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
            }
        }else{
            $quiz_content_script .= "
                if(typeof options === 'undefined'){
                    var options = [];
                }
                options['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
        }
        $quiz_content_script .= "
        </script>";
        
    /*******************************************************************************************************/
        
        /*
         * Styles for quiz
         *
         * Generating HTML code
         */
                
        
        $quest_animation = 'shake';
        
        if(isset($options['quest_animation']) && $options['quest_animation'] != ''){
            $quest_animation = $options['quest_animation'];
        }
        
        $quiz_styles = "<style>
            div#ays-quiz-container-" . $id . " * {
                box-sizing: border-box;
            }

            /* Styles for Internet Explorer start */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " {
                " . $ie_container_css . "
            }";

        if($ie_container_css != ''){
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays_next.action-button,
                            #ays-quiz-container-" . $id . " .ays_previous.action-button{
                                margin: 10px 5px;
                            }";
        }
                
        $quiz_styles .= "

            /* Styles for Quiz container */
            #ays-quiz-container-" . $id . "{
                min-height: " . $quiz_height . "px;
                width:" . $quiz_width . ";
                background-color:" . $bg_color . ";
                background-position:" . $quiz_bg_image_position . ";";

        if($ays_quiz_bg_image != null){
            $quiz_styles .=  "background-image: url('$ays_quiz_bg_image');";
        } elseif($enable_background_gradient) {
            $quiz_styles .=  "background-image: linear-gradient($quiz_gradient_direction, $background_gradient_color_1, $background_gradient_color_2);";
        }

        if($quiz_modified_border_radius != ""){
            $quiz_styles .= $quiz_modified_border_radius;
        }else{
            $quiz_styles .=  "border-radius:" . $quiz_border_radius . "px;";
        }

        if($enable_box_shadow){
            $quiz_styles .=  "box-shadow: 0 0 15px 1px " . $this->hex2rgba($box_shadow_color, '0.4') . ";";
        }else{
            $quiz_styles .=  "box-shadow: none;";
        }
        if($enable_border){
            $quiz_styles .=  "border-width: " . $quiz_border_width.'px;'.
                           "border-style: " . $quiz_border_style.';'.
                           "border-color: " . $quiz_border_color.';';
        }else{
            $quiz_styles .=  "border: none;";
        }

        $quiz_styles .= "}

            /* Styles for questions */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " div.step {
                min-height: " . $quiz_height . "px;
            }

            /* Styles for text inside quiz container */
            #ays-quiz-container-" . $id . " .ays-start-page *:not(input),
            #ays-quiz-container-" . $id . " .ays_question_hint,
            #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"],
            #ays-quiz-container-" . $id . " p,
            #ays-quiz-container-" . $id . " .ays-fs-title,
            #ays-quiz-container-" . $id . " .ays-fs-subtitle,
            #ays-quiz-container-" . $id . " .logged_in_message,
            #ays-quiz-container-" . $id . " .ays_message{
               color: " . $text_color . ";
               outline: none;
            }
            
            #ays-quiz-container-" . $id . " textarea,
            #ays-quiz-container-" . $id . " input::first-letter,
            #ays-quiz-container-" . $id . " select::first-letter,
            #ays-quiz-container-" . $id . " option::first-letter {
                color: initial !important;
            }
            
            #ays-quiz-container-" . $id . " p::first-letter:not(.ays_no_questions_message) {
                color: " . $text_color . " !important;
                background-color: transparent !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                float: none !important;
                line-height: inherit !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            #ays-quiz-container-" . $id . " .select2-container,
            #ays-quiz-container-" . $id . " .ays-field * {
                font-size: ".$answers_font_size."px !important;
            }
            
            #ays-quiz-container-" . $id . " input[type='button'],
            #ays-quiz-container-" . $id . " input[type='submit'] {
                color: " . $text_color . " !important;
                outline: none;
            }
            #ays-quiz-container-" . $id . " .information_form input[type='text'],
            #ays-quiz-container-" . $id . " .information_form input[type='url'],
            #ays-quiz-container-" . $id . " .information_form input[type='number'],
            #ays-quiz-container-" . $id . " .information_form input[type='email'],
            #ays-quiz-container-" . $id . " .information_form input[type='checkbox'],
            #ays-quiz-container-" . $id . " .information_form input[type='tel'],
            #ays-quiz-container-" . $id . " .information_form textarea,
            #ays-quiz-container-" . $id . " .information_form select,
            #ays-quiz-container-" . $id . " .information_form option {
                color: initial !important;
                outline: none;
            }
            
            #ays-quiz-container-" . $id . " .wrong_answer_text{
                color:#ff4d4d;
            }
            #ays-quiz-container-" . $id . " .right_answer_text{
                color:#33cc33;
            }
            #ays-quiz-container-" . $id . " .ays_cb_and_a,
            #ays-quiz-container-" . $id . " .ays_cb_and_a * {
                color: " . $this->hex2rgba($text_color) . ";
            }



            /* Quiz rate and passed users count */
            #ays-quiz-container-" . $id . " .ays_quizn_ancnoxneri_qanak,
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg{
                color:" . $bg_color . ";
                background-color:" . $text_color . ";                                        
            }
            #ays-quiz-container-" . $id . " div.for_quiz_rate.ui.star.rating .icon {
                color: " . $this->hex2rgba($text_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg div.for_quiz_rate_avg.ui.star.rating .icon {
                color: " . $this->hex2rgba($bg_color, '0.5') . ";
            }

            /* Loaders */
            #ays-quiz-container-" . $id . " div.lds-spinner,
            #ays-quiz-container-" . $id . " div.lds-spinner2 {
                color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " div.lds-spinner div:after,
            #ays-quiz-container-" . $id . " div.lds-spinner2 div:after {
                background-color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-circle,
            #ays-quiz-container-" . $id . " .lds-facebook div,
            #ays-quiz-container-" . $id . " .lds-ellipsis div{
                background: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-ripple div{
                border-color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-dual-ring::after,
            #ays-quiz-container-" . $id . " .lds-hourglass::after{
                border-color: " . $text_color . " transparent " . $text_color . " transparent;
            }


            /* Progress bars */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress {
                border-color: " . $this->hex2rgba($text_color, '0.8') . ";
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress-bg {
                background-color: " . $this->hex2rgba($text_color, '0.3') . ";
            }
            #ays-quiz-container-" . $id . " .ays-progress-value {
                color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .ays-progress-bar {
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .ays-question-counter .ays-live-bar-wrap {
                direction:ltr !important;
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-fill{
                color: " . $text_color . ";
                border-bottom: 2px solid " . $this->hex2rgba($text_color, '0.8') . ";
                text-shadow: 0px 0px 5px " . $bg_color . ";
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-percent{
                display:none;
            }
            
            
            /* Music, Sound */
            #ays-quiz-container-" . $id . " .ays_music_sound {
                color:" . $this->hex2rgba($text_color) . ";
            }

            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " blockquote {
                border-left-color: " . $text_color . " !important;                                      
            }


            /* Question hint */
            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text {
                background-color:" . $bg_color . ";
                box-shadow: 0 0 15px 3px " . $this->hex2rgba($box_shadow_color, '0.6') . ";
            }

            /* Information form */
            #ays-quiz-container-" . $id . " .ays-form-title{
                color:" . $this->hex2rgba($text_color) . ";
            }

            /* Quiz timer */
            #ays-quiz-container-" . $id . " div.ays-quiz-timer{
                color: " . $text_color . ";
            }
            
            /* Quiz buttons */
            #ays-quiz-container-" . $id . " .ays_arrow {
                color:". $text_color ."!important;
            }
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button {
                background-color: " . $color . ";
                color:" . $text_color . ";
            }
            #ays-quiz-container-" . $id . " input#ays-submit:hover,
            #ays-quiz-container-" . $id . " input#ays-submit:focus,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:hover,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:focus {
                box-shadow: 0 0 0 2px $text_color;
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .ays_restart_button {
                color: " . $color . ";
            }

            /* Question answers */
            #ays-quiz-container-".$id." .ays-field {";
        if($answers_border){
            $quiz_styles .= "
                border-color: " . $answers_border_color . ";
                border-style: " . $answers_border_style . ";
                border-width: " . $answers_border_width . "px;";
        }else{
            $quiz_styles .= "
                border-color: transparent;
                border-style: none;
                border-width: 0;";
        }

        if($answers_box_shadow){
            $quiz_styles .= "
                box-shadow: 0px 0px 10px " . $answers_box_shadow_color . ";";
        }else{
            $quiz_styles .= "
                box-shadow: none;";
        }

        $quiz_styles .=
                "flex-direction: ".$ans_img_caption_style.";
            }
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field:hover{
                opacity: 1;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field label.ays_answer_caption[for^='ays-answer-'] {
                z-index: 1;
                " . $ans_img_caption_position;

        if(! $show_answers_caption){
            $quiz_styles .= "display: none !important;";
        }
        $quiz_styles .= "}";

        if($ans_image_caption_style == 'inside'){
            $quiz_styles .= "
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item label.ays_answer_caption[for^='ays-answer-'] {
                background-color: " . $this->hex2rgba($color, '0.6') . ";
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item label.ays_answer_caption[for^='ays-answer-']:hover {
                background-color: " . $this->hex2rgba($color, '1') . ";
            }";
        }
        $quiz_styles .= "
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field input~label[for^='ays-answer-'] {
                padding: " . $answers_padding . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item {
                width: calc(50% - " . ($answers_margin / 2) . "px);
                margin-bottom: " . ($answers_margin) . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item:nth-child(odd) {
                margin-right: " . ($answers_margin / 2) . "px;
            }


            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field input:checked+label:before {
                border-color: " . $color . ";
                background: " . $color . ";
                background-clip: content-box;
            }
            #ays-quiz-container-" . $id . " .ays-quiz-answers div.ays-text-right-answer {
                color: " . $text_color . ";
            }
            
            /* Questions answer image */
            #ays-quiz-container-" . $id . " .ays-answer-image {
                width:" . ($answer_view_class == "grid" ? "100%" : "15em") . ";
                height:" . $ans_img_height . ";
            }
            
            /* Questions answer right/wrong icons */
            ";
        if($ans_right_wrong_icon == 'default'){
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input~label.answered.correct:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/correct.png');          }
            #ays-quiz-container-" . $id . " .ays-field input~label.answered.wrong:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/wrong.png');
            }";
        }else{
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input~label.answered.correct:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/correct-".$ans_right_wrong_icon.".png');
            }
            #ays-quiz-container-" . $id . " .ays-field input~label.answered.wrong:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/wrong-".$ans_right_wrong_icon.".png');
            }";
        }
        if($ans_right_wrong_icon == 'style-9'){
            $quiz_styles .= "
                #ays-quiz-container-" . $id . " .ays-field label.answered::after{
                    height: auto;
                }";
        }
        $quiz_styles .= "
            #ays-quiz-container-" . $id . " .ays-field label.answered:last-of-type:after{
                height: auto;
                left: " . ($answers_padding+5) . "px;";
        if($ans_image_caption_position == 'top'){
            $quiz_styles .= "bottom: " . ($answers_padding+5) . "px;";
        }else{
            $quiz_styles .= "top: " . ($answers_padding+5) . "px;";
        }
        $quiz_styles .= "}";
        $quiz_styles .= "
            /* Dropdown questions */            
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field .select2-container--default .select2-selection--single {
                border-bottom: 2px solid " . $color . ";
            }
            
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow {
                color: " . $text_color . ";
            }

            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: " . $color . ";
            }
            
            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar {
                width: 7px
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-track {
                background-color: " . $this->hex2rgba($bg_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb {
                transition: .3s ease-in-out;
                background-color: " . $this->hex2rgba($bg_color, '0.55') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb:hover {
                transition: .3s ease-in-out;
                background-color: " . $this->hex2rgba($bg_color, '0.85') . ";
            }
            
            @media screen and (max-width: 768px){
                #ays-quiz-container-" . $id . "{
                    max-width: $mobile_max_width;
                }
            }
                        
            /* Custom css styles */
            " . $options['custom_css'] . "
            
            /* RTL direction styles */
            " . $rtl_style . "
        </style>";

        
    /*******************************************************************************************************/
        
        /*
         * Quiz container
         *
         * Generating HTML code
         */
        
        $quiz_theme = "";
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';
        switch ($options['quiz_theme']) {
            case 'elegant_dark':
                $quiz_theme = "ays_quiz_elegant_dark";
                break;
            case 'elegant_light':
                $quiz_theme = "ays_quiz_elegant_light";
                break;
            case 'rect_dark':
                $quiz_theme = "ays_quiz_rect_dark";
                break;
            case 'rect_light':
                $quiz_theme = "ays_quiz_rect_light";
                break;
            case 'modern_dark':
                $quiz_theme = "ays_quiz_modern_dark";
                break;                
            case 'modern_light':
                $quiz_theme = "ays_quiz_modern_light";
                break;
        }
        
        $custom_class = isset($options['custom_class']) && $options['custom_class'] != "" ? $options['custom_class'] : "";
        
        $quiz_container_first_part = "
            <div class='ays-quiz-container ".$quiz_theme." ".$custom_class."' data-quest-effect='".$quest_animation."' id='ays-quiz-container-" . $id . "'>
                {$live_progress_bar}
                {$ays_quiz_music_html}
                <div class='ays-questions-container'>
                    $ays_quiz_reports
                    <form 
                        action='' 
                        method='post' 
                        id='ays_finish_quiz_" . $id . "' 
                        class='" . $correction_class . " " . $enable_questions_result . " " . $enable_logged_users . "'
                    >";
        if($question_per_page && $question_count_per_page > 0){
            $quiz_container_first_part .= "<input type='hidden' class='ays_question_count_per_page' value='$question_count_per_page'>";
        }
        
        $quiz_container_first_part .= "
            <input type='hidden' value='" . $answer_view_class . "' class='answer_view_class'>
            <input type='hidden' value='" . $enable_arrows . "' class='ays_qm_enable_arrows'>";
        
        $quiz_container_middle_part = "";
        
        if($paypal !== null){
            if(is_user_logged_in()){
                if($paypal == ''){
                    $quiz_container_middle_part = __("It seems PayPal Client ID is missing.", $this->plugin_name);
                    $main_content_first_part = "";
                    $main_content_last_part = "";
                }else{
                    $quiz_container_middle_part = $paypal;
                    $main_content_first_part = "";
                    $main_content_last_part = "";
                }
            }else{
                if(isset($paypal_options['payment_terms'])){
                    $payment_terms = $paypal_options['payment_terms'];
                }else{
                    $payment_terms = "lifetime";
                }
                switch($payment_terms){
                    case "onetime":
                        $quiz_container_middle_part = $paypal;
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                    case "lifetime":
                        $quiz_container_middle_part = ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                }
            }
        }
        if($is_expired){
            $quiz_container_middle_part = $expired_quiz_message;
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        if($enable_tackers_count){
            $quiz_tackers_count = $this->get_quiz_tackers_count($id);
            if($quiz_tackers_count >= $tackers_count){
                $quiz_container_middle_part = $tackers_message;
                $main_content_first_part = "";
                $main_content_last_part = "";
            }
        }
        if($limit_users === true){
            $quiz_container_middle_part = $limit_users_html;
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        if($user_massage !== null){
            $quiz_container_middle_part = "<!-- This content is empty -->";
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        
        $quiz_container_last_part = $quiz_content_script;
        $nonce = wp_create_nonce( 'ays_finish_quiz' );
        $quiz_container_last_part .= "
                    <input type='hidden' name='quiz_id' value='" . $id . "'/>
                    <input type='hidden' name='start_date' class='ays-start-date'/>
                    <input type='hidden' name='ays_finish_quiz_nonce_".$id."' value='".$nonce."'/>
                </form>";
        if($user_massage !== null){
            $quiz_container_last_part .= $user_massage;
        }
        $quiz_container_last_part .= "</div>
                                </div>";
        
        
    /*******************************************************************************************************/
        
        /*
         * Generating Quiz parts array
         */
        
        $quiz_parts = array(
            "container_first_part" => $quiz_container_first_part,
            "main_content_first_part" => $main_content_first_part,
            "main_content_middle_part" => $quiz_container_middle_part,
            "main_content_last_part" => $main_content_last_part,
            "quiz_styles" => $quiz_styles,
            "quiz_additional_styles" => "",
            "container_last_part" => $quiz_container_last_part,
        );
        
        $quizOptions = array(
            'buttons' => $buttons,
            'correction' => $enable_correction,
            'randomizeAnswers' => $randomize_answers,
            'questionImageWidth' => $question_image_width,
            'questionImageHeight' => $question_image_height,
            'questionImageSizing' => $question_image_sizing,
            'questionsCounter' => $questions_counter,
            'informationForm' => $options['information_form'],
            'answersViewClass' => $answer_view_class,
            'quizTheme' => $options['quiz_theme'],
            'rtlDirection' => $rtl_direction,
            'showQuestionCategory' => $show_question_category,
        );
        
        $ays_quiz = (object)array(
            "quizID" => $id,
            "quizOptions" => $quizOptions,
            "questions" => $arr_questions,
            "questionsCount" => $questions_count,
            "quizParts" => $quiz_parts,
            "quizColors" => array(
                "Color" => $color,
                "textColor" => $text_color,
                "bgColor" => $bg_color,
                "boxShadowColor" => $box_shadow_color,
                "borderColor" => $quiz_border_color
            )
        );
            
        return $ays_quiz;
    }

    public function ays_generate_quiz($quiz){
        
        $quiz_id = $quiz->quizID;
        $arr_questions = $quiz->questions;
        $questions_count = $quiz->questionsCount;
        $options = $quiz->quizOptions;
        $questions = "";
        $questions = $this->get_quiz_questions($arr_questions, $quiz_id, $options, false);
        
        if($quiz->quizParts['main_content_middle_part'] == ""){
            $quiz->quizParts['main_content_middle_part'] = $questions;
        }
        $additional_css = "
            <style>
                #ays-quiz-container-" . $quiz_id . " p {
                    margin: 0.625em;
                }
                
                #ays-quiz-container-" . $quiz_id . " .ays-field.checked_answer_div input:checked~label {
                    background-color: " . $this->hex2rgba($quiz->quizColors['Color'], '0.6') . ";
                }

                #ays-quiz-container-" . $quiz_id . " .ays-field:hover{
                    background: " . $this->hex2rgba($quiz->quizColors['Color'], '0.8') . ";
                    color: #fff;
                    transition: all .3s;
                }
                
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:hover,
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:focus {
                    box-shadow: 0 0 0 2px white, 0 0 0 3px " . $quiz->quizColors['Color'] . ";
                    background: " . $quiz->quizColors['Color'] . ";
                }
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        
        $container = implode("", $quiz->quizParts);
        
        return $container;
    }

    public function get_quiz_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }
    
    public function get_quiz_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizcategories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }

    public function get_question_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_categories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }

    public function get_quiz_tackers_count($id){
        global $wpdb;

        $sql = "SELECT COUNT(*)
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $id;

        $count = intval($wpdb->get_var($sql));

        return $count;
    }
    
    public function get_quiz_results_count_by_id($id){
        global $wpdb;

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    public function get_quiz_attributes_by_id($id){
        global $wpdb;
        $quiz_attrs = isset(json_decode($this->get_quiz_by_id($id)['options'])->quiz_attributes) ? json_decode($this->get_quiz_by_id($id)['options'])->quiz_attributes : array();
        $quiz_attributes = implode(',', $quiz_attrs);
        if (!empty($quiz_attributes)) {
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes WHERE `id` in ($quiz_attributes) AND published = 1";
            $results = $wpdb->get_results($sql);
            return $results;
        }
        return array();

    }
    
    public function get_quiz_question_by_id($id){
        
        global $wpdb;
        
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;
        
        $results = $wpdb->get_row($sql, "ARRAY_A");
        
        return $results;

    }

    public function get_quiz_questions($ids, $quiz_id, $options, $per_page){
        
        $container = $this->ays_questions_parts($ids, $quiz_id, $options, $per_page);
        $questions_container = array();
        foreach($container as $key => $question){
            $answer_container = '';
            $use_html = $this->in_question_use_html($question['questionID']);
            switch ($question["questionType"]) {
                case "select":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                    );
                    $answer_container .= $this->ays_dropdown_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "text":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                    );
                    $answer_container .= $this->ays_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "short_text":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                    );
                    $answer_container .= $this->ays_short_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "number":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                    );
                    $answer_container .= $this->ays_number_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                default:
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'rtlDirection' => $options['rtlDirection'],
                        'questionType' => $question["questionType"],
                        'answersViewClass' => $options['answersViewClass'],
                        'useHTML' => $use_html,
                    );
                    $answer_container .= $this->ays_default_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
            }
            $question['questionParts']['question_middle_part'] = $answer_container;
            $questions_container[] = implode("", $question['questionParts']);
        }
        $container = implode("", $questions_container);
        return $container;
    }
    
    public function ays_questions_parts($ids, $quiz_id, $options, $per_page){
        global $wpdb;
        $total = count($ids);
        $container = array();
        $buttons = $options['buttons'];
        $enable_arrows = $buttons['enableArrows'];
        
        foreach($ids as $key => $id){
            $current = $key + 1;
            if($total == $current){
                $last = true;
            }else{
                $last = false;
            }
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;
            $question = $wpdb->get_row($sql, 'ARRAY_A');

            if (!empty($question)) {
                $answers = $this->get_answers_with_question_id($question["id"]);
                $question_image = '';
                $question_image_style = '';
                $question_category = '';
                $show_question_category = $options['showQuestionCategory'];
                if($show_question_category){
                    $question_category_data = $this->get_question_category_by_id($question['category_id']);
                    $question_category = $question_category_data['title'];

                    $question_category = "<p style='margin:0!important;text-align:left;'>
                        <em style='font-style:italic;font-size:0.8em;'>". __("Category", $this->plugin_name) .":</em>
                        <strong style='font-size:0.8em;'>{$question_category}</strong>
                    </p>";
                }
                $question_options     = json_decode($question['options'], true);
                
                $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on') ? true : false;
                
                $question_image_style = "style='width:{$options['questionImageWidth']};height:{$options['questionImageHeight']};object-fit:{$options['questionImageSizing']};object-position:center center;'";
                
                if ($question['question_image'] != NULL) {
                    $question_image = '<div class="ays-image-question-img"><img src="' . $question['question_image'] . '" alt="Question Image" ' . $question_image_style . '></div>';
                }
                $answer_view_class = "";
                $question_hint = '';
                $user_explanation = "";
                if ($options['randomizeAnswers']) {
                    shuffle($answers);
                }
                if (isset($question['question_hint']) && strlen($question['question_hint']) !== 0) {
                    $question_hint = "<div class='ays_question_hint_container'><i class='ays_fa ays_fa_info_circle ays_question_hint' aria-hidden='true'></i><span class='ays_question_hint_text'>" . do_shortcode(wpautop(stripslashes($question['question_hint']))) . "</span></div>";
                }
                if(isset($question['user_explanation']) && $question['user_explanation'] == 'on'){
                    $user_explanation = "<div class='ays_user_explanation'>
                        <textarea placeholder='".__('You can enter your answer explanation',$this->plugin_name)."' class='ays_user_explanation_text' name='user-answer-explanation[{$id}]'></textarea>
                    </div>";
                }

                if($question['wrong_answer_text'] == ''){
                    $wrong_answer_class = 'ays_do_not_show';
                }else{
                    $wrong_answer_class = '';
                }
                if($question['right_answer_text'] == ''){
                    $right_answer_class = 'ays_do_not_show';
                }else{
                    $right_answer_class = '';
                }
                
                if($options['questionsCounter']){
                    $questions_counter = "<p class='ays-question-counter animated'>{$current} / {$total}</p>";
                }else{
                    $questions_counter = "";
                }
                
                $early_finish = "";                
                if($buttons['earlyButton']){
                    $early_finish = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_flag_checkered ays_early_finish action-button ays_arrow'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_early_finish action-button' value='" . __('Finish', $this->plugin_name) . "'/>";
                }
                
                $clear_answer = "";                
                if($buttons['clearAnswerButton']){
                    $clear_answer = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_eraser ays_clear_answer action-button ays_arrow'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_clear_answer action-button' value='" . __('Clear', $this->plugin_name) . "'/>";
                }
                if($options['correction']){
                    $clear_answer = "";
                }
                
                if ($last) {
                    switch($options['informationForm']){
                        case "disable":
                            $input = "<i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . ($enable_arrows ? 'ays_display_none' : '') . " ays_finish action-button' value='" . __('See Result', $this->plugin_name) . "'/>";
                            break;
                        case "before":
                            $input = "<i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . ($enable_arrows ? 'ays_display_none' : '') . " ays_finish action-button' value='" . __('See Result', $this->plugin_name) . "'/>";
                            break;
                        case "after":
                            $input = "<i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow'></i><input type='button' name='next' class=' " . ($enable_arrows ? 'ays_display_none' : '') . " ays_next action-button' value='" . __('Finish', $this->plugin_name) . "' />";
                            break;
                        default:
                            $input = "<i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . ($enable_arrows ? 'ays_display_none' : '') . " ays_finish action-button' value='" . __('See Result', $this->plugin_name) . "'/>";
                            break;                        
                    }
                    $buttons_div = "<div class='ays_buttons_div'>
                            {$clear_answer}
                            <i class=\"ays_fa ays_fa_arrow_left ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                            <input type='button' name='next' class='ays_previous action-button " . $buttons['prevButton'] . "'  value='".__('Prev', $this->plugin_name)."' />
                            {$input}
                        </div>";
                }else{
                    $buttons_div = "<div class='ays_buttons_div'>
                        {$clear_answer}
                        <i class=\"ays_fa ays_fa_arrow_left ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_previous action-button " . $buttons['prevButton'] . "' value='".__('Prev', $this->plugin_name)."' />
                        " . $early_finish . "
                        <i class=\"ays_fa ays_fa_arrow_right ays_next action-button ays_arrow ays_next_arrow " . $buttons['nextArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_next action-button " . $buttons['nextButton'] . "' value='" . __('Next', $this->plugin_name) . "' />                        
                    </div>";
                }
                
                $additional_css = "";
                $answer_view_class = $options['answersViewClass'];
                
                $question_bg_image = (isset($question_options['bg_image']) && $question_options['bg_image'] != "") ? $question_options['bg_image'] : null;
                $question_bg_class = ($question_bg_image !== null) ? "ays-quiz-question-with-bg" : "";
                
                switch ($options['quizTheme']) {
                    case 'elegant_dark':
                    case 'elegant_light':
                    case 'rect_dark':
                    case 'rect_light':
                        $question_html = "<div class='ays_quiz_question'>
                                " . do_shortcode(wpautop(stripslashes($question['question']))) . "
                            </div>
                            {$question_image}";
                        $answer_view_class = "ays_".$answer_view_class."_view_container";
                        if($question_bg_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_bg_image}');
                                }
                            </style>";
                        }
                        break;
                    case 'modern_light':
                        $question_image = $question['question_image'];
                        $question_html = "<div class='ays_quiz_question'>
                                " . do_shortcode(wpautop(stripslashes($question['question']))) . "
                            </div>";
                        if($question_image != "" || $question_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_image}');
                                }
                            </style>";
                        }elseif($question_bg_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_bg_image}');
                                }
                            </style>";
                        }
                        break;
                    case 'modern_dark':
                        $question_image = $question['question_image'];
                        $question_html = "<div class='ays-modern-dark-question'>
                                ".do_shortcode(wpautop(stripslashes($question["question"])))."
                            </div>";
                        if($question_image != "" || $question_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_image}');
                                }
                            </style>";
                        }elseif($question_bg_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_bg_image}');
                                }
                            </style>";
                        }
                        break;
                    default:
                        $question_html = "<div class='ays_quiz_question'>
                                " . do_shortcode(wpautop(stripslashes($question['question']))) . "
                            </div>
                            {$question_image}";
                        $answer_view_class = "ays_".$answer_view_class."_view_container";
                        if($question_bg_image !== null){
                            $additional_css = "<style>
                                #ays-quiz-container-" . $quiz_id . " div.step[data-question-id='".$question["id"]."'] {
                                    background-image:url('{$question_bg_image}');
                                }
                            </style>";
                        }
                        break;
                }
                $not_influence_to_score_class = $not_influence_to_score ? 'not_influence_to_score' : '';
                $container_first_part = "<div class='step ".$question_bg_class." ".$not_influence_to_score_class."' data-question-id='" . $question["id"] . "'>
                    {$question_hint}
                    {$questions_counter}
                    <div class='ays-abs-fs'>
                        {$question_category}
                        {$question_html}
                        <div class='ays-quiz-answers $answer_view_class'>";
                                            
                $container_last_part = "</div>
                        {$user_explanation}
                        {$buttons_div}
                        <div class='wrong_answer_text $wrong_answer_class' style='display:none'>
                            " . do_shortcode(wpautop(stripslashes($question['wrong_answer_text']))) . "
                        </div>
                        <div class='right_answer_text $right_answer_class' style='display:none'>
                            " . do_shortcode(wpautop(stripslashes($question["right_answer_text"]))) . "
                        </div>
                        <div class='ays_questtion_explanation' style='display:none'>
                            " . do_shortcode(wpautop(stripslashes($question["explanation"]))) . "
                        </div>
                        {$additional_css}
                    </div>
                </div>";
                
                $container[] = array(
                    'quizID' => $quiz_id,
                    'questionID' => $question['id'],
                    'questionAnswers' => $answers,
                    'questionType' => $question["type"],
                    'questionParts' => array(
                        'question_first_part' => $container_first_part,
                        'question_middle_part' => "",
                        'question_last_part' => $container_last_part
                    )
                );
            }
        }
        return $container;
    }

    protected function get_answers_with_question_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_answers
                WHERE question_id=" . $id;

        $answer = $wpdb->get_results($sql, 'ARRAY_A');

        return $answer;
    }

    public function get_quiz_questions_count($id){
        global $wpdb;

        $sql = "SELECT `question_ids`
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $questions_str = $wpdb->get_row($sql, 'ARRAY_A');
        $questions = explode(',', $questions_str['question_ids']);
        return $questions;
    }

    public function ays_finish_quiz(){
        error_reporting(0);
        if(!session_id()) {
            session_start();
        }
        $quiz_id = isset($_REQUEST['ays_quiz_id']) ? absint(intval($_REQUEST['ays_quiz_id'])) : 0;
        if($quiz_id === 0){            
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array("status" => false, "message" => "No no no" ));
            wp_die();
        }
        if (isset($_REQUEST["ays_finish_quiz_nonce_".$quiz_id]) && wp_verify_nonce($_REQUEST["ays_finish_quiz_nonce_".$quiz_id], 'ays_finish_quiz')) {
            global $wpdb;
            $questions_answers = (isset($_REQUEST["ays_questions"])) ? $_REQUEST['ays_questions'] : array();

            $quiz = $this->get_quiz_by_id($quiz_id);
            $quiz_intervals = json_decode($quiz['intervals']);
            $quiz_attributes = $this->get_quiz_attributes_by_id($quiz_id);
            $options = json_decode($quiz['options']);
            $quiz_image = ( isset($quiz['quiz_image']) && $quiz['quiz_image'] != "" ) ? $quiz['quiz_image'] : "";
            $quiz_questions_count = $this->get_quiz_questions_count($quiz_id);

            if (isset($options->enable_question_bank) && $options->enable_question_bank == "on" && isset($options->questions_count) && intval($options->questions_count) > 0 && count($quiz_questions_count) > intval($options->questions_count)) {
                $question_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            } else {
                $question_ids = $this->get_quiz_questions_count($quiz_id);
            }
            // Strong calculation of checkbox answers
            $options->checkbox_score_by = ! isset($options->checkbox_score_by) ? 'on' : $options->checkbox_score_by;
            $strong_count_checkbox = (isset($options->checkbox_score_by) && $options->checkbox_score_by == "on") ? true : false;
            
            // Calculate the score
            $options->calculate_score = ! isset($options->calculate_score) ? 'by_correctness' : $options->calculate_score;
            $calculate_score = (isset($options->calculate_score) && $options->calculate_score != "") ? $options->calculate_score : 'by_correctness';

            // Disable store data 
            $options->disable_store_data = ! isset( $options->disable_store_data ) ? 'off' : $options->disable_store_data;
            $disable_store_data = (isset($options->disable_store_data) && $options->disable_store_data == 'off') ? true : false;

            // Display score option
            $display_score = (isset($options->display_score) && $options->display_score != "") ? $options->display_score : 'by_percentage';

            // Send interval message to user
            $options->send_interval_msg = ! isset( $options->send_interval_msg ) ? 'off' : $options->send_interval_msg;
            $send_interval_msg = (isset($options->send_interval_msg) && $options->send_interval_msg == 'on') ? true : false;
            
            // Send interval message to user
            $options->send_results_user = ! isset( $options->send_results_user ) ? 'off' : $options->send_results_user;
            $send_results_user = (isset($options->send_results_user) && $options->send_results_user == 'on') ? true : false;

            // Send interval message to admin
            $options->send_interval_msg_to_admin = ! isset( $options->send_interval_msg_to_admin ) ? 'off' : $options->send_interval_msg_to_admin;
            $send_interval_msg_to_admin = (isset($options->send_interval_msg_to_admin) && $options->send_interval_msg_to_admin == 'on') ? true : false;

            // Send interval message to admin
            $options->send_results_admin = ! isset( $options->send_results_admin ) ? 'on' : $options->send_results_admin;
            $send_results_admin = (isset($options->send_results_admin) && $options->send_results_admin == 'on') ? true : false;

            // Show interval message
            $options->show_interval_message = isset($options->show_interval_message) ? $options->show_interval_message : 'on';
            $show_interval_message = (isset($options->show_interval_message) && $options->show_interval_message == 'on') ? true : false;

            // MailChimp
            $quiz_settings = $this->settings;
            $mailchimp_res = ($quiz_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mailchimp');
            $mailchimp = json_decode($mailchimp_res, true);
            $mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
            $mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
            
            $enable_mailchimp = (isset($options->enable_mailchimp) && $options->enable_mailchimp == 'on') ? true : false;
            $mailchimp_list = (isset($options->mailchimp_list)) ? $options->mailchimp_list : '';
            $mailchimp_email = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? $_REQUEST['ays_user_email'] : "";
            $user_name = explode(" ", $_REQUEST['ays_user_name']);
            $mailchimp_fname = (isset($user_name[0]) && $user_name[0] != "") ? $user_name[0] : "";
            $mailchimp_lname = (isset($user_name[1]) && $user_name[1] != "") ? $user_name[1] : "";
            
            // Campaign Monitor
            $monitor_res     = ($quiz_settings->ays_get_setting('monitor') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('monitor');
            $monitor         = json_decode($monitor_res, true);
            $monitor_client  = isset($monitor['client']) ? $monitor['client'] : '';
            $monitor_api_key = isset($monitor['apiKey']) ? $monitor['apiKey'] : '';
            $enable_monitor  = (isset($options->enable_monitor) && $options->enable_monitor == 'on') ? true : false;
            $monitor_list    = (isset($options->monitor_list)) ? $options->monitor_list : '';
            $monitor_email   = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $monitor_name    = sanitize_text_field($_REQUEST['ays_user_name']);

            // ActiveCampaign
            $active_camp_res        = ($quiz_settings->ays_get_setting('active_camp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('active_camp');
            $active_camp            = json_decode($active_camp_res, true);
            $active_camp_url        = isset($active_camp['url']) ? $active_camp['url'] : '';
            $active_camp_api_key    = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';
            $enable_active_camp     = (isset($options->enable_active_camp) && $options->enable_active_camp == 'on') ? true : false;
            $active_camp_list       = (isset($options->active_camp_list)) ? $options->active_camp_list : '';
            $active_camp_automation = (isset($options->active_camp_automation)) ? $options->active_camp_automation : '';
            $active_camp_email      = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $user_name              = explode(" ", $_REQUEST['ays_user_name']);
            $active_camp_fname      = (isset($user_name[0]) && $user_name[0] != "") ? $user_name[0] : "";
            $active_camp_lname      = (isset($user_name[1]) && $user_name[1] != "") ? $user_name[1] : "";
            $active_camp_phone      = sanitize_text_field($_REQUEST['ays_user_phone']);
            
            // Zapier
            $zapier_res    = ($quiz_settings->ays_get_setting('zapier') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('zapier');
            $zapier        = json_decode($zapier_res, true);
            $enable_zapier = (isset($options->enable_zapier) && $options->enable_zapier == 'on') ? true : false;
            $zapier_hook   = isset($zapier['hook']) ? $zapier['hook'] : '';
            $zapier_data   = array();

            $zapier_data['E-mail'] = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $zapier_data['Name']  = isset($_REQUEST['ays_user_name']) ? sanitize_text_field($_REQUEST['ays_user_name']) : "";
            $zapier_data['Phone'] = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field($_REQUEST['ays_user_phone']) : "";

            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $zapier_data[$attr->name] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }

            // Slack
            $slack_res          = ($quiz_settings->ays_get_setting('slack') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('slack');
            $slack              = json_decode($slack_res, true);
            $enable_slack       = (isset($options->enable_slack) && $options->enable_slack == 'on') ? true : false;
            $slack_conversation = (isset($options->slack_conversation)) ? $options->slack_conversation : '';
            $slack_token        = isset($slack['token']) ? $slack['token'] : '';
            $slack_data         = array();

            $slack_data['Name']   = isset($_REQUEST['ays_user_name']) ? sanitize_text_field($_REQUEST['ays_user_name']) : "";
            $slack_data['E-mail'] = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $slack_data['Phone']  = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field($_REQUEST['ays_user_phone']) : "";
                        
            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $slack_data[$attr->name] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }
            
            // User explanation            

            if(isset($_REQUEST['user-answer-explanation']) && count($_REQUEST['user-answer-explanation']) != 0){
                $user_explanation = $_REQUEST['user-answer-explanation'];
            }else{
                $user_explanation = array();
            }

            $questions_count = count($question_ids);
            $correctness = array();
            $user_answered = array();
            $correctness_results = array();
            $answer_max_weights = array();
            if (is_array($questions_answers)) {
                foreach ($questions_answers as $key => $questions_answer) {
                    $continue = false;
                    $question_id = explode('-', $key)[2];
                    if($this->is_question_not_influence($question_id)){
                        $questions_count--;
                        $continue = true;
                    }
                    $multiple_correctness = array();
                    $has_multiple = $this->has_multiple_correct_answers($question_id);
                    $is_checkbox = $this->is_checkbox_answer($question_id);
                    $answer_max_weights[$question_id] = $this->get_answers_max_weight($question_id, $is_checkbox);
                    
                    $user_answered["question_id_" . $question_id] = $questions_answer;
                    if ($has_multiple) {
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_id) {
                                $multiple_correctness[] = $this->check_answer_correctness($question_id, $answer_id, $calculate_score);
                            }
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = array_sum($multiple_correctness);
                                }
                                $correctness_results["question_id_" . $question_id] = array_sum($multiple_correctness);
                                continue;
                            }
                            
                            if($strong_count_checkbox === false){
                                if(!$continue){
                                    $correctness[$question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                            }else{
                                if ($this->isHomogenous($multiple_correctness, $question_id)) {
                                    if(!$continue){
                                        $correctness[$question_id] = true;
                                    }
                                    $correctness_results["question_id_" . $question_id] = true;
                                } else {
                                    if(!$continue){
                                        $correctness[$question_id] = false;
                                    }
                                    $correctness_results["question_id_" . $question_id] = false;
                                }
                            }
                        } else {
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                continue;
                            }
                            if($strong_count_checkbox === false){
                                if($this->check_answer_correctness($question_id, $questions_answer, $calculate_score)){
                                    if(!$continue){
                                        $correctness[$question_id] = 1 / intval($this->count_multiple_correct_answers($question_id));
                                    }
                                }else{
                                    if(!$continue){
                                        $correctness[$question_id] = false;
                                    }
                                }
                                $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                            }else{
                                if(!$continue){
                                    $correctness[$question_id] = false;
                                }
                                $correctness_results["question_id_" . $question_id] = false;
                            }
                        }
                    } elseif($this->has_text_answer($question_id)) {
                        if(!$continue){
                            $correctness[$question_id] = $this->check_text_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = $this->check_text_answer_correctness($question_id, $questions_answer, $calculate_score);
                    } else {
                        if(!$continue){
                            $correctness[$question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                    }
                }
                
                $new_correctness = array();
                $quiz_weight_correctness = array();
                $quiz_weight_points = array();
                $corrects_count = 0;
                foreach($correctness as $question_id => $item){
                    $question_weight = $this->get_question_weight($question_id);
                    $new_correctness[] = $question_weight * floatval($item);
                    $quiz_weight_points[] = $question_weight * floatval($answer_max_weights[$question_id]);
                    $quiz_weight_correctness[] = $question_weight;
                    switch($calculate_score){
                        case "by_correctness":
                            if($item){
                                $corrects_count++;
                            }
                        break;
                        case "by_points":
                            if($item == floatval($answer_max_weights[$question_id])){
                                $corrects_count++;
                            }
                        break;
                        default:
                            if($item){
                                $corrects_count++;
                            }
                        break;
                    }
                }

                $average_percent = 100 / $questions_count;
                
                switch($calculate_score){
                    case "by_correctness":
                        $quiz_weight = array_sum($quiz_weight_correctness);
                    break;
                    case "by_points":
                        $quiz_weight = array_sum($quiz_weight_points);
                    break;
                    default:
                        $quiz_weight = array_sum($quiz_weight_correctness);
                    break;
                }
                $correct_answered_count = array_sum($new_correctness);
                
                if($quiz_weight == 0){
                    $final_score = floatval(0);
                }else{
                    $final_score = floatval(floor(($correct_answered_count / $quiz_weight) * 100));
                }

                switch($display_score){
                    case "by_correctness":
                        $score = $corrects_count . " / " . $questions_count;
                    break;
                    case "by_points":
                        $score = $correct_answered_count . " / " . $quiz_weight;
                    break;
                    case "by_percentage":
                        $score = $final_score . "%";
                    break;
                    default:
                        $score = $final_score . "%";
                    break;
                }


                $hide_result = false;
                $finish_text = null;
                $interval_msg = null;
                $interval_message = '';
                $interval_image = null;
                $product_id = null;
                if (isset($options->enable_result) && $options->enable_result == "on") {
                    $text = $options->result_text;
                    $hide_result = true;
                }

                foreach ($quiz_intervals as $quiz_interval) {
                    $quiz_interval = (array)$quiz_interval;
                    if ($quiz_interval['interval_min'] <= $final_score && $final_score <= $quiz_interval['interval_max']) {
                        $interval_msg = stripslashes($quiz_interval['interval_text']);
                        $interval_image = $quiz_interval['interval_image'];
                        $product_id = $quiz_interval['interval_wproduct'];

                        $interval_message = "<div>";
                        if($interval_image !== null){
                            $interval_message .= "<div style='width:100%;max-width:400px;margin:10px auto;'>";
                            $interval_message .= "<img style='max-width:100%;' src='".$interval_image."'>";
                            $interval_message .= "</div>";
                        }
                        if($interval_msg !== null){
                            $interval_message .= "<div>" . $interval_msg . "</div>";
                        }
                        $interval_message .= "</div>";
                        break;
                    }
                }
                
                if($finish_text == ''){
                    $finish_text = null;
                }
                
                $correctness_and_answers = array(
                    'correctness' => $correctness_results,
                    'user_answered' => $user_answered
                );
                $quiz_logo = "";
                if($quiz_image !== ""){
                    $quiz_logo = "<img src='".$quiz_image."' alt='Quiz logo' title='Quiz logo'>";
                }
                
                $quiz_attributes_information = array();
                foreach ($quiz_attributes as $attribute) {
                    $quiz_attributes_information[strval($attribute->name)] = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
                }
                
                $message_data = array(
                    'quiz_name' => stripslashes($quiz['title']),
                    'user_name' => $_REQUEST['ays_user_name'],
                    'user_email' => $_REQUEST['ays_user_email'],
                    'user_pass_time' => $this->get_time_difference(esc_sql($_REQUEST['start_date']), esc_sql($_REQUEST['end_date'])),
                    'quiz_time' => $this->secondsToWords($options->timer),
                    'score' => $final_score . "%",
                    'user_points' => $correct_answered_count,
                    'max_points' => $quiz_weight,
                    'user_corrects_count' => $corrects_count,
                    'questions_count' => $questions_count,
                    'quiz_logo' => $quiz_logo,
                    'interval_message' => $interval_message,
                    'avg_score' => $this->ays_get_average_of_scores($quiz_id) . "%",
                    'avg_rate' => round($this->ays_get_average_of_rates($quiz_id), 1),
                    'current_date' => date('M d, Y', strtotime(esc_sql($_REQUEST['end_date']))),
                );

                $data = array(
                    'user_ip' => $this->get_user_ip(),
                    'user_name' => $_REQUEST['ays_user_name'],
                    'user_email' => $_REQUEST['ays_user_email'],
                    'user_phone' => $_REQUEST['ays_user_phone'],
                    'start_date' => esc_sql($_REQUEST['start_date']),
                    'end_date' => esc_sql($_REQUEST['end_date']),
                    'answered' => $correctness_and_answers,
                    'score' => $final_score,
                    'quiz_id' => absint(intval($_REQUEST["quiz_id"])),
                    'user_explanation' => $user_explanation,
                    'calc_method' => $calculate_score,
                    'user_points' => $correct_answered_count,
                    'max_points' => $quiz_weight,
                    'user_corrects_count' => $corrects_count,
                    'questions_count' => $questions_count,
                    'attributes_information' => $quiz_attributes_information,
                );

                // Disabling store data in DB
                if($disable_store_data){
                    $result = $this->add_results_to_db($data);
                }else{
                    $result = true;
                }
                
                $nsite_url_base = get_site_url();
                $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
                $nsite_url = trim( $nsite_url_replaced, '/' );
                //$nsite_url = "levon.com";
                $nno_reply = "noreply@".$nsite_url;
                        
                if(isset($options->email_config_from_name) && $options->email_config_from_name != "") {
                    $uname = stripslashes($options->email_config_from_name);
                } else {
                    $uname = 'Quiz Maker'; //$_REQUEST['ays_user_name'];
                }
                if(isset($options->email_config_from_email) && $options->email_config_from_email != "") {
                    $nfrom = "From: " . $uname . " <".stripslashes($options->email_config_from_email).">";
                    $nfrom_smtp = stripslashes($options->email_config_from_email);
                }else{
                    $nfrom = "From: " . $uname . " <quiz_maker@".$nsite_url.">";
                    $nfrom_smtp = "quiz_maker@".$nsite_url;
                }
                if(isset($options->email_config_from_subject) && $options->email_config_from_subject != "") {
                    $subject = stripslashes($options->email_config_from_subject);
                } else {
                    $subject = stripslashes($quiz['title']);
                }
                
                $subject = $this->replace_message_variables($subject, $message_data);
                
                $cert = false;
                if (isset($options->enable_certificate) && $options->enable_certificate == "on"){
                    $cert = true;
                    if(isset($options->user_mail) && $options->user_mail == "on"){
                        if($options->mail_message == ""){
                            $options->mail_message = "Certificate";
                        }
                    }else{
                        $options->mail_message = "Certificate";
                    }
                    $options->user_mail = "on";
                }

                $last_result_id = $wpdb->insert_id;
                
                if (isset($options->user_mail) && $options->user_mail == "on") {
                    if (isset($_REQUEST['ays_user_email']) && filter_var($_REQUEST['ays_user_email'], FILTER_VALIDATE_EMAIL)) {
                        $pdf_content = null;
                        if($cert && $final_score >= intval($options->certificate_pass)){
                            $cert_title = stripslashes((isset($options->certificate_title)) ? $options->certificate_title : '');
                            $cert_body = wpautop(stripslashes((isset($options->certificate_body)) ? $options->certificate_body : ''));
                            $pdf = new Quiz_PDF_API();
                            $pdfData = array(
                                "type"          => "pdfapi",
                                "cert_title"    => $cert_title,
                                "cert_body"     => $cert_body,
                                "cert_score"    => $final_score,
                                "cert_data"     => $message_data,
                                "cert_user"     => $_REQUEST['ays_user_name'],
                                "cert_quiz"     => stripslashes($quiz['title'])
                            );
                            $pdf_content = $pdf->generate_PDF($pdfData);
                        }
                        $message = (isset($options->mail_message)) ? $options->mail_message : '';
                        $message = $this->replace_message_variables($message, $message_data);
                        $message = str_replace('%name%', $_REQUEST['ays_user_name'], $message);
                        $message = str_replace('%score%', $final_score, $message);
                        $message = str_replace('%logo%', $quiz_logo, $message);
                        $message = str_replace('%quiz_name%', stripslashes($quiz['title']), $message);
                        $message = str_replace('%date%', date("Y-m-d", current_time('timestamp')), $message);
                        $message = stripslashes(wpautop($message));
                        
                        if($send_interval_msg){
                            $message .= $interval_message;
                        }
                        
                        //AV Send results to User
                        if ($send_results_user) {
                            $message_content = $this->ays_report_mail_content($data, 'user', $send_results_user);
                            $message .= $message_content;
                        }
                        
                        if (isset($options->enable_smtp) && $options->enable_smtp == "on") {
                            $mail = new PHPMailer();
                            //Server settings
                            $mail->SMTPDebug = false;
                            $mail->CharSet = 'UTF-8';
                            
                            $mail->isSMTP();
                            $mail->Host = isset($options->smtp_host) ? $options->smtp_host : '';
                            $mail->SMTPAuth = true;
                            $mail->Username = isset($options->smtp_username) ? $options->smtp_username : '';
                            $mail->Password = isset($options->smtp_password) ? $options->smtp_password : '';
                            $mail->SMTPSecure = isset($options->smtp_secure) ? $options->smtp_secure : '';
                            $mail->Port = isset($options->smtp_port) ? $options->smtp_port : '';
                            
                            $mail->SetFrom($nfrom_smtp, $uname);
                            
                            $email = $_REQUEST['ays_user_email'];
                            $mail->addAddress($email, $_REQUEST['ays_user_name']);
                            
                            $mail->addReplyTo($nno_reply, 'Don\'t reply');
                            //$mail->addReplyTo('noreply@example.com', 'Don\'t reply');

                            //Content
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body = $message;
                            if ($cert && $final_score >= intval($options->certificate_pass)) {
                                if($pdf_content === true){
                                    $mail->addAttachment(__DIR__ . '/certificate.pdf', 'Certificate', 'base64', 'application/pdf');                                    
                                    if($mail->send()){
                                        $ays_send_mail = true;
                                    }else{
                                        $ays_send_mail = false;
                                    }
                                }
                            }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                                if($mail->send()){
                                    $ays_send_mail = true;
                                }else{
                                    $ays_send_mail = false;
                                }
                            }
                        } else {
                            $email = $_REQUEST['ays_user_email'];
                            $to = $_REQUEST['ays_user_name'] . " <$email>";

                            $headers = $nfrom."\r\n";
                            $headers .= "Reply-To: Don't reply <".$nno_reply."> \r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                            $attachment = array();
                            if($cert && $final_score >= intval($options->certificate_pass)){
                                if($pdf_content === true){
                                    $attachment = array(__DIR__ . '/certificate.pdf');
                                    $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                                }
                            }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }
                    }
                }
                
                if (isset($options->admin_mail) && $options->admin_mail == "on") {
                    if (filter_var(get_option('admin_email'), FILTER_VALIDATE_EMAIL)) {

                        $message_content = '';
                        if($send_interval_msg_to_admin){
                            $message_content .= $interval_message;
                        }

                        $message_content .= $this->ays_report_mail_content($data, 'admin', $send_results_admin || $send_interval_msg_to_admin);

                        $admin_subject = ' '.$data['user_name'].' '.$data['score'].'%';
                        if($data['calc_method'] == 'by_points'){
                            $admin_subject = ' '.$data['user_name'].' '.$data['user_points'].'/'.$data['max_points'];
                        }
                        
                        if (isset($options->enable_smtp) && $options->enable_smtp == "on") {
                            $mail = new PHPMailer();
                            //Server settings
                            $mail->SMTPDebug = false;
                            $mail->CharSet = 'UTF-8';

                            $mail->isSMTP();
                            $mail->Host = isset($options->smtp_host) ? $options->smtp_host : '';
                            $mail->SMTPAuth = true;
                            $mail->Username = isset($options->smtp_username) ? $options->smtp_username : '';
                            $mail->Password = isset($options->smtp_password) ? $options->smtp_password : '';
                            $mail->SMTPSecure = isset($options->smtp_secure) ? $options->smtp_secure : '';
                            $mail->Port = isset($options->smtp_port) ? $options->smtp_port : '';

                            $mail->SetFrom(isset($options->smtp_username) ? $options->smtp_username : '', $uname);

                            $mail->addAddress(get_option('admin_email'), '');

                            // Add a recipient for additional emails
                            if(isset($options->additional_emails) && !empty($options->additional_emails)) { 
                                $additional_emails = explode(", ", $options->additional_emails);
                                foreach($additional_emails as $additional_email){
                                    if(filter_var(trim($additional_email), FILTER_VALIDATE_EMAIL)){
                                        $mail->addAddress(trim($additional_email), '');
                                    }
                                }
                            }
                            $mail->addReplyTo('noreply@example.com', 'Don\'t reply');
                            $mail->isHTML(true);
                            $mail->Subject = stripslashes($quiz['title']).$admin_subject;
                            $mail->Body = $message_content;
                            if($mail->send()){
                                $ays_send_mail_to_admin = true;
                            }else{
                                $ays_send_mail_to_admin = false;
                            }
                        } else {
                            $email = get_option('admin_email');

                            $add_emails = "";
                            if(isset($options->additional_emails) && !empty($options->additional_emails)) {
                                $add_emails = ", ";
                                $additional_emails = explode(", ", $options->additional_emails);
                                foreach($additional_emails as $key => $additional_email){
                                    if($key==count($additional_emails)-1)
                                        $add_emails .= "<$additional_email>";
                                    else
                                       $add_emails .= "<$additional_email>, ";
                                }
                            }
                            $to = "<$email>".$add_emails;
                            $subject = stripslashes($quiz['title']).$admin_subject;
                            $headers = $nfrom."\r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                            $attachment = array();
                            $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                        }
                    }
                }
                
                if($enable_mailchimp && $mailchimp_list != ""){
                    if($mailchimp_username != "" && $mailchimp_api_key != ""){
                        $args = array(
                            "email" => $mailchimp_email,
                            "fname" => $mailchimp_fname,
                            "lname" => $mailchimp_lname
                        );
                        $mresult = $this->ays_add_mailchimp_transaction($mailchimp_username, $mailchimp_api_key, $mailchimp_list, $args);
                    }
                }
                
                if ($enable_monitor && $monitor_list != "") {
                    if ($monitor_client != "" && $monitor_api_key != "") {
                        $args    = array(
                            "EmailAddress" => $monitor_email,
                            "Name"         => $monitor_name,
                        );
                        $mresult = $this->ays_add_monitor_transaction($monitor_client, $monitor_api_key, $monitor_list, $args);
                    }
                }
                
                if ($enable_active_camp) {
                    if ($active_camp_url != "" && $active_camp_api_key != "") {
                        $args    = array(
                            "email"     => $active_camp_email,
                            "firstName" => $active_camp_fname,
                            "lastName"  => $active_camp_lname,
                            "phone"     => $active_camp_phone,
                        );
                        $mresult = $this->ays_add_active_camp_transaction($active_camp_url, $active_camp_api_key, $args, $active_camp_list, $active_camp_automation);
                    }
                }
                
                if ($enable_zapier && $zapier_hook != "") {
                    $zresult = $this->ays_add_zapier_transaction($zapier_hook, $zapier_data);
                }
                
                if ($enable_slack && $slack_token != "") {
                    $sresult = $this->ays_add_slack_transaction($slack_token, $slack_conversation, $slack_data, $quiz['title'], $final_score);
                }
                
                if($finish_text == null){
                    $finish_text = (isset($options->final_result_text) && $options->final_result_text != '')?$options->final_result_text:'';
                }
                $finish_text = $this->replace_message_variables($finish_text, $message_data);

                if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                    unset($_SESSION['ays_quiz_paypal_purchase']);
                }
                if(array_key_exists('ays_quiz_paypal_purchase', $_SESSION)){
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                    unset($_SESSION['ays_quiz_paypal_purchase']);
                }
                
                $admin_mails = get_option('admin_email');
                if ($result) {
                    $woo = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
                    $product = "";
                    if($product_id == 0){
                        $product_id = null;
                    }
                    if ($woo && isset($product_id)) {
                        $wpf = new WC_Product_Factory();
                        $cart_text = __('Add to cart', 'woocommerce');
                        $product = array(
                            'prodUrl'  => get_permalink(intval($product_id)),
                            'name'  => $wpf->get_product($product_id)->get_data()['name'],
                            'image' => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'single-post-thumbnail')[0],
                            'link'  => "<a href=\"?add-to-cart=$product_id\" data-quantity=\"1\" class=\"button product_type_simple add_to_cart_button ajax_add_to_cart\" data-product_id=\"$product_id\" data-product_sku=\"\" aria-label=\"$cart_text\" rel=\"nofollow\">$cart_text</a>"
                        );
                    }
                
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();
                    echo json_encode(array(
                        "status" => true,
                        "hide_result" => $hide_result,
                        "showIntervalMessage" => $show_interval_message,
                        "score" => $score,
                        "displayScore" => $display_score,
                        "finishText" => $finish_text,
                        "product" => $product,
                        "intervalMessage" => $interval_message,
                        "mail" => $ays_send_mail,
                        "mail_to_admin" => $ays_send_mail_to_admin,
                        "admin_mail" => $admin_mails
                    ));
                    wp_die();
                }else{
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();
                    echo json_encode(array("status" => false, "message" => "No no no", "admin_mail" => $admin_mails ));
                    wp_die();
                }

            } else {
                $admin_mails = get_option('admin_email');
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array("status" => false, "message" => "No no no", "admin_mail" => $admin_mails ));
                wp_die();
            }
        }
    }
    
    public function replace_message_variables($content, $data){
        foreach($data as $variable => $value){
            $content = str_replace("%%".$variable."%%", $value, $content);
        }
        return $content;
    }

    public function get_answers_max_weight($question_id, $has_multiple){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $answer_id = absint(intval($answer_id));
        $query_part = "";
        $sql = "SELECT MAX(weight) FROM {$answers_table} WHERE question_id={$question_id}";
        if($has_multiple){
            $sql = "SELECT SUM(weight) FROM {$answers_table} WHERE question_id={$question_id} AND weight > 0";
        }
        $checks = $wpdb->get_var($sql);
        $answer_weight = floatval($checks);
        
        return $answer_weight;
    }
        
    public function ays_report_mail_content($last_results, $where, $send_results){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $send = null;
        $send_info = null;
        if($where == 'user' && $send_results){
            $send = true;
            $send_info = true;
        }elseif($where == 'user' && !$send_results){
            $send = false;
            $send_info = false;
        }elseif($where == 'admin' && $send_results){
            $send = true;
            $send_info = true;
        }elseif($where == 'admin' && !$send_results){
            $send = false;
            $send_info = true;
        }

        $last_result = $last_results;
        $data_result = $last_results['answered'];
        
        $quiz_calc_method = $last_results['calc_method'];
        $all_quiz_points = $last_results['max_points'];
        $user_points_score = $last_results['answered']['correctness'];
        $user_points_scored = $last_results['user_points'];

        $duration = $this->get_time_difference($last_result['start_date'], $last_result['end_date']);

        $result_attributes = $last_results['attributes_information'];        

        $last_result['user_name'] = empty($last_result['user_name']) || $last_result['user_name'] == '' ? ' - ' : $last_result['user_name'];
        
        $last_result['user_email'] = empty($last_result['user_email']) || $last_result['user_email'] == '' ? ' - ' : $last_result['user_email'];

        $last_result['user_phone'] = empty($last_result['user_phone']) || $last_result['user_phone'] == '' ? ' - ' : $last_result['user_phone'];

        $td_value_html = '';
        if($send_info){
            $td_value_html .= "<tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Name', $this->plugin_name)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_name'] . "</td>
                   </tr>
                   <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Email', $this->plugin_name)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_email'] . "</td>
                   </tr>
                   <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Phone', $this->plugin_name)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_phone'] . "</td>
                   </tr>";

            foreach ($result_attributes as $attribute => $value) {
                $value = empty($value) || $value == '' ? ' - ' : $value;
                $td_value_html .= "<tr><td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>" . $attribute . "</td><td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $value . "</td></tr>";
            }
            $td_value_html .= " <tr>
                    <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Duration', $this->plugin_name)."</td>
                    <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $duration . " </td>
               </tr>";
        }
        if ($quiz_calc_method == 'by_correctness') {

            if($send_info){
                $td_value_html .= " <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Score', $this->plugin_name)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['score'] . " %</td>
                   </tr>";
            }
            if($send){
                $index = 1;
                foreach ($data_result['correctness'] as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        $correct_answers = $this->get_correct_answers($question_id);

                        if($this->has_text_answer($question_id)){
                            $user_answered = $this->get_user_text_answered((object)$data_result['user_answered'], $key);
                        }else{
                            $user_answered = $this->get_user_answered((object)$data_result['user_answered'], $key);
                        }

                        if(is_array($user_answered)){
                            $user_answered = $user_answered['message'];
                        }
                        if ($option == true) {
                            $td_value_html .= '<tr>
                                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',$this->plugin_name).' ' . $index . ' :</strong><br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',$this->plugin_name).':</strong><br/><p class="success">' . htmlentities(do_shortcode(stripslashes($correct_answers))) . '</p></td>
                                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('User answer',$this->plugin_name).':</strong><br/><p class="success">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '</p></td>
                                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                                <p class="success" style="font-weight: 600; color:green;">'.__('Success', $this->plugin_name).'</p>
                                            </td>
                                        </tr>';
                        } else {
                            $td_value_html .= '<tr>
                                        <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',$this->plugin_name).'' . $index . ' :</strong><br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                                        <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',$this->plugin_name).':</strong><br/><p class="success">' . htmlentities(do_shortcode(stripslashes($correct_answers))) . '</p></td>
                                        <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('User answer',$this->plugin_name).':</strong><br/><p class="error">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '</p></td>
                                        <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                            <p class="error" style="font-weight: 600; color:red;">'.__('Fail',$this->plugin_name).'</p>
                                        </td>
                                    </tr>';
                        }
                        $index++;
                    }
                }
            }
        }elseif($quiz_calc_method == 'by_points'){            

            if($send_info){
                $td_value_html .= " <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Score', $this->plugin_name)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='2'> ".$user_points_scored." / " . $all_quiz_points . " </td>
                   </tr>";
            }
            if($send){
                $index = 1;
                foreach ($data_result['correctness'] as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        $correct_answers = $this->get_correct_answers($question_id);
                        if($this->has_text_answer($question_id)){
                            $user_answered = $this->get_user_text_answered((object)$data_result['user_answered'], $key);
                        }else{
                            $user_answered = $this->get_user_answered((object)$data_result['user_answered'], $key);
                        }

                        $ans_point = $option;
                        $ans_point_class = 'success';
                        if(is_array($user_answered)){
                            $user_answered = $user_answered['message'];
                            $ans_point = '-';
                            $ans_point_class = 'error';
                        }

                        $td_value_html .= '<tr>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',$this->plugin_name).' ' . $index . ' :</strong><br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('User answer',$this->plugin_name).':</strong><br/><p class="'.$ans_point_class.'">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '</p></td>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Point',$this->plugin_name).':</strong><br/><p class="'.$ans_point_class.'" style="font-weight: 600; text-align:center;">'.$ans_point.'</p></td>
                        </tr>';
                        $index++;
                    }
                }
            }
        }
        
        $message_content = '<!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Document</title>
        </head>
        <body>
            <div>
                <h1>%%quiz_title%%</h1>
                <table style="border-collapse: collapse; width: 100%;">
                        %%attribute_values%%
                </table>
            </div>
        </body>
        </html>';

        $message_content = str_replace('%%quiz_title%%', stripslashes($quiz['title']), $message_content);
        $message_content = str_replace('%%attribute_values%%', $td_value_html, $message_content);

        return $message_content;
    }

    public function get_user_answered($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;
        
        if($choices == ''){
            return array(
                'message' => __( "The question was not answered.", $this->plugin_name ),
                'status' => false
            );
        }
        
        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                $text[] = $result['answer'];
            }
            $text = implode(', ', $text);
        } else {
            if ($choices == '')  $choices = 0;
            $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            $text = $result['answer'];
        }
        return $text;
    }

    public function get_user_answered_images($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;

        if($choices == ''){
            return '';
        }

        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                if(isset($result['image']) && $result['image'] != ''){
                    $text[] = "<img src='". $result['image'] ."' alt='Answer image'>";
                }
            }
            $text = '<br>' . implode('<br>', $text);
        } else {
            $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            if(isset($result['image']) && $result['image'] != ''){
                $text = "<br><img src='". $result['image'] ."' alt='Answer image'>";
            }else{
                $text = '';
            }
        }
        return $text;
    }

    public function get_user_text_answered($user_choice, $key){
        if($user_choice->$key == ""){
            $choices = __( "The user have not answered to this question.", $this->plugin_name );
        }else{
            $choices = trim($user_choice->$key);
        }
        
        return $choices;
    }

    public function get_correct_answers($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT answer FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($key == (count($correct_answers) - 1))
                $text .= $correct_answer->answer;
            else
                $text .= $correct_answer->answer . ',';
        }
        return $text;
    }

    public function get_correct_answer_images($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT image FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($correct_answer->image){
                $text .= "<img src='". $correct_answer->image ."' alt='Answer image'>";
            }
        }
        return $text;
    }
    
    public function check_answer_correctness($question_id, $answer_id, $calc_method){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $answer_id = absint(intval($answer_id));
        $checks = $wpdb->get_row("SELECT * FROM {$answers_table} WHERE question_id={$question_id} AND id={$answer_id}", "ARRAY_A");
        $answer_weight = floatval($checks['weight']);
        $answer = false;
        switch($calc_method){
            case "by_correctness":
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
            case "by_points":
                $answer = $answer_weight;
            break;
            default:
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
        }
        return $answer;
    }

    public function check_text_answer_correctness($question_id, $answer, $calc_method){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $checks = $wpdb->get_row("SELECT COUNT(*) AS qanak, answer, weight FROM {$answers_table} WHERE question_id={$question_id}", ARRAY_A);
        $answer_weight = floatval($checks['weight']);
        $answer_res = false;
        switch($calc_method){
            case "by_correctness":
                if(strtolower($checks['answer']) == strtolower(trim($answer)))
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
            case "by_points":
                if(strtolower($checks['answer']) == strtolower(trim($answer)))
                    $answer_res = $answer_weight;
                else
                    $answer_res = 0;
            break;
            default:
                if(strtolower($checks['answer']) == strtolower(trim($answer)))
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
        }
        return $answer_res;
    }

    public function count_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");        
        return $get_answers;
    }

    public function has_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");

        if (intval($get_answers) > 1) {
            return true;
        }
        return false;
    }

    public function has_text_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'short_text', 'number');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public function is_checkbox_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if ($get_answers == 'checkbox') {
            return true;
        }
        return false;
    }
    
    public function is_question_not_influence($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
        if(isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on'){
            return true;
        }
        return false;
    }

    public function in_question_use_html($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);
        if(isset($options['use_html']) && $options['use_html'] == 'on'){
            return true;
        }
        return false;
    }

    public function ays_get_image_thumbnauil($ans_img){
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'attachment' AND `guid` = '".$ans_img."'";
        $result_img =  $wpdb->get_row( $query, "ARRAY_A" );
        $url_img = wp_get_attachment_image_src($result_img['ID'], 'medium');
        if($url_img === false){
           $new_img = $ans_img;
        }else{
           $new_img = $url_img[0];
        }
        return $new_img;
    }
    
    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){

        $answer_container = "";
        foreach ($answers as $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
//                $ans_img = $this->ays_get_image_thumbnauil($answer['image']);
                $ans_img = $answer['image'];
                $answer_image = "<img src='{$ans_img}' alt='answer_image' class='ays-answer-image'>";
            }

            if($answer_image == ""){
                $answer_label_class = "";
                $answer_img_label_class = " ays_position_initial ";
            }else{
                if($options['answersViewClass'] == 'grid'){
                    $answer_label_class = " ays_empty_before_content ";
                }else{
                    $answer_label_class = "";
                }
                $answer_img_label_class = " ays_answer_caption ays_without_after_content ";
            }

            $label = "";

            if($options['useHTML']){
                $answer_content = do_shortcode((stripslashes($answer["answer"])));
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='$answer_label_class $answer_img_label_class'>" . $answer_content . "</label>";
            $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='ays_answer_image ays_empty_before_content'>{$answer_image}</label>";

            $answer_container .= "
            <div class='ays-field ays_" . $options['answersViewClass'] . "_view_item'>
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                {$label}    

            </div>";

        }
        
        return $answer_container;
    }
    
    protected function ays_text_answer_html($question_id, $quiz_id, $answers, $options){        
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<textarea style='$enable_correction_textarea' type='text' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'></textarea>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>".(__('Check', $this->plugin_name))."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"]))
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        return $answer_container;
    }
    
    protected function ays_number_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input style='$enable_correction_textarea' type='number' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>".(__('Check', $this->plugin_name))."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"]))
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        return $answer_container;
    }

    protected function ays_short_text_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input style='$enable_correction_textarea' type='text' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>".(__('Check', $this->plugin_name))."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'short_text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"]))
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";
        return $answer_container;
    }

    protected function ays_dropdown_answer_html($question_id, $quiz_id, $answers, $options){
        
        $answer_container = "<div class='ays-field ays-select-field'>            
            <select class='ays-select'>                
                <option value=''>".__('Select an answer', $this->plugin_name)."</option>";
        foreach ($answers as $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
//                    $answer_image = $this->ays_get_image_thumbnauil($answer['image']);
                $answer_image = $answer['image'];
            }

            $answer_container .= "<option data-nkar='{$answer_image}' data-chisht='{$answer["correct"]}' value='{$answer["id"]}'>" . do_shortcode(htmlspecialchars(stripslashes($answer["answer"]))) . "</option>";
        }
        $answer_container .= "</select>";
        $answer_container .= "<input class='ays-select-field-value' type='hidden' name='ays_questions[ays-question-{$question_id}]' value=''/>";

        foreach ($answers as $answer) {
            $answer_container .= "<input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>";
        }
        $answer_container .= "</div>";
        
        return $answer_container;
    }

    protected function isHomogenousStrong($arr, $question_id){
        $arr_count = count( $arr );
        $arr_sum = array_sum( $arr );
        $count_correct = intval( $this->count_multiple_correct_answers($question_id) );
        $a = $arr_count - $arr_sum;
        $b = $arr_sum - $a;
        
        return $b / $count_correct;
    }
    
    protected function isHomogenous($arr, $question_id){
        $mustBe = true;
        $count = 0;
        foreach ($arr as $val) {
            if ($mustBe !== $val) {
                return false;
            }
            $count++;
        }
        $count_correct = intval( $this->count_multiple_correct_answers($question_id) );
        if($count !== $count_correct){
            return false;
        }
        return true;
    }
    
    protected function get_question_weight($id){
        global $wpdb;
        $sql = "SELECT weight FROM {$wpdb->prefix}aysquiz_questions WHERE id = $id";
        $result = $wpdb->get_var($sql);
        return floatval($result);
    }

    protected function hex2rgba($color, $opacity = false){

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }else{
            return $color;
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }

    protected function secondsToWords($seconds){
        $ret = "";

        /*** get the days ***/
        $days = intval(intval($seconds) / (3600 * 24));
        if ($days > 0) {
            $ret .= "$days days ";
        }

        /*** get the hours ***/
        $hours = (intval($seconds) / 3600) % 24;
        if ($hours > 0) {
            $ret .= "$hours hours ";
        }

        /*** get the minutes ***/
        $minutes = (intval($seconds) / 60) % 60;
        if ($minutes > 0) {
            $ret .= "$minutes minutes ";
        }

        /*** get the seconds ***/
        $seconds = intval($seconds) % 60;
        if ($seconds > 0) {
            $ret .= "$seconds seconds";
        }

        return $ret;
    }

    protected function add_results_to_db($data){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';

        $user_ip = $data['user_ip'];
        $user_name = $data['user_name'];
        $user_email = $data['user_email'];
        $user_phone = $data['user_phone'];
        $quiz_id = $data['quiz_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $score = $data['score'];
        $options = $data['answered'];
        $calc_method = $data['calc_method'];
        $options['passed_time'] = $this->get_time_difference($start_date, $end_date);
        $options['user_points'] = $data['user_points'];
        $options['max_points'] = $data['max_points'];
        $duration = strtotime($end_date) - strtotime($start_date);
        $user_points = $data['user_points'];
        $max_points = $data['max_points'];
        $user_corrects_count = $data['user_corrects_count'];
        $questions_count = $data['questions_count'];
        
        $user_explanation = (count($data['user_explanation']) == 0) ?  '' : json_encode($data['user_explanation']);
        
        $quiz_attributes_information = array();
        $quiz_attributes = $this->get_quiz_attributes_by_id($quiz_id);

        foreach ($quiz_attributes as $attribute) {
            $quiz_attributes_information[strval($attribute->name)] = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
        }
        $options['attributes_information'] = $quiz_attributes_information;
        $options['calc_method'] = $calc_method;
        $results = $wpdb->insert(
            $results_table,
            array(
                'quiz_id' => absint(intval($quiz_id)),
                'user_id' => get_current_user_id(),
                'user_name' => $user_name,
                'user_email' => $user_email,
                'user_phone' => $user_phone,
                'user_ip' => $user_ip,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'score' => $score,
                'duration' => $duration,
                'points' => $user_points,
                'max_points' => $max_points,
                'corrects_count' => $user_corrects_count,
                'questions_count' => $questions_count,
                'user_explanation' => $user_explanation,
                'options' => json_encode($options)
            ),
            array(
                '%d', // quiz_id
                '%d', // user_id
                '%s', // user_name
                '%s', // user_email
                '%s', // user_phone
                '%s', // user_ip
                '%s', // start_date
                '%s', // end_date
                '%d', // score
                '%s', // duration
                '%s', // user_points
                '%s', // max_points
                '%s', // user_corrects_count
                '%s', // questions_count
                '%s', // user_explanation
                '%s', // options
            )
        );
//        $wpdb->show_errors(true);
//        var_dump($wpdb->last_error);
//        var_dump($results);
//        die();
        if ($results >= 0) {
            return true;
        }

        return false;
    }
    
    protected function ays_get_count_of_rates($id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }
    
    protected function ays_get_count_of_reviews($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE (review<>'' OR options<>'') AND quiz_id = $quiz_id ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_var($sql);
        return $result;
    }
    
    protected function ays_set_rate_id_of_result($id){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT MAX(id) AS max_id FROM $results_table WHERE end_date = ( SELECT MAX(end_date) FROM $results_table )";
        $res = $wpdb->get_results($sql, ARRAY_A);
        $sql = "SELECT * FROM $results_table WHERE id = ".intval($res[0]['max_id']);
        $report_result = $wpdb->get_row($sql, ARRAY_A);
        
        $options = json_decode($report_result['options'], true);
        $options['rate_id'] = $id;
        $results = $wpdb->update(
            $results_table,
            array( 'options' => json_encode($options) ),
            array( 'id' => intval($res[0]['max_id'])),
            array( '%s' ),
            array( '%d' )
        );
        if($results !== false){
            return true;
        }
        return false;
    }
    
    protected function ays_get_average_of_scores($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id= $id";
        $result = round($wpdb->get_var($sql));
        return $result;
    }
    
    protected function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }
    
    protected function ays_get_reasons_of_rates($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id=$quiz_id AND (review<>'' OR options<>'') ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_results($sql, "ARRAY_A");
        return $result;
    }
    
    protected function ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga){
        $quiz_rate_reasons = $this->ays_get_reasons_of_rates($start, $limit, $quiz_id);
        $quiz_rate_html = "";
        foreach($quiz_rate_reasons as $key => $reasons){
            $user_name = !empty($reasons['user_name']) ? "<span>".$reasons['user_name']."</span>" : '';            
            $reason = $reasons['review'];
            if(intval($reasons['user_id']) != 0){
                $user_img = esc_url( get_avatar_url( intval($reasons['user_id']) ) );
            }else{
                $user_img = "https://ssl.gstatic.com/accounts/ui/avatar_2x.png";
            }
            $score = $reasons['score'];
            $commented = date('M j, Y', strtotime($reasons['rate_date']));
            if($zuyga == 1){
                $row_reverse = ($key % 2 == 0) ? 'row_reverse' : '';
            }else{
                $row_reverse = ($key % 2 == 0) ? '' : 'row_reverse';
            }
            $quiz_rate_html .= "<div class='quiz_rate_reasons'>
                  <div class='rate_comment_row $row_reverse'>
                    <div class='rate_comment_user'>
                        <div class='thumbnail'>
                            <img class='img-responsive user-photo' src='".$user_img."'>
                        </div>
                    </div>
                    <div class='rate_comment'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'>
                                <i class='ays_fa ays_fa_user'></i> <strong>$user_name</strong><br/>
                                <i class='ays_fa ays_fa_clock_o'></i> $commented<br/>
                                ".__("Rated", $this->plugin_name)." <i class='ays_fa ays_fa_star'></i> $score
                            </div>
                            <div class='panel-body'><div>". stripslashes($reason) ."</div></div>
                        </div>
                    </div>
                </div>
            </div>";
        }
        return $quiz_rate_html;
    }
    
    public function ays_get_rate_last_reviews(){
        error_reporting(0);
        $quiz_id = absint(intval($_REQUEST["quiz_id"]));
        $quiz_rate_html = "<div class='quiz_rate_reasons_container'>";
        $quiz_rate_html .= $this->ays_get_full_reasons_of_rates(0, 5, $quiz_id, 0);
        $quiz_rate_html .= "</div>";
        if($this->ays_get_count_of_reviews(0, 5, $quiz_id) / 5 > 1){
            $quiz_rate_html .= "<div class='quiz_rate_load_more'>
                <div>
                    <div data-class='lds-spinner' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                </div>
                <button type='button' zuyga='1' startfrom='5' class='action-button ays_load_more_review'><i class='ays_fa ays_fa_chevron_circle_down'></i> ".__("Load more", $this->plugin_name)."</button>
            </div>";
        }
        echo $quiz_rate_html;
        wp_die();
    }
    
    public function ays_load_more_reviews(){
        error_reporting(0);
        $quiz_id = absint(intval($_REQUEST["quiz_id"]));
        $start = absint(intval($_REQUEST["start_from"]));
        $zuyga = absint(intval($_REQUEST["zuyga"]));
        $limit = 5;
        $quiz_rate_html = "";
        $quiz_rate_html .= $this->ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga);
        if($quiz_rate_html == ""){
            echo "<p class='ays_no_more'>" . __( "No more reviews", $this->plugin_name ) . "</p>";
            wp_die();
        }else{            
            $quiz_rate_html = "<div class='quiz_rate_more_review'>".$quiz_rate_html."</div>";            
            echo $quiz_rate_html;
            wp_die();
        }
    }
    
    public function ays_rate_the_quiz(){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $rates_table = $wpdb->prefix . 'aysquiz_rates';
//        $sql = "";
//        $res = $wpdb->get_results($sql, ARRAY_A);
        $sql = "SELECT * FROM $results_table WHERE id = (SELECT MAX(id) AS max_id FROM $results_table WHERE end_date = ( SELECT MAX(end_date) FROM $results_table ))";//.intval($res[0]['max_id']);
        $report_result = $wpdb->get_row($sql, ARRAY_A);
        $report_id = intval($report_result['id']);
        $user_ip = $this->get_user_ip();
        if(isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != ''){
            $user_name = $_REQUEST['ays_user_name'];
        }elseif(is_user_logged_in()){
            $user = wp_get_current_user();
            $user_name = $user->data->display_name;
        }else{
            $user_name = 'Anonymous';
        }
        $user_email = isset($_REQUEST['ays_user_email']) ? $_REQUEST['ays_user_email'] : '';
        $user_phone = isset($_REQUEST['ays_user_phone']) ? $_REQUEST['ays_user_phone'] : '';
        $quiz_id = absint(intval($_REQUEST["quiz_id"]));
        $score = $_REQUEST['rate_score'];
        $rate_date = esc_sql($_REQUEST['rate_date']);

        $results = $wpdb->insert(
            $rates_table,
            array(
                'quiz_id' => $quiz_id,
                'user_id' => get_current_user_id(),
                'report_id' => $report_id,
                'user_ip' => $user_ip,
                'user_name' => $user_name,
                'user_email' => $user_email,
                'user_phone' => $user_phone,
                'score' => $score,
                'review' => isset($_REQUEST['rate_reason']) ? stripcslashes($_REQUEST['rate_reason']) : '',
                'options' => '',
                'rate_date' => $rate_date,
            ),
            array(
                '%d', //quiz_id
                '%d', //user_id
                '%d', //report_id
                '%s', //user_ip
                '%s', //user_name
                '%s', //user_email
                '%s', //user_phone
                '%s', //score
                '%s', //review
                '%s', //options
                '%s'  //rate_date
            )
        );
        $rate_id = $wpdb->insert_id;
        $avg_score = $this->ays_get_average_of_rates($quiz_id);
        if ($results >= 0 && $this->ays_set_rate_id_of_result($rate_id)) {
            echo json_encode(array(
//                'rate_id'   => $rate_id,
//                'result' => $this->ays_set_rate_id_of_result($rate_id),
                'quiz_id'   => $quiz_id,
                'status'    => true,
                'avg_score' => round($avg_score, 1),
                'score'     => intval(round($avg_score)),
                'rates_count'     => $this->ays_get_count_of_rates($quiz_id),
            ));
            wp_die();
        }
        echo json_encode(array(
            'status'    => false,
        ));
        wp_die();
    }

    protected function get_user_by_ip($id){
        global $wpdb;
        $user_ip = $this->get_user_ip();
        $sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}aysquiz_reports` WHERE `user_ip` = '$user_ip' AND `quiz_id` = $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    protected function get_limit_user_by_id($quiz_id, $user_id){
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}aysquiz_reports` WHERE `user_id` = '$user_id' AND `quiz_id` = $quiz_id";
        $result = intval($wpdb->get_var($sql));
        return $result;
    }

    protected function get_user_ip(){
        $ipaddress = '';
        if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        elseif (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    protected function get_time_difference($strStart, $strEnd){
        $dteStart = new DateTime($strStart);
        $dteEnd = new DateTime($strEnd);
        $texts = array(
            'year' => __( "year", $this->plugin_name ),
            'years' => __( "years", $this->plugin_name ),
            'month' => __( "month", $this->plugin_name ),
            'months' => __( "months", $this->plugin_name ),
            'day' => __( "day", $this->plugin_name ),
            'days' => __( "days", $this->plugin_name ),
            'hour' => __( "hour", $this->plugin_name ),
            'hours' => __( "hours", $this->plugin_name ),
            'minute' => __( "minute", $this->plugin_name ),
            'minutes' => __( "minutes", $this->plugin_name ),
            'second' => __( "second", $this->plugin_name ),
            'seconds' => __( "seconds", $this->plugin_name ),
        );
        $interval = $dteStart->diff($dteEnd);
        $return = '';

        if ($v = $interval->y >= 1) $return .= $interval->y ." ". $texts[$this->pluralize_new($interval->y, 'year')] . ' ';
        if ($v = $interval->m >= 1) $return .= $interval->m ." ". $texts[$this->pluralize_new($interval->m, 'month')] . ' ';
        if ($v = $interval->d >= 1) $return .= $interval->d ." ". $texts[$this->pluralize_new($interval->d, 'day')] . ' ';
        if ($v = $interval->h >= 1) $return .= $interval->h ." ". $texts[$this->pluralize_new($interval->h, 'hour')] . ' ';
        if ($v = $interval->i >= 1) $return .= $interval->i ." ". $texts[$this->pluralize_new($interval->i, 'minute')] . ' ';

        $return .= $interval->s ." ". $texts[$this->pluralize_new($interval->s, 'second')];

        return $return;
    }
    
    protected function pluralize($count, $text){
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }
    
    protected function pluralize_new($count, $text){
        return ($count == 1) ? $text."" : $text."s";
    }

    public function ays_get_user_information() {        
        if(is_user_logged_in()) {
            $output = json_encode(wp_get_current_user());
        } else {
            $output = null;
        }
        echo $output;
        wp_die();
    }
    
    // Mailchimp
    public function ays_add_mailchimp_transaction($username, $api_key, $list_id, $args){
        if($username == "" || $api_key == ""){
            return false;
        }
        
        $email = isset($args['email']) ? $args['email'] : null;
        $fname = isset($args['fname']) ? $args['fname'] : "";
        $lname = isset($args['lname']) ? $args['lname'] : "";
        
        $api_prefix = explode("-",$api_key)[1];
        
        $fields = array(
            "email_address" => $email,
            "status" => "subscribed",
            "merge_fields" => array(
                "FNAME" => $fname,
                "LNAME" => $lname
            )
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/".$list_id."/members/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERPWD => "$username:$api_key",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }

    // Campaign Monitor
    public function ays_add_monitor_transaction( $client, $api_key, $list_id, $args ) {
        if ($client == "" || $api_key == "") {
            return false;
        }

        $default_options = array(
            "CustomFields" => array(
                array(
                    "Key"   => "from",
                    "Value" => $this->plugin_name
                ),
                array(
                    "Key"   => "date",
                    "Value" => date("Y/m/d", current_time('timestamp'))
                )
            ),

            "Resubscribe"                            => true,
            "RestartSubscriptionBasedAutoresponders" => true,
            "ConsentToTrack"                         => "Yes"
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => "https://api.createsend.com/api/v3.2/subscribers/$list_id.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERPWD        => "$api_key:x",
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode(array_merge($args, $default_options)),
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }

    // ActiveCampaign
    public function ays_add_active_camp_transaction( $url, $api_key, $args, $list_id, $automation_id, $data = "contact" ) {
        if ($url == "" || $api_key == "") {
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => "$url/api/3/{$data}s",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode(array("$data" => $args)),
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "cache-control: no-cache",
                "Api-Token: $api_key"
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        $res = $err ? array(
            'Code'       => 0,
            'cURL Error' => $err
        ) : json_decode($response, true)["$data"];

        if ($data == "contactList" || $data == "contactAutomation") {
            return $res;
        } else {
            if ($list_id) {
                $list_args = array(
                    "list"    => $list_id,
                    "contact" => $res['id'],
                    "status"  => 1
                );

                return $this->ays_add_active_camp_transaction($url, $api_key, $list_args, $list_id, $automation_id, 'contactList');
            }
            if ($automation_id) {
                $automation_args = array(
                    "automation" => $automation_id,
                    "contact"    => $res['id']
                );

                return $this->ays_add_active_camp_transaction($url, $api_key, $automation_args, $list_id, $automation_id, 'contactAutomation');
            }

            return $res;
        }

    }

    // Zapier
    public function ays_add_zapier_transaction( $hook, $data ) {
        if ($hook == "") {
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $hook,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode(array("AysQuiz" => $data)),
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }

    // Slack
    public function ays_add_slack_transaction( $token, $channel, $data, $quiz = "", $score = 0 ) {
        if ($token == "" || $channel == "") {
            return false;
        }
        global $wpdb;
        
        $text           = __("Your `" . stripslashes($quiz) . "` Quiz was survived by", $this->plugin_name) . "\n";
        foreach ( $data as $key => $value ) {
            if($value == ""){
                continue;
            }
            $text .= __(ucfirst($key) . ":", $this->plugin_name) . " `$value`\n";
        }
        $text .= __("Score:", $this->plugin_name) . " `" . $score . '%`';
        $args = array(
            "channel"  => $channel,
            "text"     => $text,
            "username" => "Ays QuizMaker"
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => "https://slack.com/api/chat.postMessage",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode($args),
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "Authorization: Bearer $token",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }
    

    /* 
    ========================================== 
        Shortcodes
    ========================================== 
    */
    
    // User page shortcode
    public function get_user_reports_info(){
        global $wpdb;

        $current_user = wp_get_current_user();
        $id = $current_user->ID;
        if($id == 0){
            return null;
        }
        
        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $sql = "SELECT q.title, r.start_date, r.end_date, r.score, r.id
                FROM $reports_table AS r 
                LEFT JOIN $quizes_table AS q 
                ON r.quiz_id = q.id 
                WHERE r.user_id=$id
                ORDER BY r.id DESC";
        $results = $wpdb->get_results($sql, "ARRAY_A");

        return $results;

    }

    public function ays_user_page_html(){
        
        $results = $this->get_user_reports_info();
        wp_enqueue_style($this->plugin_name.'-animate', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_script( $this->plugin_name . '-user-page-public', plugin_dir_url(__FILE__) . 'js/user-page/user-page-public.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-user-page-public', 'quiz_maker_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));

        
        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode($quiz_settings_options, true);
        $quiz_set_option['ays_show_result_report'] = !isset($quiz_set_option['ays_show_result_report']) ? 'on' : $quiz_set_option['ays_show_result_report'];
        $show_result_report = isset($quiz_set_option['ays_show_result_report']) && $quiz_set_option['ays_show_result_report'] == 'on' ? true : false;

        if($results === null){
            $user_page_html = "<p style='text-align: center;font-style:italic;'>" . __( "You must log in to see your results.", $this->plugin_name ) . "</p>";
            return $user_page_html;
        }

        $user_page_html = "<div class='ays-quiz-user-results-container'>
        <table id='ays-quiz-user-score-page' class='display'>
            <thead>
                <tr>
                    <th>" . __( "Quiz", $this->plugin_name ) . "</th>
                    <th style='width:17%;'>" . __( "Start", $this->plugin_name ) . "</th>
                    <th style='width:17%;'>" . __( "End", $this->plugin_name ) . "</th>
                    <th style='width:80px;'>" . __( "Score", $this->plugin_name ) . "</th>";
        
        if ($show_result_report) {
           $user_page_html .= "<th style='width:120px;'></th>";
        }
        $user_page_html .= "</tr></thead>";
        foreach($results as $result){
            $user_page_html .= "<tr>";
            $id = isset($result['id']) ? $result['id'] : null;
            $title = isset($result['title']) ? $result['title'] : "";
            $start_date = date_create($result['start_date']);
            $start_date = date_format($start_date, 'H:i:s M d, Y');
            $end_date = date_create($result['end_date']);
            $end_date = date_format($end_date, 'H:i:s M d, Y');
            $score = isset($result['score']) ? $result['score'] : 0;
            $user_page_html .= "<td>$title</td>";
            $user_page_html .= "<td>$start_date</td>";
            $user_page_html .= "<td>$end_date</td>";
            $user_page_html .= "<td class='ays-quiz-score-column'>$score%</td>";
            if ($show_result_report) {
                $user_page_html .= "<td><button type='button' data-id='".$id."' class='ays-quiz-user-sqore-pages-details'>".__("Details", $this->plugin_name)."</button></td>";
            }
            $user_page_html .= "</tr>";
        }
        $user_page_html .= "</table>
            </div>
            <div id='ays-results-modal' class='ays-modal'>
                <div class='ays-modal-content'>
                    <div class='ays-quiz-preloader'>
                        <img class='loader' src='". AYS_QUIZ_ADMIN_URL."/images/loaders/3-1.svg'>
                    </div>
                    <div class='ays-modal-header'>
                        <span class='ays-close' id='ays-close-results'>&times;</span>
                    </div>
                    <div class='ays-modal-body' id='ays-results-body'></div>
                </div>
            </div>";
        
        return $user_page_html;
    }
    
    protected function ays_quiz_rate( $id ) {
        global $wpdb;
        if($id === '' || $id === null){
            $reason = __("No rate provided", $this->plugin_name);
            $output = array(
                "review" => $reason,
            );
        }else{
            $rate = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$id}", "ARRAY_A");
            $output = array();
            if($rate !== null){
                $review = $rate['review'];
                $reason = stripslashes($review);
                if($reason == ''){
                    $reason = __("No review provided", $this->plugin_name);
                }
                $score = $rate['score'];
                $output = array(
                    "score" => $score,
                    "review" => $reason,
                );
            }else{
                $reason = __("No rate provided", $this->plugin_name);
                $output = array(
                    "review" => $reason,
                );
            }
        }
        return $output;
    }

    public function question_is_text_type($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'number', 'short_text');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public function user_reports_info_popup_ajax(){
        global $wpdb;
        error_reporting(0);
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'user_reports_info_popup_ajax') {
            $id = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id}", "ARRAY_A");
            $user_id = intval($results['user_id']);
            $quiz_id = intval($results['quiz_id']);
            $user = get_user_by('id', $user_id);

            $user_ip = $results['user_ip'];
            $options = json_decode($results['options']);
            $user_attributes = $options->attributes_information;
            $start_date = $results['start_date'];
            $duration = $options->passed_time;
            $rate_id = isset($options->rate_id) ? $options->rate_id : null;
            $rate = $this->ays_quiz_rate($rate_id);
            $calc_method = isset($options->calc_method) ? $options->calc_method : 'by_correctness';

            $json = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            $country = $json->country;
            $region = $json->region;
            $city = $json->city;
            $from = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;

            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';
            $score = $calc_method == 'by_points' ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';

            $row = "<table id='ays-results-table'>";

            $row .= '<tr class="ays_result_element">
                    <td colspan="4"><h1>' . __('Quiz Information',$this->plugin_name) . '</h1></td>
                </tr>';
            if(isset($rate['score'])){
                $rate_html = '<tr style="vertical-align: top;" class="ays_result_element">
                <td>'.__('Rate',$this->plugin_name).'</td>
                <td>'. __("Rate Score", $this->plugin_name).":<br>" . $rate['score'] . '</td>
                <td colspan="2" style="max-width: 200px;">'. __("Review", $this->plugin_name).":<br>" . $rate['review'] . '</td>
            </tr>';
            }else{
                $rate_html = '<tr class="ays_result_element">
                <td>'.__('Rate',$this->plugin_name).'</td>
                <td colspan="3">' . $rate['review'] . '</td>
            </tr>';
            }
            $row .= '<tr class="ays_result_element">
                    <td>'.__('Start date',$this->plugin_name).'</td>
                    <td colspan="3">' . $start_date . '</td>
                </tr>                        
                <tr class="ays_result_element">
                    <td>'.__('Duration',$this->plugin_name).'</td>
                    <td colspan="3">' . $duration . '</td>
                </tr>
                <tr class="ays_result_element">
                    <td>'.__('Score',$this->plugin_name).'</td>
                    <td colspan="3">' . $score . '</td>
                </tr>'.$rate_html;


            $row .= '<tr class="ays_result_element">
                    <td colspan="4"><h1>' . __('Questions',$this->plugin_name) . '</h1></td>
                </tr>';

            $index = 1;
            $user_exp = array();
            if($results['user_explanation'] != '' || $results['user_explanation'] !== null){
                $user_exp = json_decode($results['user_explanation'], true);
            }

            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id = absint(intval(explode('_', $key)[2]));
                    $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                    $correct_answers = $this->get_correct_answers($question_id);
                    $correct_answer_images = $this->get_correct_answer_images($question_id);

                    if($this->question_is_text_type($question_id)){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                        $user_answered_images = '';
                    }else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                        $user_answered_images = $this->get_user_answered_images($options->user_answered, $key);
                    }
                    $ans_point = $option;
                    $ans_point_class = 'success';
                    if(is_array($user_answered)){
                        $user_answered = $user_answered['message'];
                        $ans_point = '-';
                        $ans_point_class = 'error';
                    }
                    $tr_class = "ays_result_element";
                    if(isset($user_exp[$question_id])){
                        $tr_class = "";
                    }
                    if($calc_method == 'by_correctness'){
                        if ($option == true) {
                            $row .= '<tr class="'.$tr_class.'">
                                    <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                                    <td>'.__('Correct answer',$this->plugin_name).':<br/><p class="success">' . htmlentities(do_shortcode(stripslashes($correct_answers))) . '<br>'.$correct_answer_images.'</p></td>
                                    <td>'.__('User answered',$this->plugin_name).':<br/><p class="success">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '<br>'.$user_answered_images.'</p></td>
                                    <td>
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                            <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                        </svg>
                                        <p class="success">'.__('Succeed',$this->plugin_name).'!</p>
                                    </td>
                                </tr>';
                        } else {
                            $row .= '<tr class="'.$tr_class.'">
                                    <td>'.__('Question',$this->plugin_name).'' . $index . ' :<br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                                    <td>'.__('Correct answer',$this->plugin_name).':<br/><p class="success">' . htmlentities(do_shortcode(stripslashes($correct_answers))) . '<br>'.$correct_answer_images.'</p></td>
                                    <td>'.__('User answered',$this->plugin_name).':<br/><p class="error">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '<br>'.$user_answered_images.'</p></td>
                                    <td>
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                                        </svg>
                                        <p class="error">'.__('Failed',$this->plugin_name).'!</p>
                                    </td>
                                </tr>';
                        }
                    }elseif($calc_method == 'by_points'){
                        $row .= '<tr class="'.$tr_class.'">
                                <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                                <td>'.__('User answered',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '<br>'.$user_answered_images.'</p></td>
                                <td>'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . htmlentities($ans_point) . '</p></td>
                            </tr>';

                    }
                    $index++;
                    if(isset($user_exp[$question_id])){
                        $row .= '<tr class="ays_result_element">
                        <td>'.__('User explanation for this question',$this->plugin_name).'</td>
                        <td colspan="3">'.$user_exp[$question_id].'</td>
                    </tr>';
                    }
                }
            }
            $row .= "</table>";
            echo json_encode(array(
                "status" => true,
                "rows" => $row
            ));
            wp_die();
        }
    }
    
    public function ays_generate_user_page_method(){
        
        $user_page_html = $this->ays_user_page_html();

        return $user_page_html;        
    }
    
    // Leaderboard shortcode    
    public function ays_generate_leaderboard_list($attr){ 
        // AV Leaderboard
        ob_start();

        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $leadboard = json_decode($leadboard_res, true);
         
        $ind_leadboard_count = isset($leadboard['individual']['count']) ? $leadboard['individual']['count'] : '5' ;
        $ind_leadboard_width = isset($leadboard['individual']['width']) ? $leadboard['individual']['width'] : '0' ;
        $ind_leadboard_width = intval($ind_leadboard_width) == 0 ? '100%' : $ind_leadboard_width ."px";
        $ind_leadboard_orderby = isset($leadboard['individual']['orderby']) ? $leadboard['individual']['orderby'] : 'id' ;
        $ind_leadboard_sort = isset($leadboard['individual']['sort']) ? $leadboard['individual']['sort'] : 'avg' ;
        $ind_leadboard_color = isset($leadboard['individual']['color']) ? $leadboard['individual']['color'] : '#99BB5A' ;
        
        global $wpdb;
        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE id =".$id;
        $x = intval($wpdb->get_var($sql));
        $duration_avg = $ind_leadboard_sort == 'avg' ? strtoupper($ind_leadboard_sort) : '';
        if ($x === 0) {
            return '[ays_quiz_leaderboard id="'.$id.'"]';
        }else{
            if($ind_leadboard_orderby == 'id'){
                if($ind_leadboard_sort == 'avg'){
                    $sql = "SELECT quiz_id, user_id, ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg, ".strtoupper($ind_leadboard_sort)."(CAST(score AS DECIMAL(10))) AS avg_score
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND user_id != 0
                            GROUP BY user_id
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.duration AS dur_avg, a.user_name, a.options
                            FROM (
                                    SELECT user_id as ue, ".strtoupper($ind_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND user_id != 0
                                    GROUP BY ue
                                 ) AS e
                            JOIN (
                                    SELECT user_id, user_name, CAST(`score` AS DECIMAL(10,0)) AS score, CAST(`duration` AS DECIMAL(10,0)) AS duration, options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                 ) AS a
                            ON e.ue = a.user_id AND e.new_score = a.score
                            ORDER BY e.new_score DESC, a.duration
                            LIMIT ".$ind_leadboard_count;
                }
            }elseif($ind_leadboard_orderby == 'email'){
                if($ind_leadboard_sort == 'avg'){
                    $sql = "SELECT user_id, user_name, ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg, ".strtoupper($ind_leadboard_sort)."(CAST(score AS DECIMAL(10))) AS avg_score
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND !(user_email='' OR user_email IS NULL)
                            GROUP BY user_email
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.duration AS dur_avg, a.user_id, a.user_name, a.options
                            FROM (
                                    SELECT user_email as ue, ".strtoupper($ind_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND !(user_email='' OR user_email IS NULL)
                                    GROUP BY ue
                                 ) AS e
                            JOIN (
                                    SELECT user_email, user_id, user_name, CAST(`score` AS DECIMAL(10,0)) AS score, CAST(`duration` AS DECIMAL(10,0)) AS duration, options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                 ) AS a
                            ON e.ue = a.user_email AND e.new_score = a.score
                            ORDER BY e.new_score DESC, a.duration
                            LIMIT ".$ind_leadboard_count;
                }
            }
            
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            if (!empty($result)) {        
                $c = 1;
                $content = "<div class='ays_lb_container'>
                <ul class='ays_lb_ul' style='width: ".$ind_leadboard_width.";'>
                    <li class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>
                        <div class='ays_lb_pos'>Pos.</div>
                        <div class='ays_lb_user'>".__("Name", $this->plugin_name)."</div>
                        <div class='ays_lb_score'>".__("Score", $this->plugin_name)."</div>
                        <div class='ays_lb_duration'>".__("Duration", $this->plugin_name)."</div>
                    </li>";

                foreach ($result as $val) {
                    $score = round($val['avg_score'], 2);
                    $user_id = intval($val['user_id']);
                    if ($user_id == 0) {
                        $user_name = isset($val['user_name']) && $val['user_name']!= '' ? $val['user_name'] : __('Guest', $this->plugin_name);
                    }else{
                        $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : '';
                        if($user_name == ''){
                            $user = get_user_by('id', $user_id);
                            $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                        }
                    }
                    $duration = (isset($val['dur_avg']) && $val['dur_avg'] != '') ? round(floatval($val['dur_avg']), 2) : '0';

                    $content .= "<li class='ays_lb_li'>
                                    <div class='ays_lb_pos'>".$c.".</div>
                                    <div class='ays_lb_user'>".$user_name."</div>
                                    <div class='ays_lb_score'>".$score." %</div>
                                    <div class='ays_lb_duration'>".$duration."s</div>
                                </li>";
                    $c++;   
                }
                $content .= "</ul>
                </div>";
                echo $content;
                return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
            }else{
                $content = "<div class='ays_lb_container'>
                    <ul class='ays_lb_ul' style='width: ".$ind_leadboard_width."px;'>
                        <li class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>
                            <div class='ays_lb_pos'>Pos.</div>
                            <div class='ays_lb_user'>".__("Name", $this->plugin_name)."</div>
                            <div class='ays_lb_score'>".__("Score", $this->plugin_name)."</div>
                            <div class='ays_lb_duration'>".__("Duration", $this->plugin_name)."</div>
                        </li>
                        <li class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</li>
                    </ul>    
                </div>";
                echo $content;                    
                return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
            }
        }
        echo $content;
        return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());   
    }

    public function ays_generate_gleaderboard_list($attr){
        ob_start();
        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $leadboard = json_decode($leadboard_res, true);
        $glob_leadboard_count = isset($leadboard['global']['count']) ? $leadboard['global']['count'] : '5' ;
        $glob_leadboard_width = isset($leadboard['global']['width']) ? $leadboard['global']['width'] : '0' ;
        $glob_leadboard_width = intval($glob_leadboard_width) == 0 ? '100%' : $glob_leadboard_width ."px";
        $glob_leadboard_orderby = isset($leadboard['global']['orderby']) ? $leadboard['global']['orderby'] : 'id' ;
        $glob_leadboard_sort = isset($leadboard['global']['sort']) ? $leadboard['global']['sort'] : 'avg' ;
        $glob_leadboard_color = isset($leadboard['global']['color']) ? $leadboard['global']['color'] : '#99BB5A' ;
        global $wpdb; 
        $duration_avg = $glob_leadboard_sort == 'avg' ? strtoupper($glob_leadboard_sort) : '';
        
        if($glob_leadboard_orderby == 'id'){
            if($glob_leadboard_sort == 'avg'){
                $sql = "SELECT quiz_id, user_id, ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE user_id != 0
                    GROUP BY user_id
                    ORDER BY avg_score DESC, dur_avg
                    LIMIT ".$glob_leadboard_count;
            }else{
                $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.duration AS dur_avg, a.user_name, a.options
                        FROM (
                                SELECT user_id as ue, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                FROM {$wpdb->prefix}aysquiz_reports
                                WHERE user_id != 0
                                GROUP BY ue
                             ) AS e
                        JOIN (
                                SELECT user_id, user_name, CAST(`score` AS DECIMAL(10,0)) AS score, CAST(`duration` AS DECIMAL(10,0)) AS duration, options
                                FROM {$wpdb->prefix}aysquiz_reports
                             ) AS a
                        ON e.ue = a.user_id AND e.new_score = a.score
                        ORDER BY e.new_score DESC, a.duration
                        LIMIT ".$glob_leadboard_count;
            }
        }elseif($glob_leadboard_orderby == 'email'){
            if($glob_leadboard_sort == 'avg'){
                $sql = "SELECT user_id, user_name, user_email, ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg, options, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score
                        FROM {$wpdb->prefix}aysquiz_reports
                        WHERE !(user_email='' OR user_email IS NULL)
                        GROUP BY user_email
                        ORDER BY avg_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }else{
                $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.duration AS dur_avg, a.user_id, a.user_name, a.options
                        FROM (
                                SELECT user_email as ue, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                FROM {$wpdb->prefix}aysquiz_reports
                                WHERE !(user_email='' OR user_email IS NULL)
                                GROUP BY ue
                             ) AS e
                        JOIN (
                                SELECT user_email, user_id, user_name, CAST(`score` AS DECIMAL(10,0)) AS score, CAST(`duration` AS DECIMAL(10,0)) AS duration, options
                                FROM {$wpdb->prefix}aysquiz_reports
                             ) AS a
                        ON e.ue = a.user_email AND e.new_score = a.score
                        ORDER BY e.new_score DESC, a.duration
                        LIMIT ".$glob_leadboard_count;
            }
        }
        
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        
        $c = 1;
        $content = "<div class='ays_lb_container'>
        <ul class='ays_lb_ul' style='width: ".$glob_leadboard_width.";'>
            <li class='ays_lb_li' style='background: ".$glob_leadboard_color.";'>
                <div class='ays_lb_pos'>Pos.</div>
                <div class='ays_lb_user'>".__("Name", $this->plugin_name)."</div>
                <div class='ays_lb_score'>".__("Score", $this->plugin_name)."</div>
                <div class='ays_lb_duration'>".__("Duration", $this->plugin_name)."</div>
            </li>";

        foreach ($result as $val) {
            $score = round($val['avg_score'], 2);
            $user_id = intval($val['user_id']);
            if ($user_id == 0) {
                $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : __('Guest', $this->plugin_name);
            }else{
                $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : '';
                if($user_name == ''){
                    $user = get_user_by('id', $user_id);
                    $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                }
            }

            $duration = (isset($val['dur_avg']) && $val['dur_avg'] != '') ? round(floatval($val['dur_avg']), 2) : '0';
            $content .= "<li class='ays_lb_li'>
                            <div class='ays_lb_pos'>".$c.".</div>
                            <div class='ays_lb_user'>".$user_name."</div>
                            <div class='ays_lb_score'>".$score." %</div>
                            <div class='ays_lb_duration'>".$duration."s</div>
                        </li>";
            $c++;   
        }
        $content .= "</ul>
        </div>";

        echo $content;
        
        return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());   
    }

}
