<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Flawless Blog
 */

?>
<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="https://gmpg.org/xfn/11">

		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div id="page" class="site">
			<a class="skip-link screen-reader-text" href="#primary-content"><?php esc_html_e( 'Skip to content', 'flawless-blog' ); ?></a>

			<div id="loader">
				<div class="loader-container">
					<div id="preloader">
						<div class="pre-loader-6"></div>
					</div>
				</div>
			</div><!-- #loader -->

			 <?php require get_template_directory() . '/inc/frontpage-sections/flash-articles.php'; ?>


			<header id="masthead" class="site-header">
				<div class="adore-header-outer-wrapper">
					<div class="adore-header">
						<div class="theme-wrapper">
							<div class="adore-header-wrapper">
								<div class="site-branding">
									<?php
									if ( has_custom_logo() ) {
										?>
										<div class="site-logo">
											<?php the_custom_logo(); ?>
										</div>
										<?php
										}
										if ( get_theme_mod( 'flawless_blog_header_text_display', true ) === true ) {
											?>

										<div class="site-identity">
											<?php if ( is_front_page() && is_home() ) : ?>
												<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
													<?php else : ?>
												<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
												<?php
											endif;

											$flawless_blog_description = get_bloginfo( 'description', 'display' );
											if ( $flawless_blog_description || is_customize_preview() ) :
												?>
											<p class="site-description"><?php echo $flawless_blog_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
											<?php endif; ?>
										</div>

									<?php } ?>
								</div><!-- .site-branding -->
								<div class="adore-navigation">
									<div class="header-nav-search">
										<div class="header-navigation">
											<nav id="site-navigation" class="main-navigation">
												<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
													<span></span>
													<span></span>
													<span></span>
												</button>
												<?php

												if ( has_nav_menu( 'primary' ) ) {

													wp_nav_menu(
														array(
															'theme_location' => 'primary',
															'menu_id'        => 'primary-menu',
														)
													);

												}

												?>
											</nav><!-- #site-navigation -->
										</div>
										<div class="header-end">
											<div class="navigation-search">
												<div class="navigation-search-wrap">
													<a href="#" title="Search" class="navigation-search-icon">
														<i class="fa fa-search"></i>
													</a>
													<div class="navigation-search-form">
														<?php get_search_form(); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header><!-- #masthead -->

			<div id="primary-content" class="primary-site-content">

				<?php
				if ( is_front_page() ) {
					require get_template_directory() . '/inc/frontpage-sections/banner.php';
				}

				if ( ! is_front_page() || is_home() ) {
					?>

					<div id="content" class="site-content theme-wrapper">
						<div class="theme-wrap">

						<?php } ?>
