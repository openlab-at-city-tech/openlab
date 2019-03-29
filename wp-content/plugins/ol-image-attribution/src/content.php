<?php
/**
 * Front-end integrations.
 */

namespace OpenLab\ImageAttribution\Content;

use function OpenLab\ImageAttribution\Helpers\get_supported_post_types;
use function OpenLab\ImageAttribution\Helpers\get_the_image_attribution;
use function OpenLab\ImageAttribution\Helpers\get_the_attached_images;
use function OpenLab\ImageAttribution\Helpers\add_image_cites_nums;

/**
 * Append attributions for images used in content.
 *
 * @param string $content
 * @return string $content
 */
function attached_media_attributions( $content ) {
	if ( ! is_singular( get_supported_post_types() ) ) {
		return $content;
	}

	$images = get_the_attached_images( $content );

	if ( empty( $images ) ) {
		return $content;
	}

	$content = add_image_cites_nums( $images, $content );

	$cites = '<div class="wp-image-attributions"><ol>';

	foreach ( array_keys( $images ) as $image_id ) {
		$cites .= sprintf(
			'<li id="cite-%2$d" class="wp-caption-text">%1$s</li>',
			get_the_image_attribution( $image_id ),
			$image_id
		);
	}

	$cites   .= '</ol></div>';
	$content .= $cites;

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\attached_media_attributions', 100 );