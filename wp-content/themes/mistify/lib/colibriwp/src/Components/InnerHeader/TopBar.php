<?php

namespace ColibriWP\Theme\Components\InnerHeader;

use ColibriWP\Theme\Components\Header\TopBar as HeaderTopBar;
use ColibriWP\Theme\View;


class TopBar extends HeaderTopBar {
	protected static $settings_prefix = 'header_post.navigation.';

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
