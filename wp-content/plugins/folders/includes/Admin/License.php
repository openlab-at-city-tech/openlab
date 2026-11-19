<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * License Management Class
 *
 * Handles license activation, deactivation, and verification.
 */
class License {

    /**
     * Check if the license is active.
     *
     * This plugin is distributed as a permanent free version: Pro features
     * stay locked regardless of any license key that may be stored or
     * validated, so this always returns false.
     *
     * @return bool Always false.
     */
    public static function is_license_active() {
        return false;
    }

    /**
     * Get the URL to the Folders Pro upgrade/plans page.
     *
     * @return string URL.
     */
    public static function get_pro_url() {
        $settingPage = \Folders\Admin\Settings::get_field_settings( 'general_settings', 'show_folder_in_settings' );
        if ( $settingPage ) {
            return admin_url( 'options-general.php?page=premio-folders-settings&tab=manage-folders-plan' );
        } else {
            return admin_url( 'admin.php?page=folders-upgrade-to-pro' );
        }
    }
}
