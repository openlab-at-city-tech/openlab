<?php
/**
 * Plugin Name: Knowledge Base for Documents and FAQs
 * Plugin URI: https://www.echoknowledgebase.com
 * Description: Create Echo Knowledge Base articles, docs and FAQs.
 * Version: 12.43.0
 * Author: Echo Plugins
 * Author URI: https://www.echoknowledgebase.com
 * Text Domain: echo-knowledge-base
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Knowledge Base for Documents and FAQs is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Knowledge Base for Documents and FAQs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Knowledge Base for Documents and FAQs. If not, see <http://www.gnu.org/licenses/>.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'EPKB_PLUGIN_NAME' ) ) {
	define( 'EPKB_PLUGIN_NAME', 'Echo Knowledge Base' );
}

if ( ! class_exists( 'Echo_Knowledge_Base' ) ) :

/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class Echo_Knowledge_Base {

	/* @var Echo_Knowledge_Base */
	private static $instance;

	public static $version = '12.43.0';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;
	public static $needs_min_add_on_version = array( 'LAY' => '1.2.1', 'MKB' => '1.10.0', 'RTD' => '1.0.0', 'IDG' => '1.0.0', 'BLK' => '1.0.0',
													 'SEA' => '1.0.0', 'PRF' => '1.0.0', 'PIE' => '1.0.0', 'ART' => '1.0.0' );

	/* @var EPKB_KB_Config_DB */
	public $kb_config_obj;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return Echo_Knowledge_Base
	 */
	public static function instance() {

		if ( ! empty( self::$instance ) && ( self::$instance instanceof Echo_Knowledge_Base ) ) {
			return self::$instance;
		}

		self::$instance = new Echo_Knowledge_Base();
		self::$instance->setup_system();
		self::$instance->setup_plugin();

		add_action( 'plugins_loaded', array( self::$instance, 'load_text_domain' ), 11 );

		return self::$instance;
	}

	/**
	 * Setup class autoloading and other support functions. Setup custom core features.
	 */
	private function setup_system() {

		// autoload classes ONLY when needed by executed code rather than on every page request
		require_once self::$plugin_dir . 'includes/system/class-epkb-autoloader.php';

		// register settings
		self::$instance->kb_config_obj = new EPKB_KB_Config_DB();

		// load non-classes
		require_once self::$plugin_dir . 'includes/system/plugin-setup.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration-public.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration-admin.php';
		require_once self::$plugin_dir . 'includes/system/plugin-links.php';

		new EPKB_Upgrades();

		// setup custom core features
		new EPKB_Articles_CPT_Setup();
		new EPKB_Articles_Admin();
		new EPKB_FAQs_CPT_Setup();

		// subscribe to category actions create/edit/delete including for REST requests in Gutenberg
		new EPKB_Categories_Admin();

		// blocks
		if ( EPKB_Block_Utilities::is_block_enabled() ) {
			new EPKB_Search_Block();
			new EPKB_Basic_Layout_Block();
			new EPKB_Tabs_Layout_Block();
			new EPKB_Categories_Layout_Block();
			new EPKB_Classic_Layout_Block();
			new EPKB_Drill_Down_Layout_Block();
		}
	}

	/**
	 * Setup plugin before it runs. Include functions and instantiate classes based on user action
	 */
	private function setup_plugin() {

		$action = EPKB_Utilities::get( 'action' );

		// process action request if any
		if ( ! empty( $action ) ) {
			$this->handle_action_request( $action );
		}

		// handle AJAX front & back-end requests (no admin, no admin bar)
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->handle_ajax_requests( $action );
			return;
		}

		// ADMIN or CLI
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            if ( $this->is_kb_plugin_active_for_network( 'echo-knowledge-base/echo-knowledge-base.php' ) ) {
                add_action( 'plugins_loaded', array( self::$instance, 'setup_backend_classes' ), 11 );
            } else {
                $this->setup_backend_classes();
            }
			return;
		}

		// catch saving and creating of Post in Gutenberg
		$server_referrer = isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '';
		if ( ! empty( $server_referrer ) && ( strpos( $server_referrer, '/wp-admin/post.php' ) !== false || strpos( $server_referrer, '/wp-admin/post-new.php' ) !== false ) ) {
			require_once self::$plugin_dir . 'includes/admin/admin-functions.php';
		}

		// FRONT-END (no ajax, possibly admin bar)
		new EPKB_Layouts_Setup();      // KB Main page shortcode, list of themes
		new EPKB_Articles_Setup();
		new EPKB_Templates();
		new EPKB_Shortcodes();
        new EPKB_Main_Page_Visual_Helper();
        new EPKB_Article_Page_Visual_Helper();
        new EPKB_Category_Page_Visual_Helper();
	}

	/**
	 * Handle plugin actions here such as saving settings
	 * @param $action
	 */
	private function handle_action_request( $action ) {

		if ( $action == 'eckb_apply_editor_changes' ) {
			new EPKB_Editor_Controller();
			return;
		}
		
		if ( $action == 'epkb_load_editor' ) {
			new EPKB_Editor_View();
			return;
		}

		if ( $action == 'epkb_download_debug_info' ) {
			new EPKB_Debug_Controller();
			return;
		}

		if ( empty( $action ) || ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}
	}

	/**
	 * Handle AJAX requests coming from front-end and back-end
	 * @param $action
	 */
	private function handle_ajax_requests( $action ) {

        if ( empty( $action ) ) {
            return;
        }

		if ( $action == 'epkb-search-kb' ) {  // user searching KB
			new EPKB_KB_Search();
			return;
		} else if ( in_array( $action, array( 'epkb_toggle_debug', 'epkb_enable_advanced_search_debug', 'epkb_show_logs', 'epkb_reset_logs' ) ) ) {
			new EPKB_Debug_Controller();
			return;
		} else if ( in_array( $action, array( 'epkb_get_wizard_template', 'epkb_apply_wizard_changes', 'epkb_wizard_update_order_view', 'epkb_apply_setup_wizard_changes', 'epkb_report_admin_error' ) ) ) {
			new EPKB_KB_Wizard_Cntrl();
			return;
		} else if ( in_array( $action, array( EPKB_Need_Help_Features::FEATURES_TAB_VISITED_ACTION ) ) ) {
			new EPKB_Need_Help_Features();
			return;
		} else if ( in_array( $action, array( 'epkb_wpml_enable', 'epkb_preload_fonts','epkb_disable_openai', 'epkb_load_resource_links_icons', 'epkb_load_general_typography', 'epkb_save_access_control', 'epkb_apply_settings_changes' ) ) ) {
			new EPKB_KB_Config_Controller();
			return;
		} else if ( in_array( $action, array( 'epkb_reset_sequence', 'epkb_show_sequence' ) ) ) {
			new EPKB_Reset();
			return;
		} else if ( in_array( $action, array( 'epkb_create_kb_demo_data' ) ) ) {
			new EPKB_Controller();
			return;
		} else if ( in_array( $action, array( 'epkb_save_faq', 'epkb_get_faq', 'epkb_delete_faq', 'epkb_save_faq_group', 'epkb_delete_faq_group' ) ) ) {
			new EPKB_FAQs_Ctrl();
			return;
		}
		
		if ( $action == 'add-tag' ) {
			new EPKB_KB_Config_Category();
			return;
		}

		if ( $action == 'epkb_dismiss_ongoing_notice' ) {
			new EPKB_Admin_Notices( true );
			return;
		}

		if ( $action == 'epkb_editor_error' ) {
			new EPKB_Editor_Controller();
			return;
		}

		if ( $action == 'epkb_deactivate_feedback' ) {
			new EPKB_Deactivate_Feedback();
			return;
		}

		if ( in_array( $action, array( 'epkb_load_articles_list', 'epkb_convert_kb_content' ) )  ) {
			new EPKB_Convert_Ctrl();
			return;
		}

		if ( $action == 'epkb_update_the_content_flag' ) {
			new EPKB_Articles_Setup();
			return;
		}

		if ( $action == 'epkb_delete_all_kb_data' ) {
			new EPKB_Delete_KB();
			return;
		}

		if ( in_array( $action, [ 'epkb_ai_request', 'epkb_ai_feedback' ] ) ) {
			new EPKB_AI_Help_Sidebar_Ctrl();
			return;
		}

		if ( $action == 'epkb_count_article_view' ) {
			new EPKB_Article_Count_Cntrl();
			return;
		}

        if ( in_array( $action, array('epkb_visual_helper_update_switch_settings', 'epkb_visual_helper_switch_template') ) ) {
            new EPKB_Visual_Helper();
            return;
        }
	}

	/**
	 * Setup up classes when on ADMIN pages
	 */
	public function setup_backend_classes() {
		global $pagenow;

		$is_kb_request = EPKB_KB_Handler::is_kb_request();
		$request_page = empty($_REQUEST['page']) ? '' : EPKB_Utilities::request_key( 'page' );
		$admin_pages = [ 'post.php', 'edit.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

		// show KB notice and AI Help Sidebar on our pages or when potential KB Main Page is being edited
		if ( $is_kb_request && in_array( $pagenow, $admin_pages ) ) {
			new EPKB_Admin_Notices();
			new EPKB_AI_Help_Sidebar();
		}

		// article new page
		if ( $is_kb_request && $pagenow == 'post-new.php' ) {
			add_action( 'admin_enqueue_scripts', 'epkb_load_admin_article_page_styles' );
		}

		// article edit page - include scripts to show categories box
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( $pagenow == 'post.php' && ! empty( $_REQUEST['post'] ) && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {
			$kb_post_type = get_post_type( sanitize_text_field( wp_unslash( $_REQUEST['post'] ) ) );
			if ( EPKB_KB_Handler::is_kb_post_type( $kb_post_type ) ) {
				new EPKB_AI_Help_Sidebar();
				add_action( 'admin_enqueue_scripts', 'epkb_load_admin_article_page_styles' );
			}
		}

		// include our admin scripts on our admin pages (submenus of KB menu) but not on Edit/Add page due to blocks etc.
		if ( $is_kb_request && $pagenow != 'post-new.php' && $pagenow != 'post.php' ) {

			// KB Configuration Page
			if ( $request_page == 'epkb-kb-configuration' ) {

				// Setup Wizard
				if ( isset( $_GET['setup-wizard-on'] ) ) {
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_kb_setup_wizard_script' );

				// Usual KB Configuration page
				} else {
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_kb_wizards_script' );
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
				}

			// KB Admin Pages (not config)
			} else {
				add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
			}
		}

		// on Category page show category icon selection feature
		if ( $is_kb_request && ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' ) ) {
			new EPKB_KB_Config_Category();
		}

		// admin core classes
		require_once self::$plugin_dir . 'includes/admin/admin-menu.php';
		require_once self::$plugin_dir . 'includes/admin/admin-functions.php';

		if ( ! empty( $pagenow ) && in_array( $pagenow, [ 'plugins.php', 'plugins-network.php' ] ) ) {
			new EPKB_Deactivate_Feedback();
		}

		// setup article views counter hooks
		new EPKB_Article_Count_Handler();
	}

	/**
	/**
	 * Loads the plugin language files from ./languages directory.
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'echo-knowledge-base', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

    private function is_kb_plugin_active_for_network( $plugin ) {
        if ( ! is_multisite() ) {
            return false;
        }

        $plugins = get_site_option( 'active_sitewide_plugins' );
        if ( isset( $plugins[ $plugin ] ) ) {
            return true;
        }

        return false;
    }
}

/**
 * Returns the single instance of this class
 * @return Echo_Knowledge_Base - this class instance
 */
function epkb_get_instance() {
	return Echo_Knowledge_Base::instance();
}
epkb_get_instance();

endif; // end class_exists() check
