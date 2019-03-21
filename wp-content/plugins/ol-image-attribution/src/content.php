<?php
/**
 * Front-end integrations.
 */

namespace OpenLab\ImageAttribution\Content;

use function OpenLab\ImageAttribution\Helpers\get_the_image_attribution;
use function OpenLab\ImageAttribution\Helpers\get_the_attached_image_ids;

/**
 * Append attributions for images used in content.
 *
 * @param string $content
 * @return string
 */
function attached_media_attributions( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	$images = get_the_attached_image_ids( get_queried_object_id(), $content );

	if ( empty( $images ) ) {
		return $content;
	}

	$html   = '<div class="wp-image-attributions">';

	foreach ( $images as $image_id ) {
		$html .= '<span class="wp-caption-text">' . get_the_image_attribution( $image_id ) . '</span>';
	}

	$html    .= '</div>';
	$content .= $html;

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\attached_media_attributions', 100 );