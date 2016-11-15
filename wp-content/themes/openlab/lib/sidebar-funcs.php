<?php

/**
 * Sidebar based functionality
 */
function openlab_bp_sidebar($type, $mobile_dropdown = false, $extra_classes = '') {

    $pull_classes = ($type == 'groups' ? ' pull-right' : '');
    $pull_classes .= ($mobile_dropdown ? ' mobile-dropdown' : '');

    echo '<div id="sidebar" class="sidebar col-sm-6 col-xs-24' . $pull_classes . ' type-' . $type . $extra_classes . '"><div class="sidebar-wrapper">';

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
                'menu_class' => 'sidebar-nav clearfix'
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

    echo '</div></div>';
}

/**
 * Mobile sidebar - for when a piece of the sidebar needs to appear above the content in the mobile space
 * @param type $type
 */
function openlab_bp_mobile_sidebar($type) {

    switch ($type) {
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
                'container_id' => 'about-mobile-menu',
                'menu_class' => 'sidebar-nav clearfix'
            );
            echo '<div class="sidebar-block">';
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

    if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
            ?>
            <div class="sidebar-widget sidebar-widget-wrapper" id="portfolio-sidebar-widget">
                <h2 class="sidebar-header group-single top-sidebar-header">
                    <?php echo ucwords(groups_get_groupmeta(bp_get_group_id(), "wds_group_type")) . ' Materials'; ?>
                </h2>
                <div class="wrapper-block">
                    <?php openlab_bp_group_site_pages(); ?>
                </div>
                <div id="sidebar-menu-wrapper" class="sidebar-menu-wrapper wrapper-block">
                    <div id="item-buttons" class="profile-nav sidebar-block clearfix">
                        <ul class="sidebar-nav clearfix">
                            <?php bp_get_options_nav(); ?>
                            <?php echo openlab_get_group_profile_mobile_anchor_links(); ?>
                        </ul>
                    </div><!-- #item-buttons -->
                </div>
                <?php do_action('bp_group_options_nav') ?>
                <?php echo openlab_get_group_activity_events_feed(); ?>
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

        <div id="item-buttons<?php echo ($mobile ? '-mobile' : '') ?>" class="mol-menu sidebar-block <?php echo $classes; ?>">

            <ul class="sidebar-nav clearfix">

                <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?> mol-profile my-profile"><a href="<?php echo $dud ?>">My Profile</a></li>

                <li class="sq-bullet <?php if (bp_is_user_settings()) : ?>selected-page<?php endif ?> mol-settings my-settings"><a href="<?php echo $dud . bp_get_settings_slug() ?>/">My Settings</a></li>

                <?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>

                    <li id="portfolios-groups-li<?php echo ($mobile ? '-mobile' : '') ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ($mobile ? '-mobile' : '') ?>">My <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></a></li>

                <?php else: ?>

                    <li id="portfolios-groups-li<?php echo ($mobile ? '-mobile' : '') ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ($mobile ? '-mobile' : '') ?>">Create <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></a></li>

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

                <li class="sq-bullet mol-dashboard my-dashboard"><a href="<?php echo $primary_site_url . '/wp-admin/my-sites.php' ?>">My Dashboard <span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span></a></li>

            </ul>

        </div>

    <?php else : ?>

        <div id="item-buttons<?php echo ($mobile ? '-mobile' : '') ?>" class="mol-menu sidebar-block <?php echo $classes; ?>">

            <ul class="sidebar-nav clearfix">

                <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?> mol-profile"><a href="<?php echo $dud ?>/">Profile</a></li>

                <?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>

                    <li id="portfolios-groups-li<?php echo ($mobile ? '-mobile' : '') ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ($mobile ? '-mobile' : '') ?>"><?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></a></li>

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

/**
 * Member pages sidebar blocks (portfolio link) - modularized for easier parsing of mobile menus
 */
function openlab_members_sidebar_blocks($mobile_hide = false) {

    $block_classes = '';

    if ($mobile_hide) {
        $block_classes = ' hidden-xs';
    }

    if (is_user_logged_in() && openlab_is_my_profile()):
        ?>
        <h2 class="sidebar-header top-sidebar-header hidden-xs">My OpenLab</h2>
    <?php else: ?>
        <h2 class="sidebar-header top-sidebar-header hidden-xs">Member Profile</h2>
    <?php endif; ?>

    <?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>

        <?php if (!$mobile_hide): ?>
            <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">My <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></h2>
            <?php else: ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">Member <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></h2>
            <?php endif; ?>
        <?php endif; ?>

        <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
        <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id() ?>

        <div class="sidebar-block<?php echo $block_classes ?>">

            <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">

                <li class="portfolio-profile-link bold">
                    <a class="bold no-deco" href="<?php openlab_user_portfolio_url() ?>">
                        <?php echo (is_user_logged_in() && openlab_is_my_profile() ? 'My ' : 'Visit '); ?>
                        <?php openlab_portfolio_label('user_id=' . $displayed_user_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span>
                    </a>
                </li>

                <li class="portfolio-site-link">
                    <a href="<?php openlab_user_portfolio_profile_url() ?>">Profile</a>
                    <?php if (openlab_is_my_profile() && openlab_user_portfolio_site_is_local()) : ?>
                        | <a class="portfolio-dashboard-link" href="<?php openlab_user_portfolio_url() ?>/wp-admin">Dashboard</a>
                    <?php endif ?>
                </li>

            </ul>
        </div>

    <?php elseif (openlab_is_my_profile() && !bp_is_group_create()) : ?>
        <?php /* Don't show the 'Create a Portfolio' link during group (ie Portfolio) creation */ ?>
        <div class="sidebar-widget" id="portfolio-sidebar-widget">

            <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">My <?php echo (xprofile_get_field_data('Account Type', bp_displayed_user_id()) == 'Student' ? 'ePortfolio' : 'Portfolio') ?></h2>
            <?php endif; ?>

            <div class="sidebar-block<?php echo $block_classes ?>">
                <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                    <li>
                        <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>
                        <a class="bold" href="<?php openlab_portfolio_creation_url() ?>">+ Create <?php openlab_portfolio_label('leading_a=1&case=upper&user_id=' . $displayed_user_id) ?></a>
                    </li>
                </ul>
            </div>
        </div>

        <?php
    endif;
}
