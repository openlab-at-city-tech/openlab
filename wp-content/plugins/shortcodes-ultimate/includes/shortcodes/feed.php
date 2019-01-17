<?php

su_add_shortcode( array(
		'id' => 'feed',
		'callback' => 'su_shortcode_feed',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/feed.svg',
		'name' => __( 'RSS feed', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'content other',
		'atts' => array(
			'url' => array(
				'values' => array( ),
				'default' => '',
				'name' => __( 'Url', 'shortcodes-ultimate' ),
				'desc' => __( 'Url to RSS-feed', 'shortcodes-ultimate' )
			),
			'limit' => array(
				'type' => 'slider',
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 3,
				'name' => __( 'Limit', 'shortcodes-ultimate' ), 'desc' => __( 'Number of items to show', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'desc' => __( 'Feed grabber', 'shortcodes-ultimate' ),
		'icon' => 'rss',
	) );

function su_shortcode_feed( $atts = null, $content = null ) {
	$atts = shortcode_atts( array(
			'url'   => get_bloginfo_rss( 'rss2_url' ),
			'limit' => 3,
			'class' => ''
		), $atts, 'feed' );
	if ( !function_exists( 'wp_rss' ) ) include_once ABSPATH . WPINC . '/rss.php';
	ob_start();
	echo '<div class="su-feed' . su_get_css_class( $atts ) . '">';
	wp_rss( $atts['url'], $atts['limit'] );
	echo '</div>';
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}
