<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Modals Management Class
 *
 * Handles the rendering of modals within the admin area.
 */
class Modals {

    /**
     * Register hooks that output the settings-page modals and footer.
     */
    public function __construct() {
        add_action( 'folders_settings_modal', array( $this, 'folders_settings_modal' ) );
        add_action( 'folders_footer', array( $this, 'folders_footer' ) );
        add_action( 'folders_hide_recommended_plugins_modal', array( $this, 'folders_hide_recommended_plugins_modal' ) );
    }

    /**
     * Output the shared plugin footer template.
     *
     * @return void
     */
    public function folders_footer() {
        include_once FOLDERS_TEMPLATE_DIR . 'footer/footer.php';
    }

    /**
     * Output the "hide recommended plugins" confirmation modal.
     *
     * @return void
     */
    public function folders_hide_recommended_plugins_modal() {
        include_once FOLDERS_TEMPLATE_DIR . 'modals/hide-recommended-plugins.php';
    }

    /**
     * Output all modals used on the Folders settings page.
     *
     * Includes the keyboard shortcuts, import, remove/delete data, user
     * permission, test email and plugin data deletion modals.
     *
     * @return void
     */
    public function folders_settings_modal() {
        include_once FOLDERS_TEMPLATE_DIR . 'modals/folders-shortcuts.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/folders-import-data.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/remove-folders-data.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/delete-folders-data.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/update-user-permission.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/send-email-test.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/import-data.php';
        include_once FOLDERS_TEMPLATE_DIR . 'modals/delete-plugin-data.php';
    }

    /**
     * Render a generic settings modal.
     *
     * @param array $args Modal arguments, available to the template as `$args`.
     * @return void
     */
    public static function render_modal( $args = [] ) {
        include FOLDERS_TEMPLATE_DIR . 'modals/modal-settings.php';
    }
}
