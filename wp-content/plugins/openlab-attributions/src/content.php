<?php

namespace OpenLab\Attributions\Content;

use function OpenLab\Attributions\Helpers\get_the_image_attribution;

/**
 * Search for images in the content.
 * Fires before `do_shortcodes()`.
 *
 * @param string $content
 * @return string $content
 */
function do_images( $content ) {
	if ( false === strpos( $content, '<img' ) ) {
		return $content;
	}

	$content = preg_replace_callback(
		'/<img [^>]+>/',
		__NAMESPACE__ . '\\replace_with_ref',
		$content
	);

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\do_images' );

/**
 * Adds image attribution [ref] shortcode.
 * This will be reformatted later by shortcode helper.
 *
 * @param array $matches Regular expression match array
 * @return string $image Updated image tag.
 */
function replace_with_ref( $matches ) {
	$image = $matches[0];

	if ( preg_match( '/wp-image-([0-9]+)/i', $image, $class ) ) {
		$image_id = $class[1];

		return sprintf(
			'%1$s[ref]%2$s[/ref]',
			$image,
			get_the_image_attribution( $image_id )
		);
	}

	return $image;
}
