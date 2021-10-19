<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use GFAPI;
use WP_Error;

class Sample_Entries_Controller extends Zapier_Controller {

	/**
	 * @since 4.1
	 *
	 * @var string
	 */
	protected $rest_base = 'forms/(?P<form_id>[\d]+)/zapier-sample-entries';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 4.1
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_sample_entries' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'_admin_labels' => array(
						'description' => __( 'Indicates if admin labels should be used instead of frontend labels.', 'gravityformszapier' ),
						'type'        => 'boolean',
					)
				),
			),
		) );
	}

	/**
	 * Get a collection of the latest 3 entries for the requested from.
	 *
	 * If the there are no entries, or the user doesn't have the gravityforms_view_entries capability, it will return the sample data returned by the '/sample-entry' endpoint.
	 *
	 * @since 4.1
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_sample_entries( $request ) {
		$form_id_valid = $this->is_url_form_id_valid( $request );
		if ( is_wp_error( $form_id_valid ) ) {
			return $form_id_valid;
		}

		$form_id      = $request->get_param( 'form_id' );
		$form         = GFAPI::get_form( $form_id );
		$admin_labels = ! empty( $request->get_param( '_admin_labels' ) );

		if ( ! $this->current_user_can_any( 'gravityforms_view_entries', $request ) ) {
			return new WP_REST_Response( $this->get_sample_data( $form, $admin_labels ), 200 );
		}

		$entries = GFAPI::get_entries( $form_id, array( 'status' => 'active' ), null, array( 'offset' => 0, 'page_size' => 3 ) );
		if ( is_wp_error( $entries ) ) {
			return $entries;
		}

		foreach ( $entries as $entry ) {
			$sample_data[] = $this->get_sample_data( $form, $admin_labels, $entry );
		}

		if ( empty( $sample_data ) ) {
			$sample_data[] = $this->get_sample_data( $form, $admin_labels );
		}

		return new WP_REST_Response( $sample_data, 200 );
	}

}
