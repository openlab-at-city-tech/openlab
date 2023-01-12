<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use GFAPI;

class Transfer_Entries_Controller extends Zapier_Controller {

	/**
	 * The base of this controller's route.
	 *
	 * @since 4.2
	 *
	 * @var string
	 */
	protected $rest_base = 'forms/(?P<form_id>[\d]+)/zapier-transfer-entries';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * This route is for internal use, and may be subject to change.
	 *
	 * @since 4.2
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
	 * Gets all the entries for a form.
	 *
	 * @since 4.2
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$form_id_valid = $this->is_url_form_id_valid( $request );
		if ( is_wp_error( $form_id_valid ) ) {
			return $form_id_valid;
		}

		$form_id       = $request->get_param( 'form_id' );
		$search_params = $this->parse_entry_search_params( $request );
		$total_count   = 0;
		$entries       = GFAPI::get_entries( $form_id, $search_params['search_criteria'], $search_params['sorting'], $search_params['paging'], $total_count );
		$form          = GFAPI::get_form( $form_id );
		$admin_labels  = ! empty( $request->get_param( '_admin_labels' ) );
		$data          = array();
		foreach ( $entries as $entry ) {
			$data[] = $this->get_sample_data( $form, $admin_labels, $entry );
		}

		$per_page  = isset( $search_params['paging']['page_size'] ) ? $search_params['paging']['page_size'] : 10;
		$max_pages = ceil( $total_count / (int) $per_page );

		$response = rest_ensure_response( $data );
		$response->header( 'X-GF-Total', (int) $total_count );
		$response->header( 'X-GF-TotalPages', (int) $max_pages );

		return $response;

	}

	/**
	 * Determines if the user has the required capability to get the item.
	 *
	 * @since 4.2
	 *
	 * @param WP_REST_Request $request The full data for the request.
	 *
	 * @return bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->current_user_can_any( 'gravityforms_view_entries', $request );
	}

}
