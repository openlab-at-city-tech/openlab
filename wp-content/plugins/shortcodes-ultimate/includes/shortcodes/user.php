<?php

su_add_shortcode(
	array(
		'id'       => 'user',
		'callback' => 'su_shortcode_user',
		'image'    => su_get_plugin_url() . 'admin/images/shortcodes/user.svg',
		'name'     => __( 'User data', 'shortcodes-ultimate' ),
		'type'     => 'single',
		'group'    => 'data',
		'atts'     => array(
			'field'   => array(
				'type'    => 'select',
				'values'  => array(
					'first_name'          => __( 'First name', 'shortcodes-ultimate' ),
					'last_name'           => __( 'Last name', 'shortcodes-ultimate' ),
					'nickname'            => __( 'Nickname', 'shortcodes-ultimate' ),
					'description'         => __( 'Description', 'shortcodes-ultimate' ),
					'locale'              => __( 'Locale', 'shortcodes-ultimate' ),
					'display_name'        => __( 'Display name', 'shortcodes-ultimate' ),
					'ID'                  => __( 'ID', 'shortcodes-ultimate' ),
					'user_login'          => __( 'Login', 'shortcodes-ultimate' ),
					'user_nicename'       => __( 'Nice name', 'shortcodes-ultimate' ),
					'user_email'          => __( 'Email', 'shortcodes-ultimate' ),
					'user_url'            => __( 'URL', 'shortcodes-ultimate' ),
					'user_registered'     => __( 'Registered', 'shortcodes-ultimate' ),
					'user_activation_key' => __( 'Activation key', 'shortcodes-ultimate' ),
					'user_status'         => __( 'Status', 'shortcodes-ultimate' ),
				),
				'default' => 'display_name',
				'name'    => __( 'Field', 'shortcodes-ultimate' ),
				'desc'    => __( 'User data field name. Custom meta field names are also allowed.', 'shortcodes-ultimate' ),
			),
			'default' => array(
				'default' => '',
				'name'    => __( 'Default', 'shortcodes-ultimate' ),
				'desc'    => __( 'This text will be shown if data is not found', 'shortcodes-ultimate' ),
			),
			'before'  => array(
				'default' => '',
				'name'    => __( 'Before', 'shortcodes-ultimate' ),
				'desc'    => __( 'This content will be shown before the value', 'shortcodes-ultimate' ),
			),
			'after'   => array(
				'default' => '',
				'name'    => __( 'After', 'shortcodes-ultimate' ),
				'desc'    => __( 'This content will be shown after the value', 'shortcodes-ultimate' ),
			),
			'user_id' => array(
				'default' => '',
				'name'    => __( 'User ID', 'shortcodes-ultimate' ),
				'desc'    => __( 'You can specify custom user ID. Leave this field empty to use an ID of the current user', 'shortcodes-ultimate' ),
			),
			'filter'  => array(
				'default' => '',
				'name'    => __( 'Filter', 'shortcodes-ultimate' ),
				'desc'    => __( 'You can apply custom filter to the retrieved value. Enter here function name. Your function must accept one argument and return modified value. Name of your function must include word <b>filter</b>. Example function: ', 'shortcodes-ultimate' ) . "<br /><pre><code style='display:block;padding:5px'>function my_custom_filter( \$value ) {\n\treturn 'Value is: ' . \$value;\n}</code></pre>",
			),
		),
		'desc'     => __( 'This shortcode can display a user data, like login or email, including meta fields', 'shortcodes-ultimate' ),
		'icon'     => 'info-circle',
	)
);

function su_shortcode_user( $atts = null, $content = null ) {

	$atts = su_parse_shortcode_atts( 'user', $atts );
	$data = '';

	if ( 'user_pass' === $atts['field'] ) {

		return su_error_message(
			'User',
			__( 'password field is not allowed', 'shortcodes-ultimate' )
		);

	}

	$atts['user_id'] = su_do_attribute( $atts['user_id'] );

	if ( ! $atts['user_id'] ) {
		$atts['user_id'] = get_current_user_id();
	}

	if ( su_is_positive_number( $atts['user_id'] ) ) {

		$user = get_user_by( 'id', $atts['user_id'] );

		if ( ! $user ) {

			return su_error_message(
				'User',
				__( 'user not found', 'shortcodes-ultimate' )
			);

		}

		$data = $user->get( $atts['field'] );

	}

	if ( ! is_string( $data ) || empty( $data ) ) {
		$data = su_do_attribute( $atts['default'] );
	}

	$data = su_safely_apply_user_filter( $atts['filter'], $data );

	return $data ? $atts['before'] . $data . $atts['after'] : '';

}
