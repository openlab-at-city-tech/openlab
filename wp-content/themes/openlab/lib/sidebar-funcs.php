<?php

/**
 * Sidebar based functionality
 */
function openlab_bp_sidebar($type) {

    echo '<div id="sidebar" class="sidebar col-sm-6">';

    switch ($type) {
        case 'actions':
            openlab_group_sidebar();
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
            echo '<div class="sidebar-block">';
            wp_nav_menu($args);
            echo '</div>';
            break;
        case 'help':
            get_sidebar('help');
            break;
        default:
            get_sidebar();
    }

    echo '</div>';
}

/**
 * Output the sidebar content for a single group
 */
function openlab_group_sidebar() {
    if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
            ?>
            <div class="group-nav sidebar-widget">
                <div id="item-buttons">
                    <h2 class="sidebar-header"><?php echo openlab_get_group_type_label('case=upper') ?></h2>
                    <div class="sidebar-block">
                        <ul>
                            <?php bp_get_options_nav(); ?>
                        </ul>
                    </div>
                </div><!-- #item-buttons -->
            </div>
            <?php do_action('bp_group_options_nav') ?>
            <?php
        endwhile;
    endif;
}

/**
 * 	Registration page sidebar
 *
 */
function openlab_buddypress_register_actions() {
    global $bp;
    ?>
    <h2 class="sidebar-title">&nbsp;</h2>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <?php
}
