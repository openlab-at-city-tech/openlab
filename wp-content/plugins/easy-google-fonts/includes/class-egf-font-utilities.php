<?php
/**
 * Class: EGF_Font_Utilities
 *
 * This file initialises the admin functionality for this plugin.
 * It initalises a posttype that acts as a data structure for
 * the font controls. It also has useful static helper functions
 * to get font controls.
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
if ( ! class_exists( 'EGF_Font_Utilities' ) ) :
	class EGF_Font_Utilities {
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

		public static $plugin_slug = "easy-google-fonts";

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
			self::$plugin_slug = $plugin->get_plugin_slug();
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
		 * Get Default Websafe Fonts
		 *
		 * Defines a list of default websafe fonts and generates
		 * an array with all of the necessary properties. Returns
		 * all of the fonts as an array to the user.
		 *
		 * Custom Filters:
		 *     - 'tt_font_default_fonts_array'
		 *     - 'tt_font_get_default_fonts'
		 *
		 * Transients:
		 *     - 'tt_font_default_fonts'
		 *
		 * @return array $fonts - All websafe fonts with their properties
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_default_fonts() {
			if ( false === get_transient( 'tt_font_default_fonts' ) ) {

				// Declare default font list
				$font_list = array(
						'Arial'               => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Century Gothic'      => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Courier New'         => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Georgia'             => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Helvetica'           => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Impact'              => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Lucida Console'      => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Lucida Sans Unicode' => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Palatino Linotype'   => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'sans-serif'          => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'serif'               => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Tahoma'              => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Trebuchet MS'        => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Verdana'             => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
				);

				// Build font list to return
				$fonts = array();
				foreach ( $font_list as $font => $attributes ) {

					$urls = array();

					// Get font properties from json array.
					foreach ( $attributes['weights'] as $variant ) {
						$urls[ $variant ] = "";
					}

					// Create a font array containing it's properties and add it to the $fonts array
					$atts = array(
							'name'         => $font,
							'font_type'    => 'default',
							'font_weights' => $attributes['weights'],
							'subsets'      => array(),
							'files'        => array(),
							'urls'         => $urls,
					);

					// Add this font to all of the fonts
					$id           = strtolower( str_replace( ' ', '_', $font ) );
					$fonts[ $id ] = $atts;
				}

				// Filter to allow us to modify the fonts array before saving the transient
				$fonts = apply_filters( 'tt_font_default_fonts_array', $fonts );

				// Set transient for google fonts (for 2 weeks)
				set_transient( 'tt_font_default_fonts', $fonts, 14 * DAY_IN_SECONDS );

			} else {
				$fonts = get_transient( 'tt_font_default_fonts' );
			}

			// Return the font list
			return apply_filters( 'tt_font_get_default_fonts', $fonts );
		}

		/**
		 * Get Default Google Fonts
		 *
		 * Fetches all of the current fonts as a JSON object using
		 * the google font API and outputs it as a PHP Array. This
		 * is an internal function designed to flag outdated and
		 * new fonts so that we can update the fonts array list
		 * accordingly. Falls back to retrieving a manual list if
		 * the json request was unsuccessful.
		 *
		 * DEVELOPER NOTE:
		 *
		 * For this function to work correctly you
		 * would need to sign up for a google API Key and enter it
		 * into the settings page.
		 *
		 * Custom Filters:
		 *     - 'tt_font_google_fonts_array'
		 *     - 'tt_font_get_google_fonts'
		 *
		 * Transients:
		 *     - 'tt_font_google_fonts'
		 *
		 * @return array $fonts - All websafe fonts with their properties
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_google_fonts() {
			/**
			 * Google Fonts API Key
			 *
			 * Please enter the developer API Key for unlimited requests
			 * to google to retrieve all fonts. If you do not enter an API
			 * key google will
			 *
			 * {@link https://developers.google.com/fonts/docs/developer_api}
			 */

			$api_key = self::get_google_api_key();
			$api_url = $api_key ? "&key={$api_key}" : "";

			// Variable to hold fonts;
			$fonts = array();
			$json  = array();

			// Check if transient is set
			if ( false === get_transient( 'tt_font_google_fonts_list' ) ) {

				/*
				 * First we want to try to update the font transient with the
				 * latest fonts if possible by sending an API request to google.
				 * If this is not possible then the theme will just use the
				 * current list of webfonts.
				 */

				// Get list of fonts as a JSON Object from Google's server
				$response = wp_remote_get( "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha{$api_url}", array( 'sslverify' => false ) );

				/*
				 * Now we want to check that the request has a valid response
				 * from google. If the request is not valid then we fall back
				 * to the webfonts.json file.
				 */
				// Check it is a valid request
				if ( ! is_wp_error( $response ) ) {

					$font_list = self::json_decode( $response['body'], true );

					// Make sure that the valid response from google is not an error message
					if ( ! isset( $font_list['error'] ) ) {
						$json = $response['body'];

					} else {
						$json  = wp_remote_fopen( plugins_url( self::$plugin_slug ) . '/assets/fonts/webfonts.json' );
					}

				} else {
					$json  = wp_remote_fopen( plugins_url( self::$plugin_slug ) . '/assets/fonts/webfonts.json' );
				}

				/**
				 * Pull in raw file from the WordPress subversion
				 * repository as a last resort.
				 *
				 */
				if ( false == $json ) {
					$fonts_from_repo = wp_remote_fopen( "https://plugins.svn.wordpress.org/easy-google-fonts/trunk/assets/fonts/webfonts.json", array( 'sslverify' => false ) );
					$json            = $fonts_from_repo;
				}

				$font_output = self::json_decode( $json, true );

				foreach ( $font_output['items'] as $item ) {

					$urls = array();

					// Get font properties from json array.
					foreach ( $item['variants'] as $variant ) {

						$name = str_replace( ' ', '+', $item['family'] );
						$urls[ $variant ] = "https://fonts.googleapis.com/css?family={$name}:{$variant}";

					}

					$atts = array(
						'name'         => $item['family'],
						'category'     => $item['category'],
						'font_type'    => 'google',
						'font_weights' => $item['variants'],
						'subsets'      => $item['subsets'],
						'files'        => $item['files'],
						'urls'         => $urls
					);

					// Add this font to the fonts array
					$id           = strtolower( str_replace( ' ', '_', $item['family'] ) );
					$fonts[ $id ] = $atts;

				}

				// Filter to allow us to modify the fonts array before saving the transient
				$fonts = apply_filters( 'tt_font_google_fonts_array', $fonts );

				// Set transient for google fonts
				set_transient( 'tt_font_google_fonts_list', $fonts, 14 * DAY_IN_SECONDS );

			} else {
				$fonts = get_transient( 'tt_font_google_fonts_list' );
			}

			return apply_filters( 'tt_font_get_google_fonts', $fonts );
		}

		/**
		 * Get All Fonts
		 *
		 * Merges the default system fonts and the google fonts
		 * into a single array and returns it
		 *
		 * @return array All fonts with their properties
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_all_fonts() {
			$default_fonts = self::get_default_fonts();
			$google_fonts  = self::get_google_fonts();

			if ( ! $default_fonts ) {
				$default_fonts = array();
			}

			if ( ! $google_fonts ) {
				$google_fonts = array();
			}

			return array_merge( $default_fonts, $google_fonts );
		}

		/**
		 * Get Google Font API Key
		 *
		 * Returns the google api key if it has been set.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/get_option 	get_option()
		 *
		 * Custom Filters:
		 *     - 'tt_font_default_google_api_key'
		 *
		 * @return string $api_key - The Google API Key
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_google_api_key() {
			$api_key = "";
			$api_key = get_option( 'tt-font-google-api-key', '' );
			$api_key = apply_filters( 'tt_font_default_google_api_key', $api_key );
			return trim( $api_key );
		}

		/**
		 * Set Google Font API Key
		 *
		 * Sets the google api key with the value passed in
		 * the parameter.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/update_option 	update_option()
		 *
		 * @return string $api_key - The Google API Key
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function set_google_api_key( $api_key ) {
			update_option( 'tt-font-google-api-key', trim( $api_key ) );
		}

		/**
		 * Google Font API Key Validation
		 *
		 * Boolean function that checks the validity of a google
		 * api key and returns true if it is valid and false if
		 * it is not a valid api key.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/wp_remote_get 	wp_remote_get()
		 *
		 * @return string $api_key - The Google API Key
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function is_valid_google_api_key( $api_key = '' ) {
			$api_url  = $api_key ? "&key={$api_key}" : "";
			$response = wp_remote_get( "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha{$api_url}", array( 'sslverify' => false ) );

			// Check it is a valid request
			if ( ! is_wp_error( $response ) ) {

				$font_list = self::json_decode( $response['body'], true );

				// Make sure that the valid response from google is not an error message
				if ( ! isset( $font_list['error'] ) ) {
					return true;
				} else {
					return false;
				}

			} else {
				return false;
			}
		}

		/**
		 * Delete Default Websafe Fonts
		 *
		 * Defines a list of default websafe fonts and generates
		 * an array with all of the necessary properties. Returns
		 * all of the fonts as an array to the user.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function delete_font_transients() {
			delete_transient( 'tt_font_default_fonts' );
			delete_transient( 'tt_font_google_fonts_list' );
			delete_transient( 'tt_font_google_fonts' );
		}

		/**
		 * Get Individual Fonts
		 *
		 * Takes an id and returns the corresponding font.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/apply_filters  	apply_filters()
		 *
		 * Custom Filters:
		 *     - 'tt_font_get_font'
		 *
		 * @return array $fonts - All websafe fonts with their properties
		 *
		 * @since 1.2
		 * @version 1.4.4
		 *
		 */
		public static function get_font( $id = '' ) {
			// Get all fonts
			$default_fonts = self::get_default_fonts();
			$google_fonts  = self::get_google_fonts();

			// Check if it is set and return if found
			if ( isset( $default_fonts[ $id ] ) ) {

				// Return default font from array if set
				return apply_filters( 'tt_font_get_font', $default_fonts[ $id ] );

			} else if ( isset( $google_fonts[ $id ] ) ) {

				// Return google font from array if set
				return apply_filters( 'tt_font_get_font', $google_fonts[ $id ] );

			} else {
				return false;
			}
		}

		/**
		 * Decode JSON
		 *
		 * Attempts to decode json into an array.
		 * This new function accounts for servers
		 * running an older version of PHP with
		 * magic quotes gpc enabled.
		 *
		 *
		 * @param  string  $str   - JSON string to convert into an array
		 * @param  boolean $accoc [- Whether to return an associative array
		 * @return array - Decoded JSON array
		 *
		 * @since 1.3.7
		 * @version 1.4.4
		 *
		 */
		public static function json_decode( $str = '', $accoc = false ) {
			$json_string = get_magic_quotes_gpc() ? stripslashes( $str ) : $str;
			return json_decode( $json_string, $accoc );
		}
	}
endif;
