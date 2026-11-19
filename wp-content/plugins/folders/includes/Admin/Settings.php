<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Settings helper for the Folders Pro plugin.
 *
 * Provides a single source of truth for default settings, migration
 * from legacy options, and convenient accessors to retrieve values
 * from the saved options in WordPress.
 */
class Settings {
    /**
     * Runtime cache for settings loaded from the `premio_folders_settings` option.
     * Prevents repeated calls to the database. Empty array until first load.
     *
     * @var array<string, array<string, mixed>>
     */
    private static $settings = [];

    /**
     * Default palette of folder colors used when no custom colors are saved.
     *
     * @var string[] Hex color values (e.g., #RRGGBB)
     */
    private static $defaultColors = ["#334155", "#86cd91", "#1E88E5", "#FF6060"];

    /**
     * Default list of media library columns supported by the plugin.
     *
     * @var string[]
     */
    private static $mediaItems = ['all', 'image_title', 'image_dimensions', 'image_type', 'image_date'];

    /**
     * Build the complete set of default settings used by the plugin.
     *
     * These defaults are applied when the settings option does not exist
     * and also used as fallbacks for any missing keys in saved options.
     *
     * @return array<string, array<string, mixed>> Nested associative array grouped by sections
     */
    private static function default_settings() {
        return [
            'general_settings'       => [
                'use_shortcuts'                => 1,
                'dynamic_folders'              => 1,
                'use_folder_undo'              => 1,
                'default_timeout'              => 5,
                'folders_enable_replace_media' => 1,
                'show_media_details'           => 1,
                'media_col_settings'           => self::$mediaItems,
                'use_max_upload_size'          => 1,
                'max_upload_size'              => self::max_upload_file_size(),
                'enable_media_trash'           => 0,
                'folders_media_cleaning'       => 1,
                'replace_media_title'          => 1,
                'new_folder_placement'         => 'top',
                'open_new_folder_by_default'   => 0,
                'force_sorting'                => 0,
                'show_folder_in_settings'      => 0,
                'folders_show_in_menu'         => 0,
            ],
            'customization_settings' => [
                'folder_font'                => '',
                'folder_size'                => 16,
                'new_folder_color'           => '#FA166B',
                'bulk_organize_button_color' => '#FA166B',
                'media_replace_button'       => '#FA166B',
                'dropdown_color'             => '#484848',
                'folder_bg_color'            => '#FA166B',
                'folder_colors'              => self::$defaultColors,
                'default_icon_color'         => '#334155',
                'enable_horizontal_scroll'   => 0,
                'show_in_page'               => 'hide',
                'custom_font_size'           => 16,
            ],
            'user_settings'          => [
                'folders_by_users'               => 0,
                'dynamic_folders_for_admin_only' => 0,
                'folders_by_user_roles'          => 0,
            ],
            'advanced_settings'      => [
                'remove_folders_when_removed' => 0,
            ],
        ];
    }

    /**
     * Get the maximum upload file size in megabytes.
     *
     * @return int Maximum upload size in MB.
     */
    public static function max_upload_file_size() {
        $max_upload_size = wp_max_upload_size();
        return intval( $max_upload_size / 1024 / 1024 );
    }

