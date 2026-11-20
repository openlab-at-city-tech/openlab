<?php
namespace FileBird\Addons\PostType\Models;

use FileBird\Addons\PostType\Init;

class Main {
    const ORDER_META_KEY      = 'fbv_tax_order';
    const AUTHOR_KEY          = 'fbv_author';

    public static function getFolders( $post_type, $order_by = null ) {
        $args = array(
            'taxonomy'   => self::getTaxonomyName( $post_type ),
            'hide_empty' => false,
            'meta_key'   => self::ORDER_META_KEY,
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
            'meta_query' => array(
                array(
                    'key'     => self::AUTHOR_KEY,
                    'value'   => apply_filters( 'fbv_folder_created_by', 0 ),
                    'compare' => '=',
                ),
            ),
        );

        $terms = get_terms( $args );

        if ( ! is_null( $order_by ) && in_array( $order_by, array( 'asc', 'desc' ) ) ) {
            usort( $terms, array( __CLASS__, "sort_natural_$order_by" ) );
        }

        return $terms;
    }

    private static function sort_natural_asc( $a, $b ) {
		return strnatcasecmp( $a->name, $b->name );
	}

	private static function sort_natural_desc( $a, $b ) {
		return strnatcasecmp( $a->name, $b->name ) * -1;
	}
    private function getMaxOrderOfPostType( $post_type, $parent = 0 ) {
        $taxonomy = self::getTaxonomyName( $post_type );
        global $wpdb;

        $order = $wpdb->get_var(
            $wpdb->prepare(
            "SELECT MAX(CONVERT(tm1.meta_value, UNSIGNED)) FROM {$wpdb->prefix}term_taxonomy as tt
            INNER JOIN {$wpdb->prefix}termmeta as tm ON tt.term_id = tm.term_id AND tm.meta_key = %s AND tm.meta_value = %d
            INNER JOIN {$wpdb->prefix}termmeta as tm1 ON tm1.term_id = tt.term_id AND tm1.meta_key = %s
            WHERE tt.taxonomy = %s AND tt.parent = %d
        ",
            self::AUTHOR_KEY,
            apply_filters( 'fbv_folder_created_by', 0 ),
            self::ORDER_META_KEY,
            $taxonomy,
            $parent
        )
        );
        return $order;
    }
    public function createFolder( $folder_name, $post_type, $parent = 0 ) {
        $taxonomy = self::getTaxonomyName( $post_type );

        $res = wp_insert_term(
            $folder_name,
            $taxonomy,
            array(
				'parent' => $parent,
				'slug'   => sanitize_title( $folder_name ) . '-' . apply_filters( 'fbv_folder_created_by', 0 ),
			)
        );

        if ( is_wp_error( $res ) ) {
            if ( $res->get_error_code() === 'term_exists' ) {
                return $res->get_error_data();
            }
        } else {
            $order = $this->getMaxOrderOfPostType( $post_type, $parent );

            update_term_meta( $res['term_id'], self::ORDER_META_KEY, is_null( $order ) ? 0 : ( intval( $order ) + 1 ) );
            update_term_meta( $res['term_id'], self::AUTHOR_KEY, apply_filters( 'fbv_folder_created_by', 0 ) );

            return $res['term_id'];
        }
    }

