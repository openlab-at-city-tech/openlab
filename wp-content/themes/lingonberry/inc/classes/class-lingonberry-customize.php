<?php

/* ---------------------------------------------------------------------------------------------
   LINGONBERRY THEME OPTIONS
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'lingonberry_customize' ) ) : 
	class lingonberry_customize {
		
		public static function register( $wp_customize ) {

			// Add a setting for accent color
			$wp_customize->add_setting( 'accent_color', array(
				'default' 			=> '#ff706c', 
				'sanitize_callback' => 'sanitize_hex_color',
				'type' 				=> 'theme_mod', 
			) );

			// Add a control to go along with the accent color setting
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lingonberry_accent_color', array(
				'label' 	=> __( 'Accent Color', 'lingonberry' ), 
				'priority' 	=> 10,
				'section' 	=> 'colors', 
				'settings' 	=> 'accent_color', 
			) ) );

		}

		// Function handling our header output of styles
		public static function header_output() {

			$default_color = '#ff706c';
			$accent = get_theme_mod( 'accent_color', $default_color );

			if ( $accent == $default_color || ! $accent ) return;

			// An array storing all of the elements with custom accent color, sorted by the CSS property to modify.
			$properties = apply_filters( 'lingonberry_accent_color_elements', array(
				'background-color' 		=> array( '.header', '.post-bubbles a:hover', '.post-nav a:hover', '.archive-nav a:hover', '.widget_tag_cloud a:hover', 'fieldset legend', ':root .has-accent-background-color', '.comment-actions a:hover', 'a#cancel-comment-reply-link:hover', 'button:hover', '.button:hover', '.wp-block-button__link:hover', '.wp-block-file__button:hover', 'input[type="button"]:hover', 'input[type="reset"]:hover', 'input[type="submit"]:hover' ),
				'color' 				=> array( 'a', '.comment-meta-content cite a:hover', '.comment-meta-content p a:hover', '.flexslider:hover .flex-next:active', '.flexslider:hover .flex-prev:active', ':root .has-accent-color' ),
			) );

			$css = '<!-- Customizer CSS --><style type="text/css">';
			foreach ( $properties as $property => $selectors ) {
				foreach ( $selectors as $selector ) {
					$css .= sprintf( '%s { %s: %s; }', $selector, $property, $accent );
				}
			}
			$css .= '</style><!-- /Customizer CSS -->';

			echo $css;

		}

	}

	// Setup the Theme Customizer settings and controls
	add_action( 'customize_register', array( 'lingonberry_customize', 'register' ) );

	// Output custom CSS
	add_action( 'wp_head', array( 'lingonberry_customize', 'header_output' ) );

endif;