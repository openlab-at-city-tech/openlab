<?php

su_add_shortcode( array(
		'id' => 'box',
		'callback' => 'su_shortcode_box',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/box.svg',
		'name' => __( 'Box', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'box',
		'atts' => array(
			'title' => array(
				'values' => array( ),
				'default' => __( 'Box title', 'shortcodes-ultimate' ),
				'name' => __( 'Title', 'shortcodes-ultimate' ), 'desc' => __( 'Text for the box title', 'shortcodes-ultimate' )
			),
			'style' => array(
				'type' => 'select',
				'values' => array(
					'default' => __( 'Default', 'shortcodes-ultimate' ),
					'soft' => __( 'Soft', 'shortcodes-ultimate' ),
					'glass' => __( 'Glass', 'shortcodes-ultimate' ),
					'bubbles' => __( 'Bubbles', 'shortcodes-ultimate' ),
					'noise' => __( 'Noise', 'shortcodes-ultimate' )
				),
				'default' => 'default',
				'name' => __( 'Style', 'shortcodes-ultimate' ),
				'desc' => __( 'Box style preset', 'shortcodes-ultimate' )
			),
			'box_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#333333',
				'name' => __( 'Color', 'shortcodes-ultimate' ),
				'desc' => __( 'Color for the box title and borders', 'shortcodes-ultimate' )
			),
			'title_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#FFFFFF',
				'name' => __( 'Title text color', 'shortcodes-ultimate' ), 'desc' => __( 'Color for the box title text', 'shortcodes-ultimate' )
			),
			'radius' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 20,
				'step' => 1,
				'default' => 3,
				'name' => __( 'Radius', 'shortcodes-ultimate' ),
				'desc' => __( 'Box corners radius', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( 'Box content', 'shortcodes-ultimate' ),
		'desc' => __( 'Colored box with caption', 'shortcodes-ultimate' ),
		'icon' => 'list-alt',
	) );

function su_shortcode_box( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'title'       => __( 'This is box title', 'shortcodes-ultimate' ),
			'style'       => 'default',
			'box_color'   => '#333333',
			'title_color' => '#FFFFFF',
			'color'       => null, // 3.x
			'radius'      => '3',
			'class'       => ''
		), $atts, 'box' );

	if ( $atts['color'] !== null ) {
		$atts['box_color'] = $atts['color'];
	}

	$atts['radius'] = is_numeric( $atts['radius'] )
		? intval( $atts['radius'] )
		: 0;

	$atts['inner_radius'] = $atts['radius'] > 2
		? $atts['radius'] - 2
		: 0;

	su_query_asset( 'css', 'su-shortcodes' );

	// Return result
	return sprintf(
		'<div class="su-box su-box-style-%1$s%2$s" style="border-color:%3$s;border-radius:%4$spx"><div class="su-box-title" style="background-color:%5$s;color:%6$s;border-top-left-radius:%7$spx;border-top-right-radius:%7$spx">%8$s</div><div class="su-box-content su-clearfix" style="border-bottom-left-radius:%7$spx;border-bottom-right-radius:%7$spx">%9$s</div></div>',
		esc_attr( $atts['style'] ),
		su_get_css_class( $atts ),
		su_hex_shift( $atts['box_color'], 'darker', 20 ),
		$atts['radius'],
		$atts['box_color'],
		$atts['title_color'],
		$atts['inner_radius'],
		su_do_attribute( $atts['title'] ),
		su_do_nested_shortcodes( $content, 'box' )
	);

}
