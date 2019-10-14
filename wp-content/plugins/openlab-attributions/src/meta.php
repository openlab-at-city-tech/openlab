<?php

namespace OpenLab\Attributions\Meta;

use function OpenLab\Attributions\Helpers\sort_by;
use function OpenLab\Attributions\Helpers\get_attribution_marker_ids;
use function OpenLab\Attributions\Helpers\get_supported_post_types;

const NONCE = 'attribution-nonce';

/**
 * Current action can save the attributions.
 *
 * @return bool
 */
function can_save_attributions( $post ) {
	return (
		isset( $_POST[ NONCE ] )
		// Check nonce.
		&& wp_verify_nonce( $_POST[ NONCE ], NONCE )
		// Check if autosave.
		&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		// Check post type
		&& in_array( $post->post_type, get_supported_post_types(), true )
	);
}

/**
 * Sanitize attribution data.
 *
 * @param array $item
 * @return array $item
 */
function sanitize_attributions( $item ) {
	$fields = [
		'title'         => 'sanitize_text_field',
		'titleUrl'      => 'esc_url_raw',
		'authorName'    => 'sanitize_text_field',
		'authorUrl'     => 'esc_url_raw',
		'publisher'     => 'sanitize_text_field',
		'publisherUrl'  => 'esc_url_raw',
		'project'       => 'sanitize_text_field',
		'projectUrl'    => 'esc_url_raw',
		'datePublished' => 'sanitize_text_field',
		'derivative'    => 'esc_url_raw',
	];

	foreach ( $fields as $name => $sanitize_callback ) {
		if ( empty( $item[ $name ] ) ) {
			continue;
		}

		$item[ $name ] = $sanitize_callback( $item[ $name ] );
	}

	return $item;
}

/**
 * Register metabox.
 *
 * @return void
 */
function register_metabox() {
	add_meta_box(
		'ol-attributions-box',
		'Attributions',
		__NAMESPACE__ . '\\render_metabox',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register_metabox' );

/**
 * Save content attributions.
 *
 * @return void
 */
function save_attributions( $post_id, $post ) {
	if ( ! can_save_attributions( $post ) ) {
		return;
	}

	if ( empty( $_POST['attributions'] ) ) {
		delete_post_meta( $post_id, 'attributions' );
		return;
	}

	$order = get_attribution_marker_ids( $post->post_content );

	// Remove items that doesn't have content markers.
	$filtered = array_filter( $_POST['attributions'], function( $item ) use ( $order ) {
		return in_array( $item['id'], $order, true );
	} );

	// Sort items based on marker order.
	$attributions = sort_by( $filtered, function ( $item ) use ( $order ) {
		return array_search( $item['id'], $order );
	} );

	// Sanitize data.
	$attributions = array_map( __NAMESPACE__ . '\\sanitize_attributions', $attributions );

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
