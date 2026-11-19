<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Filters admin list queries by the selected folder.
 *
 * Turns the folder taxonomy query argument into a tax query that matches only
 * the selected folder (not its subfolders), or, for the value -1, items that
 * are not in any folder ("Unassigned").
 */
class FoldersFilter {

    /**
     * Register the `pre_get_posts` filter.
     */
    public function __construct() {
        add_filter('pre_get_posts', [$this, 'filter_record_list']);
    }

    /**
     * Restrict a posts query to the folder selected in the request.
     *
     * Only applies to post types with folders enabled and when the folder
     * taxonomy parameter is present in the request.
     *
     * @param \WP_Query $query Query being prepared.
     * @return \WP_Query Modified query.
     */
    public function filter_record_list($query)
    {
        global $typenow;

        if (!isset($query->query['post_type'])) {
            return $query;
        }

        $status = \Folders\Folders\Settings::check_for_folder($query->query['post_type']);
        if (!$status) {
            return $query;
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($query->query['post_type']);

        if (!isset($_REQUEST[$folder_type])) {
            return $query;
        }

        $term = sanitize_text_field($_REQUEST[$folder_type]);
        if ($term === "") {
            return $query;
        }

        if ($term != -1) {
            if(empty($query->query_vars[$folder_type])) {
                return $query;
            }
            unset($query->query_vars[$folder_type]);
            $tax_query = $query->get('tax_query') ?: [];
            $tax_query[] = [
                'taxonomy'         => $folder_type,
                'field'            => is_numeric($term) ? 'term_id' : 'slug',
                'terms'            => $term,
                'include_children' => false,
            ];
            $query->set('tax_query', $tax_query);
            return $query;
        }

        unset($query->query_vars[$folder_type]);

        $user_filter = false;

        if ($user_filter) {

            $args = array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array(
                    array(
                        'key' => 'created_by',
                        'value' => $user_id,
                        'compare' => '='
                    )
                ),
                'taxonomy' => $folder_type,
            );
            $terms = get_terms($args);

            $termIds = [];
            if (! empty( $terms ) && ! is_wp_error( $terms )) {
                foreach ($terms as $term) {
                    if (isset($term->term_id)) {
                        $termIds[] = $term->term_id;
                    }
                }
            }

            if (!empty($termIds)) {
                $tax_query = [
                    'relation' => 'OR',
                    array(
                        'taxonomy' => $folder_type,
                        'operator' => 'NOT EXISTS'
                    ),
                    [
                        'taxonomy' => $folder_type,
                        'field' => 'id',
                        'terms' => $termIds,
                        'operator' => 'NOT IN'
                    ]
                ];
            } else {
                $tax_query = [
                    'taxonomy' => $folder_type,
                    'operator' => 'NOT EXISTS',
                ];
            }

        } else {
            $tax_query = [
                'taxonomy' => $folder_type,
                'operator' => 'NOT EXISTS',
            ];
        }

        $query->set('tax_query', [$tax_query]);
        $query->tax_query = new \WP_Tax_Query([$tax_query]);

        return $query;

    }
}
