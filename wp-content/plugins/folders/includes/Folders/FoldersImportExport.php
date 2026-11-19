<?php
/**
 * Class Folders Plugins import/export
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */
namespace Folders\Folders;

use Folders\Folders\Actions\FoldersCRUD;

defined( 'ABSPATH' ) || exit;

/**
 * Exports and imports the folder structure as JSON.
 *
 * The export contains, for each post type, the folder names, their nesting
 * and their `folder_info` flags. Items inside the folders are not included.
 */
class FoldersImportExport
{
    /**
     * Constructor. No hooks are registered; the class is used statically.
     */
    public function __construct()
    {

    }

    /**
     * Export the folder structure of every post type shown in the admin menu.
     *
     * Pages, posts and media are always included; other post types only when
     * they have folders.
     *
     * @param array $params Request parameters with `nonce` for the `premio_folders_export` action.
     * @return array|\WP_Error Result array with the export in `data` (a list of
     *                         `post_type`, `post_title` and `folders` entries), or
     *                         WP_Error on invalid nonce.
     */
    public static function export_folders_data($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';

        if (empty($nonce) || !wp_verify_nonce($nonce, 'premio_folders_export')) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $post_setting = apply_filters("check_for_folders_post_args", ["show_in_menu" => 1]);
        $posts = [];
        $post_types = get_post_types($post_setting, 'objects');
        foreach ($post_types as $post => $value) {
            $folder_type = \Folders\Folders\Settings::get_folder_post_type($post);
            $post_folders = self::get_terms_hierarchical_download($folder_type);
            if (in_array($post, ['page', 'post', 'media']) || count($post_folders) > 0) {
                $posts[] = [
                    'post_type' => $post,
                    'post_title' => $value->label,
                    'folders' => $post_folders
                ];
            }
        }


        return array(
            'success'       => true,
            'message'       => esc_html__('Your folders structure has been successfully exported', 'folders'),
            'data'          => $posts
        );

    }//end export_folders_data()


    /**
     * Get the top-level folders of a taxonomy with their subfolders, in custom order.
     *
     * @param string $taxonomy Folder taxonomy name.
     * @return array[] Folders, each with `name`, `properties` (folder_info flags) and `children`.
     */
    public static function get_terms_hierarchical_download($taxonomy)
    {
//        $customize_folders = get_option('customize_folders', []);
//        $foldersByUser = isset($customize_folders['folders_by_users']) && $customize_folders['folders_by_users'] == "on" ? true : false;
        $folder_by_user = 0;
//        if ($foldersByUser) {
//            $user_id = get_current_user_id();
//            $folder_by_user = $user_id;
//            if (function_exists("wp_get_current_user")) {
//                $user = wp_get_current_user();
//                $user_roles = (array)$user->roles;
//                $user_roles = !is_array($user_roles) ? [] : $user_roles;
//                if (in_array("administrator", $user_roles)) {
//                    $folder_by_user = 0;
//                }
//            }
//        }

        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
        ];

        if ($folder_by_user) {
            $args['meta_query'] = [
                [
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
                [
                    'key' => 'created_by',
                    'type' => '=',
                    'value' => $folder_by_user,
                ],
            ];
        } else {
            $args['meta_query'] = [
                [
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }

        $terms = get_terms($args);
        $hierarchical_terms = [];
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            foreach ($terms as $term) {
                if (!empty($term) && isset($term->term_id)) {
                    $folder_info = get_term_meta($term->term_id, "folder_info", true);
                    $folder_info = shortcode_atts([
                        'is_sticky' => 0,
                        'is_high' => 0,
                        'is_locked' => 0,
                        'is_active' => 0,
                        'has_color' => ''
                    ], $folder_info);

                    $main_term = ['name' => $term->name, 'properties' => $folder_info, 'children' => []];
                    $main_term['children'] = self::get_child_terms_download($taxonomy, $main_term['children'], $term->term_id, "-", $folder_by_user);
                    array_push($hierarchical_terms, $main_term);
                }
            }
        }
        return $hierarchical_terms;

    }//end get_terms_hierarchical_download()

    /**
     * Recursively get the subfolders of a folder, in custom order.
     *
     * @param string $taxonomy       Folder taxonomy name.
     * @param array  $main_term      Working array reused for each child entry (pass an empty array).
     * @param int    $term_id        Parent folder term ID.
     * @param string $separator      Depth marker, extended by "-" per level. Not included in the output.
     * @param int    $folder_by_user Optional. Only include folders created by this user ID; 0 for all.
     * @return array[] Subfolders, each with `name`, `properties` and `children`.
     */
    public static function get_child_terms_download($taxonomy, $main_term, $term_id, $separator = "-", $folder_by_user = 0)
    {
        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => $term_id,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
        ];
        if ($folder_by_user) {
            $args['meta_query'] = [
                [
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
                [
                    'key' => 'created_by',
                    'type' => '=',
                    'value' => $folder_by_user,
                ],
            ];
        } else {
            $args['meta_query'] = [
                [
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }


        $terms = get_terms($args);
        $hierarchical_terms_1 = [];
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            foreach ($terms as $term) {
                if (isset($term->name)) {
                    $main_term['name'] = $term->name;
                    $folder_info = get_term_meta($term->term_id, "folder_info", true);
                    $folder_info = shortcode_atts([
                        'is_sticky' => 0,
                        'is_high' => 0,
                        'is_locked' => 0,
                        'is_active' => 0,
                        'has_color' => ''
                    ], $folder_info);
                    $main_term['properties'] = $folder_info;
                    $main_term['children'] = [];
                    $main_term['children'] = self::get_child_terms_download($taxonomy, $main_term['children'], $term->term_id, $separator . "-");
                    array_push($hierarchical_terms_1, $main_term);
                }
            }
        }

        return $hierarchical_terms_1;

    }//end get_child_terms_download()

    /**
     * Import a folder structure from an export file.
     *
     * Top-level folders that already exist are reused; their subfolders are
     * merged into them.
     *
     * @param array $params Request parameters with `nonce` (for `premio_import_folders`) and
     *                      `uploaded_data` (the decoded export, see {@see export_folders_data()}).
     * @return array|\WP_Error Result array with `success` and `message`, or WP_Error on invalid nonce.
     */
    public static function import_folders_data($params)
    {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'premio_import_folders')) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $fileContent = $params['uploaded_data'];
        $success = false;
        foreach ($fileContent as $content) {
            if (isset($content['post_type']) && $content['folders']) {
                $post_type = sanitize_text_field($content['post_type']);
                $folders = $content['folders'];
                $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
                $success = self::insert_imported_folders($folders, $folder_type);
            }
        }
        if ($success) {
            return array(
                'success'       => true,
                'message'       => esc_html__('Your folders structure has been imported successfully', 'folders')
            );
        }
        return array(
            'success'       => true,
            'message'       => esc_html__('Error during importing folders', 'folders')
        );
    }//end import_folders_data()

