<?php defined( 'ABSPATH' ) || exit;

return apply_filters(
	'su/config/groups',
	array(
		'all'     => __( 'All', 'shortcodes-ultimate' ),
		'content' => __( 'Content', 'shortcodes-ultimate' ),
		'box'     => __( 'Box', 'shortcodes-ultimate' ),
		'media'   => __( 'Embed', 'shortcodes-ultimate' ),
		'gallery' => __( 'Gallery', 'shortcodes-ultimate' ),
		'data'    => __( 'Data', 'shortcodes-ultimate' ),
		'other'   => __( 'Other', 'shortcodes-ultimate' ),
	)
);
