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

function openlab_header_bar() {
    ?>
    <nav class="navbar navbar-default" role="navigation">
        <div class="header-mobile-wrapper visible-xs">
            <div class="container-fluid">
                <div class="navbar-header clearfix">
                    <h1 id="title" class="pull-left"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
                    <div class="pull-right search search-trigger">
                        <div class="search-trigger-wrapper">
                            <span class="fa fa-search search-trigger" data-mode="mobile"></span>
                        </div>
                    </div>
                </div>
                <div class="search search-form row">
                    <?php openlab_site_wide_bp_search('mobile'); ?>
                </div>
            </div>
        </div>
        <div class="main-nav-wrapper">
            <div class="container-fluid">
                <div class="navbar-header hidden-xs">
                    <h1 id="title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
                </div>
                <div class="navbar-collapse collapse" id="main-nav">
                    <?php
                    //this adds the main menu, controlled through the WP menu interface
                    $args = array(
                        'theme_location' => 'main',
                        'container' => false,
                        'menu_class' => 'nav navbar-nav',
                    );

                    wp_nav_menu($args);
                    ?>
                    <div class="navbar-right search hidden-xs">
                        <?php openlab_site_wide_bp_search('desktop'); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

add_action('bp_before_header', 'openlab_header_bar', 10);

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
