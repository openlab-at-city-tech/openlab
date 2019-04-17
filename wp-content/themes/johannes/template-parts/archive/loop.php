<div class="section-content row  justify-content-center">
    
    <?php if ( johannes_has_sidebar( 'left' ) ): ?>
        <div class="col-12 col-lg-4 johannes-order-3">
            <?php get_sidebar(); ?>
        </div>
    <?php endif; ?>

    <div class="col-12 johannes-order-1 <?php echo esc_attr( johannes_get_loop_col_class( johannes_get( 'loop' ) ) ); ?>">
        <div class="row johannes-items johannes-posts">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php $layout = johannes_get_loop_params( johannes_get( 'loop' ), $wp_query->current_post ); ?>
                    <div class="<?php echo esc_attr( $layout['col'] ); ?>">
                        <?php get_template_part( 'template-parts/layouts/' . $layout['style'] ); ?>
                    </div>
                    <?php if( $wp_query->current_post === johannes_get('ads', 'between_position') ) : ?>
                        <?php get_template_part( 'template-parts/ads/between-posts' ); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <?php get_template_part( 'template-parts/archive/empty' ); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( johannes_has_sidebar( 'right' ) ): ?>
        <div class="col-12 col-lg-4 johannes-order-3">
            <?php get_sidebar(); ?>
        </div>
    <?php endif; ?>

    <?php get_template_part( 'template-parts/pagination/'. johannes_get( 'pagination' ) ); ?>

</div>