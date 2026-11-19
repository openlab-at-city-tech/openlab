<?php

namespace FileBird\Controller\Import\PostType\PluginImporters;

use FileBird\Controller\Import\PostType\PluginImporter;

defined( 'ABSPATH' ) || exit;

class WickedFoldersImporter extends PluginImporter {
    public function get_taxonomy( $post_type ) {
        return sprintf( 'wf_%s_folders', $post_type );
    }
}