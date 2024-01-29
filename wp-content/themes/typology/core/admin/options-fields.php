<?php

/* Branding */
Redux::setSection( $opt_name , array(
        'icon'      => ' el-icon-smiley',
        'title'     => esc_html__( 'Branding', 'typology' ),
        'desc'     => esc_html__( 'Personalize theme by adding your own images', 'typology' ),
        'fields'    => array(

            array(
                'id'        => 'logo',
                'type'      => 'media',
                'url'       => true,
                'title'     => esc_html__( 'Logo', 'typology' ),
                'subtitle'      => esc_html__( 'Upload your logo image here, or leave empty to show the website title instead.', 'typology' ),
                'default'   => typology_get_default_option( 'logo' ),
            ),

            array(
                'id'        => 'logo_retina',
                'type'      => 'media',
                'url'       => true,
                'title'     => esc_html__( 'Retina logo (2x)', 'typology' ),
                'subtitle'      => esc_html__( 'Optionally upload another logo for devices with retina displays. It should be double the size of your standard logo', 'typology' ),
                'default'   => typology_get_default_option( 'logo_retina' ),
            ),

            array(
                'id'        => 'logo_custom_url',
                'type'      => 'text',
                'title'     => esc_html__( 'Custom logo URL', 'typology' ),
                'subtitle'  => esc_html__( 'Optionally specify custom URL if you want logo to point out to some other page/website instead of your home page', 'typology' ),
                'default'   => typology_get_default_option( 'logo_custom_url' )
            )

        ) )
);


