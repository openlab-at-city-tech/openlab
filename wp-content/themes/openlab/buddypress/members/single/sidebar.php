<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
if (!$dud = bp_displayed_user_domain()) {
    $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}

$is_my_groups_post = ( get_queried_object() instanceof WP_Post ) && 0 === strpos( get_queried_object()->post_name, 'my-' );

// Portfolio links.

if ( ( bp_is_user_activity() || ! bp_current_component() ) && ! $is_my_groups_post ) {
	$mobile_hide = true;
	$id = 'portfolio-sidebar-widget';
} else {
	$mobile_hide = false;
	$id = 'portfolio-sidebar-inline-widget';
}

?>

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
