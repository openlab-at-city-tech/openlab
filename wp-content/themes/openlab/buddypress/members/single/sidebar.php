<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
if (!$dud = bp_displayed_user_domain()) {
    $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}
?>

<?php if (is_user_logged_in() && openlab_is_my_profile()) : ?>

    <h2 class="sidebar-title">My OpenLab</h2>

    <div id="item-buttons" class="mol-menu sidebar-block">

        <ul class="sidebar-nav">

            <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?>" class="mol-profile my-profile"><a href="<?php echo $dud ?>">My Profile</a></li>

            <li class="sq-bullet <?php if (bp_is_user_settings()) : ?>selected-page<?php endif ?>" class="mol-settings my-settings"><a href="<?php echo $dud . bp_get_settings_slug() ?>/">My Settings</a></li>

            <li class="sq-bullet <?php if (is_page('my-courses') || openlab_is_create_group('course')) : ?>selected-page<?php endif ?>" class="mol-courses my-courses"><a href="<?php echo bp_get_root_domain() ?>/my-courses/">My Courses</a></li>

            <li class="sq-bullet <?php if (is_page('my-projects') || openlab_is_create_group('project')) : ?>selected-page<?php endif ?>" class="mol-projects my-projects"><a href="<?php echo bp_get_root_domain() ?>/my-projects/">My Projects</a></li>

            <li class="sq-bullet <?php if (is_page('my-clubs') || openlab_is_create_group('club')) : ?>selected-page<?php endif ?>" class="mol-clubs my-clubs"><a href="<?php echo bp_get_root_domain() ?>/my-clubs/">My Clubs</a></li>

            <?php /* Get a friend request count */ ?>
            <?php if (bp_is_active('friends')) : ?>
                <?php
                $request_ids = friends_get_friendship_request_user_ids(bp_loggedin_user_id());
                $request_count = intval(count((array) $request_ids));
                ?>

                <li class="sq-bullet <?php if (bp_is_user_friends()) : ?>selected-page<?php endif ?>" class="mol-friends my-friends"><a href="<?php echo $dud . bp_get_friends_slug() ?>/">My Friends <?php echo openlab_get_menu_count_mup($request_count); ?></a></li>
            <?php endif; ?>

            <?php /* Get an unread message count */ ?>
            <?php if (bp_is_active('messages')) : ?>
        <?php $message_count = bp_get_total_unread_messages_count() ?>

                <li class="sq-bullet <?php if (bp_is_user_messages()) : ?>selected-page<?php endif ?>" class="mol-messages my-messages"><a href="<?php echo $dud . bp_get_messages_slug() ?>/inbox/">My Messages <?php echo openlab_get_menu_count_mup($message_count); ?></a></li>
            <?php endif; ?>

            <?php /* Get an invitation count */ ?>
            <?php if (bp_is_active('groups')) : ?>
                <?php
                $invites = groups_get_invites_for_user();
                $invite_count = isset($invites['total']) ? (int) $invites['total'] : 0;
                ?>

                <li class="sq-bullet <?php if (bp_is_user_groups() && bp_is_current_action('invites')) : ?>selected-page<?php endif ?>" class="mol-invites my-invites"><a href="<?php echo $dud . bp_get_groups_slug() ?>/invites/">My Invitations <?php echo openlab_get_menu_count_mup($invite_count); ?></a></li>
    <?php endif ?>

        </ul>

    </div>

<?php else : ?>

    <h2 class="sidebar-title">People</h2>

    <div id="item-buttons" class="mol-menu sidebar-block">

        <ul class="sidebar-nav">

            <li class="sq-bullet <?php if (bp_is_user_activity()) : ?>selected-page<?php endif ?>" class="mol-profile"><a href="<?php echo $dud ?>/">Profile</a></li>

    <?php /* Current page highlighting requires the GET param */ ?>
    <?php $current_group_view = isset($_GET['type']) ? $_GET['type'] : ''; ?>

            <li class="sq-bullet <?php if (bp_is_user_groups() && 'course' == $current_group_view) : ?>selected-page<?php endif ?>" class="mol-courses"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=course">Courses</a></li>

            <li class="sq-bullet <?php if (bp_is_user_groups() && 'project' == $current_group_view) : ?>selected-page<?php endif ?>" class="mol-projects"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=project">Projects</a></li>

            <li class="sq-bullet <?php if (bp_is_user_groups() && 'club' == $current_group_view) : ?>selected-page<?php endif ?>" class="mol-club"><a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=club">Clubs</a></li>

            <li class="sq-bullet <?php if (bp_is_user_friends()) : ?>selected-page<?php endif ?>" class="mol-friends"><a href="<?php echo $dud . bp_get_friends_slug() ?>/">Friends</a></li>

        </ul>

    </div>

