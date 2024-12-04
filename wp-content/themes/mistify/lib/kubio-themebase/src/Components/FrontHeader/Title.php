<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class Title extends \ColibriWP\Theme\Components\FrontHeader\Title {

	static $settings_prefix = 'front-header.title.';


	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'title' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(
				"{$prefix}show"               => array(
					'default'   => Defaults::get( "{$prefix}show" ),
					'transport' => 'refresh',
					'control'   => array(
						'label'       => Translations::get( 'show_title' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => 'hero',
						'colibri_tab' => 'content',
					),

				),
				"{$prefix}localProps.content" => array(
					'default' => __( 'Your Colorful Life', 'mistify' ),
					'control' => array(
						'label'       => Translations::get( 'title' ),
						'type'        => 'input',
						'input_type'  => 'textarea',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
				),
				"{$prefix}style.descendants.text.textAlign" => array(
					'default'    => Defaults::get( "{$prefix}style.descendants.text.textAlign" ),
					'control'    => array(
						'label'       => Translations::escHtml( 'align' ),
						'type'        => 'align-button-group',
						'button_size' => 'medium',
						'choices'     => array(
							'left'   => 'left',
							'center' => 'center',
							'right'  => 'right',
						),
						'none_value'  => 'left',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector' => static::selectiveRefreshSelector() . ' .wp-block-kubio-heading__text',
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'text-align',
						),
					),
				),
			),
		);
	}


	public static function selectiveRefreshSelector() {
		return "[data-kubio-partial-refresh='title']";
	}

	public function renderContent( $parameters = array() ) {
		if ( $this->mod( static::$settings_prefix . 'show' ) ) {
			?>
		<div data-kubio-partial-refresh='title' >
			<?php
			parent::renderContent();
			?>
		</div>
			<?php
		}
	}
}
