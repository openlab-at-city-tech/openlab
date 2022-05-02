<?php

return [
	'api_version'     => 2,
	'render_callback' => function( $atts ) {
		$markup  = '<div class="openlab-block-openlab-help">';
		$markup .= '<h2 class="widget-title">OpenLab Help</h2>';
		$markup .= openlab_render_block( 'openlab-help', $atts );
		$markup .= '</div>';

		return $markup;
	},
];

