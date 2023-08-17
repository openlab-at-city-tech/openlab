<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib;

/**
 * Class Dialog
 *
 * @package Bookly\Backend\Components\Dialogs\Staff\Edit
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale, $wpdb;

        wp_enqueue_media();

        self::enqueueStyles( array(
            'wp' => array( 'wp-color-picker' ),
            'frontend' => get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                ? array()
                : array( 'css/intlTelInput.css' )
        ,
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'frontend' => get_option( 'bookly_cst_phone_default_country' ) == 'disabled'
                ? array()
                : array( 'js/intlTelInput.min.js' => array( 'jquery' ) )
        ,
            'backend' => array(
                'js/jCal.js' => array( 'jquery' ),
                'js/nav-scrollable.js' => array( 'bookly-backend-globals' ),
                'js/range-tools.js' => array( 'bookly-backend-globals' ),
            ),
            'module' => array(
                'js/staff-details.js' => array( 'bookly-range-tools.js', 'wp-color-picker' ),
                'js/staff-services.js' => array( 'bookly-staff-details.js' ),
                'js/staff-schedule.js' => array( 'bookly-staff-services.js' ),
                'js/staff-days-off.js' => array( 'bookly-staff-schedule.js' ),
                'js/staff-edit-dialog.js' => array( 'bookly-staff-days-off.js' ),
            ),
        ) );

        Proxy\Pro::enqueueAssets();

        $query = Lib\Entities\Staff::query( 's' )
            ->select( 's.id, s.full_name' )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = s.wp_user_id' );

        if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
            $query->where( 's.wp_user_id', get_current_user_id() );
        }

        wp_localize_script( 'bookly-staff-edit-dialog.js', 'BooklyStaffEditDialogL10n', array(
            'intlTelInput' => array(
                'country' => get_option( 'bookly_cst_phone_default_country' ),
                'enabled' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled',
                'utils' => plugins_url( 'intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/resources/js/intlTelInput.utils.js' ),
            ),
            'holidays' => array(
                'close' => esc_attr__( 'Close', 'bookly' ),
                'days' => array_values( $wp_locale->weekday_abbrev ),
                'firstDay' => (int) get_option( 'start_of_week' ),
                'loading_img' => plugins_url( 'bookly-responsive-appointment-booking-tool/backend/resources/images/loading.gif' ),
                'months' => array_values( $wp_locale->month ),
                'repeat' => esc_attr__( 'Repeat every year', 'bookly' ),
                'special_days_error' => esc_attr__( 'The date has already passed.', 'bookly' ),
                'we_are_not_working' => esc_attr__( 'We are not working on this day', 'bookly' ),
            ),
            'services' => array(
                'capacity_error' => esc_attr__( 'Min capacity should not be greater than max capacity.', 'bookly' ),
                'hideTip' => get_user_meta( get_current_user_id(), 'bookly_packages_hide_staff_services_tip', true ),
            ),
            'appointmentsUrl' => Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Appointments\Ajax::pageSlug() ),
            'areYouSure' => esc_attr__( 'Are you sure?', 'bookly' ),
            'createStaff' => esc_attr__( 'Create staff', 'bookly' ),
            'currentTab' => self::parameter( 'tab', 'details' ),
            'editStaff' => esc_attr__( 'Edit staff', 'bookly' ),
            'proRequired' => (int) ! Lib\Config::proActive(),
            'settingsSaved' => esc_attr__( 'Settings saved.', 'bookly' ),
            'staff' => $query->sortBy( 'position' )->fetchArray(),
        ) );

        self::renderTemplate( 'dialog' );

        Proxy\Pro::renderArchivingComponents();
    }
}