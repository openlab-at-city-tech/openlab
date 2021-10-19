<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

class Requirements_Controller extends Zapier_Controller {

	/**
	 * @since 4.0
	 *
	 * @var string
	 */
	protected $rest_base = 'zapier-requirements';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 4.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
		) );
	}

	/**
	 * Returns the Zapier app requirements for this version of the Zapier Add-On.
	 *
	 * @since 4.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {
		$response = array(
			'zapier-version' => GF_ZAPIER_TARGET_ZAPIER_APP_VERSION,
		);

		return new WP_REST_Response( $response, 200 );
	}

}
