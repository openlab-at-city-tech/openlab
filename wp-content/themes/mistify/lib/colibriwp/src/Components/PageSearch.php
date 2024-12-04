<?php


namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class PageSearch extends ComponentBase {

	public function renderContent( $parameters = array() ) {

		View::printIn(
			View::CONTENT_ELEMENT,
			function () {
				View::printIn(
					View::SECTION_ELEMENT,
					function () {
						View::printIn(
							View::ROW_ELEMENT,
							function () {
								View::printIn(
									View::COLUMN_ELEMENT,
									function () {
										View::partial(
											'main',
											'search',
											array(
												'component' => $this,
											)
										);
									}
								);
							}
						);
					}
				);
			},
			array( array( 'post-single' ) )
		);
	}


	/**
	 * @return array();
	 */
	protected static function getOptions() {
		return array();
	}
}
