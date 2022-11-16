<?php
namespace Bookly\Backend\Modules\CloudSms;

use Bookly\Lib;
use Bookly\Backend\Modules\CloudProducts\Page as CloudProducts;
use Bookly\Backend\Components;

/**
 * Class Controller
 * @package Bookly\Backend\Modules\CloudSms
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $cloud = Lib\Cloud\API::getInstance();
        if ( ! $cloud->account->loadProfile() ) {
            Components\Cloud\LoginRequired\Page::render( __( 'SMS Notifications', 'bookly' ), self::pageSlug() );
        } else {
            self::enqueueStyles( array(
                'frontend' => array( 'css/intlTelInput.css' => array( 'bookly-backend-globals', ) ),
            ) );

            self::enqueueScripts( array(
                'frontend' => get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'js/intlTelInput.min.js' => array( 'jquery' ) ),
                'module' => array(
                    'js/notifications-list.js' => array( 'bookly-backend-globals', 'bookly-notification-dialog.js', ),
                    'js/sms.js' => array( 'bookly-notifications-list.js', ),
                ),
            ) );

            // Prepare tables settings.
            $datatables = Lib\Utils\Tables::getSettings( array(
                Lib\Utils\Tables::SMS_NOTIFICATIONS,
                Lib\Utils\Tables::SMS_DETAILS,
                Lib\Utils\Tables::SMS_PRICES,
                Lib\Utils\Tables::SMS_SENDER,
                Lib\Utils\Tables::SMS_MAILING_LISTS,
                Lib\Utils\Tables::SMS_MAILING_RECIPIENTS_LIST,
                Lib\Utils\Tables::SMS_MAILING_CAMPAIGNS
            ) );

            $current_tab = self::hasParameter( 'tab' ) ? self::parameter( 'tab' ) : 'notifications';

            // Number of undelivered sms.
            $undelivered_count = Lib\Cloud\SMS::getUndeliveredSmsCount();

            wp_localize_script( 'bookly-sms.js', 'BooklyL10n',
                array(
                    'moment_format_date_time' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_MOMENT_JS ) . ' ' . Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
                    'areYouSure' => __( 'Are you sure?', 'bookly' ),
                    'country' => $cloud->account->getCountry(),
                    'current_tab' => $current_tab,
                    'intlTelInput' => array(
                        'country' => get_option( 'bookly_cst_phone_default_country' ),
                        'utils' => plugins_url( 'intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/resources/js/intlTelInput.utils.js' ),
                        'enabled' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled',
                    ),
                    'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
                    'dateRange' => Lib\Utils\DateTime::dateRangeOptions( array( 'lastMonth' => __( 'Last month', 'bookly' ), ) ),
                    'sender_id' => array(
                        'sent' => __( 'Sender ID request is sent.', 'bookly' ),
                        'set_default' => __( 'Sender ID is reset to default.', 'bookly' ),
                    ),
                    'zeroRecords' => __( 'No records for selected period.', 'bookly' ),
                    'noResults' => __( 'No records.', 'bookly' ),
                    'processing' => __( 'Processing...', 'bookly' ),
                    'state' => array( __( 'Disabled', 'bookly' ), __( 'Enabled', 'bookly' ) ),
                    'action' => array( __( 'enable', 'bookly' ), __( 'disable', 'bookly' ) ),
                    'edit' => __( 'Edit', 'bookly' ),
                    'settingsSaved' => __( 'Settings saved.', 'bookly' ),
                    'na' => __( 'N/A', 'bookly' ),
                    'campaign' => array(
                        'pending' => __( 'Pending', 'bookly' ),
                        'in_progress' => __( 'In progress', 'bookly' ),
                        'completed' => __( 'Completed', 'bookly' ),
                        'canceled' => __( 'Canceled', 'bookly' ),
                    ),
                    'gateway' => 'sms',
                    'default' => __( 'Default', 'bookly' ),
                    'datatables' => $datatables,
                )
            );

            self::renderTemplate( 'index', compact( 'cloud', 'datatables', 'undelivered_count' ) );
        }
    }

    /**
     * Show 'SMS Notifications' submenu with counter inside Bookly main menu.
     */
    public static function addBooklyMenuItem()
    {
        $sms = __( 'SMS Notifications', 'bookly' );

        $cloud = Lib\Cloud\API::getInstance();

        $promotion = $cloud->general->getPromotionForNotice();
        if ( $promotion ) {
            $title = sprintf( '%s <span class="update-plugins"><span class="update-count">$</span></span>', $sms );
        } else {
            $count = get_option( 'bookly_cloud_badge_consider_sms' ) ? Lib\Cloud\SMS::getUndeliveredSmsCount() : 0;
            $title = $count ? sprintf( '%s <span class="update-plugins"><span class="update-count">%d</span></span>', $sms, $count ) : $sms;
        }

        $page = $cloud->getToken() && $cloud->account->productActive( 'sms' ) ? self::pageSlug() : CloudProducts::pageSlug();

        add_submenu_page(
            'bookly-menu',
            $sms,
            '<span id="bookly-js-sms-menu-redirect">' . $title . '</span><script>document.getElementById("bookly-js-sms-menu-redirect").parentNode.href+="=' . $page . '";</script>',
            Lib\Utils\Common::getRequiredCapability(),
            '',
            function () { Page::render(); }
        );
    }

    /**
     * Show 'SMS Notifications' submenu with counter inside Bookly Cloud main menu.
     *
     * @param array $product
     */
    public static function addBooklyCloudMenuItem( $product )
    {
        $sms = $product['texts']['title'];

        $count = get_option( 'bookly_cloud_badge_consider_sms' ) ? Lib\Cloud\SMS::getUndeliveredSmsCount() : 0;
        $title = $count ? sprintf( '%s <span class="update-plugins"><span class="update-count">%d</span></span>', $sms, $count ) : $sms;

        add_submenu_page(
            'bookly-cloud-menu',
            $sms,
            $title,
            Lib\Utils\Common::getRequiredCapability(),
            self::pageSlug(),
            function () { Page::render(); }
        );
    }
}