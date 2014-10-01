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
require_once( STYLESHEETPATH . '/lib/media-funcs.php' );
require_once( STYLESHEETPATH . '/lib/group-funcs.php' );
require_once( STYLESHEETPATH . '/lib/ajax-funcs.php' );
require_once( STYLESHEETPATH . '/lib/help-funcs.php' );
require_once( STYLESHEETPATH . '/lib/member-funcs.php' );
require_once( STYLESHEETPATH . '/lib/page-funcs.php' );
require_once( STYLESHEETPATH . '/lib/sidebar-funcs.php' );
require_once( STYLESHEETPATH . '/lib/admin-funcs.php' );
require_once( STYLESHEETPATH . '/lib/search-funcs.php' );
require_once( STYLESHEETPATH . '/lib/invite-funcs.php' );
require_once( STYLESHEETPATH . '/lib/email-funcs.php' );
require_once( STYLESHEETPATH . '/lib/files-funcs.php' );
require_once( STYLESHEETPATH . '/lib/theme-hooks.php' );

function openlab_load_scripts() {
    /**
     * scripts, additional functionality
     */
    if (!is_admin()) {


        //google fonts
        wp_register_style('google-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic', array(), '2014', 'all');
        wp_enqueue_style('google-open-sans');
        wp_register_style('font-awesome', get_stylesheet_directory_uri() . '/css/font-awesome.min.css', array(), '20130604', 'all');
        wp_enqueue_style('font-awesome');

        wp_register_style('camera-js-styles', get_stylesheet_directory_uri() . '/css/camera.css', array(), '20130604', 'all');
        wp_enqueue_style('camera-js-styles');

        //less compliation via js so we can check styles in firebug via fireless - local dev only
        if (IS_LOCAL_ENV) {
            wp_register_script('less-config-js', get_stylesheet_directory_uri() . '/js/less.config.js', array('jquery'));
            wp_enqueue_script('less-config-js');
            wp_register_script('less-js', get_stylesheet_directory_uri() . '/js/less-1.7.4.js', array('jquery'));
            wp_enqueue_script('less-js');
        }

        wp_register_script('bootstrap-js', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jquery'));
        wp_enqueue_script('bootstrap-js');
        wp_register_script('jcarousellite', get_bloginfo('stylesheet_directory') . '/js/jcarousellite.js');
        wp_enqueue_script('jcarousellite');
        wp_register_script('easyaccordion', get_bloginfo('stylesheet_directory') . '/js/easyaccordion.js');
        wp_enqueue_script('easyaccordion');
        wp_register_script('easing-js', get_stylesheet_directory_uri() . '/js/jquery.easing.1.3.js', array('jquery'));
        wp_enqueue_script('easing-js');
        wp_register_script('mobile-custom-js', get_stylesheet_directory_uri() . '/js/jquery.mobile.customized.min.js', array('jquery'));
        wp_enqueue_script('mobile-custom-js');
        wp_register_script('camera-js', get_stylesheet_directory_uri() . '/js/camera.min.js', array('jquery'));
        wp_enqueue_script('camera-js');
        wp_register_script('select-js', get_stylesheet_directory_uri() . '/js/jquery.customSelect.min.js', array('jquery'));
        wp_enqueue_script('select-js');
        wp_register_script('utility', get_bloginfo('stylesheet_directory') . '/js/utility.js');
        wp_enqueue_script('utility');
        
    }
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts');

/**
 * Giving the main stylesheet the highest priority among stylesheets to make sure it loads last
 */
function openlab_load_scripts_high_priority() {
    //less compliation via js so we can check styles in firebug via fireless - local dev only
    //@to-do: way to enqueue as last item?
    if (IS_LOCAL_ENV) {
        wp_register_style('main-styles', get_stylesheet_directory_uri() . '/style.less', array(), '20130604', 'all');
        wp_enqueue_style('main-styles');
    } else {
        wp_register_style('main-styles', get_stylesheet_uri(), array(), '20130604', 'all');
        wp_enqueue_style('main-styles');
    }
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts_high_priority', 999);

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

//for less js - local dev only
function enqueue_less_styles($tag, $handle) {
    global $wp_styles;
    $match_pattern = '/\.less$/U';
    if (preg_match($match_pattern, $wp_styles->registered[$handle]->src)) {
        $handle = $wp_styles->registered[$handle]->handle;
        $media = $wp_styles->registered[$handle]->args;
        $href = $wp_styles->registered[$handle]->src;
        $rel = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
        $title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr($wp_styles->registered[$handle]->extra['title']) . "'" : '';

        $tag = "<link rel='stylesheet/less' $title href='$href' type='text/css' media='$media' />";
    }
    return $tag;
}

add_filter('style_loader_tag', 'enqueue_less_styles', 5, 2);
