<?php


namespace ColibriWP\Theme\Components\InnerHeader;

use ColibriWP\Theme\Components\FrontHeader\Title as FrontTitle;
use ColibriWP\Theme\View;

class Title extends FrontTitle {
	protected static $settings_prefix = 'header_post.title.';

	public function renderContent( $parameters = array() ) {

		if ( $this->mod( static::$settings_prefix . 'show' ) ) {
			View::partial(
				'inner-header',
				'title',
				array(
					'component' => $this,
				)
			);
		}
	}


	protected static function getOptions() {
		$prefix  = static::$settings_prefix;
		$options = parent::getOptions();
		unset( $options['settings'][ "{$prefix}localProps.content" ] );

		return $options;
	}
}
