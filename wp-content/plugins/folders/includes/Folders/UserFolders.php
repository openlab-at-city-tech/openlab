<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * User access level for folders.
 *
 * In this version every user gets full ("admin") folder access; per-role
 * restrictions such as "view-only" and "no-access" are Pro features.
 */
class UserFolders {

    private static $user_role = null;

    /**
     * Get the current user's folder access level.
     *
     * @return string Always "admin".
     */
    public static function get_user_role() {
        if ( null !== self::$user_role ) {
            return self::$user_role;
        }

        self::$user_role = "admin";
        return self::$user_role;
    }

    /**
     * Get the folder the current user had open last for a post type.
     *
     * Stored per user (user meta `folders_last_folder`), so one user's
     * navigation never changes what another user sees.
     *
     * @param string $post_type Post type name.
     * @return string Folder slug, "-1", "all" or an empty string when none is stored.
     */
    public static function get_last_folder($post_type) {
        $user_id = get_current_user_id();
        if (!$user_id || empty($post_type)) {
            return '';
        }
        $folders = get_user_meta($user_id, 'folders_last_folder', true);
        return (is_array($folders) && isset($folders[$post_type])) ? (string) $folders[$post_type] : '';
    }

    /**
     * Remember (or, with an empty value, forget) the folder the current user has open.
     *
     * @param string $post_type Post type name.
     * @param string $folder    Folder slug, "-1" or "all"; empty to forget.
     * @return void
     */
    public static function set_last_folder($post_type, $folder = '') {
        $user_id = get_current_user_id();
        if (!$user_id || empty($post_type)) {
            return;
        }
        $folders = get_user_meta($user_id, 'folders_last_folder', true);
        $folders = is_array($folders) ? $folders : [];
        if ($folder === '' || $folder === null) {
            if (!isset($folders[$post_type])) {
                return;
            }
            unset($folders[$post_type]);
        } else {
            $folders[$post_type] = (string) $folder;
        }
        if (empty($folders)) {
            delete_user_meta($user_id, 'folders_last_folder');
        } else {
            update_user_meta($user_id, 'folders_last_folder', $folders);
        }
    }
}
