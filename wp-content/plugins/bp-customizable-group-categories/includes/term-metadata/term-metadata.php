<?php

/*
 * For versions of WordPress < 4.4
 * Supports term metadata
 */

global $wpdb;

if (!$wpdb->termmeta) {
        
    $wpdb->tables[] = 'termmeta';
    $wpdb->termmeta = $wpdb->prefix . 'termmeta';
}

if (!function_exists('add_term_meta')):

    /**
     * Adds metadata to a term.
     *
     * @since 4.4.0
     *
     * @param int    $term_id    Term ID.
     * @param string $meta_key   Metadata name.
     * @param mixed  $meta_value Metadata value.
     * @param bool   $unique     Optional. Whether to bail if an entry with the same key is found for the term.
     *                           Default false.
     * @return int|WP_Error|bool Meta ID on success. WP_Error when term_id is ambiguous between taxonomies.
     *                           False on failure.
     */
    function add_term_meta($term_id, $meta_key, $meta_value, $unique = false) {

        if (wp_term_is_shared($term_id)) {
            return new WP_Error('ambiguous_term_id', __('Term meta cannot be added to terms that are shared between taxonomies.'), $term_id);
        }

        $added = add_metadata('term', $term_id, $meta_key, $meta_value, $unique);

        // Bust term query cache.
        if ($added) {
            wp_cache_set('last_changed', microtime(), 'terms');
        }

        return $added;
    }

endif;

if (!function_exists('delete_term_meta')):

    /**
     * Removes metadata matching criteria from a term.
     *
     * @since 4.4.0
     *
     * @param int    $term_id    Term ID.
     * @param string $meta_key   Metadata name.
     * @param mixed  $meta_value Optional. Metadata value. If provided, rows will only be removed that match the value.
     * @return bool True on success, false on failure.
     */
    function delete_term_meta($term_id, $meta_key, $meta_value = '') {

        $deleted = delete_metadata('term', $term_id, $meta_key, $meta_value);

        // Bust term query cache.
        if ($deleted) {
            wp_cache_set('last_changed', microtime(), 'terms');
        }

        return $deleted;
    }

endif;

if (!function_exists('get_term_meta')):

    /**
     * Retrieves metadata for a term.
     *
     * @since 4.4.0
     *
     * @param int    $term_id Term ID.
     * @param string $key     Optional. The meta key to retrieve. If no key is provided, fetches all metadata for the term.
     * @param bool   $single  Whether to return a single value. If false, an array of all values matching the
     *                        `$term_id`/`$key` pair will be returned. Default: false.
     * @return mixed If `$single` is false, an array of metadata values. If `$single` is true, a single metadata value.
     */
    function get_term_meta($term_id, $key = '', $single = false) {

        return get_metadata('term', $term_id, $key, $single);
    }

endif;

if (!function_exists('update_term_meta')):

    /**
     * Updates term metadata.
     *
     * Use the `$prev_value` parameter to differentiate between meta fields with the same key and term ID.
     *
     * If the meta field for the term does not exist, it will be added.
     *
     * @since 4.4.0
     *
     * @param int    $term_id    Term ID.
     * @param string $meta_key   Metadata key.
     * @param mixed  $meta_value Metadata value.
     * @param mixed  $prev_value Optional. Previous value to check before removing.
     * @return int|WP_Error|bool Meta ID if the key didn't previously exist. True on successful update.
     *                           WP_Error when term_id is ambiguous between taxonomies. False on failure.
     */
    function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') {

        if (wp_term_is_shared($term_id)) {
            return new WP_Error('ambiguous_term_id', __('Term meta cannot be added to terms that are shared between taxonomies.'), $term_id);
        }

        $updated = update_metadata('term', $term_id, $meta_key, $meta_value, $prev_value);

        // Bust term query cache.
        if ($updated) {
            wp_cache_set('last_changed', microtime(), 'terms');
        }

        return $updated;
    }

endif;

if (!function_exists('update_termmeta_cache')):

    /**
     * Updates metadata cache for list of term IDs.
     *
     * Performs SQL query to retrieve all metadata for the terms matching `$term_ids` and stores them in the cache.
     * Subsequent calls to `get_term_meta()` will not need to query the database.
     *
     * @since 4.4.0
     *
     * @param array $term_ids List of term IDs.
     * @return array|false Returns false if there is nothing to update. Returns an array of metadata on success.
     */
    function update_termmeta_cache($term_ids) {

        return update_meta_cache('term', $term_ids);
    }

endif;

if (!function_exists('wp_term_is_shared')):

    /**
     * Determine whether a term is shared between multiple taxonomies.
     *
     * Shared taxonomy terms began to be split in 4.3, but failed cron tasks or other delays in upgrade routines may cause
     * shared terms to remain.
     *
     * @since 4.4.0
     *
     * @param int $term_id
     * @return bool
     */
    function wp_term_is_shared($term_id) {
        global $wpdb;

        if (get_option('finished_splitting_shared_terms')) {
            return false;
        }

        $tt_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE term_id = %d", $term_id));

        return $tt_count > 1;
    }




    
endif;