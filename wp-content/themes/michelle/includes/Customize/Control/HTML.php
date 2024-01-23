<?php
/**
 * Customizer custom control: HTML.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize\Control;

use WP_Customize_Control;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class HTML extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $type = 'html';

	/**
	 * Control content.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $content = '';

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content() {

		// Output

			if ( ! empty( $this->label ) ) {
				echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
			}

			if ( isset( $this->content ) ) {
				echo wp_kses( $this->content, 'option_description' );
			} else {
				esc_html_e( 'Please set the `content` parameter for the HTML control.', 'michelle' );
			}

			if ( ! empty( $this->description ) ) {
				echo '<span class="description customize-control-description">' . wp_kses( $this->description, 'option_description' ) . '</span>';
			}

	} // /render_content

}
