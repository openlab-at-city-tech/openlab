<?php

/**
 * Theme based hooks
 */

function openlab_custom_the_content($content){
    global $post;
    
    if($post->post_name == 'contact-us' && $post->post_type == 'page'){
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

add_filter('the_content','openlab_custom_the_content');

function openlab_header_bar() {
    ?>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <h1 id="title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
            </div>
            <div class="navbar-collapse"> 
                <?php
                //this adds the main menu, controlled through the WP menu interface
                $args = array(
                    'theme_location' => 'main',
                    'container' => false,
                    'menu_class' => 'nav navbar-nav',
                );

                wp_nav_menu($args);
                ?>
                <div class="navbar-right search">
                    <?php openlab_site_wide_bp_search(); ?>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

add_action('bp_before_header', 'openlab_header_bar', 10);

function openlab_custom_menu_items($items, $menu) {

    if (is_user_logged_in()) {
        $opl_link = '<li ' . (bp_is_my_profile() ? 'class="current-menu-item"' : '') . '>';
        $opl_link .= '<a href="' . bp_loggedin_user_domain() . '">My OpenLab</a>';
        $opl_link .= '</li>';
    }

    return $items . $opl_link;
}

add_filter('wp_nav_menu_items', 'openlab_custom_menu_items', 10, 2);

function openlab_activity_log_text($text) {
    $text = '%s';

    return $text;
}

add_filter('bp_core_time_since_ago_text', 'openlab_activity_log_text');

function openlab_form_classes($classes) {

    $classes[] = 'field-group';

    return $classes;
}

add_filter('bp_field_css_classes', 'openlab_form_classes');

function openlab_hide_docs_native_menu($menu_template) {

    return false;
}

add_filter('bp_docs_header_template', 'openlab_hide_docs_native_menu');

/**
 * For right now going to create custom docs templates until more hooks are available
 * @param type $path
 * @param type $template
 * @return type
 */
function openlab_custom_docs_templates($path, $template) {

    if ($template->current_view == 'list') {
        $path = bp_locate_template('groups/single/docs/docs-loop.php', false);
    } else if ($template->current_view == 'create' || $template->current_view == 'edit') {
        $path = bp_locate_template('groups/single/docs/edit-doc.php', false);
    } else if ($template->current_view == 'single') {
        $path = bp_locate_template('groups/single/docs/single-doc.php', false);
    }
    
    return $path;
}

add_filter('bp_docs_template', 'openlab_custom_docs_templates', 10, 2);

function openlab_plugin_custom_header_elements() {
    global $post, $bp;

    if (bp_docs_is_existing_doc()) :
        ?>
        <?php // echo '<pre>'.print_r($post,true).'</pre>'; ?>
        <?php // echo '<pre>'.print_r(bp_docs_get_group_doc_permalink(),true).'</pre>'; ?>
        <div class="doc-tabs">
            <ul>
                <li<?php if ('single' == bp_docs_current_view()) : ?> class="current"<?php endif ?>>
                    <a href="<?php echo bp_docs_get_group_doc_permalink() ?>"><?php _e('Read', 'bp-docs') ?></a>
                </li>

                <?php if (bp_docs_current_user_can('edit')) : ?>
                    <li<?php if ('edit' == bp_docs_current_view()) : ?> class="current"<?php endif ?>>
                        <a href="<?php echo bp_docs_get_group_doc_permalink() . '/' . BP_DOCS_EDIT_SLUG ?>"><?php _e('Edit', 'bp-docs') ?></a>
                    </li>
                <?php endif ?>

                <?php do_action('bp_docs_header_tabs') ?>
            </ul>
        </div>

        <?php
    endif;
}

add_action('bp_before_group_plugin_template', 'openlab_plugin_custom_header_elements');

function openlab_custom_form_classes($classes){
    return 'form-panel '.$classes;
}

add_filter('wpcf7_form_class_attr','openlab_custom_form_classes');
