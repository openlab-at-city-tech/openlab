<!DOCTYPE html>

<html <?php language_attributes(); ?>>

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

		<a class="skip-link button" href="#site-content"><?php esc_html_e( 'Skip to the content', 'hemingway' ); ?></a>
	
		<div class="big-wrapper">
	
			<div class="header-cover section bg-dark-light no-padding">

				<?php $header_image_url = get_header_image() ? get_header_image() : get_template_directory_uri() . '/assets/images/header.jpg'; ?>
		
				<div class="header section" style="background-image: url( <?php echo esc_url( $header_image_url ); ?> );">
							
					<div class="header-inner section-inner">
					
						<?php 

						$custom_logo_id 	= get_theme_mod( 'custom_logo' );
						$legacy_logo_url 	= get_theme_mod( 'hemingway_logo' );

						$blog_title 		= get_bloginfo( 'title' );
						$blog_description 	= get_bloginfo( 'description' );

						$blog_title_elem 	= ( ( is_front_page() || is_home() ) && ! is_page() ) ? 'h1' : 'div';
						
						if ( $custom_logo_id || $legacy_logo_url ) : 

							$custom_logo_url = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : $legacy_logo_url;
						
							?>
						
							<<?php echo $blog_title_elem; ?> class="blog-logo">
							
						        <a href="<?php echo esc_url( home_url( "/" ) ); ?>" rel="home">
						        	<img src="<?php echo esc_url( $custom_logo_url ); ?>">
									<?php if ( $blog_title ) : ?>
										<span class="screen-reader-text"><?php echo $blog_title; ?></span>
									<?php endif; ?>
						        </a>
						        
						    </<?php echo $blog_title_elem; ?>><!-- .blog-logo -->
					
						<?php elseif ( $blog_title || $blog_description ) : ?>
					
							<div class="blog-info">
							
								<?php if ( $blog_title ) : ?>
									<<?php echo $blog_title_elem; ?> class="blog-title">
										<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo $blog_title; ?></a>
									</<?php echo $blog_title_elem; ?>>
								<?php endif; ?>
								
								<?php if ( $blog_description ) : ?>
									<p class="blog-description"><?php echo $blog_description; ?></p>
								<?php endif; ?>
							
							</div><!-- .blog-info -->
							
						<?php endif; ?>
									
					</div><!-- .header-inner -->
								
				</div><!-- .header -->
			
			</div><!-- .bg-dark -->
			
			<div class="navigation section no-padding bg-dark">
			
				<div class="navigation-inner section-inner group">
				
					<div class="toggle-container section-inner hidden">
			
						<button type="button" class="nav-toggle toggle">
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
							<span class="screen-reader-text"><?php _e( 'Toggle mobile menu', 'hemingway' ); ?></span>
						</button>
						
						<button type="button" class="search-toggle toggle">
							<div class="metal"></div>
							<div class="glass"></div>
							<div class="handle"></div>
							<span class="screen-reader-text"><?php _e( 'Toggle search field', 'hemingway' ); ?></span>
						</button>
											
					</div><!-- .toggle-container -->
					
					<div class="blog-search hidden">
						<?php get_search_form(); ?>
					</div><!-- .blog-search -->
				
					<ul class="blog-menu">
						<?php if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu( array( 
								'container' 		=> '', 
								'items_wrap' 		=> '%3$s',
								'theme_location' 	=> 'primary', 
							) );
						} else {
							wp_list_pages( array(
								'container' => '',
								'title_li' 	=> ''
							) );
						} ?>
					 </ul><!-- .blog-menu -->
					 
					 <ul class="mobile-menu">
					
						<?php if ( has_nav_menu( 'primary' ) ) {
																			
							wp_nav_menu( array( 
								'container' 		=> '', 
								'items_wrap' 		=> '%3$s',
								'theme_location' 	=> 'primary', 
							) ); 
						
						} else {
						
							wp_list_pages( array(
								'container' 	=> '',
								'title_li' 		=> ''
							) );
							
						} ?>
						
					 </ul><!-- .mobile-menu -->
				 
				</div><!-- .navigation-inner -->
				
			</div><!-- .navigation -->