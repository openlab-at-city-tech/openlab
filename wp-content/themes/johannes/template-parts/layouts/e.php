<?php $display = johannes_get_post_layout_options('e'); ?>

<article <?php post_class('johannes-post johannes-layout-e row justify-content-center category-pill'); ?>>
    <?php if ( $fimg = johannes_get_featured_image( 'johannes-e' ) ): ?>
	    <div class="col-12 col-md-5 col-lg-6">
		    <div class="entry-media">
		        <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $fimg ); ?></a>
		    </div>
	    </div>
    <?php endif; ?>

    <div class="col-12 col-md-7 col-lg-6">
	    <div class="entry-header">
	    	<?php if( $display['format'] ): ?>
        		<div class="entry-format"><?php echo johannes_get_format_icon( $display['format'] ); ?></div>
        	<?php endif; ?>
	        <?php if( $display['category'] ): ?>
		        <div class="entry-category">
		            <?php echo johannes_get_category(); ?>
		        </div>
	        <?php endif; ?>
	        <?php the_title( sprintf( '<h2 class="entry-title h2"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	        <?php if( $display['meta'] ): ?>
		        <div class="entry-meta">
		            <?php echo johannes_get_meta_data( $display['meta'] ); ?>
		        </div>
	        <?php endif; ?>
	    </div>
	    <?php if( $display['excerpt'] ): ?>
		    <div class="entry-content">
		        <?php echo johannes_get_excerpt( $display['excerpt'] ); ?>
		    </div>
	    <?php endif; ?>
	    <?php if( $display['rm'] ): ?>
		    <div class="entry-footer">
		        <a href="<?php the_permalink(); ?>" class="johannes-button johannes-button-secondary johannes-button-medium"><?php echo __johannes( 'read_more'); ?></a>
		    </div>
	    <?php endif; ?>
    </div>
</article>