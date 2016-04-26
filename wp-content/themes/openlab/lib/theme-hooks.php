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
    <nav class="navbar navbar-default oplb-bs navbar-location-<?php echo $location ?>" role="navigation">
        <?php openlab_sitewide_header($location); ?>
        <div class="main-nav-wrapper">
            <div class="container-fluid">
                <div class="navbar-header hidden-xs">
                    <header class="menu-title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></header>
                </div>
                <div class="navbar-collapse collapse" id="main-nav-<?php echo $location ?>">
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
                        <?php openlab_mu_site_wide_bp_search('desktop', $location); ?>
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
            $class = '';
            if (bp_is_my_profile() || bp_is_current_action('create') || is_page('my-courses') || is_page('my-projects') || is_page('my-clubs')) {
                $class = 'class="current-menu-item"';
            }
            $opl_link = '<li ' . $class . '>';
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

function openlab_loader_class() {
    ?>

    <script type="text/javascript">
        document.documentElement.className = 'page-loading';
    </script>

    <?php
}

add_action('wp_head', 'openlab_loader_class', 999);
