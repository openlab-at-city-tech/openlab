<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;

class GradientControl extends VueControl {

	public $type = 'colibri-gradient';

	public static function sanitize( $value, $control_data, $default = '' ) {
		return Utils::sanitizeEscapedJSON( $value );
	}

	protected function printVueContent() {
		?>
		<ul class="gradients-list inline-elements-container">
			<li :class="[(gradient.name == value.name)?'selected':'']" class="inline-element"
				v-for="gradient in gradients"
				@click="setValue(gradient)">
				<div class="web-gradient" :style="computeGradient(gradient)"></div>
			</li>
		</ul>
		<?php
	}
}
