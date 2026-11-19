<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Manages the items (posts, pages, media) inside folders.
 *
 * Assigns and removes items from folders, supports undoing the last change,
 * sorts the folder list, and calculates the item counts shown next to each
 * folder (excluding trashed items).
 */
class FoldersItems {

    /**
     * Name of the transient that caches folder item counts.
     */
    const COUNT_CACHE = 'premio_folders_without_trash';

    /**
     * Version of the count cache layout. A cache saved in another layout is ignored.
     */
    const COUNT_CACHE_VERSION = 2;

    /**
     * In-request copy of the count cache.
     *
     * @var array|null
     */
    private static $count_cache = null;

    /**
     * Folder taxonomies of the folder-enabled post types, for this request.
     *
     * @var string[]|null
     */
    private static $folder_taxonomies = null;

    /**
     * Register the `get_terms` filter that adds item counts to folder terms.
     */
    public function __construct()
    {
        add_filter('get_terms', [$this, 'get_terms_filter_without_trash'], 10, 3);

        // Whenever the count cache is deleted, forget the copy kept for this request too.
        add_action('delete_transient_' . self::COUNT_CACHE, [__CLASS__, 'reset_count_cache']);
    }

    /**
     * Forget the in-request copy of the folder count cache.
     *
     * @return void
     */
    public static function reset_count_cache()
    {
        self::$count_cache = null;
    }

    /**
     * Read the count cache.
     *
     * Layout: `['version' => 2, 'tax' => [taxonomy => [key => value]]]`, where
     * a key is "terms", "unassigned" or "total" plus the count context (for
     * example the admin language).
     *
     * @return array
     */
    private static function get_count_cache()
    {
        if (self::$count_cache === null) {
            $cache = get_transient(self::COUNT_CACHE);
            if (!is_array($cache) || !isset($cache['version']) || $cache['version'] !== self::COUNT_CACHE_VERSION || !isset($cache['tax']) || !is_array($cache['tax'])) {
                $cache = ['version' => self::COUNT_CACHE_VERSION, 'tax' => []];
            }
            self::$count_cache = $cache;
        }
        return self::$count_cache;
    }

    /**
     * Save one value in the count cache.
     *
     * @param string $taxonomy Folder taxonomy.
     * @param string $key      Cache key within the taxonomy.
     * @param mixed  $value    Value to store.
     * @return void
     */
    public static function set_count_cache($taxonomy, $key, $value)
    {
        $cache = self::get_count_cache();
        $cache['tax'][$taxonomy][$key] = $value;
        self::$count_cache = $cache;
        set_transient(self::COUNT_CACHE, $cache, 3 * DAY_IN_SECONDS);
    }

    /**
     * Read one value from the count cache.
     *
     * @param string $taxonomy Folder taxonomy.
     * @param string $key      Cache key within the taxonomy.
     * @return mixed|null The value, or null when it is not cached.
     */
    public static function get_cached_count($taxonomy, $key)
    {
        $cache = self::get_count_cache();
        return isset($cache['tax'][$taxonomy][$key]) ? $cache['tax'][$taxonomy][$key] : null;
    }

    /**
     * Clear cached counts.
     *
     * Pass a folder taxonomy to clear only its counts (item counts, the
     * "Unassigned" count and the total), or nothing to clear everything.
     *
     * @param string $taxonomy Optional folder taxonomy.
     * @return void
     */
    public static function flush_count_cache($taxonomy = '')
    {
        if (empty($taxonomy)) {
            delete_transient(self::COUNT_CACHE);
            self::$count_cache = null;
            return;
        }

        $cache = self::get_count_cache();
        if (!isset($cache['tax'][$taxonomy])) {
            return;
        }
        unset($cache['tax'][$taxonomy]);
        self::$count_cache = $cache;
        set_transient(self::COUNT_CACHE, $cache, 3 * DAY_IN_SECONDS);
    }

    /**
     * Folder taxonomy of a post type, or an empty string when folders are off for it.
     *
     * @param string $post_type Post type.
     * @return string
     */
    public static function folder_taxonomy_for_post_type($post_type)
    {
        if (empty($post_type) || !is_string($post_type) || !\Folders\Folders\Settings::check_for_folder($post_type)) {
            return '';
        }
        return \Folders\Folders\Settings::get_folder_post_type($post_type);
    }

