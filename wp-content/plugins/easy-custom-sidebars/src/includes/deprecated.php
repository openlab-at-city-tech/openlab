<?php
/**
 * Backward Compatibility
 *
 * Preserves data for sidebars created in versions
 * of easy custom sidebars prior to v2.0.0.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Deprecated;

add_filter(
	'ecs_sidebar_attachments',
	function( $attachments, $post_id, $with_metadata ) {
		return array_map(
			function ( $attachment ) {
				if (
					array_key_exists( 'menu-item-db-id', $attachment ) &&
					array_key_exists( 'menu-item-object', $attachment ) &&
					array_key_exists( 'menu-item-type', $attachment )
				) {
					return [
						'id'              => $attachment['menu-item-db-id'],
						'data_type'       => $attachment['menu-item-object'],
						'attachment_type' => $attachment['menu-item-type'],
					];
				}

				return $attachment;
			},
			$attachments
		);
	},
	5,
	3
);

/**
 * Preserve Legacy Sidebar IDs
 *
 * Handle legacy sidebar ids, new ids will be
 * generated dynamically from the post id.
 *
 * @param string $sidebar_id Generated sidebar_id slug used in register_sidebar().
 * @param int    $post_id ID of a 'sidebar_instance' post.
 */
function preserve_legacy_sidebar_id( $sidebar_id, $post_id ) {
	$old_sidebar_id = get_post_meta( $post_id, 'sidebar_id', true );

	if ( ! empty( $old_sidebar_id ) ) {
		return $old_sidebar_id;
	}

	return $sidebar_id;
}
add_filter( 'ecs_sidebar_id', __NAMESPACE__ . '\\preserve_legacy_sidebar_id', 10, 2 );
