<div class="johannes-section johannes-cover johannes-bg-alt-2 section-archive-3 size-johannes-archive-3">
    <?php if( $fimg = johannes_get_category_featured_image( 'johannes-archive-3' ) ) : ?>
    	<div class="section-bg"><?php echo wp_kses_post( $fimg ); ?></div>
	<?php endif; ?>
    <div class="container">
    	<div class="section-head johannes-content-alt johannes-offset-bg section-head-alt single-md-content">
        	<?php get_template_part( 'template-parts/archive/content' ); ?>
        </div>
    </div>
</div>

<?php get_template_part( 'template-parts/ads/above-archive' ); ?>

<div class="johannes-section">
    <div class="container">
        <?php get_template_part( 'template-parts/archive/loop' ); ?>
    </div>
</div>