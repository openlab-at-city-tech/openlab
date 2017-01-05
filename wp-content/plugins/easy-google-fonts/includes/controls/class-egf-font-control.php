<?php
/**
 * Class: EGF_Font_Control
 *
 * Google Font Options Theme Customizer Integration
 *
 * This file integrates the Theme Customizer for this Theme. 
 * All options in this theme are managed in the live customizer. 
 * We believe that themes should only alter the display of content 
 * and should not add any additional functionality that would be 
 * better suited for a plugin. Since all options are presentation 
 * centered, they should all be controllable by the Customizer.
 * 
 *
 * @package   Easy_Google_Fonts_Admin
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.2
 * 
 */
if ( ! class_exists( 'EGF_Font_Control' ) && class_exists( 'WP_Customize_Control' ) ) :
	class EGF_Font_Control extends WP_Customize_Control {

		/**
		 * Control type
		 * 
		 * @access public
		 * @var string
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 */
		public $type = 'egf_font';

		/**
		 * JSON statuses
		 * 
		 * @access public
		 * @var array
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 */
		public $statuses;

		protected $tabs = array();

		/**
		 * Constructor Function
		 * 
		 * Sets up the variables for this class
		 * instance.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		function __construct( $manager, $id, $args = array() ) {
			$this->plugin_slug = 'easy-google-fonts';
			$this->manager     = $manager;
			$this->id          = $id;
			$this->args        = $args;

			// Call WP_Customize_Control parent constuctor.
			parent::__construct( $this->manager, $this->id, $this->args );

			// Define tabs for this font control.
			$this->add_tab( 'font-styles',      __( 'Styles', 'easy-google-fonts' ),      array( $this, 'get_style_controls' ), true );
			$this->add_tab( 'font-appearance',  __( 'Appearance', 'easy-google-fonts' ),  array( $this, 'get_appearance_controls' ) );
			$this->add_tab( 'font-positioning', __( 'Positioning', 'easy-google-fonts' ), array( $this, 'get_positioning_controls' ) );			
		}

		/**
		 * Add a tab to the control.
		 *
		 * @param string $id
		 * @param string $label
		 * @param mixed $callback
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function add_tab( $id, $label, $callback, $selected = false ) {
			$this->tabs[ $id ] = array(
				'label'    => $label,
				'callback' => $callback,
				'selected' => $selected,
			);
		}

		/**
		 * Remove a tab from the control.
		 *
		 * @param string $id
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function remove_tab( $id ) {
			unset( $this->tabs[ $id ] );
		}

		/**
		 * PHP Render Font Control
		 *
		 * Easy google fonts doesn't render the 
		 * control content from PHP, as it's 
		 * rendered via JS on load.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function render_content() {
		}

		/**
		 * JS Render Font Control
		 *
		 * Render a JS template for the content 
		 * of this font control.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function content_template() {
			// Get path to styles directory.
			$path = Easy_Google_Fonts::get_views_path() . '/customizer/control';

			// Include control elements.
			include( "{$path}/control-title.php" );
			include( "{$path}/control-start.php" );
			include( "{$path}/control-toggle.php" );
			include( "{$path}/properties-start.php" );
			include( "{$path}/control-tabs.php" );
			include( "{$path}/control-tab-panes.php" );
			include( "{$path}/properties-end.php" );
			include( "{$path}/control-end.php" );
		}

		/**
		 * Set Font Control JSON
		 * 
		 * Refresh the parameters passed to the 
		 * JavaScript via JSON.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function to_json() {
			parent::to_json();
			$this->json['id'] = $this->id;

			// Define additional json parameters.
			$this->json['egf_properties']      = $this->args['option']['properties'];
			$this->json['egf_defaults']        = $this->args['option']['default'];
			$this->json['egf_subsets']         = $this->get_subsets();
			$this->json['egf_text_decoration'] = $this->get_text_decoration_options();
			$this->json['egf_text_transform']  = $this->get_text_transform_options();
			$this->json['egf_display']         = $this->get_display_options();
			$this->json['egf_border_styles']   = $this->get_border_style_options();
		}

		/**
		 * Get Style Controls
		 * 
		 * Controls:
		 *     - Font Family
		 *     - Font Weight
		 *     - Text Decoration
		 *     - Text Transform
		 *     - Display
		 *
		 * @uses EGF_Font_Utilities::get_google_fonts() 	defined in includes\class-egf-font-utilities
		 * @uses EGF_Font_Utilities::get_default_fonts() 	defined in includes\class-egf-font-utilities
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_style_controls() {
			// Get path to styles directory.
			$path = Easy_Google_Fonts::get_views_path() . '/customizer/control/styles';
			
			// Include style controls.
			include( "{$path}/subsets.php" );
			include( "{$path}/font-family.php" );
			include( "{$path}/font-weight.php" );
			include( "{$path}/text-decoration.php" );
			include( "{$path}/text-transform.php" );
		}

		/**
		 * Get Appearance Controls
		 * 
		 * Controls:
		 *     - Font Color
		 *     - Background Color
		 *     - Font Size
		 *     - Line Height
		 *     - Letter Spacing
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_appearance_controls() {
			// Get path to styles directory.
			$path = Easy_Google_Fonts::get_views_path() . '/customizer/control/appearance';

			// Include appearance controls.
			include( "{$path}/font-color.php" );
			include( "{$path}/background-color.php" );
			include( "{$path}/font-size.php" );
			include( "{$path}/line-height.php" );
			include( "{$path}/letter-spacing.php" );		
		}

		/**
		 * Get Positioning Controls
		 *
		 * Controls:
		 *     - Border  ( Top, Bottom, Left, Right )
		 *     - Margin  ( Top, Bottom, Left, Right )
		 *     - Padding ( Top, Bottom, Left, Right )
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_positioning_controls() {
			// Get path to styles directory.
			$path = Easy_Google_Fonts::get_views_path() . '/customizer/control/positioning';

			$folders    = array( 'margin', 'padding', 'border', 'border-radius' );
			$file_names = array( 'start', 'top', 'bottom', 'left', 'right', 'end' );

			// Include margin and padding controls.
			foreach ( $folders as $folder ) {
				foreach ($file_names as $file_name ) {
					include( "{$path}/{$folder}/{$file_name}.php" );
				}
			}

			// Include display control.
			include( "{$path}/display.php" );
		}		

		/**
		 * Get Subset Options
		 *
		 * Returns the array of subsets to be enqueued
		 * as a json object in the customizer.
		 *
		 * Custom Filters:
		 *     - tt_font_subset_options
		 *
		 * 
		 * @return array - Key/value array of available subsets.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_subsets() {
			// Font subset options
			$font_subset_options = array(
				'all'          => __( 'All Subsets', 'easy-google-fonts' ),
				'latin'        => __( 'Latin', 'easy-google-fonts' ),
				'latin-ext'    => __( 'Latin Extended', 'easy-google-fonts' ),
				'arabic'       => __( 'Arabic', 'easy-google-fonts' ),
				'cyrillic'     => __( 'Cyrillic', 'easy-google-fonts' ),
				'cyrillic-ext' => __( 'Cyrillic Extended', 'easy-google-fonts' ),
				'devanagari'   => __( 'Devanagari', 'easy-google-fonts' ),
				'greek'        => __( 'Greek', 'easy-google-fonts' ),	
				'greek-ext'    => __( 'Greek Extended', 'easy-google-fonts' ),
				'khmer'        => __( 'Khmer', 'easy-google-fonts' ),
				'telugu'       => __( 'Telugu', 'easy-google-fonts' ),
				'vietnamese'   => __( 'Vietnamese', 'easy-google-fonts' ),
			);

			return apply_filters( 'tt_font_subset_options', $font_subset_options );
		}

		/**
		 * Get Text Decoration Options
		 *
		 * Returns the array of text decoration options 
		 * to be enqueued as a json object in the 
		 * customizer.
		 *
		 * Custom Filters:
		 *     - tt_font_text_decoration_options
		 *
		 * 
		 * @return array - Key/value array of available options.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_text_decoration_options() {
			// Text decoration options
			$text_decoration_options = array(
				'none'			 => __( 'None', 'easy-google-fonts' ),
				'underline'		 => __( 'Underline', 'easy-google-fonts' ),
				'line-through' 	 => __( 'Line-through', 'easy-google-fonts' ),
				'overline'		 => __( 'Overline', 'easy-google-fonts' ),				
			);

			return apply_filters( 'tt_font_text_decoration_options', $text_decoration_options );	
		}

		/**
		 * Get Text Transform Options
		 * 
		 * @return [type] [description]
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_text_transform_options() {
			$text_transform_options = array(
				'none'			 => __( 'None', 'easy-google-fonts' ),
				'uppercase'		 => __( 'Uppercase', 'easy-google-fonts' ),
				'lowercase' 	 => __( 'Lowercase', 'easy-google-fonts' ),
				'capitalize'	 => __( 'Capitalize', 'easy-google-fonts' ),			
			);

			return apply_filters( 'tt_font_text_transform_options', $text_transform_options );
		}

		/**
		 * Get Display Options
		 * 
		 * @return [type] [description]
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_display_options() {
			$display_options = array(
				'block'        => __( 'Block', 'easy-google-fonts' ),
				'inline-block' => __( 'Inline Block', 'easy-google-fonts' ),
			);

			return apply_filters( 'tt_font_display_options', $display_options );			
		}

		/**
		 * Get Border Style Options
		 * 
		 * @return [type] [description]
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function get_border_style_options() {
			$styles = array(
				'none'   => __( 'None', 'easy-google-fonts' ),
				'solid'  => __( 'Solid', 'easy-google-fonts' ),
				'dashed' => __( 'Dashed', 'easy-google-fonts' ),
				'dotted' => __( 'Dotted', 'easy-google-fonts' ),
				'double' => __( 'Double', 'easy-google-fonts' ),
			);

			return apply_filters( 'tt_font_border_style_options', $styles );
		}
	}
endif;
