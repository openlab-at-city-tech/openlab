<div class="johannes-section category-pill johannes-cover johannes-bg-alt-2 johannes-section-margin-alt size-johannes-single-3">
    
    <?php if ( johannes_get( 'fimg' ) && $fimg = johannes_get_featured_image( 'johannes-single-3', true ) ): ?>
            <div class="section-bg">
            <?php echo wp_kses_post( $fimg ); ?>
            <?php if ( johannes_get( 'fimg_cap' ) && $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) : ?>
            <figure class="wp-caption-text">
                <?php echo wp_kses_post( $caption );  ?>
            </figure>
            <?php endif; ?>
            </div>
    <?php endif; ?>
    
    <div class="container">
        <div class="section-head johannes-content-alt section-head-alt">
             <?php echo johannes_breadcrumbs(); ?>
             <?php if ( johannes_get( 'category' ) ): ?>
                <div class="entry-category">
                    <?php echo johannes_get_category(); ?>
                </div>
            <?php endif; ?>
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <?php if ( johannes_get( 'meta' ) ): ?>
                <div class="entry-meta">
                    <?php echo johannes_get_meta_data( johannes_get( 'meta' ) ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_template_part( 'template-parts/ads/above-singular' ); ?>

<?php get_template_part('template-parts/single/content'); ?>