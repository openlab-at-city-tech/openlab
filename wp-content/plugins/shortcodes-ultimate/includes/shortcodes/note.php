<?php

su_add_shortcode( array(
		'id' => 'note',
		'callback' => 'su_shortcode_note',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/note.svg',
		'name' => __( 'Note', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'box',
		'atts' => array(
			'note_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#FFFF66',
				'name' => __( 'Background', 'shortcodes-ultimate' ), 'desc' => __( 'Note background color', 'shortcodes-ultimate' )
			),
			'text_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#333333',
				'name' => __( 'Text color', 'shortcodes-ultimate' ),
				'desc' => __( 'Note text color', 'shortcodes-ultimate' )
			),
			'radius' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 20,
				'step' => 1,
				'default' => 3,
				'name' => __( 'Radius', 'shortcodes-ultimate' ), 'desc' => __( 'Note corners radius', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( 'Note text', 'shortcodes-ultimate' ),
		'desc' => __( 'Colored box', 'shortcodes-ultimate' ),
		'icon' => 'list-alt',
	) );

function su_shortcode_note( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'note_color' => '#FFFF66',
			'text_color' => '#333333',
			'background' => null, // 3.x
			'color'      => null, // 3.x
			'radius'     => '3',
			'class'      => ''
		), $atts, 'note' );

	if ( $atts['color'] !== null ) {
		$atts['note_color'] = $atts['color'];
	}

	if ( $atts['background'] !== null ) {
		$atts['note_color'] = $atts['background'];
	}

	// Prepare border-radius
	$radius = $atts['radius'] != '0'
		? 'border-radius:' . $atts['radius'] . 'px;-moz-border-radius:' . $atts['radius'] . 'px;-webkit-border-radius:' . $atts['radius'] . 'px;'
		: '';

	su_query_asset( 'css', 'su-shortcodes' );

	return '<div class="su-note' . su_get_css_class( $atts ) . '" style="border-color:' . su_hex_shift( $atts['note_color'], 'darker', 10 ) . ';' . $radius . '"><div class="su-note-inner su-clearfix" style="background-color:' . $atts['note_color'] . ';border-color:' . su_hex_shift( $atts['note_color'], 'lighter', 80 ) . ';color:' . $atts['text_color'] . ';' . $radius . '">' . su_do_nested_shortcodes( $content, 'note' ) . '</div></div>';

}
