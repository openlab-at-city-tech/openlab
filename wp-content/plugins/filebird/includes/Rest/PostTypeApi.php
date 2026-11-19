<?php
namespace FileBird\Rest;

defined( 'ABSPATH' ) || exit;

use FileBird\Controller\PostTypeController;

class PostTypeApi {
	private $controller;

	public function register_rest_routes() {
		$this->controller = new PostTypeController();

		register_rest_route(
			NJFB_REST_URL,
			'pt-import',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this->controller, 'getFoldersToImport' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			NJFB_REST_URL,
			'pt-import-run',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this->controller, 'runImport' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}
}