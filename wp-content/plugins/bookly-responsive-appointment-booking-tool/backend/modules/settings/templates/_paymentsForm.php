<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Config;
use Bookly\Lib\Utils\Price;
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib\Utils\Common;
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'payments' ) ) ?>">
    <div class="card-body">
        <div class="form-row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="bookly_pmt_currency"><?php esc_html_e( 'Currency', 'bookly' ) ?></label>
                    <select id="bookly_pmt_currency" class="form-control custom-select" name="bookly_pmt_currency">
                        <?php foreach ( Price::getCurrencies() as $code => $currency ) : ?>
                            <option value="<?php echo esc_attr( $code ) ?>" data-symbol="<?php esc_attr_e( $currency['symbol'] ) ?>" <?php selected( Config::getCurrency(), $code ) ?> ><?php echo esc_html( $code ) ?> (<?php esc_html_e( $currency['symbol'] ) ?>)</option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="bookly_pmt_price_format"><?php esc_html_e( 'Price format', 'bookly' ) ?></label>
                    <select id="bookly_pmt_price_format" class="form-control custom-select" name="bookly_pmt_price_format">
                        <?php foreach ( Price::getFormats() as $format ) : ?>
                            <option value="<?php echo esc_attr( $format ) ?>" <?php selected( get_option( 'bookly_pmt_price_format' ), $format ) ?> ></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
        <?php Proxy\DepositPayments::renderPayments() ?>
        <?php Proxy\Taxes::renderPayments() ?>
        <div id="bookly-payment-systems">
            <?php foreach ( $payments as $payment ) : ?>
                <?php echo Common::stripScripts( $payment ) ?>
            <?php endforeach ?>
        </div>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <input type="hidden" name="bookly_pmt_order" value="<?php echo get_option( 'bookly_pmt_order' ) ?>"/>
        <?php Inputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( 'bookly-payments-reset', 'ml-2' ) ?>
    </div>
</form>