/* Stylings */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-brush',
        'title'     => esc_html__( 'Styling & Colors', 'typology' ),
        'desc'     => esc_html__( 'Styling and color settings', 'typology' ),
        'fields'    => array(

            array(
                'id'        => 'style',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Theme style', 'typology' ),
                'subtitle' => esc_html__( 'Choose general theme style', 'typology' ),
                'options'   => array(
                    'material' => array( 'title' => esc_html__( 'Material', 'typology' ),       'img' =>  get_parent_theme_file_uri( '/assets/img/admin/style_material.png' ) ),
                    'flat' => array( 'title' => esc_html__( 'Flat', 'typology' ),       'img' =>  get_parent_theme_file_uri( '/assets/img/admin/style_flat.png' ) ),
                ),
                'default'   => typology_get_default_option( 'style' ),
            ),

            array(
                'id' => 'color_header_bg',
                'type' => 'color',
                'title' => esc_html__( 'Header/cover background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to header and cover background', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_header_bg' ),
            ),

            array(
                'id' => 'cover_gradient',
                'type' => 'switch',
                'title' => esc_html__( 'Header/cover gradient', 'typology' ),
                'subtitle' => esc_html__( 'Enable cover gradient', 'typology' ),
                'default' => typology_get_default_option( 'cover_gradient' ),
            ),

            array(
                'id' => 'cover_gradient_color',
                'type' => 'color',
                'title' => esc_html__( 'Second cover background color', 'typology' ),
                'subtitle' => esc_html__( 'This color will be used to create gradient background', 'typology' ),
                'default'  => typology_get_default_option( 'cover_gradient_color' ),
                'transparent' => false,
                'required' => array( 'cover_gradient', '=', true )
            ),

            array(
                'id' => 'cover_gradient_orientation',
                'type' => 'select',
                'title' => esc_html__( 'Choose gradient orientation', 'typology' ),
                'subtitle' => esc_html__( 'Select your desired orientation (direction) for background gradient', 'typology' ),
                'transparent' => false,
                'options'  => array(
                    'to right top' => esc_html__( 'Left bottom to right top', 'typology' ),
                    'to right' => esc_html__( 'Left to right', 'typology' ),
                    'to right bottom' => esc_html__( 'Left top to right bottom', 'typology' ),
                    'to bottom' => esc_html__( 'Top to bottom', 'typology' ),
                    'to left bottom' => esc_html__( 'Right top to left bottom', 'typology' ),
                    'to left' => esc_html__( 'Right to left', 'typology' ),
                    'to left top' => esc_html__( 'Right bottom to left top', 'typology' ),
                    'to top' => esc_html__( 'Bottom to top', 'typology' ),
                    'circle' => esc_html__( 'Circle', 'typology' ),
                ),
                'default'  => typology_get_default_option( 'cover_gradient_orientation' ),
                'required' => array( 'cover_gradient', '=', true )
            ),

            array(
                'id' => 'cover_bg_media',
                'type' => 'radio',
                'title' => esc_html__( 'Cover background media', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload an image or a video as the default cover backgorund', 'typology' ),
                'options' => array(
                    'image' => 'Image',
                    'video' => 'Video'
                ),
                'default' => typology_get_default_option( 'cover_bg_media' ),
            ),

            array(
                'id' => 'cover_bg_img',
                'type' => 'media',
                'url'       => true,
                'title' => esc_html__( 'Cover background image', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload cover background image', 'typology' ),
                'default' => typology_get_default_option( 'cover_bg_img' ),
                'required' => array( 'cover_bg_media', '=', 'image' )
            ),

            array(
                'id' => 'cover_bg_video',
                'type' => 'media',
                'url'  => true,
                'mode' => false,
                'title' => esc_html__( 'Cover background video', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload cover background video', 'typology' ),
                'desc' => esc_html__( 'Note: preferred formats is .mp4', 'typology' ),
                'default' => typology_get_default_option( 'cover_bg_video' ),
                'required' => array( 'cover_bg_media', '=', 'video' )
            ),

            array(
                'id' => 'cover_bg_video_image',
                'type' => 'media',
                'url'  => true,
                'mode' => false,
                'title' => esc_html__( 'Video placeholder image', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload an image to display on mobile devices that don\'t support background video', 'typology' ),
                'default' => typology_get_default_option( 'cover_bg_video_image' ),
                'required' => array( 'cover_bg_media', '=', 'video' )
            ),

            array(
                'id'        => 'cover_bg_opacity',
                'type'      => 'slider',
                'title'     => esc_html__( 'Cover background color opacity', 'typology' ),
                'subtitle'  => esc_html__( 'If background image is uploaded, you can set background color opacity ', 'typology' ),
                'default' => typology_get_default_option( 'cover_bg_opacity' ),
                'resolution' => 0.1,
                'min' => 0,
                'step' => .1,
                'max' => 1,
                'display_value' => 'label'
            ),

            array(
                'id' => 'color_header_txt',
                'type' => 'color',
                'title' => esc_html__( 'Header/cover text color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to header and cover text', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_header_txt' ),
            ),


            array(
                'id' => 'color_body_bg',
                'type' => 'color',
                'title' => esc_html__( 'Body background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to body background (used only in "material" version)', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_body_bg' ),
                'required' => array( 'style', '=', 'material' )
            ),

            array(
                'id' => 'color_content_bg',
                'type' => 'color',
                'title' => esc_html__( 'Content background color', 'typology' ),
                'subtitle' => esc_html__( 'This is your main content background color', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_content_bg' ),
            ),


            array(
                'id' => 'color_content_h',
                'type' => 'color',
                'title' => esc_html__( 'Heading color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to post/page titles, widget titles, etc... ', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_content_h' ),
            ),

            array(
                'id' => 'color_content_txt',
                'type' => 'color',
                'title' => esc_html__( 'Text color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to standard text', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_content_txt' ),
            ),

            array(
                'id' => 'color_content_acc',
                'type' => 'color',
                'title' => esc_html__( 'Accent color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to links, buttons and some other special elements', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_content_acc' ),
            ),

            array(
                'id' => 'color_content_meta',
                'type' => 'color',
                'title' => esc_html__( 'Meta color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to miscellaneous elements like post meta data (author link, date, etc...)', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_content_meta' ),
            ),

            array(
                'id' => 'color_footer_bg',
                'type' => 'color',
                'title' => esc_html__( 'Footer background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer background', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_footer_bg' ),
            ),


            array(
                'id' => 'color_footer_txt',
                'type' => 'color',
                'title' => esc_html__( 'Footer text color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer text', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_footer_txt' ),
            ),

            array(
                'id' => 'color_footer_acc',
                'type' => 'color',
                'title' => esc_html__( 'Footer accent color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer links, buttons and some other special elements', 'typology' ),
                'transparent' => false,
                'default' => typology_get_default_option( 'color_footer_acc' ),
            ),


        ) )
);



/* Header */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-bookmark',
        'title'     => esc_html__( 'Header', 'typology' ),
        'desc'     => esc_html__( 'Modify and style your header', 'typology' ),
        'fields'    => array(

            array(
                'id'        => 'header_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Header layout', 'typology' ),
                'subtitle' => esc_html__( 'Choose a layout for your header', 'typology' ),
                'options'   => typology_get_header_layouts(),
                'default' => typology_get_default_option( 'header_layout' ),

            ),

            array(
                'id'        => 'header_elements',
                'type'      => 'checkbox',
                'multi'     => true,
                'title'     => esc_html__( 'Header elements', 'typology' ),
                'subtitle' => esc_html__( 'Check elements you want to display in header ', 'typology' ),
                'options'   => array(
                    'main-menu' => esc_html__( 'Main Navigation', 'typology' ),
                    'sidebar-button' => esc_html__( 'Sidebar (hamburger icon)', 'typology' ),
                    'search-dropdown' => esc_html__( 'Search dropdown', 'typology' ),
                    'social-menu-dropdown' => esc_html__( 'Social icons dropdown', 'typology' ),
                    'site-desc' => esc_html__( 'Site description', 'typology' ),
                ),
                'default' => typology_get_default_option( 'header_elements' ),
            ),

            array(
                'id' => 'header_height',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Header height', 'typology' ),
                'subtitle' => esc_html__( 'Specify height for your header/navigation area', 'typology' ),
                'desc' => esc_html__( 'Note: Height value is in px.', 'typology' ),
                'default' => typology_get_default_option( 'header_height' ),
                'validate' => 'numeric'
            ),


            array(
                'id'        => 'header_orientation',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Header orientation', 'typology' ),
                'subtitle' => esc_html__( 'Choose if header elements follow site content or browser width ', 'typology' ),
                'options'   => array(
                    'content' => array( 'title' => esc_html__( 'Site content', 'typology' ),       'img' =>  get_parent_theme_file_uri( '/assets/img/admin/header_content.png' ) ),
                    'wide' => array( 'title' => esc_html__( 'Browser (full width)', 'typology' ),       'img' =>  get_parent_theme_file_uri( '/assets/img/admin/header_wide.png' ) ),
                ),
                'default' => typology_get_default_option( 'header_orientation' ),

            ),

            array(
                'id'        => 'header_sticky',
                'type'      => 'switch',
                'title'     => esc_html__( 'Enable sticky header', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to enable sticky header', 'typology' ),
                'default' => typology_get_default_option( 'header_sticky' ),
            ),


        ) ) );



/* Footer */
Redux::setSection( $opt_name, array(
        'icon'   => 'el-icon-bookmark-empty',
        'title'  => esc_html__( 'Footer', 'typology' ),
        'desc'   => esc_html__( 'Manage the options for your footer area', 'typology' ),
        'fields' => array(

            array(
                'id'       => 'footer_layout',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Footer area layout', 'typology' ),
                'subtitle' => esc_html__( 'Choose a layout for your footer widgetized area', 'typology' ),
                'desc'     => wp_kses_post( sprintf( __( 'Note: Each column represents one Footer Sidebar in <a href="%s">Apperance -> Widgets</a> settings.', 'typology' ), admin_url( 'widgets.php' ) ) ),
                'options'  => typology_get_footer_layouts(),
                'default' => typology_get_default_option( 'footer_layout' ),
            ),

        ),
    )
);



/* Layout settings */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-th-large',
        'title'     => esc_html__( 'Post Layouts', 'typology' ),
        'heading' => false,
        'fields'    => array(
        ) )
);


/* Layout A */
Redux::setSection( $opt_name , array(
        'icon'      => '',
        'title'     => esc_html__( 'Layout A', 'typology' ),
        'heading' => false,
        'subsection' => true,
        'fields'    => array(

            array(
                'id'        => 'section_layout_a',
                'type'      => 'typology_section',
                'title'     => '<img src="'.esc_url( get_parent_theme_file_uri( '/assets/img/admin/layout_a.png' ) ).'"/>'.esc_html__( 'Layout A', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout A', 'typology' ),
                'indent'   => false
            ),

            array(
                'id'        => 'layout_a_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default' => typology_get_default_option( 'layout_a_dropcap' ),
            ),

            array(
                'id'        => 'layout_a_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default' => typology_get_default_option( 'layout_a_fimg' ),
            ),

            array(
                'id'        => 'layout_a_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_default_option( 'layout_a_meta' ),
            ),

            array(
                'id' => 'layout_a_content',
                'type' => 'radio',
                'title' => esc_html__( 'Content type', 'typology' ),
                'options' => array(
                    'excerpt' =>  esc_html__( 'Excerpt (automatically limit number of characters)', 'typology' ),
                    'content' =>  esc_html__( 'Full content (optionally split with "<--more-->" tag)', 'typology' ),
                ),
                'subtitle' => esc_html__( 'Choose content type', 'typology' ),
                'default' => typology_get_default_option( 'layout_a_content' ),
            ),

            array(
                'id' => 'layout_a_excerpt_limit',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Excerpt limit', 'typology' ),
                'subtitle' => esc_html__( 'Specify your excerpt limit', 'typology' ),
                'desc' => esc_html__( 'Note: Value represents number of characters', 'typology' ),
                'default' => typology_get_default_option( 'layout_a_excerpt_limit' ),
                'validate' => 'numeric',
                'required'  => array( 'layout_a_content', '=', 'excerpt' )
            ),

            array(
                'id'        => 'layout_a_buttons',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Buttons', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which buttons you want to display', 'typology' ),
                'options'   => typology_get_button_opts(),
                'default' => typology_get_default_option( 'layout_a_buttons' ),
            ),


        ) ) );

/* Layout B */
Redux::setSection( $opt_name , array(
        'icon'      => '',
        'title'     => esc_html__( 'Layout B', 'typology' ),
        'heading' => false,
        'subsection' => true,
        'fields'    => array(
            array(
                'id'        => 'section_layout_b',
                'type'      => 'typology_section',
                'title'     => '<img src="'.esc_url( get_parent_theme_file_uri( '/assets/img/admin/layout_b.png' ) ).'"/>'.esc_html__( 'Layout B', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout B', 'typology' ),
                'indent'   => false
            ),

            array(
                'id'        => 'layout_b_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default' => typology_get_default_option( 'layout_b_fimg' ),
            ),

            array(
                'id'        => 'layout_b_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_default_option( 'layout_b_meta' ),
            ),

            array(
                'id'        => 'layout_b_excerpt',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display text excerpt', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display excerpt', 'typology' ),
                'default' => typology_get_default_option( 'layout_b_excerpt' ),
            ),

            array(
                'id' => 'layout_b_excerpt_limit',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Excerpt limit', 'typology' ),
                'subtitle' => esc_html__( 'Specify your excerpt limit', 'typology' ),
                'desc' => esc_html__( 'Note: Value represents number of characters', 'typology' ),
                'default' => typology_get_default_option( 'layout_b_excerpt_limit' ),
                'validate' => 'numeric',
                'required'  => array( 'layout_b_excerpt', '=', true )
            ),

            array(
                'id'        => 'layout_b_buttons',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Buttons', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which buttons you want to display', 'typology' ),
                'options'   => typology_get_button_opts(),
                'default' => typology_get_default_option( 'layout_b_buttons' ),
            ),


        ) ) );


/* Layout C */
Redux::setSection( $opt_name , array(
        'icon'      => '',
        'title'     => esc_html__( 'Layout C', 'typology' ),
        'heading' => false,
        'subsection' => true,
        'fields'    => array(
            array(
                'id'        => 'section_layout_c',
                'type'      => 'typology_section',
                'title'     => '<img src="'.esc_url( get_parent_theme_file_uri( '/assets/img/admin/layout_c.png' ) ).'"/>'.esc_html__( 'Layout C', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout C', 'typology' ),
                'indent'   => false
            ),

            array(
                'id'        => 'layout_c_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default' => typology_get_default_option( 'layout_c_dropcap' ),
            ),

            array(
                'id'        => 'layout_c_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default' => typology_get_default_option( 'layout_c_fimg' ),
            ),

            array(
                'id'        => 'layout_c_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_default_option( 'layout_c_meta' ),
            ),


        ) ) );

/* Home Page */

if ( get_option( 'show_on_front' ) != 'page' ) {

    $info = array(
        'id' => 'home_setup_info',
        'type' => 'info',
        'style' => 'critical',
        'title' => esc_html__( 'Important note:', 'typology' ),
        'subtitle' => wp_kses_post( sprintf( __( 'Your front page is currently set to display <strong>"latest posts"</strong>. In order to use these options, you need to set your front page option as <strong>"static page"</strong> inside <a href="%s" target="_blank">Settings->Reading</a>.', 'typology' ), admin_url( 'options-reading.php' ) ) ),
    );
} else {

    $info = array();
}


Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-home',
        'title'     => esc_html__( 'Home Page', 'typology' ),
        'heading' => false,
        'fields'    => array(

            $info,

            array(
                'id'        => 'section_front_page_cover',
                'type'      => 'section',
                'title'     => esc_html__( 'Cover area', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for home page cover area', 'typology' ),
                'indent'    => false
            ),

            array(
                'id' => 'front_page_cover',
                'type' => 'radio',
                'title' => esc_html__( 'Cover area displays', 'typology' ),
                'subtitle' => esc_html__( 'Choose what to display in cover area', 'typology' ),
                'options'   => array(
                    'posts' => esc_html__( 'Posts (slider)', 'typology' ),
                    'bloginfo' => esc_html__( 'Site title & description (tagline)', 'typology' ),
                    'content' => esc_html__( 'Page content', 'typology' ),
                    'title' => esc_html__( 'Page title', 'typology' ),
                    '0' => esc_html__( 'None (do not display cover)', 'typology' ),
                ),
                'default' => typology_get_default_option( 'front_page_cover' ),
            ),



            array(
                'id' => 'front_page_cover_posts',
                'type' => 'radio',
                'title' => esc_html__( 'Cover area chooses from', 'typology' ),
                'subtitle' => esc_html__( 'Choose which posts to display', 'typology' ),
                'options'   => typology_get_posts_order_opts(),
                'default' => typology_get_default_option( 'front_page_cover_posts' ),
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'front_page_cover_posts_cat',
                'type'      => 'select',
                'data'      => 'categories',
                'multi'     => true,
                'title'     => esc_html__( 'In category', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display posts from one or more specific categories', 'typology' ),
                'desc'      => esc_html__( 'Note: Leave empty for "all categories"', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_posts_cat' ),
                'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) )

            ),

            array(
                'id'        => 'front_page_cover_posts_tag',
                'type'      => 'select',
                'data'      => 'tags',
                'multi'     => true,
                'title'     => esc_html__( 'Tagged with', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display posts that are tagged with one or more specific tags', 'typology' ),
                'desc'      => esc_html__( 'Note: Leave empty for "all tags"', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_posts_tag' ),
                'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) )
            ),


            array(
                'id'        => 'front_page_cover_posts_manual',
                'type'      => 'text',
                'title'     => esc_html__( 'Pick posts manually ', 'typology' ),
                'subtitle'  => esc_html__( 'Use this option to manually specify posts by their IDs', 'typology' ),
                'desc'      => esc_html__( 'Note: Separate post IDs by comma, i.e. 43,56,26,187', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_posts_manual' ),
                'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '=', 'manual' ) )
            ),

            array(
                'id'        => 'front_page_cover_posts_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of post to display', 'typology' ),
                'subtitle'  => esc_html__( 'Choose how many posts to display', 'typology' ),
                'required'  => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) ),
                'validate'  => 'numeric',
                'default' => typology_get_default_option( 'front_page_cover_posts_num' ),
            ),

            array(
                'id'        => 'front_page_cover_posts_unique',
                'type'      => 'switch',
                'title'     => esc_html__( 'Unique posts', 'typology' ),
                'subtitle'  => esc_html__( 'If you check this option, cover posts will be excluded from the main post section below', 'typology' ),
                'required' => array( 'front_page_cover', '=', 'posts' ),
                'default' => typology_get_default_option( 'front_page_cover_posts_unique' ),
            ),

            array(
                'id'        => 'front_page_cover_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_dropcap' ),
                'required' => array( 'front_page_cover', '!=', '0' )
            ),



            array(
                'id'        => 'layout_cover_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Display meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display for post in cover area', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_default_option( 'layout_cover_meta' ),
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'layout_cover_buttons',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Buttons', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which buttons you want to display for post in cover area', 'typology' ),
                'options'   => typology_get_button_opts(),
                'default' => typology_get_default_option( 'layout_cover_buttons' ),
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'front_page_cover_posts_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display cover post featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display post featured images as cover background', 'typology' ),
                'required' => array( 'front_page_cover', '=', 'posts' ),
                'default' => typology_get_default_option( 'front_page_cover_posts_fimg' ),
            ),

            array(
                'id' => 'front_page_cover_autoplay',
                'type' => 'switch',
                'title' => esc_html__( 'Autoplay (rotate)', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to auto rotate items in cover area slider', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_autoplay' ),
                'required'  => array( 'front_page_cover', '=', 'posts' )
            ),
            array(
                'id' => 'front_page_cover_autoplay_time',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Autoplay time', 'typology' ),
                'subtitle' => esc_html__( 'Specify autoplay time per slide', 'typology' ),
                'desc' => esc_html__( 'Note: Please specify number in seconds', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_autoplay_time' ),
                'validate' => 'numeric',
                'required'  => array( 'front_page_cover_autoplay', '=', true )
            ),

            array(
                'id' => 'front_page_cover_on_first_page',
                'type' => 'checkbox',
                'title' => esc_html__( 'Enable Cover section on first page only', 'typology' ),
                'subtitle' => esc_html__( 'Cover section will not be displyed on page 2,3,4, etc...', 'typology' ),
                'default' => typology_get_default_option( 'front_page_cover_on_first_page' ),
                'required' => array( 'front_page_cover', '!=', '0' )
            ),

            array(
                'id'        => 'section_front_page_intro',
                'type'      => 'section',
                'title'     => esc_html__( 'Intro section', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for home page introduction section', 'typology' ),
                'indent'    => false
            ),

            array(
                'id' => 'front_page_intro',
                'type' => 'radio',
                'title' => esc_html__( 'Intro section displays', 'typology' ),
                'subtitle' => esc_html__( 'Choose what to display in intro section', 'typology' ),
                'options'   => array(
                    'content' => esc_html__( 'Page content', 'typology' ),
                    'title_content' => esc_html__( 'Page title & page content', 'typology' ),
                    '0' => esc_html__( 'None (do not display intro section)', 'typology' ),
                ),
                'default' => typology_get_default_option( 'front_page_intro' ),
            ),

            array(
                'id' => 'front_page_intro_on_first_page',
                'type' => 'checkbox',
                'title' => esc_html__( 'Enable Intro section on first page only', 'typology' ),
                'subtitle' => esc_html__( 'Intro section will not be displyed on page 2,3,4, etc...', 'typology' ),
                'default' => typology_get_default_option( 'front_page_intro_on_first_page' ),
                'required' => array( 'front_page_intro', '!=', '0' )

            ),

            array(
                'id'        => 'section_front_page_posts',
                'type'      => 'section',
                'title'     => esc_html__( 'Posts section', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for home page posts section', 'typology' ),
                'indent'    => false
            ),

            array(
                'id' => 'front_page_posts',
                'type' => 'radio',
                'title' => esc_html__( 'Posts section chooses from', 'typology' ),
                'subtitle' => esc_html__( 'Choose which posts to display', 'typology' ),
                'options'   => typology_get_posts_order_opts( true ),
                'default' => typology_get_default_option( 'front_page_posts' ),
            ),

            array(
                'id'        => 'front_page_posts_cat',
                'type'      => 'select',
                'data'      => 'categories',
                'multi'     => true,
                'title'     => esc_html__( 'In category', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display posts from one or more specific categories', 'typology' ),
                'desc'      => esc_html__( 'Note: Leave empty for "all categories"', 'typology' ),
                'default' => typology_get_default_option( 'front_page_posts_cat' ),
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),

            array(
                'id'        => 'front_page_posts_tag',
                'type'      => 'select',
                'data'      => 'tags',
                'multi'     => true,
                'title'     => esc_html__( 'Tagged with', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display posts that are tagged with one or more specific tags', 'typology' ),
                'desc'      => esc_html__( 'Note: Leave empty for "all tags"', 'typology' ),
                'default' => typology_get_default_option( 'front_page_posts_tag' ),
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),


            array(
                'id'        => 'front_page_posts_manual',
                'type'      => 'text',
                'title'     => esc_html__( 'Pick posts manually ', 'typology' ),
                'subtitle'  => esc_html__( 'Use this option to manually specify posts by their IDs', 'typology' ),
                'desc'      => esc_html__( 'Note: Separate post IDs by comma, i.e. 43,56,26,187', 'typology' ),
                'default' => typology_get_default_option( 'front_page_posts_manual' ),
                'required' => array( 'front_page_posts', '=', 'manual' )
            ),

            array(
                'id'        => 'front_page_posts_ppp',
                'type'      => 'radio',
                'title'     => esc_html__( 'Posts per page', 'typology' ),
                'subtitle'  => esc_html__( 'Choose how many post you want to display', 'typology' ),
                'options'   => array(
                    'inherit' => wp_kses_post( sprintf( __( 'Inherit from global option in <a href="%s">Settings->Reading</a>', 'typology' ), admin_url( 'options-reading.php' ) ) ),
                    'custom' => esc_html__( 'Custom number', 'typology' )
                ),
                'default' => typology_get_default_option( 'front_page_posts_ppp' ),
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),



            array(
                'id'        => 'front_page_posts_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default' => typology_get_default_option( 'front_page_posts_num' ),
                'required'  => array( 'front_page_posts_ppp', '=', 'custom' ),
                'validate'  => 'numeric'
            ),

            array(
                'id'        => 'front_page_posts_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a layout for home page posts', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'required'  => array( 'front_page_posts', '!=', '0' ),
                'default' => typology_get_default_option( 'front_page_posts_layout' ),
            ),

            array(
                'id'        => 'front_page_posts_pagination',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Pagination', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a pagination for home page posts', 'typology' ),
                'options'   => typology_get_pagination_layouts(),
                'required'  => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) ),
                'default' => typology_get_default_option( 'front_page_posts_pagination' ),
            ),

        ) )
);


/* Archives */
Redux::setSection( $opt_name ,  array(
        'icon'      => 'el-icon-file-edit',
        'title'     => esc_html__( 'Archive Templates', 'typology' ),
        'desc'     => esc_html__( 'Manage settings for your archive templates', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'archive_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on archive templates', 'typology' ),
                'default' => typology_get_default_option( 'archive_cover' ),
            ),

            array(
                'id'        => 'archive_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap in cover', 'typology' ),
                'subtitle'  => esc_html__( 'If cover is used, dropcap will be displayed', 'typology' ),
                'default' => typology_get_default_option( 'archive_dropcap' ),
            ),

            array(
                'id' => 'archive_description',
                'type' => 'switch',
                'title' => esc_html__( 'Display archive description', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you want to display category/tag/author description', 'typology' ),
                'default' => typology_get_default_option( 'archive_description' ),
            ),

            array(
                'id'        => 'archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Main layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default' => typology_get_default_option( 'archive_layout' ),
            ),


            array(
                'id'        => 'archive_ppp',
                'type'      => 'radio',
                'title'     => esc_html__( 'Posts per page', 'typology' ),
                'subtitle'  => esc_html__( 'Choose how many posts per page you want to display', 'typology' ),
                'options'   => array(
                    'inherit' => wp_kses( sprintf( __( 'Inherit from global option in <a href="%s">Settings->Reading</a>', 'typology' ), admin_url( 'options-reading.php' ) ), wp_kses_allowed_html( 'post' ) ),
                    'custom' => esc_html__( 'Custom number', 'typology' )
                ),
                'default' => typology_get_default_option( 'archive_ppp' ),
            ),

            array(
                'id'        => 'archive_ppp_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default' => typology_get_default_option( 'archive_ppp_num' ),
                'required'  => array( 'archive_ppp', '=', 'custom' ),
                'validate'  => 'numeric'
            ),

            array(
                'id'        => 'archive_pagination',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Pagination', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a pagination type for archive templates', 'typology' ),
                'options'   => typology_get_pagination_layouts(),
                'default' => typology_get_default_option( 'archive_pagination' ),
            ),

        ) )
);


/* Category */
Redux::setSection( $opt_name ,  array(
        'title'      => esc_html__( 'Category Templates', 'typology' ),
        'desc'       => esc_html__( 'Manage settings for your category templates', 'typology' ),
        'subsection' => true,
        'fields'     => array(

            array(
                'id'      => 'category_settings_type',
                'type'    => 'radio',
                'title'   => esc_html__( 'Settings', 'typology' ),
                'options' => array(
                    'inherit' => esc_html__( 'Inherit from global archive options', 'typology' ),
                    'custom'  => esc_html__( 'Customize', 'typology' ),
                ),
                'default' => typology_get_default_option( 'category_settings_type' ),
            ),

            array(
                'id'       => 'category_cover',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on category templates', 'typology' ),
                'default' => typology_get_default_option( 'category_cover' ),
                'required' => array( 'category_settings_type', '=', 'custom' ),
            ),

            array(
                'id'       => 'category_layout',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Main layout', 'typology' ),
                'subtitle' => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'  => typology_get_main_layouts(),
                'default' => typology_get_default_option( 'category_layout' ),
                'required' => array( 'category_settings_type', '=', 'custom' ),
            ),


            array(
                'id'       => 'category_ppp',
                'type'     => 'radio',
                'title'    => esc_html__( 'Posts per page', 'typology' ),
                'subtitle' => esc_html__( 'Choose how many posts per page you want to display', 'typology' ),
                'options'  => array(
                    'inherit' => wp_kses( sprintf( __( 'Inherit from global option in <a href="%s">Settings->Reading</a>', 'typology' ), admin_url( 'options-reading.php' ) ), wp_kses_allowed_html( 'post' ) ),
                    'custom'  => esc_html__( 'Custom number', 'typology' ),
                ),
                'default' => typology_get_default_option( 'category_ppp' ),
                'required' => array( 'category_settings_type', '=', 'custom' ),
            ),

            array(
                'id'       => 'category_ppp_num',
                'type'     => 'text',
                'class'    => 'small-text',
                'title'    => esc_html__( 'Number of posts per page', 'typology' ),
                'default' => typology_get_default_option( 'category_ppp_num' ),
                'validate' => 'numeric',
                'required' => array(
                    array( 'category_settings_type', '=', 'custom' ),
                    array( 'category_ppp', '=', 'custom' ),
                ),
            ),

            array(
                'id'       => 'category_pagination',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Pagination', 'typology' ),
                'subtitle' => esc_html__( 'Choose a pagination type for category templates', 'typology' ),
                'options'  => typology_get_pagination_layouts(),
                'default' => typology_get_default_option( 'category_pagination' ),
                'required' => array( 'category_settings_type', '=', 'custom' ),
            ),

        ),
    ) );

/* Tag */
Redux::setSection( $opt_name ,  array(
        'title'     => esc_html__( 'Tag Templates', 'typology' ),
        'desc'     => esc_html__( 'Manage settings for your tag templates', 'typology' ),
        'subsection' => true,
        'fields'    => array(

            array(
                'id'      => 'tag_settings_type',
                'type'    => 'radio',
                'title'   => esc_html__( 'Settings', 'typology' ),
                'options' => array(
                    'inherit' => esc_html__( 'Inherit from global archive options', 'typology' ),
                    'custom'  => esc_html__( 'Customize', 'typology' ),
                ),
                'default' => typology_get_default_option( 'tag_settings_type' ),

            ),

            array(
                'id' => 'tag_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on tag templates', 'typology' ),
                'default' => typology_get_default_option( 'tag_cover' ),
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'tag_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Main layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default' => typology_get_default_option( 'tag_layout' ),
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),


            array(
                'id'        => 'tag_ppp',
                'type'      => 'radio',
                'title'     => esc_html__( 'Posts per page', 'typology' ),
                'subtitle'  => esc_html__( 'Choose how many posts per page you want to display', 'typology' ),
                'options'   => array(
                    'inherit' => wp_kses( sprintf( __( 'Inherit from global option in <a href="%s">Settings->Reading</a>', 'typology' ), admin_url( 'options-reading.php' ) ), wp_kses_allowed_html( 'post' ) ),
                    'custom' => esc_html__( 'Custom number', 'typology' )
                ),
                'default' => typology_get_default_option( 'tag_ppp' ),
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'tag_ppp_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default' => typology_get_default_option( 'tag_ppp_num' ),
                'validate'  => 'numeric',
                'required' => array(
                    array( 'tag_ppp', '=', 'custom' ),
                    array( 'tag_settings_type', '=', 'custom' )
                ),
            ),

            array(
                'id'        => 'tag_pagination',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Pagination', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a pagination type for tag templates', 'typology' ),
                'options'   => typology_get_pagination_layouts(),
                'default' => typology_get_default_option( 'tag_pagination' ),
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),

        ) )
);

/* Author */
Redux::setSection( $opt_name ,  array(
        'title'     => esc_html__( 'Author Templates', 'typology' ),
        'desc'     => esc_html__( 'Manage settings for your author templates', 'typology' ),
        'subsection' => true,
        'fields'    => array(

            array(
                'id'      => 'author_settings_type',
                'type'    => 'radio',
                'title'   => esc_html__( 'Settings', 'typology' ),
                'options' => array(
                    'inherit' => esc_html__( 'Inherit from global archive options', 'typology' ),
                    'custom'  => esc_html__( 'Customize', 'typology' ),
                ),
                'default' => typology_get_default_option( 'author_settings_type' ),
            ),

            array(
                'id' => 'use_author_image',
                'type' => 'switch',
                'title' => esc_html__( 'Display author avatar on author archive page', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you want to display author avatar/image on author archive page', 'typology' ),
                'default' => typology_get_default_option( 'use_author_image' ),
            ),

            array(
                'id' => 'author_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on author templates', 'typology' ),
                'default' => typology_get_default_option( 'author_cover' ),
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'author_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Main layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default' => typology_get_default_option( 'author_layout' ),
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),


            array(
                'id'        => 'author_ppp',
                'type'      => 'radio',
                'title'     => esc_html__( 'Posts per page', 'typology' ),
                'subtitle'  => esc_html__( 'Choose how many posts per page you want to display', 'typology' ),
                'options'   => array(
                    'inherit' => wp_kses( sprintf( __( 'Inherit from global option in <a href="%s">Settings->Reading</a>', 'typology' ), admin_url( 'options-reading.php' ) ), wp_kses_allowed_html( 'post' ) ),
                    'custom' => esc_html__( 'Custom number', 'typology' )
                ),
                'default' => typology_get_default_option( 'author_ppp' ),
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'author_ppp_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default' => typology_get_default_option( 'author_ppp_num' ),
                'validate'  => 'numeric',
                'required'  => array(
                    array( 'author_settings_type', '=', 'custom' ),
                    array( 'author_ppp', '=', 'custom' )
                ),
            ),

            array(
                'id'        => 'author_pagination',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Pagination', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a pagination type for author templates', 'typology' ),
                'options'   => typology_get_pagination_layouts(),
                'default' => typology_get_default_option( 'author_pagination' ),
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),
        ) )
);



/* Single Post */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-pencil',
        'title'     => esc_html__( 'Single Post', 'typology' ),
        'desc'     => esc_html__( 'Manage settings for your single posts', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'single_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on single post template', 'typology' ),
                'default' => typology_get_default_option( 'single_cover' ),
            ),

            array(
                'id'        => 'single_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default' => typology_get_default_option( 'single_dropcap' ),
            ),

            array(
                'id'        => 'layout_single_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_default_option( 'layout_single_meta' ),
            ),

            array(
                'id' => 'single_fimg',
                'type' => 'radio',
                'title' => esc_html__( 'Display featured image', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the featured image', 'typology' ),
                'options'   => array(
                    'cover' => esc_html__( 'As the post cover background', 'typology' ),
                    'content' => esc_html__( 'Inside the post content', 'typology' ),
                    'none' => esc_html__( 'Do not display', 'typology' ),
                ),
                'default' => typology_get_default_option( 'single_fimg' ),
            ),

            array(
                'id' => 'single_fimg_cap',
                'type' => 'switch',
                'title' => esc_html__( 'Display featured image caption', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the caption of the featured image', 'typology' ),
                'default' => typology_get_default_option( 'single_fimg_cap' ),
                'required'  => array( 'single_fimg', '=', 'content' )
            ),

            array(
                'id' => 'single_share',
                'type' => 'switch',
                'title' => esc_html__( 'Display share buttons', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display share buttons', 'typology' ),
                'desc' => !function_exists( 'meks_ess_share' ) ? wp_kses_post( sprintf( __( 'Note: <a href="%s">Meks Easy Social Share plugin</a> must be activated to use this option.', 'typology' ),  admin_url( 'themes.php?page=install-required-plugins' ) ) ) : '',
                'default' => typology_get_default_option( 'single_share' ),

            ),

            array(
                'id' => 'single_share_options',
                'type' => 'radio',
                'title' => esc_html__( 'Display share options', 'typology' ),
                'subtitle' => esc_html__( 'Select where you like to display share buttons', 'typology' ),
                'desc' => !function_exists( 'meks_ess_share' ) ? wp_kses_post( sprintf( __( 'Note: <a href="%s">Meks Easy Social Share plugin</a> must be activated to use this option.', 'typology' ),  admin_url( 'themes.php?page=install-required-plugins' ) ) ) : '',
                'options'  => array(
                    'above' => esc_html__( 'Above content', 'typology' ),
                    'below' => esc_html__( 'Below content', 'typology' ),
                    'above_below' => esc_html__( 'Above and below content', 'typology' ),
                ),
                'default' => typology_get_default_option( 'single_share_options' ),
                'required'  => array( 'single_share', '=', true )
            ),

            array(
                'id' => 'single_tags',
                'type' => 'switch',
                'title' => esc_html__( 'Display tags', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display tags', 'typology' ),
                'default' => typology_get_default_option( 'single_tags' ),
            ),

            array(
                'id' => 'single_author',
                'type' => 'switch',
                'title' => esc_html__( 'Display author area', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the author area', 'typology' ),
                'default' => typology_get_default_option( 'single_author' ),
            ),

            array(
                'id' => 'single_sticky_bottom_bar',
                'type' => 'switch',
                'title' => esc_html__( 'Enable sticky bottom bar', 'typology' ),
                'subtitle' => esc_html__( 'This bottom bar displays post meta data and previous/next post navigation. It will appear while you scroll through single posts', 'typology' ),
                'default' => typology_get_default_option( 'single_sticky_bottom_bar' ),
            ),

            array(
                'id' => 'single_prev_next_in_same_term',
                'type' => 'switch',
                'title' => esc_html__( 'Previous/next post in same category', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option to display previous and next post in the same category.', 'typology' ),
                'default' => typology_get_default_option( 'single_prev_next_in_same_term' ),
            ),

            array(
                'id' => 'single_related',
                'type' => 'switch',
                'title' => esc_html__( 'Display "related" posts', 'typology' ),
                'subtitle' => esc_html__( 'Choose if you want to display related posts section below single post', 'typology' ),
                'default' => typology_get_default_option( 'single_related' ),
            ),

            array(
                'id'        => 'related_type',
                'type'      => 'radio',
                'title'     => esc_html__( 'Related area chooses from posts', 'typology' ),
                'options'   => array(
                    'cat' => esc_html__( 'Located in the same category', 'typology' ),
                    'tag' => esc_html__( 'Tagged with at least one same tag', 'typology' ),
                    'cat_or_tag' => esc_html__( 'Located in the same category OR tagged with a same tag', 'typology' ),
                    'cat_and_tag' => esc_html__( 'Located in the same category AND tagged with a same tag', 'typology' ),
                    'author' => esc_html__( 'By the same author', 'typology' ),
                    '0' => esc_html__( 'All posts', 'typology' )
                ),
                'default' => typology_get_default_option( 'related_type' ),
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_order',
                'type'      => 'radio',
                'title'     => esc_html__( 'Related posts are ordered by', 'typology' ),
                'options'   => typology_get_post_related_order_opts(),
                'default' => typology_get_default_option( 'related_order' ),
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_limit',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Related area posts number limit', 'typology' ),
                'default' => typology_get_default_option( 'related_limit' ),
                'validate'  => 'numeric',
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Related posts layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a layout for related posts', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default' => typology_get_default_option( 'related_layout' ),
                'required'  => array( 'single_related', '=', true ),
            ),

        ) )
);


/* Page */
Redux::setSection( $opt_name ,  array(
        'icon'      => 'el-icon-file-edit',
        'title'     => esc_html__( 'Page', 'typology' ),
        'desc'     => esc_html__( 'Manage default settings for your pages', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'page_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on pages', 'typology' ),
                'default' => typology_get_default_option( 'page_cover' ),
            ),

            array(
                'id'        => 'page_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default' => typology_get_default_option( 'page_dropcap' ),
            ),


            array(
                'id' => 'page_fimg',
                'type' => 'radio',
                'title' => esc_html__( 'Display featured image', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the featured image', 'typology' ),
                'options'   => array(
                    'cover' => esc_html__( 'As the post cover background', 'typology' ),
                    'content' => esc_html__( 'Inside the post content', 'typology' ),
                    'none' => esc_html__( 'Do not display', 'typology' ),
                ),
                'default' => typology_get_default_option( 'page_fimg' ),

            ),

        ) )
);


/* Typography */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-fontsize',
        'title'     => esc_html__( 'Typography', 'typology' ),
        'desc'     => esc_html__( 'Manage fonts and typography settings', 'typology' ),
        'fields'    => array(

            array(
                'id'          => 'main_font',
                'type'        => 'typography',
                'title'       => esc_html__( 'Main text font', 'typology' ),
                'google'      => true,
                'font-backup' => false,
                'font-size' => false,
                'color' => false,
                'line-height' => false,
                'text-align' => false,
                'units'       =>'px',
                'letter-spacing' => true,
                'subsets' => false,
                'subtitle'    => esc_html__( 'This is your main font, used for standard text', 'typology' ),
                'default' => typology_get_default_option( 'main_font' ),
                'preview' => array(
                    'always_display' => true,
                    'font-size' => '16px',
                    'line-height' => '26px',
                    'text' => 'This is a font used for your main content on the website. Here at Meks, we believe that readability is a very important part of any WordPress theme. This is an example of how a simple paragraph of text will look like on your website.'
                )
            ),

            array(
                'id'          => 'h_font',
                'type'        => 'typography',
                'title'       => esc_html__( 'Headings font', 'typology' ),
                'google'      => true,
                'font-backup' => false,
                'font-size' => false,
                'color' => false,
                'line-height' => false,
                'text-align' => false,
                'units'       =>'px',
                'letter-spacing' => true,
                'subsets' => false,
                'subtitle'    => esc_html__( 'This is a font used for titles and headings', 'typology' ),
                'default' => typology_get_default_option( 'h_font' ),
                'preview' => array(
                    'always_display' => true,
                    'font-size' => '35px',
                    'line-height' => '50px',
                    'text' => 'THERE IS NO GOOD BLOG WITHOUT GREAT READABILITY'
                )

            ),

            array(
                'id'          => 'nav_font',
                'type'        => 'typography',
                'title'       => esc_html__( 'Navigation font', 'typology' ),
                'google'      => true,
                'font-backup' => false,
                'font-size' => false,
                'color' => false,
                'line-height' => false,
                'text-align' => false,
                'units'       =>'px',
                'letter-spacing' => true,
                'subsets' => false,
                'subtitle'    => esc_html__( 'This is a font used for main website navigation', 'typology' ),
                'default' => typology_get_default_option( 'nav_font' ),
                'preview' => array(
                    'always_display' => true,
                    'font-size' => '11px',
                    'text' => 'HOME &nbsp;&nbsp;ABOUT &nbsp;&nbsp;BLOG &nbsp;&nbsp;CONTACT'
                )

            ),

            array(
                'id'          => 'finetune',
                'type'        => 'section',
                'indent' => false,
                'title'       => esc_html__( 'Fine-tune typography', 'typology' ),
                'subtitle'    => esc_html__( 'Advanced options to adjust font sizes', 'typology' )
            ),


            array(
                'id'       => 'font_size_p',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Main text font size', 'typology' ),
                'subtitle' => esc_html__( 'This is your default text font size', 'typology' ),
                'default' => typology_get_default_option( 'font_size_p' ),
                'min'      => '14',
                'step'     => '1',
                'max'      => '22',
            ),

            array(
                'id'       => 'font_size_nav',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Navigation font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to main website navigation', 'typology' ),
                'default' => typology_get_default_option( 'font_size_nav' ),
                'min'      => '10',
                'step'     => '1',
                'max'      => '20',
            ),


            array(
                'id'       => 'font_size_small',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Small text (widget) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to widgets and some special elements', 'typology' ),
                'default' => typology_get_default_option( 'font_size_small' ),
                'min'      => '12',
                'step'     => '1',
                'max'      => '20',
            ),

            array(
                'id'       => 'font_size_meta',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Meta text font size ', 'typology' ),
                'subtitle' => esc_html__( 'Applies to meta items like author link, date, category link, etc...', 'typology' ),
                'default' => typology_get_default_option( 'font_size_meta' ),
                'min'      => '10',
                'step'     => '1',
                'max'      => '18',
            ),

            array(
                'id'       => 'font_size_cover',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Cover title font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to cover titles', 'typology' ),
                'default' => typology_get_default_option( 'font_size_cover' ),
                'min'      => '50',
                'step'     => '1',
                'max'      => '80',
            ),

            array(
                'id'       => 'font_size_cover_dropcap',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Cover dropcap font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to cover background dropcap letter', 'typology' ),
                'default' => typology_get_default_option( 'font_size_cover_dropcap' ),
                'min'      => '400',
                'step'     => '1',
                'max'      => '800',
            ),

            array(
                'id'       => 'font_size_dropcap',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Dropcap font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to dropcap letter on post/page titles', 'typology' ),
                'default' => typology_get_default_option( 'font_size_dropcap' ),
                'min'      => '150',
                'step'     => '1',
                'max'      => '400',
            ),

            array(
                'id'       => 'font_size_h1',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H1 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H1 elements and single post/page titles', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h1' ),
                'min'      => '30',
                'step'     => '1',
                'max'      => '60',
            ),

            array(
                'id'       => 'font_size_h2',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H2 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H2 elements', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h2' ),
                'min'      => '30',
                'step'     => '1',
                'max'      => '55',
            ),

            array(
                'id'       => 'font_size_h3',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H3 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H3 elements', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h3' ),
                'min'      => '25',
                'step'     => '1',
                'max'      => '45',
            ),

            array(
                'id'       => 'font_size_h4',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H4 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H4 elements', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h4' ),
                'min'      => '20',
                'step'     => '1',
                'max'      => '40',
            ),

            array(
                'id'       => 'font_size_h5',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H5 (widget title) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H5 elements and widget titles', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h5' ),
                'min'      => '14',
                'step'     => '1',
                'max'      => '24',
            ),

            array(
                'id'       => 'font_size_h6',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H6 (section title) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H6 elements and section titles', 'typology' ),
                'default' => typology_get_default_option( 'font_size_h6' ),
                'min'      => '14',
                'step'     => '1',
                'max'      => '22',
            ),

            array(
                'id' => 'uppercase',
                'type' => 'checkbox',
                'multi' => true,
                'title' => esc_html__( 'Uppercase text', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to show CAPITAL LETTERS for specific elements', 'typology' ),
                'options' => array(
                    '.site-title' => esc_html__( 'Site title', 'typology' ),
                    '.typology-site-description' => esc_html__( 'Site description', 'typology' ),
                    '.typology-nav' => esc_html__( 'Main navigation', 'typology' ),
                    'h1, h2, h3, h4, h5, h6, .wp-block-cover-text, .wp-block-cover-image-text' => esc_html__( 'H elements', 'typology' ),
                    '.section-title' => esc_html__( 'Section titles', 'typology' ),
                    '.widget-title' => esc_html__( 'Widget titles', 'typology' ),
                    '.meta-item' => esc_html__( 'Post meta data', 'typology' ),
                    '.typology-button' => esc_html__( 'Buttons', 'typology' )
                ),
                'default' => typology_get_default_option( 'uppercase' ),
            ),

            array(
                'id'        => 'content-paragraph-width',
                'type'      => 'slider',
                'title'     => esc_html__( 'Content paragraph width', 'typology' ),
                'subtitle'  => esc_html__( 'Width of paragraph will be applied to single post, page and Layout A', 'typology' ),
                'default' => typology_get_default_option( 'content-paragraph-width' ),
                'min'       => 600,
                'step'      => 5,
                'max'       => 800,
                'display_value' => 'label'
            ),

        ) )
);



