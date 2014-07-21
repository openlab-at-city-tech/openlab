<?php

function openlab_core_setup() {
    add_theme_support('post-thumbnails');
    global $content_width;
    register_nav_menus(array(
        'main' => __('Main Menu', 'openlab'),
        'aboutmenu' => __('About Menu', 'openlab'),
        'helpmenu' => __('Help Menu', 'openlab'),
        'helpmenusec' => __('Help Menu Secondary', 'openlab')
    ));
}

add_action('after_setup_theme', 'openlab_core_setup');

/* * creating a library to organize functions* */
require_once( STYLESHEETPATH . '/lib/course-clone.php' );
require_once( STYLESHEETPATH . '/lib/header-funcs.php' );
require_once( STYLESHEETPATH . '/lib/post-types.php' );
require_once( STYLESHEETPATH . '/lib/menus.php' );
require_once( STYLESHEETPATH . '/lib/content-processing.php' );
require_once( STYLESHEETPATH . '/lib/nav.php' );
require_once( STYLESHEETPATH . '/lib/breadcrumbs.php' );
require_once( STYLESHEETPATH . '/lib/group-funcs.php' );
require_once( STYLESHEETPATH . '/lib/ajax-funcs.php' );
require_once( STYLESHEETPATH . '/lib/help-funcs.php' );
require_once( STYLESHEETPATH . '/lib/member-funcs.php' );
require_once( STYLESHEETPATH . '/lib/page-funcs.php' );
require_once( STYLESHEETPATH . '/lib/admin-funcs.php' );
require_once( STYLESHEETPATH . '/lib/search-funcs.php' );
require_once( STYLESHEETPATH . '/lib/theme-hooks.php' );

function openlab_load_scripts() {
    /**
     * scripts, additional functionality
     */
    if (!is_admin()) {

        //need to turn less.js (local only) off for now until issues with comments in Bootstrap is resolved
        $local_off = false;

        //less for local dev
        //Local dev less debugging
        if ($local_off) {
            wp_register_style('main-styles', get_stylesheet_directory_uri() . '/less/style.less', array(), '20130604', 'all');
            wp_enqueue_style('main-styles');
        } else {
            wp_register_style('main-styles', get_stylesheet_uri(), array(), '20130604', 'all');
            wp_enqueue_style('main-styles');
        }


        if ($local_off) {
            wp_register_script('less-config-js', get_stylesheet_directory_uri() . '/js/less.config.js', array('jquery'));
            wp_enqueue_script('less-config-js');
            wp_register_script('less-js', get_stylesheet_directory_uri() . '/js/less-1.7.0.js', array('jquery'));
            wp_enqueue_script('less-js');
        }

        wp_register_script('bootstrap-js', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jquery'));
        wp_enqueue_script('bootstrap-js');
        wp_register_script('jcarousellite', get_bloginfo('stylesheet_directory') . '/js/jcarousellite.js');
        wp_enqueue_script('jcarousellite');
        wp_register_script('easyaccordion', get_bloginfo('stylesheet_directory') . '/js/easyaccordion.js');
        wp_enqueue_script('easyaccordion');
        wp_register_script('utility', get_bloginfo('stylesheet_directory') . '/js/utility.js');
        wp_enqueue_script('utility');
        wp_enqueue_script('dtheme-ajax-js', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/global.js', array('jquery'));
    }
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts');

//custom widgets for OpenLab
function cuny_widgets_init() {
    //add widget for Rotating Post Gallery Widget - will be placed on the homepage
    register_sidebar(array(
        'name' => __('Rotating Post Gallery Widdget', 'cuny'),
        'description' => __('This is the widget for holding the Rotating Post Gallery Widget', 'cuny'),
        'id' => 'pgw-gallery',
        'before_widget' => '<div id="pgw-gallery">',
        'after_widget' => '</div>',
    ));
    //add widget for the Featured Widget - will be placed on the homepage under "In the Spotlight"
    register_sidebar(array(
        'name' => __('Featured Widget', 'cuny'),
        'description' => __('This is the widget for holding the Featured Widget', 'cuny'),
        'id' => 'cac-featured',
        'before_widget' => '<div id="cac-featured">',
        'after_widget' => '</div>',
    ));
}

add_action('widgets_init', 'cuny_widgets_init');

function cuny_o_e_class($num) {
    return $num % 2 == 0 ? " even" : " odd";
}

function cuny_third_end_class($num) {
    return $num % 3 == 0 ? " last" : "";
}

/**
 * Modify the body class
 *
 * Invite New Members and Your Email Options fall under "Settings", so need
 * an appropriate body class.
 */
function openlab_group_admin_body_classes($classes) {
    if (!bp_is_group()) {
        return $classes;
    }

    if (in_array(bp_current_action(), array('invite-anyone', 'notifications'))) {
        $classes[] = 'group-admin';
    }

    return $classes;
}

add_filter('bp_get_the_body_class', 'openlab_group_admin_body_classes');

/**
 * Don't allow BuddyPress Docs to use its own theme compatibility layer
 */
add_filter('bp_docs_do_theme_compat', '__return_false');
