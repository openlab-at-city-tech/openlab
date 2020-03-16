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

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix){
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style('sweetalert-css', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.min.css', array(), $this->version, 'all');
        if (false === strpos($hook_suffix, $this->plugin_name))
            return;
        wp_enqueue_style('wp-color-picker');
        // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );
        
        wp_enqueue_style('animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style('ays-animations.css', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style('ays-qm-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style('ays-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-jquery-datetimepicker', plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-admin.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-loaders", plugin_dir_url(__FILE__) . 'css/loaders.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-public", AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-public.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-public1", AYS_QUIZ_PUBLIC_URL . '/css/theme_elegant_dark.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-public2", AYS_QUIZ_PUBLIC_URL . '/css/theme_elegant_light.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-public3", AYS_QUIZ_PUBLIC_URL . '/css/theme_rect_dark.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name."-public4", AYS_QUIZ_PUBLIC_URL . '/css/theme_rect_light.css', array(), time()/*$this->version*/, 'all');


    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix){
        
        wp_enqueue_script( 'sweetalert-js', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.all.min.js', array('jquery'), $this->version, true );
        wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name . '-admin',  'quiz_maker_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));

        
        if (false === strpos($hook_suffix, $this->plugin_name))
            return;
        
        /* 
        ========================================== 
            Scripts for charts 
        ========================================== 
        */
        if (strpos($hook_suffix, 'results') || strpos($hook_suffix, 'orders') || strpos($hook_suffix, 'each-result')) {
            wp_enqueue_script('apm-charts-core', plugin_dir_url(__FILE__) . 'js/core.js', array('jquery'), $this->version, true);
            wp_enqueue_script('apm-charts-main', plugin_dir_url(__FILE__) . 'js/charts.js', array('jquery'), $this->version, true);
            wp_enqueue_script('apm-charts-animated', plugin_dir_url(__FILE__) . 'js/animated.js', array('jquery'), $this->version, true);
            wp_enqueue_script('quiz-maker-chart1', plugin_dir_url(__FILE__) . 'js/quiz-maker-charts.js', array('jquery'), $this->version, true);
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array('wp-color-picker'), $this->version, true);

        /* 
        ========================================== 
           * Bootstrap
           * select2
           * jQuery DataTables
        ========================================== 
        */
        wp_enqueue_script("ays_quiz_popper", plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script("ays_quiz_bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('select2js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('ays-datatable-js', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.js', array('jquery'), $this->version, true);
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

        wp_enqueue_script($this->plugin_name .'-rate-quiz', AYS_QUIZ_PUBLIC_URL . '/js/rating.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public1', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_dark.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public2', AYS_QUIZ_PUBLIC_URL . '/js/theme_elegant_light.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public3', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_dark.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public4', AYS_QUIZ_PUBLIC_URL . '/js/theme_rect_light.js', array('jquery'), $this->version, true);

        wp_enqueue_script( $this->plugin_name."-functions", plugin_dir_url(__FILE__) . 'js/partials/functions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-quiz-styles", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-styles.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-information-form", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-information-form.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-quick-start", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quick-start.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-tabs", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-quiz-tabs.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name."-questions", plugin_dir_url(__FILE__) . 'js/partials/quiz-maker-admin-questions.js', array('jquery', 'wp-color-picker'), $this->version, true);
        wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
		wp_enqueue_script( $this->plugin_name."-cookie.js", plugin_dir_url( __FILE__ ) . 'js/cookie.js', array( 'jquery' ), $this->version, true );
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

                wp_localize_script('jquery', 'cm_settings', $cm_settings);

                wp_enqueue_script('wp-theme-plugin-editor');
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
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0";
        $unread_results_count = $wpdb->get_var($sql);
        $menu_item = ($unread_results_count == 0) ? 'Quiz Maker' : 'Quiz Maker' . '<span class="ays_menu_badge" id="ays_results_bage">' . $unread_results_count . '</span>';
        
        $capability = $this->quiz_maker_capabilities();
                
        add_menu_page(
            'Quiz Maker', 
            $menu_item, 
            $capability, 
            $this->plugin_name,
            array($this, 'display_plugin_quiz_page'), 
            AYS_QUIZ_ADMIN_URL . '/images/icons/icon-128x128.png', 
            6
        );

        $hook_quiz_maker = add_submenu_page(
            $this->plugin_name,
            __('Quizzes', $this->plugin_name),
            __('Quizzes', $this->plugin_name),
            $capability,
            $this->plugin_name,
            array($this, 'display_plugin_quiz_page')
        );

        add_action("load-$hook_quiz_maker", array($this, 'screen_option_quizes'));

        $hook_questions = add_submenu_page(
            $this->plugin_name,
            __('Questions', $this->plugin_name),
            __('Questions', $this->plugin_name),
            $capability,
            $this->plugin_name . '-questions',
            array($this, 'display_plugin_questions_page')
        );

        add_action("load-$hook_questions", array($this, 'screen_option_questions'));
        
        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Quiz Categories', $this->plugin_name),
            __('Quiz Categories', $this->plugin_name),
            $capability,
            $this->plugin_name . '-quiz-categories',
            array($this, 'display_plugin_quiz_categories_page')
        );

        add_action("load-$hook_quiz_categories", array($this, 'screen_option_quiz_categories'));

        $hook_questions_categories = add_submenu_page(
            $this->plugin_name,
            __('Question Categories', $this->plugin_name),
            __('Question Categories', $this->plugin_name),
            $capability,
            $this->plugin_name . '-question-categories',
            array($this, 'display_plugin_question_categories_page')
        );

        add_action("load-$hook_questions_categories", array($this, 'screen_option_questions_categories'));

        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Custom Fields', $this->plugin_name),
            __('Custom Fields', $this->plugin_name),
            $capability,
            $this->plugin_name . '-quiz-attributes',
            array($this, 'display_plugin_quiz_attributes_page')
        );

        add_action("load-$hook_quiz_categories", array($this, 'screen_option_quiz_attributes'));

        $hook_quiz_orders = add_submenu_page(
            $this->plugin_name,
            __('Orders', $this->plugin_name),
            __('Orders', $this->plugin_name),
            $capability,
            $this->plugin_name . '-quiz-orders',
            array($this, 'display_plugin_orders_page')
        );

        add_action("load-$hook_quiz_orders", array($this, 'screen_option_orders'));

        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0";
        $unread_results_count = $wpdb->get_var($sql);
        $results_text = __('Results', $this->plugin_name);
        $menu_item = ($unread_results_count == 0) ? $results_text : $results_text . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
        $hook_results = add_submenu_page(
            $this->plugin_name,
            $results_text,
            $menu_item,
            $capability,
            $this->plugin_name . '-results',
            array($this, 'display_plugin_results_page')
        );

        add_action("load-$hook_results", array($this, 'screen_option_results'));
        
        $hook_each_result = add_submenu_page(
            'each_result_slug',
            __('Each', $this->plugin_name),
            null,
            $capability,
            $this->plugin_name . '-each-result',
            array($this, 'display_plugin_each_results_page')
        );

        add_action("load-$hook_each_result", array($this, 'screen_option_each_quiz_results'));

        add_filter('parent_file', array($this,'quiz_maker_select_submenu'));
        
        $hook_quizes = add_submenu_page(
            $this->plugin_name,
            __('Dashboard', $this->plugin_name),
            __('Dashboard', $this->plugin_name),
            $capability,
            $this->plugin_name . '-dashboard',
            array($this, 'display_plugin_setup_page')
        );
        
        $hook_settings = add_submenu_page( $this->plugin_name,
            __('General Settings', $this->plugin_name),
            __('General Settings', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_settings_page') 
        );
        add_action("load-$hook_settings", array($this, 'screen_option_settings'));
        
        add_submenu_page( $this->plugin_name,
            __('Featured Plugins', $this->plugin_name),
            __('Featured Plugins', $this->plugin_name),
            $capability,
            $this->plugin_name . '-featured-plugins',
            array($this, 'display_plugin_featured_plugins_page') 
        );
    }

    public function quiz_maker_select_submenu($file) {
        global $plugin_page;
        if ("quiz-maker-each-result" == $plugin_page) {
            $plugin_page = $this->plugin_name."-results";
        }
        return $file;
    }
    
    protected function quiz_maker_capabilities(){
        global $wpdb;
        $sql = "SELECT meta_value FROM {$wpdb->prefix}aysquiz_settings WHERE `meta_key` = 'user_roles'";
        $result = $wpdb->get_var($sql);
        
        $capability = 'manage_options';
        if($result !== null){
            $ays_user_roles = json_decode($result, true);
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $current_user_roles = $current_user->caps;
                $ishmar = 0;
                foreach($current_user_roles as $r){
                    if(in_array($r, $ays_user_roles)){
                        $ishmar++;
                    }
                }
                if($ishmar > 0){
                    $capability = "edit_posts";
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

    /**
     * Adding questions from modal to table
     */
    public function add_question_rows(){
        error_reporting(0);
        $this->quizes_obj = new Quizes_List_Table($this->plugin_name);

        if ((isset($_REQUEST["add_question_rows"]) && wp_verify_nonce($_REQUEST["add_question_rows"], 'add_question_rows')) || (isset($_REQUEST["add_question_rows_top"]) && wp_verify_nonce($_REQUEST["add_question_rows_top"], 'add_question_rows_top'))) {
            $question_ids = $_REQUEST["ays_questions_ids"];
            $rows = array();
            $ids = array();
            if (!empty($question_ids)) {
                foreach ($question_ids as $question_id) {
                    $data = $this->quizes_obj->get_published_questions_by('id', absint(intval($question_id)));
                    $table_question = (strip_tags(stripslashes($data['question'])));
                    $table_question = $this->ays_restriction_string("word", $table_question, 8);
                    $rows[] = '<tr class="ays-question-row ui-state-default" data-id="' . $data['id'] . '">
                                    <td class="ays-sort"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td>' . $table_question . '</td>
                                    <td>' . $data['type'] . '</td>
                                    <td>' . stripslashes($data['id']) . '</td>
                                    <td>
                                        <input type="checkbox" class="ays_del_tr" style="margin-right:15px;">
                                        <a href="javascript:void(0)" class="ays-delete-question" data-id="' . $data['id'] . '">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                               </tr>';
                    $ids[] = $data['id'];
                }

                echo json_encode(array(
                    'status' => true,
                    'rows' => $rows,
                    'ids' => $ids
                ));
                wp_die();
            } else {
                echo json_encode(array(
                    'status' => true,
                    'rows' => '',
                    'ids' => array()
                ));
                wp_die();
            }
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
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'csv';
        $results = array();
        switch($type){
            case 'csv':
                $results = $this->ays_questions_export_csv();
                break;
//            case 'xls':
//                $results = $this->ays_questions_export_xls();
//                break;
            case 'xlsx':
                $results = $this->ays_questions_export_xlsx();
                break;
            case 'json':
                $results = $this->ays_questions_export_json();
                break;
            default:
                $results = $this->ays_questions_export_csv();
                break;
        }
        
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($results);
        wp_die();
    }
    
    public function ays_questions_export_file($path){
        global $wpdb;
        error_reporting(0);
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
        $questions = $wpdb->get_results($sql);
        $export_file_fields = array('category_id','question','image','hint','type','published','wrong_answer_text','right_answer_text','answers');
        $results_array_csv = array();
        foreach ($questions as $key => $question){
            $question = (array)$question;
            $answers_line = '';
            $answers = $this->get_question_answers($question['id']);
            foreach ($answers as $answer){
                $answers_line .= htmlentities($answer['answer'])."::".htmlentities($answer['correct']).";;";
            }
            $question['answers'] = $answers_line;
            $results_array_csv[] = (object)array(
                $question['category_id'],
                htmlentities((str_replace("\n", "", wpautop($question['question'])))),
                (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '',
                htmlentities(str_replace("\n", "", wpautop($question['question_hint']))),
                $question['type'],
                $question['published'],
                htmlentities(str_replace("\n", "", wpautop($question['wrong_answer_text']))),
                htmlentities(str_replace("\n", "", wpautop($question['right_answer_text']))),
                $question['answers']
            );
        }
        
        $export_data = array(
            'data'          => $results_array_csv,
            'fields'        => $export_file_fields
        );
        echo json_encode($export_data);
        wp_die();
    }

	public function ays_questions_export_sql() {
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

	public function ays_questions_export_csv() {
        global $wpdb;
        error_reporting(0);
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
        $questions = $wpdb->get_results($sql);
        $export_file_fields = array(
            'category_id',
            'question',
            'question_image',
            'question_hint',
            'type',
            'published',
            'wrong_answer_text',
            'right_answer_text',
            'explanation',
            'user_explanation',
            'answers'
        );
        $results_array_csv = array();
        if(empty($questions)){
            $export_data = array(
                'status'     => false,
                'type'       => 'csv',
                'data'       => array(),
                'fields'     => array()
            );
        }else{
            foreach ($questions as $key => $question){
                $question = (array)$question;
                $answers_line = '';
                $answers = $this->get_question_answers($question['id']);
                foreach ($answers as $answer){
                    $answers_line .= htmlentities($answer['answer'])."::".htmlentities($answer['correct']).";;";
                }
                $question['answers'] = $answers_line;
                $q = array(
                    $question['category_id'],
                    htmlentities((str_replace("\n", "", wpautop($question['question'])))),
                    (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '',
                    htmlentities(str_replace("\n", "", wpautop($question['question_hint']))),
                    ($question['type'] == null) ? '' : $question['type'],
                    ($question['published'] == null) ? '' : $question['published'],
                    htmlentities(str_replace("\n", "", wpautop($question['wrong_answer_text']))),
                    htmlentities(str_replace("\n", "", wpautop($question['right_answer_text']))),
                    htmlentities(str_replace("\n", "", wpautop($question['explanation']))),
                    ($question['user_explanation'] == null) ? '' : $question['user_explanation'],
                    $question['answers']
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

	public function ays_questions_export_xls() {
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

	public function ays_questions_export_xlsx() {
		global $wpdb;
		error_reporting(0);
		$sql               = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
		$questions         = $wpdb->get_results($sql, 'ARRAY_A');
        $quests = array();
		$questions_headers = array(
            array( 'text' => "category_id" ),
            array( 'text' => "question" ),
            array( 'text' => "question_image" ),
            array( 'text' => "question_hint" ),
            array( 'text' => "type" ),
            array( 'text' => "published" ),
            array( 'text' => "wrong_answer_text" ),
            array( 'text' => "right_answer_text" ),
            array( 'text' => "explanation" ),
            array( 'text' => "user_explanation" ),
            array( 'text' => "answers" )
		);
        $quests[] = $questions_headers;
		foreach ( $questions as &$question ) {
			$answers = json_encode($this->get_question_answers($question['id']));
            
            $q = array(
                array( 'text' => $question['category_id'] ),
                array( 'text' => htmlentities((str_replace("\n", "", wpautop($question['question'])))) ),
                array( 'text' => (isset($question['question_image']) && $question['question_image'] != null) ? $question['question_image'] : '' ),
                array( 'text' => htmlentities(str_replace("\n", "", wpautop($question['question_hint']))) ),
                array( 'text' => ($question['type'] == null) ? '' : $question['type'] ),
                array( 'text' => ($question['published'] == null) ? '' : $question['published'] ),
                array( 'text' => htmlentities(str_replace("\n", "", wpautop($question['wrong_answer_text']))) ),
                array( 'text' => htmlentities(str_replace("\n", "", wpautop($question['right_answer_text']))) ),
                array( 'text' => htmlentities(str_replace("\n", "", wpautop($question['explanation']))) ),
                array( 'text' => ($question['user_explanation'] == null) ? '' : $question['user_explanation'] ),
                array( 'text' => $answers )
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

	public function ays_questions_export_json() {
		global $wpdb;
		error_reporting(0);
		$sql       = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
		$questions = $wpdb->get_results($sql, 'ARRAY_A');
		foreach ( $questions as &$question ) {
			$question['answers'] = $this->get_question_answers($question['id']);
		}
		$response = array(
			'status' => true,
			'data'   => $questions,
			"type"   => 'json'
		);
		return $response;
	}
    
    public function ays_results_export_filter(){
        global $wpdb;
        error_reporting(0);
        $user_id = (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null) ? implode(',', $_REQUEST['user_id']) : "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports";
        $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? implode(',', $_REQUEST['quiz_id']) : "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : 0;
        $date_to = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : 0;
//        $results = array($user_id, $quiz_id, $date_from, $date_to);
        $sql = "SELECT COUNT(*) AS qanak FROM {$wpdb->prefix}aysquiz_reports WHERE user_id IN ($user_id) AND quiz_id IN ($quiz_id) AND start_date BETWEEN '$date_from' AND '$date_to 23:59:59' ORDER BY id DESC";
        $results = $wpdb->get_row($sql);
        echo json_encode($results);
        wp_die();
    }
    
    public function ays_results_export_file($path){
        global $wpdb;
        error_reporting(0);
        $user_id = (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != null) ? implode(',', $_REQUEST['user_id']) : "SELECT user_id FROM {$wpdb->prefix}aysquiz_reports";
        $quiz_id = (isset($_REQUEST['quiz_id']) && $_REQUEST['quiz_id'] != null) ? implode(',', $_REQUEST['quiz_id']) : "SELECT quiz_id FROM {$wpdb->prefix}aysquiz_reports";
        $date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : 0;
        $date_to = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : 0;
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE user_id IN ($user_id) AND quiz_id IN ($quiz_id) AND start_date BETWEEN '$date_from' AND '$date_to 23:59:59' ORDER BY id DESC";
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
        
        echo json_encode($export_data);
        wp_die();
    }
    
    public function ays_results_export_csv($results, $attributes){
        
        global $wpdb;
        error_reporting(0);
        $export_file_fields = array('user_id','user_ip','start_date','end_date','score','rate','review','name','email','phone');
        foreach ($attributes as $attribute){
            array_push($export_file_fields, $attribute->name);
        }
        $results_array_csv = array();

        if(empty($results)){
            $export_data = array(
                'status'        => false,
                'data'          => array(),
                'fileFields'    => array(),
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
                $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();
                unset($result['quiz_id'],$result['id'],$result['options']);
                $results_array_csv[] = array(
                    $result['user_id'],
                    $result['user_ip'],
                    $result['start_date'],
                    $result['end_date'],
                    $result['score'],
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
            array( 'text' => "user_id" ),
            array( 'text' => "user_ip" ),
            array( 'text' => "start_date" ),
            array( 'text' => "end_date" ),
            array( 'text' => "score" ),
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
            $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();
            $res_array = array(
                array( 'text' => $result['user_id'] ),
                array( 'text' => $result['user_ip'] ),
                array( 'text' => $result['start_date'] ),
                array( 'text' => $result['end_date'] ),
                array( 'text' => $result['score'] ),
                array( 'text' => (isset($rate_result['score']) && $rate_result['score'] != null) ?  $rate_result['score'] : '' ),
                array( 'text' => (isset($rate_result['review']) && $rate_result['review'] != null) ? stripslashes(htmlspecialchars(str_replace("\n", "", (strip_tags($rate_result['review']))))) : '' ),
                array( 'text' => (isset($result['user_name']) && $result['user_name'] != null) ? $result['user_name'] : '' ),
                array( 'text' => (isset($result['user_email']) && $result['user_email'] != null) ? $result['user_email'] : '' ),
                array( 'text' => (isset($result['user_phone']) && $result['user_phone'] != null) ? $result['user_phone'] : '' ),
            );
            
            foreach ($attributes as $attribute){
                $attribute = (array)$attribute;
                if(isset($result_attributes[$attribute['name']]) && $result_attributes[$attribute['name']] != null){
//                    var_dump($result_attributes[$attribute['name']]);
//                    die;
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
            $result_attributes = isset($result_option['attributes_information'])?(array)$result_option['attributes_information']:array();
            $res_array = array(
                'user_id' => $result['user_id'],
                'user_ip' => $result['user_ip'],
                'start_date' => $result['start_date'],
                'end_date' => $result['end_date'],
                'score' => $result['score'],
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
        $questions = $_REQUEST['ays_quick_question'];
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
        $user = get_userdata($user_id);
        $author = array(
            'id' => $user->ID,
            'name' => $user->data->display_name
        );
        $options = array(
            'author' => $author,
        );
        foreach ($questions as $question_key => $question) {
            $wpdb->insert($questions_table, array(
                'category_id' => 1,
                'question' => $question,
                'published' => 1,
                'type' => 'radio',
                'create_date' => $create_date,
                'options' => json_encode($options)
            ));
            $question_id = $wpdb->insert_id;
            $questions_ids .= $question_id . ',';
            foreach ($answers[$question_key] as $key => $answer) {
                $wpdb->insert($answers_table, array(
                    'question_id' => $question_id,
                    'answer' => $answer,
                    'correct' => ($answers_correct[$question_key][$key] == "true") ? 1 : 0,
                    'ordering' => $key
                ));
            }
        }
        $questions_ids = rtrim($questions_ids, ",");
        $wpdb->insert($quizes_table, array(
            'title' => $quiz_title,
            'question_ids' => $questions_ids,
            'published' => 1,
            'options' => json_encode(array(
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
                'create_date' => $create_date,
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
            )),
            'quiz_category_id' => 1,
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
        $sql1 = "SELECT GROUP_CONCAT(question_ids SEPARATOR ',') AS ids 
                 FROM {$wpdb->prefix}aysquiz_quizes WHERE question_ids IS NOT NULL AND question_ids !='';";
        $res1 = $wpdb->get_var( $sql1 );
        if(! $res1){
            return array();
        }
        $res1 = array_unique(explode(',', $res1));
        if(empty($res1)){
            return array();
        }
        $res1 = trim(implode(',', $res1), ',');
        $sql = "SELECT quest.id 
                FROM {$wpdb->prefix}aysquiz_questions AS quest
                WHERE quest.id IN (".$res1.") 
                GROUP BY id";
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        $used_question = array();
        foreach ($results as $key => $value) {
            $used_question[] = $value["id"];
        }
        return $used_question;
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
            echo json_encode($data);
            wp_die();
        } else {
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
            $correctness = (array)$options->correctness;
            
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
                        <td colspan="4"><h1>' . __('User Information',$this->plugin_name) . '</h1></td>
                    </tr>';
            
            $row .= '<tr class="ays_result_element">
                        <td>'.__('User',$this->plugin_name).' IP</td>
                        <td colspan="3">' . $from . '</td>
                    </tr>';
            
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
            
            $sql = "UPDATE $results_table SET `read`=1 WHERE `id`=$id";
            $wpdb->get_var($sql);
            echo json_encode(array(
                "status" => true,
                "rows" => $row
            ));
            wp_die();
        }
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

    public function get_current_quiz_statistic(){
        error_reporting(0);
        $quiz_id = abs(intval($_REQUEST['quiz_id']));
        $dates = array();
        $dates_values = array();
        $dates_array = Results_List_Table::get_results_dates($quiz_id);
        $start_date = date_create($dates_array[0]['min_date']);
        $end_date = date_create($dates_array[0]['max_date']);
        $date_diff = date_diff($end_date, $start_date, true)->days;
        $start_date = (array)$start_date;
        $start_date_time = strtotime($start_date["date"]);
        $i = 0;
        while ($i != $date_diff + 1) {
            array_push($dates, date("Y-m-d", strtotime("+$i day", $start_date_time)));
            $i++;
        }
        foreach ($dates as $key => $date) {
            foreach (Results_List_Table::get_each_date_statistic($date) as $count) {
                $dates_values[] = (int)$count;
                $dates[$key] = date("F d", strtotime($dates[$key]));
            }
        }
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

        $result = json_encode(array('dates' => $dates, 'dates_values' => $dates_values, 'charts' => $charts));
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
                'message' => __( "The user have not answered to this question.", $this->plugin_name ),
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
                'message' => __( "The user have not answered to this question.", $this->plugin_name ),
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
        $text_types = array('text', 'number', 'short_text');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public function get_questions_categories(){
        global $wpdb;
        $categories_table = $wpdb->prefix . "aysquiz_categories";
        $get_cats = $wpdb->get_results("SELECT * FROM {$categories_table}", ARRAY_A);
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
        $plugin_array['ays_quiz_button_mce'] = AYS_QUIZ_BASE_URL . 'ays_quiz_shortcode.js';
        return $plugin_array;
    }

    public function ays_quiz_add_tinymce_button($buttons){
        $buttons[] = "ays_quiz_button_mce";
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
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

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
                    <td><label for="ays_quiz">Quiz Maker</label></td>
                    <td>
                     <span>
                               <select id="ays_quiz" style="padding: 2px; height: 25px; font-size: 16px;width:100%;">
                           <option>--<?php echo  __('Select Quiz',$this->plugin_name) ?>--</option>
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
        <script>

        </script>
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
                        $title = $results['question'];
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
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists",
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
			CURLOPT_URL            => "https://slack.com/api/conversations.list?token=$token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
			CURLOPT_HTTPHEADER     => array(
				"Content-Type: application/x-www-form-urlencoded",
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

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_show_filters') {
            
            $users = $wpdb->get_results(
                "SELECT
                    $results_table.user_id,
                    {$wpdb->prefix}users.display_name 
                FROM
                    $results_table
                JOIN {$wpdb->prefix}users ON $results_table.user_id = {$wpdb->prefix}users.ID 
                GROUP BY
                    $results_table.user_id",
            "ARRAY_A");
            $is_there_guest = 0 == $wpdb->get_var("SELECT MIN(user_id) FROM {$results_table}");
            if ($is_there_guest) {
                $users[] = array('user_id' => 0, 'display_name' => 'Guests');
            }
            $quizzes = $wpdb->get_results(
                "SELECT
                    $results_table.quiz_id,
                    $quiz_table.title 
                FROM
                    $results_table
                JOIN $quiz_table ON $results_table.quiz_id = $quiz_table.id
                GROUP BY
                    $results_table.quiz_id",
            "ARRAY_A");
            $date_min = $wpdb->get_var("SELECT DATE(MIN(start_date)) FROM {$results_table}");
            $date_max = $wpdb->get_var("SELECT DATE(MAX(start_date)) FROM {$results_table}");
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports ORDER BY id DESC";
            $qanak = $wpdb->get_var($sql);
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
        if(current_user_can($this->quiz_maker_capabilities())){
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
        $questions_count = Questions_List_Table::record_count();
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

}