/* Ads */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-usd',
        'title'     => esc_html__( 'Ads', 'typology' ),
        'desc'     => esc_html__( 'Use these options to fill your ad slots. Both image and JavaScript related ads are allowed.', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'ad_top',
                'type' => 'editor',
                'title' => esc_html__( 'Top', 'typology' ),
                'subtitle' => esc_html__( 'This ad will be displayed above the content', 'typology' ),
                'default' => typology_get_default_option( 'ad_top' ),
                'desc' => esc_html__( 'Note: If you want to paste an HTML or a JavaScript code, use "text" mode in editor', 'typology' ),
                'args'   => array(
                    'textarea_rows'    => 5,
                    'default_editor' => 'html'
                )
            ),

            array(
                'id' => 'ad_bottom',
                'type' => 'editor',
                'title' => esc_html__( 'Bottom', 'typology' ),
                'subtitle' => esc_html__( 'This ad will be displayed below the content', 'typology' ),
                'default' => typology_get_default_option( 'ad_bottom' ),
                'desc' => esc_html__( 'Note: If you want to paste an HTML or a JavaScript code, use "text" mode in editor', 'typology' ),
                'args'   => array(
                    'textarea_rows'    => 5,
                    'default_editor' => 'html'
                )
            ),

            array(
                'id' => 'ad_between_posts',
                'type' => 'editor',
                'title' => esc_html__( 'Between posts', 'typology' ),
                'subtitle' => esc_html__( 'This ad will be displayed between posts. You can specify the position in the option below.', 'typology' ),
                'default' => typology_get_default_option( 'ad_between_posts' ),
                'desc' => esc_html__( 'Note: If you want to paste an HTML or a JavaScript code, use "text" mode in editor', 'typology' ),
                'args'   => array(
                    'textarea_rows'    => 5,
                    'default_editor' => 'html'
                )
            ),


            array(
                'id' => 'ad_between_posts_position',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Between posts position', 'typology' ),
                'subtitle' => esc_html__( 'Specify after how many posts you want to display the ad', 'typology' ),
                'validate' => 'numeric',
                'default' => typology_get_default_option( 'ad_between_posts_position' ),
            ),

            array(
                'id'       => 'ad_exclude_404',
                'type'     => 'switch',
                'title'    => esc_html__( 'Do not show ads on 404 page', 'typology' ),
                'subtitle' => esc_html__( 'Disable ads on 404 error page', 'typology' ),
                'default' => typology_get_default_option( 'ad_exclude_404' ),
            ),

            array(
                'id'       => 'ad_exclude_from_pages',
                'type'     => 'select',
                'title'    => esc_html__( 'Do not show ads on specific pages', 'typology' ),
                'subtitle' => esc_html__( 'Select pages on which you don\'t want to display ads', 'typology' ),
                'multi'    => true,
                'sortable' => true,
                'data'     => 'page',
                'args'     => array(
                    'posts_per_page' => - 1,
                ),
                'default' => typology_get_default_option( 'ad_exclude_from_pages' ),
            ),
        )
    )
);


