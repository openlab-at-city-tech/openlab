<?php
/**
 * Miniva Theme Customizer
 *
 * @package Miniva
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function miniva_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'miniva_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'miniva_customize_partial_blogdescription',
			)
		);
	}

	$section_priority = 160;
	foreach ( miniva_get_customizer_data() as $section_name => $section_data ) {
		/**
		 * Section
		 */
		$default_sections = array( 'title_tagline', 'colors', 'header_image', 'background_image', 'static_front_page', 'featured_content' );
		if ( ! in_array( $section_name, $default_sections, true ) ) {
			$defaults = array(
				'priority' => $section_priority++,
			);

			$section_args = wp_parse_args( $section_data, $defaults );
			$wp_customize->add_section( $section_name, $section_args );
		}

		$control_priority = 1;
		foreach ( (array) $section_data['fields'] as $field_name => $field_data ) {
			/**
			 * Setting
			 */
			if ( 'custom' === $field_data['type'] ) {
				$wp_customize->add_setting(
					$field_name,
					array(
						'sanitize_callback' => '__return_false',
					)
				);
			} else {
				$wp_customize->add_setting(
					$field_name,
					array(
						'default'           => isset( $field_data['default'] ) ? $field_data['default'] : '',
						'sanitize_callback' => isset( $field_data['sanitize_callback'] ) ? $field_data['sanitize_callback'] : miniva_get_sanitize_callback( $field_data['type'] ),
					)
				);
			}

			/**
			 * Control
			 */
			$defaults = array(
				'priority' => $control_priority++,
				'section'  => $section_name,
			);

			$control_args = wp_parse_args( $field_data, $defaults );
			switch ( $control_args['type'] ) {
				case 'image':
					$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $field_name, $control_args ) );
					break;
				case 'color':
					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $field_name, $control_args ) );
					break;
				case 'radio-image':
					$wp_customize->add_control( new Miniva_Radio_Image_Control( $wp_customize, $field_name, $control_args ) );
					break;
				case 'custom':
					$wp_customize->add_control( new Miniva_Custom_Control( $wp_customize, $field_name, $control_args ) );
					break;
				default:
					$wp_customize->add_control( $field_name, $control_args );
					break;
			}
		}
	}
}
add_action( 'customize_register', 'miniva_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function miniva_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function miniva_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function miniva_customize_preview_js() {
	wp_enqueue_script( 'miniva-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'miniva_customize_preview_js' );

/**
 * Embed CSS in the Customizer
 */
function miniva_customize_css() {
	wp_enqueue_style( 'miniva-customizer-css', get_template_directory_uri() . '/css/customizer.css', array(), MINIVA_VERSION );
}
add_action( 'customize_controls_print_styles', 'miniva_customize_css' );

/**
 * Sanitization callback for 'select' and 'radio' type controls. This callback sanitizes `$input`
 * as a slug, and then validates `$input` against the choices defined for the control.
 *
 * @param  string               $input   Slug to sanitize.
 * @param  WP_Customize_Setting $setting Setting instance.
 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
 *
 * @link https://github.com/WPTRT/code-examples
 */
function miniva_sanitize_select( $input, $setting ) {
	// Ensure input is a slug.
	$input = sanitize_key( $input );

	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;

	// If the input is a valid key, return it; otherwise, return the default.
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 * @param  bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 *
 * @link https://github.com/WPTRT/code-examples
 */
function miniva_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Get sanitize callback based on the field type
 *
 * @param  string $type Field type.
 * @return callback
 */
function miniva_get_sanitize_callback( $type ) {
	switch ( $type ) {
		case 'text':
			return 'sanitize_text_field';
		case 'textarea':
			return 'sanitize_textarea_field';
		case 'color':
			return 'sanitize_hex_color';
		case 'radio':
		case 'radio-image':
		case 'select':
			return 'miniva_sanitize_select';
		case 'checkbox':
			return 'miniva_sanitize_checkbox';
		default:
			return 'sanitize_text_field';
	}
}

/**
 * Header layout callback
 *
 * @param  object $control Control.
 * @return boolean
 */
function miniva_header_default_callback( $control ) {
	$option = $control->manager->get_setting( 'header_layout' );
	return 'top' === $option->value() ? true : false;
}

/**
 * Check if the page uses the Widgetized Page Template
 *
 * @return boolean
 */
function miniva_is_widgetized_page() {
	return is_page_template( 'template-widgetized.php' );
}

/**
 * Get blog display
 *
 * @return string
 */
function miniva_get_blog_display_default() {
	$jetpack_blog_display = get_option( 'jetpack_content_blog_display' );
	if ( 'excerpt' === $jetpack_blog_display ) {
		return 'excerpt';
	}
	return 'content';
}

/**
 * Get customizer data
 *
 * @return array
 */
function miniva_get_customizer_data() {
	$data['header']['title'] = esc_html__( 'Header', 'miniva' );

	$data['header']['fields']['header_layout'] = array(
		'type'    => 'select',
		'label'   => esc_html__( 'Logo Position', 'miniva' ),
		'choices' => miniva_get_header_layouts(),
		'default' => 'top',
	);

	$data['header']['fields']['logo_centered'] = array(
		'type'            => 'checkbox',
		'label'           => esc_html__( 'Centered Logo', 'miniva' ),
		'default'         => true,
		'active_callback' => 'miniva_header_default_callback',
	);

	$data['header']['fields']['menu_centered'] = array(
		'type'            => 'checkbox',
		'label'           => esc_html__( 'Centered Menu', 'miniva' ),
		'default'         => true,
		'active_callback' => 'miniva_header_default_callback',
	);

	$data['header']['fields']['header_search'] = array(
		'type'    => 'checkbox',
		'label'   => esc_html__( 'Show Search Button', 'miniva' ),
		'default' => false,
	);

	$data['content']['title'] = esc_html__( 'Content', 'miniva' );

	$data['content']['fields']['posts_layout'] = array(
		'type'    => 'select',
		'label'   => esc_html__( 'Posts Layout', 'miniva' ),
		'choices' => miniva_get_posts_layouts(),
		'default' => 'large',
	);

	$data['content']['fields']['sidebar_layout'] = array(
		'type'    => 'select',
		'label'   => esc_html__( 'Sidebar Layout', 'miniva' ),
		'choices' => miniva_get_sidebar_layouts(),
		'default' => 'right',
	);

	$data['content']['fields']['blog_display'] = array(
		'type'        => 'select',
		'label'       => esc_html__( 'Blog Display', 'miniva' ),
		'default'     => miniva_get_blog_display_default(),
		'description' => esc_html__( 'Choose between a full post or an excerpt for the blog and archive pages.', 'miniva' ),
		'choices'     => array(
			'content' => esc_html__( 'Full post', 'miniva' ),
			'excerpt' => esc_html__( 'Post excerpt', 'miniva' ),
		),
	);

	$data['content']['fields']['excerpt_length'] = array(
		'type'    => 'text',
		'label'   => esc_html__( 'Excerpt Length', 'miniva' ),
		'default' => 20,
	);

	$data['content']['fields']['welcome_text'] = array(
		'type'        => 'textarea',
		'label'       => esc_html__( 'Welcome Text', 'miniva' ),
		'description' => esc_html__( 'A short text dislayed on front page.', 'miniva' ),
		'default'     => '',
	);

	$data['content']['fields']['featured_image_nocrop'] = array(
		'type'    => 'checkbox',
		'label'   => esc_html__( 'Show full height featured image in single posts & pages', 'miniva' ),
		'default' => false,
	);

	$data['widgetized']['title'] = esc_html__( 'Widgetized Page', 'miniva' );

	$data['widgetized']['fields']['widgetized_page_hide_title_content'] = array(
		'type'            => 'checkbox',
		'label'           => esc_html__( 'Hide page title and content in widgetized page template', 'miniva' ),
		'default'         => false,
		'active_callback' => 'miniva_is_widgetized_page',
	);

	$data['colors']['fields']['accent_color'] = array(
		'type'              => 'color',
		'mode'              => 'hue',
		'sanitize_callback' => 'absint',
		'default'           => 349,
		'label'             => esc_html__( 'Link / accent color', 'miniva' ),
		'priority'          => 10,
	);

	$data['colors']['fields']['submenu_color'] = array(
		'type'     => 'radio',
		'label'    => esc_html__( 'Submenu Color', 'miniva' ),
		'choices'  => array(
			'light' => esc_html__( 'Light', 'miniva' ),
			'dark'  => esc_html__( 'Dark', 'miniva' ),
		),
		'default'  => 'dark',
		'priority' => 11,
	);

	$data['more']['title'] = esc_html__( 'More', 'miniva' );

	$data['more']['priority'] = 201;

	$data['more']['fields']['more'] = array(
		'type'      => 'custom',
		'label'     => esc_html__( 'Miniva Pro', 'miniva' ),
		'content'   => __( 'Get Miniva Pro Add-on for additional features including WooCommerce support, dark color scheme, featured slider, and many more...', 'miniva' ),
		'link'      => esc_html__( 'https://tajam.id/miniva-pro/', 'miniva' ),
		'link_text' => esc_html__( 'Learn more about Miniva Pro', 'miniva' ),
	);

	return apply_filters( 'miniva_customizer_data', $data );
}
