<?php
namespace Bookly\Backend\Modules\CloudProducts;

use Bookly\Backend\Modules\Settings\Page as SettingsPage;
use Bookly\Backend\Modules\CloudSMS\Page as CloudSMSPage;
use Bookly\Backend\Modules\CloudZapier\Page as CloudZapierPage;
use Bookly\Backend\Modules\CloudVoice\Page as CloudVoicePage;
use Bookly\Backend\Modules\CloudWhatsapp\Page as CloudWhatsAppPage;
use Bookly\Backend\Modules\CloudMobileStaffCabinet\Page as CloudMobileStaffCabinetPage;
use Bookly\Backend\Modules\Services\Page as ServicesPage;
use Bookly\Lib;
use Bookly\Lib\Cloud\Account;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    /**
     * Get cloud products info.
     */
    public static function cloudGetProductInfo()
    {
        $info = Lib\Cloud\API::getInstance()->general->getProductInfo( self::parameter( 'product' ) );

        if ( $info ) {
            wp_send_json_success( array( 'html' => $info ) );
        }

        wp_send_json_error();
    }

    /**
     * Enable/disable SMS Notifications
     */
    public static function cloudSmsChangeStatus()
    {
        $status = self::parameter( 'status' );

        $cloud = Lib\Cloud\API::getInstance();
        if ( $cloud->getProduct( Account::PRODUCT_SMS_NOTIFICATIONS )->changeSmsStatus( $status ) ) {
            wp_send_json_success( array(
                'redirect_url' => add_query_arg(
                        array( 'page' => Page::pageSlug() ),
                        admin_url( 'admin.php' ) ) . '#cloud-product=sms&status=activated',
            ) );
        } else {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        }
    }

    /**
     * Revert cancel subscription
     */
    public static function cloudRevertCancelSubscription()
    {
        $product = Lib\Cloud\API::getInstance()->getProduct( self::parameter( 'product' ) );

        if ( isset( $product ) && ! $product->revertCancel() ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        }
        wp_send_json_success();
    }

    /**
     * Enable/disable Stripe Cloud
     */
    public static function cloudStripeChangeStatus()
    {
        $status = self::parameter( 'status' );
        $api = Lib\Cloud\API::getInstance();
        if ( $status ) {
            $redirect_url = $api->getProduct( Account::PRODUCT_STRIPE )->connect();
            if ( $redirect_url !== false ) {
                wp_send_json_success( compact( 'redirect_url' ) );
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        } elseif ( $api->getProduct( Account::PRODUCT_STRIPE )->disconnect() ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }
    }

    /**
     * Change product status
     *
     * @return void
     */
    public static function cloudChangeProductStatus()
    {
        $product_slug = self::parameter( 'product' );
        $product = Lib\Cloud\API::getInstance()->getProduct( $product_slug );
        $status = self::parameter( 'status' );
        switch ( $product_slug ) {
            case $product instanceof Lib\Cloud\ProductX:
            case Lib\Cloud\Account::PRODUCT_VOICE:
                $status = $status ?: 'now';
                break;
        }
        if ( $status === '1' ) {
            $response = $product->activate( self::parameter( 'product_price' ), self::parameter( 'purchase_code' ) );
            if ( $response !== false ) {
                wp_send_json_success( array(
                    'redirect_url' => add_query_arg( array( 'page' => Page::pageSlug() ), admin_url( 'admin.php' ) ) . '#cloud-product=' . $product_slug . '&status=activated',
                ) );
            } else {
                $errors = Lib\Cloud\API::getInstance()->getErrors();
                $data = array( 'message' => current( $errors ) );
                if ( array_key_exists( 'ERROR_LOW_BALANCE', $errors ) ) {
                    $data['offer_to_top_up_balance'] = true;
                }

                wp_send_json_error( $data );
            }
        } elseif ( $product->deactivate( $status ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        }
    }

    /**
     * Get text for 'product activation' modal
     */
    public static function cloudGetProductActivationMessage()
    {
        $product = self::parameter( 'product' );
        $status = self::parameter( 'status' );
        if ( $product === Account::PRODUCT_STRIPE && $status === 'cancelled' ) {
            wp_send_json_error( array( 'content' => __( 'Stripe activation was not completed', 'bookly' ) ) );
        }
        $api = Lib\Cloud\API::getInstance();
        $texts = $api->account->getProductActivationTexts( self::parameter( 'product' ) );
        if ( $texts ) {
            $data = array(
                'content' => $texts['message'],
            );
            switch ( $product ) {
                case Account::PRODUCT_SMS_NOTIFICATIONS:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => CloudSMSPage::pageSlug() ), admin_url( 'admin.php' ) ),
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_2CHECKOUT:
                case Account::PRODUCT_AUTHORIZE_NET:
                case Account::PRODUCT_MOLLIE:
                case Account::PRODUCT_PAYSON:
                case Account::PRODUCT_PAYU_LATAM:
                case Account::PRODUCT_SQUARE:
                case Account::PRODUCT_STRIPE:
                case Account::PRODUCT_STRIPE_CLASSIC:
                    if ( $status === 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'payments' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
                    break;
                case Account::PRODUCT_CART:
                    if ( $status === 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'cart' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
                    break;
                case Account::PRODUCT_COUPONS:
                    if ( $status === 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => 'bookly-coupons' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
                    break;
                case Account::PRODUCT_CUSTOM_FIELDS:
                    if ( $status === 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => 'bookly-custom-fields' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
                    break;
                case Account::PRODUCT_GROUP_BOOKING:
                    if ( $status === 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'group_booking' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
                    break;
                case Account::PRODUCT_GIFT:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => 'bookly-cloud-gift-cards', 'tab' => 'card-types' ), admin_url( 'admin.php' ) ),
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_ZAPIER:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => CloudZapierPage::pageSlug() ), admin_url( 'admin.php' ) ),
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_VOICE:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => CloudVoicePage::pageSlug() ), admin_url( 'admin.php' ) ) . '#settings',
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_WHATSAPP:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => CloudWhatsAppPage::pageSlug() ), admin_url( 'admin.php' ) ),
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_MOBILE_STAFF_CABINET:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => CloudMobileStaffCabinetPage::pageSlug() ), admin_url( 'admin.php' ) )
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_SERVICE_EXTRAS:
                    $data['button'] = array(
                        'caption' => $texts['button'],
                        'url' => add_query_arg( array( 'page' => ServicesPage::pageSlug() ), admin_url( 'admin.php' ) )
                    );
                    wp_send_json_success( $data );
                    break;
                case Account::PRODUCT_CRON:
                default:
                    wp_send_json_success( $data );
                    break;
            }
        } else {
            wp_send_json_error( array( 'content' => current( $api->getErrors() ) ) );
        }
    }
}