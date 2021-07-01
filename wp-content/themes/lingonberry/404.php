<?php get_header(); ?>

<main id="site-content" class="content section-inner">

	<article class="post singular-container">
	
		<div class="content-inner">
				
			<header class="post-header">
					
				<h1 class="post-title"><?php _e( 'Error 404', 'lingonberry' ); ?></h1>
				
			</header>
																		
			<div class="post-content">
							
				<p><?php _e( "It seems like you have tried to open a page that doesn't exist. It could have been deleted, moved, or it never existed at all. You are welcome to search for what you are looking for with the form below.", 'lingonberry' ); ?></p>
				
				<?php get_search_form(); ?>
				
			</div><!-- .post-content -->
		
		</div><!-- .content-inner -->
											
	</article><!-- .post -->

</main><!-- #site-content -->

<?php get_footer(); ?>
