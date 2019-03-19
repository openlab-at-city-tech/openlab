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
                'default'   => array( 'url' => esc_url( get_template_directory_uri().'/assets/img/typology_logo.png' ) ),
            ),

            array(
                'id'        => 'logo_retina',
                'type'      => 'media',
                'url'       => true,
                'title'     => esc_html__( 'Retina logo (2x)', 'typology' ),
                'subtitle'      => esc_html__( 'Optionally upload another logo for devices with retina displays. It should be double the size of your standard logo', 'typology' ),
                'default'   => array( 'url' => esc_url( get_template_directory_uri().'/assets/img/typology_logo@2x.png' ) ),
            ),

            array(
                'id'        => 'logo_custom_url',
                'type'      => 'text',
                'title'     => esc_html__( 'Custom logo URL', 'typology' ),
                'subtitle'  => esc_html__( 'Optionally specify custom URL if you want logo to point out to some other page/website instead of your home page', 'typology' ),
                'default'   => ''
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
                    'material' => array( 'title' => esc_html__( 'Material', 'typology' ),       'img' =>  get_template_directory_uri().'/assets/img/admin/style_material.png' ),
                    'flat' => array( 'title' => esc_html__( 'Flat', 'typology' ),       'img' =>  get_template_directory_uri().'/assets/img/admin/style_flat.png' ),
                ),
                'default'   => 'material',
            ),

            array(
                'id' => 'color_header_bg',
                'type' => 'color',
                'title' => esc_html__( 'Header/cover background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to header and cover background', 'typology' ),
                'transparent' => false,
                'default' => '#c62641',
            ),

            array(
                'id' => 'cover_gradient',
                'type' => 'switch',
                'title' => esc_html__( 'Header/cover gradient', 'typology' ),
                'subtitle' => esc_html__( 'Enable cover gradient', 'typology' ),
                'default' => false
            ),

            array(
                'id' => 'cover_gradient_color',
                'type' => 'color',
                'title' => esc_html__( 'Second cover background color', 'typology' ),
                'subtitle' => esc_html__( 'This color will be used to create gradient background', 'typology' ),
                'default'  => '#000000',
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
                    'to right top' => esc_html__('Left bottom to right top', 'typology'),
                    'to right' => esc_html__('Left to right', 'typology'),
                    'to right bottom' => esc_html__('Left top to right bottom', 'typology'),
                    'to bottom' => esc_html__('Top to bottom', 'typology'),
                    'to left bottom' => esc_html__('Right top to left bottom', 'typology'),
                    'to left' => esc_html__('Right to left', 'typology'),
                    'to left top' => esc_html__('Right bottom to left top', 'typology'),
                    'to top' => esc_html__('Bottom to top', 'typology'),
                    'circle' => esc_html__('Circle', 'typology'),
                ),
                'default'  => 'to right top',
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
                'default' => 'image'
            ),

            array(
                'id' => 'cover_bg_img',
                'type' => 'media',
                'url'       => true,
                'title' => esc_html__( 'Cover background image', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload cover background image', 'typology' ),
                'default' => '',
                'required' => array( 'cover_bg_media', '=', 'image')
            ),

            array(
                'id' => 'cover_bg_video',
                'type' => 'media',
                'url'  => true,
                'mode' => false,
                'title' => esc_html__( 'Cover background video', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload cover background video', 'typology' ),
                'desc' => esc_html__( 'Note: preferred formats is .mp4', 'typology' ),
                'default' => '',
                'required' => array( 'cover_bg_media', '=', 'video')
            ),

            array(
                'id' => 'cover_bg_video_image',
                'type' => 'media',
                'url'  => true,
                'mode' => false,
                'title' => esc_html__( 'Video placeholder image', 'typology' ),
                'subtitle' => esc_html__( 'Optionally, you can upload an image to display on mobile devices that don\'t support background video', 'typology' ),
                'default' => '',
                'required' => array( 'cover_bg_media', '=', 'video')
            ),

            array(
                'id'        => 'cover_bg_opacity',
                'type'      => 'slider',
                'title'     => esc_html__( 'Cover background color opacity', 'typology' ),
                'subtitle'  => esc_html__( 'If background image is uploaded, you can set background color opacity ', 'typology' ),
                "default" => 0.6,
                'resolution' => 0.1,
                "min" => 0,
                "step" => .1,
                "max" => 1,
                'display_value' => 'label'
            ),

            array(
                'id' => 'color_header_txt',
                'type' => 'color',
                'title' => esc_html__( 'Header/cover text color', 'typology' ),
                 'subtitle' => esc_html__( 'This color applies to header and cover text', 'typology' ),
                'transparent' => false,
                'default' => '#ffffff',
            ),


            array(
                'id' => 'color_body_bg',
                'type' => 'color',
                'title' => esc_html__( 'Body background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to body background (used only in "material" version)', 'typology' ),
                'transparent' => false,
                'default' => '#f8f8f8',
                'required' => array( 'style', '=', 'material' )
            ),

            array(
                'id' => 'color_content_bg',
                'type' => 'color',
                'title' => esc_html__( 'Content background color', 'typology' ),
                'subtitle' => esc_html__( 'This is your main content background color', 'typology' ),
                'transparent' => false,
                'default' => '#ffffff',
            ),


            array(
                'id' => 'color_content_h',
                'type' => 'color',
                'title' => esc_html__( 'Heading color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to post/page titles, widget titles, etc... ', 'typology' ),
                'transparent' => false,
                'default' => '#333333',
            ),

            array(
                'id' => 'color_content_txt',
                'type' => 'color',
                'title' => esc_html__( 'Text color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to standard text', 'typology' ),
                'transparent' => false,
                'default' => '#444444',
            ),

            array(
                'id' => 'color_content_acc',
                'type' => 'color',
                'title' => esc_html__( 'Accent color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to links, buttons and some other special elements', 'typology' ),
                'transparent' => false,
                'default' => '#c62641',
            ),

            array(
                'id' => 'color_content_meta',
                'type' => 'color',
                'title' => esc_html__( 'Meta color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to miscellaneous elements like post meta data (author link, date, etc...)', 'typology' ),
                'transparent' => false,
                'default' => '#888888',
            ),

            array(
                'id' => 'color_footer_bg',
                'type' => 'color',
                'title' => esc_html__( 'Footer background color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer background', 'typology' ),
                'transparent' => false,
                'default' => '#f8f8f8',
            ),


            array(
                'id' => 'color_footer_txt',
                'type' => 'color',
                'title' => esc_html__( 'Footer text color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer text', 'typology' ),
                'transparent' => false,
                'default' => '#aaaaaa',
            ),

            array(
                'id' => 'color_footer_acc',
                'type' => 'color',
                'title' => esc_html__( 'Footer accent color', 'typology' ),
                'subtitle' => esc_html__( 'This color applies to footer links, buttons and some other special elements', 'typology' ),
                'transparent' => false,
                'default' => '#888888',
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
                'default'   => 1,

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
                'default'   => array(
                    'main-menu' => 1,
                    'sidebar-button' => 1,
                    'search-dropdown' => 0,
                    'social-menu-dropdown' => 0,
                    'site-desc' => 0,
                ),
            ),

            array(
                'id' => 'header_height',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Header height', 'typology' ),
                'subtitle' => esc_html__( 'Specify height for your header/navigation area', 'typology' ),
                'desc' => esc_html__( 'Note: Height value is in px.', 'typology' ),
                'default' => 110,
                'validate' => 'numeric'
            ),


            array(
                'id'        => 'header_orientation',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Header orientation', 'typology' ),
                'subtitle' => esc_html__( 'Choose if header elements follow site content or browser width ', 'typology' ),
                'options'   => array(
                    'content' => array( 'title' => esc_html__( 'Site content', 'typology' ),       'img' =>  get_template_directory_uri().'/assets/img/admin/header_content.png' ),
                    'wide' => array( 'title' => esc_html__( 'Browser (full width)', 'typology' ),       'img' =>  get_template_directory_uri().'/assets/img/admin/header_wide.png' ),
                ),
                'default'   => 'content',

            ),

            array(
                'id'        => 'header_sticky',
                'type'      => 'switch',
                'title'     => esc_html__( 'Enable sticky header', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to enable sticky header', 'typology' ),
                'default'   => true,
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
				'default'  => '4-4-4',
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
                'type'      => 'section',
                'title'     => '<img src="'.esc_url( get_template_directory_uri().'/assets/img/admin/layout_a.png' ).'"/>'.esc_html__( 'Layout A', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout A', 'typology' ),
                'indent'   => false
            ),

            array(
                'id'        => 'layout_a_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default'   => true,
            ),

            array(
                'id'        => 'layout_a_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default'   => false,
            ),

            array(
                'id'        => 'layout_a_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_meta_opts( array( 'author', 'category', 'rtime' ) )
            ),

             array(
                'id' => 'layout_a_content',
                'type' => 'radio',
                'title' => esc_html__( 'Content type', 'typology' ),
                'options' => array(
                    'excerpt' =>  esc_html__('Excerpt (automatically limit number of characters)', 'typology' ),
                    'content' =>  esc_html__('Full content (optionally split with "<--more-->" tag)', 'typology'),
                 ),
                'subtitle' => esc_html__( 'Choose content type', 'typology' ),
                'default' => 'excerpt'
            ),

            array(
                'id' => 'layout_a_excerpt_limit',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Excerpt limit', 'typology' ),
                'subtitle' => esc_html__( 'Specify your excerpt limit', 'typology' ),
                'desc' => esc_html__( 'Note: Value represents number of characters', 'typology' ),
                'default' => '400',
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
                'default' => typology_get_button_opts( array( 'rm', 'rl' ) )
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
                'type'      => 'section',
                'title'     => '<img src="'.esc_url( get_template_directory_uri().'/assets/img/admin/layout_b.png' ).'"/>'.esc_html__( 'Layout B', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout B', 'typology' ),
                'indent'   => false
            ),

             array(
                'id'        => 'layout_b_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default'   => false,
            ),

            array(
                'id'        => 'layout_b_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_meta_opts( array( 'author', 'rtime', 'comments' ) )
            ),

             array(
                'id'        => 'layout_b_excerpt',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display text excerpt', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display excerpt', 'typology' ),
                'default'   => true,
            ),

            array(
                'id' => 'layout_b_excerpt_limit',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Excerpt limit', 'typology' ),
                'subtitle' => esc_html__( 'Specify your excerpt limit', 'typology' ),
                'desc' => esc_html__( 'Note: Value represents number of characters', 'typology' ),
                'default' => '400',
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
                'default' => typology_get_button_opts( array( '' ) )
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
                'type'      => 'section',
                'title'     => '<img src="'.esc_url( get_template_directory_uri().'/assets/img/admin/layout_c.png' ).'"/>'.esc_html__( 'Layout C', 'typology' ),
                'subtitle'  => esc_html__( 'Manage options for posts displayed in Layout C', 'typology' ),
                'indent'   => false
            ),

            array(
                'id'        => 'layout_c_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default'   => true,
            ),

            array(
                'id'        => 'layout_c_fimg',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display featured image', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display featured image', 'typology' ),
                'default'   => false,
            ),

            array(
                'id'        => 'layout_c_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_meta_opts( array( 'date' ) )
            ),


        ) ) );

