<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Folder-enabled post type settings.
 *
 * Reads and writes the `folders_settings` option (the list of post types that
 * have folders), maps post types to their folder taxonomy, and checks
 * whether folders should be shown on the current screen.
 */
class Settings {

    private static $settings = [];

    /**
     * Save the list of folder-enabled post types.
     *
     * @param string[] $settings Post type names.
     * @return void
     */
    public static function update_settings($settings) {
        update_option("folders_settings", $settings);
        self::$settings = $settings;
    }

    /**
     * Get the list of folder-enabled post types.
     *
     * Defaults to pages, posts and media when the option does not exist. The
     * value is cached for the rest of the request.
     *
     * @return string[] Post type names.
     */
    public static function get_settings() {
        if(!self::$settings) {
            $settings = get_option("folders_settings", false);
            if($settings === false) {
                $settings = ['page', 'post', 'attachment'];
            }
            self::$settings = $settings;
        }

        return self::$settings;
    }

    /**
     * Check whether folders are enabled for a post type.
     *
     * @param string $post_type Post type name.
     * @return bool True when folders are enabled for the post type.
     */
    public static function check_for_folder($post_type) {
        if(!self::$settings) {
            self::get_settings();
        }

        return is_array(self::$settings) && in_array($post_type, self::$settings);
    }

    /**
     * Get the folder taxonomy name for a post type.
     *
     * Posts use `post_folder`, pages use `folder`, media uses `media_folder`
     * and other post types use `{post_type}_folder`.
     *
     * @param string $post_type Post type name.
     * @return string Folder taxonomy name.
     */
    public static function get_folder_post_type($post_type) {
        if ($post_type == "post") {
            return "post_folder";
        } else if ($post_type == "page") {
            return "folder";
        } else if ($post_type == "attachment") {
            return "media_folder";
        }

        return $post_type . '_folder';
    }

    /**
     * Check whether the current user may change the folder structure
     * (create, move, sort, lock, color or copy folders).
     *
     * Matches the `manage_categories` capability the folder taxonomies are
     * registered with.
     *
     * @return bool
     */
    public static function current_user_can_manage_folders() {
        return current_user_can('manage_categories');
    }

    /**
     * Check whether the current user may use the folder sidebar for a post type
     * (open and collapse folders, resize or hide the sidebar).
     *
     * @param string $post_type Post type the sidebar belongs to. Optional.
     * @return bool
     */
    public static function current_user_can_use_folders($post_type = '') {
        if (self::current_user_can_manage_folders()) {
            return true;
        }

        if ($post_type === 'attachment' || $post_type === 'media') {
            return current_user_can('upload_files') || current_user_can('edit_posts');
        }

        $post_type_object = !empty($post_type) ? get_post_type_object($post_type) : null;
        if ($post_type_object && isset($post_type_object->cap->edit_posts)) {
            return current_user_can($post_type_object->cap->edit_posts);
        }

        return current_user_can('edit_posts') || current_user_can('upload_files');
    }

    /**
     * Check whether the folder sidebar should be shown on the current screen.
     *
     * True on the list screen (or Media Library) of a folder-enabled post type.
     * False on the media trash view and for AJAX requests other than media.
     *
     * @return bool
     */
    public static function is_folders_active($post_type = '') {
        global $typenow;
        if(empty($post_type)) {
            $post_type = $typenow;
        }

        if($post_type == 'shop_order') {
            return false;
        }

        if (($typenow == "attachment" || $typenow == "media") && (isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash")) {
            return false;
        }

        $isAJAX = defined('DOING_AJAX') && DOING_AJAX;
        if ($isAJAX && $typenow != "attachment") {
            return false;
        }

        global $current_screen;

        // There is no current screen on front-end, REST and some page-builder
        // requests, so never read ->base from it directly.
        $screen_base = isset($current_screen->base) ? $current_screen->base : '';

        if (self::check_for_folder($typenow) && ('edit' == $screen_base || 'upload' == $screen_base)) {
            return true;
        }

        if (empty($typenow) && 'upload' == $screen_base) {
            if (self::check_for_folder("attachment")) {
                return true;
            }
        }

        return false;
    }
}