/* Misc. */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-wrench',
        'title'     => esc_html__( 'Misc.', 'typology' ),
        'desc'     => esc_html__( 'These are some additional miscellaneous theme settings', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'rtl_mode',
                'type' => 'switch',
                'title' => esc_html__( 'RTL mode (right to left)', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you are using right to left writing/reading', 'typology' ),
                'default' => typology_get_default_option( 'rtl_mode' ),
            ),

            array(
                'id' => 'rtl_lang_skip',
                'type' => 'text',
                'title' => esc_html__( 'Skip RTL for specific language(s)', 'typology' ),
                'subtitle' => esc_html__( 'Paste specific WordPress language <a href="http://wpcentral.io/internationalization/" target="_blank">locale code</a> to exclude it from the RTL mode', 'typology' ),
                'desc' => esc_html__( 'i.e. If you are using Arabic and English versions on the same WordPress installation you should put "en_US" in this field and its version will not be displayed as RTL. Note: To exclude multiple languages, separate by comma: en_US, de_DE', 'typology' ),
                'default' => typology_get_default_option( 'rtl_lang_skip' ),
                'required' => array( 'rtl_mode', '=', true )
            ),


            array(
                'id' => 'more_string',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'More string', 'typology' ),
                'subtitle' => esc_html__( 'Specify your "more" string to append after limited post excerpts', 'typology' ),
                'default' => typology_get_default_option( 'more_string' ),
                'validate' => 'no_html'
            ),

            array(
                'id' => 'use_gallery',
                'type' => 'switch',
                'title' => esc_html__( 'Use built-in theme gallery', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you want to use built-in theme gallery style, or disable if you are using some other gallery plugin to avoid conflicts', 'typology' ),
                'default' => typology_get_default_option( 'use_gallery' ),
            ),

            array(
                'id' => 'on_single_img_popup',
                'type' => 'switch',
                'title' => esc_html__( 'Open content image(s) in pop-up', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you want regular images inserted in post/page content to be open in pop-up', 'typology' ),
                'default' => typology_get_default_option( 'on_single_img_popup' ),
            ),

            array(
                'id' => 'scroll_down_arrow',
                'type' => 'switch',
                'title' => esc_html__( 'Display scroll-down arrow in cover', 'typology' ),
                'subtitle' => esc_html__( 'Use this option if you want to display an arrow as a scrolling indicator', 'typology' ),
                'desc' => esc_html__( 'Note: The arrow will be visible on smaller resolutions only (if cover area fills the entire screen)', 'typology' ),
                'default' => typology_get_default_option( 'scroll_down_arrow' ),
            ),

            array(
                'id' => 'words_read_per_minute',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Words to read per minute', 'typology' ),
                'subtitle' => esc_html__( 'Use this option to set number of words your visitors read per minute, in order to fine-tune calculation of post reading time meta data', 'typology' ),
                'validate' => 'numeric',
                'default' => typology_get_default_option( 'words_read_per_minute' ),
            ),

            array(
				'id'       => 'post_modified_date',
				'type'     => 'switch',
				'title' => esc_html__( 'Use "last modified" date for post meta data', 'typology' ),
				'subtitle' => esc_html__( 'Enable this option if you want posts to display modified date instead of publish date.', 'typology' ),
				'default' => typology_get_default_option( 'post_modified_date' )
			)

        )
    )
);


