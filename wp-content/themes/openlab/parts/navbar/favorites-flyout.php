<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

if ( ! class_exists( '\OpenLab\Favorites\App' ) ) {
	return;
}

$user_favorites = OpenLab\Favorites\Favorite\Query::get_results(
	[
		'user_id' => bp_loggedin_user_id(),
	]
);

?>

<div class="flyout-menu" id="favorites-flyout" role="menu">
	<div class="drawer-panel-submenu">
		<div class="flyout-heading">
			<?php get_template_part( 'parts/navbar/favorites-icon' ); ?>
			<span>My Favorites</span>
		</div>
		<ul class="drawer-list">
			<?php if ( $user_favorites ) : ?>
				<?php foreach ( $user_favorites as $user_favorite ) : ?>
					<li class="drawer-item">
						<a class="truncate-on-the-fly" data-basevalue="30" href="<?php echo esc_attr( $user_favorite->get_group_url() ); ?>">
							<?php echo esc_html( $user_favorite->get_group_name() ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			<?php else : ?>
				<li class="drawer-item">
					<a href="https://openlab.citytech.cuny.edu/blog/help/adding-a-course-project-or-club-to-favorites/">
						Learn how to add favorites!
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
