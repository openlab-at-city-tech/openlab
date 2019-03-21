<?php get_header(); ?>

	<?php if( have_posts() ): ?>
		
		<?php while( have_posts() ) : the_post(); ?>
		
		<?php $meta = typology_get_page_meta(); ?>
        <?php $cover_class = !absint($meta['cover']) ? 'typology-cover-empty' : ''; ?>
        <div id="typology-cover" class="typology-cover <?php echo esc_attr($cover_class); ?>">
            <?php if(absint($meta['cover'])): ?>
	            <?php get_template_part('template-parts/cover/cover-page'); ?>
	            <?php if(typology_get_option( 'scroll_down_arrow' )): ?>
                    <a href="javascript:void(0)" class="typology-scroll-down-arrow"><i class="fa fa-angle-down"></i></a>
	            <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="typology-fake-bg">
            <div class="typology-section">
                <?php get_template_part('template-parts/ads/top'); ?>

                <?php get_template_part('template-parts/page/content'); ?>

                <?php comments_template(); ?>
                
                <?php get_template_part('template-parts/ads/bottom'); ?>
            </div>

		<?php endwhile; ?>

	<?php endif; ?>

<?php get_footer(); ?>