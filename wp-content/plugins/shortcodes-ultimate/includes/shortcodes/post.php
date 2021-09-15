<?php

su_add_shortcode(
	array(
		'id'       => 'post',
		'type'     => 'single',
		'group'    => 'data',
		'callback' => 'su_shortcode_post',
		'icon'     => 'info-circle',
		'image'    => su_get_plugin_url() . 'admin/images/shortcodes/post.svg',
		'name'     => __( 'Post data', 'shortcodes-ultimate' ),
		'desc'     => __( 'The utility shortcode to display various post data, like post title, status or excerpt', 'shortcodes-ultimate' ),
		'atts'     => array(
			'field'     => array(
				'type'    => 'select',
				'values'  => array(
					'ID'                => __( 'Post ID', 'shortcodes-ultimate' ),
					'post_author'       => __( 'Post author', 'shortcodes-ultimate' ),
					'post_date'         => __( 'Post date', 'shortcodes-ultimate' ),
					'post_date_gmt'     => __( 'Post date', 'shortcodes-ultimate' ) . ' GMT',
					'post_content'      => __( 'Post content (Raw)', 'shortcodes-ultimate' ),
					'the_content'       => __( 'Post content (Filtered)', 'shortcodes-ultimate' ),
					'post_title'        => __( 'Post title', 'shortcodes-ultimate' ),
					'post_excerpt'      => __( 'Post excerpt', 'shortcodes-ultimate' ),
					'post_status'       => __( 'Post status', 'shortcodes-ultimate' ),
					'comment_status'    => __( 'Comment status', 'shortcodes-ultimate' ),
					'ping_status'       => __( 'Ping status', 'shortcodes-ultimate' ),
					'post_name'         => __( 'Post name', 'shortcodes-ultimate' ),
					'post_modified'     => __( 'Post modified', 'shortcodes-ultimate' ),
					'post_modified_gmt' => __( 'Post modified', 'shortcodes-ultimate' ) . ' GMT',
					'post_parent'       => __( 'Post parent', 'shortcodes-ultimate' ),
					'guid'              => __( 'GUID', 'shortcodes-ultimate' ),
					'menu_order'        => __( 'Menu order', 'shortcodes-ultimate' ),
					'post_type'         => __( 'Post type', 'shortcodes-ultimate' ),
					'post_mime_type'    => __( 'Post mime type', 'shortcodes-ultimate' ),
					'comment_count'     => __( 'Comment count', 'shortcodes-ultimate' ),
				),
				'default' => 'post_title',
				'name'    => __( 'Field', 'shortcodes-ultimate' ),
				'desc'    => __( 'Post data field name', 'shortcodes-ultimate' ),
			),
			'default'   => array(
				'default' => '',
				'name'    => __( 'Default', 'shortcodes-ultimate' ),
				'desc'    => __( 'This text will be shown if data is not found', 'shortcodes-ultimate' ),
			),
			'before'    => array(
				'default' => '',
				'name'    => __( 'Before', 'shortcodes-ultimate' ),
				'desc'    => __( 'This content will be shown before the value', 'shortcodes-ultimate' ),
			),
			'after'     => array(
				'default' => '',
				'name'    => __( 'After', 'shortcodes-ultimate' ),
				'desc'    => __( 'This content will be shown after the value', 'shortcodes-ultimate' ),
			),
			'post_id'   => array(
				'default' => '',
				'name'    => __( 'Post ID', 'shortcodes-ultimate' ),
				'desc'    => __( 'You can specify custom post ID. Post slug is also allowed. Leave this field empty to use ID of the current post. Current post ID may not work in Live Preview mode', 'shortcodes-ultimate' ),
			),
			'post_type' => array(
				'type'    => 'post_type',
				'default' => 'post',
				'name'    => __( 'Post type', 'shortcodes-ultimate' ),
				'desc'    => __( 'Post type of the post you want to display the data from', 'shortcodes-ultimate' ),
			),
			'filter'    => array(
				'default' => '',
				'name'    => __( 'Filter', 'shortcodes-ultimate' ),
				'desc'    => __( 'You can apply custom filter to the retrieved value. Enter here function name. Your function must accept one argument and return modified value. Name of your function must include word <b>filter</b>. Example function: ', 'shortcodes-ultimate' ) . "<br /><pre><code style='display:block;padding:5px'>function my_custom_filter( \$value ) {\n\treturn 'Value is: ' . \$value;\n}</code></pre>",
			),
		),
	)
);

function su_shortcode_post( $atts = null, $content = null ) {

	$atts = su_parse_shortcode_atts(
		'post',
		$atts,
		array( 'filter_content' => 'no' )
	);

	if ( ! $atts['post_id'] ) {
		$atts['post_id'] = get_the_ID();
	}

	if ( ! $atts['post_id'] ) {

		return su_error_message(
			'Post',
			__( 'invalid post ID', 'shortcodes-ultimate' )
		);

	}

	if ( 'the_content' === $atts['field'] ) {

		$atts['field']          = 'post_content';
		$atts['filter_content'] = 'yes';

	}

	$data = '';
	$post = su_is_positive_number( $atts['post_id'] )
		? get_post( $atts['post_id'] )
		: get_page_by_path( $atts['post_id'], OBJECT, $atts['post_type'] );

	if ( isset( $post->{$atts['field']} ) ) {
		$data = $post->{$atts['field']};
	}

	if ( 'yes' === $atts['filter_content'] ) {
		$data = su_filter_the_content( $data );
	}

	$data = su_safely_apply_user_filter( $atts['filter'], $data );

	if ( empty( $data ) ) {
		$data = $atts['default'];
	}

	return $data ? $atts['before'] . $data . $atts['after'] : '';

}
