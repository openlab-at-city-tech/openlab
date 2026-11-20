<?php

namespace FileBird\Controller\Import\PostType;

use FileBird\Controller\Import\PostType\PluginImporters\WickedFoldersImporter;
use FileBird\Controller\Import\PostType\PluginImporters\PremioFoldersImporter;

defined( 'ABSPATH' ) || exit;

class ImportPostType {
    private $plugins;
    private $post_types;

    public function __construct( $post_types ) {
        $this->plugins['wf'] = array(
			'name'   => 'Wicked Folders',
			'author' => 'Wicked Plugins',
            'class'  => new WickedFoldersImporter( $post_types ),
        );

        $this->plugins['premio'] = array(
            'name'   => 'Folders',
			'author' => 'Premio',
            'class'  => new PremioFoldersImporter( $post_types ),
        );

        $this->post_types = $post_types;
    }

    public function get() {
        $need_import = array();

        foreach ( $this->plugins as $plugin_key => $plugin ) {
            $folders = $plugin['class']->get_folders();

            if ( ! empty( $folders ) ) {
                $need_import[ $plugin_key ]['name']       = $plugin['name'];
                $need_import[ $plugin_key ]['author']     = $plugin['author'];
                $need_import[ $plugin_key ]['post_types'] = $folders;
            }
        }

        return $need_import;
    }
}