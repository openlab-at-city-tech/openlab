<?php
namespace Bookly\Backend\Modules\CloudSettings;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Send client info for invoice.
     */
    public static function saveInvoiceData()
    {
        $cloud  = Lib\Cloud\API::getInstance();
        $result = $cloud->account->setInvoiceData( (array) self::parameter( 'invoice' ) );
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        } else {
            wp_send_json_success();
        }
    }

    /**
     * Enable or Disable administrators email reports.
     */
    public static function adminNotify()
    {
        switch ( self::parameter( 'option_name' ) ) {
            case 'notify_summary':
                self::parameter( 'value' )
                    ? Lib\Cloud\API::getInstance()->account->enableSendingWeeklySummary()
                    : Lib\Cloud\API::getInstance()->account->disableSendingWeeklySummary();
                break;
            case 'bookly_cloud_notify_low_balance':
            case 'bookly_cloud_badge_consider_sms':
                update_option( self::parameter( 'option_name' ), self::parameter( 'value' ) );
                break;
        }
        wp_send_json_success();
    }

    /**
     * Change country.
     */
    public static function changeCountry()
    {
        $country = self::parameter( 'country' );

        $result = Lib\Cloud\API::getInstance()->account->changeCountry( $country );
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        } else {
            wp_send_json( $result );
        }
    }

    /**
     * Change password.
     */
    public static function changePassword()
    {
        $old_password = self::parameter( 'old_password' );
        $new_password = self::parameter( 'new_password' );

        $result = Lib\Cloud\API::getInstance()->account->changePassword( $new_password, $old_password );
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        } else {
            wp_send_json_success();
        }
    }
}