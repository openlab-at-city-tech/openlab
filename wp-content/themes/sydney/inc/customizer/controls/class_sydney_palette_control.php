<?php
/**
 * Create page control
 *
 * @package Sydney
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sydney_Palette_Control extends WP_Customize_Control {
		
	/**
	 * The type of control being rendered
	 */
	public $type = 'sydney-multicolor-control';

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
	
	/**
	 * Constructor
	 */
	public function __construct( $manager, $id, $args = array(), $options = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the control in the customizer
	 */
	public function render_content() {
		?>
			<div class="sydney-custom-palettes-wrapper">
				<?php if( !empty( $this->label ) ) { ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php } ?>
				<?php if( !empty( $this->description ) ) { ?>
					<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php } ?>

				<div class="sydney-custom-palette">
					<?php for ( $i = 1; $i <= 9 ; $i++ ) { 
						$val = $this->value( 'global_color_' . $i ) ? $this->value( 'global_color_' . $i ) : $this->settings['global_color_' . $i]->default; ?>
						<div class="sydney-color-control sydney-global-color-control">
							<div class="sydney-color-picker" data-default-color="<?php echo esc_attr( $this->settings['global_color_' . $i]->default ); ?>" style="background-color: <?php echo esc_attr( $this->value( 'global_color_' . $i ) ); ?>;"></div>						
							<input type="text" value="<?php echo esc_attr( $this->value( 'global_color_' . $i ) ); ?>" class="sydney-color-input" <?php $this->link( 'global_color_' . $i ); ?> />						
						</div>
					<?php } ?>
				</div>
			</div>
        <?php

	}
}
