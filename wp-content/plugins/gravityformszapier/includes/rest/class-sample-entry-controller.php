<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use GFAPI;

class Sample_Entry_Controller extends Zapier_Controller {

	/**
	 * @since 2.4
	 *
	 * @var string
	 */
	protected $rest_base = 'forms/(?P<form_id>[\d]+)/sample-entry';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 2.4
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_sample_entry' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
		) );
	}

	/**
	 * Get a collection of feeds for the form.
	 *
	 * @since 2.4
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_sample_entry( $request ) {
		$form_id_valid = $this->is_url_form_id_valid( $request );
		if ( is_wp_error( $form_id_valid ) ) {
			return $form_id_valid;
		}

		$form = GFAPI::get_form( $request->get_param( 'form_id' ) );

		return new WP_REST_Response( $this->get_sample_data( $form ), 200 );
	}

}
