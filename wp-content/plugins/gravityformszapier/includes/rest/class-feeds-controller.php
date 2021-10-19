<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class Feeds_Controller extends Zapier_Controller {

	/**
	 * The base of this controller's route.
	 *
	 * @since 4.1
	 *
	 * @var string
	 */
	protected $rest_base = 'forms/(?P<form_id>[\d]+)/zapier-feeds/(?P<zap_id>[\d]+)';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 4.1
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
		) );
	}

	/**
	 * Gets a Zapier feed by the zapID stored in the feed meta.
	 *
	 * @since 4.1
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$form_id_valid = $this->is_url_form_id_valid( $request );
		if ( is_wp_error( $form_id_valid ) ) {
			return $form_id_valid;
		}

		$result = $this->query_item( $request );

		if ( empty( $result ) ) {
			return new WP_Error( 'feed_not_found', __( 'Feed not found.', 'gravityformszapier' ), array( 'status' => 404 ) );
		}

		$result['meta'] = json_decode( $result['meta'], true );

		return new WP_REST_Response( $result, 200 );
	}

	/**
	 * Performs the database query to retrieve the feed.
	 *
	 * @since 4.1
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return array|null
	 */
	public function query_item( $request ) {
		global $wpdb;

		$table = $wpdb->prefix . 'gf_addon_feed';
		$like  = '%' . $wpdb->esc_like( sprintf( '"zapID":"%d"', $request->get_param( 'zap_id' ) ) ) . '%';

		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$table} WHERE (form_id=%d) AND (addon_slug=%s) AND (meta LIKE %s) ORDER BY id DESC",
			$request->get_param( 'form_id' ),
			'gravityformszapier',
			$like
		), ARRAY_A );
	}

}
