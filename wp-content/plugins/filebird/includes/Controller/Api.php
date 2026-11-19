<?php
namespace FileBird\Controller;

use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;

defined( 'ABSPATH' ) || exit;

class Api {
	public function restApi( $request ) {
		$act = $request->get_param( 'act' );
		$act = isset( $act ) ? sanitize_text_field( $act ) : '';
		if ( $act == 'generate-key' ) {
			$key = $this->generateRandomString( 40 );
			update_option( 'fbv_rest_api_key', $key );
			wp_send_json_success( array( 'key' => $key ) );
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Invalid action', 'filebird' ),
			)
		);
	}

	private function addFolderCounter( $folders, $counter ) {
		if ( is_array( $folders ) ) {
			foreach ( $folders as $k => $v ) {
				$folders[ $k ]['data-count'] = isset( $counter[ $v['id'] ] ) ? $counter[ $v['id'] ] : 0;
				if ( isset( $v['children'] ) ) {
					$folders[ $k ]['children'] = $this->addFolderCounter( $v['children'], $counter );
				}
			}
		}
		return $folders;
	}

	public function publicRestApiGetFolders( $request ) {
		$lang = sanitize_key( $request->get_param( 'language' ) );
		$lang = ! empty( $lang ) ? $lang : null;

		$counter           = FolderModel::countAttachments( $lang )['display'];
		$result['folders'] = $this->addFolderCounter( Tree::getFolders(), $counter );

		wp_send_json_success( $result );
	}

	public function publicRestApiGetPostTypeFolders( $request ) {
		$post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
		$order     = strtolower( sanitize_text_field( $request->get_param( 'order' ) ) ) ?: null;

		if ( ! \FileBird\Addons\PostType\Init::getInstance()->isPostTypeAvailable( $post_type ) ) {
			wp_send_json_error(
				array(
					'mess' => __( 'Post Type not enabled.', 'filebird' ),
				)
			);
		}

		$colors    = get_option( "fbv_{$post_type}_folder_colors", array() );
		$raw_terms = \FileBird\Addons\PostType\Models\Main::getFolders( $post_type, $order );
		$terms     = array_map(
			function ( $term ) use ( $colors ) {
				return \FileBird\Addons\PostType\Models\Main::convertFormat( $term, $colors );
			},
			$raw_terms
		);

		$tree = array();
		\FileBird\Addons\PostType\Models\Main::sortTerms( $terms, $tree, 0 );

		wp_send_json_success(
			array(
				'folders' => $tree,
			)
		);
	}

	public function publicRestApiGetPostTypeFolderPosts( $request ) {
		$post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
		$folder_id = (int) $request->get_param( 'folder_id' );
		$per_page  = $request->get_param( 'per_page' );
		$per_page  = is_null( $per_page ) ? 50 : (int) $per_page;
		$page      = (int) $request->get_param( 'page' );
		$page      = $page > 0 ? $page : 1;

		if ( ! \FileBird\Addons\PostType\Init::getInstance()->isPostTypeAvailable( $post_type ) ) {
			wp_send_json_error(
				array(
					'mess' => __( 'Post Type not enabled.', 'filebird' ),
				)
			);
		}

		$taxonomy = \FileBird\Addons\PostType\Models\Main::getTaxonomyName( $post_type );

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'fields'         => 'ids',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		);

		if ( $folder_id > 0 ) {
			// Posts inside the given folder.
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $folder_id,
				),
			);
		} elseif ( $folder_id === 0 ) {
			// folder_id = 0 -> uncategorized: posts not assigned to any folder of this post type.
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'operator' => 'NOT EXISTS',
				),
			);
		}
		// folder_id = -1 -> all posts regardless of folder (no tax_query filter).

		$query = new \WP_Query( $args );

		wp_send_json_success(
			array(
				'post_ids'    => array_map( 'intval', $query->posts ),
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
				'page'        => $page,
				'per_page'    => $per_page,
			)
		);
	}

	private function getPostTypeController() {
		if ( ! isset( $this->postTypeController ) ) {
			$this->postTypeController = new \FileBird\Addons\PostType\Controllers\Main();
		}
		return $this->postTypeController;
	}

	private function ensurePostTypeEnabled( $request ) {
		$post_type = sanitize_text_field( $request->get_param( 'post_type' ) );
		if ( ! \FileBird\Addons\PostType\Init::getInstance()->isPostTypeAvailable( $post_type ) ) {
			return new \WP_Error( 'post_type_not_enable', __( 'Post Type not enabled.', 'filebird' ), array( 'status' => 400 ) );
		}
		return null;
	}

	public function publicRestApiNewPostTypeFolder( $request ) {
		$guard = $this->ensurePostTypeEnabled( $request );
		if ( is_wp_error( $guard ) ) {
			return $guard;
		}
		return $this->getPostTypeController()->restNewFolder( $request );
	}

	public function publicRestApiUpdatePostTypeFolder( $request ) {
		$guard = $this->ensurePostTypeEnabled( $request );
		if ( is_wp_error( $guard ) ) {
			return $guard;
		}
		return $this->getPostTypeController()->restEditFolder( $request );
	}

	public function publicRestApiDeletePostTypeFolder( $request ) {
		$guard = $this->ensurePostTypeEnabled( $request );
		if ( is_wp_error( $guard ) ) {
			return $guard;
		}
		return $this->getPostTypeController()->restDeleteFolder( $request );
	}

	public function publicRestApiSetPostTypeFolder( $request ) {
		$guard = $this->ensurePostTypeEnabled( $request );
		if ( is_wp_error( $guard ) ) {
			return $guard;
		}
		return $this->getPostTypeController()->restSetFolder( $request );
	}

	public function publicRestApiGetFolderDetail( $request ) {
		$folder_id = $request->get_param( 'folder_id' );
		wp_send_json_success(
			array(
				'folder' => FolderModel::findById( $folder_id, 'id, name, parent' ),
			)
		);
	}
	public function publicRestApiGetAttachmentIds( $request ) {
		$folder_id = $request->get_param( 'folder_id' );

		if ( $folder_id != '' ) {
			wp_send_json_success(
				array(
					'attachment_ids' => Helpers::getAttachmentIdsByFolderId( $folder_id ),
				)
			);
		}
		wp_send_json_error(
			array(
				'mess' => __( 'folder_id is missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiGetAttachmentCount( $request ) {
		$folder_id = $request->get_param( 'folder_id' );
		$icl_lang  = $request->get_param( 'icl_lang' );
		if ( $folder_id !== '' ) {
			$count = 0;
			if ( $folder_id > 0 ) {
				$count = Helpers::getAttachmentCountByFolderId( $folder_id );
			} elseif ( $folder_id == -1 ) {
				$count = is_null( $icl_lang ) ? Tree::getCount( -1 ) : Tree::getCount( -1, $icl_lang );
			} else {
				//Uncategorized
				$total            = is_null( $icl_lang ) ? Tree::getCount( -1 ) : Tree::getCount( -1, $icl_lang );
				$folders          = is_null( $icl_lang ) ? Tree::getAllFoldersAndCount() : Tree::getAllFoldersAndCount( $icl_lang );
				$count_of_folders = 0;
				foreach ( $folders as $k => $v ) {
					$count_of_folders += $v;
				}
				$count = $total - $count_of_folders;
			}
			wp_send_json_success(
				array(
					'count' => $count,
				)
			);
		}
		wp_send_json_error(
			array(
				'mess' => __( 'folder_id is missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiNewFolder( $request ) {
		$parent_id = (int) ( $request->get_param( 'parent_id' ) );
		$name      = sanitize_text_field( $request->get_param( 'name' ) );

		if ( $name != '' ) {
			$folder = FolderModel::newUniqueFolder( $name, $parent_id );
			wp_send_json_success( array( 'id' => $folder['id'] ) );
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Required fields are missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiSetAttachment( $request ) {
		$ids    = $request->get_param( 'ids' );
		$folder = $request->get_param( 'folder' );

		$ids    = ! empty( $ids ) ? Helpers::sanitize_array( $ids ) : '';
		$folder = sanitize_text_field( $folder );

		if ( \is_numeric( $ids ) ) {
			$ids = array( $ids );
		}

		if ( $ids != '' && is_numeric( $folder ) ) {
			FolderModel::setFoldersForPosts( $ids, $folder );
			wp_send_json_success();
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Validation failed', 'filebird' ),
			)
		);
	}

	private function generateRandomString( $length = 10 ) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$randomString .= $characters[ wp_rand( 0, $charactersLength - 1 ) ];
		}
		return $randomString;
	}
}