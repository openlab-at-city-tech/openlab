<?php $related = johannes_get_related(); ?>
<?php if ( $related->have_posts() ) : ?>
    <div class="johannes-section johannes-related section-margin johannes-bg-alt-2">
        <div class="container">
            <div class="section-head">
                <h5 class="section-title h2"><?php echo __johannes( 'related' ); ?></h5>
            </div>
            <div class="section-content row justify-content-center">
                <div class="col-12 <?php echo esc_attr( johannes_get_loop_col_class( johannes_get( 'related_layout' ) ) ); ?>">
                    <div class="row johannes-items johannes-posts">
                        <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                        <?php $layout = johannes_get_loop_params( johannes_get( 'related_layout' ), $related->current_post ); ?>
                        <div class="<?php echo esc_attr( $layout['col'] ); ?>">
                            <?php get_template_part( 'template-parts/layouts/' . $layout['style'] ); ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php wp_reset_postdata(); ?>