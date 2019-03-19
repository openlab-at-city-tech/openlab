<?php if( typology_can_display_ads() && $ad = typology_get_option('ad_top') ): ?>
    <div class="typology-ad typology-ad-top"><?php echo do_shortcode( $ad ); ?></div>
<?php endif; ?>
    