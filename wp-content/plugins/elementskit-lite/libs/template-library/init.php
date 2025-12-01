<?php

namespace ElementsKit_Lite\Libs\Template_Library;

defined('ABSPATH') || exit;

/**
 * Class Init
 *
 * Initializes the Template Library of the ElementsKit Lite plugin.
 */
class Init{
	/**
	 * Initializes the Init class.
	 *
	 * Includes necessary files.
	 */
	public function __construct(){
		add_action('activate_gutenkit-blocks-addon/gutenkit-blocks-addon.php', array($this, 'load_gutenkit_plugin'), 9999);
		add_action('enqueue_block_editor_assets', array($this, 'library_enqueue_block_editor_assets'));
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
		delete_option('gutenkit_do_activation_redirect');
	}

	/**
	 * Enqueue scripts and styles for the template library in the admin area.
	 *
	 * @param string $screen The current admin screen.
	 * @since 3.1.4
	 */
	public function library_enqueue_block_editor_assets() {
		// Enqueue block editor only JavaScript and CSS.
		$file_path = self::get_dir() . 'assets/library/editor-template-library.asset.php';
		if (file_exists($file_path)) {
			$editor_template_library = include $file_path;
			wp_enqueue_script(
				'gutenkit-editor-template-library',
				self::get_url() . 'assets/library/editor-template-library.js',
				$editor_template_library['dependencies'],
				$editor_template_library['version'],
				[
					'in_footer' => true,
				]
			);

			wp_enqueue_style(
				'gutenkit-editor-template-library',
				self::get_url() . 'assets/library/editor-template-library.css',
				array(),
				$editor_template_library['version']
			);
		}
	}
}