Redux::setSection( $opt_name , array(
        'type' => 'divide',
        'id' => 'typology-divide',
    ) );

/* Translation Options */

$translate_options[] = array(
    'id' => 'enable_translate',
    'type' => 'switch',
    'switch' => true,
    'title' => esc_html__( 'Enable theme translation?', 'typology' ),
    'default' => typology_get_default_option( 'enable_translate' ),
);

$translate_strings = typology_get_translate_options();

foreach ( $translate_strings as $string_key => $string ) {
    $translate_options[] = array(
        'id' => 'tr_'.$string_key,
        'type' => 'text',
        'title' => esc_html( $string['text'] ),
        'subtitle' => isset( $string['desc'] ) ? $string['desc'] : '',
        'default' => ''
    );
}

Redux::setSection( $opt_name, array(
        'icon'      => 'el-icon-globe-alt',
        'title' => esc_html__( 'Translation', 'typology' ),
        'desc' => wp_kses_post( __( 'Use these settings to quckly translate or change the text in this theme. If you want to remove the text completely instead of modifying it, you can use <strong>"-1"</strong> as a value for particular field translation. <br/><br/><strong>Note:</strong> If you are using this theme for a multilingual website, you need to disable these options and use multilanguage plugins (such as WPML) and manual translation with .po and .mo files located inside the "languages" folder.', 'typology' ) ),
        'fields' => $translate_options
    ) );

