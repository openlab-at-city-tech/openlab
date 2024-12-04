<?php

namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\Header\TopBar as HeaderTopBar;
use ColibriWP\Theme\View;


class TopBar extends HeaderTopBar {
	protected static $settings_prefix = 'header_front_page.navigation.';


	public function makeView() {
		View::partial(
			'front-header',
			'top-bar',
			array(
				'component' => $this,
			)
		);
	}
}
