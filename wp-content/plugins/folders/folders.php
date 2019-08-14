<?php
/**
 * Plugin Name: Folders
 * Description: Arrange media, pages, custom post types and posts into folders
 * Version: 2.1.5
 * Author: Premio
 * Author URI: https://premio.io/downloads/folders/
 */

defined('ABSPATH') or die('Nope, not accessing this');

define('WCP_FOLDERS_PLUGIN_FILE', __FILE__ );
define('WCP_FOLDERS_PLUGIN_BASE', plugin_basename(WCP_FOLDERS_PLUGIN_FILE ) );
define('WCP_FOLDER', 'folders');
define('WCP_FOLDER_VAR', 'folders_settings');
define("WCP_DS", DIRECTORY_SEPARATOR);
define('WCP_FOLDER_URL',plugin_dir_url(__FILE__));
define('WCP_FOLDER_VERSION',"2.1.5");

include_once plugin_dir_path(__FILE__)."includes/folders.class.php";
register_activation_hook( __FILE__, array( 'WCP_Folders', 'activate' ) );
WCP_Folders::get_instance();