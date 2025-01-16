<?php
namespace Bookly\Backend\Modules\CloudWhatsapp;

use Bookly\Lib;
use Bookly\Backend\Components;

class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $cloud = Lib\Cloud\API::getInstance();
        if ( ! $cloud->account->loadProfile() ) {
            Components\Cloud\LoginRequired\Page::render( __( 'WhatsApp Notifications', 'bookly' ), self::pageSlug() );
        } elseif ( $cloud->account->productActive( Lib\Cloud\Account::PRODUCT_WHATSAPP ) ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/intlTelInput.css' => array( 'bookly-backend-globals', ) ),
            ) );

            self::enqueueScripts( array(
                'frontend' => get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'js/intlTelInput.min.js' => array( 'jquery' ) ),
                'bookly' => array( 'backend/modules/cloud_sms/resources/js/notifications-list.js' => array( 'bookly-backend-globals', 'bookly-notification-dialog.js' ), ),
                'module' => array(
                    'js/whatsapp.js' => array( 'bookly-notifications-list.js', ),
                ),
            ) );

            // Prepare tables settings.
            $datatables = Lib\Utils\Tables::getSettings( array(
                Lib\Utils\Tables::WHATSAPP_NOTIFICATIONS,
                Lib\Utils\Tables::WHATSAPP_DETAILS,
            ) );

            wp_localize_script( 'bookly-whatsapp.js', 'BooklyL10n',
                array(
                    'moment_format_date_time' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_MOMENT_JS ) . ' ' . Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
                    'areYouSure' => __( 'Are you sure?', 'bookly' ),
                    'country' => $cloud->account->getCountry(),
                    'intlTelInput' => array(
                        'country' => get_option( 'bookly_cst_phone_default_country' ),
                        'enabled' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled',
                    ),
                    'lastMonth' => __( 'Last month', 'bookly' ),
                    'zeroRecords' => __( 'No records for selected period.', 'bookly' ),
                    'noResults' => __( 'No records.', 'bookly' ),
                    'processing' => __( 'Processing...', 'bookly' ),
                    'state' => array( __( 'Disabled', 'bookly' ), __( 'Enabled', 'bookly' ) ),
                    'action' => array( __( 'enable', 'bookly' ), __( 'disable', 'bookly' ) ),
                    'edit' => __( 'Edit', 'bookly' ),
                    'settingsSaved' => __( 'Settings saved.', 'bookly' ),
                    'gateway' => 'whatsapp',
                    'datatables' => $datatables,
                    'status' => array(
                        'sent' => __( 'Sent', 'bookly' ),
                        'failed' => __( 'Failed', 'bookly' ),
                    ),
                )
            );
            $whatsapp = \Bookly\Lib\Cloud\API::getInstance()->getProduct( Lib\Cloud\Account::PRODUCT_WHATSAPP );
            self::renderTemplate( 'index', compact( 'datatables', 'whatsapp' ) );
        } else {
            Lib\Utils\Common::redirect( add_query_arg(
                    array( 'page' => \Bookly\Backend\Modules\CloudProducts\Page::pageSlug() ),
                    admin_url( 'admin.php' ) )
            );
        }
    }

    /**
     * Show 'WhatsApp' submenu with counter inside Bookly Cloud main menu.
     *
     * @param array $product
     */
    public static function addBooklyCloudMenuItem( $product )
    {
        $title = $product['texts']['title'];

        add_submenu_page(
            'bookly-cloud-menu',
            $title,
            $title,
            Lib\Utils\Common::getRequiredCapability(),
            self::pageSlug(),
            function() {
                \Bookly\Backend\Modules\CloudWhatsapp\Page::render();
            }
        );
    }
}