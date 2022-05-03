<?php

return [
	'api_version'     => 2,
	'render_callback' => function( $atts ) {
		$markup  = '<div class="openlab-block-openlab-support">';
		$markup .= '<h2 class="widget-title">OpenLab Support</h2>';
		$markup .= openlab_render_block( 'openlab-support', $atts );
		$markup .= '</div>';

		return $markup;
	},
];

