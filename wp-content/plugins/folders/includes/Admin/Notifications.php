<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Email notification settings defaults.
 *
 * Builds the default email notification settings (which events trigger
 * emails, subjects, templates and placeholders) and merges them with the
 * settings saved by the user.
 */
class Notifications {

    private static $default_settings = null;

    /**
     * Register the `check_for_folders_notification_settings` filter.
     */
    public function __construct() {
        add_filter("check_for_folders_notification_settings", [$this, "notification_setting"], 10, 1);
    }

    /**
     * Get the notification settings merged with their defaults.
     *
     * Defaults cover item insert, edit, remove and move, and folder create,
     * remove and move events for every folder-enabled post type. The result is
     * cached for the rest of the request.
     *
     * @param array $current_settings Saved notification settings.
     * @return array Complete notification settings.
     */
    public function notification_setting($current_settings)
    {
        if (!is_null(self::$default_settings)) {
            return self::$default_settings;
        }
        $folders = \Folders\Folders\Settings::get_settings();
        $post_setting = apply_filters("check_for_folders_post_args", []);
        $post_types = get_post_types($post_setting, 'objects');
        $default_post_type = [];
        if (!empty($post_types)) {
            foreach ($post_types as $post_type => $setting) {
                if (in_array($post_type, $folders)) {
                    $default_post_type[$post_type] = $setting->label;
                }
            }
        }
        if (in_array("folders4plugins", $folders)) {
            $default_post_type['plugin'] = "Plugins";
        }

        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        $default_settings = [
            'allow_notification' => 'off',
            'notification_email' => [$user_email],
            'mail_setting' => [
                'on_item_insert' => [
                    'status' => 'off',
                    'default' => $default_post_type,
                    'post_type' => [],
                    'title' => esc_html__("Send Notifications when users add any of the following new items", 'folders'),
                    'email' => [
                        'subject' => "New {post_type} added by {user_name}, {email} - Folders",
                        'content' => "Activity: {post_type} added\nWhere: {post_type}\nTitle: {post_title}\nPost Status: {post_status}\n{activity_link}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}\nPost Status: {post_status}\n{activity_link}"
                ],
                'on_item_edit' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users making edits to any of the following items", 'folders'),
                    'email' => [
                        'subject' => "{post_type} edited by {user_name}, {email} - Folders",
                        'content' => "Activity: {post_type} edited\nWhere: {post_type}\nTitle: {post_title}\n{activity_link}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}\n{activity_link}"
                ],
                'on_item_remove' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users delete/deactivate any of the following items", 'folders'),
                    'email' => [
                        'subject' => "{post_type} deleted by {user_name}, {email} - Folders",
                        'content' => "Activity: {post_type} deleted\nWhere: {post_type}\nTitle: {post_title}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}"
                ],
                'on_creating_folder' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users add a new folder", 'folders'),
                    'email' => [
                        'subject' => "New folder added by {user_name}, {email} in {post_type} - Folders",
                        'content' => "Activity: {post_type} folder added\nWhere: {post_type}\nFolder name: {folder_name}\n{activity_link}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}\n{activity_link}"
                ],
                'on_removing_folder' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users delete folder", 'folders'),
                    'email' => [
                        'subject' => "Folder deleted by {user_name}, {email} in {post_type} - Folders",
                        'content' => "Activity: {post_type} folder deleted\nWhere: {post_type}\nFolder name: {folder_name}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}"
                ],
                'on_moving_folder' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users move folder", 'folders'),
                    'email' => [
                        'subject' => "Folder moved by {user_name}, {email} in {post_type} - Folders",
                        'content' => "Activity: {post_type} folder moved\nWhere: {post_type}\nFolder name: {folder_name}\n{activity_link}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}\n{activity_link}"
                ],
                'on_item_move' => [
                    'status' => 'off',
                    'post_type' => [],
                    'default' => $default_post_type,
                    'title' => esc_html__("Send Notifications when users move any of the following items to folder", 'folders'),
                    'email' => [
                        'subject' => "{post_type} moved by {user_name}, {email} - Folders",
                        'content' => "Activity: {post_type} moved\nWhere: {post_type}\nTitle: {post_title}\n{activity_link}"
                    ],
                    'help' => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}\nFolder name: {folder_name}\n{activity_link}"
                ],
                'remove_users' => [
                    'status' => 'off',
                    'users' => [],
                    'default' => [],
                    'title' => esc_html__("Don't Send Notifications when these users make changes", 'folders')
                ],
            ]
        ];
        $current_settings = !is_array($current_settings) ? [] : $current_settings;

        self::$default_settings = self::set_default_value($current_settings, $default_settings);
        return self::$default_settings;
    }


    /**
     * Recursively merge saved settings into the default settings.
     *
     * Saved scalar values and list arrays replace the defaults; associative
     * arrays are merged key by key; keys missing from the saved settings keep
     * their default value.
     *
     * @param array|mixed $current_settings Saved settings.
     * @param array|mixed $default_settings Default settings.
     * @return array|mixed Merged settings.
     */
    private static function set_default_value($current_settings, $default_settings)
    {
        if (is_array($default_settings)) {
            foreach ($default_settings as $key => $value) {
                if (isset($current_settings[$key]) && is_array($current_settings[$key]) && self::is_numeric_array($current_settings[$key])) {
                    $default_settings[$key] = $current_settings[$key];
                } else {
                    if (!is_array($value)) {
                        if (isset($current_settings[$key])) {
                            $default_settings[$key] = $current_settings[$key];
                        }
                    } else {
                        if (!isset($current_settings[$key])) {
                            $default_settings[$key] = $value;
                        } else if (!isset($default_settings[$key])) {
                            $default_settings[$key] = $current_settings[$key];
                        } else {
                            $default_settings[$key] = self::set_default_value($current_settings[$key], $default_settings[$key]);
                        }
                    }
                }
            }
        } else {
            return $current_settings;
        }
        return $default_settings;
    }

    /**
     * Check whether an array is a list (sequential keys starting at 0).
     *
     * @param array $arr Array to check.
     * @return bool True for an empty array or a list.
     */
    private static function is_numeric_array(array $arr)
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
