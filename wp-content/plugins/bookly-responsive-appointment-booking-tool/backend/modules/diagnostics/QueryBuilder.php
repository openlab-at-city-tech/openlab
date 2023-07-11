<?php

namespace Bookly\Backend\Modules\Diagnostics;

/**
 * Class QueryBuilder
 *
 * @package Bookly\Backend\Modules\Diagnostics
 */
class QueryBuilder
{
    /**
     * Get add column query
     *
     * @param string $table
     * @param string $column
     * @return string
     */
    public static function getAddColumn( $table, $column )
    {
        $data = self::getColumnData( $table, $column );
        if ( $data ) {
            return sprintf(
                'ALTER TABLE `%s` ADD COLUMN `%s` %s',
                $table,
                $column,
                self::getColumnDataType( $data )
            );
        }

        return '';
    }

    /**
     * Get drop foreign key query
     *
     * @param string $table
     * @param string $constraint_name
     * @return string
     */
    public static function getDropForeignKey( $table, $constraint_name )
    {
        return sprintf(
            'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
            $table,
            $constraint_name
        );
    }

    /**
     * Get change column query
     *
     * @param string $table
     * @param string $column
     * @return string
     */
    public static function getChangeColumn( $table, $column )
    {
        $data = self::getColumnData( $table, $column );
        if ( $data ) {
            return sprintf(
                'ALTER TABLE `%1$s` CHANGE COLUMN `%2$s` `%2$s` %3$s',
                $table,
                $column,
                self::getColumnDataType( $data )
            );
        }

        return '';
    }

    /**
     * Get add constraint query
     *
     * @param string $table
     * @param string $column
     * @param string $ref_table
     * @param string $ref_column
     * @return bool|string
     */
    public static function getAddConstraint( $table, $column, $ref_table, $ref_column )
    {
        $rules = self::getConstraintRules( $table, $column, $ref_table, $ref_column );
        $sql = sprintf( 'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`)', $table, $column, $ref_table, $ref_column );
        $delete_rule = $rules['DELETE_RULE'];
        switch ( $delete_rule ) {
            case 'RESTRICT':
            case 'CASCADE':
            case 'SET NULL':
            case 'NO ACTIONS':
                $sql .= ' ON DELETE ' . $delete_rule;
                break;
            default:
                return false;
        }
        $update_rule = $rules['UPDATE_RULE'];
        switch ( $update_rule ) {
            case 'RESTRICT':
            case 'CASCADE':
            case 'SET NULL':
            case 'NO ACTIONS':
                $sql .= ' ON UPDATE ' . $update_rule;
                break;
            default:
                return false;
        }

        return $sql;
    }

