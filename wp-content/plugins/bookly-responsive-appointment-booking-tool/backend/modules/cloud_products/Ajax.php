<?php
namespace Bookly\Backend\Modules\CloudProducts;

use Bookly\Backend\Modules\Settings\Page as SettingsPage;
use Bookly\Backend\Modules\CloudSMS\Page as CloudSMSPage;
use Bookly\Backend\Modules\CloudZapier\Page as CloudZapierPage;
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
     * Enable/disable Zapier
     */
    public static function cloudZapierChangeStatus()
    {
        $status = self::parameter( 'status' );
        $api    = Lib\Cloud\API::getInstance();
        if ( $status == '1' ) {
            $response = $api->zapier->activate( self::parameter( 'product_price' ) );
            if ( $response !== false ) {
                wp_send_json_success( array(
                    'redirect_url' => add_query_arg(
                        array( 'page' => Page::pageSlug() ),
                        admin_url( 'admin.php' ) ) . '#cloud-product=zapier&status=activated'
                ) );
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        } else {
            if ( $api->zapier->deactivate( $status ) ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        }
    }

    /**
     * Revert cancel Zapier subscription
     */
    public static function cloudZapierRevertCancel()
    {
        $api = Lib\Cloud\API::getInstance();
        if ( $api->zapier->revertCancel() ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }
    }

    /**
     * Enable/disable Cron
     */
    public static function cloudCronChangeStatus()
    {
        $status = self::parameter( 'status' );
        $api = Lib\Cloud\API::getInstance();
        if ( $status === '1' ) {
            $response = $api->cron->activate( self::parameter( 'product_price' ) );
            if ( $response !== false ) {
                wp_send_json_success( array(
                    'redirect_url' => add_query_arg(
                            array( 'page' => Page::pageSlug() ),
                            admin_url( 'admin.php' )
                        ) . '#cloud-product=cron&status=activated',
                ) );
            } else {
                wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
            }
        } elseif ( $api->cron->deactivate( $status ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }
    }

    /**
     * Revert cancel Cron subscription
     */
    public static function cloudCronRevertCancel()
    {
        $api = Lib\Cloud\API::getInstance();
        if ( $api->cron->revertCancel() ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
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
        $api     = Lib\Cloud\API::getInstance();
        $texts   = $api->account->getProductActivationTexts( self::parameter( 'product' ) );
        if ( $texts ) {
            switch ( $product ) {
                case Account::PRODUCT_SMS_NOTIFICATIONS:
                    wp_send_json_success( array(
                        'content' => $texts['message'],
                        'button'  => array(
                            'caption' => $texts['button'],
                            'url'     => add_query_arg( array( 'page' => CloudSMSPage::pageSlug() ), admin_url( 'admin.php' ) )
                        )
                    ) );
                    break;
                case Account::PRODUCT_STRIPE:
                    if ( $status == 'activated' ) {
                        wp_send_json_success( array(
                            'content' => $texts['message'],
                            'button'  => array(
                                'caption' => $texts['button'],
                                'url' => add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'payments' ), admin_url( 'admin.php' ) ),
                            )
                        ) );
                    }
                    break;
                case Account::PRODUCT_ZAPIER:
                    wp_send_json_success( array(
                        'content' => $texts['message'],
                        'button' => array(
                            'caption' => $texts['button'],
                            'url' => add_query_arg( array( 'page' => CloudZapierPage::pageSlug() ), admin_url( 'admin.php' ) ),
                        ),
                    ) );
                    break;
                case Account::PRODUCT_CRON:
                    wp_send_json_success( array(
                        'content' => $texts['message'],
                    ) );
                    break;
            }
        } else {
            wp_send_json_error( array( 'content' => current( $api->getErrors() ) ) );
        }
    }
}