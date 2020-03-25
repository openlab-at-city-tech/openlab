<?php
/*
 * Plugin Name: HD Quiz - Save Results Light
 * Description: Addon for HD Quiz to save quiz results - Light version
 * Plugin URI: https://harmonicdesign.ca/addons/save-results-light/
 * Author: Harmonic Design
 * Author URI: https://harmonicdesign.ca
 * Version: 0.1
 */

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

/* Automatically deactivate if HD Quiz is not active
------------------------------------------------------- */
function hdq_q_light_check_hd_quiz_active()
{
    if (function_exists('is_plugin_active')) {
        if (!is_plugin_active("hd-quiz/index.php")) {
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }
}
add_action('init', 'hdq_q_light_check_hd_quiz_active');

/* Include the basic required files
------------------------------------------------------- */
require dirname(__FILE__) . '/includes/functions.php'; // general functions

/* Enqueue admin scripts to relevant pages
------------------------------------------------------- */
function hdq_a_light_add_admin_scripts($hook)
{
    global $post;
    if ($hook == "hd-quiz_page_hdq_results") {
        function hdq_a_light_print_scripts()
        {
            wp_enqueue_style(
                'hdq_admin_style',
                plugin_dir_url(__FILE__) . './includes/css/hdq_a_light_admin_style.css?v=' . HDQ_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'hdq_admin_script',
                plugins_url('./includes/js/hdq_a_light_admin.js?v=' . HDQ_PLUGIN_VERSION, __FILE__),
                array('jquery'),
                '1.0',
                true
            );
        }
        hdq_a_light_print_scripts();
    }
}
add_action('admin_enqueue_scripts', 'hdq_a_light_add_admin_scripts', 10, 1);

/* Create HD Quiz Results light Settings page
------------------------------------------------------- */
function hdq_a_light_create_settings_page()
{
    function hdq_a_light_register_settings_page()
    {
        add_submenu_page('hdq_quizzes', 'Results', 'Results', 'publish_posts', 'hdq_results', 'hdq_a_light_register_quizzes_page_callback');
    }
    add_action('admin_menu', 'hdq_a_light_register_settings_page', 11);
}
add_action('init', 'hdq_a_light_create_settings_page');

function hdq_a_light_register_quizzes_page_callback()
{
    require dirname(__FILE__) . '/includes/results.php';
}
