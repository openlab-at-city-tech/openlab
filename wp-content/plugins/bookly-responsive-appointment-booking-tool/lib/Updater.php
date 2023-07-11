<?php
namespace Bookly\Lib;

/**
 * Class Updater
 *
 * @package Bookly
 */
class Updater extends Base\Updater
{
    function update_21_8()
    {
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_notifications_queue' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `token` VARCHAR(255) NOT NULL,
                `data` TEXT DEFAULT NULL,
                `sent` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME NOT NULL
            ) ENGINE = INNODB
            ' . $charset_collate
        );
    }

    function update_21_7()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` MODIFY slot_length VARCHAR(32) DEFAULT \'default\' NOT NULL',
                'ALTER TABLE `%s` MODIFY color VARCHAR(32) DEFAULT \'#FFFFFF\' NOT NULL',
                'ALTER TABLE `%s` MODIFY deposit VARCHAR(16) DEFAULT \'100%%\' NOT NULL',
                'ALTER TABLE `%s` MODIFY start_time_info VARCHAR(32) DEFAULT \'\' NULL',
                'ALTER TABLE `%s` MODIFY end_time_info VARCHAR(32) DEFAULT \'\' NULL',
                'ALTER TABLE `%s` ADD COLUMN `waiting_list_capacity` INT UNSIGNED DEFAULT NULL AFTER `capacity_max`',
            ),
            'bookly_payments' => array(
                'ALTER TABLE `%s` MODIFY target ENUM (\'appointments\', \'packages\', \'gift_cards\') DEFAULT \'appointments\' NOT NULL',
            ),
        ) );

        if ( ! $this->existsColumn( 'bookly_services', 'gateways' ) ) {
            $this->alterTables( array(
                'bookly_services' => array(
                    'ALTER TABLE `%s` ADD COLUMN `gateways` VARCHAR(255) DEFAULT NULL',
                ),
            ) );
        }

        add_option( 'bookly_email_gateway', 'wp' );
        add_option( 'bookly_smtp_host', '' );
        add_option( 'bookly_smtp_port', '' );
        add_option( 'bookly_smtp_user', '' );
        add_option( 'bookly_smtp_password', '' );
        add_option( 'bookly_smtp_secure', 'none' );
    }

    function update_21_6()
    {
        add_option( 'bookly_app_show_slots', get_option( 'bookly_app_show_single_slot', false ) ? 'single' : 'all' );
        delete_option( 'bookly_app_show_single_slot' );
    }

    function update_21_5()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `gateways` VARCHAR(255) DEFAULT NULL',
            ),
            'bookly_sessions' => array(
                'ALTER TABLE `%s` ADD COLUMN `name` VARCHAR(255) DEFAULT NULL AFTER `token`',
            ),
            'bookly_notifications' => array(
                'ALTER TABLE `%s` CHANGE `gateway` `gateway` ENUM("email","sms","voice","whatsapp") NOT NULL DEFAULT "email"',
            ),
        ) );
    }

    function update_21_4()
    {
        $this->alterTables( array(
            'bookly_mailing_queue' => array(
                'ALTER TABLE `%s` ADD COLUMN `name` VARCHAR(255) DEFAULT NULL AFTER `phone`',
            ),
            'bookly_notifications' => array(
                'UPDATE `%s` SET `type` = \'appointment_reminder\' WHERE `type` = \'verify_email\' and `gateway` = \'voice\'',
            ),
        ) );
    }

    function update_21_3()
    {
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_sessions' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `token` VARCHAR(255) NOT NULL,
                `value` TEXT DEFAULT NULL,
                `expire` DATETIME NOT NULL,
                INDEX `token` (`token`),
                INDEX `expire` (`expire`)
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $this->alterTables( array(
            'bookly_notifications' => array(
                'ALTER TABLE `%s` CHANGE `gateway` `gateway` ENUM("email","sms","voice") NOT NULL DEFAULT "email"',
            ),
            'bookly_payments' => array(
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("local", "free", "paypal", "authorize_net", "stripe", "2checkout", "payu_biz", "payu_latam", "payson", "mollie", "woocommerce", "cloud_stripe", "cloud_square", "cloud_gift" ) NOT NULL DEFAULT "local"',
            ),
        ) );

        add_option( 'bookly_gen_session_type', 'php' );

        $this->addL10nOptions( array(
            'bookly_l10n_button_time_prev' => __( '&lt;', 'bookly' ),
            'bookly_l10n_button_time_next' => __( '&gt;', 'bookly' ),
        ) );

        $this->addNotifications( array(
            array(
                'gateway' => 'voice',
                'type' => 'appointment_reminder',
                'name' => __( 'Evening reminder to customer about next day appointment (requires cron setup)', 'bookly' ),
                'message' => __( "Dear {client_name}.\nWe would like to remind you that you have booked {service_name} tomorrow at {appointment_time}. We are waiting for you at {company_address}.\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ),
                'to_customer' => 1,
                'settings' => '{"status":"any","option":2,"services":{"any":"any","ids":[]},"offset_hours":1,"perform":"before","at_hour":18,"before_at_hour":18,"offset_before_hours":-24,"offset_bidirectional_hours":-24}',
            ),
        ) );
    }

    function update_21_2()
    {
        $currency = get_option( 'bookly_pmt_currency' );
        if ( $currency === 'RMB' ) {
            update_option( 'bookly_pmt_currency', 'CNY' );
        }
        $this->alterTables( array(
            'bookly_payments' => array(
                'ALTER TABLE `%s` ADD COLUMN `gift_card_id` INT UNSIGNED DEFAULT NULL AFTER `coupon_id`',
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("local", "free", "paypal", "authorize_net", "stripe", "2checkout", "payu_biz", "payu_latam", "payson", "mollie", "woocommerce", "cloud_stripe", "square", "gift_card") NOT NULL DEFAULT "local"',
            ),
        ) );
    }

    function update_21_1()
    {
        $this->alterTables( array(
            'bookly_shop' => array(
                'TRUNCATE TABLE `%s`',
                'ALTER TABLE `%s` ADD COLUMN `image` VARCHAR(255) NOT NULL AFTER `icon`',
            ),
        ) );
    }

    function update_21_0()
    {
        $this->alterTables( array(
            'bookly_log' => array(
                'ALTER TABLE `%s` CHANGE `action` `action` ENUM("create","update","delete","error") DEFAULT NULL',
            ),
            'bookly_categories' => array(
                'ALTER TABLE `%s` ADD COLUMN `info` TEXT DEFAULT NULL AFTER `name`',
                'ALTER TABLE `%s` ADD COLUMN `attachment_id` INT UNSIGNED DEFAULT NULL AFTER `name`',
            ),
            'bookly_payments' => array(
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("local", "free", "paypal", "authorize_net", "stripe", "2checkout", "payu_biz", "payu_latam", "payson", "mollie", "woocommerce", "cloud_stripe", "square") NOT NULL DEFAULT "local"',
            ),
        ) );
        add_option( 'bookly_gen_delete_data_on_uninstall', '0' );
        add_option( 'bookly_app_show_category_info', '0' );
    }

    function update_20_9()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` CHANGE `online_meetings` `online_meetings` ENUM("off","zoom","google_meet","jitsi","bbb") NOT NULL DEFAULT "off"',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` CHANGE `online_meeting_provider` `online_meeting_provider` ENUM("zoom","google_meet","jitsi","bbb") DEFAULT NULL',
            ),
        ) );
        add_option( 'bookly_cloud_cron_api_key', '' );
        $options = array(
            'bookly_l10n_qr_code_description' => 'bookly_l10n_ics_customer_template',
        );

        $this->renameL10nStrings( $options );
        if ( ! get_option( 'bookly_l10n_ics_customer_template' ) ) {
            update_option( 'bookly_l10n_ics_customer_template', "{service_name}\n{staff_name}" );
        }

        add_option( 'bookly_ics_staff_template', "{client_name}\n{client_phone}\n{status}" );
    }

    function update_20_8()
    {
        $this->alterTables( array(
            'bookly_mailing_queue' => array(
                'ALTER TABLE `%s` ADD COLUMN `campaign_id` INT NOT NULL DEFAULT 0 AFTER `sent`',
            ),
            'bookly_mailing_campaigns' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `state` `state` ENUM(\'pending\', \'in-progress\', \'completed\', \'canceled\') NOT NULL DEFAULT \'pending\'',
            ),
        ) );

        $this->dropTableColumns( $this->getTableName( 'bookly_customer_appointments' ), array( 'extras_consider_duration' ) );
    }

    function update_20_6()
    {
        $this->alterTables( array(
            'bookly_payments' => array(
                'ALTER TABLE `%s` ADD COLUMN `target` ENUM("appointments","packages") NOT NULL DEFAULT "appointments" AFTER `id`',
                'ALTER TABLE `%s` ADD COLUMN `ref_id` VARCHAR(255) DEFAULT NULL AFTER `details`',
                'ALTER TABLE `%s` CHANGE COLUMN `status` `status` ENUM("pending","completed","rejected","refunded") NOT NULL DEFAULT "completed"',
            ),
        ) );

        add_option( 'bookly_cloud_stripe_custom_metadata', '0' );
        add_option( 'bookly_cloud_stripe_metadata', array() );
        add_option( 'bookly_cal_show_new_appointments_badge', '0' );
        add_option( 'bookly_cal_last_seen_appointment', '0' );
    }

    function update_20_5()
    {
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_mailing_lists' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) DEFAULT NULL
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_mailing_list_recipients' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `mailing_list_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(255) DEFAULT NULL,
                `phone` VARCHAR(255) DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
            CONSTRAINT
                FOREIGN KEY (mailing_list_id)
                REFERENCES ' . $this->getTableName( 'bookly_mailing_lists' ) . ' (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_mailing_campaigns' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `mailing_list_id` INT UNSIGNED NULL,
                `name` VARCHAR(255) DEFAULT NULL,
                `text` TEXT DEFAULT NULL,
                `state`  ENUM("pending","completed") NOT NULL DEFAULT "pending",
                `send_at` DATETIME NOT NULL,
                `created_at` DATETIME NOT NULL,
            CONSTRAINT
                FOREIGN KEY (mailing_list_id)
                REFERENCES ' . $this->getTableName( 'bookly_mailing_lists' ) . ' (`id`)
                ON DELETE SET NULL 
                ON UPDATE CASCADE
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_mailing_queue' ) . '` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `phone` VARCHAR(255) NOT NULL,
                `text` TEXT DEFAULT NULL,
                `sent` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME NOT NULL
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` CHANGE `online_meetings` `online_meetings` ENUM("off","zoom","google_meet","jitsi") NOT NULL DEFAULT "off"',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` CHANGE `online_meeting_provider` `online_meeting_provider` ENUM("zoom","google_meet","jitsi") DEFAULT NULL',
            ),
        ) );

        add_option( 'bookly_cloud_auto_recharge_end_at', '' );
        add_option( 'bookly_cloud_auto_recharge_end_at_ts', '0' );
        add_option( 'bookly_cloud_auto_recharge_gateway', '' );
        add_option( 'bookly_cloud_renew_auto_recharge_notice_hide_until', '-1' );
        add_option( 'bookly_cloud_badge_consider_sms', '1' );
        add_option( 'bookly_cal_month_view_style', 'classic' );
        add_option( 'bookly_gen_badge_consider_news', '1' );
    }

    function update_20_4()
    {
        $self = $this;

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-add-tokens-1', function () use ( $self ) {
            $self->alterTables( array(
                'bookly_staff' => array(
                    'ALTER TABLE `%s` ADD COLUMN `gateways` VARCHAR(255) DEFAULT NULL',
                    'ALTER TABLE `%s` ADD COLUMN `icalendar_days_after` INT NOT NULL DEFAULT 365 AFTER `zoom_oauth_token`',
                    'ALTER TABLE `%s` ADD COLUMN `icalendar_days_before` INT NOT NULL DEFAULT 365 AFTER `zoom_oauth_token`',
                    'ALTER TABLE `%s` ADD COLUMN `icalendar_token` VARCHAR(255) DEFAULT NULL AFTER `zoom_oauth_token`',
                    'ALTER TABLE `%s` ADD COLUMN `icalendar` TINYINT(1) NOT NULL DEFAULT 0 AFTER `zoom_oauth_token`',
                ),
            ) );

            add_option( 'bookly_app_show_terms', '0' );
            add_option( 'bookly_app_show_download_ics', '0' );
            add_option( 'bookly_co_email' );
            add_option( 'bookly_co_industry' );
            add_option( 'bookly_co_size' );

            $self->addL10nOptions( array(
                'bookly_l10n_button_download_ics' => __( 'Download ICS', 'bookly' ),
                'bookly_l10n_label_terms' => __( 'I agree to the terms of service', 'bookly' ),
                'bookly_l10n_error_terms' => __( 'You must accept our terms', 'bookly' ),
            ) );
        } );

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-add-tokens-2', function () use ( $self ) {
            /** @global \wpdb $wpdb */
            global $wpdb;

            // Setup tokens for existing payments
            $staff_table = $self->getTableName( 'bookly_staff' );

            $wpdb->query( $wpdb->prepare( 'UPDATE `' . $staff_table . '` SET `icalendar_token` = MD5(CONCAT(%s,id)) WHERE icalendar_token IS NULL', md5( uniqid( time(), true ) ) ) );
        } );

        foreach ( $disposable_options as $option_name ) {
            delete_option( $option_name );
        }
    }

    function update_20_3()
    {
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_orders' ) . '` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `token`      VARCHAR(255) DEFAULT NULL
              ) ENGINE = INNODB
              ' . $charset_collate
        );

        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` CHANGE `online_meetings` `online_meetings` ENUM("off","zoom","google_meet") NOT NULL DEFAULT "off"',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` CHANGE `online_meeting_provider` `online_meeting_provider` ENUM("zoom", "google_meet") DEFAULT NULL',
            ),
            'bookly_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `order_id` INT UNSIGNED DEFAULT NULL AFTER `payment_id`',
                'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`order_id`) REFERENCES `' . $this->getTableName( 'bookly_orders' ) . '` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
            ),
        ) );

        add_option( 'bookly_app_show_single_slot', '0' );
    }

    function update_20_2()
    {
        global $wpdb;

        $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS `%s`', $this->getTableName( 'bookly_messages' ) ) );

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_news' ) . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `news_id`     INT UNSIGNED NOT NULL,
                `title`       TEXT,
                `media_type`  ENUM("image","youtube") NOT NULL DEFAULT "image",
                `media_url`   VARCHAR(255) NOT NULL,
                `text`        TEXT,
                `button_url`  VARCHAR(255) DEFAULT NULL,
                `button_text` VARCHAR(255) DEFAULT NULL,
                `seen`        TINYINT(1) NOT NULL DEFAULT 0,
                `updated_at`  DATETIME NOT NULL,
                `created_at`  DATETIME NOT NULL
              ) ENGINE = INNODB
              ' . $charset_collate
        );

        $this->addL10nOptions( array(
            'bookly_l10n_step_service_service_info' => '{service_info}',
            'bookly_l10n_step_service_staff_info' => '{staff_info}',
        ) );

        $this->renameOptions( array(
            'bookly_gen_default_appointment_status' => 'bookly_appointment_default_status',
            'bookly_cst_cancel_action' => 'bookly_appointment_cancel_action',
        ) );

        add_option( 'bookly_app_show_staff_info', '0' );
    }

    function update_20_1()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `attachment_id` INT UNSIGNED DEFAULT NULL AFTER `title`',
                'ALTER TABLE `%s` ADD COLUMN `min_time_prior_cancel` INT DEFAULT NULL AFTER `wc_cart_info`',
                'ALTER TABLE `%s` ADD COLUMN `min_time_prior_booking` INT DEFAULT NULL AFTER `wc_cart_info`',
            ),
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `color` VARCHAR(255) NOT NULL DEFAULT "#dddddd"',
            ),
        ) );
        add_option( 'bookly_cal_coloring_mode', 'service' );
        add_option( 'bookly_appointment_status_pending_color', '#1e73be' );
        add_option( 'bookly_appointment_status_approved_color', '#81d742' );
        add_option( 'bookly_appointment_status_cancelled_color', '#eeee22' );
        add_option( 'bookly_appointment_status_rejected_color', '#dd3333' );
        add_option( 'bookly_appointment_status_mixed_color', '#8224e3' );
    }

    function update_20_0()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `wc_cart_info` TEXT DEFAULT NULL AFTER `final_step_url`',
                'ALTER TABLE `%s` ADD COLUMN `wc_cart_info_name` VARCHAR(255) DEFAULT NULL AFTER `final_step_url`',
                'ALTER TABLE `%s` ADD COLUMN `wc_product_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `final_step_url`',
            ),
        ) );

        if ( get_option( 'bookly_cst_verify_customer_details' ) === 'always' ) {
            if ( in_array( 'phone', get_option( 'bookly_cst_required_details', array() ) ) ) {
                update_option( 'bookly_cst_verify_customer_details', 'always_phone' );
            } else {
                update_option( 'bookly_cst_verify_customer_details', 'always_email' );
            }
        }
    }

    function update_19_9()
    {
        add_option( 'bookly_app_show_service_info', '0' );
        add_option( 'bookly_cst_verify_customer_details', '0' );

        $this->addNotifications( array(
                array(
                    'gateway' => 'email',
                    'type' => 'verify_email',
                    'name' => __( 'Notification to customer with verification code', 'bookly' ),
                    'subject' => __( 'Bookly verification code', 'bookly' ),
                    'message' => '{verification_code}',
                    'active' => 1,
                    'to_customer' => 1,
                    'settings' => '[]',
                ),
                array(
                    'gateway' => 'sms',
                    'type' => 'verify_phone',
                    'name' => __( 'Notification to customer with verification code', 'bookly' ),
                    'message' => '{verification_code}',
                    'active' => 1,
                    'to_customer' => 1,
                    'settings' => '[]',
                ),
            )
        );
    }

    function update_19_8()
    {
        $this->addL10nOptions( array(
            'bookly_l10n_email_in_use' => __( 'This email is already in use', 'bookly' ),
        ) );

        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `final_step_url` VARCHAR(512) NOT NULL DEFAULT "" AFTER `online_meetings`',
            ),
        ) );
    }

    function update_19_6()
    {
        global $wpdb;

        $this->alterTables( array(
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `zoom_authentication` ENUM("default", "jwt", "oauth") NOT NULL DEFAULT "default"',
                'ALTER TABLE `%s` ADD COLUMN `zoom_oauth_token` TEXT DEFAULT NULL',
            ),
        ) );

        $staff_table = $this->getTableName( 'bookly_staff' );
        $wpdb->update( $staff_table, array( 'zoom_authentication' => 'jwt' ), array( 'zoom_personal' => '1' ) );

        $this->dropTableColumns( $staff_table, array( 'zoom_personal' ) );
    }

    function update_19_5()
    {
        $this->alterTables( array(
            'bookly_notifications' => array(
                'ALTER TABLE `%s` ADD COLUMN `custom_recipients` VARCHAR(255) DEFAULT NULL AFTER `to_admin`',
                'ALTER TABLE `%s` ADD COLUMN `to_custom` TINYINT(1) NULL DEFAULT 0 AFTER `to_admin`',
            ),
        ) );
    }

    function update_19_3()
    {
        $this->alterTables( array(
            'bookly_appointments' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
            ),
            'bookly_customer_appointments' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
            ),
            'bookly_payments' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
            ),
        ) );
        $this->addL10nOptions( array(
            'bookly_l10n_step_done_button_start_over' => __( 'Start over', 'bookly' ),
        ) );

        add_option( 'bookly_app_show_start_over', '0' );
        add_option( 'bookly_gen_prevent_session_locking', '0' );
    }

    function update_19_0()
    {
        $this->alterTables( array(
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `zoom_personal` TINYINT(1) NULL DEFAULT 0',
                'ALTER TABLE `%s` ADD COLUMN `zoom_jwt_api_key` VARCHAR(255) DEFAULT NULL',
                'ALTER TABLE `%s` ADD COLUMN `zoom_jwt_api_secret` VARCHAR(255) DEFAULT NULL',
            ),
            'bookly_customers' => array(
                'ALTER TABLE `%s` ADD COLUMN `stripe_account` VARCHAR(255) DEFAULT NULL AFTER `info_fields`',
            ),
        ) );
    }

    function update_18_9()
    {
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'bookly_log' ) . '` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `action`     ENUM("create","update","delete") DEFAULT NULL,
                `target`     VARCHAR(255) DEFAULT NULL,
                `target_id`  INT UNSIGNED DEFAULT NULL,
                `author`     VARCHAR(255) DEFAULT NULL,
                `details`    TEXT DEFAULT NULL,
                `ref`        VARCHAR(255) DEFAULT NULL,
                `comment`    VARCHAR(255) DEFAULT NULL,
                `created_at` DATETIME NOT NULL
              ) ENGINE = INNODB
              ' . $charset_collate
        );

        $this->alterTables( array(
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `time_zone` VARCHAR(255) DEFAULT NULL AFTER `phone`',
            ),
        ) );

        add_option( 'bookly_logs_enabled', '0' );

        $this->renameOptions( array(
            'bookly_app_show_powered_by' => 'bookly_gen_show_powered_by',
            'bookly_app_prevent_caching' => 'bookly_gen_prevent_caching',
        ) );

        if ( get_option( 'bookly_cloud_notify_weekly_summary' ) && get_option( 'bookly_cloud_token' ) ) {
            wp_remote_post( 'https://cloud.bookly.pro/1.0/users/' . get_option( 'bookly_cloud_token' ) . '/weekly-summary/send', array(
                'sslverify' => false,
                'timeout' => 10,
                'body' => array( 'site_url' => site_url(), 'bookly' => '18.9', ),
            ) );
        }
        delete_option( 'bookly_cloud_notify_weekly_summary' );
        delete_option( 'bookly_cloud_notify_weekly_summary_sent' );
    }

    function update_18_7()
    {
        global $wpdb;

        $self = $this;

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-change-schema', function () use ( $self ) {
            $self->alterTables( array(
                'bookly_appointments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `updated_at` DATETIME DEFAULT NULL',
                ),
                'bookly_customer_appointments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `updated_at` DATETIME DEFAULT NULL',
                ),
                'bookly_payments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `updated_at` DATETIME DEFAULT NULL',
                ),
            ) );
        } );

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-set-updated_at', function () use ( $self, $wpdb ) {
            foreach ( array( 'bookly_appointments', 'bookly_customer_appointments', 'bookly_payments' ) as $table ) {
                $wpdb->query( 'UPDATE `' . $self->getTableName( $table ) . '` SET `updated_at` = `created` WHERE `updated_at` IS null' );
            }
        } );
        $disposable_options[] = $this->disposable( __FUNCTION__ . '-rename', function () use ( $self ) {
            $self->alterTables( array(
                'bookly_appointments' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_customers' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_customer_appointments' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_messages' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_payments' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL',
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_sent_notifications' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_shop' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
                'bookly_stats' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `created` `created_at` DATETIME NOT NULL',
                ),
            ) );
        } );

        add_option( 'bookly_cloud_zapier_api_key', '' );
        add_option( 'bookly_cal_show_only_business_days', '0' );
        add_option( 'bookly_cal_show_only_business_hours', '0' );
        add_option( 'bookly_cal_show_only_staff_with_appointments', '0' );

        foreach ( $disposable_options as $option_name ) {
            delete_option( $option_name );
        }
    }

    function update_18_6()
    {
        $this->addL10nOptions( array(
            'bookly_l10n_label_pay_cloud_stripe' => __( 'I will pay now with Credit Card', 'bookly' ),
        ) );

        add_option( 'bookly_cloud_stripe_enabled', '0' );
        add_option( 'bookly_cloud_stripe_timeout', '0' );
        add_option( 'bookly_cloud_stripe_increase', '0' );
        add_option( 'bookly_cloud_stripe_addition', '0' );

        $this->alterTables( array(
            'bookly_payments' => array(
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("local", "free", "paypal", "authorize_net", "stripe", "2checkout", "payu_biz", "payu_latam", "payson", "mollie", "woocommerce", "cloud_stripe") NOT NULL DEFAULT "local"',
            ),
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `same_staff_for_subservices` TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_time_info`',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `online_meeting_data` TEXT DEFAULT NULL AFTER `online_meeting_id`',
            ),
        ) );

        $this->renameOptions( array(
            'bookly_sms_notify_low_balance' => 'bookly_cloud_notify_low_balance',
            'bookly_sms_notify_weekly_summary' => 'bookly_cloud_notify_weekly_summary',
            'bookly_sms_notify_weekly_summary_sent' => 'bookly_cloud_notify_weekly_summary_sent',
            'bookly_sms_token' => 'bookly_cloud_token',
            'bookly_sms_promotions' => 'bookly_cloud_promotions',
        ) );
        $this->renameUserMeta( array(
            'bookly_dismiss_sms_confirm_email' => 'bookly_dismiss_cloud_confirm_email',
            'bookly_dismiss_sms_promotion_notices' => 'bookly_dismiss_cloud_promotion_notices',
            'bookly_sms_purchases_table_settings' => 'bookly_cloud_purchases_table_settings',
        ) );
        $this->deleteUserMeta( array( 'bookly_dismiss_sms_account_settings_notice' ) );
    }

    function update_18_4()
    {
        add_option( 'bookly_sms_promotions', array() );
        delete_option( 'bookly_sms_unverified_token' );
        delete_option( 'bookly_sms_unverified_username' );
    }

    function update_18_3()
    {
        $self = $this;
        $disposable_options[] = $this->disposable( __FUNCTION__ . '-add-tokens-1', function () use ( $self ) {
            $self->alterTables( array(
                'bookly_payments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `token` VARCHAR(255) DEFAULT NULL AFTER `status`',
                ),
            ) );
        } );

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-add-tokens-2', function () use ( $self ) {
            /** @global \wpdb $wpdb */
            global $wpdb;

            // Setup tokens for existing payments
            $payments_table = $self->getTableName( 'bookly_payments' );

            foreach ( $wpdb->get_results( 'SELECT id FROM `' . $payments_table . '` WHERE token IS NULL' ) as $record ) {
                $wpdb->query( $wpdb->prepare( 'UPDATE `' . $payments_table . '` SET `token` = %s WHERE id = %d', Utils\Common::generateToken( 'Bookly\Lib\Entities\Payment', 'token' ), $record->id ) );
            }
        } );

        foreach ( $disposable_options as $option_name ) {
            delete_option( $option_name );
        }
    }

    function update_18_0()
    {
        $this->alterTables( array(
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `online_meetings` ENUM("off","zoom") NOT NULL DEFAULT "off" AFTER `collaborative_equal_duration`',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `online_meeting_provider` ENUM("zoom") DEFAULT NULL AFTER `outlook_event_series_id`',
                'ALTER TABLE `%s` ADD COLUMN `online_meeting_id` VARCHAR(255) DEFAULT NULL AFTER `online_meeting_provider`',
            ),
        ) );

        add_option( 'bookly_сa_count', '0' );
        $this->updateUserMeta( array( 'bookly_dismiss_nps_notice' => '0' ) );
    }

    function update_17_9()
    {
        $plugins = apply_filters( 'bookly_plugins', array() );

        if ( ! array_key_exists( 'bookly-addon-pro', $plugins ) ) {
            delete_option( 'bookly_l10n_label_ccard_code' );
            delete_option( 'bookly_l10n_label_ccard_expire' );
            delete_option( 'bookly_l10n_label_ccard_number' );
        }
        if ( ! array_key_exists( 'bookly-addon-mollie', $plugins ) ) {
            delete_option( 'bookly_l10n_label_pay_mollie' );
        }
        delete_option( 'bookly_l10n_label_pay_ccard' );

        foreach ( array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ) as $day ) {
            foreach ( array( 'start', 'end' ) as $tail ) {
                $option = sprintf( 'bookly_bh_%s_%s', $day, $tail );
                $value = get_option( $option );
                if ( $value != '' ) {
                    list( $hours, $minutes ) = explode( ':', $value );

                    update_option( $option, sprintf( '%02d:%02d:00', $hours, $minutes ) );
                }
            }
        }
    }

    function update_17_8()
    {
        global $wpdb;

        $meta_names = array(
            'bookly_filter_appointments_list',
            'bookly_filter_staff_list',
            'bookly_filter_services_list',
            'bookly_filter_staff_categories',
            'bookly_filter_services_categories',
        );
        $wpdb->query(
            $wpdb->prepare(
                sprintf(
                    'DELETE FROM `' . $wpdb->usermeta . '` WHERE meta_key IN (%s)',
                    implode( ', ', array_fill( 0, count( $meta_names ), '%s' ) )
                ), $meta_names
            )
        );
    }

    function update_17_7()
    {
        add_option( 'bookly_app_prevent_caching', '1' );
    }

    function update_17_6()
    {
        $this->alterTables( array(
            'bookly_payments' => array(
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("local", "free", "paypal", "authorize_net", "stripe", "2checkout", "payu_biz", "payu_latam", "payson", "mollie", "woocommerce") NOT NULL DEFAULT "local"',
            ),
        ) );

        $payments_table = $this->getTableName( 'bookly_payments' );

        $disposable_options[] = $this->disposable( __FUNCTION__ . '-add-gateway', function () use ( $payments_table ) {
            /** @global \wpdb $wpdb */
            global $wpdb;

            $update = 'UPDATE `' . $payments_table . '` SET `details` = %s WHERE id = %d';
            $records = $wpdb->get_results( 'SELECT id, `type`, `details` FROM `' . $payments_table . '` WHERE `details` NOT LIKE \'%"gateway"%\'', ARRAY_A );
            foreach ( $records as $record ) {
                $details = str_replace( '"extras_multiply_nop"', '"gateway":"' . $record['type'] . '","extras_multiply_nop"', $record['details'] );
                $wpdb->query( $wpdb->prepare( $update, $details, $record['id'] ) );
            }
        } );

        add_option( 'bookly_app_show_powered_by', '0' );

        foreach ( $disposable_options as $option_name ) {
            delete_option( $option_name );
        }
    }

    function update_17_5()
    {
        global $wpdb;

        $minutes = (int) get_option( 'bookly_gen_time_slot_length' );
        if ( $minutes > 0 ) {
            $wpdb->query( 'UPDATE `' . $this->getTableName( 'bookly_appointments' ) . '` SET end_date = DATE_ADD(start_date, INTERVAL ' . $minutes . ' MINUTE) WHERE start_date > end_date AND start_date IS NOT NULL AND end_date IS NOT NULL' );
        }
    }

    function update_17_3()
    {
        $this->upgradeCharsetCollate( array(
            'bookly_appointments',
            'bookly_categories',
            'bookly_customer_appointments',
            'bookly_customers',
            'bookly_holidays',
            'bookly_messages',
            'bookly_notifications',
            'bookly_payments',
            'bookly_schedule_item_breaks',
            'bookly_sent_notifications',
            'bookly_series',
            'bookly_services',
            'bookly_shop',
            'bookly_staff',
            'bookly_staff_schedule_items',
            'bookly_staff_services',
            'bookly_stats',
            'bookly_sub_services',
        ) );
    }

    function update_16_9()
    {
        $this->alterTables( array(
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `outlook_data` TEXT DEFAULT NULL AFTER `google_data`',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `outlook_event_id` VARCHAR(255) DEFAULT NULL AFTER `google_event_etag`',
                'ALTER TABLE `%s` ADD COLUMN `outlook_event_change_key` VARCHAR(255) DEFAULT NULL AFTER `outlook_event_id`',
                'ALTER TABLE `%s` ADD COLUMN `outlook_event_series_id` VARCHAR(255) DEFAULT NULL AFTER `outlook_event_change_key`',
                'ALTER TABLE `%s` CHANGE `created_from` `created_from` ENUM("bookly","google","outlook") NOT NULL DEFAULT "bookly"',
            ),
        ) );
    }

    function update_16_8()
    {
        global $wpdb;

        $self = $this;
        $default_settings = json_decode( '{"status":"any","option":2,"services":{"any":"any","ids":[]},"offset_hours":2,"perform":"before","at_hour":9,"before_at_hour":18,"offset_before_hours":-24,"offset_bidirectional_hours":0}', true );
        $notifications_table = $this->getTableName( 'bookly_notifications' );
        $notifications = array(
            'appointment_start_time' => array( 'type' => 'appointment_reminder', 'name' => __( 'Custom notification', 'bookly' ) . ': ' . __( 'Appointment reminder', 'bookly' ) ),
            'ca_created' => array( 'type' => 'new_booking', 'name' => __( 'Custom notification', 'bookly' ) . ': ' . __( 'New booking', 'bookly' ) ),
            'ca_status_changed' => array( 'type' => 'ca_status_changed', 'name' => __( 'Custom notification', 'bookly' ) . ': ' . __( 'Notification about customer\'s appointment status change', 'bookly' ) ),
            'client_approved_appointment' => array( 'type' => 'new_booking', 'name' => __( 'Notification to customer about approved appointment', 'bookly' ) ),
            'client_birthday_greeting' => array( 'type' => 'customer_birthday', 'name' => __( 'Customer birthday greeting (requires cron setup)', 'bookly' ) ),
            'client_cancelled_appointment' => array( 'type' => 'ca_status_changed', 'name' => __( 'Notification to customer about cancelled appointment', 'bookly' ) ),
            'client_follow_up' => array( 'type' => 'appointment_reminder', 'name' => __( 'Follow-up message in the same day after appointment (requires cron setup)', 'bookly' ) ),
            'client_pending_appointment' => array( 'type' => 'new_booking', 'name' => __( 'Notification to customer about pending appointment', 'bookly' ) ),
            'client_rejected_appointment' => array( 'type' => 'ca_status_changed', 'name' => __( 'Notification to customer about rejected appointment', 'bookly' ) ),
            'client_reminder' => array( 'type' => 'appointment_reminder', 'name' => __( 'Evening reminder to customer about next day appointment (requires cron setup)', 'bookly' ) ),
            'client_reminder_1st' => array( 'type' => 'appointment_reminder', 'name' => __( '1st reminder to customer about upcoming appointment (requires cron setup)', 'bookly' ) ),
            'client_reminder_2nd' => array( 'type' => 'appointment_reminder', 'name' => __( '2nd reminder to customer about upcoming appointment (requires cron setup)', 'bookly' ) ),
            'client_reminder_3rd' => array( 'type' => 'appointment_reminder', 'name' => __( '3rd reminder to customer about upcoming appointment (requires cron setup)', 'bookly' ) ),
            'last_appointment' => array( 'type' => 'last_appointment', 'name' => __( 'Custom notification', 'bookly' ) . ': ' . __( 'Last client\'s appointment', 'bookly' ) ),
            'staff_agenda' => array( 'type' => 'staff_day_agenda', 'name' => __( 'Evening notification with the next day agenda to staff member (requires cron setup)', 'bookly' ) ),
            'staff_approved_appointment' => array( 'type' => 'new_booking', 'name' => __( 'Notification to staff member about approved appointment', 'bookly' ) ),
            'staff_cancelled_appointment' => array( 'type' => 'ca_status_changed', 'name' => __( 'Notification to staff member about cancelled appointment', 'bookly' ) ),
            'staff_day_agenda' => array( 'type' => 'staff_day_agenda', 'name' => __( 'Custom notification', 'bookly' ) . ': ' . __( 'Full day agenda', 'bookly' ) ),
            'staff_pending_appointment' => array( 'type' => 'new_booking', 'name' => __( 'Notification to staff member about pending appointment', 'bookly' ) ),
            'staff_rejected_appointment' => array( 'type' => 'ca_status_changed', 'name' => __( 'Notification to staff member about rejected appointment', 'bookly' ) ),
        );

        // Changes in schema
        $disposable_options[] = $this->disposable( __FUNCTION__ . '-1', function () use ( $self, $wpdb, $notifications_table, $notifications, $default_settings ) {
            $wpdb->query( 'UPDATE `' . $wpdb->usermeta . '` SET meta_key = \'bookly_dismiss_feature_requests_description\' WHERE meta_key = \'bookly_feature_requests_rules_hide\'' );
            if ( ! $self->existsColumn( 'bookly_notifications', 'name' ) ) {
                $self->alterTables( array(
                    'bookly_notifications' => array(
                        'ALTER TABLE `%s` ADD COLUMN `name` VARCHAR(255) NOT NULL DEFAULT "" AFTER `active`',
                    ),
                ) );
            }
            $self->alterTables( array(
                'bookly_customer_appointments' => array(
                    'ALTER TABLE `%s` CHANGE `status` `status` VARCHAR(255) NOT NULL DEFAULT "approved"',
                ),
                'bookly_shop' => array(
                    'ALTER TABLE `%s` ADD COLUMN `demo_url` VARCHAR(255) DEFAULT NULL AFTER `type`',
                    'ALTER TABLE `%s` ADD COLUMN `priority` INT UNSIGNED DEFAULT 0 AFTER `type`',
                    'ALTER TABLE `%s` ADD COLUMN `highlighted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `type`',
                ),
            ) );

            $update_name = 'UPDATE `' . $notifications_table . '` SET `name` = %s WHERE `type` = %s AND name = \'\'';
            foreach ( $notifications as $type => $value ) {
                $wpdb->query( $wpdb->prepare( $update_name, $value['name'], $type ) );

                switch ( substr( $type, 0, 6 ) ) {
                    case 'staff_':
                        $wpdb->query( sprintf( 'UPDATE `%s` SET `to_staff` = 1 WHERE `type` = "%s"', $notifications_table, $type ) );
                        break;
                    case 'client':
                        $wpdb->query( sprintf( 'UPDATE `%s` SET `to_customer` = 1 WHERE `type` = "%s"', $notifications_table, $type ) );
                        break;
                }
            }

            $update_settings = 'UPDATE `' . $notifications_table . '` SET `settings` = %s WHERE id = %d';
            $records = $wpdb->get_results( 'SELECT id, `settings`, `type` FROM `' . $notifications_table . '` WHERE `type` IN (\'appointment_start_time\', \'customer_birthday\', \'last_appointment\', \'ca_status_changed\', \'ca_created\')', ARRAY_A );
            foreach ( $records as $record ) {
                $new_settings = $default_settings;
                if ( $record['settings'] != '[]' && $record['settings'] != '' ) {
                    $current_settings = (array) json_decode( $record['settings'], true );
                    switch ( $record['type'] ) {
                        case 'appointment_start_time':
                        case 'last_appointment':
                            $set = $current_settings['existing_event_with_date_and_time'];
                            $new_settings['option'] = $set['option'];
                            $new_settings['offset_hours'] = $set['offset_hours'];
                            $new_settings['at_hour'] = $set['at_hour'];
                            $new_settings['offset_bidirectional_hours'] = $set['offset_bidirectional_hours'];
                            if ( $record['type'] !== 'last_appointment' ) {
                                if ( isset( $set['services']['any'] ) && $set['services']['any'] ) {
                                    $new_settings['services']['any'] = 'any';
                                } elseif ( isset( $set['services']['ids'] ) && is_array( $set['services']['ids'] ) && count( $set['services']['ids'] ) > 0 ) {
                                    $new_settings['services']['any'] = 'selected';
                                    $new_settings['services']['ids'] = $set['services']['ids'];
                                }
                            } else {
                                $new_settings['status'] = $set['status'];
                            }
                            break;
                        case 'staff_day_agenda':
                            $set = $current_settings['existing_event_with_date_before'];
                            $new_settings['at_hour'] = $set['at_hour'];
                            $new_settings['offset_bidirectional_hours'] = $set['offset_bidirectional_hours'];
                            break;
                        case 'customer_birthday':
                            $set = $current_settings['existing_event_with_date'];
                            $new_settings['at_hour'] = $set['at_hour'];
                            $new_settings['offset_bidirectional_hours'] = $set['offset_bidirectional_hours'];
                            break;
                        case 'ca_status_changed':
                        case 'ca_created':
                            $set = $current_settings['after_event'];
                            $new_settings['status'] = $set['status'];
                            $new_settings['option'] = $set['option'] - 1;
                            $new_settings['offset_hours'] = $set['offset_hours'];
                            $new_settings['at_hour'] = $set['at_hour'];
                            $new_settings['offset_bidirectional_hours'] = $set['offset_bidirectional_hours'];
                            if ( isset( $set['services']['any'] ) && $set['services']['any'] ) {
                                $new_settings['services']['any'] = 'any';
                            } elseif ( isset( $set['services']['ids'] ) && is_array( $set['services']['ids'] ) && count( $set['services']['ids'] ) > 0 ) {
                                $new_settings['services']['any'] = 'selected';
                                $new_settings['services']['ids'] = $set['services']['ids'];
                            }
                            break;
                    }
                }
                $wpdb->query( $wpdb->prepare( $update_settings, json_encode( $new_settings ), $record['id'] ) );
            }
        } );

        // WPML
        $disposable_options[] = $this->disposable( __FUNCTION__ . '-2', function () use ( $self, $wpdb, $notifications_table, $notifications ) {
            $records = $wpdb->get_results( $wpdb->prepare( 'SELECT id, `type`, `gateway` FROM `' . $notifications_table . '` WHERE COALESCE( `settings`, \'[]\' ) = \'[]\' AND `type` IN (' . implode( ', ', array_fill( 0, count( $notifications ), '%s' ) ) . ')', array_keys( $notifications ) ), ARRAY_A );
            $strings = array();
            foreach ( $records as $record ) {
                $type = $record['type'];
                if ( isset( $notifications[ $type ]['type'] ) && $type != $notifications[ $type ]['type'] ) {
                    $key = sprintf( '%s_%s_%d', $record['gateway'], $type, $record['id'] );
                    $value = sprintf( '%s_%s_%d', $record['gateway'], $notifications[ $type ]['type'], $record['id'] );
                    $strings[ $key ] = $value;
                    if ( $record['gateway'] == 'email' ) {
                        $strings[ $key . '_subject' ] = $value . '_subject';
                    }
                }
            }
            $self->renameL10nStrings( $strings, false );
        } );

        // Add settings for notifications
        $disposable_options[] = $this->disposable( __FUNCTION__ . '-3', function () use ( $wpdb, $notifications_table, $notifications, $default_settings ) {
            $combined_notifications = get_option( 'bookly_cst_combined_notifications', 'missing' );
            if ( $combined_notifications === 'missing' ) {
                $combined_notifications = (bool) $wpdb->query( 'SELECT 1 FROM `' . $notifications_table . '` WHERE `type` = \'new_booking_combined\' AND `active` = 1 LIMIT 1' );
            }
            $combined_notifications_disabled = (int) ! $combined_notifications;
            $cron_reminder_times = get_option( 'bookly_cron_reminder_times' );
            $insert_from_select = 'INSERT INTO `' . $notifications_table . '` (`gateway`, `name`, `subject`, `message`, `to_staff`, `to_customer`, `to_admin`, `attach_ics`, `attach_invoice`, `active`,  `settings`, `type`) 
                SELECT `gateway`, `name`, `subject`, `message`, `to_staff`, `to_customer`, `to_admin`, `attach_ics`, `attach_invoice`, %d, %s, %s
                  FROM `' . $notifications_table . '` WHERE id = %d';
            $update_settings = 'UPDATE `' . $notifications_table . '` SET `type` = %s, `settings` = %s, `active` = %d WHERE id = %d';

            $records = $wpdb->get_results( $wpdb->prepare( 'SELECT id, `type`, `gateway`, `message`, `subject`, `active`, `settings` FROM `' . $notifications_table . '` WHERE `type` IN (' . implode( ', ', array_fill( 0, count( $notifications ), '%s' ) ) . ')', array_keys( $notifications ) ), ARRAY_A );
            foreach ( $records as $record ) {
                if ( ! isset( $notifications[ $record['type'] ]['type'] )
                    || $notifications[ $record['type'] ]['type'] == $record['type']
                ) {
                    continue;
                }
                if ( $record['settings'] != '[]' && $record['settings'] != '' ) {
                    $settings = (array) json_decode( $record['settings'], true );
                } else {
                    $settings = $default_settings;
                    $settings['services']['any'] = 'any';
                    $settings['services']['ids'] = array();
                }
                $clone_type = null;
                $new_type = $notifications[ $record['type'] ]['type'];
                $new_active = $record['active'];
                if ( isset( $settings[ $new_type ]['services']['any'] ) && ! $settings[ $new_type ]['services']['any'] ) {
                    $settings['services']['ids'] = $settings[ $new_type ]['services']['ids'];
                    $settings['services']['any'] = 'selected';
                }
                switch ( $record['type'] ) {
                    case 'client_approved_appointment':
                        $settings['status'] = 'approved';
                        $clone_type = ( $combined_notifications_disabled && $record['active'] ) ? 'ca_status_changed' : null;
                        $new_active = $combined_notifications_disabled ? $record['active'] : 0;
                        break;
                    case 'client_birthday_greeting':
                        $settings['at_hour'] = (int) $cron_reminder_times['client_birthday_greeting'];
                        break;
                    case 'client_cancelled_appointment':
                        $settings['status'] = 'cancelled';
                        $clone_type = ( $combined_notifications_disabled && $record['active'] ) ? 'new_booking' : null;
                        break;
                    case 'client_follow_up':
                        $settings['option'] = 2;
                        $settings['at_hour'] = (int) $cron_reminder_times['client_follow_up'];
                        break;
                    case 'client_pending_appointment':
                        $settings['status'] = 'pending';
                        $clone_type = ( $combined_notifications_disabled && $record['active'] ) ? 'ca_status_changed' : null;
                        $new_active = $combined_notifications_disabled ? $record['active'] : 0;
                        break;
                    case 'client_rejected_appointment':
                        $settings['status'] = 'rejected';
                        $clone_type = ( $combined_notifications_disabled && $record['active'] ) ? 'new_booking' : null;
                        break;
                    case 'client_reminder':
                        $settings['option'] = 2;
                        $settings['offset_hours'] = 1;
                        $settings['perform'] = 'before';
                        $settings['at_hour'] = (int) $cron_reminder_times['client_reminder'];
                        $settings['offset_bidirectional_hours'] = -24;
                        break;
                    case 'client_reminder_1st':
                        $settings['option'] = 1;
                        $settings['offset_hours'] = (int) $cron_reminder_times['client_reminder_1st'];
                        $settings['perform'] = 'before';
                        $settings['at_hour'] = 18;
                        $settings['offset_bidirectional_hours'] = -24;
                        break;
                    case 'client_reminder_2nd':
                        $settings['option'] = 1;
                        $settings['offset_hours'] = (int) $cron_reminder_times['client_reminder_2nd'];
                        $settings['perform'] = 'before';
                        $settings['at_hour'] = 18;
                        $settings['offset_bidirectional_hours'] = -24;
                        break;
                    case 'client_reminder_3rd':
                        $settings['option'] = 1;
                        $settings['offset_hours'] = (int) $cron_reminder_times['client_reminder_3rd'];
                        $settings['perform'] = 'before';
                        $settings['at_hour'] = 18;
                        $settings['offset_bidirectional_hours'] = -24;
                        break;
                    case 'staff_agenda':
                        $settings['option'] = 3;
                        $settings['before_at_hour'] = (int) $cron_reminder_times['staff_agenda'];
                        $settings['offset_before_hours'] = -24;
                        break;
                    case 'staff_approved_appointment':
                        $settings['status'] = 'approved';
                        $clone_type = $record['active'] ? 'ca_status_changed' : null;
                        break;
                    case 'staff_cancelled_appointment':
                        $settings['status'] = 'cancelled';
                        $clone_type = $record['active'] ? 'new_booking' : null;
                        break;
                    case 'staff_pending_appointment':
                        $settings['status'] = 'pending';
                        $clone_type = $record['active'] ? 'ca_status_changed' : null;
                        break;
                    case 'staff_rejected_appointment':
                        $settings['status'] = 'rejected';
                        $clone_type = $record['active'] ? 'new_booking' : null;
                        break;
                }
                if ( $clone_type ) {
                    $wpdb->query( $wpdb->prepare( $insert_from_select, $new_active, json_encode( $settings ), $clone_type, $record['id'] ) );
                    $name = sprintf( '%s_%s_%d', $record['gateway'], $clone_type, $wpdb->insert_id );
                    do_action( 'wpml_register_single_string', 'bookly', $name, $record['message'] );
                    if ( $record['gateway'] == 'email' ) {
                        do_action( 'wpml_register_single_string', 'bookly', $name . '_subject', $record['subject'] );
                    }
                }
                $wpdb->query( $wpdb->prepare( $update_settings, $new_type, json_encode( $settings ), $new_active, $record['id'] ) );
            }
        } );

        delete_option( 'bookly_cron_reminder_times' );
        foreach ( $disposable_options as $option_name ) {
            delete_option( $option_name );
        }
    }

    function update_16_2()
    {
        global $wpdb;

        $this->dropTableForeignKeys( $this->getTableName( 'bookly_staff_schedule_items' ), array( 'staff_id' ) );

        $this->alterTables( array(
            'bookly_staff' => array(
                'ALTER TABLE `%s` ADD COLUMN `category_id` INT UNSIGNED DEFAULT NULL AFTER `id`',
                'ALTER TABLE `%s` ADD COLUMN `working_time_limit` INT UNSIGNED DEFAULT NULL AFTER `info`',
                'ALTER TABLE `%s` CHANGE COLUMN `visibility` `visibility` ENUM(\'public\',\'private\',\'archive\') NOT NULL DEFAULT \'public\'',
            ),
            'bookly_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `time_requirements` ENUM("required","optional","off") NOT NULL DEFAULT "required" AFTER `recurrence_frequencies`',
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("simple","collaborative","compound","package") NOT NULL DEFAULT "simple"',
                'ALTER TABLE `%s` ADD COLUMN `collaborative_equal_duration` TINYINT(1) NOT NULL DEFAULT 0 AFTER `time_requirements`',
                'ALTER TABLE `%s` ADD COLUMN `deposit` VARCHAR(100) NOT NULL DEFAULT "100%%" AFTER `color`',
                'ALTER TABLE `%s` CHANGE `staff_preference` `staff_preference` ENUM("order", "least_occupied", "most_occupied", "least_occupied_for_period", "most_occupied_for_period", "least_expensive", "most_expensive") NOT NULL DEFAULT "most_expensive"',
                'ALTER TABLE `%s` ADD COLUMN `staff_preference_settings` TEXT DEFAULT NULL AFTER `staff_preference`',
                'ALTER TABLE `%s` ADD COLUMN `one_booking_per_slot` TINYINT(1) NOT NULL DEFAULT 0 AFTER `capacity_max`',
                'ALTER TABLE `%s` CHANGE `limit_period` `limit_period` ENUM("off","day","week","month","year","upcoming","calendar_day","calendar_week","calendar_month","calendar_year") NOT NULL DEFAULT "off"',
                'ALTER TABLE `%s` ADD COLUMN `slot_length` VARCHAR(255) NOT NULL DEFAULT "default" AFTER `duration`',
            ),
            'bookly_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `series_id` INT UNSIGNED DEFAULT NULL AFTER `id`',
                'ALTER TABLE `%s` ADD COLUMN `extras_consider_duration` TINYINT(1) NOT NULL DEFAULT 1 AFTER `extras`',
                'ALTER TABLE `%s` ADD COLUMN `extras_multiply_nop` TINYINT(1) NOT NULL DEFAULT 1 AFTER `extras`',
                'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`series_id`) REFERENCES `' . $this->getTableName( 'bookly_series' ) . '` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
                'ALTER TABLE `%s` ADD COLUMN `collaborative_token` VARCHAR(255) DEFAULT NULL AFTER `locale`',
                'ALTER TABLE `%s` ADD COLUMN `collaborative_service_id` INT UNSIGNED DEFAULT NULL AFTER `locale`',
            ),
            'bookly_staff_schedule_items' => array(
                'ALTER TABLE `%s` ADD COLUMN `location_id` INT UNSIGNED DEFAULT NULL AFTER `staff_id`',
                'ALTER TABLE `%s` DROP INDEX `unique_ids_idx`',
                'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`staff_id`) REFERENCES `' . $this->getTableName( 'bookly_staff' ) . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
                'ALTER TABLE `%s` ADD UNIQUE KEY unique_ids_idx (staff_id, day_index, location_id)',
            ),
            'bookly_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `created` DATETIME DEFAULT NULL',
            ),
        ) );

        $wpdb->query( sprintf( 'UPDATE `%s` SET `staff_preference_settings` = "{}"', $this->getTableName( 'bookly_services' ) ) );
        $wpdb->query( sprintf( 'UPDATE `%s` `ca` LEFT JOIN `%s` `a` ON `a`.`id` = `ca`.`appointment_id` SET `ca`.`series_id`=`a`.`series_id`', $this->getTableName( 'bookly_customer_appointments' ), $this->getTableName( 'bookly_appointments' ) ) );

        $this->dropTableColumns( $this->getTableName( 'bookly_appointments' ), array( 'series_id' ) );

        $notifications_table = $this->getTableName( 'bookly_notifications' );
        $records = $wpdb->get_results(
            sprintf(
                'SELECT id, `settings` FROM `%s` WHERE `type` IN (\'appointment_start_time\', \'customer_birthday\', \'last_appointment\', \'ca_status_changed\', \'ca_created\')',
                $notifications_table
            ), ARRAY_A
        );

        $for_any_services = array( 'services' => array( 'any' => 1, 'ids' => array() ) );
        foreach ( $records as $record ) {
            $settings = (array) json_decode( $record['settings'], true );
            foreach ( array( 'after_event', 'existing_event_with_date_and_time' ) as $set ) {
                $result = array_merge( $for_any_services, $settings[ $set ] );
                $settings[ $set ] = $result;
            }
            $wpdb->query(
                sprintf(
                    'UPDATE `%s` SET `settings`= \'%s\' WHERE id = %d',
                    $notifications_table,
                    json_encode( $settings ),
                    $record['id']
                )
            );
        }

        add_option( 'bookly_app_align_buttons_left', '0' );
        add_option( 'bookly_app_show_email_confirm', '0' );

        $this->addL10nOptions( array(
            'bookly_l10n_label_email_confirm' => __( 'Confirm email', 'bookly' ),
            'bookly_l10n_email_confirm_not_match' => __( 'Email confirmation doesn\'t match', 'bookly' ),
        ) );
    }

    function update_16_0()
    {
        global $wpdb;

        $this->alterTables( array(
            'ab_appointments' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `start_date` `start_date` DATETIME DEFAULT NULL',
                'ALTER TABLE `%s` CHANGE COLUMN `end_date` `end_date` DATETIME DEFAULT NULL',
            ),
            'ab_customer_appointments' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `status` `status` ENUM("pending","approved","cancelled","rejected","waitlisted","done") NOT NULL DEFAULT "approved"',
            ),
            'ab_customers' => array(
                'ALTER TABLE `%s` ADD COLUMN `street_number` VARCHAR(255) DEFAULT NULL AFTER `street`',
            ),
        ) );

        $bookly_cst_address_show_fields = get_option( 'bookly_cst_address_show_fields' );
        $bookly_cst_address_show_fields['street_number'] = array( 'show' => 1 );
        update_option( 'bookly_cst_address_show_fields', $bookly_cst_address_show_fields );

        $this->renameOptions( array(
            'bookly_lic_repeat_time' => 'bookly_pr_show_time',
        ) );

        add_option( 'bookly_pr_data', array(
            'SW1wb3J0YW50ITxicj5JdCBsb29rcyBsaWtlIHlvdSBhcmUgdXNpbmcgYW4gaWxsZWdhbCBjb3B5IG9mIEJvb2tseS4gQW5kIGl0IG1heSBjb250YWluIGEgbWFsaWNpb3VzIGNvZGUsIGEgdHJvamFuIG9yIGEgYmFja2Rvb3Iu',
            'Q29uc2lkZXIgc3dpdGNoaW5nIHRvIHRoZSBsZWdhbCBjb3B5IG9mIEJvb2tseSB0aGF0IGluY2x1ZGVzIGFsbCBmZWF0dXJlcywgbGlmZXRpbWUgZnJlZSB1cGRhdGVzLCBhbmQgMjQvNyBzdXBwb3J0Lg==',
            'WW91IGNhbiBidXkgbGVnYWwgY29weSBhdCBvdXIgd2Vic2l0ZSA8YSBocmVmPSJodHRwczovL3d3dy5ib29raW5nLXdwLXBsdWdpbi5jb20iIHRhcmdldD0iX2JsYW5rIj53d3cuYm9va2luZy13cC1wbHVnaW4uY29tPC9hPiwgb3IgY29udGFjdCBhcyBhdCA8YSBocmVmPSJtYWlsdG86c3VwcG9ydEBsYWRlbGEuY29tIj5zdXBwb3J0QGxhZGVsYS5jb208L2E+IGZvciBhbnkgYXNzaXN0YW5jZS4=',
        ) );
        add_option( 'bookly_cst_required_birthday', '1' );
        add_option( 'bookly_sms_undelivered_count', '0' );

        delete_option( 'bookly_last_updated_info' );
        delete_option( 'bookly_reminder_data' );

        // Rename tables.
        $tables = array(
            'appointments',
            'categories',
            'customers',
            'customer_appointments',
            'holidays',
            'messages',
            'notifications',
            'payments',
            'schedule_item_breaks',
            'sent_notifications',
            'series',
            'services',
            'shop',
            'staff',
            'staff_preference_orders',
            'staff_schedule_items',
            'staff_services',
            'stats',
            'sub_services',
        );
        $query = 'RENAME TABLE ';
        foreach ( $tables as $table ) {
            $query .= sprintf( '`%s` TO `%s`, ', $this->getTableName( 'ab_' . $table ), $this->getTableName( 'bookly_' . $table ) );
        }
        $query = substr( $query, 0, -2 );
        $wpdb->query( $query );

        // Make 'Collect stats' notice appear again.
        if ( get_option( 'bookly_gen_collect_stats' ) == '0' ) {
            foreach ( get_users( 'role=administrator' ) as $user ) {
                delete_user_meta( $user->ID, 'bookly_dismiss_collect_stats_notice' );
            }
        }

        // Find out what legacy version we are updating from.
        if ( get_option( 'bookly_gen_lite_uninstall_remove_bookly_data' ) !== false ) {
            // Lite.
            add_option( 'bookly_updated_from_legacy_version', 'lite' );
            delete_option( 'bookly_gen_lite_uninstall_remove_bookly_data' );
            foreach ( get_users( 'role=administrator' ) as $user ) {
                add_user_meta( $user->ID, 'bookly_show_lite_rebranding_notice', '1' );
            }
            $wpdb->insert( $this->getTableName( 'bookly_messages' ), array(
                'message_id' => 0,
                'type' => 'simple',
                'subject' => 'Major update. Introducing the new Free version of Bookly.',
                'body' => 'Hello.<br/><br/>Recently Bookly Lite was updated to the latest version – Bookly 16.0. Bookly Lite rebrands into Bookly with more features available for free. Paid version will be available with Pro add-on and other add-ons to bring even more features and flexibility into the booking process.<br/><br/>Take a moment to read our <a href="https://www.booking-wp-plugin.com/bookly-major-update/?utm_campaign=migration_free&utm_medium=cpc&utm_source=newsletter" target="_blank">blog post</a> to see a full list of updates available in the new free version of Bookly.<br/><br/>Thank you',
                'created' => current_time( 'mysql' ),
            ) );
        } else {
            // Bookly.
            add_option( 'bookly_updated_from_legacy_version', 'bookly' );
        }
    }

    function update_15_1()
    {
        $this->alterTables( array(
            'ab_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `units_min` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `end_time_info`',
                'ALTER TABLE `%s` ADD COLUMN `units_max` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `units_min`',
            ),
        ) );
    }

    function update_15_0()
    {
        global $wpdb;

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_shop' ) . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `plugin_id`   INT UNSIGNED NOT NULL,
                `type`        ENUM("plugin","bundle") NOT NULL DEFAULT "plugin",
                `title`       VARCHAR(255) NOT NULL,
                `slug`        VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `url`         VARCHAR(255) NOT NULL,
                `icon`        VARCHAR(255) NOT NULL,
                `price`       DECIMAL(10,2) NOT NULL,
                `sales`       INT UNSIGNED NOT NULL,
                `rating`      DECIMAL(10,2) NOT NULL,
                `reviews`     INT UNSIGNED NOT NULL,
                `published`   DATETIME NOT NULL,
                `seen`        TINYINT(1) NOT NULL DEFAULT 0,
                `created`     DATETIME NOT NULL
              ) ENGINE = INNODB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci'
        );

        $this->alterTables( array(
            'ab_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `units` INT UNSIGNED NOT NULL DEFAULT 1 after `number_of_persons`',
            ),
        ) );

        add_option( 'bookly_cst_allow_duplicates', '0' );
        update_option( 'bookly_reminder_data', array(
            'SW1wb3J0YW50ISBJdCBsb29rcyBsaWtlIHlvdSBhcmUgdXNpbmcgYW4gaWxsZWdhbCBjb3B5IG9mIEJvb2tseSDigJMgaXQgbWF5IGNvbnRhaW4gYSBtYWxpY2lvdXMgY29kZSwgYSB0cm9qYW4gb3IgYSBiYWNrZG9vci4=',
            'VGhlIGxlZ2FsIGNvcHkgb2YgQm9va2x5IGluY2x1ZGVzIGFsbCBmZWF0dXJlcywgbGlmZXRpbWUgZnJlZSB1cGRhdGVzIHdoaWNoIGludHJvZHVjZSBuZXcgZmVhdHVyZXMgYW5kIGltcG9ydGFudCBzZWN1cml0eSBmaXhlcywgYW5kIDI0Lzcgc3VwcG9ydC4=',
            'PGEgaHJlZj0iaHR0cHM6Ly93d3cuYm9va2luZy13cC1wbHVnaW4uY29tL2JlY29tZS1sZWdhbC8iIHRhcmdldD0iX2JsYW5rIj5DbGljayBoZXJlIHRvIGxlYXJuIG1vcmUgPj4+PC9hPg',
        ) );
    }

    function update_14_9()
    {
        global $wpdb;

        $payment_id = (int) get_option( 'bookly_processing_14_9' );

        if ( $payment_id === 0 ) {

            $this->alterTables( array(
                'ab_staff_services' => array(
                    'ALTER TABLE `%s` ADD COLUMN `location_id` INT UNSIGNED DEFAULT NULL AFTER `service_id`',
                ),
            ) );

            $refs = $wpdb->get_results(
                sprintf(
                    'SELECT `constraint_name`, `referenced_table_name` FROM `information_schema`.`key_column_usage`
                WHERE `TABLE_SCHEMA` = SCHEMA() AND `TABLE_NAME` = "%s" AND `REFERENCED_TABLE_NAME` IS NOT NULL',
                    $this->getTableName( 'ab_staff_services' )
                )
            );

            if ( $refs ) {
                foreach ( $refs as $ref ) {
                    $wpdb->query( sprintf( 'ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $this->getTableName( 'ab_staff_services' ), $ref->constraint_name ) );
                }
                $wpdb->query(
                    sprintf(
                        'ALTER TABLE `%s` DROP INDEX `unique_ids_idx`',
                        $this->getTableName( 'ab_staff_services' )
                    )
                );
                $wpdb->query(
                    sprintf(
                        'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (service_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
                        $this->getTableName( 'ab_staff_services' ),
                        $this->getTableName( 'ab_services' )
                    )
                );
                $wpdb->query(
                    sprintf(
                        'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (staff_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
                        $this->getTableName( 'ab_staff_services' ),
                        $this->getTableName( 'ab_staff' )
                    )
                );
                $wpdb->query(
                    sprintf(
                        'ALTER TABLE `%s` ADD UNIQUE KEY unique_ids_idx (staff_id, service_id, location_id)',
                        $this->getTableName( 'ab_staff_services' )
                    )
                );
            }

            $birthday_labels = array_combine( Utils\DateTime::getDatePartsOrder(), array( __( 'Birthday', 'bookly' ), '', '' ) );

            $this->alterTables( array(
                'ab_customers' => array(
                    'ALTER TABLE `%s` ADD COLUMN `additional_address` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                    'ALTER TABLE `%s` ADD COLUMN `street` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                    'ALTER TABLE `%s` ADD COLUMN `city` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                    'ALTER TABLE `%s` ADD COLUMN `postcode` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                    'ALTER TABLE `%s` ADD COLUMN `state` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                    'ALTER TABLE `%s` ADD COLUMN `country` VARCHAR(255) DEFAULT NULL AFTER `birthday`',
                ),
            ) );

            add_option( 'bookly_app_show_birthday', '0' );
            add_option( 'bookly_paypal_send_tax', '0' );
            add_option( 'bookly_app_show_facebook_login_button', '0' );
            add_option( 'bookly_fb_app_id', '' );

            add_option( 'bookly_cst_required_address', '0' );
            add_option( 'bookly_cst_address_show_fields', array(
                'country' => array( 'show' => 1 ),
                'state' => array( 'show' => 1 ),
                'postcode' => array( 'show' => 1 ),
                'city' => array( 'show' => 1 ),
                'street' => array( 'show' => 1 ),
                'additional_address' => array( 'show' => 1 ),
            ) );

            add_option( 'bookly_app_show_address', '0' );

            $required_details = array( 'email' );
            if ( get_option( 'bookly_cst_required_phone', 1 ) ) {
                $required_details[] = 'phone';
            }
            add_option( 'bookly_cst_required_details', $required_details );
            delete_option( 'bookly_cst_required_phone' );

            $this->addL10nOptions( array(
                'bookly_l10n_label_birthday_day' => $birthday_labels['day'],
                'bookly_l10n_label_birthday_month' => $birthday_labels['month'],
                'bookly_l10n_label_birthday_year' => $birthday_labels['year'],
                'bookly_l10n_invalid_day' => __( 'Invalid day', 'bookly' ),
                'bookly_l10n_required_day' => __( 'Day is required', 'bookly' ),
                'bookly_l10n_required_month' => __( 'Month is required', 'bookly' ),
                'bookly_l10n_required_year' => __( 'Year is required', 'bookly' ),
                'bookly_l10n_option_day' => __( 'Select day', 'bookly' ),
                'bookly_l10n_option_month' => __( 'Select month', 'bookly' ),
                'bookly_l10n_option_year' => __( 'Select year', 'bookly' ),
                'bookly_l10n_info_address' => __( 'Address', 'bookly' ),
                'bookly_l10n_label_country' => __( 'Country', 'bookly' ),
                'bookly_l10n_label_state' => __( 'State/Region', 'bookly' ),
                'bookly_l10n_label_postcode' => __( 'Postal Code', 'bookly' ),
                'bookly_l10n_label_city' => __( 'City', 'bookly' ),
                'bookly_l10n_label_street' => __( 'Street Address', 'bookly' ),
                'bookly_l10n_label_additional_address' => __( 'Additional Address', 'bookly' ),
                'bookly_l10n_required_country' => __( 'Country is required', 'bookly' ),
                'bookly_l10n_required_state' => __( 'State is required', 'bookly' ),
                'bookly_l10n_required_postcode' => __( 'Postcode is required', 'bookly' ),
                'bookly_l10n_required_city' => __( 'City is required', 'bookly' ),
                'bookly_l10n_required_street' => __( 'Street is required', 'bookly' ),
                'bookly_l10n_required_additional_address' => __( 'Additional address is required', 'bookly' ),
            ) );

            $cart_show_columns = get_option( 'bookly_cart_show_columns' );
            $cart_show_columns['tax'] = array( 'show' => '0' );
            update_option( 'bookly_cart_show_columns', $cart_show_columns );

            $this->alterTables( array(
                'ab_payments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `tax` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `total`',
                    'ALTER TABLE `%s` CHANGE COLUMN `type` `type` ENUM("local","coupon","paypal","authorize_net","stripe","2checkout","payu_biz","payu_latam","payson","mollie","woocommerce") NOT NULL DEFAULT "local"',
                ),
                'ab_customers' => array(
                    'ALTER TABLE `%s` ADD COLUMN `facebook_id` BIGINT(20) UNSIGNED DEFAULT NULL AFTER `wp_user_id`',
                ),
                'ab_notifications' => array(
                    'ALTER TABLE `%s` ADD COLUMN `attach_invoice` TINYINT(1) NOT NULL DEFAULT 0 AFTER `attach_ics`',
                ),
                'ab_appointments' => array(
                    'ALTER TABLE `%s` ADD COLUMN `google_event_etag` VARCHAR(255) DEFAULT NULL',
                    'ALTER TABLE `%s` ADD COLUMN `created_from` ENUM("bookly","google") NOT NULL DEFAULT "bookly"',
                ),
            ) );
            // Google Calendar options.
            update_option(
                'bookly_gc_event_title', strtr( get_option( 'bookly_gc_event_title' ), array(
                    '[[SERVICE_NAME]]' => '{service_name}',
                    '[[CLIENT_NAMES]]' => '{client_names}',
                    '[[STAFF_NAME]]' => '{staff_name}',
                ) )
            );
            add_option( 'bookly_gc_sync_mode', get_option( 'bookly_gc_two_way_sync' ) == '1' ? '1.5-way' : '1-way' );
            delete_option( 'bookly_gc_two_way_sync' );
            // Update google_data in staff table.
            $staff_table = $this->getTableName( 'ab_staff' );
            $rows = (array) $wpdb->get_results( sprintf( 'SELECT * FROM `%s` WHERE `google_data` IS NOT NULL', $staff_table ), ARRAY_A );
            foreach ( $rows as $row ) {
                $google_data = array(
                    'token' => $row['google_data'],
                    'calendar' => array(
                        'id' => $row['google_calendar_id'],
                        'sync_token' => null,
                    ),
                    'channel' => array(
                        'id' => null,
                        'resource_id' => null,
                        'expiration' => null,
                    ),
                );
                $wpdb->query(
                    $wpdb->prepare(
                        sprintf( 'UPDATE `%s` SET `google_data` = %%s WHERE `id` = %%d', $staff_table ),
                        json_encode( $google_data ),
                        $row['id']
                    )
                );
            }
            $this->alterTables( array(
                'ab_staff' => array(
                    'ALTER TABLE `%s` DROP COLUMN `google_calendar_id`',
                ),
            ) );
        }

        $payment_table = $this->getTableName( 'ab_payments' );
        $rows = (array) $wpdb->get_results( $wpdb->prepare( 'SELECT id, details FROM `' . $payment_table . '` WHERE id > %d', $payment_id ), ARRAY_A );
        foreach ( $rows as $row ) {
            $details = json_decode( $row['details'], true );
            $details['subtotal'] = array( 'price' => 0, 'deposit' => 0, );
            $details['tax_paid'] = null;
            $details['tax_in_price'] = 'excluded';
            if ( isset( $details['customer_group'] ) ) {
                $details['customer_group']['discount_format'] = strpos( $details['customer_group']['discount'], '%' ) === false ? Utils\Price::format( $details['customer_group']['discount'] ) : $details['customer_group']['discount'];
            }
            if ( isset( $details['adjustments'] ) ) {
                foreach ( $details['adjustments'] as &$adjustment ) {
                    $adjustment['tax'] = 0;
                }
            }
            if ( isset( $details['items'] ) ) {
                foreach ( $details['items'] as &$item ) {
                    $extras_price = 0;
                    $deposit_price = 0;
                    $price = $item['service_price'] * $item['number_of_persons'];
                    if ( ! empty( $item['extras'] ) ) {
                        foreach ( (array) $item['extras'] as $extras ) {
                            $extras_price += $extras['quantity'] * $extras['price'];
                        }
                        $price += $item['extras_multiply_nop'] ? $extras_price * $item['number_of_persons'] : $extras_price;
                    }

                    if ( Config::depositPaymentsActive() ) {
                        $deposit_price = Proxy\DepositPayments::prepareAmount( $price, $item['deposit'], $item['number_of_persons'] );
                        $item['deposit_format'] = Proxy\DepositPayments::formatDeposit( $deposit_price, $item['deposit'] );
                    } else {
                        $item['deposit_format'] = null;
                    }
                    $item['wait_listed'] = false;
                    $item['service_tax'] = null;
                    $details['subtotal']['price'] += $price;
                    $details['subtotal']['deposit'] += $deposit_price;
                }

                $wpdb->update( $payment_table, array( 'details' => json_encode( $details ) ), array( 'id' => $row['id'] ) );
            }
            update_option( 'bookly_processing_14_9', $row['id'] );
        }

        delete_option( 'bookly_processing_14_9' );
    }

    function update_14_8()
    {
        global $wpdb;

        $this->alterTables( array(
            'ab_customers' => array(
                'ALTER TABLE `%s` ADD COLUMN `info_fields` TEXT DEFAULT NULL',
                'ALTER TABLE `%s` ADD COLUMN `created` DATETIME DEFAULT NULL',
            ),
        ) );
        $wpdb->query( sprintf( 'UPDATE `%s` SET `info_fields` = "[]", `created` = NOW()', $this->getTableName( 'ab_customers' ) ) );
        $this->alterTables( array(
            'ab_customers' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `created` `created` DATETIME NOT NULL',
            ),
            'ab_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `rating_comment` TEXT DEFAULT NULL AFTER `time_zone_offset`',
                'ALTER TABLE `%s` ADD COLUMN `rating` INT DEFAULT NULL AFTER `time_zone_offset`',
            ),
        ) );

        update_option( 'bookly_pmt_price_format', str_replace( '{price', '{sign}{price', get_option( 'bookly_pmt_price_format' ) ) );
    }

    function update_14_6()
    {
        global $wpdb;

        $meta_id = (int) get_option( 'bookly_processing_wc_order_id' );
        if ( $meta_id == 0 ) {

            $this->alterTables( array(
                'ab_customers' => array(
                    'ALTER TABLE `%s` ADD COLUMN `group_id` INT UNSIGNED DEFAULT NULL AFTER `wp_user_id`',
                ),
                'ab_payments' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `status` `status` ENUM("pending","completed","rejected") NOT NULL DEFAULT "completed"',
                    'ALTER TABLE `%s` ADD COLUMN `coupon_id` INT UNSIGNED DEFAULT NULL AFTER `id`',
                    'ALTER TABLE `%s` ADD COLUMN `gateway_price_correction` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `paid_type`',
                ),
                'ab_services' => array(
                    'ALTER TABLE `%s` CHANGE COLUMN `visibility` `visibility` ENUM("public","private","group") NOT NULL DEFAULT "public"',
                    'ALTER TABLE `%s` ADD COLUMN `package_unassigned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `package_size`',
                ),
            ) );

            if ( $this->existsTable( 'icl_strings' ) ) {
                $rows = $wpdb->get_results(
                    sprintf(
                        'SELECT id, gateway, type FROM `%s`',
                        $this->getTableName( 'ab_notifications' )
                    )
                );

                $strings = array();
                foreach ( $rows as $row ) {
                    $name = sprintf( '%s_%s', $row->gateway, $row->type );
                    $strings[ $name ] = $name . '_' . $row->id;
                    if ( $row->gateway == 'email' ) {
                        $strings[ $name . '_subject' ] = $name . '_' . $row->id . '_subject';
                    }
                }
                $this->renameL10nStrings( $strings, false );
            }

            add_option( 'bookly_app_show_time_zone_switcher', '0' );
            add_option( 'bookly_paypal_increase', '0' );
            add_option( 'bookly_paypal_addition', '0' );

            $wc_exists = $this->existsTable( 'woocommerce_order_itemmeta' );
        } else {
            $wc_exists = true;
        }

        if ( $wc_exists ) {
            $wc_order_meta_table = $this->getTableName( 'woocommerce_order_itemmeta' );
            $rows = (array) $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT meta_id, meta_value FROM `' . $wc_order_meta_table . '` WHERE meta_key = \'bookly\' AND meta_id > %d',
                    $meta_id
                ), ARRAY_A
            );
            foreach ( $rows as $row ) {
                $meta = @unserialize( $row['meta_value'] );
                if ( isset( $meta['items'] ) ) {
                    $update = false;
                    foreach ( $meta['items'] as &$data ) {
                        if ( is_numeric( $data['slots'][0][2] ) ) {
                            $data['slots'][0][2] = date( 'Y-m-d H:i:s', $data['slots'][0][2] );
                            $update = true;
                        }
                    }
                    if ( $update ) {
                        $wpdb->update( $wc_order_meta_table, array( 'meta_value' => serialize( $meta ) ), array( 'meta_id' => $row['meta_id'] ) );
                    }
                }
                update_option( 'bookly_processing_wc_order_id', $row['meta_id'] );
            }

            delete_option( 'bookly_processing_wc_order_id' );
        }
    }

    function update_14_5()
    {
        $bookly_custom_fields = get_option( 'bookly_custom_fields', 'missing' );
        if ( $bookly_custom_fields != 'missing' ) {
            update_option( 'bookly_custom_fields_data', $bookly_custom_fields );
            delete_option( 'bookly_custom_fields' );
        }
        $bookly_custom_fields_merge_repetitive = get_option( 'bookly_custom_fields_merge_repetitive', 'missing' );
        if ( $bookly_custom_fields_merge_repetitive != 'missing' ) {
            update_option( 'bookly_custom_fields_merge_repeating', $bookly_custom_fields_merge_repetitive );
            delete_option( 'bookly_custom_fields_merge_repetitive' );
        }
    }

    function update_14_4()
    {
        global $wpdb;

        if ( get_option( 'bookly_pmt_local' ) != 1 ) {
            update_option( 'bookly_pmt_local', '0' );
        }

        add_option( 'bookly_url_cancel_confirm_page_url', home_url() );
        add_option( 'bookly_ntf_processing_interval', '2' );
        add_option( 'bookly_app_show_notes', '0' );
        add_option( 'bookly_reminder_data', array(
            'SW1wb3J0YW50ISBJdCBsb29rcyBsaWtlIHlvdSBhcmUgdXNpbmcgYW4gaWxsZWdhbCBjb3B5IG9mIEJvb2tseSDigJMgaXQgbWF5IGNvbnRhaW4gYSBtYWxpY2lvdXMgY29kZSwgYSB0cm9qYW4gb3IgYSBiYWNrZG9vci4=',
            'VGhlIGxlZ2FsIGNvcHkgb2YgQm9va2x5IGluY2x1ZGVzIGFsbCBmZWF0dXJlcywgbGlmZXRpbWUgZnJlZSB1cGRhdGVzLCBhbmQgMjQvNyBzdXBwb3J0LiBCeSBidXlpbmcgYSBsZWdhbCBjb3B5IG9mIEJvb2tseSBhdCBhIHNwZWNpYWwgZGlzY291bnRlZCBwcmljZSwgeW91IG1heSBiZW5lZml0IGZyb20gb3VyIHBhcnRuZXLigJlzIGV4Y2x1c2l2ZSBkaXNjb3VudHMh',
            'PGEgaHJlZj0iaHR0cHM6Ly93d3cuYm9va2luZy13cC1wbHVnaW4uY29tL2JlY29tZS1sZWdhbC8iIHRhcmdldD0iX2JsYW5rIj5DbGljayBoZXJlIHRvIGxlYXJuIG1vcmUgPj4+PC9hPg',
        ) );
        add_option( 'bookly_lic_repeat_time', time() + 7776000 );
        $this->addL10nOptions( array(
            'bookly_l10n_label_notes' => __( 'Notes', 'bookly' ),
        ) );

        $this->renameOptions( array(
            'bookly_pmt_paypal' => 'bookly_paypal_enabled',
            'bookly_pmt_paypal_sandbox' => 'bookly_paypal_sandbox',
            'bookly_pmt_paypal_api_password' => 'bookly_paypal_api_password',
            'bookly_pmt_paypal_api_signature' => 'bookly_paypal_api_signature',
            'bookly_pmt_paypal_api_username' => 'bookly_paypal_api_username',
            'bookly_pmt_paypal_id' => 'bookly_paypal_id',
            'bookly_custom_fields' => 'bookly_custom_fields_data',
            'bookly_custom_fields_merge_repetitive' => 'bookly_custom_fields_merge_repeating',
        ) );

        $this->alterTables( array(
            'ab_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `custom_service_name` VARCHAR(255) DEFAULT NULL AFTER `service_id`',
                'ALTER TABLE `%s` ADD COLUMN `custom_service_price` DECIMAL(10,2) DEFAULT NULL AFTER `custom_service_name`',
                'ALTER TABLE `%s` CHANGE COLUMN `service_id` `service_id` INT UNSIGNED DEFAULT NULL',
            ),
            'ab_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `status_changed_at` DATETIME NULL AFTER `status`',
                'ALTER TABLE `%s` ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `number_of_persons`',
            ),
            'ab_notifications' => array(
                'ALTER TABLE `%s` ADD COLUMN `attach_ics` TINYINT(1) NOT NULL DEFAULT 0 AFTER `to_admin`',
            ),
            'ab_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `recurrence_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `staff_preference`',
                'ALTER TABLE `%s` ADD COLUMN `recurrence_frequencies` SET("daily","weekly","biweekly","monthly") NOT NULL DEFAULT "daily,weekly,biweekly,monthly" AFTER `recurrence_enabled`',
            ),
        ) );

        // Remove `unique_ids_idx` index from `ab_sub_services`.
        $ref = $wpdb->get_row(
            sprintf(
                'SELECT `constraint_name`, `referenced_table_name` FROM `information_schema`.`key_column_usage`
                WHERE `TABLE_SCHEMA` = SCHEMA() AND `TABLE_NAME` = "%s" AND `COLUMN_NAME` = "service_id" AND `REFERENCED_TABLE_NAME` IS NOT NULL',
                $this->getTableName( 'ab_sub_services' )
            )
        );
        if ( $ref ) {
            $wpdb->query( sprintf( 'ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $this->getTableName( 'ab_sub_services' ), $ref->constraint_name ) );
            $this->alterTables( array(
                'ab_sub_services' => array(
                    'ALTER TABLE `%s` DROP INDEX `unique_ids_idx`',
                ),
            ) );
            $wpdb->query(
                sprintf(
                    'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (service_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
                    $this->getTableName( 'ab_sub_services' ),
                    $ref->referenced_table_name
                )
            );
        }

        foreach ( (array) json_decode( 'bookly_recurring_appointments_frequencies', true ) as $service_id => $frequencies ) {
            $wpdb->update(
                $this->getTableName( 'ab_services' ),
                array(
                    'recurrence_enabled' => $frequencies['enabled'],
                    'recurrence_frequencies' => implode( ',', $frequencies['frequencies'] ),
                ),
                array( 'id' => $service_id )
            );
        }
        delete_option( 'bookly_recurring_appointments_frequencies' );

        $notifications = (array) $wpdb->get_results(
            sprintf(
                'SELECT id, settings FROM `%s` WHERE `type` IN (\'%s\',\'%s\') AND `active` = 1',
                $this->getTableName( 'ab_notifications' ),
                'appointment_start_time',
                'last_appointment'
            )
        );

        foreach ( $notifications as $notification ) {
            $settings = (array) json_decode( $notification->settings, true );
            if ( $settings['existing_event_with_date_and_time']['status'] == '' ) {
                $settings['existing_event_with_date_and_time']['status'] = 'any';
                $wpdb->update( $this->getTableName( 'ab_notifications' ), array( 'settings' => json_encode( $settings ) ), array( 'id' => $notification->id ) );
            }
        }
    }

    function update_14_3()
    {
        $this->renameOptions( array(
            'bookly_gen_approve_page_url' => 'bookly_url_approve_page_url',
            'bookly_gen_approve_denied_page_url' => 'bookly_url_approve_denied_page_url',
            'bookly_gen_cancel_page_url' => 'bookly_url_cancel_page_url',
            'bookly_gen_cancel_denied_page_url' => 'bookly_url_cancel_denied_page_url',
            'bookly_gen_final_step_url' => 'bookly_url_final_step_url',
        ) );

        add_option( 'bookly_url_reject_page_url', home_url() );
        add_option( 'bookly_url_reject_denied_page_url', home_url() );

        $this->alterTables( array(
            'ab_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `end_time_info` VARCHAR(255) DEFAULT "" AFTER `info`',
                'ALTER TABLE `%s` ADD COLUMN `start_time_info` VARCHAR(255) DEFAULT "" AFTER `info`',
                'ALTER TABLE `%s` ADD COLUMN `appointments_limit` INT DEFAULT NULL AFTER `package_size`',
                'ALTER TABLE `%s` ADD COLUMN `limit_period` ENUM("off","day","week","month","year") NOT NULL DEFAULT "off" AFTER `appointments_limit`',
            ),
            'ab_customer_appointments' => array(
                'ALTER TABLE `%s` ADD COLUMN `package_id` INT UNSIGNED DEFAULT NULL AFTER `id`',
            ),
            'ab_notifications' => array(
                'ALTER TABLE `%s` ADD COLUMN `settings` TEXT NULL',
                'ALTER TABLE `%s` ADD COLUMN `to_staff` TINYINT(1) NOT NULL DEFAULT 0',
                'ALTER TABLE `%s` ADD COLUMN `to_customer` TINYINT(1) NOT NULL DEFAULT 0',
                'ALTER TABLE `%s` CHANGE COLUMN `copy` `to_admin` TINYINT(1) NOT NULL DEFAULT 0',
            ),
            'ab_sent_notifications' => array(
                'ALTER TABLE `%s` ADD COLUMN `notification_id` INT UNSIGNED',
                'UPDATE `%s` `sn` SET `sn`.`notification_id` = (SELECT `n`.`id` FROM `' . $this->getTableName( 'ab_notifications' ) . '` `n` WHERE `n`.`type` = `sn`.`type` LIMIT 1)',
                'ALTER TABLE `%s` CHANGE COLUMN `notification_id` INT UNSIGNED NOT NULL',
                'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`notification_id`) REFERENCES `' . $this->getTableName( 'ab_notifications' ) . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            ),
            'ab_sub_services' => array(
                'ALTER TABLE `%s` ADD COLUMN `type` ENUM("service","spare_time") NOT NULL DEFAULT "service" AFTER `id`',
                'ALTER TABLE `%s` ADD COLUMN `duration` INT DEFAULT NULL AFTER `sub_service_id`',
                'ALTER TABLE `%s` CHANGE COLUMN `sub_service_id` `sub_service_id` INT UNSIGNED DEFAULT NULL',
            ),
        ) );

        $this->dropTableColumns( $this->getTableName( 'ab_services' ), array( 'sub_services' ) );
        $this->dropTableColumns( $this->getTableName( 'ab_sent_notifications' ), array( 'type', 'gateway' ) );

        add_option( 'bookly_cst_show_update_details_dialog', '1' );
        add_option( 'bookly_custom_fields_merge_repetitive', '0' );

        $options = array(
            'bookly_l10n_info_complete_step_limit_error' => __( 'You are trying to use the service too often. Please contact us to make a booking.', 'bookly' ),
            'bookly_l10n_info_complete_step_processing' => __( 'Your payment has been accepted for processing.', 'bookly' ),
        );
        $this->addL10nOptions( $options );
    }

    function update_14_1()
    {
        global $wpdb;

        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE `type` `type` ENUM("simple","compound","package") NOT NULL DEFAULT "simple"',
                $this->getTableName( 'ab_services' )
            )
        );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD `package_life_time` INT DEFAULT NULL AFTER `type`',
                $this->getTableName( 'ab_services' )
            )
        );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD `package_size` INT DEFAULT NULL AFTER `package_life_time`',
                $this->getTableName( 'ab_services' )
            )
        );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD COLUMN `staff_preference` ENUM("order","least_occupied","most_occupied","least_expensive","most_expensive") NOT NULL DEFAULT "most_expensive" AFTER `package_size`',
                $this->getTableName( 'ab_services' )
            )
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_sub_services' ) . '` (
                `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `service_id`        INT UNSIGNED NOT NULL,
                `sub_service_id`    INT UNSIGNED NOT NULL,
                `position`          INT NOT NULL DEFAULT 9999,
                UNIQUE KEY unique_ids_idx (service_id, sub_service_id),
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . $this->getTableName( 'ab_services' ) . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (sub_service_id)
                    REFERENCES ' . $this->getTableName( 'ab_services' ) . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_messages' ) . '` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `message_id` INT UNSIGNED NOT NULL,
                `type`       VARCHAR(255) NOT NULL,
                `subject`    TEXT,
                `body`       TEXT,
                `seen`       TINYINT(1) NOT NULL DEFAULT 0,
                `created`    DATETIME NOT NULL
              ) ENGINE = INNODB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_staff_preference_orders' ) . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `service_id`  INT UNSIGNED NOT NULL,
                `staff_id`    INT UNSIGNED NOT NULL,
                `position`    INT NOT NULL DEFAULT 9999,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . $this->getTableName( 'ab_services' ) . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . $this->getTableName( 'ab_staff' ) . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `name` `full_name` VARCHAR(255) NOT NULL DEFAULT ""',
                $this->getTableName( 'ab_customers' )
            )
        );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s`
                ADD COLUMN `first_name` VARCHAR(255) NOT NULL DEFAULT "" AFTER `full_name`,
                ADD COLUMN `last_name` VARCHAR(255) NOT NULL DEFAULT "" AFTER `first_name`',
                $this->getTableName( 'ab_customers' )
            )
        );
        add_option( 'bookly_cst_first_last_name', '0' );

        $options = array(
            'bookly_l10n_label_first_name' => __( 'First name', 'bookly' ),
            'bookly_l10n_label_last_name' => __( 'Last name', 'bookly' ),
            'bookly_l10n_required_first_name' => __( 'Please tell us your first name', 'bookly' ),
            'bookly_l10n_required_last_name' => __( 'Please tell us your last name', 'bookly' ),
        );
        $this->addL10nOptions( $options );

        // Update first and last name fields from full name.
        $wpdb->query(
            sprintf(
                'UPDATE `%s` SET `first_name` = SUBSTRING_INDEX(`full_name`, " ", 1), `last_name` = TRIM(SUBSTR(`full_name`, LOCATE(" ", `full_name`)))',
                $this->getTableName( 'ab_customers' )
            )
        );

        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD `staff_any` TINYINT(1) NOT NULL DEFAULT 0 AFTER `staff_id`',
                $this->getTableName( 'ab_appointments' )
            )
        );

        // Move location from CustomerAppointment to Appointment.
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD `location_id` INT UNSIGNED DEFAULT NULL AFTER `series_id`',
                $this->getTableName( 'ab_appointments' )
            )
        );
        $wpdb->query(
            sprintf(
                'UPDATE `%s` `a` SET `a`.`location_id` = (SELECT `ca`.`location_id` FROM `%s` `ca` WHERE `ca`.`appointment_id` = `a`.`id` AND `ca`.`location_id` IS NOT NULL LIMIT 1)',
                $this->getTableName( 'ab_appointments' ),
                $this->getTableName( 'ab_customer_appointments' )
            )
        );
        $ref = $wpdb->get_row(
            sprintf(
                'SELECT `constraint_name`, `referenced_table_name` FROM `information_schema`.`key_column_usage`
                WHERE `TABLE_SCHEMA` = SCHEMA() AND `TABLE_NAME` = "%s" AND `COLUMN_NAME` = "location_id"',
                $this->getTableName( 'ab_customer_appointments' )
            )
        );
        if ( $ref ) {
            $wpdb->query( sprintf( 'ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $this->getTableName( 'ab_customer_appointments' ), $ref->constraint_name ) );
            $wpdb->query(
                sprintf(
                    'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (location_id) REFERENCES %s(id) ON DELETE SET NULL ON UPDATE CASCADE',
                    $this->getTableName( 'ab_appointments' ),
                    $ref->referenced_table_name
                )
            );
        }
        $wpdb->query( sprintf( 'ALTER TABLE `%s` DROP COLUMN `location_id`', $this->getTableName( 'ab_customer_appointments' ) ) );

        // Add 'waitlisted' status.
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE `status` `status` ENUM("pending","approved","cancelled","rejected","waitlisted") NOT NULL DEFAULT "approved"',
                $this->getTableName( 'ab_customer_appointments' )
            )
        );

        // Add new options.
        add_option( 'bookly_gen_approve_denied_page_url', get_option( 'bookly_gen_approve_page_url' ) );
    }

    function update_13_9()
    {
        global $wpdb;

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_stats' ) . '` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name`      VARCHAR(255) NOT NULL,
                `value`     TEXT,
                `created`   DATETIME NOT NULL
              ) ENGINE = INNODB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci'
        );

        add_option( 'bookly_app_show_login_button', '0' );
        add_option( 'bookly_cst_remember_in_cookie', '0' );
        $this->addL10nOptions( array( 'bookly_l10n_step_details_button_login' => __( 'Log In' ) ) );

        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD COLUMN `paid_type` ENUM("in_full","deposit") NOT NULL DEFAULT "in_full" AFTER `paid`',
                $this->getTableName( 'ab_payments' )
            )
        );
        $wpdb->query(
            sprintf(
                'UPDATE `%s` SET `paid_type` = (CASE WHEN `paid` = `total` THEN "in_full" ELSE "deposit" END)',
                $this->getTableName( 'ab_payments' )
            )
        );

        // Set price format.
        $currencies = Utils\Price::getCurrencies();
        $format = $currencies[ get_option( 'bookly_pmt_currency' ) ]['format'];
        add_option( 'bookly_pmt_price_format', $format );

        // Time zone.
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD `time_zone` VARCHAR(255) AFTER `token`',
                $this->getTableName( 'ab_customer_appointments' )
            )
        );
    }

    function update_13_4()
    {
        global $wpdb;

        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s`
                ADD `created_from` ENUM("frontend","backend") NOT NULL DEFAULT "frontend" AFTER `compound_token`,
                ADD `created` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" AFTER `created_from`',
                $this->getTableName( 'ab_customer_appointments' )
            )
        );

        $wpdb->query( sprintf( 'ALTER TABLE `%s` CHANGE `capacity` `capacity_max` INT NOT NULL DEFAULT 1', $this->getTableName( 'ab_services' ) ) );
        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD `capacity_min` INT NOT NULL DEFAULT 1 AFTER `color`', $this->getTableName( 'ab_services' ) ) );
        $wpdb->query( sprintf( 'ALTER TABLE `%s` CHANGE `capacity` `capacity_max` INT NOT NULL DEFAULT 1', $this->getTableName( 'ab_staff_services' ) ) );
        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD `capacity_min` INT NOT NULL DEFAULT 1 AFTER `deposit`', $this->getTableName( 'ab_staff_services' ) ) );

        add_option( 'bookly_app_service_name_with_duration', '0' );

        $items = $wpdb->get_results(
            sprintf(
                'SELECT `subject`, `message`, `gateway` FROM `%s` WHERE `type` = "client_reminder"',
                $this->getTableName( 'ab_notifications' )
            )
        );

        foreach ( $items as $item ) {
            $types = array( 'client_reminder_1st', 'client_reminder_2nd', 'client_reminder_3rd' );
            foreach ( $types as $type ) {
                $wpdb->insert( $this->getTableName( 'ab_notifications' ), array(
                    'gateway' => $item->gateway,
                    'type' => $type,
                    'subject' => $item->subject,
                    'message' => $item->message,
                    'active' => 0,
                ) );
            }
        }

        $times = get_option( 'bookly_cron_reminder_times' );
        $times['client_reminder_1st'] = 1;
        $times['client_reminder_2nd'] = 2;
        $times['client_reminder_3rd'] = 3;
        update_option( 'bookly_cron_reminder_times', $times );

        $bookly_cal_one_participant = '{service_name}' . "\n" . '{client_name}' . "\n" . '{client_phone}' . "\n" . '{client_email}' . "\n" . '{extras}' . '{location_name}' . '{custom_fields}' . "\n" . '{total_price} {payment_type} {payment_status}' . "\n" . __( 'Status', 'bookly' ) . ': {status}' . "\n" . __( 'Signed up', 'bookly' ) . ': {signed_up}' . "\n" . __( 'Capacity', 'bookly' ) . ': {service_capacity}';
        $bookly_cal_one_participant = str_replace( '{extras}', Config::serviceExtrasActive() ? '{extras}' . "\n" : '', $bookly_cal_one_participant );
        $bookly_cal_one_participant = str_replace( '{location_name}', Config::locationsActive() ? __( 'Location', 'bookly' ) . ': {location_name}' . "\n" : '', $bookly_cal_one_participant );

        add_option( 'bookly_cal_one_participant', $bookly_cal_one_participant );
        add_option( 'bookly_cal_many_participants', '{service_name}' . "\n" . __( 'Signed up', 'bookly' ) . ': {signed_up}' . "\n" . __( 'Capacity', 'bookly' ) . ': {service_capacity}' );
        $options = array(
            'bookly_l10n_step_time_slot_not_available' => __( 'The selected time is not available anymore. Please, choose another time slot.', 'bookly' ),
            'bookly_l10n_step_cart_slot_not_available' => __( 'The highlighted time is not available anymore. Please, choose another time slot.', 'bookly' ),
        );
        $this->addL10nOptions( $options );

        // Drop stats tables.
        $this->drop( array( $this->getTableName( 'ab_stats_forms' ), $this->getTableName( 'ab_stats_steps' ) ) );

        add_option( 'bookly_admin_preferred_language', '' );
    }

    function update_13_3()
    {
        add_option( 'bookly_app_custom_styles', '' );
        add_option( 'bookly_cst_required_phone', '1' );

        // Rename and add new appearance options.
        $info_coupon = get_option( 'bookly_l10n_info_coupon' );
        $info_payment_step = get_option( 'bookly_l10n_info_payment_step' );
        $this->renameL10nStrings( array(
            'bookly_l10n_info_coupon' => 'bookly_l10n_info_coupon_single_app',
            'bookly_l10n_info_payment_step' => 'bookly_l10n_info_payment_step_single_app',
        ) );
        $this->addL10nOptions( array(
            'bookly_l10n_info_coupon_several_apps' => $info_coupon,
            'bookly_l10n_info_payment_step_several_apps' => $info_payment_step,
        ) );
    }

    function update_13_2()
    {
        $next = get_option( 'bookly_l10n_button_next' );
        $this->renameOptions( array( 'bookly_l10n_button_next' => 'bookly_l10n_step_service_button_next' ) );
        $options = array(
            'bookly_l10n_step_service_mobile_button_next' => $next,
            'bookly_l10n_step_cart_button_next' => $next,
            'bookly_l10n_step_details_button_next' => $next,
            'bookly_l10n_step_payment_button_next' => $next,
        );
        $this->addL10nOptions( $options );
    }

    function update_13_1()
    {
        global $wpdb;

        // Statistics.
        add_option( 'bookly_gen_collect_stats', '1' );

        // Birthday greetings.
        $times = get_option( 'bookly_cron_reminder_times' );
        $times['client_birthday_greeting'] = 9;
        update_option( 'bookly_cron_reminder_times', $times );

        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD COLUMN `birthday` DATE AFTER `notes`', $this->getTableName( 'ab_customers' ) ) );
        $notifications = array(
            array(
                'gateway' => 'email',
                'type' => 'client_birthday_greeting',
                'subject' => __( 'Happy Birthday!', 'bookly' ),
                'message' => wpautop( __( "Dear {client_name},\n\nHappy birthday!\nWe wish you all the best.\nMay you and your family be happy and healthy.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ) ),
                'active' => 0,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'client_birthday_greeting',
                'subject' => '',
                'message' => __( "Dear {client_name},\nHappy birthday!\nWe wish you all the best.\nMay you and your family be happy and healthy.\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ),
                'active' => 0,
            ),
        );
        foreach ( $notifications as $data ) {
            $wpdb->insert( $this->getTableName( 'ab_notifications' ), $data );
        }

        $sn_table = $this->getTableName( 'ab_sent_notifications' );
        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD COLUMN `ref_id` INT UNSIGNED AFTER `id`, ADD INDEX `ref_id_idx` (`ref_id`)', $sn_table ) );
        $wpdb->query( sprintf( 'UPDATE `%s` SET `ref_id` = COALESCE(`customer_appointment_id`, `staff_id`)', $sn_table ) );
        $this->dropTableColumns( $sn_table, array( 'customer_appointment_id', 'staff_id' ) );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `status` `status` ENUM("pending","approved","cancelled","rejected") NOT NULL DEFAULT "approved" AFTER `custom_fields`',
                $this->getTableName( 'ab_customer_appointments' )
            )
        );
        $this->dropTableColumns( $this->getTableName( 'ab_services' ), array( 'start_time', 'end_time' ) );
    }

    function update_13_0()
    {
        global $wpdb;

        $dismiss_subscribe_notice = ! get_option( 'bookly_gen_show_subscribe_notice' );
        foreach ( get_users( array( 'role' => 'administrator' ) ) as $admin ) {
            delete_user_meta( $admin->ID, 'bookly_dismiss_admin_notice' );
            if ( $dismiss_subscribe_notice ) {
                update_user_meta( $admin->ID, 'bookly_dismiss_subscribe_notice', 1 );
            }
        }
        delete_option( 'bookly_gen_show_subscribe_notice' );

        add_option( 'bookly_api_server_error_time', '0' );
        add_option( 'bookly_grace_notifications', array( 'bookly' => '0', 'add-ons' => '0', 'sent' => '0' ) );
        add_option( 'bookly_grace_hide_admin_notice_time', '0' );
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin_class ) {
            add_option( $plugin_class::getPrefix() . 'grace_start', time() + 2 * WEEK_IN_SECONDS );
        }

        $options = array(
            'bookly_email_content_type' => 'bookly_email_send_as',
            'bookly_pmt_authorizenet' => 'bookly_pmt_authorize_net',
            'bookly_pmt_authorizenet_api_login_id' => 'bookly_pmt_authorize_net_api_login_id',
            'bookly_pmt_authorizenet_transaction_key' => 'bookly_pmt_authorize_net_transaction_key',
            'bookly_pmt_authorizenet_sandbox' => 'bookly_pmt_authorize_net_sandbox',
            'bookly_pmt_pay_locally' => 'bookly_pmt_local',
        );
        $this->renameOptions( $options );

        if ( get_option( 'bookly_email_content_type' ) == 'plain' ) {
            update_option( 'bookly_email_content_type', 'text' );
        }

        // Authorize.Net => authorize_net.
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `type` `type` ENUM("local","coupon","paypal","authorizeNet","authorize_net","stripe","2checkout","payu_latam","payson","mollie","woocommerce") NOT NULL DEFAULT "local"',
                $this->getTableName( 'ab_payments' )
            )
        );
        $wpdb->query(
            sprintf(
                'UPDATE `%s` SET `type` = "authorize_net" WHERE `type` = "authorizeNet"',
                $this->getTableName( 'ab_payments' )
            )
        );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `type` `type` ENUM("local","free","paypal","authorize_net","stripe","2checkout","payu_latam","payson","mollie","woocommerce") NOT NULL DEFAULT "local"',
                $this->getTableName( 'ab_payments' )
            )
        );
        $this->dropTableColumns( $this->getTableName( 'ab_payments' ), array( 'transaction_id', 'token' ) );

        $notifications = array(
            array(
                'gateway' => 'email',
                'type' => 'client_rejected_appointment',
                'subject' => __( 'Booking rejection', 'bookly' ),
                'message' => wpautop( __( "Dear {client_name}.\n\nYour booking of {service_name} on {appointment_date} at {appointment_time} has been rejected.\n\nReason: {cancellation_reason}\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ) ),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'staff_rejected_appointment',
                'subject' => __( 'Booking rejection', 'bookly' ),
                'message' => wpautop( __( "Hello.\n\nThe following booking has been rejected.\n\nReason: {cancellation_reason}\n\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookly' ) ),
                'active' => 1,
            ),

            array(
                'gateway' => 'sms',
                'type' => 'client_rejected_appointment',
                'subject' => '',
                'message' => __( "Dear {client_name}.\nYour booking of {service_name} on {appointment_date} at {appointment_time} has been rejected.\nReason: {cancellation_reason}\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'staff_rejected_appointment',
                'subject' => '',
                'message' => __( "Hello.\nThe following booking has been rejected.\nReason: {cancellation_reason}\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookly' ),
                'active' => 1,
            ),
        );
        foreach ( $notifications as $data ) {
            $wpdb->insert( $this->getTableName( 'ab_notifications' ), $data );
        }

        $this->dropTableColumns( $this->getTableName( 'ab_customer_appointments' ), array( 'series' ) );
        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . $this->getTableName( 'ab_series' ) . '` (
                `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `repeat` VARCHAR(255),
                `token`  VARCHAR(255) NOT NULL
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );
        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD COLUMN `series_id` INT UNSIGNED AFTER `id`', $this->getTableName( 'ab_appointments' ) ) );
        $wpdb->query(
            sprintf(
                'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (series_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
                $this->getTableName( 'ab_appointments' ),
                $this->getTableName( 'ab_series' )
            )
        );
    }

    function update_12_1()
    {
        global $wpdb;

        $options = array(
            'bookly_l10n_required_email' => __( 'Please tell us your email', 'bookly' ),
            'bookly_l10n_required_employee' => __( 'Please select an employee', 'bookly' ),
            'bookly_l10n_required_name' => __( 'Please tell us your name', 'bookly' ),
            'bookly_l10n_required_phone' => __( 'Please tell us your phone', 'bookly' ),
            'bookly_l10n_required_service' => __( 'Please select a service', 'bookly' ),
        );
        foreach ( $options as $option_name => $option_value ) {
            if ( get_option( $option_name ) == '' ) {
                $this->addL10nOptions( array( array( $option_name => $option_value ) ) );
            }
        }
        $wpdb->query( sprintf( 'ALTER TABLE `%s` ADD COLUMN `series` VARCHAR(255) NULL DEFAULT NULL', $this->getTableName( 'ab_customer_appointments' ) ) );
    }

    function update_12_0()
    {
        global $wpdb;

        $wpdb->query( sprintf( 'ALTER TABLE `%s` CHANGE COLUMN `google_data` `google_data` TEXT', $this->getTableName( 'ab_staff' ) ) );
    }

    /**
     * @since 16.0
     * Removed outdated updates for 11.7 and earlier versions.
     */
}