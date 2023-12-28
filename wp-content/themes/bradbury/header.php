<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="profile" href="//gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div id="container">

	<a class="skip-link screen-reader-text" href="#site-main"><?php esc_html_e( 'Skip to content', 'bradbury' ); ?></a>
	<div class="site-wrapper-all site-wrapper-boxed">

		<?php if (has_nav_menu( 'secondary' )) { ?> 

		<div id="site-preheader">
			<div class="site-section-wrapper site-section-wrapper-preheader">

				<nav id="site-secondary-nav">

				<?php wp_nav_menu( array( 'container' => '', 'container_class' => '', 'menu_class' => '', 'menu_id' => 'site-secondary-menu', 'sort_column' => 'menu_order', 'depth' => '1', 'theme_location' => 'secondary' ) ); ?>

				</nav><!-- #site-secondary-menu -->

			</div><!-- .site-section-wrapper .site-section-wrapper-preheader -->
		</div><!-- #site-preheader -->

		<?php }	?>

		<header id="site-masthead" class="site-section site-section-masthead">
			<div class="site-section-wrapper site-section-wrapper-masthead">
				<div id="site-logo"><?php
				if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
					bradbury_the_custom_logo();
				} else { ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php } ?>
			</div><!-- #site-logo --><!-- ws fix 
			--><div id="site-section-primary-menu">
					<?php
					if (has_nav_menu( 'primary' )) { ?>
					<nav id="site-primary-nav">
						<?php
							// Output the mobile menu
							get_template_part( 'template-parts/mobile-menu' );

							if (has_nav_menu( 'primary' )) 
							{ 
								wp_nav_menu( array(
									'container' => '', 
									'container_class' => '', 
									'menu_class' => 'navbar-nav dropdown large-nav sf-menu clearfix', 
									'menu_id' => 'site-primary-menu',
									'sort_column' => 'menu_order', 
									'theme_location' => 'primary', 
									'link_after' => '', 
									'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
								) );
							}
						?></nav><!-- #site-primary-nav --><?php } ?>
				</div><!-- #site-section-primary-menu -->
			</div><!-- .site-section-wrapper .site-section-wrapper-masthead -->
		</header><!-- #site-masthead .site-section-masthead -->
		<?php
		if ( ( is_front_page() || is_home() ) && !is_paged() ) {
			
			if ( 1 == get_theme_mod( 'bradbury-display-pages', 1 ) ) {
				get_template_part( 'template-parts/content', 'home-featured' );
			}

		} else {
			if ( is_singular() ) {
				if ( 1 == get_theme_mod( 'theme-display-post-featured-image', 1 ) ) {
					while (have_posts()) : the_post();
					get_template_part('slideshow','single');
					endwhile;
				}
			} 
		}
		?>