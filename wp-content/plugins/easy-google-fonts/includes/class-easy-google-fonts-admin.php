<?php
/**
 * Class: Easy_Google_Fonts_Admin
 *
 * This file initialises the admin functionality for this plugin.
 * It is used to initialise the font control admin management page.
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
if ( ! class_exists( 'Easy_Google_Fonts_Admin' ) ) :
	class Easy_Google_Fonts_Admin {
		
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
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_actions() {
			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
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
			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( dirname( __FILE__ ) ) . $this->plugin_slug . '.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @return    null    Return early if no settings page is registered.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function enqueue_admin_styles() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				wp_deregister_style( $this->plugin_slug .'-admin-styles' );
				wp_register_style( 
					$this->plugin_slug .'-admin-styles', 
					Easy_Google_Fonts::get_css_url() . '/admin.css', 
					array(), 
					Easy_Google_Fonts::VERSION 
				);
				wp_enqueue_style( $this->plugin_slug .'-admin-styles' );
			}
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @return    null    Return early if no settings page is registered.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function enqueue_admin_scripts() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {

				// Load jQuery and jQuery UI
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'utils' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-effects-core' );
				wp_enqueue_script( 'jquery-effects-fade' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );
				wp_enqueue_script( 'jquery-ui-position' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-droppable' );

				// Load PostBox
				wp_enqueue_script( 'postbox' );

				if ( wp_is_mobile() ) {
					wp_enqueue_script( 'jquery-touch-punch' );
				}
						
				// Load Tag-it script
				wp_deregister_script( $this->plugin_slug . '-tag-it-admin-script' );
				wp_register_script( 
					$this->plugin_slug . '-tag-it-admin-script', 
					Easy_Google_Fonts::get_js_url() . '/tag-it.js',
					array( 'jquery' ), 
					Easy_Google_Fonts::VERSION 
				);
				wp_enqueue_script( $this->plugin_slug . '-tag-it-admin-script' );

				// Load admin page js
				wp_deregister_script( $this->plugin_slug . '-admin-script' );
				wp_register_script( 
					$this->plugin_slug . '-admin-script', 
					Easy_Google_Fonts::get_js_url() . '/admin.js',
					array( 'jquery','jquery-ui-core', 'jquery-ui-widget' ), 
					Easy_Google_Fonts::VERSION 
				);
				wp_enqueue_script( $this->plugin_slug . '-admin-script' );

				// Load in customizer control javascript object
				wp_localize_script( $this->plugin_slug . '-admin-script', 'ttFontl10n', $this->get_l10n() );
			}
		}

		/**
		 * Get Translation Object
		 *
		 * Returns an array of strings to be enqueued as a 
		 * JSON object on the admin page. This allows the 
		 * plugin to remain fully translatable. Developers
		 * can add/modify this array by hooking into the
		 * appropriate filter.
		 *
		 * Custom Filters:
		 *     - tt_font_admin_l10n
		 *
		 * 
		 * @return array $l10n - An array of translatable strings.
		 *
		 * @since 1.3.2
		 * @version 1.4.4
		 * 
		 */
		public function get_l10n() {
			// Create an array of translatable strings
			$l10n = array(
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
				'themeDefault'         => __( '&mdash; Theme Default &mdash;', 'easy-google-fonts' ),
			);

			// Return the l10n array
			return apply_filters( 'tt_font_admin_l10n', $l10n );
		}

		/**
		 * Add Admin Menu 
		 * 
		 * Register the administration menu for this plugin 
		 * into the WordPress Dashboard menu.
		 *
		 * @link http://codex.wordpress.org/Administration_Menus	Administration Menus
		 * @link http://codex.wordpress.org/Roles_and_Capabilities 	Roles and Capabilities
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function add_plugin_admin_menu() {

			/**
			 * Add a settings page for this plugin to the Settings menu.
			 *
			 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
			 *
			 *   Administration Menus: http://codex.wordpress.org/Administration_Menus
			 *
			 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
			 */
			$this->plugin_screen_hook_suffix = add_options_page(
				__( 'Easy Google Fonts', $this->plugin_slug ),
				__( 'Google Fonts', $this->plugin_slug ),
				'edit_theme_options',
				$this->plugin_slug,
				array( $this, 'display_plugin_admin_page' )
			);

			/*
			 * Use the retrieved $this->plugin_screen_hook_suffix to hook the function that enqueues our 
			 * contextual help tabs. This hook invokes the function only on our plugin administration screen,
			 * see: http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
			 */
			add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'add_help_tabs' ) );
			add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'add_screen_option' ) );
		}

		/**
		 * Output Admin Page
		 * 
		 * Render the settings page for this plugin.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function display_plugin_admin_page() {
			$controller = new EGF_Admin_Controller();
			$controller->render();
		}

		/**
		 * Add Action Links
		 * 
		 * Add settings action link to the plugins page.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */	
		public function add_action_links( $links ) {

			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
				),
				$links
			);
		}
		
		/**
		 * Get Screen Tab Options
		 *
		 * This function has been created in order to give developers
		 * a hook by which to add their own screen options.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function add_screen_option() {
			
			// Bail if hook not defined
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				// Developers: Add Options Below
			}
		}

		/**
		 * Add Help Tabs To The Font Generator Admin Page
		 *
		 * Adds contextual help tabs to the custom themes fonts page.
		 * This function is attached to an action that ensures that the
		 * help tabs are only displayed on the custom admin page.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function add_help_tabs() {

			// Bail if hook not defined
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			
				// Overview Tab
				$overview  = '<p>' . __( 'This screen is used for managing your custom font controls. It provides a way to create a custom font controls for any type of content in your theme.', 'easy-google-fonts' ) . '</p>';
				$overview .= '<p>' . __( 'From this screen you can:' ) . '</p>';
				$overview .= '<ul><li>' . __( 'Create, edit, and delete custom font controls.', 'easy-google-fonts' ) . '</li>';
				$overview .= '<li>' . __( 'Manage all of your custom font controls.', 'easy-google-fonts' ) . '</li>';
				$overview .= '<li>' . __( 'Add a Google API key in order to enable automatic font updates.', 'easy-google-fonts' ) . '</li></ul>';
				$overview .= '<p><strong>' . __( 'Please Note: ', 'easy-google-fonts' ) . '</strong>';
				$overview .= __( 'This screen is used to manage/create new font controls. To preview fonts for each control please visit the typography section in the ', 'easy-google-fonts' );
				$overview .= '<a href="' . admin_url( 'customize.php' ) . '">' . __( 'customizer', 'easy-google-fonts' ) . '</a></p>';

				
				$screen->add_help_tab( array(
					'id'      => 'overview',
					'title'   => __('Overview', 'easy-google-fonts'),
					'content' => $overview,
				) );

				$edit_content  = '';
				$edit_content .= '<p>' . 'This screen is used for creating and managing individual custom font controls.'  . '</p>';
				$edit_content .= '<p>' . __( 'From this screen you can:' ) . '</p>';
				$edit_content .= '<ul><li>' . __( 'Create, edit, and delete custom font controls.', 'easy-google-fonts' ) . '</li>';
				$edit_content .= '<li>' . __( 'Add CSS Selectors: Add any CSS selectors/styles that you want this custom font control to manage.', 'easy-google-fonts' ) . '</li>';
				$edit_content .= '<li>' . __( "Force Styles Override (Optional): If your theme is forcing any styles in it's stylesheet for any styles managed by this control then check this option to force a stylesheet override.", 'easy-google-fonts' ) . '</li></ul>';
				$edit_content .= '<p><strong>' . __( 'Please Note: ', 'easy-google-fonts' ) . '</strong>';
				$edit_content .= __( 'This screen is used to manage/create new font controls. To preview fonts for each control please visit the typography section in the ', 'easy-google-fonts' );
				$edit_content .= '<a href="' . admin_url( 'customize.php' ) . '">' . __( 'customizer', 'easy-google-fonts' ) . '</a></p>';

				
				$screen->add_help_tab( array(
					'id'      => 'edit-controls',
					'title'   => __( 'Edit Font Controls', 'easy-google-fonts'),
					'content' => $edit_content,
				) );

				$manage_content  = '';
				$manage_content .= '<p>' . 'This screen is used for managing all of your custom font controls.'  . '</p>';
				$manage_content .= '<p>' . __( 'From this screen you can:' ) . '</p>';
				$manage_content .= '<ul><li>' . __( 'View all of your custom font controls and the CSS selectors they are managing.', 'easy-google-fonts' ) . '</li>';
				$manage_content .= '<li>' . __( 'Delete any/all custom font controls.', 'easy-google-fonts' ) . '</li>';
				$manage_content .= '<li>' . __( "Force Styles Override (Optional): If your theme is forcing any styles in it's stylesheet for any styles managed by this control then check this option to force a stylesheet override.", 'easy-google-fonts' ) . '</li></ul>';
				$manage_content .= '<p><strong>' . __( 'Please Note: ', 'easy-google-fonts' ) . '</strong>';
				$manage_content .= __( 'This screen is used to manage/create new font controls. To preview fonts for each control please visit the typography section in the ', 'easy-google-fonts' );
				$manage_content .= '<a href="' . admin_url( 'customize.php' ) . '">' . __( 'customizer', 'easy-google-fonts' ) . '</a></p>';

				$screen->add_help_tab( array(
					'id'      => 'manage-controls',
					'title'   => __( 'Manage Font Controls', 'easy-google-fonts'),
					'content' => $manage_content,
				) );

				$api_content  = '<p><strong>' . __( 'How to get your Google API Key:', 'easy-google-fonts' ) . '</strong></p>';
				$api_content .= '<p>';
				$api_content .= '<ul>';
				$api_content .= '<li>' . __( 'Visit the <a href="https://code.google.com/apis/console" target="_blank">Google APIs Console</a>.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Create a new project.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Select APIs Under the APIs & auth menu.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Turn on Web Fonts Developer API.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Select Credentials under the APIs & auth menu.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Create a new browser key and <strong>leave the referrer box empty</strong>.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '<li>' . __( 'Once you have an API key, you can enter it in the Google Fonts API Key text field on the "Advanced" settings page.', 'easy-google-fonts' ) . '</li>';
				$api_content .= '</ul>';
				$api_content .= '</p>';
				$api_content .= '<p>' . __( 'Once you have entered a valid Google API key this plugin will automatically update itself with the latest fonts from google.', 'easy-google-fonts' ) . '</p>';
				
				$screen->add_help_tab( array(
					'id'      => 'api-key',
					'title'   => __( 'Google API Key Instructions', 'easy-google-fonts'),
					'content' => $api_content,
				) );

				$screen->set_help_sidebar(
					'<p><strong>' . __('For more information:', 'easy-google-fonts') . '</strong></p>' .
					'<p><a href="http://www.google.com/fonts#AboutPlace:about" target="_blank">' . __('Documentation on Google Fonts', 'easy-google-fonts') . '</a></p>' .
					'<p><a href="https://console.developers.google.com/" target="_blank">' . __( 'Get Google Fonts API Key', 'easy-google-fonts' ) . '</a></p>'
				);
			}	
		}


	}
endif;
