<?php if (!defined('ABSPATH')) {
die('No direct access.');
} ?>
<div class="schedule_placeholder">
    <?php if (metaslider_pro_is_installed()) : ?>
        <p style="mb-0 text-base"><?php esc_html_e('Update or activate your MetaSlider Pro now to add a start/end date option to your slides', 'ml-slider'); ?></p>
    <?php else : ?>
        <p style="text-base"><?php esc_html_e('Get MetaSlider Pro now to add a start/end date option to your slides', 'ml-slider'); ?></p>
        <a href="<?php echo esc_url(metaslider_get_upgrade_link()); ?>" class="button button-primary" target="_blank"><?php esc_html_e('Get it now!', 'ml-slider'); ?></a>
    <?php endif; ?>
</div>
