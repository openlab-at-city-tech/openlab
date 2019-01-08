<?php

su_add_shortcode( array(
		'id' => 'divider',
		'callback' => 'su_shortcode_divider',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/divider.svg',
		'name' => __( 'Divider', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'content',
		'atts' => array(
			'top' => array(
				'type' => 'bool',
				'default' => 'yes',
				'name' => __( 'Show TOP link', 'shortcodes-ultimate' ),
				'desc' => __( 'Show link to top of the page or not', 'shortcodes-ultimate' )
			),
			'text' => array(
				'values' => array( ),
				'default' => __( 'Go to top', 'shortcodes-ultimate' ),
				'name' => __( 'Link text', 'shortcodes-ultimate' ), 'desc' => __( 'Text for the GO TOP link', 'shortcodes-ultimate' )
			),
			'style' => array(
				'type' => 'select',
				'values' => array(
					'default' => __( 'Default', 'shortcodes-ultimate' ),
					'dotted'  => __( 'Dotted', 'shortcodes-ultimate' ),
					'dashed'  => __( 'Dashed', 'shortcodes-ultimate' ),
					'double'  => __( 'Double', 'shortcodes-ultimate' )
				),
				'default' => 'default',
				'name' => __( 'Style', 'shortcodes-ultimate' ),
				'desc' => __( 'Choose style for this divider', 'shortcodes-ultimate' )
			),
			'divider_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#999999',
				'name' => __( 'Divider color', 'shortcodes-ultimate' ),
				'desc' => __( 'Pick the color for divider', 'shortcodes-ultimate' )
			),
			'link_color' => array(
				'type' => 'color',
				'values' => array( ),
				'default' => '#999999',
				'name' => __( 'Link color', 'shortcodes-ultimate' ),
				'desc' => __( 'Pick the color for TOP link', 'shortcodes-ultimate' )
			),
			'size' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 40,
				'step' => 1,
				'default' => 3,
				'name' => __( 'Size', 'shortcodes-ultimate' ),
				'desc' => __( 'Height of the divider (in pixels)', 'shortcodes-ultimate' )
			),
			'margin' => array(
				'type' => 'slider',
				'min' => 0,
				'max' => 200,
				'step' => 5,
				'default' => 15,
				'name' => __( 'Margin', 'shortcodes-ultimate' ),
				'desc' => __( 'Adjust the top and bottom margins of this divider (in pixels)', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'desc' => __( 'Content divider with optional TOP link', 'shortcodes-ultimate' ),
		'icon' => 'ellipsis-h',
	) );

function su_shortcode_divider( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'top'           => 'yes',
			'text'          => __( 'Go to top', 'shortcodes-ultimate' ),
			'style'         => 'default',
			'divider_color' => '#999999',
			'link_color'    => '#999999',
			'size'          => '3',
			'margin'        => '15',
			'class'         => ''
		), $atts, 'divider' );

	// Prepare TOP link
	$top = $atts['top'] === 'yes'
		? '<a href="#" style="color:' . $atts['link_color'] . '">' . su_do_attribute( $atts['text'] ) . '</a>'
		: '';

	su_query_asset( 'css', 'su-shortcodes' );

	return '<div class="su-divider su-divider-style-' . $atts['style'] . su_get_css_class( $atts ) . '" style="margin:' . $atts['margin'] . 'px 0;border-width:' . $atts['size'] . 'px;border-color:' . $atts['divider_color'] . '">' . $top . '</div>';

}
