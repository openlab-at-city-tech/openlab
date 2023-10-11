<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;


    private $quizes_obj;
    private $quiz_categories_obj;
    private $questions_obj;
    private $question_categories_obj;
    private $results_obj;
    private $each_result_obj;
    private $orders_obj;
    private $settings_obj;
    private $all_results_obj;
    private $all_reviews_obj;
    private $not_finished_result_obj;
    private $question_tags_obj;
    private $question_reports_obj;
    private $attributes_obj;

    private $current_user_can_edit;

    private $capability;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
        $per_page_array = array(
            'quizes_per_page',
            'questions_per_page',
            'quiz_categories_per_page',
            'question_categories_per_page',
            'attributes_per_page',
            'quiz_results_per_page',
            'quiz_each_results_per_page',
            'quiz_orders_per_page',
            'quiz_all_results_per_page',
            'quiz_all_reviews_per_page',
        );
        foreach($per_page_array as $option_name){
            add_filter('set_screen_option_'.$option_name, array(__CLASS__, 'set_screen'), 10, 3);
        }

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix){
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-sweetalert-css', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all');
        if (false === strpos($hook_suffix, $this->plugin_name))
            return;
        wp_enqueue_style('wp-color-picker');
        // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
        // wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        // wp_enqueue_style( 'jquery-ui' );
        
        wp_enqueue_style($this->plugin_name . '-animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-animations.css', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-font-awesome', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-qm-select2', AYS_QUIZ_PUBLIC_URL .  '/css/quiz-maker-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-jquery-datetimepicker', plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-admin.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-loaders", plugin_dir_url(__FILE__) . 'css/loaders.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style( $this->plugin_name . "-affiliate", plugin_dir_url( __FILE__ ) . 'css/quiz-maker-affiliate.css', array(), time(), 'all' );

        $quiz_page = explode('_', $hook_suffix);
        $quiz_page = $quiz_page[count($quiz_page)-1];
        $quiz_page = explode('-', $quiz_page);
        if (isset($quiz_page[2])){
            return;
        }
        wp_enqueue_style($this->plugin_name . "-public", AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-public.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-public1", AYS_QUIZ_PUBLIC_URL . '/css/theme_elegant_dark.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-public2", AYS_QUIZ_PUBLIC_URL . '/css/theme_elegant_light.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-public3", AYS_QUIZ_PUBLIC_URL . '/css/theme_rect_dark.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-public4", AYS_QUIZ_PUBLIC_URL . '/css/theme_rect_light.css', array(), time()/*$this->version*/, 'all');


    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix){
        
        if (false !== strpos($hook_suffix, "plugins.php")){
            wp_enqueue_script($this->plugin_name . '-sweetalert-js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name . '-admin',  'quiz_maker_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
        }
        
        if (false === strpos($hook_suffix, $this->plugin_name))
            return;
        
        /* 
        ========================================== 
            Scripts for charts 
        ========================================== 
        */
        if (strpos($hook_suffix, 'results') || strpos($hook_suffix, 'orders') || strpos($hook_suffix, 'each-result')) {
            wp_enqueue_script($this->plugin_name.'-apm-charts-core', plugin_dir_url(__FILE__) . 'js/core.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name.'-apm-charts-main', plugin_dir_url(__FILE__) . 'js/charts.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name.'-apm-charts-animated', plugin_dir_url(__FILE__) . 'js/animated.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name.'-chart1', plugin_dir_url(__FILE__) . 'js/quiz-maker-charts.js', array('jquery'), $this->version, true);

            wp_localize_script( $this->plugin_name.'-chart1', 'AysQuizQuestionChartObj', array(
                'completes'         => __( "Completes", $this->plugin_name ),
                'count'             => __( "Count", $this->plugin_name ),
                'interval'          => __( "Interval", $this->plugin_name ),
                'users'             => __( "Users", $this->plugin_name ),
                'category'          => __( "Category", $this->plugin_name ),
                'percent'           => __( "Percent", $this->plugin_name ),
                'users2'            => __( "users", $this->plugin_name ),
                'guest'             => __( "Guest", $this->plugin_name ),
                'loggedInUsers'     => __( "Logged in user", $this->plugin_name ),
                'keyword'           => __( "Keyword", $this->plugin_name ),
            ) );
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_media();
        wp_enqueue_script( $this->plugin_name . '-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), $this->version, true );
        $color_picker_strings = array(
            'clear'            => __( 'Clear', $this->plugin_name ),
            'clearAriaLabel'   => __( 'Clear color', $this->plugin_name ),
            'defaultString'    => __( 'Default', $this->plugin_name ),
            'defaultAriaLabel' => __( 'Select default color', $this->plugin_name ),
            'pick'             => __( 'Select Color', $this->plugin_name ),
            'defaultLabel'     => __( 'Color value', $this->plugin_name ),
        );
        wp_localize_script( $this->plugin_name . '-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings );

        global $wp_version;
        if(Quiz_Maker_Data::ays_version_compare($wp_version, '>=', '5.5' )){
            wp_enqueue_script('ays-wp-load-scripts', plugin_dir_url(__FILE__) . 'js/ays-wp-load-scripts.js', array(), $this->version, true);
        }

        /* 
        ========================================== 
           * Bootstrap
           * select2
           * jQuery DataTables
        ========================================== 
        */
        wp_enqueue_script( $this->plugin_name."-ays_quiz_popper", plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-ays_quiz_bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name.'-select2js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name.'-sweetalert-js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script( $this->plugin_name."-db4.min.js", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name."-jquery.datetimepicker.js", plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array( 'jquery' ), $this->version, true );
        
        /* 
        ========================================== 
           File exporters
           * SCV
           * xlsx
        ========================================== 
        */
		wp_enqueue_script( $this->plugin_name."-CSVExport.js", plugin_dir_url( __FILE__ ) . 'js/CSVExport.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name."-xlsx.core.min.js", plugin_dir_url( __FILE__ ) . 'js/xlsx.core.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name."-fileSaver.js", plugin_dir_url( __FILE__ ) . 'js/FileSaver.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name."-jhxlsx.js", plugin_dir_url( __FILE__ ) . 'js/jhxlsx.js', array( 'jquery' ), $this->version, true );
        
        /* 
        ========================================== 
           Quiz admin dashboard scripts
        ========================================== 
        */

        $quiz_page = explode('_', $hook_suffix);
        $quiz_page = $quiz_page[count($quiz_page)-1];
        $quiz_page = explode('-', $quiz_page);
        if (! isset($quiz_page[2])){
            wp_enqueue_script($this->plugin_name .'-rate-quiz', AYS_QUIZ_PUBLIC_URL . '/js/rating.min.js', array('jquery'), $this->version, true);
            // wp_enqueue_script($this->plugin_name . '-public', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public1', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_dark.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public2', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_light.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public3', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_dark.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public4', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_light.js', array('jquery'), $this->version, true);
        }

        wp_enqueue_script( $this->plugin_name."-functions", plugin_dir_url(__FILE__) . 'js/partials/functions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_localize_script( $this->plugin_name."-functions", 'functionsQuizLangObj', array(
            'ajax_url'          => admin_url('admin-ajax.php'),
            'answerText'  => __( 'Answer text', $this->plugin_name),
        ) );
        wp_enqueue_script( $this->plugin_name."-quiz-styles", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-styles.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-information-form", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-information-form.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-quick-start", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quick-start.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-tabs", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-tabs.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-questions", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-questions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
		wp_enqueue_script( $this->plugin_name."-cookie.js", plugin_dir_url( __FILE__ ) . 'js/cookie.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( $this->plugin_name, 'quizLangObj', array(
            'notAnsweredText'                   => __( 'You have not answered this question', $this->plugin_name ),
            'areYouSure'                        => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
            'sorry'                             => __( 'Sorry', $this->plugin_name ),
            'unableStoreData'                   => __( 'We are unable to store your data', $this->plugin_name ),
            'connectionLost'                    => __( 'Connection is lost', $this->plugin_name ),
            'checkConnection'                   => __( 'Please check your connection and try again', $this->plugin_name ),
            'selectPlaceholder'                 => __( 'Select an answer', $this->plugin_name ),
            'shareDialog'                       => __( 'Share Dialog', $this->plugin_name ),
            'emptyTitle'                        => __( 'Sorry, you must fill out Title field.', $this->plugin_name ),

            'questionTitle'                     => __( 'Question Default Title', $this->plugin_name ),
            'radio'                             => __( 'Radio', $this->plugin_name ),
            'checkbox'                          => __( 'Checkbox', $this->plugin_name ),
            'dropdawn'                          => __( 'Dropdown', $this->plugin_name ),
            'emptyAnswer'                       => __( 'Empty Answer', $this->plugin_name ),
            'addGif'                            => __( 'Add Gif', $this->plugin_name),
            'addImage'                          => __( 'Add image', $this->plugin_name),
            'add'                               => __( 'Add', $this->plugin_name),
            'textType'                          => __( 'Text', $this->plugin_name),
            'answerText'                        => __( 'Answer text', $this->plugin_name),
            'copied'                            => __( 'Copied!', $this->plugin_name),
            'clickForCopy'                      => __( 'Click for copy.', $this->plugin_name),
            'redirect'                          => __( 'Redirect', $this->plugin_name ),
            'redirectUrl'                       => __( 'Redirect Url', $this->plugin_name ),
            'redirectDelay'                     => __( 'Redirect Delay (sec)', $this->plugin_name ),
            'shortTextType'                     => __( 'Short Text', $this->plugin_name),
            'true'                              => __( 'True', $this->plugin_name),
            'false'                             => __( 'False', $this->plugin_name),
            'number'                            => __( 'Number', $this->plugin_name),
            'trueOrFalse'                       => __( 'True/False', $this->plugin_name),
            'date'                              => __( 'Date', $this->plugin_name),
            'currentTime'                       => current_time( 'Y-m-d' ),

            'loadResource'                      => __( "Can't load resource.", $this->plugin_name ),
            'somethingWentWrong'                => __( "Maybe something went wrong.", $this->plugin_name ),
            'dataDeleted'                       => __( "Maybe the data has been deleted.", $this->plugin_name ),
            'useThisShortcode'                  => __( "You can use this shortcode to show your quiz.", $this->plugin_name ),
            'advancedconfiguration'             => __( "For more advanced configuration visit", $this->plugin_name ),
            'editQuizPage'                      => __( "edit quiz page.", $this->plugin_name ),
            'greatJob'                          => __( "Great job", $this->plugin_name ),
            'great'                             => __( "Great!", $this->plugin_name ),
            'thumbsUpGreat'                     => __( "Thumbs up, great!", $this->plugin_name ),
            'quizTitleNotEmpty'                 => __( "Quiz title can't be empty.", $this->plugin_name ),
            'mustFillAllAnswers'                => __( "You must fill all answers", $this->plugin_name ),
            'pleaseEnterMore'                   => __( "Please enter 1 or more characters", $this->plugin_name ),
            'searching'                         => __( "Searching...", $this->plugin_name ),
            'selectQuestionTags'                => __( "Select Tags", $this->plugin_name ),
            'selectAll'                         => __( "Select All", $this->plugin_name ),
            'deselectAll'                       => __( "Deselect All", $this->plugin_name ),
            'youWantToDelete'                   => __( "Are you sure you want to delete?", $this->plugin_name ),
            'minimumCountAnswerShouldBe'        => __( "Sorry minimum count of answers should be", $this->plugin_name ),
            'minimumCountQuestionShouldBe'      => __( "Sorry minimum count of questions should be", $this->plugin_name ),
            'youMustSelectAtLeast'              => __( "You must select at least one correct answer", $this->plugin_name ),
            'sorryYouMustFillout'               => __( "Sorry, you must fill out all answer fields.", $this->plugin_name ),
            'nextQustionPage'                   => __( 'Are you sure you want to go to the next question page?', $this->plugin_name),
            'areYouSureButton'                  => __( 'Are you sure you want to redirect to another quiz? Note that the changes made in this quiz will not be saved.', $this->plugin_name),
            'deleteQuestion'                    => __( 'Are you sure you want to delete question ?', $this->plugin_name),
            'deleteAnswer'                      => __( 'Are you sure you want to delete answer ?', $this->plugin_name),
            "all"                               => __( "All", $this->plugin_name ),
            "selectCategory"                    => __( "Select Category", $this->plugin_name ),
            "selectTags"                        => __( "Select Tags", $this->plugin_name ),
            "activated"                         => __( "Activated", $this->plugin_name ),
            "noIncorrectMatches"                => __( "No incorrect matches yet.", $this->plugin_name ),
            "answer"                            => __( "Answer", $this->plugin_name ),
            "placeholder"                       => __( "Placeholder", $this->plugin_name ),
            "slug"                              => __( "Slug", $this->plugin_name ),
            "delete"                            => __( "Delete", $this->plugin_name ),
            "copied"                            => __( "Copied!", $this->plugin_name ),
            "clickForCopy"                      => __( "Click for copy", $this->plugin_name ),

        ) );

        wp_localize_script( $this->plugin_name."-tabs", 'quizLangDataTableObj', array(
            "sEmptyTable"           => __( "No data available in table", $this->plugin_name ),
            "sInfo"                 => __( "Showing _START_ to _END_ of _TOTAL_ entries", $this->plugin_name ),
            "sInfoEmpty"            => __( "Showing 0 to 0 of 0 entries", $this->plugin_name ),
            "sInfoFiltered"         => __( "(filtered from _MAX_ total entries)", $this->plugin_name ),
            // "sInfoPostFix":          => __( "", $this->plugin_name ),
            // "sInfoThousands":        => __( ",", $this->plugin_name ),
            "sLengthMenu"           => __( "Show _MENU_ entries", $this->plugin_name ),
            "sLoadingRecords"       => __( "Loading...", $this->plugin_name ),
            "sProcessing"           => __( "Processing...", $this->plugin_name ),
            "sSearch"               => __( "Search:", $this->plugin_name ),
            // "sUrl":                  => __( "", $this->plugin_name ),
            "sZeroRecords"          => __( "No matching records found", $this->plugin_name ),
            "sFirst"                => __( "First", $this->plugin_name ),
            "sLast"                 => __( "Last", $this->plugin_name ),
            "sNext"                 => __( "Next", $this->plugin_name ),
            "sPrevious"             => __( "Previous", $this->plugin_name ),
            "sSortAscending"        => __( ": activate to sort column ascending", $this->plugin_name ),
            "sSortDescending"       => __( ": activate to sort column descending", $this->plugin_name ),

            "all"                   => __( "All", $this->plugin_name ),
            "selectCategory"        => __( "Select Category", $this->plugin_name ),
            "selectTags"            => __( "Select Tags", $this->plugin_name ),
        ) );

        $question_categories = Quiz_Maker_Data::get_question_categories();
        wp_localize_script( $this->plugin_name, 'aysQuizCatObj', array(
            'category' => $question_categories,
        ) );

        if( isset( $_GET['action'] ) && $_GET['action'] == "edit" ){
            wp_enqueue_script( $this->plugin_name."-load-questions", plugin_dir_url( __FILE__ ) . 'js/partials/quiz-maker-admin-load-questions.js', array( 'jquery' ), $this->version, true );
        }
                
        /* 
        ========================================== 
          Quiz admin dashboard scripts for AJAX
        ========================================== 
        */
        wp_enqueue_script( $this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/quiz-maker-admin-ajax.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-ajax', 'quiz_maker_ajax', array(
            'ajax_url'          => admin_url('admin-ajax.php'),
            "emptyEmailError"   => __( 'Email field is empty', $this->plugin_name),
            "invalidEmailError" => __( 'Invalid Email address', $this->plugin_name),
            'selectUser'        => __( 'Select user', $this->plugin_name),
            'pleaseEnterMore'   => __( "Please enter 1 or more characters", $this->plugin_name ),
            'searching'         => __( "Searching...", $this->plugin_name ),
            'activated'         => __( "Activated", $this->plugin_name ),
            'errorMsg'          => __( "Error", $this->plugin_name ),
            'loadResource'      => __( "Can't load resource.", $this->plugin_name ),
            'somethingWentWrong'=> __( "Maybe something went wrong.", $this->plugin_name ),
        ));
		wp_enqueue_script( $this->plugin_name . '-custom-dropdown-adapter', plugin_dir_url( __FILE__ ) . 'js/ays-select2-dropdown-adapter.js', array( 'jquery' ), $this->version, true );
    }

    public function codemirror_enqueue_scripts($hook) {
        if(strpos($hook, $this->plugin_name) !== false){
            if(function_exists('wp_enqueue_code_editor')){
                $cm_settings['codeEditor'] = wp_enqueue_code_editor(array(
                    'type' => 'text/css',
                    'codemirror' => array(
                        'inputStyle' => 'contenteditable',
                        'theme' => 'cobalt',
                    )
                ));

                wp_enqueue_script('wp-theme-plugin-editor');
                wp_localize_script('wp-theme-plugin-editor', 'cm_settings', $cm_settings);

                wp_enqueue_style('wp-codemirror');
            }
        }
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu(){

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        $setting_actions = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $options = ($setting_actions->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes( $setting_actions->ays_get_setting('options') ), true);

        // Disable Quiz maker menu item notification
        $options['quiz_disable_quiz_menu_notification'] = isset($options['quiz_disable_quiz_menu_notification']) ? esc_attr( $options['quiz_disable_quiz_menu_notification'] ) : 'off';
        $quiz_disable_quiz_menu_notification = (isset($options['quiz_disable_quiz_menu_notification']) && esc_attr( $options['quiz_disable_quiz_menu_notification'] ) == "on") ? true : false;

        if( $quiz_disable_quiz_menu_notification ){
            $menu_item = 'Quiz Maker';
        } else {
            global $wpdb;
            $unread_results_count = Results_List_Table::unread_records_count();
            $menu_item = ($unread_results_count == 0) ? 'Quiz Maker' : 'Quiz Maker' . '<span class="ays_menu_badge" id="ays_results_bage">' . $unread_results_count . '</span>';
        }

        
        $this->capability = $this->quiz_maker_capabilities();
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();
                
        add_menu_page(
            'Quiz Maker', 
            $menu_item, 
            $this->capability,
            $this->plugin_name,
            array($this, 'display_plugin_quiz_page'), 
            AYS_QUIZ_ADMIN_URL . '/images/icons/icon-128x128.png', 
            '6.20'
        );

    }

    public function add_plugin_quizzes_submenu(){
        $hook_quiz_maker = add_submenu_page(
            $this->plugin_name,
            __('Quizzes', $this->plugin_name),
            __('Quizzes', $this->plugin_name),
            $this->capability,
            $this->plugin_name,
            array($this, 'display_plugin_quiz_page')
        );

        add_action("load-$hook_quiz_maker", array($this, 'screen_option_quizes'));
        add_action("load-$hook_quiz_maker", array( $this, 'add_tabs' ));
    }

    public function add_plugin_questions_submenu(){
        $hook_questions = add_submenu_page(
            $this->plugin_name,
            __('Questions', $this->plugin_name),
            __('Questions', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-questions',
            array($this, 'display_plugin_questions_page')
        );

        add_action("load-$hook_questions", array($this, 'screen_option_questions'));
        add_action("load-$hook_questions", array( $this, 'add_tabs' ));

        $hook_all_results = add_submenu_page(
            'question_reports_slug',
            __('Reports', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-question-reports',
            array($this, 'display_plugin_question_reports_page')
        );

        add_action("load-$hook_all_results", array($this, 'screen_option_question_reports'));
        add_action("load-$hook_all_results", array( $this, 'add_tabs' ));

    }

    public function add_plugin_quiz_categories_submenu(){
        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Quiz Categories', $this->plugin_name),
            __('Quiz Categories', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-quiz-categories',
            array($this, 'display_plugin_quiz_categories_page')
        );

        add_action("load-$hook_quiz_categories", array($this, 'screen_option_quiz_categories'));
        add_action("load-$hook_quiz_categories", array( $this, 'add_tabs' ));
    }

    public function add_plugin_questions_categories_submenu(){
        $hook_questions_categories = add_submenu_page(
            $this->plugin_name,
            __('Question Categories', $this->plugin_name),
            __('Question Categories', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-question-categories',
            array($this, 'display_plugin_question_categories_page')
        );

        add_action("load-$hook_questions_categories", array($this, 'screen_option_questions_categories'));
        add_action("load-$hook_questions_categories", array( $this, 'add_tabs' ));

        $hook_all_results = add_submenu_page(
            'question_tags_slug',
            __('Tags', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-question-tags',
            array($this, 'display_plugin_question_tags_page')
        );

        add_action("load-$hook_all_results", array($this, 'screen_option_question_tags'));
        add_action("load-$hook_all_results", array( $this, 'add_tabs' ));

        add_filter('parent_file', array($this,'quiz_maker_select_question_cats_submenu'));
    }

    public function add_plugin_custom_fields_submenu(){
        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Custom Fields', $this->plugin_name),
            __('Custom Fields', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-quiz-attributes',
            array($this, 'display_plugin_quiz_attributes_page')
        );

        add_action("load-$hook_quiz_categories", array($this, 'screen_option_quiz_attributes'));
        add_action("load-$hook_quiz_categories", array( $this, 'add_tabs' ));
    }

    public function add_plugin_orders_submenu(){
        $hook_quiz_orders = add_submenu_page(
            $this->plugin_name,
            __('Orders', $this->plugin_name),
            __('Orders', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-quiz-orders',
            array($this, 'display_plugin_orders_page')
        );

        add_action("load-$hook_quiz_orders", array($this, 'screen_option_orders'));
        add_action("load-$hook_quiz_orders", array( $this, 'add_tabs' ));
    }

    public function add_plugin_results_submenu(){

        $setting_actions = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $options = ($setting_actions->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes( $setting_actions->ays_get_setting('options') ), true);

        // Disable Results menu item notification
        $options['quiz_disable_results_menu_notification'] = isset($options['quiz_disable_results_menu_notification']) ? esc_attr( $options['quiz_disable_results_menu_notification'] ) : 'off';
        $quiz_disable_results_menu_notification = (isset($options['quiz_disable_results_menu_notification']) && esc_attr( $options['quiz_disable_results_menu_notification'] ) == "on") ? true : false;

        if( $quiz_disable_results_menu_notification ){
            $results_text = __('Results', $this->plugin_name);
            $menu_item = __('Results', $this->plugin_name);
        } else {
            global $wpdb;
            $unread_results_count = Results_List_Table::unread_records_count();
            $results_text = __('Results', $this->plugin_name);
            $menu_item = ($unread_results_count == 0) ? $results_text : $results_text . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
        }

        $hook_results = add_submenu_page(
            $this->plugin_name,
            $results_text,
            $menu_item,
            $this->capability,
            $this->plugin_name . '-results',
            array($this, 'display_plugin_results_page')
        );

        add_action("load-$hook_results", array($this, 'screen_option_results'));
        add_action("load-$hook_results", array( $this, 'add_tabs' ));
        
        $hook_each_result = add_submenu_page(
            'each_result_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result',
            array($this, 'display_plugin_each_results_page')
        );

        add_action("load-$hook_each_result", array($this, 'screen_option_each_quiz_results'));
        add_action("load-$hook_each_result", array( $this, 'add_tabs' ));

        $hook_each_result_statistics = add_submenu_page(
            'each_result_statistics_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result-statistics',
            array($this, 'display_plugin_each_results_statistics_page')
        );

        // add_action("load-$hook_each_result_statistics", array($this, 'screen_option_each_quiz_results_statistics'));
        add_action("load-$hook_each_result_statistics", array( $this, 'add_tabs' ));

        $hook_each_result_questions = add_submenu_page(
            'each_result_questions_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result-questions',
            array($this, 'display_plugin_each_results_questions_page')
        );

        // add_action("load-$hook_each_result_questions", array($this, 'screen_option_each_quiz_results_statistics'));
        add_action("load-$hook_each_result_questions", array( $this, 'add_tabs' ));

        $hook_each_result_question_category_statistics = add_submenu_page(
            'each_result_question_category_statistics_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result-question-category-statistics',
            array($this, 'display_plugin_each_results_question_category_statistics_page')
        );

        // add_action("load-$hook_each_result_question_category_statistics", array($this, 'screen_option_each_quiz_results_statistics'));
        add_action("load-$hook_each_result_question_category_statistics", array( $this, 'add_tabs' ));

        $hook_each_result_leaderboard = add_submenu_page(
            'each_result_leaderboard_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result-leaderboard',
            array($this, 'display_plugin_each_results_leaderboard_page')
        );

        // add_action("load-$hook_each_result_leaderboard", array($this, 'screen_option_each_quiz_results_statistics'));
        add_action("load-$hook_each_result_leaderboard", array( $this, 'add_tabs' ));

        $hook_all_results = add_submenu_page(
            'all_results_slug',
            __('Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-all-results',
            array($this, 'display_plugin_all_results_page')
        );

        add_action("load-$hook_all_results", array($this, 'screen_option_all_quiz_results'));
        add_action("load-$hook_all_results", array( $this, 'add_tabs' ));

        $hook_global_statistics = add_submenu_page(
            'global_statistics_slug',
            __('Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-results-global-statistics',
            array($this, 'display_plugin_global_statistics_page')
        );

        // add_action("load-$hook_global_statistics", array($this, 'screen_option_results'));
        add_action("load-$hook_global_statistics", array( $this, 'add_tabs' ));

        $hook_global_leaderboard = add_submenu_page(
            'global_leaderboard_slug',
            __('Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-results-global-leaderboard',
            array($this, 'display_plugin_global_leaderboard_page')
        );

        // add_action("load-$hook_all_results", array($this, 'screen_option_results'));
        add_action("load-$hook_global_leaderboard", array( $this, 'add_tabs' ));

        $hook_all_reviews = add_submenu_page(
            'all_reviews_slug',
            __('Reviews', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-all-reviews',
            array($this, 'display_plugin_all_reviews_page')
        );

        add_action("load-$hook_all_reviews", array($this, 'screen_option_all_quiz_reviews'));
        add_action("load-$hook_all_reviews", array( $this, 'add_tabs' ));

        $hook_not_finished_result = add_submenu_page(
            'not_finished_result_slug',
            __('Not Finished Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-not-finished-results',
            array($this, 'display_plugin_not_finished_results_page')
        );

        add_action("load-$hook_not_finished_result", array($this, 'screen_option_not_finished_results'));
        add_action("load-$hook_not_finished_result", array( $this, 'add_tabs' ));

        add_filter('parent_file', array($this,'quiz_maker_select_submenu'));
    }

    public function add_plugin_dashboard_submenu(){
        $hook_quizes = add_submenu_page(
            $this->plugin_name,
            __('How to use', $this->plugin_name),
            __('How to use', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-dashboard',
            array($this, 'display_plugin_setup_page')
        );
        add_action("load-$hook_quizes", array( $this, 'add_tabs' ));
    }

    public function add_plugin_general_settings_submenu(){
        $hook_settings = add_submenu_page( $this->plugin_name,
            __('General Settings', $this->plugin_name),
            __('General Settings', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_settings_page') 
        );
        add_action("load-$hook_settings", array($this, 'screen_option_settings'));
        add_action("load-$hook_settings", array( $this, 'add_tabs' ));
    }

    public function add_plugin_featured_plugins_submenu(){
        $hook_featured_plugins = add_submenu_page( $this->plugin_name,
            __('Our products', $this->plugin_name),
            __('Our Products', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-our-products',
            array($this, 'display_plugin_featured_plugins_page') 
        );
        add_action("load-$hook_featured_plugins", array( $this, 'add_tabs' ));
    }

    public function add_plugin_affiliate_submenu(){
        add_submenu_page( $this->plugin_name,
            __('Affiliates', $this->plugin_name),
            __('Affiliates', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-affiliates',
            array($this, 'display_plugin_affiliate_page') 
        );
    }

    public function add_plugin_addons_submenu(){
        $hook_addons = add_submenu_page( $this->plugin_name,
            __('Addons', $this->plugin_name),
            __('Addons', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-addons',
            array($this, 'display_plugin_addons_page')
        );
        add_action("load-$hook_addons", array( $this, 'add_tabs' ));
    }

    public function quiz_maker_select_submenu($file) {
        global $plugin_page;
        if ("quiz-maker-each-result" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-all-results" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-all-reviews" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-not-finished-results" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-results-global-statistics" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-results-global-leaderboard" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-each-result-statistics" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-each-result-questions" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-each-result-question-category-statistics" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-each-result-leaderboard" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }else if("quiz-maker-question-reports" == $plugin_page) {
            $plugin_page = $this->plugin_name."-questions";
        }

        return $file;
    }
    
    public function quiz_maker_select_question_cats_submenu($file) {
        global $plugin_page;
        if ("quiz-maker-question-tags" == $plugin_page) {
            $plugin_page = $this->plugin_name."-question-categories";
        }

        return $file;
    }

    protected function quiz_maker_capabilities(){
        global $wpdb;
        $sql = "SELECT meta_value FROM {$wpdb->prefix}aysquiz_settings WHERE `meta_key` = 'user_roles'";
        $result = $wpdb->get_var($sql);
        
        $capability = 'ays_quiz_manage_options';
        if($result !== null){
            $ays_user_roles = json_decode($result, true);
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $current_user_roles = $current_user->roles;
                $ishmar = 0;
                foreach($current_user_roles as $r){
                    if(in_array($r, $ays_user_roles)){
                        $ishmar++;
                    }
                }
                if($ishmar > 0){
                    $capability = "read";
                }
            }
        }
        return $capability;
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links){
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page(){
        include_once('partials/quiz-maker-admin-display.php');
    }

    public function display_plugin_quiz_categories_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/quizes/actions/quiz-maker-quiz-categories-actions.php');
                break;
            case 'edit':
                include_once('partials/quizes/actions/quiz-maker-quiz-categories-actions.php');
                break;
            default:
                include_once('partials/quizes/quiz-maker-quiz-categories-display.php');
        }
    }

    public function display_plugin_quiz_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/quizes/actions/quiz-maker-quizes-actions.php');
                break;
            case 'edit':
                include_once('partials/quizes/actions/quiz-maker-quizes-actions.php');
                break;
            default:
                include_once('partials/quizes/quiz-maker-quizes-display.php');
        }
    }

    public function display_plugin_question_categories_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/questions/actions/quiz-maker-questions-categories-actions.php');
                break;
            case 'edit':
                include_once('partials/questions/actions/quiz-maker-questions-categories-actions.php');
                break;
            default:
                include_once('partials/questions/quiz-maker-question-categories-display.php');
        }
    }

    public function display_plugin_questions_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/questions/actions/quiz-maker-questions-actions.php');
                break;
            case 'edit':
                include_once('partials/questions/actions/quiz-maker-questions-actions.php');
                break;
            default:
                include_once('partials/questions/quiz-maker-questions-display.php');
        }
    }

    public function display_plugin_quiz_attributes_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/attributes/actions/quiz-maker-attributes-actions.php');
                break;
            case 'edit':
                include_once('partials/attributes/actions/quiz-maker-attributes-actions.php');
                break;
            default:
                include_once('partials/attributes/quiz-maker-attributes-display.php');
        }
    }

    public function display_plugin_results_page(){

        include_once('partials/results/quiz-maker-results-display.php');
    }
    
    public function display_plugin_each_results_page(){
        include_once 'partials/results/quiz-maker-each-results-display.php';
    }

    public function display_plugin_each_results_statistics_page(){
        include_once 'partials/results/each/quiz-maker-each-results-statistics-display.php';
    }

    public function display_plugin_each_results_questions_page(){
        include_once 'partials/results/each/quiz-maker-each-results-questions-display.php';
    }

    public function display_plugin_each_results_question_category_statistics_page(){
        include_once 'partials/results/each/quiz-maker-each-results-question-category-statistics-display.php';
    }

    public function display_plugin_each_results_leaderboard_page(){
        include_once 'partials/results/each/quiz-maker-each-results-leaderboard-display.php';
    }
    
    public function display_plugin_orders_page(){

        include_once('partials/orders/quiz-maker-orders-display.php');
    }
    
    public function display_plugin_settings_page(){        
        include_once('partials/settings/quiz-maker-settings.php');
    }

    public function display_plugin_featured_plugins_page(){
        include_once('partials/features/quiz-maker-plugin-featured-display.php');
    }

    public function display_plugin_addons_page(){
        include_once('partials/features/quiz-maker-addons-display.php');
    }

    public function display_plugin_all_results_page(){
        include_once('partials/results/quiz-maker-all-results-display.php');
    }

    public function display_plugin_global_statistics_page(){
        include_once('partials/results/quiz-maker-results-global-statistics-display.php');
    }

    public function display_plugin_global_leaderboard_page(){
        include_once('partials/results/quiz-maker-results-global-leaderboard-display.php');
    }

    public function display_plugin_all_reviews_page(){
        include_once('partials/results/quiz-maker-all-reviews-display.php');
    }

    public function display_plugin_not_finished_results_page(){
        include_once 'partials/results/quiz-maker-not-finished-results-display.php';
    }

    public function display_plugin_affiliate_page(){
        include_once('partials/affiliate/quiz-maker-affiliate-display.php');
    }

    public function display_plugin_question_tags_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';

        switch ($action) {
            case 'add':
                include_once('partials/questions/actions/quiz-maker-questions-tags-actions.php');
                break;
            case 'edit':
                include_once('partials/questions/actions/quiz-maker-questions-tags-actions.php');
                break;
            default:
            include_once('partials/questions/quiz-maker-question-tags-display.php');
        }

    }

    public function display_plugin_question_reports_page(){
        include_once('partials/questions/quiz-maker-question-reports-display.php');
    }

    public static function set_screen($status, $option, $value){
        return $value;
    }

    public function screen_option_quizes(){
        $option = 'per_page';
        $args = array(
            'label' => __('Quizzes', $this->plugin_name),
            'default' => 20,
            'option' => 'quizes_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->quizes_obj = new Quizes_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_quiz_categories(){
        $option = 'per_page';
        $args = array(
            'label' => __('Quiz Categories', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_categories_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->quiz_categories_obj = new Quiz_Categories_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_questions(){
        $option = 'per_page';
        $args = array(
            'label' => __('Questions', $this->plugin_name),
            'default' => 20,
            'option' => 'questions_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->questions_obj = new Questions_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_questions_categories(){
        $option = 'per_page';
        $args = array(
            'label' => __('Question Categories', $this->plugin_name),
            'default' => 20,
            'option' => 'question_categories_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->question_categories_obj = new Question_Categories_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_quiz_attributes(){
        $option = 'per_page';
        $args = array(
            'label' => __('Quiz Attributes', $this->plugin_name),
            'default' => 20,
            'option' => 'attributes_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->attributes_obj = new Quiz_Attributes_List_Table($this->plugin_name);
    }

    public function screen_option_results(){
        $option = 'per_page';
        $args = array(
            'label' => __('Results', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_results_per_page'
        );

        add_screen_option($option, $args);
        $this->results_obj = new Results_List_Table($this->plugin_name);
    }

    public function screen_option_each_quiz_results() {
        $option = 'per_page';
        $args = array(
            'label' => __('Results', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_each_results_per_page',
        );

        add_screen_option($option, $args);
        $this->each_result_obj = new Quiz_Each_Results_List_Table($this->plugin_name);
    }
    
    public function screen_option_not_finished_results() {
        $option = 'per_page';
        $args = array(
            'label' => __('Not Finished Results', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_not_finished_results_per_page',
        );

        add_screen_option($option, $args);
        $this->not_finished_result_obj = new Quiz_Not_Finished_Results_List_Table($this->plugin_name);
    }

    public function screen_option_orders(){
        $option = 'per_page';
        $args = array(
            'label' => __('Orders', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_orders_per_page'
        );

        add_screen_option($option, $args);
        $this->orders_obj = new Quiz_Orders_List_Table($this->plugin_name);
    }
    
    public function screen_option_settings(){
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_all_quiz_results(){
        $option = 'per_page';
        $args = array(
            'label' => __('All Results', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_all_results_per_page'
        );

        add_screen_option($option, $args);
        $this->all_results_obj = new All_Results_List_Table($this->plugin_name);
    }

    public function screen_option_all_quiz_reviews(){
        $option = 'per_page';
        $args = array(
            'label' => __('All Reviews', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_all_reviews_per_page'
        );

        add_screen_option($option, $args);
        $this->all_reviews_obj = new All_Reviews_List_Table($this->plugin_name);
    }

    public function screen_option_question_tags(){
        $option = 'per_page';

        $args = array(
            'label' => __('Question Tags', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_question_tags_per_page'
        );

        add_screen_option($option, $args);

        $this->question_tags_obj = new Question_tags_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_question_reports(){
        $option = 'per_page';

        $args = array(
            'label' => __('Question Reports', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_question_reports_per_page'
        );

        add_screen_option($option, $args);

        $this->question_reports_obj = new Question_reports_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }


    /**
     * Adding questions from modal to table
     */
    public function add_question_rows(){
        error_reporting(0);

        if ( (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_question_rows") || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_question_rows_top") ) {
            $question_ids = (isset($_REQUEST["ays_questions_ids"]) && !empty($_REQUEST["ays_questions_ids"])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_questions_ids'] ) : array();

            if ( !empty( $question_ids ) ) {
                $question_ids = array_unique($question_ids);
                $question_ids = array_values($question_ids);
            }
            
            $rows = array();
            $ids = array();
            if (!empty($question_ids)) {
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

                foreach ($question_ids as $question_id) {
                    $data = Quiz_Maker_Data::get_published_questions_by('id', absint(intval($question_id)));

                    switch ( $data['type'] ) {
                        case 'short_text':
                            $ays_question_type = 'short text';
                            break;
                        case 'true_or_false':
                            $ays_question_type = 'true/false';
                            break;
                        default:
                            $ays_question_type = $data['type'];
                            break;
                    }

                    if($data['type'] == 'custom'){
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = htmlspecialchars_decode( $data['question_title'], ENT_COMPAT);
                            $table_question = stripslashes( $table_question );
                        }else{
                            $table_question = __( 'Custom question', $this->plugin_name ) . ' #' . $data['id'];
                        }
                    }else{
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = esc_attr( $data['question_title'] );
                        }elseif (isset($data['question']) && strlen($data['question']) != 0){
                            // $is_exists_ruby = Quiz_Maker_Data::ays_quiz_is_exists_needle_tag( $data['question'] , '<ruby>' );
                            // if ( $is_exists_ruby ) {
                            //     $table_question = strip_tags( stripslashes($data['question']), '<ruby><rbc><rtc><rb><rt>' );
                            // } else {
                            // }
                            $table_question = strip_tags(stripslashes($data['question']));
                        }elseif (isset($data['question_image']) && $data['question_image'] !=''){
                            $table_question = __( 'Image question', $this->plugin_name );
                        }
                        $table_question = $this->ays_restriction_string("word", $table_question, 8);
                    }
                    $edit_question_url = "?page=".$this->plugin_name."-questions&action=edit&question=".$data['id'];

                    $tag_ids = explode(',',$data['tag_id']);
                    $question_tags_title = '';

                    foreach ($tag_ids as $tag_id) {
                        $question_tags_title .= $question_tags_array[$tag_id].",";
                    }

                    $rows[] = '<tr class="ays-question-row ui-state-default" data-id="' . $data['id'] . '">
                                    <td class="ays-sort ays-quiz-question-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-question-row">
                                        <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                            ' . $table_question . '
                                        </a>
                                    </td>
                                    <td class="ays-quiz-question-type-row">' . $ays_question_type . '</td>
                                    <td class="ays-quiz-question-category-row">' . $question_categories_array[$data['category_id']] . '</td>
                                    <td class="ays-quiz-question-tag-row">' . rtrim($question_tags_title, ',') . '</td>
                                    <td class="ays-quiz-question-id-row">' . stripslashes($data['id']) . '</td>
                                    <td class="ays-quiz-question-action-row">
                                        <div class="ays-question-row-actions">
                                            <input type="checkbox" class="ays_del_tr">
                                            <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                                <i class="ays_fa ays_fa_pencil_square" aria-hidden="true"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="ays-delete-question" title="'. __('Delete', $this->plugin_name) .'"
                                               data-id="' . $data['id'] . '">
                                                <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </td>
                               </tr>';
                    $ids[] = $data['id'];
                }

                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => $rows,
                    'ids' => $ids
                ));
                wp_die();
            } else {
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => '',
                    'ids' => array()
                ));
                wp_die();
            }
        } else {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'rows' => '',
                'ids' => array()
            ));
            wp_die();
        }
    }
    
    protected function get_question_answers( $question_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_answers WHERE question_id=" . absint( intval( $question_id ) );

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    
    public function ays_questions_export_filter(){
        global $wpdb;
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        $author_id_sql = "SELECT author_id FROM {$wpdb->prefix}aysquiz_questions";
        $category_id_sql = "SELECT category_id FROM {$wpdb->prefix}aysquiz_questions";
        $tag_id_sql = "";

        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $author_id_sql = "SELECT q.author_id
                            FROM {$wpdb->prefix}aysquiz_questions AS q
                            LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                                ON q.category_id = c.id
                            WHERE q.author_id = ".$current_user;
            $category_id_sql = "SELECT q.category_id
                            FROM {$wpdb->prefix}aysquiz_questions AS q
                            LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                                ON q.category_id = c.id
                            WHERE q.author_id = ".$current_user;
        }
        $author_id = (isset($_REQUEST['author_id']) && $_REQUEST['author_id'] != null) ? implode(',', $_REQUEST['author_id']) : $author_id_sql;
        $category_id = (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != null) ? implode(',', $_REQUEST['category_id']) : $category_id_sql;
        $tag_id = (isset($_REQUEST['tag_id']) && $_REQUEST['tag_id'] != null) ? implode(',', $_REQUEST['tag_id']) : null;

        if( isset($tag_id) && !empty( $tag_id ) ){
            $tag_id_sql = " qt.id IN (". $tag_id .") AND ";
        }

        $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

        $date_isnull = 'q.create_date IS NULL OR';
        if( (isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '') || (isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '') ){
            $date_isnull = '';
        }
        if( ! $this->current_user_can_edit ){
            $sql = "SELECT COUNT(*) AS count
                    FROM {$wpdb->prefix}aysquiz_questions AS q
                    LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                        ON q.category_id = c.id
                    LEFT JOIN {$wpdb->prefix}aysquiz_question_tags AS qt
                        ON (find_in_set(qt.id,q.tag_id)>0)
                    WHERE
                        q.author_id = {$current_user} AND
                        q.author_id IN ($author_id) AND
                        q.category_id IN ($category_id) AND
                        {$tag_id_sql}
                        q.published != 2 AND
                        q.create_date BETWEEN '$date_from' AND '$date_to 23:59:59'
                    ORDER BY q.id DESC";
        }else{
            $sql = "SELECT COUNT(*) AS count
                    FROM {$wpdb->prefix}aysquiz_questions AS q
                    LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                        ON q.category_id = c.id
                    LEFT JOIN {$wpdb->prefix}aysquiz_question_tags AS qt
                        ON (find_in_set(qt.id,q.tag_id)>0)
                    WHERE
                        q.author_id IN ($author_id) AND
                        q.category_id IN ($category_id) AND
                        {$tag_id_sql}
                        q.published != 2 AND
                        ({$date_isnull}
                         q.create_date = '0000-00-00 00:00:00' OR q.create_date BETWEEN '$date_from' AND '$date_to 23:59:59')
                    ORDER BY q.id DESC";
        }
        $results = $wpdb->get_row($sql);
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($results);
        wp_die();
    }

	public function ays_questions_export() {
        error_reporting(0);
		global $wpdb;
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'csv';
        $results = array();
		// $sql = "SELECT q.*, c.title AS category_title
          //    FROM {$wpdb->prefix}aysquiz_questions AS q
          //    LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
          //        ON q.category_id = c.id
          //    ORDER BY q.id";
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();
        $author_id_sql = "SELECT author_id FROM {$wpdb->prefix}aysquiz_questions";
        $category_id_sql = "SELECT category_id FROM {$wpdb->prefix}aysquiz_questions";
        $tag_id_sql = "";

        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $author_id_sql = "SELECT q.author_id
                            FROM {$wpdb->prefix}aysquiz_questions AS q
                            LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                                ON q.category_id = c.id
                            WHERE q.author_id = ".$current_user;
            $category_id_sql = "SELECT q.category_id
                            FROM {$wpdb->prefix}aysquiz_questions AS q
                            LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                                ON q.category_id = c.id
                            WHERE q.author_id = ".$current_user;
        }

        $author_id = (isset($_REQUEST['author_id']) && $_REQUEST['author_id'] != null) ? implode(',', $_REQUEST['author_id']) : $author_id_sql;
        $category_id = (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != null) ? implode(',', $_REQUEST['category_id']) : $category_id_sql;
        $tag_id = (isset($_REQUEST['tag_id']) && $_REQUEST['tag_id'] != null) ? implode(',', $_REQUEST['tag_id']) : null;

        if( isset($tag_id) && !empty( $tag_id ) ){
            $tag_id_sql = " qt.id IN (". $tag_id .") AND ";
        }

        $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

        $date_isnull = 'q.create_date IS NULL OR';
        if( (isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '') || (isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '') ){
            $date_isnull = '';
        }
        if( ! $this->current_user_can_edit ){
            $sql = "SELECT q.*, c.title AS category_title, ( SELECT GROUP_CONCAT( `title` SEPARATOR ',' )
                      FROM `{$wpdb->prefix}aysquiz_question_tags`
                      WHERE FIND_IN_SET( `id`, q.`tag_id` ) AND `status` = 'published'
                    ) AS tag_title
                    FROM {$wpdb->prefix}aysquiz_questions AS q
                    LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                        ON q.category_id = c.id

                    LEFT JOIN {$wpdb->prefix}aysquiz_question_tags AS qt
                        ON (find_in_set(qt.id,q.tag_id)>0)

                    WHERE
                        q.author_id = {$current_user} AND
                        q.author_id IN ($author_id) AND
                        q.category_id IN ($category_id) AND
                        {$tag_id_sql}
                        q.published != 2 AND
                        q.create_date BETWEEN '$date_from' AND '$date_to 23:59:59'
                    ORDER BY q.id DESC";
        }else{
            $sql = "SELECT q.* , c.title AS category_title, ( SELECT GROUP_CONCAT( `title` SEPARATOR ',' )
                      FROM `{$wpdb->prefix}aysquiz_question_tags`
                      WHERE FIND_IN_SET( `id`, q.`tag_id` ) AND `status` = 'published'
                    ) AS tag_title
                    FROM {$wpdb->prefix}aysquiz_questions AS q
                    LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                        ON q.category_id = c.id

                    LEFT JOIN {$wpdb->prefix}aysquiz_question_tags AS qt
                        ON (find_in_set(qt.id,q.tag_id)>0)

                    WHERE
                        q.author_id IN ($author_id) AND
                        q.category_id IN ($category_id) AND
                        {$tag_id_sql}
                        q.published != 2 AND
                        ({$date_isnull}
                        q.create_date = '0000-00-00 00:00:00' OR q.create_date BETWEEN '$date_from' AND '$date_to 23:59:59')
                    ORDER BY q.id DESC";
        }

		$questions = $wpdb->get_results($sql, 'ARRAY_A');

        switch($type){
            case 'csv':
                $results = $this->ays_questions_export_csv($questions);
                break;
            case 'xlsx':
                $results = $this->ays_questions_export_xlsx($questions);
                break;
            case 'json':
                $results = $this->ays_questions_export_json($questions);
                break;
            case 'simple_xlsx':
                $results = $this->ays_questions_export_simple_xlsx($questions);
                break;
            default:
                $results = $this->ays_questions_export_csv($questions);
                break;
        }
        
       // $data = array();
       // $data['progress_label'] = '';

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($results, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        wp_die();
    }

	public function ays_questions_export_sql($questions) {
		global $wpdb;
		error_reporting(0);
		$response = array('status' => false, 'data' => "", "type" => 'sql');

		$db_host = $wpdb->dbhost;
		$db_user = $wpdb->dbuser;
		$db_pass = $wpdb->dbpassword;
		$db_name = $wpdb->dbname;

		//connect to db
		$link = mysqli_connect($db_host, $db_user, $db_pass);
		mysqli_set_charset($link, 'utf8');
		mysqli_select_db($link, $db_name);

		//disable foreign keys (to avoid errors)
		$sql_string = 'SET FOREIGN_KEY_CHECKS=0;' . "\r\n";
		$sql_string .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . "\r\n";
		$sql_string .= 'SET AUTOCOMMIT=0;' . "\r\n";
		$sql_string .= 'START TRANSACTION;' . "\r\n";

		$tables = array(
			$wpdb->prefix . "aysquiz_questions",
			$wpdb->prefix . "aysquiz_answers",
		);
		foreach ( $tables as $table ) {
			$result     = mysqli_query($link, 'SELECT * FROM ' . $table);
			$num_fields = mysqli_num_fields($result);
			$num_rows   = mysqli_num_rows($result);
			$i_row      = 0;

			$sql_string .= "\n\n";

			if ($num_rows !== 0) {
				$row3       = mysqli_fetch_fields($result);
				$sql_string .= 'INSERT INTO ' . $table . '( ';
				foreach ( $row3 as $th ) {
					$sql_string .= '`' . $th->name . '`, ';
				}
				$sql_string = substr($sql_string, 0, -2);
				$sql_string .= ' ) VALUES';

				for ( $i = 0; $i < $num_fields; $i++ ) {
					while ( $row = mysqli_fetch_row($result) ) {
						$sql_string .= "\n(";
						for ( $j = 0; $j < $num_fields; $j++ ) {
							$row[$j] = addslashes($row[$j]);
							$row[$j] = preg_replace("#\n#", "\\n", $row[$j]);
							if (isset($row[$j])) {
								$sql_string .= '"' . $row[$j] . '"';
							} else {
								$sql_string .= '""';
							}
							if ($j < ($num_fields - 1)) {
								$sql_string .= ',';
							}
						}
						if (++$i_row == $num_rows) {
							$sql_string .= ");"; // last row
						} else {
							$sql_string .= "),"; // not last row
						}
					}
				}
			}
			$sql_string .= "\n\n\n";
		}

		// enable foreign keys
		$sql_string .= 'SET FOREIGN_KEY_CHECKS=1;' . "\r\n";
		$sql_string .= 'COMMIT;';

		$response['status'] = true;
		$response['data']   = $sql_string;
		echo json_encode($response);
		wp_die();
	}

	public function ays_questions_export_csv($questions) {
        global $wpdb;
        error_reporting(0);

        $export_file_fields = array(
            'category',
            'question',
            'question_image',
            'question_hint',
            'type',
            'published',
            'wrong_answer_text',
            'right_answer_text',
            'explanation',
            'user_explanation',
            'not_influence_to_score',
            'weight',
            'answers',
            'options',
            'question_title',
            'tags',
            'id',
        );

        $results_array_csv = array();
        if(empty($questions)){
            $export_data = array(
                'status'     => true,
                'type'       => 'csv',
                'data'       => array(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ),
                'fields'     => $export_file_fields
            );
        }else{
            foreach ($questions as $key => $question){
                $question = (array)$question;

                $question_id = (isset($question['id']) && $question['id'] != '') ? absint($question['id'] ): "";
                $category_id = (isset($question['category_title']) && $question['category_title'] != '') ? "\"".$question['category_title']."\"" : "\"".'Uncategorized'."\"";
                $question_content = "\"".htmlspecialchars(str_replace("\n", "", wpautop($question['question'])))."\"";
                $question_title = (isset($question['question_title']) && $question['question_title'] != '') ? $question['question_title'] : '';
                $question_image = (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '';

                if( !empty( $question_image ) ){
                    $question_image = Quiz_Maker_Data::ays_quiz_question_get_image_full_size_url_by_url($question_image);
                }

                $question_hint = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['question_hint']))))."\"";
                $type = (isset($question['type']) && $question['type'] != null) ? $question['type'] : 'radio';
                $published = (isset($question['published']) && $question['published'] != null) ? $question['published'] : 1;
                $wrong_answer_text = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['wrong_answer_text']))))."\"";
                $right_answer_text = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['right_answer_text']))))."\"";
                $explanation = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['explanation']))))."\"";
                $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
                $question_weight = (isset($question['weight']) && $question['weight'] != null) ? $question['weight'] : 1;
                $question_tags = (isset($question['tag_title']) && $question['tag_title'] != "") ? $question['tag_title'] : '';

                $answers_line = '';
                $answers = $this->get_question_answers($question_id);
                foreach ($answers as $ans){
                    $answer = htmlspecialchars( str_replace( "\n", "", $ans['answer'] ) );
                    $image = (isset($ans['image']) && ($ans['image'] != null || $ans['image'] != '')) ? ($ans['image']) : '';
                    $correct = htmlentities($ans['correct']);
                    $weight = isset($ans['weight']) && $ans['weight'] != null ? htmlentities($ans['weight']) : 0;
                    $placeholder = isset($ans['placeholder']) && $ans['placeholder'] != '' ? htmlspecialchars( stripslashes( str_replace( "\n", "", $ans['placeholder'] ) ) ) : "";
                    $keyword = isset($ans['keyword']) && $ans['keyword'] != '' ? htmlspecialchars( stripslashes( $ans['keyword'] ) ) : "A";
                    $answer_id = isset($ans['id']) && $ans['id'] != '' ? htmlspecialchars( stripslashes( $ans['id'] ) ) : "";
                    $slug = isset($ans['slug']) && $ans['slug'] != '' ? htmlspecialchars( stripslashes( $ans['slug'] ) ) : "";
                    $answer_options = isset($ans['options']) && $ans['options'] != '' ? htmlspecialchars( stripslashes( $ans['options'] ) ) : "";

                    $answers_array = array(
                        $answer,
                        $correct,
                        $weight,
                        $image,
                        $placeholder,
                        $keyword,
                        $answer_id,
                        $slug,
                        $answer_options,
                    );
                    $answers_line .= implode( '::', $answers_array ) . ";;";
                }
                $answers = "\"" . $answers_line . "\"";
                $question_tags = "\"" . $question_tags . "\"";
                $question_id = "\"" . $question_id . "\"";

                $questions_options = (isset($question['options']) && ($question['options'] != '' && $question['options'] != null)) ? json_decode($question['options'], true) : array();
                $options = array();
                $bg_image = (isset($questions_options['bg_image']) && $questions_options['bg_image'] != '') ? $questions_options['bg_image'] : '';
                $use_html = (isset($questions_options['use_html']) && $questions_options['use_html'] != '') ? $questions_options['use_html'] : 'off';

                // Maximum length of a text field
                $questions_options['enable_question_text_max_length'] = isset($questions_options['enable_question_text_max_length']) ? sanitize_text_field($questions_options['enable_question_text_max_length']) : 'off';
                $enable_question_text_max_length = (isset($questions_options['enable_question_text_max_length']) && sanitize_text_field( $questions_options['enable_question_text_max_length'] ) == 'on') ? 'on' : 'off';

                // Length
                $question_text_max_length = ( isset($questions_options['question_text_max_length']) && sanitize_text_field( $questions_options['question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field( $questions_options['question_text_max_length'] ) ) ) : '';

                // Limit by
                $question_limit_text_type = ( isset($questions_options['question_limit_text_type']) && sanitize_text_field( $questions_options['question_limit_text_type'] ) != '' ) ? sanitize_text_field( $questions_options['question_limit_text_type'] ) : 'characters';

                // Show the counter-message
                $questions_options['question_enable_text_message'] = isset($questions_options['question_enable_text_message']) ? sanitize_text_field( $questions_options['question_enable_text_message'] ) : 'off';
                $question_enable_text_message = (isset($questions_options['question_enable_text_message']) && $questions_options['question_enable_text_message'] == 'on') ? 'on' : 'off';

                // Maximum length of a number field
                $questions_options['enable_question_number_max_length'] = isset($questions_options['enable_question_number_max_length']) ? sanitize_text_field( $questions_options['enable_question_number_max_length'] ) : 'off';
                $enable_question_number_max_length = (isset($questions_options['enable_question_number_max_length']) && sanitize_text_field( $questions_options['enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

                // Length
                $question_number_max_length = ( isset($questions_options['question_number_max_length']) && sanitize_text_field( $questions_options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $questions_options['question_number_max_length'] ) ) : '';

                // Hide question text on the front-end
                $questions_options['quiz_hide_question_text'] = isset($questions_options['quiz_hide_question_text']) ? sanitize_text_field( $questions_options['quiz_hide_question_text'] ) : 'off';
                $quiz_hide_question_text = (isset($questions_options['quiz_hide_question_text']) && $questions_options['quiz_hide_question_text'] == 'on') ? 'on' : 'off';


                // Enable maximum selection number
                $questions_options['enable_max_selection_number'] = isset($questions_options['enable_max_selection_number']) ? sanitize_text_field( $questions_options['enable_max_selection_number'] ) : 'off';
                $enable_max_selection_number = (isset($questions_options['enable_max_selection_number']) && sanitize_text_field( $questions_options['enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

                // Max value
                $max_selection_number = ( isset($questions_options['max_selection_number']) && $questions_options['max_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['max_selection_number'] ) ) : '';

                // Note text
                $quiz_question_note_message = ( isset($questions_options['quiz_question_note_message']) && $questions_options['quiz_question_note_message'] != '' ) ? wp_kses_post( $questions_options['quiz_question_note_message'] ) : '';
                if ( $quiz_question_note_message != "" ) {
                    $quiz_question_note_message = htmlspecialchars( stripslashes( str_replace( "\n", "", $quiz_question_note_message ) ) );
                }

                // Enable case sensitive text
                $enable_case_sensitive_text = (isset($questions_options['enable_case_sensitive_text']) && sanitize_text_field( $questions_options['enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

                // Enable minimum selection number
                $questions_options['enable_min_selection_number'] = isset($questions_options['enable_min_selection_number']) ? sanitize_text_field( $questions_options['enable_min_selection_number'] ) : 'off';
                $enable_min_selection_number = (isset($questions_options['enable_min_selection_number']) && sanitize_text_field( $questions_options['enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

                // Min value
                $min_selection_number = ( isset($questions_options['min_selection_number']) && $questions_options['min_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['min_selection_number'] ) ) : '';

                // Minimum length of a number field
                $questions_options['enable_question_number_min_length'] = isset($questions_options['enable_question_number_min_length']) ? sanitize_text_field( $questions_options['enable_question_number_min_length'] ) : 'off';
                $enable_question_number_min_length = (isset($questions_options['enable_question_number_min_length']) && sanitize_text_field( $questions_options['enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

                // Length
                $question_number_min_length = ( isset($questions_options['question_number_min_length']) && sanitize_text_field( $questions_options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $questions_options['question_number_min_length'] ) ) : '';

                // Show error message
                $questions_options['enable_question_number_error_message'] = isset($questions_options['enable_question_number_error_message']) ? sanitize_text_field( $questions_options['enable_question_number_error_message'] ) : 'off';
                $enable_question_number_error_message = (isset($questions_options['enable_question_number_error_message']) && sanitize_text_field( $questions_options['enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

                // Message
                $question_number_error_message = ( isset($questions_options['question_number_error_message']) && sanitize_text_field( $questions_options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $questions_options['question_number_error_message'] ) ) : '';

                // Enable strip slashes for questions
                $questions_options['quiz_enable_question_stripslashes'] = isset($questions_options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) : 'off';
                $quiz_enable_question_stripslashes = (isset($questions_options['quiz_enable_question_stripslashes']) && sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) == 'on') ? 'on' : 'off';

                // Disable strip slashes for answers
                $questions_options['quiz_disable_answer_stripslashes'] = isset($questions_options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) : 'off';
                $quiz_disable_answer_stripslashes = (isset($questions_options['quiz_disable_answer_stripslashes']) && sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

                // Answer slug max ID
                $answer_slug_max_id = ( isset($questions_options['answer_slug_max_id']) && sanitize_text_field( $questions_options['answer_slug_max_id'] ) != '' ) ? absint( sanitize_text_field ( $questions_options['answer_slug_max_id'] ) ) : 1;

                // Matching question type incorrect answers/matches
                $answer_incorrect_matches = (isset($questions_options['answer_incorrect_matches']) && !empty($questions_options['answer_incorrect_matches'])) ? base64_encode(json_encode( $questions_options['answer_incorrect_matches'] )) : '';

                $options_data_arr = array(
                    'bg_image'                              => $bg_image,
                    'use_html'                              => $use_html,
                    'enable_question_text_max_length'       => $enable_question_text_max_length,
                    'question_text_max_length'              => $question_text_max_length,
                    'question_limit_text_type'              => $question_limit_text_type,
                    'question_enable_text_message'          => $question_enable_text_message,
                    'enable_question_number_max_length'     => $enable_question_number_max_length,
                    'question_number_max_length'            => $question_number_max_length,
                    'quiz_hide_question_text'               => $quiz_hide_question_text,
                    'enable_max_selection_number'           => $enable_max_selection_number,
                    'max_selection_number'                  => $max_selection_number,
                    'quiz_question_note_message'            => $quiz_question_note_message,
                    'enable_case_sensitive_text'            => $enable_case_sensitive_text,
                    'enable_min_selection_number'           => $enable_min_selection_number,
                    'min_selection_number'                  => $min_selection_number,
                    'enable_question_number_min_length'     => $enable_question_number_min_length,
                    'question_number_min_length'            => $question_number_min_length,
                    'enable_question_number_error_message'  => $enable_question_number_error_message,
                    'question_number_error_message'         => $question_number_error_message,
                    'quiz_enable_question_stripslashes'     => $quiz_enable_question_stripslashes,
                    'quiz_disable_answer_stripslashes'      => $quiz_disable_answer_stripslashes,
                    'answer_slug_max_id'                    => $answer_slug_max_id,
                    'answer_incorrect_matches'              => $answer_incorrect_matches,
                );
                
                foreach ($options_data_arr as $option_key => $option_value) {
                    $options[] = $option_key . "=" . $option_value;
                }

                $options = implode( '::', $options );

                $q = array(
                    $category_id,
                    $question_content,
                    $question_image,
                    $question_hint,
                    $type,
                    $published,
                    $wrong_answer_text,
                    $right_answer_text,
                    $explanation,
                    $user_explanation,
                    $not_influence_to_score,
                    $question_weight,
                    $answers,
                    $options,
                    $question_title,
                    $question_tags,
                    $question_id,
                );

                $results_array_csv[] = $q;
            }

            $export_data = array(
                'status'     => true,
                'type'       => 'csv',
                'data'       => $results_array_csv,
                'fields'     => $export_file_fields
            );
        }
        return $export_data;
	}

	public function ays_questions_export_xls($questions) {
		global $wpdb;
		error_reporting(0);
		$sql               = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
		$questions         = $wpdb->get_results($sql, 'ARRAY_A');
		$questions_headers = array(
            'category_id' => "Number",
            'question' => "String",
            'question_image' => "String",
            'question_hint' => "String",
            'type' => "String",
            'published' => "Number",
            'wrong_answer_text' => "String",
            'right_answer_text' => "String",
            'explanation' => "String",
            'user_explanation' => "String",
            'answers' => "String"
		);
        $quests = array();
		foreach ( $questions as &$question ) {
			$answers = json_encode($this->get_question_answers($question['id']));
            
            $q = array(
                'category_id' => $question['category_id'],
                'question' => htmlentities((str_replace("\n", "", wpautop($question['question'])))),
                'question_image' => (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '',
                'question_hint' => htmlentities(str_replace("\n", "", wpautop($question['question_hint']))),
                'type' => ($question['type'] == null) ? '' : $question['type'],
                'published' => ($question['published'] == null) ? '' : $question['published'],
                'wrong_answer_text' => htmlentities(str_replace("\n", "", wpautop($question['wrong_answer_text']))),
                'right_answer_text' => htmlentities(str_replace("\n", "", wpautop($question['right_answer_text']))),
                'explanation' => htmlentities(str_replace("\n", "", wpautop($question['explanation']))),
                'user_explanation' => ($question['user_explanation'] == null) ? '' : $question['user_explanation'],
                'answers' => $answers
            );
            
            $quests[] = $q;
		}

		$export_data = array(
			'status' => true,
			'type'   => 'xls',
			'data'   => $quests,
			'fields' => $questions_headers
		);
		return $export_data;
	}

	public function ays_questions_export_xlsx($questions) {
		global $wpdb;
		error_reporting(0);

        $quests = array();
		$questions_headers = array(
            array( 'text' => "id" ),
            array( 'text' => "category" ),
            array( 'text' => "question" ),
            array( 'text' => "question_title" ),
            array( 'text' => "question_image" ),
            array( 'text' => "question_hint" ),
            array( 'text' => "type" ),
            array( 'text' => "published" ),
            array( 'text' => "wrong_answer_text" ),
            array( 'text' => "right_answer_text" ),
            array( 'text' => "explanation" ),
            array( 'text' => "user_explanation" ),
            array( 'text' => "not_influence_to_score" ),
            array( 'text' => "weight" ),
            array( 'text' => "tags" ),
            array( 'text' => "answers" ),
            array( 'text' => "options" )
		);
        $quests[] = $questions_headers;
		foreach ( $questions as &$question ) {

            $question_id = (isset($question['id']) && $question['id'] != '') ? absint( $question['id'] ) : '';
            $category = (isset($question['category_title']) && $question['category_title'] != '') ? $question['category_title'] : 'Uncategorized';
            $question_content = esc_attr(stripslashes(str_replace("\n", "", ($question['question']))));
            $question_title = (isset($question['question_title']) && $question['question_title'] != '') ? strip_tags(stripslashes(str_replace("\n", "", ($question['question_title'])))) : '';
            $question_image = (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '';
            
            if( !empty( $question_image ) ){
                $question_image = Quiz_Maker_Data::ays_quiz_question_get_image_full_size_url_by_url($question_image);
            }

            $question_hint = esc_attr(stripslashes(str_replace("\n", "", ($question['question_hint']))));
            $type = (isset($question['type']) && $question['type'] != null) ? $question['type'] : 'radio';
            $published = (isset($question['published']) && $question['published'] != null) ? $question['published'] : 1;
            $wrong_answer_text = esc_attr(stripslashes(str_replace("\n", "", ($question['wrong_answer_text']))));
            $right_answer_text = esc_attr(stripslashes(str_replace("\n", "", ($question['right_answer_text']))));
            $explanation = esc_attr(stripslashes(str_replace("\n", "", ($question['explanation']))));
            $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
            $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
            $question_weight = (isset($question['weight']) && $question['weight'] != null) ? $question['weight'] : 1;
            $question_tags = (isset($question['tag_title']) && $question['tag_title'] != "") ? $question['tag_title'] : '';

            $answers = $this->get_question_answers($question_id);

            // foreach ( $answers as &$answer ) {
                // unset($answer['id']);
                // unset($answer['question_id']);
            // }

            $answers = json_encode($answers, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
			$answers = trim($answers, '[]');
            $options = (isset($question['options']) && $question['options'] != '') ? json_decode($question['options'], true) : array();
            if( gettype( $options ) != 'array' ){
                $options = array();
            }

            if(array_key_exists('author', $options)){
                unset($options['author']);
            }
            if(! empty($options) ){
                $options = json_encode($options);
            }else{
                $options = '';
            }
            
            $q = array(
                array( 'text' => $question_id ),
                array( 'text' => $category ),
                array( 'text' => $question_content ),
                array( 'text' => $question_title ),
                array( 'text' => $question_image ),
                array( 'text' => $question_hint ),
                array( 'text' => $type ),
                array( 'text' => $published ),
                array( 'text' => $wrong_answer_text ),
                array( 'text' => $right_answer_text ),
                array( 'text' => $explanation ),
                array( 'text' => $user_explanation ),
                array( 'text' => $not_influence_to_score ),
                array( 'text' => $question_weight ),
                array( 'text' => $question_tags ),
                array( 'text' => $answers ),
                array( 'text' => $options )
            );
            
            $quests[] = $q;
		}

		$export_data = array(
			'status' => true,
			'type'   => 'xlsx',
			'data'   => $quests
		);
		return $export_data;
	}

    public function ays_questions_export_simple_xlsx($questions) {
        global $wpdb;
        error_reporting(0);

        $quests = array();
        $ans_qanak = 0;
        foreach ( $questions as &$question ) {

            $question_content = htmlspecialchars(stripslashes(str_replace("\n", "", $question['question'])));
            $category = (isset($question['category_title']) && $question['category_title'] != '') ? $question['category_title'] : 'Uncategorized';

            $question_explanation = (isset($question['explanation']) && $question['explanation'] != '') ?htmlspecialchars(stripslashes(str_replace("\n", "", $question['explanation']))) : '';

            $answers = $this->get_question_answers($question['id']);

            $q = array(
                array( 'text' => $question_content ),
                array( 'text' => $category ),
                array( 'text' => $question_explanation ),
            );

            $q_answers = array();
            $ans_krug = 1;

            foreach ( $answers as &$answer ) {
                if ($answer['correct'] != 1) {
                    $ans_krug++;
                }else{
                    $correct_answer = array(array("text" => $ans_krug));
                }
                $q_answers['text']  = htmlspecialchars(stripslashes(str_replace("\n", "", $answer['answer'])));
                array_push($q,$q_answers);
            }

            if ($ans_qanak < $ans_krug) {
                $ans_qanak = $ans_krug;
            }

            array_splice($q,3,0,$correct_answer);
            $quests[] = $q;
        }

        $questions_headers = array(
            array( 'text' => "Question" ),
            array( 'text' => "Category" ),
            array( 'text' => "Question explanation" ),
            array( 'text' => "Correct answer" ),
        );

        $h_answers = array();
        for ($i=1; $i <= $ans_qanak; $i++) {
            $h_answers = array("text" => 'Answer '.$i);
            array_push($questions_headers, $h_answers);
        }

        array_unshift($quests, $questions_headers);

        $export_data = array(
            'status' => true,
            'type'   => 'xlsx',
            'data'   => $quests
        );
        return $export_data;
    }

	public function ays_questions_export_json($questions) {
		global $wpdb;
		error_reporting(0);

		foreach ( $questions as &$question ) {
            $category = (isset($question['category_title']) && $question['category_title'] != '') ? $question['category_title'] : 'Uncategorized';
            $question['category'] = $category;

            $question_image = (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '';
            if( !empty( $question_image ) ){
                $question['question_image'] = Quiz_Maker_Data::ays_quiz_question_get_image_full_size_url_by_url($question_image);
            }

            unset($question['category_title']);
            unset($question['category_id']);
            $answers = $this->get_question_answers($question['id']);
            // foreach ( $answers as &$answer ) {
                // unset($answer['id']);
                // unset($answer['question_id']);
            // }
            $options = (isset($question['options']) && $question['options'] != '') ? json_decode($question['options'], true) : array();
            if( gettype( $options ) != 'array' ){
                $options = array();
            }

            if(array_key_exists('author', $options)){
                unset($options['author']);
            }
            if(! empty($options) ){
                $question['options'] = json_encode($options);
            }else{
                $question['options'] = '';
            }
			$question['answers'] = $answers;
            // unset($question['id']);
		}
		$response = array(
			'status' => true,
			'data'   => $questions,
			"type"   => 'json'
		);
		return $response;
	}
    
    public function ays_questions_statistics_export() {
        error_reporting(0);
        global $wpdb;

        $quiz_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $quizes_questions_table = $wpdb->prefix . 'aysquiz_questions';
        $sql = "SELECT question_ids FROM {$quizes_table} WHERE id = {$quiz_id};";
        $results = $wpdb->get_var( $sql);
        $questions_ids = array();
        $questions_counts = array();
        $questions_list = array();
        if($results != ''){
            $results = explode("," , $results);
            foreach ($results as $key){
                $questions_ids[$key] = 0;
                $questions_counts[$key] = 0;
                $sql = "SELECT question FROM {$quizes_questions_table} WHERE id = {$key} ; ";
                $questions_list[$key] = $wpdb->get_var( $sql);
            }
        }

        $quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT options FROM {$quizes_reports_table} WHERE quiz_id ={$quiz_id} AND `status` = 'finished';";
        $report = $wpdb->get_results( $sql, 'ARRAY_A' );
        if(! empty($report)){
            foreach ($report as $key){
                $report = json_decode($key["options"]);
                $questions = $report->correctness;
                foreach ($questions as $i => $v){
                    $q = (int) substr($i,12);
                    if(isset($questions_ids[$q])) {
                        if ($v) {
                            $questions_ids[$q]++;
                        }

                        $questions_counts[$q]++;
                    }
                }
            }
        }

        $quests = array();
        $questions_headers = array(
            array( 'text' => "question" ),
            array( 'text' => "correctness" ),
            array( 'text' => "correct_answers" ),
            array( 'text' => "answered_questions" )
        );
        $export_data = array();
        $quests[] = $questions_headers;

        foreach ($questions_ids as $n => $a){
            if ($a != 0 ||  $questions_counts[$n] != 0){
                $score = round($a/$questions_counts[$n]*100, 1) . "%";
            }else {
                $score = 0 . "%";
            }

            $q = array(
                array( 'text' => strip_tags(stripslashes(str_replace("\n", "", wpautop($questions_list[$n])))) ),
                array( 'text' => $score ),
                array( 'text' => $a ),
                array( 'text' => $questions_counts[$n] )
            );

            $quests[] = $q;
        }

        $export_data = array(
            'status' => true,
            'type'   => 'xlsx',
            'data'   => $quests
        );

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($export_data);
        wp_die();
    }

    public function ays_single_question_results_export() {
        global $wpdb;
        error_reporting(0);
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_single_question_results_export') {

            $id      = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id}", "ARRAY_A");
            $user_id = intval($results['user_id']);
            $quiz_id = intval($results['quiz_id']);


            $user            = get_user_by('id', $user_id);
            $user_ip         = $results['user_ip'];
            $options         = json_decode($results['options']);
            $user_attributes = $options->attributes_information;
            $start_date      = $results['start_date'];
            $duration        = $options->passed_time;
            $rate_id         = isset($options->rate_id) ? $options->rate_id : null;
            $rate            = $this->ays_quiz_rate($rate_id);
            $calc_method     = isset($options->calc_method) ? $options->calc_method : 'by_correctness';
            $correctness     = (array)$options->correctness;

            if(!isset($options->user_points)){
                $options->user_points = array_sum($correctness);
            }

            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';

            $score      = ($calc_method == 'by_points') ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';
            $user       = ($user_id === 0) ? __( "Guest", $this->plugin_name ) : $user->data->display_name;
            $review     = (isset($rate['review']) && $rate['review'] != null) ? stripslashes(html_entity_decode(str_replace("\n", "", (strip_tags($rate['review']) )))) : '';
            $email      = (isset($results['user_email']) && $results['user_email'] !== '') ? stripslashes($results['user_email']) : '';
            $user_name  = (isset($results['user_name']) && $results['user_name'] !== '') ? stripslashes($results['user_name']) : '';
            $user_phone = (isset($results['user_phone']) && $results['user_phone'] !== '') ? stripslashes($results['user_phone']) : '';

            $res_question_title_arr = ( isset($options->questions_title) && !empty($options->questions_title) ) ? (array)$options->questions_title : array();


            $json    = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            $country = $json->country;
            $region  = $json->region;
            $city    = $json->city;
            $from    = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;

            $user_ip_header = __( "User IP", $this->plugin_name );
            if ($user_ip == '') {
                $from = '';
                $user_ip_header = '';
            }

            $quests      = array();
            $export_data = array();

            $user_information = array(
                array('text' => __( 'User Information', $this->plugin_name ) )
            );
            $quests[] = $user_information;
            $user_information_headers = array(
                $user_ip_header,
                __( "User ID", $this->plugin_name ),
                __( "User", $this->plugin_name ),
                __( "Email", $this->plugin_name ),
                __( "Name", $this->plugin_name ),
                __( "Phone", $this->plugin_name )
            );
            $user_information_results = array(
                $from,
                $user_id."",
                $user,
                $email,
                $user_name,
                $user_phone
            );
            foreach ($user_information_headers as $key => $value) {
                if ($user_information_results[$key] != '') {
                    $user_results = array(
                        array( 'text' => $user_information_headers[$key] ),
                        array( 'text' => $user_information_results[$key] )
                    );
                    $quests[] = $user_results;
                }
            }

            if ($user_attributes !== null) {
                $user_attributes = (array)$user_attributes;

                foreach ($user_attributes as $name => $value) {
                    if(stripslashes($value) == ''){
                        $attr_value = '-';
                    }else{
                        $attr_value = stripslashes($value);
                    }

                    if($attr_value == 'on'){
                        $attr_value = __('Checked',$this->plugin_name);
                    }
                    $custom_fild = array(
                        array( 'text' => stripslashes($name) ),
                        array( 'text' => $attr_value )
                    );
                    $quests[] = $custom_fild;
                }
            }

            $quests[] = array(
                array( 'text' => '' ),
            );

            $quiz_information = array(
                array('text' => __( 'Quiz Information', $this->plugin_name ) )
            );

            $quests[] = $quiz_information;
            $quiz_information_headers = array(
                __( "Start date", $this->plugin_name ),
                __( "Duration", $this->plugin_name ),
                __( "Score", $this->plugin_name ),
                __( "Rate", $this->plugin_name )
            );
            $quiz_information_results = array(
                $start_date,
                $duration,
                $score,
                $review
            );
            foreach ($quiz_information_headers as $key => $value) {
                if ($quiz_information_results[$key] != '') {
                    $user_results = array(
                        array( 'text' => $quiz_information_headers[$key] ),
                        array( 'text' => $quiz_information_results[$key] )
                    );
                    $quests[] = $user_results;
                }

            }

            $quests[] = array(
                array( 'text' => '' ),
            );

            $questions_headers = array(
                array( 'text' => __( "Questions", $this->plugin_name ) ),
            );

            if( $calc_method == 'by_correctness' ){
                $questions_headers[] = array( 'text' => __( "Correct answers", $this->plugin_name ) );
            }

            $questions_headers[] = array( 'text' => __( "User answers", $this->plugin_name ) );

            if( $calc_method == 'by_correctness' ){
                $questions_headers[] = array( 'text' => __( "Status", $this->plugin_name ) );
            }elseif( $calc_method == 'by_points' ){
                $questions_headers[] = array( 'text' => __( "Answer point", $this->plugin_name ) );
            }

            $quests[] = $questions_headers;
            $index = 1;
            $user_exp = array();
        //    if($results['user_explanation'] != '' || $results['user_explanation'] !== null){
        //        $user_exp = json_decode($results['user_explanation'], true);
        //    }

            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id     = absint(intval(explode('_', $key)[2]));
                    $question_content = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");

                    if ( is_null( $question_content ) || empty( $question_content )  ) {
                        continue;
                    }

                    $qoptions = isset($question_content['options']) && $question_content['options'] != '' ? json_decode($question_content['options'], true) : array();

                    $question_type = isset($question_content['type']) && $question_content['type'] != '' ? sanitize_text_field( $question_content['type'] ) : 'radio';

                    $correct_answers = $this->get_correct_answers($question_id);
                    $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);
                    $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );
                    $is_text_type = $this->question_is_text_type($question_id);
                    $text_type = $this->text_answer_is($question_id);
                    $not_multiple_text_types = array("number", "date");

                    // Incorrect matches for answers
                    $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                    $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                    if($this->question_is_text_type($question_id)){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                    }elseif( $question_type == "fill_in_blank" ){
                        $user_answered = $this->get_user_fill_in_blank_answered($options->user_answered, $key);
                    }elseif( $is_matching_type ){
                        $user_answered = $this->get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                        $correct_answers = $this->get_correct_answers_for_matching_type($question_id);
                    }else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                    }

                    if ( ! $is_matching_type && is_array( $user_answered ) ) {
                        if ( isset( $user_answered['status'] ) && $user_answered['status'] == false ) {
                            // $user_answered_empty_text = (isset( $user_answered['message'] ) && $user_answered['message'] != "") ? sanitize_text_field( $user_answered['message'] ) : "";

                            $user_answered = "-";
                        }
                    }

                    $successed_or_failed = ($option == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );

                    $question = isset( $question_content["question"]) && $question_content["question"] != '' ? html_entity_decode(strip_tags(stripslashes($question_content["question"]))) : '';

                    if( !empty( $res_question_title_arr ) ){
                        $question = isset( $res_question_title_arr[$question_id] ) && $res_question_title_arr[$question_id] != '' ? html_entity_decode(strip_tags(stripslashes($res_question_title_arr[$question_id]))) : $question;
                    }

                    if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                        $c_answers = explode('%%%', $correct_answers);
                        if(!empty($c_answers)){
                            $correct_answers = trim($c_answers[0]);
                        }
                    }elseif( $question_type == "fill_in_blank" ){

                        $fill_in_blank_question_title_correct = $question;

                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            $answer_html = $corect_answer;

                            $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                        }


                        $correct_answers = $fill_in_blank_question_title_correct;

                        //////////////////////////////////////////


                        $fill_in_blank_question_title_user_answer = $question;
                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                $answer_html = $user_answer;
                            } elseif( $user_answer == "" ){
                                $answer_html = "-";
                            } else {
                                $answer_html = $user_answer;
                            }


                            $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                        }

                        $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );


                    }else{
                        if($text_type == 'date'){
                            $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                        }
                    }

                    $questions = array(
                        array( 'text' => $index .". ". $question ),
                    );

                    if( $is_matching_type ) {
                        foreach ( $correct_answers as $key => $_correct_answer ) {
                            if( $key > 0 ){
                                $questions = array(
                                    array( 'text' => "" ),
                                );
                            }

                            $correct_answer_text = html_entity_decode( strip_tags( stripslashes( $_correct_answer ) ) );
                            $user_answer    = html_entity_decode( strip_tags( stripslashes( $user_answered[$key]['answer'] ) ) );

                            if( $calc_method == 'by_correctness' ){
                                $questions[] = array( 'text' => $correct_answer_text );
                            }

                            $questions[] = array( 'text' => $user_answer );

                            $successed_or_failed = ($user_answered[$key]['correct'] == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );

                            if( $calc_method == 'by_correctness' ){
                                $questions[] = array( 'text' => $successed_or_failed );
                            }elseif( $calc_method == 'by_points' ){
                                $questions[] = array( 'text' => $option );
                            }

                            $quests[] = $questions;
                        }
                    } else {
                        $correct_answer = html_entity_decode( strip_tags( stripslashes( $correct_answers ) ) );
                        $user_answer    = html_entity_decode( strip_tags( stripslashes( $user_answered ) ) );

                        if( $calc_method == 'by_correctness' ){
                            $questions[] = array( 'text' => $correct_answer );
                        }

                        $questions[] = array( 'text' => $user_answer );

                        if( $calc_method == 'by_correctness' ){
                            $questions[] = array( 'text' => $successed_or_failed );
                        }elseif( $calc_method == 'by_points' ){
                            $questions[] = array( 'text' => $option );
                        }

                        $quests[] = $questions;
                    }
                }

                $index++;
            }

            $export_data = array(
                'status' => true,
                'type'   => 'xlsx',
                'data'   => $quests
            );

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode($export_data);
            wp_die();
        }
    }

    public function ays_export_result_pdf() {
        global $wpdb;
        error_reporting(0);
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $quizzes_table = $wpdb->prefix . "aysquiz_quizes";

        $pdf_response = null;
        $pdf_content = null;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_export_result_pdf') {

            $id      = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id} AND `status` = 'finished';", "ARRAY_A");
            $user_id = intval($results['user_id']);
            $quiz_id = intval($results['quiz_id']);

            $user            = get_user_by('id', $user_id);
            $user_ip         = $results['user_ip'];
            $options         = json_decode($results['options']);
            $user_attributes = $options->attributes_information;
            $start_date      = $results['start_date'];
            $duration        = $options->passed_time;
            $rate_id         = isset($options->rate_id) ? $options->rate_id : null;
            $rate            = $this->ays_quiz_rate($rate_id);
            $calc_method     = isset($options->calc_method) ? $options->calc_method : 'by_correctness';
            $correctness     = (array)$options->correctness;

            if(!isset($options->user_points)){
                $options->user_points = array_sum($correctness);
            }

            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';

            $score      = ($calc_method == 'by_points') ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';
            $user       = ($user_id === 0) ? __( "Guest", $this->plugin_name ) : $user->data->display_name;
            $review     = (isset($rate['review']) && $rate['review'] != null) ? stripslashes(html_entity_decode(str_replace("\n", "", (strip_tags($rate['review']) )))) : '';
            $email      = (isset($results['user_email']) && $results['user_email'] !== '') ? stripslashes($results['user_email']) : '';
            $user_name  = (isset($results['user_name']) && $results['user_name'] !== '') ? stripslashes($results['user_name']) : '';
            $user_phone = (isset($results['user_phone']) && $results['user_phone'] !== '') ? stripslashes($results['user_phone']) : '';
            $unique_code = (isset($results['unique_code']) && $results['unique_code'] !== '') ? strtoupper($results['unique_code']) : '';


            $json    = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            $country = $json->country;
            $region  = $json->region;
            $city    = $json->city;
            $from    = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;

            if ($user_ip == '') {
                $from = '';
            }

            $quests      = array();
            $export_data = array();

            $data_headers   = array();
            $data_questions = array();

            $data_headers['user_data'] = array(
                'api_user_information_header' => __( "User Information", $this->plugin_name ),

                'api_user_ip_header'     => __( "User IP", $this->plugin_name ),
                'api_user_id_header'     => __( "User ID", $this->plugin_name ),
                'api_user_header'        => __( "User", $this->plugin_name ),
                'api_user_mail_header'   => __( "Email", $this->plugin_name ),
                'api_user_name_header'   => __( "Name", $this->plugin_name ),
                'api_user_phone_header'  => __( "Phone", $this->plugin_name ),
                'api_checked_header'     => __( "Checked", $this->plugin_name ),

                'api_quiz_information_header' => __( "Quiz Information", $this->plugin_name ),

                'api_user_ip'     =>  $from,
                'api_user_id'     =>  $user_id."",
                'api_user'        =>  $user,
                'api_user_mail'   =>  $email,
                'api_user_name'   =>  $user_name,
                'api_user_phone'  =>  $user_phone,

                'api_start_date_header' =>  __( "Start date", $this->plugin_name ),
                'api_duration_header'   =>  __( "Duration", $this->plugin_name ),
                'api_score_header'      =>  __( "Score", $this->plugin_name ),
                'api_rate_header'       =>  __( "Rate", $this->plugin_name ),

                'api_start_date' =>  $start_date,
                'api_duration'   =>  $duration,
                'api_score'      =>  $score,
                'api_rate'       =>  $review,
            );

            if ($user_attributes !== null) {
                $user_attributes = (array)$user_attributes;

                foreach ($user_attributes as $name => $value) {
                    if(stripslashes($value) == ''){
                        $attr_value = '-';
                    }else{
                        $attr_value = stripslashes($value);
                    }

                    if($attr_value == 'on'){
                        $attr_value = __('Checked',$this->plugin_name);
                    }
                    $custom_fild = array(
                        'api_custom_fild_name'  => stripslashes($name),
                        'api_custom_fild_value' => $attr_value,
                    );
                    $quests[] = $custom_fild;
                }
            }
            $data_headers['custom_fild'] = $quests;

            $data_questions['headers'] = array(
                'api_glob_question_header'  => __( "Questions", $this->plugin_name ),
                'api_question_header'       => __( "Question", $this->plugin_name ),
                'api_correct_answer_header' => __( "Correct answer", $this->plugin_name ),
                'api_user_answer_header'    => __( "User answered", $this->plugin_name ),
                'api_user_answer_points'    => __( "Answer point", $this->plugin_name ),
            );

            $quests = array();
            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id     = absint(intval(explode('_', $key)[2]));
                    $question_content = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");

                    if ( is_null( $question_content ) || empty( $question_content )  ) {
                        continue;
                    }

                    $question_type = isset($question_content['type']) && $question_content['type'] != '' ? sanitize_text_field( $question_content['type'] ) : 'radio';
                    $qoptions = isset($question_content['options']) && $question_content['options'] != '' ? json_decode($question_content['options'], true) : array();

                    $correct_answers = $this->get_correct_answers($question_id);
                    $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);
                    $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );

                    // Incorrect matches for answers
                    $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                    $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                    if($this->question_is_text_type($question_id)){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                    }elseif( $question_type == "fill_in_blank" ){
                        $user_answered = $this->get_user_fill_in_blank_answered($options->user_answered, $key);
                    }elseif( $is_matching_type ){
                        $user_answered = $this->get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                        $correct_answers = $this->get_correct_answers_for_matching_type($question_id);
                    }else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                    }

                    $successed_or_failed = ($option == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );
                    $question       = esc_attr(stripslashes($question_content["question"]));

                    if( $question_type == "fill_in_blank" ){

                        $fill_in_blank_question_title_correct = $question;

                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            $answer_html = $corect_answer;

                            $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                        }


                        $correct_answers = $fill_in_blank_question_title_correct;

                        //////////////////////////////////////////


                        $fill_in_blank_question_title_user_answer = $question;
                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                $answer_html = $user_answer;
                            } elseif( $user_answer == "" ){
                                $answer_html = "-";
                            } else {
                                $answer_html = $user_answer;
                            }


                            $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                        }

                        $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );
                    } 

                    if( $is_matching_type ){
                        $question               = esc_attr( stripslashes( $question_content["question"] ) );
                        $successed_or_failed    = '';
                        $correct_answer         = '';
                        $user_answer            = '';
                        foreach ( $correct_answers as $key => $_correct_answer ) {
                            $user_answered_text = $user_answered[$key]['answer'];
                            if ( $user_answered_text === '' ) {
                                $user_answered_text = ' - ';
                            }

                            $separator = ", ";
                            if( $key  === count( $correct_answers ) - 1 ){
                                $separator = "";
                            }

                            $successed_or_failed .= ( $user_answered[$key]['correct'] == true ) ? __( "Succeed", $this->plugin_name ) . $separator : __( "Failed", $this->plugin_name ) . $separator;
                            $correct_answer      .= html_entity_decode( strip_tags( stripslashes( $_correct_answer ) ) ) . $separator;
                            $user_answer         .= html_entity_decode( strip_tags( stripslashes( $user_answered[$key]['answer'] ) ) ) . $separator;
                        }

                        $questions      = array(
                            'api_question'       => $question,
                            'api_correct_answer' => $correct_answer,
                            'api_user_answer'    => $user_answer,
                            'api_status'         => $successed_or_failed,
                            'api_check_status'   => $option,
                            'api_calc_method'    => $calc_method,
                        );

                        $quests[] = $questions;
                    } else {
                        if ($user_answered == '' || ( isset($user_answered['status']) && $user_answered['status'] == false ) ) {
                            $user_answered = ' - ';
                        }

                        $correct_answer = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                        $user_answer    = html_entity_decode(strip_tags(stripslashes($user_answered)));
                        $questions = array(
                            'api_question'       => $question,
                            'api_correct_answer' => $correct_answer,
                            'api_user_answer'    => $user_answer,
                            'api_status'         => $successed_or_failed,
                            'api_check_status'   => $option,
                            'api_calc_method'    => $calc_method,
                        );

                        $quests[] = $questions;
                    }
                }
            }
            $data_questions['data_question'] = $quests;

            $pdf = new Quiz_PDF_API();
            $export_data = array(
                'status'          => true,
                'type'            => 'pdfapi',
                'api_quiz_id'     => $quiz_id,
                'data_headers'    => $data_headers,
                'data_questions'  => $data_questions
            );

            $pdf_response = $pdf->generate_report_PDF($export_data);
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

    public function ays_answers_statistics_export() {
        global $wpdb;
        error_reporting(0);
        $results_table   = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $quizes_table    = $wpdb->prefix . "aysquiz_quizes";

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_answers_statistics_export') {
            $this->quizes_obj = new Quizes_List_Table($this->plugin_name);

            $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? intval($_REQUEST['quiz_id']) : null;
            if($quiz_id === null){
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => false,
                ));
                wp_die();
            }

            $gusets_in_sql1 = '';
            $flag = true;
            $only_guests = false;
            if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == 'true' ) {
                $flag = false;
                $gusets_in_sql1 = ' AND user_id != 0 ';
            }

            if (isset($_REQUEST['only_guests']) && $_REQUEST['only_guests'] == 'true' ) {
                $only_guests = true;
                $gusets_in_sql1 = ' AND user_id = 0 ';
            }

            $quiz_id = ($quiz_id !== null) ? $quiz_id : "SELECT quiz_id FROM {$results_table}";

            if( isset($_REQUEST['user_id']) && ($_REQUEST['user_id'] != null || $_REQUEST['user_id'] != '') ){
                if( $flag === true ){
                    $_REQUEST['user_id'][] = '0';
                }
                $user_id = implode(',', $_REQUEST['user_id']);

                if( $only_guests === true ){
                    $user_id = 0;
                }
            }else{
                $user_id = "SELECT user_id FROM {$results_table} WHERE quiz_id IN ($quiz_id) $gusets_in_sql1";
            }

            $date_from = (isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '') ? $_REQUEST['date_from'] : '2000-01-01';
            $date_to = (isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '') ? $_REQUEST['date_to'] : current_time('Y-m-d');
            $sql = "SELECT *
                    FROM {$results_table}
                    WHERE
                        user_id IN ($user_id) AND
                        quiz_id IN ($quiz_id) AND
                        start_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND `status` = 'finished'
                    ORDER BY id DESC";
            $results = $wpdb->get_results($sql);

            $sql = "SELECT * FROM {$quizes_table} WHERE id = $quiz_id";
            $quiz_data = $wpdb->get_row($sql, "ARRAY_A");

            $options = (isset( $quiz_data['options'] ) && $quiz_data['options'] != '' ) ? json_decode($quiz_data['options'], true) : array();

            // Pass score of the quiz
            $quiz_pass_score = (isset($options['pass_score']) && $options['pass_score'] != "") ? intval($options['pass_score']) : 0;

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

            $quest       = array();
            $users       = array();
            $users_data  = array();
            $quest_data  = array();
            $user_attr_data   = array();
            $user_score_data  = array();
            foreach ($results as $key => $result) {
                $row_id    = intval($result->id);
                $user_id   = intval($result->user_id);
                $user      = get_user_by('id', $user_id);
                $user      = ($user_id === 0) ? 'Guest' : $user->data->display_name;
                $email     = (isset($result->user_email) && ($result->user_email !== '' || $result->user_email !== null)) ? stripslashes($result->user_email) : '';
                $user_nam  = (isset($result->user_name) && ($result->user_name !== '' || $result->user_name !== null)) ? stripslashes($result->user_name) : '';
                $user_phone  = (isset($result->user_phone) && ($result->user_phone !== '' || $result->user_phone !== null)) ? stripslashes($result->user_phone) : '';

                $score   = (isset($result->score) && ($result->score !== '' || $result->score !== null)) ? stripslashes($result->score) : '';
                $points  = (isset($result->points) && ($result->points !== '' || $result->points !== null)) ? stripslashes($result->points) : '';

                $status = '';
                if( $quiz_pass_score != 0 ){
                    if( $score >= $quiz_pass_score ){
                        $status = __( "Passed", $this->plugin_name );
                    }else{
                        $status = __( "Failed", $this->plugin_name );
                    }
                }

                $user_name = html_entity_decode(strip_tags(stripslashes($user_nam)));
                $options   = json_decode($result->options);

                $user_attributes = (isset($options->attributes_information) && ( $options->attributes_information !== '' || $options->attributes_information !== null ) ) ? $options->attributes_information : null;

                $user_attributes_tvyal = array();

                if ($user_attributes !== null) {
                    $user_attributes = (array)$user_attributes;

                    uksort($user_attributes, function($key1, $key2) use ($quiz_attributes_active_order) {
                        return ((array_search($key1, $quiz_attributes_active_order) > array_search($key2, $quiz_attributes_active_order)) ? 1 : -1);
                    });
                }

                foreach ($custom_fields_active as $key => $custom_field) {

                    if ( $custom_field['slug'] == 'ays_form_name' ) {
                        $user_attributes_tvyal[] = array( 'text' => $user_nam );
                        continue;
                    }

                    if ( $custom_field['slug'] == 'ays_form_email' ) {
                        $user_attributes_tvyal[] = array( 'text' => $email );
                        continue;
                    }

                    if ( $custom_field['slug'] == 'ays_form_phone' ) {
                        $user_attributes_tvyal[] = array( 'text' => $user_phone );
                        continue;
                    }

                    if ($user_attributes !== null) {
                        if( array_key_exists($custom_field['name'], $user_attributes) ){
                            $value = $user_attributes[ $custom_field['name'] ];
                            if(stripslashes($value) == ''){
                                $attr_value = '';
                            }else{
                                $attr_value = stripslashes($value);
                            }

                            if($attr_value == 'on'){
                                $attr_value = __('Checked',$this->plugin_name);
                            }
                            $custom_fild = array( 'text' => $attr_value );
                        } else {
                            $custom_fild = array( 'text' => '' );
                        }
                    }else {
                        $custom_fild = array( 'text' => '' );
                    }

                    $user_attributes_tvyal[] = $custom_fild;
                }
                $user_attr_data[  ] = $user_attributes_tvyal;

                $user_score_tvyal = array(
                    'score'  => $score,
                    'points' => $points,
                    'status' => $status,
                );

                $user_score_data[] = $user_score_tvyal;

                if ($user == 'Guest') {
                    if ($user_name == '' && $email == '') {
                        $user = __('Guest', $this->plugin_name);
                    } else {
                        $user_name_arr = array(
                            __('Guest', $this->plugin),
                        );

                        if ($user_name != '') {
                            $user_name_arr[] = $user_name;
                        }

                        if ($email != '') {
                            $user_name_arr[] = $email;
                        }

                        $user = implode(' - ', $user_name_arr);
                    }
                }
                $users[] = $user;

                $user_tvyal  = array();
                foreach ($options->correctness as $key => $option) {

                    if (strpos($key, 'question_id_') !== false) {

                        $question_id      = absint(intval(explode('_', $key)[2]));
                        $question_content = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");

                        if ( is_null( $question_content ) || empty( $question_content )  ) {
                            continue;
                        }

                        $question_type = isset($question_content['type']) && $question_content['type'] != '' ? sanitize_text_field( $question_content['type'] ) : 'radio';
                        $qoptions = isset($question_content['options']) && $question_content['options'] != '' ? json_decode($question_content['options'], true) : array();

                        $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);
                        $correct_answers  = $this->get_correct_answers($question_id);
                        $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );

                        // Incorrect matches for answers
                        $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                        $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                        if($this->question_is_text_type($question_id)){
                            $user_answered = $this->get_user_text_answered($options->user_answered, $key); 
                        } elseif( $question_type == 'fill_in_blank' ){
                            $user_answered = $this->get_user_fill_in_blank_answered($options->user_answered, $key);
                        }elseif( $is_matching_type ){
                            $user_answered = $this->get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                            $correct_answers = $this->get_correct_answers_for_matching_type($question_id);
                        }else{
                            $user_answered = $this->get_user_answered($options->user_answered, $key);
                        }

                        $question = (isset( $question_content["question"] ) && $question_content["question"] != "") ? esc_attr(strip_tags(stripslashes($question_content["question"]))) : "";

                        if ( ! $is_matching_type && is_array( $correct_answers ) && isset( $correct_answers['message'] ) ) {
                            $correct_answer  = (isset( $correct_answers['message'] ) && $correct_answers['message'] != "") ? $correct_answers['message'] : "";
                        } elseif( $question_type == "fill_in_blank" ){

                            $fill_in_blank_question_title_correct = $question;

                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                $answer_html = $corect_answer;

                                $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);

                            }

                            $correct_answer = $fill_in_blank_question_title_correct;


                        } elseif ( $is_matching_type ) {
                            $correct_answer = array();
                            foreach ( $correct_answers as $key => $answer ) {
                                $correct_answer[] = html_entity_decode( strip_tags( stripslashes( $answer ) ) );
                            }
                        } else {
                            $correct_answer  = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                        }

                        if ( ! $is_matching_type && is_array( $user_answered ) && isset( $correct_answers['message'] ) ) {
                            // $user_answer  = (isset( $user_answered['message'] ) && $user_answered['message'] != "") ? $user_answered['message'] : "";
                            $user_answer  = "";
                        } elseif ( $question_type == "fill_in_blank" ) {
                            
                            $fill_in_blank_question_title_user_answer = $question;
                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                    $answer_html = $user_answer;
                                } elseif( $user_answer == "" ){
                                    $answer_html = "-";
                                } else {
                                    $answer_html = $user_answer;
                                }


                                $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                            }

                            if( is_string( $fill_in_blank_question_title_user_answer ) ){
                                $user_answer = stripslashes( $fill_in_blank_question_title_user_answer );
                            } else {
                                $user_answer = "";
                            }

                        } elseif ( $is_matching_type ) {
                            $user_answer = array();
                            foreach ( $user_answered as $__key => $__answer ) {
                                if( is_string( $__answer['answer'] ) ){
                                    $user_answer[] = html_entity_decode( strip_tags( stripslashes( $__answer['answer'] ) ) );
                                }
                            }
                        }else {
                            if( is_string( $user_answered ) ){
                                $user_answer  = html_entity_decode(strip_tags(stripslashes($user_answered)));
                            } else {
                                $user_answer  = "";
                            }
                        }

                        $q               = $question;
                        $correct_answ    = $correct_answer;
                        $answers         = $user_answer;
                        $quest_data[strval($question_id)] = array(
                            'question'       => $q,
                            'correct_answer' => $correct_answ,
                            'type' => $is_matching_type ? 'matching' : 'default',
                        );
                        $user_tvyal[strval($question_id)] = $answers;
                    }
                }
                $users_data[] = $user_tvyal;
            }

            $headers = array(
                array( 'text' => __( "User Information", $this->plugin_name ) ),
                array( 'text' => '' ),
            );
            foreach ($users_data as $k => $v) {
                $headers[] = array( 'text' => $users[$k] );
            }

            $attributes = array();
            $attributes_headers = array(
                array( 'text' => __( "Custom Fields", $this->plugin_name ) ),
            );
            $user_attributes_quest = array();
            if (! is_null( $custom_fields_active ) && ! empty($custom_fields_active) ) {

                $custom_fields_index = 0;
                foreach ($custom_fields_active as $qid => $custom_field) {

                    $user_attributes_quest = array(
                        array( 'text' => $custom_field['name'] ),
                        array( 'text' => '' ),
                    );
                    foreach ($user_attr_data as $kkk => $attr_data) {
                        if (! isset( $attr_data[$custom_fields_index] ) ) {
                            $attr_data[$qid] = '';
                        }

                        $user_attributes_quest[] = $attr_data[$custom_fields_index];

                    }
                    $attributes[] = $user_attributes_quest;
                    $custom_fields_index++;

                }
            }

            $score_data          = array();
            $score_data_arr      = array();
            if (! is_null( $user_score_data ) && ! empty($user_score_data) ) {

                $user_score_data_header = array(
                    'score'  => __( "Score", $this->plugin_name ),
                    'points' => __( "Points", $this->plugin_name ),
                    'status' => __( "Status", $this->plugin_name ),
                );

                foreach ($user_score_data_header as $score_key => $score_data_header) {

                    $score_data_arr = array(
                        array( 'text' => $score_data_header ),
                        array( 'text' => '' ),
                    );

                    foreach ($user_score_data as $kk => $user_score) {
                        $score_data_arr[] = array( 'text' => $user_score[ $score_key ] );
                    }

                    $score_data[] = $score_data_arr;
                }
            }

            $questions = array();
            $user_headers = array();
            $user_answered_quest = array();

            foreach ($quest_data as $qid => $question) {
                $type = isset( $question['type'] ) && ! empty( $question['type'] ) ? $question['type'] : 'default';

                if( $type === 'matching' ){
                    foreach ( $question['correct_answer'] as $key => $correct_answer ) {
                        if ( $key === 0 ) {
                            $user_answered_quest = array(
                                array( 'text' => $question['question'] ),
                                array( 'text' => $correct_answer ),
                            );
                        } else {
                            $user_answered_quest = array(
                                array( 'text' => '' ),
                                array( 'text' => $correct_answer ),
                            );
                        }

                        foreach ( $users_data as $user => $usr_ans ) {
                            $answers               = array( 'text' => $usr_ans[ $qid ][$key] );
                            $user_answered_quest[] = $answers;
                        }
                        $questions[] = $user_answered_quest;
                    }
                }else {
                    $user_answered_quest = array(
                        array( 'text' => $question['question'] ),
                        array( 'text' => $question['correct_answer'] ),
                    );

                    foreach ( $users_data as $user => $usr_ans ) {
                        $answers               = array( 'text' => $usr_ans[ $qid ] );
                        $user_answered_quest[] = $answers;
                    }
                    $questions[] = $user_answered_quest;
                }
            }

            $quest[] = $headers;
            $quest[] = array(
                array( 'text' => '' ),
            );

            if (! is_null( $custom_fields_active ) && ! empty($custom_fields_active) ) {

                for ($i=0; $i < count($attributes) ; $i++) {
                    $quest[] = $attributes[$i];
                }

                $quest[] = array(
                    array( 'text' => '' ),
                );

            }

            if ( ! empty( $score_data ) ) {

                foreach ($score_data as $key => $s_data) {
                    $quest[] = $s_data;
                }

                $quest[] = array(
                    array( 'text' => '' ),
                );
            }

            $questions_headers = array(
                array( 'text' => __( "Questions", $this->plugin_name ) ),
                array( 'text' => __( "Correct answers", $this->plugin_name ) ),
            );

            foreach ($users_data as $k => $v) {
                $questions_headers[] = array( 'text' => __( "User answers", $this->plugin_name ) );
            }
            $quest[] = $questions_headers;

            $quest[] = array(
                array( 'text' => '' ),
            );

            for ($i=0; $i < count($questions) ; $i++) {
                $quest[] = $questions[$i];
            }

            $export_data = array(
                'status' => true,
                'type'   => 'xlsx',
                'data'   => $quest
            );

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode($export_data);
            wp_die();
        }
    }

    public function ays_results_export_filter(){
        global $wpdb;
        error_reporting(0);
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        $gusets_in_sql1 = '';
        $gusets_in_sql2 = '';
        $flag = true;
        $only_guests = false;
        if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == 'true' ) {
            $flag = false;
            $gusets_in_sql1 = ' WHERE user_id != 0 ';
            $gusets_in_sql2 = ' AND rp.user_id != 0 ';
        }

        if (isset($_REQUEST['with_guests']) && $_REQUEST['with_guests'] == 'true' ) {
            $flag = false;
            $gusets_in_sql1 = ' WHERE user_id != 0 ';
            $gusets_in_sql2 = ' AND rp.user_id != 0 ';
        }

        if (isset($_REQUEST['only_guests']) && $_REQUEST['only_guests'] == 'true' ) {
            $only_guests = true;
            $gusets_in_sql1 = ' WHERE user_id = 0 ';
            $gusets_in_sql2 = ' AND rp.user_id = 0 ';
        }

        $user_id_sql = "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports" . $gusets_in_sql1;
        $quiz_id_sql = "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $user_id_sql = "SELECT rp.user_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS rp
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS qz
                                ON rp.quiz_id = qz.id
                            WHERE rp.status = 'finished' AND qz.author_id = ".$current_user . $gusets_in_sql2;
            $quiz_id_sql = "SELECT rep.quiz_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS rep
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS qu
                                ON rep.quiz_id = qu.id
                            WHERE qu.author_id = ".$current_user;
        }

        if( isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null ){
            if( $flag === true ){
                $_REQUEST['user_id'][] = '0';
            }
            $user_id = implode(',', $_REQUEST['user_id']);

            if( $only_guests === true ){
                $user_id = 0;
            }
        }else{
            $user_id = $user_id_sql;
        }


        if ( isset( $_REQUEST['flag'] ) ) {
            $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? intval($_REQUEST['quiz_id']) : $quiz_id_sql;
        }else{
            $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? implode(',', $_REQUEST['quiz_id']) : $quiz_id_sql;
        }

        $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

        if( $only_guests === true ){
            $user_id = 0;
        }
        if( ! $this->current_user_can_edit ){
            $sql = "SELECT COUNT(*) AS qanak
                    FROM {$wpdb->prefix}aysquiz_reports AS r
                    LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                        ON r.quiz_id = q.id
                    WHERE
                        q.author_id = {$current_user} AND
                        r.user_id IN ($user_id) AND
                        r.quiz_id IN ($quiz_id) AND
                        r.start_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND
                        r.status = 'finished'
                    ORDER BY r.id DESC";
        }else{
            $sql = "SELECT COUNT(*) AS qanak
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE
                        user_id IN ($user_id) AND
                        quiz_id IN ($quiz_id) AND
                        start_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND
                        `status` = 'finished'
                    ORDER BY id DESC";
        }
        
        $results = $wpdb->get_row($sql);

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($results);
        wp_die();
    }
    
    public function ays_results_export_file($path){
        global $wpdb;
        error_reporting(0);
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        $gusets_in_sql1 = '';
        $gusets_in_sql2 = '';
        $flag = true;
        $only_guests = false;
        if (isset($_REQUEST['with_guests']) && $_REQUEST['with_guests'] == 'true' ) {
            $flag = false;
            $gusets_in_sql1 = ' WHERE user_id != 0 ';
            $gusets_in_sql2 = ' AND rp.user_id != 0 ';
        }

        if (isset($_REQUEST['only_guests']) && $_REQUEST['only_guests'] == 'true' ) {
            $only_guests = true;
            $gusets_in_sql1 = ' WHERE user_id = 0 ';
            $gusets_in_sql2 = ' AND rp.user_id = 0 ';
        }
        
        $user_id_sql = "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports" . $gusets_in_sql1;
        $quiz_id_sql = "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $user_id_sql = "SELECT r.user_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS rp
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                                ON rp.quiz_id = q.id
                            WHERE rp.status = 'finished' AND q.author_id = ".$current_user . $gusets_in_sql2;
            $quiz_id_sql = "SELECT r.quiz_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS r
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                                ON r.quiz_id = q.id
                            WHERE r.status = 'finished' AND q.author_id = ".$current_user;
        }
        
        $user_id = (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null) ? implode(',', $_REQUEST['user_id']) : $user_id_sql;
        $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? implode(',', $_REQUEST['quiz_id']) : $quiz_id_sql;
        $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';


        if( isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null ){
            if( $flag === true ){
                $_REQUEST['user_id'][] = '0';
            }
            $user_id = implode(',', $_REQUEST['user_id']);

            if( $only_guests === true ){
                $user_id = 0;
            }
        }else{
            $user_id = $user_id_sql;
        }

        if( $only_guests === true ){
            $user_id = 0;
        }

        if( ! $this->current_user_can_edit ){
            $sql = "SELECT r.*
                    FROM {$wpdb->prefix}aysquiz_reports AS r
                    LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                        ON r.quiz_id = q.id
                    WHERE
                        q.author_id = {$current_user} AND
                        r.user_id IN ($user_id) AND
                        r.quiz_id IN ($quiz_id) AND
                        r.start_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND
                        r.status = 'finished'
                    ORDER BY r.id DESC";
        }else{
            $sql = "SELECT *
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE
                        user_id IN ($user_id) AND
                        quiz_id IN ($quiz_id) AND
                        start_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND
                        `status` = 'finished'
                    ORDER BY id DESC";
        }

        $results = $wpdb->get_results($sql);
        $sql = "SELECT `name` FROM {$wpdb->prefix}aysquiz_attributes";
        $attributes = $wpdb->get_results($sql);
        switch($type){
            case 'csv':
                $export_data = $this->ays_results_export_csv($results, $attributes);
            break;
            case 'xlsx':
                $export_data = $this->ays_results_export_xlsx($results, $attributes);
            break;
            case 'json':
                $export_data = $this->ays_results_export_json($results, $attributes);
            break;
        }
        
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($export_data);
        wp_die();
    }
    
    public function ays_results_export_csv($results, $attributes){
        
        global $wpdb;
        error_reporting(0);
        $export_file_fields = array('user','user_ip','user_role','quiz_name','start_date','end_date','score','points','duration','rate','review','name','email','phone');
        $export_file_fields0 = array('','','','','','','','','','','','','','');
        foreach ($attributes as $attribute){
            array_push($export_file_fields, $attribute->name);
            array_push($export_file_fields0, '');
        }
        $results_array_csv = array();

        if(empty($results)){
            $export_data = array(
                'status'        => true,
                'data'          => $export_file_fields0,
                'fileFields'    => $export_file_fields,
                'type'          => 'csv'
            );
        }else{
            foreach ($results as $key => $result){
                $result = (array)$result;
                $result_option = (array)json_decode($result['options']);
                $rate_id = isset($result_option['rate_id']) ? $result_option['rate_id'] : null;
                if($rate_id === null){
                    $rate_result = array();
                }else{
                    $rate_result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
                }
                $quiz_id = intval($result['quiz_id']);
                $quiz = $this->ays_get_quiz_by_id($quiz_id);
                $quiz_name = stripslashes($quiz['title']);
                $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();
                unset($result['quiz_id'],$result['id'],$result['options']);
                $points = (isset($result['points']) && $result['points'] != null) ? $result['points'] : '';
                $max_points = (isset($result['max_points']) && $result['max_points'] != null) ? $result['max_points'] : '';
                $duration = (isset($result['duration']) && $result['duration'] != null) ? $result['duration'] : '';
                $duration = intval($duration) . 's';
                $res_points = floatval($points) . ' of ' . floatval($max_points);
                $user = get_user_by('id', $result['user_id']);
                if( $user !== false ){
                    $user_name = $user->data->display_name;
                    $user_roles_arr = $user->roles;
                    $user_roles = implode(';', $user_roles_arr);
                }else{
                    $user_name = __( 'Guest', $this->plugin_name );
                    $user_roles = __( 'Guest', $this->plugin_name );
                }
                $results_array_csv[] = array(
                    $user_name,
                    $result['user_ip'],
                    $user_roles,
                    $quiz_name,
                    $result['start_date'],
                    $result['end_date'],
                    $result['score'],
                    $res_points,
                    $duration,
                    (isset($rate_result['score']) && $rate_result['score'] != null) ?  $rate_result['score'] : '',
                    (isset($rate_result['review']) && $rate_result['review'] != null) ? stripslashes(htmlspecialchars(str_replace("\n", "", (strip_tags($rate_result['review']))))) : '',
                    (isset($result['user_name']) && $result['user_name'] != null) ? $result['user_name'] : '',
                    (isset($result['user_email']) && $result['user_email'] != null) ? $result['user_email'] : '',
                    (isset($result['user_phone']) && $result['user_phone'] != null) ? $result['user_phone'] : '',
                );
                foreach ($attributes as $attribute){
                    $attribute = (array)$attribute;
                    if(isset($result_attributes[$attribute['name']]) && $result_attributes[$attribute['name']] != null){
                        array_push($results_array_csv[$key],$result_attributes[$attribute['name']]);
                    }else{
                        array_push($results_array_csv[$key], '');
                    }
                }
            }
            $export_data = array(
                'status'        => true,
                'data'          => $results_array_csv,
                'fileFields'    => $export_file_fields,
                'type'          => 'csv'
            );
        }
        return $export_data;
    }
    
    public function ays_results_export_xlsx($results, $attributes){
        
		global $wpdb;
		error_reporting(0);
        
        $results_array = array();
		$results_headers = array(
            array( 'text' => "user" ),
            array( 'text' => "user_ip" ),
            array( 'text' => "user_role" ),
            array( 'text' => "quiz_name" ),
            array( 'text' => "start_date" ),
            array( 'text' => "end_date" ),
            array( 'text' => "score" ),
            array( 'text' => "points" ),
            array( 'text' => "duration" ),
            array( 'text' => "rate" ),
            array( 'text' => "review" ),
            array( 'text' => "name" ),
            array( 'text' => "email" ),
            array( 'text' => "phone" )
		);        
        foreach ($attributes as $attribute){
            $results_headers[] = array( 'text' => $attribute->name );
        }
        $results_array[] = $results_headers;
        
        foreach ($results as $key => $result){
            $result = (array)$result;
            $result_option = (array)json_decode($result['options']);
            $rate_id = isset($result_option['rate_id']) ? $result_option['rate_id'] : null;
            if($rate_id === null){
                $rate_result = array();
            }else{
                $rate_result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
            }
            $quiz_id = intval($result['quiz_id']);
            $quiz = $this->ays_get_quiz_by_id($quiz_id);
            $quiz_name = stripslashes($quiz['title']);
            $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();
            $points = (isset($result['points']) && $result['points'] != null) ? $result['points'] : '';
            $max_points = (isset($result['max_points']) && $result['max_points'] != null) ? $result['max_points'] : '';
            $duration = (isset($result['duration']) && $result['duration'] != null) ? $result['duration'] : '';
            $duration = intval($duration) . 's';
            $res_points = floatval($points) . ' of ' . floatval($max_points);
            $user = get_user_by('id', $result['user_id']);
            if( $user !== false ){
                $user_name = $user->data->display_name;
                $user_roles_arr = $user->roles;
                $user_roles = implode(',', $user_roles_arr);
            }else{
                $user_name = __( 'Guest', $this->plugin_name );
                $user_roles = __( 'Guest', $this->plugin_name );
            }
            $res_array = array(
                array( 'text' => $user_name ),
                array( 'text' => $result['user_ip'] ),
                array( 'text' => $user_roles ),
                array( 'text' => $quiz_name ),
                array( 'text' => $result['start_date'] ),
                array( 'text' => $result['end_date'] ),
                array( 'text' => $result['score'] ),
                array( 'text' => $res_points ),
                array( 'text' => $duration ),
                array( 'text' => (isset($rate_result['score']) && $rate_result['score'] != null) ?  $rate_result['score'] : '' ),
                array( 'text' => (isset($rate_result['review']) && $rate_result['review'] != null) ? stripslashes(htmlspecialchars(str_replace("\n", "", (strip_tags($rate_result['review']))))) : '' ),
                array( 'text' => (isset($result['user_name']) && $result['user_name'] != null) ? $result['user_name'] : '' ),
                array( 'text' => (isset($result['user_email']) && $result['user_email'] != null) ? $result['user_email'] : '' ),
                array( 'text' => (isset($result['user_phone']) && $result['user_phone'] != null) ? $result['user_phone'] : '' ),
            );
            
            foreach ($attributes as $attribute){
                $attribute = (array)$attribute;
                if(isset($result_attributes[$attribute['name']]) && $result_attributes[$attribute['name']] != null){
                    array_push( $res_array, array( 'text' => $result_attributes[$attribute['name']] ) );
                }else{
                    array_push( $res_array, array( 'text' => '' ) );
                }
            }
            $results_array[] = $res_array;
        }
        
		$response = array(
			'status' => true,
			'data'   => $results_array,
			"type"   => 'xlsx'
		);
		return $response;
    }
    
    public function ays_results_export_json($results, $attributes){
        
		global $wpdb;
		error_reporting(0);
        
        $results_array = array();
        foreach ($results as $key => $result){
            $result = (array)$result;
            $result_option = (array)json_decode($result['options']);
            $rate_id = isset($result_option['rate_id']) ? $result_option['rate_id'] : null;
            if($rate_id === null){
                $rate_result = array();
            }else{
                $rate_result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
            }

            $quiz_id = intval($result['quiz_id']);
            $quiz = $this->ays_get_quiz_by_id($quiz_id);
            $quiz_name = stripslashes($quiz['title']);
            $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();

            $points = (isset($result['points']) && $result['points'] != null) ? $result['points'] : '';
            $max_points = (isset($result['max_points']) && $result['max_points'] != null) ? $result['max_points'] : '';
            $duration = (isset($result['duration']) && $result['duration'] != null) ? $result['duration'] : '';
            $duration = intval($duration) . 's';
            $res_points = floatval($points) . ' of ' . floatval($max_points);
            $user = get_user_by('id', $result['user_id']);
            if( $user !== false ){
                $user_name = $user->data->display_name;
                $user_roles_arr = $user->roles;
                $user_roles = implode(',', $user_roles_arr);
            }else{
                $user_name = __( 'Guest', $this->plugin_name );
                $user_roles = __( 'Guest', $this->plugin_name );
            }

            $res_array = array(
                'user' => $user_name,
                'user_ip' => $result['user_ip'],
                'user_role' => $user_roles,
                'quiz_name' => $quiz_name,
                'start_date' => $result['start_date'],
                'end_date' => $result['end_date'],
                'score' => $result['score'],
                'points' => $res_points,
                'duration' => $duration,
                'rate' => (isset($rate_result['score']) && $rate_result['score'] != null) ?  $rate_result['score'] : '',
                'review' => (isset($rate_result['review']) && $rate_result['review'] != null) ? stripslashes(htmlspecialchars(str_replace("\n", "", (strip_tags($rate_result['review']))))) : '',
                'name' => (isset($result['user_name']) && $result['user_name'] != null) ? $result['user_name'] : '',
                'email' => (isset($result['user_email']) && $result['user_email'] != null) ? $result['user_email'] : '',
                'phone' => (isset($result['user_phone']) && $result['user_phone'] != null) ? $result['user_phone'] : '',                
            );
            foreach ($result_attributes as $attr_name => $value){
                $res_array[$attr_name] = $value;
            }
            $results_array[] = $res_array;
        }
        
		$response = array(
			'status' => true,
			'data'   => $results_array,
			"type"   => 'json'
		);
		return $response;
    }    

    public function ays_quick_start(){
        global $wpdb;
        error_reporting(0);

        $quiz_title = stripslashes( $_REQUEST['ays_quiz_title'] );
        $quiz_description = (isset( $_REQUEST['ays_quick_quiz_description'] ) && $_REQUEST['ays_quick_quiz_description'] != "") ? stripslashes( wp_kses_post( $_REQUEST['ays_quick_quiz_description'] ) ) : "";
        $quiz_cat_id = sanitize_text_field( $_REQUEST['ays_quiz_category'] );
        $questions = $_REQUEST['ays_quick_question'];
        $questions_type = $_REQUEST['ays_quick_question_type'];
        $questions_cat = $_REQUEST['ays_quick_question_cat'];
        $answers_correct = $_REQUEST['ays_quick_answer_correct'];
        $answers = $_REQUEST['ays_quick_answer'];

        $answers_table = $wpdb->prefix . 'aysquiz_answers';
        $questions_table = $wpdb->prefix . 'aysquiz_questions';
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';

        $questions_ids = '';

        $max_id = $this->get_max_id('quizes');
        $ordering = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;

        $create_date = current_time( 'mysql' );
        $user_id = get_current_user_id();

        $options = json_encode(array(
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
                'twitter_link' => ''
            ),
            'show_quiz_title' => 'on',
            'show_quiz_desc' => 'on',
            'show_login_form' => 'off',
            'mobile_max_width' => '',
            'limit_users_by' => 'ip',
            'progress_live_bar_style' => 'default',

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
            'enable_google_sheets' => '',
            'spreadsheet_id' => '',

            // Email config options
            'send_results_user' => 'off', //AV
            'send_interval_msg' => 'off',
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
        ));

        $quiz_settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $quiz_default_options = ($quiz_settings->ays_get_setting('quiz_default_options') === false) ? '' : $quiz_settings->ays_get_setting('quiz_default_options');
        if (! empty($quiz_default_options)) {
            $options = $quiz_default_options;
        }

        foreach ($questions as $question_key => $question) {

            $cat_key = array_search( $question_key, $questions_cat );
            $q_category_id = (isset( $questions_cat[$cat_key] ) && $questions_cat[$cat_key] != "") ? esc_sql( $questions_cat[$cat_key] ) : 1;

            if ( !isset( $questions_type[$question_key] ) || is_null( $questions_type[$question_key] ) ) {
                continue;
            }

            $wpdb->insert($questions_table, array(
                'category_id' => $q_category_id,
                'question' => stripslashes( $question ),
                'published' => 1,
                'type' => $questions_type[$question_key],
                'create_date' => $create_date,
                'author_id' => $user_id,
                'options' => json_encode(array(
                    'bg_image' => "",
                    'use_html' => 'off',
                    'enable_question_text_max_length' => 'off',
                    'question_text_max_length' => '',
                    'question_limit_text_type' => 'characters',
                    'question_enable_text_message' => 'off',
                    'enable_question_number_max_length' => 'off',
                    'question_number_max_length' => '',
                    'quiz_hide_question_text' => 'off',
                ))
            ));
            $question_id = $wpdb->insert_id;
            $questions_ids .= $question_id . ',';
            if ( isset( $answers[$question_key] ) && ! empty( $answers[$question_key] ) ) {
                foreach ($answers[$question_key] as $key => $answer) {
                    $type = $questions_type[$question_key];

                    if($type == "text" || $type == "short_text"){
                        $correct = 1;
                    }else{
                        $correct = ($answers_correct[$question_key][$key] == "true") ? 1 : 0;
                    }
                    $placeholder = '';

                    $wpdb->insert($answers_table, array(
                        'question_id' => esc_sql( $question_id ),
                        'answer' => esc_sql( trim( stripslashes($answer) ) ),
                        'correct' => $correct,
                        'ordering' => $key,
                        'placeholder' => $placeholder

                    ));
                }
            }
        }
        $questions_ids = rtrim($questions_ids, ",");
        $wpdb->insert($quizes_table, array(
            'title' => $quiz_title,
            'description' => $quiz_description,
            'question_ids' => $questions_ids,
            'published' => 1,
            'create_date' => $create_date,
            'author_id' => $user_id,
            'options' => $options,
            'quiz_category_id' => $quiz_cat_id,
            'ordering' => $ordering
        ));
        $quiz_id = $wpdb->insert_id;
        echo json_encode(array(
            'status' => true,
            'quiz_id' => $quiz_id
        ));
        wp_die();
    }
    
    public static function get_max_id($table) {
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_'.$table;

        $sql = "SELECT max(id) FROM {$quiz_table}";

        $result = intval($wpdb->get_var($sql));

        return $result;
    }
    
    public static function get_published_questions_used(){
        global $wpdb;
        /*$sql1 = "SELECT GROUP_CONCAT(question_ids SEPARATOR ',') AS ids
                 FROM {$wpdb->prefix}aysquiz_quizes WHERE question_ids IS NOT NULL AND question_ids !='';";
        $res1 = $wpdb->get_var( $sql1 );*/
        $sql1 = "SELECT question_ids
                 FROM {$wpdb->prefix}aysquiz_quizes
                 WHERE question_ids IS NOT NULL AND question_ids !='';";
        $res1 = $wpdb->get_results( $sql1, 'ARRAY_A' );
        if(! $res1){
            return array();
        }
        $result = array();

        foreach ($res1 as $key => $value) {
            $result[] = $value['question_ids'];
        }
        $res1 = implode(',', $result);

        $results = array_unique(explode(',', $res1));

        if(empty($results)){
            return array();
        }

        return $results;
    }

    public function show_results_details(){
        error_reporting(0);
        $result_id = intval($_REQUEST["resultId"]);
        if ($result_id !== 0) {
            $result = array();
            $data = $this->get_results_row($result_id);
            if($data['user_explanation'] != ''){
                $user_exp = json_decode($data['user_explanation'], true);
                foreach($user_exp as $question_id => $exp){
                    $question = $this->get_question_row($question_id);
                    $result[] = array(
                        'question' => $question['question'],
                        'exp' => $exp
                    );
                }
            }
            $data['status'] = true;
            $data['explanations'] = $result;
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode($data);
            wp_die();
        } else {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => false,
            ));
            wp_die();
        }
    }

    public function ays_show_results(){
        global $wpdb;
        error_reporting(0);
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_show_results') {
            $id = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id} AND `status` = 'finished'", "ARRAY_A");
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
            $correctness = (array)$options->correctness;
            $answers_keyword_counts = (array)$options->answers_keyword_counts;
            $quiz_coupon = isset($options->quiz_coupon) ? $options->quiz_coupon : '';
            $rate_html = "";

            $question_id_arr = array();
            $question_correctness = array();
            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $current_question_id = absint(intval(explode('_', $key)[2]));

                    $question_id_arr[] = $current_question_id;
                    $question_correctness[ $current_question_id ] = $option;
                }
            }
            
            if(!isset($options->user_points)){
                $options->user_points = array_sum($correctness);
            }
            
            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';
            $score = $calc_method == 'by_points' ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';

            
            $json = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            $country = $json->country;
            $region = $json->region;
            $city = $json->city;
            $from = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;
            
            $note_text = ( isset($options->note_text) && $options->note_text != '' ) ? sanitize_text_field( stripslashes( $options->note_text ) ) : '';

            $res_question_title_arr = ( isset($options->questions_title) && !empty($options->questions_title) ) ? (array)$options->questions_title : array();

            $settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
            $settings_options = ($settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes($settings_obj->ays_get_setting('options') ), true);
            
            $ays_quiz_show_result_info_user_ip = isset($settings_options['ays_quiz_show_result_info_user_ip']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_user_ip'] ) : 'on';
            $ays_quiz_show_result_info_user_id = isset($settings_options['ays_quiz_show_result_info_user_id']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_user_id'] ) : 'on';
            $ays_quiz_show_result_info_user = isset($settings_options['ays_quiz_show_result_info_user']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_user'] ) : 'on';
            $ays_quiz_show_result_info_admin_note = isset($settings_options['ays_quiz_show_result_info_admin_note']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_admin_note'] ) : 'on';

            $ays_quiz_show_result_info_start_date = isset($settings_options['ays_quiz_show_result_info_start_date']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_start_date'] ) : 'on';
            $ays_quiz_show_result_info_duration = isset($settings_options['ays_quiz_show_result_info_duration']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_duration'] ) : 'on';
            $ays_quiz_show_result_info_score = isset($settings_options['ays_quiz_show_result_info_score']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_score'] ) : 'on';
            $ays_quiz_show_result_info_rate = isset($settings_options['ays_quiz_show_result_info_rate']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_rate'] ) : 'on';
            $ays_quiz_show_result_info_unique_code = isset($settings_options['ays_quiz_show_result_info_unique_code']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_unique_code'] ) : 'on';
            $ays_quiz_show_result_info_keywords = isset($settings_options['ays_quiz_show_result_info_keywords']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_keywords'] ) : 'on';
            $ays_quiz_show_result_info_res_by_cats = isset($settings_options['ays_quiz_show_result_info_res_by_cats']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_res_by_cats'] ) : 'on';
            $ays_quiz_show_result_info_coupon = isset($settings_options['ays_quiz_show_result_info_coupon']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_coupon'] ) : 'on';
            $ays_quiz_show_result_info_certificate = isset($settings_options['ays_quiz_show_result_info_certificate']) ? sanitize_text_field( $settings_options['ays_quiz_show_result_info_certificate'] ) : 'on';


            $row = "<table id='ays-results-table'>";
            
            $row .= '<tr class="ays_result_element">
                        <td colspan="5">
                            <div class="ays-quiz-admin-note">
                                <div class="ays-quiz-click-for-admin-note">
                                    <button class="button button-primary" style="color:#ffffff !important; font-weight:normal;">'
                                        .__( 'Click For Admin Note', $this->plugin_name ).
                                    '</button>
                                </div>
                                <div class="ays-quiz-admin-note-textarea">
                                    <div class="ays-quiz-admin-note-text">
                                        <textarea style="width:100%; height:125px; font-weight:normal;" value="" name="ays_admin_notes" data-result="'.$id.'">'.$note_text.'</textarea>
                                    </div>
                                    <div class="ays-quiz-admin-note-save">
                                        <button class="button button-primary ays-quiz-save-note" style="color:#ffffff !important; font-weight:normal;">'.__( 'Save', $this->plugin_name ).'</button>
                                        <button class="button button-primary ays-quiz-close-note" style="color:#ffffff !important; font-weight:normal;">'.__( 'Close', $this->plugin_name ).'</button>
                                    </div>
                                </div>
                                <div class="ays-quiz-preloader-note" style="top:0;left:0;">
                                    <img class="loader" src="'.AYS_QUIZ_ADMIN_URL.'/images/loaders/tail-spin.svg" >
                                </div>
                            </div>
                        </td>
                    </tr>';

            $row .= '<tr class="ays_result_element">
                        <td colspan="3"><h1>' . __('User Information',$this->plugin_name) . '</h1></td>
                        <td>
                            <div class="question-action-butons" style="align-items: center;">
                                <span style="min-width: 70px;">'.__("Export to", $this->plugin_name).'</span>
                                <a download="" id="downloadFile" hidden href=""></a>
                                <button type="button" class="button button-primary ays-export-result-pdf" data-result="'.$id.'">'.__("PDF", $this->plugin_name).'</button>
                                <button type="button" class="button button-primary ays-single-question-results-export" date-result="'.$id.'" data-type="xlsx" quiz-id='.$quiz_id.'>'.__("XLSX", $this->plugin_name).'</button>
                            </div>
                        </td>
                    </tr>';
            if ($ays_quiz_show_result_info_user_ip == 'on') {
                if ($user_ip != '') {
                    $row .= '<tr class="ays_result_element">
                                <td>'.__('User',$this->plugin_name).' IP</td>
                                <td colspan="3">' . $from . '</td>
                            </tr>';
                }
            }
            
            $user_name = $user_id === 0 ? __( "Guest", $this->plugin_name ) : $user->data->display_name;

            if ($ays_quiz_show_result_info_user_id == 'on') {
                if($user_id !== 0){
                    $row .= '<tr class="ays_result_element">
                            <td>'.__('User',$this->plugin_name).' ID</td>
                            <td colspan="3">' . $user_id . '</td>
                        </tr>';
                }
            }

            if ($ays_quiz_show_result_info_user == 'on') {
                $row .= '<tr class="ays_result_element">
                        <td>'.__('User',$this->plugin_name).'</td>
                        <td colspan="3">' . $user_name . '</td>
                    </tr>';
            }
            if(isset($results['user_email']) && $results['user_email'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Email',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_email'])."</td>
                     </tr>";
            }
            if(isset($results['user_name']) && $results['user_name'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Name',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_name'])."</td>
                     </tr>";
            }
            if(isset($results['user_phone']) && $results['user_phone'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Phone',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_phone'])."</td>
                     </tr>";
            }
            if ($user_attributes !== null) {
                $user_attributes = (array)$user_attributes;
            
                foreach ($user_attributes as $name => $value) {
                    if(stripslashes($value) == ''){
                        $attr_value = '-';
                    }else{
                        $attr_value = stripslashes($value);
                    }
                    
                    if($attr_value == 'on'){
                        $attr_value = __('Checked',$this->plugin_name);
                    }
                    
                    $row .= '<tr class="ays_result_element">
                            <td>' . stripslashes($name) . '</td>
                            <td colspan="3">' . $attr_value . '</td>
                        </tr>';
                }
            }
            
            if ($ays_quiz_show_result_info_admin_note == 'on') {
                $row .= '<tr class="ays_result_element">';
                    $row .=  '<td>'.__( 'Admin note', $this->plugin_name ).'</td>';
                    $admin_note_text = '';
                    if(isset($options->note_text) && $options->note_text !== ''){
                        $admin_note_text = sanitize_text_field( stripslashes( $options->note_text ) );
                    }
                    $row .= '<td colspan="3" class="ays_quiz_admin_note_td">' . $admin_note_text . '</td>';
                $row .= '</tr>';
            }

            $row .= apply_filters( 'ays_qm_track_users_contents', '', $id );

            $row .= '<tr class="ays_result_element">
                        <td colspan="4"><h1>' . __('Quiz Information',$this->plugin_name) . '</h1></td>
                    </tr>';

            if ($ays_quiz_show_result_info_rate == 'on') {
                if(isset($rate['score'])){
                    $rate_html = '<tr style="vertical-align: top;" class="ays_result_element">
                        <td>'.__('Rate',$this->plugin_name).'</td>
                        <td>'. __("Rate Score", $this->plugin_name).":<br>" . $rate['score'] . '</td>
                        <td colspan="2" style="max-width: 200px;">'. __("Review", $this->plugin_name).":<br>" . nl2br($rate['review']) . '</td>
                    </tr>';
                }else{
                    $rate_html = '<tr class="ays_result_element">
                        <td>'.__('Rate',$this->plugin_name).'</td>
                        <td colspan="3">' . nl2br($rate['review']) . '</td>
                    </tr>';
                }
            }

            if ($ays_quiz_show_result_info_start_date == 'on') {
                $row .= '<tr class="ays_result_element">
                            <td>'.__('Start date',$this->plugin_name).'</td>
                            <td colspan="3">' . $start_date . '</td>
                        </tr>';                        
            }

            if ($ays_quiz_show_result_info_duration == 'on') {
                $row .= '<tr class="ays_result_element">
                            <td>'.__('Duration',$this->plugin_name).'</td>
                            <td colspan="3">' . $duration . '</td>
                        </tr>';
            }

            if ($ays_quiz_show_result_info_score == 'on') {
                $row .= '<tr class="ays_result_element">
                            <td>'.__('Score',$this->plugin_name).'</td>
                            <td colspan="3">' . $score . '</td>
                        </tr>';
            }

            if($ays_quiz_show_result_info_rate == 'on'){
                $row .= $rate_html;
            }



            if ($ays_quiz_show_result_info_unique_code == 'on') {
                if(isset($results['unique_code']) && $results['unique_code'] !== ''){
                    $row .= '<tr class="ays_result_element">
                                <td>'.__('Unique Code',$this->plugin_name).'</td>
                                <td colspan="3"><strong>' . strtoupper($results['unique_code']) . '</strong></td>
                            </tr>';
                }
            }

            if ($ays_quiz_show_result_info_keywords == 'on') {
                if(isset( $answers_keyword_counts) &&  !empty($answers_keyword_counts)){

                    $total_keywords_count = array_sum($answers_keyword_counts);
                    $row .= '<tr class="ays_result_element">
                                <td>'.__('Keywords',$this->plugin_name).'</td>
                                
                                <td>
                    ';
                    ksort($answers_keyword_counts);
                    foreach ($answers_keyword_counts as $key => $value) {
                        $mv_keyword_percentage = 0;

                        if($total_keywords_count > 0){
                            $mv_keyword_percentage = ( $value / $total_keywords_count ) * 100;
                        }
                        $row .= '<p>'.$key .' &#8594; ' . $value . ' (' . (round($mv_keyword_percentage,2)). '%)</p>';
                    }

                    $row .= '</td></tr>';
                }
            }

            if ($ays_quiz_show_result_info_res_by_cats == 'on') {

                $results_by_categories = Quiz_Maker_Data::ays_quiz_current_result_by_category($options, $question_correctness, $question_id_arr, $calc_method);

                if(isset( $results_by_categories) &&  !empty($results_by_categories)){
                    $row .= '<tr class="ays_result_element">
                                <td>'.__('Results by Categories',$this->plugin_name).'</td>
                                <td colspan="3">'. $results_by_categories .'</td>
                            </tr>
                    ';
                }
            }

            if( $ays_quiz_show_result_info_coupon == 'on' ){
                if(isset($quiz_coupon) && $quiz_coupon != ''){
                    $row .= '<tr class="ays_result_element">';
                        $row .= '<td>'.__('Quiz Coupon',$this->plugin_name).'</td>';
                        $row .= '<td>'.$quiz_coupon.'</td>';
                    $row .= '</tr>';
                }
            }

            if ($ays_quiz_show_result_info_certificate == 'on') {
                $cert_file_name = isset($options->cert_file_name) && $options->cert_file_name != '' ? $options->cert_file_name : '';
                $cert_file_url = isset($options->cert_file_url) && $options->cert_file_url != '' ? $options->cert_file_url : '';
                $cert_file_path = isset($options->cert_file_path) && $options->cert_file_path != '' ? $options->cert_file_path : '';
                if(file_exists($cert_file_path)){
                    $cert_html = "<a class='ays_result_certificate' href='".$cert_file_url."' target='_blank'>" . __( 'Open', $this->plugin_name ) . "</a>";
                    $cert_html .= "<a class='ays_result_certificate' href='".$cert_file_url."' target='_blank' download>" . __( 'Download', $this->plugin_name ) . "</a>";

                    $row .= '<tr class="ays_result_element">
                                <td>'.__('Certificate',$this->plugin_name).'</td>
                                <td colspan="3">' . $cert_html . '</td>
                            </tr>';
                }
            }

            $row .= '<tr class="ays_result_element">
                        <td colspan="3"><h1>' . __('Questions',$this->plugin_name) . '</h1></td>
                        <td>
                            <div class="ays_result_toogle_block">
                                <span class="ays-show-quest-toggle quest-toggle-all">All</span>
                                <input type="checkbox" class="ays_toggle ays_toggle_slide" id="ays_show_questions_toggle" checked>
                                <label for="ays_show_questions_toggle" class="ays_switch_toggle">Toggle</label>
                                <span class="ays-show-quest-toggle quest-toggle-failed">Failed</span>
                            </div>
                        </td>
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

                    if ( is_null( $question ) || empty( $question )  ) {
                        continue;
                    }

                    $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                    $question_type = isset($question['type']) && $question['type'] != '' ? sanitize_text_field( $question['type'] ) : 'radio';
                    $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                    $correct_answers = $this->get_correct_answers($question_id);
                    $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);
                    $correct_answer_images = $this->get_correct_answer_images($question_id);
                    $is_text_type = $this->question_is_text_type($question_id);
                    $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );
                    $text_type = $this->text_answer_is($question_id);
                    $not_multiple_text_types = array("number", "date");

                    // Incorrect matches for answers
                    $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                    $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                    if($is_text_type){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                        $user_answered_images = '';
                    } elseif( $question_type == 'fill_in_blank' ){
                        $user_answered = $this->get_user_fill_in_blank_answered($options->user_answered, $key);
                        $user_answered_images = '';
                    }elseif( $is_matching_type ){
                        $user_answered = $this->get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                        $correct_answers = $this->get_correct_answers_for_matching_type($question_id);
                        $user_answered_images = '';
                    }
                    else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                        $user_answered_images = $this->get_user_answered_images($options->user_answered, $key);
                    }
                    $ans_point = $option;
                    $ans_point_class = 'success';
                    if( ! $is_matching_type && is_array($user_answered) && isset( $user_answered['message'] ) && $user_answered['message'] != ""){
                        $user_answered = $user_answered['message'];
                        $ans_point = '-';
                        $ans_point_class = 'error';
                    }

                    $tr_class = "ays_result_element";

                    $not_influence_to_score = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? true : false;
                    if ( $not_influence_to_score ) {
                        $not_influance_check_td = ' colspan="2" ';
                    }else{
                        $not_influance_check_td = '';
                    }

                    $correct_row = $option == true ? 'tr_success' : '';

                    $question_image = isset( $question["question_image"] ) && $question["question_image"] != '' ? $question["question_image"] : '';
                    $question_title = isset( $question["question"] ) && $question["question"] != '' ? stripslashes(nl2br($question["question"])) : '';

                    if( !empty( $res_question_title_arr ) ){
                        $question_title = isset( $res_question_title_arr[$question_id] ) && $res_question_title_arr[$question_id] != '' ? stripslashes(nl2br($res_question_title_arr[$question_id])) : $question_title;
                    }

                    if($calc_method == 'by_correctness'){
                        $row .= '<tr class="'.$tr_class.' '.$correct_row.'">
                            <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                        if( $question_image != '' ){
                            $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                        }
                        $row .= (stripslashes($question_title)) .
                            '</td>';

                        $status_class = 'error';
                        $correct_answers_status_class = 'success';
                        if ($option == true) {
                            $status_class = 'success';
                        }

                        if ($not_influence_to_score) {
                            $status_class = 'no_status';
                            $correct_answers_status_class = 'no_status';
                        }

                        if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                            $c_answers = explode('%%%', $correct_answers);
                            $c_answer = $c_answers[0];
                            foreach($c_answers as $c_ans){
                                if(mb_strtolower(trim($user_answered)) == mb_strtolower(trim($c_ans))){
                                    $c_answer = $c_ans;
                                    break;
                                }
                            }
                            $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>';
                            $row .= '<p class="success">' . stripslashes( esc_attr( $c_answer ) ) . '<br>'.$correct_answer_images.'</p>';
                            $row .= '</td>';
                        }elseif( $question_type == "fill_in_blank" ){

                            $fill_in_blank_question_title_correct = $question_title;

                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                $answer_html = "<span class='fill-in-blank fill-in-blank-success'>". $corect_answer ."</span>";

                                $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                            }

                            $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>';
                            $row .= '<div>' . stripslashes( ( $fill_in_blank_question_title_correct ) ) . '<br>'.$correct_answer_images.'</div>';
                            $row .= '</td>';

                        } elseif ( $is_matching_type ) {
                            $row .= '<td class="ays-report-correct-answer">' . __( 'Correct answer', $this->plugin_name ) . ':<br/>';
                            foreach ( $correct_answers as $correct_answer ) {
                                $correct_answer_content = esc_attr( $correct_answer );
                                if($use_html){
                                    $correct_answer_content = stripslashes( $correct_answer );
                                }
                                $row .= '<p class="' . $correct_answers_status_class . '">' . $correct_answer_content . '<br>' . $correct_answer_images . '</p>';
                                $row .= '<hr />';
                            }
                            $row .= '</td>';
                        } else{
                            if($text_type == 'date'){
                                $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                            }
                            $correct_answer_content = esc_attr( $correct_answers );
                            if($use_html){
                                $correct_answer_content = stripslashes( $correct_answers );
                            }

                            $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>
                                <p class="'.$correct_answers_status_class.'">' . $correct_answer_content . '<br>'.$correct_answer_images.'</p>
                            </td>';
                        }

                        if($text_type == 'date'){
                            if(self::validateDate($user_answered, 'Y-m-d')){
                                $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                            }
                        }

                        if( $question_type == "fill_in_blank" ){

                            $fill_in_blank_question_title_user_answer = $question_title;
                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-success'>". $user_answer ."</span>";
                                } elseif( $user_answer == "" ){
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-empty'>". "—" ."</span>";
                                } else {
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-incorrect'>". $user_answer ."</span>";
                                }


                                $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                            }

                            $user_answer_content = stripslashes( $fill_in_blank_question_title_user_answer );
                            // if($use_html){
                            //     $user_answer_content = stripslashes( $user_answered );
                            // }

                            $row .= '<td '.$not_influance_check_td.' class="ays-report-user-answer">'.__('User answered',$this->plugin_name).':<br/>
                                <div>' . $user_answer_content . '</div>
                            </td>';

                        } elseif ( $is_matching_type ) {
                            $row .= '<td ' . $not_influance_check_td . ' class="ays-report-user-answer">' . __( 'User answered', $this->plugin_name ) . ':<br/>';
                            foreach ( $user_answered as $user_answer ) {
                                $user_answer_content = esc_attr( $user_answer['answer'] );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answer['answer'] );
                                }


                                $status_class = 'error';
                                if ($user_answer['correct'] == true) {
                                    $status_class = 'success';
                                }

                                $row .= '<p class="' . $status_class . '">' . $user_answer_content . '</p>';
                                $row .= '<hr />';
                            }
                            $row .= '</td>';
                        }  else {
                            $user_answer_content = stripslashes( esc_attr( $user_answered ) );
                            if($use_html){
                                $user_answer_content = stripslashes( $user_answered );
                            }

                            $row .= '<td '.$not_influance_check_td.' class="ays-report-user-answer">'.__('User answered',$this->plugin_name).':<br/>
                                <p class="'.$status_class.'">' . $user_answer_content . '</p>
                            </td>';
                        }

                        if (! $not_influence_to_score) {
                            if ($option == true) {
                                    $row .= '<td class="ays-report-status-icon">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                            <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                        </svg>
                                        <p class="success">'.__('Succeed',$this->plugin_name).'!</p>
                                    </td>';
                            } else {
                                $row .= '<td class="ays-report-status-icon">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                        <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                        <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                        <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                                    </svg>
                                    <p class="error">'.__('Failed',$this->plugin_name).'!</p>
                                </td>';
                            }
                        }

                        $row .= '</tr>';

                    }elseif($calc_method == 'by_points'){
                        $row .= '<tr class="'.$tr_class.'">
                            <td colspan="2">'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                        if( $question_image != '' ){
                            $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                        }
                        $row .= (stripslashes($question_title)) .
                                '</td>';

                        if( $question_type == "fill_in_blank" ){

                            $fill_in_blank_question_title_user_answer = $question_title;
                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-success'>". $user_answer ."</span>";
                                } elseif( $user_answer == "" ){
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-empty'>". "—" ."</span>";
                                } else {
                                    $answer_html = "<span class='fill-in-blank fill-in-blank-incorrect'>". $user_answer ."</span>";
                                }


                                $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                            }

                            $user_answer_content = stripslashes( $fill_in_blank_question_title_user_answer );
                            // if($use_html){
                            //     $user_answer_content = stripslashes( $user_answered );
                            // }

                            $row .= '<td class="ays-report-user-answer ays-report-user-answer-by-points">'.__('User answered',$this->plugin_name).':<br/><div class="">' . (stripslashes($user_answer_content)) . '</div></td>
                                <td class="ays-report-answer-point">'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr($ans_point) . '</p></td>
                            </tr>';

                        } elseif ( $is_matching_type ) {

                            $row .= '<td class="ays-report-user-answer ays-report-user-answer-by-points">'.__('User answered',$this->plugin_name).':<br/>';

                            foreach ( $user_answered as $user_answer ) {
                                $user_answer_content = esc_attr( $user_answer['answer'] );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answer['answer'] );
                                }
                                $row .= '<p class="' . $ans_point_class . '">' . $user_answer_content . '</p>';
                                $row .= '<hr />';
                            }

                            $row .= '</td>
                                <td class="ays-report-answer-point">'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr($ans_point) . '</p></td>
                            </tr>';

                        } else {

                            $row .= '<td class="ays-report-user-answer ays-report-user-answer-by-points">'.__('User answered',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr(stripslashes($user_answered)) . '</p></td>
                                    <td class="ays-report-answer-point">'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr($ans_point) . '</p></td>
                                </tr>';
                        }
                    }
                    $index++;
                    if(isset($user_exp[$question_id]) && $user_exp[$question_id] != ""){
                        $row .= '<tr class="ays_result_element '. $correct_row .'">
                            <td>'.__('User explanation for this question',$this->plugin_name).'</td>
                            <td colspan="3">'. stripslashes( $user_exp[$question_id] ) .'</td>
                        </tr>';
                    }
                }
            }
            $row .= "</table>";
            
            $sql = "UPDATE $results_table SET `read`=1 WHERE `id`=$id";
            $wpdb->get_var($sql);
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                "status" => true,
                "rows" => $row
            ));
            wp_die();
        }
    }
    
    public static function ays_get_woocommerce_product( $prod_id ){
        $product_post_ids = array();
        foreach($prod_id as $key => $value){
            foreach($value as $_key => $_value){
                $product_post_ids[] = get_post( intval($_value) );
            }
        }
        return $product_post_ids;
    }

    public function ays_get_woocommerce_products(){
        global $wpdb;
        error_reporting(0);

        $search = isset($_REQUEST['q']) && $_REQUEST['q'] != '' ? $_REQUEST['q'] : null;

        $results = array(
            'results' => array()
        );

        $sql = "SELECT t.*
                FROM {$wpdb->prefix}posts AS t
                WHERE t.post_type IN ('product')
                    AND t.post_status = 'publish' ";

        if($search !== null){
            $sql .= " AND t.post_title LIKE '%{$search}%' ";
        }else{

        }

        $sql .= " ORDER BY t.post_title ASC";
        $products = $wpdb->get_results( $sql );

        foreach ($products as $key => $value) {
            $results['results'][] = array(
                'id' => $value->ID,
                'text' => $value->post_title,
            );
        }

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode( $results );
        wp_die();
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

    public static function get_quiz_statistic_by_id( $quiz_id ){
        $dates = array();
        $dates_values = array();

        $quiz_id = intval( $quiz_id );
        $dates_array = Results_List_Table::get_results_dates( $quiz_id );
        $start_date = date_create($dates_array['min_date']);
        $end_date = date_create($dates_array['max_date']);
        $date_diff = date_diff($end_date, $start_date, true)->days;
        $start_date = (array)$start_date;
        $start_date_time = strtotime($start_date["date"]);
        $i = 0;
        while ($i != $date_diff + 1) {
            array_push($dates, date("Y-m-d", strtotime("+$i day", $start_date_time)));
            $i++;
        }
        foreach ($dates as $key => $date) {
            $count = Results_List_Table::get_each_date_statistic( $date, $quiz_id );
            $dates_values[] = intval( $count );
            $dates[$key] = date("F d Y", strtotime($dates[$key]));
        }
        $data = array(
            "values" => $dates_values,
            "dates" => $dates
        );

        return $data;
    }

    public function get_current_quiz_statistic(){
        error_reporting(0);
        $quiz_id = abs(intval($_REQUEST['quiz_id']));

        $data = self::get_quiz_statistic_by_id( $quiz_id );

        $charts = '';
        $statistics_items = array( 1, 7, 25, 30, 120 );
        foreach ($statistics_items as $statistics_item) {
            $img = '';
            $element = Results_List_Table::get_quizzes_count_by_days($statistics_item, $quiz_id);
            $diff = $element['difference'];
            if ($diff < 0) {
                $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/down_red_arrow.png" alt="Down">';
            } elseif ($diff > 0) {
                $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/up_green_arrow.png" alt="Up">';
            } else {
                $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/equal.png" alt="Equal">';
            }
            $charts .= "<li class=\"ays-collection-item\">
                <div class=\"stat-left-div\">
                    <p class=\"stat-count\"> " . $element['quizzes_count'] . "</p>
                    <span class=\"stat-description\">quizzes taken last " . $statistics_item . " day</span>
                </div>
                <div class=\"stat-right-div\">
                    <p class=\"stat-diff-count\">" . $element['difference'] . "%</p>
                    " . $img . "
                </div>
            </li>";
        }

        $result = json_encode(array(
            'dates' => $data['dates'],//$dates,
            'dates_values' => $data['values'],//$dates_values,
            'charts' => $charts
        ));

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo $result;
        wp_die();
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

    public function get_correct_answers_for_matching_type($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $results = $wpdb->get_results("SELECT options FROM {$answers_table} WHERE question_id={$id}");
        $answers = array();
        foreach ( $results as $result ) {
            $answer_options = isset( $result->options ) && ! empty( $result->options ) ? $result->options : '';
            $answer_options = json_decode( $answer_options, true );
            if ( ! $answer_options ) {
                $answer_options = array();
            }

            $match = isset( $answer_options['correct_match'] ) && $answer_options['correct_match'] ? $answer_options['correct_match'] : '';
            $answers[] = $match;
        }

        return $answers;
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

    public function get_user_answered($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;
        
        if($choices == ''){
            return array(
                'message' => __( "The user has not answered this question.", $this->plugin_name ),
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

    public function get_user_matching_answered($user_choice, $key, $incorrect_matches){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;
        $text = array();

        foreach ( $choices as $answer_id => $choice ) {
            if ( $choice === '' ) {
                $text[] = array(
                    'answer' => __( "The user has not answered.", $this->plugin_name ),
                    'correct' => false
                );
            } elseif ( isset( $incorrect_matches[ $choice ] ) && !empty( $incorrect_matches[ $choice ] ) ) {
                $text[] = array(
                    'answer' => trim( $incorrect_matches[ $choice ] ),
                    'correct' => false
                );
            } else {
                $result = $wpdb->get_var("SELECT options FROM {$answers_table} WHERE id={$answer_id}");
                $answer_options = ! empty( $result ) ? json_decode( $result, true ) : array();
                if ( ! $answer_options ) {
                    $answer_options = array();
                }
                $match = isset( $answer_options['correct_match'] ) && $answer_options['correct_match'] ? $answer_options['correct_match'] : '';

                $user_answerd_value = $wpdb->get_row("SELECT `answer`,`options` FROM {$answers_table} WHERE id={$choice}", "ARRAY_A");

                $user_answer_options = ! empty( $user_answerd_value['options'] ) ? json_decode( $user_answerd_value['options'], true ) : array();
                $user_answer = isset( $user_answerd_value['answer'] ) && $user_answerd_value['answer'] != "" ? esc_attr( $user_answerd_value['answer'] ) : "";
                if ( ! $user_answer_options ) {
                    $user_answer_options = array();
                }
                $user_match = isset( $user_answer_options['correct_match'] ) && $user_answer_options['correct_match'] ? $user_answer_options['correct_match'] : '';
                $if_correct = false;
                if( $user_match == $match ){
                    $if_correct = true;
                    $user_answer = $user_match;
                }

                $text[] = array(
                    'answer' => trim( $user_answer ),
                    'correct' => $if_correct,
                );
            }
        }
        
        return $text;
    }

    public function get_user_text_answered($user_choice, $key){
        if($user_choice->$key == ""){
            $choices = array(
                'message' => __( "The user has not answered this question.", $this->plugin_name ),
                'status' => false
            );
        }else{
            $choices = trim($user_choice->$key);
        }
        
        return $choices;
    }

    public function get_user_fill_in_blank_answered($user_choice, $key){

        if($user_choice->$key == "" || empty($user_choice->$key)){
            $choices = array(
                'message' => __( "The user has not answered this question.", $this->plugin_name ),
                'status' => false
            );
        }else{
            $choices = (array)$user_choice->$key;
        }
        
        return $choices;
    }
    
    public function question_is_text_type($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'number', 'short_text', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public function text_answer_is($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");

        if (in_array($get_answers, $text_types)) {
            return $get_answers;
        }
        return false;
    }

    public function get_questions_categories(){
        global $wpdb;
        $categories_table = $wpdb->prefix . "aysquiz_categories";
        $get_cats = $wpdb->get_results("SELECT * FROM {$categories_table} ORDER BY title ASC", ARRAY_A);
        return $get_cats;
    }

    public function get_questions_tags(){
        global $wpdb;
        $tags_table = $wpdb->prefix . "aysquiz_question_tags";
        $get_tags = $wpdb->get_results("SELECT * FROM {$tags_table} ORDER BY title ASC", ARRAY_A);
        return $get_tags;
    }

    public function get_questions_tags_by_author_id( $autor_id, $columns ){
        global $wpdb;

        $autor_id = absint( sanitize_text_field( $autor_id ) );

        $table_columns = "*";
        if ( isset( $columns ) && !empty( $columns) ) {
            $table_columns = implode(",", $columns);
        }

        $tags_table = $wpdb->prefix . "aysquiz_question_tags";
        $get_tags = $wpdb->get_results("SELECT {$table_columns} FROM {$tags_table} WHERE `author_id` = {$autor_id} ORDER BY title ASC", ARRAY_A);
        return $get_tags;
    }

    public static function ays_get_quiz_by_id($id){
        global $wpdb;
        $quizzes_table = $wpdb->prefix . "aysquiz_quizes";
        $quiz = $wpdb->get_row("SELECT * FROM {$quizzes_table} WHERE id={$id}", ARRAY_A);
        return $quiz;
    }
    
    public static function ays_get_quiz_options(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'aysquiz_quizes';
        $res = $wpdb->get_results("SELECT id, title FROM $table_name");
        $aysGlobal_array = array();

        foreach ($res as $ays_res_options) {
            $aysStatic_array = array();
            $aysStatic_array[] = $ays_res_options->id;
            $aysStatic_array[] = $ays_res_options->title;
            $aysGlobal_array[] = $aysStatic_array;
        }
        return $aysGlobal_array;
    }

    public function ays_quiz_register_tinymce_plugin($plugin_array){
        $capability = $this->quiz_maker_capabilities();

        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);

        // General Settings | options
        $gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes($this->settings_obj->ays_get_setting('options') ), true);

        // Show quiz button to Admins only
        $gen_options['quiz_show_quiz_button_to_admin_only'] = isset($gen_options['quiz_show_quiz_button_to_admin_only']) ? sanitize_text_field( $gen_options['quiz_show_quiz_button_to_admin_only'] ) : 'off';
        $quiz_show_quiz_button_to_admin_only = (isset($gen_options['quiz_show_quiz_button_to_admin_only']) && sanitize_text_field( $gen_options['quiz_show_quiz_button_to_admin_only'] ) == "on") ? true : false;

        if ( $quiz_show_quiz_button_to_admin_only ) {

            if( current_user_can( $capability ) ){
                $plugin_array['ays_quiz_button_mce'] = AYS_QUIZ_BASE_URL . 'ays_quiz_shortcode.js';
            }

        } else {
            $plugin_array['ays_quiz_button_mce'] = AYS_QUIZ_BASE_URL . 'ays_quiz_shortcode.js';
        }

        return $plugin_array;
    }

    public function ays_quiz_add_tinymce_button($buttons){
        $capability = $this->quiz_maker_capabilities();
        if( current_user_can( $capability ) ){
            $buttons[] = "ays_quiz_button_mce";
        }
        return $buttons;
    }

    public function gen_ays_quiz_shortcode_callback(){
        $shortcode_data = $this->ays_get_quiz_options();
        error_reporting(0);
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title><?php echo __('Quiz Maker', $this->plugin_name); ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
            <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
            <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
            <?php
                wp_print_scripts('jquery');
            ?>
            <base target="_self">
        </head>
        <body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" dir="ltr"
              class="forceColors">
        <div class="select-sb">
            <table align="center">
                <tr>
                    <td>
                        <label for="ays_quiz">Quiz Maker</label>
                    </td>
                    <td>
                        <span>
                            <select id="ays_quiz" style="padding: 2px; height: 25px; font-size: 16px;width:100%;">
                                <option>--<?php echo __('Select Quiz',$this->plugin_name) ?>--</option>
                                   <?php
                                   echo "<pre>";
                                   print_r($shortcode_data);
                                   echo "</pre>";
                                   ?>
                                   <?php foreach ($shortcode_data as $index => $data)
                                       echo '<option id="' . $data[0] . '" value="' . $data[0] . '"  class="ays_quiz_options">' . $data[1] . '</option>';
                                   ?>
                            </select>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="mceActionPanel">
            <input type="submit" id="insert" name="insert" value="Insert" onClick="quiz_insert_shortcode();"/>
        </div>
        <script type="text/javascript">
            function quiz_insert_shortcode() {
                var tagtext = '[ays_quiz id="' + document.getElementById('ays_quiz')[document.getElementById('ays_quiz').selectedIndex].id + '"]';
                window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                tinyMCEPopup.close();
            }
        </script>
        </body>
        </html>
        <?php
        die();
    }

    public function vc_before_init_actions(){
        require_once(AYS_QUIZ_DIR . 'pb_templates/quiz_maker_wpbvc.php');
    }

    public function quiz_maker_el_widgets_registered() {
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        // We check if the Elementor plugin has been installed / activated.
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            // get our own widgets up and running:
            // copied from widgets-manager.php
            if ( class_exists( 'Elementor\Plugin' ) ) {
                if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
                    $elementor = Elementor\Plugin::instance();
                    if ( isset( $elementor->widgets_manager ) ) {
                        if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
                            if ( method_exists( $elementor->widgets_manager, 'register' ) ) {
                                $widget_file   = 'plugins/elementor/quiz_maker_elementor.php';
                                $template_file = locate_template( $widget_file );
                                if ( !$template_file || !is_readable( $template_file ) ) {
                                    $template_file = AYS_QUIZ_DIR.'pb_templates/quiz_maker_elementor.php';
                                }
                                if ( $template_file && is_readable( $template_file ) ) {
                                    require_once $template_file;
                                    Elementor\Plugin::instance()->widgets_manager->register( new Elementor\Widget_Quiz_Maker_Elementor() );
                                }
                            }
                        } else { 
                            if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {
                                $widget_file   = 'plugins/elementor/quiz_maker_elementor.php';
                                $template_file = locate_template( $widget_file );
                                if ( !$template_file || !is_readable( $template_file ) ) {
                                    $template_file = AYS_QUIZ_DIR.'pb_templates/quiz_maker_elementor.php';
                                }
                                if ( $template_file && is_readable( $template_file ) ) {
                                    require_once $template_file;
                                    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Quiz_Maker_Elementor() );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function deactivate_plugin_option(){
        error_reporting(0);
        $request_value = $_REQUEST['upgrade_plugin'];
        $upgrade_option = get_option('ays_quiz_maker_upgrade_plugin','');
        if($upgrade_option === ''){
            add_option('ays_quiz_maker_upgrade_plugin',$request_value);
        }else{
            update_option('ays_quiz_maker_upgrade_plugin',$request_value);
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array('option'=>get_option('ays_quiz_maker_upgrade_plugin','')));
        wp_die();
    }

    public static function ays_restriction_string($type, $x, $length){
        $output = "";
        switch($type){
            case "char":                
                if(strlen($x)<=$length){
                    $output = $x;
                } else {
                    $output = substr($x,0,$length) . '...';
                }
                break;
            case "word":
                $res = explode(" ", $x);
                if(count($res)<=$length){
                    $output = implode(" ",$res);
                } else {
                    $res = array_slice($res,0,$length);
                    $output = implode(" ",$res) . '...';
                }
            break;
        }
        return $output;
    }
    
    public static function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    //widget
    public function load_quiz_maker_widget(){
        
        require_once AYS_QUIZ_DIR . "/widget/quiz-maker-widget.php";
        register_widget('Quiz_Maker_Widget');

    }
    
    // Title change function in dashboard
    public function change_dashboard_title( $admin_title ) {
        
        global $current_screen;
        global $wpdb;
        
        if(strpos($current_screen->id, $this->plugin_name) === false){
            return $admin_title;
        }
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';        
        $quiz_id = (isset($_GET['quiz'])) ? absint(intval($_GET['quiz'])) : null;
        $question_id = (isset($_GET['question'])) ? absint(intval($_GET['question'])) : null;
        $question_cat_id = (isset($_GET['question_category'])) ? absint(intval($_GET['question_category'])) : null;
        $quiz_cat_id = (isset($_GET['quiz_category'])) ? absint(intval($_GET['quiz_category'])) : null;
        $quiz_attribute_id = (isset($_GET['quiz_attribute'])) ? absint(intval($_GET['quiz_attribute'])) : null;
        
        if($quiz_id !== null){
            $id = $quiz_id;
        }elseif($question_id !== null){
            $id = $question_id;
        }elseif($question_cat_id !== null){
            $id = $question_cat_id;
        }elseif($quiz_cat_id !== null){
            $id = $quiz_cat_id;
        }elseif($quiz_attribute_id !== null){
            $id = $quiz_attribute_id;
        }else{
            $id = null;
        }
        
        $current = explode($this->plugin_name, $current_screen->id);
        $current = trim($current[count($current)-1], "-");
        $sql = '';
        switch($current){
            case "":
                $page = __("Quiz", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_quizes WHERE id=".$id;
                }
                break;
            case "questions":
                $page = __("Question", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_questions WHERE id=".$id;
                }
                break;
            case "quiz-categories":
                $page = __("Category", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_quizcategories WHERE id=".$id;
                }
                break;
            case "question-categories":
                $page = __("Category", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_categories WHERE id=".$id;
                }
                break;
            case "quiz-attributes":
                $page = __("Attribute", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_attributes WHERE id=".$id;
                }
                break;
            case "each-result":
                $page = __("Results", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_quizes WHERE id=".$id;
                }
                break;
            default:
                $page = '';
                $sql = '';
                break;
        }
        $results = null;
        if($sql != ""){
            $results = $wpdb->get_row($sql, "ARRAY_A");
        }
        $change_title = null;
        switch($action){
            case "add":
                $change_title = __("Add New", $this->plugin_name) ." ‹ ".$page;
                break;
            case "edit":
                if($results !== null){
                    $title = "";
                    if($current == "questions"){
                        if($results['type'] == 'custom'){
                            if(isset($results['question_title']) && $results['question_title'] != ''){
                                $title = htmlspecialchars_decode($results['question_title'], ENT_COMPAT);
                                $title = stripslashes( $results['question_title'] );
                            }else{
                                $title = __( 'Custom question', $this->plugin_name ) . ' #' . $results['id'];
                            }
                        }else{
                            $title = '';
                            if(isset($results['question_title']) && $results['question_title'] != ''){
                                $title = esc_attr( $results['question_title'] );
                            }elseif(isset($results['question']) && strlen($results['question']) != 0){
                                $title = strip_tags( stripslashes( $results['question'] ) );
                            }elseif ((isset($results['question_image']) && $results['question_image'] !='')){
                                $title = __( 'Image question', $this->plugin_name );
                            }
                            $title = Quiz_Maker_Admin::ays_restriction_string("word", $title, 10);
                            $title = esc_attr( $title );
                        }
                    }elseif($current == "quiz-attributes"){                        
                        $title = stripslashes( $results['name'] );
                    }else{                        
                        $title = stripslashes( $results['title'] );
                    }
                    $title = strip_tags($title);
                    $change_title = $this->ays_restriction_string("word", $title, 5) ." ‹ ". __("Edit", $this->plugin_name) . " ".$page;
                }
                break;
            default:
                $change_title = $admin_title;
                break;
        }
        if($current == "each-result"){
            $title = $results['title'];
            $change_title = $this->ays_restriction_string("word", $title, 5) ." ‹ ".$page;
        }
        if($change_title === null){
            $change_title = $admin_title;
        }
        
        return $change_title;

    }
    
    // Mailchimp - Get mailchimp lists
    public function ays_get_mailchimp_lists($username, $api_key){
        error_reporting(0);
        if($username == ""){
            return array(
                'total_items' => 0
            );
        }
        if($api_key == ""){
            return array(
                'total_items' => 0
            );
        }
        
        $api_prefix = explode("-",$api_key)[1];
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/?count=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_USERPWD => "$username:$api_key",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           // echo "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }
    
    // Mailchimp - Get mailchimp list
    public static function ays_get_mailchimp_list($username, $api_key, $list_id){
        error_reporting(0);
        if($username == ""){
            return array();
        }
        if($api_key == ""){
            return array();
        }
        if($list_id == ""){
            return array();
        }

        $api_prefix = explode("-",$api_key)[1];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/".$list_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_USERPWD => "$username:$api_key",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           // echo "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    // Mailchimp update list
    public static function ays_add_mailchimp_update_list($username, $api_key, $list_id, $args){
        if($username == "" || $api_key == ""){
            return false;
        }

        if( $list_id == '' ){
            return false;
        }

        if( ! isset( $args['double_optin'] ) || ! array_key_exists( 'double_optin', $args ) ){
            return false;
        }

        $list_data = Quiz_Maker_Admin::ays_get_mailchimp_list( $username, $api_key, $list_id );

        if( empty( $list_data ) ){
            return false;
        }

        $double_optin = isset( $args['double_optin'] ) && $args['double_optin'] == 'on' ? true : false;

        $fields = array(
            "name" => $list_data['name'],
            "contact" => $list_data['contact'],
            "permission_reminder" => $list_data['permission_reminder'],
            "use_archive_bar" => $list_data['use_archive_bar'],
            "campaign_defaults" => $list_data['campaign_defaults'],
            "email_type_option" => $list_data['email_type_option'],
            "double_optin" => $double_optin,
        );

        $api_prefix = explode("-",$api_key)[1];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/".$list_id."/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERPWD => "$username:$api_key",
            CURLOPT_CUSTOMREQUEST => "PATCH",
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
            return json_decode( $response, true );
        }
    }

	// Campaign Monitor - Get subscribe lists
	public function ays_get_monitor_lists($client, $api_key){
		error_reporting(0);
		if ($client == "" || $api_key == "") {
			return array(
				'Code' => 0
			);
		}


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.createsend.com/api/v3.2/clients/$client/lists.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_USERPWD => "$api_key:x",
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return array(
				'Code'       => 0,
				'cURL Error' => $err
			);
		} else {
			return json_decode($response,true);
		}
	}

	// Slack - Get channels
	public function ays_get_slack_conversations( $token ) {
		error_reporting(0);
		if ($token == "") {
			return array(
				'Code' => 0
			);
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL            => "https://slack.com/api/conversations.list",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPHEADER     => array(
                "Authorization: Bearer $token",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err      = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return array(
				'Code'       => 0,
				'cURL Error' => $err
			);
		} else {
			return json_decode($response, true)['channels'];
		}
	}

	// Campaign Monitor - Get subscribe lists
	public function ays_get_active_camp_data( $data, $url, $api_key ) {
		error_reporting(0);
		if ($url == "" || $api_key == "") {
			return array(
				'Code' => 0
			);
		}


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL            => "$url/api/3/$data?limit=1000",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
			CURLOPT_HTTPHEADER     => array(
				"Content-Type: application/json",
				"cache-control: no-cache",
				"Api-Token: $api_key"
			),
		));

		$response = curl_exec($curl);
		$err      = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return array(
				'Code'       => 0,
				'cURL Error' => $err
			);
		} else {
			return json_decode($response, true);
		}
	}
    
    // EXPORT FILTERS
    public function ays_show_filters(){
        error_reporting(0);
        global $wpdb;
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $quiz_table = $wpdb->prefix . "aysquiz_quizes";
        $current_user = get_current_user_id();
        $db_prefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_show_filters') {
            
            $user_sql = "SELECT
                    $results_table.user_id,
                    {$db_prefix}users.display_name
                FROM $results_table
                JOIN {$db_prefix}users
                    ON $results_table.user_id = {$db_prefix}users.ID
                GROUP BY
                    $results_table.user_id";
            
            if( ! $this->current_user_can_edit ){
                $user_sql = "SELECT
                        r.user_id,
                        u.display_name 
                    FROM
                        $results_table AS r
                    JOIN {$db_prefix}users AS u
                        ON r.user_id = u.ID 
                    LEFT JOIN {$quiz_table} AS q
                        ON r.quiz_id = q.id
                    WHERE q.author_id = {$current_user}
                        AND r.status = 'finished'
                    GROUP BY
                        r.user_id";
            }

            $users = $wpdb->get_results( $user_sql, "ARRAY_A" );

           // $is_there_guest = 0 == $wpdb->get_var("SELECT MIN(user_id) FROM {$results_table}");

           // if ($is_there_guest) {
           //     $users[] = array('user_id' => 0, 'display_name' => 'Guests');
           // }
            $quizzes_sql = "SELECT
                    $results_table.quiz_id,
                    $quiz_table.title 
                FROM
                    $results_table
                JOIN $quiz_table ON $results_table.quiz_id = $quiz_table.id
                GROUP BY
                    $results_table.quiz_id";

            if( ! $this->current_user_can_edit ){
                $quizzes_sql = "SELECT
                        r.quiz_id,
                        q.title 
                    FROM $results_table AS r
                    JOIN $quiz_table AS q
                        ON r.quiz_id = q.id
                    WHERE q.author_id = {$current_user}
                    GROUP BY
                        r.quiz_id";
            }

            $quizzes = $wpdb->get_results( $quizzes_sql, "ARRAY_A" );

            $min_date_sql = "SELECT DATE(MIN(start_date)) FROM {$results_table}";
            $max_date_sql = "SELECT DATE(MAX(start_date)) FROM {$results_table}";
            if( ! $this->current_user_can_edit ){
                $min_date_sql = "SELECT DATE(MIN(r.start_date))
                                FROM {$results_table} AS r
                                LEFT JOIN {$quiz_table} AS q
                                    ON r.quiz_id = q.id
                                WHERE q.author_id = ".$current_user;
                $max_date_sql = "SELECT DATE(MAX(r.start_date))
                                FROM {$results_table} AS r
                                LEFT JOIN {$quiz_table} AS q
                                    ON r.quiz_id = q.id
                                WHERE q.author_id = ".$current_user;
            }
            $date_min = $wpdb->get_var($min_date_sql);
            $date_max = $wpdb->get_var($max_date_sql);
            $gusets_in_sql = '';
            if (isset($_REQUEST['flag']) && $_REQUEST['flag']) {
                $gusets_in_sql = ' AND user_id != 0 ';
            }
            if (isset($_REQUEST['with_guests']) && $_REQUEST['with_guests']) {
                $gusets_in_sql = ' AND user_id != 0 ';
            }

            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `status` = 'finished' $gusets_in_sql ORDER BY id DESC";
            if( ! $this->current_user_can_edit ){
                $sql = "SELECT COUNT(*) 
                        FROM {$results_table} AS r
                        LEFT JOIN {$quiz_table} AS q
                            ON r.quiz_id = q.id
                        WHERE q.author_id = {$current_user} $gusets_in_sql
                        ORDER BY r.id DESC";
            }

            if (isset($_REQUEST['flag']) && $_REQUEST['flag']) {
                $quiz_id_sql = "SELECT quiz_id FROM {$results_table} WHERE user_id != 0";
                if( ! $this->current_user_can_edit ){
                    $quiz_id_sql = "SELECT rp.quiz_id 
                                    FROM {$results_table} AS rp
                                    LEFT JOIN {$quiz_table} AS qz
                                        ON rp.quiz_id = qz.id
                                    WHERE user_id != 0 AND qz.author_id = ".$current_user;
                }
                $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? intval($_REQUEST['quiz_id']) : $quiz_id_sql;
                $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id IN ($quiz_id) AND `status` = 'finished' AND user_id != 0 ORDER BY id DESC";
                if( ! $this->current_user_can_edit ){
                    $sql = "SELECT COUNT(*) 
                            FROM {$results_table} AS r
                            LEFT JOIN {$quiz_table} AS q
                                ON r.quiz_id = q.id
                            WHERE q.author_id = {$current_user} AND
                                quiz_id IN ($quiz_id) AND r.user_id != 0
                            ORDER BY r.id DESC";
                }
            }
            $qanak = $wpdb->get_var($sql);
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                "quizzes" => $quizzes,
                "users" => $users,
                "date_min" => $date_min,
                "date_max" => $date_max,
                "count" => $qanak
            ));
            wp_die();
        }
    }
    
    // EXPORT FILTERS
    public function ays_show_questions_filters(){
        error_reporting(0);
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $categories_table = $wpdb->prefix . "aysquiz_categories";
        $tags_table = $wpdb->prefix . "aysquiz_question_tags";
        $current_user = get_current_user_id();
        $db_prefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_show_questions_filters') {

            $author_sql = "SELECT
                    $questions_table.author_id,
                    {$db_prefix}users.display_name
                FROM $questions_table
                JOIN {$db_prefix}users
                    ON $questions_table.author_id = {$db_prefix}users.ID
                GROUP BY
                    $questions_table.author_id";

            if( ! $this->current_user_can_edit ){
                $author_sql = "SELECT
                        q.author_id,
                        u.display_name
                    FROM
                        $questions_table AS q
                    JOIN {$db_prefix}users AS u
                        ON q.author_id = u.ID
                    WHERE q.author_id = {$current_user}
                    GROUP BY
                        q.author_id";
            }

            $authors = $wpdb->get_results( $author_sql, "ARRAY_A" );

           // $is_there_guest = 0 == $wpdb->get_var("SELECT MIN(user_id) FROM {$results_table}");

           // if ($is_there_guest) {
           //     $users[] = array('user_id' => 0, 'display_name' => 'Guests');
           // }
            $categories_sql = "SELECT
                    $questions_table.category_id,
                    $categories_table.title
                FROM
                    $questions_table
                JOIN $categories_table ON $questions_table.category_id = $categories_table.id
                GROUP BY
                    $questions_table.category_id
                ORDER BY $categories_table.title ASC";

            if( ! $this->current_user_can_edit ){
                $categories_sql = "SELECT
                        q.category_id,
                        c.title
                    FROM $questions_table AS q
                    JOIN $categories_table AS c
                        ON q.category_id = c.id
                    WHERE q.author_id = {$current_user}
                    GROUP BY
                        q.category_id
                    ORDER BY c.title ASC";
            }

            $categories = $wpdb->get_results( $categories_sql, "ARRAY_A" );

            $tags = array();
            $tag_table_columns = array('id', 'title');
            if( ! $this->current_user_can_edit ){
                $tags = $this->get_questions_tags_by_author_id($current_user, $tag_table_columns );
            } else {
                $tags = $this->get_questions_tags();
            }

            $min_date_sql = "SELECT DATE(MIN(create_date)) FROM {$questions_table}";
            $max_date_sql = "SELECT DATE(MAX(create_date)) FROM {$questions_table}";
            if( ! $this->current_user_can_edit ){
                $min_date_sql = "SELECT DATE(MIN(q.create_date))
                                FROM {$questions_table} AS q
                                LEFT JOIN {$categories_table} AS c
                                    ON q.category_id = c.id
                                WHERE q.author_id = ".$current_user;
                $max_date_sql = "SELECT DATE(MAX(q.create_date))
                                FROM {$questions_table} AS q
                                LEFT JOIN {$categories_table} AS c
                                    ON q.category_id = c.id
                                WHERE q.author_id = ".$current_user;
            }
            $date_min = $wpdb->get_var($min_date_sql);
            $date_max = $wpdb->get_var($max_date_sql);

            $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
            $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

            $sql = "SELECT COUNT(*)
                    FROM {$wpdb->prefix}aysquiz_questions
                    WHERE create_date IS NULL OR create_date = '0000-00-00 00:00:00' OR create_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND published != 2
                    ORDER BY id DESC";
            if( ! $this->current_user_can_edit ){
                $sql = "SELECT COUNT(*) AS count
                        FROM {$wpdb->prefix}aysquiz_questions AS q
                        LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                            ON q.category_id = c.id
                        WHERE q.author_id = {$current_user} AND
                            q.create_date BETWEEN '$date_from' AND '$date_to 23:59:59' AND
                            q.published != 2
                        ORDER BY q.id DESC";
            }


            $count = $wpdb->get_var($sql);
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                "categories" => $categories,
                "tags" => $tags,
                "authors" => $authors,
                "date_min" => $date_min,
                "date_max" => $date_max,
                "count" => $count
            ));
            wp_die();
        }
    }

    public function quiz_maker_add_dashboard_widgets() {
        $capability = $this->quiz_maker_capabilities();
        if( current_user_can( $capability ) ){
            wp_add_dashboard_widget(
                'quiz-maker', 
                __( 'Quiz Maker Status', $this->plugin_name ),
                array( $this, 'quiz_maker_dashboard_widget' )
            );

            // Globalize the metaboxes array, this holds all the widgets for wp-admin
            global $wp_meta_boxes;

            // Get the regular dashboard widgets array 
            // (which has our new widget already but at the end)
            $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

            // Backup and delete our new dashboard widget from the end of the array
            $example_widget_backup = array( 
                'quiz-maker' => $normal_dashboard['quiz-maker'] 
            );
            unset( $normal_dashboard['example_dashboard_widget'] );

            // Merge the two arrays together so our widget is at the beginning
            $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );

            // Save the sorted array back into the original metaboxes 
            $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
        }
    } 

    /**
     * Create the function to output the contents of our Dashboard Widget.
     */
    public function quiz_maker_dashboard_widget() {
        global $wpdb;
        $questions_count = Questions_List_Table::record_count_for_dashboard();
        $quizzes_count = Quizes_List_Table::record_count();
        $results_count = Results_List_Table::unread_records_count();
        
        $questions_label = intval($questions_count) == 1 ? __( "question", $this->plugin_name ) : __( "questions", $this->plugin_name );
        $quizzes_label = intval($quizzes_count) == 1 ? __( "quiz", $this->plugin_name ) : __( "quizzes", $this->plugin_name );
        $results_label = intval($results_count) == 1 ? __( "new result", $this->plugin_name ) : __( "new results", $this->plugin_name );
        
        // Display whatever it is you want to show.
        ?>
        <ul class="ays_quiz_maker_dashboard_widget">
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name; ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/icon-128x128.png"; ?>" alt="Quizzes">
                    <span><?php echo $quizzes_count; ?></span>
                    <span><?php echo $quizzes_label; ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-questions" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/question2.png"; ?>" alt="Questions">
                    <span><?php echo $questions_count; ?></span>
                    <span><?php echo $questions_label; ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-results" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/users2.png"; ?>" alt="Results">
                    <span><?php echo $results_count; ?></span>
                    <span><?php echo $results_label; ?></span>
                </a>
            </li>
        </ul>
        <div style="padding:10px;font-size:14px;border-top:1px solid #ccc;">
            <?php
                echo sprintf(
                    __( 'Works version %s of ', $this->plugin_name ),
                    AYS_QUIZ_VERSION
                );
            ?>
            <a href="<?php echo "admin.php?page=".$this->plugin_name ?>"><?php echo __( 'Quiz Maker', $this->plugin_name ); ?></a>
        </div>
    <?php
    }
    
    public static function ays_query_string($remove_items){
        $query_string = $_SERVER['QUERY_STRING'];
        $query_items = explode( "&", $query_string );
        foreach($query_items as $key => $value){
            $item = explode("=", $value);
            foreach($remove_items as $k => $i){
                if(in_array($i, $item)){
                    unset($query_items[$key]);
                }
            }
        }
        return implode( "&", $query_items );
    }    
    
    public function quiz_maker_admin_footer(){
        if(isset($_REQUEST['page'])){
            if(false !== strpos($_REQUEST['page'], $this->plugin_name)){
                ?>
                <p style="font-size:13px;text-align:center;font-style:italic;">
                    <span style="margin-left:0px;margin-right:10px;" class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                    <span><?php echo __( "If you love our plugin, please do big favor and rate us on", $this->plugin_name); ?></span> 
                    <!--<a target="_blank" href='https://bit.ly/2m4Cya8'>WordPress.org</a>-->
                    <a target="_blank" href='https://wordpress.org/support/plugin/quiz-maker/reviews/?rate=5#new-post'>WordPress.org</a>
                    <span class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                </p>
            <?php
            }
        }
    }
    
    /**
     * Check if Block Editor is active.
     * Must only be used after plugins_loaded action is fired.
     *
     * @return bool
     */
    public static function is_active_gutenberg() {
        // Gutenberg plugin is installed and activated.
        $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );
        // Block editor since 5.0.
        $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

        if ( ! $gutenberg && ! $block_editor ) {
            return false;
        }

        if ( self::is_classic_editor_plugin_active() ) {
            $editor_option       = get_option( 'classic-editor-replace' );
            $block_editor_active = array( 'no-replace', 'block' );

            return in_array( $editor_option, $block_editor_active, true );
        }

        return true;
    }

    /**
     * Check if Classic Editor plugin is active.
     *
     * @return bool
     */
    public static function is_classic_editor_plugin_active() {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
            return true;
        }

        return false;
    }

    public static function string_starts_with_number($string){
        $match = preg_match('/^\d/', $string);
        if($match === 1){
            return true;
        }else{
            return false;
        }
    }

    public function get_quiz_attributes_by_id($ids){
        global $wpdb;
        if (!empty($ids)) {
            $quiz_attributes = implode(',', $ids);
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes WHERE `id` IN ($quiz_attributes) AND published = 1";
            $results = $wpdb->get_results($sql);
            return $results;
        }
        return array();
    }

     // SEND TESTING MAIL (AV)
    public function ays_send_testing_mail(){
        error_reporting(0);
        if(isset($_REQUEST['ays_test_email']) && filter_var($_REQUEST['ays_test_email'], FILTER_VALIDATE_EMAIL)){
            $quiz_id = absint(intval($_REQUEST['ays_quiz_id_for_test']));
            $nsite_url_base = get_site_url();
            $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
            $nsite_url = trim( $nsite_url_replaced, '/' );
            $nno_reply = "noreply@".$nsite_url;

            if(isset($_REQUEST['ays_email_configuration_from_name']) && $_REQUEST['ays_email_configuration_from_name'] != "") {
                $uname = stripslashes($_REQUEST['ays_email_configuration_from_name']);
            } else {
                $uname = 'Quiz Maker';
            }

            if(isset($_REQUEST['ays_email_configuration_from_email']) && $_REQUEST['ays_email_configuration_from_email'] != "") {
                $nfrom = "From: " . $uname . " <".stripslashes($_REQUEST['ays_email_configuration_from_email']).">";
            }else{
                $nfrom = "From: " . $uname . " <quiz_maker@".$nsite_url.">";
            }

            if(isset($_REQUEST['ays_email_configuration_from_subject']) && $_REQUEST['ays_email_configuration_from_subject'] != "") {
                $subject = stripslashes($_REQUEST['ays_email_configuration_from_subject']);
            } else {
                $subject = stripslashes($_REQUEST['ays_quiz_title']);
            }

            $headers = $nfrom."\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $attachment = array();

            $message_content = isset($_REQUEST['ays_mail_message']) && !empty($_REQUEST['ays_mail_message']) ? stripslashes($_REQUEST['ays_mail_message']) : __( "Message text", $this->plugin_name );
            $ays_quiz_attributes = isset($_REQUEST['ays_quiz_attributes']) && !empty($_REQUEST['ays_quiz_attributes']) ? $_REQUEST['ays_quiz_attributes'] : array();

            $quiz_attributes = $this->get_quiz_attributes_by_id($ays_quiz_attributes);

            if (isset($_REQUEST['ays_show_interval_message']) && $_REQUEST['ays_show_interval_message'] == "on") {
                $show_interval_message = (isset($_REQUEST['ays_show_interval_message']) && $_REQUEST['ays_show_interval_message'] == 'on') ? true : false;
                if ($show_interval_message) {
                    $int_count = count($_REQUEST['interval_min']);
                    $int_index = rand(0,$int_count);
                    $interval_msg = stripslashes($_REQUEST['interval_text'][$int_index]);
                    $interval_image = $_REQUEST['interval_image'][$int_index];
                    $interval_message = "<div>";
                    if($interval_image !== null || $interval_image != ''){
                        $interval_message .= "<div style='width:100%;max-width:400px;margin:10px auto;'>";
                        $interval_message .= "<img style='max-width:100%;' src='".$interval_image."'>";
                        $interval_message .= "</div>";
                    }
                    if($interval_msg !== null || $interval_msg != ''){
                        $interval_message .= "<div>" . $interval_msg . "</div>";
                    }
                    $interval_message .= "</div>";

                    if($interval_msg == '' && $interval_image == ''){
                        $interval_message = "";
                    }
                }
                $message_content .= $interval_message;
            }

            if (isset($_REQUEST['ays_send_results_user']) && $_REQUEST['ays_send_results_user'] == "on") {

                $message_content .= '<table style="border-collapse:collapse;width:100%">
                    <tr>
                        <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'. __( "Name", $this->plugin_name ) .'</td>
                        <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "User Name here", $this->plugin_name ) .'</em></td>
                   </tr>
                   <tr>
                        <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'. __( "Email", $this->plugin_name ) .'</td>
                        <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "User Email here", $this->plugin_name ) .'</em></td>
                   </tr>
                   <tr>
                        <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'. __( "Phone", $this->plugin_name ) .'</td>
                        <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "User Phone here", $this->plugin_name ) .'</em></td>
                   </tr>';

               if (isset($quiz_attributes) && !empty($quiz_attributes)) {
                  foreach ($quiz_attributes as $attr_value) {
                    $message_content .= '<tr>
                            <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'.$attr_value->name.'</td>
                            <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "User value for", $this->plugin_name ) .' '.$attr_value->name.' '. __( "field", $this->plugin_name ) .'</em></td>
                        </tr> ';
                  }
               }
               $message_content .= '<tr>
                            <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'. __( "Duration", $this->plugin_name ) .'</td>
                            <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "Duration of passing the quiz", $this->plugin_name ) .' E.g. 45 seconds</em></td>
                       </tr>
                       <tr>
                            <td style="font-weight:600;border:1px solid #ccc;padding:10px 11px 9px 6px">'. __( "Score", $this->plugin_name ) .'</td>
                            <td style="border:1px solid #ccc;text-align:center;padding:10px 11px 9px 6px" colspan="3"><em>'. __( "Score by percentage", $this->plugin_name ) .' E.g. 75%</em></td>
                       </tr>
                       <tr>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "Question", $this->plugin_name ) .' 1 :</strong><br>
                                <p>'. __( "Question", $this->plugin_name ) .' 1 '. __( "example", $this->plugin_name ) .'?</p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "Correct answer", $this->plugin_name ) .':</strong><br>
                                <p><em>'. __( "Correct answer here", $this->plugin_name ) .'</em></p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "User answer", $this->plugin_name ) .':</strong><br>
                                <p><em>'. __( "User answer here", $this->plugin_name ) .'</em></p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <p style="font-weight:600;color:red">'. __( "Fail", $this->plugin_name ) .'</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "Question", $this->plugin_name ) .' 2 :</strong><br>
                                <p>'. __( "Question", $this->plugin_name ) .' 2 '. __( "example", $this->plugin_name ) .'?</p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "Correct answer", $this->plugin_name ) .':</strong><br>
                                <p><em>'. __( "Correct answer here", $this->plugin_name ) .'</em></p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <strong>'. __( "User answer", $this->plugin_name ) .':</strong><br>
                                <p><em>'. __( "User answer here", $this->plugin_name ) .'</em></p>
                            </td>
                            <td style="border:1px solid #ccc;padding:10px 11px 9px 6px">
                                <p style="font-weight:600;color:green">'. __( "Success", $this->plugin_name ) .'</p>
                            </td>
                        </tr>
                </table>';
            }

            $message = $message_content;
            $to = $_REQUEST['ays_test_email'];

            $ays_send_test_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
            $response_text = __( "Test email delivered", $this->plugin_name );
            if($ays_send_test_mail === false){
                $response_text = __( "Test email not delivered", $this->plugin_name );
            }

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'mail' => $ays_send_test_mail,
                'message' => $response_text,
            ));
            wp_die();
        }else{
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Test email not delivered", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }
    }

     /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     *
     * @access  public
     * @param   array       $links_array            An array of the plugin's metadata
     * @param   string      $plugin_file_name       Path to the plugin file
     * @param   array       $plugin_data            An array of plugin data
     * @param   string      $status                 Status of the plugin
     * @return  array       $links_array
     */
    function quiz_maker_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ){

        if ( AYS_QUIZ_BASENAME === $plugin_file_name ) {
            $index = count($links_array) - 1;
            $view_details = $links_array[$index];
            $view_details = strip_tags($view_details);
            if($view_details == 'View details' || $view_details == 'Visit plugin site'){
                unset($links_array[$index]);
            }
			$row_meta = array(
				'visit' => '<a href="https://ays-pro.com/wordpress/quiz-maker" aria-label="' . esc_attr__( 'Visit plugin site', $this->plugin_name ) . '" target="_blank">' . esc_html__( 'Visit plugin site', $this->plugin_name ) . '</a>',
				'docs' => '<a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" aria-label="' . esc_attr__( 'Quiz Maker Documentation', $this->plugin_name ) . '" target="_blank">' . esc_html__( 'Documentation', $this->plugin_name ) . '</a>',
			);

			return array_merge( $links_array, $row_meta );
		}

		return (array) $links_array;
    }

    public function ays_live_preivew_content(){
        error_reporting(0);
        $content = isset($_REQUEST['content']) && $_REQUEST['content'] != '' ? $_REQUEST['content'] : null;
        if($content === null){
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => false,
            ));
        }
        $content = Quiz_Maker_Data::ays_autoembed( $content );
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'status' => true,
            'content' => $content,
        ));
        wp_die();
    }

    public function ays_quiz_users_search(){
        error_reporting(0);
        $checked = isset($_REQUEST['val']) && $_REQUEST['val'] !='' ? $_REQUEST['val'] : null;
        $search = isset($_REQUEST['q']) && trim($_REQUEST['q']) !='' ? sanitize_text_field( trim( $_REQUEST['q'] ) ) : null;

        $content_text = array(
            'results' => array()
        );

        $args = 'search=';
        if($search !== null){
            $args .= $search;
            $args .= '*';
        } else {
            ob_end_clean();
            echo json_encode( $content_text);
            wp_die();
        }

        $users = get_users($args);

        $content_text = array(
            'results' => array()
        );

        foreach ($users as $key => $value) {
            if ($checked !== null) {
                if (in_array($value->ID, $checked)) {
                    continue;
                }else{
                    $content_text['results'][] = array(
                        'id' => $value->ID,
                        'text' => $value->data->display_name,
                    );
                }
            }else{
                $content_text['results'][] = array(
                    'id' => $value->ID,
                    'text' => $value->data->display_name,
                );
            }
        }

        ob_end_clean();
        echo json_encode( $content_text);

        wp_die();
    }

    public function ays_quiz_reports_user_search() {
        error_reporting(0);
        global $wpdb;

        $search = isset($_REQUEST['search']) && $_REQUEST['search'] != '' ? $_REQUEST['search'] : null;
        $checked = isset($_REQUEST['val']) && $_REQUEST['val'] !='' ? $_REQUEST['val'] : null;
        $users_sql = "SELECT user_id
                       FROM {$wpdb->prefix}aysquiz_reports
                       GROUP BY user_id";
        $users = $wpdb->get_results($users_sql,"ARRAY_A");
        $args = array();
        $arg = '';

        if($search !== null){
             $arg .= $search;
             $arg .= '*';
             $args['search'] = $arg;
        }

        foreach ($users as $key => $value ) {
            $args['include'][] = $value['user_id'];
        }

        $reports_users = get_users($args);
        $response = array(
            'results' => array()
        );
        if(empty($args)){
            $reports_users = '';
        }

        foreach ($reports_users as $key => $user) {
            if ($checked !== null) {
                if (in_array($user->ID, $checked)) {
                    continue;
                }else{
                    $response['results'][] = array(
                        'id' => $user->ID,
                        'text' => $user->data->display_name
                    );
                }
            }else{
                $response['results'][] = array(
                    'id' => $user->ID,
                    'text' => $user->data->display_name,
                );
            }
        }

        ob_end_clean();
        // $ob_get_clean = ob_get_clean();
        echo json_encode($response);
        wp_die();
    }

    public function ays_save_google_credentials(){
        global $wpdb;

        // Google sheets
        $google_client = isset($_REQUEST['client_id']) ? $_REQUEST['client_id'] : '';
        $google_secret = isset($_REQUEST['client_secret']) ? $_REQUEST['client_secret'] : '';
        $google_redirect_uri = isset($_REQUEST['redirect_uri']) ? $_REQUEST['redirect_uri'] : '';

        $google_sheets = array(
            'client' => $google_client,
            'secret' => $google_secret,
            'redirect_uri' => $google_redirect_uri,
        );

        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $value = array(
            'meta_value'  => json_encode( $google_sheets ),
        );
        $value_s = array( '%s' );

        $result = $wpdb->update(
            $settings_table,
            $value,
            array( 'meta_key' => 'google' ),
            $value_s,
            array( '%s' )
        );

        ob_end_clean();
        echo json_encode(array(

        ));
        wp_die();
    }

    // Generate certificat
    public function ays_generate_cert_preview(){
        error_reporting(0);

        if(isset($_REQUEST['ays_certificate_title']) && $_REQUEST['ays_certificate_title'] != "") {
            $certificate_title = stripslashes($_REQUEST['ays_certificate_title']);
        } else {
            $certificate_title = '';
        }

        if(isset($_REQUEST['ays_certificate_body']) && $_REQUEST['ays_certificate_body'] != "") {
            $certificate_body = stripslashes($_REQUEST['ays_certificate_body']);
        }else{
            $certificate_body = '';
        }

        // Certificate background image
        $certificate_image = (isset($_REQUEST['ays_certificate_image']) && $_REQUEST['ays_certificate_image'] != '') ? $_REQUEST['ays_certificate_image'] : '';

        // Certificate background frame
        $certificate_frame = (isset($_REQUEST['ays_certificate_frame']) && $_REQUEST['ays_certificate_frame'] != '') ? $_REQUEST['ays_certificate_frame'] : 'default';

        // Certificate orientation
        $certificate_orientation = (isset($_REQUEST['ays_certificate_orientation']) && $_REQUEST['ays_certificate_orientation'] != '') ? $_REQUEST['ays_certificate_orientation'] : 'l';

        // Quiz title
        $quiz_title = (isset($_REQUEST['ays_quiz_title']) && $_REQUEST['ays_quiz_title'] != '') ? $_REQUEST['ays_quiz_title'] : 'Quiz';

        // Variables
        $message_data = array();


        $pdf = new Quiz_PDF_API();
        $pdfData = array(
            "type"          => "pdfapi",
            "cert_title"    => $certificate_title,
            "cert_body"     => $certificate_body,
            "cert_score"    => 100,
            "cert_data"     => $message_data,
            "cert_user"     => "John Smith",
            "cert_quiz"     => $quiz_title,
            "cert_image"    => $certificate_image,
            "cert_frame"    => $certificate_frame,
            "cert_orientation" => $certificate_orientation,
            "current_date"  => date_i18n( get_option( 'date_format' ), strtotime( sanitize_text_field( current_time( 'mysql' ) ) ) ),
        );
        $pdf_response = $pdf->generate_PDF($pdfData);
        $pdf_content = $pdf_response['status'];

        if($pdf_content){
            $cert_url = $pdf_response['cert_url']; //AYS_QUIZ_PUBLIC_URL . "/certificate.pdf";
            $cert_open = __( "Open certificate", $this->plugin_name );

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'certUrl' => $cert_url,
                'open' => $cert_open,
            ));
            wp_die();
        }else{
            $fail = __( "Something is wrong please try again later.", $this->plugin_name );

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => false,
                'fail' => $fail,
            ));
            wp_die();
        }
    }

    public function ays_quiz_generate_keyword_array( $max_val ) {
        if (is_null($max_val) || $max_val == '') {
            $max_val = 6; //'F';
        }
        $max_val = absint(intval($max_val)) - 1;

        $keyword_arr = array();
        $letters = range('A', 'Z');

        if($max_val <= 25){
            $max_alpha_val = $letters[$max_val];
        }elseif($max_val > 25){
            $dividend = ($max_val + 1);
            $max_alpha_val = '';
            $modulo;
            while ($dividend > 0){
                $modulo = ($dividend - 1) % 26;
                $max_alpha_val = $letters[$modulo] . $max_alpha_val;
                $dividend = floor((($dividend - $modulo) / 26));
            }
        }

        $keyword_arr = $this->ays_quiz_create_columns_array( $max_alpha_val );

        return $keyword_arr;

    }

    public function ays_quiz_create_columns_array($end_column, $first_letters = '') {
        $columns = array();
        $letters = range('A', 'Z');
        $length = strlen($end_column);

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter;

            // Add the column to the final array.
            $columns[] = $column;

            // If it was the end column that was added, return the columns.
            if ($column == $end_column)
                return $columns;
        }

        // Add the column children.
        foreach ($columns as $column) {
            // Don't itterate if the $end_column was already set in a previous itteration.
            // Stop iterating if you've reached the maximum character length.
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
              $new_columns = $this->ays_quiz_create_columns_array($end_column, $column);
              // Merge the new columns which were created with the final columns array.
              $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }

    public function ays_generate_coupons(){

        if ($_FILES['coupon_data']['size'] == 0 && $_FILES['coupon_data']['error'] == 0){
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Please choose the file", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }

        if(isset($_REQUEST['action']) && $_REQUEST['action'] != ''){

            $name_arr = explode('.', $_FILES['coupon_data']['name']);
            $type     = end($name_arr);

            if($type == 'csv'){

                require_once(AYS_QUIZ_DIR . 'includes/PHPExcel/vendor/autoload.php');
                $spreadsheet = IOFactory::load($_FILES['coupon_data']['tmp_name']);
                $coupon_sheet_names = $spreadsheet->getSheetNames();
                $quiz_coupons_data = array();
                $quiz_coupons = array();

                foreach ($coupon_sheet_names as $coupon_sheet_names_key => $coupon_sheet_name) {

                    $current_sheet = $spreadsheet->getSheet($coupon_sheet_names_key);
                    $highest_row = $current_sheet->getHighestRow();
                    $highest_column = $current_sheet->getHighestColumn();

                    $quiz_coupons[$coupon_sheet_names_key] = $coupon_sheet_name;

                    for ($row = 1; $row <= $highest_row; $row++){

                        //  Read a row of data into an array
                        $ready_array = $current_sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, "", false, true );

                        //  Insert row data array into your database of choice here
                        $ready_array = array_values( $ready_array );
                        $quiz_coupons_data[$coupon_sheet_names_key][] = $ready_array[0];

                    }
                }

                $ready_data = array();
                foreach ($quiz_coupons_data as $quiz_coupon_data_key => $quiz_coupon_data) {

                    foreach($quiz_coupon_data as $coupon_data_key => $quiz_coupon_data_value){

                        $coupons_array = array();

                        foreach($quiz_coupon_data_value as $s_key => $s_value){

                            if($s_value != ''){
                                $coupons_array[] = $s_value;
                            }
                        }

                        $ready_data[] = $coupons_array;
                    }
                }

                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'coupons_ready_data' => $ready_data,
                ));
                wp_die();
            }else{
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                $response_text = __( "File type should be a 'CSV'", $this->plugin_name );
                echo json_encode(array(
                    'status' => false,
                    'message' => $response_text,
                ));
                wp_die();
            }
        }else{
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Something went wrong", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }
    }

    public function get_admin_notes(){
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_admin_notes'){
            global $wpdb;

            $result_id = ( isset($_REQUEST['result_id']) && $_REQUEST['result_id'] != '' ) ? intval($_REQUEST['result_id']) : null;
            $note_text = ( isset($_REQUEST['note_text']) && $_REQUEST['note_text'] != '' ) ? sanitize_text_field(stripslashes($_REQUEST['note_text'])) : '';

            if($result_id === null){
                return;
            }

            $report_table = $wpdb->prefix .'aysquiz_reports';

            $sql     = "SELECT options FROM {$report_table} WHERE `id` =".$result_id;
            $result  = $wpdb->get_var($sql);
            $options = json_decode( $result, true );

            $options['note_text'] = $note_text;

            $quiz_result = $wpdb->update(
                $report_table,
                array(
                    'options' => json_encode( $options ),
                ),
                array( 'id' => $result_id ),
                array( '%s'),
                array( '%d' )
            );

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'note_text' => $options['note_text'],
                'result_id' => $result_id,
            ));
            wp_die();
        }else{
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Something went wrong", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }
    }

    public function ays_quiz_update_database_tables () {
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_quiz_update_database_tables'){
            $ays_quiz_db_version = '1.0.0';
            update_site_option( 'ays_quiz_db_version', $ays_quiz_db_version );
            
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
            ));
            wp_die();
        }else{
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Something went wrong", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }
    }

    public function add_tabs() {
        $screen = get_current_screen();
    
        if ( ! $screen) {
            return;
        }

        /*
        ==========================================
            General information Tab | Start
        ==========================================
        */
        
        $general_tab_title   = __( 'General Information', $this->plugin_name);

        $content = array();

        $content[] = '<div class="ays-quiz-help-tab-conatiner">';

            $content[] = '<div class="ays-quiz-help-tab-title">';
                $content[] = __( "Quiz Maker Information", AYS_QUIZ_NAME );
            $content[] = '</div>';


            $content[] = '<div class="ays-quiz-help-tab-row">';

                $content[] = '<div class="ays-quiz-help-tab-box">';
                    $content[] = '<span>';

                        $content[] = sprintf(
                            __( 'Create engaging quizzes, tests, and exams within a few minutes with the help of the WordPress Quiz Maker plugin. The Quiz Maker has a user-friendly interface and responsive design.%s With this plugin, you are free to add as many questions as needed with the following question types: %sRadio, Checkbox, Dropdown, Text, Short Text, Number, Date.%s In order, to activate Integrations, send Certificates via Email, or create Intervals for your quiz results you will need to download and install the Pro Versions of the WordPress Quiz plugin.', AYS_QUIZ_NAME ),
                            '<br>',
                            '<em>',
                            '</em><br><br>'
                        );

                    $content[] = '</span>';
                $content[] = '</div>';

            $content[] = '</div>';
        $content[] = '</div>';

        $content_genereal_info = implode( '', $content );

        /*
        ==========================================
            General information Tab | End
        ==========================================
        */

        /*
        ==========================================
            Premium information Tab | Start
        ==========================================
        */

        $premium_tab_title   = __( 'Premium version', $this->plugin_name);

        $content = array();

        $content[] = '<div class="ays-quiz-help-tab-conatiner">';

            $content[] = '<div class="ays-quiz-help-tab-title">';
                $content[] = __( "Premium versions' overview", AYS_QUIZ_NAME );
            $content[] = '</div>';

                $content[] = '<div class="ays-quiz-dicount-wrap-box">';
                    $content[] = '<span>';

                        $content[] = sprintf(
                            __( 'By activating the pro version, you will get all the features to strive your WordPress website’s quizzes to an advanced level.%sWith the WordPress Quiz plugin, it is easy to generate the quiz types like %sTrivia quiz, Assessment quiz, Personality test,  Multiple-choice quiz, Knowledge quiz, IQ test, Yes-or-no quiz, True-or-false quiz, This-or-that quiz(with images), Diagnostic quiz, Scored quiz, Buzzfeed quiz, Viral Quiz%s and etc.%sMotivate your visitors with Certificates and Advanced Leaderboards, prevent cheating during online exams with Timer-Based quizzes, earn money with Paid Quizzes.', $this->plugin_name ),
                            '<br>',
                            '<em>',
                            '</em>',
                            '</br></br>'
                        );

                    $content[] = '</span>';
            $content[] = '</div>';

        $content[] = '</div>';

        $content_premium_info = implode( '', $content );

        /*
        ==========================================
            Premium information Tab | End
        ==========================================
        */

        /*
        ==========================================
            Sidebar information | Start
        ==========================================
        */

        $sidebar_content = '
        <p><strong>' . __( 'For more information:', AYS_QUIZ_NAME ) . '</strong></p>' .
        '<p><a href="https://www.youtube.com/watch?v=oKPOdbZahK0" target="_blank">' . __( 'Youtube video tutorials' , AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank">' . __( 'Documentation', AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://ays-pro.com/wordpress/quiz-maker" target="_blank">' . __( 'Quiz Maker plugin premium version', AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://quiz-plugin.com/wordpress-quiz-plugin-free-demo" target="_blank">' . __( 'Quiz Maker plugin free demo', AYS_QUIZ_NAME ) . '</a></p>';

        /*
        ==========================================
            Sidebar information | End
        ==========================================
        */


        $general_tab_content = array(
            'id'      => 'quiz-maker-general-tab',
            'title'   => $general_tab_title,
            'content' => $content_genereal_info
        );

        $premium_tab_content = array(
            'id'      => 'quiz-maker-premium-tab',
            'title'   => $premium_tab_title,
            'content' => $content_premium_info
        );
        
        $screen->add_help_tab($general_tab_content);
        $screen->add_help_tab($premium_tab_content);

        $screen->set_help_sidebar($sidebar_content);
    }

    public function get_next_or_prev_row_by_id( $id, $type = "next", $table = "aysquiz_questions" ) {
        global $wpdb;

        if ( is_null( $table ) || empty( $table ) ) {
            return null;
        }

        $ays_table = esc_sql( $wpdb->prefix . $table );

        $where = array();
        $where_condition = "";

        $id     = (isset( $id ) && $id != "" && absint($id) != 0) ? absint( sanitize_text_field( $id ) ) : null;
        $type   = (isset( $type ) && $type != "") ? sanitize_text_field( $type ) : "next";

        if ( is_null( $id ) || $id == 0 ) {
            return null;
        }

        switch ( $type ) {
            case 'prev':
                $where[] = ' `id` < ' . $id . ' ORDER BY `id` DESC ';
                break;
            case 'next':
            default:
                $where[] = ' `id` > ' . $id;
                break;
        }

        if( ! empty($where) ){
            $where_condition = " WHERE " . implode( " AND ", $where );
        }

        $sql = "SELECT `id` FROM {$ays_table} ". $where_condition ." LIMIT 1;";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;
    }

    public function get_published_questions_ajax(){
        global $wpdb;

        $questions_table        = $wpdb->prefix."aysquiz_questions";
        $quiz_categories_table  = $wpdb->prefix."aysquiz_categories";
        $question_tags_table    = $wpdb->prefix."aysquiz_question_tags";

        $quiz_id        = isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != '' ? intval(sanitize_text_field($_REQUEST['quiz_id'])) : 0;
        $start          = isset($_REQUEST['start']) && $_REQUEST['start'] != '' ? intval(sanitize_text_field($_REQUEST['start'])) : 0;
        $length         = isset($_REQUEST['length']) && $_REQUEST['length'] != '' ? intval(sanitize_text_field($_REQUEST['length'])) : 5;
        $search         = isset($_REQUEST['search']) && !empty($_REQUEST['search']) ? array_map( "sanitize_text_field", $_REQUEST['search']) : array();
        $cats           = isset($_REQUEST['cats']) && !empty($_REQUEST['cats']) ? array_map( "sanitize_text_field", $_REQUEST['cats']) : array();
        $tags           = isset($_REQUEST['tags']) && !empty($_REQUEST['tags']) ? array_map( "sanitize_text_field", $_REQUEST['tags']) : array();
        $search_value   = isset($search['value']) && $search['value'] != '' ? esc_sql($search['value']) : '';

        $order          = isset($_REQUEST['order']) && !empty($_REQUEST['order']) ? $_REQUEST['order'] : array();
        $order_col      = isset($order[0]['column']) && $order[0]['column'] != '' ? intval($order[0]['column']) : 0;
        $order_dir      = isset($order[0]['dir']) && $order[0]['dir'] != '' ? esc_sql($order[0]['dir']) : ' DESC ';
        $order_columns  = array(
            0 => ' q.id ',
            1 => ' q.question ',
            2 => ' q.type ',
            3 => ' q.create_date ',
            4 => ' c.title ',
            5 => ' q.id ',
            6 => ' q.id ',
            7 => ' q.id ',
        );
        $order_column = $order_columns[$order_col];
        $order_dir = strtoupper($order_dir);

        $where = array();
        if($search_value != ''){
            $where[] = " q.id LIKE '%".$search_value."%' ";
            $where[] = " q.question LIKE '%".$search_value."%' ";
            $where[] = " q.type LIKE '%".$search_value."%' ";
            $where[] = " q.create_date LIKE '%".$search_value."%' ";
            $where[] = " c.title LIKE '%".$search_value."%' ";
        }

        $where_sql = "";

        if(!empty($cats)){
            $where_sql .= " AND c.id IN (" . implode(",", $cats) . ") ";
        }

        if(!empty($tags)){
            $where_sql .= " AND qt.id IN (" . implode(",", $tags) . ") ";
        }

        if(!empty($where)){
            $where_sql .= " AND ( " . implode(" OR ", $where) . ") ";
        }
        $limit = " LIMIT " . $start . ", " . $length;
        if(intval($length) < 0){
            $limit = '';
        }

        $sql = "SELECT DISTINCT q.id, q.question, q.type, q.create_date, q.options, c.title, 
                    ( SELECT GROUP_CONCAT( `title` SEPARATOR ', ' )
                      FROM `{$question_tags_table}`
                      WHERE FIND_IN_SET( `id`, q.`tag_id` ) AND `status` = 'published'
                    ) AS tag_title
                FROM {$questions_table} AS q
                LEFT JOIN {$quiz_categories_table} AS c
                ON q.category_id=c.id

                LEFT JOIN {$question_tags_table} AS qt
                ON (find_in_set(qt.id,q.tag_id)>0)

                WHERE q.published = 1 ".$where_sql."
                ORDER BY ".$order_column." ".$order_dir."
                ". $limit ."";

        $question_id_array = array();
        if($quiz_id != 0){
            $quiz = $this->ays_get_quiz_by_id($quiz_id);
            $question_id_array = isset($quiz['question_ids']) && $quiz['question_ids'] != '' ? explode(',', $quiz['question_ids']) : array();
        }

        $questions = $wpdb->get_results( $sql, 'ARRAY_A' );

        $results = array();
        $json = array();

        $sql = "SELECT COUNT(*)
                FROM {$questions_table} AS q
                LEFT JOIN {$quiz_categories_table} AS c
                ON q.category_id=c.id
                LEFT JOIN {$question_tags_table} AS qt
                ON (find_in_set(qt.id,q.tag_id)>0)
                WHERE q.published = 1 
                ORDER BY q.id DESC";
        $total_count = $wpdb->get_var($sql);

        $sql = "SELECT COUNT(*)
                FROM {$questions_table} AS q
                LEFT JOIN {$quiz_categories_table} AS c
                ON q.category_id=c.id
                LEFT JOIN {$question_tags_table} AS qt
                ON (find_in_set(qt.id,q.tag_id)>0)
                WHERE q.published = 1 ".$where_sql."
                ORDER BY q.id DESC";
        $filtered_count = $wpdb->get_var($sql);

        $json["recordsTotal"]       = intval($total_count);
        $json["recordsFiltered"]    = intval($filtered_count);
        $json['loader']             = AYS_QUIZ_ADMIN_URL . "/images/loaders/tail-spin.svg";
        $json['loaderText']         = __( "Processing...", $this->plugin_name );

        $used_questions = $this->get_published_questions_used();

        $quiz_tag_arr = array();
        foreach ($questions as $index => $question) {

            if ( isset( $question['options'] ) && $question['options'] != "" ) {
                $question_options = json_decode($question['options'], true);
            } else {
                $question_options = array();

            }
            $date = isset($question['create_date']) && $question['create_date'] != '' ? $question['create_date'] : "0000-00-00 00:00:00";
            if(isset($question_options['author'])){
                if(is_array($question_options['author'])){
                    $author = $question_options['author'];
                }else{
                    if ( isset( $question_options['author'] ) && $question_options['author'] != "" ) {
                        $author = json_decode($question_options['author'], true);
                    } else {
                        $author = array("name"=>"Unknown");
                    }
                }
            }else{
                $author = array("name"=>"Unknown");
            }
            $text = "";
            if(self::validateDate($date)){
                $text .= "<p style='margin:0;text-align:left;'><b>Date:</b> ".$date."</p>";
            }
            if($author['name'] !== "Unknown"){
                $text .= "<p style='margin:0;text-align:left;'><b>Author:</b> ".$author['name']."</p>";
            }
            $first_column = '<span>';
            if (in_array($question["id"], $question_id_array)){
                $first_column .= '<i class="ays-select-single ays_fa ays_fa_check_square_o"></i>';
            }else{
                $first_column .= '<i class="ays-select-single ays_fa ays_fa_square_o"></i>';
            }
            $first_column .= '</span>';

            $selected_question  = (in_array($question["id"], $question_id_array)) ? "selected" : "";
            $table_question     = (strip_tags(stripslashes($question['question'])));
            $table_question     = $this->ays_restriction_string("word", $table_question, 8);

            $used = __( "False", $this->plugin_name );

            if( in_array($question["id"], $used_questions) ){
                $used = __( "True", $this->plugin_name );
            }

            $tag_title = (isset( $question["tag_title"] ) && $question["tag_title"] != "") ? sanitize_text_field( $question["tag_title"] ) : "-";

            // switch ( $data['type'] ) {
            //     case 'short_text':
            //         $ays_question_type = 'short text';
            //         break;
            //     case 'true_or_false':
            //         $ays_question_type = 'true/false';
            //         break;
            //     case 'fill_in_blank':
            //         $ays_question_type = 'Fill in blank';
            //         break;
            //     default:
            //         $ays_question_type = $question['type'];
            //         break;
            // }

            $question_title_val = isset( $question["title"] ) && $question["title"] != "" ? stripslashes($question["title"]) : "";
            
            $results[] = array(
                'first_column'  => $first_column,
                'id'            => $question['id'],
                'type'          => $question['type'],
                'question'      => $table_question,
                'used'          => $used,
                'title'         => $question_title_val,
                'tag_data'      => $tag_title,
                'create_date'   => $text,
                'selected'      => $selected_question,
            );
        }
        $json["data"] = $results;
        echo json_encode($json);
        wp_die();
    }

    public function ays_quiz_author_user_search() {
        $search = isset($_REQUEST['search']) && trim($_REQUEST['search']) != '' ? sanitize_text_field( trim($_REQUEST['search']) ) : null;
        $checked = isset($_REQUEST['val']) && $_REQUEST['val'] !='' ? sanitize_text_field( $_REQUEST['val'] ) : null;

        $content_text = array(
            'results' => array()
        );

        $args = 'search=';
        if($search !== null){
            $args .= '*';
            $args .= $search;
            $args .= '*';
        } else {
            ob_end_clean();
            echo json_encode($content_text);
            wp_die();
        }

        $users = get_users($args);

        foreach ($users as $key => $value) {
            if ($checked !== null) {
                if ( !is_array( $checked ) ) {
                    $checked2 = $checked;
                    $checked = array();
                    $checked[] = absint($checked2);
                }
                if (in_array($value->ID, $checked)) {
                    continue;
                }else{
                    $content_text['results'][] = array(
                        'id' => $value->ID,
                        'text' => $value->data->display_name,
                    );
                }
            }else{
                $content_text['results'][] = array(
                    'id' => $value->ID,
                    'text' => $value->data->display_name,
                );
            }
        }

        ob_end_clean();
        echo json_encode($content_text);
        wp_die();
    }

    /**
     * Determine if the plugin/addon installations are allowed.
     *
     * @since 21.7.6
     *
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_quiz_can_install( $type ) {

        return self::ays_quiz_can_do( 'install', $type );
    }

    /**
     * Determine if the plugin/addon activations are allowed.
     *
     * @since 21.7.6
     *
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_quiz_can_activate( $type ) {

        return self::ays_quiz_can_do( 'activate', $type );
    }

    /**
     * Determine if the plugin/addon installations/activations are allowed.
     *
     * @since 21.7.6
     *
     * @param string $what Should be 'activate' or 'install'.
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_quiz_can_do( $what, $type ) {

        if ( ! in_array( $what, array( 'install', 'activate' ), true ) ) {
            return false;
        }

        if ( ! in_array( $type, array( 'plugin', 'addon' ), true ) ) {
            return false;
        }

        $capability = $what . '_plugins';

        if ( ! current_user_can( $capability ) ) {
            return false;
        }

        // Determine whether file modifications are allowed and it is activation permissions checking.
        if ( $what === 'install' && ! wp_is_file_mod_allowed( 'ays_quiz_can_install' ) ) {
            return false;
        }

        // All plugin checks are done.
        if ( $type === 'plugin' ) {
            return true;
        }
        return false;
    }

    /**
     * Activate plugin.
     *
     * @since 1.0.0
     * @since 21.7.6 Updated the permissions checking.
     */
    public function ays_quiz_activate_plugin() {

        // Run a security check.
        check_ajax_referer( $this->plugin_name . '-install-plugin-nonce', sanitize_key( $_REQUEST['_ajax_nonce'] ) );

        // Check for permissions.
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'quiz-maker' ) );
        }

        $type = 'addon';

        if ( isset( $_POST['plugin'] ) ) {

            if ( ! empty( $_POST['type'] ) ) {
                $type = sanitize_key( $_POST['type'] );
            }

            $plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
            $activate = activate_plugins( $plugin );

            if ( ! is_wp_error( $activate ) ) {
                if ( $type === 'plugin' ) {
                    wp_send_json_success( esc_html__( 'Plugin activated.', 'quiz-maker' ) );
                } else {
                        ( esc_html__( 'Addon activated.', 'quiz-maker' ) );
                }
            }
        }

        if ( $type === 'plugin' ) {
            wp_send_json_error( esc_html__( 'Could not activate the plugin. Please activate it on the Plugins page.', 'quiz-maker' ) );
        }

        wp_send_json_error( esc_html__( 'Could not activate the addon. Please activate it on the Plugins page.', 'quiz-maker' ) );
    }

    /**
     * Install addon.
     *
     * @since 1.0.0
     * @since 21.7.6 Updated the permissions checking.
     */
    public function ays_quiz_install_plugin() {

        // Run a security check.
        check_ajax_referer( $this->plugin_name . '-install-plugin-nonce', sanitize_key( $_REQUEST['_ajax_nonce'] ) );

        $generic_error = esc_html__( 'There was an error while performing your request.', 'quiz-maker' );
        $type          = ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';

        // Check if new installations are allowed.
        if ( ! self::ays_quiz_can_install( $type ) ) {
            wp_send_json_error( $generic_error );
        }

        $error = $type === 'plugin'
            ? esc_html__( 'Could not install the plugin. Please download and install it manually.', 'quiz-maker' )
            : "";

        $plugin_url = ! empty( $_POST['plugin'] ) ? esc_url_raw( wp_unslash( $_POST['plugin'] ) ) : '';

        if ( empty( $plugin_url ) ) {
            wp_send_json_error( $error );
        }

        // Prepare variables.
        $url = esc_url_raw(
            add_query_arg(
                [
                    'page' => 'quiz-maker-featured-plugins',
                ],
                admin_url( 'admin.php' )
            )
        );

        ob_start();
        $creds = request_filesystem_credentials( $url, '', false, false, null );

        // Hide the filesystem credentials form.
        ob_end_clean();

        // Check for file system permissions.
        if ( $creds === false ) {
            wp_send_json_error( $error );
        }
        
        if ( ! WP_Filesystem( $creds ) ) {
            wp_send_json_error( $error );
        }

        /*
         * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
         */
        require_once AYS_QUIZ_DIR . 'includes/admin/class-quiz-maker-upgrader.php';
        require_once AYS_QUIZ_DIR . 'includes/admin/class-quiz-maker-install-skin.php';
        require_once AYS_QUIZ_DIR . 'includes/admin/class-quiz-maker-skin.php';


        // Do not allow WordPress to search/download translations, as this will break JS output.
        remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

        // Create the plugin upgrader with our custom skin.
        $installer = new QuizMaker\Helpers\QuizMakerPluginSilentUpgrader( new Quiz_Maker_Install_Skin() );

        // Error check.
        if ( ! method_exists( $installer, 'install' ) ) {
            wp_send_json_error( $error );
        }

        $installer->install( $plugin_url );

        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();

        $plugin_basename = $installer->plugin_info();

        if ( empty( $plugin_basename ) ) {
            wp_send_json_error( $error );
        }

        $result = array(
            'msg'          => $generic_error,
            'is_activated' => false,
            'basename'     => $plugin_basename,
        );

        // Check for permissions.
        if ( ! current_user_can( 'activate_plugins' ) ) {
            $result['msg'] = $type === 'plugin' ? esc_html__( 'Plugin installed.', 'quiz-maker' ) : "";

            wp_send_json_success( $result );
        }

        // Activate the plugin silently.
        $activated = activate_plugin( $plugin_basename );
        remove_action( 'activated_plugin', array( 'ays_sccp_activation_redirect_method', 'gallery_p_gallery_activation_redirect_method', 'poll_maker_activation_redirect_method' ), 100 );

        if ( ! is_wp_error( $activated ) ) {

            $result['is_activated'] = true;
            $result['msg']          = $type === 'plugin' ? esc_html__( 'Plugin installed and activated.', 'quiz-maker' ) : esc_html__( 'Addon installed and activated.', 'quiz-maker' );

            wp_send_json_success( $result );
        }

        // Fallback error just in case.
        wp_send_json_error( $result );
    }

    /**
     * List of AM plugins that we propose to install.
     *
     * @since 21.7.6
     *
     * @return array
     */
    protected function get_am_plugins() {
        if ( !isset( $_SESSION ) ) {
            session_start();
        }

        $images_url = AYS_QUIZ_ADMIN_URL . '/images/icons/';

        $plugin_slug = array(
            'poll-maker',
            'survey-maker',
            'ays-popup-box',
            'gallery-photo-gallery',
            'gallery-photo-gallery',
            'secure-copy-content-protection',
            'personal-dictionary',
        );

        $plugin_url_arr = array();
        foreach ($plugin_slug as $key => $slug) {
            if ( isset( $_SESSION['ays_quiz_our_product_links'] ) && !empty( $_SESSION['ays_quiz_our_product_links'] ) 
                && isset( $_SESSION['ays_quiz_our_product_links'][$slug] ) && !empty( $_SESSION['ays_quiz_our_product_links'][$slug] ) ) {
                $plugin_url = (isset( $_SESSION['ays_quiz_our_product_links'][$slug] ) && $_SESSION['ays_quiz_our_product_links'][$slug] != "") ? esc_url( $_SESSION['ays_quiz_our_product_links'][$slug] ) : "";
            } else {
                $latest_version = $this->ays_quiz_get_latest_plugin_version($slug);
                $plugin_url = 'https://downloads.wordpress.org/plugin/'. $slug .'.zip';
                if ( $latest_version != '' ) {
                    $plugin_url = 'https://downloads.wordpress.org/plugin/'. $slug .'.'. $latest_version .'.zip';
                    $_SESSION['ays_quiz_our_product_links'][$slug] = $plugin_url;
                }
            }

            $plugin_url_arr[$slug] = $plugin_url;
        }

        $plugins_array = array(
           'poll-maker/poll-maker-ays.php'        => array(
                'icon'        => $images_url . 'icon-poll-128x128.png',
                'name'        => __( 'Poll Maker', 'quiz-maker' ),
                'desc'        => __( 'Create amazing online polls for your WordPress website super easily.', 'quiz-maker' ),
                'desc_hidden' => __( 'Build up various types of polls in a minute and get instant feedback on any topic or product.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/poll-maker/',
                'buy_now'     => 'https://ays-pro.com/wordpress/poll-maker/',
                'url'         => $plugin_url_arr['poll-maker'],
            ),
            'survey-maker/survey-maker.php'        => array(
                'icon'        => $images_url . 'icon-survey-128x128.png',
                'name'        => __( 'Survey Maker', 'quiz-maker' ),
                'desc'        => __( 'Make amazing online surveys and get real-time feedback quickly and easily.', 'quiz-maker' ),
                'desc_hidden' => __( 'Learn what your website visitors want, need, and expect with the help of Survey Maker. Build surveys without limiting your needs.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/survey-maker/',
                'buy_now'     => 'https://ays-pro.com/wordpress/survey-maker?utm_source=dashboard-our-products&utm_medium=pro&utm_campaign=quiz ',
                'url'         => $plugin_url_arr['survey-maker'],
            ),
            'ays-popup-box/ays-pb.php'        => array(
                'icon'        => $images_url . 'icon-popup-128x128.png',
                'name'        => __( 'Popup Box', 'quiz-maker' ),
                'desc'        => __( 'Popup everything you want! Create informative and promotional popups all in one plugin.', 'quiz-maker' ),
                'desc_hidden' => __( 'Attract your visitors and convert them into email subscribers and paying customers.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/ays-popup-box/',
                'buy_now'     => 'https://ays-pro.com/wordpress/popup-box/',
                'url'         => $plugin_url_arr['ays-popup-box'],
            ),
            'gallery-photo-gallery/gallery-photo-gallery.php'        => array(
                'icon'        => $images_url . 'icon-gallery-128x128.png',
                'name'        => __( 'Gallery Photo Gallery', 'quiz-maker' ),
                'desc'        => __( 'Create unlimited galleries and include unlimited images in those galleries.', 'quiz-maker' ),
                'desc_hidden' => __( 'Represent images in an attractive way. Attract people with your own single and multiple free galleries from your photo library.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/gallery-photo-gallery/',
                'buy_now'     => 'https://ays-pro.com/wordpress/photo-gallery/',
                'url'         => $plugin_url_arr['gallery-photo-gallery'],
            ),
            'secure-copy-content-protection/secure-copy-content-protection.php'        => array(
                'icon'        => $images_url . 'icon-sccp-128x128.png',
                'name'        => __( 'Secure Copy Content Protection', 'quiz-maker' ),
                'desc'        => __( 'Disable the right click, copy paste, content selection and copy shortcut keys on your website.', 'quiz-maker' ),
                'desc_hidden' => __( 'Protect web content from being plagiarized. Prevent plagiarism from your website with this easy to use plugin.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/secure-copy-content-protection/',
                'buy_now'     => 'https://ays-pro.com/wordpress/secure-copy-content-protection/',
                'url'         => $plugin_url_arr['secure-copy-content-protection'],
            ),
            'personal-dictionary/personal-dictionary.php'        => array(
                'icon'        => $images_url . 'pd-logo-128x128.png',
                'name'        => __( 'Personal Dictionary', 'quiz-maker' ),
                'desc'        => __( 'Allow your students to create personal dictionary, study and memorize the words.', 'quiz-maker' ),
                'desc_hidden' => __( 'Allow your users to create their own digital dictionaries and learn new words and terms as fastest as possible.', 'quiz-maker' ),
                'wporg'       => 'https://wordpress.org/plugins/personal-dictionary/',
                'buy_now'     => 'https://ays-pro.com/wordpress/personal-dictionary/',
                'url'         => $plugin_url_arr['personal-dictionary'],
                // 'pro'   => array(
                //     'plug' => '',
                //     'icon' => '',
                //     'name' => '',
                //     'desc' => '',
                //     'url'  => '',
                //     'act'  => 'go-to-url',
                // ),
            ),
        );

        return $plugins_array;
    }

    protected function ays_quiz_get_latest_plugin_version( $slug ){

        if ( is_null( $slug ) || empty($slug) ) {
            return "";
        }

        $version_latest = "";

        if ( ! function_exists( 'plugins_api' ) ) {
              require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        }

        // set the arguments to get latest info from repository via API ##
        $args = array(
            'slug' => $slug,
            'fields' => array(
                'version' => true,
            )
        );

        /** Prepare our query */
        $call_api = plugins_api( 'plugin_information', $args );

        /** Check for Errors & Display the results */
        if ( is_wp_error( $call_api ) ) {
            $api_error = $call_api->get_error_message();
        } else {

            //echo $call_api; // everything ##
            if ( ! empty( $call_api->version ) ) {
                $version_latest = $call_api->version;
            }
        }

        return $version_latest;
    }

    /**
     * Get AM plugin data to display in the Addons section of About tab.
     *
     * @since 21.7.6
     *
     * @param string $plugin      Plugin slug.
     * @param array  $details     Plugin details.
     * @param array  $all_plugins List of all plugins.
     *
     * @return array
     */
    protected function get_plugin_data( $plugin, $details, $all_plugins ) {

        $have_pro = ( ! empty( $details['pro'] ) && ! empty( $details['pro']['plug'] ) );
        $show_pro = false;

        $plugin_data = array();

        if ( $have_pro ) {
            if ( array_key_exists( $plugin, $all_plugins ) ) {
                if ( is_plugin_active( $plugin ) ) {
                    $show_pro = true;
                }
            }
            if ( array_key_exists( $details['pro']['plug'], $all_plugins ) ) {
                $show_pro = true;
            }
            if ( $show_pro ) {
                $plugin  = $details['pro']['plug'];
                $details = $details['pro'];
            }
        }

        if ( array_key_exists( $plugin, $all_plugins ) ) {
            if ( is_plugin_active( $plugin ) ) {
                // Status text/status.
                $plugin_data['status_class'] = 'status-active';
                $plugin_data['status_text']  = esc_html__( 'Active', 'quiz-maker' );
                // Button text/status.
                $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-quiz-card__btn-info disabled';
                $plugin_data['action_text']  = esc_html__( 'Activated', 'quiz-maker' );
                $plugin_data['plugin_src']   = esc_attr( $plugin );
            } else {
                // Status text/status.
                $plugin_data['status_class'] = 'status-installed';
                $plugin_data['status_text']  = esc_html__( 'Inactive', 'quiz-maker' );
                // Button text/status.
                $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-quiz-card__btn-info';
                $plugin_data['action_text']  = esc_html__( 'Activate', 'quiz-maker' );
                $plugin_data['plugin_src']   = esc_attr( $plugin );
            }
        } else {
            // Doesn't exist, install.
            // Status text/status.
            $plugin_data['status_class'] = 'status-missing';

            if ( isset( $details['act'] ) && 'go-to-url' === $details['act'] ) {
                $plugin_data['status_class'] = 'status-go-to-url';
            }
            $plugin_data['status_text'] = esc_html__( 'Not Installed', 'quiz-maker' );
            // Button text/status.
            $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-quiz-card__btn-info';
            $plugin_data['action_text']  = esc_html__( 'Install Plugin', 'quiz-maker' );
            $plugin_data['plugin_src']   = esc_url( $details['url'] );
        }

        $plugin_data['details'] = $details;

        return $plugin_data;
    }

    /**
     * Display the Addons section of About tab.
     *
     * @since 21.7.6
     */
    public function output_about_addons() {

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins          = get_plugins();
        $am_plugins           = $this->get_am_plugins();
        $can_install_plugins  = self::ays_quiz_can_install( 'plugin' );
        $can_activate_plugins = self::ays_quiz_can_activate( 'plugin' );

        $content = '';
        $content.= '<div class="ays-quiz-cards-block">';
        foreach ( $am_plugins as $plugin => $details ){

            $plugin_data              = $this->get_plugin_data( $plugin, $details, $all_plugins );
            $plugin_ready_to_activate = $can_activate_plugins
                && isset( $plugin_data['status_class'] )
                && $plugin_data['status_class'] === 'status-installed';
            $plugin_not_activated     = ! isset( $plugin_data['status_class'] )
                || $plugin_data['status_class'] !== 'status-active';

            $plugin_action_class = ( isset( $plugin_data['action_class'] ) && esc_attr( $plugin_data['action_class'] ) != "" ) ? esc_attr( $plugin_data['action_class'] ) : "";

            $plugin_action_class_disbaled = "";
            if ( strpos($plugin_action_class, 'status-active') !== false ) {
                $plugin_action_class_disbaled = "disbaled='true'";
            }

            $content .= '
                <div class="ays-quiz-card">
                    <div class="ays-quiz-card__content flexible">
                        <div class="ays-quiz-card__content-img-box">
                            <img class="ays-quiz-card__img" src="'. esc_url( $plugin_data['details']['icon'] ) .'" alt="'. esc_attr( $plugin_data['details']['name'] ) .'">
                        </div>
                        <div class="ays-quiz-card__text-block">
                            <h5 class="ays-quiz-card__title">'. esc_html( $plugin_data['details']['name'] ) .'</h5>
                            <p class="ays-quiz-card__text">'. wp_kses_post( $plugin_data['details']['desc'] ) .'
                                <span class="ays-quiz-card__text-hidden">
                                    '. wp_kses_post( $plugin_data['details']['desc_hidden'] ) .'
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="ays-quiz-card__footer">';
                        if ( $can_install_plugins || $plugin_ready_to_activate || ! $details['wporg'] ) {
                            $content .= '<button class="'. esc_attr( $plugin_data['action_class'] ) .'" data-plugin="'. esc_attr( $plugin_data['plugin_src'] ) .'" data-type="plugin" '. $plugin_action_class_disbaled .'>
                                '. wp_kses_post( $plugin_data['action_text'] ) .'
                            </button>';
                        }
                        elseif ( $plugin_not_activated ) {
                            $content .= '<a href="'. esc_url( $details['wporg'] ) .'" target="_blank" rel="noopener noreferrer">
                                '. esc_html_e( 'WordPress.org', 'quiz-maker' ) .'
                                <span aria-hidden="true" class="dashicons dashicons-external"></span>
                            </a>';
                        }
            $content .='
                        <a target="_blank" href="'. esc_url( $plugin_data['details']['buy_now'] ) .'" class="ays-quiz-card__btn-primary">'. __('Buy Now', $this->plugin_name) .'</a>
                    </div>
                </div>';
        }
        $install_plugin_nonce = wp_create_nonce( $this->plugin_name . '-install-plugin-nonce' );
        $content.= '<input type="hidden" id="ays_quiz_ajax_install_plugin_nonce" name="ays_quiz_ajax_install_plugin_nonce" value="'. $install_plugin_nonce .'">';
        $content.= '</div>';

        echo $content;
    }
    
    // ==================================================================
    // =====================  Questions  loading  =======================
    // ========================    START   ==============================

    public function get_quiz_question_html(){

        if ( (isset($_REQUEST["action"]) && $_REQUEST["action"] == "get_quiz_question_html") ) {
            $question_ids = (isset($_REQUEST["ays_questions_ids"]) && !empty($_REQUEST["ays_questions_ids"])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_questions_ids'] ) : array();

            if ( !empty( $question_ids ) ) {
                $question_ids = array_unique($question_ids);
                $question_ids = array_values($question_ids);
            }
            
            $rows = array();
            $ids = array();
            if (!empty($question_ids)) {
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

                foreach ($question_ids as $question_id) {
                    $data = Quiz_Maker_Data::get_published_questions_by('id', absint(intval($question_id)));

                    switch ( $data['type'] ) {
                        case 'short_text':
                            $ays_question_type = 'short text';
                            break;
                        case 'true_or_false':
                            $ays_question_type = 'true/false';
                            break;
                        case 'fill_in_blank':
                            $ays_question_type = 'Fill in blank';
                            break;
                        default:
                            $ays_question_type = $data['type'];
                            break;
                    }

                    if($data['type'] == 'custom'){
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = htmlspecialchars_decode( $data['question_title'], ENT_COMPAT);
                            $table_question = stripslashes( $table_question );
                        }else{
                            $table_question = __( 'Custom question', $this->plugin_name ) . ' #' . $data['id'];
                        }
                    }else{
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = esc_attr( $data['question_title'] );
                        }elseif (isset($data['question']) && strlen($data['question']) != 0){
                            $table_question = strip_tags(stripslashes($data['question']));
                        }elseif (isset($data['question_image']) && $data['question_image'] !=''){
                            $table_question = __( 'Image question', $this->plugin_name );
                        }
                        $table_question = $this->ays_restriction_string("word", $table_question, 8);
                    }
                    $edit_question_url = "?page=".$this->plugin_name."-questions&action=edit&question=".$data['id'];

                    $tag_ids = array();
                    if( isset( $data['tag_id'] ) && !empty( $data['tag_id'] ) ){
                        $tag_ids = explode(',',$data['tag_id']);
                    }

                    $question_tags_title = '';

                    foreach ($tag_ids as $tag_id) {
                        if( !isset( $question_tags_array[$tag_id] ) && empty($question_tags_array[$tag_id]) ){
                            continue;
                        }
                        $question_tags_title .= $question_tags_array[$tag_id].",";
                    }

                    $question_category_name = isset( $question_categories_array[$data['category_id']] ) && $question_categories_array[$data['category_id']] != "" ? $question_categories_array[$data['category_id']] : "";

                    $rows[] = '<tr class="ays-question-row ui-state-default ays-question-selected" data-id="' . $data['id'] . '">
                                    <td class="ays-sort ays-quiz-question-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-question-row">
                                        <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                            ' . $table_question . '
                                        </a>
                                    </td>
                                    <td class="ays-quiz-question-type-row">' . $ays_question_type . '</td>
                                    <td class="ays-quiz-question-category-row">' . $question_category_name . '</td>
                                    <td class="ays-quiz-question-tag-row">' . rtrim($question_tags_title, ',') . '</td>
                                    <td class="ays-quiz-question-id-row">' . stripslashes($data['id']) . '</td>
                                    <td class="ays-quiz-question-action-row">
                                        <div class="ays-question-row-actions">
                                            <input type="checkbox" class="ays_del_tr">
                                            <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                                <i class="ays_fa ays_fa_pencil_square" aria-hidden="true"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="ays-delete-question" title="'. __('Delete', $this->plugin_name) .'"
                                               data-id="' . $data['id'] . '">
                                                <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </td>
                               </tr>';
                    $ids[] = $data['id'];
                }

                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => $rows,
                    'ids' => $ids
                ));
                wp_die();
            } else {
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => '',
                    'ids' => array()
                ));
                wp_die();
            }
        } else {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'rows' => '',
                'ids' => array()
            ));
            wp_die();
        }
    }

    // ==================================================================
    // =====================  Questions  loading  =======================
    // ========================     End    ==============================

    public function ays_generate_passwords_via_import(){

        if ($_FILES['password_data']['size'] == 0 && $_FILES['password_data']['error'] == 0){
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Please choose the file", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }

        if(isset($_REQUEST['action']) && $_REQUEST['action'] != ''){

            $name_arr = explode('.', $_FILES['password_data']['name']);
            $type     = end($name_arr);

            if($type == 'csv'){

                require_once(AYS_QUIZ_DIR . 'includes/PHPExcel/vendor/autoload.php');
                $spreadsheet = IOFactory::load($_FILES['password_data']['tmp_name']);
                $coupon_sheet_names = $spreadsheet->getSheetNames();
                $quiz_passwords_data = array();
                $quiz_passwords = array();

                foreach ($coupon_sheet_names as $coupon_sheet_names_key => $coupon_sheet_name) {

                    $current_sheet = $spreadsheet->getSheet($coupon_sheet_names_key);
                    $highest_row = $current_sheet->getHighestRow();
                    $highest_column = $current_sheet->getHighestColumn();

                    $quiz_passwords[$coupon_sheet_names_key] = $coupon_sheet_name;

                    for ($row = 1; $row <= $highest_row; $row++){

                        //  Read a row of data into an array
                        $ready_array = $current_sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, "", false, true );

                        //  Insert row data array into your database of choice here
                        $ready_array = array_values( $ready_array );
                        $quiz_passwords_data[$coupon_sheet_names_key][] = $ready_array[0];

                    }
                }

                $ready_data = array();
                foreach ($quiz_passwords_data as $quiz_password_data_key => $quiz_password_data) {

                    foreach($quiz_password_data as $password_data_key => $quiz_password_data_value){

                        if ($password_data_key == 0) {
                            continue;
                        }

                        foreach($quiz_password_data_value as $s_key => $s_value){

                            if($s_value != ''){
                                $ready_data[] = $s_value;
                            }
                        }
                    }
                }

                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'passwords_ready_data' => $ready_data,
                ));
                wp_die();
            }
            elseif ($type == 'txt') {

                $fh = fopen($_FILES['password_data']['tmp_name'],'r');
                $ready_data = array();
                if ( $fh ) {
                    while ($line = fgets($fh)) {
                        if (trim($line) != "") {
                            $ready_data[] = trim($line);
                        }
                    }
                }

                fclose($fh);                
                
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'passwords_ready_data' => $ready_data,
                ));
                wp_die();
            }
            else{
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                $response_text = __( "File type should be a 'CSV' or 'TXT'", $this->plugin_name );
                echo json_encode(array(
                    'status' => false,
                    'message' => $response_text,
                ));
                wp_die();
            }
        }else{
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            $response_text = __( "Something went wrong", $this->plugin_name );
            echo json_encode(array(
                'status' => false,
                'message' => $response_text,
            ));
            wp_die();
        }
    }
    
}
