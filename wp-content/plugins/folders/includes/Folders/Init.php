<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the folder taxonomies.
 *
 * Creates a hierarchical, non-public folder taxonomy for each folder-enabled
 * post type on `init`.
 */
class Init {

    /**
     * Folder taxonomies registered on this request.
     *
     * @var string[]
     */
    private static $folder_taxonomies = [];

    /**
     * Register the `init` hook (priority 15).
     */
    public function __construct() {
        add_action('init', [$this, 'create_folders_terms'], 15);
        add_filter('to/get_terms_orderby/ignore', [$this, 'ignore_terms_order_plugin'], 10, 3);
    }

    /**
     * Keep "Category Order and Taxonomy Terms Order" away from folder queries.
     *
     * That plugin rewrites the ORDER BY of every term query in wp-admin to its
     * own `term_order` column, which replaces the folders' custom order and the
     * sort options picked in the sidebar. Folders keep their own order, so tell
     * it to leave queries that only read folder taxonomies alone.
     *
     * @param bool   $ignore  Whether the plugin should skip this query.
     * @param string $orderby Current ORDER BY.
     * @param array  $args    get_terms() arguments.
     * @return bool
     */
    public function ignore_terms_order_plugin($ignore, $orderby = '', $args = []) {
        if ($ignore || empty(self::$folder_taxonomies) || !is_array($args) || empty($args['taxonomy'])) {
            return $ignore;
        }

        $taxonomies = (array) $args['taxonomy'];
        if (array_diff($taxonomies, self::$folder_taxonomies)) {
            return $ignore;
        }

        return true;
    }

    /**
     * Register a folder taxonomy for every folder-enabled post type.
     *
     * Also normalizes the saved post type list and makes sure every new folder
     * term gets a `wcp_custom_order` meta value of 0. Taxonomies are not
     * registered for users with "no-access", and are read-only in the UI for
     * "view-only" users.
     *
     * @return false|void False when there is nothing to register.
     */
    public static function create_folders_terms() {
        $folders = \Folders\Folders\Settings::get_settings();
        $posts = [];
        if (!empty($folders) && is_array($folders)) {
            foreach ($folders as $folder) {
                if (!(strpos($folder, 'folder4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }

                if (in_array($folder, ["page", "post", "attachment"])) {
                    $posts[] = str_replace("folder4", "", $folder);
                } else {
                    $posts[] = $folder;
                }
            }

            if (!empty($posts)) {
                \Folders\Folders\Settings::update_settings($posts);
            }
        }

        $folders = \Folders\Folders\Settings::get_settings();
        $show_in_menu = apply_filters('folders_show_in_menu', true);
        $user_role = \Folders\Folders\UserFolders::get_user_role();

        if(empty($folders) || !is_array($folders) || $user_role == "no-access") {
            return false;
        }
        $edit_status = true;
        if ($user_role == "view-only") {
            $edit_status = false;
        }
        foreach ($folders as $folder) {
            $labels = [
                'name' => esc_html__('Folders', 'folders'),
                'singular_name' => esc_html__('Folder', 'folders'),
                'all_items' => esc_html__('All Folders', 'folders'),
                'edit_item' => esc_html__('Edit Folder', 'folders'),
                'update_item' => esc_html__('Update Folder', 'folders'),
                'add_new_item' => esc_html__('Add New Folder', 'folders'),
                'new_item_name' => esc_html__('Add folder name', 'folders'),
                'menu_name' => esc_html__('Folders', 'folders'),
                'search_items' => esc_html__('Search Folders', 'folders'),
                'parent_item' => esc_html__('Parent Folder', 'folders'),
            ];

            $args = [
                'label' => esc_html__('Folder', 'folders'),
                'labels' => $labels,
                'show_tagcloud' => false,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => $edit_status,
                'show_in_quick_edit' => $edit_status,
                'show_in_menu' => false,
                'show_in_rest' => true,
                'show_admin_column' => true,
                // 'update_count_callback' => '_update_post_term_count',
                'update_count_callback' => '_update_generic_term_count',
                'query_var' => true,
                'rewrite' => false,
                'capabilities' => [
                    'edit_terms' => 'manage_categories',
                    'delete_terms' => 'manage_categories',
                    'assign_terms' => 'manage_categories',
                ],
            ];

            // if (!$edit_status) {
            //     $status = use_block_editor_for_post_type($folder);
            //     if ($status) {
            //         $args['meta_box_cb'] = false;
            //     } else {
            //         $args['meta_box_cb'] = function ($post, $args) {
            //             return false;
            //         };
            //     }
            // }

            $folder_post_type = \Folders\Folders\Settings::get_folder_post_type($folder);

            register_taxonomy(
                $folder_post_type,
                $folder,
                $args
            );
            self::$folder_taxonomies[] = $folder_post_type;

            add_action("create_{$folder_post_type}", function($term_id, $tt_id) {
                if ( ! get_term_meta( $term_id, 'wcp_custom_order', true ) ) {
                    add_term_meta( $term_id, 'wcp_custom_order', 0, true );
                }
            }, 10, 2);
        }
    }
}
