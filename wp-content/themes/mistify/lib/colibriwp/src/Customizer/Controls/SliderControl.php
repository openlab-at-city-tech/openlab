<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;

class SliderControl extends VueControl {
	public $type = 'colibri-slider';

	public static function sanitize( $value, $control_data, $default = '' ) {
		$to_float = ( Utils::pathGet( $control_data, 'step', 1 ) < 0 );

		if ( $to_float ) {
			return floatval( $value );
		}

		return intval( $value );

	}

	protected function printVueContent() {
		?>
		<div class="inline-elements-container">
			<div class="inline-element">
				<el-slider
						v-model="value"
						:min="min"
						:max="max"
						:step="step"
						@change="setValue"
				>
				</el-slider>
			</div>
			<div class="inline-element fit">
				<el-input-number
						size="small"
						v-model="value"
						:min="min"
						:max="max"
						:step="step"
						@keyup.native="keyUp"
						@change="setValue"
						controls-position="right">
				</el-input-number>
			</div>
		</div>
		<?php
	}
}
