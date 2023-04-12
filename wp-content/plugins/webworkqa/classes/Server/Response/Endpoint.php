<?php

namespace WeBWorK\Server\Response;

/**
 * Response API endpoint.
 */
class Endpoint extends \WP_Rest_Controller {
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'webwork/v' . $version;

		$base = 'responses';

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
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::DELETABLE ),
				),
			)
		);
	}

	/**
	 * Get the query params for collections.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'is_answer' => array(
				'description'       => 'Whether a response is an answer to its question',
				'type'              => 'boolean',
				'sanitize_callback' => 'boolval',
			),
		);
	}

	public function create_item( $request ) {
		$params = $request->get_params();

		$question_id = $params['question_id'];
		$value       = $params['value'];

		$response = new \WeBWorK\Server\Response();

		$response->set_author_id( get_current_user_id() );
		$response->set_content( $value );
		$response->set_question_id( $question_id );
		$response->set_is_new( true );
		$response->set_client_url( $params['client_url'] );
		$response->set_client_name( $params['client_name'] );

		if ( $response->save() ) {
			$response_id = $response->get_id();
			$r           = new \WeBWork\Server\Response\Query(
				array(
					'response_id__in' => $response_id,
				)
			);

			$for_endpoint = $r->get_for_endpoint();
			$retval       = $for_endpoint[ $response_id ];

			$r = rest_ensure_response( $retval );
			$r->set_status( 201 );
		} else {
			$r = rest_ensure_response( false );
			$r->set_status( 500 );
		}

		return $r;
	}

	public function create_item_permissions_check( $request ) {
		// @todo make this better
		return is_user_logged_in();
	}

	public function update_item( $request ) {
		$retval = false;

		$params = $request->get_params();
		if ( isset( $params['id'] ) ) {
			$response = new \WeBWorK\Server\Response( $params['id'] );
			if ( $response->exists() ) {
				if ( isset( $params['is_answer'] ) ) {
					$response->set_is_answer( $params['is_answer'] );
				}

				if ( isset( $params['content'] ) ) {
					$response->set_content( $params['content'] );
				}

				$response->set_client_url( $params['client_url'] );
				$response->set_client_name( $params['client_name'] );

				$response->save();

				$r = new \WeBWork\Server\Response\Query(
					array(
						'response_id__in' => $params['id'],
					)
				);

				$for_endpoint = $r->get_for_endpoint();
				$retval       = $for_endpoint[ $params['id'] ];
			}
		}

		$response = rest_ensure_response( $retval );

		if ( $retval ) {
			$response->set_status( 200 );
		} else {
			$response->set_status( 500 );
		}

		return $response;
	}

	public function update_item_permissions_check( $request ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$params = $request->get_params();
		if ( ! isset( $params['id'] ) ) {
			return false;
		}

		$response_id = $params['id'];
		$response    = new \WeBWorK\Server\Response( $response_id );

		if ( ! $response->exists() ) {
			return false;
		}

		$user_is_admin = current_user_can( 'edit_others_posts' );
		$user_is_admin = apply_filters( 'webwork_user_is_admin', $user_is_admin );
		if ( $user_is_admin ) {
			return true;
		}

		// 'is_answer' is only accessible by question author or faculty member.
		if ( isset( $params['is_answer'] ) ) {
			$question_id = $response->get_question_id();
			$question    = get_post( $question_id );
			return $question && get_current_user_id() == $question->post_author;
		} else {
			return get_current_user_id() == $response->get_author_id();
		}

		return false;
	}

	protected function existing_item_permissions_check( $request ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$params = $request->get_params();
		if ( ! isset( $params['id'] ) ) {
			return false;
		}

		$response_id = $params['id'];
		$response    = new \WeBWorK\Server\Response( $response_id );

		if ( ! $response->exists() ) {
			return false;
		}

		$question_id = $response->get_question_id();
		if ( ! $question_id ) {
			return false;
		}

		// @todo modeling
		$question = get_post( $question_id );
		if ( ! $question ) {
			return false;
		}

		$user_is_admin = current_user_can( 'edit_others_posts' );
		$user_is_admin = apply_filters( 'webwork_user_is_admin', $user_is_admin );

		return $user_is_admin || $response->get_author_id() == get_current_user_id();
	}

	public function delete_item( $request ) {
		$retval = false;

		$params = $request->get_params();
		if ( isset( $params['id'] ) ) {
			$r      = new \WeBWorK\Server\Response( $params['id'] );
			$retval = $r->delete();
		}

		$request_response = rest_ensure_response( $retval );

		if ( $retval ) {
			$request_response->set_status( 200 );
		} else {
			$request_response->set_status( 500 );
		}

		return $request_response;
	}

	public function delete_item_permissions_check( $request ) {
		return $this->existing_item_permissions_check( $request );
	}

	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'type'       => 'object',
			'properties' => array(
				'is_answer' => array(
					'type' => 'boolean',
				),
			),
		);

		return $schema;
	}
}
