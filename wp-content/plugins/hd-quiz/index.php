<?php
/*
 * Plugin Name: HD Quiz
 * Description: HD Quiz allows you to easily add an unlimited amount of Quizzes to your site.
 * Plugin URI: https://harmonicdesign.ca/hd-quiz/
 * Author: Harmonic Design
 * Author URI: https://harmonicdesign.ca
 * Version: 1.7.0
 */

// check if mbsting exists

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('HDQ_PLUGIN_VERSION')) {
    define('HDQ_PLUGIN_VERSION', '1.7.0');
}

// custom quiz image sizes
// add_image_size('hd_qu_size', 600, 400, true); // featured image - removed since version 1.5.0
add_image_size('hd_qu_size2', 400, 400, true); // image-as-answer

/* Include the basic required files
------------------------------------------------------- */
require dirname(__FILE__) . '/includes/post_type.php'; // custom post types
require dirname(__FILE__) . '/includes/meta.php'; // custom meta
require dirname(__FILE__) . '/includes/functions.php'; // general functions

// function to check if HD Quiz is active
function hdq_exists()
{
    return;
}

/* Enqueue admin scripts to relevant pages
------------------------------------------------------- */
function hdq_add_admin_scripts($hook)
{
    global $post;
    // Only enqueue if we're on the
    // add/edit questions, quizzes, or settings page
    if ($hook == 'post-new.php' || $hook == 'post.php' || $hook == 'term.php' || $hook == 'edit-tags.php' || $hook == "hd-quiz_page_hdq_options" || $hook == "toplevel_page_hdq_quizzes" || $hook == "toplevel_page_hdq_addons") {
        function hdq_print_scripts()
        {
            wp_enqueue_style(
                'hdq_admin_style',
                plugin_dir_url(__FILE__) . './includes/css/hdq_admin_style.css?v=' . HDQ_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'hdq_admin_script',
                plugins_url('./includes/js/hdq_admin.js?v=' . HDQ_PLUGIN_VERSION, __FILE__),
                array('jquery', 'jquery-ui-sortable'),
                '1.0',
                true
            );
        }
        if ($hook == "hd-quiz_page_hdq_options" || $hook == "term.php" || $hook == 'edit-tags.php' || $hook == "toplevel_page_hdq_quizzes" || $hook == "toplevel_page_hdq_addons") {
            hdq_print_scripts();
        } else {
            if ($post->post_type === 'post_type_questionna') {
                hdq_print_scripts();
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'hdq_add_admin_scripts', 10, 1);

/* Add shortcode
------------------------------------------------------- */
function hdq_add_shortcode($atts)
{
    // Attributes
    extract(
        shortcode_atts(
            array(
                'quiz' => '',
            ),
            $atts
        )
    );

    // Code
    ob_start();
    include plugin_dir_path(__FILE__) . './includes/template.php';
    return ob_get_clean();
}
add_shortcode('HDquiz', 'hdq_add_shortcode');

/* Disable Canonical redirection for paginated quizzes
------------------------------------------------------- */
function hdq_disable_redirect_canonical($redirect_url)
{
    global $post;
    if (has_shortcode($post->post_content, 'HDquiz')) {
        $redirect_url = false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'hdq_disable_redirect_canonical');

/* Create HD Quiz Settings page
------------------------------------------------------- */
function hdq_create_settings_page()
{
    if (hdq_user_permission()) {
        function hdq_register_quizzes_page()
        {
            add_menu_page("HD Quiz", "HD Quiz", 'publish_posts', "hdq_quizzes", 'hdq_register_quizzes_page_callback', "dashicons-clipboard", 5);
			add_menu_page('HD Quiz Addons', 'HDQ Addons', 'edit_posts', 'hdq_addons' , 'hdq_register_addons_page_callbak', "", 99);
			add_submenu_page( 'hdq_quizzes', 'Addons', 'Addons', 'manage_options', 'admin.php?page=hdq_addons');
			remove_menu_page( 'hdq_addons' );
        }
        add_action('admin_menu', 'hdq_register_quizzes_page');

        function hdq_register_settings_page()
        {
            add_submenu_page('hdq_quizzes', 'Quizzes', 'Quizzes', 'publish_posts', 'hdq_quizzes', 'hdq_register_quizzes_page_callback');
            add_submenu_page('hdq_quizzes', 'HD Quiz About', 'About / Options', 'publish_posts', 'hdq_options', 'hdq_register_settings_page_callback');
        }
        add_action('admin_menu', 'hdq_register_settings_page', 11);
    }

    $hdq_version = sanitize_text_field(get_option("HDQ_PLUGIN_VERSION"));
    if (HDQ_PLUGIN_VERSION != $hdq_version) {
        update_option("HDQ_PLUGIN_VERSION", HDQ_PLUGIN_VERSION);
        function hdq_show_upgrade_message()
        {
            ?>
			<div class="notice notice-success is-dismissible">
				<p><strong>HD QUIZ</strong>. Thank you for upgrading. If you experience any issues at all, please don't hesitate to <a href = "https://wordpress.org/support/plugin/hd-quiz" target = "_blank">reach out for support</a>! I'm always glad to help when I can.</p>
			</div>
			<?php
}
        add_action('admin_notices', 'hdq_show_upgrade_message');
    }
}
add_action('init', 'hdq_create_settings_page');

function hdq_register_quizzes_page_callback()
{
    require dirname(__FILE__) . '/includes/hdq_quizzes.php';
}

function hdq_register_settings_page_callback()
{
    require dirname(__FILE__) . '/includes/hdq_about_options.php';
}

function hdq_register_addons_page_callbak()
{
	require dirname(__FILE__) . '/includes/addons.php';
}
