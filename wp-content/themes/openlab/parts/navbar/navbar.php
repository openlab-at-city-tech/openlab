<?php
$navbar_nav_menu_items = openlab_get_global_nav_links();

$my_openlab_has_unread_class = openlab_user_has_unread_counts() ? 'has-unread-upper-dot' : '';

$help_is_current_class       = is_singular( 'help' ) || is_tax( 'help_category' ) ? 'navbar-action-link-current' : '';
$search_is_current_class     = is_page( 'search' ) ? 'navbar-action-link-current' : '';
$my_openlab_is_current_class = bp_is_my_profile() ? 'navbar-action-link-current' : '';

?>

<nav class="openlab-navbar" role="navigation">
	<header class="navbar-logo pull-left">
		<a href="<?php echo bp_get_root_url(); ?>">
			<span class="screen-reader-text">OpenLab at City Tech</span>
			<span class="hidden-xs"><?php include( ABSPATH . '/wp-content/mu-plugins/parts/persistent/svg-logo.php' ); ?></span>
			<span class="visible-xs"><?php include( ABSPATH . '/wp-content/mu-plugins/parts/persistent/svg-logo-notext.php' ); ?></span>
		</a>
	</header>

	<div class="navbar-nav-menu hidden-md hidden-sm hidden-xs">
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
		<div class="navbar-action-link navbar-action-link-help <?php echo esc_attr( $help_is_current_class ); ?>">
			<a class="navbar-action-link-link" href="<?php echo esc_url( home_url( 'blog/help/openlab-help' ) ); ?>">
				<span class="screen-reader-text">Help</span>
				<?php get_template_part( 'parts/navbar/help-icon' ); ?>
			</a>
		</div>

		<div class="navbar-action-link navbar-action-link-search <?php echo esc_attr( $search_is_current_class ); ?>">
			<a class="navbar-action-link-link" href="<?php echo esc_url( home_url( 'search' ) ); ?>">
				<span class="screen-reader-text">Search</span>
				<?php get_template_part( 'parts/navbar/search-icon' ); ?>
			</a>
		</div>

		<?php if ( is_user_logged_in() ) : ?>
			<div class="navbar-action-link navbar-action-link-favorites navbar-action-link-toggleable">
				<button class="navbar-flyout-toggle" aria-expanded="false" aria-controls="favorites-flyout">
					<span class="screen-reader-text">Favorites</span>
					<span class="icon-default"><?php get_template_part( 'parts/navbar/favorites-icon' ); ?></span>
					<span class="icon-close"><?php get_template_part( 'parts/navbar/close-icon' ); ?></span>
				</button>
			</div>

			<div class="navbar-action-link navbar-action-link-my-openlab navbar-action-link-toggleable <?php echo esc_attr( $my_openlab_is_current_class ); ?>">
				<button class="navbar-flyout-toggle <?php echo esc_attr( $my_openlab_has_unread_class ); ?>" aria-expanded="false" aria-controls="my-openlab-flyout">
					<span class="screen-reader-text">My OpenLab</span>
					<span class="icon-default"><?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?></span>
					<span class="icon-close"><?php get_template_part( 'parts/navbar/close-icon' ); ?></span>
				</button>
			</div>
		<?php else : ?>
			<div class="navbar-action-link navbar-action-link-login navbar-action-link-toggleable">
				<button class="navbar-flyout-toggle navbar-flyout-toggle-login" aria-expanded="false" aria-controls="login-flyout">
					<span>Sign In</span>
					<span class="icon-default"><?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?></span>
					<span class="icon-close"><?php get_template_part( 'parts/navbar/close-icon' ); ?></span>
				</button>
			</div>
		<?php endif; ?>

		<div class="navbar-action-link navbar-action-link-main-menu navbar-action-link-toggleable">
			<button class="navbar-flyout-toggle" aria-expanded="false" aria-controls="main-menu-flyout">
				<span class="screen-reader-text">Main Menu</span>
				<span class="icon-default"><?php get_template_part( 'parts/navbar/menu-icon' ); ?></span>
				<span class="icon-close"><?php get_template_part( 'parts/navbar/close-icon' ); ?></span>
			</button>
		</div>
	</div>

	<div class="shadow-mask-left"></div>
	<div class="shadow-mask-right"></div>
</nav>

<div class="openlab-navbar-drawer" inert>
	<?php if ( is_user_logged_in() ) : ?>
		<?php get_template_part( 'parts/navbar/favorites-flyout' ); ?>
		<?php get_template_part( 'parts/navbar/my-openlab-flyout' ); ?>
	<?php else : ?>
		<?php get_template_part( 'parts/navbar/login-flyout' ); ?>
	<?php endif; ?>

	<?php get_template_part( 'parts/navbar/main-menu-flyout' ); ?>
</div>
