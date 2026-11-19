<?php defined( 'ABSPATH' ) || exit;

return apply_filters(
	'su/config/popular_shortcodes',
	array(
		array(
			'id'          => 'posts_grid',
			'title'       => __( 'Posts Grid', 'shortcodes-ultimate' ),
			'description' => __( 'Create your own posts query and display it where you want', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/posts_grid.svg',
		),
		array(
			'id'          => 'accordion',
			'title'       => __( 'Accordion &amp; Spoiler', 'shortcodes-ultimate' ),
			'description' => __( 'Create a single toggle or an accordion with multiple items', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/accordion.svg',
		),
		array(
			'id'          => 'button',
			'title'       => __( 'Button', 'shortcodes-ultimate' ),
			'description' => __( 'Beautiful button with multiple styles and ton of options', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/button.svg',
		),
		array(
			'id'          => 'lightbox',
			'title'       => __( 'Lightbox', 'shortcodes-ultimate' ),
			'description' => __( 'A lightbox, that can display images and custom HTML', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/lightbox.svg',
		),
		array(
			'id'          => 'columns',
			'title'       => __( 'Columns', 'shortcodes-ultimate' ),
			'description' => __( 'Two shortcodes for creating flexible multi-column layouts', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/row.svg',
		),
		array(
			'id'          => 'image_carousel',
			'title'       => __( 'Image carousel', 'shortcodes-ultimate' ),
			'description' => __( 'A powerful shortcode for creating both sliders and carousels', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/image_carousel.svg',
		),
		array(
			'id'          => 'box',
			'title'       => __( 'Box', 'shortcodes-ultimate' ),
			'description' => __( 'Simple yet stylish colorful block with caption and content', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/box.svg',
		),
		array(
			'id'          => 'tabs',
			'title'       => __( 'Tabs', 'shortcodes-ultimate' ),
			'description' => __( 'Two shortcodes for breaking your content into tabs', 'shortcodes-ultimate' ),
			'icon'        => 'admin/images/shortcodes/svgs/tabs.svg',
		),
	)
);
