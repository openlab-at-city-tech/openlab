<?php

namespace Imagely\NGG\REST\Admin;

use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

class Block extends \WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'ngg/v1';
		$this->rest_base = 'admin/block/image';
	}

	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<image_id>.*)/',
			[
				'args' => [
					'image_id' => [
						'description' => \__( 'Image ID', 'nggallery' ),
						'type'        => 'integer',
						'required'    => true,
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => '__return_true',
				],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$id = $request->get_param( 'image_id' );

		$image = ImageMapper::get_instance()->find( $id );

		if ( ! $image ) {
			return new \WP_Error(
				'invalid_image_id',
				'Invalid image ID',
				[ 'status' => 404 ]
			);
		}

		$storage              = StorageManager::get_instance();
		$image->thumbnail_url = $storage->get_image_url( $image, 'thumb' );
		$image->image_url     = $storage->get_image_url( $image, 'full' );

		return new \WP_REST_Response(
			[
				'success' => true,
				'image'   => $image,
			]
		);
	}
}
