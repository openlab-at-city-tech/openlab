<?php

namespace ColibriWP\Theme\Components\MainContent;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class PostLoop extends ComponentBase {

	protected static function getOptions() {
		return array();
	}

	public function renderContent( $parameters = array() ) {
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				View::partial(
					'main',
					'item_template',
					array(
						'component' => $this,
					)
				);

			endwhile;
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
