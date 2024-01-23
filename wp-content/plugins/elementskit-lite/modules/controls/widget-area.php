<?php 
namespace ElementsKit_Lite\Modules\Controls;

defined( 'ABSPATH' ) || exit;

class Widget_Area extends \Elementor\Base_Data_Control {
	/**
	 * Get choose control type.
	 *
	 * Retrieve the control type, in this case `choose`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'widgetarea';
	}

	/**
	 * Enqueue ontrol scripts and styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue() {
		// Define script dependencies if needed.
		$dependencies = array('jquery'); // Replace 'jquery' with the appropriate dependency.
		// styles
		wp_register_style( 'elementskit-css-widgetarea-control-inspactor', Init::get_url() . 'assets/css/widgetarea-inspactor.css', array(), '1.0.0' );
		wp_enqueue_style( 'elementskit-css-widgetarea-control-inspactor' );

		// script
		wp_register_script( 'elementskit-js-widgetarea-control-inspactor', Init::get_url() . 'assets/js/widgetarea-inspactor.js',  $dependencies, '1.0.0', true );
		wp_enqueue_script( 'elementskit-js-widgetarea-control-inspactor' );
	}


	/**
	 * Render choose control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div style="display:none" class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input id="<?php echo esc_attr( $control_uid ); ?>" type="text" data-setting="{{ data.name }}" />
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Get choose control default settings.
	 *
	 * Retrieve the default settings of the choose control. Used to return the
	 * default settings while initializing the choose control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array(
			'label_block'      => true,
			'show_edit_button' => false,
		);
	}
}
