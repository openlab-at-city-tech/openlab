<?php
namespace ElementsKit_Lite\Compatibility\Wpml;

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

		if(defined('ICL_SITEPRESS_VERSION')){
			add_filter( 'elementor/documents/get/post_id', [$this, 'wpml_template_translation']);
		}
	}

	/**
	* Get the ID in the current language or in another language you specify
	* @param $id
	* @return string or array of object ids
	* @since 2.6.1 
	* @access public
	*/
	public function wpml_template_translation($element_id){

		$element_type = get_post_type($element_id);

		if (in_array($element_type, ['elementskit_template', 'elementskit_content'])) {
			return apply_filters('wpml_object_id', $element_id, $element_type, true);
		}

		return $element_id;
	}
}