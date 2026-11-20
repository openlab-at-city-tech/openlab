<?php

su_add_shortcode(
	array(
		'id'       => 'dummy_image',
		'callback' => 'su_shortcode_dummy_image',
		'image'    => su_get_plugin_url() . 'admin/images/shortcodes/dummy_image.svg',
		'name'     => __( 'Dummy image', 'shortcodes-ultimate' ),
		'type'     => 'single',
		'group'    => 'content',
		'atts'     => array(
			'width'  => array(
				'type'    => 'slider',
				'min'     => 10,
				'max'     => 1600,
				'step'    => 10,
				'default' => 600,
				'name'    => __( 'Width', 'shortcodes-ultimate' ),
				'desc'    => __( 'Image width', 'shortcodes-ultimate' ),
			),
			'height' => array(
				'type'    => 'slider',
				'min'     => 10,
				'max'     => 1600,
				'step'    => 10,
				'default' => 400,
				'name'    => __( 'Height', 'shortcodes-ultimate' ),
				'desc'    => __( 'Image height', 'shortcodes-ultimate' ),
			),
			'text'   => array(
				'type'    => 'text',
				'default' => '',
				'name'    => __( 'Text', 'shortcodes-ultimate' ),
				'desc'    => __( 'Custom text for this image', 'shortcodes-ultimate' ),
			),
			'class'  => array(
				'type'    => 'extra_css_class',
				'name'    => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc'    => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'desc'     => __( 'Image placeholder with random image', 'shortcodes-ultimate' ),
		'icon'     => 'picture-o',
	)
);

function su_shortcode_dummy_image( $atts = null, $content = null ) {
	$atts = shortcode_atts(
		array(
			'width'  => 600,
			'height' => 400,
			'text'   => '',
			'class'  => '',
		),
		$atts,
		'dummy_image'
	);
	$atts['width']  = max( 1, absint( $atts['width'] ) );
	$atts['height'] = max( 1, absint( $atts['height'] ) );
	$url            = 'https://placehold.co/' . $atts['width'] . 'x' . $atts['height'];

	if ( '' !== $atts['text'] ) {
		$url .= '?text=' . urlencode( $atts['text'] );
	}

	return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr__( 'Dummy image', 'shortcodes-ultimate' ) . '" width="' . esc_attr( $atts['width'] ) . '" height="' . esc_attr( $atts['height'] ) . '" class="su-dummy-image' . su_get_css_class( $atts ) . '" />';
}
