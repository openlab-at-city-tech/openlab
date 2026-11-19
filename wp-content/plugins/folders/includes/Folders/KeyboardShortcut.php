<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Keyboard shortcuts for the folder sidebar.
 *
 * Shows the shortcuts button in the sidebar header and outputs the shortcuts
 * help modal when the "use shortcuts" setting is enabled.
 */
class KeyboardShortcut {

    /**
     * Register the sidebar header and admin footer hooks.
     */
    public function __construct() {
        add_action('folders_header_section', array($this, 'folders_header_section'));
        add_action('admin_footer', array($this, 'admin_footer'));
    }

    /**
     * Output the keyboard shortcuts button in the folder sidebar header.
     *
     * @return void
     */
    public function folders_header_section()
    {
        $shortcut_status = \Folders\Admin\Settings::get_field_settings('general_settings', 'use_shortcuts');
        if(!$shortcut_status) {
            return;
        }
        include_once FOLDERS_TEMPLATE_DIR . 'folders/shortcut.php';
    }

    /**
     * Output the keyboard shortcuts modal on screens where folders are active.
     *
     * @return void
     */
    public function admin_footer() {
        $is_active = \Folders\Folders\Settings::is_folders_active();
        $shortcut_status = \Folders\Admin\Settings::get_field_settings('general_settings', 'use_shortcuts');
        if(!$shortcut_status || !$is_active) {
            return;
        }
        $shortcut_status = \Folders\Admin\Settings::get_field_settings('general_settings', 'use_shortcuts');
        if(!$shortcut_status) {
            return;
        }
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/shortcut.php';
    }

    /**
     * Get the list of keyboard shortcuts shown in the shortcuts modal.
     *
     * Keys starting with "dashicons-" are rendered as arrow icons.
     *
     * @return array[] List of shortcuts, each with a `title` and a `key` array.
     */
    public static function get_shortcodes()
    {
        return [
            [
                'title' => esc_html__('Edit Folder', 'folders'),
                'key'   => ['Right-click']
            ], [
                'title' => esc_html__('Create New Folder', 'folders'),
                'key'   => ['Shift', 'N']
            ], [
                'title' => esc_html__('Rename Folder', 'folders'),
                'key'   => ['F2']
            ], [
                'title' => esc_html__('Delete Folder', 'folders'),
                'key'   => ['Delete']
            ], [
                'title' => esc_html__('Next Folder', 'folders'),
                'key'   => ['dashicons-arrow-down-alt']
            ], [
                'title' => esc_html__('Previous Folder', 'folders'),
                'key'   => ['dashicons-arrow-up-alt']
            ], [
                'title' => esc_html__('Re-order folders to upwards', 'folders'),
                'key'   => ['Ctrl', 'dashicons-arrow-up-alt']
            ], [
                'title' => esc_html__('Re-order folders to downwards', 'folders'),
                'key'   => ['Ctrl', 'dashicons-arrow-down-alt']
            ]
        ];
    }
}
