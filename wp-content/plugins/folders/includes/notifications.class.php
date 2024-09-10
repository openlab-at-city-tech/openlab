<?php
/**
 * Class Folders Plugins import/export
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Folders_Notifications
 *
 * This class is responsible for managing notifications related to folders.
 */
class Folders_Notifications_Free
{
    /**
     * Class constructor.
     *
     * This method is called when an instance of the class is created. It is used to initialize the object.
     *
     * @return void
     */

    public $default_settings = null;


    public function __construct() {
        add_filter("check_for_folders_notification_settings", [$this, "notification_setting"], 10, 1);

    }

    public function notification_setting($current_settings)
    {
        if(!is_null($this->default_settings)) {
            return $this->default_settings;
        }
        $folders_settings = get_option("folders_settings");
        $folders_settings = !is_array($folders_settings)?[]:$folders_settings;
        $post_setting = apply_filters("check_for_folders_post_args", ["show_in_menu" => 1]);
        $post_types = get_post_types( $post_setting, 'objects' );
        $default_post_type = [];
        if(!empty($post_types)) {
            foreach($post_types as $post_type => $setting) {
                if(in_array($post_type, $folders_settings)) {
                    $default_post_type[$post_type] = $setting->label;
                }
            }
        }
        if(in_array("folders4plugins",$folders_settings)) {
            $default_post_type['plugin'] = "Plugins";
        }
        $default_settings = [
            'allow_notification' => 'off',
            'notification_email' => [""],
            'mail_setting'       => [
                'on_item_insert'     => [
                    'status'         => 'off',
                    'default'        => $default_post_type,
                    'post_type'      => [],
                    'title'          => esc_html__("Send Notifications when users add any of the following new items", "folders"),
                    'email'          => [
                        'subject'    => "New {post_type} added by {user_name}, {email} - Folders",
                        'content'    => "Activity: {post_type} added\nWhere: {post_type}\nTitle: {post_title}\nPost Status: {post_status}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}\nPost Status: {post_status}"
                ],
                'on_item_edit'       => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users making edits to any of the following items", "folders"),
                    'email'          => [
                        'subject'    => "{post_type} edited by {user_name}, {email} - Folders",
                        'content'    => "Activity: {post_type} edited\nWhere: {post_type}\nTitle: {post_title}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}"
                ],
                'on_item_remove'     => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users delete/deactivate any of the following items", "folders"),
                    'email'          => [
                        'subject'    => "{post_type} deleted by {user_name}, {email} - Folders",
                        'content'    => "Activity: {post_type} deleted\nWhere: {post_type}\nTitle: {post_title}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}"
                ],
                'on_creating_folder' => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users add a new folder", "folders"),
                    'email'          => [
                        'subject'    => "New folder added by {user_name}, {email} in {post_type} - Folders",
                        'content'    => "Activity: {post_type} folder added\nWhere: {post_type}\nFolder name: {folder_name}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}"
                ],
                'on_removing_folder' => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users delete folder", "folders"),
                    'email'          => [
                        'subject'    => "Folder deleted by {user_name}, {email} in {post_type} - Folders",
                        'content'    => "Activity: {post_type} folder deleted\nWhere: {post_type}\nFolder name: {folder_name}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}"
                ],
                'on_moving_folder' => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users move folder", "folders"),
                    'email'          => [
                        'subject'    => "Folder moved by {user_name}, {email} in {post_type} - Folders",
                        'content'    => "Activity: {post_type} folder moved\nWhere: {post_type}\nFolder name: {folder_name}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nFolder name: {folder_name}"
                ],
                'on_item_move'       => [
                    'status'         => 'off',
                    'post_type'      => [],
                    'default'        => $default_post_type,
                    'title'          => esc_html__("Send Notifications when users move any of the following items to folder", "folders"),
                    'email'          => [
                        'subject'    => "{post_type} moved by {user_name}, {email} - Folders",
                        'content'    => "Activity: {post_type} moved\nWhere: {post_type}\nTitle: {post_title}"
                    ],
                    'help'           => "Username: {user_name}\nEmail: {email}\nPost type: {post_type}\nPost title: {post_title}\nFolder name: {folder_name}"
                ],
                'remove_users' => [
                    'status'         => 'off',
                    'users'          => [],
                    'default'        => [],
                    'title'          => esc_html__("Don't Send Notifications when these users make changes", "folders")
                ],
            ]
        ];
        $current_settings = !is_array($current_settings)?[]:$current_settings;

        $this->default_settings = $this->set_default_value($current_settings, $default_settings);
        return $this->default_settings;
    }

    function set_default_value($current_settings, $default_settings) {
        if(is_array($default_settings)) {
            foreach ($default_settings as $key => $value) {
                if(isset($current_settings[$key]) && is_array($current_settings[$key]) && $this->is_numeric_array($current_settings[$key])) {
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
                            $default_settings[$key] = $this->set_default_value($current_settings[$key], $default_settings[$key]);
                        }
                    }
                }
            }
        } else {
            return $current_settings;
        }
        return $default_settings;
    }

}
if(class_exists("Folders_Notifications_Free")) {
    $Folders_Notifications = new Folders_Notifications_Free();
}
