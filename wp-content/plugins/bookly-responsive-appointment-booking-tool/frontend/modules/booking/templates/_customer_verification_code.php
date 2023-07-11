<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-modal bookly-fade bookly-js-modal bookly-js-verification-code">
    <div class="bookly-modal-dialog">
        <div class="bookly-modal-content bookly-js-modal-content">
            <div class="bookly-modal-header">
                <div><?php esc_html_e( 'Verification code', 'bookly' ) ?></div>
                <button type="button" class="bookly-close bookly-js-close">×</button>
            </div>
            <div class="bookly-modal-body bookly-js-modal-body">
                <label id="bookly-verification-code-text"></label>
                <input type="text" id="bookly-verification-code">
            </div>
            <div class="bookly-modal-footer">
                <button class="bookly-btn-submit" type="submit"><?php esc_html_e( 'Verify', 'bookly' ) ?></button>
            </div>
        </div>
    </div>
</div>