<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Translations;
use WP_Customize_Manager;

class ButtonGroupControl extends VueControl {

	public $type = 'colibri-button-group';

	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	public static function sanitize( $value, $control_data, $default = '' ) {
		$list_value = Utils::sanitizeSelectControl( $control_data, $value );
		$none_value = Utils::pathGet( $control_data, 'none_value', null );

		if ( ! $list_value && $none_value !== null && $none_value === $value ) {
			$list_value = $none_value;
		}

		return $list_value;
	}

	/**
	 * @return bool|mixed
	 */
	public function getNoneValue() {
		return $this->getParam( 'none_value' );
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
					<# if ( data.none_value ) { #>
					<el-button @click="noneClicked"
							   type="text"><?php Translations::escHtmlE( 'none' ); ?></el-button>
					<# } #>
				</div>
			</div>
			<colibri-group-control
					:items="options"
					:value="value"
					:size="size"
					@change="handleButtonClicked"></colibri-group-control>
		</div>
		<?php
	}
}
