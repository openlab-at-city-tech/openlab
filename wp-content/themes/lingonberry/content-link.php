<div class="post-bubbles">

	<a href="<?php the_permalink(); ?>" class="format-bubble" title="<?php the_title_attribute(); ?>"></a>	
	
	<?php if ( is_sticky() ) : ?>
		<a href="<?php the_permalink(); ?>" title="<?php _e( 'Sticky post', 'lingonberry' ); ?>: <?php the_title_attribute(); ?>" class="sticky-bubble"><?php _e( 'Sticky post', 'lingonberry' ); ?></a>
	<?php endif; ?>

</div>

<div class="content-inner">

    <?php if ( is_single() ) : ?>

		<div class="post-header">
			
	    	<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
			
			<?php lingonberry_meta(); ?>
					    	    
		</div><!-- .post-header -->
	
	<?php endif; ?>
										                                    	    
	<div class="post-content">
	
		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

	</div><!-- .post-content -->
	
	<div class="clear"></div>

</div><!-- .content-inner -->