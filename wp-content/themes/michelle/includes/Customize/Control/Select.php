<?php
/**
 * Customizer custom control: Select field with optgroups support.
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

class Select extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $type = 'select';

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) :
				?>

				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo wp_kses( $this->description, 'option_description' ); ?></span><?php endif; ?>

					<select name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); ?>>
						<?php

						foreach ( $this->choices as $value => $name ) {
							if ( 0 === strpos( $value, 'optgroup' ) ) {
								echo '<optgroup label="' . esc_attr( $name ) . '">';
							} elseif ( 0 === strpos( $value, '/optgroup' ) ) {
								echo '</optgroup>';
							} else {
								echo '<option value="' . esc_attr( $value ) . '" ' . selected( $this->value(), $value, false ) . '>' . esc_html( $name ) . '</option>';
							}
						}

						?>
					</select>
				</label>

				<?php
			endif;

	} // /render_content

}
