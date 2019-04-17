<?php

//delete_option('johannes_settings');

/**
 * Load Kirki Framework
 */

if ( ! class_exists( 'Kirki' ) ) {
	return;
}

add_filter( 'kirki_config', 'johannes_modify_kirki_config' );

function johannes_modify_kirki_config( $config ) {
	return wp_parse_args( array(
			'disable_loader' => true
		), $config );
}

/**
 * Kirki params
 */

Kirki::add_config( 'johannes', array(
		'capability'    => 'edit_theme_options',
		'option_type'   => 'option',
		'option_name'   => 'johannes_settings',
	) );

/* Root */

Kirki::add_panel( 'johannes_panel', array(
		'priority'    => 1,
		'title'       => esc_html__( 'Theme Options', 'johannes' )
	) );


/* Header */

Kirki::add_panel( 'johannes_panel_header', array(
		'priority'    => 10,
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Header', 'johannes' ),
	) );



/* Header / General */

Kirki::add_section( 'johannes_header_general', array(
		'panel'          => 'johannes_panel_header',
		'title'          => esc_attr__( 'General', 'johannes' ),
	) );



Kirki::add_field( 'johannes', array(
		'settings'    => 'header_layout',
		'section'     => 'johannes_header_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_layout' ),
		'choices'     => johannes_get_header_layouts(),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_height',
		'section'     => 'johannes_header_general',
		'type'        => 'slider',
		'label'       => esc_html__( 'Header height', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_height' ),
		'choices'     => array(
			'min'  => '40',
			'max'  => '300',
			'step' => '1',
		),
		'transport'   => 'postMessage'
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_orientation',
		'section'     => 'johannes_header_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Elements orientation', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_orientation' ),
		'choices'  => array(
			'content' => array( 'alt' => esc_html__( 'Site content', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_orientation_content.svg' )  ),
			'window'    => array( 'alt' => esc_html__( 'Browser (screen)', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_orientation_window.svg' ) ),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_bottom_style',
		'section'     => 'johannes_header_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Header bottom bar style', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_bottom_style' ),
		'choices'  => array(
			'boxed' => array( 'alt' => esc_html__( 'Boxed', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_boxed.svg' )  ),
			'unboxed'    => array( 'alt' => esc_html__( 'Unboxed', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_unboxed.svg' ) ),
		),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '5', '6', '7', '8', '9', '10' ),
			),
			array(
				'setting'  => 'header_orientation',
				'operator' => '==',
				'value'    => 'content',
			),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'header_cover_indent',
		'section'     => 'johannes_header_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Indent cover into header', 'johannes' ),
		'description'       => esc_html__( 'If the current page has a cover area, it will be indented into header', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_cover_indent' ),
		'choices'  => array(
			'1' => array( 'alt' => esc_html__( 'On', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/cover_indent_on.svg' )  ),
			'0'    => array( 'alt' => esc_html__( 'Off', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/cover_indent_off.svg' ) ),
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_main_nav',
		'section'     => 'johannes_header_general',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable main navigation', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_main_nav' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_site_desc',
		'section'     => 'johannes_header_general',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable site desciprition', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_site_desc' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_actions',
		'section'     => 'johannes_header_general',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Enable special elements in header', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_actions' ),
		'choices'     => johannes_get_header_main_area_actions(),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '1', '2', '3', '5', '7', '8', '9', '10' ),
			),
		),

	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_actions_l',
		'section'     => 'johannes_header_general',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Enable special elements left', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_actions_l' ),
		'choices'     => johannes_get_header_main_area_actions(),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '4', '6', '9' ),
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_actions_r',
		'section'     => 'johannes_header_general',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Enable special elements right', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_actions_r' ),
		'choices'     => johannes_get_header_main_area_actions(),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '4', '6', '9' ),
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'logo',
		'section'     => 'johannes_header_general',
		'type'     => 'image',
		'label'  => esc_html__( 'Logo', 'johannes' ),
		'description'   => esc_html__( 'This is your default logo image. If it is not uploaded, theme will display the website label instead.', 'johannes' ),
		'default'  => johannes_get_default_option( 'logo' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'logo_retina',
		'section'     => 'johannes_header_general',
		'type'     => 'image',
		'label'    => esc_html__( 'Retina logo (2x)', 'johannes' ),
		'description' => esc_html__( 'Optionally upload another logo for devices with retina displays. It should be double the size of your standard logo', 'johannes' ),
		'default'  => johannes_get_default_option( 'logo_retina' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'logo_mini',
		'section'     => 'johannes_header_general',
		'type'     => 'image',
		'label'    => esc_html__( 'Mobile logo', 'johannes' ),
		'description' => esc_html__( 'Optionally upload another logo which may be used as mobile/tablet logo', 'johannes' ),
		'default'  => johannes_get_default_option( 'logo_mini' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'logo_mini_retina',
		'section'     => 'johannes_header_general',
		'type'     => 'image',
		'label'    => esc_html__( 'Mobile retina logo (2x)', 'johannes' ),
		'description' => esc_html__( 'Upload double sized mobile logo for devices with retina displays', 'johannes' ),
		'default'  => johannes_get_default_option( 'logo_mini_retina' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_multicolor',
		'section'     => 'johannes_header_general',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable multi-color header style', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_multicolor' ),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '1', '2', '3' ),
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_middle_bg',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Header background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_middle_bg' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_middle_txt',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Header text color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_middle_txt' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_middle_acc',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Header accent color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_middle_acc' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_middle_bg_multi',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Header alternate background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_middle_bg_multi' ),
		'required'    => array(
			array(
				'setting'  => 'header_multicolor',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_bottom_bg',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Bottom bar background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_bottom_bg' ),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '5', '6', '7', '8', '9', '10' )
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_bottom_txt',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Bottom bar text color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_bottom_txt' ),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '5', '6', '7', '8', '9', '10' )
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_bottom_acc',
		'section'     => 'johannes_header_general',
		'type'     => 'color',
		'label'    => esc_html__( 'Bottom bar accent color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_bottom_acc' ),
		'required'    => array(
			array(
				'setting'  => 'header_layout',
				'operator' => 'in',
				'value'    => array( '5', '6', '7', '8', '9', '10' )
			),
		),
	) );



/* Header / Top */

Kirki::add_section( 'johannes_header_top', array(
		'title'          => esc_attr__( 'Top Bar', 'johannes' ),
		'panel'          => 'johannes_panel_header'
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'header_top',
		'section'     => 'johannes_header_top',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable top bar', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_top' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_top_l',
		'section'     => 'johannes_header_top',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Top bar left slot', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_top_l' ),
		'choices'     => johannes_get_header_top_elements(),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_top_c',
		'section'     => 'johannes_header_top',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Top bar center slot', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_top_c' ),
		'choices'     => johannes_get_header_top_elements(),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_top_r',
		'section'     => 'johannes_header_top',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Top bar right slot', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_top_r' ),
		'choices'     => johannes_get_header_top_elements(),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_top_bg',
		'section'     => 'johannes_header_top',
		'type'     => 'color',
		'label'    => esc_html__( 'Top bar background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_top_bg' ),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_top_txt',
		'section'     => 'johannes_header_top',
		'type'     => 'color',
		'label'    => esc_html__( 'Top bar text color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_top_txt' ),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_header_top_acc',
		'section'     => 'johannes_header_top',
		'type'     => 'color',
		'label'    => esc_html__( 'Top bar accent color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_header_top_acc' ),
		'required'    => array(
			array(
				'setting'  => 'header_top',
				'operator' => '==',
				'value'    => true
			),
		),
	) );


/* Header / Sticky */

