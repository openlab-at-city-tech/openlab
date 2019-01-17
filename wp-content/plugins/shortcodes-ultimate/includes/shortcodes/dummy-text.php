<?php

su_add_shortcode( array(
		'id' => 'dummy_text',
		'callback' => 'su_shortcode_dummy_text',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/dummy_text.svg',
		'name' => __( 'Dummy text', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'content',
		'atts' => array(
			'what' => array(
				'type' => 'select',
				'values' => array(
					'paras' => __( 'Paragraphs', 'shortcodes-ultimate' ),
					'words' => __( 'Words', 'shortcodes-ultimate' ),
					'bytes' => __( 'Bytes', 'shortcodes-ultimate' ),
				),
				'default' => 'paras',
				'name' => __( 'What', 'shortcodes-ultimate' ),
				'desc' => __( 'What to generate', 'shortcodes-ultimate' )
			),
			'amount' => array(
				'type' => 'slider',
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 1,
				'name' => __( 'Amount', 'shortcodes-ultimate' ),
				'desc' => __( 'How many items (paragraphs or words) to generate. Minimum words amount is 5', 'shortcodes-ultimate' )
			),
			'cache' => array(
				'type' => 'bool',
				'default' => 'yes',
				'name' => __( 'Cache', 'shortcodes-ultimate' ),
				'desc' => __( 'Generated text will be cached. Be careful with this option. If you disable it and insert many dummy_text shortcodes the page load time will be highly increased', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'desc' => __( 'Text placeholder', 'shortcodes-ultimate' ),
		'icon' => 'text-height',
	) );

function su_shortcode_dummy_text( $atts = null, $content = null ) {
	$atts = shortcode_atts( array(
			'amount' => 1,
			'what'   => 'paras',
			'cache'  => 'yes',
			'class'  => ''
		), $atts, 'dummy_text' );
	$transient = 'su/cache/dummy_text/' . sanitize_text_field( $atts['what'] ) . '/' . intval( $atts['amount'] );
	$return = get_transient( $transient );
	if ( $return && $atts['cache'] === 'yes' ) return $return;
	else {
		$xml = simplexml_load_file( 'http://www.lipsum.com/feed/xml?amount=' . $atts['amount'] . '&what=' . $atts['what'] . '&start=0' );
		$return = '<div class="su-dummy-text' . su_get_css_class( $atts ) . '">' . wpautop( str_replace( "\n", "\n\n", $xml->lipsum ) ) . '</div>';
		set_transient( $transient, $return, 60*60*24*30 );
		return $return;
	}
}
