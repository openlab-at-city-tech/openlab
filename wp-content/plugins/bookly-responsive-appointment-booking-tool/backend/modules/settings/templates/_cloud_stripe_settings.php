<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Plugin;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Components\Controls\Elements;
use Bookly\Lib\Entities\Payment;
use Bookly\Backend\Modules\Appearance\Codes;
?>
<div class="card bookly-collapse-with-arrow" data-gateway="<?php echo esc_attr( $type ) ?>">
    <div class="card-header d-flex align-items-center">
        <?php Elements::renderReorder() ?>
        <a href="#bookly_pmt_cloud_stripe" class="ml-2" role="button" data-toggle="bookly-collapse">
            Stripe Cloud
        </a>
        <img class="ml-auto" src="<?php echo plugins_url( 'backend/modules/settings/resources/images/stripe.svg', Plugin::getMainFile() ) ?>"/>
    </div>
    <div id="bookly_pmt_cloud_stripe" class="bookly-collapse bookly-show">
        <div class="card-body">
            <?php Selects::renderSingle( 'bookly_cloud_stripe_enabled', null, null, array(), array( 'data-expand' => '1' ) ) ?>
            <div class="bookly_cloud_stripe_enabled-expander">
                <?php Components\Settings\Payments::renderPriceCorrection( Payment::TYPE_CLOUD_STRIPE ) ?>
                <?php
                $values = array( array( '0', __( 'OFF', 'bookly' ) ) );
                foreach ( array_merge(
                    range( 5 * MINUTE_IN_SECONDS, 55 * MINUTE_IN_SECONDS, 5 * MINUTE_IN_SECONDS ),
                    range( HOUR_IN_SECONDS, 23 * HOUR_IN_SECONDS, HOUR_IN_SECONDS ),
                    range( 24 * HOUR_IN_SECONDS, 168 * HOUR_IN_SECONDS, 24 * HOUR_IN_SECONDS ),
                    array( 336 * HOUR_IN_SECONDS, 504 * HOUR_IN_SECONDS, 672 * HOUR_IN_SECONDS )
                ) as $seconds ) {
                    $values[] = array( $seconds, DateTime::secondsToInterval( $seconds ) );
                }
                Selects::renderSingle( 'bookly_cloud_stripe_timeout', __( 'Time interval of payment gateway', 'bookly' ), __( 'This setting determines the time limit after which the payment made via the payment gateway is considered to be incomplete. This functionality requires a scheduled cron job.', 'bookly' ), $values );
                ?>
                <?php if ( ! \Bookly\Lib\Cloud\API::getInstance()->account->productActive( \Bookly\Lib\Cloud\Account::PRODUCT_CRON ) ) : ?>
                <div class="alert alert-info">
                    <div class="form-row">
                        <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
                        <div class="align-content-center">
                            <?php printf( __( 'To ensure stable operation of the "Time interval of payment gateway" feature, please activate %s or follow <a href="%s" target="_blank">the instructions</a> about cron setup.', 'bookly' ), '<a href="' . esc_url( admin_url( 'admin.php?page=bookly-cloud-products' ) ) . '">Bookly Cloud Cron</a>', 'https://support.booking-wp-plugin.com/hc/en-us/articles/360015017400-How-can-I-configure-CRON-to-send-the-Bookly-reminders' ) ?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <?php Selects::renderSingle( 'bookly_cloud_stripe_custom_metadata', __( 'Add custom metadata to payment', 'bookly' ), __( 'You can specify up to 50 keys, with key names up to 40 characters long and values up to 500 characters long. Key names can contain only letters, digits and spaces.', 'bookly' ), array(), array( 'data-expand' => '1' ) ) ?>
                <div class="form-group border-left mt-3 ml-4 pl-3 bookly_cloud_stripe_custom_metadata-expander">
                    <div id="bookly-cloud-stripe-metadata"></div>
                    <?php Components\Controls\Buttons::renderAdd( 'bookly-cloud-stripe-add-metadata', null, __( 'Add metadata', 'bookly' ) ) ?>
                    <div class="mt-3">
                        <?php echo Codes::getJson( '7', false, 'table' ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="bookly-stripe-metadata-template" style="display: none;">
        <div class="form-group bookly-js-metadata-row">
            <div class="d-flex">
                <div class="form-row col flex-fill px-0">
                    <div class="col-6">
                        <label><?php esc_html_e( 'Name', 'bookly' ) ?></label>
                        <input type="text" class="form-control bookly-js-meta-name" value="{{name}}"/>
                    </div>
                    <div class="col-6">
                        <label><?php esc_html_e( 'Value', 'bookly' ) ?></label>
                        <input type="text" class="form-control bookly-js-meta-value" value="{{value}}"/>
                    </div>
                </div>
                <div class="d-flex col px-0">
                    <button class="btn align-self-end bookly-js-delete-metadata"><i class="far fa-fw fa-trash-alt text-danger"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>