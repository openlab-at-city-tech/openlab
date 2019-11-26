<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gillian
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'gillian' ); ?></a>
	
	<header id="masthead" class="site-header" role="banner" <?php 
	    if ( get_header_image() ) { 
	        echo 'class="header-bar header-background-image" style="background-image: url(\'';
			header_image();
			echo '\')"'; 
	    } ?>>
		
		<?php if ( has_nav_menu( 'top-menu' ) or has_nav_menu( 'social' ) ) {
			echo '<nav id="site-navigation-top" class="main-navigation top-navigation" role="navigation" aria-label="';
			esc_attr_e( 'Top Navigation', 'gillian' );
			echo '"><div class="top-menu"><button class="menu-toggle" aria-controls="top-menu menu-social-items" aria-expanded="false"><span class="screen-reader-text">';
			esc_html_e( 'Menu', 'gillian' );
			echo '</span></button>';
			gillian_top_menu();
			echo '</div>';
			
			if (has_nav_menu( 'social' )) {
				echo '<div class="social-menu">';
				gillian_social_menu();
				echo '</div>';
			}
			else {
				echo '<div class="social-menu"><div id="menu-social" class="menu-social"><ul id="menu-social-items" class="menu-items social-menu"></ul></div></div>';
			};
			
			echo '</nav> <!-- .top-navigation -->';
		} ?>
	
		<div class="header-bar">
			<div class="site-branding">
				<?php
				if ( is_front_page() && is_home() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
				endif;

				$description = get_bloginfo( 'description', 'display' );
				if ( $description || is_customize_preview() ) : ?>
					<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
				<?php
				endif; ?>
			</div><!-- .site-branding -->
			
			<div class="header-search">
				<?php get_search_form(); ?>
			</div> <!-- .header-search -->
		</div> <!-- .header-bar -->

		<nav id="site-navigation" class="main-navigation bottom-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'gillian' ); ?>">
		<div class="bottom-menu">
			<button class="menu-toggle" aria-controls="bottom-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'gillian' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'bottom-menu', 'menu_id' => 'bottom-menu' ) ); ?>
		</div>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
