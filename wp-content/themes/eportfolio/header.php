<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ePortfolio
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
	<?php if ( function_exists( 'wp_body_open' ) ) {
	    wp_body_open();
	}
	?>
	<div id="page" class="site twp-page-content">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'eportfolio' ); ?></a>
		<?php if (has_header_image()) {
			$twp_header_image_url = get_header_image();
		} else {
			$twp_header_image_url = '';
		}?>
		<header id="masthead" class="site-header twp-header  data-bg twp-overlay-black" data-background="<?php echo esc_url($twp_header_image_url); ?>">
			<div class="twp-menu-section twp-w-100">
				<div class="twp-menu-icon twp-white-menu-icon" id="twp-menu-icon">
					<button>
						<span></span>
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
                <?php if (has_nav_menu('social-nav')) { ?>
					<div class="twp-social-icon-section">
						<?php
							wp_nav_menu( array(
								'theme_location' => 'social-nav',
								'menu_id'        => 'social-menu',
								'menu_class' => 'twp-social-icons twp-social-icons-white'
							) );
						?>
					</div>
				<?php } ?>
			</div>
			<div class="site-branding">
				<div class="twp-wrapper">
					<?php the_custom_logo(); ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					
					<?php
					$eportfolio_description = get_bloginfo( 'description', 'display' );
					if ( $eportfolio_description || is_customize_preview() ) :
						?>
						<p class="site-description"><?php echo esc_html($eportfolio_description); /* WPCS: xss ok. */ ?></p>
					<?php endif; ?>
					<?php 
						$twp_short_description = eportfolio_get_option('short_description_details');
						$twp_button_text = eportfolio_get_option('button_text');
						$twp_button_url_link = eportfolio_get_option('button_url_link');
					?>
					<?php if (!empty($twp_short_description)) { ?>
						<div class="twp-caption">
							<p><?php echo esc_html($twp_short_description); ?></p>
						</div>
					<?php } ?>

					<?php if (!empty($twp_button_text)) { ?>
						<div class="twp-btn-section">
							<a href="<?php echo esc_url($twp_button_url_link); ?>" class="twp-border-btn twp-border-btn-white"><?php echo esc_html($twp_button_text); ?></a>
						</div>
					<?php } ?>

				</div>
				
			</div><!-- .site-branding -->

		
			<!-- primary nav -->
			<nav id="site-navigation" class="main-navigation twp-nav-section">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'eportfolio' ); ?></button>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary-nav',
					'menu_id'        => 'primary-menu',
				) );
				?>
			</nav><!-- #site-navigation -->
		</header><!-- #masthead -->
        
        <?php if ((eportfolio_get_option('enable_preloader')) == 1) { ?>
			<div class="twp-preloader" id="preloader">
				<div class="status" id="status">
					<div class="twp-square"></div>
					<div class="twp-square"></div>
					<div class="twp-square"></div>
					<div class="twp-square"></div>
				</div>
				<div class="twp-preloader" id="preloader">
					<div class="status" id="status">
						<div class="twp-square"></div>
						<div class="twp-square"></div>
						<div class="twp-square"></div>
						<div class="twp-square"></div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php  if (is_home() && !is_paged()) {
			do_action('eportfolio_action_blog_banner_slider');
		}