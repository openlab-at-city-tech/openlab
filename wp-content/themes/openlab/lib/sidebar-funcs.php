<?php

/**
 * Sidebar based functionality
 */
function openlab_bp_sidebar($type, $mobile_dropdown = false) {

    $pull_classes = ($type == 'groups' ? ' pull-right' : '');
    $pull_classes .= ($mobile_dropdown ? ' mobile-dropdown' : '');

    echo '<div id="sidebar" class="sidebar col-sm-6 col-xs-24' . $pull_classes . ' type-' . $type . '">';

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
                'menu_class' => 'sidebar-nav'
            );
            echo '<h2 class="sidebar-title hidden-xs">About</h2>';
            echo '<div class="sidebar-block hidden-xs">';
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
 * Mobile sidebar - for when a piece of the sidebar needs to appear above the content in the mobile space
 * @param type $type
 */
function openlab_bp_mobile_sidebar($type) {

    switch ($type) {
        case 'actions':
            echo '<div id="sidebar-mobile" class="sidebar group-single-item mobile-dropdown clearfix">';
            openlab_group_sidebar(true);
            echo '</div>';
            break;
        case 'members':
            echo '<div id="sidebar-mobile" class="sidebar group-single-item mobile-dropdown clearfix">';
            openlab_member_sidebar_menu(true);
            echo '</div>';
            break;
        case 'about':
            echo '<div id="sidebar-mobile" class="sidebar clearfix mobile-dropdown">';
            $args = array(
                'theme_location' => 'aboutmenu',
                'container' => 'div',
                'container_id' => 'about-menu',
                'menu_class' => 'sidebar-nav'
            );
            echo '<div class="sidebar-block">';
            wp_nav_menu($args);
            echo '</div>';
            echo '</div>';
            break;
        case 'help':
            echo '<div id="sidebar-mobile" class="sidebar clearfix mobile-dropdown">';
            echo '<div class="sidebar-block">';

            $args = array(
                'theme_location' => 'helpmenu',
                'container' => 'div',
                'container_id' => 'help-menu',
                'menu_class' => 'sidebar-nav',
            );
            wp_nav_menu($args);

            echo '</div>';
            echo '</div>';
            break;
    }
}

/**
 * Output the sidebar content for a single group
 */
