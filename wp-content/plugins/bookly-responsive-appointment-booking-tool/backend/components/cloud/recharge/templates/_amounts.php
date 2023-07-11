<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Cloud\Recharge\Amounts;

$amounts = Amounts::getInstance();
?>
<div class="text-center mt-3">
    <div class="btn-group">
        <button type="button" class="btn btn-bookly btn-lg bookly-js-auto-recharges-btn" style="box-shadow: none;"><?php esc_html_e( 'Auto-Recharge' ) ?></button>
        <button type="button" class="btn btn-default btn-lg bookly-js-manual-recharges-btn" style="box-shadow: none;"><?php esc_html_e( 'One-time payment' ) ?></button>
    </div>
</div>

<div class="bookly-js-auto-recharge-text">
    <?php if ( ! $cloud->account->autoRechargeEnabled() ) : ?>
        <h4 class="text-center mt-3"><?php esc_html_e( 'Please select an amount and enable Auto-Recharge', 'bookly' ) ?></h4>
    <?php endif ?>
    <div class="mb-3 mt-4">
        <div class="text-center">
            <a class="text-muted" style="text-decoration:underline dotted" data-toggle="bookly-collapse" href="#how-auto-recharge-works">
                <?php esc_html_e( 'How it works', 'bookly' ) ?> <i class="fas fa-question-circle"></i>
            </a>
        </div>
        <div class="bookly-collapse alert alert-info text-justify mx-5" id="how-auto-recharge-works">
            <?php printf( __( 'Your account will be topped up with the selected amount <b>now</b> if your balance is less than %1$s, and <b>automatically later</b> when the balance falls below %1$s.', 'bookly' ), '$10' ) ?>
        </div>
    </div>
</div>

<div class="bookly-js-manual-recharge-text">
    <h4 class="text-center mt-3 mb-4"><?php esc_html_e( 'Please select an amount and recharge your account', 'bookly' ) ?></h4>
</div>

<div class="form-row bookly-js-manual-recharges mt-4" style="display: none;">
    <?php foreach ( $amounts->getItems( Amounts::RECHARGE_TYPE_MANUAL ) as $recharge ) : ?>
        <div class="col-12 col-md-6 col-lg-4">
            <?php self::renderTemplate( '_button', array( 'recharge' => $recharge, 'type' => Amounts::RECHARGE_TYPE_MANUAL ) ) ?>
        </div>
    <?php endforeach ?>
</div>

<div class="form-row bookly-js-auto-recharges mt-4">
    <?php foreach ( $amounts->getItems( Amounts::RECHARGE_TYPE_AUTO ) as $recharge ) : ?>
        <div class="col-12 col-md-6 col-lg-4">
            <?php self::renderTemplate( '_button', array( 'recharge' => $recharge, 'type' => Amounts::RECHARGE_TYPE_AUTO, 'cloud' => $cloud ) ) ?>
        </div>
    <?php endforeach ?>
</div>
<div class="row my-3 text-center" style="color:#595959">
    <div class="col"><i class="fab fa-2x fa-cc-paypal"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-mastercard"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-visa"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-amex"></i></div>
    <div class="col"><i class="fab fa-2x fa-cc-discover"></i></div>
</div>