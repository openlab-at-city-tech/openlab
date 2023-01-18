<?php
namespace Bookly\Backend\Modules\CloudProducts;

use Bookly\Backend\Modules\Settings\Page as SettingsPage;
use Bookly\Backend\Modules\CloudSMS\Page as CloudSMSPage;
use Bookly\Backend\Modules\CloudZapier\Page as CloudZapierPage;
use Bookly\Backend\Modules\CloudVoice\Page as CloudVoicePage;
use Bookly\Lib;
use Bookly\Lib\Cloud\Account;

/**
 * Class Ajax
 * @package Bookly\Backend\Modules\CloudProducts
 */
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
        if ( $cloud->sms->changeSmsStatus( $status ) ) {
            wp_send_json_success( array(
                'redirect_url' => add_query_arg(
                    array( 'page' => Page::pageSlug() ),
                    admin_url( 'admin.php' ) ) . '#cloud-product=sms&status=activated'
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
        $cloud = Lib\Cloud\API::getInstance();
        $product = null;
        switch ( self::parameter( 'product' ) ) {
            case Lib\Cloud\Account::PRODUCT_ZAPIER:
                $product = $cloud->zapier;
                break;
            case Lib\Cloud\Account::PRODUCT_CRON:
                $product = $cloud->cron;
                break;
            case Lib\Cloud\Account::PRODUCT_VOICE:
                $product = $cloud->voice;
                break;
            case Lib\Cloud\Account::PRODUCT_SQUARE:
                $product = $cloud->square;
                break;
            case Lib\Cloud\Account::PRODUCT_GIFT:
                $product = $cloud->gift;
                break;
        }

        if ( isset( $product ) && ! $product->revertCancel() ) {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        }
        wp_send_json_success();
    }

    /**
     * Enable/disable Stripe Cloud
     */
    public static function cloudStripeChangeStatus()
    {
        $status  = self::parameter( 'status' );
        $api     = Lib\Cloud\API::getInstance();
        if ( $status ) {
            $redirect_url = $api->stripe->connect();
            if ( $redirect_url !== false ) {
                wp_send_json_success( compact( 'redirect_url' ) );
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        } else {
            if ( $api->stripe->disconnect() ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        }
    }

    /**
     * Change product status
     *
     * @return void
     */
    public static function cloudChangeProductStatus()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $product_slug = self::parameter( 'product' );
        $product = null;
        $status = self::parameter( 'status' );
        switch ( $product_slug ) {
            case Lib\Cloud\Account::PRODUCT_ZAPIER:
                $product = $cloud->zapier;
                break;
            case Lib\Cloud\Account::PRODUCT_CRON:
                $product = $cloud->cron;
                break;
            case Lib\Cloud\Account::PRODUCT_VOICE:
                $status = $status ?: 'now';
                $product = $cloud->voice;
                break;
            case Lib\Cloud\Account::PRODUCT_SQUARE:
                $product = $cloud->square;
                break;
            case Lib\Cloud\Account::PRODUCT_GIFT:
                $product = $cloud->gift;
                break;
        }
        if ( $status === '1' ) {
            $response = $product->activate( self::parameter( 'product_price' ) );
            if ( $response !== false ) {
                wp_send_json_success( array(
                    'redirect_url' => add_query_arg( array( 'page' => Page::pageSlug() ), admin_url( 'admin.php' ) ) . '#cloud-product=' . $product_slug . '&status=activated',
                ) );
            } else {
                wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
            }
        } elseif ( $product->deactivate( $status ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
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
                case Account::PRODUCT_STRIPE:
                case Account::PRODUCT_SQUARE:
                case Account::PRODUCT_GIFT:
                    if ( $status == 'activated' ) {
                        $data['button'] = array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'payments' ), admin_url( 'admin.php' ) ),
                        );
                        wp_send_json_success( $data );
                    }
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