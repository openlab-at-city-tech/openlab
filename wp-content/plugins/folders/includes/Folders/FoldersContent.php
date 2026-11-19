<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Outputs the folder sidebar and supplies default folder metadata.
 */
class FoldersContent {

    /**
     * Register the sidebar output and default folder info hooks.
     */
    public function __construct() {
        add_action('admin_footer', array($this, 'admin_footer'));
        add_filter('apply_filters_folder_info_metadata', array($this, 'default_folder_info'),10, 5);
    }

    /**
     * Provide default `folder_info` values when a folder has no stored info.
     *
     * Hooked to the `apply_filters_folder_info_metadata` filter.
     *
     * @param mixed  $value     Current metadata value; false when none is stored.
     * @param int    $object_id Term ID.
     * @param string $meta_key  Meta key.
     * @param bool   $single    Whether a single value was requested.
     * @param string $meta_type Meta type.
     * @return mixed Stored value, or an array of default flags when none is stored.
     */
    public function default_folder_info($value, $object_id, $meta_key, $single, $meta_type)
    {
        if($value === false) {
            return [
                'is_sticky' => 0,
                'is_high' => 0,
                'is_locked' => 0,
                'is_default' => 0,
                'is_active' => 0,
                'has_color' => ''
            ];
        }
        return $value;
    }

    /**
     * Output the folder sidebar on screens where folders are active.
     *
     * @return void
     */
    public static function admin_footer() {
        $is_active = \Folders\Folders\Settings::is_folders_active();
        if($is_active) {
            include_once FOLDERS_TEMPLATE_DIR . 'folders/sidebar.php';
        }
    }
}
