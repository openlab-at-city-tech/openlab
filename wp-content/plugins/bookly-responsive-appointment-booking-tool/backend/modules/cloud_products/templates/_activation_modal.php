<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-product-activation-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body text-center">
                <div class="bookly-js-success">
                    <h3 class="text-success pb-0 mb-0"><?php esc_html_e( 'Congrats!', 'bookly' ) ?></h3>
                    <div class="text-success py-5">
                        <i class="mx-auto bookly-success-icon"></i>
                    </div>
                </div>
                <div class="bookly-js-fail">
                    <h3 class="text-danger pb-0 mb-0"><?php esc_html_e( 'Oops!', 'bookly' ) ?></h3>
                    <div class="text-danger py-5">
                        <i class="mx-auto bookly-fail-icon"></i>
                    </div>
                </div>
                <div class="bookly-js-content"></div>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit( null, 'bookly-js-action-btn', '' ) ?>
                <?php Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>