<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Frontend\Components\Payment\Gateway;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\Utils\Common;
echo Common::stripScripts( $progress_tracker );
Proxy\Coupons::renderPaymentStep( $userData );
Proxy\Pro::renderPaymentStep( $userData );
Proxy\DepositPayments::renderPaymentStep( $userData );
?>

<div class="bookly-payment-nav">
    <div class="bookly-box"><?php echo Common::html( $info_text ) ?></div>
    <div class="bookly-box bookly-list" style="display: none">
        <input type="radio" class="bookly-js-coupon-free" name="payment-method-<?php echo esc_attr( $form_id ) ?>" value="coupon"/>
    </div>
    <?php foreach ( $payment_options as $payment_option ) : ?>
        <?php echo Common::stripScripts( $payment_option ) ?>
    <?php endforeach ?>
</div>
<?php if ( $payment_options ) : ?>
    <?php Proxy\RecurringAppointments::renderInfoMessage( $userData ) ?>

    <?php if ( isset( $payment_options['local'] ) ) : ?>
        <div class="bookly-gateway-buttons pay-local bookly-box bookly-nav-steps">
            <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
                <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
            </button>
            <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
                <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
                    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_payment_button_next' ) ?></span>
                </button>
            </div>
        </div>
    <?php endif ?>

    <div class="bookly-gateway-buttons pay-card bookly-box bookly-nav-steps" style="display:none">
        <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
        </button>
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
            <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
                <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_payment_button_next' ) ?></span>
            </button>
        </div>
    </div>

    <?php if ( isset( $payment_options[ Payment::TYPE_CLOUD_STRIPE ] ) ) : ?>
        <?php Gateway::renderForm( $form_id, Payment::TYPE_CLOUD_STRIPE, $page_url ) ?>
    <?php endif ?>

    <?php Proxy\Shared::renderPaymentForms( $form_id, $page_url ) ?>
<?php endif ?>

<div class="bookly-gateway-buttons pay-coupon bookly-box bookly-nav-steps" <?php if ( $payment_options ) : ?>style="display: none"<?php endif ?>>
    <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
        <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
    </button>
    <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
        <button class="bookly-next-step bookly-js-next-step bookly-js-coupon-payment bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_payment_button_next' ) ?></span>
        </button>
    </div>
</div>
