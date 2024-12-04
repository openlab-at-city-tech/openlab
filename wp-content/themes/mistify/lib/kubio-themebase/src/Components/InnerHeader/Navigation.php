<?php


namespace Kubio\Theme\Components\InnerHeader;

use ColibriWP\Theme\View;
use Kubio\Theme\Components\Common\NavigationStyle;
use \Kubio\Theme\Components\FrontHeader\Navigation  as FrontNavigation;
class Navigation extends FrontNavigation {
	static $settings_prefix = 'header.navigation.';

	public static function style() {
		return NavigationStyle::getInstance( static::getPrefix(), static::selectiveRefreshSelector() );
	}

	public function renderContent( $parameters = array() ) {
		static::style()->renderContent();

		View::partial(
			'header',
			'navigation',
			array(
				'component' => $this,
			)
		);
	}
}
