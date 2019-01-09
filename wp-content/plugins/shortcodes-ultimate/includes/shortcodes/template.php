<?php

su_add_shortcode( array(
		'id' => 'template',
		'callback' => 'su_shortcode_template',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/template.svg',
		'name' => __( 'Template', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'other',
		'atts' => array(
			'name' => array(
				'default' => '',
				'name' => __( 'Template name', 'shortcodes-ultimate' ),
				'desc' => sprintf( __( 'Use template file name (with optional .php extension). If you need to use templates from theme sub-folder, use relative path. Example values: %s, %s, %s', 'shortcodes-ultimate' ), '<b%value>page</b>', '<b%value>page.php</b>', '<b%value>includes/page.php</b>' )
			)
		),
		'desc' => __( 'Theme template', 'shortcodes-ultimate' ),
		'icon' => 'puzzle-piece',
	) );

function su_shortcode_template( $atts = null, $content = null ) {
	$atts = shortcode_atts( array(
			'name' => ''
		), $atts, 'template' );
	// Check template name
	if ( !$atts['name'] ) return sprintf( '<p class="su-error">Template: %s</p>', __( 'please specify template name', 'shortcodes-ultimate' ) );
	// Get template output
	ob_start();
	get_template_part( str_replace( '.php', '', $atts['name'] ) );
	$output = ob_get_contents();
	ob_end_clean();
	// Return result
	return $output;
}
