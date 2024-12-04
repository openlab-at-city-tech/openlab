<?php


namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class Subtitle extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.subtitle.';

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
					'default'   => Defaults::get( "{$prefix}show" ),
					'transport' => 'refresh',
					'control'   => array(
						'label'       => Translations::get( 'show_subtitle' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => 'hero',
						'colibri_tab' => 'content',
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

				"{$prefix}style.textAlign"    => array(
					'default'    => Defaults::get( "{$prefix}style.textAlign" ),
					'control'    => array(
						'label'       => Translations::escHtml( 'align' ),
						'type'        => 'align-button-group',
						'button_size' => 'medium',
						'choices'     => array(
							'left'   => 'left',
							'center' => 'center',
							'right'  => 'right',
						),
						'none_value'  => 'flex-start',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector' => static::selectiveRefreshSelector(),
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'text-align',
						),
					),
				),
			),
		);
	}

	public static function selectiveRefreshSelector() {
		return Defaults::get( 'header_front_page.subtitle.selective_selector', false );
	}

	public function getPenPosition() {
		return static::PEN_ON_RIGHT;
	}

	public function renderContent( $parameters = array() ) {
		if ( $this->mod( static::$settings_prefix . 'show' ) ) {
			View::partial(
				'front-header',
				'subtitle',
				array(
					'component' => $this,
				)
			);
		}
	}

	public function printSubtitle() {

		$prefix = static::$settings_prefix;
		if ( get_theme_mod( "{$prefix}localProps.content", false ) || is_user_logged_in() ) {
			$value = trim( $this->mod( "{$prefix}localProps.content" ) );
		} else {
			$value = get_bloginfo( 'description' );
		}
		echo str_replace( "\n", '<br/>', $value );
	}
}
