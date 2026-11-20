<?php
namespace FileBird\Addons\PostType\Controllers;

use FileBird\Addons\PostType\Init;
use FileBird\Addons\PostType\Models\Main as MainModel;
use FileBird\Classes\Helpers;
use FileBird\Model\SettingModel;

class Main {
    private $model;

    public function __construct() {
         $this->model = new MainModel();
    }

    public function restGetFolders( \WP_REST_Request $request ) {
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
        $order     = strtolower( sanitize_text_field( $request->get_param( 'order' ) ) ) ?: null;
        $orderby   = strtolower( sanitize_text_field( $request->get_param( 'orderby' ) ) ) ?: null;
        $lang      = sanitize_key( $request->get_param( 'language' ) );
        $user_id   = get_current_user_id();

        if ( Init::getInstance()->isPostTypeAvailable( $post_type ) ) {
            $config = Init::getInstance()->load_config( $post_type, $user_id );

            if ( $order === 'reset' ) {
				$config->deleteSetting( 'DEFAULT_SORT_FOLDERS' );
			} elseif ( ! empty( $orderby ) && ! empty( $order ) ) {
                $config->setSetting(
                    'DEFAULT_SORT_FOLDERS',
                    array(
                        'orderby' => $orderby,
                        'order'   => $order,
                    )
                );
            }

            $colors = get_option( "fbv_{$post_type}_folder_colors", array() );
            $raw_terms = MainModel::getFolders( $post_type, $order );
            $top_level_terms = [];
            if (!empty($raw_terms) && !is_wp_error($raw_terms)) {
                foreach ($raw_terms as $term) {
                    if ($term->parent == 0) {
                        $top_level_terms[] = $term;
                    }
                }
            }
            $terms  = array_map(
                function ( $term ) use ( $colors ) {
                    return MainModel::convertFormat( $term, $colors );
                },
                $top_level_terms
            );

            $tree = array();
            MainModel::sortTerms( $terms, $tree, 0 );

            $result = array(
                'allAttachmentsCount' => self::getCountOfPost( $post_type, $lang ),
                'attachmentsCount'    => self::getCountFromTerms( $post_type, $lang ),
                'tree'                => $tree,
            );

            return rest_ensure_response( $result );
        }

        return new \WP_Error( 'post_type_not_enable', __( 'Post Type not enabled.', 'filebird' ) );
    }

    public function restNewFolder( \WP_REST_Request $request ) {
        $post_type   = sanitize_text_field( $request->get_param( 'post_type' ) );
        $folder_name = sanitize_text_field( $request->get_param( 'title' ) );
        $parent      = (int) $request->get_param( 'parent' );

		if ( $this->folderNameExists( $folder_name, $post_type, $parent ) ) {
		    return new \WP_Error( 'folder_name_exist', __( 'A folder with this name already exists. Please choose another one.', 'filebird' ) );
		}

        $res = wp_insert_term(
             $folder_name,
            MainModel::getTaxonomyName( $post_type ),
            array(
				'parent' => $parent,
				'slug'   => sanitize_title( $folder_name ) . '-' . apply_filters( 'fbv_folder_created_by', 0 ),
			)
        );

        if ( is_wp_error( $res ) ) {
            return new \WP_Error( 'folder_name_exist', __( 'A folder with this name already exists. Please choose another one.', 'filebird' ) );
        } else {
            global $wpdb;

            $order = $wpdb->get_var(
                $wpdb->prepare(
                "SELECT MAX(CONVERT(tm1.meta_value, UNSIGNED)) FROM {$wpdb->prefix}term_taxonomy as tt
                INNER JOIN {$wpdb->prefix}termmeta as tm ON tt.term_id = tm.term_id AND tm.meta_key = %s AND tm.meta_value = %d
                INNER JOIN {$wpdb->prefix}termmeta as tm1 ON tm1.term_id = tt.term_id AND tm1.meta_key = %s
                WHERE tt.taxonomy = %s AND tt.parent = %d
            ",
                MainModel::AUTHOR_KEY,
                apply_filters( 'fbv_folder_created_by', 0 ),
                MainModel::ORDER_META_KEY,
                MainModel::getTaxonomyName( $post_type ),
                $parent
            )
                );

            update_term_meta( $res['term_id'], MainModel::ORDER_META_KEY, is_null( $order ) ? 0 : ( intval( $order ) + 1 ) );
            update_term_meta( $res['term_id'], MainModel::AUTHOR_KEY, apply_filters( 'fbv_folder_created_by', 0 ) );

            return rest_ensure_response(
                array(
                    array(
                        'id'         => $res['term_id'],
                        'key'        => $res['term_id'],
                        'data-id'    => $res['term_id'],
                        'parent'     => $parent,
                        'data-count' => 0,
                        'children'   => array(),
                        'title'      => $folder_name,
                    )
                )
            );
        }
    }

