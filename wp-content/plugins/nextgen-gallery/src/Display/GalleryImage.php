<?php

namespace Imagely\NGG\Display;

/**
 * Markup helpers for gallery image tags.
 */
class GalleryImage {

	/**
	 * Default CSS class added to every front-end gallery image tag.
	 *
	 * Site owners paste this into the exclusion list of lazy-load and image
	 * optimization plugins to opt galleries out of rewriting.
	 *
	 * @var string
	 */
	const DEFAULT_CLASS = 'ngg-gallery-thumbnail-img';

	/**
	 * Build an escaped HTML attribute string for a gallery image tag.
	 *
	 * The returned string has a leading space and is meant to be echoed
	 * immediately after "<img", e.g. `<img<?php echo GalleryImage::attributes(); ?> ...`.
	 *
	 * The `ngg_gallery_image_attributes` filter lets site owners and add-ons
	 * add attributes such as `skip-lazy` or `data-no-lazy` without overriding a
	 * template. Return `true` as the value for boolean attributes, or `false`
	 * / `null` to drop one.
	 *
	 * @param array $extra Extra attributes to merge in, as name => value pairs.
	 * @return string Escaped attribute string with a leading space.
	 */
	public static function attributes( $extra = [] ) {
		$attributes = array_merge( [ 'class' => self::DEFAULT_CLASS ], $extra );

		/**
		 * Filters the attributes rendered on a front-end gallery image tag.
		 *
		 * @param array $attributes Attribute name => value pairs.
		 */
		$attributes = \apply_filters( 'ngg_gallery_image_attributes', $attributes );

		// A filter callback that returns a non-array (e.g. forgets to return)
		// would otherwise empty the tag and strip the class from every image.
		if ( ! is_array( $attributes ) ) {
			$attributes = [ 'class' => self::DEFAULT_CLASS ];
		}

		$html = '';
		foreach ( $attributes as $name => $value ) {
			if ( false === $value || null === $value ) {
				continue;
			}
			if ( true === $value ) {
				$html .= ' ' . \esc_attr( $name );
				continue;
			}
			$html .= ' ' . \esc_attr( $name ) . '="' . \esc_attr( $value ) . '"';
		}

		return $html;
	}
}
