<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Quiz_Maker_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AYS_QUIZ_NAME_VERSION' ) ) {
			$this->version = AYS_QUIZ_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'quiz-maker';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_integrations_hooks();
		$this->define_iframe_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Quiz_Maker_Loader. Orchestrates the hooks of the plugin.
	 * - Quiz_Maker_i18n. Defines internationalization functionality.
	 * - Quiz_Maker_Admin. Defines all hooks for the admin area.
	 * - Quiz_Maker_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
        if ( ! class_exists( 'WP_List_Table' ) ) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

		/**
		 * The class responsible for defining all functions for getting all quiz data
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-data.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-quiz-maker-admin.php';


		/*
		 * The class is responsible for showing quizes in wordpress default WP_LIST_TABLE style
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-quizes-list-table.php';

        /*
         * The class is responsible for showing quiz categories in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-quiz-categories-list-table.php';

        /*
         * The class is responsible for showing questions in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-questions-list-table.php';

        /*
         * The class is responsible for showing question categories in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-question-categories-list-table.php';

        /*
         * The class is responsible for showing quiz results in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-results-list-table.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-each-results-list-table.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-attributes-list-table.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-orders-list-table.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/quiz-maker-settings-actions.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-all-results-list-table.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-all-reviews-list-table.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-not-finished-results-list-table.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-questions-tags-list-table.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-questions-reports-list-table.php';

        /**
		 * The class responsible for defining all functions for getting all quiz integrations data
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-integrations.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-quiz-maker-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-user-page-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-leaderboards-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-user-position-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-category-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-all-results-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-quiz-all-results-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-display-questions-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-flash-cards-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-recent-quizes-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-extra-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-most-popular-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-other-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-intervals-chart-shortcode.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-show-all-orders-shortcode.php';	

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-pdfapi.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-iframe.php';
        
		$this->loader = new Quiz_Maker_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Quiz_Maker_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Quiz_Maker_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
    
    
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Quiz_Maker_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Add menu item
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_quizzes_submenu', 90 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_questions_submenu', 95 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_quiz_categories_submenu', 100 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_questions_categories_submenu', 105 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_custom_fields_submenu', 110 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_orders_submenu', 115 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_results_submenu', 120 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_dashboard_submenu', 132 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_general_settings_submenu', 130 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_affiliate_submenu', 133 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_featured_plugins_submenu', 135 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_addons_submenu', 140 );

        $this->loader->add_action( 'wp_ajax_gen_ays_quiz_shortcode', $plugin_admin, 'gen_ays_quiz_shortcode_callback');
        $this->loader->add_filter( "mce_external_plugins", $plugin_admin, "ays_quiz_register_tinymce_plugin");
        $this->loader->add_filter( 'mce_buttons', $plugin_admin, 'ays_quiz_add_tinymce_button');

        $this->loader->add_action( 'wp_ajax_add_question_rows', $plugin_admin, 'add_question_rows' );
        $this->loader->add_action( 'wp_ajax_nopriv_add_question_rows', $plugin_admin, 'add_question_rows' );

        $this->loader->add_action( 'wp_ajax_ays_show_results', $plugin_admin, 'ays_show_results' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_show_results', $plugin_admin, 'ays_show_results' );
        
        $this->loader->add_action( 'wp_ajax_ays_questions_export', $plugin_admin, 'ays_questions_export' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_questions_export', $plugin_admin, 'ays_questions_export' );
        
        $this->loader->add_action( 'wp_ajax_ays_questions_statistics_export', $plugin_admin, 'ays_questions_statistics_export' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_questions_statistics_export', $plugin_admin, 'ays_questions_statistics_export' );

        $this->loader->add_action( 'wp_ajax_ays_export_result_pdf', $plugin_admin, 'ays_export_result_pdf' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_export_result_pdf', $plugin_admin, 'ays_export_result_pdf' );

        $this->loader->add_action( 'wp_ajax_ays_single_question_results_export', $plugin_admin, 'ays_single_question_results_export' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_single_question_results_export', $plugin_admin, 'ays_single_question_results_export' );

        $this->loader->add_action( 'wp_ajax_ays_answers_statistics_export', $plugin_admin, 'ays_answers_statistics_export' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_answers_statistics_export', $plugin_admin, 'ays_answers_statistics_export' );

        $this->loader->add_action( 'wp_ajax_ays_results_export_file', $plugin_admin, 'ays_results_export_file' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_results_export_file', $plugin_admin, 'ays_results_export_file' );
        
        $this->loader->add_action( 'wp_ajax_get_current_quiz_statistic', $plugin_admin, 'get_current_quiz_statistic' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_current_quiz_statistic', $plugin_admin, 'get_current_quiz_statistic' );

        $this->loader->add_action( 'wp_ajax_deactivate_plugin_option_qm', $plugin_admin, 'deactivate_plugin_option');
        $this->loader->add_action( 'wp_ajax_nopriv_deactivate_plugin_option_qm', $plugin_admin, 'deactivate_plugin_option');

        $this->loader->add_action( 'wp_ajax_ays_quick_start', $plugin_admin, 'ays_quick_start' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quick_start', $plugin_admin, 'ays_quick_start' );

        $this->loader->add_action( 'wp_ajax_ays_live_preivew_content', $plugin_admin, 'ays_live_preivew_content' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_live_preivew_content', $plugin_admin, 'ays_live_preivew_content' );
        
        $this->loader->add_action( 'wp_ajax_ays_quiz_users_search', $plugin_admin, 'ays_quiz_users_search' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_users_search', $plugin_admin, 'ays_quiz_users_search' );

        $this->loader->add_action( 'wp_ajax_ays_get_woocommerce_products', $plugin_admin, 'ays_get_woocommerce_products' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_get_woocommerce_products', $plugin_admin, 'ays_get_woocommerce_products' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_reports_user_search', $plugin_admin, 'ays_quiz_reports_user_search' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_reports_user_search', $plugin_admin, 'ays_quiz_reports_user_search' );

        // Generate certificate preview ajax ----->
		$this->loader->add_action( 'wp_ajax_ays_generate_cert_preview', $plugin_admin, 'ays_generate_cert_preview' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_generate_cert_preview', $plugin_admin, 'ays_generate_cert_preview' );
        // <-----

		$this->loader->add_action( 'wp_ajax_ays_save_google_credentials', $plugin_admin, 'ays_save_google_credentials' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_save_google_credentials', $plugin_admin, 'ays_save_google_credentials' );

		// EXPORT FILTERS
		$this->loader->add_action( 'wp_ajax_ays_show_filters', $plugin_admin, 'ays_show_filters' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_show_filters', $plugin_admin, 'ays_show_filters' );
        
		$this->loader->add_action( 'wp_ajax_ays_results_export_filter', $plugin_admin, 'ays_results_export_filter' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_results_export_filter', $plugin_admin, 'ays_results_export_filter' );
        
        // Questions EXPORT FILTERS
		$this->loader->add_action( 'wp_ajax_ays_show_questions_filters', $plugin_admin, 'ays_show_questions_filters' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_show_questions_filters', $plugin_admin, 'ays_show_questions_filters' );

        $this->loader->add_action( 'wp_ajax_ays_questions_export_filter', $plugin_admin, 'ays_questions_export_filter' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_questions_export_filter', $plugin_admin, 'ays_questions_export_filter' );

        // SEND TEST MAIL
		$this->loader->add_action( 'wp_ajax_ays_send_testing_mail', $plugin_admin, 'ays_send_testing_mail' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_send_testing_mail', $plugin_admin, 'ays_send_testing_mail' );

        // Generate Coupons
		$this->loader->add_action( 'wp_ajax_ays_generate_coupons', $plugin_admin, 'ays_generate_coupons' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_generate_coupons', $plugin_admin, 'ays_generate_coupons' );

		// Copied Coupons
		$this->loader->add_action( 'wp_ajax_ays_copied_coupons', $plugin_admin, 'ays_copied_coupons' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_copied_coupons', $plugin_admin, 'ays_copied_coupons' );

        // Admin Notes
		$this->loader->add_action( 'wp_ajax_get_admin_notes', $plugin_admin, 'get_admin_notes' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_admin_notes', $plugin_admin, 'get_admin_notes' );

        // Update database tables
		$this->loader->add_action( 'wp_ajax_ays_quiz_update_database_tables', $plugin_admin, 'ays_quiz_update_database_tables' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_update_database_tables', $plugin_admin, 'ays_quiz_update_database_tables' );

        $this->loader->add_action( 'wp_ajax_get_published_questions_ajax', $plugin_admin, 'get_published_questions_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_published_questions_ajax', $plugin_admin, 'get_published_questions_ajax' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_author_user_search', $plugin_admin, 'ays_quiz_author_user_search' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_author_user_search', $plugin_admin, 'ays_quiz_author_user_search' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_install_plugin', $plugin_admin, 'ays_quiz_install_plugin' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_install_plugin', $plugin_admin, 'ays_quiz_install_plugin' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_activate_plugin', $plugin_admin, 'ays_quiz_activate_plugin' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_activate_plugin', $plugin_admin, 'ays_quiz_activate_plugin' );

        $this->loader->add_action( 'wp_ajax_get_quiz_question_html', $plugin_admin, 'get_quiz_question_html' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_quiz_question_html', $plugin_admin, 'get_quiz_question_html' );

        // Generate password via import CSV | TXT
		$this->loader->add_action( 'wp_ajax_ays_generate_passwords_via_import', $plugin_admin, 'ays_generate_passwords_via_import' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_generate_passwords_via_import', $plugin_admin, 'ays_generate_passwords_via_import' );
        
        // Add Settings link to the plugin
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
        $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

        // Before VC Init
        $this->loader->add_action( 'vc_before_init', $plugin_admin, 'vc_before_init_actions' );

        if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
        	$this->loader->add_action( 'elementor/widgets/register', $plugin_admin, 'quiz_maker_el_widgets_registered' );
        } else {
        	$this->loader->add_action( 'elementor/widgets/widgets_registered', $plugin_admin, 'quiz_maker_el_widgets_registered' );
        }
        
        //Widget
        $this->loader->add_action( 'widgets_init', $plugin_admin, 'load_quiz_maker_widget' );        
        
        $this->loader->add_action( 'admin_title', $plugin_admin, 'change_dashboard_title' );
		
        $this->loader->add_action( 'in_admin_footer', $plugin_admin, 'quiz_maker_admin_footer', 1 );
        
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'quiz_maker_add_dashboard_widgets' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'codemirror_enqueue_scripts');
        
        $this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'quiz_maker_row_meta', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Quiz_Maker_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_user_page = new Quiz_Maker_User_Page( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_leaderboards = new Quiz_Maker_Leaderboards_Shortcode( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_user_position_leaderboard = new Quiz_Maker_Leaderboard_Position_Shortcode( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_quiz_category = new Quiz_Maker_Quiz_Category( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_results_page = new Quiz_Maker_All_Results( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_quiz_all_results_page = new Quiz_Maker_Quiz_All_Results( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_display_questions = new Quiz_Maker_Display_Questions( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_quiz_flash_cards_page = new Quiz_Maker_Flash_Cards( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_recent_quizes_page = new Quiz_Maker_Recent_Quizes( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_extra_shortcodes = new Ays_Quiz_Maker_Extra_Shortcodes_Public( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_most_popular_shortcodes = new Ays_Quiz_Maker_Most_Popular_Shortcodes_Public( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_other_shortcodes = new Ays_Quiz_Maker_Other_Shortcodes( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_all_orders = new Quiz_Maker_All_Orders( $this->get_plugin_name(), $this->get_version() );
        $plugin_public_intervals_chart_shortcodes = new Ays_Quiz_Maker_Intervals_Chart_Shortcodes_Public( $this->get_plugin_name(), $this->get_version() );
                
        $this->loader->add_action( 'wp_ajax_ays_finish_quiz', $plugin_public, 'ays_finish_quiz' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_finish_quiz', $plugin_public, 'ays_finish_quiz' );

        $this->loader->add_action( 'wp_ajax_ays_rate_the_quiz', $plugin_public, 'ays_rate_the_quiz' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_rate_the_quiz', $plugin_public, 'ays_rate_the_quiz' );
        
        $this->loader->add_action( 'wp_ajax_ays_get_rate_last_reviews', $plugin_public, 'ays_get_rate_last_reviews' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_get_rate_last_reviews', $plugin_public, 'ays_get_rate_last_reviews' );
        
        $this->loader->add_action( 'wp_ajax_ays_load_more_reviews', $plugin_public, 'ays_load_more_reviews' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_load_more_reviews', $plugin_public, 'ays_load_more_reviews' );
        
        $this->loader->add_action( 'wp_ajax_ays_get_user_information', $plugin_public, 'ays_get_user_information' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_get_user_information', $plugin_public, 'ays_get_user_information' );

        $this->loader->add_action( 'wp_ajax_ays_generated_used_passwords', $plugin_public, 'ays_generated_used_passwords' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_generated_used_passwords', $plugin_public, 'ays_generated_used_passwords' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_check_user_started', $plugin_public, 'ays_quiz_check_user_started' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_check_user_started', $plugin_public, 'ays_quiz_check_user_started' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_check_user_started_for_paypal', $plugin_public, 'ays_quiz_check_user_started_for_paypal' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_check_user_started_for_paypal', $plugin_public, 'ays_quiz_check_user_started_for_paypal' );
 
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles_early' );
        
        $this->loader->add_action( 'wp_ajax_user_reports_info_popup_ajax', $plugin_public_user_page, 'user_reports_info_popup_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_user_reports_info_popup_ajax', $plugin_public_user_page, 'user_reports_info_popup_ajax' );

        $this->loader->add_action( 'wp_ajax_user_export_result_pdf', $plugin_public_user_page, 'user_export_result_pdf' );
        $this->loader->add_action( 'wp_ajax_nopriv_user_export_result_pdf', $plugin_public_user_page, 'user_export_result_pdf' );

        $this->loader->add_action( 'wp_ajax_ays_store_result_payed', $plugin_public, 'ays_store_result_payed' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_store_result_payed', $plugin_public, 'ays_store_result_payed' );

        $this->loader->add_action( 'wp_ajax_user_export_quiz_questions_pdf', $plugin_public, 'user_export_quiz_questions_pdf' );
        $this->loader->add_action( 'wp_ajax_nopriv_user_export_quiz_questions_pdf', $plugin_public, 'user_export_quiz_questions_pdf' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_send_question_report', $plugin_public, 'ays_quiz_send_question_report' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_send_question_report', $plugin_public, 'ays_quiz_send_question_report' );

        $this->loader->add_filter( 'ays_quiz_get_submission_results', $plugin_public, 'ays_quiz_get_submission_results_by_unique_code' );

        $this->loader->add_filter( 'script_loader_tag', $plugin_public, 'ays_quiz_add_data_attribute', 10, 2);


        $settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $settings_options = $settings_obj->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
        }else{
            $settings_options = array();
        }

        // Enable custom login form redirect if user fail
        $settings_options['quiz_enable_custom_login_form_redirect'] = (isset( $settings_options['quiz_enable_custom_login_form_redirect'] ) && $settings_options['quiz_enable_custom_login_form_redirect'] == 'on') ? sanitize_text_field( $settings_options['quiz_enable_custom_login_form_redirect'] ) : 'off';
        $quiz_enable_custom_login_form_redirect = (isset( $settings_options['quiz_enable_custom_login_form_redirect'] ) && $settings_options['quiz_enable_custom_login_form_redirect'] == 'on') ? true : false;

        if( $quiz_enable_custom_login_form_redirect ){
	        // WP Login redirect
	        $this->loader->add_action( 'wp_login_failed', $plugin_public, 'ays_quiz_front_end_login_fail');
	        $this->loader->add_filter( 'authenticate', $plugin_public, 'ays_quiz_authenticate_login', 99, 3);
        }
	}


	/**
	 * Register all of the hooks related to the integrations functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_integrations_hooks() {

		$plugin_integrations = new Quiz_Maker_Integrations( $this->get_plugin_name(), $this->get_version() );

		// Action hooks
		// Quiz Maker Integrations / quiz page
		$this->loader->add_action( 'ays_qm_quiz_page_integrations', $plugin_integrations, 'ays_quiz_page_integrations_content' );

		// Quiz Maker Integrations / settings page
		$this->loader->add_action( 'ays_qm_settings_page_integrations', $plugin_integrations, 'ays_quiz_settings_page_integrations_content' );

		// Filter Hooks

		// ===== Mad Mimi integration ====
		// Mad Mimi integration / settings page
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_contents', $plugin_integrations, 'ays_settings_page_mad_mimi_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_saves', $plugin_integrations, 'ays_settings_page_mad_mimi_save', 1, 2 );

		// Mad Mimi integration / quiz page
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_contents', $plugin_integrations, 'ays_quiz_page_mad_mimi_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_options', $plugin_integrations, 'ays_quiz_page_mad_mimi_options', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_saves', $plugin_integrations, 'ays_quiz_page_mad_mimi_save', 1, 2 );

		// Mad Mimi integration / front-end
		$this->loader->add_filter( 'ays_qm_front_end_integrations_options', $plugin_integrations, 'ays_front_end_mad_mimi_options', 1, 2 );
		$this->loader->add_action( 'ays_qm_front_end_integrations', $plugin_integrations, 'ays_front_end_mad_mimi_functional', 1, 3 );


		// ===== ConvertKit integration ====
		// ConvertKit integration / settings page
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_contents', $plugin_integrations, 'ays_settings_page_convert_kit_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_saves', $plugin_integrations, 'ays_settings_page_convert_kit_save', 1, 2 );

		// ConvertKit integration / quiz page
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_contents', $plugin_integrations, 'ays_quiz_page_convert_kit_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_options', $plugin_integrations, 'ays_quiz_page_convert_kit_options', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_saves', $plugin_integrations, 'ays_quiz_page_convert_kit_save', 1, 2 );

		// ConvertKit integration / front-end
		$this->loader->add_filter( 'ays_qm_front_end_integrations_options', $plugin_integrations, 'ays_front_end_convert_kit_options', 1, 2 );
		$this->loader->add_action( 'ays_qm_front_end_integrations', $plugin_integrations, 'ays_front_end_convert_kit_functional', 1, 3 );


		// ===== GetResponse integration ====
		// GetResponse integration / settings page
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_contents', $plugin_integrations, 'ays_settings_page_get_response_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_saves', $plugin_integrations, 'ays_settings_page_get_response_save', 1, 2 );

		// GetResponse integration / quiz page
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_contents', $plugin_integrations, 'ays_quiz_page_get_response_content', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_options', $plugin_integrations, 'ays_quiz_page_get_response_options', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_saves', $plugin_integrations, 'ays_quiz_page_get_response_save', 1, 2 );

		// GetResponse integration / front-end
		$this->loader->add_filter( 'ays_qm_front_end_integrations_options', $plugin_integrations, 'ays_front_end_get_response_options', 1, 2 );
		$this->loader->add_action( 'ays_qm_front_end_integrations', $plugin_integrations, 'ays_front_end_get_response_functional', 1, 3 );

		// ===== reCAPTCHA integration ====
		// reCAPTCHA integration / settings page
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_contents', $plugin_integrations, 'ays_settings_page_recaptcha_content', 15, 2 );
		$this->loader->add_filter( 'ays_qm_settings_page_integrations_saves', $plugin_integrations, 'ays_settings_page_recaptcha_save', 1, 2 );

		// reCAPTCHA integration / quiz page
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_contents', $plugin_integrations, 'ays_quiz_page_recaptcha_content', 15, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_options', $plugin_integrations, 'ays_quiz_page_recaptcha_options', 1, 2 );
		$this->loader->add_filter( 'ays_qm_quiz_page_integrations_saves', $plugin_integrations, 'ays_quiz_page_recaptcha_save', 1, 2 );

		// reCAPTCHA integration / front-end
		$this->loader->add_filter( 'ays_qm_front_end_integrations_options', $plugin_integrations, 'ays_front_end_recaptcha_options', 1, 2 );
		$this->loader->add_filter( 'ays_qm_front_end_recaptcha', $plugin_integrations, 'ays_front_end_recaptcha_functional', 1, 3 );
		// ===== reCAPTCHA integration ====

	}

	/**
	 * Register all of the hooks related to the iframe functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_iframe_hooks() {
		$plugin_iframe = new Quiz_Maker_iFrame( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Action for opening quiz in iframe
		 */
		$this->loader->add_action( 'wp_ajax_ays_quiz_iframe_shortcode', $plugin_iframe, 'iframe_shortcode' );
		$this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_iframe_shortcode', $plugin_iframe, 'iframe_shortcode' );

		$this->loader->add_action( 'init', $plugin_iframe, 'add_rewrite_endpoint' );
		$this->loader->add_action( 'template_redirect', $plugin_iframe, 'add_template_redirect' );
		$this->loader->add_filter( 'request', $plugin_iframe, 'add_request_check' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Quiz_Maker_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
