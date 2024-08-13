<?php 
namespace ElementsKit_Lite\Libs\Template_Library;

defined( 'ABSPATH' ) || exit;

/**
 * Class Init
 * 
 * Initializes the Template Library of the ElementsKit Lite plugin.
 */
class Init {
	/**
	 * Initializes the Init class.
	 * 
	 * Includes necessary files.
	 */
	public function __construct() {
		add_action('activate_gutenkit-blocks-addon/gutenkit-blocks-addon.php', array( $this, 'load_gutenkit_plugin' ), 9999);
		add_action( 'admin_enqueue_scripts', array( $this, 'library_admin_enqueue_scripts' ) );
	}

	/**
	 * Retrieves the URL of the Template Library.
	 * 
	 * @return string The URL of the Template Library.
	 * @since 3.1.4
	 */
	public static function get_url() {
		return \ElementsKit_Lite::lib_url() . 'template-library/';
	}

	/**
	 * Retrieves the directory of the Template Library.
	 * 
	 * @return string The directory of the Template Library.
	 * @since 3.1.4
	 */
	public static function get_dir() {
		return \ElementsKit_Lite::lib_dir() . 'template-library/';
	}

	/**
	 * Loads the GutenKit plugin.
	 * @since 3.1.4
	 * Deletes the 'gutenkit_do_activation_redirect' option.
	 */
	public function load_gutenkit_plugin() {
		delete_option( 'gutenkit_do_activation_redirect' );
	}

	/**
	 * Enqueue scripts and styles for the template library in the admin area.
	 *
	 * @param string $screen The current admin screen.
	 * @since 3.1.4
	 */
	public function library_admin_enqueue_scripts($screen) {
		// Enqueue block editor only JavaScript and CSS.
		$post_editor_template_library = include self::get_dir() . 'assets/library/post-editor-template-library.asset.php';
		$site_editor_template_library = include self::get_dir() . 'assets/library/site-editor-template-library.asset.php';
		
		if ( $screen === 'post.php' || $screen === 'post-new.php') {
			wp_enqueue_script(
				'gutenkit-post-editor-template-library',
				self::get_url() . 'assets/library/post-editor-template-library.js',
				$post_editor_template_library['dependencies'],
				$post_editor_template_library['version'],
				true
			);
	
			wp_enqueue_style(
				'gutenkit-post-editor-library',
				self::get_url() . 'assets/library/post-editor-template-library.css',
				array(),
				$post_editor_template_library['version']
			);
			// Google Roboto Font
			wp_enqueue_style(
				'gutenkit-google-fonts', 
				'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap'
			);
		}
	
		if ( $screen === 'site-editor.php') {
			wp_enqueue_script(
				'gutenkit-site-editor-library',
				self::get_url() . 'assets/library/site-editor-template-library.js',
				$site_editor_template_library['dependencies'],
				$site_editor_template_library['version'],
				true
			);
	
			wp_enqueue_style(
				'gutenkit-site-editor-library',
				self::get_url() . 'assets/library/site-editor-template-library.css',
				array(),
				$site_editor_template_library['version']
			);
			// Google Roboto Font
			wp_enqueue_style(
				'gutenkit-google-fonts', 
				'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap'
			);
		}
	}
}
