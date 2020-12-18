<?php 
/**
 * Theme Options Panel.
 *
 * @package ePortfolio
 */

$default = eportfolio_get_default_theme_options();

// Add Theme Options Panel.
$wp_customize->add_panel( 'theme_option_panel',
	array(
		'title'      => esc_html__( 'Theme Options', 'eportfolio' ),
		'priority'   => 200,
		'capability' => 'edit_theme_options',
	)
);

$wp_customize->add_setting( 'short_description_details',
	array(
	'default'           => $default['short_description_details'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_text_field',
	)
);
$wp_customize->add_control( 'short_description_details',
	array(
	'label'    => __( 'Short Description Details', 'eportfolio' ),
	'section'  => 'header_image',
	'type'     => 'text',
	'priority' => 120,
	)
);

// Setting button_text.
$wp_customize->add_setting( 'button_text',
	array(
	'default'           => $default['button_text'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_text_field',
	)
);
$wp_customize->add_control( 'button_text',
	array(
	'label'    => __( 'Button Text', 'eportfolio' ),
	'section'  => 'header_image',
	'type'     => 'text',
	'priority' => 120,
	)
);

// Setting button_url_link.
$wp_customize->add_setting( 'button_url_link',
	array(
	'default'           => $default['button_url_link'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'esc_url_raw',
	)
);
$wp_customize->add_control( 'button_url_link',
	array(
	'label'    => __( 'Button URL Link', 'eportfolio' ),
	'section'  => 'header_image',
	'type'     => 'text',
	'priority' => 120,
	)
);


/*Archive section start */
$wp_customize->add_section( 'theme_archive_option_section_settings',
	array(
		'title'      => esc_html__( 'Archive Section Setting Options', 'eportfolio' ),
		'priority'   => 100,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_option_panel',
	)
);


$wp_customize->add_setting( 'enable_archive_layout_switch',
	array(
		'default'           => $default['enable_archive_layout_switch'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_archive_layout_switch',
	array(
		'label'    => esc_html__( 'Enable Switch/toggle Option on Arcvhives', 'eportfolio' ),
		'section'  => 'theme_archive_option_section_settings',
		'type'     => 'checkbox',
		'priority' => 20,
	)
);

$wp_customize->add_setting( 'archive_layout_style',
	array(
		'default'           => $default['archive_layout_style'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'archive_layout_style',
	array(
		'label'    => esc_html__( 'Select Default Layout For Archive Post', 'eportfolio' ),
		'section'  => 'theme_archive_option_section_settings',
		'choices'  => array(
                'archive-list-post-layout' => esc_html__( 'List Post Layout', 'eportfolio' ),
                'archive-grid-post-layout' => esc_html__( 'Grid/Masonry Layout', 'eportfolio' ),
		    ),
		'type'     => 'select',
		'priority' => 30,
	)
);


$wp_customize->add_setting( 'archive_layout_grid_column',
	array(
		'default'           => $default['archive_layout_grid_column'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'archive_layout_grid_column',
	array(
		'label'    => esc_html__( 'Select Number of Grid Column', 'eportfolio' ),
		'section'  => 'theme_archive_option_section_settings',
		'choices'  => array(
                '3-column-arc' => esc_html__( '3-Column Layout', 'eportfolio' ),
                '2-column-arc' => esc_html__( '2-Column Layout', 'eportfolio' ),
		    ),
		'type'     => 'select',
		'priority' => 30,
	)
);


/*layout management section start */
$wp_customize->add_section( 'theme_option_section_settings',
	array(
		'title'      => esc_html__( 'Layout Management', 'eportfolio' ),
		'priority'   => 100,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_option_panel',
	)
);

/*Date Layout*/
$wp_customize->add_setting( 'site_date_layout_option',
	array(
		'default'           => $default['site_date_layout_option'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'site_date_layout_option',
	array(
		'label'    => esc_html__( 'Select Date Format', 'eportfolio' ),
		'section'  => 'theme_option_section_settings',
		'choices'  => array(
                'in-time-span' => __( 'Time Span Format', 'eportfolio' ),
                'normal-format' => __( 'Regular Format', 'eportfolio' ),
		    ),
		'type'     => 'select',
		'priority' => 160,
	)
);

/*Global Layout*/
$wp_customize->add_setting( 'global_layout',
	array(
		'default'           => $default['global_layout'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'global_layout',
	array(
		'label'    => esc_html__( 'Global Page/Post Sidebar Layout', 'eportfolio' ),
		'section'  => 'theme_option_section_settings',
		'choices'   => array(
			'left-sidebar'  => esc_html__( 'Primary Sidebar - Content', 'eportfolio' ),
			'right-sidebar' => esc_html__( 'Content - Primary Sidebar', 'eportfolio' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'eportfolio' ),
			),
		'type'     => 'select',
		'priority' => 170,
	)
);

// Pagination Section.
$wp_customize->add_section( 'pagination_section',
	array(
	'title'      => __( 'Pagination Options', 'eportfolio' ),
	'priority'   => 110,
	'capability' => 'edit_theme_options',
	'panel'      => 'theme_option_panel',
	)
);

// Setting pagination_type.
$wp_customize->add_setting( 'pagination_type',
	array(
	'default'           => $default['pagination_type'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'pagination_type',
	array(
	'label'       => __( 'Pagination Type', 'eportfolio' ),
	'section'     => 'pagination_section',
	'type'        => 'select',
	'choices'               => array(
		'default' => __( 'Default (Older / Newer Post)', 'eportfolio' ),
		'numeric' => __( 'Numeric', 'eportfolio' ),
	    ),
	'priority'    => 100,
	)
);

// Footer Section.
$wp_customize->add_section( 'footer_section',
	array(
	'title'      => __( 'Footer Options', 'eportfolio' ),
	'priority'   => 130,
	'capability' => 'edit_theme_options',
	'panel'      => 'theme_option_panel',
	)
);

// Setting copyright_text.
$wp_customize->add_setting( 'copyright_text',
	array(
	'default'           => $default['copyright_text'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_text_field',
	)
);
$wp_customize->add_control( 'copyright_text',
	array(
	'label'    => __( 'Footer Copyright Text', 'eportfolio' ),
	'section'  => 'footer_section',
	'type'     => 'text',
	'priority' => 120,
	)
);

// Preloader Section.
$wp_customize->add_section('enable_preloader_option',
    array(
        'title' => __('Preloader Options', 'eportfolio'),
        'priority' => 120,
        'capability' => 'edit_theme_options',
        'panel' => 'theme_option_panel',
    )
);

// Setting enable_preloader.
$wp_customize->add_setting('enable_preloader',
    array(
        'default' => $default['enable_preloader'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'eportfolio_sanitize_checkbox',
    )
);
$wp_customize->add_control('enable_preloader',
    array(
        'label' => __('Enable Preloader', 'eportfolio'),
        'section' => 'enable_preloader_option',
        'type' => 'checkbox',
        'priority' => 150,
    )
);
