<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Translations;

class LinkedSelectControl extends VueControl {
	public $type = 'colibri-linked-select';

	public static function sanitize( $value, $control_data, $default = '' ) {
		return Utils::sanitizeSelectControl( $control_data, $value );
	}

	protected function printVueContent() {
		?>
		<el-select v-model="value" :size="size" @change="setValue"
				   placeholder="<?php Translations::escAttrE( 'select' ); ?>">
			<el-option
					v-for="item in options"
					:key="item.value"
					:label="item.label"
					:value="item.value">
			</el-option>
		</el-select>
		<?php
	}
}
