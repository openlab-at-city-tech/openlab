<?php
namespace Bookly\Lib;

use Bookly\Backend;
use Bookly\Frontend;

/**
 * Class Plugin
 *
 * @package Bookly\Lib
 */
abstract class Plugin extends Base\Plugin
{
    protected static $prefix = 'bookly_';
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    /**
     * @inheritDoc
     */
    public static function init()
    {
        Backend\Modules\Settings\Ajax::init();

        // Init ajax.
        Backend\Components\Cloud\Account\Ajax::init();
        Backend\Components\Cloud\Recharge\Ajax::init();
        Backend\Components\Dashboard\Appointments\Ajax::init();
        Backend\Components\Dashboard\Appointments\Widget::init();
        Backend\Components\Dialogs\Appointment\Delete\Ajax::init();
        Backend\Components\Dialogs\Appointment\Edit\Ajax::init();
        Backend\Components\Dialogs\Customer\Delete\Ajax::init();
        Backend\Components\Dialogs\Customer\Edit\Ajax::init();
        Backend\Components\Dialogs\Mailing\AddRecipients\Ajax::init();
        Backend\Components\Dialogs\Mailing\Campaign\Ajax::init();
        Backend\Components\Dialogs\Mailing\CreateList\Ajax::init();
        Backend\Components\Dialogs\Payment\Ajax::init();
        Backend\Components\Dialogs\Service\Edit\Ajax::init();
        Backend\Components\Dialogs\Service\Order\Ajax::init();
        Backend\Components\Dialogs\Sms\Ajax::init();
        Backend\Components\Dialogs\Staff\Edit\Ajax::init();
        Backend\Components\Dialogs\Staff\Order\Ajax::init();
        Backend\Components\Dialogs\TableSettings\Ajax::init();
        Backend\Components\Dialogs\VoiceTest\Ajax::init();
        Backend\Components\Dialogs\Whatsapp\Ajax::init();
        Backend\Components\Editable\Proxy\Shared::init();
        Backend\Components\Gutenberg\BooklyForm\Block::init();
        Backend\Components\Notices\Limitation\Ajax::init();
        Backend\Components\Notices\Lite\Ajax::init();
        Backend\Components\Notices\Nps\Ajax::init();
        Backend\Components\Notices\PoweredBy\Ajax::init();
        Backend\Components\Notices\Promotion\Ajax::init();
        Backend\Components\Notices\Rate\Ajax::init();
        Backend\Components\Notices\RenewAutoRecharge\Ajax::init();
        Backend\Components\Notices\Statistic\Ajax::init();
        Backend\Components\Notices\Subscribe\Ajax::init();
        Backend\Components\Notices\Wpml\Ajax::init();
        Backend\Components\Notices\ZoomJwt\Ajax::init();
        Backend\Components\Support\ButtonsAjax::init();
        Backend\Components\TinyMce\Tools::init();
        Backend\Modules\Appearance\Ajax::init();
        Backend\Modules\Appointments\Ajax::init();
        Backend\Modules\Calendar\Ajax::init();
        Backend\Modules\CloudBilling\Ajax::init();
        Backend\Modules\CloudProducts\Ajax::init();
        Backend\Modules\CloudSettings\Ajax::init();
        Backend\Modules\CloudSms\Ajax::init();
        Backend\Modules\CloudVoice\Ajax::init();
        Backend\Modules\CloudWhatsapp\Ajax::init();
        Backend\Modules\CloudZapier\Ajax::init();
        Backend\Modules\Customers\Ajax::init();
        Backend\Modules\Diagnostics\Ajax::init();
        Backend\Modules\News\Ajax::init();
        Backend\Modules\Notifications\Ajax::init();
        Backend\Modules\Payments\Ajax::init();
        Backend\Modules\Services\Ajax::init();
        Backend\Modules\Setup\Ajax::init();
        Backend\Modules\Shop\Ajax::init();
        Backend\Modules\Staff\Ajax::init();
        Frontend\Modules\Booking\Ajax::init();
        Frontend\Modules\Booking\Proxy\Invoices::init();
        Frontend\Modules\Cron\Ajax::init();
        Frontend\Modules\Stripe\Ajax::init();
        Frontend\Modules\Zapier\Ajax::init();

        add_action( 'elementor/widgets/widgets_registered', function ( $widgets_manager ) {
            Backend\Components\Elementor\Widgets\BooklyForm\Widget::register( $widgets_manager );
        } );

        if ( ! is_admin() ) {
            // Init short code.
            Frontend\Modules\Booking\ShortCode::init();
        }
    }

    /**
     * @inheritDoc
     */
    public static function run()
    {
        // l10n.
        load_plugin_textdomain( 'bookly', false, self::getSlug() . '/languages' );

        parent::run();
    }

    /**
     * @inheritDoc
     */
    public static function registerHooks()
    {
        global $wp_version;

        parent::registerHooks();

        if ( is_admin() ) {
            Backend\Backend::registerHooks();
        } else {
            Frontend\Frontend::registerHooks();
        }

        if ( get_option( 'bookly_gen_collect_stats' ) ) {
            // Store admin preferred language.
            add_filter( 'wp_authenticate_user', function ( $user ) {
                if ( $user instanceof \WP_User && $user->has_cap( Utils\Common::getRequiredCapability() ) && isset ( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                    $locale = strtok( $_SERVER['HTTP_ACCEPT_LANGUAGE'], ',;' );
                    update_option( 'bookly_admin_preferred_language', $locale );
                }

                return $user;
            }, 99, 1 );
        }

        // Gutenberg category
        add_filter( version_compare( $wp_version, '5.8', '>=' ) ? 'block_categories_all' : 'block_categories', function ( $block_categories, $block_editor_context ) {
            return array_merge( array(
                array(
                    'slug' => 'bookly-blocks',
                    'title' => 'Bookly',
                ),
            ),
                $block_categories
            );
        }, 10, 2 );

        PluginsUpdater::init();

        // Register and schedule routines.
        Routines::init();
    }
}