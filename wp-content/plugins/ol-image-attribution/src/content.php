<?php
/**
 * Front-end integrations.
 */

namespace OpenLab\ImageAttribution\Content;

use function OpenLab\ImageAttribution\Helpers\get_the_image_attribution;

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

	$html   = '<div class="wp-image-attributions">';
	$images = get_attached_media( 'image' );

	foreach ( $images as $image ) {
		$html .= '<span class="wp-caption-text">' . get_the_image_attribution( $image->ID ) . '</span>';
	}
	$html .= '</div>';

	return $content .= $html;
}
add_filter( 'the_content', __NAMESPACE__ . '\\attached_media_attributions', 100 );