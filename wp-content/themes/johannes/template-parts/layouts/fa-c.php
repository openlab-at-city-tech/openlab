<?php $display = johannes_get_post_layout_options('fa_c'); ?>

<article <?php post_class('johannes-layout-fa-c johannes-overlay overlay-content-left category-pill'); ?>>
    
        <div class="entry-header">
            <?php if( $display['format'] ): ?>
                <div class="entry-format"><?php echo johannes_get_format_icon( $display['format'] ); ?></div>
            <?php endif; ?>
            <?php if( $display['category'] ): ?>
                    <div class="entry-category"><?php echo johannes_get_category(); ?></div>
            <?php endif; ?>
            <?php the_title( sprintf( '<h2 class="entry-title h2"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php if( $display['meta'] ): ?>
                <div class="entry-meta">
                    <?php echo johannes_get_meta_data( $display['meta'] ); ?>
                </div>
            <?php endif; ?>
        </div>

    <?php if ( $fimg = johannes_get_featured_image( 'johannes-fa-c' ) ): ?>
        <div class="entry-media">
            <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $fimg ); ?></a>
        </div>
    <?php endif; ?>

</article>