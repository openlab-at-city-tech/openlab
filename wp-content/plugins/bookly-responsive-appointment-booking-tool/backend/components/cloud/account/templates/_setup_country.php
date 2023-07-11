<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div class="bookly-modal bookly-fade" id="bookly-setup-country" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Setup your country', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <p>
                        <?php esc_html_e( 'Please spend a minute to setup your country. This will help us provide you with appropriate payment methods when replenishing your account.', 'bookly' ) ?>
                    </p>
                    <p class="mb-0">
                        <?php esc_html_e( 'The country will also be displayed in the invoice on a separate line below the company address. Make sure the other fields in the invoice do not contain the name of the country.', 'bookly' ) ?>
                    </p>
                </div>
                <div class="form-group mt-2">
                    <select id="bookly-s-country"></select>
                    <small class="text-muted"><?php esc_html_e( 'Your country is the location from where you consume Bookly SMS services and is used to provide you with the payment methods available in that country', 'bookly' ) ?></small>
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit( 'bookly-set-country', null, __( 'Set country', 'bookly' ) ) ?>
                <?php Buttons::renderCancel( __( 'I\'ll do it later', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>
