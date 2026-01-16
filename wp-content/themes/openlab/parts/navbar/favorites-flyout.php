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

<div class="flyout-menu" id="favorites-flyout" data-default-panel="favorites-root">
	<div class="drawer-panel drawer-panel-root" id="favorites-root" inert>
		<div class="flyout-heading">
			<?php get_template_part( 'parts/navbar/favorites-icon' ); ?>
			<span>My Favorites</span>
		</div>
		<button class="flyout-close-button sr-only sr-only-focusable" data-flyout-close="favorites-flyout" aria-label="Close Favorites menu">
			Close
		</button>
		<ul class="drawer-list">
			<?php if ( $user_favorites ) : ?>
				<?php foreach ( $user_favorites as $user_favorite ) : ?>
					<?php
					$group_url  = $user_favorite->get_group_url();
					$group_name = $user_favorite->get_group_name();

					if ( ! $group_url || ! $group_name ) {
						continue;
					}

					?>
					<li class="drawer-item">
						<a href="<?php echo esc_attr( $group_url ); ?>">
							<?php echo esc_html( $group_name ); ?>
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
