<?php


namespace ColibriWP\Theme\Customizer\Sections;

use ColibriWP\Theme\Translations;
use WP_Customize_Section;

class ColibriSection extends WP_Customize_Section {
	public $type = 'colibri_section';

	protected $hidden = false;

	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}"
			class="accordion-section control-section control-section-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text"><?php Translations::escHtmlE( 'press_enter_to_open_section' ); ?></span>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container section-meta <# if ( data.description_hidden ) { #>customize-info<# } #>">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
							<span class="screen-reader-text"><?php Translations::escHtmlE( 'back' ); ?></span>
						</button>
						<h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span>
							{{ data.title }}
						</h3>
						<# if ( data.description && data.description_hidden ) { #>
						<button type="button" class="customize-help-toggle dashicons dashicons-editor-help"
								aria-expanded="false"><span
									class="screen-reader-text"><?php Translations::escHtmlE( 'help' ); ?></span>
						</button>
						<div class="description customize-section-description">
							{{{ data.description }}}
						</div>
						<# } #>

						<div class="customize-control-notifications-container"></div>
					</div>

					<# if ( data.description && ! data.description_hidden ) { #>
					<div class="description customize-section-description">
						{{{ data.description }}}
					</div>
					<# } #>
				</li>
				<li class="colibri-section-tabs-section customize-control">
					<div role="tablist" class="tabs-nav">
						<div role="tab" data-tab-name="content" class="tab-item active"
							 title="<?php Translations::escAttrE( 'content' ); ?>">
							<span class="dashicons dashicons-edit"></span>
							<span class="tab-label"><?php Translations::escHtmlE( 'content' ); ?></span>
						</div>
						<div role="tab" data-tab-name="style" class="tab-item"
							 title="<?php Translations::escAttrE( 'style' ); ?>">
							<span class="dashicons dashicons-admin-customizer"></span>
							<span class="tab-label"><?php Translations::escHtmlE( 'style' ); ?></span>
						</div>
						<div role="tab" data-tab-name="layout" class="tab-item"
							 title="<?php Translations::escAttrE( 'advanced' ); ?>">
							<span class="dashicons dashicons-admin-generic"></span>
							<span class="tab-label"><?php Translations::escHtmlE( 'advanced' ); ?></span>
						</div>
					</div>
				</li>
			</ul>
		</li>
		<?php
	}

	public function json() {
		$json           = parent::json();
		$json['hidden'] = $this->hidden;

		return $json;
	}
}
