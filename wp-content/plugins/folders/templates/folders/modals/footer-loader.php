<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<div class="folders-wrap">
    <div class="folder-loader" id="folder-loader">
        <div class="folder-loader-wrap folders-flex items-center gap-2" id="folder-loader-wrap">
            <div class="loading-status">
                <div class="loading-spinner"></div>
                <div class="loading-checkmark"></div>
            </div>
            <div class="message" id="loading-message">
                <div class="loader-message">
                    <?php esc_html_e("Loading...", 'folders'); ?>
                </div>
                <div class="success-message">
                    <?php esc_html_e("Successfully updated!", 'folders'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
