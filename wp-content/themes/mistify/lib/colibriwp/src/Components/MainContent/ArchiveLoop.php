<?php

namespace ColibriWP\Theme\Components\MainContent;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class ArchiveLoop extends ComponentBase {

	protected static function getOptions() {
		return array();
	}

	public function renderContent( $parameters = array() ) {
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				View::partial(
					'main',
					'archive_item_template',
					array(
						'component' => $this,
					)
				);

			endwhile;
		  else :
			  $self = $this;
			  /** ROW START */
            View::printIn(
				View::ROW_ELEMENT,
				function () use ( $self ) {
					/** COLUMN START */
					View::printIn(
						View::COLUMN_ELEMENT,
						function () use ( $self ) {
							View::partial(
								'main',
								'404',
								array(
									'component' => $this,
								)
							);
						}
					);
				},
				array(
					'outer_class' => array( 'w-100' ),
				)
            );

		  endif;
	}
}
