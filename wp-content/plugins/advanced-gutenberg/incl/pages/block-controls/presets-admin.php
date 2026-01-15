<?php
// Admin page for preset management
?>
<div class="wrap">
    <div class="advgb-admin-presets-container">
        <div id="advgb-admin-preset-manager">
            <div class="advgb-preset-loading-container">
                <div class="advgb-preset-loading-spinner">
                    <div class="advgb-spinner">
                        <div class="advgb-spinner-circle"></div>
                    </div>
                    <p><?php _e('Loading presets...', 'advanced-gutenberg'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize the preset manager for admin page
    if (window.AdvGBPresetManager) {
        const container = document.getElementById('advgb-admin-preset-manager');
        if (container) {
            wp.element.render(
                wp.element.createElement(window.AdvGBPresetManager, { isModal: false }),
                container
            );
        }
    }
});
</script>