    /**
     * Folder taxonomies of all folder-enabled post types.
     *
     * @return string[]
     */
    private static function folder_taxonomies()
    {
        if (self::$folder_taxonomies === null) {
            self::$folder_taxonomies = [];
            $options = \Folders\Folders\Settings::get_settings();
            if (!empty($options) && is_array($options)) {
                foreach ($options as $option) {
                    self::$folder_taxonomies[] = \Folders\Folders\Settings::get_folder_post_type($option);
                }
            }
        }
        return self::$folder_taxonomies;
    }

    /**
     * Post type that a folder taxonomy belongs to.
     *
     * @param string $taxonomy Folder taxonomy.
     * @return string
     */
    private static function post_type_for_taxonomy($taxonomy)
    {
        if ($taxonomy == "post_folder") {
            return "post";
        } else if ($taxonomy == "folder") {
            return "page";
        } else if ($taxonomy == "media_folder") {
            return "attachment";
        }
        // Strip the "_folder" suffix (trim() would strip characters, not the suffix).
        return (substr($taxonomy, -7) === '_folder') ? substr($taxonomy, 0, -7) : $taxonomy;
    }

    /**
     * Extra cache key part for counts that depend on more than the data, such
     * as the admin language when WPML or Polylang is active.
     *
     * @param string $taxonomy  Folder taxonomy.
     * @param string $post_type Post type.
     * @return string
     */
    private static function count_context($taxonomy, $post_type)
    {
        $context = apply_filters('premio_folders_count_context', '', $taxonomy, $post_type);
        return is_scalar($context) && $context !== '' ? '|' . $context : '';
    }

