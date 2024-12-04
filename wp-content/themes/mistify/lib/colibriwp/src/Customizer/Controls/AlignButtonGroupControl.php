<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Translations;
use WP_Customize_Manager;

class AlignButtonGroupControl extends ButtonGroupControl {

	public $type = 'colibri-align-button-group';

	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	public static function sanitize( $value, $control_data, $default = '' ) {
		if ( in_array( $value, array_keys( $control_data['choices'] ) ) ) {
			return $value;
		}

		return 'left';
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
					<span class="customize-control-title">{{{data.label }}}</span>
					<# } #>
				</div>

				<div class="inline-element fit">
					<# if ( data.none_value ) { #>
					<el-button @click="noneClicked"
							   type="text"><?php Translations::escHtmlE( 'none' ); ?></el-button>
					<# } #>
				</div>
			</div>

			<el-button-group class="colibri-select-buttons-container">
				<div class="h-row no-gutters">

					<el-button :is-selected="buttonIsSelected(values['left'])" :class="classes(values['left'])"
							   v-if="values['left']" @click="handleButtonClicked(values['left'])">
						   <span>
							<svg version="1.1" viewBox="0 0 448 512" class="svg-icon svg-fill"
								 style="width: 14px; height: 14px;">
								<path v-html="rawHtml" pid="0"
									  d="M288 44v40c0 8.837-7.163 16-16 16H16c-8.837 0-16-7.163-16-16V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16zM0 172v40c0 8.837 7.163 16 16 16h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16zm16 312h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm256-200H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16z">
								</path>
							</svg>
						   </span>
					</el-button>

					<el-button :is-selected="buttonIsSelected(values['center'])" :class="classes(values['center'])"
							   v-if="values['center']" @click="handleButtonClicked(values['center'])">
						<span>
						<svg version="1.1" viewBox="0 0 448 512" class="svg-icon svg-fill"
							 style="width: 14px; height: 14px;">
							<path v-html="rawHtml"
								  d="M352 44v40c0 8.837-7.163 16-16 16H112c-8.837 0-16-7.163-16-16V44c0-8.837 7.163-16 16-16h224c8.837 0 16 7.163 16 16zM16 228h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 256h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm320-200H112c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16h224c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16z">
							</path>
						</svg>
						</span>
					</el-button>

					<el-button :is-selected="buttonIsSelected(values['right'])" :class="classes(values['right'])"
							   v-if="values['right']" @click="handleButtonClicked(values['right'])">
						<span>
						<svg version="1.1" viewBox="0 0 448 512" class="svg-icon svg-fill"
							 style="width: 14px; height: 14px;">
							<path v-html="rawHtml"
								  d="M160 84V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16v40c0 8.837-7.163 16-16 16H176c-8.837 0-16-7.163-16-16zM16 228h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 256h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm160-128h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H176c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z">
							</path>
						</svg>
						</span>
					</el-button>

				</div>
			</el-button-group>
		</div>
		<?php
	}
}
