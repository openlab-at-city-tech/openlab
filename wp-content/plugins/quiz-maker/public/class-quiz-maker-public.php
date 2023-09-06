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

    public $settings;

    public $buttons_texts;

    protected $fields_placeholders;
    protected $chain_id;
    protected $chain_result_btn;
    protected $is_training;
    protected $aysQuizUserExportDataArray = null;
    protected $category_selective;

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

        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
        }else{
            $settings_options = array();
        }

        // General CSS File
        $settings_options['quiz_exclude_general_css'] = isset($settings_options['quiz_exclude_general_css']) ? esc_attr( $settings_options['quiz_exclude_general_css'] ) : 'off';
        $quiz_exclude_general_css = (isset($settings_options['quiz_exclude_general_css']) && esc_attr( $settings_options['quiz_exclude_general_css'] ) == "on") ? true : false;

        if ( ! $quiz_exclude_general_css ) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');
        }else {
            if ( ! is_front_page() ) {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');
            }

        }

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
                'AYS_QUIZ_PUBLIC_URL'   => AYS_QUIZ_PUBLIC_URL,
            ));

            $this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $this->settings );

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
                'redirectAfter'         => __( 'Redirecting after', $this->plugin_name ),
                'loadResource'          => __( "Can't load resource.", $this->plugin_name ),
                'somethingWentWrong'    => __( "Maybe something went wrong.", $this->plugin_name ),
                'show'                  => __( 'Show', $this->plugin_name ),
                'hide'                  => __( 'Hide', $this->plugin_name ),
                'emptyReportMessage'    => __( 'You cannot submit an empty report. Please add some details.', $this->plugin_name ),
                'reportSentMessage'     => __( 'Report has been submitted successfully', $this->plugin_name ),
                'unansweredQuestion'    => __( 'Unanswered question', $this->plugin_name ),


                'AYS_QUIZ_PUBLIC_URL'   => AYS_QUIZ_PUBLIC_URL,
            ) );
        }
    }
    
    public function ays_generate_quiz_method($attr){
        // ob_start();
        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
        $embed = isset( $attr['embed'] ) && sanitize_text_field($attr['embed']) === 'true';

        $chain_id = (isset($attr['chain'])) ? absint(intval($attr['chain'])) : null;
        $chain_result_btn = (isset($attr['report'])) ? $attr['report'] : null;
        $is_training = isset($attr['training']) && sanitize_text_field($attr['training']) === 'true';
        $category_selective = isset($attr['category_selective']) && sanitize_text_field($attr['category_selective']) === 'true';

        $this->set_prop('chain_id', $chain_id );
        $this->set_prop( 'chain_result_btn', $chain_result_btn );
        $this->set_prop( 'is_training', $is_training );
        $this->set_prop( 'category_selective', $category_selective );

        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
        }

        if( Quiz_Maker_iFrame::isAMP() ) {
            $content = Quiz_Maker_iFrame::get_iframe_for_amp( $id, $attr );
        }elseif( $embed === true ) {
            $content = Quiz_Maker_iFrame::get_iframe( $id, $attr );
        } else {
            $this->enqueue_styles();
            $this->enqueue_scripts();

            $content = $this->show_quiz($id, $attr);
            $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );
        }

        return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
    }
    
    public function show_quiz($id, $attr){
        global $wpdb;
        if( ! session_id() ){
            session_start();
        }
        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
        
        if (is_null($quiz)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return $content;
        }
        if (intval($quiz['published']) === 0 || intval($quiz['published']) === 2) {
            $content = "";
            return $content;
        }
        
        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();
        $is_editor_exists = Quiz_Maker_Data::ays_quiz_is_editor();

        
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        $paypal_client_id = isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '' ? $quiz_integrations['paypal_client_id'] : null;
        $quiz_paypal = (isset($options['enable_paypal']) && $options['enable_paypal'] == "on") ? true : false;
        $quiz_paypal_message = (isset($options['paypal_message']) && $options['paypal_message'] != "") ? $options['paypal_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $quiz_paypal_message = stripslashes( wpautop( $quiz_paypal_message ) );
        $paypal_subscribtion_duration = isset( $quiz_integrations['subscribtion_duration'] ) && $quiz_integrations['subscribtion_duration'] != '' ? absint( $quiz_integrations['subscribtion_duration'] ) : '';
        $paypal_subscribtion_duration_by = isset( $quiz_integrations['subscribtion_duration_by'] ) && $quiz_integrations['subscribtion_duration_by'] != '' ? $quiz_integrations['subscribtion_duration_by'] : 'day';

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

        // Paypal And Stripe Paymant type
        $payment_type = (isset($options['payment_type']) && sanitize_text_field( $options['payment_type'] ) != '') ? sanitize_text_field( esc_attr( $options['payment_type']) ) : 'prepay';

        $paypal_connection = Quiz_Maker_Data::get_payment_connection( 'paypal', $payment_type, $payment_terms, $id, array(
            'subsctiptionDuration' => $paypal_subscribtion_duration,
            'subsctiptionDurationBy' => $paypal_subscribtion_duration_by,
        ));

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

                $quiz_paypal_style = '';
                if ( $payment_type == 'prepay' ) {
                    $quiz_paypal_style = 'display: block;';
                }

                $paypal_html = ' 
                    <div class="ays_paypal_wrap_div" style="'. $quiz_paypal_style .'">
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
                                                        quizId: '.$id.',
                                                        paymentType: "'.$payment_type.'"
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
                                                    }).then((result) => {';
                if($payment_type == 'prepay'){
                    $paypal_html .= 'location.reload();';
                }elseif($payment_type == 'postpay'){
                    $paypal_html .= '
                        var resId = $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".ays_paypal_wrap_div").attr("data-result");
                        $.ajax({
                            url: window.quiz_maker_ajax_public.ajax_url,
                            method: "post",
                            dataType: "json",
                            data: {
                                action: "ays_store_result_payed",
                                res_id: resId
                            },
                            success: function(response){

                            }
                        });
                        var data = $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".ays-quiz-container").find("form").serializeFormJSON();
                        data.action = "ays_finish_quiz";
                        data.end_date = GetFullDateTime();
                        data.res_id = resId;
                        data["ays-paypal-paid"] = true;

                        $.ajax({
                            url: window.quiz_maker_ajax_public.ajax_url,
                            method: "post",
                            dataType: "json",
                            data: data,
                            success: function(response){
                            }
                        });

                        
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".ays_paypal_wrap_div").hide(500);
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".step").find(".ays_stripe_wrap_div").hide(500);
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".step").find(".ays_quiz_results_page").html(window.aysResultsForQuiz);
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".step").find(".ays_quiz_results_page").css("display", "block");
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".step").find(".ays_quiz_results_page *").css("opacity", "1");
                        $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents("form").find(".ays_quiz_results").slideDown(1000);
                        setTimeout(function(){
                            $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".ays_paypal_wrap_div").remove();
                        }, 1200);

                        var form = $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents("form");
                        var resCont = $(document).find("#ays_quiz_paypal_button_container_'.$id.'").parents(".step").find(".ays_quiz_results_page");
                        setTimeout(function () {
                            form.find("p.ays_score").addClass("tada");
                        }, 500);
                        var quizScore = form.find(".ays_score_percent").text();
                        quizScore = parseInt(quizScore);
                        let numberOfPercent = 0;
                        let percentAnimate = setInterval(function(){
                            form.find(".ays-progress-value").text(numberOfPercent + "%");
                            if(numberOfPercent == quizScore){
                                clearInterval(percentAnimate);
                            }
                            numberOfPercent++;                            
                        },20);
                        
                        var score = quizScore;
                        if(score > 0){
                            form.find(".ays-progress-bar").css("padding-right", "7px");
                            var progressBarStyle = "first";
                            if(form.find(".ays-progress-bar").hasClass("second")){
                                progressBarStyle = "second";
                            }else if(form.find(".ays-progress-bar").hasClass("third")){
                                progressBarStyle = "third";
                            }else if(form.find(".ays-progress-bar").hasClass("fourth")){
                                progressBarStyle = "fourth";
                            }
                            if(progressBarStyle == "first" || progressBarStyle == "second"){
                                form.find(".ays-progress-value").css("width", 0);
                                form.find(".ays-progress-value").css("transition", "width " + score*25 + "ms linear");
                                setTimeout(function(){
                                    form.find(".ays-progress-value").css("width", score+"%");
                                }, 1);
                            }
                            form.find(".ays-progress-bar").css("transition", "width " + score*25 + "ms linear");
                            setTimeout(function(){
                                form.find(".ays-progress-bar").css("width", score+"%");
                            }, 1);
                        }
                        form.find(".for_quiz_rate").rating({
                            onRate: function(res){
                                $(this).rating("disable");
                                $(this).parent().find(".for_quiz_rate_reason").slideDown(500);
                                $(this).parents(".ays_quiz_rete").attr("data-rate_score", res);
                            }
                        });
                    ';
                }
                                    $paypal_html .= '});
                                                }).catch(error => console.error(error));
                                            });
                                        }
                                    }).render("#ays_quiz_paypal_button_container_'.$id.'");
                                });
                            })(jQuery);';
                $paypal_html .= '});';
                $paypal_html .= '</script>';
                $attr['quiz_paypal'] = $paypal_html;
            }
        }else{
            $attr['quiz_paypal'] = null;
        }

        $stripe_connection = Quiz_Maker_Data::get_payment_connection( 'stripe', $payment_type, $stripe_payment_terms, $id, array());

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

                $attr['quiz_stripe'] = '';
                if( $enqueue_stripe_scripts ){
                    wp_enqueue_style( $this->plugin_name . '-stripe-client', plugin_dir_url(__FILE__) . 'css/stripe-client.css', array(), $this->version, 'all');
                    wp_enqueue_script( $this->plugin_name . '-stripe', "https://js.stripe.com/v3/", array('jquery'), null, true );
                    wp_enqueue_script( $this->plugin_name . '-stripe-client', plugin_dir_url(__FILE__) . "js/stripe_client.js", array('jquery'), $this->version, true );

                    $quiz_stripe_js_options = array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'fetchUrl' => AYS_QUIZ_PUBLIC_URL .'/partials/stripe-before-transaction.php',
                        'transactionCompleteUrl' => AYS_QUIZ_PUBLIC_URL .'/partials/stripe-transaction-complete.php',
                        'secretKey' => $stripe_secret_key,
                        'apiKey' => $stripe_api_key,
                        'paymentTerms' => $stripe_payment_terms,
                        'paymentType' => $payment_type,
                        'wrapClass' => '.ays_stripe_div_'.$id,
                        'containerId' => '#ays_quiz_stripe_button_container_'.$id,
                        'quizId' => $id,
                        'stripeOptions' => array(
                            'amount' => $stripe_amount,
                            'currency' => $stripe_currency,
                        ),
                    );
                    $attr['quiz_stripe'] .= '
                        <script>
                            if(typeof quizMakerStripe === "undefined"){
                                var quizMakerStripe = [];
                            }
                            quizMakerStripe["'.$id.'"]  = "' . base64_encode(json_encode($quiz_stripe_js_options)) . '";
                        </script>
                    ';
                }

                $quiz_stripe_style = '';
                $quiz_stripe_type_js = '';
                if ( $payment_type == 'prepay' ) {
                    $quiz_stripe_style = 'display: block; margin-bottom: 40px;';
                }else{
                    $quiz_stripe_type_js = 'postpay';
                    $quiz_stripe_style = 'margin-bottom: 40px;';
                }

                $attr['quiz_stripe'] .= '
                    <div class="ays_stripe_wrap_div" style="'. $quiz_stripe_style .'" data-type="'. $quiz_stripe_type_js .'">
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

        $stripe_payment_usermeta = Quiz_Maker_Data::get_quiz_payment_usermeta( 'stripe', get_current_user_id(), $id );
        $paypal_payment_usermeta = Quiz_Maker_Data::get_quiz_payment_usermeta( 'paypal', get_current_user_id(), $id );
        $stripe_ontime_payment_usermeta = Quiz_Maker_Data::is_not_logged_in_user_paid( $id, 'stripe', array() );
        $paypal_ontime_payment_usermeta = Quiz_Maker_Data::is_not_logged_in_user_paid( $id, 'paypal', array() );

        $quiz_paypal_payment_done = false;
        $quiz_stripe_payment_done = false;

        if ( isset($paypal_payment_usermeta) && is_array($paypal_payment_usermeta) && !empty( $paypal_payment_usermeta ) ) {
            $quiz_paypal_payment_done = (isset( $paypal_payment_usermeta['purchased'] ) && $paypal_payment_usermeta['purchased'] != "") ? $paypal_payment_usermeta['purchased'] : false;
        }

        if ( isset($stripe_payment_usermeta) && is_array($stripe_payment_usermeta) && !empty( $stripe_payment_usermeta ) ) {
            $quiz_stripe_payment_done = (isset( $stripe_payment_usermeta['purchased'] ) && $stripe_payment_usermeta['purchased'] != "") ? $stripe_payment_usermeta['purchased'] : false;
        }

        if ( $quiz_paypal_payment_done || $quiz_stripe_payment_done ) {
            $quiz_paypal_payment_done = true;
            $quiz_stripe_payment_done = true;
        }

        if( $quiz_paypal && $payment_type == "prepay" ) {
            if ( $payment_terms == 'onetime' ) {
                if ( $paypal_ontime_payment_usermeta !== true ) {
                    if ( $attr['quiz_stripe'] !== null ) {
                        $attr['quiz_stripe'] = null;
                    }
                }
            } else {
                if ( $paypal_payment_usermeta !== false ) {
                    if ( $quiz_paypal_payment_done && $attr['quiz_stripe'] !== null ) {
                        $attr['quiz_stripe'] = null;
                    }
                }
            }
        }
 
        if( $enable_stripe && $payment_type == "prepay" ) {
            if ( $stripe_payment_terms == 'onetime' ) {
                if ( $stripe_ontime_payment_usermeta !== true ) {
                    if ( $attr['quiz_paypal'] !== null ) {
                        $attr['quiz_paypal'] = null;
                    }
                }
            } else {
                if ( $stripe_payment_usermeta !== false ) {
                    if ( $quiz_stripe_payment_done && $attr['quiz_paypal'] !== null ) {
                        $attr['quiz_paypal'] = null;
                    }
                }
            }
        }

        if($enable_copy_protection){
            if ( ! $is_elementor_exists && ! $is_editor_exists ) {
                wp_enqueue_script ($this->plugin_name .'-quiz_copy_protection', plugin_dir_url(__FILE__) . 'js/quiz_copy_protection.min.js', array('jquery'), $this->version, true);
            }
        }
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';

        $settings_for_theme = $this->settings;

        if( $is_elementor_exists ){
            $this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $settings_for_theme );
        } elseif( is_null( $this->buttons_texts ) ){
            $this->buttons_texts = Quiz_Maker_Data::ays_set_quiz_texts( $this->plugin_name, $settings_for_theme );
        }

        $this->fields_placeholders = Quiz_Maker_Data::ays_set_quiz_fields_placeholders_texts();


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
                'payment_type' => $payment_type,
                'html' => $attr['quiz_paypal'],
            ),
            'stripe' => array(
                'payment_terms' => $stripe_payment_terms,
                'payment_type' => $payment_type,
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

        /*
         * Quiz message variables for Start Page
         */

        $message_variables_data = Quiz_Maker_Data::ays_set_quiz_message_variables_data( $id, $quiz );

    /*******************************************************************************************************/
        
        /* 
        ========================================== 
            Quiz questions
            Question bank
        ========================================== 
        */
        
        $randomize_answers = false;
        $questions = null;
        $randomize_questions = false;
        $questions_ordering_by_cat = false;
        $quiz_questions_ids = "";
        
        $arr_questions = ($quiz["question_ids"] == "") ? array() : explode(',', $quiz["question_ids"]);
        $arr_questions = (count($arr_questions) == 1 && $arr_questions[0] == '') ? array() : $arr_questions;

        if ( !empty($arr_questions) ) {
            $new_arr_questions = implode( ",", $arr_questions );
            $arr_questions = Quiz_Maker_Data::get_published_questions_id_arr($new_arr_questions);
        }

        if( $this->get_prop( 'is_training' ) === true ){
            $saved_passed_questions = apply_filters( 'ays_qm_front_end_training_passed_questions', array(), $id );
            $saved_wrong_questions = apply_filters( 'ays_qm_front_end_training_wrong_questions', array(), $id );
            $arr_questions = array_diff( $arr_questions, $saved_passed_questions );
            $arr_questions = array_values( $arr_questions );
        }

        if (isset($options['randomize_questions']) && $options['randomize_questions'] == "on") {
            shuffle($arr_questions);
        }

        $quiz_questions_ids = join(',', $arr_questions);
        
        $question_bank = false;
        $question_bank_type = 'general';
        $question_bank_count = 0;
        $questions_bank_cat_count = array();
        $question_bank_cats = array();
        $new_question_bank_cats = array();
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

                        if ( !is_array( $random_questions ) ) {
                            $random_questions = array( $random_questions );
                        }

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
                        $quiz_questions_ids = array();
                        $question_bank_by_categories1 = array();

                        foreach($arr_questions as $key => $val){
                            $question_bank_questions[$val] = (isset( $quests[$val] ) && !empty( $quests[$val] )) ? $quests[$val] : array();
                            if ( empty( $question_bank_questions[$val] ) ) {
                                continue;
                            }
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

        $ays_quiz_arr_questions_flag = false;
        foreach ($arr_questions as $q_key => $arr_question_id) {
            $arr_question_data = Quiz_Maker_Data::get_published_questions_by('id', absint($arr_question_id));
            if ( is_null( $arr_question_data ) || empty( $arr_question_data ) ) {
                unset( $arr_questions[$q_key] );
                $ays_quiz_arr_questions_flag = true;
            }
        }

        if ( !empty( $arr_questions ) && $ays_quiz_arr_questions_flag ) {
            $arr_questions = array_values($arr_questions);
            $questions_count = count($arr_questions);
        } elseif ( empty( $arr_questions ) ) {
            $arr_questions = array();
            $questions_count = count($arr_questions);
        }

        if( !empty( $arr_questions ) ){
            $new_question_bank_questions = array();
            foreach($arr_questions as $key => $val){
                $new_question_bank_questions[$val] = (isset( $quests[$val] ) && !empty( $quests[$val] )) ? $quests[$val] : array();
                if ( empty( $new_question_bank_questions[$val] ) ) {
                    continue;
                }

                $new_question_bank_cats[$quests[$val]['category_id']][] = strval($val);
            }
        }

        // Waiting time
        $options['quiz_waiting_time'] = isset($options['quiz_waiting_time']) ? esc_attr($options['quiz_waiting_time']) : 'off';
        $quiz_waiting_time = (isset($options['quiz_waiting_time']) && $options['quiz_waiting_time'] == 'on') ? true : false;
        
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
        $form_title = "";
        if(isset($options['form_title']) && $options['form_title'] != ''){
            $form_title = Quiz_Maker_Data::replace_message_variables($options['form_title'], $message_variables_data);
            $form_title = Quiz_Maker_Data::ays_autoembed($form_title);
        }
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

        // Display form fields labels
        $options['display_fields_labels'] = isset($options['display_fields_labels']) ? $options['display_fields_labels'] : 'on';
        $display_fields_labels = (isset($options['display_fields_labels']) && $options['display_fields_labels'] == 'on') ? true : false;


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
            "placeholder" => __( "Name", $this->plugin_name ),
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
            "placeholder" => __( "Email", $this->plugin_name ),
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
            "placeholder" => __( "Phone Number", $this->plugin_name ),
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
                'placeholder' => esc_attr( $attr['name'] ),
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
                if( $display_fields_labels ){
                    $form_inputs .= "<label for='ays_form_field_". $attribute->slug ."_". $id ."'>". $attribute->name ."</label>";
                }
                $form_inputs .= "<textarea id='ays_form_field_". $attribute->slug ."_". $id ."' name='". $attribute->slug ."' class='ays_quiz_form_input ays_animated_x5ms' placeholder='" . $attribute->name . "' " . $attr_required . "></textarea>";
            }elseif($attribute->type == "select"){
                $attr_options = isset($attribute->options) ? $attribute->options : '';
                $attr_options = explode(';', $attr_options);
                $attr_options1 = array();
                $attr_options1[] = "<option value=''>".$attribute->name."</option>";
                foreach ($attr_options as $attr_options_val) {                       
                    $attr_options1[] = "<option value='".trim( $attr_options_val, ' ' )."'>".trim( $attr_options_val, ' ' )."</option>";
                }
                $attr_options2 = implode('', $attr_options1);

                if( $display_fields_labels ){
                    $form_inputs .= "<label for='ays_form_field_". $attribute->slug ."_". $id ."'>". $attribute->name ."</label>";
                }
                $form_inputs .= "<select id='ays_form_field_". $attribute->slug ."_". $id ."' name='". $attribute->slug ."' class='ays_quiz_form_input ays_animated_x5ms' " . $attr_required . ">". $attr_options2 ."</select>";
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

                if($attribute->slug == "ays_user_name"){
                    $attribute->name = $this->fields_placeholders['nameLabel'];
                    $attribute->placeholder = $this->fields_placeholders['namePlaceholder'];
                }

                if($attribute->slug == "ays_user_email"){
                    $attribute->name = $this->fields_placeholders['emailLabel'];
                    $attribute->placeholder = $this->fields_placeholders['emailPlaceholder'];
                }

                if($attribute->slug == "ays_user_phone"){
                    $attribute->name = $this->fields_placeholders['phoneLabel'];
                    $attribute->placeholder = $this->fields_placeholders['phonePlaceholder'];
                }

                if( $display_fields_labels ){
                    if( $attribute->type != 'hidden' ){
                        $form_inputs .= "<label for='ays_form_field_". $attribute->slug ."_". $id ."'>". $attribute->name ."</label>";
                    }
                }

                $form_inputs .= "<input id='ays_form_field_". $attribute->slug ."_". $id ."' type='". $attribute->type ."' class='ays_quiz_form_input ays_animated_x5ms' name='". $attribute->slug ."' placeholder='" . $attribute->placeholder . "' " . $attr_required . "/>";
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

        // Quiz title font size
        $quiz_title_font_size = (isset($options['quiz_title_font_size']) && ( $options['quiz_title_font_size'] ) != '' && ( $options['quiz_title_font_size'] ) != 0) ? esc_attr( absint( $options['quiz_title_font_size'] ) ) : 21;

        // Quiz title font size | On mobile
        $quiz_title_mobile_font_size = (isset($options['quiz_title_mobile_font_size']) && sanitize_text_field($options['quiz_title_mobile_font_size']) != '') ? esc_attr( absint($options['quiz_title_mobile_font_size']) ) : 21;

        // Quiz title text shadow
        $options['quiz_enable_title_text_shadow'] = isset($options['quiz_enable_title_text_shadow']) ? esc_attr($options['quiz_enable_title_text_shadow']) : 'off';
        $quiz_enable_title_text_shadow = (isset($options['quiz_enable_title_text_shadow']) && $options['quiz_enable_title_text_shadow'] == 'on') ? true : false;

        // Quiz title text shadow color
        $quiz_title_text_shadow_color = (isset($options['quiz_title_text_shadow_color']) && $options['quiz_title_text_shadow_color'] != '') ? esc_attr($options['quiz_title_text_shadow_color']) : '#333';

        // Quiz Title Text Shadow X offset
        $quiz_title_text_shadow_x_offset = (isset($options['quiz_title_text_shadow_x_offset']) && ( $options['quiz_title_text_shadow_x_offset'] ) != '' && ( $options['quiz_title_text_shadow_x_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_x_offset'] ) ) : 2;

        // Quiz Title Text Shadow Y offset
        $quiz_title_text_shadow_y_offset = (isset($options['quiz_title_text_shadow_y_offset']) && ( $options['quiz_title_text_shadow_y_offset'] ) != '' && ( $options['quiz_title_text_shadow_y_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_y_offset'] ) ) : 2;

        // Quiz Title Text Shadow Z offset
        $quiz_title_text_shadow_z_offset = (isset($options['quiz_title_text_shadow_z_offset']) && ( $options['quiz_title_text_shadow_z_offset'] ) != '' && ( $options['quiz_title_text_shadow_z_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_z_offset'] ) ) : 2;

        $title_text_shadow_offsets = $quiz_title_text_shadow_x_offset . 'px ' . $quiz_title_text_shadow_y_offset . 'px ' . $quiz_title_text_shadow_z_offset . 'px ';

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

        // Button font-size (px) | Mobile
        $buttons_mobile_font_size = (isset($options['buttons_mobile_font_size']) && $options['buttons_mobile_font_size'] != '') ? absint( esc_attr( $options['buttons_mobile_font_size'] ) ) : 17;

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

        // Answer font size | On mobile
        $answers_mobile_font_size = ( isset($options['answers_mobile_font_size']) && $options['answers_mobile_font_size'] != "" ) ? absint( sanitize_text_field( $options['answers_mobile_font_size'] ) ) : 15;

        // Question Font Size
        $question_font_size = '16';
        if(isset($options['question_font_size']) && $options['question_font_size'] != ""){
            $question_font_size = $options['question_font_size'];
        }

        // Question font size | On mobile
        $question_mobile_font_size = ( isset($options['question_mobile_font_size']) && $options['question_mobile_font_size'] != "" ) ? absint( sanitize_text_field( $options['question_mobile_font_size'] ) ) : 16;

        // Font size for the right answer
        $right_answers_font_size = (isset($options['right_answers_font_size']) && $options['right_answers_font_size'] != '') ? absint(sanitize_text_field($options['right_answers_font_size'])) : '16';

        // Font size for the right answer | Mobile
        $right_answers_mobile_font_size = (isset($options['right_answers_mobile_font_size']) && $options['right_answers_mobile_font_size'] != '') ? absint(esc_attr($options['right_answers_mobile_font_size'])) : $right_answers_font_size;

        // Font size for the wrong answer
        $wrong_answers_font_size = (isset($options['wrong_answers_font_size']) && $options['wrong_answers_font_size'] != '') ? absint(sanitize_text_field($options['wrong_answers_font_size'])) : '16';

        // Font size for the wrong answer | Mobile
        $wrong_answers_mobile_font_size = (isset($options['wrong_answers_mobile_font_size']) && $options['wrong_answers_mobile_font_size'] != '') ? absint(sanitize_text_field($options['wrong_answers_mobile_font_size'])) : $wrong_answers_font_size;

        // Font size for the question explanation
        $quest_explanation_font_size = (isset($options['quest_explanation_font_size']) && $options['quest_explanation_font_size'] != '') ? absint(sanitize_text_field($options['quest_explanation_font_size'])) : '16';

        // Font size for the question explanation | Mobile
        $quest_explanation_mobile_font_size = (isset($options['quest_explanation_mobile_font_size']) && $options['quest_explanation_mobile_font_size'] != '') ? absint(esc_attr($options['quest_explanation_mobile_font_size'])) : $quest_explanation_font_size;

        // Font size for the Note text | PC
        $note_text_font_size = (isset($options['note_text_font_size']) && $options['note_text_font_size'] != '') ? absint(esc_attr($options['note_text_font_size'])) : '14';

        // Font size for the Note text | Mobile
        $note_text_mobile_font_size = (isset($options['note_text_mobile_font_size']) && $options['note_text_mobile_font_size'] != '') ? absint(esc_attr($options['note_text_mobile_font_size'])) : $note_text_font_size;


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

        //  Box Shadow X offset
        $quiz_answer_box_shadow_x_offset = (isset($options['quiz_answer_box_shadow_x_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) ) : 0;

        //  Box Shadow Y offset
        $quiz_answer_box_shadow_y_offset = (isset($options['quiz_answer_box_shadow_y_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) ) : 0;

        //  Box Shadow Z offset
        $quiz_answer_box_shadow_z_offset = (isset($options['quiz_answer_box_shadow_z_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) ) : 10;

        $answer_box_shadow_offsets = $quiz_answer_box_shadow_x_offset . 'px ' . $quiz_answer_box_shadow_y_offset . 'px ' . $quiz_answer_box_shadow_z_offset . 'px ';

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
        $description = Quiz_Maker_Data::replace_message_variables($quiz['description'], $message_variables_data);
        $description = Quiz_Maker_Data::ays_autoembed( $description );

                
        $quiz_image = $quiz['quiz_image'];
        
        
        $quiz_rate_reports = '';
        $quiz_result_reports = '';
        $active_passwords = array();
        
        if($questions_count == 0){
            $empty_questions_notification = '<p id="ays_no_questions_message" style="color:red">' . __('You need to add questions', $this->plugin_name) . '</p>';
            $empty_questions_button = "disabled";
        }else{
            $empty_questions_notification = "";
            $empty_questions_button = "";
        }

        if( $this->get_prop( 'is_training' ) === true && empty( $arr_questions ) ){
            $empty_questions_notification = "<button type='button' class='action-button ays_restart_training_button'>
                    <i class='ays_fa ays_fa_undo'></i>
                    <span>". $this->buttons_texts['restartQuizButton'] ."</span>
                </button>";;
        }
        
        // Limit Quiz by password
        $general_password_radio = (isset($options['generate_password']) && $options['generate_password'] != '') ? $options['generate_password'] : 'general';
        $generated_passwords  = (isset($options['generated_passwords']) && $options['generated_passwords'] != '') ? $options['generated_passwords'] : array();
        if(!empty($generated_passwords)){
            $active_passwords = (isset( $generated_passwords['active_passwords']) && !empty( $generated_passwords['active_passwords'])) ?  $generated_passwords['active_passwords'] : array();
        }

        $password_message = "";
        $start_button_disabled = "";
        $quiz_password_message_html = "";
        $password_message_with_toggle = "";

        // Password quiz
        $quiz_password = ( isset( $options['password_quiz']) && $options['password_quiz'] != '' ) ? stripslashes( $options['password_quiz'] ) : '';

        // Quiz password width
        $quiz_password_width = (isset($options['quiz_password_width']) && ( $options['quiz_password_width'] ) != '' && ( $options['quiz_password_width'] ) != 0) ? esc_attr( absint( $options['quiz_password_width'] ) ) : "";

        $quiz_password_width_css = "";
        if ( $quiz_password_width != "" ) {
            $quiz_password_width_css = $quiz_password_width . "px";
        } else {
            $quiz_password_width_css = "100%";
        }

        $quiz_password_flag = true;
        switch ( $general_password_radio ) {
            case 'generated_password':
                if ( empty( $active_passwords ) ) {
                    $quiz_password_flag = false;
                }
                break;
            case 'general':
            default:
                if ( $quiz_password == "" ) {
                    $quiz_password_flag = false;
                }
                break;
        }

        if(isset($options['enable_password']) && $options['enable_password'] == 'on' && $quiz_password_flag){

            // Password for passing quiz | Message
            $quiz_password_message = ( isset( $options['quiz_password_message']) && $options['quiz_password_message'] != '' ) ? stripslashes( $options['quiz_password_message'] ) : '';

            // Enable toggle password visibility
            $options['quiz_enable_password_visibility'] = isset($options['quiz_enable_password_visibility']) ? $options['quiz_enable_password_visibility'] : 'off';
            $quiz_enable_password_visibility = (isset($options['quiz_enable_password_visibility']) && $options['quiz_enable_password_visibility'] == 'on') ? true : false;

            if ( $quiz_password_message != '' ) {
                $quiz_password_message_html .= '<div class="ays-quiz-password-message-box">';
                    $quiz_password_message_html .= Quiz_Maker_Data::ays_autoembed($quiz_password_message);
                $quiz_password_message_html .= '</div>';
            }

            $password_message = "<input type='password' autocomplete='no' id='ays_quiz_password_val_". $id ."' class='ays_quiz_password' placeholder='". __( "Please enter password", $this->plugin_name) ."'>";

            if ( $quiz_enable_password_visibility ) {
                $password_message_with_toggle .= "<div class='ays-quiz-password-toggle-visibility-box'>";
                    $password_message_with_toggle .= $password_message;
                    $password_message_with_toggle .= "<img src='". AYS_QUIZ_PUBLIC_URL ."/images/quiz-maker-eye-visibility-off.svg' class='ays-quiz-password-toggle ays-quiz-password-toggle-visibility-off'>";
                    $password_message_with_toggle .= "<img src='". AYS_QUIZ_PUBLIC_URL ."/images/quiz-maker-eye-visibility.svg' class='ays-quiz-password-toggle ays-quiz-password-toggle-visibility ays_display_none'>";
                $password_message_with_toggle .= "</div>";

                $password_message = $password_message_with_toggle;
            }

            $start_button_disabled = " disabled='disabled' ";

            $password_message = $quiz_password_message . $password_message;
        }

        // Checking confirmation box for leaving the page enabled or diabled
        if (isset($options['enable_leave_page']) && $options['enable_leave_page'] == 'on') {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }elseif (! isset($options['enable_leave_page'])) {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }else{
            $enable_leave_page = '';
        }

        // Enable lazy loading attribute for images
        $settings_options['quiz_enable_lazy_loading'] = isset($settings_options['quiz_enable_lazy_loading']) ? esc_attr( $settings_options['quiz_enable_lazy_loading'] ) : 'off';
        $quiz_enable_lazy_loading = (isset($settings_options['quiz_enable_lazy_loading']) && esc_attr( $settings_options['quiz_enable_lazy_loading'] ) == "on") ? true : false;

        // Disable answer hover
        $settings_options['enable_start_button_loader'] = isset($settings_options['enable_start_button_loader']) ? sanitize_text_field($settings_options['enable_start_button_loader']) : 'off';
        $enable_start_button_loader = (isset($settings_options['enable_start_button_loader']) && sanitize_text_field($settings_options['enable_start_button_loader']) == "on") ? true : false;

        // Show information form only once
        $settings_options['quiz_show_information_form_only_once'] = isset($settings_options['quiz_show_information_form_only_once']) ? sanitize_text_field($settings_options['quiz_show_information_form_only_once']) : 'off';
        $quiz_show_information_form_only_once = (isset($settings_options['quiz_show_information_form_only_once']) && sanitize_text_field($settings_options['quiz_show_information_form_only_once']) == "on") ? true : false;

        //Enable keyboard navigation
        $options['quiz_enable_keyboard_navigation'] = isset($options['quiz_enable_keyboard_navigation']) ? $options['quiz_enable_keyboard_navigation'] : 'off';
        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == "on") ? true : false;
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){            
            $class_for_keyboard = "ays-quiz-keyboard-active";
        }

        $quiz_start_button = "<input type='button' $empty_questions_button $start_button_disabled name='next' class='ays_next start_button action-button ".$class_for_keyboard."' value='". $this->buttons_texts['startButton'] ."' ". $enable_leave_page ." />";        
        
        if ( $enable_start_button_loader ) {
            $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();
            if ( $is_elementor_exists ) {
                $enable_start_button_loader = false;
            }
        }

        if ( $enable_start_button_loader ) {
            if ($questions_count != 0) {
                $quiz_start_butto_html = "<input type='button' $empty_questions_button class='ays_next start_button action-button ays_quiz_enable_loader ".$class_for_keyboard."' disabled='disabled' value='". __('Loading ...', $this->plugin_name) ."' ". $enable_leave_page ." />".$empty_questions_notification;

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

            // $quiz_result_reports = Quiz_Maker_Data::get_quiz_results_count_by_id_for_quiz_demo($id, $quiz_result_reports);

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

        // Show question category description
        $options['quiz_enable_question_category_description'] = isset($options['quiz_enable_question_category_description']) ? $options['quiz_enable_question_category_description'] : 'off';
        $quiz_enable_question_category_description = (isset($options['quiz_enable_question_category_description']) && $options['quiz_enable_question_category_description'] == 'on') ? true : false;

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
        $quiz_message_before_timer = '';
            
        
        /*
         * Generating Quiz timer
         *
         * Checking timer enabled or diabled
         */
        
        $timer_enabled = false;
        if (isset($options['enable_timer']) && $options['enable_timer'] == 'on') {
            $timer_enabled = true;
            $timer_text = (isset($options['timer_text'])) ? $options['timer_text'] : '';
            $timer_text = Quiz_Maker_Data::replace_message_variables($timer_text, $message_variables_data);
            $timer_text = Quiz_Maker_Data::ays_autoembed( $timer_text );
            $after_timer_text = (isset($options['after_timer_text'])) ? $options['after_timer_text'] : '';
            $after_timer_text = Quiz_Maker_Data::replace_message_variables($after_timer_text, $message_variables_data);
            $after_timer_text = Quiz_Maker_Data::ays_autoembed( $after_timer_text );

            // Message before timer
            $quiz_message_before_timer = (isset($options['quiz_message_before_timer']) && $options['quiz_message_before_timer'] != '') ? esc_attr( sanitize_text_field( $options['quiz_message_before_timer'] ) ) : '';

            $quiz_message_before_timer_class = '';
            if ( $quiz_message_before_timer != '' ) {
                $quiz_message_before_timer_class = 'ays-quiz-message-before-timer';
            }

            $hide_timer_cont = "";
            $empty_after_timer_text_class = "";
            if($timer_text == ""){
                $hide_timer_cont = " style='display:none;' ";
            }
            if($after_timer_text == ""){
                $empty_after_timer_text_class = " empty_after_timer_text ";
            }
            $timer_row = "<section {$hide_timer_cont} class='ays_quiz_timer_container'>
                <div class='ays-quiz-timer ". $quiz_message_before_timer_class ."' data-timer='" . $options['timer'] . "'>{$timer_text}</div>
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

        if ( strstr($actual_link, 'action=ays_quiz_iframe_shortcode') && strstr($actual_link, 'embed=true') && $actual_link != "" ) {
            $actual_link = (isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] != "") ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : $actual_link;
        } else {
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on"){
                $actual_link = "https" . $actual_link;
            }else{
                $actual_link = "http" . $actual_link;
            }
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

        // Questions numbering
        $show_questions_numbering = (isset($options['show_questions_numbering']) && $options['show_questions_numbering'] !== '') ?  $options['show_questions_numbering'] : 'none';


        
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

        if(isset($options['enable_questions_reporting']) && $options['enable_questions_reporting'] == 'on'){
            $questions_reporting = true;
            $send_email = ( isset($options['quiz_enable_questions_reporting_mail']) && $options['quiz_enable_questions_reporting_mail'] == 'on' ) ? 'on' : 'off';

            $questions_reporting_modal = '<div class="ays-modal-reports" id="ays-quiz-question-report-modal">
                                            <div class="ays-modal-content-reports">
                                                <span class="ays-close-reports-window"><img src="' . AYS_QUIZ_PUBLIC_URL . '/images/close-report-window.svg" title="close"></span>
                                                <h3>' . __( "Report a question", $this->plugin_name ) . '</h3>
                                                <form id="ays-quiz-question-report-form">
                                                    <label for="ays-quiz-question-report-textarea">' . __( "What's wrong with this question?", $this->plugin_name ) . '</label>
                                                    <textarea id="ays-quiz-question-report-textarea" name="ays-quiz-question-report-textarea"></textarea>
                                                    <div class="ays-quiz-question-report-error">' . __( "You cannot submit an empty report. Please add some details.", $this->plugin_name ) . '</div>
                                                    <input type="hidden" class="ays-quiz-report-question-id" value="">
                                                    <input type="hidden" class="ays-quiz-report-quiz-id" value="' . $id . '">
                                                    <input type="hidden" class="ays-quiz-report-question-send-email" value="' . $send_email . '">
                                                    <input type="submit" class="ays-quiz-submit-question-report" value="Submit">
                                                </form>
                                                <div class="ays-quiz-preloader" style="top:0">
                                                    <img class="loader" src="' . AYS_QUIZ_ADMIN_URL . '/images/loaders/tail-spin.svg">
                                                </div>
                                            </div>
                                          </div>';

        }else{
            $questions_reporting = false;
            $questions_reporting_modal = '';
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
        $questions_hint_type = (isset($options['questions_hint_icon_or_text']) && $options['questions_hint_icon_or_text'] != '' ) ? sanitize_text_field( $options['questions_hint_icon_or_text'] ) : 'default';
        $questions_hint_value = (isset($options['questions_hint_value']) && $options['questions_hint_value'] != '') ? stripslashes(esc_attr($options['questions_hint_value'])) : '';
        $questions_hint_button_value = (isset($options['questions_hint_button_value']) && $options['questions_hint_button_value'] != '') ? stripslashes(esc_attr($options['questions_hint_button_value'])) : '';

        $questions_hint_arr = array(
            'questionsHintType' => $questions_hint_type,
            'questionsHintValue' => $questions_hint_value,
            'questionsHintButtonValue' => $questions_hint_button_value,
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

        // Question per page type
        $question_count_per_page_type = (isset($options['question_count_per_page_type']) && $options['question_count_per_page_type'] != '') ? sanitize_text_field($options['question_count_per_page_type']) : 'general';

        $question_count_per_page_custom_order = (isset($options['question_count_per_page_custom_order']) && $options['question_count_per_page_custom_order'] != "" ) ? sanitize_text_field($options['question_count_per_page_custom_order']) : '';

        $question_count_per_page_custom_order_first = null;
        if( !empty( $question_count_per_page_custom_order ) ){
            $question_count_per_page_custom_order_arr = explode(',', $question_count_per_page_custom_order);

            if ( !empty( $question_count_per_page_custom_order_arr ) ) {
                $question_count_per_page_custom_order_arr_new = array();
                foreach ($question_count_per_page_custom_order_arr as $per_page_key => $per_page_value) {
                    if( is_numeric( $per_page_value ) && $per_page_value != "" && $per_page_value != 0 ){
                        if( $per_page_key == 0 ){
                            $question_count_per_page_custom_order_first = $per_page_value;
                        }
                        $question_count_per_page_custom_order_arr_new[] = absint($per_page_value);
                    }
                }
                $question_count_per_page_custom_order = implode( "," , $question_count_per_page_custom_order_arr_new);
            }
        }
        
        /*
         * Quiz all questions in one page
         */
        if(isset($options['quiz_display_all_questions']) && $options['quiz_display_all_questions'] == "on"){
            $display_all_questions = true;
        }else{
            $display_all_questions = false;
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
            $restart_button = "<button type='button' class='action-button ays_restart_button ".$class_for_keyboard."'>
                    <i class='ays_fa ays_fa_undo'></i>
                    <span>". $this->buttons_texts['restartQuizButton'] ."</span>
                </button>";
        }else{
            $restart_button = "";
        }

        
        if($this->chain_id !== null){
            $chain_quiz_button = "<button type='button' class='action-button ays_chain_next_quiz_button ays_display_none ".$class_for_keyboard."'>
                    <span>". $this->buttons_texts['nextChainQuiz'] ."</span>
                </button>";
        }else{
            $chain_quiz_button = "";
        }

        if($this->chain_result_btn !== null){
            $chain_quiz_see_result_button = "<button type='button' class='action-button ays_chain_see_result_button ays_display_none ".$class_for_keyboard."'>
                    <span>". $this->buttons_texts['seeResultChainQuiz'] ."</span>
                </button>";
        }else{
            $chain_quiz_see_result_button = "";
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
            $exit_button = "<a style='width:auto;' href='".$exit_redirect_url."' class='action-button ays-quiz-exit-button ".$class_for_keyboard."' target='_self'>
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

        $options['ays_allow_exporting_quizzes'] = isset($options['ays_allow_exporting_quizzes']) ? $options['ays_allow_exporting_quizzes'] : 'off';
        $allow_exporting_quizzes = (isset($options['ays_allow_exporting_quizzes']) && $options['ays_allow_exporting_quizzes'] == "on") ? true : false;

        if($allow_exporting_quizzes) {
            $users_to_export = (isset($options['ays_users_to_export_search']) && $options['ays_users_to_export_search'] != '') ? $options['ays_users_to_export_search'] : '';
            if(is_user_logged_in()){
                $current_users = wp_get_current_user();
                $current_user  = $current_users->data->ID;

                if (is_array($users_to_export)) {
                    if(in_array($current_user, $users_to_export)){
                        $export_quiz_button_visibility = true;
                    }
                    else {
                        $export_quiz_button_visibility = false;
                    }
                }
            } else {
                $export_quiz_button_visibility = false;
            }
        }
        else {
            $export_quiz_button_visibility = false;
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
            "exportQuizButton" => $export_quiz_button_visibility,
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
                    width: 100%;
                }
                #ays-quiz-container-" . $id . " .select2-container .select2-selection--single .select2-selection__rendered {
                    padding-right: 30px;
                }
                #ays-quiz-container-" . $id . " .ays-field.ays-select-field {
                    margin: 0;
                }

                #ays-quiz-container-" . $id . " .ays-field.ays-matching-field-option {
                    flex-direction: row-reverse !important;
                }
                #ays-quiz-container-" . $id . " .ays-field.ays-matching-field-option .ays-matching-field-choice {
                    text-align: right;
                    padding-right: 10px;
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

                #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container p.ays-question-counter {
                    left: 20px;
                }

                div#ays-quiz-container-" . $id . " .ays_question_report {
                    text-align: left;
                }
                ";
        }else{
            $rtl_direction = false;
        }

        // Enable copy protection
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        
        
        /*
         * General Settings
         * Animation Top (px)
         * Store all not finished results
         */
        $quiz_animation_top = (isset($settings_options['quiz_animation_top']) && $settings_options['quiz_animation_top'] != 0) ? absint(intval($settings_options['quiz_animation_top'])) : 100;
        $settings_options['quiz_enable_animation_top'] = isset($settings_options['quiz_enable_animation_top']) ? $settings_options['quiz_enable_animation_top'] : 'on';
        $quiz_enable_animation_top = (isset($settings_options['quiz_enable_animation_top']) && $settings_options['quiz_enable_animation_top'] == "on") ? 'on' : 'off';


        // Store all not finished results
        $settings_options['store_all_not_finished_results'] = (isset( $settings_options['store_all_not_finished_results'] ) && $settings_options['store_all_not_finished_results'] == 'on') ? $settings_options['store_all_not_finished_results'] : 'off';
        $store_all_not_finished_results = (isset( $settings_options['store_all_not_finished_results'] ) && $settings_options['store_all_not_finished_results'] == 'on') ? true : false;

        
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

        // Show question explanation
        $show_questions_explanation = (isset($options['show_questions_explanation']) && $options['show_questions_explanation'] != '') ? sanitize_text_field( $options['show_questions_explanation'] ) : 'on_results_page';

        // Show messages for right/wrong answers
        $answers_rw_texts = (isset($options['answers_rw_texts']) && $options['answers_rw_texts'] != '') ? sanitize_text_field( $options['answers_rw_texts'] ) : 'on_passing';

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
        
        // Add all reviews link
        $options['quiz_make_all_review_link'] = isset($options['quiz_make_all_review_link']) ? sanitize_text_field($options['quiz_make_all_review_link']) : 'off';
        $quiz_make_all_review_link = (isset($options['quiz_make_all_review_link']) && $options['quiz_make_all_review_link'] == 'on') ? true : false;

        $all_review_link_html = '';
        if ( $quiz_make_all_review_link ) {
            if ( Quiz_Maker_Data::ays_get_count_of_reviews(0, 5, $id) > 0 ) {
                $all_review_link_html = "<div class='ays-quiz-rate-link-box'><span class='ays-quiz-rate-link'>". __( "See review", $this->plugin_name ) ."</span></div>";
            }
        }

        // Enable quiz assessment | Placeholder text
        $quiz_review_placeholder_text = (isset($options['quiz_review_placeholder_text']) && $options['quiz_review_placeholder_text'] != '') ? stripslashes( esc_attr( $options['quiz_review_placeholder_text'] ) ) : "";

        // Make review required
        $options['quiz_make_review_required'] = isset($options['quiz_make_review_required']) ? sanitize_text_field($options['quiz_make_review_required']) : 'off';
        $quiz_make_review_required = (isset($options['quiz_make_review_required']) && $options['quiz_make_review_required'] == 'on') ? "true" : "false";

        // Enable users' anonymous assessment
        $options['quiz_enable_user_cհoosing_anonymous_assessment'] = isset($options['quiz_enable_user_cհoosing_anonymous_assessment']) ? sanitize_text_field($options['quiz_enable_user_cհoosing_anonymous_assessment']) : 'off';
        $quiz_enable_user_cհoosing_anonymous_assessment = (isset($options['quiz_enable_user_cհoosing_anonymous_assessment']) && $options['quiz_enable_user_cհoosing_anonymous_assessment'] == 'on') ? true : false;

        /*
         * Passed or Failed quiz score html
         */
        $pass_score_html = "<div class='ays_score_message'></div>";

        
    // if($calculate_score == 'by_points'){
    //     $enable_questions_result = '';
    // }
        
        /*
         * Quiz rate
         *
         * Generating HTML code
         */
        
        if(isset($options['rate_form_title'])){
            $rate_form_title = Quiz_Maker_Data::ays_autoembed( $options['rate_form_title'] );
        }
        
        if(isset($options['enable_quiz_rate']) && $options['enable_quiz_rate'] == "on"){

            // Thank you message | Review
            $quiz_review_thank_you_message = (isset($options['quiz_review_thank_you_message']) && $options['quiz_review_thank_you_message'] != '') ? Quiz_Maker_Data::ays_autoembed( $options['quiz_review_thank_you_message'] ) : "";

            // Enable Comment Field
            $options['quiz_review_enable_comment_field'] = isset($options['quiz_review_enable_comment_field']) ? sanitize_text_field($options['quiz_review_enable_comment_field']) : 'on';
            $quiz_review_enable_comment_field = (isset($options['quiz_review_enable_comment_field']) && $options['quiz_review_enable_comment_field'] == 'on') ? true : false;

            $review_thank_you_message = "";
            if ( $quiz_review_thank_you_message != "" ) {
                $review_thank_you_message = "<div class='ays-quiz-review-thank-you-message ays_display_none'>". $quiz_review_thank_you_message ."</div>";
            }

            $review_comment_field_html = "";
            if ( $quiz_review_enable_comment_field ) {
                $review_comment_field_html = "<textarea id='quiz_rate_reason_".$id."' class='quiz_rate_reason' data-required='". $quiz_make_review_required ."' placeholder='". $quiz_review_placeholder_text ."'></textarea>";
            }

            $enable_user_cհoosing_anonymous_assessment_html = "";
            if( $quiz_enable_user_cհoosing_anonymous_assessment ){
                $enable_user_cհoosing_anonymous_assessment_html = "<div class='ays-quiz-user-cհoosing-anonymous-assessment'>
                <label for='ays-quiz-user-cհoosing-anonymous-assessment-{$id}'>". __("Anonymous feedback", $this->plugin_name) ."</label>
                <input type='checkbox' name='ays_quiz_user_cհoosing_anonymous_assessment' id='ays-quiz-user-cհoosing-anonymous-assessment-{$id}' class='ays-quiz-user-cհoosing-anonymous-assessment'value='on'/></div>";
            }

            $quiz_rate_html = "<div class='ays_quiz_rete'>
                <div>$rate_form_title</div>
                $enable_user_cհoosing_anonymous_assessment_html
                <div class='for_quiz_rate ui huge star rating' data-rating='0' data-max-rating='5'></div>
                <div class='ays-quiz-lds-spinner-box' style='text-align:center;'><div class='lds-spinner-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                ". $all_review_link_html ."
                ". $review_thank_you_message ."
                <div class='for_quiz_rate_reason'>
                    ". $review_comment_field_html ."
                    <div class='ays_feedback_button_div'>
                        <button type='button' class='action-button ".$class_for_keyboard."'>". $this->buttons_texts['sendFeedbackButton'] ."</button>
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
        
        // Heading for social buttons
        $social_buttons_heading = (isset($options['social_buttons_heading']) && $options['social_buttons_heading'] != '') ? stripslashes( wpautop( $options['social_buttons_heading'] ) ) : "";

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


        if ( ! $quiz_enable_linkedin_share_button && ! $quiz_enable_facebook_share_button && ! $quiz_enable_twitter_share_button && ! $quiz_enable_vkontakte_share_button ) {
            $quiz_enable_linkedin_share_button = true;
            $quiz_enable_facebook_share_button = true;
            $quiz_enable_twitter_share_button = true;
            $quiz_enable_vkontakte_share_button = true;
        }

        
        if(isset($options['enable_social_buttons']) && $options['enable_social_buttons'] == "on"){
            $ays_social_buttons .= "<div class='ays-quiz-social-shares'>";
                $ays_social_buttons .= "<div class='ays-quiz-social-shares-heading'>";
                    $ays_social_buttons .= $social_buttons_heading;
                $ays_social_buttons .= "</div>";

            if ( $quiz_enable_linkedin_share_button ) {
                $ays_social_buttons .= "
                    <!-- Branded LinkedIn button -->
                    <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-linkedin'
                       href='https://www.linkedin.com/shareArticle?mini=true&url=" . $actual_link . "'
                       title='Share on LinkedIn'>
                        <span class='ays-quiz-share-btn-icon'></span>
                        <span class='ays-share-btn-text'>LinkedIn</span>
                    </a>";
            }

            if ( $quiz_enable_facebook_share_button ) {
                $ays_social_buttons .= "
                    <!-- Branded Facebook button -->
                    <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-facebook'
                       href='https://www.facebook.com/sharer/sharer.php?u=" . $actual_link . "'
                       title='Share on Facebook'>
                        <span class='ays-quiz-share-btn-icon'></span>
                        <span class='ays-share-btn-text'>Facebook</span>
                    </a>";
            }

            if ( $quiz_enable_twitter_share_button ) {
                $ays_social_buttons .= "
                <!-- Branded Twitter button -->
                <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-twitter'
                   href='https://twitter.com/share?url=" . $actual_link . "'
                   title='Share on Twitter'>
                    <span class='ays-quiz-share-btn-icon'></span>
                    <span class='ays-share-btn-text'>Twitter</span>
                </a>";
            }

            if ( $quiz_enable_vkontakte_share_button ) {
                $ays_social_buttons .= "
                <!-- Branded VK button -->
                <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-vkontakte'
                   href='https://vk.com/share.php?url=" . $actual_link . "'
                   title='Share on VKontakte'>
                    <span class='ays-quiz-share-btn-icon'></span>
                    <span class='ays-share-btn-text'>VKontakte</span>
                </a>";
            }

            $ays_social_buttons .= "</div>";

        }
        
        
        /*
         * Quiz social media links
         *
         * Generating HTML code
         */

        // Heading for social media links
        $social_links_heading = (isset($options['social_links_heading']) && $options['social_links_heading'] != '') ? stripslashes( wpautop( $options['social_links_heading'] ) ) : "";

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
        $ays_social_links_array = array();

        $linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? $social_links['linkedin_link'] : '';
        $facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? $social_links['facebook_link'] : '';
        $twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? $social_links['twitter_link'] : '';
        $vkontakte_link = isset($social_links['vkontakte_link']) && $social_links['vkontakte_link'] != '' ? $social_links['vkontakte_link'] : '';
        $instagram_link = isset($social_links['instagram_link']) && $social_links['instagram_link'] != '' ? $social_links['instagram_link'] : '';
        $youtube_link = isset($social_links['youtube_link']) && $social_links['youtube_link'] != '' ? $social_links['youtube_link'] : '';
        $behance_link = isset($social_links['behance_link']) && $social_links['behance_link'] != '' ? $social_links['behance_link'] : '';

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
        if($instagram_link != ''){
            $ays_social_links_array['Instagram'] = $instagram_link;
        }
        if($youtube_link != ''){
            $ays_social_links_array['Youtube'] = $youtube_link;
        }
        if($behance_link != ''){
            $ays_social_links_array['Behance'] = $behance_link;
        }
        $ays_social_links = '';
        
        if($enable_social_links){
            $ays_social_links .= "<div class='ays-quiz-social-shares'>";

            if( $social_links_heading != "" ) {
                $ays_social_links .= "<div class='ays-quiz-social-links-heading'>";
                    $ays_social_links .= $social_links_heading;
                $ays_social_links .= "</div>";
            }

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
                    <div class='ays-loader' data-class='ays-loader-text' data-role='loader' style='text-align: center;'>
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
        $user_role_count_result = null;
        $limit_attempts_count_user_role_message = '';
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
                        // $result = $check_user_by_ip;

                        $result_get_limit_cookie_count = 0;
                        $result_check_user_by_ip = 0;
                        if( $check_cookie ){
                            $result_get_limit_cookie_count = absint( Quiz_Maker_Data::get_limit_cookie_count( $limit_users_attr ) );
                        }
                        
                        if ( $check_user_by_ip > 0 ) {
                            $result_check_user_by_ip = absint( $check_user_by_ip );
                        }
                        
                        if( $result_get_limit_cookie_count >= $result_check_user_by_ip ){
                            $result = $result_get_limit_cookie_count;
                        } elseif( $result_get_limit_cookie_count < $result_check_user_by_ip ){
                            $result = $result_check_user_by_ip;
                        } else {
                            $result = $check_user_by_ip;
                        }
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

            // Hide attempts limitation notice
            $options['hide_limit_attempts_notice'] = isset($options['hide_limit_attempts_notice']) ? sanitize_text_field($options['hide_limit_attempts_notice']) : 'off';
            $hide_limit_attempts_notice = (isset($options['hide_limit_attempts_notice']) && $options['hide_limit_attempts_notice'] == 'on') ? false : true;


            // Limit attempts count by user role
            $results_table = $wpdb->prefix . 'aysquiz_reports';

            $limit_attempts_count_by_user_role = (isset($options['limit_attempts_count_by_user_role']) && $options['limit_attempts_count_by_user_role'] != "") ? absint(intval($options['limit_attempts_count_by_user_role'])) : null;

            if( $limit_attempts_count_by_user_role !== null ){
                if(is_user_logged_in()){
                    $restrict_user_role = (isset($options['user_role']) && !empty($options['user_role']) != '') ? $options['user_role'] : array();
                    if(isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on' && !empty( $restrict_user_role )){
                        $user_id = get_current_user_id();

                        $user_role_count_sql = "SELECT COUNT(*) FROM {$results_table} WHERE quiz_id = ". $id ." AND user_id=".$user_id;
                        $user_role_count_result = $wpdb->get_var($user_role_count_sql);


                        // Get the user object.
                        $user = get_userdata( $user_id );

                        // Get all the user roles as an array.
                        $user_roles = $user->roles;

                        foreach ($restrict_user_role as $key => $user_role) {
                            if(is_array($user_roles)){
                                if( in_array( strtolower($user_role), $user_roles ) ){
                                    if( absint( $user_role_count_result ) >= $limit_attempts_count_by_user_role ){
                                        $limit_attempts_count_user_role_message = '<div class="logged_in_message">'. __( "You have already passed with this user role", $this->plugin_name ) . '</div>';
                                    }
                                }
                            }else{
                                if( absint( $user_roles ) == strtolower($user_role) ){
                                    if( $user_role_count_result >= $limit_attempts_count_by_user_role ){
                                        $limit_attempts_count_user_role_message = '<div class="logged_in_message">'. __( "You have already passed with this user role", $this->plugin_name ) . '</div>';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if( $user_role_count_result !== null ){
                $result = $user_role_count_result;
                $quiz_max_pass_count = $limit_attempts_count_by_user_role !== null ? $limit_attempts_count_by_user_role : $quiz_max_pass_count;
            }

            if( intval( $result ) < $quiz_max_pass_count ){
                $attempts_count = $quiz_max_pass_count - $result;
                $attempts_count_last_chance = "<span class='ays-quiz-limitation-attempts-count'>" . $attempts_count . "</span>";
                if( $hide_limit_attempts_notice ){
                    $count_of_attempts_remaining .= '<p class="ays-quiz-limitation-attempts-notice">';
                    $count_of_attempts_remaining .= __('The number of attempts remaining is ', $this->plugin_name) . " " . $attempts_count;
                    $count_of_attempts_remaining .= '</p>';
                }
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
                    $limit_message = Quiz_Maker_Data::replace_message_variables($limit_message, $message_variables_data);
                }

                if( $user_role_count_result !== null ){
                    $limit_message = $limit_attempts_count_user_role_message;
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

        if( is_user_logged_in() ){
            $show_login_form = false;
        }

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
                $logged_users_message = Quiz_Maker_Data::replace_message_variables($logged_users_message, $message_variables_data);
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

            $user_role = (isset($options['user_role']) && $options['user_role'] != '') ? $options['user_role'] : '';
            if (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on' && !empty( $user_role )) {
                $user = wp_get_current_user();
                $user_roles   = $wp_roles->role_names;
                $message = (isset($options['restriction_pass_message']) && $options['restriction_pass_message'] != '') ? $options['restriction_pass_message'] : __('Permission Denied', $this->plugin_name);
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
        
        // Quiz takers message
        $quiz_tackers_message = ( isset($options['quiz_tackers_message']) && $options['quiz_tackers_message'] != '' ) ? stripslashes( wpautop( $options['quiz_tackers_message'] ) ) : __( "This quiz is expired!", $this->plugin_name );

        if ( $quiz_tackers_message != __( "This quiz is expired!", $this->plugin_name ) ) {
            $tackers_message = "<div class='ays-quiz-limitation-count-of-takers'>". $quiz_tackers_message ."</div>";
        } else {
            $tackers_message = "<div class='ays-quiz-limitation-count-of-takers'><p>" . __( "This quiz is expired!", $this->plugin_name ) . "</p></div>";
        }


    /*******************************************************************************************************/

        
        /*
         * Schedule quiz
         * Check is quiz expired
         */
        
        $is_expired = false;
        $active_date_check = false;
        $UTC_seconds = null;
        $startDate = '';
        $endDate = '';
        $startDate_atr = '';
        $endDate_atr = '';
        $current_time = strtotime(current_time( "Y:m:d H:i:s" ));
        $activeInterval = isset( $options['activeInterval'] ) && $options['activeInterval'] != '' ? $options['activeInterval'] : current_time( 'mysql' );
        $deactiveInterval = isset( $options['deactiveInterval'] ) && $options['deactiveInterval'] != '' ? $options['deactiveInterval'] : current_time( 'mysql' );
		$startDate = strtotime( $activeInterval );
		$endDate   = strtotime( $deactiveInterval );

        // Timezone | Schedule the quiz
        $ays_quiz_schedule_timezone = (isset($options['quiz_schedule_timezone']) && $options['quiz_schedule_timezone'] != '') ? sanitize_text_field( $options['quiz_schedule_timezone'] ) : get_option( 'timezone_string' );

        if ( class_exists( 'DateTimeZone' )) {

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

            $if_timezone_UTC = false;
            if ( strpos($ays_quiz_schedule_timezone, 'UTC+') !== false ) {
                $if_timezone_UTC = true;

                $UTC_val_arr = explode('+', $ays_quiz_schedule_timezone );

                $UTC_val     = ( isset( $UTC_val_arr[1] ) && $UTC_val_arr[1] != '' ) ? $UTC_val_arr[1] : 0;

                $UTC_seconds = (int) ($UTC_val * 3600);

            } elseif ( strpos($ays_quiz_schedule_timezone, 'UTC-') !== false ) {
                $if_timezone_UTC = true;

                $UTC_val_arr = explode('-', $ays_quiz_schedule_timezone );

                $UTC_val     = ( isset( $UTC_val_arr[1] ) && $UTC_val_arr[1] != '' ) ? $UTC_val_arr[1] : 0;

                $UTC_seconds =  (int) ( -1 * ( $UTC_val * 3600 ) );
            }

            if (in_array( $ays_quiz_schedule_timezone , DateTimeZone::listIdentifiers()) && ! $if_timezone_UTC ) {

                $Date_Time_Zone = new DateTime("now", new DateTimeZone( $ays_quiz_schedule_timezone ));
                $current_time   = strtotime( $Date_Time_Zone->format( "Y:m:d H:i:s" ) );
            } else {
                if ( ! is_null( $UTC_seconds ) && ! empty( $UTC_seconds ) ) {
                    $Date_Time_Zone = new DateTime("now", new DateTimeZone( 'UTC' ));
                    $current_time   = strtotime( $Date_Time_Zone->format( "Y:m:d H:i:s" ) ) + ( $UTC_seconds );
                } else {
                    $current_time = strtotime(current_time( "Y:m:d H:i:s" ));
                }
            }
        }

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
                        $show_timer .= '<p class="show_timer_countdown" data-timer_countdown="'.$startDate_atr.'"></p>';
                    }else if ($show_timer_type == 'enddate') {
                        $show_timer .= '<p class="show_timer_countdown">' . __('This Quiz will start on', $this->plugin_name);
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
            $quiz_image_alt_text = Quiz_Maker_Data::ays_quiz_get_image_id_by_url($quiz_image);

            $quiz_image = "<img src='{$quiz_image}' alt='". $quiz_image_alt_text ."' class='ays_quiz_image'>";
        }else{
            $quiz_image = "";
        }

        $ays_protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
         
        $quiz_current_page_link = esc_url( $ays_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        
        
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
            <div class='step active-step'>
                <div class='ays-abs-fs ays-start-page'>
                    {$show_cd_and_author}
                    {$quiz_image}
                    {$title}
                    {$description}
                    <input type='hidden' name='ays_quiz_id' value='{$id}'/>
                    <input type='hidden' name='ays_quiz_curent_page_link' class='ays-quiz-curent-page-link' value='{$quiz_current_page_link}'/>
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
            $chain_quiz_button_html = $chain_quiz_button;
            $chain_quiz_see_result_button_html = $chain_quiz_see_result_button;
        }else{
            $restart_button_html = "";
            $chain_quiz_button_html = "";
            $chain_quiz_see_result_button_html = "";
        }
        
        if($attempts_count_last_chance !== null){
            if($attempts_count_last_chance <= 1){
                $restart_button_html = "";
                $chain_quiz_button_html = $chain_quiz_button;
                $chain_quiz_see_result_button_html = $chain_quiz_see_result_button;
            }
        }

        $main_content_last_part = "<div class='step ays_thank_you_fs'>
            <div class='ays-abs-fs ays-end-page'>
            " . $quiz_loader_html;

        $ays_quiz_results_page_content = $pass_score_html .
            "<div class='ays_message'></div>" .
            $show_score_html .
            $show_average .
            $ays_social_buttons .
            $ays_social_links .
            $progress_bar_html .
            "<p class='ays_restart_button_p'>".
            $restart_button_html .
            $chain_quiz_button_html .
            $chain_quiz_see_result_button .
            $exit_button .
            "</p>".
            $quiz_rate_html;

        if( $payments['paypal']['html'] !== null ) {
            if ($payments['paypal']['payment_type'] == 'postpay') {
                if (is_user_logged_in()) {
                    if ($payments['paypal']['html'] == '') {
                        $main_content_last_part .= __("It seems PayPal Client ID is missing.", $this->plugin_name);
                    } else {
                        $main_content_last_part .= $payments['paypal']['html'];
                    }
                } else {
                    if (isset($payments['paypal']['payment_terms'])) {
                        $payment_terms = $payments['paypal']['payment_terms'];
                    } else {
                        $payment_terms = "lifetime";
                    }
                    switch ($payment_terms) {
                        case "onetime":
                            $main_content_last_part .= $payments['paypal']['html'];
                            break;
                        case "lifetime":
                            $main_content_last_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            break;
                    }
                }
                $main_content_last_part .= "<input type='hidden' name='ays-paypal-type' value='postpay'>";
            }
        }

        if( $payments['stripe']['html'] !== null ) {
            if ($payments['stripe']['payment_type'] == 'postpay') {
                if (is_user_logged_in()) {
                    if ($payments['stripe']['html'] == '') {
                        $main_content_last_part .= __("It seems PayPal Client ID is missing.", $this->plugin_name);
                    } else {
                        $main_content_last_part .= $payments['stripe']['html'];
                    }
                } else {
                    if (isset($payments['stripe']['payment_terms'])) {
                        $payment_terms = $payments['stripe']['payment_terms'];
                    } else {
                        $payment_terms = "lifetime";
                    }
                    switch ($payment_terms) {
                        case "onetime":
                            $main_content_last_part .= $payments['stripe']['html'];
                            break;
                        case "lifetime":
                            $main_content_last_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            break;
                    }
                }
                $main_content_last_part .= "<input type='hidden' name='ays-paypal-type' value='postpay'>";
            }
        }

        if( has_action( 'ays_qm_front_end_recaptcha' ) ){
            $integrations_args = apply_filters( 'ays_qm_front_end_integrations_options', array(), $options );
            $recaptcha_content = apply_filters( "ays_qm_front_end_recaptcha", array(), $integrations_args, $options );

            $main_content_last_part .= implode( $recaptcha_content );
        }


        $main_content_last_part .= "<div class='ays_quiz_results_page'>" . $ays_quiz_results_page_content . "</div>";

        $main_content_last_part .= "</div></div>";
        
        if (! $show_information_form) {
            if(is_user_logged_in()){
                $show_form = null;
            }
        }

        if( $quiz_show_information_form_only_once ){
            $custom_limit_users_attr = array(
                'name' => 'ays_quiz_user_information_cookie',
                'title' => $id,
            );

            $custom_check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $custom_limit_users_attr, false );

            if( $custom_check_cookie ){
                $show_form = null;
            }
        }


        switch( $quiz_arrow_type ){
            case 'default':
                $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                break;
            case 'long_arrow':
                $quiz_arrow_type_class_right = "ays_fa_long_arrow_right";
                break;
            case 'arrow_circle_o':
                $quiz_arrow_type_class_right = "ays_fa_arrow_circle_o_right";
                break;
            case 'arrow_circle':
                $quiz_arrow_type_class_right = "ays_fa_arrow_circle_right";
                break;
            default:
                $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                break;
        }

        if($show_form != null){
            if ($options['information_form'] == "after") {
                $main_content_last_part = "<div class='step'>
                    <div class='ays-abs-fs ays-end-page information_form'>
                    <div class='ays-form-title'>{$form_title}</div>
                        " . $form_inputs . "
                        <div class='ays_buttons_div'>
                            <i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow ".$class_for_keyboard."'></i>
                            <input type='submit' name='ays_finish_quiz' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_next ays_finish action-button ".$class_for_keyboard."' value='" . $this->buttons_texts['seeResultButton'] . "'/>
                        </div>
                    </div>
                  </div>" . $main_content_last_part;
                
            } elseif ($options['information_form'] == "before") {
                $main_content_first_part = $main_content_first_part . "<div class='step' data-role='info-form'>
                    <div class='ays-abs-fs ays-start-page information_form'>
                    <div class='ays-form-title'>{$form_title}</div>
                        " . $form_inputs . "
                        <div class='ays_buttons_div'>
                            <i class='ays_fa " . $quiz_arrow_type_class_right . " ays_next action-button ays_arrow ays_next_arrow " . ($enable_arrows ? '' : 'ays_display_none') . "'></i>
                            <input type='button' name='next' class='ays_next action-button ".$class_for_keyboard." " . ($enable_arrows ? 'ays_display_none' : '') . "' value='" . $this->buttons_texts['nextButton'] . "' />
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
            $options['submit_redirect_after'] = Quiz_Maker_Data::secondsToWords( absint($options['submit_redirect_delay']) );
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
        
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        if( $payment_terms == 'onetime' ){
            if( isset( $quiz_integrations['extra_check'] ) && $quiz_integrations['extra_check'] == 'on' ){
                $order_id = isset( $_SESSION['ays_quiz_paypal_purchased_item'] ) && isset( $_SESSION['ays_quiz_paypal_purchased_item'][$id] ) ? $_SESSION['ays_quiz_paypal_purchased_item'][$id]['order_id'] : 0;
                $quiz_options['paypalStatus'] = array(
                    'extraCheck' => true,
                    'orderId' => $order_id,
                );
            }
        }

        $quiz_options['is_user_logged_in'] = is_user_logged_in();
        $quiz_options['quiz_animation_top'] = $quiz_animation_top;
        $quiz_options['quiz_enable_animation_top'] = $quiz_enable_animation_top;
        $quiz_options['store_all_not_finished_results'] = $store_all_not_finished_results;

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
                
        $options['custom_css'] = isset( $options['custom_css'] ) && !empty( $options['custom_css'] ) ? stripslashes( htmlspecialchars_decode($options['custom_css']) ) : '';
        
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


            /* Styles for Navigation bar */
            #ays-quiz-questions-nav-wrap-" . $id . " {
                width: 100%;";

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

            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-content .ays-quiz-questions-nav-item a.ays_questions_nav_question {
                color: " . $text_color . ";
                border-color: " . $text_color . ";
                background-color: " . $bg_color . ";
            }
            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-content .ays-quiz-questions-nav-item.ays-quiz-questions-nav-item-active a.ays_questions_nav_question {
                box-shadow: inset 0 0 5px " . $text_color . ", 0 0 5px " . $text_color . ";
            }
            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-content .ays-quiz-questions-nav-item.ays-quiz-questions-nav-item-answered a.ays_questions_nav_question {
                color: " . $bg_color . ";
                border-color: " . $bg_color . ";
                background-color: " . $text_color . ";
            }
            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-content .ays-quiz-questions-nav-item a.ays_questions_nav_question.ays_quiz_correct_answer {
                color: rgba(39, 174, 96, 1);
                border-color: rgba(39, 174, 96, 1);
                background-color: rgba(39, 174, 96, 0.4);
            }
            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-content .ays-quiz-questions-nav-item a.ays_questions_nav_question.ays_quiz_wrong_answer {
                color: rgba(243, 134, 129, 1);
                border-color: rgba(243, 134, 129, 1);
                background-color: rgba(243, 134, 129, 0.4);
            }

            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-bookmark-box {
                height: 27px;
                padding: 0 9px 10px;
                text-align: right;
            }
            #ays-quiz-questions-nav-wrap-" . $id . " .ays-quiz-questions-nav-bookmark-box .ays-navbar-bookmark {
                height: 100%;
                cursor: pointer;
            }


            /* Styles for questions */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " div.step {
                min-height: " . $quiz_height . "px;
            }

            /* Styles for text inside quiz container */
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-start-page *:not(input),
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container label[for^=\"ays-answer-\"],
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-matching-field-choice,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container p,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-fs-title,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-fs-subtitle,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .logged_in_message,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-quiz-limitation-count-of-takers,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays-quiz-limitation-count-of-takers *,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_score_message,
            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_message{
               color: " . $text_color . ";
               outline: none;
            }

            #ays-quiz-container-" . $id . ".ays-quiz-container .ays-questions-container .ays_question_hint {
                color: " . $text_color . ";
            }
            
            /* Quiz title / transformation */
            #ays-quiz-container-" . $id . " .ays-fs-title{
                text-transform: " . $quiz_title_transformation . ";
                font-size: " . $quiz_title_font_size . "px;
                text-align: center;";
                
            if($quiz_enable_title_text_shadow){
                $quiz_styles .= "
                    text-shadow: " . $title_text_shadow_offsets . " " . $quiz_title_text_shadow_color . ";";
            }else{
                $quiz_styles .= "
                    text-shadow: none;";
            }

            $quiz_styles .= "
            }

            #ays-quiz-container-" . $id . " .ays-quiz-password-message-box,
            #ays-quiz-container-" . $id . " .ays-quiz-question-note-message-box,
            #ays-quiz-container-" . $id . " .ays_quiz_question,
            #ays-quiz-container-" . $id . " .ays_quiz_question *:not([class^='enlighter']) {
                color: " . $text_color . ";
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

            #ays-quiz-container-" . $id . " .ays-fs-subtitle p {
                text-align:  ". $quiz_question_text_alignment ." ;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question p {
                font-size: ".$question_font_size."px;
                text-align: ". $quiz_question_text_alignment .";
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question {
                text-align:  ". $quiz_question_text_alignment ." ;
                margin-bottom: 10px;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question pre {
                max-width: 100%;
                white-space: break-spaces;
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

            #ays-quiz-container-" . $id . " section.ays_quiz_redirection_timer_container hr,
            #ays-quiz-container-" . $id . " section.ays_quiz_timer_container hr {
                margin: 0;
            }

            #ays-quiz-container-" . $id . " section.ays_quiz_timer_container.ays_quiz_timer_red_warning .ays-quiz-timer {
                color: red;
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

            #ays-quiz-container-" . $id . " .right_answer_text p {
                font-size:" . $right_answers_font_size . "px;
            }

            #ays-quiz-container-" . $id . " .wrong_answer_text p {
                font-size:" . $wrong_answers_font_size . "px;
            }

            #ays-quiz-container-" . $id . " .ays_questtion_explanation p {
                font-size:" . $quest_explanation_font_size . "px;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-question-note-message-box p {
                font-size:" . $note_text_font_size . "px;
            }

            #ays-quiz-container-" . $id . " .ays_cb_and_a,
            #ays-quiz-container-" . $id . " .ays_cb_and_a * {
                color: " . Quiz_Maker_Data::hex2rgba($text_color) . ";
                text-align: center;
            }

            #ays-quiz-container-" . $id . " iframe {
                /*min-height: " . $quiz_height . "px;*/
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
            #ays-quiz-container-" . $id . " .ays-questions-container > .ays_quizn_ancnoxneri_qanak {
                padding: 5px 20px;
            }
            #ays-quiz-container-" . $id . " div.for_quiz_rate.ui.star.rating .icon {
                color: " . Quiz_Maker_Data::hex2rgba($text_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg div.for_quiz_rate_avg.ui.star.rating .icon {
                color: " . Quiz_Maker_Data::hex2rgba($bg_color, '0.5') . ";
            }

            #ays-quiz-container-" . $id . " .ays_quiz_rete .ays-quiz-rate-link-box .ays-quiz-rate-link {
                color: " . $text_color . ";
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
                text-align: center;
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
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-fourth,
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-third,
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-second {
                text-shadow: unset;
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

            /* Quiz Password */
            #ays-quiz-container-" . $id . " .ays-start-page > input[id^='ays_quiz_password_val_'],
            #ays-quiz-container-" . $id . " .ays-quiz-password-toggle-visibility-box {
                width: ". $quiz_password_width_css .";
                margin: 0 auto;
            }

            /* Question hint */
            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text {
                background-color:" . $bg_color . ";
                box-shadow: 0 0 15px 3px " . Quiz_Maker_Data::hex2rgba($box_shadow_color, '0.6') . ";
                max-width: 270px;
                color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text p {
                max-width: unset;
            }

            #ays-quiz-container-" . $id . " .ays_questions_hint_max_width_class {
                max-width: 80%;
            }

            /* Information form */
            #ays-quiz-container-" . $id . " .ays-form-title{
                color:" . Quiz_Maker_Data::hex2rgba($text_color) . ";
            }

            /* Quiz timer */
            #ays-quiz-container-" . $id . " div.ays-quiz-redirection-timer,
            #ays-quiz-container-" . $id . " div.ays-quiz-timer{
                color: " . $text_color . ";
                text-align: center;
            }
            
            #ays-quiz-container-" . $id . " div.ays-quiz-timer.ays-quiz-message-before-timer:before {
                font-weight: 500;
            }

            /* Quiz buttons */
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button,
            div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_restart_button {
                background-color: " . $color . ";
                color:" . $buttons_text_color . ";
                font-size: " . $buttons_font_size . ";
                padding: " . $buttons_top_bottom_padding . " " . $buttons_left_right_padding . ";
                border-radius: " . $buttons_border_radius . ";
                white-space: nowrap;
                letter-spacing: 0;
                box-shadow: unset;
                white-space: normal;
                word-break: break-word;
            }
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " input.action-button {
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

            #ays-quiz-container-" . $id . " .ays_restart_button_p {
                display: flex;
                justify-content: " . $buttons_position . ";
                flex-wrap: wrap;
            }

            #ays-quiz-container-" . $id . " .ays_buttons_div {
                justify-content: " . $buttons_position . ";
            }
            #ays-quiz-container-" . $id . " .step:first-of-type .ays_buttons_div {
                justify-content: ". $buttons_position ." !important;
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
                margin-bottom: 5px;
                display: inline-block;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-question-title-text-to-speech-icon {
                cursor: pointer;
                position: absolute;
                right: 0px;
                top: 0px;
                z-index: 1;
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
                box-shadow: " . $answer_box_shadow_offsets . " 1px " . $answers_box_shadow_color . ";";
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

        if($enable_copy_protection){
            $quiz_styles .= "
            *:not(input):not(textarea)::selection {
                background-color: transparent !important;
                color: inherit !important;
            }

            *:not(input):not(textarea)::-moz-selection {
                background-color: transparent !important;
                color: inherit !important;
            }

            *:not(input):not(textarea):not(button) {
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
                user-select: none !important;
                -webkit-tap-highlight-color: rgba(0, 0, 0, 0) !important;
                -webkit-touch-callout: none !important;
            }";
        }
        
        $quiz_styles .= "
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field input~label[for^='ays-answer-'] {
                padding: " . $answers_padding . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field {
                margin-bottom: " . ($answers_margin) . "px;
                position: relative;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item {
                width: calc(50% - " . ($answers_margin / 2) . "px);
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item:nth-child(odd) {
                margin-right: " . ($answers_margin / 2) . "px;
            }

            #ays-quiz-container-" . $id . " img.ays-quiz-check-button-right-wrong-icon {
                position: absolute;
                right: 15px;
                bottom: 15px;
            }
            #ays-quiz-container-" . $id . " img.ays-quiz-check-button-right-wrong-icon[data-type='style-9'] {
                width: 20px;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_results .step[data-type='fill_in_blank'] img.ays-quiz-check-button-right-wrong-icon {
                right: 25px;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_results .step[data-type='fill_in_blank'] .ays_fieldset img.ays-quiz-check-button-right-wrong-icon {
                right: 30px;
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
        } elseif( $ans_right_wrong_icon == "none" ){
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input~label.answered.correct:after{
                content: '';          }
            #ays-quiz-container-" . $id . " .ays-field input~label.answered.wrong:after{
                content: '';
            }";

            $quiz_styles .= "
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field label.answered[for^='ays-answer-']::after{
                content: none!important;
            }";

        } else{
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
                padding: 0.75rem;
            }

            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field .select2-container--default .select2-selection--single {
                border-bottom: 2px solid " . $color . ";
                background-color: " . $color . ";
            }
            
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__placeholder,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow {
                /*color: " . Quiz_Maker_Data::ays_color_inverse( $color ) . ";*/
                color: " . $buttons_text_color . ";
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

            #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option {
                padding: 6px;
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
                background-color: " . Quiz_Maker_Data::hex2rgba($color, '0.8') . ";
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
            #ays-quiz-container-" . $id . " .mejs-container .mejs-mediaelement video {
                margin: 0;
            }

            /* Limitation */
            #ays-quiz-container-" . $id . " .ays-quiz-limitation-count-of-takers {
                padding: 50px;
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block span.ays-show-res-toggle.ays-res-toggle-show,
            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block span.ays-show-res-toggle.ays-res-toggle-hide{
                color: ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle {
                border: 1px solid ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle {
                border: 1px solid ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle:after{
                background: ". $text_color .";
            }

            #ays-quiz-container-" . $id . ".ays_quiz_elegant_dark div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle:after,
            #ays-quiz-container-" . $id . ".ays_quiz_rect_dark div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle:after{
                background: #000;
            }

            /* Hestia theme (Version: 3.0.16) | Start */
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button:hover,
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button {
                box-shadow: unset;
                background-color: transparent;
            }
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button {
                margin: 10px 6px;
            }
            /* Hestia theme (Version: 3.0.16) | End */

            /* Go theme (Version: 1.4.3) | Start */
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before,
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before {
                -webkit-mask-image: unset;
                mask-image: unset;
            }

            #ays-quiz-container-" . $id . " .ays_question_report {
                text-align: right;
            }

            #ays-quiz-container-" . $id . " .ays-export-quiz-button-container {
                position: absolute;
                right: 74px;
                top: -19px;
                margin: 1em 0;
            }


            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field input:checked+label.answered:before,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field input:checked+label.answered:before {
                background-color: ". $color ." !important;
            }
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field input:checked+label.answered.correct:before,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field input:checked+label.answered.correct:before {
                background-color: #27ae60 !important;
            }
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field input:checked+label.answered.wrong:before,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field input:checked+label.answered.wrong:before {
                background-color: #cc3700 !important;
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

            #ays-quiz-container-" . $id . " .ays_quiz_login_form p{
                color: $text_color;
            }

            /* Personality Test | Start */
            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-description p {
                text-align: left;
                padding: 0;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box {
                background: white;
                border-radius: 17px;
                box-shadow: 0px 0px 20px rgba(98, 85, 165, 0.1);
                /*padding: 30px 3% 40px;*/
                padding: 20px 30px;
                margin: 30px 0;
                font-size: 16px;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-title {
                color: #413A5C;
                font-size: 23px;
                margin: 0;
                text-align: left;
                font-weight: bold;
            }

            div#ays-quiz-container-" . $id . ".ays-quiz-container .ays-quiz-personality-result-box .ays-quiz-personality-result-description {
                margin: 0;
                font-size: 16px;
                text-align: left;
                color: #413A5C;
            }

            div#ays-quiz-container-" . $id . ".ays-quiz-container .ays-quiz-personality-result-box .ays-quiz-personality-result-description * {
                color: #413A5C;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-progress {
                width: 100%;
                background-color: rgba(128, 126, 137, 0.1);
                border-radius: 15px;
                position: relative;
                display: flex;
                /* justify-content: flex-end; */
                margin-top: 1rem;
                margin-bottom: 1rem;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-progress-end {
                justify-content: flex-end;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-bar {
                height: 30px;
                border-radius: 15px;
                color: white;
                font-size: 18px;
                padding: 3px 15px 0;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-percentages {
                position: absolute;
                width: 100%;
                padding: 4px 15px;
                top: 0;
                -ms-flex-pack: justify;
                justify-content: space-between;
                display: -ms-flexbox;
                display: flex;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-keyword-box div,
            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-text-dark-purple {
                color: #413A5C;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-text-white {
                color: #ffffff;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-text-percentage {
                font-weight: bolder;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box .ays-quiz-personality-result-keyword-box {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-pack: justify;
                justify-content: space-between;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-purple .ays-quiz-personality-result-bar {
                background-color: #6255A5;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-purple .ays-quiz-personality-result-keyword-text-color {
                color: #6255A5;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-yellow .ays-quiz-personality-result-bar {
                background-color: #F2C94C;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-yellow .ays-quiz-personality-result-keyword-text-color {
                color: #F2C94C;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-green .ays-quiz-personality-result-bar {
                background-color: #88D29D;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-green .ays-quiz-personality-result-keyword-text-color {
                color: #88D29D;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-red .ays-quiz-personality-result-bar {
                background-color: #E5A69D;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-red .ays-quiz-personality-result-keyword-text-color {
                color: #E5A69D;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-blue .ays-quiz-personality-result-bar {
                background-color: #03A9F4;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box.ays-quiz-personality-result-box-blue .ays-quiz-personality-result-keyword-text-color {
                color: #03A9F4;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box div:before,
            #ays-quiz-container-" . $id . " .ays-quiz-personality-result-box div:after {
                content: unset;
            }

            /* Personality Test | End */

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

                #ays-quiz-container-" . $id . " .ays_quiz_question p {
                    font-size: ".$question_mobile_font_size."px;
                }

                #ays-quiz-container-" . $id . " .select2-container,
                #ays-quiz-container-" . $id . " .ays-field * {
                    font-size: ".$answers_mobile_font_size."px !important;
                }

                div#ays-quiz-container-" . $id . " input#ays-submit,
                div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button,
                div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_restart_button {
                    font-size: ".$buttons_mobile_font_size."px;
                }

                div#ays-quiz-container-" . $id . " div.ays-questions-container div.ays-woo-block {
                    width: 100%;
                }

                /* Quiz title / mobile font size */
                div#ays-quiz-container-" . $id . " .ays-fs-title {
                    font-size: " . $quiz_title_mobile_font_size . "px;
                }

                /* Question explanation / mobile font size */
                #ays-quiz-container-" . $id . " .ays_questtion_explanation p {
                    font-size:" . $quest_explanation_mobile_font_size . "px;
                }

                /* Wrong answers / mobile font size */
                #ays-quiz-container-" . $id . " .wrong_answer_text p {
                    font-size:" . $wrong_answers_mobile_font_size . "px;
                }

                /* Right answers / mobile font size */
                #ays-quiz-container-" . $id . " .right_answer_text p {
                    font-size:" . $right_answers_mobile_font_size . "px;
                }

                /* Note text / mobile font size */
                #ays-quiz-container-" . $id . " .ays-quiz-question-note-message-box p {
                    font-size:" . $note_text_mobile_font_size . "px;
                }

                #ays-quiz-container-" . $id . " div.ays-quiz-personality-result-box .ays-quiz-personality-result-title {
                    font-size: 18px;
                }

                /* Personality Test */
                #ays-quiz-container-" . $id . " div.ays-quiz-personality-result-box .ays-quiz-personality-result-bar {
                    font-size: 14px;
                    padding: 6px 10px 0;
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

        // Question Image Zoom
        $options['quiz_enable_question_image_zoom'] = isset($options['quiz_enable_question_image_zoom']) ? $options['quiz_enable_question_image_zoom'] : 'off';
        $quiz_enable_question_image_zoom = (isset($options['quiz_enable_question_image_zoom']) && $options['quiz_enable_question_image_zoom'] == "on") ? true : false;

        // Display Messages before the buttons
        $options['quiz_display_messages_before_buttons'] = isset($options['quiz_display_messages_before_buttons']) ? esc_attr($options['quiz_display_messages_before_buttons']) : 'off';
        $quiz_display_messages_before_buttons = (isset($options['quiz_display_messages_before_buttons']) && $options['quiz_display_messages_before_buttons'] == 'on') ? true : false;

        // Enable questions ordering by category
        $options['enable_questions_ordering_by_cat'] = isset($options['enable_questions_ordering_by_cat']) ? $options['enable_questions_ordering_by_cat'] : 'off';
        $enable_questions_ordering_by_cat = (isset($options['enable_questions_ordering_by_cat']) && $options['enable_questions_ordering_by_cat'] == "on") ? true : false;

        // Enable questions numbering by category
        $options['quiz_questions_numbering_by_category'] = isset($options['quiz_questions_numbering_by_category']) ? sanitize_text_field($options['quiz_questions_numbering_by_category']) : 'off';
        $quiz_questions_numbering_by_category = (isset($options['quiz_questions_numbering_by_category']) && $options['quiz_questions_numbering_by_category'] == 'on') ? true : false;

        // Questions text to speech enable
        $options[ 'quiz_question_text_to_speech' ] = isset($options[ 'quiz_question_text_to_speech' ]) ? $options[ 'quiz_question_text_to_speech' ] : 'off';
        $quiz_question_text_to_speech = (isset($options[ 'quiz_question_text_to_speech' ]) && $options[ 'quiz_question_text_to_speech' ] == 'on') ? true : false;

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


        //Enable navigation bar
        $options['enable_navigation_bar'] = isset($options['enable_navigation_bar']) ? $options['enable_navigation_bar'] : 'off';
        $enable_navigation_bar = (isset($options['enable_navigation_bar']) && $options['enable_navigation_bar'] == 'on') ? true : false;

        //Enable navigation bar marked questions
        $options['enable_navigation_bar_marked_questions'] = isset($options['enable_navigation_bar_marked_questions']) ? $options['enable_navigation_bar_marked_questions'] : 'off';
        $enable_navigation_bar_marked_questions = (isset($options['enable_navigation_bar_marked_questions']) && $options['enable_navigation_bar_marked_questions'] == 'on') ? true : false;


        if( $display_all_questions === true ){
            $enable_navigation_bar = false;
        }

        if( $question_per_page === true ){
            $enable_navigation_bar = false;
        }

        $questions_nav = '';
        if( $enable_navigation_bar ){
            $questions_nav = "<div id='ays-quiz-questions-nav-wrap-" . $id . "' class='ays-quiz-questions-nav-wrap'>";
                $questions_nav .= "<div class='ays-quiz-questions-nav-go-left'>
                    <i class='ays_fa ays_fa_angle_left'></i>
                </div>";
                $questions_nav .= "<div class='ays-quiz-questions-nav-go-right'>
                    <i class='ays_fa ays_fa_angle_right'></i>
                </div>";
                $questions_nav .= "<div class='ays-quiz-questions-nav-content'>";
                $arr_questions_length = count($arr_questions);
                $index_flag = 0;
                foreach ($arr_questions as $key => $question_id) {
                    $last_index_class = "";
                    if(++$index_flag === $arr_questions_length) {
                        $last_index_class = "ays-quiz-questions-nav-item-last-question";
                    }
                    $questions_nav .= "<div class='ays-quiz-questions-nav-item ". $last_index_class ."'>";
                        $questions_nav .= "<a href='javascript:void(0);' data-id='".$question_id."' class='ays_questions_nav_question'>";
                            $questions_nav .= ($key+1);
                            if ($enable_navigation_bar_marked_questions) {
                                $questions_nav .= "<span class='ays-quiz-navbar-highlighted-notice'><img src='". AYS_QUIZ_PUBLIC_URL ."/images/bookmark-filled.svg'></span>";
                            }
                        $questions_nav .= "</a>";
                    $questions_nav .= "</div>";
                }

                $questions_nav .= "</div>";
                if ($enable_navigation_bar_marked_questions) {
                    $questions_nav .= "<div class='ays-quiz-questions-nav-bookmark-box'>";
                        $questions_nav .= "<img class='ays-navbar-bookmark' highlighted='false' title='Bookmark question' src='". AYS_QUIZ_PUBLIC_URL ."/images/bookmark-empty.svg'>";
                    $questions_nav .= "</div>";
                }
            $questions_nav .= "</div>";
        }


        $quiz_container_first_part = "<div class='ays-quiz-wrap'>
            " . $questions_reporting_modal . "
            <div class='ays-quiz-container ".$quiz_theme." ".$quiz_bg_img_class." ".$custom_class." ".$class_for_keyboard."'
                 data-quest-effect='".$quest_animation."' ".$quiz_gradient."
                 data-hide-bg-image='".$quiz_bg_img_in_finish_page."'
                " . (Quiz_Maker_iFrame::isAMP() ? 'data-is-amp="1"' : '') . "
                " . (!Quiz_Maker_iFrame::isAMP() && Quiz_Maker_iFrame::isEmbed() ? 'data-is-amp="0" data-is-embed="1"' : '') . "
                 id='ays-quiz-container-" . $id . "'>
                {$live_progress_bar}
                {$ays_quiz_music_html}
                <div class='ays-questions-container'>
                    <div class='ays-quiz-some-items-icons-wrap'>{$ays_quiz_music_sound}{$fullcsreen_mode}</div>
                    $ays_quiz_reports
                    {$questions_nav}
                    <form 
                        action='' 
                        method='post' 
                        autocomplete='off'
                        id='ays_finish_quiz_" . $id . "' 
                        class='ays-quiz-form " . $correction_class . " " . $enable_questions_result . " " . $enable_logged_users . "'
                    >";
        if($question_per_page && $question_count_per_page > 0 && $question_count_per_page_type == 'general'){
            if($question_count_per_page > $questions_count){
                $question_count_per_page = $questions_count;
            }

            $quiz_container_first_part .= "<input type='hidden' class='ays_question_count_per_page' value='$question_count_per_page'>";
            $quiz_container_first_part .= $question_count_per_page_custom_html;
        } elseif ( $question_per_page && $question_count_per_page_type == 'custom' ) { 
            $question_count_per_page_custom_html = "";
            if ( $question_count_per_page_type == 'custom' && $question_count_per_page_custom_order != "" ) {
                $question_count_per_page_custom_html = "
                <input type='hidden' class='ays_question_count_per_page_custom_next' value='$question_count_per_page_custom_order'>
                <input type='hidden' class='ays_question_count_per_page_custom_prev' value=''>";

                if( !is_null( $question_count_per_page_custom_order_first ) ){
                    $question_count_per_page = $question_count_per_page_custom_order_first;
                }

            }

            $quiz_container_first_part .= "<input type='hidden' class='ays_question_count_per_page' value='$question_count_per_page'>";
            $quiz_container_first_part .= $question_count_per_page_custom_html;

        } elseif ( isset($options['quiz_display_all_questions']) && $options['quiz_display_all_questions'] == "on" ) {
            $quiz_questions_count_arr = Quiz_Maker_Data::get_quiz_questions_count($id);

            $quiz_questions_count = 0;
            if ( !empty( $quiz_questions_count_arr ) ) {
                $quiz_questions_count = count($quiz_questions_count_arr);
            }
            $quiz_container_first_part .= "<input type='hidden' class='ays_question_count_per_page' value='". $quiz_questions_count ."'>";
        }
        
        $quiz_container_first_part .= "
            <input type='hidden' value='" . $answer_view_class . "' class='answer_view_class'>
            <input type='hidden' value='" . $enable_arrows . "' class='ays_qm_enable_arrows'>";
        
        if ( $limit_users_res_id || $store_all_not_finished_results ) {
            $quiz_container_first_part .= "<input type='hidden' value='' name='ays_quiz_result_row_id' class='ays_quiz_result_row_id'>";
        }

        if($this->chain_id !== null){
            $quiz_container_first_part .= "<input type='hidden' value='".$this->chain_id."' name='ays_chained_quiz_id'>";
        }

        if($this->chain_result_btn !== null){
            $quiz_container_first_part .= "<input type='hidden' value='".$this->chain_result_btn."' name='ays_chained_quiz_see_result'>";
        }

        $quiz_container_middle_part = "";
        if( $payments['paypal']['html'] !== null && $payments['stripe']['html'] !== null ){
            if (is_user_logged_in()) {
                if( $payments['paypal']['payment_type'] == 'prepay' ) {
                    $quiz_container_middle_part .= $payments['paypal']['html'];
                    $main_content_first_part = "";
                    $main_content_last_part = "";
                    $quiz_container_middle_part .= "<br>";
                }
                if( $payments['stripe']['payment_type'] == 'prepay' ) {
                    $quiz_container_middle_part .= $payments['stripe']['html'];
                    $main_content_first_part = "";
                    $main_content_last_part = "";
                    $quiz_container_middle_part .= "<br>";
                }
            } else {
                $ays_quiz_paypal_stripe_message_flag = false;
                if( $payments['paypal']['payment_type'] == 'prepay' ) {
                    switch ($payments['paypal']['payment_terms']) {
                        case "onetime":
                            $quiz_container_middle_part .= $payments['paypal']['html'];
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                            break;
                        case "lifetime":
                            $quiz_container_middle_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";

                            $ays_quiz_paypal_stripe_message_flag = true;
                            break;
                        case "subscribtion":
                            $quiz_container_middle_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                            break;
                    }
                }

                if ( $payments['paypal']['payment_type'] == 'prepay' || $payments['stripe']['payment_type'] == 'prepay' ) {
                    $quiz_container_middle_part .= "<br>";
                }

                if( $payments['stripe']['payment_type'] == 'prepay' ) {
                    switch ($payments['stripe']['payment_terms']) {
                        case "onetime":
                            $quiz_container_middle_part .= $payments['stripe']['html'];
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                            break;
                        case "lifetime":
                            $ays_quiz_stripe_message_val = ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            if ( is_null( $user_massage ) && $ays_quiz_paypal_stripe_message_flag ) {
                                $ays_quiz_stripe_message_val = "";
                            }

                            $quiz_container_middle_part .= $ays_quiz_stripe_message_val;
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                            break;
                        case "subscribtion":
                            $quiz_container_middle_part .= ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                            break;
                    }
                }
            }
        }else{
            if( $payments['paypal']['payment_type'] == 'prepay' ) {
                if ($payments['paypal']['payment_terms'] == 'onetime') {
                    if (isset($_SESSION['ays_quiz_paypal_purchase']) && isset($_SESSION['ays_quiz_paypal_purchase'][$id]) && $_SESSION['ays_quiz_paypal_purchase'][$id] === true) {
                        if (isset($_SESSION['ays_quiz_paypal_purchased_item']) && isset($_SESSION['ays_quiz_paypal_purchased_item'][$id])) {
                            if ($_SESSION['ays_quiz_paypal_purchased_item'][$id]['status'] == 'started') {
                                if ($payments['paypal']['html'] !== null) {
                                    if (is_user_logged_in()) {
                                        if ($payments['paypal']['html'] == '') {
                                            $quiz_container_middle_part = __("It seems PayPal Client ID is missing.", $this->plugin_name);
                                            $main_content_first_part = "";
                                            $main_content_last_part = "";
                                        } else {
                                            $quiz_container_middle_part = $payments['paypal']['html'];
                                            $main_content_first_part = "";
                                            $main_content_last_part = "";
                                        }
                                    } else {
                                        switch ($payments['paypal']['payment_terms']) {
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
                                            case "subscribtion":
                                                $quiz_container_middle_part = ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                                                $main_content_first_part = "";
                                                $main_content_last_part = "";
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($payments['paypal']['html'] !== null) {
                            if (is_user_logged_in()) {
                                if ($payments['paypal']['html'] == '') {
                                    $quiz_container_middle_part = __("It seems PayPal Client ID is missing.", $this->plugin_name);
                                    $main_content_first_part = "";
                                    $main_content_last_part = "";
                                } else {
                                    $quiz_container_middle_part = $payments['paypal']['html'];
                                    $main_content_first_part = "";
                                    $main_content_last_part = "";
                                }
                            } else {
                                $quiz_container_middle_part = $payments['paypal']['html'];
                                $main_content_first_part = "";
                                $main_content_last_part = "";
                            }
                        }
                    }
                } elseif (in_array($payments['paypal']['payment_terms'], array('lifetime', 'subscribtion'))) {
                    if ($payments['paypal']['html'] !== null) {
                        if (is_user_logged_in()) {
                            if ($payments['paypal']['html'] == '') {
                                $quiz_container_middle_part = __("It seems PayPal Client ID is missing.", $this->plugin_name);
                                $main_content_first_part = "";
                                $main_content_last_part = "";
                            } else {
                                $quiz_container_middle_part = $payments['paypal']['html'];
                                $main_content_first_part = "";
                                $main_content_last_part = "";
                            }
                        } else {
                            $quiz_container_middle_part = ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                            $main_content_first_part = "";
                            $main_content_last_part = "";
                        }
                    }
                }
            }

            if( $payments['stripe']['payment_type'] == 'prepay' ) {
                if ( $payments['stripe']['payment_terms'] == 'onetime' ) {
                    if ( isset( $_SESSION['ays_quiz_stripe_purchase'] ) && isset( $_SESSION['ays_quiz_stripe_purchase'][ $id ] ) && $_SESSION['ays_quiz_stripe_purchase'][ $id ] === true ) {
                        if (isset($_SESSION['ays_quiz_stripe_purchased_item']) && isset($_SESSION['ays_quiz_stripe_purchased_item'][$id])) {
                            if ($_SESSION['ays_quiz_stripe_purchased_item'][$id]['status'] == 'started') {
                                if ($payments['stripe']['html'] !== null) {
                                    if (is_user_logged_in()) {
                                        if ($payments['stripe']['html'] == '') {
                                            $quiz_container_middle_part = __("It seems Stripe Client ID is missing.", $this->plugin_name);
                                            $main_content_first_part = "";
                                            $main_content_last_part = "";
                                        } else {
                                            $quiz_container_middle_part = $payments['stripe']['html'];
                                            $main_content_first_part = "";
                                            $main_content_last_part = "";
                                        }
                                    } else {
                                        switch ($payments['stripe']['payment_terms']) {
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
                                            case "subscribtion":
                                                $quiz_container_middle_part = ($user_massage != null) ? $user_massage : __("You need to log in to pass this quiz.", $this->plugin_name);
                                                $main_content_first_part = "";
                                                $main_content_last_part = "";
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ( $payments['stripe']['html'] !== null ) {
                            if ( is_user_logged_in() ) {
                                if ( $payments['stripe']['html'] == '' ) {
                                    $quiz_container_middle_part = __( "It seems Stripe API key or Secret key is missing.", $this->plugin_name );
                                    $main_content_first_part    = "";
                                    $main_content_last_part     = "";
                                } else {
                                    $quiz_container_middle_part = $payments['stripe']['html'];
                                    $main_content_first_part    = "";
                                    $main_content_last_part     = "";
                                }
                            } else {
                                $quiz_container_middle_part = $payments['stripe']['html'];
                                $main_content_first_part    = "";
                                $main_content_last_part     = "";
                            }
                        }
                    }
                } elseif ( in_array( $payments['stripe']['payment_terms'], array( 'lifetime' ) ) ) {
                    if ( $payments['stripe']['html'] !== null ) {
                        if ( is_user_logged_in() ) {
                            if ( $payments['stripe']['html'] == '' ) {
                                $quiz_container_middle_part = __( "It seems Stripe API key or Secret key is missing.", $this->plugin_name );
                                $main_content_first_part    = "";
                                $main_content_last_part     = "";
                            } else {
                                $quiz_container_middle_part = $payments['stripe']['html'];
                                $main_content_first_part    = "";
                                $main_content_last_part     = "";
                            }
                        } else {
                            $quiz_container_middle_part = ( $user_massage != null ) ? $user_massage : __( "You need to log in to pass this quiz.", $this->plugin_name );
                            $main_content_first_part    = "";
                            $main_content_last_part     = "";
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
        
        if( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

            if( in_array('quiz-maker-woocommerce/quiz-maker-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

                if( has_action( 'ays_qm_woocommerce_front_end_integrations' ) ){
                    $woocommerce_integration_options = (array)$options;
                    $woocommerce_integration_options['id'] = $id;

                    // Enable Woocommerce
                    $options['enable_woocommerce'] = isset( $options['enable_woocommerce'] ) ? sanitize_text_field( $options['enable_woocommerce'] ) : 'off';
                    $enable_woocommerce = ( isset( $options['enable_woocommerce'] ) && $options['enable_woocommerce'] == 'on' ) ? true : false;

                    $woocommerce_product = ( isset($options['woocommerce_product']) && $options['woocommerce_product'] != '' ) ? esc_attr( sanitize_text_field( $options['woocommerce_product'] ) ) : '';

                    if ( $enable_woocommerce ) {
                        if ( $woocommerce_product > 0 && $woocommerce_product != "" ) {
                            $woocommerce_result = apply_filters( "ays_qm_woocommerce_front_end_integrations", $woocommerce_integration_options );

                            if ( ! empty( $woocommerce_result ) ) {

                                $woocommerce_result_message = "<div class='step active-step'>
                                    <div class='ays-abs-fs'>
                                        " . Quiz_Maker_Data::ays_autoembed( $woocommerce_result['woocommerce_html'] ) . "
                                    </div>
                                </div>";

                                $quiz_container_middle_part = $woocommerce_result_message;
                                $main_content_first_part = "";
                                $main_content_last_part = "";
                            }
                        }
                    }
                }
            }
        }

        $quiz_container_last_part = $quiz_content_script;
        $nonce = wp_create_nonce( 'ays_finish_quiz' );

        if( $this->get_prop( 'is_training' ) === true ){
            $quiz_container_last_part .= "<input type='hidden' name='is_training' value='true' />";
        }

        $cat_selective_start_page = "";
        $cat_selective_restart_bttn = "";
        if( $this->get_prop( 'category_selective' ) === true ){
            if (isset($_COOKIE['ays_quiz_selected_categories-'.$id])) {
                $selected_questions = apply_filters( 'ays_qm_front_end_category_selective_get_questions', array(), $id );
                $arr_questions = $selected_questions;
                $cat_selective_restart_bttn = apply_filters( 'ays_qm_front_end_category_selective_restart_button', "", $id );
            } else {
                $cat_selective_start_page = apply_filters( 'ays_qm_front_end_category_selective_start_page', "", $id );
            }
        }

        $quiz_container_last_part .= "
                    <input type='hidden' name='quiz_id' value='" . $id . "'/>
                    <input type='hidden' name='start_date' class='ays-start-date'/>
                    <input type='hidden' name='ays_end_date' class='ays-quiz-end-date'/>
                    <input type='hidden' name='ays_finish_quiz_nonce_".$id."' value='".$nonce."'/>
                </form>";
        if($user_massage !== null){
            $quiz_container_last_part .= $user_massage;
        }
        $quiz_container_last_part .= "</div>
                                </div>
                                {$cat_selective_restart_bttn}
                            </div>";
        
        
    /*******************************************************************************************************/
        
        /*
         * Generating Quiz parts array
         */
        
        $quiz_styles = str_replace(array("\r\n", "\n", "\r"), "", $quiz_styles);
        $quiz_styles = preg_replace('/\s+/', ' ', $quiz_styles);

        $quiz_parts = array(
            "container_first_part" => $quiz_container_first_part,
            "main_content_first_part" => $main_content_first_part,
            "main_content_middle_part" => $quiz_container_middle_part,
            "main_content_last_part" => $main_content_last_part,
            "quiz_styles" => $quiz_styles,
            "quiz_additional_styles" => "",
            "container_last_part" => $quiz_container_last_part,
            "cat_selective_start_page" => $cat_selective_start_page,
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
            'showQuestionCategoryDescription' => $quiz_enable_question_category_description,
            'questionsHint' => $questions_hint_arr,
            'isRequired' => $make_questions_required,
            'show_answers_numbering' => $show_answers_numbering,
            'show_questions_numbering' => $show_questions_numbering,
            'disable_hover_effect' => $disable_hover_effect,
            'quiz_waiting_time' => $quiz_waiting_time,
            'show_questions_explanation' => $show_questions_explanation,
            'answers_rw_texts' => $answers_rw_texts,
            'quiz_enable_keyboard_navigation' => $quiz_enable_keyboard_navigation,
            'quiz_enable_lazy_loading' => $quiz_enable_lazy_loading,
            'quiz_enable_question_image_zoom' => $quiz_enable_question_image_zoom,
            'quiz_display_messages_before_buttons' => $quiz_display_messages_before_buttons,
            'enable_next_button' => $next_button,
            'questionsReporting' => $questions_reporting,
            'enable_questions_ordering_by_cat' => $enable_questions_ordering_by_cat,
            'quiz_questions_numbering_by_category' => $quiz_questions_numbering_by_category,
            'question_bank_cats' => $new_question_bank_cats,
            'quiz_question_text_to_speech' => $quiz_question_text_to_speech,

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

        if (isset($quiz->quizParts['cat_selective_start_page']) && $quiz->quizParts['cat_selective_start_page'] != "") {
            return $quiz->quizParts['cat_selective_start_page'];
        }

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
                
                #ays-quiz-container-" . $quiz_id . ".ays_quiz_classic_light .enable_correction .ays-field.checked_answer_div input:checked+label,
                #ays-quiz-container-" . $quiz_id . ".ays_quiz_classic_dark .enable_correction .ays-field.checked_answer_div input:checked+label {
                    background-color: transparent;
                }";

        if ( isset($options['correction']) && $options['correction'] == true ) {
            $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " .ays-field.checked_answer_div input:checked~label:not(.ays_answer_image) {
                    background-color: " . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.6') . ";
                }";

        } else {
            $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " .ays-field.checked_answer_div input:checked~label {
                    background-color: " . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.6') . ";
                }";
        }


        if (! $disable_hover_effect) {
            $additional_css .= "
                #ays-quiz-container-" . $quiz_id . ".ays-quiz-container.ays_quiz_classic_light .ays-questions-container .ays-field:hover label[for^='ays-answer-'],
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

        if ( !is_null( $this->aysQuizUserExportDataArray ) ) {

            $additional_css .= "<script>";
            $additional_css .= "
                    if(typeof aysQuizUserExportDataArray === 'undefined'){
                        var aysQuizUserExportDataArray = [];
                    }
                    aysQuizUserExportDataArray['".$quiz_id."']  = '" . base64_encode(json_encode( $this->aysQuizUserExportDataArray )) . "';";
            $additional_css .= "</script>";
        }
        
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
                        'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    );
                    $answer_container .= $this->ays_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "short_text":
                    $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'questionMaxLengthArray' => $question_max_length_array,
                        'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    );
                    $answer_container .= $this->ays_short_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "fill_in_blank":
                    // $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['questionID']);
                    // $ans_options = array(
                    //     'correction' => $options['correction'],
                    //     'answersViewClass' => $options['answersViewClass'],
                    //     'questionMaxLengthArray' => $question_max_length_array,
                    //     'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    // );
                    // $answer_container .= $this->ays_fill_in_blank_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
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
                case "matching":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'answersViewClass' => $options['answersViewClass'],
                        'show_answers_numbering' => $options['show_answers_numbering'],
                        'show_questions_numbering' => $options['show_questions_numbering'],
                        'answer_incorrect_matches' => $question['answer_incorrect_matches'],
                        'total_questions' => count( $container ),
                    );
                    $answer_container .= $this->ays_matching_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "custom":
                    break;
                case "true_or_false":
                default:
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'rtlDirection' => $options['rtlDirection'],
                        'questionType' => $question["questionType"],
                        'answersViewClass' => $options['answersViewClass'],
                        'useHTML' => $use_html,
                        'show_answers_numbering' => $options['show_answers_numbering'],
                        'enable_max_selection_number' => $question['enable_max_selection_number'],
                        'max_selection_number' => $question['max_selection_number'],
                        'enable_min_selection_number' => $question['enable_min_selection_number'],
                        'min_selection_number' => $question['min_selection_number'],
                        'quiz_enable_keyboard_navigation' => $options['quiz_enable_keyboard_navigation'],
                        'quiz_enable_lazy_loading' => $options['quiz_enable_lazy_loading'],
                        'key_number' => $key,

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
        $quiz_waiting_time = $options['quiz_waiting_time'];
        $quiz_enable_lazy_loading = $options['quiz_enable_lazy_loading'];
        $quiz_enable_question_image_zoom = $options['quiz_enable_question_image_zoom'];
        $quiz_display_messages_before_buttons = $options['quiz_display_messages_before_buttons'];
        $enable_next_button = $options['enable_next_button'];
        $enable_questions_ordering_by_cat = $options['enable_questions_ordering_by_cat'];
        $quiz_questions_numbering_by_category = $options['quiz_questions_numbering_by_category'];
        $question_bank_cats = $options['question_bank_cats'];
        $quiz_question_text_to_speech = $options['quiz_question_text_to_speech'];
        
        $questions_show_count = array();
        $container_for_exporting_data = array();

        foreach($ids as $key => $id){
            if(Quiz_Maker_Data::is_question_type_a_custom($id)){
                $total_show--;
            }else{
                $questions_show_count[] = $id;
            }
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
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
                $question_category_description = '';
                $question_category_description_html = '';
                $show_question_category = $options['showQuestionCategory'];
                $show_question_category_description = $options['showQuestionCategoryDescription'];
                $show_questions_explanation = $options['show_questions_explanation'];
                $show_answers_rw_texts = $options['answers_rw_texts'];
                $question_matches = array();
                if($show_question_category){
                    $question_category_data = Quiz_Maker_Data::get_question_category_by_id($question['category_id']);
                    $question_category =  ( isset( $question_category_data['title'] ) && $question_category_data['title'] != "" ) ? $question_category_data['title'] : "";
                    $question_category_description = ( isset( $question_category_data['description'] ) && $question_category_data['description'] != "" ) ? $question_category_data['description'] : "";

                    $question_category = "<p style='margin:0!important;text-align:left;'>
                        <em style='font-style:italic;font-size:0.8em;'>". __("Category", $this->plugin_name) .":</em>
                        <strong style='font-size:0.8em;'>{$question_category}</strong>
                    </p>";

                    if ( $show_question_category_description && $question_category_description != "" ) {
                        $question_category_description_html .= '<div class="ays-quiz-category-description-box">';
                            $question_category_description_html .= Quiz_Maker_Data::ays_autoembed($question_category_description);
                        $question_category_description_html .= '</div>';

                        $question_category .= $question_category_description_html;
                    }
                }

                if ( is_null( $question['options'] ) || empty( $question['options'] ) ) {
                    $question_options = array();
                } else {
                    $question_options = json_decode($question['options'], true) !== null ? json_decode($question['options'], true) : array();
                }
                if( !is_array( $question_options ) ){
                    $question_options = array();
                }
                
                if ( $question["type"] == 'true_or_false' ) {
                    $question["type"] = 'radio';
                }

                $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on') ? true : false;
                
                // Hide question text on the front-end
                $question_options['quiz_hide_question_text'] = isset($question_options['quiz_hide_question_text']) ? sanitize_text_field( $question_options['quiz_hide_question_text'] ) : 'off';
                $quiz_hide_question_text = (isset($question_options['quiz_hide_question_text']) && $question_options['quiz_hide_question_text'] == 'on') ? true : false;

                $question_image_style = "style='width:{$options['questionImageWidth']};height:{$options['questionImageHeight']};object-fit:{$options['questionImageSizing']};object-position:center center;'";
                
                // Enable maximum selection number
                $question_options['enable_max_selection_number'] = isset($question_options['enable_max_selection_number']) ? sanitize_text_field( $question_options['enable_max_selection_number'] ) : 'off';
                $enable_max_selection_number = (isset($question_options['enable_max_selection_number']) && sanitize_text_field( $question_options['enable_max_selection_number'] ) == 'on') ? true : false;

                // Max value
                $max_selection_number = ( isset($question_options['max_selection_number']) && $question_options['max_selection_number'] != '' ) ? intval( sanitize_text_field( $question_options['max_selection_number'] ) ) : '';

                // Enable minimum selection number
                $question_options['enable_min_selection_number'] = isset($question_options['enable_min_selection_number']) ? sanitize_text_field( $question_options['enable_min_selection_number'] ) : 'off';
                $enable_min_selection_number = (isset($question_options['enable_min_selection_number']) && sanitize_text_field( $question_options['enable_min_selection_number'] ) == 'on') ? true : false;

                // Min value
                $min_selection_number = ( isset($question_options['min_selection_number']) && $question_options['min_selection_number'] != '' ) ? intval( sanitize_text_field( $question_options['min_selection_number'] ) ) : '';

                // Incorrect matches for answers
                $question_options['answer_incorrect_matches'] = isset($question_options['answer_incorrect_matches']) ? $question_options['answer_incorrect_matches'] : array();
                $answer_incorrect_matches = isset($question_options['answer_incorrect_matches']) && !empty( $question_options['answer_incorrect_matches'] ) ? $question_options['answer_incorrect_matches'] : array();

                $max_selection_number_class = '';
                $min_selection_number_class = '';
                if ( $question["type"] == 'checkbox' ) {

                    if ( $enable_max_selection_number && ! empty( $max_selection_number ) && $max_selection_number != 0 ) {
                        $max_selection_number_class = 'enable_max_selection_number';
                    }
                    if ( $enable_min_selection_number && ! empty( $min_selection_number ) && $min_selection_number != 0 ) {
                        $min_selection_number_class = 'enable_min_selection_number';
                    }
                }

                $enable_case_sensitive_text = false;
                if ( $question["type"] == 'text' || $question["type"] == 'short_text' ) {

                    // Enable case sensitive text
                    $question_options['enable_case_sensitive_text'] = isset($question_options['enable_case_sensitive_text']) ? sanitize_text_field( $question_options['enable_case_sensitive_text'] ) : 'off';
                    $enable_case_sensitive_text = (isset($question_options['enable_case_sensitive_text']) && sanitize_text_field( $question_options['enable_case_sensitive_text'] ) == 'on') ? true : false;
                }

                // Enable strip slashes for questions
                $question_options['quiz_enable_question_stripslashes'] = isset($question_options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $question_options['quiz_enable_question_stripslashes'] ) : 'off';
                $quiz_enable_question_stripslashes = (isset($question_options['quiz_enable_question_stripslashes']) && $question_options['quiz_enable_question_stripslashes'] == 'on') ? true : false;


                if ($question['question_image'] != NULL && $question['question_image'] != "") {
                    $question_image_alt_text = Quiz_Maker_Data::ays_quiz_get_image_id_by_url($question['question_image']);

                    $question_image_lazy_loading_attr = "";
                    if ( $quiz_enable_lazy_loading ) {
                        if( $key != 0 ){
                            $question_image_lazy_loading_attr = 'loading="lazy"';
                        }
                    }

                    $quiz_question_image_zoom_class = "";
                    $quiz_question_full_size_url_attr = "";
                    if ( $quiz_enable_question_image_zoom ) {
                        $quiz_question_image_zoom_class = "ays-quiz-question-image-zoom";
                        $quiz_question_full_size_url = Quiz_Maker_Data::ays_quiz_get_image_full_size_url_by_url($question['question_image']);

                        if ( $quiz_question_full_size_url && $quiz_question_full_size_url != "" ) {
                            $quiz_question_full_size_url_attr = ' data-ays-src="'. esc_url( $quiz_question_full_size_url ) .'" ';
                        } elseif ( $quiz_question_full_size_url == "" ) {
                            $quiz_question_full_size_url_attr = ' data-ays-src="'. esc_url( $question['question_image'] ) .'" ';
                        }
                    }


                    $question_image .= '<div class="ays-image-question-img">';
                        $question_image .= '<img src="' . $question['question_image'] . '" '. $quiz_question_full_size_url_attr .' '. $question_image_lazy_loading_attr .' alt="'. $question_image_alt_text .'" ' . $question_image_style . ' class="'. $quiz_question_image_zoom_class .' '. $class_for_keyboard .'" '. $attributes_for_keyboard .'>';
                    $question_image .= '</div>';
                }
                $answer_view_class = "";
                $question_hint = '';
                $user_explanation = "";
                if ($options['randomizeAnswers']) {
                    shuffle($answers);
                }
                if (isset($question['question_hint']) && strlen($question['question_hint']) !== 0) {
                    $question_hint_arr = $options['questionsHint'];
                    $questions_hint_type = $options['questionsHint']['questionsHintType'];
                    $question_text_value = $options['questionsHint']['questionsHintValue'];
                    $questions_hint_button_value = $options['questionsHint']['questionsHintButtonValue'];



                    $questions_hint_content = "<i class='ays_fa ays_fa_info_circle ays_question_hint ". $class_for_keyboard ."' ". $attributes_for_keyboard ." aria-hidden='true'></i>";
                    $questions_hint_max_width_class = '';
                    switch ( $questions_hint_type ) {
                        case 'text':
                            if ($question_text_value != '') {
                                $questions_hint_content = '<p class="ays_question_hint">'. $question_text_value .'</p>';
                            }
                            break;
                        case 'button':
                            if ($questions_hint_button_value != '') {
                                $questions_hint_max_width_class = 'ays_questions_hint_max_width_class';

                                $questions_hint_content = '<button class="ays_question_hint action-button ays_question_hint_button_type '.$class_for_keyboard.'">'. $questions_hint_button_value .'</button>';
                            }
                            break;
                        case 'hide':
                            $questions_hint_content = '';
                            break;
                        case 'default':
                        default:
                            $questions_hint_content = "<i class='ays_fa ays_fa_info_circle ays_question_hint ". $class_for_keyboard ."' ". $attributes_for_keyboard ." aria-hidden='true'></i>";
                            break;
                    }

                    $question_hint = Quiz_Maker_Data::ays_autoembed( $question['question_hint'] );
                    $question_hint = "
                    <div class='ays_question_hint_container ". $questions_hint_max_width_class ."'>
                        ".$questions_hint_content."
                        <span class='ays_question_hint_text'>" . $question_hint . "</span>
                    </div>";

                    if ( $questions_hint_type == "hide" ) {
                        $question_hint = "";
                    }
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
                
                // Note text
                $quiz_question_note_message = ( isset( $question_options['quiz_question_note_message']) && $question_options['quiz_question_note_message'] != '' ) ? stripslashes( $question_options['quiz_question_note_message'] ) : '';

                $quiz_question_note_message_html = '';
                if ( $quiz_question_note_message != '' ) {
                    $quiz_question_note_message_html .= '<div class="ays-quiz-question-note-message-box">';
                        $quiz_question_note_message_html .= Quiz_Maker_Data::ays_autoembed($quiz_question_note_message);
                    $quiz_question_note_message_html .= '</div>';
                }

                $quiz_waiting_time_html = '';
                // Waiting time
                if ( $quiz_waiting_time ) {
                    $quiz_waiting_time_html .= '<div class="ays-quiz-question-waiting-time-box">';
                        // $quiz_waiting_time_html .= Quiz_Maker_Data::ays_autoembed($quiz_question_note_message);
                    $quiz_waiting_time_html .= '</div>';
                }

                if($options['questionsCounter']){
                    $questions_counter = "<p class='ays-question-counter animated'>{$current_show} / {$total_show}</p>";
                }else{
                    $questions_counter = "";
                }

                if ($options['questionsReporting']) {
                    $questions_reporting = ' <div class="ays_question_report">
                                                <img src="' . AYS_QUIZ_PUBLIC_URL . '/images/report_questions.svg" title="Report question" class="ays-quiz-open-report-window">
                                            </div>';                
                } else {
                    $questions_reporting = '';
                }

                if($buttons['exportQuizButton']) {
                    $export_quiz_button = "
                        <div class='ays-export-quiz-button-container'>
                            <a download='' id='downloadFileU' hidden href=''></a>
                            <input type='button' class='ays-export-quiz-button action-button' value='". __("PDF", $this->plugin_name) ."'/>
                            <input type='checkbox' class='ays-export-quiz-answers' title='". __("Export with answers", $this->plugin_name) ."'/>
                        </div>
                    ";
                }
                else {
                    $export_quiz_button = "";
                }
                
                $early_finish = "";                
                if($buttons['earlyButton']){
                    $early_finish = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_flag_checkered ays_early_finish action-button ays_arrow ".$class_for_keyboard."'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_early_finish action-button ".$class_for_keyboard."' value='" . $settings_buttons_texts['finishButton'] . "'/>";
                }
                
                $clear_answer = "";                
                if($buttons['clearAnswerButton']){
                    $clear_answer = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_eraser ays_clear_answer action-button ays_arrow ".$class_for_keyboard."'></i><input type='button' name='next' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_clear_answer action-button' value='" . $settings_buttons_texts['clearButton'] . "'/>";
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
                    if ( $is_required && !$enable_next_button ) {
                        $buttons['nextButton'] = "";
                    }

                    switch($options['informationForm']){
                        case "disable":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow ".$class_for_keyboard."'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button ".$class_for_keyboard."' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "before":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow ".$class_for_keyboard."'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button ".$class_for_keyboard."' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "after":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ". $quiz_arrow_type_class_right ." ays_finish action-button ays_arrow ays_next_arrow ".$class_for_keyboard."'></i><input type='button' name='next' class=' " . $buttons['nextButton'] . " ays_next action-button ".$class_for_keyboard."' value='" . $settings_buttons_texts['finishButton'] . "' />";
                            break;
                        default:
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow ".$class_for_keyboard."'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button ".$class_for_keyboard."' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;                        
                    }
                    $buttons_div = "<div class='ays_buttons_div'>
                            {$clear_answer}
                            <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ".$class_for_keyboard." ays_arrow " . $buttons['prevArrow'] . "\"></i>
                            <input type='button' name='next' class='ays_previous action-button ".$class_for_keyboard." " . $buttons['prevButton'] . "'  value='". $settings_buttons_texts['previousButton'] ."' />
                            {$input}
                        </div>";
                }else{
                    $buttons_div = "<div class='ays_buttons_div'>
                        {$clear_answer}
                        <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ays_arrow ".$class_for_keyboard." " . $buttons['prevArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_previous action-button ".$class_for_keyboard." " . $buttons['prevButton'] . "' value='". $settings_buttons_texts['previousButton'] ."' />
                        " . $early_finish . "
                        <i class=\"ays_fa ". $quiz_arrow_type_class_right ." ays_next action-button ays_arrow ays_next_arrow ".$class_for_keyboard." " . $buttons['nextArrow'] . "\"></i>
                        <input type='button' name='next' class='ays_next action-button ".$class_for_keyboard." " . $buttons['nextButton'] . "' value='" . $settings_buttons_texts['nextButton'] . "' />
                    </div>";
                }
                
                $additional_css = "";
                $answer_view_class = $options['answersViewClass'];
                
                $question_bg_image = (isset($question_options['bg_image']) && $question_options['bg_image'] != "") ? $question_options['bg_image'] : null;
                $question_bg_class = ($question_bg_image !== null) ? "ays-quiz-question-with-bg" : "";
                
                $show_questions_numbering = $options['show_questions_numbering'];
                $question_numering_type = Quiz_Maker_Data::ays_question_numbering($show_questions_numbering,$total);

                $question_title =  htmlspecialchars_decode( $question['question'] );

                if ( $quiz_enable_question_stripslashes ) {
                    $question_title = stripslashes( $question_title );
                }

                $question_title = Quiz_Maker_Data::ays_autoembed( $question_title );
                
                $question_numering_value = "";
                if( isset( $question_numering_type[$key] ) && $question_numering_type[$key] != '' ){
                    $question_numering_value = $question_numering_type[$key] . " ";
                    if( $enable_questions_ordering_by_cat && $quiz_questions_numbering_by_category ){
                        if( !empty( $question_bank_cats ) ){
                            $question_bank_cat_key = null;
                            foreach ($question_bank_cats as $question_bank_cat_id => $question_bank_cat_arr) {
                                $question_bank_cat_key_index = array_search($id, $question_bank_cat_arr);
                                if( is_numeric( $question_bank_cat_key_index ) && !is_bool( $question_bank_cat_key_index ) ){
                                    $question_bank_cat_key = $question_bank_cat_key_index;
                                    break;
                                }
                            }
                            if( !is_null( $question_bank_cat_key ) ){
                                $question_numering_value = $question_numering_type[$question_bank_cat_key] . " ";
                            }
                        }
                    }


                    if (substr( $question_title , 0, 1) === '<') {
                        // preg_match('/<([a-z]+[1-9]*)\b[^>]*>(.*?)<\/\1>/', $question_title, $matches );
                        preg_match('/<([^>]+)>(?:([^<]+))*(?=[^>]*\<)/', $question_title, $matches );
                        if(empty($matches)){
                            $question_title = $question_numering_value . $question_title;
                        } elseif( !isset( $matches[2] ) || !isset( $matches[0] ) ){ 
                            preg_match('/<([a-z]+[1-9]*)\b[^>]*>(.*?)<\/\1>/', $question_title, $matches2 );
                            if(empty($matches2) || !isset( $matches2[2] ) || !isset( $matches2[0] ) ){
                                $question_title = $question_numering_value . $question_title;
                            } else {
                                $question_title_numbering_1 = $question_numering_value . $matches2[2];
                                $question_title_numbering_2 = str_replace( $matches2[2], $question_title_numbering_1, $matches2[0] );
                                $question_title = str_replace( $matches2[0], $question_title_numbering_2, $question_title );
                            }
                        }else{
                            $question_title_numbering_1 = $question_numering_value . $matches[2];
                            $question_title_numbering_2 = str_replace( $matches[2], $question_title_numbering_1, $matches[0] );
                            $question_title = str_replace( $matches[0], $question_title_numbering_2, $question_title );
                        }
                    } else {
                        $question_title = $question_numering_value . $question_title;
                    }
                }

                $question_content = $question_title;
                $question_content_for_text_to_speech = $question_content;

                if( $question["type"] == 'fill_in_blank' ){
                    $question_max_length_array = Quiz_Maker_Data::ays_quiz_get_question_max_length_array($question['id']);
                    $ans_options = array(
                        // 'correction' => $options['correction'],
                        // 'answersViewClass' => $options['answersViewClass'],
                        // 'questionMaxLengthArray' => $question_max_length_array,
                        // 'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    );
                    $new_question_content = $this->ays_fill_in_blank_answer_html($question_content, $question['id'], $quiz_id, $answers, $ans_options);

                    if( !empty( $new_question_content ) ){
                        $question_content = $new_question_content;
                    }
                } elseif ( $question["type"] == 'matching' ){
                    
                    $answer_incorrect_matches = isset( $question_options['answer_incorrect_matches'] ) && !empty( $question_options['answer_incorrect_matches'] ) ? $question_options['answer_incorrect_matches'] : array();
                    foreach ($answers as $answer) {
                        $answer_options = isset( $answer['options'] ) && ! empty( $answer['options'] ) ? $answer['options'] : '';
                        $answer_options = json_decode( $answer_options, true );
                        if ( ! $answer_options ) {
                            $answer_options = array();
                        }

                        $correct_match = isset( $answer_options['correct_match'] ) && !empty( $answer_options['correct_match'] ) ? $answer_options['correct_match'] : '';
                        if( $correct_match !== '' ) {
                            $question_matches[] = array(
                                'id' => $answer['id'],
                                'answer' => $correct_match
                            );
                        }
                    }

                    foreach ( $answer_incorrect_matches as $key => $match ) {
                        if( $match !== '' ) {
                            $question_matches[] = array(
                                'id' => $key,
                                'answer' => $match
                            );
                        }
                    }

                    shuffle($question_matches);
                }

                if ( $quiz_hide_question_text ) {
                    $question_content = '';
                    $question_content_for_text_to_speech = '';
                }

                $quiz_question_text_to_speech_html = "";
                if( $quiz_question_text_to_speech && !empty( $question_content_for_text_to_speech ) ){
                    $quiz_question_text_to_speech_html = '<div class="ays-quiz-question-title-text-to-speech-icon" data-question="'.base64_encode(strip_tags($question_content_for_text_to_speech)).'"><img src="'. AYS_QUIZ_PUBLIC_URL.'/images/audio-volume-high-svgrepo-com.svg"></div>';
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
                                {$quiz_question_text_to_speech_html}
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
                        {$export_quiz_button}
                        {$quiz_waiting_time_html}
                        {$questions_counter}
                        <div class='ays-abs-fs'>
                            {$question_category}
                            {$quiz_question_text_to_speech_html}
                            {$question_html}
                            <div class='ays-quiz-answers $answer_view_class $max_selection_number_class $min_selection_number_class'>";

                    $required_question_message = '';
                    if( $options['isRequired'] || $enable_min_selection_number ){
                        $required_question_message = '<div class="ays-quiz-question-validation-error" role="alert"></div>';
                    }

                    $explanation = "";
                    if ( $show_questions_explanation != "" && $show_questions_explanation != "disable") {
                        $explanation = Quiz_Maker_Data::ays_autoembed($question["explanation"]);
                    }

                    $wrong_answer_text = "";
                    $right_answer_text = "";
                    if ( $show_answers_rw_texts != "" && $show_answers_rw_texts != "disable") {
                        $wrong_answer_text = Quiz_Maker_Data::ays_autoembed($question["wrong_answer_text"]);
                        $right_answer_text = Quiz_Maker_Data::ays_autoembed($question["right_answer_text"]);
                    }

                    $new_buttons_div_html = "";
                    if( $quiz_display_messages_before_buttons ) {
                        $new_buttons_div_html = $buttons_div;
                        $buttons_div = "";
                    }

                    $container_last_part = "</div>
                            {$quiz_question_note_message_html}
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
                            {$questions_reporting}
                            {$new_buttons_div_html}
                            {$additional_css}
                        </div>
                    </div>";
                }

               // $container_first_part = "<div class='step ".$question_bg_class." ".$not_influence_to_score_class."' data-question-id='" . $question["id"] . "'>
               //     {$question_hint}
               //     {$questions_counter}
               //     <div class='ays-abs-fs'>
               //         {$question_category}
               //         {$question_html}
               //         <div class='ays-quiz-answers $answer_view_class'>";

               // $wrong_answer_text = Quiz_Maker_Data::ays_autoembed( $question['wrong_answer_text'] );
               // $right_answer_text = Quiz_Maker_Data::ays_autoembed( $question['right_answer_text'] );
               // $explanation = Quiz_Maker_Data::ays_autoembed( $question['explanation'] );
               // $container_last_part = "</div>
               //         {$user_explanation}
               //         {$buttons_div}
               //         <div class='wrong_answer_text $wrong_answer_class' style='display:none'>
               //             " . $wrong_answer_text . "
               //         </div>
               //         <div class='right_answer_text $right_answer_class' style='display:none'>
               //             " . $right_answer_text . "
               //         </div>
               //         <div class='ays_questtion_explanation' style='display:none'>
               //             " . $explanation . "
               //         </div>
               //         {$additional_css}
               //     </div>
               // </div>";
                
                $container[] = array(
                    'quizID' => $quiz_id,
                    'questionID' => $question['id'],
                    'questionAnswers' => $answers,
                    'questionType' => $question["type"],
                    'answer_incorrect_matches' => $answer_incorrect_matches,
                    'enable_max_selection_number' => $enable_max_selection_number,
                    'max_selection_number' => $max_selection_number,
                    'enable_min_selection_number' => $enable_min_selection_number,
                    'min_selection_number' => $min_selection_number,
                    'enable_case_sensitive_text' => $enable_case_sensitive_text,
                    'questionParts' => array(
                        'question_first_part' => $container_first_part,
                        'question_middle_part' => "",
                        'question_last_part' => $container_last_part
                    )
                );

                if( $buttons['exportQuizButton'] ){
                    $container_for_exporting_data[] = array(
                        'quizID' => $quiz_id,
                        'questionID' => $question['id'],
                        'questions'    => $question['question'],
                        'questionAnswers' => $answers,
                        'questionType' => $question["type"],
                        'questionMatches' => $question_matches,
                    );
                }
            }
        }

        if( $buttons['exportQuizButton'] ){
            $this->aysQuizUserExportDataArray = $container_for_exporting_data;
        }

        return $container;
    }

    public function ays_finish_quiz(){
        Quiz_Maker_iFrame::headers_for_ajax();

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

            $paypal_type = (isset($_REQUEST["ays-paypal-type"])) ? sanitize_text_field( $_REQUEST['ays-paypal-type'] ) : null;
            $paypal_paid = (isset($_REQUEST["ays-paypal-paid"])) ? sanitize_text_field( $_REQUEST['ays-paypal-paid'] ) : null;

            $paypal_result_send = true;
            $paypal_mail_send = true;
            if($paypal_type !== null){
                if($paypal_paid !== null){
                    $paypal_result_send = false;
                    $paypal_mail_send = true;
                }else{
                    $paypal_result_send = true;
                    $paypal_mail_send = false;
                }
            }

            $limited_result_id = (isset($_REQUEST['ays_quiz_result_row_id']) && $_REQUEST['ays_quiz_result_row_id'] != '') ? absint(intval( $_REQUEST['ays_quiz_result_row_id'] )) : null;
            $questions_answers = (isset($_REQUEST["ays_questions"])) ? $_REQUEST['ays_questions'] : array();
            $questions_answers_ids_arr = (isset($_REQUEST["ays_question_answers"])) ? $_REQUEST['ays_question_answers'] : array();
            $questions_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            $questions_answers = Quiz_Maker_Data::sort_array_keys_by_array($questions_answers, $questions_ids);
            $is_training = isset( $_REQUEST["is_training"] ) && sanitize_text_field( $_REQUEST['is_training'] ) === 'true' ? true : false;

            if( $is_training === true ){
                $paypal_mail_send = false;
                $paypal_result_send = false;
            }

            if (isset($_COOKIE['ays_quiz_selected_categories-'.$quiz_id])) {
                setcookie('ays_quiz_selected_categories-'.$quiz_id, null, time() - 86401, '/');
                unset($_COOKIE['ays_quiz_selected_categories-'.$quiz_id]);
            }

            $chained_quiz_id = (isset($_REQUEST['ays_chained_quiz_id']) && $_REQUEST['ays_chained_quiz_id'] != '') ? absint(intval( $_REQUEST['ays_chained_quiz_id'] )) : null;

            $chained_quiz_see_result = (isset($_REQUEST['ays_chained_quiz_see_result']) && $_REQUEST['ays_chained_quiz_see_result'] == 'on') ? true : false;

            $quiz = Quiz_Maker_Data::get_quiz_by_id($quiz_id);
            $quiz_intervals_data = (isset( $quiz['intervals'] ) && $quiz['intervals'] != "") ? $quiz['intervals'] : "";
            $quiz_intervals = array();
            if ( $quiz_intervals_data != "" ) {
                $quiz_intervals = json_decode($quiz_intervals_data);
            }
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

            // Quiz create date 
            $quiz_creation_date = (isset( $quiz['create_date'] ) && $quiz['create_date'] != '') ? sanitize_text_field( $quiz['create_date'] ) : "";

            // Quiz Author ID
            $quiz_current_author = (isset( $quiz['author_id'] ) && $quiz['author_id'] != '') ? absint( sanitize_text_field( $quiz['author_id'] ) ) : "";

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

            // Apply points to keywords
            $options->apply_points_to_keywords = isset($options->apply_points_to_keywords) ? $options->apply_points_to_keywords : 'off';
            $apply_points_to_keywords = (isset($options->apply_points_to_keywords) && $options->apply_points_to_keywords == 'on') ? true : false;

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

            // Pass score type
            $quiz_pass_score_type = (isset($options->quiz_pass_score_type) && $options->quiz_pass_score_type != '') ? sanitize_text_field($options->quiz_pass_score_type) : "percentage";

            // Certifacte Quiz Pass Score type
            $quiz_certificate_pass_score_type = (isset($options->quiz_certificate_pass_score_type) && $options->quiz_certificate_pass_score_type != '') ? sanitize_text_field($options->quiz_certificate_pass_score_type) : "percentage";

            // Display Interval by
            $display_score_by = (isset($options->display_score_by) && $options->display_score_by != '') ? $options->display_score_by : 'by_percentage';

            // Show information form to logged in users
            $options->show_information_form = isset($options->show_information_form) ? $options->show_information_form : 'on';
            $show_information_form = (isset($options->show_information_form) && $options->show_information_form == 'on') ? true : false;

            // Enable Top Keywords
            $options->enable_top_keywords = isset($options->enable_top_keywords) ? $options->enable_top_keywords : 'off';
            $enable_top_keywords = (isset($options->enable_top_keywords) && $options->enable_top_keywords == 'on') ? true : false;

            // Equal keywords text
            $quiz_equal_keywords_text = (isset($options->quiz_equal_keywords_text) && $options->quiz_equal_keywords_text != '') ? Quiz_Maker_Data::ays_autoembed( stripslashes($options->quiz_equal_keywords_text) ) : "";

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

            //Enable Bulk Coupon
            $options->quiz_enable_coupon = isset($options->quiz_enable_coupon) ? sanitize_text_field($options->quiz_enable_coupon) : 'off';
            $quiz_enable_coupon = (isset($options->quiz_enable_coupon) && $options->quiz_enable_coupon == 'on') ? true : false;

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
            $mailchimp_email = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
            $user_name = explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) );
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
            $monitor_name    = stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) );

            // ActiveCampaign
            $active_camp_res        = ($quiz_settings->ays_get_setting('active_camp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('active_camp');
            $active_camp            = json_decode($active_camp_res, true);
            $active_camp_url        = isset($active_camp['url']) ? $active_camp['url'] : '';
            $active_camp_api_key    = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';
            $enable_active_camp     = (isset($options->enable_active_camp) && $options->enable_active_camp == 'on') ? true : false;
            $active_camp_list       = (isset($options->active_camp_list)) ? $options->active_camp_list : '';
            $active_camp_automation = (isset($options->active_camp_automation)) ? $options->active_camp_automation : '';
            $active_camp_email      = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $user_name              = explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) );
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
            $zapier_data['Name']  = isset($_REQUEST['ays_user_name']) ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";
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

            $slack_data['Name']   = isset($_REQUEST['ays_user_name']) ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";
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

            // Show information form only once
            $settings_options['quiz_show_information_form_only_once'] = (isset( $settings_options['quiz_show_information_form_only_once'] ) && $settings_options['quiz_show_information_form_only_once'] == 'on') ? sanitize_text_field( $settings_options['quiz_show_information_form_only_once'] ) : 'off';
            $quiz_show_information_form_only_once = (isset($settings_options['quiz_show_information_form_only_once']) && $settings_options['quiz_show_information_form_only_once'] == 'on') ? true : false;
            
            // Limit user
            $options->limit_users = isset($options->limit_users) ? $options->limit_users : 'off';
            $limit_users = (isset($options->limit_users) && $options->limit_users == 'on') ? true : false;

            // Limit user by
            $limit_users_by = (isset($options->limit_users_by) && $options->limit_users_by != '') ? $options->limit_users_by : 'ip';

            // Limit user by
            $options->turn_on_extra_security_check = isset($options->turn_on_extra_security_check) ? $options->turn_on_extra_security_check : 'on';
            $turn_on_extra_security_check = (isset($options->turn_on_extra_security_check) && $options->turn_on_extra_security_check == 'on') ? true : false;

            $quiz_max_pass_count = (isset( $options->quiz_max_pass_count ) && $options->quiz_max_pass_count != '') ? absint( intval( $options->quiz_max_pass_count ) ) : 1;

            $limit_attempts_count_by_user_role = (isset($options->limit_attempts_count_by_user_role) && $options->limit_attempts_count_by_user_role != "") ? absint(intval($options->limit_attempts_count_by_user_role)) : null;

            // Quiz Title
            $quiz_title = (isset($quiz['title']) && $quiz['title'] != '') ? esc_attr( stripslashes( $quiz['title'] ) ) : '';

            // Keyword Default Max Value
            $keyword_default_max_value = (isset($settings_options['keyword_default_max_value']) && $settings_options['keyword_default_max_value'] != '') ? absint($settings_options['keyword_default_max_value']) : 6;

            // Pass score of the quiz
            $quiz_pass_score = (isset($options->quiz_pass_score) && $options->quiz_pass_score != "") ? intval($options->quiz_pass_score) : 0;

            $assign_keywords_texts = (isset($options->assign_keywords) && !empty($options->assign_keywords)) ?  $options->assign_keywords : array();

            $limit_users_attr = array(
                'id' => $quiz_id,
                'name' => 'ays_quiz_cookie_',
                'title' => $quiz_title,
            );

            if( !is_null($limit_attempts_count_by_user_role) && $limit_attempts_count_by_user_role !== '' ){
                $restrict_user_role = (isset($options->user_role) && !empty($options->user_role) != '') ? $options->user_role : array();
                if ( isset($options->enable_restriction_pass) && $options->enable_restriction_pass == 'on' && !empty( $restrict_user_role )  ) {
                    $quiz_max_pass_count = $limit_attempts_count_by_user_role;
                }
            }

            $started_user_count = Quiz_Maker_Data::get_limit_cookie_count( $limit_users_attr );
            if( $quiz_max_pass_count > $started_user_count){
                $limit_users_attr['increase_count'] = true;
            }

            $check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $limit_users_attr );
            $return_false_status_arr = array(
                "status" => false,
                "flag" => false,
                "text" => __( 'You have already passed this quiz.', $this->plugin_name ),
            );

            if ( $limit_users ) {
                if ( !$turn_on_extra_security_check ) {
                    switch ( $limit_users_by ) {
                        case 'ip':
                            break;
                        case 'user_id':
                            break;
                        case 'cookie':
                            if ( ! $check_cookie ) {
                                $set_cookie = Quiz_Maker_Data::ays_quiz_set_cookie( $limit_users_attr );
                            }

                            if( $quiz_max_pass_count <= $started_user_count){
                                echo json_encode( $return_false_status_arr );
                                wp_die();
                            }

                            break;
                        case 'ip_cookie':
                            $check_user_by_ip = Quiz_Maker_Data::get_user_by_ip($quiz_id, $quiz_pass_score);
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
            }


            // User explanation            

            if(isset($_REQUEST['user-answer-explanation']) && count($_REQUEST['user-answer-explanation']) != 0){
                $user_explanation = $_REQUEST['user-answer-explanation'];
            }else{
                $user_explanation = array();
            }

            $questions_count = count($question_ids);
            $all_questions_id_arr = array();
            $correctness = array();
            $user_answered = array();
            $correctness_results = array();
            $answer_max_weights = array();
            $questions_title_arr = array();

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
                $points_keywords_arr = array();
                foreach ($questions_answers as $key => $questions_answer) {
                    $continue = false;
                    $question_id = explode('-', $key)[2];
                    if(Quiz_Maker_Data::is_question_not_influence($question_id)){
                        $questions_count--;
                        $continue = true;
                    }

                    $all_questions_id_arr[] = $question_id;

                    $multiple_correctness = array();
                    $keyword_points_sum = array();
                    $has_multiple = Quiz_Maker_Data::has_multiple_correct_answers($question_id);
                    $is_checkbox = Quiz_Maker_Data::is_checkbox_answer($question_id);
                    $is_matching_answer = Quiz_Maker_Data::is_matching_answer($question_id);
                    $is_fill_in_blank = Quiz_Maker_Data::is_fill_in_blank_answer($question_id);
                    $answers_weight = Quiz_Maker_Data::get_question_answers_weight($question_id);
                    $current_question_category_id = Quiz_Maker_Data::get_question_category_id_by_question_id($question_id);

                    if( $is_fill_in_blank ){
                        $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_fill_in_blank_max_weight($question_id, $questions_answers_ids_arr);
                    } elseif( $is_matching_answer ) {
                        $answer_max_weights[$question_id] = $answers_weight;
                    } else {
                        $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_max_weight($question_id, $is_checkbox);
                    }
                    
                    if( $is_fill_in_blank ){
                        $new_questions_answer = array();

                        if( is_string( $questions_answer ) ){
                            $questions_answer = array( $questions_answer );
                        }

                        foreach ($questions_answer as $q_key => $user_q_answer) {
                            $q_fill_in_blank_answer_id = isset( $q_key ) && $q_key != "" ? absint( $q_key ) : 0;

                            if( $q_fill_in_blank_answer_id == 0 ){
                                continue;
                            }

                            $q_answer_id = $q_fill_in_blank_answer_id;

                            $new_questions_answer[ $q_answer_id ] = $user_q_answer;

                        }

                        $user_answered["question_id_" . $question_id] = $new_questions_answer;
                        $questions_title = Quiz_Maker_Data::get_quiz_question_title_by_id($question_id);
                        $questions_title_arr[$question_id] = $questions_title;

                    } else {
                        $user_answered["question_id_" . $question_id] = $questions_answer;
                    }

                    if($is_checkbox){
                       $has_multiple = true;
                    } elseif ($is_fill_in_blank) {
                        $has_multiple = false;
                    }

                    if ($has_multiple) {
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_id) {
                                $multiple_correctness[] = Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, $calculate_score);
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $answer_id);
                                $keyword_points_sum[] = Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, 'by_points');
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                    $points_keywords_arr[$question_id][] = array(
                                        'keyword' => $answer_keyword,
                                        'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, 'by_points'),
                                        'cat_id'  => $current_question_category_id,
                                    );
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
                            $questions_answer_keyword = $questions_answer;
                            if( intval( $questions_answer_keyword ) != 0 ){
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer_keyword);
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                    $points_keywords_arr[$question_id] = array(
                                        'keyword' => $answer_keyword,
                                        'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                        'cat_id'  => $current_question_category_id,
                                    );
                                }
                            }
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                                $points_keywords_arr[$question_id] = array(
                                    'keyword' => $answer_keyword,
                                    'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                    'cat_id'  => $current_question_category_id,
                                );
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

                                $questions_answer = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                if(!$continue){
                                    $correctness[$question_id] = $this->isHomogenous( array( $questions_answer ), $question_id );
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenous( array( $questions_answer ), $question_id );
                            }
                        }
                    } elseif(Quiz_Maker_Data::has_text_answer($question_id)) {
                        $quests_data = ( isset( $quests[$question_id] ) && ! empty( $quests[$question_id] ) ) ? $quests[$question_id] : array();
                        $quests_data_options = isset( $quests_data['options'] ) ? json_decode( $quests_data['options'], true ) : array();

                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, null, $calculate_score, $quests_data_options);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, null, $calculate_score, $quests_data_options);
                    } elseif( $is_fill_in_blank ) {
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_key => $answer_id) {

                                if( isset( $questions_answers_ids_arr[$question_id] ) && is_string( $questions_answers_ids_arr[$question_id] ) ){
                                    $questions_answers_ids_arr[$question_id][$answer_key] = $questions_answers_ids_arr[$question_id];
                                }

                                $fill_in_blank_answer_id = isset( $answer_key ) && $answer_key != "" ? absint( $answer_key ) : 0;

                                if( $fill_in_blank_answer_id == 0 ){
                                    continue;
                                }

                                $answer_id = $fill_in_blank_answer_id;

                                $quests_data = ( isset( $quests[$question_id] ) && ! empty( $quests[$question_id] ) ) ? $quests[$question_id] : array();
                                $quests_data_options = isset( $quests_data['options'] ) ? json_decode( $quests_data['options'], true ) : array();

                                if(!$continue){
                                    $correctness[$question_id][$answer_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer[$answer_key], $answer_id, $calculate_score, $quests_data_options);
                                }
                                $correctness_results["question_id_" . $question_id][$answer_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer[$answer_key], $answer_id, $calculate_score, $quests_data_options);

                                $multiple_correctness[] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer[$answer_key], $answer_id, $calculate_score, $quests_data_options);

                            }

                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = array_sum($multiple_correctness);
                                }
                                $correctness_results["question_id_" . $question_id] = array_sum($multiple_correctness);
                                continue;
                            }
                            
                            
                            if ($this->isHomogenous($multiple_correctness, $question_id, "fill_in_blank")) {
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
                    } elseif( $is_matching_answer ) {

                        foreach ($questions_answer as $answer_id => $given_answer ) {
                            $multiple_correctness[] = absint( $answer_id ) === absint( $given_answer );
                        }

                        if( $calculate_score == 'by_points' ){
                            if ( $strong_count_checkbox === false ) {
                                $earned_weight = $this->isHomogenousStrong( $multiple_correctness, $question_id, 'matching' ) * $answers_weight;
                                $earned_weight = round( $earned_weight, 2 );
                                if ( ! $continue ) {
                                    $correctness[$question_id] = $earned_weight;
                                }
                                $correctness_results["question_id_" . $question_id] = $earned_weight;
                            } else {
                                if ( $this->isHomogenous( $multiple_correctness, $question_id, 'matching' ) ) {
                                    if ( ! $continue ) {
                                        $correctness[$question_id] = $answers_weight;
                                    }
                                    $correctness_results["question_id_" . $question_id] = $answers_weight;
                                } else {
                                    if ( ! $continue ) {
                                        $correctness[$question_id] = floatval(0);
                                    }
                                    $correctness_results["question_id_" . $question_id] = floatval(0);
                                }
                            }
                            continue;
                        }

                        if ( is_array( $questions_answer ) ) {
                            if ( $strong_count_checkbox === false ) {
                                if ( ! $continue ) {
                                    $correctness[$question_id] = $this->isHomogenousStrong( $multiple_correctness, $question_id, 'matching' );
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenousStrong( $multiple_correctness, $question_id, 'matching' );
                            } else {
                                if ( $this->isHomogenous( $multiple_correctness, $question_id, 'matching' ) ) {
                                    if ( ! $continue ) {
                                        $correctness[$question_id] = true;
                                    }
                                    $correctness_results["question_id_" . $question_id] = true;
                                } else {
                                    if ( ! $continue ) {
                                        $correctness[$question_id] = false;
                                    }
                                    $correctness_results["question_id_" . $question_id] = false;
                                }
                            }
                        } else {
                            if ( ! $continue ) {
                                $correctness[$question_id] = false;
                            }
                            $correctness_results["question_id_" . $question_id] = false;
                        }
                    } else {
                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        if( intval( $questions_answer ) != 0 ){
                            $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                            if(!is_null($answer_keyword) && $answer_keyword != false){
                                $keywords_arr[] = $answer_keyword;
                                $points_keywords_arr[$question_id] = array(
                                    'keyword' => $answer_keyword,
                                    'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                    'cat_id'  => $current_question_category_id,
                                );
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

                $final_score_by_cats = array();
                $quiz_weight_cats = array();
                $correct_answered_count_cats = array();
                $correct_answered_count_cats_arr_length = array();
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

                    // $correct_answered_count_cat = array_sum($q_ids);
                    $correct_answered_count_cats[$cat_id] = array_sum($q_ids);
                    $correct_answered_count_cats_arr_length[$cat_id] = count($quiz_weight_correctness_by_cats[$cat_id]);

                    if(floatval($quiz_weight_cat) == 0){
                        $final_score_by_cats[$cat_id] = floatval(0);
                    }else{
                        // $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                        //$final_score_by_cats[$cat_id] = floatval(floor((intval($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
                        $final_score_by_cats[$cat_id] = floatval(floor((($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
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

                    if ( $final_score <= 0 ) {
                        $final_score = 0;
                    }
                }

                $score_by_cats = array();
                foreach($final_score_by_cats as $cat_id => $cat_score){
                    switch($display_score){
                        case "by_correctness":
                            $score_by_cats[$cat_id] = array(
                                'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                                'avg_cat_score' => "",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_points":
                            $correct_answered_count_cats_1 = 0;
                            $correct_answered_count_cats_2 = 0;
                            if( $correct_answered_count_cats_arr_length[$cat_id] == 0 ){
                                $correct_answered_count_cats_1 = 0;
                            }else {
                                $correct_answered_count_cats_1 = floatval($correct_answered_count_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                                $correct_answered_count_cats_1 = round( $correct_answered_count_cats_1, 2 );
                            }

                            if( $quiz_weight_cats[$cat_id] == 0 ){
                                $correct_answered_count_cats_2 = 0;
                            }else {
                                $correct_answered_count_cats_2 = floatval($quiz_weight_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                                $correct_answered_count_cats_2 = round( $correct_answered_count_cats_2, 2 );
                            }

                            $score_by_cats[$cat_id] = array(
                                // 'score' => $correct_answered_count_cat[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'score' => $correct_answered_count_cats[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'avg_cat_score' => $correct_answered_count_cats_1 . " / " . $correct_answered_count_cats_2,
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_percentage":
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'avg_cat_score' => "",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        default:
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'avg_cat_score' => "",
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

                $final_display_score_mail = $score;

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

                $only_wrong_answers_count = $questions_count - ( $corrects_count + $skipped_questions_count );
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
                    $avg_result_score_by_categories = '';
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

                    $avg_result_score_by_categories = '<div class="ays_avg_result_by_cats">';
                    foreach($score_by_cats as $cat_id => $cat){
                        $avg_result_score_by_categories .= '<p class="ays_avg_result_by_cat">
                            <strong class="ays_avg_result_by_cat_name">'. $cat['categoryName'] .':</strong>
                            <span class="ays_avg_result_by_cat_score">'. $cat['avg_cat_score'] .'</span>
                        </p>';
                    }
                    $avg_result_score_by_categories .= '</div>';
                    $avg_result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $avg_result_score_by_categories);
                }

                $score_by = ($display_score_by == 'by_percentage') ? $final_score : intval($correct_answered_count);
                $score_by = '';
                $if_equal_keyword = false;
                switch ($display_score_by) {
                    case 'by_percentage':
                        $score_by = $final_score;
                        break;
                    case 'by_points':
                        $score_by = floatval( $correct_answered_count );
                        break;
                    case 'by_keywords':
                        if($apply_points_to_keywords){
                            $points_keywords_full_arr = array();
                            $points_sum_keywords_arr = array();

                            foreach ($points_keywords_arr as $id => $points_keywords) {
                                if(!array_key_exists('keyword', $points_keywords)){
                                    foreach ($points_keywords as $key => $value) {
                                        $points_keywords_full_arr[] = $value;
                                    }
                                }else{
                                   $points_keywords_full_arr[] = $points_keywords;
                                }
                            }

                            foreach ($points_keywords_full_arr as $id => $points_keywords) {
                                $points_sum_keywords_arr[$points_keywords['keyword']] = 0;
                            }

                            foreach ($points_keywords_full_arr as $id => $points_keywords) {
                                $points_sum_keywords_arr[$points_keywords['keyword']] += $points_keywords['point'];
                            }
                            if( is_array( $points_keywords_full_arr ) ){
                                if ( !empty( $points_sum_keywords_arr ) ) {
                                    $max_keywords_answered_count = max( $points_sum_keywords_arr );
                                    $new_points_sum_keywords_arr = $points_sum_keywords_arr;
                                    $max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $points_sum_keywords_arr );

                                    if( $max_keywords_answered_keyword != '' ){
                                        unset( $new_points_sum_keywords_arr[$max_keywords_answered_keyword] );

                                        $new_max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $new_points_sum_keywords_arr );

                                        if( $new_max_keywords_answered_keyword ){
                                            $if_equal_keyword = true;
                                        }

                                    }

                                    $score_by = $max_keywords_answered_keyword;
                                } else {
                                    $score_by = "";
                                }
                            }else{
                                $score_by = "";
                            }
                        }else{
                            if( is_array( $keywords_arr ) ){
                                $keywords_count_arr = array_count_values($keywords_arr);
                                if ( ! empty( $keywords_count_arr ) ) {
                                    $max_keywords_answered_count = max( $keywords_count_arr );
                                    $new_keywords_count_arr = $keywords_count_arr;
                                    $max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $keywords_count_arr );

                                    if( $max_keywords_answered_keyword != '' ){
                                        unset( $new_keywords_count_arr[$max_keywords_answered_keyword] );

                                        $new_max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $new_keywords_count_arr );

                                        if( $new_max_keywords_answered_keyword ){
                                            $if_equal_keyword = true;
                                        }

                                    }

                                    $score_by = $max_keywords_answered_keyword;
                                } else {
                                    $score_by = "";
                                }
                            }else{
                                $score_by = "";
                            }
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
                        if( $if_equal_keyword ){
                            $interval_flag = true;
                        } else {
                            if ($quiz_interval['interval_keyword'] == $score_by) {
                                $interval_flag = true;
                            }
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
                        $interval_redirect_url = $quiz_interval['interval_redirect_url'];
                        $interval_redirect_delay = $quiz_interval['interval_redirect_delay'];

                        $interval_message = "";
                        $interval_redirect_after = "";
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
                        if($interval_redirect_url !== null && $interval_redirect_url != ''){
                            if($interval_redirect_delay == ''){
                               $interval_redirect_delay = 0;
                            }
                            $interval_redirect_after = Quiz_Maker_Data::secondsToWords($interval_redirect_delay);
                        }

                        if( $if_equal_keyword ){
                            $interval_message = "<div>" . $quiz_equal_keywords_text . "</div>";
                            $interval_redirect_delay = 0;
                            $interval_redirect_after = "";
                            $interval_redirect_url = "";
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
                    $quiz_image_alt_text = Quiz_Maker_Data::ays_quiz_get_image_id_by_url($quiz_image);

                    $quiz_logo = '<img src="'.$quiz_image.'" alt="'. $quiz_image_alt_text .'" title="Quiz logo">';
                }

                $user_first_name        = '';
                $user_last_name         = '';
                $user_nickname          = '';
                $user_display_name      = '';
                $user_wordpress_email   = '';
                $user_wordpress_roles   = '';
                $user_wordpress_website = '';
                $user_id = get_current_user_id();
                if($user_id != 0){
                    $usermeta = get_user_meta( $user_id );
                    if($usermeta !== null){
                        $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                        $user_last_name = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                        $user_nickname   = (isset($usermeta['nickname'][0]) && sanitize_text_field( $usermeta['nickname'][0] != '') ) ? sanitize_text_field( $usermeta['nickname'][0] ) : '';
                    }

                    $current_user_data = get_userdata( $user_id );
                    if ( ! is_null( $current_user_data ) && $current_user_data ) {
                        $user_display_name    = ( isset( $current_user_data->data->display_name ) && $current_user_data->data->display_name != '' ) ? sanitize_text_field( $current_user_data->data->display_name ) : "";
                        $user_wordpress_email = ( isset( $current_user_data->data->user_email ) && $current_user_data->data->user_email != '' ) ? sanitize_text_field( $current_user_data->data->user_email ) : "";

                        $user_wordpress_roles = ( isset( $current_user_data->roles ) && ! empty( $current_user_data->roles ) ) ? $current_user_data->roles : "";

                        if ( !empty( $user_wordpress_roles ) && $user_wordpress_roles != "" ) {
                            if ( is_array( $user_wordpress_roles ) ) {
                                $user_wordpress_roles = implode(",", $user_wordpress_roles);
                            }
                        }

                        $user_wordpress_website_url = ( isset( $current_user_data->user_url ) && ! empty( $current_user_data->user_url ) ) ? sanitize_url($current_user_data->user_url) : "";

                        if( !empty( $user_wordpress_website_url ) ){
                            $user_wordpress_website = "<a href='". esc_url( $user_wordpress_website_url ) ."' target='_blank' class='ays-quiz-user-website-link-a-tag'>". __( "Website", $this->plugin_name ) ."</a>";
                        }
                    }
                }

                $current_quiz_author = __( "Unknown", $this->plugin_name );
                $current_quiz_author_email = "";

                $super_admin_email = get_option('admin_email');

                $current_quiz_user_data = get_userdata( $quiz_current_author );
                if ( ! is_null( $current_quiz_user_data ) && $current_quiz_user_data ) {
                    $current_quiz_author = ( isset( $current_quiz_user_data->data->display_name ) && $current_quiz_user_data->data->display_name != '' ) ? sanitize_text_field( $current_quiz_user_data->data->display_name ) : "";
                    $current_quiz_author_email = ( isset( $current_quiz_user_data->data->user_email ) && $current_quiz_user_data->data->user_email != '' ) ? sanitize_text_field( $current_quiz_user_data->data->user_email ) : "";
                }

                $active_coupon = '';
                if ( $quiz_enable_coupon ) {
                    $active_coupon = Quiz_Maker_Data::ays_quiz_get_active_coupon( $quiz_id, $options );
                }

                $result_unique_code = strtoupper( uniqid() );

                $quiz_curent_page_link = isset( $_REQUEST['ays_quiz_curent_page_link'] ) && $_REQUEST['ays_quiz_curent_page_link'] != '' ? sanitize_url( $_REQUEST['ays_quiz_curent_page_link'] ) : "";

                $quiz_current_page_link_html = "<a href='". esc_sql( $quiz_curent_page_link ) ."' target='_blank' class='ays-quiz-curent-page-link-a-tag'>". __( "Quiz link", $this->plugin_name ) ."</a>";

                if($disable_user_ip){
                    $current_user_ip = '';
                }else{
                    $current_user_ip = Quiz_Maker_Data::get_user_ip();
                }

                $avg_user_points = Quiz_Maker_Data::ays_get_average_of_points_by_user($quiz_id, $user_id);

                $correctness_and_answers_arr = array(
                    $correctness_and_answers
                );

                $result_score_by_tags = Quiz_Maker_Data::ays_quiz_current_result_by_tag($correctness_and_answers_arr, $correctness, $quiz_questions_ids, $calculate_score, $display_score);

                $message_data = array(
                    'quiz_name'                     => stripslashes($quiz_title),
                    'user_name'                     => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
                    'user_email'                    => sanitize_email( $_REQUEST['ays_user_email'] ),
                    'user_phone'                    => stripslashes( sanitize_text_field( $_REQUEST['ays_user_phone'] ) ),
                    'user_pass_time'                => Quiz_Maker_Data::get_time_difference(sanitize_text_field($_REQUEST['start_date']), sanitize_text_field($_REQUEST['end_date'])),
                    'quiz_time'                     => Quiz_Maker_Data::secondsToWords($options->timer),
                    'score'                         => $final_score . "%",
                    'user_points'                   => $correct_answered_count,
                    'max_points'                    => $quiz_weight,
                    'user_corrects_count'           => $corrects_count,
                    'questions_count'               => $questions_count,
                    'quiz_logo'                     => $quiz_logo,
                    'avg_score'                     => Quiz_Maker_Data::ays_get_average_of_scores($quiz_id) . "%",
                    'avg_rate'                      => round(Quiz_Maker_Data::ays_get_average_of_rates($quiz_id), 1),
                    'current_date'                  => date_i18n( get_option( 'date_format' ), strtotime( sanitize_text_field( $_REQUEST['end_date'] ) ) ),
                    'results_by_cats'               => $result_score_by_categories,
                    'unique_code'                   => $result_unique_code,
                    'wrong_answers_count'           => $wrong_answered_count,
                    'not_answered_count'            => $skipped_questions_count,
                    'skipped_questions_count'       => $skipped_questions_count,
                    'answered_questions_count'      => $answered_questions_count,
                    'score_by_answered_questions'   => $score_by_answered_questions,
                    'user_first_name'               => $user_first_name,
                    'user_last_name'                => $user_last_name,
                    'only_wrong_answers_count'      => $only_wrong_answers_count,
                    'quiz_coupon'                   => $active_coupon,
                    'user_nickname'                 => $user_nickname,
                    'user_display_name'             => $user_display_name,
                    'user_wordpress_email'          => $user_wordpress_email,
                    'user_wordpress_roles'          => $user_wordpress_roles,
                    'user_wordpress_website'        => $user_wordpress_website,
                    'quiz_creation_date'            => date_i18n( get_option( 'date_format' ), strtotime( $quiz_creation_date ) ),
                    'current_quiz_author'           => $current_quiz_author,
                    'current_quiz_page_link'        => $quiz_current_page_link_html,
                    'current_user_ip'               => $current_user_ip,
                    'current_quiz_author_email'     => $current_quiz_author_email,
                    'admin_email'                   => $super_admin_email,
                    'avg_user_points'               => $avg_user_points,
                    'avg_res_by_cats'               => $avg_result_score_by_categories,
                    'results_by_tags'               => $result_score_by_tags,
                );

                $all_mv_keywords_arr = Quiz_Maker_Data::ays_quiz_generate_keyword_array($keyword_default_max_value);
                $mv_keyword_counts = array_count_values($keywords_arr);
                $get_keyword_max_point_percent = Quiz_Maker_Data::keyword_data_by_user_answer( $points_keywords_arr, $all_questions_id_arr, $quiz_id);


                $personality_result_by_question_data = array(
                    'points_keywords_arr'       => $points_keywords_arr,
                    'all_questions_id_arr'      => $all_questions_id_arr,
                    'quiz_id'                   => $quiz_id,
                    'assign_keywords_texts'     => $assign_keywords_texts,
                    'apply_points_to_keywords'  => $apply_points_to_keywords,
                );
                $message_data[ 'personality_result_by_question_ids' ] = $personality_result_by_question_data;

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

                    if( array_key_exists( $value, $get_keyword_max_point_percent) ){
                        $message_data[ 'user_keyword_point_' . $value ] = $get_keyword_max_point_percent[$value]['user_keyword_point'];
                        $message_data[ 'max_point_keyword_' . $value ] = $get_keyword_max_point_percent[$value]['user_keyword_point'].'/'.$get_keyword_max_point_percent[$value]['max_point_keyword'];
                        $message_data[ 'user_keyword_percentage_' . $value ] = $get_keyword_max_point_percent[$value]['user_keyword_percentage'].'%';
                    }

                }

                if($enable_top_keywords){
                    $assign_keywords_count_arr = array();
                    $assign_keywords_percentage_arr = array();

                    if($apply_points_to_keywords){
                        $top_points_keywords_arr = $points_keywords_arr;

                        $top_points_keywords_full_arr = array();
                        $top_points_sum_keywords_arr = array();

                        foreach ($top_points_keywords_arr as $id => $points_keywords) {
                            if(!array_key_exists('keyword', $points_keywords)){
                                foreach ($points_keywords as $key => $value) {
                                    $top_points_keywords_full_arr[] = $value;
                                }
                            }else{
                               $top_points_keywords_full_arr[] = $points_keywords;
                            }
                        }

                        foreach ($top_points_keywords_full_arr as $id => $points_keywords) {
                            $top_points_sum_keywords_arr[$points_keywords['keyword']] = 0;
                        }

                        foreach ($top_points_keywords_full_arr as $id => $points_keywords) {
                            $top_points_sum_keywords_arr[$points_keywords['keyword']] += $points_keywords['point'];
                        }

                        foreach ($all_mv_keywords_arr as $key => $value) {
                            $top_keyword_percentage = 0;
                            $total_top_keywords_count = array_sum($top_points_sum_keywords_arr);
                            if($total_top_keywords_count > 0){
                                $top_keyword_percentage = ( $top_points_sum_keywords_arr[$value] / $total_top_keywords_count ) * 100;
                            }

                            if( array_key_exists( $value, $top_points_sum_keywords_arr) ){
                                $assign_keywords_count_arr[$value]['keyword_count'] = $top_points_sum_keywords_arr[$value];
                                $assign_keywords_percentage_arr[$value]['keyword_percentage'] = round($top_keyword_percentage,2);
                            }
                        }

                    } else {
                        foreach ($all_mv_keywords_arr as $key => $value) {
                            $top_keyword_percentage = 0;
                            $total_top_keywords_count = array_sum($mv_keyword_counts);
                            if($total_top_keywords_count > 0){
                                $top_keyword_percentage = ( $mv_keyword_counts[$value] / $total_top_keywords_count ) * 100;
                            }

                            if( array_key_exists( $value, $mv_keyword_counts) ){
                                $assign_keywords_count_arr[$value]['keyword_count'] = $mv_keyword_counts[$value];
                                $assign_keywords_percentage_arr[$value]['keyword_percentage'] = round($top_keyword_percentage,2);
                            }
                        }
                    }

                    $assign_keywords_obj = (isset($options->assign_keywords) && !empty($options->assign_keywords)) ?  $options->assign_keywords : array();


                    foreach ($assign_keywords_obj as $key => $value) {
                        if( array_key_exists( $value->assign_top_keyword, $assign_keywords_count_arr) ){
                            $assign_keywords_count_arr[$value->assign_top_keyword]['keyword_text'] = $value->assign_top_keyword_text;
                        }

                        if( array_key_exists( $value->assign_top_keyword, $assign_keywords_percentage_arr) ){
                            $assign_keywords_percentage_arr[$value->assign_top_keyword]['keyword_text'] = $value->assign_top_keyword_text;

                        }
                    }

                    usort($assign_keywords_count_arr, array( $this, 'sortByOrderTopKeywords' ) );
                    usort($assign_keywords_percentage_arr, array( $this, 'sortByOrderTopKeywords' ) );

                    $message_data[ 'top_keywords_count' ] = $assign_keywords_count_arr;
                    $message_data[ 'top_keywords_percentage' ] = $assign_keywords_percentage_arr;
                }

                $interval_message_for_cert = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message_for_cert;

                $quiz_attributes_information = array();
                foreach ($quiz_attributes as $attribute) {
                    $attr_value = (isset($_REQUEST[strval($attribute->slug)])) ? stripslashes($_REQUEST[strval($attribute->slug)]) : '';
                    $quiz_attributes_information[strval($attribute->name)] = $attr_value;
                    $message_data[$attribute->slug] = $attr_value;
                }

                if($disable_user_ip){
                    $user_ip = '';
                }else{
                    $user_ip = Quiz_Maker_Data::get_user_ip();
                }

                if($chained_quiz_id !== null ){
                    if (!is_user_logged_in()) {
                        if (!isset($_COOKIE['ays_chained_quiz_guest_id'])) {
                            $unique_id = uniqid();
                            setcookie('ays_chained_quiz_guest_id', $unique_id,  time()+60*60*24*30, "/");
                        } else {
                            $unique_id = sanitize_text_field($_COOKIE['ays_chained_quiz_guest_id']);
                        }
                    } else {
                        $unique_id = '';
                    }
                }

                $data = array(
                    'user_ip' => $user_ip,
                    'user_name' => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
                    'user_email' => sanitize_email( $_REQUEST['ays_user_email'] ),
                    'user_phone' => stripslashes( sanitize_text_field( $_REQUEST['ays_user_phone'] ) ),
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
                    'quiz_coupon' => $active_coupon,
                    'chained_quiz_id' => $chained_quiz_id,
                    'final_display_score' => $final_display_score_mail,
                    'chained_quiz_guest_unique_id' => $unique_id,
                    'questions_title' => $questions_title_arr,
                );

                $message_data['conditions_mv_keywords_counts'] = $mv_keyword_counts;
                
                $nsite_url_base = get_site_url();
                $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
                $nsite_url = trim( $nsite_url_replaced, '/' );
                //$nsite_url = "levon.com";
                $nno_reply = "noreply@".$nsite_url;

                if(isset($options->email_config_from_name) && $options->email_config_from_name != "") {
                    $uname = stripslashes($options->email_config_from_name);
                } else {
                    $uname = 'Quiz Maker';
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
                $subject = Quiz_Maker_Data::ays_quiz_translate_content( $subject );
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
                            $options->mail_message = __("Certificate", $this->plugin_name);
                        }
                    }else{
                        $options->mail_message = __("Certificate", $this->plugin_name);
                        $force_mail_to_user = true;
                    }
                    $options->user_mail = "on";
                }

                $send_mail_to_user = isset($options->user_mail) && $options->user_mail == "on" ? true : false;
                
                if( $enable_certificate_without_send === true ){
                    $cert = true;
                }

                if( $is_training === true ){
                    $disable_store_data = false;
                }

                $pdf_response = null;
                $pdf_content = null;
                if( $send_mail_to_user || $enable_certificate_without_send ){

                    switch ( $quiz_certificate_pass_score_type ) {
                        case 'point':
                            $certificate_final_score = $correct_answered_count;
                            break;
                        
                        case 'percentage':
                        default:
                            $certificate_final_score = $final_score;
                            break;
                    }

                    if($cert && $certificate_final_score >= intval($options->certificate_pass)){
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
                            "cert_user"     => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
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

                $conditions_data = array("hasAction" => false);
                if(has_action('ays_qm_conditions_action')){
                    $quiz_conditions = isset($quiz['conditions']) && $quiz['conditions'] != "" ? json_decode($quiz['conditions'], true) : array();
                    // $conditions_data = apply_filters( 'ays_qm_conditions_action', $quiz_conditions, $questions_answers );
                    $conditions_data = apply_filters( 'ays_qm_conditions_action', $quiz_conditions, $questions_answers, $options, $message_data );
                    $cond_page_message = "";
                    if(!empty($conditions_data)){
                        $conditions_data['hasAction'] = true;
                        $cond_page_message = isset($conditions_data['pageMessage']) && $conditions_data['pageMessage'] != "" ? $conditions_data['pageMessage'] : "";
                        $cond_email_file_id = isset($conditions_data['email_file_id']) && $conditions_data['email_file_id'] != "" ? $conditions_data['email_file_id'] : "";
                        $cond_email_file = isset($conditions_data['email_file']) && $conditions_data['email_file'] != "" ? $conditions_data['email_file'] : "";
                        $cond_email_message = isset($conditions_data['emailMessage']) && $conditions_data['emailMessage'] != "" ? $conditions_data['emailMessage'] : "";
                        $conditions_data['pageMessage'] = Quiz_Maker_Data::replace_message_variables($cond_page_message, $message_data);
                        $wp_user = null;
                        if( is_user_logged_in() ){
                            $wp_user = get_userdata( get_current_user_id() );
                        }
                        $c_user_email = isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "" ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
                        $c_user_name = isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != "" ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";

                        $cond_user_email = $c_user_email == "" ? $wp_user->data->user_email : $c_user_email;
                        $cond_user_name =  $c_user_name  == "" ? $wp_user->data->display_name : $c_user_name;

                        $conditions_email_data = array(
                            "cond_user_email"    => $cond_user_email,
                            "cond_user_name"     => $cond_user_name,
                            "cond_email_file_id" => $cond_email_file_id,
                            "cond_email_message"  => $cond_email_message,
                            "from"     => $nfrom,
                            "reply_to" => $nreply,
                            "subject"  => $subject,
                            "message_data"  => $message_data,
                        );
                        if($cond_email_file || $cond_email_message){
                            do_action("ays_qm_conditions_send_email", $conditions_email_data);
                        }
                    }
                    else{
                        $conditions_data['hasAction'] = false;
                    }
                }

                if($paypal_result_send) {
                    // Disabling store data in DB
                    $download_certificate_html = "";

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
                                <a target='_blank' href='" . $cert_file_url . "' class='action-button ays_download_certificate ".$class_for_keyboard."' download='" . $cert_file_name . "'>" . __( "Download your certificate", $this->plugin_name ) . "</a>
                            </div>";
                        }
                    }

                    if($disable_store_data){
                        $result_id = $this->add_results_to_db($data);
                        $g_last_id = $wpdb->insert_id;
                        $google_data['results_last_id'] = $result_id;

                        $result = true;
                        if( is_bool( $result_id ) ){
                            $result = $result_id;
                        }
                    }else{
                        $result = true;
                    }
                }

                if($paypal_type !== null){
                    if($paypal_mail_send){
                        $result = true;
                    }
                }

                if( $is_training === true ){
                    $enable_certificate_without_send = false;
                    $send_mail_to_user = false;
                    $result = true;
                }

                $last_result_id = $wpdb->insert_id;
                if( $limited_result_id !== null ){
                    $last_result_id = $limited_result_id;
                }

                if( $quiz_show_information_form_only_once ){
                    if (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "" && filter_var($_REQUEST['ays_user_email'], FILTER_VALIDATE_EMAIL)) {
                        // Check user limitation
                        $custom_limit_users_attr = array(
                            'name' => 'ays_quiz_user_information_cookie',
                            'title' => $quiz_id,
                        );

                        $custom_check_cookie = Quiz_Maker_Data::ays_quiz_check_cookie( $custom_limit_users_attr, false );
                        if ( ! $custom_check_cookie ) {
                            $custom_set_cookie = Quiz_Maker_Data::ays_quiz_set_cookie( $custom_limit_users_attr, false );
                        }

                    }
                }

                $message_data['avg_score_by_category'] = Quiz_Maker_Data::ays_get_average_score_by_category($quiz_id);
                $message_data['download_certificate'] = $download_certificate_html;
                $interval_message = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message;

                if($paypal_mail_send === false){
                    $send_mail_to_user = false;
                } else {
                    if($enable_send_mail_to_user_by_pass_score){
                        switch ( $quiz_pass_score_type ) {
                            case 'point':
                                if($correct_answered_count < $pass_score_count){
                                    $send_mail_to_user = false;
                                }
                                break;
                            
                            case 'percentage':
                            default:
                                if($final_score < $pass_score_count){
                                    $send_mail_to_user = false;
                                }
                                break;
                        }
                    }
                }

                if ($send_mail_to_user) {
                    if (isset($_REQUEST['ays_user_email']) && filter_var($_REQUEST['ays_user_email'], FILTER_VALIDATE_EMAIL)) {
                        $message = (isset($options->mail_message)) ? $options->mail_message : '';
                        $message = Quiz_Maker_Data::replace_message_variables($message, $message_data);
                        $message = str_replace('%name%', stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ), $message);
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

                        $message = Quiz_Maker_Data::ays_quiz_translate_content( $message );
                        
                        $email = sanitize_email( $_REQUEST['ays_user_email'] );
                        $to = stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) . " <$email>";

                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();

                        switch ( $quiz_certificate_pass_score_type ) {
                            case 'point':
                                $certificate_final_score = $correct_answered_count;
                                break;
                            
                            case 'percentage':
                            default:
                                $certificate_final_score = $final_score;
                                break;
                        }

                        if($cert && $certificate_final_score >= intval($options->certificate_pass)){
                            if($pdf_content === true){
                                if ( isset( $pdf_response['cert_file_path'] ) ) {
                                    $cert_path = $pdf_response['cert_file_path']; // array(__DIR__ . '/certificate.pdf');
                                } else {
                                    $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                }
                                $attachment = $cert_path;
                            }
                            if( $enable_certificate_without_send === true ){
                                $attachment = array();
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
                        }elseif(!($cert && $certificate_final_score >= intval($options->certificate_pass))){
                            if(!$force_mail_to_user){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }
                    }
                }

                if($paypal_mail_send === false) {
                    $send_mail_to_admin = false;
                } else {
                    if($enable_send_mail_to_admin_by_pass_score){
                        switch ( $quiz_pass_score_type ) {
                            case 'point':
                                if($correct_answered_count < $pass_score_count){
                                    $send_mail_to_admin = false;
                                }
                                break;
                            
                            case 'percentage':
                            default:
                                if($final_score < $pass_score_count){
                                    $send_mail_to_admin = false;
                                }
                                break;
                        }
                    }
                }

                if( $is_training === true ){
                    $send_mail_to_admin = false;
                }

                if ($send_mail_to_admin) {
                    if (filter_var( trim( get_option('admin_email') ), FILTER_VALIDATE_EMAIL)) {

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

                        $message_content = Quiz_Maker_Data::ays_quiz_translate_content( $message_content );

                        $admin_subject = ' - '.$data['user_name'].' - '.$data['score'].'%';
                        if($data['calc_method'] == 'by_points'){
                            $admin_subject = ' - '.$data['user_name'].' - '.$data['user_points'].'/'.$data['max_points'];
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

                        if(isset($options->use_subject_for_admin_email) && $options->use_subject_for_admin_email == "on") {

                        } else {
                            $subject = stripslashes($quiz['title']).$admin_subject;
                        }

                        $subject = Quiz_Maker_Data::ays_quiz_translate_content( $subject );

                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();

                        switch ( $quiz_certificate_pass_score_type ) {
                            case 'point':
                                $certificate_final_score = $correct_answered_count;
                                break;
                            
                            case 'percentage':
                            default:
                                $certificate_final_score = $final_score;
                                break;
                        }

                        if($send_certificate_to_admin){
                            if($cert && $certificate_final_score >= intval($options->certificate_pass)){
                                if($pdf_content === true){
                                    if ( isset( $pdf_response['cert_file_path'] ) ) {
                                        $cert_path = $pdf_response['cert_file_path']; // array(__DIR__ . '/certificate.pdf');
                                    } else {
                                        $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                    }
                                    $attachment = $cert_path;
                                }
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }elseif(!($cert && $certificate_final_score >= intval($options->certificate_pass))){
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }
                        }else{
                            $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                        }
                    }
                }
                
                if ($paypal_mail_send) {
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
                }

                if( $is_training === true ) {
                    if ( has_action( 'ays_qm_front_end_trainings_save' ) ) {
                        $correct_questions = array();
                        $wrong_questions = array();
                        foreach ( $correctness_results as $q_key => $is_correct ){
                            $qid = explode( '_', $q_key )[2];
                            if( $is_correct === true ){
                                $correct_questions[] = absint( $qid );
                            }else{
                                $wrong_questions[] = absint( $qid );
                            }
                        }
                        do_action( "ays_qm_front_end_trainings_save", $quiz_id, $correct_questions, $wrong_questions );
                    }
                }else{
                    if ( has_action( 'ays_qm_front_end_integrations' ) ) {
                        $integration_args          = array();
                        $integration_options       = (array) $options;
                        $integration_options['id'] = $quiz_id;
                        $integrations_data         = apply_filters( 'ays_qm_front_end_integrations_options', $integration_args, $integration_options );
                        do_action( "ays_qm_front_end_integrations", $integrations_data, $integration_options, $data );
                    }
                }

                switch ( $quiz_pass_score_type ) {
                    case 'point':
                        if($correct_answered_count >= $pass_score_count){
                            $score_message = $pass_score_message;
                        }else{
                            $score_message = $fail_score_message;
                        }
                        break;
                    
                    case 'percentage':
                    default:
                        if ($final_score >= $pass_score_count) {
                            $score_message = $pass_score_message;
                        }else{
                            $score_message = $fail_score_message;
                        }
                        break;
                }

                if($chained_quiz_id !== null ){
                    $chained_quiz_data = Quiz_Maker_Data::get_chained_quiz_by_id(intval($chained_quiz_id));

                    $chained_quiz_options = json_decode($chained_quiz_data['options']);
                    if( is_array( $chained_quiz_options ) ){
                        $chained_quiz_options = (object) $chained_quiz_options;
                    }

                    $print_report_table = isset($chained_quiz_options->chained_quizzes_print_report) && $chained_quiz_options->chained_quizzes_print_report == 'on' ? true : false;
                    $calculate_report_type = isset($chained_quiz_options->calculate_report_type) && $chained_quiz_options->calculate_report_type != '' ? $chained_quiz_options->calculate_report_type : 'take_quiz';

                    if($print_report_table){
                        if($calculate_report_type == 'pass_quiz'){

                            switch ( $quiz_pass_score_type ) {
                                case 'point':
                                    if($chained_quiz_see_result && ($correct_answered_count >= $pass_score_count)){
                                        $chain_quiz_button_text = 'seeResult';
                                    }else{
                                        $chain_quiz_button_text = 'nextQuiz';
                                    }
                                    break;
                                
                                case 'percentage':
                                default:
                                    if($chained_quiz_see_result && ($final_score >= $pass_score_count)){
                                        $chain_quiz_button_text = 'seeResult';
                                    }else{
                                        $chain_quiz_button_text = 'nextQuiz';
                                    }
                                    break;
                            }

                        }else{
                            if($chained_quiz_see_result){
                                $chain_quiz_button_text = 'seeResult';
                            }else{
                                $chain_quiz_button_text = 'nextQuiz';
                            }
                        }
                    }else{
                        if($calculate_report_type == 'pass_quiz'){
                            

                            switch ( $quiz_pass_score_type ) {
                                case 'point':
                                    if($chained_quiz_see_result && $correct_answered_count >= $pass_score_count){
                                        $chain_quiz_button_text = '';
                                    }else{
                                        $chain_quiz_button_text = 'nextQuiz';
                                    }
                                    break;
                                
                                case 'percentage':
                                default:
                                    if($chained_quiz_see_result && $final_score >= $pass_score_count){
                                        $chain_quiz_button_text = '';
                                    }else{
                                        $chain_quiz_button_text = 'nextQuiz';
                                    }
                                    break;
                            }
                        }else{
                            if(!$chained_quiz_see_result){
                                $chain_quiz_button_text = 'nextQuiz';
                            }else{
                                $chain_quiz_button_text = '';
                            }
                        }
                    }

                }

                $final_score_message = "";
                if($pass_score_count > 0){
                    $final_score_message = Quiz_Maker_Data::replace_message_variables($score_message, $message_data);
                }

                $finish_text = (isset($options->final_result_text) && $options->final_result_text != '') ? Quiz_Maker_Data::ays_autoembed( $options->final_result_text ) : '';
                $finish_text = Quiz_Maker_Data::replace_message_variables($finish_text, $message_data);

                $heading_for_share_buttons = '';
                if( isset($options->enable_social_buttons) && $options->enable_social_buttons ){
                    $heading_for_share_buttons = isset( $options->social_buttons_heading ) ? $options->social_buttons_heading : "";
                    $heading_for_share_buttons = Quiz_Maker_Data::replace_message_variables($heading_for_share_buttons, $message_data);
                    $heading_for_share_buttons = Quiz_Maker_Data::ays_autoembed($heading_for_share_buttons);
                }

                $heading_for_social_links = '';
                if( isset($options->enable_social_links) && $options->enable_social_links ){
                    if( isset( $options->social_links_heading ) && $options->social_links_heading != "" ){
                        $heading_for_social_links = isset( $options->social_links_heading ) ? $options->social_links_heading : "";
                        $heading_for_social_links = Quiz_Maker_Data::replace_message_variables($heading_for_social_links, $message_data);
                        $heading_for_social_links = Quiz_Maker_Data::ays_autoembed($heading_for_social_links);
                    }

                }

                if(isset($_SESSION['ays_quiz_paypal_purchased_item']) && isset( $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id] ) ){
                    $wpdb->update(
                        $wpdb->prefix . 'aysquiz_orders',
                        array( 'status' => 'finished' ),
                        array( 'id' => absint( $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['order_id'] ) ),
                        array( '%s' ),
                        array( '%d' )
                    );
                }
                if(isset($_SESSION['ays_quiz_stripe_purchased_item']) && isset( $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id] ) ){
                    $wpdb->update(
                        $wpdb->prefix . 'aysquiz_orders',
                        array( 'status' => 'finished' ),
                        array( 'id' => absint( $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['order_id'] ) ),
                        array( '%s' ),
                        array( '%d' )
                    );
                }

                if(isset($_SESSION['ays_quiz_paypal_purchase']) && isset( $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] ) ){
                    if ( $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] ) {
                        $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                    }
                    $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] = false;
                    unset($_SESSION['ays_quiz_paypal_purchase'][$quiz_id]);
                }
                if ( (isset($_SESSION) && is_array($_SESSION)) && (isset($_SESSION['ays_quiz_paypal_purchase']) && is_array($_SESSION['ays_quiz_paypal_purchase'])) ) {
                    if(array_key_exists('ays_quiz_paypal_purchase', $_SESSION) && array_key_exists($quiz_id, $_SESSION['ays_quiz_paypal_purchase'])){
                        if ( $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] ) {
                            $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                        }
                        $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] = false;
                        unset($_SESSION['ays_quiz_paypal_purchase'][$quiz_id]);
                    }
                }
                if(isset($_SESSION['ays_quiz_paypal_purchased_item']) && isset( $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id] ) ){
                    if ( $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] == "created" ) {
                        $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                    }
                    $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] = 'finished';
                    unset($_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]);
                }
                if ( (isset($_SESSION) && is_array($_SESSION)) && (isset($_SESSION['ays_quiz_paypal_purchased_item']) && is_array($_SESSION['ays_quiz_paypal_purchased_item'])) ) {
                    if(array_key_exists('ays_quiz_paypal_purchased_item', $_SESSION) && array_key_exists($quiz_id, $_SESSION['ays_quiz_paypal_purchased_item'])){
                        if ( $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] == "created" ) {
                            $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                        }
                        $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] = 'finished';
                        unset($_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]);
                    }
                }

                if(isset($_SESSION['ays_quiz_stripe_purchase']) && isset( $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] ) ){
                    if ( $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] ) {
                        $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                    }
                    $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] = false;
                    unset($_SESSION['ays_quiz_stripe_purchase'][$quiz_id]);
                }
                if ( (isset($_SESSION) && is_array($_SESSION)) && (isset($_SESSION['ays_quiz_stripe_purchase']) && is_array($_SESSION['ays_quiz_stripe_purchase'])) ) {
                    if(array_key_exists('ays_quiz_stripe_purchase', $_SESSION) && array_key_exists($quiz_id, $_SESSION['ays_quiz_stripe_purchase'])){
                        if ( $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] ) {
                            $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                        }
                        $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] = false;
                        unset($_SESSION['ays_quiz_stripe_purchase'][$quiz_id]);
                    }
                }
                if(isset($_SESSION['ays_quiz_stripe_purchased_item']) && isset( $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id] ) ){
                    if ( $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] == "created" ) {
                        $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                    }
                    $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] = 'finished';
                    unset($_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]);
                }
                if ( (isset($_SESSION) && is_array($_SESSION)) && (isset($_SESSION['ays_quiz_stripe_purchased_item']) && is_array($_SESSION['ays_quiz_stripe_purchased_item'])) ) {
                    if(array_key_exists('ays_quiz_stripe_purchased_item', $_SESSION) && array_key_exists($quiz_id, $_SESSION['ays_quiz_stripe_purchased_item'])){
                        if ( $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] == "created" ) {
                            $paid_res = $this->ays_store_result_prepay_payed( $last_result_id );
                        }
                        $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] = 'finished';
                        unset($_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]);
                    }
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
                            $get_product_name_data = $wpf->get_product($_value)->get_data();
                            $get_product_name = '';
                            if ( isset( $get_product_name_data ) && is_array( $get_product_name_data ) ) {
                                $get_product_name = (isset($get_product_name_data['name']) && $get_product_name_data['name']) ? $get_product_name_data['name'] : "";
                            }

                            $product[] = array(
                                'prodUrl'  => get_permalink(intval($_value)),
                                'name'  => $get_product_name,
                                'image' => wp_get_attachment_image_src(get_post_thumbnail_id($_value), 'single-post-thumbnail')[0],
                                'link'  => "<a href=\"?add-to-cart=$_value\" data-quantity=\"1\" class=\"action-button product_type_simple add_to_cart_button ajax_add_to_cart\" data-product_id=\"$_value\" data-product_sku=\"\" aria-label=\"$cart_text\" rel=\"nofollow\">$cart_text</a>"
                            );
                        }
                    }

                    $show_interval_message     = Quiz_Maker_Data::ays_quiz_translate_content( $show_interval_message );
                    $final_score_message       = Quiz_Maker_Data::ays_quiz_translate_content( $final_score_message );
                    $interval_message          = Quiz_Maker_Data::ays_quiz_translate_content( $interval_message );
                    $finish_text               = Quiz_Maker_Data::ays_quiz_translate_content( $finish_text );
                    $heading_for_share_buttons = Quiz_Maker_Data::ays_quiz_translate_content( $heading_for_share_buttons );
                
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();
                    echo json_encode(array(
                        "status" => true,
                        "cert_file_name" => isset( $data['cert_file_name'] ) ? $data['cert_file_name'] : null,
                        "hide_result" => false,
                        "showIntervalMessage" => $show_interval_message,
                        "score" => $score,
                        "scoreMessage" => $final_score_message,
                        "displayScore" => $display_score,
                        "finishText" => $finish_text,
                        "conditionData" => $conditions_data,
                        "product" => $product,
                        "intervalMessage" => $interval_message,
                        "mail" => $ays_send_mail,
                        "mail_to_admin" => $ays_send_mail_to_admin,
                        "admin_mail" => $admin_mails,
                        "result_id" => $last_result_id,
                        "result" => $result_id,
                        'interval_redirect_url' => $interval_redirect_url,
                        "interval_redirect_delay" => $interval_redirect_delay,
                        "interval_redirect_after" => $interval_redirect_after,
                        "chain_quiz_button_text" => $chain_quiz_button_text,
                        "socialHeading" => $heading_for_share_buttons,
                        "socialLinksHeading"    => $heading_for_social_links,
                        "unique_code" => $result_unique_code,
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

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $quiz_enable_lazy_loading = (isset($options['quiz_enable_lazy_loading']) && $options['quiz_enable_lazy_loading'] === true) ? true : false;
        $question_key_number = $options['key_number'];
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        $class_label_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
            $class_label_for_keyboard = "ays-quiz-keyboard-label";
        }

        $answer_container_script    = '';
        $answer_container_script_html = '';
        $script_data_arr = array();
        $question_answer = array();
        if ( $options["questionType"] == 'checkbox' ) {

            $enable_max_selection_number = ( isset( $options['enable_max_selection_number'] ) && $options["enable_max_selection_number"] == 'on' ) ? true : false;
            $max_selection_number        = ( isset( $options["max_selection_number"] ) && $options["max_selection_number"] != '' ) ? absint($options["max_selection_number"]) : '';

            $enable_min_selection_number = ( isset( $options['enable_min_selection_number'] ) && $options["enable_min_selection_number"] == 'on' ) ? true : false;
            $min_selection_number        = ( isset( $options["min_selection_number"] ) && $options["min_selection_number"] != '' ) ? absint($options["min_selection_number"]) : '';

            if ( ( $enable_max_selection_number && ! empty( $max_selection_number ) && $max_selection_number != 0 ) || ( $enable_min_selection_number && ! empty( $min_selection_number ) && $min_selection_number != 0 ) ) {

                $script_data_arr['enable_max_selection_number'] = $enable_max_selection_number;
                $script_data_arr['max_selection_number'] = $max_selection_number;
                $script_data_arr['enable_min_selection_number'] = $enable_min_selection_number;
                $script_data_arr['min_selection_number'] = $min_selection_number;
            }
        }

        foreach ($answers as $key => $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
                //$ans_img = Quiz_Maker_Data::ays_get_image_thumbnauil($answer['image']);
                $ans_img = $answer['image'];
                $answer_image_alt_text = Quiz_Maker_Data::ays_quiz_get_image_id_by_url($ans_img);

                $question_image_lazy_loading_attr = "";
                if ( $quiz_enable_lazy_loading ) {
                    if( $question_key_number != 0 ){
                        $question_image_lazy_loading_attr = 'loading="lazy"';
                    }
                }

                $answer_image = "<img src='{$ans_img}' alt='". $answer_image_alt_text ."' ". $question_image_lazy_loading_attr ." class='ays-answer-image'>";
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

                $answer_content = stripslashes( $answer_content );
                $answer_content = do_shortcode( $answer_content );
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $question_answer[ $answer["id"] ] = htmlspecialchars_decode(stripslashes($answer["correct"]), ENT_QUOTES);

            $correct_answer_flag = 'ays_answer_image_class';
            if( $answer["correct"] == 1 ){
                $correct_answer_flag = 'ays_anser_image_class';
            }

            $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='$answer_label_class $answer_img_label_class $class_label_for_keyboard'>" . $numering_value . $answer_content . "</label>";
            $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='ays_answer_image {$correct_answer_flag} ays_empty_before_content'>{$answer_image}</label>";

            $answer_container .= "
            <div class='ays-field ays_" . $options['answersViewClass'] . "_view_item ".$class_for_keyboard."' ".$attributes_for_keyboard.">
                <input type='hidden' name='ays_answer_correct[]' value='0'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                {$label}

            </div>";

        }

        $script_data_arr['question_answer'] = $question_answer;

        $answer_container_script_html .= '<script>';
        $answer_container_script_html .= "
            if(typeof window.quizOptions_$quiz_id === 'undefined'){
                window.quizOptions_$quiz_id = [];
            }
            window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode($script_data_arr)) . "';";
        $answer_container_script_html .= '</script>';

        $answer_container .= $answer_container_script_html;
        
        return $answer_container;
    }
    
    protected function ays_text_answer_html($question_id, $quiz_id, $answers, $options){        
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $is_enable_question_max_length = Quiz_Maker_Data::ays_quiz_is_enable_question_max_length( $question_id, 'text' );

        $question_not_influence_class  = "";
        if ( Quiz_Maker_Data::is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
        }

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

        // Enable case sensitive text
        $enable_case_sensitive_text = ( isset($options['enable_case_sensitive_text']) && $options['enable_case_sensitive_text'] != '' ) ? $options['enable_case_sensitive_text'] : false;

        $answer_container = "<div class='ays-field ays-text-field' tabindex='0'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<textarea type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' autocomplete='off' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'></textarea>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ".$class_for_keyboard." ". $question_not_influence_class ."'>". $this->buttons_texts['checkButton'] ."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars_decode(stripslashes($answer["answer"]), ENT_QUOTES),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message,
                            'enable_case_sensitive_text' => $enable_case_sensitive_text,
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

        $question_not_influence_class  = "";
        if ( Quiz_Maker_Data::is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
        }

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class        = '';
        $ays_quiz_question_number_message_html  = '';
        $question_number_min_message_html       = '';
        $question_number_error_message          = '';
        $question_number_error_message_html     = '';

        $enable_question_number_max_length      = false;
        $enable_question_number_min_length      = false;
        $enable_question_number_error_message   = false;

        $question_number_max_length = '';
        $question_number_min_length = '';

        if (! empty($question_text_max_length_array) ) {

            $enable_question_number_max_length      = $question_text_max_length_array['enable_question_number_max_length'];
            $question_number_max_length             = $question_text_max_length_array['question_number_max_length'];

            $enable_question_number_min_length      = $question_text_max_length_array['enable_question_number_min_length'];
            $question_number_min_length             = $question_text_max_length_array['question_number_min_length'];

            $enable_question_number_error_message   = $question_text_max_length_array['enable_question_number_error_message'];
            $question_number_error_message          = $question_text_max_length_array['question_number_error_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ($question_number_max_length != 0 && $question_number_max_length != '') {
                $ays_quiz_question_number_message_html .= 'max="'. $question_number_max_length .'"';
            }
        }

        if ( $enable_question_number_min_length ) {
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ($question_number_min_length != 0 && $question_number_min_length != '') {
                $question_number_min_message_html .= 'min="'. $question_number_min_length .'"';
            }
        }

        if ( $enable_question_number_error_message ) {
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ( $question_number_error_message != "" ) {
                $question_number_error_message_html .= "<div class='ays-quiz-number-error-message ays_display_none'>";
                    $question_number_error_message_html .= $question_number_error_message;
                $question_number_error_message_html .= "</div>";
            }
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='number' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' ". $ays_quiz_question_number_message_html ." ". $question_number_min_message_html ." name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $class_for_keyboard." ". $question_not_influence_class ."'>". $this->buttons_texts['checkButton'] ."</button>";

                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"])),
                            'enable_question_number_max_length' => $enable_question_number_max_length,
                            'question_number_max_length' => $question_number_max_length,
                            'enable_question_number_min_length' => $enable_question_number_min_length,
                            'question_number_min_length' => $question_number_min_length,
                            'enable_question_number_error_message' => $enable_question_number_error_message,
                            'question_number_error_message' => $question_number_error_message,
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";

        $answer_container .= $question_number_error_message_html;

        return $answer_container;
    }

    protected function ays_date_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $question_not_influence_class  = "";
        if ( Quiz_Maker_Data::is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='date' autocomplete='off' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $class_for_keyboard." ". $question_not_influence_class ."'>". $this->buttons_texts['checkButton'] ."</button>";
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

        $question_not_influence_class  = "";
        if ( Quiz_Maker_Data::is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
        }

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

        // Enable case sensitive text
        $enable_case_sensitive_text = ( isset($options['enable_case_sensitive_text']) && $options['enable_case_sensitive_text'] != '' ) ? $options['enable_case_sensitive_text'] : false;

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' autocomplete='off' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $class_for_keyboard." ". $question_not_influence_class ."'>". $this->buttons_texts['checkButton'] ."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'short_text',
                            'question_answer' => htmlspecialchars_decode(stripslashes($answer["answer"]), ENT_QUOTES),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message,
                            'enable_case_sensitive_text' => $enable_case_sensitive_text,
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";
        $answer_container .= $ays_quiz_question_text_message_html;

        return $answer_container;
    }

    protected function ays_fill_in_blank_answer_html($question,$question_id, $quiz_id, $answers, $options){

        if( $question == "" ){
            return;
        }

        $enable_correction = isset($options['correction']) ? "display:inline-block;white-space: nowrap;" : "display:none";
        $is_enable_question_max_length = Quiz_Maker_Data::ays_quiz_is_enable_question_max_length( $question_id, 'short_text' );

        $question_not_influence_class  = "";
        if ( Quiz_Maker_Data::is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
        }

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
        }

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

        // Enable case sensitive text
        $enable_case_sensitive_text = ( isset($options['enable_case_sensitive_text']) && $options['enable_case_sensitive_text'] != '' ) ? $options['enable_case_sensitive_text'] : false;

        $question_answer = array();
        $question_answer_input = array();

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $slug = isset($answer["slug"]) && $answer["slug"] != '' ? stripslashes(htmlentities($answer["slug"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_id = (isset($answer['id']) && $answer['id'] != '') ? $answer["id"] : "";

                if( $slug == "" ){
                    continue;
                }

                $answer_html = "<input type='text' placeholder='$placeholder' class='ays-text-input ays-quiz-fill-in-blank-input ". $ays_question_limit_length_class ."' autocomplete='off' name='ays_questions[ays-question-{$question_id}][{$answer_id}]' data-question-id='". $question_id ."' data-answer-id='". $answer_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <input type='hidden' name='ays_question_answers[{$question_id}][]' value='{$answer_id}'/>";

                $question_answer_input[ $slug ] = $answer_html;

                $question = str_replace( $slug ,$answer_html, $question);

                // <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $class_for_keyboard." ". $question_not_influence_class ."'>". $this->buttons_texts['checkButton'] ."</button>";

                $question_answer[ $answer["id"] ] = htmlspecialchars_decode(stripslashes($answer["answer"]), ENT_QUOTES);
            }

            $answer_container_script = "<script>
                    if(typeof window.quizOptions_$quiz_id === 'undefined'){
                        window.quizOptions_$quiz_id = [];
                    }
                    window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                        'question_type' => 'fill_in_blank',
                        'question_answer' => $question_answer,
                        'enable_question_text_max_length' => $enable_question_text_max_length,
                        'question_text_max_length' => $question_text_max_length,
                        'question_limit_text_type' => $question_limit_text_type,
                        'question_enable_text_message' => $question_enable_text_message,
                        'enable_case_sensitive_text' => $enable_case_sensitive_text,
                    ))) . "';
                </script>";
        
        $answer_container .= "</div>";

        $question .= $answer_container_script;

        return $question;
    }

    protected function ays_dropdown_answer_html($question_id, $quiz_id, $answers, $options){
        
        $answer_container = "<div class='ays-field ays-select-field'>            
            <select class='ays-select' tabindex='0'>                
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

    protected function ays_matching_answer_html($question_id, $quiz_id, $answers, $options){

        $answer_container = "<div class='ays-matching-field'>";
        $show_answers_numbering = $options['show_answers_numbering'];
        $answer_incorrect_matches = $options['answer_incorrect_matches'];
        $numering_type = Quiz_Maker_Data::ays_answer_numbering( $show_answers_numbering );
        $total = $options['total_questions'];
        $show_questions_numbering = $options['show_questions_numbering'];
        $question_numering_type = Quiz_Maker_Data::ays_question_numbering($show_questions_numbering, $total);

        $matches = array();
        foreach ($answers as $answer) {
            $answer_options = isset( $answer['options'] ) && ! empty( $answer['options'] ) ? $answer['options'] : '';
            $answer_options = json_decode( $answer_options, true );
            if ( ! $answer_options ) {
                $answer_options = array();
            }

            $correct_match = isset( $answer_options['correct_match'] ) && !empty( $answer_options['correct_match'] ) ? $answer_options['correct_match'] : '';
            if( $correct_match !== '' ) {
                $matches[] = array(
                    'id' => $answer['id'],
                    'answer' => $correct_match
                );
            }
        }

        foreach ( $answer_incorrect_matches as $key => $match ) {
            if( $match !== '' ) {
                $matches[] = array(
                    'id' => $key,
                    'answer' => $match
                );
            }
        }

        shuffle($matches);

        foreach ($answers as $key => $answer) {

            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
                //$answer_image = Quiz_Maker_Data::ays_get_image_thumbnauil($answer['image']);
                $answer_image = $answer['image'];
            }

            $question_numering_value = "";
            if( isset( $question_numering_type[$key] ) && $question_numering_type[$key] != '' ) {
                $question_numering_value = $question_numering_type[$key] . " ";
            }

            $answer_container .= "<div class='ays-field ays-matching-field-option'>
                <div class='ays-matching-field-choice'>
                    " . $question_numering_value . do_shortcode(htmlspecialchars(stripslashes($answer["answer"]))) . "
                </div>
                <div class='ays-matching-field-match' data-answer-id='". $answer['id'] ."'>
                    <select class='ays-select' tabindex='0'>
                        <option value=''>".__('Select an answer', $this->plugin_name)."</option>";

                        foreach ($matches as $k => $match) {
                            $numering_value = "";
                            if( isset( $numering_type[$k] ) && $numering_type[$k] != '' ){
                                $numering_value = $numering_type[$k] . " ";
                            }

                            $answer_text = do_shortcode(htmlspecialchars(stripslashes($match['answer'])));
                            $answer_container .= "<option data-nkar='{$answer_image}' value='". esc_attr($answer_text) ."'>" . $numering_value . $answer_text . "</option>";
                        }

                $answer_container .= "</select>";
                $answer_container .= "<input class='ays-select-field-value' type='hidden' name='ays_questions[ays-question-{$question_id}][{$answer["id"]}]' value=''/>";
                $answer_container .= "</div>";
            $answer_container .= "</div>";
        }

        foreach ($answers as $answer) {
            $answer_container .= "<input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>";
        }

        $script_options = array(
            'question_type' => 'matching',
            'question_answer' => array(),
                 // 'enable_question_text_max_length' => $enable_question_text_max_length,
                 // 'question_text_max_length' => $question_text_max_length,
                 // 'question_limit_text_type' => $question_limit_text_type,
                 // 'question_enable_text_message' => $question_enable_text_message,
                 // 'enable_case_sensitive_text' => $enable_case_sensitive_text,
        );

        foreach ($matches as $k => $match) {
            $answer_text = htmlspecialchars_decode(stripslashes($match["answer"]), ENT_QUOTES);
            $script_options['question_answer'][ $answer_text ] = $match['id'];
        }

        $answer_container .= "<script>
            if(typeof window.quizOptions_$quiz_id === 'undefined'){
                window.quizOptions_$quiz_id = [];
            }
            window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode($script_options)) . "';
        </script>";

        $answer_container .= "</div>";

        return $answer_container;
    }


    protected function isHomogenousStrong($arr, $question_id, $type = 'default'){
        $arr_count = count( $arr );
        $arr_sum = array_sum( $arr );

        if ( $type === 'matching' ) {
            return $arr_sum / $arr_count;
        } elseif( $type === 'fill_in_blank' ){
            $count_correct = $arr_count;
        } else {
            $count_correct = intval( Quiz_Maker_Data::count_multiple_correct_answers($question_id) );
        }

        $a = $arr_count - $arr_sum;
        $b = $arr_sum - $a;
        
        return $b / $count_correct;
    }
    
    protected function isHomogenous($arr, $question_id, $type = 'default'){
        $mustBe = true;
        $count = 0;
        foreach ($arr as $val) {
            if ($mustBe !== $val) {
                return false;
            }
            $count++;
        }

        if( $type === 'fill_in_blank' ){
            if($count !== count( $arr )){
                return false;
            }
        } elseif( $type === 'default' ) {
            $count_correct = intval( Quiz_Maker_Data::count_multiple_correct_answers($question_id) );
            if($count !== $count_correct){
                return false;
            }
        }

        return true;
    }

    public function ays_store_result_prepay_payed( $res_id ){

        $res_id = isset($res_id) && $res_id != null && $res_id > 0 ? absint( sanitize_text_field($res_id) ) : 0;
        if($res_id !== 0){
            global $wpdb;
            $results_table = $wpdb->prefix . 'aysquiz_reports';
            $res = $wpdb->update(
                $results_table,
                array( "paid" => 1 ),
                array( "id" => $res_id ),
                array( "%d" ),
                array( "%d" )
            );
        }
    }

    public function ays_store_result_payed(){
        Quiz_Maker_iFrame::headers_for_ajax();

        error_reporting(0);
        $res_id = isset($_REQUEST['res_id']) ? absint(intval($_REQUEST['res_id'])) : 0;
        if($res_id !== 0){
            global $wpdb;
            $results_table = $wpdb->prefix . 'aysquiz_reports';
            $res = $wpdb->update(
                $results_table,
                array( "paid" => 1 ),
                array( "id" => $res_id ),
                array( "%d" ),
                array( "%d" )
            );
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode( array( "status" => true ) );
        wp_die();
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
        $questions_title_arr = isset($data['questions_title']) ? $data['questions_title'] : array();

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
        $options['questions_title'] = $questions_title_arr;
        $options['answers_keyword_counts'] = isset($data['mv_keywords_counts']) && !empty($data['mv_keywords_counts']) ? $data['mv_keywords_counts'] : array();
        $options['quiz_coupon'] = (isset($data['quiz_coupon']) && $data['quiz_coupon'] != '') ? $data['quiz_coupon'] : '';

        $unique_id = "";
        if( isset($data['chained_quiz_id']) && $data['chained_quiz_id'] !== null){
            $options['chained_quiz_id'] = $data['chained_quiz_id'];
            $unique_id = $data['chained_quiz_guest_unique_id'];
        }

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
            'unique_id' => $unique_id,
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
            '%s', // unique_id
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

            if ($results >= 0) {
                $res_id = absint(intval($started_status));
                return $res_id;
            }
        }

        if ($results >= 0) {
            $res_id = $wpdb->insert_id;
            return $res_id;
        }

        return false;
    }

    public function ays_get_rate_last_reviews(){
        Quiz_Maker_iFrame::headers_for_ajax();

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
        echo json_encode(array(
            'status'         => true,
            'quiz_rate_html' => $quiz_rate_html
        ));
        wp_die();
    }
    
    public function ays_load_more_reviews(){
        Quiz_Maker_iFrame::headers_for_ajax();

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
        Quiz_Maker_iFrame::headers_for_ajax();

        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $rates_table = $wpdb->prefix . 'aysquiz_rates';

        $user_id = get_current_user_id();

        //$sql = "SELECT * FROM $results_table WHERE id = (SELECT MAX(id) AS max_id FROM $results_table WHERE end_date = ( SELECT MAX(end_date) FROM $results_table ))";//.intval($res[0]['max_id']);
        //$report_result = $wpdb->get_row($sql, ARRAY_A);
        //$report_id = intval($report_result['id']);
        $report_id = (isset($_REQUEST['last_result_id']) && sanitize_text_field($_REQUEST['last_result_id']) != '' && ! is_null( sanitize_text_field($_REQUEST['last_result_id']) ) ) ? intval( sanitize_text_field( $_REQUEST['last_result_id'] ) ) : 0;

        // Make responses anonymous
        $quiz_make_responses_anonymous = (isset($_REQUEST['quiz_make_responses_anonymous']) && sanitize_text_field($_REQUEST['quiz_make_responses_anonymous']) == 'true' ) ? true : false;

        // Make responses anonymous
        $quiz_enable_user_cհoosing_anonymous_assessment = (isset($_REQUEST['quiz_enable_user_cհoosing_anonymous_assessment']) && sanitize_text_field($_REQUEST['quiz_enable_user_cհoosing_anonymous_assessment']) == 'true' ) ? true : false;
        $quiz_enable_user_cհoosing_anonymous_assessment_checkbox_flag = (isset($_REQUEST['quiz_enable_user_cհoosing_anonymous_assessment_checkbox_flag']) && sanitize_text_field($_REQUEST['quiz_enable_user_cհoosing_anonymous_assessment_checkbox_flag']) == 'true' ) ? true : false;

        $user_ip = Quiz_Maker_Data::get_user_ip();
        if(isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != ''){
            $user_name = stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) );
        }elseif(is_user_logged_in()){
            $user = wp_get_current_user();
            $user_name = $user->data->display_name;
        }else{
            $user_name = __( 'Anonymous' , AYS_QUIZ_NAME );
        }
        $user_email =   isset($_REQUEST['ays_user_email']) ? sanitize_email( $_REQUEST['ays_user_email'] ) : '';
        $user_phone = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field( $_REQUEST['ays_user_phone'] ) : '';
        $quiz_id = absint( sanitize_text_field($_REQUEST["quiz_id"]) );
        $score = (isset($_REQUEST['rate_score']) && $_REQUEST['rate_score'] != "") ? esc_sql( absint( sanitize_text_field( $_REQUEST['rate_score'] ) ) ) : 5;
        $rate_date = esc_sql($_REQUEST['rate_date']);
        $rate_reason = (isset($_REQUEST['rate_reason']) && $_REQUEST['rate_reason'] != "") ? stripslashes( sanitize_textarea_field( $_REQUEST['rate_reason'] ) ) : '';

        switch ($score) {
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
                $score = $score;
                break;
            default:
                $score = 5;
                break;
        }

        if ( $quiz_make_responses_anonymous ) {
            $user_id     = 0;
            $user_ip     = '';
            $user_name   = __( 'Anonymous' , AYS_QUIZ_NAME );
            $user_email  = '';
            $user_phone  = '';
        }

        if ( $quiz_enable_user_cհoosing_anonymous_assessment && $quiz_enable_user_cհoosing_anonymous_assessment_checkbox_flag ) {
            $user_id     = 0;
            $user_ip     = '';
            $user_name   = __( 'Anonymous' , AYS_QUIZ_NAME );
            $user_email  = '';
            $user_phone  = '';
        }

        $results = $wpdb->insert(
            $rates_table,
            array(
                'quiz_id'    => $quiz_id,
                'user_id'    => $user_id,
                'report_id'  => $report_id,
                'user_ip'    => $user_ip,
                'user_name'  => $user_name,
                'user_email' => $user_email,
                'user_phone' => $user_phone,
                'score'      => $score,
                'review'     => $rate_reason,
                'options'    => '',
                'rate_date'  => $rate_date,
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
                'score'     => intval(round($score)),
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
        Quiz_Maker_iFrame::headers_for_ajax();

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
        Quiz_Maker_iFrame::headers_for_ajax();

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
        Quiz_Maker_iFrame::headers_for_ajax();

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
        $quiz_options->limit_users = isset($quiz_options->limit_users) ? $quiz_options->limit_users : 'off';
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

    public function ays_quiz_store_user_started() {
        Quiz_Maker_iFrame::headers_for_ajax();

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

        $final_url = "$url/api/3/{$data}s";

        if ($data == "contact") {
            $final_url = "$url/api/3/{$data}/sync";
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $final_url,
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

                $this->ays_add_active_camp_transaction($url, $api_key, $list_args, $list_id, $automation_id, 'contactList');
            }
            if ($automation_id) {
                $automation_args = array(
                    "automation" => $automation_id,
                    "contact"    => $res['id']
                );

                $this->ays_add_active_camp_transaction($url, $api_key, $automation_args, $list_id, $automation_id, 'contactAutomation');
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

        $quiz_attributes['ays_form_name'] = isset($_REQUEST['ays_user_name'])  && $_REQUEST['ays_user_name']  != '' ? sanitize_text_field( $_REQUEST['ays_user_name'] )  : '';
        $quiz_attributes['ays_form_email'] = isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != '' ? sanitize_email( $_REQUEST['ays_user_email'] ) : '';
        $quiz_attributes['ays_form_phone'] = isset($_REQUEST['ays_user_phone']) && $_REQUEST['ays_user_phone'] != '' ? sanitize_text_field( $_REQUEST['ays_user_phone'] ) : '';


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

    public function sortByOrderTopKeywords($a, $b) {

        if( isset($a['keyword_count']) && $a['keyword_count'] !== null ){
            return intval($b['keyword_count']) - intval($a['keyword_count']);
        }elseif( isset($a['keyword_percentage']) && $a['keyword_percentage'] !== null ){
            return intval($b['keyword_percentage']) - intval($a['keyword_percentage']);
        }
    }

    public function ays_quiz_check_user_started_for_paypal() {
        Quiz_Maker_iFrame::headers_for_ajax();

        global $wpdb;
        if( ! session_id() ){
            session_start();
        }
        $order_id = isset( $_REQUEST['order_id'] ) && $_REQUEST['order_id'] != '' ? absint( $_REQUEST['order_id'] ) : 0;
        $quiz_id = isset( $_REQUEST['quiz_id'] ) && $_REQUEST['quiz_id'] != '' ? absint( $_REQUEST['quiz_id'] ) : 0;
        if( $order_id !== 0 ){
            $result = $wpdb->update(
                $wpdb->prefix . 'aysquiz_orders',
                array( 'status' => 'started' ),
                array( 'id' => $order_id ),
                array( '%s' ),
                array( '%d' )
            );

            if ( $result > 0) {
                if (isset($_SESSION['ays_quiz_paypal_purchase']) && isset($_SESSION['ays_quiz_paypal_purchase'][$quiz_id]) && $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] === true) {
                    if (isset($_SESSION['ays_quiz_paypal_purchased_item']) && isset($_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id])) {
                        $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] = "started";

                    }
                }
            }
        }
    }

    public function set_prop( $prop, $value ){
        if( property_exists( $this, $prop ) && ! empty( $value ) ){
            $this->$prop = $value;
        }
    }

    public function get_prop( $prop ){
        if( isset( $this->$prop ) ){
            return $this->$prop;
        }

        return null;
    }

    // Export quiz to pdf
    public function user_export_quiz_questions_pdf() {
        global $wpdb;
        $pdf_response = null;
        $pdf_content  = null;

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'user_export_quiz_questions_pdf') {
            $data_questions = array();
            $data_for_export = $_REQUEST['dataForExport'];
            $export_quiz_answers = $_REQUEST['exportQuizAnswers'];
            foreach ($data_for_export as $key => $value) {
                $quiz_id = (int)$value['quizID'];
            }

            $sql = "SELECT title FROM {$wpdb->prefix}aysquiz_quizes WHERE id = " . $quiz_id;
            $quiz_title = $wpdb->get_row($sql, 'ARRAY_A');

            $data_questions['quiz_title'] = $quiz_title;
            $data_questions['data_questions'] = $data_for_export;
            $data_questions['export_quiz_answers'] = $export_quiz_answers;

            $pdf = new Quiz_PDF_API();
            $export_data = array(
                'status'          => true,
                'type'            => 'pdfapi',
                'api_quiz_id'     => (int)$quiz_id,
                'data'            => $data_questions
            );

            $pdf_response = $pdf->generate_quiz_PDF_public_user($export_data);

            $pdf_content  = $pdf_response['status'];

            if($pdf_content === true){
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode($pdf_response);
            }else{
                $export_data = array(
                    'status' => false,
                );
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode($export_data);
            }
            wp_die();
        }
    }

    public function ays_quiz_get_submission_results_by_unique_code () {
        global $wpdb;
        $unique_code = isset($_GET['uniquecode']) && $_GET['uniquecode'] != "" ? sanitize_text_field($_GET['uniquecode']) : "";

        $sql = "SELECT * FROM `{$wpdb->prefix}aysquiz_reports` WHERE `unique_code` = '" . $unique_code . "'";
        $results = $wpdb->get_row($sql, 'ARRAY_A');
        if ($results) {
            return json_encode($results);
        }
        
        return null;
    }

    public function ays_quiz_send_question_report() {
        global $wpdb;

        $question_id = isset( $_REQUEST['question_id'] ) && $_REQUEST['question_id'] != '' ? absint( $_REQUEST['question_id'] ) : 0;
        $quiz_id = isset( $_REQUEST['quiz_id'] ) && $_REQUEST['quiz_id'] != '' ? absint( $_REQUEST['quiz_id'] ) : 0;
        $report_text = isset( $_REQUEST['report_text'] ) && $_REQUEST['report_text'] != '' ? sanitize_textarea_field( $_REQUEST['report_text'] ) : '';
        $create_date = isset($_REQUEST['create_date']) && $_REQUEST['create_date'] != '' ? $_REQUEST['create_date'] : NULL;
        $send_email = isset($_REQUEST['send_email']) && $_REQUEST['send_email'] == 'on' ? true : false;

        if( $question_id !== 0 && $report_text !== ''){
            $question_reports_table = $wpdb->prefix . 'aysquiz_question_reports';
            $data = array(
                'question_id' => $question_id,
                'report_text' => nl2br( stripslashes($report_text) ),
                'resolved' => 0,
                'create_date' => $create_date,
                'resolve_date' => NULL
            );
            $format = array( '%d', '%s', '%d', '%s', '%s' );

            $result = $wpdb->insert( $question_reports_table, $data, $format );

            if($result) {
                echo json_encode(array(
                    'status' => true,
                ));
            } else {
                echo json_encode(array(
                    'status' => false,
                ));
            }
        } else{
            echo json_encode(array(
                'status' => false,
            ));
        }

        if ($send_email) {
            $sql = "SELECT author_id FROM `{$wpdb->prefix}aysquiz_quizes` WHERE id = {$quiz_id}";
            $author_id_data = $wpdb->get_var($sql);
            
            $quiz_current_author = isset($author_id_data) && $author_id_data != "" ? absint( $author_id_data ) : 0;

            $current_quiz_author = "";
            $current_quiz_author_email = "";

            $current_quiz_user_data = get_userdata( $quiz_current_author );
            if ( ! is_null( $current_quiz_user_data ) && $current_quiz_user_data ) {
                $current_quiz_author = ( isset( $current_quiz_user_data->data->display_name ) && $current_quiz_user_data->data->display_name != '' ) ? sanitize_text_field( $current_quiz_user_data->data->display_name ) : "";
                $current_quiz_author_email = ( isset( $current_quiz_user_data->data->user_email ) && $current_quiz_user_data->data->user_email != '' ) ? sanitize_text_field( $current_quiz_user_data->data->user_email ) : "";
            }

            if (filter_var( trim( $current_quiz_author_email ), FILTER_VALIDATE_EMAIL)) {
                $to = $current_quiz_author_email;
                $nsite_url_base = get_site_url();
                $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
                $nsite_url = trim( $nsite_url_replaced, '/' );
                $nfrom = "From:Quiz Maker<quiz_maker@".$nsite_url.">";
                $reply_to = "Reply-To: " . $to;

                $subject = __( "You have a new Question Report", $this->plugin_name );
                
                if (!empty($current_quiz_author)) {
                    $admin_name = $current_quiz_author;
                } else {
                    $admin_name = __('Admin', $this->plugin_name);
                }

                $current_page_url = isset($_SERVER['HTTP_REFERER']) ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : '';

                $message = sprintf(
                    __( "%s Dear %s. You have a new Report for the Question with ID %s. %s Here is the Report Message: %s The Report is received from the Quiz, the shortcode of which is inserted into the following URL: %s", $this->plugin_name ),
                    "<p>",
                    $admin_name,
                    $question_id,
                    "</p><p>",
                    $report_text . "</p><p>",
                    $current_page_url . "</p>"
                );

                $headers = $nfrom."\r\n";
                $headers .= $reply_to . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $attachments = array();

                wp_mail( $to, $subject, $message, $headers, $attachments );
            }
        }

        wp_die();
    }

    public function ays_quiz_front_end_login_fail( $username ) {
        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
        }else{
            $settings_options = array();
        }

        // Enable custom login form redirect if user fail
        $settings_options['quiz_enable_custom_login_form_redirect'] = (isset( $settings_options['quiz_enable_custom_login_form_redirect'] ) && $settings_options['quiz_enable_custom_login_form_redirect'] == 'on') ? sanitize_text_field( $settings_options['quiz_enable_custom_login_form_redirect'] ) : 'off';
        $quiz_enable_custom_login_form_redirect = (isset( $settings_options['quiz_enable_custom_login_form_redirect'] ) && $settings_options['quiz_enable_custom_login_form_redirect'] == 'on') ? true : false;

        // Custom login form link
        $quiz_custom_login_form_redirect_link = (isset($settings_options['quiz_custom_login_form_redirect_link']) && $settings_options['quiz_custom_login_form_redirect_link'] != '') ? stripslashes( esc_url( $settings_options['quiz_custom_login_form_redirect_link'] ) ) : '';

        if( $quiz_enable_custom_login_form_redirect ){

            $referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];

            if( !empty( $quiz_custom_login_form_redirect_link ) ){
                $referrer = $quiz_custom_login_form_redirect_link;
            }

            $referrer = add_query_arg('result', 'failed', $referrer);
            
            if(!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) :
                wp_redirect($referrer);
                exit;
            endif;
        }

        // $referrer = add_query_arg('username', $username, $referrer);

    }

    public function ays_quiz_authenticate_login( $user, $username, $password ) {

        if(is_wp_error($user)) :
            if( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) && isset( $_REQUEST['wp-submit'] ) ):
                $codes = $user->get_error_codes();
                $messages = $user->get_error_messages();

                $user = new WP_Error;

                for($i = 0; $i <= count($codes) - 1; $i++) :

                    $code = $codes[$i];
                    if(in_array($code, array('empty_username', 'empty_password'))) :
                        $code = 'ays_quiz_' . $code;
                    endif;

                    $user->add($code, $messages[$i]);

                endfor;
            endif;
        endif;

        return $user;
    }


}
