<?php get_template_part( 'template-parts/ads/above-singular' ); ?>

<div class="johannes-section johannes-section-margin-alt">
    <div class="container">
        <div class="section-head johannes-content-alt section-head-alt section-head-alt-page">
            <?php echo johannes_breadcrumbs(); ?>
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </div>

        <?php if ( johannes_get( 'fimg' ) && $fimg = johannes_get_featured_image( 'johannes-page-1', true ) ): ?>
            <div class="entry-media mb-0">
                <?php echo wp_kses_post( $fimg ); ?>
                <?php if ( johannes_get( 'fimg_cap' ) && $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) : ?>
                <figure class="wp-caption-text">
                    <?php echo wp_kses_post( $caption );  ?>
                </figure>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_template_part('template-parts/page/content'); ?>