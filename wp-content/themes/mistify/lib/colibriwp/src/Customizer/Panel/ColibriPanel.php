<?php


namespace ColibriWP\Theme\Customizer\Panel;

use ColibriWP\Theme\Translations;
use WP_Customize_Panel;

class ColibriPanel extends WP_Customize_Panel {
	public $type = 'colibri_panel';

	public $footer_buttons = array();

	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button class="customize-panel-back" tabindex="-1"><span
						class="screen-reader-text"><?php Translations::escHtmlE( 'back' ); ?></span></button>
			<div class="accordion-section-title">
				<span class="preview-notice">
					<strong class="panel-title">{{ data.title }}</strong>
				</span>
				<# if ( data.description ) { #>
				<button type="button" class="customize-help-toggle dashicons dashicons-editor-help"
						aria-expanded="false"><span
							class="screen-reader-text"><?php Translations::escHtmlE( 'help' ); ?></span></button>
				<# } #>
			</div>
			<# if ( data.description ) { #>
			<div class="description customize-panel-description">
				{{{ data.description }}}
			</div>
			<# } #>

			<div class="customize-control-notifications-container"></div>
		</li>
		<?php
	}

	public function json() {
		$json = parent::json();

		$json['footer_buttons'] = $this->footer_buttons;

		return $json;
	}
}
