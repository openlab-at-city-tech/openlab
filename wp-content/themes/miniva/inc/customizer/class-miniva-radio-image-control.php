<?php
/**
 * Customize Radio Image Control
 *
 * @package Miniva
 */

/**
 * Customize Radio Image Control Class
 */
class Miniva_Radio_Image_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'radio-image';

	/**
	 * Render the control's content.
	 *
	 * @since 1.0
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}
		foreach ( $this->choices as $value => $img ) {
			if ( is_array( $img ) ) {
				$img_array['src']   = sprintf( $img['src'], get_template_directory_uri() );
				$img_array['title'] = $img['title'];
			} else {
				$img_array['src']   = sprintf( $img, get_template_directory_uri() );
				$img_array['title'] = $this->label . ' ' . $value;
			}
			$this->choices[ $value ] = $img_array;
		}

		$name = '_customize-radio-' . $this->id;
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div class="radio-image hide-radio inline">
			<?php foreach ( $this->choices as $value => $img ) : ?>
				<label class="radio-image">
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $value ); ?>>

					<img src="<?php echo esc_url( $img['src'] ); ?>" alt="<?php echo esc_attr( $img['title'] ); ?>" title="<?php echo esc_attr( $img['title'] ); ?>">
					<span class="screen-reader-text"><?php echo esc_html( $img['title'] ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