    /**
     * Get create table query
     *
     * @param string $table
     * @return string
     */
    public static function getCreateTable( $table )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        return sprintf(
            'CREATE TABLE `%s` ( `id` %s, PRIMARY KEY (`id`)) ENGINE = INNODB %s',
            $table,
            str_replace( ' primary key', '', self::getColumnDataType( self::getColumnData( $table, 'id' ) ) ),
            $wpdb->has_cap( 'collation' )
                ? $wpdb->get_charset_collate()
                : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci'
        );
    }

    /**
     * Get column data type array
     *
     * @param string $table
     * @param string $column
     * @return array
     */
    public static function getColumnData( $table, $column )
    {
        /*
            SELECT CONCAT ( '\'', CONCAT_WS( '.', SUBSTR(TABLE_NAME,4), COLUMN_NAME ), '\' => array( \'type\' => "', COLUMN_TYPE, '", \'is_nullabe\' => ', IF(IS_NULLABLE = 'NO', '0', '1' ), ', \'extra\' => "', EXTRA, '", \'default\' => ', CONCAT (
                IF (COLUMN_DEFAULT is NULL, IF( IS_NULLABLE = 'NO', COALESCE(COLUMN_DEFAULT,'null'), 'null' ), CONCAT('"',COALESCE(COLUMN_DEFAULT,''), '"'))), ', \'key\' => "' , COLUMN_KEY ,'" ),'
            ) as l
                  FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = SCHEMA()
                   AND TABLE_NAME LIKE 'wp_bookly_%'
              ORDER BY TABLE_NAME, ORDINAL_POSITION
        */

        $data = array(
            'bookly_appointments.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_appointments.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_appointments.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_appointments.staff_any' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_appointments.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_appointments.custom_service_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.custom_service_price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.start_date' => array( 'type' => "datetime", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.end_date' => array( 'type' => "datetime", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.extras_duration' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_appointments.internal_note' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.google_event_id' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.google_event_etag' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.outlook_event_id' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.outlook_event_change_key' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.outlook_event_series_id' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.online_meeting_provider' => array( 'type' => "enum('zoom','google_meet','jitsi','bbb')", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.online_meeting_id' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.online_meeting_data' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.created_from' => array( 'type' => "enum('bookly','google','outlook')", 'is_nullabe' => 0, 'extra' => "", 'default' => "bookly", 'key' => "" ),
            'bookly_appointments.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_appointments.updated_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_categories.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_categories.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_categories.attachment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_categories.info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_categories.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_coupon_customers.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_coupon_customers.coupon_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupon_customers.customer_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupon_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_coupon_services.coupon_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupon_services.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupon_staff.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_coupon_staff.coupon_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupon_staff.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_coupons.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_coupons.code' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_coupons.discount' => array( 'type' => "decimal(5,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_coupons.deduction' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_coupons.usage_limit' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_coupons.used' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_coupons.once_per_customer' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_coupons.date_limit_start' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_coupons.date_limit_end' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_coupons.min_appointments' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_coupons.max_appointments' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_custom_statuses.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_custom_statuses.slug' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "UNI" ),
            'bookly_custom_statuses.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_custom_statuses.busy' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_custom_statuses.color' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "#dddddd", 'key' => "" ),
            'bookly_custom_statuses.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_customer_appointment_files.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_customer_appointment_files.customer_appointment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointment_files.file_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_customer_appointments.series_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.package_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.customer_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.appointment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.payment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.order_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_appointments.number_of_persons' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_customer_appointments.units' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_customer_appointments.notes' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.extras' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.extras_multiply_nop' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_customer_appointments.custom_fields' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.status' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "approved", 'key' => "" ),
            'bookly_customer_appointments.status_changed_at' => array( 'type' => "datetime", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.time_zone' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.time_zone_offset' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.rating' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.rating_comment' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.locale' => array( 'type' => "varchar(8)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.collaborative_service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.collaborative_token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.compound_service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.compound_token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.created_from' => array( 'type' => "enum('frontend','backend')", 'is_nullabe' => 0, 'extra' => "", 'default' => "frontend", 'key' => "" ),
            'bookly_customer_appointments.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_appointments.updated_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_groups.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_customer_groups.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_groups.description' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_groups.appointment_status' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customer_groups.discount' => array( 'type' => "varchar(100)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_customer_groups.skip_payment' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_customer_groups.gateways' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customer_groups_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_customer_groups_services.group_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customer_groups_services.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customers.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_customers.wp_user_id' => array( 'type' => "bigint unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.facebook_id' => array( 'type' => "bigint unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.group_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_customers.full_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customers.first_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customers.last_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customers.phone' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customers.email' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_customers.birthday' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.country' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.state' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.postcode' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.city' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.street' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.street_number' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.additional_address' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.notes' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.info_fields' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.stripe_account' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_customers.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_discounts.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_discounts.title' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_discounts.type' => array( 'type' => "enum('nop','appointments')", 'is_nullabe' => 0, 'extra' => "", 'default' => "nop", 'key' => "" ),
            'bookly_discounts.threshold' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_discounts.discount' => array( 'type' => "decimal(5,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_discounts.deduction' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_discounts.date_start' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_discounts.date_end' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_discounts.enabled' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_email_log.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_email_log.to' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_email_log.subject' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_email_log.body' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_email_log.headers' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_email_log.attach' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_email_log.type' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_email_log.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_files.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_files.name' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_files.slug' => array( 'type' => "varchar(32)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_files.path' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_files.custom_field_id' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_forms.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_forms.type' => array( 'type' => "enum('search-form','services-form','staff-form','cancellation-confirmation')", 'is_nullabe' => 0, 'extra' => "", 'default' => "search-form", 'key' => "" ),
            'bookly_forms.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_forms.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_forms.settings' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_forms.custom_css' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_forms.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_gift_card_type_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_gift_card_type_services.gift_card_type_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_card_type_services.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_card_type_staff.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_gift_card_type_staff.gift_card_type_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_card_type_staff.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_card_types.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_gift_card_types.title' => array( 'type' => "varchar(64)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_gift_card_types.amount' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_gift_card_types.start_date' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_gift_card_types.end_date' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_gift_card_types.min_appointments' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_gift_card_types.max_appointments' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_gift_cards.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_gift_cards.code' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_gift_cards.gift_card_type_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_cards.balance' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_gift_cards.customer_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_gift_cards.payment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_holidays.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_holidays.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_holidays.parent_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_holidays.date' => array( 'type' => "date", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_holidays.repeat_event' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_locations.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_locations.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_locations.info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_locations.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_log.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_log.action' => array( 'type' => "enum('create','update','delete','error')", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.target' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.target_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.author' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.details' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.ref' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.comment' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_log.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_campaigns.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_mailing_campaigns.mailing_list_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_mailing_campaigns.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_campaigns.text' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_campaigns.state' => array( 'type' => "enum('pending','in-progress','completed','canceled')", 'is_nullabe' => 0, 'extra' => "", 'default' => "pending", 'key' => "" ),
            'bookly_mailing_campaigns.send_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_campaigns.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_list_recipients.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_mailing_list_recipients.mailing_list_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_mailing_list_recipients.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_list_recipients.phone' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_list_recipients.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_lists.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_mailing_lists.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_queue.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_mailing_queue.phone' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_queue.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_queue.text' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_mailing_queue.sent' => array( 'type' => "tinyint(1)", 'is_nullabe' => 1, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_mailing_queue.campaign_id' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_mailing_queue.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_news.news_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.title' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.media_type' => array( 'type' => "enum('image','youtube')", 'is_nullabe' => 0, 'extra' => "", 'default' => "image", 'key' => "" ),
            'bookly_news.media_url' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.text' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.button_url' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.button_text' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.seen' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_news.updated_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_news.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_notifications.gateway' => array( 'type' => "enum('email','sms','voice','whatsapp')", 'is_nullabe' => 0, 'extra' => "", 'default' => "email", 'key' => "" ),
            'bookly_notifications.type' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_notifications.active' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_notifications.subject' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_notifications.message' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications.to_staff' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.to_customer' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.to_admin' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.to_custom' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.custom_recipients' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications.attach_ics' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.attach_invoice' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications.settings' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications_queue.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_notifications_queue.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications_queue.data' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_notifications_queue.sent' => array( 'type' => "tinyint(1)", 'is_nullabe' => 1, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_notifications_queue.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_orders.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_orders.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_packages.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_packages.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_packages.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_packages.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_packages.customer_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_packages.internal_note' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_packages.payment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_packages.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_payments.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_payments.target' => array( 'type' => "enum('appointments','packages','gift_cards')", 'is_nullabe' => 0, 'extra' => "", 'default' => "appointments", 'key' => "" ),
            'bookly_payments.coupon_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_payments.gift_card_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_payments.type' => array( 'type' => "enum('local','free','paypal','authorize_net','stripe','2checkout','payu_biz','payu_latam','payson','mollie','woocommerce','cloud_stripe','cloud_square','cloud_gift')", 'is_nullabe' => 0, 'extra' => "", 'default' => "local", 'key' => "", ),
            'bookly_payments.total' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_payments.tax' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_payments.paid' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_payments.paid_type' => array( 'type' => "enum('in_full','deposit')", 'is_nullabe' => 0, 'extra' => "", 'default' => "in_full", 'key' => "" ),
            'bookly_payments.gateway_price_correction' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 1, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_payments.status' => array( 'type' => "enum('pending','completed','rejected','refunded')", 'is_nullabe' => 0, 'extra' => "", 'default' => "completed", 'key' => "" ),
            'bookly_payments.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_payments.details' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_payments.ref_id' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_payments.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_payments.updated_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_schedule_item_breaks.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_schedule_item_breaks.staff_schedule_item_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_schedule_item_breaks.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_schedule_item_breaks.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_sent_notifications.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_sent_notifications.ref_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_sent_notifications.notification_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_sent_notifications.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_series.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_series.repeat' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_series.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_discounts.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_discounts.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_discounts.discount_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_extras.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_extras.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_extras.attachment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_extras.title' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_service_extras.duration' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_service_extras.price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_service_extras.min_quantity' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_service_extras.max_quantity' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_service_extras.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_service_schedule_breaks.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_schedule_breaks.service_schedule_day_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_schedule_breaks.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_schedule_breaks.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_schedule_days.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_schedule_days.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_schedule_days.day_index' => array( 'type' => "smallint", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_schedule_days.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_schedule_days.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_special_days.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_special_days.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_special_days.date' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_special_days.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_special_days.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_special_days_breaks.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_special_days_breaks.service_special_day_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_special_days_breaks.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_special_days_breaks.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_service_taxes.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_service_taxes.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_service_taxes.tax_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_services.category_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_services.type' => array( 'type' => "enum('simple','collaborative','compound','package')", 'is_nullabe' => 0, 'extra' => "", 'default' => "simple", 'key' => "" ),
            'bookly_services.title' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_services.attachment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.duration' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "900", 'key' => "" ),
            'bookly_services.slot_length' => array( 'type' => "varchar(32)", 'is_nullabe' => 0, 'extra' => "", 'default' => "default", 'key' => "" ),
            'bookly_services.price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_services.color' => array( 'type' => "varchar(32)", 'is_nullabe' => 0, 'extra' => "", 'default' => "#FFFFFF", 'key' => "" ),
            'bookly_services.deposit' => array( 'type' => "varchar(16)", 'is_nullabe' => 0, 'extra' => "", 'default' => "100%", 'key' => "" ),
            'bookly_services.capacity_min' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_services.capacity_max' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_services.waiting_list_capacity' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.one_booking_per_slot' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.padding_left' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.padding_right' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.start_time_info' => array( 'type' => "varchar(32)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_services.end_time_info' => array( 'type' => "varchar(32)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_services.same_staff_for_subservices' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.units_min' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_services.units_max' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_services.package_life_time' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.package_size' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.package_unassigned' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.appointments_limit' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.limit_period' => array( 'type' => "enum('off','day','week','month','year','upcoming','calendar_day','calendar_week','calendar_month','calendar_year')", 'is_nullabe' => 0, 'extra' => "", 'default' => "off", 'key' => "", ),
            'bookly_services.staff_preference' => array( 'type' => "enum('order','least_occupied','most_occupied','least_occupied_for_period','most_occupied_for_period','least_expensive','most_expensive')", 'is_nullabe' => 0, 'extra' => "", 'default' => "most_expensive", 'key' => "", ),
            'bookly_services.staff_preference_settings' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.recurrence_enabled' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_services.recurrence_frequencies' => array( 'type' => "set('daily','weekly','biweekly','monthly')", 'is_nullabe' => 0, 'extra' => "", 'default' => "daily,weekly,biweekly,monthly", 'key' => "" ),
            'bookly_services.time_requirements' => array( 'type' => "enum('required','optional','off')", 'is_nullabe' => 0, 'extra' => "", 'default' => "required", 'key' => "" ),
            'bookly_services.collaborative_equal_duration' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.online_meetings' => array( 'type' => "enum('off','zoom','google_meet','jitsi','bbb')", 'is_nullabe' => 0, 'extra' => "", 'default' => "off", 'key' => "" ),
            'bookly_services.final_step_url' => array( 'type' => "varchar(512)", 'is_nullabe' => 0, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_services.wc_product_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_services.wc_cart_info_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.wc_cart_info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.min_time_prior_booking' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.min_time_prior_cancel' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.gateways' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_services.visibility' => array( 'type' => "enum('public','private','group')", 'is_nullabe' => 0, 'extra' => "", 'default' => "public", 'key' => "" ),
            'bookly_services.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_sessions.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_sessions.token' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_sessions.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_sessions.value' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_sessions.expire' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_shop.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_shop.plugin_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.type' => array( 'type' => "enum('plugin','bundle')", 'is_nullabe' => 0, 'extra' => "", 'default' => "plugin", 'key' => "" ),
            'bookly_shop.highlighted' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_shop.priority' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_shop.demo_url' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.title' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.slug' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.description' => array( 'type' => "text", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.url' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.icon' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.image' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.sales' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.rating' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.reviews' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.published' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_shop.seen' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_shop.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_special_days_breaks.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_special_days_breaks.staff_special_day_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_special_days_breaks.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_special_days_breaks.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff.category_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff.wp_user_id' => array( 'type' => "bigint unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.attachment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.full_name' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.email' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.phone' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.time_zone' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.working_time_limit' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.visibility' => array( 'type' => "enum('public','private','archive')", 'is_nullabe' => 0, 'extra' => "", 'default' => "public", 'key' => "" ),
            'bookly_staff.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_staff.google_data' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.outlook_data' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.zoom_authentication' => array( 'type' => "enum('default','jwt','oauth')", 'is_nullabe' => 0, 'extra' => "", 'default' => "default", 'key' => "" ),
            'bookly_staff.zoom_jwt_api_key' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.zoom_jwt_api_secret' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.zoom_oauth_token' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.icalendar' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_staff.icalendar_token' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff.icalendar_days_before' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "365", 'key' => "" ),
            'bookly_staff.icalendar_days_after' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "365", 'key' => "" ),
            'bookly_staff.color' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "#dddddd", 'key' => "" ),
            'bookly_staff.gateways' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_categories.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_categories.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_categories.attachment_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_categories.info' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_categories.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_staff_locations.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_locations.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_locations.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_locations.custom_services' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_staff_locations.custom_schedule' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_staff_locations.custom_special_days' => array( 'type' => "tinyint(1)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0", 'key' => "" ),
            'bookly_staff_preference_orders.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_preference_orders.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_preference_orders.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_preference_orders.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_staff_schedule_items.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_schedule_items.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_schedule_items.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_schedule_items.day_index' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_schedule_items.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_schedule_items.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_services.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_services.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_services.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_services.price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_staff_services.deposit' => array( 'type' => "varchar(100)", 'is_nullabe' => 0, 'extra' => "", 'default' => "100%", 'key' => "" ),
            'bookly_staff_services.capacity_min' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_staff_services.capacity_max' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "1", 'key' => "" ),
            'bookly_staff_special_days.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_special_days.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_special_days.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_days.date' => array( 'type' => "date", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_days.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_days.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_hours.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_staff_special_hours.staff_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_special_hours.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_staff_special_hours.location_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_hours.start_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_hours.end_time' => array( 'type' => "time", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_staff_special_hours.days' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => "1,2,3,4,5,6,7", 'key' => "" ),
            'bookly_staff_special_hours.price' => array( 'type' => "decimal(10,2)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.00", 'key' => "" ),
            'bookly_stats.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_stats.name' => array( 'type' => "varchar(255)", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_stats.value' => array( 'type' => "text", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_stats.created_at' => array( 'type' => "datetime", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_sub_services.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_sub_services.type' => array( 'type' => "enum('service','spare_time')", 'is_nullabe' => 0, 'extra' => "", 'default' => "service", 'key' => "" ),
            'bookly_sub_services.service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_sub_services.sub_service_id' => array( 'type' => "int unsigned", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "MUL" ),
            'bookly_sub_services.duration' => array( 'type' => "int", 'is_nullabe' => 1, 'extra' => "", 'default' => null, 'key' => "" ),
            'bookly_sub_services.position' => array( 'type' => "int", 'is_nullabe' => 0, 'extra' => "", 'default' => "9999", 'key' => "" ),
            'bookly_taxes.id' => array( 'type' => "int unsigned", 'is_nullabe' => 0, 'extra' => "auto_increment", 'default' => null, 'key' => "PRI" ),
            'bookly_taxes.title' => array( 'type' => "varchar(255)", 'is_nullabe' => 1, 'extra' => "", 'default' => "", 'key' => "" ),
            'bookly_taxes.rate' => array( 'type' => "decimal(10,3)", 'is_nullabe' => 0, 'extra' => "", 'default' => "0.000", 'key' => "" ),
        );

        /** @global \wpdb $wpdb */
        global $wpdb;

        $prefix_len = strlen( $wpdb->prefix );
        $key = substr( $table, $prefix_len ) . '.' . $column;

        return array_key_exists( $key, $data )
            ? $data[ $key ]
            : array();
    }

    /**
     * Get constraint rules
     *
     * @param string $table
     * @param string $column
     * @param string $ref_table
     * @param string $ref_column
     * @return array
     */
    public static function getConstraintRules( $table, $column, $ref_table, $ref_column )
    {
        /*
           SELECT CONCAT( '\'', SUBSTR(kcu.TABLE_NAME,4), '.', kcu.COLUMN_NAME , '\' => array( \'', SUBSTR(kcu.REFERENCED_TABLE_NAME,4), '.', kcu.REFERENCED_COLUMN_NAME, '\' => array( \'UPDATE_RULE\' => \'', rc.UPDATE_RULE, '\', \'DELETE_RULE\' => \'', rc.DELETE_RULE, '\', ), ),' ) AS c
             FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS rc
        LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS kcu ON ( rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME )
            WHERE rc.UNIQUE_CONSTRAINT_SCHEMA = SCHEMA()
              AND kcu.CONSTRAINT_NAME LIKE 'wp_bookly_%'
         ORDER BY kcu.TABLE_NAME,kcu.REFERENCED_TABLE_NAME,kcu.REFERENCED_COLUMN_NAME
         */

        $rules = array(
            'bookly_appointments.location_id' => array( 'bookly_locations.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_appointments.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_appointments.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_customers.coupon_id' => array( 'bookly_coupons.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_customers.customer_id' => array( 'bookly_customers.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_services.coupon_id' => array( 'bookly_coupons.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_services.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_staff.coupon_id' => array( 'bookly_coupons.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_coupon_staff.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_appointment_files.customer_appointment_id' => array( 'bookly_customer_appointments.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_appointment_files.file_id' => array( 'bookly_files.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_appointments.appointment_id' => array( 'bookly_appointments.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_appointments.customer_id' => array( 'bookly_customers.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_appointments.order_id' => array( 'bookly_orders.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_customer_appointments.package_id' => array( 'bookly_packages.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_customer_appointments.payment_id' => array( 'bookly_payments.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_customer_appointments.series_id' => array( 'bookly_series.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_groups_services.group_id' => array( 'bookly_customer_groups.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customer_groups_services.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_customers.group_id' => array( 'bookly_customer_groups.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_gift_card_type_services.gift_card_type_id' => array( 'bookly_gift_card_types.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_card_type_services.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_card_type_staff.gift_card_type_id' => array( 'bookly_gift_card_types.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_card_type_staff.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_cards.customer_id' => array( 'bookly_customers.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_cards.gift_card_type_id' => array( 'bookly_gift_card_types.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_gift_cards.payment_id' => array( 'bookly_payments.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_holidays.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_mailing_campaigns.mailing_list_id' => array( 'bookly_mailing_lists.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_mailing_list_recipients.mailing_list_id' => array( 'bookly_mailing_lists.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_packages.customer_id' => array( 'bookly_customers.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_packages.payment_id' => array( 'bookly_payments.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_packages.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_packages.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_payments.coupon_id' => array( 'bookly_coupons.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_payments.gift_card_id' => array( 'bookly_gift_cards.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_schedule_item_breaks.staff_schedule_item_id' => array( 'bookly_staff_schedule_items.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_sent_notifications.notification_id' => array( 'bookly_notifications.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_discounts.discount_id' => array( 'bookly_discounts.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_discounts.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_extras.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_schedule_breaks.service_schedule_day_id' => array( 'bookly_service_schedule_days.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_schedule_days.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_special_days.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_special_days_breaks.service_special_day_id' => array( 'bookly_service_special_days.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_taxes.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_service_taxes.tax_id' => array( 'bookly_taxes.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_services.category_id' => array( 'bookly_categories.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_special_days_breaks.staff_special_day_id' => array( 'bookly_staff_special_days.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff.category_id' => array( 'bookly_staff_categories.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'SET NULL', ), ),
            'bookly_staff_locations.location_id' => array( 'bookly_locations.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_locations.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_preference_orders.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_preference_orders.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_schedule_items.location_id' => array( 'bookly_locations.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_schedule_items.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_services.location_id' => array( 'bookly_locations.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_services.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_services.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_special_days.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_special_hours.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_staff_special_hours.staff_id' => array( 'bookly_staff.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_sub_services.sub_service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
            'bookly_sub_services.service_id' => array( 'bookly_services.id' => array( 'UPDATE_RULE' => 'CASCADE', 'DELETE_RULE' => 'CASCADE', ), ),
        );

        /** @global \wpdb $wpdb */
        global $wpdb;

        $prefix_len = strlen( $wpdb->prefix );
        $key = substr( $table, $prefix_len ) . '.' . $column;
        $ref = substr( $ref_table, $prefix_len ) . '.' . $ref_column;
        if ( isset( $rules[ $key ][ $ref ] ) ) {
            return $rules[ $key ][ $ref ];
        } else {
            return array( 'UPDATE_RULE' => null, 'DELETE_RULE' => null );
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private static function getColumnDataType( array $data )
    {
        return sprintf(
            '%s %s %s',
            $data['type'],
            $data['is_nullabe']
                ? 'null'
                : 'not null',
            $data['extra'] === 'auto_increment'
                ? ( 'auto_increment' . ( $data['key'] === 'PRI' ? ' primary key' : '' ) )
                : ( $data['default'] === null ? ( $data['is_nullabe'] ? 'default null' : '' ) : 'default \'' . $data['default'] . '\'' )
        );
    }
}