function openlab_group_sidebar($mobile = false) {

    $classes = ($mobile ? 'visible-xs' : 'hidden-xs');

    if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
            ?>
            <div class="sidebar-widget" id="portfolio-sidebar-widget">
                <?php if (!$mobile): ?>
                    <h2 class="sidebar-header group-single top-sidebar-header">
                        <?php echo ucwords(groups_get_groupmeta(bp_get_group_id(), "wds_group_type")) . ' Materials'; ?>
                    </h2>
                <?php openlab_bp_group_site_pages(); ?>
                <?php endif; ?>
                <div id="item-buttons" class="profile-nav sidebar-block <?php echo $classes; ?>">
                    <ul class="sidebar-nav">
                        <?php bp_get_options_nav(); ?>

                        <?php
                        if ($mobile):

                            echo openlab_get_group_profile_mobile_anchor_links();

                        endif;
                        ?>

                    </ul>
                </div><!-- #item-buttons -->
                <?php if (!$mobile): ?>
                    <?php do_action('bp_group_options_nav') ?>
                <?php endif; ?>
            </div>
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

/**
 * Member pages sidebar - modularized for easier parsing of mobile menus
 * @param type $mobile
 */
function openlab_member_sidebar_menu($mobile = false) {

    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }

    if ($mobile) {
        $classes = 'visible-xs';
    } else {
        $classes = 'hidden-xs';
    }

    if (is_user_logged_in() && openlab_is_my_profile()) :
        ?>

        <div id="item-buttons" class="mol-menu sidebar-block <?php echo $classes; ?>">

            <ul class="sidebar-nav">

                <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?> mol-profile my-profile"><a href="<?php echo $dud ?>">My Profile</a></li>

                <li class="sq-bullet <?php if (bp_is_user_settings()) : ?>selected-page<?php endif ?> mol-settings my-settings"><a href="<?php echo $dud . bp_get_settings_slug() ?>/">My Settings</a></li>
                
                <?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>
                
                <li id="portfolios-groups-li" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-widget" id="portfolios">My <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></a></li>
                
                <?php endif; ?>

                <li class="sq-bullet <?php if (is_page('my-courses') || openlab_is_create_group('course')) : ?>selected-page<?php endif ?> mol-courses my-courses"><a href="<?php echo bp_get_root_domain() ?>/my-courses/">My Courses</a></li>

                <li class="sq-bullet <?php if (is_page('my-projects') || openlab_is_create_group('project')) : ?>selected-page<?php endif ?> mol-projects my-projects"><a href="<?php echo bp_get_root_domain() ?>/my-projects/">My Projects</a></li>

                <li class="sq-bullet <?php if (is_page('my-clubs') || openlab_is_create_group('club')) : ?>selected-page<?php endif ?> mol-clubs my-clubs"><a href="<?php echo bp_get_root_domain() ?>/my-clubs/">My Clubs</a></li>

                <?php /* Get a friend request count */ ?>
                <?php if (bp_is_active('friends')) : ?>
                    <?php
                    $request_ids = friends_get_friendship_request_user_ids(bp_loggedin_user_id());
                    $request_count = intval(count((array) $request_ids));
                    ?>

                    <li class="sq-bullet <?php if (bp_is_user_friends()) : ?>selected-page<?php endif ?> mol-friends my-friends"><a href="<?php echo $dud . bp_get_friends_slug() ?>/">My Friends <?php echo openlab_get_menu_count_mup($request_count); ?></a></li>
                <?php endif; ?>

                <?php /* Get an unread message count */ ?>
                <?php if (bp_is_active('messages')) : ?>
                    <?php $message_count = bp_get_total_unread_messages_count() ?>

                    <li class="sq-bullet <?php if (bp_is_user_messages()) : ?>selected-page<?php endif ?> mol-messages my-messages"><a href="<?php echo $dud . bp_get_messages_slug() ?>/inbox/">My Messages <?php echo openlab_get_menu_count_mup($message_count); ?></a></li>
                <?php endif; ?>

                <?php /* Get an invitation count */ ?>
                <?php if (bp_is_active('groups')) : ?>
                    <?php
                    $invites = groups_get_invites_for_user();
                    $invite_count = isset($invites['total']) ? (int) $invites['total'] : 0;
                    ?>

                    <li class="sq-bullet <?php if (bp_is_current_action('invites') || bp_is_current_action('sent-invites') || bp_is_current_action('invite-new-members')) : ?>selected-page<?php endif ?> mol-invites my-invites"><a href="<?php echo $dud . bp_get_groups_slug() ?>/invites/">My Invitations <?php echo openlab_get_menu_count_mup($invite_count); ?></a></li>
                <?php endif ?>

                <?php
                // My Dashboard points to the my-sites.php Dashboard panel for this user. However,
                // this panel only works if looking at a site where the user has Dashboard-level
                // permissions. So we have to find a valid site for the logged in user.
                $primary_site_id = get_user_meta(bp_loggedin_user_id(), 'primary_blog', true);
                $primary_site_url = set_url_scheme(get_blog_option($primary_site_id, 'siteurl'));
                ?>

                <li class="sq-bullet mol-dashboard my-dashboard"><a href="<?php echo $primary_site_url . '/wp-admin/my-sites.php' ?>">My Dashboard <span class="fa fa-chevron-circle-right cyan-circle"></span></a></li>

            </ul>

        </div>

    <?php else : ?>

        <div id="item-buttons" class="mol-menu sidebar-block <?php echo $classes; ?>">

            <ul class="sidebar-nav">

                <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?> mol-profile"><a href="<?php echo $dud ?>/">Profile</a></li>
                
                <?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>
                
                <li id="portfolios-groups-li" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-widget" id="portfolios"><?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></a></li>
                
                <?php endif; ?>

                <?php /* Current page highlighting requires the GET param */ ?>
                <?php $current_group_view = isset($_GET['type']) ? $_GET['type'] : ''; ?>

                <li class="sq-bullet <?php if (bp_is_user_groups() && 'course' == $current_group_view) : ?>selected-page<?php endif ?> mol-courses"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=course">Courses</a></li>

                <li class="sq-bullet <?php if (bp_is_user_groups() && 'project' == $current_group_view) : ?>selected-page<?php endif ?> mol-projects"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=project">Projects</a></li>

                <li class="sq-bullet <?php if (bp_is_user_groups() && 'club' == $current_group_view) : ?>selected-page<?php endif ?> mol-club"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=club">Clubs</a></li>

                <li class="sq-bullet <?php if (bp_is_user_friends()) : ?>selected-page<?php endif ?> mol-friends"><a href="<?php echo $dud . bp_get_friends_slug() ?>/">Friends</a></li>

            </ul>

        </div>

    <?php
    endif;
}
