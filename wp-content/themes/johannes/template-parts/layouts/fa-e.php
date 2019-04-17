<?php $display = johannes_get_post_layout_options('fa_e'); ?>

<article <?php post_class('johannes-layout-fa-e row category-pill align-items-center justify-content-around'); ?>>
        
        <?php if ( $fimg = johannes_get_featured_image( 'johannes-fa-e' ) ): ?>
            <div class="col-12 col-md-5 ">
                <div class="entry-media media-shadow">
                    <a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $fimg ); ?></a>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12 col-md-6 offset-md-1 section-head-alt">
            <div class="entry-header">
                <?php if( $display['format'] ): ?>
                    <div class="entry-format"><?php echo johannes_get_format_icon( $display['format'] ); ?></div>
                <?php endif; ?>
                <?php if( $display['category'] ): ?>
                        <div class="entry-category"><?php echo johannes_get_category(); ?></div>
                <?php endif; ?>
                <?php the_title( sprintf( '<h2 class="entry-title display-1"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                <?php if( $display['meta'] ): ?>
                    <div class="entry-meta">
                        <?php echo johannes_get_meta_data( $display['meta'] ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

</article>
