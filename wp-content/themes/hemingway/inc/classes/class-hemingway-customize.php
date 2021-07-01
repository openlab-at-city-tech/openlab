<?php

/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Hemingway_Customize' ) ) : 
	class Hemingway_Customize {

		public static function register( $wp_customize ) {

			/* ------------------------------------------------------------------------
			 * Accent Color
			 * ------------------------------------------------------------------------ */
			
			$wp_customize->add_setting( 'accent_color', array(
				'default' 			=> '#1abc9c',
				'type' 				=> 'theme_mod',
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color'
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hemingway_accent_color', array(
				'label' 		=> __( 'Accent Color', 'hemingway' ), 
				'section' 		=> 'colors',
				'settings' 		=> 'accent_color', 
				'priority' 		=> 10, 
			) ) );

			/* ------------------------------------------------------------------------
			 * Hemingway Logo
			 * ------------------------------------------------------------------------ */

			// Only display the Customizer section for the hemingway_logo setting if the setting already has a value.
			// This means that site owners with existing logos can remove them, but new site owners can't add them.
			// Since v2.0.0, the core custom_logo setting (in the Site Identity Customizer panel) should be used instead.

			if ( get_theme_mod( 'hemingway_logo' ) ) {

				$wp_customize->add_section( 'hemingway_logo_section', array(
					'title'       => __( 'Logo', 'hemingway' ),
					'priority'    => 40,
					'description' => __( 'Upload a logo to replace the default site name and description in the header','hemingway' ),
				) );		

				$wp_customize->add_setting( 'hemingway_logo', array( 
					'sanitize_callback' => 'esc_url_raw'
				) );
				
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hemingway_logo', array(
					'label'    => __( 'Logo', 'hemingway' ),
					'section'  => 'hemingway_logo_section',
					'settings' => 'hemingway_logo',
				) ) );

			}

		}

		// Output CSS for the color setting in the header
		public static function custom_css_output() {

			$default_color = '#1abc9c';
			$accent_color = get_theme_mod( 'accent_color', $default_color );

			// Only proceed if a custom color is set.
			if ( $accent_color == $default_color || ! $accent_color ) return;

			// An array storing all of the elements with custom accent color, sorted by the CSS property to modify.
			$properties = apply_filters( 'hemingway_accent_color_elements', array(
				'background-color' 		=> array( '::selection', '.featured-media .sticky-post', 'fieldset legend', ':root .has-accent-background-color', 'button:hover', '.button:hover', '.faux-button:hover', 'a.more-link:hover', '.wp-block-button__link:hover', '.is-style-outline .wp-block-button__link.has-accent-color:hover', '.wp-block-file__button:hover', 'input[type="button"]:hover', 'input[type="reset"]:hover', 'input[type="submit"]:hover', '.post-tags a:hover', '.content #respond input[type="submit"]:hover', '.search-form .search-submit', '.sidebar .tagcloud a:hover', '.footer .tagcloud a:hover' ),
				'border-color'			=> array( '.is-style-outline .wp-block-button__link.has-accent-color:hover' ),
				'border-right-color'	=> array( '.post-tags a:hover:after' ),
				'color' 				=> array( 'a', '.blog-title a:hover', '.blog-menu a:hover', '.post-title a:hover', '.post-meta a:hover', '.blog .format-quote blockquote cite a:hover', ':root .has-accent-color', '.post-categories a', '.post-categories a:hover', '.post-nav a:hover', '.archive-nav a:hover', '.comment-meta-content cite a:hover', '.comment-meta-content p a:hover', '.comment-actions a:hover', '#cancel-comment-reply-link', '#cancel-comment-reply-link:hover', '.widget-title a', '.widget-title a:hover', '.widget_text a', '.widget_text a:hover', '.widget_rss a', '.widget_rss a:hover', '.widget_archive a', '.widget_archive a:hover', '.widget_meta a', '.widget_meta a:hover', '.widget_recent_comments a', '.widget_recent_comments a:hover', '.widget_pages a', '.widget_pages a:hover', '.widget_links a', '.widget_links a:hover', '.widget_recent_entries a', '.widget_recent_entries a:hover', '.widget_categories a', '.widget_categories a:hover', '#wp-calendar a', '#wp-calendar a:hover', '#wp-calendar tfoot a:hover', '.wp-calendar-nav a:hover', '.widgetmore a', '.widgetmore a:hover' ),
			) );

			$css = '<style type="text/css"><!-- Customizer CSS -->';
			foreach ( $properties as $property => $selectors ) {
				foreach ( $selectors as $selector ) {
					$css .= sprintf( '%s { %s: %s; }', $selector, $property, $accent_color );
				}
			}
			$css .= '</style><!-- /Customizer CSS -->';

			echo $css;

		}
	}

	// Setup the Theme Customizer settings and controls...
	add_action( 'customize_register', array( 'Hemingway_Customize', 'register' ) );

	// Output custom CSS to live site
	add_action( 'wp_head', array( 'Hemingway_Customize', 'custom_css_output' ) );

endif;
