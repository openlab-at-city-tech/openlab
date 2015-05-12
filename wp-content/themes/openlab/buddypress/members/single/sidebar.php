<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
if (!$dud = bp_displayed_user_domain()) {
    $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}
?>

<?php /* Portfolio links */ ?>

<div class="sidebar-widget mol-menu" id="portfolio-sidebar-widget">

    <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
        <h2 class="sidebar-header top-sidebar-header hidden-xs">My OpenLab</h2>
    <?php else: ?>
        <h2 class="sidebar-header top-sidebar-header hidden-xs">Member Profile</h2>
    <?php endif; ?>
        
<?php if (openlab_user_has_portfolio(bp_displayed_user_id()) && (!openlab_group_is_hidden(openlab_get_user_portfolio_id()) || openlab_is_my_profile() || groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )) : ?>
        
    <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
        <h2 class="sidebar-header top-sidebar-header visible-xs">My Portfolio</h2>
    <?php else: ?>
        <h2 class="sidebar-header top-sidebar-header visible-xs">Member Portfolio</h2>
    <?php endif; ?>
    
    <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
    <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id() ?>

        <div class="sidebar-block">

            <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">

                <li class="portfolio-profile-link bold">
                    <a class="bold no-deco" href="<?php openlab_user_portfolio_url() ?>"><?php openlab_portfolio_label('user_id=' . $displayed_user_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right cyan-circle"></span></a>
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
    <div class="sidebar-block" id="portfolio-sidebar-widget">
        <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
            <li>
                <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>
                <a class="bold" href="<?php openlab_portfolio_creation_url() ?>">+ Create <?php openlab_portfolio_label('leading_a=1&case=upper&user_id=' . $displayed_user_id) ?></a>
            </li>
        </ul>
    </div>

<?php endif; ?>
    
    <?php openlab_member_sidebar_menu(); ?>
    
    </div>

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
    <h2 class="sidebar-header">Recent Activity</h2>
<?php endif ?>
    
<div class="activity-wrapper">
<?php if (bp_has_activities($activity_args)) : ?>
        <div id="activity-stream" class="activity-list item-list inline-element-list sidebar-sublinks">
            <?php while (bp_activities()) : bp_the_activity(); ?>
                <div class="sidebar-block activity-block">
                    <div class="row activity-row">
                        <div class="activity-avatar col-sm-8">
                            <a href="<?php bp_activity_user_link() ?>">
                                <?php echo openlab_activity_user_avatar(); ?>
                            </a>
                        </div>

                        <div class="activity-content col-sm-16">

                            <div class="activity-header">
                                <?php echo openlab_get_custom_activity_action(); ?>
                            </div>

                            <?php if (bp_activity_has_content()) : ?>
                                <div class="activity-inner semibold hyphenate">
                                    <?php bp_activity_content_body() ?>
                                </div>
                            <?php endif; ?>

                            <?php do_action('bp_activity_entry_content') ?>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
<?php else : ?>
        <div id="activity-stream" class="activity-list item-list">
            <div class="sidebar-block">
                <div class="row activity-row">
                    <div id="message" class="info col-sm-24">
                        <p><?php _e('No recent activity.', 'buddypress') ?></p>
                    </div>
                </div>
            </div>
        </div>
<?php endif; ?>
</div>
