<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib;

abstract class Tables
{
    const APPOINTMENTS = 'appointments';
    const CLOUD_MOBILE_STAFF_CABINET = 'cloud_mobile_staff_cabinet';
    const CLOUD_PURCHASES = 'cloud_purchases';
    const COUPONS = 'coupons';
    const CUSTOMERS = 'customers';
    const CUSTOMER_GROUPS = 'customer_groups';
    const CUSTOM_STATUSES = 'custom_statuses';
    const DISCOUNTS = 'discounts';
    const EMAIL_LOGS = 'email_logs';
    const EMAIL_NOTIFICATIONS = 'email_notifications';
    const GIFT_CARDS = 'gift_cards';
    const GIFT_CARD_TYPES = 'gift_card_types';
    const LOCATIONS = 'locations';
    const PACKAGES = 'packages';
    const PAYMENTS = 'payments';
    const SERVICES = 'services';
    const SMS_DETAILS = 'sms_details';
    const SMS_MAILING_CAMPAIGNS = 'sms_mailing_campaigns';
    const SMS_MAILING_LISTS = 'sms_mailing_lists';
    const SMS_MAILING_RECIPIENTS_LIST = 'sms_mailing_recipients_list';
    const SMS_NOTIFICATIONS = 'sms_notifications';
    const SMS_PRICES = 'sms_prices';
    const SMS_SENDER = 'sms_sender';
    const STAFF_MEMBERS = 'staff_members';
    const TAXES = 'taxes';
    const VOICE_DETAILS = 'voice_details';
    const VOICE_NOTIFICATIONS = 'voice_notifications';
    const VOICE_PRICES = 'voice_prices';
    const WHATSAPP_DETAILS = 'whatsapp_details';
    const WHATSAPP_NOTIFICATIONS = 'whatsapp_notifications';
    const LOGS = 'logs';
    const CUSTOMER_CABINET_APPOINTMENTS = 'customer_cabinet_appointments';

