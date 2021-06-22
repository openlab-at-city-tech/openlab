<?php

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
class Quiz_Maker_Public {

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

    protected $buttons_texts;

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

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $this->settings );
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
        wp_enqueue_style($this->plugin_name.'-font-awesome', plugin_dir_url(__FILE__) . 'css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-sweetalert-css', plugin_dir_url(__FILE__) . 'css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all' );
        wp_enqueue_style($this->plugin_name.'-animate', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-animations', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-rating', plugin_dir_url(__FILE__) . 'css/rating.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-select2', plugin_dir_url(__FILE__) . 'css/quiz-maker-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-loaders', plugin_dir_url(__FILE__) . 'css/loaders.css', array(), $this->version, 'all');

    }

    public function enqueue_styles_early(){
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');

        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();
        if ( $is_elementor_exists ) {
            wp_enqueue_style($this->plugin_name.'-font-awesome', plugin_dir_url(__FILE__) . 'css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name.'-rating', plugin_dir_url(__FILE__) . 'css/rating.min.css', array(), $this->version, 'all');
            wp_enqueue_script( $this->plugin_name . '-rate-quiz', plugin_dir_url(__FILE__) . 'js/rating.min.js', array('jquery'), $this->version, true );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(){

        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();

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

        if ( ! $is_elementor_exists ) {
            wp_enqueue_script("jquery-effects-core");
            wp_enqueue_script( $this->plugin_name . '-select2js', plugin_dir_url(__FILE__) . 'js/quiz-maker-select2.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-sweetalert-js', plugin_dir_url(__FILE__) . 'js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-rate-quiz', plugin_dir_url(__FILE__) . 'js/rating.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-functions.js', plugin_dir_url(__FILE__) . 'js/quiz-maker-functions.js', array('jquery'), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-ajax-public', plugin_dir_url(__FILE__) . 'js/quiz-maker-public-ajax.js', array('jquery'), time(), true );
            wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-public.js', array('jquery'), time(), true );

            wp_localize_script( $this->plugin_name . '-ajax-public', 'quiz_maker_ajax_public', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'warningIcon' => plugin_dir_url(__FILE__) . "images/warning.svg",
            ));
            wp_localize_script( $this->plugin_name, 'quizLangObj', array(
                'notAnsweredText'       => __( 'You have not answered this question', $this->plugin_name ),
                'areYouSure'            => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
                'sorry'                 => __( 'Sorry', $this->plugin_name ),
                'unableStoreData'       => __( 'We are unable to store your data', $this->plugin_name ),
                'connectionLost'        => __( 'Connection is lost', $this->plugin_name ),
                'checkConnection'       => __( 'Please check your connection and try again', $this->plugin_name ),
                'selectPlaceholder'     => __( 'Select an answer', $this->plugin_name ),
                'shareDialog'           => __( 'Share Dialog', $this->plugin_name ),
                'passwordIsWrong'       => __( 'Password is wrong!', $this->plugin_name ),
                'expiredMessage'        => __( 'The quiz has expired!', $this->plugin_name ),
                'day'                   => __( 'day', $this->plugin_name ),
                'days'                  => __( 'days', $this->plugin_name ),
                'hour'                  => __( 'hour', $this->plugin_name ),
                'hours'                 => __( 'hours', $this->plugin_name ),
                'minute'                => __( 'minute', $this->plugin_name ),
                'minutes'               => __( 'minutes', $this->plugin_name ),
                'second'                => __( 'second', $this->plugin_name ),
                'seconds'               => __( 'seconds', $this->plugin_name ),
                'alreadyPassedQuiz'     => __( 'You already passed this quiz.', $this->plugin_name ),
                'requiredError'         => __( 'This is a required question', $this->plugin_name ),
                'avg'                   => __( 'avg', $this->plugin_name ),
                'votes'                 => __( 'votes', $this->plugin_name ),
                'startButtonText'       => $this->buttons_texts['startButton'],
                'defaultStartButtonText'=> __( 'Start', $this->plugin_name ),
            ) );
        }
    }
    
    public function ays_generate_quiz_method($attr){
//        ob_start();
        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
        
        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }
        
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        $content = $this->show_quiz($id, $attr);
        return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
    }
    
    public function show_quiz($id, $attr){
        if( ! session_id() ){
            session_start();
        }
        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
        
        if (is_null($quiz)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return $content;
        }
        if (intval($quiz['published']) === 0) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return $content;
        }
        
        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();

        
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        $paypal_client_id = isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '' ? $quiz_integrations['paypal_client_id'] : null;
        $quiz_paypal = (isset($options['enable_paypal']) && $options['enable_paypal'] == "on") ? true : false;
        $quiz_paypal_message = (isset($options['paypal_message']) && $options['paypal_message'] != "") ? $options['paypal_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $quiz_paypal_message = stripslashes( wpautop( $quiz_paypal_message ) );
        

        // Stripe
        $quiz_settings = $this->settings;
        $stripe_res = ($quiz_settings->ays_get_setting('stripe') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('stripe');
        $stripe = json_decode($stripe_res, true);
        $stripe_secret_key = isset($stripe['secret_key']) ? $stripe['secret_key'] : '';
        $stripe_api_key = isset($stripe['api_key']) ? $stripe['api_key'] : '';
        $stripe_payment_terms = isset($stripe['payment_terms']) ? $stripe['payment_terms'] : 'lifetime';

        // Stripe parameters
        $options['enable_stripe'] = !isset( $options['enable_stripe'] ) ? 'off' : $options['enable_stripe'];
        $enable_stripe = ( isset($options['enable_stripe']) && $options['enable_stripe'] == 'on' ) ? true : false;
        $stripe_amount = (isset($options['stripe_amount'])) ? $options['stripe_amount'] : '';
        $stripe_currency = (isset($options['stripe_currency'])) ? $options['stripe_currency'] : '';
        $stripe_message = (isset($options['stripe_message'])) ? $options['stripe_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $stripe_message = stripslashes( wpautop( $stripe_message ) );

        $paypal_connection = null;
        if(is_user_logged_in()){
            if($payment_terms == "onetime"){
                if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                    if($_SESSION['ays_quiz_paypal_purchase'] == true){
                        $paypal_connection = false;
                    }else{
                        $paypal_connection = true;
                    }
                }else{
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                    $paypal_connection = true;
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
                            $paypal_connection = false;
                        }else{
                            $paypal_connection = true;
                        }
                    }else{
                        $opts = json_encode(array(
                            'quizId' => $id,
                            'purchased' => false
                        ));
                        add_user_meta($current_user->data->ID, "quiz_paypal_purchase", $opts);
                        $paypal_connection = true;
                    }
                }else{
                    $opts = json_encode(array(
                        'quizId' => $id,
                        'purchased' => false
                    ));
                    add_user_meta($current_user->data->ID, "quiz_paypal_purchase", $opts);
                    $paypal_connection = true;
                }
            }
        }else{
            if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                if($_SESSION['ays_quiz_paypal_purchase'] == true){
                    $attr['quiz_paypal'] = null;
                    $paypal_connection = false;
                }else{
                    $paypal_connection = true;
                }
            }else{
                $_SESSION['ays_quiz_paypal_purchase'] = false;
                $paypal_connection = true;
            }
        }

        if($quiz_paypal && $paypal_connection === true){
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
                $attr['quiz_paypal'] = '
                    <div class="ays_paypal_wrap_div">
                        <div class="ays_paypal_details_div">
                            '.$quiz_paypal_message.'
                        </div>
                        <div class="ays_paypal_div">
                            <div id="ays_quiz_paypal_button_container_'.$id.'"></div>
                        </div>
                    </div>
                    <script>
                        window.addEventListener("DOMContentLoaded", function() {
                            (function($){
                                $(document).ready(function(){
                                    aysQuizPayPal.Buttons({
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
                        });
                    </script>';
            }
        }else{
            $attr['quiz_paypal'] = null;
        }
        
        $stripe_connection = null;
        if(is_user_logged_in()){
            if($stripe_payment_terms == "onetime"){
                if(isset($_SESSION['ays_quiz_stripe_purchase'])){
                    if($_SESSION['ays_quiz_stripe_purchase'] == true){
                        $stripe_connection = false;
                    }else{
                        $stripe_connection = true;
                    }
                }else{
                    $_SESSION['ays_quiz_stripe_purchase'] = false;
                    $stripe_connection = true;
                }
            }elseif($stripe_payment_terms == "lifetime"){
                $current_user = wp_get_current_user();
                $current_usermeta = get_user_meta($current_user->data->ID, "quiz_stripe_purchase");
                if($current_usermeta !== false && !empty($current_usermeta)){
                    foreach($current_usermeta as $usermeta){
                        if($id == json_decode($usermeta, true)['quizId']){
                            $quiz_stripe_usermeta = json_decode($usermeta, true);
                            break;
                        }else{
                            $quiz_stripe_usermeta = false;
                        }
                    }
                    if($quiz_stripe_usermeta !== false){
                        if($quiz_stripe_usermeta['purchased'] == true){
                            $stripe_connection = false;
                        }else{
                            $stripe_connection = true;
                        }
                    }else{
                        $opts = json_encode(array(
                            'quizId' => $id,
                            'purchased' => false
                        ));
                        add_user_meta($current_user->data->ID, "quiz_stripe_purchase", $opts);
                        $stripe_connection = true;
                    }
                }else{
                    $opts = json_encode(array(
                        'quizId' => $id,
                        'purchased' => false
                    ));
                    add_user_meta($current_user->data->ID, "quiz_stripe_purchase", $opts);
                    $stripe_connection = true;
                }
            }
        }else{
            if(isset($_SESSION['ays_quiz_stripe_purchase'])){
                if($_SESSION['ays_quiz_stripe_purchase'] == true){
                    $stripe_connection = false;
                }else{
                    $stripe_connection = true;
                }
            }else{
                $_SESSION['ays_quiz_stripe_purchase'] = false;
                $stripe_connection = true;
            }
        }

        if($enable_stripe && $stripe_connection === true){
            if($stripe_secret_key == '' || $stripe_api_key == ''){
                $attr['quiz_stripe'] = null;
            }else{
                $enqueue_stripe_scripts = true;
                if( !is_user_logged_in() && $stripe_payment_terms == "lifetime" ){
                    $enqueue_stripe_scripts = false;
                }

                if( $is_elementor_exists ){
                    $enqueue_stripe_scripts = false;
                }

                if( $enqueue_stripe_scripts ){
                    wp_enqueue_style( $this->plugin_name . '-stripe-client', plugin_dir_url(__FILE__) . 'css/stripe-client.css', array(), $this->version, 'all');
                    wp_enqueue_script( $this->plugin_name . '-stripe', "https://js.stripe.com/v3/", array('jquery'), null, true );
                    wp_enqueue_script( $this->plugin_name . '-stripe-client', plugin_dir_url(__FILE__) . "js/stripe_client.js", array('jquery'), $this->version, true );
                    wp_localize_script( $this->plugin_name . '-stripe-client', 'quizMakerStripe', array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'fetchUrl' => AYS_QUIZ_PUBLIC_URL .'/partials/stripe-before-transaction.php',
                        'transactionCompleteUrl' => AYS_QUIZ_PUBLIC_URL .'/partials/stripe-transaction-complete.php',
                        'secretKey' => $stripe_secret_key,
                        'apiKey' => $stripe_api_key,
                        'paymentTerms' => $stripe_payment_terms,
                        'wrapClass' => '.ays_stripe_div_'.$id,
                        'containerId' => '#ays_quiz_stripe_button_container_'.$id,
                        'quizId' => $id,
                        'stripeOptions' => base64_encode( json_encode( array(
                            'amount' => $stripe_amount,
                            'currency' => $stripe_currency,
                        ) ) ),
                    ));
                }

                $attr['quiz_stripe'] = '
                    <div class="ays_stripe_wrap_div">
                        <div class="ays_stripe_details_div">
                            '.$stripe_message.'
                        </div>
                        <div class="ays_stripe_div_'.$id.'" style="display: none;">
                            <div id="ays_quiz_stripe_button_container_'.$id.'"></div>
                            <button class="ays_quiz_stripe_submit" type="button">
                                <div class="ays_quiz_stripe_spinner ays_quiz_stripe_hidden"></div>
                                <span class="ays_quiz_stripe_button_text">' . __( "Pay now", $this->plugin_name ) . '</span>
                            </button>
                            <span class="ays_quiz_stripe_card_error" role="alert"></span>
                        </div>
                    </div>';
            }
        }else{
            $attr['quiz_stripe'] = null;
        }

        if($enable_copy_protection){
            if ( ! $is_elementor_exists ) {
                wp_enqueue_script ($this->plugin_name .'-quiz_copy_protection', plugin_dir_url(__FILE__) . 'js/quiz_copy_protection.min.js', array('jquery'), $this->version, true);
            }
        }
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';

        $settings_for_theme = $this->settings;

        //$this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $settings_for_theme );


        if(has_action('ays_qm_front_end_integrations_before')){
            $integration_args = array(
                'status' => true,
                'message' => ''
            );
            $quiz_access_data = apply_filters("ays_qm_front_end_integrations_before", $integration_args, $quiz );

            if( $quiz_access_data['status'] === false ) {
                return $quiz_access_data['message'];
            }
        }

        $quiz_payments = array(
            'paypal' => array(
                'payment_terms' => $payment_terms,
                'html' => $attr['quiz_paypal'],
            ),
            'stripe' => array(
                'payment_terms' => $stripe_payment_terms,
                'html' => $attr['quiz_stripe'],
            )
        );

        $quiz_parts = $this->ays_quiz_parts($id, $quiz_payments);
        
        $buttons_texts_for_theme = $this->buttons_texts;

        switch ($options['quiz_theme']) {
            case 'elegant_dark':
                include_once('partials/class-quiz-theme-elegant-dark.php');
                $theme_obj = new Quiz_Theme_Elegant_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_dark', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'elegant_light':
                include_once('partials/class-quiz-theme-elegant-light.php');
                $theme_obj = new Quiz_Theme_Elegant_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_light', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_light':
                include_once('partials/class-quiz-theme-rect-light.php');
                $theme_obj = new Quiz_Theme_Rect_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_light', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_dark':
                include_once('partials/class-quiz-theme-rect-dark.php');
                $theme_obj = new Quiz_Theme_Rect_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_dark', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'modern_light':
                include_once('partials/class-quiz-theme-modern-light.php');
                $theme_obj = new Quiz_Theme_Modern_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'modern_light', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'modern_dark':
                include_once('partials/class-quiz-theme-modern-dark.php');
                $theme_obj = new Quiz_Theme_Modern_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'modern_light', $settings_for_theme,  $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            default:
                $content = $this->ays_generate_quiz($quiz_parts);
        }
        return $content;
    }
    
    public function ays_quiz_parts($id, $payments){
        
        global $wpdb;        
        
    /*******************************************************************************************************/
        
        /*
         * Get Quiz data from database by id
         * Separation options from quiz data
         */
        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        
        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
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
        $randomize_questions = false;
        $questions_ordering_by_cat = false;
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
        if(isset($options['enable_questions_ordering_by_cat']) && $options['enable_questions_ordering_by_cat'] == "on"){
            $questions_ordering_by_cat = true;
        }
        if (isset($options['randomize_questions']) && $options['randomize_questions'] == "on") {
            $randomize_questions = true;
        }
        if(isset($options['questions_count']) && intval($options['questions_count']) != 0){
            $question_bank_count = intval($options['questions_count']);
        }
        if(isset($options['question_bank_type']) && $options['question_bank_type'] != ''){
            $question_bank_type = $options['question_bank_type'];
        }
        if(isset($options['questions_bank_cat_count']) && !empty($options['questions_bank_cat_count'])){
            $questions_bank_cat_count = $options['questions_bank_cat_count'];
            foreach($questions_bank_cat_count as $cat_id => $q_count){
                if(! $q_count){
                    unset($questions_bank_cat_count[$cat_id]);
                }
            }
        }

        $quest_s = Quiz_Maker_Data::get_quiz_questions_by_ids($arr_questions);
        $quests = array();
        foreach($quest_s as $quest){
            $quests[$quest['id']] = $quest;
        }

        $question_bank_categories = Quiz_Maker_Data::get_question_bank_categories($quiz_questions_ids);

        $ays_quiz_cat_attrs = array(
            'arr_questions' => $arr_questions,
            'question_bank_categories' => $question_bank_categories,
            'randomize_questions' => $randomize_questions,
            'quests' => $quests,
        );

        if(count($arr_questions) > 0){
            if($question_bank){
                if($question_bank_type == 'general'){
                    if ($question_bank_count > 0 && $question_bank_count <= count($arr_questions)) {
                        $random_questions = array_rand($arr_questions, $question_bank_count);
                        foreach ($random_questions as $key => $question) {
                            $random_questions[$key] = strval($arr_questions[$question]);
                        }
                        $arr_questions = $random_questions;
                        $quiz_questions_ids = join(',', $random_questions);

                        if($questions_ordering_by_cat){
                            $ays_quiz_cat_attrs['arr_questions'] = $arr_questions;
                            $quiz_questions_ids = Quiz_Maker_Data::get_question_ids_ordering_by_categories( $ays_quiz_cat_attrs );
                            $ays_quiz_questions_arr = explode(',', $quiz_questions_ids);
                            $arr_questions = $ays_quiz_questions_arr;
                        }
                    }
                }elseif($question_bank_type == 'by_category'){
                    if(!empty($questions_bank_cat_count)){
                        $question_bank_questions = array();
                        $question_bank_cats = array();
                        $quiz_questions_ids = array();
                        $question_bank_by_categories1 = array();

                        foreach($arr_questions as $key => $val){
                            $question_bank_questions[$val] = $quests[$val];
                            if(isset($questions_bank_cat_count[$quests[$val]['category_id']])){
                                $question_bank_cats[$quests[$val]['category_id']][] = strval($val);
                            }
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
            }

            if($questions_ordering_by_cat && !$question_bank){
                $quiz_questions_ids = Quiz_Maker_Data::get_question_ids_ordering_by_categories( $ays_quiz_cat_attrs );
                $ays_quiz_questions_arr = explode(',', $quiz_questions_ids);
                $arr_questions = $ays_quiz_questions_arr;
            }

        }
        
        $check_custom_questions = explode(',', $quiz_questions_ids);
        foreach ($check_custom_questions as $key => $qid) {
            if(Quiz_Maker_Data::is_question_type_a_custom($qid)){
                unset($check_custom_questions[$key]);
            }
        }

        $quiz_questions_ids = join(',', $check_custom_questions);


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

        // Pass score of the quiz
        $quiz_pass_score = (isset($options['quiz_pass_score']) && $options['quiz_pass_score'] != "") ? intval($options['quiz_pass_score']) : 0;
        

    /*******************************************************************************************************/
        
        /*
         * Quiz information form fields
         *
         * Checking required filelds
         *
         * Creating HTML code for printing
         *
         * Quiz attributes for form fields
         *
         * Adding Attribute fields to information form
         */
        
        $form_inputs = null;
        $show_form = null;
        $form_title = (isset($options['form_title']) && $options['form_title'] != '') ? Quiz_Maker_Data::ays_autoembed($options['form_title']) : '';
        $required_fields = (array_key_exists('required_fields', $options) && !is_null($options['required_fields'])) ? $options['required_fields'] : array();


        $quiz_attributes = Quiz_Maker_Data::get_quiz_attributes_by_id($id, true);
        $quiz_attributes_order = (isset($options['quiz_attributes_active_order'])) ? $options['quiz_attributes_active_order'] : array();
        $default_attributes = array("ays_user_name", "ays_user_email", "ays_user_phone");
        $quiz_attributes_back = array("ays_form_name", "ays_form_email", "ays_form_phone");

        // Show information form to logged in users
        $options['information_form'] = isset($options['information_form']) ? $options['information_form'] : 'disable';

        // Show information form to logged in users
        $options['show_information_form'] = isset($options['show_information_form']) ? $options['show_information_form'] : 'on';
        $show_information_form = (isset($options['show_information_form']) && $options['show_information_form'] == 'on') ? true : false;

        foreach($quiz_attributes_order as &$order){
            if(in_array($order, $quiz_attributes_back)){
                $ind = array_search($order, $quiz_attributes_back);
                $order = $default_attributes[$ind];
            }
        }
        foreach($required_fields as &$order){
            if(in_array($order, $quiz_attributes_back)){
                $ind = array_search($order, $quiz_attributes_back);
                $order = $default_attributes[$ind];
            }
        }

        $quiz_form_attrs = array();

        if(isset($options['form_name']) && $options['form_name'] == 'on'){
            $attr_type = 'text';
        }else{
            $attr_type = 'hidden';
        }

        $quiz_form_attrs[] = array(
            "id" => null,
            "slug" => "ays_user_name",
            "name" => __( "Name", $this->plugin_name ),
            "type" => $attr_type,
            "options" => ''
        );

        if(isset($options['form_email']) && $options['form_email'] == 'on'){
            $attr_type = 'text';
        }else{
            $attr_type = 'hidden';
        }

        $quiz_form_attrs[] = array(
            "id" => null,
            "slug" => "ays_user_email",
            "name" => __( "Email", $this->plugin_name ),
            "type" => $attr_type,
            "options" => ''
        );

        if(isset($options['form_phone']) && $options['form_phone'] == 'on'){
            $attr_type = 'text';
        }else{
            $attr_type = 'hidden';
        }
        
        $quiz_form_attrs[] = array(
            "id" => null,
            "slug" => "ays_user_phone",
            "name" => __( "Phone Number", $this->plugin_name ),
            "type" => $attr_type,
            "options" => ''
        );

        $all_attributes = array_merge($quiz_form_attrs, $quiz_attributes);
        
        $custom_fields = array();
        foreach($all_attributes as $key => $attr){
            $attr_required = in_array(strval($attr['id']), $required_fields) ? 'required' : '';
            if(in_array($attr['slug'], $required_fields)){
                $attr_required = 'required';
            }
            if($attr['type'] == 'hidden'){
                $attr_required = '';
            }

            $custom_fields[$attr['slug']] = array(
                'id' => $attr['id'],
                'name' => esc_attr( $attr['name'] ),
                'type' => $attr['type'],
                'slug' => $attr['slug'],
                'required' => $attr_required,
                'options' => $attr['options']
            );
        }

        uksort($custom_fields, function($key1, $key2) use ($quiz_attributes_order) {
            return ((array_search($key1, $quiz_attributes_order) > array_search($key2, $quiz_attributes_order)) ? 1 : -1);
        });
        
        if(count($custom_fields) !== 0){
            $show_form = "show";
        }
        
        foreach ($custom_fields as $slug => $attribute) {
            $attribute = (object)$attribute;
            $attr_required = $attribute->required;
            if($attribute->type == "textarea"){
                $form_inputs .= "<textarea name='". $attribute->slug ."' class='ays_quiz_form_input ays_animated_x5ms' placeholder='" . $attribute->name . "' " . $attr_required . "></textarea>";
            }elseif($attribute->type == "select"){
                $attr_options = isset($attribute->options) ? $attribute->options : '';
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
                $attr_description = isset($attribute->options) ? $attribute->options : '';
                $form_inputs .= "<div class='ays_checkbox_for_label'>";
                $form_inputs .= "<label class='ays_for_checkbox' " . $attr_required . ">". $attribute->name;
                $form_inputs .= "<input type='checkbox' class='ays_quiz_form_input ays_animated_x5ms' name='". $attribute->slug ."' " . $attr_required . "/ >";
                $form_inputs .= "</label>";
                if($attr_description != ''){
                    $form_inputs .= "<span class='ays_checkbox_for_span'>".stripslashes(html_entity_decode($attr_description))."</span>";
                }
                $form_inputs .= "</div>";

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
        
        // Color of text of buttons inside quiz container

        if(isset($options['buttons_text_color']) && $options['buttons_text_color'] != ''){
            $buttons_text_color = $options['buttons_text_color'];
        }else{
            $buttons_text_color = $text_color;
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
        
        if(isset($options['width']) && absint( $options['width'] ) > 0 ){
            $quiz_width = $options['width'] . 'px';
            if (isset($options['quiz_width_by_percentage_px']) && $options['quiz_width_by_percentage_px'] == 'percentage') {
                if (absint(intval($options['width'])) > 100 ) {
                    $quiz_width = '100%';
                }else{
                    $quiz_width = $options['width'] . '%';
                }
            }else{
                $quiz_width = $options['width'] . 'px';
            }
        }else{
            $quiz_width = '100%';
        }
                
        // Quiz container max-width for mobile
        if(isset($options['mobile_max_width']) && $options['mobile_max_width'] != '' && absint( $options['mobile_max_width'] ) > 0){
            $mobile_max_width_val = absint( $options['mobile_max_width'] );
            if ( $mobile_max_width_val > 100 ) {
                $mobile_max_width = '100%';
            } else {
                $mobile_max_width = $mobile_max_width_val . '%';
            }
        }else{
            $mobile_max_width = '100%';
        }
        
        // Quiz title transformation
        $quiz_title_transformation = (isset($options['quiz_title_transformation']) && sanitize_text_field( $options['quiz_title_transformation'] ) != "") ? sanitize_text_field( $options['quiz_title_transformation'] ) : 'uppercase';

        // Quiz image height
        $quiz_image_height = (isset($options['quiz_image_height']) && sanitize_text_field( $options['quiz_image_height'] ) != '') ? absint( sanitize_text_field( $options['quiz_image_height'] ) ) : '';


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
        
        //  Box Shadow X offset
        $quiz_box_shadow_x_offset = (isset($options['quiz_box_shadow_x_offset']) && intval( $options['quiz_box_shadow_x_offset'] ) != 0) ? intval( $options['quiz_box_shadow_x_offset'] ) : 0;

        //  Box Shadow Y offset
        $quiz_box_shadow_y_offset = (isset($options['quiz_box_shadow_y_offset']) && intval( $options['quiz_box_shadow_y_offset'] ) != 0) ? intval( $options['quiz_box_shadow_y_offset'] ) : 0;

        //  Box Shadow Z offset
        $quiz_box_shadow_z_offset = (isset($options['quiz_box_shadow_z_offset']) && intval( $options['quiz_box_shadow_z_offset'] ) != 0) ? intval( $options['quiz_box_shadow_z_offset'] ) : 15;

        $box_shadow_offsets = $quiz_box_shadow_x_offset . 'px ' . $quiz_box_shadow_y_offset . 'px ' . $quiz_box_shadow_z_offset . 'px ';


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
        
        // Hide quiz background image on the result page
        $quiz_bg_img_in_finish_page = "false";

        if(isset($options['quiz_bg_img_in_finish_page']) && $options['quiz_bg_img_in_finish_page'] == "on"){
            $quiz_bg_img_in_finish_page = "true";
        }

        // Hide background image on start page
        $options['quiz_bg_img_on_start_page'] = isset($options['quiz_bg_img_on_start_page']) ? $options['quiz_bg_img_on_start_page'] : 'off';
        $quiz_bg_img_on_start_page = (isset($options['quiz_bg_img_on_start_page']) && $options['quiz_bg_img_on_start_page'] == 'on') ? true : false;

        $quiz_bg_img_class = '';
        if ( $quiz_bg_img_on_start_page ) {
            $quiz_bg_img_class = 'ays_quiz_hide_bg_on_start_page';
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
        
        // Image Width(px)
        $image_width = (isset($options['image_width']) && sanitize_text_field($options['image_width']) != '' && absint($options['image_width']) > 0) ? absint( $options['image_width'] ) : '';

        // Quiz image width percentage/px
        $quiz_image_width_by_percentage_px = (isset($options['quiz_image_width_by_percentage_px']) && sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) != '') ? sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) : 'pixels';

        if($image_width != ''){
            if ($quiz_image_width_by_percentage_px == 'percentage') {
                if ($image_width > 100 ) {
                    $question_image_width = '100%';
                }else{
                    $question_image_width = $image_width . '%';
                }
            }else{
                $question_image_width = $image_width . 'px';
            }
        }else{
            $question_image_width = "100%";
        }
        
        if(isset($options['image_height']) && $options['image_height'] != ''){
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
            Buttons styles
        ==========================================
        */

        // Buttons position
        $buttons_position = 'center';
        if(isset($options['buttons_position']) && $options['buttons_position'] != ''){
            $buttons_position = $options['buttons_position'];
        }

        // Buttons font size
        $buttons_font_size = '17px';
        if(isset($options['buttons_font_size']) && $options['buttons_font_size'] != ''){
            $buttons_font_size = $options['buttons_font_size'] . 'px';
        }

        // Buttons font size
        $buttons_width = '';
        if(isset($options['buttons_width']) && $options['buttons_width'] != ''){
            $buttons_width = $options['buttons_width'] . 'px';
        }

        $buttons_width_html = '';
        if( $buttons_width != ''){
            $buttons_width_html = "width:" . $buttons_width;
        }

        // Buttons Left / Right padding
        $buttons_left_right_padding = '20px';
        if(isset($options['buttons_left_right_padding']) && $options['buttons_left_right_padding'] != ''){
            $buttons_left_right_padding = $options['buttons_left_right_padding'] . 'px';
        }

        // Buttons Top / Bottom padding
        $buttons_top_bottom_padding = '10px';
        if(isset($options['buttons_top_bottom_padding']) && $options['buttons_top_bottom_padding'] != ''){
            $buttons_top_bottom_padding = $options['buttons_top_bottom_padding'] . 'px';
        }

        // Buttons border radius
        $buttons_border_radius = '3px';
        if(isset($options['buttons_border_radius']) && $options['buttons_border_radius'] != ''){
            $buttons_border_radius = $options['buttons_border_radius'] . 'px';
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
        $answers_padding = '5';
        if(isset($options['answers_padding']) && $options['answers_padding'] != ''){
            $answers_padding = $options['answers_padding'];
        }

        // Answers margin option
        $answers_margin = 10;
        if(isset($options['answers_margin']) && $options['answers_margin'] != ''){
            $answers_margin = intval( $options['answers_margin'] );
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

        // Question Font Size
        $question_font_size = '16';
        if(isset($options['question_font_size']) && $options['question_font_size'] != ""){
            $question_font_size = $options['question_font_size'];
        }

        // Disable answer hover
        $options['disable_hover_effect'] = isset($options['disable_hover_effect']) ? $options['disable_hover_effect'] : 'off';
        $disable_hover_effect = (isset($options['disable_hover_effect']) && $options['disable_hover_effect'] == "on") ? true : false;

        // Question text alignment
        $quiz_question_text_alignment = (isset($options['quiz_question_text_alignment']) && sanitize_text_field( $options['quiz_question_text_alignment'] ) != '') ? sanitize_text_field( $options['quiz_question_text_alignment'] ) : 'center';

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

        $answers_object_fit = 'cover';
        if(isset($options['answers_object_fit']) && $options['answers_object_fit'] != ''){
            $answers_object_fit = $options['answers_object_fit'];
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
        $description = Quiz_Maker_Data::ays_autoembed( $quiz['description'] );
                
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
        
        // Limit Quiz by password
        $general_password_radio = (isset($options['generate_password']) && $options['generate_password'] != '') ? $options['generate_password'] : 'general';
        $generated_passwords  = (isset($options['generated_passwords']) && $options['generated_passwords'] != '') ? $options['generated_passwords'] : array();
        if(!empty($generated_passwords)){
            $active_passwords = (isset( $generated_passwords['active_passwords']) && !empty( $generated_passwords['active_passwords'])) ?  $generated_passwords['active_passwords'] : array();
        }

        $password_message = "";
        $start_button_disabled = "";
        if(isset($options['enable_password']) && $options['enable_password'] == 'on'){
            $password_message = "<input type='password' autocomplete='no' id='ays_quiz_password_val_". $id ."' class='ays_quiz_password' placeholder='". __( "Please enter password", $this->plugin_name) ."'>";
            $start_button_disabled = " disabled='disabled' ";
        }

        // Checking confirmation box for leaving the page enabled or diabled
        if (isset($options['enable_leave_page']) && $options['enable_leave_page'] == 'on') {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }elseif (! isset($options['enable_leave_page'])) {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }else{
            $enable_leave_page = '';
        }

        // Disable answer hover
        $settings_options['enable_start_button_loader'] = isset($settings_options['enable_start_button_loader']) ? sanitize_text_field($settings_options['enable_start_button_loader']) : 'off';
        $enable_start_button_loader = (isset($settings_options['enable_start_button_loader']) && sanitize_text_field($settings_options['enable_start_button_loader']) == "on") ? true : false;

        $quiz_start_button = "<input type='button' $empty_questions_button $start_button_disabled name='next' class='ays_next start_button action-button' value='". $this->buttons_texts['startButton'] ."' ". $enable_leave_page ." />";
        
        if ( $enable_start_button_loader ) {
            if ($questions_count != 0) {
                $quiz_start_butto_html = "<input type='button' $empty_questions_button class='ays_next start_button action-button ays_quiz_enable_loader' disabled='disabled' value='". __('Loading ...', $this->plugin_name) ."' ". $enable_leave_page ." />".$empty_questions_notification;

                $quiz_start_button = '
                <div class="ays-quiz-start-button-preloader">
                    '. $quiz_start_butto_html .'
                    <div class="ays_quiz_start_button_loader_container">
                        <img src="'. AYS_QUIZ_ADMIN_URL .'/images/loaders/tail-spin.svg" class="ays_quiz_start_button_loader">
                    </div>
                </div>';
            }
        }


        /*
         * Show quiz head information
         * Show quiz title and description
         */
        
        $options['show_quiz_title'] = isset($options['show_quiz_title']) ? $options['show_quiz_title'] : 'on';
        $options['show_quiz_desc'] = isset($options['show_quiz_desc']) ? $options['show_quiz_desc'] : 'on';
        $show_quiz_title = (isset($options['show_quiz_title']) && $options['show_quiz_title'] == "on") ? true : false;
        $show_quiz_desc = (isset($options['show_quiz_desc']) && $options['show_quiz_desc'] == "on") ? true : false;

        /*
         * Make the questions required
         */

        $options['make_questions_required'] = isset($options['make_questions_required']) ? $options['make_questions_required'] : 'off';
        $make_questions_required = (isset($options['make_questions_required']) && $options['make_questions_required'] == "on") ? true : false;

        
        /* 
         * Quiz passed users count
         *
         * Generate HTML code
         */
        
        if(isset($options['enable_pass_count']) && $options['enable_pass_count'] == "on"){
            $enable_pass_count = true;
            $quiz_result_reports = Quiz_Maker_Data::get_quiz_results_count_by_id($id);
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
        
        $quiz_rates_avg = round(Quiz_Maker_Data::ays_get_average_of_rates($id), 1);
        $quiz_rates_count = Quiz_Maker_Data::ays_get_count_of_rates($id);
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
            $quiz_create_date = (isset($quiz['create_date']) && $quiz['create_date'] != '') ? $quiz['create_date'] : "0000-00-00 00:00:00";
            if( Quiz_Maker_Admin::validateDate( $quiz_create_date ) ){
                $show_cd_and_author .= "<span>". __( "Created on", $this->plugin_name ) ." </span>
                    <strong><time>". date_i18n( "F d, Y", strtotime( $quiz_create_date ) ) ."</time></strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }

        if($show_author){
            $user_id = 0;
            if(isset($quiz['author_id'])){
                $user_id = intval( $quiz['author_id'] );
            }

            $author_name = '';
            if($user_id != 0){
                $author = get_userdata( $user_id );
                if($author !== null){
                    $author_name = $author->data->display_name;
                }
            }
            $image = get_avatar($user_id, 32);

            if($author_name != ''){
                if($show_create_date){
                    $text = __("By", $this->plugin_name);
                }else{
                    $text = __("Created by", $this->plugin_name);
                }
                $show_cd_and_author .= "<span>   ".$text." </span>".$image."<strong>". $author_name ."</strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        if($show_category){
            $category_id = isset($quiz['quiz_category_id']) ? intval($quiz['quiz_category_id']) : null;
            if($category_id !== null){
                $quiz_category = Quiz_Maker_Data::get_quiz_category_by_id($category_id);
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
            $timer_text = Quiz_Maker_Data::ays_autoembed( str_replace( '%%time%%', Quiz_Maker_Data::secondsToWords($options['timer']), $timer_text ) );
            $after_timer_text = (isset($options['after_timer_text'])) ? $options['after_timer_text'] : '';
            $after_timer_text = Quiz_Maker_Data::ays_autoembed( str_replace( '%%time%%', Quiz_Maker_Data::secondsToWords($options['timer']), $after_timer_text ) );
            $hide_timer_cont = "";
            $empty_after_timer_text_class = "";
            if($timer_text == ""){
                $hide_timer_cont = " style='display:none;' ";
            }
            if($after_timer_text == ""){
                $empty_after_timer_text_class = " empty_after_timer_text ";
            }
            $timer_row = "<section {$hide_timer_cont} class='ays_quiz_timer_container'>
                <div class='ays-quiz-timer' data-timer='" . $options['timer'] . "'>{$timer_text}</div>
                <div class='ays-quiz-after-timer ".$empty_after_timer_text_class."'>{$after_timer_text}</div>
                <hr style='height:1px;'>
            </section>";
        }
        
        
        /*
         * Quiz live progress bar
         *
         * Checking enabled or diabled
         *
         * Checking percent view or not
         */
        $filling_type = '';
        $filling_type_wrap = '';

        // Progress live bar style
        $options['enable_live_progress_bar'] = isset($options['enable_live_progress_bar']) ? $options['enable_live_progress_bar'] : 'off';
        $enable_live_progress_bar = (isset($options['enable_live_progress_bar']) && $options['enable_live_progress_bar'] == 'on') ? true : false;

        if( $enable_live_progress_bar ){
            $live_preview_view = isset($options['progress_live_bar_style']) && $options['progress_live_bar_style'] != '' ? $options['progress_live_bar_style'] : '';
            
            if(isset($options['enable_percent_view']) && $options['enable_percent_view'] == "on"){
                $live_progress_bar_percent = "<span class='ays-live-bar-percent'>0</span>%";
            }else{
                $live_progress_bar_percent = "<span class='ays-live-bar-percent ays-live-bar-count'></span>/$questions_count";
            }
            switch ($live_preview_view) {
                case 'second':
                    $filling_type_wrap = 'ays-live-second-wrap';
                    $filling_type = 'ays-live-second';
                    break;
                case 'third':
                    $filling_type_wrap = 'ays-live-third-wrap';
                    $filling_type = 'ays-live-third';
                    break;
                case 'fourth':
                    $filling_type_wrap = 'ays-live-fourth-wrap';
                    $filling_type = 'ays-live-fourth';
                    break;
                default:
                    $filling_type_wrap = '';
                    $filling_type = '';
                    break;
            }

            
            $live_progress_bar = "<div class='ays-live-bar-wrap $filling_type_wrap'><div class='ays-live-bar-fill $filling_type' style='width: 0%;'><span>$live_progress_bar_percent</span></div></div>";
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
              
        // Answeres numbering
        $show_answers_numbering = (isset($options['show_answers_numbering']) && $options['show_answers_numbering'] !== '') ?  $options['show_answers_numbering'] : 'none';

        
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
         * Question hint value 
         */
        $questions_hint_icon_or_text = (isset($options['questions_hint_icon_or_text']) && $options['questions_hint_icon_or_text'] == 'text') ? true : false;
        $questions_hint_value = (isset($options['questions_hint_value']) && $options['questions_hint_value'] != '') ? stripslashes(esc_attr($options['questions_hint_value'])) : '';

        $questions_hint_arr = array(
            'questionsHintIconOrText' => $questions_hint_icon_or_text,
            'questionsHintValue' => $questions_hint_value,
        );
        
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
        
        // Quiz arrows option arrows
        if(isset($options['quiz_arrow_type']) && $options['quiz_arrow_type'] != ""){
            $quiz_arrow_type = $options['quiz_arrow_type'];
        }else{
            $quiz_arrow_type = 'default';
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
                    <span>". $this->buttons_texts['restartQuizButton'] ."</span>
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
            $exit_button = "<a style='width:auto;' href='".$exit_redirect_url."' class='action-button' target='_self'>
                        <span>". $this->buttons_texts['exitButton'] ."</span>
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
            "quizArrowType" => $quiz_arrow_type,
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
         * General Settings
         * Animation Top (px)
         */
        $quiz_animation_top = (isset($settings_options['quiz_animation_top']) && $settings_options['quiz_animation_top'] != 0) ? absint(intval($settings_options['quiz_animation_top'])) : 100;

        
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


        /*
         * Text quetion type
         * Textarea height (public)
         */

        // Textarea height (public)
        $quiz_textarea_height = (isset($settings_options['quiz_textarea_height']) && $settings_options['quiz_textarea_height'] != '' && $settings_options['quiz_textarea_height'] != 0) ? absint( sanitize_text_field($settings_options['quiz_textarea_height']) ) : 100;

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
        $pass_score_html = "";
        
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
            $result = Quiz_Maker_Data::ays_get_average_of_scores($id);
            $show_average = "<p class='ays_average'>" . __('The average score is', $this->plugin_name) . " " . $result . "%</p>";
        }
        
        
        /*
         * Passed quiz score
         *
         * Checking enabled or diabled
         */
        $options['hide_score'] = isset( $options['hide_score'] ) ? $options['hide_score'] : 'off';
        if(array_key_exists('hide_score', $options) && $options['hide_score'] != "on"){
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
        
        /*
         * Passed or Failed quiz score html
         */
        $pass_score_html = "<div class='ays_score_message'></div>";

        
//        if($calculate_score == 'by_points'){
//            $enable_questions_result = '';
//        }
        
        /*
         * Quiz rate
         *
         * Generating HTML code
         */
        
        if(isset($options['rate_form_title'])){
            $rate_form_title = Quiz_Maker_Data::ays_autoembed( $options['rate_form_title'] );
        }
        
        if(isset($options['enable_quiz_rate']) && $options['enable_quiz_rate'] == "on"){
            $quiz_rate_html = "<div class='ays_quiz_rete'>
                <div>$rate_form_title</div>
                <div class='for_quiz_rate ui huge star rating' data-rating='0' data-max-rating='5'></div>
                <div style='text-align:center;'><div class='lds-spinner-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                <div class='for_quiz_rate_reason'>
                    <textarea id='quiz_rate_reason_".$id."' class='quiz_rate_reason'></textarea>
                    <div class='ays_feedback_button_div'>
                        <button type='button' class='action-button'>". $this->buttons_texts['sendFeedbackButton'] ."</button>
                    </div>
                </div>
                <div style='text-align:center;'><div class='lds-spinner2-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
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
                            <span class='ays-quiz-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>LinkedIn</span>
                        </a>
                        <!-- Branded Facebook button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-facebook'
                           href='https://www.facebook.com/sharer/sharer.php?u=" . $actual_link . "'
                           title='Share on Facebook'>
                            <span class='ays-quiz-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>Facebook</span>
                        </a>
                        <!-- Branded Twitter button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-twitter'
                           href='https://twitter.com/share?url=" . $actual_link . "'
                           title='Share on Twitter'>
                            <span class='ays-quiz-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>Twitter</span>
                        </a>
                        <!-- Branded VK button -->
                        <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-vkontakte'
                           href='https://vk.com/share.php?url=" . $actual_link . "'
                           title='Share on VKontakte'>
                            <span class='ays-quiz-share-btn-icon'></span>
                            <span class='ays-share-btn-text'>VKontakte</span>
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
            'twitter_link' => '',
            'vkontakte_link' => '',
        );
        $ays_social_links_array = array();

        $linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? $social_links['linkedin_link'] : '';
        $facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? $social_links['facebook_link'] : '';
        $twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? $social_links['twitter_link'] : '';
        $vkontakte_link = isset($social_links['vkontakte_link']) && $social_links['vkontakte_link'] != '' ? $social_links['vkontakte_link'] : '';
        if($linkedin_link != ''){
            $ays_social_links_array['Linkedin'] = $linkedin_link;
        }
        if($facebook_link != ''){
            $ays_social_links_array['Facebook'] = $facebook_link;
        }
        if($twitter_link != ''){
            $ays_social_links_array['Twitter'] = $twitter_link;
        }
        if($vkontakte_link != ''){
            $ays_social_links_array['VKontakte'] = $vkontakte_link;
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
                        <span class='ays-quiz-share-btn-icon'></span>
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
        
        // Custom Text
        $quiz_loader_text_value = (isset($options['quiz_loader_text_value']) && $options['quiz_loader_text_value'] != '') ? stripslashes($options['quiz_loader_text_value']) : '';

        // Custom Gif
        $quiz_loader_custom_gif = (isset($options['quiz_loader_custom_gif']) && $options['quiz_loader_custom_gif'] != '') ? stripslashes($options['quiz_loader_custom_gif']) : '';

        //  Quiz loader custom gif width
        $quiz_loader_custom_gif_width = (isset($options['quiz_loader_custom_gif_width']) && $options['quiz_loader_custom_gif_width'] != '') ? absint( intval( $options['quiz_loader_custom_gif_width'] ) ) : 100;

        $quiz_loader_custom_gif_width_css = '';
        if ( $quiz_loader_custom_gif_width != '' ) {
            $quiz_loader_custom_gif_width_css = 'width: '. $quiz_loader_custom_gif_width .'px; height: auto; max-width: 100%;';
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
            case 'text':
                if ($quiz_loader_text_value != '') {
                    $quiz_loader_html = "
                    <div class='ays-loader' data-class='ays-loader-text' data-role='loader'>
                        <p class='ays-loader-content'>". $quiz_loader_text_value ."</p>
                    </div>";
                }else{
                    $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                }
                break;
            case 'custom_gif':
                if ($quiz_loader_custom_gif != '') {
                    $quiz_loader_html = "
                    <div class='ays-loader' data-class='ays-loader-text' data-role='loader'>
                        <img src='". $quiz_loader_custom_gif ."' class='ays-loader-content ays-loader-custom-gif-content' style='". $quiz_loader_custom_gif_width_css ."'>
                    </div>";
                }else{
                    $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                }
                break;
            default:
                $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                break;
        }
        
        $quiz_loader_html = "<div style='text-align:center;'>" . $quiz_loader_html . "</div>";


        
        
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
        $limit_users_res_id = false;
        
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
        $limit_users_attr = array();
        $check_cookie = null;

        if(isset($options['limit_users_by']) && $options['limit_users_by'] != ''){
            $limit_users_by = $options['limit_users_by'];
        }

        $limit_users_attr = array(
            'id' => $id,
            'name' => 'ays_quiz_cookie_',
            'title' => $title,
        );

        $attempts_count_last_chance = null;
        $count_of_attempts_remaining = '';
        if (isset($options['limit_users']) && $options['limit_users'] == "on") {
            switch ( $limit_users_by ) {
                case 'ip':
                    $result = Quiz_Maker_Data::get_user_by_ip( $id, $quiz_pass_score );
                    if ( $check_cookie ) {
                        $remove_cookie = Quiz_Maker_Data::ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
                case 'user_id':
                    if(is_user_logged_in()){
                        $user_id = get_current_user_id();
                        $result = Quiz_Maker_Data::get_limit_user_by_id( $id, $user_id, $quiz_pass_score );
                    }else{
                        $result = 0;
                    }

                    if ( $check_cookie ) {
                        $remove_cookie = Quiz_Maker_Data::ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
                case 'cookie':
                    $check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $limit_users_attr );
                    if ( ! $check_cookie ) {
                        $result = 0;
                    }else{
                        $result = Quiz_Maker_Data::get_limit_cookie_count( $limit_users_attr );
                    }
                    break;
                case 'ip_cookie':
                    $check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $limit_users_attr );
                    $check_user_by_ip = Quiz_Maker_Data::get_user_by_ip( $id, $quiz_pass_score );
                    if($check_cookie || $check_user_by_ip > 0){
                        $result = $check_user_by_ip;
                    }elseif(! $check_cookie || $check_user_by_ip <= 0){
                        $result = 0;
                    }
                    break;
                default:
                    $result = 0;
                    if ( $check_cookie ) {
                        $remove_cookie = Quiz_Maker_Data::ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
            }

            $limit_users_res_id = true;
            $quiz_max_pass_count = (isset($options['quiz_max_pass_count']) && $options['quiz_max_pass_count'] != '') ? absint(intval($options['quiz_max_pass_count'])) : 1;

            if( intval( $result ) < $quiz_max_pass_count ){
                $attempts_count = $quiz_max_pass_count - $result;
                $attempts_count_last_chance = $attempts_count;
                $count_of_attempts_remaining .= '<p>';
                $count_of_attempts_remaining .= __('The number of attempts remaining is ', $this->plugin_name) . $attempts_count;
                $count_of_attempts_remaining .= '</p>';
            }else{
                $timer_row = "";
            }

            if ($result != 0) {
                $limit_users = true;

                if( intval( $result ) < $quiz_max_pass_count ){
                    $limit_users = false;
                }

                if(isset($options['redirection_delay']) && $options['redirection_delay'] != ''){
                    if(isset($options['redirect_url']) && $options['redirect_url'] != ''){
                        if($limit_users){
                            $timer_row = "<qm_rurl class='ays_redirect_url' style='display:none'>" .
                                    $options['redirect_url'] . 
                                "</qm_rurl>
                                <div class='ays-quiz-timer' data-show-in-title='".$show_timer_in_title."' data-timer='" . $options['redirection_delay'] . "'>". 
                                    __( "Redirecting after", $this->plugin_name ). " " . 
                                    Quiz_Maker_Data::secondsToWords($options['redirection_delay']) .
                                    "<EXTERNAL_FRAGMENT></EXTERNAL_FRAGMENT>                                
                                </div>";
                        }
                    }
                }

                $limit_message = '';
                if( isset($options['limitation_message']) && $options['limitation_message'] != '' ){
                    $limit_message = Quiz_Maker_Data::ays_autoembed( $options['limitation_message'] );
                }
                
                if($limit_message == ''){
                    $limit_message = __('You already passed this quiz.', $this->plugin_name);
                }
                
                $limit_users_html = $timer_row . "<div style='color:" . $text_color . ";min-height:".($quiz_height/2)."px;' class='ays_block_content'>" . $limit_message . "</div><style>form{min-height:0 !important;}</style>";
            }
        }else{
            $limit_users = false;
            if ( $check_cookie ) {
                $remove_cookie = Quiz_Maker_Data::ays_quiz_remove_cookie( $limit_users_attr );
            }
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
            $ays_login_button_text = $this->buttons_texts['loginButton'];
            $args = array(
                'echo' => false,
                'id_username' => 'ays_user_login',
                'id_password' => 'ays_user_pass',
                'id_remember' => 'ays_rememberme',
                'id_submit' => 'ays-submit',
                'label_log_in' => $ays_login_button_text,
            );
            $quiz_login_form = "<div class='ays_quiz_login_form'>" . wp_login_form( $args ) . "</div>";
        }
        
        global $wp_roles;
        
        if(isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on' && !is_user_logged_in()){
            $enable_logged_users = 'only_logged_users';
            if(isset($options['enable_logged_users_message']) && $options['enable_logged_users_message'] != ""){
                $logged_users_message = Quiz_Maker_Data::ays_autoembed( $options['enable_logged_users_message'] );
            }else{
                $logged_users_message = '';
                if (! $show_login_form) {
                    $logged_users_message =  __('You must log in to pass this quiz.', $this->plugin_name);
                }
            }
            if($logged_users_message !== null){
                $user_massage = '<div class="logged_in_message">' . $logged_users_message . '</div>';
            }else{
                $user_massage = null;
            }
        }else{
            $user_massage = null;
            $enable_logged_users = '';
            $search_user_ishmar = false;
            $enable_restriction_pass_users = isset($options['enable_restriction_pass_users']) && $options['enable_restriction_pass_users'] == 'on' ? true : false;
            if ($enable_restriction_pass_users) {
                $current_users = wp_get_current_user();
                $current_user  = $current_users->data->ID;
                $search_users_message = (isset($options['restriction_pass_users_message']) && $options['restriction_pass_users_message'] != '') ? $options['restriction_pass_users_message'] : __('Permission Denied', $this->plugin_name);
                $search_users = (isset($options['ays_users_search']) && $options['ays_users_search'] != '') ? $options['ays_users_search'] : '';
                $user_massage = '<div class="logged_in_message">' . Quiz_Maker_Data::ays_autoembed( $search_users_message ) . '</div>';

                if (is_array($search_users)) {
                    if(in_array($current_user, $search_users)){
                        $user_massage = null;
                        $search_user_ishmar = true;
                    }
                }else{
                    if($current_user == $search_users){
                        $user_massage = null;
                        $search_user_ishmar = true;
                    }
                }
            }

            if (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') {
                $user = wp_get_current_user();
                $user_roles   = $wp_roles->role_names;
                $message = (isset($options['restriction_pass_message']) && $options['restriction_pass_message'] != '') ? $options['restriction_pass_message'] : __('Permission Denied', $this->plugin_name);
                $user_role = (isset($options['user_role']) && $options['user_role'] != '') ? $options['user_role'] : '';
                $user_massage = '<div class="logged_in_message">' . Quiz_Maker_Data::ays_autoembed( $message ) . '</div>';
                
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
                        if (in_array(strtolower($role), (array)$user->roles) || $search_user_ishmar) {
                            $user_massage = null;
                            break;
                        }
                    }                    
                }else{
                    if (in_array(strtolower($user_role), (array)$user->roles) || $search_user_ishmar) {
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
        $startDate = '';
        $endDate = '';
        $startDate_atr = '';
        $endDate_atr = '';
        $current_time = strtotime(current_time( "Y:m:d H:i:s" ));
        $activeInterval = isset( $options['activeInterval'] ) && $options['activeInterval'] != '' ? $options['activeInterval'] : current_time( 'mysql' );
        $deactiveInterval = isset( $options['deactiveInterval'] ) && $options['deactiveInterval'] != '' ? $options['deactiveInterval'] : current_time( 'mysql' );
		$startDate = strtotime( $activeInterval );
		$endDate   = strtotime( $deactiveInterval );

		$expired_quiz_message = "<p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>";

        if (isset($options['active_date_check']) && $options['active_date_check'] == "on") {
            $active_date_check = true;

            if (isset($options['activeInterval']) && !empty($options['activeInterval'])) {
                $startDate_atr = $startDate - $current_time;
            }elseif (isset($options['deactiveInterval']) && !empty($options['deactiveInterval'])) {
                $endDate_atr = $endDate - $current_time;
            }

            // show timer
            $activeDateCheck =  isset($options['active_date_check']) && !empty($options['active_date_check']) ? true : false;
            $activeDeactiveDateCheck =  isset($options['deactiveInterval']) && !empty($options['deactiveInterval']) ? true : false;
            $show_timer_type = isset($options['show_timer_type']) && !empty($options['show_timer_type']) ? $options['show_timer_type'] : 'countdown';
            $activeActiveDateCheck =  isset($options['activeInterval']) && !empty($options['activeInterval']) ? true : false;

            $show_timer = '';
            if ($activeDateCheck && $activeActiveDateCheck && $active_date_check) {
                if (isset($options['show_schedule_timer']) && $options['show_schedule_timer'] == 'on') {
                    $show_timer .= "<div class='ays_quiz_show_timer'>";
                    if ($show_timer_type == 'countdown') {
                        $show_timer .= '<p id="show_timer_countdown" data-timer_countdown="'.$startDate_atr.'"></p>';
                    }else if ($show_timer_type == 'enddate') {
                        $show_timer .= '<p id="show_timer_countdown">' . __('This Quiz will start on', $this->plugin_name);
                        $show_timer .= ' ' . date_i18n('H:i:s F jS, Y', intval($startDate));
                        $show_timer .= '</p>';
                    }
                    $show_timer .= "</div>";
                }
            }

            if ($startDate > $current_time) {
				$is_expired = true;
                if(isset($options['active_date_pre_start_message'])){
			        $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            ".$show_timer."
                            " . Quiz_Maker_Data::ays_autoembed( $options['active_date_pre_start_message'] ) . "
                        </div>
                    </div>";
                }else{
                    $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            ".$show_timer."
                            <p class='ays-fs-subtitle'>" . __('The quiz will be available soon.', $this->plugin_name) . "</p>
                        </div>
                    </div>";
                }
			}elseif ($endDate < $current_time) {
                $is_expired = true;
                if(isset($options['active_date_message']) && $options['active_date_message'] != ''){
                    $expired_quiz_message = "<div class='step active-step' data-message-exist='true'>
                        <div class='ays-abs-fs'>
                            " . Quiz_Maker_Data::ays_autoembed( $options['active_date_message'] ) . "
                        </div>
                    </div>";
                }else{
                    $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            <p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>
                        </div>
                    </div>";
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
            $description = "<div class='ays-fs-subtitle'>" . $description . "</div>";
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
                    {$password_message}
                    <div class='ays_buttons_div'>
                        {$quiz_start_button}
                    </div>
                    {$count_of_attempts_remaining}
                    {$empty_questions_notification}
                </div>
            </div>";
        
        if($limit_users === false || $limit_users === null){
            $restart_button_html = $restart_button;
        }else{
            $restart_button_html = "";
        }
        
        if($attempts_count_last_chance !== null){
            if($attempts_count_last_chance <= 1){
                $restart_button_html = "";
            }
        }

        $main_content_last_part = "<div class='step ays_thank_you_fs'>
            <div class='ays-abs-fs ays-end-page'>".
            $quiz_loader_html .
            "<div class='ays_quiz_results_page'>".
                $pass_score_html .
                "<div class='ays_message'></div>" .
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
        
        if (! $show_information_form) {
            if(is_user_logged_in()){
                $show_form = null;
            }
        }

        if($show_form != null){
            if ($options['information_form'] == "after") {
                $main_content_last_part = "<div class='step'>
                    <div class='ays-abs-fs ays-end-page information_form'>
                    <div class='ays-form-title'>{$form_title}</div>
                        " . $form_inputs . "
                        <div class='ays_buttons_div'>
                            <i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i>
                            <input type='submit' name='ays_finish_quiz' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_next ays_finish action-button' value='" . $this->buttons_texts['seeResultButton'] . "'/>
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
                            <input type='button' name='next' class='ays_next action-button " . ($enable_arrows ? 'ays_display_none' : '') . "' value='" . $this->buttons_texts['nextButton'] . "' />
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
            $options['submit_redirect_after'] = Quiz_Maker_Data::secondsToWords($options['submit_redirect_delay']);
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
        
        $quiz_options['is_user_logged_in'] = is_user_logged_in();
        $quiz_options['quiz_animation_top'] = $quiz_animation_top;

        if ($limit_users) {
            if($limit_users_by == 'ip'){
                $result = Quiz_Maker_Data::get_user_by_ip($id, $quiz_pass_score);
            }elseif($limit_users_by == 'user_id'){
                if(is_user_logged_in()){
                    $user_id = get_current_user_id();
                    $result = Quiz_Maker_Data::get_limit_user_by_id($id, $user_id, $quiz_pass_score);
                }else{
                    $result = 0;
                }
            }else{
                $result = 0;
            }
            $result = Quiz_Maker_Data::get_user_by_ip($id, $quiz_pass_score);
            if ($result == 0) {
                $quiz_content_script .= "
                    if(typeof aysQuizOptions === 'undefined'){
                        var aysQuizOptions = [];
                    }
                    aysQuizOptions['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
            }
        }else{
            $quiz_content_script .= "
                if(typeof aysQuizOptions === 'undefined'){
                    var aysQuizOptions = [];
                }
                aysQuizOptions['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
        }
        $quiz_content_script .= "
        </script>";
        
    /*******************************************************************************************************/
        
        /*
         * Styles for quiz
         *
         * Generating HTML code
         */
                
        $options['custom_css'] = isset( $options['custom_css'] ) && !empty( $options['custom_css'] ) ? $options['custom_css'] : '';
        
        $quest_animation = 'shake';
        
        if(isset($options['quest_animation']) && $options['quest_animation'] != ''){
            $quest_animation = $options['quest_animation'];
        }
        
        $quiz_styles = "<style>
            div#ays-quiz-container-" . $id . " * {
                box-sizing: border-box;
            }";

        if($ie_container_css != ''){
            $quiz_styles .= "
            /*
            #ays-quiz-container-" . $id . " .ays_next.action-button,
            #ays-quiz-container-" . $id . " .ays_previous.action-button{
                margin: 10px 5px;
            }
            */

            #ays-quiz-container-" . $id . " .ays_block_content{
                margin: 0 auto;
                word-break: break-all;
            }

            ";
        }else{
            $quiz_styles .= "
            /* Styles for Internet Explorer start */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " {
                " . $ie_container_css . "
            }
            ";
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
            $quiz_styles .=  "box-shadow: ". $box_shadow_offsets ." 1px " . Quiz_Maker_Data::hex2rgba($box_shadow_color, '0.4') . ";";
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
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-start-page *:not(input),
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_question_hint,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container label[for^=\"ays-answer-\"],
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container p,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-fs-title,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-fs-subtitle,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .logged_in_message,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_score_message,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_message{
               color: " . $text_color . ";
               outline: none;
            }
            
            /* Quiz title / transformation */
            #ays-quiz-container-" . $id . " .ays-fs-title{
                text-transform: " . $quiz_title_transformation . ";
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

            #ays-quiz-container-" . $id . " .ays_quiz_question p {
                font-size: ".$question_font_size."px;
                text-align: ". $quiz_question_text_alignment .";
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question {
                text-align:  ". $quiz_question_text_alignment ." ;
            }

            div#ays-quiz-container-" . $id . " .ays-questions-container .ays-field,
            div#ays-quiz-container-" . $id . " .ays-questions-container .ays-field input~label[for^='ays-answer-'],
            div#ays-quiz-container-" . $id . " .ays-questions-container .ays-modern-dark-question *,
            div#ays-quiz-container-" . $id . " .ays-questions-container .ays_quiz_question,
            div#ays-quiz-container-" . $id . " .ays-questions-container .ays_quiz_question *{
                word-break: break-word;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-timer p {
                font-size: 16px;
            }

            #ays-quiz-container-" . $id . " .ays_thank_you_fs p {
                text-align: center;
            }

            #ays-quiz-container-" . $id . " .information_form input[type='text'],
            #ays-quiz-container-" . $id . " .information_form input[type='url'],
            #ays-quiz-container-" . $id . " .information_form input[type='number'],
            #ays-quiz-container-" . $id . " .information_form input[type='email'],
            #ays-quiz-container-" . $id . " .information_form input[type='tel'],
            #ays-quiz-container-" . $id . " .information_form textarea,
            #ays-quiz-container-" . $id . " .information_form select,
            #ays-quiz-container-" . $id . " .information_form option {
                color: initial !important;
                outline: none;
                margin-left: 0;
                background-image: unset;
            }

            #ays-quiz-container-" . $id . " .information_form input[type='checkbox'] {
                margin: 0 10px;
                outline: initial;
                -webkit-appearance: auto;
                -moz-appearance: auto;
                position: initial;
                width: initial;
                height: initial;
                border: initial;
                background: initial;
            }

            #ays-quiz-container-" . $id . " .information_form input[type='checkbox']::after {
                content: none;
            }
            
            #ays-quiz-container-" . $id . " .wrong_answer_text{
                color:#ff4d4d;
            }
            #ays-quiz-container-" . $id . " .right_answer_text{
                color:#33cc33;
            }
            #ays-quiz-container-" . $id . " .ays_cb_and_a,
            #ays-quiz-container-" . $id . " .ays_cb_and_a * {
                color: " . Quiz_Maker_Data::hex2rgba($text_color) . ";
            }

            #ays-quiz-container-" . $id . " iframe {
                min-height: " . $quiz_height . "px;
            }

            #ays-quiz-container-" . $id . " label.ays_for_checkbox,
            #ays-quiz-container-" . $id . " span.ays_checkbox_for_span {
                color: initial !important;
                display: block;
            }


            /* Quiz textarea height */
            #ays-quiz-container-" . $id . " textarea {
                height: ". $quiz_textarea_height ."px;
                min-height: ". $quiz_textarea_height ."px;
            }

            /* Quiz rate and passed users count */
            #ays-quiz-container-" . $id . " .ays_quizn_ancnoxneri_qanak,
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg{
                color:" . $bg_color . ";
                background-color:" . $text_color . ";                                        
            }
            #ays-quiz-container-" . $id . " div.for_quiz_rate.ui.star.rating .icon {
                color: " . Quiz_Maker_Data::hex2rgba($text_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg div.for_quiz_rate_avg.ui.star.rating .icon {
                color: " . Quiz_Maker_Data::hex2rgba($bg_color, '0.5') . ";
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

            /* Stars */
            #ays-quiz-container-" . $id . " .ui.rating .icon,
            #ays-quiz-container-" . $id . " .ui.rating .icon:before {
                font-family: Rating !important;
            }

            /* Progress bars */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress {
                border-color: " . Quiz_Maker_Data::hex2rgba($text_color, '0.8') . ";
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress-bg {
                background-color: " . Quiz_Maker_Data::hex2rgba($text_color, '0.3') . ";
            }";

        if ($enable_live_progress_bar) {
            $quiz_styles .= "

            #ays-quiz-container-" . $id . " .$filling_type {
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .$filling_type_wrap {
                background-color: " . $text_color . ";
            }";
        }

        if ($quiz_image_height != '' && $quiz_image_height > 0) {
            $quiz_styles .= "
            /* Quiz image */
            #ays-quiz-container-" . $id . " .ays_quiz_image{
                height: " . $quiz_image_height . "px;
            }";
        }

        if ($quiz_bg_img_on_start_page) {
            if($enable_background_gradient) {
                $ays_quiz_bg_style_value = "background-image: linear-gradient(". $quiz_gradient_direction .", ". $background_gradient_color_1 .", ". $background_gradient_color_2 .");";
            }else {
                $ays_quiz_bg_style_value = "background-image: unset";
            }

            $quiz_styles .= "
            div#ays-quiz-container-" . $id . ".ays_quiz_hide_bg_on_start_page {
                " . $ays_quiz_bg_style_value . ";
            }";
        }

        $quiz_styles .= "
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
                border-bottom: 2px solid " . Quiz_Maker_Data::hex2rgba($text_color, '0.8') . ";
                text-shadow: 0px 0px 5px " . $bg_color . ";
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-percent{
                display:none;
            }
            
            
            /* Music, Sound */
            #ays-quiz-container-" . $id . " .ays_music_sound {
                color:" . Quiz_Maker_Data::hex2rgba($text_color) . ";
            }

            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " blockquote {
                border-left-color: " . $text_color . " !important;                                      
            }


            /* Question hint */
            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text {
                background-color:" . $bg_color . ";
                box-shadow: 0 0 15px 3px " . Quiz_Maker_Data::hex2rgba($box_shadow_color, '0.6') . ";
            }

            /* Information form */
            #ays-quiz-container-" . $id . " .ays-form-title{
                color:" . Quiz_Maker_Data::hex2rgba($text_color) . ";
            }

            /* Quiz timer */
            #ays-quiz-container-" . $id . " div.ays-quiz-redirection-timer,
            #ays-quiz-container-" . $id . " div.ays-quiz-timer{
                color: " . $text_color . ";
            }
            
            /* Quiz buttons */
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button {
                background-color: " . $color . ";
                color:" . $buttons_text_color . ";
                font-size: " . $buttons_font_size . ";
                padding: " . $buttons_top_bottom_padding . " " . $buttons_left_right_padding . ";
                border-radius: " . $buttons_border_radius . ";
                white-space: nowrap;
                letter-spacing: 0;
                box-shadow: unset;
            }
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " input.action-button {
                " . $buttons_width_html . "
            }


            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " a[class~=ajax_add_to_cart]{
                background-color: " . $color . ";
                color:" . $buttons_text_color . ";
                padding: 10px 5px;
                font-size: 14px;
                border-radius: " . $buttons_border_radius . ";
                white-space: nowrap;
                " . $buttons_width_html . "
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_check_answer {
                padding: 5px 10px;
                font-size: " . $buttons_font_size . " !important;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_download_certificate {
                white-space: nowrap;
                padding: 5px 10px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_arrow {
                color:". $buttons_text_color ."!important;
                white-space: nowrap;
                padding: 5px 10px;
            }
            #ays-quiz-container-" . $id . " input#ays-submit:hover,
            #ays-quiz-container-" . $id . " input#ays-submit:focus,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:hover,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:focus {
                box-shadow: 0 0 0 2px $buttons_text_color;
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .ays_restart_button {
                color: " . $buttons_text_color . ";
            }
            #ays-quiz-container-" . $id . " .ays_buttons_div {
                justify-content: " . $buttons_position . ";
            }
            #ays-quiz-container-" . $id . " .step:first-of-type .ays_buttons_div {
                justify-content: center !important;
            }

            #ays-quiz-container-" . $id . " input[type='button'],
            #ays-quiz-container-" . $id . " input[type='submit'] {
                color: " . $buttons_text_color . " !important;
                outline: none;
            }

            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_early_finish.action-button[disabled]:hover,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_early_finish.action-button[disabled]:focus,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_early_finish.action-button[disabled],
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_arrow.action-button[disabled]:hover,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_arrow.action-button[disabled]:focus,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " i.ays_arrow.action-button[disabled] {
                color: #aaa !important;
            }

            #ays-quiz-container-" . $id . " .ays_finish.action-button{
                margin: 10px 5px;
            }

            #ays-quiz-container-" . $id . " .ays-share-btn.ays-share-btn-branded {
                color: #fff;
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
            ";

        if (! $disable_hover_effect) {
            $quiz_styles .= "

            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field:hover{
                opacity: 1;
            }";
        } else{
            $quiz_styles .= "
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field{
                opacity: 1;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_elegant_light .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_light .ays-quiz-answers .ays-field,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_dark .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_dark .ays-quiz-answers .ays-field{
                opacity: 0.6;
            }";
        }

        $quiz_styles .= "
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
                background-color: " . Quiz_Maker_Data::hex2rgba($color, '0.6') . ";
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item label.ays_answer_caption[for^='ays-answer-']:hover {
                background-color: " . Quiz_Maker_Data::hex2rgba($color, '1') . ";
            }";
        }
        $quiz_styles .= "
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field input~label[for^='ays-answer-'] {
                padding: " . $answers_padding . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field {
                margin-bottom: " . ($answers_margin) . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item {
                width: calc(50% - " . ($answers_margin / 2) . "px);
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
            
            /* Answer maximum length of a text field */
            #ays-quiz-container-" . $id . " .ays_quiz_question_text_message{
                color: " . $text_color . ";
                text-align: left;
                font-size: 12px;
            }

            div#ays-quiz-container-" . $id . " div.ays_quiz_question_text_error_message {
                color: #ff0000;
            }


            /* Questions answer image */
            #ays-quiz-container-" . $id . " .ays-answer-image {
                width:" . ($answer_view_class == "grid" ? "100%" : "15em") . ";
                height:" . $ans_img_height . ";
                object-fit: " . $answers_object_fit . ";
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

            #ays-quiz-container-" . $id . " .select2-container--default .select2-search--dropdown .select2-search__field:focus,
            #ays-quiz-container-" . $id . " .select2-container--default .select2-search--dropdown .select2-search__field {
                outline: unset;
            }

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
            
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .selection,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .dropdown-wrapper,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered .select2-selection__placeholder,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow b[role='presentation'] {
                font-size: 16px !important;
            }


            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar {
                width: 7px;
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-track {
                background-color: " . Quiz_Maker_Data::hex2rgba($bg_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb {
                transition: .3s ease-in-out;
                background-color: " . Quiz_Maker_Data::hex2rgba($bg_color, '0.55') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb:hover {
                transition: .3s ease-in-out;
                background-color: " . Quiz_Maker_Data::hex2rgba($bg_color, '0.85') . ";
            }
            
            /* WooCommerce product */
            #ays-quiz-container-" . $id . " .ays-woo-block {
                background-color: " . $color . ";
            }

            #ays-quiz-container-" . $id . " .ays-woo-product-block h4.ays-woo-product-title > a {
                color: " . $text_color . ";
            }

            /* Audio / Video */
            #ays-quiz-container-" . $id . " .mejs-container .mejs-time{
                box-sizing: unset;
            }
            #ays-quiz-container-" . $id . " .mejs-container .mejs-time-rail {
                padding-top: 15px;
            }

            /* Hestia theme (Version: 3.0.16) | Start */
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button:hover,
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button {
                box-shadow: unset;
                background-color: transparent;
            }
            /* Hestia theme (Version: 3.0.16) | End */

            /* Go theme (Version: 1.4.3) | Start */
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before,
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before {
                -webkit-mask-image: unset;
                mask-image: unset;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field input:checked+label.answered.correct:before,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field input:checked+label.answered.correct:before {
                background-color: ". $color ." !important;
            }
            /* Go theme (Version: 1.4.3) | End */

            #ays-quiz-container-" . $id . " .ays_quiz_results fieldset.ays_fieldset .ays_quiz_question .wp-video {
                width: 100% !important;
                max-width: 100%;
            }

            /* Classic Dark / Classic Light */
            /* Dropdown questions right/wrong styles */
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .correct_div,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .correct_div{
                border-color: green !important;
                opacity: 1 !important;
                background-color: rgba(39,174,96,0.4) !important;
            }
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .correct_div .selected-field,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .correct_div .selected-field {
                padding: 0px 10px 0px 10px;
                color: green !important;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .wrong_div,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .wrong_div{
                border-color: red !important;
                opacity: 1 !important;
                background-color: rgba(243,134,129,0.4) !important;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field.checked_answer_div.wrong_div input:checked~label,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field.checked_answer_div.wrong_div input:checked~label {
                background-color: rgba(243,134,129,0.4) !important;
            }

            #ays-quiz-container-" . $id . " .ays_question_result .ays-field .ays_quiz_hide_correct_answer:after{
                content: '' !important;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-close-full-screen {
                fill: $text_color;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-open-full-screen {
                fill: $text_color;
            }


            @media screen and (max-width: 768px){
                #ays-quiz-container-" . $id . "{
                    max-width: $mobile_max_width;
                }
                div#ays-quiz-container-" . $id . ".ays_quiz_modern_light .step,
                div#ays-quiz-container-" . $id . ".ays_quiz_modern_dark .step {
                    padding-right: 0px !important;
                    padding-top: 0px !important;
                }

                div#ays-quiz-container-" . $id . ".ays_quiz_modern_light div.step[data-question-id],
                div#ays-quiz-container-" . $id . ".ays_quiz_modern_dark div.step[data-question-id] {
                    background-size: cover !important;
                    background-position: center center !important;
                }

                div#ays-quiz-container-" . $id . ".ays_quiz_modern_light .ays-abs-fs:not(.ays-start-page):not(.ays-end-page),
                div#ays-quiz-container-" . $id . ".ays_quiz_modern_dark .ays-abs-fs:not(.ays-start-page):not(.ays-end-page) {
                    width: 100%;
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
            case 'classic_dark':
                $quiz_theme = "ays_quiz_classic_dark";
                break;
            case 'classic_light':
                $quiz_theme = "ays_quiz_classic_light";
                break;
            default:
                $quiz_theme = "ays_quiz_classic_dark";
                break;
        }
        
        $custom_class = isset($options['custom_class']) && $options['custom_class'] != "" ? $options['custom_class'] : "";
        
        $quiz_gradient = '';
		if($enable_background_gradient){
			$quiz_gradient = " data-bg-gradient='linear-gradient($quiz_gradient_direction, $background_gradient_color_1, $background_gradient_color_2)' ";
		}

        $options['enable_full_screen_mode'] = isset($options['enable_full_screen_mode']) ? $options['enable_full_screen_mode'] : 'off';
        $enable_full_screen_mode = (isset($options['enable_full_screen_mode']) && $options['enable_full_screen_mode'] == "on") ? true : false;

        $fullcsreen_mode = '';

        if($enable_full_screen_mode){
            $fullcsreen_mode = '<div class="ays-quiz-full-screen-wrap">
                <a class="ays-quiz-full-screen-container">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" fill="#fff" viewBox="0 0 24 24" width="24" class="ays-quiz-close-full-screen">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" fill="#fff" viewBox="0 0 24 24" width="24" class="ays-quiz-open-full-screen">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </a>
            </div>';
        }else {
            $fullcsreen_mode = '';
        }

        $quiz_container_first_part = "
            <div class='ays-quiz-container ".$quiz_theme." ".$quiz_bg_img_class." ".$custom_class."'
                 data-quest-effect='".$quest_animation."' ".$quiz_gradient."
                 data-hide-bg-image='".$quiz_bg_img_in_finish_page."'
                 id='ays-quiz-container-" . $id . "'>
                {$live_progress_bar}
                {$ays_quiz_music_html}
                <div class='ays-questions-container'>
                    {$fullcsreen_mode}
                    $ays_quiz_reports
                    <form 
                        action='' 
                        method='post' 
                        autocomplete='off'
                        id='ays_finish_quiz_" . $id . "' 
                        class='" . $correction_class . " " . $enable_questions_result . " " . $enable_logged_users . "'
                    >";
        if($question_per_page && $question_count_per_page > 0){
            if($question_count_per_page > $questions_count){
                $question_count_per_page = $questions_count;
            }
            $quiz_container_first_part .= "<input type='hidden' class='ays_question_count_per_page' value='$question_count_per_page'>";
        }
        
        $quiz_container_first_part .= "
            <input type='hidden' value='" . $answer_view_class . "' class='answer_view_class'>
            <input type='hidden' value='" . $enable_arrows . "' class='ays_qm_enable_arrows'>";
        
        if ( $limit_users_res_id ) {
            $quiz_container_first_part .= "<input type='hidden' value='' name='ays_quiz_result_row_id' class='ays_quiz_result_row_id'>";
        }

        $quiz_container_middle_part = "";
        
        if( $payments['paypal']['html'] !== null && $payments['stripe']['html'] !== null ){
            if(is_user_logged_in()){
                $quiz_container_middle_part .= $payments['paypal']['html'];
                $quiz_container_middle_part .= "<br>";
                $quiz_container_middle_part .= $payments['stripe']['html'];
                $main_content_first_part = "";
                $main_content_last_part = "";
            }else{
                switch( $payments['paypal']['payment_terms'] ){
                    case "onetime":
                        $quiz_container_middle_part .= $payments['paypal']['html'];
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                    case "lifetime":
                        $quiz_container_middle_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                }

                $quiz_container_middle_part .= "<br>";

                switch( $payments['stripe']['payment_terms'] ){
                    case "onetime":
                        $quiz_container_middle_part .= $payments['stripe']['html'];
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                    case "lifetime":
                        $quiz_container_middle_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                        $main_content_first_part = "";
                        $main_content_last_part = "";
                    break;
                }
            }
        }else{
            if( ( isset( $_SESSION['ays_quiz_paypal_purchase'] ) && $_SESSION['ays_quiz_paypal_purchase'] === true ) ||
                ( isset( $_SESSION['ays_quiz_stripe_purchase'] ) && $_SESSION['ays_quiz_stripe_purchase'] === true ) ){
            }else{
                if($payments['paypal']['html'] !== null){
                    if(is_user_logged_in()){
                        if($payments['paypal']['html'] == ''){
                            $quiz_container_middle_part = __("It seems PayPal Client ID is missing.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                        }else{
                            $quiz_container_middle_part = $payments['paypal']['html'];
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                        }
                    }else{
                        switch( $payments['paypal']['payment_terms'] ){
                            case "onetime":
                                $quiz_container_middle_part = $payments['paypal']['html'];
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

                if($payments['stripe']['html'] !== null){
                    if(is_user_logged_in()){
                        if($payments['stripe']['html'] == ''){
                            $quiz_container_middle_part = __("It seems Stripe API key or Secret key is missing.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                        }else{
                            $quiz_container_middle_part = $payments['stripe']['html'];
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                        }
                    }else{
                        switch( $payments['stripe']['payment_terms'] ){
                            case "onetime":
                                $quiz_container_middle_part = $payments['stripe']['html'];
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
            }
        }

        if($is_expired){
            $quiz_container_middle_part = $expired_quiz_message;
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        if($enable_tackers_count){
            $quiz_tackers_count = Quiz_Maker_Data::get_quiz_tackers_count($id);
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
            'questionsHint' => $questions_hint_arr,
            'isRequired' => $make_questions_required,
            'show_answers_numbering' => $show_answers_numbering,
            'disable_hover_effect' => $disable_hover_effect,
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
                "buttonsTextColor" => $buttons_text_color,
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

        // Disable answer hover
        $options['disable_hover_effect'] = isset($options['disable_hover_effect']) ? $options['disable_hover_effect'] : 'off';
        $disable_hover_effect = (isset($options['disable_hover_effect']) && $options['disable_hover_effect'] == "on") ? true : false;

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
                    background-color: " . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.6') . ";
                }";

        if (! $disable_hover_effect) {
            $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " .ays-field:hover{
                    background: " . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.8') . ";
                    color: #fff;
                    transition: all .3s;
                }";
        }

        $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:hover,
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:focus {
                    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5), 0 0 0 3px " . $quiz->quizColors['buttonsTextColor'] . ";
                    background: " . $quiz->quizColors['Color'] . ";
                }
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        
        $container = implode("", $quiz->quizParts);
        
        return $container;
    }

    public function get_quiz_questions($ids, $quiz_id, $options, $per_page){
        
        $container = $this->ays_questions_parts($ids, $quiz_id, $options, $per_page);
        $questions_container = array();
        foreach($container as $key => $question){
            $answer_container = '';
            $use_html = Quiz_Maker_Data::in_question_use_html($question['questionID']);
            switch ($question["questionType"]) {
                case "select":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'show_answers_numbering' => $options['show_answers_numbering'],
                    );
                    $answer_container .= $this->ays_dropdown_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "text":
                    $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'questionMaxLengthArray' => $question_max_length_array,
                    );
                    $answer_container .= $this->ays_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "short_text":
                    $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'questionMaxLengthArray' => $question_max_length_array,
                    );
                    $answer_container .= $this->ays_short_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "number":
                    $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'questionMaxLengthArray' => $question_max_length_array,
                    );
                    $answer_container .= $this->ays_number_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "date":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                    );
                    $answer_container .= $this->ays_date_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "custom":
                    break;
                default:
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'rtlDirection' => $options['rtlDirection'],
                        'questionType' => $question["questionType"],
                        'answersViewClass' => $options['answersViewClass'],
                        'useHTML' => $use_html,
                        'show_answers_numbering' => $options['show_answers_numbering'],
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
        $total_show = count($ids);
        $container = array();
        $buttons = $options['buttons'];
        $enable_arrows = $buttons['enableArrows'];
        $quiz_arrow_type = $buttons['quizArrowType'];
        $settings_buttons_texts = $this->buttons_texts;
        $is_required = $options['isRequired'] ? 'true' : 'false';
        
        $questions_show_count = array();

        foreach($ids as $key => $id){
            if(Quiz_Maker_Data::is_question_type_a_custom($id)){
                $total_show--;
            }else{
                $questions_show_count[] = $id;
            }
        }

        $current_show = '';
        foreach($ids as $key => $id){
            $current = $key + 1;
            if(in_array($id, $questions_show_count)){
                $current_show = array_search($id, $questions_show_count) + 1;
            }
            if($total == $current){
                $last = true;
            }else{
                $last = false;
            }
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;
            $question = $wpdb->get_row($sql, 'ARRAY_A');

            if (!empty($question)) {
                $answers = Quiz_Maker_Data::get_answers_with_question_id($question["id"]);
                $question_options = (isset($question['options']) && sanitize_text_field( $question['options'] ) != '') ? json_decode( sanitize_text_field( $question['options'] ) , true ) : array();
                $question_image = '';
                $question_image_style = '';
                $question_category = '';
                $show_question_category = $options['showQuestionCategory'];
                if($show_question_category){
                    $question_category_data = Quiz_Maker_Data::get_question_category_by_id($question['category_id']);
                    $question_category = $question_category_data['title'];

                    $question_category = "<p style='margin:0!important;text-align:left;'>
                        <em style='font-style:italic;font-size:0.8em;'>". __("Category", $this->plugin_name) .":</em>
                        <strong style='font-size:0.8em;'>{$question_category}</strong>
                    </p>";
                }
                $question_options = json_decode($question['options'], true) !== null ? json_decode($question['options'], true) : array();
                if( !is_array( $question_options ) ){
                    $question_options = array();
                }
                
                $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on') ? true : false;
                
                // Hide question text on the front-end
                $question_options['quiz_hide_question_text'] = isset($question_options['quiz_hide_question_text']) ? sanitize_text_field( $question_options['quiz_hide_question_text'] ) : 'off';
                $quiz_hide_question_text = (isset($question_options['quiz_hide_question_text']) && $question_options['quiz_hide_question_text'] == 'on') ? true : false;

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
                    $question_hint_arr = $options['questionsHint'];
                    $icon_or_text = $options['questionsHint']['questionsHintIconOrText'];
                    $question_text_value = $options['questionsHint']['questionsHintValue'];

                    $questions_hint_content = "<i class='ays_fa ays_fa_info_circle ays_question_hint' aria-hidden='true'></i>";
                    if ($icon_or_text) {
                        if ($question_text_value != '') {
                            $questions_hint_content = '<p class="ays_question_hint">'. $question_text_value .'</p>';
                        }
                    }

                    $question_hint = Quiz_Maker_Data::ays_autoembed( $question['question_hint'] );
                    $question_hint = "
                    <div class='ays_question_hint_container'>
                        ".$questions_hint_content."
                        <span class='ays_question_hint_text'>" . $question_hint . "</span>
                    </div>";
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
                    $questions_counter = "<p class='ays-question-counter animated'>{$current_show} / {$total_show}</p>";
                }else{
                    $questions_counter = "";
                }
                
                $early_finish = "";                
                if($buttons['earlyButton']){
                    $early_finish = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_flag_checkered ays_early_finish action-button ays_arrow'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_early_finish action-button' value='" . $settings_buttons_texts['finishButton'] . "'/>";
                }
                
                $clear_answer = "";                
                if($buttons['clearAnswerButton']){
                    $clear_answer = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_eraser ays_clear_answer action-button ays_arrow'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_clear_answer action-button' value='" . $settings_buttons_texts['clearButton'] . "'/>";
                }
                if($options['correction']){
                    $clear_answer = "";
                }
                
                switch( $quiz_arrow_type ){
                    case 'default':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_left";
                        break;
                    case 'long_arrow':
                        $quiz_arrow_type_class_right = "ays_fa_long_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_long_arrow_left";
                        break;
                    case 'arrow_circle_o':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_circle_o_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_circle_o_left";
                        break;
                    case 'arrow_circle':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_circle_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_circle_left";
                        break;
                    default:
                        $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_left";
                        break;
                }

                if ($last) {
                    switch($options['informationForm']){
                        case "disable":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "before":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "after":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ". $quiz_arrow_type_class_right ." ays_finish action-button ays_arrow ays_next_arrow'></i><input type='button' name='next' class=' " . $buttons['nextButton'] . " ays_next action-button' value='" . $settings_buttons_texts['finishButton'] . "' />";
                            break;
                        default:
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;                        
                    }
                    $buttons_div = "<div class='ays_buttons_div'>
                            {$clear_answer}
                            <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                            <input type='button' name='next' class='ays_previous action-button " . $buttons['prevButton'] . "'  value='". $settings_buttons_texts['previousButton'] ."' />
                            {$input}
                        </div>";
                }else{
                    $buttons_div = "<div class='ays_buttons_div'>
                        {$clear_answer}
                        <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_previous action-button " . $buttons['prevButton'] . "' value='". $settings_buttons_texts['previousButton'] ."' />
                        " . $early_finish . "
                        <i class=\"ays_fa ". $quiz_arrow_type_class_right ." ays_next action-button ays_arrow ays_next_arrow " . $buttons['nextArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_next action-button " . $buttons['nextButton'] . "' value='" . $settings_buttons_texts['nextButton'] . "' />
                    </div>";
                }
                
                $additional_css = "";
                $answer_view_class = $options['answersViewClass'];
                
                $question_bg_image = (isset($question_options['bg_image']) && $question_options['bg_image'] != "") ? $question_options['bg_image'] : null;
                $question_bg_class = ($question_bg_image !== null) ? "ays-quiz-question-with-bg" : "";
                
                $question_content = Quiz_Maker_Data::ays_autoembed( $question['question'] );

                if ( $quiz_hide_question_text ) {
                    $question_content = '';
                }

                switch ($options['quizTheme']) {
                    case 'elegant_dark':
                    case 'elegant_light':
                    case 'rect_dark':
                    case 'rect_light':
                        $question_html = "<div class='ays_quiz_question'>
                                " . $question_content . "
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
                                " . $question_content . "
                            </div>";
                        $answer_view_class = "ays_list_view_container";
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
                                " . $question_content . "
                            </div>";
                        $answer_view_class = "ays_list_view_container";
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
                                " . $question_content . "
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

                if ($question['type'] === 'custom') {
                    $container_first_part = "
                        <div class='step ays-custom-step ".$question_bg_class."'
                             data-question-id='" . $question["id"] . "'
                             data-type='" . $question["type"] . "'>
                            <div class='ays-abs-fs'>
                                {$question_html}";

                    $container_last_part = "
                            {$buttons_div}
                            {$additional_css}
                        </div>
                    </div>";
                }else{
                    $container_first_part = "<div class='step ".$question_bg_class." ".$not_influence_to_score_class."'
                        data-question-id='" . $question["id"] . "'
                        data-type='" . $question["type"] . "'
                        data-required='" . $is_required . "'>
                        {$question_hint}
                        {$questions_counter}
                        <div class='ays-abs-fs'>
                            {$question_category}
                            {$question_html}
                            <div class='ays-quiz-answers $answer_view_class'>";

                    $required_question_message = '';
                    if( $options['isRequired'] ){
                        $required_question_message = '<div class="ays-quiz-question-validation-error" role="alert"></div>';
                    }

                    $wrong_answer_text = Quiz_Maker_Data::ays_autoembed( $question['wrong_answer_text'] );
                    $right_answer_text = Quiz_Maker_Data::ays_autoembed( $question['right_answer_text'] );
                    $explanation = Quiz_Maker_Data::ays_autoembed( $question['explanation'] );
                    $container_last_part = "</div>
                            {$user_explanation}
                            {$buttons_div}
                            {$required_question_message}
                            <div class='wrong_answer_text $wrong_answer_class' style='display:none'>
                                " . $wrong_answer_text . "
                            </div>
                            <div class='right_answer_text $right_answer_class' style='display:none'>
                                " . $right_answer_text . "
                            </div>
                            <div class='ays_questtion_explanation' style='display:none'>
                                " . $explanation . "
                            </div>
                            {$additional_css}
                        </div>
                    </div>";
                }

//                $container_first_part = "<div class='step ".$question_bg_class." ".$not_influence_to_score_class."' data-question-id='" . $question["id"] . "'>
//                    {$question_hint}
//                    {$questions_counter}
//                    <div class='ays-abs-fs'>
//                        {$question_category}
//                        {$question_html}
//                        <div class='ays-quiz-answers $answer_view_class'>";
//
//                $wrong_answer_text = Quiz_Maker_Data::ays_autoembed( $question['wrong_answer_text'] );
//                $right_answer_text = Quiz_Maker_Data::ays_autoembed( $question['right_answer_text'] );
//                $explanation = Quiz_Maker_Data::ays_autoembed( $question['explanation'] );
//                $container_last_part = "</div>
//                        {$user_explanation}
//                        {$buttons_div}
//                        <div class='wrong_answer_text $wrong_answer_class' style='display:none'>
//                            " . $wrong_answer_text . "
//                        </div>
//                        <div class='right_answer_text $right_answer_class' style='display:none'>
//                            " . $right_answer_text . "
//                        </div>
//                        <div class='ays_questtion_explanation' style='display:none'>
//                            " . $explanation . "
//                        </div>
//                        {$additional_css}
//                    </div>
//                </div>";
                
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
        } else {
            global $wpdb;

            $limited_result_id = (isset($_REQUEST['ays_quiz_result_row_id']) && $_REQUEST['ays_quiz_result_row_id'] != '') ? absint(intval( $_REQUEST['ays_quiz_result_row_id'] )) : null;
            $questions_answers = (isset($_REQUEST["ays_questions"])) ? $_REQUEST['ays_questions'] : array();
            $questions_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            $questions_answers = Quiz_Maker_Data::sort_array_keys_by_array($questions_answers, $questions_ids);

            $quiz = Quiz_Maker_Data::get_quiz_by_id($quiz_id);
            $quiz_intervals = json_decode($quiz['intervals']);
            $quiz_attributes = Quiz_Maker_Data::get_quiz_attributes_by_id($quiz_id);
            $options = json_decode($quiz['options']);
            if( is_array( $options ) ){
                $options = (object) $options;
            }

            $quiz_image = "";
            if( isset($quiz['quiz_image']) && $quiz['quiz_image'] != "" ){
                $quiz_image = $quiz['quiz_image'];
            }elseif( isset($options->quiz_bg_image) && $options->quiz_bg_image != "" ){
                $quiz_image = $options->quiz_bg_image;
            }

            $quiz_questions_count = Quiz_Maker_Data::get_quiz_questions_count($quiz_id);

            //if (isset($options->enable_question_bank) && $options->enable_question_bank == "on" && isset($options->questions_count) && intval($options->questions_count) > 0 && count($quiz_questions_count) > intval($options->questions_count)) {
                $question_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            //} else {
            //    $question_ids = Quiz_Maker_Data::get_quiz_questions_count($quiz_id);
            //}
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

            // Send Mail to the site Admin too
            $options->send_mail_to_site_admin = ! isset( $options->send_mail_to_site_admin ) ? 'on' : $options->send_mail_to_site_admin;
            $send_mail_to_site_admin = (isset($options->send_mail_to_site_admin) && $options->send_mail_to_site_admin == 'on') ? true : false;

            // Send mail to USER by pass score
            $options->enable_send_mail_to_user_by_pass_score = ! isset( $options->enable_send_mail_to_user_by_pass_score ) ? 'off' : sanitize_text_field( $options->enable_send_mail_to_user_by_pass_score );
            $enable_send_mail_to_user_by_pass_score = (isset($options->enable_send_mail_to_user_by_pass_score) && $options->enable_send_mail_to_user_by_pass_score == 'on') ? true : false;

            // Send mail to ADMIN by pass score
            $options->enable_send_mail_to_admin_by_pass_score = ! isset( $options->enable_send_mail_to_admin_by_pass_score ) ? 'off' : sanitize_text_field( $options->enable_send_mail_to_admin_by_pass_score );
            $enable_send_mail_to_admin_by_pass_score = (isset($options->enable_send_mail_to_admin_by_pass_score) && $options->enable_send_mail_to_admin_by_pass_score == 'on') ? true : false;

            // Information form
            $information_form = (isset($options->information_form) && $options->information_form != '') ? $options->information_form : 'disable';

            // Allow collecting logged in users data
            $options->allow_collecting_logged_in_users_data = isset($options->allow_collecting_logged_in_users_data) ? $options->allow_collecting_logged_in_users_data : 'off';
            $allow_collecting_logged_in_users_data = (isset($options->allow_collecting_logged_in_users_data) && $options->allow_collecting_logged_in_users_data == 'on') ? true : false;

            // Send certificate to admin too
            $options->send_certificate_to_admin = isset($options->send_certificate_to_admin) ? $options->send_certificate_to_admin : 'off';
            $send_certificate_to_admin = (isset($options->send_certificate_to_admin) && $options->send_certificate_to_admin == 'on') ? true : false;

            //Pass score count
            $pass_score_count = (isset($options->pass_score) && $options->pass_score != '') ? absint(intval($options->pass_score)) : 0;

            // Display Interval by
            $display_score_by = (isset($options->display_score_by) && $options->display_score_by != '') ? $options->display_score_by : 'by_percentage';

            // Show information form to logged in users
            $options->show_information_form = isset($options->show_information_form) ? $options->show_information_form : 'on';
            $show_information_form = (isset($options->show_information_form) && $options->show_information_form == 'on') ? true : false;

            // Pass Score Text
            $pass_score_message = '';
            if(isset($options->pass_score_message) && $options->pass_score_message != ''){
                $pass_score_message = Quiz_Maker_Data::ays_autoembed($options->pass_score_message);
            }else{
                $pass_score_message = '<h4 style="text-align: center;">'. __("Congratulations!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You passed the quiz!", $this->plugin_name) .'</p>';
            }

            // Fail Score Text
            $fail_score_message = '';
            if(isset($options->fail_score_message) && $options->fail_score_message != ''){
                $fail_score_message = Quiz_Maker_Data::ays_autoembed($options->fail_score_message);
            }else{
                $fail_score_message = '<h4 style="text-align: center;">'. __("Oops!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You are not passed the quiz! <br> Try again!", $this->plugin_name) .'</p>';
            }

            if($allow_collecting_logged_in_users_data){
                if($information_form == 'disable'){
                    $user = wp_get_current_user();
                    if($user->ID != 0){
                        $_REQUEST['ays_user_email'] = $user->data->user_email;
                        $_REQUEST['ays_user_name'] = $user->data->display_name;
                    }
                }
            }

            if(! $show_information_form){
                if($information_form !== 'disable'){
                    $user = wp_get_current_user();
                    if($user->ID != 0){
                        $_REQUEST['ays_user_email'] = $user->data->user_email;
                        $_REQUEST['ays_user_name'] = $user->data->display_name;
                    }
                }
            }

            // Check RTL direction
            $enable_rtl_direction = (isset($options->enable_rtl_direction) && $options->enable_rtl_direction == 'on') ? true : false;

            // MailChimp
            $quiz_settings = $this->settings;
            $mailchimp_res = ($quiz_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mailchimp');
            $mailchimp = json_decode($mailchimp_res, true);
            $mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
            $mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
            
            $enable_mailchimp = (isset($options->enable_mailchimp) && $options->enable_mailchimp == 'on') ? true : false;
            $enable_double_opt_in = (isset($options->enable_double_opt_in) && $options->enable_double_opt_in == 'on') ? true : false;
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

            $zapier_data['Email'] = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $zapier_data['Name']  = isset($_REQUEST['ays_user_name']) ? sanitize_text_field($_REQUEST['ays_user_name']) : "";
            $zapier_data['Phone'] = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field($_REQUEST['ays_user_phone']) : "";

            $zapier_flag = false;
            if($zapier_data['Email'] == "" && $zapier_data['Name'] == "" && $zapier_data['Phone'] == ""){
                $zapier_flag = true;
            }

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

            // Google Sheets
            $google_res           = ($quiz_settings->ays_get_setting('google') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('google');
            $google               = json_decode($google_res, true);
            $enable_google        = (isset($options->enable_google_sheets) && $options->enable_google_sheets == 'on') ? true : false;
            $google_sheet_custom_fields = (isset($options->google_sheet_custom_fields) && $options->google_sheet_custom_fields != '') ? $options->google_sheet_custom_fields : array();
            $sheet_id             = (isset($options->spreadsheet_id) && $options->spreadsheet_id != '') ? $options->spreadsheet_id : '';
            $quiz_id              = (isset($_REQUEST['ays_quiz_id']) && $_REQUEST['ays_quiz_id'] != '') ? $_REQUEST['ays_quiz_id'] : '';
            $google_token         = isset($google['token']) ? $google['token'] : '';
            $google_refresh_token = isset($google['refresh_token']) ? $google['refresh_token'] : '';
            $google_client_id     = isset($google['client']) ? $google['client'] : '';
            $google_client_secret = isset($google['secret']) ? $google['secret'] : '';
            $google_data = array(
                "refresh_token" => $google_refresh_token,
                "client_id"     => $google_client_id,
                "client_secret" => $google_client_secret,
                "sheed_id"      => $sheet_id,
                "custom_fields" => $google_sheet_custom_fields,
                'id'            => $quiz_id,
                'quiz_attributes' => array(),
            );

            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $google_data['quiz_attributes'][$attr->slug] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }

            // General Setting's Options
            $general_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
            $settings_options = json_decode( stripslashes( $general_settings_options ), true );

            // Do not store IP adressess 
            $disable_user_ip = (isset($settings_options['disable_user_ip']) && $settings_options['disable_user_ip'] == 'on') ? true : false;
            
            // Limit user
            $options->limit_users = isset($options->limit_users) ? $options->limit_users : 'off';
            $limit_users = (isset($options->limit_users) && $options->limit_users == 'on') ? true : false;

            // Limit user by
            $limit_users_by = (isset($options->limit_users_by) && $options->limit_users_by != '') ? $options->limit_users_by : 'ip';

            $quiz_max_pass_count = (isset( $options->quiz_max_pass_count ) && $options->quiz_max_pass_count != '') ? absint( intval( $options->quiz_max_pass_count ) ) : 1;

            // Quiz Title
            $quiz_title = (isset($quiz['title']) && $quiz['title'] != '') ? stripslashes( $quiz['title'] ) : '';

            // Keyword Default Max Value
            $keyword_default_max_value = (isset($settings_options['keyword_default_max_value']) && $settings_options['keyword_default_max_value'] != '') ? absint($settings_options['keyword_default_max_value']) : 6;



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
                $quests = array();
                $questions_cats = array();
                $quiz_questions_ids = array();
                $question_bank_by_categories1 = array();

                foreach($questions_answers as $key => $val){
                    $question_id = explode('-', $key)[2];
                    $quiz_questions_ids[] = strval($question_id);
                }

                $questions_categories = Quiz_Maker_Data::get_questions_categories( implode( ',', $quiz_questions_ids ) );
                $quest_s = Quiz_Maker_Data::get_quiz_questions_by_ids($quiz_questions_ids);
                foreach($quest_s as $quest){
                    $quests[$quest['id']] = $quest;
                }

                foreach($quiz_questions_ids as $key => $question_id){
                    $questions_cats[$quests[$question_id]['category_id']][$question_id] = null;
                }

                $keywords_arr = array();
                foreach ($questions_answers as $key => $questions_answer) {
                    $continue = false;
                    $question_id = explode('-', $key)[2];
                    if(Quiz_Maker_Data::is_question_not_influence($question_id)){
                        $questions_count--;
                        $continue = true;
                    }
                    $multiple_correctness = array();
                    $has_multiple = Quiz_Maker_Data::has_multiple_correct_answers($question_id);
                    $is_checkbox = Quiz_Maker_Data::is_checkbox_answer($question_id);
                    $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_max_weight($question_id, $is_checkbox);
                    
                    $user_answered["question_id_" . $question_id] = $questions_answer;

                    if($is_checkbox){
                       $has_multiple = true;
                    }

                    if ($has_multiple) {
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_id) {
                                $multiple_correctness[] = Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, $calculate_score);
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $answer_id);
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                }
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
                                    $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                continue;
                            }
                            if($strong_count_checkbox === false){
                                if(Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score)){
                                    if(!$continue){
                                        $correctness[$question_id] = 1 / intval(Quiz_Maker_Data::count_multiple_correct_answers($question_id));
                                    }
                                }else{
                                    if(!$continue){
                                        $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                    }
                                }
                                $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                            }else{
                                //if(!$continue){
                                //    $correctness[$question_id] = false;
                                //}
                                //$correctness_results["question_id_" . $question_id] = false;

                                if(!$continue){
                                    $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                            }
                            if( intval( $questions_answer ) != 0 ){
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                }
                            }
                        }
                    } elseif(Quiz_Maker_Data::has_text_answer($question_id)) {
                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, $calculate_score);
                    } else {
                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        if( intval( $questions_answer ) != 0 ){
                            $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                            if(!is_null($answer_keyword) && $answer_keyword != false){
                                $keywords_arr[] = $answer_keyword;
                            }
                        }
                    }
                }
                
                $new_correctness = array();
                $quiz_weight_correctness = array();
                $quiz_weight_points = array();
                $corrects_count = 0;
                $corrects_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    $corrects_count_by_cats[$cat_id] = 0;
                    foreach($correctness as $question_id => $item){
                        if( array_key_exists( strval($question_id), $q_ids ) ){
                            switch($calculate_score){
                                case "by_correctness":
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                                case "by_points":
                                    if($item == floatval($answer_max_weights[$question_id])){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                                default:
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                            }
                        }
                    }
                }

                foreach($correctness as $question_id => $item){
                    $question_weight = Quiz_Maker_Data::get_question_weight($question_id);
                    $new_correctness[strval($question_id)] = $question_weight * floatval($item);
                    $quiz_weight_points[strval($question_id)] = $question_weight * floatval($answer_max_weights[$question_id]);
                    $quiz_weight_correctness[strval($question_id)] = $question_weight;
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

                $quiz_weight_new_correctness_by_cats = array();
                $quiz_weight_correctness_by_cats = array();
                $quiz_weight_points_by_cats = array();

                $questions_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    foreach($q_ids as $q_id => &$val){
                        $val = array_key_exists($q_id, $new_correctness) ? $new_correctness[$q_id] : false;
                        $quiz_weight_new_correctness_by_cats[$cat_id][$q_id] = $val;
                        if( Quiz_Maker_Data::is_question_not_influence($q_id) ){
                            continue;
                        }

                        if ( isset( $quiz_weight_correctness[$q_id] ) && sanitize_text_field( $quiz_weight_correctness[$q_id] ) != '' ) {
                            $quiz_weight_correctness_by_cats[$cat_id][$q_id] = $quiz_weight_correctness[$q_id];
                        }
                        if ( isset( $quiz_weight_points[$q_id] ) && sanitize_text_field( $quiz_weight_points[$q_id] ) != '' ) {
                            $quiz_weight_points_by_cats[$cat_id][$q_id] = $quiz_weight_points[$q_id];
                        }

                    }
                    $questions_count_by_cats[$cat_id] = count($q_ids);
                }

                $average_percent = 100 / $questions_count;
                
                $final_score_by_cats = array();
                $quiz_weight_cats = array();
                $correct_answered_count_cats = array();
                $cat_score_is_decimal = false;
                $final_score_is_decimal = false;
                foreach($quiz_weight_new_correctness_by_cats as $cat_id => $q_ids){

                    if ( ! isset( $quiz_weight_correctness_by_cats[$cat_id] ) ) {
                        continue;
                    }
                    $quiz_weight_correctness_by_cats[$cat_id] = array_filter($quiz_weight_correctness_by_cats[$cat_id], "strlen");

                    switch($calculate_score){
                        case "by_correctness":
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                        case "by_points":
                            $quiz_weight_cat = array_sum($quiz_weight_points_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_points_by_cats[$cat_id]);
                        break;
                        default:
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                    }

//                    $correct_answered_count_cat = array_sum($q_ids);
                    $correct_answered_count_cats[$cat_id] = array_sum($q_ids);

                    if($quiz_weight_cat == 0){
                        $final_score_by_cats[$cat_id] = floatval(0);
                    }else{
//                        $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                        $final_score_by_cats[$cat_id] = floatval(floor((intval($correct_answered_count_cats[$cat_id]) / intval($quiz_weight_cat) ) * 100));
                        $final_score_by_cats[$cat_id] = round($final_score_by_cats[$cat_id], 2);
                    }
                }

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
                    $final_score = floatval( ( $correct_answered_count / $quiz_weight ) * 100 );
                    $final_score = round( $final_score, 2 );
                }

                $score_by_cats = array();
                foreach($final_score_by_cats as $cat_id => $cat_score){
                    switch($display_score){
                        case "by_correctness":
                            $score_by_cats[$cat_id] = array(
                                'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_points":
                            $score_by_cats[$cat_id] = array(
//                                'score' => $correct_answered_count_cat[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'score' => $correct_answered_count_cats[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_percentage":
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        default:
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                    }
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

                $wrong_answered_count = $questions_count - $corrects_count;

                $skipped_questions_count = 0;
                foreach ($user_answered as $q_id => $user_answered_val) {
                    $question_id_val = explode('_', $q_id)[2];
                    if(Quiz_Maker_Data::is_question_not_influence($question_id_val)){
                        continue;
                    }

                    if ( $user_answered_val == '') {
                        $skipped_questions_count++;
                    }
                }

                $answered_questions_count = $questions_count - $skipped_questions_count;
                $user_failed_questions_count = $corrects_count + ( $questions_count - ($corrects_count + $skipped_questions_count) );

                if ( ! empty( $user_failed_questions_count ) || $user_failed_questions_count != 0) {
                    $score_by_answered_questions = round( ( $corrects_count * 100 ) / $user_failed_questions_count , 1 );
                } else {
                    $score_by_answered_questions = 0;
                }

                $hide_result = false;
                $finish_text = null;
                $interval_msg = null;
                $interval_message = '';
                $interval_image = null;
                $product_id = null;

                //if (isset($options->enable_result) && $options->enable_result == "on") {
                //    $text = $options->result_text;
                //    $hide_result = true;
                //}
                //if($finish_text == ''){
                //    $finish_text = null;
                //}

                if(empty($score_by_cats)){
                    $result_score_by_categories = '';
                }else{
                    $result_score_by_categories = '<div class="ays_result_by_cats">';
                    foreach($score_by_cats as $cat_id => $cat){
                        $result_score_by_categories .= '<p class="ays_result_by_cat">
                            <strong class="ays_result_by_cat_name">'. $cat['categoryName'] .':</strong>
                            <span class="ays_result_by_cat_score">'. $cat['score'] .'</span>
                        </p>';
                    }
                    $result_score_by_categories .= '</div>';
                    $result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $result_score_by_categories);
                }

                $score_by = ($display_score_by == 'by_percentage') ? $final_score : intval($correct_answered_count);
                $score_by = '';
                switch ($display_score_by) {
                    case 'by_percentage':
                        $score_by = $final_score;
                        break;
                    case 'by_points':
                        $score_by = floatval( $correct_answered_count );
                        break;
                    case 'by_keywords':
                        if( is_array( $keywords_arr ) ){
                            $keywords_count_arr = array_count_values($keywords_arr);
                            $max_keywords_answered_count = max( $keywords_count_arr );
                            $max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $keywords_count_arr );
                            $score_by = $max_keywords_answered_keyword;
                        }else{
                            $score_by = "";
                        }
                        break;
                    default:
                        $score_by = $final_score;
                        break;
                }

                $interval_flag = false;
                foreach ($quiz_intervals as $quiz_interval) {
                    $quiz_interval = (array)$quiz_interval;
                    if($display_score_by == 'by_keywords'){
                        if ($quiz_interval['interval_keyword'] == $score_by) {
                            $interval_flag = true;
                        }
                    }else{
                        if ( floatval( $quiz_interval['interval_min'] ) <= $score_by && $score_by <= floatval( $quiz_interval['interval_max'] ) ) {
                            $interval_flag = true;
                        }
                    }

                    if($interval_flag){
                        $interval_msg = Quiz_Maker_Data::ays_autoembed( $quiz_interval['interval_text'] );
                        $interval_image = $quiz_interval['interval_image'];
                        $product_id = $quiz_interval['interval_wproduct'];

                        $interval_message = "";
                        $intimg = false;
                        $intmsg = false;
                        if($interval_image !== null && $interval_image != ''){
                            $intimg = true;
                            $interval_message .= "<div style='width:100%;max-width:400px;margin:10px auto;'>";
                            $interval_message .= "<img style='max-width:100%;' src='".$interval_image."'>";
                            $interval_message .= "</div>";
                        }
                        if($interval_msg !== null && $interval_msg != ''){
                            $intmsg = true;
                            $interval_message .= "<div>" . $interval_msg . "</div>";
                        }
                        if($intimg || $intmsg){
                            $interval_message = "<div>" . $interval_message . "</div>";
                        }
                        break;
                    }
                }

                
                $correctness_and_answers = array(
                    'correctness' => $correctness_results,
                    'user_answered' => $user_answered
                );

                $not_influence_m = array();
                foreach ($quest_s as $key => $value) {
                    $not_influence_m[] = $quest_s[$key]['not_influence_to_score'];
                }


                $quiz_logo = "";
                if($quiz_image !== ""){
                    $quiz_logo = '<img src="'.$quiz_image.'" alt="Quiz logo" title="Quiz logo">';
                }

                $user_first_name = '';
                $user_last_name = '';
                $user_id = get_current_user_id();
                if($user_id != 0){
                    $usermeta = get_user_meta( $user_id );
                    if($usermeta !== null){
                        $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                        $user_last_name = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                    }
                }

                $result_unique_code = strtoupper( uniqid() );

                $message_data = array(
                    'quiz_name' => stripslashes($quiz['title']),
                    'user_name' => $_REQUEST['ays_user_name'],
                    'user_email' => $_REQUEST['ays_user_email'],
                    'user_pass_time' => Quiz_Maker_Data::get_time_difference(sanitize_text_field($_REQUEST['start_date']), sanitize_text_field($_REQUEST['end_date'])),
                    'quiz_time' => Quiz_Maker_Data::secondsToWords($options->timer),
                    'score' => $final_score . "%",
                    'user_points' => $correct_answered_count,
                    'max_points' => $quiz_weight,
                    'user_corrects_count' => $corrects_count,
                    'questions_count' => $questions_count,
                    'quiz_logo' => $quiz_logo,
                    'avg_score' => Quiz_Maker_Data::ays_get_average_of_scores($quiz_id) . "%",
                    'avg_rate' => round(Quiz_Maker_Data::ays_get_average_of_rates($quiz_id), 1),
                    'current_date' => date_i18n( get_option( 'date_format' ), strtotime( sanitize_text_field( $_REQUEST['end_date'] ) ) ),
                    'results_by_cats' => $result_score_by_categories,
                    'unique_code' => $result_unique_code,
                    'wrong_answers_count' => $wrong_answered_count,
                    'not_answered_count' => $skipped_questions_count,
                    'skipped_questions_count' => $skipped_questions_count,
                    'answered_questions_count' => $answered_questions_count,
                    'score_by_answered_questions' => $score_by_answered_questions,
                    'user_first_name' => $user_first_name,
                    'user_last_name' => $user_last_name,
                );

                $all_mv_keywords_arr = Quiz_Maker_Data::ays_quiz_generate_keyword_array($keyword_default_max_value);
                $mv_keyword_counts = array_count_values($keywords_arr);

                foreach ($all_mv_keywords_arr as $key => $value) {
                    $mv_keyword_percentage = 0;
                    $total_keywords_count = array_sum($mv_keyword_counts);
                    if($total_keywords_count > 0){
                        $mv_keyword_percentage = ( $mv_keyword_counts[$value] / $total_keywords_count ) * 100;
                    }
                    if( array_key_exists( $value, $mv_keyword_counts) ){
                        $message_data[ 'keyword_count_' . $value ] = $mv_keyword_counts[$value];
                        $message_data[ 'keyword_percentage_' . $value ] = round($mv_keyword_percentage,2) .'%';
                    }else{
                        $message_data[ 'keyword_count_' . $value ] = 0;
                        $message_data[ 'keyword_percentage_' . $value ] = 0 . '%';
                    }

                }

                $interval_message_for_cert = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message_for_cert;

                $quiz_attributes_information = array();
                foreach ($quiz_attributes as $attribute) {
                    $attr_value = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
                    $quiz_attributes_information[strval($attribute->name)] = $attr_value;
                    $message_data[$attribute->slug] = $attr_value;
                }

                if($disable_user_ip){
                    $user_ip = '';
                }else{
                    $user_ip = Quiz_Maker_Data::get_user_ip();
                }

                $data = array(
                    'user_ip' => $user_ip,
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
                    'unique_code' => $result_unique_code,
                    'rtl_direction' => $enable_rtl_direction,
                    'started_status' => $limited_result_id,
                    'mv_keywords_counts' => $mv_keyword_counts,
                );
                
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
                }else{
                    $nfrom = "From: " . $uname . " <quiz_maker@".$nsite_url.">";
                }
                if(isset($options->email_config_from_subject) && $options->email_config_from_subject != "") {
                    $subject = stripslashes($options->email_config_from_subject);
                } else {
                    $subject = stripslashes($quiz['title']);
                }
                
                if(isset($options->email_config_replyto_name) && $options->email_config_replyto_name != "") {
                    $replyto_name = stripslashes($options->email_config_replyto_name);
                } else {
                    $replyto_name = '';
                }

                $nreply = "";
                if(isset($options->email_config_replyto_email) && $options->email_config_replyto_email != "") {
                    if(filter_var($options->email_config_replyto_email, FILTER_VALIDATE_EMAIL)){
                        $nreply = "Reply-To: " . $replyto_name . " <".stripslashes($options->email_config_replyto_email).">";
                    }
                }

                $subject = Quiz_Maker_Data::replace_message_variables($subject, $message_data);
                $uname = Quiz_Maker_Data::replace_message_variables($uname, $message_data);
                $replyto_name = Quiz_Maker_Data::replace_message_variables($replyto_name, $message_data);
                
                $send_mail_to_user = isset($options->user_mail) && $options->user_mail == "on" ? true : false;
                $send_mail_to_admin = isset($options->admin_mail) && $options->admin_mail == "on" ? true : false;
                $send_certificate_to_user = isset($options->enable_certificate) && $options->enable_certificate == "on" ? true : false;
                
                // Enable certificate without send
                $options->enable_certificate_without_send = isset( $options->enable_certificate_without_send ) ? $options->enable_certificate_without_send : 'off';
                $enable_certificate_without_send = ( isset( $options->enable_certificate_without_send ) && $options->enable_certificate_without_send == "on" ) ? true : false;

                if( $send_certificate_to_user === true && $enable_certificate_without_send === true ){
                    $send_certificate_to_user = true;
                    $enable_certificate_without_send = false;
                }elseif( $send_certificate_to_user === true && $enable_certificate_without_send === false ){
                    $send_certificate_to_user = true;
                    $enable_certificate_without_send = false;
                }elseif( $send_certificate_to_user === false && $enable_certificate_without_send === true ){
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = true;
                }elseif( $send_certificate_to_user === false && $enable_certificate_without_send === false ){
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = false;
                }else{
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = false;
                }

                $cert = false;
                $force_mail_to_user = false;
                if ($send_certificate_to_user){
                    $cert = true;
                    if($send_mail_to_user){
                        if($options->mail_message == ""){
                            $options->mail_message = "Certificate";
                        }
                    }else{
                        $options->mail_message = "Certificate";
                        $force_mail_to_user = true;
                    }
                    $options->user_mail = "on";
                }

                $send_mail_to_user = isset($options->user_mail) && $options->user_mail == "on" ? true : false;
                
                if( $enable_certificate_without_send === true ){
                    $cert = true;
                }

                $pdf_response = null;
                $pdf_content = null;
                if( $send_mail_to_user || $enable_certificate_without_send ){
                    if($cert && $final_score >= intval($options->certificate_pass)){
                        $cert_title = stripslashes((isset($options->certificate_title)) ? $options->certificate_title : '');
                        $cert_body = Quiz_Maker_Data::ays_autoembed((isset($options->certificate_body)) ? $options->certificate_body : '');
                        $cert_body = Quiz_Maker_Data::ays_autoembed((isset($options->certificate_body)) ? $options->certificate_body : '');
                        $certificate_image = (isset($options->certificate_image) && $options->certificate_image != '') ? $options->certificate_image : '';
                        $certificate_frame = (isset($options->certificate_frame) && $options->certificate_frame != '') ? $options->certificate_frame : 'default';
                        $certificate_orientation = (isset($options->certificate_orientation) && $options->certificate_orientation != '') ? $options->certificate_orientation : 'l';

                        $pdf = new Quiz_PDF_API();
                        $pdfData = array(
                            "type"          => "pdfapi",
                            "cert_title"    => $cert_title,
                            "cert_body"     => $cert_body,
                            "cert_score"    => $final_score,
                            "cert_data"     => $message_data,
                            "cert_user"     => $_REQUEST['ays_user_name'],
                            "cert_quiz"     => stripslashes($quiz['title']),
                            "cert_quiz_id"  => $quiz_id,
                            "cert_image"    => $certificate_image,
                            "cert_frame"    => $certificate_frame,
                            "cert_orientation"    => $certificate_orientation,
                            "current_date"  => current_time( 'Y-m-d H:i:s' ),
                        );
                        $pdf_response = $pdf->generate_PDF($pdfData);
                        $pdf_content = $pdf_response['status'];
                    }
                }

                // Disabling store data in DB
                $download_certificate_html = "";
                if($disable_store_data){
                    if($pdf_response !== null){
                        $cert_file_name = isset($pdf_response['cert_file_name']) ? $pdf_response['cert_file_name'] : null;
                        $cert_file_path = isset($pdf_response['cert_file_path']) ? $pdf_response['cert_file_path'] : null;
                        $cert_file_url = isset($pdf_response['cert_file_url']) ? $pdf_response['cert_file_url'] : null;
                        if($cert_file_name !== null){
                            $data['cert_file_name'] = $cert_file_name;
                        }
                        if($cert_file_path !== null){
                            $data['cert_file_path'] = $cert_file_path;
                        }
                        if($cert_file_url !== null){
                            $data['cert_file_url'] = $cert_file_url;
                            $download_certificate_html = "<div style='text-align:center;'>
                                <a target='_blank' href='" . $cert_file_url . "' class='action-button ays_download_certificate' download='" . $cert_file_name . "'>" . __( "Download your certificate", $this->plugin_name ) . "</a>
                            </div>";
                        }
                    }
                    $result = $this->add_results_to_db($data);
                    $g_last_id = $wpdb->insert_id;
                    $google_data['results_last_id'] = $g_last_id;
                }else{
                    $result = true;
                }

                $last_result_id = $wpdb->insert_id;

                $message_data['avg_score_by_category'] = Quiz_Maker_Data::ays_get_average_score_by_category($quiz_id);
                $message_data['download_certificate'] = $download_certificate_html;
                $interval_message = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message;

                if($enable_send_mail_to_user_by_pass_score){
                    if($final_score < $pass_score_count){
                        $send_mail_to_user = false;
                    }
                }

                if ($send_mail_to_user) {
                    if (isset($_REQUEST['ays_user_email']) && filter_var($_REQUEST['ays_user_email'], FILTER_VALIDATE_EMAIL)) {
                        $message = (isset($options->mail_message)) ? $options->mail_message : '';
                        $message = Quiz_Maker_Data::replace_message_variables($message, $message_data);
                        $message = str_replace('%name%', $_REQUEST['ays_user_name'], $message);
                        $message = str_replace('%score%', $final_score, $message);
                        $message = str_replace('%logo%', $quiz_logo, $message);
                        $message = str_replace('%quiz_name%', stripslashes($quiz['title']), $message);
                        $message = str_replace('%date%', date("Y-m-d", current_time('timestamp')), $message);
                        $message = Quiz_Maker_Data::ays_autoembed( $message );
                        
                        if(! $force_mail_to_user){
                            if($send_interval_msg){
                                $message .= $interval_message;
                            }

                            // Send results to User
                            if ($send_results_user) {
                                $message_content = Quiz_Maker_Data::ays_report_mail_content($data, 'user', $send_results_user);
                                $message .= $message_content;
                            }
                        }
                        
                        $email = $_REQUEST['ays_user_email'];
                        $to = $_REQUEST['ays_user_name'] . " <$email>";

                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();
                        if($cert && $final_score >= intval($options->certificate_pass)){
                            if($pdf_content === true){
                                $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                $attachment = $cert_path;
                            }
                            $sendMail = false;
                            if($force_mail_to_user == true && $pdf_content === true){
                                $sendMail = true;
                            }elseif($force_mail_to_user == false){
                                $sendMail = true;
                            }
                            if($sendMail){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                            if(!$force_mail_to_user){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }
                    }
                }
                
                if($enable_send_mail_to_admin_by_pass_score){
                    if($final_score < $pass_score_count){
                        $send_mail_to_admin = false;
                    }
                }

                if ($send_mail_to_admin) {
                    if (filter_var(get_option('admin_email'), FILTER_VALIDATE_EMAIL)) {

                        $message_content = '';
                        $message_content = (isset($options->mail_message_admin) && $options->mail_message_admin != '') ? $options->mail_message_admin : '';
                        $message_content = Quiz_Maker_Data::replace_message_variables($message_content, $message_data);
                        $message_content = Quiz_Maker_Data::ays_autoembed( $message_content );

                        if($interval_message == ''){
                            $send_interval_msg_to_admin = false;
                        }
                        if($send_interval_msg_to_admin){
                            $message_content .= $interval_message;
                        }
                        if($send_results_admin){
                            $message_content .= Quiz_Maker_Data::ays_report_mail_content($data, 'admin', $send_results_admin);
                        }
                        if(!$send_interval_msg_to_admin && !$send_results_admin){
                            $message_content .= Quiz_Maker_Data::ays_report_mail_content($data, 'admin', null);
                        }

                        $admin_subject = ' '.$data['user_name'].' '.$data['score'].'%';
                        if($data['calc_method'] == 'by_points'){
                            $admin_subject = ' '.$data['user_name'].' '.$data['user_points'].'/'.$data['max_points'];
                        }
                        
                        if ($send_mail_to_site_admin) {
                            $admin_email = get_option('admin_email');
                            $email = "<$admin_email>";
                        }else{
                            $email = "";
                        }

                        $add_emails = "";
                        if(isset($options->additional_emails) && !empty($options->additional_emails)) {
                            if ($send_mail_to_site_admin) {
                                $add_emails = ", ";
                            }
                            $additional_emails = explode(", ", $options->additional_emails);
                            foreach($additional_emails as $key => $additional_email){
                                if($key==count($additional_emails)-1)
                                    $add_emails .= "<$additional_email>";
                                else
                                   $add_emails .= "<$additional_email>, ";
                            }
                        }
                        $to = $email.$add_emails;
                        $subject = stripslashes($quiz['title']).$admin_subject;
                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();

                        if($send_certificate_to_admin){
                            if($cert && $final_score >= intval($options->certificate_pass)){
                                if($pdf_content === true){
                                    $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                    $attachment = $cert_path;
                                }
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }
                        }else{
                            $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                        }
                    }
                }
                
                if($enable_mailchimp && $mailchimp_list != ""){
                    if($mailchimp_username != "" && $mailchimp_api_key != ""){
                        $args = array(
                            "email" => $mailchimp_email,
                            "fname" => $mailchimp_fname,
                            "lname" => $mailchimp_lname,
                            "double_optin" => $enable_double_opt_in
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
                    if(! $zapier_flag){
                        $zresult = $this->ays_add_zapier_transaction($zapier_hook, $zapier_data);
                    }
                }
                
                if ($enable_slack && $slack_token != "") {
                    $sresult = $this->ays_add_slack_transaction($slack_token, $slack_conversation, $slack_data, $quiz['title'], $final_score);
                }
                
                if ($enable_google && $google_token != "") {
                    $sresult = $this->ays_add_google_sheets($google_data);
                }

                if( has_action( 'ays_qm_front_end_integrations' ) ){
                    $integration_args = array();
                    $integration_options = (array)$options;
                    $integration_options['id'] = $quiz_id;
                    $integrations_data = apply_filters('ays_qm_front_end_integrations_options', $integration_args, $integration_options);
                    do_action( "ays_qm_front_end_integrations", $integrations_data, $integration_options, $data );
                }

                if ($final_score >= $pass_score_count) {
                    $score_message = $pass_score_message;
                }else{
                    $score_message = $fail_score_message;
                }

                $final_score_message = "";
                if($pass_score_count > 0){
                    $final_score_message = Quiz_Maker_Data::replace_message_variables($score_message, $message_data);
                }

                $finish_text = (isset($options->final_result_text) && $options->final_result_text != '') ? Quiz_Maker_Data::ays_autoembed( $options->final_result_text ) : '';
                $finish_text = Quiz_Maker_Data::replace_message_variables($finish_text, $message_data);

                if(isset($_SESSION['ays_quiz_paypal_purchase'])){
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                    unset($_SESSION['ays_quiz_paypal_purchase']);
                }
                if(array_key_exists('ays_quiz_paypal_purchase', $_SESSION)){
                    $_SESSION['ays_quiz_paypal_purchase'] = false;
                    unset($_SESSION['ays_quiz_paypal_purchase']);
                }
                if(isset($_SESSION['ays_quiz_stripe_purchase'])){
                    $_SESSION['ays_quiz_stripe_purchase'] = false;
                    unset($_SESSION['ays_quiz_stripe_purchase']);
                }
                if(array_key_exists('ays_quiz_stripe_purchase', $_SESSION)){
                    $_SESSION['ays_quiz_stripe_purchase'] = false;
                    unset($_SESSION['ays_quiz_stripe_purchase']);
                }
                
                $admin_mails = get_option('admin_email');
                if ($result) {
                    $woo = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
                    $product = array();
                    if($product_id == ""){
                        $product_id = null;
                    }
                    if ($woo && isset($product_id)) {
                        $wpf = new WC_Product_Factory();
                        $cart_text = __('Add to cart', 'woocommerce');
                        $product_id = explode(',', $product_id);
                        foreach($product_id as $_key => $_value){
                            $product[] = array(
                                'prodUrl'  => get_permalink(intval($_value)),
                                'name'  => $wpf->get_product($_value)->get_data()['name'],
                                'image' => wp_get_attachment_image_src(get_post_thumbnail_id($_value), 'single-post-thumbnail')[0],
                                'link'  => "<a href=\"?add-to-cart=$_value\" data-quantity=\"1\" class=\"action-button product_type_simple add_to_cart_button ajax_add_to_cart\" data-product_id=\"$_value\" data-product_sku=\"\" aria-label=\"$cart_text\" rel=\"nofollow\">$cart_text</a>"
                            );
                        }
                    }
                
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();
                    echo json_encode(array(
                        "status" => true,
                        "hide_result" => false,
                        "showIntervalMessage" => $show_interval_message,
                        "score" => $score,
                        "scoreMessage" => $final_score_message,
                        "displayScore" => $display_score,
                        "finishText" => $finish_text,
                        "product" => $product,
                        "intervalMessage" => $interval_message,
                        "mail" => $ays_send_mail,
                        "mail_to_admin" => $ays_send_mail_to_admin,
                        "admin_mail" => $admin_mails,
                        "result_id" => $last_result_id,
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
    
    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){

        $answer_container = "";
        $show_answers_numbering = $options['show_answers_numbering'];
        $numering_type = Quiz_Maker_Data::ays_answer_numbering($show_answers_numbering);

        foreach ($answers as $key => $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
                //$ans_img = Quiz_Maker_Data::ays_get_image_thumbnauil($answer['image']);
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

            $numering_value = "";
            if( isset( $numering_type[$key] ) && $numering_type[$key] != '' ){
                $numering_value = $numering_type[$key] . " ";
            }

            $label = "";

            if($options['useHTML']){

                $answer_content = $answer["answer"];
                if( function_exists( 'tidy_parse_string' ) ){
                    $answer_content = tidy_parse_string( $answer_content );
                    $answer_content->cleanRepair();
                }else{
                    $answer_content = Quiz_Maker_Data::closetags( $answer_content );
                }

                $answer_content = stripslashes( $answer_content );
                $answer_content = do_shortcode( $answer_content );
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='$answer_label_class $answer_img_label_class'>" . $numering_value . $answer_content . "</label>";
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
        $is_enable_question_max_length = Quiz_Maker_Data::ays_quiz_is_enable_question_max_length( $question_id, 'text' );

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class = '';
        $ays_quiz_question_text_message_html = '';

        $enable_question_text_max_length = false;
        $question_text_max_length = '';
        $question_limit_text_type = 'characters';
        $question_enable_text_message = false;
        if (! empty($question_text_max_length_array) ) {

            $enable_question_text_max_length = $question_text_max_length_array['enable_question_text_max_length'];

            $question_text_max_length = $question_text_max_length_array['question_text_max_length'];

            $question_limit_text_type = $question_text_max_length_array['question_limit_text_type'];

            $question_enable_text_message = $question_text_max_length_array['question_enable_text_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_limit_length';

            if ($question_enable_text_message && $question_text_max_length != 0 && $question_text_max_length != '') {
                $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_conteiner">';
                    $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_message">';
                        $ays_quiz_question_text_message_html .= '<span class="ays_quiz_question_text_message_span">'. $question_text_max_length . '</span> ' . $question_limit_text_type . ' ' . __( 'left' , $this->plugin_name );
                    $ays_quiz_question_text_message_html .= '</div>';
                $ays_quiz_question_text_message_html .= '</div>';
            }
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<textarea type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'></textarea>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>". $this->buttons_texts['checkButton'] ."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"])),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        $answer_container .= $ays_quiz_question_text_message_html;

        return $answer_container;
    }
    
    protected function ays_number_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $is_enable_question_max_length = Quiz_Maker_Data::ays_quiz_is_enable_question_max_length( $question_id , 'number' );

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class = '';
        $ays_quiz_question_number_message_html = '';
        $enable_question_number_max_length = false;
        $question_number_max_length = '';
        if (! empty($question_text_max_length_array) ) {

            $enable_question_number_max_length = $question_text_max_length_array['enable_question_number_max_length'];

            $question_number_max_length = $question_text_max_length_array['question_number_max_length'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ($question_number_max_length != 0 && $question_number_max_length != '') {
                $ays_quiz_question_number_message_html .= 'max="'. $question_number_max_length .'"';
            }
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='number' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' ". $ays_quiz_question_number_message_html ." name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>". $this->buttons_texts['checkButton'] ."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"])),
                            'enable_question_number_max_length' => $enable_question_number_max_length,
                            'question_number_max_length' => $question_number_max_length,
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        return $answer_container;
    }

    protected function ays_date_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='date' autocomplete='off' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>". $this->buttons_texts['checkButton'] ."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'date',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"]))
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        return $answer_container;
    }

    protected function ays_short_text_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $is_enable_question_max_length = Quiz_Maker_Data::ays_quiz_is_enable_question_max_length( $question_id, 'short_text' );

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class = '';
        $ays_quiz_question_text_message_html = '';

        $enable_question_text_max_length = false;
        $question_text_max_length = '';
        $question_limit_text_type = 'characters';
        $question_enable_text_message = false;
        if (! empty($question_text_max_length_array) ) {

            $enable_question_text_max_length = $question_text_max_length_array['enable_question_text_max_length'];

            $question_text_max_length = $question_text_max_length_array['question_text_max_length'];

            $question_limit_text_type = $question_text_max_length_array['question_limit_text_type'];

            $question_enable_text_message = $question_text_max_length_array['question_enable_text_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_limit_length';

            if ($question_enable_text_message && $question_text_max_length != 0 && $question_text_max_length != '') {
                $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_conteiner">';
                    $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_message">';
                        $ays_quiz_question_text_message_html .= '<span class="ays_quiz_question_text_message_span">'. $question_text_max_length . '</span> ' . $question_limit_text_type . ' ' . __( 'left' , $this->plugin_name );
                    $ays_quiz_question_text_message_html .= '</div>';
                $ays_quiz_question_text_message_html .= '</div>';
            }
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button'>". $this->buttons_texts['checkButton'] ."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'short_text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"])),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";
        $answer_container .= $ays_quiz_question_text_message_html;

        return $answer_container;
    }

    protected function ays_dropdown_answer_html($question_id, $quiz_id, $answers, $options){
        
        $answer_container = "<div class='ays-field ays-select-field'>            
            <select class='ays-select'>                
                <option value=''>".__('Select an answer', $this->plugin_name)."</option>";

        $show_answers_numbering = $options['show_answers_numbering'];
        $numering_type = Quiz_Maker_Data::ays_answer_numbering( $show_answers_numbering );
        foreach ($answers as $key => $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
                //$answer_image = Quiz_Maker_Data::ays_get_image_thumbnauil($answer['image']);
                $answer_image = $answer['image'];
            }

            $numering_value = "";
            if( isset( $numering_type[$key] ) && $numering_type[$key] != '' ){
                $numering_value = $numering_type[$key] . " ";
            }

            $answer_container .= "<option data-nkar='{$answer_image}' data-chisht='{$answer["correct"]}' value='{$answer["id"]}'>" . $numering_value . do_shortcode(htmlspecialchars(stripslashes($answer["answer"]))) . "</option>";
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
        $count_correct = intval( Quiz_Maker_Data::count_multiple_correct_answers($question_id) );
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
        $count_correct = intval( Quiz_Maker_Data::count_multiple_correct_answers($question_id) );
        if($count !== $count_correct){
            return false;
        }
        return true;
    }


    protected function add_results_to_db($data){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';

        $started_status = isset($data['started_status']) ? $data['started_status'] : null;

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
        $options['passed_time'] = Quiz_Maker_Data::get_time_difference($start_date, $end_date);
        $options['user_points'] = $data['user_points'];
        $options['max_points'] = $data['max_points'];
        $duration = strtotime($end_date) - strtotime($start_date);
        $user_points = $data['user_points'];
        $max_points = $data['max_points'];
        $user_corrects_count = $data['user_corrects_count'];
        $questions_count = $data['questions_count'];
        
        $user_explanation = (count($data['user_explanation']) == 0) ?  '' : json_encode($data['user_explanation']);
        
        $cert_unique_code = isset($data['unique_code']) ? $data['unique_code'] : '';

        $cert_file_name = isset($data['cert_file_name']) ? $data['cert_file_name'] : '';
        $cert_file_path = isset($data['cert_file_path']) ? $data['cert_file_path'] : '';
        $cert_file_url = isset($data['cert_file_url']) ? $data['cert_file_url'] : '';

        $quiz_attributes_information = array();
        $quiz_attributes = Quiz_Maker_Data::get_quiz_attributes_by_id($quiz_id);

        foreach ($quiz_attributes as $attribute) {
            $quiz_attributes_information[strval($attribute->name)] = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
        }
        $options['attributes_information'] = $quiz_attributes_information;
        $options['calc_method'] = $calc_method;
        $options['cert_file_name'] = $cert_file_name;
        $options['cert_file_path'] = $cert_file_path;
        $options['cert_file_url'] = $cert_file_url;
        $options['answers_keyword_counts'] = isset($data['mv_keywords_counts']) && !empty($data['mv_keywords_counts']) ? $data['mv_keywords_counts'] : array();

        $db_fields = array(
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
            'unique_code' => $cert_unique_code,
            'options' => json_encode($options),
            'status' => 'finished',
        );

        $db_fields_types = array(
            '%d', // quiz_id
            '%d', // user_id
            '%s', // user_name
            '%s', // user_email
            '%s', // user_phone
            '%s', // user_ip
            '%s', // start_date
            '%s', // end_date
            '%s', // score
            '%s', // duration
            '%s', // user_points
            '%s', // max_points
            '%s', // user_corrects_count
            '%s', // questions_count
            '%s', // user_explanation
            '%s', // unique_code
            '%s', // options
            '%s', // status
        );

        if(is_null($started_status)){
            $results = $wpdb->insert(
                $results_table,
                $db_fields,
                $db_fields_types
            );
        }else{
            $results = $wpdb->update(
                $results_table,
                $db_fields,
                array( 'id' => absint(intval($started_status)) ),
                $db_fields_types,
                array( '%d' )
            );
        }

        if ($results >= 0) {
            return true;
        }

        return false;
    }

    public function ays_get_rate_last_reviews(){
        error_reporting(0);

        $this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $this->settings );
        $ays_load_more_button_text = $this->buttons_texts['loadMoreButton'];

        $quiz_id = absint(intval($_REQUEST["quiz_id"]));
        $quiz_rate_html = "<div class='quiz_rate_reasons_container'>";
        $quiz_rate_html .= Quiz_Maker_Data::ays_get_full_reasons_of_rates(0, 5, $quiz_id, 0);
        $quiz_rate_html .= "</div>";
        if(Quiz_Maker_Data::ays_get_count_of_reviews(0, 5, $quiz_id) / 5 > 1){
            $quiz_rate_html .= "<div class='quiz_rate_load_more'>
                <div style='text-align:center;'>
                    <div data-class='lds-spinner' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                </div>
                <button type='button' zuyga='1' startfrom='5' class='action-button ays_load_more_review'><i class='ays_fa ays_fa_chevron_circle_down'></i> ". $ays_load_more_button_text ."</button>
            </div>";
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
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
        $quiz_rate_html .= Quiz_Maker_Data::ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga);
        if($quiz_rate_html == ""){
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo "<p class='ays_no_more'>" . __( "No more reviews", $this->plugin_name ) . "</p>";
            wp_die();
        }else{            
            $quiz_rate_html = "<div class='quiz_rate_more_review'>".$quiz_rate_html."</div>";
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo $quiz_rate_html;
            wp_die();
        }
    }
    
    public function ays_rate_the_quiz(){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $rates_table = $wpdb->prefix . 'aysquiz_rates';

        //$sql = "SELECT * FROM $results_table WHERE id = (SELECT MAX(id) AS max_id FROM $results_table WHERE end_date = ( SELECT MAX(end_date) FROM $results_table ))";//.intval($res[0]['max_id']);
        //$report_result = $wpdb->get_row($sql, ARRAY_A);
        //$report_id = intval($report_result['id']);
        $report_id = (isset($_REQUEST['last_result_id']) && sanitize_text_field($_REQUEST['last_result_id']) != '' && ! is_null( sanitize_text_field($_REQUEST['last_result_id']) ) ) ? intval( sanitize_text_field( $_REQUEST['last_result_id'] ) ) : 0;
        $user_ip = Quiz_Maker_Data::get_user_ip();
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
        $avg_score = Quiz_Maker_Data::ays_get_average_of_rates($quiz_id);
        if ($results >= 0 && Quiz_Maker_Data::ays_set_rate_id_of_result($rate_id , $report_id)) {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'quiz_id'   => $quiz_id,
                'status'    => true,
                'avg_score' => round($avg_score, 1),
                'score'     => intval(round($avg_score)),
                'rates_count'     => Quiz_Maker_Data::ays_get_count_of_rates($quiz_id),
            ));
            wp_die();
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'status'    => false,
        ));
        wp_die();
    }

    public function ays_get_user_information() {        
        if(is_user_logged_in()) {
            $output = json_encode(wp_get_current_user());
        } else {
            $output = null;
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo $output;
        wp_die();
    }
    
    public function ays_generated_used_passwords() {
        global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_generated_used_passwords'){
            $quiz_id = isset($_REQUEST['quizId']) ? absint(intval($_REQUEST['quizId'])) : 0;
            if($quiz_id === 0){
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array("status" => false));
                wp_die();
            }else{
                $sql = "SELECT options FROM $quizes_table WHERE `id` =".$quiz_id;

                $quiz_options = $wpdb->get_var($sql);
                $options = json_decode( $quiz_options, true );

                $generated_passwords  = (isset($options['generated_passwords']) && $options['generated_passwords'] != '') ? $options['generated_passwords'] : array();
                if(!empty($generated_passwords)){
                    $active_passwords = (isset( $generated_passwords['active_passwords']) && !empty( $generated_passwords['active_passwords'])) ?  $generated_passwords['active_passwords'] : array();
                    $used_passwords = (isset( $generated_passwords['used_passwords']) && !empty( $generated_passwords['used_passwords'])) ?  $generated_passwords['used_passwords'] : array();
                    $user_generated_password = (isset($_REQUEST['userGeneratedPassword']) && $_REQUEST['userGeneratedPassword'] != '') ? $_REQUEST['userGeneratedPassword'] : '';
                    if (($key = array_search($user_generated_password, $active_passwords)) !== false) {
                        unset($active_passwords[$key]);
                    }
                    $used_passwords[] = $user_generated_password;
                    $generated_passwords['active_passwords'] = $active_passwords;
                    $generated_passwords['used_passwords'] = $used_passwords;
                    $generate_password_encode = $generated_passwords;
                    $options['generated_passwords'] = $generate_password_encode;
                }
                $quiz_result = $wpdb->update(
                    $quizes_table,
                    array(
                        'options' => json_encode( $options ),
                    ),
                    array( 'id' => $quiz_id ),
                    array( '%s'),
                    array( '%d' )
                );
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array("status" => true));
                wp_die();
            }
        }
    }


    public function get_results_from_db($data){
        global $wpdb;
        if(!empty($data)){
            $reports_table = $wpdb->prefix."aysquiz_reports";
            $last_id = isset($data['results_last_id']) && $data['results_last_id'] != '' ? $data['results_last_id'] : '';
            $sql = "SELECT * FROM ".$reports_table." WHERE id = ".$last_id;
            $results = $wpdb->get_row($sql , "ARRAY_A");
            return $results;
        }else{
            return false;
        }
    }

    public function get_question_answers( $question_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_answers WHERE question_id=" . absint( intval( $question_id ) );

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        foreach ($results as $key => &$result) {
            unset($result['id']);
            unset($result['question_id']);
        }

        return $results;
    }

    public function ays_quiz_check_user_started() {
        error_reporting(0);
        global $wpdb;
        $reports_table = $wpdb->prefix . "aysquiz_reports";

        $quiz_id    = (isset($_REQUEST["quiz_id"]) && $_REQUEST["quiz_id"] != '') ? absint(intval($_REQUEST["quiz_id"])) : NULL;
        $start_date = (isset($_REQUEST["start_date"]) && $_REQUEST["start_date"] != '') ? $_REQUEST['start_date'] : NULL;
        $quiz_max_pass_count = (isset($_REQUEST["quiz_max_pass_count"]) && $_REQUEST["quiz_max_pass_count"] != '') ? absint(intval($_REQUEST['quiz_max_pass_count'])) : 1;
        $quiz_pass_score = (isset($_REQUEST["quiz_pass_score"]) && $_REQUEST["quiz_pass_score"] != '') ? absint(intval($_REQUEST['quiz_pass_score'])) : 0;
        $options = array();

        $user_id = get_current_user_id();
        $user_ip = Quiz_Maker_Data::get_user_ip();

        $quiz_settings = $this->settings;
        $quiz = Quiz_Maker_Data::get_quiz_by_id($quiz_id);

        $quiz_options = json_decode( $quiz['options'] );
        if( is_array( $quiz_options ) ){
            $quiz_options = (object) $quiz_options;
        }

        // General Setting's Options
        $general_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $settings_options = json_decode( stripslashes( $general_settings_options ), true );

        // Do not store IP adressess
        $disable_user_ip = (isset($settings_options['disable_user_ip']) && $settings_options['disable_user_ip'] == 'on') ? true : false;

        // Limit user
        $options->limit_users = isset($quiz_options->limit_users) ? $quiz_options->limit_users : 'off';
        $limit_users = (isset($quiz_options->limit_users) && $quiz_options->limit_users == 'on') ? true : false;

        // Limit user by
        $limit_users_by = (isset($quiz_options->limit_users_by) && $quiz_options->limit_users_by != '') ? $quiz_options->limit_users_by : 'ip';

        // Quiz Title
        $quiz_title = (isset($quiz['title']) && $quiz['title'] != '') ? stripslashes( $quiz['title'] ) : '';

        $started_user_count = 0;
        if( $limit_users ){
            // Check user limitation
            $limit_users_attr = array(
                'id' => $quiz_id,
                'name' => 'ays_quiz_cookie_',
                'title' => $quiz_title,
            );

            switch ( $limit_users_by ) {
                case 'ip':
                    $started_user_count = Quiz_Maker_Data::get_user_by_ip($quiz_id, $quiz_pass_score);
                    break;
                case 'user_id':
                    $started_user_count = Quiz_Maker_Data::get_limit_user_by_id($quiz_id, $user_id, $quiz_pass_score);
                    break;
                case 'cookie':
                    $started_user_count = Quiz_Maker_Data::get_limit_cookie_count( $limit_users_attr );
                    if( $quiz_max_pass_count > $started_user_count){
                        $limit_users_attr['increase_count'] = true;
                    }
                    $check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $limit_users_attr );
                    $return_false_status_arr = array(
                        "status" => false,
                        "flag" => false,
                        "text" => __( 'You already passed this quiz.', $this->plugin_name ),
                    );

                    if( $quiz_max_pass_count <= $started_user_count){
                        echo json_encode( $return_false_status_arr );
                        wp_die();
                    }

                    if( ! $check_cookie ){
                        $set_cookie = Quiz_Maker_Data::ays_quiz_set_cookie( $limit_users_attr );
                    }
                    break;
                case 'ip_cookie':
                    $check_user_by_ip = Quiz_Maker_Data::get_user_by_ip( $quiz_id, $quiz_pass_score );

                    $started_user_count = Quiz_Maker_Data::get_limit_cookie_count( $limit_users_attr );
                    if( $quiz_max_pass_count > $started_user_count){
                        $limit_users_attr['increase_count'] = true;
                    }
                    $check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $limit_users_attr );

                    $return_false_status_arr = array(
                        "status" => false,
                        "flag" => false,
                        "text" => __( 'You already passed this quiz.', $this->plugin_name ),
                    );

                    if ( ! $check_cookie || $check_user_by_ip <= 0 ) {
                        if ( ! $check_cookie ) {
                            $set_cookie = Quiz_Maker_Data::ays_quiz_set_cookie( $limit_users_attr );
                        }
                    } elseif( $quiz_max_pass_count <= $started_user_count || $quiz_max_pass_count <= $check_user_by_ip ) {
                        echo json_encode( $return_false_status_arr );
                        wp_die();
                    }
                    break;
                default:
                    break;
            }
        }

        if($started_user_count < $quiz_max_pass_count){
            $results = $wpdb->insert(
                $reports_table,
                array(
                    'quiz_id' => absint(intval($quiz_id)),
                    'user_id' => $user_id,
                    'user_ip' => $user_ip,
                    'start_date' => $start_date,
                    'end_date' => $start_date,
                    'score' => '',
                    'duration' => '',
                    'points' => '',
                    'max_points' => '',
                    'corrects_count' => '',
                    'questions_count' => '',
                    'options' => json_encode($options),
                    'status' => 'started',
                ),
                array(
                    '%d', // quiz_id
                    '%d', // user_id
                    '%s', // user_ip
                    '%s', // start_date
                    '%s', // end_date
                    '%d', // score
                    '%s', // duration
                    '%s', // user_points
                    '%s', // max_points
                    '%s', // user_corrects_count
                    '%s', // questions_count
                    '%s', // options
                    '%s', // status
                )
            );
            $result_id = $wpdb->insert_id;

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            if($results){
                echo json_encode(array(
                    'status'    => true,
                    'result_id' => $result_id,
                ));
            }
        }else{
            echo json_encode(array(
                'status'    => false,
                'result_id' => '',
            ));
        }
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
        $double_optin = isset( $args['double_optin'] ) ? $args['double_optin'] : false;
        
        $api_prefix = explode("-",$api_key)[1];
        $contact_status = "subscribed";
        if( $double_optin === true ){
            $contact_status = "pending";
        }
        
        $fields = array(
            "email_address" => $email,
            "status" => $contact_status,
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

    //Google Sheets
    public function ays_add_google_sheets($data) {
        error_reporting(0);
        if (empty($data)) {
            return false;
        }
        $new_token = '';

        $user  = '';
        $user_ip    = '';
        $start_date = '';
        $end_date   = '';
        $score      = '';
        $points     = '';
        $duration   = '';
        $rate       = '';
        $review     = '';

        $last_id = isset($data['results_last_id']) && $data['results_last_id'] != '' ? $data['results_last_id'] : '';
        $quiz_attributes = (isset($data['quiz_attributes']) && !empty($data['quiz_attributes'])) ? $data['quiz_attributes'] : array();
        $custom_fields = (isset($data['custom_fields']) && !empty($data['custom_fields'])) ? $data['custom_fields'] : array();
        $reports = $this->get_results_from_db($data);
        if(isset($reports) && !empty($reports)){
            $user_id    = isset($reports['user_id']) && $reports['user_id'] != '' ? intval($reports['user_id']) : '';
            $user_ip    = isset($reports['user_ip']) && $reports['user_ip'] != '' ? esc_attr( $reports['user_ip'] ) : '';
            $start_date = isset($reports['start_date']) && $reports['start_date'] != '' ? esc_attr( $reports['start_date'] ) : '';
            $end_date   = isset($reports['end_date']) && $reports['end_date'] != '' ? esc_attr( $reports['end_date'] ) : '';
            $score      = isset($reports['score']) && $reports['score'] != '' ? esc_attr( $reports['score']."%" ) : '';
            $points     = isset($reports['points']) && $reports['points'] != '' ? esc_attr( $reports['points'] ) : '';
            $duration   = isset($reports['duration']) && $reports['duration'] != '' ? esc_attr( $reports['duration'] ) . "s" : '';
            // $rate       = isset($reports['duration']) && $reports['duration'] != '' ? $reports['duration'] : '';
            // $review     = isset($reports['duration']) && $reports['duration'] != '' ? $reports['duration'] : '';

            $this_user = get_userdata($user_id);
            if(isset($this_user)){
                $user = $this_user->data->display_name;
            }
        }

        $quiz_attributes['ays_form_name'] = isset($_POST['ays_user_name'])  && $_POST['ays_user_name']  != '' ? sanitize_text_field( $_POST['ays_user_name'] )  : '';
        $quiz_attributes['ays_form_email'] = isset($_POST['ays_user_email']) && $_POST['ays_user_email'] != '' ? sanitize_email( $_POST['ays_user_email'] ) : '';
        $quiz_attributes['ays_form_phone'] = isset($_POST['ays_user_phone']) && $_POST['ays_user_phone'] != '' ? sanitize_text_field( $_POST['ays_user_phone'] ) : '';

        $sheet_id      = isset($data['sheed_id']) && $data['sheed_id'] != '' ? $data['sheed_id'] : '';
        $refresh_token = isset($data['refresh_token']) && $data['refresh_token'] != '' ? $data['refresh_token'] : '';

        if($refresh_token != ''){
            $new_token = Quiz_Maker_Data::ays_get_refreshed_token($data);
        }

        $props_values_arr = array(
            $user,
            $user_ip,
            $start_date,
            $end_date,
            $score,
            $points,
            $duration
        );

        foreach ( $custom_fields as $slug => $attribute ) {
            if ( isset( $quiz_attributes[$slug] ) && $quiz_attributes[$slug] != '' ) {
                $props_values_arr[] = $quiz_attributes[$slug];
            }else{
                $props_values_arr[] = '';
            }
        }

        $props = array(
            "range" => "A1",
            "majorDimension" => "ROWS",
            "values" => array(
                $props_values_arr
            )
        );

        $properties = json_encode($props,true);
        $url = "https://sheets.googleapis.com/v4/spreadsheets/".$sheet_id."/values/A1:append?";

        $args = array(
            "valueInputOption" => "RAW",
            "insertDataOption" => "OVERWRITE",
            "responseValueRenderOption" => "FORMATTED_VALUE",
            "responseDateTimeRenderOption" => "SERIAL_NUMBER",
            "access_token" => $new_token
        );

        $url .= http_build_query( $args );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $properties,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        curl_close($curl);

        if( $http_code != 200 ){
            return null;
        }

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }

    public function ays_quiz_add_data_attribute($tag, $handle) {
        if ( $this->plugin_name . '-paypal' == $handle ){
            return str_replace( ' src', ' data-namespace="aysQuizPayPal" src', $tag );
        }

        if ( $this->plugin_name . '-stripe' == $handle ){
            return str_replace( ' src', ' data-namespace="aysQuizStripe" src', $tag );
        }
        return $tag;
    }
}
