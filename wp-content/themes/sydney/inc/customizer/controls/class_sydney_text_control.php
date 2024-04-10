<?php
/**
 * Text control
 *
 * @package Sydney
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sydney_Text_Control extends WP_Customize_Control {
		
	/**
	 * The type of control being rendered
	 */
	public $type = 'sydney-text-control';

	public $link_title = '';

	public $link = '';	

	public $controls_general;

	public $controls_design;

	public $separator = false;
	
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

		<?php if ( 'before' === $this->separator ) : ?>
			<hr class="sydney-cust-divider before">
		<?php endif; ?>

		<?php if( !empty( $this->label ) ) { ?>
			<span class="customize-control-title"><?php echo wp_kses_post( $this->label ); ?></span>
		<?php } ?>
		<?php if( !empty( $this->description ) && !function_exists( 'athemes_wl_get_data' ) ) { ?>
			<span class="customize-control-description"><?php echo $this->description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php } ?>		
		<?php if( !empty( $this->link_title ) && !empty( $this->link ) ) { ?>
			<a href="<?php echo esc_url( $this->link ); ?>" target="_blank"><?php echo $this->link_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php } ?>	
		
		<?php if ( 'after' === $this->separator ) : ?>
			<hr class="sydney-cust-divider">
		<?php endif; ?>			
	<?php
	}
}