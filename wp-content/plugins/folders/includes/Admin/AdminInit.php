<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin initialization handler.
 *
 * Performs one-off admin actions on `admin_init`: the post-activation redirect
 * to the settings page and hiding the "Scan files" media menu.
 */
class AdminInit {

    /**
     * Register the `admin_init` hook.
     */
    public function __construct() {
        add_action('admin_init', [$this, 'admin_init']);
    }

    /**
     * Handle admin-side actions on `admin_init`.
     *
     * Redirects to the Folders settings page once after activation, and hides
     * the media cleaning ("scan-files") menu when requested with a valid nonce
     * by a user who can manage options.
     *
     * @return void
     */
    function admin_init()
    {
        $option = get_option("folder_redirect_status");
        if ($option == 1) {
            update_option("folder_redirect_status", 2);
            $settingsURL = \Folders\Admin\Settings::get_setting_page_url();
            wp_redirect($settingsURL);
            exit;
        }

        $getData = filter_input_array(INPUT_GET);
        if (isset($getData['hide_menu']) && $getData['hide_menu'] == "scan-files" && isset($getData['nonce'])) {
            if (current_user_can('manage_options')) {
                $nonce = $getData['nonce'];
                if (wp_verify_nonce($nonce, "folders-scan-files")) {
                    add_option('hide_folders_media_cleaning_menu', "yes");
                    wp_redirect(admin_url("upload.php"));
                    exit;
                }
            }
        }
    }
}
