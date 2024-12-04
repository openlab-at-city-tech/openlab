<?php


namespace ColibriWP\Theme\Customizer\Controls;

class SeparatorControl extends VueControl {

	public $type = 'colibri-separator';

	public static function sanitize( $value, $control_data, $default = '' ) {
		return '';
	}

	protected function printVueContent() {
		?>
		<div class="separator">&nbsp;</div>
		<?php
	}
}
