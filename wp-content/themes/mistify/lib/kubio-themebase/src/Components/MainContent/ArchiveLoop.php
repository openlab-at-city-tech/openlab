<?php

namespace Kubio\Theme\Components\MainContent;

use ColibriWP\Theme\Components\MainContent\ArchiveLoop as ArchiveLoopBase;
use ColibriWP\Theme\View;

class ArchiveLoop extends ArchiveLoopBase {

	protected static function getOptions() {
		return array();
	}

	public function renderContent( $partial = array() ) {
			$partial = array_merge(
				array(
					'view' => 'content/index/loop-item',
				),
				$partial
			);

			$view = $partial['view'];

			$partialParts = explode( '/', $view );
			$category     = array_shift( $partialParts );
			$slug         = implode( '/', $partialParts );

		while ( have_posts() ) :
			the_post();
			View::partial(
				$category,
				$slug,
				array(
					'component' => $this,
				)
			);
		endwhile;

	}
}
