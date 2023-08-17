<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;

?>
<div id="bookly-product-unsubscribe-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Cancel subscription', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div id="bookly_cancel_subscription_method">
                    <div class="form-group">
                        <label><?php esc_html_e( 'When do you want to cancel?', 'bookly' ) ?></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="bookly-cancel-subscription-on-renewal" checked="checked" value="next_renewal" name="bookly_cancel_subscription_method" class="custom-control-input">
                            <label class="custom-control-label" for="bookly-cancel-subscription-on-renewal"><?php esc_html_e( 'Cancel on next renewal', 'bookly' ) ?></label>
                        </div>
                        <small class="text-muted form-text ml-4"><?php esc_html_e( 'You will be able to use the product until the end of the already paid period. If during this period you decide to keep your subscription, you will have the option to do so.', 'bookly' ) ?></small>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" id="bookly-cancel-subscription-now" value="now" name="bookly_cancel_subscription_method" class="custom-control-input">
                            <label class="custom-control-label" for="bookly-cancel-subscription-now"><?php esc_html_e( 'Cancel immediately', 'bookly' ) ?></label></div>
                        <small class="text-muted form-text ml-4"><?php esc_html_e( 'The product will be disabled immediately. To re-enable the product, you will need to sign up for a new subscription.', 'bookly' ) ?></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php Controls\Buttons::render( 'bookly-cancel-subscription', 'btn-danger', __( 'Cancel subscription', 'bookly' ) ) ?>
                <?php Controls\Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>