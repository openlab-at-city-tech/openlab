<?php 
/**
 * Class: EGF_Customize_Manager
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
if ( ! class_exists( 'EGF_Customize_Manager' ) ) :
	class EGF_Customize_Manager {
		
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.3
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.3
		 *
		 */
		protected $plugin_screen_hook_suffix = null;

		/**
		 * Plugin slug.
		 * 
		 * @var      string
		 * @since    1.3
		 *
		 */
		public static $slug = 'easy-google-fonts';

		/**
		 * Constructor Function
		 * 
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @since 1.3
		 * @version 1.4.2
		 * 
		 */
		function __construct() {

			$this->plugin_slug = 'easy-google-fonts';
			$this->include_control_class();

			/**
			 * We need to register the filter before the action here
			 * to filter the options before the customizer uses it.
			 */
			$this->register_filters();
			$this->register_actions();		
		}

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.3
		 * @version 1.4.2
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
		 * @since 1.3
		 * @version 1.4.2
		 * 
		 */
		public function register_actions() {
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );
			add_action( 'customize_preview_init', array( $this, 'customize_live_preview_scripts' ) );
			add_action( 'customize_register', array( $this, 'customize_preview_styles' ) );
			add_action( 'customize_register', array( $this, 'register_font_control_type' ) );
			add_action( 'customize_register', array( $this, 'register_controls' ) );
			add_action( 'customize_save_tt_font_theme_options', array( $this, 'customize_save_tt_font_theme_options' ) );
			add_action( 'customize_save_after', array( $this, 'customize_save_after' ) );
		}

		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.3
		 * @version 1.4.2
		 * 
		 */
		public function register_filters() {
		}

		/**
		 * Include Required Control Classes
		 * 
		 * Only includes the classes required for this 
		 * control to function if they haven't been 
		 * loaded yet.
		 *
		 * @since 1.3
		 * @version 1.4.2
		 * 
		 */
		public function include_control_class() {

			if ( ! class_exists( 'WP_Customize_Control' ) ) {
				include_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );
			}
			
			if ( ! class_exists( 'EGF_Font_Control' ) ) {
				include_once( Easy_Google_Fonts::get_includes_path() . '/controls/class-egf-font-control.php' );
			}
		}

		/**
		 * Register Font Control
		 *
		 * 
		 * 
		 * @param  [type] $wp_customize [description]
		 * @return [type]               [description]
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		public function register_font_control_type( $wp_customize ) {
			$wp_customize->register_control_type( 'EGF_Font_Control' );
		}

		/**
		 * Load All Fonts in Customizer
		 *
		 * Loads the required fonts as a json object for the live
		 * theme previewer and customizer. By enqueuing the fonts
		 * on the screen we redice the number of ajax requests which
		 * increases performance dramatically.
		 * 
		 * @return array complete list of fonts
		 *
		 * @since  1.3
		 * @version 1.4.2
		 */
		public function customize_load_all_fonts() {
			return EGF_Font_Utilities::get_all_fonts();
		}

		/**
		 * Load Customizer Control Scripts
		 *
		 * Loads the required js for the custom controls in the live 
		 * theme previewer. This is hooked into the live previewer 
		 * using the action: 'customize_controls_enqueue_scripts'.
		 *  
		 * @return void
		 *
		 * @since  1.2
		 * @version 1.4.2
		 * 
		 */
		public function customize_controls_enqueue_scripts() {

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'iris' );

			// Load WordPress media lightbox
			wp_enqueue_media();

			// Load chosen script
			wp_deregister_script( $this->plugin_slug . '-chosen' );
			wp_register_script( 
				$this->plugin_slug . '-chosen',
				Easy_Google_Fonts::get_js_url() . '/chosen.jquery.js',
				array( 'customize-controls', 'iris', 'underscore', 'wp-util', 'jquery' ), 
				'1.3.0', 
				false 
			);
			wp_enqueue_script( $this->plugin_slug . '-chosen' );

			// Load js for live customizer control
			wp_deregister_script( $this->plugin_slug . '-customize-controls-js' );
			wp_register_script( 
				$this->plugin_slug . '-customize-controls-js',
				Easy_Google_Fonts::get_js_url() . '/customize-controls.js',
				array( 'customize-controls', 'iris', 'underscore', 'wp-util', 'jquery' ), 
				Easy_Google_Fonts::VERSION, 
				false 
			);
			wp_enqueue_script( $this->plugin_slug . '-customize-controls-js' );

			// Load translation json object.
			$translationl10n = $this->customize_control_l10n();
			wp_localize_script( $this->plugin_slug . '-customize-controls-js', 'egfTranslation', $translationl10n );

			// Load in all fonts as a json object.
			$all_fonts = $this->customize_load_all_fonts();
			wp_localize_script( $this->plugin_slug . '-customize-controls-js', 'egfAllFonts', $all_fonts );
		}

		/**
		 * Load Customizer Live Preview Scripts
		 *
		 * Loads the required js for the live theme previewer. This
		 * is hooked into the live previewer using the action:
		 * 'customize_preview_init'. Updates options visually in the
		 * live previewer without refreshing the page.
		 *  
		 * @return void
		 *
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_live_preview_scripts() {
			global $wp_customize;

			// Load js for live customizer control
			wp_deregister_script( $this->plugin_slug . '-customize-preview-js' );
			wp_register_script( 
				$this->plugin_slug . '-customize-preview-js',
				Easy_Google_Fonts::get_js_url() . '/customize-preview.js',
				array( 'customize-preview' ),
				Easy_Google_Fonts::VERSION, 
				false 
			);
			wp_enqueue_script( $this->plugin_slug . '-customize-preview-js' );

			$previewl10n = $this->customize_live_preview_l10n();
			wp_localize_script( $this->plugin_slug . '-customize-preview-js', 'egfFontPreviewControls', $previewl10n );

			$all_fonts = $this->customize_load_all_fonts();
			wp_localize_script( $this->plugin_slug . '-customize-preview-js', 'egfAllFonts', $all_fonts );
		}

		/**
		 * Load Customizer Styles
		 *
		 * Loads the required css for the live theme previewer. It is used
		 * as a way to style the custom customizer controls on the live
		 * preview screen. This is hooked into the live previewer using the 
		 * action: 'customize_register'.
		 *  
		 * @return void
		 *
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_preview_styles() {

			wp_enqueue_style( 'wp-color-picker' );

			// Load Chosen CSS
			wp_register_style( 
				$this->plugin_slug . '-chosen-css',
				Easy_Google_Fonts::get_css_url() . '/chosen.css',
				false, 
				'1.3.0' 
			);
			wp_enqueue_style( $this->plugin_slug . '-chosen-css' );			

			// Load CSS to style custom customizer controls
			wp_register_style( 
				$this->plugin_slug . '-customizer-css',
				Easy_Google_Fonts::get_css_url() . '/customizer.css',
				false, 
				Easy_Google_Fonts::VERSION 
			);
			wp_enqueue_style( $this->plugin_slug . '-customizer-css' );
		}

		/**
		 * Load Custom Customizer JS Object
		 *
		 * Major rewrite, now generates the array without 
		 * referencing the customizer manager, which increases
		 * performance and prevents recursion.
		 * 
		 * @return array $controls 	Control properties which will be enqueues as a JSON object on the page
		 *
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_live_preview_l10n() {

			$controls      = array();
			$font_controls = array_merge( EGF_Register_Options::get_option_parameters(), EGF_Register_Options::get_custom_option_parameters( array() ) );

			foreach ( $font_controls as $key => $value ) {

				$controls[ $key ] = array(
					'id'             => $key,
					'label'          => $value['title'],
					'type'           => $value['type'],
					'section'        => 'tt_font_typography',
					'egf_properties' => $value['properties'],
					'force_styles'   => $value['properties']['force_styles'],
					'json'           => array(),
					'selector'       => $value['properties']['selector'],
					'setting'        => array(
							'capability'           => 'edit_theme_options',
							'id'                   => "tt_font_theme_options[{$key}]",
							'default'              => $value['default'],
							'sanitize_callback'    => '',
							'sanitize_js_callback' => '',
							'theme_supports'       => '',
							'transport'            => $value['transport'],
							'type'                 => 'option'	
					),				
				);
			}
			
			return $controls;
		}

		/**
		 * Load Customizer Translation JS Object
		 *
		 * Loads in all of the strings defined in the array as
		 * as JSON object in the customizer.
		 *
		 * Custom Filters:
		 *     - tt_font_customize_control_l10n
		 *
		 * 
		 * @return array $translations - String variables 
		 *
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_control_l10n() {
			// Build l10n array
			$translations = array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'confirmation'         => __( 'This page is asking you to confirm that you want to leave - data you have entered may not be saved.', 'easy-google-fonts' ),
				'deleteAllWarning'     => __( "Warning! You are about to permanently delete all font controls. 'Cancel' to stop, 'OK' to delete.", 'easy-google-fonts' ),
				'deleteWarning'        => __( "You are about to permanently delete this font control. 'Cancel' to stop, 'OK' to delete.", 'easy-google-fonts' ),
				'displayFontLabel'     => __( 'Google Display Fonts', 'easy-google-fonts' ),
				'fallbackFontLabel'    => __( 'Google Fonts', 'easy-google-fonts' ),
				'handwritingFontLabel' => __( 'Google Handwriting Fonts', 'easy-google-fonts' ),
				'monospaceFontLabel'   => __( 'Google Monospace Fonts', 'easy-google-fonts' ),
				'serifFontLabel'       => __( 'Google Serif Fonts', 'easy-google-fonts' ),
				'sansSerifFontLabel'   => __( 'Google Sans Serif Fonts', 'easy-google-fonts' ),
				'standardFontLabel'    => __( 'Standard Web Fonts', 'easy-google-fonts' ),
				'themeDefault'         => '&mdash; ' . __( 'Theme Default', 'easy-google-fonts' ) . ' &mdash;',
			);

			return apply_filters( 'tt_font_customize_control_l10n', $translations );
		}

		/**
		 * Customizer Save Action Hook
		 *
		 * Specifically add code that you want to execute when
		 * the font setting is being saved.
		 * 
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_save_tt_font_theme_options() {
		}

		/**
		 * Customizer Save Action Hook
		 *
		 * Remove / refresh any stored tranients that have 
		 * become stale due to the user changing options.
		 * This function can also be used to add any function
		 * that you wish to run after the options have been
		 * saved.
		 * 
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function customize_save_after() {
			delete_transient( 'tt_font_dynamic_styles' );
			delete_transient( 'tt_font_theme_options' );
		}

		/**
		 * Theme Customizer Controls Implementation
		 *
		 * Implement the Theme Customizer for the Theme Settings
		 * in this theme.
		 * 
		 * @link	http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/	Otto
		 * 
		 * @see final class WP_Customize_Manager 	defined in \{root}\wp-includes\class-wp-customize-manager.php 
		 * 
		 * @param 	object	$wp_customize	Object that holds the customizer data
		 * 
		 * @since  1.3
		 * @version 1.4.2
		 * 
		 */
		public function register_controls( $wp_customize ) {

			if ( ! isset( $wp_customize ) ) {
				return;
			}

			$tt_font_options = EGF_Register_Options::get_options( false );

			// Get the array of option parameters
			$option_parameters = EGF_Register_Options::get_option_parameters();

			// Get customizer panels
			$panels = EGF_Register_Options::get_panels();

			// Get list of tabs
			$tabs = EGF_Register_Options::get_setting_tabs();

			/**
			 * 1. Add Panels
			 *
			 * Add the panels to the customizer:
			 * Add panels to the customizer based on each $panel
			 * from EGF_Register_Options::get_panels() using the 
			 * new panels API in the customizer.
			 * 
			 */
			if ( method_exists( $wp_customize, 'add_panel' ) ) {
				foreach ( $panels as $panel ) {
					$wp_customize->add_panel( $panel['name'], array(
						'priority'       => $panel['priority'],
						'capability'     => $panel['capability'],
						'title'          => $panel['title'],
						'description'    => $panel['description']
					) );
				}
			}
		
			/**
			 * 2. Register Sections
			 *
			 * Add Each Customizer Section: 
			 * Add each customizer section based on each $tab section
			 * from EGF_Register_Options::get_setting_tabs()
			 * 
			 */
			foreach ( $tabs as $tab ) {
				// Add $tab section
				$wp_customize->add_section( 'tt_font_' . $tab['name'], array(
					'title'       => $tab['title'],
					'description' => $tab['description'],
					'panel'       => $tab['panel'],
				) );
			}

			/**
			 * 3. Add Settings to Sections
			 * 4. Register Control for Each Setting
			 *  
			 */
			$priority = 0;
			foreach ( $option_parameters as $id => $option_parameter ) {

				// error_log( $option_parameter['name'] );
				// if ( empty( EGF_Register_Options::get_linked_controls( $option_parameter['name'] ) ) ) {
				// 	error_log( 'this has no linked controls' );
				// } else {
				// 	error_log( 'this does have linked controls' );
				// }

				// Add the setting.
				$this->add_setting( $wp_customize, $option_parameter );

				// Set control $priority
				$priority += 20;

				// Add the control.
				$this->add_control( $wp_customize, $option_parameter, $priority );

			}
		}

		/**
		 * Add Setting
		 * 
		 * @param [type] $wp_customize     [description]
		 * @param [type] $option_parameter [description]
		 *
		 * @since 1.4.0
		 * @version 1.4.2
		 * 
		 */
		public function add_setting( $wp_customize, $option_parameter ) {
			/**
			 * Set Transport Method:
			 * 
			 * Default is to reload the iframe when the option is 
			 * modified in the customizer. 
			 * 
			 * DEVELOPER NOTE: To change the transport type for each 
			 * option modify the 'transport' value for the appropriate 
			 * option in the $options array found in:
			 * tt_font_get_option_parameters()
			 * 
			 */
			$transport = empty( $option_parameter['transport'] ) ? 'refresh' : $option_parameter['transport'];

			/**
			 * Add Setting To Customizer:
			 * 
			 * Adds $option_parameter setting to 
			 * the customizer.
			 * 
			 */
			$wp_customize->add_setting( 'tt_font_theme_options[' . $option_parameter['name'] . ']', array(
				'default'        => $option_parameter['default'],
				'type'           => 'option',
				'transport'      => $transport,
			) );
		}

		/**
		 * Add Control
		 * 
		 * @param [type]  $wp_customize     [description]
		 * @param [type]  $option_parameter [description]
		 * @param integer $priority         [description]
		 *
		 * @since 1.4.0
		 * @version 1.4.2
		 * 
		 */
		public function add_control( $wp_customize, $option_parameter, $priority = 10 ) {
			/**
			 * Section Prefix:
			 *
			 * Add the 'tt_font_' prefix to prevent namespace
			 * collisions. Removes the prefix if we are adding
			 * this option to a default WordPress section.
			 *  
			 */
			$prefix = empty( $option_parameter['wp_section'] ) ? 'tt_font_' : '' ;

			switch ( $option_parameter['type'] ) {
				case 'font' :
					$wp_customize->add_control( 
						new EGF_Font_Control( 
							$wp_customize, 
							$option_parameter['name'], 
							array(
								'id'       => '',
								'label'    => $option_parameter['title'],
								'section'  => $prefix . $option_parameter['tab'],
								'settings' => 'tt_font_theme_options['. $option_parameter['name'] . ']',
								'priority' => $priority,
								'option'   => $option_parameter,
							)
						) 
					);
					break;

				// Here in case we decide to implement 
				// an additional lightweight control.
				case 'font_basic':
					break;
			}
		}
	}
endif;
