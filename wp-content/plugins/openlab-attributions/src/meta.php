<?php

namespace OpenLab\Attributions\Meta;

use function OpenLab\Attributions\Helpers\sort_by;
use function OpenLab\Attributions\Helpers\get_attribution_marker_ids;
use function OpenLab\Attributions\Helpers\get_supported_post_types;

const NONCE = 'attribution-nonce';

/**
 * Current action can save the attributions.
 *
 * @param string $post_type
 * @return bool
 */
function can_save_attributions( $post_type ) {
	return (
		isset( $_POST[ NONCE ] )
		// Check nonce.
		&& wp_verify_nonce( $_POST[ NONCE ], NONCE )
		// Check if autosave.
		&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		// Check post type
		&& in_array( $post_type, get_supported_post_types(), true )
	);
}

/**
 * Sanitize attribution data.
 *
 * @param array $item
 * @return array $item
 */
function sanitize_attributions( $item ) {
	$sanitized = [];
	$fields    = [
		'id'             => false,
		'title'          => 'sanitize_text_field',
		'titleUrl'       => 'esc_url_raw',
		'authorName'     => 'sanitize_text_field',
		'authorUrl'      => 'esc_url_raw',
		'publisher'      => 'sanitize_text_field',
		'publisherUrl'   => 'esc_url_raw',
		'project'        => 'sanitize_text_field',
		'projectUrl'     => 'esc_url_raw',
		'datePublished'  => 'sanitize_text_field',
		'derivative'     => 'esc_url_raw',
		'adaptedTitle'   => 'sanitize_text_field',
		'adaptedAuthor'  => 'sanitize_text_field',
		'adaptedLicense' => false,
		'license'        => false,
		'content'        => function( $value ) {
			return wp_kses( $value, [ 'a' => [ 'href' => [] ] ] );
		},
	];

	foreach ( $fields as $name => $sanitize_callback ) {
		// Skip unknown fields.
		if ( ! isset( $item[ $name ] ) ) {
			continue;
		}

		if ( empty( $item[ $name ] ) ) {
			$sanitized[ $name ] = '';
			continue;
		}

		// No need to revalidate ID and the license.
		if ( ! $sanitize_callback ) {
			$sanitized[ $name ] = $item[ $name ];
			continue;
		}

		$sanitized[ $name ] = $sanitize_callback( $item[ $name ] );
	}

	return $sanitized;
}

/**
 * Register metabox.
 *
 * @return void
 */
function register_metabox() {
	add_meta_box(
		'ol-attributions-box',
		__( 'Attributions', 'openlab-attributions' ),
		__NAMESPACE__ . '\\render_metabox',
		get_supported_post_types(),
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register_metabox' );

/**
 * Remove unused markers from the content before saving.
 *
 * @param array $post_data An array of slashed, sanitized, and processed post data.
 * @return array
 */
function remove_markers( $post_data ) {
	if ( ! can_save_attributions( $post_data['post_type'] ) ) {
		return $post_data;
	}

	$marker_ids = get_attribution_marker_ids( $post_data['post_content'] );
	if ( empty( $marker_ids ) ) {
		return $post_data;
	}

	$search = [];

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$attr_ids   = empty( $_POST['attributions'] ) ? [] : wp_list_pluck( $_POST['attributions'], 'id' );
	$remove_ids = array_diff( $marker_ids, $attr_ids );

	// Bail if we don't have markers to remove.
	if ( empty( $remove_ids ) ) {
		return $post_data;
	}

	// Build array of old markers to remove.
	foreach ( $remove_ids as $id ) {
		$search[] = sprintf(
			// $post_data array is slashed.
			'<a id=\"anchor-%1$s\" class=\"attribution-anchor\" href=\"#ref-%1$s\"></a>',
			$id
		);

		// Attribute order isn't consistent; try to account for the most common ones.
		$search[] = sprintf(
			'<a id=\"anchor-%1$s\" href=\"#ref-%1$s\" class=\"attribution-anchor\"></a>',
			$id
		);

		$search[] = sprintf(
			'<a href=\"#ref-%1$s\" id=\"anchor-%1$s\" class=\"attribution-anchor\"></a>',
			$id
		);
	}

	if ( empty( $search ) ) {
		return $post_data;
	}

	$post_data['post_content'] = str_replace( $search, '', $post_data['post_content'] );

	return $post_data;
}
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\\remove_markers' );

/**
 * Save content attributions.
 *
 * @return void
 */
function save_attributions( $post_id, $post ) {
	if ( ! can_save_attributions( $post->post_type ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( empty( $_POST['attributions'] ) ) {
		delete_post_meta( $post_id, 'attributions' );
		return;
	}

	$order = get_attribution_marker_ids( $post->post_content );

	// Remove items that doesn't have content markers.
	$filtered = array_filter(
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$_POST['attributions'],
		function( $item ) use ( $order ) {
			return in_array( $item['id'], $order, true );
		}
	);

	// Sort items based on marker order.
	$attributions = sort_by(
		$filtered,
		function ( $item ) use ( $order ) {
			return array_search( $item['id'], $order, true );
		}
	);

	// Sanitize data.
	$attributions = array_map( __NAMESPACE__ . '\\sanitize_attributions', $attributions );

	// Reset array keys.
	// JS uses enumeration order vs insertion order.
	$attributions = array_values( $attributions );

	update_post_meta( $post_id, 'attributions', $attributions );
}
add_action( 'save_post', __NAMESPACE__ . '\\save_attributions', 10, 2 );

/**
 * Render container for our React scripts.
 *
 * @return void
 */
function render_metabox() {
	wp_nonce_field( NONCE, NONCE, false );
	echo '<div id="attribution-box"></div>';
}
