<div class="post-bubbles">

	<a href="<?php the_permalink(); ?>" class="format-bubble" title="<?php the_title_attribute(); ?>"></a>	
	
	<?php if ( is_sticky() ) : ?>
		<a href="<?php the_permalink(); ?>" title="<?php _e( 'Sticky post', 'lingonberry'); ?>: <?php the_title_attribute(); ?>" class="sticky-bubble"><?php _e( 'Sticky post', 'lingonberry'); ?></a>
	<?php endif; ?>

</div>

<div class="content-inner">

	<div class="post-header<?php if ( ! is_single() ) echo ' hidden'; ?>">
		
	    <?php if ( is_single() ) : ?>
		
	    	<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
	    
	    <?php else : ?>
	    
	    	<h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
	    
	    <?php endif; ?>
	    
		<?php lingonberry_meta(); ?>
	    
    </div><!-- .post-header -->
										                                    	    
    <div class="post-content">
	
		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

	</div><!-- .post-content -->
    
	<div class="clear"></div>
	
	<?php if ( is_single() ) : ?>
	
		<div class="post-cat-tags">
					
			<p class="post-categories"><?php _e( 'Categories:', 'lingonberry' ); ?> <?php the_category( ', ' ); ?></p>
		
			<p class="post-tags"><?php the_tags( __( 'Tags:', 'lingonberry' ) . ' ', ', ' ); ?></p>
					
		</div>
		
	<?php endif; ?>
        
</div><!-- .post content-inner -->