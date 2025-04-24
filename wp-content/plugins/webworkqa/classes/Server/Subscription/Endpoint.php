<?php

namespace WeBWorK\Server\Subscription;

use \WP_REST_Response;

/**
 * Subscription API endpoint.
 */
class Endpoint extends \WP_Rest_Controller {
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'webwork/v' . $version;

		$base = 'subscriptions';

		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/' . $base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	public function create_item_permissions_check( $request ) {
		return is_user_logged_in();
	}

	public function delete_item_permissions_check( $request ) {
		return is_user_logged_in();
	}

	public function create_item( $request ) {
		$params  = $request->get_params();
		$item_id = $params['itemId'];

		$question = new \WeBWorK\Server\Question( $item_id );
		$result   = false;
		if ( $question->exists() ) {
			$result = $question->set_subscription( get_current_user_id(), true );
		}

		$response = new WP_REST_Response();

		if ( $result ) {
			$response->set_status( 200 );
		}

		return $response;
	}

	public function delete_item( $request ) {
		$params = $request->get_params();
		$id     = $params['id'];

		$question = new \WeBWorK\Server\Question( $id );
		$result   = false;
		if ( $question->exists() ) {
			$result = $question->set_subscription( get_current_user_id(), false );
		}

		$response = new WP_REST_Response();

		if ( $result ) {
			$response->set_status( 200 );
		}

		return $response;
	}
}
