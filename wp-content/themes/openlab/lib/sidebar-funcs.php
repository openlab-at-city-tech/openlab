<?php

/**
 * Sidebar based functionality
 */
function openlab_bp_sidebar($type) {

    echo '<div id="sidebar" class="sidebar col-sm-6">';

    switch ($type) {
        case 'actions':
            cuny_buddypress_group_actions();
            break;
        case 'members':
            bp_get_template_part('members/single/sidebar');
            break;
        case 'register':
            openlab_buddypress_register_actions();
            break;
        case 'groups':
            get_sidebar('group-archive');
            break;
        case 'about':
            $args = array(
                'theme_location' => 'aboutmenu',
                'container' => 'div',
                'container_id' => 'about-menu',
                'menu_class' => 'sidbar-nav'
            );
            echo '<h2 class="sidebar-title">About</h2>';
            wp_nav_menu($args);
            break;
        case 'help':
            get_sidebar('help');
            break;
        default:
            get_sidebar();
    }

    echo '</div>';
}
