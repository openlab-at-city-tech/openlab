<?php

/**
 * Filters.
 *
 * @since        5.0.4
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */

/**
 * Disable wptexturize filter for nestable shortcodes.
 *
 * @since  5.0.4
 * @param array   $shortcodes Shortcodes to not texturize.
 * @return array              Shortcodes to not texturize.
 */
function su_filter_disable_wptexturize( $shortcodes ) {

	$prefix = su_get_shortcode_prefix();

	$exclude = array(
		$prefix . 'spoiler',
		$prefix . 'row',
		$prefix . 'column',
		$prefix . 'list',
		$prefix . 'note',
		$prefix . 'box',
	);

	return array_merge( $shortcodes, $exclude );

}

/**
 * Custom formatting filter.
 *
 * @since  5.0.4
 * @param string  $content
 * @return string Formatted content with clean shortcodes content.
 */
function su_filter_custom_formatting( $content ) {

	$replacements = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);

	return strtr( $content, $replacements );

}

/**
 * Adds 'Slide Link' field at attachment page.
 *
 * @since  5.0.5
 */
function su_slide_link_input( $form_fields, $post ) {

	$form_fields['su_slide_link'] = array(
		'label' => __( 'Slide link', 'shortcodes-ultimate' ),
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'su_slide_link', true ),
		'helps' => sprintf( '<strong>%s</strong><br>%s', __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ), __( 'Use this field to add custom links to slides used with Slider, Carousel and Custom Gallery shortcodes', 'shortcodes-ultimate' ) ),
	);

	return $form_fields;

}

/**
 * Saves 'Slide Link' field.
 *
 * @since  5.0.5
 */
function su_slide_link_save( $post, $attachment ) {

	if ( isset( $attachment['su_slide_link'] ) ) {
		update_post_meta( $post['ID'], 'su_slide_link', $attachment['su_slide_link'] );
	}

	return $post;

}
