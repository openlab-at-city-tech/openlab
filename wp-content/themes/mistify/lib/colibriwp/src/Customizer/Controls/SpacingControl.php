<?php


namespace ColibriWP\Theme\Customizer\Controls;

class SpacingControl extends VueControl {

	public $type = 'colibri-spacing';

	public static function sanitize( $value, $control_data, $default = '' ) {
		// @TODO proper sanitization
		return $value;
	}

	protected function content_template() {
		$this->printVueMountPoint();

		?>
		<div class="customize-control-notifications-container"></div>
		<?php
	}

	protected function printVueContent() {
		?>
		<div class="colibri-fullwidth">

			<div class="inline-elements-container">
				<div class="inline-element">
					<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
					<# } #>
				</div>

				<div class="inline-element fit">
					<el-radio-group v-model="value.unit">
						<el-radio-button
								v-for="u in spacing_units"
								size="mini"
								:label="u.label"
								:key="u.unit"
						>
						</el-radio-button>
					</el-radio-group>
				</div>
			</div>

			<div class="colibri-fullwidth">
				<div class="inline-elements-container">

					<div class="side" v-for="(side_value,side) in value.sides" class="inline-element" :key="side">
						<div class="side-inner">
							<label class="side-label"><?php $this->vueEcho( 'label(side)' ); ?></label>
							<el-input-number
									controls-position="right"
									placeholder=""

									v-model="value.sides[side]">
							</el-input-number>
						</div>
					</div>

				</div>
			</div>

		</div>
		<?php
	}
}
