<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<form id="bookly-confirm-email-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Thank you for registration.', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                    <?php esc_html_e( 'You\'re almost ready to get started with Bookly Cloud.', 'bookly' ) ?>
                    <?php esc_html_e( 'An email containing the confirmation code has been sent to your email address.', 'bookly' ) ?>
                </p>
                <p><?php esc_html_e( 'To complete registration, please enter the confirmation code below.', 'bookly' ) ?></p>
                <div class="input-group mb-4">
                    <input type="text" class="form-control bookly-js-confirmation-code" id="bookly-confirmation-code" placeholder="<?php esc_attr_e( 'Confirmation code', 'bookly' ) ?>" />
                    <div class="input-group-append">
                        <?php Buttons::renderSubmit( 'bookly-apply-confirmation-code', null, __( 'Confirm', 'bookly' ), array( 'name' => 'submit' ) ) ?>
                    </div>
                </div>
                <h6>
                    <b><?php esc_html_e( 'Didn\'t receive the email?', 'bookly' ) ?></b>
                </h6>
                <ol>
                    <li>
                        <?php esc_html_e( 'Check your spam folder.', 'bookly' ) ?>
                    </li>
                    <li>
                        <?php printf( esc_html__( 'Click %s here %s to resend the email.', 'bookly' ), '<a href="#" class="bookly-js-resend-confirmation">', '</a>' ) ?>
                    </li>
                </ol>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderCancel( __( 'I\'ll do it later', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</form>