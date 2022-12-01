<?php

/* Add customizer panels, sections, settings, and controls */
add_action( 'customize_register', 'ct_period_add_customizer_content' );

function ct_period_add_customizer_content( $wp_customize ) {

	/***** Reorder default sections *****/

	$wp_customize->get_section( 'title_tagline' )->priority = 2;

	// check if exists in case user has no pages
	if ( is_object( $wp_customize->get_section( 'static_front_page' ) ) ) {
		$wp_customize->get_section( 'static_front_page' )->priority = 5;
	}

	/***** Add PostMessage Support *****/

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/********** Add Panels **********/

	// Add panel for colors
	if ( method_exists( 'WP_Customize_Manager', 'add_panel' ) ) {

		$wp_customize->add_panel( 'ct_period_layout_panel', array(
			'priority'    => 40,
			'title'       => __( 'Layout', 'period' ),
			'description' => __( 'Change your layouts across the site', 'period' )
		) );
	}
	
	/***** Logo Upload *****/

	// section
	$wp_customize->add_section( 'ct_period_logo_upload', array(
		'title'    => __( 'Logo', 'period' ),
		'priority' => 20
	) );
	// Upload - setting
	$wp_customize->add_setting( 'logo_upload', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	// Upload - control
	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'logo_image', array(
			'label'    => __( 'Upload a logo', 'period' ),
			'section'  => 'ct_period_logo_upload',
			'settings' => 'logo_upload'
		)
	) );
	// Size - setting
	$wp_customize->add_setting( 'logo_size', array(
		'default'           => '168',
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage'
	) );
	// Size - control
	$wp_customize->add_control( 'logo_size', array(
		'label'    => __( 'Adjust the size of the logo', 'period' ),
		'section'  => 'ct_period_logo_upload',
		'settings' => 'logo_size',
		'type'     => 'range',
		'input_attrs' => array(
			'min'  => 5,
			'max'  => 750,
			'step' => 1
		)
	) );

	/***** Social Media Icons *****/

	// get the social sites array
	$social_sites = ct_period_social_array();

	// set a priority used to order the social sites
	$priority = 5;

	// section
	$wp_customize->add_section( 'ct_period_social_media_icons', array(
		'title'       => __( 'Social Media Icons', 'period' ),
		'priority'    => 25,
		'description' => __( 'Add the URL for each of your social profiles.', 'period' )
	) );

	// create a setting and control for each social site
	foreach ( $social_sites as $social_site => $value ) {
		// if email icon
		if ( $social_site == 'email' ) {
			// setting
			$wp_customize->add_setting( $social_site, array(
				'sanitize_callback' => 'ct_period_sanitize_email'
			) );
			// control
			$wp_customize->add_control( $social_site, array(
				'label'    => __( 'Email Address', 'period' ),
				'section'  => 'ct_period_social_media_icons',
				'priority' => $priority
			) );
		} else if ( $social_site == 'phone' ) {
			// setting
			$wp_customize->add_setting( $social_site, array(
				'sanitize_callback' => 'ct_period_sanitize_phone'
			) );
			// control
			$wp_customize->add_control( $social_site, array(
				'label'    => __( 'Phone', 'period' ),
				'section'     => 'ct_period_social_media_icons',
				'priority'    => $priority,
				'type'        => 'text'
			) );
		} else {

			$label = ucfirst( $social_site );

			if ( $social_site == 'rss' ) {
				$label = __('RSS', 'period');
			} elseif ( $social_site == 'researchgate' ) {
				$label = __('ResearchGate', 'period');
			} elseif ( $social_site == 'diaspora' ) {
				$label = __('diaspora*', 'period');
			} elseif ( $social_site == 'imdb' ) {
				$label = __('IMDB', 'period');
			} elseif ( $social_site == 'soundcloud' ) {
				$label = __('SoundCloud', 'period');
			} elseif ( $social_site == 'slideshare' ) {
				$label = __('SlideShare', 'period');
			} elseif ( $social_site == 'codepen' ) {
				$label = __('CodePen', 'period');
			} elseif ( $social_site == 'stumbleupon' ) {
				$label = __('StumbleUpon', 'period');
			} elseif ( $social_site == 'deviantart' ) {
				$label = __('DeviantArt', 'period');
			} elseif ( $social_site == 'hacker-news' ) {
				$label = __('Hacker News', 'period');
			} elseif ( $social_site == 'google-wallet' ) {
				$label = __('Google Wallet', 'period');
			} elseif ( $social_site == 'whatsapp' ) {
				$label = __('WhatsApp', 'period');
			} elseif ( $social_site == 'qq' ) {
				$label = __('QQ', 'period');
			} elseif ( $social_site == 'vk' ) {
				$label = __('VK', 'period');
			} elseif ( $social_site == 'wechat' ) {
				$label = __('WeChat', 'period');
			} elseif ( $social_site == 'tencent-weibo' ) {
				$label = __('Tencent Weibo', 'period');
			} elseif ( $social_site == 'paypal' ) {
				$label = __('PayPal', 'period');
			} elseif ( $social_site == 'ok-ru' ) {
				$label = __('OK.ru', 'period');
			} elseif ( $social_site == 'stack-overflow' ) {
				$label = __('Stack Overflow', 'period');
			} elseif ( $social_site == 'artstation' ) {
				$label = __('ArtStation', 'period');
			} elseif ( $social_site == 'email-form' ) {
				$label = __('Contact Form', 'period');
			} elseif ( $social_site == 'tiktok' ) {
				$label = __('TikTok', 'period');
			}

			if ( $social_site == 'skype' ) {
				// setting
				$wp_customize->add_setting( $social_site, array(
					'sanitize_callback' => 'ct_period_sanitize_skype'
				) );
				// control
				$wp_customize->add_control( $social_site, array(
					'type'        => 'url',
					'label'       => $label,
					'description' => sprintf( __( 'Accepts Skype link protocol (<a href="%s" target="_blank">learn more</a>)', 'period' ), 'https://www.competethemes.com/blog/skype-links-wordpress/' ),
					'section'     => 'ct_period_social_media_icons',
					'priority'    => $priority
				) );
			} else {
				// setting
				$wp_customize->add_setting( $social_site, array(
					'sanitize_callback' => 'esc_url_raw'
				) );
				// control
				$wp_customize->add_control( $social_site, array(
					'type'     => 'url',
					'label'    => $label,
					'section'  => 'ct_period_social_media_icons',
					'priority' => $priority
				) );
			}
		}
		// increment the priority for next site
		$priority = $priority + 5;
	}
	// Custom icon 1
	$wp_customize->add_setting( 'social_icon_custom_1_name', array(
		'sanitize_callback' => 'ct_period_sanitize_text'
	) );
	$wp_customize->add_control( 'social_icon_custom_1_name', array(
		'label'    => __('Custom icon 1 Name', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_1_name',
		'type'     => 'text',
		'priority' => $priority + 5
	) );
	$wp_customize->add_setting( 'social_icon_custom_1', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control( 'social_icon_custom_1', array(
		'label'    => __('Custom icon 1 URL', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_1',
		'type'     => 'url',
		'priority' => $priority + 6
	) );
	$wp_customize->add_setting( 'social_icon_custom_1_image', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'social_icon_custom_1_image', array(
		'label'    => __( 'Custom icon 1 image', 'period' ),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_1_image',
		'priority' => $priority + 7
	)));
	$wp_customize->add_setting( 'social_icon_custom_1_size', array(
		'default'			=> '20',
		'sanitize_callback' => 'absint'
	) );
	$wp_customize->add_control( 'social_icon_custom_1_size', array(
		'label'    => __('Custom icon 1 size (px)', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_1_size',
		'type'     => 'number',
		'priority' => $priority + 8
	) );
	// Custom icon 2
	$wp_customize->add_setting( 'social_icon_custom_2_name', array(
		'sanitize_callback' => 'ct_period_sanitize_text'
	) );
	$wp_customize->add_control( 'social_icon_custom_2_name', array(
		'label'    => __('Custom icon 2 Name', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_2_name',
		'type'     => 'text',
		'priority' => $priority + 9
	) );
	$wp_customize->add_setting( 'social_icon_custom_2', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control( 'social_icon_custom_2', array(
		'label'    => __('Custom icon 2 URL', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_2',
		'type'     => 'url',
		'priority' => $priority + 10
	) );
	$wp_customize->add_setting( 'social_icon_custom_2_image', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'social_icon_custom_2_image', array(
		'label'    => __( 'Custom icon 2 image', 'period' ),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_2_image',
		'priority' => $priority + 11
	)));
	$wp_customize->add_setting( 'social_icon_custom_2_size', array(
		'default'			=> '20',
		'sanitize_callback' => 'absint'
	) );
	$wp_customize->add_control( 'social_icon_custom_2_size', array(
		'label'    => __('Custom icon 2 size (px)', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_2_size',
		'type'     => 'number',
		'priority' => $priority + 12
	) );
	// Custom icon 3
	$wp_customize->add_setting( 'social_icon_custom_3_name', array(
		'sanitize_callback' => 'ct_period_sanitize_text'
	) );
	$wp_customize->add_control( 'social_icon_custom_3_name', array(
		'label'    => __('Custom icon 3 Name', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_3_name',
		'type'     => 'text',
		'priority' => $priority + 13
	) );
	$wp_customize->add_setting( 'social_icon_custom_3', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control( 'social_icon_custom_3', array(
		'label'    => __('Custom icon 3 URL', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_3',
		'type'     => 'url',
		'priority' => $priority + 14
	) );
	$wp_customize->add_setting( 'social_icon_custom_3_image', array(
		'sanitize_callback' => 'esc_url_raw'
	) );
	$wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'social_icon_custom_3_image', array(
		'label'    => __( 'Custom icon 3 image', 'period' ),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_3_image',
		'priority' => $priority + 15
	)));
	$wp_customize->add_setting( 'social_icon_custom_3_size', array(
		'default'			=> '20',
		'sanitize_callback' => 'absint'
	) );
	$wp_customize->add_control( 'social_icon_custom_3_size', array(
		'label'    => __('Custom icon 3 size (px)', 'period'),
		'section'  => 'ct_period_social_media_icons',
		'settings' => 'social_icon_custom_3_size',
		'type'     => 'number',
		'priority' => $priority + 16
	) );

	/***** Search Bar *****/

	// section
	$wp_customize->add_section( 'period_search_bar', array(
		'title'    => __( 'Search Bar', 'period' ),
		'priority' => 37
	) );
	// setting
	$wp_customize->add_setting( 'search_bar', array(
		'default'           => 'hide',
		'sanitize_callback' => 'ct_period_sanitize_all_show_hide_settings'
	) );
	// control
	$wp_customize->add_control( 'search_bar', array(
		'type'    => 'radio',
		'label'   => __( 'Show search bar at top of site?', 'period' ),
		'section' => 'period_search_bar',
		'setting' => 'search_bar',
		'choices' => array(
			'show' => __( 'Show', 'period' ),
			'hide' => __( 'Hide', 'period' )
		),
	) );

	/***** Layout *****/

	// section
	$wp_customize->add_section( 'period_layout', array(
		'title'       => __( 'Posts', 'period' ),
		'priority'    => 1,
		'description' => sprintf( __( 'Want more layouts? Check out the <a target="_blank" href="%1$s">%2$s Pro plugin</a>.', 'period' ), 'https://www.competethemes.com/period-pro/', wp_get_theme( get_template() ) ),
		'panel'				=> 'ct_period_layout_panel'
	) );
	// setting
	$wp_customize->add_setting( 'layout', array(
		'default'           => 'right',
		'sanitize_callback' => 'ct_period_sanitize_layout_settings',
		'transport'         => 'postMessage'
	) );
	// control
	$wp_customize->add_control( 'layout', array(
		'label'    => __( 'Choose your layout', 'period' ),
		'section'  => 'period_layout',
		'settings' => 'layout',
		'type'     => 'radio',
		'choices'  => array(
			'right' => __( 'Right sidebar', 'period' ),
			'left'  => __( 'Left sidebar', 'period' )
		)
	) );
	// section
	$wp_customize->add_section( 'period_layout_pages', array(
		'title'       => __( 'Pages', 'period' ),
		'priority'    => 2,
		'description' => sprintf( __( 'Want more layouts? Check out the <a target="_blank" href="%1$s">%2$s Pro plugin</a>.', 'period' ), 'https://www.competethemes.com/period-pro/', wp_get_theme( get_template() ) ),
		'panel'				=> 'ct_period_layout_panel'
	) );
	// setting
	$wp_customize->add_setting( 'layout_pages', array(
		'default'           => 'right',
		'sanitize_callback' => 'ct_period_sanitize_layout_settings',
		'transport'         => 'postMessage'
	) );
	// control
	$wp_customize->add_control( 'layout_pages', array(
		'label'    => __( 'Choose your layout', 'period' ),
		'section'  => 'period_layout_pages',
		'settings' => 'layout_pages',
		'type'     => 'radio',
		'choices'  => array(
			'right' => __( 'Right sidebar', 'period' ),
			'left'  => __( 'Left sidebar', 'period' )
		)
	) );
	// section
	$wp_customize->add_section( 'period_layout_blog', array(
		'title'       => __( 'Blog', 'period' ),
		'priority'    => 3,
		'description' => sprintf( __( 'Want more layouts? Check out the <a target="_blank" href="%1$s">%2$s Pro plugin</a>.', 'period' ), 'https://www.competethemes.com/period-pro/', wp_get_theme( get_template() ) ),
		'panel'				=> 'ct_period_layout_panel'
	) );
	// setting
	$wp_customize->add_setting( 'layout_blog', array(
		'default'           => 'right',
		'sanitize_callback' => 'ct_period_sanitize_layout_settings',
		'transport'         => 'postMessage'
	) );
	// control
	$wp_customize->add_control( 'layout_blog', array(
		'label'    => __( 'Choose your layout', 'period' ),
		'section'  => 'period_layout_blog',
		'settings' => 'layout_blog',
		'type'     => 'radio',
		'choices'  => array(
			'right' => __( 'Right sidebar', 'period' ),
			'left'  => __( 'Left sidebar', 'period' )
		)
	) );
	// section
	$wp_customize->add_section( 'period_layout_archives', array(
		'title'       => __( 'Archives', 'period' ),
		'priority'    => 4,
		'description' => sprintf( __( 'Want more layouts? Check out the <a target="_blank" href="%1$s">%2$s Pro plugin</a>.', 'period' ), 'https://www.competethemes.com/period-pro/', wp_get_theme( get_template() ) ),
		'panel'				=> 'ct_period_layout_panel'
	) );
	// setting
	$wp_customize->add_setting( 'layout_archives', array(
		'default'           => 'right',
		'sanitize_callback' => 'ct_period_sanitize_layout_settings',
		'transport'         => 'postMessage'
	) );
	// control
	$wp_customize->add_control( 'layout_archives', array(
		'label'    => __( 'Choose your layout', 'period' ),
		'section'  => 'period_layout_archives',
		'settings' => 'layout_archives',
		'type'     => 'radio',
		'choices'  => array(
			'right' => __( 'Right sidebar', 'period' ),
			'left'  => __( 'Left sidebar', 'period' )
		)
	) );

	/***** Blog *****/

	// section
	$wp_customize->add_section( 'period_blog', array(
		'title'    => _x( 'Blog', 'noun: blog section', 'period' ),
		'priority' => 45
	) );
	// setting
	$wp_customize->add_setting( 'full_post', array(
		'default'           => 'no',
		'sanitize_callback' => 'ct_period_sanitize_yes_no_settings'
	) );
	// control
	$wp_customize->add_control( 'full_post', array(
		'label'    => __( 'Show full posts on blog?', 'period' ),
		'section'  => 'period_blog',
		'settings' => 'full_post',
		'type'     => 'radio',
		'choices'  => array(
			'yes' => __( 'Yes', 'period' ),
			'no'  => __( 'No', 'period' )
		)
	) );
	// setting
	$wp_customize->add_setting( 'excerpt_length', array(
		'default'           => '25',
		'sanitize_callback' => 'absint'
	) );
	// control
	$wp_customize->add_control( 'excerpt_length', array(
		'label'    => __( 'Excerpt word count', 'period' ),
		'section'  => 'period_blog',
		'settings' => 'excerpt_length',
		'type'     => 'number'
	) );
	// Read More text - setting
	$wp_customize->add_setting( 'read_more_text', array(
		'default'           => __( 'Continue Reading', 'period' ),
		'sanitize_callback' => 'ct_period_sanitize_text'
	) );
	// Read More text - control
	$wp_customize->add_control( 'read_more_text', array(
		'label'    => __( 'Read More button text', 'period' ),
		'section'  => 'period_blog',
		'settings' => 'read_more_text',
		'type'     => 'text'
	) );

	/***** Display Controls *****/

	// section
	$wp_customize->add_section( 'period_display', array(
		'title'       => __( 'Show/Hide Elements', 'period' ),
		'priority'    => 55,
		'description' => sprintf( __( 'Want more options like these? Check out the <a target="_blank" href="%1$s">%2$s Pro plugin</a>.', 'period' ), 'https://www.competethemes.com/period-pro/', wp_get_theme( get_template() ) )
	) );
	// setting - post author
	$wp_customize->add_setting( 'display_post_author', array(
		'default'           => 'show',
		'sanitize_callback' => 'ct_period_sanitize_show_hide'
	) );
	// control - post author
	$wp_customize->add_control( 'display_post_author', array(
		'type'    => 'radio',
		'label'   => __( 'Post author name in byline', 'period' ),
		'section' => 'period_display',
		'setting' => 'display_post_author',
		'choices' => array(
			'show' => __( 'Show', 'period' ),
			'hide' => __( 'Hide', 'period' )
		)
	) );
	// setting - post date
	$wp_customize->add_setting( 'display_post_date', array(
		'default'           => 'show',
		'sanitize_callback' => 'ct_period_sanitize_show_hide'
	) );
	// control - post author
	$wp_customize->add_control( 'display_post_date', array(
		'type'    => 'radio',
		'label'   => __( 'Post date in byline', 'period' ),
		'section' => 'period_display',
		'setting' => 'display_post_date',
		'choices' => array(
			'show' => __( 'Show', 'period' ),
			'hide' => __( 'Hide', 'period' )
		)
	) );

	/***** Additional Options  *****/

	// section
	$wp_customize->add_section( 'ct_period_additional_options', array(
		'title'    => __( 'Additional Options', 'period' ),
		'priority' => 70
	) );
	// setting - scroll-to-top arrow
	$wp_customize->add_setting( 'scroll_to_top', array(
		'default'           => 'no',
		'sanitize_callback' => 'ct_period_sanitize_yes_no_settings'
	) );
	// control - scroll-to-top arrow
	$wp_customize->add_control( 'scroll_to_top', array(
		'label'    => __( 'Display Scroll-to-top arrow?', 'period' ),
		'section'  => 'ct_period_additional_options',
		'settings' => 'scroll_to_top',
		'type'     => 'radio',
		'choices'  => array(
			'yes' => __( 'Yes', 'period' ),
			'no'  => __( 'No', 'period' )
		)
	) );
	// setting - last updated
	$wp_customize->add_setting( 'last_updated', array(
		'default'           => 'no',
		'sanitize_callback' => 'ct_period_sanitize_yes_no_settings'
	) );
	// control - last updated
	$wp_customize->add_control( 'last_updated', array(
		'label'    => __( 'Display the date each post was last updated?', 'period' ),
		'section'  => 'ct_period_additional_options',
		'settings' => 'last_updated',
		'type'     => 'radio',
		'choices'  => array(
			'yes' => __( 'Yes', 'period' ),
			'no'  => __( 'No', 'period' )
		)
	) );

	/***** Custom CSS *****/

	if ( function_exists( 'wp_update_custom_css_post' ) ) {
		// Migrate any existing theme CSS to the core option added in WordPress 4.7.
		$css = get_theme_mod( 'custom_css' );
		if ( $css ) {
			$core_css = wp_get_custom_css(); // Preserve any CSS already added to the core option.
			$return = wp_update_custom_css_post( $core_css . $css );
			if ( ! is_wp_error( $return ) ) {
				// Remove the old theme_mod, so that the CSS is stored in only one place moving forward.
				remove_theme_mod( 'custom_css' );
			}
		}
	} else {
		// section
		$wp_customize->add_section( 'period_custom_css', array(
			'title'    => __( 'Custom CSS', 'period' ),
			'priority' => 75
		) );
		// setting
		$wp_customize->add_setting( 'custom_css', array(
			'sanitize_callback' => 'ct_period_sanitize_css',
			'transport'         => 'postMessage'
		) );
		// control
		$wp_customize->add_control( 'custom_css', array(
			'type'     => 'textarea',
			'label'    => __( 'Add Custom CSS Here', 'period' ),
			'section'  => 'period_custom_css',
			'settings' => 'custom_css'
		) );
	}
}

/***** Custom Sanitization Functions *****/

/*
 * Sanitize settings with show/hide as options
 * Used in: search bar
 */
function ct_period_sanitize_all_show_hide_settings( $input ) {

	$valid = array(
		'show' => __( 'Show', 'period' ),
		'hide' => __( 'Hide', 'period' )
	);

	return array_key_exists( $input, $valid ) ? $input : '';
}

/*
 * sanitize email address
 * Used in: Social Media Icons
 */
function ct_period_sanitize_email( $input ) {
	return sanitize_email( $input );
}

// sanitize yes/no settings
function ct_period_sanitize_yes_no_settings( $input ) {

	$valid = array(
		'yes' => __( 'Yes', 'period' ),
		'no'  => __( 'No', 'period' )
	);

	return array_key_exists( $input, $valid ) ? $input : '';
}

function ct_period_sanitize_text( $input ) {
	return wp_kses_post( force_balance_tags( $input ) );
}

function ct_period_sanitize_skype( $input ) {
	return esc_url_raw( $input, array( 'http', 'https', 'skype' ) );
}

function ct_period_sanitize_css( $css ) {
	$css = wp_kses( $css, array( '\'', '\"' ) );
	$css = str_replace( '&gt;', '>', $css );

	return $css;
}

function ct_period_sanitize_show_hide( $input ) {

	$valid = array(
		'show' => __( 'Show', 'period' ),
		'hide' => __( 'Hide', 'period' )
	);

	return array_key_exists( $input, $valid ) ? $input : '';
}

function ct_period_sanitize_layout_settings( $input ) {

	/*
	 * Also allow layouts only included in the premium plugin.
	 * Needs to be done this way b/c sanitize_callback cannot by updated
	 * via get_setting()
	 */
	$valid = array(
		'right'      => __( 'Right sidebar', 'period' ),
		'left'       => __( 'Left sidebar', 'period' ),
		'narrow'     => __( 'No sidebar - Narrow', 'period' ),
		'wide'       => __( 'No sidebar - Wide', 'period' ),
		'two-right'  => __( 'Two column - Right sidebar', 'period' ),
		'two-left'   => __( 'Two column - Left sidebar', 'period' ),
		'two-narrow' => __( 'Two column - No Sidebar - Narrow', 'period' ),
		'two-wide'   => __( 'Two column - No Sidebar - Wide', 'period' )
	);

	return array_key_exists( $input, $valid ) ? $input : '';
}

function ct_period_sanitize_phone( $input ) {
	if ( $input != '' ) {
		return esc_url_raw( 'tel:' . $input, array( 'tel' ) );
	} else {
		return '';
	}
}


function ct_period_customize_preview_js() {
	if ( !function_exists( 'ct_period_pro_init' ) && !(isset($_GET['mailoptin_optin_campaign_id']) || isset($_GET['mailoptin_email_campaign_id'])) ) {
		$url = 'https://www.competethemes.com/period-pro/?utm_source=wp-dashboard&utm_medium=Customizer&utm_campaign=Period%20Pro%20-%20Customizer';
		$content = "<script>jQuery('#customize-info').prepend('<div class=\"upgrades-ad\"><a href=\"". $url ."\" target=\"_blank\">Customize Colors with Period Pro <span>&rarr;</span></a></div>')</script>";
		echo apply_filters('ct_period_customizer_ad', $content);
	}
}
add_action('customize_controls_print_footer_scripts', 'ct_period_customize_preview_js');