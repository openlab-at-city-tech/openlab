<?php $url = M_Marketing::get_utm_link('https://www.imagely.com/nextgen-gallery/', 'igw', 'wantmorelayouts' ); ?>
<div class="ngg_igw_promo">
    <p><?php esc_html_e('Want More Layouts?', 'nggallery'); ?></p>
    <p>
        <a class="wp-block-button__link has-text-color has-background no-border-radius"
           href="<?php print esc_attr($url); ?>"
           target="_blank"
           rel="noreferrer noopener">
            <?php esc_html_e('Upgrade to NextGEN Pro', 'nggallery') ?>
        </a>
    </p>
    <p class='coupon'>
        <?php esc_html_e("Get 20% Off Now!", 'nggallery') ?>
    </p>
</div>