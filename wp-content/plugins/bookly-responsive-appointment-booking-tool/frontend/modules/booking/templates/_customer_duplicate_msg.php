<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-modal bookly-fade bookly-js-modal bookly-js-cst-duplicate">
    <div class="bookly-modal-dialog">
        <div class="bookly-modal-content bookly-js-modal-content">
            <div class="bookly-modal-header">
                <div><?php esc_html_e( 'Data already in use', 'bookly' ) ?></div>
                <button type="button" class="bookly-close bookly-js-close" style="margin-top: -25px; font-size: 21px; line-height: 1;">×</button>
            </div>
            <div class="bookly-modal-body bookly-js-modal-body"></div>
            <div class="bookly-modal-footer">
                <button class="bookly-btn-submit" type="submit"><?php esc_html_e( 'Update' ) ?></button>
                <a href="#" class="bookly-btn-cancel bookly-js-close"><?php esc_html_e( 'Cancel' ) ?></a>
            </div>
        </div>
    </div>
</div>