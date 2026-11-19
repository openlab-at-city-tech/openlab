<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activation handler.
 *
 * Sets the options the plugin needs on first activation, including the flag
 * that redirects the admin to the settings page.
 */
class Activate {

    /**
     * Constructor. No hooks are registered; activation runs via {@see activate()}.
     */
    public function __construct() {

    }

    /**
     * Run activation tasks.
     *
     * Flags a redirect to the settings page, marks the intro box to be shown the
     * first time, and (for version 3.0) hides the folder color pop-up.
     *
     * @return void
     */
    public function activate() {
        update_option("folder_redirect_status", 1);

        if (FOLDERS_VERSION == "3.0") {
            $hide_folder_color_pop_up = get_option("hide_folder_color_pop_up");
            if (!($hide_folder_color_pop_up)) {
                add_option("hide_folder_color_pop_up", "yes");
            } else {
                update_option("hide_folder_color_pop_up", "yes");
            }
        }

        $option = get_option("folder_intro_box", false);
        if ($option === false) {
            add_option("folder_intro_box", "show");
        }
    }
}
