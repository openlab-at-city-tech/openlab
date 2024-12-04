<?php


namespace Kubio\Theme\Components\InnerHeader;

use ColibriWP\Theme\View;

class Title extends \ColibriWP\Theme\Components\InnerHeader\Title {
	protected static $settings_prefix = 'header.title.';

	public function renderContent( $parameters = array() ) {

		if ( $this->mod( static::$settings_prefix . 'show', true ) ) {
			View::partial(
				'header',
				'title',
				array(
					'component' => $this,
				)
			);
		}
	}

}
