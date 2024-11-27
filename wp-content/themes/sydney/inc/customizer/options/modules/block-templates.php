<?php 
/**
 * Breadcrumb options
 */

$wp_customize->add_section(
	'sydney_block_templates',
	array(
		'title'         => esc_html__( 'Block Templates', 'sydney'),
		'priority'      => 79,
		'panel'         => 'sydney_panel_general',
	)
);

$wp_customize->add_setting(
	'enable_block_templates',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_block_templates',
		array(
			'label'         	=> esc_html__( 'Enable Block Templates', 'sydney' ),
			'section'       	=> 'sydney_block_templates',
		)
	)
);

$wp_customize->add_setting( 'block_templates_notice',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'esc_attr'
	)
);

$wp_customize->add_control( new Sydney_Text_Control( $wp_customize, 'block_templates_notice',
		array(
			'label' 		=> __( 'How to use block templates?', 'sydney' ),
			'description' 	=> 
				'<ol>'
				. '<li>' . sprintf( __( 'Go %s and customize the templates you want to use.', 'sydney' ), '<a target="_blank" href="' . admin_url( 'site-editor.php?path=%2Fwp_template_part%2Fall' ) . '">' . __( 'here', 'sydney' ) . '</a>' ) . '</li>'
				. '<li>' . __( 'Activate the block template you want to use below. Example: if you activate the header template, the theme\'s default header will be replaced with your header block template.', 'sydney' ) . '</li>'
				. '<li>' . __( 'Save the changes and refresh this page.', 'sydney' ) . '</li>'
				. '</ol>',
			'section' 		=> 'sydney_block_templates',
			'active_callback' 	=> 'sydney_block_templates_active_callback'
		)
	)
);

//enable header
$wp_customize->add_setting(
	'enable_header_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_header_block_template',
		array(
			'label'         	=> esc_html__( 'Enable Header Block Template', 'sydney' ),
			'section'       	=> 'sydney_block_templates',
			'separator'     	=> 'before',
			'active_callback' 	=> 'sydney_block_templates_active_callback'
		)
	)
);

//enable footer
$wp_customize->add_setting(
	'enable_footer_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_footer_block_template',
		array(
			'label'         => esc_html__( 'Enable Footer Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);

//enable single
$wp_customize->add_setting(
	'enable_single_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_single_block_template',
		array(
			'label'         => esc_html__( 'Enable Single Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);

//enable page
$wp_customize->add_setting(
	'enable_page_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_page_block_template',
		array(
			'label'         => esc_html__( 'Enable Page Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);

//enable archive
$wp_customize->add_setting(
	'enable_archive_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_archive_block_template',
		array(
			'label'         => esc_html__( 'Enable Archive Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);

//enable search
$wp_customize->add_setting(
	'enable_search_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_search_block_template',
		array(
			'label'         => esc_html__( 'Enable Search Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);

//enable 404
$wp_customize->add_setting(
	'enable_404_block_template',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_404_block_template',
		array(
			'label'         => esc_html__( 'Enable 404 Block Template', 'sydney' ),
			'section'       => 'sydney_block_templates',
			'active_callback' => 'sydney_block_templates_active_callback'
		)
	)
);