<?php

/* CUSTOMIZER SETTINGS
------------------------------------------------ */

if ( ! class_exists( 'Hamilton_Customize' ) ) :
	class Hamilton_Customize {

		public static function register( $wp_customize ) {

			// Add our Customizer section
			$wp_customize->add_section( 'hamilton_options', array(
				'title' 		=> __( 'Theme Options', 'hamilton' ),
				'priority' 		=> 35,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Customize the theme settings for Hamilton.', 'hamilton' ),
			) );

			// Dark Mode
			$wp_customize->add_setting( 'hamilton_dark_mode', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'hamilton_sanitize_checkbox',
				'transport'			=> 'postMessage'
			) );

			$wp_customize->add_control( 'hamilton_dark_mode', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'colors', // Default WP section added by background_color
				'label' 		=> __( 'Dark Mode', 'hamilton' ),
				'description' 	=> __( 'Displays the site with white text and black background. If Background Color is set, only the text color will change.', 'hamilton' ),
			) );
			
			// Always show preview titles
			$wp_customize->add_setting( 'hamilton_alt_nav', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'hamilton_sanitize_checkbox',
				'transport'			=> 'postMessage'
			) );

			$wp_customize->add_control( 'hamilton_alt_nav', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'hamilton_options', // Add a default or your own section
				'label' 		=> __( 'Show Primary Menu in the Header', 'hamilton' ),
				'description' 	=> __( 'Replace the navigation toggle in the header with the Primary Menu on desktop.', 'hamilton' ),
			) );
			
			// Maximum number of columns
			$wp_customize->add_setting( 'hamilton_max_columns', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'hamilton_sanitize_checkbox',
				'transport'			=> 'postMessage'
			) );

			$wp_customize->add_control( 'hamilton_max_columns', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'hamilton_options',
				'label' 		=> __( 'Three Columns', 'hamilton' ),
				'description' 	=> __( 'Check to use three columns in the post grid on desktop.', 'hamilton' ),
			) );
			
			// Always show preview titles
			$wp_customize->add_setting( 'hamilton_show_titles', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'hamilton_sanitize_checkbox',
				'transport'			=> 'postMessage'
			) );

			$wp_customize->add_control( 'hamilton_show_titles', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'hamilton_options', // Add a default or your own section
				'label' 		=> __( 'Show Preview Titles', 'hamilton' ),
				'description' 	=> __( 'Check to always show the titles in the post previews.', 'hamilton' ),
			) );
			
			// Set the home page title
			$wp_customize->add_setting( 'hamilton_home_title', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'sanitize_textarea_field',
			) );

			$wp_customize->add_control( 'hamilton_home_title', array(
				'type' 			=> 'textarea',
				'section' 		=> 'hamilton_options', // Add a default or your own section
				'label' 		=> __( 'Front Page Title', 'hamilton' ),
				'description' 	=> __( 'The title you want shown on the front page when the "Front page displays" setting is set to "Your latest posts" in Settings > Reading.', 'hamilton' ),
			) );
			
			// Make built-in controls use live JS preview
			$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
			$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
			
			// SANITATION

			// Sanitize boolean for checkbox
			function hamilton_sanitize_checkbox( $checked ) {
				return ( ( isset( $checked ) && true == $checked ) ? true : false );
			}
			
		}

		// Initiate the live preview JS
		public static function live_preview() {
			wp_enqueue_script( 'hamilton-themecustomizer', get_template_directory_uri() . '/assets/js/theme-customizer.js', array(  'jquery', 'customize-preview', 'masonry' ), '', true );
		}

	}

	// Setup the Theme Customizer settings and controls
	add_action( 'customize_register', array( 'Hamilton_Customize', 'register' ) );

	// Enqueue live preview javascript in Theme Customizer admin screen
	add_action( 'customize_preview_init', array( 'Hamilton_Customize', 'live_preview' ) );

endif;