<?php endif ?>

<?php /* Portfolio links */ ?>

<?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>

    <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
    <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id() ?>

    <div class="sidebar-widget mol-menu" id="portfolio-sidebar-widget">
        <h2 class="sidebar-header">
    <?php openlab_portfolio_label('user_id=' . $displayed_user_id . '&case=upper') ?> Site
        </h2>

        <div class="sidebar-block">

            <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">

                <li class="portfolio-profile-link bold">
                    <span class="fa fa-external-link"></span><a class="bold" href="<?php openlab_user_portfolio_profile_url() ?>">Profile</a>
                </li>

                <li class="portfolio-site-link">
                    <a href="<?php openlab_user_portfolio_url() ?>">Site</a>
                    <?php if (openlab_is_my_profile() && openlab_user_portfolio_site_is_local()) : ?>
                        | <a class="portfolio-dashboard-link" href="<?php openlab_user_portfolio_url() ?>/wp-admin">Dashboard</a>
    <?php endif ?>
                </li>

            </ul>
        </div>
    </div>

<?php elseif (openlab_is_my_profile() && !bp_is_group_create()) : ?>
    <?php /* Don't show the 'Create a Portfolio' link during group (ie Portfolio) creation */ ?>
    <div class="sidebar-widget" id="portfolio-sidebar-widget">
        <h2 class="sidebar-header">
            <div class="sidebar-block">
                <a href="<?php openlab_portfolio_creation_url() ?>">+ Create <?php openlab_portfolio_label('leading_a=1&case=upper&user_id=' . $displayed_user_id) ?></a>
            </div>
        </h2>
    </div>

<?php endif ?>

<?php /* End portfolio links */ ?>

<?php /* Recent Account Activity / Recent Friend Activity */ ?>

<?php
// The 'user_id' param is the displayed user, but displayed user is not set on
// my-* pages
$user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();

$activity_args = array(
    'user_id' => $user_id,
    'per_page' => openlab_is_my_profile() ? 4 : 2, // Legacy. Not sure why
    'scope' => bp_is_user_friends() ? 'friends' : '',
    'show_hidden' => openlab_is_my_profile(),
    'primary_id' => false,
);
?>

<?php if (bp_is_user_friends()) : ?>
    <h2 class="sidebar-header">Recent Friend Activity</h2>
<?php else : ?>
    <h2 class="sidebar-header">Recent Account Activity</h2>
<?php endif ?>

<?php if (bp_has_activities($activity_args)) : ?>
    <div class="sidebar-block">
        <ul id="activity-stream" class="activity-list item-list inline-element-list sidebar-sublinks">
            <div class="row">
    <?php while (bp_activities()) : bp_the_activity(); ?>

                    <div class="activity-avatar col-sm-7">
                        <a href="<?php bp_activity_user_link() ?>">
                            <?php echo openlab_activity_user_avatar(); ?>
                        </a>
                    </div>

                    <div class="activity-content col-sm-17">

                        <div class="activity-header">
                        <?php echo openlab_get_custom_activity_action(); ?>
                        </div>

                        <?php if (bp_activity_has_content()) : ?>
                            <div class="activity-inner semibold">
                            <?php bp_activity_content_body() ?>
                            </div>
        <?php endif; ?>

        <?php do_action('bp_activity_entry_content') ?>

                    </div>
                    <hr style="clear:both" />

    <?php endwhile; ?>
            </div>
        </ul>
    </div>
<?php else : ?>
    <div class="sidebar-block">
        <ul id="activity-stream" class="activity-list item-list">
            <div>
                <div id="message" class="info">
                    <p><?php _e('No recent activity.', 'buddypress') ?></p>
                </div>
            </div>
        </ul>
    </div>
<?php endif; ?>
