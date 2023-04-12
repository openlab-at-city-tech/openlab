<?php

namespace WeBWorK\Server\Question;

/**
 * Question API endpoint.
 */
class Endpoint extends \WP_Rest_Controller {
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'webwork/v' . $version;

		$base = 'questions';

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
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::READABLE ),
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
	/*
	public function get_collection_params() {
		return array(
			'is_answer' => array(
				'description'       => 'Whether a response is an answer to its question',
				'type'              => 'boolean',
				'sanitize_callback' => 'boolval',
			),
		);
	}
	*/

	public function create_item( $request ) {
		$params = $request->get_params();

		$problem_id = $params['problem_id'];
		$content    = $params['content'];
		$tried      = $params['tried'];

		$problem_data            = null;
		$remote_class_url        = null;
		$remote_problem_url      = null;
		$remote_user_problem_url = null;
		if ( isset( $params['post_data_key'] ) ) {
			$post_data_key = $params['post_data_key'];
			$problem_data  = get_option( $post_data_key );

			if ( isset( $problem_data['remote_class_url'] ) ) {
				$remote_class_url = $problem_data['remote_class_url'];
			}

			if ( isset( $problem_data['remote_problem_url'] ) ) {
				$remote_problem_url = $problem_data['remote_problem_url'];
			}

			if ( isset( $problem_data['webwork_user_problem_url'] ) ) {
				$remote_user_problem_url = $problem_data['webwork_user_problem_url'];
			}

			// Don't ever keep this data around.
			delete_option( $post_data_key );
		}

		// Try fetching another question from the same problem.
		if ( ! $problem_data ) {
			$query = new Query(
				array(
					'problem_id' => $problem_id,
				)
			);

			$questions = $query->get();

			foreach ( $questions as $q ) {
				if ( $q->get_problem_text() ) {
					$problem_data = array(
						'problem_id'   => $q->get_problem_id(),
						'problem_set'  => $q->get_problem_set(),
						'problem_text' => $q->get_problem_text(),
					);

					break;
				}
			}
		}

		/*
		 * Sanity check: Don't allow the question to be created if there's already
		 * one from the same user with the same metadata.
		 */
		$query          = new Query(
			array(
				'problem_id'  => $problem_data['problem_id'],
				'max_results' => 100,
			)
		);
		$existing_items = $query->get();
		if ( $existing_items ) {
			foreach ( $existing_items as $existing_item ) {
				if ( get_current_user_id() !== $existing_item->get_author_id() ) {
					continue;
				}

				if ( $content === $existing_item->get_content() && $tried === $existing_item->get_tried() ) {
					return new \WP_Error( 'webwork_item_exists', __( 'It looks like you are trying to post a duplicate.' ), array( 'status' => 400 ) );
				}
			}
		}

		$question = new \WeBWorK\Server\Question();

		$course  = isset( $problem_data['course'] ) ? $problem_data['course'] : '';

		$question->set_author_id( get_current_user_id() );
		$question->set_content( $content );
		$question->set_tried( $tried );
		$question->set_is_anonymous( $params['isAnonymous'] );
		$question->set_problem_id( $problem_data['problem_id'] );
		$question->set_problem_set( $problem_data['problem_set'] );
		$question->set_course( $course );
		$question->set_problem_text( $problem_data['problem_text'] );
		$question->set_client_url( $params['client_url'] );
		$question->set_client_name( $params['client_name'] );
		$question->set_emailable_url( $problem_data['emailableURL'] );
		$question->set_random_seed( $problem_data['randomSeed'] );
		$question->set_notify_addresses( $problem_data['notifyAddresses'] );

		$question->set_student_name( $problem_data['studentName'] );

		if ( $remote_class_url ) {
			$question->set_remote_class_url( $remote_class_url );
		}

		if ( $remote_problem_url ) {
			$question->set_remote_problem_url( $remote_problem_url );
		}

		if ( $remote_user_problem_url ) {
			$question->set_remote_user_problem_url( $remote_user_problem_url );
		}

		// Parse for external images and try to pull them in.
		$question->fetch_external_assets();

		if ( $question->save() ) {
			$query = new Query(
				array(
					'question_id' => $question->get_id(),
				)
			);

			$results = $query->get_for_endpoint();

			// @todo not found?
			$retval = reset( $results );

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

		$question = new \WeBWorK\Server\Question( $params['id'] );

		$retval = false;
		if ( $question->exists() ) {
			$question->set_content( $params['content'] );
			$question->set_tried( $params['tried'] );

			$retval = $question->save();

			if ( $retval ) {
				$query = new Query(
					array(
						'question_id' => $question->get_id(),
					)
				);

				$results = $query->get_for_endpoint();
				$results = reset( $results );
			} else {
				$results = $retval;
			}
		}

		$response = rest_ensure_response( $results );

		if ( $retval ) {
			$response->set_status( 200 );
		} else {
			$response->set_status( 500 );
		}

		return $response;
	}

	public function delete_item( $request ) {
		$params   = $request->get_params();
		$question = new \WeBWorK\Server\Question( $params['id'] );
		$retval   = $question->delete();

		$response = rest_ensure_response( '' );

		if ( $retval ) {
			$response->set_status( 200 );
		} else {
			$response->set_status( 500 );
		}

		return $response;
	}

	public function get_items( $request ) {
		$params = $request->get_params();
		$keys   = array(
			'orderby',
			'order',
			'course',
			'lastQuestion',
			'maxResults',
		);

		$args = array();
		foreach ( $keys as $k ) {
			if ( isset( $params[ $k ] ) ) {
				$args[ $k ] = $params[ $k ];
			}
		}

		// Programming
		if ( isset( $params['problemSet'] ) ) {
			$args['problem_set'] = $params['problemSet'];
		}

		$args['offset']      = (int) $params['offset'];
		$args['max_results'] = (int) $params['maxResults'];

		$q = new Query( $args );

		$questions = $q->get_for_endpoint();

		$attachments = $attachment_ids = array();
		foreach ( $q->get() as $question ) {
			$q_att_ids      = $question->get_attachment_ids();
			$attachment_ids = array_merge( $q_att_ids, $attachment_ids );
		}

		$pf          = new \WeBWorK\Server\Util\ProblemFormatter();
		$attachments = $pf->get_attachment_data( $attachment_ids );

		$retval = array(
			'attachments' => $attachments,
			'questionIds' => array_keys( $questions ),
			'questions'   => $questions,
		);

		$response = rest_ensure_response( $retval );

		return $response;
	}

	// @todo
	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function update_item_permissions_check( $request ) {
		$params = $request->get_params();
		$post   = get_post( $params['id'] );

		$user_is_admin = webwork_user_is_admin();

		return $user_is_admin || $post->post_author == get_current_user_id();
	}

	public function delete_item_permissions_check( $request ) {
		$params = $request->get_params();
		$post   = get_post( $params['id'] );

		$user_is_admin = webwork_user_is_admin();

		return $user_is_admin || $post->post_author == get_current_user_id();
	}

	// @todo here and Response
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
