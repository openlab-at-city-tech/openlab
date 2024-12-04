<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;

class ComposedControl extends VueControl {

	public $type    = 'colibri-composed';
	private $fields = array();

	public static function sanitize( $value, $control_data, $default = '' ) {
		return Utils::sanitizeEscapedJSON( $value );
	}

	protected function printVueContent() {
		?>
		<ul class="colibri-fullwidth">
			<li class="customize-control customize-control-colibri-slider"
				v-for="(field, name) in fields" v-bind:class="classControlType">
				<div :class="{ 'inline-elements-container' : field.inline == true}">
					<div :class="{ 'inline-element' : field.inline == true}">
						<span class="customize-control-title"><?php $this->vueEcho( 'field.label' ); ?></span>
					</div>

					<div :class="{ 'inline-element fit' : field.inline == true}">
						<div
								:is="getComponentType(field.type)"
								v-model="value[name]"
								v-bind="field.props"
								@change="propChanged($event,field,name)"></div>
						<div>
			</li>
		</ul>
		<?php
	}

}
