<?php get_header(); ?>

<div class="content section-inner">
											        
	<?php 	
	if ( have_posts() ) : 
		while ( have_posts() ) : 
			the_post();

			?>

			<div class="singular-container">

				<?php
				
				get_template_part( 'content', get_post_format() ); 

				if ( is_single() ) {
					the_post_navigation( array(
						'class' 	=> 'post-nav',
					) );
				}
				
				comments_template( '', true );

				?>

			</div><!-- .singular-container -->

			<?php

		endwhile;
	endif;
	?>

</div><!-- .content section-inner -->
		
<?php get_footer(); ?>