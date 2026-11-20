<?php

namespace FileBird\Controller;

use FileBird\Controller\Import\PostType\ImportPostType;
use FileBird\Addons\PostType\Models\Main as MainModel;

class PostTypeController {
    private $model;

    public function __construct() {
        $this->model = new MainModel();
    }

    public function getFoldersToImport( \WP_REST_Request $request ) {
        $post_types = $request->get_param( 'post_types' );

        $folders = new ImportPostType( $post_types );

        return rest_ensure_response( $folders->get() );
    }

    public function importFolders( $folders, $posts, $post_type, $parent = 0 ) {
        foreach ( $folders as $folder ) {
            $folderId = $this->model->createFolder( $folder['name'], $post_type, $parent );
            $post_ids = isset( $posts[ $folder['term_id'] ] ) ? explode( ',', $posts[ $folder['term_id'] ]['post_ids'] ) : array();

            $this->model->assignFolder( $folderId, $post_type, $post_ids );

            if ( isset( $folder['children'] ) && ! empty( $folder['children'] ) ) {
                $this->importFolders( $folder['children'], $posts, $post_type, $folderId );
            }
        }
    }

    public function runImport( \WP_REST_Request $request ) {
        $post_types = $request->get_param( 'post_types' );

        foreach ( $post_types as $post_type => $value ) {
            $this->importFolders( $value['folders'], $value['posts'] ?? array(), $post_type );
        }

        return rest_ensure_response( array( 'success' => true ) );
	}
}
