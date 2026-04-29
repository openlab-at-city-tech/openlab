<?php
/**
 * Button Styling Loader for Astra theme.
 *
 * @package     Astra
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Initialization
 *
 * @since 3.0.0
 */
class Astra_Footer_Button_Component_Loader {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'astra_get_fonts', array( $this, 'add_fonts' ), 1 );
	}

	/**
	 * Add Font Family Callback
	 *
	 * @return void
	 */
	public function add_fonts() {
		/**
		 * Footer - Button
		 */
		$num_of_footer_button = Astra_Builder_Helper::$num_of_footer_button;
		for ( $index = 1; $index <= $num_of_footer_button; $index++ ) {
			if ( ! Astra_Builder_Helper::is_component_loaded( 'button-' . $index, 'footer' ) ) {
				continue;
			}

			$_prefix = 'button' . $index;

			$btn_font_family = astra_get_option( 'footer-' . $_prefix . '-font-family' );
			$btn_font_weight = astra_get_option( 'footer-' . $_prefix . '-font-weight' );
			Astra_Fonts::add_font( $btn_font_family, $btn_font_weight );
		}
	}

}

/**
 *  Kicking this off by creating the object of the class.
 */
new Astra_Footer_Button_Component_Loader();
