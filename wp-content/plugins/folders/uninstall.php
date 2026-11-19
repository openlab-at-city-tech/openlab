<?php
/**
 * Uninstall handler.
 *
 * Runs when the plugin is deleted from the Plugins screen (not when it is
 * deactivated). Removes every folder and the plugin's settings, but only when
 * the "remove folders when the plugin is removed" setting is enabled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Did the site owner ask for all folder data to be removed with the plugin?
// Read the current settings first, then the legacy option for sites that never migrated.
$folders_uninstall_value    = null;
$folders_uninstall_settings = get_option( 'premio_folders_settings', [] );

if ( is_array( $folders_uninstall_settings ) && isset( $folders_uninstall_settings['advanced_settings']['remove_folders_when_removed'] ) ) {
    $folders_uninstall_value = $folders_uninstall_settings['advanced_settings']['remove_folders_when_removed'];
} else {
    $folders_uninstall_legacy = get_option( 'customize_folders', [] );
    if ( is_array( $folders_uninstall_legacy ) && isset( $folders_uninstall_legacy['remove_folders_when_removed'] ) ) {
        $folders_uninstall_value = $folders_uninstall_legacy['remove_folders_when_removed'];
    }
}

if ( is_string( $folders_uninstall_value ) ) {
    $folders_uninstall_remove = in_array( strtolower( trim( $folders_uninstall_value ) ), [ '1', 'on', 'yes' ], true );
} else {
    $folders_uninstall_remove = ! empty( $folders_uninstall_value );
}

if ( ! $folders_uninstall_remove ) {
    return;
}

// Folders Pro uses the same folders, so keep them while Pro is still installed.
if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

foreach ( array_keys( get_plugins() ) as $folders_uninstall_plugin_file ) {
    if ( 0 === strpos( $folders_uninstall_plugin_file, 'folders-pro/' ) ) {
        return;
    }
}

if ( ! class_exists( '\Folders\Folders\Actions\FoldersCRUD' ) ) {
    require_once __DIR__ . '/includes/Folders/Actions/FoldersCRUD.php';
}

if ( method_exists( '\Folders\Folders\Actions\FoldersCRUD', 'remove_all_data' ) ) {
    \Folders\Folders\Actions\FoldersCRUD::remove_all_data();
}
