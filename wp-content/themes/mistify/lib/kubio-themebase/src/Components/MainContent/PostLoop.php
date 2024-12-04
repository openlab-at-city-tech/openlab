<?php

namespace Kubio\Theme\Components\MainContent;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class PostLoop extends \ColibriWP\Theme\Components\MainContent\PostLoop {

	protected static function getOptions() {
		return array();
	}

	public function renderContent( $parameters = array() ) {
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				View::partial(
					'content',
					'item_template',
					array(
						'component' => $this,
					)
				);

			endwhile;
		else :
			View::partial(
				'content',
				'404',
				array(
					'component' => $this,
				)
			);
		endif;
	}
}
