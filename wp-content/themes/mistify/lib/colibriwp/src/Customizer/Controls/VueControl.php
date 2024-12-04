<?php


namespace ColibriWP\Theme\Customizer\Controls;

abstract class VueControl extends ColibriControl {

	protected $inline_content_template = false;

	final protected function printVueMountPoint() {
		?>
		<div class="sidebar-container" data-name="vue-mount-point">
			<?php $this->printVueContent(); ?>
		</div>
		<?php
	}

	public function json() {
		$json                            = parent::json();
		$json['inline_content_template'] = $this->getParam(
			'inline_content_template',
			false
		);

		return $json;
	}

	protected function content_template() {
		?>
		<# if(data.inline_content_template) { #>
		<?php $this->printInlineContentTemplate(); ?>
		<# } else { #>
		<?php $this->printDefaultContentTemplate(); ?>
		<# } #>

		<div class="customize-control-notifications-container"></div>
		<?php
	}


	protected function printInlineContentTemplate() {
		?>
		<div class="inline-elements-container">
			<div class="inline-element">
				<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
			</div>
			<div class="inline-element fit">
				<?php $this->printVueMountPoint(); ?>
			</div>
		</div>

		<# if ( data.description ) { #>
		<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<?php
	}

	protected function printDefaultContentTemplate() {
		?>

		<# if ( data.label ) { #>
		<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<div>
			<?php $this->printVueMountPoint(); ?>
		</div>


		<# if ( data.description ) { #>
		<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<?php
	}

	abstract protected function printVueContent();

	protected function vueEcho( $to_echo ) {
		echo '${ ' . $to_echo . ' }';
	}

	protected function render() {
	}
}
