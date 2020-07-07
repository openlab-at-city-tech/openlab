<?php

/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Koji_Customize' ) ) :
	class Koji_Customize {

		public static function koji_register( $wp_customize ) {

			/* 2X Header Logo ----------------------------- */

			$wp_customize->add_setting( 'koji_retina_logo', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
				'transport'			=> 'postMessage',
			) );

			$wp_customize->add_control( 'koji_retina_logo', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'title_tagline',
				'priority'		=> 10,
				'label' 		=> __( 'Retina logo', 'koji' ),
				'description' 	=> __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'koji' ),
			) );

			/* ------------------------------------
			 * Fallback Image Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_image_options', array(
				'title' 		=> __( 'Images', 'koji' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for images in Koji.', 'koji' ),
			) );

			// Activate low-resolution images setting
			$wp_customize->add_setting( 'koji_activate_low_resolution_images', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'koji_activate_low_resolution_images', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_image_options',
				'priority'		=> 5,
				'label' 		=> __( 'Use Low-Resolution Images', 'koji' ),
				'description'	=> __( 'Checking this will decrease load times, but also make images look less sharp on high-resolution screens.', 'koji' ),
			) );

			// Fallback image setting
			$wp_customize->add_setting( 'koji_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'absint'
			) );

			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'koji_fallback_image', array(
				'label'			=> __( 'Fallback Image', 'koji' ),
				'description'	=> __( 'The selected image will be used when a post is missing a featured image. A default fallback image included in the theme will be used if no image is set.', 'koji' ),
				'priority'		=> 10,
				'mime_type'		=> 'image',
				'section' 		=> 'koji_image_options',
			) ) );

			// Disable fallback image setting
			$wp_customize->add_setting( 'koji_disable_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'koji_disable_fallback_image', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_image_options',
				'priority'		=> 15,
				'label' 		=> __( 'Disable Fallback Image', 'koji' )
			) );

			/* ------------------------------------
			 * Post Meta Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_post_meta_options', array(
				'title' 		=> __( 'Post Meta', 'koji' ),
				'priority' 		=> 41,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Choose which meta information to display in Koji.', 'koji' ),
			) );

			/* Post Meta Setting ----------------------------- */

			$post_meta_choices = apply_filters( 'koji_post_meta_choices_in_the_customizer', array(
				'author'		=> __( 'Author', 'koji' ),
				'categories'	=> __( 'Categories', 'koji' ),
				'comments'		=> __( 'Comments', 'koji' ),
				'edit-link'		=> __( 'Edit link (for logged in users)', 'koji' ),
				'post-date'		=> __( 'Post date', 'koji' ),
				'sticky'		=> __( 'Sticky status', 'koji' ),
				'tags'			=> __( 'Tags', 'koji' ),
			) );

			// Post Meta Single Setting
			$wp_customize->add_setting( 'koji_post_meta_single', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date', 'categories' ),
				'sanitize_callback' => 'koji_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Koji_Customize_Control_Checkbox_Multiple( $wp_customize, 'koji_post_meta_single', array(
				'section' 		=> 'koji_post_meta_options',
				'label'   		=> __( 'Post Meta On Single:', 'koji' ),
				'description'	=> __( 'Select the post meta values to show on single posts.', 'koji' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			// Post Meta Preview Setting
			$wp_customize->add_setting( 'koji_post_meta_preview', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date', 'comments' ),
				'sanitize_callback' => 'koji_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Koji_Customize_Control_Checkbox_Multiple( $wp_customize, 'koji_post_meta_preview', array(
				'section' 		=> 'koji_post_meta_options',
				'label'   		=> __( 'Post Meta In Previews:', 'koji' ),
				'description'	=> __( 'Select the post meta values to show in previews.', 'koji' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			/* ------------------------------------
			 * Pagination Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_pagination_options', array(
				'title' 		=> __( 'Pagination', 'koji' ),
				'priority' 		=> 45,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Choose which type of pagination to display.', 'koji' ),
			) );

			/* Pagination Type Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_pagination_type', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => 'button',
				'sanitize_callback' => 'koji_sanitize_radio',
			) );

			$wp_customize->add_control( 'koji_pagination_type', array(
				'type'			=> 'radio',
				'section' 		=> 'koji_pagination_options',
				'label'   		=> __( 'Pagination Type:', 'koji' ),
				'choices' 		=> array(
					'button'		=> __( 'Load more on button click', 'koji' ),
					'scroll'		=> __( 'Load more on scroll', 'koji' ),
					'links'			=> __( 'Previous and next page links', 'koji' ),
				),
			) );

			/* ------------------------------------
			 * Search Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_search_options', array(
				'title' 		=> __( 'Search', 'koji' ),
				'priority' 		=> 50,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> '',
			) );

			/* Disable Search Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_disable_search', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'koji_disable_search', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_search_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Search Toggle', 'koji' ),
				'description' 	=> __( 'Check to remove the search toggle from the row of icons.', 'koji' ),
			) );

			/* ------------------------------------
			 * Related Posts Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_related_posts_options', array(
				'title' 		=> __( 'Related Posts', 'koji' ),
				'priority' 		=> 60,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> '',
			) );

			/* Disable Related Posts Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_disable_related_posts', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'koji_disable_related_posts', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_related_posts_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Related Posts', 'koji' ),
				'description' 	=> __( 'Check to hide the related posts section on single posts.', 'koji' ),
			) );

			/* Sanitation functions ----------------------------- */

			// Sanitize boolean for checkbox
			function koji_sanitize_checkbox( $checked ) {
				return ( ( isset( $checked ) && true == $checked ) ? true : false );
			}

			// Sanitize booleans for multiple checkboxes
			function koji_sanitize_multiple_checkboxes( $values ) {
				$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
				return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
			}

			function koji_sanitize_radio( $input, $setting ) {
				$input = sanitize_key( $input );
				$choices = $setting->manager->get_control( $setting->id )->choices;
				return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
			}

		}

		// Initiate the customize controls js
		public static function koji_customize_controls() {
			wp_enqueue_script( 'koji-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array( 'jquery', 'customize-controls' ), '', true );
		}

	}

	// Setup the Theme Customizer settings and controls
	add_action( 'customize_register', array( 'Koji_Customize', 'koji_register' ) );

	// Enqueue customize controls javascript in Theme Customizer admin screen
	add_action( 'customize_controls_init', array( 'Koji_Customize', 'koji_customize_controls' ) );

endif;
