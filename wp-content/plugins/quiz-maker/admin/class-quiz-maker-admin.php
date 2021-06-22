<?php

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
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );
        
        wp_enqueue_style($this->plugin_name . '-animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-animations.css', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-font-awesome', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-qm-select2', AYS_QUIZ_PUBLIC_URL .  '/css/quiz-maker-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-ays-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-jquery-datetimepicker', plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-admin.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . "-loaders", plugin_dir_url(__FILE__) . 'css/loaders.css', array(), time()/*$this->version*/, 'all');

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
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_media();
        // wp_enqueue_script('wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array('wp-color-picker'), $this->version, true);

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
            wp_enqueue_script($this->plugin_name . '-public', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public1', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_dark.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public2', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_light.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public3', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_dark.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-public4', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_light.js', array('jquery'), $this->version, true);
        }

        wp_enqueue_script( $this->plugin_name."-functions", plugin_dir_url(__FILE__) . 'js/partials/functions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-quiz-styles", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-styles.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-information-form", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-information-form.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-quick-start", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quick-start.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-tabs", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-tabs.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-questions", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-questions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
		wp_enqueue_script( $this->plugin_name."-cookie.js", plugin_dir_url( __FILE__ ) . 'js/cookie.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( $this->plugin_name, 'quizLangObj', array(
            'notAnsweredText'       => __( 'You have not answered this question', $this->plugin_name ),
            'areYouSure'            => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
            'sorry'                 => __( 'Sorry', $this->plugin_name ),
            'unableStoreData'       => __( 'We are unable to store your data', $this->plugin_name ),
            'connectionLost'        => __( 'Connection is lost', $this->plugin_name ),
            'checkConnection'       => __( 'Please check your connection and try again', $this->plugin_name ),
            'selectPlaceholder'     => __( 'Select an answer', $this->plugin_name ),
            'shareDialog'           => __( 'Share Dialog', $this->plugin_name ),
            'emptyTitle'            => __( 'Sorry, you must fill out Title field.', $this->plugin_name ),

            'questionTitle'         => __( 'Question Default Title', $this->plugin_name ),
            'radio'                 => __( 'Radio', $this->plugin_name ),
            'checkbox'              => __( 'Checkbox', $this->plugin_name ),
            'dropdawn'              => __( 'Dropdawn', $this->plugin_name ),
            'emptyAnswer'           => __( 'Empty Answer', $this->plugin_name ),
            'addGif'                => __( 'Add Gif', $this->plugin_name),
            'addImage'              => __( 'Add image', $this->plugin_name),
            'add'                   => __( 'Add', $this->plugin_name),
            'textType'              => __( 'Text', $this->plugin_name),
            'answerText'            => __( 'Answer text', $this->plugin_name),
            'copied'                => __( 'Copied!', $this->plugin_name),
            'clickForCopy'          => __( 'Click for copy.', $this->plugin_name),

        ) );

        $question_categories = Quiz_Maker_Data::get_question_categories();
        wp_localize_script( $this->plugin_name, 'aysQuizCatObj', array(
            'category' => $question_categories,
        ) );
                
        /* 
        ========================================== 
          Quiz admin dashboard scripts for AJAX
        ========================================== 
        */
        wp_enqueue_script( $this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/quiz-maker-admin-ajax.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-ajax', 'quiz_maker_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
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
        global $wpdb;
        $unread_results_count = Results_List_Table::unread_records_count();
        $menu_item = ($unread_results_count == 0) ? 'Quiz Maker' : 'Quiz Maker' . '<span class="ays_menu_badge" id="ays_results_bage">' . $unread_results_count . '</span>';
        
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
    }

    public function add_plugin_results_submenu(){
        global $wpdb;
        $unread_results_count = Results_List_Table::unread_records_count();
        $results_text = __('Results', $this->plugin_name);
        $menu_item = ($unread_results_count == 0) ? $results_text : $results_text . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
        $hook_results = add_submenu_page(
            $this->plugin_name,
            $results_text,
            $menu_item,
            $this->capability,
            $this->plugin_name . '-results',
            array($this, 'display_plugin_results_page')
        );

        add_action("load-$hook_results", array($this, 'screen_option_results'));
        
        $hook_each_result = add_submenu_page(
            'each_result_slug',
            __('Each', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-each-result',
            array($this, 'display_plugin_each_results_page')
        );

        add_action("load-$hook_each_result", array($this, 'screen_option_each_quiz_results'));

        $hook_all_results = add_submenu_page(
            'all_results_slug',
            __('Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-all-results',
            array($this, 'display_plugin_all_results_page')
        );

        add_action("load-$hook_all_results", array($this, 'screen_option_all_quiz_results'));

        $hook_all_reviews = add_submenu_page(
            'all_reviews_slug',
            __('Reviews', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-all-reviews',
            array($this, 'display_plugin_all_reviews_page')
        );

        add_action("load-$hook_all_reviews", array($this, 'screen_option_all_quiz_reviews'));

        $hook_not_finished_result = add_submenu_page(
            'not_finished_result_slug',
            __('Not Finished Results', $this->plugin_name),
            null,
            $this->capability,
            $this->plugin_name . '-not-finished-results',
            array($this, 'display_plugin_not_finished_results_page')
        );

        add_action("load-$hook_not_finished_result", array($this, 'screen_option_not_finished_results'));

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
    }

    public function add_plugin_featured_plugins_submenu(){
        add_submenu_page( $this->plugin_name,
            __('Our products', $this->plugin_name),
            __('Our Products', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-our-products',
            array($this, 'display_plugin_featured_plugins_page') 
        );
    }

    public function add_plugin_addons_submenu(){
        add_submenu_page( $this->plugin_name,
            __('Addons', $this->plugin_name),
            __('Addons', $this->plugin_name),
            $this->capability,
            $this->plugin_name . '-addons',
            array($this, 'display_plugin_addons_page')
        );
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

    public function display_plugin_all_reviews_page(){
        include_once('partials/results/quiz-maker-all-reviews-display.php');
    }

    public function display_plugin_not_finished_results_page(){
        include_once 'partials/results/quiz-maker-not-finished-results-display.php';
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

        add_screen_option($option, $args);
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

        add_screen_option($option, $args);
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

        add_screen_option($option, $args);

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

        add_screen_option($option, $args);
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

        add_screen_option($option, $args);
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

    /**
     * Adding questions from modal to table
     */
    public function add_question_rows(){
        error_reporting(0);
        $this->quizes_obj = new Quizes_List_Table($this->plugin_name);

        if ((isset($_REQUEST["add_question_rows"]) && wp_verify_nonce($_REQUEST["add_question_rows"], 'add_question_rows')) || (isset($_REQUEST["add_question_rows_top"]) && wp_verify_nonce($_REQUEST["add_question_rows_top"], 'add_question_rows_top'))) {
            $question_ids = (isset($_REQUEST["ays_questions_ids"]) && !empty($_REQUEST["ays_questions_ids"])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_questions_ids'] ) : array();
            $rows = array();
            $ids = array();
            if (!empty($question_ids)) {
                $question_categories = $this->get_questions_categories();
                $question_categories_array = array();
                foreach($question_categories as $cat){
                    $question_categories_array[$cat['id']] = $cat['title'];
                }

                foreach ($question_ids as $question_id) {
                    $data = $this->quizes_obj->get_published_questions_by('id', absint(intval($question_id)));
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
                    $rows[] = '<tr class="ays-question-row ui-state-default" data-id="' . $data['id'] . '">
                                    <td class="ays-sort"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td>
                                        <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                            ' . $table_question . '
                                        </a>
                                    </td>
                                    <td>' . $data['type'] . '</td>
                                    <td>' . $question_categories_array[$data['category_id']] . '</td>
                                    <td>' . stripslashes($data['id']) . '</td>
                                    <td>
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
    
	public function ays_questions_export() {
        error_reporting(0);
		global $wpdb;
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'csv';
        $results = array();
		$sql = "SELECT q.*, c.title AS category_title
                FROM {$wpdb->prefix}aysquiz_questions AS q
                LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                    ON q.category_id = c.id
                ORDER BY q.id";
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
        
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($results);
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
        );

        $results_array_csv = array();
        if(empty($questions)){
            $export_data = array(
                'status'     => false,
                'type'       => 'csv',
                'data'       => array(),
                'fields'     => $export_file_fields
            );
        }else{
            foreach ($questions as $key => $question){
                $question = (array)$question;

                $category_id = (isset($question['category_title']) && $question['category_title'] != '') ? $question['category_title'] : 'Uncategorized';
                $question_content = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['question']))))."\"";
                $question_title = (isset($question['question_title']) && $question['question_title'] != '') ? $question['question_title'] : '';
                $question_image = (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '';
                $question_hint = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['question_hint']))))."\"";
                $type = (isset($question['type']) && $question['type'] != null) ? $question['type'] : 'radio';
                $published = (isset($question['published']) && $question['published'] != null) ? $question['published'] : 1;
                $wrong_answer_text = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['wrong_answer_text']))))."\"";
                $right_answer_text = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['right_answer_text']))))."\"";
                $explanation = "\"".htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['explanation']))))."\"";
                $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
                $question_weight = (isset($question['weight']) && $question['weight'] != null) ? $question['weight'] : 1;

                $answers_line = '';
                $answers = $this->get_question_answers($question['id']);
                foreach ($answers as $ans){
                    $answer = htmlspecialchars( stripslashes( str_replace( "\n", "", $ans['answer'] ) ) );
                    $image = (isset($ans['image']) && ($ans['image'] != null || $ans['image'] != '')) ? ($ans['image']) : '';
                    $correct = htmlentities($ans['correct']);
                    $weight = isset($ans['weight']) && $ans['weight'] != null ? htmlentities($ans['weight']) : 0;
                    $placeholder = isset($ans['placeholder']) && $ans['placeholder'] != '' ? htmlspecialchars( stripslashes( str_replace( "\n", "", $ans['placeholder'] ) ) ) : "";
                    $answers_array = array(
                        $answer,
                        $correct,
                        $weight,
                        $image,
                        $placeholder
                    );
                    $answers_line .= implode( '::', $answers_array ) . ";;";
                }
                $answers = "\"" . $answers_line . "\"";

                $questions_options = (isset($question['options']) && ($question['options'] != '' && $question['options'] != null)) ? json_decode($question['options'], true) : array();
                $options = array();
                $bg_image = (isset($questions_options['bg_image']) && $questions_options['bg_image'] != '') ? $questions_options['bg_image'] : '';
                $use_html = (isset($questions_options['use_html']) && $questions_options['use_html'] != '') ? $questions_options['use_html'] : 'off';
                $enable_question_text_max_length = (isset($questions_options['enable_question_text_max_length']) && $questions_options['enable_question_text_max_length'] != '') ? $questions_options['enable_question_text_max_length'] : 'off';
                $question_text_max_length = (isset($questions_options['question_text_max_length']) && $questions_options['question_text_max_length'] != '') ? $questions_options['question_text_max_length'] : '';
                $question_limit_text_type = (isset($questions_options['question_limit_text_type']) && $questions_options['question_limit_text_type'] != '') ? $questions_options['question_limit_text_type'] : '';
                $question_enable_text_message = (isset($questions_options['question_enable_text_message']) && $questions_options['question_enable_text_message'] != '') ? $questions_options['question_enable_text_message'] : 'off';

                $options[] = "bg_image=" . $bg_image;
                $options[] = "use_html=" . $use_html;
                $options[] = "enable_question_text_max_length=" . $enable_question_text_max_length;
                $options[] = "question_text_max_length=" . $question_text_max_length;
                $options[] = "question_limit_text_type=" . $question_limit_text_type;
                $options[] = "question_enable_text_message=" . $question_enable_text_message;
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
            array( 'text' => "answers" ),
            array( 'text' => "options" )
		);
        $quests[] = $questions_headers;
		foreach ( $questions as &$question ) {

            $category = (isset($question['category_title']) && $question['category_title'] != '') ? $question['category_title'] : 'Uncategorized';
            $question_content = htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['question']))));
            $question_title = (isset($question['question_title']) && $question['question_title'] != '') ? strip_tags(stripslashes(str_replace("\n", "", wpautop($question['question_title'])))) : '';
            $question_image = (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '';
            $question_hint = htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['question_hint']))));
            $type = (isset($question['type']) && $question['type'] != null) ? $question['type'] : 'radio';
            $published = (isset($question['published']) && $question['published'] != null) ? $question['published'] : 1;
            $wrong_answer_text = htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['wrong_answer_text']))));
            $right_answer_text = htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['right_answer_text']))));
            $explanation = htmlspecialchars(stripslashes(str_replace("\n", "", wpautop($question['explanation']))));
            $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
            $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
            $question_weight = (isset($question['weight']) && $question['weight'] != null) ? $question['weight'] : 1;

            $answers = $this->get_question_answers($question['id']);

            foreach ( $answers as &$answer ) {
                unset($answer['id']);
                unset($answer['question_id']);
            }

			$answers = json_encode($answers);
			$answers = trim($answers, '[]');
            $options = (isset($question['options']) && $question['options'] != '') ? json_decode($question['options'], true) : array();
            if( gettype( $option ) != 'array' ){
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

            $answers = $this->get_question_answers($question['id']);

            $q = array(
                array( 'text' => $question_content ),
                array( 'text' => $category )
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

            array_splice($q,2,0,$correct_answer);
            $quests[] = $q;
        }

        $questions_headers = array(
            array( 'text' => "Question" ),
            array( 'text' => "Category" ),
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
            unset($question['category_title']);
            unset($question['category_id']);
            $answers = $this->get_question_answers($question['id']);
            foreach ( $answers as &$answer ) {
                unset($answer['id']);
                unset($answer['question_id']);
            }
            $options = (isset($question['options']) && $question['options'] != '') ? json_decode($question['options'], true) : array();
            if( gettype( $option ) != 'array' ){
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
            unset($question['id']);
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
                array( 'text' => __( "Correct answers", $this->plugin_name ) ),
                array( 'text' => __( "User answers", $this->plugin_name ) ),
                array( 'text' => __( "Status", $this->plugin_name ) )
            );
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
                    $correct_answers = $this->get_correct_answers($question_id);
                    $is_text_type = $this->question_is_text_type($question_id);
                    $text_type = $this->text_answer_is($question_id);
                    $not_multiple_text_types = array("number", "date");

                    if($this->question_is_text_type($question_id)){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                    }else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                    }

                    $successed_or_failed = ($option == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );

                    $question = html_entity_decode(strip_tags(stripslashes($question_content["question"])));
                    if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                        $c_answers = explode('%%%', $correct_answers);
                        if(!empty($c_answers)){
                            $correct_answers = trim($c_answers[0]);
                        }
                    }else{
                        if($text_type == 'date'){
                            $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                        }
                    }
                    $correct_answer = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                    $user_answer    = html_entity_decode(strip_tags(stripslashes($user_answered)));


                    $questions = array(
                        array( 'text' => $index .". ". $question ),
                        array( 'text' => $correct_answer ),
                        array( 'text' => $user_answer ),
                        array( 'text' => $successed_or_failed )
                    );

                    $quests[] = $questions;
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
            );

            $quests = array();
            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id     = absint(intval(explode('_', $key)[2]));
                    $question_content = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                    $correct_answers = $this->get_correct_answers($question_id);

                    if($this->question_is_text_type($question_id)){
                        $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                    }else{
                        $user_answered = $this->get_user_answered($options->user_answered, $key);
                    }

                    if ($user_answered == '' || ( isset($user_answered['status']) && $user_answered['status'] == false ) ) {
                        $user_answered = ' - ';
                    }

                    $successed_or_failed = ($option == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );

                    $question       = esc_attr(stripslashes($question_content["question"]));
                    $correct_answer = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                    $user_answer    = html_entity_decode(strip_tags(stripslashes($user_answered)));
                    $questions = array(
                        'api_question'       => $question,
                        'api_correct_answer' => $correct_answer,
                        'api_user_answer'    => $user_answer,
                        'api_status'         => $successed_or_failed,
                        'api_check_status'   => $option,
                    );

                    $quests[] = $questions;
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
            $quiz_id = ($quiz_id !== null) ? $quiz_id : "SELECT quiz_id FROM {$results_table}";
            $user_id = (isset($_REQUEST['user_id']) && ($_REQUEST['user_id'] != null || $_REQUEST['user_id'] != '')) ? implode(',', $_REQUEST['user_id']) : "SELECT user_id FROM {$results_table} WHERE quiz_id IN ($quiz_id)";
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
                    $custom_fields_active[] = $attr;
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
            foreach ($results as $key => $result) {
                $row_id    = intval($result->id);
                $user_id   = intval($result->user_id);
                $user      = get_user_by('id', $user_id);
                $user      = ($user_id === 0) ? 'Guest' : $user->data->display_name;
                $email     = (isset($result->user_email) && ($result->user_email !== '' || $result->user_email !== null)) ? stripslashes($result->user_email) : '';
                $user_nam  = (isset($result->user_name) && ($result->user_name !== '' || $result->user_name !== null)) ? stripslashes($result->user_name) : '';
                $user_phone  = (isset($result->user_phone) && ($result->user_phone !== '' || $result->user_phone !== null)) ? stripslashes($result->user_phone) : '';

                $user_name = html_entity_decode(strip_tags(stripslashes($user_nam)));
                $options   = json_decode($result->options);

                $user_attributes = (isset($options->attributes_information) && ( $options->attributes_information !== '' || $options->attributes_information !== null ) ) ? $options->attributes_information : null;

                $user_attributes_tvyal = array();

                if ($user_attributes !== null) {
                    $user_attributes = (array)$user_attributes;
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
                        $correct_answers  = $this->get_correct_answers($question_id);

                        if($this->question_is_text_type($question_id)){
                            $user_answered = $this->get_user_text_answered($options->user_answered, $key);
                        }else{
                            $user_answered = $this->get_user_answered($options->user_answered, $key);
                        }

                        $question        = esc_attr(strip_tags(stripslashes($question_content["question"])));
                        $correct_answer  = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                        $user_answer     = html_entity_decode(strip_tags(stripslashes($user_answered)));

                        $q               = $question;
                        $correct_answ    = $correct_answer;
                        $answers         = $user_answer;
                        $quest_data[strval($question_id)] = array(
                            'question'       => $q,
                            'correct_answer' => $correct_answ,
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

                foreach ($custom_fields_active as $qid => $custom_field) {

                    $user_attributes_quest = array(
                        array( 'text' => $custom_field['name'] ),
                        array( 'text' => '' ),
                    );
                    foreach ($user_attr_data as $kkk => $attr_data) {
                        if (! isset( $attr_data[$qid] ) ) {
                            $attr_data[$qid] = '';
                        }

                        $user_attributes_quest[] = $attr_data[$qid];

                    }
                    $attributes[] = $user_attributes_quest;

                }
            }
            $questions = array();
            $user_headers = array();
            $user_answered_quest = array();

            foreach ($quest_data as $qid => $question) {

                $user_answered_quest = array(
                    array( 'text' => $question['question'] ),
                    array( 'text' => $question['correct_answer'] ),
                );

                foreach ($users_data as $user => $usr_ans) {
                    $answers = array( 'text' => $usr_ans[$qid] );
                    $user_answered_quest[] = $answers;
                }
                $questions[] = $user_answered_quest;

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

            $quest[] = array(
                array( 'text' => __( "Questions", $this->plugin_name ) ),
                array( 'text' => __( "Correct answers", $this->plugin_name ) ),
            );

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
        if (isset($_REQUEST['flag']) && $_REQUEST['flag']) {
            $gusets_in_sql1 = ' WHERE user_id != 0 ';
            $gusets_in_sql2 = ' AND ep.user_id != 0 ';
        }
        $user_id_sql = "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports" . $gusets_in_sql1;
        $quiz_id_sql = "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $user_id_sql = "SELECT rp.user_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS rp
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS qz
                                ON rp.quiz_id = qz.id
                            WHERE rep.status = 'finished' AND rp.status = 'finished' AND qz.author_id = ".$current_user . $gusets_in_sql2;
            $quiz_id_sql = "SELECT rep.quiz_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS rep
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS qu
                                ON rep.quiz_id = qu.id
                            WHERE qu.author_id = ".$current_user;
        }
        $user_id = (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null) ? implode(',', $_REQUEST['user_id']) : $user_id_sql;
        $gusets_in_sql1 = '';
        $gusets_in_sql2 = '';
        if (isset($_REQUEST['flag']) && $_REQUEST['flag']) {
            $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? intval($_REQUEST['quiz_id']) : $quiz_id_sql;
            $gusets_in_sql1 = ' r.user_id != 0 ';
            $gusets_in_sql2 = ' user_id != 0 ';
        }else{
            $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? implode(',', $_REQUEST['quiz_id']) : $quiz_id_sql;
        }

        $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');
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
        
        $user_id_sql = "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports";
        $quiz_id_sql = "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $current_user = get_current_user_id();
        if( ! $this->current_user_can_edit ){
            $user_id_sql = "SELECT r.user_id 
                            FROM {$wpdb->prefix}aysquiz_reports AS r
                            LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                                ON r.quiz_id = q.id
                            WHERE r.status = 'finished' AND q.author_id = ".$current_user;
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
                    $user_roles = __( 'Subscriber', $this->plugin_name );
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
                $user_roles = __( 'Subscriber', $this->plugin_name );
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
                $user_roles = __( 'Subscriber', $this->plugin_name );
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

        $data = $_REQUEST;
        $quiz_title = $_REQUEST['ays_quiz_title'];
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
        $quiz_default_options = ($quiz_settings->ays_get_setting('quiz_default_options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('quiz_default_options');
        if (! empty($quiz_default_options)) {
            $options = $quiz_default_options;
        }

        foreach ($questions as $question_key => $question) {
            $wpdb->insert($questions_table, array(
                'category_id' => $questions_cat[$question_key],
                'question' => $question,
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
            foreach ($answers[$question_key] as $key => $answer) {
                $type = $questions_type[$question_key];

                if($type == "text"){
                    $correct = 1;
                }else{
                    $correct = ($answers_correct[$question_key][$key] == "true") ? 1 : 0;
                }
                $placeholder = '';

                $wpdb->insert($answers_table, array(
                    'question_id' => $question_id,
                    'answer' => esc_sql( trim($answer) ),
                    'correct' => $correct,
                    'ordering' => $key,
                    'placeholder' => $placeholder
                ));
            }
        }
        $questions_ids = rtrim($questions_ids, ",");
        $wpdb->insert($quizes_table, array(
            'title' => $quiz_title,
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
            
            $row = "<table id='ays-results-table'>";
            
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
            if ($user_ip != '') {
                $row .= '<tr class="ays_result_element">
                            <td>'.__('User',$this->plugin_name).' IP</td>
                            <td colspan="3">' . $from . '</td>
                        </tr>';
            }
            
            $user_name = $user_id === 0 ? __( "Guest", $this->plugin_name ) : $user->data->display_name;
            if($user_id !== 0){
                $row .= '<tr class="ays_result_element">
                        <td>'.__('User',$this->plugin_name).' ID</td>
                        <td colspan="3">' . $user_id . '</td>
                    </tr>';
            }
            $row .= '<tr class="ays_result_element">
                    <td>'.__('User',$this->plugin_name).'</td>
                    <td colspan="3">' . $user_name . '</td>
                </tr>';
            
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

            if(isset($results['unique_code']) && $results['unique_code'] !== ''){
                $row .= '<tr class="ays_result_element">
                            <td>'.__('Unique Code',$this->plugin_name).'</td>
                            <td colspan="3"><strong>' . strtoupper($results['unique_code']) . '</strong></td>
                        </tr>';
            }

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
                    $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                    $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                    $correct_answers = $this->get_correct_answers($question_id);
                    $correct_answer_images = $this->get_correct_answer_images($question_id);
                    $is_text_type = $this->question_is_text_type($question_id);
                    $text_type = $this->text_answer_is($question_id);
                    $not_multiple_text_types = array("number", "date");

                    if($is_text_type){
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

                    $not_influence_to_score = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? true : false;
                    if ( $not_influence_to_score ) {
                        $not_influance_check_td = ' colspan="2" ';
                    }else{
                        $not_influance_check_td = '';
                    }

                    $question_image = isset( $question["question_image"] ) && $question["question_image"] != '' ? $question["question_image"] : '';
                    if($calc_method == 'by_correctness'){
                        $row .= '<tr class="'.$tr_class.'">
                            <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                        if( $question_image != '' ){
                            $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                        }
                        $row .= (stripslashes($question["question"])) .
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
                                if(strtolower(trim($user_answered)) == strtolower(trim($c_ans))){
                                    $c_answer = $c_ans;
                                    break;
                                }
                            }
                            $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>';
                            $row .= '<p class="success">' . esc_attr( $c_answer ) . '<br>'.$correct_answer_images.'</p>';
                            $row .= '</td>';
                        }else{
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
                        $user_answer_content = esc_attr( $user_answered );
                        if($use_html){
                            $user_answer_content = stripslashes( $user_answered );
                        }

                        $row .= '<td '.$not_influance_check_td.' class="ays-report-user-answer">'.__('User answered',$this->plugin_name).':<br/>
                            <p class="'.$status_class.'">' . $user_answer_content . '</p>
                        </td>';

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
                        $row .= (stripslashes($question["question"])) .
                                '</td>';
                        $row .= '<td class="ays-report-user-answer ays-report-user-answer-by-points">'.__('User answered',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr(stripslashes($user_answered)) . '</p></td>
                                <td class="ays-report-answer-point">'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr($ans_point) . '</p></td>
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
        if( current_user_can( $capability ) ){
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
                        $title = $results['name'];
                    }else{                        
                        $title = $results['title'];
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
//            echo "cURL Error #:" . $err;
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
//            echo "cURL Error #:" . $err;
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
			CURLOPT_URL            => "$url/api/3/$data",
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

//            $is_there_guest = 0 == $wpdb->get_var("SELECT MIN(user_id) FROM {$results_table}");
//
//            if ($is_there_guest) {
//                $users[] = array('user_id' => 0, 'display_name' => 'Guests');
//            }
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
    
    public function quiz_maker_add_dashboard_widgets() {
        $capability = $this->quiz_maker_capabilities();
        if( current_user_can( $capability ) ){
            wp_add_dashboard_widget(
                'quiz-maker', 
                'Quiz Maker Status', 
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
        
        $questions_label = intval($questions_count) == 1 ? "question" : "questions";
        $quizzes_label = intval($quizzes_count) == 1 ? "quiz" : "quizzes";
        $results_label = intval($results_count) == 1 ? "new result" : "new results";
        
        // Display whatever it is you want to show.
        ?>
        <ul class="ays_quiz_maker_dashboard_widget">
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name; ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/icon-128x128.png"; ?>" alt="Quizzes">
                    <span><?php echo $quizzes_count; ?></span>
                    <span><?php echo __($quizzes_label, $this->plugin_name); ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-questions" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/question2.png"; ?>" alt="Questions">
                    <span><?php echo $questions_count; ?></span>
                    <span><?php echo __($questions_label, $this->plugin_name); ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-results" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/users2.png"; ?>" alt="Results">
                    <span><?php echo $results_count; ?></span>
                    <span><?php echo __($results_label, $this->plugin_name); ?></span>
                </a>
            </li>
        </ul>
        <div style="padding:10px;font-size:14px;border-top:1px solid #ccc;">
            <?php echo "Works version ".AYS_QUIZ_VERSION." of "; ?>
            <a href="<?php echo "admin.php?page=".$this->plugin_name ?>">Quiz Maker</a>
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
                    <a target="_blank" href='https://bit.ly/2m4Cya8'>WordPress.org</a>
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
            if($view_details == 'View details'){
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
        $search = isset($_REQUEST['q']) && $_REQUEST['q'] !='' ? $_REQUEST['q'] : null;
        $args = 'search=';
        if($search !== null){
            $args .= $search;
            $args .= '*';
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
            "current_date"  => current_time( 'Y-m-d H:i:s' ),
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

}
