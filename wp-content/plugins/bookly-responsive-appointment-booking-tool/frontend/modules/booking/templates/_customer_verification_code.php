<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-modal bookly-fade bookly-js-modal bookly-js-verification-code">
    <div class="bookly-modal-dialog">
        <div class="bookly-modal-content bookly-js-modal-content">
            <div class="bookly-modal-header">
                <div><?php esc_html_e( 'Verification code', 'bookly' ) ?></div>
                <button type="button" class="bookly-close bookly-js-close" style="margin-top: -25px; font-size: 21px; line-height: 1;">×</button>
            </div>
            <div class="bookly-modal-body bookly-js-modal-body">
                <label for="bookly-verification-code" id="bookly-verification-code-text"></label>
                <input type="text" id="bookly-verification-code">
                <div class="bookly-js-verification-code-error bookly-label-error"></div>
            </div>
            <div class="bookly-modal-footer">
                <button class="bookly-btn-submit bookly-js-resend-button" type="submit" disabled><?php esc_html_e( 'Resend', 'bookly' ) ?><span class="bookly-js-resend-timer bookly-font-mono"></span></button>
                <button class="bookly-btn-submit bookly-js-verify-button" type="submit"><?php esc_html_e( 'Verify', 'bookly' ) ?></button>
            </div>
        </div>
    </div>
</div>