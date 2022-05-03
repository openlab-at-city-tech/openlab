<?php

/**
 * Gets a list of block widgets.
 */
function openlab_block_widgets() {
	$widgets = [
		'openlab_help'    => [
			'slug'  => 'openlab-help',
			'class' => 'OpenLab_Help_Widget',
		],
		'openlab_support' => [
			'slug'  => 'openlab-support',
			'class' => 'OpenLab_Support_Widget',
		],
	];

	return $widgets;
}

/**
 * Registers block widgets.
 */
function openlab_register_block_widgets() {
	$widgets = openlab_block_widgets();

	foreach ( $widgets as $widget_info ) {
		require_once __DIR__ . '/block-widgets/' . $widget_info['slug'] . '.php';
		register_widget( $widget_info['class'] );
	}
}
add_action( 'widgets_init', 'openlab_register_block_widgets' );
