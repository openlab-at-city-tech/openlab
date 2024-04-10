<?php
/**
 * Alpha color control
 *
 * @package Sydney
 */

class Sydney_Alpha_Color extends WP_Customize_Control {

	public $type = 'sydney-alpha-color';

	/**
	 * Add support for palettes to be passed in.
	 *
	 * Supported palette values are true, false, or an array of RGBa and Hex colors.
	 */
	public $palette;
	/**
	 * Add support for showing the opacity value on the slider handle.
	 */
	public $show_opacity;	

	public $remove_bordertop = false;

	public $connected_global = false;

	public function enqueue() {
		wp_enqueue_script( 'sydney-pickr', get_template_directory_uri() . '/js/pickr.min.js', array( 'jquery' ), '1.8.2', true );
	}

	public function render_content() {

		// Process the palette
		if ( is_array( $this->palette ) ) {
			$palette = implode( '|', $this->palette );
		} else {
			// Default to true.
			$palette = ( false === $this->palette || 'false' === $this->palette ) ? 'false' : 'true';
		}

		// Support passing show_opacity as string or boolean. Default to true.
		$show_opacity = ( false === $this->show_opacity || 'false' === $this->show_opacity ) ? 'false' : 'true';

		?>
			<div class="sydney-color-controls">
				<?php // Output the label and description if they were passed in.
				if ( isset( $this->label ) && '' !== $this->label ) {
					echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
				}
				if ( isset( $this->description ) && '' !== $this->description ) {
					echo '<span class="description customize-control-description">' . esc_html( $this->description ) . '</span>';
				} ?>		
				<div class="color-options">		
				<?php foreach ( array_keys( $this->settings ) as $key => $value ) : ?>
					<?php if ( 0 === $key && count( $this->settings ) > 1 ) : ?>
						<div class="sydney-global-control">
							<span class="dashicons dashicons-admin-site-alt3"></span>
							<div class="global-colors-dropdown" data-element="<?php echo esc_attr( str_replace( 'global_', '', $this->settings[ $value ]->id ) ); ?>">
								<div class="title">
									<?php esc_html_e( 'Select a Global Color', 'sydney' ); ?>
									<a href="javascript:wp.customize.control( 'custom_palette' ).focus();"><span class="dashicons dashicons-admin-generic"></span></a>
								</div>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $this->settings[ $value ]->id ); ?>" value="<?php echo esc_attr( $this->value( $value ) ); ?>" class="sydney-connected-global" <?php $this->link( $value ); ?> />
						</div>
						<?php else : ?>
					<div class="sydney-color-control" data-control-id="<?php echo esc_attr( $this->settings[ $value ]->id ); ?>">
						<div class="sydney-color-picker" data-default-color="<?php echo esc_attr( $this->settings[ $value ]->default ); ?>" style="background-color: <?php echo esc_attr( $this->value( $value ) ); ?>;"></div>
						<input type="text" name="<?php echo esc_attr( $this->settings[ $value ]->id ); ?>" value="<?php echo esc_attr( $this->value( $value ) ); ?>" class="sydney-color-input" <?php $this->link( $value ); ?> />
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
				</div>
			</div>
	<?php 
	}
}