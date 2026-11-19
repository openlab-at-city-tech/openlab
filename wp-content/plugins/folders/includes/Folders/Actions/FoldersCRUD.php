<?php

namespace Folders\Folders\Actions;

/**
 * Folder create, read, update and delete operations.
 *
 * Handles the folder actions triggered from the folder sidebar: creating
 * folders and subfolders, renaming, deleting, locking, pinning (sticky),
 * starring, coloring and setting the default folder. Per-folder flags are
 * stored in the `folder_info` term meta array (`is_locked`, `is_sticky`,
 * `is_high` for starred, `is_default`, `is_active` for expanded, `has_color`).
 */
class FoldersCRUD
{

    /**
     * Lock or unlock several folders at once.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string $nonce      Nonce for the `folder_nonce_{type}` action.
     *     @type string $type       Post type the folders belong to.
     *     @type array  $folder_ids IDs of the folders to update. Non-numeric IDs are skipped.
     *     @type mixed  $status     Truthy to lock, falsy to unlock.
     * }
     * @return array|\WP_Error Result array with the `folders` and new `status`, or WP_Error on invalid request.
     */
    public static function lock_unlock_all_folders($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folders     = isset( $params['folder_ids'] ) ? rest_sanitize_array( $params['folder_ids'] ) : [];

        if (empty($folders) || empty($nonce) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$type ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_manage_folders()) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';
        $status = $status ? 1 : 0;

        foreach ( $folders as $folder_id ) {
            if(!is_numeric($folder_id)) {
                continue;
            }
            $folder_info = get_term_meta($folder_id, "folder_info", true);

            if ($folder_info) {
                $folder_info['is_locked'] = $status;
                update_term_meta($folder_id, "folder_info", $folder_info);
            } else {
                $folder_info = [];
                $folder_info['is_locked'] = $status;
                add_term_meta($folder_id, "folder_info", $folder_info);
            }
        }

        return array(
            'success'    => true,
            'message'    => esc_html__('Folders updated successfully', 'folders'),
            'folders'    => $folders,
            'status'     => $status
        );
    }

    /**
     * Set a folder's color.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce     Nonce for the `folder_nonce_{folder_id}` action.
     *     @type string     $type      Post type the folder belongs to.
     *     @type int|string $folder_id Folder term ID.
     *     @type string     $color     Color value; empty string removes the color.
     * }
     * @return array|\WP_Error Result array with `has_color`, or WP_Error on invalid request.
     */
    public static function update_folder_color($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || empty($nonce) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$folder_id ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_manage_folders()) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_info = get_term_meta($folder_id, "folder_info", true);

        $color   = isset( $params['color'] ) ? sanitize_text_field( $params['color'] ) : '';

