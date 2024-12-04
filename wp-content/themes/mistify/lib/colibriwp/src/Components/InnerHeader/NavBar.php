<?php


namespace ColibriWP\Theme\Components\InnerHeader;

use ColibriWP\Theme\Components\FrontHeader\NavBar as FrontNavBar;
use ColibriWP\Theme\View;


class NavBar extends FrontNavBar {
	protected static $settings_prefix = 'header_post.navigation.';

	public function renderContent( $parameters = array() ) {
		static::style()->renderContent();

		View::partial(
			'inner-header',
			'navigation',
			array(
				'component' => $this,
			)
		);
	}
}
