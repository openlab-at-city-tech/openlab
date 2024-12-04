<?php


namespace ColibriWP\Theme\Customizer\Controls;

class PenControl extends ColibriControl {

	public $type = 'colibri-pen';

	protected function content_template() {
		?>
		<div class="control-focus"></div>
		<?php
	}
}
