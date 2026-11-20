<?php
namespace FileBird\Controller\Import\PostType;

defined( 'ABSPATH' ) || exit;

use FileBird\Classes\Helpers;

abstract class PluginImporter {
    public $post_types;

    public function __construct( $post_types ) {
        $this->post_types = $post_types;
    }

    abstract public function get_taxonomy( $post_type );

    public function get_folders() {
        return array_reduce(
            $this->post_types,
            function( $folders, $post_type ) {
                $taxonomy = $this->get_taxonomy( $post_type );
                $query    = new \WP_Term_Query(
                    array(
                        'taxonomy'   => $taxonomy,
                        'hide_empty' => false,
                    )
                );
                $terms    = $query->get_terms();
                if ( ! empty( $terms ) ) {
                    $terms                            = array_combine( array_column( $terms, 'term_id' ), $terms );
                    $folders[ $post_type ]['folders'] = $this->generate_tree( array_combine( array_column( $terms, 'term_id' ), $terms ) );
                    $folders[ $post_type ]['count']   = count( $terms );
                    $folders[ $post_type ]['posts']   = $this->get_related_posts( $taxonomy );
                }
                return $folders;
            },
            array()
        );
    }

    protected function generate_tree( &$elements, $parentId = 0 ) {
        $branch = array();

        foreach ( $elements as &$element ) {
            if ( $element->parent == $parentId ) {
                $children = $this->generate_tree( $elements, $element->term_id );
                if ( $children ) {
                    $element->children = $children;
                }
                $branch[ $element->term_id ] = $element;
                unset( $elements[ $element->term_id ] );
            }
        }
        return $branch;
    }

    protected function get_related_posts( $taxonomy ) {
        global $wpdb;

        $postStatuses = Helpers::array_to_in_clause( array_keys( get_post_statuses() ) );

        $join = $wpdb->prepare(
            "INNER JOIN $wpdb->term_taxonomy as term_taxonomy ON terms.term_id = term_taxonomy.term_id AND term_taxonomy.taxonomy = %s
            INNER JOIN $wpdb->term_relationships as term_relationships ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
            INNER JOIN $wpdb->posts as posts ON posts.ID = term_relationships.object_id AND (posts.post_status IN ($postStatuses))",
            $taxonomy
        );

        $query = "SELECT terms.term_id, GROUP_CONCAT(DISTINCT term_relationships.object_id) as post_ids
            FROM $wpdb->terms as terms $join GROUP BY (terms.term_id)";

        return $wpdb->get_results( $query, OBJECT_K );
    }
}
