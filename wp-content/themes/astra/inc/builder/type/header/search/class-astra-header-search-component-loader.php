<?php
/**
 * Search Styling Loader for Astra theme.
 *
 * @package     astra-builder
 * @link        https://wpastra.com/
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Initialization
 *
 * @since 3.0.0
 */
class Astra_Header_Search_Component_Loader {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		add_filter( 'astra_get_search', array( $this, 'get_search_markup' ), 10, 3 );
	}

	/**
	 * Adding Wrapper for Search Form.
	 *
	 * @since 3.0.0
	 *
	 * @param string $search_markup   Search Form Content.
	 * @param string $option    Search Form Options.
	 * @param string $device    Device Desktop/Tablet/Mobile.
	 * @return Search HTML structure created.
	 */
	public static function get_search_markup( $search_markup, $option = '', $device = '' ) {

		if ( is_customize_preview() ) {
			Astra_Builder_UI_Controller::render_customizer_edit_button();
		}

		return $search_markup;
	}
}

/**
 *  Kicking this off by creating the object of the class.
 */
new Astra_Header_Search_Component_Loader();
