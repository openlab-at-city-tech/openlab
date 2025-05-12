<?php
$navbar_nav_menu_items = openlab_get_global_nav_links();

$user_unread_counts = openlab_get_user_unread_counts( bp_loggedin_user_id() );

$has_any_unread = (
	$user_unread_counts['messages'] > 0 ||
	$user_unread_counts['friend_requests'] > 0 ||
	$user_unread_counts['group_invites'] > 0
);

$my_openlab_has_unread_class = $has_any_unread ? 'has-unread' : '';

?>

<nav class="openlab-navbar" role="navigation">
	<header class="navbar-logo pull-left"><a href="<?php echo bp_get_root_url(); ?>"><span class="screen-reader-text">OpenLab at City Tech</span></a></header>

	<div class="navbar-nav-menu">
		<ul class="navbar-nav">
			<?php foreach ( $navbar_nav_menu_items as $item ) : ?>
				<li class="navbar-nav-item">
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="navbar-nav-link">
						<?php echo esc_html( $item['text'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="navbar-action-links">
		<div class="navbar-action-link-search">
			<a class="navbar-action-link-link" href="<?php echo esc_url( home_url( 'search' ) ); ?>">
				<span class="screen-reader-text">Search</span>
				<i class="fa fa-search" aria-hidden="true"></i>
			</a>
		</div>

		<div class="navbar-action-link-help">
			<a class="navbar-action-link-link" href="<?php echo esc_url( home_url( 'blog/help/openlab-help' ) ); ?>">
				<span class="screen-reader-text">Help</span>
				<i class="fa fa-question-circle-o" aria-hidden="true"></i>
			</a>
		</div>

		<?php if ( is_user_logged_in() ) : ?>
			<div class="navbar-action-logged-in">
				<div class="navbar-action-link-favorites navbar-action-link-toggleable">
					<button class="navbar-flyout-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="favorites-flyout">
						<span class="screen-reader-text">Favorites</span>
						<?php get_template_part( 'parts/navbar/favorites-icon' ); ?>
					</button>

				</div>

				<div class="navbar-action-link-my-openlab navbar-action-link-toggleable">
					<span class="screen-reader-text">My OpenLab</span>
					<button class="navbar-flyout-toggle <?php echo esc_attr( $my_openlab_has_unread_class ); ?>" aria-haspopup="true" aria-expanded="false" aria-controls="my-openlab-flyout">
						<span class="screen-reader-text">My OpenLab</span>
						<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>
					</button>
				</div>
			</div>
		<?php else : ?>

		<?php endif; ?>

		<div class="navbar-action-link-main-menu navbar-action-link-toggleable">
			<button class="navbar-flyout-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="main-menu-flyout">
				<span class="screen-reader-text">Main Menu</span>
				<?php get_template_part( 'parts/navbar/menu-icon' ); ?>
			</button>
		</div>
	</div>

	<div class="shadow-mask-left"></div>
	<div class="shadow-mask-right"></div>
</nav>

<div class="openlab-navbar-flyouts">
	<?php if ( is_user_logged_in() ) : ?>
		<?php get_template_part( 'parts/navbar/favorites-flyout' ); ?>
		<?php get_template_part( 'parts/navbar/my-openlab-flyout' ); ?>
	<?php endif; ?>

	<?php get_template_part( 'parts/navbar/main-menu-flyout' ); ?>
</div>
