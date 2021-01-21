<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="b2s-plugin-modal" id="b2s-plugin-deactivate-modal" aria-hidden="true" style="display:none;">
    <div class="b2s-plugin-modal-dialog">
        <div class="b2s-plugin-modal-header">
            <a href="#" class="b2s-plugin-modal-btn-close" data-modal-target="b2s-plugin-deactivate-modal" aria-hidden="true">×</a>
            <h4 class="b2s-plugin-modal-title"><?php echo esc_html_e("Do you want to delete your scheduled posts?","blog2social"); ?></h4>
        </div>
        <div class="b2s-plugin-modal-body">
            <p><?php echo esc_html_e("Do you want Blog2Social to delete all your scheduled social media posts? Your scheduled posts will no longer be sent to your social networks.","blog2social"); ?></p>
            <p><input type="checkbox" value="1" id="b2s-plugin-deactivate-checkbox-sched-post"> <?php echo esc_html_e("Delete scheduled posts","blog2social"); ?></p>
        </div>
        <div class="b2s-plugin-modal-footer">
            <button id="b2s-plugin-deactivate-btn" class="b2s-btn b2s-btn-primary"><?php echo esc_html_e("Continue deactivation","blog2social"); ?></button>
            <input type="hidden" id="b2s-plugin-deactivate-redirect-url" value="">
            <?php wp_nonce_field('b2s_deactivate_nonce', 'b2s_deactivate_nonce'); ?>
        </div>
    </div>
</div>
