<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Duplicates a folder, optionally duplicating the posts inside it.
 */
class DuplicateFolder {

    /**
     * Constructor. No hooks are registered; the class is used statically.
     */
    public function __construct() {

    }

    /**
     * Create a duplicate of a folder under the given parent.
     *
     * Copies the source folder's display flags to the new folder. When
     * `duplicate_data` is set, the posts in the source folder are duplicated as
     * drafts into the new folder; otherwise the existing posts are also assigned
     * to the new folder. Requires the `manage_categories` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce          Nonce for the `premio_folders_duplicate_folder` action.
     *     @type string     $type           Post type the folder belongs to.
     *     @type string     $folder_name    Name for the new folder.
     *     @type int|string $folder_id      ID of the folder to duplicate.
     *     @type int|string $parent_id      Optional. Parent folder ID for the copy; 0 for root.
     *     @type string     $siblings       Optional. Comma-separated IDs of sibling folders.
     *     @type int|string $duplicate_data Optional. Truthy to duplicate the posts, not just reassign them.
     * }
     * @return array|\WP_Error Result array with the new `folder_item`, or WP_Error on invalid request.
     */
    public static function duplicate_folder($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type   = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : '';
        $folder_name = isset( $params['folder_name'] ) ? sanitize_text_field( $params['folder_name'] ) : '';
        $parent_id   = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;
        $folder_id   = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : 0;
        $siblings    = isset( $params['siblings'] ) ? sanitize_text_field( $params['siblings'] ) : 0;

        if ( empty($nonce) || ! wp_verify_nonce( $nonce, 'premio_folders_duplicate_folder' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (empty($folder_name)) {
            return new \WP_Error( 'error', 'Please enter folder name', array( 'status' => 403 ) );
        }
        if (empty($post_type) || empty($folder_id)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!current_user_can("manage_categories")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $user_id     = get_current_user_id();
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        $slug = FoldersCRUD::create_slug_from_string($folder_name) . "-" . time() . "-" . $user_id;

        if(term_exists($folder_name, $folder_type, $parent_id)) {
            return array(
                'success'       => false,
                'message'       => esc_html__('Folder name already exists', 'folders'),
            );
        }

        $result = wp_insert_term(
            urldecode($folder_name),
            // the term
            $folder_type,
            // the taxonomy
            [
                'parent' => $parent_id,
                'slug' => $slug,
            ]
        );

        if (empty($result)) {
            return array(
                'success'       => false,
                'message'       => !empty($message)? $message : esc_html__('Error during creating folder', 'folders'),
            );
        }

        $show_on_top = \Folders\Admin\Settings::get_field_settings('general_settings', 'new_folder_placement') == 'top';
        if($show_on_top || empty($siblings)) {
            $order       = 0;
        } else {
            $siblings = explode(",", $siblings);
            $order = count($siblings) + 1;
        }

        $term = get_term($result['term_id'], $folder_type);
        $term_nonce = wp_create_nonce('folder_nonce_' . $term->term_id);
        add_term_meta($result['term_id'], "created_by", $user_id);
        add_term_meta($result['term_id'], "wcp_custom_order", $order);
        $order++;

        $folder_item = [];
        $folder_item['slug'] = $term->slug;
        $folder_item['nonce'] = $term_nonce;
        $folder_item['term_id'] = $result['term_id'];
        $folder_item['title'] = $folder_name;
        $folder_item['parent_id'] = $term->parent;
        $folder_item['is_default'] = 0;
        $folder_item['folder_count'] = 0;
        $folder_item['order'] = $order;

        $folder_info = get_term_meta($folder_id, "folder_info", true);
        $folder_info = shortcode_atts([
            'is_sticky' => 0,
            'is_high' => 0,
            'is_locked' => 0,
            'is_active' => 0,
            'has_color' => ''
        ], $folder_info);

        $folder_item['is_active'] = intval($folder_info['is_active']);
        $folder_item['is_high'] = intval($folder_info['is_high']);
        $folder_item['is_locked'] = intval($folder_info['is_locked']);
        $folder_item['is_sticky'] = intval($folder_info['is_sticky']);
        $folder_item['has_color'] = $folder_info['has_color'];

        add_term_meta($result['term_id'], "folder_info", $folder_info);

        if ($parent_id != 0) {
            $folder_info = get_term_meta($parent_id, "folder_info", true);
            $folder_info = !is_array($folder_info) ? [] : $folder_info;
            $folder_info['is_active'] = 1;
            update_term_meta($parent_id, "folder_info", $folder_info);
        }

        $duplicate_data   = isset( $params['duplicate_data'] ) ? sanitize_text_field( $params['duplicate_data'] ) : 0;
        self::duplicate_folder_data($duplicate_data, $folder_id, $result['term_id'], $post_type, $folder_type);


        if ($post_type == "attachment" || $post_type == "media") {
            $url = admin_url("upload.php?post_type=attachment&media_folder=" . $slug);
        } else if ($post_type == "plugin") {
            $url = admin_url("plugins.php?folders4plugins_folder=" . $slug);
        } else {
            $url = admin_url("edit.php?post_type=" . $post_type . "&" . $folder_type . "=" . $slug);
        }

        if($show_on_top && !empty($siblings) && is_array($siblings)) {
           foreach ($siblings as $sibling) {
               update_term_meta(intval($sibling), "wcp_custom_order", $order);
               $order++;
           }
        }

        delete_transient("premio_folders_without_trash");

        return array(
            'success'       => true,
            'message'       => esc_html__('Duplicate folder created successfully', 'folders'),
            'parent_id'     => empty($parent_id) ? "#" : $parent_id,
            'folder_item'   => $folder_item,
        );
    }


    /**
     * Copy the contents of one folder into another.
     *
     * @param bool|int|string $is_duplicate        Truthy to create draft copies of the posts;
     *                                             falsy to assign the existing posts to the new folder.
     * @param int             $duplicate_folder_id Source folder term ID.
     * @param int             $folder_id           Destination folder term ID.
     * @param string          $post_type           Post type of the items.
     * @param string          $folder_type         Folder taxonomy name.
     * @return void
     */
    private static function duplicate_folder_data($is_duplicate, $duplicate_folder_id, $folder_id, $post_type, $folder_type)
    {
        $term_data = get_term($duplicate_folder_id, $folder_type);
        if (!empty($term_data)) {
            $posts = get_posts(
                [
                    'posts_per_page' => -1,
                    'post_type' => $post_type,
                    'post_status' => 'draft, publish, future, pending, private , inherit',
                    'tax_query' => [
                        [
                            'taxonomy' => $folder_type,
                            'field' => 'term_id',
                            'terms' => $duplicate_folder_id,
                        ],
                    ],
                ]
            );

            if (!empty($posts)) {
                if ($is_duplicate) {
                    $newPostArray = [];
                    foreach ($posts as $post) {
                        $newPostArray[] = self::duplicate_posts_as_draft($post->ID);
                    }
                    foreach ($newPostArray as $newPost) {
                        wp_set_post_terms($newPost, $folder_id, $folder_type, true);
                    }
                } else {
                    foreach ($posts as $p) {
                        wp_set_post_terms($p->ID, $folder_id, $folder_type, true);
                    }
                }
            }
        }
    }

    /**
     * Duplicate a post as a draft owned by the current user.
     *
     * Attachments keep the same underlying file and metadata. Other post types
     * get their non-folder taxonomy terms and all post meta (except
     * `_wp_old_slug`) copied.
     *
     * @param int $post_id ID of the post to duplicate.
     * @return int ID of the new post, or 0 when the source post does not exist.
     */
    private static function duplicate_posts_as_draft($post_id)
    {
        global $wpdb;
        $post = get_post($post_id);
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;
        if (isset($post) && $post != null) {
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
                'post_author' => $new_post_author,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_name' => $post->post_name,
                'post_parent' => $post->post_parent,
                'post_password' => $post->post_password,
                'post_status' => 'draft',
                'post_title' => $post->post_title,
                'post_type' => $post->post_type,
                'to_ping' => $post->to_ping,
                'menu_order' => $post->menu_order,
                'post_mime_type' => $post->post_mime_type
            );
            $new_post_id = wp_insert_post($args);

            if ($post->post_type === 'attachment') {
                update_post_meta($new_post_id, '_wp_attached_file', get_post_meta($post_id, '_wp_attached_file', true));
                update_post_meta($new_post_id, '_wp_attachment_metadata', get_post_meta($post_id, '_wp_attachment_metadata', true));
            } else {
                $taxonomies = get_object_taxonomies($post->post_type);
                foreach ($taxonomies as $taxonomy) {
                    if (!str_contains($taxonomy, 'folder')) {
                        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                    }
                }
                $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                if (count($post_meta_infos) != 0) {
                    $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                    foreach ($post_meta_infos as $meta_info) {
                        $meta_key = $meta_info->meta_key;
                        if ($meta_key == '_wp_old_slug') continue;
                        $meta_value = addslashes($meta_info->meta_value);
                        $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
                    }
                    $sql_query .= implode(" UNION ALL ", $sql_query_sel);
                    $wpdb->query($sql_query);
                }
            }
            return $new_post_id;
        }
        return 0;
    }//end duplicate_posts_as_draft()

}
