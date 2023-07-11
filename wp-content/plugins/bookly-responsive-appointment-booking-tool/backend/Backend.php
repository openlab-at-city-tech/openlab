<?php
namespace Bookly\Backend;

use Bookly\Lib;

/**
 * Class Backend
 *
 * @package Bookly\Backend
 */
abstract class Backend
{
    /**
     * Register hooks.
     */
    public static function registerHooks()
    {
        $bookly_page = isset ( $_REQUEST['page'] ) && strncmp( $_REQUEST['page'], 'bookly-', 7 ) === 0;

        add_action( 'admin_menu', array( __CLASS__, 'addAdminMenu' ) );

        add_action( 'all_admin_notices', function () use ( $bookly_page ) {
            if ( ! Lib\Config::setupMode() ) {
                if ( $bookly_page ) {
                    // Subscribe notice.
                    Components\Notices\Subscribe\Notice::render();
                    // Lite rebranding notice.
                    Components\Notices\Lite\Notice::render();
                    // NPS notice.
                    Components\Notices\Nps\Notice::render();
                    // Collect stats notice.
                    Components\Notices\Statistic\Notice::render();
                    // Show Powered by Bookly notice.
                    Components\Notices\PoweredBy\Notice::render();
                    // Show SMS promotion notice.
                    Components\Notices\Promotion\Notice::render();
                    // Show renew auto-recharge notice.
                    Components\Notices\RenewAutoRecharge\Notice::create( 'bookly-js-renew' )->render();
                    // Show WPML re save notice.
                    Components\Notices\Wpml\Notice::render();
                    // Show Zoom JWT deprecation notice.
                    Components\Notices\ZoomJwt\Notice::render();
                }
                // Let add-ons render admin notices.
                Lib\Proxy\Shared::renderAdminNotices( $bookly_page );
            }
        }, 10, 0 );

        // for Site Health
        // Close current session, for fixing loopback request
        add_filter( 'site_status_tests', function ( $tests ) {
            session_write_close();

            return $tests;
        }, 10, 1 );

        // Disable emoji in IE11
        if ( $bookly_page && array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0' ) !== false ) {
            Lib\Utils\Common::disableEmoji();
        }

        // Elementor hooks
        add_action( 'elementor/elements/categories_registered', function ( $elements_manager ) {
            /** @var \Elementor\Elements_Manager $elements_manager */
            $elements_manager->add_category( 'bookly', array( 'title' => 'Bookly' ) );
        } );
        add_action( 'elementor/editor/before_enqueue_scripts', function () {
            wp_register_style(
                'bookly-elementor',
                plugins_url( '/backend/components/elementor/resources/css/elementor.css', __DIR__ ),
                array(),
                Lib\Plugin::getVersion()
            );
        } );
    }

