<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Setting Tabs Management Class
 *
 * Handles the inclusion of different settings tabs templates.
 */
class SettingTabs {

    /**
     * Active tab identifier.
     *
     * @var string
     */
    public $active_tab = 'folders_settings_tab';

    /**
     * Register the actions that render each settings tab.
     */
    public function __construct() {
        add_action( 'folders_settings_tab', array( $this, 'folders_settings_tab' ) );
        add_action( 'folders_customization_tab', array( $this, 'folders_customization_tab' ) );
        add_action( 'folders_users_tab', array( $this, 'folders_users_tab' ) );
        add_action( 'folders_notifications_tab', array( $this, 'folders_notifications_tab' ) );
        add_action( 'folders_maintenance_tab', array( $this, 'folders_maintenance_tab' ) );
        add_action( 'folders_active_tab', array( $this, 'folders_active_tab' ), 10, 1 );
    }

    /**
     * Get the currently active settings tab from the `tab` query argument.
     *
     * @param string $tab Unused; the value is always read from `$_GET['tab']`.
     * @return string Tab slug, or "general-settings" when missing or invalid.
     */
    public function folders_active_tab($tab = '') {
        $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general-settings';
        if(in_array($tab, array('general-settings', 'customize-folders', 'user-restrictions', 'notifications', 'tools-and-maintenance', 'manage-folders-plan'))) {
            return $tab;
        }
        return 'general-settings';
    }

    /**
     * Render the general settings tab.
     *
     * @return void
     */
    public function folders_settings_tab() {
        include_once FOLDERS_TEMPLATE_DIR . 'settings/general-settings.php';
    }

    /**
     * Render the customization settings tab.
     *
     * @return void
     */
    public function folders_customization_tab() {
        include_once FOLDERS_TEMPLATE_DIR . 'settings/customize-folders.php';
    }

    /**
     * Render the user restrictions settings tab.
     *
     * @return void
     */
    public function folders_users_tab() {
        include_once FOLDERS_TEMPLATE_DIR . 'settings/user-restrictions.php';
    }

    /**
     * Render the notifications settings tab.
     *
     * @return void
     */
    public function folders_notifications_tab() {
        include_once FOLDERS_TEMPLATE_DIR . 'settings/notifications.php';
    }

    /**
     * Render the maintenance settings tab.
     *
     * @return void
     */
    public function folders_maintenance_tab() {
        include_once FOLDERS_TEMPLATE_DIR . 'settings/tools-and-maintenance.php';
    }
}
