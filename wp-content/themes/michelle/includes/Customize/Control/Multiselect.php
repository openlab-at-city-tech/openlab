<?php
/**
 * Customizer custom control: Multi-select field.
 *
 * Related script is already included within `assets/js/customize-controls.js`.
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

class Multiselect extends WP_Customize_Control {

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content() {

		// Requirements check

			if (
				empty( $this->choices )
				|| ! is_array( $this->choices )
			) {
				return;
			}


		// Output

			if ( 'multicheckbox' === $this->type ) {
				$this->render_content_checkbox();
			} else {
				$this->render_content_select();
			}

	} // /render_content

	/**
	 * Get value as array.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function get_value_array() {

		// Output

			return ( is_string( $this->value() ) ) ? ( explode( ',', $this->value() ) ) : ( (array) $this->value() );

	} // /get_value_array

	/**
	 * Renders the checkbox control.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content_checkbox() {

		// Variables

			$value_array = $this->get_value_array();


		// Output

			?>

			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo wp_kses( $this->description, 'option_description' ); ?></span><?php endif; ?>

			<ul>
			<?php foreach ( $this->choices as $value => $label ) : ?>
				<li>
					<label>
						<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->id ); ?>[]" <?php checked( in_array( $value, $value_array ) ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				</li>
			<?php endforeach; ?>
			</ul>

			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $value_array ) ); ?>" />

			<?php

	} // /render_content_checkbox

	/**
	 * Renders the select control.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content_select() {

		// Output

			?>

			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo wp_kses( $this->description, 'option_description' ); ?></span><?php endif; ?>

				<select name="<?php echo esc_attr( $this->id ); ?>" multiple="multiple" <?php $this->link(); ?>>
					<?php foreach ( $this->choices as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"<?php selected( in_array( $value, $this->get_value_array() ) ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>

				<em><?php esc_html_e( 'Press CTRL key for multiple selection.', 'michelle' ); ?></em>
			</label>

			<?php

	} // /render_content_select

}
