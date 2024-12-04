<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;

class SelectIconControl extends VueControl {
	public $type = 'colibri-select-icon';

	public static function sanitize( $value, $control_data, $default = '' ) {
		return Utils::sanitizeSelectControl( $control_data, $value );
	}

	protected function printVueContent() {
		?>

		<select-with-icon
				slot="control"
				:value="value"
				@change="setValue($event)"
				:items="options"
		></select-with-icon>

		<?php
	}
}
