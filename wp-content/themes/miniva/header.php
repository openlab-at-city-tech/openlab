<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Miniva
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'wp_body_open' ); ?>

<?php do_action( 'miniva_body_start' ); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'miniva' ); ?></a>

	<?php do_action( 'miniva_header_before' ); ?>

	<header id="masthead" class="site-header" role="banner">

		<?php do_action( 'miniva_header_start' ); ?>

		<div class="site-branding">

			<?php the_custom_logo(); ?>

			<div class="site-branding-text">
				<?php
				if ( is_front_page() && is_home() ) :
					?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<?php
				else :
					?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php
				endif;
				$miniva_description = get_bloginfo( 'description', 'display' );
				if ( $miniva_description || is_customize_preview() ) :
					?>
					<p class="site-description"><?php echo $miniva_description; /* phpcs:ignore WordPress.Security.EscapeOutput */ ?></p>
				<?php endif; ?>
			</div>
		</div><!-- .site-branding -->

		<?php do_action( 'miniva_header_middle' ); ?>

		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary', 'miniva' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'miniva' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'container'      => 'ul',
				)
			);
			?>
		</nav><!-- #site-navigation -->

		<?php do_action( 'miniva_header_end' ); ?>

	</header><!-- #masthead -->

	<?php do_action( 'miniva_header_after' ); ?>

	<div id="content" class="site-content<?php miniva_content_class(); ?>">

		<?php do_action( 'miniva_content_start' ); ?>