    public function assignFolder( $folder_id, $post_type, $post_ids ) {
        $taxonomy = self::getTaxonomyName( $post_type );

		foreach ( $post_ids as $id ) {
			if ( $folder_id > 0 ) {
				wp_set_post_terms( (int) $id, array( $folder_id ), $taxonomy );
			} elseif ( $folder_id == 0 ) {
				$old_folders = wp_get_post_terms( (int) $id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( is_array( $old_folders ) && count( $old_folders ) > 0 ) {
					wp_remove_object_terms( (int) $id, $old_folders, $taxonomy );
				}
			}
		}
    }

    public static function getFolderOfPost( $post_type, $post_id, $term_query = array() ) {
        $terms = wp_get_post_terms( (int) $post_id, self::getTaxonomyName( $post_type ), $term_query );
        return isset( $terms[0] ) ? $terms[0] : null;
    }

    public static function convertFormat( $term, $colors = array() ) {
        return json_decode(
            wp_json_encode(
                array(
					'id'         => intval( $term->term_id ),
                    'key'        => intval( $term->term_id ),
					'title'      => $term->name,
					'text'       => $term->name,
                    'data-id'    => intval( $term->term_id ),
					'data-count' => 0,
                    'color'      => $colors[ $term->term_id ] ?? '',
					'parent'     => intval( $term->parent ),
					'children'   => array(),
                )
            )
        );
    }

    public static function sortTerms( &$terms, &$out_put, $parent_id = 0 ) {
		foreach ( $terms as $i => $cat ) {
            if ( $cat->parent == $parent_id ) {
                $out_put[] = $cat;
                unset( $terms[ $i ] );
            }
		}

		foreach ( $out_put as $topCat ) {
			$topCat->children = array();
			self::sortTerms( $terms, $topCat->children, $topCat->id );
		}
    }

    public static function getTaxonomyName( $post_type ) {
        return Init::PREFIX . $post_type;
    }

    public static function isFolderExist( $folder_id, $taxonomy ) {
        $isExist = get_terms(
            array(
                'fields'       => 'ids',
				'include'      => array( $folder_id ),
                'hide_empty'   => false,
				'taxonomy'     => $taxonomy,
                'meta_key'     => self::AUTHOR_KEY,
                'meta_value'   => apply_filters( 'fbv_folder_created_by', 0 ),
                'meta_compare' => '=',
			)
        );

        return count( $isExist ) > 0 ? true : false;
    }

    private function folderNameExists( $folder_name, $post_type, $parent ) {
        $terms = get_terms(
            array(
                'fields'       => 'ids',
				'name'         => $folder_name,
				'parent'       => $parent,
                'hide_empty'   => false,
				'taxonomy'     => self::getTaxonomyName( $post_type ),
                'meta_key'     => self::AUTHOR_KEY,
                'meta_value'   => apply_filters( 'fbv_folder_created_by', 0 ),
                'meta_compare' => '=',
			)
        );
        return count( $terms ) > 0;
    }   

    public function duplicateFolder( $folder_id, $post_type, $lang ) {
        //delete all terms
        // $all = get_terms(
        //     array(
        //         'taxonomy' => self::getTaxonomyName( $post_type ),
        //         'hide_empty' => false,
        //     )
        // );
        // foreach ( $all as $term ) {
        //     wp_delete_term( $term->term_id, self::getTaxonomyName( $post_type ) );
        // }
        // exit('Deleted');
        
        $folder = get_term( $folder_id, self::getTaxonomyName( $post_type ) );
        if( is_wp_error( $folder ) || !$folder ) {
            return false;
        }
        $new_folder_name = substr($folder->name, 0, 190) . ' ' . esc_html__( '(Copy)', 'filebird' );
        $new_folder_id = $this->createFolderWithUniqueName( $new_folder_name, $post_type, $folder->parent );
        $i = 1;
        while( ! is_numeric( $new_folder_id ) ) {
            $new_folder_name = substr($folder->name, 0, 190) . ' ' . esc_html__( '(Copy)', 'filebird' ) . ' ' . $i;
            $new_folder_id = $this->createFolderWithUniqueName( $new_folder_name, $post_type, $folder->parent );
            $i++;
        }

        $order = $this->getMaxOrderOfPostType( $post_type, $folder->parent );
        update_term_meta( $new_folder_id, self::AUTHOR_KEY, get_term_meta( $folder_id, self::AUTHOR_KEY, true ) );
        update_term_meta( $new_folder_id, self::ORDER_META_KEY, is_null( $order ) ? 0 : ( intval( $order ) + 1 ) );
        //duplicate children
        $this->duplicateChildren( $folder_id, $new_folder_id, $post_type, $lang );

        return $new_folder_id;
    }
    public function duplicateChildren( $folder_id, $new_parent_id, $post_type, $lang ) {
        $children = get_terms(
            array(
                'taxonomy' => self::getTaxonomyName( $post_type ),
                'parent'   => $folder_id,
                'hide_empty' => false,
            )
        );
        if( ! is_wp_error( $children ) ) {
            foreach ( $children as $child ) {
                $new_folder_id = $this->createFolderWithUniqueName( $child->name, $post_type, $new_parent_id );
                //update author
                update_term_meta( $new_folder_id, self::AUTHOR_KEY, get_term_meta( $child->term_id, self::AUTHOR_KEY, true ) );
                //update order
                $order = $this->getMaxOrderOfPostType( $post_type, $new_parent_id );
                update_term_meta( $new_folder_id, self::ORDER_META_KEY, is_null( $order ) ? 0 : ( intval( $order ) + 1 ) );

                $this->duplicateChildren( $child->term_id, $new_folder_id, $post_type, $lang );
            }
        }
    }
    private function createFolderWithUniqueName( $folder_name, $post_type, $parent ) {
        $taxonomy = self::getTaxonomyName( $post_type );

        $res = wp_insert_term(
            $folder_name,
            $taxonomy,
            array(
				'parent' => $parent,
				'slug'   => sanitize_title( $folder_name ) . '-' . apply_filters( 'fbv_folder_created_by', 0 ),
			)
        );
        if( is_wp_error( $res ) ) {
            return $res;
        }
        return $res['term_id'];
    }
}
