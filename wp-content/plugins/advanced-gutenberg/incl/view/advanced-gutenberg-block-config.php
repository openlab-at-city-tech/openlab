<?php
defined('ABSPATH') || die;

wp_nonce_field('advgb_block_config_nonce', 'advgb_block_config_nonce');
?>

<div id="advgb-loading-screen">
    <div id="advgb-loading-screen-image"></div>
</div>
<div class="block-config-modal-wrapper" style="display: none">
    <div class="block-config-modal-header clearfix">
        <h2 class="block-config-modal-title"><?php esc_html_e(' block', 'advanced-gutenberg'); ?></h2>
        <button class="ju-button orange-button block-config-save"><?php esc_html_e('Save', 'advanced-gutenberg') ?></button>
    </div>
    <div class="block-config-settings-wrapper">
        <input type="hidden" name="block-type" value="<?php echo esc_html($current_block) ?>" class="block-type-input">
        <?php $this->renderBlockConfigFields($current_block_settings, $current_block_settings_value); ?>
    </div>
    <div style="padding: 10px; text-align: center">
        <button class="ju-button orange-button block-config-save"><?php esc_html_e('Save', 'advanced-gutenberg') ?></button>
    </div>
</div>