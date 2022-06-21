<?php
namespace ElementsKit_Lite\Compatibility\Conflicts;

defined( 'ABSPATH' ) || exit;


/**
 * Init
 * Initiate all necessary classes, hooks, configs.
 *
 * @since 1.2.6
 */
class Scripts {

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

		add_action( 'admin_enqueue_scripts', array( $this, 'deregister_from_admin' ) );
	}
	

	/**
	 * Conflicted script deregister function
	 *
	 * @since 1.2.6
	 * @access public
	 */
	public function deregister_from_admin() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-elementskit_template', 'toplevel_page_elementskit', 'elementskit_page_elementskit-license', 'nav-menus' ) ) ) {
			wp_deregister_script( 'wpsp_wp_admin_jquery2' );
		}
	}


}
