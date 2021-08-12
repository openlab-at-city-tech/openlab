<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
		 
		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}
		?>

		<a class="skip-link button" href="#site-content"><?php esc_html_e( 'Skip to the content', 'lingonberry' ); ?></a>
	
		<div class="navigation">
				
			<div class="navigation-inner section-inner">
		
				<ul class="blog-menu">
				
					<?php 
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu( array( 
							'container'			=> '', 
							'items_wrap' 		=> '%3$s',
							'theme_location' 	=> 'primary', 
						) ); 
					} else {
						wp_list_pages( array(
							'container' => '',
							'title_li' 	=> '',
						) );
					}
					?>
					
				 </ul>
				 
				 <?php get_search_form(); ?>
			 
			</div><!-- .navigation-inner -->
		 
		</div><!-- .navigation -->
	
		<header class="header section" id="site-header">
				
			<div class="header-inner section-inner group">

				<button class="nav-toggle">
					<div class="bar"></div>
					<div class="bar"></div>
					<div class="bar"></div>
				</button>

				<div class="blog-title-wrapper">
			
					<?php if ( get_header_image() != '' ) : ?>
								
						<a href="<?php echo esc_url( home_url() ); ?>/" rel="home" class="logo">
							<img src="<?php header_image(); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</a>
						
					<?php else : ?>
					
						<a href="<?php echo esc_url( home_url() ); ?>/" rel="home" class="logo noimg"></a>
					
					<?php endif; ?>

					<?php if ( is_front_page() && is_home() && ! is_paged() ) : ?>
						<h1 class="blog-title"><a href="<?php echo esc_url( home_url() ); ?>/" rel="home"><?php echo get_bloginfo( 'title' ); ?></a></h1>
					<?php else : ?>
						<div class="blog-title"><a href="<?php echo esc_url( home_url() ); ?>/" rel="home"><?php echo get_bloginfo( 'title' ); ?></a></div>
					<?php endif; ?>

				</div><!-- .blog-title-wrapper -->
																							
			</div><!-- .header section -->
			
		</header><!-- .header-inner section-inner -->