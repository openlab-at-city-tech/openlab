<?php

su_add_shortcode(
	array(
		'id'       => 'spoiler',
		'callback' => 'su_shortcode_spoiler',
		'name'     => __( 'Spoiler', 'shortcodes-ultimate' ),
		'type'     => 'wrap',
		'group'    => 'box',
		'atts'     => array(
			'title'  => array(
				'default' => __( 'Spoiler title', 'shortcodes-ultimate' ),
				'name'    => __( 'Title', 'shortcodes-ultimate' ),
				'desc'    => __( 'Text in spoiler title', 'shortcodes-ultimate' ),
			),
			'open'   => array(
				'type'    => 'bool',
				'default' => 'no',
				'name'    => __( 'Open', 'shortcodes-ultimate' ),
				'desc'    => __( 'Is spoiler content visible by default', 'shortcodes-ultimate' ),
			),
			'style'  => array(
				'type'    => 'select',
				'values'  => array(
					'default' => __( 'Default', 'shortcodes-ultimate' ),
					'fancy'   => __( 'Fancy', 'shortcodes-ultimate' ),
					'simple'  => __( 'Simple', 'shortcodes-ultimate' ),
				),
				'default' => 'default',
				'name'    => __( 'Style', 'shortcodes-ultimate' ),
				'desc'    => __( 'Choose style for this spoiler', 'shortcodes-ultimate' ) . '%su_skins_link%',
			),
			'icon'   => array(
				'type'    => 'select',
				'values'  => array(
					'plus'           => __( 'Plus', 'shortcodes-ultimate' ),
					'plus-circle'    => __( 'Plus circle', 'shortcodes-ultimate' ),
					'plus-square-1'  => __( 'Plus square 1', 'shortcodes-ultimate' ),
					'plus-square-2'  => __( 'Plus square 2', 'shortcodes-ultimate' ),
					'arrow'          => __( 'Arrow', 'shortcodes-ultimate' ),
					'arrow-circle-1' => __( 'Arrow circle 1', 'shortcodes-ultimate' ),
					'arrow-circle-2' => __( 'Arrow circle 2', 'shortcodes-ultimate' ),
					'chevron'        => __( 'Chevron', 'shortcodes-ultimate' ),
					'chevron-circle' => __( 'Chevron circle', 'shortcodes-ultimate' ),
					'caret'          => __( 'Caret', 'shortcodes-ultimate' ),
					'caret-square'   => __( 'Caret square', 'shortcodes-ultimate' ),
					'folder-1'       => __( 'Folder 1', 'shortcodes-ultimate' ),
					'folder-2'       => __( 'Folder 2', 'shortcodes-ultimate' ),
				),
				'default' => 'plus',
				'name'    => __( 'Icon', 'shortcodes-ultimate' ),
				'desc'    => __( 'Icons for spoiler', 'shortcodes-ultimate' ),
			),
			'anchor' => array(
				'default' => '',
				'name'    => __( 'Anchor', 'shortcodes-ultimate' ),
				'desc'    => __( 'You can use unique anchor for this spoiler to access it with hash in page url. For example: type here <b%value>Hello</b> and then use url like http://example.com/page-url#Hello. This spoiler will be open and scrolled in', 'shortcodes-ultimate' ),
			),
			'class'  => array(
				'type'    => 'extra_css_class',
				'name'    => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc'    => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content'  => __( 'Hidden content', 'shortcodes-ultimate' ),
		'desc'     => __( 'Spoiler with hidden content', 'shortcodes-ultimate' ),
		'note'     => __( 'Did you know that you can wrap multiple spoilers with [accordion] shortcode to create accordion effect?', 'shortcodes-ultimate' ),
		'example'  => 'spoilers',
		'icon'     => 'list-ul',
		'image'    => su_get_plugin_url() . 'admin/images/shortcodes/spoiler.svg',
	)
);

function su_shortcode_spoiler( $atts = null, $content = null ) {
	$atts           = shortcode_atts(
		array(
			'title'  => __( 'Spoiler title', 'shortcodes-ultimate' ),
			'open'   => 'no',
			'style'  => 'default',
			'icon'   => 'plus',
			'anchor' => '',
			'class'  => '',
		),
		$atts,
		'spoiler'
	);
	$atts['style']  = str_replace( array( '1', '2' ), array( 'default', 'fancy' ), $atts['style'] );
	$atts['anchor'] = ( $atts['anchor'] ) ? ' data-anchor="' . str_replace( array( ' ', '#' ), '', sanitize_text_field( $atts['anchor'] ) ) . '"' : '';
	if ( 'yes' !== $atts['open'] ) {
		$atts['class'] .= ' su-spoiler-closed';
	}
	su_query_asset( 'css', 'su-icons' );
	su_query_asset( 'css', 'su-shortcodes' );
	su_query_asset( 'js', 'jquery' );
	su_query_asset( 'js', 'su-other-shortcodes' );
	do_action( 'su/shortcode/spoiler', $atts );
	return '<div class="su-spoiler su-spoiler-style-' . $atts['style'] . ' su-spoiler-icon-' . $atts['icon'] . su_get_css_class( $atts ) . '"' . $atts['anchor'] . '><div class="su-spoiler-title" tabindex="0" role="button"><span class="su-spoiler-icon"></span>' . su_do_attribute( $atts['title'] ) . '</div><div class="su-spoiler-content su-clearfix">' . su_do_nested_shortcodes( $content, 'spoiler' ) . '</div></div>';
}
