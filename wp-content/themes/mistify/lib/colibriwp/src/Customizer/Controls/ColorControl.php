<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;

class ColorControl extends VueControl {
	public $type                       = 'colibri-color';
	public $alpha                      = true;
	protected $inline_content_template = true;

	public static function sanitize( $value, $control_data, $default = '' ) {

		if ( ! is_string( $value ) ) {
			return '#000000';
		}

		$alpha = Utils::pathGet( $control_data, 'alpha', true );
		$value = trim( $value );

		if ( ! $alpha ) {
			if ( strpos( $value, '#' ) === 0 ) {
				return sanitize_hex_color( $value );
			} else {
				if ( strpos( $value, 'rgb(' ) === 0 ) {
					$color = str_replace( ' ', '', $value );
					sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );

					return 'rgb(' . $red . ',' . $green . ',' . $blue . ')';
				}

				if ( strpos( $value, 'rgba(' ) === 0 ) {
					$color = str_replace( ' ', '', $value );
					sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
					return 'rgb(' . $red . ',' . $green . ',' . $blue . ')';
				}
			}
		} else {

			if ( strpos( $value, 'rgba' ) !== 0 ) {
				return '';
			} else {
				$color = str_replace( ' ', '', $value );
				sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

				return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
			}
		}

	}

	public function json() {
		$json          = parent::json();
		$json['alpha'] = $this->alpha;

		return $json;
	}

	protected function printVueContent() {
		?>
		<el-color-picker
				v-model="value"
				:size="size"
				:show-alpha="alpha"
				@change="setValue"
				@active-change="activeChange"
		<# (data.alpha == false) ? '': print('show-alpha') #>
		>
		</el-color-picker>
		<?php
	}
}
