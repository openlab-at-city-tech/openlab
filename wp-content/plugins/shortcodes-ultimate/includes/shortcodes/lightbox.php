<?php

su_add_shortcode( array(
		'id' => 'lightbox',
		'callback' => 'su_shortcode_lightbox',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/lightbox.svg',
		'name' => __( 'Lightbox', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'gallery',
		'possible_sibling' => 'lightbox_content',
		'article' => 'http://docs.getshortcodes.com/article/76-how-to-use-lightbox-shortcode',
		'atts' => array(
			'type' => array(
				'type' => 'select',
				'values' => array(
					'iframe' => __( 'Iframe', 'shortcodes-ultimate' ),
					'image' => __( 'Image', 'shortcodes-ultimate' ),
					'inline' => __( 'Inline (html content)', 'shortcodes-ultimate' )
				),
				'default' => 'iframe',
				'name' => __( 'Content type', 'shortcodes-ultimate' ),
				'desc' => __( 'Select type of the lightbox window content', 'shortcodes-ultimate' )
			),
			'src' => array(
				'default' => '',
				'name' => __( 'Content source', 'shortcodes-ultimate' ),
				'desc' => __( 'Insert here URL or CSS selector. Use URL for Iframe and Image content types. Use CSS selector for Inline content type.<br />Example values:<br /><b%value>http://www.youtube.com/watch?v=XXXXXXXXX</b> - YouTube video (iframe)<br /><b%value>http://example.com/wp-content/uploads/image.jpg</b> - uploaded image (image)<br /><b%value>http://example.com/</b> - any web page (iframe)<br /><b%value>#my-custom-popup</b> - any HTML content (inline)', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( 'Click here to open lightbox', 'shortcodes-ultimate' ),
		'desc' => __( 'Lightbox window with custom content', 'shortcodes-ultimate' ),
		'icon' => 'external-link',
	) );

function su_shortcode_lightbox( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'src'   => false,
			'type'  => 'iframe',
			'class' => ''
		), $atts, 'lightbox' );

	if ( ! $atts['src'] ) {
		return su_error_message( 'Lightbox', __( 'please specify correct source', 'shortcodes-ultimate' ) );
	}

	su_query_asset( 'css', 'magnific-popup' );
	su_query_asset( 'js', 'jquery' );
	su_query_asset( 'js', 'magnific-popup' );
	su_query_asset( 'js', 'su-other-shortcodes' );

	return '<span class="su-lightbox' . su_get_css_class( $atts ) . '" data-mfp-src="' . su_do_attribute( $atts['src'] ) . '" data-mfp-type="' . $atts['type'] . '">' . do_shortcode( $content ) . '</span>';

}
