<div class="johannes-section johannes-cover johannes-bg-alt-2 section-archive-2 size-johannes-archive-2">
    <?php if( $fimg = johannes_get_category_featured_image( 'johannes-archive-2' ) ) : ?>
    	<div class="section-bg"><?php echo wp_kses_post( $fimg ); ?></div>
	<?php endif; ?>
    <div class="container">
        <?php get_template_part( 'template-parts/archive/content' ); ?>
    </div>
</div>

<?php get_template_part( 'template-parts/ads/above-archive' ); ?>

<div class="johannes-section">
    <div class="container">
        <?php get_template_part( 'template-parts/archive/loop' ); ?>
    </div>
</div>