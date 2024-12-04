<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\View;

class TopBarListIcons extends \ColibriWP\Theme\Components\FrontHeader\TopBarListIcons {


	static $settings_prefix = 'front-header.icon_list.';


	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-header',
			'top-bar/icon-list',
			array(
				'component' => $this,
			)
		);

	}

	public function printIcons() {
		$icons = $this->mod( static::$settings_prefix . 'localProps.iconList', array() );
		if ( $icons ) {
			$count = count( $icons );

			for ( $i = 0; $i < $count; $i ++ ) {
				$icon = $icons[ $i ];

				View::partial( 'front-header', 'top-bar/icon-list-item', $icon );
			}
		}
	}
}
