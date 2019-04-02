<?php if( typology_can_display_ads() && $ad = typology_get_option('ad_bottom') ): ?>
    <div class="typology-ad typology-ad-bottom"><?php echo do_shortcode( $ad ); ?></div>
<?php endif; ?>