<?php
if (!defined('HELLO_AGENCY_VERSION')) {
    // Replace the version number of the theme on each release.
    define('HELLO_AGENCY_VERSION', wp_get_theme()->get('Version'));
}
define('HELLO_AGENCY_DEBUG', defined('WP_DEBUG') && WP_DEBUG === true);
define('HELLO_AGENCY_DIR', trailingslashit(get_template_directory()));
define('HELLO_AGENCY_URL', trailingslashit(get_template_directory_uri()));

if (!function_exists('hello_agency_support')) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * @since walker_fse 1.0.0
     *
     * @return void
     */
    function hello_agency_support()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');
        // Add support for block styles.
        add_theme_support('wp-block-styles');
        add_theme_support('post-thumbnails');
        // Enqueue editor styles.
        add_editor_style('style.css');
        // Removing default patterns.
        remove_theme_support('core-block-patterns');
    }

endif;
add_action('after_setup_theme', 'hello_agency_support');

/*----------------------------------------------------------------------------------
Enqueue Styles
-----------------------------------------------------------------------------------*/
if (!function_exists('hello_agency_styles')) :
    function hello_agency_styles()
    {
        // registering style for theme
        wp_enqueue_style('hello-agency-style', get_stylesheet_uri(), array(), HELLO_AGENCY_VERSION);
        wp_enqueue_style('hello-agency-blocks-style', get_template_directory_uri() . '/assets/css/blocks.css');
        if (is_rtl()) {
            wp_enqueue_style('hello-agency-rtl-css', get_template_directory_uri() . '/assets/css/rtl.css', 'rtl_css');
        }
        // registering js for theme
        wp_enqueue_script('jquery');
        wp_enqueue_script('hello-agency-custom-scripts', get_template_directory_uri() . '/assets/js/hello-agency-scripts.js', array(), HELLO_AGENCY_VERSION, true);
    }
endif;

add_action('wp_enqueue_scripts', 'hello_agency_styles');

/**
 * Enqueue scripts for admin area
 */
function hello_agency_admin_style()
{
    $hello_notice_current_screen = get_current_screen();
    if (!empty($_GET['page']) && 'about-hello-agency' === $_GET['page'] || $hello_notice_current_screen->id === 'themes' || $hello_notice_current_screen->id === 'dashboard') {
        wp_enqueue_style('hello-agency-admin-style', get_template_directory_uri() . '/assets/css/admin-style.css', array(), HELLO_AGENCY_VERSION, 'all');
        wp_enqueue_script('hello-agency-welcome-scripts', get_template_directory_uri() . '/assets/js/hello-agency-welcome-scripts.js', array('jquery'), HELLO_AGENCY_VERSION, true);
        wp_localize_script('hello-agency-welcome-scripts', 'hello_agency_localize', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hello_agency_nonce'),
            'redirect_url' => admin_url('themes.php?page=_cozy_companions')
        ));
    }
}
add_action('admin_enqueue_scripts', 'hello_agency_admin_style');

/**
 * Enqueue assets scripts for both backend and frontend
 */
function hello_agency_block_assets()
{
    wp_enqueue_style('hello-agency-blocks-style', get_template_directory_uri() . '/assets/css/blocks.css');
}
add_action('enqueue_block_assets', 'hello_agency_block_assets');

/**
 * Load core file.
 */
require_once get_template_directory() . '/inc/core/init.php';

/**
 * Load welcome page file.
 */
require_once get_template_directory() . '/inc/admin/welcome-notice.php';

if (!function_exists('hello_agency_excerpt_more_postfix')) {
    function hello_agency_excerpt_more_postfix($more)
    {
        if (is_admin()) {
            return $more;
        }
        return '...';
    }
    add_filter('excerpt_more', 'hello_agency_excerpt_more_postfix');
}
if (!function_exists('hello_agency_excerpt_limit')) {
    function hello_agency_excerpt_limit($length)
    {
        if (is_admin()) {
            return $length;
        }
        return 29;
    }
    add_filter('excerpt_length', 'hello_agency_excerpt_limit');
}
