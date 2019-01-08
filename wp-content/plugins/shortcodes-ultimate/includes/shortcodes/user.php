<?php

su_add_shortcode( array(
		'id' => 'user',
		'callback' => 'su_shortcode_user',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/user.svg',
		'name' => __( 'User data', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'data',
		'atts' => array(
			'field' => array(
				'type' => 'select',
				'values' => array(
					'display_name'        => __( 'Display name', 'shortcodes-ultimate' ),
					'ID'                  => __( 'ID', 'shortcodes-ultimate' ),
					'user_login'          => __( 'Login', 'shortcodes-ultimate' ),
					'user_nicename'       => __( 'Nice name', 'shortcodes-ultimate' ),
					'user_email'          => __( 'Email', 'shortcodes-ultimate' ),
					'user_url'            => __( 'URL', 'shortcodes-ultimate' ),
					'user_registered'     => __( 'Registered', 'shortcodes-ultimate' ),
					'user_activation_key' => __( 'Activation key', 'shortcodes-ultimate' ),
					'user_status'         => __( 'Status', 'shortcodes-ultimate' )
				),
				'default' => 'display_name',
				'name' => __( 'Field', 'shortcodes-ultimate' ),
				'desc' => __( 'User data field name', 'shortcodes-ultimate' )
			),
			'default' => array(
				'default' => '',
				'name' => __( 'Default', 'shortcodes-ultimate' ),
				'desc' => __( 'This text will be shown if data is not found', 'shortcodes-ultimate' )
			),
			'before' => array(
				'default' => '',
				'name' => __( 'Before', 'shortcodes-ultimate' ),
				'desc' => __( 'This content will be shown before the value', 'shortcodes-ultimate' )
			),
			'after' => array(
				'default' => '',
				'name' => __( 'After', 'shortcodes-ultimate' ),
				'desc' => __( 'This content will be shown after the value', 'shortcodes-ultimate' )
			),
			'user_id' => array(
				'default' => '',
				'name' => __( 'User ID', 'shortcodes-ultimate' ),
				'desc' => __( 'You can specify custom user ID. Leave this field empty to use an ID of the current user', 'shortcodes-ultimate' )
			),
			'filter' => array(
				'default' => '',
				'name' => __( 'Filter', 'shortcodes-ultimate' ),
				'desc' => __( 'You can apply custom filter to the retrieved value. Enter here function name. Your function must accept one argument and return modified value. Name of your function must include word <b>filter</b>. Example function: ', 'shortcodes-ultimate' ) . "<br /><pre><code style='display:block;padding:5px'>function my_custom_filter( \$value ) {\n\treturn 'Value is: ' . \$value;\n}</code></pre>"
			)
		),
		'desc' => __( 'User data', 'shortcodes-ultimate' ),
		'icon' => 'info-circle',
	) );

function su_shortcode_user( $atts = null, $content = null ) {
	$atts = shortcode_atts( array(
			'field'   => 'display_name',
			'default' => '',
			'before'  => '',
			'after'   => '',
			'user_id' => '',
			'filter'  => ''
		), $atts, 'user' );
	// Check for password requests
	if ( $atts['field'] === 'user_pass' ) return sprintf( '<p class="su-error">User: %s</p>', __( 'password field is not allowed', 'shortcodes-ultimate' ) );
	// Define current user ID
	if ( !$atts['user_id'] ) $atts['user_id'] = get_current_user_id();
	// Check user ID
	if ( !is_numeric( $atts['user_id'] ) || $atts['user_id'] < 0 ) return sprintf( '<p class="su-error">User: %s</p>', __( 'user ID is incorrect', 'shortcodes-ultimate' ) );
	// Get user data
	$user = get_user_by( 'id', $atts['user_id'] );
	// Get user data if user was found
	$user = ( $user && isset( $user->data->{$atts['field']} ) ) ? $user->data->{$atts['field']} : $atts['default'];
	// Apply cutom filter
	if (
		$atts['filter'] &&
		su_is_filter_safe( $atts['filter'] ) &&
		function_exists( $atts['filter'] )
	) {
		$user = call_user_func( $atts['filter'], $user );
	}
	// Return result
	return ( $user ) ? $atts['before'] . $user . $atts['after'] : '';
}
