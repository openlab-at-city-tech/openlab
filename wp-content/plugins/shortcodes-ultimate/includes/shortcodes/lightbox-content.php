<?php

su_add_shortcode( array(
		'id' => 'lightbox_content',
		'callback' => 'su_shortcode_lightbox_content',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/lightbox_content.svg',
		'name' => __( 'Lightbox content', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'gallery',
		'required_sibling' => 'lightbox',
		'article' => 'http://docs.getshortcodes.com/article/76-how-to-use-lightbox-shortcode',
		'atts' => array(
			'id' => array(
				'default' => '',
				'name' => __( 'ID', 'shortcodes-ultimate' ),
				'desc' => sprintf( __( 'Enter here the ID from Content source field. %s Example value: %s', 'shortcodes-ultimate' ), '<br>', '<b%value>my-custom-popup</b>' )
			),
			'width' => array(
				'default' => '50%',
				'name' => __( 'Width', 'shortcodes-ultimate' ),
				'desc' => sprintf( __( 'Adjust the width for inline content (in pixels or percents). %s Example values: %s, %s, %s', 'shortcodes-ultimate' ), '<br>', '<b%value>300px</b>', '<b%value>600px</b>', '<b%value>90%</b>' )
			),
			'margin' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 600,
				'step' => 5,
				'default' => 40,
				'name' => __( 'Margin', 'shortcodes-ultimate' ),
				'desc' => __( 'Adjust the margin for inline content (in pixels)', 'shortcodes-ultimate' )
			),
			'padding' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 600,
				'step' => 5,
				'default' => 40,
				'name' => __( 'Padding', 'shortcodes-ultimate' ),
				'desc' => __( 'Adjust the padding for inline content (in pixels)', 'shortcodes-ultimate' )
			),
			'text_align' => array(
				'type' => 'select',
				'values' => array(
					'left'   => __( 'Left', 'shortcodes-ultimate' ),
					'center' => __( 'Center', 'shortcodes-ultimate' ),
					'right'  => __( 'Right', 'shortcodes-ultimate' )
				),
				'default' => 'center',
				'name' => __( 'Text alignment', 'shortcodes-ultimate' ),
				'desc' => __( 'Select the text alignment', 'shortcodes-ultimate' )
			),
			'background' => array(
				'type' => 'color',
				'default' => '#FFFFFF',
				'name' => __( 'Background color', 'shortcodes-ultimate' ),
				'desc' => __( 'Pick a background color', 'shortcodes-ultimate' )
			),
			'color' => array(
				'type' => 'color',
				'default' => '#333333',
				'name' => __( 'Text color', 'shortcodes-ultimate' ),
				'desc' => __( 'Pick a text color', 'shortcodes-ultimate' )
			),
			'color' => array(
				'type' => 'color',
				'default' => '#333333',
				'name' => __( 'Text color', 'shortcodes-ultimate' ),
				'desc' => __( 'Pick a text color', 'shortcodes-ultimate' )
			),
			'shadow' => array(
				'type' => 'shadow',
				'default' => '0px 0px 15px #333333',
				'name' => __( 'Shadow', 'shortcodes-ultimate' ),
				'desc' => __( 'Adjust the shadow for content box', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( 'Inline content', 'shortcodes-ultimate' ),
		'desc' => __( 'Inline content for lightbox', 'shortcodes-ultimate' ),
		'icon' => 'external-link',
	) );

function su_shortcode_lightbox_content( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'id'         => '',
			'width'      => '50%',
			'margin'     => '40',
			'padding'    => '40',
			'text_align' => 'center',
			'background' => '#FFFFFF',
			'color'      => '#333333',
			'shadow'     => '0px 0px 15px #333333',
			'class'      => ''
		), $atts, 'lightbox_content' );

	su_query_asset( 'css', 'su-shortcodes' );

	if ( ! $atts['id'] ) {
		return su_error_message( 'Lightbox content', __( 'please specify correct ID for this block. You should use same ID as in the Content source field (when inserting lightbox shortcode)', 'shortcodes-ultimate' ) );
	}

	$return = '<div class="su-lightbox-content ' . su_get_css_class( $atts ) . '" id="' . trim( $atts['id'], '#' ) . '" style="display:none;width:' . $atts['width'] . ';margin-top:' . $atts['margin'] . 'px;margin-bottom:' . $atts['margin'] . 'px;padding:' . $atts['padding'] . 'px;background-color:' . $atts['background'] . ';color:' . $atts['color'] . ';box-shadow:' . $atts['shadow'] . ';text-align:' . $atts['text_align'] . '">' . do_shortcode( $content ) . '</div>';

	return did_action( 'su/generator/preview/before' )
		? '<div class="su-lightbox-content-preview">' . $return . '</div>'
		: $return;

}
