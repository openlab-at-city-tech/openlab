<!DOCTYPE html>

<?php 

// Class indicating whether the admin bar is showing
$html_class = is_admin_bar_showing() ? ' showing-admin-bar' : ''; ?>

<html class="no-js<?php echo $html_class; ?>" <?php language_attributes(); ?>>

	<head>

		<meta http-equiv="content-type" content="<?php bloginfo( 'html_type' ); ?>" charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}
		?>

		<div id="site-wrapper">

			<header id="site-header" role="banner">

				<a class="skip-link" href="#site-content"><?php _e( 'Skip to the content', 'koji' ); ?></a>
				<a class="skip-link" href="#main-menu"><?php _e( 'Skip to the main menu', 'koji' ); ?></a>

				<div class="header-top section-inner">

					<?php

					if ( function_exists( 'the_custom_logo' ) && get_theme_mod( 'custom_logo' ) ) :

						koji_custom_logo();

					elseif ( is_front_page() || is_home() ) : ?>

						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

					<?php else : ?>

						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>

					<?php endif; ?>

					<button type="button" aria-pressed="false" class="toggle nav-toggle" data-toggle-target=".mobile-menu-wrapper" data-toggle-scroll-lock="true" data-toggle-attribute="">
						<label>
							<span class="show"><?php _e( 'Menu', 'koji' ); ?></span>
							<span class="hide"><?php _e( 'Close', 'koji' ); ?></span>
						</label>
						<div class="bars">
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
						</div><!-- .bars -->
					</button><!-- .nav-toggle -->

				</div><!-- .header-top -->

				<div class="header-inner section-inner">

					<div class="header-inner-top">

						<?php if ( get_bloginfo( 'description' ) ) : ?>

							<p class="site-description"><?php echo wp_kses_post( get_bloginfo( 'description' ) ); ?></p>

						<?php endif; ?>

						<ul class="site-nav reset-list-style" id="main-menu" role="navigation">
							<?php
							if ( has_nav_menu( 'primary-menu' ) ) {
								wp_nav_menu( array(
									'container' 		=> '',
									'items_wrap' 		=> '%3$s',
									'theme_location' 	=> 'primary-menu',
								) );
							} else {
								wp_list_pages( array(
									'container' => '',
									'title_li' 	=> '',
								) );
							}
							?>
						</ul>

						<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>

							<div class="sidebar-widgets">
								<?php dynamic_sidebar( 'sidebar' ); ?>
							</div><!-- .sidebar-widgets -->

						<?php endif; ?>

					</div><!-- .header-inner-top -->

					<div class="social-menu-wrapper">

						<?php

						$disable_search = get_theme_mod( 'koji_disable_search' ) ? get_theme_mod( 'koji_disable_search' ) : false;
						$show_social_menu = has_nav_menu( 'social' ) || ! $disable_search;

						if ( $show_social_menu ) : ?>

							<ul class="social-menu reset-list-style social-icons s-icons">

								<?php if ( ! $disable_search ) : ?>

									<li class="search-toggle-wrapper"><button type="button" aria-pressed="false" data-toggle-target=".search-overlay" data-set-focus=".search-overlay .search-field" class="toggle search-toggle"><span class="screen-reader-text"><?php _e( 'Toggle the search field', 'koji' ); ?></span></button></li>

									<?php
								endif;

								$social_menu_args = array(
									'theme_location'	=> 'social',
									'container'			=> '',
									'container_class'	=> '',
									'items_wrap'		=> '%3$s',
									'menu_id'			=> '',
									'menu_class'		=> '',
									'depth'				=> 1,
									'link_before'		=> '<span class="screen-reader-text">',
									'link_after'		=> '</span>',
									'fallback_cb'		=> '',
								);

								wp_nav_menu( $social_menu_args );

								?>

							</ul><!-- .social-menu -->

						<?php endif; ?>

					</div><!-- .social-menu-wrapper -->

				</div><!-- .header-inner -->

			</header><!-- #site-header -->

			<div class="mobile-menu-wrapper" aria-expanded="false">

				<div class="mobile-menu section-inner">

					<div class="mobile-menu-top">

						<?php if ( get_bloginfo( 'description' ) ) : ?>

							<p class="site-description"><?php echo wp_kses_post( get_bloginfo( 'description' ) ); ?></p>

						<?php endif; ?>

						<ul class="site-nav reset-list-style" id="mobile-menu" role="navigation">
							<?php
							if ( has_nav_menu( 'mobile-menu' ) ) {
								wp_nav_menu( array(
									'container' 		=> '',
									'items_wrap' 		=> '%3$s',
									'theme_location' 	=> 'mobile-menu',
								) );
							} else {
								wp_list_pages( array(
									'container' => '',
									'title_li' 	=> '',
								) );
							}
							?>
						</ul>

						<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>

							<div class="sidebar-widgets">
								<?php dynamic_sidebar( 'sidebar' ); ?>
							</div><!-- .sidebar-widgets -->

						<?php endif; ?>

					</div><!-- .mobile-menu-top -->

					<div class="social-menu-wrapper">

						<?php if ( $show_social_menu ) : ?>

							<ul class="social-menu reset-list-style social-icons s-icons mobile">

								<?php if ( ! $disable_search ) : ?>

									<li class="search-toggle-wrapper"><button type="button" aria-pressed="false" data-toggle-target=".search-overlay" data-set-focus=".search-overlay .search-field" class="toggle search-toggle"><span class="screen-reader-text"><?php _e( 'Toggle the search field', 'koji' ); ?></span></button></li>

									<?php
								endif;

								wp_nav_menu( $social_menu_args ); ?>

							</ul><!-- .social-menu -->

						<?php endif; ?>

					</div><!-- .social-menu-wrapper -->

				</div><!-- .mobile-menu -->

			</div><!-- .mobile-menu-wrapper -->

			<?php if ( ! $disable_search ) : ?>

				<div class="search-overlay cover-modal" aria-expanded="false">

					<div class="section-inner search-overlay-form-wrapper">
						<?php echo get_search_form(); ?>
					</div><!-- .section-inner -->

					<button type="button" class="toggle search-untoggle" data-toggle-target=".search-overlay" data-set-focus=".search-toggle:visible">
						<div class="search-untoggle-inner">
							<img aria-hidden="true" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/icons/cross.svg" />
						</div>
						<span class="screen-reader-text"><?php _e( 'Hide the search overlay', 'koji' ); ?></span>
					</button><!-- .search-untoggle -->

				</div><!-- .search-overlay -->

			<?php endif; ?>
