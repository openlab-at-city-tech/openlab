<?php
/**
 * The blank customize control extends the WP_Customize_Control class.
 *
 * @package customizer-controls
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}


/**
 * Class Kadence_Control_Available
 *
 * @access public
 */
class Kadence_Control_Available extends WP_Customize_Control {
	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'kadence_available_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $input_attrs = array();

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['input_attrs'] = $this->input_attrs;
	}
	/**
	 * Empty Render Function to prevent errors.
	 */
	public function render_content() {
	}
}
$wp_customize->register_control_type( 'Kadence_Control_Available' );
