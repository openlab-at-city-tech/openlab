<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Frontend\Components\Payment\Gateway;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Utils\Common;
echo Common::stripScripts( $progress_tracker );
Proxy\Coupons::renderPaymentStep( $userData );
Proxy\Pro::renderPaymentStep( $userData );
Proxy\DepositPayments::renderPaymentStep( $userData );
?>
<div class="bookly-payment-nav">
    <div class="bookly-box"><?php echo Common::html( $info_text ) ?></div>
    <?php if ( isset( $payment_options['free'] ) ) : ?>
        <div class="bookly-box bookly-list" style="display: none">
            <input type="radio" class="bookly-js-payment" name="payment-method-<?php echo esc_attr( $form_id ) ?>" value="free"/>
        </div>
    <?php endif ?>
    <?php foreach ( $payment_options as $payment_option ) : ?>
        <?php echo Common::html( $payment_option ) ?>
    <?php endforeach ?>
</div>
<?php Proxy\RecurringAppointments::renderInfoMessage( $userData ) ?>
<?php foreach ( $payment_options as $gateway_slug => $data ) {
    Gateway::renderForm( $form_id, $gateway_slug );
} ?>