    /**
     * Migrate settings from legacy options to the new consolidated option.
     *
     * Reads the old `customize_folders` and `folders_user_role_settings` options
     * and transforms their values to the current structure, normalizing boolean-
     * like strings (e.g., 'on'/'off', 'yes'/'no', 'show'/'hide') to integers 1/0.
     *
     * Side effects:
     * - Creates the `premio_folders_settings` option if it does not exist.
     * - Populates the in-memory cache `self::$settings`.
     *
     * @return void
     */
    private static function migrate_legacy_settings() {
        $settings         = get_option( 'customize_folders', [] );
        $default_settings = self::default_settings();
        if ( empty( $settings ) ) {
            add_option( 'premio_folders_settings', $default_settings );
            self::$settings = $default_settings;
        } else {
            update_option( 'old_customize_folders', $settings ) or add_option( 'old_customize_folders', $settings );
            $user_role_settings = get_option( 'folders_user_role_settings' );
            $updated_settings   = [
                'general_settings'       => [
                    'use_shortcuts'                => isset( $settings['use_shortcuts'] ) ? $settings['use_shortcuts'] : 1,
                    'dynamic_folders'              => isset( $settings['dynamic_folders'] ) ? $settings['dynamic_folders'] : 1,
                    'use_folder_undo'              => isset( $settings['use_folder_undo'] ) ? $settings['use_folder_undo'] : 1,
                    'default_timeout'              => isset( $settings['default_timeout'] ) ? $settings['default_timeout'] : 5,
                    'folders_enable_replace_media' => isset( $settings['folders_enable_replace_media'] ) ? $settings['folders_enable_replace_media'] : 1,
                    'show_media_details'           => isset( $settings['show_media_details'] ) ? $settings['show_media_details'] : 1,
                    'media_col_settings'           => isset( $settings['media_col_settings'] ) ? $settings['media_col_settings'] : self::$mediaItems,
                    'use_max_upload_size'          => isset( $settings['use_max_upload_size'] ) ? $settings['use_max_upload_size'] : 0,
                    'max_upload_size'              => isset( $settings['max_upload_size'] ) ? $settings['max_upload_size'] : self::max_upload_file_size(),
                    'enable_media_trash'           => isset( $settings['enable_media_trash'] ) ? $settings['enable_media_trash'] : 0,
                    'folders_media_cleaning'       => isset( $settings['folders_media_cleaning'] ) ? $settings['folders_media_cleaning'] : 1,
                    'replace_media_title'          => isset( $settings['replace_media_title'] ) ? $settings['replace_media_title'] : 1,
                    'force_sorting'                => isset( $settings['force_sorting'] ) ? $settings['force_sorting'] : 0,
                    'show_folder_in_settings'      => isset( $settings['show_folder_in_settings'] ) ? $settings['show_folder_in_settings'] : 0,
                    'folders_show_in_menu'         => isset( $settings['folders_show_in_menu'] ) ? $settings['folders_show_in_menu'] : 0,
                    'open_new_folder_by_default'   => isset( $settings['open_new_folder_by_default'] ) ? $settings['open_new_folder_by_default'] : 0,
                    'new_folder_placement'         => isset( $settings['new_folder_placement'] ) ? $settings['new_folder_placement'] : 'top',
                ],
                'customization_settings' => [
                    'folder_font'                => isset( $settings['folder_font'] ) ? $settings['folder_font'] : '#f39c12',
                    'folder_size'                => isset( $settings['folder_size'] ) ? $settings['folder_size'] : 16,
                    'new_folder_color'           => isset( $settings['new_folder_color'] ) ? $settings['new_folder_color'] : '#FA166B',
                    'bulk_organize_button_color' => isset( $settings['bulk_organize_button_color'] ) ? $settings['bulk_organize_button_color'] : '#FA166B',
                    'media_replace_button'       => isset( $settings['media_replace_button'] ) ? $settings['media_replace_button'] : '#FA166B',
                    'dropdown_color'             => isset( $settings['dropdown_color'] ) ? $settings['dropdown_color'] : '#484848',
                    'folder_bg_color'            => isset( $settings['folder_bg_color'] ) ? $settings['folder_bg_color'] : '#FA166B',
                    'folder_colors'              => isset( $settings['folder_colors'] ) ? $settings['folder_colors'] : self::$defaultColors,
                    'default_icon_color'         => isset( $settings['default_icon_color'] ) ? $settings['default_icon_color'] : '#334155',
                    'enable_horizontal_scroll'   => isset( $settings['enable_horizontal_scroll'] ) ? $settings['enable_horizontal_scroll'] : 0,
                    'custom_font_size'           => isset( $settings['folder_custom_font_size'] ) ? $settings['folder_custom_font_size'] : 16,
                    'show_in_page'               => isset( $settings['show_in_page'] ) ? $settings['show_in_page'] : 0,
                ],
                'user_settings'          => [
                    'folders_by_users'               => isset( $settings['folders_by_users'] ) ? $settings['folders_by_users'] : 0,
                    'dynamic_folders_for_admin_only' => isset( $settings['dynamic_folders_for_admin_only'] ) ? $settings['dynamic_folders_for_admin_only'] : 0,
                    'folders_by_user_roles'          => 'on' === $user_role_settings ? 1 : 0,
                ],
                'advanced_settings'      => [
                    'remove_folders_when_removed' => isset( $settings['remove_folders_when_removed'] ) ? $settings['remove_folders_when_removed'] : 0,
                ],
            ];
            foreach ( $updated_settings as $parent_key => $fields ) {
                foreach ( $fields as $field_key => $value ) {
                    if ( 'on' === $value || 'yes' === $value || 'show' === $value ) {
                        $updated_settings[ $parent_key ][ $field_key ] = 1;
                    } else if ( 'off' === $value || 'no' === $value || 'hide' === $value ) {
                        $updated_settings[ $parent_key ][ $field_key ] = 0;
                    }
                }
            }
            update_option('premio_folders_settings', $updated_settings) or add_option('premio_folders_settings', $updated_settings);
            self::$settings = $updated_settings;
        }
    }

