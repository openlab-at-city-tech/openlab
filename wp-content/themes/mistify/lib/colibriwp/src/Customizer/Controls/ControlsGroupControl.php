<?php


namespace ColibriWP\Theme\Customizer\Controls;

use WP_Customize_Manager;

class ControlsGroupControl extends VueControl {
	public $type = 'colibri-controls-group';

	protected $inline_content_template = true;

	protected $active_color   = '#1989fa';
	protected $inactive_color = '#949596';

	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {

		if ( isset( $args['default'] ) ) {
			$args['default'] = ! ! intval( $args['default'] );
		}

		parent::__construct( $manager, $id, $args );
	}

	public static function sanitize( $value, $control_data, $default = '' ) {
		return 1;
	}

	public function json() {
		$json        = parent::json();
		$json['key'] = $this->id . '-controls-holder';

		return $json;
	}

	protected function printVueContent() {
		?>
		<el-switch

				v-if="show_toggle"
				v-model="value"
				active-color="{{ data.active_color }}"
				inactive-color="{{ data.inactive_color }}"
				@change="conditionChanged"
		>
		</el-switch>

		<el-popover
				placement="right-end"
				width="334"
				trigger="click"
				@show="onShow($event)">
			<div class="holder" data-holder-id="{{ data.key }}">
			</div>

			<el-button
					class="popover-toggler"
					:disabled="!value"
					type="text"
					@click="togglePopup"
					slot="reference"
					icon="el-icon-setting" circle>
			</el-button>


		</el-popover>


		<?php
	}
}
