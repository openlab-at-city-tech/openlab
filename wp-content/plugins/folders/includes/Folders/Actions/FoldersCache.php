<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Clears cached folder counts when they can have changed.
 *
 * Folder counts (items per folder, "Unassigned" and the total) only change
 * when an item is added to or removed from a folder, changes status (for
 * example draft to published, or into the trash), is created or is deleted.
 * Updating a post's title or content does not change them, so saving a post
 * no longer clears the cache. Only the counts of the affected post type's
 * folder taxonomy are cleared.
 */
class FoldersCache {

    /**
     * Register the hooks that clear the folder count cache.
     */
    public function __construct() {
        // Folder assignments changed.
        add_action('set_object_terms', [$this, "object_terms_changed"], 10, 6);
        add_action('deleted_term_relationships', [$this, "term_relationships_deleted"], 10, 3);
        add_action('delete_term', [$this, "term_deleted"], 10, 3);

        // Status changes of posts (attachments do not fire this, see below).
        add_action('transition_post_status', [$this, "post_status_changed"], 10, 3);

        // Language changed (WPML; Polylang uses its "language" taxonomy,
        // handled in object_terms_changed()).
        add_action('wpml_translation_update', [$this, "wpml_translation_changed"]);

        // Items created, trashed, restored or deleted.
        add_action('add_attachment', [$this, "post_changed"]);
        add_action('delete_attachment', [$this, "post_changed"]);
        add_action('wp_trash_post', [$this, "post_changed"]);
        add_action('untrashed_post', [$this, "post_changed"]);
        add_action('before_delete_post', [$this, "post_changed"]);
    }

    /**
     * Whether a taxonomy is one of the plugin's folder taxonomies.
     *
     * @param string $taxonomy Taxonomy slug.
     * @return bool
     */
    private static function is_folder_taxonomy($taxonomy)
    {
        return is_string($taxonomy) && ($taxonomy === 'folder' || substr($taxonomy, -7) === '_folder');
    }

    /**
     * Clear the counts of a post type's folders, if folders are enabled for it.
     *
     * @param string $post_type Post type.
     * @return void
     */
    private static function flush_for_post_type($post_type)
    {
        $taxonomy = FoldersItems::folder_taxonomy_for_post_type($post_type);
        if (!empty($taxonomy)) {
            FoldersItems::flush_count_cache($taxonomy);
        }
    }

    /**
     * Clear the counts when items are assigned to folders.
     *
     * Skipped when the assignment did not change.
     *
     * @param int    $object_id  Object ID.
     * @param array  $terms      Terms.
     * @param array  $tt_ids     New term taxonomy IDs.
     * @param string $taxonomy   Taxonomy slug.
     * @param bool   $append     Whether terms were appended.
     * @param array  $old_tt_ids Previous term taxonomy IDs.
     * @return void
     */
    public function object_terms_changed($object_id, $terms, $tt_ids, $taxonomy, $append = false, $old_tt_ids = [])
    {
        if ($taxonomy === 'language' || $taxonomy === 'post_translations') {
            // Polylang: the item's language changed, which changes the
            // language-specific counts of its post type.
            if ($tt_ids != $old_tt_ids) {
                $this->post_changed($object_id);
            }
            return;
        }

        if (!self::is_folder_taxonomy($taxonomy)) {
            return;
        }

        $new_ids = array_map('intval', (array) $tt_ids);
        $old_ids = array_map('intval', (array) $old_tt_ids);
        sort($new_ids);
        sort($old_ids);
        if ($new_ids === $old_ids) {
            return;
        }

        FoldersItems::flush_count_cache($taxonomy);
    }

    /**
     * Clear the counts when items are removed from folders.
     *
     * @param int    $object_id Object ID.
     * @param array  $tt_ids    Term taxonomy IDs.
     * @param string $taxonomy  Taxonomy slug.
     * @return void
     */
    public function term_relationships_deleted($object_id, $tt_ids, $taxonomy)
    {
        if (self::is_folder_taxonomy($taxonomy)) {
            FoldersItems::flush_count_cache($taxonomy);
        }
    }

    /**
     * Clear the counts when a folder is deleted.
     *
     * @param int    $term     Term ID.
     * @param int    $tt_id    Term taxonomy ID.
     * @param string $taxonomy Taxonomy slug.
     * @return void
     */
    public function term_deleted($term, $tt_id, $taxonomy)
    {
        if (self::is_folder_taxonomy($taxonomy)) {
            FoldersItems::flush_count_cache($taxonomy);
        }
    }

    /**
     * Clear the counts when a post's status changes.
     *
     * @param string   $new_status New post status.
     * @param string   $old_status Previous post status.
     * @param \WP_Post $post       Post object.
     * @return void
     */
    public function post_status_changed($new_status, $old_status, $post)
    {
        if ($new_status === $old_status || !isset($post->post_type)) {
            return;
        }
        self::flush_for_post_type($post->post_type);
    }

    /**
     * Clear the counts when WPML changes an item's language or translations.
     *
     * @param array $args Update details from WPML, with `element_id` and `element_type` (for example "post_page").
     * @return void
     */
    public function wpml_translation_changed($args)
    {
        if (!is_array($args) || empty($args['element_type']) || strpos($args['element_type'], 'post_') !== 0) {
            return;
        }
        self::flush_for_post_type(substr($args['element_type'], 5));
    }

    /**
     * Clear the counts when an item is added, trashed, restored or deleted.
     *
     * @param int $post_id Post ID.
     * @return void
     */
    public function post_changed($post_id)
    {
        $post_type = get_post_type($post_id);
        if ($post_type) {
            self::flush_for_post_type($post_type);
        }
    }
}