/* Home Page */

if( get_option('show_on_front') != 'page' ) {

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
                'title'     => __( 'Cover area', 'typology' ),
                'subtitle'  => __( 'Manage options for home page cover area', 'typology' ),
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
                'default' => 'posts'
            ),



            array(
                'id' => 'front_page_cover_posts',
                'type' => 'radio',
                'title' => esc_html__( 'Cover area chooses from', 'typology' ),
                'subtitle' => esc_html__( 'Choose which posts to display', 'typology' ),
                'options'   => array(
                    'date' => esc_html__( 'Latest posts', 'typology' ),
                    'comment_count' => esc_html__( 'Most commented posts', 'typology' ),
                    'manual' => esc_html__( 'Manually picked posts', 'typology' )
                ),
                'default' => 'date',
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'front_page_cover_posts_cat',
                'type'      => 'select',
                'data'      => 'categories',
                'multi'     => true,
                'title'     => __( 'In category', 'typology' ),
                'subtitle'  => __( 'Check if you want to display posts from one or more specific categories', 'typology' ),
                'desc'      => __( 'Note: Leave empty for "all categories"', 'typology' ),
                'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) )
            ),

            array(
                'id'        => 'front_page_cover_posts_tag',
                'type'      => 'select',
                'data'      => 'tags',
                'multi'     => true,
                'title'     => __( 'Tagged with', 'typology' ),
                'subtitle'  => __( 'Check if you want to display posts that are tagged with one or more specific tags', 'typology' ),
                'desc'      => __( 'Note: Leave empty for "all tags"', 'typology' ),
                 'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) )
            ),


            array(
                'id'        => 'front_page_cover_posts_manual',
                'type'      => 'text',
                'title'     => __( 'Pick posts manually ', 'typology' ),
                'subtitle'  => __( 'Use this option to manually specify posts by their IDs', 'typology' ),
                'desc'      => __( 'Note: Separate post IDs by comma, i.e. 43,56,26,187', 'typology' ),
                'required' => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '=', 'manual' ) )
            ),

            array(
                'id'        => 'front_page_cover_posts_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => __( 'Number of post to display', 'typology' ),
                'subtitle'  => __( 'Choose how many posts to display', 'typology' ),
                'required'  => array( array( 'front_page_cover', '=', 'posts' ), array( 'front_page_cover_posts', '!=', 'manual' ) ),
                'validate'  => 'numeric',
                'default' => 5
            ),

             array(
                'id'        => 'front_page_cover_posts_unique',
                'type'      => 'switch',
                'title'     => __( 'Unique posts', 'typology' ),
                'subtitle'  => __( 'If you check this option, cover posts will be excluded from the main post section below', 'typology' ),
                'required' => array( 'front_page_cover', '=', 'posts' ),
                'default'  => true
            ),

            array(
                'id'        => 'front_page_cover_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default'   => true,
                'required' => array( 'front_page_cover', '!=', '0' )
            ),



            array(
                'id'        => 'layout_cover_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Display meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display for post in cover area', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_meta_opts( array( 'author', 'category', 'comments' ) ),
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'layout_cover_buttons',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Buttons', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which buttons you want to display for post in cover area', 'typology' ),
                'options'   => typology_get_button_opts(),
                'default' => typology_get_button_opts( array( 'rm', 'rl' ) ),
                'required' => array( 'front_page_cover', '=', 'posts' )
            ),

            array(
                'id'        => 'front_page_cover_posts_fimg',
                'type'      => 'switch',
                'title'     => __( 'Display cover post featured image', 'typology' ),
                'subtitle'  => __( 'Check if you want to display post featured images as cover background', 'typology' ),
                'required' => array( 'front_page_cover', '=', 'posts' ),
                'default'  => false
            ),

            array(
                'id' => 'front_page_cover_autoplay',
                'type' => 'switch',
                'title' => esc_html__( 'Autoplay (rotate)', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to auto rotate items in cover area slider', 'typology' ),
                'default' => false,
                'required'  => array( 'front_page_cover', '=', 'posts' )
            ),
            array(
                'id' => 'front_page_cover_autoplay_time',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Autoplay time', 'typology' ),
                'subtitle' => esc_html__( 'Specify autoplay time per slide', 'typology' ),
                'desc' => esc_html__( 'Note: Please specify number in seconds', 'typology' ),
                'default' => 4,
                'validate' => 'numeric',
                'required'  => array( 'front_page_cover_autoplay', '=', true )
            ),

            array(
                'id' => 'front_page_cover_on_first_page',
                'type' => 'checkbox',
                'title' => esc_html__( 'Enable Cover section on first page only', 'typology' ),
                'subtitle' => esc_html__( 'Cover section will not be displyed on page 2,3,4, etc...', 'typology' ),
                'default' => false,
                'required' => array('front_page_cover', '!=', '0')
            ),

            array(
                'id'        => 'section_front_page_intro',
                'type'      => 'section',
                'title'     => __( 'Intro section', 'typology' ),
                'subtitle'  => __( 'Manage options for home page introduction section', 'typology' ),
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
                'default' => '0'
            ),

            array(
                'id' => 'front_page_intro_on_first_page',
                'type' => 'checkbox',
                'title' => esc_html__( 'Enable Intro section on first page only', 'typology' ),
                'subtitle' => esc_html__( 'Intro section will not be displyed on page 2,3,4, etc...', 'typology' ),
                'default' => false,
                'required' => array('front_page_intro', '!=', '0')

            ),

            array(
                'id'        => 'section_front_page_posts',
                'type'      => 'section',
                'title'     => __( 'Posts section', 'typology' ),
                'subtitle'  => __( 'Manage options for home page posts section', 'typology' ),
                'indent'    => false
            ),

            array(
                'id' => 'front_page_posts',
                'type' => 'radio',
                'title' => esc_html__( 'Posts section chooses from', 'typology' ),
                'subtitle' => esc_html__( 'Choose which posts to display', 'typology' ),
                'options'   => array(
                    'date' => esc_html__( 'Latest posts', 'typology' ),
                    'comment_count' => esc_html__( 'Most commented posts', 'typology' ),
                    'manual' => esc_html__( 'Manually picked posts', 'typology' ),
                    '0' => esc_html__( 'None (do not display post section)', 'typology' ),
                ),
                'default' => 'date'
            ),


            array(
                'id'        => 'front_page_posts_cat',
                'type'      => 'select',
                'data'      => 'categories',
                'multi'     => true,
                'title'     => __( 'In category', 'typology' ),
                'subtitle'  => __( 'Check if you want to display posts from one or more specific categories', 'typology' ),
                'desc'      => __( 'Note: Leave empty for "all categories"', 'typology' ),
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),

            array(
                'id'        => 'front_page_posts_tag',
                'type'      => 'select',
                'data'      => 'tags',
                'multi'     => true,
                'title'     => __( 'Tagged with', 'typology' ),
                'subtitle'  => __( 'Check if you want to display posts that are tagged with one or more specific tags', 'typology' ),
                'desc'      => __( 'Note: Leave empty for "all tags"', 'typology' ),
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),


            array(
                'id'        => 'front_page_posts_manual',
                'type'      => 'text',
                'title'     => __( 'Pick posts manually ', 'typology' ),
                'subtitle'  => __( 'Use this option to manually specify posts by their IDs', 'typology' ),
                'desc'      => __( 'Note: Separate post IDs by comma, i.e. 43,56,26,187', 'typology' ),
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
                'default'   => 'inherit',
                'required' => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) )
            ),



            array(
                'id'        => 'front_page_posts_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default'   => get_option( 'posts_per_page' ),
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
                'default'   => 'a'
            ),

            array(
                'id'        => 'front_page_posts_pagination',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Pagination', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a pagination for home page posts', 'typology' ),
                'options'   => typology_get_pagination_layouts(),
                'required'  => array( array( 'front_page_posts', '!=', 'manual' ), array( 'front_page_posts', '!=', '0' ) ),
                'default'   => 'load-more'
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
				'default' => false,
			),
			
			array(
				'id'        => 'archive_dropcap',
				'type'      => 'switch',
				'title'     => esc_html__( 'Display dropcap in cover', 'typology' ),
				'subtitle'  => esc_html__( 'If cover is used, dropcap will be displayed', 'typology' ),
				'default'   => true,
			),
			
			array(
				'id' => 'archive_description',
				'type' => 'switch',
				'title' => esc_html__( 'Display archive description', 'typology' ),
				'subtitle' => esc_html__( 'Enable this option if you want to display category/tag/author description', 'typology' ),
				'default' => true
			),
			
			array(
				'id'        => 'archive_layout',
				'type'      => 'image_select',
				'title'     => esc_html__( 'Main layout', 'typology' ),
				'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
				'options'   => typology_get_main_layouts(),
				'default'   => 'a'
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
				'default'   => 'inherit'
			),
			
			array(
				'id'        => 'archive_ppp_num',
				'type'      => 'text',
				'class'     => 'small-text',
				'title'     => esc_html__( 'Number of posts per page', 'typology' ),
				'default'   => get_option( 'posts_per_page' ),
				'required'  => array( 'archive_ppp', '=', 'custom' ),
				'validate'  => 'numeric'
			),
			
			array(
				'id'        => 'archive_pagination',
				'type'      => 'image_select',
				'title'     => esc_html__( 'Pagination', 'typology' ),
				'subtitle'  => esc_html__( 'Choose a pagination type for archive templates', 'typology' ),
				'options'   => typology_get_pagination_layouts(),
				'default'   => 'load-more'
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
				'default' => 'inherit',
			),
			
			array(
				'id'       => 'category_cover',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display cover', 'typology' ),
				'subtitle' => esc_html__( 'Check if you want to display cover area on category templates', 'typology' ),
				'default'  => false,
				'required' => array( 'category_settings_type', '=', 'custom' ),
			),
			
			array(
				'id'       => 'category_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Main layout', 'typology' ),
				'subtitle' => esc_html__( 'Choose your main post layout', 'typology' ),
				'options'  => typology_get_main_layouts(),
				'default'  => 'a',
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
				'default'  => 'inherit',
				'required' => array( 'category_settings_type', '=', 'custom' ),
			),
			
			array(
				'id'       => 'category_ppp_num',
				'type'     => 'text',
				'class'    => 'small-text',
				'title'    => esc_html__( 'Number of posts per page', 'typology' ),
				'default'  => get_option( 'posts_per_page' ),
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
				'default'  => 'load-more',
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
		        'default' => 'inherit',
	        ),

            array(
                'id' => 'tag_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on tag templates', 'typology' ),
                'default' => false,
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'tag_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Main layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default'   => 'a',
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
                'default'   => 'inherit',
                'required' => array( 'tag_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'tag_ppp_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default'   => get_option( 'posts_per_page' ),
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
                'default'   => 'load-more',
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
		        'default' => 'inherit',
	        ),
	
	        array(
		        'id' => 'use_author_image',
		        'type' => 'switch',
		        'title' => esc_html__( 'Display author avatar on author archive page', 'typology' ),
		        'subtitle' => esc_html__( 'Enable this option if you want to display author avatar/image on author archive page', 'typology' ),
		        'default' => false,
	        ),
	
	        array(
                'id' => 'author_cover',
                'type' => 'switch',
                'title' => esc_html__( 'Display cover', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display cover area on author templates', 'typology' ),
                'default' => false,
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),
            
            array(
                'id'        => 'author_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Main layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose your main post layout', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default'   => 'a',
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
                'default'   => 'inherit',
                'required' => array( 'author_settings_type', '=', 'custom' ),
            ),

            array(
                'id'        => 'author_ppp_num',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Number of posts per page', 'typology' ),
                'default'   => get_option( 'posts_per_page' ),
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
                'default'   => 'load-more',
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
                'default' => false,
            ),

            array(
                'id'        => 'single_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default'   => true,
            ),

            array(
                'id'        => 'layout_single_meta',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Meta data', 'typology' ),
                'subtitle'  => esc_html__( 'Check and re-order which meta data you want to display', 'typology' ),
                'options'   => typology_get_meta_opts(),
                'default' => typology_get_meta_opts( array( 'author', 'comments', 'date', 'rtime', 'category' ) ),
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
                'default' => 'none'
            ),

            array(
                'id' => 'single_fimg_cap',
                'type' => 'switch',
                'title' => esc_html__( 'Display featured image caption', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the caption of the featured image', 'typology' ),
                'default' => false,
                'required'  => array( 'single_fimg', '=', 'content' )
            ),

            array(
                'id' => 'single_share',
                 'type' => 'switch',
                'title' => esc_html__( 'Display share buttons', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display share buttons', 'typology' ),
                'default' => true
            ),

            array(
                'id' => 'single_share_options',
                'type' => 'radio',
                'title' => esc_html__( 'Display share options', 'typology' ),
                'subtitle' => esc_html__( 'Select where you like to display share buttons', 'typology' ),
                'options'  => array(
                    'above' => 'Above content', 
                    'below' => 'Below content', 
                    'above_below' => 'Above and below content'
                ),
                'default' => 'below',
                'required'  => array( 'single_share', '=', true )
            ),

             array(
                'id'        => 'social_share',
                'type'      => 'sortable',
                'mode'      => 'checkbox',
                'title'     => esc_html__( 'Social sharing', 'typology' ),
                'subtitle'  => esc_html__( 'Choose social networks that you want to use for sharing posts', 'typology' ),
                'options'   => array(
                    'facebook' => esc_html__( 'Facebook', 'typology' ),
                    'twitter' => esc_html__( 'Twitter', 'typology' ),
                    'reddit' => esc_html__( 'Reddit', 'typology' ),
                    'pinterest' => esc_html__( 'Pinterest', 'typology' ),
                    'email' => esc_html__( 'Email', 'typology' ),
                    'gplus' => esc_html__( 'Google+', 'typology' ),
                    'linkedin' => esc_html__( 'LinkedIN', 'typology' ),
                    'stumbleupon' => esc_html__( 'StumbleUpon', 'typology' ),
                    'whatsapp' => esc_html__( 'WhatsApp', 'typology' ),

                ),
                'default' => array(
                    'facebook' => 1,
                    'twitter' => 1,
                    'reddit' => 1,
                    'pinterest' => 0,
                    'email' => 0,
                    'gplus' => 0,
                    'linkedin' => 0,
                    'stumbleupon' => 0,
                ),
                'required'  => array( 'single_share', '=', true )
            ),

            array(
                'id' => 'single_tags',
                'type' => 'switch',
                'title' => esc_html__( 'Display tags', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display tags', 'typology' ),
                'default' => true
            ),

            array(
                'id' => 'single_author',
                'type' => 'switch',
                'title' => esc_html__( 'Display author area', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display the author area', 'typology' ),
                'default' => true
            ),

            array(
                'id' => 'single_sticky_bottom_bar',
                'type' => 'switch',
                'title' => esc_html__( 'Enable sticky bottom bar', 'typology' ),
                'subtitle' => esc_html__( 'Check if you want to display sticky bottom bar which will appear while you scroll through single posts', 'typology' ),
                'default' => true
            ),

            array(
                'id' => 'single_related',
                'type' => 'switch',
                'title' => esc_html__( 'Display "related" posts', 'typology' ),
                'subtitle' => esc_html__( 'Choose if you want to display related posts section below single post', 'typology' ),
                'default' => true
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
                'default'   => 'cat',
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_order',
                'type'      => 'radio',
                'title'     => esc_html__( 'Related posts are ordered by', 'typology' ),
                'options'   => typology_get_post_order_opts(),
                'default'   => 'date',
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_limit',
                'type'      => 'text',
                'class'     => 'small-text',
                'title'     => esc_html__( 'Related area posts number limit', 'typology' ),
                'default'   => 4,
                'validate'  => 'numeric',
                'required'  => array( 'single_related', '=', true ),
            ),

            array(
                'id'        => 'related_layout',
                'type'      => 'image_select',
                'title'     => esc_html__( 'Related posts layout', 'typology' ),
                'subtitle'  => esc_html__( 'Choose a layout for related posts', 'typology' ),
                'options'   => typology_get_main_layouts(),
                'default'   => 'c',
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
                'default' => false,
            ),

            array(
                'id'        => 'page_dropcap',
                'type'      => 'switch',
                'title'     => esc_html__( 'Display dropcap', 'typology' ),
                'subtitle'  => esc_html__( 'Check if you want to display dropcap (first letter)', 'typology' ),
                'default'   => true,
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
                'default' => 'none'
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
                'subtitle'    => esc_html__( 'This is your main font, used for standard text', 'typology' ),
                'default'     => array(
                    'google'      => true,
                    'font-weight'  => '400',
                    'font-family' => 'Domine',
                    'subsets' => 'latin-ext'
                ),
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
                'subtitle'    => esc_html__( 'This is a font used for titles and headings', 'typology' ),
                'default'     => array(
                    'google'      => true,
                    'font-weight'  => '600',
                    'font-family' => 'Josefin Sans',
                    'subsets' => 'latin-ext'
                ),
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
                'subtitle'    => esc_html__( 'This is a font used for main website navigation', 'typology' ),
                'default'     => array(
                    'font-weight'  => '600',
                    'font-family' => 'Josefin Sans',
                    'subsets' => 'latin-ext'
                ),

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
                'default'  => '16',
                'min'      => '14',
                'step'     => '1',
                'max'      => '22',
            ),

            array(
                'id'       => 'font_size_nav',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Navigation font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to main website navigation', 'typology' ),
                'default'  => '11',
                'min'      => '10',
                'step'     => '1',
                'max'      => '20',
            ),


            array(
                'id'       => 'font_size_small',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Small text (widget) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to widgets and some special elements', 'typology' ),
                'default'  => '14',
                'min'      => '12',
                'step'     => '1',
                'max'      => '20',
            ),

            array(
                'id'       => 'font_size_meta',
                'type'     => 'spinner', 
                'title'    => esc_html__( 'Meta text font size ', 'typology' ),
                'subtitle' => esc_html__( 'Applies to meta items like author link, date, category link, etc...', 'typology' ),
                'default'  => '13',
                'min'      => '10',
                'step'     => '1',
                'max'      => '18',
            ),

            array(
                'id'       => 'font_size_cover',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Cover title font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to cover titles', 'typology' ),
                'default'  => '64',
                'min'      => '50',
                'step'     => '1',
                'max'      => '80',
            ),

            array(
                'id'       => 'font_size_cover_dropcap',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Cover dropcap font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to cover background dropcap letter', 'typology' ),
                'default'  => '600',
                'min'      => '400',
                'step'     => '1',
                'max'      => '800',
            ),

            array(
                'id'       => 'font_size_dropcap',
                'type'     => 'spinner',
                'title'    => esc_html__( 'Dropcap font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to dropcap letter on post/page titles', 'typology' ),
                'default'  => '260',
                'min'      => '150',
                'step'     => '1',
                'max'      => '400',
            ),

            array(
                'id'       => 'font_size_h1',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H1 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H1 elements and single post/page titles', 'typology' ),
                'default'  => '48',
                'min'      => '30',
                'step'     => '1',
                'max'      => '60',
            ),

            array(
                'id'       => 'font_size_h2',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H2 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H2 elements', 'typology' ),
                'default'  => '35',
                'min'      => '30',
                'step'     => '1',
                'max'      => '55',
            ),

            array(
                'id'       => 'font_size_h3',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H3 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H3 elements', 'typology' ),
                'default'  => '28',
                'min'      => '25',
                'step'     => '1',
                'max'      => '45',
            ),

            array(
                'id'       => 'font_size_h4',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H4 font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H4 elements', 'typology' ),
                'default'  => '23',
                'min'      => '20',
                'step'     => '1',
                'max'      => '40',
            ),

            array(
                'id'       => 'font_size_h5',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H5 (widget title) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H5 elements and widget titles', 'typology' ),
                'default'  => '18',
                'min'      => '14',
                'step'     => '1',
                'max'      => '24',
            ),

            array(
                'id'       => 'font_size_h6',
                'type'     => 'spinner',
                'title'    => esc_html__( 'H6 (section title) font size', 'typology' ),
                'subtitle' => esc_html__( 'Applies to H6 elements and section titles', 'typology' ),
                'default'  => '15',
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
                'default' => array(
                    '.site-title' => 1,
                    '.typology-site-description' => 0,
                    '.typology-nav' => 1,
                    'h1, h2, h3, h4, h5, h6' => 1,
                    '.section-title' => 1,
                    '.widget-title' => 1,
                    '.meta-item' => 0,
                    '.typology-button' => 1
                )
            ),

            array(
                'id'        => 'content-paragraph-width', // Note: Do not change minus!
                'type'      => 'slider',
                'title'     => esc_html__('Content paragraph width', 'typology'),
                'subtitle'  => esc_html__('Width of paragraph will be applied to single post, page and Layout A', 'typology'),
                "default"   => 720,
                "min"       => 600,
                "step"      => 5,
                "max"       => 800,
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
				'default' => '',
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
				'default' => '',
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
				'default' => '',
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
				'default' => 4
			),
			
			array(
				'id'       => 'ad_exclude_404',
				'type'     => 'switch',
				'title'    => esc_html__( 'Do not show ads on 404 page', 'typology' ),
				'subtitle' => esc_html__( 'Disable ads on 404 error page', 'typology' ),
				'default'  => false,
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
				'default'  => array(),
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
                'default' => false
            ),

            array(
                'id' => 'rtl_lang_skip',
                'type' => 'text',
                'title' => esc_html__( 'Skip RTL for specific language(s)', 'typology' ),
                'subtitle' => esc_html__( 'Paste specific WordPress language <a href="http://wpcentral.io/internationalization/" target="_blank">locale code</a> to exclude it from the RTL mode', 'typology' ),
                'desc' => esc_html__( 'i.e. If you are using Arabic and English versions on the same WordPress installation you should put "en_US" in this field and its version will not be displayed as RTL. Note: To exclude multiple languages, separate by comma: en_US, de_DE', 'typology' ),
                'default' => '',
                'required' => array( 'rtl_mode', '=', true )
            ),


            array(
                'id' => 'more_string',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'More string', 'typology' ),
                'subtitle' => esc_html__( 'Specify your "more" string to append after limited post excerpts', 'typology' ),
                'default' => '...',
                'validate' => 'no_html'
            ),

            array(
                'id' => 'use_gallery',
                'type' => 'switch',
                'title' => esc_html__( 'Use built-in theme gallery', 'typology' ),
                'subtitle' => esc_html__( 'Enable this option if you want to use built-in theme gallery style, or disable if you are using some other gallery plugin to avoid conflicts', 'typology' ),
                'default' => true
            ),

            array(
                'id' => 'disable_editor_style',
                'type' => 'switch',
                'title' => esc_html__( 'Disable admin editor styling', 'typology' ),
                'subtitle' => esc_html__( 'Use this option if you want to disable fonts and colors from Theme Options to appear in post/page content editor in admin panel', 'typology' ),
                'default' => false
            ), 

            array(
                'id' => 'scroll_down_arrow',
                'type' => 'switch',
                'title' => esc_html__( 'Display scroll-down arrow in cover', 'typology' ),
                'subtitle' => esc_html__( 'Use this option if you want to display an arrow as a scrolling indicator', 'typology' ),
                'desc' => esc_html__( 'Note: The arrow will be visible on smaller resolutions only (if cover area fills the entire screen)', 'typology' ),
                'default' => false
            ),

            array(
                'id' => 'words_read_per_minute',
                'type' => 'text',
                'class' => 'small-text',
                'title' => esc_html__( 'Words to read per minute', 'typology' ),
                'subtitle' => esc_html__( 'Use this option to set number of words your visitors read per minute, in order to fine-tune calculation of post reading time meta data', 'typology' ),
                'validate' => 'numeric',
                'default' => 200
            ),



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
    'default' => '1'
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
        'desc' => __( 'Use these settings to quckly translate or change the text in this theme. If you want to remove the text completely instead of modifying it, you can use <strong>"-1"</strong> as a value for particular field translation. <br/><br/><strong>Note:</strong> If you are using this theme for a multilingual website, you need to disable these options and use multilanguage plugins (such as WPML) and manual translation with .po and .mo files located inside the "languages" folder.', 'typology' ),
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
                'default' => true
            ),

            array(
                'id' => 'minify_js',
                'type' => 'switch',
                'title' => esc_html__( 'Use minified JS', 'typology' ),
                'subtitle' => esc_html__( 'Load all theme js files combined and minified into a single file.', 'typology' ),
                'default' => true
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

                'default' => array()
            ),



        ) ) );

