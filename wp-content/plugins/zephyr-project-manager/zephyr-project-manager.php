<?php

/**
* @package ZephyrProjectManager
*
* Plugin Name:  Zephyr Project Manager
* Description:  A modern project manager for WordPress to keep track of all your projects from within WordPress.
* Plugin URI:   https://zephyr-one.com
* Version:      3.2.32
* Author:       Dylan James
* License:      GPLv2 or later
* Text Domain:  zephyr-project-manager
* Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) ) {
    die;
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

global $wpdb;
global $zpm_settings;
global $zpmMessages;

use Inc\Core\Task;
use Inc\Core\Tasks;
use Inc\Base\Activate;
use Inc\Base\Deactivate;
use Inc\Core\Utillities;
use Inc\Api\Emails;
use Inc\ZephyrProjectManager;
use Inc\Core\Controllers\MessageController;
use Inc\Zephyr;

define( 'ZPM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ZPM_PLUGIN', plugin_basename( __FILE__ ) );
define( 'ZPM_PROJECTS_TABLE', $wpdb->prefix . 'zpm_projects' );
define( 'ZPM_TASKS_TABLE', $wpdb->prefix . 'zpm_tasks' );
define( 'ZPM_MESSAGES_TABLE', $wpdb->prefix . 'zpm_messages' );
define( 'ZPM_CATEGORY_TABLE', $wpdb->prefix . 'zpm_categories' );
define( 'ZPM_ACTIVITY_TABLE', $wpdb->prefix . 'zpm_activity' );
define( 'ZEPHYR_PRO_LINK', 'https://zephyr-one.com/purchase-pro/' );

$zpmMessages = new MessageController();

function activate_project_manager_plugin($networkwide) {

    // Handle multisite
    if ( is_multisite() && $networkwide ) {
        global $wpdb;

         // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( (array) $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            Activate::activate();
            if ( function_exists('zephyr_project_manager_activate_pro') ) {
                zephyr_project_manager_activate_pro();
            }
            restore_current_blog();
        }
    } else { // Running on a single blog
        Activate::activate();
        if ( function_exists('zephyr_project_manager_activate_pro') ) {
            zephyr_project_manager_activate_pro();
        }
    }

}

register_activation_hook( __FILE__, 'activate_project_manager_plugin' );

function deactivate_project_manager_plugin() {
    Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_project_manager_plugin' );

if ( class_exists( 'Inc\\Init' ) ) {
    include( ZPM_PLUGIN_PATH . 'includes/functions.php' );
    Utillities::install_missing_columns();
    Inc\Init::register_services();
    zpm_add_scheduled_events();
    // $currentVersion = Zephyr::getPluginVersion();
    // $databaseVersion = get_option( 'zpm_database_version', 1 );
    // if (version_compare($currentVersion, $databaseVersion, '>')) {
    //     Activate::activate();
    // }
}

function zpm_plugin_init() {
    global $zpm_settings;

    $version = zpm_get_version();
    $db_version = get_option( 'zpm_db_version', '0' );
    if (version_compare($db_version, $version, '<')) {
        Activate::installTables();
        update_option( 'zpm_db_version', $version );
    }

    load_plugin_textdomain( 'zephyr-project-manager', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    Utillities::check_save_general_settings();
    $tasks = new Tasks();
    $zpm_settings = Utillities::general_settings();
}

add_action( 'plugins_loaded', 'zpm_plugin_init' );

add_filter( 'admin_body_class', 'zpm_body_classes' );

function zpm_body_classes( $classes ) {
    if (isZephyrPage()) {
        return "$classes zephyr-project-manager";
    }
    return $classes;
}

function zpm_get_timezone() {
    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }

    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }

    $timezone = new DateTimeZone( $tzstring );
    return $timezone;
}





