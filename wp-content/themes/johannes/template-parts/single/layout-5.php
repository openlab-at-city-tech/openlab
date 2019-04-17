<?php get_template_part( 'template-parts/ads/above-singular' ); ?>

<div class="johannes-section johannes-section-margin-alt johannes-section-margin-alt-2 single-layout-5">
    <div class="container">
        <div class="row category-pill align-items-center">
            <div class="col-12 col-md-5">
                <?php if ( $fimg = johannes_get_featured_image( 'johannes-single-5' ) ): ?>
                    <div class="entry-media media-shadow">
                        <?php echo wp_kses_post( $fimg ); ?>
                        <?php if ( johannes_get( 'fimg_cap' ) && $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) : ?>
                            <figure class="wp-caption-text">
                                <?php echo wp_kses_post( $caption );  ?>
                            </figure>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6 offset-md-1 section-head-alt ">
                <div class="entry-header">
                    <?php echo johannes_breadcrumbs(); ?>
                    <?php if ( johannes_get( 'category' ) ): ?>
                        <div class="entry-category">
                            <?php echo johannes_get_category(); ?>
                        </div>
                    <?php endif; ?>

                    <?php the_title( '<h1 class="entry-title display-1">', '</h1>' ); ?>
                    
                    <?php if ( johannes_get( 'meta' ) ): ?>
                        <div class="entry-meta">
                            <?php echo johannes_get_meta_data( johannes_get( 'meta' ) ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_template_part('template-parts/single/content'); ?>