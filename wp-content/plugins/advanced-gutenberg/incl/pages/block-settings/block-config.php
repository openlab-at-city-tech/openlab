<?php

defined('ABSPATH') || die;

wp_nonce_field('advgb_block_config_nonce', 'advgb_block_config_nonce');
?>
<div id="advgb-loading-screen">
    <div id="advgb-loading-screen-image"></div>
</div>
<div id="advgb-config-status"
     class="screen-reader-text"
     role="status"
     aria-live="polite"
     data-saving="<?php esc_attr_e('Saving... Do not close this window!', 'advanced-gutenberg'); ?>"
     data-saved="<?php esc_attr_e('Saved successfully!', 'advanced-gutenberg'); ?>"
     data-error="<?php esc_attr_e('Error: changes could not be saved.', 'advanced-gutenberg'); ?>"></div>
<div class="block-config-modal-wrapper wp-core-ui" style="display: none">
    <div class="block-config-modal-header clearfix">
        <button class="button button-primary block-config-save">
            <?php
            esc_html_e('Save', 'advanced-gutenberg') ?>
        </button>
        <h2 class="block-config-modal-title">
            <?php
            esc_html_e('block', 'advanced-gutenberg'); ?>
        </h2>
    </div>
    <div class="block-config-settings-wrapper">
        <input type="hidden" name="block-type" value="<?php
        echo esc_attr($current_block) ?>" class="block-type-input">
        <?php
        $this->renderBlockConfigFields($current_block_settings, $current_block_settings_value); ?>
    </div>
    <div class="block-config-bottom">
        <button class="button button-primary block-config-save">
            <?php
            esc_html_e('Save', 'advanced-gutenberg') ?>
        </button>
    </div>
</div>
