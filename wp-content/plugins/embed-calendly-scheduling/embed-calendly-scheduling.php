<?php

/**
 * Plugin Name: Embed Calendly
 * Description: Easy and simple way to embed Calendly scheduling pages on WordPress.
 * Author: Embed Calendly, Shycoder
 * Author URI: https://embedcalendly.com/
 * Version: 3.7
 * License: GPLv2 or later
 */

defined('ABSPATH') or die('No script kiddies please.');
defined('EMCS_DIR')                             || define('EMCS_DIR', plugin_dir_path(__FILE__));
defined('EMCS_URL')                             || define('EMCS_URL', plugin_dir_url(__FILE__));
defined('EMCS_INCLUDES')                        || define('EMCS_INCLUDES', EMCS_DIR . 'includes/');
defined('EMCS_EVENT_TYPES')                     || define('EMCS_EVENT_TYPES', EMCS_INCLUDES . 'event-types/');
defined('EMCS_CUSTOMIZER_TEMPLATES')            || define('EMCS_CUSTOMIZER_TEMPLATES', EMCS_INCLUDES . 'widget-customizer/template-parts/');
defined('EMCS_CIPHER')                          || define('EMCS_CIPHER', 'aes-256-cbc');

include_once(EMCS_INCLUDES . 'admin.php');
include_once(EMCS_INCLUDES . 'shortcode.php');
include_once(EMCS_EVENT_TYPES . 'event-types-dashboard.php');
include_once(EMCS_INCLUDES . 'widget-customizer/customizer.php');
include_once(EMCS_INCLUDES . 'promotions.php');

register_activation_hook(__FILE__, 'EMCS_Admin::on_activation');

add_action('admin_enqueue_scripts', 'emcs_admin_scripts');

function emcs_admin_scripts()
{
    if (isset($_REQUEST['page'])) {
        if (
            $_REQUEST['page'] == 'emcs-customizer' || $_REQUEST['page'] == 'emcs-settings'
            || $_REQUEST['page'] == 'emcs-event-types'
        ) {
            wp_enqueue_style('emcs_admin_css', EMCS_URL . 'assets/css/admin.css');
            wp_enqueue_style('emcs_util_css', EMCS_URL . 'assets/css/util.css');
            wp_enqueue_script('emcs_customizer_js',  EMCS_URL . 'assets/js/widget-customizer.js', [], false, true);
        }
    }

    wp_register_style('emcs_style', EMCS_URL . 'assets/css/style.css');
}

add_action('wp_enqueue_scripts', 'emcs_calendly_scripts');
add_action('admin_enqueue_scripts', 'emcs_calendly_scripts');

function emcs_calendly_scripts()
{
    wp_register_style('emcs_calendly_css', EMCS_URL . 'assets/css/widget.css');
    wp_register_script('emcs_calendly_js',  EMCS_URL . 'assets/js/widget.js', [], false, true);
}

add_shortcode('calendly', array('EMCS_Shortcode', 'register_shortcode'));
add_action('admin_menu', 'EMCS_Event_Types_Dashboard::init');
add_action('admin_menu', 'EMCS_Customizer::init');
include_once(EMCS_INCLUDES . 'settings.php');
add_action('in_admin_header', 'EMCS_Admin::clear_unwanted_notices', 1000);
add_action('admin_init', 'EMCS_Promotions::init');
add_action('admin_menu', 'EMCS_Promotions::init_menu');
