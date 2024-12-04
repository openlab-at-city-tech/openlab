<?php


namespace ColibriWP\Theme\Customizer\Controls;

class IconControl extends VueControl {

	public $type = 'colibri-icon';

	public static function sanitize( $value, $control_data, $default = '' ) {

		$keys = array( 'name', 'content' );
		$diff = array_diff( array_keys( $value ), $keys );
		if ( is_array( $value ) && count( $diff ) === 0 ) {

		}

		return array(
			'content' => '',
			'name'    => '',
		);
	}

	protected function printVueContent() {
		?>
		<icon-picker :value="value" :icons="icons"></icon-picker>
		<?php
	}
}
