<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
if (!$dud = bp_displayed_user_domain()) {
    $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}
?>

<?php /* Portfolio links */ ?>

 <?php if ( (bp_is_user_activity() || !bp_current_component()) && !(strpos($post->post_name,'my-') > -1)) :
        $mobile_hide = true;
        $id = 'portfolio-sidebar-widget';
     else:
         $mobile_hide = false;
         $id = 'portfolio-sidebar-inline-widget';
     endif; ?>

<div class="sidebar-widget mol-menu" id="<?php echo $id ?>">

    <?php openlab_members_sidebar_blocks($mobile_hide); ?>
    <?php openlab_member_sidebar_menu(); ?>

</div>

<?php if ( openlab_is_my_profile() && class_exists( '\OpenLab\Favorites\App' ) ) : ?>

	<?php
	$user_favorites = OpenLab\Favorites\Favorite\Query::get_results(
		[
			'user_id' => bp_loggedin_user_id(),
		]
	);
	?>

	<?php if ( $user_favorites ) : ?>
		<h2 class="sidebar-header">My Favorites</h2>

		<div class="sidebar-block sidebar-block-my-favorites">
			<ul class="sidebar-sublinks inline-element-list">
				<?php foreach ( $user_favorites as $user_favorite ) : ?>
					<li><a href="<?php echo esc_attr( $user_favorite->get_group_url() ); ?>"><?php echo esc_html( $user_favorite->get_group_name() ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php /* End portfolio links */ ?>

<?php /* Recent Account Activity / Recent Friend Activity */ ?>

<?php
// The 'user_id' param is the displayed user, but displayed user is not set on
// my-* pages
$user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();

$activity_args = array(
    'user_id' => $user_id,
    'max' => openlab_is_my_profile() ? 4 : 2, // Legacy. Not sure why
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
                        <div class="activity-avatar col-sm-8 col-xs-7">
                            <a href="<?php bp_activity_user_link() ?>">
                                <?php echo openlab_activity_user_avatar(); ?>
                            </a>
                        </div>

                        <div class="activity-content overflow-hidden col-sm-16 col-xs-17">

                            <div class="activity-header">
                                <?php echo openlab_get_custom_activity_action(); ?>
                            </div>

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
