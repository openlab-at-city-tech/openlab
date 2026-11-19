<?php
/**
 * Plugin Name: Folders
 * Description: Organize your Media library, Pages, and Posts into folders. You can easily drag and drop items into directories and change the folders tree view.
 * Version: 3.3.1
 * Author: Premio
 * Author URI: https://premio.io/downloads/folders/
 * Text Domain: folders
 * Domain Path: /languages
 * License: GPLv3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'FOLDERS_VERSION', '3.3.1');
const FOLDERS_FILE = __FILE__;
define( 'FOLDERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FOLDERS_PLUGIN_BASE', plugin_basename( FOLDERS_FILE ) );
const FOLDERS_TEMPLATE_DIR = FOLDERS_PLUGIN_DIR . 'templates' . DIRECTORY_SEPARATOR;
define( 'FOLDERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
const FOLDERS_IMAGE_URL = FOLDERS_PLUGIN_URL . "assets/images/";
const IS_FOLDER_DEVELOPER_MODE = true;
if (!defined("WCP_DS")) {
    define("WCP_DS", DIRECTORY_SEPARATOR);
}
// Main Plugin Class
if ( ! class_exists( 'Folders' ) ) {
    /**
     * Main plugin bootstrap class.
     *
     * Singleton that registers the PSR-4 style autoloader for the `Folders\`
     * namespace, loads the plugin text domain and boots all plugin classes
     * through {@see \Folders\Loader::run()} on `init`.
     */
    class Folders {
        
        private static $instance = null;

        /**
         * Get the single shared instance of the plugin, creating it on first call.
         *
         * @return Folders Plugin instance.
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Register the class autoloader and the core WordPress hooks.
         */
        public function __construct() {
            // Hooks and initialization
            spl_autoload_register( array( $this, 'autoloader' ) );
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         * Autoload classes in the `Folders\` namespace from the `includes/` directory.
         *
         * Maps `Folders\Admin\Settings` to `includes/Admin/Settings.php`. Classes
         * outside the namespace are ignored so other autoloaders can handle them.
         *
         * @param string $class Fully qualified class name being requested.
         * @return void
         */
        public function autoloader( $class ) {
            if ( 0 !== strpos( $class, 'Folders\\' ) ) {
                return;
            }

            $class = substr( $class, strlen( 'Folders\\' ) );
            $class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
            $file  = FOLDERS_PLUGIN_DIR . 'includes/' . $class . '.php';

            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }

        /**
         * Load the plugin translations from the `languages/` directory.
         *
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'folders', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        /**
         * Boot the plugin by instantiating every class under `includes/`.
         *
         * Hooked to `init`.
         *
         * @return void
         */
        public function init() {
            \Folders\Loader::run();
        }
    }
}

// Initialize the plugin
if (!class_exists('folders_init')) {
    /**
     * Create the main plugin instance once all plugins are loaded.
     *
     * Hooked to `plugins_loaded`.
     *
     * @return void
     */
    function folders_init() {
        Folders::get_instance();
    }
    add_action( 'plugins_loaded', 'folders_init' );
}

/**
 * Plugin activation callback.
 *
 * Runs {@see \Folders\Admin\Activate::activate()} to set the post-activation
 * redirect flag and default options.
 *
 * @return void
 */
function folders_activate_plugin() {
    require_once FOLDERS_PLUGIN_DIR . 'includes/Admin/Activate.php';
    $activator = new \Folders\Admin\Activate();
    $activator->activate();
}
register_activation_hook(__FILE__, 'folders_activate_plugin');

/**
 * Plugin deactivation callback.
 *
 * Runs {@see \Folders\Admin\Deactivate::deactivate()}. Folders and settings are
 * kept on deactivation; `uninstall.php` removes them when the plugin is deleted
 * and the "remove data on uninstall" setting is enabled.
 *
 * @return void
 */
function folders_deactivate_plugin() {
    require_once FOLDERS_PLUGIN_DIR . 'includes/Admin/Deactivate.php';
    $deactivator = new \Folders\Admin\Deactivate();
    $deactivator->deactivate();
}
register_deactivation_hook(__FILE__, 'folders_deactivate_plugin');


if (!function_exists("folders_sanitize_text")) {
    /**
     * Read and sanitize a text value from the current request.
     *
     * Strips slashes, HTML tags and null bytes, then encodes single and double
     * quotes as HTML entities.
     *
     * @param string $key  Request parameter name.
     * @param string $type Source to read from: "post" for $_POST, anything else for $_GET.
     * @return string Sanitized value, or an empty string when the key is missing.
     */
    function folders_sanitize_text($key, $type = "post")
    {
        if ($type == "post") {
            $string = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : "";
        } else {
            $string = isset($_GET[$key]) ? sanitize_text_field($_GET[$key]) : "";
        }
        $string = stripslashes($string);
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}