<?php get_template_part( 'template-parts/ads/above-singular' ); ?>

<div class="johannes-section johannes-section-margin-alt">
    <div class="container">
        <div class="section-head johannes-content-alt category-pill section-head-alt section-head-alt-post">
            <?php echo johannes_breadcrumbs(); ?>
            <?php if ( johannes_get( 'category' ) ): ?>
                <div class="entry-category">
                    <?php echo johannes_get_category(); ?>
                </div>
            <?php endif; ?>
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <?php if ( johannes_get( 'meta' ) ): ?>
            <div class="entry-meta justify-content-sm-start justify-content-md-center">
                <?php echo johannes_get_meta_data( johannes_get( 'meta' ) ); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php echo johannes_get_media( johannes_get_post_format(),  '<div class="entry-media mb-0">', '</div>'); ?>

    </div>
</div>

<?php get_template_part('template-parts/single/content'); ?>