/* Additional code */

Redux::setSection( $opt_name, array(
        'icon'      => 'el-icon-css',
        'title' => esc_html__( 'Additional Code', 'typology' ),
        'desc' =>  esc_html__( 'Modify the default styling of the theme by adding custom CSS or JavaScript code. Note: These options are for advanced users only, so use it with caution.', 'typology' ),
        'fields' => array(


            array(
                'id'       => 'additional_css',
                'type'     => 'ace_editor',
                'title'    => esc_html__( 'Additional CSS', 'typology' ),
                'subtitle' => esc_html__( 'Use this field to add CSS code and modify the default theme styling', 'typology' ),
                'mode'     => 'css',
                'theme'    => 'monokai',
                'default'  => ''
            ),

            array(
                'id'       => 'additional_js',
                'type'     => 'ace_editor',
                'title'    => esc_html__( 'Additional JavaScript', 'typology' ),
                'subtitle' => esc_html__( 'Use this field to add JavaScript code', 'typology' ),
                'desc' => esc_html__( 'Note: Please use clean execution JavaScript code without "script" tags', 'typology' ),
                'mode'     => 'javascript',
                'theme'    => 'monokai',
                'default'  => ''
            )

        ) )
);



/* Updater Options */

if ( !typology_is_envato_market_active() ) {
    Redux::setSection( $opt_name, array(
            'icon'      => 'el-icon-time',
            'title' => esc_html__( 'Updater', 'typology' ),
            'fields' => array(

                array( 
                    'id'       => 'theme_update_info',
                    'type'     => 'raw',
                    'content'  =>  sprintf( __('For the best possible experience, from now on, all theme updates are handled via Theme Forest official <a href="%s">Envato Market plugin</a>.<br/><br/>Once you install it, follow their instructions and add your Envato token key to enable further update notifications and update this theme with a single click whenever we release a new version. </br></br><a href="%s" class="button-primary">Install plugin</a>', 'typology'),  admin_url( 'themes.php?page=install-required-plugins' ), admin_url( 'themes.php?page=install-required-plugins' ) )
                )

            ) )
    );
}




?>
