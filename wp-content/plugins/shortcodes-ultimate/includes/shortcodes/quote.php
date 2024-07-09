<?php

su_add_shortcode(
	array(
		'id'       => 'quote',
		'callback' => 'su_shortcode_quote',
		'image'    => su_get_plugin_url() . 'admin/images/shortcodes/quote.svg',
		'name'     => __( 'Quote', 'shortcodes-ultimate' ),
		'type'     => 'wrap',
		'group'    => 'box',
		'atts'     => array(
			'style' => array(
				'type'    => 'select',
				'values'  => su_get_available_styles_for( 'quote' ),
				'default' => 'default',
				'name'    => __( 'Style', 'shortcodes-ultimate' ),
				'desc'    => __( 'Choose style for this quote', 'shortcodes-ultimate' ),
			),
			'cite'  => array(
				'default' => '',
				'name'    => __( 'Cite', 'shortcodes-ultimate' ),
				'desc'    => __( 'Quote author name', 'shortcodes-ultimate' ),
			),
			'url'   => array(
				'values'  => array(),
				'default' => '',
				'name'    => __( 'Cite url', 'shortcodes-ultimate' ),
				'desc'    => __( 'Url of the quote author. Leave empty to disable link', 'shortcodes-ultimate' ),
			),
			'class' => array(
				'type'    => 'extra_css_class',
				'name'    => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc'    => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content'  => __( 'Quote', 'shortcodes-ultimate' ),
		'desc'     => __( 'Blockquote alternative', 'shortcodes-ultimate' ),
		'icon'     => 'quote-right',
	)
);

function su_shortcode_quote( $atts = null, $content = null ) {

	$atts = shortcode_atts(
		array(
			'style' => 'default',
			'cite'  => '',
			'url'   => '',
			'class' => '',
		),
		$atts,
		'quote'
	);

	$atts['cite'] = su_do_attribute($atts['cite'], true);

	if ($atts['cite'] && $atts['url']) {
		$atts['cite'] = '<a href="' . esc_url( $atts['url'] ) . '" target="_blank">' . $atts['cite'] . '</a>';
	}

	if ($atts['cite']) {
		$atts['cite'] = '<span class="su-quote-cite">' . $atts['cite'] . '</span>';
		$atts['class'] .= ' su-quote-has-cite';
	}

	su_query_asset( 'css', 'su-shortcodes' );

	return '<div class="su-quote su-quote-style-' . esc_attr( $atts['style'] ) . su_get_css_class( $atts ) . '"><div class="su-quote-inner su-u-clearfix su-u-trim">' . su_do_nested_shortcodes( $content, 'quote' ) . $atts['cite'] . '</div></div>';

}
