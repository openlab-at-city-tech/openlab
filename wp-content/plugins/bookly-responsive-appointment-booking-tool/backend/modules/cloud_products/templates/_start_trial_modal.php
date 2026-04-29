<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
?>
<div id="bookly-product-start-trial-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Activate trial', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="text-success font-weight-bold"><?php printf( __( 'You are about to activate a 7-day Trial for %s', 'bookly' ), '<span class=\'bookly-js-product-name\'></span>' ) ?></label><br/><br/>
                    <?php esc_html_e( 'During the trial period, you will have full access to all features of this product - completely free of charge.', 'bookly' ) ?><br/><br/>
                    <?php esc_html_e( 'No payment will be taken at this stage.', 'bookly' ) ?>
                    <?php esc_html_e( 'After the Trial ends, the selected plan will be automatically billed from your Bookly Cloud balance.', 'bookly' ) ?><br/><br/>
                    <?php esc_html_e( 'Important: If you disable the Trial before it ends, it cannot be reactivated. To continue using the product, activate the selected plan.', 'bookly' ) ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php Controls\Buttons::render( 'bookly-start-trial-button', 'btn-danger', __( 'Continue', 'bookly' ) ) ?>
                <?php Controls\Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>