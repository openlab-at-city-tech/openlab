<div class="johannes-section">
    <div class="container">
        
        <?php if ( johannes_get( 'fa_display_title' ) ): ?>
            <div class="section-head">
                <h3 class="section-title h2"><?php echo esc_html( __johannes('front_page_featured_title') ); ?></h3>
            </div>
        <?php endif; ?>

        <div class="section-content row <?php echo esc_attr( johannes_get_fa_class( johannes_get( 'fa_loop' ) ) ); ?> justify-content-md-center">
          
            <?php $front_page_fa = new WP_Query( johannes_get( 'fa_query_args' ) ); ?>
            
            <?php if ( $front_page_fa->have_posts() ) : ?>
                <?php while ( $front_page_fa->have_posts() ) : $front_page_fa->the_post(); ?>
                    <?php $layout = johannes_get_loop_params( johannes_get( 'fa_loop' ), $front_page_fa->current_post, 'fa' ); ?>
                    <div class="<?php echo esc_attr( $layout['col'] ); ?>">
                        <?php get_template_part( 'template-parts/layouts/' . $layout['style'] ); ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>
    </div>
</div>