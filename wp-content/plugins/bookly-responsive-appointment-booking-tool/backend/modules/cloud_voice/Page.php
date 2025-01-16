<?php
namespace Bookly\Backend\Modules\CloudVoice;

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
            Components\Cloud\LoginRequired\Page::render( __( 'Voice Notifications', 'bookly' ), self::pageSlug() );
        } elseif ( $cloud->account->productActive( Lib\Cloud\Account::PRODUCT_VOICE ) ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/intlTelInput.css' => array( 'bookly-backend-globals', ) ),
            ) );

            self::enqueueScripts( array(
                'frontend' => get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'js/intlTelInput.min.js' => array( 'jquery' ) ),
                'bookly' => array( 'backend/modules/cloud_sms/resources/js/notifications-list.js' => array( 'bookly-backend-globals', 'bookly-notification-dialog.js' ), ),
                'module' => array(
                    'js/calls.js' => array( 'bookly-notifications-list.js', ),
                ),
            ) );

            // Prepare tables settings.
            $datatables = Lib\Utils\Tables::getSettings( array(
                Lib\Utils\Tables::VOICE_NOTIFICATIONS,
                Lib\Utils\Tables::VOICE_DETAILS,
                Lib\Utils\Tables::VOICE_PRICES,
            ) );

            wp_localize_script( 'bookly-calls.js', 'BooklyL10n',
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
                    'gateway' => 'voice',
                    'datatables' => $datatables,
                    'status' => array(
                        'out-of-credit' => __( 'Out of credit', 'bookly' ),
                        'completed' => __( 'Completed', 'bookly' ),
                        'busy' => __( 'Busy', 'bookly' ),
                        'failed' => __( 'Failed', 'bookly' ),
                        'no-answer' => __( 'No answer', 'bookly' ),
                        'pending' => __( 'Pending', 'bookly' ),
                    ),
                )
            );
            $voice = Lib\Cloud\API::getInstance()->getProduct( Lib\Cloud\Account::PRODUCT_VOICE );
            self::renderTemplate( 'index', compact( 'datatables', 'voice' ) );
        } else {
            Lib\Utils\Common::redirect( add_query_arg(
                    array( 'page' => \Bookly\Backend\Modules\CloudProducts\Page::pageSlug() ),
                    admin_url( 'admin.php' ) )
            );
        }
    }

    /**
     * Show 'Voice' submenu with counter inside Bookly Cloud main menu.
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
                \Bookly\Backend\Modules\CloudVoice\Page::render();
            }
        );
    }

    /**
     * Get languages like in WP
     *
     * @return string[][]
     */
    public static function getLanguages()
    {
        $languages = array(
            array( 'da-DK', 'Danish, Denmark' ),
            array( 'de-DE', 'German, Germany' ),
            array( 'en-AU', 'English, Australia' ),
            array( 'en-CA', 'English, Canada' ),
            array( 'en-GB', 'English, UK' ),
            array( 'en-IN', 'English, India' ),
            array( 'en-US', 'English, United States' ),
            array( 'ca-ES', 'Catalan, Spain' ),
            array( 'es-ES', 'Spanish, Spain' ),
            array( 'es-MX', 'Spanish, Mexico' ),
            array( 'fi-FI', 'Finnish, Finland' ),
            array( 'fr-CA', 'French, Canada' ),
            array( 'fr-FR', 'French, France' ),
            array( 'it-IT', 'Italian, Italy' ),
            array( 'ja-JP', 'Japanese, Japan' ),
            array( 'ko-KR', 'Korean, Korea' ),
            array( 'nb-NO', 'Norwegian, Norway' ),
            array( 'nl-NL', 'Dutch, Netherlands' ),
            array( 'pl-PL', 'Polish-Poland' ),
            array( 'pt-BR', 'Portuguese, Brazil' ),
            array( 'pt-PT', 'Portuguese, Portugal' ),
            array( 'ru-RU', 'Russian, Russia' ),
            array( 'sv-SE', 'Swedish, Sweden' ),
            array( 'zh-CN', 'Chinese (Mandarin)' ),
            array( 'zh-HK', 'Chinese (Cantonese)' ),
            array( 'zh-TW', 'Chinese (Taiwanese Mandarin)' ),
        );
        $translations = get_site_transient( 'available_translations' );
        foreach ( $languages as &$data ) {
            $key = str_replace( '-', '_', $data[0] );
            if ( isset( $translations[ $key ]['native_name'] ) ) {
                $data[1] = $translations[ $key ]['native_name'];
            }
        }

        return $languages;
    }
}