    /**
     * Item counts (not trashed, not auto-drafts) of every folder in a taxonomy,
     * keyed by term taxonomy ID, in a single query.
     *
     * @param string $taxonomy Folder taxonomy.
     * @return int[]
     */
    private static function query_folder_counts($taxonomy)
    {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT tr.term_taxonomy_id AS ttid, COUNT(*) AS total
            FROM {$wpdb->term_relationships} tr
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
            WHERE tt.taxonomy = %s
            AND p.post_status NOT IN ('trash', 'auto-draft')
            GROUP BY tr.term_taxonomy_id",
            $taxonomy
        ));

        $counts = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $counts[(int) $row->ttid] = (int) $row->total;
            }
        }
        return $counts;
    }

    /**
     * Item counts for the given folders of one taxonomy, keyed by term taxonomy ID.
     *
     * Counts come from the cache when possible. Otherwise all counts of the
     * taxonomy are read with one query, or, when an integration supplies its
     * own count through the `premio_folder_item_in_taxonomy` filter (WPML,
     * Polylang), from that filter for each folder that is not cached yet.
     *
     * @param string     $taxonomy Folder taxonomy.
     * @param \WP_Term[] $terms    Folders of that taxonomy.
     * @return int[]
     */
    public static function get_folder_counts($taxonomy, $terms)
    {
        $post_type = self::post_type_for_taxonomy($taxonomy);
        $key = 'terms' . self::count_context($taxonomy, $post_type);
        $counts = self::get_cached_count($taxonomy, $key);
        $counts = is_array($counts) ? $counts : [];

        $missing = [];
        foreach ($terms as $term) {
            if (!array_key_exists((int) $term->term_taxonomy_id, $counts)) {
                $missing[] = $term;
            }
        }
        if (empty($missing)) {
            return $counts;
        }

        $raw_counts = null;
        if (has_filter("premio_folder_item_in_taxonomy")) {
            foreach ($missing as $term) {
                $count = apply_filters("premio_folder_item_in_taxonomy", $term->term_id, [
                    'post_type' => $post_type,
                    'taxonomy'  => $taxonomy,
                ]);
                if ($count === null) {
                    // The integration does not handle this post type: use the plain count.
                    if ($raw_counts === null) {
                        $raw_counts = self::query_folder_counts($taxonomy);
                    }
                    $count = isset($raw_counts[(int) $term->term_taxonomy_id]) ? $raw_counts[(int) $term->term_taxonomy_id] : 0;
                }
                $counts[(int) $term->term_taxonomy_id] = (int) $count;
            }
        } else {
            $counts = self::query_folder_counts($taxonomy);
            foreach ($terms as $term) {
                if (!isset($counts[(int) $term->term_taxonomy_id])) {
                    $counts[(int) $term->term_taxonomy_id] = 0;
                }
            }
        }

        self::set_count_cache($taxonomy, $key, $counts);
        return $counts;
    }

    /**
     * Undo the last folder assignment change.
     *
     * Restores the folder terms saved in the `folder_undo_settings` transient by
     * the previous move or remove action. Requires the `edit_posts` capability.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`)
     *                      and `post_type`.
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function undo_folders_changes($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';

        if(empty($post_type) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        if ( !current_user_can( 'edit_posts' ) ) {
            return new \WP_Error( 'error', esc_html__('You have not permission to undo folder changes', 'folders'), array( 'status' => 403 ) );
        }

        $settings = get_transient("folder_undo_settings");
        $post_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        if (!empty($settings) && is_array($settings)) {
            foreach ($settings as $item) {
                if (!isset($item['post_id']) || !current_user_can('edit_post', absint($item['post_id']))) {
                    continue;
                }
                $terms = get_the_terms($item['post_id'], $post_type);
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        wp_remove_object_terms($item['post_id'], $term->term_id, $post_type);
                        if (isset($trash_folders[$term->term_taxonomy_id])) {
                            unset($trash_folders[$term->term_taxonomy_id]);
                        }
                    }
                }

                if (!empty($item['terms']) && is_array($item['terms'])) {
                    foreach ($item['terms'] as $term) {
                        wp_set_post_terms($item['post_id'], $term->term_id, $post_type, true);
                        if (isset($trash_folders[$term->term_taxonomy_id])) {
                            unset($trash_folders[$term->term_taxonomy_id]);
                        }
                    }
                }
            }

            self::flush_count_cache();
        }

        return array(
            'success' => true
        );
    }

    /**
     * Sort the folder list and save the chosen order for the post type.
     *
     * Supported values: "a-z" and "z-a" (by name), "n-o" (newest first) and
     * "o-n" (oldest first). Falls back to the saved order when `sort` is empty.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`),
     *                      `post_type` and `sort`.
     * @return array|\WP_Error Result array with the sorted folders in `data`, or WP_Error on invalid request.
     */
    public static function sort_folder_data($params) {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $order_field = isset( $params['sort'] ) ? sanitize_text_field( $params['sort'] ) : '';

        if(empty($order_field)) {
            $order_field = get_option('wcp_custom_sort_'.$post_type, 'a-z');
        }

        if(empty($post_type) || empty($order_field) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_manage_folders()) {
            return new \WP_Error( 'error', esc_html__('You do not have permission to sort folders', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        update_option("wcp_custom_sort_" . $post_type, $order_field) or add_option("wcp_custom_sort_" . $post_type, $order_field);

        $order_by = "";
        $order = "ASC";

        if ($order_field == "a-z" || $order_field == "z-a") {
            $order_by = 'title';
            if ($order_field == "z-a") {
                $order = "DESC";
            }
        } else if ($order_field == "n-o" || $order_field == "o-n") {
            $order_by = 'ID';
            if ($order_field == "o-n") {
                $order = "ASC";
            } else {
                $order = "DESC";
            }
        }

        if(!empty($order_by) && !empty($order)) {
            $folders = \Folders\Folders\FoldersTree::get_folders($folder_type, $post_type, true, $order_by, $order);
        } else {
            $folders = \Folders\Folders\FoldersTree::get_folders($folder_type, $post_type);
        }

        return array(
            'success' => true,
            'data'    => $folders,
        );
    }

    /**
     * Remove items from a folder after checking whether they belong to other folders.
     *
     * If any item is also in another folder, nothing is removed and the item IDs
     * are returned with `success` false so the UI can ask the user whether to
     * remove them from the current folder only or from all folders. Otherwise
     * the items are removed via {@see remove_post_from_folders()}.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`),
     *                      `post_type`, `folder_id` and `post_ids`.
     * @return array|\WP_Error Result array, or WP_Error on invalid request.
     */
    public static function check_for_other_folders($params) {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $post_ids  = isset( $params['post_ids'] ) ? map_deep( $params['post_ids'], 'sanitize_text_field' ) : [];

        if(empty($post_type) || empty($folder_id) || empty($post_ids) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        foreach ($post_ids as $id) {
            $terms = get_the_terms($id, $folder_type);
            if (!empty($terms) && is_array($terms)) {
                foreach ($terms as $term) {
                    if ($term->term_id != $folder_id) {
                        return array(
                            'success' => false,
                            'data' => [
                                'post_ids' => $post_ids,
                            ]
                        );
                    }
                }
            }
        }

        return self::remove_post_from_folders($params);
    }

    /**
     * Remove items from the current folder or from all folders.
     *
     * The previous folder assignments are saved in the `folder_undo_settings`
     * transient for one day so the change can be undone.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce       Nonce for the `folder_nonce_{post_type}` action.
     *     @type string     $post_type   Post type of the items.
     *     @type array      $post_ids    IDs of the items.
     *     @type int|string $folder_id   Optional. Current folder ID.
     *     @type string     $remove_from Optional. "current" to remove only from `folder_id`;
     *                                   otherwise the items are removed from all folders.
     * }
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function remove_post_from_folders($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $post_ids  = isset( $params['post_ids'] ) ? map_deep( $params['post_ids'], 'sanitize_text_field' ) : [];

        if(empty($post_type) || empty($post_ids) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        $remove_from  = isset( $params['remove_from'] ) ? sanitize_text_field( $params['remove_from'] ) : '';

        $folderUndoSettings = [];
        foreach ($post_ids as $id) {
            if (!empty($id) && is_numeric($id) && $id > 0 && current_user_can('edit_post', absint($id))) {
                $terms = get_the_terms($id, $folder_type);
                $post_terms = [
                    'post_id' => $id,
                    'terms' => $terms,
                ];
                $folderUndoSettings[] = $post_terms;
                if ($remove_from == "current" && !empty($folder_id)) {
                    wp_remove_object_terms($id, intval($folder_id), $folder_type);
                } else {
                    wp_delete_object_term_relationships($id, $folder_type);
                }
            }
        }

        delete_transient("folder_undo_settings");
        self::flush_count_cache();
        set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);
        return array(
            'success'       => true
        );
    }

    /**
     * Remember the last opened folder for a post type.
     *
     * Stored in the `last_folder_status_for{post_type}` option.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`),
     *                      `post_type` and `folder_id`.
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function save_last_folder_state($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';

        if(empty($post_type) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_use_folders($post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        if (!empty($post_type) && !empty($folder_id)) {
            \Folders\Folders\UserFolders::set_last_folder($post_type, $folder_id);
        } else if(!empty($post_type)) {
            \Folders\Folders\UserFolders::set_last_folder($post_type, '');
        }

        return array(
            'success'       => true
        );
    }
    /**
     * Add a `trash_count` property to folder terms with the number of items that
     * are not trashed or auto-drafts.
     *
     * Runs for every get_terms() call on the site, so it returns straight away
     * unless the query is for a folder taxonomy. Counts come from
     * {@see get_folder_counts()} (cached, one query per taxonomy).
     *
     * @param \WP_Term[]|mixed $terms      Terms returned by get_terms().
     * @param string[]         $taxonomies Queried taxonomies.
     * @param array            $args       get_terms() query arguments.
     * @return \WP_Term[]|mixed Terms, with `trash_count` added for folder taxonomies.
     */
    public function get_terms_filter_without_trash($terms, $taxonomies, $args)
    {
        if (!is_array($terms) || empty($terms) || empty($taxonomies) || !is_array($taxonomies)) {
            return $terms;
        }

        $folder_taxonomies = self::folder_taxonomies();
        if (!array_intersect($taxonomies, $folder_taxonomies)) {
            return $terms;
        }

        $by_taxonomy = [];
        foreach ($terms as $term) {
            if (is_object($term) && isset($term->term_id, $term->term_taxonomy_id, $term->taxonomy) && in_array($term->taxonomy, $folder_taxonomies, true)) {
                $by_taxonomy[$term->taxonomy][] = $term;
            }
        }

        foreach ($by_taxonomy as $taxonomy => $taxonomy_terms) {
            $counts = self::get_folder_counts($taxonomy, $taxonomy_terms);
            foreach ($taxonomy_terms as $term) {
                $term->trash_count = isset($counts[(int) $term->term_taxonomy_id]) ? $counts[(int) $term->term_taxonomy_id] : 0;
            }
        }

        return $terms;
    }//end get_terms_filter_without_trash()

    /**
     * Get the folder list, folder tree and item counts for a post type.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`)
     *                      and `post_type`.
     * @return array|\WP_Error Result array with `folders`, `folders_tree`, `total_items`
     *                         and `empty_items` in `data`, or WP_Error on invalid request.
     */
    public static function get_folder_items($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';

        if(empty($post_type) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
        $folders = \Folders\Folders\FoldersTree::get_folders($folder_type, $post_type);
        $folders_tree = \Folders\Folders\FoldersTree::arrange_folders_to_tree( $folders, '- ' );
        $total_items = self::total_items($post_type);
        $empty_items = self::total_unassigned_items($post_type);

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'data'     => [
                'folders' => $folders,
                'folders_tree' => \Folders\Folders\FoldersTree::slim_tree($folders_tree),
                'total_items' => $total_items,
                'empty_items' => $empty_items,
            ]
        );
    }
    /**
     * Count the items of a post type that are not in any folder.
     *
     * Counts published, scheduled, draft, private and pending posts (or inherit
     * and private attachments). The `premio_folder_un_categorized_items` filter
     * can supply the count instead.
     *
     * @param string $post_type Post type.
     * @return int|string Number of unassigned items.
     */
    public static function total_unassigned_items($post_type)
    {
        $taxonomy = \Folders\Folders\Settings::get_folder_post_type($post_type);
        // Third-party filters can change this query per request; never cache then.
        $use_cache = !has_filter('folders_count_join_query') && !has_filter('folders_count_where_query');
        $key = 'unassigned' . self::count_context($taxonomy, $post_type);
        if ($use_cache) {
            $cached = self::get_cached_count($taxonomy, $key);
            if ($cached !== null) {
                return $cached;
            }
        }

        $count = self::query_unassigned_items($post_type);
        if ($use_cache) {
            self::set_count_cache($taxonomy, $key, $count);
        }
        return $count;
    }

    /**
     * Count the items of a post type that are not in any folder (uncached).
     *
     * @param string $post_type Post type.
     * @return int|string Number of unassigned items.
     */
    private static function query_unassigned_items($post_type)
    {
        global $wpdb;

        $post_table = $wpdb->prefix . "posts";
        $term_table = $wpdb->prefix . "term_relationships";
        $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";
        $term_meta = $wpdb->prefix . "termmeta";
        $taxonomy = \Folders\Folders\Settings::get_folder_post_type($post_type);;
        $tlrcds = null;
        if (has_filter("premio_folder_un_categorized_items")) {
            $tlrcds = apply_filters("premio_folder_un_categorized_items", $post_type, $taxonomy);
        }
        if ($tlrcds === null) {
            $user_filter = false;

            if (!$user_filter) {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1 AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";

                    $query = $wpdb->prepare($query, $taxonomy, $post_type);
                } else {
                    $select = "SELECT COUNT(DISTINCT(P.ID)) AS total_records FROM {$post_table} AS P";
                    $where = ["post_type = 'attachment' "];
                    $where[] = "(post_status = 'inherit' OR post_status = 'private')";
                    $where[] = "(NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = P.ID
                                    )
                                )";

                    $join = apply_filters('folders_count_join_query', "");
                    $where = apply_filters('folders_count_where_query', $where);

                    $query = $select . $join . " WHERE " . implode(' AND ', $where);

                    $query = $wpdb->prepare($query, $taxonomy);
                }
            } else {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1 AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    INNER JOIN {$term_meta}
                                    ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id AND {$term_meta}.meta_key = 'created_by' AND {$term_meta}.meta_value = {$user_id}
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";

                    $query = $wpdb->prepare($query, $taxonomy, $post_type);
                } else {

                    $select = "SELECT COUNT(DISTINCT(P.ID)) AS total_records FROM {$post_table} AS P";
                    $where = ["post_type = 'attachment' "];
                    $where[] = "(post_status = 'inherit' OR post_status = 'private')";
                    $where[] = "(
                                NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        INNER JOIN {$term_meta}
                                        ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id AND {$term_meta}.meta_key = 'created_by' AND {$term_meta}.meta_value = {$user_id}
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = P.ID
                                    )
                                )";

                    $join = apply_filters('folders_count_join_query', "");
                    $where = apply_filters('folders_count_where_query', $where);

                    $query = $select . $join . " WHERE " . implode(' AND ', $where);

                    $query = $wpdb->prepare($query, $taxonomy);
                }
            }


            $tlrcds = $wpdb->get_var($query);
        }

        if (!empty($tlrcds)) {
            return $tlrcds;
        } else {
            return 0;
        }
    }

    /**
     * Count all items of a post type shown in the "All" entry of the folder sidebar.
     *
     * Counts published, scheduled, draft, private and pending posts (or inherit
     * and private attachments). The `premio_folder_all_categorized_items` filter
     * can supply the count instead.
     *
     * @param string $post_type Post type; defaults to the current screen's post type when empty.
     * @return int|string Number of items.
     */
    public static function total_items($post_type) {
        global $typenow;
        if (empty($post_type)) {
            $post_type = $typenow;
        }

        $taxonomy = \Folders\Folders\Settings::get_folder_post_type($post_type);
        $use_cache = !has_filter('folders_count_join_query') && !has_filter('folders_count_where_query');
        $key = 'total' . self::count_context($taxonomy, $post_type);
        if ($use_cache) {
            $cached = self::get_cached_count($taxonomy, $key);
            if ($cached !== null) {
                return $cached;
            }
        }

        $count = self::query_total_items($post_type);
        if ($use_cache) {
            self::set_count_cache($taxonomy, $key, $count);
        }
        return $count;
    }

    /**
     * Count all items of a post type (uncached).
     *
     * @param string $post_type Post type.
     * @return int|string Number of items.
     */
    private static function query_total_items($post_type) {
        $item_count = null;
        if (has_filter("premio_folder_all_categorized_items")) {
            $item_count = apply_filters("premio_folder_all_categorized_items", $post_type);
        }
        if ($item_count === null) {
            if ($post_type == "attachment") {
                global $wpdb;

                $select = "SELECT COUNT(ID) FROM " . $wpdb->posts . " as P ";

                $where = ["post_type = 'attachment' "];
                $where[] = "(post_status = 'inherit' OR post_status = 'private')";

                $join = apply_filters('folders_count_join_query', "");
                $where = apply_filters('folders_count_where_query', $where);

                $query = $select . $join . " WHERE " . implode(' AND ', $where);

                $item_count = $wpdb->get_var($query);

            } else {
                $count_posts = wp_count_posts($post_type);
                $item_count = (isset($count_posts->publish) ? $count_posts->publish : 0) + 
                              (isset($count_posts->draft) ? $count_posts->draft : 0) + 
                              (isset($count_posts->future) ? $count_posts->future : 0) + 
                              (isset($count_posts->private) ? $count_posts->private : 0) + 
                              (isset($count_posts->pending) ? $count_posts->pending : 0);
            }
        }
        return $item_count;
    }
    /**
     * Move items into a folder (drag and drop from the list screen).
     *
     * Items are added to the target folder and removed from the folder currently
     * being viewed (`selected_folder_id`). The previous assignments are saved for
     * undo. Requires `edit_pages` for pages or `edit_posts` otherwise.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce              Nonce for the `folder_nonce_{folder_id}` action.
     *     @type int|string $folder_id          Target folder ID.
     *     @type string     $post_type          Post type of the items.
     *     @type array      $post_ids           IDs of the items to move.
     *     @type int|string $selected_folder_id Optional. ID or slug of the folder being viewed.
     * }
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function save_folder_items($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $parent_id = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $selected_folder_id = isset( $params['selected_folder_id'] ) ? sanitize_text_field( $params['selected_folder_id'] ) : '';
        $post_ids  = isset( $params['post_ids'] ) ? map_deep( $params['post_ids'], 'sanitize_text_field' ) : [];

        if(empty($folder_id) || empty($post_type) || empty($post_ids) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_' . $folder_id)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        } else if ($post_type == "page" && !current_user_can("edit_pages")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        } else if ($post_type != "page" && !current_user_can("edit_posts")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        $folder_title = '';
        $term = get_term($folder_id);
        if ($term) {
            if ($post_type == "attachment" || $folder_type == "media") {
                $url = admin_url("upload.php?post_type=attachment&media_folder=" . $term->slug);
            } else if ($folder_type == "plugin") {
                $url = admin_url("plugins.php?folders4plugins_folder=" . $term->slug);
            } else {
                $url = admin_url("edit.php?post_type=" . $post_type . "&" . $folder_type . "=" . $term->slug);
            }
            $folder_title = "<a href='{$url}'>{$term->title}</a>";
        }

        $folderUndoSettings = [];
        foreach ($post_ids as $post_id) {
            if (!is_numeric($post_id) || $post_id <= 0 || !current_user_can('edit_post', absint($post_id))) {
                continue;
            }
            $terms = get_the_terms($post_id, $folder_type);
            $post_terms = [
                'post_id' => $post_id,
                'terms' => $terms,
            ];
            $folderUndoSettings[] = $post_terms;
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (!empty($selected_folder_id) && isset($term->term_id) && ($term->term_id == $selected_folder_id || $term->slug == $selected_folder_id)) {
                        wp_remove_object_terms($post_id, $term->term_id, $folder_type);
                    }
                }
            }

            wp_set_post_terms($post_id, $folder_id, $folder_type, true);
        }

        delete_transient("folder_undo_settings");
        set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);

        if(!get_option("show_folder_upgrade_popup")) {
            add_option("show_folder_upgrade_popup", "hide");
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id
        );
    }

    /**
     * Move the selected items to a folder from the list screen's bulk actions.
     *
     * A `folder_id` of -1 removes the items from all folders. The previous
     * assignments are saved for undo. Requires `edit_pages` for pages or
     * `edit_posts` otherwise.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce     Nonce for the `premio_folders_bulk_action_{post_type}` action.
     *     @type string     $post_type Post type of the items.
     *     @type int|string $folder_id Target folder ID, or -1 to unassign.
     *     @type string     $post_ids  Comma-separated item IDs.
     * }
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function bulk_folder_action($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $post_ids  = isset( $params['post_ids'] ) ? sanitize_text_field( $params['post_ids'] ) : '';

        if(empty($folder_id) || empty($post_type) || empty($post_ids) || empty($nonce) || !wp_verify_nonce($nonce, 'premio_folders_bulk_action_' . $post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        } else if ($post_type == "page" && !current_user_can("edit_pages")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        } else if ($post_type != "page" && !current_user_can("edit_posts")) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        $folder_title = '';
        $term = get_term($folder_id);
        if ($term) {
            if ($post_type == "attachment" || $folder_type == "media") {
                $url = admin_url("upload.php?post_type=attachment&media_folder=" . $term->slug);
            } else if ($folder_type == "plugin") {
                $url = admin_url("plugins.php?folders4plugins_folder=" . $term->slug);
            } else {
                $url = admin_url("edit.php?post_type=" . $post_type . "&" . $folder_type . "=" . $term->slug);
            }
            $folder_title = "<a href='{$url}'>{$term->title}</a>";
        }

        $post_ids = explode(",", $post_ids);
        $folderUndoSettings = [];
        foreach ($post_ids as $post_id) {
            if (!is_numeric($post_id) || $post_id <= 0 || !current_user_can('edit_post', absint($post_id))) {
                continue;
            }
            $terms = get_the_terms($post_id, $folder_type);
            $post_terms = [
                'post_id' => $post_id,
                'terms' => $terms,
            ];
            $folderUndoSettings[] = $post_terms;
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if ($folder_id == -1) {
                        wp_remove_object_terms($post_id, $term->term_id, $folder_type);
                    }
                    else if (!empty($selected_folder_id) && isset($term->term_id) && ($term->term_id == $selected_folder_id || $term->slug == $selected_folder_id)) {
                        wp_remove_object_terms($post_id, $term->term_id, $folder_type);
                    }
                }
            }

            if($folder_id != -1) {
                wp_set_post_terms($post_id, $folder_id, $folder_type, true);
            }
        }

        delete_transient("folder_undo_settings");
        set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id
        );
    }
}
