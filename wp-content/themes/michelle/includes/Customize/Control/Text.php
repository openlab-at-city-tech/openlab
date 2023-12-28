<?php
/**
 * Customizer custom control: Text field with datalist support.
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

class Text extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $type = 'text';

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {
				ob_start();
				parent::render_content();

				echo str_replace( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$this->get_link(),
					'list="datalist-' . esc_attr( $this->id ) . '"' . $this->get_link(),
					ob_get_clean()
				);

				echo '<datalist id="datalist-' . esc_attr( $this->id ) . '">';
				foreach ( $this->choices as $value ) {
					echo '<option value="' . esc_attr( $value ) . '">';
				}
				echo '</datalist>';
			} else {
				parent::render_content();
			}

	} // /render_content

}
