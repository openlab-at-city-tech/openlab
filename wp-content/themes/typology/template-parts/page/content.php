<div class="section-content section-content-page">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'typology-post typology-single-post' ); ?>>
		
		<?php $meta = typology_get_page_meta(); ?>
		<?php if(!absint($meta['cover']) ) : ?>
		    <header class="entry-header">
		        <?php the_title( '<h1 class="entry-title entry-title-cover-empty">', '</h1>' ); ?>
		        <?php if( typology_get_option( 'page_dropcap' ) ) : ?>
	                    <div class="post-letter"><?php echo typology_get_letter(); ?></div>
	            <?php endif; ?>
		    </header>
		<?php endif; ?>
	    
	    <div class="entry-content clearfix">

	    	<?php if( $meta['fimg'] == 'content' && has_post_thumbnail() ) : ?>
	    		<div class="typology-featured-image">
                	<?php the_post_thumbnail('typology-a'); ?>
                </div>
        	<?php endif; ?>

	        <?php the_content(); ?>
	        <?php wp_link_pages( array('before' => '<div class="typology-link-pages">', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
	    </div>

	</article>
</div>