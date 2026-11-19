<?php
namespace FileBird\Addons\PostType;

use FileBird\Addons\PostType\Controllers\Main as MainController;

class Routes {
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'registerRestFields' ) );
    }

    public function registerRestFields() {
        $controller = new MainController();

        register_rest_route(
            NJFB_REST_URL,
            'pt-folders',
            array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $controller, 'restGetFolders' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-new-folder',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restNewFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-edit-folder',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restEditFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-update-tree',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restUpdateTree' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-delete-folder',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restDeleteFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-set-folder',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restSetFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-set-folder-color',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restSetFolderColor' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
            NJFB_REST_URL,
            'pt-set-setting',
            array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restSetSetting' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
            )
        );
        register_rest_route(
			NJFB_REST_URL,
			'pt-folder-counter',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $controller, 'setFolderCounter' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
        register_rest_route(
			NJFB_REST_URL,
			'pt-duplicate-folder',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $controller, 'restPtDuplicateFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);  
    }

    public function resPermissionsCheck() {
        return current_user_can( 'manage_options' ) || current_user_can( 'edit_posts' );
    }
}