    /**
     * Load settings from the database with in-memory caching and fill in defaults.
     *
     * If the option is missing, it triggers a migration from legacy settings.
     * When the option exists, any missing keys are backfilled from
     * `default_settings()` to ensure a complete structure.
     *
     * @return array<string, array<string, mixed>> Fully resolved settings grouped by sections
     */
    public static function get_settings() {
        if ( ! empty( self::$settings ) ) {
            return self::$settings;
        }
        $settings = get_option( 'premio_folders_settings', [] );
        if ( empty( $settings ) ) {
            self::migrate_legacy_settings();
            return self::$settings;
        } else {
            $old_settings = get_option( 'old_customize_folders', [] );
            $current_settings         = get_option( 'customize_folders', [] );
            if($old_settings !== $current_settings) {
                self::migrate_legacy_settings();
                return self::$settings;
            } else {
                $default_settings = self::default_settings();
                foreach ($default_settings as $parent_key => $fields) {
                    foreach ($fields as $field_key => $value) {
                        if (!isset($settings[$parent_key][$field_key])) {
                            $settings[$parent_key][$field_key] = $value;
                        }
                    }
                }
                self::$settings = $settings;
            }
        }
        return self::$settings;
    }

    /**
     * Clear the in-memory settings cache so the next read reloads from the database.
     *
     * @return void
     */
    public static function reset_settings()
    {
        self::$settings = [];
    }

    /**
     * Retrieve settings for a specific field, an entire section, or all settings.
     *
     * Usage:
     * - No params: returns the fully resolved settings array.
     * - Only `$section`: returns that section's array if it exists.
     * - `$section` and `$field_name`: returns the specific field value or `null`.
     *
     * @param string $section    Section key (e.g., 'general_settings'). Optional.
     * @param string $field_name Specific field key within a section. Optional.
     *
     * @return mixed|null Array, scalar value, or null when the requested key is missing.
     */
    public static function get_field_settings( $section = '', $field_name = '' ) {
        $settings = self::get_settings();
        if ( empty( $field_name ) && empty( $section ) ) {
            return self::get_settings();
        } else if ( empty( $field_name ) && isset( $settings[ $section ] ) ) {
            return $settings[ $section ];
        } else if ( isset( $settings[ $section ][ $field_name ] ) ) {
            return $settings[ $section ][ $field_name ];
        }
        return null;
    }

    /**
     * Save the email notification settings.
     *
     * Verifies the `save_folders_notifications_settings` nonce, sanitizes the
     * submitted settings deeply and stores them in `folders_notification_settings`.
     *
     * @param array $params Request parameters with `nonce` and `notification_setting`.
     * @return array|\WP_Error Success array, or WP_Error on invalid nonce or empty settings.
     */
    public static function save_notifications( $params ) {
        $nonce      = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $settings   = isset( $params['notification_setting'] ) ? map_deep( $params['notification_setting'], 'sanitize_text_field' ) : [];


        if ( empty($nonce) || ! wp_verify_nonce( $nonce, 'save_folders_notifications_settings' ) ) {
            return new \WP_Error( 'invalid_nonce', 'Invalid nonce provided', array( 'status' => 403 ) );
        }

        if ( empty( $settings ) ) {
            return new \WP_Error( 'invalid_request', 'Invalid request!!', array( 'status' => 403 ) );
        }

        update_option('folders_notification_settings', $settings);

        return array(
            'success' => true,
            'message' => 'Settings saved successfully',
        );
    }

    /**
     * Save the general settings tab.
     *
     * Verifies the nonce, sanitizes input arrays deeply and updates the
     * `premio_folders_settings`, `folders_settings` (enabled post types) and
     * `default_folders` options.
     *
     * @param array $params Request parameters with `nonce`, `general_settings`,
     *                      `folders_settings` and `default_folders`.
     * @return array|\WP_Error Success array with `redirect_url`, or WP_Error on failure.
     */
    public static function save_general_settings( $params ) {
        $nonce            = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $general_settings = isset( $params['general_settings'] ) ? map_deep( $params['general_settings'], 'sanitize_text_field' ) : [];
        $folders_settings = isset( $params['folders_settings'] ) ? map_deep( $params['folders_settings'], 'sanitize_text_field' ) : [];
        $default_folders  = isset( $params['default_folders'] ) ? map_deep( $params['default_folders'], 'sanitize_text_field' ) : [];

        if ( ! wp_verify_nonce( $nonce, 'save_folders_general_settings' ) ) {
            return new \WP_Error( 'invalid_nonce', 'Invalid nonce provided', array( 'status' => 403 ) );
        }

        if ( empty( $general_settings ) ) {
            return new \WP_Error( 'invalid_request', 'Invalid request!!', array( 'status' => 403 ) );
        }

        $current_settings                     = self::get_settings();
        $current_settings['general_settings'] = $general_settings;
        update_option( 'premio_folders_settings', $current_settings );
        update_option( 'folders_settings', $folders_settings );
        update_option( 'default_folders', $default_folders );
        self::$settings = $current_settings;
        $redirect_url = self::get_setting_page_url();
        return array(
            'success' => true,
            'message' => 'Settings saved successfully',
            'redirect_url' => $redirect_url,
        );
    }

