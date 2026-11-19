<?php

namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Email newsletter sign-up screen shown on the Folders settings page.
 *
 * Decides when the sign-up screen is shown, provides its content, and
 * handles the "sign up" and "skip" actions. Skipping postpones the screen
 * for 7 days the first time and hides it permanently the second time.
 */
class FoldersSignup {
    /**
     * Option name used to store the update message for the "Folder" feature.
     */
    private static $update_message_option = 'folder_update_message';

    /**
     * Option name used to store the date for the next signup related to the "Folder" feature.
     */
    private static $next_signup_date = 'folder_next_signup_date';

    /**
     * Stores the status of the modal to be shown.
     */
    private static $show_modal_name = 'folder_show_signup_modal';

    /**
     * Register the `wp_ajax_folder_update_status` AJAX handler.
     */
    public function __construct() {
        // ajax callback
        add_action( 'wp_ajax_folder_update_status', array($this, 'update_status'));


    }//end __construct()


    /**
     * Define the `FOLDER_UPDATE_POPUP_CONTENT` constant with the sign-up screen
     * texts and image URLs.
     *
     * Does nothing if the constant is already defined.
     *
     * @return void
     */
    public static function load_signup_settings()
    {
        if(defined('FOLDER_UPDATE_POPUP_CONTENT')) {
            return;
        }

        define('FOLDER_UPDATE_POPUP_CONTENT', array(
            'plugin_name'           => esc_html__('Folders', 'folders'),
            'trust_user'            => esc_html__('Join the list 80,000+ users trust', 'folders'),
            'website_owners'        => esc_html__('80,000+', 'folders'),
            'rating'                => esc_html__('5/5 Rating', 'folders'),
            'review'                => esc_html__('Based on 1,400+ Reviews', 'folders'),
            'plugin_logo'           => FOLDERS_PLUGIN_URL . "assets/images/signup/folder-icon.png",
            'trust_user_img'        => FOLDERS_PLUGIN_URL . "assets/images/signup/user-trust.svg",
            'font_url'              => FOLDERS_PLUGIN_URL . "assets/fonts/Lato-Regular.woff",
            'background_image'      => FOLDERS_PLUGIN_URL . "assets/images/signup/premio-update-bg.svg",
            'shape_bottom'          => FOLDERS_PLUGIN_URL . "assets/images/signup/premio-update-bg-btm.png",
            'shape_bottom_right'    => FOLDERS_PLUGIN_URL . "assets/images/signup/premio-update-bg-right.png",
            'mail_icon'             => FOLDERS_PLUGIN_URL . "assets/images/signup/mail-icon.svg",
            'user_icon'             => FOLDERS_PLUGIN_URL . "assets/images/signup/users.svg",
            'slash_icon'            => FOLDERS_PLUGIN_URL . "assets/images/signup/slash.svg",
            'star_icon'             => FOLDERS_PLUGIN_URL . "assets/images/signup/star.svg",
            'arrow_right'           => FOLDERS_PLUGIN_URL . "assets/images/signup/arrow-right.svg",
            'check_circle'          => FOLDERS_PLUGIN_URL . "assets/images/signup/check-circle.svg",
            'pre_loader'            => FOLDERS_PLUGIN_URL . "assets/images/signup/pre-loader.svg",
        ));
    }


    /**
     * Checks the status of a modal and determines whether it should be displayed.
     *
     * This method evaluates various conditions, such as predefined options and the HTTP referrer,
     * to decide if the modal should be shown. It may also update specific modal-related options
     * based on the results of these checks.
     *
     * @return bool Returns true if the modal should be displayed; otherwise, false.
     */
    public static function check_modal_status() {
        if(get_option(self::$update_message_option) == -1 || get_option(self::$show_modal_name) == 2) {
            return false;
        }
        $referer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';

        if (!str_contains($referer, 'wcp_folders_settings')) {
            $default_folders = get_option("get_folders_page_views", false);
            if ($default_folders !== false) {
                add_option(self::$show_modal_name, 1);
            }
        }

        if (get_option(self::$show_modal_name)) {
            $next_signup_date = get_option(self::$next_signup_date);

            if($next_signup_date === false) {
                self::load_signup_settings();
                return true;
            } else {
                if($next_signup_date < date('Y-m-d')) {
                    self::load_signup_settings();
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * AJAX handler for the sign-up screen buttons.
     *
     * With `status` 1, subscribes the given email on premioapps.com and hides the
     * screen permanently. Otherwise postpones the screen for 7 days, or hides it
     * permanently if it was already postponed once. Always outputs "1" and exits.
     *
     * @return void
     */
    public function update_status() {

        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'folder_update_nonce')) {
            $status = sanitize_text_field($_REQUEST['status']);
            $email = sanitize_text_field($_REQUEST['email']);
            if($status == 1) {
                update_option(self::$update_message_option, -1);
                $url = 'https://premioapps.com/premio/signup/email.php';
                $apiParams = [
                    'plugin' => 'folders',
                    'email'  => $email,
                ];

                // Signup Email for Chaty
                $apiResponse = wp_safe_remote_post($url, ['body' => $apiParams, 'timeout' => 15, 'sslverify' => true]);
            } else {

                $next_date = date('Y-m-d', strtotime('+7 days'));
                $next_signup_date = get_option(self::$next_signup_date);

                if($next_signup_date === false) {

                    add_option(self::$next_signup_date, $next_date);
                } else {
                    update_option(self::$update_message_option, -1);
                }
            }
        }
        echo "1";
        die;
    }
}