<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>
		
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1" >
		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}
		?>
	
		<div class="big-wrapper">
	
			<div class="header-cover section bg-dark-light no-padding">

				<?php $header_image_url = get_header_image() ? get_header_image() : get_template_directory_uri() . '/images/header.jpg'; ?>
		
				<div class="header section" style="background-image: url( <?php echo  $header_image_url; ?> );">
							
					<div class="header-inner section-inner">
					
						<?php if ( get_theme_mod( 'hemingway_logo' ) ) : ?>
						
							<div class='blog-logo'>
							
						        <a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>' rel='home'>
						        	<img src='<?php echo esc_url( get_theme_mod( 'hemingway_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>'>
						        </a>
						        
						    </div><!-- .blog-logo -->
					
						<?php elseif ( get_bloginfo( 'description' ) || get_bloginfo( 'title' ) ) : ?>
					
							<div class="blog-info">
							
								<h2 class="blog-title">
									<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo esc_attr( get_bloginfo( 'title' ) ); ?></a>
								</h2>
								
								<?php if ( get_bloginfo( 'description' ) ) { ?>
								
									<h3 class="blog-description"><?php echo esc_attr( get_bloginfo( 'description' ) ); ?></h3>
									
								<?php } ?>
							
							</div><!-- .blog-info -->
							
						<?php endif; ?>
									
					</div><!-- .header-inner -->
								
				</div><!-- .header -->
			
			</div><!-- .bg-dark -->
			
			<div class="navigation section no-padding bg-dark">
			
				<div class="navigation-inner section-inner">
				
					<div class="toggle-container hidden">
			
						<button type="button" class="nav-toggle toggle">
								
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
						
						</button>
						
						<button type="button" class="search-toggle toggle">
								
							<div class="metal"></div>
							<div class="glass"></div>
							<div class="handle"></div>
						
						</button>
						
						<div class="clear"></div>
					
					</div><!-- .toggle-container -->
					
					<div class="blog-search hidden">
					
						<?php get_search_form(); ?>
					
					</div>
				
					<ul class="blog-menu">
					
						<?php if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu( array( 
								'container' 		=> '', 
								'items_wrap' 		=> '%3$s',
								'theme_location' 	=> 'primary', 
								'walker' 			=> new hemingway_nav_walker
							) );
						} else {
							wp_list_pages( array(
								'container' => '',
								'title_li' 	=> ''
							) );
						} ?>

					 </ul>

					 <div class="clear"></div>
					 
					 <ul class="mobile-menu">
					
						<?php if ( has_nav_menu( 'primary' ) ) {
																			
							wp_nav_menu( array( 
								'container' 		=> '', 
								'items_wrap' 		=> '%3$s',
								'theme_location' 	=> 'primary', 
								'walker' 			=> new hemingway_nav_walker
							) ); 
						
						} else {
						
							wp_list_pages( array(
								'container' 	=> '',
								'title_li' 		=> ''
							) );
							
						} ?>
						
					 </ul>
				 
				</div><!-- .navigation-inner -->
				
			</div><!-- .navigation -->