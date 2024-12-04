<?php


namespace Kubio\Theme\Components;

use ColibriWP\Theme\AssetsManager;
use ColibriWP\Theme\Components\Footer\FrontFooter;
use ColibriWP\Theme\View;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class Footer extends FrontFooter {
	protected static $settings_prefix = 'footer.footer.';

	public function renderContent( $parameters = array() ) {
		View::partial(
			'footer',
			'footer',
			array(
				'component' => $this,
			)
		);

	}

	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'title' ),
					'panel'  => 'footer_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(

				"{$prefix}pen"            => array(
					'control' => array(
						'type'    => 'pen',
						'section' => 'footer',
					),

				),

				"{$prefix}plugin-content" => array(
					'control' => array(
						'type'        => 'plugin-message',
						'section'     => 'footer',
						'colibri_tab' => 'content',
					),
				),

			),
		);
	}
}
