<?php
namespace Wpmet\Libs;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Wpmet\Libs\Forms' ) ) :

	class Forms {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;
	
		/**
		* Instance.
		*
		* Ensures only one instance of the plugin class is loaded or can be loaded.
		*
		* @since 2.6.3
		* @access public
		* @static
		*
		* @return Init An instance of the class.
		*/
		public static function instance() {

			if ( is_null( self::$instance ) ) {
	
				self::$instance = new self();
			}
	
			return self::$instance;
		}

		/**
		* Construct the plugin object.
		*
		* @since 2.6.3
		* @access public
		*/
		public function __construct() {

			// register admin menus
			add_action('admin_menu', [$this, 'register_sub_menu'], 999);

			// register js/ css
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	
			// check for metform plugin is active or not.
			if(in_array('metform/metform.php', apply_filters('active_plugins', get_option('active_plugins'))) && is_admin()) {
				add_action('current_screen', [$this, 'redirect_to_metform_page']);
			}
		}

		/**
		* Get forms dir.
		*
		* @since 2.6.3
		* @access public
		*/
		public static function get_dir() {

			return \ElementsKit_Lite::lib_dir() . 'forms/';
		}

		/**
		* Get forms dir url.
		*
		* @since 2.6.3
		* @access public
		*/
		public static function get_url() {

			return \ElementsKit_Lite::lib_url() . 'forms/';
		}

		/**
		* Enqueuing js/ css
		*
		* @since 2.6.3
		* @access public
		*/
		public function enqueue_scripts() {

			$current_screen = get_current_screen();

			if(!empty($current_screen->id) && $current_screen->id === 'elementskit_page_forms') {
				wp_enqueue_style( 'elementskit-forms', self::get_url() . 'assets/css/forms.css', [], \ElementsKit_Lite::version() );
				wp_enqueue_script('elementskit-forms', self::get_url() . 'assets/js/forms.js', ['jquery'], \ElementsKit_Lite::version(), true);
			}
		}

		/**
		* Adds a submenu page under elementskit menu.
		*
		* @since 2.6.3
		* @access public
		*/
		public function register_sub_menu() {

			add_submenu_page(
				'elementskit',
				esc_html__('Forms', 'elementskit-lite'),
				esc_html__('Forms', 'elementskit-lite'),
				'manage_options',
				'forms',
				[$this, 'form_page_callback']
			);
		}

		/**
		* Display callback for the form submenu page.
		*
		* @since 2.6.3
		* @access public
		*/
		public function form_page_callback() {

			// Include template file
			include self::get_dir() . 'pages/template.php';
		}

		/**
		* Redirect to metfor plugin page
		*
		* @since 2.6.3
		* @access public
		*/
		public function redirect_to_metform_page() {

			$current_screen = get_current_screen();

			if(!empty($current_screen->id) && $current_screen->id === 'elementskit_page_forms') {
				wp_safe_redirect(admin_url('edit.php?post_type=metform-form'));
				exit;
			}
		}
	}

endif;
