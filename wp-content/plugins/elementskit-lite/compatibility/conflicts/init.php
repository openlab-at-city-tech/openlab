<?php
namespace ElementsKit_Lite\Compatibility\Conflicts;

defined( 'ABSPATH' ) || exit;


/**
 * Init
 * Initiate all necessary classes, hooks, configs.
 *
 * @since 1.2.6
 */
class Init {

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
	 * @since 1.2.6
	 * @access public
	 * @static
	 *
	 * @return Init An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {

			// Fire when ElementsKit_Lite instance.
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Construct the plugin object.
	 *
	 * @since 1.2.6
	 * @access public
	 */
	public function __construct() {
		Scripts::instance();
	}


}
