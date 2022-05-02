<?php

/**
 * Registers block widgets.
 */
function openlab_register_block_widgets() {
	$widgets = [
		'openlab-help'    => 'OpenLab_Help_Widget',
		'openlab-support' => 'OpenLab_Support_Widget',
	];

	foreach ( $widgets as $widget_slug => $widget_class ) {
		require_once __DIR__ . '/block-widgets/' . $widget_slug . '.php';
		register_widget( $widget_class );
	}
}
add_action( 'widgets_init', 'openlab_register_block_widgets' );
