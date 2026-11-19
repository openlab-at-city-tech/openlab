<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the folder data used by the sidebar tree and folder dropdowns.
 *
 * Loads folder terms with their `folder_info` flags and counts, caches them
 * per post type for the request, and flattens them into an indented list.
 */
class FoldersTree {

    private static $default = [];
    private static $folders = null;

    /**
     * Constructor. No hooks are registered; the class is used statically.
     */
    public function __construct() {

    }

    public static $defaultData = [
        'is_sticky' => 0,
        'is_high' => 0,
        'is_locked' => 0,
        'is_active' => 0,
        'is_default' => 0,
        'has_color' => ''
    ];

    /**
     * Get the folders of a post type as a flat, indented list in tree order.
     *
     * Suitable for dropdowns: each folder's `text` is prefixed once per nesting level.
     *
     * @param string $post_type Post type.
     * @param string $prefix    Indent string repeated for each nesting level.
     * @return array[] Folder data arrays in tree order.
     */
    public static function get_data_by_post_type( $post_type = '', $prefix = '- ' ) {
        if ( empty( $post_type ) ) {
            return [];
        }

        $default = get_option("last_folder_status_for" . $post_type);
        self::$default[$post_type] = '';
        if(!empty($default)) {
            self::$default[$post_type] = $default;
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        if(!isset(self::$folders[$folder_type])) {
            self::$folders[$post_type] = \Folders\Folders\FoldersTree::get_folders($folder_type, $post_type);
        }

        return self::arrange_folders_to_tree( self::$folders[$post_type], $prefix );
    }

    /**
     * Arrange a flat folder list into tree order with indented names.
     *
     * @param array[] $folders Folder data arrays from {@see get_folders()}.
     * @param string  $prefix  Indent string repeated for each nesting level.
     * @return array[] Folder data arrays in tree order.
     */
    public static function arrange_folders_to_tree( $folders, $prefix = '- ' ) {
        $grouped = [];
        foreach ( $folders as $folder ) {
            $grouped[$folder['parent_id']][] = $folder;
        }

        $result = [];
        self::build_folder_tree( $grouped, 0, 0, $prefix, $result );

        return $result;
    }

    /**
     * Keep only the fields the folder dropdowns need from a folder tree.
     *
     * The tree list is sent to the browser next to the full folder list, so
     * repeating every field (nonce, state, flags) for every folder only made
     * the page larger.
     *
     * @param array[] $tree Folder tree from {@see arrange_folders_to_tree()}.
     * @return array[]
     */
    public static function slim_tree( $tree ) {
        $fields = [ 'id' => 1, 'parent_id' => 1, 'text' => 1, 'slug' => 1, 'count' => 1, 'trash_count' => 1, 'term_url' => 1 ];
        $slim = [];
        foreach ( (array) $tree as $folder ) {
            $slim[] = array_intersect_key( (array) $folder, $fields );
        }
        return $slim;
    }

    /**
     * Recursively append the children of a folder to the result, depth first.
     *
     * @param array[] $grouped   Folders grouped by `parent_id`.
     * @param int     $parent_id Parent folder ID whose children are added.
     * @param int     $depth     Current nesting depth.
     * @param string  $prefix    Indent string repeated for each nesting level.
     * @param array[] $result    Result list, passed by reference.
     * @return void
     */
    private static function build_folder_tree( $grouped, $parent_id, $depth, $prefix, &$result ) {
        if ( ! isset( $grouped[$parent_id] ) ) {
            return;
        }

        foreach ( $grouped[$parent_id] as $folder ) {
            $folder['text'] = str_repeat( $prefix, $depth ) . $folder['text'];
            $result[]  = $folder;
            self::build_folder_tree( $grouped, $folder['id'], $depth + 1, $prefix, $result );
        }
    }

    /**
     * Get all folders of a post type as data arrays for the sidebar tree (jsTree format).
     *
     * By default folders are ordered by their saved custom order; the stored
     * order is then re-numbered to match. The folder matching the last opened or
     * requested folder is marked as selected. Results are cached per post type.
     *
     * @param string $folder_type Folder taxonomy name.
     * @param string $post_type   Post type.
     * @param bool   $custom_sort Whether to use `$order_by` and `$order` instead of the custom order.
     * @param string $order_by    get_terms() `orderby` value (for example "title" or "ID").
     * @param string $order       "ASC" or "DESC".
     * @return array[] Folder data arrays with id, parent, text, slug, counts, flags, nonce, state and URL.
     */
    public static function get_folders( $folder_type = 0 , $post_type = '', $custom_sort = false, $order_by = '', $order = '' )
    {
        if(isset(self::$folders[$post_type])) {
            return self::$folders[$post_type];
        }

        if ( empty( $folder_type ) ) {
            return [];
        }

        if(!isset(self::$default[$post_type]) || empty(self::$default[$post_type])) {
            $default = get_option("last_folder_status_for" . $post_type);
            self::$default[$post_type] = '';
            if (!empty($default)) {
                self::$default[$post_type] = $default;
            }

            if(empty(self::$default[$post_type]) && isset($_GET[$folder_type]) && !empty($_GET[$folder_type])) {
                self::$default[$post_type] = sanitize_text_field($_GET[$folder_type]);
            }
        }

        $args = [
            'taxonomy' => $folder_type,
            'hide_empty' => false,
            'order' => 'ASC',
            'update_count_callback' => '_update_generic_term_count',
            // Polylang auto-filters get_terms() results (including each term's
            // "count") to whatever language is currently selected in the admin
            // language switcher, once it treats a taxonomy as belonging to a
            // translated post type. Explicitly passing 'lang' => '' tells
            // Polylang not to apply that filter here, so folder counts always
            // reflect ALL languages, matching what "count" is meant to show.
            // Harmless / ignored when Polylang isn't active.
            'lang' => '',
        ];

        if($custom_sort && !empty($order_by) && !empty($order)) {
            $args['orderby'] = $order_by;
            $args['order'] = $order;
        } else {
            $args['orderby'] = 'meta_value_num';
            $args['meta_query'] = [
                [
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }


        $terms = get_terms($args);

        if ( is_wp_error( $terms ) ) {
            return [];
        }

        $folders = [];
        foreach ($terms as $key => $term) {
            $folder_info = get_term_meta($term->term_id, "folder_info", true);
            $folder_info = shortcode_atts(self::$defaultData, $folder_info);
            $folders[] = [
                'id' => $term->term_id,
                'parent' => $term->parent == 0 ? '#' : $term->parent,
                'parent_id' => $term->parent,
                'text' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
                'trash_count' => $term->trash_count,
                'is_sticky' => $folder_info['is_sticky'],
                'is_high' => $folder_info['is_high'],
                'is_locked' => $folder_info['is_locked'],
                'is_active' => $folder_info['is_active'],
                'has_color' => $folder_info['has_color'],
                'is_default' => $folder_info['is_default'],
                'nonce'     => wp_create_nonce( 'folder_nonce_' . $term->term_id ),
                'state'     => [
                    'opened' => $folder_info['is_active'],
                    'selected' => isset(self::$default[$post_type]) && $term->slug == self::$default[$post_type] ? true : false
                ],
                'is_deleted' => 0,
                'term_url' => self::get_term_url($post_type, $term->slug)
            ];
            update_term_meta($term->term_id, "wcp_custom_order", $key);
        }
        self::$folders[$post_type] = $folders;
        return self::$folders[$post_type];
    }

    /**
     * Get the admin list URL filtered by a folder.
     *
     * @param string $post_type Post type.
     * @param string $slug      Folder slug.
     * @return string Admin URL.
     */
    public static function get_term_url( $post_type, $slug = '' ) {
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        if($post_type == 'attachment'){
            return admin_url('upload.php?media_folder=' . $slug);
        }
        return admin_url('edit.php?post_type=' . $post_type . '&' . $folder_type . '=' . $slug);
    }
}
