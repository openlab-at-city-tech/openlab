<?php

namespace WeBWorK\Server\App;

use \WP_REST_Controller;

/**
 * App API endpoint.
 */
class Endpoint extends WP_Rest_Controller {
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'webwork/v' . $version;

		$base = 'app-config';

		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_config' ),
					'permission_callback' => array( $this, 'get_config_permissions_check' ),
				),
			)
		);
	}

	public function get_config_permissions_check( $request ) {
		return true;
	}

	public function get_config( $request ) {
		$subscriptions = array();
		if ( is_user_logged_in() ) {
			$subscription_query = new \WeBWorK\Server\Subscription\Query(
				array(
					'user_id' => get_current_user_id(),
				)
			);
			$subs               = $subscription_query->get();

			$subscriptions = array_fill_keys( $subs, 1 );
		}

		$retval = array(
			'subscriptions' => $subscriptions,
		);

		return rest_ensure_response( $retval );
	}
}