    /**
     * Toggle the "remove folders data when the plugin is removed" setting.
     *
     * @param array $params Request parameters with `nonce` and `status` (truthy to enable).
     * @return array|\WP_Error Result array with `success` and `message`, or WP_Error on invalid nonce.
     */
    public static function remove_folders_data( $params ) {
        $nonce      = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $status     = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';
        $status     = ($status)?1:0;

        if ( empty($nonce) || !wp_verify_nonce( $nonce, 'remove-plugin-data-on-deactivate' ) ) {
            return new \WP_Error( 'invalid_request', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $settings = self::get_settings();
        if(!empty($settings) && is_array($settings)) {
            $settings['advanced_settings']['remove_folders_when_removed'] = $status;
            update_option( 'premio_folders_settings', $settings );
            self::reset_settings();
            return array(
                'success' => true,
                'message' => esc_html__('Settings saved successfully', 'folders'),
            );
        }
        return array(
            'success' => false,
            'message' => esc_html__('Something went wrong, please try again', 'folders')
        );
    }

    /**
     * Save the folder customization settings (colors, fonts, sizes and so on).
     *
     * Verifies the nonce, sanitizes the submitted values deeply and stores them in
     * the `customization_settings` section of `premio_folders_settings`.
     *
     * @param array $params Request parameters with `nonce` and `customization_settings`.
     * @return array|\WP_Error Success array with `redirect_url`, or WP_Error on failure.
     */
    public static function save_folders_customization( $params ) {
        $nonce            = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $customization_settings = isset( $params['customization_settings'] ) ? map_deep( $params['customization_settings'], 'sanitize_text_field' ) : [];

        if ( ! wp_verify_nonce( $nonce, 'save_folders_customization' ) ) {
            return new \WP_Error( 'invalid_nonce', 'Invalid nonce provided', array( 'status' => 403 ) );
        }

        if ( empty( $customization_settings ) ) {
            return new \WP_Error( 'invalid_request', 'Invalid request!!', array( 'status' => 403 ) );
        }


        $current_settings                           = self::get_settings();
        $current_settings['customization_settings'] = $customization_settings;
        self::$settings = $current_settings;
        update_option( 'premio_folders_settings', $current_settings );
        $redirect_url = self::get_setting_page_url()."&tab=customize-folders";
        return array(
            'success' => true,
            'message' => 'Settings saved successfully',
            'redirect_url' => $redirect_url,
        );
    }

    /**
     * Hide the Recommended Plugins admin page permanently.
     *
     * @param array $params Request parameters with `nonce`.
     * @return array|\WP_Error Success array with `redirect_url`, or WP_Error on invalid nonce.
     */
    public static function hide_recommended_plugin_page( $params )
    {
        $nonce = isset($params['nonce']) ? sanitize_text_field($params['nonce']) : '';

        if (!wp_verify_nonce($nonce, 'hide_recommended_plugin_page_nonce')) {
            return new \WP_Error('invalid_nonce', 'Invalid nonce provided', array('status' => 403));
        }

        update_option('hide_folder_recommended_plugin', "1");
        return array(
            'success' => true,
            'message' => 'Settings updated successfully',
            'redirect_url' => self::get_setting_page_url()
        );
    }

    /**
     * Get the URL of the Folders settings page.
     *
     * The page lives under Settings or as a top-level menu depending on the
     * "show folder in settings" option.
     *
     * @return string Admin URL of the settings page.
     */
    public static function get_setting_page_url()
    {
        $page_settings = self::get_field_settings('general_settings', 'show_folder_in_settings');
        if ( ! empty( $page_settings ) ) {
            return admin_url('options-general.php?page=premio-folders-settings');
        } else {
            return admin_url('admin.php?page=wcp_folders_settings');
        }
    }
}