    /**
     * Admin menu.
     */
    public static function addAdminMenu()
    {
        /** @var \WP_User $current_user */
        global $current_user, $submenu;

        $is_staff = Lib\Entities\Staff::query()->where( 'wp_user_id', $current_user->ID )->count() > 0;
        $required_capability = Lib\Utils\Common::getRequiredCapability();
        if ( $current_user->has_cap( $required_capability ) || $current_user->has_cap( 'manage_bookly_appointments' ) || $is_staff ) {
            $dynamic_position = '80.0000001' . mt_rand( 1, 1000 ); // position always is under `Settings`
            $badge_number = 0;
            $calendar_badge = 0;
            if ( Lib\Utils\Common::isCurrentUserSupervisor() ) {
                $badge_number = Modules\Shop\Page::getNotSeenCount();
                if ( get_option( 'bookly_gen_badge_consider_news' ) ) {
                    $badge_number += Modules\News\Page::getNewsCount();
                }
                if ( get_option( 'bookly_cloud_badge_consider_sms' ) ) {
                    $badge_number += Lib\Cloud\SMS::getUndeliveredSmsCount();
                }
                if ( get_option( 'bookly_cal_show_new_appointments_badge' ) ) {
                    $calendar_badge = Modules\Calendar\Page::getAppointmentsCount();
                    $badge_number += $calendar_badge;
                }
            }
            if ( $badge_number ) {
                add_menu_page( 'Bookly', sprintf( 'Bookly <span class="update-plugins count-%d"><span class="update-count">%d</span></span>', $badge_number, $badge_number ), 'read', 'bookly-menu', '',
                    plugins_url( 'resources/images/menu.png', __FILE__ ), $dynamic_position );
            } else {
                add_menu_page( 'Bookly', 'Bookly', 'read', 'bookly-menu', '',
                    plugins_url( 'resources/images/menu.png', __FILE__ ), $dynamic_position );
            }
            if ( Lib\Config::setupMode() ) {
                $setup = __( 'Initial setup', 'bookly' );
                add_submenu_page( 'bookly-menu', $setup, $setup, $required_capability, Modules\Setup\Page::pageSlug(), function () { Modules\Setup\Page::render(); } );
            } elseif ( Lib\Proxy\Pro::graceExpired() ) {
                Lib\Proxy\Pro::addLicenseBooklyMenuItem();
                if ( isset ( $_GET['page'] ) && $_GET['page'] == 'bookly-diagnostics' ) {
                    Modules\Diagnostics\Page::addBooklyMenuItem();
                }
            } else {
                // Translated submenu pages.
                $dashboard = __( 'Dashboard', 'bookly' );
                $appointments = __( 'Appointments', 'bookly' );
                $staff_members = __( 'Staff Members', 'bookly' );
                $services = __( 'Services', 'bookly' );
                $notifications = __( 'Email Notifications', 'bookly' );
                $customers = __( 'Customers', 'bookly' );
                $payments = __( 'Payments', 'bookly' );
                $appearance = __( 'Appearance', 'bookly' );
                $settings = __( 'Settings', 'bookly' );
                $products = __( 'Products', 'bookly' );
                $billing = __( 'Billing', 'bookly' );

                add_submenu_page( 'bookly-menu', $dashboard, $dashboard, $required_capability,
                    Modules\Dashboard\Page::pageSlug(), function () { Modules\Dashboard\Page::render(); } );
                Modules\Calendar\Page::addBooklyMenuItem( $calendar_badge );
                if ( $current_user->has_cap( $required_capability ) || $current_user->has_cap( 'manage_bookly_appointments' ) ) {
                    add_submenu_page( 'bookly-menu', $appointments, $appointments, 'read',
                        Modules\Appointments\Page::pageSlug(), function () { Modules\Appointments\Page::render(); } );
                }
                Lib\Proxy\Locations::addBooklyMenuItem();
                if ( $current_user->has_cap( $required_capability ) || $current_user->has_cap( 'manage_bookly_appointments' ) ) {
                    Lib\Proxy\Packages::addBooklyMenuItem();
                }
                if ( $current_user->has_cap( $required_capability ) ) {
                    add_submenu_page( 'bookly-menu', $staff_members, $staff_members, $required_capability,
                        Modules\Staff\Page::pageSlug(), function () { Modules\Staff\Page::render(); } );
                } elseif ( $is_staff ) {
                    if ( get_option( 'bookly_gen_allow_staff_edit_profile' ) == 1 ) {
                        add_submenu_page( 'bookly-menu', __( 'Profile', 'bookly' ), __( 'Profile', 'bookly' ), 'read',
                            Modules\Staff\Page::pageSlug(), function () { Modules\Staff\Page::render(); } );
                    }
                }
                add_submenu_page( 'bookly-menu', $services, $services, $required_capability,
                    Modules\Services\Page::pageSlug(), function () { Modules\Services\Page::render(); } );
                Lib\Proxy\Taxes::addBooklyMenuItem();
                if ( $current_user->has_cap( $required_capability ) || $current_user->has_cap( 'manage_bookly_appointments' ) ) {
                    add_submenu_page( 'bookly-menu', $customers, $customers, 'read',
                        Modules\Customers\Page::pageSlug(), function () { Modules\Customers\Page::render(); } );
                }
                Lib\Proxy\CustomerInformation::addBooklyMenuItem();
                Lib\Proxy\CustomerGroups::addBooklyMenuItem();
                Lib\Proxy\Discounts::addBooklyMenuItem();
                add_submenu_page( 'bookly-menu', $notifications, $notifications, $required_capability,
                    Modules\Notifications\Page::pageSlug(), function () { Modules\Notifications\Page::render(); } );
                Modules\CloudSms\Page::addBooklyMenuItem();
                if ( $current_user->has_cap( $required_capability ) || $current_user->has_cap( 'manage_bookly_appointments' ) ) {
                    add_submenu_page( 'bookly-menu', $payments, $payments, 'read',
                        Modules\Payments\Page::pageSlug(), function () { Modules\Payments\Page::render(); } );
                }
                add_submenu_page( 'bookly-menu', $appearance, $appearance, $required_capability,
                    Modules\Appearance\Page::pageSlug(), function () { Modules\Appearance\Page::render(); } );
                Lib\Proxy\Coupons::addBooklyMenuItem();
                Lib\Proxy\CustomFields::addBooklyMenuItem();
                add_submenu_page(
                    'bookly-menu', $settings, $settings, $required_capability,
                    Modules\Settings\Page::pageSlug(), function () { Modules\Settings\Page::render(); }
                );
                Modules\Diagnostics\Page::addBooklyMenuItem();
                Modules\News\Page::addBooklyMenuItem();
                Modules\Shop\Page::addBooklyMenuItem();

                if ( ! Lib\Config::proActive() ) {
                    $submenu['bookly-menu'][] = array( esc_attr__( 'Get Bookly Pro', 'bookly' ) . ' <i class="fas fa-fw fa-certificate" style="color: #f4662f"></i>', 'read', Lib\Utils\Common::prepareUrlReferrers( 'https://codecanyon.net/item/bookly/7226091?ref=ladela', 'admin_menu' ), );
                }

                // Bookly Cloud menu
                $cloud = Lib\Cloud\API::getInstance();
                $dynamic_position .= '1'; // position always is under `Bookly`
                $page_title = $menu_title = 'Bookly Cloud';
                if ( $cloud->general->getPromotionForNotice() ) {
                    $menu_title .= ' <span class="update-plugins"><span class="update-count">$</span></span>';
                }
                add_menu_page( $page_title, $menu_title, $required_capability, 'bookly-cloud-menu', '',
                    plugins_url( 'resources/images/menu_cloud.png', __FILE__ ), $dynamic_position );
                add_submenu_page( 'bookly-cloud-menu', $products, $products, $required_capability,
                    Modules\CloudProducts\Page::pageSlug(), function () { Modules\CloudProducts\Page::render(); } );
                if ( $cloud->getToken() ) {
                    foreach ( $cloud->general->getProducts() as $product ) {
                        if ( $cloud->account->productActive( $product['id'] ) ) {
                            switch ( $product['id'] ) {
                                case Lib\Cloud\Account::PRODUCT_SMS_NOTIFICATIONS:
                                    Modules\CloudSms\Page::addBooklyCloudMenuItem( $product );
                                    break;
                                case Lib\Cloud\Account::PRODUCT_ZAPIER:
                                    Modules\CloudZapier\Page::addBooklyCloudMenuItem( $product );
                                    break;
                                case Lib\Cloud\Account::PRODUCT_VOICE:
                                    Modules\CloudVoice\Page::addBooklyCloudMenuItem( $product );
                                    break;
                                case Lib\Cloud\Account::PRODUCT_WHATSAPP:
                                    Modules\CloudWhatsapp\Page::addBooklyCloudMenuItem( $product );
                                    break;
                                default:
                                    Lib\Cloud\Proxy\Shared::renderCloudMenu( $product );
                            }
                        }
                    }
                    add_submenu_page( 'bookly-cloud-menu', $billing, $billing, $required_capability,
                        Modules\CloudBilling\Page::pageSlug(), function () { Modules\CloudBilling\Page::render(); } );
                    add_submenu_page( 'bookly-cloud-menu', $settings, $settings, $required_capability,
                        Modules\CloudSettings\Page::pageSlug(), function () { Modules\CloudSettings\Page::render(); } );
                }
            }

            unset( $submenu['bookly-menu'][0], $submenu['bookly-cloud-menu'][0] );
        }
    }
}