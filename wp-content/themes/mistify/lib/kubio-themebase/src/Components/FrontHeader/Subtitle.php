<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class Subtitle extends \ColibriWP\Theme\Components\FrontHeader\Subtitle {

	static $settings_prefix = 'front-header.subtitle.';

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'subtitle' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(
				"{$prefix}show"               => array(
					'default'      => Defaults::get( "{$prefix}show" ),
					'transport'    => 'refresh',
					'control'      => array(
						'label'       => Translations::get( 'show_subtitle' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => 'hero',
						'colibri_tab' => 'content',
					),
					'active_rules' => array(
						array(
							'function' => 'is_front_page',
						),
					),

				),
				"{$prefix}localProps.content" => array(
					'default' => Defaults::get( 'lorem_ipsum' ),
					'control' => array(
						'label'       => Translations::get( 'subtitle' ),
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
							'selector' => static::selectiveRefreshSelector() . ' .wp-block-kubio-text__text',
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'text-align',
						),
					),
				),
			),
		);
	}


	public static function selectiveRefreshSelector() {
		return "[data-kubio-partial-refresh='subtitle']";
	}

	public function renderContent( $parameters = array() ) {
		if ( $this->mod( static::$settings_prefix . 'show' ) ) {
			?>
		<div data-kubio-partial-refresh='subtitle'>
			<?php
			parent::renderContent();
			?>
		</div>
			<?php
		}
	}
}
