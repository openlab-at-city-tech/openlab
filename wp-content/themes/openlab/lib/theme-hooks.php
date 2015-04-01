<?php

/**
 * Theme based hooks
 */
function openlab_custom_the_content($content) {
    global $post;

    if ($post->post_name == 'contact-us' && ($post->post_type == 'page' || $post->post_type == 'help')) {
        $form = do_shortcode('[contact-form-7 id="447" title="Contact Form 1"]');
        $content = <<<HTML
                <div class="panel panel-default">
                    <div class="panel-heading">Contact Form</div>
                    <div class="panel-body">
                        {$content}
                        {$form}
                    </div>
                </div>
HTML;
    }

    return $content;
}

add_filter('the_content', 'openlab_custom_the_content');

/**
 * OpenLab main menu markup
 * @param type $location
 */
function openlab_main_menu($location = 'header') {
    ?>
    <nav class="navbar navbar-default navbar-location-<?= $location ?>" role="navigation">
        <div class="header-mobile-wrapper visible-xs">
            <div class="container-fluid">
                <div class="navbar-header clearfix">
                    <h1 class="menu-title pull-left"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
                    <div class="pull-right search search-trigger">
                        <div class="search-trigger-wrapper">
                            <span class="fa fa-search search-trigger" data-mode="mobile" data-location="<?= $location ?>"></span>
                        </div>
                    </div>
                </div>
                <div class="search search-form row">
                    <?php openlab_site_wide_bp_search('mobile',$location); ?>
                </div>
            </div>
        </div>
        <div class="main-nav-wrapper">
            <div class="container-fluid">
                <div class="navbar-header hidden-xs">
                    <h1 class="menu-title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
                </div>
                <div class="navbar-collapse collapse" id="main-nav-<?= $location ?>">
                    <?php
                    //this adds the main menu, controlled through the WP menu interface
                    $args = array(
                        'theme_location' => 'main',
                        'container' => false,
                        'menu_class' => 'nav navbar-nav',
                        'menu_id' => 'menu-main-menu-' . $location,
                    );

                    wp_nav_menu($args);
                    ?>
                    <div class="navbar-right search hidden-xs">
                        <?php openlab_site_wide_bp_search('desktop',$location); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Main menu in header
 */
function openlab_header_bar() {
    openlab_main_menu('header');
}

/*
 * Main menu in footer
 */
function openlab_footer_bar() {
    openlab_main_menu('footer');
}

add_action('bp_before_header', 'openlab_header_bar', 10);
add_action('bp_before_footer', 'openlab_footer_bar', 6);

function openlab_custom_menu_items($items, $menu) {
    global $post, $bp;

    if ($menu->theme_location == 'main') {

        $opl_link = '';

        $classes = '';

        if (is_user_logged_in()) {
            $opl_link = '<li ' . (bp_is_my_profile() || $bp->current_action == 'create' || ($post->post_name == 'my-courses' || $post->post_name == 'my-projects' || $post->post_name == 'my-clubs') ? 'class="current-menu-item"' : '') . '>';
            $opl_link .= '<a href="' . bp_loggedin_user_domain() . '">My OpenLab</a>';
            $opl_link .= '</li>';
        }

        return $items . $opl_link;
    } else if ($menu->theme_location == 'aboutmenu') {

        $items = str_replace('Privacy Policy', '<i class="fa fa-external-link no-margin no-margin-left"></i>Privacy Policy', $items);

        return $items;
    } else {
        return $items;
    }
}

add_filter('wp_nav_menu_items', 'openlab_custom_menu_items', 10, 2);

function openlab_form_classes($classes) {

    $classes[] = 'field-group';

    return $classes;
}

add_filter('bp_field_css_classes', 'openlab_form_classes');

function openlab_custom_form_classes($classes) {
    return 'form-panel ' . $classes;
}

add_filter('wpcf7_form_class_attr', 'openlab_custom_form_classes');

function openlab_message_thread_excerpt_custom_size($message) {
    global $messages_template;

    $message = strip_tags(bp_create_excerpt($messages_template->thread->last_message_content, 55));

    return $message;
}

add_filter('bp_get_message_thread_excerpt', 'openlab_message_thread_excerpt_custom_size');

/**
 * Adds divs that can be used for client-side detection of bootstrap breakpoints
 */
function openlab_add_breakpoint_detection() {
    ?>

    <div class="device-xs visible-xs"></div>
    <div class="device-sm visible-sm"></div>
    <div class="device-md visible-md"></div>
    <div class="device-lg visible-lg"></div>

    <?php
}

add_action('wp_footer', 'openlab_add_breakpoint_detection');