    /**
     * Get columns for given table.
     *
     * @param string $table
     * @return array
     */
    public static function getColumns( $table )
    {
        $columns = array();
        switch ( $table ) {
            case self::APPOINTMENTS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'no' => esc_html__( 'No.', 'bookly' ),
                    'start_date' => esc_html__( 'Appointment date', 'bookly' ),
                    'staff_name' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_employee' ) ),
                    'customer_full_name' => esc_html__( 'Customer name', 'bookly' ),
                    'customer_phone' => esc_html__( 'Customer phone', 'bookly' ),
                    'customer_email' => esc_html__( 'Customer email', 'bookly' ),
                    'service_title' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_service' ) ),
                    'service_duration' => esc_html__( 'Duration', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                    'payment' => esc_html__( 'Payment', 'bookly' ),
                    'notes' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_notes' ) ),
                    'created_date' => esc_html__( 'Created', 'bookly' ),
                    'internal_note' => esc_html__( 'Internal note', 'bookly' ),
                );
                break;
            case self::CLOUD_PURCHASES:
                $columns = array(
                    'date' => esc_html__( 'Date', 'bookly' ),
                    'time' => esc_html__( 'Time', 'bookly' ),
                    'type' => esc_html__( 'Type', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                    'amount' => esc_html__( 'Amount', 'bookly' ),
                );
                break;
            case self::CUSTOMERS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'full_name' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_name' ) ),
                    'first_name' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_first_name' ) ),
                    'last_name' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_last_name' ) ),
                    'wp_user' => esc_html__( 'User', 'bookly' ),
                    'phone' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_phone' ) ),
                    'email' => esc_html( Common::getTranslatedOption( 'bookly_l10n_label_email' ) ),
                    'notes' => esc_html__( 'Notes', 'bookly' ),
                    'last_appointment' => esc_html__( 'Last appointment', 'bookly' ),
                    'total_appointments' => esc_html__( 'Total appointments', 'bookly' ),
                    'payments' => esc_html__( 'Payments', 'bookly' ),
                    'birthday' => esc_html__( 'Birthday', 'bookly' ),
                );
                break;
            case self::EMAIL_NOTIFICATIONS:
            case self::SMS_NOTIFICATIONS:
            case self::VOICE_NOTIFICATIONS:
            case self::WHATSAPP_NOTIFICATIONS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'type' => esc_html__( 'Type', 'bookly' ),
                    'name' => esc_html__( 'Name', 'bookly' ),
                    'active' => esc_html__( 'State', 'bookly' ),
                );
                break;
            case self::EMAIL_LOGS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'to' => esc_html__( 'To', 'bookly' ),
                    'subject' => esc_html__( 'Subject', 'bookly' ),
                    'created_at' => esc_html__( 'Created', 'bookly' ),
                );
                break;
            case self::PAYMENTS:
                $columns = array(
                    'id' => esc_html__( 'No.', 'bookly' ),
                    'created_at' => esc_html__( 'Date', 'bookly' ),
                    'type' => esc_html__( 'Type', 'bookly' ),
                    'customer' => esc_html__( 'Customer', 'bookly' ),
                    'provider' => esc_html__( 'Provider', 'bookly' ),
                    'service' => esc_html__( 'Service', 'bookly' ),
                    'start_date' => esc_html__( 'Appointment date', 'bookly' ),
                    'paid' => esc_html__( 'Amount', 'bookly' ),
                    'subtotal' => esc_html__( 'Subtotal', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                );
                break;
            case self::SERVICES:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'title' => esc_html__( 'Title', 'bookly' ),
                    'category_name' => esc_html__( 'Category', 'bookly' ),
                    'duration' => esc_html__( 'Duration', 'bookly' ),
                    'price' => esc_html__( 'Price', 'bookly' ),
                );
                break;
            case self::SMS_MAILING_CAMPAIGNS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'name' => esc_html__( 'Name', 'bookly' ),
                    'send_at' => esc_html__( 'Start at', 'bookly' ),
                    'state' => esc_html__( 'State', 'bookly' ),
                );
                break;
            case self::SMS_MAILING_LISTS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'name' => esc_html__( 'Name', 'bookly' ),
                    'number_of_recipients' => esc_html__( 'Number of recipients', 'bookly' ),
                );
                break;
            case self::SMS_MAILING_RECIPIENTS_LIST:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'name' => esc_html__( 'Name', 'bookly' ),
                    'phone' => esc_html__( 'Phone', 'bookly' ),
                );
                break;
            case self::SMS_DETAILS:
                $columns = array(
                    'date' => esc_html__( 'Date', 'bookly' ),
                    'time' => esc_html__( 'Time', 'bookly' ),
                    'message' => esc_html__( 'Text', 'bookly' ),
                    'phone' => esc_html__( 'Phone', 'bookly' ),
                    'sender_id' => esc_html__( 'Sender ID', 'bookly' ),
                    'charge' => esc_html__( 'Cost', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                    'info' => esc_html__( 'Info', 'bookly' ),
                    'resend' => esc_html__( 'Resend', 'bookly' ),
                );
                break;
            case self::VOICE_DETAILS:
                $columns = array(
                    'date' => esc_html__( 'Date', 'bookly' ),
                    'time' => esc_html__( 'Time', 'bookly' ),
                    'message' => esc_html__( 'Text', 'bookly' ),
                    'phone' => esc_html__( 'Phone', 'bookly' ),
                    'duration' => esc_html__( 'Duration', 'bookly' ),
                    'charge' => esc_html__( 'Cost', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                );
                break;
            case self::WHATSAPP_DETAILS:
                $columns = array(
                    'date' => esc_html__( 'Date', 'bookly' ),
                    'time' => esc_html__( 'Time', 'bookly' ),
                    'template' => esc_html__( 'Template', 'bookly' ),
                    'language' => esc_html__( 'Language', 'bookly' ),
                    'phone' => esc_html__( 'Phone', 'bookly' ),
                    'charge' => esc_html__( 'Cost', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                    'info' => esc_html__( 'Info', 'bookly' ),
                );
                break;
            case self::VOICE_PRICES:
                $columns = array(
                    'country_iso_code' => esc_html__( 'Flag', 'bookly' ),
                    'country_name' => esc_html__( 'Country', 'bookly' ),
                    'phone_code' => esc_html__( 'Code', 'bookly' ),
                    'call_price' => esc_html__( 'Price/Minute', 'bookly' ),
                );
                break;
            case self::SMS_PRICES:
                $columns = array(
                    'country_iso_code' => esc_html__( 'Flag', 'bookly' ),
                    'country_name' => esc_html__( 'Country', 'bookly' ),
                    'phone_code' => esc_html__( 'Code', 'bookly' ),
                    'price' => esc_html__( 'Regular price', 'bookly' ),
                    'price_alt' => esc_html__( 'Price with custom Sender ID', 'bookly' ),
                );
                break;
            case self::SMS_SENDER:
                $columns = array(
                    'date' => esc_html__( 'Date', 'bookly' ),
                    'name' => esc_html__( 'Requested ID', 'bookly' ),
                    'status' => esc_html__( 'Status', 'bookly' ),
                    'status_date' => esc_html__( 'Status date', 'bookly' ),
                );
                break;
            case self::STAFF_MEMBERS:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'full_name' => esc_html__( 'Name', 'bookly' ),
                    'email' => esc_html__( 'Email', 'bookly' ),
                    'phone' => esc_html__( 'Phone', 'bookly' ),
                    'wp_user' => esc_html__( 'User', 'bookly' ),
                );
                break;
            case self::CLOUD_MOBILE_STAFF_CABINET:
                $columns = array(
                    'id' => esc_html__( 'ID', 'bookly' ),
                    'full_name' => esc_html__( 'Name', 'bookly' ),
                    'email' => esc_html__( 'Email', 'bookly' ),
                    'cloud_msc_token' => esc_html__( 'Access token', 'bookly' ),
                );
                break;
            case self::CUSTOMER_CABINET_APPOINTMENTS:
                $columns = array(
                    'category' => Common::getTranslatedOption( 'bookly_l10n_label_category' ),
                    'service' => Common::getTranslatedOption( 'bookly_l10n_label_service' ),
                    'staff' => Common::getTranslatedOption( 'bookly_l10n_label_employee' ),
                    'location' => Common::getTranslatedOption( 'bookly_l10n_label_location' ),
                    'duration' => __( 'Duration', 'bookly' ),
                    'date' => __( 'Date', 'bookly' ),
                    'time' => __( 'Time', 'bookly' ),
                    'price' => __( 'Price', 'bookly' ),
                    'online_meeting' => __( 'Online meeting', 'bookly' ),
                    'join_online_meeting' => __( 'Join online meeting', 'bookly' ),
                    'cancel' => __( 'Cancel', 'bookly' ),
                    'reschedule' => __( 'Reschedule', 'bookly' ),
                    'status' => __( 'Status', 'bookly' ),
                );
                break;
            case self::LOGS:
                $columns = array(
                    'id' => __( 'ID', 'bookly' ),
                    'created_at' => __( 'Date', 'bookly' ),
                    'action' => __( 'Action', 'bookly' ),
                    'target' => __( 'Target', 'bookly' ),
                    'target_id' => __( 'Target ID', 'bookly' ),
                    'author' => __( 'Author', 'bookly' ),
                    'details' => __( 'Details', 'bookly' ),
                    'comment' => __( 'Comment', 'bookly' ),
                    'ref' => __( 'Reference', 'bookly' ),
                );
                break;
        }

        return Lib\Proxy\Shared::prepareTableColumns( $columns, $table );
    }

    /**
     * Get table settings.
     *
     * @param string|array $tables
     * @return array
     */
    public static function getSettings( $tables )
    {
        if ( ! is_array( $tables ) ) {
            $tables = array( $tables );
        }
        $result = array();
        foreach ( $tables as $table ) {
            $columns = self::getColumns( $table );
            $meta = get_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', true );
            $defaults = self::getDefaultSettings( $table );

            $exist = true;
            if ( ! $meta ) {
                $exist = false;
                $meta = array();
            }

            if ( ! isset ( $meta['columns'] ) ) {
                $meta['columns'] = array();
            }

            // Remove columns with no title.
            foreach ( $meta['columns'] as $key => $column ) {
                if ( ! isset( $columns[ $key ] ) ) {
                    unset( $meta['columns'][ $key ] );
                }
            }
            // New columns, which not saved at meta
            // show/hide if default settings exist and show without default settings
            foreach ( $columns as $column => $title ) {
                if ( ! isset ( $meta['columns'][ $column ] ) ) {
                    $meta['columns'][ $column ] = array_key_exists( $column, $defaults )
                        ? $defaults[ $column ]
                        : true;
                }
            }

            $result[ $table ] = array(
                'settings' => array(
                    'columns' => $meta['columns'],
                    'filter' => isset ( $meta['filter'] ) ? $meta['filter'] : array(),
                    'order' => isset ( $meta['order'] ) ? $meta['order'] : array(),
                    'page_length' => isset ( $meta['page_length'] ) ? $meta['page_length'] : 25,
                ),
                'titles' => $columns,
                'exist' => $exist,
            );
        }

        return $result;
    }

    /**
     * Update table settings.
     *
     * @param string $table
     * @param array $columns
     * @param array $order
     * @param array $filter
     */
    public static function updateSettings( $table, $columns, $order, $filter )
    {
        $meta = get_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', true ) ?: array();
        if ( $columns !== null && $order !== null ) {
            $order_columns = array();
            foreach ( $order as $sort_by ) {
                if ( isset( $columns[ $sort_by['column'] ] ) ) {
                    $order_columns[] = array(
                        'column' => $columns[ $sort_by['column'] ]['data'],
                        'order' => $sort_by['dir'],
                    );
                }
            }
            $meta['order'] = $order_columns;
        }

        $meta['filter'] = $filter;

        update_user_meta( get_current_user_id(), 'bookly_' . $table . '_table_settings', $meta );
    }

    /**
     * @param array $columns
     * @param string $table
     * @return array
     */
    public static function filterColumns( $columns, $table )
    {
        $def_columns = array_keys( self::getColumns( $table ) );

        $replaces = array();
        switch ( $table ) {
            case self::CUSTOMER_CABINET_APPOINTMENTS:
                $replaces = array(
                    'date' => 'start_date',
                    'service' => 'service.title',
                    'staff' => 'staff_name',
                    'online_meeting' => 'online_meeting_provider',
                    'join_online_meeting' => 'online_meeting_provider',
                );
                foreach ( $def_columns as &$column ) {
                    if ( strpos( $column, 'custom_field' ) === 0 ) {
                        $column = 'custom_fields.' . substr( $column, 13 );
                    }
                }
                break;
            case self::APPOINTMENTS:
                $replaces = array(
                    'customer_full_name' => 'customer.full_name',
                    'customer_phone' => 'customer.phone',
                    'customer_email' => 'customer.email',
                    'customer_address' => 'customer.address',
                    'customer_birthday' => 'customer.birthday',
                    'staff_name' => 'staff.name',
                    'service_title' => 'service.title',
                    'service_duration' => 'service.duration',
                    'attachments' => 'attachment',
                    'online_meeting' => 'online_meeting_provider',
                );
                break;
            case self::PACKAGES:
                foreach ( $def_columns as &$column ) {
                    $column = str_replace( '_', '.', $column );
                }
                $def_columns[] = 'customer.full_name';
                $def_columns[] = 'created_at';
                break;
        }
        foreach ( $def_columns as &$column ) {
            foreach ( $replaces as $key => $replacement ) {
                if ( $column === $key ) {
                    $column = $replacement;
                }
            }
            if ( strpos( $column, 'custom_field' ) === 0 ) {
                $column = 'custom_fields.' . substr( $column, 13 );
            }
        }

        foreach ( $columns as &$column ) {
            if ( ! in_array( $column['data'], $def_columns, true ) ) {
                $column['data'] = 'true';
            }
        }

        return $columns;
    }

    /**
     * Get default settings for hide/show table columns
     *
     * @param string $table
     * @return array
     */
    private static function getDefaultSettings( $table )
    {
        $columns = array();
        switch ( $table ) {
            case self::CUSTOMERS:
                $columns = array(
                    'id' => false,
                    'full_name' => ! Lib\Config::showFirstLastName(),
                    'first_name' => Lib\Config::showFirstLastName(),
                    'last_name' => Lib\Config::showFirstLastName(),
                    'birthday' => false,
                );
                break;
            case self::APPOINTMENTS:
                $columns = array(
                    'no' => false,
                    'internal_note' => false,
                );
                break;
            case self::EMAIL_LOGS:
            case self::EMAIL_NOTIFICATIONS:
            case self::SERVICES:
            case self::SMS_DETAILS:
            case self::SMS_MAILING_CAMPAIGNS:
            case self::SMS_MAILING_LISTS:
            case self::SMS_MAILING_RECIPIENTS_LIST:
            case self::SMS_NOTIFICATIONS:
            case self::STAFF_MEMBERS:
            case self::CLOUD_MOBILE_STAFF_CABINET:
                $columns = array( 'id' => false, );
                break;
        }

        return Lib\Proxy\Shared::prepareTableDefaultSettings( $columns, $table );
    }

    /**
     * @param string $table
     * @return bool
     */
    public static function supportPagination( $table )
    {
        switch ( $table ) {
            case self::APPOINTMENTS:
            case self::CUSTOMERS:
            case self::EMAIL_LOGS:
            case self::PAYMENTS:
            case self::SERVICES:
            case self::SMS_DETAILS:
            case self::STAFF_MEMBERS:

                return true;
        }

        return false;
    }
}