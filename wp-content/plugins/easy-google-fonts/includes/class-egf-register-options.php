<?php
/**
 * Class: EGF_Register_Options
 *
 * This file defines the Options for this Theme. The theme options
 * structure is heavily based on the theme options structure in Chip
 * Bennett's Oenology Theme. We take the same stance as Automattic
 * and exclusively use the Customizer for theme options instead of
 * creating theme option pages.
 *
 *  - Define Default Theme Options
 *  - Register/Initialize Theme Options
 *
 *
 * @package   Easy_Google_Fonts_Admin
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 *
 */
if ( ! class_exists( 'EGF_Register_Options' ) ) :
	class EGF_Register_Options {
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
		 * @var     null
		 * @since   1.2
		 *
		 */
		protected $plugin_screen_hook_suffix = null;

		/**
		 * Slug of the plugin screen.
		 *
		 * @var      string
		 * @since    1.2
		 *
		 */
		public static $slug = 'easy-google-fonts';

		/**
		 * Validation Flag
		 *
		 * @var   boolean
		 * @since 1.2
		 *
		 */
		public $validated;

		/**
		 * Validation Flag
		 *
		 * @var   boolean
		 * @since 1.2
		 *
		 */
		protected $default_options;

		/**
		 * Constructor Function
		 *
		 * Initialize the plugin by loading admin scripts &
		 * styles and adding a settings page and menu.
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
			$this->validated = false;
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
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function register_actions() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_init', array( $this, 'add_settings_section' ) );
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
			add_filter( 'tt_font_get_option_parameters', array( $this, 'get_custom_option_parameters' ), 0 );
		}

		/**
		 * Register Typography Settings
		 *
		 * Registers the settings as a serialized array for
		 * a light footprint in the database.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_setting 	register_setting()
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function register_settings() {
			register_setting(
				'tt_font_theme_options',
				'tt_font_theme_options',
				array( $this, 'validate_settings' )
			);
		}

		/**
		 * Validate Settings
		 *
		 * @param  array $input - The array of settings
		 * @return array $input - The array of settings after sanitization
		 *
		 * @todo  Increase sanitization checks and reduce number
		 *     of times this callback function is ran.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function validate_settings( $input ) {

			// Get default options
			$all_options = array_merge( self::get_option_parameters(), self::get_custom_option_parameters( array() ) );

			// Array of whitelisted values
			$whitelist   = array(
					'subset',
					'font_id',
					'font_name',
					'font_color',
					'font_weight',
					'font_style',
					'font_weight_style',
					'background_color',
					'stylesheet_url',
					'text_decoration',
					'text_transform',
					'line_height',
					'font_size',
					'display',
					'letter_spacing',
					'margin_top',
					'margin_right',
					'margin_bottom',
					'margin_left',
					'padding_top',
					'padding_right',
					'padding_bottom',
					'padding_left',
					'border_top_color',
					'border_top_style',
					'border_top_width',
					'border_bottom_color',
					'border_bottom_style',
					'border_bottom_width',
					'border_left_color',
					'border_left_style',
					'border_left_width',
					'border_right_color',
					'border_right_style',
					'border_right_width',
					'border_radius_top_left',
					'border_radius_top_right',
					'border_radius_bottom_left',
					'border_radius_bottom_right',
			);

			/**
			 * Remove any values from $input that
			 * are not in the safe $whitelist array
			 *
			 * $option is the option name
			 * $setting is all of the properties in the option
			 *
			 */

			foreach ( $input as $option => $setting ) {

				if ( ! isset( $all_options[ $option ] )  ) {
					unset( $input[ $option ] );
					continue;
				}

				$defaults = $all_options[ $option ]['default'];

				// Parse setting into array if it is a json string
				if ( is_string( $setting ) ) {
					$setting = json_decode( $setting );
				} elseif ( empty( $setting ) ) {
					$setting = $defaults;
				}

				// Convert setting into array if it is StdClass Object

				if ( is_object( $setting ) ) {
					$setting = $this->object_to_array( $setting );
				}

				if ( is_array( $setting ) ) {
					// Remove blacklisted values if they exist
					foreach ( $setting as $key => $value ) {
						if ( ! in_array( $key, $whitelist ) ) {
							unset( $setting[ $key ] );
						}
					}
				}

				// Parse args with default
				$input[ $option ] = wp_parse_args( $setting, $defaults );
			}

			/**
			 * Specific Validation
			 */

			// Set validated flag
			$this->validated = true;

			return $input;
		}

		/**
		 * Recursive Function: Object to Array
		 *
		 * @param  class $obj The object we want to convert
		 * @return array $arr The object converted into an associative array
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function object_to_array( $obj ) {
			$arrObj = is_object( $obj ) ? get_object_vars( $obj ) : $obj;

			$arr = array();

			foreach ( $arrObj as $key => $val ) {
				$val = ( is_array( $val ) || is_object( $val ) ) ? $this->object_to_array( $val ) : $val;
				$arr[$key] = $val;
			}
			return $arr;
		}

		/**
		 * Add Settings Section
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function add_settings_section() {
			$tabs = self::get_setting_tabs();

			foreach ( $tabs as $tab ) {
				$tab_name     = $tab['name'];
				$tab_sections = $tab['sections'];

				foreach ( $tab_sections as $section ) {
					$section_name  = $section['name'];
					$section_title = $section['title'];

					// Add settings section
					add_settings_section(
						"tt_font_{$section_name}_section", 			// $sectionid
						$section_title,								// $title
						array( $this, 'settings_section_callback'),	// $callback
						"tt_font_{$tab_name}_tab"					// $pageid
					);
				}
			}
		}

		/**
		 * Settings Section Callback
		 *
		 * Call add_settings_section() for each Settings
		 *
		 * Loop through each Theme Font Settings page tab, and add
		 * a new section to the Theme Settings page for each
		 * section specified for each tab.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public function settings_section_callback() {
			$tabs = self::get_setting_tabs();

			foreach ( $tabs as $tab_name => $tab ) {
				$tab_sections = $tab['sections'];
				foreach ( $tab_sections as $section_name => $section ) {
					if ( "tt_font_{$section_name}_section" == $section_passed['id'] ) {
						?>
						<p><?php echo $section['description']; ?></p>
						<?php
					}
				}
			}
		}

		/**
		 * Separate Settings By Tab
		 *
		 * Returns an array of tabs, each of which is an indexed
		 * array of settings included with the specified tab.
		 *
		 * @return	array	$settings_by_tab	array of arrays of settings by tab
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_settings_by_tab() {
			// Get the list of settings page tabs
			$tabs = self::get_setting_tabs();

			// Initialize an array to hold an indexed array of tabnames
			$settings_by_tab = array();

			// Loop through the array of tabs
			foreach ( $tabs as $tab ) {

				// Add an indexed array key to the settings-by-tab
				// array for each tab name
				$tab_name          = $tab['name'];
				$settings_by_tab[] = $tab_name;
			}

			// Get the array of option parameters
			$option_parameters = self::get_option_parameters();

			// Loop through the option parameters array
			foreach ( $option_parameters as $option_parameter ) {
				$option_tab  = $option_parameter['tab'];
				$option_name = $option_parameter['name'];

				/*
				 * Add an indexed array key to the settings-by-tab array
				 * for each setting associated with each tab
				 */
				$settings_by_tab[ $option_tab ][] = $option_name;
				$settings_by_tab['all'][]         = $option_name;
			}

			return $settings_by_tab;
		}

		/**
		 * Get Setting Tabs
		 *
		 * Returns an array of tabs that will be used as sections
		 * in the WordPress customizer. Theme authors can now hook
		 * into this array filter and add their own sections to
		 * group a set of controls. Subsection support within a
		 * section will be added in future versions.
		 *
		 * Custom Filters:
		 *     - tt_font_typography_panel_id
		 *     - tt_font_typography_description
		 *     - tt_font_custom_typography_panel_id
		 *     - tt_font_custom_typography_description
		 *     - tt_font_get_settings_page_tabs
		 *
		 *
		 * @return array $tabs - Array of tabs with their properties
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_setting_tabs() {
			// Tabs
			$tabs = array(
				'typography'=> array(
					'name'        => 'typography',
					'title'       => __( 'Default Typography', 'easy-google-fonts' ),
					'panel'       => apply_filters( 'tt_font_typography_panel_id', 'tt_font_typography_panel' ),
					'description' => apply_filters( 'tt_font_typography_description', __( 'Your theme has typography support. You can create custom font controls on the google fonts screen in the settings section.', 'easy-google-fonts' ) ),
					'sections'    => array(
						// Test section
						'default' => array(
							'name'        => 'default',
							'title'       => __( 'Default Theme Fonts', 'easy-google-fonts' ),
							'description' => __( 'Default theme font options', 'easy-google-fonts' ),
						),
					)
				),

				'theme-typography'=> array(
					'name'        => 'theme-typography',
					'title'       => __( 'Theme Typography', 'easy-google-fonts' ),
					'panel'       => apply_filters( 'tt_font_custom_typography_panel_id', 'tt_font_typography_panel' ),
					'description' => apply_filters( 'tt_font_custom_typography_description', __( 'Any custom font controls that you have created for your website will appear below. To create more controls visit the google fonts screen in the settings section.', 'easy-google-fonts' ) ),
					'sections'    => array(
						// Test section
						'custom' => array(
							'name'        => 'custom',
							'title'       => __( 'Custom Theme Fonts', 'easy-google-fonts' ),
							'description' => __( 'Custom theme font options', 'easy-google-fonts' ),
						),
					)
				),
			);

			// Return tabs
			return apply_filters( 'tt_font_get_settings_page_tabs', $tabs );
		}

		/**
		 * Get Panels
		 *
		 * Returns an array of panels that will be used as panels
		 * in the WordPress customizer. Theme authors can now hook
		 * into this array filter and add their own panels in
		 * the customizer.
		 *
		 * Custom Filters:
		 *     - tt_font_get_panels
		 *
		 *
		 * @return array $panels - Array of panels with their properties
		 *
		 * @since 1.3.2
		 * @version 1.4.4
		 *
		 */
		public static function get_panels() {
			$panels = array(
				'tt_font_typography_panel' => array(
					'name'        => 'tt_font_typography_panel',
					'title'       => __( 'Typography', 'easy-google-fonts' ),
					'priority'    => 10,
					'capability'  => 'edit_theme_options',
					'description' => __( 'Your theme has typography support. You can create custom font controls on the google fonts screen in the settings section.', 'easy-google-fonts' ),
				),
			);

			$panels = apply_filters( 'tt_font_get_panels', $panels );

			// Parse panels against defaults to ensure all $args are present.
			foreach ( $panels as $id => $panel ) {
				$font_controls[ $id ] = self::parse_customizer_panel( $panel );
			}

			// return panels
			return $panels;
		}

		/**
		 * Parse Font Controls
		 *
		 * Parses any font controls against a set of defaults using
		 * wp_parse_args(). This allows developers to quickly add
		 * custom font controls without having to specify every
		 * property each time.
		 *
		 * Custom Filters:
		 *     - tt_font_force_styles
		 *     - tt_font_size_min_range
		 *     - tt_font_size_max_range
		 *     - tt_font_size_step
		 *     - tt_font_line_height_min_range
		 *     - tt_font_line_height_max_range
		 *     - tt_font_line_height_step
		 *     - tt_font_letter_spacing_min_range
		 *     - tt_font_letter_spacing_max_range
		 *     - tt_font_letter_spacing_step
		 *     - tt_font_margin_min_range
		 *     - tt_font_margin_max_range
		 *     - tt_font_margin_step
		 *     - tt_font_padding_min_range
		 *     - tt_font_padding_max_range
		 *     - tt_font_padding_step
		 *     - tt_font_border_min_range
		 *     - tt_font_border_max_range
		 *     - tt_font_border_step
		 *
		 * @param  array $args 		The properties of the current font control
		 * @return array $control   The font control parsed with the default values
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function parse_font_control_array( $args ) {
			$defaults = array(
				'title'       => __( 'Font Control', 'easy-google-fonts' ),
				'type'        => 'font',
				'description' => __( "Edit Font", 'easy-google-fonts' ),
				'section'     => 'default',
				'tab'         => 'typography',
				'transport'   => 'postMessage',
				'since'       => '1.2',
				'properties'  => array(
						'selector'                 => '',
						'force_styles'             => apply_filters( 'tt_font_force_styles', false ),
						'font_size_min_range'      => apply_filters( 'tt_font_size_min_range', 10 ),
						'font_size_max_range'      => apply_filters( 'tt_font_size_max_range', 100 ),
						'font_size_step'           => apply_filters( 'tt_font_size_step', 1 ),
						'line_height_min_range'    => apply_filters( 'tt_font_line_height_min_range', 0.8 ),
						'line_height_max_range'    => apply_filters( 'tt_font_line_height_max_range', 4 ),
						'line_height_step'         => apply_filters( 'tt_font_line_height_step', 0.1 ),
						'letter_spacing_min_range' => apply_filters( 'tt_font_letter_spacing_min_range', -5 ),
						'letter_spacing_max_range' => apply_filters( 'tt_font_letter_spacing_max_range', 20 ),
						'letter_spacing_step'      => apply_filters( 'tt_font_letter_spacing_step', 1 ),
						'margin_min_range'         => apply_filters( 'tt_font_margin_min_range', 0 ),
						'margin_max_range'         => apply_filters( 'tt_font_margin_max_range', 400 ),
						'margin_step'              => apply_filters( 'tt_font_margin_step', 1 ),
						'padding_min_range'        => apply_filters( 'tt_font_padding_min_range', 0 ),
						'padding_max_range'        => apply_filters( 'tt_font_padding_max_range', 400 ),
						'padding_step'             => apply_filters( 'tt_font_padding_step', 1 ),
						'border_min_range'         => apply_filters( 'tt_font_border_min_range', 0 ),
						'border_max_range'         => apply_filters( 'tt_font_border_max_range', 100 ),
						'border_step'              => apply_filters( 'tt_font_border_step', 1 ),
						'border_radius_min_range'  => apply_filters( 'tt_font_border_radius_min_range', 0 ),
						'border_radius_max_range'  => apply_filters( 'tt_font_border_radius_max_range', 100 ),
						'border_radius_step'       => apply_filters( 'tt_font_border_radius_step', 1 ),
						'min_screen'               => array( 'amount' => '', 'unit' => 'px' ),
						'max_screen'               => array( 'amount' => '', 'unit' => 'px' ),
						'linked_control_id'        => false,
					),
				'default' => array(
						'subset'                     => 'latin,all',
						'font_id'                    => '',
						'font_name'                  => '',
						'font_color'                 => '',
						'font_weight'                => '',
						'font_style'                 => '',
						'font_weight_style'          => '',
						'background_color'           => '',
						'stylesheet_url'             => '',
						'text_decoration'            => '',
						'text_transform'             => '',
						'line_height'                => '',
						'display'                    => '',
						'font_size'                  => array( 'amount' => '', 'unit' => 'px' ),
						'letter_spacing'             => array( 'amount' => '', 'unit' => 'px' ),
						'margin_top'                 => array( 'amount' => '', 'unit' => 'px' ),
						'margin_right'               => array( 'amount' => '', 'unit' => 'px' ),
						'margin_bottom'              => array( 'amount' => '', 'unit' => 'px' ),
						'margin_left'                => array( 'amount' => '', 'unit' => 'px' ),
						'padding_top'                => array( 'amount' => '', 'unit' => 'px' ),
						'padding_right'              => array( 'amount' => '', 'unit' => 'px' ),
						'padding_bottom'             => array( 'amount' => '', 'unit' => 'px' ),
						'padding_left'               => array( 'amount' => '', 'unit' => 'px' ),
						'border_radius_top_left'     => array( 'amount' => '', 'unit' => 'px' ),
						'border_radius_top_right'    => array( 'amount' => '', 'unit' => 'px' ),
						'border_radius_bottom_right' => array( 'amount' => '', 'unit' => 'px' ),
						'border_radius_bottom_left'  => array( 'amount' => '', 'unit' => 'px' ),
						'border_top_color'           => '',
						'border_top_style'           => '',
						'border_top_width'           => array( 'amount' => '', 'unit' => 'px' ),
						'border_bottom_color'        => '',
						'border_bottom_style'        => '',
						'border_bottom_width'        => array( 'amount' => '', 'unit' => 'px' ),
						'border_left_color'          => '',
						'border_left_style'          => '',
						'border_left_width'          => array( 'amount' => '', 'unit' => 'px' ),
						'border_right_color'         => '',
						'border_right_style'         => '',
						'border_right_width'         => array( 'amount' => '', 'unit' => 'px' ),
					),
			);

			// Parse Properties
			if ( isset( $args['properties'] ) ) {
				$args['properties'] = wp_parse_args( $args['properties'], $defaults['properties'] );
			}

			// Parse default preset values
			if ( isset( $args['default'] ) ) {
				$args['default'] = wp_parse_args( $args['default'], $defaults['default'] );
			}

			// Parse complete control
			$control = wp_parse_args( $args, $defaults );

			return $control;
		}

		/**
		 * Parse Customizer Panel
		 *
		 * Parse any panels against a set of defaults using
		 * wp_parse_args(). This is to ensure that every
		 * panel property is populated in order for the
		 * plugin to function correctly.
		 *
		 * @param  array $args [description]
		 * @return array       [description]
		 *
		 * @since 1.0
		 * @version 1.0
		 *
		 */
		public static function parse_customizer_panel( $args ) {
			$defaults = array(
				'name'        => 'tt_font_typography_panel',
				'title'       => __( 'Typography', 'easy-google-fonts' ),
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'description' => __( 'Your theme has typography support. You can create custom font controls on the Google Fonts screen in the Settings section.', 'easy-google-fonts' ),
			);

			// Parse the panel passed in the parameter with the defaults
			$panel = wp_parse_args( $args, $defaults );

			// Return the panel
			return $panel;
		}

		/**
		 * Get Theme Font Option Parameters
		 *
		 * Array that holds parameters for all default font options in this theme.
		 * The 'type' key is used to generate the proper form field markup and to
		 * sanitize the user-input data properly. The 'tab' key determines the
		 * Settings Page on which the option appears, and the 'section' tab
		 * determines the section of the Settings Page tab in which the option
		 * appears.
		 *
		 * Custom Filters:
		 *     - tt_font_get_option_parameters
		 *
		 * @return	array	$options	array of arrays of option parameters
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_option_parameters() {
			$options = array(

				/**
				 * Typography Tab Options
				 *
				 * The following options are used to register controls
				 * that will appear in the 'Typography' section in the
				 * Customizer
				 *
				 * @since 1.2
				 * @version 1.4.4
				 *
				 */
				'tt_default_body' => array(
					'name'        => 'tt_default_body',
					'title'       => __( 'Paragraphs', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's body and paragraph text", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_body', 'p' ) ),
				),

				'tt_default_heading_1' => array(
					'name'        => 'tt_default_heading_1',
					'title'       => __( 'Heading 1', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 1 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_1', 'h1' ) ),
				),

				'tt_default_heading_2' => array(
					'name'        => 'tt_default_heading_2',
					'title'       => __( 'Heading 2', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 2 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_h2', 'h2' ) ),
				),

				'tt_default_heading_3' => array(
					'name'        => 'tt_default_heading_3',
					'title'       => __( 'Heading 3', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 3 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_h3', 'h3' ) ),
				),

				'tt_default_heading_4' => array(
					'name'        => 'tt_default_heading_4',
					'title'       => __( 'Heading 4', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 4 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_h4', 'h4' ) ),
				),

				'tt_default_heading_5' => array(
					'name'        => 'tt_default_heading_5',
					'title'       => __( 'Heading 5', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 5 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_h5', 'h5' ) ),
				),

				'tt_default_heading_6' => array(
					'name'        => 'tt_default_heading_6',
					'title'       => __( 'Heading 6', 'easy-google-fonts' ),
					'description' => __( "Please select a font for the theme's heading 6 styles", 'easy-google-fonts' ),
					'properties'  => array( 'selector' => apply_filters( 'tt_default_heading_h6', 'h6' ) ),
				),
			);

			$font_controls = apply_filters( 'tt_font_get_option_parameters', $options );

			foreach ( $font_controls as $id => $control ) {
				$font_controls[ $id ] = self::parse_font_control_array( $control );
			}

			return $font_controls;
		}

		/**
		 * Get Custom Theme Font Option Parameters
		 *
		 * This function converts custom controls
		 * in the admin section to the internal
		 * font control.
		 *
		 * Array that holds parameters for all custom font options in this theme.
		 * The 'type' key is used to generate the proper form field markup and to
		 * sanitize the user-input data properly. The 'tab' key determines the
		 * Settings Page on which the option appears, and the 'section' tab
		 * determines the section of the Settings Page tab in which the option
		 * appears.
		 *
		 * @uses EGF_Posttype::get_all_font_controls() defined in \includes\class-egf=posttype.php
		 *
		 * @return	array	$options	array of arrays of option parameters
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_custom_option_parameters( $options ) {
			$query  = EGF_Posttype::get_all_font_controls();
			$custom_options = array();

			if ( $query ) {
				while( $query->have_posts() ) {

					$query->the_post();

					// Extract font control properties
					$control_id      = get_post_meta( get_the_ID(), 'control_id', true );
					$selectors_array = get_post_meta( get_the_ID(), 'control_selectors', true );
					$description     = get_post_meta( get_the_ID(), 'control_description', true );
					$force_styles    = get_post_meta( get_the_ID(), 'force_styles', true );

					if ( empty( $force_styles ) ) {
						$force_styles = false;
					}

					// Build selectors
					$selectors = '';

					if ( ! $selectors_array ) {
						$selectors_array = array();
					}

					foreach ( $selectors_array as $selector ) {
						$selectors .= $selector . ',';
					}

					while ( substr( $selectors, -1 ) == ',' ) {
						$selectors = rtrim( $selectors, "," );
					}

					// Add control
					if ( $control_id ) {
						$custom_options[ $control_id ] = array(
							'name'        => $control_id,
							'title'       => get_the_title(),
							'type'        => 'font',
							'description' => $description,
							'section'     => 'default',
							'tab'         => 'theme-typography',
							'transport'   => 'postMessage',
							'since'       => '1.0',
							'properties'  => array(
								'selector'     => $selectors,
								'force_styles' => $force_styles,
							),
						);
					}

				} //endwhile

				// Reset the query globals
				wp_reset_postdata();
			}

			// Parse with defaults
			foreach ( $custom_options as $id => $control ) {
				$custom_options[ $id ] = self::parse_font_control_array( $control );
			}

			return array_merge( $options, $custom_options );
		}

		/**
		 * Theme Font Option Defaults
		 *
		 * Returns an associative array that holds all of the default
		 * values for all of the theme font options.
		 *
		 * @return	array	$defaults	associative array of option defaults
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_option_defaults() {

			// Get the array that holds all theme 
			// font option parameters
			$tt_font_parameters = self::get_option_parameters();

			// Initialize the array to hold the default 
			// values for all of the font options
			$tt_font_defaults = array();

			// Loop through the font option parameters array
			foreach ( $tt_font_parameters as $tt_font_parameter ) {

				/*
				 * Add an associative array key to the defaults array for each
				 * option in the parameters array
				 */
				$name                      = $tt_font_parameter['name'];
				$tt_font_defaults[ $name ] = $tt_font_parameter['default'];
			}

			// Return the defaults array
			return apply_filters( 'tt_font_option_defaults', $tt_font_defaults );
		}

		/**
		 * Get Theme Options
		 *
		 * Array that holds all of the defined values for the current
		 * theme options. If the user has not specified a value for a
		 * given Theme option, then the option's default value is
		 * used instead. This function uses the WordPress Transients API
		 * in order to increase speed performance. Please make sure
		 * that you refresh the transient if you modify this function.
		 *
		 * Uses the following transient: 'tt_font_theme_options'
		 *
		 * Note: In order to refresh the transient that is set in this
		 * function please visit the Customizer. This will automatically
		 * refresh the transient.
		 *
		 * @return	array	$tt_font_options	current values for all Theme options
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_options( $with_transient = true ) {
			// Get the global customize variable
			global $wp_customize;

			// Get the option defaults
			$option_defaults = self::get_option_defaults();

			$tt_font_options = array();

			// Check if a transient exists, if it doesn't or we are in customize mode then reset the transient
			if ( ! $with_transient || isset( $wp_customize ) || false === ( $tt_font_options = get_transient( 'tt_font_theme_options' ) ) ) {

				// Delete transient if it exists
				delete_transient( 'tt_font_theme_options' );

				// Parse the stored options with the defaults
				$all_options     = get_option( 'tt_font_theme_options', array() );
				$tt_font_options = wp_parse_args( $all_options, $option_defaults );

				// Remove redundant options
				foreach ( $tt_font_options as $key => $value ) {

					if ( ! isset( $option_defaults[ $key ] ) ) {
						unset( $tt_font_options[ $key ] );
					}
				}

				// Set the transient
				set_transient( 'tt_font_theme_options', $tt_font_options, 0 );
			}

			// Return the parsed array
			return wp_parse_args( $tt_font_options, $option_defaults );
		}

		/**
		 * Get Linked Controls
		 *
		 * Gets all of the controls linked to the control
		 * with the id passed in the parameter. Will return
		 * an array of linked control ids if applicable or
		 * an empty array if no controls were found.
		 *
		 * Note: if you want to get top level controls then 
		 * pass in false in the parameter.
		 * 
		 * @param  $linked_control_id 	id to check, boolean/string
		 * @return $linked_controls 	array of linked control ids
		 *
		 * @since 1.4.0
		 * @version 1.4.4
		 * 
		 */
		public static function get_linked_controls( $linked_control_id = false ) {
			$linked_controls   = array();
			$option_parameters = self::get_option_parameters();
			
			// Add ids to $linked_controls.
			foreach ( $option_parameters as $id => $option_parameter ) {
				if ( $linked_control_id === $option_parameter['properties']['linked_control_id'] ) {
					$linked_controls[] = $id;
				}
			}

			return $linked_controls;
		}


	}
endif;