Kirki::add_section( 'johannes_header_sticky', array(
		'title'          => esc_attr__( 'Sticky Header', 'johannes' ),
		'panel'          => 'johannes_panel_header'
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable sticky header', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_offset',
		'type'        => 'number',
		'label'       => esc_html__( 'Sticky header offset', 'johannes' ),
		'description' => esc_html__( 'Specify after how many px of scrolling the sticky header appears', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky_offset' ),
		'choices'     => array(
			'min'  => '50',
			'max'  => '1000',
			'step' => '50',
		),
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_up',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Smart sticky', 'johannes' ),
		'description' => esc_html__( 'Sticky header appears only if you scroll up', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky_up' ),
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_logo',
		'type'        => 'radio',
		'label'       => esc_html__( 'Sticky header logo', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky_logo' ),
		'choices' => array(
			'regular' => esc_html__( 'Regular logo', 'johannes' ),
			'mini'      => esc_html__( 'Mini (mobile) logo', 'johannes' )
		),
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_height',
		'type'        => 'slider',
		'label'       => esc_html__( 'Sticky header height', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky_height' ),
		'choices'     => array(
			'min'  => '40',
			'max'  => '200',
			'step' => '1',
		),
		'transport'   => 'postMessage',
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_contextual',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable contextual sticky bar on single posts', 'johannes' ),
		'section'     => 'johannes_header_sticky',
		'default'     => johannes_get_default_option( 'header_sticky_contextual' ),
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'header_sticky_layout',
		'section'     => 'johannes_header_sticky',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_sticky_layout' ),
		'choices'     => johannes_get_header_layouts( array('5','6','7','8','9','10') ),
		'required'    => array(
			array(
				'setting'  => 'header_sticky',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

/* Header / Misc */

Kirki::add_section( 'johannes_header_misc', array(
		'title'          => esc_attr__( 'Misc.', 'johannes' ),
		'panel'          => 'johannes_panel_header'
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'mega_menu',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable mega menu functionality', 'johannes' ),
		'section'     => 'johannes_header_misc',
		'default'     => johannes_get_default_option( 'mega_menu' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'mega_menu_ppp',
		'type'        => 'number',
		'label'       => esc_html__( 'Mega menu posts (in category menu item) limit', 'johannes' ),
		'section'     => 'johannes_header_misc',
		'default'     => johannes_get_default_option( 'mega_menu_ppp' ),
		'choices'     => array(
			'min'  => '3',
			'max'  => '10',
			'step' => '1',
		),
		'required'    => array(
			array(
				'setting'  => 'mega_menu',
				'operator' => '==',
				'value'    => true
			),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'header_labels',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display labels for special elements', 'johannes' ),
		'section'     => 'johannes_header_misc',
		'default'     => johannes_get_default_option( 'header_labels' ),
	) );


/* Header / Misc */

Kirki::add_section( 'johannes_header_responsive', array(
		'title'          => esc_attr__( 'Responsive/mobile', 'johannes' ),
		'panel'          => 'johannes_panel_header'
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'header_actions_responsive',
		'section'     => 'johannes_header_responsive',
		'type'        => 'sortable',
		'label'    => esc_html__( 'Enable elements to add to your mobile/resposnive menu', 'johannes' ),
		'default'     => johannes_get_default_option( 'header_actions_responsive' ),
		'choices'     => johannes_get_header_top_elements( array( 'search-modal', 'social-modal', 'date' ) ),
	) );

/* Content */
Kirki::add_section( 'johannes_content', array(
		'priority'    => 20,
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Content Styling', 'johannes' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_bg',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_bg' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_txt',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Text color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_txt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_h',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Heading/title color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_h' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_acc',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Accent color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_acc' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_meta',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Meta color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_meta' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_bg_alt_1',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Alternative background 1 color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_bg_alt_1' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_bg_alt_2',
		'section'     => 'johannes_content',
		'type'     => 'color',
		'label'    => esc_html__( 'Alternative background 2 color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_bg_alt_2' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'overlays',
		'section'     => 'johannes_content',
		'type'        => 'radio-buttonset',
		'label'       => esc_html__( 'Image overlays', 'johannes' ),
		'description'       => esc_html__( 'Use this option to control the overlay opacity for covers and specific layouts with text over images', 'johannes' ),
		'default'     => johannes_get_default_option( 'overlays' ),
		'choices'     => array(
			'dark'   => esc_html__( 'Dark', 'johannes' ),
			'soft' => esc_html__( 'Soft', 'johannes' ),
			'none'  => esc_html__( 'None', 'johannes' ),
		),
	) );


/* Sidebar */
Kirki::add_section( 'johannes_sidebar', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Sidebar & Widgets', 'johannes' ),
		'priority'    => 30,
	) );

Kirki::add_field( 'johannes', array(
		'settings'     => 'sidebars',
		'section'     => 'johannes_sidebar',
		'type'        => 'repeater',
		'label'       => esc_html__( 'Sidebars', 'johannes' ),
		'description' => wp_kses_post( sprintf( __( 'Use this option to create additional sidebars for your website. Afterwards, you can manage sidebars content in the <a href="%s">Apperance -> Widgets</a> settings.', 'johannes' ), admin_url( 'widgets.php' ) ) ),
		'row_label' => array(
			'type' => 'text',
			'value' => esc_html__( 'Custom sidebar', 'johannes' ),
		),

		'button_label' => esc_html__( 'Add new', 'johannes' ),

		'default'      => johannes_get_default_option( 'sidebars' ),
		'fields' => array(
			'name' => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Sidebar name', 'johannes' ),
				'default'     => '',
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'widget_bg',
		'section'     => 'johannes_sidebar',
		'type'        => 'radio',
		'label'    => esc_html__( 'Widget default background color', 'johannes' ),
		'default'     => johannes_get_default_option( 'widget_bg' ),
		'choices'     =>  johannes_get_background_opts( false )
	) );


/* Footer */

Kirki::add_section( 'johannes_footer', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Footer', 'johannes' ),
		'priority'    => 40,
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_instagram',
		'section'     => 'johannes_footer',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display instagram area', 'johannes' ),
		'default'     => johannes_get_default_option( 'footer_instagram' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_instagram_front',
		'section'     => 'johannes_footer',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display instagram area on front (home) page only', 'johannes' ),
		'default'     => johannes_get_default_option( 'footer_instagram_front' ),
		'required'    => array(
			array(
				'setting'  => 'footer_instagram',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_instagram_username',
		'section'     => 'johannes_footer',
		'type'        => 'text',
		'label'       => esc_html__( 'Instagram username or hashtag', 'johannes' ),
		'description'       => esc_html__( 'Example 1: @natgeo Example 2: #flowers', 'johannes' ),
		'default'     => johannes_get_default_option( 'footer_instagram_username' ),
		'required'    => array(
			array(
				'setting'  => 'footer_instagram',
				'operator' => '==',
				'value'    => true
			),
		),
	) );



Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_widgets',
		'section'     => 'johannes_footer',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display footer widgets', 'johannes' ),
		'default'     => johannes_get_default_option( 'footer_widgets' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_widgets_layout',
		'section'     => 'johannes_footer',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Footer widgets layout', 'johannes' ),
		'desc'     => wp_kses_post( sprintf( __( 'Note: Each column represents one Footer Sidebar in <a href="%s">Apperance -> Widgets</a> settings.', 'johannes' ), admin_url( 'widgets.php' ) ) ),
		'default'     => johannes_get_default_option( 'footer_widgets_layout' ),
		'choices'     => johannes_get_footer_layouts(),
		'required'    => array(
			array(
				'setting'  => 'footer_widgets',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'footer_copyright',
		'section'     => 'johannes_footer',
		'type'        => 'textarea',
		'label'       => esc_html__( 'Footer copyright text', 'johannes' ),
		'default'     => johannes_get_default_option( 'footer_copyright' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_footer_bg',
		'section'     => 'johannes_footer',
		'type'     => 'color',
		'label'    => esc_html__( 'Background color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_footer_bg' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_footer_txt',
		'section'     => 'johannes_footer',
		'type'     => 'color',
		'label'    => esc_html__( 'Text color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_footer_txt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_footer_acc',
		'section'     => 'johannes_footer',
		'type'     => 'color',
		'label'    => esc_html__( 'Accent color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_footer_acc' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'color_footer_meta',
		'section'     => 'johannes_footer',
		'type'     => 'color',
		'label'    => esc_html__( 'Meta color', 'johannes' ),
		'default'  => johannes_get_default_option( 'color_footer_meta' ),
	) );


/* Layouts */


Kirki::add_panel( 'johannes_panel_layouts', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Post Layouts', 'johannes' ),
		'priority'    => 50,
	) );

/* Layout A */
Kirki::add_section( 'johannes_layout_a', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout A', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_a.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_cat',
		'section'     => 'johannes_layout_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_format',
		'section'     => 'johannes_layout_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_meta',
		'section'     => 'johannes_layout_a',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_excerpt',
		'section'     => 'johannes_layout_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_excerpt_type',
		'section'     => 'johannes_layout_a',
		'type'        => 'radio',
		'label'       => esc_html__( 'Excerpt type', 'johannes' ),
		'choices'     => array(
			'auto' => esc_html__( 'Automatic excerpt (with characters limit)', 'johannes' ),
			'manual' => esc_html__( 'Full content (manually split with read-more tag)', 'johannes' ),
		),
		'default'     => johannes_get_default_option( 'layout_a_excerpt_type' ),
		'required'    => array(
			array(
				'setting'  => 'layout_a_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_excerpt_limit',
		'section'     => 'johannes_layout_a',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_a_excerpt',
				'operator' => '==',
				'value'    => true
			),
			array(
				'setting'  => 'layout_a_excerpt_type',
				'operator' => '==',
				'value'    => 'auto'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_width',
		'section'     => 'johannes_layout_a',
		'type'        => 'radio-buttonset',
		'label'       => esc_html__( 'Content (text) width', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_width' ),
		'choices'     => array(
			'6'   => esc_html__( 'Narrow', 'johannes' ),
			'7' => esc_html__( 'Medium', 'johannes' ),
			'8'  => esc_html__( 'Wide', 'johannes' ),
		),
		'required'    => array(
			array(
				'setting'  => 'layout_a_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_rm',
		'section'     => 'johannes_layout_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "read more" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_rm' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_img_ratio',
		'section'     => 'johannes_layout_a',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_a_img_custom',
		'section'     => 'johannes_layout_a',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_a_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_a_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );




/* Layout B */
Kirki::add_section( 'johannes_layout_b', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout B', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_b.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_cat',
		'section'     => 'johannes_layout_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_format',
		'section'     => 'johannes_layout_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_meta',
		'section'     => 'johannes_layout_b',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_excerpt',
		'section'     => 'johannes_layout_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_excerpt_type',
		'section'     => 'johannes_layout_b',
		'type'        => 'radio',
		'label'       => esc_html__( 'Excerpt type', 'johannes' ),
		'choices'     => array(
			'auto' => esc_html__( 'Automatic excerpt (with characters limit)', 'johannes' ),
			'manual' => esc_html__( 'Full content (manually split with read-more tag)', 'johannes' ),
		),
		'default'     => johannes_get_default_option( 'layout_b_excerpt_type' ),
		'required'    => array(
			array(
				'setting'  => 'layout_b_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_excerpt_limit',
		'section'     => 'johannes_layout_b',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_b_excerpt',
				'operator' => '==',
				'value'    => true
			),
			array(
				'setting'  => 'layout_b_excerpt_type',
				'operator' => '==',
				'value'    => 'auto'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_width',
		'section'     => 'johannes_layout_b',
		'type'        => 'radio-buttonset',
		'label'       => esc_html__( 'Content (text) width', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_width' ),
		'choices'     => array(
			'9'   => esc_html__( 'Narrow', 'johannes' ),
			'10' => esc_html__( 'Medium', 'johannes' ),
			'12'  => esc_html__( 'Wide', 'johannes' ),
		),
		'required'    => array(
			array(
				'setting'  => 'layout_b_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_rm',
		'section'     => 'johannes_layout_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "read more" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_rm' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_img_ratio',
		'section'     => 'johannes_layout_b',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_b_img_custom',
		'section'     => 'johannes_layout_b',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_b_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_b_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );


/* Layout C */
Kirki::add_section( 'johannes_layout_c', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout C', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_c.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_cat',
		'section'     => 'johannes_layout_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_format',
		'section'     => 'johannes_layout_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_meta',
		'section'     => 'johannes_layout_c',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_excerpt',
		'section'     => 'johannes_layout_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_excerpt_limit',
		'section'     => 'johannes_layout_c',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_c_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_rm',
		'section'     => 'johannes_layout_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "read more" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_rm' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_img_ratio',
		'section'     => 'johannes_layout_c',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_c_img_custom',
		'section'     => 'johannes_layout_c',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_c_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_c_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );

/* Layout D */
Kirki::add_section( 'johannes_layout_d', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout D', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_d.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_cat',
		'section'     => 'johannes_layout_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_format',
		'section'     => 'johannes_layout_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_meta',
		'section'     => 'johannes_layout_d',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_excerpt',
		'section'     => 'johannes_layout_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_excerpt_limit',
		'section'     => 'johannes_layout_d',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_d_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_rm',
		'section'     => 'johannes_layout_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "read more" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_rm' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_img_ratio',
		'section'     => 'johannes_layout_d',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_d_img_custom',
		'section'     => 'johannes_layout_d',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_d_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_d_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );

/* Layout E */
Kirki::add_section( 'johannes_layout_e', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout E', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_e.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_cat',
		'section'     => 'johannes_layout_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_format',
		'section'     => 'johannes_layout_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_meta',
		'section'     => 'johannes_layout_e',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_excerpt',
		'section'     => 'johannes_layout_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_excerpt_limit',
		'section'     => 'johannes_layout_e',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_e_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_rm',
		'section'     => 'johannes_layout_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "read more" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_rm' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_img_ratio',
		'section'     => 'johannes_layout_e',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_e_img_custom',
		'section'     => 'johannes_layout_e',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_e_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_e_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );


/* Layout F */
Kirki::add_section( 'johannes_layout_f', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Layout F', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_f.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_cat',
		'section'     => 'johannes_layout_f',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_format',
		'section'     => 'johannes_layout_f',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_meta',
		'section'     => 'johannes_layout_f',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_excerpt',
		'section'     => 'johannes_layout_f',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post text excerpt', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_excerpt' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_excerpt_limit',
		'section'     => 'johannes_layout_f',
		'type'        => 'number',
		'label'       => esc_html__( 'Excerpt characters limit', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_excerpt_limit' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'layout_f_excerpt',
				'operator' => '==',
				'value'    => true
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_img_ratio',
		'section'     => 'johannes_layout_f',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_f_img_custom',
		'section'     => 'johannes_layout_f',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_f_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_f_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );

/* Layout Featured A */
Kirki::add_section( 'johannes_layout_fa_a', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Featured Layout A', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_fa_a.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_a_cat',
		'section'     => 'johannes_layout_fa_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_a_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_a_format',
		'section'     => 'johannes_layout_fa_a',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_a_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_a_meta',
		'section'     => 'johannes_layout_fa_a',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_a_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_a_height',
		'section'     => 'johannes_layout_fa_a',
		'type'        => 'number',
		'label'       => esc_html__( 'Image height', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_a_height' ),
		'choices'     => array(
			'step' => '1'
		),

	) );



/* Layout Featured B */
Kirki::add_section( 'johannes_layout_fa_b', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Featured Layout B', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_fa_b.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_b_cat',
		'section'     => 'johannes_layout_fa_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_b_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_b_format',
		'section'     => 'johannes_layout_fa_b',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_b_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_b_meta',
		'section'     => 'johannes_layout_fa_b',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_b_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_b_img_ratio',
		'section'     => 'johannes_layout_fa_b',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_b_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_b_img_custom',
		'section'     => 'johannes_layout_fa_b',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_b_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_fa_b_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );

/* Layout Featured C */
Kirki::add_section( 'johannes_layout_fa_c', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Featured Layout C', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_fa_c.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_c_cat',
		'section'     => 'johannes_layout_fa_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_c_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_c_format',
		'section'     => 'johannes_layout_fa_c',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_c_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_c_meta',
		'section'     => 'johannes_layout_fa_c',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_c_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_c_img_ratio',
		'section'     => 'johannes_layout_fa_c',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_c_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_c_img_custom',
		'section'     => 'johannes_layout_fa_c',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_c_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_fa_c_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );


/* Layout Featured D */
Kirki::add_section( 'johannes_layout_fa_d', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Featured Layout D', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_fa_d.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_d_cat',
		'section'     => 'johannes_layout_fa_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_d_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_d_format',
		'section'     => 'johannes_layout_fa_d',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_d_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_d_meta',
		'section'     => 'johannes_layout_fa_d',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_d_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_d_img_ratio',
		'section'     => 'johannes_layout_fa_d',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_d_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_d_img_custom',
		'section'     => 'johannes_layout_fa_d',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_d_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_fa_d_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );


/* Layout Featured E */
Kirki::add_section( 'johannes_layout_fa_e', array(
		'panel'          => 'johannes_panel_layouts',
		'title'          => esc_attr__( 'Featured Layout E', 'johannes' ),
		'description' => wp_kses_post( '<img src="'.get_parent_theme_file_uri( '/assets/img/admin/layout_fa_e.svg' ).'"/>' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_e_cat',
		'section'     => 'johannes_layout_fa_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_e_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_e_format',
		'section'     => 'johannes_layout_fa_e',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display post format icon', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_e_format' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_e_meta',
		'section'     => 'johannes_layout_fa_e',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_e_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_e_img_ratio',
		'section'     => 'johannes_layout_fa_e',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_e_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'layout_fa_e_img_custom',
		'section'     => 'johannes_layout_fa_e',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'layout_fa_e_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'layout_fa_e_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
		),
	) );


/* Front page */

Kirki::add_panel( 'johannes_panel_front', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Front Page', 'johannes' ),
		'priority'    => 60,
	) );


/* General */
Kirki::add_section( 'johannes_front_general', array(
		'panel'          => 'johannes_panel_front',
		'title'          => esc_attr__( 'General', 'johannes' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_template',
		'section'     => 'johannes_front_general',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Use theme\'s built-in front page', 'johannes' ),
		'description'       => esc_html__( 'Disable this option if you want front page to display "Your latest posts" or "A static page" as specified in Settings -> Reading.', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_template' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_sections',
		'section'     => 'johannes_front_general',
		'type'        => 'sortable',
		'label'          => esc_html__( 'Sections', 'johannes' ),
		'description'       => esc_html__( 'Select (and re-order) front page sections that you want to display', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_sections' ),
		'choices' =>  array(
			'welcome' => esc_html__( 'Welcome area', 'johannes' ),
			'featured' => esc_html__( 'Featured posts', 'johannes' ),
			'classic' => esc_html__( 'Latest posts', 'johannes' )
		),
		'required'    => array(
			array(
				'setting'  => 'front_page_template',
				'operator' => '==',
				'value'    => true
			) )
	) );


/* Welcome area */

Kirki::add_section( 'johannes_front_welcome', array(
		'panel'          => 'johannes_panel_front',
		'title'          => esc_attr__( 'Welcome Area', 'johannes' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_wa_display_title',
		'section'     => 'johannes_front_welcome',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Display section title', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_wa_display_title' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_wa_title',
		'section'     => 'johannes_front_featured',
		'type'     => 'text',
		'label'  => esc_html__( 'Title', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_wa_title' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_display_title',
				'operator' => '==',
				'value'    => true
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_wa_layout',
		'section'     => 'johannes_front_welcome',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_wa_layout' ),
		'choices'     => johannes_get_welcome_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_wa_img',
		'section'     => 'johannes_front_welcome',
		'type'     => 'image',
		'label'  => esc_html__( 'Image', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_wa_img' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_1_img_ratio',
		'section'     => 'johannes_front_welcome',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 1', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_1_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '1'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_1_img_custom',
		'section'     => 'johannes_front_welcome',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_1_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'wa_layout_1_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '1'
			),

		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_2_img_ratio',
		'section'     => 'johannes_front_welcome',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 2', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_2_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_2_img_custom',
		'section'     => 'johannes_front_welcome',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_2_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'wa_layout_2_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_3_height',
		'section'     => 'johannes_front_welcome',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 3', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_3_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '3'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'wa_layout_4_height',
		'section'     => 'johannes_front_welcome',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 4', 'johannes' ),
		'default'     => johannes_get_default_option( 'wa_layout_4_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_layout',
				'operator' => '==',
				'value'    => '4'
			),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_wa_punchline',
		'section'     => 'johannes_front_welcome',
		'type'     => 'text',
		'label'  => esc_html__( 'Punchline', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_wa_punchline' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_wa_text',
		'section'     => 'johannes_front_welcome',
		'type'     => 'editor',
		'label'  => esc_html__( 'Intro text', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_wa_text' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_wa_cta',
		'section'     => 'johannes_front_welcome',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Display button', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_wa_cta' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_wa_cta_label',
		'section'     => 'johannes_front_welcome',
		'type'     => 'text',
		'label'  => esc_html__( 'Button label', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_wa_cta_label' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_cta',
				'operator' => '==',
				'value'    => true
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_wa_cta_url',
		'section'     => 'johannes_front_welcome',
		'type'     => 'link',
		'label'  => esc_html__( 'Button link (URL)', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_wa_cta_url' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_wa_cta',
				'operator' => '==',
				'value'    => true
			) )
	) );


/* Featured area */

Kirki::add_section( 'johannes_front_featured', array(
		'panel'          => 'johannes_panel_front',
		'title'          => esc_attr__( 'Featured Posts', 'johannes' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_fa_display_title',
		'section'     => 'johannes_front_featured',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Display section title', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_fa_display_title' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_featured_title',
		'section'     => 'johannes_front_featured',
		'type'     => 'text',
		'label'  => esc_html__( 'Title', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_featured_title' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_fa_display_title',
				'operator' => '==',
				'value'    => true
			) )
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_fa_loop',
		'section'     => 'johannes_front_featured',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_fa_loop' ),
		'choices'     => johannes_get_featured_layouts()
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_fa_ppp',
		'section'     => 'johannes_front_featured',
		'type'     => 'number',
		'label'  => esc_html__( 'Number of posts', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_fa_ppp' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_fa_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_featured_layouts( array( 'slider' => true ) ) ) )
			) )
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_fa_orderby',
		'section'     => 'johannes_front_featured',
		'type'        => 'radio',
		'label'       => esc_html__( 'Order posts by', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_fa_orderby' ),
		'choices'     => johannes_get_post_order_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_fa_cat',
		'section'     => 'johannes_front_featured',
		'type'        => 'select',
		'multiple'    => 10,
		'label'       => esc_html__( 'From category', 'johannes' ),
		'description'       => esc_html__( 'Select one or more categories to pull the posts from, or leave empty for all categories', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_fa_cat' ),
		'choices'     => Kirki_Helper::get_terms( 'category' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_fa_tag',
		'section'     => 'johannes_front_featured',
		'type'        => 'select',
		'multiple'    => 10,
		'label'       => esc_html__( 'Tagged with', 'johannes' ),
		'description'       => esc_html__( 'Select one or more tags to pull the posts from, or leave empty for all tags', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_fa_tag' ),
		'choices'     => Kirki_Helper::get_terms( 'post_tag' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_fa_unique',
		'section'     => 'johannes_front_featured',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Make featured posts unique', 'johannes' ),
		'description'       => esc_html__( 'If you check this option, featured posts will be automatically excluded from "latest" posts area', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_fa_unique' )
	) );


/* Classic (latest) */

Kirki::add_section( 'johannes_front_classic', array(
		'panel'          => 'johannes_panel_front',
		'title'          => esc_attr__( 'Latest Posts', 'johannes' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_classic_display_title',
		'section'     => 'johannes_front_classic',
		'type'     => 'toggle',
		'label'  => esc_html__( 'Display section title', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_classic_display_title' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'tr_front_page_classic_title',
		'section'     => 'johannes_front_classic',
		'type'     => 'text',
		'label'  => esc_html__( 'Title', 'johannes' ),
		'default'  => johannes_get_default_option( 'tr_front_page_classic_title' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_classic_display_title',
				'operator' => '==',
				'value'    => true
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_loop',
		'section'     => 'johannes_front_classic',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_loop' ),
		'choices'     => johannes_get_post_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_sidebar_position',
		'section'     => 'johannes_front_classic',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Sidebar position', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_sidebar_position' ),
		'choices'     => johannes_get_sidebar_layouts(),
		'required'    => array(
			array(
				'setting'  => 'front_page_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_sidebar_standard',
		'section'     => 'johannes_front_classic',
		'type'        => 'select',
		'label'       => esc_html__( 'Standard sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_sidebar_standard' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'front_page_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_sidebar_sticky',
		'section'     => 'johannes_front_classic',
		'type'        => 'select',
		'label'       => esc_html__( 'Sticky sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_sidebar_sticky' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'front_page_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_ppp',
		'section'     => 'johannes_front_classic',
		'type'        => 'radio',
		'label'       => esc_html__( 'Number of posts per page', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_ppp' ),
		'choices'     => array(
			'inherit' => esc_html__( 'Inherit from global option set in Settings / Reading', 'johannes' ),
			'custom'  => esc_html__( 'Custom number', 'johannes' ),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'front_page_ppp_num',
		'section'     => 'johannes_front_classic',
		'type'     => 'number',
		'label'  => esc_html__( 'Specify number of posts', 'johannes' ),
		'default'  => johannes_get_default_option( 'front_page_ppp_num' ),
		'required'    => array(
			array(
				'setting'  => 'front_page_ppp',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_pagination',
		'section'     => 'johannes_front_classic',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Pagination', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_pagination' ),
		'choices'     => johannes_get_pagination_layouts( false, true )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_cat',
		'section'     => 'johannes_front_classic',
		'type'        => 'select',
		'multiple'    => 10,
		'label'       => esc_html__( 'From category', 'johannes' ),
		'description'       => esc_html__( 'Select one or more categories to pull the posts from, or leave empty for all categories', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_cat' ),
		'choices'     => Kirki_Helper::get_terms( 'category' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'front_page_tag',
		'section'     => 'johannes_front_classic',
		'type'        => 'select',
		'multiple'    => 10,
		'label'       => esc_html__( 'Tagged with', 'johannes' ),
		'description'       => esc_html__( 'Select one or more tags to pull the posts from, or leave empty for all tags', 'johannes' ),
		'default'     => johannes_get_default_option( 'front_page_tag' ),
		'choices'     => Kirki_Helper::get_terms( 'post_tag' )
	) );




/* Single */

Kirki::add_section( 'johannes_single', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Single Post', 'johannes' ),
		'priority'    => 70,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout',
		'section'     => 'johannes_single',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout' ),
		'choices'     => johannes_get_single_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_1_img_ratio',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 1', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_1_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '1'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_1_img_custom',
		'section'     => 'johannes_single',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_1_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'single_layout_1_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '1'
			),

		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_2_img_ratio',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 2', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_2_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_2_img_custom',
		'section'     => 'johannes_single',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_2_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'single_layout_2_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_3_height',
		'section'     => 'johannes_single',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 3', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_3_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '3'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_4_height',
		'section'     => 'johannes_single',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 4', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_4_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '4'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_5_img_ratio',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 5', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_5_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '5'
			),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'single_layout_5_img_custom',
		'section'     => 'johannes_single',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_layout_5_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'single_layout_5_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'single_layout',
				'operator' => '==',
				'value'    => '5'
			),
		),
	) );



Kirki::add_field( 'johannes', array(
		'settings'    => 'single_sidebar_position',
		'section'     => 'johannes_single',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Sidebar position', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_sidebar_position' ),
		'choices'     => johannes_get_sidebar_layouts( false, true )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_sidebar_standard',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Standard sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_sidebar_standard' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'single_sidebar_position',
				'operator' => '!=',
				'value'    =>  'none'
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_sidebar_sticky',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Sticky sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_sidebar_sticky' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'single_sidebar_position',
				'operator' => '!=',
				'value'    =>  'none'
			)
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'single_width',
		'section'     => 'johannes_single',
		'type'        => 'radio-buttonset',
		'label'       => esc_html__( 'Content (text) width', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_width' ),
		'choices'     => array(
			'6'   => esc_html__( 'Narrow', 'johannes' ),
			'7' => esc_html__( 'Medium', 'johannes' ),
			'8'  => esc_html__( 'Wide', 'johannes' ),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'single_cat',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display category link', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_cat' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_meta',
		'section'     => 'johannes_single',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Display meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_meta' ),
		'choices' =>  johannes_get_meta_opts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_fimg',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display featured image', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_fimg' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_fimg_cap',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display featured image caption', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_fimg_cap' ),
		'required'    => array(
			array(
				'setting'  => 'single_fimg',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'single_headline',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display headline (post exceprt)', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_headline' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_avatar',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display side author avatar', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_avatar' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_tags',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display tags', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_tags' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_share',
		'section'     => 'johannes_single',
		'type'        => 'select',
		'label'       => esc_html__( 'Display share buttons', 'johannes' ),
		'choices' => array(
			'above' => esc_html__( 'Above the content', 'johannes' ),
			'below' => esc_html__( 'Below the content', 'johannes' ),
			'above_below' => esc_html__( 'Above and below the content', 'johannes' ),
			'none' => esc_html__( 'Do not display', 'johannes' ),
		),
		'default'     => johannes_get_default_option( 'single_share' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'single_author',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display author area', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_author' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'single_related',
		'section'     => 'johannes_single',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display "related posts" area', 'johannes' ),
		'default'     => johannes_get_default_option( 'single_related' ),
	) );



Kirki::add_field( 'johannes', array(
		'settings'    => 'related_layout',
		'section'     => 'johannes_single',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Related post layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'related_layout' ),
		'choices'     => johannes_get_post_layouts( array( 'sidebar' => false ) ),
		'required'    => array(
			array(
				'setting'  => 'single_related',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'related_limit',
		'section'     => 'johannes_single',
		'type'        => 'number',
		'label'       => esc_html__( 'Number of related post', 'johannes' ),
		'default'     => johannes_get_default_option( 'related_limit' ),
		'required'    => array(
			array(
				'setting'  => 'single_related',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'related_type',
		'section'     => 'johannes_single',
		'type'        => 'radio',
		'label'       => esc_html__( 'Related area chooses from posts', 'johannes' ),
		'default'     => johannes_get_default_option( 'related_type' ),
		'choices'  => array(
			'cat'         => esc_html__( 'Located in the same category', 'johannes' ),
			'tag'         => esc_html__( 'Tagged with at least one same tag', 'johannes' ),
			'cat_or_tag'  => esc_html__( 'Located in the same category OR tagged with a same tag', 'johannes' ),
			'cat_and_tag' => esc_html__( 'Located in the same category AND tagged with a same tag', 'johannes' ),
			'author'      => esc_html__( 'By the same author', 'johannes' ),
			'0'           => esc_html__( 'All posts', 'johannes' ),
		),
		'required'    => array(
			array(
				'setting'  => 'single_related',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'related_order',
		'section'     => 'johannes_single',
		'type'        => 'radio',
		'label'       => esc_html__( 'Related posts are ordered by', 'johannes' ),
		'default'     => johannes_get_default_option( 'related_order' ),
		'choices'  => johannes_get_post_order_opts(),
		'required'    => array(
			array(
				'setting'  => 'single_related',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );


/* Page */

Kirki::add_section( 'johannes_page', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Page', 'johannes' ),
		'priority'    => 80,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout',
		'section'     => 'johannes_page',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout' ),
		'choices'     => johannes_get_page_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_1_img_ratio',
		'section'     => 'johannes_page',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 1', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_1_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '1'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_1_img_custom',
		'section'     => 'johannes_page',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_1_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'page_layout_1_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '1'
			),

		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_2_img_ratio',
		'section'     => 'johannes_page',
		'type'        => 'select',
		'label'       => esc_html__( 'Image ratio for layout 2', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_2_img_ratio' ),
		'choices'   => johannes_get_image_ratio_opts(),
		'required'    => array(
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_2_img_custom',
		'section'     => 'johannes_page',
		'type'        => 'text',
		'label'       => esc_html__( 'Your custom ratio', 'johannes' ),
		'description'      => esc_html__( 'i.e. Put 2:1', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_2_img_custom' ),
		'required'    => array(
			array(
				'setting'  => 'page_layout_2_img_ratio',
				'operator' => '==',
				'value'    => 'custom'
			),
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_3_height',
		'section'     => 'johannes_page',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 3', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_3_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '3'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_layout_4_height',
		'section'     => 'johannes_page',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 4', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_layout_4_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'page_layout',
				'operator' => '==',
				'value'    => '4'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_sidebar_position',
		'section'     => 'johannes_page',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Sidebar position', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_sidebar_position' ),
		'choices'     => johannes_get_sidebar_layouts( false, true )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_sidebar_standard',
		'section'     => 'johannes_page',
		'type'        => 'select',
		'label'       => esc_html__( 'Standard sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_sidebar_standard' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'page_sidebar_position',
				'operator' => '!=',
				'value'    =>  'none'
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_sidebar_sticky',
		'section'     => 'johannes_page',
		'type'        => 'select',
		'label'       => esc_html__( 'Sticky sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_sidebar_sticky' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'page_sidebar_position',
				'operator' => '!=',
				'value'    =>  'none'
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_width',
		'section'     => 'johannes_page',
		'type'        => 'radio-buttonset',
		'label'       => esc_html__( 'Content (text) width', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_width' ),
		'choices'     => array(
			'6'   => esc_html__( 'Narrow', 'johannes' ),
			'7' => esc_html__( 'Medium', 'johannes' ),
			'8'  => esc_html__( 'Wide', 'johannes' ),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_fimg',
		'section'     => 'johannes_page',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display featured image', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_fimg' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'page_fimg_cap',
		'section'     => 'johannes_page',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display featured image caption', 'johannes' ),
		'default'     => johannes_get_default_option( 'page_fimg_cap' ),
		'required'    => array(
			array(
				'setting'  => 'page_fimg',
				'operator' => '==',
				'value'    =>  true
			)
		)
	) );


/* Archive */

Kirki::add_panel( 'johannes_panel_archives', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Archive Templates', 'johannes' ),
		'priority'    => 90,
	) );

Kirki::add_section( 'johannes_archives_general', array(
		'panel'          => 'johannes_panel_archives',
		'title'          => esc_attr__( 'General', 'johannes' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_layout',
		'section'     => 'johannes_archives_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_layout' ),
		'choices'     => johannes_get_archive_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_layout_2_height',
		'section'     => 'johannes_archives_general',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 2', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_layout_2_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'archive_layout',
				'operator' => '==',
				'value'    => '2'
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_layout_3_height',
		'section'     => 'johannes_archives_general',
		'type'        => 'number',
		'label'       => esc_html__( 'Cover area (image) height for layout 3', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_layout_3_height' ),
		'choices'     => array(
			'step' => '1'
		),
		'required'    => array(
			array(
				'setting'  => 'archive_layout',
				'operator' => '==',
				'value'    => '3'
			),
		),
	) );



Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_description',
		'section'     => 'johannes_archives_general',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display archive description', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_description' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_meta',
		'section'     => 'johannes_archives_general',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display number of posts label', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_meta' )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_loop',
		'section'     => 'johannes_archives_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Posts layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_loop' ),
		'choices'     => johannes_get_post_layouts()
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_sidebar_position',
		'section'     => 'johannes_archives_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Sidebar position', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_sidebar_position' ),
		'choices'     => johannes_get_sidebar_layouts(),
		'required'    => array(
			array(
				'setting'  => 'archive_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_sidebar_standard',
		'section'     => 'johannes_archives_general',
		'type'        => 'select',
		'label'       => esc_html__( 'Standard sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_sidebar_standard' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'archive_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_sidebar_sticky',
		'section'     => 'johannes_archives_general',
		'type'        => 'select',
		'label'       => esc_html__( 'Sticky sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_sidebar_sticky' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'archive_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			)
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_ppp',
		'section'     => 'johannes_archives_general',
		'type'        => 'radio',
		'label'       => esc_html__( 'Number of posts per page', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_ppp' ),
		'choices'     => array(
			'inherit' => esc_html__( 'Inherit from global option set in Settings / Reading', 'johannes' ),
			'custom'  => esc_html__( 'Custom number', 'johannes' ),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'archive_ppp_num',
		'section'     => 'johannes_archives_general',
		'type'     => 'number',
		'label'  => esc_html__( 'Specify number of posts', 'johannes' ),
		'default'  => johannes_get_default_option( 'archive_ppp_num' ),
		'required'    => array(
			array(
				'setting'  => 'archive_ppp',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'archive_pagination',
		'section'     => 'johannes_archives_general',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Pagination', 'johannes' ),
		'default'     => johannes_get_default_option( 'archive_pagination' ),
		'choices'     => johannes_get_pagination_layouts()
	) );



/* Category */

Kirki::add_section( 'johannes_archives_category', array(
		'panel'          => 'johannes_panel_archives',
		'title'          => esc_attr__( 'Category Template', 'johannes' )
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'category_settings',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio',
		'label'       => esc_html__( 'Category settings', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_settings' ),
		'choices'     => array(
			'inherit' => esc_html__( 'Inherit from general Archive settings', 'johannes' ),
			'custom'  => esc_html__( 'Customize', 'johannes' ),
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'category_layout',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_layout' ),
		'choices'     => johannes_get_archive_layouts(),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'category_description',
		'section'     => 'johannes_archives_category',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display archive description', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_description' ),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_meta',
		'section'     => 'johannes_archives_category',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Display number of posts label', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_meta' ),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_loop',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Posts layout', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_loop' ),
		'choices'     => johannes_get_post_layouts(),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_sidebar_position',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Sidebar position', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_sidebar_position' ),
		'choices'     => johannes_get_sidebar_layouts(),
		'required'    => array(
			array(
				'setting'  => 'category_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			),
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_sidebar_standard',
		'section'     => 'johannes_archives_category',
		'type'        => 'select',
		'label'       => esc_html__( 'Standard sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_sidebar_standard' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'category_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			),
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			)
		)
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_sidebar_sticky',
		'section'     => 'johannes_archives_category',
		'type'        => 'select',
		'label'       => esc_html__( 'Sticky sidebar', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_sidebar_sticky' ),
		'choices'     => johannes_get_sidebars_list(),
		'required'    => array(
			array(
				'setting'  => 'category_loop',
				'operator' => 'in',
				'value'    =>  array_map( 'strval', array_keys( johannes_get_post_layouts( array( 'sidebar' => true ) ) ) )
			),
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			)
		)
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'category_ppp',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio',
		'label'       => esc_html__( 'Number of posts per page', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_ppp' ),
		'choices'     => array(
			'inherit' => esc_html__( 'Inherit from global option set in Settings / Reading', 'johannes' ),
			'custom'  => esc_html__( 'Custom number', 'johannes' ),
		),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'       => 'category_ppp_num',
		'section'     => 'johannes_archives_category',
		'type'     => 'number',
		'label'  => esc_html__( 'Specify number of posts', 'johannes' ),
		'default'  => johannes_get_default_option( 'category_ppp_num' ),
		'required'    => array(
			array(
				'setting'  => 'category_ppp',
				'operator' => '==',
				'value'    => 'custom'
			) ),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'category_pagination',
		'section'     => 'johannes_archives_category',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Pagination', 'johannes' ),
		'default'     => johannes_get_default_option( 'category_pagination' ),
		'choices'     => johannes_get_pagination_layouts(),
		'required'    => array(
			array(
				'setting'  => 'category_settings',
				'operator' => '==',
				'value'    => 'custom'
			) )
	) );


/* Typography */

Kirki::add_section( 'johannes_typography', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Typography', 'johannes' ),
		'priority'    => 100,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'main_font',
		'section'     => 'johannes_typography',
		'type'        => 'typography',
		'label'       => esc_html__( 'Main text font', 'johannes' ),
		'default'     => johannes_get_default_option( 'main_font' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'h_font',
		'section'     => 'johannes_typography',
		'type'        => 'typography',
		'label'       => esc_html__( 'Headings font', 'johannes' ),
		'description'    => esc_html__( 'This is the font used for titles and headings', 'johannes' ),
		'default'     => johannes_get_default_option( 'h_font' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'nav_font',
		'section'     => 'johannes_typography',
		'type'        => 'typography',
		'label'       => esc_html__( 'Navigation font', 'johannes' ),
		'description'    => esc_html__( 'This is the font used for main website navigation', 'johannes' ),
		'default'     => johannes_get_default_option( 'nav_font' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'nav_font',
		'section'     => 'johannes_typography',
		'type'        => 'typography',
		'label'       => esc_html__( 'Navigation font', 'johannes' ),
		'description'    => esc_html__( 'This is the font used for main website navigation', 'johannes' ),
		'default'     => johannes_get_default_option( 'nav_font' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'button_font',
		'section'     => 'johannes_typography',
		'type'        => 'typography',
		'label'       => esc_html__( 'Button font', 'johannes' ),
		'description'    => esc_html__( 'This is the font used for button labels and category links', 'johannes' ),
		'default'     => johannes_get_default_option( 'button_font' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_p',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Regular text font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_p' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_small',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Small text font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_small' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_nav',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Main website navigation font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_nav' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_nav_ico',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Navigation icons (hamburger, search...) font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_nav_ico' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_section_title',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Section title font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_section_title' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_widget_title',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Widget title font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_widget_title' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_punchline',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'Punchline font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_punchline' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h1',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H1 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h1' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h2',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H2 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h2' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h3',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H3 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h3' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h4',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H4 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h4' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h5',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H5 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h5' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'font_size_h6',
		'section'     => 'johannes_typography',
		'type'        => 'number',
		'label'       => esc_html__( 'H6 font size', 'johannes' ),
		'default'     => johannes_get_default_option( 'font_size_h6' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'uppercase',
		'section'     => 'johannes_typography',
		'type'        => 'multicheck',
		'label'       => esc_html__( 'Uppercase text', 'johannes' ),
		'description' => esc_html__( 'Select elements that you want to display with all CAPITAL LETTERS', 'johannes' ),
		'default'     => johannes_get_default_option( 'uppercase' ),
		'choices'  => array(
			'.johannes-header .site-title a'  => esc_html__( 'Site title (when logo is not used)', 'johannes' ),
			'.site-description' => esc_html__( 'Site description', 'johannes' ),
			'.johannes-header li a' => esc_html__( 'Main site navigation', 'johannes' ),
			'.johannes-header-top' =>  esc_html__( 'Header top bar', 'johannes' ),
			'.widget-title'  => esc_html__( 'Widget title', 'johannes' ),
			'.section-title' => esc_html__( 'Section title (modules and archives)', 'johannes' ),
			'.entry-title' => esc_html__( 'Post/page title', 'johannes' ),
		),
	) );




/* WooCommerce */

if ( johannes_is_woocommerce_active() ) {

	Kirki::add_section( 'johannes_woocommerce', array(
			'panel'          => 'johannes_panel',
			'title'          => esc_attr__( 'WooCommerce', 'johannes' ),
			'priority'    => 105,
		) );

	Kirki::add_field( 'johannes', array(
			'settings'    => 'woocommerce_sidebar_position',
			'section'     => 'johannes_woocommerce',
			'type'        => 'radio-image',
			'label'       => esc_html__( 'WooCommerce sidebar layout', 'johannes' ),
			'default'     => johannes_get_default_option( 'woocommerce_sidebar_position' ),
			'choices'   => johannes_get_sidebar_layouts( false, true ),
		) );

	Kirki::add_field( 'johannes', array(
			'settings'    => 'woocommerce_sidebar_standard',
			'section'     => 'johannes_woocommerce',
			'type'        => 'select',
			'label'       => esc_html__( 'WooCommerce standard sidebar', 'johannes' ),
			'default'     => johannes_get_default_option( 'woocommerce_sidebar_standard' ),
			'choices'   => johannes_get_sidebars_list(),
			'required'    => array(
				array(
					'setting'  => 'woocommerce_sidebar_position',
					'operator' => '!=',
					'value'    => 'none',
				),
			)
		) );

	Kirki::add_field( 'johannes', array(
			'settings'    => 'woocommerce_sidebar_sticky',
			'section'     => 'johannes_woocommerce',
			'type'        => 'select',
			'label'       => esc_html__( 'WooCommerce sticky sidebar', 'johannes' ),
			'default'     => johannes_get_default_option( 'woocommerce_sidebar_sticky' ),
			'choices'   => johannes_get_sidebars_list(),
			'required'    => array(
				array(
					'setting'  => 'woocommerce_sidebar_position',
					'operator' => '!=',
					'value'    => 'none',
				),
			)

		) );

	Kirki::add_field( 'johannes', array(
			'settings'    => 'woocommerce_cart_force',
			'section'     => 'johannes_woocommerce',
			'type'        => 'toggle',
			'label'       => esc_html__( 'Always display Cart icon', 'johannes' ),
			'description' => esc_html__( 'If you check this option, Cart icon will be always visible on WooCommerce pages, even if it is not selected globally in Header options', 'johannes' ),
			'default'     => johannes_get_default_option( 'woocommerce_cart_force' ),
		) );

}




/* Ads */
Kirki::add_section( 'johannes_ads', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Ads', 'johannes' ),
		'description'   => esc_html__( 'Use these options to fill your ad slots. Both HTML/image and JavaScript related ads are allowed. You can also use shortcodes from your favorite Ad plugins.', 'johannes' ),
		'priority'    => 110,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_header',
		'section'     => 'johannes_ads',
		'type'        => 'editor',
		'label'    => esc_html__( 'Inside header', 'johannes' ),
		'description' => esc_html__( 'This ad will be visible inside header if you choose one of specific header layouts that can display the ad', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_header' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_above_archive',
		'section'     => 'johannes_ads',
		'type'        => 'editor',
		'label'    => esc_html__( 'Archive top', 'johannes' ),
		'description' => esc_html__( 'This ad will be displayed above the content of your archive templates (i.e. categories, tags, etc...)', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_above_archive' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_above_singular',
		'section'     => 'johannes_ads',
		'type'        => 'editor',
		'label'    => esc_html__( 'Single post/page top', 'johannes' ),
		'description' => esc_html__( 'This ad will be displayed above the content of single posts and pages', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_above_singular' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_above_footer',
		'section'     => 'johannes_ads',
		'type'        => 'editor',
		'label'    => esc_html__( 'Above footer', 'johannes' ),
		'description' => esc_html__( 'This ad will be displayed above the footer area on all templates', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_above_footer' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_between_posts',
		'section'     => 'johannes_ads',
		'type'        => 'editor',
		'label'    => esc_html__( 'Between posts', 'johannes' ),
		'description' => esc_html__( 'This ad will be displayed between the posts listing on your archive templates', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_between_posts' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_between_position',
		'section'     => 'johannes_ads',
		'type'        => 'number',
		'label'    => esc_html__( 'Between posts ad position', 'johannes' ),
		'description' => esc_html__( 'Specify after how many posts you want to display the ad', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_between_position' ),
		'required'    => array(
			array(
				'setting'  => 'ad_between_posts',
				'operator' => '!=',
				'value'    => '',
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'ad_exclude',
		'section'     => 'johannes_ads',
		'type'        => 'select',
		'multiple'    => 10,
		'label'       => esc_html__( 'Do not show ads these specific pages', 'johannes' ),
		'description'       => esc_html__( 'Select pages on which you don\'t want to display ads', 'johannes' ),
		'default'     => johannes_get_default_option( 'ad_exclude' ),
		'choices'     => Kirki_Helper::get_posts( array( 'post_type' => 'page', 'posts_per_page' => '-1' ) )
	) );


/* Miscellaneous */
Kirki::add_section( 'johannes_misc', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Miscellaneous', 'johannes' ),
		'priority'    => 120,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'rtl_mode',
		'section'     => 'johannes_misc',
		'type'        => 'toggle',
		'label'       => esc_html__( 'RTL mode (right to left)', 'johannes' ),
		'description' => esc_html__( 'Enable this option if the website is using right to left writing/reading', 'johannes' ),
		'default'     => johannes_get_default_option( 'rtl_mode' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'rtl_lang_skip',
		'section'     => 'johannes_misc',
		'type'        => 'text',
		'label'       => esc_html__( 'Skip RTL for specific language(s)', 'johannes' ),
		'description' => esc_html__( 'i.e. If you are using Arabic and English versions on the same WordPress installation you should put "en_US" in this field and its version will not be displayed as RTL. Note: To exclude multiple languages, separate by comma: en_US, de_DE', 'johannes' ),
		'default'     => johannes_get_default_option( 'rtl_lang_skip' ),
		'required'    => array(
			array(
				'setting'  => 'rtl_mode',
				'operator' => '==',
				'value'    => true,
			),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'social_share',
		'section'     => 'johannes_misc',
		'type'        => 'sortable',
		'label'       => esc_html__( 'Social sharing', 'johannes' ),
		'description' => esc_html__( 'Select social networks that you want to use for sharing posts', 'johannes' ),
		'default'     => johannes_get_default_option( 'social_share' ),
		'choices' => array(
			'facebook'    => esc_html__( 'Facebook', 'johannes' ),
			'twitter'     => esc_html__( 'Twitter', 'johannes' ),
			'reddit'      => esc_html__( 'Reddit', 'johannes' ),
			'pinterest'   => esc_html__( 'Pinterest', 'johannes' ),
			'email'       => esc_html__( 'Email', 'johannes' ),
			'gplus'       => esc_html__( 'Google+', 'johannes' ),
			'linkedin'    => esc_html__( 'LinkedIN', 'johannes' ),
			'stumbleupon' => esc_html__( 'StumbleUpon', 'johannes' ),
			'vk'          => esc_html__( 'vKontakte', 'johannes' ),
			'whatsapp'    => esc_html__( 'WhatsApp', 'johannes' ),
		),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'breadcrumbs',
		'section'     => 'johannes_misc',
		'type'        => 'radio',
		'label'       => esc_html__( 'Enable breadcrumbs support', 'johannes' ),
		'description' => esc_html__( 'Select a plugin you are using for breadcrumbs', 'johannes' ),
		'default'     => johannes_get_default_option( 'breadcrumbs' ),
		'choices'   => johannes_get_breadcrumbs_options(),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'popup',
		'section'     => 'johannes_misc',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Open images in popup', 'johannes' ),
		'description' => esc_html__( 'If you check this option, images inside galleries and post content will open in popup', 'johannes' ),
		'default'     => johannes_get_default_option( 'popup' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'go_to_top',
		'section'     => 'johannes_misc',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable "go to top" button', 'johannes' ),
		'default'     => johannes_get_default_option( 'go_to_top' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'more_string',
		'section'     => 'johannes_misc',
		'type'        => 'text',
		'label'       => esc_html__( 'More string', 'johannes' ),
		'description' => esc_html__( 'Specify your "more" string to append after the limited post excerpts', 'johannes' ),
		'default'     => johannes_get_default_option( 'more_string' ),
	) );


Kirki::add_field( 'johannes', array(
		'settings'    => 'words_read_per_minute',
		'section'     => 'johannes_misc',
		'type'        => 'number',
		'label'       => esc_html__( 'Words to read per minute', 'johannes' ),
		'description' => esc_html__( 'Use this option to set the number of words your visitors read per minute, in order to fine-tune the calculation of the post reading time meta data', 'johannes' ),
		'default'     => johannes_get_default_option( 'words_read_per_minute' ),
		'choices'     => array(
			'step' => '1'
		),
	) );


Kirki::add_field( 'johannes', array(
		'settings'       => 'default_fimg',
		'section'     => 'johannes_misc',
		'type'     => 'image',
		'label'    => esc_html__( 'Default featured image', 'johannes' ),
		'description' => esc_html__( 'Upload your default featured image/placeholder. It will be displayed for posts that do not have a featured image set', 'johannes' ),
		'default'  => johannes_get_default_option( 'default_fimg' ),
		'choices'     => array(
			'save_as' => 'array',
		),
	) );


/* Performance */
Kirki::add_section( 'johannes_performance', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Performance', 'johannes' ),
		'priority'    => 130,
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'minify_css',
		'section'     => 'johannes_performance',
		'type'        => 'toggle',
		'label'    => esc_html__( 'Use minified CSS', 'johannes' ),
		'description' => esc_html__( 'Load all theme CSS files combined and minified into a single file.', 'johannes' ),
		'default'     => johannes_get_default_option( 'minify_css' ),
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'minify_js',
		'section'     => 'johannes_performance',
		'type'        => 'toggle',
		'label'    => esc_html__( 'Use minified JS', 'johannes' ),
		'description' => esc_html__( 'Load all theme JavaScript files combined and minified into a single file.', 'johannes' ),
		'default'     => johannes_get_default_option( 'minify_js' ),
	) );


$image_sizes = johannes_get_image_sizes();

foreach ( $image_sizes as $key => $size ) {
	$image_sizes[$key] = $size['title'];
}

Kirki::add_field( 'johannes', array(
		'settings'    => 'disable_img_sizes',
		'section'     => 'johannes_performance',
		'type'        => 'multicheck',
		'label'    => esc_html__( 'Disable additional image sizes', 'johannes' ),
		'description' => esc_html__( 'By default, the theme generates additional size for each of the layouts it offers. You can use this option to avoid creating additional sizes if you are not using a particular layout, in order to save your server space.', 'johannes' ),
		'default'     => johannes_get_default_option( 'disable_img_sizes' ),
		'choices' => $image_sizes
	) );


/* Translate */
Kirki::add_section( 'johannes_translate', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Translation', 'johannes' ),
		'description'   => esc_html__( 'Use these settings to quickly translate or change the text in this theme. If you want to remove the text completely instead of modifying it, you can use "-1" as a value for a particular field. Note: If you are using this theme for a multilingual website, you need to disable these options and use multilanguage plugins (such as WPML) and manual translation with .po and .mo files located inside the "languages" folder.', 'johannes' ),
		'priority'    => 140
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'enable_translate',
		'section'     => 'johannes_translate',
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable theme translation', 'johannes' ),
		'default'     => johannes_get_default_option( 'enable_translate' ),
	) );


$translate_strings = johannes_get_translate_options();

foreach ( $translate_strings as $string_key => $item ) {

	if ( isset( $item['hidden'] ) ) {
		continue;
	}

	Kirki::add_field( 'johannes', array(
			'settings'    => 'tr_' . $string_key,
			'section'     => 'johannes_translate',
			'type'        => 'text',
			'label'       => esc_html( $item['text'] ),
			'description' => isset( $item['desc'] ) ? $item['desc'] : '',
			'default'  => isset( $item['default'] ) ? $item['default'] : '',
		) );
}


/* Presets */
Kirki::add_section( 'johannes_presets', array(
		'panel'          => 'johannes_panel',
		'title'          => esc_attr__( 'Presets', 'johannes' ),
		'description'   => esc_html__( 'Use these settings to set options in bulk with our handpicked design presets. Of course, you can fine-tune everything in the theme options.', 'johannes' ),
		'priority'    => 150
	) );


$all_presets = johannes_get_option_presets();

$preset_layouts_choices = array();
$preset_layouts_settings = array();

foreach( $all_presets['layouts'] as $preset_id => $preset ){
	$preset_layouts_choices[$preset_id]['alt'] = $preset['alt'];
	$preset_layouts_choices[$preset_id]['src'] = $preset['src'];
	$preset_layouts_settings[$preset_id] = array( 'settings' => $preset['settings'] );
}

$preset_colors_choices = array();
$preset_colors_settings = array();

foreach( $all_presets['colors'] as $preset_id => $preset ){
	$preset_colors_choices[$preset_id]['alt'] = $preset['alt'];
	$preset_colors_choices[$preset_id]['src'] = $preset['src'];
	$preset_colors_settings[$preset_id] = array( 'settings' => $preset['settings'] );
}

$preset_fonts_choices = array();
$preset_fonts_settings = array();

foreach( $all_presets['fonts'] as $preset_id => $preset ){
	$preset_fonts_choices[$preset_id]['alt'] = $preset['alt'];
	$preset_fonts_choices[$preset_id]['src'] = $preset['src'];
	$preset_fonts_settings[$preset_id] = array( 'settings' => $preset['settings'] );
}


Kirki::add_field( 'johannes', array(
		'settings'    => 'preset_layouts',
		'section'     => 'johannes_presets',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Layouts', 'johannes' ),
		'default'     => 0,
		'choices'     => $preset_layouts_choices,
		'preset'      => $preset_layouts_settings
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'preset_colors',
		'section'     => 'johannes_presets',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Color', 'johannes' ),
		'default'     => 0,
		'choices'     => $preset_colors_choices,
		'preset'      => $preset_colors_settings
	) );

Kirki::add_field( 'johannes', array(
		'settings'    => 'preset_fonts',
		'section'     => 'johannes_presets',
		'type'        => 'radio-image',
		'label'       => esc_html__( 'Font', 'johannes' ),
		'default'     => 0,
		'choices'     => $preset_fonts_choices,
		'preset'      => $preset_fonts_settings
	) );



?>