    /**
     * Create the imported top-level folders and their subfolders for one taxonomy.
     *
     * @param array[] $folders     Folders with `name`, optional `properties` and optional `children`.
     * @param string  $folder_type Folder taxonomy name.
     * @return int Always 1.
     */
    public static function insert_imported_folders($folders, $folder_type)
    {
        $success = true;
        foreach ($folders as $folder) {
            $folder_name = sanitize_text_field(trim($folder['name']));
            $user_id = get_current_user_id();
            $term_id = term_exists($folder_name, $folder_type, 0);
            if (empty($term_id)) {
                $slug = FoldersCRUD::create_slug_from_string(esc_attr($folder_name)) . "-" . time()."-".$user_id;
                $result = wp_insert_term(
                    $folder_name,
                    // the term
                    $folder_type,
                    // the taxonomy
                    [
                        'parent' => 0,
                        'slug' => $slug,
                    ]
                );
                if (!is_wp_error($result)) {
                    if (isset($result['term_id']) && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                        add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                    }
                    if (isset($folder['children']) && !empty($folder['children'])) {
                        self::insert_imported_folders_child($folder['children'], $folder_type, $result['term_id']);
                    }
                }
            } else {
                if ($term_id && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                    add_term_meta($term_id, "folder_info", $folder['properties']);
                }
                if (isset($folder['children']) && !empty($folder['children'])) {
                    self::insert_imported_folders_child($folder['children'], $folder_type, $term_id);
                }
            }
        }
        return 1;
    }

    /**
     * Recursively create imported subfolders under a parent folder.
     *
     * @param array[]   $folders     Subfolders with `name`, optional `properties` and optional `children`.
     * @param string    $folder_type Folder taxonomy name.
     * @param int|array $parent_id   Parent folder term ID.
     * @return int Always 1.
     */
    public static function insert_imported_folders_child($folders, $folder_type, $parent_id)
    {
        $user_id = get_current_user_id();
        foreach ($folders as $folder) {
            $folder_name = sanitize_text_field(trim($folder['name']));
            $term_id = term_exists($folder_name, $folder_type, 0);
            if (empty($term_id)) {
                $slug = FoldersCRUD::create_slug_from_string($folder_name) . "-" . time()."-".$user_id;
                $result = wp_insert_term(
                    $folder_name,
                    // the term
                    $folder_type,
                    // the taxonomy
                    [
                        'parent' => $parent_id,
                        'slug' => $slug,
                    ]
                );
                if (!is_wp_error($result)) {
                    if (isset($result['term_id']) && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                        add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                    }
                    if (isset($folder['children']) && !empty($folder['children'])) {
                        $success = self::insert_imported_folders_child($folder['children'], $folder_type, $result['term_id']);
                    }
                }
            } else {
                if ($term_id && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                    add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                }
                if (isset($folder['children']) && !empty($folder['children'])) {
                    $success = self::insert_imported_folders_child($folder['children'], $folder_type, $term_id);
                }
            }
        }
        return 1;
    }
}