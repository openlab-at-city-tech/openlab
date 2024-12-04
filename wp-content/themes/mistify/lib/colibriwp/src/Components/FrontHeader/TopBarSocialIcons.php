<?php

namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class TopBarSocialIcons extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.social_icons.';

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'social_icons' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(

				"{$prefix}localProps.iconList" => array(
					'default' => Defaults::get( "{$prefix}localProps.iconList" ),
					'control' => array(
						'label'          => Translations::get( 'icons' ),
						'type'           => 'repeater',
						'input_type'     => 'textarea',
						'section'        => "{$prefix}section",
						'colibri_tab'    => 'content',
						'item_add_label' => Translations::get( 'add_icon' ),
						'max'            => 10,
						'fields'         => array(

							'icon'       => array(
								'type'    => 'icon',
								'label'   => Translations::get( 'icon' ),
								'default' => Defaults::get( 'icons.facebook' ),
							),

							'link_value' => array(
								'type'    => 'text',
								'label'   => Translations::get( 'link' ),
								'default' => '#',
							),
						),
					),
				),
			),
		);
	}

	public function getPenPosition() {
		return static::PEN_ON_LEFT;
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-header',
			'top-bar/social-icons',
			array(
				'component' => $this,
			)
		);
	}

	public function printIcons() {
		$icons = $this->mod( static::$settings_prefix . 'localProps.iconList', array() );
		if ( $icons ) {
			foreach ( $icons as $icon ) {
				View::partial( 'front-header', 'top-bar/social-icon', $icon );
			}
		}
	}
}
