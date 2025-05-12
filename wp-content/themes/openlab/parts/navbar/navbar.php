<?php
$navbar_nav_menu_items = [
	'about' => [
		'text' => 'About',
		'url'  => home_url( 'about' ),
	],
	'people' => [
		'text' => 'People',
		'url'  => home_url( 'members' ),
	],
	'courses' => [
		'text' => 'Courses',
		'url'  => home_url( 'courses' ),
	],
	'projects' => [
		'text' => 'Projects',
		'url'  => home_url( 'projects' ),
	],
	'clubs' => [
		'text' => 'Clubs',
		'url'  => home_url( 'clubs' ),
	],
	'portfolios' => [
		'text' => 'Portfolios',
		'url'  => home_url( 'portfolios' ),
	],
];
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

						<span class="fa-stack">
							<i class="fa fa-bookmark fa-stack-1x fa-stack-bg" aria-hidden="true"></i>
							<i class="fa fa-bookmark-o fa-stack-1x fa-stack-outline" aria-hidden="true"></i>
						</span>
					</button>

				</div>

				<div class="navbar-action-link-my-openlab navbar-action-link-toggleable">
					<span class="screen-reader-text">My OpenLab</span>
					<i class="fa fa-user-circle-o" aria-hidden="true"></i>
				</div>
			</div>
		<?php else : ?>

		<?php endif; ?>

		<div class="navbar-action-link-main-menu navbar-action-link-hoverable">
			<span class="screen-reader-text">Main Menu</span>
			<i class="fa fa-bars" aria-hidden="true"></i>
		</div>
	</div>

	<div class="shadow-mask-left"></div>
	<div class="shadow-mask-right"></div>
</nav>

<div class="openlab-navbar-flyouts">
	<?php if ( is_user_logged_in() ) : ?>
		<?php get_template_part( 'parts/navbar/favorites-flyout' ); ?>
	<?php endif; ?>
</div>
