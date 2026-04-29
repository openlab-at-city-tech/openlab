<?php
/**
 * Class TagREST
 * Handles REST API endpoints for NextGEN Gallery tags.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use Imagely\NGG\Util\Security;

/**
 * Class TagREST
 * Handles REST API endpoints for NextGEN Gallery tags.
 */
class TagREST {
	/**
	 * Register the REST API routes for tags
	 */
	public static function register_routes() {
		// Get all tags.
		register_rest_route(
			'imagely/v1',
			'/tags',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_tags' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
			]
		);

		// Get single tag by ID or slug.
		register_rest_route(
			'imagely/v1',
			'/tags/(?P<identifier>[\w-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_tag' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'identifier' => [
						'required' => true,
						'type'     => 'string',
					],
				],
			]
		);

		// Create new tag.
		register_rest_route(
			'imagely/v1',
			'/tags',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'create_tag' ],
				'permission_callback' => [ self::class, 'check_create_permission' ],
				'args'                => [
					'name'        => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'description' => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
					],
					'slug'        => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					],
				],
			]
		);

		// Update tag by ID or slug.
		register_rest_route(
			'imagely/v1',
			'/tags/(?P<identifier>[\w-]+)',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_tag' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'identifier'  => [
						'required' => true,
						'type'     => 'string',
					],
					'name'        => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'description' => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
					],
					'slug'        => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					],
				],
			]
		);

		// Delete tag by ID or slug.
		register_rest_route(
			'imagely/v1',
			'/tags/(?P<identifier>[\w-]+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ self::class, 'delete_tag' ],
				'permission_callback' => [ self::class, 'check_delete_permission' ],
				'args'                => [
					'identifier' => [
						'required' => true,
						'type'     => 'string',
					],
				],
			]
		);
	}

	/**
	 * Get all tags
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_tags() {
		$terms = get_terms(
			[
				'taxonomy'   => 'ngg_tag',
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $terms ) ) {
			return new \WP_Error( 'ngg_no_tags', __( 'No tags found.', 'nggallery' ), [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( $terms, 200 );
	}

	/**
	 * Get a single tag by ID or slug
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_tag( $request ) {
		$identifier = $request['identifier'];

		// Check if identifier is numeric (ID).
		if ( is_numeric( $identifier ) ) {
			$term = get_term( absint( $identifier ), 'ngg_tag' );
		} else {
			// Treat as slug.
			$term = get_term_by( 'slug', sanitize_title( $identifier ), 'ngg_tag' );
		}

		if ( is_wp_error( $term ) || ! $term ) {
			return new \WP_Error( 'ngg_tag_not_found', __( 'Tag not found.', 'nggallery' ), [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( $term, 200 );
	}

	/**
	 * Create a new tag
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function create_tag( $request ) {
		$name        = $request['name'];
		$description = isset( $request['description'] ) ? $request['description'] : '';
		$args        = [ 'description' => $description ];

		// Only add slug if it's provided and not empty
		if ( isset( $request['slug'] ) && ! empty( trim( $request['slug'] ) ) ) {
			$args['slug'] = sanitize_title( $request['slug'] );
		}

		$term = wp_insert_term( $name, 'ngg_tag', $args );

		if ( is_wp_error( $term ) ) {
			return new \WP_Error( 'ngg_tag_creation_failed', $term->get_error_message(), [ 'status' => 400 ] );
		}

		$new_term = get_term( $term['term_id'], 'ngg_tag' );
		return new \WP_REST_Response( $new_term, 201 );
	}

	/**
	 * Update an existing tag
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function update_tag( $request ) {
		$identifier = $request['identifier'];
		$term_id    = null;

		// Check if identifier is numeric (ID).
		if ( is_numeric( $identifier ) ) {
			$term_id = absint( $identifier );
			$term    = get_term( $term_id, 'ngg_tag' );
		} else {
			// Treat as slug.
			$term = get_term_by( 'slug', sanitize_title( $identifier ), 'ngg_tag' );
			if ( $term ) {
				$term_id = $term->term_id;
			}
		}

		if ( ! $term ) {
			return new \WP_Error( 'ngg_tag_not_found', __( 'Tag not found.', 'nggallery' ), [ 'status' => 404 ] );
		}

		$args = [];

		if ( isset( $request['name'] ) ) {
			$args['name'] = $request['name'];
		}

		if ( isset( $request['description'] ) ) {
			$args['description'] = $request['description'];
		}

		if ( isset( $request['slug'] ) && ! empty( trim( $request['slug'] ) ) ) {
			$args['slug'] = sanitize_title( $request['slug'] );
		}

		$updated = wp_update_term( $term_id, 'ngg_tag', $args );

		if ( is_wp_error( $updated ) ) {
			return new \WP_Error( 'ngg_tag_update_failed', $updated->get_error_message(), [ 'status' => 400 ] );
		}

		$updated_term = get_term( $updated['term_id'], 'ngg_tag' );
		return new \WP_REST_Response( $updated_term, 200 );
	}

	/**
	 * Delete a tag
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function delete_tag( $request ) {
		$identifier = $request['identifier'];

		// Check if identifier is numeric (ID).
		if ( is_numeric( $identifier ) ) {
			$term_id = absint( $identifier );
			$term    = get_term( $term_id, 'ngg_tag' );
		} else {
			// Treat as slug.
			$term = get_term_by( 'slug', sanitize_title( $identifier ), 'ngg_tag' );
			if ( $term ) {
				$term_id = $term->term_id;
			}
		}

		if ( ! $term ) {
			return new \WP_Error( 'ngg_tag_not_found', __( 'Tag not found.', 'nggallery' ), [ 'status' => 404 ] );
		}

		$deleted = wp_delete_term( $term_id, 'ngg_tag' );

		if ( is_wp_error( $deleted ) ) {
			return new \WP_Error( 'ngg_tag_deletion_failed', $deleted->get_error_message(), [ 'status' => 400 ] );
		}

		return new \WP_REST_Response( null, 204 );
	}

	/**
	 * Check if user has permission to read tags
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Check if user has permission to create tags
	 *
	 * @return bool
	 */
	public static function check_create_permission() {
		return Security::is_allowed( 'NextGEN Manage tags' );
	}

	/**
	 * Check if user has permission to edit tags
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Manage tags' );
	}

	/**
	 * Check if user has permission to delete tags
	 *
	 * @return bool
	 */
	public static function check_delete_permission() {
		return Security::is_allowed( 'NextGEN Manage tags' );
	}
}