/* Performance */
Redux::setSection( $opt_name , array(
        'icon'      => 'el-icon-dashboard',
        'title'     => esc_html__( 'Performance', 'typology' ),
        'desc'     => esc_html__( 'Use these options to put your theme to a high speed as well as save your server resources!', 'typology' ),
        'fields'    => array(

            array(
                'id' => 'minify_css',
                'type' => 'switch',
                'title' => esc_html__( 'Use minified CSS', 'typology' ),
                'subtitle' => esc_html__( 'Load all theme css files combined and minified into a single file.', 'typology' ),
                'default' => typology_get_default_option( 'minify_css' ),
            ),

            array(
                'id' => 'minify_js',
                'type' => 'switch',
                'title' => esc_html__( 'Use minified JS', 'typology' ),
                'subtitle' => esc_html__( 'Load all theme js files combined and minified into a single file.', 'typology' ),
                'default' => typology_get_default_option( 'minify_js' ),
            ),

            array(
                'id' => 'disable_img_sizes',
                'type' => 'checkbox',
                'multi' => true,
                'title' => esc_html__( 'Disable additional image sizes', 'typology' ),
                'subtitle' => esc_html__( 'By default, theme generates additional image size for each of the layouts it offers. You can use this option to avoid creating additional sizes if you are not using particular layout in order to save your server space.', 'typology' ),
                'options' => array(
                    'cover' => esc_html__( 'Cover image', 'typology' ),
                    'a' => esc_html__( 'Layout A image (also used for single post and pages)', 'typology' ),
                    'b' => esc_html__( 'Layout B image', 'typology' ),
                    'c' => esc_html__( 'Layout C image', 'typology' ),
                ),
                'default' => typology_get_default_option( 'disable_img_sizes' ),
            ),



        ) ) );


?>
