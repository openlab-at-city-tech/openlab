<div class="johannes-section">
    <div class="container">

    <?php if ( johannes_get( 'display_title' ) ): ?>
        <div class="section-head">
            <h3 class="section-title h2"><?php echo esc_html( __johannes('front_page_classic_title') ); ?></h3>
        </div>
    <?php endif; ?>
    
    <div class="section-content row justify-content-center">
            
            <?php if ( johannes_has_sidebar( 'left' ) ): ?>
                <div class="col-12 col-lg-4 johannes-order-3">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>

            <div class="col-12 johannes-order-1 <?php echo esc_attr( johannes_get_loop_col_class( johannes_get( 'loop' ) ) ); ?>">
                <div class="row johannes-items johannes-posts">
                    <?php $front_page_classic = new WP_Query( johannes_get( 'query_args' ) ); ?>
                    <?php if ( $front_page_classic->have_posts() ) : ?>
                        <?php while ( $front_page_classic->have_posts() ) : $front_page_classic->the_post(); ?>
                            <?php $layout = johannes_get_loop_params( johannes_get( 'loop' ), $front_page_classic->current_post ); ?>
                            <div class="<?php echo esc_attr( $layout['col'] ); ?>">
                                <?php get_template_part( 'template-parts/layouts/' . $layout['style'] ); ?>
                            </div>
                            <?php if( $front_page_classic->current_post === johannes_get('ads', 'between_position') ) : ?>
                                <?php get_template_part( 'template-parts/ads/between-posts' ); ?>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <?php get_template_part( 'template-parts/archive/empty' ); ?>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>

                </div>
            </div>

            <?php if ( johannes_has_sidebar( 'right' ) ): ?>
                <div class="col-12 col-lg-4 johannes-order-3">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>

            <?php if ( johannes_get( 'pagination' ) ): ?>
                <?php $wp_query->max_num_pages = $front_page_classic->max_num_pages; ?>
                <?php get_template_part( 'template-parts/pagination/'. johannes_get( 'pagination' ) ); ?>
            <?php endif; ?>

        </div>
    </div>
</div>