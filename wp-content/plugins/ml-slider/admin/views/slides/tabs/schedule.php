<?php if (!defined('ABSPATH')) {
die('No direct access.');
} ?>
<div class="schedule_placeholder">
    <?php if (metaslider_pro_is_installed()) : ?>
        <p style="mb-0 text-base"><?php esc_html_e('Update or activate your MetaSlider Pro now to add a start/end date option to your slides', 'ml-slider'); ?></p>
    <?php else : ?>
        <h1><?php esc_html_e('Get MetaSlider Pro', 'ml-slider'); ?></h1>
        <p><?php esc_html_e('With MetaSlider Pro, you can schedule your slides. You can choose to show your slides on specific dates, days of the week, or times of day.', 'ml-slider'); ?></p>
        <a href="<?php echo esc_url(metaslider_get_upgrade_link()); ?>" class="probutton button button-primary button-hero" target="_blank"><?php esc_html_e('Upgrade now', 'ml-slider'); ?> <span class='dashicons dashicons-external'></span></a>
    <?php endif; ?>
</div>
