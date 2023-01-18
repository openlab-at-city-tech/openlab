<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
?>
<div id="bookly-product-unsubscribe-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Cancel subscription', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div id="bookly_cancel_subscription_method">
                <?php Controls\Inputs::renderRadioGroup( __( 'When do you want to cancel?', 'bookly' ), '', array(
                    'next_renewal' => array( 'title' => __( 'Cancel on next renewal', 'bookly' ) ),
                    'now'        => array( 'title' => __( 'Cancel immediately', 'bookly' ) ),
                ), 'next_renewal', array( 'name' => 'bookly_cancel_subscription_method' ) ) ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php Controls\Buttons::render( 'bookly-cancel-subscription', 'btn-danger', __( 'Cancel subscription', 'bookly' ) ) ?>
                <?php Controls\Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>