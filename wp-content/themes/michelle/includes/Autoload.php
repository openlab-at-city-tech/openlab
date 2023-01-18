<?php
/**
 * PSR-4 autoloader.
 *
 * Via Composer or custom.
 *
 * @link  https://www.php-fig.org/psr/psr-4/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( file_exists( MICHELLE_PATH_VENDOR . 'autoload.php' ) ) {
	require_once MICHELLE_PATH_VENDOR . 'autoload.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
} else {

	class Michelle_Autoload {

		/**
		 * Theme PHP class namespace.
		 *
		 * @since   1.0.0
		 * @access  private
		 * @var     string
		 */
		private static $namespace = 'WebManDesign\Michelle';

		/**
		 * Directory to load PHP classes from.
		 *
		 * @since   1.0.0
		 * @access  private
		 * @var     string
		 */
		private static $directory = 'includes';

		/**
		 * Array of white-listed, allowed files for improved security.
		 *
		 * TIP:
		 * Can be obtained in code editor by searching for `namespace `
		 * in `michelle/includes/*.php` files.
		 *
		 * @since    1.0.0
		 * @version  1.3.0
		 *
		 * @access  private
		 * @var     array
		 */
		private static $allowed_files = array(
			'/Component_Interface.php',
			'/Theme.php',

			'/Accessibility/Component.php',

			'/Assets/Component.php',
			'/Assets/Editor.php',
			'/Assets/Factory.php',
			'/Assets/Scripts.php',
			'/Assets/Styles.php',

			'/Content/Block.php',
			'/Content/Block_Area.php',
			'/Content/Block_Patterns.php',
			'/Content/Block_Styles.php',
			'/Content/Component.php',
			'/Content/Container.php',
			'/Content/Starter.php',

			'/Customize/Colors.php',
			'/Customize/Component.php',
			'/Customize/Control.php',
			'/Customize/CSS_Variables.php',
			'/Customize/Custom_Logo.php',
			'/Customize/Mod.php',
			'/Customize/Options.php',
			'/Customize/Options_Conditional.php',
			'/Customize/Options_Partial_Refresh.php',
			'/Customize/Preview.php',
			'/Customize/RGBA.php',
			'/Customize/Sanitize.php',
			'/Customize/Styles.php',

				'/Customize/Control/HTML.php',
				'/Customize/Control/Multiselect.php',
				'/Customize/Control/Select.php',
				'/Customize/Control/Text.php',

			'/Entry/Component.php',
			'/Entry/Media.php',
			'/Entry/Navigation.php',
			'/Entry/Page_Template.php',
			'/Entry/Post_Class.php',
			'/Entry/Summary.php',

			'/Footer/Component.php',
			'/Footer/Container.php',

			'/Header/Body_Class.php',
			'/Header/Component.php',
			'/Header/Container.php',
			'/Header/Head.php',

			'/Loop/Component.php',
			'/Loop/Featured_Posts.php',
			'/Loop/Pagination.php',

			'/Menu/Component.php',

			'/Plugin/Component.php',

				'/Plugin/Beaver_Builder/Component.php',

				'/Plugin/One_Click_Demo_Import/Component.php',

			'/Setup/Component.php',
			'/Setup/Editor.php',
			'/Setup/Media.php',
			'/Setup/Post_Type.php',
			'/Setup/Upgrade.php',

			'/Theme_Hook_Alliance/Component.php',

			'/Tool/AMP.php',
			'/Tool/Component.php',
			'/Tool/Google_Fonts.php',
			'/Tool/KSES.php',
			'/Tool/Page_Builder.php',
			'/Tool/Wrapper.php',

			'/Welcome/Component.php',
		);

		/**
		 * Register custom autoload.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $class_name  Class name to load.
		 *
		 * @return  bool  True if the class was loaded, false otherwise.
		 */
		public static function register( $class_name ) {

			// Requirements check

				if ( 0 !== strpos( $class_name, self::$namespace . '\\' ) ) {
					return false;
				}


			// Variables

				$path  = '';
				$parts = explode( '\\', substr( $class_name, strlen( self::$namespace . '\\' ) ) );


			// Processing

				foreach ( $parts as $part ) {
					$path .= '/' . $part;
				}
				$path .= '.php';

				if ( ! in_array( $path, self::$allowed_files ) ) {
					return false;
				}

				$path = get_template_directory() . '/' . self::$directory . $path;

				if ( ! file_exists( $path ) ) {
					return false;
				}

				require_once $path; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound


			// Output

				return true;

		} // /register

	}

	spl_autoload_register( 'Michelle_Autoload::register' );

}