        if ($folder_info) {
            $folder_info['has_color'] = $color;
            update_term_meta($folder_id, "folder_info", $folder_info);
        } else {
            $folder_info = [];
            $folder_info['has_color'] = $color;
            add_term_meta($folder_id, "folder_info", $folder_info);
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id,
            'has_color'     => $color
        );
    }

    /**
     * Delete every folder for every post type and reset the plugin settings.
     *
     * Removes all folder terms (media, page, post and custom post type folder
     * taxonomies), deletes the plugin's options and restores the default list of
     * folder-enabled post types (pages, posts and media).
     *
     * @param array $params Request parameters with `nonce` for the
     *                      `delete-folders-plugin-data-manually` action.
     * @return array|\WP_Error Result array with `redirect_url`, or WP_Error on invalid nonce.
     */
    public static function delete_all_folder($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';

        if (empty($nonce) || ! wp_verify_nonce( $nonce, 'delete-folders-plugin-data-manually' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        self::remove_all_data();

        add_option('folders_settings', ['page', 'post', 'attachment']);

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders deleted successfully', 'folders'),
            'redirect_url'  => admin_url('admin.php?page=wcp_folders_settings')
        );
    }

    /**
     * Delete every folder for every post type and the plugin's settings.
     *
     * Does no permission checks: callers are responsible for them. Used by the
     * "delete all data" tool and by `uninstall.php`, so it must not depend on
     * any other plugin class.
     *
     * @return void
     */
    public static function remove_all_data()
    {
        self::remove_folder_by_taxonomy("media_folder");
        self::remove_folder_by_taxonomy("folder");
        self::remove_folder_by_taxonomy("post_folder");

        $post_array = [
            "page",
            "post",
            "attachment",
        ];

        // Registered post types, plus the post types folders were enabled for
        // (their post type may not be registered right now).
        $post_types = array_keys(get_post_types([], 'objects'));
        $enabled    = get_option('folders_settings');
        if (is_array($enabled)) {
            foreach ($enabled as $post_type) {
                if (is_string($post_type) && $post_type !== '') {
                    $post_types[] = $post_type;
                }
            }
        }

        foreach (array_unique($post_types) as $post_type) {
            if (!in_array($post_type, $post_array)) {
                self::remove_folder_by_taxonomy($post_type . '_folder');
            }
        }

        delete_option('folders_settings');
        delete_option('default_folders');
        delete_option('customize_folders');
        delete_option('premio_folders_settings');
        delete_option('hide_folders_media_cleaning_menu');
        delete_option('hide_folder_recommended_plugin');
        delete_transient('premio_folders_without_trash');
    }

    /**
     * Permanently delete all terms of a folder taxonomy, including their
     * relationships and term meta.
     *
     * @param string $taxonomy Folder taxonomy name (for example `media_folder`).
     * @return void
     */
    private static function remove_folder_by_taxonomy($taxonomy)
    {
        global $wpdb;
        $query   = "SELECT * FROM ".$wpdb->term_taxonomy."
                LEFT JOIN  ".$wpdb->terms."
                ON  ".$wpdb->term_taxonomy.".term_id =  ".$wpdb->terms.".term_id
                WHERE ".$wpdb->term_taxonomy.".taxonomy = '%s'
                ORDER BY parent ASC";
        $query   = $wpdb->prepare($query, $taxonomy);
        $folders = $wpdb->get_results($query);
        $folders = array_values($folders);
        $removed = [];
        foreach ($folders as $folder) {
            $term_id          = intval($folder->term_id);
            $term_taxonomy_id = intval($folder->term_taxonomy_id);
            if ($term_id && $term_taxonomy_id) {
                // term_relationships is keyed by term_taxonomy_id, which is not always equal to term_id.
                $wpdb->delete($wpdb->term_relationships, ['term_taxonomy_id' => $term_taxonomy_id]);
                $wpdb->delete($wpdb->term_taxonomy, ['term_taxonomy_id' => $term_taxonomy_id]);

                // Only remove the term itself when no other taxonomy still uses it.
                $still_used = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE term_id = %d", $term_id));
                if (!$still_used) {
                    $wpdb->delete($wpdb->terms, ['term_id' => $term_id]);
                    $wpdb->delete($wpdb->termmeta, ['term_id' => $term_id]);
                }
                $removed[] = $term_id;
            }
        }

        if (!empty($removed)) {
            clean_term_cache($removed, $taxonomy);
        }
    }

    /**
     * Delete several folders and all of their subfolders.
     *
     * Posts inside the folders are not deleted, only unassigned. Requires the
     * `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string $nonce     Nonce for the `premio_folders_delete_multiple_folders` action.
     *     @type string $type      Post type the folders belong to.
     *     @type string $folder_id Comma-separated folder IDs.
     * }
     * @return array|\WP_Error Result array with the deleted `folder_ids`, or WP_Error on invalid request.
     */
    public static function delete_multiple_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || empty($nonce) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'premio_folders_delete_multiple_folders' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);

        $folder_ids = explode(",", $folder_id);

        foreach ($folder_ids as $folder_id) {
            $terms = get_terms(
                [
                    'taxonomy' => $folder_type,
                    'hide_empty' => false,
                    'parent' => $folder_id,
                ]
            );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                foreach ($terms as $term) {
                    self::remove_folder_child_items($term->term_id, $folder_type);
                }

                wp_delete_term($folder_id, $folder_type);
            } else {
                wp_delete_term($folder_id, $folder_type);
            }
        }

        delete_transient("premio_folders_without_trash");

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders deleted successfully', 'folders'),
            'folder_ids'     => $folder_ids
        );
    }

    /**
     * Delete a folder and all of its subfolders.
     *
     * Posts inside the folder are not deleted, only unassigned. Requires the
     * `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce     Nonce for the `premio_folders_delete_folder` action.
     *     @type string     $type      Post type the folder belongs to.
     *     @type int|string $folder_id Folder term ID.
     * }
     * @return array|\WP_Error Result array with `folder_id`, or WP_Error on invalid request.
     */
    public static function delete_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || !is_numeric($folder_id) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'premio_folders_delete_folder' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);

        $terms = get_terms(
            [
                'taxonomy' => $folder_type,
                'hide_empty' => false,
                'parent' => $folder_id,
            ]
        );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ($terms as $term) {
                self::remove_folder_child_items($term->term_id, $folder_type);
            }

            wp_delete_term($folder_id, $folder_type);
        } else {
            wp_delete_term($folder_id, $folder_type);
        }

        delete_transient("premio_folders_without_trash");

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders deleted successfully', 'folders'),
            'folder_id'     => $folder_id
        );
    }

    /**
     * Recursively delete a folder term and all of its descendants.
     *
     * @param int    $term_id   Folder term ID to delete.
     * @param string $post_type Folder taxonomy name (despite the parameter name).
     * @return void
     */
    public static function remove_folder_child_items($term_id, $post_type)
    {
        $terms = get_terms(
            [
                'taxonomy' => $post_type,
                'hide_empty' => false,
                'parent' => $term_id,
            ]
        );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ($terms as $term) {
                self::remove_folder_child_items($term->term_id, $post_type);
            }

            wp_delete_term($term_id, $post_type);
        } else {
            wp_delete_term($term_id, $post_type);
        }

    }//end remove_folder_child_items()

    /**
     * Create one or more subfolders under an existing folder.
     *
     * Names longer than 50 characters or already used under the same parent are
     * skipped. The parent folder is marked as expanded. Requires the
     * `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce       Nonce for the `premio_folders_add_sub_folder` action.
     *     @type string     $type        Post type the folders belong to.
     *     @type int|string $parent_id   Parent folder term ID.
     *     @type array      $folder_name Names of the subfolders to create.
     * }
     * @return array|\WP_Error Result array with the created `folders`, or WP_Error on invalid request.
     */
    public static function add_child_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $parent_id   = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;
        $folders     = isset( $params['folder_name'] ) ? rest_sanitize_array( $params['folder_name'] ) : [];

        if ( ! wp_verify_nonce( $nonce, 'premio_folders_add_sub_folder' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folders)) {
            return new \WP_Error( 'error', 'Please enter folder name', array( 'status' => 403 ) );
        }
        if (empty($type) || empty($parent_id) || !is_numeric($parent_id)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $user_id     = get_current_user_id();
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);
        $records = [];
        foreach ($folders as $folder) {

            $folder = trim($folder);

            if(strlen(trim($folder)) > 50) {
                $message = esc_html__('Please keep folder names to 50 characters or fewer.', 'folders');
                continue;
            } else if(term_exists($folder, $folder_type, $parent_id)) {
                $message = esc_html__('Folder already exists', 'folders');
                continue;
            }

            $slug = self::create_slug_from_string($folder) . "-" . time() . "-" . $user_id;

            $result = wp_insert_term(
                urldecode($folder),
                $folder_type,
                [
                    'parent' => $parent_id,
                    'slug' => $slug,
                ]
            );

            if (!empty($result)) {
                $order = 0;
                $term = get_term($result['term_id'], $folder_type);
                $term_nonce = wp_create_nonce('folder_nonce_' . $term->term_id);
                add_term_meta($result['term_id'], "created_by", $user_id);
                add_term_meta($result['term_id'], "wcp_custom_order", $order);

                $folder_item = [];
                $folder_item['slug'] = $term->slug;
                $folder_item['nonce'] = $term_nonce;
                $folder_item['term_id'] = $result['term_id'];
                $folder_item['title'] = $folder;
                $folder_item['parent_id'] = $term->parent;
                $folder_item['is_sticky'] = 0;
                $folder_item['is_high'] = 0;
                $folder_item['is_locked'] = 0;
                $folder_item['is_default'] = 0;
                $folder_item['has_color'] = '';
                $folder_item['folder_count'] = 0;

                if ($type == "attachment" || $type == "media") {
                    $url = admin_url("upload.php?post_type=attachment&media_folder=" . $slug);
                } else if ($type == "plugin") {
                    $url = admin_url("plugins.php?folders4plugins_folder=" . $slug);
                } else {
                    $url = admin_url("edit.php?post_type=" . $type . "&" . $folder_type . "=" . $slug);
                }

                if ($parent_id != 0) {
                    $folder_info = get_term_meta($parent_id, "folder_info", true);
                    $folder_info = !is_array($folder_info) ? [] : $folder_info;
                    $folder_info['is_active'] = 1;
                    update_term_meta($parent_id, "folder_info", $folder_info);
                }

                $records[] = $folder_item;
            }
        }

        if(!empty($records)) {
            return array(
                'success' => true,
                'message' => esc_html__('Sub folders created successfully', 'folders'),
                'parent_id' => empty($parent_id) ? "#" : $parent_id,
                'folders' => $records,
            );
        }

        return array(
            'success'       => false,
            'message'       => !empty($message)? $message : esc_html__('Error during creating folder', 'folders'),
        );
    }

    /**
     * Validate a duplicate-folder request.
     *
     * Not implemented: this method only validates the request and returns
     * nothing. Duplication is handled by
     * {@see \Folders\Folders\Actions\DuplicateFolder::duplicate_folder()}.
     *
     * @param array $params Request parameters with `nonce`, `type`, `folder_name` and `parent_id`.
     * @return \WP_Error|null WP_Error on invalid request, otherwise null.
     */
    public static function duplicate_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_name = isset( $params['folder_name'] ) ? rest_sanitize_array( $params['folder_name'] ) : [];
        if ( ! wp_verify_nonce( $nonce, 'premio_folders_duplicate_folder' ) ) {
            return new \WP_Error( 'error', 'Invalid nonce provided', array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folder_name)) {
            return new \WP_Error( 'error', 'Please enter folder name', array( 'status' => 403 ) );
        }
        if (empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $parent_id   = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);
        $user_id     = get_current_user_id();
        $folderCount = 0;
        $records     = array();
        $order       = 0;
    }

    /**
     * Toggle a folder as the default folder for its post type.
     *
     * The default folder is the one opened when the post type's list screen
     * loads. The previous default folder is un-flagged and the slug is saved in
     * the `default_folders` option. Requires the `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce     Nonce for the `folder_nonce_{folder_id}` action.
     *     @type string     $type      Post type the folder belongs to.
     *     @type string     $slug      Folder slug.
     *     @type int|string $folder_id Folder term ID.
     * }
     * @return array|\WP_Error Result array with the new `is_default` value, or WP_Error on invalid request.
     */
    public static function default_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type   = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $slug        = isset( $params['slug'] ) ? sanitize_text_field( $params['slug'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || !is_numeric($folder_id) || empty($post_type) || empty($slug)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$folder_id ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $default_folders = get_option('default_folders', false);
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        if(isset($default_folders[$post_type])) {
            $default_folder = $default_folders[$post_type];

            if($default_folder !== $slug) {
                $term = get_term_by('slug', $default_folder, $folder_type);
                if(isset($term->term_id) && !empty($term->term_id)) {
                    $folder_info = get_term_meta($term->term_id, "folder_info", true);
                    if ($folder_info) {
                        $folder_info['is_default'] = 0;
                        update_term_meta($term->term_id, "folder_info", $folder_info);
                    }
                }
            }
        }

        if ($default_folders === false) {
            $default_folders = [];
            $default_folders[$post_type] = $slug;
            add_option("default_folders", $default_folders);
        } else {
            if (!is_array($default_folders)) {
                $default_folders = [];
            }

            $default_folders[$post_type] = $slug;
            update_option("default_folders", $default_folders);
        }

        $folder_info = get_term_meta($folder_id, "folder_info", true);
        $status = intval(isset($folder_info['is_default']) ? $folder_info['is_default'] : 0);
        $status = ($status) ? 0 : 1;

        if ($folder_info) {
            $folder_info['is_default'] = $status;
            update_term_meta($folder_id, "folder_info", $folder_info);
        } else {
            $folder_info = [];
            $folder_info['is_default'] = $status;
            add_term_meta($folder_id, "folder_info", $folder_info);
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id,
            'is_default'    => $status
        );
    }

    /**
     * Toggle a folder's starred (favorite) state, stored as `is_high`.
     *
     * Requires the `manage_categories` capability.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{folder_id}`),
     *                      `type` and `folder_id`.
     * @return array|\WP_Error Result array with the new `is_high` value, or WP_Error on invalid request.
     */
    public static function add_remove_star($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || !is_numeric($folder_id) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$folder_id ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_info = get_term_meta($folder_id, "folder_info", true);

        $status = intval(isset($folder_info['is_high']) ? $folder_info['is_high'] : 0);
        $status = ($status) ? 0 : 1;

        if ($folder_info) {
            $folder_info['is_high'] = $status;
            update_term_meta($folder_id, "folder_info", $folder_info);
        } else {
            $folder_info = [];
            $folder_info['is_high'] = $status;
            add_term_meta($folder_id, "folder_info", $folder_info);
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id,
            'is_high'       => $status
        );
    }

    /**
     * Toggle a folder's locked state. Locked folders cannot be moved or edited
     * from the sidebar.
     *
     * Requires the `manage_categories` capability.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{folder_id}`),
     *                      `type` and `folder_id`.
     * @return array|\WP_Error Result array with the new `is_locked` value, or WP_Error on invalid request.
     */
    public static function lock_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || !is_numeric($folder_id) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$folder_id ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_info = get_term_meta($folder_id, "folder_info", true);

        $status = intval(isset($folder_info['is_locked']) ? $folder_info['is_locked'] : 0);
        $status = ($status) ? 0 : 1;

        if ($folder_info) {
            $folder_info['is_locked'] = $status;
            update_term_meta($folder_id, "folder_info", $folder_info);
        } else {
            $folder_info = [];
            $folder_info['is_locked'] = $status;
            add_term_meta($folder_id, "folder_info", $folder_info);
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id,
            'is_locked'     => $status
        );
    }
    /**
     * Toggle a folder's sticky (pinned) state.
     *
     * Requires the `manage_categories` capability.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{folder_id}`),
     *                      `type` and `folder_id`.
     * @return array|\WP_Error Result array with the new `is_sticky` value, or WP_Error on invalid request.
     */
    public static function toggle_sticky_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';

        if (empty($folder_id) || !is_numeric($folder_id) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$folder_id ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('You have not permission to rename folder', 'folders'), array( 'status' => 403 ) );
        }

        $folder_info = get_term_meta($folder_id, "folder_info", true);

        $status = intval(isset($folder_info['is_sticky']) ? $folder_info['is_sticky'] : 0);
        $status = ($status) ? 0 : 1;

        if ($folder_info) {
            $folder_info['is_sticky'] = $status;
            update_term_meta($folder_id, "folder_info", $folder_info);
        } else {
            $folder_info = [];
            $folder_info['is_sticky'] = $status;
            add_term_meta($folder_id, "folder_info", $folder_info);
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id,
            'is_sticky'     => $status
        );
    }
    /**
     * Rename a folder.
     *
     * Rejects empty names, names over 50 characters and names already used by
     * another folder with the same parent. Requires the `manage_categories`
     * capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce       Nonce for the `premio_folders_rename_folder` action.
     *     @type string     $type        Post type the folder belongs to.
     *     @type int|string $folder_id   Folder term ID.
     *     @type string     $folder_name New folder name.
     *     @type int|string $parent_id   Parent folder ID; "#" or 0 for root.
     * }
     * @return array|\WP_Error Result array with `folder_name`, or WP_Error on invalid request.
     */
    public static function rename_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $folder_name = isset( $params['folder_name'] ) ? sanitize_text_field( $params['folder_name'] ) : '';
        $parent_id = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;

        if ( ! wp_verify_nonce( $nonce, 'premio_folders_rename_folder' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('You have not permission to rename folder', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folder_name) || !is_string($folder_name)) {
            return new \WP_Error( 'error', esc_html__('Please enter folder name', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folder_name) || !is_string($folder_name)) {
            return new \WP_Error( 'error', esc_html__('Please enter folder name', 'folders'), array( 'status' => 403 ) );
        }
        if(strlen(trim($folder_name)) > 50) {
            return new \WP_Error( 'error', esc_html__('Please keep folder names to 50 characters or fewer.', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folder_id) || !is_numeric($folder_id) || empty($type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);
        $parent_id = ($parent_id == '#') ? 0 : $parent_id;
        $term = term_exists($folder_name, $folder_type, $parent_id);
        if(isset($term['term_id']) && $term['term_id'] != $folder_id) {
            return new \WP_Error( 'error', esc_html__('Folder already exists', 'folders'), array( 'status' => 403 ) );
        }
        $result = wp_update_term(
            $folder_id,
            $folder_type,
            ['name' => $folder_name]
        );
        if (!empty($result)) {
            return array(
                'success'       => true,
                'message'       => esc_html__('Folders renamed successfully', 'folders'),
                'folder_id'     => $folder_id,
                'folder_name'   => $folder_name
            );
        } else {
            return array(
                'success'       => false,
                'message'       => esc_html__('Something went wrong, please try again', 'folders')
            );
        }
    }
    /**
     * Create one or more folders.
     *
     * Names longer than 50 characters or already used under the same parent are
     * skipped. New folders are placed at the top or bottom of their siblings
     * according to the "new folder placement" setting, and the parent folder is
     * marked as expanded. Requires the `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce       Nonce for the `prm_folders_add_folder` action.
     *     @type string     $type        Post type the folders belong to.
     *     @type array      $folder_name Names of the folders to create.
     *     @type int|string $parent_id   Optional. Parent folder ID; 0 for root.
     *     @type string     $siblings    Optional. Comma-separated IDs of existing sibling folders.
     * }
     * @return array|\WP_Error Result array with the created `folders`, or WP_Error on invalid request.
     */
    public static function add_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $siblings    = isset( $params['siblings'] ) ? sanitize_text_field( $params['siblings'] ) : '';
        $folders     = isset( $params['folder_name'] ) ? rest_sanitize_array( $params['folder_name'] ) : [];
        if ( ! wp_verify_nonce( $nonce, 'prm_folders_add_folder' ) ) {
            return new \WP_Error( 'error', 'Invalid nonce provided', array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', 'You have not permission to add folder', array( 'status' => 403 ) );
        }
        if (empty($folders) || !is_array($folders)) {
            return new \WP_Error( 'error', 'Please enter folder name', array( 'status' => 403 ) );
        }
        if (empty($type)) {
            return new \WP_Error( 'error', 'Your request is not valid', array( 'status' => 403 ) );
        }

        $parent_id   = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);
        $user_id     = get_current_user_id();
        $folderCount = 0;
        $records     = array();
        $order       = 0;
        $is_new_folder_default = 0;
        $last_added_id = 0;
        if (empty($folders) || !is_array($folders)){
            return array(
                'success'       => false,
                'message'       => !empty($message)? $message : esc_html__('Error during creating folder', 'folders'),
            );
        }
        $siblings = explode(",", $siblings);
        $siblingCount = count($siblings);
        $show_on_top = \Folders\Admin\Settings::get_field_settings('general_settings', 'new_folder_placement') == 'top';
        $is_new_folder_default = \Folders\Admin\Settings::get_field_settings('general_settings', 'open_new_folder_by_default') == 1;
        if($show_on_top) {
            $siblingCount = 0;
        }

        foreach ($folders as $folder_name) {
            if(strlen(trim($folder_name)) > 50) {
                $message = esc_html__('Please keep folder names to 50 characters or fewer.', 'folders');
                continue;
            } else if(term_exists($folder_name, $folder_type, $parent_id)) {
                $message = esc_html__('Folder already exists', 'folders');
                continue;
            }
            $slug = self::create_slug_from_string($folder_name) . "-" . time() . "-" . $user_id;

            $result = wp_insert_term(
                urldecode($folder_name),
                $folder_type,
                [
                    'parent' => $parent_id,
                    'slug' => $slug,
                ]
            );

            if (!empty($result)) {
                $last_added_id = $result["term_id"];
                $folderCount++;
                $term = get_term($result['term_id'], $folder_type);
                $term_nonce = wp_create_nonce('folder_nonce_' . $term->term_id);
                add_term_meta($result['term_id'], "created_by", $user_id);
                add_term_meta($result['term_id'], "wcp_custom_order", $siblingCount);

                $folder_item = [];
                $folder_item['slug'] = $term->slug;
                $folder_item['nonce'] = $term_nonce;
                $folder_item['term_id'] = $result['term_id'];
                $folder_item['title'] = $folder_name;
                $folder_item['parent_id'] = $term->parent;
                $folder_item['is_sticky'] = 0;
                $folder_item['is_high'] = 0;
                $folder_item['is_locked'] = 0;
                $folder_item['is_default'] = 0;
                $folder_item['has_color'] = '';
                $folder_item['folder_count'] = 0;
                $folder_item['position'] = $siblingCount;
                $siblingCount++;

                if ($type == "attachment" || $type == "media") {
                    $url = admin_url("upload.php?post_type=attachment&media_folder=" . $slug);
                } else if ($type == "plugin") {
                    $url = admin_url("plugins.php?folders4plugins_folder=" . $slug);
                } else {
                    $url = admin_url("edit.php?post_type=" . $type . "&" . $folder_type . "=" . $slug);
                }

                if ($parent_id != 0) {
                    $folder_info = get_term_meta($parent_id, "folder_info", true);
                    $folder_info = !is_array($folder_info) ? [] : $folder_info;
                    $folder_info['is_active'] = 1;
                    update_term_meta($parent_id, "folder_info", $folder_info);
                }

                $records[] = $folder_item;
            }
        }

        if($show_on_top && !empty($siblings)) {
            foreach ($siblings as $sibling) {
                update_term_meta(intval($sibling), "wcp_custom_order", $siblingCount);
                $siblingCount++;
            }
        }


        if (!empty($records)) {
            return array(
                'success'       => true,
                'message'       => esc_html__('Folders created successfully', 'folders'),
                'folders_count' => $folderCount,
                'folders'       => $records,
                'parent_id'     => empty($parent_id) ? "#" : $parent_id,
                'is_new_folder_default' => $is_new_folder_default,
                'folder_id'    => $last_added_id,
            );
        }
        return array(
            'success'       => false,
            'message'       => !empty($message)? $message : esc_html__('Error during creating folder', 'folders'),
        );
    }

    /**
     * Convert a folder name into a URL-friendly slug.
     *
     * Transliterates accented Latin characters to ASCII, removes any other
     * characters except letters, digits, spaces and hyphens, collapses spaces
     * and hyphens into single hyphens and lowercases the result.
     *
     * @param string $str Folder name.
     * @return string Slug.
     */
    public static function create_slug_from_string($str)
    {
        $a = [
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?',
        ];
        $b = [
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o',
        ];
        return strtolower(preg_replace(['/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'], ['', '-', ''], str_replace($a, $b, $str)));

    }//end create_slug_from_string()
}