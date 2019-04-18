<?php get_template_part( 'template-parts/ads/above-singular' ); ?>

<div class="johannes-section johannes-section-margin-alt">
    <div class="container">
        <?php echo johannes_get_media( johannes_get_post_format(),  '<div class="entry-media mb-0">', '</div>'); ?>
    </div>
</div>

<?php get_template_part('template-parts/single/content'); ?>