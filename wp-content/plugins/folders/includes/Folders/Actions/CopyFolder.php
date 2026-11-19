<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Copies folders to a new parent folder.
 *
 * Used by the "copy folder" action in the folder sidebar.
 */
class CopyFolder {

    /**
     * Constructor. No hooks are registered; the class is used statically.
     */
    public function __construct() {

    }

    /**
     * Copy one or more folders under a new parent.
     *
     * For each source folder, creates a new folder term with the same name, copies
     * its display flags (sticky, starred, locked, expanded, color), assigns the
     * source folder's posts to the copy, and positions the copies at the top or
     * bottom of their siblings according to the "new folder placement" setting.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string       $nonce      Nonce for the `folder_nonce_{post_type}` action.
     *     @type string       $post_type  Post type the folders belong to.
     *     @type array        $folder_ids IDs of the folders to copy.
     *     @type int|string   $parent_id  Optional. Target parent folder ID; 0 for root.
     *     @type array        $siblings   Optional. IDs of the existing folders at the target level.
     * }
     * @return array|\WP_Error Result array with the created `folders`, or WP_Error on invalid request.
     */
    public static function copy_folder($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $folder_ids  = isset( $params['folder_ids'] ) ? map_deep( $params['folder_ids'], 'sanitize_text_field' ) : [];

        if(empty($post_type) || empty($folder_ids) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_manage_folders()) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $parent_id = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : 0;
        $siblings  = isset( $params['siblings'] ) ? map_deep( $params['siblings'], 'sanitize_text_field' ) : [];
        $user_id     = get_current_user_id();
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        $show_on_top = \Folders\Admin\Settings::get_field_settings('general_settings', 'new_folder_placement') == 'top';
        $parent_id = empty($parent_id) || !is_numeric($parent_id) ? 0 : (int) $parent_id;
        if($show_on_top == 'top') {
            $order = 0;
        } else {
            $order = count($siblings);
        }
        $records = [];
        $last_added_id = 0;
        foreach ($folder_ids as $folder_id) {
            $folder_id = intval($folder_id);
            $term = get_term($folder_id);
            if (!empty($term) && isset($term->slug)) {
                $folder = trim($term->name);
                $slug = FoldersCRUD::create_slug_from_string($term->name) . "-" . time() . "-" . $user_id;

                $result = wp_insert_term(
                    $folder,
                    $folder_type,
                    [
                        'parent' => $parent_id,
                        'slug' => $slug,
                    ]
                );

                if (!empty($result)) {
                    $last_added_id = $result['term_id'];
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
                    $folder_item['is_default'] = 0;
                    $folder_item['folder_count'] = 0;
                    $folder_item['position'] = $order;
                    $order++;

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

                    if ($post_type == "attachment" || $post_type == "media") {
                        $url = admin_url("upload.php?post_type=attachment&media_folder=" . $slug);
                    } else if ($post_type == "plugin") {
                        $url = admin_url("plugins.php?folders4plugins_folder=" . $slug);
                    } else {
                        $url = admin_url("edit.php?post_type=" . $post_type . "&" . $folder_type . "=" . $slug);
                    }

                    if ($parent_id != 0) {
                        $folder_info = get_term_meta($parent_id, "folder_info", true);
                        $folder_info = !is_array($folder_info) ? [] : $folder_info;
                        $folder_info['is_active'] = 1;
                        update_term_meta($parent_id, "folder_info", $folder_info);
                    }

                    $postArray = get_posts(
                        [
                            'posts_per_page' => -1,
                            'post_type' => $folder_type,
                            'tax_query' => [
                                [
                                    'taxonomy' => $post_type,
                                    'field' => 'term_id',
                                    'terms' => $folder_id,
                                ],
                            ],
                        ]
                    );
                    if (!empty($postArray)) {
                        foreach ($postArray as $p) {
                            wp_set_post_terms($p->ID, $result['term_id'], $folder_type, true);
                        }
                    }

                    $records[] = $folder_item;
                }
            }
        }
        if(!empty($records)) {
            if ($show_on_top && !empty($siblings)) {
                foreach ($siblings as $sibling) {
                    update_term_meta(intval($sibling), "wcp_custom_order", $order);
                    $order++;
                }
            }
            $is_new_folder_default = \Folders\Admin\Settings::get_field_settings('general_settings', 'open_new_folder_by_default') == 1;
            return array(
                'success'       => true,
                'message'       => esc_html__('Folders created successfully', 'folders'),
                'folders_count' => $order,
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
}
