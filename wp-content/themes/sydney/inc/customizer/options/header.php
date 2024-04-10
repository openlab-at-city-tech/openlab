<?php
/**
 * Header Customizer options
 *
 * @package Sydney
 */

/**
 * Header
 */
$wp_customize->add_panel(
	'sydney_panel_header',
	array(
		'title'         => esc_html__( 'Header', 'sydney'),
		'priority'      => 1,
	)
);

/**
 * Site identity
 */
$wp_customize->add_setting( 'site_logo_size_desktop', array(
	'default'   		=> 100,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );			

$wp_customize->add_setting( 'site_logo_size_tablet', array(
	'default'   		=> 100,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_setting( 'site_logo_size_mobile', array(
	'default'   		=> 100,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );			


$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'site_logo_size',
	array(
		'label' 		=> esc_html__( 'Logo height', 'sydney' ),
		'section' 		=> 'title_tagline',
		'is_responsive'	=> 1,
		'settings' 		=> array (
			'size_desktop' 		=> 'site_logo_size_desktop',
			'size_tablet' 		=> 'site_logo_size_tablet',
			'size_mobile' 		=> 'site_logo_size_mobile',
		),
		'input_attrs' => array (
			'min'	=> 0,
			'max'	=> 500
		)		
	)
) );

$wp_customize->add_setting(
	'logo_site_title',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'logo_site_title',
		array(
			'label'         	=> esc_html__( 'Show site title and description next to the logo', 'sydney' ),
			'section'       	=> 'title_tagline',
			'priority'       => 59,
			'separator' 	=> 'after'
		)
	)
);

$wp_customize->add_setting(
	'global_site_title_color',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_setting(
	'site_title_color',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Alpha_Color(
		$wp_customize,
		'site_title_color',
		array(
			'label'         	=> esc_html__( 'Site title color', 'sydney' ),
			'section'       	=> 'title_tagline',
			'priority' => 61,
			'settings'			=> array(
				'global'	=> 'global_site_title_color',
				'setting'	=> 'site_title_color',
			),
		)
	)
);

$wp_customize->add_setting( 'site_title_font_size_desktop', array(
	'default'   		=> 32,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_setting( 'site_title_font_size_tablet', array(
	'default'   		=> 24,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_setting( 'site_title_font_size_mobile', array(
	'default'   		=> 20,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'site_title_font_size',
	array(
		'label' 		=> esc_html__( 'Site title font size', 'sydney' ),
		'section' 		=> 'title_tagline',
		'is_responsive'	=> 1,
		'settings' 		=> array (
			'size_desktop' 		=> 'site_title_font_size_desktop',
			'size_tablet' 		=> 'site_title_font_size_tablet',
			'size_mobile' 		=> 'site_title_font_size_mobile',
		),
		'priority' 			=> 62,
		'input_attrs' => array (
			'min'	=> 12,
			'max'	=> 100,
			'step'  => 1
		),
		'separator' 	=> 'after'
	)
) );

$wp_customize->add_setting(
	'global_site_desc_color',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_setting(
	'site_desc_color',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Alpha_Color(
		$wp_customize,
		'site_desc_color',
		array(
			'label'         	=> esc_html__( 'Site description color', 'sydney' ),
			'section'       	=> 'title_tagline',
			'priority' 			=> 63,
			'settings'			=> array(
				'global'	=> 'global_site_desc_color',
				'setting'	=> 'site_desc_color',
			),
		)
	)
);

$wp_customize->add_setting( 'site_desc_font_size_desktop', array(
	'default'   		=> 16,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_setting( 'site_desc_font_size_tablet', array(
	'default'   		=> 16,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_setting( 'site_desc_font_size_mobile', array(
	'default'   		=> 16,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'site_desc_font_size',
	array(
		'label' 		=> esc_html__( 'Site description font size', 'sydney' ),
		'section' 		=> 'title_tagline',
		'is_responsive'	=> 1,
		'settings' 		=> array (
			'size_desktop' 		=> 'site_desc_font_size_desktop',
			'size_tablet' 		=> 'site_desc_font_size_tablet',
			'size_mobile' 		=> 'site_desc_font_size_mobile',
		),
		'priority' 			=> 64,
		'input_attrs' => array (
			'min'	=> 12,
			'max'	=> 100,
			'step'  => 1
		)
	)
) );

/**
 * Main header
 */
$wp_customize->add_section(
	'sydney_section_main_header',
	array(
		'title'      => esc_html__( 'Main header', 'sydney'),
		'panel'      => 'sydney_panel_header',
	)
);

$wp_customize->add_setting(
	'sydney_main_header_tabs',
	array(
		'default'           => '',
		'sanitize_callback' => 'esc_attr'
	)
);
$wp_customize->add_control(
	new Sydney_Tab_Control (
		$wp_customize,
		'sydney_main_header_tabs',
		array(
			'label' 				=> '',
			'section'       		=> 'sydney_section_main_header',
			'controls_general'		=> json_encode( array( '#customize-control-header_social_divider_1','#customize-control-social_profiles_header','#customize-control-transparent_header','#customize-control-sydney_upsell_main_header','#customize-control-header_layout_desktop','#customize-control-header_divider_1','#customize-control-main_header_settings_title','#customize-control-main_header_menu_position','#customize-control-header_container','#customize-control-enable_sticky_header','#customize-control-sticky_header_type','#customize-control-header_divider_2','#customize-control-main_header_elements_title','#customize-control-header_components_l1','#customize-control-header_components_l3left','#customize-control-header_components_l3right','#customize-control-header_components_l4top','#customize-control-header_components_l4bottom','#customize-control-header_components_l5topleft','#customize-control-header_components_l5topright','#customize-control-header_components_l5bottom','#customize-control-header_divider_3','#customize-control-main_header_cart_account_title','#customize-control-enable_header_cart','#customize-control-enable_header_account','#customize-control-header_divider_4','#customize-control-main_header_button_title','#customize-control-header_button_text','#customize-control-header_button_link','#customize-control-header_button_newtab','#customize-control-header_divider_5','#customize-control-main_header_contact_info_title','#customize-control-header_contact_mail','#customize-control-header_contact_phone', ) ),
			'controls_design'		=> json_encode( array( '#customize-control-submenu_items_hover','#customize-control-main_header_color_sticky','#customize-control-enable_top_menu_typography','#customize-control-menu_typography_title','#customize-control-main_header_divider_8','#customize-control-sydney_menu_font','#customize-control-menu_items_text_transform','#customize-control-sydney_menu_font_size','#customize-control-menu_items_hover','#customize-control-main_header_submenu_color','#customize-control-main_header_submenu_background','#customize-control-main_header_bottom_padding','#customize-control-main_header_bottom_background', '#customize-control-main_header_bottom_color','#customize-control-main_header_divider_7','#customize-control-main_header_background','#customize-control-main_header_background_sticky','#customize-control-main_header_color','#customize-control-main_header_divider_6','#customize-control-main_header_padding','#customize-control-main_header_divider_size','#customize-control-main_header_divider_color','#customize-control-main_header_divider_width' ) ),
		)
	)
);

//Layout
$choices = sydney_header_layouts();

$wp_customize->add_setting(
	'header_layout_desktop',
	array(
		'default'           => 'header_layout_2',
		'sanitize_callback' => 'sanitize_key',
		//'transport'			=> 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Radio_Images(
		$wp_customize,
		'header_layout_desktop',
		array(
			'label'    	=> esc_html__( 'Layout', 'sydney' ),
			'section'  	=> 'sydney_section_main_header',
			'cols'		=> 2,
			'choices'  	=> $choices
		)
	)
); 

$wp_customize->selective_refresh->add_partial( 'header_layout_desktop', array(
	'selector' 				=> '#masthead',
	'settings' 				=> 'header_layout_desktop',
	'render_callback' => function() {
		$top_bar = Sydney_Header::get_instance();
		$top_bar->header_markup();
	},
	'container_inclusive' 	=> true,
) );

//General
$wp_customize->add_setting( 'main_header_settings_title',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'esc_attr'
	)
);

$wp_customize->add_control( new Sydney_Text_Control( $wp_customize, 'main_header_settings_title',
		array(
			'label'			=> esc_html__( 'Settings', 'sydney' ),
			'section' 		=> 'sydney_section_main_header',
			'separator' 	=> 'before'
		)
	)
);

$wp_customize->add_setting( 'main_header_menu_position',
	array(
		'default' 			=> 'right',
		'sanitize_callback' => 'sydney_sanitize_text',
		'transport'			=> 'postMessage'
	)
);
$wp_customize->add_control( new Sydney_Radio_Buttons( $wp_customize, 'main_header_menu_position',
	array(
		'label' 		=> esc_html__( 'Menu position', 'sydney' ),
		'section' => 'sydney_section_main_header',
		'choices' => array(
			'left' 		=> esc_html__( 'Left', 'sydney' ),
			'center' 	=> esc_html__( 'Center', 'sydney' ),
			'right' 	=> esc_html__( 'Right', 'sydney' ),
		),
		'active_callback' => 'sydney_callback_menu_position',
	)
) );

$wp_customize->add_setting( 'header_container',
	array(
		'default' 			=> 'container',
		'sanitize_callback' => 'sydney_sanitize_text',
		'transport'			=> 'postMessage'
	)
);
$wp_customize->add_control( new Sydney_Radio_Buttons( $wp_customize, 'header_container',
	array(
		'label' 		=> esc_html__( 'Container type', 'sydney' ),
		'section' => 'sydney_section_main_header',
		'choices' => array(
			'container' 		=> esc_html__( 'Contained', 'sydney' ),
			'container-fluid' 	=> esc_html__( 'Full-width', 'sydney' ),
		)
	)
) );

$wp_customize->add_setting(
	'enable_sticky_header',
	array(
		'default'           => 1,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'			=> 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_sticky_header',
		array(
			'label'         	=> esc_html__( 'Enable sticky header', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
		)
	)
);

$wp_customize->add_setting( 'sticky_header_type',
	array(
		'default' 			=> 'always',
		'sanitize_callback' => 'sydney_sanitize_text'
	)
);
$wp_customize->add_control( new Sydney_Radio_Buttons( $wp_customize, 'sticky_header_type',
	array(
		'label' 		=> esc_html__( 'Sticky header type', 'sydney' ),
		'section' => 'sydney_section_main_header',
		'choices' => array(
			'always' 		=> esc_html__( 'Always sticky', 'sydney' ),
			'scrolltop' 	=> esc_html__( 'On scroll to top', 'sydney' ),
		),
		'active_callback' => 'sydney_callback_sticky_header',
	)
) );

// Header Image Display Conditions
$wp_customize->add_setting(
	'transparent_header',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_textarea_field'
	)
);
$wp_customize->add_control(
	new Sydney_Display_Conditions_Control(
		$wp_customize,
		'transparent_header',
		array(
			'label'    		  => esc_html__( 'Transparent header', 'sydney' ),
			'section'  		  => 'sydney_section_main_header'
		)
	)
);

$wp_customize->add_setting( 'main_header_elements_title',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'esc_attr'
	)
);

$wp_customize->add_control( new Sydney_Text_Control( $wp_customize, 'main_header_elements_title',
		array(
			'label'			=> esc_html__( 'Elements', 'sydney' ),
			'section' 		=> 'sydney_section_main_header',
			'separator' 	=> 'before'
		)
	)
);

$header_components 	= sydney_header_elements();
$default_components = sydney_get_default_header_components();

//Layout 1&2 elements
$wp_customize->add_setting( 'header_components_l1', array(
	'default'  			=> $default_components['l1'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l1', array(
	'label'   			=> '',
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_1_2',
) ) );

//Layout 3 elements
$wp_customize->add_setting( 'header_components_l3left', array(
	'default'  			=> $default_components['l3left'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l3left', array(
	'label'   			=> esc_html__( 'Left', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_3',
) ) );

$wp_customize->add_setting( 'header_components_l3right', array(
	'default'  			=> $default_components['l3right'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l3right', array(
	'label'   			=> esc_html__( 'Right', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_3',
) ) );

//Layout 4 elements
$wp_customize->add_setting( 'header_components_l4top', array(
	'default'  			=> $default_components['l4top'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l4top', array(
	'label'   			=> esc_html__( 'Top row', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_4',
) ) );

$wp_customize->add_setting( 'header_components_l4bottom', array(
	'default'  			=> $default_components['l4bottom'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l4bottom', array(
	'label'   			=> esc_html__( 'Bottom row', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_4',
) ) );

//Layout 5 elements
$wp_customize->add_setting( 'header_components_l5topleft', array(
	'default'  			=> $default_components['l5topleft'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l5topleft', array(
	'label'   			=> esc_html__( 'Top left', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_5',
) ) );

$wp_customize->add_setting( 'header_components_l5topright', array(
	'default'  			=> $default_components['l5topleft'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l5topright', array(
	'label'   			=> esc_html__( 'Top right', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_5',
) ) );

$wp_customize->add_setting( 'header_components_l5bottom', array(
	'default'  			=> $default_components['l5topleft'],
	'sanitize_callback'	=> 'sydney_sanitize_header_components',
	//'transport'			=> 'postMessage'
) );

$wp_customize->add_control( new \Kirki\Control\Sortable( $wp_customize, 'header_components_l5bottom', array(
	'label'   			=> esc_html__( 'Bottom', 'sydney' ),
	'section' 			=> 'sydney_section_main_header',
	'choices' 			=> $header_components,
	'active_callback' 	=> 'sydney_callback_header_layout_5',
) ) );

$header_areas = array( 'header_components_l1','header_components_l3left','header_components_l3right','header_components_l4top','header_components_l4bottom','header_components_l5topleft','header_components_l5topright','header_components_l5bottom' );

foreach ( $header_areas as $header_area ) {
	$wp_customize->selective_refresh->add_partial( $header_area, array(
		'selector' 				=> '#masthead',
		'settings' 				=> $header_area,
		'render_callback' => function() {
			$header = Sydney_Header::get_instance();
			$header->header_markup();
		},
		'container_inclusive' 	=> true,
	) );
}

/**
 * Elements
 */
//social_profiles_header
$wp_customize->add_setting( 'social_profiles_header',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'sydney_sanitize_urls',
		'transport'			=> 'postMessage'
	)
);

$wp_customize->add_control( new Sydney_Repeater_Control( $wp_customize, 'social_profiles_header',
	array(
		'label' 		=> esc_html__( 'Social profile', 'sydney' ),
		'section' 		=> 'sydney_section_main_header',
		'button_labels' => array(
			'add' => esc_html__( 'Add new', 'sydney' ),
		),
		'active_callback' 	=> function() { return sydney_callback_header_elements( 'social' ); }
	)
) );

$wp_customize->selective_refresh->add_partial( 'social_profiles_header', array(
	'selector' 				=> '.header-social',
	'container_inclusive' 	=> true,
	'render_callback' 		=> array( 'Sydney_Header', 'social' ),
) );

$wp_customize->add_setting(
	'enable_header_cart',
	array(
		'default'           => 1,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_header_cart',
		array(
			'label'         	=> esc_html__( 'Enable cart icon', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
			'active_callback' 	=> function() { return sydney_callback_header_elements( 'woocommerce_icons' ); }
		)
	)
);

$wp_customize->add_setting(
	'enable_header_account',
	array(
		'default'           => 1,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_header_account',
		array(
			'label'         	=> esc_html__( 'Enable account icon', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
			'active_callback' 	=> function() { return sydney_callback_header_elements( 'woocommerce_icons' ); }
		)
	)
);

//Button
$wp_customize->add_setting(
	'header_button_text',
	array(
		'sanitize_callback' => 'sydney_sanitize_text',
		'default'           => esc_html__( 'Click me', 'sydney' ),
		'transport'         => 'postMessage'
	)       
);
$wp_customize->add_control( 'header_button_text', array(
	'label'       => esc_html__( 'Button text', 'sydney' ),
	'type'        => 'text',
	'section'     => 'sydney_section_main_header',
	'active_callback' 	=> function() { return sydney_callback_header_elements( 'button' ); }
) );

$wp_customize->add_setting(
	'header_button_link',
	array(
		'sanitize_callback' => 'esc_url_raw',
		'default'           => '#',
		'transport'         => 'postMessage'
	)       
);
$wp_customize->add_control( 'header_button_link', array(
	'label'       => esc_html__( 'Button link', 'sydney' ),
	'type'        => 'text',
	'section'     => 'sydney_section_main_header',
	'active_callback' 	=> function() { return sydney_callback_header_elements( 'button' ); }
) );

$wp_customize->add_setting(
	'header_button_newtab',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'header_button_newtab',
		array(
			'label'         	=> esc_html__( 'Open in a new tab?', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
			'active_callback' 	=> function() { return sydney_callback_header_elements( 'button' ); }
		)
	)
);

//Contact info
$wp_customize->add_setting(
	'header_contact_mail',
	array(
		'sanitize_callback' => 'sydney_sanitize_text',
		'default'           => esc_html__( 'office@example.org', 'sydney' ),
		'transport'         => 'postMessage'
	)       
);
$wp_customize->add_control( 'header_contact_mail', array(
	'label'       => esc_html__( 'Email address', 'sydney' ),
	'type'        => 'text',
	'section'     => 'sydney_section_main_header',
	'active_callback' 	=> function() { return sydney_callback_header_elements( 'contact_info' ); }
) );

$wp_customize->add_setting(
	'header_contact_phone',
	array(
		'sanitize_callback' => 'sydney_sanitize_text',
		'default'           => esc_html__( '111222333', 'sydney' ),
		'transport'         => 'postMessage'
	)       
);
$wp_customize->add_control( 'header_contact_phone', array(
	'label'       => esc_html__( 'Phone number', 'sydney' ),
	'type'        => 'text',
	'section'     => 'sydney_section_main_header',
	'active_callback' 	=> function() { return sydney_callback_header_elements( 'contact_info' ); }
) );

/**
 * Styling
 */
$wp_customize->add_setting(
	'global_main_header_background',
	array(
		'default'           => 'global_color_6',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_setting(
	'main_header_background',
	array(
		'default'           => '#00102E',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Alpha_Color(
		$wp_customize,
		'main_header_background',
		array(
			'label'         	=> esc_html__( 'Background color', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
			'settings'			=> array(
				'global'	=> 'global_main_header_background',
				'setting'	=> 'main_header_background',
			),
		)
	)
);

$wp_customize->add_setting(
	'global_main_header_background_sticky',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_setting(
	'main_header_background_sticky',
	array(
		'default'           => '',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);

$wp_customize->add_control(
	new Sydney_Alpha_Color(
		$wp_customize,
		'main_header_background_sticky',
		array(
			'label'         	=> esc_html__( 'Background color (sticky)', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
			'settings'			=> array(
				'global'	=> 'global_main_header_background_sticky',
				'setting'	=> 'main_header_background_sticky',
			),
		)
	)
);

$wp_customize->add_setting(
    'global_main_header_color',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_color',
    array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_color',
        array(
            'label'          => esc_html__( 'Text color', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'settings'       => array(
                'global'  => 'global_main_header_color',
                'setting' => 'main_header_color',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_main_header_color_sticky',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_color_sticky',
    array(
        'default'           => '',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_color_sticky',
        array(
            'label'          => esc_html__( 'Text color (sticky)', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'settings'       => array(
                'global'  => 'global_main_header_color_sticky',
                'setting' => 'main_header_color_sticky',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_main_header_bottom_background',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_bottom_background',
    array(
        'default'           => '#00102E',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_bottom_background',
        array(
            'label'          => esc_html__( 'Bottom row background color', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'active_callback' => 'sydney_callback_header_bottom',
            'settings'       => array(
                'global'  => 'global_main_header_bottom_background',
                'setting' => 'main_header_bottom_background',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_main_header_bottom_color',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_bottom_color',
    array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_bottom_color',
        array(
            'label'          => esc_html__( 'Bottom row text color', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'active_callback' => 'sydney_callback_header_bottom',
            'settings'       => array(
                'global'  => 'global_main_header_bottom_color',
                'setting' => 'main_header_bottom_color',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_main_header_submenu_background',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_submenu_background',
    array(
        'default'           => '#233452',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_submenu_background',
        array(
            'label'          => esc_html__( 'Submenu background', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'settings'       => array(
                'global'  => 'global_main_header_submenu_background',
                'setting' => 'main_header_submenu_background',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_main_header_submenu_color',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'main_header_submenu_color',
    array(
        'default'           => '#fff',
        'sanitize_callback' => 'sydney_sanitize_hex_rgba',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'main_header_submenu_color',
        array(
            'label'          => esc_html__( 'Submenu color', 'sydney' ),
            'section'        => 'sydney_section_main_header',
            'settings'       => array(
                'global'  => 'global_main_header_submenu_color',
                'setting' => 'main_header_submenu_color',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_menu_items_hover',
    array(
        'default'           => 'global_color_1',
        'sanitize_callback' => 'wp_kses_post',
    )
);

$wp_customize->add_setting(
    'menu_items_hover',
    array(
        'default'           => '#d65050',
        'sanitize_callback' => 'sanitize_hex_color',
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'menu_items_hover',
        array(
            'label'         => __('Menu items hover', 'sydney'),
            'section'       => 'sydney_section_main_header',
            'settings'      => array(
                'global'  => 'global_menu_items_hover',
                'setting' => 'menu_items_hover',
            ),
        )
    )
);

$wp_customize->add_setting(
    'global_submenu_items_hover',
    array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage'
    )
);

$wp_customize->add_setting(
    'submenu_items_hover',
    array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage'
    )
);
$wp_customize->add_control(
    new Sydney_Alpha_Color(
        $wp_customize,
        'submenu_items_hover',
        array(
            'label'         => __('Submenu items hover', 'sydney'),
            'section'       => 'sydney_section_main_header',
            'settings'      => array(
                'global'  => 'global_submenu_items_hover',
                'setting' => 'submenu_items_hover',
            ),
        )
    )
);

$wp_customize->add_setting( 'main_header_padding', array(
	'default'   		=> 15,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );			

$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'main_header_padding',
	array(
		'label' 		=> esc_html__( 'Padding', 'sydney' ),
		'section' 		=> 'sydney_section_main_header',
		'is_responsive'	=> 0,
		'settings' 		=> array (
			'size_desktop' 		=> 'main_header_padding',
		),
		'input_attrs' => array (
			'min'	=> 0,
			'max'	=> 100
		),
		'separator' 	=> 'before'
	)
) );

$wp_customize->add_setting( 'main_header_bottom_padding', array(
	'default'   		=> 15,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );			

$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'main_header_bottom_padding',
	array(
		'label' 		=> esc_html__( 'Bottom row padding', 'sydney' ),
		'section' 		=> 'sydney_section_main_header',
		'is_responsive'	=> 0,
		'settings' 		=> array (
			'size_desktop' 		=> 'main_header_bottom_padding',
		),
		'input_attrs' => array (
			'min'	=> 0,
			'max'	=> 100
		),
		'active_callback'   => 'sydney_callback_header_bottom',
		'separator' 	=> 'after'
	)
) );

$wp_customize->add_setting( 'main_header_divider_size', array(
	'sanitize_callback' => 'absint',
	'default' 			=> 0,
) );

$wp_customize->add_control( 'main_header_divider_size', array(
	'type' 				=> 'number',
	'section' 			=> 'sydney_section_main_header',
	'label' 			=> esc_html__( 'Border size', 'sydney' ),
) );

$wp_customize->add_setting(
	'main_header_divider_color',
	array(
		'default'           => 'rgba(255,255,255,0.1)',
		'sanitize_callback' => 'sydney_sanitize_hex_rgba',
		'transport'         => 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Alpha_Color(
		$wp_customize,
		'main_header_divider_color',
		array(
			'label'         	=> esc_html__( 'Border color', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
		)
	)
);

$wp_customize->add_setting( 'main_header_divider_width',
	array(
		'default' 			=> 'fullwidth',
		'sanitize_callback' => 'sydney_sanitize_text'
	)
);
$wp_customize->add_control( new Sydney_Radio_Buttons( $wp_customize, 'main_header_divider_width',
	array(
		'label' 	=> esc_html__( 'Border width', 'sydney' ),
		'section' 	=> 'sydney_section_main_header',
		'choices' 	=> array(
			'contained' 	=> esc_html__( 'Contained', 'sydney' ),
			'fullwidth' 	=> esc_html__( 'Full-width', 'sydney' ),
		),
	)
) );

/**
 * Menu typography
 */
$wp_customize->add_setting( 'menu_typography_title',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'esc_attr'
	)
);

$wp_customize->add_control( new Sydney_Text_Control( $wp_customize, 'menu_typography_title',
		array(
			'label'			=> esc_html__( 'Top-level menu items', 'sydney' ),
			'section' 		=> 'sydney_section_main_header',
			'separator' 	=> 'before'
		)
	)
);

$wp_customize->add_setting(
	'enable_top_menu_typography',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox',
		'transport'         => 'postMessage'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'enable_top_menu_typography',
		array(
			'label'         	=> esc_html__( 'Enable top-level menu items typography options', 'sydney' ),
			'section'       	=> 'sydney_section_main_header',
		)
	)
);

$wp_customize->add_setting( 'sydney_menu_font',
	array(
		'default'           => '{"font":"System default","regularweight":"bold","category":"sans-serif"}',
		'sanitize_callback' => 'sydney_google_fonts_sanitize',
		'transport'	 		=> 'postMessage'
	)
);


$wp_customize->add_control( new Sydney_Typography_Control( $wp_customize, 'sydney_menu_font',
	array(
		'section' => 'sydney_section_main_header',
		'settings' => array (
			'family' => 'sydney_menu_font',
		),
		'input_attrs' => array(
			'font_count'    => 'all',
			'orderby'       => 'alpha',
			'disableRegular' => false,
		),
		'active_callback' => 'sydney_callback_menu_typography'
	)
) );

$wp_customize->add_setting( 'menu_items_text_transform',
	array(
		'default' 			=> 'none',
		'sanitize_callback' => 'sydney_sanitize_text',
		'transport'			=> 'postMessage',
	)
);
$wp_customize->add_control( new Sydney_Radio_Buttons( $wp_customize, 'menu_items_text_transform',
	array(
		'label'   => esc_html__( 'Text transform', 'sydney' ),
		'section' => 'sydney_section_main_header',
		'choices' => array(
			'none' 			=> '-',
			'capitalize' 	=> 'Aa',
			'lowercase' 	=> 'aa',
			'uppercase' 	=> 'AA',
		),
		'active_callback' => 'sydney_callback_menu_typography'
	)
) );

$wp_customize->add_setting( 'sydney_menu_font_size_desktop', array(
	'default'   		=> 14,
	'transport'			=> 'postMessage',
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_control( new Sydney_Responsive_Slider( $wp_customize, 'sydney_menu_font_size',
	array(
		'label' 		=> esc_html__( 'Menu Items Font size', 'sydney' ),
		'section' 		=> 'sydney_section_main_header',
		'is_responsive'	=> 0,
		'settings' 		=> array (
			'size_desktop' 		=> 'sydney_menu_font_size_desktop',
		),
		'input_attrs' => array (
			'min'	=> 12,
			'max'	=> 100,
			'step'  => 1
		),
		'active_callback' => 'sydney_callback_menu_typography'
	)
) );