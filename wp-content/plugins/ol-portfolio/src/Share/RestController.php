<?php

namespace OpenLab\Portfolio\Share;

use WP_Error;

class RestController {

	/**
	 * The namespace of comments controller's route.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of comments controller's route.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'comments';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/shared/(?P<id>[\d]+)',
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
				'args'                => [
					'id' => [
						'sanitize_callback' => 'absint',
					],
				],
			]
		);
	}

	/**
	 * Checks if a given REST request has access to update a comment.
	 *
	 * @param \WP_REST_Request $request
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( \is_wp_error( $comment ) ) {
			return $comment;
		}

		if ( \get_current_user_id() !== (int) $comment->user_id ) {
			return new WP_Error( 'rest_cannot_edit', 'Sorry, you are not allowed to edit this comment.', [ 'status' => \rest_authorization_required_code() ] );
		}

		return true;
	}

	/**
	 * Mark comment as "Added to Portfolio".
	 *
	 * @param \WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response Response
	 */
	public function update_item( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( \is_wp_error( $comment ) ) {
			return $comment;
		}

		if ( ! isset( $request['meta']['portfolio_post_id'] ) ) {
			return new WP_Error( 'no_portfolio_post_id', 'Missing Portfolio post ID.', [ 'status' => 404 ] );
		}

		$meta = (int) $request['meta']['portfolio_post_id'];
		\update_comment_meta( $comment->comment_ID, 'portfolio_post_id', $meta );

		return $meta;
	}

	/**
	 * Get the comment, if the ID is valid.
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Comment|WP_Error Comment object if ID is valid, WP_Error otherwise.
	 */
	protected function get_comment( $id ) {
		$error = new WP_Error( 'rest_comment_invalid_id', 'Invalid comment ID.', [ 'status' => 404 ] );
		if ( (int) $id <= 0 ) {
			return $error;
		}

		$id      = (int) $id;
		$comment = \get_comment( $id );
		if ( empty( $comment ) ) {
			return $error;
		}

		if ( ! empty( $comment->comment_post_ID ) ) {
			$post = \get_post( (int) $comment->comment_post_ID );
			if ( empty( $post ) ) {
				return new WP_Error( 'rest_post_invalid_id', 'Invalid post ID.', [ 'status' => 404 ] );
			}
		}

		return $comment;
	}
}
