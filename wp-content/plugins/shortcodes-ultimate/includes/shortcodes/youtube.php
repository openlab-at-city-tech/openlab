<?php

su_add_shortcode( array(
		'id' => 'youtube',
		'callback' => 'su_shortcode_youtube',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/youtube.svg',
		'name' => __( 'YouTube', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'media',
		'atts' => array(
			'url' => array(
				'default' => '',
				'name' => __( 'Url', 'shortcodes-ultimate' ),
				'desc' => __( 'Url of YouTube page with video. Ex: http://youtube.com/watch?v=XXXXXX', 'shortcodes-ultimate' )
			),
			'width' => array(
				'type' => 'slider',
				'min' => 200,
				'max' => 1600,
				'step' => 20,
				'default' => 600,
				'name' => __( 'Width', 'shortcodes-ultimate' ),
				'desc' => __( 'Player width', 'shortcodes-ultimate' )
			),
			'height' => array(
				'type' => 'slider',
				'min' => 200,
				'max' => 1600,
				'step' => 20,
				'default' => 400,
				'name' => __( 'Height', 'shortcodes-ultimate' ),
				'desc' => __( 'Player height', 'shortcodes-ultimate' )
			),
			'responsive' => array(
				'type' => 'bool',
				'default' => 'yes',
				'name' => __( 'Responsive', 'shortcodes-ultimate' ),
				'desc' => __( 'Ignore width and height parameters and make player responsive', 'shortcodes-ultimate' )
			),
			'autoplay' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Autoplay', 'shortcodes-ultimate' ),
				'desc' => __( 'Play video automatically when a page is loaded. Please note, in modern browsers autoplay option only works with the mute option enabled', 'shortcodes-ultimate' )
			),
			'mute' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Mute', 'shortcodes-ultimate' ),
				'desc' => __( 'Mute the player', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'desc' => __( 'YouTube video', 'shortcodes-ultimate' ),
		'example' => 'media',
		'icon' => 'youtube-play',
	) );

function su_shortcode_youtube( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'url'        => false,
			'width'      => 600,
			'height'     => 400,
			'autoplay'   => 'no',
			'mute'       => 'no',
			'responsive' => 'yes',
			'class'      => ''
		), $atts, 'youtube' );

	if ( ! $atts['url'] ) {
		return su_error_message( 'YouTube', __( 'please specify correct url', 'shortcodes-ultimate' ) );
	}

	$atts['url'] = su_do_attribute( $atts['url'] );

	$video_id = preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $atts['url'], $match )
		? $match[1]
		: false;

	if ( ! $video_id ) {
		return su_error_message( 'YouTube', __( 'please specify correct url', 'shortcodes-ultimate' ) );
	}

	$url_params = array();

	if ( $atts['autoplay'] === 'yes' ) {
		$url_params['autoplay'] = '1';
	}

	if ( $atts['mute'] === 'yes' ) {
		$url_params['mute'] = '1';
	}

	$domain = strpos( $atts['url'], 'youtube-nocookie.com' ) !== false
		? 'www.youtube-nocookie.com'
		: 'www.youtube.com';

	su_query_asset( 'css', 'su-shortcodes' );

	return '<div class="su-youtube su-responsive-media-' . $atts['responsive'] . su_get_css_class( $atts ) . '"><iframe width="' . $atts['width'] . '" height="' . $atts['height'] . '" src="https://' . $domain . '/embed/' . $video_id . '?' . http_build_query( $url_params ) . '" frameborder="0" allowfullscreen="true"></iframe></div>';

}
