<?php
/**
 * Theme option conditionals class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize;

use WP_Customize_Control;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Options_Conditional {

	/**
	 * Are Google Fonts loaded via theme enabled?
	 *
	 * @since  1.0.0
	 *
	 * @param  WP_Customize_Control $control
	 *
	 * @return  bool
	 */
	public static function is_typography_google_fonts( WP_Customize_Control $control ): bool {

		// Variables

			$option = $control->manager->get_setting( 'typography_google_fonts' );


		// Output

			return (bool) $option->value();

	} // /is_typography_google_fonts

}
