<?php

namespace ColibriWP\Theme\Components\MainContent;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class SingleItemTemplate extends ComponentBase {

	protected static function getOptions() {
		return array();
	}

	public function renderContent( $parameters = array() ) {
		if ( have_posts() ) :

			View::partial(
				'main',
				'post',
				array(
					'component' => $this,
				)
			);

		else :
			View::partial(
				'main',
				'404',
				array(
					'component' => $this,
				)
			);
		endif;
	}
}
