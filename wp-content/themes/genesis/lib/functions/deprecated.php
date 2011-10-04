<?php
/**
 * This file is home to the code that has
 * been deprecated / replaced by other code.
 *
 * It serves as a compatibility mechanism.
 *
 * @package Genesis
 */

/**
 * @deprecated in 1.6
 */
function genesis_ie8_js() {
	_deprecated_function( __FUNCTION__, '1.6' );
}

/**
 * @deprecated in 1.5
 */
function genesis_post_date($format = '', $label = '') {
	_deprecated_function( __FUNCTION__, '1.5', 'genesis_post_date_shortcode()' );

	echo genesis_post_date_shortcode( array( 'format' => $format, 'label' => $label ) );
}

/**
 * @deprecated in 1.5
 */
function genesis_post_author_posts_link($label = '') {
	_deprecated_function( __FUNCTION__, '1.5', 'genesis_post_author_posts_link_shortcode()' );

	echo genesis_post_author_posts_link_shortcode( array( 'before' => $label ) );
}

/**
 * @deprecated in 1.5
 */
function genesis_post_comments_link($zero = false, $one = false, $more = false) {
	_deprecated_function( __FUNCTION__, '1.5', 'genesis_post_comments_shortcode()' );

	echo genesis_post_comments_shortcode( array( 'zero' => $zero, 'one' => $one, 'more' => $more ) );
}

/**
 * @deprecated in 1.5
 */
function genesis_post_categories_link($sep = ', ', $label = '') {
	_deprecated_function( __FUNCTION__, '1.5', 'genesis_post_categories_shortcode()' );

	echo genesis_post_categories_shortcode( array( 'sep' => $sep, 'before' => $label ) );
}

/**
 * @deprecated in 1.5
 */
function genesis_post_tags_link($sep = ', ', $label = '') {
	_deprecated_function( __FUNCTION__, '1.5', 'genesis_post_tags_shortcode()' );

	echo genesis_post_tags_shortcode( array( 'sep' => $sep, 'before' => $label ) );
}

/**
 * @deprecated in 1.2
 */
function genesis_add_image_size($name, $width = 0, $height = 0, $crop = FALSE) {
	_deprecated_function( __FUNCTION__, '1.2', 'add_image_size()' );

	add_image_size($name, $width, $height, $crop);
}

/**
 * @deprecated in 1.2
 */
function genesis_add_intermediate_sizes($deprecated = '') {
	_deprecated_function( __FUNCTION__, '1.2' );

	return array();
}

/**
 * @deprecated in 1.2
 */
function genesis_comment() {
	_deprecated_function( __FUNCTION__, '1.2', 'genesis_after_comment()' );

	do_action('genesis_after_comment');
}