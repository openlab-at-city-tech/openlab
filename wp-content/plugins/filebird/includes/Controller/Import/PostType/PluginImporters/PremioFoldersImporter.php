<?php

namespace FileBird\Controller\Import\PostType\PluginImporters;

use FileBird\Controller\Import\PostType\PluginImporter;

defined( 'ABSPATH' ) || exit;

class PremioFoldersImporter extends PluginImporter {
    public function get_taxonomy( $post_type ) {
        if ( $post_type == 'post' ) {
            return 'post_folder';
        } elseif ( $post_type == 'page' ) {
            return 'folder';
        } elseif ( $post_type == 'attachment' ) {
            return 'media_folder';
        }

        return $post_type . '_folder';
    }
}