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

			echo '<h2 class="sidebar-title hidden-xs">Learn More</h2>';
            echo '<div class="sidebar-block sidebar-block-learnmore hidden-xs">';
			openlab_learnmore_sidebar();
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
		$group_site_settings = openlab_get_group_site_settings( bp_get_group_id() );

		$widget_wrapper_class = 'sidebar-widget sidebar-widget-wrapper';
		if ( ! empty( $group_site_settings['site_url'] ) && $group_site_settings['is_visible'] ) {
			$widget_wrapper_class .= ' group-has-site';
		}

            ?>
            <div class="<?php echo esc_attr( $widget_wrapper_class ); ?>" id="portfolio-sidebar-widget">
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
            </div>
            <?php
        endwhile;
    endif;
}

/**
 * 'Learn More' sidebar for About pages.
 */
function openlab_learnmore_sidebar() {
	?>
	<div class="learn-more-sidebar">
		<p>Get updates on the <a href="https://openlab.citytech.cuny.edu/openroad/">Open Road</a></p>
		<p>Follow our student bloggers on <a href="https://openlab.citytech.cuny.edu/the-buzz/">The Buzz</a></p>
		<p>Join the conversation about <a href="https://openlab.citytech.cuny.edu/openpedagogyopenlab/">Open Pedagogy</a></p>
	</div>
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

                <li class="sq-bullet <?php if ( bp_is_user_settings() || bp_is_user_change_avatar() || bp_is_user_profile_edit() ) : ?>selected-page<?php endif ?> mol-settings my-settings"><a href="<?php echo $dud . bp_get_settings_slug() ?>/">My Settings</a></li>

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

                <li class="sq-bullet mol-dashboard my-dashboard"><a href="<?php echo $primary_site_url . '/wp-admin/my-sites.php' ?>">My Dashboard <span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></li>

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

                <li class="portfolio-site-link bold">
                    <a class="bold no-deco" href="<?php openlab_user_portfolio_url() ?>">
                        <?php echo (is_user_logged_in() && openlab_is_my_profile() ? 'My ' : 'Visit '); ?>
                        <?php openlab_portfolio_label('user_id=' . $displayed_user_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span>
                    </a>
                </li>

                <li class="portfolio-dashboard-link">
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

/**
 * Get the current filter value out of GET parameters.
 */
function openlab_get_current_filter( $param ) {
	$value = '';

	switch ( $param ) {
		case 'school' :
			if ( isset( $_GET['school'] ) ) {
				$value_raw           = wp_unslash( $_GET['school'] );
				$schools_and_offices = array_merge( openlab_get_school_list(), openlab_get_office_list() );

				if ( 'school_all' === $value_raw ) {
					$value = 'school_all';
				} elseif ( isset( $schools_and_offices[ $value_raw ] ) ) {
					$value = $value_raw;
				}
			}
		break;

		case 'group_types' :
			$value = isset( $_GET['group_types'] ) ? wp_unslash( $_GET['group_types'] ) : [];
		break;

		case 'member_type' :
			if ( isset( $_GET['member_type'] ) ) {
				$valid_user_types = openlab_valid_user_types();

				$user_types    = array_merge( array_keys( $valid_user_types ), [ 'user_type_all' ] );
				$user_type_raw = $_GET['member_type'];
				if ( in_array( $user_type_raw, $user_types ) ) {
					$value = $user_type_raw;
				}
			}
		break;

		case 'order' :
			$whitelist = [ 'alphabetical', 'newest', 'active' ];
			$value     =  isset( $_GET['order'] ) && in_array( $_GET['order'], $whitelist, true ) ? $_GET['order'] : 'active';
		break;

		case 'open' :
			$value = ! empty( $_GET['is_open'] );
		break;

		case 'cloneable' :
			$value = ! empty( $_GET['is_cloneable'] );
		break;

		case 'badges' :
			$value = isset( $_GET['badges'] ) ? array_map( 'intval', $_GET['badges'] ) : [];
		break;

		case 'sort' :
			$valid = [ 'newest', 'alphabetical', 'active' ];
			if ( isset( $_GET['sort'] ) && in_array( $_GET['sort'], $valid, true ) ) {
				$value = $_GET['sort'];
			} else {
				$value = 'active';
			}
		break;

		case 'group-types' :
			$value = [];
			if ( ! empty( $_GET['group-types'] ) && is_array( $_GET['group-types'] ) ) {
				$value = array_filter(
					wp_unslash( $_GET['group-types'] ),
					function( $group_type ) {
						return in_array( $group_type, openlab_group_types(), true );
					}
				);
			}
		break;

		default :
			$value = isset( $_GET[ $param ] ) ? wp_unslash( $_GET[ $param ] ) : '';
		break;
	}

	return $value;
}
