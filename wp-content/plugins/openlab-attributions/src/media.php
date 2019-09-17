<?php
/**
 * Dashboard integrations.
 */

namespace OpenLab\Attributions\Media;

use function OpenLab\Attributions\Helpers\get_licenses;
use function OpenLab\Attributions\Helpers\get_licenses_select;

/**
 * Adds attribution fields to image editor.
 *
 * @param array $fields
 * @param WP_Post $post
 * @return array $fields
 */
function attachment_fields( array $fields, \WP_Post $post ) {
	// Only add attribution fields to images.
	if ( ! wp_attachment_is( 'image', $post ) ) {
		return $fields;
	}

	$author     = get_post_meta( $post->ID, '_wp_attachment_author', true );
	$author_uri = get_post_meta( $post->ID, '_wp_attachment_author_uri', true );

	$fields['author'] = [
		'label' => __( 'Author' ),
		'value' => esc_attr( $author ),
	];

	$fields['author-uri'] = [
		'label' => __( 'Author URI' ),
		'value' => esc_attr( $author_uri ),
	];

	$fields['license'] = [
		'label' => __( 'License' ),
		'input' => 'html',
		'html'  => get_licenses_select( $post ),
	];

	return $fields;
}
add_filter( 'attachment_fields_to_edit', __NAMESPACE__ . '\\attachment_fields', 10, 2 );

/**
 * Save image attribution data.
 *
 * @param array $post
 * @param array $attachment
 * @return void
 */
function attachment_save_fields( array $post, array $attachment ) {
	$author     = isset( $attachment['author'] ) ? sanitize_text_field( $attachment['author'] ) : '';
	$author_uri = isset( $attachment['author-uri']) ? esc_url_raw( $attachment['author-uri'] ) : '';

	update_post_meta( $post['ID'], '_wp_attachment_author', $author );
	update_post_meta( $post['ID'], '_wp_attachment_author_uri', $author_uri );

	$licenses = get_licenses();
	if ( isset( $licenses[ $attachment['license'] ] ) ) {
		update_post_meta( $post['ID'], '_wp_attachment_license', $attachment['license'] );
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', __NAMESPACE__ . '\\attachment_save_fields', 10, 2 );

/**
 * Add attribution columns to media list.
 *
 * @param array $columns
 * @return array $columns
 */
function attribution_columns( array $columns ) {
	unset( $columns['author'] );

	$columns['credit']  = __( 'Credit' );
	$columns['license'] = __( 'License' );

	return $columns;
}
add_filter( 'manage_media_columns', __NAMESPACE__ . '\\attribution_columns' );

/**
 * Render attachment attributions.
 *
 * @param string $column_name
 * @param int $post_id
 * @return void
 */
function render_attribution_column( $column_name, $post_id ) {
	if ( 'license' === $column_name ) {
		$license  = get_post_meta( $post_id, '_wp_attachment_license', true );
		$licenses = get_licenses();
		echo isset( $licenses[ $license ] ) ? esc_html( $licenses[ $license ]['label'] ) : '&#8212;';
	}

	if ( 'credit' === $column_name ) {
		$author = get_post_meta( $post_id, '_wp_attachment_author', true);
		echo $author ? esc_html( $author ) : '&#8212;';
	}
}
add_action( 'manage_media_custom_column', __NAMESPACE__ . '\\render_attribution_column', 10, 2 );
