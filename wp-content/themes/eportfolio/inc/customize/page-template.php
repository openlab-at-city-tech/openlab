<?php 
/**
 * Options Panel for page-template settings.
 *
 * @package ePortfolio
 */

$default = eportfolio_get_default_theme_options();

// Add page template setting panel.
$wp_customize->add_panel( 'theme_page_template_panel',
	array(
		'title'      => esc_html__( 'Page-Template / Page Layout Options', 'eportfolio' ),
		'priority'   => 190,
		'capability' => 'edit_theme_options',
	)
);


// Blog Page Section Settings.
$wp_customize->add_section( 'blog_page_setting_options',
	array(
		'title'      => esc_html__( 'Latest Posts / Blog Page Settings', 'eportfolio' ),
		'priority'   => 10,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_page_template_panel',
	)
);


// Setting - show_slider_on_blog.
$wp_customize->add_setting( 'show_slider_on_blog',
	array(
		'default'           => $default['show_slider_on_blog'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'show_slider_on_blog',
	array(
		'label'    => esc_html__( 'Enable Slider On Blog', 'eportfolio' ),
		'section'  => 'blog_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 10,
	)
);


// Setting - drop down category for slider.
$wp_customize->add_setting( 'select_category_for_blog_slider',
	array(
		'default'           => $default['select_category_for_blog_slider'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint',
	)
);
$wp_customize->add_control( new Eportfolio_Dropdown_Taxonomies_Control( $wp_customize, 'select_category_for_blog_slider',
	array(
        'label'           => esc_html__( 'Category For Blog Slider', 'eportfolio' ),
        'description'     => esc_html__( 'Select category to be shown on Blog Page or post Page slider section ', 'eportfolio' ),
        'section'         => 'blog_page_setting_options',
        'type'            => 'dropdown-taxonomies',
        'taxonomy'        => 'category',
		'priority'    	  => 10,
		'active_callback' => 'eportfolio_show_slider_on_blog',
    ) ) );


$wp_customize->add_setting('blog_page_slider_number',
	array(
		'default'           => $default['blog_page_slider_number'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_positive_integer',
	)
);
$wp_customize->add_control('blog_page_slider_number',
	array(
		'label'       => esc_html__('Select No Of Slider', 'eportfolio'),
        'description'     => esc_html__( 'Number of Slider to be shown the allowed range is 1 - 4', 'eportfolio' ),
		'section'     => 'blog_page_setting_options',
		'type'     => 'number',
		'priority' => 15,
		'input_attrs' => array('min' => 1, 'max' => 4, 'style' => 'width: 150px;'),
		'active_callback' => 'eportfolio_show_slider_on_blog',
	)
);

$wp_customize->add_setting( 'enable_blog_layout_switch',
	array(
		'default'           => $default['enable_blog_layout_switch'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_blog_layout_switch',
	array(
		'label'    => esc_html__( 'Enable Switch/toggle Option on Blog Layout', 'eportfolio' ),
		'section'  => 'blog_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 20,
	)
);

$wp_customize->add_setting( 'blog_layout_style',
	array(
		'default'           => $default['blog_layout_style'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'blog_layout_style',
	array(
		'label'    => esc_html__( 'Select Default Layout For Blog/Latest Post', 'eportfolio' ),
		'section'  => 'blog_page_setting_options',
		'choices'  => array(
                'list-post-layout' => esc_html__( 'List Post Layout', 'eportfolio' ),
                'grid-post-layout' => esc_html__( 'Grid/Masonry Layout', 'eportfolio' ),
		    ),
		'type'     => 'select',
		'priority' => 30,
		'active_callback' => 'eportfolio_enable_blog_layout_switch',
	)
);

$wp_customize->add_setting( 'blog_layout_grid_column',
	array(
		'default'           => $default['blog_layout_grid_column'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_select',
	)
);
$wp_customize->add_control( 'blog_layout_grid_column',
	array(
		'label'    => esc_html__( 'Select Number of Column in Grid view', 'eportfolio' ),
		'section'  => 'blog_page_setting_options',
		'choices'  => array(
                '3-column' => esc_html__( 'Three Column Grid', 'eportfolio' ),
                '2-column' => esc_html__( 'Two Column Grid', 'eportfolio' ),
		    ),
		'type'     => 'select',
		'priority' => 30,
		'active_callback' => 'eportfolio_enable_blog_layout_switch',
	)
);


// Portfolio Page Template  Settings.
$wp_customize->add_section( 'portfolio_page_setting_options',
	array(
		'title'      => esc_html__( 'Portfolio Page Template Settings', 'eportfolio' ),
		'priority'   => 10,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_page_template_panel',
	)
);

// Setting - enable_portfolio_widget_sidebar.
$wp_customize->add_setting( 'enable_portfolio_widget_sidebar',
	array(
		'default'           => $default['enable_portfolio_widget_sidebar'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_portfolio_widget_sidebar',
	array(
		'label'    => esc_html__( 'Enable Portfolio Template Widget Section', 'eportfolio' ),
        'description'     => esc_html__( 'After enabling please visit your widget.php and add widget on Portfolio Template Widget', 'eportfolio' ),
		'section'  => 'portfolio_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 10,
	)
);

// Setting - enable_portfolio_masonry_section.
$wp_customize->add_setting( 'enable_portfolio_masonry_section',
	array(
		'default'           => $default['enable_portfolio_masonry_section'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_portfolio_masonry_section',
	array(
		'label'    => esc_html__( 'Enable Masonry Section On Portfolio', 'eportfolio' ),
        'description'     => esc_html__( 'After enabling this masonry section will be shown on portfolio side', 'eportfolio' ),
		'section'  => 'portfolio_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 10,
	)
);

// Setting - enable_portfolio_page_title.
$wp_customize->add_setting( 'enable_portfolio_page_title',
	array(
		'default'           => $default['enable_portfolio_page_title'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_portfolio_page_title',
	array(
		'label'    => esc_html__( 'Enable Portfolio Title', 'eportfolio' ),
		'section'  => 'portfolio_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 10,
	)
);

// Setting - select_category_for_portfolio_section.
$wp_customize->add_setting( 'select_category_for_portfolio_section',
	array(
		'default'           => $default['select_category_for_portfolio_section'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint',
	)
);
$wp_customize->add_control( new Eportfolio_Dropdown_Taxonomies_Control( $wp_customize, 'select_category_for_portfolio_section',
	array(
        'label'           => esc_html__( 'Category For Portfolio Section', 'eportfolio' ),
        'description'     => esc_html__( 'Select category to be shown on Portfolio Page template - post list section ', 'eportfolio' ),
        'section'         => 'portfolio_page_setting_options',
        'type'            => 'dropdown-taxonomies',
        'taxonomy'        => 'category',
		'priority'    	  => 10,
    ) ) );


$wp_customize->add_setting('portfolio_section_post_number',
	array(
		'default'           => $default['portfolio_section_post_number'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_positive_integer',
	)
);
$wp_customize->add_control('portfolio_section_post_number',
	array(
		'label'       => esc_html__('Select No Of Post /. portfolio to show', 'eportfolio'),
        'description'     => esc_html__( 'Number of Slider to be shown the allowed range is 1 - 20', 'eportfolio' ),
		'section'     => 'portfolio_page_setting_options',
		'type'     => 'number',
		'priority' => 15,
		'input_attrs' => array('min' => 1, 'max' => 20, 'style' => 'width: 150px;'),
	)
);



// Photography Page Template  Settings.
$wp_customize->add_section( 'photography_page_setting_options',
	array(
		'title'      => esc_html__( 'Photography Page Template Settings', 'eportfolio' ),
		'priority'   => 20,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_page_template_panel',
	)
);

// Setting - enable_photography_slider_overlay.
$wp_customize->add_setting( 'enable_photography_slider_overlay',
	array(
		'default'           => $default['enable_photography_slider_overlay'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_photography_slider_overlay',
	array(
		'label'    => esc_html__( 'Enable Overlay on Slider', 'eportfolio' ),
		'section'  => 'photography_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 10,
	)
);

// Setting - drop down category for photography slider.
$wp_customize->add_setting( 'select_category_for_photography_slider',
	array(
		'default'           => $default['select_category_for_photography_slider'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint',
	)
);
$wp_customize->add_control( new Eportfolio_Dropdown_Taxonomies_Control( $wp_customize, 'select_category_for_photography_slider',
	array(
        'label'           => esc_html__( 'Category For Photography Page Template Slider', 'eportfolio' ),
        'description'     => esc_html__( 'Select category to be shown on photography Page template - post slider section ', 'eportfolio' ),
        'section'         => 'photography_page_setting_options',
        'type'            => 'dropdown-taxonomies',
        'taxonomy'        => 'category',
		'priority'    	  => 10,
    ) ) );


$wp_customize->add_setting('photography_page_slider_number',
	array(
		'default'           => $default['photography_page_slider_number'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_positive_integer',
	)
);
$wp_customize->add_control('photography_page_slider_number',
	array(
		'label'       => esc_html__('Select No Of Slider', 'eportfolio'),
        'description'     => esc_html__( 'Number of Slider to be shown the allowed range is 1 - 9', 'eportfolio' ),
		'section'     => 'photography_page_setting_options',
		'type'     => 'number',
		'priority' => 15,
		'input_attrs' => array('min' => 1, 'max' => 9, 'style' => 'width: 150px;'),
	)
);

// Setting - enable_background_on_text_details.
$wp_customize->add_setting( 'enable_background_on_text_details',
	array(
		'default'           => $default['enable_background_on_text_details'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'eportfolio_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'enable_background_on_text_details',
	array(
		'label'    => esc_html__( 'Enable Slider Text Background', 'eportfolio' ),
		'section'  => 'photography_page_setting_options',
		'type'     => 'checkbox',
		'priority' => 20,
	)
);
