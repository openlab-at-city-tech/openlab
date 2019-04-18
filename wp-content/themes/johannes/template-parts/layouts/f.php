<?php $display = johannes_get_post_layout_options('f'); ?>

<article <?php post_class('johannes-post johannes-layout-f row justify-content-start category-pill category-pill-small'); ?>>

    <?php if ( $fimg = johannes_get_featured_image( 'johannes-f' ) ): ?>
	    <div class="col-6 col-sm-4">
		    <div class="entry-media">
		        <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $fimg ); ?></a>
		    </div>
	    </div>
    <?php endif; ?>

    <div class="col-6 col-sm-8 col-md-7">
	    <div class="entry-header">
	    	<?php if( $display['format'] ): ?>
        		<div class="entry-format"><?php echo johannes_get_format_icon( $display['format'] ); ?></div>
        	<?php endif; ?>
	        <?php if( $display['category'] ): ?>
		        <div class="entry-category">
		            <?php echo johannes_get_category(); ?>
		        </div>
	        <?php endif; ?>
	        <?php the_title( sprintf( '<h2 class="entry-title h3"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	        <?php if( $display['meta'] ): ?>
		        <div class="entry-meta">
		            <?php echo johannes_get_meta_data( $display['meta'] ); ?>
		        </div>
	        <?php endif; ?>
	    </div>
    </div>
</article>