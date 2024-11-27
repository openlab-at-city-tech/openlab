<?php
namespace Bookly\Backend\Components\Dialogs\TableSettings;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /** @var array */
    protected static $tables = array(
        Lib\Utils\Tables::APPOINTMENTS,
        Lib\Utils\Tables::CLOUD_MOBILE_STAFF_CABINET,
        Lib\Utils\Tables::CLOUD_PURCHASES,
        Lib\Utils\Tables::COUPONS,
        Lib\Utils\Tables::CUSTOMERS,
        Lib\Utils\Tables::CUSTOMER_GROUPS,
        Lib\Utils\Tables::CUSTOM_STATUSES,
        Lib\Utils\Tables::DISCOUNTS,
        Lib\Utils\Tables::EMAIL_LOGS,
        Lib\Utils\Tables::EMAIL_NOTIFICATIONS,
        Lib\Utils\Tables::GIFT_CARDS,
        Lib\Utils\Tables::GIFT_CARD_TYPES,
        Lib\Utils\Tables::LOCATIONS,
        Lib\Utils\Tables::PACKAGES,
        Lib\Utils\Tables::PAYMENTS,
        Lib\Utils\Tables::SERVICES,
        Lib\Utils\Tables::SMS_DETAILS,
        Lib\Utils\Tables::SMS_MAILING_CAMPAIGNS,
        Lib\Utils\Tables::SMS_MAILING_LISTS,
        Lib\Utils\Tables::SMS_MAILING_RECIPIENTS_LIST,
        Lib\Utils\Tables::SMS_NOTIFICATIONS,
        Lib\Utils\Tables::SMS_PRICES,
        Lib\Utils\Tables::SMS_SENDER,
        Lib\Utils\Tables::STAFF_MEMBERS,
        Lib\Utils\Tables::TAXES,
        Lib\Utils\Tables::VOICE_DETAILS,
        Lib\Utils\Tables::VOICE_NOTIFICATIONS,
        Lib\Utils\Tables::VOICE_PRICES,
        Lib\Utils\Tables::WHATSAPP_DETAILS,
        Lib\Utils\Tables::WHATSAPP_NOTIFICATIONS,
    );

    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * Update table settings.
     */
    public static function updateTableSettings()
    {
        $table = self::parameter( 'table' );

        $meta = get_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', true ) ?: array();
        if ( self::hasParameter( 'page_length' ) ) {
            $meta['page_length'] = self::parameter( 'page_length' );
        }
        if ( in_array( $table, self::$tables ) ) {
            $meta['columns'] = self::parameter( 'columns', array() );
            array_walk( $meta['columns'], function( &$show ) { $show = (bool) $show; } );
            update_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', $meta );
        }

        wp_send_json_success();
    }

    /**
     * Update table sorting.
     */
    public static function updateTableOrder()
    {
        $table = self::parameter( 'table' );

        $meta = get_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', true ) ?: array();
        if ( in_array( $table, self::$tables ) ) {
            $meta['order'] = self::parameter( 'order', array() );
            update_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', $meta );
        }

        wp_send_json_success();
    }
}