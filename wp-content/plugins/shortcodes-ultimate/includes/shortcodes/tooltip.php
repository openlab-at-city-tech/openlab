<?php

su_add_shortcode( array(
		'id' => 'tooltip',
		'callback' => 'su_shortcode_tooltip',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/tooltip.svg',
		'name' => __( 'Tooltip', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'other',
		'atts' => array(
			'style' => array(
				'type' => 'select',
				'values' => array(
					'light' => __( 'Basic: Light', 'shortcodes-ultimate' ),
					'dark' => __( 'Basic: Dark', 'shortcodes-ultimate' ),
					'yellow' => __( 'Basic: Yellow', 'shortcodes-ultimate' ),
					'green' => __( 'Basic: Green', 'shortcodes-ultimate' ),
					'red' => __( 'Basic: Red', 'shortcodes-ultimate' ),
					'blue' => __( 'Basic: Blue', 'shortcodes-ultimate' ),
					'youtube' => __( 'Youtube', 'shortcodes-ultimate' ),
					'tipsy' => __( 'Tipsy', 'shortcodes-ultimate' ),
					'bootstrap' => __( 'Bootstrap', 'shortcodes-ultimate' ),
					'jtools' => __( 'jTools', 'shortcodes-ultimate' ),
					'tipped' => __( 'Tipped', 'shortcodes-ultimate' ),
					'cluetip' => __( 'Cluetip', 'shortcodes-ultimate' ),
				),
				'default' => 'yellow',
				'name' => __( 'Style', 'shortcodes-ultimate' ),
				'desc' => __( 'Tooltip window style', 'shortcodes-ultimate' )
			),
			'position' => array(
				'type' => 'select',
				'values' => array(
					'north' => __( 'Top', 'shortcodes-ultimate' ),
					'south' => __( 'Bottom', 'shortcodes-ultimate' ),
					'west' => __( 'Left', 'shortcodes-ultimate' ),
					'east' => __( 'Right', 'shortcodes-ultimate' )
				),
				'default' => 'top',
				'name' => __( 'Position', 'shortcodes-ultimate' ),
				'desc' => __( 'Tooltip position', 'shortcodes-ultimate' )
			),
			'shadow' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Shadow', 'shortcodes-ultimate' ),
				'desc' => __( 'Add shadow to tooltip. This option is only works with basic styes, e.g. blue, green etc.', 'shortcodes-ultimate' )
			),
			'rounded' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Rounded corners', 'shortcodes-ultimate' ),
				'desc' => __( 'Use rounded for tooltip. This option is only works with basic styes, e.g. blue, green etc.', 'shortcodes-ultimate' )
			),
			'size' => array(
				'type' => 'select',
				'values' => array(
					'default' => __( 'Default', 'shortcodes-ultimate' ),
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				),
				'default' => 'default',
				'name' => __( 'Font size', 'shortcodes-ultimate' ),
				'desc' => __( 'Tooltip font size', 'shortcodes-ultimate' )
			),
			'title' => array(
				'default' => '',
				'name' => __( 'Tooltip title', 'shortcodes-ultimate' ),
				'desc' => __( 'Enter title for tooltip window. Leave this field empty to hide the title', 'shortcodes-ultimate' )
			),
			'content' => array(
				'default' => __( 'Tooltip text', 'shortcodes-ultimate' ),
				'name' => __( 'Tooltip content', 'shortcodes-ultimate' ),
				'desc' => __( 'Enter tooltip content here', 'shortcodes-ultimate' )
			),
			'behavior' => array(
				'type' => 'select',
				'values' => array(
					'hover' => __( 'Show and hide on mouse hover', 'shortcodes-ultimate' ),
					'click' => __( 'Show and hide by mouse click', 'shortcodes-ultimate' ),
					'always' => __( 'Always visible', 'shortcodes-ultimate' )
				),
				'default' => 'hover',
				'name' => __( 'Behavior', 'shortcodes-ultimate' ),
				'desc' => __( 'Select tooltip behavior', 'shortcodes-ultimate' )
			),
			'close' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Close button', 'shortcodes-ultimate' ),
				'desc' => __( 'Show close button', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( 'Hover me to open tooltip', 'shortcodes-ultimate' ),
		'desc' => __( 'Tooltip window with custom content', 'shortcodes-ultimate' ),
		'icon' => 'comment-o',
	) );

function su_shortcode_tooltip( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'style'        => 'yellow',
			'position'     => 'north',
			'shadow'       => 'no',
			'rounded'      => 'no',
			'size'         => 'default',
			'title'        => '',
			'content'      => __( 'Tooltip text', 'shortcodes-ultimate' ),
			'behavior'     => 'hover',
			'close'        => 'no',
			'class'        => ''
		), $atts, 'tooltip' );

	// Prepare style
	$atts['style'] = in_array( $atts['style'], array( 'light', 'dark', 'green', 'red', 'blue', 'youtube', 'tipsy', 'bootstrap', 'jtools', 'tipped', 'cluetip' ) )
		? $atts['style']
		: 'plain';

	// Position
	$atts['position'] = str_replace( array( 'top', 'right', 'bottom', 'left' ), array( 'north', 'east', 'south', 'west' ), $atts['position'] );
	$position = array(
		'my' => str_replace( array( 'north', 'east', 'south', 'west' ), array( 'bottom center', 'center left', 'top center', 'center right' ), $atts['position'] ),
		'at' => str_replace( array( 'north', 'east', 'south', 'west' ), array( 'top center', 'center right', 'bottom center', 'center left' ), $atts['position'] )
	);

	// Prepare classes
	$classes = array( 'su-qtip qtip-' . $atts['style'] );
	$classes[] = 'su-qtip-size-' . $atts['size'];

	if ( $atts['shadow'] === 'yes' ) {
		$classes[] = 'qtip-shadow';
	}

	if ( $atts['rounded'] === 'yes' ) {
		$classes[] = 'qtip-rounded';
	}

	// Query assets
	su_query_asset( 'css', 'qtip' );
	su_query_asset( 'css', 'su-shortcodes' );
	su_query_asset( 'js', 'jquery' );
	su_query_asset( 'js', 'qtip' );
	su_query_asset( 'js', 'su-other-shortcodes' );

	return '<span class="su-tooltip' . su_get_css_class( $atts ) . '" data-close="' . $atts['close'] . '" data-behavior="' . $atts['behavior'] . '" data-my="' . $position['my'] . '" data-at="' . $position['at'] . '" data-classes="' . implode( ' ', $classes ) . '" data-title="' . $atts['title'] . '" title="' . esc_attr( $atts['content'] ) . '">' . do_shortcode( $content ) . '</span>';

}