    public function restEditFolder( \WP_REST_Request $request ) {
        $post_type   = sanitize_text_field( $request->get_param( 'post_type' ) );
        $folder_name = sanitize_text_field( $request->get_param( 'title' ) );
        $id          = (int) $request->get_param( 'id' );
        $parent      = (int) $request->get_param( 'parent' );

        if ( ! self::hasAccess( $id ) ) {
            return new \WP_Error( 'folder_update_fail', __( 'Could not edit this folder (author error)', 'filebird' ) );
        }

        if ( $this->folderNameExists( $folder_name, $post_type, $parent, $id ) ) {
			return new \WP_Error( 'folder_name_exist', __( 'A folder with this name already exists. Please choose another one.', 'filebird' ) );
        }

        $res = wp_update_term(
            $id,
            MainModel::getTaxonomyName( $post_type ),
            array(
                'name'   => $folder_name,
                'parent' => $parent,
            )
        );

        if ( is_wp_error( $res ) ) {
            return new \WP_Error( 'folder_update_fail', __( 'Could not edit this folder', 'filebird' ) );
        } else {
            return rest_ensure_response( true );
        }
    }

    public function restDeleteFolder( \WP_REST_Request $request ) {
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
        $ids       = $request->get_param( 'ids' );
        $ids       = isset( $ids ) ? Helpers::sanitize_array( $ids ) : '';
        $lang      = sanitize_key( $request->get_param( 'language' ) );

        if ( $ids != '' ) {
            if ( ! is_array( $ids ) ) {
                $ids = array( $ids );
            }
            $ids = array_map( 'intval', $ids );

            foreach ( $ids as $id ) {
                if ( $id > 0 && self::hasAccess( $id ) ) {
                    $children = get_term_children( $id, MainModel::getTaxonomyName( $post_type ) );
                    foreach ( $children as $child ) {
                        wp_delete_term( $child, MainModel::getTaxonomyName( $post_type ) );
                    }
                    wp_delete_term( $id, MainModel::getTaxonomyName( $post_type ) );
                }
            }

            return rest_ensure_response( self::getCountFromTerms( $post_type, $lang ) );
        }

        return new \WP_Error( 'delete_fail', __( 'Can\'t delete folder, please try again later', 'filebird' ) );
    }

    public function restSetFolder( \WP_REST_Request $request ) {
        $ids       = $request->get_param( 'ids' );
        $folder    = (int) $request->get_param( 'folderId' );
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
        $lang      = sanitize_key( $request->get_param( 'language' ) );
        //TODO user mode
        $ids = isset( $ids ) ? Helpers::sanitize_array( $ids ) : '';
        if ( $folder > 0 && ! self::hasAccess( $folder ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'Permisson Denied.', 'filebird' ),
                array( 'status' => rest_authorization_required_code() )
            );
        }
        if ( $ids != '' && is_array( $ids ) ) {
            if ( ! Helpers::canEditObjects( $ids, $post_type ) ) {
                return new \WP_Error(
                    'rest_forbidden',
                    __( 'Sorry, you are not allowed to edit one or more of these items.', 'filebird' ),
                    array( 'status' => rest_authorization_required_code() )
                );
            }

            $this->model->assignFolder( $folder, $post_type, $ids );
            return rest_ensure_response( self::getCountFromTerms( $post_type, $lang ) );
        }

