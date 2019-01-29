<?php
/**
 * Class: EGF_Frontend
 *
 * This file is responsible for retrieving all of
 * the options and outputting any appropriate styles
 * for the theme.
 *
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
if ( ! class_exists( 'EGF_Frontend' ) ) :
	class EGF_Frontend {
		
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.2
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.2
		 *
		 */
		protected $plugin_screen_hook_suffix = null;
		
		/**
		 * Constructor Function
		 * 
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		function __construct() {
			/**
			 * Call $plugin_slug from public plugin class.
			 *
			 */
			$plugin = Easy_Google_Fonts::get_instance();
			$this->plugin_slug = $plugin->get_plugin_slug();
			$this->register_actions();		
			$this->register_filters();
		}	

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * We add a high action to wp_head to ensure
		 * that our styles are outputted as late as 
		 * possible.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_actions() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylesheets' ) );
			add_action( 'wp_head', array( $this, 'output_styles' ), 999 );
		}

		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_filters() {

		}

		/**
		 * Enqueue Font Stylesheets
		 *
		 * Enqueues the required stylesheet for the selected google
		 * fonts. By using wp_enqueue_style() we can ensure that 
		 * the stylesheet for each font is only being included on
		 * the page once. 
		 * 
		 * Update: This function now combines the call to 
		 *     google in one http request. It now uses 
		 *     esc_url_raw()
		 *
		 * @link http://codex.wordpress.org/Function_Reference/wp_register_style 	wp_register_style()
		 *
		 * @global $wp_customize
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function enqueue_stylesheets() {
			global $wp_customize;

			$transient         = isset( $wp_customize ) ? false : true;
			$options           = EGF_Register_Options::get_options( $transient );
			$stylesheet_handle = 'tt-easy-google-fonts';
			$font_families     = array();
			$font_family_sets  = array();
			$subsets           = array();
			$protocol          = is_ssl() ? 'https' : 'http';

			if ( $options ) {

				foreach ( $options as $option ) {

					/**
					 * Check Font Type:
					 *
					 * If the current font is a google font then we
					 * add it to the $font_families array and enqueue
					 * the font after we have gone through all of the
					 * $options. Otherwise, if this font is a custom
					 * font enqueued by a developer then we enqueue
					 * it straight away. This allows developers to hook
					 * into the custom filters in this plugin and make
					 * local fonts available in the customizer which
					 * will automatically enqueue on the frontend.
					 * 
					 */
					if ( ! empty( $option['stylesheet_url'] ) ) {

						if ( strpos( $option['stylesheet_url'], 'fonts.googleapis' ) !== false ) {
							
							// Generate array key
							$key = str_replace( ' ', '+', $option['font_name'] );

							// Initialise the font array if this is a new font
							if ( ! isset( $font_families[ $key ] ) ) {
								$font_families[ $key ] = array();
							}

							/**
							 * Add the font weight to the font family if
							 * it hasn't been added already.
							 */
							if ( ! in_array( $option['font_weight_style'], $font_families[ $key ] ) ) {
								$font_families[ $key ][] = $option['font_weight_style'];
							}

							// Populate subset
							if ( ! empty( $option['subset'] ) && ! in_array( $option['subset'], $subsets ) ) {
								$subsets[] = $option['subset'];
							}

						} else {
							
							// Fallback enqueue method
							$subset = empty( $option['subset'] ) ? '' : '&subset=' . $option['subset'];
							$handle = "{$option['font_id']}-{$option['font_weight_style']}";

							if ( ! empty( $option['subset'] ) ) {
								$handle .= '-' . $option['subset'];
							}

							// Enqueue custom font using wp_enqueue_style()
							wp_deregister_style( $handle );
							wp_register_style( $handle, $option['stylesheet_url'] . $subset );
							wp_enqueue_style( $handle );
						}
					}					
				}

				/**
				 * Check if Google Fonts Exist:
				 * 
				 * Checks if the user has selected any google fonts
				 * to enqueue on the frontend and requests the fonts
				 * from Google in a single http request.
				 * 
				 */
				if ( ! empty( $font_families ) && is_array( $font_families ) ) {

					foreach ( $font_families as $font_family => $variants ) {
						$font_family_sets[] = $font_family . ':' . implode( ',', $variants );
					}

					$query_args = array(
						'family' => implode( '|', $font_family_sets ),
						'subset' => implode( ',', array_unique( $subsets ) ),
					);

					$request_url = add_query_arg( $query_args, "{$protocol}://fonts.googleapis.com/css" );

					// Temporarily removed esc_url_raw().
					wp_deregister_style( $stylesheet_handle );
					wp_register_style( 
						$stylesheet_handle, 
						str_replace( array( '|:', ':|' ), '', $request_url )
					);
					wp_enqueue_style( $stylesheet_handle );
				}
			}
		}

		/**
		 * Output Inline Styles in Head
		 *
		 * Hooks into the 'wp_head' action and outputs specific
		 * inline styles relevant to each font option.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_action 	add_action()
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function output_styles() {
			
			global $wp_customize;

			// Fetch options and transients.
			$transient       = isset( $wp_customize ) ? false : true;
			$options         = EGF_Register_Options::get_options( $transient );
			$default_options = EGF_Register_Options::get_option_parameters();

			// @todo remove line
			EGF_Register_Options::get_linked_controls();

			// Output opening <style> tag if the 
			// customizer isn't running.
			if ( ! isset( $wp_customize ) ) {
				echo '<style id="tt-easy-google-font-styles" type="text/css">';
			}

			/**
			 * Loop through each font control and
			 * output the selector and the css 
			 * styles in the <head>.
			 * 
			 */
			foreach ( $options as $key => $value ) {

				// Check if css styles should be forced.
				$force_styles = isset( $default_options[ $key ]['properties']['force_styles'] ) ? $default_options[ $key ]['properties']['force_styles'] : false;

				// Echo css output in the <head>.
				if ( isset( $wp_customize ) && ! empty( $options[ $key ] ) ) {					
					// Output styles differently if the
					// customizer is running.
					echo $this->generate_customizer_css( $options[ $key ], $default_options[ $key ]['properties']['selector'], $key, $force_styles );
				
				} elseif ( ! empty( $default_options[ $key ] ) ) {

					// Output media query, selector and styles 
					// for this font control.
					echo $this->get_opening_media_query( $key );
					
					// Only output styles if a selector exists.
					if ( ! empty( $default_options[ $key ]['properties']['selector'] ) ) {
						echo $default_options[ $key ]['properties']['selector'] . " { ";
						echo $this->generate_css( $options[ $key ], $force_styles );
						echo "}\n";
					}

					echo $this->get_closing_media_query( $key );
				}
			}

			// Output closing </style> tag if the 
			// customizer isn't running.
			if ( ! isset( $wp_customize ) ) {
				echo '</style>';
			}
		}

		/**
		 * Generate Inline Font CSS
		 *
		 * Takes a font option array as a parameter and
		 * return a string of inline styles.
		 * 
		 * @param  array $option 	Font option array
		 * @return string $output 	Inline styles
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function generate_css( $option, $force_styles = false ) {
			$output     = '';
			$importance = $force_styles ? '!important' : '';
			$properties = $this->get_css_properties();
			
			/**
			 * Output CSS Styles
			 *
			 * Outputs each css style and appends it
			 * to $output.
			 * 
			 */
			foreach ( $properties as $id => $property ) {

				// Bail if the option hasn't been set.
				if ( empty( $option[ $id ] ) ) {
					continue;
				}

				// Bail if poperty has units but is empty.
				if ( $property['has_units'] && empty( $option[ $id ]['amount'] ) ) {
					continue;
				}

				// Handle borders.
				if ( ! empty( $property['is_border'] ) ) {

					$output .= "{$property['property']}: ";
					$output .= "{$option[ $key ]['width']['amount']}{$option[ $key ]['width']['unit']} ";
					$output .= "{$option[ $key ]['style']} ";
					$output .= "{$option[ $key ]['color']} ";
					$output .= "{$importance}; ";
					continue;
				}

				// Handle all other options.
				if ( $property['has_units'] ) {
					$output .= "{$property['property']}: {$option[ $id ]['amount']}{$option[ $id ]['unit']}{$importance}; ";
				} elseif ( 'font-family' == $property['property'] ) {
					$output .= "{$property['property']}: '{$option[ $id ]}'{$importance}; ";
				} else {
					$output .= "{$property['property']}: {$option[ $id ]}{$importance}; ";
				}
			}

			// Return output
			return $output;
		}

		/**
		 * Generate Customizer Preview Inline Font CSS
		 *
		 * Outputs compatible <style> tags that are necessary in
		 * order to facilitate the live preview. By outputting the
		 * styles in their own <style> tag we are able to use the
		 * font-customizer-preview.js to revert back to theme 
		 * defaults without refreshing the page.
		 * 
		 * @param  array $option 	Font option array
		 * @return string $output 	Inline styles
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function generate_customizer_css( $option, $selector, $id = '', $force_styles = false ) {
			$output     = '';
			$importance = $force_styles ? '!important' : '';
			$properties = $this->get_css_properties();

			/**
			 * Output CSS Styles
			 *
			 * Outputs each css style and appends it
			 * to $output.
			 * 
			 */
			foreach ( $properties as $key => $property ) {
				
				// Bail if the option hasn't been set.
				if ( empty( $option[ $key ] ) ) {
					continue;
				}

				// Bail if poperty has units but is empty.
				if ( $property['has_units'] && empty( $option[ $key ]['amount'] ) ) {
					continue;
				}

				// Handle borders.
				if ( ! empty( $property['is_border'] ) ) {

					// Open <style> tag.
					$output .= "<style id='tt-font-{$id}-{$property['property']}' type='text/css'>";
					$output .= $this->get_opening_media_query( $id );
					$output .= "{$selector} {";

					// Output property.
					$output .= "{$property['property']}: ";
					$output .= "{$option[ $key ]['width']['amount']}{$option[ $key ]['width']['unit']} ";
					$output .= "{$option[ $key ]['style']} ";
					$output .= "{$option[ $key ]['color']} ";
					$output .= "{$importance}; ";

					// Close <style> tag.
					$output .= "}";
					$output .= $this->get_closing_media_query( $id );
					$output .= "</style>";

					// Exit loop iteration.
					continue;
				}

				// Open <style> tag.
				$output .= "<style id='tt-font-{$id}-{$property['property']}' type='text/css'>";
				$output .= $this->get_opening_media_query( $id );
				$output .= "{$selector} {";

				// Handle all other options.
				if ( $property['has_units'] ) {
					$output .= "{$property['property']}: {$option[ $key ]['amount']}{$option[ $key ]['unit']}{$importance}; ";
				} elseif ( 'font-family' == $property['property'] ) {
					$output .= "{$property['property']}: '{$option[ $key ]}'{$importance}; ";
				} else {
					$output .= "{$property['property']}: {$option[ $key ]}{$importance}; ";
				}

				// Close <style> tag.
				$output .= "}";
				$output .= $this->get_closing_media_query( $id );
				$output .= "</style>";
			}

			// Return output.
			return $output;	
		}

		/**
		 * Get Opening Media Query Markup
		 *
		 * Returns the opening media query markup or 
		 * an empty string if this font control has 
		 * no media query settings. 
		 * 
		 * @param  string $option_key 	Font control id
		 * @return string $output 		The opening media query markup
		 *
		 * @since 1.4.0
		 * @version 1.4.4
		 * 
		 */
		public function get_opening_media_query( $option_key ) {
			$output          = "";
			$default_options = EGF_Register_Options::get_option_parameters();

			if ( ! empty( $default_options[ $option_key ]['properties'] ) ) {

				// Get the min and max properties for 
				// this option.
				$min_screen = $default_options[ $option_key ]['properties']['min_screen'];
				$max_screen = $default_options[ $option_key ]['properties']['max_screen'];

				// Return $output if this option has 
				// no min and max value.
				if ( empty( $min_screen['amount'] ) && empty( $max_screen['amount'] ) ) {
					return $output;
				}

				// Build the $output.
				$output .= "@media ";

				// Append min-width value if applicable.
				if ( ! empty( $min_screen['amount'] ) ) {
					$output .= "(min-width: {$min_screen['amount']}{$min_screen['unit']})";
				}

				// Append 'and' keyword if min and max value exists.
				if ( ! empty( $min_screen['amount'] ) && ! empty( $max_screen['amount'] ) ) {
					$output .= " and ";
				}

				// Append max-width value if applicable.
				if ( ! empty( $max_screen['amount'] ) ) {
					$output .= "(max-width: {$max_screen['amount']}{$max_screen['unit']})";
				}

				$output .= " {\n\t";
			}

			return $output;
		}

		/**
		 * Get Closing Media Query Markup
		 *
		 * Returns the closing media query markup or 
		 * an empty string if this font control has 
		 * no media query settings. 
		 * 
		 * @param  string $option_key 	Font control id
		 * @return string $output 		The opening media query markup
		 *
		 * @since 1.4.0
		 * @version 1.4.4
		 * 
		 */
		public function get_closing_media_query( $option_key ) {
			$media_query = $this->get_opening_media_query( $option_key );
			return empty( $media_query ) ? "" : "}\n";
		}

		/**
		 * Get CSS Properties
		 *
		 * Returns an associative array containing the
		 * settings id and their respective css properties.
		 * Used by this class to output styles in the
		 * frontend.
		 * 
		 * @return array $properties 	Array of settings with css properties.
		 *
		 * @since 1.3.4
		 * @version 1.4.4
		 * 
		 */
		public function get_css_properties() {		
			$properties = array( 
				'background_color'           => array( 'property' => 'background-color',           'has_units' => false ),
				'display'                    => array( 'property' => 'display',                    'has_units' => false ),
				'font_color'                 => array( 'property' => 'color',                      'has_units' => false ),
				'font_name'                  => array( 'property' => 'font-family',                'has_units' => false ),
				'font_size'                  => array( 'property' => 'font-size',                  'has_units' => true ),
				'font_style'                 => array( 'property' => 'font-style',                 'has_units' => false ),
				'font_weight'                => array( 'property' => 'font-weight',                'has_units' => false ),
				'letter_spacing'             => array( 'property' => 'letter-spacing',             'has_units' => true ),
				'line_height'                => array( 'property' => 'line-height',                'has_units' => false ),
				'margin_top'                 => array( 'property' => 'margin-top',                 'has_units' => true ),
				'margin_bottom'              => array( 'property' => 'margin-bottom',              'has_units' => true ),
				'margin_left'                => array( 'property' => 'margin-left',                'has_units' => true ),
				'margin_right'               => array( 'property' => 'margin-right',               'has_units' => true ),
				'padding_top'                => array( 'property' => 'padding-top',                'has_units' => true ),
				'padding_bottom'             => array( 'property' => 'padding-bottom',             'has_units' => true ),
				'padding_left'               => array( 'property' => 'padding-left',               'has_units' => true ),
				'padding_right'              => array( 'property' => 'padding-right',              'has_units' => true ),
				'text_decoration'            => array( 'property' => 'text-decoration',            'has_units' => false ),
				'text_transform'             => array( 'property' => 'text-transform',             'has_units' => false ),
				'border_top_color'           => array( 'property' => 'border-top-color',           'has_units' => false ),
				'border_top_style'           => array( 'property' => 'border-top-style',           'has_units' => false ),
				'border_top_width'           => array( 'property' => 'border-top-width',           'has_units' => true ),
				'border_bottom_color'        => array( 'property' => 'border-bottom-color',        'has_units' => false ),
				'border_bottom_style'        => array( 'property' => 'border-bottom-style',        'has_units' => false ),
				'border_bottom_width'        => array( 'property' => 'border-bottom-width',        'has_units' => true ),
				'border_left_color'          => array( 'property' => 'border-left-color',          'has_units' => false ),
				'border_left_style'          => array( 'property' => 'border-left-style',          'has_units' => false ),
				'border_left_width'          => array( 'property' => 'border-left-width',          'has_units' => true ),
				'border_right_color'         => array( 'property' => 'border-right-color',         'has_units' => false ),
				'border_right_style'         => array( 'property' => 'border-right-style',         'has_units' => false ),
				'border_right_width'         => array( 'property' => 'border-right-width',         'has_units' => true ),
				'border_radius_top_left'     => array( 'property' => 'border-top-left-radius',     'has_units' => true ),
				'border_radius_top_right'    => array( 'property' => 'border-top-right-radius',    'has_units' => true ),
				'border_radius_bottom_right' => array( 'property' => 'border-bottom-right-radius', 'has_units' => true ),
				'border_radius_bottom_left'  => array( 'property' => 'border-bottom-left-radius',  'has_units' => true ),
			);
			
			// Return properties.
			return $properties;
		}
	}
endif;
