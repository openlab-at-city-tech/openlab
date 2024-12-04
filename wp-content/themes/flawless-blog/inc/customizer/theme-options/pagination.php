<?php
/**
 * Pagination setting
 */

// Pagination setting.
$wp_customize->add_section(
	'flawless_blog_pagination',
	array(
		'title' => esc_html__( 'Pagination', 'flawless-blog' ),
		'panel' => 'flawless_blog_theme_options_panel',
	)
);

// Pagination enable setting.
$wp_customize->add_setting(
	'flawless_blog_pagination_enable',
	array(
		'default'           => true,
		'sanitize_callback' => 'flawless_blog_sanitize_checkbox',
	)
);

$wp_customize->add_control(
	new Flawless_Blog_Toggle_Checkbox_Custom_control(
		$wp_customize,
		'flawless_blog_pagination_enable',
		array(
			'label'    => esc_html__( 'Enable Pagination.', 'flawless-blog' ),
			'settings' => 'flawless_blog_pagination_enable',
			'section'  => 'flawless_blog_pagination',
			'type'     => 'checkbox',
		)
	)
);

// Pagination - Pagination Style.
$wp_customize->add_setting(
	'flawless_blog_pagination_type',
	array(
		'default'           => 'numeric',
		'sanitize_callback' => 'flawless_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'flawless_blog_pagination_type',
	array(
		'label'           => esc_html__( 'Pagination Style', 'flawless-blog' ),
		'section'         => 'flawless_blog_pagination',
		'type'            => 'select',
		'choices'         => array(
			'default' => __( 'Default (Older/Newer)', 'flawless-blog' ),
			'numeric' => __( 'Numeric', 'flawless-blog' ),
		),
		'active_callback' => function( $control ) {
			return ( $control->manager->get_setting( 'flawless_blog_pagination_enable' )->value() );
		},
	)
);