        return new \WP_Error( 'validation_failed', __( 'Validation failed', 'filebird' ) );
    }

    public function restSetSetting( \WP_REST_Request $request ) {
        $params    = $request->get_params();
        $user_id   = get_current_user_id();
        $post_type = sanitize_text_field( $params['post_type'] );

        $config   = Init::getInstance()->load_config( $post_type, $user_id );
        $settings = $config->getAllSettings();

        foreach ( $settings as $key => $value ) {
            if ( isset( $params[ $key ] ) ) {
                $config->setSetting( $key, $params[ $key ] );
            }
        }

        return rest_ensure_response( true );
    }

    public function restSetFolderColor( \WP_REST_Request $request ) {
        $id        = (int) $request->get_param( 'folderId' );
		$color     = sanitize_hex_color( $request->get_param( 'color' ) );
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );

        if ( empty( $post_type ) ) {
            return new \WP_Error( 'post_type_not_enable', __( 'Post Type not enabled.', 'filebird' ) );
        }

        if ( $id <= 0 || ! self::hasAccess( $id ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'Permisson Denied.', 'filebird' ),
                array( 'status' => rest_authorization_required_code() )
            );
        }

        $option_key    = "fbv_{$post_type}_folder_colors";
		$option        = get_option( $option_key, array() );
		$option[ $id ] = $color;

		update_option( $option_key, $option, false );

		return rest_ensure_response( true );
    }

    public function restUpdateTree( \WP_REST_Request $request ) {
        global $wpdb;

        $post_type = Helpers::sanitize_array( $request->get_param( 'post_type' ) );

        if ( Init::getInstance()->isPostTypeAvailable( $post_type ) ) {
            global $wpdb;

            $data = array(
                'dropPosition' => $request->get_param( 'dropPosition' ),
                'dropNodeId'   => $request->get_param( 'dropNodeId' ),
                'dragNodeId'   => $request->get_param( 'dragNodeId' ),
                'toParentId'   => $request->get_param( 'toParentId' ),
            );

            foreach ( $data as $param ) {
                if ( strlen( $param ) === 0 || ! is_numeric( $param ) ) {
                    return new \WP_Error( 'invalid_params', __( 'Invalid params', 'filebird' ), array( 'status' => 400 ) );
                }
            }

            $new_parent_id = ( 0 === (int) $data['dropPosition'] )
                ? (int) $data['dropNodeId']
                : (int) $data['toParentId'];

            if ( ! self::hasAccess( (int) $data['dragNodeId'] )
                || ( $new_parent_id > 0 && ! self::hasAccess( $new_parent_id ) ) ) {
                return new \WP_Error(
                    'rest_forbidden',
                    __( 'Permisson Denied.', 'filebird' ),
                    array( 'status' => rest_authorization_required_code() )
                );
            }

            // wp_update_term() would silently drop such a move; reject it here
            // so the caller gets an explicit error instead of a success response.
            if ( self::isSelfOrDescendant( (int) $data['dragNodeId'], $new_parent_id, $post_type ) ) {
                return new \WP_Error(
                    'invalid_parent',
                    __( 'A folder cannot be moved into itself or into one of its subfolders.', 'filebird' ),
                    array( 'status' => 400 )
                );
            }

            // Drop folder inside another folder
            if ( 0 === $data['dropPosition'] ) {
                $children = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT tm1.`term_id` as id, tm1.`meta_value` as ord
                        FROM {$wpdb->term_taxonomy} as tt
                        INNER JOIN {$wpdb->termmeta} as tm 
                            ON tt.term_id = tm.term_id AND tt.taxonomy = %s 
                            AND parent = %d AND tm.meta_key = 'fbv_author' 
                            AND tm.meta_value = %d 
                            AND tt.term_id != %d
                        INNER JOIN {$wpdb->termmeta} as tm1 
                            ON tt.term_id = tm1.term_id AND tm1.meta_key = 'fbv_tax_order'",
                        MainModel::getTaxonomyName( $post_type ),
                        $data['dropNodeId'],
                        apply_filters( 'fbv_folder_created_by', 0 ),
                        $data['dragNodeId']
                    )
                );

                array_unshift(
                    $children,
                    (object) array(
						'id'  => $data['dragNodeId'],
						'ord' => 0,
					)
                );

                foreach ( $children as $key => $child ) {
					update_term_meta( $child->id, 'fbv_tax_order', $key + 1 );
					wp_update_term( $child->id, MainModel::getTaxonomyName( $post_type ), array( 'parent' => $data['dropNodeId'] ) );
                }
            }

            if ( 1 === $data['dropPosition'] ) {
                $children = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT tm1.`term_id` as id, tm1.`meta_value` as ord
                        FROM {$wpdb->term_taxonomy} as tt
                        INNER JOIN {$wpdb->termmeta} as tm 
                            ON tt.term_id = tm.term_id AND tt.taxonomy = %s 
                            AND parent = %d AND tm.meta_key = 'fbv_author' 
                            AND tm.meta_value = %d 
                            AND tt.term_id != %d
                        INNER JOIN {$wpdb->termmeta} as tm1 
                            ON tt.term_id = tm1.term_id AND tm1.meta_key = 'fbv_tax_order'
                        ORDER BY ord+0 ASC",
                        MainModel::getTaxonomyName( $post_type ),
                        $data['toParentId'],
                        apply_filters( 'fbv_folder_created_by', 0 ),
                        $data['dragNodeId']
                    )
                );

                $ord = 0;

                foreach ( $children as $child ) {
                    update_term_meta( $child->id, 'fbv_tax_order', $ord++ );
					wp_update_term( $child->id, MainModel::getTaxonomyName( $post_type ), array( 'parent' => $data['toParentId'] ) );
                    if ( intval( $child->id ) === $data['dropNodeId'] ) {
                        update_term_meta( $data['dragNodeId'], 'fbv_tax_order', $ord++ );
                        wp_update_term( $data['dragNodeId'], MainModel::getTaxonomyName( $post_type ), array( 'parent' => $data['toParentId'] ) );
                    }
                }
            }

            if ( -1 === $data['dropPosition'] ) {
                $children = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT tm1.`term_id` as id, tm1.`meta_value` as ord
                        FROM {$wpdb->term_taxonomy} as tt
                        INNER JOIN {$wpdb->termmeta} as tm 
                            ON tt.term_id = tm.term_id AND tt.taxonomy = %s 
                            AND parent = %d AND tm.meta_key = 'fbv_author' 
                            AND tm.meta_value = %d 
                            AND tt.term_id != %d
                        INNER JOIN {$wpdb->termmeta} as tm1 
                            ON tt.term_id = tm1.term_id AND tm1.meta_key = 'fbv_tax_order'
                        ORDER BY ord+0 ASC",
                        MainModel::getTaxonomyName( $post_type ),
                        $data['toParentId'],
                        apply_filters( 'fbv_folder_created_by', 0 ),
                        $data['dragNodeId']
                    )
                );

                array_unshift(
                    $children,
                    (object) array(
                        'id'  => $data['dragNodeId'],
                        'ord' => 0,
                    )
                    );

                foreach ( $children as $key => $child ) {
                    update_term_meta( $child->id, 'fbv_tax_order', $key + 1 );
					wp_update_term( $child->id, MainModel::getTaxonomyName( $post_type ), array( 'parent' => $data['toParentId'] ) );
                }
            }

            return rest_ensure_response( true );
        }

        return new \WP_Error( 'post_type_not_enable', __( 'Post Type not enabled.', 'filebird' ) );
    }

    public function setFolderCounter( \WP_REST_Request $request ) {
		$type      = sanitize_key( $request->get_param( 'type' ) );
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
        $lang      = sanitize_key( $request->get_param( 'language' ) );

		add_filter(
            'fbv_counter_type',
            function() use ( $type ) {
				return $type;
			}
        );

		return rest_ensure_response( self::getCountFromTerms( $post_type, $lang ) );
	}

    public function restPtDuplicateFolder( \WP_REST_Request $request ) {
        $folder_id = (int) $request->get_param( 'folder_id' );
        $post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
        $lang      = sanitize_key( $request->get_param( 'language' ) );

        if ( $folder_id <= 0 || ! self::hasAccess( $folder_id ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'Permisson Denied.', 'filebird' ),
                array( 'status' => rest_authorization_required_code() )
            );
        }

        $new_folder = $this->model->duplicateFolder( $folder_id, $post_type, $lang );

        return rest_ensure_response( $new_folder );
    }

    private static function getNestedFolder( $taxonomy ) {
		global $wpdb;

        $counterType = SettingModel::getInstance()->get( 'folder_counter_type' );
		$isUsed      = apply_filters( 'fbv_counter_type', $counterType === 'counter_file_in_folder_and_sub' );

		if ( ! $isUsed ) {
			return array();
		}

		$query = $wpdb->prepare(
            "SELECT parent,GROUP_CONCAT(term_id) as child 
            FROM {$wpdb->term_taxonomy}
			WHERE taxonomy = %s
			GROUP BY parent",
        	$taxonomy
        );

		$result       = $wpdb->get_results( $query );
		$nestedFolder = array();

		foreach ( $result as $v ) {
			if ( $v->parent !== $v->child ) {
			    $nestedFolder[ $v->parent ] = explode( ',', $v->child );
            }
		}

		return $nestedFolder;
	}

    private static function getCountFromTerms( $post_type, $lang = null ) {
        global $wpdb;

        $postStatuses = Helpers::array_to_in_clause( array_keys( get_post_statuses() ) );

        $join = $wpdb->prepare(
            "INNER JOIN $wpdb->term_taxonomy as term_taxonomy ON terms.term_id = term_taxonomy.term_id AND term_taxonomy.taxonomy = %s
            INNER JOIN $wpdb->term_relationships as term_relationships ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
            INNER JOIN $wpdb->posts as posts ON posts.ID = term_relationships.object_id AND (posts.post_status IN ($postStatuses)) AND posts.post_type = %s
            INNER JOIN $wpdb->termmeta as term_meta ON term_meta.term_id = terms.term_id AND term_meta.meta_key = 'fbv_author' AND term_meta.meta_value = %d",
            MainModel::getTaxonomyName( $post_type ),
            $post_type,
            apply_filters( 'fbv_folder_created_by', 0 )
        );

        $where = '';

        $groupby = 'GROUP BY (terms.term_id)';

        $clauses = apply_filters( 'fbv_post_type_term_counter', $post_type, $join, $where, $groupby, $lang );

        if ( is_array( $clauses ) ) {
            $join    = ! empty( $clauses['join'] ) ? $clauses['join'] : '';
            $where   = ! empty( $clauses['where'] ) ? $clauses['where'] : '';
            $groupby = ! empty( $clauses['groupby'] ) ? $clauses['groupby'] : '';
        }

        $query = "SELECT terms.term_id, COUNT(DISTINCT term_relationships.object_id) as counter 
                FROM $wpdb->terms as terms $join
                WHERE 1=1 $where
                $groupby";

		$nestedFolder = self::getNestedFolder( MainModel::getTaxonomyName( $post_type ) );

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $counters         = $wpdb->get_results( $query, OBJECT_K );
        $formattedCounter = array();
		$actualCounter    = array();

        foreach ( $nestedFolder as $term_id => $counter ) {
		    $formattedCounter[ $term_id ] = self::getNestedCountAttachments( $counters, $nestedFolder, $term_id );
		}

        foreach ( $counters as $counter ) {
			$actualCounter[ $counter->term_id ] = $counter->counter;
        }

		return array(
			'display' => array_replace( $actualCounter, $formattedCounter ),
			'actual'  => $actualCounter,
		);
    }

    private static function getNestedCountAttachments( $counters, $nestedFolder, $folder_id ) {
        $c = 0;

		if ( isset( $counters[ $folder_id ] ) ) {
			$c = intval( $counters[ $folder_id ]->counter );
		}

		if ( ! isset( $nestedFolder[ $folder_id ] ) ) {
			return $c;
		}

		$total = $c;

		foreach ( $nestedFolder[ $folder_id ] as $folder_id => $children_id ) {
			$total += self::getNestedCountAttachments( $counters, $nestedFolder, $children_id );
		}

		return $total;
	}

    private static function getCountOfPost( $post_type, $lang ) {
       global $wpdb;

       $postStatuses = Helpers::array_to_in_clause( array_keys( get_post_statuses() ) );

       $query = $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = %s AND (post_status IN ($postStatuses))", $post_type );

       $query = apply_filters( 'fbv_post_type_all_counter', $query, $post_type, $lang );

       $count = $wpdb->get_var( $query );

       return intval( $count );
    }

    private function folderNameExists( $folder_name, $post_type, $parent, $exclude_id = 0 ) {
        $args = array(
            'fields'       => 'ids',
			'name'         => $folder_name,
			'parent'       => $parent,
            'hide_empty'   => false,
			'taxonomy'     => MainModel::getTaxonomyName( $post_type ),
            'meta_key'     => MainModel::AUTHOR_KEY,
            'meta_value'   => apply_filters( 'fbv_folder_created_by', 0 ),
            'meta_compare' => '=',
		);

        if ( $exclude_id > 0 ) {
            $args['exclude'] = array( $exclude_id );
        }

        $terms = get_terms( $args );
        return count( $terms ) > 0;
    }

    /**
     * Whether $candidate_parent_id is $term_id itself or one of its descendants.
     *
     * @param int    $term_id             Term being moved.
     * @param int    $candidate_parent_id Proposed new parent (0 for root).
     * @param string $post_type           Post type owning the taxonomy.
     * @return bool
     */
    private static function isSelfOrDescendant( $term_id, $candidate_parent_id, $post_type ) {
        $term_id             = (int) $term_id;
        $candidate_parent_id = (int) $candidate_parent_id;

        if ( $candidate_parent_id <= 0 ) {
            return false;
        }

        if ( $term_id === $candidate_parent_id ) {
            return true;
        }

        $ancestors = get_ancestors( $candidate_parent_id, MainModel::getTaxonomyName( $post_type ), 'taxonomy' );

        return in_array( $term_id, array_map( 'intval', $ancestors ), true );
    }

    private static function hasAccess( $folder_id ) {
        $author = intval( get_term_meta( $folder_id, MainModel::AUTHOR_KEY, true ) );
        return $author === intval( apply_filters( 'fbv_folder_created_by', 0 ) );
    }
}
