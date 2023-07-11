<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Editable\Elements;
use Bookly\Backend\Modules\Appearance\Codes;
use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Lib\Config;
/** @var array $payment_options */
?>
<div class="bookly-form">
    <?php include '_progress_tracker.php' ?>
    <div class="bookly-js-payment-gateways-intersected">
        <?php Proxy\Coupons::renderCouponBlock() ?>
        <?php Proxy\Pro::renderGiftCards() ?>
        <?php Proxy\Pro::renderTips() ?>
        <?php Proxy\DepositPayments::renderAppearance() ?>
        <div class="bookly-payment-nav">
            <div class="bookly-box bookly-js-payment-single-app">
                <?php Elements::renderText( 'bookly_l10n_info_payment_step_single_app', Codes::getJson( 7 ) ) ?>
            </div>
            <?php Proxy\Pro::renderBookingStatesText() ?>
            <div class="bookly-js-payment-gateways">
                <?php foreach ( $payment_options as $slug => $gateway ) : ?>
                    <div class="bookly-box bookly-list">
                        <label>
                            <input type="radio" name="payment" id="bookly-card-payment"/>
                            <?php Elements::renderString( array( $gateway['label_option_name'], ), $gateway['title'] ) ?>
                            <?php if ( $gateway['logo_url'] ) : ?>
                                <img src="<?php echo esc_attr( $gateway['logo_url'] ) ?>" alt="<?php echo esc_attr( $gateway['title'] ) ?>"/>
                            <?php endif ?>
                        </label>
                        <?php if ( $gateway['with_card'] ) : ?>
                            <form class="bookly-card-form bookly-clear-bottom" style="margin-top:15px;display: none;">
                                <div class="bookly-box bookly-table">
                                    <div class="bookly-form-group" style="width:200px!important">
                                        <label>
                                            <?php Elements::renderString( array( 'bookly_l10n_label_ccard_number', ) ) ?>
                                        </label>
                                        <div>
                                            <input type="text"/>
                                        </div>
                                    </div>
                                    <div class="bookly-form-group">
                                        <label>
                                            <?php Elements::renderString( array( 'bookly_l10n_label_ccard_expire', ) ) ?>
                                        </label>
                                        <div>
                                            <select class="bookly-card-exp">
                                                <?php for ( $i = 1; $i <= 12; ++$i ) : ?>
                                                    <option value="<?php echo esc_attr( $i ) ?>"><?php printf( '%02d', $i ) ?></option>
                                                <?php endfor ?>
                                            </select>
                                            <select class="bookly-card-exp">
                                                <?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; ++$i ) : ?>
                                                    <option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bookly-box bookly-clear-bottom">
                                    <div class="bookly-form-group">
                                        <label>
                                            <?php Elements::renderString( array( 'bookly_l10n_label_ccard_code', ) ) ?>
                                        </label>
                                        <div>
                                            <input class="bookly-card-cvc" type="text"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php endif ?>
                        <form class="bookly-card-form bookly-clear-bottom" style="display: none;">
                            <?php Proxy\Shared::renderGatewayOptions( $slug ) ?>
                        </form>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <?php Proxy\RecurringAppointments::renderInfoMessage() ?>
    </div>
    <?php Proxy\Pro::renderPaymentImpossible() ?>
    <div class="bookly-box bookly-nav-steps">
        <div class="bookly-back-step bookly-js-back-step bookly-btn">
            <?php Elements::renderString( array( 'bookly_l10n_button_back' ) ) ?>
        </div>
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
            <div class="bookly-next-step bookly-js-next-step bookly-btn">
                <?php if ( Config::customJavaScriptActive() ): ?>
                    <?php Proxy\CustomJavaScript::renderNextButton( 'payment' ) ?>
                <?php else: ?>
                    <?php Elements::renderString( array( 'bookly_l10n_step_payment_button_next' ) ) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>