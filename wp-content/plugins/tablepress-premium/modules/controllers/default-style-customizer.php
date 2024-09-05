<?php
/**
 * TablePress Default Style Customizer.
 *
 * @package TablePress
 * @subpackage Default Style Customizer
 * @author Tobias Bäthge
 * @since 2.2.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Default Style Customizer feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.2.0
 */
class TablePress_Module_Default_Style_Customizer {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			self::init_admin();
		}
	}

	/**
	 * Initializes the admin screens of the Default Style Customizer module.
	 *
	 * @since 2.2.0
	 */
	public static function init_admin(): void {
		add_filter( 'tablepress_load_file_full_path', array( __CLASS__, 'change_options_view_full_path' ), 10, 3 );
		add_filter( 'tablepress_load_class_name', array( __CLASS__, 'change_view_options_class_name' ) );
	}

	/**
	 * Loads the Default Style Customizer view class when the TablePress Options view class is loaded.
	 *
	 * @since 2.2.0
	 *
	 * @param string $full_path Full path of the class file.
	 * @param string $file      File name of the class file.
	 * @param string $folder    Folder name of the class file.
	 * @return string Modified full path.
	 */
	public static function change_options_view_full_path( string $full_path, string $file, string $folder ): string {
		if ( 'view-options.php' === $file ) {
			require_once $full_path; // Load desired file first, as we inherit from it in the new $full_path file.
			$full_path = TABLEPRESS_ABSPATH . 'modules/views/view-default_style_customizer.php';
		}
		return $full_path;
	}

	/**
	 * Changes Options View class name, to load extended view.
	 *
	 * @since 2.2.0
	 *
	 * @param string $class_name Name of the class that shall be loaded.
	 * @return string Changed class name.
	 */
	public static function change_view_options_class_name( string $class_name ): string {
		if ( 'TablePress_Options_View' === $class_name ) {
			$class_name = 'TablePress_Default_Style_Customizer_View';
		}
		return $class_name;
	}

} // class TablePress_Module_Default_Style_Customizer
