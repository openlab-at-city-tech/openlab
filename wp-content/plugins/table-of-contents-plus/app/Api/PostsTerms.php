<?php
namespace AIOSEO\TableOfContents\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Models;

/**
 * Handles post/term lookups.
 *
 * @since 1.0.0
 */
class PostsTerms {
	/**
	 * Returns posts by ID/name.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function searchForObjects( $request ) {
		$body       = $request->get_json_params();
		$searchTerm = ! empty( $body['query'] ) ? sanitize_text_field( $body['query'] ) : null;
		$type       = ! empty( $body['type'] ) ? sanitize_text_field( $body['type'] ) : null;
		if ( empty( $searchTerm ) || empty( $type ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => 'No search term or object type was provided.'
			], 400 );
		}

		$wpdb = aioseoTableOfContents()->core->db->db;
		$like = '%' . $wpdb->esc_like( $searchTerm ) . '%';

		$objects = [];
		if ( 'posts' === $body['type'] ) {
			$postTypes = aioseoTableOfContents()->helpers->getPublicPostTypes( true );
			$objects   = aioseoTableOfContents()->core->db
				->start( 'posts' )
				->select( 'ID, post_type, post_title, post_name' )
				->whereRaw( $wpdb->prepare( '( post_title LIKE %s OR post_name LIKE %s OR ID = %d )', $like, $like, $searchTerm ) )
				->whereIn( 'post_type', $postTypes )
				->whereIn( 'post_status', [ 'publish', 'draft', 'future', 'pending' ] )
				->orderBy( 'post_title' )
				->limit( 10 )
				->run()
				->result();
		}

		if ( empty( $objects ) ) {
			return new \WP_REST_Response( [
				'success' => true,
				'objects' => []
			], 200 );
		}

		$parsedObjects = [];
		foreach ( $objects as $object ) {
			if ( 'posts' === $type ) {
				$parsedObjects[] = [
					'value' => (int) $object->ID,
					'slug'  => $object->post_name,
					'label' => $object->post_title,
					'type'  => $object->post_type,
					'link'  => get_permalink( $object->ID )
				];
			}
		}

		return new \WP_REST_Response( [
			'success' => true,
			'objects' => $parsedObjects
		], 200 );
	}
}