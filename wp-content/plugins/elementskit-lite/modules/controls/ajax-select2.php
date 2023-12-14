<?php 
namespace ElementsKit_Lite\Modules\Controls;

defined( 'ABSPATH' ) || exit;

class Ajax_Select2 extends \Elementor\Base_Data_Control {

	public function get_api_url() {
		return get_rest_url() . 'elementskit/v1';
	}

	/**
	 * Get select2 control type.
	 *
	 * Retrieve the control type, in this case `select2`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'ajaxselect2';
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
	
		// Register the script with version and set it to load in the footer.
		wp_register_script('elementskit-js-ajaxchoose-control', Init::get_url() . 'assets/js/ajaxchoose.js', $dependencies, '1.0.0', true);
	
		// Enqueue the script.
		wp_enqueue_script('elementskit-js-ajaxchoose-control');
	}	

	/**
	 * Get select2 control default settings.
	 *
	 * Retrieve the default settings of the select2 control. Used to return the
	 * default settings while initializing the select2 control.
	 *
	 * @since 1.8.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array(
			'options'        => array(),
			'multiple'       => false,
			'select2options' => array(),
		);
	}


	/**
	 * Render select2 control output in the editor.
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
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select 
					id="<?php echo esc_attr( $control_uid ); ?>" 
					class="elementor-megamenuajaxselect2" 
					type="megamenuajaxselect2" {{ multiple }} 
					data-setting="{{ data.name }}"
					data-ajax-url="<?php echo esc_attr( $this->get_api_url() . '/{{data.options}}/' ); ?>"
				>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
