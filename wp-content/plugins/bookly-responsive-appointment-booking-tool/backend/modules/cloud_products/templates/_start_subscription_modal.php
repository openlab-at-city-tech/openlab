<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;

?>
<div id="bookly-product-start-subscription-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Activate Product', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="text-success font-weight-bold"><?php printf( __( 'You are about to activate %s', 'bookly' ), '<span class=\'bookly-js-product-name\'></span>' ) ?></label><br/><br/>
                    <?php esc_html_e( 'An amount for the selected plan will be immediately charged from your Bookly Cloud balance.', 'bookly' ) ?><br/>
                </div>
            </div>
            <div class="modal-footer">
                <?php Controls\Buttons::render( 'bookly-start-subscription-button', 'btn-danger', __( 'Continue', 'bookly' ) ) ?>
                <?php Controls\Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>